<?php
  
  /*
    Plugin Name: miniOrange Limit Login Attempts
    Plugin URI: http://miniorange.com
    Description: Security against Login, Brute force attacks by tracking and Blacklisting IPs.
    Author: miniOrange
    Version: 4.0.71
    Author URI: http://miniorange.com
    License: MIT
    */
	
	require('integrations/class_buddypress.php');
	require('integrations/class_icegram_email_subscription.php');
    define( 'MO2F_TEST_MODE_LIMIT_LOGIN_LIMIT_LOGIN', false );

    $plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);

    $plugin_version = $plugin_data['Version'];
    define('LIMITLOGIN_VERSION',$plugin_version);

    if (class_exists('Miniorange_twoFactor')) {
        echo '<div>
             <p>Cannot Activate miniOrange Limit Login Attempts : Please Deactivate miniOrange 2 factor authentication plugin <a href="https://plugins.miniorange.com/2-factor-authentication-for-wordpress" target="_blank">more info</a></p>
         </div>';
        deactivate_plugins( plugin_basename( __FILE__ ) );
        exit;
    }

class WPSecurityPro{

		function __construct()
		{

			register_deactivation_hook(__FILE__		 , array( $this, 'mo_wpns_deactivate'		       )		);
			register_activation_hook  (__FILE__		 , array( $this, 'mo_wpns_activate'			       )		);
			add_action( 'admin_menu'				 , array( $this, 'mo_wpns_widget_menu'		  	   )		);
			add_action( 'admin_enqueue_scripts'		 , array( $this, 'mo_wpns_settings_style'	       )		);
			add_action( 'admin_enqueue_scripts'		 , array( $this, 'mo_wpns_settings_script'	       )	    );
			add_action( 'wpns_show_message'		 	 , array( $this, 'mo_show_message' 				   ), 1 , 2 );
			add_action( 'wp_footer'					 , array( $this, 'footer_link'					   ),100	);
            add_action( 'admin_footer'				 , array( $this, 'feedback_request' 			   ) 		);
            add_action(	'bp_signup_validate'		 , array('Mo_BuddyPress', 'signup_errors'		   )		);
          	add_action( 'upgrader_process_complete'  , array( $this, 'migration_update'				   )		);
          	//add_action( 'user_register'				 , array( $this, 'register_check' 				   )		);

            if(get_option('disable_file_editing')) 	 define('DISALLOW_FILE_EDIT', true);
			$this->includes();
			if(get_option('mo_wpns_logout_time')){
				add_filter( 'login_footer', array( $this, 'add_js' ) );
				add_filter('auth_cookie_expiration', array($this,'my_expiration_filter'), 10, 3);
			}
			if (get_option('mo_wpns_activate_recaptcha')) {
			    if (get_option('mo_wpns_activate_recaptcha_for_buddypress_registration')) {
                    add_action('bp_signup_profile_fields', array($this, 'bp_signup_with_captcha'));
                }
            }
           
		}

        // As on plugins.php page not in the plugin
        function feedback_request() {

            if ( 'plugins.php' != basename( $_SERVER['PHP_SELF'] ) ) {
                return;
            }
            global $mo_lla_dirName;

             $email = get_option("mo_wpns_admin_email");
            if(empty($email)){
                $user = wp_get_current_user();
                $email = $user->user_email;
            }
            $imagepath=plugins_url( '/includes/images/', __FILE__ );

            wp_enqueue_style( 'wp-pointer' );
            wp_enqueue_script( 'wp-pointer' );
            wp_enqueue_script( 'utils' );
            wp_enqueue_style( 'mo_wpns_admin_plugins_page_style', plugins_url( '/includes/css/style_settings.css?ver=4.8.60', __FILE__ ) );

            include $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'feedback.php';
            

        }
        function migration_update()
        {
			$brute_force=false;
        	if(get_option("mo_wpns_enable_brute_force") || get_option("mo_wpns_enable_brute_force")=="on"){$brute_force=true;}	
			update_option("mo_wpns_enable_brute_force",$brute_force);
			
			
			$recaptcha_bp = 1;
        	if(get_option("mo_wpns_activate_recaptcha_for_buddypress_registration")!='on'){$recaptcha_bp = 0;}
        	update_option("mo_wpns_activate_recaptcha_for_buddypress_registration",$recaptcha_bp);
        	

        }
        public function add_js() {
		echo 
		'<script type="text/javascript">
			var checkbox = document.getElementById("rememberme");
			if ( null != checkbox )
				checkbox.checked = true;
			 document.getElementsByClassName("forgetmenot")[0].style.display = "none";
		</script>';
		}
		function my_expiration_filter($seconds, $user_id, $remember){
			if(get_option('mo_wpns_logout_time')){
			$expiration=get_option('mo_wpns_logout_time');
			$expiration=$expiration*24*60*60;
			return $expiration;
			}
			if ( $remember ) {
				$expiration = 14*24*60*60;
			} else {
				$expiration = 2*24*60*60; //2 days
			}
			if ( PHP_INT_MAX - time() < $expiration ) {
				$expiration =  PHP_INT_MAX - time() - 5;
			}
			return $expiration;
		}

