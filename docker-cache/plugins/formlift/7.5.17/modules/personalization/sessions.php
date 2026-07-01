<?php

define( 'FORMLIFT_SESSIONS_TABLE', 'formlift_sessions' );
define( 'FORMLIFT_SESSIONS_DB_VERSION', '1.0' );

function formlift_create_sessions_table() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . FORMLIFT_SESSIONS_TABLE;

	$sql = "CREATE TABLE $table_name (
      ID bigint(20) NOT NULL AUTO_INCREMENT,
      session_id varchar(191) NOT NULL,
      data text NOT NULL,
      expires bigint(20) NOT NULL,
      ip_address text NOT NULL,
      PRIMARY KEY  (ID)
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( 'formlift_sessions_db_version', FORMLIFT_SUBMISSIONS_DB_VERSION );
}

function formlift_update_session( $sessionID, $data, $expires ) {
	global $wpdb;

	return $wpdb->update(
		$wpdb->prefix . FORMLIFT_SESSIONS_TABLE,
		array(
			'data'       => $data,
			'expires'    => time() + $expires,
			'ip_address' => formlift_get_the_user_ip()
		),
		array( 'session_id' => $sessionID ),
		array( '%s', '%s' ),
		array( '%s' )
	);
}

function formlift_create_session( $sessionID, $data, $expires ) {
	global $wpdb;

	if ( ! $sessionID ) {
		return;
	}

	return $wpdb->insert(
		$wpdb->prefix . FORMLIFT_SESSIONS_TABLE,
		array(
			'session_id' => $sessionID,
			'data'       => $data,
			'expires'    => $expires + time(),
			'ip_address' => formlift_get_the_user_ip()
		)
	);
}

function formlift_get_session( $sessionId ) {

	global $wpdb;

	$table_name = $wpdb->prefix . FORMLIFT_SESSIONS_TABLE;
	$sql_prep1  = $wpdb->prepare( "SELECT * FROM $table_name WHERE session_id = %s", $sessionId );
	$result     = $wpdb->get_row( $sql_prep1, ARRAY_A );

	return ( ! isset( $result['data'] ) || empty( $result['data'] ) ) ? false : $result['data'];
}

function formlift_clean_up_sessions() {

	$run = get_transient( 'formlift_clean_up_waiting' );
	if ( $run ) {
		return;
	}

	set_transient( 'formlift_clean_up_waiting', true, 24 * HOUR_IN_SECONDS );

	$currentTime = time();

	global $wpdb;
	$table_name = $wpdb->prefix . FORMLIFT_SESSIONS_TABLE;

	return $wpdb->query(
		$wpdb->prepare( "
                DELETE FROM $table_name
                 WHERE expires < %d
             ", $currentTime ) );
}

add_action( 'init', 'formlift_clean_up_sessions' );

function formlift_delete_sessions() {

	global $wpdb;
	$table_name = $wpdb->prefix . FORMLIFT_SESSIONS_TABLE;

	return $wpdb->query(
		$wpdb->prepare( "
                DELETE FROM $table_name
                 WHERE session_id LIKE %s
             ", "formlift_session_%" ) );
}

if ( ! function_exists( 'formlift_get_the_user_ip' ) ) {
	function formlift_get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return apply_filters( 'wpb_get_ip', $ip );
	}
}

function formlift_delete_transients() {
	if ( ! is_user_logged_in() || ! is_admin() || ! isset( $_POST[ FORMLIFT_SETTINGS ]['delete_all_sessions'] ) || ! wp_verify_nonce( $_POST['delete_sessions_nonce'], 'formlift_delete_sessions' ) ) {
		return;
	}

	if ( formlift_delete_sessions() ) {
		FormLift_Notice_Manager::add_success( 'sessions-deleted', 'Sessions deleted successfully!' );
	} else {
		FormLift_Notice_Manager::add_error( 'sessions-deleted', 'Something went wrong deleting the sessions...' );
	}

}

add_action( 'init', 'formlift_delete_transients' );