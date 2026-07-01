<?php

function wpb_get_email($email_id = false)
{
    global $wpdb;

    $sql = "SELECT * FROM $wpdb->wpb_booking_emails";
    $parameters = array();

    if ($email_id !== false) {
        $sql .= " WHERE id = %d";
        $parameters[] = $email_id;
    }

    if (!empty($parameters)) {
        $prepared_sql = $wpdb->prepare($sql, $parameters); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    } else {
        $prepared_sql = $sql;
    }

    $results = $wpdb->get_results($prepared_sql, ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

    return $results;
}

function wpb_update_email($args)
{
    global $wpdb;

    // Extracting arguments
    $email_id = (int) $args['email_id'];
    $update_data = array();

    if (isset($args['status'])) {
        $update_data['status'] = $args['status'];
    }
    if (!empty($args['recipient'])) {
        $update_data['recipient'] = sanitize_text_field($args['recipient']);
    }
    if (!empty($args['subject'])) {
        $update_data['subject'] = sanitize_text_field($args['subject']);
    }
    if (!empty($args['heading'])) {
        $update_data['emails_heading'] = sanitize_text_field($args['heading']);
    }
    if (!empty($args['type'])) {
        $update_data['type'] = sanitize_text_field($args['type']);
    }
    if (!empty($args['reminder'])) {
        $update_data['reminder'] = sanitize_text_field($args['reminder']);
    }
    if (!empty($args['content'])) {
        $update_data['emails_content'] = wp_kses_post($args['content']);
    }

    $table_name = $wpdb->wpb_booking_emails;

    // Where condition
    $where = array(
        'id' => $email_id,
    );

    // Run the update query
    $result = $wpdb->update($table_name, $update_data, $where);

    return $result !== false; // Return true if update succeeded, false otherwise
}
function wpb_add_email($args) {
    global $wpdb;

    // Prepare the SQL statement
    $sql = "INSERT INTO {$wpdb->wpb_booking_emails} 
            (status, emails_title, emails_heading, emails_content, emails_subject, is_reminder, reminder, role) 
            VALUES 
            (%d, %s, %s, %s, %s, %s, %d, %s)";

    // Prepare data for insertion
    foreach ($args as $arg) {
        $wpdb->query(
            $wpdb->prepare(
                $sql,  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                intval($arg['status']),
                sanitize_text_field($arg['emails_title']),
                sanitize_text_field($arg['emails_heading']),
                wp_kses_post($arg['emails_content']),
                sanitize_text_field($arg['emails_subject']),
                intval($arg['is_reminder']),
                sanitize_text_field($arg['reminder']),
                sanitize_text_field($arg['role'])
            )
        );
    }

    // Check if there were any errors
    if ($wpdb->last_error) {
        // Handle error
        return false;
    }

    // Data inserted successfully
    return true;
}
