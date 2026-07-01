<?php

	global $moWpnsUtility,$mo_lla_dirName;
	$username = $user->data->user_login;
	$message  = isset($newpassword) && ($newpassword != $confirmpassword) ? "Both Passwords do not match." : "Please enter a stronger password.";
	$plugin   = plugin_basename(dirname(dirname(__FILE__)));
	$css_file = plugins_url($plugin.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'style_settings.css',$mo_lla_dirName);
	$js_file  = plugins_url($plugin.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'settings_page.js',$mo_lla_dirName);
	$js_url	  = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js';

	include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'change-password.php';

	exit;
