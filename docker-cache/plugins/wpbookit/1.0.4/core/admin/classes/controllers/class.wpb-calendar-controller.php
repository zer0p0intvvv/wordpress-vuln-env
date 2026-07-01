<?php

final class WPB_Calendar_Controller
{
    private  $slots_per_booking;

    public function get_calendar_booking(WP_REST_Request $request)
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
            'order_by'      => 'booking_date',
            'offset'        => 0,
            'staff'         => false
        );

        

        if ($request->has_param('advanceFilter')) {
            $advanceFilter = $request->get_param('advanceFilter');
        
            // Parse wpb_booking_daterange if present
            if (isset($advanceFilter['wpb_booking_daterange'])) {
                $dateRange = explode(' to ', $advanceFilter['wpb_booking_daterange']);
                if (count($dateRange) == 2) { 
                    $args['date_from'] = date('Y-m-d', strtotime($dateRange[0]));  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
                    $args['date_to'] = date('Y-m-d', strtotime($dateRange[1])); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
                }
            }

            // Set date_from and date_to from advanceFilter if available
            $args['date_from'] = $args['date_from'] ?: $advanceFilter['date_from'];
            $args['date_to'] = $args['date_to'] ?: $advanceFilter['date_to'];

            $args = array_merge($args, [
                'status'        => $advanceFilter['wpb_status'],
                'booking_type'  => $advanceFilter['wpb_booking_type'],
            ]);
           
        }
        if ($request->has_param('customer_search')) {
            $args['booking_name'] = $request->get_param('customer_search');
        }

        if ($request->has_param('order')) {
            $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');
            $args['order_by'] = $request->get_param('order')[0]['name'] ?? "";
        }
        if ($request->has_param('booking_type')) {
            $args['booking_type'] = $request->get_param('booking_type');
        }
      
        $booking = wpb_get_bookings($args);
        $booking = (array) $booking;
        $data = array_map(function ($item) {
        
            $user_id = 0;
            if ($user = get_user_by('email', $item->get_booking_email())) {
                $user_id = $user->ID;
            }

            global $wpdb;
            $booking_query = $wpdb->prepare("SELECT booking_type FROM $wpdb->wpb_bookings WHERE id = %d", $item->get_id() );
            $booking_type_title = $wpdb->get_var($booking_query); // phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared 

            $star_time= $item->get_timeslot();
            $booking_type = $item->get_booking_type('view',['name','id',['duration']],true);
            $booking = $booking_type ? $booking_type['duration'] : $item->get_meta('booking_duration') ?? 0; // $booking_type['duration'];
            $end_time= strtotime("+ {$booking} Min",strtotime($star_time));
            
            return [
                'id' => $item->get_id(),
                'name' => $item->get_booking_name(),
                'payment_id' => $item->get_payment_id(),
                'gender'    => empty(get_user_meta($user_id, "gender", true)) ?  '-' : get_user_meta($user_id, "gender", true),
                'dob'       => empty(get_user_meta($user_id, "date_of_birth", true)) ? '-' : wpb_get_formated_date_time(get_user_meta($user_id, "date_of_birth", true), ''),
                'profile_img' => get_avatar_url($user_id, ['size' => 50]),
                'duration' => sprintf("%s %s", $item->get_booking_type('view', ['duration']) ? $item->get_booking_type('view', ['duration'])['duration'] : $item->get_meta('booking_duration') ?? 0, esc_html__("Min", 'wpbookit')),
                'email' => $item->get_booking_email(),
                'datetime' => $item->get_formated_booking_datetime(),
                'list_view_date'    => date_format( date_create( $item->get_booking_date() ), 'F d,Y' ),
                'dayname'    => date_format( date_create( $item->get_booking_date() ), 'l' ),
                'date' => $item->get_booking_date(),
                'start_time' => $item->get_timeslot(),
                'end_time' =>date("H:i:s",$end_time),  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date  
                'type' => $booking_type_title,
                'price' => $item->get_booking_price(),
                'date_created' => sprintf("%s %s",wp_date(get_option('date_format'), strtotime($item->get_date_created() ) ) , wp_date(get_option('time_format'), strtotime($item->get_date_created() ) ) , new DateTimeZone( wpb_get_timezone())),
                'location' => $item->get_meta('location'),
                'location_source' => $item->get_meta('location_source') ?? $item->get_meta('meeting_link'),
                'status' => [
                    'key' => $item->get_status(),
                    'label' => wpb_booking_status_label($item->get_status())
                ],
                "questions_answers"=>json_decode($item->get_meta('questions_answers')??'{}'),
            ];
            
        }, $booking['results'] ?? []);

        wp_send_json(array(
            "recordsTotal"      => $booking['total'] ?? 0,
            "recordsFiltered"   => $booking['total'] ?? 0,
            "data"              => $data
        ));
        
    }
}