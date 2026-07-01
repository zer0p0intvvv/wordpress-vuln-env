<?php

if( ! class_exists( 'Chart_Builder_Settings_DB_Actions' ) ){
	ob_start();

	/**
	 * Class Chart_Builder_Settings_DB_Actions
	 * Class contains functions to interact with settings database
	 *
	 * Main functionality belong to inserting, updating and deleting
	 *
	 * Hooks used in the class
	 * @hooks           @filters        ays_cb_settings_page_integrations_saves
	 *                                  ays_chart_item_save_settings
	 *
	 * Database tables without prefixes
	 * @tables          settings
	 *
	 * @param           $plugin_name
	 *
	 * @since           1.0.0
	 * @package         Chart_Builder
	 * @subpackage      Chart_Builder/includes
	 * @author          Chart Builder Team <info@ays-pro.com>
	 */
    class Chart_Builder_Settings_DB_Actions {

	    /**
	     * The ID of this plugin.
	     *
	     * @since       1.0.0
	     * @access      private
	     * @var         string    $plugin_name    The ID of this plugin.
	     */
	    private $plugin_name;

	    /**
	     * The name of table in the database.
	     *
	     * @since       1.0.0
	     * @access      private
	     * @var         string    $db_table    The name of database table.
	     */
	    private $db_table;

	    /**
	     * The constructor of the class
	     *
	     * @since       1.0.0
	     * @access      public
	     *
	     * @param       $plugin_name
	     */
        public function __construct( $plugin_name ) {

	        global $wpdb;

	        /**
	         * Assigning $plugin_name to the @plugin_name property
	         */
	        $this->plugin_name = $plugin_name;

	        /**
	         * Assigning database @charts table full name to the @db_table property
	         */
	        $this->db_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "settings";

        }

	    /**
	     * Get instance of this class
	     *
	     * @since       1.0.0
	     * @access      public
	     *
	     * @param       $plugin_name
	     *
	     * @return      Chart_Builder_DB_Actions
	     */
	    public static function get_instance( $plugin_name ){
		    return new self( $plugin_name );
	    }

        public function store_data(){

            if( isset( $_REQUEST["settings_action"] ) && wp_verify_nonce( $_REQUEST["settings_action"], 'settings_action' ) ){
                $success = 0;
                $name_prefix = 'ays_';

	            $user_roles = (isset($_REQUEST['ays_user_roles']) && !empty($_REQUEST['ays_user_roles'])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_user_roles'] ) : array('administrator');

                // User roles to change plugin
                $user_roles_to_change_plugin = (isset($_REQUEST[$name_prefix . 'user_roles_to_change_plugin']) && !empty( $_REQUEST[$name_prefix . 'user_roles_to_change_plugin'] ) ) ? array_map( 'sanitize_text_field', $_REQUEST[$name_prefix . 'user_roles_to_change_plugin'] ) : array('administrator');

                // // Do not store IP addresses
                // $disable_user_ip = (isset($_REQUEST[$name_prefix . 'chart_disable_user_ip']) && $_REQUEST[$name_prefix . 'chart_disable_user_ip'] == 'on') ? stripslashes( sanitize_text_field( $_REQUEST[$name_prefix . 'chart_disable_user_ip'] ) ) : '';

                $chart_title_length = (isset($_REQUEST[$name_prefix . 'chart_title_length']) && $_REQUEST[$name_prefix . 'chart_title_length'] != '') ? absint( sanitize_text_field( $_REQUEST[$name_prefix . 'chart_title_length'] ) ) : 5;

                // // Textarea height (public)
                // $textarea_height = (isset($_REQUEST[$name_prefix . 'chart_textarea_height']) && $_REQUEST[$name_prefix . 'chart_textarea_height'] != '' && $_REQUEST[$name_prefix . 'chart_textarea_height'] != 0 ) ? absint( sanitize_text_field($_REQUEST[$name_prefix . 'chart_textarea_height']) ) : 100;

                // // WP Editor height
                // $wp_editor_height = (isset($_REQUEST[$name_prefix . 'chart_wp_editor_height']) && $_REQUEST[$name_prefix . 'chart_wp_editor_height'] != '' && $_REQUEST[$name_prefix . 'chart_wp_editor_height'] != 0) ? absint( sanitize_text_field($_REQUEST[$name_prefix . 'chart_wp_editor_height']) ) : 100 ;

                $options = array(
                    // "disable_user_ip"            => $disable_user_ip,
                    "title_length"               => $chart_title_length,
                    // "textarea_height"            => $textarea_height,
                    // "wp_editor_height"           => $wp_editor_height,

                    // User roles options
                    "user_roles_to_access"       => $user_roles,
                    "user_roles_to_change"       => $user_roles_to_change_plugin,
                );

                $fields = array();

                $fields['user_roles'] = $user_roles;
                $fields['options'] = $options;

                $fields = apply_filters( 'ays_cb_settings_page_integrations_saves', $fields );

                foreach ($fields as $key => $value) {
                    $result = $this->update_setting( $key, json_encode( $value ) );
                    if($result){
                        $success++;
                    }
                }

                $message = "saved";
                if($success > 0){
                    $tab = "";
                    if( isset( $_REQUEST['ays_tab'] ) ){
                        $tab = "&ays_tab=". sanitize_text_field( $_REQUEST['ays_tab'] );
                    }

                    $url = admin_url('admin.php') . "?page=". $this->plugin_name ."-settings" . $tab . '&status=' . $message;
                    wp_redirect( $url );
                    exit();
                }
            }

        }

	    /**
	     * @return array
	     */
        public function get_all_data(){
            global $wpdb;

            $sql = "SELECT * FROM " . $this->db_table;

            $results = $wpdb->get_results($sql, ARRAY_A);

            if( count( $results ) > 0 ){
                return $results;
            }else{
                return array();
            }
        }

	    /**
	     * Get record meta by record id and meta key
	     *
	     * @since       1.0.0
	     * @access      public
	     *
	     * @param       $id
	     * @param       $meta_key
	     *
	     * @return      false|array
	     */
	    public function get_setting( $meta_key ){
		    global $wpdb;

		    if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
			    return false;
		    }

		    $sql = "SELECT meta_value FROM ". $this->db_table ." WHERE meta_key = '".$meta_key."'";
		    $result = $wpdb->get_var($sql);

		    if($result != ""){
			    return $result;
		    }

		    return false;
	    }

		public function update_setting( $meta_key, $meta_value, $note = "", $options = "" ){
		    global $wpdb;

		    if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
			    return false;
		    }

		    $value = array(
			    'meta_value'  => $meta_value,
		    );

		    $value_s = array( '%s' );
		    if($note != null){
			    $value['note'] = $note;
			    $value_s[] = '%s';
		    }

		    if($options != null){
			    $value['options'] = $options;
			    $value_s[] = '%s';
		    }

		    $result = $wpdb->update(
			    $this->db_table,
			    $value,
			    array(
				    'meta_key' => $meta_key,
			    ),
			    $value_s,
			    array( '%s' )
		    );

		    if($result >= 0){
			    return true;
		    }

		    return false;
	    }
    
		public function get_listtables_title_length() {
			global $wpdb;
	
			$sql = "SELECT meta_value FROM ".$this->db_table." WHERE meta_key = 'options'";
			$result = $wpdb->get_var($sql);
			$options = ($result == "") ? array() : json_decode(stripcslashes($result), true);
	
			$listtable_title_length = 5;
			if( !empty($options) ){
				$listtable_title_length = (isset($options['title_length']) && intval($options['title_length']) != 0) ? absint(intval($options['title_length'])) : 5;
			}
			return $listtable_title_length;
		}
    }
}
