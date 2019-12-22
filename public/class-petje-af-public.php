<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://petje.af
 * @since      1.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/public
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af_Public {

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of the plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

  }

  /**
   * Register the stylesheets for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

    wp_enqueue_style('petje-af-google-fonts', '//fonts.googleapis.com/css?family=Nunito:400,700&display=swap', false, null);

  }

  /**
   * Register the JavaScript for the public-facing side of the site.
   *
   * @since    2.0.0
   */
  public function enqueue_scripts() {

    wp_enqueue_script( $this->plugin_name . '-oauth2', plugin_dir_url( __FILE__ ) . 'js/oauth2.js', array( 'jquery' ), $this->version, false );
    wp_localize_script( $this->plugin_name . '-oauth2', 'petjeaf_vars', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));

    if (get_the_ID() == get_option('petje_af_redirect_uri_page') || get_the_ID() == get_option('petje_af_account_page')) {
      wp_enqueue_script( $this->plugin_name . '-login', plugin_dir_url( __FILE__ ) . 'js/login.js', array( 'jquery' ), $this->version, false );
    }

    if (get_the_ID() == get_option('petje_af_account_page')) {
      wp_enqueue_script( $this->plugin_name . '-disconnect', plugin_dir_url( __FILE__ ) . 'js/disconnect.js', array( 'jquery' ), $this->version, false );
    }
  }


}