<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://petje.af
 * @since      1.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/admin
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register settings.
	 *
	 * @since    2.0.0
	 */
	public function register_settings() {
		register_setting( 'petje_af_settings', 'petje_af_client_id');
		register_setting( 'petje_af_settings', 'petje_af_client_secret');
        register_setting( 'petje_af_settings', 'petje_af_page_id');
        register_setting( 'petje_af_settings', 'petje_af_ignore_access_settings_for_admin');
	}

	/**
	 * Add admin menu.
	 *
	 * @since    2.0.0
	 */
	public function admin_menu() {

		add_options_page( __('Petje.af','petje-af'), __('Petje.af','petje-af'), 'manage_options', 'petje-af', array($this,'html') );

	}

	/**
	 * Html for option page.
	 *
	 * @since    2.0.0
	 */
	public function html() {

		if ( !current_user_can( 'manage_options' ) )  {
		  wp_die( __( 'You do not have sufficient permissions to access this page.', 'petje-af' ) );
		}

		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/petje-af-admin-display.php';

	}

	/**
	 * Add metabox.
	 *
	 * @since    2.0.0
	 */
    public function add_meta_box() {

        $screens = apply_filters('petje_af_post_types', ['post', 'page']);

        foreach ( $screens as $screen ) {
            add_meta_box('petjeaf-meta-box', __('Petje.af settings', 'petje-af'), array($this, 'meta_box_html'), $screen, 'side');
        }
    }
    
	/**
	 * Add metabox HTML.
	 *
	 * @since    2.0.0
	 */
    public function meta_box_html() {
        include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/petje-af-meta-box.php';
    }
    
	/**
	 * Save metabox
	 *
	 * @since    2.0.0
	 */
    public function save_meta_box($post_id) {

        if ( ! isset( $_POST['petje_af_meta_box_nonce'] ) ) {
            return;
        }      
        
        if ( ! wp_verify_nonce( $_POST['petje_af_meta_box_nonce'] , 'petje_af_meta_box_nonce' ) ) {
            return;
        }

        if ( ! isset( $_POST['petje_af_page_plan_id'] ) ) {
            return;
        }

        update_post_meta($post_id, 'petje_af_page_plan_id', $_POST['petje_af_page_plan_id']);
    }

    /**
    *  Dropdown for pages in admin.
    *
    *  @since	2.0.0
    */
    public static function pages_dropdown() {
        
        $pages = petjeaf_cache('pages', false);

        $dropdown = '<select name="petje_af_page_id" id="petje_af_page_id">';
    
        if (!empty($pages)) {
            foreach($pages as $page) {
                $selected = get_option('petje_af_page_id') == $page->id ? 'selected' : '';
                $dropdown .= '<option value="' . $page->id . '"' . $selected .'>' . $page->name . '</option>';
            }
        }
    
        $dropdown .= '</select>';
    
        return $dropdown;
    }

    /**
    *  Dropdown for plans in admin.
    *
    *  @since	2.0.0
    */
    public static function page_plans_dropdown($post_id) {
        
        $pagePlans = petjeaf_cache('page_plans', false);

        $dropdown = '<select id="petje_af_plan_select" name="petje_af_page_plan_id" id="petje_af_page_plan_id" class="components-select-control__input">';

        $selected = get_post_meta($post_id, 'petje_af_page_plan_id', true)? '' : 'selected';
        $dropdown .= '<option value="" ' . $selected .'>' . __('Public', 'petje-af') . '</option>';

        if (!empty($pagePlans)) {
            foreach($pagePlans as $pagePlan) {
                $selected = get_post_meta($post_id, 'petje_af_page_plan_id', true) == $pagePlan->id ? 'selected' : '';
                $dropdown .= '<option value="' . $pagePlan->id . '"' . $selected .'>' . $pagePlan->name . '</option>';
            }
        }

        $dropdown .= '</select>';

        return $dropdown;
    }
    

	/**
	 * Register all widgets.
	 *
	 * @since    1.0.0
	 */
    public function register_widgets() 
    {
        register_widget( 'Petje_Af_Main_Widget' );
    }
    
    /**
    *  Display post states.
    *
    *  @since	2.0.0
    */
    public function display_post_states($post_states, $post)
    {
        if ('page' === get_post_type($post->ID) && $post->ID == get_option('petje_af_account_page')) {
            $post_states[] = __('Petje.af account', 'petje-af'); 
        }

        if ('page' === get_post_type($post->ID) && $post->ID == get_option('petje_af_access_denied_page')) {
            $post_states[] = __('Petje.af access denied', 'petje-af'); 
        }

        if ('page' === get_post_type($post->ID) && $post->ID == get_option('petje_af_redirect_uri_page')) {
            $post_states[] = __('Petje.af redirect ui', 'petje-af'); 
        }

        return $post_states;
    }

}