<?php

final class WPB_Backend
{
    public function __construct()
    {
        // Initialize event handler and custom action link filter
        $this->event_handler();
    }

    private function event_handler()
    {
        add_action('admin_menu', array($this, 'register_admin_page'), 100, 1);
        add_filter('plugin_action_links_' . plugin_basename(IQWPB_PLUGIN_PATH . 'wpbookit.php'), array($this, 'wpbookit_add_custom_action_link'));

        if (isset($_REQUEST['page']) && 'wpbookit-dashboard' === $_REQUEST['page']) :
            // Filter to update browser tab title
            add_filter('admin_title', [$this, 'wpb_dashboard_title']);
            add_action('wp_loaded', [$this, 'load_settings_classes']);

            // Enqueue Admin-side assets...
            add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);

            // Hook to remove hide sidebar and top-bar...
            add_action('admin_head', [$this, 'hide_side_bar']);

            // Hooks to load content & header
            add_action('wpb_settings_before_main_content', [$this, 'load_sidebar'], 20, 2);
            add_action('wpb_settings_before_content', [$this, 'load_navigation'], 20, 2);
            add_action('wpb_settings_after_main_content', [$this, 'load_footer'], 20, 2);
            add_action('admin_footer', function () {
                if (is_rtl()) {
                    wp_enqueue_style('wpb-rtl', IQWPB_PLUGIN_URL . 'core/admin/assets/src/css/rtl.css', array(), IQWPB_VERSION);
                }
            });

