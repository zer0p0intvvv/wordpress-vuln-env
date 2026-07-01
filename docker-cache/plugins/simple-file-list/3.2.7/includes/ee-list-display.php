<?php // Simple File List - ee-list-display.php - mitchellbennis@gmail.com
	
// List files in the path defined within $eeSFL_UploadDir
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'ee_include_page' ) ) exit('That is Noncense!'); // Exit if nonce fails

// $eeSFL_Log[] = 'Loaded: ee-list-display';
// $eeSFL_Log[] = 'Listing File in: ' . $eeSFL_UploadDir;

// EXTENSION CHECK
if($eeSFLF) {
	if(!@$eeSFLF_ListFolder) { // If not already set up
		$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
		include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_PathSetup.php');
	}
}
if($eeSFLS) {
	
}

// Ignore these
$eeSFL_Excluded = array('.', '..', "", basename($_SERVER['PHP_SELF']), '.php', '.htaccess', '.ftpquota', 'error_log', '.DS_Store', 'index.html');

$eeSFL_Files = array(); // This will be our file list
$eeSFL_FileCount = 0;

$eeSFL_ListClass = 'eeSFL'; // The basic list's CSS class. Extensions might change this.

// If Delete Files...
if(@$_POST['eeDeleteFile']) {

	foreach(@$_POST['eeDeleteFile'] as $eeSFL_Key => $eeSFL_File) {
		
		// Detect upward path traversal
		$realPath = realpath($eeSFL_UploadDir) . '/' . basename($eeSFL_File); // Defy traversal
		$userPath = realpath($eeSFL_UploadDir . $eeSFL_File);  // This could be problematic
		
		if ($userPath === false OR strpos($userPath, $realPath) !== 0) { // Must match
		    $eeSFL_Log['errors'] = 'Directory Traversal is Not Allowed';
		    break; // Bad guy found, bail out.
		}
		
		
		if(is_file($eeSFL_UploadDir . $eeSFL_File)) { // Gotta be a File
		
			if(unlink($eeSFL_UploadDir . $eeSFL_File)) {
				
				$eeSFL_Msg = __('Deleted the File', 'ee-simple-file-list') . ' &rarr; ' . $eeSFL_File;
				$eeSFL_Log[] = $eeSFL_Msg;
				$eeSFL_Log['messages'][] = $eeSFL_Msg;
				
				$eeSFL_Thumb = $eeSFL_UploadDir . '.thumbnails/thumb_' . $eeSFL_File;
				
				if(!is_file($eeSFL_Thumb)) { // Not found ?
					
					$eeExt = strrchr($eeSFL_File, '.'); // Get the extension
					$eeSFL_Thumb = str_replace($eeExt, '.png', $eeSFL_Thumb); // Change to PNG (video thumbs)
				}
				
				if(is_file($eeSFL_Thumb)) {
					
					if(!unlink($eeSFL_Thumb)) {
						$eeSFL_Msg = __('Thumbnail File Delete Failed', 'ee-simple-file-list') . ':' . $eeSFL_File;
						$eeSFL_Log[] = $eeSFL_Msg;
						$eeSFL_Log['errors'][] = $eeSFL_Msg;
					}
				}
				
				
			} else {
				$eeSFL_Msg = __('File Delete Failed', 'ee-simple-file-list') . ':' . $eeSFL_File;
				$eeSFL_Log[] = $eeSFL_Msg;
				$eeSFL_Log['messages'][] = $eeSFL_Msg;
			}
		}
	}
}

// If Renaming a File/Folder
if(@$_POST['eeNewFileName']) { 
	
	$eeOldFileName = filter_var($_POST['eeOldFileName'], FILTER_SANITIZE_STRING);
	$eeNewFileName = filter_var($_POST['eeNewFileName'], FILTER_SANITIZE_STRING);
	
	$eeNewFileName = eeSFL_SanitizeFileName($eeNewFileName);
	
	if($eeNewFileName) {
			
		$eeSFL_Log[] = 'Renaming: ' . $eeOldFileName . ' to ' . $eeNewFileName;
		
		if( !@rename($eeSFL_UploadDir . $eeOldFileName, $eeSFL_UploadDir . $eeNewFileName) ) {
			$eeSFL_Log['errors'][] = 'Could Not Rename ' . $eeOldFileName . ' to ' . $eeNewFileName;
		}
	}
	
}


