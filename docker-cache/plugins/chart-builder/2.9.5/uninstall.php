<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
if(get_option('ays_chart_builder_upgrade_plugin','false') === 'false'){
	global $wpdb;
	$charts_table          =   $wpdb->prefix . 'ayschart_charts';
    $charts_meta_table     =   $wpdb->prefix . 'ayschart_charts_meta';
	$settings_table        =   $wpdb->prefix . 'ayschart_settings';

	$wpdb->query("DROP TABLE IF EXISTS `".$charts_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$charts_meta_table."`");
    $wpdb->query("DROP TABLE IF EXISTS `".$settings_table."`");
	
	delete_option( "ays_chart_db_version");
    delete_option( "ays_chart_builder_upgrade_plugin");
}