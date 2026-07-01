<?php
/*
Plugin Name: Gwyn's Imagemap Selector
Plugin URI: http://gwynethllewelyn.net/gwyns-imagemap-selector/
Version: 0.3.3
License: Simplified BSD License
Author: Gwyneth Llewelyn
Author URI: http://gwynethllewelyn.net/
Description: Uses shortcodes to display imagemaps with categories of posts. 
Loosely based on http://wordpress.org/support/topic/loop-through-shortcode-attribute-array?replies=4
Imagemap creator code uses Adam Maschek's imgmap library http://code.google.com/p/imgmap/
Integration with the WordPress Media Library popup as described by http://www.webmaster-source.com/2010/01/08/using-the-wordpress-uploader-in-your-plugin-or-theme/
Some code fixes by Tom Rusko

Copyright 2011, 2012, 2013 Gwyneth Llewelyn. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
	  conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
	  of conditions and the following disclaimer in the documentation and/or other materials
	  provided with the distribution.

THIS SOFTWARE IS PROVIDED BY GWYNETH LLEWELYN ``AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL GWYNETH LLEWELYN OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of Gwyneth Llewelyn.

*/

// Return the directory where the plugin is installed (with trailing slash)
function get_plugin_dir()
{
	return WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
} // end get_plugin_dir()

/*
Settings API implemented as suggested by this tutorial: http://www.chipbennett.net/2011/02/17/incorporating-the-settings-api-in-wordpress-themes/
With further tricks to fix some details at http://www.presscoders.com/wordpress-settings-api-explained/
*/

// Setting up the panels
function gwyns_imagemap_selector_get_settings_page_tabs() {
	 $tabs = array(
		  'imagemapcreator'	=> __('Imagemap Creator', 'gwyns-imagemap-selector'),
		  'instructions'	=> __('Instructions', 'gwyns-imagemap-selector'),
		  'settings'		=> __('Settings', 'gwyns-imagemap-selector')
	 );
	 return $tabs;
} // end gwyns_imagemap_selector_get_settings_page_tabs()

// Set up styling for page tabs
function gwyns_imagemap_selector_admin_options_page_tabs( $current = 'imagemapcreator' ) {
	 if ( isset ( $_GET['tab'] ) ) :
		  $current = $_GET['tab'];
	 else:
		  $current = 'imagemapcreator';
	 endif;
	 $tabs = gwyns_imagemap_selector_get_settings_page_tabs();
	 $links = array();
	 foreach( $tabs as $tab => $name ) :
		  if ( $tab == $current ) :
			   $links[] = "<a class='nav-tab nav-tab-active' href='?page=gwyns_imagemap_selector&amp;tab=$tab'>$name</a>";
		  else :
			   $links[] = "<a class='nav-tab' href='?page=gwyns_imagemap_selector&amp;tab=$tab'>$name</a>";
		  endif;
	 endforeach;
	 echo '<div id="icon-themes" class="icon32"><br /></div>';
	 echo '<h2 class="nav-tab-wrapper">';
	 foreach ( $links as $link )
		  echo $link;
	 echo '</h2>';
} // end gwyns_imagemap_selector_admin_options_page_tabs()

// Helper function for default options
function gwyns_imagemap_selector_get_default_options() {
	 $options = array(
			'debug_mode'	=> false,
			'popup_url'	=> get_plugin_dir() . 'popup.php',
			'popup_css'	=> '
#gwyns_popup {
	float: left;
	color: gray;
	width: 400px;
	height: 160px;
	background: url(\'' . get_plugin_dir() . 'popup-background.png\');
	opacity: 0.8;
	filter: alpha(opacity=80);
	display: none;
}
.popup {
	font-size: 11px;
	line-height: 12px;
	font-weight: normal;
	margin: 2px 6px 6px 2px;
	padding: 0 0 0 0;
}
.popup h2 {
	font-size: 12px;
	line-height: 14px;
	font-weight: bold;
}
.popup-thumbnail {
	float: left;
}
.popup-content {
	float: right;
	width: 225px;
}
',
			'version'		=> '0.3.3'
	 );
	 return $options;
} // end gwyns_imagemap_selector_get_default_options()

