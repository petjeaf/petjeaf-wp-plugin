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
     * WP page ID of the redirect page.
     *
     * @since    2.1.0
     * @access   protected
     * @var      integer
     */
    protected $redirectPageId;

    /**
     * Initialize class.
     *
     * @since   2.0.0
     * 
     */
    public function __construct()
    {
        $this->accessDeniedPageId = get_option('petje_af_access_denied_page');
        $this->redirectPageId = get_option('petje_af_redirect_uri_page');
    }

    /**
     * On template_redirect validate if user has access.
     *
     * @since   2.0.0
     * 
     */
    public function template_redirect()
    {
        if (!is_singular() || is_admin()) return;

        global $post;

        if (empty( $post )) return;

        if ($post->ID == $this->redirectPageId) return;

        if ($post->ID == $this->accessDeniedPageId) return;

        $planId = get_post_meta($post->ID, 'petje_af_page_plan_id', true) ? get_post_meta($post->ID, 'petje_af_page_plan_id', true) : get_option('petje_af_site_protection_plan', '');

        $access_denied = false;

        if (!$planId) return;

        $access_denied = $this->accessDeniedtoPlanById($planId);

        $link = add_query_arg( 'plan_id', $planId, get_permalink($this->accessDeniedPageId));
        $link = add_query_arg( 'r', get_permalink($post->ID), $link);

        if ($access_denied) wp_redirect($link);
    }

    /**
     * On wp action validate if user has access.
     *
     * @since   2.0.0
     * 
     */
    public function wp()
    {
        if (is_admin()) return;

        global $post;

        if (!empty( $post ) && $post->ID == $this->redirectPageId) return;

        if (!empty( $post ) && $post->ID == $this->accessDeniedPageId) return;

        $planId = get_option('petje_af_site_protection_plan', '');

        $access_denied = false;

        if (!$planId) return;

        $access_denied = $this->accessDeniedtoPlanById($planId);

        $link = add_query_arg( 'plan_id', $planId, get_permalink($this->accessDeniedPageId));
        $link = add_query_arg( 'r', get_permalink($post->ID), $link);

        if ($access_denied) wp_redirect($link);
    }

    /**
     * Validate if user has access to content that is protected with a certain plan by id.
     *
     * @since   2.1.4
     * @param   string  planId
     * 
     * @return  boolean
     * 
     */
    protected function accessDeniedtoPlanById($planId)
    {   
        if (!$planId) return false;

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

        return $access_denied;
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
        $active_statuses = ['active', 'active_end', 'active_end_month', 'active_end_year'];
        
        $membership = petjeaf_cache('membership');
        
        if (get_option('petje_af_ignore_access_settings_for_admin')) {
            if (current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' )  ) return true;
        }
        
        if ($membership) {
            if ($membership->planId == $plan->id && in_array($membership->status, $active_statuses)) return true;
            if ($membership->amount >= $plan->amount && in_array($membership->status, $active_statuses)) return true;
        }
    
        return false;
    }
}