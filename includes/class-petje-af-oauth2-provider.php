<?php

use Petjeaf\OAuth2\Client\Provider\Petjeaf;

class Petje_Af_OAuth2_Provider
{
    public $provider;

    protected $userId = null;

    protected $cache;

    protected $accessToken;

    public function __construct($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
        }

        $this->setProvider();
    }

    protected function setProvider() 
    {
        $this->provider = new Petjeaf([
            'clientId' => get_option('petje_af_client_id'),
            'clientSecret' => get_option('petje_af_client_secret')
        ]);
    }

    public function getAuthorizationUrl($scopes = [])
    {
        return $this->provider->getAuthorizationUrl(['scope' => $scopes ]);
    }

    public function getState()
    {
        return $this->provider->getState();
    }

    protected function getAcccesTokenByCode($code)
    {
        $this->accessToken = $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        return $this->accessToken;
    }

    public function getAcccesTokenByRefreshToken($refreshToken)
    {
        $this->accessToken = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken
        ]);

        return $this->accessToken;
    }

    protected function saveAccessToken()
    {
        $this->cache->saveField('access_token', $this->accessToken->getToken());      
    }

    protected function saveRefreshToken()
    {
        $this->cache->saveField('refresh_token', $this->accessToken->getRefreshToken());       
    }

    public static function decodeToken($token)
    {
        list($header, $payload, $signature) = explode (".", $token);
        return base64_decode($payload);
    }

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