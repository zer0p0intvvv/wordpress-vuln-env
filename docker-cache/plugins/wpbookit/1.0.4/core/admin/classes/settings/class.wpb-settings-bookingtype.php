<?php
/**
 * WPB Admin Settings Design Class
 */

if ( ! class_exists( 'WPB_Settings_BookingType', false ) ) :

	/**
	 * WPB_Settings_BookingType Class.
	 */
	class WPB_Settings_BookingType extends WPB_Setting_Page {

		function __construct() {
			$this->id        = 'booking_type';
			$this->label     = esc_html__( 'Booking Types', 'wpbookit' );
			$this->icon      = IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/booking-type-icon.svg';
			$this->type      = esc_html__( 'COMPANY', 'wpbookit' );
			$this->priority  = 40;

			parent::__construct();
		}

		public function get_base_permalink(){
			$general_setting = wpb_get_general_settings();
			$base_url = $general_setting['permalink_strcture']??'booking';
			$permalink_structure = isset($base_url) ? $base_url : 'booking';
			
			// Ensure permalink structure starts with a slash
			if (substr($permalink_structure, 0, 1) !== '/') {
				$permalink_structure = '/' . $permalink_structure;
			}

			// Ensure permalink structure ends with a slash
			if (substr($permalink_structure, -1) !== '/') {
				$permalink_structure .= '/';
			}
			return site_url('/index.php') . $permalink_structure;
		}

		public function booking_type_meeting_tools(){
			return apply_filters('wpbookit_booking_type_meeting_tools', 
				[
					'custom_link'   => esc_html__("Custom Link", 'wpbookit'),
				]
			);
		}

		public function get_settings_html() {
			$current_page =  1;
			$records_per_page = 99; 

			$pagination_args = array(
				'paged' 	=> $current_page,
				'per_page' 	=> $records_per_page,
				'staff'		=> current_user_can('administrator') ? 0 : get_current_user_id(),
			);

			$total_booking_types = wpb_get_total_booking_types();
			$total_pages 		 = ceil($total_booking_types / $records_per_page);
			$bookings_types 	 = wpb_get_all_booking_types($pagination_args);

			// $customers 	 	= $customer->get_results();	
			$meeting_tools 	= $this->booking_type_meeting_tools();

			$all_weekdays = WPBOOKIT()->helpers->wpb_get_all_weekdays();
			$directory_link = $this->get_base_permalink();
			$avalible_duration = apply_filters('wpb_booking_type_avalible_durations',[

				// translators: Minutes placeholder:0
				10 => sprintf(esc_html__("%d Minutes",'wpbookit'),10) ,
				// translators: Minutes placeholder:0
				15 => sprintf(esc_html__("%d Minutes",'wpbookit'),15) ,
				// translators: Minutes placeholder:0
				20 => sprintf(esc_html__("%d Minutes",'wpbookit'),20) ,
				// translators: Minutes placeholder:0
				25 => sprintf(esc_html__("%d Minutes",'wpbookit'),25) ,
			]);

			if($total_booking_types==0){
				include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-bookingtype-nothing.php';
				return;
			}
			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-bookingtype.php';
		}
	}

	new WPB_Settings_BookingType();
endif;
