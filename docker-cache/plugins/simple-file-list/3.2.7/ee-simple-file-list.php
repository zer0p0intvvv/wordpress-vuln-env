<?php

/**
 * @package Element Engage - Simple File List
 */
/*
Plugin Name: Simple File List
Plugin URI: http://simplefilelist.com
Description: Full Featured File List with Front-Side File Uploading | <a href="https://simplefilelist.com/donations/simple-file-list-project/">Donate</a> | <a href="admin.php?page=ee-simple-file-list&tab=folders">Add Folder Support</a>
Author: Mitchell Bennis - Element Engage, LLC
Version: 3.2.7
Author URI: http://elementengage.com
License: GPLv2 or later
Text Domain: ee-simple-file-list
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// SFL Version
$eeSFL_Version = '3.2.7';

// Our Core
$eeSFL_Config = array(); // We store DB return values here
$eeSFL = FALSE; // Our main class
$eeSFL_ListID = 1; // Normally "1" for first install.
$eeSFL_ListNumber = 1; // Used for multiple list displays per page
$eeSFL_Admin = FALSE; // Are we in the admin area or not?
$eeSFL_DevMode = FALSE; // No visible log is displayed

// The Log
$eeSFL_Log = array('Simple File List is Loading...');
// Format: [] => 'log entry'
//	['messages'][] = 'Message to the user'
//	['errors'][] = 'Error condition'

if(strpos($_SERVER['HTTP_HOST'], 'elementengage.net')) { 
	$eeSFL_DevMode = TRUE; // Enables visible logging
}

/*  Log Files Written...
   /wp-content/plugins/simple-file-list/logs/Simple-File-List-Log.txt (256kb max)
   /wp-content/plugins/simple-file-list/logs/ee-email-error.log
   /wp-content/plugins/simple-file-list/logs/ee-upload-error.log

*/

// Available Extensions
$eeSFL_AddOnsURL = 'https://get.simplefilelist.com/index.php'; // SFL

$eeSFL_Extensions = array( // Slugs
	'ee-simple-file-list-folders' // Folder Support
	,'ee-simple-file-list-search' // Search & Pagination
	,'ee-simple-file-list-users' // File List User Manager
);

// Initialize Class Variables
$eeSFLF = FALSE; $eeSFLS = FALSE; $eeSFLU = FALSE;


// Plugin Setup
function eeSFL_Setup() {
	
	global $eeSFL, $eeSFL_Extensions, $eeSFL_Log, $eeSFL_Config, $eeSFL_ListID;
	
	// $eeSFL_Log[] = 'Running eeSFL_Setup...';

	// Get functions files
	$eeSFL_Nonce = wp_create_nonce('eeSFL_Functions'); // Security
	include_once(plugin_dir_path(__FILE__) . 'includes/ee-functions.php'); // General Functions

	$eeSFL_Nonce = wp_create_nonce('eeSFL_File_Functions'); // Security
	include_once(plugin_dir_path(__FILE__) . 'includes/ee-file-functions.php'); // File System Operations
	$eeSFL_Nonce = wp_create_nonce('ee_include_page'); // Security
	
	// Let's have some class...
	if(!class_exists('eeSFL')) {
		require_once(plugin_dir_path(__FILE__) . 'includes/ee-class.php'); // Get the main class file
		$eeSFL = new eeSFL_MainClass(); // Initiate the SFL Class
		$eeSFL_Config = $eeSFL->eeSFL_Config($eeSFL_ListID); // Get the Configuration Array	
	}
	
	// Extension Checks ------------------------
	
	// A required resource...
	if(!function_exists('is_plugin_active')) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	}
	
	// Get the default folder locations
	$eeSFL_UploadDir = $eeSFL_Config['eeSFL_UploadDir'];
	$eeSFL_UploadURL = $eeSFL_Config['eeSFL_UploadURL'];
	
	// $eeSFL_Log[] = $eeSFL_UploadDir;
	// $eeSFL_Log[] = $eeSFL_UploadURL;
	
	$eeSFL_Log[] = 'Checking for Extensions...';
	
	// Loop thru and set up
	foreach($eeSFL_Extensions as $key => $eeSFL_Extension) {
	
		if(is_plugin_active( $eeSFL_Extension . '/' . $eeSFL_Extension . '.php' )) { // Is the plugin active?
	
			$eeSFL_Log['extensions'] = $eeSFL_Extension;
			
			// Legacy
			$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security (Legacy)
			
			$eeNonce = wp_create_nonce('eeNonce');
			
			$eeExtension = wp_create_nonce('eeExtension'); // Security
			include_once(WP_PLUGIN_DIR . '/' . $eeSFL_Extension . '/ee-ini.php'); // Run initialization
		}
	}
	
	// --------------
	
	return TRUE;
}

