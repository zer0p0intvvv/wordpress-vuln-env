<?php
final class WPB_Home_Controller
{
    public function get_dashboard_apt_revenue_date(WP_REST_Request $request)
    {
        // Get start and end dates from the request, default to today's date if not provided
        $start_date = $request->get_param('start') ?: date('Y-m-d');  // phpcs:ignore   WordPress.DateTime.RestrictedFunctions.date_date  
        $end_date = $request->get_param('end') ?: date('Y-m-d'); // phpcs:ignore   WordPress.DateTime.RestrictedFunctions.date_date  

        // Apply filters to the date data
        $location_id = apply_filters('wpb_after_start_end_dashboard_dates', $request);
        
        // Create date objects for formatting (optional if further manipulation is needed)
        $start_date = new DateTime( $start_date );
        $end_date   = new DateTime( $end_date );

        $start_date_format = $start_date->format('Y-m-d'); 
        $end_date_format   = $end_date->format('Y-m-d');

        // Instantiate settings and customer objects
        $settings = new WPB_Settings_Dashboard();
        $customer = new WPB_Settings_Customer();

        // Fetch chart data, revenue, and customer count
        $chart_data = $settings->get_chart_content($start_date_format, $end_date_format, $location_id);
        $chart_revenue = $settings->get_chart_revenue($start_date_format, $end_date_format, $location_id);
        $customer_count = $customer->get_customers_count($start_date_format, $end_date_format);

        // Prepare bookings and revenue data for the response
        $booking_counts = !empty($chart_data->booking_counts) ? explode(",", $chart_data->booking_counts) : [];
        $args=[];
       
        $total_booking = (wpb_get_bookings($args))->total;
        $total_revenue = isset( $chart_data->total_revenues ) ? array_sum(explode(",", $chart_data->total_revenues)) : 0; 

        // Send the response
        wp_send_json_success(
            apply_filters(
                'wpb_dashboard_analytics_data',
                array(
                    'bookings_revenue_chart' => [
                        [
                            'name'      => esc_html__('Revenue', 'wpbookit'),
                            'type'      => 'column',
                            'data'      => !empty($chart_revenue) ? $chart_revenue : []
                        ],
                        [
                            'name'      => esc_html__('Bookings', 'wpbookit'),
                            'type'      => 'line',
                            'data'      => $booking_counts
                        ],
                    ],
                    'bookings_revenue_range' => [
                        'labels'        => $chart_data->formatted_dates,
                    ],
                    'dashboard_fragments' => [
                        '#wpb-total-booking' => $total_booking,
                        '#wpb-total-revenue' => wpb_get_prefix_postfix_price(number_format($total_revenue,2,'.','')),
                        '#wpb-total-customer' => $customer_count,
                    ],
                ),
                $start_date,
                $end_date
            )
        );
    }


}
