<?php

if (!defined('ABSPATH')) {
    exit;
}

class Canto_Settings
{

    /**
     * The single instance of Canto_Settings.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var    object
     * @access  public
     * @since    1.0.0
     */
    public $parent = null;

    /**
     * Prefix for Canto.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    public function __construct($parent)
    {
        $this->parent = $parent;

        $this->base = 'fbc_';

        // Initialise settings
        add_action('init', array($this, 'init_settings'), 11);

        // Register Canto
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));

        // Add settings link to plugins page
        add_filter('plugin_action_links_' . plugin_basename($this->parent->file),
            array($this, 'add_settings_link'));
    }

    /**
     * Initialise settings
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
        add_action('wp_ajax_fbc_updateOptions', array($this, 'fbc_updateOptions'));
        //add_action( 'wp_ajax_fbc_getToken', array( $this, 'fbc_getToken' ) );
        //add_action( 'wp_ajax_fbc_refreshToken', array( $this, 'fbc_refreshToken' ) );
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        $page = add_options_page(__('Canto', 'canto'),
            __('Canto', 'canto'), 'manage_options', $this->parent->_token . '_settings',
            array($this, 'settings_page'));
        add_action('admin_print_styles-' . $page, array($this, 'settings_assets'));
    }

    /**
     * Load settings JS & CSS
     * @return void
     */
    public function settings_assets()
    {

        // We're including the WP media scripts here because they're needed for the image upload field
        // If you're not including an image upload then you can leave this function call out
        wp_enqueue_media();

        wp_register_script($this->parent->_token . '-settings-js',
            $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js',
            array('farbtastic', 'jquery'), '1.0.0');
        wp_enqueue_script($this->parent->_token . '-settings-js');
    }

