<?php

/**
 * Create connection with Petje.af OAuth2 Client.
 *
 * Create connection with the Petje.af OAuth2 Client.
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

class Petje_Af_OAuth2_Provider
{
    /**
     * Instance of Petje_Af_Connector
     *
     * @since    2.0.0
     * @access   protected
     * @var      Petje_Af_Connector
     */
    protected $connector;

    /**
     * Web base url.
     *
     * @since    2.0.2
     * @access   protected
     * @var      string
     */
    protected $webBaseUrl = 'https://petje.af/';

    /**
     * The Client ID
     *
     * @since    2.0.2
     * @access   public
     * @var      string
     */
    protected $client_id;

    /**
     * The Client secret
     *
     * @since    2.0.2
     * @access   public
     * @var      string
     */
    protected $client_secret;

    /**
     * The state
     *
     * @since    2.0.2
     * @access   public
     * @var      string
     */
    protected $state;

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
     * The Refresh Token
     *
     * @since    2.0.2
     * @access   protected
     * @var      string
     */
    protected $refreshToken;

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

        $this->connector = new Petje_Af_Connector($userId);

        $this->client_id = get_option('petje_af_client_id');
        $this->client_secret = get_option('petje_af_client_secret');
    }

    /**
     * Build query parameters for authorization Url
     *
     * @since   2.0.0
     * @param   array   $scopes
     * 
     * @return Authorization query
     * 
     */
    protected function buildAuthorizationQuery($scopes = [])
    {
        $this->state = wp_generate_password(12, false);
        
        $query = [
            'client_id' => $this->client_id,
            'response_type' => 'code',
            'state' => $this->state,
            'scope' => implode(' ', $scopes)
        ];
        
        return http_build_query($query);
    }

    /**
     * Returns Authorization Url
     *
     * @since   2.0.0
     * @param   array   $scopes
     * 
     * @return Authorization Url
     * 
     */
    public function getAuthorizationUrl($scopes = [])
    {
        return $this->webBaseUrl . 'oauth2/authorize?' . $this->buildAuthorizationQuery($scopes);
    }

    /**
     * Get state from Authorization
     *
     * @since   2.0.0
     * 
     * @return  state that is used in authorization url
     * 
     */
    public function getState()
    {
        return $this->state;
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
        $res = $this->connector->post('oauth2/tokens', [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'authorization_code',
            'code' => $code
        ]);

        $this->accessToken = $res->access_token;
        $this->refreshToken = $res->refresh_token;

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
        $res = $this->connector->post('oauth2/tokens', [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ]);

        $this->accessToken = $res->access_token;
        $this->refreshToken = $res->refresh_token;

        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken
        ];
    }

    /**
     * Save access token to cache
     *
     * @since   2.0.0
     * 
     */
    protected function saveAccessToken()
    {
        $this->cache->saveField('access_token', $this->accessToken);      
    }

    /**
     * Save refresh token to cache
     *
     * @since   2.0.0
     * 
     */
    protected function saveRefreshToken()
    {
        $this->cache->saveField('refresh_token', $this->refreshToken);       
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
            
            $accessToken = $this->cache->get('access_token');

            $this->connector->setAccessToken($accessToken);
        
            $this->connector->delete('oauth2/tokens');

            $this->cache->delete('access_token');
            $this->cache->delete('membership');

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
        if ($_POST['user'] != 'yes' && $_POST['user'] != 'no') {
            wp_send_json_error([
                'message' => __('User needs to be set to yes or no', 'petje-af')
            ]);            
        }
        
        $user = $_POST['user'];

        if ($user != 'no') {
            $this->userId = wp_get_current_user()->ID;
        }

        try {

            $this->revoke(true);
            delete_user_meta($this->userId, 'petjeaf_user_id');

            if (current_user_can('petjeaf_member')) {
                wp_delete_user($this->userId);
            }

            wp_send_json_success();
            
        } catch (\Throwable $th) {

            wp_send_json_error([
                'message' => __($th->getMessage(), 'petje-af')
            ]);
        }
    }
    
    /**
     * Ajax call for getting the authorize url and saving the state to transient.
     *
     * @since   2.0.0
     * 
     */
    public function ajax_get_authorize_url() 
    {
        $scopes = [
            'profile.read',
            'memberships.read',
            'pages.read'
        ];

        try {

            $redirect_uri = $this->getAuthorizationUrl($scopes);

            set_transient('petje_af_state_' . $this->getState(), true, 1500);
            
            wp_send_json_success([
                'redirect_uri' => $redirect_uri
            ]);

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
        if (!wp_http_validate_url($_POST['redirect'])) {
            wp_send_json_error([
                'message' => __('Redirect is not a valid URL', 'petje-af')
            ]); 
        }

        if ($_POST['user'] != 'yes' && $_POST['user'] != 'no') {
            wp_send_json_error([
                'message' => __('User needs to be set to yes or no', 'petje-af')
            ]);            
        }

        if (!$_POST['code']) {
            wp_send_json_error([
                'message' => __('Code is not set', 'petje-af')
            ]);            
        }

        if (!$_POST['state']) {
            wp_send_json_error([
                'message' => __('State is not set', 'petje-af')
            ]);            
        }

        $code = sanitize_text_field($_POST['code']);
        $redirect = sanitize_text_field( $_POST['redirect']);
        $user = sanitize_text_field( $_POST['user']);
        $state = sanitize_text_field( $_POST['state']);

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