// Add a menu option on the admin panel for this plugin
function gwyns_imagemap_selector_admin_menu_options()
{
	// Deal with translations. Portuguese only for now.
	load_plugin_textdomain('gwyns-imagemap-selector', false, dirname( plugin_basename( __FILE__ ) ));

	$myOptionsPage = add_options_page(__('Gwyn\'s Imagemap Selector', 'gwyns-imagemap-selector'), __('Gwyn\'s Imagemap Selector', 'gwyns-imagemap-selector'), 'publish_pages',
		'gwyns_imagemap_selector', 'gwyns_imagemap_selector_menu');
	// Enqueue Admin Stylesheet at admin_print_styles()
	add_action("admin_print_styles-$myOptionsPage", 'gwyns_imagemap_selector_enqueue_admin_styles');
	// Enqueue Admin Scripts at admin_print_scripts
	add_action("admin_print_scripts-$myOptionsPage", 'gwyns_imagemap_selector_enqueue_admin_scripts');
} // end gwyns_imagemap_selector_admin_menu_options()

// Admin panel uses Settings API
//
function gwyns_imagemap_selector_menu()
{
?>
<div class="wrap" style="direction:ltr;">

<?php gwyns_imagemap_selector_admin_options_page_tabs(); ?>
<?php $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'imagemapcreator' ); ?>

<h2><?php _e('Gwyn\'s Imagemap Selector', 'gwyns-imagemap-selector'); ?></h2>

<?php 
	// Check on which tab we are, and call the appropriate page-form

	if ($tab == 'settings') {
		if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
?>
<form method="post" action="options.php">
<?php
	 settings_fields('gwyns_imagemap_selector_settings');
	 do_settings_sections('gwyns_imagemap_selector');
?>
<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'gwyns-imagemap-selector'); ?>" />
</form>

<?php 
	} 
	elseif ($tab == 'imagemapcreator') {
		settings_fields('gwyns_imagemap_selector_settings-imagemapcreator');
		do_settings_sections('gwyns_imagemap_selector-imagemapcreator');
	}
	elseif ($tab == 'instructions') {
		settings_fields('gwyns_imagemap_selector_settings-instructions');
		do_settings_sections('gwyns_imagemap_selector-instructions');
	}
	else {
?>
<h3><?php _e('Error', 'gwyns-imagemap-selector'); ?></h3>

<?php _e('This page should have never been displayed!', 'gwyns-imagemap-selector'); ?>

<?php
	}
} // end gwyns_imagemap_selector_menu()
	

// Imagemap shortcodes will be:
// [imagemap {category="1"|direct="true"} img="/.../img/gwyn.gif" map="my-map" id="my-map"]
// [area shape="rect" url="linkurl"]1,2,3,4[/area]
// [area shape="rect" url="linkurl"]5,6,7,8[/area]
// [/imagemap]


