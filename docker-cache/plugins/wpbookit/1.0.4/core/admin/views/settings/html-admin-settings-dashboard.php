<div class="content-inner container-fluid pb-0" id="page_layout">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
                <div class="d-flex flex-column">
                    <h4 class="mb-0"><?php esc_html_e('Dashboard','wpbookit'); ?></h4>
                </div>
                <div class="d-flex justify-content-between align-items-center rounded flex-wrap gap-3">
                    <?php  do_action('wpb_before_wpb_range_flatpicker_dashboard');?>
                    <div class="form-group mb-0">
                        <?php  
                        $placeholder = htmlspecialchars(date('Y-m-d', strtotime('-7 days'))) . " to " . date('Y-m-d'); //phpcs:ignore Generic.PHP.ForbiddenFunctions.Found ,  WordPress.DateTime.RestrictedFunctions.date_date  ?> 
                        <input type="text" name="start" id="wpb-range-flatpicker" class="form-control date_custom_input range_flatpicker flatpickr-input active bg-white" placeholder="<?php esc_attr_e("Select Date Range",'wpbookit'); ?>" readonly="readonly">
                    </div>
                    <button type="button" id="flatpickr-submit" class="btn btn-primary"><?php esc_html_e('Submit','wpbookit'); ?></button>
                    <button type="button" id="flatpickr-reset" class="btn btn-danger"><?php esc_html_e('Reset','wpbookit'); ?></button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="card card-block card-stretch card-height">
                        <a id="wpb-bookings-link" href="<?php echo esc_url( admin_url( 'admin.php?page=wpbookit-dashboard&tab=bookings' ) ); ?>">
                            <div class="card-body">
                                <small><?php esc_html_e('Total Bookings','wpbookit'); ?></small>
                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <h2 class="counter mb-0" id="wpb-total-booking"><?php echo esc_html( $total_booking );?></h2>
                                    <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL.'/core/admin/assets/images/checked.svg' ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php if( current_user_can('administrator') ) { ?>
                <div class="col">
                    <div class="card card-block card-stretch card-height">
                        <a id="wpb_customer_link" href="<?php echo esc_url( admin_url( 'admin.php?page=wpbookit-dashboard&tab=customer' ) ); ?>">
                            <div class="card-body">
                                <small><?php esc_html_e('Total Customer','wpbookit'); ?></small>
                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <h2 class="counter mb-0" id="wpb-total-customer"><?php echo esc_html($customerCount); ?></h2>
                                    <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL.'/core/admin/assets/images/person.svg' ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php } ?>
                <?php do_action( 'wpb_dashboard_card_blocks',$this->date_from, $this->date_to); ?>
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-header">
                            <div class=" d-flex justify-content-between  flex-wrap">
                                <h4 class="card-title"><?php esc_html_e('Booking','wpbookit'); ?></h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="dashboard-line-chart" data-chart-option='<?php echo wp_json_encode($dashboard_chart_option)?>' >
                            <?php ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <div class=" d-flex justify-content-between  flex-wrap">
                        <h4 class="card-title"><?php esc_html_e('All Bookings','wpbookit'); ?></h4>
                        <!-- Setting offcanvas start here -->
                        <div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?> add-booking" tabindex="-1" id="add-booking" data-bs-scroll="true"
                            data-bs-backdrop="true" aria-labelledby="add-booking-label">
                            <div class="offcanvas-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="offcanvas-title" id="add-booking-label"><?php esc_html_e('Add Bookings','wpbookit'); ?></h4>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                        
                                </div>
                                <button type="button" class="btn-close px-0 text-reset shadow-none" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body data-scrollbar">
                                <div class="row">
                                    
                                </div>
                            </div>
                        </div>
                        <!-- Settings sidebar end here -->
                        <a class="btn btn-secondary btn-sm"  href="<?php echo esc_attr(admin_url('admin.php?page=wpbookit-dashboard&tab=' . esc_attr('bookings').'&offcanvas=true')); ?>" role="button" aria-controls="add-booking">
                        <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="icon" />
                        <span class="align-middle"><?php echo esc_html_x( 'New', 'Booking', 'wpbookit' ); ?></span>
                    </a>
                    </div>
                </div>
                <div class="card-body">
                    <nav>
                        <div class="mb-3 nav nav-tabs nav-iconly gap-3" id="nav-tab3" role="tablist">
                            <button class="nav-link active" id="pro-nav-home-tab" data-bs-toggle="tab"
                                data-bs-target="#pro-nav-home" type="button" role="tab"
                                aria-controls="pro-nav-home" aria-selected="true">
                                <?php esc_html_e('Upcoming','wpbookit'); ?>
                            </button>
                            <button class="nav-link" id="pro-nav-profile-tab" data-bs-toggle="tab"
                                data-bs-target="#pro-nav-profile" type="button" role="tab"
                                aria-controls="pro-nav-profile" aria-selected="false">
                                <?php esc_html_e('Pending','wpbookit'); ?>
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content iq-tab-fade-up all-booking-tab-content" id="nav-tab-content">
                        <div class="tab-pane fade show active" id="pro-nav-home" role="tabpanel" aria-labelledby="pro-nav-home-tab">
                            <ul class="list-inline m-0 p-0">
                            <?php if( !empty( $upcoming_data->results ) ) : 
                                    foreach ($upcoming_data->results as $u_booking): ?>
                                        <li class="mb-4 p-4 bg-body rounded">
                                            <div class="row gx-4 align-items-center">
                                                <div class="col-sm-5 col-6">
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        <span class="text-secondary small">
                                                            <?php echo esc_html(wpb_get_formated_date($u_booking->get_booking_date())); ?>
                                                        </span>
                                                        <span class="text-secondary small">
                                                            <?php echo esc_html(wpb_get_formated_time($u_booking->get_timeslot('view', false))); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-7 col-6">
                                                    <div>
                                                        <h6><?php echo esc_html($u_booking->get_booking_name()); ?></h6>
                                                        <div class="d-flex align-items-center">
                                                            <small><?php esc_html_e('Booking for ','wpbookit'); ?> <span class="fw-bold text-title"><?php 
                                                            echo esc_html($u_booking->get_data()['booking_type']); ?></span></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; 
                                else:
                                    ?>
                                    <p class="text-center"><?php esc_html_e('No Booking Available','wpbookit') ?></p>
                                    <?php endif; ?>
                            </ul><br>
                            <?php
                            if( !empty( $upcoming_data->results ) && $upcoming_data->total > 4 ) { ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=wpbookit-dashboard&tab=bookings')); ?>" class="text-secondary d-block text-center tab_btn-view_all"><?php esc_html_e('View All','wpbookit'); ?></a>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pro-nav-profile" role="tabpanel"
                            aria-labelledby="pro-nav-profile-tab">
                            <ul class="list-inline m-0 p-0">
                                <?php if( !empty( $pending_data->results ) ) : 
                                    foreach ($pending_data->results as $p_booking): ?>
                                        <li class="mb-4 p-4 bg-body rounded">
                                            <div class="row gx-4 align-items-center">
                                                <div class="col-sm-5 col-6">
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        <span class="text-secondary small">
                                                            <?php echo esc_html(wpb_get_formated_date($p_booking->get_booking_date())); ?>
                                                        </span>
                                                        <span class="text-secondary small">
                                                            <?php echo esc_html(wpb_get_formated_time($p_booking->get_timeslot('view', false))); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-7 col-6">
                                                    <div>
                                                        <h6><?php echo esc_html($p_booking->get_booking_name()); ?></h6>
                                                        <div class="d-flex align-items-center">
                                                            <small><?php esc_html_e('Booking for ','wpbookit'); ?><span class="fw-bold text-title"><?php 
                                                            $booking_type = $p_booking->get_booking_type();
                                                            echo esc_html($p_booking->get_data()['booking_type']);?></span></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach;
                                else:
                                    ?>
                                    <p class="text-center"><?php esc_html_e('No Booking Available','wpbookit') ?></p>
                                    <?php
                                endif; ?>
                            </ul><br>
                            <?php
                            if( !empty( $pending_data->results  ) && $pending_data->total > 4) { ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=wpbookit-dashboard&tab=bookings')); ?>" class="text-secondary d-block text-center tab_btn-view_all"><?php esc_html_e('View All','wpbookit'); ?></a>
                            <?php } ?>
                        </div>
                        <div class="tab-pane fade" id="pro-nav-contact" role="tabpanel"
                            aria-labelledby="pro-nav-contact-tab">
                            <ul class="list-inline m-0 p-0">
                                <?php if( !empty( $completed_data->results ) ) :
                                    foreach ( $completed_data->results as $c_booking ) : ?>
                                        <li class="mb-4 p-4 bg-body rounded">
                                            <div class="row gx-4 align-items-center">
                                                <div class="col-sm-5 col-6">
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        <span class="text-secondary small">
                                                            <?php echo esc_html(wpb_get_formated_date($c_booking->get_booking_date())); ?>
                                                        </span>
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; 
                                else:
                                        ?>
                                        <p class="text-center"><?php esc_html_e('No Booking Available','wpbookit') ?></p>
                                        <?php
                                endif; ?>
                            </ul><br>
                            <?php
                            if( !empty( $completed_data->results ) ) { ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=wpbookit-dashboard&tab=bookings')); ?>" class="text-secondary d-block text-center tab_btn-view_all"><?php esc_html_e('View All','wpbookit'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>