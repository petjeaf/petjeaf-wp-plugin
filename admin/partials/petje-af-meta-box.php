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
</div>