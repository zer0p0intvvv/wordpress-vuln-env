<?php
/**
 *
 * Class for subscribe
 *
 * @author Sidney van de Stouwe <sidney@vandestouwe.com>
 * @package subscribe-to-category
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( class_exists( 'STC_Subscribe' ) ) {
	$stc_subscribe = new STC_Subscribe();
}


/**
 *
 * STC Subscribe class
 */
class STC_Subscribe {

	protected static $instance = null;
	private $data = array();
	private $error = array();
	private $notice = array();
	private $settings = array();
	private $post_type = 'stc';
	private $sleep_flag = 25;
	private $show_all_categories = true;
        private $subscribed_cats = array();
        private $stcEntryPresent = false;
        private $taxonomies = array();
        private $currentIndex = 0;
        private $customPostTypes = array();
        private $textmagic = null;
        private $api_textmagic = null;
        private $possible_areas = array( 0 => array('name' => "Title", 'status' => "checked", 'local_name' => ""),
                                         1 => array('name' => "Content", 'status' => "checked", 'local_name' => "" ),
                                         2 => array('name' => "Tags", 'status' => "checked", 'local_name' => "" ),
                                         3 => array('name' => "Taxonomies", 'status' => "checked", 'local_name' => ""),
                                  );
        private $possible_moments = array( 0 => array('name' => "Sun", 'status' => "", 'local_name' => ""),
                                           1 => array('name' => "Mon", 'status' => "", 'local_name' => ""),
                                           2 => array('name' => "Tue", 'status' => "", 'local_name' => "" ),
                                           3 => array('name' => "Wed", 'status' => "", 'local_name' => "" ),
                                           4 => array('name' => "Thu", 'status' => "", 'local_name' => ""),
                                           5 => array('name' => "Fri", 'status' => "", 'local_name' => ""),
                                           6 => array('name' => "Sat", 'status' => "", 'local_name' => ""),
                                           7 => array('name' => "Daily", 'status' => "", 'local_name' => ""),
                                           8 => array('name' => "Hourly", 'status' => "", 'local_name' => ""),
                                           9 => array('name' => "STC", 'status' => "checked", 'local_name' => ""),
                                  );
        /**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
            $this->init();
	}

	/**
	 * Single instance of this class.
	 *
	 * @since  1.0.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Init method
	 *
	 * @since  1.0.0
	 */
	private function init() {
                global $wpdb;
                
                $table_name = $wpdb->prefix . "stc_joblist";
                if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ||
                     $wpdb->get_var( $wpdb->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = %s AND column_name = 'mobile_phone'", $table_name )) != 'mobile_phone') {
                        $wpdb->query("DROP TABLE IF EXISTS $table_name;");
                        $sql = "CREATE TABLE `". $wpdb->dbname . "`.`". $table_name . "` ( ";
                        $sql .= "`ID` bigint(20) NOT NULL auto_increment, ";
                        $sql .= "`day` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`subscriber_id` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`mobile_phone` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`mobile_phone_status` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`hash` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`email` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`post_id` bigint(20) NOT NULL, ";
                        $sql .= "`reason` text COLLATE utf8mb4_unicode_ci NOT NULL, ";
                        $sql .= "`merged` TINYINT(1) NOT NULL, ";
                        $sql .= "PRIMARY KEY `order_id` (`ID`), "; 
                        $sql .= "KEY `Days` (`day`(10)) "; 
                        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
                        dbDelta($sql);
                }
                
                // add the locale names to the possible areas structure
                $this->possible_areas[0]['local_name'] = __( 'Title', 'subscribe-to-category' );
                $this->possible_areas[1]['local_name'] = __( 'Content', 'subscribe-to-category' );
                $this->possible_areas[2]['local_name'] = __( 'Tags', 'subscribe-to-category' );
                $this->possible_areas[3]['local_name'] = __( 'Taxonomies', 'subscribe-to-category' );
                $this->possible_moments[0]['local_name'] = __( 'Sun', 'subscribe-to-category' );
                $this->possible_moments[1]['local_name'] = __( 'Mon', 'subscribe-to-category' );
                $this->possible_moments[2]['local_name'] = __( 'Tue', 'subscribe-to-category' );
                $this->possible_moments[3]['local_name'] = __( 'Wed', 'subscribe-to-category' );
                $this->possible_moments[4]['local_name'] = __( 'Thu', 'subscribe-to-category' );
                $this->possible_moments[5]['local_name'] = __( 'Fri', 'subscribe-to-category' );
                $this->possible_moments[6]['local_name'] = __( 'Sat', 'subscribe-to-category' );
                $this->possible_moments[7]['local_name'] = __( 'Daily', 'subscribe-to-category' );
                $this->possible_moments[8]['local_name'] = __( 'Hourly', 'subscribe-to-category' );
                $this->possible_moments[9]['local_name'] = __( 'STC', 'subscribe-to-category' );
                                               
		// save settings to array.
		$this->settings = get_option( 'stc_settings' );
                
		add_action( 'init', array( $this, 'register_post_type' ), 99 );
		add_action( 'create_category', array( $this, 'update_subscriber_categories' ) );

		add_action( 'wp', array( $this, 'collect_get_data' ),01);
		add_action( 'wp', array( $this, 'collect_post_data' ),01);

		add_action( 'save_post_stc', array( $this, 'save_post_stc' ) );
		add_action( 'admin_notices', array( $this, 'save_post_stc_error' ) );

		add_shortcode( 'stc-subscribe', array( $this, 'stc_subscribe_render' ) );
		add_shortcode( 'stc-subscribe-to-post', array( $this, 'stc_subscribe_render_to_post' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 90, 2 );

		add_action( 'stc_schedule_email', array( $this, 'stc_send_email'), 10, 1 );
		add_action( 'stc_schedule_email_daily', array( $this, 'stc_send_email' ), 10, 1);

		// adding checkbox to publish meta box if activated (hook only works with the classic editor).
		if ( isset( $this->settings['resend_option'] ) && '1' === $this->settings['resend_option'] ) {
			add_action( 'post_submitbox_misc_actions', array( $this, 'resend_post_option' ) );
			add_action( 'post_submitbox_misc_actions', array( $this, 'notsend_post_option' ) );
		}

		// react on updates from the gutenberg document panel.
		add_action( 'updated_post_meta', array( $this, 'stc_gutenberg_addon_updated_post_meta' ), 10, 4 );
                
//                add_filter( 'stc_filter_wp_mail', array( $this, 'stc_wp_mail_filtering' ), 10, 4);

                add_filter('manage_notifications_posts_columns', array( $this, 'set_notification_table_columns'));
                add_action('manage_notifications_posts_custom_column' , array( $this, 'custom_notifications_column'), 10, 2 );
                add_filter('manage_stc_posts_columns', array( $this, 'set_book_table_columns'));
                add_action('manage_stc_posts_custom_column' , array( $this, 'custom_stc_column'), 10, 2 );
               
                add_action('wp_ajax_stc_get_results', array($this, 'stc_get_results_callback'));
                add_action('wp_ajax_nopriv_stc_get_results', array($this, 'stc_get_results_callback'));

                add_action('wp_ajax_stc_get_results_treeview', array($this, 'stc_get_results_callback'));
                add_action('wp_ajax_nopriv_stc_get_results_treeview', array($this, 'stc_get_results_treeview_callback'));
                
                // link STC into the query post filter
                if (isset($this->settings['post_filter_query'])) add_action( explode('|', $this->settings['post_filter_query'])[0], array( $this, 'link_into_smart_filter_query' ), 20, 2 );
                
                //connect to the TextMagic API Interface
                if (isset($this->settings['enable_sms_notification']) && '1' === $this->settings['enable_sms_notification']) $this->textmagic = STC_SMSNotification::get_instance();
                if (isset($this->settings['enable_sms_notification']) && '1' === $this->settings['enable_sms_notification']) $this->api_textmagic = $this->textmagic->configTextMagic();                
        }
        
//        public function stc_wp_mail_filtering($email_address, $email_subject, $message, $headers) {
//            return $email_address;
//        }
        
	/**
	 * Hook to handle the event from the gutenberg panel
	 *
	 * @since  2.0.0
	 *
	 * @param integer $meta_id The ID into the post_meta table.
	 * @param integer $post_id The ID into the post tabel.
	 * @param string  $meta_key The name of the key.
	 * @param value   $meta_value The value of the key.
	 */
	public function stc_gutenberg_addon_updated_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
		// this funtion is for the document panel STC drop down only else return if not.
		if ( ! isset( $this->settings['exclude_gutenberg'] ) || '1' != $this->settings['exclude_gutenberg'] ) {
			return;}
		// only react on the checkbox request from.
                switch ($meta_key) {
                    case '_stc_notifier_prevent' :
                        if ($meta_value) {
                                update_post_meta( $post_id, '_stc_notifier_prevent', '1' );   
                                update_post_meta( $post_id, '_stc_notifier_status', 'prevent');
                        } else {
                                update_post_meta( $post_id, '_stc_notifier_prevent', '0' );
                        }
                        break;
                    case '_stc_notifier_request' :
                        if ($meta_value) {
                                update_post_meta( $post_id, '_stc_notifier_status', 'outbox' );
                        }
                        update_post_meta( $post_id, '_stc_notifier_request', '0' );
                        break;
                }
                return;
	}

	/**
	 * Adding checkbox to publish meta box with an option to resend a post
	 * This function is not called when the gutenberg editor is acttive
	 *
	 * @since 1.2.0
	 */
	public function resend_post_option() {
		global $post;
		$stc_status = get_post_meta( $post->ID, '_stc_notifier_status', true );

		// We wont show resend option on a post that hasn´t been sent.
		if ( 'sent' != $stc_status ) {
			return false;
		}

		$time_in_seconds_i18n = strtotime( date_i18n( 'Y-m-d H:i:s' ) ) + STC_Settings::get_next_cron_time( 'stc_schedule_email' );
		$next_run = gmdate( 'Y-m-d H:i:s', $time_in_seconds_i18n );
		?>
			<div class="misc-pub-section stc-section">
			<span class="dashicons dashicons-groups"></span><label> <input id="stc-resend" type="checkbox" name="stc_resend"><?php esc_html_e( 'Resend post to subscribers', 'subscribe-to-category' ); ?></label>
			<div id="stc-resend-info" style="display:none;">
			  <?php /* translators: %s: time of send mail, number of post to send */ ?>
			<p><i><?php printf( esc_html__( 'This post update will be re-sent to subscribers %s', 'subscribe-to-category' ), esc_attr( $next_run ) ); ?></i></p>
			</div>
			</div>
		<?php
	}

	/**
	 * Adding checkbox to publish meta box with an option to resend a post
	 * This function is not called when the gutenberg editor is acttive
	 *
	 * @since 1.2.0
	 */
	public function notsend_post_option() {
		global $post;
		$stc_status = get_post_meta( $post->ID, '_stc_notifier_prevent', true );
                if ($stc_status === "1") { $selected = "selected"; } else { $selected = ""; }
		?>
			<div class="misc-pub-section stc-section">
                            <span class="dashicons dashicons-groups"></span><label> <input id="stc-notsend" value="1" type="checkbox" name="stc_notsend" <?php checked( '1', $stc_status); ?> ><?php esc_html_e( 'Do not mail this blog', 'subscribe-to-category' ); ?></label>
			</div>
		<?php
	}

        /**
	 * Adding a newly created category to subscribers who subscribes to all categories
	 *
	 * @since  1.0.0
	 *
	 * @param integer $category_id The id for newly created category.
	 */
	public function update_subscriber_categories( $category_id ) {

		$args = array(
			'post_type' => 'stc',
			'post_status' => 'publish',
			'meta_key' => '_stc_all_categories',
			'meta_value' => '1',
		);

		$subscribers = get_posts( $args );

		if ( ! empty( $subscribers ) ) {
			foreach ( $subscribers as $s ) {

				$categories = $s->post_category;
				$categories[] = $category_id;

				$post_data = array(
					'ID' => $s->ID,
					'post_category' => $categories,
				);

				wp_update_post( $post_data );
			}
		}
	}

        /**
	 * Method for printing result of email conformation to landing page or custom page
	 *
	 * @since  2.5.5
	 */
	public function unsubscribe_html() {
		global $post;
                
                if (is_null(get_page_by_path("stcs-landing-page"))) {
                        get_header();
                        ?>
                                <div id="stc-unsubscribe-wrapper" class="">
                                  <div class="alert alert-success text-center">
                                        <p><?php echo esc_html( $this->notice[0] ); ?></p>
                                        <p><a href="<?php echo esc_html( get_bloginfo( 'url' ) ); ?>"><?php esc_html_e( 'Take me to start page', 'subscribe-to-category' ); ?></a></p>
                                  </div>
                                </div>
                        <?php
                        get_footer();
                        exit;
                } else {
                        wp_redirect( get_home_url().  "/stcs-landing-page?notice=". $this->notice[0]);
                        exit;
                }
        }

	/**
	 * Collecting data through _GET
	 *
	 * @since  1.0.0
	 */
	public function collect_get_data() {

		if ( empty( $_GET ) ) {
			return false;
		}

                // Unsubscription confirmation.
		if ( isset( $_GET['stc_unsubscribe'] ) && strlen( sanitize_key( $_GET['stc_unsubscribe'] ) ) === 32 && ctype_xdigit( sanitize_key( $_GET['stc_unsubscribe'] ) ) ) {
			if ( isset( $_GET['stc_user'] ) && is_email( $_GET['stc_user'] ) ) {
				$this->unsubscribe_user();
				add_action( 'template_redirect', array( $this, 'unsubscribe_html' ) );
			}
		}

		// Subscription confirmation.
		if ( isset( $_GET['stc_subscribe'] ) && strlen( sanitize_key( $_GET['stc_subscribe'] ) ) === 32 && ctype_xdigit( sanitize_key( $_GET['stc_subscribe'] ) ) ) {
			if ( isset( $_GET['stc_user'] ) && is_email( $_GET['stc_user'] ) ) {
				$this->subscribe_user();
                                add_action( 'template_redirect', array( $this, 'unsubscribe_html' ) );
			}
		}
		if ( isset( $_GET['notice'] )) {
                        global $post;
                        $content = str_replace("{{stc-notice}}", $_GET['notice'], $post->post_content);
                        $post->post_content = $content;
		}
	}

