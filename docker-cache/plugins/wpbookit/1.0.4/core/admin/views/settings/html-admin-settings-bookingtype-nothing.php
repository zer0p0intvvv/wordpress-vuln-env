<?php

/**
 * Booking Types 
 */
?>

<div class="content-inner container-fluid pb-0 d-flex flex-column justify-content-center" id="page_layout">
    <div class="text-center my-5">
        <div class="mb-5">
            <?php include IQWPB_PLUGIN_PATH . 'core/admin/assets/images/no-booking-vector.svg'; ?>
        </div>
        <h4><?php esc_html_e("Ohhh no.. There are no booking types available right now.", 'wpbookit') ?></h4>
        <p class="col-md-6 mx-auto">
            <?php esc_html_e("There are no booking types available you can add booking type and start a demo call and solve your customer problem easily.", 'wpbookit') ?>
        </p>
        <button class="btn btn-secondary" id="add-booking-type-btn" data-bs-toggle="offcanvas" data-bs-target="#add-booking-type" role="button"
            aria-controls="add-booking-type">
            <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>"
                alt="icon" />
            <span class="align-middle"><?php echo esc_attr_x('New', 'Booking_type', 'wpbookit'); ?></span>
        </button>
    </div>
    <?php

    do_action('wpb_add_booking_type_form', [ 'avalible_duration' => $avalible_duration,  'all_weekdays' => $all_weekdays, 'meeting_tools' => $meeting_tools ]); ?>
</div>