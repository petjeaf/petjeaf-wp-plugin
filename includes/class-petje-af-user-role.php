<?php

/**
 * User Role.
 *
 * Class responsible for creating the custom user role for Petje.af members
 * and how this role should act.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * User Role.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */

class Petje_Af_User_Role
{
    /**
     * Add petjeaf_member role on activation of the plugin
     *
     * @since   2.0.0
     * 
     */
    public function add_role()
    {
        add_role('petjeaf_member', __('Petje.af member'), ['read' => true]);
    }

    /**
     * Hide admin bar for petjeaf_member. 
     * 
     * Called on after_theme_setup
     *
     * @since   2.0.0
     * 
     */
    public function hide_admin_bar()
    {
        if (current_user_can('petjeaf_member')) {
            show_admin_bar(false);
        }
    }

    /**
     * On wp_logout redirect petjeaf_member to Petje.af account page.
     *
     * @since   2.0.0
     * 
     */
    public function logout_redirect()
    {
        if (current_user_can('petjeaf_member')) {
            wp_redirect(get_permalink(get_option('petje_af_account_page')));
            exit;
        }       
    }
}