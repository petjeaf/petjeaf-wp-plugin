<?php

/**
 * Cache alle results form Petje.af api.
 *
 * Loads all results from cache first before trying to 
 * get from api endpoints.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Cache alle results form Petje.af api.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af_Cache
{
    /**
     * The prefix for all database keys.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string
     */
    protected $prefix = 'petjeaf_';

    /**
     * The user ID.
     *
     * @since    2.0.0
     * @access   protected
     * @var      integer
     */
    protected $userId;

    /**
     * The Petje.af page ID.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string
     */
    protected $pageId;

    /**
     * Instance of Petje_Af_Connector.
     *
     * @since    2.0.0
     * @access   protected
     * @var      Petje_Af_Connector
     */
    protected $connector;

    /**
     * Access Token
     *
     * @since    2.1.0
     * @access   protected
     */
    protected $accessToken;

    /**
     * Initialize.
     *
     * @since   2.0.0
     * @param   $userId
     * 
     */
    public function __construct($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
            $this->setUserPrefix();
        }

        $this->pageId = get_option('petje_af_page_id');

        $this->accessToken = $this->get('access_token');

        $this->connector = new Petje_Af_Connector($userId, $this->accessToken);
    }

    /**
     * Set the database prefix for users.
     *
     * @since   2.0.0
     * 
     */
    protected function setUserPrefix() {
        if ($this->userId) {
            $this->prefix = 'petjeaf_user_' . $this->userId . '_';
        }
    }

    /**
     * Get key from the cache. Its a wrapper for the transient function
     *
     * @since   2.0.0
     * @param   $key
     * 
     * @return  $result
     * 
     */
    public function get($key) 
    {   
        if (!$this->get_refresh_token()) {
            return null;
        }
        
        $result = $this->transient($key);

        return $result;
    }

    /**
     * Delete by key from the databas.
     *
     * @since   2.0.0
     * @param   $key
     * 
     * @return  delete_transient
     * 
     */
    public function delete($key)
    {
        return delete_transient($this->prefix . $key);
    }

    /**
     * Reset user when needed.
     *
     * @since   2.0.0
     * @param   $userId
     * 
     */
    public function setUser($userId) {
        
        $this->userId = $userId;
        $this->setUserPrefix();

        $this->connector->setUser($userId);
    }

    /**
     * Set the database prefix for users.
     *
     * @since   2.0.0
     * @param   $key
     * @param   $expiration     For if the transient needs to be recreated.
     * 
     * @return  object          From database or fetched from API
     * 
     */
    protected function transient($key, $expiration = 1 * HOUR_IN_SECONDS)
    {
        if (false == ($obj = get_transient($this->prefix . $key))) {
            $obj = $this->get_data($key);
            $this->saveField($key, $obj, $expiration);
        }

        return $obj;
    }

    /**
     * Save .
     *
     * @since   2.0.0
     * @param   $key
     * @param   $value
     * @param   $expiration
     * 
     * @return  $value that is save
     * 
     */
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
            $this->save_fallback($key, $value);
        }
        return $value;
    }

    /**
     * Save fallback to options table for pages and page plans only.
     *
     * @since   2.1.0
     * @param   $key
     * @param   $value
     * 
     * @return  $value that is save
     * 
     */
    public function save_fallback($key, $value)
    {
        if (!in_array($key, ['pages', 'page_plans'])) return;

        if ($value) {
            update_option($this->prefix . $key, $value);
        }
        return $value;
    }

    /**
     * Get fallback from options. To use when api is not connected (anymore)
     *
     * @since   2.1.0
     * @param   $key
     * @param   $value
     * 
     * @return  $value that is save
     * 
     */
    public function get_fallback($key)
    {
        if ($key == 'pages' || $key == 'page_plans') {
            return get_option($this->prefix . $key);
        }

        return null;
    }

    /**
     * Get data from the API.
     *
     * @since   2.0.0
     * @param   $key
     * 
     * @return  $result
     * @throws  Exception
     * 
     */
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
            throw new Exception($th->getMessage());
        }
    }

    /**
     * Get access token from OAuth2 Provider
     *
     * @since   2.0.0
     * 
     * @return  string      The access token
     * 
     */
    protected function get_access_token()
    {   
        $oauth2_provider = new Petje_Af_OAuth2_Provider($this->userId);

        $res = $oauth2_provider->getAcccesTokenByRefreshToken($this->get_refresh_token());

        $this->saveField('refresh_token', $res['refresh_token']);

        return $res['access_token'];
    }

    /**
     * Get refresh token from database
     *
     * @since   2.0.0
     * 
     * @return  string      The refresh token
     * 
     */
    protected function get_refresh_token() 
    {
        $refresh_token = get_transient($this->prefix . 'refresh_token');

        return $refresh_token;
    }


    /**
     * Get current user from API
     *
     * @since   2.0.0
     * 
     * @return  object
     * 
     */
    protected function get_current_user()
    {
        return $this->get('users/me');      
    }

    /**
     * Get membership from API
     *
     * @since   2.0.0
     * 
     * @return  object
     * 
     */
    protected function get_membership()
    {
        if (!$this->pageId) return null;
        $memberships = $this->connector->get('memberships', ['pageId' => $this->pageId]);
        if (!empty($memberships->_embedded->memberships)) return $memberships->_embedded->memberships[0];
        return null;  
    }

    /**
     * Get membership rewards from API
     *
     * @since   2.0.0
     * 
     * @return  array   with objects
     * 
     */
    protected function get_membership_rewards()
    {
        $membership = $this->get('membership');
        if ($membership) {
            $rewards = $this->connector->get("memberships/$membership->ID/rewards");
            return $rewards->_embedded->rewards;
        }
        return null;        
    }

    /**
     * Get current pages from API
     *
     * @since   2.0.0
     * 
     * @return  array   with objects
     * 
     */
    protected function get_pages()
    {
        if (
            !$this->accessToken && 
            $this->get_fallback('pages')
        ) return $this->get_fallback('pages');
        
        $pages = $this->connector->get('pages');

        return $pages->_embedded->pages;
    }

    /**
     * Get page plans from API
     *
     * @since   2.0.0
     * 
     * @return  array   with objects
     * 
     */
    protected function get_page_plans()
    {
        if (!$this->pageId) return null;

        if (
            !$this->accessToken && 
            $this->get_fallback('page_plans')
        ) return $this->get_fallback('page_plans');

        $plans = $this->connector->get("pages/$this->pageId/plans");
        return $plans->_embedded->plans;
    }  

    /**
     * Get page rewards from API
     *
     * @since   2.0.0
     * 
     * @return  array   with objects
     * 
     */
    protected function get_page_rewards()
    {
        if (!$this->pageId) return null;
        $rewards = $this->connector->get("pages/$this->pageId/rewards");
        return $rewards->_embedded->rewards;
    }
}

/**
*  Petje af cache function.
*
*  This function will return the Petje.af data that is cached.
*
*  @type	function
*  @since	2.0.0
*
*  @param	$key
*  @param   boolean     $formUser
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