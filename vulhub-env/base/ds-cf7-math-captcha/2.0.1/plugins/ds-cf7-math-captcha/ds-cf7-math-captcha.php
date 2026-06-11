<?php
/*
Plugin Name: DS CF7 Math Captcha
Version: 2.0.1
Author: Dotsquares WPTeam
Author URI: https:dotsquares.com
Plugin URI: dotsquares.com
Description: To prevent spam emails, adding a math captcha is a useful strategy. .
Text Domain: dscf7-math-captcha
Domain Path: /languages

*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// Define the version number for the DS CF7 Math Captcha plugin
define( 'DSCF7_VERSION', '2.0.1' );


// Define the required WordPress version for the DS CF7 Math Captcha plugin
define( 'DSCF7_REQUIRED_WP_VERSION', '6.3.1' );

// Define the text domain for translation
define( 'DSCF7_TEXT_DOMAIN', 'dscf7-math-captcha' );

// Define the plugin file path
define( 'DSCF7_PLUGIN', __FILE__ );

// Define the plugin's basename
define( 'DSCF7_PLUGIN_BASENAME', plugin_basename( DSCF7_PLUGIN ) );

// Define the plugin's name
define( 'DSCF7_PLUGIN_NAME', trim( dirname( DSCF7_PLUGIN_BASENAME ), '/' ) );

// Define the plugin's URL
define( 'DSCF7_PLUGIN_URL', WP_CONTENT_URL . '/plugins/'. DSCF7_PLUGIN_NAME );

// Define the plugin's directory path
define( 'DSCF7_PLUGIN_DIR', untrailingslashit( dirname( DSCF7_PLUGIN ) ) );


/**added init capctha hook */
add_action( 'wpcf7_init', 'dscf7_capctha' );

/**check validation filter */	
add_filter( 'wpcf7_validate_dscf7captcha', 'dscf7_captcha_validation', 10, 2 );

//add_filter( 'wpcf7_validate_dscf7captcha*', 'dscf7_captcha_validation', 10, 2 );
add_action( 'wp_enqueue_scripts', 'dscf7_ajaxify_scripts' );

/**ajax callback handler */
add_action( 'wp_ajax_dscf7_refreshcaptcha','dscf7_refreshcaptcha_callback');
add_action( 'wp_ajax_nopriv_dscf7_refreshcaptcha','dscf7_refreshcaptcha_callback');

/**text domain handler */
add_action( 'init', 'wpcf7sr_load_textdomain' );

/**deactivate handler hook */
add_action('admin_init', 'dscf7_deactivate_on_cf7_deactivation');

/**message section dynamic */
add_filter('wpcf7_messages', 'dscf7_wpcf7_messages_callback');

/**
 * Function to deactivate DS CF7 Math Captcha when Contact Form 7 is deactivated.
 * 
 * This function checks if Contact Form 7 is not installed and the DS CF7 Math Captcha plugin is active,
 * then it adds a notice and deactivates the plugin.
 */
function dscf7_deactivate_on_cf7_deactivation()
{
    // Check if Contact Form 7 class doesn't exist and DS CF7 Math Captcha plugin is active
    if (!class_exists('WPCF7') && is_plugin_active('ds-cf7-math-captcha/ds-cf7-math-captcha.php'))
	{
        // Add an admin notice to inform the user
        add_action('admin_notices', 'dscf7_plugin_contact_form7_notice');
        // Add more actions or custom notices if needed
        
        // Deactivate the plugin (if needed)
        //deactivate_plugins( plugin_basename( DSCF7_PLUGIN ) );
    }
}


/**
 * Function to display notice when Contact Form 7 is not activated.
 */
function dscf7_plugin_contact_form7_notice()
{
    ?>
    <!-- Display a notice in the WordPress admin area -->
    <div class="notice notice-error">
        <!-- Display a warning message -->
        <p><?php _e( 'Warning: Your DS CF7 Math Captcha plugin requires Contact Form 7 to be installed and activated. Please install and activate Contact Form 7 to use this plugin.', DSCF7_TEXT_DOMAIN); ?></p>
    </div>
    <?php
}


