<?php

/**
 * WPBookit Woocommerce Payment Integration
 *
 */

defined('ABSPATH') || exit;


class WPB_Booking_Cancellation
{

    public function __construct()
    {
        add_action('admin_post_nopriv_cancel_booking', [$this, 'handle_booking_cancellation']);
        add_action('admin_post_cancel_booking', [$this, 'handle_booking_cancellation']);
    }
    public function handle_booking_cancellation()
    {
        global $wpdb;
        if (isset($_GET['booking_id']) && isset($_GET['token'])) {
            $booking_id = intval($_GET['booking_id']);
            $token = $_GET['token'];
            add_filter('nonce_user_logged_out','wpb_return_zero');
            if (wp_verify_nonce($token, 'cancel_booking_' . $booking_id)) {

                $where = array(
                    'id' => $booking_id
                );

                $booking_data = array(
                    'status'            => 'wpb-cancelled'
                );

                $response = $wpdb->update($wpdb->wpb_bookings, $booking_data, $where);

                do_action('wpb_customer_cancel_booking', (int) $booking_id);

                // cancel staff email hook
                do_action('wpb_staff_booking_cancellation', (int) $booking_id);
            }
        }
        wp_redirect(home_url());
        exit;
    }
}
new WPB_Booking_Cancellation();
