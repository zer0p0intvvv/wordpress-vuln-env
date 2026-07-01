<?php 

	class Mo_lla_LoginHandler
	{
		function __construct()
		{
			add_action( 'init' , array( $this, 'mo_wpns_init' ) );

			if(get_option('mo_wpns_enforce_strong_passswords') || get_option('mo_wpns_activate_recaptcha_for_login')
                || get_option('mo_wpns_activate_recaptcha_for_woocommerce_login') || get_option('mo_wpns_enable_rename_login_url') || get_option('mo_wpns_enable_brute_force'))
            {
                remove_filter('authenticate'		 , 'wp_authenticate_username_password'				 ,20 );
                if(get_option('mo_wpns_enable_rename_login_url')){
                    add_filter   ('authenticate'		 , array( $this, 'rename_login_custom_authenticate'		       ) ,1, 3 );
                }else
                    add_filter   ('authenticate'		 , array( $this, 'custom_authenticate'		       ) ,1, 3 );
            }
			if(get_option('mo_wpns_enable_brute_force'))
			{
				add_action('wp_login'				 , array( $this, 'mo_wpns_login_success' 	       )		);
				add_action('wp_login_failed'		 , array( $this, 'mo_wpns_login_failed'	 	       ) 	    );
				
			}
            if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration') ){
				add_action( 'woocommerce_register_post', array( $this,'wooc_validate_user_captcha_register'), 1, 3);
			} 
		}

		function mo_wpns_init()
		{

            global $moWpnsUtility,$mo_lla_dirName;
			$WAFEnabled = get_option('WAFEnabled');
			$WAFLevel = get_option('WAF');
			if($WAFEnabled == 1)
			{
				if($WAFLevel == 'PluginLevel')
				{
					if(file_exists($mo_lla_dirName .'handler'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php'))
						include_once($mo_lla_dirName .'handler'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php');
					else
					{
						//Unable to find file. Please reconfigure.
					}
				}
			}
			
				$userIp 	= $moWpnsUtility->get_client_ip();
				$current_time = time();
				$wpns_database = new Mo_lla_MoWpnsDB;
				$wpns_count_ips_blocked = $wpns_database->get_time_of_block_ip($userIp);
				$mo_wpns_config = new Mo_lla_MoWpnsHandler();
				$isWhitelisted   = $mo_wpns_config->is_whitelisted($userIp);
				$isIpBlocked = false;
				if(!$isWhitelisted){
				$isIpBlocked = $mo_wpns_config->is_ip_blocked_in_anyway($userIp);
				}
	
				$mo_check_status_time = new Mo_lla_MoWpnsHandler();
				$mo_check_status_time->mollm_check_ip_duration();
				 if(!is_bool($isIpBlocked) && $isIpBlocked['status']){
				    $error_message = $isIpBlocked['message'] ;
				 	include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'403.php';
				 	exit;
				 }

				$requested_uri = $_SERVER["REQUEST_URI"];


            $option = false;

				if (is_user_logged_in())

				{
				    //chr?

                    $mo2f_url_login = (string) get_option('login_page_url', "false");
					if (strpos($requested_uri, $mo2f_url_login) != false) {
                        wp_safe_redirect(site_url()."/wp-admin/admin.php". ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );
						die;
					}



				} else {

					$option = get_option('mo_wpns_enable_rename_login_url');


				}
				if ($option) {
                    global $pagenow;
                    if (strpos($requested_uri, '/wp-login.php?checkemail=confirm') !== false) {
                        $requested_uri = str_replace("wp-login.php","",$requested_uri);
                        wp_redirect($requested_uri);
                        die;
                    } else if (strpos($requested_uri, '/wp-login.php?checkemail=registered') !== false) {
                        $requested_uri = str_replace("wp-login.php","",$requested_uri);
                        wp_redirect($requested_uri);
                        die;
                    }
                    if ($pagenow =='wp-login.php' && strpos($requested_uri, '/wp-login.php') !== false) {
                    
                        $requested_uri = str_replace("wp-login.php","",get_option('login_page_url'));
                        wp_redirect($requested_uri);

                    }
					else if (strpos($requested_uri, get_option('login_page_url')) !== false ) {
					
						@require_once ABSPATH . 'wp-login.php';
						die;
					}
				}

				if(isset($_POST['option']))
				{

						switch($_POST['option'])
						{
							case "mo_wpns_change_password":
								$this->handle_change_password($_POST['username']
									,$_POST['new_password'],$_POST['confirm_password']);		break;
						}
				}
				if(is_user_logged_in())
				{
					$this->user_inactive_logout_action();
				}



		}

		function wooc_validate_user_captcha_register($username, $email, $validation_errors) {

			if (empty($_POST['g-recaptcha-response'])) {
				$validation_errors->add( 'woocommerce_recaptcha_error', __('Please verify the captcha', 'woocommerce' ) );
			}
		}

		//Function to Handle Change Password Form
		function handle_change_password($username,$newpassword,$confirmpassword)
		{
			global $mo_lla_dirName;
			$user  = get_user_by("login",$username);
			$error = wp_authenticate_username_password($user,$username,$newpassword);

			if(is_wp_error($error))
			{
				$this->mo_wpns_login_failed($username);
				return $error;
			}

			if($this->update_strong_password($username,$newpassword,$confirmpassword)=="success")
			{
				wp_set_auth_cookie($user->ID,false,false);
				$this->mo_wpns_login_success($username);
				wp_redirect(get_option('siteurl'),301);
			}
		}


		//Function to Update User password
		function update_strong_password($username,$newpassword,$confirmpassword)
		{
			global $mo_lla_dirName;

			if(strlen($newpassword) > 5 && preg_match("#[0-9]+#", $newpassword) && preg_match("#[a-zA-Z]+#", $newpassword)
				&& preg_match('/[^a-zA-Z\d]/', $newpassword) && $newpassword==$confirmpassword)
			{
				$user = get_user_by("login",$username);
				wp_set_password($_POST['new_password'],$user->ID);
				return "success";
			}
			else
				include $mo_lla_dirName . 'controllers'.DIRECTORY_SEPARATOR.'change-password.php';
		}


		//Our custom logic for user authentication
		function custom_authenticate($user, $username, $password)
		{

			global $moWpnsUtility;
			$error = new WP_Error();
			if(empty($username) && empty ($password))
				return $error;
			if(empty($username))
				$error->add('empty_username', __('<strong>ERROR</strong>: Username field is empty.'));

			if(empty($password))
				$error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
			$error1 = wp_authenticate_username_password($user,$username,$password);

			if(is_wp_error($error1))
			{
				$this->mo_wpns_login_failed($username);
				return $error1;
			}



			if(empty($error->errors))
			{
				$user  = get_user_by("login",$username);

				if($user)
				{

                    if(get_option('mo_wpns_activate_recaptcha_for_login'))
                    {
                        $captcha_version = get_option('mo_wpns_recaptcha_version');
                        if($captcha_version=='reCAPTCHA_v3'){
                            $recaptchaError = $moWpnsUtility->verify_recaptcha_3(sanitize_text_field($_POST['g-recaptcha-response']));
                        }else if($captcha_version=='reCAPTCHA_v2'){
                            $recaptchaError = $moWpnsUtility->verify_recaptcha(sanitize_text_field($_POST['g-recaptcha-response']));
                        }

                        }

					if(empty($recaptchaError->errors) && get_option('mo_wpns_enforce_strong_passswords'))
					    {
                            $error = $this->check_password($user,$error,$password);
                        }
					    else
					        {
                        $error = $recaptchaError;

                    }

					if(empty($error->errors)){

						if(!get_option('mo_wpns_enable_brute_force'))
						{
						   $this->mo_wpns_login_success($username);

						}
						return $user;
					}
				}
				else
					$error->add('empty_password', __('<strong>ERROR</strong>: Invalid Username.'));

			}
			return $error;
		}

       function rename_login_custom_authenticate($user, $username, $password)
		{

			global $moWpnsUtility;
			$error = new WP_Error();
		    $user = wp_authenticate_username_password( $user, $username, $password );

			if(empty($username) || empty($password) ){

			    $userIp 		= $moWpnsUtility->get_client_ip();
			    $mo_wpns_config = new Mo_lla_MoWpnsHandler();

                $failedAttempts 	 = $mo_wpns_config->get_failed_attempts_count($userIp);
                $allowedLoginAttempts = get_option('mo_wpns_allwed_login_attempts') ? get_option('mo_wpns_allwed_login_attempts') : 10;

                if($allowedLoginAttempts - $failedAttempts <= 0)
                    $this->handle_login_attempt_exceeded($userIp);
                else if(get_option('mo_wpns_show_remaining_attempts') && $allowedLoginAttempts - $failedAttempts != $allowedLoginAttempts){
                    $this->show_limit_login_left($allowedLoginAttempts,$failedAttempts);
                    $error->add('empty_username', __('<strong>ERROR</strong>: Invalid username or Password.'));
                }
				return $error;
		    }
		    if(is_wp_error( $user )){
		        $error->add('empty_username', __('<strong>ERROR</strong>: Invalid username or Password.'));
                return $user;
		    }

			if(empty($error->errors)){

				$user  = get_user_by("login",$username);

				if($user)
				{
					 if(get_option('mo_wpns_activate_recaptcha_for_login'))
                    {
                        $captcha_version = get_option('mo_wpns_recaptcha_version');
                        if($captcha_version=='reCAPTCHA_v3'){
                            $recaptchaError = $moWpnsUtility->verify_recaptcha_3(sanitize_text_field($_POST['g-recaptcha-response']));
                        }else if($captcha_version=='reCAPTCHA_v2'){
                            $recaptchaError = $moWpnsUtility->verify_recaptcha(sanitize_text_field($_POST['g-recaptcha-response']));
                        }
                        	

                        }
					if(empty($recaptchaError->errors) && get_option('mo_wpns_enforce_strong_passswords'))
						$error = $this->check_password($user,$error,$password);
					else
						$error = $recaptchaError;
 					if(empty($error->errors)){
						if(!get_option('mo_wpns_enable_brute_force'))
						{
						   $this->mo_wpns_login_success($username);
						}
						return $user;
					}
				}
				else
					$error->add('empty_password', __('<strong>ERROR</strong>: Invalid Username or password.'));
			}

		}

		//Function to check user password
		function check_password($user,$error,$password)
		{
			global $moWpnsUtility, $mo_lla_dirName;

			if ( wp_check_password( $password, $user->data->user_pass, $user->ID) )
			{
				if($moWpnsUtility->check_user_password_strength($user,$password,"")=="success")
				{
					if(get_option('mo_wpns_enable_brute_force'))
						$this->mo_wpns_login_success($user->data->user_login);
					return $user;
				}
				else
                    {
                        include $mo_lla_dirName .'controllers'.DIRECTORY_SEPARATOR.'change-password.php';
                    }
			}
			else
				$error->add('empty_password', __('<strong>ERROR</strong>: Wrong password.'));

            return $error;

        }
		//Function to handle successful user login
		function mo_wpns_login_success($username)
		{
			global $moWpnsUtility;
		    $user = get_user_by( 'login', $username );
		    update_user_meta($user->ID,'last_active_time',date('H:i:s'));
			$mo_wpns_config = new Mo_lla_MoWpnsHandler();
			$userIp 		= $moWpnsUtility->get_client_ip();
            filter_var($userIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

            $mo_wpns_config->move_failed_transactions_to_past_failed($userIp);

			if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
				$moWpnsUtility->sendNotificationToUserForUnusualActivities($username, $userIp, Mo_lla_MoWpnsConstants::LOGGED_IN_FROM_NEW_IP);


			$mo_wpns_config->add_transactions($userIp, $username, Mo_lla_MoWpnsConstants::LOGIN_TRANSACTION, Mo_lla_MoWpnsConstants::SUCCESS);
		}
		//Function to handle failed user login attempt
		function mo_wpns_login_failed($username)
		{
			global $moWpnsUtility;
				$userIp 		= $moWpnsUtility->get_client_ip();
                filter_var($userIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
				if(empty($userIp) || empty($username) || !get_option('mo_wpns_enable_brute_force'))
					return;

				$mo_wpns_config = new Mo_lla_MoWpnsHandler();
				$isWhitelisted  = $mo_wpns_config->is_whitelisted($userIp);

	

				$mo_wpns_config->add_transactions($userIp, $username, Mo_lla_MoWpnsConstants::LOGIN_TRANSACTION, Mo_lla_MoWpnsConstants::FAILED);



					if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
							$moWpnsUtility->sendNotificationToUserForUnusualActivities($username, $userIp, Mo_lla_MoWpnsConstants::FAILED_LOGIN_ATTEMPTS_FROM_NEW_IP);

					$failedAttempts 	 = $mo_wpns_config->get_failed_attempts_count($userIp);
					$allowedLoginAttepts = get_option('mo_wpns_allwed_login_attempts') ? get_option('mo_wpns_allwed_login_attempts') : 10;

					if($allowedLoginAttepts - $failedAttempts<=0)
						$this->handle_login_attempt_exceeded($userIp);
					else if(get_option('mo_wpns_show_remaining_attempts'))
						$this->show_limit_login_left($allowedLoginAttepts,$failedAttempts);
		}
		//Function to show number of attempts remaining
		function show_limit_login_left($allowedLoginAttepts,$failedAttempts)
		{
			global $error;
			$diff = $allowedLoginAttepts - $failedAttempts;
			$error = "<br>You have <b>".$diff."</b> login attempts remaining.";
		}
		//Function to handle login limit exceeded
		function handle_login_attempt_exceeded($userIp)
		{
			global $moWpnsUtility, $mo_lla_dirName;
			$mo_wpns_config = new Mo_lla_MoWpnsHandler();
			$error_message = "Number of failed login attempts exceeded.";
			$mo_wpns_config->block_ip($userIp, Mo_lla_MoWpnsConstants::LOGIN_ATTEMPTS_EXCEEDED, false);
			include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'403.php';
		}
		function user_inactive_logout_action(){
	        if (is_user_logged_in() && get_option('mo_wpns_inactive_user_logout')) {
	            $current_time = date('H:i:s');
	            $last_active_time = get_user_meta(get_current_user_id(),'last_active_time',true);
	            $inactive_logout_duration = get_option('mo_inactive_logout_duration');
	            $difference = strtotime($current_time) - strtotime($last_active_time);
	            $user = wp_get_current_user();
	            $roles = $user->roles[0];
	            if ($difference >= $inactive_logout_duration) {
	                if ("administrator" == $roles) {
	                    if (get_option('mo_inactive_allowed_admin_session')) {
	                        wp_logout();
	                    } else {
	                        update_user_meta(get_current_user_id(),'last_active_time',$current_time);
	                    }
	                } else {
	                    wp_logout();
	                }
	            } else {
	                update_user_meta(get_current_user_id(),'last_active_time',$current_time);
	            }
	        }
    	}

	}
	new Mo_lla_LoginHandler;
