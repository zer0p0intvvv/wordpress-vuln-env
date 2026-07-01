<?php

	global $mo_lla_dirName;
	
	if(current_user_can( 'manage_options' )  && isset($_POST['option']))
	{
		switch($_POST['option'])
		{
			case "mo_wpns_send_query":
				wpns_handle_support_form($_POST['query_email'],$_POST['query'],$_POST['query_phone']);
				break;
		}
	}

	$current_user 	= wp_get_current_user();
	$email 			= get_option("mo_wpns_admin_email");
	$phone 			= get_option("mo_wpns_admin_phone");

	
	if(empty($email))
		$email 		= $current_user->user_email;

	include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'support.php';


	/* SUPPORT FORM RELATED FUNCTIONS */

	//Function to handle support form submit
	function wpns_handle_support_form($email,$query,$phone)
	{

		if( empty($email) || empty($query) )
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('SUPPORT_FORM_VALUES'),'SUCCESS');
			return;
		}

		$query = sanitize_text_field( $query );
		$email = sanitize_text_field( $email );
		$phone = sanitize_text_field( $phone );
		$subject = 'Query for Limit Login Attempts - '.$email;

		$contact_us = new Mo_lla_MocURL();
		$submited = json_decode($contact_us->submit_contact_us($email, $phone, $query,$subject),true);

		if(json_last_error() == JSON_ERROR_NONE && $submited) 
		{
			do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('SUPPORT_FORM_SENT'),'SUCCESS');
			return;
		}
			
		do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
	}