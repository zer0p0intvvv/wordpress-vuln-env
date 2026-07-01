<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class FormLift_User
 */
class FormLift_User {
	/**
	 * @var $instance FormLift_User
	 */
	var $ID;
	var $attributes;
	var $key;
	var $iv;

	static $instance;

	function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ], 2 );
	}

	function init() {
		//Do not create a session if the person is a bot.
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
			return;
		}

		if ( ! isset( $_COOKIE['FORMLIFT_ID'] ) ) {

			if ( isset( $_GET['formlift_session'] ) ) {
				$this->ID = urldecode( $_GET['formlift_session'] );
			} else {
				$this->ID = uniqid( 'formlift_session_', true );
			}

			$expiresInDays = get_formlift_setting( 'time_to_live', 30 );
			setcookie( 'FORMLIFT_ID', $this->ID, time() + $expiresInDays * 24 * HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		} else {
			$this->ID = sanitize_text_field( $_COOKIE['FORMLIFT_ID'] );
		}

		$encodedAttributes = formlift_get_session( $this->ID );
		if ( $encodedAttributes ) {
			$decodedAttributes = json_decode( formlift_encrypt_decrypt( $encodedAttributes, 'd' ), true );
			$this->attributes  = ( ! empty( $decodedAttributes ) && is_array( $decodedAttributes ) ) ? $decodedAttributes : array();
		} else {
			$this->attributes = array();
		}
	}

	function getId() {
		return $this->ID;
	}

	function getData( $name, $default = false ) {
		return $this->get_user_data( $name, $default );
	}

	function setData( $field, $data ) {
		return $this->set_user_data( $field, $data );
	}

	function set_user_data( $field, $data ) {
		$this->attributes[ $field ] = $data;

		return true;
	}

	function remove_user_data( $field ) {
		if ( isset( $this->attributes[ $field ] ) ) {
			unset ( $this->attributes[ $field ] );
		}
	}

	/* exists just because it has a better name */
	function get_user_data( $field, $default = false ) {

		if ( isset( $this->attributes[ $field ] ) ) {
			$data = $this->attributes[ $field ];

			return $data;
		} else if ( is_user_logged_in() ) {
			return $this->get_user_data_from_wp( $field, $default );
		} else {
			return $default;
		}
	}

	function addImpression( $formID ) {
		$this->set_user_data( $formID . '-impression', $formID );
		$this->update();
	}

	function addSubmission( $formID ) {
		$this->set_user_data( $formID . '-submission', $formID );
		$this->update();
	}

	function hasImpression( $formID ) {
		return $this->get_user_data( $formID . '-impression' ) == $formID;
	}

	function hasSubmission( $formID ) {
		return $this->get_user_data( $formID . '-submission' ) == $formID;
	}

	function update() {
		if ( get_formlift_setting( "disable_session_storage" ) || empty( $this->ID ) ) {
			return;
		}

		$expiresInDays = intval( get_formlift_setting( 'time_to_live', 30 ) );

		if ( formlift_get_session( $this->ID ) ) {
			formlift_update_session( $this->ID, formlift_encrypt_decrypt( json_encode( $this->attributes ) ), $expiresInDays * 24 * HOUR_IN_SECONDS );
		} else {
			formlift_create_session( $this->ID, formlift_encrypt_decrypt( json_encode( $this->attributes ) ), $expiresInDays * 24 * HOUR_IN_SECONDS );
		}
	}

	function get_user_data_from_wp( $field, $default ) {
		/* because I'm lazy I'll only serve certain fields*/
		$user = wp_get_current_user();

		if ( "inf_field_Email" == $field ) {
			return $user->user_email;
		} elseif ( "inf_field_FirstName" == $field ) {
			return $user->user_firstname;
		} elseif ( "inf_field_LastName" == $field ) {
			return $user->user_lastname;
		} elseif ( "inf_field_Username" == $field ) {
			return $user->user_login;
		} else {
			//added for memberium support
			return apply_filters( 'formlift_get_user_data', $default, $field );
		}

	}

	function sanitize_headers() {

		$do = false;
		// check for doing a redirect
		if ( ! empty( $_GET ) && ! isset( $_GET['formlift_action'] ) && ! isset( $_GET['form_action'] ) ) {
			if ( preg_match( '/inf_custom/', $_SERVER['QUERY_STRING'] ) || preg_match( '/inf_other/', $_SERVER['QUERY_STRING'] ) || preg_match( '/contactId/', $_SERVER['QUERY_STRING'] ) || preg_match( '/inf_field/', $_SERVER['QUERY_STRING'] ) ) {
				$do = true;
			}
		}

		if ( ! $do ) {
			return;
		}

		$filters = get_formlift_setting( 'exclude_from_utm_removal' );
		$filters = explode( PHP_EOL, $filters );
		$filters = array_map( 'trim', $filters );

		foreach ( $_GET as $key => $value ) {
			$unfiltered = urldecode( $value );
			/* special case for emails from the $_GET*/
			if ( preg_match( '/inf_field_Email[2-3]?/', $key ) ) {
				$filtered = str_replace( ' ', '+', $unfiltered );
			} else {
				$filtered = $unfiltered;
			}

			$this->set_user_data( sanitize_text_field( $key ), sanitize_textarea_field( $filtered ) );
			//remove special case for inf_contact_key
			if ( ! get_formlift_setting( 'disable_utm_removal', false ) ) {
				if ( ( preg_match( '/inf_custom/', $key ) || preg_match( '/inf_other/', $key ) || preg_match( '/inf_field/', $key ) ) && ! in_array( $key, $filters ) ) {
					unset( $_GET[ $key ] );
				}
			}
		}

		$uri = preg_replace( "/\?.*/", '', $_SERVER['REQUEST_URI'] );
		//redirect to a clean version of the URL to protect user data.

		if ( isset( $_GET['contactId'] ) ) {
			$this->set_user_data( "contactId", intval( $_GET["contactId"] ) );
		}

		$this->update();
		$this->update_contact_with_recovery_id();

		do_action( 'formlift_after_sanitize_url' );

		if ( ! get_formlift_setting( 'disable_utm_removal', false ) ) {
			if ( empty( $_GET ) ) {
				wp_redirect( $uri );
				die();
			} else {
				$_GET['formlift_action'] = 'url_cleaned';
				wp_redirect( $uri . "?" . http_build_query( $_GET ) );
				die();
			}
		}
	}

	function update_contact_with_recovery_id() {
		$customField = get_formlift_setting( 'session_storage_field', false );

		if ( ! $customField || ! $this->getData( 'contactId', false ) ) {
			return;
		}

		$data = array(
			$customField => $this->getId()
		);

		FormLift_Infusionsoft_Manager::updateContact( $this->getData( 'contactId' ), $data );
	}

	public static function db_extend( $field ) {
		if ( strpos( $field, "_" ) && ! strpos( $field, "inf_" ) ) {
			return "inf_custom" . $field;
		} else if ( ! strpos( $field, "inf_" ) && ! empty( $field ) ) {
			return "inf_field_" . $field;
		} else {
			return $field;
		}
	}

	public static function do_replacements( $content ) {
		global $FormLiftUser;

		preg_match_all( '/%%[\w\d]+%%/', $content, $matches );
		$actual_matches = $matches[0];

		foreach ( $actual_matches as $pattern ) {
			$field = str_replace( '%%', '', $pattern );

			$value = $FormLiftUser->get_user_data( $field );

			if ( empty( $value ) ) {
				$value = '';
			}

			$content = preg_replace( '/' . $pattern . '/', $value, $content );
		}

		return $content;
	}

	public static function display_field( $atts, $content ) {
		global $FormLiftUser;

		$atters = shortcode_atts( array(
			'name'       => '',
			'value'      => '',
			'default'    => '',
			'id'         => '',
			'everything' => false
		), $atts, 'formlift_data' );

		if ( $atters['everything'] ) {
			return $FormLiftUser;
		}

		if ( ! empty( $atters['id'] ) ) {
			$atters['name'] = $atters['id'];
		}

		if ( $content ) {
			if ( ! empty( $atters['name'] ) ) {
				$val = $FormLiftUser->get_user_data( $atters['name'] );
				if ( empty( $val ) ) {
					return '';
				} elseif ( ! empty( $atters['value'] ) && $val != $atters['value'] ) {
					return '';
				}
			}
			$content = self::do_replacements( $content );
			$content = do_shortcode( $content );
		} else {
			$val = $FormLiftUser->get_user_data( $atters['name'] );
			if ( empty( $val ) ) {
				return '';
			} elseif ( ! empty( $atters['value'] ) && $val != $atters['value'] ) {
				return '';
			}
			$content = $val;
		}

		return $content;
	}

	public function __toString() {
		$content = "<table><tbody>";
		foreach ( $this->attributes as $attribute => $value ) {
			$content .= "<tr><td>{$attribute}</td><td>{$value}</td></tr>";
		}
		$content .= "</table></tbody>";

		return $content;
	}
}

$FormLiftUser = new FormLift_User();

add_action( 'plugins_loaded', array( $FormLiftUser, 'sanitize_headers' ), 1 );
add_shortcode( 'infusion_field', array( 'FormLift_User', 'display_field' ) );
add_shortcode( 'formlift_user', array( 'FormLift_User', 'display_field' ) );
add_shortcode( 'formlift_data', array( 'FormLift_User', 'display_field' ) );