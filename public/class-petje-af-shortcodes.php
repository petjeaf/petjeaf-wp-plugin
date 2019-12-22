<?php

/**
 * Shortcodes.
 *
 * The class that contains all the shortcodes and the helper functions
 * for displaying the shortcodes.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Shortcodes.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */
class Petje_Af_Shortcodes
{
    /**
     * The database key for the Petje.af user id
     *
     * @since    2.0.0
     * @access   protected
     * @var      string
     */
    protected $petje_user_id_key = 'petjeaf_user_id';

    /**
     * Shortcode used on the redirect_uri page.
     * 
     * Shortcode callback for "petjeaf_redirect_uri".
     *
     * @since   2.0.0     
     *  
     * @return  $content html
     * 
     */
    public function redirect_uri() 
    {   
        $content = '<div id="#petjeaf_redirecter" class="petjeaf-redirecter petjeaf-redirecter--loading">';
        $content .= '<div class="petjeaf-redirecter__error"></div>';
        $content .= '<div class="petjeaf-redirecter__loader"></div>';
        $content .= '</div>';

        return $content;
    }

    /**
     * Shortcode used on the access denied page
     * 
     * Shortcode callback for "petjeaf_access_denied".
     *
     * @since   2.0.0
     * 
     * @return  $content html
     * 
     */
    public function access_denied() 
    {   
        $page = $this->findPage();
        
        if (isset($_GET['plan_id']) && $page) {
            $plan_id = $_GET['plan_id'];
            $plan = $this->findPlan($plan_id);

            if ($plan) {
                return $this->accessDeniedBox(
                    sprintf( __( 'Access denied. Only accessible for Petje.af members from %s with plan:', 'text_domain' ), $page->name ),
                    $plan->name,
                    PETJE_AF_BASE_URL . $page->slug . '/petjes',
                    __('Become a member!', 'petje-af'),
                    $this->loginButton(
                        __('Login with Petje.af', 'petje-af'),
                        __('Already an account?', 'petje-af')
                    )
                );                
            } else {
                return $this->accessDeniedBox(
                    __('Access denied. Only accessible for Petje.af page members from:', 'petje-af'),
                    $page->name,
                    PETJE_AF_BASE_URL . $page->slug,
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
                PETJE_AF_BASE_URL . $page->slug,
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

    /**
     * Shortcode used on the account page.
     * 
     * Shortcode callback for "petjeaf_account_page".
     *
     * @since   2.0.0
     * 
     * @return  $content html
     * 
     */
    public function account_page() 
    {
        $content = '<div class="petje-af-account">';

        if (is_user_logged_in()) {
            $petjeaf_user_id = get_user_meta(wp_get_current_user()->ID, $this->petje_user_id_key, true);

            if ($petjeaf_user_id) {
                $accessToken = petjeaf_cache('access_token');

                $decodedToken = Petje_Af_OAuth2_Provider::decodeToken($accessToken);

                $content .= '<div class="petje-af-account__profile">';

                $content .= '<h4>' . __('Your connected Petje.af-account', 'petje-af') . '</h4>';

                $content .= __('Name:', 'petje-af') . ' '. $decodedToken->name . '</br>';
                $content .= __('Email:', 'petje-af') . ' ' . $decodedToken->email . '</br>';

                $content .= '</div>';

                $content .= $this->disconnectButton(
                    __('Disconnect from Petje.af', 'petje-af'),
                    __("Detach your Petje.af account from this account.", 'petje-af'),
                    true,
                    'petje-af-account'
                );

                $content .= '<a class="petje-af-account__logout-link" href="/wp-login.php?action=logout">' . __('Logout', 'petje-af') .'</a>';

            } else {
                $content .= $this->loginButton(
                    __('Connect with Petje.af', 'petje-af'),
                    __("Attach your account with a Petje.af account.", 'petje-af'),
                    true,
                    'petje-af-account'
                );
            }
        } else {
            $content .= $this->loginButton(
                __('Login with Petje.af', 'petje-af'),
                __("Click on the button below to login with your Petje.af account.", 'petje-af'),
                true,
                'petje-af-account'
            );           
        }

        $content .= '</div>';

        return $content;
    }

    /**
     * Shortcode used for hiding parts of content.
     * 
     * Shortcode callback for "petjeaf_hide_content".
     *
     * @since   2.0.0
     * 
     * @return  $content html
     * 
     */
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
                    PETJE_AF_BASE_URL . $page->slug . '/petjes',
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
                    PETJE_AF_BASE_URL . $page->slug,
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

    /**
     * Find page
     * 
     * @return  $page  object or null if nothing found
     * 
     */
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

    /**
     * Find plan by plan id
     *
     * @return  $plan   object or null if nothing found
     * 
     */
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

    /**
     * Creating the login button
     * 
     * @since   2.0.0
     * @param   $button_text    string
     * @param   $prefix         string
     * @param   $paragraph      boolean
     * @param   $wrapper_class  string
     * 
     * @return  $content button
     * 
     */
    protected function loginButton($button_text = '', $prefix = '', $paragraph = true, $wrapper_class = 'petje-af-access-denied-box')
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $petjeaf_id = get_user_meta($user->ID, 'petjeaf_user_id');

            if ($petjeaf_id) return '';
        }

        $button = '';

        if ($paragraph) {
            $button .= '<p>';
        }

        if ($prefix) {
            $button .= '<span class="' . $wrapper_class .'__prefix">' . $prefix . '</span>';
        }

        $button .= '<button type="button" class="' . $wrapper_class . '__connect-button petjeaf-button petjeaf-button--icon petjeaf-connect-button ">' . $button_text . '</button>';

        if ($paragraph) {
            $button .= '</p>';
        }

        return $button;

    }

    /**
     * Creating the disconnect button
     * 
     * @since   2.0.0
     * @param   $button_text    string
     * @param   $prefix         string
     * @param   $paragraph      boolean
     * @param   $wrapper_class  string
     * 
     * @return  $content button
     * 
     */
    protected function disconnectButton($button_text, $prefix, $paragraph = true, $wrapper_class = 'petje-af-account')
    {
        $button = '';

        if ($paragraph) {
            $button .= '<p>';
        }

        if ($prefix) {
            $button .= '<span class="' . $wrapper_class . '__prefix">' . $prefix . '</span>';
        }

        $button .= '<button type="button" class="' . $wrapper_class . '__disconnect-button petjeaf-button petjeaf-button--info petjeaf-disconnect-button">' . $button_text . '</button>';

        if ($paragraph) {
            $button .= '</p>';
        }

        return $button;
    }

    /**
     * Access Denied Box HTML
     * 
     * @since   2.0.0
     * @param   $lead           string
     * @param   $title          string
     * @param   $link           string
     * @param   $button_text    string
     * @param   $login_button   string of html from the loginButton function
     * 
     * @return  $content
     * 
     */
    protected function accessDeniedBox($lead, $title, $link, $button_text, $login_button)
    {
        $content = '<div class="petje-af-access-denied-box">';

        $content .= $lead;

        if ($title) $content .= '<h4>' . $title . '</h4>';

        if ($link) $content .=  '<a href="' . $link . '" class="petjeaf-button petjeaf-button--cta petje-af-access-denied-box__button-link" target="_blank">' . $button_text . '</a>';

        $content .= $login_button;

        $content .= '</div>';

        return $content;
    }

    /**
     * Access Denied Box HTML
     * 
     * @since   2.0.0
     * @param   $error      string
     * 
     * @return  $content
     * 
     */
    protected function accessDeniedBoxError($error) {

        $content = '<div class="petje-af-access-denied-box">';

        $content .= '<div class="petje-af-access-denied-box__error">' . $error . '</div>';

        $content .= '</div>';

        return $content;
    }
}