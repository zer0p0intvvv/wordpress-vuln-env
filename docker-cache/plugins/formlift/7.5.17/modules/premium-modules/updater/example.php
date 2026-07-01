<?php
/**
 * Created by PhpStorm.
 * User: adria
 * Date: 2018-04-06
 * Time: 5:35 PM
 */

define( 'FL_SAMPLE_ID', 1234 );
define( 'FL_SAMPLE_NAME', "Example" );
define( 'FL_SAMPLE_VERSION', 1.0 );

# FormLift is definitely loaded if this is running
function formlift_example_plugin_load() {
	add_filter( 'get_formlift_module_extensions', 'formlift_add_example_plugin' );

	if ( FormLift_Module_Manager::has_license( FL_SAMPLE_ID ) && FormLift_Module_Manager::get_license_status( FL_SAMPLE_ID ) !== 'invalid' ) {

		$updater = new FORMLIFT_EDD_SL_Plugin_Updater( FormLift_Module_Manager::$storeUrl, __FILE__, array(
				'version' => FL_SAMPLE_VERSION,
				// current version number
				'license' => trim( FormLift_Module_Manager::get_license( FL_SAMPLE_ID ) ),
				// license key (used get_option above to retrieve from DB)
				'item_id' => FL_SAMPLE_ID,
				// id of this product in EDD
				'author'  => 'Adrian Tobey',
				// author of this plugin
				'url'     => home_url()
			)
		);

		/* include any files here */

		/* stop editing */
	}
}

function formlift_add_example_plugin( $modules ) {
	$modules[ FL_SAMPLE_ID ] = array(
		'item_name'   => FL_SAMPLE_NAME,
		'item_id'     => FL_SAMPLE_ID,
		'version'     => FL_SAMPLE_VERSION,
		'img_source'  => 'https://src.com',
		'description' => 'Quick plugin description'
	);

	return $modules;
}

if ( class_exists( "FormLift_Module_Manager" ) ) {
	formlift_example_plugin_load();
} else {
	add_action( "formlift_loaded", "formlift_example_plugin_load", 10 );
}