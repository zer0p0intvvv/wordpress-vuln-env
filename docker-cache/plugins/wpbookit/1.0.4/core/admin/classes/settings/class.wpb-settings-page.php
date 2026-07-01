<?php
/**
 * WPBookit Admin Settings Class
 *
 * @package  WPBookit\Admin
 * @version  3.4.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPB_Setting_Page', false ) ) :


	/**
	 * WPB_Setting_Page Class.
	 */
	abstract class WPB_Setting_Page {

		protected $id       = '';
		protected $label    = '';
		protected $icon     = IQWPB_PLUGIN_PATH . '/core/admin/assets/images/menu-icons/default-icon.svg';
		protected $settings = '';
		protected $type 	= '';
		protected $paged 	= '';
		protected $users_per_page = 1;
		protected $priority = 10; // Default priority
		

		public function __construct() {
			$this->get_settings();
			add_filter( 'wpb_settings_tabs_array', array( $this, 'add_settings_page' ), 99, 1 );
			add_action( 'wpb_settings_' . $this->id, array( $this, 'get_settings_html' ) );
		}

		public function get_id() {
			return $this->id;
		}

		public function get_label() {
			return $this->label;	
		}

		public function get_settings() {
			return $this->settings = get_option( 'wpb_settings' );
		}

		public function add_settings_page( $pages ) {
			$pages[ $this->id ] = [
				'label' 	=> $this->label,
				'icon'  	=> file_get_contents($this->icon), // phpcs:ignore   WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents 
				'priority' 	=> $this->priority, // Include priority
				'type' 		=> $this->type, 
			];
			return $pages;
		}

		public function get_field( $field, $default = '' ) {
			if ( isset( $this->settings[ $field ] ) && $field ) :
				return $this->settings[ $field ];
			endif;
			return $default;
		}
    }

endif;