// Deals with shortcodes. For now, we just have [imagemap type="category"]
function gwyns_imagemap_selector_shortcode_imagemap($atts, $content)
{
	extract( shortcode_atts( array(
  		'category' => '',		// What category to use; use ids; multiples are fine
  		'category_name' => '',	// What category to use; use names
  		'tag' => '',			// What tag to use; use ids
  		'tag_name' => '',		// What tag to use; use names
  		'query' => '',			// Full query to WP_QUERY
  		'direct' => false,		// "true" to interpret URLs as final links
  		'img' => '',			// URL to image to be image-mapped
  		'title' => '',			// Shows on image titile
  		'order' => 'DESC',		// for category and tag, what order to return posts
  		'nopaging' => false,	// To override the limit set on the Reading Settings
  		'map' => 'gwyn-imagemap',
  		'id'  => 'gwyn-imagemap',
  		'class' => 'gwyn-image',
  		'alt' => 'Imagemap alt',
  		'popup' => false,
  		'thumbnail' => false,
  		'excerpt' => false
  		), $atts ) );

	unset($GLOBALS['area']); // start with a clean slate
	$GLOBALS['area_count'] = 0;

	$gwyns_imagemap_selector_settings = get_option('gwyns_imagemap_selector_settings');	// debug mode

	$return = "<!-- " . __('This imagemap inserted by ', 'gwyns-imagemap-selector') . __('Gwyn\'s Imagemap Selector', 'gwyns-imagemap-selector') . " http://gwynethllewelyn.net/gwyns-imagemap-selector/ -->\n";
	
	if ($gwyns_imagemap_selector_settings['debug_mode']) $return .= "<!-- \tCategory: '$category' Tag: '$tag' Query: '$query' img: '$img' Order: '$order' -->\n";
	
	// Now go and parse the [area]...[/area] tag
	do_shortcode( $content );

	// $GLOBALS['area'] has been set by the "area" shortcode and includes all the
	//	 area info — e.g. coords, URL to link to (if direct mode is set), etc.

	if( is_array( $GLOBALS['area'] ) ) {
		// output begin of imagemap
		$return .= "<img src='$img' usemap='#$map' class='$class' " . ($title ? "title='$title' " : "")
				. ($alt ? "alt='$alt' " : "")
				. "id='img-$id' />\n" .
			"<map id='$id' name='$map'>\n";
	
		// if direct is false, start listing posts in the order received with permalinks to them
		// if direct is true, just use the URLs defined in the "[area]" shortcode 
	
		if ( !$direct ) {
			if ($gwyns_imagemap_selector_settings['debug_mode']) $return .= "<!-- Dump area\n" . print_r($GLOBALS['area'], true) . "\n"
					. "Category: '$category' Tag: '$tag' Query: '$query' Order: '$order' -->\n";
		
			// Create a new instance for a query
			//  category and/or tag and/or full WP_QUERY query, not all
			
			if ($category) {
				if (is_numeric($category) || strpos($category, ",") === true) $query = "cat=$category";
				else $query = "category_name=$category";
			}
			else if ($category_name) {
				$query = "category_name=$category_name";
			}
			else if ($tag) {
				if (is_numeric($tag) || strpos($tag, ",") === true) $query = "tag_id=$tag";
				else $query = "tag=$tag";
			}
			else if ($tag_name) {
				$query = "tag_name=$tag_name";
			}
			else if (!$query) {
				$return .= "<!-- ". __('No valid category, tag, or generic query', 'gwyns-imagemap-selector') . " -->\n</map>\n";
				return $return;
			}
			$order = strtoupper($order);
			if ($order != 'DESC')
				$query .= "&order=$order";

			if ($nopaging && strtoupper($nopaging) != 'FALSE')
				$query .= "&nopaging=true";
				
			// WordPress encodes & as &amp; which will fail on the query
			//  This attempts to revert the encoding
			if (function_exists("htmlspecialchars_decode")) // will only work on PHP > 5.1.0
				$query = str_replace(array("&lt;", "&gt;", '&amp;', '&#039;', '&quot;','&lt;', '&gt;', '&#038;'), array("<", ">",'&','\'','"','<','>', '&'), htmlspecialchars_decode($query, ENT_NOQUOTES));
				
			if ($gwyns_imagemap_selector_settings['debug_mode']) $return .= "<!-- Query is '$query' -->\n";
			
			$second_query = new WP_Query( $query ); // make our query


			$area_count = 0;
			$area = $GLOBALS['area'];

			if ($popup)
			{
			?>
			<script type="text/javascript">
			/* Inspired by code copyrighted by Sinfic
			/*********************AJAX*********************/
			function getXmlHttpObject () {
				   var xmlHttp = null;
				   try { // Firefox, Opera 8.0+, Safari
					   try {
						   netscape.security.PrivilegeManager.enablePrivilege ( "UniversalBrowserRead" );
					   } catch ( e ) {
					   }
					   xmlHttp=new XMLHttpRequest ();
				   } catch ( e ) { // Internet Explorer
					   try {
						   xmlHttp = new ActiveXObject ( "Msxml2.XMLHTTP" );
					   } catch ( e ) {
						   xmlHttp = new ActiveXObject ( "Microsoft.XMLHTTP" );
					   }
				   }
				   return xmlHttp;
			}
			
			function ajax ( url_function, div_id ) {
				   xmlHttp = getXmlHttpObject ();
				   if ( xmlHttp == null ) {
					   return;
				   }
				   if ( xmlHttp.overrideMimeType ) {
					   xmlHttp.overrideMimeType ( 'text/xml' );
				   }
				   try {
					   xmlHttp.open ( "GET", url_function, true );
				   } catch ( e ) {
				   }
				   xmlHttp.onreadystatechange = function stateChanged () {
													if ( xmlHttp.readyState == 4 ) {
														document.getElementById ( div_id ).innerHTML = xmlHttp.responseText;
													}
												};
				   xmlHttp.send ( null );
			}
			/*********************DIV POPUP*********************/			
			function popup_exit ( div_id ) {
				var element = document.getElementById ( div_id );
				element.style.display = 'none';
			}
			
			function popup_show ( div_id, img_id, ajax_url_function, evt ) {
				   if ( !evt ) evt = window.event;
				   var element = document.getElementById ( div_id );
				   if ( ajax_url_function ) {
					   ajax ( ajax_url_function, div_id );
				   } else {
					   element.innerHTML = 'vazio';
				   }
				   element.style.position = "absolute";
				   element.style.display  = "block";
				   element.style.left = evt.clientX + 20 + 'px';
				   element.style.top  = evt.clientY + 20 + 'px';
			}
			</script>
			<style type="text/css" media="screen">
			<?php
				// inline CSS for the popup
				echo $gwyns_imagemap_selector_settings['popup_css'];
			?>
			</style>
			<div id="gwyns_popup"> </div> 
			<?php
			
				$plugin_url = get_plugin_dir();
			}

			// The Loop
			while( $second_query->have_posts() ) : $second_query->the_post();
				$myCSSclass = ($area['class'] ? $area['class'] : $id . "-area");
				$return .= "\t" .'<area shape="' . $area[$area_count]['shape']
					. '" coords="' . $area[$area_count]['coords']
					. '" href="' . get_permalink()
					. '" alt="' . ($area['alt'] ? $area['alt'] : strip_tags(get_the_title()))
					. '" title="' . ($area['title'] ? $area['title'] : strip_tags(get_the_title())) . "'"
					. $area['target'] ? " target='" . $area['target'] . "'" : ""
					. ' class="' . $myCSSclass
					. '" id="' . $id . "-area-" . $area_count;
				if ($popup)
				{
					// handle popup code

					$return .= "\" onmouseover=\"popup_show('gwyns_popup', 'img-$id', '" . ($gwyns_imagemap_selector_settings['popup_url'] ? $gwyns_imagemap_selector_settings['popup_url'] : ($plugin_url . "popup.php")) . "?id=" . get_the_ID() . "&amp;class=$myCSSclass" . ($thumbnail ? "&amp;thumbnail=true" : "") . ($thumbnail ? "&amp;excerpt=true" : "") . "', event);\" onmouseout=\"popup_exit('gwyns_popup');";
				}
				$return .= '" />' . "\n";
				$area_count++;
			endwhile;

			wp_reset_postdata();	// better be safe than sorry...		
		}
		else {
			$area_count = 0;
		
			// just cycle through $GLOBALS['area'] and use the URL set there instead
			foreach( $GLOBALS['area'] as $area ) {
				$return .= "\t" . '<area shape="' . $area['shape']
					. '" coords="' . $area['coords']
					. '" href="' . ($area['url'] ? $area['url'] : "#")
					. '" alt="' . ($area['alt'] ? $area['alt'] : $id . "-" . $area_count)
					. '" title="' . ($area['title'] ? $area['title'] : $id . "-" . $area_count) 
					. ($area['target'] ? '" target="' . $area['target'] .'"' : '')
					. ' class="' . ($area['class'] ? $area['class'] : $id . "-area")
					. '" id="' . $id . "-area-" . $area_count
					. '" />' ."\n";
				$area_count++;
			}
		}
	
		$return .= "</map>\n";
	}
	else {
		$return = "<!-- " . __('No imagemap data found', 'gwyns-imagemap-selector') . " -->\n";
	}
	return $return;
} // end gwyns_imagemap_selector_shortcode_imagemap()

