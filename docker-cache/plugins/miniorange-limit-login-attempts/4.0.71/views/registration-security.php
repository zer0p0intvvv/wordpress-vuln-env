<?php

echo'<div>	
		<div class="mo_wpns_setting_layout">';

echo'		<h3>Block Registerations from fake users</h3>
			<div class="mo_wpns_subheading">
				Disallow Disposable / Fake / Temporary email addresses
			</div>
			
			<form id="mo_wpns_enable_fake_domain_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_fake_domain_blocking">
				<input type="checkbox" name="mo_wpns_enable_fake_domain_blocking" '.$domain_blocking.' onchange="document.getElementById(\'mo_wpns_enable_fake_domain_blocking\').submit();"> Enable blocking registrations from fake users.
			</form>
		</div>
		
		<div class="mo_wpns_setting_layout">	
			<h3>Social Login Integration</h3>
			<div class="mo_wpns_subheading">Allow your user to login and auto-register with their favourite social network like Google, Twitter, Facebook, Vkontakte, LinkedIn, Instagram, Amazon, Salesforce, Windows Live.</div>
			
			<form id="mo_wpns_social_integration" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_social_integration">
				<input type="checkbox" name="mo_wpns_enable_social_integration" '.$social_login.' onchange="document.getElementById(\'mo_wpns_social_integration\').submit();"> Enable login and registrations with social networks.<br>
			    
			</form>';
			
			if($social_login)
				echo $html2;
				
echo'	</div>
	</div>';