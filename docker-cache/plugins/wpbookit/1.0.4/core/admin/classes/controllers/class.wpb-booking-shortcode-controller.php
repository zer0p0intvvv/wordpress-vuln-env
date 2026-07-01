<?php

final class WPB_Booking_ShortCode_Controller
{
    private $slots_per_booking;
    private $enable_group_booking;
    private $show_remaining_slot;
    
    private $maximum_buffer;
    private $block_time;
    private $duration;
    private $unavailable_date;
    public $booked_timeslot;

    public function get_booking_timeslot(WP_REST_Request $request)
    {

        $booking_type_id = $request->get_param('bookingTypeId');
        $selected_date = $request->get_param('selected_date');
        $timestamp = strtotime($selected_date);

        $dayOfWeek = strtolower(date("l", $timestamp)); //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
        $specificDay = strtolower(date("Y-m-d", $timestamp)); //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
        $date_formated = wpb_get_formated_date_time($specificDay, '');

        $booking_type_instance = new WPB_Booking_Type($booking_type_id);

        $booking_weekly_time_slots = json_decode(get_metadata('wpb_booking_type', $booking_type_instance->get_id(), 'weekly_time_slots', true), true);
        $booking_specific_day_time_slots = array_reduce(json_decode(get_metadata('wpb_booking_type', $booking_type_instance->get_id(), 'specific_dates', true), true), function($acc, $item) {
            $acc[$item['date']] = [
                'from' => $item['from'],
                'to' => $item['to']
            ];
            return $acc;
        }, []);

        $this->slots_per_booking = $booking_type_instance->get_meta('slots_per_booking_number') ?? INF;
        $this->enable_group_booking = $booking_type_instance->get_meta('enable_group_booking') ?? false;
        $this->show_remaining_slot = $booking_type_instance->get_meta('show_remaining_slot') ?? false;
        $this->unavailable_date = json_decode($booking_type_instance->get_meta('unavailable_dates'), true) ?? false;


        $this->maximum_buffer = (int)($booking_type_instance->get_meta('maximum_buffer')) ?? 0;
        $this->duration = ((int)($booking_type_instance->get_meta('duration')) ?? 0 ) + $this->maximum_buffer;

        $booking_type_ids = [$booking_type_instance->get_id()];
        $booking_types = wpb_get_all_booking_types(['staff'    => $booking_type_instance->get_meta('staff')]);

        foreach ($booking_types as $booking_type) {
            if ($booking_type->get_id() == $booking_type_instance->get_id()) continue;
            $booking_type_ids[] = $booking_type->get_id();
        }

        $wpb_booking = (new WPB_Booking())->get_bookings(
            array(
                'paged' => 1,
                'per_page' => 99,
                'order_by' => 'ID',
                'order' => 'DESC',
                'booking_type' => $booking_type_ids,
                'date' => date('Y-m-d', $timestamp)//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
            ) 
        );

        // Convert the array into a Laravel collection
        $bookingCollection = collect($wpb_booking->results)->map(function ($booking) {
            $booking= $booking->get_data();
            foreach ($booking['meta_data'] as $meta) {
                $booking[$meta['key']] = $meta['value'];
            }
            return $booking;
        });
      

        // Group bookings by both 'booking_date' and 'timeslot'
        $groupedBookingsByTimeslot = $bookingCollection->groupBy(function ($item) {
            return  $item['timeslot'];
        });


        $booked_timeslot = array_count_values(array_map(function ($wpbooking_instance) {
            return $wpbooking_instance->get_timeslot();
        }, $wpb_booking->results));
       
        foreach ($wpb_booking->results as $key => $wpbooking_instance) {
            $this->booked_timeslot[$wpbooking_instance->get_timeslot()][]=$wpbooking_instance;
        }

        foreach ($booked_timeslot as $key => $value) {
            $to = new DateTime($selected_date . ' ' . $key);
            $from = new DateTime($selected_date . ' ' . $key);
            $duration = ($this->maximum_buffer +  (int)$this->duration);
            $from->modify("+{$duration} Minutes");

            $this->block_time[$key] = ['to' =>  $to, 'from' => $from];
        }

        switch ($booking_type_instance->get_meta('booking_number_by')) {
            case 'days':
                if (!empty($booking_type_instance->get_meta('maximum_booking')) && (count($wpb_booking->results) >= $booking_type_instance->get_meta('maximum_booking'))) {
                    $booking_weekly_time_slots = $booking_specific_day_time_slots = [];
                }
                break;
            case 'weeks':
                $startOfWeek = strtotime("last monday", $timestamp);
                // If the given date is already Monday, adjust the start date to that day
                if (date('N', $timestamp) == 1) { //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    $startOfWeek = $timestamp;
                }

                $endOfWeek = strtotime("next sunday", $startOfWeek);
                $endOfWeek = strtotime("23:59:59", $endOfWeek);


                $wpb_weeks_booking = (new WPB_Booking())->get_bookings(
                    array(
                        'paged' => 1,
                        'per_page' => 99,
                        'order_by' => 'ID',
                        'order' => 'DESC',
                        'status' => ['wpb-approved', 'wpb-pending'],
                        'booking_type' => [$booking_type_instance->get_id()],
                        'date_from' => date('Y-m-d', $endOfWeek), //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                        'date_to' => date('Y-m-d', $startOfWeek),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    )
                );


                if (!empty($booking_type_instance->get_meta('maximum_booking')) && (count($wpb_weeks_booking->results) >= $booking_type_instance->get_meta('maximum_booking'))) {
                    $booking_weekly_time_slots = $booking_specific_day_time_slots = [];
                }
                break;
            case 'months':
                // Get the first day of the month
                $startOfMonth = strtotime(date("Y-m-01 00:00:00", $timestamp)); //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                $endOfMonth = strtotime(date("Y-m-t 23:59:59", $timestamp));//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                $wpb_month_booking = (new WPB_Booking())->get_bookings(
                    array(
                        'paged' => 1,
                        'per_page' => 99,
                        'order_by' => 'ID',
                        'order' => 'DESC',
                        'status' => ['wpb-approved', 'wpb-pending'],
                        'booking_type' => [$booking_type_instance->get_id()],
                        'date_from' => date('Y-m-d', $endOfMonth),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                        'date_to' => date('Y-m-d', $startOfMonth),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    )
                );



                if (!empty($booking_type_instance->get_meta('maximum_booking')) && (count($wpb_month_booking->results) >= $booking_type_instance->get_meta('maximum_booking'))) {
                    $booking_weekly_time_slots = $booking_specific_day_time_slots = [];
                }
                break;
        }



        ob_start();
        
        if ($booking_specific_day_time_slots[$specificDay] ?? false) {
            $timeTo = strtolower(date("Y-m-d", $timestamp)) . ' ' . $booking_specific_day_time_slots[$specificDay]['to']; //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
            $timeFrom = strtolower(date("Y-m-d", $timestamp)) . ' ' . $booking_specific_day_time_slots[$specificDay]['from'];//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date

            foreach ($this->generate_time_slots($timeFrom, $timeTo, (int)$this->duration, $booked_timeslot, $booking_type_instance, $specificDay) as  $value) {
                do_action('wpb_bookings_timeslot_hook', ['time_slot' => $value['slot'],'time_display' => $value['time_display'],'date_formated' => $date_formated, 'remain_lable' => $this->enable_group_booking == 'true' && $this->show_remaining_slot == 'true' ? sprintf("%s : %s/%s", esc_html__("Seats Left", 'wpbookit'), abs(($value['remain'] ?? 0) - (int)$this->slots_per_booking), $this->slots_per_booking) : false]);
            }
        }else{
            foreach (($booking_weekly_time_slots[$dayOfWeek] ?? []) as $range) {
                $timeFrom = strtotime($range['timeFrom']);
                $timeTo = strtotime($range['timeTo']);
                if ($timeFrom < $timeTo) {
                    $temp = $range['timeTo'];
                    $range['timeTo'] = $range['timeFrom'];
                    $range['timeFrom'] = $temp;
                }
            
                $timeTo = strtolower(date("Y-m-d", $timestamp)) . ' ' . $range['timeTo']; //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                $timeFrom = strtolower(date("Y-m-d", $timestamp)) . ' ' . $range['timeFrom'];//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                foreach ($this->generate_time_slots($timeTo, $timeFrom, (int)$this->duration, $booked_timeslot, $booking_type_instance, $specificDay) as $value) {
                    $remain = $this->enable_group_booking == 'true' && $this->show_remaining_slot == 'true' ? abs(($value['remain'] ?? 0) - (int)$this->slots_per_booking) : false;
                        if($remain===0){
                            continue;
                        }
                        do_action('wpb_bookings_timeslot_hook', [
                            'time_slot' => $value['slot'],
                            'date_formated' => $date_formated,
                            'time_display' => $value['time_display'],
                            // translators: Label of Seats Left placeholder:0, Remain Seat Count placeholder:1 ,Total Seat Count placeholder:2
                            'remain_lable' => $this->enable_group_booking == 'true' && $this->show_remaining_slot == 'true' ? sprintf("%s : %s/%s", esc_html__("Seats Left", 'wpbookit'), abs(($value['remain'] ?? 0) - (int)$this->slots_per_booking), $this->slots_per_booking) : false,
                            // translators: No of Available Seat placeholder:0
                            'remain_slot_label' => $this->enable_group_booking == 'true' && $this->show_remaining_slot == 'true' ? sprintf(__('%s Available', 'wpbookit'), abs(($value['remain'] ?? 0) - (int)$this->slots_per_booking)) : false,
                            'remain_slot' =>$remain = $this->enable_group_booking == 'true' && $this->show_remaining_slot == 'true' ? abs(($value['remain'] ?? 0) - (int)$this->slots_per_booking) : false
                        ]);

                }
            }
        }
       