// item has a title
function gwyns_imagemap_selector_shortcode_area($atts, $content)
{
	extract(shortcode_atts(array(
		'shape' => 'rect',
		'url' => '',
		'alt' => '',
		'class' => '',
		'target' => '_self',
		'title' => ''
	), $atts));	

	$x = $GLOBALS['area_count'];
	$GLOBALS['area'][$x] = array( 'shape' => $shape, 'url' => $url, 'alt' => $alt, 'class' => $class, 'title' => $title, 'target' => $target, 'coords' =>  $content );

	$GLOBALS['area_count']++;
} // end gwyns_imagemap_selector_shortcode_area()


// Settings API to display options
// Add a settings group, which hopefully makes it easier to delete later on
//	This is where imagemaps will be stored in the distant future
// Note that we have three different pages, so each gets its own set
function gwyns_imagemap_selector_register_settings()
{
	global $gwyns_imagemap_selector_settings;
	
	register_setting('gwyns_imagemap_selector_settings', 'gwyns_imagemap_selector_settings', 'gwyns_imagemap_selector_validate');
	
	// Main
	add_settings_section( 'gwyns_imagemap_selector_main_section', __('Options', 'gwyns-imagemap-selector'), 'gwyns_imagemap_selector_main_section_text', 'gwyns_imagemap_selector');
	add_settings_field( 'debug_mode', __('Debug mode', 'gwyns-imagemap-selector'), 'gwyns_imagemap_selector_gwyn_debug', 'gwyns_imagemap_selector', 'gwyns_imagemap_selector_main_section');
	add_settings_field( 'popup_url', __('Pop-Up URL', 'gwyns-imagemap-selector'), 'gwyns_imagemap_selector_popup_url', 'gwyns_imagemap_selector', 'gwyns_imagemap_selector_main_section');
	add_settings_field( 'popup_css', __('Pop-Up CSS', 'gwyns-imagemap-selector'), 'gwyns_imagemap_selector_popup_css', 'gwyns_imagemap_selector', 'gwyns_imagemap_selector_main_section');

	// Imagemap Creator
	add_settings_section( 'gwyns_imagemap_selector_imagemapcreator_section', __('Imagemap Creator', 'gwyns-imagemap-selector'), 'gwyns_imagemap_selector_imagemapcreator_section_text', 'gwyns_imagemap_selector-imagemapcreator');
	
	// Instructions
	add_settings_section( 'gwyns_imagemap_selector_instructions_section', __('Instructions', 'gwyns-imagemap-selector'), 'gwyns_imagemap_selector_instructions_section_text', 'gwyns_imagemap_selector-instructions');
}

