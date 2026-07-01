<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

function get_formlift_setting( $id, $default = false ) {
	$option = get_option( FORMLIFT_SETTINGS );

	if ( isset( $option[ $id ] ) && ! empty( $option[ $id ] ) ) {
		return $option[ $id ];
	} else {
		return $default;
	}
}

function formlift_get_form_setting( $formId, $key, $default = null ) {
	$options = get_post_meta( $formId, FORMLIFT_SETTINGS, true );

	if ( isset( $options[ $key ] ) && ! empty( $options[ $key ] ) ) {
		return $options[ $key ];
	} else {
		return $default;
	}
}


function formlift_option_exists( $option_name ) {
	$option = get_option( $option_name );

	return ! empty( $option );
}

function formlift_get_auto_fill_query_extension( $fieldsOrInt ) {
	if ( is_int( $fieldsOrInt ) ) {
		$form   = new FormLift_Form( $fieldsOrInt );
		$fields = $form->get_fields();
	} else if ( is_array( $fieldsOrInt ) ) {
		$fields = $fieldsOrInt;
	} else {
		$form   = new FormLift_Form( intval( $fieldsOrInt ) );
		$fields = $form->get_fields();
	}

	$params = "?";
	if ( empty( $fields ) ) {
		return "";
	}
	foreach ( $fields as $fieldname => $field_params ) {
		if ( strpos( $fieldname, '_field_' ) || strpos( $fieldname, '_other_' ) ) {
			$params .= $fieldname . "=" . "~Contact." . substr( $fieldname, strrpos( $fieldname, "_" ) + 1 ) . "~";
			$params .= "&";
		} elseif ( strpos( $fieldname, '_custom_' ) ) {
			$params .= $fieldname . "=" . "~Contact." . substr( $fieldname, strrpos( $fieldname, "_" ) ) . "~";
			$params .= "&";
		}
	}

	return $params;
}

function get_all_formlift_forms() {
	$args      = array( 'numberposts' => '-1', 'post_type' => 'infusion_form', 'post_status' => 'any' );
	$postslist = get_posts( $args );

	return $postslist;
}

function get_formlift_form_drop_down() {
	$forms = get_all_formlift_forms(); //array(Post)
	$list  = array();
	foreach ( $forms as $form ) {
		$list[ $form->ID ] = $form->post_title;
	}

	return $list;
}


add_action( 'plugins_loaded', 'formlift_update_web_form_list' );

function formlift_update_web_form_list() {
	if ( isset( $_POST[ FORMLIFT_SETTINGS ]['formlift_update_webform_list'] ) ) {
		_formlift_update_web_form_list();
	}
}

function _formlift_update_web_form_list() {
	$array = FormLift_Infusionsoft_Manager::getWebForms();

	if ( is_wp_error( $array ) ) {
		FormLift_Notice_Manager::add_notice( 'oauth_error', array(
			'is_dismissable' => true,
			'is_premium'     => 'both',
			'is_specific'    => false,
			'type'           => 'notice-error',
			'html'           => 'Something went wrong pulling the webform list. Try manually refreshing your connection in the settings. If the problem persists copy the following and send it to <a href="mailto:info@formlift.net">info@formlift.net</a>. Error: ' . $array->get_error_message()
		) );

		return $array;
	}

	update_option( 'formlift_web_forms', $array );
	FormLift_Notice_Manager::add_success( "refresh_success", "Successfully retrieved new web forms." );

	return $array;
}

function formlift_get_infusionsoft_webforms() {

	$forms = get_option( 'formlift_web_forms', array() );

	if ( ! empty( $forms ) ) {
		return $forms;
	} else {
		return _formlift_update_web_form_list();
	}
}

/**
 * @param $id
 *
 * @return false|mixed
 */
function formlift_get_webform_name( $id ){
	$list = formlift_get_infusionsoft_webforms();

	return isset( $list[ $id ] ) ? $list[ $id ] : false;
}

function get_formlift_html( $id ) {
	$code = FormLift_Infusionsoft_Manager::getWebFormHtml( $id );

	if ( is_wp_error( $code ) ) {
		FormLift_Notice_Manager::add_notice( 'oauth_error', array(
			'is_dismissable' => true,
			'is_premium'     => 'both',
			'is_specific'    => false,
			'type'           => 'notice-error',
			'html'           => 'Something went wrong pulling the webform html. Try manually refreshing your connection in the settings. If the problem persists copy the following and send it to <a href="mailto:info@formlift.net">info@formlift.net</a>. Error: ' . $code->get_error_message()
		) );

		return '';
	}

	return $code;
}

