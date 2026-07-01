<?php
/**
 * WPBookit
 * @wordpress-plugin
 * Plugin Name:   WPBookit
 * Plugin URI:    https://wpbookit.com
 * Description:   WPBookit is a comprehensive WordPress plugin that streamlines appointment bookings and enhances user experience, ideal for businesses of all sizes. Manage reservations effortlessly directly from your WordPress site.
 * Version:       1.0.4
 * Author:        Iqonic Design
 * Author URI:    https://iqonic.design
 * Text Domain:   wpbookit
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:   http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
} else {
	die( 'Something went wrong' );
}

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function WPBOOKIT() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
if(!defined('IQWPB_NAME')){
	define( 'IQWPB_NAME', 'WPBookit' );
}

// Plugin version
if(!defined('IQWPB_VERSION')){
	define( 'IQWPB_VERSION',	'1.0.4' );
}

if (!defined('IQWPB_PLUGIN_FILE')) {
    define('IQWPB_PLUGIN_FILE', __FILE__);
}

if (!defined('IQWPB_PLUGIN_BASE')) {
    define('IQWPB_PLUGIN_BASE', plugin_basename(IQWPB_PLUGIN_FILE));
}

if (!defined('IQWPB_PLUGIN_PATH')) {
    define('IQWPB_PLUGIN_PATH', plugin_dir_path(IQWPB_PLUGIN_FILE));
}

if (!defined('IQWPB_PLUGIN_URL')) {
    define('IQWPB_PLUGIN_URL', plugin_dir_url(IQWPB_PLUGIN_FILE));
}

// Plugin TAXONOMY TYPE
if (!defined('IQWPB_BOOKING_TAXONOMY_TYPE')) {
	define( 'IQWPB_BOOKING_TAXONOMY_TYPE', 'wpb_custom_calendars' );
}

/**
 * Load the main class for the core functionality
 */
require_once IQWPB_PLUGIN_PATH . 'core/class-wpbookit.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Iqonic Design
 * @since   1.0.4
 * @return  object|Wpbookit
 */
if(!function_exists('WPBOOKIT')){
	function WPBOOKIT() {
		return Wpbookit::instance();
	}
}
/**
 * The code that runs during plugin activation
 */
register_activation_hook( __FILE__, [ Wpbookit::class, 'activate'] );
register_activation_hook( __FILE__, function(){
	if(in_array('wpbookit-pro/wpbookit.php', (array) get_option('active_plugins', array()))){
		deactivate_plugins('wpbookit/wpbookit.php');
		wp_die(
            esc_html__('WPBookit Lite cannot be activated while Pro version is active.', 'wpbookit'),
            'Plugin Activation Error',
            array(
                'back_link' => true,
                'response'  => 200
            )
        );
	}
});


WPBOOKIT();

