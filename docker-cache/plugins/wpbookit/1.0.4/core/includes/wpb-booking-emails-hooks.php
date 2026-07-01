<?php

if (!defined('ABSPATH')) {
    exit;
}

$actions = [
    'wpb_customer_booking_reminder' => ['wpb_trigger_customer_booking_email',1],
    'wpb_customer_registration' => ['wpb_trigger_user_registration_email',2],
    'wpb_customer_cancel_booking' => ['wpb_trigger_cancellation_email',1],
    'wpb_customer_booking_approval' => ['wpb_trigger_approval_email',1],
    'wpb_customer_booking_confirmation' => ['wpb_trigger_confirmation_email',1],
    'wpb_staff_booking_reminder' => ['wpb_trigger_staff_booking_email',1],
    'wpb_staff_booking_request' => ['wpb_trigger_staff_booking_request_email',1],
    'wpb_staff_booking_cancellation' => ['wpb_trigger_staff_booking_cancellation_email',1],
    'wpb_staff_registration' => ['wpb_trigger_staff_registration_email',2],
];

foreach ($actions as $action => $details) {
    $function = $details[0];
    $parameter_count = $details[1];
    add_action($action, $function, 10, $parameter_count);
}

function wpb_get_booking_instances($booking_id)
{
    $booking = new WPB_Booking($booking_id);
    $booking_type_id = $booking->get_booking_type_id();
    $customer_id = $booking->get_customer_id();
    $wp_customer_instance = !empty($customer_id) ? new WP_User($customer_id) :((Object) [
        'user_login' => $booking->get_booking_name(),
        'user_email' => $booking->get_booking_email(),
    ]);
    
    $booking_type = new WPB_Booking_Type($booking_type_id);
    $staff_id = get_metadata('wpb_booking_type', $booking_type_id, 'staff', true);
    $wp_staff_instance = new WP_User($staff_id);

    return [$booking, $booking_type, $wp_customer_instance, $wp_staff_instance];
}

function wpb_trigger_email($email_title, $to_email, $message_replacements,$custom_email_content='')
{
    $email_data = wpb_get_email_data($email_title);
    if ($email_data->status) {
        $email_subject = $email_data->emails_subject;
        if(!empty($custom_email_content)){
            $email_data->emails_content=$custom_email_content;
        }
        $email_message = wpb_email_content_key_replace($email_data->emails_content, $message_replacements);
        $status = wpb_mailer($to_email, $email_subject, $email_message);
    }
}

function wpb_trigger_customer_booking_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);


    wpb_trigger_email(
        'Customer Booking Reminder',
        $booking->get_booking_email(),
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type]
    );
}

function wpb_trigger_approval_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);
    wpb_trigger_email(
        'Customer Booking Approval',
        $booking->get_booking_email(),
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type]
    );
}

function wpb_trigger_confirmation_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);
    $email_reminder = $booking_type->get_booking_type_meta('email_reminder');
    $custom_email_content= '';
    if($email_reminder=='true'){
        $custom_email_content = $booking_type->get_booking_type_meta('email_content_editor');
    }
    wpb_trigger_email(
        'Customer Booking Confirmation',
        $booking->get_booking_email(),
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type],
        $custom_email_content
    );
}

function wpb_trigger_cancellation_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);
    wpb_trigger_email(
        'Customer Booking Cancellation',
        $booking->get_booking_email(),
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type]
    );
}

function wpb_trigger_user_registration_email(WP_User $user,$password)
{
    wpb_trigger_email(
        'Customer Registration',
        $user->user_email,
        ['user' => $user, 'password' => $password]
    );
}

function wpb_trigger_staff_registration_email(WP_User $user, $password)
{
    wpb_trigger_email(
        'Staff Registration',
        $user->user_email,
        ['user' => $user, 'password' => $password]
    );
}

function wpb_trigger_staff_booking_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);
    wpb_trigger_email(
        'Staff Booking Reminder',
        $wp_staff_instance->user_email,
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type]
    );
}

function wpb_trigger_staff_booking_cancellation_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);
    wpb_trigger_email(
        'Staff Booking Cancellation',
        $wp_staff_instance->user_email,
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type]
    );
}

function wpb_trigger_staff_booking_request_email($booking_id)
{
    list($booking, $booking_type, $wp_customer_instance, $wp_staff_instance) = wpb_get_booking_instances($booking_id);
    wpb_trigger_email(
        'Staff Booking Request',
        $wp_staff_instance->user_email,
        ['user' => $wp_customer_instance, 'staff' => $wp_staff_instance, 'booking' => $booking, 'booking_type' => $booking_type]
    );
}

function wpb_get_email_data($emails_title)
{
    global $wpdb;

    $sql = "SELECT * FROM {$wpdb->wpb_booking_emails} WHERE emails_title = %s";
    return $wpdb->get_row($wpdb->prepare($sql, $emails_title)); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function wpb_email_content_key_replace($email_message, $replacements)
{
    $site_path = home_url('/') . 'wp-admin/';
    $keys = [
        '{{customer_name}}' => isset($replacements['user']->data->display_name) ? 
            $replacements['user']->data->display_name : (isset($replacements['booking']) ? $replacements['booking']->get_booking_name() : ''),
        '{{customer_email}}' => isset($replacements['user']->data->user_email) ? 
            $replacements['user']->data->user_email : (isset($replacements['booking']) ? $replacements['booking']->get_booking_email() : ''),
        '{{booking_type}}' => (isset($replacements['booking']) && isset($replacements['booking_type'])) ? $replacements['booking']->get_booking_type()['name'] : '',
        '{{booking_status}}' => (isset($replacements['booking'])) ? (ucfirst(str_replace('wpb-', '', $replacements['booking']->get_status('view')))) : '',
        '{{booking_date}}' => (isset($replacements['booking'])) ? $replacements['booking']->get_booking_date() : '',
        '{{booking_time}}' => (isset($replacements['booking'])) ? $replacements['booking']->get_timeslot('view', true) : '',
        '{{staff_name}}' => (isset($replacements['staff']) && isset($replacements['staff']->data->display_name)) ? $replacements['staff']->data->display_name : '',
        '{{login_url}}' => get_first_wpb_profile_page_url() ?? '',
        '{{meeting_url}}' => (isset($replacements['booking'])) ? get_metadata('wpb_bookings', $replacements['booking']->get_id(), 'location_source',true) : '',
        '{{password}}' => isset($replacements['password']) ? $replacements['password'] : '', 
        '{{staff_login}}' => $site_path
    ];


    $keys = apply_filters('wpb_template_dynamic_keys_value', $keys,$email_message, $replacements);

    return str_replace(array_keys($keys), array_values($keys), $email_message);
}
