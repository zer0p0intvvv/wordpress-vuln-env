<?php
global $moWpnsUtility,$mo_lla_dirName;

$setup_dirName = $mo_lla_dirName.'views'.DIRECTORY_SEPARATOR.'link_tracer.php';
 include $setup_dirName;
add_action( 'admin_footer', 'login_security_ajax' );
echo '
		<div id="wpns_message" style=" padding-top:8px"></div>
		<div>

		

		<div class="mo_wpns_setting_layout">';

echo ' 		<h3>Brute Force Protection ( Login Protection )<a href='.$two_factor_premium_doc['Brute Force Protection'].' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#269eb3;float: right;"></span></a> </h3>
			<div class="mo_wpns_subheading">This protects your site from attacks which tries to gain access / login to a site with random usernames and passwords.</div>
			
				<input id="mo_bf_button" type="checkbox" name="enable_brute_force_protection" '.$brute_force_enabled.'> Enable Brute force protection
			<br>';

			 
				
echo'			<form id="mo_wpns_enable_brute_force_form" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_brute_force_configuration">
					<table class="mo_wpns_settings_table">
						<tr>
							<td style="width:40%">Allowed login attempts before blocking an IP  : </td>
							<td><input class="mo_wpns_table_textbox" type="number" id="allwed_login_attempts" name="allwed_login_attempts" required placeholder="Enter no of login attempts" value="'.$allwed_login_attempts.'" /></td>
							<td></td>
						</tr>
						<tr>
							<td>Time period for which IP should be blocked  : </td>
							<td>
								<select id="time_of_blocking_type" name="time_of_blocking_type" style="width:100%;">
								  <option value="permanent" '.($time_of_blocking_type=="permanent" ? "selected" : "").'>Permanently</option>
								  <option value="months" '.($time_of_blocking_type=="months" ? "selected" : "").'>Months</option>
								  <option value="days" '.($time_of_blocking_type=="days" ? "selected" : "").'>Days</option>
								  <option value="hours" '.($time_of_blocking_type=="hours" ? "selected" : "").'>Hours</option>
								  <option value="minutes" '.($time_of_blocking_type=="minutes" ? "selected" : "").'>Minutes</option>
								</select>
							</td>
							<td><input class="mo_wpns_table_textbox '.($time_of_blocking_type=="permanent" ? "hidden" : "").' type="number" id="time_of_blocking_val" name="time_of_blocking_val" value="'.$time_of_blocking_val.'" placeholder="How many?" /></td>
						</tr>
						<tr>
							<td>Show remaining login attempts to user : </td>
							<td><input  type="checkbox"  id="rem_attempt" name="show_remaining_attempts" '.$remaining_attempts.' ></td>
							<td></td>
						</tr>
						<tr>
							<td></td>
							<td><br>
							<input type="hidden" id="brute_nonce" value ="'. wp_create_nonce("wpns-brute-force").'" />
							<input type="button" style="width:100px;" value="Save" class="button button-primary button-large" id="mo_bf_save_button">
							</td>
							<td></td>
						</tr>
					</table>
				</form>';
			
echo'	</div>';

		
	
			
echo'	

		<div class="mo_wpns_setting_layout" id="mo2f_google_recaptcha">
			<h3>Google reCAPTCHA <a href='.$two_factor_premium_doc['Google reCAPTCHA'].' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#269eb3;float: right;"></span></a></h3>
			<div class="mo_wpns_subheading">Google reCAPTCHA protects your website from spam and abuse. reCAPTCHA uses an advanced risk analysis engine and adaptive CAPTCHAs to keep automated software from engaging in abusive activities on your site. It does this while letting your valid users pass through with ease.</div>
			<form id="mo_wpns_activate_recaptcha" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_activate_recaptcha">

			</form>';

