<?php
/**
 * WPB Admin Settings Design Class
 */

if ( ! class_exists( 'WPB_Settings_Guest', false ) ) :

	/**
	 * WPB_Settings_Guest Class.
	 */
	class WPB_Settings_Guest extends WPB_Setting_Page {
		public $paged;
		public $per_page;

		function __construct() {
			$this->id    		= 'guest-users';
			$this->label 		= esc_html__( 'Guest Users', 'wpbookit' );
			$this->icon 		= IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/guest-users.svg';
			$this->per_page 	= 5;
			$this->paged 		= $_GET['paged'] ?? 1;
			$this->priority     = 70;
			$this->type         = esc_html__( 'USER', 'wpbookit' );
			
			if( current_user_can( 'administrator' ) ) :
				parent::__construct();
			endif;
		}

		public function get_settings_html() {
			$columns 			= $this->get_table_column();
			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-guest.php';
		}
		
		public function get_table_column() {
			return apply_filters(
				'wpb_add_guest_columns',
				array(
					'guest-id'  			=> esc_html__( 'ID', 'wpbookit' ),
					'guest-name'  		    => esc_html__( 'Guest Name', 'wpbookit' ),
					'guest-email'  		    => esc_html__( 'Guest Email', 'wpbookit' ),
					'guest-phone-number'  		    => esc_html__( 'Guest Phone Number', 'wpbookit' ),
					'guest-actions' 		=> esc_html__( 'Action', 'wpbookit' ),
				)
			);
		}
	
		
	}

	new WPB_Settings_Guest();
endif;
