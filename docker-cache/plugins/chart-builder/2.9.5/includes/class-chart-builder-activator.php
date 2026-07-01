<?php
global $ays_chart_db_version;
$ays_chart_db_version = '1.0.2';

/**
 * Fired during plugin activation
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Chart_Builder
 * @subpackage Chart_Builder/includes
 * @author     Chart Builder Team <info@ays-pro.com>
 */
class Chart_Builder_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        global $wpdb;
        global $ays_chart_db_version;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $installed_ver = get_option( "ays_chart_db_version" );
        $charts_table                   = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'charts';
        $charts_meta_table              = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'charts_meta';
        $settings_table                 = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'settings';
        $charset_collate = $wpdb->get_charset_collate();

        if( $installed_ver != $ays_chart_db_version )  {

            $sql = "CREATE TABLE `".$charts_table."` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `author_id` INT(16) UNSIGNED NOT NULL DEFAULT '0',
                `title` TEXT NOT NULL,
                `description` TEXT NOT NULL DEFAULT '',
                `type` VARCHAR(256) NOT NULL DEFAULT '',
                `source_chart_type` VARCHAR(256) NOT NULL DEFAULT '',
                `source_type` VARCHAR(256) NOT NULL DEFAULT '',
                `source` LONGTEXT NOT NULL DEFAULT '',
                `date_created` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
                `date_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
                `status` VARCHAR(256) NOT NULL DEFAULT 'published',
                `trash_status` VARCHAR(256) NOT NULL DEFAULT '',
                `ordering` INT(16) NOT NULL,
                `quiz_query` VARCHAR(256) DEFAULT '',
                `quiz_id` INT(16) UNSIGNED DEFAULT '0',
                `options` TEXT NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
            )$charset_collate;";

            $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                           WHERE table_schema = '".DB_NAME."' AND table_name = '".$charts_table."' ";
            $results = $wpdb->get_results($sql_schema);

            if( empty( $results ) ){
                $wpdb->query( $sql );
            }else{
                dbDelta( $sql );
            }


            $sql = "CREATE TABLE `".$charts_meta_table."` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `chart_id` INT(11) UNSIGNED NOT NULL,
                `meta_key` TEXT NOT NULL DEFAULT '',
                `meta_value` TEXT NOT NULL DEFAULT '',
                `note` TEXT NOT NULL DEFAULT '',
                `options` TEXT NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
            )$charset_collate;";

            $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                           WHERE table_schema = '".DB_NAME."' AND table_name = '".$charts_meta_table."' ";
            $results = $wpdb->get_results($sql_schema);

            if(empty($results)){
                $wpdb->query( $sql );
            }else{
                dbDelta( $sql );
            }


            $sql = "CREATE TABLE `".$settings_table."` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `meta_key` TEXT NOT NULL DEFAULT '',
                `meta_value` TEXT NOT NULL DEFAULT '',
                `note` TEXT NOT NULL DEFAULT '',
                `options` TEXT NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
            )$charset_collate;";

            $sql_schema = "SELECT * FROM INFORMATION_SCHEMA.TABLES
                           WHERE table_schema = '".DB_NAME."' AND table_name = '".$settings_table."' ";
            $results = $wpdb->get_results($sql_schema);

            if(empty($results)){
                $wpdb->query( $sql );
            }else{
                dbDelta( $sql );
            }

            update_option( 'ays_chart_db_version', $ays_chart_db_version );

        }

        $metas = array(
            "user_roles",
            "google",
            "options"
        );

        foreach($metas as $meta_key){
            $meta_val = "";
            if($meta_key == "user_roles"){
                $meta_val = json_encode(array('administrator'));
            }
            $sql = "SELECT COUNT(*) FROM `".$settings_table."` WHERE `meta_key` = '". esc_sql( $meta_key ) ."'";
            $result = $wpdb->get_var($sql);
            if(intval($result) == 0){
                $result = $wpdb->insert(
                    $settings_table,
                    array(
                        'meta_key'    => $meta_key,
                        'meta_value'  => $meta_val,
                        'note'        => "",
                        'options'     => ""
                    ),
                    array( '%s', '%s', '%s', '%s' )
                );
            }
        }

        self::create_default_chart();
	}

    public static function db_update_check() {
        global $ays_chart_db_version;
        if ( get_site_option( 'ays_chart_db_version' ) != $ays_chart_db_version ) {
            self::activate();
        }
    }

    public static function create_default_chart() {
        global $wpdb;
        global $ays_chart_db_version;

        $charts_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'charts';
        $charts_meta_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'charts_meta';
        $settings_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'settings';

        $chart_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $charts_table);
        $chart_meta_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $charts_meta_table);
        if ($chart_count == 0 && $chart_meta_count == 0) {
            $default_chart_source = array(
                "0" => ["User roles", "Visits"],
                "1" => ["Administrator","11"],
                "2" => ["Author","2"],
                "3" => ["Editor","5"],
                "4" => ["Guest","2"],
                "5" => ["Subscriber","7"]
            );

            $default_chart_options = array(
                //
            );

            $default_chart = array(
                'title' => 'Default chart',
                'description' => '',
                'type' => 'google-charts',
                'source_chart_type' => 'pie_chart',
                'source_type' => 'manual',
                'source' => json_encode($default_chart_source),
                'date_created' => current_time('mysql'),
                'date_modified' => current_time('mysql'),
                'status' => 'published',
                'trash_status' => '',
                'ordering' => '0',
                'options' => json_encode($default_chart_options),
                'author_id' => get_current_user_id()
            );

            $wpdb->insert($charts_table, $default_chart);
            $chart_id = $wpdb->insert_id;

            $default_chart_meta = array(
                array(
                    'chart_id' => $chart_id,
                    'meta_key' => 'width',
                    'meta_value' => '100',
                    'note' => '',
                    'options' => ''
                ),
                array(
                    'chart_id' => $chart_id,
                    'meta_key' => 'height',
                    'meta_value' => '400',
                    'note' => '',
                    'options' => ''
                ),
                array(
                    'chart_id' => $chart_id,
                    'meta_key' => 'title_color',
                    'meta_value' => '#000000',
                    'note' => '',
                    'options' => ''
                ),
                array(
                    'chart_id' => $chart_id,
                    'meta_key' => 'font_size',
                    'meta_value' => '14',
                    'note' => '',
                    'options' => ''
                ),
                array(
                    'chart_id' => $chart_id,
                    'meta_key' => 'tooltip_trigger',
                    'meta_value' => 'hover',
                    'note' => '',
                    'options' => ''
                ),
                array(
                    'chart_id' => $chart_id,
                    'meta_key' => 'show_color_code',
                    'meta_value' => 'off',
                    'note' => '',
                    'options' => ''
                )
            );

            foreach ($default_chart_meta as $key => $value) {
                $wpdb->insert($charts_meta_table, $value);
            }

            $default_chart['type'] = 'chart-js';
            $wpdb->insert($charts_table, $default_chart);
        } else {
            $wpdb->update(
                $charts_table,
                array(
                    'type' => 'google-charts',
                ),
                array(
                    'type' => 'google_charts',
                )
            );
        }
    }
}
