<?php
/*
 * Plugin Name: FormLift
 * Description: The Ultimate Web Form Solution for WordPress and Infusionsoft. Style your web forms, create personalized pages, and create epic automation with them too.
 * Version: 7.5.17
 * Author: Adrian Tobey
 * Plugin URI: https://formlift.net
 * Author URI: https://formlift.net/blog
 *
 * Copyright (c) Groundhogg Inc. 2021
 * 25 Lesmill Road unit D, Toronto, Ontario, Canada
 * License: GPLv2
 *
 * For Support Please send emails to info@formlift.net or visit https://formlift.net/contact-us.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FORMLIFT_VERSION', '7.5.17' );
define( 'FORMLIFT_CSS_VERSION', '7.5.15' );
define( 'FORMLIFT_JS_VERSION', '7.5.14' );
define( 'FORMLIFT_VERSION_KEY', 'formlift_db_version' );
define( 'FORMLIFT_SETTINGS', 'formlift_form_settings' );
define( 'FORMLIFT_STYLE', 'formlift_style_settings' );
define( 'FORMLIFT_FIELDS', 'formlift_form_bits' );

/* Load Modules */

if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ):

	include __DIR__ . "/modules/modules-loader.php";

	add_filter( 'single_template', 'formlift_form_template_hack' );

	function formlift_form_template_hack( $page_template ) {
		if ( get_post_type( get_the_ID() ) == 'infusion_form' ) {
			$page_template = __DIR__ . '/templates/formlift-single.php';
		}

		return $page_template;
	}

	register_activation_hook( __FILE__, 'formlift_set_plugin_defaults' );

	function formlift_set_plugin_defaults() {
		update_option( FORMLIFT_VERSION_KEY, FORMLIFT_VERSION );

		formlift_create_impressions_table();
		formlift_create_sessions_table();
		formlift_create_submissions_table();

		FormLift_Defaults::set_defaults_on_activation();

		FormLift_Notice_Manager::add_success( 'welcome', "<p>Hey, thanks for installing FormLift!</p><p>We do a TON of work on this plugin to make sure you have the easiest experience possible. We release updates constantly, so make sure you are always on top of them to have access to the latest features.</p><p>If you're finding FormLift is lacking something, we probably have a solution for that! <a href=\"https://formlift.net/store/\">Check out our extensions library</a> when you need FormLift to do something extra.</p>" );
	}

	do_action( 'formlift_loaded' );

else:
	function formlift_php_error_notice() {
		?>
        <div class='notice notice-error'><p>Uh oh... your <strong>PHP level must be 5.6 or greater</strong> for FormLift
                to activate. Please update
                your PHP level to 5.6 or better (7+ is recommended)! You can use
                <a href='https://wordpress.org/plugins/php-compatibility-checker/'>this plugin</a> to determine if it is
                safe
                for you to upgrade your PHP.</p></div>
		<?php
	}

	add_action( 'admin_notices', 'formlift_php_error_notice' );
endif;