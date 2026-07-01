<?php // Simple File List - ee-download.php - rev 1.19 - mitchellbennis@gmail.com
	
// Force File to Download
// This script is accessed via link by ee-list-display.php
	
ini_set("log_errors", 1);
error_reporting (E_ALL);
ini_set ('display_errors', FALSE);
ini_set("error_log", "../logs/ee-download-error.log");

// Tie into Wordpress
define('WP_USE_THEMES', false);
$wordpress = getcwd() . '/../../../../wp-blog-header.php';
require($wordpress);

// Security
if(!wp_verify_nonce($_GET['_wpnonce'], 'eesfl-download-file-nonce')) {
	trigger_error("WP Access Error", E_USER_ERROR);
	exit();
}

// Passed Values
if(@$_GET['eeSFL_File']) {
	$eeFile = filter_var($_GET['eeSFL_File'], FILTER_SANITIZE_STRING);
	$eeFile = basename($eeFile); // Make sure it's just the file name.
} else { 
	trigger_error("Missing File Argument", E_USER_ERROR); exit;
}

if(@$_GET['eeSFLF_Folder']) {
	$eeFolder = filter_var($_GET['eeSFLF_Folder'], FILTER_SANITIZE_STRING);
} else { 
	$eeFolder = FALSE;
}

if(@$_GET['eeSFL_ID']) { 
	$eeSFL_ID = filter_var($_GET['eeSFL_ID'], FILTER_VALIDATE_INT);
	$eeSFL_UploadDir = get_option('eeSFL-' . $eeSFL_ID . '-UploadDir');
} else {
	$eeSFL_ID = FALSE; exit;
}


// Build the path and URL
$wp_HomeDir = ABSPATH; // This Wordpress Root
$wp_HomeURL = get_site_url();

$eeSFL_DownloadDir = $wp_HomeDir . $eeSFL_UploadDir;
if($eeFolder) { $eeSFL_DownloadDir .= $eeFolder . '/'; }


// Detect upward path traversal
$realPath = realpath($eeSFL_DownloadDir) . '/' . $eeFile; // Defy traversal
$userPath = realpath($eeSFL_DownloadDir . $eeFile); // Might be trouble

if ($userPath === false || strpos($userPath, $realPath) !== 0) {
    exit('Directory Traversal is Not Allowed');
} else {
	$eeSFL_DownloadDir = $realPath;
}

$eeSFL_DownloadURL = $wp_HomeURL . $eeSFL_UploadDir;
if($eeFolder) { $eeSFL_DownloadURL .= $eeFolder . '/'; }
$eeSFL_DownloadURL .= $eeFile;




?><!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title><?php _e('File Download', 'ee-simple-file-list'); ?></title>

<script type="text/javascript">
    function startDownload() {
        window.location = "<?php echo 'ee-downloader.php?eeFile=' . urlencode($eeSFL_DownloadDir); ?>";
    }
</script>

<style type="text/css">

* {
	text-align: center;
	margin: 0 auto;
	margin-bottom: 1em;
	padding: 0;
}

body {
	background-color: #FFF;
	color: #333;
	margin-top: 3em;
}

a {
	color: #0049ff;
}

</style>

</head>

<body <?php echo 'onload="startDownload()"'; ?> >
	
	<h1><?php _e('File Download', 'ee-simple-file-list'); ?></h1>
	
	<h2><?php _e('Downloading', 'ee-simple-file-list'); ?>...<br />
		<?php echo $eeFile; ?></h2>
	
	<p><?php _e('If the download fails to start', 'ee-simple-file-list'); ?> <a href="<?php echo $eeSFL_DownloadURL; ?>"><?php _e('click here', 'ee-simple-file-list'); ?></a></p>	
		
	<p><small><a href="javascript:self.close();"><?php _e('Close this Window', 'ee-simple-file-list'); ?></a></small></p>
	
</body>
</html>