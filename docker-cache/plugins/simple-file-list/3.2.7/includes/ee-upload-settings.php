<?php // Simple File List Uploader Settings - Mitchell Bennis | Element Engage, LLC | mitch@elementengage.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'ee_include_page' ) ) exit('That is Noncence'); // Exit if nonce fails

$eeSFL_Log[] = 'Loading Uploader Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce')) {
		
	if($_POST['eeAllowUploads'] == 'YES') { 
		
		$eeSFL_AllowUploads = 'YES';
	
	} elseif($_POST['eeAllowUploads'] == 'USER') { // Only logged in users
		 
		 $eeSFL_AllowUploads = 'USER';
		 
	} elseif($_POST['eeAllowUploads'] == 'ADMIN') { // Only logged in users
		 
		 $eeSFL_AllowUploads = 'ADMIN';
		 
	} else { 
		$eeSFL_AllowUploads = 'NO';
	}
	update_option('eeSFL-' . $eeSFL_ID . '-AllowUploads', $eeSFL_AllowUploads); // Allow Uploads ?
	
	
	// Get Uploader Info
	if(@$_POST['eeGetUploaderInfo']) { // If uploader is Off, these inputs won't be available
		
		if(@$_POST['eeGetUploaderInfo'] == 'YES') { 
			
			$eeSFL_GetUploaderInfo= 'YES';
		
		} else { 
			$eeSFL_GetUploaderInfo= 'NO';
		}
		update_option('eeSFL-' . $eeSFL_ID . '-GetUploaderInfo', $eeSFL_GetUploaderInfo); // Allow Uploads ?
	}
	
	
	
	// File Limit
	$eeSFL_UploadLimit = filter_var(@$_POST['eeUploadLimit'], FILTER_VALIDATE_INT);
	if(!$eeSFL_UploadLimit) { $eeSFL_UploadLimit = 10; }
	update_option('eeSFL-' . $eeSFL_ID . '-UploadLimit', $eeSFL_UploadLimit);
	
	
	
	// Maximum File Size
	if(@$_POST['eeUploadMaxFileSize']) {
		
		$eeSFL_UploadMaxFileSize = (int) $_POST['eeUploadMaxFileSize'];
		
		if(!$eeSFL_UploadMaxFileSize OR $eeSFL_UploadMaxFileSize > $eeSFL_MaxPostSize) { // Can't be more than the system allows.
			$eeSFL_UploadMaxFileSize = $eeSFL_MaxPostSize;
		}
		
		$eeSFL_UploadMaxFileSize = $eeSFL_UploadMaxFileSize;
		
		update_option('eeSFL-' . $eeSFL_ID . '-UploadMaxFileSize', $eeSFL_UploadMaxFileSize); // Max File Size
		
	} else {
		$eeSFL_UploadMaxFileSize = 1;
	}
	
	
	// File Formats
	if(@$_POST['eeFileFormats']) {
		$eeSFL_FileFormats = preg_replace("/[^a-z0-9 ,]/i", "", $_POST['eeFileFormats']); // Strip all but what we need for the comma list of file extensions
		update_option('eeSFL-' . $eeSFL_ID . '-FileFormats', $eeSFL_FileFormats); // Allow Only These File Formats
	}

	// Track Owner
	if(@$_POST['eeTrackFileOwner']) {
		if(@$_POST['eeTrackFileOwner'] == 'YES') { $eeTrackFileOwner = 'YES'; } else { $eeTrackFileOwner = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-TrackFileOwner', $eeTrackFileOwner);	
	}
	
	// Custom Upload Folder
	$eeSFL_LastUploadDir = get_option('eeSFL-' . $eeSFL_ID . '-UploadDir');
	
	if(@$_POST['eeUploadDir']) {
		
		$eeSFL_UploadDir = filter_var($_POST['eeUploadDir'], FILTER_SANITIZE_STRING);
		
		// Get rid of leading slash
		if(strpos($eeSFL_UploadDir, '/') === 0) {
			$eeSFL_UploadDir = substr($eeSFL_UploadDir, 1);
		}
		
		$eeSFL_DirCheck = eeSFL_UploadDirCheck(ABSPATH . $eeSFL_UploadDir);
		
		if(@$eeSFL_DirCheck['Error']) {
			$eeSFL_Log['errors'][] = $eeSFL_DirCheck;
			$eeSFL_Log['errors'][] = __('Cannot create the file directory. Reverting to default.', 'ee-simple-file-list');
			$eeSFL_UploadDir = $eeSFL_UploadDefaultDir;
		}
		
		$eeSFL_Log[] = $eeSFL_DirCheck;	
	
	} else {
		
		$eeSFL_UploadDir = $eeSFL_UploadDefaultDir;
	}
	
	update_option('eeSFL-' . $eeSFL_ID . '-UploadDir', $eeSFL_UploadDir);
	
	
	$eeSFL_To = @$_POST['eeNotify'];
		
	if(strpos($eeSFL_To, ',')) { // Multiple Addresses
	
		$eeSFL_Addresses = explode(',', $eeSFL_To); // Make array
		
		$eeSFL_AddressesString = '';
		
		foreach($eeSFL_Addresses as $add){
			
			$add = trim($add);
			
			if(filter_var($add, FILTER_VALIDATE_EMAIL)) {
		
				$eeSFL_AddressesString .= $add . ',';
			} else {
				$eeSFL_Log['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
			}
		}
		
		$eeSFL_Notify = substr($eeSFL_AddressesString, 0, -1); // Remove last comma
		
	
	} elseif(filter_var(@$_POST['eeNotify'], FILTER_SANITIZE_EMAIL)) { // Only one address
		
		$add = $_POST['eeNotify'];
		
		if(filter_var($add, FILTER_VALIDATE_EMAIL)) {
			$eeSFL_Notify = $add;
		} else {
			$eeSFL_Log['errors'][] = $add . ' - ' . __('This is not a valid email address.', 'ee-simple-file-list');
		}
		
	} else {
		
		$eeSFL_Notify = ''; // Anything but a good email gets null.
	}
	
	update_option('eeSFL-' . $eeSFL_ID . '-Notify', $eeSFL_Notify); // Set Notification Address(es)
		
	$eeSFL_Confirm = __('Uploader Settings Saved', 'ee-simple-file-list');
	$eeSFL_Log[] = $eeSFL_Confirm;
	
	if($eeSFL_DevMode) {
		$eeSFL_Log[] = $_POST;
	}
	
	
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if(@$eeSFL_Log['errors']) { 
	
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error');
	
} elseif(@$eeSFL_Confirm) { 
	
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Confirm, 'updated');
}
	
$eeOutput .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL_Page . '&tab=list_settings&subtab=uploader_settings" method="post" id="eeSFL_Settings">
		<input type="hidden" name="eePost" value="TRUE" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-upload-settings', 'ee-simple-file-list-upload-settings-nonce' );
		
		$eeOutput .= '<fieldset>
			
			<h2>' . __('File Upload Settings', 'ee-simple-file-list') . '</h2>
			
			<label for="eeAllowUploads">' . __('File Uploader', 'ee-simple-file-list') . '</label>
			
			<select name="eeAllowUploads" id="eeAllowUploads">
			
				<option value="YES"';

				if($eeSFL_AllowUploads == 'YES') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Anyone Can Upload', 'ee-simple-file-list') . '</option>
				
				<option value="USER"';

				if($eeSFL_AllowUploads == 'USER') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Users Can Upload', 'ee-simple-file-list') . '</option>
				
				<option value="ADMIN"';

				if($eeSFL_AllowUploads == 'ADMIN') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Only Logged in Admins Can Upload', 'ee-simple-file-list') . '</option>
				
				<option value="NO"';

				if($eeSFL_AllowUploads == 'NO') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Hide the Front Side Uploader Completely', 'ee-simple-file-list') . '</option>
			
			</select>';
			
			
			$eeOutput .= '<div class="eeNote">' . 
				__('Allow anyone to upload, only logged in users, administrators or nobody.', 'ee-simple-file-list') . '</div>
					
			<br class="eeClearFix" />';
			
			
			if($eeSFL_AllowUploads != 'NO') {
				
				// Uploader Engine
					
				$eeSFL_UploadLimit = get_option('eeSFL-' . $eeSFL_ID . '-UploadLimit');
				
				$eeOutput .= '
				
				<label for="eeUploadLimit">' . __('Upload Limit', 'ee-simple-file-list') . '</label>
		
				<input type="number" min="1" max="100" step="1" name="eeUploadLimit" value="' . $eeSFL_UploadLimit . '" class="eeAdminInput" id="eeUploadLimit" />
					<div class="eeNote">' . __('The maximum number of files that may be uploaded per submission.', 'ee-simple-file-list') . '</div>
					
					<br class="eeClearFix" />';
					
					
				// If using custom location
				if($eeSFL_UploadDefaultDir != 'wp-content/uploads/simple-file-list/') { 
					
					// Custom Upload Folder
					
					$eeSFL_UploadDir = get_option('eeSFL-' . $eeSFL_ID . '-UploadDir');
					
					$eeOutput .= '<label for="eeUploadDir">' . __('Upload Directory', 'ee-simple-file-list') . ':</label><input type="text" name="eeUploadDir" value="';
				
					if($eeSFL_UploadDir) { $eeOutput .= $eeSFL_UploadDir; } else { $eeOutput .= $eeSFL_UploadDefaultDir; }
				
					$eeOutput .= '" class="eeAdminInput" id="eeUploadDir" size="64" />
						<div class="eeNote">' . __('This is relative to your Wordpress home folder.', 'ee-simple-file-list') . ' <em>wp-content/uploads/simple-file-list/</em> ' . __('is the default', 'ee-simple-file-list') . '.<br />
							' . __('This will create the directory if it does not yet exist.', 'ee-simple-file-list') . '<br />
							' . __('Your website must use a FQDN in order to change the path.', 'ee-simple-file-list') . '
						</div>
					
					<br class="eeClearFix" />';
					
				}
				
				
				// Maximum File Size
				
				$eeSFL_UploadMaxFileSize = get_option('eeSFL-' . $eeSFL_ID . '-UploadMaxFileSize');
				
				if(!$eeSFL_UploadMaxFileSize) { $eeSFL_UploadMaxFileSize = $eeSFL_MaxFileSize; }
				
				$eeOutput .= '<label for="eeUploadMaxFileSize">' . __('Maximum File Size', 'ee-simple-file-list') . ' (MB):</label><input type="number" min="1" max="' . $eeSFL_MaxPostSize . '" step="1" name="eeUploadMaxFileSize" value="' . $eeSFL_UploadMaxFileSize . '" class="eeAdminInput" id="eeUploadMaxFileSize" />
					<div class="eeNote">' . __('Your hosting limits the maximum file upload size to', 'ee-simple-file-list') . ' <strong>' . $eeSFL_MaxPostSize . ' MB</strong>.</div>
				
				<br class="eeClearFix" />';
				
				
				
				
				// Get Uploader Info
				
				$eeSFL_GetUploaderInfo = get_option('eeSFL-' . $eeSFL_ID . '-GetUploaderInfo');
				
				$eeOutput .= '<span>' . __('Get Uploader\'s Information?', 'ee-simple-file-list') . '</span><label for="eeGetUploaderInfoYes" class="eeRadioLabel">' . __('Yes', 'ee-simple-file-list') . '</label><input type="radio" name="eeGetUploaderInfo" value="YES" id="eeGetUploaderInfoYes"';
				
				if($eeSFL_GetUploaderInfo == 'YES') { $eeOutput .= ' checked'; }
				
				$eeOutput .= '/>
					<label for="eeFormNo" class="eeRadioLabel">' . __('No', 'ee-simple-file-list') . '</label><input type="radio" name="eeGetUploaderInfo" value="NO" id="eeFormNo"';
					
				if($eeSFL_GetUploaderInfo != 'YES') { $eeOutput .= ' checked'; }
				
				$eeOutput .= ' />
					<br class="eeClearFix" />
					<div class="eeNote">' . __('Displays a form which must be filled out', 'ee-simple-file-list') . '; ' . __('Name, Email, with optional text Notes.', 'ee-simple-file-list') . '<br />
						' . __('Submissions are sent to the Notice Email.', 'ee-simple-file-list') . '</div>
				<br class="eeClearFix" />';
				
				
				
				// File Formats Allowed
					
				$eeSFL_FileFormats = get_option('eeSFL-' . $eeSFL_ID . '-FileFormats');
				
				$eeOutput .= '<label for="eeFormats">' . __('Allowed File Types', 'ee-simple-file-list') . ':</label><textarea name="eeFileFormats" class="eeAdminInput" id="eeFormats" cols="64" rows="3" />' . $eeSFL_FileFormats . '</textarea>
					<div class="eeNote">' . __('Only use the file types you absolutely need, such as', 'ee-simple-file-list') . ' jpg, jpeg, png, pdf, mp4, etc</div>';
					
				
				// Upload Notification
				$eeOutput .= '<label for="eeNotify">' . __('Notice Email', 'ee-simple-file-list') . ':</label><input type="text" name="eeNotify" value="' . $eeSFL_Notify . '" class="eeAdminInput" id="eeNotify" size="64" />
						<div class="eeNote">' . __('You will get an email whenever a file is uploaded.', 'ee-simple-file-list') . ' ' .  __('Separate multiple addresses with a comma.', 'ee-simple-file-list') . '</div>';
			}
			
			$eeOutput .= '<br class="eeClearFix" />
			
			<input type="submit" name="submit" id="submit2" value="' . __('SAVE', 'ee-simple-file-list') . '" class="eeAlignRight" />
			
			</fieldset>
			
			
			
	</form>
	
</div>';
	
	
?>