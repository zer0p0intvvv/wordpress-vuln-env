<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'FORMLIFT_EDD_SL_Plugin_Updater' ) ) {
	include __DIR__ . '/updater/FORMLIFT_EDD_SL_Plugin_Updater.php';
}

//define( 'FORMLIFT_LICENSES', 'formlift_licensed_modules' );
//define( 'FORMLIFT_MODULES', 'formlift_activate_modules' );

class FormLift_Module_Manager {
	static $modules = array(); // array( item_id => array( license, status ) )
	static $storeUrl = "https://formlift.net/store/";

	public static function add_module( $item_id, $license, $status, $item_name, $expiry ) {
		if ( empty( static::$modules ) ) {
			static::$modules = get_option( "formlift_modules", array() );
		}

		static::$modules[ $item_id ] = array(
			'license'   => $license,
			'status'    => $status,
			'item_name' => $item_name,
			'expiry'    => $expiry
		);

		return update_option( "formlift_modules", static::$modules );
	}

	public static function has_modules() {
		if ( empty( static::$modules ) ) {
			static::$modules = get_option( "formlift_modules", array() );
		}

		return ! empty( static::$modules );
	}

	public static function has_license( $item_id ) {
		if ( empty( static::$modules ) ) {
			static::$modules = get_option( "formlift_modules", array() );
		}

		return isset( static::$modules[ $item_id ]['license'] );
	}

	public static function get_license( $item_id ) {
		if ( empty( static::$modules ) ) {
			static::$modules = get_option( "formlift_modules", array() );
		}

		return static::$modules[ $item_id ]['license'];
	}

	public static function get_license_status( $item_id ) {
		if ( empty( static::$modules ) ) {
			static::$modules = get_option( "formlift_modules", array() );
		}

		return static::$modules[ $item_id ]['status'];
	}

	public static function update_license_status( $item_id, $status ) {
		static::$modules[ $item_id ]['status'] = $status;

		return update_option( "formlift_modules", static::$modules );
	}

	public static function perform_activation() {
		if ( isset( $_POST['formlift_activate_license'] ) && $_POST['formlift_activate_license'] == "Activate This Module" ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( "Cannot access this functionality" );
			}

			$license   = trim( $_POST['license'] );
			$item_id   = intval( trim( $_POST['item_id'] ) );
			$item_name = trim( $_POST['item_name'] );

			//wp_die( $item_id );

			self::activate_license( $license, $item_id, $item_name );
		}
	}

	public static function activate_license( $license, $item_id, $item_name ) {
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'    => $item_id,// The ID of the item in EDD,
			// 'item_name'  => $item_name,
			'url'        => home_url(),
			'beta'       => false
		);
		// Call the custom API.
		$response = wp_remote_post( static::$storeUrl, array( 'timeout'   => 15,
		                                                      'sslverify' => true,
		                                                      'body'      => $api_params
		) );
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$message = ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$message = __( 'Your license key has been disabled.' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $item_name );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.' );
						break;
					default :
						$message = __( 'An error occurred, please try again. Full response msg: ' . json_encode( $license_data ) );
						break;
				}
			}
		}
		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			FormLift_Notice_Manager::add_error( 'license-error-' . $item_id, $message . " License: " . $license );
			$status = 'invalid';
			$expiry = "no Data";
		} else {
			FormLift_Notice_Manager::add_success( 'license-success-' . $item_id, "License activated for " . $item_name );
			$status = 'valid';
			$expiry = $license_data->expires;
		}

		//wp_die( print_r( $license_data, true ) );

		self::add_module( $item_id, $license, $status, $item_name, $expiry );

		return $license_data->success;
	}

	public static function perform_verification() {
		if ( empty( static::$modules ) ) {
			static::$modules = get_option( "formlift_modules", array() );
		}

		if ( get_transient( 'formlift_license_verification' ) ) {
			return;
		} else {
			set_transient( 'formlift_license_verification', 'wait', 72 * HOUR_IN_SECONDS );
		}

		foreach ( static::$modules as $item_id => $args ) {
			if ( ! self::verify_license( $item_id, $args['item_id'], $args['license'] ) ) {
				FormLift_Notice_Manager::add_error( 'license-error-' . $item_id, "Your license for {$args['item_name']} is not longer valid." );
				wp_mail(
					get_option( 'admin_email' ),
					"Your license for {$args['item_name']} is not longer valid. Please login into your <a href='https://formlift.net/store/account/'>account</a> for more info. Or, contact info@formlift.net.",
					"Your license for {$args['item_name']} has expired or your billing has failed. Please login into https://formlift.net/store/account/ for more info. Or, contact info@formlift.net."
				);
			}
		}
	}

	public static function verify_license( $item_id, $item_name, $license ) {
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_id'    => $item_id,
			'url'        => home_url()
		);

		$response = wp_remote_post( static::$storeUrl, array( 'body'      => $api_params,
		                                                      'timeout'   => 15,
		                                                      'sslverify' => true
		) );

		if ( is_wp_error( $response ) ) {
			// return true in the event of an error. Check again later...
			return true;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( isset( $license_data->license ) && $license_data->license == 'invalid' ) {
			self::update_license_status( $item_id, 'invalid' );

			return false;
		}

		return true;
	}

	public static function add_this_page() {
		add_submenu_page(
			'edit.php?post_type=infusion_form',
			'Licenses',
			'Licenses',
			'manage_options',
			'formlift_modules',
			array( 'FormLift_Module_Manager', 'module_page' )
		);
	}

	public static function add_scripts() {
		wp_enqueue_style( 'formlift-modules-style', plugins_url( 'assets/css/style.css', __FILE__ ), array(), FORMLIFT_CSS_VERSION );
	}

	public static function module_page() {
		//use a filter instead of the member variable so that it goes away when plugin is deactivated.
		$modules = apply_filters( 'get_formlift_module_extensions', array() );

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">FormLift Extension Licenses:</h1>
			<div>
				<?php
				if ( empty( $modules ) ) {
					echo "<p>You have no active extensions installed. Want some to make FormLift even cooler? <a href='https://formlift.net/store/'>Check these out...</a></p>";
				}

				foreach ( $modules as $moduleId => $args ) {
					echo new FormLift_Module( $moduleId, $args );
				}
				?>
			</div>
		</div>
		<?php
	}
}

