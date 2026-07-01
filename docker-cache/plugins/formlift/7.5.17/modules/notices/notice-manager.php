<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * listener class that stores all admin notices and runs them sequentially when admin_notices is called
 */
class FormLift_Notice_Manager {
	static $notice_option = 'formlift_notices';
	static $used_notices = 'formlift_used_notices';

	public static function init() {
		add_action( 'admin_notices', array( 'FormLift_Notice_Manager', 'run' ) );
		add_action( 'wp_ajax_dismiss_formlift_notice', array( 'FormLift_Notice_Manager', 'do_dismiss' ) );
	}

	public static function is_forever_dismissed( $id ) {
		$used_notices = get_option( static::$used_notices, array() );

		return isset( $used_notices[ $id ] );
	}

	public static function dismiss_forever( $id ) {
		$used_notices        = get_option( static::$used_notices, array() );
		$used_notices[ $id ] = true;
		update_option( static::$used_notices, $used_notices );
	}

	/**
	 * API for the dismissing of notices
	 *
	 * @param $options
	 */
	public static function do_dismiss() {
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) && isset( $_POST['id'] ) ) {
			self::remove_notice( $_POST['id'] );
			wp_die( 'Success' );
		} else {
			wp_die( 'Nice try, but you aren\'t allowed to do this' );
		}
	}

	/**
	 * displays the notices
	 *
	 * @return void
	 */
	public static function run() {
		if ( get_option( self::$notice_option ) ) {
			$notices = get_option( self::$notice_option );

			if ( ! is_array( $notices ) ) {
				echo $notices;
				delete_option( self::$notice_option );

				return;
			}
			foreach ( $notices as $notice_id => $args ) {
				echo new FormLift_Notice( $notice_id, $args['type'], $args['html'], $args['is_dismissable'], $args['is_specific'], $args['is_premium'] );
			}

			update_option( self::$notice_option, [] );
		}
	}

	public static function add_notice( $id, $args ) {
		$notices = get_option( self::$notice_option, array() );

		if ( self::is_forever_dismissed( $id ) ) {
			return false;
		}

		$defaults = array(
			'is_dismissable' => true,
			'show_once'      => false,
			'is_premium'     => 'both',
			'is_specific'    => false,
			'type'           => 'notice-info',
			'html'           => 'Hello',
		);

		$results = array_merge( $defaults, $args );

		$notices[ $id ] = $results;

		update_option( self::$notice_option, $notices );

		return new FormLift_Notice( $id, $results['type'], $results['html'], $results['is_dismissable'], $results['is_specific'], $results['is_premium'] );
	}

	public static function remove_notice( $id ) {
		$notices = get_option( self::$notice_option );

		if ( isset( $notices[ $id ] ) ) {
			if ( isset( $notices[ $id ]['show_once'] ) && $notices[ $id ]['show_once'] == true ) {
				self::dismiss_forever( $id );
			}
			unset( $notices[ $id ] );
			update_option( self::$notice_option, $notices );
		}
	}

	public static function add_error( $id, $message ) {
		return self::add_notice( $id, array(
			'is_dismissable' => true,
			'show_once'      => false,
			'is_premium'     => 'both',
			'is_specific'    => true,
			'type'           => 'notice-error',
			'html'           => 'Something went wrong. ' . $message
		) );
	}

	public static function add_info( $id, $message ) {
		return self::add_notice( $id, array(
			'is_dismissable' => true,
			'show_once'      => false,
			'is_premium'     => 'both',
			'is_specific'    => true,
			'type'           => 'notice-info',
			'html'           => 'Attention! ' . $message
		) );
	}

	public static function add_success( $id, $message ) {
		return self::add_notice( $id, array(
			'is_dismissable' => true,
			'show_once'      => false,
			'is_premium'     => 'both',
			'is_specific'    => false,
			'type'           => 'notice-success',
			'html'           => $message
		) );
	}

	public static function add_scripts() {
		wp_enqueue_style( 'formlift-notices', plugins_url( 'assets/css/style.css', __FILE__ ), array(), FORMLIFT_CSS_VERSION );
		wp_enqueue_script( 'formlift-notices', plugins_url( 'assets/js/notice-functions.js', __FILE__ ), array(), FORMLIFT_JS_VERSION );
	}
}

add_action( 'formlift_loaded', array( 'FormLift_Notice_Manager', 'init' ) );
add_action( 'admin_enqueue_scripts', array( 'FormLift_Notice_Manager', 'add_scripts' ) );