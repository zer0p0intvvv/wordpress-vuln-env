<?php // Simple File List - ee-list-settings.php - mitchellbennis@gmail.com
	
	// tab=list_settings
	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! wp_verify_nonce( $eeSFL_Nonce, 'ee_include_page' ) ) exit('That is Noncense!'); // Exit if nonce fails

$eeSFL_Log[] = 'Loading List Settings Page ...';
	
// Check for POST and Nonce
if(@$_POST['eePost'] AND check_admin_referer( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce')) {
	
	if($_POST['eeShowList'] == 'YES') { $eeSFL_ShowList = 'YES'; } 
		elseif($_POST['eeShowList'] == 'USER') { $eeSFL_ShowList = 'USER'; } // Show only to logged in users
		 elseif($_POST['eeShowList'] == 'ADMIN') { $eeSFL_ShowList = 'ADMIN'; } // Show only to logged in Admins
			else { $eeSFL_ShowList = 'NO'; }
	
	update_option('eeSFL-' . $eeSFL_ID . '-ShowList', $eeSFL_ShowList);
	
	if($_POST['eeShowList'] != 'NO') { // If list is Off, these inputs won't be available
	
		if(@$_POST['eeShowFileThumb'] == 'YES') { $eeSFL_ShowFileThumb = 'YES'; } else { $eeSFL_ShowFileThumb = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-ShowFileThumb', $eeSFL_ShowFileThumb);
		
		if(@$_POST['eeShowFileDate'] == 'YES') { $eeSFL_ShowFileDate = 'YES'; } else { $eeSFL_ShowFileDate = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-ShowFileDate', $eeSFL_ShowFileDate);
		
		if(@$_POST['eeShowFileSize'] == 'YES') { $eeSFL_ShowFileSize = 'YES'; } else { $eeSFL_ShowFileSize = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-ShowFileSize', $eeSFL_ShowFileSize);
		
		if(@$_POST['eeAllowFrontDelete'] == 'YES') { $eeSFL_AllowFrontDelete = 'YES'; } else { $eeSFL_AllowFrontDelete = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-AllowFrontDelete', $eeSFL_AllowFrontDelete);
	
		if(@$_POST['eeSortBy']) {
			
			$eeSFL_SortBy = filter_var($_POST['eeSortBy'], FILTER_SANITIZE_STRING);
			update_option('eeSFL-' . $eeSFL_ID . '-SortBy', $eeSFL_SortBy);
		}
		
		if(@$_POST['eeSortOrder'] == 'Descending') { 
			$eeSFL_SortOrder = 'Descending';
		} else { 
			$eeSFL_SortOrder = 'Ascending';
		}
		update_option('eeSFL-' . $eeSFL_ID . '-SortOrder', $eeSFL_SortOrder);
		
		
		if(@$_POST['eeShowFileActions'] == 'YES') { $eeSFL_ShowFileActions = 'YES'; } else { $eeSFL_ShowFileActions = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-ShowFileActions', $eeSFL_ShowFileActions);
		
		if(@$_POST['eeShowListHeader'] == 'YES') { $eeSFL_ShowListHeader = 'YES'; } else { $eeSFL_ShowListHeader = 'NO'; }
		update_option('eeSFL-' . $eeSFL_ID . '-ShowListHeader', $eeSFL_ShowListHeader);
		
		
		if($eeSFLF) {
			if(!@$eeSFLF_ListFolder) { // If not already set up
				$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
				include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSettingsProcess.php');
			}
		}
	}
	
	$eeSFL_Confirm = __('List Settings Saved', 'ee-simple-file-list');
	
}

// Settings Display =========================================
	
$eeOutput .= '<div class="eeSFL_Admin">';
	
if(@$eeSFL_Log['errors']) { 
	
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Log['errors'], 'error');
	
} elseif(@$eeSFL_Confirm) { 
	
	$eeOutput .=  eeSFL_ResultsDisplay($eeSFL_Confirm, 'updated');
}

// Begin the Form	
$eeOutput .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=' . $eeSFL_Page . '&tab=list_settings&subtab=list_settings" method="post" id="eeSFL_Settings">
		<input type="hidden" name="eePost" value="TRUE" />';	
		
		$eeOutput .= wp_nonce_field( 'ee-simple-file-list-settings', 'ee-simple-file-list-settings-nonce' );
				
		$eeSFL_ShowList = get_option('eeSFL-' . $eeSFL_ID . '-ShowList');
		
		$eeOutput .= '<fieldset>
		
			<h2>' . __('File List Settings', 'ee-simple-file-list') . '</h2>
			
			<label for="eeShowList">' . __('File List Display', 'ee-simple-file-list') . '</label>
			
			<select name="eeShowList" id="eeShowList">
			
				<option value="YES"';

				if($eeSFL_ShowList == 'YES') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Show to Everyone', 'ee-simple-file-list') . '</option>
				
				<option value="USER"';

				if($eeSFL_ShowList == 'USER') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Show to Only Logged in Users', 'ee-simple-file-list') . '</option>
				
				<option value="ADMIN"';

				if($eeSFL_ShowList == 'ADMIN') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Show to Only Logged in Admins', 'ee-simple-file-list') . '</option>
				
				<option value="NO"';

				if($eeSFL_ShowList == 'NO') { $eeOutput .= ' selected'; }
				
				$eeOutput .= '>' . __('Hide the File List Completely', 'ee-simple-file-list') . '</option>
			
			</select>
			
			<br class="eeClearFix" />
			<div class="eeNote">' . __('You can use the uploader without showing the file list.', 'ee-simple-file-list') . '</div>';
				
			if($eeSFL_ShowList != 'NO') {
			
				$eeOutput .= '<h3>' . __('Columns to Show', 'ee-simple-file-list') . '</h3>
				
				<label class="eeNoClear" for="eeShowFileThumb">' . __('Show Thumbnail', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileThumb" value="YES" id="eeShowFileThumb"'; 
				if(get_option('eeSFL-' . $eeSFL_ID . '-ShowFileThumb') == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />
				
				<label class="eeNoClear" for="eeShowFileDate">' . __('Show File Date', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileDate" value="YES" id="eeShowFileDate"'; 
				if(get_option('eeSFL-' . $eeSFL_ID . '-ShowFileDate') == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' /> 
				
				<label class="eeNoClear" for="eeShowFileSize">' . __('Show File Size', 'ee-simple-file-list') . ':</label><input type="checkbox" name="eeShowFileSize" value="YES" id="eeShowFileSize"'; 
				if(get_option('eeSFL-' . $eeSFL_ID . '-ShowFileSize') == 'YES') { $eeOutput .= ' checked'; }
				$eeOutput .= ' />';
				
				$eeOutput .= '<div class="eeNote">' . __('Limit the columns of file details to display on the front-side file list.', 'ee-simple-file-list') . '</div>
					
				<h3>' . __('File Sorting and Order', 'ee-simple-file-list') . '</h3>	
				
				<label for="eeSortList">' . __('Sort By', 'ee-simple-file-list') . ':</label><select name="eeSortBy" id="eeSortList">
				
						<option value="Name"';
						
						$eeSFL_SortBy = get_option('eeSFL-' . $eeSFL_ID . '-SortBy');
						
						if($eeSFL_SortBy == 'Name') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('File Name', 'ee-simple-file-list') . '</option>
						<option value="Date"';
						
						if($eeSFL_SortBy == 'Date') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('File Date', 'ee-simple-file-list') . '</option>
						<option value="Size"';
						
						if($eeSFL_SortBy == 'Size') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('File Size', 'ee-simple-file-list') . '</option>
						<option value="Random"';
						
						if($eeSFL_SortBy == 'Random') { $eeOutput .=  'selected'; }
						
						$eeOutput .= '>' . __('Random', 'ee-simple-file-list') . '</option>
					</select> 
				<div class="eeNote">' . __('Sort the list by name, date, file size, or randomly.', 'ee-simple-file-list') . '</div>
					
				<br class="eeClearFix" />
					
				<label for="eeSortOrder">' . __('Reverse Order', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeSortOrder" value="Descending" id="eeSortOrder"';
				
				if(get_option('eeSFL-' . $eeSFL_ID . '-SortOrder') == 'Descending') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>&darr; ' . __('Descending', 'ee-simple-file-list') . '</p>
				
				<div class="eeNote">' . __('Check this box to reverse the default sort order.', 'ee-simple-file-list') . '<br />
					' . __('The list is sorted Ascending by default', 'ee-simple-file-list') . ': A to Z, ' . __('Small to Large', 'ee-simple-file-list') . ', ' . __('Old to New', 'ee-simple-file-list') . '</div>
					
				<br class=eeClearFix />
					
				<h3>' . __('File List Display', 'ee-simple-file-list') . '</h3>	
				
				
				<label for="eeShowListHeader">' . __('Show Header', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeShowListHeader" value="YES" id="eeShowListHeader"';
				
				if(get_option('eeSFL-' . $eeSFL_ID . '-ShowListHeader') == 'YES') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' />
				
				<div class="eeNote">' . __('Show file list\'s table header or not.', 'ee-simple-file-list') . '</div>
				
				<br class=eeClearFix />
				
				
				<label for="eeShowFileActions">' . __('Show File Actions', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeShowFileActions" value="YES" id="eeShowFileActions"';
				
				if(get_option('eeSFL-' . $eeSFL_ID . '-ShowFileActions') == 'YES') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>' . __('Open | Download', 'ee-simple-file-list') . '</p>
				
				<div class="eeNote">' . __('Show file action links below each file name on the front-side list', 'ee-simple-file-list') . '</div>
				
				<br class=eeClearFix />
				
				
				<label for="eeAllowFrontDelete">' . __('Allow Front Delete', 'ee-simple-file-list') . ':</label>
				<input type="checkbox" name="eeAllowFrontDelete" value="YES" id="eeAllowFrontDelete"';
				
				if(get_option('eeSFL-' . $eeSFL_ID . '-AllowFrontDelete') == 'YES') { $eeOutput .= ' checked="checked"'; }
				
				$eeOutput .= ' /> <p>' . __('Use with Caution', 'ee-simple-file-list') . '</p>
								
				<div class="eeNote">' . __('Allows file deletion on the front-side of the website', 'ee-simple-file-list') . '</div>
				
				<br class=eeClearFix />';
				
				if($eeSFLF) {
					
					$eeSFLF_Nonce = wp_create_nonce('eeSFLF_Include'); // Security
					include_once(WP_PLUGIN_DIR . '/ee-simple-file-list-folders/includes/eeSFLF_ListSettings.php');
				}
					
			}
			
		$eeOutput .= '<input type="submit" name="submit" id="submit2" value="' . __('SAVE', 'ee-simple-file-list') . '" class="eeAlignRight" />
		
		</fieldset>
		
	</form>
	
</div>';
	
?>