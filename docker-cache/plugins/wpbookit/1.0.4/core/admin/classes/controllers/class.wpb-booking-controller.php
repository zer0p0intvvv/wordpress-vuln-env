<?php

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\EchoedStringsSniff;

final class WPB_Booking_Controller
{
    private  $slots_per_booking;

    public function get_bookings(WP_REST_Request $request)
{
    $args = array(
        'user_id'       => 0,
        'paged'         => 1,
        'status'        => [],
        'booking_type'  => [],
        'per_page'      => $request->get_param('length'),
        'date_from'     => '',
        'date_to'       => '',
        'booking_name'  => '',
        'order'         => 'DESC',
        'order_by'      => 'id',
        'offset'        => $request->get_param('start'),
        // 'staff'         => get_current_user_id(),
    );

    if ($request->has_param('advanceFilter')) {
        $advanceFilter = $request->get_param('advanceFilter');

        if (isset($advanceFilter['wpb_booking_daterange'])) {
            $dateRange = explode(' to ', $advanceFilter['wpb_booking_daterange']);
            if (count($dateRange) == 2) {
                $args['date_from'] = date('Y-m-d', strtotime($dateRange[0]));  // phpcs:ignore   WordPress.DateTime.RestrictedFunctions.date_date 
                $args['date_to'] = date('Y-m-d', strtotime($dateRange[1])); // phpcs:ignore   WordPress.DateTime.RestrictedFunctions.date_date 
            }
        }

        $args['date_from'] = $args['date_from'] ?: $advanceFilter['date_from'];
        $args['date_to'] = $args['date_to'] ?: $advanceFilter['date_to'];

        $args = array_merge($args, [
             'status' => $advanceFilter['wpbit_status'] ?? [],
             'booking_type' => $advanceFilter['wpbit_booking_type'] ?? [], 
        ]);
    }

    if ($request->has_param('customer_search')) {
        $args['booking_name'] = $request->get_param('customer_search');
    }

    if ($request->has_param('order')) {
        $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');
        $args['order_by'] = $request->get_param('order')[0]['name'] ?? "";
    }

    $booking = wpb_get_bookings($args);

    $data = array_map(function ($item) {
        $user_id = 0;
        if ($user = get_user_by('email', $item->get_booking_email())) {
            $user_id = $user->ID;
        }
        global $wpdb;
        $booking_query = $wpdb->prepare("SELECT booking_type FROM $wpdb->wpb_bookings WHERE id = %d", $item->get_id());
        $booking_type_title = $wpdb->get_var($booking_query);  // phpcs:ignore   WordPress.DB.PreparedSQL.NotPrepared 

        $booking_type = $item->get_booking_type('view', ['name', 'id'], true);

        $locations = [
            'physical_address' => esc_html__("Address", 'wpbookit'),
            'online_video' => esc_html__("Meeting URL", 'wpbookit'),
            'phone_number' => esc_html__("Phone Number", 'wpbookit')
        ];

        $user__profile_url = wp_get_attachment_url(get_user_meta($user_id, "wp_user_avatar", true) ?? 0);
        $tax_data_json = $item->get_meta('tax_data');
        $tax_data = is_array($tax_data_json) ?$tax_data_json : json_decode($tax_data_json, true);
        $price = isset($tax_data['main_total']) ? $tax_data['main_total'] : 0;
        $free_label = __("Free", 'wpbookit');
        $booking_data = [
            'id' => $item->get_id(),
            'name' => $item->get_booking_name(),
            'payment_id' => $item->get_payment_id(),
            'gender' => empty(get_user_meta($user_id, "gender", true)) ? '-' : get_user_meta($user_id, "gender", true),
            'dob' => empty(get_user_meta($user_id, "date_of_birth", true)) ? '-' : wpb_get_formated_date_time(get_user_meta($user_id, "date_of_birth", true), ''),
            'profile_img' => $user__profile_url === false ? get_avatar_url(0, ['size' => 50]) : $user__profile_url,
            'is_customer_image' => $user__profile_url === false ? false : true,
            'duration' => sprintf("%s %s", $item->get_booking_type('view', ['duration']) ? $item->get_booking_type('view', ['duration'])['duration'] : $item->get_meta('booking_duration') ?? 0, esc_html__("Min", 'wpbookit')),
            'email' => $item->get_booking_email(),
            'datetime' => $item->get_formated_booking_datetime(),
            'type' => $booking_type_title,
            'price' => $price ? wpb_get_general_settings()['prefix'].number_format( $item->get_dis_booking_price(),2,'.','').wpb_get_general_settings()['postfix'] : $free_label ,
            'date_created' => sprintf("%s %s", wp_date(get_option('date_format'), strtotime($item->get_date_created())), wp_date(get_option('time_format'), strtotime($item->get_date_created())), new DateTimeZone(wpb_get_timezone())),
            // 'staff' => get_user_by('ID', $booking_type ? $booking_type['meta']['staff'] : $item->get_meta('staff_id'))->display_name,
            'location' => $locations[$item->get_meta('location')] ?? false,
            'location_source' => $item->get_meta('location_source'),
            'status' => [
                'key' => $item->get_status(),
                'label' => wpb_booking_status_label($item->get_status())
            ],
            'questions_answers' => array_map(function ($item) {
                $item['question'] = wpb_unicode_to_utf8($item['question']);
                $item['ans'] = wpb_unicode_to_utf8($item['ans']);
                return $item;
            }, json_decode($item->get_meta('questions_answers') ?? '{}', true)),
            'actions' => [
                "edit" => true,
                "delete" => true,
            ],
        ];
        $booking_data = apply_filters('wpb_after_actions_modify_booking_data', $booking_data, $item);
        return $booking_data;
    }, $booking->results);

    wp_send_json(array(
        "recordsTotal" => $booking->total ?? 0,
        "recordsFiltered" => $booking->total ?? 0,
        "data" => apply_filters('wpb_get_booking_list_filter',$data)
    ));
}