	/**
	 * Subscribe user from subscription
	 *
	 * @TODO: add contact email if something went wrong
	 *
	 * @since  1.0.0
	 */
	private function subscribe_user() {
		global $wpdb;
		$meta_key = '_stc_hash';
		if ( isset( $_GET['stc_subscribe'] ) ) {
			$meta_value = sanitize_key( $_GET['stc_subscribe'] );
		}
		if ( isset( $_GET['stc_user'] ) ) {
			$stc_user = strtolower( $_GET['stc_user'] );
		}

		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $wpdb->posts AS post
                                LEFT JOIN $wpdb->postmeta AS meta ON post.ID = meta.post_id
                                WHERE meta.meta_key = %s AND meta.meta_value = %s
                                AND post.post_type = %s
                                AND post.post_title = %s",
				$meta_key,
				$meta_value,
				$this->post_type,
				$stc_user
			)
		);

		if ( empty( $user_id ) ) {
			$notice[] = __( 'We are sorry but something went wrong with activating your subscription.', 'subscribe-to-category' );
			$this->notice = $notice;
			return $notice;
		}
                
                // some chache configurations are triggering STC twice for unknown reasons 
                // so check if we are not processing the same url twice so we check the last time this subscriber executed a confirmation
                $TimeLastUsed = get_post_meta( $user_id, '_stc_url_blocked', true);
                if (($TimeLastUsed + 3)  > time()) {
			$notice[] = get_post_meta( $user_id, '_stc_last_notice', true);
			$this->notice = $notice;
			return $notice;
                } else {
                        update_post_meta( $user_id, '_stc_url_blocked', time());
                }

		// hook right before setting post status to publish.
		do_action( 'stc_before_subscribe', $user_id );
                
                if ((get_post_status($user_id) != 'approval') && (get_post_status($user_id) != 'update_approval')) {
        		/* translators: %s: email from the subscriber */
        		$notice[] = sprintf( __( 'Confirmation email %s is not in the [update_]approval state', 'subscribe-to-category' ), $stc_user );
                } else if (get_post_status($user_id) == 'update_approval') {
                        // activate the requested updated categories and the state to publish
                        $cats = get_post_meta($user_id, '_stc_cats', true);
                        $mobile_phone = get_post_meta($user_id, '_stc_mobile_phone', true);
                        update_post_meta( $user_id, '_stc_subscriber_mobile_phone', $mobile_phone);
                        $keywords = get_post_meta($user_id, '_stc_keywords', true);
                        update_post_meta( $user_id, '_stc_subscriber_keywords', $keywords);
                        $search_areas = get_post_meta($user_id, '_stc_search_areas', true);
                        update_post_meta( $user_id, '_stc_subscriber_search_areas', $search_areas);
                        $notifications = get_post_meta($user_id, '_stc_notifications', true);
                        update_post_meta( $user_id, '_stc_subscriber_notifications', $notifications);
                        wp_update_post(array( 'ID' => $user_id, 'post_status' => 'publish', 'post_category' => $cats));
                        $taxos = array();
                        foreach($cats as $ct) {$taxos[] = intval($ct);}
                        if (isset($this->settings['taxonomies'])) {
                                foreach ($this->settings['taxonomies'] as $taxName) {
                                        wp_set_object_terms( $user_id, $taxos, $taxName );
                                }
                        }
        		/* translators: %s: email from the subscriber */
        		$notice[] = sprintf( __( 'We have successfully activated the updated categories/keywords for subscription %s in our database.', 'subscribe-to-category' ), $stc_user );
                } else {
                        // set this stc post to the published state.
                        wp_update_post(array( 'ID' => $user_id, 'post_status' => 'publish'));
        		/* translators: %s: email from the subscriber */
        		$notice[] = sprintf( __( 'We have successfully activated your email %s in our database.', 'subscribe-to-category' ), $stc_user );
                }
                update_post_meta( $user_id, '_stc_last_notice', $notice[0]);
		$this->notice = $notice;
		return $notice;
	}

        /**
	 * Unsubscribe user from subscription
	 *
	 * @TODO: add contact email if something went wrong
	 *
	 * @since  1.0.0
	 */
	private function unsubscribe_user() {
		global $wpdb;
		$meta_key = '_stc_hash';
		if ( isset( $_GET['stc_unsubscribe'] ) ) {
			$meta_value = sanitize_key( $_GET['stc_unsubscribe'] );
		}
		if ( isset( $_GET['stc_user'] ) ) {
			$stc_user = strtolower( $_GET['stc_user'] );
		}

		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $wpdb->posts AS post
                                LEFT JOIN $wpdb->postmeta AS meta ON post.ID = meta.post_id
                                WHERE meta.meta_key = %s AND meta.meta_value = %s
                                AND post.post_type = %s
                                AND post.post_title = %s",
				$meta_key,
				$meta_value,
				$this->post_type,
				$stc_user
			)
		);

		if ( empty( $user_id ) ) {
			$notice[] = __( 'We are sorry but something went wrong with your unsubscription.', 'subscribe-to-category' );
			$this->notice = $notice;
			return $notice;
		}

		// hook right before deleting post.
		do_action( 'stc_before_unsubscribe', $user_id );

		$subscriber_email = get_the_title( $user_id );
		wp_update_post( array('ID' => $user_id, 'post_status' => 'marked' ) );
		/* translators: %s: email from the unsubscriber */
		$notice[] = sprintf( __( 'We have successfully removed your email %s from our database.', 'subscribe-to-category' ), $stc_user );
		$this->notice = $notice;
		return $notice;
	}
        
        /**
         * Check if this posttype is one of the custom post checked in the stc settings
         * 
         * @since 2.4.1
         * 
         * @param string $name contains the custom post name.
         * @return boolean $found true if the name is checked in the stc settings.
         */
        private function check_selected_posts($name) {
             $found = false;
             if (isset($this->settings['cpt'])) {
                 foreach ($this->settings['cpt'] as $post_name) {
                    if ($post_name == $name) { $found = true;}
                 }
             }
             return $found;
        }
        

	/**
	 * Save post hook to update post meta
	 *
	 * @since 1.2.0
	 *
	 * @param  int   $post_id     Post ID.
	 * @param  array $post      Post ID.
	 */
	public function save_post( $post_id, $post ) {
                global $wpdb;

		// If this is just a revision, exit.
		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		// check for gutenberg editor active.
		if ( isset( $this->settings['exclude_gutenberg'] ) && '1' === $this->settings['exclude_gutenberg'] ) {
			// exit for bulk actions and auto-drafts.
			if ( empty( $post ) ) {
				return false;
			}
			// only trigger this from admin.
			if ( ! isset( $post->post_type ) ) {
				return false;
			}

                        // exit if not post type post or one of the elected cutom posts.
			if ( 'post' != $post->post_type && !$this->check_selected_posts($post->post_type)) {
				return false;
			}
		} else {
			if ( isset( $_POST['post_type']  ) ) {
				wp_verify_nonce( sanitize_key( $_POST['post_type'] ) );
			}
			// exit for bulk actions and auto-drafts.
			if ( empty( $_POST ) ) {
				return false;
			}
			// only trigger this from admin.
			if ( ! isset( $_POST['post_type'] ) ) {
				return false;
			}
			// exit if not post type post or one of the elected cutom posts.
			if ( isset( $_POST['post_type'] ) && 'post' != $_POST['post_type'] && !$this->check_selected_posts($_POST['post_type']) ) {
				return false;
			}
                        // check if we need to update the '_stc_notifier_prevent' meta data
                        if (isset($_POST['stc_notsend']) && "1" === $_POST['stc_notsend']) {
        			update_post_meta( $post_id, '_stc_notifier_prevent', '1' );
                        } else {
        			update_post_meta( $post_id, '_stc_notifier_prevent', '0' );
                        }
		}

		// exit if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// if our current user can't edit this post, bail out.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

                // check if the STC postmeta keys are present
                $stc_status = get_post_meta( $post_id, '_stc_notifier_status', true );
                $stc_prevent = get_post_meta( $post_id, '_stc_notifier_prevent', true );

		if ( empty( $stc_status) && ($post->post_status === "publish" || $post->post_status === "future") && (empty($stc_prevent) || $stc_prevent='0') ) {
			update_post_meta( $post_id, '_stc_notifier_status', 'outbox' ); // updating post meta to initiate the send email process.
			update_post_meta( $post_id, '_stc_notifier_request', '' );
		} else {
			// only set to outbox when the classic editor is active and the resend checkbox is checked.
                        if (  ( !isset( $this->settings['exclude_gutenberg'] ) || '1' != $this->settings['exclude_gutenberg']) &&
                                isset( $_POST['stc_resend'] ) && 'on' === $_POST['stc_resend'] && !isset($_POST['stc_notsend']) ) {
				update_post_meta( $post_id, '_stc_notifier_status', 'outbox' ); // updating post meta.
			}
		}
                return false;
	}

	/**
	 * Sending an email to a subscriber with a confirmation link to unsubscription
	 *
	 * @since  1.0.0
	 *
	 * @param  int $stc_id post id for subscriber.
	 * @return [type]         [description]
	 */
	private function send_unsubscribe_mail( $stc_id = '' ) {

		// bail if not numeric.
		if ( empty( $stc_id ) || ! is_numeric( $stc_id ) ) {
			return false;
		}

		// get title and user hash.
		$stc['email'] = get_the_title( $stc_id );
		$stc['hash'] = get_post_meta( $stc_id, '_stc_hash', true );

		// Website name to print as sender.
		$website_name = get_bloginfo( 'name' );

		$email_from = $this->settings['email_from'];
		if ( ! is_email( $email_from ) ) {
			$email_from = get_option( 'admin_email' ); // set admin email if email settings is not valid.
		}

		// Email headers.
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'From: ' . $website_name . ' <' . $email_from . '>' . "\r\n";

		// Setting subject.
		/* translators: %s: email from the bloginfo */
		$title = sprintf( __( 'Unsubscribe from %s', 'subscribe-to-category' ), get_bloginfo( 'name', false ) );

		ob_start(); // start buffer.
		$this->email_html_unsubscribe( $stc );
		$message = ob_get_contents();
		ob_get_clean();

		// encode subject to match åäö for some email clients.
// phpmailer takes care of the UTF-8 characters		$subject = '=?UTF-8?B?' . base64_encode( $title ) . '?=';
		wp_mail( $stc['email'], $title, $message, $headers );
	}

	/**
	 * Sending an email to a subscriber with a activation link to subscription
	 *
	 * @since  2.1.7
	 *
	 * @param  int $stc_id post id for subscriber.
	 * @return [type]         [description]
	 */
	private function send_subscribe_mail( $stc_id = '' ) {

		// bail if not numeric.
		if ( empty( $stc_id ) || ! is_numeric( $stc_id ) ) {
			return false;
		}

		// get title and user hash.
		$stc['email'] = get_the_title( $stc_id );
		$stc['hash'] = get_post_meta( $stc_id, '_stc_hash', true );

		// Website name to print as sender.
		$website_name = get_bloginfo( 'name' );

		$email_from = $this->settings['email_from'];
		if ( ! is_email( $email_from ) ) {
			$email_from = get_option( 'admin_email' ); // set admin email if email settings is not valid.
		}

		// Email headers.
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'From: ' . $website_name . ' <' . $email_from . '>' . "\r\n";

		// Setting subject.
		/* translators: %s: email from the bloginfo */
		$title = sprintf( __( 'Activate new or updated subscription to %s', 'subscribe-to-category' ), get_bloginfo( 'name' , false) );

		ob_start(); // start buffer.
		$this->email_html_subscribe( $stc );
		$message = ob_get_contents();
		ob_get_clean();

		// encode subject to match åäö for some email clients.
//                phpmailer takes care of the UTF-8 characters		$subject = '=?UTF-8?B?' . base64_encode( $title ) . '?=';
		wp_mail( $stc['email'], $title, $message, $headers );
	}
        
	/**
	 * Returns the content for email unsubscription
	 *
	 * @since  1.0.0
	 *
	 * @param  array $stc Holds the undiscriber.
	 * @return string
	 */
	private function email_html_unsubscribe( $stc = '' ) {
		if ( empty( $stc ) ) {
			return false;
		}
		?>
			<?php /* translators: %s: the bloginfo */ ?>
			<h3><?php printf( esc_html__( 'Unsubscribe from %s', 'subscribe-to-category' ), esc_attr( get_bloginfo( 'name' ) ) ); ?></h3>
			<div style="margin-top: 20px;"><a href="<?php echo esc_attr( get_bloginfo( 'url' ) ) . '/?stc_unsubscribe=' . esc_attr( $stc['hash'] ) . '&stc_user=' . esc_attr( $stc['email'] ); ?>"><?php esc_html_e( 'Follow this link to confirm your unsubscription', 'subscribe-to-category' ); ?></a></div>
		<?php
	}
        
	/**
	 * Returns the content for email unsubscription
	 *
	 * @since  1.0.0
	 *
	 * @param  array $stc Holds the undiscriber.
	 * @return string
	 */
	private function email_html_subscribe( $stc = '' ) {
		if ( empty( $stc ) ) {
			return false;
		}
		?>
			<?php /* translators: %s: the bloginfo */ ?>
			<h3><?php printf( esc_html__( 'Subscribe to %s activation', 'subscribe-to-category' ), esc_attr( get_bloginfo( 'name' ) ) ); ?></h3>
			<div style="margin-top: 20px;"><a href="<?php echo esc_attr( get_bloginfo( 'url' ) ) . '/?stc_subscribe=' . esc_attr( $stc['hash'] ) . '&stc_user=' . esc_attr( $stc['email'] ); ?>"><?php esc_html_e( 'Follow this link to activate your subscription', 'subscribe-to-category' ); ?></a></div>
		<?php
	}

        /**
	 * Collect data from _POST for subscription
	 *
	 * @since  1.0.0
	 *
	 * @return string Notice to user
	 */
	public function collect_post_data() {
                
		// correct form submitted.
		if ( isset( $_POST['action'] ) && 'stc_subscribe_me' === $_POST['action'] ) {

			// if there is an unsubscription event.
			if ( isset( $_POST['stc-unsubscribe'] ) && '1' === $_POST['stc-unsubscribe'] ) {

				if ( isset( $_POST['stc_email'] ) ) {
					wp_verify_nonce( sanitize_email( wp_unslash( $_POST['stc_email'] ) ) );
				}
				// check if email is valid.
				if ( is_email( wp_unslash( $_POST['stc_email'] ) ) ) {
					$data['email'] = sanitize_email( wp_unslash( $_POST['stc_email'] ) );
				} else {
					$error[] = __( 'You need to enter a valid email address', 'subscribe-to-category' );
				}

				// check if user exists and through error if not.
				if ( empty( $error ) ) {

					$this->data = $data;
					$result = $this->subscriber_exists();

					if ( empty( $result ) ) {
						$error[] = __( 'Email address not found in database', 'subscribe-to-category' );
					}
				}

				if ( ! empty( $error ) ) {
                                        if (is_null(get_page_by_path("stcs-confirmation-page"))) {
                                                $this->error = $error;
                                                return $error;
                                        } else {
                                                wp_redirect( get_home_url().  "/stcs-confirmation-page?notice=". $error[0]);
                                                exit;
                                        }
				}

				$this->send_unsubscribe_mail( $result );

                                $notice[] = __( 'Please check email to confirm within 24 hours.', 'subscribe-to-category' );
                                if (is_null(get_page_by_path("stcs-confirmation-page"))) {
                                        $this->notice = $notice;
                                        return $notice;
                                } else {
                                        wp_redirect( get_home_url().  "/stcs-confirmation-page?notice=". $notice[0]);
                                        exit;
                                }
			}
                        
			// check if email is valid and save an error if not.
			$error = false;
			if ( is_email( sanitize_email( wp_unslash( $_POST['stc_email'] ) ) ) ) {
				$data['email'] = sanitize_text_field( wp_unslash( $_POST['stc_email'] ) );
			} else {
				$error[] = __( 'You need to enter a valid email address', 'subscribe-to-category' );
			}

                        // process mobile phone number
			if ( ! empty( $_POST['stc_mobile_phone_hidden'] ) ) {
                                // Allow + sign in phone number
                                $phone_to_check = filter_var($_POST['stc_mobile_phone_hidden'], FILTER_SANITIZE_NUMBER_INT);
                                // Remove - . and , from number
                                $phone_to_check = str_replace("-", "", $phone_to_check);
                                $phone_to_check = str_replace(".", "", $phone_to_check);
                                $phone_to_check = str_replace(",", "", $phone_to_check);
				$data['stc_mobile_phone'] = $phone_to_check;

                                // We check if this mobile number is in the unsubscibers list of TextMagic
                                try {
                                        // get all unsubscribers in the list
                                        $result = $this->api_textmagic->getUnsubscribers(1, 10);
                                        // identify if the current mobile number is in the list
                                        $phone = substr($phone_to_check, 1);
                                        foreach($result['resources'] as $resource) {
                                                if ($resource['phone'] === $phone) {
                        				$error[] = __( 'Mobile phone number has issued a stop command', 'subscribe-to-category' );
                                                        break;
                                                }
                                        }
                                } catch (Exception $e) {
                                        echo 'Exception when calling TextMagicApi->getUnsubscribers: ', $e->getMessage(), PHP_EOL;
                                }

                        } else {
                                $data['stc_mobile_phone'] = "";
			}

                        // process keywords
			if ( ! empty( $_POST['stc_keywords'] ) ) {
				$data['keywords'] = sanitize_text_field(wp_unslash( $_POST['stc_keywords'] ) );
			} else {
                                $data['keywords'] = "";
			}

                        // in what areas to search for keywords
			if ( ! empty( $_POST['stc_search_areas'] ) ) {
				$data['search_areas'] = implode(',', $_POST['stc_search_areas'] );
			} else {
                                $data['search_areas'] = "";
			}

                        // process the notifications
			if ( ! empty( $_POST['stc_notifications'] ) ) {
				$data['notifications'] = implode(',', $_POST['stc_notifications'] );
			} else {
                                $data['notifications'] = "";
			}

                        // subscribe for all categories.
			$data['all_categories'] = false;
                        $data['categories'] = array();
			if ( isset( $_POST['stc_all_categories'] ) ) {
				$data['all_categories'] = true;
			}

                        // check if stc_categories are present in the form
                        if (isset($_POST['stc_categories'])) {
                                // is there one or more category selected.
                                if ( ! empty( $_POST['stc_categories'] ) ) {
                                        $data['categories'] = array_map( 'sanitize_text_field', wp_unslash( $_POST['stc_categories'] ) );
                                }
                        } else {
                                // take the postmeta data from the page with slug "stcs-filter-posts-page"
                                if (get_page_by_path("stcs-filter-posts-page") != null) $pageId = get_page_by_path("stcs-filter-posts-page")->ID;
                                // save the filter values for usage by new or updated subscriptions.
                                if (isset($pageId)) {
                                        if (get_post_meta( $pageId, '_stc_filtered_categories', true) === "") {
                                                $terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false) );
                                                foreach($terms as $term) $data['categories'][] = $term->term_id;
                                        } else {
                                                $data['categories'] = explode('|', get_post_meta( $pageId, '_stc_filtered_categories', true));
                                        }
                                        // set the mandatory subscription terms if they are given together with the query link name in the settings
                                        // but only if the subscriber does not yet exsist
                                        $this->data = $data;
                                        if (!$this->subscriber_exists()) {
                                                $terms=array();
                                                if (isset($this->settings['post_filter_query']) && explode('|', $this->settings['post_filter_query'])[1] != null && explode('|', $this->settings['post_filter_query'])[1] != "" ) {
                                                        // get the comma delimited string of mandatory category term id's
                                                        $terms = explode(',',explode('|', $this->settings['post_filter_query'])[1]);
                                                        foreach($terms as $term) {
                                                             $idx = array_search($term, $data['categories']);
                                                             // if not present in the array add the mandatory term_id
                                                             if (is_bool($idx)) $data['categories'][] = intval($term);
                                                        }
                                                }

                                        }
                                }
                        }

			// save user to subscription post type if no error.
			if ( empty( $error ) ) {
                                $this->data = $data;
                                // first check if the subscriber does exsist but is in an awainting conformation state
                                // we need to do this because refreshing the subscription page would lead to double awaiting confirmation entries of the same user
                                if (! empty($this->subscriber_exists('approval')) || (! empty($this->subscriber_exists('update_approval')))) {
                                    $error[] = __( 'Awaiting Approval', 'subscribe-to-category' );

                                    if (is_null(get_page_by_path("stcs-confirmation-page"))) {
                                            $this->error = $error;
                                            return $error;
                                    } else {
                                            wp_redirect( get_home_url().  "/stcs-confirmation-page?notice=". $error[0]);
                                            exit;
                                    }
                                } else {
                                    $post_id = $this->insert_or_update_subscriber();

                                    $stc_hash = get_post_meta( $post_id, '_stc_hash', true );
                                    $url_querystring = '?stc_status=success&stc_hash=' . $stc_hash;
                                }
			} else {
                                if (is_null(get_page_by_path("stcs-confirmation-page"))) {
                                        $this->error = $error;
                                        return $error;
                                } else {
                                        wp_redirect( get_home_url().  "/stcs-confirmation-page?notice=". $error[0]);
                                        exit;
                                }
			}
                        if (!is_user_logged_in()) {
                                $notice[] = __( 'Please check email to confirm within 24 hours.', 'subscribe-to-category' );
                                if (is_null(get_page_by_path("stcs-confirmation-page"))) {
                                        $this->notice = $notice;
                                        return $notice;
                                } else {
                                        wp_redirect( get_home_url().  "/stcs-confirmation-page?notice=". $notice[0]);
                                        exit;
                                }
                        }
                        $this->notice = array();
		}
	}

	/**
	 * Check if subscriber already exists
	 *
	 * @since  1.0.0
	 *
	 * @return int post_id
	 */
	private function subscriber_exists($status = 'publish') {
		global $wpdb;
		$data = $this->data;

		$result = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'stc' AND post_status = %s ", $data['email'], $status ) );

		if ( empty( $result ) ) {
			return false;
		}

		return $result->ID;
	}

	/**
	 * Update user with selected categories if user exists, else add user as new user.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $post_data currently not in use.
	 */
	private function insert_or_update_subscriber( $post_data = '' ) {
                
		$data = $this->data;

		if ( empty( $data ) ) {
			$data = $post_data;
		}

		if ( empty( $data ) ) {
			return false;
		}
                
                foreach ($data['categories'] as $str) $data['taxonomies'][] = intval($str);

		// if subscriber already exists, grab the post id.
		$post_id = $this->subscriber_exists();

		$post_data = array(
			'ID' => $post_id,
			'post_type' => 'stc',
			'post_title' => $data['email'],
			'post_status' => 'publish',
			'post_author' => 1,
			'post_category' => $data['categories'],
		);
                if (!is_user_logged_in() && empty($post_id)) $post_data['post_status'] = 'approval';
                
		// update post if subscriber exist, else insert subscriber as new post.
		if ( ! empty( $post_id ) ) {
                        
                        // check if existing mobile phone number differs with the one in the data array
                        if (get_post_meta($post_id, '_stc_subscriber_mobile_phone', true) != $data['stc_mobile_phone']) {
                                if ($data['stc_mobile_phone'] != "") {
                                        update_post_meta( $post_id, '_stc_subscriber_mobile_phone_status', "new");
                                        try {
                                                // is this phone number allready in a contact list?
                                                $result = $this->api_textmagic->getContactByPhone(substr($data['stc_mobile_phone'],1));
                                        } catch (Exception $e) {
                                                // error 404 means contact not found in any list
                                                if ($e->getCode() === 404) {
                                                        $this->textmagic->addPhoneToSTCContactList($data, $resource['id']);
                                                } else {
                                                        echo 'Exception when calling TextMagicApi->getContactByPhone ', $e->getMessage(), PHP_EOL;
                                                }
                                        }
                                        // Send a confirm request to the mobil number
                                        $this->textmagic->sendSingleSMS($data['stc_mobile_phone'], $this->get_html_content("stc-sms-confirm-message"));
                                } else {
                                        update_post_meta( $post_id, '_stc_subscriber_mobile_phone_status', "");
                                }
                        }

                        // if the user is not logged in we will save the requested categories and taxonomies to the meta data and leave current ones in place
                        // we also set the status to update_approval effectively the update is postponed until the user has verified the update
                        if (!is_user_logged_in()) {
                            update_post_meta( $post_id, '_stc_cats', $data['categories']);
                            update_post_meta( $post_id, '_stc_mobile_phone', $data['stc_mobile_phone']);
                            update_post_meta( $post_id, '_stc_keywords', $data['keywords']);
                            update_post_meta( $post_id, '_stc_search_areas', $data['search_areas']);
                            update_post_meta( $post_id, '_stc_notifications', $data['notifications']);
                            $post_data['post_status'] = 'update_approval';
                            $post_id = wp_update_post(array(
                                    'ID' => $post_id,
                                    'post_status' => 'update_approval'
                            ));
                        } else {
                                $post_id = wp_update_post( $post_data );
                                update_post_meta( $post_id, '_stc_subscriber_mobile_phone', $data['stc_mobile_phone']);
                                update_post_meta( $post_id, '_stc_subscriber_keywords', $data['keywords']);
                                update_post_meta( $post_id, '_stc_subscriber_search_areas', $data['search_areas']);
                                update_post_meta( $post_id, '_stc_subscriber_notifications', $data['notifications']);
                                if (isset($this->settings['taxonomies']) && isset($data['taxonomies'])) {
                                        foreach ($this->settings['taxonomies'] as $taxName) {
                                                wp_set_object_terms( $post_id, $data['taxonomies'], $taxName );
                                        }
                                }
                        }
                        if (!is_user_logged_in()) {$this->send_subscribe_mail( $post_id );}
                        
			// hook after updating a subscriber.
			do_action( 'stc_after_update_subscriber', $post_id, $data['categories'], true === $data['all_categories'] ? '1' : '0' );
		} else {
			$post_id = wp_insert_post( $post_data );
                        // add also possible taxonomies to this new subscriber
                        if (isset($this->settings['taxonomies']) && isset($data['taxonomies'])) {
                                foreach ($this->settings['taxonomies'] as $taxName) {
                                        wp_set_object_terms( $post_id, $data['taxonomies'], $taxName );
                                }
                        }
			update_post_meta( $post_id, '_stc_hash', md5( $data['email'] . time() ) );
                        update_post_meta( $post_id, '_stc_url_blocked', 0);
                        update_post_meta( $post_id, '_stc_subscriber_mobile_phone', $data['stc_mobile_phone']);
                        if ($data['stc_mobile_phone'] === "") {
                                update_post_meta( $post_id, '_stc_subscriber_mobile_phone_status', "");
                        } else {
                                update_post_meta( $post_id, '_stc_subscriber_mobile_phone_status', "new");
                        }
                        update_post_meta( $post_id, '_stc_subscriber_keywords', $data['keywords']);
                        update_post_meta( $post_id, '_stc_subscriber_search_areas', $data['search_areas']);
                        update_post_meta( $post_id, '_stc_subscriber_notifications', $data['notifications']);

                        // initally set the categories and keywords also as meta data to recover previous setting when approval / update is not verified by the user
			update_post_meta( $post_id, '_stc_cats', $data['categories'] );
                        update_post_meta( $post_id, '_stc_mobile_phone', $data['stc_mobile_phone']);
                        update_post_meta( $post_id, '_stc_keywords', $data['keywords']);
                        update_post_meta( $post_id, '_stc_search_areas', $data['search_areas']);
                        update_post_meta( $post_id, '_stc_notifications', $data['notifications']);

                        if (!is_user_logged_in()) {$this->send_subscribe_mail( $post_id );}

			// hook after inserting a subscriber.
			do_action( 'stc_after_insert_subscriber', $post_id, $data['categories'], true === $data['all_categories'] ? '1' : '0' );
		}
                
                // update post meta if the user subscribes to all categories.
		if ( true === $data['all_categories'] ) {
			update_post_meta( $post_id, '_stc_all_categories', 1 );
		} else {
			delete_post_meta( $post_id, '_stc_all_categories' );
		}

		return $post_id;
	}

	/**
	 * Save post for stc post_type from admin
	 *
	 * @since  1.0.0
	 * @param int $post_id The id into the post table.
	 */
	public function save_post_stc( $post_id ) {
		global $post;

		if ( isset( $_POST['post_type'] ) ) {
			wp_verify_nonce( sanitize_key( $_POST['post_type'] ) );
		}

		// bail for bulk actions and auto-drafts.
		if ( empty( $_POST ) ) {
			return false;
		}

		// only trigger this from admin.
		if ( ! isset( $_POST['post_type'] ) ) {
			return false;
		}

		if ( isset( $_POST['post_type'] ) && 'stc' != $_POST['post_type'] ) {
			return false;
		}

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		// get categories to for counting and comparing to user categories.
		$categories = get_categories( array( 'hide_empty' => false ) );
		$sum_of_categories = count( $categories );

		if ( isset( $_POST['post_category'] ) ) {
			$sum_of_post_categories = count( $_POST['post_category'] ) - 1; // wp sets a dummy item in post_category, therefore -1.
		}
                                
		// sanitize input.
		if ( isset( $_POST['post_title'] ) ) {
			$email = sanitize_email( wp_unslash( $_POST['post_title'] ) );
		}

		// is email valid.
		$post_id_match = '';
		if ( ! is_email( $email ) ) {
			set_transient( 'error', __( 'You need to enter a valid email address', 'subscribe-to-category' ) ); // set error if not valid.
		} else {
			$this->data['email'] = $email;
			$post_id_match = $this->subscriber_exists();
		}

		if ( $post_id_match != $post_id || empty( $post_id_match ) ) {
			set_transient( 'error', __( 'E-mail address already exists', 'subscribe-to-category' ) ); // set error.
		}

		$error = get_transient( 'error' );

		// if there are errors set post to draft.
		if ( ! empty( $error ) ) {

			remove_action( 'save_post_stc', array( $this, 'save_post_stc' ) );
			// update the post set it to draft.
			wp_update_post(
				array(
					'ID' => $post_id,
					'post_status' => 'draft',
				)
			);

			add_action( 'save_post_stc', array( $this, 'save_post_stc' ) );

			return false;
		}

		// no errors, continue.
		// is there a hash for user.
		$hash_exists = get_post_meta( $post_id, '_stc_hash', true );
		if ( empty( $hash_exists ) ) {
			update_post_meta( $post_id, '_stc_hash', md5( $this->data['email'] . time() ) );
		}

		// check if user has all categories and update post meta if true.
		if ( $sum_of_categories == $sum_of_post_categories ) {
			update_post_meta( $post_id, '_stc_all_categories', 1 );
		} else {
			delete_post_meta( $post_id, '_stc_all_categories' );
		}
	}

	/**
	 * Display error in wordpress format as notice if exists
	 *
	 * @since  1.0.0
	 */
	public function save_post_stc_error() {

		if ( get_transient( 'error' ) ) {
			$error = get_transient( 'error' );

			$error .= __( ' - this post is set to draft', 'subscribe-to-category' );
			printf( '<div id="message" class="error"><p><strong>%s</strong></p></div>', esc_attr( $error ) );
			delete_transient( 'error' );
		}
	}

	/**
	 * Render html to subscribe to categories
	 *
	 * @since  1.0.0
	 * @param string $atts The perticulars for the subscriber.
	 * @return string
	 */
	public function stc_subscribe_render( $atts ) {

		// start buffering.
		ob_start();
		$this->html_render( $atts );
		$form = ob_get_contents();
		ob_get_clean();

		return $form;
	}

	/**
	 * Render html to subscribe to categories
	 *
	 * @since  1.0.0
	 * @param string $atts The perticulars for the subscriber.
	 * @return string
	 */
	public function stc_subscribe_render_to_post( $atts ) {

		// start buffering.
		ob_start();
		$this->html_render_to_post( $atts );
		$form = ob_get_contents();
		ob_get_clean();

		return $form;
	}
        
        
	/**
	 * Copy the terms used in the query to filter post
	 *
	 * @since  2.6.3
	 */
	public static function link_into_smart_filter_query($query, $widget) {
                $terms= array();
                // check if there are more then one terms in the filter query
                if (is_array($query->query_vars['tax_query']['category']['terms'])) {
                        // process the filter data as araay of terms
                        foreach($query->query_vars['tax_query']['category']['terms'] as $id) {
                                $terms[] = $id;
                        }
                } else if (!is_null($query->query_vars['tax_query']['category']['terms'])) {
                        // we have only one item in the filter query as a string
                        $terms[] = $query->query_vars['tax_query']['category']['terms'];
                }
                // get the page id of the page with the slug name of the edit post elelement
                $pageId = get_page_by_path("stcs-filter-posts-page")->ID;
                // save the filter values for usage by new or updated subscriptions.
                if (isset($pageId)) update_post_meta( $pageId, '_stc_filtered_categories', implode('|', $terms));
	}
        
	/**
	 * Sort the terms (recursive function)
	 *
	 * @since  2.4.1
	 * @param array $a The terms arguments.
	 * @param integer $parent The id of the parent.
	 * @param integer $level The indent in the list structure.
	 * @param string $name The label of this taxonomy.
         * @param array $atts $atts The category parameters.
	 */
	private function sort_terms( $a, $parent, $level, $levelBefore, $name, $atts) {
                $args = $a;
                $args['parent'] = $parent;
                if (isset($atts['treeview_enabled']) && $atts['treeview_enabled']=="false") {
                        unset($args['parent']);
                }
		$cats = get_terms( $args );
                if (isset($atts['treeview_enabled']) && $atts['treeview_enabled']=="false") {
                        $ct = array();
                        foreach($cats as $cat) {
                                $cat->parent = 0;
                                $ct[] = $cat;
                        }
                        $cats = $ct;
                }
                if (isset($atts['category_not_in'])) {
                        $not_in = array();
                        $not_in = explode( ',', str_replace(', ', ',', $atts['category_not_in']));
                        foreach($cats as $idx => $cat) {
                                if (in_array($cat->name, $not_in)) {
                                        unset($cats[$idx]);
                                }
                        }
                }
                if (isset($atts['category_id_not_in'])) {
                        $not_in = array();
                        $not_in = explode( ',', $atts['category_id_not_in']);
                        foreach($cats as $idx => $cat) {
                                if (in_array($cat->term_id, $not_in)) {
                                        unset($cats[$idx]);
                                }
                        }
                }
                if (isset($atts['category_in']) && $parent == 0) {
                        $in = array();
                        $in = explode( ',', str_replace(', ', ',', $atts['category_in']));
                        foreach($cats as $idx => $cat) {
                                if (!in_array($cat->name, $in)) {
                                        unset($cats[$idx]);
                                }
                        }
                }
                if (isset($atts['category_id_in']) && $parent == 0) {
                        $in = array();
                        $in = explode( ',', $atts['category_id_in']);
                        foreach($cats as $idx => $cat) {
                                if (!in_array($cat->term_id, $in)) {
                                        unset($cats[$idx]);
                                }
                        }
                }
                foreach ($cats as $cat) {
                        $this->taxonomies['name'][] = $name;
                        $this->taxonomies['taxonomies'][] = $cat;
                        if (!(isset($atts['treeview_enabled']) && $atts['treeview_enabled']=="false") && get_terms( array('hide_empty' => false, 'taxonomy' => $cat->taxonomy, 'child_of' => $cat->term_id))) {
                                $this->taxonomies['levelBefore'][] = $levelBefore++;
                                $this->taxonomies['level'][] = ++$level;
                                $this->sort_terms($a, $cat->term_id, $level, $levelBefore, $name, $atts);
                                --$level;
                                --$levelBefore;
                        } else {
                                $this->taxonomies['levelBefore'][] = $levelBefore;
                                $this->taxonomies['level'][] = $level;
                        }
                }
        }

	/**
	 * Create the list for this taxonomy label (recursive function)
	 *
	 * @since  2.4.1
	 * @param array $cat_objects The terms.
	 */
	private function create_list( $cat_objects) {
            foreach($cat_objects as $cat) {
                $checkedButton = "";
                foreach ($this->subscribed_cats as $item) {
                        if ($item->term_id === $cat->term_id) $checkedButton = "checked";
                } 
                ?><div><label class="stc-categories-label">
                    <input type="checkbox" name="stc_categories[]" <?php echo $checkedButton?> value="<?php echo esc_html( $cat->term_id ); ?>">
                    <?php echo esc_html( $cat->name ); ?>
                </label></div><?php
            }
        }

        /**
	 * Create the nested list for this taxonomy label (recursive function)
	 *
	 * @since  2.4.1
	 * @param integer $level The current indent in the list structure.
	 * @param string $name The label of this taxonomy.
	 * @param string $ItemName The label of this taxonomy.
	 * @param array $cat_objects The terms.
	 * @param array $cat_names The labels.
	 * @param array $cat_levels The required Levels.
	 * @param string $catet The toggle class folded or unfolded
	 */
	private function create_nested_list( $level, $name, $ItemName, $cat_objects, $cat_names, $cat_levels, $cat_levelsBefore, $caret, $nested) {
                ?><li><?php echo "<span class=".$caret."></span>";?><label class="stc-categories-label"><?php echo $ItemName ?></label>
                 <?php echo "<ul class=".$nested.">";
                while ($this->currentIndex < count($cat_names) && $cat_names[$this->currentIndex]===$name)  {
                    $checkedButton = "";
                    foreach ($this->subscribed_cats as $item) {
                            if ($item->term_id === $cat_objects[$this->currentIndex]->term_id) $checkedButton = "checked";
                    } 
                    if ($level < $cat_levels[$this->currentIndex] && $cat_levels[$this->currentIndex] > $cat_levelsBefore[$this->currentIndex] ) {
                        $ItN = '<input type="checkbox" name="stc_categories[]" ' . $checkedButton .' value="'.esc_html( $cat_objects[$this->currentIndex]->term_id ).'"> '.esc_html( $cat_objects[$this->currentIndex]->name);
                        $this->currentIndex++;
                        $this->create_nested_list( ++$level, $name, $ItN, $cat_objects, $cat_names, $cat_levels, $cat_levelsBefore, $caret, $nested);
                    } else if ($level >= $cat_levels[$this->currentIndex] && $cat_levels[$this->currentIndex] > $cat_levelsBefore[$this->currentIndex] ){
                        for( ; $level>$cat_levelsBefore[$this->currentIndex]; --$level) {
                            ?></ul><?php
                        }
                        $ItN = '<input type="checkbox" name="stc_categories[]" ' . $checkedButton .' value="'.esc_html( $cat_objects[$this->currentIndex]->term_id ).'"> '.esc_html( $cat_objects[$this->currentIndex]->name);
                        $this->currentIndex++;
                        $this->create_nested_list( ++$level, $name, $ItN, $cat_objects, $cat_names, $cat_levels, $cat_levelsBefore, $caret, $nested);
                    } else if ($level >= $cat_levels[$this->currentIndex] && $cat_levels[$this->currentIndex] == $cat_levelsBefore[$this->currentIndex] ) {
                          for( ; $level>$cat_levelsBefore[$this->currentIndex]; --$level) {
                              ?></ul><?php
                          }
                          ?><li>
                          <label class="stc-categories-label">
                            <input type="checkbox" name="stc_categories[]" <?php echo $checkedButton?> value="<?php echo esc_html( $cat_objects[$this->currentIndex]->term_id ); ?>">
                            <?php echo esc_html( $cat_objects[$this->currentIndex]->name ); ?>
                          </label>
                        </li><?php
                        $this->currentIndex++;
                    } else {
                        ?><li>
                          <label class="stc-categories-label">
                            <input type="checkbox" name="stc_categories[]" <?php echo $checkedButton?> value="<?php echo esc_html( $cat_objects[$this->currentIndex]->term_id ); ?>">
                            <?php echo esc_html( $cat_objects[$this->currentIndex]->name ); ?>
                          </label>
                        </li><?php
                        $this->currentIndex++;
                    }
                }
                ?></ul></li><?php
       }

        /**
	 * Html for subscribe form
	 *
	 * @since  1.0.0
	 * @param array $atts The category parameters.
	 * @return [type] [description]
	 */
	public function html_render( $atts = false ) {
		global $wpdb;
                                
                if ( isset( $atts['smart_filter_usage']) && "true" === $atts['smart_filter_usage'] ) {
                        $smart_filter_usage = true;
                } else {
                        $smart_filter_usage = false;
                }

                if ( isset( $atts['subscriber_notification']) && "true" === $atts['subscriber_notification']) {
                        $subscriber_selected_notification = true;
                } else {
                        $subscriber_selected_notification = false;
                }

                if ( isset( $atts['hide_unsubscribe']) && "true" === $atts['hide_unsubscribe'] && "1" === $this->settings['hide_unsubscribe'] ) {
			$hide_unsubscribe = true;
		} else {
			$hide_unsubscribe = false;
		}

                if ( isset( $atts['treeview_folded']) && "false" === $atts['treeview_folded']) {
                        $stc_caret = "stc-caret-u";
                        $stc_nested = "stc-nested-u";
                } else {
                        $stc_caret = "stc-caret";
                        $stc_nested = "stc-nested";
                }

                if ( isset( $atts['treeview_enabled']) && "false" === $atts['treeview_enabled']) {
                        $stc_treeview_enabled = false;
                } else {
                        if (!isset($atts['category_in']) && !isset($atts['category_id_in']) && !isset($atts['category_not_in']) && !isset($atts['category_id_not_in'])) { $stc_treeview_enabled = true; } else { $stc_treeview_enabled = false;}
                }
                
                if ( isset( $atts['mobile_phone']) && "on" === $atts['mobile_phone']) {
                        $mobile_phone_on = true;
                } else {
                        $mobile_phone_on = false;
                }
                
                if ( isset( $atts['keyword_search']) && isset($this->settings['enable_keyword_search']) && "on" === $atts['keyword_search'] && $this->settings['enable_keyword_search'] === "1") {
                        $stc_keyword_on = true;
                } else {
                        $stc_keyword_on = false;
                }
                
		// getting all categories.
		$args = array( 'taxonomy' => 'category', 'hide_empty' => 0, 'orderby' => 'name', 'order' => 'ASC', 'parent' => 0);
                $this->taxonomies = array();

                // getting all the taxonomies
                if (isset($this->settings['taxonomies'])) {
                        foreach ($this->settings['taxonomies'] as $taxName) {
                                if ($taxName == 'category') {
                                        $this->taxonomies['label'][] = get_taxonomies(array( 'public'   => true, '_builtin' => true ), 'objects', 'and')['category']->label;
                                        $this->sort_terms($args, 0, 0, 0, get_taxonomies(array( 'public'   => true, '_builtin' => true ), 'objects', 'and')['category']->label, $atts );
                                } else {
                                        if (!is_null(get_taxonomies(array( 'public'   => true, '_builtin' => false ), 'objects', 'and')[$taxName]->label)) {
                                                $args['taxonomy'] = $taxName;
                                                $this->taxonomies['label'][] = get_taxonomies(array( 'public'   => true, '_builtin' => false ), 'objects', 'and')[$taxName]->label;
                                                $this->sort_terms($args, 0, 0, 0, get_taxonomies(array( 'public'   => true, '_builtin' => false ), 'objects', 'and')[$taxName]->label, $atts);
                                        }
                                }
                        }
                }
                                
                $cats = $this->taxonomies['taxonomies'];
                $cats_level = $this->taxonomies['level'];
                $cats_levelBefore = $this->taxonomies['levelBefore'];
                $cats_names = $this->taxonomies['name'];
                $cats_labels = $this->taxonomies['label'];
                $mobile_phone_number_status = "";

		// if error store email address in field value so user dont need to add it again.
		if ( isset( $_POST['stc_email'] ) ) {
			wp_verify_nonce( sanitize_key( $_POST['stc_email'] ) );
		}
		if ( ! empty( $this->error ) ) {
			if ( isset( $_POST['stc_email'] ) ) {
				$email = sanitize_email( wp_unslash( $_POST['stc_email'] ) );
			}
		} else {
			// preset for email adress if the user is logged in.
                        $this->subscribed_cats = array();
			if ( is_user_logged_in() ) {
                                $this->subscribed_cats = array();
				$user_id = get_current_user_id();
				$user_info = get_userdata( $user_id );
				$email = $user_info->user_email;
                		$result = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_title = %s AND post_type = %s", $email, 'stc' ) );
                                // check if the upload directory for sms exists
                                $path = wp_get_upload_dir()['basedir']."/sms";
                                if(!is_dir($path)){
                                        mkdir($path);
                                }                                       
                                if (isset($result)) {
                                        // we are now checking if we have a different subscriber in the (id)-status.txt file
                                        // if that is the case we will substitute hte logged in user for the subscriber in the (id)-status.txt file
                                        $stc_original_subscriber_id_hidden = $result->ID;
                                        if (file_exists(wp_get_upload_dir()['basedir']."/sms/". $result->ID . "-status.txt")) {
                                                $content = file_get_contents(wp_get_upload_dir()['basedir']."/sms/". $result->ID . "-status.txt");
                                                $content = explode("|", $content);
                                                $email_other = $content[1];
                                		$result_other = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_title = %s AND post_type = %s", $email_other, 'stc' ) );
                                                if (isset($result_other) &&  $result_other->ID != $result->ID) {
                                                        $result = $result_other;
                                                        $email = $email_other;
                                                }
                                        }
                                        switch (get_post_meta($result->ID, "_stc_subscriber_mobile_phone_status", true)) {
                                                case "pending" : $mobile_phone_number_status = "join pending"; break;
                                                case "error"   : $mobile_phone_number_status = "sms send error"; break;
                                                case "rejected": $mobile_phone_number_status = "sms rejected"; break;
                                                case "unknown" : $mobile_phone_number_status = "unknown"; break;
                                                case "joined"  : $mobile_phone_number_status = "joined"; break;
                                                case "new"     : $mobile_phone_number_status = "new"; break;
                                                case "stopped" : $mobile_phone_number_status = "stopped"; break;
                                                default        : $mobile_phone_number_status = "-"; break;
                                        }
                                        file_put_contents( wp_get_upload_dir()['basedir']."/sms/". $result->ID . "-status.txt" , $mobile_phone_number_status . "|" . $email);
                                        $stc_subscriber_id_hidden = $result->ID;
                                        $mobile_phone_number = get_post_meta($result->ID, "_stc_subscriber_mobile_phone", true);
                                        $keywords = get_post_meta($result->ID, "_stc_subscriber_keywords", true);
                                        $search_areas_checked = explode(',', get_post_meta($result->ID, "_stc_subscriber_search_areas", true));
                                        foreach ($this->possible_areas as $key=>$area) {
                                                if (in_array($area['name'], $search_areas_checked)) {
                                                        $this->possible_areas[$key]['status'] = "checked";
                                                } else {
                                                        $this->possible_areas[$key]['status'] = "";
                                                }
                                        }
                                        $notifications = explode(',', get_post_meta($result->ID, "_stc_subscriber_notifications", true));
                                        if (isset($notifications[0]) && $notifications[0] === "") $notifications[0] = "STC";
                                        foreach ($this->possible_moments as $key=>$not) {
                                                if (in_array($not['name'], $notifications)) {
                                                        $this->possible_moments[$key]['status'] = "checked";
                                                } else {
                                                        $this->possible_moments[$key]['status'] = "";
                                                }
                                        }
                                        $stc_post = get_post($result);
                                        $stc_post->categories = array();
                                        if ($stc_post->post_status == "publish") {
                                                // getting all the categories of this STC post
                                                // getting all the taxonomies of this STC post
                                                if (isset($this->settings['taxonomies'])) {
                                                        foreach ($this->settings['taxonomies'] as $taxName) {
                                                                $taxonomies = get_the_terms($result->ID, $taxName);
                                                                if ($taxonomies != false){
                                                                    foreach($taxonomies as $taxo) {
                                                                        $this->subscribed_cats[] = $taxo;
                                                                    }
                                                                }
                                                        }
                                                }
                                                $this->stcEntryPresent = true;
                                        }
                                } else {
                                        file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . "-status.txt" , "-");
                                        $this->stcEntryPresent = false;
                                        // default notification is controlled by STC and not by the user itself
                                        $this->possible_moments[count($this->possible_moments)-1]['status'] = "checked";
                                        // set all possible search areas checked
                                        foreach ($this->possible_areas as $area) {
                                                $area['status'] = "checked";
                                        }
                                }
			}
		}

		// Is there a unsubscribe action.
		$post_stc_unsubscribe = false;
                // Is there a subscribed user active.
		$post_stc_update = false;
		if ( isset( $_POST['stc-unsubscribe'] ) && '1' === $_POST['stc-unsubscribe'] ) {
			$post_stc_unsubscribe = 1;
		}
                ?>
                                <div class="stc-subscribe-wrapper well">
				<?php if ( ! empty( $this->error ) ) : // printing error if exists. ?>
					<?php foreach ( $this->error as $error ) : ?>
						<div class="stc-error"><?php echo esc_html( $error ); ?></div>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ! empty( $this->notice ) ) : // printing notice if exists. ?>
					<?php foreach ( $this->notice as $notice ) : ?>
						<div class="stc-notice"><?php echo esc_html( $notice ); ?></div>
					<?php endforeach; ?>
				<?php else : ?>
                                        <form role="form" method="post" action-xhr="#">
                                                <div class="stc-email-field">
                                                    <label class="stc-categories-label-title" for="stc-email"><?php esc_html_e( 'E-mail Address:', 'subscribe-to-category' ); ?></label><br>
                                                    <input type="text" id="stc-email" class="stc-international-access form-control" <?php echo (!is_user_logged_in() || (is_user_logged_in() && current_user_can('administrator'))) ? null : "readonly" ?> name="stc_email" value="<?php echo (!empty( $email )) ? esc_html( $email ) : null ?>" ></div><br>
                                                <?php if ($mobile_phone_on) { ?>
                                                    <div><label class="stc-categories-label-phone" id="stc-mobile-phone-label" for="stc-mobile-phone"><?php echo sprintf(esc_html__('SMS Notifications: %s', 'subscribe-to-category' ),$mobile_phone_number_status); ?> </label><br>
                                                        <input type="tel" id="stc_mobile_phone" class="form-control" name="stc_mobile_phone" value="<?php echo ! empty( $mobile_phone_number ) ? esc_html( $mobile_phone_number ) : ""; ?>" ><span id="error-msg" class="hide"></div><br>
                                                <?php } ?>
                                                <?php if ($stc_keyword_on) { ?>
                                                        <label class="stc-categories-label-keywords" for="stc-keywords"><?php esc_html_e( 'Search in posts for keywords: ', 'subscribe-to-category' ); ?></label>
                                                        <input type="text" id="stc-keywords" class="stc-form-control" name="stc_keywords" value="<?php echo ! empty( $keywords ) ? esc_html( $keywords ) : null; ?>" >
                                                        <div class="stc-area-checkboxes"><label class="stc-categories-label-title"><?php esc_html_e( 'Select post areas to search in:&nbsp;', 'subscribe-to-category' ); ?>
                                                        <?php foreach ( $this->possible_areas as $area ) : ?>  
                                                                <label class="stc-categories-label" for="<?php echo esc_attr( $area['name'] ); ?>"> <input type="checkbox" name="stc_search_areas[]" id="<?php echo esc_attr( $area['name'] ); ?>" value="<?php echo esc_attr( $area['name'] ) ?>" <?php echo $area['status'] ?> > <?php echo '&nbsp;' . esc_attr( $area['local_name'] ) . '&nbsp;&nbsp;&nbsp;&nbsp;' ?>  </label>
                                                        <?php endforeach;
                                                ?> </label></div><?php } ?> 
                                                <?php if ($subscriber_selected_notification) { ?>
                                                        <div class="stc-notification-checkboxes"><label class="stc-categories-label-title"><?php esc_html_e( 'Notification moment:&nbsp;', 'subscribe-to-category' ); ?>
                                                        <?php foreach ( $this->possible_moments as $not ) : ?>  
                                                                <label class="stc-categories-label" for="<?php echo esc_attr( $not['name'] ); ?>"> <input type="checkbox" name="stc_notifications[]" id="<?php echo esc_attr( $not['name'] ); ?>" value="<?php echo esc_attr( $not['name'] ) ?>" <?php echo $not['status'] ?> > <?php echo '&nbsp;' . esc_attr( $not['local_name'] ) . '&nbsp;&nbsp;' ?>  </label>
                                                        <?php endforeach;
                                                ?> </label></div><?php } ?> 
                                                <?PHP if (!$hide_unsubscribe) { ?>
                                                        <div class="stc-checkbox">
                                                        <label class="stc-categories-label">
                                                                <input type="checkbox" id="stc-unsubscribe-checkbox" name="stc-unsubscribe" value="1" <?php checked( '1', $post_stc_unsubscribe ); ?> >
                                                                <?php esc_html_e( 'Unsubscribe Me', 'subscribe-to-category' ); ?>
                                                        </label>
                                                        </div>
                                                <?PHP }
                                                if (!$smart_filter_usage) { ?>
                                                <div class="stc-categories"<?php echo 1 === $post_stc_unsubscribe ? ' style="display:none;"' : null; ?>>
							<?php if ( ! empty( $cats ) ) : ?>
								<?php if ( count( $cats ) > 1 ) : ?>
                                                                        <?PHP if (!$hide_unsubscribe) { ?>
                                                                                <label class="stc-categories-label-title"><?php esc_html_e( 'Categories / Taxonomies', 'subscribe-to-category' ); ?></label>
                                                                        <?PHP } ?>
									<?php if ( true === $this->show_all_categories ) : ?>
										<div class="stc-checkbox">
											<label class="stc-categories-label">
												<input type="checkbox" id="stc-all-categories" name="stc_all_categories" value="1">
												<?php esc_html_e( 'All categories', 'subscribe-to-category' ); ?>
											</label>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<div class="stc-categories-checkboxes">
									<?php if ( count( $cats ) > 0 ) : ?>
                                                                                <?php $this->currentIndex = 0; foreach ($cats_labels as $label) : ?>
                                                                                        <ul class= "stcUL" >
                                                                                                <?php if ( $stc_treeview_enabled ) {
                                                                                                          $this->create_nested_list(0, $label, $label, $cats, $cats_names, $cats_level, $cats_levelBefore, $stc_caret, $stc_nested);
                                                                                                      } else {
                                                                                                          $this->create_list($cats);
                                                                                                          ?> </ul> <?php break;
                                                                                                      }?>
                                                                                        </ul>
                                                                                <?php endforeach; ?>
									<?php else : ?>
										<input type="hidden" name="stc_categories[]" value="<?php echo esc_html( $cats[0]->cat_ID ); ?>">
									<?php endif; ?>
								</div><!-- .stc-categories-checkboxes -->
							<?php endif; ?>
						</div><!-- .stc-categories -->
                                                <?PHP } ?>
                                                <input type="hidden" name="stc_js_folders_hidden" id="stc_js_folders_hidden" value="<?php echo STC_PLUGIN_URL . '/intl-tel-input/js/utils.js'.'|'.wp_get_upload_dir()['baseurl'] . "/sms/status.txt?dummy=".'|'.esc_url(admin_url('admin-ajax.php')); ?>" />
                                                <input type="hidden" name="stc_original_subscriber_id_hidden" id="stc_original_subscriber_id_hidden" value="<?php echo ! empty( $stc_original_subscriber_id_hidden ) ? esc_html( $stc_original_subscriber_id_hidden ) : ""; ?>" />
						<input type="hidden" name="stc_subscriber_id_hidden" id="stc_subscriber_id_hidden" value="<?php echo ! empty( $stc_subscriber_id_hidden ) ? esc_html( $stc_subscriber_id_hidden ) : ""; ?>" />
						<input type="hidden" name="stc_mobile_phone_hidden" id="stc_mobile_phone_hiddden" value="<?php echo ! empty( $mobile_phone_number ) ? esc_html( $mobile_phone_number ) : ""; ?>" />
						<input type="hidden" name="action" value="stc_subscribe_me"/>
						<?php wp_nonce_field( 'wp_nonce_stc', 'stc_nonce', true, true );
                                                if ( isset( $options['exclude_css'] ) && $options['exclude_css'] ) { ?>
                                                        <button id="stc-update-btn" type="submit" class="stc-btn stc-btn-default"<?php echo !(!$post_stc_unsubscribe && $this->stcEntryPresent)? ' style="display:none;"' : null; ?>><?php esc_html_e( 'Update Me', 'subscribe-to-category' ); ?></button>
        						<button id="stc-subscribe-btn" type="submit" class="stc-btn stc-btn-default"<?php echo !(!$post_stc_unsubscribe && !$this->stcEntryPresent) ? ' style="display:none;"' : null; ?>><?php esc_html_e( 'Subscribe Me', 'subscribe-to-category' ); ?></button>
                					<button id="stc-unsubscribe-btn" type="submit" class="stc-btn stc-btn-default"<?php echo 1 != $post_stc_unsubscribe ? ' style="display:none;"' : null; ?>><?php esc_html_e( 'Unsubscribe', 'subscribe-to-category' ); ?></button>
                                                <?php } else { ?>
                                                        <button id="stc-update-btn" type="submit" class="btn btn-default"<?php echo !(!$post_stc_unsubscribe && $this->stcEntryPresent)? ' style="display:none;"' : null; ?>><?php _e( '<strong>Update Me</strong>', 'subscribe-to-category' ); ?></button>
                                                        <button id="stc-subscribe-btn" type="submit" class="btn btn-default"<?php echo !(!$post_stc_unsubscribe && !$this->stcEntryPresent) ? ' style="display:none;"' : null; ?>><?php _e( '<strong>Subscribe Me</strong>', 'subscribe-to-category' ); ?></button>
                                                        <button id="stc-unsubscribe-btn" type="submit" class="btn btn-default"<?php echo 1 != $post_stc_unsubscribe ? ' style="display:none;"' : null; ?>><?php _e( '<strong>Unsubscribe Me</strong>', 'subscribe-to-category' ); ?></button>
                                                <?php } ?>
                                        </form>
				<?php endif; ?>
			</div><!-- .stc-subscribe-wrapper -->
		<?php
		return;
	}

        /**
	 * Html for subscribe to post form
	 *
	 * @since  4.5.1
	 * @param array $atts The category parameters.
	 * @return [type] [description]
	 */
	public function html_render_to_post( $atts = false ) {
		global $wpdb;
                
                // gets the categories from the active posts.
                // do this for all the terms in all the taxonomies
                get_the_terms(get_post()->ID, 'category');

		// store email address in field value so user dont need to add it again.
		if ( isset( $_POST['stc_email'] ) ) {
			wp_verify_nonce( sanitize_key( $_POST['stc_email'] ) );
		}
		if ( ! empty( $this->error ) ) {
			if ( isset( $_POST['stc_email'] ) ) {
				$email = sanitize_email( wp_unslash( $_POST['stc_email'] ) );
			}
		} else {
			// preset for email adress if the user is logged in.
                        $this->subscribed_cats = array();
			if ( is_user_logged_in() ) {
                                $this->subscribed_cats = array();
				$user_id = get_current_user_id();
				$user_info = get_userdata( $user_id );
				$email = $user_info->user_email;
                		$result = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_title = %s AND post_type = %s", $email, 'stc' ) );
                                if (isset($result)) {
                                        $stc_post = get_post($result);
                                }
                        }
		}

		?>
                <div class="stc-subscribe-wrapper-to-post well">
                <?php if ( ! empty( $this->error ) ) : // printing error if exists. ?>
                        <?php foreach ( $this->error as $error ) : ?>
                                <div class="stc-error"><?php echo esc_html( $error ); ?></div>
                        <?php endforeach; ?>
                <?php endif; ?>
                <?php if ( ! empty( $this->notice ) ) : // printing notice if exists. ?>
                        <?php foreach ( $this->notice as $notice ) : ?>
                                <div class="stc-notice"><?php echo esc_html( $notice ); ?></div>
                        <?php endforeach; ?>
                <?php else : ?>
                        <form role="form" method="post">
                                <table class="stc-post-table"><tbody><tr class="stc-post-table-tr">
                                <td class="stc-post-table-tr-td-l"><?php esc_html_e( 'E-mail Address:', 'subscribe-to-category' ) . ' '; ?></td>
                                <td class="stc-post-table-tr-td-c"><input type="text" id="stc-email" class="form-control" size="50" <?php echo (!is_user_logged_in() || (is_user_logged_in() && current_user_can('administrator'))) ? null : "readonly" ?> name="stc_email" value="<?php echo (!empty( $email )) ? esc_html( $email ) : null ?>" ></td>
                                <td class="stc-post-table-tr-td-r"><button id="stc-subscribe-btn" type="submit" class="stc-btn stc-btn-default"><?php esc_html_e( 'Subscribe Me', 'subscribe-to-category' ); ?></td>
                                </tr></tbody></table>
                                <input type="hidden" name="action" value="stc_subscribe_me_to_post"/>
                                <?php wp_nonce_field( 'wp_nonce_stc', 'stc_nonce', true, true ); ?>
                        </form>
                <?php endif; ?>
                </div><!-- .stc-subscribe-wrapper-to-post -->
		<?php
		return;
	}

        /*
	 * On the scheduled action hook, run a function.
	 *
	 * @since  1.0.0
	 */
	public function stc_send_email($mode="Trigger") {
		global $wpdb;

                // the following code is cron scheduled to run at least once every hour or faster depending on the cron time

                // check for subscriptions that are marked for delete
		$args = ['post_type' => 'stc',
                        'post_status' => 'marked',
			'numberposts' => -1
                        ];
		$posts = get_posts( $args );
		foreach ( $posts as $p ) {
                        // delete post if it is older then 60 seconds.
                        if ((get_post_modified_time('U',false,$p->ID) + 60) < time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )) {
                                wp_delete_post($p->ID, true);
                        }
                }
                // check for subscriptions that are not approved within the required 24 hours
		$args = ['post_type' => 'stc',
                        'post_status' => 'approval',
			'numberposts' => -1
                        ];
		$posts = get_posts( $args );
		foreach ( $posts as $p ) {
                        // delete post if it is older then 24 hours = 86400 seconds.
                        if ((get_post_modified_time('U',false,$p->ID) + 86400) < time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )) {
                                wp_delete_post($p->ID, true);
                        }
                }
                // check for subscription updates that are not approved within the required 24 hours
		$args = ['post_type' => 'stc',
			'post_status' => 'update_approval',
			'numberposts' => -1
                        ];
		$posts = get_posts( $args );
		foreach ( $posts as $p ) {
                        // ignore category settings "update approval". Post is older then 24 hours = 86400 seconds.
                        if ((get_post_modified_time('U',false,$p->ID) + 86400) < time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )) {
                                wp_update_post(array(
                                    'ID' => $p->ID,
                                    'post_status' => 'publish'
                                ));
                        }
                }

                // check post that somehow where not created via de save_post functionality
                $sql = "select DISTINCT wp_posts.ID,wp_posts.post_title FROM wp_posts WHERE wp_posts.post_type='post' and wp_posts.post_status='publish' and NOT EXISTS (SELECT * FROM wp_postmeta where wp_posts.ID = wp_postmeta.post_id and wp_postmeta.meta_key = '_stc_notifier_status');";
      		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
                // look also in the custom posts that are selected within STC
                if (isset($this->settings['cpt'])) {
                    foreach ($this->settings['cpt'] as $custp) {
                        $sql = "select DISTINCT wp_posts.ID,wp_posts.post_title FROM wp_posts WHERE wp_posts.post_type='" . $custp . "' and wp_posts.post_status='publish' and NOT EXISTS (SELECT * FROM wp_postmeta where wp_posts.ID = wp_postmeta.post_id and wp_postmeta.meta_key = '_stc_notifier_status');";
                        $rslt = $wpdb->get_results( $sql, 'ARRAY_A' );
                        // if any found add these to the total list of posts without the status field
                        foreach($rslt as $p) {
                            $result[] = $p;
                        }
                    }
                }                
                // add the detected missing metaboxes to the post
		foreach ( $result as $p ) {
                    // we need to add the status to trigger STC for the first time
                    update_post_meta( intval($p['ID']), '_stc_notifier_status', 'outbox'); // updating post meta to initiate the send email process.
                    update_post_meta( intval($p['ID']), '_stc_notifier_request', '');
                }
                                
                // get posts with a post meta value in outbox.
		$meta_key = '_stc_notifier_status';
		$meta_value = 'outbox';

		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_key' => $meta_key,
			'meta_value' => $meta_value,
		);
                
                // collect all 'posts' with meta data containing 'outbox'
		$posts = get_posts( $args );

                // look also in the custom posts for meta data containing 'outbox'
                if (isset($this->settings['cpt'])) {
                    foreach ($this->settings['cpt'] as $custp) {
                        $args['post_type'] = $custp;
                        $psts = get_posts( $args );
                        foreach($psts as $ps) {
                            $posts[] = $ps;
                        }
                    }
                }

		// add categories to object.
		$outbox = array();
		foreach ( $posts as $p ) {
                        
                        // pickup the assigned categories
			$p->categories = array();

                        // pickup the selected taxonomy names and for each selected name pickup the assigned term_id's
                        // because all the term_id's are unique we just add them to the category array
                        if (isset($this->settings['taxonomies'])) {
                                foreach ($this->settings['taxonomies'] as $taxName) {
                                        $tax = get_the_terms( $p->ID, $taxName );
                                        if ($tax) { foreach ( $tax as $t ) {
                                                $p->categories[] = $t->term_id;
                                        }}
                                }
                        }
                        $outbox[] = $p;
                }
             	// update postmeta that post is processed by setting it to sent.
		foreach ( $outbox as $post ) {
			update_post_meta( $post->ID, '_stc_notifier_status', 'sent' );
		}

        	$this->send_notifier( $outbox, $mode );
	}
        

        /**
	 * Send notifier to subscribers
	 *
	 * @since  1.0.0
	 *
	 * @param  object $outbox Link to the post.
         * @param literal $mode contains the reason 
	 */
	private function send_notifier( $outbox, $mode ) {
                global $wpdb;
		$subscribers = $this->get_subscribers();

		$i = 3;
		$emails = array();
		foreach ( $outbox as $post ) {

			// edit category value so it could be used in in_array(), we dont want a value 2 to be match with value 22.
			$post_cat_compare = array();
                        $post_cat_name = array();
			if ( ! empty( $post->categories ) ) {
				foreach ( $post->categories as $cat ) {
					$post_cat_compare[] = ':' . $cat . ':';
                                        $post_cat_name[] = get_term($cat, "")->name;
				}
			}

			foreach ( $subscribers as $subscriber ) {
                                $mobile_phone_number = get_post_meta($subscriber->ID, "_stc_subscriber_mobile_phone", true);
                                $taxonomy_hierarchy = get_post_meta($subscriber->ID, "_stc_subscriber_taxonomy_hierarchy", true);
                                $keywords = explode(',', str_replace(', ', ',',get_post_meta($subscriber->ID, "_stc_subscriber_keywords", true)));
                                if (isset($keywords[0]) && $keywords[0] === "" ) $keywords = array();
                                foreach ($keywords as $k=>$key) {
                                        $keywords[$k] = strtolower($keywords[$k]);
                                }
                                if (count($keywords)===0 || (!isset($this->settings['enable_keyword_search']) || (isset($this->settings['enable_keyword_search']) && $this->settings['enable_keyword_search'] <> "1"))) {
                                        // notify if matching catagories
                                        $reason = "";
                                        foreach ( $subscriber->categories as $key=>$categories ) {
                                                // add compare signs for in_array().
                                                $catcheck = ':' . $categories . ':';
                                                if ( in_array( $catcheck, $post_cat_compare ) ) {
                                                        $emails[ $i ]['subscriber_id'] = $subscriber->ID;
                                                        $emails[ $i ]['mobile_phone'] = $subscriber->mobile_phone;
                                                        $emails[ $i ]['mobile_phone_status'] = $subscriber->mobile_phone_status;
                                                        $emails[ $i ]['hash'] = get_post_meta( $subscriber->ID, '_stc_hash', true );
                                                        $emails[ $i ]['email'] = $subscriber->post_title;
                                                        $emails[ $i ]['post_id'] = $post->ID;
                                                        if (isset($this->settings['enable_taxonomy_hierarchy']) && $this->settings['enable_taxonomy_hierarchy'] === "1") {
                                                                $tax_label = get_taxonomy(get_term($categories, "")->taxonomy)->label;
                                                                // translators: Taxonomy Group: "term1->term2" found
                                                                $emails[ $i ]['reason'] = sprintf(esc_html__('%s: "%s" found', 'subscribe-to-category'), $tax_label, rtrim(get_term_parents_list($categories,get_term($categories, "")->taxonomy, array('separator' => '->', 'link' => false)),'->'));
                                                        } else {
                                                                $emails[ $i ]['reason'] = sprintf(esc_html__('Term: "%s" found.', 'subscribe-to-category'), get_term($categories, "")->name);
                                                        }
                                                        $emails[ $i ]['merged'] = false;
                                                        $i++;
                                                        break;
                                                }
                                        }
                                } else {
                                        // get the requested search areas
                                        $search_areas = explode(',', str_replace(', ', ',',get_post_meta($subscriber->ID, "_stc_subscriber_search_areas", true)));
                                        $reason = "";
                                        $reason_p1 = "";
                                        $reason_p2 = "";
                                        
                                        if (count($subscriber->categories) == 0){
                                                // look for matching keywords in this post with none of the taxonomies assigned to this post
                                                if ($this->FindKeywords($post, $keywords, $search_areas, $reason)) {
                                                        $emails[ $i ]['subscriber_id'] = $subscriber->ID;
                                                        $emails[ $i ]['mobile_phone'] = $subscriber->mobile_phone;
                                                        $emails[ $i ]['mobile_phone_status'] = $subscriber->mobile_phone_status;
                                                        $emails[ $i ]['hash'] = get_post_meta( $subscriber->ID, '_stc_hash', true );
                                                        $emails[ $i ]['email'] = $subscriber->post_title;
                                                        $emails[ $i ]['post_id'] = $post->ID;
                                                        $emails[ $i ]['reason'] = $reason;
                                                        $emails[ $i ]['merged'] = false;
                                                        $i++;
                                                }
                                        } else {
                                                // check if we have a hit on at least one Taxonomie
                                                foreach ( $subscriber->categories as $key=>$categories ) {
                                                        if ( in_array( ':' . $categories . ':', $post_cat_compare ) ) {
                                                                if (isset($this->settings['enable_taxonomy_hierarchy']) && $this->settings['enable_taxonomy_hierarchy'] === "1") {
                                                                        $tax_label = get_taxonomy(get_term($categories, "")->taxonomy)->label;
                                                                        // translators: Taxonomy Group: "term1->term2" found
                                                                        $reason_p1 = sprintf(esc_html__('%s: "%s" found.', 'subscribe-to-category'), $tax_label, rtrim(get_term_parents_list($categories,get_term($categories, "")->taxonomy, array('separator' => '->', 'link' => false)),'->'));
                                                                } else {
                                                                        $reason_p1 = sprintf(esc_html__('Term: "%s" found.', 'subscribe-to-category'), get_term($categories, "")->name);
                                                                }
                                                                break;
                                                        }
                                                }
                                                $this->FindKeywords($post, $keywords, $search_areas, $reason_p2);
                                                
                                                if ($reason_p1 <> "" && $reason_p2 <> "") { $reason = $reason_p1 . " & " . $reason_p2; }
                                                else if ($reason_p1 <> "" && $reason_p2 == "") {$reason = $reason_p1;}
                                                else if ($reason_p2 <> "" && $reason_p1 == "") {$reason = $reason_p2;}
                                                else {$reason = "";}

                                                if ($reason <> "") {
                                                        // make an email entry for this subscriber
                                                        $emails[ $i ]['subscriber_id'] = $subscriber->ID;
                                                        $emails[ $i ]['mobile_phone'] = $subscriber->mobile_phone;
                                                        $emails[ $i ]['mobile_phone_status'] = $subscriber->mobile_phone_status;
                                                        $emails[ $i ]['hash'] = get_post_meta( $subscriber->ID, '_stc_hash', true );
                                                        $emails[ $i ]['email'] = $subscriber->post_title;
                                                        $emails[ $i ]['post_id'] = $post->ID;
                                                        $emails[ $i ]['reason'] = $reason;
                                                        $emails[ $i ]['merged'] = false;
                                                        $i++;
                                                }
                                        }
                                }
			}
		}

		// remove duplicates, we will just send one email to subscriber.
		$emails = array_intersect_key( $emails, array_unique( array_map( 'serialize', $emails ) ) );
                
                // do we need to postpone the notification on request of the subscriber?                
                foreach($emails as $index=>$email) {

                        // if this subscriber is set to hourly or default(hourly) we follow trough and send the notifier
                        if (isset($subscribers[$email['subscriber_id']]->notifications['Hourly']) || (isset($subscribers[$email['subscriber_id']]->notifications['STC']) && !isset($this->settings['daily_emails']))) continue;
                        // we move the email notification to the joblist as a daily job
                        else if (isset($subscribers[$email['subscriber_id']]->notifications['Daily']) || (isset($subscribers[$email['subscriber_id']]->notifications['STC']) && isset($this->settings['daily_emails'])))  self::insert_email_in_joblist('Daily', $email );
                        // we schedule the job on the first day in the week that is checked by the subscriber (starting today)
                        else {  for($i=intval(date('w')), $x=0; $x < 7 ; $x++, $i++ ) {
                                        if ($i === 7) $i = 0;
                                        if (isset($subscribers[$email['subscriber_id']]->notifications[$this->possible_moments[$i]['name']])) {
                                                self::insert_email_in_joblist($this->possible_moments[$i]['name'], $email );
                                                break; // leave the loop because we only wnat to schedule it once
                                        }
                                }
                        }
                        unset($emails[$index]);
                }
                
                // do we need to add postponed daily work saved in the joblist data table
                if ($mode != "Timer") {
                        // Fetch todays email data from the joblist today=date('w') and/or 'Daily'
                        $jobs = self::retrieve_email_in_joblist(intval(date('w')));
                        // merge the results with the timer email nofifications
                        foreach($jobs as $job) { $emails[] = $job; }
                        // remove duplicates, we will just send one email per subscriber.
                        $emails = array_intersect_key( $emails, array_unique( array_map( 'serialize', $emails ) ) );
                }
                
                $website_name = get_bloginfo( 'name' );
		$email_title = $this->settings['title'];

		$email_from = $this->settings['email_from'];
		if ( ! is_email( $email_from ) ) {
			$email_from = get_option( 'admin_email' ); // set admin email if email settings is not valid.
		}

                $headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $headers .= 'From: ' . $website_name . ' <' . $email_from . '>' . "\r\n";
                
		// loop through subscribers and send notice.
		$i = 1; // loop counter.
                set_time_limit(0);
		foreach ( $emails as $key=>$email ) {
                        // if this email is not yet allreay send to the subscriber via the mulipost route
                        if (!$emails[$key]['merged']) {

                                // we need to identify subscribers that will possible receive multiple new and / or updated post notifications
                                $multiplePosts = array();
                                $keyIndex = array_keys(array_column($emails, 'email'), $email['email'], true);
                                if (count($keyIndex)>1){
                                        //identify the multiple posts that must be merged into one email 
                                        foreach ( $keyIndex as $k=>$Idx) {
                                                $multiplePosts[$k]['post_id'] = $emails[array_keys($emails)[$Idx]]['post_id'];
                                                $multiplePosts[$k]['reason'] = $emails[array_keys($emails)[$Idx]]['reason'];
                                                $emails[array_keys($emails)[$Idx]]['merged'] = true;
                                        }
                                }

                                ob_start(); // start buffering and get content.
                                $sms_content = $this->email_html_content( $email, $multiplePosts);
                                $message = ob_get_contents();
                                ob_get_clean();

                                // do we need to broadcast an SMS message
                                if (isset($this->settings['enable_sms_notification']) && $email['mobile_phone'] != "" && $sms_content != "") {
                                        // Send the SMS notification
                                        $this->textmagic->sendSingleSMS($email['mobile_phone'], $sms_content);
                                }
                                
                                $email_subject = $email_title;
                                if ( empty( $email_title ) ) {
                                        $email_subject = get_post($email['post_id'])->post_title;
                                }

                                // add updated to title if its an update for post.
                                if ( $this->is_stc_resend( $email['post_id'] ) ) {
                                        $email_subject = __( 'Update | ', 'subscribe-to-category' ) . $email_subject;
                                }
                                
                                // filter gives result back and we need that to leave the sending of mails to a provider
                                $filter_answer = apply_filters( 'stc_filter_wp_mail', $email['email'], $email_subject, $message, $headers);
                                
                                if ($filter_answer == $email['email']) {
                                    wp_mail( $email['email'], $email_subject, $message, $headers );
                                }
                                // sleep 2 seconds once every 25 email to prevent blacklisting.
                                if ( $i == $this->sleep_flag ) {
                                        sleep( 2 ); // sleep for two seconds, then proceed.
                                        $i = 0; // reset loop counter.
                                }

                                $i++;
                        }
		}

		// update postmeta on what time email was sent.
		foreach ( $outbox as $post ) {
			update_post_meta( $post->ID, '_stc_notifier_sent_time', gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );
                }
	}
        /**
	 * Find Keywords in Post 
	 *
	 * @since  2.4.19
	 *
	 * @return  boolean True for found
	 */        
        private function FindKeywords(&$post, &$keywords, &$search_areas, &$reason) {
                // notify when keywords are found in post areas
                foreach($search_areas as $area) {
                        switch ($area) {
                                case 'Title'     : {
                                        $title = apply_filters('the_content', get_post_field('post_title', $post->ID));
                                        foreach($keywords as $key) {
                                                $position = stripos($title, $key);
                                                if (!($position === false)) {
                                                        $reason = sprintf(esc_html__('Keyword: "%s" found in Title', 'subscribe-to-category'), $key);
                                                        return true;
                                                }
                                        }
                                        break;
                                }                                                                        
                                case 'Tags'      : {
                                        $tags = get_the_tags($post->ID);
                                        foreach($tags as $tag) {
                                                if (in_array(strtolower($tag->name), $keywords)) {
                                                        $reason = sprintf(esc_html__('Keyword: "%s" found in Tags', 'subscribe-to-category'), $key);
                                                        return true;
                                                }
                                        }
                                        break;
                                }
                                case 'Taxonomies': {
                                        if (isset($this->settings['taxonomies'])) {
                                                foreach ($this->settings['taxonomies'] as $taxName) {
                                                        $tax = get_the_terms( $post->ID, $taxName );
                                                        if ($tax) {foreach ( $tax as $t ) {
                                                                if (in_array(strtolower($t->name), $keywords)) {
                                                                        $reason = sprintf(esc_html__('Keyword: "%s" found in Taxonomies', 'subscribe-to-category'), $key);
                                                                        return true;
                                                                }
                                                        }}
                                                }
                                        }
                                        break;
                                }
                                case 'Content'   : {
                                        $content = apply_filters('the_content', get_post_field('post_content', $post->ID));
                                        foreach($keywords as $key) {
                                                $position = stripos($content, $key);
                                                if (!($position === false)) {
                                                        $reason = sprintf(esc_html__('Keyword: "%s" found in Content', 'subscribe-to-category'), $key);
                                                        return true;
                                                }
                                        }
                                        break;                                                                        
                                }
                        }
                }
                // nothing found so false return
                return false;
        }

	/**
	 * Function to check if a post has been sent before
	 *
	 * @since 1.2.0
	 *
	 * @param  int $post_id    Post ID.
	 *
	 * @return boolean          True or false
	 */
	private function is_stc_resend( $post_id = '' ) {

		$stc_status = get_post_meta( $post_id, '_stc_notifier_sent_time', true );

		if ( ! empty( $stc_status ) ) {
			return true;
		}

		return false;
	}
        
	/**
	 * Render html to email.
	 * Setting limit to content as we still want the user to click and visit our site.
	 *
	 * @since  1.0.0
	 *
	 * @param  object $email Email to complete.
	 */
	private function email_html_content( $email, $multiplePosts ) {
                if (isset($this->settings['nr_words_in_content'])) {$sum_of_words = $this->settings['nr_words_in_content'];} else {$sum_of_words = 0;}
                
                $output = array();
                $output['title'] = "";
		$output['unsubscribe'] = '<a class="stc-unsubscribe-a" href="' . esc_url( get_bloginfo( 'url' ) ) . '/?stc_unsubscribe=' . $email['hash'] . '&stc_user=' . $email['email'] . '">' . __( 'Unsubscribe me', 'subscribe-to-category' ) . '</a>';
		$output['managesubscription'] = '<a class="stc-unsubscribe-a" href="' . esc_url( get_bloginfo( 'url' ) ) . '/?stc_managesubscribe=' . $email['hash'] . '&stc_user=' . $email['email'] . '">' . __( 'Manage Subscription', 'subscribe-to-category' ) . '</a>';
                if (!isset($multiplePosts[0])) {
                        // create an entry into the multiplepost array from the single post in $email
                        $multiplePosts[0]['post_id'] = $email['post_id'];
                        $multiplePosts[0]['reason'] = $email['reason'];
                }
                // create the title and contents for the SMS notification
                $sms_content = "";
                if ($email['mobile_phone_status'] === "joined") {
                        foreach($multiplePosts as $key => $singlePost) {
                                $singlePost['post'] = get_post($singlePost['post_id']);
                                if ($key === 0) {
                                        $template_content = $this->get_html_content("stc-sms-notification-message");
                                        $template_content = strip_tags($template_content, "<br>");
                                        // get all the placeholders available in the template content
                                        $answer = preg_match_all('/{{(.*)}}/Uis', $template_content, $matches);
                                        // do we have a repeatable block in the template content
                                        if (in_array("{{start_content}}", $matches[0]) && in_array("{{end_content}}", $matches[0])) {
                                                $begin = strpos($template_content, "{{start_content}}");
                                                $end = strpos($template_content, "{{end_content}}");
                                                $header = substr($template_content, 0, $begin);
                                                $body = substr($template_content, $begin+strlen("{{start_content}}"), $end-$begin-strlen("{{start_content}}"));
                                                $footer = substr($template_content, $end+strlen("{{end_content}}"));
                                        }
                                        $sms_content .= $header;
                                        $sms_content = str_replace("{{blog_title}}", get_bloginfo( 'name' ), $sms_content);
                                        $sms_content .= $body;
                                        if ($this->is_stc_resend( $singlePost['post_id'])) {
                                                $sms_content = str_replace("{{new_or_update}}", "Update on", $sms_content);
                                        } else {
                                                $sms_content = str_replace("{{new_or_update}}", "New", $sms_content);
                                        }
                                        $sms_content = str_replace("{{post_title_with_perma_link}}", esc_attr( $singlePost['post']->post_title ).' '.trailingslashit(get_home_url()).get_post( $singlePost['post_id'])->post_name, $sms_content);
                                } else if ($key > 0 && $key <= count($multiplePosts)) {
                                        $sms_content .= $body;
                                        $sms_content = str_replace("{{post_title_with_perma_link}}", esc_attr( $singlePost['post']->post_title ).' '.trailingslashit(get_home_url()).get_post( $singlePost['post_id'])->post_name, $sms_content);
                                }
                                if ($key == count($multiplePosts)-1) {
                                        $sms_content .= $footer;
                                }
                                $sms_content = str_replace("{{LF}}", "\n", $sms_content);
                        }
                }
                
                // create the title and part of the contents for each new/update notification
                do_action( 'stc_before_message', $email['post_id'], $email['subscriber_id'] );
                $content = "";
                foreach($multiplePosts as $key => $singlePost) {
                        $singlePost['post'] = get_post($singlePost['post_id']);
                        $output['title'] = '<a href="' . get_permalink( $singlePost['post_id'] ) . '">' . '<h3>' . esc_attr( $singlePost['post']->post_title ) . '</h3></a>';
        		$output['link_to_post'] = '<a class="stc-read-more-a" href="' . get_permalink( $singlePost['post_id'] ) . '">&nbsp;' . __( 'Click here to read more', 'subscribe-to-category' ) . '&nbsp;</a>';

                        // there should be only none or one checked
                        $template = null;
                        if (isset($this->settings['notifications'])) {foreach ($this->settings['notifications'] as $not) { $template = $not; }}
                        if ($template <> null) {
                                if ($key === 0) {
                                        $template_content = $this->get_html_content($template);
                                        // remove the comments of type stc:
                                        $template_content = preg_replace('/<!-- stc:(.*)-->/Uis', '', $template_content);
                                        // get all the placeholders available in the template content
                                        $answer = preg_match_all('/{{(.*)}}/Uis', $template_content, $matches);
                                        // do we have a repeatable block in the template content
                                        if (in_array("{{start_content}}", $matches[0]) && in_array("{{end_content}}", $matches[0])) {
                                                $begin = strpos($template_content, "{{start_content}}");
                                                $end = strpos($template_content, "{{end_content}}");
                                                $header = substr($template_content, 0, $begin);
                                                $body = substr($template_content, $begin+strlen("{{start_content}}"), $end-$begin-strlen("{{start_content}}"));
                                                $footer = substr($template_content, $end+strlen("{{end_content}}"));
                                        }
                                        $content .= $header;
                                        $content = str_replace("{{blog_title}}", get_bloginfo( 'name' ), $content);
                                        $content .= $body;
                                        $content = str_replace("{{post_title}}", esc_attr( $singlePost['post']->post_title ), $content);
                                        $content = str_replace("{{post_title_with_perma_link}}", '<a class="stc-post-title-a" href="' . get_permalink( $singlePost['post_id'] ) . '">' . esc_attr( $singlePost['post']->post_title )  . '</a>', $content);
                                        $content = str_replace("{{post_author}}", esc_attr( get_the_author_meta('display_name', $singlePost['post']->post_author )), $content);
                                        $cnt = get_the_post_thumbnail($singlePost['post']);
                                        if (empty($cnt)) {
                                                $content = str_replace("{{post_featured_image}}", __("No featured image assigned to post", 'subscribe-to-category' ), $content);
                                        } else {
                                                $content = str_replace("{{post_featured_image}}", $cnt, $content);
                                                $content = str_replace("{{post_featured_image_url}}", get_the_post_thumbnail_url($singlePost['post']), $content);
                                        }
                                        $cnt = apply_filters( 'the_content', $this->string_cut( $singlePost['post']->post_content, apply_filters( 'stc_message_length_sum_of_words', $sum_of_words) , apply_filters( 'stc_message_link_to_post_html', $output['link_to_post'])));
                                        $content = str_replace("{{post_content}}", $cnt, $content);
                                        $cnt = apply_filters( 'the_excerpt', get_the_excerpt($singlePost['post']));
                                        $content = str_replace("{{post_excerpt}}", $cnt, $content);
                                        if (strstr($cnt, "</p>")) {$cnt = str_replace("</p>", "", $cnt) . $output['link_to_post'] . "</p>";}
                                        $content = str_replace("{{post_excerpt_with_read_more_link}}", $cnt, $content);
                                        $content = str_replace("{{search_reason}}", $singlePost['reason'] , $content);
                                } else if ($key > 0 && $key <= count($multiplePosts)) {
                                        $content .= $body;
                                        $content = str_replace("{{post_title}}", esc_attr( $singlePost['post']->post_title ), $content);
                                        $content = str_replace("{{post_title_with_perma_link}}", '<a class="stc-post-title-a" href="' . get_permalink( $singlePost['post_id'] ) . '">' . esc_attr( $singlePost['post']->post_title )  . '</a>', $content);
                                        $content = str_replace("{{post_author}}", esc_attr( get_the_author_meta('display_name', $singlePost['post']->post_author )), $content);
                                        $cnt = get_the_post_thumbnail($singlePost['post']);
                                        if (empty($cnt)) {
                                                $content = str_replace("{{post_featured_image}}", __("No featured image assigned to post", 'subscribe-to-category' ), $content);
                                        } else {
                                                $content = str_replace("{{post_featured_image}}", $cnt, $content);
                                                $content = str_replace("{{post_featured_image_url}}", get_the_post_thumbnail_url($singlePost['post']), $content);
                                        }
                                        $cnt = apply_filters( 'the_content', $this->string_cut( $singlePost['post']->post_content, apply_filters( 'stc_message_length_sum_of_words', $sum_of_words) , apply_filters( 'stc_message_link_to_post_html', $output['link_to_post'])));
                                        $content = str_replace("{{post_content}}", $cnt, $content);
                                        $cnt = apply_filters( 'the_excerpt', get_the_excerpt($singlePost['post']));
                                        $content = str_replace("{{post_excerpt}}", $cnt, $content);
                                        if (strstr($cnt, "</p>")) {$cnt = str_replace("</p>", "", $cnt) . $output['link_to_post'] . "</p>";}
                                        $content = str_replace("{{post_excerpt_with_read_more_link}}", $cnt, $content);
                                        $content = str_replace("{{search_reason}}", $singlePost['reason'] , $content);
                                }
                                if ($key == count($multiplePosts)-1) {
                                        $content .= $footer;
                                        $content = str_replace("{{draw_line}}", '<hr class="stc-notify-hr">', $content);
                                        $content = str_replace("{{unsubscribe}}", $output['unsubscribe'], $content);
                                        echo $content;
                                }
                        } else {
                                do_action( 'stc_before_message_title', $singlePost['post_id'], $email['subscriber_id'] );
                                // @codingStandardsIgnoreLine because no user input is involved and we want to send html code.
                                $content .= apply_filters( 'stc_message_title_html', $output['title'], $singlePost['post_id'], $email['subscriber_id'] );
                                do_action( 'stc_after_message_title', $singlePost['post_id'], $email['subscriber_id'] );
                                $content .= '<div>';
                                if ($sum_of_words > 0) {
                                        // @codingStandardsIgnoreLine because no user input is involved and we want to send html code.
                                        $content .= apply_filters( 'the_content', $this->string_cut( $singlePost['post']->post_content, apply_filters( 'stc_message_length_sum_of_words', $sum_of_words), apply_filters( 'stc_message_link_to_post_html', $output['link_to_post'])));
                                } else {
                                        $content .= '</div>';
                                }
                                $content .= '<hr>';
                                if ($key == count($multiplePosts)-1) {
                                        // @codingStandardsIgnoreLine because no user input is involved and we want to send html code.
                                        $content .= apply_filters( 'stc_message_unsubscribe_html', $output['unsubscribe'] );
                                        echo $content;
                                }
                                do_action( 'stc_after_message_content', $singlePost['post_id'], $email['subscriber_id'] );
                        }
                }
		do_action( 'stc_after_message', $email['post_id'], $email['subscriber_id'] );
                return $sms_content;
	}

	/**
	 * Cut a text string closest word on a given length.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $string The string.
	 * @param  int    $max_length Maximum length of the string.
	 * @return string
	 */
	private function string_cut( $string, $max_length, $more ) {

		if ( $max_length < 0 ) {
			return $string;
		}

		// remove shortcode, unwanted tags and <p></p> pairs
                $string = strip_shortcodes($string);
		$string = preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $string);
                $string = strip_tags($string, '<p><h4><h3><h2><h1><strong><i>');
		$string = str_replace("<p></p>", "", $string);
                
                //remove empty lines
                $answer = preg_split('//u', $string, null, PREG_SPLIT_NO_EMPTY);
                for ($i=0;$i<count($answer); $i++) {
                     if (ord($answer[$i]) == 10) { unset($answer[$i]);}
                }
                
                // recreate the string but cutting on multybyte characters 
                $string = "";
                if (count($answer) >= $max_length) {$cut = true;} else {$cut = false;}
                $counter=0;
                foreach($answer as $chr) {
                        if ($cut && ++$counter >= $max_length) {
                                break;}
                        $string .= $chr;
                }
                
                if (substr($string, strlen($string)-5, 4) === "</p>") {
                        $string = substr($string, 0, strlen($string)-5);
                }
                // check if we have an unclosed html tag. If so we remove the unclosed tag from the string
                preg_match_all('~<([^<]*)~', $string, $matches);
                if ($matches[0][sizeof($matches[0])-1][0] === '<' && is_bool(strpos($matches[0][sizeof($matches[0])-1], ">"))) {
                        unset($matches[0][sizeof($matches[0])-1]);
                        $string = implode($matches[0]);
                }

              	return force_balance_tags($string) . __('[&hellip;]', 'subscribe-to-category' ) . $more;                
	}

	/**
	 * Get all subscribers with subscribed categories
	 *
	 * @since  1.0.0
	 *
	 * @return object Subscribers
	 */
	private function get_subscribers() {

		$args = array(
			'post_type' => 'stc',
			'numberposts' => -1,
			'post_status' => 'publish',
		);

		$stc = get_posts( $args );

		$subscribers = array();
		foreach ( $stc as $s ) {

                        // pickup the subscribed taxonomies and create term_id array's for each selected name
                        // because all the term_id's are unique we just add the to the category array
			$s->categories = array();
                        if (isset($this->settings['taxonomies'])) {
                                foreach ($this->settings['taxonomies'] as $taxName) {
                                        $tax = get_the_terms( $s->ID, $taxName );
                                        if ($tax) {foreach ( $tax as $t ) {
                                                $s->categories[] = $t->term_id;
                                        }}
                                }
                                
                        }
                        // pickup the mobile phone number and SMS status
                        $s->mobile_phone = get_post_meta($s->ID, "_stc_subscriber_mobile_phone", true);
                        $s->mobile_phone_status = get_post_meta($s->ID, "_stc_subscriber_mobile_phone_status", true);
                        // pickup subscription notifications
                        $s->notifications = array();
                        $nots = explode(',', get_post_meta($s->ID, "_stc_subscriber_notifications", true));
                        if (isset($nots[0]) && $nots[0] === "") $nots[0] = "STC";
                        foreach ($nots as $not) {
                                $s->notifications[$not] = $not;
                        }
                        $subscribers[$s->ID] = $s;
		}

		return $subscribers;
	}

     
        /**
	 * Register custom post type for subscribers
	 *
	 * @since  1.0.0
	 */
	public function register_post_type() {
                
                global $wpdb, $wp_taxonomies;

                // add the update_count_callback to catagory and all custom taxonomies
                foreach($wp_taxonomies as $tax) {
                        switch ($tax->name) {
                              case 'post_tag':
                              case 'nav_menu':
                              case 'link_category':
                              case 'post_format':
                              case 'ngg_tag' : break;
                              default : $wp_taxonomies[$tax->name]->update_count_callback = 'stc_update_count_callback';
                        }
                }

                register_post_type( 'notifications', array(
                        'labels' => array(
                                'name' => __( 'Notifications', 'subscribe-to-category' ),
                                'singular_name' => __( 'Notification', 'subscribe-to-category' ),
        			'view_item' => __( 'View notification', 'subscribe-to-category' ),
        			'menu_name' => __( 'Notifications', 'subscribe-to-category' )
                        ),
                        'public' => false,
                        'has_archive' => true,
                        'rewrite' => array('slug' => 'notifications'),
                        'show_in_rest' => true,
                        'menu_icon' => 'dashicons-email-alt',
                        'show_ui' => true,
                        'show_in_menu' => 'stc-subscribe-settings',
                        'show_in_nav_menus' => true,
                        'publicly_queryable' => true,
                        'exclude_from_search' => true,
                        'query_var' => true,
                        'can_export' => true,
                        'capability_type' => 'post',
                        'supports' => array( 'title', 'editor', 'custom-fields'  ),
                   )
                );
                
                // create the demo e-mail notification lay-out if it ia not there.
                $result = $wpdb->get_row( "SELECT ID FROM $wpdb->posts WHERE post_name like('stc-demo-e-mail-template-v2-3%') AND post_type = 'notifications'" );
                if (!isset($result)) {
                        $this->insertNotificationPost("stc-demo-e-mail-template-v2-3", "STC Demo E-mail Template V2.3",
'<!-- wp:html -->
<style> .stc-notify-hr { width:100%;} .stc-read-more-a { color: #000000} .stc-post-title-a { color: #000000;} .stc-unsubscribe-a { color: #000000;} </style>
<table style="width: 100%; max-width: 800px; border-collapse: collapse;">
<tbody><tr><td width="100%"><img src="https://www.vandestouwe.com/wp-content/uploads/2020/10/Patersdorf.jpg" width="100%" alt="" style="display: block;"></td></tr>
<tr><td width="100%" style=" text-align: center;"><h1>{{blog_title}}</h1></td></tr>
<tr><td>{{draw_line}}</td></tr>
<tr><td>
<!-- stc:This is the start of a repeatable content section -->{{start_content}}
Triggered by: {{search_reason}}<br>
<h2>{{post_title_with_perma_link}}</h2>
{{post_content}}
<!-- stc:This is the end of a (repeatable) content section -->{{end_content}}
</td></tr></tbody></table><br>
<table style="width: 100%; max-width: 800px; border-collapse: collapse;"><tbody><tr style="height: 75px;  background-color: blanchedAlmond;">
<td width="35%" style="padding-left:10px;  text-align: left;">© 2020 - Sidney van de Stouwe</td>
<td width="30%" style="text-align: center;">{{unsubscribe}}</td>
<td width="35%" style="padding-right: 10px; text-align: right; "><a href="mailto:onzereisverhalen@gmail.com" style="color: #000000;">Email Us</a></td>
</tr></tbody></table>
<!-- /wp:html -->'
                ); }

                // create the basic e-mail notification lay-out if it ia not there.
                $result = $wpdb->get_row( "SELECT ID FROM $wpdb->posts WHERE post_name like('stc-basic-e-mail-template-v2-3%') AND post_type = 'notifications'" );
                if (!isset($result)) {
                        $this->insertNotificationPost("stc-basic-e-mail-template-v2-3", "STC Basic E-Mail Template V2.3",
'<!-- wp:html -->
<style> .stc-notify-hr { width:100%;} .stc-read-more-a { color: #000000} .stc-post-title-a { color: #000000;} .stc-unsubscribe-a { color: #000000;} </style>
<p>Blog Title: {{blog_title}}</p>
<!-- stc: Mandatory command marks start of content -->{{start_content}}
<!-- stc: Command to draw a line separator -->{{draw_line}}
<p>Notification: {{search_reason}}</p>
<p>Title: {{post_title}}</p>
<p>Title with link: {{post_title_with_perma_link}}</p>
<p>Featured Image: {{post_featured_image}}</p>
<p>Content: {{post_content}}</p>
<p>Excerpt: {{post_excerpt}}</p>
<p>Excerpt: {{post_excerpt_with_read_more_link}}</p>
<!-- stc: Mandatory command marks end of content -->{{end_content}}
<!-- stc: Command to draw a line separator -->{{draw_line}}
<p>Unsubscribe link: {{unsubscribe}}</p>
<!-- /wp:html -->'
                ); }
                
		$labels = array(
			'name' => __( 'Subscribers', 'subscribe-to-category' ),
			'singular_name' => __( 'Subscribe', 'subscribe-to-category' ),
			'add_new' => __( 'Add new subscriber', 'subscribe-to-category' ),
			'add_new_item' => __( 'Add new subscriber', 'subscribe-to-category' ),
			'edit_item' => __( 'Edit subscriber', 'subscribe-to-category' ),
			'new_item' => __( 'New subscriber', 'subscribe-to-category' ),
			'view_item' => __( 'View subscriber', 'subscribe-to-category' ),
			'search_items' => __( 'Search subscribers', 'subscribe-to-category' ),
			'not_found' => __( 'Not found', 'subscribe-to-category' ),
			'not_found_in_trash' => __( 'Nothing found in trash', 'subscribe-to-category' ),
			'menu_name' => __( 'Subscribers', 'subscribe-to-category' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'supports' => array( 'title' ),
			'public' => false,
			'menu_icon' => 'dashicons-groups',
			'show_ui' => true,
			'show_in_menu' => 'stc-subscribe-settings',
			'show_in_nav_menus' => true,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'has_archive' => false,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'taxonomies' => array( 'post_tag', 'category'),
		);
                register_post_type( 'stc', $args );
                
                // Identify all the available Taxonomies (public and not _builtin)
                $args=array( 'public'   => true, '_builtin' => false );
                $output = 'objects';
                $operator = 'and';
                $this->taxonomies = get_taxonomies($args,$output,$operator);

                // Identify all the available Custom Post Types (public and not _builtin)
                $args=array( 'public'   => true, '_builtin' => false );
                $output = 'objects';
                $operator = 'and';
                $this->customPostTypes = get_post_types($args,$output,$operator);

                foreach( $this->customPostTypes as $custp) {
                    if ($custp->name != 'manage_cpt_template') {
                        // for the Custom Post Type enabled in STC Setting
                        if (isset($this->settings['cpt'][$custp->name])) {
                            add_post_type_support($custp->name, array( 'title', 'editor', 'custom-fields' ));
                            // attach to each post type the identifioed and selected taxonomies
                            if (isset($this->settings['taxonomies'])) {
                                foreach ($this->settings['taxonomies'] as $taxo) {
                                       register_taxonomy_for_object_type($taxo, $custp->name);
                                }
                           }
                        }
                    }
                }
                
                // add the stc plugin to the selected taxonomies and remove the stc plugin from taxonomies whom became unselected
                if (isset($this->settings['taxonomies'])){foreach( $this->taxonomies as $taxo) {
                        if (isset($this->settings['taxonomies'][$taxo->name])) {
                                $taxo -> object_type['stc'] = 'stc';
                        } else {
                                unset($taxo-> object_type['stc']);
                        }
                }}
                  
                //add a custom statussus to make sure we do not use this subscriber before it is acknowledge by the subscriber
                register_post_status( 'approval', array(
                    'label'                     => _x('Approval ', 'post status label', 'bznrd' ),
                    'public'                    => true,
                    'label_count'               => _n_noop( 'Approval <span class="count">(%s)</span>', 'Approvals <span class="count">(%s)</span>', 'subscribe-to-category' ),
                    'post_type'                 => array( 'stc' ), // Define one or more post types the status can be applied to.
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_metabox_dropdown'  => true,
                    'show_in_inline_dropdown'   => true,
                    'dashicon'                  => 'dashicons-businessman',
                ) );                
                register_post_status( 'update_approval', array(
                    'label'                     => _x( 'Update Approval ', 'post status label', 'bznrdua' ),
                    'public'                    => true,
                    'label_count'               => _n_noop( 'Update Approval <span class="count">(%s)</span>', 'Update Approvals <span class="count">(%s)</span>', 'subscribe-to-category' ),
                    'post_type'                 => array( 'stc' ), // Define one or more post types the status can be applied to.
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_metabox_dropdown'  => true,
                    'show_in_inline_dropdown'   => true,
                    'dashicon'                  => 'dashicons-businessman',
                ) );                
                register_post_status( 'marked', array(
                    'label'                     => _x( 'Marked ', 'post status label', 'bznrdmrk' ),
                    'public'                    => true,
                    'label_count'               => _n_noop( 'Marked <span class="count">(%s)</span>', 'Marked <span class="count">(%s)</span>', 'subscribe-to-category' ),
                    'post_type'                 => array( 'stc' ), // Define one or more post types the status can be applied to.
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'show_in_metabox_dropdown'  => true,
                    'show_in_inline_dropdown'   => true,
                    'dashicon'                  => 'dashicons-businessman',
                ) );                
	}
        
        /**
	 * Insert Post in the notification post type 
	 *
	 * @since  2.4.11
         * 
         * 
	 */
        private function insertNotificationPost ($slug, $title, $content) {
                wp_insert_post( array(
                        'post_title'    => $title,
                        'post_content'  => $content, //'<p>At the beginning of the notification message</p>',
                        'post_status'   => 'publish',
                        'post_name'     => $slug,
                        'post_type'     => 'notifications'
                ));
        }
        
        /**
	 * Register Custom columns
	 *
	 * @since  2.1.7
	 */
        public function set_book_table_columns($columns) {
            $new = array();
            $new['cb'] = $columns['cb'];
            $new['title'] = $columns['title'];
            // only add mobile number and SMS status when it is enabled on the STC setttings page.
            if (isset($this->settings['enable_sms_notification'])) {
                    $new['mobile_number'] = __( 'Mobile Number', 'subscribe-to-category');
                    $new['sms_status'] = __( 'SMS Status', 'subscribe-to-category');
            }
            $new['post_status'] = __( 'STC Status', 'subscribe-to-category');
            $new['categories'] = $columns['categories'];
            // add the taxonomies label to the admin column headers
            foreach( $this->taxonomies as $taxo) {
                if (isset($this->settings['taxonomies'][$taxo->name])) {
                    $new['taxonomy-'.$taxo->name] = $taxo -> label;
                }
            }
            $new['date'] = $columns['date'];
            return $new;
        }

        /**
	 * Register Custom columns
	 *
	 * @since  2.4.9
	 */
        public function set_notification_table_columns($columns) {
            $new = array();
            $new['cb'] = $columns['cb'];
            $new['title'] = $columns['title'];
            $new['post_status'] = __( 'Notification Status', 'subscribe-to-category');
            $new['date'] = $columns['date'];
            return $new;
        }
        
        /**
	 * Populate Custom columns
	 *
	 * @since  2.1.7
	 */
        public function custom_stc_column( $column, $post_id ) {
            switch ( $column ) {
                case 'sms_status' :
                        echo get_post_meta($post_id, "_stc_subscriber_mobile_phone_status", true);
                        break;
                case 'mobile_number' :
                        echo get_post_meta($post_id, "_stc_subscriber_mobile_phone", true);
                        break;
                case 'post_status' :
                    $terms = get_post_status( $post_id );
                    if ( is_string( $terms ) )
                        echo $terms;
                    else
                        _e( 'Unable to get stc status', 'subscribe-to-category');
                break;
            }
        }

        /**
	 * Populate Custom columns
	 *
	 * @since  2.4.8
	 */
        public function custom_notifications_column( $column, $post_id ) {
            switch ( $column ) {
                case 'post_status' :
                    $terms = get_post_status( $post_id );
                    if ( is_string( $terms ) )
                        echo $terms;
                    else
                        _e( 'Unable to get notification status', 'subscribe-to-category');
                break;
            }
        }

        /**
	 * Callback when input field of discription form is changed
	 *
	 * @since  2.1.7
	 */
        public function stc_get_results_callback() {
                global $wpdb;
                
                $cats = array();
                $email = $_POST['email'];
                $stc_original_subscriber_id_hidden = $_POST['original_id'];
                $result = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s AND post_status <> 'trash' ", $email, 'stc' ) );
                if (isset($result)) {
                        if (get_post_status($result->ID) == 'publish' || get_post_status($result->ID) == 'update_approval') {$cats['user_state'] = 'exsist';} else {$cats['user_state'] = 'approval';}
                        
                        // getting the subscribers mobile phone number
                        $cats['stc_mobile_phone'] = get_post_meta($result->ID, "_stc_subscriber_mobile_phone", true);
                        switch (get_post_meta($result->ID, "_stc_subscriber_mobile_phone_status", true)) {
                                case "pending" : $cats['stc_mobile_phone_status'] = "join pending"; break;
                                case "error"   : $cats['stc_mobile_phone_status'] = "sms send error"; break;
                                case "rejected": $cats['stc_mobile_phone_status'] = "sms rejected"; break;
                                case "unknown" : $cats['stc_mobile_phone_status'] = "unknown"; break;
                                case "joined"  : $cats['stc_mobile_phone_status'] = "joined"; break;
                                case "new"     : $cats['stc_mobile_phone_status'] = "new"; break;
                                case "stopped" : $cats['stc_mobile_phone_status'] = "stopped"; break;
                                default        : $cats['stc_mobile_phone_status'] = "-"; break;
                        }
                        // check if we have the id of a logged in person
                        if ($stc_original_subscriber_id_hidden > 0 ) {
                                file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $stc_original_subscriber_id_hidden . "-status.txt" , $cats['stc_mobile_phone_status'] . "|" . $email);                                        
                        } else {
                                file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $result->ID . "-status.txt" , $cats['stc_mobile_phone_status'] . "|" . $email);
                        }
                        $cats['stc_subscriber_id'] = $result->ID;

                        // getting the keywords
                        $cats['keywords'] = get_post_meta($result->ID, "_stc_subscriber_keywords", true);
                        // getting all the search area checkbox values
                        $search_areas_checked = explode(',', get_post_meta($result->ID, "_stc_subscriber_search_areas", true));
                        foreach ($this->possible_areas as $key=>$area) {
                                if (in_array($area['name'], $search_areas_checked)) {
                                        $this->possible_areas[$key]['status'] = "checked";
                                        $cats['search_areas'][] = $this->possible_areas[$key];
                                }
                        }
                        // getting the notifications
                        $notifications = explode(',', get_post_meta($result->ID, "_stc_subscriber_notifications", true));
                        if (isset($notifications[0]) && $notifications[0] === "") $notifications[0] = "STC";
                        foreach ($this->possible_moments as $key=>$not) {
                                if (in_array($not['name'], $notifications)) {
                                        $this->possible_moments[$key]['status'] = "checked";
                                        $cats['notifications'][] = $this->possible_moments[$key];
                                }
                        }
                        // getting all the taxonomies of this STC post
                        if (isset($this->settings['taxonomies'])) {
                                foreach ($this->settings['taxonomies'] as $taxName) {
                                        $taxonomies = get_the_terms($result->ID, $taxName);
                                        if (!is_bool($taxonomies))foreach($taxonomies as $taxo) {
                                                $cats['categories'][] = $taxo;
                                        }
                                }
                        }
                } else {
                        $cats['stc_subscriber_id'] = "";
                        $cats['user_state'] = 'new';
                        $cats['stc_mobile_phone'] = "";
                        $cats['stc_mobile_phone_status'] = "-";
                        $cats['keywords'] = "";
                        file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . "-status.txt" , $cats['stc_mobile_phone_status']);                                        
                }
                echo json_encode($cats);
                die();
        }
        
        /**
	 * email template is required in the notification e-mail
	 *
	 * @since  2.4.16
	 */
        private function stc_notify_email_template($template, $title, $content, $unsubscribe, $justify = "left", $reason = "") {
                $htmlCode = apply_filters('the_content', $this->get_html_content($template));
                $htmlCode = str_replace("{{post_title}}", $title, $htmlCode);
                if ($justify === 'center') {$htmlCode = str_replace('text-align: left">{{post_content}}', 'text-align: center;">{{post_content}}', $htmlCode);}
                $htmlCode = str_replace("{{post_content}}", $content, $htmlCode);
                $htmlCode = str_replace("{{unsubscribe}}", $unsubscribe, $htmlCode);
                $htmlCode = str_replace("{{search_reason}}", $reason, $htmlCode);
                echo $htmlCode;
        }

        /**
	 * After content is required in the notification e-mail
	 *
	 * @since  2.4.9
	 */
        private function get_html_content($notification_title) {
                global $wpdb;
                $result = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'notifications' AND post_status = 'publish' ", $notification_title ) );
                if (isset($result)) {
                        return get_post_field('post_content', $result->ID);
                }
                return "";
        }

        /**
	 * insert emailnotification data in joblist
	 *
	 * @since  2.5.3
	 */
        private function insert_email_in_joblist($day, $email ) {
                global $wpdb;

                $table_name = $wpdb->prefix . "stc_joblist";

                $wpdb->insert($table_name, array( 'day' => $day,
                                                  'subscriber_id' => $email['subscriber_id'],
                                                  'mobile_phone' => $email['mobile_phone'],
                                                  'mobile_phone_status' => $email['mobile_phone_status'],
                                                  'hash' => $email['hash'],
                                                  'email' => $email['email'],
                                                  'post_id' => $email['post_id'],
                                                  'reason' => $email['reason'],
                                                  'merged' => $email['merged']));
        }        

        /**
	 * return th ejoblist of email notifications for a perticular day
	 *
	 * @since  2.5.3
	 */
        private function retrieve_email_in_joblist($day) {
                global $wpdb;

                // select the email notifion jobs that must be processed today: present day of the week and Daily
                $sql = "SELECT * FROM {$wpdb->prefix}stc_joblist where `day` = 'Daily' or `day` = '" . $this->possible_moments[$day]['name'] . "';";
      		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
                // delete the records that where just received from the select query
                $wpdb->query("DELETE FROM {$wpdb->prefix}stc_joblist where `day` = 'Daily' or `day` = '" . $this->possible_moments[$day]['name'] . "';");
                return $result;
        }        
}
