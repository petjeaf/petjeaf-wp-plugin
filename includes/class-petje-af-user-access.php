<?php

class Petje_Af_User_Access 
{
    protected $accessDeniedPageId;

    public function __construct()
    {
        $this->accessDeniedPageId = get_option('petje_af_access_denied_page');
    }

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

        if ($access_denied) wp_redirect(get_permalink($this->accessDeniedPageId));
    }

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