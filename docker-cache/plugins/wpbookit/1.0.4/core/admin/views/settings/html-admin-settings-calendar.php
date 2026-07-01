<?php
/**
 * Calendar 
 */
?>
<div class="content-inner container-fluid pb-0" id="page_layout">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
                <div class="d-flex flex-column">
                    <h4 class="mb-0"><?php esc_html_e('Calendar', 'wpbookit'); ?></h4>
                </div>
                <div class="d-flex justify-content-between align-items-center rounded flex-wrap gap-3">
                    <a class="btn btn-secondary lh-lg" id="create-booking" data-bs-toggle="offcanvas" data-bs-target="#booking-form" role="button" aria-controls="new-booking">
                        <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg');  // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="icon" />
                        <span class="align-middle"><?php echo esc_html_x('New', 'Booking', 'wpbookit'); ?></span>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <h6 class="m-0"><?php esc_html_e( "Status", 'wpbookit' ) ?></h6>
                                        <ul class="list-inline m-0 p-0 d-flex align-items-center gap-2">
                                            <?php foreach (wpb_get_booking_statuses() as $class => $label) :?>
                                                <li>
                                                <span class="d-flex align-items-center gap-1">
                                                    <span class="wpb-booking-status <?php echo esc_attr($class) ?>">
                                                        <svg class="align-baseline" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="6" cy="6" r="6" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                    <small><?php echo esc_html($label) ?></small>
                                                </span>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-3 justify-content-sm-end flex-wrap">
                                        <div class="dropdown">
                                            <div class="custom-dropdown-calender dropdown-toggle h-100" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                                <dic class="d-inline-flex align-items-center gap-2">
                                                    <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/menu-icons/booking-icon.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="all" class="">
                                                    <?php esc_html_e("All Booking Types", 'wpbookit'); ?>
                                                </dic>
                                            </div>
                                            
                                            <form class="dropdown-menu dropdown-menu-end p-3 w-100">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input all-booking-type-checkboxes" id="all_booking_type" value="all" checked onchange="window.DashbordSideBarModule.onChangeBookingType(this)">
                                                    <label class="form-check-label mb-2" for="all_booking_type">
                                                        <?php esc_html_e("All Booking Types", 'wpbookit'); ?>
                                                    </label>

                                                    <?php if (!empty($booking_types)) : ?>
                                                        <?php foreach ($booking_types as $booking_type) : ?>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input booking-type-checkboxes" id="<?php echo sprintf("%s%s", 'display_booking_type_', esc_html($booking_type->get_id())); ?>" value="<?php echo sprintf("%s", esc_attr( $booking_type->get_id() )); ?>" checked onchange="window.DashbordSideBarModule.onChangeBookingType(this)">
                                                            <label class="form-check-label" for="<?php echo sprintf("%s%s", 'display_booking_type_', esc_html( $booking_type->get_id() )); ?>">
                                                                <?php echo esc_html($booking_type->get_name()); ?>
                                                            </label>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input booking-type-checkboxes" id="<?php echo sprintf("%s%s", 'display_booking_type_', 0); ?>" value="<?php echo sprintf("%s", 0); ?>" checked onchange="window.DashbordSideBarModule.onChangeBookingType(this)">
                                                        <label class="form-check-label" for="<?php echo sprintf("%s%s", 'display_booking_type_', 0); ?>">
                                                            <?php esc_html_e("Custom", 'wpbookit'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="dropdown btn-group">
                                            <button class="custom-dropdown-calender refresh-btn btn-same-height h-100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e("Refresh",'wpbookit') ?>">
                                                <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . 'core/admin/assets/images/menu-icons/sync-dark.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="all" class="img-fluid">
                                            </button>
                                            <?php do_action('wpbookit_calendar_after_refresh_button'); ?>
                                        </div>
                                        <div class="dropdown">
                                            <button class="custom-dropdown-calender print-btn btn-same-height h-100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e("Print",'wpbookit') ?>">
                                                <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . 'core/admin/assets/images/printer.png'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="sync" class="img-fluid">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if( in_array('administrator', wp_get_current_user()->roles) ): ?>
                            <div class="d-flex align-items-center justify-content-start mb-5 staff-list-container gap-3">
                                <div class="left" onclick="slide('left', event)">
                                    <span type="button" class="text-body d-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="12" viewBox="0 0 7 12" fill="none">
                                            <path d="M6 11L1 6L6 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="right" onclick="slide('right', event)">
                                    <span type="button" class="text-body d-inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="12" viewBox="0 0 7 12" fill="none">
                                            <path d="M1 1L6 6L1 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>    
                                
                                <div class="staff-list custom-nav-slider">
                                    <ul class="list-inline m-0 p-0 d-flex align-items-center gap-3">
                                        <li>
                                            <div class="staff-item">
                                                <input type="radio" name="staff_id" id="all" class="staff-radio-input" value="0" checked>
                                                <label for="all" class="radio-label">
                                                    <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/avatar.png'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="all" class="staff-img">
                                                    All
                                                </label>
                                            </div>
                                        </li>
                                        <?php if (!empty($all_staffs)) : ?>
                                            <?php foreach ($all_staffs as $key => $value) : ?>
                                                <li>
                                                    <div class="staff-item">
                                                        <input type="radio" name="staff_id" id="<?php echo sprintf("%s%s", 'staff_', esc_html($value['user_nicename'])); ?>" class="staff-radio-input staff-radio" value="<?php echo sprintf("%s", esc_attr($value['id'])); ?>">
                                                        <label for="<?php echo sprintf("%s%s", 'staff_', esc_html($value['user_nicename'])); ?>" class="radio-label">
                                                            <img src="<?php echo esc_url($value['profile_img']); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="<?php echo esc_attr($value['name']); ?>" class="staff-img">
                                                            <span><?php echo esc_html($value['name']); ?></span>
                                                        </label>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>
                            <input type="hidden" name="current_user_id" id="current_user_id" value="<?php echo esc_attr( current_user_can('administrator') ? 0 : get_current_user_id() ); ?>">
                            <div id="event-calendar" class="calendar position-relative"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade booking-detail-modal" id="booking-detail-modal" tabindex="-1" aria-labelledby="booking-detail-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header p-5">
                <h5 class="modal-title mb-0 text-body"><?php esc_html_e("Booking details", 'wpbookit') ?></h5>
                <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <h4 class="mb-5 wpb-booking-type"></h4>
                <ul class="list-inline mb-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="h6 mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none">
                                    <path d="M1.0918 8.40421H18.9157" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14.4429 12.3097H14.4522" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.0054 12.3097H10.0147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.55818 12.3097H5.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14.4429 16.1962H14.4522" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.0054 16.1962H10.0147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.55818 16.1962H5.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14.0433 1V4.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.96515 1V4.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.2383 2.57922H5.77096C2.83427 2.57922 1 4.21516 1 7.22225V16.2719C1 19.3263 2.83427 21 5.77096 21H14.229C17.175 21 19 19.3546 19 16.3475V7.22225C19.0092 4.21516 17.1842 2.57922 14.2383 2.57922Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span class="wpb-booking-date-time"></span>
                        </div>
                    </li>
                    <li>
                        <div class="d-flex align-items-center gap-3">
                            <span class="h6 mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                    <path d="M13.6853 12.8192C13.9996 12.924 14.3394 12.7541 14.4442 12.4397C14.549 12.1254 14.3791 11.7856 14.0647 11.6808L13.6853 12.8192ZM10.5 11.125H9.9C9.9 11.3833 10.0653 11.6125 10.3103 11.6942L10.5 11.125ZM11.1 6.42087C11.1 6.0895 10.8314 5.82087 10.5 5.82087C10.1686 5.82087 9.9 6.0895 9.9 6.42087H11.1ZM14.0647 11.6808L10.6897 10.5558L10.3103 11.6942L13.6853 12.8192L14.0647 11.6808ZM11.1 11.125V6.42087H9.9V11.125H11.1ZM18.9 10C18.9 14.6392 15.1392 18.4 10.5 18.4V19.6C15.8019 19.6 20.1 15.3019 20.1 10H18.9ZM10.5 18.4C5.86081 18.4 2.1 14.6392 2.1 10H0.9C0.9 15.3019 5.19807 19.6 10.5 19.6V18.4ZM2.1 10C2.1 5.36081 5.86081 1.6 10.5 1.6V0.4C5.19807 0.4 0.9 4.69807 0.9 10H2.1ZM10.5 1.6C15.1392 1.6 18.9 5.36081 18.9 10H20.1C20.1 4.69807 15.8019 0.4 10.5 0.4V1.6Z" fill="currentColor" />
                                </svg>
                            </span>
                            <span class="wpb-booking-duration">

                            </span>
                        </div>
                    </li>
                </ul>
                <ul class="mt-4 list-inline mb-0 p-0">
                    <li class="mb-3">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <h6 class="mb-0"><?php esc_html_e('Email:', 'wpbookit') ?></h6>
                            <span>
                                <a class="text-primary wpb-booking-user-email"></a>
                            </span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-center gap-1 flex-wrap wpb-booking-meeting">
                            <h6 class="mb-0"><?php esc_html_e('Meeting URL:', 'wpbookit') ?></h6>
                            <span>
                                <a href="https://iqonic.design/" class="text-primary wpb-booking-meeting-link" target="_blank"></a>
                            </span>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <h6 class="mb-0"><?php esc_html_e('Created:', 'wpbookit') ?></h6>
                            <span class="wpb-booking-created"></span>
                        </div>
                    </li>

                </ul>



                <ul class="mt-3 list-inline mb-0 p-0 wpb-booking-questions">
                    <li class="mb-3">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <h6 class="mb-0 question"></h6>
                            <span class="ans"></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php require_once IQWPB_PLUGIN_PATH . "core/admin/views/settings/bookings/booking-form.php"; ?>