<?php

	class Mo_lla_RegistrationHandler
	{
		function __construct()
		{
			add_filter( 'registration_errors' , array($this, 'mo_wpns_registration_validations' ), 10, 3 );			
		}

		function mo_wpns_registration_validations( $errors, $sanitized_user_login, $user_email ) 
		{
			global $moWpnsUtility;

			if(get_option('mo_wpns_activate_recaptcha_for_registration')){
                if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v3')
                    $recaptchaError = $moWpnsUtility->verify_recaptcha_3(sanitize_text_field($_POST['g-recaptcha-response']));
                else if(get_option('mo_wpns_recaptcha_version')=='reCAPTCHA_v2')
                    $recaptchaError = $moWpnsUtility->verify_recaptcha(sanitize_text_field($_POST['g-recaptcha-response']));
                if(!empty($recaptchaError->errors))
                $errors = $recaptchaError;
            }

			if($moWpnsUtility->check_if_valid_email($user_email) && empty($recaptchaError->errors))
				$errors->add( 'blocked_email_error', __( '<strong>ERROR</strong>: Your email address is not allowed to register. Please select different email address.') );
			else if(!empty($recaptchaError->errors))
				$errors = $recaptchaError;
				
			return $errors;
		}

	}
	new Mo_lla_RegistrationHandler;