// Extension Check
if($eeSFLF) { 
	
	// $eeSFL_Log[] = 'Loading Folder/File Listing Extension';
	
	// Create a new folder, if needed
	if(@$_POST['eeSFLF_NewFolderName']) { $eeSFLF->eeSFLF_CreateFolder($eeSFL_UploadDir); }
	
	$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
	
	// Run the File/Folder Listing and Sorting Engines
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSetup.php');	

} else {

	// $eeSFL_Log[] = 'Loading Default File Listing Engine';

	// Default File Listing and Sorting Engines
	$eeSFL_Files = $eeSFL->eeSFL_ListFiles($eeSFL_UploadDir, $eeSFL_Excluded);
	$eeSFL_Files = $eeSFL->eeSFL_SortFiles($eeSFL_UploadDir, $eeSFL_Files, $eeSFL_SortBy, $eeSFL_SortOrder);
}

// How Many?
$eeSFL_FileCount = count($eeSFL_Files);
$eeSFL_FileTotalCount = count($eeSFL_Files);

// Pagination Request Processing
if($eeSFLS) {
	$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
	include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-processing.php');
}

// User Messaging	
if(@$eeSFL_Log['messages']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['messages'], 'updated'); // Add to the output
	$eeSFL_Log['messages'] = array(); // Clear
}	
if(@$eeSFL_Log['errors']) { 
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error'); // Add to the output
	$eeSFL_Log['errors'] = array(); // Clear
}


// DISPLAY ===================================================

// List the files, all sorted and ready to go
	
// Our List Container
$eeOutput .= '

<!-- Simple File List -->';

if($eeSFLF) {
	if($eeSFL_Admin OR $eeSFL_ShowBreadCrumb == 'YES') {
		if($eeSFL_ListNumber == 1) {
			$eeOutput .= $eeSFLF_FunctionBar;
		}
	}
}

// Prepare a form so we can delete files in the Admin area
if($eeSFL_Admin OR ($eeSFL_AllowFrontDelete == 'YES' AND $eeSFL_ListNumber == 1 )) {

	$eeOutput .= '
	
	<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL_PluginSlug;	
	
	if($eeSFLF) {
		if($eeSFLF_ListFolder) {
			$eeOutput .= '&eeSFLF_ListFolder=' . urlencode($eeSFLF_ListFolder);
		}
	}
		
	$eeOutput .= '" method="post" id="eeSFL_FilesForm">';
			
}
if($eeSFL_Admin) {

		$eeOutput .= '<button class="eeDeleteCheckedButton button eeButton">' . __('Delete Checked', 'ee-simple-file-list') . '</button>
			<a href="#" class="button eeButton" id="uploadFilesButton">' . __('Upload Files', 'ee-simple-file-list') . '</a>
					<a href="?page=ee-simple-file-list&tab=list_settings" class="button eeButton">' . __('Settings', 'ee-simple-file-list') . '</a>';
}


// TABLE HEAD ==================================================================================================

