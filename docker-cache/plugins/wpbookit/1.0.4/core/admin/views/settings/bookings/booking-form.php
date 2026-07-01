<div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?> booking-form" tabindex="-1" id="booking-form" data-bs-scroll="true" data-bs-backdrop="true" aria-labelledby="booking-form-label">
    <form id="add-booking-form" class="add-booking-form">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center">
                <h4 class="offcanvas-title offcanvas-title-add d-none" id="booking-form-label"><?php esc_html_e('Create Booking', 'wpbookit'); ?></h4>
                <h4 class=" offcanvas-title offcanvas-title-edit d-none"><?php esc_html_e('Edit Booking', 'wpbookit'); ?></h4>
            </div>
            <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body data-scrollbar">
            <div class="form-group">
                <label class="form-label"><?php esc_html_e('Booking Type*', 'wpbookit'); ?></label>
                <select class="select2-basic-multiple js-states form-control" id="wpb_booking_type" name="wpb_booking_type" style="width: 100%;">
                    <option value=""><?php esc_html_e('Select Booking Type', 'wpbookit'); ?></option>
                    <?php
                    foreach ($booking_types as $bookingTypesStatus) : ?>
                        <option value="<?php echo esc_attr($bookingTypesStatus->get_id()); ?>" data-price="<?php echo esc_attr($bookingTypesStatus->get_meta('price') == '0' ? esc_html__("Free", 'wpbookit') : wpb_get_prefix_postfix_price($bookingTypesStatus->get_meta('price'))); ?>">
                            <?php echo esc_html($bookingTypesStatus->get_name()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="booking_type_error" class="error-message"></span>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="time" class="form-label"><?php esc_html_e('Date*', 'wpbookit'); ?></label>
                        <input type="date" name="date" id="wpb-datepicker" readonly style="width: 100%;" class="form-control date_flatpicker flatpickr-input active" placeholder="<?php esc_html_e("Select Date", 'wpbookit') ?>">
                        <span id="date_error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label"><?php esc_html_e('Select Time*', 'wpbookit'); ?></label>
                        <svg class="spinner iqwpbwm-notification-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                        </svg>
                        <select class="" id="wpb_booking_slot_time" name="wpb_booking_slot_time" style="width: 100%;">
                        </select>
                        <span id="time_errorz" class="error-message"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label"><?php esc_html_e('Customer*', 'wpbookit'); ?></label>
                <select class="select2-basic-multiple js-states form-control" id="wpb_customer" name="wpb_customer" style="width: 100%;">
                    <option value=""><?php esc_html_e('Select Customer', 'wpbookit'); ?></option>
                    <?php foreach ($all_customers as $customersKey => $customersval) : ?>
                        <option value="<?php echo esc_attr($customersval->ID); ?>">
                            <?php echo esc_html($customersval->display_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="customer_error" class="error-message"></span>
            </div>
            <?php do_action('wpb_new_booking_after_customer_fields'); ?>
            <div class="form-group">
                <label class="form-label"><?php esc_html_e('Status*', 'wpbookit'); ?></label>
                <select class="form-control" id="wpb_booking_status" name="wpb_booking_status" style="width: 100%;">
                    <?php foreach ($statuses as $statusKey => $statussval) : ?>
                        <option value="<?php echo esc_attr($statusKey); ?>">
                            <?php echo esc_html($statussval); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id="status_error" class="error-message"></span>
            </div>
            <div id="wpb-payment-section">
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Payment Mode*', 'wpbookit'); ?></label>
                    <select class="form-control" id="wpb_booking_payment_mode" name="wpb_booking_payment_mode" style="width: 100%;">
                        <?php foreach ($payment_modes as $paymentStatusKey => $paymentStatussval) : ?>
                            <option value="<?php echo esc_attr($paymentStatusKey); ?>">
                                <?php echo esc_html($paymentStatussval); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="payment_mode_error" class="error-message"></span>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php esc_html_e('Payment Status*', 'wpbookit'); ?></label>
                    <select class="form-control" id="wpb_booking_payment_status" name="wpb_booking_payment_status" style="width: 100%;">
                        <?php foreach ($payment_statuses as $paymentStatusKey => $paymentStatussval) : ?>
                            <option value="<?php echo esc_attr($paymentStatusKey); ?>">
                                <?php echo esc_html($paymentStatussval); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="payment_status_error" class="error-message"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="notesFormControlTextarea1"><?php esc_html_e('Notes', 'wpbookit'); ?></label>
                <textarea class="form-control" name="notes" id="notesFormControlTextarea1" rows="5" placeholder="<?php esc_html_e("Write Your Message Here", 'wpbookit') ?>"></textarea>
            </div>
            <div class="align-items-center p-3 bg-body mt-5 rounded d-none" id="booking_price"></div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex align-items-center">
                <input type="hidden" id="edit-booking-id" name="edit-booking-id">
                <button type="submit" class="btn btn-primary w-100" id="wpb-submit-booking">
                    <svg class="spinner d-none wpb-booking-submit-svg" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                    </svg>
                    <?php esc_html_e('Save', 'wpbookit'); ?>
                </button>
            </div>
        </div>
    </form>
</div>