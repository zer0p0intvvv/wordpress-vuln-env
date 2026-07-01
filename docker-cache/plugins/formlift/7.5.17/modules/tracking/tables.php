<?php
/**
 * Created by PhpStorm.
 * User: adria
 * Date: 2018-04-12
 * Time: 3:30 PM
 */
define( 'FORMLIFT_SUBMISSIONS_TABLE', 'formlift_submissions' );
define( 'FORMLIFT_IMPRESSIONS_TABLE', 'formlift_impressions' );
define( 'FORMLIFT_SUBMISSIONS_DB_VERSION', '2.0' );
define( 'FORMLIFT_IMPRESSIONS_DB_VERSION', '2.0' );

function formlift_create_submissions_table() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . FORMLIFT_SUBMISSIONS_TABLE;

	$sql = "CREATE TABLE $table_name (
      ID bigint(20) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      form_id bigint(20) NOT NULL,
      email varchar(64) NOT NULL,
      PRIMARY KEY  (ID),
      KEY form_id (form_id)
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( 'formlift_submissions_db_version', FORMLIFT_SUBMISSIONS_DB_VERSION );
}

function formlift_create_impressions_table() {

	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . FORMLIFT_IMPRESSIONS_TABLE;

	$sql = "CREATE TABLE $table_name (
      ID bigint(20) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      form_id bigint(20) NOT NULL,
      PRIMARY KEY  (ID),
      KEY form_id (form_id)
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	update_option( 'formlift_impressions_db_version', FORMLIFT_IMPRESSIONS_DB_VERSION );
}

/**
 * Truncate both the submissions and impressions tables
 */
function formlift_truncate_all_stats(){
	global $wpdb;
	$impressions = $wpdb->prefix . FORMLIFT_IMPRESSIONS_TABLE;
	$submissions = $wpdb->prefix . FORMLIFT_SUBMISSIONS_TABLE;

	$wpdb->query( "DROP TABLE {$impressions};" );
	$wpdb->query( "DROP TABLE {$submissions};" );

	formlift_create_impressions_table();
	formlift_create_submissions_table();
}

function formlift_add_impression( $form_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . FORMLIFT_IMPRESSIONS_TABLE;

	return $wpdb->insert(
		$table_name,
		array(
			'form_id' => $form_id,
			'time'    => current_time( 'mysql' )
		)
	);
}

//add_action( 'formlift_record_submission', 'formlift_add_submission');

function formlift_add_submission( $form_id ) {
	global $wpdb;
	global $FormLiftUser;

	$email      = $FormLiftUser->get_user_data( 'inf_field_Email' );
	$table_name = $wpdb->prefix . FORMLIFT_SUBMISSIONS_TABLE;

	return $wpdb->insert(
		$table_name,
		array(
			'form_id' => $form_id,
			'email'   => $email,
			'time'    => current_time( 'mysql' )
		)
	);
}

function formlift_get_all_impressions( $date1, $date2 ) {
	global $wpdb;

	$table_name = $wpdb->prefix . FORMLIFT_IMPRESSIONS_TABLE;

	return $wpdb->get_var(
		"
	SELECT COUNT(*) 
	FROM $table_name WHERE time >= '$date1' AND time <= '$date2'
	" );
}

function formlift_get_all_submissions( $date1, $date2 ) {
	global $wpdb;
	$table_name = $wpdb->prefix . FORMLIFT_SUBMISSIONS_TABLE;

	return $wpdb->get_var(
		"
	SELECT COUNT(*) 
	FROM $table_name WHERE time >= '$date1' AND time <= '$date2'
	" );
}

function formlift_get_form_impressions( $date1, $date2, $form_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . FORMLIFT_IMPRESSIONS_TABLE;

	return $wpdb->get_var(
		"
	SELECT COUNT(*) 
	FROM $table_name WHERE time >= '$date1' AND time <= '$date2' AND form_id = $form_id
	" );
}

function formlift_get_form_submissions( $date1, $date2, $form_id ) {
	global $wpdb;

	$table_name = $wpdb->prefix . FORMLIFT_SUBMISSIONS_TABLE;

	return $wpdb->get_var(
		"
	SELECT COUNT(*) 
	FROM $table_name WHERE time >= '$date1' AND time <= '$date2' AND form_id = $form_id
	" );
}