function gwyns_imagemap_selector_add_defaults()
{
	$gwyns_imagemap_selector_settings = get_option( 'gwyns_imagemap_selector_settings' );
	if ( false === $gwyns_imagemap_selector_settings ) {
		$gwyns_imagemap_selector_settings = gwyns_imagemap_selector_get_default_options();
	}
	update_option( 'gwyns_imagemap_selector_settings', $gwyns_imagemap_selector_settings ); 
} // end gwyns_imagemap_selector_add_defaults()

/* Main text */

// Text before the option
function gwyns_imagemap_selector_main_section_text()
{
?>
	<p><?php _e('Settings for ','gwyns-imagemap-selector'); _e ('Gwyn\'s Imagemap Selector', 'gwyns-imagemap-selector'); ?></p>
<?php
} // end gwyns_imagemap_selector_main_section_text()

function gwyns_imagemap_selector_gwyn_debug() {
	$options = get_option('gwyns_imagemap_selector_settings');
	if($options['debug_mode']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk1' name='gwyns_imagemap_selector_settings[debug_mode]' type='checkbox' />";
	echo '<span class="description">' . __('Debug mode adds further comments on outputted HTML','gwyns-imagemap-selector') . '</span>';
} // end gwyns_imagemap_selector_gwyn_debug()

function gwyns_imagemap_selector_popup_url() {
	$options = get_option('gwyns_imagemap_selector_settings');
	echo "<input id='popup_url' name='gwyns_imagemap_selector_settings[popup_url]' size='100' type='text' value='{$options['popup_url']}' />";
	echo '<span class="description">' . __('External pop-up link','gwyns-imagemap-selector') . '</span>';
} // end gwyns_imagemap_selector_popup_url()

function gwyns_imagemap_selector_popup_css() {
	$options = get_option('gwyns_imagemap_selector_settings');
	echo "<textarea id='popup_css' name='gwyns_imagemap_selector_settings[popup_css]' rows='24' cols='80' type='textarea'>{$options['popup_css']}</textarea>";
	echo '<span class="description">' . __('Inline CSS for popup styling','gwyns-imagemap-selector') . '</span>';
} //end gwyns_imagemap_selector_popup_css()


/* Imagemap creator */

function gwyns_imagemap_selector_imagemapcreator_section_text()
{
?>
<p><?php _e('Uses the online editor as described by Adam Maschek in
<a href="http://www.maschek.hu/imagemap/imgmap">http://www.maschek.hu/imagemap/imgmap</a>; you can go to that page to get more help.', 'gwyns-imagemap-selector'); ?></p>

	<form id="img_area_form">
		<fieldset>
			<legend>
				<a onclick="toggleFieldset(this.parentNode.parentNode)"><?php _e('Select source', 'gwyns-imagemap-selector'); ?></a>
			</legend>
			<div>
				<div class="source_desc"><?php _e('An image from the Media Library', 'gwyns-imagemap-selector'); ?>:
				</div>
				<div class="source_url">
					<input id="upload_image" name="upload_image" type="text" />
					<input id="upload_image_button" value="<?php _e('Get image', 'gwyns-imagemap-selector'); ?>" type="button" onclick="myMediaPopupHandler();" />
				</div>
				<a href="javascript:gui_loadImage(document.getElementById('upload_image').value)" class="source_accept"><?php _e('accept', 'gwyns-imagemap-selector'); ?></a><br/>			
			</div>
		</fieldset>
		<fieldset>
			<legend>
				<a onclick="toggleFieldset(this.parentNode.parentNode)"><?php _e('Image map areas', 'gwyns-imagemap-selector'); ?></a>
			</legend>
			<div style="border-bottom: solid 1px #efefef">
			<div id="button_container">
				<!-- buttons come here -->
				<img src="<?php echo WP_PLUGIN_URL ?>/gwyns-imagemap-selector/imagemap-creator/add.gif" onclick="myimgmap.addNewArea()" alt="<?php _e('Add new area', 'gwyns-imagemap-selector'); ?>" title="<?php _e('Add new area', 'gwyns-imagemap-selector'); ?>"/>
				<img src="<?php echo WP_PLUGIN_URL ?>/gwyns-imagemap-selector/imagemap-creator/delete.gif" onclick="myimgmap.removeArea(myimgmap.currentid)" alt="<?php _e('Delete selected area', 'gwyns-imagemap-selector'); ?>" title="<?php _e('Delete selected area', 'gwyns-imagemap-selector'); ?>"/>
				<img src="<?php echo WP_PLUGIN_URL ?>/gwyns-imagemap-selector/imagemap-creator/zoom.gif" id="i_preview" onclick="myimgmap.togglePreview();" alt="<?php _e('Preview image map', 'gwyns-imagemap-selector'); ?>" title="<?php _e('Preview image map', 'gwyns-imagemap-selector'); ?>"/>
				<img src="<?php echo WP_PLUGIN_URL ?>/gwyns-imagemap-selector/imagemap-creator/html.gif" onclick="gui_htmlShow()" alt="<?php _e('Get image map HTML', 'gwyns-imagemap-selector'); ?>" title="<?php _e('Get image map HTML', 'gwyns-imagemap-selector'); ?>"/>
				<label for="dd_zoom"><?php _e('Zoom:', 'gwyns-imagemap-selector'); ?></label>
				<select onchange="gui_zoom(this)" id="dd_zoom">
				<option value='0.25'>25%</option>
				<option value='0.5'>50%</option>
				<option value='1' selected="1">100%</option>
				<option value='2'>200%</option>
				<option value='3'>300%</option>
				</select>
				<label for="dd_output"><?php _e('Output:', 'gwyns-imagemap-selector'); ?></label> 
				<select id="dd_output" onchange="return gui_outputChanged(this)">
				<option value='wp' selected><?php _e('WordPress shortcodes', 'gwyns-imagemap-selector'); ?></option>
				<option value='imagemap'><?php _e('Standard imagemap', 'gwyns-imagemap-selector'); ?></option>
				<option value='css'><?php _e('CSS imagemap', 'gwyns-imagemap-selector'); ?></option>
				<option value='wiki'><?php _e('Wiki imagemap', 'gwyns-imagemap-selector'); ?></option>
				</select>
				<div>
					<a class="toggler toggler_off" onclick="gui_toggleMore();return false;"><?php _e('More actions', 'gwyns-imagemap-selector'); ?></a>
					<div id="more_actions" style="display: none; position: absolute;">
						<div><a href="" onclick="toggleBoundingBox(this); return false;">&nbsp; <?php _e('bounding box', 'gwyns-imagemap-selector'); ?></a></div>
						<div><a href="" onclick="return false">&nbsp; <?php _e('background color', 'gwyns-imagemap-selector'); ?> </a><input onchange="gui_colorChanged(this)" id="color1" style="display: none;" value="#ffffff"></div>
					</div>
				</div>
			</div>
			<div style="float: right; margin: 0 5px">
				<select onchange="changelabeling(this)">
				<option value=''><?php _e('No labeling', 'gwyns-imagemap-selector'); ?></option>
				<option value='%n' selected='1'><?php _e('Label with numbers', 'gwyns-imagemap-selector'); ?></option>
				<option value='%a'><?php _e('Label with alt text', 'gwyns-imagemap-selector'); ?></option>
				<option value='%h'><?php _e('Label with href', 'gwyns-imagemap-selector'); ?></option>
				<option value='%c'><?php _e('Label with coords', 'gwyns-imagemap-selector'); ?></option>
				</select>
			</div>
			</div>
			<div id="form_container" style="clear: both;">
			<!-- form elements come here -->
		 	</div>
		</fieldset>
		<fieldset>
			<legend>
				<a onclick="toggleFieldset(this.parentNode.parentNode)"><?php _e('Image', 'gwyns-imagemap-selector'); ?></a>
			</legend>
			<div id="pic_container">
			</div>			
		</fieldset>
		<fieldset>
			<legend>
				<a onclick="toggleFieldset(this.parentNode.parentNode)"><?php _e('Status', 'gwyns-imagemap-selector'); ?></a>
			</legend>
			<div id="status_container"></div>
		</fieldset>
		<fieldset id="fieldset_html" class="fieldset_off">
			<legend>
				<a onclick="toggleFieldset(this.parentNode.parentNode)"><?php _e('Code', 'gwyns-imagemap-selector'); ?></a>
			</legend>
			<div>
			<div id="output_help">
			</div>
			<textarea id="html_container"></textarea></div>
		</fieldset>
	</form>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL ?>/gwyns-imagemap-selector/imgmap/imgmap.js"></script>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL ?>/gwyns-imagemap-selector/imagemap-creator/default_interface.js"></script>

<?php
} // end gwyns_imagemap_selector_imagemapcreator_section_text()

/* Instructions text */

function gwyns_imagemap_selector_instructions_section_text()
{
?>
<p><?php _e('Gwyn\'s Imagemap Selector'); _e(' uses shortcodes to define imagemaps, assign an image to it, and automatically make queries on the WordPress database to extract the appropriate links.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('There are two basic usages of this plugin. The first is if you know exactly which URLs are linked to each area of the imagemap. This is appropriate for menus (and very likely used on a widget) that will not change much over time. This uses the "direct" approach and requires URLs to be explicitly named on each area; thus, it\'s quite similar to directly placing the imagemap HTML inside the post/page/widget (the only advantage of using the shortcode is to get automatic ids).', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('The second variant makes a query on the WordPress database and returns the appropriate permalinks for each post. You can query by category, tag, or even add a free query (it will be passed to WP_Query so the same syntax applies; see <a href="http://codex.wordpress.org/Function_Reference/WP_Query">http://codex.wordpress.org/Function_Reference/WP_Query</a>). The order of permalinks thus retrieved will dynamically be assigned to each area (in the order those are written). If there are more areas than permalinks, the remaining areas will be ignored (the reverse situation is undefined). Category and tag queries can be made by name or id and can be retrieved either in the descending (default) or ascending order.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('A few extra parameters are available to add names, titles, ids, and classes. If those are omitted, this plugin will provide "best effort" alternatives to comply with HTML guidelines. This also allows for extra styling.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('The overall syntax is:', 'gwyns-imagemap-selector'); ?>
</p>
<p><pre><?php _e('[imagemap (category=["category id"|"category name"] 
		| category_name="category name"
		| tag=["tag id"|"tag name"]
		| tag_name="tag name"
		| query="a WP_QUERY string") 
		| direct=[0|1] 
		img="/your/url/to/image"
		title="Image title" 
		order=["DESC"|"ASC"]
		nopaging=["true"|"false"]
		map="imagemap name" 
		id="HTML id" 
		class="CSS style"
		alt="Alt text for the image"
		popup="true|false"
		thumbnail="true|false"
		excerpt="true|false"]
[area shape=["rect"|"poly"|"circle"]
	url="/direct/link/for/this/area 
	alt="name for this area"
	class="CSS style"
	target=["_self"|"_blank"|"_top"|...]
	title="name for this area"]coord1,coord2,coord3,...,coordN[/area]
[/imagemap]</pre>'); ?></p>

<p><?php _e('Thus, imagemaps can be either in direct mode (direct=1) or in query mode (omit the direct clause). When in query mode, you can use query by categories (id or name/slug), tags (id or name), or a free query that complies with WP_Query types of queries. Queries by category or tag can be made in descending (default; can be omitted) or ascending order (this parameter is ignored when using a WP_Query-compliant query instead). The <code>img</code> parameter is mandatory. <code>map</code>, <code>id</code>, <code>class</code>, and <code>alt</code> are all optional and a best effort to fill them with plausible values will be provided.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('Queries are usually paged, i.e. they will respect the post limit set on <code>Reading Settings > Blog pages show at most</code>. To override, use <code>nopaging="true"</code>.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('Each area has just a mandatory section, the one between [area][/area] tags. Default shape is a rectangle (<code>shape="rect"</code>) and this would require 4 coordinates (coordinate numbers are neither checked nor validated). For direct mode, an additional url for each area has to be supplied (inside the [area] tag). <code>alt</code>, <code>class</code>, and <code>title</code> are optional and they will be filled automatically with plausible values if omitted.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('Multiple categories/tags should work (as well as excluding categories/tags); note that <code>category=Uncategorized</code> will work (even though it should be <code>category_name=Uncategorized</code>) but <code>category=first,second,last</code> will not (use <code>category_name=first,second,last</code> instead).', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('If you wish, you can get a hovering popup for the imagemap that shows the article linked to it. Use <code>popup=true</code> and you can optionally specify a thumbnail and/or just show the excerpt. CSS styling is done via the <code>class</code> parameter.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('You can also specify your own AJAX handler and call it remotely instead of using the built-in handler. All parameters are passed via <code>GET</code>, and you should at least pass a post ID (parameter <code>id</code>). Optionally you can receive the CSS class for styling on the parameter <code>class</code>. <code>excerpt</code> and <code>thumbnail</code> will be set to true if the user specifies those parameters on the shortcode tag for the imagemap.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('Also, several imagemaps with the same name in the same post/page/widget haven\'t been tested either. In theory you could define an imagemap <em>without</em> an associated image, and insert images and change manually the imagemap for each, so that the same imagemap is used across several images in the same page. None of this was tested.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('Imagemaps can be conveniently created from the Imagemap Creator tab, which uses <a href="http://code.google.com/p/imgmap/">Adam Maschek\'s imgmap Javascript library</a> to provide an interactive, Web-based imagemap creator.', 'gwyns-imagemap-selector'); ?>
</p>
<p><?php _e('The plugin will now work for anyone with read capabilities (and not only administrators), but only administrators have access to the "Settings" tab.', 'gwyns-imagemap-selector'); ?>
<p><?php _e('Warning: this plugin will not work with any automatic image-resizing mechanisms (e.g. Jetpack\'s Photon or similar tools, specially those for dynamic/fluid themes which resize images for mobile viewing). This is a limitation of the <code>imagemap</code> HTML command, which is very simple and basic, and relies upon absolute values relative to an image\'s dimensions, which are supposed to be known in advance. There are a few techniques to rewrite imagemaps (basically recalculating the selection areas based on the redimensioned image), but these are a bit pointless for the purpose of this plugin, which totally avoids Javascript (for browsers which have it turned off), and there are already better, Javascript/Flash/Java-based solutions which allow clickable areas on resized images.', 'gwyns-imagemap-selector'); ?>
</p>
<?php
} // end gwyns_imagemap_selector_instructions_section_text()

/* Form validation functions */

function gwyns_imagemap_selector_validate($input)
{
	// CSS should be validated

	return $input; // it's just a checkbox with true/false
//	$gwyns_imagemap_selector_settings = get_option('gwyns_imagemap_selector_settings');
//	$input = $gwyns_imagemap_selector_settings;
} // end gwyns_imagemap_selector_validate()

/* Dealing with admin CSS for the nice tabs and CSS for the Imagemap Creator */

function gwyns_imagemap_selector_enqueue_admin_styles() {
	// define admin stylesheet
	$admin_handle = 'gwyns_imagemap_selector_admin_stylesheet';
	$admin_stylesheet = plugins_url() . '/gwyns-imagemap-selector/gwyns-imagemap-selector-admin.css';
	 
	echo "<!-- Directory URI: $admin_stylesheet -->\n";

	wp_enqueue_style( $admin_handle, $admin_stylesheet );
	 
	/* Imagemap creator requires some styles */
	wp_enqueue_style('gwyns_imagemap_selector_imgmap', plugins_url() .  '/gwyns-imagemap-selector/imagemap-creator/imgmap.css');
	wp_enqueue_style('gwyns_imagemap_selector_colorpicker', plugins_url() .  '/gwyns-imagemap-selector/imagemap-creator/js/colorPicker.css');

	/* For the imagemap creator; the following needs the >IE6 comparison tag since
	 	it relies on Canvas */
?>
<!--[if gte IE 6]>
<script language="javascript" type="text/javascript" src="<?php echo plugins_url() ?>/gwyns-imagemap-selector/imgmap/excanvas.js"></script>
<![endif]-->
<?php

	wp_enqueue_style('thickbox'); // for the media uploader textbox
} // end gwyns_imagemap_selector_enqueue_admin_styles()

/* For the Imagemap creator we need jQuery and the colorpicker (registered scripts) */

function gwyns_imagemap_selector_enqueue_admin_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-colorPicker', plugins_url() .  '/gwyns-imagemap-selector/imagemap-creator/js/jquery.colorPicker.js');
	wp_enqueue_media();
	// wp_enqueue_script('media-upload');
	// wp_enqueue_script('thickbox');
?>
<script type="text/javascript">
// Deals with calling the WordPress Media popup box
function myMediaPopupHandler()
{
	event.preventDefault();

	frame = wp.media({
		title:	  '<?php _e('Choose', 'gwyns-imagemap-selector'); ?>',
		library:	  {				   
		}
	});

	frame.on( 'toolbar:render:select', function(view) {
		view.set({
			select: {
				style: 'primary',
				text:  '<?php _e('Choose', 'gwyns-imagemap-selector'); ?>',
				click: function() {
					var attachment = frame.state().get('selection').first();										jQuery('#upload_image').val(attachment.attributes.url);											frame.close();
				}
			}
		});
	});
 
	frame.setState('library').open();
       
return false;
}
</script>
<?php
} // end gwyns_imagemap_selector_enqueue_admin_scripts()

register_activation_hook(__FILE__, 'gwyns_imagemap_selector_add_defaults');
add_action('admin_menu', 'gwyns_imagemap_selector_admin_menu_options');
add_action('admin_init', 'gwyns_imagemap_selector_register_settings');
add_shortcode('imagemap', 'gwyns_imagemap_selector_shortcode_imagemap');
add_shortcode('area', 'gwyns_imagemap_selector_shortcode_area');
add_filter('widget_text', 'do_shortcode'); // allow shortcodes in widgets too
?>