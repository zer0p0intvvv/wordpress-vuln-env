<?php
	
if (!class_exists('ReCaptcha')) {
	class ReCaptcha extends wpMailPlugin {

		var $api_url = 'https://www.google.com/recaptcha/api/siteverify';
		var $secret = '';
		var $errors = array();

		function __construct($secret = null) {
			$this->secret = $secret;
		}

		function verify($response = null, $ip_address = null) {
			global $Html;

			$ip_address = (empty($ip_address)) ? $_SERVER['REMOTE_ADDR'] : $ip_address;

			$args = array(
				'secret'    => $this->secret,
				'response'  => $response,
				'remoteip'  => $ip_address,
			);

			$response = wp_remote_post($this->api_url, array('body' => $args, 'timeout' => 120));

			// Default return object
			$result = (object) array(
				'success' => false,
				'score' => null,
				'action' => null,
				'error_codes' => array(),
			);

			if (!is_wp_error($response)) {
				if (!empty($response['body'])) {
					$body = json_decode($response['body']);

					if (isset($body->success)) {
						$result->success = $body->success;
						$result->score = isset($body->score) ? floatval($body->score) : null; // v3-specific
						$result->action = isset($body->action) ? $body->action : null; // v3-specific

						if (!$body->success) {
							if (!empty($body->{'error-codes'})) {
								$result->error_codes = $body->{'error-codes'};
								foreach ($body->{'error-codes'} as $error_code) {
									$this->errors[] = $Html->reCaptchaErrorMessage($error_code);
								}
							} else {
								$this->errors[] = __('CAPTCHA failed, try again', 'wp-mailinglist');
								$result->error_codes[] = 'unknown-error';
							}
						}
					} else {
						$this->errors[] = __('Invalid CAPTCHA response format', 'wp-mailinglist');
						$result->error_codes[] = 'invalid-response';
					}
				} else {
					$this->errors[] = __('CAPTCHA response was empty', 'wp-mailinglist');
					$result->error_codes[] = 'empty-response';
				}
			} else {
				$this->errors[] = $response->get_error_message();
				$result->error_codes[] = 'http-error';
			}

			return $result;
		}

		// Helper methods for compatibility
		function isSuccess() {
			$result = $this->verify($this->last_response, $this->last_ip);
			return $result->success;
		}

		function getScore() {
			$result = $this->verify($this->last_response, $this->last_ip);
			return $result->score;
		}

		function getErrorCodes() {
			$result = $this->verify($this->last_response, $this->last_ip);
			return $result->error_codes;
		}
	}
}


?>