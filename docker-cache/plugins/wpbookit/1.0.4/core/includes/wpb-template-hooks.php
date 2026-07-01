<?php
/**
 * WPBookit Template Hooks
 *
 * Action/filter hooks used for WPBookit functions/templates.
 *
 * @package WPBookit\Templates
 * @version 1.0.4
 */

defined( 'ABSPATH' ) || exit;


/**
 *
 * Profile
*/
add_action('wpb_edit_profile_hook','wpb_edit_profile',10);
add_action('wpb_bookings_history_hook','wpb_bookings_history',10);
add_action('wpb_upcoming_bookings_hook','wpb_upcoming_bookings',10,1);
add_action('wpb_pending_bookings_hook','wpb_pending_bookings',10);
add_action('wpb_booking_no_upcoming_hook','wpb_booking_no_upcoming',10,1);
add_action('wpb_booking_no_pending_hook','wpb_booking_no_pending',10,1);
add_action('wpb_booking_no_history_hook','wpb_booking_no_history',10,1);

/**
 * Booking-Types
 */
add_action('wpb_booking_types_hook','wpb_booking_types',10);


add_action('wpb_bookings_timeslot_hook','wpb_bookings_timeslot',10);
add_action('wpb_booking_shortcode_tabs_hook','wpb_booking_shortcode_tabs_render',10);

add_action('wpb_booking_shortcode_detail_tab','wpb_booking_shortcode_detail_tab_render',10);
add_action('wpb_booking_shortcode_payment_tab','wpb_booking_shortcode_payment_tab_render',10);
add_action('wpb_booking_shortcode_model_pagination','wpb_booking_shortcode_model_pagination_render',10);


add_action('wpb_booking_shortcode_after','wpb_bookings_model',10);


add_action('wpb_add_booking_type_form','wpb_add_booking_type_form',10);

add_action('wpb_booking_shortcode_form','wpb_booking_shortcode_form',10);
add_action('wpb_booking_shortcode_form_question_type','wpb_booking_shortcode_form_question_type',10);
add_action('wpb_booking_shortcode_user_name_fields','wpb_booking_shortcode_user_name_fields',10);

/**
 * Staff setting fields
 */

add_action( 'wpb_add_navbar_menu', 'wpb_staff_navbar_menu_filter' );