add_action('init', 'eeSFL_Setup');


// Fixes folder names not renamed properly by extension updater
function eeSFL_FixExtensionFolderNames() {
	
	global $eeSFL, $eeSFL_Extensions, $eeSFL_Log;
	
	// Scan Folders in the Plugins Dir
	$wpPlugins = scandir( WP_PLUGIN_DIR );
	
	foreach($eeSFL_Extensions as $eeExtension){
		
		$eeExtensionCorrect = WP_PLUGIN_DIR . '/' . $eeExtension;
		
		foreach( $wpPlugins as $wpPlugin ) {
			
			$eePos = strpos($wpPlugin, $eeExtension); // Check if our slug in contained
			
			if($eePos) { // If contained, but not if begins with
	
				$eeExtensionWrong = WP_PLUGIN_DIR . '/' . $wpPlugin; // This is our messed-up plugin folder
				
				$eeSFL_Log['issues'][] = 'WRONG: ' . $eeExtensionWrong;
				
				// Rename this folder
				if( @rename ( $eeExtensionWrong, $eeExtensionCorrect ) ) { // Fix the folder name
					
					$eeSFL_Log['issues'][] = 'FIXED ' . $eeExtensionCorrect;
					
				} else {
					
					$eeSFL_Log['issues'][] = 'Rename Failure :-(';
				}
				
				$eeSFL->eeSFL_WriteLogFile($eeSFL_Log);
				
				continue; // Skip to next extension					
			}
		}
	}
}
add_action( 'admin_init', 'eeSFL_FixExtensionFolderNames' );



// Plugin Update Check
function eeSFL_VersionCheck() {
		
	global $eeSFL_Log, $eeSFL_Version;
	
	$eeSFL_VersionInstalled = get_option('eeSFL-Version');
	
	$eeSFL_Log[] = 'Plugin Version: ' . $eeSFL_VersionInstalled;
	
	if($eeSFL_VersionInstalled != $eeSFL_Version) {
		
		eeSFL_UpdateThisPlugin(); // Run the DB update process
		$eeSFL_Log[] = 'UPGRADING THE PLUGIN: ' . $eeSFL_VersionInstalled . ' to ' . $eeSFL_Version;

	}
}
add_action('plugins_loaded', 'eeSFL_VersionCheck');