echo'			<form id="mo_wpns_recaptcha_settings" method="post" action="">
                    <div style="padding: 5px;">
                        <input id="enable_captcha" type="checkbox" name="enable_captcha" '.$google_recaptcha.'>
                         Enable reCAPTCHA</div>
                    <p>Select your preferred version of the reCAPTCHA:</p>
                    <div style="padding: 5px;">
                        <input type="radio" name="gcaptchatype" value="reCAPTCHA_v2"/>version 2</div>
                    <div style="padding: 5px;">
                        <input type="radio" name="gcaptchatype" value="reCAPTCHA_v3"/>version 3</div>
                        ';


echo'           <p>Before you can use reCAPTCHA, you need to register your domain/website
                <a href="'.$captcha_url.'"  target="blank" title="guide">here</a>.</p><br>
                <p>Enter Site key and Secret key that you get after registration.</p>


					<table class="mo_wpns_settings_table">
						<tr>
							<td style="width:30%">Site key  : </td>
							<td style="width:30%"><input id="captcha_site_key" class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_site_key" required placeholder="site key" /></td>
							<td style="width:20%"></td>
						</tr>
						<tr>
							<td>Secret key  : </td>
							<td><input id="captcha_secret_key" class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_secret_key" required placeholder="secret key" /></td>
						</tr>
						<tr>
							<td style="vertical-align:top;">Enable reCAPTCHA for :</td>
							<td><input id="login_captcha" type="checkbox" name="mo_wpns_activate_recaptcha_for_login" '.$captcha_login.'> Login form</td>
							<td><input id="reg_captcha" style="margin-left:10px" type="checkbox" name="mo_wpns_activate_recaptcha_for_registration" '.$captcha_reg.' > Registration form</td>
							<td>
							<input id="cmnt_captcha" style="margin-left:10px" type="checkbox" name="mo_wpns_activate_recaptcha_for_comments" '.$captcha_cmnt.' > WordPress Comments
							</td></tr>
							<tr><td><input id="bp_captcha" style="margin-left:10px" type="checkbox" name="mo_wpns_activate_recaptcha_for_buddypress_registration" '.$captcha_bp_reg.' > BuddyPress Registration form 
							</td><td>

							<input id="email_captcha" style="margin-left:10px" type="checkbox" name="mo_wpns_activate_recaptcha_for_email_subscription" '.$captcha_email.' > Email Subscription form
							</td>
							


						</tr>
					</table><br/>
					<input type="hidden" id="captcha_nonce" value = "'.wp_create_nonce("wpns-captcha").'">
					<input type="button" id="captcha_button" type="button" value="Save Settings" class="button button-primary button-large" />
					<input type="button" value="Test reCAPTCHA Configuration" onclick="testcaptchaConfiguration()" class="button button-primary button-large" />

				</form> </div>';?>
		 <script>
                var recaptcha_version ="<?php echo get_option('mo_wpns_recaptcha_version');?>";
                if(recaptcha_version=='reCAPTCHA_v3')
                    jQuery('input:radio[name="gcaptchatype"]').filter('[value="reCAPTCHA_v3"]').attr('checked', true);
                else if(recaptcha_version=='reCAPTCHA_v2')
  	                jQuery('input:radio[name="gcaptchatype"]').filter('[value="reCAPTCHA_v2"]').attr('checked', true);
  	            if(recaptcha_version =='reCAPTCHA_v3'){
  	            	 jQuery("#captcha_site_key").val("<?php echo get_option('mo_wpns_recaptcha_site_key_v3'); ?>");
  	            	
  	            	 jQuery("#captcha_secret_key").val("<?php echo get_option('mo_wpns_recaptcha_secret_key_v3'); ?>");
  	            	}
  	            	else if(recaptcha_version =='reCAPTCHA_v2') {

                       jQuery("#captcha_site_key").val("<?php echo get_option('mo_wpns_recaptcha_site_key'); ?>");
                       jQuery("#captcha_secret_key").val("<?php echo get_option('mo_wpns_recaptcha_secret_key'); ?>");
  	            	}
  	            jQuery('input:radio[name="gcaptchatype"]').change(function(){
  	            
  	            	var captcha_version=jQuery("input[name='gcaptchatype']:checked").val();
  	            	
  	            	if(captcha_version =='reCAPTCHA_v3'){
  	            	 jQuery("#captcha_site_key").val("<?php echo get_option('mo_wpns_recaptcha_site_key_v3'); ?>");
  	            	
  	            	 jQuery("#captcha_secret_key").val("<?php echo get_option('mo_wpns_recaptcha_secret_key_v3'); ?>");
  	            	}
  	            	else if(captcha_version =='reCAPTCHA_v2') {

                       jQuery("#captcha_site_key").val("<?php echo get_option('mo_wpns_recaptcha_site_key'); ?>");
                       jQuery("#captcha_secret_key").val("<?php echo get_option('mo_wpns_recaptcha_secret_key'); ?>");
  	            	}
  	            })
             </script>
			
