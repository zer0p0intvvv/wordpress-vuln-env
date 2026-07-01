<?php
/**
 *
 * Class for the settings page
 *
 * @author Sidney van de Stouwe <sidney@vandestouwe.com>
 * @package subscribe-to-category
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( class_exists( 'STC_Settings' ) ) {
	$stc_setting = new STC_Settings();
}

/**
 *
 * STC Settings class
 */
class STC_Settings {

	/**
	 * Holds the values to be used in the fields callbacks.
	 *
	 * @var array
	 */
	private $options;

        /**
	 * Holds value for filter export categories.
	 *
	 * @var array
	 */
	private $export_in_categories = array();

        /**
	 * Holds value for filter import categories and CSV file name.
	 *
	 * @var array
	 */
	private $import_in_categories = array();
	private $import_file_name = "";

        /**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		// only in admin mode.
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Ajax call for sendings emails manually.
			add_action( 'wp_ajax_force_run', array( $this, 'force_run' ) );

		}

	}

	/**
	 * Ajax call for trigger send action manually
	 *
	 * @since  1.1.0
	 */
	public function force_run() {
		check_ajax_referer( 'ajax_nonce', 'nonce' );

		$subscriber = STC_Subscribe::get_instance();
		$subscriber->stc_send_email();
		esc_html_e( 'Scheduled event successfully executed', 'subscribe-to-category' );
		die();
	}

	/**
	 * Add options page
	 *
	 * @since  1.0.0
	 */
	public function add_plugin_page() {

		if ( isset( $_POST['action'] ) ) {
			wp_verify_nonce( sanitize_key( $_POST['action'] ) );
		}
		if ( isset( $_POST['action'] ) && 'export' === sanitize_key( $_POST['action'] ) ) {
			// listen for filter by categories.
			if ( isset( $_POST['in_categories'] ) && ! empty( $_POST['in_categories'] ) ) {
				$this->export_in_categories = array_map( 'sanitize_text_field', wp_unslash( $_POST['in_categories'] ) );
			}
			$this->export_to_excel();
		}
		if ( isset( $_POST['action'] ) && 'import' === sanitize_key( $_POST['action'] ) ) {
			// listen for filter by categories.
			if ( isset( $_POST['in_categories'] ) && ! empty( $_POST['in_categories'] ) ) {
				$this->import_in_categories = array_map( 'sanitize_text_field', wp_unslash( $_POST['in_categories'] ) );
			}
                        // check and wait if the subscriber list is uploaded
                        $wait_cycles = 0;
                        while (!isset( $_FILES['uploadedfile']) || $wait_cycles > 10) {sleep(5); $wait_cycles++;}
                        if ( isset( $_FILES['uploadedfile'] ) ) {
                            $this->import_file_name =  $_FILES['uploadedfile']['tmp_name'][0]; 
                            $this->import_from_csv();
                        }
		}

                
		add_menu_page(
			__( 'Subscribe to Category', 'subscribe-to-category' ),
			__( 'STC Subscribe', 'subscribe-to-category' ),
			'manage_options',
			'stc-subscribe-settings',
			array($this, 'create_stc_settings_page'),
                        'dashicons-email-alt',
                        81
		);
		add_submenu_page(
			'stc-subscribe-settings',
                        __( 'Settings', 'subscribe-to-category' ),
			__( 'Settings', 'subscribe-to-category' ),
			'manage_options',
			'stc-subscribe-settings',
			array( $this, 'create_stc_settings_page' ),
                        0
		);

	}

        
        /**
	 * Settings page callback
	 *
	 * @since  1.0.0
	 */
	public function create_stc_settings_page() {

                // show error if they occured
                settings_errors();
                
		// Set class property.
		$this->options = get_option( 'stc_settings' );
                if (isset($this->options['daily_emails'])) {
                        $time_in_seconds_i18n = strtotime( date_i18n( 'Y-m-d H:i:s' ) ) + self::get_next_cron_time( 'stc_schedule_email_daily' );
                } else {
                        $time_in_seconds_i18n = strtotime( date_i18n( 'Y-m-d H:i:s' ) ) + self::get_next_cron_time( 'stc_schedule_email' );
                }
		$next_run = gmdate( 'Y-m-d H:i', $time_in_seconds_i18n );
                
                  //Get the active tab from the $_GET param
		?>
                <div class="wrap">
		<h2><?php esc_html_e( 'Settings for subscribe to category', 'subscribe-to-category' ); ?></h2>       


		<table style="background-color:blanchedalmond; font-size: 14px;">
		  <tbody>
			<tr>
			  <td class="desc"><strong><?php esc_html_e( 'Schedule: ', 'subscribe-to-category' ); ?></strong> <?php
                          if (isset($this->options['daily_emails'])) {
                                esc_html_e( 'E-mail is scheduled to be sent once every day at hh:mm local time.', 'subscribe-to-category' );
                          } else {
                                // @codingStandardsIgnoreLine because no user input is involved and we want to send html code
				printf( __( 'E-mail is scheduled to be sent once every <strong>%1$s minutes</strong>.', 'subscribe-to-category' ), $this->options['cron_time']/60 );
                          }
                          ?></td>
			  <td class="desc"></td>
			</tr>
			<tr style="line-height: 30px;">
			<?php /* translators: %s: time of send mail, number of post to send */ ?>
			<td valign="middle">
			<?php
                                // @codingStandardsIgnoreLine because no user input is involved and we want to send html code
				printf( __( 'Next run is going to be <strong>%1$s</strong> and will include %2$s posts.', 'subscribe-to-category' ), esc_attr( $next_run ), '<span id="stc-posts-in-que">' . esc_attr( $this->get_posts_in_que() ) . '</span>' );
                                ?> <button type="submit" id="stc-force-run" class="button button-primary" style="line-height: 30px;"><?php esc_html_e( 'Click here to run this action right now', 'subscribe-to-category' ); ?></button>
			</td>
			</tr>
		  </tbody>
		</table>
		<form method="post" action="options.php">
		<?php
			// print out all hidden setting fields.
			settings_fields( 'stc_option_group' );
			do_settings_sections( 'stc-subscribe-settings' );
			do_settings_sections( 'stc-cpt-settings' );
			do_settings_sections( 'stc-taxonomy-settings' );
			do_settings_sections( 'stc-notification-settings' );
			do_settings_sections( 'stc-attribute-settings' );
			do_settings_sections( 'stc-cron-settings' );
			do_settings_sections( 'stc-resend-settings' );
			do_settings_sections( 'stc-style-settings' );
			do_settings_sections( 'stc-deactivation-settings' );
			submit_button();
		?>
		</form>
		<?php $this->export_to_excel_form(); ?>
		<?php $this->import_from_csv_form(); ?>

	  </div>
		<?php
	}

