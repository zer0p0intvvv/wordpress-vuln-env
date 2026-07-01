<?php 

function wpb_get_customers() {

    // Define the user role you want to query
    $role = WPBOOKIT()->helpers->get_customer_role();

    // Create user query arguments
    $args = array(
        'role'    => $role,
        'orderby' => 'user_registered',
        'order'   => 'ASC',
    );

    // Create a new WP_User_Query object
    $user_query = new WP_User_Query( $args );

    // Get the results
    $users = $user_query->get_results();

    // Initialize an empty array to store the formatted result
    $customer_list = array();
    
    // Loop through the booking types and format them as required
    foreach ($users as $user) {
        $customer_list[$user->ID] = $user->display_name;
    }

    // Loop through each user
    foreach ($users as $user) {
        $customer_list[$user->ID] = $user->display_name;
    }

    return apply_filters( 'wpb_customer_list', $customer_list );
}

 