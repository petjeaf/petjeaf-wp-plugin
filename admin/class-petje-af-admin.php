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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() 
    {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/petje-af-admin.css', array(), $this->version );
    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'petjeaf_vars', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        ));
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
    public function admin_menu() 
    {
        add_menu_page( __('Petje.af','petje-af'), __('Petje.af','petje-af'), 'manage_options', 'petje-af', array($this,'html'), 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMTcxcHgiIGhlaWdodD0iMTcycHgiIHZpZXdCb3g9IjAgMCAxNzEgMTcyIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA1MyAoNzI1MjApIC0gaHR0cHM6Ly9za2V0Y2hhcHAuY29tIC0tPgogICAgPHRpdGxlPkxvZ28gYmxhY2tAMng8L3RpdGxlPgogICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+CiAgICA8ZyBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyBpZD0iTG9nby1ibGFjayIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsIC04OS4wMDAwMDApIiBmaWxsPSIjNTEwRkE4IiBmaWxsLXJ1bGU9Im5vbnplcm8iPgogICAgICAgICAgICA8cGF0aCBkPSJNMTU2LjcyMDU1LDEyNy4xMzUwMDEgQzE1NC4xNDc4NDEsMTI2LjU5ODg3NyAxNTEuNDgxODMsMTI2LjMxNzA3MyAxNDguNzUsMTI2LjMxNzA3MyBDMTI3LjI4MTYxNCwxMjYuMzE3MDczIDEwOS44NzgwNDksMTQzLjcyMDYzOSAxMDkuODc4MDQ5LDE2NS4xODkwMjQgQzEwOS44NzgwNDksMTg2LjY1NzQxIDEyNy4yODE2MTQsMjA0LjA2MDk3NiAxNDguNzUsMjA0LjA2MDk3NiBDMTU1LjUwMzIzNywyMDQuMDYwOTc2IDE2MS44NTQyNSwyMDIuMzM4ODU2IDE2Ny4zODc4NDYsMTk5LjMwOTgxMiBDMTYyLjk4MTY0NCwyMTMuODc5OTY4IDE1NC43OTIzODQsMjI2LjgwNzQ3IDE0My45NjI2NTUsMjM2Ljk0OTczIEwxMzAuOTUyNzA3LDIwNy4wNTQxNzggQzEyNi44NDkzNTIsMjA1LjU1ODcwNSAxMjMuODY0NDg3LDIwNC4yOTMxOTUgMTIxLjk5ODExNCwyMDMuMjU3NjUgQzEyMC4xMzE3NCwyMDIuMjIyMTA0IDExNy42Nzg1ODIsMjAwLjQ3MDM2MiAxMTQuNjM4NjQsMTk4LjAwMjQyMiBMNzkuOTEyODU2NCwyMDguNDQ2OTEgQzc1LjI2OTE3NDgsMjA5LjQwNTM2MyA3MS44NTAyNjkzLDIwOS44ODQ1OSA2OS42NTYxMzk5LDIwOS44ODQ1OSBDNjcuNDYyMDEwNCwyMDkuODg0NTkgNjQuNjQwNTAwOCwyMDkuMTcyODcgNjEuMTkxNjEwOSwyMDcuNzQ5NDI5IEw1MC4wNzIzNTA4LDIwMi4xNDg2MDEgQzUwLjk3Mzk4NTQsMTk5LjkyODQ0MiA1MS44MDg2MjI4LDE5OC4yMDk0MzEgNTIuNTc2MjYyOSwxOTYuOTkxNTcgQzUzLjM0MzkwMywxOTUuNzczNzA4IDU0LjUwMjkyNDIsMTkzLjk5NzM0OCA1Ni4wNTMzMjY2LDE5MS42NjI0OSBMNTYuNjg1ODM5MywxNzYuMjUyNTU0IEM1My45MDQ1Mjg3LDE3My43MTcxNzEgNTEuNzAwMDMyNSwxNzEuODg3MDQ0IDUwLjA3MjM1MDgsMTcwLjc2MjE3NCBDNDguNDQ0NjY5LDE2OS42MzczMDQgNDYuMzUwODAzOSwxNjguNDk2ODg4IDQzLjc5MDc1NTIsMTY3LjM0MDkyNyBDNDMuNjM4ODMxNiwxNjIuNDc1OTg2IDQzLjYzODgzMTYsMTU4LjgyNjgxOSA0My43OTA3NTUyLDE1Ni4zOTM0MjcgQzQzLjk0MjY3ODgsMTUzLjk2MDAzNCA0NC4zODY1NSwxNTAuNDk5NjI0IDQ1LjEyMjM2ODksMTQ2LjAxMjE5NSBDNDMuMDg3MzAxMywxNDYuMjczNDk3IDQxLjQwMzY4NDEsMTQ2LjYwNDM1NSA0MC4wNzE1MTcyLDE0Ny4wMDQ3NjggQzM5LjM3Mjk4MjQsMTQ3LjIxNDcyOSAzNy42Mjk2MzU5LDE0OC4yOTg3MiAzNi4yNTYyNDEzLDE0OS42ODk5ODIgQzM1LjQyNTcxNTMsMTUwLjUzMTMxMyAzNC4wODc4NjczLDE1Mi41MDEwOSAzMi4yNDI2OTcyLDE1NS41OTkzMTUgQzI5LjQyMTYxNjIsMTc0LjcwMDk1NiAyOC40NTUxOTc4LDE4OC44MzUzMjUgMjkuMzQzNDQxOSwxOTguMDAyNDIyIEMzMC4yMzE2ODYxLDIwNy4xNjk1MTkgMzIuNTM1OTUyNSwyMTQuMzgwODMxIDM2LjI1NjI0MTMsMjE5LjYzNjM1OSBDNDMuMjQ3ODMzNiwyMjkuMzM3NzM2IDUxLjU1OTYyMzQsMjM0LjkxMzE0NyA2MS4xOTE2MTA5LDIzNi4zNjI1OSBDNzAuODIzNTk4NCwyMzcuODEyMDMzIDg3LjUyNTUyMzUsMjM0Ljg0Mjg3NCAxMTEuMjk3Mzg2LDIyNy40NTUxMTQgTDExMS4yOTczODYsMjU2LjA4MjYxMiBDMTAzLjE2MjU1MiwyNTguNjUxMjc0IDk0LjUwMjIwMDksMjYwLjAzNjU4NSA4NS41MTgyOTI3LDI2MC4wMzY1ODUgQzM4LjI4Nzg0MzgsMjYwLjAzNjU4NSAwLDIyMS43NDg3NDIgMCwxNzQuNTE4MjkzIEMwLDEyNy4yODc4NDQgMzguMjg3ODQzOCw4OSA4NS41MTgyOTI3LDg5IEM5NS40ODY5Mzc3LDg5IDEwNS4wNTcyMDgsOTAuNzA1NjQ2NSAxMTMuOTUzMTg1LDkzLjg0MTAyMTYgQzExMC42MDA0NDgsMTAwLjA1MzA5NSAxMDguMzI2MTYzLDEwNC4yNTY0ODkgMTA3LjEzMDMzLDEwNi40NTEyMDMgQzEwNC40MTMyOTMsMTExLjQzNzc4NSAxMDIuNjgzNzEyLDExNC4xNDU5ODYgMTAzLjIyNDAxMywxMTQuNDU4MDU1IEMxMDQuMTUzMjA5LDExNC45OTQ3NDMgOTYuMzkzMDU2NiwxMTEuNTQ2MjU4IDc5Ljk0MzU1NTcsMTA0LjExMjU5OSBDNjguMzk4MjQyNywxMTMuMTk2Mzg5IDYwLjU5MDIxNTUsMTIxLjQwMjM5MSA1Ni41MTk0NzQsMTI4LjczMDYwMyBDNTIuNDQ4NzMyNSwxMzYuMDU4ODE1IDQ5LjE1NzY4ODMsMTQ3Ljg2ODU0OCA0Ni42NDYzNDE1LDE2NC4xNTk4MDEgTDY3LjMyNDc1NiwxNzcuOTk1NjY4IEw1NS4yNjMxNTkxLDIwMC4xNDUyMyBMNjEuMTgwOTE3LDIwNC4wNjA5NzYgTDExOS43NDQ0NDUsOTYuMTI0MTAxNCBDMTM0Ljg0NTMxNCwxMDIuNzI2MyAxNDcuNjcxMjcyLDExMy41NjM4NTYgMTU2LjcyMDU1LDEyNy4xMzUwMDEgTDE1Ni43MjA1NSwxMjcuMTM1MDAxIFogTTE3MCwxNzYuMDczMTcxIEMxNjkuMjg2MjgyLDE3OC42ODg2MTIgMTY4LjU1OTMwMiwxODAuNjM4MTg2IDE2Ny44MTkwNiwxODEuOTIxODkzIEMxNjcuMDc4ODE4LDE4My4yMDU2IDE2NS42MjcwMjYsMTg1LjAzNDM5MSAxNjMuNDYzNjg2LDE4Ny40MDgyNjYgQzE2MC4wNTc5NjEsMTkwLjE2NzE0MiAxNTcuMzg0NTk4LDE5MS44OTkzMzkgMTU1LjQ0MzU5NywxOTIuNjA0ODU5IEMxNTMuNTAyNTk3LDE5My4zMTAzNzkgMTUwLjU5MzA1OSwxOTMuNjczOCAxNDYuNzE0OTg0LDE5My42OTUxMjIgQzE0My43MzgyNDksMTkyLjc0NjIyMSAxNDEuNDU5Mjc2LDE5MS43NDgwOTcgMTM5Ljg3ODA2MywxOTAuNzAwNzQ4IEMxMzguMjk2ODUsMTg5LjY1MzM5OSAxMzYuMjQ0LDE4Ny44OTQ1MjggMTMzLjcxOTUxMiwxODUuNDI0MTM0IEMxNDEuNjc3MDY1LDE4NC44MzU1NDYgMTQ3LjY4OTY2MywxODQuMjAwMjI0IDE1MS43NTczMDcsMTgzLjUxODE2OSBDMTU1LjgyNDk1MSwxODIuODM2MTEzIDE2MS45MDU4NDksMTgwLjM1NDQ0NyAxNzAsMTc2LjA3MzE3MSBaIiBpZD0iQ29tYmluZWQtU2hhcGUiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==', 100 );
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

        $dropdown = '<select id="petje_af_plan_select" name="petje_af_page_plan_id" id="petje_af_page_plan_id" class="">';

        $selected = get_post_meta($post_id, 'petje_af_page_plan_id', true)? '' : 'selected';
        $dropdown .= '<option value="" ' . $selected .'>' . __('Public', 'petje-af') . '</option>';

        if (!empty($pagePlans)) {
            foreach($pagePlans as $pagePlan) {
                $selected = get_post_meta($post_id, 'petje_af_page_plan_id', true) == $pagePlan->id ? 'selected' : '';
                $dropdown .= '<option value="' . $pagePlan->id . '"' . $selected .'>' . $pagePlan->name . ' (' . Petje_Af_Formatter::amount($pagePlan->amount) . ' ' . Petje_Af_Formatter::interval($pagePlan) . ')</option>';
            }
        }

        $dropdown .= '</select>';

        return $dropdown;
    }

    /**
    *  Shortcode examples for page plans in meta box.
    *
    *  @since	2.0.1
    */
    public static function page_plans_shortcode_examples() {
        
        $pagePlans = petjeaf_cache('page_plans', false);

        $shortcodes = '';

        if (!empty($pagePlans)) {
            foreach($pagePlans as $pagePlan) {
                $shortcodes .= '<h5>' . $pagePlan->name . ':</h5><p>[petjeaf_hide_content plan_id="' . $pagePlan->id . '"]<br>
                    JOUW AFGESCHERMDE CONTENT<br>[/petjeaf_hide_content]</p>';
            }
        }

        return $shortcodes;
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