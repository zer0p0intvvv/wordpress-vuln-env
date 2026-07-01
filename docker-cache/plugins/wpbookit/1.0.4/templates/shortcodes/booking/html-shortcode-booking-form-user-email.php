<?php

defined('ABSPATH') || exit;



?>

<div class="col-12 mb-4">
    <label for="wpb_user_email" class="form-label">
    <?php esc_html_e("Email address", 'wpbookit') ?>
    <?php echo esc_attr($args['shortcode_instance']->require_guest_email_address == "true" ? "*" : '') ;
    ?>
    </label>
    <div class="input-group">
        <input type="email" id="wpb_user_email" name="wpb_user_email" class="form-control" placeholder="e.g. kenny@demo.com" aria-label="wpb-user-first-name" aria-describedby="basic-addon1"  <?php echo esc_attr($args['shortcode_instance']->require_guest_email_address == "true" ? "required" : '') ?>>
        <span class="input-group-text" id="basic-addon1">
            <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.4432 5.66309L7.85128 7.77069C7.36158 8.15919 6.67259 8.15919 6.18288 7.77069L3.56909 5.66309" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round"></path>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.86359 12.75C11.6377 12.7549 12.8334 11.2972 12.8334 9.50571V5.49917C12.8334 3.70765 11.6377 2.25 9.86359 2.25H4.13658C2.36246 2.25 1.16675 3.70765 1.16675 5.49917V9.50571C1.16675 11.2972 2.36246 12.7549 4.13658 12.75H9.86359Z" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </span>
    </div>
    <span id="booking_shortcode_user_email_error" class="error-message"></span>
</div>