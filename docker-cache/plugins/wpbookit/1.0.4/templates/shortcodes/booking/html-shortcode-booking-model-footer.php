<?php

defined('ABSPATH') || exit; ?>
<div class="col-lg-12">
    <?php if ((($shortcode_instance->booking_type->get_meta('price')) != 0 && count($shortcode_instance->payment) == 0)) : ?>
        <p class="text-danger"> <?php esc_html_e("No Payment Gateway Available", 'wpbookit') ?></p>
    <?php endif; ?>
    <div class="d-flex align-items-center justify-content-between position-relative">
        <button type="button" class="wpb-close-model-btn btn btn-outline-primary "><?php esc_html_e("Cancel", 'wpbookit') ?></button>
        <button type="button" class="wpb-prev-btn btn btn-outline-primary d-none "><?php esc_html_e("Prev", 'wpbookit') ?></button>
        <ul class="wpb-pagination <?php echo esc_attr( count($shortcode_instance->get_tabs())==1?" d-none":"") ?> "   >
            <?php  foreach ($shortcode_instance->get_tabs() as $tab) :?>
            <li id="<?php echo esc_html($tab['tab']) ?>_pagination" class="pagination-item"></li>
            <?php endforeach; ?>
        </ul>
        <?php if ((($shortcode_instance->booking_type->get_meta('price')) > 0 && count($shortcode_instance->payment) > 0) || ($shortcode_instance->booking_type->get_meta('price')) == 0) : ?>
            <button type="submit" class="wpb-submit-model-btn d-none btn btn-primary ms-3 fw-bold">
                <svg class="spinner d-none wpb-booking-submit-svg" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"></path>
                </svg>
                <?php esc_html_e("Book Now", 'wpbookit') ?>
            </button>
            <button class="wpb-next-btn btn btn-primary ms-3 fw-bold">
                <svg class="spinner d-none wpb-booking-submit-svg" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"></path>
                </svg>
                <?php esc_html_e("Next Step", 'wpbookit') ?>
            </button>
        <?php endif; ?>
    </div>

</div>