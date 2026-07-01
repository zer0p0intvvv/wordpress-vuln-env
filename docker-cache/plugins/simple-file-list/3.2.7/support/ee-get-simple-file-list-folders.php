<?php // Buy the Extension
	
defined( 'ABSPATH' ) or die( 'No direct access is allowed' );
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'ee_include_page' ) ) exit('That is Noncense!'); // Exit if nonce fails

$eeSFL_Log[] = 'Loaded: ee-get-simple-file-list-folders';
$eeSFL_Button = '';

$eeSFL_ThisDomain = eeSFL_GetExtensionURL();

$eeSFL_ThisEmail = $eeSFL_Notify;
if(!$eeSFL_ThisEmail) {
	$eeSFL_ThisEmail = get_option('admin_email');
}
if(!filter_var($eeSFL_ThisEmail, FILTER_VALIDATE_EMAIL)) {
	$eeSFL_ThisEmail = FALSE;
}

if(filter_var($eeSFL_ThisDomain, FILTER_VALIDATE_URL)) {

	// Build the query URL
	$eeOrderURL = $eeSFL_AddOnsURL . '?eeDomain=' . urlencode( $eeSFL_ThisDomain ) . '&'; // Add this domain name, with protocal
	$eeOrderURL .= 'eeExtension=ee-simple-file-list-folders&'; // Add the plugin extension slug
	
	$eeArg = '?eeDomain=' . urlencode( $eeSFL_ThisDomain ); // Feed this to the destination form
	
	if($eeSFL_ThisEmail) {
		$eeOrderURL .= 'eeEmail=' . urlencode($eeSFL_ThisEmail); // The notification email
	}
	
	$eeSFL_Button = '<a class="button" target="_blank" href="' . $eeOrderURL . '">' . __('Add Folder Support Now', 'ee-simple-file-list') . '</a>';
	
	
	
}

// The Content
$eeOutput .= '<article class="eeSupp eeExtensions">

	<h2>' . __('Add Folder Support', 'ee-simple-file-list') . '</h2>
	
	<p>' . __('Add an extension that allows folder listing, navigation and management capabilities.', 'ee-simple-file-list') . '</p>
	
	<ul>
		<li>' . __('Create folders and unlimited levels of sub-folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Use a shortcode attribute to display specific folders.', 'ee-simple-file-list') . '</li>
		<li>' . __('Display different folders in different places on your site.', 'ee-simple-file-list') . '<br />
			' . __('You can even show several different folders on the same page and within widgets.', 'ee-simple-file-list') . '<br />
			' . __('Front-side users cannot navigate above the folder you specify.', 'ee-simple-file-list') . '</li>
		<li>' . __('Breadcrumb navigation indicates where you are.', 'ee-simple-file-list') . '</li>
		<li>' . __('Easily rename any folder.', 'ee-simple-file-list') . '</li>
		<li>' . __('Easily delete any folder, along with all contents.', 'ee-simple-file-list') . '</li>
		<li>' . __('Optionally display folder sizes.', 'ee-simple-file-list') . '</li>
		<li>' . __('Updating to newer versions is just like other Wordpress plugins.', 'ee-simple-file-list') . '</li>
	</ul>
	
	<p><a class="button" target="_blank" href="https://simplefilelist.com/add-folder-support/' . $eeArg . '">' . __('See Demo', 'ee-simple-file-list') . '</a> ' . $eeSFL_Button . '</p>
	
	</article>';
	
	
	$eeSFL_Log[] = '$eeOrderURL ...';
	$eeSFL_Log[] = urldecode($eeOrderURL);
	
	
	
function eeSFL_GetExtensionURL() {

	$secure = FALSE;
	
	if(isset($_SERVER['HTTPS'])) {
	    
	    if ($_SERVER['HTTPS'] == "on") {
	        $secure = TRUE;
	    }
	}
	
	if($secure) { $eeProtocol = 'https://'; } else { $eeProtocol = 'http://'; }
	
	$thisUrl = $eeProtocol . $_SERVER['HTTP_HOST'];
	 
	return $thisUrl;

}	

?>