<?php
echo'	</div>
		
		<div class="mo_wpns_setting_layout">		
			<h3>Mobile authentication <a href='.$two_factor_premium_doc['Mobile authentication'].' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#269eb3;float: right;"></span></a></h3>
			<div class="mo_wpns_subheading">Rather than relying on a password alone, which can be phished or guessed, Two Factor authentication adds a second layer of security to your WordPress accounts. We support <b>QR code</b>, <b>OTP over SMS</b> and <b>Email</b>, <b>Push</b>, <b>Soft token</b> (15+ methods to choose from). </div>
			    
			   
				<input type="hidden" id="mobile2fa" value ="'.wp_create_nonce("wpns-mobile-auth").'" />
				<input id="mobile_auth"  type="checkbox" name="mo_wpns_enable_2fa" '.$enable_2fa.'> Enable Mobile Authentication
			';

				if($twofa_status=="ACTIVE")
				{
echo 				'<br><a href="'.$deactivateUrl.'">2FA Plugin has all the security features of this plugin, Please Deactivate Limit Login Attempts Plugin.</a>';
				} 
				else if($twofa_status=="INSTALLED")
				{
echo 				'<br><span style="color:red">For Mobile Authentication you need to have miniOrange 2 Factor plugin activated.</span><br><a href="'.$activateUrl.'">Click here to activate 2 Factor Plugin</a>';
				} 
				else 
				{

echo				'<br><span style="color:red">For Mobile Authentication you need to have miniOrange 2 Factor plugin installed.</span><br><a href="'.$install_link.'">Install 2 Factor Plugin</a>';
				} 
			
				
