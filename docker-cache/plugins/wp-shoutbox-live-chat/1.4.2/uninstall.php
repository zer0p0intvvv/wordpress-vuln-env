<?php
if (!defined( 'WP_UNINSTALL_PLUGIN' )){
    exit;
}
global $wpdb;
$Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';
$Shoutbox_users_table_name = $wpdb->prefix . 'Shoutbox_users';

if(get_option('Shoutbox_options')) delete_option('Shoutbox_options');
if(get_option('Shoutbox_db_version')) delete_option('Shoutbox_db_version');
if(get_option('widget_shoutbox-widget')) delete_option('widget_shoutbox-widget');
$query = $wpdb->query('DROP TABLE IF EXISTS '.$Shoutbox_messages_table_name.';');
$query = $wpdb->query('DROP TABLE IF EXISTS '.$Shoutbox_users_table_name.';');
?>
