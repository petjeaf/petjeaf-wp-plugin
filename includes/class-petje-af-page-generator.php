<?php

class Petje_Af_Page_Generator
{
    public function generatePages() 
    {
      $this->generateRedirectUriPage();
      $this->generateAccountPage();
      $this->accessDeniedPage();
    }

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

    protected function saveGeneratedPage($key, $id)
    {
      if ($id) {
        update_option($key, $id);
      }
    }

    protected function generateRedirectUriPage() 
    {
      $pageId = $this->generatePage([
        'post_title' => __('Petje.af redirect', 'petje-af'),
        'post_status' => 'publish',
        'post_content' => '[petjeaf_redirect_uri]',
      ], 'petje_af_redirect_uri_page');

      return $pageId;
    }

    protected function generateAccountPage() 
    {
      $pageId = $this->generatePage([
        'post_title' => __('Petje.af account', 'petje-af'),
        'post_status' => 'publish',
        'post_content' => '[petjeaf_account]',
      ], 'petje_af_account_page');

      return $pageId;
    }

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