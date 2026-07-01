<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Form_Settings_Meta_Box {

	public static function add_meta_box() {
		add_meta_box(
			"formlift_form_settings",
			"Form Settings",
			array( 'Form_Settings_Meta_Box', "create_form_settings_box" ),
			"infusion_form"
		);
	}

	/**
	 * Add the settings panel to the post type
	 *
	 * @param $post
	 */
	public static function create_form_settings_box( $post ) {

		wp_nonce_field( 'formlift_saving_settings', 'formlift_settings_nonce' );

		$meta_box = new FormLift_Options_Skin( $post->ID, 'form_settings' );

		$meta_box->add_section( 'formlift_import_settings', 'Import Form', FormLift_Settings::import_settings() );
		$meta_box->add_section( 'formlift_form_settings', 'Submission Settings', FormLift_Settings::submission_settings() );
		$meta_box->add_section( 'formlift_error_settings', 'Messages', FormLift_Settings::error_settings() );
		$meta_box->add_section( 'import_export', 'Import/Export', FormLift_Settings::import_export_form_level() );

		$meta_box = apply_filters( 'formlift_settings_widget', $meta_box );

		echo $meta_box;
	}

	public static function remove_import_form_setting_pre_import( $fields ) {
		$formFields = get_post_meta( get_the_ID(), FORMLIFT_FIELDS, true );

		if ( empty( $formFields ) ) {
			unset( $fields["infusionsoft_form_id"] );
			unset( $fields["infusionsoft_form_original_html"] );
		}

		return $fields;
	}

	public static function export_settings( $form_id ) {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['export_form'] ) && current_user_can( 'manage_options' ) ) {
			$filename = "formlift_plugin_settings_" . date( "Y-m-d_H-i", time() );

			header( "Content-type: text/plain" );
			header( "Content-disposition: attachment; filename=" . $filename . ".txt" );

			$file = fopen( 'php://output', 'w' );

			fputs( $file, json_encode( get_post_meta( $form_id, FORMLIFT_SETTINGS, true ) ) );

			fclose( $file );

			exit();
		}
	}

	public static function import_settings( $form_id ) {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['import_form'] ) && current_user_can( 'manage_options' ) ) {

			$options = stripslashes( $_POST[ FORMLIFT_SETTINGS ]['import_form_settings'] );

			if ( empty( $options ) ) {
				FormLift_Notice_Manager::add_error( 'bad_import', "No settings to import..." );

				return;
			}

			$options = apply_filters( 'formlift_sanitize_form_settings', json_decode( $options, true ) );
			update_post_meta( $form_id, FORMLIFT_SETTINGS, $options );

		}
	}
}

add_action( 'add_meta_boxes', array( 'Form_Settings_Meta_Box', 'add_meta_box' ) );
add_filter( 'formlift_import_settings', array( 'Form_Settings_Meta_Box', 'remove_import_form_setting_pre_import' ) );
add_action( 'formlift_after_save_form', array( 'Form_Settings_Meta_Box', 'export_settings' ) );
add_action( 'formlift_after_save_form', array( 'Form_Settings_Meta_Box', 'import_settings' ) );