echo		'<br>
		</div>
		
		<!--<div class="mo_wpns_setting_layout">		
			<h3>Enforce Strong Passwords <a href='.$two_factor_premium_doc['Enforce Strong Passwords'].' target="_blank"><span class="dashicons dashicons-text-page" style="font-size:23px;color:#269eb3;float: right;"></span></a></h3>
			<div class="mo_wpns_subheading">Checks the password strength of admin and other users to enhance login security</div>
			
			<form id="mo_wpns_enable_strong_password_form" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enforce_strong_passswords">
				<input id="strong_password_check" type="checkbox" name="mo_wpns_enforce_strong_passswords" '.$enforce_strong_password.' > Enable strong passwords
				
				<table style="width:100%"><tr><td style="width:58%">Select accounts for which you want to enable password security</td>
				<td><select id="mo_wpns_enforce_strong_passswords_for_accounts" name="mo_wpns_enforce_strong_passswords_for_accounts" style="width:100%;">
				  <option value="all" '.($strong_password_account=="all" ? "selected" : "").'>All Accounts</option>
				  <option value="admin" '.($strong_password_account=="admin" ? "selected" : "").'>Administrators Account Only</option>
				  <option value="user" '.($strong_password_account=="user" ? "selected" : "").'>Users Account Only</option>
				</select></td></tr></table>
				<input type="hidden" id="str_pass" value ="'.wp_create_nonce("wpns-strn-pass").'" >
				<input type="button" id="strong_password" name="submit" style="width:100px;" value="Save" class="button button-primary button-large">
			</form>
		</div> -->
		
		<div class="mo_wpns_setting_layout">	
			<h3>Risk Based Access</h3>';
				
			
			if(!empty($enable_2fa))
			{ 
echo'			<form id="mo_wpns_risk_based_access" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_risk_based_access">
					<input type="checkbox" name="mo_wpns_risk_based_access" '.$rba_enabled.' > Enable risk based access<br><br>
					<b>Note:</b> Checking this option will display an option \'Remember this device\' on 2nd factor screen. In the next login from the same device, user will bypass 2nd factor, i.e. user will be logged in through username + password only.
					<br><br>
					<input type="submit" name="submit" style="width:100px;" value="Save" class="button button-primary button-large">
				</form>';
				
				if($twofa_status=="INSTALLED")
				{
echo'				<br><span style="color:red">For Risk Based Access you need to have miniOrange 2 Factor plugin activated.</span><br><a href="'.$activateUrl.'">Click here to activate 2 Factor Plugin</a>';
				} 
				else if( $twofa_status!="ACTIVE" && $twofa_status!="INSTALLED")
				{
echo'				<br><span style="color:red">For Risk Based Access you need to have miniOrange 2 Factor plugin installed.</span><br><a href="'.$install_link.'">Install 2 Factor Plugin</a>';
				} 
			} 
			else 
			{ 
echo'				<form id="mo_wpns_rba_enable_2fa" method="post" action="">
						<input type="hidden" name="option" value="mo_wpns_rba_enable_2fa">
					</form>
					<span style="color:red">Mobile authentication (2 Factor) need to be enabled to use this option. <a style="cursor:pointer;" onclick="document.getElementById(\'mo_wpns_rba_enable_2fa\').submit();">Click here</a> to enable mobile authentication.</span><br>';
			}

?> 
</div>