/**
 * Callback function to modify Contact Form 7 error messages.
 *
 * @param array $messages The array of error messages.
 * @return array The modified array of error messages.
 */
function dscf7_wpcf7_messages_callback($messages)
{
    // Get the current Contact Form 7 form
    $current_form = WPCF7_ContactForm::get_current();
    
    // Check if a form is currently being processed
    if ($current_form)
	{
        // Get the ID of the current form
        $form_id = $current_form->id();
        
        // Get custom error messages for the form from post meta
        $custom_value = get_post_meta($form_id, '_messages', true);
        
        // Set dynamic error message for incorrect captcha
        $dynami_err_message = isset($custom_value['invalid_letters_digits']) && !empty($custom_value['invalid_letters_digits']) ? $custom_value['invalid_letters_digits'] : __('Incorrect captcha!', DSCF7_TEXT_DOMAIN);
        
        // Set error message for missing captcha
        $please_enter_capthca = isset($custom_value['invalid_letters']) && !empty($custom_value['invalid_letters']) ? $custom_value['invalid_letters'] : __('Please enter captcha.', DSCF7_TEXT_DOMAIN);
        
        // Check if dynamic error message is not empty
        if (!empty($dynami_err_message))
		{
			// Add dynamic error message for incorrect captcha to messages array
			$messages['invalid_letters_digits'] = [
            'description' => __('Incorrect captcha!', DSCF7_TEXT_DOMAIN),
            'default'     => $dynami_err_message,
        	];
		}
		
		// Check if error message for missing captcha is not empty
		if (!empty($please_enter_capthca))
		{
        	// Add error message for missing captcha to messages array
        	$messages['invalid_letters'] = [
            'description' => __('Please enter captcha.', DSCF7_TEXT_DOMAIN),
            'default'     => $please_enter_capthca,
        	];
		}
    }

    return $messages;
}

