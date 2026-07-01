<?php

/**
 * JT Admin Settings Design Class
 */

if (!class_exists('WPB_Settings_Bookings', false)):

	/**
	 * WPB_Settings_calender Class.
	 */
	class WPB_Settings_Bookings extends WPB_Setting_Page
	{
		public $per_page;
		public $id;

		function __construct()
		{
			$this->id 			= 'bookings';
			$this->label 		= esc_html__('Bookings', 'wpbookit');
			$this->icon 		= IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/calender-icon.svg';
			$this->per_page 	= 5;
			$this->type         = esc_html__( 'COMPANY', 'wpbookit' );
			$this->priority     = 30; 

			parent::__construct();
		}

		public static function get_table_column() {
			return apply_filters(
				'wpb_bookings_columns',
				array(
					'booking-name' 				=> esc_html__( 'Customer', 'wpbookit' ),
					'booking-date'    			=> esc_html__( 'Date and Time', 'wpbookit' ),
					'booking-type'  			=> esc_html__( 'Type', 'wpbookit' ),
					'booking-duration'  		=> esc_html__( 'Duration', 'wpbookit' ),
					'booking-price'  			=> esc_html__( 'Total Price', 'wpbookit' ),
					'booking-status' 			=> esc_html__( 'Status', 'wpbookit' ),
					'booking-actions' 			=> esc_html__( 'Actions', 'wpbookit' ),
				)
			);
		}

		public function get_bookings( $current_page = 1 ) {
			return (object)['results'=>[],'total'=>25,'maxnumpages'=>10];
			
		}
	

		public function get_settings_html() {
			$current_page 			= $_GET['paged'] ?? 1;
			$columns 				= self::get_table_column();

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

			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-bookings.php';
		}
	}

	new WPB_Settings_Bookings();
endif;
