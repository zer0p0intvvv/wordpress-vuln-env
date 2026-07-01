<?php

	global $moWpnsUtility,$mo_lla_dirName;

	$controller = $mo_lla_dirName . 'controllers/';

	include $controller 	 . 'navbar.php';

	if( isset( $_GET[ 'page' ])) 
	{
		switch($_GET['page'])
		{
			case 'dashboard':
                include $controller . 'dashboard.php';			    break;
            case 'login_and_spam':
				include $controller . 'login-spam.php';				break;
			case 'default':
				include $controller . 'login-security.php';			break;
			case 'wpnsaccount':
				include $controller . 'account.php';				break;		
			case 'backup':
				include $controller . 'backup.php'; 				break;
			case 'upgrade':
				include $controller . 'upgrade.php';                break;
			case 'waf':
				include $controller . 'waf.php';		    		break;
			case 'blockedips':
				include $controller . 'ip-blocking.php';			break;
			case 'advancedblocking':
				include $controller . 'advanced-blocking.php';		break;
			case 'notifications':
				include $controller . 'notification-settings.php';	break;
			case 'reports':
				include $controller . 'reports.php';				break;
			case 'licencing':
				include $controller . 'licensing.php';				break;
			case 'troubleshooting':
				include $controller . 'troubleshooting.php';		break;
			case 'malwarescan':
				include $controller . 'scan_malware.php';			break;
		}
	}

	include $controller . 'support.php';