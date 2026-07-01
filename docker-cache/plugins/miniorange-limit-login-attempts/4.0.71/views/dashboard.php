<?php

echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_dashboard_layout">
			<center>
				<div class ="mo_wpns_inside_dashboard_layout ">Failed Login<hr class="line"><p class ="wpns_font_size mo_wpns_dashboard_text" >'.$wpns_attacks_blocked.'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">Attacks Blocked <hr class="line"><p class ="wpns_font_size mo_wpns_dashboard_text">'.$totalAttacks.'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">Blocked IPs<hr class="line"><p class ="wpns_font_size mo_wpns_dashboard_text">'.$wpns_count_ips_blocked.'</p></div>
				<!-- <div class ="mo_wpns_inside_dashboard_layout">Infected Files<hr class="line"><p class ="wpns_font_size mo_wpns_dashboard_text" >'.$total_malicious.'</p></div> -->
				<div class ="mo_wpns_inside_dashboard_layout">White-listed IPs<hr class="line"><p class ="wpns_font_size mo_wpns_dashboard_text">'.$wpns_count_ips_whitelisted.'</p></div>
				
				
			</center>
		</div>
			

			<div class="mo_wpns_small_layout">
				<h3>Web Application Firewall (WAF)</h3>
				Web Application Firewall protects your website from several website attacks such as <b>SQL Injection(SQLI), Cross Site Scripting(XSS), Remote File Inclusion</b> and many more cyber attacks.It also protects your website from <b>critical attacks</b> such as <b>Dos and DDos attacks.</b><br>
				<ul><li><a class="button button-primary button-large" href="'.$waf.'">Settings</a></li></ul>
			</div>
			<div class="mo_wpns_small_layout">
				<h3>Login and Spam</h3>
				Firewall protects the whole website.
				If you just want to prevent your login page from <b> password guessing attacks</b> by humans or by bots.
				 We have features such as <b> Brute Force,Enforcing Strong Password,Custom Login Page URL,Recaptcha </b> etc. <br>
				<ul><li><a class="button button-primary button-large" href="'.$login_and_spam.'">Settings</a></li></ul>
			</div>
			<!-- <div class="mo_wpns_small_layout">
				<h3>Encrypted Backup</h3>
				Creating regular backups for your website is essential. By Creating backup you can <b>restore your website back to normal</b> within a few minutes. miniOrange creates <b>database and file Backup</b> which is stored locally in your system.
				<ul><li><a class="button button-primary button-large" href="'.$backup.'">Settings</a></li></ul>
			</div>
			<div class="mo_wpns_small_layout">
				<h3>Malware Scan</h3>
				 A malware scanner / detector or virus scanner is a <b>software that detects the malware</b> into the system. It detects different kinds of malware and categories based on the <b>strength of vulnerability or harmfulness.</b> <br>
				<ul><li><a class="button button-primary button-large" href="'.$scan_url.'">Settings</a></li></ul>
			</div> -->
			<div class="mo_wpns_small_layout">
				<h3>Advanced Blocking</h3>
				In Advanced blocking we have features like <b> Country Blocking, IP range Blocking , Browser blocking </b> and other options you can set up specifically according to your needs 
				<ul><li><a class="button button-primary button-large" href="'.$advance_block.'">Settings</a></li></ul>
			</div>
		    <div class="mo_wpns_small_layout">
				<h3>Reports</h3>
                Track users <b>login activity</b> on your website. You can also <b>track 404 error</b> so that if anyone tries to access it too many times you can take action on it.
			    <ul><li><a class="button button-primary button-large" href="'.$reports_url.'">Settings</a></li></ul>
			</div>

			<div class="mo_wpns_small_layout">
				<h3>Notification</h3>
				Get <b>Notified realtime</b> about any <b>IP getting Blocked.</b> With that, also get informed about any <b>unusual activities</b> detected by miniOrange.
				<ul><li><a class="button button-primary button-large" href="'.$notif_url.'">Settings</a></li></ul>
			</div>
		
	</div>

	
	
		

	';
