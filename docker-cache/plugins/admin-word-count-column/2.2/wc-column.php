<?php
/*
Plugin Name: Admin Word Count Column
Plugin URI: brooks.tent.is
Description: Adds a word count view to the posts list
Version: 2.2
Author: Brooks
Author URI: hhttp://brooks.tent.is
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

$cpwcmsg = '<p style="background-color:#66ccff;border:2px;">hello world.</p>';

/*
**Settings
*/
//$cpwcflatsettings = get_option('cp_wc_plugin_options');
function cp_wc_explode_settings($field) {
	$firstexpl = explode(PHP_EOL, $field);
	$cpwcexsettings = array();
	foreach($firstexpl as $colonsv) {
		$cpwcexsettings[] = explode(':', $colonsv);
	}
	return $cpwcexsettings;
}
//$rules = cp_wc_explode_settings($cpwcflatsettings['text_string']);
$cp_wc_post_types = array('post');

//post meta helper
foreach($cp_wc_post_types as $type) {
	add_action('quick_edit_custom_box', 'cp_wc_qe_box', 10, 2);
	add_action('add_meta_boxes', 'cp_wc_register_meta_box');
	function cp_wc_register_meta_box() {
		add_meta_box('cp-wc-meta-box', 'Target Word Count', 'cp_wc_meta_box', $type, 'side', 'high');
	}
	function cp_wc_meta_box() {
		$cpwcmetaval = get_post_meta(get_the_id(), 'wc_target', true);
		echo 'At least: <input type="number" style="width: 60px" min="0" max="100000" step="10" name="cpwc" value="' . $cpwcmetaval . '" /> words.
		<br />Posts that fall short will appear with a red word count in the post list.';
//echo '<input type="number" style="width: 50px" "padding-right: -20px" min="0" max="1000" step="10" width="4" name="cbox[cra]" value="70" size="25" />';
	}
	function cp_wc_qe_box($column_name,$type) {
		//if($column_name == 'cpwordcount'):

		the_id();
		$cpwcqemetaval = get_post_meta(get_the_id(), 'wc_target', true);
		echo '
		<fieldset class="inline-edit-col-left">
		<div class="inline-edit-col">
		Target Word Count: <input type="number" min="0" max="100000" step="10" name="cpwc" value="' . $cpwcqemetaval . '" />
		</div>
		</fieldset>
		';
		//endif;
	}
	add_action('post_updated', 'cp_wc_save_field');
	add_action('edit_post', 'cp_wc_save_field');
	add_action('save_post', 'cp_wc_save_field');
	add_action('publish_post', 'cp_wc_save_field');
	add_action('wp_publish_post', 'cp_wc_save_field');
	add_action('wp_insert_post', 'cp_wc_save_field');//this one seems to do it.  i don't know why none of the other hooks are called
	function cp_wc_save_field($post_id) {
	if(isset($_POST['cpwc'])):
		$cpwc_got_meta = (int)$_POST['cpwc'];
		//print_r($oma_got_meta);
	endif;
	if(isset($cpwc_got_meta)):
		if($cpwc_got_meta>0):
			update_post_meta($post_id, 'wc_target', $cpwc_got_meta);
		else:
			delete_post_meta($post_id, 'wc_target');
		endif;
	endif;//isset $cpwc_got_meta
	}//cp_wc_save_field
}//foreach $cp_wc_post_types as $type



//register column
add_action('admin_head', 'cpwc_column_style');
function cpwc_column_style() {
	echo '
	<style>
	.column-cpwordcount { width: 60px; }		
        </style>
     ';
}
foreach($cp_wc_post_types as $type) {
	if($type == 'post'):
		add_filter('manage_posts_columns', 'cp_wc_column_filter');
		add_action('manage_posts_custom_column', 'cp_wc_column_action', 10, 2);
	else:
	//	add_filter('manage_' . $type . '_columns','cp_wc_column_filter');
	//	add_action('manage_' . $type . 'posts_custom_column','cp_wc_column_action');
	endif;
}


//register submenus
function cp_wc_dl_submenu() {
	add_posts_page('Download Word Count Spreadsheet','Word Count Spreadsheet','edit_others_posts','wordcount-csv','cp_wc_csv_dl_page');
}
add_action('admin_menu','cp_wc_dl_submenu');



//define column
function cp_wc_column_filter($label) {
	$label['cpwordcount'] = 'Words';
    return $label;
}
function cp_wc_column_action($column_name, $current_post_id) {
	if($column_name == 'cpwordcount'):
		$cpwcpost = new CPWordCount($current_post_id,true);
		$thetarget = $cpwcpost->target_count();
		echo $cpwcpost->word_count(true,$thetarget);
		//unset($cpwcpost);
	endif;//column label is cpwordcount
}

