<?php
/**
 * WPB Admin Settings Design Class
 */

if ( ! class_exists( 'WPB_Settings_Customer', false ) ) :

	/**
	 * WPB_Settings_Customer Class.
	 */
	class WPB_Settings_Customer extends WPB_Setting_Page {
		public $paged;
		public $per_page;

		function __construct() {
			$this->id    		= 'customer';
			$this->label 		= __( 'Customers', 'wpbookit' );
			$this->icon 		= IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/customers.svg';
			$this->per_page 	= 5;
			$this->paged 		= $_GET['paged'] ?? 1;
			$this->priority     = 50;
			$this->type         = esc_html__( 'USER', 'wpbookit' );
			
			if( current_user_can( 'administrator' ) ) :
				parent::__construct();
			endif;
		}

		public function get_settings_html() {
			$columns 			= $this->get_table_column();
			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-customer.php';
		}
		
		public function get_table_column() {
			return apply_filters(
				'wpb_add_customer_columns',
				array(
					'customer-id'  			=> esc_html__( 'ID', 'wpbookit' ),
					'customer-name'  		=> esc_html__( 'Customer', 'wpbookit' ),
					'customer-dob'    		=> esc_html__( 'Date of Birth', 'wpbookit' ),
					'customer-phone'    	=> esc_html__( 'Phone', 'wpbookit' ),
					'customer-gender'   	=> esc_html__( 'Gender', 'wpbookit' ),
					'customer-date-time'    => esc_html__( 'Created', 'wpbookit' ),
					'customer-actions' 		=> esc_html__( 'Action', 'wpbookit' ),
				)
			);
		}
		
		public function get_customers( $paged ){
			return new WP_User_Query( 
				apply_filters(
					'wpb_get_customers_query_args',
					array(
						'role'    => WPBOOKIT()->helpers->get_customer_role(),
						'orderby' => 'ID',
						'order'   => 'DESC',
						'number'  => $this->per_page,
						'paged'   => $this->paged ,
					) 
				)
			);
		}
		public function get_all_customers(){
			return new WP_User_Query( 
				apply_filters(
					'wpb_get_all_customers_query_args',
					array(
						'role'    => WPBOOKIT()->helpers->get_customer_role(),
						'orderby' => 'ID',
						'order'   => 'DESC',
					) 
				)
			);
		}
		
		public function get_customers_count($start_date, $end_date){
			// Convert dates to MySQL date format 
			$start_date = date('Y-m-d', strtotime($start_date)); // phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date 
			$end_date = date('Y-m-d', strtotime($end_date)); // phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date 
	
			// Query users
			$args = array(
				'role' => WPBOOKIT()->helpers->get_customer_role(),
				'date_query' => array(
					array(
						'after' => $start_date,
						'before' => $end_date,
						'inclusive' => true,
						'column' => 'user_registered',
					),
				),
				'fields' => 'ID',
			);
	
			$user_query = new WP_User_Query($args);
			// Return count
			return $user_query->get_total();
		}
	
		
	}

	new WPB_Settings_Customer();
endif;
