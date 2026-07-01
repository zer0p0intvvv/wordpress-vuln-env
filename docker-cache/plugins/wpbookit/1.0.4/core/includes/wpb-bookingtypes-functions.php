<?php 

/**
 * Get all Booking statuses.
 * @return array
 **/

function wpb_get_booking_type_statuses() {
    return apply_filters(
        'wpb_booking_type_statuses',
        array(
            'wpb-pending'   => _x( 'Pending', 'Booking status', 'wpbookit' ),
            'wpb-enable'    => _x( 'Enabled', 'Booking status', 'wpbookit' ),
            'wpb-disable'   => _x( 'Disable', 'Booking status', 'wpbookit' ),
        )
    );
}

function get_bookingtype_base_url( $context = 'view' ) {
    $general_setting = wpb_get_general_settings();
    $base_url = $general_setting['permalink_strcture']??'booking/';
    $permalink_structure = isset($base_url) ? $base_url : 'booking';
    
    // Ensure permalink structure starts with a slash
    if (substr($permalink_structure, 0, 1) !== '/') {
        $permalink_structure = '/' . $permalink_structure;
    }

    // Ensure permalink structure ends with a slash
    if (substr($permalink_structure, -1) !== '/') {
        $permalink_structure .= '/';
    }
    // Ensure the full URL ends correctly with the slug
    $permalink = site_url() . $permalink_structure ;

    return $permalink;
}


function wpb_get_booking_type( $booking_id, $fields = [], $meta = false ) {
    global $wpdb;
    
    $query = "SELECT ";
    
    // Add selected fields or default to 'name'
    if (!empty($fields)) {
        $fields_str = implode(', ', array_map(function($field) use ($wpdb) {
            return $wpdb->prepare('%1s', $field); 
        }, $fields));
        $query .= $fields_str;
    } else {
        $query .= "name"; 
    }
    
    $query .= " FROM {$wpdb->prefix}wpb_booking_type";
    if( is_int( $booking_id ) ) :
        $query .= $wpdb->prepare(" WHERE {$wpdb->wpb_booking_type}.id = %d", $booking_id);
    else :
        $query .= $wpdb->prepare(" WHERE {$wpdb->wpb_booking_type}.slug = %s", $booking_id);
    endif;  
    $result = $wpdb->get_row($query, ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

    if(!empty($result["id"]) && $meta){
        $meta_data = wpb_get_booking_types_meta_data($result["id"]);
        $meta_restructured = [];
        
        foreach ($meta_data as $row) {

            if ($row["meta_key"] == "cover_image_id") {
                $attachment_url = wp_get_attachment_url($row['meta_value']);
                if ($attachment_url) {
                    $meta_restructured["cover_image_url"] = $attachment_url;
                }
            }            

            if($row['meta_key']=='questions'){
                $row['meta_value']= json_decode($row['meta_value'],true);
            }
            $meta_restructured[$row['meta_key']] = $row['meta_value'];
        }
        $result['meta'] = $meta_restructured;
    }
    return $result;
}

/**
 * Main function for returning booking, uses the WPB_Booking_Type class.
 * @return bool|WPB_Booking_Type
 */
function wpb_get_all_booking_types( $args = array(), $defaults = array() ) {
    // Default arguments
    $defaults = array(
        'paged'     => 1,
        'per_page'  => 10,
        'status'    => [],
        'staff'     => 0,
        'charges'   => 'all'
    );
    $args = wp_parse_args($args, $defaults);

    $current_user = wp_get_current_user();

    // Calculate offset
    $args['offset']= ($args['paged'] - 1) * $args['per_page'];

    // Query for booking types with pagination
    $booking_types = ( new WPB_Booking_Type )->get_booking_types( $args );
    
    return $booking_types;
}

function wpb_get_total_booking_types() {
    global $wpdb;
    $staff = current_user_can('administrator') ? 0 : get_current_user_id();
    
    $query = "SELECT COUNT(*) 
        FROM {$wpdb->wpb_booking_type}
        LEFT JOIN {$wpdb->wpb_booking_typemeta} 
        ON {$wpdb->wpb_booking_typemeta}.wpb_booking_type_id = {$wpdb->wpb_booking_type}.id 
        AND {$wpdb->wpb_booking_typemeta}.meta_key = 'staff'
        WHERE 1 = 1";
    
    if( $staff ) {
        $query .= $wpdb->prepare(
            " AND {$wpdb->wpb_booking_typemeta}.meta_value = %d",
            $staff
        );
    }

    $total_booking_types = $wpdb->get_var($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    return $total_booking_types;
}



function wpb_get_booking_types_meta_data($id) {
    global $wpdb;
    $wpb_booking_typemeta_table = $wpdb->prefix . 'wpb_booking_typemeta';
    $original_meta_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpb_booking_typemeta_table WHERE wpb_booking_type_id = %d", $id), ARRAY_A);
    return $original_meta_data;
}