if($eeSFL_Files) { 
	
	$eeSFL_RowID = 0; // Assign an ID number to each row
	
	$eeOutput .= '<table class="eeFiles">';
	
	if($eeSFL_ShowListHeader == 'YES' OR $eeSFL_Admin) { $eeOutput .= '<thead><tr>';
							
		if($eeSFL_ShowThumb == 'YES') { $eeOutput .= '<th class="eeSFL_Thumbnail">' . __('Thumb', 'ee-simple-file-list') . '</th>'; }
		
		$eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileName">&#x25B3; ' . __('Name', 'ee-simple-file-list') . ' &#x25BD;</th>';
									
		if($eeSFL_ShowFileSize == 'YES' OR $eeSFL_Admin) { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileSize">&#x25B3; ' . __('Size', 'ee-simple-file-list') . ' &#x25BD;</th>'; }
									
		if($eeSFL_ShowFileDate == 'YES' OR $eeSFL_Admin) { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileDate">&#x25B3; ' . __('Date', 'ee-simple-file-list') . ' &#x25BD;</th>'; }
									
		if($eeSFL_ShowFileOwner == 'YES') { $eeOutput .= '<th class="eeSFL_Sortable eeSFL_FileOwner">&#x25B3; ' . __('Owner', 'ee-simple-file-list') . ' &#x25BD;</th>'; }						
									
		if($eeSFL_Admin OR ($eeSFL_AllowFrontDelete == 'YES' AND $eeSFL_ListNumber == 1 )) { $eeOutput .= '<th><input type="checkbox" name="eeSFL_DeleteAll" value="YES" id="eeSFL_SelectAll"></th>'; }
		
		$eeOutput .= '</tr></thead>';
	}						
	
	$eeOutput .= '<tbody>'; // TABLE BODY == BEGIN FILE LIST =========================================================
						
	$eeSFL_FileCount = 0; // Reset
	
	$eeSFL_Log[] = 'Creating file list display...';
							
	// Loop through array
	foreach($eeSFL_Files as $eeSFL_Key => $eeSFL_File) {
		
		// if(!$eeListPosition) { $eeListPosition = $eeSFL_Key; } // Get the first key
		
		$eeSFL_IsFile = FALSE;
		$eeSFL_FileURL = FALSE;
		$eeSFL_IsFolder = FALSE;
		
		if($eeSFL_File) {
			
			if(strpos(basename($eeSFL_File), '.') !== 0) { // Don't display hidden items
			
				$eeSFL_FileCount++; // Bump the count
				
				if(is_file($eeSFL_UploadDir . $eeSFL_File)) {
					
					$eeSFL_FileURL = $eeSFL_UploadURL . $eeSFL_File; // Clickable URL
					
					$eeSFL_IsFile = TRUE;
				
				} elseif($eeSFLF) {
					
					$eeSFL_IsFolder = TRUE;
					if(!$eeSFL_Admin AND $eeSFL_ListNumber > 1) { continue; } // Disable folder support for additional lists
					$eeSFL_FileURL = $eeSFLF->eeSFLF_GetFolderURL($eeSFL_File, $eeSFLF_ShortcodeFolder); // Extension required
					
				} else {
					
					$eeSFL_File = FALSE;
				}
				
				$eeSFL_FileName = $eeSFL_File; // FileName is used for visible link, if we trim the owner info
				
				// Check for the actual file
				if($eeSFL_IsFile AND $eeSFL_Admin) { // Only check URLs if in Admin area (speed tweak)
					$eeSFL_FileURL = eeSFL_UrlExists($eeSFL_FileURL); // Sets to FALSE if file not found.
				}
				
				$eeOutput .= "\n\r" . '<tr id="eeSFL_RowID-' . $eeSFL_RowID . '">';
				
				
				
				// Thumbnail
				if($eeSFL_ShowThumb == 'YES') {
					
					$eeSFL_Thumb = $eeSFL->eeSFL_FileThumbnail($eeSFL_UploadDir, $eeSFL_UploadURL, $eeSFL_File); // Check for file thumbnail, create if not found.
					
					$eeOutput .= '<td class="eeSFL_Thumbnail">';
					
					if($eeSFL_Thumb) { $eeOutput .= '<a href="' . $eeSFL_FileURL .  '"';
						
						if(!$eeSFL_IsFolder) { $eeOutput .= ' target="_blank"'; }
							
						$eeOutput .= '><img src="' . $eeSFL_Thumb . '" width="64" height="64" /></a>'; }
					
					$eeOutput .= '</td>';
				}
				
				
				
				// NAME
				$eeOutput .= '<td class="eeSFL_FileName">';
				
				if($eeSFL_FileURL) {
					
					$eeOutput .= '<a class="eeSFL_FileName" href="' . $eeSFL_FileURL .  '"';
					if(!$eeSFL_IsFolder) {
						$eeOutput .= ' target="_blank"';
					}
					$eeOutput .= '>';
					
					// Extension Check
					if($eeSFLF) { // Show a small folder icon before the name if thumbs are not used.
						if($eeSFL_IsFolder AND $eeSFL_ShowThumb != 'YES') {
							$eeOutput .= '<b class="eeSFL_FolderIconSmall">' . $eeSFLF_FolderIcon . '</b> ';
						}	
					}
					
					$eeSFL_NoncedURL = wp_nonce_url($eeSFL_PluginURL . 'ee-download.php', 'eesfl-download-file-nonce');
					
					$eeSFL_FileNameBase = basename($eeSFL_FileName);
					
					$eeOutput .= $eeSFL_FileNameBase . '</a>';
					
					// OPEN / DOWNLOAD
					if($eeSFL_IsFile) { // File
						
						$eeSFL_FileActions = '<br /><small class="eeSFL_ListFileActions">
							<a href="' . $eeSFL_FileURL . '" target="_blank">' . __('Open', 'ee-simple-file-list') . '</a> | 
								<a href="' . $eeSFL_NoncedURL . '&eeSFL_File=' . urlencode($eeSFL_FileNameBase) . '&eeSFL_ID=' . $eeSFL_ID;
								
						if($eeSFLF) { if($eeSFLF_ListFolder) { $eeSFL_FileActions .= '&eeSFLF_Folder=' . $eeSFLF_ListFolder; }	}	
								
						$eeSFL_FileActions .= '" target="_blank">' . __('Download', 'ee-simple-file-list') . '</a>';
						
						if($eeSFL_ShowFileActions == 'YES' OR $eeSFL_Admin) {
							$eeOutput .= $eeSFL_FileActions;
						}
						
						if($eeSFL_Admin) { // Only Admins can rename
							
							$eeOutput .= ' | <a href="#" onclick="eeSFL_Rename(' . $eeSFL_RowID . ')">' . __('Rename', 'ee-simple-file-list') . '</a>';
						}
								
						$eeOutput .= '</small>';
						
					} else { // Folder
						
						if($eeSFL_Admin) { // Only Admins can rename
							
							$eeOutput .= '<br /><small class="eeSFL_ListFileActions">
								<a href="#" onclick="eeSFL_Rename(' . $eeSFL_RowID . ')">' . __('Rename', 'ee-simple-file-list') . '</a>
							</small>';
						}
						
					}
					
				} else { // ERROR
					
					$eeOutput .= '<span style="color:red;">! &rarr;</span>  ' . $eeSFL_File; // Mark as error, No link if not accessible
					
					$eeSFL_Log['errors'][] = __('File Not Found', 'ee-simple-file-list') . ': ' . $eeSFL_UploadDir . $eeSFL_File;
				}
				
				$eeOutput .= '</td>';
				
				
				
				
				
				// File Size
				if($eeSFL_ShowFileSize == 'YES' OR $eeSFL_Admin) {
				
					$eeOutput .= '<td class="eeSFL_FileSize">';
					
					if($eeSFL_IsFile) {
						$eeOutput .= eeSFL_GetFileSize($eeSFL_UploadDir . $eeSFL_File);
					} else {
						
						if($eeSFL_ShowFolderSize == 'YES') {
							$eeOutput .= $eeSFLF->eeSFLF_GetFolderSize($eeSFL_UploadDir . $eeSFL_File);
						} else {
							$eeOutput .= __('Folder', 'ee-simple-file-list');
						} 
					}
					
					$eeOutput .= '</td>';
				}
				
				
				
				
				
				// File Modification Date
				if($eeSFL_ShowFileDate == 'YES' OR $eeSFL_Admin) {
					$eeOutput .= '<td class="eeSFL_FileDate">';
					
					$eeModDate = date_i18n( get_option( 'date_format' ), @filemtime($eeSFL_UploadDir . $eeSFL_File) );
					
					if($eeModDate) {
						$eeOutput .= $eeModDate;
					}
					 
					$eeOutput .= '</td>';
				}
				
				
				
				
				
				// File Owner
				if($eeSFL_ShowFileOwner == 'YES') {
					
					if($eePOS2) { // If the file name had an owner
						$eePOS = strpos($eeSFL_File, '_Via-');
						$eeSFL_FileOwner = substr($eeSFL_File, ($eePOS+5));
						
						$eePOS = strpos($eeSFL_FileOwner, '.');
						$eeSFL_FileOwner = substr($eeSFL_FileOwner, 0, $eePOS);
					
					} else {
						$eeSFL_FileOwner = '';
					}
					
					$eePOS2 = '';
					$eePOS1 = '';
					$eePOS = '';
					
					$eeOutput .= '<td class="eeSFL_FileOwner">' .  $eeSFL_FileOwner . '</td>';
				}
				
				
				// Delete a File
				if($eeSFL_Admin OR ($eeSFL_AllowFrontDelete == 'YES' AND $eeSFL_ListNumber == 1 )) {
					
					$eeOutput .= '<td class="eeSFL_FileOps">';
					
					if($eeSFL_IsFile) { 
						$eeOutput .= '<input type="checkbox" class="eeDeleteFile" name="eeDeleteFile[]" value="' . $eeSFL_File . '" />';
					} elseif($eeSFLF) {
						$eeOutput .= '<a onclick="eeSFLF_ConfirmFolderDelete()" href="' . $_SERVER['PHP_SELF'] . '?page=' . 
							$eeSFL_PluginSlug . '&eeSFLF_ListFolder=' . urlencode($eeSFLF_ListFolder) . '&eeSFLF_DeleteFolder=' . $eeSFL_File . '" class="eeSFLF_DeleteFolderButton">X</a>';
					}
					$eeOutput .= '</td>';
				}
				
				$eeOutput .= "</tr>\n";
			
			}

		}
		
		$eeSFL_RowID++; // Bump the ID	
			
	} // END loop
	
	
	$eeSFL_Msg = __('Number of Items', 'ee-simple-file-list') . ': ' . $eeSFL_FileCount . ' | '  . __('Sorted by', 'ee-simple-file-list') . ' ' . $eeSFL_SortBy;

	$eeOutput .= '</tbody></table>
	
	<p class="eeSFL_Hide">
			<span id="eeSFL_FileCount">' . $eeSFL_FileCount . '</span>
		</p>'; // This allows javascript to access the info
				
	$eeSFL_Log[] = $eeSFL_Msg;
	
	if($eeSFL_Admin OR ($eeSFL_AllowFrontDelete == 'YES' AND $eeSFL_ListNumber == 1 )) { 
		$eeOutput .= '<input type="submit" class="eeDeleteCheckedButton button eeRight" value="' . __('Delete Checked', 'ee-simple-file-list') . '" />';
	}
	
	if($eeSFL_Admin) { $eeOutput .= '<p class="eeFileListInfo">' . $eeSFL_Msg . '</p>'; }

	if($eeSFL_Admin OR ($eeSFL_AllowFrontDelete == 'YES' AND $eeSFL_ListNumber == 1 )) { $eeOutput .= '</form>'; }
	
	
	// Pagination Controls
	if($eeSFLS) {
		$eeSFLS_Nonce = wp_create_nonce('eeSFLS_Include'); // Security
		include(WP_PLUGIN_DIR . '/ee-simple-file-list-search/includes/ee-pagination-display.php');
	}
 
} else {
	
	$eeSFL_Log[] = 'There are no files here.';
	$eeOutput .= '<h3>' . __('No Files', 'ee-simple-file-list') . '</h3>';
}


?>