// Language Enabler
function eeSFL_Textdomain() {
    load_plugin_textdomain( 'ee-simple-file-list', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'eeSFL_Textdomain' );



// Createing a New Post with Shortcode
function eeSFL_CreatePostwithShortcode() { 
	
	global $eeSFL_Log;
	
	$eeShortcode = FALSE;
	$eeCreatePostType = FALSE;
	$eeCreatePostType = filter_var(@$_POST['eeCreatePostType'], FILTER_SANITIZE_STRING);
	$eeShortcode = filter_var(@$_POST['eeShortcode'], FILTER_SANITIZE_STRING);
		
	if(($eeCreatePostType == "Post" OR $eeCreatePostType == "Page") AND $eeShortcode) {
		
		// Create Post Object
		$eeNewPost = array(
			'post_type'		=> $eeCreatePostType,
			'post_title'    => 'My Simple File List ' . $eeCreatePostType,
			'post_content'  => '<p><em>Note that this ' . $eeCreatePostType . ' is in draft status</em></p><div>' . $eeShortcode . '</div>',
			'post_status'   => 'draft'
		);
 
		// Create Post
		$eeNewPostID = wp_insert_post( $eeNewPost );
		
		if($eeNewPostID) {
			
			$eeSFL_Log['p=' . $eeNewPostID][] = 'Creating new ' . $eeCreatePostType . ' with shortcode...';
			$eeSFL_Log['p=' . $eeNewPostID][] = $eeShortcode;
			
			header('Location: /?p=' . $eeNewPostID);
		}
		
		return TRUE;
	}
	
}
add_action( 'wp_loaded', 'eeSFL_CreatePostwithShortcode' );

// Page Output ============================================


// Shortcode
function eeSFL_Shortcode($atts, $content = null) {
	
	// Usage: [eeSFL]
    
    global $eeSFL, $eeSFL_DevMode, $eeSFL_Log, $eeSFL_Config, $eeSFL_ListNumber;
    global $eeSFLF, $eeSFLS; // Extensions
    
    $eeSFL_Admin = FALSE; // We are on the front side of the site
	
	$eeSFL_Log['L' . $eeSFL_ListNumber][] = 'Shortcode Loading: ' . get_permalink();
    
    // Run the Configuration Class
	$eeSFL_Nonce = wp_create_nonce('ee_include_page'); // Checked on the included pages

	// Loop through and build variable names from the associative array
	if(@is_array($eeSFL_Config)) {
		foreach($eeSFL_Config as $eeKey => $eeValue) {
			
			${$eeKey} = $eeValue;
			
			if(is_array($eeValue)) {
				$eeValue = implode(' = ', $eeValue);
			}
		}
	} else {
		// Error 'No SFL Configuration';
		return FALSE;
	}

    // Over-Riding Shortcode Attributes
	if($atts) {
	
		$atts = shortcode_atts( array( // Use lowercase att names only
			'showlist' => $eeSFL_ShowList, // defaults to DB settings
			'allowuploads' => $eeSFL_AllowUploads,
			'showthumb' => $eeSFL_ShowThumb,
			'showdate' => $eeSFL_ShowFileDate,
			'showsize' => $eeSFL_ShowFileSize,
			'showheader' => $eeSFL_ShowListHeader,
			'showactions' => $eeSFL_ShowFileActions,
			'showfolder' => '',
			'id' => ''
		), $atts );
		
		extract($atts);
	
		$eeSFL_Log['L' . $eeSFL_ListNumber][] = 'Shortcode Attributes...';
		
		$eeSFL_ShowList = $showlist;
		$eeSFL_AllowUploads = $allowuploads;
		$eeSFL_ShowThumb = $showthumb;
		$eeSFL_ShowFileDate = $showdate;
		$eeSFL_ShowFileSize = $showsize;
		$eeSFL_ShowListHeader = $showheader;
		$eeSFL_ShowFileActions = $showactions;
		$eeSFLF_ShortcodeFolder = $showfolder;
		
		if($eeSFL_ShowList != $showlist) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showlist: ' . $showlist; }
		if($eeSFL_AllowUploads != $allowuploads) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'allowuploads: ' . $allowuploads; }
		if($eeSFL_ShowThumb != $showthumb) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showthumb: ' . $showthumb; }
		if($eeSFL_ShowFileDate != $showdate) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showdate: ' . $showdate; }
		if($eeSFL_ShowFileSize != $showsize) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showsize: ' . $showsize; }
		if($eeSFL_ShowListHeader != $showheader) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showheader: ' . $showheader; }
		if($eeSFL_ShowFileActions != $showactions) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showactions: ' . $showactions; }
		if($showfolder) { $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'showfolder: ' . $showfolder; }
		
	
	} else {
		$eeSFL_Log['L' . $eeSFL_ListNumber][] = 'No Shortcode Attributes';
	}
	
	
	// Begin Front-Side List Display ==================================================================
	
	$eeOutput = '<div id="eeSFL">';
	// $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'Begin Frontside Output Buffer...';
	
	// Who Can Upload?
	switch ($eeSFL_AllowUploads) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_AllowUploads = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_AllowUploads = 'NO'; }
	        break;
		default:
			$eeSFL_AllowUploads = 'NO'; // Show Nothing
	}
	
	if($eeSFL_AllowUploads != 'NO' AND $eeSFL_ListNumber == 1) {
		include($eeSFL_PluginPath . 'includes/ee-uploader.php');
	}
	
	// Who Can View the List?
	switch ($eeSFL_ShowList) {
	    case 'YES':
	        break; // Show It
	    case 'USER':
	        // Show It If...
	        if( get_current_user_id() ) { break; } else { $eeSFL_ShowList = 'NO'; }
	    case 'ADMIN':
	        // Show It If...
	        if(current_user_can('manage_options')) { break; } else { $eeSFL_ShowList = 'NO'; }
	        break;
		default:
			$eeSFL_ShowList = 'NO'; // Show Nothing
	}
	if($eeSFL_ShowList != 'NO') {
		
		include($eeSFL_PluginPath . 'includes/ee-list-display.php');
	}
	
	// $eeSFL_Log['L' . $eeSFL_ListNumber][] = 'Outputting the page...';
	
	$eeOutput .= '</div>';
	
	$eeSFL_ListNumber++;
	
	if(@$_REQUEST) {
		array_unshift($eeSFL_Log, $_REQUEST);
	}
	
	// Logging
	if($eeSFL_DevMode OR @$_REQUEST) { 
		$eeSFL->eeSFL_WriteLogFile($eeSFL_Log);
		$eeOutput .= '<pre id="eeSFL_DevMode">Log File ' . print_r($eeSFL_Log, TRUE) . '</pre>';
	} 
	
	return $eeOutput; // Output the page
}

