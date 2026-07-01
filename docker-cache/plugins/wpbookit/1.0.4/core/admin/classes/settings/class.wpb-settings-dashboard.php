<?php

/**
 * WPB Admin Settings Design Class
 */

if (!class_exists('WPB_Settings_Dashboard', false)):

    /**
     * WPB_Settings_Dashboard Class.
     */
    class WPB_Settings_Dashboard extends WPB_Setting_Page
    {
        public $date_from;
        public $date_to;

        function __construct()
        {
            $this->id           = 'dashboard';
            $this->label        = esc_html__('Dashboard', 'wpbookit');
            $this->icon         = IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/default-icon.svg';
            $this->type         = esc_html__('MAIN', 'wpbookit');
            $this->priority     = 10;

            $this->date_from = apply_filters('wpb_get_dashboard_filter_date_from', date('Y-m-d'));  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
            $this->date_to = apply_filters('wpb_get_dashboard_filter_date_to', date('Y-m-d', strtotime('+1 Month'))); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
            parent::__construct();
        }

        public function get_settings_html()
        {
            $wpb_settings = get_option('wpb_general_setting_data');
            $wpb_prefix = isset($wpb_settings['prefix']) ? $wpb_settings['prefix'] : '';

            $chart_data     = $this->get_chart_content($this->date_from, $this->date_to);
            $chart_revenue  = $this->get_chart_revenue($this->date_from, $this->date_to);

            $pending_data     = $this->wpb_get_pending_booking();
            $upcoming_data     = $this->wpb_get_upcoming_booking();

            $dashboard_chart_option = array(
                'series' => array(
                    array(
                        'name' => __('Bookings', 'wpbookit'),
                        'type' => 'line',
                        'data' => !empty($chart_data->booking_counts) ? explode(",", $chart_data->booking_counts) : [],
                    )
                ),
                'chart' => array(
                    'height' => '350',
                    'width' => '100%',
                    'type' => 'line',
                    'toolbar' => array('show' => false)
                ),
                'colors' => array( "#EF476F"),
                'stroke' => array('width' => array( 4)),
               
                'labels' => !empty($chart_data->formatted_dates) ? $chart_data->formatted_dates : [],
                'xaxis' => array('type' => 'date'),
                'responsive' => array(
                    array(
                        'breakpoint' => 500,
                        'options' => array(
                            'xaxis' => array('labels' => array('offsetY' => -35, 'offsetX' => 10)),
                            'legend' => array('offsetY' => '5')
                        )
                    )
                )
            );

            $booking_args = array('date_from' => $this->date_from, 'date_to' => $this->date_to);

            $current_user = wp_get_current_user();
            if (in_array(WPBOOKIT()->helpers->get_staff_role(), (array) $current_user->roles)) {
                $booking_args['staff'] = get_current_user_id();
            }

            // Prepare bookings and revenue data for the response
            $booking_counts = !empty($chart_data->booking_counts) ? explode(",", $chart_data->booking_counts) : [];

            $total_booking = (wpb_get_bookings($booking_args))->total;

            $revenue         = isset($chart_revenue) ? wpb_get_prefix_postfix_price(number_format(array_sum($chart_revenue),2,'.','')) :wpb_get_prefix_postfix_price(0);
            $customer         = new WPB_Settings_Customer();
            $customerCount  = $customer->get_customers_count($this->date_from, $this->date_to);

            include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-dashboard.php';
        }

        private function get_chart_query($date_from, $date_to, $user_id, $is_admin, $location_id = false) {
            global $wpdb;
            
            // Condition for non-admin users to filter by user ID
            $condition = $is_admin ? '' : $wpdb->prepare("AND btm.meta_value = %d", $user_id);
        
            // Apply filter condition based on location_id
            $filter = apply_filters('wpb_after_condition_isadmin_chart_query', $location_id);
            $filter_join = isset($filter['join']) ? $filter['join'] : '';
            $filter_where = isset($filter['where']) ? $filter['where'] : '';
                    
            // Prepare the query
            $query = $wpdb->prepare(
                "SELECT
                    GROUP_CONCAT(booking_count) AS booking_counts,
                    GROUP_CONCAT(total_revenue) AS total_revenues,
                    GROUP_CONCAT(formatted_date) AS formatted_dates
                FROM (
                    SELECT
                        b.booking_date AS formatted_date,
                        COUNT(b.id) AS booking_count,
                        SUM(CASE WHEN p.bookings_id = b.id THEN p.paid_amount ELSE 0 END) AS total_revenue
                    FROM {$wpdb->prefix}wpb_bookings b
                    LEFT JOIN {$wpdb->prefix}wpb_payments p ON b.id = p.bookings_id
                    LEFT JOIN {$wpdb->prefix}wpb_booking_type bt ON b.booking_type_id = bt.id
                    LEFT JOIN {$wpdb->prefix}wpb_booking_typemeta btm ON b.booking_type_id = btm.wpb_booking_type_id AND btm.meta_key = 'staff'
                    {$filter_join}
                    WHERE (p.payment_status = '1' OR b.id IN ( 
                        SELECT id
                        FROM {$wpdb->prefix}wpb_bookings b
                        JOIN {$wpdb->prefix}wpb_booking_typemeta mt ON b.booking_type_id = mt.wpb_booking_type_id
                        WHERE b.booking_type_id = wpb_booking_type_id
                        AND mt.meta_key = 'price'
                       
                    ))
                    AND b.booking_date BETWEEN %s AND %s {$filter_where}   {$condition}
                    GROUP BY b.booking_date
                ) AS subquery",
                $date_from, 
                $date_to
            );
            return $query;
        }

        private function get_chart_revenue_query($date_from, $date_to, $user_id, $is_admin,$location_id = false)
        {
            global $wpdb;


            $filter = apply_filters('wpb_before_condition_isadmin_chart_revenue_query_filter',$location_id);
            $filter_join = isset($filter['join']) ? $filter['join'] : '';
            $filter_where = isset($filter['where']) ? $filter['where'] : '';
            // Prepare the condition part of the query
            $condition = $is_admin ? '' : $wpdb->prepare("AND btm.meta_value LIKE '%d'", $user_id); // phpcs:ignore  WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery 

            // Complete query with placeholders
            $query = $wpdb->prepare(
                "SELECT
                    SUM(CASE WHEN p.bookings_id = b.id THEN p.paid_amount ELSE 0 END) AS total_revenues
                FROM {$wpdb->wpb_bookings} b
                LEFT JOIN {$wpdb->wpb_payments} p ON b.id = p.bookings_id
                LEFT JOIN {$wpdb->wpb_booking_type} bt ON b.booking_type_id = bt.id
                LEFT JOIN {$wpdb->wpb_booking_typemeta} btm ON b.booking_type_id = btm.wpb_booking_type_id AND btm.meta_key = 'staff'
                {$filter_join}
                WHERE p.payment_status='1' AND b.booking_date BETWEEN %s AND %s {$condition} {$filter_where}
                GROUP BY b.booking_date",
                $date_from,
                $date_to
            );
            return $query;
        }


        public function get_chart_content($date_from, $date_to,$location_id = false)
        {
            global $wpdb;
            $user_id        = get_current_user_id();
            $is_admin = !in_array(WPBOOKIT()->helpers->get_staff_role(),wp_get_current_user()->roles);
            $query          = $this->get_chart_query($date_from, $date_to, $user_id, $is_admin, $location_id );
            $result         = $wpdb->get_row($query); // phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared 

            if ($result) {
                $dates = explode( ',', $result->formatted_dates ?? '' );
                $result->formatted_dates = array_map(function ($date) {
                    return wpb_get_formated_date_time($date, '');
                }, $dates);
            }

            return $result;
        }

        public function get_chart_revenue($date_from, $date_to,$location_id = false)
        {
            global $wpdb;
            $user_id = get_current_user_id();
            $is_admin = !in_array(WPBOOKIT()->helpers->get_staff_role(),wp_get_current_user()->roles);
            $query = $this->get_chart_revenue_query($date_from, $date_to, $user_id, $is_admin,$location_id);

            return $wpdb->get_col($query); // phpcs:ignore  WordPress.DB.PreparedSQL.NotPrepared 
        }


        public function wpb_get_pending_booking()
        {
            return wpb_get_bookings(array(
                'per_page' => 5,
                'status' => array('wpb-pending'),
                'staff' => current_user_can('administrator') ? 0 : get_current_user_id()
            ));
        }

        public function wpb_get_completed_booking()
        {
            return wpb_get_bookings(array(
                'per_page'     => 4,
                'status'     => array('wpb-approved'),
                'date_from' => '',
                'date_to'   => date('Y-m-d'), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
                'staff'     => current_user_can('administrator') ? 0 : get_current_user_id(),
                'is_paid'   => false
            ));
        }

        public function wpb_get_upcoming_booking()
        {
            $today = date('Y-m-d'); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
            return wpb_get_bookings(array(
                'per_page'  => 4,
                'status' => array('wpb-approved'),
                'staff'     => current_user_can('administrator') ? 0 : get_current_user_id(),
                'date_from' => $today,
                'date_to'   => '',
                'order'     => '',
                'order_by'  => 'id',
                'is_paid'   => false
            ));
        }
    }

    return new WPB_Settings_Dashboard();
endif;
