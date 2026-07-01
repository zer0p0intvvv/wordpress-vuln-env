<?php	
	
echo'<!--Register with miniOrange-->
	<form name="f" method="post" action="">
		<input type="hidden" name="option" value="mo_wpns_register_customer" />
		<div class="mo_wpns_divided_layout">
			<div class="mo_wpns_setting_layout">
				<h3>Register with miniOrange</h3>
				<p>Just complete the short registration below to configure Limit Login Attempts plugin. Please enter a valid email id that you have access to. You will be able to move forward after verifying an OTP that we will send to this email.</p>
				<table class="mo_wpns_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input class="mo_wpns_table_textbox" type="email" name="email"
							required placeholder="person@example.com"
							value="'.$current_user->user_email.'" /></td>
					</tr>

					<tr>
						<td><b><font color="#FF0000">*</font>Website/Company Name:</b></td>
						<td><input class="mo_wpns_table_textbox" type="text" name="companyName"
							required placeholder="Enter your companyName"
							value="'.$_SERVER["SERVER_NAME"].'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;FirstName:</b></td>
						<td><input class="mo_wpns_table_textbox" type="text" name="firstName"
							placeholder="Enter your First Name"
							value="'.$current_user->user_firstname.'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;LastName:</b></td>
						<td><input class="mo_wpns_table_textbox" type="text" name="lastName"
							placeholder="Enter your Last Name"
							value="'.$current_user->user_lastname.'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;Phone number:</b></td>
						<td><input class="mo_wpns_table_textbox" type="tel" id="phone"
							pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" name="phone"
							title="Phone with country code eg. +1xxxxxxxxxx"
							placeholder="Phone with country code eg. +1xxxxxxxxxx"
							value="'.get_option('mo_wpns_admin_phone').'" /><br>We will call only if you need support.</td>
						<td></td>
					</tr>

					<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_wpns_table_textbox" required type="password"
							name="password" placeholder="Choose your password (Min. length 6)" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
						<td><input class="mo_wpns_table_textbox" required type="password"
							name="confirmPassword" placeholder="Confirm your password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><br /><input type="submit" name="submit" value="Next" style="width:100px;"
							class="button button-primary button-large" /></td>
					</tr>
				</table>
			</div>
		</div>
	</form>';