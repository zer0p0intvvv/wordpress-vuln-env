<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once ABSPATH . '/wp-includes/class-IXR.php';
require_once ABSPATH . '/wp-includes/class-wp-http-ixr-client.php';

class FormLift_App {
	protected $hostname;
	protected $apiKey;

	protected $accessToken;
	protected $refreshToken;
	protected $expiresIn;

	var $logger;


	public function __construct( $hostname = '' ) {

		if ( strpos( $hostname, "." ) === false ) {
			$hostname = $hostname . '.infusionsoft.com';
		} elseif ( empty( $hostname ) ) {
			$hostname = $this->getHostname();
		}

		$this->logger = new FormLift_Api_Log( __DIR__ . '/api_log' );

		$this->hostname     = $hostname;
		$this->accessToken  = $this->getAccessToken();
		$this->refreshToken = $this->getRefreshToken();
		$this->apiKey       = $this->getApiKey();
	}

	public function getApiKey() {
		if ( ! empty( $this->apiKey ) ) {
			return $this->apiKey;
		} else {
			$this->apiKey = get_formlift_setting( 'infusionsoft_api_key', $this->getAccessToken() );

			return $this->apiKey;
		}
	}

	public function getAccessToken() {
		if ( $this->accessToken ) {
			return $this->accessToken;
		} else {
			$tokens = get_option( 'FormLift_Oauth_Tokens' );
			if ( $tokens && isset( $tokens['accessToken'] ) ) {
				return $tokens['accessToken'];
			} else {
				return null;
			}
		}
	}

	public function getRefreshToken() {
		if ( $this->refreshToken ) {
			return $this->refreshToken;
		} else {
			$tokens = get_option( 'FormLift_Oauth_Tokens', false );
			if ( $tokens && isset( $tokens['refreshToken'] ) ) {
				return $tokens['refreshToken'];
			} else {
				return null;
			}
		}
	}

	public function getHostname() {
		if ( $this->hostname ) {
			return $this->hostname;
		} else {
			$tokens = get_option( 'FormLift_Oauth_Tokens', false );
			if ( $tokens && isset( $tokens['appDomain'] ) ) {
				return $tokens['appDomain'];
			} else {
				return null;
			}
		}
	}

	/**
	 * @param $method string
	 * @param $args   array
	 *
	 * @return mixed
	 */
	public function send( $method, $args ) {
		array_unshift( $args, $this->getApiKey() );
		array_unshift( $args, $method );

		if ( empty( $this->accessToken ) && ! empty( $this->apiKey ) ) {
			$client = new WP_HTTP_IXR_Client( 'https://' . $this->getHostname() . '/api/xmlrpc' );
		} elseif ( ! empty( $this->accessToken ) ) {
			$client = new WP_HTTP_IXR_Client( add_query_arg( "access_token", $this->accessToken, "https://api.infusionsoft.com/crm/xmlrpc/v1" ) );
		} else {
			return new WP_Error( "NO_CONNECTION", "Please set an Infusionsoft connection in the settings." );
		}

		// Call the function and return any error that happens
		if ( ! call_user_func_array( array( $client, 'query' ), $args ) ) {
			/* refresh and try again */
			$this->refreshTokens();
			//re-init client.
			$client = new WP_HTTP_IXR_Client( add_query_arg( "access_token", $this->accessToken, "https://api.infusionsoft.com/crm/xmlrpc/v1" ) );

			if ( ! call_user_func_array( array( $client, 'query' ), $args ) ) {
				/* finished retries now throw error */

				$this->logger->log( "Method: {$method}, Response: {$client->error->message}" );

				do_action( 'formlift_api_call_failed' );

				return new WP_Error( "BAD_REQUEST", $client->error->message );
			}
		}

		// Pass the response directly to the user
		$response = $client->getResponse();

		$this->logger->log( "Method: {$method}, Response: 200 OK" );

		do_action( 'formlift_api_goal_succeeded' );

		return $response;
	}

	public function hasTokens() {
		return $this->accessToken != '' && $this->refreshToken != '';
	}

	public function refreshTokens() {
		$tokens = FormLift_Infusionsoft_Manager::refreshTokens( $this->refreshToken );

		if ( $tokens ) {
			$this->updateAndSaveTokens( $tokens['access_token'], $tokens['refresh_token'], $tokens['expires_in'] );
		} else {
			wp_mail(
				get_option( 'admin_email' ),
				'FormLift failed to connect.',
				'FormLift failed to refresh the Oauth tokens provided by Infusionsoft. You may have to manually re-connect to Infusionsoft to be on the safe side.
                Click the link to go to the settings page now! <' . admin_url( 'edit.php?post_type=infusion_form&page=formlift_settings_page' ) . '>'
			);
		}
	}

	public function updateAndSaveTokens( $accessToken, $refreshToken, $expiresIn ) {
		$args = array(
			'appDomain'    => $this->getHostname(),
			'accessToken'  => $accessToken,
			'refreshToken' => $refreshToken,
			'expiresAt'    => time() + $expiresIn,
		);

		update_option( 'FormLift_Oauth_Tokens', $args );

		$this->accessToken  = $accessToken;
		$this->refreshToken = $refreshToken;
		$this->expiresIn    = time() + $expiresIn;
	}

	public function deleteTokens() {
		$this->accessToken  = null;
		$this->refreshToken = null;
		delete_option( 'FormLift_Oauth_Tokens' );
	}
}