//define submenu
function cp_wc_csv_dl_page() {
	echo '<h1>Download Word Count CSV</h1>
	<br />';
	if(isset($_GET['category'])):
		echo '<h2>Filtered by category</h2>
		<br />';
	endif;
	if(isset($_GET['date'])):
		echo '<h2>Filtered by date range</h2>
		<br />';
	endif;
	//unlink('cpwc.csv');
	$cpwcdlurl = plugin_dir_url('/admin-word-count-column/wc-column.php');
	$cpwcdir = WP_PLUGIN_DIR . plugin_dir_path('/admin-word-count-column/wc-column.php');
	//$cpcsv = fopen($cpwcdir . 'cpwc.csv', 'w+');
	$cpcsv = false;
	$cpwcargs = array('posts_per_page'=>-1,'orderby'=>'post_date','order'=>'DESC');
	$cpwccsvheader = array('Date','Author','Title','Words','Target','Completion');
	//header
	if($cpcsv):
		fputcsv($cpcsv, $cpwccsvheader);
	else:
		$cpwcstring = '"Date","Author","Title","Words","Target","Completion"';
	endif;
	query_posts( $cpwcargs );
    while ( have_posts() ) : the_post();//running through query_posts
    	$awcpost = new CPWordCount(get_the_id());
    	$awctarget = $awcpost->target_count();
    	$awcwords = $awcpost->word_count();
    	$acwpercent = $awcpost->target_completion(false,$awcwords,$awctarget);
    	//$thetarget = $cpwcpost->target_count();
    	if($cpcsv):
			fputcsv($cpcsv, $awcpost->csv_row($awcwords,$awctarget,$acwpercent));
		else:
			foreach($awcpost->csv_row($awcwords,$awctarget,$acwpercent) as $num=>$cpwc) {
				if($num>0):
					$cpwcstring .= ',';
				endif;
				$cpwcstring .= '&quot;' . $cpwc . '&quot;';
			}
			$cpwcstring .= PHP_EOL;
		endif;
    endwhile;
    echo '<p>SPREADSHEET UPDATED.</p>';
    if($cpcsv):
		fclose($cpcsv);
		echo '<a href="' . plugins_url('admin-word-count-column/download-csv.php?path=' . urlencode($cpwcdlurl)) . '">Download aforementioned CSV file</a>';
	else:
		echo '<form action="' . plugins_url('admin-word-count-column/alt-csv.php') . '" method="post" target="_blank">';
		echo '<input type="hidden" value="' . $cpwcstring . '" name="csv" />';
		echo '<input type="submit" value="Download Spreadsheet" />';
		echo '</form>';
	endif;
	
	
	
		$cpwctalk = wp_remote_retrieve_response_code(wp_remote_head('http://brooksnewberry.com/talk.php'));
		if(date('y')==12&&date('z')<345&&$cpwctalk==200):
	echo '<p style="padding:3px;font-size:11pt;width:400px;background-color:#d9eeff;border-style:outset;border-width:4px;">Hi there. I whipped up this tool way back in 2009 because my classroom had a very specific WordPress need that no one had yet filled. When I noticed there are still users today, I decided to breathe some new life into it. <em>If there is a <b>classroom-friendly feature</b> you think WordPress should have, <a href="http://wordpress.org/support/plugin/admin-word-count-column">make a post in the support forum here.</a></em> I\'ll keep an eye on that forum for a few weeks (through Nov 2012) as I release fixes for this plugin.<br />&mdash;b</p>';
	endif;
	
}

class CPWordCount {//this object represents the word count of a single post.
	public static $id;
	public static $words;
	public static $target = false;
	function __construct($postid, $inloop=true) {
//		$this::$id = $postid;
//		if(!$inloop):			
//		endif;//in loop
	}
	
