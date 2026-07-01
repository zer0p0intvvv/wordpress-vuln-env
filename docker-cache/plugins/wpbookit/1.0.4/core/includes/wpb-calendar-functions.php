<?php

/**
 * Get all Booking statuses.
 * @return array
 **/

function wpb_get_calendar_booking_statuses() {
    return apply_filters(
        'wpb_booking_statuses',
        array(
            'wpb-pending'   => _x( 'Pending', 'Booking status', 'wpbookit' ), // warning
            'wpb-approved'  => _x( 'Approved', 'Booking status', 'wpbookit' ), // info
            'wpb-cancelled' => _x( 'Cancelled', 'Booking status', 'wpbookit' ), // danger
            'wpb-completed' => _x( 'Completed', 'Booking status', 'wpbookit' ),
        )
    );
}

function wpb_get_calendar_booking_payment_statuses() {
    return apply_filters(
        'wpb_booking_payment_statuses',
        array(
            '1'   => _x( 'Paid', 'Payment status', 'wpbookit' ),
            '0'   => _x( 'Unpaid', 'Payment status', 'wpbookit' ),
        )
    );
}

function wpb_get_calendar_booking_modes() {
    $payment_mode=[];
    foreach (get_option('wpb_offline_payment_modes',[]) as $key => $value) {
        $payment_mode[str_replace(' ','-',strtolower($value['name']))]=$value['name'];
    }
    $payments = apply_filters('wpb_booking_shortcode_active_payment_gateway',$payment_mode);

    return $payments;
}

/**
 * See if a string is an booking status.
 *
 * @param  string $has_status Status, including any wpb-prefix.
 * @return bool
 */
function wpb_is_calendar_booking_status($has_status) {
    $booking_statuses = wpb_get_booking_statuses();
    return isset( $booking_statuses[$has_status] );
}

/**
 * Get a string is an booking status.
 *
 * @param  string $has_status Status, including any wpb-prefix.
 * @return bool
 */
function wpb_calendar_booking_status_label($statusKey) {
    $booking_statuses = wpb_get_booking_statuses();
    return isset( $booking_statuses[$statusKey] ) ? $booking_statuses[$statusKey] : false;
}

/**
 * Main function for returning booking, uses the WPB_Booking class.
 * @return bool|WPB_Booking
 */
function wpb_get_calendar_bookings($args = array()) {  
    // Default arguments
    $defaults = array(
        'user_id'       => 0,
        'paged'         => 1,
        'per_page'      => 10,
        'status'        => [],
        'booking_type'  => [],
        'date_from'     => '',
        'date_to'       => '',
        'order'         => 'DESC',
        'order_by'      => 'booking_date',
        'booking_name'  => '',
        'staff'         => 0,
        'offset'        => '',
    );
    $args = wp_parse_args($args, $defaults);
    return (new WPB_Booking)->get_bookings( $args );
}

/**
 * Main function for returning booking, uses the WPB_Booking class.
 * @return bool|WPB_Booking
 */
function wpb_get_calendar_booking($booking = false) {
    if (!$booking)
        return $booking;
    return (new WPB_Booking)->get_booking($booking);
}


/**
 * Generate booking pagination HTML with WordPress coding standards.
 *
 * @param int $total_pages Total number of pages.
 * @param int $paged Current page number.
 * @param int $range Range of pages to show before and after the current page.
 * @return string Pagination HTML.
 */
function wpb_get_calendar_booking_pagination( $total_pages, $paged, $range = 2 ) {
    ob_start(); ?>
    <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
        <ul class="pagination justify-content-md-end justify-content-center">

        <?php if ( 1 !== (int) $paged && 0 !== $total_pages ) : ?>
            <li class="paginate_button page-item previous" data-id="<?php echo esc_attr( $paged - 1 ); ?>" id="datatable_previous">
                <a aria-controls="datatable" aria-disabled="true" role="link" data-dt-idx="previous" tabindex="-1" class="page-link">
                    <span class="prev-icon">«</span>
                </a>
            </li>
        <?php endif; ?>

            <?php
                for ( $i = 1; $i <= $total_pages; $i++ ) :
                    if ( $i == 1 || $i == $total_pages || ( $i >= $paged - $range && $i <= $paged + $range ) ) : 
                        $active = $i == (int)$paged ? ' active': '';  ?>
                        <li data-id="<?php echo esc_attr( $i ); ?>" class="<?php echo esc_attr( 'paginate_button page-item' . $active ); ?>">
                            <a href="#" aria-controls="datatable" role="link" aria-current="page" data-dt-idx="<?php echo esc_html( $i - 1 ); ?>" tabindex="0" class="page-link"><?php echo esc_html($i); ?></a>
                        </li>
                    
                    <?php elseif ( $i == $paged - $range - 1 || $i == $paged + $range + 1 ) : ?>
                        <li class="paginate_button page-item disabled"><span class="page-link">&hellip;</span></li>

                    <?php endif;
                endfor;
            ?>

            <?php if ( (int) $paged !== (int) $total_pages && 0 !== $total_pages ) : ?>
                <li class="paginate_button page-item next" data-id="<?php echo esc_attr( $paged + 1 ); ?>" id="datatable_next">
                    <a aria-controls="datatable" aria-disabled="true" role="link" data-dt-idx="next" tabindex="-1" class="page-link">
                        <span class="next-icon">»</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php 
    return ob_get_clean();
}