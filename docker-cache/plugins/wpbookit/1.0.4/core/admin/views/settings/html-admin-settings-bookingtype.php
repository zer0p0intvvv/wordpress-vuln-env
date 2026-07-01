<?php
/**
 * Booking Types 
 */ ?>

<div class="content-inner container-fluid pb-0" id="page_layout">
    <?php do_action('wpb_add_booking_type_form', [ 'avalible_duration' => $avalible_duration, 'all_weekdays' => $all_weekdays, 'meeting_tools' => $meeting_tools ]); ?>
    <div class="row">
        <div class="col-lg-12 mb-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
                <div class="d-flex flex-column">
                    <h4 class="mb-0"><?php esc_html_e('Booking Types', 'wpbookit'); ?></h4>
                </div>
                <div class="d-flex justify-content-between align-items-center rounded flex-wrap gap-3">
                    <a class="btn btn-secondary" id="add-booking-type-btn" data-bs-toggle="offcanvas"
                        data-bs-target="#add-booking-type" role="button" aria-controls="add-booking-type">
                        <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>"
                            alt="icon" />
                        <span class="align-middle"><?php echo esc_attr_x('New', 'Booking_type', 'wpbookit'); ?></span>
                    </a>
                </div>
            </div>

            <div class="row gy-5">
                <?php if (!empty($bookings_types)) {
                    foreach ($bookings_types as $booking) { ?>
                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 booking-type-card " data-id="<?php echo esc_html($booking->get_id())  ?>">
                            <div class="card mb-0 h-100 card-has-gredient">
                                <div class="card-body pb-0">
                                    <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                                        <h5 class="mb-0"><?php echo esc_html($booking->get_name()); ?></h5>
                                   
                                    </div>
                                    <div class="bg-body p-2 rounded mt-4">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span
                                                class="title-text"><?php echo esc_html( sprintf(__('Status', 'wpbookit'), $booking->get_status() )); ?></span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input wpb-booking-type-status" type="checkbox"
                                                    id="flexSwitchCheckChecked"
                                                    data-id="<?php echo esc_attr($booking->get_id()); ?>" <?php echo esc_attr($booking->get_status() == 1) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="title-text"><?php esc_html_e("Duration", 'wpbookit'); ?></span>
                                            <span><?php
                                           // translators: Minutes placeholder:0
                                           echo esc_html(sprintf(__('%d Minutes', 'wpbookit'), $booking->get_duration())); ?></span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="title-text"><?php esc_html_e("Price", 'wpbookit'); ?></span>
                                            <ul class="list-unstyled d-flex align-items-center m-0">
                                                <li>
                                                    <?php if ($booking->get_meta('price')): ?>
                                                        <span>
                                                            <?php
                                                            $price = $booking->get_meta('price');
                                                            echo esc_html(wpb_get_prefix_postfix_price($price));
                                                            ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span><?php esc_html_e("Free", 'wpbookit'); ?></span>
                                                    <?php endif; ?>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <span class="title-text"><?php esc_html_e("Short code", 'wpbookit'); ?></span>
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <span class="wpb-copy-text">
                                                    <?php echo esc_html("[wpb-booking id='" . $booking->get_id() . "']"); ?>
                                                </span>
                                                <span class="title-text text-center">
                                                    <a href="javascript: void(0);" class="text-body wpb-copy-button"
                                                        data-toggle="tooltip"
                                                        title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17"
                                                            viewBox="0 0 15 17" fill="none">
                                                            <path
                                                                d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z"
                                                                stroke="currentColor" stroke-width="1.66667"
                                                                stroke-linecap="round" />
                                                        </svg>
                                                    </a>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-auto my-3 overflow-content" style="height: 100px;">
                                        <p class="mb-0">
                                            <?php echo wp_kses_post($booking->get_description()); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="card-footer pt-0 contain-gredient">
                                    <div class="d-flex flex-wrap gap-3 wpb-booking-type-btn-group">
                                        <a class="btn btn-primary-subtle BookingTypeEditButton"
                                            id="edit-booking-type-btn"
                                            data-id="<?php echo esc_attr($booking->get_id()); ?>"><?php esc_html_e("Edit", 'wpbookit'); ?></a>
                                        <a class="btn btn-warning-subtle BookingTypeCloneButton"
                                            data-id="<?php echo esc_attr($booking->get_id()); ?>"><?php esc_html_e("Clone", 'wpbookit'); ?></a>
                                        <a class="btn btn-secondary-subtle BookingTypeDeleteButton"
                                            data-name="<?php echo esc_attr($booking->get_name()); ?>" data-id="<?php echo esc_attr($booking->get_id()); ?>"><?php esc_html_e("Delete", 'wpbookit'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>