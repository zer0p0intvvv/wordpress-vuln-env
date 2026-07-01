<?php

class Mo_lla_MocURL
{

	public static function create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = '')
	{
		$url = Mo_lla_MoWpnsConstants::HOST_NAME . '/moas/rest/customer/add';
		$fields = array (
			'companyName' 	 => $company,
			'areaOfInterest' => 'Limit Login Attempts',
			'firstname' 	 => $first_name,
			'lastname' 		 => $last_name,
			'email' 		 => $email,
			'phone' 		 => $phone,
			'password' 		 => $password
		);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	public static function get_customer_key($email, $password) 
	{
		$url 	= Mo_lla_MoWpnsConstants::HOST_NAME. "/moas/rest/customer/key";
		$fields = array (
					'email' 	=> $email,
					'password'  => $password
				);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	function submit_contact_us( $q_email, $q_phone, $query, $subject )
	{
		$current_user = wp_get_current_user();
		$url    = Mo_lla_MoWpnsConstants::HOST_NAME . "/moas/rest/customer/contact-us";
		$query  = '[WordPress Limit Login Attempts Plugin: V:'.LIMITLOGIN_VERSION.'] ' . $query;
		$fields = array(
					'firstName'	=> $current_user->user_firstname,
					'lastName'	=> $current_user->user_lastname,
					'company' 	=> $_SERVER['SERVER_NAME'],
					'email' 	=> $q_email,
					'ccEmail'   => '2fasupport@xecurify.com',
					'phone'		=> $q_phone,
					'query'		=> $query
				);
		$field_string = json_encode( $fields );
		$response = self::callAPI($url, $field_string);
		
		return true;
	}

	function lookupIP($ip)
	{
		$url 	= Mo_lla_MoWpnsConstants::HOST_NAME. "/moas/rest/security/iplookup";
		$fields = array (
					'ip' => $ip
				);
		$json = json_encode($fields);
        return self::callAPI($url, $json);
	}
	
	function send_otp_token($auth_type, $phone, $email)
	{
		
		$url 		 = Mo_lla_MoWpnsConstants::HOST_NAME . '/moas/api/auth/challenge';
		$customerKey = Mo_lla_MoWpnsConstants::DEFAULT_CUSTOMER_KEY;
		$apiKey 	 = Mo_lla_MoWpnsConstants::DEFAULT_API_KEY;

		$fields  	 = array(
							'customerKey' 	  => $customerKey,
							'email' 	  	  => $email,
							'phone' 	  	  => $phone,
							'authType' 	  	  => $auth_type,
							'transactionName' => 'Limit Login Attempts'
						);
		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
        return self::callAPI($url, $json, $authHeader);
	}

	function mo_lla_validate_recaptcha($ip,$response)
	{
		$url 		 = Mo_lla_MoWpnsConstants::RECAPTCHA_VERIFY;
		$json		 = "";
		$fields 	 = array(
							'response' => $response,
							'secret'   => get_option('mo_wpns_recaptcha_secret_key'),
							'remoteip' => $ip
						);
		foreach($fields as $key=>$value) { $json .= $key.'='.$value.'&'; }
		rtrim($json, '&');
		$response 	 = self::callAPI($url, $json, null);
		return $response;
	}

	function mo_lla_get_Captcha_v3($Secretkey)
    {

        $json		 = "";
        $url         = "https://www.google.com/recaptcha/api/siteverify";
        $fields 	 = array(
                        'response' => $Secretkey,
                        'secret'   => get_option('mo_wpns_recaptcha_secret_key_v3'),
                        'remoteip' => $_SERVER['REMOTE_ADDR']
                    );
        foreach($fields as $key=>$value) {
            $json .= $key.'='.$value.'&';
        }
        json_encode($json);
        $result 	 = $this->callAPI($url, $json, null);

        return $result;
    }

	function validate_otp_token($transactionId,$otpToken)
	{
		$url 		 = Mo_lla_MoWpnsConstants::HOST_NAME . '/moas/api/auth/validate';
		$customerKey = Mo_lla_MoWpnsConstants::DEFAULT_CUSTOMER_KEY;
		$apiKey 	 = Mo_lla_MoWpnsConstants::DEFAULT_API_KEY;

		$fields 	 = array(
						'txId'  => $transactionId,
						'token' => $otpToken,
					 );

		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
        return self::callAPI($url, $json, $authHeader);
	}
	
	function check_customer($email)
	{
		$url 	= Mo_lla_MoWpnsConstants::HOST_NAME . "/moas/rest/customer/check-if-exists";
		$fields = array(
					'email' 	=> $email,
				);
		$json     = json_encode($fields);
        return self::callAPI($url, $json);
	}
	
	function mo_wpns_forgot_password()
	{
	
		$url 		 = Mo_lla_MoWpnsConstants::HOST_NAME . '/moas/rest/customer/password-reset';
		$email       = get_option('mo_wpns_admin_email');
		$customerKey = get_option('mo_wpns_admin_customer_key');
		$apiKey 	 = get_option('mo_wpns_admin_api_key');
	
		$fields 	 = array(
						'email' => $email
					 );
	
		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
        return self::callAPI($url, $json, $authHeader);
	}

	function send_notification($toEmail,$subject,$content,$fromEmail,$fromName,$toName)
	{
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		$headers .= 'From: '.$fromName.'<'.$fromEmail.'>' . "\r\n";
		//$headers .= 'Cc: cc@example.com' . "\r\n";

		mail($toEmail,$subject,$content,$headers);

		return json_encode(array("status"=>'SUCCESS','statusMessage'=>'SUCCESS'));
	}

	//added for feedback

    function send_email_alert($email,$phone,$message, $subject){


        $url = Mo_lla_MoWpnsConstants::HOST_NAME . '/moas/api/notify/send';
        $customerKey = Mo_lla_MoWpnsConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = Mo_lla_MoWpnsConstants::DEFAULT_API_KEY;
        $fromEmail			= 'no-reply@xecurify.com';

        global $user;
        $user         = wp_get_current_user();

        $query        = '[WordPress Limit Login Attempts Plugin]: ' . $message;

        $content='<div >Hello, <br><br>First Name :'.$user->user_firstname.'<br><br>Last  Name :'.$user->user_lastname.'   <br><br>Company :<a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>Phone Number :'.$phone.'<br><br>Email :<a href="mailto:'.$email.'" target="_blank">'.$email.'</a><br><br>Query :'.$query.'</div>';

        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'bccEmail' 		=> $fromEmail,
                'fromName' 		=> 'Xecurify',
                'toEmail' 		=> '2fasupport@xecurify.com',
                'toName' 		=> '2fasupport@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);
        $authHeader  = $this->createAuthHeader($customerKey,$apiKey);
        $response = self::callAPI($url, $field_string,$authHeader);

        return $response;

    }


	private static function createAuthHeader($customerKey, $apiKey) {
		$currentTimestampInMillis = round(microtime(true) * 1000);
		$currentTimestampInMillis = number_format($currentTimestampInMillis, 0, '', '');

		$stringToHash = $customerKey . $currentTimestampInMillis . $apiKey;
		$authHeader = hash("sha512", $stringToHash);

        return array (
            "Content-Type: application/json",
            "Customer-Key: $customerKey",
            "Timestamp: $currentTimestampInMillis",
            "Authorization: $authHeader"
        );
	}


	private static function callAPI($url, $json_string, $headers = array("Content-Type: application/json")) {
		//For testing (0, false)
		//For Production (1, true)
		
		$sslhost=0;
		$sslpeer=false;
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $sslhost );

		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $sslpeer );  
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		if(!is_null($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
		$content = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Request Error:' . curl_error($ch);
			exit();
		}

		curl_close($ch);
		return $content;
	}



}