add_action( 'admin_menu', array( 'FormLift_Module_Manager', 'add_this_page' ) );
add_action( 'init', array( 'FormLift_Module_Manager', 'perform_activation' ) );
add_action( 'init', array( 'FormLift_Module_Manager', 'perform_verification' ) );
add_action( 'admin_enqueue_scripts', array( 'FormLift_Module_Manager', 'add_scripts' ) );

class FormLift_Module {
	var $item_id;
	var $item_name;
	var $img_source;
	var $description;

	function __construct( $item_id, $args ) {
		$this->item_id     = $item_id;
		$this->item_name   = $args['item_name'];
		$this->img_source  = $args['img_source'];
		$this->description = $args['description'];
	}

	public function license_exists() {
		return isset( FormLift_Module_Manager::$modules[ $this->item_id ] );
	}

	public function get_license() {
		return FormLift_Module_Manager::$modules[ $this->item_id ]['license'];
	}

	public function get_license_status() {
		return FormLift_Module_Manager::$modules[ $this->item_id ]['status'];
	}

	public function get_expiry() {
		return isset( FormLift_Module_Manager::$modules[ $this->item_id ]['expiry'] ) ? FormLift_Module_Manager::$modules[ $this->item_id ]['expiry'] : 'No data. See your account at FormLift.net';
	}

	public function __toString() {
		//head container
		$content = "<div class=\"formlift-module\">";
		//image
		$content .= "<div class=\"formlift-image-container\">";
		$content .= "<img class=\"formlift-module-image\" src=\"{$this->img_source}\">";
		$content .= "</div>";
		//description
		$content .= "<div class=\"formlift-description-container\">";
		$content .= "<h1>{$this->item_name}</h1>";
		$content .= "<p>{$this->description}</p>";
		$content .= "</div>";
		$content .= "<form method='post' action=''>";
		$content .= "<input type='hidden' name='item_id' value='{$this->item_id}'>";
		$content .= "<input type='hidden' name='item_name' value='{$this->item_name}'>";
		if ( $this->license_exists() ) {
			$content .= "<input type='text' style='margin-right: 10px;' placeholder='License' name='license' value='{$this->get_license()}'>";
		} else {
			$content .= "<input type='text' style='margin-right: 10px;' placeholder='License' name='license'>";
		}
		$content .= "<input type='submit' class='button button-primary' name='formlift_activate_license' value='Activate This Module'>";
		if ( $this->license_exists() ) {
			$content .= "<p>License is currently: {$this->get_license_status()}<br/>
Expires: {$this->get_expiry()}</p>";

		}
		$content .= "</form>";
		$content .= "</div>";

		return $content;
	}
}