        if (is_null($booking_weekly_time_slots[$dayOfWeek] ?? null) && is_null($booking_specific_day_time_slots[$specificDay] ?? null)) {
            do_action('wpb_bookings_timeslot_no_timeslot_available', ['time_slot' => $value, 'date_formated' => $date_formated]);
        }

        wp_send_json(['status' => 'success', 'data' => wp_kses_post(ob_get_clean())]);
    }
    public function generate_time_slots($start, $end, $interval, $exclude_timeslots, $booking_type_instance, $specificDay)
    {
        $booking_threshold = $booking_type_instance->get_meta('booking_threshold');
        $start = new DateTime($start);
        $gmt_offset = get_option('gmt_offset');

        $current_time = new DateTime('now');
        $current_time->setTime($current_time->format('H'), $current_time->format('i'), 0);

        $seconds_offset = $gmt_offset * 3600;
        $current_time->modify("+$seconds_offset seconds");

        $date_Selected = new DateTime($specificDay);

        $end = new DateTime($end);
        $intervalObj = new DateInterval('PT' . $interval . 'M');
        $timeSlots = [];

        // Check if the selected date is today
        $is_today = $date_Selected->format('Y-m-d') === $current_time->format('Y-m-d');

        if ($is_today && !empty($booking_threshold) && $booking_threshold > 0) :
            $current_time->modify('+' . (int)$booking_threshold . ' minutes');
        endif;


        if ($is_today && $current_time > $start) {
            $minutes = (int)$interval;
            $current_minutes = (int)$current_time->format('i');
            $start_minutes = (int)$start->format('i');
            $minutes_to_next_interval = $minutes - (($current_minutes - $start_minutes) % $minutes);

            $current_time->add(new DateInterval('PT' . $minutes_to_next_interval . 'M'));

            $start = $current_time;
        }

        while ($start < $end) {
            $timeslot_display = $start->format('h:i A');
            $timeslot = wp_date(get_option('time_format'),$start->getTimestamp(),new DateTimeZone( 'UTC')) ;

            $temp_start = clone $start;
            $temp_end = clone $start;
            $temp_end->add(new DateInterval('PT' . ($interval) . 'M'));
            $temp_interval = 0;
            
            $formattedStartDate = $start->format('Y-m-d');
            $found = false;

            foreach ($this->unavailable_date as $unavailable) {
                if ($unavailable['date'] === $formattedStartDate) {
                   
                    // Create a DateTime object from $fromTime
                    $fromTimeObj = new DateTime($unavailable['date'] . ' ' . $unavailable['from']);
                    $fromTimeObj->modify('-1 minute');
                    $fromTime = $fromTimeObj->format('H:i');
            
                    $toTime = $unavailable['to'];
                    $found = true;
                    break; 
                }
            }
            
            if ($found) {
                if ($this->unavailable_date && isset($unavailable['date']) &&  
                   (new DateTime($start->format('Y-m-d') . ' ' . $toTime) <= $start &&
                    new DateTime($start->format('Y-m-d') . ' ' . $fromTime) >= $start)) {         
                   
                    $start->add($intervalObj);
                    continue;
                }
            }
            
            if ($this->unavailable_date && isset($this->unavailable_date[$start->format('Y-m-d')]) &&  (new DateTime($start->format('Y-m-d') . ' ' .   $this->unavailable_date[$start->format('Y-m-d')]['to']) <=  $start   && new DateTime($start->format('Y-m-d') . ' ' . $this->unavailable_date[$start->format('Y-m-d')]['from']) >= $start)) {
                $start->add($intervalObj);
                continue;
            }

            $is_add= false;
            foreach ($this->block_time as $key => $booking_time) {
                if($booking_time['to'] <= $temp_start && $booking_time ['from'] >= $temp_end   ){
                    $temp_interval = $temp_interval + ($interval);
                    $is_add=true;
                }
            }
            if($is_add){
                $start->add(new DateInterval('PT' . ($temp_interval) . 'M'));
                continue;
            }
            if( $this->enable_group_booking == 'true' &&($exclude_timeslots[$start->format('H:i:s')] ?? 0) > ($this->slots_per_booking)){
                $start->add($intervalObj);
                continue;
            }
          

            if ($this->enable_group_booking == 'true' && is_array($exclude_timeslots) && !empty($exclude_timeslots)) {
                if (($exclude_timeslots[$start->format('H:i:s')] ?? 0) < ($this->slots_per_booking)) {
                    $timeSlots[] = ['slot' => $timeslot, 'time_display' => $timeslot_display,'remain' => apply_filters('wpb_booking_shortcode_remain_seats',($exclude_timeslots[$start->format('H:i:s')] ?? 0),$start->format('H:i:s'),$this)];
                }
            } else {
                if (($exclude_timeslots[$start->format('H:i:s')] ?? 0) < 1) {
                    $timeSlots[] = ['slot' => $timeslot,'time_display' => $timeslot_display];
                }
            }
            $start->add($intervalObj);
        }
        return $timeSlots;
    }

    public function new_booking(WP_REST_Request $request)
    {
        global $wpdb;
        $args = array(
            'user_id' => get_current_user_id()
        );
        if ($timestamp = strtotime($request->get_param('timeslot') . ' ' . $request->get_param('date'))) {
            // Create a new DateTime object
            $datetime_instance = new DateTime();
            $datetime_instance->setTimestamp($timestamp);
            $current_timeslot = $datetime_instance->getTimestamp();

            $booking_type_instance = new WPB_Booking_Type($request->get_param('bookingTypeId'));

            $datetime_instance->modify("-" . $booking_type_instance->get_duration() - 1 . " minutes");
            $booking_type_start_time = $datetime_instance->getTimestamp();
            $datetime_instance->modify("+" . (($booking_type_instance->get_duration() * 2) - 2) . " minutes");
            $booking_type_end_time = $datetime_instance->getTimestamp();

            $wpb_booking = (new WPB_Booking())->get_bookings(
                array(
                    'paged' => 1,
                    'status' => ['wpb-approved', 'wpb-pending'],
                    'per_page' => 99,
                    'orderby' => 'ID',
                    'booking_type' => [$booking_type_instance->get_id()],
                    'order' => 'DESC',
                    'date' => date('Y-m-d', $timestamp),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    'time_from' => date('H:i:s', $booking_type_start_time),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    'time_to' => date('H:i:s', $booking_type_end_time),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                )
            );

            $already_booking = false;
            if (($booking_type_instance->get_meta('slots_per_booking_number') ?? 0) < count($wpb_booking->results)) {
                wp_send_json(['status' => 'error', 'message' => esc_html__("Already had an booking at this time. Kindly book on a different available time.", 'wpbookit')]);
            }



            switch ($booking_type_instance->get_meta('booking_number_by')) {
                case 'days':
                    if (!empty($booking_type_instance->get_meta('maximum_booking')) && (count($wpb_booking->results) >= $booking_type_instance->get_meta('maximum_booking'))) {
                        $already_booking = true;
                    }
                    break;
                case 'weeks':
                    $startOfWeek = strtotime("last monday", $timestamp);
                    // If the given date is already Monday, adjust the start date to that day
                    if (date('N', $timestamp) == 1) { //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                        $startOfWeek = $timestamp;
                    }

                    $endOfWeek = strtotime("next sunday", $startOfWeek);
                    $endOfWeek = strtotime("23:59:59", $endOfWeek);


                    $wpb_weeks_booking = (new WPB_Booking())->get_bookings(
                        array(
                            'paged' => 1,
                            'per_page' => 99,
                            'order_by' => 'ID',
                            'order' => 'DESC',
                            'status' => ['wpb-approved', 'wpb-pending'],
                            'booking_type' => [$booking_type_instance->get_id()],
                            'date_from' => date('Y-m-d', $endOfWeek), //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                            'date_to' => date('Y-m-d', $startOfWeek),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                        )
                    );


                    if (!empty($booking_type_instance->get_meta('maximum_booking')) && (count($wpb_weeks_booking->results) >= $booking_type_instance->get_meta('maximum_booking'))) {
                        $already_booking = true;
                    }
                    break;
                case 'months':
                    // Get the first day of the month
                    $startOfMonth = strtotime(date("Y-m-01 00:00:00", $timestamp)); //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    $endOfMonth = strtotime(date("Y-m-t 23:59:59", $timestamp));//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    $wpb_month_booking = (new WPB_Booking())->get_bookings(
                        array(
                            'paged' => 1,
                            'per_page' => 99,
                            'order_by' => 'ID',
                            'order' => 'DESC',
                            'status' => ['wpb-approved'],
                            'booking_type' => [$booking_type_instance->get_id()],
                            'date_from' => date('Y-m-d', $endOfMonth), //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                            'date_to' => date('Y-m-d', $startOfMonth),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                        )
                    );



                    if (!empty($booking_type_instance->get_meta('maximum_booking')) && (count($wpb_month_booking->results) >= $booking_type_instance->get_meta('maximum_booking'))) {
                        $already_booking = true;
                    }
                    break;
            }
            if ($already_booking) {
                wp_send_json(['status' => 'error', 'message' => esc_html__("Already had an booking at this time. Kindly book on a different available time.", 'wpbookit')]);
            }

            $default_booking_status = 'wpb-' . wpb_get_general_settings()['booking_status'];

            $is_guest_mode = false;

            if ($booking_type_instance->get_meta('guest_invite') != 'true') {
                $is_guest_mode = (wpb_get_general_settings()['booking_type'] ?? "registered") == 'guest';
            } else {
                $is_guest_mode = true;
            }
            if (is_user_logged_in()) {
                $is_guest_mode = false;
            }
            if (!$is_guest_mode) {
                if (!is_user_logged_in()) {

                    if ($request->get_param('wpb-user-booking-with') == 'wpb-register') {

                        $full_name = sanitize_text_field($request->get_param('wpb_user_name'));
                        $first_name = sanitize_text_field($request->get_param('wpb_user_first_name'));
                        $last_name = sanitize_text_field($request->get_param('wpb_user_last_name'));

                        $email = strtolower(sanitize_email($request->get_param('wpb_user_email')));
                        
                        $password =   sanitize_text_field($request->get_param('wpb_user_password'));
                        $user_data = [
                            "user_login" => $email,
                            "user_email" => strtolower($email),
                            "user_pass" => $password,
                            "first_name" => empty($full_name) ? $first_name : $full_name,
                            "last_name" => $last_name ?? '',
                            "role" => WPBOOKIT()->helpers->get_customer_role(),
                            "user_registered" => date("Y-m-d H:i:s"), //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                        ];

                        $exists = email_exists($email);
                        if ($exists)
                            wp_send_json(['status' => 'error', 'message' => __("That E-mail is registered kindly login", 'wpbookit')]);

                        $user_data = apply_filters(
                            "wpb_insert_customer",
                            $user_data
                        );

                        do_action('wpb_insert_customer', $user_data);

                        $user_id = wp_insert_user($user_data);
                        $wp_user_instance = get_user_by('ID', $user_id);

                        do_action('wpb_customer_registration', $wp_user_instance,$password);

                        if (is_wp_error($user_id)) {
                            wp_send_json(['status' => 'error', 'message' => $user_id->get_error_message()]);
                        }
                    } else {

                        $creds = array();
                        $creds['user_login'] = sanitize_text_field($request->get_param('wpb_login_user_email'));
                        $creds['user_password'] = sanitize_text_field($request->get_param('wpb_login_user_password'));
                        $creds['remember'] = true;
                        $user = wp_signon($creds, false);
                        if (is_wp_error($user)) {
                            wp_send_json(['status' => 'error', 'message' => str_replace('username', 'email', wp_strip_all_tags($user->get_error_message()))]);
                        }
                        $user_id = $user->ID;


                        if (wpb_get_general_settings()['booking_limit'] != 'no-limit') {
                            $current_user_booking = wpb_get_bookings(array(
                                'per_page' => 99,
                                'status' => array('wpb-pending'),
                                'user_id' => $user_id
                            ));
                            if ($current_user_booking->total >= wpb_get_general_settings()['booking_limit']) {
                                wp_send_json(['status' => 'error', 'message' => __("You have reached your booking limit. No more bookings can be made at this time.", 'wpbookit')]);
                            }
                        }
                    }
                } else {
                    $user_id = get_current_user_id();
                    if ($request->has_param('wpb_user_name')) {
                        update_user_meta($user_id, 'first_name', $request->get_param('wpb_user_name') ?? '');
                    }

                    if (wpb_get_general_settings()['booking_limit'] != 'no-limit') {
                        $current_user_booking = wpb_get_bookings(array(
                            'per_page' => 99,
                            'status' => array('wpb-pending'),
                            'user_id' => $user_id
                        ));
                        if ($current_user_booking->total >= wpb_get_general_settings()['booking_limit']) {
                            wp_send_json(['status' => 'error', 'message' => __("You have reached your booking limit. No more bookings can be made at this time.", 'wpbookit')]);
                        }
                    }
                }

                $wp_user_instance = get_user_by('ID', $user_id);
                wp_set_current_user($wp_user_instance->ID, $wp_user_instance->user_login);
                wp_set_auth_cookie($wp_user_instance->ID);
                do_action('wp_login', $wp_user_instance->user_login, $wp_user_instance);
                $booking_data = array(
                    'booking_type_id' => $booking_type_instance->get_id(),
                    'customer_id' => $user_id,
                    'booking_name' => $wp_user_instance->first_name . ' ' . $wp_user_instance->last_name,
                    'booking_email' => $wp_user_instance->user_email,
                    'booking_type' => $booking_type_instance->get_name('view'),
                    'booking_date' => date('Y-m-d', $timestamp),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    'timeslot' => date('H:i:s', $current_timeslot),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    'status' => $default_booking_status,
                    'date_created'      => current_time('mysql', 1)
                );
            } else {
                $booking_data = array(
                    'booking_type_id' => $booking_type_instance->get_id(),
                    'customer_id' => 0,
                    'booking_name' => $request->has_param('wpb_user_name') ? $request->get_param('wpb_user_name') : $request->get_param('wpb_user_first_name') . ' ' . $request->get_param('wpb_user_last_name'),
                    'booking_email' => strtolower($request->get_param('wpb_user_email')),
                    'booking_type' => $booking_type_instance->get_name('view'),
                    'booking_date' => date('Y-m-d', $timestamp),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    'timeslot' => date('H:i:s', $current_timeslot),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                    'status' => $default_booking_status,
                    'date_created'      => current_time('mysql', 1)
                );
            }
            $google_meet_settings = get_option('google_meet_settings');
            $args = array(
                'data'                  => ['wpb_booking_type' => $booking_type_instance->get_id()],
                'customer_info'         => $booking_data['booking_name'],
                'booking_type_name'     => $booking_type_instance->get_name(),
                'formatted_date'        => date('Y-m-d', $timestamp),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                'time_24h'              => date('H:i:s', $current_timeslot),//phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date
                'interval'              => $booking_type_instance->get_duration(),
                'google_meet_status'    => isset($google_meet_settings['google_meet_status']) ? $google_meet_settings['google_meet_status'] : 'off',
                'booking_name'          => $request->has_param('wpb_user_name') ? $request->get_param('wpb_user_name') : $request->get_param('wpb_user_first_name') . ' ' . $request->get_param('wpb_user_last_name'),
                'booking_email'         => strtolower($request->get_param('wpb_user_email')),
                'is_guest_mode'         => $is_guest_mode,
                'phone_number'          => $request->has_param('wpb_user_phone_number') && !empty($request->get_param('wpb_user_phone_number')) ? $request->get_param('wpb_user_phone_number') : '',
                'request_data'          => $request->get_params(),
            );

            // Add Booking Insert Do Actions
            do_action('wpb_before_booking_insert', $args);

            $booking_inserted = $wpdb->insert($wpdb->wpb_bookings, $booking_data);
            if ($booking_inserted != 1) {
                wp_send_json(['status' => 'error', 'message' => esc_html__("Something Went Wrong", 'wpbookit')]);
            }

            $booking_id = $wpdb->insert_id;
            $args['booking_id'] = $booking_id;

            update_metadata('wpb_bookings', (int)$booking_id, 'location', $booking_type_instance->get_meta('location')??'');
            update_metadata('wpb_bookings', (int)$booking_id, 'location_source', $booking_type_instance->get_meta('location_source')??'');
            update_metadata('wpb_bookings', (int)$booking_id, 'staff_id', $booking_type_instance->get_meta('staff')??'0');
            update_metadata('wpb_bookings', (int)$booking_id, 'booking_duration', $booking_type_instance->get_meta('duration')??'0');
            do_action('wpb_after_bookingmodal_booking_duration',$request,$booking_id);

            // Get Booking Total And HTML
            $booking_type_id = $booking_type_instance->get_id();


            if ($request->has_param('wpb-booking_question')) {
                $question_ans = [];
                foreach (json_decode($booking_type_instance->get_meta('questions'), true) as $key => $value) {
                    $value['ans'] = $request->get_param('wpb-booking_question')[$value['questionId']];
                    $question_ans[] = $value;
                }
                update_metadata('wpb_bookings', (int)$booking_id, 'questions_answers', wp_json_encode($question_ans), '');
            }

            // Add Booking Insert Do Actions
            do_action('wpb_after_booking_insert', $args);
            update_metadata('wpb_bookings', (int)$booking_id, 'booking_user_type', wpb_get_general_settings()['booking_type'] ?? '', '');

            $booking_type_price = $booking_type_instance->get_meta('price');
            if ($request->has_param('coupon_code')) {
                $booking_type_price = $booking_type_instance->get_calulated_price();
            }
            if (count(apply_filters('wpb_booking_shortcode_active_payment_gateway', [])) > 0 && $booking_type_price > 0) {
                $payment_responce = apply_filters('wpb_booking_' . $request->get_param('wpb_payments_gateways'), ['status' => 'error', 'message' => esc_html__("SomeThing Went Wrong", 'wpbookit')], $booking_type_instance, new WPB_Booking($booking_id), $payment_id);
            } else if ($default_booking_status === 'wpb-approved') {
                do_action('wpb_customer_booking_approval', (int) $booking_id);
                do_action('wpb_customer_booking_confirmation', (int) $booking_id);
                wpb_booking_reminder($booking_id);
            } else if ($default_booking_status === 'wpb-pending') {
                do_action('wpb_customer_booking_confirmation', (int) $booking_id);
                do_action('wpb_staff_booking_request', (int) $booking_id);
            }

            $booking_redirect_url = wpb_iq_get_booking_redirect_url(new WPB_Booking($booking_id), $booking_type_instance);

            if (isset($payment_responce)) {
                wp_send_json($payment_responce);
            }

            wp_send_json(['status' => 'success', 'data' => ['booking_id' => $booking_id, 'redirect_url' => $booking_redirect_url], 'message' => esc_html__("Your reservation has been successfully confirmed!", 'wpbookit')]);
        }
    }

    function wpb_get_bookings_count($args)
    {
        global $wpdb;

        $user_id = isset($args['user_id']) ? $args['user_id'] : '';
        if (empty($user_id)) {
            return true;
        }

        $table_name = $wpdb->wpb_bookings;

        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE customer_id  = %d AND status = %s",
            $user_id,
            'wpb-approved'
        );

        $count = $wpdb->get_var($query);  //phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared 

        return $count;
    }
    public  function cancle_booking_appointment(WP_REST_Request $request)
    {
        global $wpdb;
        $is_guest_mode = false;
        $booking_instance = new WPB_Booking((int)$request->get_param('id')) ;
        $booking_type_instance = $booking_instance->get_booking_type('view',['name','id'],true);
        $cancellation_time_for_booking = !empty(wpb_get_general_settings()['minimum_time_before_cancellation']) ? wpb_get_general_settings()['minimum_time_before_cancellation'] : '';

		if($booking_type_instance['meta']['guest_invite']!='true'){
			$is_guest_mode =wpb_get_general_settings()['booking_type'] ?? "registered"=='guest';
		}else{
			$is_guest_mode= true;
		}
        
        if (!$booking_instance->get_minimum_time_before_cancellation()) {
            wp_send_json(array(
                'status'  => 'error',
                'message' => sprintf(
                    // translators: Minutes placeholder:0
                    __('Cancellation is Not Allowed Within %d Minutes of The Reservation Time.', 'wpbookit'),
                    $cancellation_time_for_booking
                ),
            ));
        }
        
        if(!$is_guest_mode){
            if(!is_user_logged_in()){
                wp_send_json(['status'=>'success','message' => esc_html__("Something Went Wrong",'wpbookit') ]);
            }
            $update_status = $wpdb->update($wpdb->wpb_bookings, ['status' => 'wpb-cancelled'], ['id' => (int)$request->get_param('id'), 'customer_id' =>  get_current_user_id()]);

            if ($update_status == false) {
                wp_send_json(['status' => 'success', 'message' => esc_html__("Something Went Wrong", 'wpbookit')]);
            }

            // cancel customer email hook
            do_action('wpb_customer_cancel_booking', (int) $request->get_param('id'));

            // cancel staff email hook
            do_action('wpb_staff_booking_cancellation', (int) $request->get_param('id'));

            wp_send_json(['status' => 'success', 'message' => esc_html__("Your Reservations Cancel Successfully", 'wpbookit')]);
        }


        $update_status = $wpdb->update($wpdb->wpb_bookings, ['status' => 'wpb-cancelled'], ['id' => (int)$request->get_param('id')]);


        if ($update_status == false) {
            wp_send_json(['status' => 'success', 'message' => esc_html__("Something Went Wrong", 'wpbookit')]);
        }
        // cancel customer email hook
        do_action('wpb_customer_cancel_booking', (int) $request->get_param('id'));

        // cancel staff email hook
        do_action('wpb_staff_booking_cancellation', (int) $request->get_param('id'));

        wp_send_json(['status' => 'success', 'message' => esc_html__("Your Reservations Cancel Successfully", 'wpbookit')]);
    }
}
