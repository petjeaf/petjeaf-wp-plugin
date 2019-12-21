<?php

class Petje_Af_Cache
{
    protected $prefix = 'petjeaf_';

    protected $userId = null;

    protected $pageId;

    protected $connector;

    protected $client;

    public function __construct($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
            $this->setUserPrefix();
        }

        $this->pageId = get_option('petje_af_page_id');

        $accessToken = $this->get('access_token');

        $this->connector = new Petje_Af_Connector($userId, $accessToken);
        $this->client = $this->connector->client;
    }

    protected function setUserPrefix() {
        if ($this->userId) {
            $this->prefix = 'petjeaf_user_' . $this->userId . '_';
        }
    }

    public function get($key) 
    {
        if (!$this->get_refresh_token()) {
            return null;
        }
        
        $result = $this->transient($key);

        return $result;
    }

    public function setUser($userId) {
        
        $this->userId = $userId;
        $this->setUserPrefix();

        $this->connector->setUser($userId);
        $this->client = $this->connector->client;
    }

    protected function transient($key, $expiration = 1 * HOUR_IN_SECONDS)
    {
		if (false == ($obj = get_transient($this->prefix . $key))) {
            $obj = $this->get_data($key);
            $this->saveField($key, $obj, $expiration);
		}

		return $obj;
    }
    
    public function saveField($key, $value, $expiration = 1 * HOUR_IN_SECONDS)
    {
        $exp = $expiration;

        if ($key == 'access_token') {
            $exp = 3000;
        }

        if ($key == 'refresh_token') {
            $exp = 100 * YEAR_IN_SECONDS;
        }

        if ($value) {
            set_transient($this->prefix . $key, $value, $exp);
        }
		return $value;
	}

    protected function get_data($key)
    {
        $res = null;

        try {
            if ($key == 'current_user') {
                $res = $this->get_current_user();
            }
            
            if ($key == 'access_token') {
                $res = $this->get_access_token();
            }
            
            if ($key == 'membership' && $this->userId) {
                $res = $this->get_membership();
            }
    
            if ($key == 'membership_rewards' && $this->userId) {
                $res = $this->get_membership_rewards();
            }
    
            if ($key == 'pages' && !$this->userId) {
                $res = $this->get_pages();
            }
    
            if ($key == 'page_plans' && !$this->userId) {
                $res = $this->get_page_plans();
            }
    
            if ($key == 'page_rewards' && !$this->userId) {
                $res = $this->get_page_rewards();
            }
    
            return $res;

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    protected function get_access_token()
    {
        $oauth2_provider = new Petje_Af_OAuth2_Provider($this->userId);

        $accessToken = $oauth2_provider->getAcccesTokenByRefreshToken($this->get_refresh_token());

        $this->saveField('refresh_token', $accessToken->getRefreshToken());

        return $accessToken->getToken();
    }

    protected function get_refresh_token() 
    {
        $refresh_token = get_transient($this->prefix . 'refresh_token');

        return $refresh_token;
    }

    protected function get_current_user()
    {
        return $this->client->users->me();      
    }

    protected function get_membership()
    {
        if (!$this->pageId) return null;
        $memberships = $this->client->memberships->byPage($this->pageId);
        if (!empty($memberships->_embedded->memberships)) return $memberships->_embedded->memberships[0];
        return null;  
    }

    protected function get_membership_rewards()
    {
        $membership = $this->get('membership');
        if ($membership) {
            $res = $this->client->membershipRewards->byMembership($membership->ID);
        }
        return $res;        
    }

    protected function get_pages()
    {
        $pages = $this->client->pages->list();

        return $pages->_embedded->pages;
    }

    protected function get_page_plans()
    {
        if (!$this->pageId) return null;
        $plans = $this->client->pagePlans->byPage($this->pageId);
        return $plans->_embedded->plans;
    }  

    protected function get_page_rewards()
    {
        if (!$this->pageId) return null;
        return $this->client->pageRewards->byPage($this->pageId);
    }
}

/*
*  Petje af cache function.
*
*  This function will return the Petje.af data that is cached.
*
*  @type	function
*  @since	2.0.0
*
*  @param	$key
*  @param   $fromUser (boolean)
*  @return	object from cache or fetched from api
*/
function petjeaf_cache($key, $fromUser = true) {
    $userId = null;
    
    if ($fromUser) {
       $user = wp_get_current_user();
       $userId = $user->ID;
    }

    $cache = new Petje_Af_Cache($userId);
    return $cache->get($key);
}