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

if ( ! class_exists( 'WPB_Admin_Settings', false ) ) :


	/**
	 * WPB_Admin_Settings Class.
	 */
	class WPB_Admin_Settings {

        /**
		 * Setting pages.
		 *
		 * @var array
		 */
        private static $settings = array();

        /**
		 * Include the settings page classes.
		 */
        public static function get_settings_pages() {
            if ( empty( self::$settings ) ) :
				$settings = array();
				
				$settings[] = include_once 'settings/class.wpb-settings-page.php';
				$settings[] = include_once 'settings/class.wpb-settings-dashboard.php';
				$settings[] = include_once 'settings/class.wpb-settings-calendar.php';
				$settings[] = include_once 'settings/class.wpb-settings-bookings.php';
				$settings[] = include_once 'settings/class.wpb-settings-bookingtype.php';
				$settings[] = include_once 'settings/class.wpb-settings-customer.php';
				$settings[] = include_once 'settings/class.wpb-settings-guest.php';
				$settings[] = include_once 'settings/class.wpb-settings-guest.php';
				$settings[] = include_once 'settings/class.wpb-settings-setting.php';

				self::$settings = apply_filters( 'wpb_get_settings_pages', $settings );
            endif;
            return self::$settings;
        }
		
		public static function load_assets() {
			//Load admin assets here
		}

		public static function get_settings_tabs_array() {
			$settings_pages = self::get_settings_pages();
			$tabs = array();

			foreach ( $settings_pages as $page ) {
				if ( is_object( $page ) && method_exists( $page, 'get_id' ) && method_exists( $page, 'get_label' ) ) {
					$tabs[ $page->get_id() ] = array(
						'label'    => $page->get_label(),
					);
				}
			}

			return apply_filters( 'wpb_settings_tabs_array', $tabs );
		}

		public static function output() {
			$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
			do_action( 'wpb_settings_start' );

			self::load_assets();

			// Get tabs for the settings page.
			$tabs = apply_filters( 'wpb_settings_tabs_array', array() );
			uasort($tabs, function ($a, $b) {
				return ($a['priority']??0) - ($b['priority']??0);
			});
			
			foreach ($tabs as $slug=>$tab) {
				$type = $tab['type'];
				if (!isset($groupedByTab[$type])) {
					$groupedByTab[$type] = [];
				}
				$groupedByTab[$type][$slug] = $tab;
			}
			$tabs=$groupedByTab;

		
			include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings.php';
		}

		public static function show_messages() {
			if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
				<div id="message" class="updated inline"><p><strong><?php esc_html_e( 'Your settings have been saved.', 'wpbookit' ); ?></strong></p></div>
				<?php
			endif;
		}

    }

endif;