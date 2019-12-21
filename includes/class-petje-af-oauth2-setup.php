<?php

class Petje_Af_OAuth2_Setup
{
    public $provider;

    protected $fromUser;

    protected $cache;

    protected $accessToken;

    public function __construct($fromUser, $accessToken)
    {
        $this->fromUser = $fromUser;
        $this->accessToken = $accessToken;

        $this->cache = new Petje_Af_Cache();
    }

    public function run()
    {
        if (!$this->fromUser) {
            $this->setupPages();
        } else {
            $this->setupUser();
        }
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    protected function setupUser() 
    {
        $user = new Petje_Af_User();
        $user->set($this->accessToken);
    }

    protected function setupPages()
    {
        $pages = $this->cache->get('pages');

        if (!empty($pages)) {
            $page = $pages[0];
            update_option('petje_af_page_id', $page->id);
        }
    }

}