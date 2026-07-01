<?php
/**
 * WPBookit Template
 *
 * Functions for the templating system.
 *
 * @package  WPBookit\Functions
 * @version  1.0.4
 */

defined('ABSPATH') || exit;

/**
 * Profile
 */
if (!function_exists('wpb_edit_profile')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_edit_profile($args = array())
	{
		if (isset($args['user_id']) && isset($args['username'])) {
			$userID = $args['user_id'];
			wpb_get_template(
				'shortcodes/profile/html-edit-profile.php',
				array('user_id' => $userID, 'username' => $args['username'])
			);
		}
	}
}

if (!function_exists('wpb_bookings_history')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_bookings_history($args)
	{
		wpb_get_template(
			'shortcodes/profile/html-bookings-history.php',
			$args
		);
	}
}
if (!function_exists('wpb_upcoming_bookings')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_upcoming_bookings($args)
	{
		wpb_get_template(
			'shortcodes/profile/html-upcoming-bookings.php',
			$args
		);
	}
}
if (!function_exists('wpb_pending_bookings')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_pending_bookings($args)
	{
		wpb_get_template(
			'shortcodes/profile/html-pending-bookings.php',
			$args
		);
	}
}

if (!function_exists('wpb_booking_no_upcoming')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_no_upcoming($args = array())
	{
			wpb_get_template(
				'shortcodes/profile/html-no-upcoming-booking.php',
				$args
			);
	}
}
if (!function_exists('wpb_booking_no_pending')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_no_pending($args = array())
	{
			wpb_get_template(
				'shortcodes/profile/html-no-pending-booking.php',
				$args
			);
	}
}
if (!function_exists('wpb_booking_no_history')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_no_history($args = array())
	{
			wpb_get_template(
				'shortcodes/profile/html-no-history-booking.php',
				$args
			);
	}
}

/**
 * Booking-Types
 */
if (!function_exists('wpb_booking_types')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_types($args = array())
	{
		if(isset($args['booking_types'])){
			wpb_get_template(
				'shortcodes/booking-types/booking-types.php',
				array('booking_types'=>$args['booking_types'])
			);
		}
	}
}


// Booking Shortcode Hooks  

/**
 * Booking
 */
if (!function_exists('wpb_bookings_model')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_bookings_model($args = array())
	{
		if(isset($args['shortcode_instance'])){
			wpb_get_template(
				'shortcodes/booking/html-shortcode-booking-model.php',
				array('shortcode_instance'=>$args['shortcode_instance'])
			);
		}
	}
}

/**
 * Booking
 */
if (!function_exists('wpb_bookings_timeslot')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_bookings_timeslot($args = array())
	{
		if(isset($args['time_slot'])){
			wpb_get_template(
				'shortcodes/booking/html-shortcode-booking-timeslot.php',
				$args
			);
		}
	}
}
if (!function_exists('wpb_booking_shortcode_detail_tab_render')) {

	function wpb_booking_shortcode_detail_tab_render( WPB_Shortcode_Booking $shortcode_instance)
	{
		wpb_get_template(
			'shortcodes/booking/tabs/html-shortcode-booking-detail-tab.php',
			array('shortcode_instance'=>$shortcode_instance )
		);
	}
}
if (!function_exists('wpb_booking_shortcode_payment_tab_render')) {

	function wpb_booking_shortcode_payment_tab_render( WPB_Shortcode_Booking $shortcode_instance)
	{
		wpb_get_template(
			'shortcodes/booking/tabs/html-shortcode-booking-payment-tab.php',
			array('shortcode_instance'=>$shortcode_instance )
		);
	}
}
if (!function_exists('wpb_booking_shortcode_tabs_render')) {

	function wpb_booking_shortcode_tabs_render( WPB_Shortcode_Booking $shortcode_instance)
	{
		foreach ($shortcode_instance->get_tabs() as  $key=> $template) {
			?><fieldset class="wpb-tab <?php echo esc_html($key==0?"active" :'')  ?>" id="<?php echo esc_html($template['tab']); ?>" ><?php
			do_action($template['tab'],$shortcode_instance);
			?></fieldset><?php
		}
	}
}
if (!function_exists('wpb_booking_shortcode_model_pagination_render')) {

	function wpb_booking_shortcode_model_pagination_render( WPB_Shortcode_Booking $shortcode_instance)
	{
		wpb_get_template(
			'shortcodes/booking/html-shortcode-booking-model-footer.php',
			array('shortcode_instance'=>$shortcode_instance )
		);
	}
}



