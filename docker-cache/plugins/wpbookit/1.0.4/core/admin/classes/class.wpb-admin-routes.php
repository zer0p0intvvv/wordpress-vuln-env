<?php


abstract class WPB_Routes
{

    protected $routes;

    public function __construct()
    {
//        $this->routes();
    }

    protected function routes()
    {
        $this->routes = apply_filters(
            'wpb_route_lists',
            array(
                'get_dashboard_apt_revenue_date' => [
                    'method' => 'get',
                    'action' => 'WPB_Home_Controller@get_dashboard_apt_revenue_date',
                    'module' => 'dashboard-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-page.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-dashboard.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-customer.php",
                    )
                ],
                'booking_list' => [
                    'method' => 'get',
                    'action' => 'WPB_Booking_Controller@get_bookings',
                    'module' => 'booking-controller'
                ],
                'add_booking' => [
                    'method' => 'post',
                    'action' => 'WPB_Bookings_Controller@add_booking',
                    'nonce' => 0,
                    'module' => 'bookings-controller'
                ],
                'delete_customer' => [
                    'method' => 'post',
                    'action' => 'WPB_Customer_Controller@delete_customer_callback',
                    'nonce' => 0,
                    'module' => 'customer-controller'
                ],
                'get_booking_type_slug' => [
                    'method' => 'get',
                    'action' => 'WPB_Booking_type_Controller@is_slug_unique',
                    'module' => 'booking-type-controller'
                ],
                'add_booking_type' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_type_Controller@add_booking_type',
                    'module' => 'booking-type-controller',
                ],
                'get_booking_type' => [
                    'method' => 'get',
                    'action' => 'WPB_Booking_type_Controller@get_booking_type',
                    'module' => 'booking-type-controller'
                ],
                'delete_booking_type' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_type_Controller@delete_booking_type',
                    'module' => 'booking-type-controller'
                ],
                'clone_booking_type' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_type_Controller@clone_booking_type',
                    'module' => 'booking-type-controller'
                ],
                'update_booking_type_status' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_type_Controller@update_booking_type_status',
                    'module' => 'booking-type-controller'
                ],
                'edit_newdata_customer' => [
                    'method' => 'post',
                    'action' => 'WPB_Customer_Controller@edit_newdata_customer_callback',
                    'nonce' => 1,
                    'module' => 'customer-controller'
                ],
                'login_customer' => [
                    'method' => 'post',
                    'action' => 'WPB_Customer_Controller@login_customer',
                    'nonce' => 0,
                    'module' => 'customer-controller'
                ],
                'register_customer' => [
                    'method' => 'post',
                    'action' => 'WPB_Customer_Controller@register_customer',
                    'nonce' => 0,
                    'module' => 'customer-controller'
                ],
                'edit_email_details' => [
                    'method' => 'post',
                    'action' => 'WPB_Email_Controller@edit_email_details',
                    'nonce' => 0,
                    'module' => 'email-controller'
                ],
                'get_email_details' => [
                    'method' => 'post',
                    'action' => 'WPB_Email_Controller@get_email_details',
                    'nonce' => 0,
                    'module' => 'email-controller'
                ],
                'email_status_update' => [
                    'method' => 'post',
                    'action' => 'WPB_Email_Controller@email_status_update',
                    'nonce' => 0,
                    'module' => 'email-controller'
                ],
                'add_general_setting' => [
                    'method' => 'post',
                    'action' => 'WPB_Setting_Controller@add_general_setting',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'save_custome_code' => [
                    'method' => 'post',
                    'action' => 'WPB_Setting_Controller@save_custome_code',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'add_new_customer' => [
                    'method' => 'post',
                    'action' => 'WPB_Customer_Controller@add_newdata_customer_callback',
                    'module' => 'customer-controller',
                    'nonce' => 1,
                ],
                'get_customer_list'    => [
                    'method' => 'get',
                    'action' => 'WPB_Customer_Controller@get_customer_list',
                    'module' => 'customer-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-core-functions.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-page.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-customer.php",
                    )
                ],
                'get_guest_list'    => [
                    'method' => 'get',
                    'action' => 'WPB_Guest_Controller@get_guest_list',
                    'module' => 'guest-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-core-functions.php",
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-guest-users-functions.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-page.php",
                    )
                ],
                'delete_guest_user' => [
                    'method' => 'post',
                    'action' => 'WPB_Guest_Controller@delete_guest_callback',
                    'nonce' => 0,
                    'module' => 'guest-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-guest-users-functions.php",
                    )
                ],
                'refresh_table_column' => [
                    'method' => 'post',
                    'action' => 'WPB_Customer_Controller@refresh_table_callback',
                    'module' => 'customer-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-core-functions.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-page.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-customer.php",
                    )
                ],
                'edit_profile_data' => [
                    'method' => 'post',
                    'action' => 'WPB_Profile_controller@edit_profile_data',
                    'module' => 'profile-controller',
                    'nonce' => 1,
                ],
                'get_profile_data' => [
                    'method' => 'get',
                    'action' => 'WPB_Profile_controller@get_profile_data',
                    'moduel' => 'profile-controller'
                ],
                'delete_booking' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_Controller@delete_booking_callback',
                    'module' => 'booking-controller'
                ],
                'get_booking_timeslot_dashboard' => [
                    'method' => 'get',
                    'action' => 'WPB_Booking_Controller@get_booking_timeslot_dashboard',
                    'module' => 'booking-controller'
                ],
                'add_update_booking' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_Controller@add_update_bookings_data',
                    'module' => 'booking-controller',
                ],
                'select_booking' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_Controller@select_booking',
                    'module' => 'booking-controller',
                ],
                'get_booking_details' => [
                    'method' => 'get',
                    'action' => 'WPB_Booking_Controller@get_bookings_data',
                    'module' => 'booking-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/abstracts/abstract-wpb-data.php",
                        IQWPB_PLUGIN_PATH . "core/includes/classes/class.wpb-booking.php",
                    )
                ],
                'refresh_booking_table' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_Controller@bookings_data_table',
                    'module' => 'booking-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-booking-functions.php",
                        IQWPB_PLUGIN_PATH . "core/includes/abstracts/abstract-wpb-data.php",
                        IQWPB_PLUGIN_PATH . "core/includes/classes/class.wpb-booking.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-page.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-bookings.php",
                    )
                ],
                'bookings_data_filter' => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_Controller@bookings_data_table',
                    'module' => 'booking-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/abstracts/abstract-wpb-data.php",
                        IQWPB_PLUGIN_PATH . "core/includes/classes/class.wpb-booking.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-page.php",
                        IQWPB_PLUGIN_PATH . "core/admin/classes/settings/class.wpb-settings-bookings.php",
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-booking-functions.php",
                    )
                ],
                'get_booking_timeslot' => [
                    'method' => 'get',
                    'action' => 'WPB_Booking_ShortCode_Controller@get_booking_timeslot',
                    'module' => 'booking-shortcode-controller',
                    'dependency' => array(
                        IQWPB_PLUGIN_PATH . "core/includes/abstracts/abstract-wpb-data.php",
                        IQWPB_PLUGIN_PATH . "core/includes/classes/class.wpb-booking.php",
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-booking-functions.php",
                    ) 
                ],
                'new_booking'  => [
                    'method' => 'post',
                    'action' => 'WPB_Booking_ShortCode_Controller@new_booking',
                    'module' => 'booking-shortcode-controller',
                    'dependency'=> array( 
                        IQWPB_PLUGIN_PATH . "core/includes/abstracts/abstract-wpb-data.php",
                        IQWPB_PLUGIN_PATH . "core/includes/classes/class.wpb-booking.php",
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-booking-functions.php",
                    ) 
                ],
                'cancle_booking_appointment'  => [
                    'method' => 'post', 
                    'action' => 'WPB_Booking_ShortCode_Controller@cancle_booking_appointment',
                    'module' => 'booking-shortcode-controller',
                    'dependency'=> array( 
                        IQWPB_PLUGIN_PATH . "core/includes/abstracts/abstract-wpb-data.php",
                        IQWPB_PLUGIN_PATH . "core/includes/classes/class.wpb-booking.php",
                        IQWPB_PLUGIN_PATH . "core/includes/wpb-booking-functions.php",
                    ) 
                ],
                'get_payment_gateways_list' => [
                    'method' => 'get',
                    'action' => 'WPB_Setting_Controller@get_payment_gateways_list',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'save_woocommerce_payment_gateway' => [
                    'method' => 'post',
                    'action' => 'WPB_Setting_Controller@save_woocommerce_payment_gateway',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
              
                'add_offline_payment_list' => [
                    'method' => 'post',
                    'action' => 'WPB_Setting_Controller@add_offline_payment_list',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'update_payment_mode_status' => [
                    'method' => 'post',
                    'action' => 'WPB_Setting_Controller@update_payment_mode_status',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'delete_payment_mode' => [
                    'method' => 'get',
                    'action' => 'WPB_Setting_Controller@delete_payment_mode',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'wpb_zoom_remove_oauth_connection' => [
                    'method' => 'get',
                    'action' => 'WPB_Setting_Controller@wpb_zoom_remove_oauth_connection',
                    'nonce' => 0,
                    'module' => 'setting-controller'
                ],
                'calendar_booking_list' => [
                    'method' => 'get',
                    'action' => 'WPB_Calendar_Controller@get_calendar_booking',
                    'module' => 'calendar-controller'
                ],
               
                'wpb_import' => [
                    'method' => 'post',
                    'action' => 'WPB_Import_Controller@wpb_import',
                    'module' => 'import-controller'
                ],
            )
        );
    }

    public function get_route($route_name)
    {
        return $this->routes[$route_name];
    }

    public function has_route($route_name)
    {
        return array_key_exists($route_name, $this->routes);
    }
}