function formlift_is_connected() {
	if ( FormLift_Infusionsoft_Manager::$app->hasTokens() ) {
		return true;
	} else {
		$app_name = get_formlift_setting( 'infusionsoft_app_name' );
		$key      = get_formlift_setting( 'infusionsoft_api_key' );

		return ! empty( $app_name ) && ! empty( $key );
	}
}

function formlift_convert_to_time_picker_usuable( $date_string ) {
	if ( $date_string == date( 'Y-m-d', strtotime( $date_string ) ) ) {
		return "new Date( '{$date_string}' )";
	} else {
		return "'$date_string'";
	}
}

function get_formlift_field_types() {
	$options = array(
		'Standard'   => array(
			'hidden'      => 'Hidden Field',
			'text'        => 'Text Field',
			'textarea'    => 'Text Area',
			'name'        => 'Name',
			'email'       => 'Email',
			'date'        => 'Date Picker',
			'phone'       => 'Phone Number',
			'number'      => 'Whole Number',
			'postal_code' => 'Postal Code',
			'zip_code'    => 'Zip Code',
			'website'     => 'Website',
			'select'      => 'Dropdown',
			'listbox'     => 'List Box',
			'radio'       => 'Radio Buttons',
			'checkbox'    => 'Checkbox'
		),
		'Additional' => array(
			'GDPR'     => 'GDPR Compliance',
			'password' => 'Password',
			'custom'   => 'Custom HTML',
			'button'   => 'Submit Button'
		)
	);

	$options = apply_filters( "formlift_add_field_types", $options );

	return $options;
}

function get_formlift_field_type_name( $name ) {
//    echo $name;
	$types = get_formlift_field_types();
	$type  = array_column( $types, $name );

	return array_pop( $type );
}

function formlift_get_custom_fields() {
	$returnFields = get_transient( 'formlift_custom_fields' );

	if ( $returnFields ) {
		return $returnFields;
	}

	$customFields = FormLift_Infusionsoft_Manager::getCustomFields();

	if ( is_wp_error( $customFields ) ) {
		$customFields = array();
	}

	$returnFields = array(
		'' => 'Please Select One'
	);

	foreach ( $customFields as $fieldRow ) {
		$returnFields[ '_' . $fieldRow['Name'] ] = $fieldRow['Label'];
	}

	set_transient( 'formlift_custom_fields', $returnFields, 24 * HOUR_IN_SECONDS );

	return $returnFields;
}

function formlift_refresh_custom_fields() {
	if ( isset( $_POST[ FORMLIFT_SETTINGS ]['refresh_custom_fields'] ) ) {
		delete_transient( 'formlift_custom_fields' );
	}
}

add_action( 'formlift_after_save_plugin_settings', 'formlift_refresh_custom_fields' );

function formlift_encrypt_decrypt( $string, $action = 'e' ) {
	// you may change these values to your own
	$encrypt_method = "AES-256-CBC";
	if ( in_array( $encrypt_method, openssl_get_cipher_methods() ) ) {
		$secret_key = admin_url();
		$secret_iv  = parse_url( site_url(), PHP_URL_HOST );

		$output = false;
		$key    = hash( 'sha256', $secret_key );
		$iv     = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		if ( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		} else if ( $action == 'd' ) {
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
	} else {
		if ( $action == 'e' ) {
			$output = base64_encode( $string );
		} else if ( $action == 'd' ) {
			$output = base64_decode( $string );
		}
	}

	return $output;
}

function formlift_isJson( $string ) {
	json_decode( $string );

	return ( json_last_error() == JSON_ERROR_NONE );
}

function formlift_get_leaderboard_position() {
	$url = get_site_url();

	$args = array(
		'url' => $url
	);

	$destination = add_query_arg( array( 'formlift_action' => 'get_leaderboard_position' ), FormLift_Stats_Collector::DEST );

	$result = wp_remote_post( $destination, array( 'body' => $args, 'sslverify' => true ) );

	$args = json_decode( wp_remote_retrieve_body( $result ), true );

	update_option( 'formlift_leaderboard_rank', $args['placement'] );

	return $args['placement'];
}

function formlift_get_leaderboard_position_ajax() {
	if ( ! wp_doing_ajax() ) {
		return;
	}

	$placement = formlift_get_leaderboard_position();

	wp_die( $placement );
}

add_action( 'wp_ajax_formlift_get_leaderboard_ranking', 'formlift_get_leaderboard_position_ajax' );