<div class="tab-pane fade" id="booking-history" role="tabpanel" aria-labelledby="bookings-history-tab">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 tab-content-container">
            
            <div class="tab-content-inner">
                <?php

                $paged = (get_query_var('paged')) && isset($_REQUEST['history_booking']) ? get_query_var('paged') : 1;
                $per_page = 10; // You can set this to any number you want

                $data = array(
                    'status' => array('wpb-completed'),
                    'user_id' => $args['user_id'],
                    'paged' => $paged,
                    'per_page' => $per_page,
                );

             
                $bookings = wpb_get_bookings($data);
                $numResults = 0;
                if (!empty($bookings->results) && isset($bookings->results) && is_array($bookings->results)) {
                    $numResults = count($bookings->results);
                }
                if ($numResults === 0) {
                    return do_action('wpb_booking_no_history_hook' , $args);
                } ?>

                <h5><?php 
                // translators: Booking Label in singular or plural placeholder:0, Number of Booking placeholder:1
                esc_html(sprintf(esc_html__("%1\$d Completed %2\$s",'wpbookit'), $numResults, _n("Booking", "Bookings", $numResults, 'wpbookit'))) ?>
                </h5>
                <p class="mb-0">
                    <?php esc_html_e('Your all Completed bookings schedule is here. Check now!', 'wpbookit'); ?>
                </p>
            </div>
        </div>
        <div class="row">
            <?php
            if (!empty($bookings->results)):
                foreach ($bookings->results as $bookingID => $booking):
                    $booking_name = $booking->get_booking_name();
                    $booking_id = $booking->get_id();
                    $booking_type = $booking->get_booking_type();
                    $btype_name = isset($booking_type["name"]) ? $booking_type["name"] : ''; 
                    $booking_date = $booking->get_booking_date();
                    $booking_time = $booking->get_timeslot();
                    $booked_timestamp= strtotime($booking_date.' '.$booking_time);
                    ?>
                    <div class="col-lg-6" data-val="<?php echo esc_attr($booking_id); ?>">
                        <div class="card bg-body">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0"><?php echo esc_html($btype_name); ?></h6>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL . "/core/shortcodes/assets/images/calender.svg");//phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>"
                                                    alt="checked">
                                                <?php $booking_date = $booking->get_booking_date();
                                                $booking_time = $booking->get_timeslot();
                                                $fbooking_date = wpb_get_formated_date_time($booking_date, $booking_time);
                                                $booking_duration = $booking->get_meta('booking_duration'); ?>
                                                <span class="title-text"><?php esc_html_e('Booking:','wpbookit'); ?></span>
                                            </div>
                                            <span id="booking_date" data-val="<?php echo esc_html($booking_date); ?>">
                                                <?php echo esc_html($fbooking_date); ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL . "/core/shortcodes/assets/images/subtotal-icon.svg");//phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>"
                                                    alt="checked">
                                                <span class="title-text"><?php esc_html_e('Total:', 'wpbookit'); ?></span>
                                            </div>
                                            <?php $booking_type_id = $booking->get_booking_type_id();
                                            $price = $booking->get_booking_price(); ?>
                                            <span
                                                class="text-primary fw-bold"><?php echo esc_html( $price ); ?></span>
                                        </div>
                                    </div>
                                    <add-to-calendar-button
                                        name="<?php echo esc_html($btype_name); ?>"
                                        startDate="<?php echo esc_html(date('Y-m-d', strtotime($booking_date))); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>"
                                        startTime="<?php echo esc_html(date('H:i', strtotime($booking_time))); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>"
                                        endTime="<?php echo esc_html(date('H:i', strtotime('+' . $booking_duration . ' minutes', $booked_timestamp))); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>"
                                        options="'Apple','Google','iCal','Microsoft365','Outlook.com','Yahoo'" 
                                        timeZone="<?php echo esc_html('UTC'); ?>"
                                        trigger="hover"
                                        label="Add to Calendar"  
                                        buttonStyle="default" 
                                        lightMode="bodyScheme"
                                        listStyle="dropdown"
                                        hideBackground='true'
                                        iCalFileName="<?php echo esc_html($btype_name); ?>"
                                        debug>
                                    </add-to-calendar-button>
                                    <?php do_action('wpb_after_calendar_button', $booking); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
                <div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php
                            $total_pages = ceil($bookings->total / $per_page); // Assuming $bookings->total gives the total number of bookings
                            $current_page = max(1, isset($_REQUEST['history_booking'])  ? get_query_var('paged') :1);

                            if ($current_page > 1) {
                                echo wp_kses_post('<li class="page-item"><a class="page-link" href="' . wpb_clear_pagination_link( get_pagenum_link($current_page - 1),'history_booking') . ' ">Previous</a></li>');
                            }

                            for ($i = 1; $i <= $total_pages && $total_pages>1; $i++) {
                                $active_class = ($i == $current_page) ? ' active' : '';
                                echo wp_kses_post('<li class="page-item' . $active_class . '"><a class="page-link" href="' . wpb_clear_pagination_link(get_pagenum_link($i),'history_booking')  . ' ">' . $i . '</a></li>');
                            }

                            if ($current_page < $total_pages) {
                                echo wp_kses_post('<li class="page-item"><a class="page-link" href="' . wpb_clear_pagination_link(get_pagenum_link($current_page + 1),'history_booking') . ' ">Next</a></li>');
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>