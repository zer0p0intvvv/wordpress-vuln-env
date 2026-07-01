<?php

/**
 * Installation related functions and actions.
 */


defined('ABSPATH') || exit;

if (!class_exists('WPB_install')) :

    class WPB_install
    {

        /**
         * Hook in tabs.
         */
        public static function init()
        {
            add_action('init', array(__CLASS__, 'install'), 5);
            if (!get_option('_wpb_email_list') == true)
                add_action('init', array(__CLASS__, 'add_emails_list'), 5);

            add_action('show_user_profile', array(__CLASS__, 'add_custom_user_fields'));
            add_action('edit_user_profile', array(__CLASS__, 'add_custom_user_fields'));
            add_action('personal_options_update', array(__CLASS__, 'save_custom_user_fields'));
            add_action('edit_user_profile_update', array(__CLASS__, 'save_custom_user_fields'));

            self::wpdb_intialize();

            add_action('admin_init',array(__CLASS__, 'add_wpb_user_roles'));
            add_action('admin_init',array(__CLASS__, 'wpb_db_migrate'));
        }

        /**
         * Install WPB booked plugin.
         */
        public static function install()
        {
            if (!is_blog_installed())
                return;
            self::create_tables();
            self::setDefaultGeneralSettingData();
            self::setDefaultThemeSettingData();
        }

        public static function setDefaultGeneralSettingData()
        {
            if (!get_option('wpb_general_setting_data')) {

                $data = array(
                    'booking_type'                      => 'registered',
                    'include_exclude_tax'               => 'incl',
                    'booking_options'                   => 'name-only',
                    'require_guest_email_address'       => 'true',
                    'require_guest_phone_number'        => 'true',
                    'booking_status'                    => 'pending',
                    'booking_limit'                     => 'no-limit',
                    'cancellation_buffer'               => 'no-buffer',
                    'login_redirect'                    => 'same-page',
                    'booking_redirect'                  => 'no_redirect',
                    'booking_redirect_url'              => site_url(),
                    'permalink_strcture'                => 'booking',
                    'prefix'                            => '$',
                    'postfix'                           => '/-',
                    'action'                            => 'wpb_ajax_post',
                    'route_name'                        => 'add_general_setting',
                    'currency'                          => 'USD',
                    'minimum_time_before_cancellation'  => '0',
                    'disabled_woocommerce_tax'          => 'false'
                );
                update_option('wpb_general_setting_data', $data);
            }
        }

        public static function setDefaultThemeSettingData()
        {
            if (!get_option('wpb_theme_setting_data')) {

                $data = array(
                    'copyright_text' => '© 2024 WPBookit, Made with ❤️ by IQONIC DESIGN',
                    'dashboard_name' => "WPBookit"
                );
                update_option('wpb_theme_setting_data', $data);
            }
        }

        public static function create_tables()
        {
            if (!get_option('wpbookit_table_created_v1.0.0')) {
                global $wpdb;
                $wpdb->hide_errors();
                require_once ABSPATH . 'wp-admin/includes/upgrade.php';
                $db_delta_result = dbDelta(self::get_schema());
                update_option('wpbookit_table_created_v1.0.0', true);
                return $db_delta_result;
            }
        }

        private static function wpdb_intialize()
        {
            global $wpdb;
            $wpdb->wpb_booking_type = $wpdb->prefix . 'wpb_booking_type';
            $wpdb->wpb_booking_typemeta = $wpdb->prefix . 'wpb_booking_typemeta';
            $wpdb->wpb_bookings = $wpdb->prefix . 'wpb_bookings';
            $wpdb->wpb_bookingsmeta = $wpdb->prefix . 'wpb_bookingsmeta';
            $wpdb->wpb_booking_emails = $wpdb->prefix . 'wpb_booking_emails';
            $wpdb->wpb_payments = $wpdb->prefix . 'wpb_payments';
            $wpdb->wpb_guest_users = $wpdb->prefix . 'wpb_guest_users';
            $wpdb->wpb_tax = $wpdb->prefix . 'wpb_tax';
        }

        private static function get_schema()
        {
            global $wpdb;
            $collate = '';
            if ($wpdb->has_cap('collation')) :
                $collate = $wpdb->get_charset_collate();
            endif;

            $tables = "
                    CREATE TABLE {$wpdb->wpb_booking_type} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        name TEXT NOT NULL,
                        slug VARCHAR(200) NOT NULL,
                        description VARCHAR(2000) NOT NULL DEFAULT '',
                        type VARCHAR(200) NOT NULL DEFAULT '',
                        unavailable ENUM('0', '1') NOT NULL DEFAULT '0',
                        duration VARCHAR(200) NOT NULL DEFAULT '',
                        url VARCHAR(200) NOT NULL DEFAULT '',
                        status VARCHAR(30) NOT NULL DEFAULT 'enable',
                        PRIMARY KEY (id)
                    ) $collate;
                    
                    CREATE TABLE {$wpdb->wpb_booking_typemeta} (
                        meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        wpb_booking_type_id BIGINT(20) UNSIGNED NOT NULL,
                        meta_key VARCHAR(255) DEFAULT NULL,
                        meta_value LONGTEXT NULL,
                        PRIMARY KEY (meta_id)
                    ) $collate;
                    
                    CREATE TABLE {$wpdb->wpb_bookings} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        booking_type_id BIGINT(20) UNSIGNED NOT NULL,
                        customer_id BIGINT(20) UNSIGNED NOT NULL,
                        booking_name TEXT NOT NULL,
                        booking_email TEXT NOT NULL,
                        booking_type TEXT NOT NULL,
                        booking_date DATE NOT NULL,
                        timeslot TIME NOT NULL,
                        status VARCHAR(20) NOT NULL,
                        date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        duration BIGINT(20) UNSIGNED NOT NULL,  
                        PRIMARY KEY (id)
                    ) $collate;
                    
                    CREATE TABLE {$wpdb->wpb_bookingsmeta} (
                        meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        wpb_bookings_id BIGINT(20) UNSIGNED NOT NULL,
                        meta_key VARCHAR(255) DEFAULT NULL,
                        meta_value LONGTEXT NULL,
                        PRIMARY KEY (meta_id)
                    ) $collate;
                    
                    CREATE TABLE {$wpdb->wpb_booking_emails} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        status TINYINT(1) NOT NULL DEFAULT 0,
                        emails_title VARCHAR(255) NOT NULL DEFAULT '',
                        emails_heading VARCHAR(255) NOT NULL DEFAULT '',
                        emails_subject VARCHAR(255) NOT NULL DEFAULT '',
                        emails_content TEXT NOT NULL DEFAULT '',
                        is_reminder TINYINT(1) NOT NULL DEFAULT 0,
                        reminder BIGINT(20) DEFAULT NULL,
                        role VARCHAR(255) NOT NULL DEFAULT '',
                        PRIMARY KEY (id) 
                    ) $collate;    
                    
                    CREATE TABLE {$wpdb->wpb_payments} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        bookings_id BIGINT(20) UNSIGNED NOT NULL,
                        payment_mode VARCHAR(150) NOT NULL DEFAULT '',
                        subtotal_amount VARCHAR(50) NOT NULL,
                        total_amount VARCHAR(50) NOT NULL,
                        paid_amount VARCHAR(50) NULL,
                        payment_status VARCHAR(20) NOT NULL DEFAULT '',
                        date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        transaction_id VARCHAR(150) NOT NULL DEFAULT '',
                        PRIMARY KEY (id)
                    ) $collate;

                    CREATE TABLE {$wpdb->wpb_tax} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        tax_booking_type VARCHAR(255) NOT NULL,
                        status TINYINT(1) NOT NULL DEFAULT 0,
                        tax_name VARCHAR(255) NOT NULL,
                        tax_rate VARCHAR(50) NOT NULL,
                        tax_type VARCHAR(255) NOT NULL,
                        inclusive_tax VARCHAR(255) NOT NULL,
                        tax_priority INT NOT NULL DEFAULT 1,
                        PRIMARY KEY (id)
                    ) $collate;

                    CREATE TABLE {$wpdb->wpb_guest_users} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,  
                        guest_name VARCHAR(255) NOT NULL,
                        guest_email VARCHAR(255) NOT NULL,
                        PRIMARY KEY (id)
                    ) $collate;
                ";
            return $tables;
        }


        public static function add_custom_user_fields($user)
        {
            require_once IQWPB_PLUGIN_PATH . 'core/admin/views/settings/html-admin-settings-custom-fields.php';
        }

        public static function save_custom_user_fields($user_id)
        {
            if (current_user_can('edit_user', $user_id)) :
                update_user_meta($user_id, 'gender', sanitize_text_field($_POST['gender']));
                update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
                update_user_meta($user_id, 'custom_note', sanitize_textarea_field($_POST['custom_note']));
            endif;
        }

        public static function add_emails_list()
        {
            $email_list = array(
                array(
                    'status' => 1,
                    'emails_title' => 'Customer Booking Reminder',
                    'emails_heading' => 'Example Subject',
                    'emails_subject' => 'Reminder: You have an appointment coming up soon!',
                    'emails_content' => '<p> Hey {{customer_name}},</p><p> Just a friendly reminder that you have a booking coming up soon! Here is the booking information: </p><p> Date: {{booking_date}} </p><p> Time: {{booking_time}} </p><p> Thank you. </p>',
                    'is_reminder' => 1,
                    'reminder' => '',
                    'role' => 'Customer'
                ),
                array(
                    'status' => 1,
                    'emails_title' => 'Customer Registration',
                    'emails_heading' => 'Example Heading',
                    'emails_subject' => 'Thank you for registering!',
                    'emails_content' => '<p> Hey {{customer_name}},</p><p> Thanks for registering. </p><p> You can now login to manage your account and bookings using the following credentials: </p><p> Email Address: {{customer_email}} </p><p> User Name: {{customer_name}} </p><p> Login URL: {{login_url}} </p><p> Password: {{password}} </p><p> Thank you. </p>',
                    'is_reminder' => 0,
                    'reminder' => '',
                    'role' => 'Customer'
                ),
                array(
                    'status' => 1,
                    'emails_title' => 'Customer Booking Confirmation',
                    'emails_heading' => 'Example Heading',
                    'emails_subject' => 'Your appointment confirmation from booked.',
                    'emails_content' => '<p> Hey {{customer_name}},</p><p> This is just an email to confirm your appointment. For reference, here is the appointment information: </p><p> Date: {{booking_date}} </p><p> Time: {{booking_time}} </p><p> Thank you. </p>',
                    'is_reminder' => 0,
                    'reminder' => '',
                    'role' => 'Customer'
                ),
                array(
                    'status' => 1,
                    'emails_title' => 'Customer Booking Approval',
                    'emails_heading' => 'Example Subject',
                    'emails_subject' => 'Your appointment has been approved!',
                    'emails_content' => '<p> Hey {{customer_name}},</p><p> The appointment you requested at booked has been approved! Here is your appointment information: </p><p> Date: {{booking_date}} </p><p> Time: {{booking_time}} </p><p> Thank you. </p>',
                    'is_reminder' => 0,
                    'reminder' => '',
                    'role' => 'Customer'
                ),
                array(
                    'status' => 1,
                    'emails_title' => 'Customer Booking Cancellation',
                    'emails_heading' => 'Example Subject',
                    'emails_subject' => 'Your appointment has been cancelled.',
                    'emails_content' => '<p> Hey {{customer_name}},</p><p> The appointment you requested at booked has been cancelled. For reference, here is the appointment information: </p><p> Date: {{booking_date}} </p><p> Time: {{booking_time}} </p><p> Thank you. </p>',
                    'is_reminder' => 0,
                    'reminder' => '',
                    'role' => 'Customer'
                ),
            );

            wpb_add_email($email_list);
            add_option('_wpb_email_list', true);
        }

        public static function add_wpb_user_roles()
        {
            if (!get_option('_wpb_add_user_role') == true) {
                foreach (WPBOOKIT()->helpers->get_roles() as $key => $value) :
                    add_role($value['role'], $value['lable'], $value['permission']);
                endforeach;

                $role = get_role('administrator');
                if ($role) :
                    $role->add_cap('manage_wpbookit');
                endif;
                add_option('_wpb_add_user_role', true);
            }
        }
        public static function wpb_db_migrate(){
            global $wpdb;
            $collate = '';
            if ($wpdb->has_cap('collation')) :
                $collate = $wpdb->get_charset_collate();
            endif;
            
            // Make sure we have access to dbDelta function
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            
            if (!get_option('_wpb_migrate_payment_table') == true){
                global $wpdb;
                wpb_update_fields_data_type($wpdb->wpb_payments,[
                    'transaction_id' => 'varchar(50)',
                ]);
                add_option('_wpb_migrate_payment_table', true);
            }

            if (!get_option('_wpb_migrate_tax_table') == true){
                $tax_table_sql = "CREATE TABLE {$wpdb->wpb_tax} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        tax_booking_type VARCHAR(255) NOT NULL,
                        status TINYINT(1) NOT NULL DEFAULT 0,
                        tax_name VARCHAR(255) NOT NULL,
                        tax_rate VARCHAR(50) NOT NULL,
                        tax_type VARCHAR(255) NOT NULL,
                        inclusive_tax VARCHAR(255) NOT NULL,
                        tax_priority INT NOT NULL DEFAULT 1,
                        PRIMARY KEY (id)
                    ) $collate;";
                
                dbDelta($tax_table_sql);
                add_option('_wpb_migrate_tax_table', true);
            }

            if (!get_option('_wpb_migrate_guest_table_') == true){
                $guest_table_sql = "CREATE TABLE {$wpdb->wpb_guest_users} (
                        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,  
                        guest_name VARCHAR(255) NOT NULL,
                        guest_email VARCHAR(255) NOT NULL,
                        PRIMARY KEY (id)
                    ) $collate;";
                
                dbDelta($guest_table_sql);
                add_option('_wpb_migrate_guest_table_', true);
            }
            if (!get_option('_wpb_migrate_guest_table_add_phone_col_') == true){
                global $wpdb;
                wpb_update_fields_data_type($wpdb->wpb_guest_users,[
                    'guest_phone_number' => 'varchar(25)',
                ]);
                add_option('_wpb_migrate_guest_table_add_phone_col_', true);
            }
        }

    }
    WPB_install::init();

endif;
