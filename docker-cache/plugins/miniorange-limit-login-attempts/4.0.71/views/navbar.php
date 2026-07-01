<?php

	if($shw_feedback)
		do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('FEEDBACK'),'CUSTOM_MESSAGE');
	if(!$safe)
		do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('WHITELIST_SELF'),'CUSTOM_MESSAGE');
    if(get_transient('ip_whitelisted'))
        echo Mo_lla_MoWpnsMessages::showMessage('ADMIN_IP_WHITELISTED');

	
	if($is_brute_force_enable=="" && !get_site_option("wpns_dont_show_enable_brute_force"))
		do_action('wpns_show_message',Mo_lla_MoWpnsMessages::showMessage('ENABLE_BRUTE_FORCE'),'CUSTOM_MESSAGE');

	echo'<div class="wrap mollm_header">
				<div class="mollm_admin_options"><img width="50" height="50"  src="'.$logo_url.'"></div>
					<h1>Limit Login Attempts &nbsp;	</h1>	
					<a class="add-new-h2" href="'.$profile_url.'">Account</a>
					<a class="add-new-h2" href="'.$help_url.'">Troubleshooting</a>
					<a class="license-button add-new-h2" href="'.$license_url.'">Upgrade</a>
					
		</div>';

	echo'<div id="tab">
			<h2 class="nav-tab-wrapper mo_wpns_nav-tab-wrapper">';

			
		echo '	<a class="nav-tab '.($active_tab == 'dashboard' 	  ? 'nav-tab-active' : '').'" href="'.$dashboard_url	.'">Dashboard</a>
		 		<a class="nav-tab '.($active_tab == 'login_and_spam'  ? 'nav-tab-active' : '').'" href="'.$login_and_spam	.'">Login and Spam</a>
		 		<a class="nav-tab '.($active_tab == 'waf' 			  ? 'nav-tab-active' : '').'" href="'.$waf				.'">WAF</a>
				<!-- <a class="nav-tab '.($active_tab == 'backup' 	  	  ? 'nav-tab-active' : '').'" href="'.$backup			.'">Encrypted Backup</a> -->
				<!-- <a class="nav-tab '.($active_tab == 'malwarescan'	  ?	'nav-tab-active' : '').'" href="'.$scan_url 		.'">Malware Scan</a> -->
				<a class="nav-tab '.($active_tab == 'advancedblocking'? 'nav-tab-active' : '').'" href="'.$advance_block	.'">Advanced Blocking</a>
				<a class="nav-tab '.($active_tab == 'notifications'	  ? 'nav-tab-active' : '').'" href="'.$notif_url		.'">Notifications</a>
				<a class="nav-tab '.($active_tab == 'reports'	  	  ?	'nav-tab-active' : '').'" href="'.$reports_url		.'">Reports</a> 
				<a class="nav-tab '.($active_tab == 'upgrade'	  	  ?	'nav-tab-active' : '').'" href="'.$upgrade_url		.'">Upgrade</a>
			</h2>
		</div>';