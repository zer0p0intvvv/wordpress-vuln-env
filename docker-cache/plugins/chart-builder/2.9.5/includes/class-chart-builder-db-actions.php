<?php
global $chart_builder_db_actions_notices;
$chart_builder_db_actions_notices = 0;

if( !class_exists( 'Chart_Builder_DB_Actions' ) ){
    ob_start();

	/**
	 * Class Chart_Builder_DB_Actions
	 * Class contains functions to interact with chart database
	 *
	 * Main functionality belong to inserting, updating and deleting of
	 * Also chart settings and options
	 *
	 * Hooks used in the class
	 * @hooks           @filters        ays_chart_item_save_options
	 *                                  ays_chart_item_save_settings
     *
     * Database tables without prefixes
     * @tables          charts
     *                  charts_meta
	 *
	 * @param           $plugin_name
     *
	 * @since           1.0.0
	 * @package         Chart_Builder
	 * @subpackage      Chart_Builder/includes
	 * @author          Chart Builder Team <info@ays-pro.com>
	 */
    class Chart_Builder_DB_Actions {

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
	     * The name of meta table in the database.
	     *
	     * @since       1.0.0
	     * @access      private
	     * @var         string    $db_table_meta    The name of database table.
	     */
        private $db_table_meta;

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
	        global $chart_builder_db_actions_notices;

	        /**
	         * Assigning $plugin_name to the @plugin_name property
	         */
            $this->plugin_name = $plugin_name;

	        /**
	         * Assigning database @charts table full name to the @db_table property
	         */
            $this->db_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts";

	        /**
	         * Assigning database @charts metas table full name to the @db_table_meta property
	         */
            $this->db_table_meta = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts_meta";


	        /**
	         * Adding action to admin_notices hook
             * Will work when there is some notice after some action
	         */
            if( $chart_builder_db_actions_notices === 0 ) {
	            add_action( 'admin_notices', array( $this, 'chart_notices' ) );
	            $chart_builder_db_actions_notices++;
            }

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

	    /**
         * Get records form database
         * Applying filters like per page and ordering
         *
         * @since       1.0.0
	     * @access      public
         *
	     * @return      array
	     */
        public function get_items(){
            global $wpdb;

            $per_page = $this->get_pagination_count();

            $page_number = 1;
            if ( ! empty( $_REQUEST['paged'] ) ) {
                $page_number = absint( sanitize_text_field( $_REQUEST['paged'] ) );
            }

            $sql = "SELECT * FROM " . $this->db_table;

            $sql .= self::get_where_condition();

            if ( ! empty( $_REQUEST['orderby'] ) ) {
                $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
                $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

                $sql_orderby = sanitize_sql_orderby( $order_by );

                if ( $sql_orderby ) {
                    $sql .= ' ORDER BY ' . $sql_orderby;
                } else {
                    $sql .= ' ORDER BY id DESC';
                }
            }else{
                $sql .= ' ORDER BY id DESC';
            }

            $p_page = ($page_number - 1) * $per_page;
            $sql .= " LIMIT " . $per_page;
            $sql .= " OFFSET " . $p_page;
            $result = $wpdb->get_results( $sql, 'ARRAY_A' );

            return $result;
        }

	    /**
	     * @return mixed
	     */
        public function get_pagination_count(){
            $per_page = get_user_meta( get_current_user_id(), 'cb_charts_per_page', true );
            if( $per_page == '' ){
                $per_page = 5;
            }
            $per_page = absint( $per_page );
            return $per_page;
        }

	    /**
         * Get WHERE condition for SQL queries that trying to get records
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @return      string
	     */
        public static function get_where_condition(){
            global $wpdb;
            $chart_types = array(
                "line_chart",
                "bar_chart",
                "pie_chart",
                "column_chart",
                "org_chart",
                "donut_chart",
            );            
            $chart_sources = array(
                "manual",
                "quiz_maker",
                "file_import",
                "google_sheet",
                "import_from_db",
                "woocommerce_data",
            ); 
            $chart_source_types = array(
                "google-charts",
                "chart-js",
            ); 
            $chart_dates = array(
                'today' => "",
                'yesterday' => "day",
                'last_week' => "week",
                'last_month' => "month",
                'last_year' => "year",
            );
            $where = array();
            $sql = '';

            $search = (isset($_REQUEST['s'])) ? sanitize_text_field($_REQUEST['s']) : false;
            if ($search) {
                $s = array();
                $s[] = sprintf( "`title` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
                $where[] = ' ( ' . implode(' OR ', $s) . ' ) ';
            }

            // if (isset( $_GET['fstatus'] ) && $_GET['fstatus'] != '') {
            //     $where[] = ' `status` = "' . esc_sql( sanitize_text_field( $_GET['fstatus'] ) ) . '" ';
            // } else {
	        //     $where[] = ' `status` != "trashed" ';
            // }
            if( isset( $_REQUEST['filterbytype'] ) && absint( sanitize_text_field( $_REQUEST['filterbytype'] ) ) > 0){
                $key = intval( sanitize_text_field( $_REQUEST['filterbytype'] ) );
                $where[] = ' `source_chart_type` = "'. $chart_types[$key-1] .'" ';
            }

            if( isset( $_REQUEST['filterbysource'] ) && absint( sanitize_text_field( $_REQUEST['filterbysource'] ) ) > 0){
                $key = intval( sanitize_text_field( $_REQUEST['filterbysource'] ) );
                $where[] = ' `source_type` = "'. $chart_sources[$key-1] .'" ';
            }
            
            if( isset( $_REQUEST['filterbychartsource'] ) && absint( sanitize_text_field( $_REQUEST['filterbychartsource'] ) ) > 0){
                $key = intval( sanitize_text_field( $_REQUEST['filterbychartsource'] ) );
                $where[] = ' `type` = "'. $chart_source_types[$key-1] .'" ';
            }

            if( isset( $_REQUEST['filterbydate'] ) && sanitize_text_field( $_REQUEST['filterbydate'] ) !== ''){
                $interval = sanitize_text_field( $_REQUEST['filterbydate'] );
                if ($chart_dates[$interval] !== '') {
                    $where[] = ' DATE(date_created) >= DATE_SUB(CURDATE(), INTERVAL 1 '. strtoupper($chart_dates[$interval]) .') AND DATE(date_created) < CURDATE() ';
                } else{
                    $where[] = ' DATE(date_created) = CURDATE() ';
                }
            }

            if( isset( $_REQUEST['filterbyauthor'] ) && absint( sanitize_text_field( $_REQUEST['filterbyauthor'] ) ) > 0){
                $where[] = ' `author_id` = "'. absint(sanitize_text_field($_REQUEST['filterbyauthor'])) .'" ';
            }

            if (!empty($where)) {
                $sql = " WHERE " . implode( " AND ", $where );
            }

            return $sql;
        }

        public static function get_search_value () {
            $search = (isset($_REQUEST['s'])) ? sanitize_text_field($_REQUEST['s']) : '';
            return $search;
        }

        public static function get_searched_author_info () {
            $id = (isset($_REQUEST['filterbyauthor'])) ? absint(sanitize_text_field($_REQUEST['filterbyauthor'])) : 0;
            $author_data = array();
            if ( $id && $id > 0 ) {
                global $wpdb;
                $users_table = esc_sql( $wpdb->prefix . 'users' );
                $sql_users = "SELECT ID, display_name FROM {$users_table} WHERE ID = {$id}";
        
                $author_data = $wpdb->get_row($sql_users, "ARRAY_A");
            }
            
            return $author_data;
        }

	    /**
         * Get record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      array|false
	     */
        public function get_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            $sql = "SELECT * FROM ". $this->db_table ." WHERE id = '". $id ."'";
            $result = $wpdb->get_row( $sql, ARRAY_A );

            if( $result ){
                return $result;
            }

            return false;
        }

	    /**
	     * Insert or update record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
         * @redirect    to specific page based on clicked button
	     * @param       $id
         *
	     * @return      false|void
	     */
        public function add_or_edit_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( isset( $_POST["chart_builder_action"] ) && wp_verify_nonce( $_POST["chart_builder_action"], 'chart_builder_action' ) ){
                $success = 0;
                $name_prefix = 'ays_';

                // Save type
                $save_type = isset( $_POST['save_type'] ) && $_POST['save_type'] != '' ? sanitize_text_field( $_POST['save_type'] ) : '';

	            // Author_id
	            $author_id = get_current_user_id();

	            // Title
                $title = isset( $_POST[ $name_prefix . 'title' ] ) && $_POST[ $name_prefix . 'title' ] != '' ? stripslashes( sanitize_text_field( $_POST[ $name_prefix . 'title' ] ) ) : 'Untitled chart';

                // if( $title == '' ){
                //     $message = 'empty-title';
                //     $url = esc_url_raw( remove_query_arg( false ) );
                //     $url = esc_url_raw( add_query_arg( array(
                //         'status' => 'empty-title'
                //     ), $url ) );
                //     wp_redirect( $url );
                //     exit();
                // }


                // Description
                $description = isset( $_POST[ $name_prefix . 'description' ] ) && $_POST[ $name_prefix . 'description' ] != '' ? stripslashes( sanitize_text_field($_POST[ $name_prefix . 'description' ]) ) : '';

                // Type
                $type = isset( $_POST[ $name_prefix . 'type' ] ) && $_POST[ $name_prefix . 'type' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'type' ] ) : 'google-charts';

                // Source chart type
                $source_chart_type = isset( $_POST[ $name_prefix . 'source_chart_type' ] ) && $_POST[ $name_prefix . 'source_chart_type' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'source_chart_type' ] ) : 'pie_chart';

                // Source type
                $source_type = isset( $_POST[ $name_prefix . 'source_type' ] ) && $_POST[ $name_prefix . 'source_type' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'source_type' ] ) : 'manual';

                // Manual data
                $chart_source_filtered_data = array();
                if ($source_chart_type == "org_chart") {
                    $chart_source_data_add = isset($_POST[ $name_prefix . 'chart_source_data_org_type' ]) && !empty( $_POST[ $name_prefix . 'chart_source_data_org_type' ] ) ? $_POST[ $name_prefix . 'chart_source_data_org_type' ] : array();
                    foreach($chart_source_data_add as $chart_source_data_key => $chart_source_data_value){
                        $chart_source_data_key = (int)filter_var($chart_source_data_key, FILTER_SANITIZE_NUMBER_INT);
                        foreach($chart_source_data_value as $s_data_key => $s_data_value){
                            if ($s_data_key === 5) {
                                $chart_source_filtered_data[$chart_source_data_key][] = (isset($s_data_value) && $s_data_value != '') ? esc_url( $s_data_value ) : '';
                            } else {
                                $chart_source_filtered_data[$chart_source_data_key][] = (isset($s_data_value) && $s_data_value != '') ? esc_attr(stripslashes( sanitize_text_field( $s_data_value ) )) : '';
                            }
                        }
                    }
                } else {
                    $chart_source_data_add = isset($_POST[ $name_prefix . 'chart_source_data' ]) && !empty( $_POST[ $name_prefix . 'chart_source_data' ] ) ? $_POST[ $name_prefix . 'chart_source_data' ] : array();
                    foreach($chart_source_data_add as $chart_source_data_key => $chart_source_data_value){
                        if ($chart_source_data_key == 0) {
                            if(!empty($chart_source_data_value)){
                                foreach($chart_source_data_value as $s_data_key => $s_data_value){
                                    $chart_source_filtered_data[$chart_source_data_key][] = (isset($s_data_value) && trim(esc_attr(stripslashes( sanitize_text_field( $s_data_value ) ))) != '') ? esc_attr(stripslashes( sanitize_text_field( $s_data_value ) )) : 'Title '.$s_data_key;
                                }
                            }
                        } else {
                            if(!empty($chart_source_data_value) && (isset($chart_source_data_value[0]) && trim($chart_source_data_value[0]) != '')){
                                foreach($chart_source_data_value as $s_data_key => $s_data_value){
                                    if ($s_data_key === 0) {
                                        $chart_source_filtered_data[$chart_source_data_key][] = (isset($s_data_value) && esc_attr(stripslashes( sanitize_text_field( $s_data_value ) )) != '') ? esc_attr(stripslashes( sanitize_text_field( $s_data_value ) )) : 'Option';
                                    } else {
                                        $chart_source_filtered_data[$chart_source_data_key][] = (isset($s_data_value) && esc_attr(stripslashes( sanitize_text_field( $s_data_value ) )) != '') ? esc_attr(stripslashes( sanitize_text_field( $s_data_value ) )) : '0';
                                    }
                                }
                            }
                        }
                    }
                }
                $chart_source_filtered_data = json_encode( $chart_source_filtered_data );

                // Source type import from quiz maker database
                $quiz_maker_data_option_name_for_quiz = $id == 0 ? 'ays_chart_quiz_maker_quiz_data_temp' : 'ays_chart_quiz_maker_quiz_data_' . $id;
                $quiz_data = get_option( $quiz_maker_data_option_name_for_quiz );

                $quiz_maker_data_option_name = $id == 0 ? 'ays_chart_quiz_maker_results_temp' : 'ays_chart_quiz_maker_results_' . $id;
                $quiz_maker_data = get_option( $quiz_maker_data_option_name, array() );
                $query_id = isset( $_POST[ $name_prefix . 'quiz_query' ] ) && $_POST[ $name_prefix . 'quiz_query' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'quiz_query' ] ) : '';
                $quiz_id = isset( $_POST[ $name_prefix . 'quiz_id' ] ) && $_POST[ $name_prefix . 'quiz_id' ] != '' ? intval( $_POST[ $name_prefix . 'quiz_id' ] ) : 0;

                // Source
                switch ( $source_type ){
                    case 'quiz_maker':
                        $query_id_op = sanitize_text_field($quiz_data['quiz_query']);
                        $quiz_id_op = intval($quiz_data['quiz_id']);

                        $chart_id = $id;
                        $user_id = get_current_user_id();
                        
                        if ( !($query_id_op == $query_id && $quiz_id_op == $quiz_id) ) {
                            if ( $chart_id >= 0 ) {
                                $return_results = CBFunctions()->get_quiz_query($query_id, $quiz_id, $user_id);
                                $result_values = $return_results['result'];
                                $query = $return_results['query'];
    
                                if ( !empty($result_values) ) {
                                    $results = array();
                                    $headers = array();
                                    if ( $result_values ) {
                                        $row_num = 0;
                                        foreach ( $result_values as $row ) {
                                            $result = array();
                                            $col_num = 0;
                                            foreach ( $row as $k => $v ) {
                                                $result[] = $v;
                                                if ( $row_num === 0 ) {
                                                    $headers[]  = $k;
                                                }
                                            }
                                            $results[] = $result;
                                            $row_num++;
                                        }
                                    }
                                }
                    
                                $source_type = 'quiz_maker';
                                $option_name_for_data = $chart_id == 0 ? 'ays_chart_quiz_maker_results_temp' : 'ays_chart_quiz_maker_results_' . $chart_id;
                                $option_name_for_quiz = $chart_id == 0 ? 'ays_chart_quiz_maker_quiz_data_temp' : 'ays_chart_quiz_maker_quiz_data_' . $chart_id;

                                $source = $query;

                                $quiz_maker_data = array(
                                    'source_type' => $source_type,
                                    'source' => $query,
                                    'data' => $results,
                                );
                                $quiz_data = array(
                                    'quiz_query' => $query_id,
                                    'quiz_id' => $quiz_id
                                );

                                update_option( $option_name_for_data, $quiz_maker_data);
                                update_option( $option_name_for_quiz, $quiz_data);
                            }
                        } else {
                            $source = isset( $quiz_maker_data['source'] ) && $quiz_maker_data['source'] !== '' ? $quiz_maker_data['source'] : '';

                            $query_id = $query_id_op;
                            $quiz_id = $quiz_id_op;
                        }
                        break;
                    case 'manual':
                    default:
                        $source = $chart_source_filtered_data;
                        break;
                }

                // Status
                $status = isset( $_POST[ $name_prefix . 'status' ] ) && $_POST[ $name_prefix . 'status' ] != '' ? sanitize_text_field( $_POST[ $name_prefix . 'status' ] ) : 'draft';

                // Date created
                $date_created = isset( $_POST[ $name_prefix . 'date_created' ] ) && CBFunctions()->validateDate( $_POST[ $name_prefix . 'date_created' ] ) ? sanitize_text_field($_POST[ $name_prefix . 'date_created' ]) : current_time( 'mysql' );

                // Date modified
                $date_modified = isset( $_POST[ $name_prefix . 'date_modified' ] ) && CBFunctions()->validateDate( $_POST[ $name_prefix . 'date_modified' ] ) ? sanitize_text_field($_POST[ $name_prefix . 'date_modified' ]) : current_time( 'mysql' );

                // Change the author of the current chart
                $create_author = ( isset($_POST[$name_prefix . 'create_author']) && $_POST[$name_prefix . 'create_author'] != "" ) ? absint( sanitize_text_field( $_POST[$name_prefix . 'create_author'] ) ) : '';

                if ( $create_author != "" && $create_author > 0 ) {
                    $user = get_userdata($create_author);
                    if ( ! is_null( $user ) && $user ) {
                        $author = array(
                            'id' => $user->ID."",
                            'name' => $user->data->display_name
                        );

                        $author = json_encode($author, JSON_UNESCAPED_SLASHES);
                    } else {
                        $author_data = json_decode($create_author, true);
                        $create_author = (isset( $author_data['id'] ) && $author_data['id'] != "") ? absint( sanitize_text_field( $author_data['id'] ) ) : get_current_user_id();
                    }
                    
                    $author_id = $create_author;
                }

                // Options
                $options = array();
                if( isset( $_POST[ $name_prefix . 'options' ] ) && !empty( $_POST[ $name_prefix . 'options' ] )) {
                    foreach($_POST[ $name_prefix . 'options' ] as $each_option_key => $each_option_value){
                        $each_option_value = isset($each_option_value) && $each_option_value != '' ? sanitize_text_field($each_option_value) : '';
                        $each_option_key   = isset($each_option_key) && $each_option_key != '' ? sanitize_text_field($each_option_key) : '';
                        $options[$each_option_key] = sanitize_text_field($each_option_value);
                    }
                }

                $options = apply_filters( 'ays_chart_item_save_options', $options );

                // Settings
                // == Sanitize in the loop all values except checkboxes, radios (only for settings array). The latter sanitize separately (out of loop) ==
                $settings = array();
                if( isset( $_POST[ $name_prefix . 'settings' ] ) && !empty( $_POST[ $name_prefix . 'settings' ] )) {
                    foreach($_POST[ $name_prefix . 'settings' ] as $each_setting_key => $each_setting_value){
                        if (!is_array($each_setting_value)) {
                            $each_setting_value = isset($each_setting_value) && $each_setting_value != '' ? esc_attr(stripslashes(sanitize_text_field($each_setting_value))) : '';
                            $each_setting_key   = isset($each_setting_key) && $each_setting_key != '' ? esc_attr(stripslashes(sanitize_text_field($each_setting_key))) : '';
                            $settings[$each_setting_key] = $each_setting_value;
                        } else {
                            foreach($each_setting_value as $each_index => $each_value){
                                $each_value = isset($each_value) && $each_value != '' ? esc_attr(stripslashes(sanitize_text_field($each_value))) : '';
                                $each_index   = isset($each_index) && $each_index >= 0 ? intval($each_index) : -1;
                                $settings[$each_setting_key][$each_index] = $each_value;
                            }
                            $settings[$each_setting_key] = json_encode($settings[$each_setting_key]);
                        }
                    }
                }

                // == Sanitize checkboxes, radios here (only for settings array) ==
                $settings['responsive_width'] = ( isset( $settings['responsive_width'] ) && $settings['responsive_width'] != '' ) ? sanitize_text_field($settings['responsive_width']) : 'off';
                $settings['transparent_background'] = ( isset( $settings['transparent_background'] ) && $settings['transparent_background'] != '' ) ? sanitize_text_field($settings['transparent_background']) : 'off';
                $settings['show_color_code'] = ( isset( $settings['show_color_code'] ) && $settings['show_color_code'] != '' ) ? sanitize_text_field($settings['show_color_code']) : 'off';
                $settings['tooltip_italic'] = ( isset( $settings['tooltip_italic'] ) && $settings['tooltip_italic'] != '' ) ? sanitize_text_field($settings['tooltip_italic']) : 'off';
                $settings['legend_italic'] = ( isset( $settings['legend_italic'] ) && $settings['legend_italic'] != '' ) ? sanitize_text_field($settings['legend_italic']) : 'off';
                $settings['legend_bold'] = ( isset( $settings['legend_bold'] ) && $settings['legend_bold'] != '' ) ? sanitize_text_field($settings['legend_bold']) : 'off';
                $settings['haxis_italic'] = ( isset( $settings['haxis_italic'] ) && $settings['haxis_italic'] != '' ) ? sanitize_text_field($settings['haxis_italic']) : 'off';
                $settings['haxis_bold'] = ( isset( $settings['haxis_bold'] ) && $settings['haxis_bold'] != '' ) ? sanitize_text_field($settings['haxis_bold']) : 'off';
                $settings['vaxis_italic'] = ( isset( $settings['vaxis_italic'] ) && $settings['vaxis_italic'] != '' ) ? sanitize_text_field($settings['vaxis_italic']) : 'off';
                $settings['vaxis_bold'] = ( isset( $settings['vaxis_bold'] ) && $settings['vaxis_bold'] != '' ) ? sanitize_text_field($settings['vaxis_bold']) : 'off';
                $settings['reverse_categories'] = ( isset( $settings['reverse_categories'] ) && $settings['reverse_categories'] != '' ) ? sanitize_text_field($settings['reverse_categories']) : 'off';
                $settings['haxis_title_italic'] = ( isset( $settings['haxis_title_italic'] ) && $settings['haxis_title_italic'] != '' ) ? sanitize_text_field($settings['haxis_title_italic']) : 'off';
                $settings['haxis_title_bold'] = ( isset( $settings['haxis_title_bold'] ) && $settings['haxis_title_bold'] != '' ) ? sanitize_text_field($settings['haxis_title_bold']) : 'off';
                $settings['vaxis_title_italic'] = ( isset( $settings['vaxis_title_italic'] ) && $settings['vaxis_title_italic'] != '' ) ? sanitize_text_field($settings['vaxis_title_italic']) : 'off';
                $settings['vaxis_title_bold'] = ( isset( $settings['vaxis_title_bold'] ) && $settings['vaxis_title_bold'] != '' ) ? sanitize_text_field($settings['vaxis_title_bold']) : 'off';
                $settings['enable_row_settings'] = ( isset( $settings['enable_row_settings'] ) && $settings['enable_row_settings'] != '' ) ? sanitize_text_field($settings['enable_row_settings']) : 'off';
                $settings['is_stacked'] = ( isset( $settings['is_stacked'] ) && $settings['is_stacked'] != '' ) ? sanitize_text_field($settings['is_stacked']) : 'off';
                $settings['multiple_selection'] = ( isset( $settings['multiple_selection'] ) && $settings['multiple_selection'] != '' ) ? sanitize_text_field($settings['multiple_selection']) : 'off';
                $settings['show_title'] = ( isset( $settings['show_title'] ) && $settings['show_title'] != '' ) ? sanitize_text_field($settings['show_title']) : 'off';
                $settings['title_bold'] = ( isset( $settings['title_bold'] ) && $settings['title_bold'] != '' ) ? sanitize_text_field($settings['title_bold']) : 'off';
                $settings['title_text_shadow'] = ( isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] != '' ) ? sanitize_text_field($settings['title_text_shadow']) : 'off';
                $settings['title_italic'] = ( isset( $settings['title_italic'] ) && $settings['title_italic'] != '' ) ? sanitize_text_field($settings['title_italic']) : 'off';
                $settings['show_description'] = ( isset( $settings['show_description'] ) && $settings['show_description'] != '' ) ? sanitize_text_field($settings['show_description']) : 'off';
                $settings['description_bold'] = ( isset( $settings['description_bold'] ) && $settings['description_bold'] != '' ) ? sanitize_text_field($settings['description_bold']) : 'off';
                $settings['description_italic'] = ( isset( $settings['description_italic'] ) && $settings['description_italic'] != '' ) ? sanitize_text_field($settings['description_italic']) : 'off';
                $settings['description_text_shadow'] = ( isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] != '' ) ? sanitize_text_field($settings['description_text_shadow']) : 'off';
                $settings['enable_interactivity'] = ( isset( $settings['enable_interactivity'] ) && $settings['enable_interactivity'] != '' ) ? sanitize_text_field($settings['enable_interactivity']) : 'off';
                $settings['maximized_view'] = ( isset( $settings['maximized_view'] ) && $settings['maximized_view'] != '' ) ? sanitize_text_field($settings['maximized_view']) : 'off';
                $settings['haxis_direction'] = ( isset( $settings['haxis_direction'] ) && $settings['haxis_direction'] != '' ) ? sanitize_text_field($settings['haxis_direction']) : '1';
                $settings['vaxis_direction'] = ( isset( $settings['vaxis_direction'] ) && $settings['vaxis_direction'] != '' ) ? sanitize_text_field($settings['vaxis_direction']) : '1';
                $settings['haxis_slanted_text_angle'] = ( isset( $settings['haxis_slanted_text_angle'] ) && $settings['haxis_slanted_text_angle'] != '0' ) ? sanitize_text_field($settings['haxis_slanted_text_angle']) : '30';
                $settings['enable_animation'] = ( isset( $settings['enable_animation'] ) && $settings['enable_animation'] != '' ) ? sanitize_text_field($settings['enable_animation']) : 'off';
                $settings['animation_startup'] = ( isset( $settings['animation_startup'] ) && $settings['animation_startup'] != '' ) ? sanitize_text_field($settings['animation_startup']) : 'off';
                $settings['orientation'] = ( isset( $settings['orientation'] ) && $settings['orientation'] != '' ) ? sanitize_text_field($settings['orientation']) : 'off';
                $settings['fill_nulls'] = ( isset( $settings['fill_nulls'] ) && $settings['fill_nulls'] != '' ) ? sanitize_text_field($settings['fill_nulls']) : 'off';
                $settings['allow_collapse'] = ( isset( $settings['allow_collapse'] ) && $settings['allow_collapse'] != '' ) ? sanitize_text_field($settings['allow_collapse']) : 'off';
                $settings['enable_img'] = ( isset( $settings['enable_img'] ) && $settings['enable_img'] != '' ) ? sanitize_text_field($settings['enable_img']) : 'off';
                // $settings['index_axis'] = ( isset( $settings['index_axis'] ) && $settings['index_axis'] != '' ) ? sanitize_text_field($settings['index_axis']) : 'off';

                $settings = apply_filters( 'ays_chart_item_save_settings', $settings );

                $message = '';
                if( $id == 0 ){
                    $result = $wpdb->insert(
                        $this->db_table,
                        array(
	                        'author_id'         => $author_id,
                            'title'             => $title,
                            'description'       => $description,
                            'type'              => $type,
                            'source_chart_type' => $source_chart_type,
                            'source_type'       => $source_type,
                            'source'            => $source,
                            'status'            => $status,
                            'date_created'      => $date_created,
                            'date_modified'     => $date_modified,
                            'quiz_query'        => $query_id,
                            'quiz_id'           => $quiz_id,
                            'options'           => json_encode( $options ),
                        ),
                        array(
	                        '%s', // author_id
                            '%s', // title
                            '%s', // description
                            '%s', // type
                            '%s', // source_chart_type
                            '%s', // source_type
                            '%s', // source
                            '%s', // status
                            '%s', // date_created
                            '%s', // date_modified
                            '%s', // query_id
                            '%d', // quiz_id
                            '%s', // options
                        )
                    );

                    $inserted_id = $wpdb->insert_id;

                    if( is_array( $settings ) && ! empty( $settings ) ){
                        foreach ( $settings as $key => $setting ){
                            $this->add_meta( $inserted_id, $key, $setting );
                        }
                    }

                    update_option( 'ays_chart_quiz_maker_results_' . $inserted_id, $quiz_maker_data );
                    delete_option( $quiz_maker_data_option_name );

                    update_option( 'ays_chart_quiz_maker_quiz_data_' . $inserted_id, $quiz_data );
                    delete_option( $quiz_maker_data_option_name_for_quiz );

                    $message = 'created';
                }else{
                    $result = $wpdb->update(
                        $this->db_table,
                        array(
	                        'author_id'         => $author_id,
                            'title'             => $title,
                            'description'       => $description,
                            'type'              => $type,
                            'source_chart_type' => $source_chart_type,
                            'source_type'       => $source_type,
                            'source'            => $source,
                            'status'            => $status,
                            'date_modified'     => $date_modified,
                            'quiz_query'        => $query_id,
                            'quiz_id'           => $quiz_id,
                            'options'           => json_encode( $options ),
                        ),
                        array( 'id' => $id ),
                        array(
	                        '%s', // author_id
                            '%s', // title
                            '%s', // description
                            '%s', // type
                            '%s', // source_chart_type
                            '%s', // source_type
                            '%s', // source
                            '%s', // status
                            '%s', // date_modified
                            '%s', // quiz_query
                            '%d', // quiz_id
                            '%s', // options
                        ),
                        array( '%d' )
                    );

                    $inserted_id = $id;

                    if( is_array( $settings ) && ! empty( $settings ) ){
                        foreach ( $settings as $key => $setting ){
                            if( $this->get_meta( $inserted_id, $key, 'id' ) ) {
                                $this->update_meta( $inserted_id, $key, $setting );
                            }else{
                                $this->add_meta( $inserted_id, $key, $setting );
                            }
                        }
                    }

                    $message = 'updated';
                }

                $ays_chart_tab = isset($_POST[ $name_prefix . 'chart_tab' ]) ? $_POST[ $name_prefix . 'chart_tab' ] : 'tab1';

                if($message == 'created'){
                    setcookie('ays_chart_created_new', $inserted_id, time() + 3600, '/');
                }

                if( $result >= 0  ) {
                    if( $save_type == 'apply' ){
                        $url = remove_query_arg( array('type', 'source') );
                        if($id == 0){
                            $url = esc_url_raw( add_query_arg( array(
                                "action"    => "edit",
                                "id"        => $inserted_id,
                                "ays_chart_tab"  => $ays_chart_tab,
                                "status"    => $message
                            ), $url ) );
                        }else{
                            $url = esc_url_raw( add_query_arg( array(
                                "ays_chart_tab"  => $ays_chart_tab,
                                "status" => $message
                            ), $url ) );
                        }
                        wp_redirect( $url );
                        exit;
                    }elseif( $save_type == 'save_new' ){
                        $url = remove_query_arg( array('id', 'type', 'source') );
                        $url = esc_url_raw( add_query_arg( array(
                            "action" => "add",
                            "ays_chart_tab"  => $ays_chart_tab,
                            "status" => $message
                        ), $url ) );
                        wp_redirect( $url );
                        exit;
                    }else{
                        $url = remove_query_arg( array('action', 'id', 'type', 'source') );
                        $url = esc_url_raw( add_query_arg( array(
                            "ays_chart_tab"  => $ays_chart_tab,
                            "status" => $message
                        ), $url ) );
                        wp_redirect( $url );
                        exit;
                    }
                }

            }else{
                return false;
            }

        }

	    /**
	     * Delete record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function delete_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            $wpdb->delete(
                $this->db_table_meta,
                array( 'chart_id' => absint( $id ) ),
                array( '%d' )
            );

            $wpdb->delete(
                $this->db_table,
                array( 'id' => absint( $id ) ),
                array( '%d' )
            );

	        return true;
        }

	    /**
	     * Move to trash record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function trash_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

	        $result = $wpdb->update(
		        $this->db_table,
		        array( 'status' => 'trashed' ),
		        array( 'id' => absint( $id ) ),
		        array( '%s' ),
		        array( '%d' )
	        );

            if( $result >= 0 ){
                return true;
            }

	        return false;
        }

        /**
	     * Move to publish record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function publish_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

	        $result = $wpdb->update(
		        $this->db_table,
		        array( 'status' => 'published' ),
		        array( 'id' => absint( $id ) ),
		        array( '%s' ),
		        array( '%d' )
	        );

            if( $result >= 0 ){
                return true;
            }

	        return false;
        }

	    /**
	     * Restore record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function restore_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

	        $result = $wpdb->update(
		        $this->db_table,
		        array( 'status' => 'draft' ),
		        array( 'id' => absint( $id ) ),
		        array( '%s' ),
		        array( '%d' )
	        );

            if( $result >= 0 ){
                return true;
            }

	        return false;
        }

        /**
	     * Duplicate record by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      bool
	     */
        public function duplicate_item( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            $current_data = $this->get_item($id);
            array_shift($current_data);
            $current_data['title'] .= __(' (Copy)', 'chart-builder');
            $current_data['date_created'] = current_time( 'mysql' );
            $current_data['date_modified'] = current_time( 'mysql' );
            $result = $wpdb->insert(
                $this->db_table,
                $current_data,
                array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s')
            );
            $new_id = $wpdb->insert_id;
            if (!$result || $new_id <= 0) {
                return false;
            }

            $quiz_option_result = get_option('ays_chart_quiz_maker_results_'.$id);
            $quiz_option_data = get_option('ays_chart_quiz_maker_quiz_data_'.$id);
            if ($quiz_option_result) {
                update_option( 'ays_chart_quiz_maker_results_' . $new_id, $quiz_option_result );
            }
            if ($quiz_option_data) {
                update_option( 'ays_chart_quiz_maker_quiz_data_' . $new_id, $quiz_option_data );
            }            

            $current_metadata = $this->get_metadata($id);
            foreach ($current_metadata as $meta_id => &$meta_row) {
                $meta_row['chart_id'] = $new_id;
                array_shift($meta_row);
                $result = $wpdb->insert(
                    $this->db_table_meta,
                    $meta_row,
                    array( '%s', '%s', '%s', '%s' )
                );
                if ($result < 0){
                    return false;
                }
            }

	        return true;
        }

	    /**
	     * Get record metadata by id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
         *
	     * @return      array
	     */
        public function get_metadata( $id ){
            global $wpdb;

            if( is_null( $id ) ){
                return array();
            }

            $sql = "SELECT * FROM " . $this->db_table_meta . " WHERE chart_id = " . $id;

            $results = $wpdb->get_results($sql, ARRAY_A);

            if( count( $results ) > 0 ){
                return $results;
            }else{
                return array();
            }
        }

	    /**
	     * Convert record metadata to useful format
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $settings
         *
	     * @return      array
	     */
        public function convert_metadata( $settings ){

            if( ! is_array( $settings ) || empty( $settings ) ){
                return array();
            }

            $data = array();
            foreach ( $settings as $k => $setting ) {
                $data[ $setting['meta_key'] ] = $setting['meta_value'];
            }

            return $data;
        }

	    /**
	     * Add default values to record metadata of the chart
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $settings
         *
	     * @return      array
	     */
        public function apply_default_metadata( $settings ){

            if( ! is_array( $settings ) || empty( $settings ) ){
                return array();
            }

            $defaults = array(
                'width' => '',
                'height' => '',
                'font_size' => '',
                'title_color' => '',
            );

            foreach ( $defaults as $key => $default ) {
                if( ! isset( $settings[ $key ] ) ) {
                    $settings[ $key ] = $default;
                }
            }

            return $settings;
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
        public function get_meta( $id, $meta_key, $select_value = "meta_value" ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $sql = "SELECT ".$select_value." FROM ". $this->db_table_meta ." WHERE meta_key = '".$meta_key."' AND chart_id = '".$id."'";
            $result = $wpdb->get_var($sql);

            if( $result != "" ){
                return $result;
            }

            return false;
        }

	    /**
	     * Insert record meta by record id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key         @accept string
	     * @param       $meta_value       @accept JSON|serialized array|string|number
	     * @param       string $note      @accept string|number
	     * @param       string $options   @accept JSON
	     *
	     * @return      bool
	     */
        public function add_meta( $id, $meta_key, $meta_value, $note = "", $options = "" ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $result = $wpdb->insert(
                $this->db_table_meta,
                array(
                    'chart_id'    => absint( $id ),
                    'meta_key'    => $meta_key,
                    'meta_value'  => $meta_value,
                    'note'        => $note,
                    'options'     => $options
                ),
                array( '%s', '%s', '%s', '%s' )
            );

            if($result >= 0){
                return true;
            }

            return false;
        }

	    /**
	     * Update record meta by record id
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key         @accept string
	     * @param       $meta_value       @accept JSON|serialized array|string|number
	     * @param       string $note      @accept string|number
	     * @param       string $options   @accept JSON
	     *
	     * @return      bool
	     */
        public function update_meta( $id, $meta_key, $meta_value, $note = "", $options = "" ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

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
                $this->db_table_meta,
                $value,
                array(
                    'chart_id' => absint( $id ),
                    'meta_key' => $meta_key,
                ),
                $value_s,
                array( '%d', '%s' )
            );

            if($result >= 0){
                return true;
            }

            return false;
        }

	    /**
	     * Delete record meta by record id and meta key
         *
	     * @since       1.0.0
	     * @access      public
         *
	     * @param       $id
	     * @param       $meta_key
	     *
	     * @return      bool
	     */
        public function delete_meta( $id, $meta_key ){
            global $wpdb;

            if( is_null( $id ) ){
                return false;
            }

            if( is_null( $meta_key ) || trim( $meta_key ) === '' ){
                return false;
            }

            $wpdb->delete(
                $this->db_table_meta,
                array(
                    'chart_id' => absint( $id ),
                    'meta_key' => $meta_key,
                ),
                array( '%d', '%s' )
            );

            return true;
        }

        public static function ays_chart_is_elementor(){
            if( isset( $_GET['action'] ) && $_GET['action'] == 'elementor' ){
                $is_elementor = true;
            }elseif( isset( $_REQUEST['elementor-preview'] ) && $_REQUEST['elementor-preview'] != '' ){
                $is_elementor = true;
            }else{
                $is_elementor = false;
            }
    
            if ( ! $is_elementor ) {
                $is_elementor = ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor_ajax' ) ? true : false;
            }
    
            return $is_elementor;
        }

	    /**
	     * Display notice based on action that happened to record
	     *
	     * @since       1.0.0
         * @access      public
         *
         * @return      void|html
	     */
        public function chart_notices(){
            $page = (isset($_REQUEST['page'])) ? sanitize_text_field( $_REQUEST['page'] ) : '';
            if ( !($page == "chart-builder") )
                return;

            $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

            if ( empty( $status ) )
                return;

            $error = false;
            switch ( $status ) {
                case 'created':
                    $updated_message =  __( 'Chart created.', "chart-builder" ) ;
                    break;
                case 'updated':
                    $updated_message =  __( 'Chart saved.', "chart-builder" ) ;
                    break;
                case 'duplicated':
                    $updated_message =  __( 'Chart duplicated.', "chart-builder" ) ;
                    break;
                case 'deleted':
                    $updated_message =  __( 'Chart deleted.', "chart-builder" ) ;
                    break;
                case 'trashed':
                    $updated_message =  __( 'Chart moved to trash.', "chart-builder" ) ;
                    break;
                case 'restored':
                    $updated_message =  __( 'Chart restored.', "chart-builder" ) ;
                    break;
                case 'all-duplicated':
                    $updated_message =  __( 'Charts are duplicated.', "chart-builder" ) ;
                    break;
                case 'all-deleted':
                    $updated_message =  __( 'Charts are deleted.', "chart-builder" ) ;
                    break;
                case 'all-trashed':
                    $updated_message =  __( 'Charts are moved to trash.', "chart-builder" );
                    break;
                case 'all-restored':
                    $updated_message =  __( 'Charts are restored.', "chart-builder" );
                    break;
                case 'empty-title':
                    $error = true;
                    $updated_message =  __( 'Error: Chart title can not be empty.', "chart-builder" ) ;
                    break;
                default:
                    break;
            }

            if ( empty( $updated_message ) )
                return;

            $notice_class = 'success';
            if( $error ){
                $notice_class = 'error';
            }
            ?>
            <div class="notice notice-<?php echo esc_attr( $notice_class ); ?> is-dismissible">
                <p> <?php echo esc_html($updated_message); ?> </p>
            </div>
            <?php
        }

    }
}