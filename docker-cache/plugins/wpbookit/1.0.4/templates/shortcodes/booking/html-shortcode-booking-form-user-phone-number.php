<?php

defined('ABSPATH') || exit;



?>

<div class="col-12 mb-4">
    <label for="wpb_user_phone_number" class="form-label">
    <?php esc_html_e("Phone Number", 'wpbookit') ?>
    <?php echo esc_attr($args['shortcode_instance']->require_guest_phone_number == "true" ? "*" : '') ?>
    </label>
    <div class="input-group">
        <input type="tel" id="wpb_user_phone_number" name="wpb_user_phone_number" class="form-control" placeholder="+1 12345 67890" aria-label="wpb-user-phone-number" aria-describedby="basic-addon1"  <?php echo esc_attr($args['shortcode_instance']->require_guest_phone_number == "true" ? "required" : '') ?>>
        <span class="input-group-text" id="basic-addon1">
            <?php echo (wpb_render_filtered_svg('phone-icon'));  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          
        </span>
    </div>
    <span id="booking_shortcode_user_phone_number_error" class="error-message"></span>
</div>