<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This is the main class that is responsible for registering
 * the core functions, including the files and setting up all features. 
 * 
 * To add a new class, here's what you need to do: 
 * 1. Add your new class within the following folder: core/includes/classes
 * 2. Create a new variable you want to assign the class to (as e.g. public $helpers)
 * 3. Assign the class within the instance() function ( as e.g. self::$instance->helpers = new Wpbookit_Helpers();)
 * 4. Register the class you added to core/includes/classes within the includes() function
 * 
 * HELPER COMMENT END
 */

if ( ! class_exists( 'Wpbookit' ) ) :

	/**
	 * Main Wpbookit Class.
	 *
	 * @package		WPBOOKIT
	 * @subpackage	Classes/Wpbookit
	 * @since		1.0.4
	 * @author		Iqonic Design
	 */
	final class Wpbookit {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.4
		 * @var        null|Wpbookit
		 */
		private static null|Wpbookit $instance = null;

		/**
		 * WPBOOKIT helpers object.
		 *
		 * @access	public
		 * @since	1.0.4
		 * @var		WPB_Helpers
		 */
		public WPB_Helpers $helpers;

		/**
		 * WPBOOKIT settings object.
		 *
		 * @access	public
		 * @since	1.0.4
		 * @var		WPB_Settings
		 */
		public WPB_Settings $settings;

		/**
		 * WPBOOKIT template object.
		 *
		 * @access	public
		 * @since	1.0.4
		 * @var		object|Wpbookit_Template
		 */
		public $template;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.4
		 * @return	void
		 */

		 public $shortcode;

		 /**
		  * WPBOOKIT template object.
		  *
		  * @access	public
		  * @since	1.0.4
		  * @var		object|Wpbookit_Template
		  */

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to clone this class.', 'wpbookit' ), '1.0.4' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.4
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to unserialize this class.', 'wpbookit' ), '1.0.4' );
		}

		/**
		 * Main Wpbookit Instance.
		 *
		 * Insures that only one instance of Wpbookit exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.4
		 * @static
		 * @return		Wpbookit	The one true Wpbookit
		 */
		public static function instance(): Wpbookit{
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wpbookit ) ) {
				self::$instance					= new Wpbookit;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new WPB_Helpers();
				self::$instance->settings		= new WPB_Settings(); 
				
				new WPB_Backend();


				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'wpb_plugin_loaded' );
			}

			return self::$instance;
		}

		public static function activate() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wpbookit ) ) {
				self::$instance					= new Wpbookit;
			}

			self::$instance->createShortcodePage();
		}

		/**
		 * Include required files.
		 *  
		 * @access  private
		 * @since   1.0.4
		 * @return  void
		 */
		private function includes() {
			/**
			 * Abstract classes.
			 */
			include_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-cache-handler.php';

			// Abstract Classes 
			include_once IQWPB_PLUGIN_PATH . 'core/includes/abstracts/abstract-wpb-data.php';
			include_once IQWPB_PLUGIN_PATH . 'core/includes/abstracts/abstract-wpb-import.php';
			
			include_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-booking.php';
			include_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class-wpb-guest-users.php';
			include_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-booking-type.php';

			include_once IQWPB_PLUGIN_PATH . 'core/includes/wpb-core-functions.php';
			require_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-helpers.php';
			require_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-settings.php';
			require_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-install.php';
			
			include_once IQWPB_PLUGIN_PATH . 'core/includes/wpb-template-functions.php';
			include_once IQWPB_PLUGIN_PATH . 'core/includes/wpb-template-hooks.php';

			require_once IQWPB_PLUGIN_PATH . 'core/admin/classes/class.wpb-admin-settings.php';
			require_once IQWPB_PLUGIN_PATH . 'core/admin/classes/class.wpb-admin.php';

			require_once IQWPB_PLUGIN_PATH . 'core/admin/classes/class.wpb-admin-routes.php';
			require_once IQWPB_PLUGIN_PATH . 'core/admin/classes/class.wpb-admin-routes-handler.php';
			
			require_once IQWPB_PLUGIN_PATH . 'core/shortcodes/class-wpbookit-shortcode.php';
			include_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class-wpb-permalink-handler.php';
			include_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class-wpb.booking-cancellation.php';

			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			require_once IQWPB_PLUGIN_PATH . 'core/includes/classes/import/class-wpb-csv-import.php';

		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.4
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'admin_init', array( self::$instance, 'load_textdomain' ) );

			add_action('admin_notices',  array( self::$instance, 'iqonic_sale_banner_notice' ) );
			add_action('wp_ajax_iq_dismiss_notice',  array( self::$instance, 'iq_dismiss_notice' ) );

			// Register the hooks
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.4
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'wpbookit', false, dirname( plugin_basename( IQWPB_PLUGIN_FILE ) ) . '/languages/' );
		}

		public function createShortcodePage(){
			$pages = apply_filters(
				'wpb_create_pages',
				array(
					'customer_profile'       => array(
						'name'    => _x( 'customer-profile', 'Page slug', 'wpbookit' ),
						'title'   => _x( 'Customer Profile', 'Page title', 'wpbookit' ),
						'content' => '<!-- wp:shortcode -->[wpb-profile]<!-- /wp:shortcode -->',
						'search_value' => '[wpb-profile]'
					),
					'register_login'       => array(
						'name'    => _x( 'register-login', 'Page slug', 'wpbookit' ),
						'title'   => _x( 'Register Login user', 'Page title', 'wpbookit' ),
						'content' => '<!-- wp:shortcode -->[wpb-login]<!-- /wp:shortcode -->',
						'search_value' => '[wpb-login]'
					),
				)
			);
	
			foreach ( $pages as $key => $page ) {
				self::wpb_create_page(
					esc_sql( $page['name'] ),
					'wpb_' . $key . '_page_id',
					$page['title'],
					$page['content'],
					! empty( $page['post_status'] ) ? $page['post_status'] : 'publish',
					$page['search_value']
				);
			}
		}

		public static function wpb_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_status = 'publish',$searchValue ='' ) {
			global $wpdb;
	
			$option_value = get_option( $option );
	
			if ( $option_value > 0 ) {
				$page_object = get_post( $option_value );
				if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
					// Valid page is already in place.
					return;
				}
			}
	
			if ( strlen( $searchValue ) > 0 ) {
				// Search for an existing page with the specified page content (typically a shortcode).
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$searchValue}%" ) );
				if(!empty($valid_page_found)){
					update_option( $option, $valid_page_found );
					return ;
				}
			}
	
	
			// Search for a matching valid trashed page.
			if ( strlen( $searchValue ) > 0 ) {
				// Search for an existing page with the specified page content (typically a shortcode).
				$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$searchValue}%" ) );
			} else {
				// Search for an existing page with the specified page slug.
				$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
			}
	
			if ( !empty($trashed_page_found) ) {
				$page_id   = $trashed_page_found;
				$page_data = array(
					'ID'          => $page_id,
					'post_status' => $post_status,
				);
				wp_update_post( $page_data );
			} else {
				$page_data = array(
					'post_status'    => $post_status,
					'post_type'      => 'page',
					'post_author'    => get_current_user_id(),
					'post_name'      => $slug,
					'post_title'     => $page_title,
					'post_content'   => $page_content,
					'comment_status' => 'closed',
				);
				$page_id   = wp_insert_post( $page_data );
			}
	
			update_option( $option, $page_id );
	
			return;
		}

		public function iqonic_sale_banner_notice()
		{
			$type="plugins";
			$product="wpbookit"; 
			$get_sale_detail= get_transient('iq-notice');
			if(is_null($get_sale_detail) || $get_sale_detail===false ){
				$get_sale_detail = wp_remote_get("https://assets.iqonic.design/wp-product-notices/notices.json?ver=" . wp_rand()) ;
				set_transient('iq-notice',$get_sale_detail ,3600)  ;
			}
	
			if (!is_wp_error($get_sale_detail) && $content = json_decode(wp_remote_retrieve_body($get_sale_detail), true)) {
				if(get_user_meta(get_current_user_id(),$content['data']['notice-id'],true)) return;
				
				$currentTime =  current_datetime();
				if (($content['data']['start-sale-timestamp']  < $currentTime->getTimestamp() && $currentTime->getTimestamp() < $content['data']['end-sale-timestamp'] )&& isset($content[$type][$product])){
	
				?>
				<div class="iq-notice notice notice-success is-dismissible" style="padding: 0;">
					<a target="_blank" href="<?php echo esc_url($content[$type][$product]['sale-ink']??"#")  ?>">
						<img src="<?php echo esc_url($content[$type][$product]['banner-img'] ??"#" )  // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" style="object-fit: contain;padding: 0;margin: 0;display: block;" width="100%" alt="">
					</a>
					<input type="hidden" id="iq-notice-id" value="<?php echo esc_html($content['data']['notice-id']) ?>">
					<input type="hidden" id="iq-notice-nounce" value="<?php echo esc_attr(wp_create_nonce('iq-dismiss-notice')) ?>">
				</div>
				<?php
						wp_enqueue_script('iq-admin-notice', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/js/iq-admin-notice.js', [], IQWPB_VERSION);
				}
			}
		}
		public function iq_dismiss_notice() {
			if(wp_verify_nonce($_GET['nounce'],'iq-dismiss-notice')){
				update_user_meta(get_current_user_id(),$_GET['key'],1);
			}
		}
	}

endif; // End if class_exists check.