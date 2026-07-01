<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wpb_available_import_file_type(){
    return apply_filters('wpb_available_import_files',[]) ;
}
function get_require_csv_fields() {
    return apply_filters(
        'wpb_import_require_fields',
        [
            'booking' => [
                'date' => esc_html__("Date (Date should be less than current date)", 'wpbookit'),
                'start_time' => esc_html__("Start time", 'wpbookit'),
                'booking_type_id' => esc_html__("Booking Type ID", 'wpbookit'),
                'customer_email' => esc_html__("Customer Email", 'wpbookit'),
            ],
            'customer'=>[
                'first_name' => esc_html__("First Name",'wpbookit'),
                'last_name' => esc_html__("Last Name",'wpbookit'),
                'email'=> esc_html__("Customer Email",'wpbookit')
            ],
            'staff' => [
                'first_name' => esc_html__("First Name",'wpbookit'),
                'last_name' => esc_html__("Last Name",'wpbookit'),
                'email'=> esc_html__("Customer Email",'wpbookit')
            ],
            'booking_from_old_wpbookit_plugin'=>[
                'First Name' => esc_html__("First Name",'wpbookit'),
                'Last Name' => esc_html__("Last Name",'wpbookit'),
                'Email'=> esc_html__("Customer Email",'wpbookit'),
                'Date'=> esc_html__("Booking Date",'wpbookit'),
                'Start Time'=> esc_html__("Booking Start Time",'wpbookit'),
            ]
        ]
    );
}