            self::load_library();
        endif;
        new WPB_Routes_Handler();
    }

    public function wpbookit_add_custom_action_link($links)
    {
        array_unshift($links, '<a href="' . admin_url('admin.php?page=wpbookit-dashboard') . '">' . esc_html__('Dashboard', 'wpbookit') . '</a>');
        return $links;
    }

    public function wpb_dashboard_title()
    {
        return get_bloginfo('name');
    }


    public function register_admin_page($admin_bar)
    {
        add_menu_page(
            esc_html__('WPBookit Dashboard', 'wpbookit'),
            esc_html__('WPBookit', 'wpbookit'),
            'manage_wpbookit',
            'wpbookit-dashboard',
            [$this, 'load_wpb_settings'],
            IQWPB_PLUGIN_URL . '/core/admin/assets/images/sidebar-icon.svg',
            3
        );
    
        $settings_pages = WPB_Admin_Settings::get_settings_tabs_array();
        
        foreach ($settings_pages as $page_id => $page) {
            if (is_array($page) && isset($page['label'])) {
                add_submenu_page(
                    'wpbookit-dashboard', 
                    $page['label'],         
                    $page['label'],        
                    'manage_wpbookit',      
                    $page_id=='dashboard' ?  'wpbookit-dashboard' : ('wpbookit-dashboard&tab=' . $page_id),  
                    [$this, 'load_wpb_settings']  
                );
            } else {
                error_log('Invalid settings page array in register_admin_page: ' . print_r($page, true));
            }
        }   
    }
    public function hide_side_bar()
    {
        remove_action('wp_body_open', 'wp_admin_bar_render', 0);
        remove_action('wp_footer', 'wp_admin_bar_render', 1000); // Back-compat for themes not using `wp_body_open`.
        remove_action('in_admin_header', 'wp_admin_bar_render', 0);

        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    public function enqueueStyles()
    {
        wp_enqueue_style('wpbookit-dashbord', IQWPB_PLUGIN_URL . 'core/admin/assets/src/css/app.css', array(), IQWPB_VERSION);
        wp_enqueue_style('wpbookit-font-family', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap', array(), IQWPB_VERSION);
        wp_register_style('wpbookit-dashbord', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/css/bootstrap.js', array(), IQWPB_VERSION);
        wp_add_inline_style('wpbookit-dashbord', '#wpcontent, #footer { margin-left: 0px !important;padding-left: 0px !important; }
        html.wp-toolbar { padding-top: 0px !important; }
        #adminmenuback, #adminmenuwrap, #wpadminbar, #wpfooter,#adminmenumain, #screen-meta { display: none !important; }
        #wpcontent .notice { display:none; }');

        $wpb_custom_code = get_option('wpb_custom_code_data', ['css_code' => '',  'js_code' => '']);

        wp_register_style('wpb-custom-code-css', '');
        wp_add_inline_style('wpb-custom-code-css', stripslashes($wpb_custom_code['css_code']));

        wp_register_script('wpb-custom-code-js', '', array("jquery"), IQWPB_VERSION);
        wp_add_inline_script('wpb-custom-code-js', stripslashes($wpb_custom_code['js_code']));


        wp_deregister_style('wp-admin');
        wp_enqueue_style('wpb-select2', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/css/select2.min.css', array(), IQWPB_VERSION);
        wp_enqueue_style('wpb-select2', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/css/datatables.min.css', array(), IQWPB_VERSION);
    }

    public function enqueueScripts()
    {
        wp_register_script('wpb-select2', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/js/select2.min.js', [], IQWPB_VERSION);
        wp_register_script('wpb-datatables', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/js/datatables.min.js', [], IQWPB_VERSION);
        wp_register_script('wpb-jquery-validate', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/js/jquery.validate.min.js', ['jquery'], IQWPB_VERSION);
        wp_register_script('wpb-printarea', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/js/printarea.js', [], IQWPB_VERSION);

        Kucrut\Vite\enqueue_asset(
            IQWPB_PLUGIN_PATH . 'core/dist',
            'core/admin/assets/src/main.js',
            [
                'handle' => 'wpbookit-dashbord',
                'dependencies' => ['wpb-select2', 'wpb-printarea', 'wpb-datatables', 'underscore', 'wpb-custom-code-js', 'wp-i18n','wpb-jquery-validate'],
                'css-dependencies' => ['wpb-custom-code-css'],
                'css-media' => 'all',
                'css-only' => false,
                'in-footer' => true,
            ]
        );
        wp_localize_script(
            'wpbookit-dashbord',
            'wpbookit',
            [
                'date_format' => get_option('date_format'),
                'time_format' => get_option('time_format'),
                'wpb_ajax_url' => admin_url('admin-ajax.php'),
                'datatable_language' => [
                    "emptyTable" => esc_html__("No data available", 'wpbookit'),
                    "info" => esc_html__("Showing _START_ to _END_ of _TOTAL_ entries", 'wpbookit'),
                    "infoEmpty" => esc_html__("Showing 0 to 0 of 0 entries", 'wpbookit'),
                    "infoFiltered" => esc_html__("(filtered from _MAX_ total entries)", 'wpbookit'),
                    "lengthMenu" => esc_html__("Show _MENU_ entries", 'wpbookit'),
                    "loadingRecords" => '',
                    "processing" => esc_html__("Loading...", 'wpbookit'),
                    "search" => esc_html__("Search:", 'wpbookit'),
                    "zeroRecords" => esc_html__("No matching records found", 'wpbookit'),
                    "paginate" => [
                        "first" => esc_html_x("«", 'datatable pagination', 'wpbookit'),
                        "last" => esc_html_x("»", 'datatable pagination', 'wpbookit'),
                        "next" => esc_html_x("›", 'datatable pagination', 'wpbookit'),
                        "previous" => esc_html_x("‹", 'datatable pagination', 'wpbookit')
                    ],
                    "aria" => [
                        "sortAscending" => esc_html__(": activate to sort column ascending", 'wpbookit'),
                        "sortDescending" => esc_html(": activate to sort column descending", 'wpbookit')
                    ],
                ],
                'flatpicker' => array(
                    'weekdays' => array(
                        'shorthand' => array(
                            esc_html__('Sun', 'wpbookit'),
                            esc_html__('Mon', 'wpbookit'),
                            esc_html__('Tue', 'wpbookit'),
                            esc_html__('Wed', 'wpbookit'),
                            esc_html__('Thu', 'wpbookit'),
                            esc_html__('Fri', 'wpbookit'),
                            esc_html__('Sat', 'wpbookit')
                        ),
                        'longhand' => array(
                            esc_html__('Sunday', 'wpbookit'),
                            esc_html__('Monday', 'wpbookit'),
                            esc_html__('Tuesday', 'wpbookit'),
                            esc_html__('Wednesday', 'wpbookit'),
                            esc_html__('Thursday', 'wpbookit'),
                            esc_html__('Friday', 'wpbookit'),
                            esc_html__('Saturday', 'wpbookit')
                        )
                    ),
                    'months' => array(
                        'shorthand' => array(
                            esc_html__('Jan', 'wpbookit'),
                            esc_html__('Feb', 'wpbookit'),
                            esc_html__('Mar', 'wpbookit'),
                            esc_html__('Apr', 'wpbookit'),
                            esc_html__('May', 'wpbookit'),
                            esc_html__('Jun', 'wpbookit'),
                            esc_html__('Jul', 'wpbookit'),
                            esc_html__('Aug', 'wpbookit'),
                            esc_html__('Sep', 'wpbookit'),
                            esc_html__('Oct', 'wpbookit'),
                            esc_html__('Nov', 'wpbookit'),
                            esc_html__('Dec', 'wpbookit')
                        ),
                        'longhand' => array(
                            esc_html__('January', 'wpbookit'),
                            esc_html__('February', 'wpbookit'),
                            esc_html__('March', 'wpbookit'),
                            esc_html__('April', 'wpbookit'),
                            esc_html__('May', 'wpbookit'),
                            esc_html__('June', 'wpbookit'),
                            esc_html__('July', 'wpbookit'),
                            esc_html__('August', 'wpbookit'),
                            esc_html__('September', 'wpbookit'),
                            esc_html__('October', 'wpbookit'),
                            esc_html__('November', 'wpbookit'),
                            esc_html__('December', 'wpbookit')
                        )
                    ),
                    'firstDayOfWeek' =>  get_option('start_of_week', 1),
                    'weekAbbreviation' => esc_html__('Wk', 'wpbookit'),
                    'scrollTitle' => esc_html__('Scroll to increment', 'wpbookit'),
                    'toggleTitle' => esc_html__('Click to toggle', 'wpbookit'),
                    'amPM' => array(
                        esc_html__('AM', 'wpbookit'),
                        esc_html__('PM', 'wpbookit')
                    ),
                    'yearAriaLabel' => esc_html__('Year', 'wpbookit'),
                    'monthAriaLabel' => esc_html__('Month', 'wpbookit'),
                    'hourAriaLabel' => esc_html__('Hour', 'wpbookit'),
                    'minuteAriaLabel' => esc_html__('Minute', 'wpbookit'),
                ),
                'wpb_plugin_url' => IQWPB_PLUGIN_URL,
                '_ajax_nonce' => wp_create_nonce('wpb_ajax_nonce'),
                'dashbord_language'=> [
                    'validation' => [
                        // Booking
                        "select_booking_type" => __("Please Select booking type", 'wpbookit'),
                        "select_booking_date" => __("Please Select Booking Date", 'wpbookit'),
                        "select_booking_time" => __("Please Select Booking Time", 'wpbookit'),
                        "select_customer" => __("Please Select Customer", 'wpbookit'),
                        "select_booking_status" => __("Please Select Booking Status", 'wpbookit'),
                        "select_booking_payment_status" => __("Please Select Booking Payment Status", 'wpbookit'),
                        "select_booking_payment_mode" => __("Please Select Booking Payment Mode", 'wpbookit'),
                        // Bookint Type moduel Validation
                        "select_cover_image" => __("Please Select Cover Image", 'wpbookit'),
                        "enter_title" => __("Please Enter Title", 'wpbookit'),
                        "enter_slug" => __("Please Enter slug", 'wpbookit'),
                        "enter_url" => __("Please Enter url", 'wpbookit'),
                        "select_duration" => __("Please Select Duration", 'wpbookit'),
                        "enter_description" => __("Please Enter Description", 'wpbookit'),
                        "enter_time_slot" => __("Please enter proper time slot", 'wpbookit'),
                        "select_date_time" => __("Please Select At least one date and time", 'wpbookit'),
                        "questions_not_enter" => __("Please Enter Your Queston", 'wpbookit'),
                        "enter_meeting_url" => __("Custom Meeting URL require Pro ", 'wpbookit'),
                        "enter_valid_url" => __("Please enter a valid URL", 'wpbookit'),
                        "enter_meeting_address" => __("Please enter meeting address", 'wpbookit'),
                        "enter_valid_seats" => __("Please enter a valid number of seats", 'wpbookit'),
                        "enter_valid_url_redirection" => __("Please enter a valid URL.", 'wpbookit'),
                        "require_avaible_day" => __("At list one avaible day require", 'wpbookit'),

                        // Customer 
                        "first_name_required" => __("First name is required", 'wpbookit'),
                        "first_name_invalid" => __("First name can only contain alphabetic characters", 'wpbookit'),
                        "last_name_required" => __("Last name is required", 'wpbookit'),
                        "last_name_invalid" => __("Last name can only contain alphabetic characters", 'wpbookit'),
                        "email_required" => __("Email address is required", 'wpbookit'),
                        "email_invalid" => __("Please enter a valid email address", 'wpbookit'),
                        "phone_invalid" => __("Please enter a valid phone number", 'wpbookit'),
                        "invalid_file_type" => __("Invalid file type. Please upload one of the following", 'wpbookit'),
                        "invalid_file_type" => __("Select type", 'wpbookit'),
                    ],
                    'placeholder' =>[
                        "plh_enter_question" => __("Enter Your Question", 'wpbookit'),
                        "plh_option_separated_comma" => __("Enter options separated by comma", 'wpbookit'),
                    ],
                    'booking' =>[
                       'confirm_delete_boooking'=> __("Do you want to delete this booking: ", "wpbookit")
                    ],
                    'booking_type' =>[
                       'confirm_delete_boooking_type'=> __("Do you want to delete this booking type: ", "wpbookit")
                    ],
                    'customer' =>[
                       'confirm_delete_customer'=> __("Do you want to delete this customer: ", "wpbookit")
                    ],
                    'guest' =>[
                       'confirm_delete_guest'=> __("Do you want to delete this customer: ", "wpbookit")
                    ],
                    'comman'=>[
                        "free" => __("Free", 'wpbookit'),
                        "copied" => __("Copied", 'wpbookit'),
                        "fail_copied" => __("Failed to copy", 'wpbookit'),
                        "click_to_copy" => __("Click to Copy", 'wpbookit'),
                        "sub_total" => __("Sub Total", 'wpbookit'),
                        "total" => __("Total", 'wpbookit'),
                        "remove" => __("Remove", 'wpbookit'),
                        "delete" => __("Delete", 'wpbookit'),
                        "delete" => __("Delete", 'wpbookit'),
                    ]
                ]
            ]
        );
    }

    public function load_wpb_settings()
    {
        WPB_Admin_Settings::output();
    }

    public static function load_library()
    {
        include_once IQWPB_PLUGIN_PATH . 'vendor/kucrut/vite-for-wp/vite-for-wp.php';
    }

    public function load_sidebar($tabs, $current_tab)
    {
        include_once IQWPB_PLUGIN_PATH . "core/admin/views/layouts/sidebar.php";
    }

    public function load_navigation($tabs, $current_tab)
    {
        $current_user = wp_get_current_user();
        include_once IQWPB_PLUGIN_PATH . "core/admin/views/layouts/header.php";
    }

    public function load_footer($tabs, $current_tab)
    {
        include_once IQWPB_PLUGIN_PATH . "core/admin/views/layouts/footer.php";
    }

    public function load_settings_classes()
    {
        if (!is_admin() || !isset($_GET['page']) || 'wpbookit-dashboard' !== $_GET['page']) :
            return;
        endif;

        WPB_Admin_Settings::get_settings_pages();
    }
}