	/**
	 * Get current posts in que to be sent
	 *
	 * @since  1.0.0
	 *
	 * @return int sum of posts
	 */
	private function get_posts_in_que() {

		// get posts with a post meta value in outbox.
		$meta_key = '_stc_notifier_status';
		$meta_value = 'outbox';

		$args = array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_key'    => $meta_key,
			'meta_value'  => $meta_value,
		);

                // collect all 'posts' with meta data containing 'outbox'
		$posts = get_posts( $args );

                // look also in the custom posts for meta data containing 'outbox'
                if (isset($this->options['cpt'])) {
                    foreach ($this->options['cpt'] as $custp) {
                        $args['post_type'] = $custp;
                        $psts = get_posts( $args );
                        foreach($psts as $ps) {
                            $posts[] = $ps;
                        }
                    }
                }
		return count( $posts );
	}

	/**
	 * Returns the time in seconds until a specified cron job is scheduled.
	 *
	 * @since  1.0.0
	 * @param string $cron_name the cron event for stc.
	 */
	public static function get_next_cron_time( $cron_name ) {

		foreach ( _get_cron_array() as $timestamp => $crons ) {

			if ( in_array( $cron_name, array_keys( $crons ) ) ) {
				return $timestamp - time();
			}
		}

		return false;
	}

	/**
	 * Register and add settings
	 *
	 * @since  1.0.0
	 */
	public function register_settings() {

		// Email settings.
		add_settings_section(
			'setting_email_id', // ID.
			__( 'E-mail settings', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-subscribe-settings' // Page.
		);

		add_settings_field(
			'stc_email_from',
			__( 'E-mail from: ', 'subscribe-to-category' ),
			array( $this, 'stc_email_from_callback' ), // Callback.
			'stc-subscribe-settings', // Page.
			'setting_email_id' // Section.
		);

		add_settings_field(
			'stc_title',
			__( 'Email subject: ', 'subscribe-to-category' ),
			array( $this, 'stc_title_callback' ), // Callback.
			'stc-subscribe-settings', // Page.
			'setting_email_id' // Section.
		);

                add_settings_field(
			'stc_nr_content',
			__( 'Number of characters: ', 'subscribe-to-category' ),
			array( $this, 'stc_nr_content_callback' ), // Callback.
			'stc-subscribe-settings', // Page.
			'setting_email_id' // Section.
		);

		// Custom Post Type settings.
		add_settings_section(
			'setting_cpt_id', // ID.
			__( 'Custom Post Type Name(s) selection', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-cpt-settings' // Page.
		);

                add_settings_field(
			'stc_cpt',
			__( 'Custom Post Type Name: ', 'subscribe-to-category' ),
			array( $this, 'stc_cpt_callback' ), // Callback.
			'stc-cpt-settings', // Page.
			'setting_cpt_id' // Section.
		);

		// Taxonomy settings.
		add_settings_section(
			'setting_taxonomies_id', // ID.
			__( 'Taxonomy Name(s) selection', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-taxonomy-settings' // Page.
		);

                add_settings_field(
			'stc_categories',
			__( 'Categories on/off enabled: ', 'subscribe-to-category' ),
			array( $this, 'stc_category_callback' ), // Callback.
			'stc-taxonomy-settings', // Page.
			'setting_taxonomies_id' // Section.
		);

                add_settings_field(
			'stc_taxonomies',
			__( 'Taxonomy Name: ', 'subscribe-to-category' ),
			array( $this, 'stc_taxonomy_callback' ), // Callback.
			'stc-taxonomy-settings', // Page.
			'setting_taxonomies_id' // Section.
		);

		// Notification settings.
		add_settings_section(
			'setting_notifications_id', // ID.
			__( 'E-Mail Template selection for notification e-mails', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-notification-settings' // Page.
		);

                add_settings_field(
			'stc_notifications',
			__( 'E-Mail Template(s): ', 'subscribe-to-category' ),
			array( $this, 'stc_notification_callback' ), // Callback.
			'stc-notification-settings', // Page.
			'setting_notifications_id' // Section.
		);

                // Attribute settings.
		add_settings_section(
			'setting_attributes', // attribute settings
			__( 'Attribute settings', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-attribute-settings' // Page.
		);
                
                add_settings_field(
			'stc_hide_unsubscribe',
			__( 'Unsubscribe: ', 'subscribe-to-category' ),
			array( $this, 'stc_hide_unsubscribe_callback' ), // Callback.
			'stc-attribute-settings', // Page.
			'setting_attributes' // Section.
		);

                add_settings_field(
			'stc_enable_keywords',
			__( 'Keyword search: ', 'subscribe-to-category' ),
			array( $this, 'stc_enable_keyword_search_callback' ), // Callback.
			'stc-attribute-settings', // Page.
			'setting_attributes' // Section.
		);

                add_settings_field(
			'stc_taxonomy_hierarchy',
			__( 'Taxonomy hierarchy: ', 'subscribe-to-category' ),
			array( $this, 'stc_enable_taxonomy_hierarchy_callback' ), // Callback.
			'stc-attribute-settings', // Page.
			'setting_attributes' // Section.
		);

                add_settings_field(
			'stc_post_filter_query_name',
			__( 'Post Query Name: ', 'subscribe-to-category' ),
			array( $this, 'stc_post_filter_query_callback' ), // Callback.
			'stc-attribute-settings', // Page.
			'setting_attributes' // Section.
		);

                add_settings_field(
			'stc_sms_notification',
			__( 'SMS Notifications: ', 'subscribe-to-category' ),
			array( $this, 'stc_enable_sms_notification_callback' ), // Callback.
			'stc-attribute-settings', // Page.
			'setting_attributes' // Section.
		);
                
                // Cron settings.
		add_settings_section(
			'setting_cron_time', // cron time in seconds.
			__( 'Reschedule time for sending emails', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-cron-settings' // Page.
		);

		add_settings_field(
			'stc_cron_time_daily',
			__( 'Daily: ', 'subscribe-to-category' ),
			array( $this, 'stc_cron_time_daily_callback' ), // Callback.
			'stc-cron-settings', // Page.
			'setting_cron_time' // Section.
		);
                
                add_settings_field(
			'stc_cron_time',
			__( 'Time in Seconds: ', 'subscribe-to-category' ),
			array( $this, 'stc_cron_time_callback' ), // Callback.
			'stc-cron-settings', // Page.
			'setting_cron_time' // Section.
		);

                // Resend settings.
		add_settings_section(
			'setting_resend_id', // ID.
			__( 'Resend post on update', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-resend-settings' // Page.
		);

		add_settings_field(
			'stc_resend',
			__( 'Resend:', 'subscribe-to-category' ),
			array( $this, 'stc_resend_callback' ), // Callback.
			'stc-resend-settings', // Page.
			'setting_resend_id' // Section.
		);

		add_settings_field(
			'stc_editor',
			__( 'Block-Editor:', 'subscribe-to-category' ),
			array( $this, 'stc_editor_callback' ), // Callback.
			'stc-resend-settings', // Page.
			'setting_resend_id' // Section.
		);

		// Styleing settings.
		add_settings_section(
			'setting_style_id', // ID.
			__( 'Stylesheet (CSS) settings', 'subscribe-to-category' ), // Title.
			'', // array( $this, 'print_section_info' ), // Callback.
			'stc-style-settings' // Page.
		);

		add_settings_field(
			'stc_custom_css',
			__( 'Custom CSS: ', 'subscribe-to-category' ),
			array( $this, 'stc_css_callback' ), // Callback.
			'stc-style-settings', // Page.
			'setting_style_id' // Section.
		);

		// Deactivation settings.
		add_settings_section(
			'setting_deactivation_id', // ID.
			__( 'On plugin deactivation', 'subscribe-to-category' ), // Title.
			array( $this, 'section_deactivation_info' ), // Callback.
			'stc-deactivation-settings' // Page.
		);

		add_settings_field(
			'stc_remove_subscribers',
			__( 'Subscribers: ', 'subscribe-to-category' ),
			array( $this, 'stc_remove_subscribers_callback' ), // Callback.
			'stc-deactivation-settings', // Page.
			'setting_deactivation_id' // Section.
		);

		register_setting(
			'stc_option_group', // Option group.
			'stc_settings', // Option name.
			array( $this, 'input_validate_sanitize' ) // Callback function for validate and sanitize input values.
		);

	}

	/**
	 * Print outs text for deactivation info
	 *
	 * @since  1.0.0
	 */
	public function section_deactivation_info() {
		?>
	  <p><?php esc_html_e( 'The plugin will remove all data in database created by this plugin but there is an option regarding subscribers', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Sanitize setting fields
	 *
	 * @since  1.0.0
	 *
	 * @param array $input The input data from the form.
	 */
	public function input_validate_sanitize( $input ) {
		$output = array();
                global $cron_time;
                $options = get_option( 'stc_settings' );

		if ( isset( $input['email_from'] ) ) {

			// sanitize email input.
			$output['email_from'] = sanitize_email( $input['email_from'] );

			if ( ! empty( $input['email_from'] ) ) {
				if ( ! is_email( $output['email_from'] ) ) {
					add_settings_error( 'setting_email_id', 'invalid-email', __( 'You have entered an invalid email.', 'subscribe-to-category' ) );
				}
			} else {
                                
                        }
		}

		if ( isset( $input['title'] ) ) {
			$output['title'] = sanitize_text_field( $input['title'] );
		}

                if ( isset( $input['nr_words_in_content'] ) ) {
                        if ($input['nr_words_in_content'] === "") {
                                $nrwords = 130;
                                $input['nr_words_in_content'] = "130";
                        } else {
                                $nrwords = intval(sanitize_text_field( $input['nr_words_in_content']));
                        }
                        if ($nrwords < 0 || $nrwords > 1000) {
                                add_settings_error( 'nr_words_in_content', 'invalid-nr_words_in_content', __( 'Nr blog content characters included in the e-mail notification between 0 and 1000', 'subscribe-to-category' ) );
                        } else {
        			$output['nr_words_in_content'] = sanitize_text_field( $input['nr_words_in_content'] );
                        }
		}

		if ( isset( $input['cpt'] ) ) {
			$output['cpt'] = $input['cpt'];
		}
                
                

                if ( isset( $input['default cat off'] ) ) {
			$output['default cat off'] = $input['default cat off'];
		}
                

                if ( isset( $input['taxonomies'] ) ) {
			$output['taxonomies'] = $input['taxonomies'];
		}

		if ( isset( $input['post_filter_query'] ) ) {
			$output['post_filter_query'] = sanitize_text_field( $input['post_filter_query'] );
		}
                
                if ( isset( $input['notifications'] ) ) {
			$output['notifications'] = $input['notifications'];
		}

                if ( isset( $input['daily_time'] )) {
                        $val = strtotime("1-1-1970" . $input['daily_time']);
                        if (!$val && $val >= 0 && $val < 86400) {
                                add_settings_error( 'setting_cron_time', 'invalid-daily-time', __( 'Time must between 00:00 and 23:59', 'subscribe-to-category' ) );
                                if (isset($options['daily_time'])) {$output['daily_time'] = $options['daily_time'];} 
                        } else {
                                $output['daily_time'] = $input['daily_time'];
                        }
                }
                                
                if ( isset( $input['daily_emails'])) {
                        $output['daily_emails'] = $input['daily_emails'];
                        
                        // calculate the timestamp midnight adjusted to gmt
                        if (isset($this->options['daily_time']) && !isset($output['daily_time'])) {
                                $val = $this->options['daily_time'];
                        } else if (isset($output['daily_time'])) {
                                $val = $output['daily_time'];
                        }
                        if (isset($val)) {
                                $timeMidday = strtotime(current_time('Y-m-d')) - get_option('gmt_offset') * 3600 + strtotime("1-1-1970" . $val);
                                $timestmp = current_time('timestamp', true);                        
                                if ( $timeMidday > $timestmp) { $scheduleTime = $timeMidday; } else { $scheduleTime = $timeMidday + 24 * 3600; }
                                // clear the hook and set the new hook
                                wp_clear_scheduled_hook("stc_schedule_email_daily", array("Daily")); 
                                wp_schedule_event( $scheduleTime, 'daily', 'stc_schedule_email_daily', array("Daily"));
                        }
                }
                                
                if ( isset( $input['cron_time'] ) ) {
                        if (strlen($input['cron_time']) == 0) {
                                $input['cron_time'] = "3600";
                        }
                        $cron = intval(sanitize_text_field( $input['cron_time']));
                        if ($cron < 180 || $cron > 3600) {
                                add_settings_error( 'setting_cron_time', 'invalid-cron-time', __( 'Cron time advised > 180 and <= 3600', 'subscribe-to-category' ) );
                                if ($cron_time <> 3600 ) $output['cron_time'] = $cron_time; 
                        } else {
                            $output['cron_time'] = sanitize_text_field( $input['cron_time'] );
                            // only reset the scheduler if we have a new interval defined
                            if ($cron_time <> $cron) {
                                $cron_time = $cron;
                                // clear the hook and set the new hook
                                wp_clear_scheduled_hook("stc_schedule_email", array("Timer")); 
                                wp_schedule_event( time(), 'stc_reschedule_time', 'stc_schedule_email', array("Timer") );
                            }
                        }
		}

                if ( isset( $input['resend_option'] ) ) {
			$output['resend_option'] = $input['resend_option'];
		}

		if ( isset( $input['exclude_gutenberg'] ) ) {
			$output['exclude_gutenberg'] = $input['exclude_gutenberg'];
		}

		if ( isset( $input['hide_unsubscribe'] ) ) {
			$output['hide_unsubscribe'] = $input['hide_unsubscribe'];
		}

		if ( isset( $input['enable_keyword_search'] ) ) {
			$output['enable_keyword_search'] = $input['enable_keyword_search'];
		}

		if ( isset( $input['enable_taxonomy_hierarchy'] ) ) {
			$output['enable_taxonomy_hierarchy'] = $input['enable_taxonomy_hierarchy'];
		}

		if ( isset( $input['enable_sms_notification'] ) ) {
			$output['enable_sms_notification'] = $input['enable_sms_notification'];
		}

                if ( isset( $input['exclude_css'] ) ) {
			$output['exclude_css'] = $input['exclude_css'];
		}

                if ( isset( $input['deactivation_remove_subscribers'] ) ) {
			$output['deactivation_remove_subscribers'] = $input['deactivation_remove_subscribers'];
		}

		return $output;
	}

	/**
	 * Printing section text
	 *
	 * @since  1.0.0
	 */
	public function print_section_info() {
		esc_html_e( 'Add your E-mail settings', 'subscribe-to-category' );
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_email_from_callback() {
		$default_email = get_option( 'admin_email' );
		?>
		<input type="text" id="email_from_1" class="regular-text" name="stc_settings[email_from]" value="<?php echo isset( $this->options['email_from'] ) ? esc_attr( $this->options['email_from'] ) : ''; ?>" />
		<?php /* translators: %s: time of send mail, number of post to send */ ?>
		<p class="description"><?php printf( esc_html__( 'Enter the e-mail address for the sender, if empty the admin e-mail address %s is going to be used as sender.', 'subscribe-to-category' ), esc_attr( $default_email ) ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_title_callback() {
		?>
		<input type="text" id="email_from_2" class="regular-text" name="stc_settings[title]" value="<?php echo isset( $this->options['title'] ) ? esc_attr( $this->options['title'] ) : ''; ?>" />
		<p class="description"><?php esc_html_e( 'Enter e-mail subject for the e-mail notification, leave empty if you wish to use post title as email subject.', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_nr_content_callback() {
		$default_chr = 130;
		?>
		<input type="text" id="nr_words_1" class="regular-text" name="stc_settings[nr_words_in_content]" value="<?php echo isset( $this->options['nr_words_in_content'] ) ? esc_attr( $this->options['nr_words_in_content'] ) : ''; ?>" style="width: 60px;"/>
		<?php /* translators: %d: nr off characters to take from the post content */ ?>
		<p class="description"><?php printf( esc_html__( "Nr of characters of the blog's content, if empty number will be %d characters", 'subscribe-to-category' ), esc_attr( $default_chr) ); ?></p>
		<?php
        }

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_cpt_callback() {
		$custumPostType = array();
                // Identify all the available Custom Post Type (public and not _builtin)
                $args=array( 'public'   => true, '_builtin' => false );
                $output = 'objects';
                $operator = 'and';
                $custumPostType = get_post_types($args,$output,$operator);
                if ( count( $custumPostType ) > 0 ) {
                        foreach ( $custumPostType as $cuspt ) {
                                if ($cuspt->name != 'manage_cpt_template') {
                                ?><div class="checkbox">
                                        <label>
                                                <input type="checkbox" name="stc_settings[cpt][<?php echo $cuspt->name; ?>]" value="<?php echo esc_html( $cuspt->name ); ?>" <?php if (isset($this->options['cpt'][$cuspt->name])) checked( $cuspt->name, $this->options['cpt'][$cuspt->name]); else (""); ?>  >
                                                <?php echo esc_html( $cuspt->label ); ?>
                                        </label>
                                </div><?php
                                }
                        }
                } else {?>
    		    <p class="description"><?php esc_html_e( 'No public "Custom Post Types" available', 'subscribe-to-category' ); ?></p><?php
                }?>
		    <p class="description"><?php esc_html_e( 'Select required Custom Post Type names', 'subscribe-to-category' ); ?></p>
		<?php
        }

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.2.0
	 */
	public function stc_category_callback() {
		$options['default cat off'] = '';

		if ( isset( $this->options['default cat off'] ) ) {
			$options['default cat off'] = $this->options['default cat off'];
		}
		?>

	  <label for="category_option"><input type="checkbox" value="1" id="category_option" name="stc_settings[default cat off]" <?php checked( '1', $options['default cat off'] ); ?> > <?php esc_html_e( 'Enable category on/off', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Enable the possibility to turn categories on/off under Taxonomies settings', 'subscribe-to-category' ); ?></p>
	  <?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_taxonomy_callback() {
		$taxonomies = array();
                // Identify all the available Taxonomies (public)
                $args=array( 'public'   => true );
                $output = 'objects';
                $operator = 'and';
                $taxonomies = get_taxonomies($args,$output,$operator);
                if ( count( $taxonomies ) > 0 ) {
                        if (!isset($this->options['default cat off'])) {
                                $this->options['taxonomies']['category'] = 'category';
                                update_option('stc_settings', $this->options);
                        }
                        // remove tags and format
                        foreach($taxonomies as $key=>$taxo) {
                                if ($taxo->name == 'post_tag') {unset($taxonomies[$key]);}
                                if ($taxo->name == 'post_format') {unset($taxonomies[$key]);}
                        }
                        foreach ( $taxonomies as $taxo ) {
                                ?><div class="checkbox">
                                        <label>
                                                <input type="checkbox" name="stc_settings[taxonomies][<?php echo $taxo->name; ?>]" value="<?php echo esc_html( $taxo->name ); ?>" <?php if (isset($this->options['taxonomies'][$taxo->name])) checked( $taxo->name, $this->options['taxonomies'][$taxo->name]); else (""); ?>  >
                                                <?php echo esc_html( $taxo->label ); ?>
                                        </label>
                                </div><?php
                        }
                }  else {?>
		<p class="description"><?php esc_html_e( 'No public "Custom Taxonomies" available', 'subscribe-to-category' ); ?></p><?php
                }?>
		<p class="description"><?php esc_html_e( 'Select required taxonomies names', 'subscribe-to-category' ); ?></p>
		<?php
        }

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_notification_callback() {
                global $wpdb;
		$args = array(
			'post_type' => 'notifications',
			'post_status' => 'publish',
			'numberposts' => -1,
		);

                // collect all 'posts' from post type notificationd
		$posts = get_posts( $args );

                if ( count( $posts ) > 0 ) {
                        // we first need to check if the selected template is still available as notification post
                        if (isset($this->options['notifications'])) {foreach($this->options['notifications'] as $not) {
                                $available = false;
                                foreach($posts as $pst) {
                                        if ($pst->post_name == $not) {$available = true;}
                                }
                                if (!$available) {
                                        // the notification post is no longer there so we remove this as the selected template
                                        unset($this->options['notifications']);
                                        update_option('stc_settings', $this->options);

                                }
                        }}
                        foreach ( $posts as $not ) {
                                if ( $not->post_name != "stc-sms-notification-message"  && $not->post_name != "stc-sms-confirm-message") {
                                        ?><div class="stc-email-notification-checkboxes">
                                                <label>
                                                        <input type="checkbox" name="stc_settings[notifications][<?php echo $not->post_title; ?>]" value="<?php echo esc_html( $not->post_name ); ?>" <?php if (isset($this->options['notifications'][$not->post_title])) checked( $not->post_name, $this->options['notifications'][$not->post_title]); else (""); ?> >
                                                        <?php echo esc_html( $not->post_title ); ?>
                                                </label>
                                        </div><!-- .stc-email-notification-checkboxes -->
                                        <?php
                                }
                        }
                } else {?>
    		    <p class="description"><?php esc_html_e( 'No E-mail template available', 'subscribe-to-category' ); ?></p><?php
                }?>
		    <p class="description"><?php esc_html_e( 'Select required E-mail template', 'subscribe-to-category' ); ?></p>
		<?php
        }
        
	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  2.4.13
	 */
	public function stc_cron_time_daily_callback() {
		$options['daily_emails'] = '';

		if ( isset( $this->options['daily_emails'] ) ) {
			$options['daily_emails'] = $this->options['daily_emails'];
		}
		if ( isset( $this->options['weekdays'] ) ) {
			$options['weekdays'] = $this->options['weekdays'];
		}
		?>

	  <label for="daily_emails_option">
              <input type="checkbox" value="1" id="daily_emails_option" name="stc_settings[daily_emails]" <?php checked( '1', $options['daily_emails'] ); ?> >
              <input type="text" id="daily_time" class="regular-text" name="stc_settings[daily_time]" value="<?php echo isset( $this->options['daily_time'] ) ? esc_attr( $this->options['daily_time'] ) : ''; ?>" style="width: 60px; text-align: center;" placeholder="HH:MM" /> <?php esc_html_e( 'Local time', 'subscribe-to-category' ); ?>
	  <p class="description"><?php esc_html_e( 'Send email notification once per day at HH:MM (24h) local time', 'subscribe-to-category' ); ?></p>
		<?php
	}
        

        /**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_cron_time_callback() {
		$default_time = 3600;
		?>
		<input type="text" id="cron_time_1" class="regular-text" name="stc_settings[cron_time]" value="<?php echo isset( $this->options['cron_time'] ) ? esc_attr( $this->options['cron_time'] ) : ''; ?>" style="width: 60px;"/>
		<?php /* translators: %d: default reschedule time */ ?>
		<p class="description"><?php printf( esc_html__( 'Reschedule time in seconds, if empty the time will be %d seconds', 'subscribe-to-category' ), esc_attr( $default_time) ); ?></p>
		<?php
        }

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.2.0
	 */
	public function stc_resend_callback() {
		$options['resend_option'] = '';

		if ( isset( $this->options['resend_option'] ) ) {
			$options['resend_option'] = $this->options['resend_option'];
		}
		?>

	  <label for="resend_option"><input type="checkbox" value="1" id="resend_option" name="stc_settings[resend_option]" <?php checked( '1', $options['resend_option'] ); ?> > <?php esc_html_e( 'Enable resend post option', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Gives an option on edit post (in the publish panel) to resend a post on update.', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_editor_callback() {
		$options['exclude_gutenberg'] = '';

		if ( isset( $this->options['exclude_gutenberg'] ) ) {
			$options['exclude_gutenberg'] = $this->options['exclude_gutenberg'];
		}
		?>

	  <label for="exclude_gutenberg"><input type="checkbox" value="1" id="exclude_gutenberg" name="stc_settings[exclude_gutenberg]" <?php checked( '1', $options['exclude_gutenberg'] ); ?>><?php esc_html_e( 'Using Block/Gutenberg editor', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Uncheck this option if you are using the WP classic editor', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_hide_unsubscribe_callback() {
		$options['hide_unsubscribe'] = '';

		if ( isset( $this->options['hide_unsubscribe'] ) ) {
			$options['hide_unsubscribe'] = $this->options['hide_unsubscribe'];
		}
		?>

	  <label for="hide_unsubscribe"><input type="checkbox" value="1" id="hide_unsubscribe" name="stc_settings[hide_unsubscribe]" <?php checked( '1', $options['hide_unsubscribe'] ); ?>><?php esc_html_e( 'Enable hide_unsubscribe attribute use', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Check this option to enable the use of hide_unsubscribe attribute functionality', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_enable_keyword_search_callback() {
		$options['enable_keyword_search'] = '';

		if ( isset( $this->options['enable_keyword_search'] ) ) {
			$options['enable_keyword_search'] = $this->options['enable_keyword_search'];
		}
		?>

	  <label for="enable_keyword_search"><input type="checkbox" value="1" id="enable_keyword_search" name="stc_settings[enable_keyword_search]" <?php checked( '1', $options['enable_keyword_search'] ); ?>><?php esc_html_e( 'Enable enable_keyword_search attribute use', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Check this option to enable the use of enable_keyword_search attribute functionality', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_enable_taxonomy_hierarchy_callback() {
		$options['enable_taxonomy_hierarchy'] = '';

		if ( isset( $this->options['enable_taxonomy_hierarchy'] ) ) {
			$options['enable_taxonomy_hierarchy'] = $this->options['enable_taxonomy_hierarchy'];
		}
		?>

	  <label for="enable_taxonomy_hierarchy"><input type="checkbox" value="1" id="enable_taxonomy_hierarchy" name="stc_settings[enable_taxonomy_hierarchy]" <?php checked( '1', $options['enable_taxonomy_hierarchy'] ); ?>><?php esc_html_e( 'Enable taxonomy hierarchy', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Check this option to enable taxonomy hierarchy when using {{search_reason}} placeholder', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  2.6.3
	 */
	public function stc_post_filter_query_callback() {
		$options['post_filter_query'] = '';

		?>
		<input type="text" id="post_filter_query" class="regular-text" name="stc_settings[post_filter_query]" value="<?php echo isset( $this->options['post_filter_query'] ) ? esc_attr( $this->options['post_filter_query'] ) : ''; ?>" />
		<p class="description"><?php esc_html_e( 'Enter name of posts filter query', 'subscribe-to-category' ); ?></p>
		<?php
	}

        /**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_enable_sms_notification_callback() {
		$options['enable_sms_notification'] = '';

		if ( isset( $this->options['enable_sms_notification'] ) ) {
			$options['enable_sms_notification'] = $this->options['enable_sms_notification'];
		}
		?>

	  <label for="enable_sms_notification"><input type="checkbox" value="1" id="enable_sms_notification" name="stc_settings[enable_sms_notification]" <?php checked( '1', $options['enable_sms_notification'] ); ?>><?php esc_html_e( 'Enable SMS Notifications', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Check this option to enable SMS notification feature', 'subscribe-to-category' ); ?></p>
		<?php
	}

        /**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_css_callback() {
		$options['exclude_css'] = '';

		if ( isset( $this->options['exclude_css'] ) ) {
			$options['exclude_css'] = $this->options['exclude_css'];
		}
		?>

	  <label for="exclude_css"><input type="checkbox" value="1" id="exclude_css" name="stc_settings[exclude_css]" <?php checked( '1', $options['exclude_css'] ); ?>><?php esc_html_e( 'Use custom CSS', 'subscribe-to-category' ); ?></label>
	  <p class="description"><?php esc_html_e( 'Check this option if you want to use your own CSS for Subscribe to Category.', 'subscribe-to-category' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 */
	public function stc_remove_subscribers_callback() {
		$options['deactivation_remove_subscribers'] = '';

		if ( isset( $this->options['deactivation_remove_subscribers'] ) ) {
			$options['deactivation_remove_subscribers'] = $this->options['deactivation_remove_subscribers'];
		}

		?>

	  <label for="deactivation_remove_subscribers"><input type="checkbox" value="1" id="deactivation_remove_subscribers" name="stc_settings[deactivation_remove_subscribers]" <?php checked( '1', $options['deactivation_remove_subscribers'] ); ?>><?php esc_html_e( 'Delete all subscribers on deactivation', 'subscribe-to-category' ); ?></label>
		<?php
	}

	/**
	 * Form for filtering categories on export to excel/csv
	 *
	 * @since  1.0.0
	 */
	public function export_to_excel_form() {
		$categories = get_categories( array( 'hide_empty' => false ) );
		?>
          <BR><h3><?php esc_html_e( 'Export subscribers to a TAB separated text file', 'subscribe-to-category' ); ?></h3>
	  <form method="post" action="options-general.php?page=stc-subscribe-settings">
	  <input type="hidden" value="export" name="action">
	  <input type="submit" value="<?php esc_html_e( 'Export to excel/csv', 'subscribe-to-category' ); ?>" class="button button-primary" id="submit_export" name="">
	  </form>
	   
		<?php
	}

	/**
	 * Form for filtering categories on import from tab separated file
	 *
	 * @since  2.1.3
	 */
	public function import_from_csv_form() {
		$categories = get_categories( array( 'hide_empty' => false ) );
		?>
          <BR><h3><?php esc_html_e( 'Import subscribers from a TAB separated file', 'subscribe-to-category' ); ?></h3>
	  <form form enctype="multipart/form-data" method="post" action="options-general.php?page=stc-subscribe-settings">    
	  <table class="form-table">
		<tbody>
                    <tr><th scope="row"><?php esc_html_e( 'Select file to import from: ', 'subscribe-to-category' ); ?> </th>
                        <td><input type="file" style="width: 100%" accept=".csv, .txt" name="uploadedfile[]" id="file" required /></td>
                    </tr>
		</tbody>
	  </table>
	  <input type="hidden" value="import" name="action">
	  <input type="submit" value="<?php esc_html_e( 'Import subscribers from a tab separated text file', 'subscribe-to-category' ); ?>" class="button button-primary" id="submit_import" name="">
	  </form>
	   
		<?php
	}

	/**
	 * Export method for excel
	 *
	 * @since  1.0.0
	 */
	public function export_to_excel() {
                
                $options = get_option( 'stc_settings' );                


		$args = array(
			'post_type'     => 'stc',
			'post_status'   => 'publish',
			'category__in'  => $this->export_in_categories, // Empty value returns all categories.
			'numberposts'   => -1, // default is set to a maximum of 5 -1 sets it to all post.
		);

		$posts = get_posts( $args );

		$i = 0;
		$export = array();
		foreach ( $posts as $p ) {

			$cats = array();
                        if (isset($options['taxonomies'])) {
                                foreach ($options['taxonomies'] as $taxName) {
                                        $tax = get_the_terms( $p->ID, $taxName );
                                        if ($tax) {foreach ( $tax as $t ) {
                                                $cats[] = $t;
                                        }}
                                }
                        }
                        
			$c_name = "";
			foreach ( $cats as $c ) {
				$c_name .= $c->name.':'. $c->term_id . ',';
			}
			$in_categories = substr( $c_name, 0, -1);
			$export[ $i ]['id'] = $p->ID;
			$export[ $i ]['email'] = $p->post_title;
			$export[ $i ]['user_categories'] = $in_categories;
			$export[ $i ]['subscription_date'] = $p->post_date;

			$i++;
		}

		// filename for download.
		$time = gmdate( 'Ymd_His' );
		$filename = STC_SLUG . '_' . $time . '.csv';

		header( "Content-Disposition: attachment; filename=\"$filename\"" );
		header( 'Content-Type:   application/vnd.ms-excel; ' );
		header( 'Content-type:   application/x-msexcel; ' );

		$flag = false;

		foreach ( $export as $row ) {
			if ( ! $flag ) {
				// display field/column names as first row.
				echo '' . esc_attr( implode( "\t", array_keys( $row ) ) ) . "\r\n";
				$flag = true;
			}

			array_walk( $row, array( $this, 'clean_data_for_excel' ) );
			echo esc_attr( implode( "\t", array_values( $row ) ) ) . "\r\n";
		}
		// print out filtered categories if there is.
		if ( ! empty( $in_category_name ) ) {
			echo "\r\n", esc_attr( utf8_decode( esc_html__( 'Filtered by: ', 'subscribe-to-category' ) ) ) . esc_attr( utf8_decode( esc_attr( $in_category_name ) ) );
		}

		exit;

	}

	/**
	 * Import subscriber list method 
	 *
	 * @since  2.1.3
	 */
	public function import_from_csv() {
                global $wpdb;
                global $message;
                global $severity;
                $message = __("Subscribers are succesfully imported", 'subscribe-to-category');
                $severity = "notice-success";
                $options = get_option( 'stc_settings' );                

                // get available categories
                $categories = array();
                if (isset($options['taxonomies'])) {
                        foreach ($options['taxonomies'] as $taxName) {
                                $tax = get_terms( array('taxonomy'=> $taxName, 'hide_empty' => false) );
                                if ($tax) {foreach ( $tax as $t ) {
                                        $categories[] = $t->name.":".$t->term_id;
                                }}
                        }
                }
                        
                // open the uploaded temporary subscriber file
                $handle = fopen($this->import_file_name, 'r');
                // skip the header row
                fgetcsv($handle, 10000, "\t");
                // process the subcribers in the list row by row
                while (($data = fgetcsv($handle, 10000, "\t")) !== false) {
                        // email of the subscriber (to be used as post title)
                        $email = sanitize_email($data[1]);
                        $post_status = 'publish';

                        // given categories for this particular subscriber 
                        $requested_cat = array();
                        if ($data[2] != "") {
                                $cat_name = explode(',', sanitize_text_field($data[2]));
                                // check if the required category exists if true add it to the $reqested_cat array
                                $cat_found = false;
                                foreach($cat_name as $name) {
                                        // check for none existing categories and prepare the notification as warning to inform the user for problems in the list
                                        if (!in_array($name, $categories)) {
                                                $message = sprintf(__('Category "%s" not found for "%s". Subscriber is added with status: concept', 'subscribe-to-category'), $name, $email);
                                                $severity = "notice-warning";
                                                $post_status = 'draft';
                                        } else {
                                                array_push($requested_cat, intval(explode(":", $name)[1]));
                                        }
        //                                                if ($cat->name === $name) {
        //                                                        $cat_found = true;
        //                                                        array_push($requested_cat, $cat->term_id);
        //                                                }
                                }
                        }
                        // check if this subscriber is allready known if true delete this subscriber
        		$result = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s", $email, 'stc' ) );
                        if (!empty($result)) wp_delete_post($result->ID, true);

                        // add the subscriber with the required categories as post of type stc
                        $post_data = array(
                                'ID' => 0,
                                'post_type' => 'stc',
                                'post_title' => $email,
                                'post_status' => $post_status,
                                'post_author' => 1,
                                'post_category' => $requested_cat,
                        );
                        $post_id = wp_insert_post( $post_data );
                        if (isset($options['taxonomies'])) {
                                foreach ($options['taxonomies'] as $taxName) {
                                        wp_set_object_terms( $post_id, $requested_cat, $taxName );
                                }
                        }
                        
                        $repsonse = wp_set_post_terms($post_id, $requested_cat);
			update_post_meta( $post_id, '_stc_hash', md5( $email . time() ) );

			// hook after inserting a subscriber.
			do_action( 'stc_after_insert_subscriber', $post_id, $requested_cat, false );
                }
                fclose($handle);
                // inform the user about the results of the import
                add_action('admin_notices', 'stc_general_admin_notice');
	}
        
        
        /**
	 * Method for cleaning data to excel
	 *
	 * @since  1.0.0
	 * @param string $str Contains the excel row.
	 */
	public function clean_data_for_excel( &$str ) {
//		$str = iconv( 'UTF-8', 'ISO-8859-1', $str );
		$str = preg_replace( "/\t/", "\\t", $str );
		$str = preg_replace( "/\r?\n/", "\\n", $str );
	}

}

?>
