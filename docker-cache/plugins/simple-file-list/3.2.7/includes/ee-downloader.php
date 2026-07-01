<?php // Simple File List - ee-downloader.php - rev 1.19 - mitchellbennis@gmail.com
	
// Force File to Download
// This script is accessed via javascript on ee-download.php 

$eeFile =  filter_var($_GET['eeFile'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);

// Detect upward path traversal
$eeFile = urldecode($eeFile);
$realPath = realpath($eeFile); // Defy traversal
$userPath = $eeFile; // Might be trouble

if ($userPath != $realPath) {
    exit('Directory Traversal is Not Allowed');
}

if(is_readable($eeFile)) {
	
	header('Pragma: public'); // required
	header('Expires: 0'); // no cache
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Last-Modified: '. gmdate ('D, d M Y H:i:s', filemtime ($eeFile)) .' GMT');
	header('Cache-Control: private',false);
	header('Content-Type: ' . mime_content_type($eeFile) );
	header('Content-Disposition: attachment; filename="'. basename($eeFile) .'"');
	// header('Content-Transfer-Encoding: binary');
	header('Content-Length: '. filesize($eeFile)); // provide file size
	header('Connection: close');
	readfile($eeFile); // Start the download

}
?>