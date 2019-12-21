<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://petje.af
 * @since      1.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Petje_Af_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PETJE_AF_VERSION' ) ) {
			$this->version = PETJE_AF_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'petje-af';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Petje_Af_Loader. Orchestrates the hooks of the plugin.
	 * - Petje_Af_i18n. Defines internationalization functionality.
	 * - Petje_Af_Admin. Defines all hooks for the admin area.
	 * - Petje_Af_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		// Classes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-petje-af-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-petje-af-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-petje-af-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-petje-af-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ). 'includes/class-petje-af-connector.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ). 'includes/class-petje-af-oauth2-provider.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ). 'includes/class-petje-af-page-generator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ). 'public/class-petje-af-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-petje-af-cache.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-petje-af-oauth2-setup.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-petje-af-user.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-petje-af-user-access.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/petje-af-main-widget.php';

		$this->loader = new Petje_Af_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Petje_Af_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Petje_Af_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Petje_Af_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'widgets_init', $plugin_admin, 'register_widgets' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_box');
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_box');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Petje_Af_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$oauth2_provider = new Petje_Af_OAuth2_Provider();

		$this->loader->add_action('wp_ajax_nopriv_petjeaf_code_for_token', $oauth2_provider, 'ajax_exchange_code_for_token');
		$this->loader->add_action('wp_ajax_petjeaf_code_for_token', $oauth2_provider, 'ajax_exchange_code_for_token');

		$this->loader->add_action('wp_ajax_nopriv_petjeaf_disconnect', $oauth2_provider, 'ajax_revoke_token');
		$this->loader->add_action('wp_ajax_petjeaf_disconnect', $oauth2_provider, 'ajax_revoke_token');

		$user_access = new Petje_Af_User_Access();

		$this->loader->add_action('template_redirect', $user_access, 'template_redirect');

		// Shortcodes
		$shortcodes = new Petje_Af_Shortcodes();

		$this->loader->add_shortcode('petjeaf_redirect_uri', $shortcodes, 'redirect_uri' );
		$this->loader->add_shortcode('petjeaf_hide_content', $shortcodes, 'hide_content' );
		$this->loader->add_shortcode('petjeaf_access_denied', $shortcodes, 'access_denied' );
		$this->loader->add_shortcode('petjeaf_account', $shortcodes, 'account_page' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Petje_Af_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
