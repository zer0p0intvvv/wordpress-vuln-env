<div class="wpb-booking-shortcode">
    <?php do_action('wpb_booking_shortcode_before', $this); ?>
    <?php $specific_dates = $this->booking_type->get_meta('specific_dates') ?: '{}';

    $specific_dates = array_reduce(json_decode($specific_dates,true), function($acc, $item) {
        $acc[$item['date']] = [
            'from' => $item['from'],
            'to' => $item['to']
        ];
        return $acc;
    }, []);
    ?>

    <input type="hidden" class="wpb-booking-type-id" name="wpb-booking-type-id" value="<?php echo esc_html($this->booking_type->get_id()) ?>">
    <input type="hidden" class="wpb-booking-available-days" name="wpb-booking-available-days" value='<?php echo esc_html($this->booking_type->get_meta('weekly_time_slots')) ?>'>
    <input type="hidden" class="wpb-booking-sepcific-date-days" name="wpb-booking-sepcific-date-days" value='<?php echo esc_html(wp_json_encode($specific_dates));  ?>'>
    <input type="hidden" class="wpb-booking-max-post-booking-days" name="wpb-booking-max-post-booking-days" value='<?php echo esc_html($max_post_booking_days) ?>'>

    <?php
    if ($this->booking_type->get_status() == 1) {
    ?>
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-3">
                    <h3 class="mb-3"><?php echo esc_html($this->booking_type->get_name()) ?></h3>
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-2">
                            <small class="title-text">
                                <svg class="icon-20 me-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.0711 12.3493C13.333 12.4367 13.6162 12.2951 13.7035 12.0331C13.7908 11.7711 13.6493 11.488 13.3873 11.4007L13.0711 12.3493ZM10.4167 10.9375H9.91667C9.91667 11.1527 10.0544 11.3438 10.2586 11.4118L10.4167 10.9375ZM10.9167 7.01739C10.9167 6.74125 10.6928 6.51739 10.4167 6.51739C10.1405 6.51739 9.91667 6.74125 9.91667 7.01739H10.9167ZM13.3873 11.4007L10.5748 10.4632L10.2586 11.4118L13.0711 12.3493L13.3873 11.4007ZM10.9167 10.9375V7.01739H9.91667V10.9375H10.9167ZM17.4167 10C17.4167 13.866 14.2827 17 10.4167 17V18C14.8349 18 18.4167 14.4183 18.4167 10H17.4167ZM10.4167 17C6.55068 17 3.41667 13.866 3.41667 10H2.41667C2.41667 14.4183 5.99839 18 10.4167 18V17ZM3.41667 10C3.41667 6.13401 6.55068 3 10.4167 3V2C5.99839 2 2.41667 5.58172 2.41667 10H3.41667ZM10.4167 3C14.2827 3 17.4167 6.13401 17.4167 10H18.4167C18.4167 5.58172 14.8349 2 10.4167 2V3Z" fill="#0C112E" />
                                </svg>
                                <?php echo esc_html(sprintf("%d %s", $this->booking_type->get_duration(), _n("Minute", "Minutes", $this->booking_type->get_duration(), 'wpbookit'))) ?>
                            </small>
                        </div>
                        <div class="d-flex align-items-center  mb-2">
                            <small class="title-text">
                                <svg class="icon-20 me-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 8V11.5M15 8V11.5M3.5 15H16.5C17.3284 15 18 14.3284 18 13.5V6.5C18 5.67157 17.3284 5 16.5 5H3.5C2.67157 5 2 5.67157 2 6.5V13.5C2 14.3284 2.67157 15 3.5 15ZM12 10C12 11.1046 11.1046 12 10 12C8.89543 12 8 11.1046 8 10C8 8.89543 8.89543 8 10 8C11.1046 8 12 8.89543 12 10Z" stroke="#0C112E" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <?php

                                $price_display =  __('Free', 'wpbookit');
                                echo esc_html($price_display);
                                ?>
                            </small>
                        </div>
                    </div>
                    <div class="title-text"><?php echo wp_kses_post($this->booking_type->get_description()) ?></div>
                </div>
                <div class="col-lg-6">
                    <h5><?php esc_html_e("Booking", 'wpbookit') ?></h5>
                    <p class="mb-0"><?php esc_html_e("Pick a date and time for booking.", 'wpbookit') ?></p>
                    <div class="calander-body">
                        <div class="form-group mb-lg-3 mb-5">
                            <input type="hidden" name="inline" class="d-none wpb-inline-flatpickr">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex align-items-center justify-content-between pb-4 border-bottom">
                        <div class="d-flex align-items-center title-text gap-2">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.0313 10.5C17.3074 10.5 17.5313 10.2761 17.5313 10C17.5313 9.72386 17.3074 9.5 17.0313 9.5V10.5ZM17 10C17 13.866 13.866 17 10 17V18C14.4183 18 18 14.4183 18 10H17ZM10 17C6.13401 17 3 13.866 3 10H2C2 14.4183 5.58172 18 10 18V17ZM3 10C3 6.13401 6.13401 3 10 3V2C5.58172 2 2 5.58172 2 10H3ZM10 3C13.866 3 17 6.13401 17 10H18C18 5.58172 14.4183 2 10 2V3ZM10 17C9.71239 17 9.39897 16.8689 9.07032 16.5511C8.73926 16.2311 8.41829 15.7438 8.13788 15.1029C7.57773 13.8225 7.21875 12.0188 7.21875 10H6.21875C6.21875 12.1234 6.5943 14.0696 7.22173 15.5037C7.53512 16.22 7.92119 16.8311 8.37526 17.2701C8.83173 17.7114 9.38152 18 10 18V17ZM7.21875 10C7.21875 7.98121 7.57773 6.17746 8.13788 4.89711C8.41829 4.25619 8.73926 3.76892 9.07032 3.44886C9.39897 3.13113 9.71239 3 10 3V2C9.38152 2 8.83173 2.2886 8.37526 2.72991C7.92119 3.16889 7.53512 3.77997 7.22173 4.49629C6.5943 5.93041 6.21875 7.87665 6.21875 10H7.21875ZM10 18C10.6185 18 11.1683 17.7114 11.6247 17.2701C12.0788 16.8311 12.4649 16.22 12.7783 15.5037C13.4057 14.0696 13.7812 12.1234 13.7812 10H12.7812C12.7812 12.0188 12.4223 13.8225 11.8621 15.1029C11.5817 15.7438 11.2607 16.2311 10.9297 16.5511C10.601 16.8689 10.2876 17 10 17V18ZM13.7812 10C13.7812 7.87665 13.4057 5.93041 12.7783 4.49629C12.4649 3.77997 12.0788 3.16889 11.6247 2.72991C11.1683 2.2886 10.6185 2 10 2V3C10.2876 3 10.601 3.13113 10.9297 3.44886C11.2607 3.76892 11.5817 4.25619 11.8621 4.89711C12.4223 6.17746 12.7812 7.98121 12.7812 10H13.7812ZM2.5 10.5L17.0313 10.5V9.5L2.5 9.5L2.5 10.5Z" fill="#0C112E" />
                            </svg>
                            <?php echo esc_html($this->booking_timezome); ?>
                        </div>
                    </div>
                    <div class="booking-slots-time-wrapper overflow-auto text-center">
                        <ul class="list-unstyled mb-0 booking-slots-time gap-3"></ul>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-12">
                    <h3 class="mb-3 text-centers">
                        <?php esc_html_e('Booking Type not available, kindly contact Admin.', 'wpbookit'); ?>
                    </h3>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <?php do_action('wpb_booking_shortcode_after', ['shortcode_instance' => $this]); ?>
</div>