add_shortcode( 'eeSFL', 'eeSFL_Shortcode' );


// HTML =====================

// Load Front-side <head>
function eeSFL_Enqueue() {
	
	// Register the style like this for a theme:
    wp_register_style( 'ee-simple-file-list-css', plugin_dir_url(__FILE__) . 'css/eeStyles.css');
 
    // Enqueue the style:
    wp_enqueue_style('ee-simple-file-list-css');
	
	// Now with Javascript !
	$deps = array('jquery');
	wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/eeJavacripts-head.js',$deps,'30',FALSE); // Head
	wp_enqueue_script('ee-simple-file-list-js-foot', plugin_dir_url(__FILE__) . 'js/eeJavacripts-footer.js',$deps,'30',TRUE); // Footer
}
add_action( 'wp_enqueue_scripts', 'eeSFL_Enqueue' );



// Admin <head>
function eeSFL_AdminHead($eeHook) {
        
        // wp_die($eeHook); // Use this to discover the hook for each page
        
        // https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
        
        $eeHooks = array(
        	'toplevel_page_ee-simple-file-list',
        	'simple-file-list_page_ee-simple-file-list',
        	'simple-file-list_page_ee-simple-file-list-settings'
        );
        
        if(in_array($eeHook, $eeHooks)) {
            wp_enqueue_style( 'ee-simple-file-list-css-front', plugins_url('css/eeStyles.css', __FILE__) );
            wp_enqueue_style( 'ee-simple-file-list-css-back', plugins_url('css/eeStyles-Back.css', __FILE__) );
            
            // Now with Javascript !
            wp_enqueue_script('ee-simple-file-list-js-footer', plugin_dir_url(__FILE__) . 'js/eeJavacripts-footer.js');
            wp_enqueue_script('ee-simple-file-list-js-head', plugin_dir_url(__FILE__) . 'js/eeJavacripts-head.js');
			wp_enqueue_script('ee-simple-file-list-js-back', plugin_dir_url(__FILE__) . 'js/eeJavacripts-back.js');
        }
}
add_action('admin_enqueue_scripts', 'eeSFL_AdminHead');



