<?php

/**
 * Setup OAuth2 after creating connection.
 *
 * Class responsible for the OAuth2 setup after 
 * a Petje.af account is created.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Setup OAuth2 after creating connection.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af_OAuth2_Setup
{
    /**
     * Determine if setup is for page or user
     *
     * @since    2.0.0
     * @access   protected
     * @var      boolean
     */
    protected $fromUser;

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
     * @param   $fromUser   boolean
     * @param   $accessToken    to call api without access token in database
     * 
     */
    public function __construct($fromUser, $accessToken)
    {
        $this->fromUser = $fromUser;
        $this->accessToken = $accessToken;

        $this->cache = new Petje_Af_Cache();
    }

    /**
     * Run setup
     *
     * @since   2.0.0
     * 
     */
    public function run()
    {
        if (!$this->fromUser) {
            $this->setupPages();
        } else {
            $this->setupUser();
        }
    }

    /**
     * Set access token if needed
     *
     * @since   2.0.0
     * @param   $accessToken
     * 
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Setup user
     *
     * @since   2.0.0
     * 
     */
    protected function setupUser() 
    {
        $user = new Petje_Af_User();
        $user->set($this->accessToken);
    }

    /**
     * Setup for page
     *
     * @since   2.0.0
     * 
     */
    protected function setupPages()
    {
        $pages = $this->cache->get('pages');

        if (!empty($pages)) {
            $page = $pages[0];
            if ($page->id) {
                $pageId = sanitize_text_field($page->id);
                update_option('petje_af_page_id', $pageId);
            }
        }
    }

}