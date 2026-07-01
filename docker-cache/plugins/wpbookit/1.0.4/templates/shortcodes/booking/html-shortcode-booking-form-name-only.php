<?php

defined('ABSPATH') || exit;

?>


<div class="col-12">
    <div class="form-group mb-4">
        <label for="#wpb-user-full-name" class="form-label"><?php esc_html_e("Full Name",'wpbookit')?>*</label>
        <div class="input-group">
            <input type="text" id="wpb-user-full-name" maxlength="50" required class="form-control" name="wpb_user_name" placeholder="Kelvin" aria-label="wpb-user-first-name" aria-describedby="basic-addon1">
            <span class="input-group-text" id="basic-addon1"><svg class="icon-16" width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.99088 9.45215C4.73477 9.45215 2.80811 9.79326 2.80811 11.1594C2.80811 12.5255 4.72255 12.8788 6.99088 12.8788C9.24699 12.8788 11.1731 12.5371 11.1731 11.1716C11.1731 9.80604 9.25922 9.45215 6.99088 9.45215Z" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.9907 7.50327C8.47125 7.50327 9.67125 6.30271 9.67125 4.82216C9.67125 3.3416 8.47125 2.1416 6.9907 2.1416C5.51014 2.1416 4.30959 3.3416 4.30959 4.82216C4.30459 6.29771 5.49681 7.49827 6.97181 7.50327H6.9907Z" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </div>
        <span id="booking_shortcode_full_name_error" class="error-message"></span>
    </div>
</div>