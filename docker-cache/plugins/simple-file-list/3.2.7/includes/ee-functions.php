<?php // Simple File List - General Plugin Functions - mitch@elementengage.com
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'eeSFL_Functions' ) ) exit('That is Noncense!'); // Exit if nonce fails

// $eeSFL_Log[] = 'Loaded: ee-functions';


// Custom Hooks
function eeSFL_UploadCompleted() {
    do_action('eeSFL_UploadCompleted');
}


// The form submission results bar at the top of the admin pages
function eeSFL_ResultsDisplay($eeSFL_Results, $eeResultType) { // error, updated, etc...
	
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
function eeSFL_MessageDisplay($eeSFL_Message) {
	
	$eeReturn = '';
	
	$eeSFL_Admin = is_admin();
	
	if(is_array($eeSFL_Message)) {
		
		if(!$eeSFL_Admin) { $eeReturn .= '<div id="eeMessageDisplay">'; }
		
		$eeReturn .= '<ul>'; // Loop through $eeSFL_Messages array
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

// Get what's in the address bar
function eeSFL_GetThisURL() {

	$eeProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
	if(strpos($eeProtocol, 'ttps') == 1) { $eeProtocol = 'https'; } else { $eeProtocol = 'http'; }
	$eeHost = $_SERVER['HTTP_HOST'];
	$eeScript = $_SERVER['SCRIPT_NAME'];
	$eeParams = $_SERVER['QUERY_STRING'];
	 
	$thisUrl = $eeProtocol . '://' . $eeHost . $eeScript . '?' . $eeParams;
	 
	return $eeProtocol;

}


// Log Failed Emails
function eeSFL_action_wp_mail_failed($wp_error) {
    return error_log(print_r($wp_error, true));
}
          
// add the action 
add_action('wp_mail_failed', 'eeSFL_action_wp_mail_failed', 10, 1);




?>