/**
 * Add Booking Type OffCanvas
 */
if (!function_exists('wpb_add_booking_type_form')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_add_booking_type_form($args = array())
	{
		wpb_get_template(
			'dashboard/booking-types/booking-types-form.php',
			[ 
				'avalible_duration' => $args['avalible_duration'],
				'all_weekdays'		=> $args['all_weekdays'],
				'meeting_tools' 	=> $args['meeting_tools'] 
			]
		);
	}
}


/**
 * Booking Shortcode Form 
 */
if (!function_exists('wpb_booking_shortcode_form')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_shortcode_form($args = array())
	{
		$is_guest_mode = true;

		if($args['shortcode_instance']->booking_type->get_meta('guest_invite')!='true'){
			$is_guest_mode =$args['shortcode_instance']->wpb_general_setting_data_booking_type=='guest';
		}else{
			$is_guest_mode= true;
		}

		if(is_user_logged_in()){
			$is_guest_mode= false;
		}
		
		if($is_guest_mode){
			wpb_get_template(
				"shortcodes/booking/html-shortcode-booking-form-{$args['shortcode_instance']->booking_options}.php",
				array('shortcode_instance'=>$args['shortcode_instance'])
			);
			wpb_get_template(
				"shortcodes/booking/html-shortcode-booking-form-user-email.php",
				array('shortcode_instance'=>$args['shortcode_instance'])
			);
			wpb_get_template(
				"shortcodes/booking/html-shortcode-booking-form-user-phone-number.php",
				array('shortcode_instance'=>$args['shortcode_instance'])
			);
		}else{
			if(!is_user_logged_in()){
				wpb_get_template(
					"shortcodes/booking/html-shortcode-booking-form-user-register-login.php",
					array('shortcode_instance'=>$args['shortcode_instance'])
				);
			}else{
				if(empty(get_user_meta( get_current_user_id() , 'first_name', true ))){
					wpb_get_template(
						"shortcodes/booking/html-shortcode-booking-form-{$args['shortcode_instance']->booking_options}.php",
						array('shortcode_instance'=>$args['shortcode_instance'])
					);
				}
			}
		}

		if(has_action('wpb_after_user_detail_field')){
			wpb_get_template(
				"shortcodes/booking/html-shortcode-booking-form-user-extra-fields.php",
				array('shortcode_instance'=>$args['shortcode_instance'])
			);
		}

		wpb_get_template(
			"shortcodes/booking/html-shortcode-booking-questions.php",
			array('shortcode_instance'=>$args['shortcode_instance'])
		);

	}
}
/**
 * Booking Shortcode Form 
 */
if (!function_exists('wpb_booking_shortcode_form_question_type')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_shortcode_form_question_type($args = array())
	{
		$guest_index = ($args['guest_index']??false);
		$guest_class = 'wpb-booking_question';
		if($guest_index!==false){
			$guest_class = "[wpb-booking_question]"; 
		}
		wpb_get_template(
			"shortcodes/booking/question-types/html-shortcode-booking-form-input-{$args['question']['type']}.php",
			array('question'=>$args['question'] ,'guest_index_class'=>$guest_class)
		);

	}
}

/**
 * Booking user First And Last Name Field
 */
if (!function_exists('wpb_booking_shortcode_user_name_fields')) {

	/**
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $default_path  Default path. (default: '').
	 */
	function wpb_booking_shortcode_user_name_fields($args = array())
	{
		wpb_get_template(
			"shortcodes/booking/html-shortcode-booking-form-{$args['shortcode_instance']->booking_options}.php",
			array('shortcode_instance'=>$args['shortcode_instance'])
		);

	}
}

/**
 * Booking user First And Last Name Field
 */
if (!function_exists('wpb_staff_navbar_menu_filter')) {

	/**
	 * @param string $navMenus args.
	 */
	function wpb_staff_navbar_menu_filter($navMenus)
	{
		if( ! current_user_can( 'administrator' ) ) :
			unset( $navMenus['general-tab'] );
			unset( $navMenus['emails-tab'] );
			unset( $navMenus['shortcode-tab'] );
			unset( $navMenus['theme-settings-tab'] );
			unset( $navMenus['offline-payments-tab'] );
			unset( $navMenus['import-tab'] );
			unset( $navMenus['custom-code-tab'] );
			unset( $navMenus['online-payments-tab'] );
			unset( $navMenus['import-tab'] );

			$navMenus['calender-tab']['is_selected'] = true;
		endif;
		return $navMenus;
	}
}