    public function delete_booking_callback(WP_REST_Request $request)
    {
        global $wpdb;
        $request_data = $request->get_params();

        try {
            $bookingID = sanitize_text_field($request_data['bookingID']) ?? 0;
            do_action('wpbookit_booking_deleted', $bookingID); //remove this hooks once hooks dependency removed from addons.
            do_action('wpb_after_booking_deleted', $bookingID);
            if ($bookingID) :
                $resposne = $wpdb->delete(
                    $wpdb->wpb_bookings,
                    array('id' => $bookingID),
                    array('%d')
                );

                $wpdb->delete(
                    $wpdb->wpb_bookingsmeta,
                    array('wpb_bookings_id' => $bookingID),
                    array('%d')
                );
                $wpdb->delete(
                    $wpdb->wpb_payments,
                    array('bookings_id' => $bookingID),
                    array('%d')
                );

                if ($resposne) :
                    $response = [
                        'status'  => 'success',
                        'message' => esc_html__('Booking Deleted Successfully.', 'wpbookit')
                    ];
                else :
                    $response = [
                        'status' => 'error',
                        'message' => esc_html__('Error while deleting booking.', 'wpbookit')
                    ];
                endif;
            else :
                $response = array(
                    'status'  => 'success',
                    'message' => esc_html__('Booking Deleted Successfully.', 'wpbookit')
                );
            endif;
        } catch (Exception $e) {
            $response = [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
        $response = [
            'status'  => 'success',
            'message' => esc_html__('Booking Deleted Successfully.', 'wpbookit')
        ];
        wp_send_json($response);
    }

    public function bookings_data_table(WP_REST_Request $request)
    {

        $paged          = $request->get_param('paged') ?? 1;
        $customer_name  = $request->get_param('wpb_customer_name');

        $date_from      = $request->get_param('date_from');
        $date_to        = $request->get_param('date_to');

        $date_from      = !empty($date_from) ? date_format(date_create($date_from), 'Y-m-d') : '';
        $date_to        = !empty($date_to) ? date_format(date_create($date_to), 'Y-m-d') : '';

        // Add null coalescing operators for status and booking_type
        $status         = $request->get_param('wpb_status') ?? [];
        $booking_type   = $request->get_param('wpb_booking_type') ?? [];

        $args           = array(
            'paged'         => $paged,
            'per_page'      => 5,
            'date_from'     => $date_from,
            'date_to'       => $date_to,
            'status'        => $status,
            'booking_type'  => $booking_type,
            'booking_name'  => $customer_name
        );

        $bookings           = wpb_get_bookings(apply_filters('wpb_bookings_table_bookings_data', $args));
        $result_total_booking = count($bookings->results);
        $columns             = WPB_Settings_Bookings::get_table_column();
        $pagination_output    = wpb_get_pagination($bookings->maxnumpages, $args['paged'], 'bookings');
        $total_booking = ($result_total_booking < $args['per_page']) ? $result_total_booking : $args['per_page'];

        ob_start();
        require_once IQWPB_PLUGIN_PATH . "core/admin/views/settings/bookings/bookings-table.php";
        $table_content = ob_get_clean();

        $table_showing_print =  sprintf('Showing ' . $total_booking . ' of ' . $bookings->total . ' entries');

        wp_send_json(
            array(
                "table_content"       => $table_content,
                "table_showing_print" => $table_showing_print,
                "paged"               => @$args['paged'],
            )
        );
    }

    public function add_update_bookings_data(WP_REST_Request $request)
    {
        global $wpdb;
        $data                   = $request->get_params();

        $booking_id             = $data['edit-booking-id'];
        $booking_type           = isset($data["wpb_booking_type"]) ? sanitize_text_field($data["wpb_booking_type"]) : '';
        $date                   = isset($data["date"]) ? $data["date"] : '';
        $time                   = isset($data["wpb_booking_slot_time"]) ? sanitize_text_field($data["wpb_booking_slot_time"]) : '';
        $wpb_customer           = isset($data["wpb_customer"]) ? sanitize_text_field($data["wpb_customer"]) : '';
        $booking_status         = isset($data["wpb_booking_status"]) ? sanitize_text_field($data["wpb_booking_status"]) : '';
        $booking_payment_mode   = isset($data["wpb_booking_payment_mode"]) ? sanitize_text_field($data["wpb_booking_payment_mode"]) : '';
        $booking_payment_status = isset($data["wpb_booking_payment_status"]) ? sanitize_text_field($data["wpb_booking_payment_status"]) : '';
        $notes                  = isset($data["notes"]) ? sanitize_text_field($data["notes"]) : '';
        $formatted_date         = date('Y-m-d', strtotime($date));  // phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date 
        $parts                  = explode('-', $booking_status);
        $status                 = end($parts);
        $customer_info          = get_userdata($wpb_customer);
        $booking_type_name      = isset($data["bookingType"]) ? sanitize_text_field($data["bookingType"]) : '';
        $timestamp              = strtotime($time);
        $time_24h               = date('H:i:s', $timestamp);  // phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date 

        $wpb_booking_type = wpb_get_booking_type((int)$booking_type, ['id'], true);
        $wpb_booking_type_duration = wpb_get_booking_type((int)$booking_type, ['duration'], true);
        $interval = $wpb_booking_type_duration['duration'] ?? 0;
        if ($booking_id) :
            $where = array(
                'id' => $booking_id
            );

            $booking_data = array(
                'status'            => $booking_status
            );

            $old_booking = new WPB_Booking($booking_id);
            $old_booking_status = $old_booking->get_status();

            $response = $wpdb->update($wpdb->wpb_bookings, $booking_data, $where);
            $booking_payment_data = array(
                'bookings_id'       => $booking_id,
                // 'payment_mode'      => $booking_payment_mode,
                'payment_status'    => $booking_payment_status,
                'date_created'      => current_time('mysql', 1)
            );

            $payment_where = array(
                'bookings_id' => $booking_id
            );

            $payment_response = $wpdb->update($wpdb->wpb_payments, $booking_payment_data, $payment_where);
            $ststus = update_metadata('wpb_bookings', (int)$booking_id, 'booking_notes', $notes, '');
            if ($response !== false || $payment_response !== false) :
                if ($booking_status === 'wpb-approved') {
                    $booking_query = $wpdb->prepare("SELECT * FROM $wpdb->wpb_bookings WHERE id = %d", $booking_id);
                    $booking_data = $wpdb->get_row($booking_query);  // phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared 
                    if ($old_booking_status === 'wpb-pending') {
                        // customer create zoom meeting
                        do_action('wpb_booking_zoom_create', (array) $booking_data, (int) $booking_id);
                        do_action('wpb_customer_location', (int) $booking_id);
                    } else {
                        do_action('wpb_customer_booking_confirmation', (int) $booking_id);
                        wpb_booking_reminder($booking_id);
                    }
                    // customer approve email hook
                    do_action('wpb_customer_booking_approval', (int) $booking_id);
            
                } elseif ($booking_status === 'wpb-cancelled') {
                    // cancel customer email hook
                    do_action('wpb_customer_cancel_booking', (int) $booking_id);
                } elseif ($booking_status === 'wpb-completed') {
                    // customer ratings
                    do_action('wpb_customer_ratings', (int) $booking_id);
                }
            
                do_action('wpb_after_booking_update', (int) $booking_id);
                
                $response_data = [
                    "status"    => 'success',
                    "message"   => esc_html__("Booking Updated Successfully", 'wpbookit'),
                ];
                wp_send_json_success($response_data);
            else :
                $response_data = [
                    "status"    => 'error',
                    "message"   => esc_html__("Booking Not Updated", 'wpbookit'),
                ];
                wp_send_json_error($response_data);
            endif;
            

        else :
            $booking_data = array(
                'booking_type_id'   => $booking_type,
                'customer_id'       => $wpb_customer,
                'booking_name'      => $customer_info->display_name,
                'booking_email'     => $customer_info->user_email,
                'booking_date'      => $formatted_date,
                'booking_type'      => $booking_type_name,
                'timeslot'          => $time_24h,
                'status'            => $booking_status,
                'date_created'      => current_time('mysql', 1)
            );
            $args = [
                'booking_id'            => $booking_id,
                'data'                  => $data,
                'customer_info'         => $customer_info,
                'booking_type_name'     => $booking_type_name,
                'formatted_date'        => $formatted_date,
                'time_24h'              => $time_24h,
                'interval'              => $interval,
            ];
            
              // Add Booking Insert Do Actions
              do_action( 'wpb_before_booking_insert', $args );

            $result = $wpdb->insert($wpdb->wpb_bookings, $booking_data);
            if ($result) :
                $booking_id = $wpdb->insert_id;
                $args['booking_id' ]= $booking_id ;
              
              
                
                
                
                update_metadata('wpb_bookings', $booking_id, 'booking_notes', $notes, '');
                update_metadata('wpb_bookings', (int)$booking_id, 'staff_id', $wpb_booking_type['meta']['staff']??'0');
                update_metadata('wpb_bookings', (int)$booking_id, 'booking_duration', $wpb_booking_type_duration['duration']??'0');

                if ($booking_status === 'wpb-approved') {
                    do_action('wpb_booking_zoom_create', (array) $booking_data, (int) $booking_id);
                    do_action('wpb_customer_booking_confirmation', (int) $booking_id);
                    do_action('wpb_customer_location', (int) $booking_id);
                    wpb_booking_reminder($booking_id);
                } elseif ($booking_status === 'wpb-cancelled') {
                    // cancle customer email hook
                    do_action('wpb_customer_cancel_booking', (int) $booking_id);

                    // cancle staff email hook
                    do_action('wpb_staff_booking_cancellation', (int) $booking_id);
                } elseif ($booking_status === 'wpb-pending') {
                    // staff booking request email hook
                    do_action('wpb_staff_booking_request', (int) $booking_id);
                }

                // Add Booking Insert Do Actions
                do_action( 'wpb_after_booking_insert', $args );
                
                $response_data = [
                    "status"    => 'success',
                    "message"   => esc_html__("Booking Added Successfully", 'wpbookit'),
                ];
                wp_send_json_success($response_data);
            else :
                $response_data = [
                    "status"    => 'error',
                    "message"   => esc_html__("Booking Not Added", 'wpbookit'),
                ];
                wp_send_json_error($response_data);
            endif;

        endif;
    }

    public function get_bookings_data(WP_REST_Request $request)
    {
        global $wpdb;
        $response = array(
            'status'  => 'error',
        );

        $data           = $request->get_params();
        $booking_id     = isset($data["booking_id"]) ? sanitize_text_field($data["booking_id"]) : '';
        $booking        = wpb_get_booking($booking_id);

        if ($booking) :
            $time_slot = $booking->get_timeslot();
            $price= $booking->get_raw_booking_sub_price();
            $response = array(
                'status'                => 'success',
                'id'                    => $booking->get_id(),
                'booking_type_id'       => $booking->get_booking_type_id(),
                'booking_date'          => $booking->get_booking_date(),
                'booking_time'          => date("g:i A", strtotime($time_slot)),  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
                'booking_customer_id'   => $booking->get_customer_id(),
                'booking_name'          => $booking->get_booking_name(),
                'booking_status'        => $booking->get_status(),
                'payment_mode'          => $booking->get_payment_mode(),
                'payment_status'        => $booking->get_payment_status(),
                'booking_notes'         => get_metadata('wpb_bookings', $booking_id, 'booking_notes', true) ?? '',
                'booking_price'         => $booking->get_booking_price(),
            );
        endif;
        wp_send_json(apply_filters('wpb_after_booking_price_html', $response, $booking_id));
    }

    // Need to repalce
    public function select_booking(WP_REST_Request $request)
    {
        $data             = $request->get_params();
        $booking_type_id  = isset($data["booking_type_id"]) ? sanitize_text_field($data["booking_type_id"]) : '';
        $booking_type     = new WPB_Booking_Type($booking_type_id);

        // Get Original Price
        $original_price = $booking_type->get_meta('price');

        // Generate HTML output

        $response_data = array(
            'specific_dates' => !empty($booking_type->get_meta('specific_dates')) ? json_decode($booking_type->get_meta('specific_dates')) : [],
            'weekly_time_slots' => !empty($booking_type->get_meta('weekly_time_slots')) ? json_decode($booking_type->get_meta('weekly_time_slots')) : [],
            'how_far' => !empty($booking_type->get_meta('how_far')) ? json_decode($booking_type->get_meta('how_far')) : [],
            'html_output' =>'<div class="d-flex justify-content-between main_booking_total">                    <h6 class="mb-0">Total:</h6>                    <p class="m-0"><span class="text-primary price_text">'.esc_html__("Free",'wpbookit').'</span></p>                </div>' ,
            'tax_values' => esc_html__("Free",'wpbookit'),
        );

        wp_send_json_success(apply_filters('wpb_filter_select_booking',$response_data,$booking_type));
    }

    public function get_booking_timeslot_dashboard(WP_REST_Request $request)
    {
        $booking_type_id = $request->get_param('bookingTypeId');
        $selected_date = $request->get_param('selected_date');
        $timestamp = strtotime($selected_date);
        $slots = $this->get_available_time_slots_by_type($booking_type_id, $timestamp);
        wp_send_json(['status' => 'success', 'data' => $slots]);
    }

    public function generate_time_slots($start, $end, $interval, $booking_type_id, $exclude_timeslots = [], $unavaiable_date = [], $specificDay = '')
    {
        $found = false; 
        $unavailableTimes = [];
        $enable_group_booking = get_metadata('wpb_booking_type', $booking_type_id, 'enable_group_booking');
        $slots_per_booking = get_metadata('wpb_booking_type', $booking_type_id, 'slots_per_booking_number');

        foreach ($unavaiable_date as $unavailable) {
            if ($unavailable['date'] === $specificDay) {
                $found = true; 
                $unavailableTimes[] = $unavailable; 
                break; 
            }
        }
    
        $from = null;
        $to = null;
    
        if ($found) {
            foreach ($unavailableTimes as $time) {
                $fromTime = DateTime::createFromFormat('H:i', $time['from']);
                $toTime = DateTime::createFromFormat('H:i', $time['to']);
        
                $from = $fromTime->format('H:i:s');  
                $to = $toTime->format('H:i:s');     
            }
        }
    
        $start = new DateTime($start);
        $end = new DateTime($end);
        $intervalObj = new DateInterval('PT' . $interval . 'M');
        $timeSlots = [];
        $timeslot = wp_date(get_option('time_format'),$start->getTimestamp(),new DateTimeZone( 'UTC')) ;
        $timeslot_display = $start->format('h:i A');
    
        $fromTimeObj = $from ? DateTime::createFromFormat('H:i:s', $from) : null;
        $toTimeObj = $to ? DateTime::createFromFormat('H:i:s', $to) : null;
    
        while ($start < $end) {
            $timeslot = $start->format(get_option('time_format'));
            $currentFormatted = $start->format('H:i:s');
    
            $exclude = false;
    
            if (is_array($exclude_timeslots) && !empty($exclude_timeslots)) {
                if (array_key_exists($currentFormatted, $exclude_timeslots)) {
                    $exclude = true;
                }
            }
            if ($fromTimeObj && $toTimeObj) {
                if ($currentFormatted >= $toTimeObj->format('H:i:s') && $currentFormatted <= $fromTimeObj->format('H:i:s')) {
                    $exclude = true;
                }
            }
    
            if (!$exclude) {
                $timeSlots[] = $timeslot;
            }
           
            if ($enable_group_booking[0] == 'true' && is_array($exclude_timeslots) && !empty($exclude_timeslots)) {
                if (($exclude_timeslots[$start->format('H:i:s')] ?? 0) < ($slots_per_booking[0] - 1)) {
                    if (!in_array($timeslot, $timeSlots)) {
                        $timeSlots[] = $timeslot;
                    }
                }else {
                    if (($exclude_timeslots[$start->format('H:i:s')] ?? 0) < 1) {
                        $timeSlots[] = $timeslot;
                    }
                }
            }
            $start->add($intervalObj);
        }
        return $timeSlots;
    }
    
    private function get_available_time_slots_by_type($booking_type_id, $timestamp)
    {
        $dayOfWeek = strtolower(date("l", $timestamp)); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
        $specificDay = strtolower(date("Y-m-d", $timestamp));// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
       
        $booking_type_instance = new WPB_Booking_Type($booking_type_id);
        $booking_weekly_time_slots = json_decode(get_metadata('wpb_booking_type', $booking_type_id, 'weekly_time_slots', true), true);
        $booking_specific_day_time_slots = json_decode(get_metadata('wpb_booking_type', $booking_type_id, 'specific_dates', true), true);
        $unavaiable_date = json_decode(get_metadata('wpb_booking_type', $booking_type_id, 'unavailable_dates', true), true);
       // print_r($unavaiable_date);
        $duration = $booking_type_instance->get_duration();

        $this->slots_per_booking =  $booking_type_instance->get_meta('slots_per_booking_number') ?? INF;
        $wpb_booking    = (new WPB_Booking())->get_bookings(array(
            'paged'     => 1,
            'per_page'     => 99,
            'order_by'     => 'id',
            'order'       => 'DESC',
            'booking_type'     => [$booking_type_instance->get_id()],
            'date' => date('Y-m-d', $timestamp), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
        ));

        $booked_timeslot = array_count_values(array_map(function ($wpbooking_instance) {
            return $wpbooking_instance->get_timeslot();
        }, $wpb_booking->results));

        if (!empty($booking_type_instance->get_meta('maximum_booking')) && count($wpb_booking->results) >= $booking_type_instance->get_meta('maximum_booking')) {
            $booking_weekly_time_slots = $booking_specific_day_time_slots = [];
        }

        $slots = array();
        foreach ($booking_weekly_time_slots[$dayOfWeek] as $range) {
            $timeTo = $range['timeTo'];
            $timeFrom = $range['timeFrom'];
            foreach ($this->generate_time_slots($timeTo, $timeFrom, $duration, $booked_timeslot, $unavaiable_date, $specificDay, $booking_type_id) as $key => $value) {
                $slots[] = $value;
            }
        }
        if ($booking_specific_day_time_slots[$specificDay]) {
            $timeTo = $booking_specific_day_time_slots[$specificDay]['to'];
            $timeFrom = $booking_specific_day_time_slots[$specificDay]['from'];
            foreach ($this->generate_time_slots($timeFrom, $timeTo, $duration, $booked_timeslot) as $key => $value) {
                $slots[] = $value;
            }
        }
        return $slots;
    }
}
