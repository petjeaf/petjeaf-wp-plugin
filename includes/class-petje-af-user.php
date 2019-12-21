<?php

class Petje_Af_User
{
    protected $petje_user_id_key = 'petjeaf_user_id';

    protected $userId;

    protected $connector;

    protected $client;

    public function __construct()
    {
        $accessToken = petjeaf_cache('access_token');
        $this->connector = new Petje_Af_Connector(null, $accessToken);
    }

    protected function userEmailAlreadyExist($email) {
        $user = get_user_by('email', $email);
        return $user;
    }

    protected function petjeafUserAlreadyExist($petjeaf_id) {
        $user = get_users([
            'meta_key' => $this->petje_user_id_key,
            'meta_value' => $petjeaf_id,
            'number' => 1
        ]);
        if (empty($user)) return null;
        return $user[0];
    }

    protected function validateNotLoggedInUser($user_from_token)
    {
        $user_exist = $this->petjeafUserAlreadyExist($user_from_token->id);

        if ($user_exist) {

            $this->userId = $user_exist->ID;

            return true;

        } else {

            if ($this->userEmailAlreadyExist($user_from_token->email)) {
                throw new Exception(__('There is already an account on this website associated with your email address. First login to connect with Petje.af', 'petje-af'));
            }

            $this->userId = $this->createUser($user_from_token);

            return true;
        }
    }

    protected function validateLoggedInUser($user_from_token)
    { 
        $wp_user = wp_get_current_user();
        $petjeaf_user_id = get_user_meta($wp_user->ID, $this->petje_user_id_key, true);

        if (
            !$petjeaf_user_id && $this->petjeafUserAlreadyExist($user_from_token->id) || 
            $petjeaf_user_id && $petjeaf_user_id != $user_from_token->id && $this->petjeafUserAlreadyExist($user_from_token->id)) {
            throw new Exception(__('There is already an other account on this website associated with this Petje.af account', 'petje-af'));
        }

        $this->userId = $wp_user->ID;

        update_user_meta($this->userId, $this->petje_user_id_key, $user_from_token->id);

        return true;

    }

    public function set($accessToken)
    {
        $this->connector->setAccessToken($accessToken);
        $this->client = $this->connector->client;
        
        $user_from_token = $this->client->users->me();
        
        if (is_user_logged_in()) {

            if ($this->validateLoggedInUser($user_from_token)) {
                return $this->userId;
            }

        }

        if ($this->validateNotLoggedInUser($user_from_token)) {
            return $this->logUserIn();
        }

        return null;
    }

    protected function createUser($user_from_token)
    {
        $this->userId = wp_insert_user([
            'user_login' => $user_from_token->email,
            'user_email' => $user_from_token->email,
            'display_name' => $user_from_token->name,
            'user_pass' => wp_generate_password()
        ]);

        update_user_meta($this->userId, 'petjeaf_user_id', $user_from_token->id);

        return $this->userId;
    }

    protected function logUserIn()
    {
        wp_clear_auth_cookie();
        wp_set_current_user($this->userId);
        wp_set_auth_cookie($this->userId);

        return $this->userId;
    }
}