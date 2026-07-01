<?php

/**
 * Get all Booking statuses.
 * @return array
 **/

function wpb_get_booking_statuses() {
    return apply_filters(
        'wpb_booking_statuses',
        array(
            'wpb-pending'   => _x( 'Pending', 'Booking status', 'wpbookit' ), // warning
            'wpb-approved'  => _x( 'Approved', 'Booking status', 'wpbookit' ), // info
            'wpb-cancelled' => _x( 'Cancelled', 'Booking status', 'wpbookit' ), // danger
            'wpb-completed' => _x( 'Completed', 'Booking status', 'wpbookit' ), // primary
        )
    );
}

function wpb_get_booking_payment_statuses() {
    return apply_filters(
        'wpb_booking_payment_statuses',
        array(
            '1'   => _x( 'Paid', 'Payment status', 'wpbookit' ),
            '0'   => _x( 'Unpaid', 'Payment status', 'wpbookit' ),
        )
    );
}

function wpb_get_booking_modes() {
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
function wpb_is_booking_status($has_status) {
    $booking_statuses = wpb_get_booking_statuses();
    return isset( $booking_statuses[$has_status] );
}

/**
 * Get a string is an booking status.
 *
 * @param  string $has_status Status, including any wpb-prefix.
 * @return bool
 */
function wpb_booking_status_label($statusKey) {
    $booking_statuses = wpb_get_booking_statuses();
    return isset( $booking_statuses[$statusKey] ) ? $booking_statuses[$statusKey] : false;
}

/**
 * Main function for returning booking, uses the WPB_Booking class.
 * @return bool|WPB_Booking
 */
function wpb_get_bookings($args = array()) {  
    // Default arguments
    $defaults = array(
        'user_id'       => 0,
        'paged'         => 1,
        'per_page'      => 31,
        'status'        => [],
        'booking_type'  => [],
        'date_from'     => '',
        'date_to'       => '',
        'order'         => 'DESC',
        'order_by'      => 'booking_date',
        'booking_name'  => '',
        'staff'         => 0,
        'offset'        => '',
        'is_paid'       => false    
    );
    $args = wp_parse_args($args, $defaults);
    return (new WPB_Booking)->get_bookings( $args );
}

/**
 * Main function for returning booking, uses the WPB_Booking class.
 * @return bool|WPB_Booking
 */
function wpb_get_booking($booking = false) {
    if (!$booking)
        return $booking;
    return (new WPB_Booking)->get_booking($booking);
}