// Add Action Links to the Plugins Page
function add_action_links( $links ) {
	
	$eeLinks = array(
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list' ) . '">' . __('File List', 'ee-simple-file-list') . '</a>',
		'<a href="' . admin_url( 'admin.php?page=ee-simple-file-list&tab=list_settings&subtab=uploader_settings' ) . '">' . __('Settings', 'ee-simple-file-list') . '</a>'
	);
	return array_merge( $links, $eeLinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );




// === ADMIN PAGES ========================================================

function eeSFL_AdminMenu() {
	
	global $eeSFL, $eeSFLF, $eeSFL_Log, $eeSFL_DevMode, $eeSFL_Admin, $eeSFL_Config, $eeSFL_Version, $eeSFL_Excluded;
	global $eeSFLF, $eeSFLU, $eeSFLS; // Extensions
	
	$eeOutput = '<!-- Simple File List Admin -->';
	$eeSFL_Log[] = 'Admin Menu Loading ...';
	
	$eeSFL_Nonce = wp_create_nonce('ee_include_page'); // Security
	
	include_once(plugin_dir_path(__FILE__) . 'includes/ee-admin-page.php'); // Admin's List Management Page
	
	// The Admin Menu
	add_menu_page(
		__('Simple File List', 'ee-simple-file-list'), // Page Title
		__('Simple File List', 'ee-simple-file-list'), // Menu Title
		'edit_posts', // User status reguired to see the menu
		'ee-simple-file-list', // Slug
		'eeSFL_ManageLists', // Function that displays the menu page
		'dashicons-index-card' // Icon used
	);
	
	if($eeSFLU) { 
		
		$eeNonce = wp_create_nonce('eeSFLU'); // Security
		include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-users/includes/eeManager.php');
		
		add_submenu_page(
		'ee-simple-file-list', 
		__('User Manager', 'ee-simple-file-list-users'), 
		__('User Manager', 'ee-simple-file-list-users'),  
		'edit_users', 
		'ee-simple-file-list-users', 
		'eeSFLU_Manager'
		);
	}
	
}
add_action( 'admin_menu', 'eeSFL_AdminMenu' );




// Check for the Upload Folder, Create if Needed
function eeSFL_UploadDirCheck($eeSFL_UploadDir) {
	
	global $eeSFL_Log;
	
	$eeSFL_Log[] = 'Checking Folder...';
	// $eeSFL_Log[] = $eeSFL_UploadDir;
	
	if(strlen($eeSFL_UploadDir)) {
		
		if(!@is_writable($eeSFL_UploadDir)) {
			
			$eeSFL_Log[] = 'No Directory Found.';
			$eeSFL_Log[] = 'Creating Upload Directory ...';
			
			// Environment Detection
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			    $eeSFL_Log[] = 'Windows detected.';
			    mkdir($eeSFL_UploadDir); // Windows
			} else {
			    $eeSFL_Log[] = 'Linux detected.';
			    if(!mkdir($eeSFL_UploadDir, 0755)) { // Linux - Need to set permissions
				    $eeSFL_Log['errors'][] = 'Cannot Create: ' . $eeSFL_UploadDir;
				}
			}
			
			if(!@is_writable($eeSFL_UploadDir)) {
				$eeSFL_Log['errors'][] = 'ERROR: I could not create the upload directory: ' . $eeSFL_UploadDir;
				
				return FALSE;
			
			} else {
				
				$eeSFL_Log[] = 'Looks Good';
			}
		} else {
			$eeSFL_Log[] = 'Looks Good';
		}
		
		// Check index.html, create if needed.
				
		$eeFile = $eeSFL_UploadDir . 'index.html'; // Disallow direct file indexing.
		
		if($handle = @fopen($eeFile, "a+")) {
			
			if(!@is_readable($eeFile)) {
			    
				$eeSFL_Log['errors'][] = 'ERROR: Could not write index.html';
				
				return FALSE;
				
			} else {
				
				fclose($handle);
				
				// $eeSFL_Log[] = 'index.html is in place.';
			}
		}
		
	} else {
		$eeSFL_Log['errors'] = 'No upload directory defined';
				
		return FALSE;
	}
	
	return TRUE;
	
}


// Plugin Upgrade =====================

