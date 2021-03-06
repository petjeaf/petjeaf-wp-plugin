<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap wrap--petje-af">
    <h1>Petje.af instellingen</h1>
    <form method="post" action="options.php">
        <div class="right-column-settings-page metabox-holder">
            <div class="postbox">
                <h3 class="hndle"><span><?php _e('Client App', 'petje-af'); ?></span></h3>
                <div class="inside">
                    <?php settings_fields( 'petje_af_settings' ); ?>
                    <?php do_settings_sections( 'petje_af_settings' ); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Client id', 'petje-af'); ?></th>
                            <td>
                                <input type="text" class="regular-text" name="petje_af_client_id" value="<?php echo esc_attr( get_option('petje_af_client_id') ); ?>" />
                                <p class="description"><?php _e('The id from your client app on your Petje.af page', 'petje-af'); ?></p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php _e('Client secret', 'petje-af'); ?></th>
                            <td>
                                <input type="password" class="regular-text" name="petje_af_client_secret" value="<?php echo esc_attr( get_option('petje_af_client_secret') ); ?>" />
                                <p class="description"><?php _e('The secret key from your client app on your Petje.af page', 'petje-af'); ?></p>
                            </td>
                        </tr>

                        <?php if (get_option('petje_af_redirect_uri_page') ) : ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('Redirect Uri', 'petje-af'); ?></th>
                            <td>
                                <strong><?php echo get_permalink(get_option('petje_af_redirect_uri_page')); ?></strong>
                                <p class="description"><?php _e('Copy and paste this redirect uri in your Petje.af client app', 'petje-af'); ?></p>
                            </td>
                        </tr>
                        <?php endif; ?>

                    </table>
                </div>
            </div>
        </div>

        <?php if (get_option('petje_af_client_id')  && get_option('petje_af_client_secret') ) : ?>
        <div class="right-column-settings-page metabox-holder">
            <div class="postbox">
                <h3 class="hndle"><span><?php _e('Petje.af connection', 'petje-af'); ?></span></h3>
                <div class="inside">
                    <table class="form-table">
                        <?php if (get_option('petje_af_client_id') && get_option('petje_af_client_secret') && !get_option('petje_af_page_id') || !petjeaf_cache('pages', false) || get_option('petjeaf_connection_failed')) : ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('Connect with Petje.af', 'petje-af'); ?></th>
                            <td>
                                <button id="petjeaf_connect_button" class="petjeaf-connect-button petjeaf-button" type="button"><?php _e('Connect with Petje.af', 'petje-af'); ?></button>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php if(get_option('petje_af_page_id') && petjeaf_cache('pages', false)) : ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('Petje.af page', 'petje-af'); ?></th>
                            <td>
                                <?= Petje_Af_Admin::pages_dropdown(); ?>
                            </td>
                        </tr>
                        <?php endif; ?>

                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="right-column-settings-page metabox-holder">
            <div class="postbox">
                <h3 class="hndle"><span><?php _e('Access management', 'petje-af'); ?></span></h3>
                <div class="inside">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Admin users', 'petje-af'); ?></th>
                            <td>
                                <input type="checkbox" name="petje_af_ignore_access_settings_for_admin" value="1" <?php checked(1, get_option('petje_af_ignore_access_settings_for_admin'), true); ?> />
                                <label><?php _e('Ingore access settings for admin users', 'petje-af'); ?></label>
                            </td>
                        </tr>

                        <?php if(petjeaf_cache('page_plans', false)) : ?>
                        <tr valign="top">
                            <th scope="row"><?php _e('Complete site protection', 'petje-af'); ?></th>
                            <td>
                                <?php echo Petje_Af_Admin::page_plans_dropdown(null, 'petje_af_site_protection_plan'); ?>
                                <p></op><label><?php _e('Protect your complete site with Petje.af login', 'petje-af'); ?></label></p>
                            </td>
                        </tr>
                        <?php endif; ?>

                    </table>
                </div>
            </div>
        </div>
        <?php submit_button(); ?>
    </form>
</div>