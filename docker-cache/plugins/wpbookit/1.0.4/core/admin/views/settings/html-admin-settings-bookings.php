<?php

/**
 * Booking Types 
 */
?>

<div class="content-inner container-fluid pb-0 dashboard-page " id="page_layout">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="d-flex flex-column">
                <h4 class="mb-0"><?php esc_html_e('Bookings', 'wpbookit'); ?></h4>
            </div>
        </div>
        <div class="col-md-8 mt-md-0 mt-3">
            <div class="d-flex justify-content-md-end justify-content-start align-items-center rounded flex-wrap gap-3">

                <div class="input-group flex-nowrap w-auto">
                    <span class="input-group-text bg-white" id="addon-wrapping">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <circle cx="11.7669" cy="11.7669" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M18.0186 18.4854L21.5426 22.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="text" class="form-control dt-search bg-white" placeholder="<?php esc_html_e("Search...", 'wpbookit') ?>" aria-label="Search" aria-describedby="addon-wrapping">
                </div>
                <a class="btn bg-white lh-lg" data-bs-toggle="offcanvas" data-bs-target="#advance-filter" role="button" aria-controls="advance-filter">
                    <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/filter-icons.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="icon" />
                    <span class="align-middle"><?php esc_html_e('ADVANCED FILTER', 'wpbookit'); ?></span>
                </a>
                <a class="btn btn-secondary lh-lg" id="create-booking" data-bs-toggle="offcanvas" data-bs-target="#booking-form" role="button" aria-controls="new-booking">
                    <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg');  // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="icon" />
                    <span class="align-middle"><?php echo esc_html_x( 'New', 'Booking', 'wpbookit' ); ?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-wrapper rounded mb-4">
                <div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="table-responsive" id="wpb-bookings-table-container">
                        <?php require_once "bookings/bookings-table.php"; ?>
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
        <h5 class="modal-title mb-0 text-body"><?php esc_html_e("Booking details",'wpbookit')?></h5>
        <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-5">
        <h4 class="mb-5 wpb-booking-type" ></h4>
        <ul class="list-inline mb-0">
            <li class="mb-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="h6 mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none">
                            <path d="M1.0918 8.40421H18.9157" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.4429 12.3097H14.4522" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.0054 12.3097H10.0147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.55818 12.3097H5.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.4429 16.1962H14.4522" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.0054 16.1962H10.0147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.55818 16.1962H5.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.0433 1V4.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.96515 1V4.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.2383 2.57922H5.77096C2.83427 2.57922 1 4.21516 1 7.22225V16.2719C1 19.3263 2.83427 21 5.77096 21H14.229C17.175 21 19 19.3546 19 16.3475V7.22225C19.0092 4.21516 17.1842 2.57922 14.2383 2.57922Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="wpb-booking-date-time"></span>
                </div>
            </li>
            <li>
                <div class="d-flex align-items-center gap-3">
                    <span class="h6 mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                            <path d="M13.6853 12.8192C13.9996 12.924 14.3394 12.7541 14.4442 12.4397C14.549 12.1254 14.3791 11.7856 14.0647 11.6808L13.6853 12.8192ZM10.5 11.125H9.9C9.9 11.3833 10.0653 11.6125 10.3103 11.6942L10.5 11.125ZM11.1 6.42087C11.1 6.0895 10.8314 5.82087 10.5 5.82087C10.1686 5.82087 9.9 6.0895 9.9 6.42087H11.1ZM14.0647 11.6808L10.6897 10.5558L10.3103 11.6942L13.6853 12.8192L14.0647 11.6808ZM11.1 11.125V6.42087H9.9V11.125H11.1ZM18.9 10C18.9 14.6392 15.1392 18.4 10.5 18.4V19.6C15.8019 19.6 20.1 15.3019 20.1 10H18.9ZM10.5 18.4C5.86081 18.4 2.1 14.6392 2.1 10H0.9C0.9 15.3019 5.19807 19.6 10.5 19.6V18.4ZM2.1 10C2.1 5.36081 5.86081 1.6 10.5 1.6V0.4C5.19807 0.4 0.9 4.69807 0.9 10H2.1ZM10.5 1.6C15.1392 1.6 18.9 5.36081 18.9 10H20.1C20.1 4.69807 15.8019 0.4 10.5 0.4V1.6Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <span  class="wpb-booking-duration">

                    </span>
                </div>
            </li>
        </ul>
        <ul class="mt-4 list-inline mb-0 p-0">
            <li class="mb-3">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <h6 class="mb-0"><?php esc_html_e('Email:','wpbookit')?></h6>
                    <span>
                        <a  class="text-primary wpb-booking-user-email"></a>
                    </span>
                </div>
            </li>
            <li class="mb-3 wpb-booking-location-container">
                <div class="d-flex align-items-center gap-1 flex-wrap ">
                    <h6 class="mb-0 wpb-booking-location"></h6>
                    <span class="wpb-booking-location-source">
                    </span>
                </div>
            </li>
            <li class="mb-3">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <h6 class="mb-0"><?php esc_html_e('Created:','wpbookit')?></h6>
                    <span class="wpb-booking-created"></span>
                </div>
            </li>
            <?php do_action('wpb_after_bookingmodal_created'); ?>
          
        </ul>

        

        <ul class="mt-3 list-inline mb-0 p-0 wpb-booking-questions">
            <li class="mb-3">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <h6 class="mb-0 question"></h6>
                    <span class="ans"></span>
                </div>
            </li>
        </ul>

        <div class="wpb-booking-extra-fields">
            <?php do_action('wpb_booking_view_model_extra_fields'); ?>
        </div>
        

      </div>
    </div>
  </div>
</div>
<?php require_once IQWPB_PLUGIN_PATH. "core/admin/views/settings/bookings/booking-form.php"; ?>
<?php require_once IQWPB_PLUGIN_PATH. "core/admin/views/settings/bookings/booking-advanced-filter.php"; ?>
<?php require_once IQWPB_PLUGIN_PATH. "core/admin/views/settings/bookings/booking-import.php"; ?>

