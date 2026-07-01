<?php // Simple File List Support - mitch@elementengage.com
	
// Rev 03.08.19 
	
// Accessed via http://website.com/wp-content/plugins/simple-file-list/logs/index.php?eePIN=2006

// The point of this script is to allow me, Mitch, and only me, to access all of the basic info and error data in one page.
// I believe in good service and support.

// Must have the proper PIN in order to access the page
$eePIN = filter_var(@$_GET['eePIN'], FILTER_VALIDATE_INT);

// Must come from EE
$eeReferer = filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_STRING);
$eeRefererMust = 'elementengage.com/simple-file-list-wordpress-plugin/support';

if($eePIN == 2006 AND strpos($eeReferer, $eeRefererMust) ) { // PIN and Referer must match
		
	// Attempt to turn on basic PHP logging...
	ini_set('display_errors', TRUE);
	error_reporting(E_ALL);
	
	// Get what's in this address bar
	$eeProtocol = strtolower($_SERVER['SERVER_PROTOCOL']);
	if(strpos($eeProtocol, 'ttps') == 1) { $eeProtocol = 'https'; } else { $eeProtocol = 'http'; }
	$eeHost = $_SERVER['HTTP_HOST'];
	
	// Add the rest...
	$eeThisWP = $eeProtocol . '://' . $eeHost;
	$eeThisURL = $eeThisWP . '/wp-content/plugins/simple-file-list/logs/';
	
	// My log file names
	$eeMainLog = 'Simple-File-List-Log.txt';
	$eeUploadLog = 'ee-upload-error.log';
	$eeEmailLog = 'ee-email-error.log';
	
	// Read My Error Log Files
	$eeUploadLogContent = file_get_contents($eeThisURL . $eeUploadLog);
	$eeEmailLogContent = file_get_contents($eeThisURL . $eeEmailLog);
	
	
	
	// PHP Log
	$eeLog = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/error_log';
	if(!is_file($eeLog)) {
		$phpErrors = 'No PHP Error Log File Found :-(. ' . $eeLog;
	} elseif(filesize($eeLog) > 10) {
		$phpErrors = file_get_contents($eeLog);
	} else {
		$phpErrors = FALSE;
	}
	
	// Wordpress Log
	$eeLog = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/debug.log';
	if(is_readable($eeLog)) {
		$wpErrors = file_get_contents($eeLog);
	} else {
		$wpErrors = 'No Wordpress Error Log File Found :-(';
	}
	
	
	// Check the log file's size
	$eeBytes = filesize($eeMainLog);
    $eeKilobyte = 1024;
    if (($eeBytes >= 0) && ($eeBytes < $eeKilobyte)) {
        $eeFileSize = $bytes . ' B';
    } else {
        $eeFileSize = round($eeBytes / $eeKilobyte, 2) . ' KB';
    }
    
    
	
	
	// Page Setup
	$eeTitle = 'Simple File List Support';
		
	?><!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $eeTitle; ?></title>

<style type="text/css">

* {
	margin: 1.5em;
}

body {
	width: 75%;
	margin-left: auto;
	margin-right: auto;
}

h1, h2, h3, p {
	text-align: left;
}

p.log, iframe {
	text-align: left;
	padding: 1em;
	border: 1px dashed #666;
	margin: 1em 0 2em 0;
}
p.log {
	height: 300px;
	overflow: scroll;
}

</style>

</head>
<body>
	
	<p><a target="_blank" style="float:right;" href="#"><?php echo $eeProtocol . '://' . $eeHost; ?></a></p>
	
	<h1><?php echo $eeTitle; ?></h1>
	
	<?php // My plugin log files...
		
	// Upload Errors
	if(!$eeUploadLogContent) {
	
		echo '<h3 style="color:green;">&#x2714; No Uploader Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">Uploader Errors!</h4>';
		echo '<p class="log">' . $eeUploadLogContent . '</p>';
	}	
	
	// Email Errors
	if(!$eeEmailLogContent) {
	
		echo '<h3 style="color:green;">&#x2714; No Email Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">Email Errors!</h4>';
		echo '<p class="log">' . $eeEmailLogContent . '</p>';
	}
	
	// The Main Log File ?>
	
	<h3>The Main Log File (<?php echo $eeFileSize; ?>)</h3>
	<iframe src="<?php echo $eeMainLog; ?>" width="100%" height="300" frameborder="0" scrolling="on" name="eeMainLog" id="eeMainLog"></iframe>
	
	<?php // The Server Environment
	
	// PHP Errors
	if(!$phpErrors) {
	
		echo '<h3 style="color:green;">&#x2714; No PHP Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">PHP Errors!</h4>';
		echo '<p class="log">' . nl2br($phpErrors) . '</p>';
	}
	
	
	// WP Errors
	if(!$wpErrors) {
	
		echo '<h3 style="color:green;">&#x2714; No Wordpress Errors</h3>';
		
	} else { 
		
		echo '<h3 style="color:red;">Wordpress Errors!</h4>';
		echo '<p class="log">' . nl2br($wpErrors) . '</p>';
	}

	
	echo '<br /><br /><h2>PHP Environment</h2>';
		
	// Get Environment Info
	phpinfo(); ?>
	

	
	
</body>
</html><?php
	
} // End PIN check
	
?>