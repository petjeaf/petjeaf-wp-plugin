<?php

class Petje_Af_Shortcodes
{
    public function redirect_uri() 
    {   
        $content = '<div id="#petjeaf_redirecter" class="petjeaf-redirecter">';
        $content .= '<div class="petjeaf-redirecter__error"></div>';
        $content .= '<div class="petjeaf-redirecter__loader"></div>';
        $content .= '</div>';

        return $content;
    }

    public function access_denied() 
    {   
        $lead = __('Access denied. This content is only accessible for Petje.af members of:', 'petje-af');

        $page = $this->findPage();
        
        if (isset($_GET['plan_id']) && $page) {
            $plan_id = $_GET['plan_id'];
            $plan = $this->findPlan($plan_id);

            if ($plan) {
                return $this->accessDeniedBox(
                    sprintf( __( 'Access denied. Only accessible for Petje.af page members from %s with plan:', 'text_domain' ), $page->name ),
                    $plan->name,
                    'https://petje.af/' . $page->slug . '/petjes',
                    __('Become a member!', 'petje-af'),
                    $this->loginButton(
                        __('Login with Petje.af', 'petje-af'),
                        __('Already an account?', 'petje-af')
                    )
                );                
            }
        } elseif ($page) {
            return $this->accessDeniedBox(
                __('Access denied. Only accessible for Petje.af page members from:', 'petje-af'),
                $page->name,
                'https://petje.af/' . $page->slug,
                __('Become a member!', 'petje-af'),
                $this->loginButton(
                    __('Login with Petje.af', 'petje-af'),
                    __('Already an account?', 'petje-af')
                )
            );
        }

        return $this->accessDeniedBoxError(
            __('Page is not found. Please contact the owner of this site', 'petje-af')
        );
    }

    public function account_page() 
    {
        
    }

    public function hide_content($atts = [], $content = null) 
    {
        $petjeaf_atts = shortcode_atts([
            'plan_id' => null,
        ], $atts);

        if ($petjeaf_atts['plan_id'] && get_option('petje_af_page_id')) {
            $page = $this->findPage();
    
            $plan = $this->findPlan($petjeaf_atts['plan_id']);
    
            if ($plan && $page) {
                
                if (Petje_Af_User_Access::toPlan($plan)) return $content;

                return $this->accessDeniedBox(
                    __('The following content is only visible for Petje.af members from the plan:', 'petje-af'),
                    $plan->name,
                    'https://petje.af/' . $page->slug . '/petjes',
                    __('Become a member!', 'petje-af'),
                    $this->loginButton(
                        __('Login with Petje.af', 'petje-af'),
                        __('Already an account?', 'petje-af')
                    )
                );
            } else {
                return $this->accessDeniedBoxError(__('Access denied, but plan does not exist.', 'petje-af'));
            }
        }

        if (!$petjeaf_atts['plan_id'] && get_option('petje_af_page_id')) {
            $page = $this->findPage();

            if ($page) {
                return $this->accessDeniedBox(
                    __('The following content is only visible for Petje.af members from', 'petje-af'),
                    $page->name,
                    'https://petje.af/' . $page->slug,
                    __('Become a member!', 'petje-af'),
                    $this->loginButton(
                        __('Login with Petje.af', 'petje-af'),
                        __('Already an account?', 'petje-af')
                    )
                );
            }  
        }

        return $this->accessDeniedBoxError(__('Access denied, but page is not found.', 'petje-af'));
    }

    protected function loginButton($button_text = '', $prefix = '', $paragraph = true)
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $petjeaf_id = get_user_meta($user->ID, 'petjeaf_user_id');

            if ($petjeaf_id) return '';
        }

        $oauth2_provider = new Petje_Af_OAuth2_Provider();
    
        $redirect_uri = $oauth2_provider->getAuthorizationUrl([ 'profile.read', 'memberships.read', 'pages.read']);
        
        set_transient('petje_af_state_' . $oauth2_provider->getState(), true, 3600);

        $button = '';

        if ($paragraph) {
            $button .= '<p>';
        }

        if ($prefix) {
            $button .= '<span class="petje-af-access-denied-box__prefix">' . $prefix . '</span>';
        }

        $button .= '<a href="#" data-redirect-uri="' . $redirect_uri .'" class="petjeaf-connect-button petje-af-access-denied-box__link" target="_blank">' . $button_text . '</a>';

        if ($paragraph) {
            $button .= '</p>';
        }

        return $button;

    }

    protected function findPage() 
    {
        $pages = petjeaf_cache('pages', false);
        $page = null;

        if (!empty($pages)) {
            foreach($pages as $p) {
                if ($p->id == get_option('petje_af_page_id')) {
                    $page = $p;
                }
            }
        }

        return $page;
    }

    protected function findPlan($planId)
    {
        $plans = petjeaf_cache('page_plans', false);
    
        $plan = null;

        if (!empty($plans)) {
            foreach ($plans as $p) {
                if ($p->id == $planId) {
                    $plan = $p;
                }
            }
        }

        return $plan;
    }

    protected function accessDeniedBox($lead, $title, $link, $button_text, $login_button)
    {
        $content = '<div class="petje-af-access-denied-box">';

        $content .= $lead;

        if ($title) $content .= '<h4>' . $title . '</h4>';

        if ($link) $content .=  '<a href="' . $link . '" class="petje-af-access-denied-box__button-link" target="_blank">' . $button_text . '</a>';

        $content .= $login_button;

        $content .= '</div>';

        return $content;
    }

    protected function accessDeniedBoxError($error) {

        $content = '<div class="petje-af-access-denied-box">';

        $content .= '<div class="petje-af-access-denied-box__error">' . $error . '</div>';

        $content .= '</div>';

        return $content;
    }
}