<?php
final class WPB_Exportcsv_Controller
{ 
    
    public function export_user_table_callback(WP_REST_Request $request) {

        $tab  = $request->get_param('tab');
        if ( isset($tab) ) {

            
            $user_role = sanitize_text_field( $tab );
    
            // WP_User_Query arguments
            $args = array (
                'role' => $user_role,
                'order' => 'ASC',
                'orderby' => 'display_name',
                'fields'  => 'all',
            );
            
            $get_users = get_users( $args );
    
            $csv_file = "users" . date('YmdHis') . ".csv"; //phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
    
            // Set CSV headers
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$csv_file}");
    
            // Open output stream
            $output = fopen('php://output', 'w');
    
            // Write CSV headers
            fputcsv($output, array('First Name', 'Last Name', 'Email', 'Role','Phone','Notes','Profile Image','Gender','Date of Birth'));
    
            // Write user data to CSV
            foreach ( $get_users as $user ) {
                $meta = get_user_meta($user->ID);
                $role = $user->roles;
                $email = $user->user_email;
                $phone = $meta['phone'];
                $custom_note = $meta['custom_note'];
                $avtar_attachment_id = $meta['wp_user_avatar'][0];
                $attachment_url = wp_get_attachment_url($avtar_attachment_id);
                
                
                $attachment_id = $meta['wp_user_avatar'][0] ?? '';
                $attachment_url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';

                $first_name = ( isset($meta['first_name'][0]) && $meta['first_name'][0] != '' ) ? $meta['first_name'][0] : '' ;
                $last_name  = ( isset($meta['last_name'][0]) && $meta['last_name'][0] != '' ) ? $meta['last_name'][0] : '' ;
                $phone  = ( isset($meta['phone'][0]) && $meta['phone'][0] != '' ) ? $meta['phone'][0] : '' ;
                $dialCode  = ( isset($meta['dialCode'][0]) && $meta['dialCode'][0] != '' ) ? $meta['dialCode'][0] : '' ;
                $custom_note  = ( isset($meta['custom_note'][0]) && $meta['custom_note'][0] != '' ) ? $meta['custom_note'][0] : '' ;

                $gender  = ( isset($meta['gender'][0]) && $meta['gender'][0] != '' ) ? $meta['gender'][0] : '' ;
                $date_of_birth  = ( isset($meta['date_of_birth'][0]) && $meta['date_of_birth'][0] != '' ) ? wpb_get_formated_date_time($meta['date_of_birth'][0], '') : '' ;
               
               

    
                fputcsv($output, array($first_name, $last_name, $email, ucfirst($role[0]), $dialCode.$phone ,$custom_note,$attachment_url,$gender,$date_of_birth));
            }
    
            // Close output stream
            fclose($output); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose  
    
            // Exit to prevent WordPress from adding extra content
            exit;
        } else {
            wp_send_json_error( 'Tab parameter missing' );
        }
    }
    
    public function export_payment_table_callback(WP_REST_Request $request)
    {
        global $wpdb;
       
            $query = $wpdb->prepare(
                "SELECT payments.*, bookings.*
                FROM %i AS payments 
                INNER JOIN %i AS bookings ON payments.bookings_id = bookings.id 
                WHERE 1 = 1",
                $wpdb->wpb_payments,
                $wpdb->wpb_bookings
            );
            $payments = $wpdb->get_results($query, ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
            $csv_file = "payments" . date('YmdHis') . ".csv"; //phpcs:ignore   WordPress.DateTime.RestrictedFunctions.date_date 
    
            // Set CSV headers
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$csv_file}");
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Payment ID','Booking Type','Booking name', 'Booking Email' ,'Booking Date', 'Mode Of Payment','Total Amount','Paid Amount','Payment Status'));

            foreach ( $payments as $payment ) {

                $payment_id = ( isset($payment['id']) && $payment['id'] != '' ) ? $payment['id'] : '' ;
                $booking_type = ( isset($payment['booking_type_id']) && $payment['booking_type_id'] != '' ) ? $payment['booking_type_id'] : '' ;
                $booking_name = ( isset($payment['booking_name']) && $payment['booking_name'] != '' ) ? $payment['booking_name'] : '' ;
                $booking_email = ( isset($payment['booking_email']) && $payment['booking_email'] != '' ) ? $payment['booking_email'] : '' ;
                $booking_date = ( isset($payment['booking_date']) && $payment['booking_date'] != '' ) ? $payment['booking_date'] : '' ;
                $payment_mode = ( isset($payment['payment_mode']) && $payment['payment_mode'] != '' ) ? $payment['payment_mode'] : '' ;
                $total_amount = ( isset($payment['total_amount']) && $payment['total_amount'] != '' ) ?wpb_get_prefix_postfix_price( $payment['total_amount']) : '' ;
                $paid_amount = ( isset($payment['paid_amount']) && $payment['paid_amount'] != '' ) ? wpb_get_prefix_postfix_price($payment['paid_amount']) : '' ;
                $payment_status = ( isset($payment['payment_status']) && $payment['payment_status'] != '' ) ? ($payment['payment_status']?__("paid",'wpbookit'):__("Unpaid",'wpbookit')) : '' ;
              
              
                fputcsv($output, array($payment_id, $booking_type,$booking_name,$booking_email,$booking_date,$payment_mode,$total_amount,$paid_amount,$payment_status));
            }
            fclose($output); //phpcs:ignore  WordPress.WP.AlternativeFunctions.file_system_operations_fclose 
            exit;

    }
    public function export_guest_user_table_callback(WP_REST_Request $request)
    {
        $guest_users = wpb_get_guest_users( array( 'per_page'  => -1 ));
        $csv_file = "guest-users" . date('YmdHis') . ".csv"; //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date 
            // Set CSV headers
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$csv_file}");
    
            // Open output stream
            $output = fopen('php://output', 'w');
    
            // Write CSV headers
            fputcsv($output, array('ID', 'Name', 'Email','Phone Number' ) );

            // // Write user data to CSV
            if( !empty( $guest_users->results ) ) :
                foreach ( $guest_users->results as $user ) :
                    $guest_id   = $user->get_id();
                    $name       = $user->get_guest_name();
                    $email      = $user->get_guest_email();
                    $phone_number      = $user->get_guest_phone_number();

                    fputcsv( $output, array( $guest_id, $name, $email,$phone_number ) );
                endforeach;
            endif;
    
            // Close output stream
            fclose($output); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose  
    
            // Exit to prevent WordPress from adding extra content
            exit;
    }
}