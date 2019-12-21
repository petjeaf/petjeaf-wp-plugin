<?php

/**
 * Create connection with Petje.af OAuth2 Client.
 *
 * Create connection with the Petje.af OAuth2 Client
 * through composer package
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Create connection with Petje.af OAuth2 Client.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */

use Petjeaf\OAuth2\Client\Provider\Petjeaf;

class Petje_Af_OAuth2_Provider
{
    /**
	 * Instance of Petjeaf\OAuth2\Client\Provider\Petjeaf
	 *
	 * @since    2.0.0
	 * @access   public
	 * @var      Petjeaf\OAuth2\Client\Provider\Petjeaf
	 */
    public $provider;

    /**
	 * The User ID
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      integer
	 */
    protected $userId = null;

    /**
	 * Instance of Petje_Af_Cache
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Petje_Af_Cache
	 */
    protected $cache;

    /**
	 * The Access Token
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string
	 */
    protected $accessToken;

    /**
	 * Initialize class.
	 *
	 * @since   2.0.0
	 * @param   $userId
     * 
	 */
    public function __construct($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
        }

        $this->setProvider();
    }

    /**
	 * Set the Petje.af OAuth2 provider with credentials.
	 *
	 * @since   2.0.0
     * 
	 */
    protected function setProvider() 
    {
        $this->provider = new Petjeaf([
            'clientId' => get_option('petje_af_client_id'),
            'clientSecret' => get_option('petje_af_client_secret')
        ]);
    }

    /**
	 * Returns Authorization Url from provdier
	 *
	 * @since   2.0.0
     * @param   array   $scopes
     * 
     * @return Authorization Url
     * 
	 */
    public function getAuthorizationUrl($scopes = [])
    {
        return $this->provider->getAuthorizationUrl(['scope' => $scopes ]);
    }

    /**
	 * Get state from Authorization Url
	 *
	 * @since   2.0.0
     * 
     * @return  state that is used in authorization url
     * 
	 */
    public function getState()
    {
        return $this->provider->getState();
    }

    /**
	 * Get access token by code from provider
	 *
	 * @since   2.0.0
     * @param   $code
     * 
     * @return  object      Access token with refresh token
     * 
	 */
    protected function getAcccesTokenByCode($code)
    {
        $this->accessToken = $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        return $this->accessToken;
    }

    /**
	 * Get access token by refresh token from provider
	 *
	 * @since   2.0.0
     * @param   $refresh_token
     * 
     * @return  object      Access token with refresh token
     * 
	 */
    public function getAcccesTokenByRefreshToken($refreshToken)
    {
        $this->accessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken
        ]);

        return $this->accessToken;
    }

    /**
	 * Save access token to cache
	 *
	 * @since   2.0.0
     * 
	 */
    protected function saveAccessToken()
    {
        $this->cache->saveField('access_token', $this->accessToken->getToken());      
    }

    /**
	 * Save refresh token to cache
	 *
	 * @since   2.0.0
     * 
	 */
    protected function saveRefreshToken()
    {
        $this->cache->saveField('refresh_token', $this->accessToken->getRefreshToken());       
    }

    /**
	 * Revoke token on logout or disconnection
	 *
	 * @since   2.0.0
     * @param   boolean     to determine if the refresh_token has to be removed too.
     * 
	 */
    protected function revoke($removeRefreshToken = false)
    {
        try {
            $this->cache = new Petje_Af_Cache($this->userId);
        
            $this->provider->getAuthenticatedRequest(
                'DELETE',
                '/oauth2/tokens',
                $this->cache->get('access_token')
            );

            $this->cache->delete('access_token');

            if ($removeRefreshToken) {
                $this->cache->delete('refresh_token');
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
	 * On wp_logout remove the current access token
	 *
	 * @since   2.0.0
     * 
	 */
    public function on_logout()
    {
        $this->userId = wp_get_current_user()->ID;
        $this->revoke();
    }

    /**
	 * Get claims form token
	 *
	 * @since   2.0.0
     * @param   JWT token
     * 
	 */
    public static function decodeToken($token)
    {
        list($header, $payload, $signature) = explode (".", $token);
        return json_decode(base64_decode($payload));
    }

    /**
	 * Ajax call for revoking token on disconnection
	 *
	 * @since   2.0.0
     * 
	 */
    public function ajax_revoke_token()
    {
        $user = $_POST['user'];

        if ($user != 'no') {
            $this->userId = wp_get_current_user()->ID;
        }

        try {

            $this->revoke(true);
            delete_user_meta($this->userId, 'petjeaf_user_id');

            wp_send_json_success();
            
        } catch (\Throwable $th) {

            wp_send_json_error([
                'message' => __($th->getMessage(), 'petje-af')
            ]);
        }
    }

    /**
	 * Ajax call for exchanging code for an access and refresh token
	 *
	 * @since   2.0.0
     * 
	 */
    public function ajax_exchange_code_for_token() 
    {
        $code = $_POST['code'];
        $redirect = $_POST['redirect'];
        $user = $_POST['user'];
        $state = $_POST['state'];

        $this->cache = new Petje_Af_Cache();

        if (!$code) {
            wp_send_json_error([
                'message' => __('Code is not set', 'petje-af'),
                'redirect' => $redirect
            ]);            
        }

        if (!$state || $state && !get_transient('petje_af_state_' . $state)) {
            wp_send_json_error([
                'message' => __('State is not valid', 'petje-af'),
                'redirect' => $redirect
            ]);            
        }

        try {

            $this->getAcccesTokenByCode($code);

            $fromUser = true;

            if ($user == 'no') {
                $fromUser = false;
                $this->saveAccessToken();
                $this->saveRefreshToken();
            }

            $setup = new Petje_Af_OAuth2_Setup($fromUser, $this->accessToken);
           
            $setup->run();

            if ($fromUser) {
                $this->userId = wp_get_current_user()->ID;
                $this->cache->setUser($this->userId);
                $this->saveAccessToken();
                $this->saveRefreshToken();
            }
            
            wp_send_json_success([
                'redirect' => $redirect
            ]);
        } catch (\Throwable $th) {
            wp_send_json_error([
                'message' => __($th->getMessage(), 'petje-af'),
                'redirect' => $redirect
            ]);
        }
    }
}