<?php

class Petje_Af_Shortcodes
{
    public function redirectUriPage() 
    {   
        $content = '<div id="#petjeaf_redirecter" class="petjeaf-redirecter">
            <div class="petjeaf-redirecter__error"></div>
            <div class="petjeaf-redirecter__loader"></div>
        </div>';

        return $content;
    }

    public function accountPage() 
    {

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
            $button .= '<span class="petje-af-access-denied-box__prefix">' . __($prefix, 'petje-af') . '</span>';
        }

        $button .= '<a href="#" id="petjeaf_connect_button" data-redirect-uri="' . $redirect_uri .'" class="petje-af-access-denied-box__link" target="_blank">' . __($button_text, 'petje-af') . '</a>';

        if ($paragraph) {
            $button .= '</p>';
        }

        return $button;

    }

    protected function findPage() {
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

    public function hideContent($atts = [], $content = null) 
    {
        $petjeaf_atts = shortcode_atts([
            'plan_id' => null,
        ], $atts);

        if ($petjeaf_atts['plan_id'] && get_option('petje_af_page_id')) {
            $page = $this->findPage();
    
            $plans = petjeaf_cache('page_plans', false);
    
            $plan = null;
    
            if (!empty($plans)) {
                foreach ($plans as $p) {
                    if ($p->id == $petjeaf_atts['plan_id']) {
                        $plan = $p;
                    }
                }
            }
    
            if ($plan && $page) {
                
                if (Petje_Af_User_Access::toPlan($plan)) return $content;
    
                return '
                <div class="petje-af-access-denied-box">'
                 . __('The following content is only visible for Petje.af members from the plan:', 'petje-af') . '<h4>' . $plan->name . '</h4>
                 <a href="https://petje.af/' . $page->slug . '/petjes" class="petje-af-access-denied-box__button-link" target="_blank">' . __('Become a member!', 'petje-af') . '</a>' . $this->loginButton('Login with Petje.af', 'Already an account?') . '</div>';
            } else {
                return '<div class="petje-af-access-denied-box"><div class="petje-af-access-denied-box__error">' . __('Access denied, but plan does not exist.', 'petje-af') . '</div></div>';
            }
        }

        if (!$petjeaf_atts['plan_id'] && get_option('petje_af_page_id')) {
            $page = $this->findPage();

            if ($page) {
                return '
                <div class="petje-af-access-denied-box">'
                 . __('The following content is only visible for Petje.af members from', 'petje-af') . '<h4>' . $page->name . '</h4>
                 <a href="https://petje.af/' . $page->slug . '" class="petje-af-access-denied-box__button-link" target="_blank">' . __('Become a member!', 'petje-af') . '</a>' . $this->loginButton('Login with Petje.af', 'Already an account?') . '</div>';     
            }  
        }

        return '<div class="petje-af-access-denied-box"><div class="petje-af-access-denied-box__error">' . __('Access denied, but page is not found.', 'petje-af') . '</div></div>';
    }
}