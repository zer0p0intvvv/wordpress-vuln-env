<?php // Simple File List - ee-class.php - mitchellbennis@gmail.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'ee_include_page' ) ) exit('That is Noncense!'); // Exit if nonce fails

// $eeSFL_Log[] = 'Loaded: ee-class';


// Plugin Setup
class eeSFL_MainClass { // Plugin Configuration --> Environment, User, Settings
    
    public $filesTotalCount = 0;
    
    public function eeSFL_Config($eeSFL_ListID = 1) {
	    
	    // wp_options --> option_name
	    
		// eeSFL-1-FileFormats
		// eeSFL-1-UploadDir
		// eeSFL-1-AllowUploads
		// eeSFL-1-FileFormats
		// eeSFL-1-GetUploaderInfo
		// eeSFL-1-ShowFileThumb
		// eeSFL-1-ShowFileDate
		// eeSFL-1-ShowFileOwner
		// eeSFL-1-ShowFileSize
		// eesFL-1-ShowFileActions
		// eeSFL-1-ShowList
		// eeSFL-1-ShowListHeader
		// eeSFL-1-SortBy
		// eeSFL-1-SortOrder
		// eeSFL-1-AllowFrontDelete
		// eeSFL-1-UploadDir
		// eeSFL-1-UploadLimit
		// eeSFL-1-UploadMaxFileSize
		// eeSFL-Notify
		// eeSFL-Extensions
		
		// We store all these returned configuation values in this array, like this...
		// $eeSFL_Config['eeSFL_Key'] = 'Value';
		// After the function is called, the script loops through this array and builds varible Names/Values from the Keys/Values
		// It's Genius, ah?
		$eeSFL_Config = array(); 
	
		$eeSFL_Config['eeSFL_ID'] = $eeSFL_ListID;  // The List ID
			
		// Basics
		$eeSFL_Config['eeSFL_PluginName'] = 'Simple File List';
		$eeSFL_Config['eeSFL_PluginSlug'] = 'ee-simple-file-list';
		$eeSFL_Config['eeSFL_WebPage'] = 'http://simplefilelist.com';
		
		
		// This Wordpress Root
		$wp_HomeDir = ABSPATH;
		$wp_HomeURL = get_site_url() . '/';
		
		// The Wordpress Upload Location
		$wp_UploadDirArray = wp_upload_dir();
		$wp_UploadDir = $wp_UploadDirArray['basedir'];
		$wp_UploadDir  = str_replace($wp_HomeDir, '/', $wp_UploadDir) . '/'; // Make relative to WP root
		
		 // Upload Path Relative to Wordpress Installation Root
		$eeSFL_Config['eeSFL_UploadDefaultDir'] = $wp_UploadDir . 'simple-file-list/';
		
		// Get Configured Upload Location
		$eeSFL_UploadDir = get_option('eeSFL-' . $eeSFL_ListID . '-UploadDir');
		$eeSFL_Config['eeSFL_UploadDirName'] = $eeSFL_UploadDir; // Relative to the WP root
		
		// Set if not set
		if(!$eeSFL_UploadDir) {
			update_option('eeSFL-' . $eeSFL_ListID . '-UploadDir', $eeSFL_Config['eeSFL_UploadDefaultDir']);
			$eeSFL_Config['eeSFL_UploadDir'] = $eeSFL_Config['eeSFL_UploadDefaultDir'];
		}
		
		// Assemble the full paths
		$eeSFL_Config['eeSFL_UploadDir'] = $wp_HomeDir . $eeSFL_UploadDir; // Relative to the Home Dir
		$eeSFL_Config['eeSFL_UploadURL'] = $wp_HomeURL . $eeSFL_UploadDir; // Full URL
		
		
		
		// Plugin Location
		$eeSFL_Config['eeSFL_PluginPath'] = plugin_dir_path(__FILE__) . '../'; // The plugin's root
		$eeSFL_Config['eeSFL_PluginURL'] = plugin_dir_url(__FILE__);
				
		
		// Database Information
		
		// The List
		$eeSFL_Config['eeSFL_ShowThumb'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowFileThumb');
		$eeSFL_Config['eeSFL_ShowList'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowList');
		$eeSFL_Config['eeSFL_ShowFileDate'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowFileDate');
		$eeSFL_Config['eeSFL_ShowFileOwner'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowFileOwner');
		$eeSFL_Config['eeSFL_ShowFileSize'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowFileSize');
		$eeSFL_Config['eeSFL_ShowFileActions'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowFileActions');
		$eeSFL_Config['eeSFL_SortBy'] = get_option('eeSFL-' . $eeSFL_ListID . '-SortBy');
		$eeSFL_Config['eeSFL_SortOrder'] = get_option('eeSFL-' . $eeSFL_ListID . '-SortOrder');
		$eeSFL_Config['eeSFL_ShowListHeader'] = get_option('eeSFL-' . $eeSFL_ListID . '-ShowListHeader');
		$eeSFL_Config['eeSFL_AllowFrontDelete'] = get_option('eeSFL-' . $eeSFL_ListID . '-AllowFrontDelete');
			
			
				
		// File Uploads
		$eeSFL_Config['eeSFL_AllowUploads'] = get_option('eeSFL-' . $eeSFL_ListID . '-AllowUploads');
		$eeSFL_Config['eeSFL_UploadLimit'] = get_option('eeSFL-' . $eeSFL_ListID . '-UploadLimit'); // in MB
		$eeSFL_Config['eeSFL_FileFormats'] = get_option('eeSFL-' . $eeSFL_ListID . '-FileFormats');
		$eeSFL_Config['eeSFL_GetUploaderInfo'] = get_option('eeSFL-' . $eeSFL_ListID . '-GetUploaderInfo');
		
		
		// Environment Upload Limitations
		
		$eeSFL_Config['eeSFL_UploadMaxFileSize'] = get_option('eeSFL-' . $eeSFL_ListID . '-UploadMaxFileSize');
		
		// Reality...
		$eeSFL_Config['eeSFL_upload_max_upload_size'] = substr(ini_get('upload_max_filesize'), 0, -1); // Strip off the "M".
		$eeSFL_Config['eeSFL_MaxPostSize'] = substr(ini_get('post_max_size'), 0, -1); // Strip off the "M".
		
		// Compare
		if ($eeSFL_Config['eeSFL_upload_max_upload_size'] <= $eeSFL_Config['eeSFL_MaxPostSize']) { // Check which is smaller, upload size or post size.
			$eeSFL_Config['eeSFL_MaxFileSize'] = $eeSFL_Config['eeSFL_upload_max_upload_size'];
		} else {
			$eeSFL_Config['eeSFL_MaxFileSize'] = $eeSFL_Config['eeSFL_MaxPostSize'];
		}
		
		// Set the lower of the two
		if($eeSFL_Config['eeSFL_MaxFileSize'] < $eeSFL_Config['eeSFL_UploadMaxFileSize']) {
			$eeSFL_Config['eeSFL_UploadMaxFileSize'] = $eeSFL_Config['eeSFL_MaxFileSize']; // If Env is lower, set to that.
		}
		
		// Notifications
		$eeSFL_Config['eeSFL_Notify'] = get_option('eeSFL-' . $eeSFL_ListID . '-Notify');
		
		
		// Also...
		if( is_admin() ) {
			$eeSFL_Config['eeSFL_Admin'] = 'YES';
		} else {
			$eeSFL_Config['eeSFL_Admin'] = FALSE;
		}
	
		
		// The whole point...	
		return $eeSFL_Config; // Associative array
		
	}
	
	
	
	
	
	public function eeSFL_SortFiles($eeSFL_UploadDir, $eeSFL_Files, $eeSFL_SortBy, $eeSFL_SortOrder) {
			
		global $eeSFL_Log;
		
		// $eeSFL_Log[] = 'Sorting Files...';
		
		if(count($eeSFL_Files)) {
			
			// Files by Name
			if($eeSFL_SortBy == 'Date') { // Files by Date
				
				$eeSFL_FilesByDate = array();
				foreach($eeSFL_Files as $eeSFL_File){
					$eeSFL_FileDate = filemtime($eeSFL_UploadDir . $eeSFL_File); // Get byte Date, yum.
					$eeSFL_FilesByDate[$eeSFL_File] = $eeSFL_FileDate; // Associative Array
				}
				
				// Sort order
				if($eeSFL_SortOrder == 'Descending') {
					arsort($eeSFL_FilesByDate);
				} else {
					asort($eeSFL_FilesByDate); // Sort by Date, ascending
				}
				
				$eeSFL_Files = array_flip($eeSFL_FilesByDate); // Swap keys & values	
				
			} elseif($eeSFL_SortBy == 'Size') { // Files by Size
				
				$eeSFL_FilesBySize = array();
				foreach($eeSFL_Files as $eeSFL_File){
					$eeSFL_FileSize = filesize($eeSFL_UploadDir . $eeSFL_File); // Get byte size, yum.
					$eeSFL_FilesBySize[$eeSFL_File] = $eeSFL_FileSize; // Associative Array
				}
				
				// Sort order
				if($eeSFL_SortOrder == 'Descending') {
					arsort($eeSFL_FilesBySize);
				} else {
					asort($eeSFL_FilesBySize); // Sort by Date, ascending
				}
				
				$eeSFL_Files = array_flip($eeSFL_FilesBySize); // Swap keys & values
		
			} elseif($eeSFL_SortBy == 'Name') { // Alpha
				
				@natcasesort($eeSFL_Files);
				
				// Sort order
				if($eeSFL_SortOrder == 'Descending') {
					arsort($eeSFL_Files);
				}
			}
		}
		
		// $eeSFL_Log[] = $eeSFL_Files;
		
		return $eeSFL_Files;
		
	}
	
	
	
	
	
	// Our Basic File List
	public function eeSFL_ListFiles($eeSFL_UploadDir, $eeSFL_Excluded) {
		
		global $eeSFL_Log;
		
		$eeSFL_Files = array();
		
		$eeSFL_Log[] = 'Listing Files Within ...';
		$eeSFL_Log[] = $eeSFL_UploadDir;
		
		// List files in folder, add to array.
		if ($eeSFL_Handle = @opendir($eeSFL_UploadDir)) {
		
			while(false !== ($eeSFL_File = readdir($eeSFL_Handle))) {
				
				// Don't list excluded files
				if(!@in_array($eeSFL_File, $eeSFL_Excluded)) {
					
					// Not allowed to list: .php, .exe, .js, .com, .wsh, .vbs
					$eeSFL_ForbiddenFormats = array('php', 'exe', 'js', 'com', 'wsh', 'vbs');
					
					$eeExt = substr(strrchr($eeSFL_File,'.'), 1); // Get the extension, lazy way
					
					if(!in_array($eeExt, $eeSFL_ForbiddenFormats)) {
					
						if(@is_file($eeSFL_UploadDir . '/' . $eeSFL_File)) { // Don't show directories.
							
							// Don't list the home dir
							if($eeSFL_File == 'wp-config.php') {
								$eeSFL_Log['errors'][] = 'File List Directory Error: Listing Root Directory';
								$eeSFL_Log['errors'][] = $eeSFL_UploadDir;
								$eeSFL_Log['errors'][] = 'List Terminated.';
								return;
							}
							
							// Get file info...
							$eeSFL_Files[] = $eeSFL_File; // Add the file to the array
						}
					}	
				}
			}
			@closedir($eeSFL_Handle);
			
		} else {
		
			$eeSFL_Log['errors'][] = "Can't read the files in the Uploads folder.";
			
			return FALSE;
		}
		
		// $eeSFL_Log[] = $eeSFL_Files;
		
		return $eeSFL_Files;
		
	}
	
	
	
	
	// Send a system notification email, via ee-email-engine.php 
	public function eeSFL_AjaxEmail($eeSFL_UploadJob, $eeSFL_Notify) {
		
		global $eeSFL_Log;
		
		// Email Notifications
		$eeSFL_Message = '';
		$eeSFL_Body = '';
		
		// Notifications via AJAX Engine
		if($_POST AND !@$eeSFL_UploadJob['Error'] AND !is_admin() ) {
		
			$eeSFL_Log['notice'][] = 'Sending Notification to ' . $eeSFL_Notify;
			
			// Create nonce to be checked on the other side
			$eeSFL_EmailNonce = wp_create_nonce('ee-simple-file-list-email');
			
			// Build the Message Body
			
			$eeSFL_Body = __('Greetings', 'ee-simple-file-list') . ",\n\n";
				
			$eeSFL_Body .= $eeSFL_UploadJob['Message'] . "\n\n";
			
			// Get Form Input?
			if(@$_POST['eeSFL_Email']) {
				
				$eeSFL_Name = substr(filter_var(@$_POST['eeSFL_Name'], FILTER_SANITIZE_STRING), 0, 64);
				$eeSFL_Name = strip_tags($eeSFL_Name);
				$eeSFL_Body .= __('Uploaded By', 'ee-simple-file-list') . ': ' . ucwords($eeSFL_Name) . " - ";
				
				$eeSFL_Email = substr(filter_var(@$_POST['eeSFL_Email'], FILTER_VALIDATE_EMAIL), 0, 128);
				$eeSFL_Body .= strtolower($eeSFL_Email) . "\n\n";
				$eeSFL_ReplyTo = $eeSFL_Name . ' <' . $eeSFL_Email . '>';
				
				$eeSFL_Notes = substr(filter_var(@$_POST['eeSFL_Notes'], FILTER_SANITIZE_STRING), 0, 5012);
				$eeSFL_Notes = strip_tags($eeSFL_Notes);
				$eeSFL_Body .= $eeSFL_Notes . "\n\n";
		
			}
			
			$eeSFL_Body .= "\n\n----------------------\n\nVia: Simple File List, " . __('located at', 'ee-simple-file-list') . ' ' . get_permalink();
			
			// Send the message to the Email Engine via Ajax
			
			$eeOutput = '<script type="text/javascript">
				
				console.log("Simple File List - Upload Notification");
				
				function eeSFL_Notification() {
				
					var eeSFL_Url = "' . plugin_dir_url( __FILE__ ) . 'ee-email-engine.php' . '";
					var eeSFL_Xhr = new XMLHttpRequest();
					var eeSFL_FormData = new FormData();
					    
					console.log("Calling Email Engine: " + eeSFL_Url);
					    
					eeSFL_Xhr.open("POST", eeSFL_Url, true);
					    
				    eeSFL_Xhr.onreadystatechange = function() {
				        
				        if (eeSFL_Xhr.readyState == 4) {
			            
			            	// Every thing ok
				            console.log("RESPONSE: " + eeSFL_Xhr.responseText);
				            
				            if(eeSFL_Xhr.responseText == "SENT") {
					            
				            	console.log("Message Sent");
								
					        } else {
						    	
						    	console.log("XHR Status: " + eeSFL_Xhr.status);
						    	console.log("XHR State: " + eeSFL_Xhr.readyState);
						    	
						    	var n = eeSFL_Xhr.responseText.search("<"); // Error condition
						    	
						    	if(n === 0) {
							    	console.log("Error Returned: " + eeSFL_Xhr.responseText);
							    }
							    return false;
					        }
				        
				        } else {
					    	console.log("XHR Status: " + eeSFL_Xhr.status);
					    	console.log("XHR State: " + eeSFL_Xhr.readyState);
					    	return false;
				        }
				    };';
				    
				    // Security First
				    $eeSFL_Timestamp = time();
				    $eeSFL_TimestampMD5 = md5('eeSFL_' . $eeSFL_Timestamp);
				    
				    $eeSFL_Body = json_encode($eeSFL_Body);
				    
				    $eeOutput .= '
				    
				    eeSFL_FormData.append("eeSFL_Timestamp", "' . $eeSFL_Timestamp . '");
				     
				    eeSFL_FormData.append("eeSFL_Token", "' . $eeSFL_TimestampMD5 . '");
				    
				    eeSFL_FormData.append("eeSFL_Notify", "' . $eeSFL_Notify . '");
				    
	 			    eeSFL_FormData.append("eeSFL_Body", ' . $eeSFL_Body . ');
				        
				    eeSFL_Xhr.send(eeSFL_FormData);
				    
				}
				
				';
				
				$eeOutput .= 'eeSFL_Notification();'; // Run the above function right now
				
				$eeOutput .= '
				
			</script>'; // Ends $eeOutput
			
			// $eeSFL_Log['notice'][] = $eeSFL_Body;
			
			return $eeOutput;
		
		} else {
			$eeSFL_Log[] = 'Upload Notification Missing Input';
		}
		
		
	}
	
	
	// Upload Info Form Display
	public function eeSFL_UploadInfoForm() {
		
		$eeOutput = '<div id="eeUploadInfoForm"><h4>' . __('Your Information', 'ee-simple-file-list') . '</h4>
			
			<label for="eeSFL_Name">' . __('Name', 'ee-simple-file-list') . ':</label>
			<input placeholder="(required)" required type="text" name="eeSFL_Name" value="" id="eeSFL_Name" size="64" maxlength="64" /> 
			
			<label for="eeSFL_Email">' . __('Email', 'ee-simple-file-list') . ':</label>
			<input placeholder="(required)" required type="email" name="eeSFL_Email" value="" id="eeSFL_Email" size="64" maxlength="128" />
			
			<label for="eeSFL_Notes">' . __('Comments', 'ee-simple-file-list') . ':</label>
			<textarea name="eeSFL_Notes" id="eeSFL_Notes" rows="5" cols="64" maxlength="5012"></textarea></div>';
			
		return $eeOutput;
	
	}
	
	
	// The form submission results bar at the top of the admin pages
	public function eeSFL_ResultsDisplay($eeSFL_Results, $eeResultType) { // error, updated, etc...
		
		$eeReturn = '<div class="';
		
		if(is_admin()) {
			$eeReturn .= $eeResultType;
		} else {
			$eeReturn .= 'eeResult';
		}
		
		$eeReturn .= '"><p>';
		$eeReturn .= eeSFL_MessageDisplay($eeSFL_Results); // Parse the message array
		$eeReturn .= '</p></div>';
		
		return $eeReturn;
	}
	
	
	
	// Problem Display / Error reporting
	public function eeSFL_MessageDisplay($eeSFL_Message) {
		
		$eeReturn = '';
		
		$eeSFL_Admin = is_admin();
		
		if(is_array($eeSFL_Message)) {
			
			if(!$eeSFL_Admin) { $eeReturn .= '<div id="eeMessageDisplay">'; }
			
			$eeReturn .= '<ul>'; // Loop through $eeSFL_Log['messages'] array
			foreach($eeSFL_Message as $key => $value) { 
				if(is_array($value)) {
					foreach ($value as $value2) {
						$eeReturn .= "<li>$value2</li>\n";
					}
				} else {
					$eeReturn .= "<li>$value</li>\n";
				}
			}
			$eeReturn .= "</ul>\n";
			
			if(!$eeSFL_Admin) { $eeReturn .= '</div>'; }
			
			return $eeReturn;
			
		} else {
			return $eeSFL_Message;
		}
	}
	
	
	
	
	
	public function eeSFL_FileThumbnail($eeSFL_UploadDir, $eeSFL_UploadURL, $eeSFL_File, $eeSFL_FileThumbSize = 64) {
		
		global $eeSFL_Log, $eeSFL_UploadMaxFileSize;
		
		// Config
		$eeExt = FALSE;
		$eeScreenshot = FALSE;
		$eeDefaultThumbnails = plugins_url() . '/simple-file-list/images/thumbnails/'; // Default thumbnails url
		$eeThumbURL = FALSE;
		$eeThumbsURL = $eeSFL_UploadURL . '.thumbnails/'; // Dynamicly created thumbnails are here
		$eeThumbsPATH = $eeSFL_UploadDir . '.thumbnails/'; // Path to them
		
		// Else, we make or assign a default thumbnail iamge ...
		
		// Get File Info
		$eePathParts = pathinfo($eeThumbsPATH . $eeSFL_File);
		$eeDirName = $eePathParts['dirname'];
		$eeBaseName = $eePathParts['basename'];
		$eeExt = strtolower(@$eePathParts['extension']);
		$eeFileName = $eePathParts['filename'];
		
		// $eeSFL_Log[] = 'Extension: ' . $eeExt;
		
		
		// Is there already a thumb?
		if(is_readable($eeThumbsPATH . 'thumb_' . $eeSFL_File)) {
			return $eeThumbsURL . 'thumb_' . $eeSFL_File;
		} else {
			$eePNG = str_replace($eeExt, 'png', $eeSFL_File); // Check for video thumb, which has a different extension.
			if(is_readable($eeThumbsPATH . 'thumb_' . $eePNG)) {
				return $eeThumbsURL . 'thumb_' . $eePNG;
			}
		}
		
		
		// FFmpeg Support
		$eeVideoFormats = array('avi', 'flv', 'm4v', 'mov', 'mp4', 'wmv'); // Is this a video ?
		
		if(in_array($eeExt, $eeVideoFormats)) {
			
			if(trim(shell_exec('type -P ffmpeg'))) { // Check for FFMPEG
				
				$eeSFL_Log[] = 'FFmpeg Installed!';
				
				$eeExt = 'png'; // Set the extension
				$eeScreenshot = $eeThumbsPATH . 'eeScreenshot_' . $eeFileName . '.' . $eeExt; // Create a temporary file
				
				// Create a full-sized image at the one-second mark
				$eeCommand = 'ffmpeg -i ' . $eeSFL_UploadDir . $eeSFL_File . ' -ss 00:00:01.000 -vframes 1 ' . $eeScreenshot;
				
				$eeFFmpeg = trim(shell_exec($eeCommand));
					
				if(is_readable($eeScreenshot)) {
					
					$eeSFL_File = basename($eeScreenshot); // It worked
				
					// Switch the path that the script below will use to look for the file used to make a thumb of.
					$eeSFL_UploadDir = $eeThumbsPATH;
				
				} else {
					$eeSFL_Log['errors'][] = __('FFmpeg Error - File Not Created', 'ee-simple-file-list');
					$eeSFL_Log['errors'][] = $eeSFL_File;
					$eeSFL_Log['errors'][] = $eeVideoThumb;
				}	
			} else {
				// $eeSFL_Log[] = 'FFmpeg Not Installed';
			}
		}
		
		
		// Generate a Thumbnail Image
		if(!$eeExt OR strpos($eeExt, '.') === 0) { // It's a Folder or Hidden
			
			$eeExt = 'folder';
			
		} else {
		
			// Dynamically Create Thumbnails --------------
			
			// Known image files
			$eeImageExts = array('gif', 'jpg', 'jpeg', 'png');
			
			if(in_array($eeExt, $eeImageExts)) { // Just for known image files... 					
					
				// Thank Wordpress for this easyness.
				$eeFileImage = wp_get_image_editor($eeSFL_UploadDir . $eeSFL_File); // Try to open the file
	        
		        if (!is_wp_error($eeFileImage)) { // Image File Opened
		            
		            $eeFileImage->resize($eeSFL_FileThumbSize, $eeSFL_FileThumbSize, TRUE); // Create the thumbnail
		            
		            if(strpos($eeSFL_File, 'eScreenshot_')) {
			            $eeSFL_File = str_replace('eeScreenshot_', '', $eeSFL_File); // Strip the temp term
		            }
		            
		            $eeFileImage->save($eeThumbsPATH . 'thumb_' . $eeSFL_File); // Save the file
		            
		            $eeThumbURL = $eeThumbsURL . 'thumb_' . $eeSFL_File; // Build full URL
		            
		            if($eeScreenshot) {
			            unlink($eeScreenshot); // Delete the screeshot file
		            }
		        
		        } else { // Cannot open
			     
			        // $eeLog[] = 'Not an Image: ' . $eeSFL_File;   
		        }
			}
		}		
		
		// Assign Default Thumbnail
		if($eeThumbURL) { 
			
			$eeSFL_Log[] = 'Thumbnail Image Created for ' . $eeSFL_File;
		
		} else {
			
			$eeThumbURL = $eeDefaultThumbnails . $eeExt . '.svg';
			
			if($eeExt != 'folder' AND !eeSFL_UrlExists($eeThumbURL)) {
				$eeThumbURL = $eeDefaultThumbnails . '!default.svg';
			}
			
			// $eeSFL_Log[] = 'Default Thumbnail Assigned to ' . $eeSFL_File;
		}
		
		
		// $eeSFL_Log[] = 'Thumbnail: ' . $eeThumbURL;
		
		// The Return
		return $eeThumbURL; // Full path to image file
	}
	
	
	
	
	// Write a log file to keep a record of things.
	public function eeSFL_WriteLogFile($eeSFL_Log) {
		
		$eeOutput = ''; // Used for DevMode
		
		if($eeSFL_Log) {
		
			// The log file
			$eeSFL_LogFile = plugin_dir_path( __FILE__ ) . '../logs/Simple-File-List-Log.txt';
			
			$eeSize = 0; // The size of the log file, TBD.
			
			// File Size Management 256k
			$eeLimit = 25600; // We delete and start over if the log gets larger than this.
		
			if(@is_file($eeSFL_LogFile)) {
				$eeSize = filesize($eeSFL_LogFile); // Get the file size
			}
		
			if($eeSize > $eeLimit) {
				unlink($eeSFL_LogFile); // Delete the file. Start Anew.
			}
		
			// Write the Log Entry
			if($handle = @fopen($eeSFL_LogFile, "a+")) { // Create if needed
				
				if(@is_writable($eeSFL_LogFile)) {
				    
					fwrite($handle, 'Date: ' . date("Y-m-d H:i:s") . "\n");
				    
				    foreach($eeSFL_Log as $key => $logEntry){
				    
				    	if(is_array($logEntry)) { 
					    	
					    	foreach($logEntry as $key2 => $logEntry2){
						    	@fwrite($handle, '(' . $key2 . ') ' . $logEntry2 . "\n");
						    }
						    
					    } else {
						    fwrite($handle, '(' . $key . ') ' . $logEntry . "\n");
					    }
				    }
				    	
				    fwrite($handle, "\n\n\n---------------------------------------\n\n\n"); // Separator
				
				    fclose($handle);
				 
				} else {
				    return FALSE;
				}	
				
			} else {
				return FALSE;
			}
	
		} else {
			return FALSE;
		}
		
		return $eeOutput;
	
	}	
	
} // END Class ?>