	function csv_row($words,$target,$percent) {//formatted for putcsv()
		$row = array(get_the_date('M j, Y'),get_the_author(),get_the_title(),$words,$target,$percent);
		return $row;
	}
	function word_count($style = false, $target) {
		global $more;
		$more = 1;
		$cpwccontent = apply_filters('the_content', get_the_content());
		$fltrcpwccontent = str_replace(']]>', ']]&gt;', $cpwccontent);
		//echo $content;
		//echo strip_tags(get_the_content('HOWDY'));
		//echo strip_tags($fltrcpwccontent);
		$words = str_word_count(strip_tags(str_replace(array('’','-',' - ','\'',''),'',$fltrcpwccontent)));
		//echo $this::$words;
	if($style && $target>$words):
//			echo $this::$target;
//			echo $this::$id . ' a ';
			$percent = $this->target_completion(true,$words,$target);
			return '<span style="color:rgb(255,' . $percent . ',' . $percent . ');">' . $words . '</span>';
		else:
			return $words;
		endif;
	}
	function target_count() {
		$target = false;
		$metafield = get_post_meta(get_the_id(),'wc_target',true);
		if($metafield != '' && $metafield != null):
			$target = (int)$metafield;
			if($target>0):
				return (int)$metafield;
			endif;
		else:
			$cpwcflatsettings = get_option('cp_wc_plugin_options');
			$rules = cp_wc_explode_settings($cpwcflatsettings['text_string']);
			//print_r($rules);
			$tags = get_the_tags();
			$cats = get_the_category();
			foreach($rules as $rule) {
				if($rule[0] == 'tag' && $tags !=null):
					foreach($tags as $tag) {
					$tname = $tag->name;
						if($rule[1] == $tname):
							$target = $rule[2];
							return (int)$rule[2];
						endif;//matching tag
					}//tags
				elseif($rule[0] == 'category' && $cats != null):
					foreach($cats as $cat) {
						if($rule[1] == $cat->name):
							$target = $rule[2];
							return (int)$rule[2];
						endif;
					}
				endif;
			}//foreach rules
			return $target;
		endif;
	}
	function target_completion($int = false,$words,$target) {
		if($target):
			$complete = (int)((($words/$target)*100)+0.50);
		endif;
		if($int):
			return $complete;
		elseif($target):
			return $complete . '%';
		else:
			return $complete;
		endif;
	}
}
//register some settings

function register_cpwcsettings() {
	register_setting( 'cp_wc_plugin_options', 'cp_wc_plugin_options', 'cp_wc_plugin_options_validate' );
	add_settings_section('cp_wc_plugin_main', 'Rules List', 'cp_wc_plugin_section_text', 'cp_wc_plugin');
	add_settings_field('cp_wc_plugin_text_string', 'Plugin Text Input', 'cp_wc_plugin_setting_string', 'cp_wc_plugin', 'cp_wc_plugin_main');
}
function cp_wc_plugin_section_text() {
	echo '<p>Type rules into the textbox, then save your settings. Each rule needs its own line. If two rules apply to one post, the rule listed here first will take precedence.
	<br />
	For example:
	<br />
	tag:weather:300<br />
	category:news:900<br />
	<br />
	Also remember, any post with the custom meta field "wc_target" and a number value, like 300, will use that number as a target.
	</p>';
}
function cp_wc_plugin_setting_string() {
	$cpwcoptions = get_option('cp_wc_plugin_options');
	echo '<textarea id="cp_wc_plugin_text_string" name="cp_wc_plugin_options[text_string] rows="16" cols="40">' . $cpwcoptions['text_string'] . '</textarea>';
}
function cp_wc_plugin_options_validate($input) {
	return $input;
}
function cp_wc_settings_menu() {
	add_options_page('Admin Word Count Column Settings', 'Word Count Settings', 'manage_options', 'cpwcsettings', 'cp_wc_settings_page');
}
if(is_admin()):
	add_action( 'admin_menu', 'cp_wc_settings_menu' );
	add_action( 'admin_init', 'register_cpwcsettings' );
endif;

//define settings page
function cp_wc_settings_page() {
	echo '<div class="wrap">
	<h2>Admin Word Count Column Settings</h2>';
	
	
	echo '<form method="post" action="options.php">
	';
	settings_fields('cp_wc_plugin_options');
	do_settings_sections('cp_wc_plugin');
	submit_button();
	echo '</form>';
		$cpwctalk = wp_remote_retrieve_response_code(wp_remote_head('http://brooksnewberry.com/talk.php'));
		if(date('y')==12&&date('z')<345&&$cpwctalk==200):
	echo '<p style="padding:3px;font-size:11pt;width:400px;background-color:#d9eeff;border-style:outset;border-width:4px;">Hi there. I whipped up this tool way back in 2009 because my classroom had a very specific WordPress need that no one had yet filled. When I noticed there are still users today, I decided to breathe some new life into it. <em>If there is a <b>classroom-friendly feature</b> you think WordPress should have, <a href="http://wordpress.org/support/plugin/admin-word-count-column">make a post in the support forum here.</a></em> I\'ll keep an eye on that forum for a few weeks (through Nov 2012) as I release fixes for this plugin.<br />&mdash;b</p>';
	endif;
	
	echo '
	</div>
	';
}

?>