function eeSFL_UpdateThisPlugin() {
	
	global $wpdb, $eeSFL_Log, $eeSFL_ListID, $eeSFL_Version;
		
	$eeSFL_V2 = FALSE; // We will check if this is a new install or update
	
	// DEFAULT SETTINGS
	
	// File List
	$eeSFL_ShowList = 'YES'; // Show the File List ?
	$eeSFL_ShowFileThumb = 'NO'; // Show Thumbnail Column
	$eeSFL_ShowFileDate = 'YES'; // Show Date Column
	$eeSFL_ShowFileSize = 'YES'; // Show Size Column
	$eeSFL_ShowFileActions = 'YES'; // Show the Open | Download links - NEW in 3.2
	$eeSFL_SortBy = 'Name'; // Sort the List by Name, Date or Size, Owner
	$eeSFL_SortOrder = 'Normal'; // Sort the List Normal or in Reverse (Ascending or Descending)
	$eeSFL_ShowListHeader = 'YES'; // Show the table header <th> 
	$eeSFL_AllowFrontDelete = 'NO'; // Allow Front-side file deletion
	
	// Uploading
	$eeSFL_AllowUploads = 'YES'; // Allow File Uploads on Front Side ?
	$wp_UploadDirArray = wp_upload_dir(); // The Wordpress Upload Location
	$wp_UploadDir = $wp_UploadDirArray['basedir']; // Get the full directory path
	$wp_UploadDir  = str_replace(ABSPATH, '/', $wp_UploadDir) . '/'; // Make relative to WP root for saving
	$eeSFL_UploadDir = $wp_UploadDir . 'simple-file-list/'; // The default upload location, relative to WP home dir
	eeSFL_UploadDirCheck(ABSPATH . $eeSFL_UploadDir); // Check/Create the Upload Folder
	
	// File Limitations
	$eeSFL_FileFormats = 'gif, jpg, jpeg, png, tif, pdf, wav, wmv, wma, avi, mov, mp4, m4v, mp3, zip'; // Acceptable File Formats
	$eeSFL_UploadLimit = 10; // Maximum files allowed per submission
	$eeSFL_UploadMaxFileSize = substr(ini_get('upload_max_filesize'), 0, -1); // in MB, the largest file this server allows to be uploaded.
	$eeSFL_GetUploaderInfo = 'NO'; // Display a form where people enter information
	$eeSFL_Notify = get_option('admin_email'); // Send a message here each time a file is uploaded --> TO DO - Or if there are errors
	
	// Upgrade Simple File List ?
	
	$eeSFL_V2 = get_option('eeSFL'); // Check for Pre SFL 3 installations
	
	if(get_option('eeSFL-1-ShowList')) { // Simple File List 3.X is Installed
		
		// New in this version !!!
		
		// Thumbnails
		$eeSFL_ShowFileThumb = get_option('eeSFL-' . $eeSFL_ListID . '-ShowFileThumb');
		
		if($eeSFL_ShowFileThumb != 'YES') {
			$eeSFL_ShowFileThumb = 'NO';
		} else {
			$eeSFL_ShowFileThumb = 'YES';
		}
		
		// Changed to prepare for address-per-list
		$eeNotifyOld = get_option('eeSFL-Notify'); // Old way, no ID
		$eeNotifyNew = get_option('eeSFL-' . $eeSFL_ListID . '-Notify'); // New way, with ID
		if($eeNotifyOld) {
			$eeSFL_Notify = $eeNotifyOld;
			delete_option('eeSFL-Notify'); // Out with the old.
		} elseif($eeNotifyNew) {
			$eeSFL_Notify = $eeNotifyNew; // In with the new.
		}
		
		delete_option('eeSFL-Legacy'); // Don't need this anymore
		
		// Get existing 3.0 settings
		$eeSFL_AllowUploads = get_option('eeSFL-1-AllowUploads');
		$eeSFL_FileFormats = get_option('eeSFL-1-FileFormats');
		$eeSFL_GetUploaderInfo = get_option('eeSFL-1-GetUploaderInfo');
		$eeSFL_ShowFileDate = get_option('eeSFL-1-ShowFileDate');
		$eeSFL_ShowFileOwner = get_option('eeSFL-1-ShowFileOwner');
		$eeSFL_ShowFileSize = get_option('eeSFL-1-ShowFileSize');
		$eeSFL_ShowList = get_option('eeSFL-1-ShowList');
		$eeSFL_SortBy = get_option('eeSFL-1-SortBy');
		$eeSFL_SortOrder = get_option('eeSFL-1-SortOrder');
		$eeSFL_TrackFileOwner = get_option('eeSFL-1-TrackFileOwner');
		$eeSFL_UploadDir = get_option('eeSFL-1-UploadDir');
		$eeSFL_UploadLimit = get_option('eeSFL-1-UploadLimit');
		$eeSFL_UploadMaxFileSize = get_option('eeSFL-1-UploadMaxFileSize');
		
		
	} elseif($eeSFL_V2) { // Simple File List 1 or 2 is Installed
			
		// SFL Version 1
		// eeAllowList=Yes|eeAllowUploads=Yes|ee_upload_max_filesize=64|eeFormats=jpg,jpeg,png,pdf,zip|eeAdminTo=name@email.com
		
		// SFL Version 2 added...
		// eeFileOwner=No|eeUploadDir=wp-content/uploads/simple-file-list|eeSortList=Name|eeSortOrder=|eeShowForm=Yes
		
		// Get the existing settings, so we can convert them.
		$eeSettings = explode('|', $eeSFL_V2);
		
		// Version 1 settings
		$eeSetting = @explode('=', $eeSettings[0]); // Show the File List
		if($eeSetting[1] != 'Yes') { $eeSFL_ShowList = 'NO'; }
		
		$eeSetting = @explode('=', $eeSettings[1]); // AllowUploads
		if($eeSetting[1] != 'Yes') { $eeSFL_AllowUploads = 'NO'; }
			else { $eeSFL_AllowUploads = 'YES'; }
		
		$eeSetting = @explode('=', $eeSettings[2]); // Upload Max File size
		if($eeSetting[1]) { $eeSFL_UploadMaxFileSize = $eeSetting[1]; } else { $eeSFL_UploadMaxFileSize = 8; }
		
		$eeSetting = @explode('=', $eeSettings[3]); // Formats
		if($eeSetting[1]) { $eeSFL_FileFormats = $eeSetting[1]; }
		
		$eeSetting = @explode('=', $eeSettings[4]); // TO Email
		if($eeSetting[1]) { $eeSFL_Notify = $eeSetting[1]; }
		
		
		if(count($eeSettings) > 5) { // Version 2 Additions
			
			$eeSetting = @explode('=', $eeSettings[5]); // Track File Owner
			if(@$eeSetting[1] != 'Yes') { $eeSFL_TrackFileOwner = 'NO'; }
			
			$eeSetting = @explode('=', $eeSettings[6]); // Upload Dir
			if(@$eeSetting[1]) { $eeSFL_UploadDir = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[7]); // Sort List By...
			if(@$eeSetting[1]) { $eeSFL_SortBy = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[8]); // Sort order
			if(@$eeSetting[1]) { $eeSFL_SortOrder = $eeSetting[1]; }
			
			$eeSetting = @explode('=', $eeSettings[9]); // Show Uploader Info Form
			if(@$eeSetting[1] != 'Yes') { $eeSFL_GetUploaderInfo = 'NO'; }
	
		}	
	}
			
	// Check if UploadDir has a trailing slash...
	$eeLastChar = substr($eeSFL_UploadDir, -1);
	if($eeLastChar != '/') {  $eeSFL_UploadDir .= '/'; } // Add the slash, required for 3.1 +
	
	// Update the DB
	update_option('eeSFL-Version', $eeSFL_Version);
	update_option('eeSFL-' . $eeSFL_ListID . '-ShowList', $eeSFL_ShowList);
	update_option('eeSFL-' . $eeSFL_ListID . '-ShowFileThumb', $eeSFL_ShowFileThumb); // NEW in 3.1
	update_option('eeSFL-' . $eeSFL_ListID . '-ShowFileDate', $eeSFL_ShowFileDate);
	update_option('eeSFL-' . $eeSFL_ListID . '-ShowFileSize', $eeSFL_ShowFileSize);
	update_option('eeSFL-' . $eeSFL_ListID . '-ShowFileActions', $eeSFL_ShowFileActions); // NEW in 3.2
	update_option('eeSFL-' . $eeSFL_ListID . '-SortBy', $eeSFL_SortBy);
	update_option('eeSFL-' . $eeSFL_ListID . '-SortOrder', $eeSFL_SortOrder);
	update_option('eeSFL-' . $eeSFL_ListID . '-AllowUploads', $eeSFL_AllowUploads);
	update_option('eeSFL-' . $eeSFL_ListID . '-UploadDir', $eeSFL_UploadDir);
	update_option('eeSFL-' . $eeSFL_ListID . '-GetUploaderInfo', $eeSFL_GetUploaderInfo);
	update_option('eeSFL-' . $eeSFL_ListID . '-UploadLimit', $eeSFL_UploadLimit);
	update_option('eeSFL-' . $eeSFL_ListID . '-UploadMaxFileSize', $eeSFL_UploadMaxFileSize);
	update_option('eeSFL-' . $eeSFL_ListID . '-FileFormats', $eeSFL_FileFormats);
	update_option('eeSFL-' . $eeSFL_ListID . '-Notify', $eeSFL_Notify);
	update_option('eeSFL-' . $eeSFL_ListID . '-AllowFrontDelete', $eeSFL_AllowFrontDelete); // NEW in 3.2
	update_option('eeSFL-' . $eeSFL_ListID . '-ShowListHeader', $eeSFL_ShowListHeader); // NEW in 3.2
	
	
	$eeSFL_Log[] = 'Plugin Updated to ' . $eeSFL_Version;
	
	
}



// Plugin Activation ==========================================================

function eeSFL_Activate() {
	
	return TRUE; // All done, nothing to do here.	
}
register_activation_hook( __FILE__, 'eeSFL_Activate' );

?>