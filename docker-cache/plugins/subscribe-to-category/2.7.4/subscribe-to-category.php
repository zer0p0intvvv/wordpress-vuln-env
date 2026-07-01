<?php
/**
  Plugin Name: Subscribe to Category
  Plugin URI: https://vandestouwe.com/stcmanual
  Description: Lets your visitor subscribe to posts for one or several categories.
  Version: 2.7.4
  Author: Daniel Söderström / Sidney van de Stouwe
  Author URI: https://vandestouwe.com
  License: GPLv2 or later
  Text Domain: subscribe-to-category
  Domain Path: /languages
 *
 * @package subscribe-to-category
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

define( 'STC_VERSION', '2.7.4' );
define( 'STC_SLUG', 'stc' );
define( 'STC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STC_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'STC_DEV_MODE', false ); // set to true email is printed out on setting page and will not be sent.

// pickup the cron reschedule time if it was set by the user else set it to 1 hour
$options = get_option( 'stc_settings' );
$cron_time = $options['cron_time'] ?? null;
if (!isset($cron_time)) {$cron_time = 3600;}

require_once( 'classes/class-stc-main.php' );
require_once( 'classes/class-stc-settings.php' );
require_once( 'classes/class-stc-sms.php' );
require_once( 'classes/class-stc-subscribe.php' );
require_once( 'classes/class-stc-widget.php' );

// Create instance for main class.
add_action( 'plugins_loaded', array( 'STC_Main', 'get_instance' ) );

// Create a custom event in WP-Cron
add_filter( 'cron_schedules', 'wpshout_add_cron_interval' );
function wpshout_add_cron_interval( $schedules ) {
    global $cron_time;
    $schedules['stc_reschedule_time'] = array(
            'interval'  => $cron_time, // reschedule time in seconds
            'display'   => 'STC Reschedule Time'
    );
    return $schedules;
}

function stc_general_admin_notice(){
    global $pagenow;
    global $message;
    global $severity;
    if ( $pagenow == 'options-general.php' ) {
         echo '<div class="notice '. $severity .' " is-dismissible">
             <p><strong>'. $message.'</p>
         </div>';
    }
}

/**
 * This callback will update term count based on object types of the current taxonomy.
 *
 * Callback for post_tag and category and taxonomies.
 *
 * @since 2.4.18
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int[]       $terms    List of Term taxonomy IDs.
 * @param WP_Taxonomy $taxonomy Current taxonomy object of terms.
 */
function stc_update_count_callback($terms, $taxonomy)
{
	global $wpdb;

	$object_types = (array) $taxonomy->object_type;

        // stc is for notifying subscribers that a post is new or updted therefore remove stc post from the count;
        foreach ( $object_types as $key=>$object_type ) {
		if ($object_type === 'stc') unset($object_types[$key]);
	}
        
        // frome here this is an exact copy of the original update_count_callback($terms, $taxonomy)
	foreach ( $object_types as &$object_type ) {
		list( $object_type ) = explode( ':', $object_type );
	}

	$object_types = array_unique( $object_types );

	$check_attachments = array_search( 'attachment', $object_types, true );
	if ( false !== $check_attachments ) {
		unset( $object_types[ $check_attachments ] );
		$check_attachments = true;
	}

	if ( $object_types ) {
		$object_types = esc_sql( array_filter( $object_types, 'post_type_exists' ) );
	}

	foreach ( (array) $terms as $term ) {
		$count = 0;

		// Attachments can be 'inherit' status, we need to base count off the parent's status if so.
		if ( $check_attachments ) {
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id AND ( post_status = 'publish' OR ( post_status = 'inherit' AND post_parent > 0 AND ( SELECT post_status FROM $wpdb->posts WHERE ID = p1.post_parent ) = 'publish' ) ) AND post_type = 'attachment' AND term_taxonomy_id = %d", $term ) );
		}

		if ( $object_types ) {
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.QuotedDynamicPlaceholderGeneration
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id AND post_status = 'publish' AND post_type IN ('" . implode( "', '", $object_types ) . "') AND term_taxonomy_id = %d", $term ) );
		}

		/** This action is documented in wp-includes/taxonomy.php */
		do_action( 'edit_term_taxonomy', $term, $taxonomy->name );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );

		/** This action is documented in wp-includes/taxonomy.php */
		do_action( 'edited_term_taxonomy', $term, $taxonomy->name );
	}
}   


// Register activation hook.
register_activation_hook( __FILE__, array( 'STC_Main', 'activate' ) );

// Register deactivation hook.
register_deactivation_hook( __FILE__, array( 'STC_Main', 'deactivate' ) );

register_post_meta(
	'',
	'_stc_notifier_status',
	array(
		'show_in_rest' => true,
		'single' => true,
		'type' => 'string',
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	)
);

register_post_meta(
	'',
	'_stc_notifier_sent_time',
	array(
		'show_in_rest' => true,
		'single' => true,
		'type' => 'string',
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	)
);
                
register_post_meta(
	'',
	'_stc_notifier_request',
	array(
		'type' => 'boolean',
		'show_in_rest' => true,
		'single' => true,
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	)
);
                
register_post_meta(
	'',
	'_stc_notifier_prevent',
	array(
		'type' => 'boolean',
		'show_in_rest' => true,
		'single' => true,
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	)
);

register_post_meta(
	'',
	'_stc_subscriber_keywords',
	array(
		'show_in_rest' => true,
		'single' => true,
		'type' => 'string',
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	)
);
                
register_post_meta(
	'',
	'_stc_subscriber_search_areas',
	array(
		'show_in_rest' => true,
		'single' => true,
		'type' => 'string',
		'auth_callback' => function() {
			return current_user_can( 'edit_posts' );
		},
	)
);

// to establish the new hook
$timestamp = wp_next_scheduled( 'stc_schedule_email', array("Timer") );
if ( false == $timestamp ) {
        wp_schedule_event( time(), 'stc_reschedule_time', 'stc_schedule_email', array("Timer") );
}





