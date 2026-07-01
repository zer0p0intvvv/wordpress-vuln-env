<?php

defined('ABSPATH') || exit;

?>
<!-- Modal -->
<div class="modal fade  confirm-booking" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="confirm-booking" aria-hidden="true">
    <form class="wpb-shortcode-booking-form">

        <?php do_action('wpb_booking_shortcode_before_model');        ?>
        <div class="modal-dialog  modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 p-4">
                    <h5 class="modal-title "><?php esc_html_e('Enter your basic information', 'wpbookit') ?></h5>
                    <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 position-relative">
                    <div class="accordion border-0 " id="wpb_booking_detail">
                        <div class="accordion-item bg-transparent    border-0">
                            <h6 class="accordion-header  wpb-booking-model-title m-0">
                                <a class="accordion-button collapsed bg-transparent p-4 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#wpb_booking_detail_collapse" aria-expanded="false" aria-controls="wpb_booking_detail_collapse">
                                    <?php
                                    if (!empty($shortcode_instance->booking_type->get_meta('staff'))) {
                                        // Translators: %s is the display name of the staff member 
                                        echo esc_html__(sprintf(!empty( $shortcode_instance->booking_type->get_meta('demo_with_whom_label'))? $shortcode_instance->booking_type->get_meta('demo_with_whom_label'): __("Demo Call with %s", 'wpbookit'), get_the_author_meta('display_name', $shortcode_instance->booking_type->get_meta('staff'))),'wpbookit'); // phpcs:ignore  WordPress.WP.I18n.NonSingularStringLiteralText 
                                    }
                                    ?>
                                </a>
                            </h6>

                            <div id="wpb_booking_detail_collapse" class="accordion-collapse collapse " data-bs-parent="#wpb_booking_detail" >
                                <div class="accordion-body p-0">
                                    <div class="d-flex align-items-center py-2 mb-3 gap-2 border-bottom">
                                        <svg class="icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.14286 7.90176H16.7812M6.48512 2.4375V4.07698M17.3125 2.4375V4.07678M20.5 7.07678L20.5 18.5625C20.5 20.2194 19.1569 21.5625 17.5 21.5625H6.5C4.84315 21.5625 3.5 20.2194 3.5 18.5625V7.07678C3.5 5.41992 4.84315 4.07678 6.5 4.07678H17.5C19.1569 4.07678 20.5 5.41992 20.5 7.07678Z" stroke="#0C112E" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="title-text wpb-selected-timestap"></span>
                                    </div>
                                    <div class="d-flex align-items-center mb-3 gap-2">
                                        <svg class="icon-20" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.625 14.9609L12.8846 11.7308H11.1538L10 10.5769L12.3077 7.11538H16.3462M5.96154 4.23077L7.1853 5.45453C7.84288 6.11211 8.0453 7.09829 7.69992 7.96174V7.96174C7.34688 8.84434 6.49206 9.42308 5.54147 9.42308H3.07692M17.5 10C17.5 14.1421 14.1421 17.5 10 17.5C5.85786 17.5 2.5 14.1421 2.5 10C2.5 5.85786 5.85786 2.5 10 2.5C14.1421 2.5 17.5 5.85786 17.5 10Z" stroke="#0C112E" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="title-text "><?php echo esc_html($shortcode_instance->booking_timezome);  ?></span>
                                        <div class="border-end">&nbsp;</div>
                                        <svg class="icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.7169 14.7243C15.9789 14.8117 16.262 14.6701 16.3493 14.4081C16.4367 14.1461 16.2951 13.863 16.0331 13.7757L15.7169 14.7243ZM12.5 13.125H12C12 13.3402 12.1377 13.5313 12.3419 13.5993L12.5 13.125ZM13 8.42087C13 8.14473 12.7761 7.92087 12.5 7.92087C12.2239 7.92087 12 8.14473 12 8.42087H13ZM16.0331 13.7757L12.6581 12.6507L12.3419 13.5993L15.7169 14.7243L16.0331 13.7757ZM13 13.125V8.42087H12V13.125H13ZM21 12C21 16.6944 17.1944 20.5 12.5 20.5V21.5C17.7467 21.5 22 17.2467 22 12H21ZM12.5 20.5C7.80558 20.5 4 16.6944 4 12H3C3 17.2467 7.25329 21.5 12.5 21.5V20.5ZM4 12C4 7.30558 7.80558 3.5 12.5 3.5V2.5C7.25329 2.5 3 6.75329 3 12H4ZM12.5 3.5C17.1944 3.5 21 7.30558 21 12H22C22 6.75329 17.7467 2.5 12.5 2.5V3.5Z" fill="#0C112E" />
                                        </svg>
                                        <p class="mb-0 title-text"> <?php echo esc_html(sprintf("%d %s", $shortcode_instance->booking_type->get_duration(), _n("Minute", "Minutes", $shortcode_instance->booking_type->get_duration(), 'wpbookit')))  ?></p>

                                    </div>
                                  
                                    <div class="d-flex align-items-center mb-3 gap-2">
                                        <svg class="icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.9999 9.6V13.8M17.9999 9.6V13.8M4.1999 18H19.7999C20.794 18 21.5999 17.1941 21.5999 16.2V7.8C21.5999 6.80589 20.794 6 19.7999 6H4.1999C3.20579 6 2.3999 6.80589 2.3999 7.8V16.2C2.3999 17.1941 3.20579 18 4.1999 18ZM14.3999 12C14.3999 13.3255 13.3254 14.4 11.9999 14.4C10.6744 14.4 9.5999 13.3255 9.5999 12C9.5999 10.6745 10.6744 9.6 11.9999 9.6C13.3254 9.6 14.3999 10.6745 14.3999 12Z" stroke="#0C112E" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mb-0 title-text">
                                            <?php
                                            $price_display = __('Free', 'wpbookit');
                                            echo esc_html($price_display);
                                            ?>
                                        </p>
                                        <?php if($shortcode_instance->is_group_booking): ?>
                                            <div class="border-end">&nbsp;</div>
                                            <span class="text-black">
                                                <?php echo wpb_render_filtered_svg('users-profiles-plus'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            </span>
                                            <p class="mb-0 title-text">
                                                <?php
                                                /* translators: %s represents the number of seats. */
                                                echo esc_html(sprintf( _n("%s Seat","%s Seats",'wpbookit'),$shortcode_instance->total_seat,$shortcode_instance->total_seat)) ;
                                                ?>
                                            </p>
                                            <span class="badge bg-success wpb-available-seat-count"></span>
                                        <?php endif; ?>

                                    </div>
                                    <?php
                                    if (!empty(trim($shortcode_instance->booking_type->get_description()))) :
                                    ?>
                                        <div class="d-flex gap-2">
                                            <svg class="icon-20 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.56522 18L9.73913 13.8261H16.3333C17.2538 13.8261 18 13.0799 18 12.1594V3.66667C18 2.74619 17.2538 2 16.3333 2H3.66667C2.74619 2 2 2.74619 2 3.66667V12.1594C2 13.0799 2.74619 13.8261 3.66667 13.8261H5.56522V18Z" stroke="#0C112E" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="booking-modal-description"><?php echo wp_kses_post($shortcode_instance->booking_type->get_description())  ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="row">
                        <div class="col-12 wpb-tabs">
                                <?php do_action('wpb_booking_shortcode_tabs_hook',$shortcode_instance); ?>
                        </div>
                    </div>
                  

                </div>
                <div class="modal-footer p-4">
                    <?php do_action('wpb_booking_shortcode_model_pagination',$shortcode_instance) ?>
                </div>

            </div>
        </div>
        <?php do_action('wpb_booking_shortcode_before_model'); ?>
    </form>

</div>