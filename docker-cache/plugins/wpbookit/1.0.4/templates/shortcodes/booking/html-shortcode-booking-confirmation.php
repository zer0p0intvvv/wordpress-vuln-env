<?php

defined('ABSPATH') || exit; ?>
<div class="wpb-booking-shortcode">

    <div class="col-lg-12">
        <div class="text-center">
            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_824_4376)">
                    <path d="M97.1406 13.6709C95.6695 12.1973 93.2826 12.1948 91.8115 13.6647L46.598 58.7588L30.2851 41.0414C28.8755 39.5114 26.4923 39.4121 24.9597 40.8215C23.4283 42.2311 23.3302 44.6155 24.7398 46.1469L43.7097 66.7487C44.4044 67.5037 45.3767 67.9422 46.4019 67.9635C46.4294 67.9647 46.456 67.9647 46.4824 67.9647C47.4787 67.9647 48.4371 67.569 49.1431 66.8655L97.1331 19.0012C98.6079 17.5315 98.6105 15.1446 97.1406 13.6709Z" fill="#199226"></path>
                    <path d="M96.2311 46.2311C94.1494 46.2311 92.4623 47.9182 92.4623 50C92.4623 73.4146 73.4146 92.4623 50 92.4623C26.5867 92.4623 7.53769 73.4146 7.53769 50C7.53769 26.5867 26.5867 7.53769 50 7.53769C52.0816 7.53769 53.7689 5.85059 53.7689 3.76895C53.7689 1.68711 52.0816 0 50 0C22.4297 0 0 22.4297 0 50C0 77.5691 22.4297 100 50 100C77.5691 100 100 77.5691 100 50C100 47.9184 98.3129 46.2311 96.2311 46.2311Z" fill="#199226"></path>
                </g>
                <defs>
                    <clipPath id="clip0_824_4376">
                        <rect width="100" height="100" fill="white"></rect>
                    </clipPath>
                </defs>
            </svg>
            <h3 class="mt-4"><?php esc_html_e("Booking Confirmed!",'wpbookit') ?></h3>
            <h5 class="mb-5"><?php esc_html_e("Your Appointment is Booked Sucessfully!",'wpbookit') ?></h5>
        </div>
        <div class="card bg-white">
            <div class="card-body p-5">
                <div class="row booking-confirm-card-wrapper">
                    <div class="col-lg-6">
                        <div class="booking-confirm-card-cols-1 ">
                            <div class="d-flex align-items-center mb-4">
                                <h4 class="m-0"><?php echo esc_html($shortcode_instance->booking_type_name['name']); ?></h4>
                            </div>
                            <?php 
                           
                            if(!empty($shortcode_instance->booking_location) && !empty($shortcode_instance->booking_location_source??"")){?>
                            <div class="d-flex align-items-center mb-3 gap-2">
                                <span class="text-heading">
                                    <?php echo wpb_render_filtered_svg($shortcode_instance->booking_location); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </span>
                                <p class="mb-0 title-text">
                                    <?php echo wp_kses_post(wpb_print_booking_type_location($shortcode_instance->booking_location_source??"")) ;?>
                                </p>
                             
                            </div>
                            <?php } ?>
                            <div class="d-flex align-items-center mb-3 gap-2">
                                <span class="text-heading">
                                    <svg class="icon-20" width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3.95222 5.5848H11.9842M3.4041 1.03125V2.39748M12.4269 1.03125V2.39731M15.0832 4.89731L15.0832 14.4687C15.0832 15.8495 13.9639 16.9687 12.5832 16.9687H3.4165C2.03579 16.9687 0.916504 15.8495 0.916504 14.4688V4.89731C0.916504 3.5166 2.03579 2.39731 3.41651 2.39731H12.5832C13.9639 2.39731 15.0832 3.5166 15.0832 4.89731Z" stroke="currentColor" stroke-width="0.833333" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <p class="mb-0 title-text">
                                    <?php echo esc_html($shortcode_instance->booked_date_timestamp);?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center mb-3 gap-2">
                                <span class="text-heading">
                                    <svg class="icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.7169 14.7243C15.9789 14.8117 16.262 14.6701 16.3493 14.4081C16.4367 14.1461 16.2951 13.863 16.0331 13.7757L15.7169 14.7243ZM12.5 13.125H12C12 13.3402 12.1377 13.5313 12.3419 13.5993L12.5 13.125ZM13 8.42087C13 8.14473 12.7761 7.92087 12.5 7.92087C12.2239 7.92087 12 8.14473 12 8.42087H13ZM16.0331 13.7757L12.6581 12.6507L12.3419 13.5993L15.7169 14.7243L16.0331 13.7757ZM13 13.125V8.42087H12V13.125H13ZM21 12C21 16.6944 17.1944 20.5 12.5 20.5V21.5C17.7467 21.5 22 17.2467 22 12H21ZM12.5 20.5C7.80558 20.5 4 16.6944 4 12H3C3 17.2467 7.25329 21.5 12.5 21.5V20.5ZM4 12C4 7.30558 7.80558 3.5 12.5 3.5V2.5C7.25329 2.5 3 6.75329 3 12H4ZM12.5 3.5C17.1944 3.5 21 7.30558 21 12H22C22 6.75329 17.7467 2.5 12.5 2.5V3.5Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <p class="mb-0 title-text">
                                    <?php echo esc_html(sprintf("%d %s",$shortcode_instance->booking_type->get_duration() , _n("Minute","Minutes",$shortcode_instance->booking_type->get_duration(),'wpbookit') ))  ?>
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-heading">
                                    <svg class="icon-20" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.9999 9.6V13.8M17.9999 9.6V13.8M4.1999 18H19.7999C20.794 18 21.5999 17.1941 21.5999 16.2V7.8C21.5999 6.80589 20.794 6 19.7999 6H4.1999C3.20579 6 2.3999 6.80589 2.3999 7.8V16.2C2.3999 17.1941 3.20579 18 4.1999 18ZM14.3999 12C14.3999 13.3255 13.3254 14.4 11.9999 14.4C10.6744 14.4 9.5999 13.3255 9.5999 12C9.5999 10.6745 10.6744 9.6 11.9999 9.6C13.3254 9.6 14.3999 10.6745 14.3999 12Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </span>
                                <p class="mb-0 title-text">
                                <?php 
                                    $price = $shortcode_instance->booking_instance->get_dis_booking_price();
                                    echo esc_html($price > 0 && $price != __('Free', 'wpbookit') ? wpb_get_prefix_postfix_price($price) : __('Free', 'wpbookit')); 
                                ?>
                                </p>
                            </div>
                            <?php do_action('wpb_after_confirm_calculated_price',$shortcode_instance);?>
                        </div>
                    </div>
                    <div class="col-lg-6 booking-confirm-card mt-4 mt-lg-0">
                        <div class="booking-confirm-card-cols-2">
                            <h4 class="mt-0 mb-5"><?php esc_html_e("Add To Calendar:",'wpbookit'); ?></h4>
                            <div class="booking-text">  
                                <add-to-calendar-button
                                    name="<?php echo esc_html($shortcode_instance->booking_type_name['name']); ?>"
                                    description="<?php echo esc_html( wp_strip_all_tags($shortcode_instance->booking_type_description['description'])); ?>"
                                    startDate="<?php echo esc_html( date_format( date_create( $shortcode_instance->booked_date_timestamp_cal ), 'Y-m-d' ) ); ?>"
                                    startTime="<?php echo esc_html( $shortcode_instance->booked_start_time_cal ); ?>"
                                    endTime="<?php echo esc_html($shortcode_instance->booked_end_time_cal);?>"
                                    options="'Apple','Google','iCal','Microsoft365','Outlook.com','Yahoo'" 
                                    timeZone="<?php echo esc_html('UTC'); ?>"
                                    trigger="hover"
                                    label="Add to Calendar"  
                                    buttonStyle="date" 
                                    listStyle="dropdown-static"
                                    hideBackground='true'
                                    iCalFileName="<?php echo esc_html($shortcode_instance->booking_type_name['name']); ?>">
                                </add-to-calendar-button>
                            </div>
                            <div class="mt-5">
                                <button type="button" class="btn btn-outline-primary w-100 book_new_meeting"><?php esc_html_e("Book a new meeting",'wpbookit') ?></button>
                                <?php if ( $shortcode_instance->show_cancel_button) : ?>
                                    <button type="button" class="btn btn-outline-secondary w-100 cancel_meeting mt-3"><?php esc_html_e("Cancel", 'wpbookit') ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>