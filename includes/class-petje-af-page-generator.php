<?php

/**
 * Page Generator.
 *
 * Create the Petje.af pages in WordPress after 
 * plugin activation.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Page Generator.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af_Page_Generator
{
    /**
	 * Page generation that is called on setup
	 *
	 * @since   2.0.0
     * 
	 */
    public function generatePages() 
    {
      $this->generateRedirectUriPage();
      $this->generateAccountPage();
      $this->accessDeniedPage();
    }

    /**
	 * General function used for page generation
	 *
	 * @since   2.0.0
     * @param   array   $arg for generating the page
     * @param   string  $key in database
     * 
	 */
    protected function generatePage($arg = [], $key) 
    {
      if (get_option($key)) return;

      $arg = array_merge($arg, [
        'post_type' => 'page'      
      ]);

      $pageId = wp_insert_post($arg);

      $this->saveGeneratedPage($key, $pageId);

      return $pageId;
    }

    /**
	 * Save generated page by key
	 *
	 * @since   2.0.0
     * @param   $key in database
     * @param   $id of generated page
     * 
	 */
    protected function saveGeneratedPage($key, $id)
    {
      if ($id) {
        update_option($key, $id);
      }
    }

    /**
	 * Generate redirect uri page
	 *
	 * @since   2.0.0
     * 
	 */
    protected function generateRedirectUriPage() 
    {
      $pageId = $this->generatePage([
        'post_title' => __('Petje.af redirect', 'petje-af'),
        'post_status' => 'publish',
        'post_content' => '[petjeaf_redirect_uri]',
      ], 'petje_af_redirect_uri_page');

      return $pageId;
    }

    /**
	 * Generate account page
	 *
	 * @since   2.0.0
     * 
	 */
    protected function generateAccountPage() 
    {
      $pageId = $this->generatePage([
        'post_title' => __('Petje.af account', 'petje-af'),
        'post_status' => 'publish',
        'post_content' => '[petjeaf_account]',
      ], 'petje_af_account_page');

      return $pageId;
    }

    /**
	 * Generate access denied page
	 *
	 * @since   2.0.0
     * 
	 */
    protected function accessDeniedPage() 
    {
      $pageId = $this->generatePage([
        'post_title' => __('Access denied', 'petje-af'),
        'post_status' => 'publish',
        'post_content' => '[petjeaf_access_denied]',
      ], 'petje_af_access_denied_page');

      return $pageId;
    }
}