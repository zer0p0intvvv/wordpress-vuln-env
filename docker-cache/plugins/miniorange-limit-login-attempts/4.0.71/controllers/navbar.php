<?php
	
	global $moWpnsUtility,$mo_lla_dirName;


	$profile_url	= add_query_arg( array('page' => 'wpnsaccount'		), $_SERVER['REQUEST_URI'] );
	$login_security	= add_query_arg( array('page' => 'default'			), $_SERVER['REQUEST_URI'] );
	$login_and_spam = add_query_arg( array('page' => 'login_and_spam'   ), $_SERVER['REQUEST_URI'] );
	$waf			= add_query_arg( array('page' => 'waf'				), $_SERVER['REQUEST_URI'] );
	$register_url	= add_query_arg( array('page' => 'registration'		), $_SERVER['REQUEST_URI'] );
	$blocked_ips	= add_query_arg( array('page' => 'blockedips'		), $_SERVER['REQUEST_URI'] );
	$advance_block	= add_query_arg( array('page' => 'advancedblocking'	), $_SERVER['REQUEST_URI'] );
	$notif_url		= add_query_arg( array('page' => 'notifications'	), $_SERVER['REQUEST_URI'] );
	$reports_url	= add_query_arg( array('page' => 'reports'			), $_SERVER['REQUEST_URI'] );
	$license_url	= add_query_arg( array('page' => 'upgrade'  		), $_SERVER['REQUEST_URI'] );
	$help_url		= add_query_arg( array('page' => 'troubleshooting'	), $_SERVER['REQUEST_URI'] );
	$content_protect= add_query_arg( array('page' => 'content_protect'	), $_SERVER['REQUEST_URI'] );
	$backup			= add_query_arg( array('page' => 'backup'			), $_SERVER['REQUEST_URI'] );
	$scan_url       = add_query_arg( array('page' => 'malwarescan'      ), $_SERVER['REQUEST_URI'] );
	//Added for new design
    $dashboard_url	= add_query_arg(array('page' => 'dashboard'			), $_SERVER['REQUEST_URI']);
    $upgrade_url	= add_query_arg(array('page' => 'upgrade'				), $_SERVER['REQUEST_URI']);
   //dynamic
    $logo_url = plugin_dir_url(dirname(__FILE__)) . 'includes/images/miniorange_logo.png';
   // $logo_url		= plugin_dir_url($mo_lla_dirName) . 'wp-security-pro/includes/images/miniorange_logo.png';
    $shw_feedback	= get_option('donot_show_feedback_message') ? false: true;
    $moPluginHandler= new Mo_lla_MoWpnsHandler();
    $safe			= $moPluginHandler->is_whitelisted($moWpnsUtility->get_client_ip());

    $active_tab 	= $_GET['page'];

	$is_brute_force_enable=get_site_option("mo_wpns_enable_brute_force");

	include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'navbar.php';