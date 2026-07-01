<?php 

/**
 * Main function for returning guest, uses the WPB_Guest_User class.
 * @return bool|WPB_Guest_User
 */
function wpb_get_guest_users($args = array()) {  
    // Default arguments
    $defaults = array(
        'offset'        => 0,
        'paged'         => 1,
        'per_page'      => 10,
        'order'         => 'ASC',
        'order_by'      => 'id',
        'guest_name'    => '',
    );
    $args = wp_parse_args($args, $defaults);
    return (new WPB_Guest_User)->get_guest_users( $args );
}

/**
 * Main function for returning guest, uses the WPB_Guest_User class.
 * @return bool|WPB_Guest_User
 */
function wpb_get_guest_user($guest = false,$get_by_col=false) {
    if (!$guest)
        return $guest;
    return (new WPB_Guest_User)->get_guest_user($guest,$get_by_col);
}
function wpb_delete_guest_user($guest = false) {
    // Ensure the guest ID is provided
    if (!$guest) return false;    
    global $wpdb;
    // Prepare and execute the delete query
    $result = $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->wpb_guest_users} WHERE id = %d",
            $guest
        )
    );

    // Check if the query was successful
    if ($result === false)  return false;
    return true;
}


function wpb_create_guest_user( $args ){
    global $wpdb;
    $is_guest_mode  = $args['is_guest_mode'];
    $booking_name   = $args['booking_name'];
    $booking_email  = $args['booking_email'];
    $phone_number  = $args['phone_number'];

    if( ! is_user_logged_in() && $is_guest_mode ) :
        $result = $wpdb->insert(
            "{$wpdb->wpb_guest_users}",
            array(
                'guest_name'  => $booking_name,
                'guest_email' => $booking_email,
                'guest_phone_number' => $phone_number,
            ),
            array(
                '%s',   // Value type: string
                '%s',    // Value type: string
                '%s'    // Value type: string
            )
        );
    endif;
}
add_action( 'wpb_before_booking_insert', 'wpb_create_guest_user', 10, 1 );