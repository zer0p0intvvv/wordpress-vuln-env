<?php // Simple File List - File List Functions - mitch@elementengage.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_File_Functions' ) ) exit('That is Noncense!'); // Exit if nonce fails

// $eeSFL_Log[] = 'Loaded: ee-file-functions';


// Post-process an upload job
function eeSFL_ProcessUpload($eeSFL_UploadURL, $eeSFL_UploadDir, $eeSFL_Notify) {
	
	global $eeSFL, $eeSFL_Log;
	
	$eeSFL_FileCount = filter_var($_POST['eeFileCount'], FILTER_VALIDATE_INT);
	
	if($eeSFL_FileCount) { 
	
		$eeSFL_Log[] = 'Post-processing Upload...';
		$eeSFL_Log[] = $eeSFL_FileCount . ' Files';
		
		$eeSFL_FileList = stripslashes($_POST['eeFileList']);
		
		// Check for Nonce
		if(check_admin_referer( 'ee-simple-file-list-upload', 'ee-simple-file-list-upload-nonce')) {
			
			$eeSFL_FileArray = json_decode($eeSFL_FileList);
			$eeNewArray = array(); // For our lowered-case extensions
			
			// Drop file extensions to lowercase
			foreach( $eeSFL_FileArray as $eeFile){
				$eeArray = explode('.', $eeFile);
				$eeNewArray[] = $eeArray[0] . '.' . strtolower($eeArray[1]);
			}
			$eeSFL_FileArray = $eeNewArray;
			
			// Files have been uploaded
			if(is_array($eeSFL_FileArray)) {
				
				$eeSFL_UploadJob = array(); // This will be what happened
				
				// Notification
				$eeSFL_UploadJob['Message'] = __('You should know that', 'ee-simple-file-list') . ' ';
				
				// Semantics
				if($eeSFL_FileCount > 1) { 
					$eeSFL_UploadJob['Message'] .= $eeSFL_FileCount . ' ' . __('files have', 'ee-simple-file-list');	
				} else {
					$eeSFL_UploadJob['Message'] .= __('a file has', 'ee-simple-file-list');
				}
				$eeSFL_UploadJob['Message'] .= ' ' . __('been uploaded to your website', 'ee-simple-file-list') . ".\n\n";
				
				// Loop through the uploaded files
				if(count($eeSFL_FileArray)) {
					
					foreach($eeSFL_FileArray as $eeSFL_File) { 
						
						$eeSFL_File = eeSFL_SanitizeFileName($eeSFL_File);
						
						// Notification
						$eeSFL_UploadJob['Message'] .=  $eeSFL_File . "\n" . 
							$eeSFL_UploadURL . $eeSFL_File . 
								"\n(" . @eeSFL_GetFileSize($eeSFL_UploadDir . $eeSFL_File) . ")\n\n\n";
					}
						
					$eeSFL_Log['messages'][] = __('File Upload Complete', 'ee-simple-file-list');
					
					// Send Email Notice
					$eeOutput = $eeSFL->eeSFL_AjaxEmail($eeSFL_UploadJob, $eeSFL_Notify);
					
					return $eeOutput; // All done.
					
					
				} else {
					$eeSFL_Log['errors'][] = 'Bad File Array';
					$eeSFL_Log['errors'][] = $eeSFL_UploadJob;
					return FALSE;
				}
			}
			
		} else {
			exit;
		}
	
	} else {
		$eeSFL_Log['errors'][] = 'No files?';
		return FALSE;
	}
	
	
}



// Check if a file's URL is reachable
function eeSFL_UrlExists($eeSFL_FileURL) {
	
	global $eeSFL_Log, $eeSFL;
	
	$parts = parse_url($eeSFL_FileURL);
	
	if($parts) { 
	 
		// CURL
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, $eeSFL_FileURL);
		
		// Set a User Agent for logs
		curl_setopt($connection, CURLOPT_USERAGENT, 'EE-Simple-File-List');
		curl_setopt($connection, CURLOPT_RETURNTRANSFER,1);
		
		// Don't follow redirects
		curl_setopt($connection, CURLOPT_FOLLOWLOCATION, 0); 
		
		// Timeout?
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($connection, CURLOPT_TIMEOUT, 2);
		     
		// Get the header
		curl_setopt($connection, CURLOPT_NOBODY, true);
		curl_setopt($connection, CURLOPT_HEADER, true);
		     
		// Handle HTTPS links
		if($parts['scheme'] == 'https'){
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST,  2);
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
		}
		 
		// Get the response
		$response = curl_exec($connection);
		curl_close($connection);
		
		$responseArray = explode(' ', $response);
		
		if($responseArray[1] == 200){
		
			$success = TRUE;
		
		} else { 
		  
		  	$success = FALSE;
		  	$eeSFL_Log = array($response); // No code found
		}
	
	} else {
		$response = '[Bad URL]';
		$code = filter_var($eeSFL_FileURL, FILTER_SANITIZE_STRING);
	}
	
	// Success ?
	if($success) {
		
		// $eeSFL_Log[] = 'File Return Code: ' . $response;
	  
	  	return $eeSFL_FileURL; // Yep
	
	} else { // Nope - Write errors to log file
	
		$eeSFL_Log['errors'][] = 'File Read Error - Could not read the file :-(';
		$eeSFL_Log['errors'][] = 'Response: ' . @$response;
		$eeSFL_Log['errors'][] = 'Code: ' . @$code;
		
		return FALSE;
	} 
}




// Make sure the file name is acceptable
function eeSFL_SanitizeFileName($eeSFL_FileName) {
	
	$eeSFL_FileName = sanitize_file_name( $eeSFL_FileName );
    
    return $eeSFL_FileName;
}




// Check if a file already exists, then number it so file will not be over-written.
function eeSFL_CheckForDuplicateFile($eeSFL_TargetFile) {
	
	if(is_file($eeSFL_TargetFile)) {
		
		// Get the file extension
		$eeSFL_Dot = strrpos($eeSFL_TargetFile, '.');
		$eeSFL_Extension = strtolower(substr($eeSFL_TargetFile, $eeSFL_Dot+1));
		
		// Append a version to the name
		$eeSFL_FilePath = substr($eeSFL_TargetFile, 0, $eeSFL_Dot);
		
		$eeSFL_CopyLimit = 1000; // Copy limit
		
		for ($i = 1; $i <= $eeSFL_CopyLimit; $i++) {
			
			$eeSFL_TargetFile = $eeSFL_FilePath . '_(' . $i . ').' . $eeSFL_Extension; // Indicate the copy number
			
			if(!is_file($eeSFL_TargetFile)) { break; }
		}							
	}
		
	return 	$eeSFL_TargetFile;
}




// Return the general size of a file in a nice format.
function eeSFL_GetFileSize($eeSFL_File) {  
    
    $bytes = filesize($eeSFL_File);
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;
    $precision = 2;
   
    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
 
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
 
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
 
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
 
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}



	
?>