<div class="mo_wpns_setting_layout">
	<h3>Rename Login URL</h3>
	<form id="mo_wpns_enable_rename_login_url_form" method="post" action="">
            <input type="hidden" name="option" value="mo_wpns_enable_rename_login_url">
            <input type="checkbox" name="enable_rename_login_url_checkbox" <?php if(get_option('mo_wpns_enable_rename_login_url')) echo "checked";?> onchange="document.getElementById('mo_wpns_enable_rename_login_url_form').submit();"> Enable Rename Login Page URL (<small>After enabling this you won't be able to login using <b>/wp-admin</b> or  <b>/wp-login.php</b></small>)
    </form>

	<?php 
	
	$login_page_url = "mylogin";
	if(get_option('mo_wpns_enable_rename_login_url')) {
        $login_page_url = "mylogin";
    }
		
	if (get_option('login_page_url')) {
			$login_page_url = get_option('login_page_url');
	}

	?>
	
	<form id="mo_wpns_enable_rename_login_url_form" method="post" action="">
				
                <input type="hidden" name="option" value="mo_wpns_rename_login_url_configuration">
                <table class="mo_wpns_settings_table">
                    <tr>
                        <td>Login Page URL : </td>
                        <td><?php echo site_url(); ?>/</td>
                        <td>
                            <input class="mo_wpns_table_textbox" type="text" id="login_page_url" name="login_page_url" placeholder="Enter New Login Page URL" value="<?php echo $login_page_url?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Your Current Login URL : </td>
                        <td colspan="2"><?php echo site_url(); ?>/<?php echo $login_page_url?></td>
                    </tr>
                    <tr>
                        <td><br><input type="submit" name="submit" style="width:100px;" value="Save"  class="button button-primary button-large"></td>
                        <td></td>
						<td></td>
                    </tr>
                </table>
            </form>
	</div>
	
<div class="mo_wpns_setting_layout">
		<h3>User Session Timeout </h3>
		<table>
		<th><td>
		<div class="mo_wpns_subheading">This sets the timeout for the Logout of a Wordpress User.</div>
		<input type="number" name="logout_time" required id ="logout_time"<?php if(get_option('mo_wpns_logout_time')) echo "value=".get_option('mo_wpns_logout_time');?> /> Number of Days</td></th>
		<th><td>
		<input type="button" name="saveUserSession" id="saveUserSession" value="Save" class="button button-primary button-large"/>
		</td></th>
            <td>
                <input type="button" name="resetUserSession" id="resetUserSession" value="Reset" class="button button-primary button-large"/>
            </td>
		</table>
		<br>

	</div>
	
	
<?php


echo '<script>

		function testcaptchaConfiguration(){
                var gradioVal = jQuery("input[name=gcaptchatype]:checked").val();
                if(gradioVal=="reCAPTCHA_v3"){
                var myWindow = window.open("'.$test_recaptcha_url_v3.'", "Test Google reCAPTCHA_v3 Configuration", "width=600, height=600");}
                else if(gradioVal=="reCAPTCHA_v2"){
                var myWindow = window.open("'.$test_recaptcha_url.'", "Test Google reCAPTCHA_v2 Configuration", "width=600, height=600");}
        }
	</script>';			

			
echo'		<br>
		</div>
	</div>
	
	<script>
		jQuery(document).ready(function(){
			$("#time_of_blocking_type").change(function() {
				if($(this).val()=="permanent")
					$("#time_of_blocking_val").addClass("hidden");
				else
					$("#time_of_blocking_val").removeClass("hidden");	
			});
		});	

		function mo_enable_disable_bf(){
			jQuery.ajax({
				type : "POST",
				data : {
					option: "mo_wpns_enable_brute_force",
					status: "'.$brute_force_enabled.'",
				},
				success: function(data){
					alert(data);
				}  
			 });
		}
		
		</script>'; 

		function login_security_ajax(){
			if ( ('admin.php' != basename( $_SERVER['PHP_SELF'] )) || ($_GET['page'] != 'login_and_spam') ) {
				return;
            }
		?>
				<script>

					jQuery(document).ready(function(){
						jQuery("#mo_bf_save_button").click(function(){
						var data =  {
					'action': 'wpns_login_security',
					'wpns_loginsecurity_ajax' : 'wpns_bruteforce_form', 
					'bf_enabled/disabled'     : jQuery("#mo_bf_button").is(":checked"),
					'allwed_login_attempts'   : jQuery("#allwed_login_attempts").val(),
					'time_of_blocking_type'   : jQuery("#time_of_blocking_type").val(),
					'time_of_blocking_val'    : jQuery("#time_of_blocking_val").val(),
					'show_remaining_attempts' : jQuery("#rem_attempt").is(':checked'),
					'nonce' 				  : jQuery("#brute_nonce").val(),	
				};
				jQuery.post(ajaxurl, data, function(response) {
				
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery('#wpns_message').show();
				if (response == "empty"){
				jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Please fill out all the fields</div>");
				window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else if(response == "true"){
					jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;' >Brute force is enabled and configuration has been saved</div>");
					window.scrollTo({ top: 0, behavior: 'smooth'});
				}
				else if(response == "false"){
					jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Brute force is disabled</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else if(response == "ERROR" ){ 
				jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >ERROR</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' }); 
				
				}
				});
					});

				jQuery(document).ready(function(){
				jQuery("#rename_login_config_url").click(function(){
							var data = {
					'action'                 :'wpns_login_security',
					'wpns_loginsecurity_ajax':'wpns_rename_loginURL',
				 	'enable_rename_loginurl' :jQuery('#rename_url_chkbx').is(':checked'),
				 	'input_url'				 :jQuery('#login_page_url').val(), 
				 	'nonce'                  :jQuery('#wpns_url').val(), 
				 }
				 jQuery.post(ajaxurl, data, function(response) {
				 jQuery("#wpns_message").empty();
				 jQuery("#wpns_message").hide();
				 jQuery('#wpns_message').show();
				 if (response == "empty"){
				 jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Please fill out all the fields</div>");
				 window.scrollTo({ top: 0, behavior: 'smooth' });
				 }
				 else if(response == "true"){
				 	jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >Login Page URL has been changed.</div>");
				 	jQuery('#loginURL').empty();
				 	jQuery('#loginURL').hide();
				 	jQuery('#loginURL').show();
				 	jQuery('#loginURL').append(data.input_url);
				 	window.scrollTo({ top: 0, behavior: 'smooth' });
				 }
				 else if(response == "false"){
				 	jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Your custom login page URL is DISABLED.</div>");
				 	jQuery('#loginURL').empty();
				 	jQuery('#loginURL').hide();
				 	jQuery('#loginURL').show();
				 	jQuery('#loginURL').append('wp-login.php');	
				 	window.scrollTo({ top: 0, behavior: 'smooth' });
				 }
				 else if(response == "ERROR" ){ 
				 jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >ERROR</div>");
				 	window.scrollTo({ top: 0, behavior: 'smooth' }); 
				
				 }
				 });
				 		});
					});	
					});
					

					jQuery(document).ready(function(){
						jQuery("#mobile_auth").click(function(){
						var data = {
					'action'                 :'wpns_login_security',  
					'wpns_loginsecurity_ajax':'wpns_mobile_auth',
					'mobile_auth_status'     :jQuery("#mobile_auth").is(':checked'),
					'nonce'					 :jQuery("#mobile2fa").val(), 

					 
				}

				jQuery.post(ajaxurl, data, function(response) {
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery('#wpns_message').show();

				if(response == "true"){
					jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >Mobile Authentication is switched ON.</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else if(response == "false"){
				jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Mobile Authentication is turned OFF.</div>");
				window.scrollTo({ top: 0, behavior: 'smooth' });	
				}
				else if(response == "ERROR" ){ 
				jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >ERROR</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' }); 
				
				}

				});
						});
					});
					
                jQuery(document).ready(function(){
                    jQuery("#captcha_button").click(function(){
                    	
                        var recaptcha_version =jQuery("input[name='gcaptchatype']:checked").val();
                        var data = {
                        'action'                 :'wpns_login_security',
                        'wpns_loginsecurity_ajax':'wpns_save_captcha',
                        'site_key'  			 : jQuery("#captcha_site_key").val(),
                        'secret_key'			 : jQuery("#captcha_secret_key").val(),
                        'version'                : recaptcha_version,
                        'enable_captcha'		 : jQuery("#enable_captcha").is(':checked'),
                        'login_form'			 : jQuery("#login_captcha").is(':checked'),
                        'registeration_form'	 : jQuery("#reg_captcha").is(':checked'),
                        'nonce'		           	 : jQuery("#captcha_nonce").val(),
                        }
                        jQuery.post(ajaxurl, data, function(response) {

                            jQuery("#wpns_message").empty();
                            jQuery("#wpns_message").hide();
                            jQuery('#wpns_message').show();
                            if (response == "empty"){
                            jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Please fill out all the fields</div></div>");
                            window.onload = nav_popup();				}
                            if (response == "version_select"){                            	
                            jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Please select a version for the reCAPTCHA</div></div>");
                            window.onload = nav_popup();				}
                            else if(response == "true"){                            	
                                jQuery('#loginURL').empty();
                                jQuery('#loginURL').hide();
                                jQuery('#loginURL').show();
                                jQuery('#loginURL').append(data.input_url);
                                jQuery('#wpns_message').append("<div id='notice_div' class='overlay_success'><div class='popup_text'>&nbsp; &nbsp; CAPTCHA is enabled.</div></div>");
                                window.onload = nav_popup();					}
                            else if(response == "false"){                            	
                                if(!jQuery("input[name='gcaptchatype']:checked").val())
                                {
                                    jQuery('#loginURL').empty();
                                    jQuery('#loginURL').hide();
                                    jQuery('#loginURL').show();
                                    jQuery('#loginURL').append('wp-login.php');
                                    jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; Select a version.</div></div>");
                                    window.onload = nav_popup();
                                }
                                else{
                                jQuery('#loginURL').empty();
                                jQuery('#loginURL').hide();
                                jQuery('#loginURL').show();
                                jQuery('#loginURL').append('wp-login.php');
                                jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; CAPTCHA is disabled.</div></div>");
                                window.onload = nav_popup();}				}
                            else if(response == "ERROR" ){
                                jQuery('#wpns_message').append("<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; ERROR</div></div>");
                                window.onload = nav_popup();
                            }
                        });
                    });
                });

					jQuery(document).ready(function(){
						jQuery("#strong_password").click(function(){
						var data = {
					'action'                 :'wpns_login_security',  
					'wpns_loginsecurity_ajax':'save_strong_password',
					'enable_strong_pass'	 :jQuery("#strong_password_check").is(':checked'),
					'accounts_strong_pass'	 :jQuery("#mo_wpns_enforce_strong_passswords_for_accounts").val(),
					'nonce'					 :jQuery("#str_pass").val(), 
				}
				jQuery.post(ajaxurl, data, function(response) {
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery('#wpns_message').show();
				if(response == "true"){
					jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >Strong password is enabled.</div>");
						window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else if(response == "false"){
					jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Strong Password is disabled.</div>");
						window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else if(response == "ERROR" ){ 
				jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >ERROR</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' }); 
				
				}
				});
						});
					});

					jQuery("#resetUserSession").click(function () {
                        var nonce 	= '<?php echo wp_create_nonce("UserSessiontimeoutNonce");?>';
                        var data 	= {
                            "action"					: "wpns_login_security",
                            "wpns_loginsecurity_ajax" 	: "wpns_userSession_form_reset",
                            "nonce"						:  nonce
                        };

                        jQuery.post(ajaxurl, data, function(response) {
                            var response = response.replace(/\s+/g," ").trim();
                            if(response == "ResetSessionSettings")
                            {
                                jQuery("#wpns_message").empty();
                                jQuery("#wpns_message").append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  '>Session Timeout has been disabled.</div>");
                                window.scrollTo({ top: 0, behavior: "smooth" });
                            }
                            else if(response == "NonceDidNotMatch")
                            {
                                jQuery("#wpns_message").empty();
                                jQuery("#wpns_message").append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Nonce Did not match.</div>");
                                window.scrollTo({ top: 0, behavior: "smooth" });
                            }
                            else
                            {
                                jQuery("#wpns_message").empty();
                                jQuery("#wpns_message").append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px; '>Format Did not match.</div>");
                                window.scrollTo({ top: 0, behavior: "smooth" });
                            }
                        });

                    })
						

				jQuery("#saveUserSession").click(function(){
				var time  	=  jQuery("#logout_time").val();
				var nonce 	= '<?php echo wp_create_nonce("UserSessiontimeoutNonce");?>';
				var data 	= {
				"action"					: "wpns_login_security",
				"wpns_loginsecurity_ajax" 	: "wpns_userSession_form", 
				"time"						:  time,
				"nonce"						:  nonce
				};
				jQuery.post(ajaxurl, data, function(response) {
					var response = response.replace(/\s+/g," ").trim();
					if(response == "SavedSessionSettings")
					{
						jQuery("#wpns_message").empty();
						jQuery("#wpns_message").append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  '>Setting has been saved.</div>");
						window.scrollTo({ top: 0, behavior: "smooth" });
					}
					else if(response == "NonceDidNotMatch")
					{
						jQuery("#wpns_message").empty();
						jQuery("#wpns_message").append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Nonce Did not match.</div>");
						window.scrollTo({ top: 0, behavior: "smooth" });
					}
					else
					{
						jQuery("#wpns_message").empty();
						jQuery("#wpns_message").append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px; '>Format Did not match.</div>");
						window.scrollTo({ top: 0, behavior: "smooth" });		
					}
				});
					
	
			});

				</script> 


			<?php }

	