		function mo_wpns_widget_menu()
		{
			$menu_slug = 'dashboard';
			add_menu_page (	'Limit Login Attempts' , 'Limit Login Attempts' , 'activate_plugins', $menu_slug , array( $this, 'mo_wpns'), plugin_dir_url(__FILE__) . 'includes/images/miniorange_icon.png' );

			add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Dashboard'		    ,'administrator',$menu_slug			, array( $this, 'mo_wpns'));
			add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Login and Spam'		,'administrator','login_and_spam'	, array( $this, 'mo_wpns'));
			add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'WAF'		   			,'administrator','waf'				, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Advanced Blocking'	,'administrator','advancedblocking'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Notifications'		,'administrator','notifications'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Reports'				,'administrator','reports'			, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Troubleshooting'		,'administrator','troubleshooting'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Account'				,'administrator','wpnsaccount'			, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Limit Login Attempts'	,'Upgrade'				,'administrator','upgrade'			, array( $this, 'mo_wpns'));
        }

		function mo_wpns()
		{
			global $wpnsDbQueries;
			$wpnsDbQueries->mo_plugin_activate();
			
			add_option( 'mo_wpns_enable_brute_force' , true);
			add_option( 'mo_wpns_show_remaining_attempts' , true);
			add_option( 'mo_wpns_enable_ip_blocked_email_to_admin', true);
			add_option('SQLInjection', 1);
			add_option('WAFEnabled' ,0);
			add_option('XSSAttack' ,1);
			add_option('RFIAttack' ,0);
			add_option('LFIAttack' ,0);
			add_option('RCEAttack' ,0);
			add_option('actionRateL',0);
			add_option('Rate_limiting',0);
			add_option('Rate_request',240);
			add_option('limitAttack',10);
			add_option( 'mo_wpns_check_vulnerable_code', 1);
			add_option( 'mo_wpns_check_sql_injection', 1);
			add_option( 'mo_wpns_scan_plugins', true);
			add_option( 'mo_wpns_scan_themes', true);
			add_option( 'mo_inactive_logout_duration' ,30);
			include 'controllers/main_controller.php';
		}

		function mo_wpns_activate() 
		{
		    if (is_plugin_active('miniorange-2-factor-authentication/miniorange_2_factor_settings.php'))
            return false;


            update_site_option('limitlogin_activated_time', time());
            update_site_option('mo_wpns_plugin_redirect', true);
			global $wpnsDbQueries;
			$wpnsDbQueries->mo_plugin_activate();

			global $moWpnsUtility, $wpdb;
            $moPluginsUtility = new Mo_lla_MoWpnsHandler();

            $sql = "SELECT ip_address FROM ".$wpdb->prefix."wpns_whitelisted_ips WHERE id = ".get_current_user_id().";";
            $is_ip_present = $wpdb->get_results($sql);
            if(empty($is_ip_present) || $moWpnsUtility->get_client_ip() != $is_ip_present[0]->ip_address){
                set_transient('ip_whitelisted',true,5);
                $moPluginsUtility->whitelist_ip($moWpnsUtility->get_client_ip());
            }
		}

		function bp_signup_with_captcha()
		{
			if (!is_user_logged_in()){
				if(get_option('mo_wpns_activate_recaptcha_for_buddypress_registration'))
				{
					echo "<script src='".Mo_lla_MoWpnsConstants::RECAPTCHA_URL."'></script>";
					echo '<div class="g-recaptcha" data-sitekey="'.get_option("mo_wpns_recaptcha_site_key").'"></div>';
					echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
				}
			}
		}

		function mo_wpns_deactivate() 
		{
			global $moWpnsUtility;
			if( !$moWpnsUtility->check_empty_or_null( get_option('mo_wpns_registration_status') ) ) {
				delete_option('mo_wpns_admin_email');
			}

			delete_option('mo_wpns_admin_customer_key');
			delete_option('mo_wpns_admin_api_key');
			delete_option('mo_wpns_customer_token');
			delete_option('mo_wpns_transactionId');
			delete_option('mo_wpns_registration_status');
		}

		function mo_wpns_settings_style()
		{

			wp_register_style( 'mo_wpns_admin_settings_style'			, plugins_url('includes/css/style_settings.css', __FILE__));
			wp_register_style( 'mo_wpns_admin_settings_phone_style'		, plugins_url('includes/css/phone.css', __FILE__));
			wp_register_style( 'mo_wpns_admin_settings_datatable_style'	, plugins_url('includes/css/jquery.dataTables.min.css', __FILE__));
			wp_register_style( 'mo_wpns_button_settings_style'			, plugins_url('includes/css/button_styles.css',__FILE__));

            wp_enqueue_style('mo_wpns_admin_settings_style');
            wp_enqueue_style('mo_wpns_admin_settings_phone_style');
            wp_enqueue_style('mo_wpns_admin_settings_datatable_style');
            wp_enqueue_style('mo_wpns_button_settings_style');
		}

		function mo_wpns_settings_script()
		{
			wp_enqueue_script( 'mo_wpns_admin_settings_phone_script'	, plugins_url('includes/js/phone.js', __FILE__ ));
			wp_enqueue_script( 'mo_wpns_admin_settings_script'			, plugins_url('includes/js/settings_page.js', __FILE__ ), array('jquery'));
			wp_enqueue_script( 'mo_wpns_admin_datatable_script'			, plugins_url('includes/js/jquery.dataTables.min.js', __FILE__ ), array('jquery'));
		}

		function mo_show_message($content,$type) 
		{
			if($type=="CUSTOM_MESSAGE")
				echo $content;
			if($type=="NOTICE")
				echo '	<div class="is-dismissible notice notice-warning"> <p>'.$content.'</p> </div>';
			if($type=="ERROR")
				echo '	<div class="notice notice-error is-dismissible"> <p>'.$content.'</p> </div>';
			if($type=="SUCCESS")
				echo '	<div class="notice notice-success is-dismissible"> <p>'.$content.'</p> </div>';
		}

		function footer_link()
		{
			echo Mo_lla_MoWpnsConstants::FOOTER_LINK;
			if (get_option('mo_wpns_activate_recaptcha_for_email_subscription')) {
                Mo_Icegram_EmailSubscription::recaptcha_for_email_sunscription();
            }
		}

		function includes()
		{
			require('helper/pluginUtility.php');
			require('database/database_functions.php');
			require('helper/utility.php');
			require('handler/ajax.php');
			require('handler/backup.php');
			require('handler/feedback_form.php');
			require('handler/recaptcha.php');
			require('handler/login.php');
			require('handler/registration.php');
			require('handler/logger.php');
			require('handler/spam.php');
			require('helper/curl.php');
			require('helper/plugins.php');
			require('helper/constants.php');
			require('helper/messages.php');
			require('views/common-elements.php');
			 
			require('controllers/wpns-loginsecurity-ajax.php');
			require('controllers/malware_scan_ajax.php');
			require('controllers/backup_ajax.php');
		}

	}

	new WPSecurityPro;
?>