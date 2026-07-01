<?php
/**
 * Cloudflare Turnstile verification helper
 * Mirrors the signature of vendors/recaptcha/ReCaptcha.php
 */
if ( ! class_exists( 'Turnstile' ) ) {

	class Turnstile extends wpMailPlugin {

		/** @var string */
		protected $api_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

		/** @var string */
		protected $secret  = '';

		/** @var array */
		public    $errors  = array();

		public function __construct( $secret = null ) {
			$this->secret = $secret;
		}

		/**
		 * @param string|null $response token from browser
		 * @param string|null $ip_address
		 *
		 * @return object { success:boolean, error_codes:array }
		 */
		public function verify( $response = null, $ip_address = null ) {

			$ip_address = empty( $ip_address ) ? $_SERVER['REMOTE_ADDR'] : $ip_address;

			$args = array(
				'secret'   => $this->secret,
				'response' => $response,
				'remoteip' => $ip_address,
			);

			$request = wp_remote_post( $this->api_url, array(
				'body'    => $args,
				'timeout' => 60,
			) );

			$result = (object) array(
				'success'     => false,
				'error_codes' => array(),
			);

			if ( ! is_wp_error( $request ) && ! empty( $request['body'] ) ) {
				$body = json_decode( $request['body'], true );

				if ( isset( $body['success'] ) && true === $body['success'] ) {
					$result->success = true;
				} else {
					$result->error_codes = ! empty( $body['error-codes'] ) ? (array) $body['error-codes'] : array( 'unknown-error' );
					$this->errors[]      = __( 'Turnstile verification failed.', 'wp-mailinglist' );
				}
			} else {
				$result->error_codes = array( 'http-error' );
				$this->errors[]      = __( 'Unable to reach Turnstile verify endpoint.', 'wp-mailinglist' );
			}

			return $result;
		}
	}
}