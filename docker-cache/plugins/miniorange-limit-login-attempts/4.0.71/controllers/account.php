<?php 
	
	global $moWpnsUtility,$mo_lla_dirName;
	$register_obj = new Mo_lla_MoWpnsUtility();

	if ( current_user_can( 'manage_options' ) and isset( $_POST['option'] ) )
	{
		$option = trim($_POST['option']);
		switch($option)
		{
			case "mo_wpns_register_customer":
				_register_customer($_POST);																	   break;
			case "mo_wpns_verify_customer":
				_verify_customer($_POST);																	   break;
			case "mo_wpns_validate_otp":
				_validate_otp($_POST);																		   break;
			case "mo_wpns_resend_otp":
				_send_otp_token(get_option('mo_wpns_admin_email'),"",'EMAIL');  break;
			case "mo_wpns_phone_verification":
				_send_phone_otp_token($_POST);													  			   break;
			case "mo_wpns_cancel":
				_revert_back_registration();																   break;
			case "mo_wpns_reset_password":
				_reset_password();																			   break;
		}
	} 

 
	if(get_option('mo_wpns_registration_status') == 'MO_OTP_DELIVERED_SUCCESS' 
		|| get_option('mo_wpns_registration_status')  == 'MO_OTP_VALIDATION_FAILURE' 
		|| get_option('mo_wpns_registration_status')  == 'MO_OTP_DELIVERED_FAILURE')
	{
		$admin_phone = get_option('mo_wpns_admin_phone') ? get_option('mo_wpns_admin_phone') : "";
		include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'verify.php';
	} 
	else if (get_option ( 'mo_wpns_verify_customer' ) == 'true' || (get_option('mo_wpns_admin_email') && !get_option('mo_wpns_admin_customer_key')))
	{
		$admin_email = get_option('mo_wpns_admin_email') ? get_option('mo_wpns_admin_email') : "";		
		include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'login.php';
	}
	else if (! $register_obj->icr()) 
	{
		delete_option ( 'password_mismatch' );
		update_option ( 'mo_wpns_new_registration', 'true' );
		$current_user 	= wp_get_current_user();
		include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'register.php';
	} 
	else
	{
		$email = get_option('mo_wpns_admin_email');
		$key   = get_option('mo_wpns_admin_customer_key');
		$api   = get_option('mo_wpns_admin_api_key');
		$token = get_option('mo_wpns_customer_token');
		include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'account'.DIRECTORY_SEPARATOR.'mo_lla_profile.php';
	}





	/* REGISTRATION RELATED FUNCTIONS */

	//Function to register new customer
	function _register_customer($post)
	{
		//validate and sanitize
		global $moWpnsUtility;
		$email 			 = sanitize_email($post['email']);
		$company 		 = sanitize_text_field($post['companyName']);
		$first_name 	 = sanitize_text_field($post['firstName']);
		$last_name 		 = sanitize_text_field($post['lastName']);
		$phone 			 = sanitize_text_field($post['phone']);
		$password 		 = sanitize_text_field($post['password']);
		$confirmPassword = sanitize_text_field($post['confirmPassword']);

		if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6)
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('PASS_LENGTH'),'ERROR');
			return;
		}
		
		if( $password != $confirmPassword )
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('PASS_MISMATCH'),'ERROR');
			return;
		}

		if( $moWpnsUtility->check_empty_or_null( $email ) || $moWpnsUtility->check_empty_or_null( $password ) 
			|| $moWpnsUtility->check_empty_or_null( $confirmPassword ) ) 
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		} 

		update_option( 'mo_wpns_admin_email', $email );
		update_option( 'mo_wpns_admin_phone', $phone );
		update_option( 'mo_wpns_company'    , $company );
		update_option( 'mo_wpns_firstName'  , $first_name );
		update_option( 'mo_wpns_lastName'   , $last_name );
		update_option( 'mo_wpns_password'   , $password );

		$customer = new Mo_lla_MocURL();
		$content  = json_decode($customer->check_customer($email), true);
		switch ($content['status'])
		{
			case 'CUSTOMER_NOT_FOUND':
				_send_otp_token($email,"",'EMAIL');
				break;
			default:
				_get_current_customer($email,$password);
				break;
		}

	}


	//Function to go back to the registration page
	function _revert_back_registration()
	{
		delete_option('mo_wpns_admin_email');
		delete_option('mo_wpns_registration_status');
		delete_option('mo_wpns_verify_customer');
	}


	//Function to reset customer's password
	function _reset_password()
	{
		$customer = new Mo_lla_MocURL();
		$forgot_password_response = json_decode($customer->mo_wpns_forgot_password());
		if($forgot_password_response->status == 'SUCCESS')
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('RESET_PASS'),'SUCCESS');
	}


	//Function to verify customer
	function _verify_customer($post)
	{
		global $moWpnsUtility;
		$email 	  = sanitize_email( $post['email'] );
		$password = sanitize_text_field( $post['password'] );

		if( $moWpnsUtility->check_empty_or_null( $email ) || $moWpnsUtility->check_empty_or_null( $password ) ) 
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('REQUIRED_FIELDS'),'ERROR');
			return;
		} 
		_get_current_customer($email,$password);
	}


	//Function to validate OTP
	function _validate_otp($post)
	{
		global $moWpnsUtility;
		$otp_token 		 = sanitize_text_field( $post['otp_token'] );
		$email 			 = get_option( 'mo_wpns_admin_email');
		$company 		 = get_option('mo_wpns_company');
		$first_name 	 = get_option('mo_wpns_firstName');
		$last_name 		 = get_option('mo_wpns_lastName');
		$phone 			 = get_option('mo_wpns_admin_phone');
		$password 		 = get_option('mo_wpns_password');
					
		if( $moWpnsUtility->check_empty_or_null( $otp_token ) ) 
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('REQUIRED_OTP'),'ERROR');
			update_option('mo_wpns_registration_status','MO_OTP_VALIDATION_FAILURE');
			return;
		} 

		$customer = new Mo_lla_MocURL();
		$content = json_decode($customer->validate_otp_token(get_option('mo_wpns_transactionId'), $otp_token ),true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0) 
		{
			$customerKey = json_decode($customer->create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = ''), true);
			if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) 
			{	
				_get_current_customer($email,$password);
			} 
			else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) 
			{
				save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
				do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('REG_SUCCESS'),'SUCCESS');
			}
		} 
		else
		{
			update_option('mo_wpns_registration_status','MO_OTP_VALIDATION_FAILURE');
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('INVALID_OTP'),'ERROR');
		}
	}


	///Function to send otp token to phone
	function _send_phone_otp_token($post)
	{
		$phone = sanitize_text_field($_POST['phone_number']);
		$phone = str_replace(' ', '', $phone);
		$pattern = "/[\+][0-9]{1,3}[0-9]{10}/";					
		if(preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE))
		{
			update_option('mo_wpns_admin_phone',$phone);
			_send_otp_token("",$phone,'PHONE');
		}
		else
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('INVALID_PHONE'),'ERROR');
	}


	//Function to send OTP token
	function _send_otp_token($email,$phone,$auth_type)
	{
		$customer = new Mo_lla_MocURL();
		$content  = json_decode($customer->send_otp_token($auth_type,$phone,$email), true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0) 
		{
			update_option('mo_wpns_transactionId',$content['txId']);
			update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_SUCCESS');
			if($auth_type=='EMAIL')
				do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('OTP_SENT',array('method'=>$email)),'SUCCESS');
			else
				do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('OTP_SENT',array('method'=>$phone)),'SUCCESS');
		} 
		else
		{
			update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_FAILURE');
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('ERR_OTP_EMAIL'),'ERROR');
		}
	}


	//Function to get customer details
	function _get_current_customer($email,$password)
	{
		$customer 	 = new Mo_lla_MocURL();
		$content     = $customer->get_customer_key($email,$password);
		$customerKey = json_decode($content, true);
		if(json_last_error() == JSON_ERROR_NONE) 
		{
			update_option( 'mo_wpns_admin_phone', $customerKey['phone'] );
			save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('REG_SUCCESS'),'SUCCESS');
		} 
		else 
		{
			update_option('mo_wpns_verify_customer', 'true');
			delete_option('mo_wpns_new_registration');
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('ACCOUNT_EXISTS'),'ERROR');
		}
	}
	
		
	//Save all required fields on customer registration/retrieval complete.
	function save_success_customer_config($id, $apiKey, $token, $appSecret)
	{
		update_option( 'mo_wpns_admin_customer_key'  , $id 		  );
		update_option( 'mo_wpns_admin_api_key'       , $apiKey    );
		update_option( 'mo_wpns_customer_token'		 , $token 	  );
		update_option( 'mo_wpns_app_secret'			 , $appSecret );
		update_option( 'mo_wpns_enable_log_requests' , true 	  );
		update_option( 'mo_wpns_password'			 , ''		  );
		delete_option( 'mo_wpns_verify_customer'				  );
		delete_option( 'mo_wpns_registration_status'			  );
		delete_option( 'mo_wpns_password'						  );
	}