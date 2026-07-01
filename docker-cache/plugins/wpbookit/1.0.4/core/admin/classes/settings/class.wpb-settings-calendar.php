<?php

/**
 * JT Admin Settings Design Class
 */

if (!class_exists('WPB_Settings_Calendar', false)):

	/**
	 * WPB_Settings_calender Class.
	 */
	class WPB_Settings_Calendar extends WPB_Setting_Page
	{
		public $per_page;
		public $id;

		function __construct()
		{
			$this->id 			= 'calendar';
			$this->label 		= esc_html__('Calendar', 'wpbookit');
			$this->icon 		= IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/booking-icon.svg';
			$this->priority     = 20;
			$this->type         = esc_html__( 'COMPANY', 'wpbookit' );

			parent::__construct();
		}

		public function get_bookings( $current_page = 1 ) {
			return    (object)['results'=>[],'total'=>25,'maxnumpages'=>10];
			// return wpb_get_bookings(
			// 	apply_filters(
			// 		'wpb_bookings_table_bookings_data',
			// 		array(
			// 			'paged' 	=> $current_page,
			// 			'per_page' 	=> $this->per_page,
			// 			'orderby' 	=> 'ID',
			// 			'order'   	=> 'DESC',
			// 		)
			// 	)
			// );
		}

		public function get_settings_html() {
			$current_page 			= $_GET['paged'] ?? 1;
			// $columns 				= self::get_table_column();

			$statuses 				= wpb_get_booking_statuses();
			$payment_statuses 		= wpb_get_booking_payment_statuses();
			$booking_types 			= wpb_get_all_booking_types(
				array(
					'staff'	=> current_user_can('administrator') ? 0 : get_current_user_id(),
					"per_page" => 99
				)
			);

			$payment_modes 			= wpb_get_booking_modes();
			$customers 				= wpb_get_customers();

			$bookings 				= $this->get_bookings( $current_page );
			
			$CustomersObj        	= new WPB_Settings_Customer();
			$CustomerEntry			= $CustomersObj->get_all_customers();
			$all_customers         	= $CustomerEntry->get_results();
			

			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-calendar.php';
		}
	}

	new WPB_Settings_Calendar();
endif;
