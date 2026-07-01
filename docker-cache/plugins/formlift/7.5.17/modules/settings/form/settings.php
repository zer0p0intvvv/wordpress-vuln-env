<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Settings {

	public static function admin_settings() {
		$fields = array();

		$fields["opt_out_of_usage_stats"] = new FormLift_Setting_Field(
			FORMLIFT_CHECKBOX,
			'opt_out_of_usage_stats',
			"Opt of Usage Stats collection.",
			null,
			'FormLift collects anonymous data from your site such as the number of submissions you collect, and the number of impressions. If you do not want to send that data to us just enable this option to stop.'
		);

		$fields["disable_utm_removal"]      = new FormLift_Setting_Field(
			FORMLIFT_CHECKBOX,
			'disable_utm_removal',
			'Disable PII Query Removal',
			null,
			'This will stop FormLift from removing PII Variables from the url query string. This may cause issues regarding Google Adwords & Analytics policies. It will also put your user\'s information at risk to external scripts.'
		);
		$fields["exclude_from_utm_removal"] = new FormLift_Setting_Field(
			FORMLIFT_TEXT,
			'exclude_from_utm_removal',
			'Exclude UTM Variables From Removal',
			null,
			'Enter 1 variable per line. FormLift strips PII (Personal Identifiable Infortmation) from the URL query string to protect user data from external scripts. Add UTM variables here to exclude them from this functionality.'
		);

		if ( FormLift_Module_Manager::has_modules() ) {
			$fields["disable_credit"] = new FormLift_Setting_Field(
				FORMLIFT_CHECKBOX,
				'disable_credit',
				"Disable the \"&#9889 by FormLift\" credit.",
				null,
				'It helps us out if you leave it!'
			);
		}

		$fields["reset_button"] = new FormLift_Setting_Field(
			FORMLIFT_BUTTON,
			"reset_style_to_defaults",
			"Reset All Style Settings To Their Defaults",
			"RESET TO DEFAULTS",
			'DANGER: clicking this will re-implement the default style settings from initial installation. This cannot be undone. Please export your settings FIRST before resetting.'
		);

		$fields["truncate_stats"] = new FormLift_Setting_Field(
			FORMLIFT_BUTTON,
			"truncate_stats",
			"Truncate the Stats tables",
			"DELETE ALL STATS",
			'DANGER: Clicking this will delete all the stats reports for your forms.'
		);

		return apply_filters( 'formlift_admin_settings', $fields );
	}

	public static function import_export() {
		$fields = array();
//        $fields[] = "<h2>Plugin Settings</h2>";
		$fields["import_plugin_settings"] = new FormLift_Setting_Field( FORMLIFT_TEXT, 'import_plugin_settings', 'Paste your Plugin Settings here...', null,
			'Paste the settings here from your TXT file export.' );
		$fields["import_plugin"]          = new FormLift_Setting_Field( FORMLIFT_BUTTON, "import_plugin", "Click to import plugin settings.", "IMPORT NOW",
			'This will overwrite ALL settings you currently have set. EVEN your Infusionsoft connection.' );
		$fields["export_plugin"]          = new FormLift_Setting_Field( FORMLIFT_BUTTON, "export_plugin", "Export your plugin settings", "EXPORT NOW",
			'This will provide you with a TXT file where you can open it and copy the settings to another installation.' );
//        $fields[] = "<h2>Style Settings</h2>";
		$fields["import_style_settings"] = new FormLift_Setting_Field( FORMLIFT_TEXT, 'import_style_settings', 'Paste your Style Settings here...', null,
			'Paste the settings here from your TXT file export.' );
		$fields["import_style"]          = new FormLift_Setting_Field( FORMLIFT_BUTTON, "import_style", "Click to import Style Settings.", "IMPORT NOW",
			'This will overwrite ALL settings you currently have set.' );
		$fields["export_style"]          = new FormLift_Setting_Field( FORMLIFT_BUTTON, "export_style", "Export your Style Settings", "EXPORT NOW",
			'This will provide you with a TXT file where you can open it and copy the settings to another installation.' );

		return apply_filters( 'formlift_import_export_settings', $fields );
	}

	public static function import_export_form_level() {
		$fields = array();
//		$fields[] = "<h2>Form Settings</h2>";
		$fields["import_form_settings"] = new FormLift_Setting_Field( FORMLIFT_TEXT, 'import_form_settings', 'Paste your Form Settings here...', null,
			'Paste the settings here from your TXT file export.' );
		$fields["import_form"]          = new FormLift_Setting_Field( FORMLIFT_BUTTON, "import_form", "Click to import your form settings.", "IMPORT NOW",
			'This will overwrite ALL settings you currently have set. However, this will NOT export the fields of your form.' );
		$fields["export_form"]          = new FormLift_Setting_Field( FORMLIFT_BUTTON, "export_form", "Export your form settings", "EXPORT NOW",
			'This will provide you with a TXT file where you can open it and copy the settings to another installation.' );

		return apply_filters( 'formlift_import_export_form_settings', $fields );
	}

	public static function submission_settings() {
		$fields                 = array();
		$fields["post_url"]     = new FormLift_Setting_Field( FORMLIFT_INPUT, 'post_url', 'Infusionsoft Post URL', null,
			'This is the url the form submits to. Do not change this if you are using an Infusionsoft Form.' );
		$fields["target_blank"] = new FormLift_Setting_Field( FORMLIFT_CHECKBOX, 'target_blank', 'Submit To Blank Page', null,
			'This is an experimental feature and is blocked by some chrome extensions. This will open up a new tab when the form submits.' );
//        $fields["show_post_url"] = new FormLift_Setting_Field(FORMLIFT_CHECKBOX, 'show_post_url', 'Show Post URL');
//        $fields[] = "<div class=\"formlift-error\">Enabling this will turn off validation and open you up to spam.</div>";
		//$fields["submit_via_ajax"] = new FormLift_Setting_Field( FORMLIFT_CHECKBOX, 'submit_via_ajax', 'Don\'t send to thank you page.' );
		$fields["enable_compatibility_mode"] = new FormLift_Setting_Field( FORMLIFT_CHECKBOX, 'enable_compatibility_mode', 'Enable compatibility mode.', null,
			'This will disable the AJAX submission if you have some spam protection plugins installed that block off wp_ajax files. ' );

		return apply_filters( 'formlift_submission_settings', $fields );
	}

	/**
	 * Returns a list of FormLift_Setting_Fields for the Button Settings Section
	 *
	 * @return array
	 */
	public static function error_settings() {
		$fields = array();

		$fields["success_message"]    = new FormLift_Setting_Field( FORMLIFT_TEXT, 'success_message', 'Success Message', null,
			'Message displayed upon a successful submission.' );
		$fields["please_wait_text"]   = new FormLift_Setting_Field( FORMLIFT_TEXT, 'please_wait_text', 'Please Wait Text...', null,
			'Message displayed while the form is submitting.' );
		$fields["invalid_data_error"] = new FormLift_Setting_Field( FORMLIFT_TEXT, 'invalid_data_error', 'Invalid Data Error', null,
			'General error message to tell the user something is wrong with their submission. Also used by the spam protection plugin when a match is made.' );
		$fields["required_error"]     = new FormLift_Setting_Field( FORMLIFT_TEXT, 'required_error', 'Required Field Error', null,
			'Message displayed when a field is required and they did not fill it out.' );
		$fields["email_error"]        = new FormLift_Setting_Field( FORMLIFT_TEXT, 'email_error', 'Invalid Email Error', null,
			'Message shown when a user enters an invalid email.' );
		$fields["phone_error"]        = new FormLift_Setting_Field( FORMLIFT_TEXT, 'phone_error', 'Invalid Phone Error', null,
			'Message shown when a user enters an invalid phone number.' );
		$fields["date_error"]         = new FormLift_Setting_Field( FORMLIFT_TEXT, 'date_error', 'Invalid Date Error', null,
			'Message shown when a user enters an invalid date.' );
		$fields["url_error"]          = new FormLift_Setting_Field( FORMLIFT_TEXT, 'url_error', 'Invalid Url Error', null,
			'Message shown when a user enters an invalid URL.' );
		$fields["password_error"]     = new FormLift_Setting_Field( FORMLIFT_TEXT, 'password_error', 'Mismatched Passwords Error', null,
			'Shown when someone has enter mismatched passwords for the affiliate lead generation form.' );

		return apply_filters( 'formlift_error_settings', $fields );
	}

	/**
	 * Returns a list of FormLift_Setting_Fields for the tracking settings section
	 *
	 * @return array
	 */
	public static function tracking_settings() {
		$fields = array();

		$fields["disable_session_storage"] = new FormLift_Setting_Field( FORMLIFT_CHECKBOX, 'disable_session_storage', "Disable Session Storage", null,
			'Enable this to turn OFF the storage of Personal Identifiable Information on FormLift between pages' );
		$fields["time_to_live"]            = new FormLift_Setting_Field( FORMLIFT_NUMBER, 'time_to_live', "PII Session Storage Time in Days", null,
			'The number of days you\'d like FormLift to store PII after a user\'s LAST successful submission so that it will auto populate forms and replacement codes.' );
		$fields['session_storage_field']   = new FormLift_Setting_Field( FORMLIFT_SELECT, 'session_storage_field', 'Use a custom field to save sessions', formlift_get_custom_fields(),
			'Use this to store sessions in Infusionsoft. Send a user back to your site with their session info by appending <pre>?formlift_session=~Contact.' . get_formlift_setting( 'session_storage_field' ) . '~</pre> to any url on your website.' );
		$fields["refresh_custom_fields"]   = new FormLift_Setting_Field( FORMLIFT_BUTTON, "refresh_custom_fields", "Refresh Custom Fields", "REFRESH",
			'The custom fields are cached, if you added new ones and they are not appearing in the dropdown above, click this.' );
		$fields["delete_all_sessions"]     = new FormLift_Setting_Field( FORMLIFT_BUTTON, "delete_all_sessions", "Delete all currently stored user sessions", "DELETE SESSIONS",
			"Use this to clear any current user sessions on your site. it may take a second." );

		return apply_filters( 'formlift_get_tracking_settings', $fields );
	}

	/**
	 * Returns a list of FormLift_Setting_Fields for the Infusionsoft API settings
	 *
	 * @return array
	 */
	public static function infusionsoft_settings() {
		$status = get_option( 'oauth_last_status' );

		$fields   = array();
		$fields[] = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'activate_OAuth', 'Connect To Infusionsoft', "CONNECT",
			"Use oauth to securely connect to Infusionsoft. This uses formlift.net as a proxy only for the initial connection. Using Oauth subjects you to anonymous API usage monitoring. No PII is stored, period.<br/><br/><b>Current Status</b>: $status" );
		$fields[] = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'refresh_OAuth', 'Refresh Infusionsoft Connection', "REFRESH",
			'Manually refresh your connection with Infusionsoft if you are experiencing intermittent errors or slowness.' );
		$fields[] = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'disconnect_Oauth', 'Disconnect From Infusionsoft', "DISCONNECT",
			'Manually disconnect your connection with Infusionsoft if you are experiencing intermittent errors or slowness.' );
		$fields[] = "<h1>Experiencing issues? Try using the Legacy API... </h1>";
		$fields[] = new FormLift_Setting_Field( FORMLIFT_INPUT, 'infusionsoft_app_name', 'Infusionsoft App Name (e.g xx123)', null,
			'Your app name is the name before the <b>.infusionsoft.com</b> for example <b>ab123</b>.infusionsoft.com' );
		$fields[] = new FormLift_Setting_Field( FORMLIFT_SECRET, 'infusionsoft_api_key', 'Infusionsoft API Key', null,
			'<a href="https://help.infusionsoft.com/userguides/get-started/tips-and-tricks/api-key" target="_blank">Find your API key.</a>' );

		return apply_filters( 'formlift_infusionsoft_settigns', $fields );
	}

	/**
	 * Returns a list of FormLift_Setting_Fields for the form import settings
	 *
	 * @return array
	 */
	public static function import_settings() {

		$fields                                    = array();
		$fields["infusionsoft_form_id"]            = new FormLift_Setting_Field( FORMLIFT_SELECT, 'infusionsoft_form_id', 'Import From Infusionsoft', formlift_get_infusionsoft_webforms(),
			'This is the list of all the web forms from your Infusionsoft APP. Including Legacy forms. Select the form you wish to import and then hit <b>sync</b> or <b>replace</b> below.' );
		$fields["formlift_update_webform_list"]    = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'formlift_update_webform_list', 'Refresh Form List', "REFRESH",
			'Is your form not showing in the list? First, make sure it\'s published in the campaign builder, then click this button to pull in the new list of forms.' );
		$fields["form_sync"]                       = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'form_sync', 'Sync Form Code', "SYNC", 'This will update your form with any changes you made in Infusionsoft, like adding new fields' );
		$fields["form_refresh"]                    = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'form_refresh', 'Replace Form Code', "REPLACE", 'This will completely replace your form code, all changes made in the editor will be lost.' );
		$fields["infusionsoft_form_original_html"] = new FormLift_Setting_Field( FORMLIFT_TEXT, 'infusionsoft_form_original_html', 'Use Form Html', null,
			'Have a custom form you want to import? Use this instead. Paste the code into the text block and then click the button below.' );
		$fields["parse_original_html"]             = new FormLift_Setting_Field( FORMLIFT_BUTTON, 'parse_original_html', 'Parse Form Html', "IMPORT",
			'Import the above code and replace all the form data.' );

		return apply_filters( 'formlift_import_settings', $fields );
	}

	/**
	 * Function that saves the default settings
	 *
	 * @param $options
	 */
	public static function save_settings() {
		if ( isset( $_POST['formlift_options'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {

			$options = $_POST[ FORMLIFT_SETTINGS ];

			$options = apply_filters( 'formlift_sanitize_form_settings', $options );

			update_option( FORMLIFT_SETTINGS, $options );

			do_action( 'formlift_after_save_plugin_settings' );

			FormLift_Notice_Manager::add_success( 'imported', "Settings saved!" );
		}
	}

	/**
	 * Cleans the settings so no one can break the form.
	 *
	 * @param $array array
	 *
	 * @return array
	 */
	public static function clean_settings( $array ) {
		foreach ( $array as $option => $value ) {
			if ( is_string( $value ) ) {
				$array[ $option ] = sanitize_textarea_field( stripslashes( $value ) );
			} elseif ( ( is_array( $value ) ) ) {
				$array[ $option ] = self::clean_settings( $value );
			}
		}

		return $array;
	}

	public static function truncate_stats(){
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['truncate_stats'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {
			formlift_truncate_all_stats();

			FormLift_Notice_Manager::add_success( 'truncated-stats', "Reset all statistics counts!" );
		}
	}

	public static function export_settings() {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['export_plugin'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {
			$filename = "formlift_plugin_settings_" . date( "Y-m-d_H-i", time() );

			header( "Content-type: text/plain" );
			header( "Content-disposition: attachment; filename=" . $filename . ".txt" );

			$file = fopen( 'php://output', 'w' );

			fputs( $file, json_encode( get_option( FORMLIFT_SETTINGS ) ) );

			fclose( $file );

			exit();
		}
	}

	public static function import() {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['import_plugin'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {

			$options = stripslashes( $_POST[ FORMLIFT_SETTINGS ]['import_plugin_settings'] );

			if ( empty( $options ) ) {
				FormLift_Notice_Manager::add_error( 'bad_import', "No settings to import..." );

				return;
			}

			$options = apply_filters( 'formlift_sanitize_form_settings', json_decode( $options, true ) );
			update_option( FORMLIFT_SETTINGS, $options );

			FormLift_Notice_Manager::add_success( 'imported', "Imported all settings!" );
		}
	}
}

add_action( 'init', array( 'FormLift_Settings', 'save_settings' ) );
add_filter( 'formlift_sanitize_form_settings', array( 'FormLift_Settings', 'clean_settings' ) );
add_action( 'init', array( 'FormLift_Settings', 'export_settings' ) );
add_action( 'init', array( 'FormLift_Settings', 'truncate_stats' ) );
add_action( 'init', array( 'FormLift_Settings', 'import' ) );