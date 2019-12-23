<?php

global $post;

/**
 * The sidebar meta box HTML
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/admin/partials
 */

wp_nonce_field( 'petje_af_meta_box_nonce', 'petje_af_meta_box_nonce' );

?>
<div class="components-base-control">
    <div class="components-base-control__field">
        <label class="components-base-control__label" for="petje_af_plan_select"><?php _e('Visible for:', 'petje-af'); ?></label>
        <?php echo Petje_Af_Admin::page_plans_dropdown($post->ID); ?>
    </div>

    <?php add_thickbox(); ?>

    <p class="components-base-control__label"><?php _e('Hiding partial content?', 'petje-af'); ?></p>
    <p><a href="#TB_inline?width=600&height=550&inlineId=petjeaf-shortcodes-modal" class="thickbox"><?php _e('Copy & Paste this shortcodes', 'petje-af'); ?></a></p>

    <div id="petjeaf-shortcodes-modal" style="display:none;">
        <h4><?php _e('How to hide partial content?', 'petje-af'); ?></h4>
        <p><?php _e('Copy and paste the shortcodes below that correspond to the plan you would like to use and put your content in between the opening and the closing.', 'petje-af'); ?></p>
        <?php echo Petje_Af_Admin::page_plans_shortcode_examples(); ?>
    </div>
</div>