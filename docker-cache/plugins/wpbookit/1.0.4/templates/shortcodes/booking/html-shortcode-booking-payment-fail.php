<?php

defined('ABSPATH') || exit; ?>
<div class="wpb-booking-shortcode">
    <div class="container  wpb-booking-payment-fail">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="wpb-vector mb-5">
                    <?php echo wpb_render_filtered_svg('payment-fail'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
                <div class="text-center" >
                    <h3 class="mb-3"><?php esc_html_e("Oops... Payment Unsuccessful !",'wpbookit') ?></h3>
                    <p class="col-md-10 mb-3 mx-auto"><?php esc_html_e("Please check your details and try again. If the issue persists, contact support for assistance.",'wpbookit') ?></p>
                    <a class="btn btn-primary" href="#"><?php esc_html_e("Try Again",'wpbookit') ?> </a>
                </div>

            </div>
        </div>
    </div>
</div>