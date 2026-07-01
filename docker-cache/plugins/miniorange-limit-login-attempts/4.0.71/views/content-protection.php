<?php
echo'<div>
			<div class="mo_wpns_setting_layout">';

	echo'		<h3>Content Protection</h3>
				<form id="mo_wpns_content_protection" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_content_protection">
					<p><input type="checkbox" name="protect_wp_config" '.$protect_wp_config.'> <b>Protect your wp-config.php file</b> &nbsp;&nbsp;<a href="'.$wp_config.'" target="_blank" style="text-decoration:none">( Test it )</a></p>
					<p>Your WordPress wp-config.php file contains your information like database username and password and it\'s very important to prevent anyone to access contents of your wp-config.php file.</p>
					<p><input type="checkbox" name="prevent_directory_browsing" '.$protect_wp_uploads.'> <b>Prevent Directory Browsing</b> &nbsp;&nbsp; <span style="color:green;font-weight:bold;">(Recommended)</span> &nbsp;&nbsp; <a href="'.$wp_uploads.'" target="_blank" style="text-decoration:none">( Test it )</a></p>
					<p>Prevent access to user from browsing directory contents like images, pdf\'s and other data from URL e.g. http://website-name.com/wp-content/uploads</p>
					<p><input type="checkbox" name="disable_file_editing" '.$disable_file_editing.'> <b>Disable File Editing from WP Dashboard (Themes and plugins)</b> &nbsp;&nbsp;<a href="'.$plugin_editor.'" target="_blank" style="text-decoration:none">( Test it )</a></p>
					<p>The WordPress Dashboard by default allows administrators to edit PHP files, such as plugin and theme files. This is often the first tool an attacker will use if able to login, since it allows code execution.</p>
					<br><input type="submit" name="submit" style="width:100px;" value="Save" class="button button-primary button-large">
				</form>
			</div>';


	echo '
			<div class="mo_wpns_setting_layout">
				<h3>Comment SPAM</h3>
				<p>This plugins prevents comment spam without requiring you to moderate any comments.</p>
				<form id="mo_wpns_enable_comment_spam_blocking" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_enable_comment_spam_blocking">
					<input type="checkbox" name="mo_wpns_enable_comment_spam_blocking" '.$comment_spam_protect.' onchange="document.getElementById(\'mo_wpns_enable_comment_spam_blocking\').submit();"> Enable comments SPAM blocking by robots or automated scripts. <span style="color:green;font-weight:bold;">(Recommended)</span>
				</form><br>
				<form id="mo_wpns_activate_recaptcha_for_comments" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_activate_recaptcha_for_comments">
					<input type="checkbox" name="mo_wpns_activate_recaptcha_for_comments" '.$enable_recaptcha.' onchange="document.getElementById(\'mo_wpns_activate_recaptcha_for_comments\').submit();"> Add google reCAPTCHA verification for comments <span style="color:green;font-weight:bold;">(Recommended)</span>
				</form>';
		
		if($enable_recaptcha)
		{ 
			echo'
			<p>Before you can use reCAPTCHA, you must need to <b>register your domain/webiste</b> <a href="https://www.google.com/recaptcha/admin#list">here</a>.</p>
			<p>Enter Site key and Secret key that you get after registration.</p>
			<form id="mo_wpns_comment_recaptcha_settings" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_comment_recaptcha_settings">
				<table class="mo_wpns_settings_table">
					<tr>
						<td style="width:30%">Site key  : </td>
						<td style="width:25%"><input class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_site_key" required placeholder="site key" value="'.$captcha_site_key.'" /></td>
						<td style="width:25%"></td>
					</tr>
					<tr>
						<td>Secret key  : </td>
						<td><input class="mo_wpns_table_textbox" type="text"  name="mo_wpns_recaptcha_secret_key" required placeholder="secret key" value="'.$captcha_secret_key.'" /></td>
					</tr>
				</table>
				<input type="submit" value="Save Settings" class="button button-primary button-large" />
				<input type="button" value="Test reCAPTCHA Configuration" onclick="testRecaptchaConfiguration()" class="button button-primary button-large" />
			</form>';
		}

echo'	</div>
	';