/**translation load file .po/.mo and language folder */
function wpcf7sr_load_textdomain() {
	load_plugin_textdomain( 'dscf7-math-captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	$domain = 'dscf7-math-captcha';
	 $locale = apply_filters('plugin_locale', get_locale(), $domain);
	load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
}

/**
 * Function to add a custom form tag for DS CF7 Math Captcha.
 */
function dscf7_capctha()
{
	// Define attributes for the form tag
	$name_attr = array( 
		'name-attr' => true 
	); 
	
	// Add the custom form tag 'dscf7captcha' using 'dscf7_captcha_handler' as the callback function
	wpcf7_add_form_tag('dscf7captcha', 'dscf7_captcha_handler', $name_attr);
}


/**
 * Function to validate DS CF7 Math Captcha.
 *
 * @param object $result The result object.
 * @param object $tag    The tag object.
 * @return object The modified result object.
 */
function dscf7_captcha_validation( $result, $tag )
{
	// Check if the tag type is 'dscf7captcha'
	if( $tag->type == 'dscf7captcha' )
	{
		// Initialize variables for captcha calculation
		$finalCechking = '';
		$cptha1 = sanitize_text_field($_POST['dscf7_hidden_val1-' . $tag->name]);
		$cptha2 = sanitize_text_field($_POST['dscf7_hidden_val2-' . $tag->name]);
		$cptha3 = sanitize_text_field($_POST['dscf7_hidden_action-' . $tag->name]);
		
		// Perform captcha calculation based on the operation
		if( $cptha3 == 'x' ) { 
			$finalCechking = $cptha1 * $cptha2;
		} elseif( $cptha3 == '+' ) { 
			$finalCechking = $cptha1 + $cptha2;
		} else {
			$finalCechking = $cptha1 - $cptha2;
		}
		
		// Get the submitted captcha value
		$cptcha_value = isset( $_POST[$tag->name] ) ? trim( wp_unslash( strtr( (string) $_POST[$tag->name], "\n", " " ) ) ) : '';
		
		// Get custom error messages
		$custom_messages = apply_filters('wpcf7_messages', array());
			
		// Check if captcha value is empty
		if( $cptcha_value == '' ) {
			$in_please_captcha_message = (isset($custom_messages['invalid_letters']['default']) && !empty(isset($custom_messages['invalid_letters']['default']))) ? $custom_messages['invalid_letters']['default'] : 'Please enter Captcha.'; 
			$result->invalidate($tag, apply_filters( 'dscf7_captcha_required', $in_please_captcha_message));
		}
		
		// Check if captcha value is incorrect
		if( $cptcha_value != '' && $cptcha_value != $finalCechking ) {
			$in_valid_captcha_message = (isset($custom_messages['invalid_letters_digits']['default']) && !empty(isset($custom_messages['invalid_letters_digits']['default']))) ? $custom_messages['invalid_letters_digits']['default'] : 'Incorrect Captcha!';  
			$result->invalidate($tag, apply_filters( 'dscf7_captcha_invalidate', $in_valid_captcha_message));
		}
	}
	
	// Return the modified result object
	return $result;
}

/**
 * Handler function for the DS CF7 Math Captcha form tag.
 *
 * @param object $tag The tag object.
 * @return string The HTML markup for the captcha.
 */
function dscf7_captcha_handler( $tag )
{
	// Define an array of mathematical operations
	$operationAry = array('+', 'x', '-');
	
	// Select two random operations
	$random_action = array_rand($operationAry, 2);
	$random_actionVal = $operationAry[$random_action[0]];
	
	// Generate two random values
	$actnVal1 = rand(1, 9);
	$actnVal2 = rand(1, 9);
	
	// Build the HTML markup for the captcha
	$ds_cf7_captcha = '<p class="dscf7captcha"><input name="dscf7_hidden_val1-' . $tag->name . '" id="dscf7_hidden_val1-' . $tag->name . '" type="hidden" value="' . $actnVal1 . '" /><input name="dscf7_hidden_val2-' . $tag->name . '" id="dscf7_hidden_val2-' . $tag->name . '" type="hidden" value="' . $actnVal2 . '" /><input name="dscf7_hidden_action-' . $tag->name . '" id="dscf7_hidden_action-' . $tag->name . '" type="hidden" value="' . $random_actionVal . '" />';
	$ds_cf7_captcha .= 'What is <span class="cf7as-firstAct">' . $actnVal2 . '</span> ' . $random_actionVal . '<span class="cf7as-firstAct"> ' . $actnVal1 . '</span> ? <a href="javascript:void(0)" id="' . $tag->name . '" class="dscf7_refresh_captcha"><img class="dscf7_captcha_icon" src="' . DSCF7_PLUGIN_URL . '/assets/img/icons8-refresh-30.png"/><img class="dscf7_captcha_reload_icon" src="' . DSCF7_PLUGIN_URL . '/assets/img/446bcd468478f5bfb7b4e5c804571392_w200.gif" style="display:none; width:30px" /></a><br><span class="wpcf7-form-control-wrap" data-name="' . $tag->name . '"> <input type="text" aria-invalid="false" aria-required="true" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" size="5" value="" name="' . $tag->name . '" placeholder="' . esc_html__('Type your answer', DSCF7_TEXT_DOMAIN) . '" style="width:200px; margin-bottom:10px;" oninput="this.value = this.value.replace(/[^0-9.]/g, \'\').replace(/(\..*)\./g, \'$1\');"></span></p>';
	
	// Return the HTML markup for the captcha
	return $ds_cf7_captcha;
}


/**
 * Enqueue scripts and styles for DS CF7 Math Captcha plugin.
 */
function dscf7_ajaxify_scripts()
{
    // Enqueue JavaScript file for refreshing captcha
    wp_enqueue_script( 'dscf7_refresh_script', DSCF7_PLUGIN_URL.'/assets/js/script-min.js', array('jquery'), '1.2.0', true );
    
    // Localize the script to make the AJAX URL available in JavaScript
    wp_localize_script( 'dscf7_refresh_script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    
    // Register and enqueue CSS style for the math captcha
    wp_register_style( 'dscf7-math-captcha-style',  DSCF7_PLUGIN_URL.'/assets/css/style.css', array(), '', true );
    wp_enqueue_style( 'dscf7-math-captcha-style' );
}


/**
 * Callback function to refresh the captcha.
 *
 * @param array $tag The tag array.
 */
function dscf7_refreshcaptcha_callback( $tag ) {
    // Define array of possible mathematical operations
    $operationAry = array('+', 'x', '-');

    // Get two random operations
    $random_action = array_rand($operationAry, 2);
    $random_actionVal = $operationAry[$random_action[0]];

    // Generate two random values
    $actnVal1 = rand(1, 9);
    $actnVal2 = rand(1, 9);

    // Get the tag name from the POST data
    $tagName = $_POST['tagname'];

	    // Construct the captcha HTML
		$ds_cf7_captcha = '<input name="dscf7_hidden_val1-' . $tagName . '" id="dscf7_hidden_val1-' . $tagName . '" type="hidden" value="' . $actnVal1 . '" />';
		$ds_cf7_captcha .= '<input name="dscf7_hidden_val2-' . $tagName . '" id="dscf7_hidden_val2-' . $tagName . '" type="hidden" value="' . $actnVal2 . '" />';
		$ds_cf7_captcha .= '<input name="dscf7_hidden_action-' . $tagName . '" id="dscf7_hidden_action-' . $tagName . '" type="hidden" value="' . $random_actionVal . '" />';
		$ds_cf7_captcha .= 'What is <span class="cf7as-firstAct">' . $actnVal2 . '</span> ' . $random_actionVal . '<span class="cf7as-firstAct"> ' . $actnVal1 . '</span> ? ';
		$ds_cf7_captcha .= '<a href="javascript:void(0)" id="' . $tagName . '" class="dscf7_refresh_captcha">';
		$ds_cf7_captcha .= '<img class="dscf7_captcha_icon" src="' . DSCF7_PLUGIN_URL . '/assets/img/icons8-refresh-30.png"/>';
		$ds_cf7_captcha .= '<img class="dscf7_captcha_reload_icon" src="' . DSCF7_PLUGIN_URL . '/assets/img/446bcd468478f5bfb7b4e5c804571392_w200.gif" style="display:none; width:30px" />';
		$ds_cf7_captcha .= '</a><br>';
		$ds_cf7_captcha .= '<span class="wpcf7-form-control-wrap" data-name="' . $tagName . '">';
		$ds_cf7_captcha .= '<input type="text" aria-invalid="false" aria-required="true" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" size="5" value="" name="' . $tagName . '" placeholder="' . esc_html__('Type your answer', DSCF7_TEXT_DOMAIN) . '" style="width:200px;margin-bottom:10px;" oninput="this.value = this.value.replace(/[^0-9.]/g, \'\').replace(/(\..*)\./g, \'$1\');">';
		$ds_cf7_captcha .= '</span>';
        echo $ds_cf7_captcha;
    exit;
}

/**
 * Adds the tag generator for the DS CF7 Math Captcha to Contact Form 7.
 */
function wpcf7_add_tag_generator_dsmathcaptcha()
{
	// Get the instance of the tag generator
	$tag_generator = WPCF7_TagGenerator::get_instance();
	
	// Add the tag generator for the 'dscf7captcha' tag
	$tag_generator->add( 'dscf7captcha', __( 'math-captcha', 'contact-form-7' ), 'wpcf7_tag_generator_dsmathcaptcha' );
}

// Hook the function to the 'wpcf7_admin_init' action with a priority of 65 and no parameters
add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_dsmathcaptcha', 65, 0 );


/**
 * Callback function for generating tag for the DS CF7 Math Captcha.
 *
 * @param object $contact_form The contact form object.
 * @param array $args          Optional arguments.
 */
function wpcf7_tag_generator_dsmathcaptcha( $contact_form, $args = '' ) {
	// Parse the arguments
	$args = wp_parse_args( $args, array() );
	$type = $args['id'];

	// Check if the type is 'dscf7captcha'
	if ( 'dscf7captcha' == $type ) {
		// Set the description for the tag generator
		$description = __( "Copy given below shortcode in form , see %s.", 'contact-form-7' );
	} 

?>
<div class="control-box">
	<fieldset>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>
<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
	<div class="submitbox">
		<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>
</div>
<?php
}