    /**
     * Add settings link to plugin list table
     *
     * @param array $links Existing links
     *
     * @return array        Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __('Settings',
                'canto') . '</a>';
        array_push($links, $settings_link);

        return $links;
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    private function settings_fields()
    {
//        $nonce = wp_create_nonce('canto_nonce');
        $settings['standard'] = array(
            'title' => "Canto Settings",
            'description' => __('', 'canto'),
            'fields' => array(
                array(
                    'id' => 'flight_domain',
                    'label' => __('Flight Domain', 'canto'),
                    'description' => __('.canto.com', 'canto'),
                    'type' => 'text',
                    'default' => '',
                    'placeholder' => __('Canto Domain', 'canto')
                ),
            ),
//            'nonce' => $nonce,
        );

        $settings = apply_filters($this->parent->_token . '_settings_fields', $settings);

        return $settings;
    }

    /**
     * Register Canto
     * @return void
     */
    public function register_settings()
    {
        error_log(json_encode($this->base));
        if (is_array($this->settings)) {
            // Check posted/selected tab
            $current_section = '';

//            if (isset($_POST['_nonce']) && wp_verify_nonce($_POST['_nonce'], 'heartbeat-nonce')) {
//                if ($_POST['tab']) {
//                    $post_tab = sanitize_text_field($_POST['tab']);
//                    $current_section = $post_tab;
//                }
//            } else {
            if (isset($_GET['tab']) && $_GET['tab']) {
                $get_tab = sanitize_text_field($_GET['tab']);
                $current_section = $get_tab;
            }
//            }

            foreach ($this->settings as $section => $data) {
                if ($current_section && $current_section != $section) {
                    continue;
                }

                // Add section to page
                add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token . '_settings');

                foreach ($data['fields'] as $field) {

                    // Validation callback for field
                    $validation = '';
                    if (isset($field['callback'])) {
                        $validation = $field['callback'];
                    }

                    // Register field
                    $option_name = $this->base . $field['id'];
                    register_setting($this->parent->_token . '_settings', $option_name, $validation);

                    // Add field to page
                    @add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'),
                        $this->parent->_token . '_settings', $section,
                        array('field' => $field, 'prefix' => $this->base));
                }

                if (!$current_section) {
                    break;
                }
            }
        }
    }

    public function settings_section($section)
    {
//        $html = '<p> ' . esc_html($this->settings[$section['id']]['description']) . '</p>' . "\n";
        echo '<p> ' . esc_html($this->settings[$section['id']]['description']) . '</p>' . "\n";;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function settings_page()
    {
        // Build page HTML
        $html = '<div class="wrap" id="' . esc_html($this->parent->_token) . '_settings">' . "\n";
        $html .= '<h2>' . __('Canto', 'canto') . '</h2>' . "\n";

        $tab = '';
        if (isset($_GET['tab']) && $_GET['tab']) {
            $get_tab = sanitize_text_field($_GET['tab']);
            $tab .= $get_tab;
        }

        // Show page tabs
        if (is_array($this->settings) && 1 < count($this->settings)) {
            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

            $c = 0;
            foreach ($this->settings as $section => $data) {
                // Set tab class
                $class = 'nav-tab';
                if (!isset($_GET['tab'])) {
                    if (0 == $c) {
                        $class .= ' nav-tab-active';
                    }
                } else {
                    $get_tab = sanitize_text_field($_GET['tab']);
                    if (isset($get_tab) && $section == $get_tab) {
                        $class .= ' nav-tab-active';
                    }
                }

                // Set tab link
                $tab_link = add_query_arg(array('tab' => $section));
                if (isset($_GET['settings-updated'])) {
                    $tab_link = remove_query_arg('settings-updated', $tab_link);
                }

                // Output tab
                $html .= '<a href="' . esc_url($tab_link) . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";
                ++$c;
            }
            $html .= '</h2>' . "\n";
        }

        /*
         * Canto oAuth Config and connection
         */
        $api_domains = array(
            'canto.com' => 'e3a2d379335d48e7afef348dda917fd9',
            'canto.global' => '0fac4b924b404106a6de4a6e53dc0de2',
            'canto.de' => '6900b53264fb44fb8e3a94f84a8502f5',
            'ca.canto.com' => '6900b53264fb44fb8e3a94f84a8502f5',
//			'cantodemo.com'           => '7dfe87f4a73e4799a1d4c4be66faff64',
//			'flightbycanto.com'       => '1ce0293cfb1040cfaec1142e77dade06',
//			'staging.cantoflight.com' => '7c2d00e9c82c469194cc770d7cd55106'
        );
        $oAuth = "https://oauth.canto.com:443/oauth/api/oauth2/authorize?response_type=code";
        $appID = "e3a2d379335d48e7afef348dda917fd9";
        $callback = urlencode("https://oauth.canto.com/oauth/api/callback/wordress?app_api=canto.com");
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

        $http_host = sanitize_text_field($_SERVER['HTTP_HOST']);
        $request_url = sanitize_text_field($_SERVER['REQUEST_URI']);
        $state = urlencode($scheme . '://' . $http_host . $request_url);
        $oAuthURL = $oAuth . '&app_id=' . $appID . '&redirect_uri=' . $callback . '&state=' . $state;

        $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
        $html .= '<div id="fbc_settings_form">' . "\n";

        if (get_option('fbc_flight_domain') == '' && get_option('fbc_app_token') == '') :
            $html .= "<i class='icon-icn_close_circle_x_01'></i>";
            $html .= '<strong>Status:</strong> You are not connected to Canto<br><br>';
            $html .= 'Select Your API endpoint: <select name="app_api" id="app_api">' . "\n";
            foreach ($api_domains as $k => $v) {
                $html .= '<option value="' . esc_attr($k) . '" data-appid="' . esc_attr($v) . '">company.' . esc_html($k) . '</option>';
            }
            $html .= '</select><br><br>';
            $html .= '<a class="button-primary" id="oAuthURL" href="' . esc_url($oAuthURL) . '">Login to Canto</a>';

        elseif (get_option('fbc_flight_domain') != '' && get_option('fbc_app_token') != '') :
            if (get_option('fbc_app_expire_token') < time()) {
                $html .= "<i class='icon-icn_close_circle_x_01'></i>";
                $html .= '<strong>Status:</strong> Your security token has expired. You are not connected to Canto<br><br>';
                $html .= '<em>Last login: <strong>' . date("F d Y, g:i A", get_option('fbc_app_timestamp')) . '</strong></em><br>';
                $html .= '<em>For security purposes you will need to login again after <strong>' . date("F d Y, g:i A", get_option('fbc_app_expire_token')) . '</strong> </em><br><br>';

                $html .= 'Select Your API endpoint: <select name="app_api" id="app_api">' . "\n";
                foreach ($api_domains as $k => $v) {
                    $html .= '<option value="' . esc_html($k) . '" data-appid="' . esc_html($v) . '">company.' . esc_html($k) . '</option>';
                }
                $html .= '</select><br><br>';
                $html .= '<a class="button-primary" id="oAuthURL" href="' . esc_url($oAuthURL) . '">Login to Canto</a>';
            } else {
                $app_api = (get_option('fbc_app_api')) ? get_option('fbc_app_api') : 'canto.com';

                $html .= "<i class='icon-icn_checkmark_circle_01'></i>";
                $html .= '<strong>Status:</strong> You are connected to Canto -  <strong>' . get_option('fbc_flight_domain') . '.' . esc_html($app_api) . '</strong><br><br>';
                $html .= '<em>Last login: <strong>' . date("F d Y, g:i A", get_option('fbc_app_timestamp')) . '</strong></em><br>';
                $html .= '<em>For security purposes you will need to login again after <strong>' . date("F d Y, g:i a", get_option('fbc_app_expire_token')) . '</strong> </em><br><br>';

                $html .= '<a class="button-primary" href="' . esc_url($scheme . '://' . $http_host . $request_url) . '&disconnect">Disconnect</a>' . "\n";
//                $html .= '<a class="button-primary" href="http://localhost/wordpress611/wp-admin/options-general.php?page=canto_settings&disconnect">Disconnect</a>'. "\n";
            }
        else :
            $html .= 'There was a problem installing the plugin. Please contact support' . "\n";
        endif;

        $duplicates = (get_option('fbc_duplicates') === "true") ? "checked" : "";
        $cron = (get_option('fbc_cron') === "true") ? "checked" : "";

        $html .= "\n\n";

        //Only show options if connected to Flight
        if (get_option('fbc_flight_domain') != '' && get_option('fbc_app_token') != '') :
            $html .= '<p><hr /></p><h3>Options</h3>';
            $html .= '<div class="checkbox"><div style= "display: table-cell; padding: 5px 0;">
                <input type="checkbox" name="duplicates" id="duplicates" ' . esc_attr($duplicates) . '></div>';
            $html .= '<label style="display: table-cell;padding: 0 10px;"><strong>Duplicate Check</strong>
                - Updates Wordpress Media Library with latest version from Canto if image is added again</label></div>' . "\n";
            $html .= '<div style="clear:both"><br /></div>';

            $html .= '<div class="checkbox"><div style="display: table-cell; padding: 5px 0;">
                        <input type="checkbox" name="cron" id="cron" ' . esc_attr($cron) . '></div>';
            $html .= '<label style="display: table-cell;padding: 0 10px;"><strong>Automatic Update</strong> 
                - Check for new versions of files added from Canto and update Wordpress Media Library with latest version' . "\n";


            //Cron schedule options
            $html .= '<div id="cron_schedule_options" style="padding: 10px; ' . ((get_option('fbc_cron') === "true") ? "" : "display:none") . '">';
            $html .= '<select name="schedule" id="schedule">';
            $html .= '<option value="every_day" ' . ((get_option('fbc_schedule') === "every_day") ? "selected" : "") . '>Every Day</option>';
            $html .= '<option value="every_week" ' . ((get_option('fbc_schedule') === "every_week") ? "selected" : "") . '>Once a Week</option>';
            $html .= '<option value="every_month" ' . ((get_option('fbc_schedule') === "every_month") ? "selected" : "") . '>Once a Month</option>';
            $html .= '</select>' . "\n";

            $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $html .= '<select style="' . ((get_option('fbc_schedule') === "every_week" || get_option('fbc_schedule') === "every_month")
                    ? "" : "display:none") . '" class="cron_times" name="cron_time_day" id="cron_time_day">' . "\n";

            foreach ($days as $d) {
                $html .= '<option value="' . esc_html($d) . '" ' . ((get_option('fbc_cron_time_day') == esc_html($d)) ?
                        "selected" : "") . '>' . esc_html($d) . '</option>';
            }
            $html .= '</select>';

            $html .= '<select class="cron_times" name="cron_time_hour" id="cron_time_hour">' . "\n";
            for ($i = 0; $i < 24; $i++) {
                $html .= '<option value="' . esc_html($i) . '" ' . ((get_option('fbc_cron_time_hour') == esc_html($i)) ?
                        "selected" : "") . '>' . esc_html($i) . ':00</option>';
            }
            $html .= '</select>';

            $html .= '<p style="' . ((get_option('fbc_schedule') != "every_month") ? "display:none;" : "") . ' margin:0;" class="cron_times" id="cron_time_month">
                <em>Will run each month on the first occurrence for the selected day of the week</em></p>';

            $html .= '</div>';

            $html .= '</label></div>' . "\n";


            $html .= '<p class="submit">' . "\n";
            $html .= '<button id="updateOptions" class="button-primary">Save Options</button>' . "\n";
            $html .= '</p>' . "\n";

            $html .= '</div>' . "\n";

        endif;
        //End options

//        $html .= '<img src="' . esc_url(CANTO_FBC_URL) . '/assets/loader_white.gif" id="loader" style="display:none">';
        $html .= '</form>' . "\n";
        $html .= '</div>' . "\n";

        $allowed_html = array(
            'div' => array(
                'class' => array(),
                'id' => array(),
                'style' => array(
                    'display' => array(),
                ),
            ),
            'img' => array(
                'src' => array(),
                'id' => array(),
                'style' => array(),
                'class' => array(),
            ),
            'form' => array(
                'enctype' => array(),
                'method' => array(),
                'action' => array(),
            ),
            'input' => array(
                'class' => array(),
                'type' => array(),
                'name' => array(),
                'id' => array(),
                'checked' => array(),
            ),
            'label' => array(
                'style' => array(),
            ),
            'h2' => array(),
            'h3' => array(),
            'p' => array(
                'style' => array(),
                'class' => array(),
                'id' => array(),
            ),
            'button' => array(
                'style' => array(),
                'class' => array(),
                'id' => array(),
            ),
            'hr' => array(),
            'i' => array('class' => array()),
            'select' => array(
                'name' => array(),
                'id' => array(),
                'style' => array(),
                'class' => array(),
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
                'data-appid' => array(),
            ),
            'a' => array(
                'href' => array(),
                'id' => array(),
                'class' => array(),
                'title' => array(),
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
        );

        add_filter( 'safe_style_css', function( $styles ) {
            $styles[] = 'display';
//            $styles[] = 'float';
            return $styles;
        } );

        echo wp_kses($html, $allowed_html);

        //Generate OAuth Token -- Unused until API {Redirect URI} is fixed
        if (isset($_REQUEST['disconnect'])) {
            delete_option('fbc_flight_domain');
            delete_option('fbc_app_id');
            delete_option('fbc_app_api');
            delete_option('fbc_app_secret');
            delete_option('fbc_app_token');
            delete_option('fbc_app_refresh_token');
            delete_option('fbc_token_expire');
            delete_option('fbc_flight_username');
            delete_option('fbc_flight_password');
            delete_option('fbc_refresh_token_expire');

            $arr = explode("&disconnect", $request_url);
            $rURI = $arr[0];

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            echo '<script type="text/javascript">';
            echo "window.location.href = '" . esc_url($scheme . "://" . $http_host . $rURI) . "';";
            echo '</script>';
        }

        //Generate OAuth Token -- Unused until API {Redirect URI} is fixed
        if (isset($_REQUEST['token']) && isset($_REQUEST['domain'])) :
            $token = sanitize_text_field($_REQUEST['token']);
            $domain = sanitize_text_field($_REQUEST['domain']);
            $refreshToken = sanitize_text_field($_REQUEST['refreshToken']);
            $expiresIn = sanitize_text_field($_REQUEST['expiresIn']);
            $app_api = sanitize_text_field($_REQUEST['app_api']);

            update_option('fbc_app_token', $token);
            update_option('fbc_app_timestamp', time());
            update_option('fbc_app_refresh_token', $refreshToken);
            update_option('fbc_app_expire_token', time() + $expiresIn);

            if (str_contains($domain, ".ca.canto.com")) {
                $domain = str_replace(".ca.canto.com", "", $domain);
            }
            update_option('fbc_flight_domain', $domain);

            $app_api = isset($_REQUEST['app_api']) ? $app_api : 'canto.com';
            if ($app_api == "canto.ca") {
                $app_api = "ca.canto.com";
            }
            update_option('fbc_app_api', $app_api);

            $arr = explode("&token=", $request_url);
            $rURI = $arr[0];

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            echo '<script type="text/javascript">';
            echo "window.location.href = '" . esc_url($scheme . "://" . $http_host . $rURI) . "';";
            echo '</script>';

        endif;
        ?>
        <script type="text/javascript">
            jQuery('#app_api').change(function (e) {
                var app_api = jQuery(this).val();
                var app_id = jQuery(this).find(':selected').data('appid');
                var oAuthURL = jQuery('#oAuthURL').attr('href');
                var endpoint = oAuthURL.replace(/https\:\/\/(.+?):443/, 'https://oauth.' + app_api + ':443');

                if (app_api == "ca.canto.com") {
                    endpoint = endpoint.replace(/app_api\%3D(.+?)&/, 'app_api%3D' + "canto.ca" + '&');
                } else {
                    endpoint = endpoint.replace(/app_api\%3D(.+?)&/, 'app_api%3D' + app_api + '&');
                }
                endpoint = endpoint.replace(/app_id=(.+?)&/, 'app_id=' + app_id + '&');
                jQuery('#oAuthURL').attr('href', endpoint);
            });
            jQuery('#updateOptions').click(function (e) {
                e.preventDefault();
                var data = {
                    'action': 'fbc_updateOptions',
                    'duplicates': jQuery("#duplicates").prop('checked'),
                    'cron': jQuery("#cron").prop('checked'),
                    'schedule': jQuery("#schedule").val(),
                    'cron_time_day': jQuery("#cron_time_day").val(),
                    'cron_time_hour': jQuery("#cron_time_hour").val()
                };
                jQuery.post(ajaxurl, data, function (response) {
                    response = jQuery.parseJSON(response);
                    if (typeof response.error === "undefined") {
                        location.reload();
                    }
                });
            });

            jQuery('#cron').on('change', function () {
                if (jQuery(this).is(':checked')) {
                    jQuery('#cron_schedule_options').show();
                } else {
                    jQuery('#cron_schedule_options').hide();
                }
            });

            jQuery('#schedule').on('change', function () {
                console.log("------------")
                jQuery('.cron_times').hide();
                var schedule = jQuery(this).val();
                if (schedule == 'every_week' || schedule == 'every_month') {
                    jQuery('#cron_time_day').show();
                }
                jQuery('#cron_time_hour').show();
                if (schedule == 'every_month') {
                    jQuery('#cron_time_month').show();
                }
            });
        </script>
        <?php
    }

    public function fbc_updateOptions()
    {
        $instance = Canto::instance();

        //var_dump($instance); wp_die();
        return $instance->updateOptions();
    }

    public function fbc_getToken()
    {
        $instance = Canto::instance();

        //var_dump($instance); wp_die();
        return $instance->getToken();
    }

    public function fbc_refreshToken()
    {
        $instance = Canto::instance();

        //var_dump($instance); wp_die();
        return $instance->refreshToken();
    }

    /**
     * Main Canto_Settings Instance
     *
     * Ensures only one instance of Canto_Settings is loaded or can be loaded.
     *
     * @return Main Canto_Settings instance
     * @see Canto()
     * @since 1.0.0
     * @static
     */
    public static function instance($parent)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($parent);
        }

        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __wakeup()

}
