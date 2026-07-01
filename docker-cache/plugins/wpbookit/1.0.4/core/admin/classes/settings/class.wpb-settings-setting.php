<?php
/**
 * WPB Admin Settings Design Class
 */

if ( ! class_exists( 'WPB_Settings_Setting', false ) ) :

	/**
	 * WPB_Settings_Setting Class.
	 */
	class WPB_Settings_Setting extends WPB_Setting_Page {

		function __construct() {
			$this->id          = 'settings';
			$this->label       = esc_html__( 'Settings', 'wpbookit' );
			$this->type        = esc_html__( 'SETTING', 'wpbookit' );
			$this->icon        = IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/settings-icon.svg';
			$this->priority    = 100;

			parent::__construct();
		}

		public function get_settings_html() {
			$currencies = get_wpbookit_currencies();
			$nav_menu = $this->get_navbar_menu();
			$booking_type = $this->get_booking_type();
			$include_exclude_tax = $this->wpb_booking_include_exclude_tax();
			$booking_options = $this->get_booking_options();
			$booking_status = $this->get_new_booking_default();
			$booking_limit = $this->get_booking_limit();
			$cancellation_buffer = $this->get_cancellation_buffer();
			$login_redirect = $this->get_login_redirect();
			$booking_redirect = $this->get_booking_booking_redirect();
			$options = $this->get_email_options();
			$general_setting_data = $this->get_general_setting_data();
			$theme_setting_data = $this->get_theme_setting_data();
			$booking_types = wpb_get_all_booking_types(
				array(
					'staff'		=> current_user_can('administrator') ? 0 : get_current_user_id(),
					"per_page" => 99
				)
			);
			/* Get Zoom Setting */
			$get_config = get_option('wpb_zoom_settings');
		
			$wpb_zoom_status = isset( $get_config['wpb_zoom_status'] ) ? $get_config['wpb_zoom_status'] : '';
			$wpb_zoom_client_id = isset($get_config['wpb_zoom_client_id']) ? $get_config['wpb_zoom_client_id'] : '';
			$wpb_zoom_client_secret = isset($get_config['wpb_zoom_client_secret']) ? $get_config['wpb_zoom_client_secret'] : '';
			$current_user = wp_get_current_user();
			if ( in_array( 'staff', (array) $current_user->roles ) ) {
				$get_config = get_user_meta($current_user->ID, 'wpb_zoom_settings', true);
				$zoom_status = isset( $get_config['zoom_status'] ) ? $get_config['zoom_status'] : '';
				$client_id = isset($get_config['client_id']) ? $get_config['client_id'] : '';
				$client_secret = isset($get_config['client_secret']) ? $get_config['client_secret'] : '';
			}
			$wpb_zoom_client_redirect_uri 	= site_url() . '/oauth-zoom-token';
			
			$get_remainder_interval = $this->remainder_interval();
			$current_year = date('Y');  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			$active_payment_gateways = get_option('wpb_payment_gateways',[]);
			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-setting.php';
		}

		public function get_navbar_menu() 
		{
			$wpb_add_navbar_menu= apply_filters(
				'wpb_add_navbar_menu',
				array(
					'general-tab' => array( 'text' => esc_html__( 'General', 'wpbookit' ), 'target' => 'general-home', 'is_selected' => 'false'),
					'theme-settings-tab' => array( 'text' => esc_html__( 'Theme Settings', 'wpbookit' ), 'target' => 'theme-settings', 'is_selected' => 'false'),
					'emails-tab' => array( 'text' => esc_html__( 'Emails', 'wpbookit' ), 'target' => 'emails', 'is_selected' => 'false'),
					'offline-payments-tab' => array( 'text' => esc_html__( 'Offline Payments', 'wpbookit' ), 'target' => 'offline-payments', 'is_selected' => 'false'),
					'online-payments-tab' => array( 'text' => esc_html__( 'Online Payments', 'wpbookit' ), 'target' => 'online-payments', 'is_selected' => 'false'),
					'calender-tab' => array( 'text' => esc_html__( 'Calendar feeds', 'wpbookit' ), 'target' => 'calender', 'is_selected' => 'false'),
					'custom-code-tab' => array( 'text' => esc_html__( 'Custom Code', 'wpbookit' ), 'target' => 'custom-code', 'is_selected' => 'false'),
					'shortcode-tab' => array( 'text' => esc_html__( 'Shortcode', 'wpbookit' ), 'target' => 'shortcode', 'is_selected' => 'false'),
					'import-tab' => array( 'text' => esc_html__( 'Import', 'wpbookit' ), 'target' => 'import', 'is_selected' => 'false'),
					'telemed-tab' => array('text' => esc_html__('Telemed', 'wpbookit'),'target' => 'telemed','is_selected' => 'false'),
				)
			);
			 $wpb_add_navbar_menu[($_GET['section']??'general').'-tab']['is_selected']='true'; 
			return $wpb_add_navbar_menu;
			
		}

		
		public function get_booking_type() 
		{
			return apply_filters(
				'wpb_add_booking_type',
				array(
					'registered' => esc_html__( 'Registered', 'wpbookit' ),
					'guest' => esc_html__( 'Guest', 'wpbookit' )
				)
			);
		}

		public function wpb_booking_include_exclude_tax() 
		{
			return apply_filters(
				'wpb_booking_include_exclude_tax',
				array(
					'incl' => esc_html__( 'Including tax', 'wpbookit' ),
					'excl' => esc_html__( 'Excluding tax', 'wpbookit' )
				)
			);
		}
			
		public function get_booking_options() 
		{
			return apply_filters(
				'wpb_add_booking_options',
				array(
					'name-only' => array( 'text' => esc_html__( 'Required "Name" Only', 'wpbookit' ), 'value' => 'name-only'),
					'first-last-name' => array( 'text' => esc_html__( 'Required "First Name" and "Last Name"', 'wpbookit' ), 'value' => 'first-last-name')
				)
			);
		}
		public function get_booking_header_footer_options() 
		{
			return apply_filters(
				'wpb_add_booking_header_footer_options',
				array(
					'hide-header' => array( 'text' => esc_html__( 'Hide Header', 'wpbookit' ), 'value' => true),
					'hide-footer' => array( 'text' => esc_html__( 'Hide Footer', 'wpbookit' ), 'value' => true)
				)
			);
		}


		public function get_new_booking_default() 
		{
			return apply_filters(
				'wpb_add_new_booking_default',
				array(
					'pending' => esc_html__( 'Set as Pending', 'wpbookit' ),
					'approved' => esc_html__( 'Set as Approved', 'wpbookit' )
				)
			);
		}
		
		public function get_booking_limit() 
		{
			return apply_filters(
				'wpb_add_booking_limit',
				array(
					'no-limit' => esc_html__( 'No Limit', 'wpbookit' ),
					'1' => esc_html__( '1 Booking', 'wpbookit' ),
					'2' => esc_html__( '2 Bookings', 'wpbookit' ),
					'3' => esc_html__( '3 Bookings', 'wpbookit' ),
					'4' => esc_html__( '4 Bookings', 'wpbookit' ),
					'5' => esc_html__( '5 Bookings', 'wpbookit' ),
					'6' => esc_html__( '6 Bookings', 'wpbookit' ),
					'7' => esc_html__( '7 Bookings', 'wpbookit' ),
					'8' => esc_html__( '8 Bookings', 'wpbookit' ),
					'9' => esc_html__( '9 Bookings', 'wpbookit' ),
					'10' => esc_html__( '10 Bookings', 'wpbookit' ),
					'15' => esc_html__( '15 Bookings', 'wpbookit' ),
					'20' => esc_html__( '20 Bookings', 'wpbookit' ),
					'25' => esc_html__( '25 Bookings', 'wpbookit' ),
					'50' => esc_html__( '50 Bookings', 'wpbookit' ),
				)
			);
		}

		public function get_cancellation_buffer() 
		{
			return apply_filters(
				'wpb_add_cancellation_buffer',
				array(
					'0' => esc_html__( 'No buffer', 'wpbookit' ),
					'0.25' => esc_html__( '15 minutes', 'wpbookit' ),
					'0.50' => esc_html__( '30 minutes', 'wpbookit' ),
					'0.75' => esc_html__( '45 minutes', 'wpbookit' ),
					'1' => esc_html__( '1 hour', 'wpbookit' ),
					'2' => esc_html__( '2 hours', 'wpbookit' ),
					'3' => esc_html__( '3 hours', 'wpbookit' ),
					'4' => esc_html__( '4 hours', 'wpbookit' ),
					'5' => esc_html__( '5 hours', 'wpbookit' ),
					'6' => esc_html__( '6 hours', 'wpbookit' ),
					'12' => esc_html__( '12 hours', 'wpbookit' ),
					'24' => esc_html__( '24 hours', 'wpbookit' ),
					'48' => esc_html__( '2 days', 'wpbookit' ),
					'72' => esc_html__( '3 days', 'wpbookit' ),
					'96' => esc_html__( '5 days', 'wpbookit' ),
					'144' => esc_html__( '6 days', 'wpbookit' ),
					'168' => esc_html__( '1 week', 'wpbookit' ),
					'336' => esc_html__( '2 weeks', 'wpbookit' ),
					'504' => esc_html__( '3 weeks', 'wpbookit' ),
					'672' => esc_html__( '4 weeks', 'wpbookit' ),
					'840' => esc_html__( '5 weeks', 'wpbookit' ),
					'1008' => esc_html__( '6 weeks', 'wpbookit' ),
					'1176' => esc_html__( '7 weeks', 'wpbookit' ),
					'1344' => esc_html__( '8 weeks', 'wpbookit' )
				)
			);
		}

		public function get_login_redirect() 
		{
			$all_pages = array('same-page' => esc_html__( 'Redirect the same Page', 'wpbookit' ));
			$pages = get_pages();
			foreach ($pages as $page) {
				$all_pages[$page->post_name] = $page->post_title;
			}

			return apply_filters(
				'wpb_add_login_redirect',
				$all_pages
			);
		}

		public function get_booking_booking_redirect() 
		{
			return apply_filters(
				'wpb_add_booking_booking_redirect',
				array(
					'no_redirect' => array( 'text' => esc_html__( 'No Redirect', 'wpbookit' ), 'value' => 'no_redirect'),
					'specific_page' => array( 'text' => esc_html__( 'Choose Specific page', 'wpbookit' ), 'value' => 'specific_page')
				)
			);
		}

		public function get_pre_booking_days() 
		{
			return apply_filters(
				'wpb_add_pre_booking_days',
				array(
					'5' => esc_html__( '5 days', 'wpbookit' )
				)
			);
		}

		public function get_email_options()
		{	
			return apply_filters(
				'wpb_email_options',
				array(
					'emails'     => __( 'Email', 'wpbookit' ),
					'recipient'  => __( 'Recipient(s)', 'wpbookit' ),
					'status'     => __( 'Enable/Disable', 'wpbookit' ),
					'actions'    => __( 'Action', 'wpbookit' ),
				)
			);
		}
		public function get_custom_code()
		{	
			return apply_filters(
				'wpb_custom_code',
				get_option('wpb_custom_code_data',[  'css_code' => '',  'js_code' => '' ])
			);
			
		}
		public function get_offline_payment_modes_cols()
		{	
			return apply_filters(
				'wpb_offline_payment_modes',
				array(
					'id'     => __( 'ID', 'wpbookit' ),
					'name'  => __( 'Payment Mode', 'wpbookit' ),
					'desc'  => __( 'Description', 'wpbookit' ),
					'status'     => __( 'Enable/Disable', 'wpbookit' ),
					'actions'    => __( 'Action', 'wpbookit' ),
				)
			);
			
		}


		public function get_general_setting_data()
		{	
			return wpb_get_general_settings();
		}
		
		public function get_theme_setting_data()
		{	
			return wpb_get_theme_settings('all');
		}
		
		public function remainder_interval() {
			return apply_filters('wpb_interval_time',[
				'0' => esc_html__("At Event Time", 'wpbookit'),
				'300' => esc_html__("5 Minutes Before", 'wpbookit'),       
				'900' => esc_html__("15 Minutes Before", 'wpbookit'),      
				'1800' => esc_html__("30 Minutes Before", 'wpbookit'),     
				'3600' => esc_html__("1 Hour Before", 'wpbookit'),         
				'7200' => esc_html__("2 Hours Before", 'wpbookit'),        
				'14400' => esc_html__("4 Hours Before", 'wpbookit'),       
				'86400' => esc_html__("1 Day Before", 'wpbookit'),         
				'604800' => esc_html__("1 Week Before", 'wpbookit'),       
			]);
		}
		
	}

	new WPB_Settings_Setting();
endif;
