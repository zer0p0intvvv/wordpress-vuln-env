<div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?> advance-filter" tabindex="-1" id="advance-filter" data-bs-scroll="false" data-bs-backdrop="true" aria-labelledby="advance-filter-label">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center">
            <h4 class="offcanvas-title" id="advance-filter-label"><?php esc_html_e('Advanced Filter', 'wpbookit'); ?></h4>
        </div>
        <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body data-scrollbar">
        <form id="booking_filters_form">
            <div>
                <div class="bg-body ranges rounded mb-4">
                    <div class="row p-0 m-0">
                        <div class=" col-lg-4 p-0">
                            <ul class="list-unstyled m-0 range-list">
                                <li id="wpb_today_btn">
                                    <a href="#">
                                        <?php esc_html_e('Today', 'wpbookit'); ?>
                                    </a>
                                </li>
                                <li id="wpb_get_last_30_days">
                                    <a href="#">
                                        <?php esc_html_e('Last 30 Days', 'wpbookit'); ?>
                                    </a>
                                </li>
                                <li class="active" id="wpb_this_month_btn">
                                    <a href="#">
                                        <?php esc_html_e('This Month', 'wpbookit'); ?>
                                    </a>
                                </li>
                                <li id="wpb_last_month_btn">
                                    <a href="#">
                                        <?php esc_html_e('Last Month', 'wpbookit'); ?>
                                    </a>
                                </li>
                                <li id="wpb_get_last_90_days">
                                    <a href="#">
                                        <?php esc_html_e('Last 90 Days', 'wpbookit'); ?>
                                    </a>
                                </li>
                                <li id="wpb_get_last_6_months">
                                    <a href="#">
                                        <?php esc_html_e('Last 6 Months', 'wpbookit'); ?>
                                    </a>
                                </li>
                                <li id="wpb_get_last_1_year">
                                    <a href="#">
                                        <?php esc_html_e('Last 1 Year', 'wpbookit'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="calender-list-box col-lg-8 p-0">
                            <div class="calender-header rounded-top-end bg-primary p-2">
                                <div class="d-flex justify-content-between">
                                    <div class="btn bg-white text-primary pe-none" id="wpb_filter_from_date">
                                        -
                                    </div>
                                    <div class="btn text-white pe-none"><?php esc_html_e('To', 'wpbookit'); ?></div>
                                    <div class="btn bg-white text-primary pe-none" id="wpb_filter_to_date">
                                        -
                                    </div>
                                </div>
                            </div>
                            <div class="calander-body">
                                <div class="form-group mb-lg-3 mb-5">
                                    <input type="hidden" name="wpb_booking_daterange" class="d-none inline_flatpickr" id="wpb-bookings-advanced-filter-flatpiker">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Booking Type*', 'wpbookit'); ?></label>
                    <select class="select2-basic-multiple js-states form-control" name="wpb_booking_type[]" style="width: 100%;" multiple>
                    <?php foreach ($booking_types as $bookingTypesStatus) : ?>
                            <option value="<?php echo esc_attr($bookingTypesStatus->get_id()); ?>"  >
                                <?php echo esc_html($bookingTypesStatus->get_name()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class=" fst-italic fw-medium"><?php esc_html_e('*You can select multiple booking types', 'wpbookit'); ?></small>
                </div>

                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Status*', 'wpbookit'); ?></label>
                    <select class="select2-basic-multiple js-states form-control" name="wpb_status[]" style="width: 100%;" multiple>
                        <?php foreach ($statuses as $statusKey => $status) : ?>
                            <option value="<?php echo esc_attr($statusKey); ?>">
                                <?php echo esc_html($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class=" fst-italic fw-medium"><?php esc_html_e('*You can select multiple booking statuses', 'wpbookit'); ?></small>
                </div>

            </div>
    </div>
    <div class="offcanvas-footer">
        <div class="row">
            <div class="col-6">
                <button id="wpb_apply_booking_reset" type="button" class="btn btn-secondary  w-100 mt-5">
                    <?php esc_html_e('Reset filters', 'wpbookit'); ?>
                </button>
            </div>
            <div class="col-6">
                <button id="wpb_apply_booking_filters" type="submit" class="btn btn-primary  w-100 mt-5">
                    <?php esc_html_e('Apply filters', 'wpbookit'); ?>
                </button>
            </div>
        </div>
    </div>
    </form>

</div>