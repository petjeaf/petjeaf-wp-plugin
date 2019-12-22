<?php

/**
 * User Access.
 *
 * Class responsible for determine if a user has access
 * to a certain post or not.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * User Access.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */

class Petje_Af_User_Access 
{
    /**
     * WP page ID of the access denied page.
     *
     * @since    2.0.0
     * @access   protected
     * @var      integer
     */
    protected $accessDeniedPageId;

    /**
     * Initialize class.
     *
     * @since   2.0.0
     * 
     */
    public function __construct()
    {
        $this->accessDeniedPageId = get_option('petje_af_access_denied_page');
    }

    /**
     * On template_redirect validate if user has access.
     *
     * @since   2.0.0
     * 
     */
    public function template_redirect()
    {
        global $post;

        $planId = get_post_meta($post->ID, 'petje_af_page_plan_id', true);

        $access_denied = false;

        if (!$planId) return;

        if ($planId) {
            $plans = petjeaf_cache('page_plans', false);

            $plan = null;

            if (!empty($plans)) {
                foreach ($plans as $p) {
                    if ($p->id == $planId) {
                        $plan = $p;
                    }
                }
            }

            if (!$plan) $access_denied = true;

            if ($plan) {
                if (Petje_Af_User_Access::toPlan($plan)) {
                    $access_denied = false;
                } else {
                    $access_denied = true;
                }
            }
        }

        $link = add_query_arg( 'plan_id', $planId, get_permalink($this->accessDeniedPageId));
        $link = add_query_arg( 'r', get_permalink($post->ID), $link);

        if ($access_denied) wp_redirect($link);
    }

    /**
     * Validate if user has access to content that is protected with a certain plan.
     *
     * @since   2.0.0
     * @param   object  plan
     * 
     * @return  boolean
     * 
     */
    public static function toPlan($plan) 
    {
        $active_statuses = ['active', 'active_end_month', 'active_end_year'];
        
        $membership = petjeaf_cache('membership');
    
        if (current_user_can( 'manage_options' ) && get_option('petje_af_ignore_access_settings_for_admin') ) return true;
    
        if ($membership) {
            if ($membership->planId == $plan->id && in_array($membership->status, $active_statuses)) return true;
            if ($membership->amount >= $plan->amount && in_array($membership->status, $active_statuses)) return true;
        }
    
        return false;
    }
}