?>
		<div class="mo_wpns_setting_layout">
		  <h3>Inactive User Logout</h3>
        <div class="mo_wpns_subheading">
            <small>
                Automatic logout if the user does not perform any action for the specified amount of the time.
            </small>
        </div>

            <input type="checkbox" name="mo_wpns_inactive_user_logout_checkbox" id="mo_wpns_inactive_user_logout_checkbox" <?php echo $InactiveUserLEnable; ?>/> Enable Inactive User Logout
        	<div id='mo_wpns_inactive_user_details'>
            <form id="mo_wpns_inactive_user_logout_form" method="post" action="">
                <table class="mo_wpns_settings_table">
                    <tr>
                        <td style="width:40%">Inactive Logout Duration(Seconds)<b style="color: red">[min = 20]:</b></td>
                        <td style="width:25%">
                            <div>
                                <input class="mo_wpns_table_textbox" type="number" id="mo_inactive_logout_duration" name="mo_inactive_logout_duration" required placeholder="30" maxlength="5" value="<?php echo get_option('mo_inactive_logout_duration');?>" min="20"/>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>Allowed in Admin session: </td>
                        <td><input type="checkbox" name="mo_inactive_allowed_admin_session" id="mo_inactive_allowed_admin_session" <?php if(get_option('mo_inactive_allowed_admin_session')) echo "checked";?> ></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><br><input type="button" name="SaveIUL" id="SaveIUL" style="width:100px;" value="Save" class="button button-primary button-large"></td>
                        <td></td>
                    </tr>
                </table>
            </form>
        </div>
		</div>

		<div class="mo_wpns_setting_layout">
        <h3>Disable XML-RPC</h3>
        <div class="mo_wpns_subheading">
            <small>
                An option to simply disable XML-RPC in WordPress.
                Most of the WordPress users don’t need XML-RPC and can disable it to prevent automated brute force attacks.
            </small>
        </div>

           <input type="checkbox" name="mo_wpns_disable_xml_rpc_checkbox" id="mo_wpns_disable_xml_rpc_checkbox" <?php if(get_option('mo_wpns_disable_xml_rpc')) echo "checked";?> > Disable XML-RPC
    	</div>

	</div>

<?php	

echo'
	<script>
		function testRecaptchaConfiguration(){
			var myWindow = window.open("'.$test_recaptcha_url.'", "Test Google reCAPTCHA Configuration", "width=600, height=600");	
		}
	</script>';
	?>
	<script type="text/javascript">
		jQuery('#mo_wpns_disable_xml_rpc_checkbox').click(function(){
			var disableXMLRPC 	= jQuery("input[name='mo_wpns_disable_xml_rpc_checkbox']:checked").val();
			var nonce 			= '<?php echo wp_create_nonce("XML-RPCNonce");?>';
			var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_xmlrpc_form',
				'nonce'						: nonce,
				'disableXMLRPC'				: disableXMLRPC
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response== 'DisabledXMLRPC')
						{
							jQuery('#wpns_message').empty();
							jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >XML-RPC Disable.</div>");
							window.scrollTo({ top: 0, behavior: 'smooth' });
						}
						else if(response == 'NotDisabledXMLRPC')
						{
							jQuery('#wpns_message').empty();
							jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >XML-RPC Enable.</div>");
							window.scrollTo({ top: 0, behavior: 'smooth' });
						}
				});

			
		});
		jQuery('#SaveIUL').click(function(){
			var enableIUL	 	= jQuery("input[name='mo_wpns_inactive_user_logout_checkbox']:checked").val();
			var nonce 			= '<?php echo wp_create_nonce("InactiveUserLNonce");?>';
			var loginDuration 	= jQuery("#mo_inactive_logout_duration").val(); 
			var adminSession	= jQuery("input[name='mo_inactive_allowed_admin_session']:checked").val();
			
			if(loginDuration != '' && loginDuration>=20)
			var data = {
				'action'					: 'wpns_login_security',
				'wpns_loginsecurity_ajax' 	: 'wpns_inactive_user_logout_form',
				'loginDuration'				: loginDuration,
				'adminSession'				: adminSession,
				'nonce'						: nonce,
				'enableIUL'					: enableIUL
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response == 'SettingsSaved')
						{
							jQuery('#wpns_message').empty();
							jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='height : 25px;padding-top: 10px;  ' >Settings Saved</div>");
							window.scrollTo({ top: 0, behavior: 'smooth' });
						}
						else if(response == 'NonceDidNotMatch')
						{
							jQuery('#wpns_message').empty();
							jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >Nonce verification failed</div>");
							window.scrollTo({ top: 0, behavior: 'smooth' });
						}
						else
						{	
							jQuery('#wpns_message').empty();
							jQuery('#wpns_message').append("<div class= 'notice notice-error is-dismissible' style='height : 25px;padding-top: 10px;  ' >An unknown Error has occured.</div>");
							window.scrollTo({ top: 0, behavior: 'smooth' });
						}

				});
				mo_inactive_allowed_admin_session
		});
	</script>


	<?php 