<?php

if( !class_exists( 'Chart_Builder_Functions' ) ){

	/**
	 * Class Chart_Builder_Functions
	 * Class contains useful functions that uses in common
	 *
	 *
	 * Hooks used in the class
	 * There are not hooks yet
	 * @hooks           @actions
	 *                  @filters
	 *
	 *
	 * @param           $plugin_name
	 *
	 * @since           1.0.0
	 * @package         Chart_Builder
	 * @subpackage      Chart_Builder/includes
	 * @author          Chart Builder Team <info@ays-pro.com>
	 */
    class Chart_Builder_Functions {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

		/**
         * The database table name
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $db_table    The database table name
         */
        private $db_table;

	    /**
	     * The constructor of the class
	     *
	     * @since   1.0.0
	     * @param   $plugin_name
	     */
        public function __construct( $plugin_name ) {
			global $wpdb;
	        /**
	         * Assigning $plugin_name to the @plugin_name property
	         */
            $this->plugin_name = $plugin_name;
			$this->db_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts";
        }

	    /**
	     * Get instance of this class
	     *
	     * @access  public
	     * @since   1.0.0
	     * @param   $plugin_name
	     * @return  Chart_Builder_Functions
	     */
	    public static function get_instance( $plugin_name ){
		    return new self( $plugin_name );
	    }

	    /**
         * Date validation function
         *
         * @accept  two parameters
	     * @param   $date
	     * @param   string $format
	     *
	     * @return  bool
	     */
        public function validateDate( $date, $format = 'Y-m-d H:i:s' ){
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

	    /**
	     * Version compare function
	     *
	     * @accept  two parameters
	     * @param   $version1
	     * @param   $operator
	     * @param   $version2
	     *
	     * @return  bool|int
	     */
        public function versionCompare( $version1, $operator, $version2 ) {

            $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
            $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );

            if (strlen ( $_fv ) > strlen ( $_sv )) {
                $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
            }

            if (strlen ( $_fv ) < strlen ( $_sv )) {
                $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
            }

            return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
        }

        public function get_all_charts_count() {
			global $wpdb;
			$where = str_replace("WHERE", "AND", Chart_Builder_DB_Actions::get_where_condition());
            $sql = "SELECT COUNT(id) FROM " . $this->db_table . " WHERE `status`='published' " . $where;
			return intval( $wpdb->get_var( $sql ) );
        }

	    /**
	     * Gets the allowed types
	     *
	     * @access private
	     */
	    public function getAllowedTypes() {
		    return array(
			    'string',
			    'number',
			    'boolean',
			    'date',
			    'datetime',
			    'timeofday',
		    );
	    }

	    /**
	     * Gets the properties of the post type
	     *
	     * @access private
	     */
	    public function get_post_type_properties( $post_type ) {
		    $array = null;

		    $query = new WP_Query(
			    array(
				    'post_type'              => $post_type,
				    'no_rows_found'          => false,
				    'post_per_page'          => 1,
				    'orderby'                => 'post_date',
				    'order'                  => 'DESC',
				    'fields'                 => 'ids',
				    'update_post_meta_cache' => false,
				    'update_post_term_cache' => false,
			    )
		    );

		    $array = array(
			    'post_title',
			    'post_status',
			    'comment_count',
			    'post_date',
			    'post_modified',
		    );

		    if ( $query->have_posts() ) {
			    $id   = $query->posts[0];
			    $meta = get_post_meta( $id, '', true );
			    foreach ( $meta as $key => $values ) {
				    $array[] = $key;
			    }
		    }

		    return $array;
	    }

	    /**
	     * Gets all tables and their columns.
	     *
	     * @access public
	     * @return array
	     */
	    public function get_all_db_tables_column_mapping( $chart_id, $use_filter = true ) {
		    $mapping    = array();
		    $tables     = $this->get_db_tables();
		    foreach ( $tables as $table ) {
			    $cols   = $this->get_db_table_columns( $table, true );
			    $names  = wp_list_pluck( $cols, 'name' );
			    $mapping[ $table ] = $names;
		    }

		    return $mapping;
	    }

	    /**
	     * Gets the tables in the database;
	     *
	     * @access public
	     * @return array
	     */
	    public function get_db_tables() {
		    global $wpdb;
		    $tables = get_transient( CHART_BUILDER_DB_PREFIX . 'db_tables' );
		    if ( $tables ) {
			    return $tables;
		    }
			$tables = array();
			
		    $sql    = $wpdb->get_col( 'SHOW TABLES', 0 );
		    foreach ( $sql as $table ) {
			    if ( empty( $prefix ) || 0 === strpos( $table, $wpdb->prefix ) ) {
				    $tables[] = $table;
			    }
		    }

		    set_transient( CHART_BUILDER_DB_PREFIX . 'db_tables', $tables, HOUR_IN_SECONDS );
		    return $tables;
	    }

	    /**
	     * Gets the column information for the table.
	     *
	     * @param string $table The table.
	     * @param bool   $prefix_with_table Whether to prefix column name with the name of the table.
	     * @access private
	     * @return array
	     */
	    private function get_db_table_columns( $table, $prefix_with_table = false ) {
		    global $wpdb;
		    $columns    = get_transient( CHART_BUILDER_DB_PREFIX . "db_{$table}_columns" );
		    if ( $columns ) {
			    return $columns;
		    }
		    $columns    = array();
		    // @codingStandardsIgnoreStart
		    $rows       = $wpdb->get_results( "SHOW COLUMNS IN `$table`", ARRAY_N );
		    // @codingStandardsIgnoreEnd
		    if ( $rows ) {
			    // n => numeric, d => date-ish, s => string-ish.
			    foreach ( $rows as $row ) {
				    $col        = ( $prefix_with_table ? "$table." : '' ) . $row[0];
				    $type       = $row[1];
				    if ( strpos( $type, 'int' ) !== false || strpos( $type, 'float' ) !== false ) {
					    $type   = 'n';
				    } elseif ( strpos( $type, 'date' ) !== false || strpos( $type, 'time' ) !== false ) {
					    $type   = 'd';
				    } else {
					    $type   = 's';
				    }
				    $columns[]  = array( 'name' => $col, 'type' => $type );
			    }
		    }

		    set_transient( CHART_BUILDER_DB_PREFIX . "db_{$table}_columns", $columns, DAY_IN_SECONDS );
		    return $columns;
	    }

	    /**
	     * Gets the dependent tables and then gets column information for all the tables.
	     *
	     * @param string $table The table.
	     * @access public
	     * @return array
	     */
	    public function get_table_columns( $table ) {
		    $columns    = array();
		    if ( ! $table ) {
			    return $columns;
		    }

		    $tables = array( $table );
		    $mapping = $this->get_db_table_mapping();
		    if ( array_key_exists( $table, $mapping ) ) {
			    $tables[] = $mapping[ $table ];
		    }
		    foreach ( $tables as $table ) {
			    $cols = $this->get_db_table_columns( $table, count( $tables ) > 1 );
			    $columns = array_merge( $columns, $cols );
		    }
		    return $columns;
	    }

	    /**
	     * Gets the relationship between tables in the database.
	     *
	     * @access public
	     * @return array
	     */
	    public function get_db_table_mapping() {
		    global $wpdb;
		    $mapping = get_transient( CHART_BUILDER_DB_PREFIX . 'db_table_mapping' );
		    if ( $mapping ) {
			    return $mapping;
		    }
		    // no need to provide x=>y and then y=>x as we will flip the array shortly.
		    $mapping = array(
			    $wpdb->prefix . 'posts' => $wpdb->prefix . 'postmeta',
			    $wpdb->prefix . 'users' => $wpdb->prefix . 'usermeta',
			    $wpdb->prefix . 'terms' => $wpdb->prefix . 'termmeta',
			    $wpdb->prefix . 'comments' => $wpdb->prefix . 'commentmeta',
		    );

		    $mapping += array_flip( $mapping );
		    set_transient( CHART_BUILDER_DB_PREFIX . 'db_table_mapping', $mapping, HOUR_IN_SECONDS );
		    return $mapping;
	    }

	    /**
	     * Creates HTML table from passed data
	     *
	     * @access public
	     * @return string
	     */
	    public function get_table_html( $headers, $rows, $table_id = 'results' ) {
		    ob_start();
		    ?>
		    <table cellspacing="0" width="100%" id="<?= $table_id ?>">
			    <thead>
			    <tr>
				    <?php
				    foreach ( $headers as $header ) {
                        if( empty( $header ) ){
                            continue;
                        }
					    echo '<th>' . $header . '</th>';
				    }
				    ?>
			    </tr>
			    </thead>
			    <tfoot>
			    </tfoot>
			    <tbody>
			    <?php
			    foreach ( $rows as $row ) {
				    if( empty( $row ) ){
					    continue;
				    }
				    echo '<tr>';
				    foreach ( $row as $r ) {
					    echo '<td>' . $r . '</td>';
				    }
				    echo '</tr>';
			    }
			    ?>
			    </tbody>
		    </table>
		    <?php
		    return ob_get_clean();
	    }

		/**
	     * Gets the the queries from quiz maker database tables.
	     *
	     * @access public
	     * @return array
	    */
		public function get_quiz_query ($query, $quiz_id = null, $user_id = null) {
			global $wpdb;
			$reports_table = $wpdb->prefix . 'aysquiz_reports';
			$quizes_table = $wpdb->prefix . 'aysquiz_quizes';

			switch ($query) {
				case 'q1':
					$sql = "SELECT CAST(end_date AS date) AS `Date`, COUNT(id) AS `Count` FROM ".$reports_table." WHERE quiz_id = ".$quiz_id." GROUP BY CAST(end_date AS date)";
					break;
				case 'q2':
					$sql = "SELECT CAST(end_date AS date) AS `Date`, COUNT(id) AS `Count` FROM ".$reports_table." WHERE user_id = ".$user_id." GROUP BY CAST(end_date AS date)";
					break;
				case 'q3':
					$sql = "SELECT CAST(end_date AS date) AS `Date`, COUNT(id) AS `Count` FROM ".$reports_table." WHERE user_id = ".$user_id." AND quiz_id = ".$quiz_id." GROUP BY CAST(end_date AS date)";
					break;
				case 'q4':
					$sql = "SELECT ".$quizes_table.".title AS `Quiz`, AVG(".$reports_table.".score) AS `Average` FROM ".$reports_table." LEFT JOIN ".$quizes_table." ON ".$reports_table.".quiz_id = ".$quizes_table.".id WHERE ".$reports_table.".user_id = ".$user_id." GROUP BY ".$quizes_table.".title";
					break;
				case 'q5':
					$sql = "SELECT ".$quizes_table.".title AS `Quiz`, COUNT(".$reports_table.".score) AS `Count` FROM ".$reports_table." LEFT JOIN ".$quizes_table." ON ".$reports_table.".quiz_id = ".$quizes_table.".id WHERE ".$reports_table.".user_id = ".$user_id." GROUP BY ".$quizes_table.".title";
					break;
				case 'q6':
					$sql = "SELECT score AS `Score` FROM ".$reports_table." WHERE user_id = ".$user_id." AND quiz_id = ".$quiz_id."";
					break;
				default:
					break;
			}

			$result = $wpdb->get_results($sql, "ARRAY_A");
			$message = empty($result) ? __( "There is no data for your query.", "chart-builder" ) : "";
			return array(
				'message' => $message,
				'result' => $result,
				'query' => $sql
			);
		}

		/**
	     * Gets all settings of google charts for admin area.
	     *
	     * @access public
	     * @return array
	    */
		public function get_chart_settings_google_admin ($settings, $action, $source) {
			$chart_default_colors = array('#3366cc','#dc3912','#ff9900','#109618', '#990099','#0099c6','#dd4477','#66aa00', '#b82e2e','#316395','#994499','#22aa99', '#aaaa11','#6633cc','#e67300','#8b0707', '#651067','#329262','#5574a6','#3b3eac', '#b77322','#16d620','#b91383','#f4359e', '#9c5935','#a9c413','#2a778d','#668d1c', '#bea413','#0c5922','#743411');

			$tooltip_trigger_options = array(
				"hover" => __("While hovering", "chart-builder"),
				"selection" => __("When selected", "chart-builder"),
				"none" => __("Disable", "chart-builder")
			);
			
			$tooltip_bold_options = array(
				"default" => __("Default", "chart-builder"),
				"true" => __("Enable", "chart-builder"),
				"false" => __("Disable", "chart-builder")
			);
			
			$tooltip_text_options = array(
				"value" => __("Value", "chart-builder"),
				"percentage" => __("Percent", "chart-builder"),
				"both" => __("Value & Percent", "chart-builder")
			);
		
			$focus_target_options = array(
				"datum" => __("Single data", "chart-builder"),
				"category" => __("Group data", "chart-builder"),
			);
		
			$legend_positions = array(
				"left" => __("Left of the chart", "chart-builder"),
				"right" => __("Right of the chart", "chart-builder"),
				"top" => __("Above the chart", "chart-builder"),
				"bottom" => __("Below the chart", "chart-builder"),
				"in" => __("Inside the chart", "chart-builder"),
				"labeled" => __("Labeled", "chart-builder"),
				"none" => __("Hide", "chart-builder")
			);
		
			$legend_alignments = array(
				"start" => __("Start", "chart-builder"),
				"center" => __("Center", "chart-builder"),
				"end" => __("End", "chart-builder"),
			);
		
			$slice_texts = array(
				"percentage" => __("Percentage", "chart-builder"),
				"value" => __("Quantitative value", "chart-builder"),
				"label" => __("Name", "chart-builder"),
				"none" => __("Disable", "chart-builder")
			);
			
			$axes_text_positions = array(
				"in" => __("Inside the chart", "chart-builder"),
				"out" => __("Outside the chart", "chart-builder"),
				"none" => __("Hide", "chart-builder")
			);
		
			$haxis_slanted_options = array(
				"automatic" => __("Automatic", "chart-builder"),
				"true" => __("True", "chart-builder"),
				"false" => __("False", "chart-builder")
			);
		
			$title_positions = array(
				"left" => __("Left", "chart-builder"),
				"right" => __("Right", "chart-builder"),
				"center" => __("Center", "chart-builder")
			);
			
			$animation_easing_options = array(
				"linear" => __("Linear", "chart-builder"),
				"in" => __("Ease in", "chart-builder"),
				"out" => __("Ease out", "chart-builder"),
				"inAndOut" => __("Ease in and out", "chart-builder")
			);
		
			$multiple_data_format_options = array(
				"category" => __("Category", "chart-builder"),
				"series" => __("Series", "chart-builder"),
				"auto" => __("Auto", "chart-builder"),
				"none" => __("None", "chart-builder"),
			);
		
			$point_shape_options = array(
				"circle" => __("Circle", "chart-builder"),
				"triangle" => __("Triangle", "chart-builder"),
				"square" => __("Square", "chart-builder"),
				"diamond" => __("Diamond", "chart-builder"),
				"star" => __("Star", "chart-builder"),
				"polygon" => __("Polygon", "chart-builder"),
			);
		
			$crosshair_trigger_options = array(
				"focus" => __("Focus", "chart-builder"),
				"selection" => __("Selection", "chart-builder"),
				"both" => __("Focus and Selection", "chart-builder"),
				"none" => __("Disable", "chart-builder"),
			);
			
			$crosshair_orientation_options = array(
				"vertical" => __("Vertical", "chart-builder"),
				"horizontal" => __("Horizontal", "chart-builder"),
				"both" => __("Both", "chart-builder"),
			);
		
			$axes_format_options = array(
				"" => __("None", "chart-builder"),
				"decimal" => __("Decimal", "chart-builder"),
				"scientific" => __("Scientific", "chart-builder"),
				"currency" => __("Currency", "chart-builder"),
				"percent" => __("Percent", "chart-builder"),
				"short" => __("Short", "chart-builder"),
				"long" => __("Long", "chart-builder"),
			);
		
			$group_width_format_options = array(
				"%" => __("%", "chart-builder"),
				"px" => __("px", "chart-builder"),
			);
		
			$position_styles = array(
				"left" => 'margin-left:0',
				"right" => 'margin-right:0',
				"center" => 'margin:auto',
			);
			
			$org_chart_font_size_options = array(
				"small" => __("Small", "chart-builder"),
				"medium" => __("Medium", "chart-builder"),
				"large" => __("Large", "chart-builder"),
			);
		
			$text_transforms = array(
				"uppercase" => __("Uppercase", "chart-builder"),
				"lowercase" => __("Lowercase", "chart-builder"),
				"capitalize" => __("Capitalize", "chart-builder"),
				"none" => __("None", "chart-builder"),
			);
		
			$text_decorations = array(
				"overline" => __("Overline", "chart-builder"),
				"underline" => __("Underline", "chart-builder"),
				"line-through" => __("Line through", "chart-builder"),
				"none" => __("None", "chart-builder"),
			);

			// Width
			$settings['width'] = isset( $settings['width'] ) && $settings['width'] != '' ? esc_attr( $settings['width'] ) : '100';
			$settings['width_format'] = isset( $settings['width_format'] ) && $settings['width_format'] != '' ? esc_attr( $settings['width_format'] ) : '%';
			$settings['width_format_options'] = $group_width_format_options;

			// responsive width
			$settings['responsive_width'] = ( isset( $settings['responsive_width'] ) && $settings['responsive_width'] != '' ) ? $settings['responsive_width'] : 'off';
			$settings['responsive_width'] = isset( $settings['responsive_width'] ) && $settings['responsive_width'] == 'on' ? 'checked' : '';

			// position
			$settings['position'] = isset( $settings['position'] ) && $settings['position'] != '' ? esc_attr( $settings['position'] ) : 'center';
			$settings['position_styles'] = $position_styles;

			// Height
			$settings['height'] = isset( $settings['height'] ) && $settings['height'] != '' ? esc_attr( $settings['height'] ) : '400';
			$settings['height_format'] = isset( $settings['height_format'] ) && $settings['height_format'] != '' ? esc_attr( $settings['height_format'] ) : 'px';

			// Font size
			$settings['font_size'] = isset( $settings['font_size'] ) && $settings['font_size'] != '' ? esc_attr( $settings['font_size'] ) : '15';

			// Background color
			$settings['background_color'] = isset( $settings['background_color'] ) && $settings['background_color'] != '' ? esc_attr( $settings['background_color'] ) : '#ffffff';

			// Transparent background
			$settings['transparent_background'] = isset( $settings['transparent_background'] ) && $settings['transparent_background'] != '' ? esc_attr( $settings['transparent_background'] ) : 'off';
			$settings['transparent_background'] = isset( $settings['transparent_background'] ) && $settings['transparent_background'] == 'on' ? 'checked' : '';

			// Border width
			$settings['border_width'] = isset( $settings['border_width'] ) && $settings['border_width'] != '' ? esc_attr( $settings['border_width'] ) : '0';

			// Border radius
			$settings['border_radius'] = isset( $settings['border_radius'] ) && $settings['border_radius'] != '' ? esc_attr( $settings['border_radius'] ) : '0';

			// Border color
			$settings['border_color'] = isset( $settings['border_color'] ) && $settings['border_color'] != '' ? esc_attr( $settings['border_color'] ) : '#666666';

			// Chart Area background color
			$settings['chart_background_color'] = isset( $settings['chart_background_color'] ) && $settings['chart_background_color'] != '' ? esc_attr( $settings['chart_background_color'] ) : '#ffffff';

			// Chart Area border width
			$settings['chart_border_width'] = isset( $settings['chart_border_width'] ) && $settings['chart_border_width'] != '' ? esc_attr( $settings['chart_border_width'] ) : '0';

			// Chart Area border color
			$settings['chart_border_color'] = isset( $settings['chart_border_color'] ) && $settings['chart_border_color'] != '' ? esc_attr( $settings['chart_border_color'] ) : '#666666';

			// Chart Area left margin
			$settings['chart_left_margin'] = isset( $settings['chart_left_margin'] ) && $settings['chart_left_margin'] != '' ? esc_attr( $settings['chart_left_margin'] ) : '';
			$settings['chart_left_margin_for_js'] = isset( $settings['chart_left_margin'] ) && $settings['chart_left_margin'] != '' ? esc_attr( $settings['chart_left_margin'] ) : 'auto';

			// Chart Area right margin
			$settings['chart_right_margin'] = isset( $settings['chart_right_margin'] ) && $settings['chart_right_margin'] != '' ? esc_attr( $settings['chart_right_margin'] ) : '';
			$settings['chart_right_margin_for_js'] = isset( $settings['chart_right_margin'] ) && $settings['chart_right_margin'] != '' ? esc_attr( $settings['chart_right_margin'] ) : 'auto';

			// Chart Area top margin
			$settings['chart_top_margin'] = isset( $settings['chart_top_margin'] ) && $settings['chart_top_margin'] != '' ? esc_attr( $settings['chart_top_margin'] ) : '';
			$settings['chart_top_margin_for_js'] = isset( $settings['chart_top_margin'] ) && $settings['chart_top_margin'] != '' ? esc_attr( $settings['chart_top_margin'] ) : 'auto';
			
			// Chart Area bottom margin
			$settings['chart_bottom_margin'] = isset( $settings['chart_bottom_margin'] ) && $settings['chart_bottom_margin'] != '' ? esc_attr( $settings['chart_bottom_margin'] ) : '';
			$settings['chart_bottom_margin_for_js'] = isset( $settings['chart_bottom_margin'] ) && $settings['chart_bottom_margin'] != '' ? esc_attr( $settings['chart_bottom_margin'] ) : 'auto';

			// Title color
			$settings['title_color'] = isset( $settings['title_color'] ) && $settings['title_color'] != '' ? esc_attr( $settings['title_color'] ) : '#000000';

			// Title color
			$settings['title_shadow_color'] = isset( $settings['title_shadow_color'] ) && $settings['title_shadow_color'] != '' ? esc_attr( $settings['title_shadow_color'] ) : $settings['title_color'];

			// Title font size
			$settings['title_font_size'] = isset( $settings['title_font_size'] ) && $settings['title_font_size'] != '' ? esc_attr( $settings['title_font_size'] ) : '30';

			// Title Bold text
			$settings['title_bold'] = ( isset( $settings['title_bold'] ) && $settings['title_bold'] != '' ) ? esc_attr($settings['title_bold']) : 'on';
			$settings['title_bold'] = isset( $settings['title_bold'] ) && $settings['title_bold'] == 'on' ? 'checked' : '';

			// Title text shadow
			$settings['title_text_shadow'] = ( isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] != '' ) ? esc_attr($settings['title_text_shadow']) : 'off';
			$settings['title_text_shadow'] = isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] == 'on' ? 'checked' : '';

			// Title italic text
			$settings['title_italic'] = ( isset( $settings['title_italic'] ) && $settings['title_italic'] != '' ) ? esc_attr($settings['title_italic']) : 'off';
			$settings['title_italic'] = isset( $settings['title_italic'] ) && $settings['title_italic'] == 'on' ? 'checked' : '';

			// Title gap
			$settings['title_gap'] = isset( $settings['title_gap'] ) && $settings['title_gap'] != '' ? esc_attr( $settings['title_gap'] ) : '5';
			
			// Title gap
			$settings['title_gap_description'] = isset( $settings['title_gap_description'] ) && $settings['title_gap_description'] != '' ? esc_attr( $settings['title_gap_description'] ) : '5';

			// Title letter spacing
			$settings['title_letter_spacing'] = isset( $settings['title_letter_spacing'] ) && $settings['title_letter_spacing'] != '' ? esc_attr( $settings['title_letter_spacing'] ) : 0;

			// Title position
			$settings['title_position'] = isset( $settings['title_position'] ) && $settings['title_position'] != '' ? esc_attr( $settings['title_position'] ) : 'left';
			$settings['title_positions'] = $title_positions;

			// Title text transform
			$settings['title_text_transform'] = isset( $settings['title_text_transform'] ) && $settings['title_text_transform'] != '' ? esc_attr( $settings['title_text_transform'] ) : 'none';
			$settings['text_transforms'] = $text_transforms;

			// Title text decoration
			$settings['title_text_decoration'] = isset( $settings['title_text_decoration'] ) && $settings['title_text_decoration'] != '' ? esc_attr( $settings['title_text_decoration'] ) : 'none';
			$settings['text_decorations'] = $text_decorations;

			// description color
			$settings['description_color'] = isset( $settings['description_color'] ) && $settings['description_color'] != '' ? esc_attr( $settings['description_color'] ) : '#4c4c4c';
			
			// description font size
			$settings['description_font_size'] = isset( $settings['description_font_size'] ) && $settings['description_font_size'] != '' ? esc_attr( $settings['description_font_size'] ) : '16';

			// description Bold text
			$settings['description_bold'] = ( isset( $settings['description_bold'] ) && $settings['description_bold'] != '' ) ? esc_attr($settings['description_bold']) : 'off';
			$settings['description_bold'] = isset( $settings['description_bold'] ) && $settings['description_bold'] == 'on' ? 'checked' : '';

			// description text shadow
			$settings['description_text_shadow'] = ( isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] != '' ) ? esc_attr($settings['description_text_shadow']) : 'off';
			$settings['description_text_shadow'] = isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] == 'on' ? 'checked' : '';

			// description color
			$settings['description_shadow_color'] = isset( $settings['description_shadow_color'] ) && $settings['description_shadow_color'] != '' ? esc_attr( $settings['description_shadow_color'] ) : $settings['description_color'];

			// description italic text
			$settings['description_italic'] = ( isset( $settings['description_italic'] ) && $settings['description_italic'] != '' ) ? esc_attr($settings['description_italic']) : 'off';
			$settings['description_italic'] = isset( $settings['description_italic'] ) && $settings['description_italic'] == 'on' ? 'checked' : '';

			// description position
			$settings['description_position'] = isset( $settings['description_position'] ) && $settings['description_position'] != '' ? esc_attr( $settings['description_position'] ) : 'left';

			// description text transform
			$settings['description_text_transform'] = isset( $settings['description_text_transform'] ) && $settings['description_text_transform'] != '' ? esc_attr( $settings['description_text_transform'] ) : 'none';

			// description letter spacing
			$settings['description_letter_spacing'] = isset( $settings['description_letter_spacing'] ) && $settings['description_letter_spacing'] != '' ? esc_attr( $settings['description_letter_spacing'] ) : 0;

			// description text decoration
			$settings['description_text_decoration'] = isset( $settings['description_text_decoration'] ) && $settings['description_text_decoration'] != '' ? esc_attr( $settings['description_text_decoration'] ) : 'none';
			
			// Rotation degree
			$settings['rotation_degree'] = isset( $settings['rotation_degree'] ) && $settings['rotation_degree'] != '' ? esc_attr( $settings['rotation_degree'] ) : '0';

			// Is stacked
			$settings['is_stacked'] = ( isset( $settings['is_stacked'] ) && $settings['is_stacked'] != '' ) ? $settings['is_stacked'] : 'off';
			$settings['is_stacked'] = isset( $settings['is_stacked'] ) && $settings['is_stacked'] == 'on' ? 'checked' : '';

			// Line width
			$settings['line_width'] = isset( $settings['line_width'] ) && $settings['line_width'] != '' ? esc_attr( $settings['line_width'] ) : '2';

			// Slice border color
			$settings['slice_border_color'] = isset( $settings['slice_border_color'] ) && $settings['slice_border_color'] != '' ? esc_attr( $settings['slice_border_color'] ) : '#ffffff';

			// Reverse categories
			$settings['reverse_categories'] = ( isset( $settings['reverse_categories'] ) && $settings['reverse_categories'] != '' ) ? $settings['reverse_categories'] : 'off';
			$settings['reverse_categories'] = isset( $settings['reverse_categories'] ) && $settings['reverse_categories'] == 'on' ? 'checked' : '';

			// Slice text
			$settings['slice_text'] = isset( $settings['slice_text'] ) && $settings['slice_text'] != '' ? esc_attr( $settings['slice_text'] ) : 'percentage';
			$settings['slice_texts'] = $slice_texts;

			// Tooltip trigger
			$settings['tooltip_trigger'] = isset( $settings['tooltip_trigger'] ) && $settings['tooltip_trigger'] != '' ? esc_attr( $settings['tooltip_trigger'] ) : 'hover';
			$settings['tooltip_trigger_options'] = $tooltip_trigger_options;
			
			// Tooltip text
			$settings['tooltip_text'] = isset( $settings['tooltip_text'] ) && $settings['tooltip_text'] != '' ? esc_attr( $settings['tooltip_text'] ) : 'both';
			$settings['tooltip_text_options'] = $tooltip_text_options;

			// Multiple data format
			$settings['multiple_data_format'] = isset( $settings['multiple_data_format'] ) && $settings['multiple_data_format'] != '' ? esc_attr( $settings['multiple_data_format'] ) : 'auto';
			$settings['multiple_data_format_options'] = $multiple_data_format_options;

			// Data grouping settings
			$settings['data_grouping_limit'] = isset( $settings['data_grouping_limit'] ) && $settings['data_grouping_limit'] != '' ? esc_attr( $settings['data_grouping_limit'] ) : '0.5';
			$settings['data_grouping_label'] = isset( $settings['data_grouping_label'] ) && $settings['data_grouping_label'] != '' ? esc_attr( $settings['data_grouping_label'] ) : 'Other';
			$settings['data_grouping_color'] = isset( $settings['data_grouping_color'] ) && $settings['data_grouping_color'] != '' ? esc_attr( $settings['data_grouping_color'] ) : '#ccc';

			// Focus target
			$settings['focus_target'] = isset( $settings['focus_target'] ) && $settings['focus_target'] != '' ? esc_attr( $settings['focus_target'] ) : 'datum';
			$settings['focus_target_options'] = $focus_target_options;

			// Show color code
			$settings['show_color_code'] = ( isset( $settings['show_color_code'] ) && $settings['show_color_code'] != '' ) ? $settings['show_color_code'] : 'off';
			$settings['show_color_code'] = isset( $settings['show_color_code'] ) && $settings['show_color_code'] == 'on' ? 'checked' : '';

			// Italic text
			$settings['tooltip_italic'] = ( isset( $settings['tooltip_italic'] ) && $settings['tooltip_italic'] != '' ) ? $settings['tooltip_italic'] : 'off';
			$settings['tooltip_italic'] = isset( $settings['tooltip_italic'] ) && $settings['tooltip_italic'] == 'on' ? 'checked' : '';

			// Bold text
			$settings['tooltip_bold'] = isset( $settings['tooltip_bold'] ) && $settings['tooltip_bold'] != '' ? esc_attr( $settings['tooltip_bold'] ) : 'default';
			$settings['tooltip_bold_options'] = $tooltip_bold_options;

			// Tooltip text color
			$settings['tooltip_text_color'] = isset( $settings['tooltip_text_color'] ) && $settings['tooltip_text_color'] != '' ? esc_attr( $settings['tooltip_text_color'] ) : '#000000';

			// Tooltip font size
			$settings['tooltip_font_size'] = isset( $settings['tooltip_font_size'] ) && intval($settings['tooltip_font_size']) > 0 ? esc_attr( $settings['tooltip_font_size'] ) : $settings['font_size'];

			// Legend position
			$settings['legend_position'] = isset( $settings['legend_position'] ) && $settings['legend_position'] != '' ? esc_attr( $settings['legend_position'] ) : 'right';
			$settings['legend_positions'] = $legend_positions;

			// Legend alignment
			$settings['legend_alignment'] = isset( $settings['legend_alignment'] ) && $settings['legend_alignment'] != '' ? esc_attr( $settings['legend_alignment'] ) : 'start';
			$settings['legend_alignments'] = $legend_alignments;

			// Legend font color
			$settings['legend_color'] = isset( $settings['legend_color'] ) && $settings['legend_color'] != '' ? esc_attr( $settings['legend_color'] ) : '#000000';

			// Legend font size
			$settings['legend_font_size'] = isset( $settings['legend_font_size'] ) && intval($settings['legend_font_size']) > 0 ? esc_attr( $settings['legend_font_size'] ) : $settings['font_size'];

			// Legend Italic text
			$settings['legend_italic'] = ( isset( $settings['legend_italic'] ) && $settings['legend_italic'] != '' ) ? $settings['legend_italic'] : 'off';
			$settings['legend_italic'] = isset( $settings['legend_italic'] ) && $settings['legend_italic'] == 'on' ? 'checked' : '';

			// Legend Bold text
			$settings['legend_bold'] = ( isset( $settings['legend_bold'] ) && $settings['legend_bold'] != '' ) ? $settings['legend_bold'] : 'off';
			$settings['legend_bold'] = isset( $settings['legend_bold'] ) && $settings['legend_bold'] == 'on' ? 'checked' : '';

			// Opacity
			$settings['opacity'] = isset( $settings['opacity'] ) && $settings['opacity'] != '' ? esc_attr( $settings['opacity'] ) : '1.0';
			
			// Group width
			$settings['group_width'] = isset( $settings['group_width'] ) && $settings['group_width'] != '' ? esc_attr( $settings['group_width'] ) : '61.8';
			$settings['group_width_format'] = isset( $settings['group_width_format'] ) && $settings['group_width_format'] != '' ? esc_attr( $settings['group_width_format'] ) : '%';
			$settings['group_width_format_options'] = $group_width_format_options;

			// Show chart description
			if (!isset($settings['show_description'])) {
				$settings['show_description'] = 'checked';
			} else {
				$settings['show_description'] = ( $settings['show_description'] != '' ) ? $settings['show_description'] : 'off';
				$settings['show_description'] = isset( $settings['show_description'] ) && $settings['show_description'] == 'on' ? 'checked' : '';
			}
			
			// Show chart title
			if (!isset($settings['show_title'])) {
				$settings['show_title'] = 'checked';
			} else {
				$settings['show_title'] = ( $settings['show_title'] != '' ) ? $settings['show_title'] : 'off';
				$settings['show_title'] = isset( $settings['show_title'] ) && $settings['show_title'] == 'on' ? 'checked' : '';
			}
			
			// Enable interactivity
			if (!isset($settings['enable_interactivity'])) {
				$settings['enable_interactivity'] = 'checked';
			} else {
				$settings['enable_interactivity'] = ( $settings['enable_interactivity'] != '' ) ? $settings['enable_interactivity'] : 'off';
				$settings['enable_interactivity'] = isset( $settings['enable_interactivity'] ) && $settings['enable_interactivity'] == 'on' ? 'checked' : '';
			}

			// Maximized view
			$settings['maximized_view'] = ( isset( $settings['maximized_view'] ) && $settings['maximized_view'] != '' ) ? $settings['maximized_view'] : 'off';
			$settings['maximized_view'] = isset( $settings['maximized_view'] ) && $settings['maximized_view'] == 'on' ? 'checked' : '';

			// Multiple data selection
			$settings['multiple_selection'] = ( isset( $settings['multiple_selection'] ) && $settings['multiple_selection'] != '' ) ? $settings['multiple_selection'] : 'off';
			$settings['multiple_selection'] = isset( $settings['multiple_selection'] ) && $settings['multiple_selection'] == 'on' ? 'checked' : '';

			// Point shape
			$settings['point_shape'] = isset( $settings['point_shape'] ) && $settings['point_shape'] != '' ? esc_attr( $settings['point_shape'] ) : 'circle';
			$settings['point_shape_options'] = $point_shape_options;
			
			// Point size
			$settings['point_size'] = isset( $settings['point_size'] ) && $settings['point_size'] != '' ? absint(esc_attr( $settings['point_size'] )) : '0';

			// Crosshair trigger
			$settings['crosshair_trigger'] = isset( $settings['crosshair_trigger'] ) && $settings['crosshair_trigger'] != '' ? esc_attr( $settings['crosshair_trigger'] ) : 'none';
			$settings['crosshair_trigger_options'] = $crosshair_trigger_options;
			
			// Crosshair orientation
			$settings['crosshair_orientation'] = isset( $settings['crosshair_orientation'] ) && $settings['crosshair_orientation'] != '' ? esc_attr( $settings['crosshair_orientation'] ) : 'both';
			$settings['crosshair_orientation_options'] = $crosshair_orientation_options;

			// Crosshair opacity
			$settings['crosshair_opacity'] = isset( $settings['crosshair_opacity'] ) && $settings['crosshair_opacity'] != '' ? esc_attr( $settings['crosshair_opacity'] ) : '1.0';

			// Dash style
			$settings['dash_style'] = isset( $settings['dash_style'] ) && $settings['dash_style'] != '' ? esc_attr( str_replace(' ', '', $settings['dash_style']) ) : '';

			// Orientation
			$settings['orientation'] = ( isset( $settings['orientation'] ) && $settings['orientation'] != '' ) ? $settings['orientation'] : 'off';
			$settings['orientation'] = isset( $settings['orientation'] ) && $settings['orientation'] == 'on' ? 'checked' : '';

			// Fill nulls
			$settings['fill_nulls'] = ( isset( $settings['fill_nulls'] ) && $settings['fill_nulls'] != '' ) ? $settings['fill_nulls'] : 'off';
			$settings['fill_nulls'] = isset( $settings['fill_nulls'] ) && $settings['fill_nulls'] == 'on' ? 'checked' : '';

			// Font size for org chart
			$settings['org_chart_font_size'] = isset( $settings['org_chart_font_size'] ) && $settings['org_chart_font_size'] != '' ? esc_attr( $settings['org_chart_font_size'] ) : 'medium';
			$settings['org_chart_font_size_options'] = $org_chart_font_size_options;
			
			// Donut hole size
			$settings['donut_hole_size'] = isset( $settings['donut_hole_size'] ) && $settings['donut_hole_size'] != '' ? esc_attr( $settings['donut_hole_size'] ) : '0.4';

			// Allow collapse
			$settings['allow_collapse'] = ( isset( $settings['allow_collapse'] ) && $settings['allow_collapse'] != '' ) ? $settings['allow_collapse'] : 'off';
			$settings['allow_collapse'] = isset( $settings['allow_collapse'] ) && $settings['allow_collapse'] == 'on' ? 'checked' : '';

			// Org custom css class
			$settings['org_classname'] = isset( $settings['org_classname'] ) && $settings['org_classname'] != '' ? esc_attr( $settings['org_classname'] ) : '';

			$settings['org_node_background_color'] = isset( $settings['org_node_background_color'] ) && $settings['org_node_background_color'] != '' ? esc_attr( $settings['org_node_background_color'] ) : '#edf7ff';
			$settings['org_node_padding'] = isset( $settings['org_node_padding'] ) && $settings['org_node_padding'] != '' ? esc_attr( $settings['org_node_padding'] ) : '2';
			$settings['org_node_border_radius'] = isset( $settings['org_node_border_radius'] ) && $settings['org_node_border_radius'] != '' ? esc_attr( $settings['org_node_border_radius'] ) : '5';
			$settings['org_node_border_width'] = isset( $settings['org_node_border_width'] ) && $settings['org_node_border_width'] != '' ? esc_attr( $settings['org_node_border_width'] ) : '0';
			$settings['org_node_border_color'] = isset( $settings['org_node_border_color'] ) && $settings['org_node_border_color'] != '' ? esc_attr( $settings['org_node_border_color'] ) : '#b5d9ea';
			$settings['org_node_text_color'] = isset( $settings['org_node_text_color'] ) && $settings['org_node_text_color'] != '' ? esc_attr( $settings['org_node_text_color'] ) : '#000000';
			$settings['org_node_text_font_size'] = isset( $settings['org_node_text_font_size'] ) && $settings['org_node_text_font_size'] != '' ? esc_attr( $settings['org_node_text_font_size'] ) : '13';
			$settings['org_node_description_font_color'] = isset( $settings['org_node_description_font_color'] ) && $settings['org_node_description_font_color'] != '' ? esc_attr( $settings['org_node_description_font_color'] ) : '#ff0000';
			$settings['org_node_description_font_size'] = isset( $settings['org_node_description_font_size'] ) && $settings['org_node_description_font_size'] != '' ? esc_attr( $settings['org_node_description_font_size'] ) : '13';

			// Org custom selected css class
			$settings['org_selected_classname'] = isset( $settings['org_selected_classname'] ) && $settings['org_selected_classname'] != '' ? esc_attr( $settings['org_selected_classname'] ) : '';

			$settings['org_selected_node_background_color'] = isset( $settings['org_selected_node_background_color'] ) && $settings['org_selected_node_background_color'] != '' ? esc_attr( $settings['org_selected_node_background_color'] ) : '#fff7ae';
			$settings['org_selected_node_text_color'] = isset( $settings['org_selected_node_text_color'] ) && $settings['org_selected_node_text_color'] != '' ? esc_attr( $settings['org_selected_node_text_color'] ) : $settings['org_node_text_color'];


			$settings['axes_text_positions'] = $axes_text_positions;
			$settings['axes_format_options'] = $axes_format_options;
			// Horizontal axis settings
			$settings['haxis_title'] = isset( $settings['haxis_title'] ) && $settings['haxis_title'] != '' ? esc_attr( $settings['haxis_title'] ) : '';
			$settings['haxis_label_font_size'] = isset( $settings['haxis_label_font_size'] ) && $settings['haxis_label_font_size'] != '' ? esc_attr( $settings['haxis_label_font_size'] ) : $settings['font_size'];
			$settings['haxis_label_color'] = isset( $settings['haxis_label_color'] ) && $settings['haxis_label_color'] != '' ? esc_attr( $settings['haxis_label_color'] ) : '#000000';
			$settings['haxis_text_position'] = isset( $settings['haxis_text_position'] ) && $settings['haxis_text_position'] != '' ? esc_attr( $settings['haxis_text_position'] ) : 'out';
			$settings['haxis_direction'] = ( isset( $settings['haxis_direction'] ) && $settings['haxis_direction'] != '' ) ? $settings['haxis_direction'] : '1';
			$settings['haxis_direction'] = isset( $settings['haxis_direction'] ) && $settings['haxis_direction'] == '-1' ? 'checked' : '';
			$settings['haxis_text_color'] = isset( $settings['haxis_text_color'] ) && $settings['haxis_text_color'] != '' ? esc_attr( $settings['haxis_text_color'] ) : '#000000';
			$settings['haxis_baseline_color'] = isset( $settings['haxis_baseline_color'] ) && $settings['haxis_baseline_color'] != '' ? esc_attr( $settings['haxis_baseline_color'] ) : '#000000';
			$settings['haxis_text_font_size'] = isset( $settings['haxis_text_font_size'] ) && $settings['haxis_text_font_size'] != '' ? absint(esc_attr( $settings['haxis_text_font_size'] )) : $settings['font_size'];
			$settings['haxis_slanted_options'] = $haxis_slanted_options;
			$settings['haxis_slanted'] = isset( $settings['haxis_slanted'] ) && $settings['haxis_slanted'] != '' ? esc_attr( $settings['haxis_slanted'] ) : 'automatic';
			$settings['haxis_slanted_text_angle'] = isset( $settings['haxis_slanted_text_angle'] ) && $settings['haxis_slanted_text_angle'] != '' && $settings['haxis_slanted_text_angle'] != '0' ? esc_attr( $settings['haxis_slanted_text_angle'] ) : '30';
			$settings['haxis_show_text_every'] = isset( $settings['haxis_show_text_every'] ) && $settings['haxis_show_text_every'] != '' ? esc_attr( $settings['haxis_show_text_every'] ) : '0';
			$settings['haxis_format'] = isset( $settings['haxis_format'] ) && $settings['haxis_format'] != '' ? esc_attr( $settings['haxis_format'] ) : '';
			$settings['haxis_max_value'] = isset( $settings['haxis_max_value'] ) && $settings['haxis_max_value'] != '' ? esc_attr( $settings['haxis_max_value'] ) : null;
			$settings['haxis_min_value'] = isset( $settings['haxis_min_value'] ) && $settings['haxis_min_value'] != '' ? esc_attr( $settings['haxis_min_value'] ) : null;
			$settings['haxis_gridlines_count'] = isset( $settings['haxis_gridlines_count'] ) && $settings['haxis_gridlines_count'] != '' ? esc_attr( $settings['haxis_gridlines_count'] ) : -1;$settings['haxis_italic'] = ( isset( $settings['haxis_italic'] ) && $settings['haxis_italic'] != '' ) ? $settings['haxis_italic'] : 'off';
			$settings['haxis_italic'] = isset( $settings['haxis_italic'] ) && $settings['haxis_italic'] == 'on' ? 'checked' : '';
			$settings['haxis_bold'] = ( isset( $settings['haxis_bold'] ) && $settings['haxis_bold'] != '' ) ? $settings['haxis_bold'] : 'off';
			$settings['haxis_bold'] = isset( $settings['haxis_bold'] ) && $settings['haxis_bold'] == 'on' ? 'checked' : '';
			$settings['haxis_title_italic'] = ( isset( $settings['haxis_title_italic'] ) && $settings['haxis_title_italic'] != '' ) ? $settings['haxis_title_italic'] : 'off';
			$settings['haxis_title_italic'] = isset( $settings['haxis_title_italic'] ) && $settings['haxis_title_italic'] == 'on' ? 'checked' : '';
			$settings['haxis_title_bold'] = ( isset( $settings['haxis_title_bold'] ) && $settings['haxis_title_bold'] != '' ) ? $settings['haxis_title_bold'] : 'off';
			$settings['haxis_title_bold'] = isset( $settings['haxis_title_bold'] ) && $settings['haxis_title_bold'] == 'on' ? 'checked' : '';
			$settings['haxis_gridlines_color'] = isset( $settings['haxis_gridlines_color'] ) && $settings['haxis_gridlines_color'] != '' ? esc_attr( $settings['haxis_gridlines_color'] ) : '#cccccc';
			$settings['haxis_minor_gridlines_color'] = isset( $settings['haxis_minor_gridlines_color'] ) && $settings['haxis_minor_gridlines_color'] != '' ? esc_attr( $settings['haxis_minor_gridlines_color'] ) : $settings['haxis_gridlines_color'];

			// Vertical axis settings
			$settings['vaxis_title'] = isset( $settings['vaxis_title'] ) && $settings['vaxis_title'] != '' ? esc_attr( $settings['vaxis_title'] ) : '';
			$settings['vaxis_label_font_size'] = isset( $settings['vaxis_label_font_size'] ) && $settings['vaxis_label_font_size'] != '' ? esc_attr( $settings['vaxis_label_font_size'] ) : $settings['font_size'];
			$settings['vaxis_label_color'] = isset( $settings['vaxis_label_color'] ) && $settings['vaxis_label_color'] != '' ? esc_attr( $settings['vaxis_label_color'] ) : '#000000';
			$settings['vaxis_text_position'] = isset( $settings['vaxis_text_position'] ) && $settings['vaxis_text_position'] != '' ? esc_attr( $settings['vaxis_text_position'] ) : 'out';
			$settings['vaxis_direction'] = ( isset( $settings['vaxis_direction'] ) && $settings['vaxis_direction'] != '' ) ? $settings['vaxis_direction'] : '1';
			$settings['vaxis_direction'] = isset( $settings['vaxis_direction'] ) && $settings['vaxis_direction'] == '-1' ? 'checked' : '';
			$settings['vaxis_text_color'] = isset( $settings['vaxis_text_color'] ) && $settings['vaxis_text_color'] != '' ? esc_attr( $settings['vaxis_text_color'] ) : '#000000';
			$settings['vaxis_baseline_color'] = isset( $settings['vaxis_baseline_color'] ) && $settings['vaxis_baseline_color'] != '' ? esc_attr( $settings['vaxis_baseline_color'] ) : '#000000';
			$settings['vaxis_text_font_size'] = isset( $settings['vaxis_text_font_size'] ) && $settings['vaxis_text_font_size'] != '' ? absint(esc_attr( $settings['vaxis_text_font_size'] )) : $settings['font_size'];
			$settings['vaxis_format'] = isset( $settings['vaxis_format'] ) && $settings['vaxis_format'] != '' ? esc_attr( $settings['vaxis_format'] ) : '';
			$settings['vaxis_max_value'] = isset( $settings['vaxis_max_value'] ) && $settings['vaxis_max_value'] != '' ? esc_attr( $settings['vaxis_max_value'] ) : null;
			$settings['vaxis_min_value'] = isset( $settings['vaxis_min_value'] ) && $settings['vaxis_min_value'] != '' ? esc_attr( $settings['vaxis_min_value'] ) : null;
			$settings['vaxis_gridlines_count'] = isset( $settings['vaxis_gridlines_count'] ) && $settings['vaxis_gridlines_count'] != '' ? esc_attr( $settings['vaxis_gridlines_count'] ) : -1;
			$settings['vaxis_italic'] = ( isset( $settings['vaxis_italic'] ) && $settings['vaxis_italic'] != '' ) ? $settings['vaxis_italic'] : 'off';
			$settings['vaxis_italic'] = isset( $settings['vaxis_italic'] ) && $settings['vaxis_italic'] == 'on' ? 'checked' : '';
			$settings['vaxis_bold'] = ( isset( $settings['vaxis_bold'] ) && $settings['vaxis_bold'] != '' ) ? $settings['vaxis_bold'] : 'off';
			$settings['vaxis_bold'] = isset( $settings['vaxis_bold'] ) && $settings['vaxis_bold'] == 'on' ? 'checked' : '';
			$settings['vaxis_title_italic'] = ( isset( $settings['vaxis_title_italic'] ) && $settings['vaxis_title_italic'] != '' ) ? $settings['vaxis_title_italic'] : 'off';
			$settings['vaxis_title_italic'] = isset( $settings['vaxis_title_italic'] ) && $settings['vaxis_title_italic'] == 'on' ? 'checked' : '';
			$settings['vaxis_title_bold'] = ( isset( $settings['vaxis_title_bold'] ) && $settings['vaxis_title_bold'] != '' ) ? $settings['vaxis_title_bold'] : 'off';
			$settings['vaxis_title_bold'] = isset( $settings['vaxis_title_bold'] ) && $settings['vaxis_title_bold'] == 'on' ? 'checked' : '';
			$settings['vaxis_gridlines_color'] = isset( $settings['vaxis_gridlines_color'] ) && $settings['vaxis_gridlines_color'] != '' ? esc_attr( $settings['vaxis_gridlines_color'] ) : '#cccccc';
			$settings['vaxis_minor_gridlines_color'] = isset( $settings['vaxis_minor_gridlines_color'] ) && $settings['vaxis_minor_gridlines_color'] != '' ? esc_attr( $settings['vaxis_minor_gridlines_color'] ) : $settings['vaxis_gridlines_color'];

			// Animation settings
			$settings['enable_animation'] = ( isset( $settings['enable_animation'] ) && $settings['enable_animation'] != '' ) ? $settings['enable_animation'] : 'off';
			$settings['enable_animation'] = isset( $settings['enable_animation'] ) && $settings['enable_animation'] == 'on' ? 'checked' : '';
			$settings['animation_duration'] = isset( $settings['animation_duration'] ) && $settings['animation_duration'] != '' ? absint(esc_attr( $settings['animation_duration'] )) : '1000';
			$settings['animation_startup'] = ( isset( $settings['animation_startup'] ) && $settings['animation_startup'] != '' ) ? $settings['animation_startup'] : 'on';
			$settings['animation_startup'] = isset( $settings['animation_startup'] ) && $settings['animation_startup'] == 'on' ? 'checked' : '';
			$settings['animation_easing_options'] = $animation_easing_options;
			$settings['animation_easing'] = isset( $settings['animation_easing'] ) && $settings['animation_easing'] != '' ? esc_attr( $settings['animation_easing'] ) : 'linear';

			$settings['enable_img'] = ( isset( $settings['enable_img'] ) && $settings['enable_img'] != '' ) ? $settings['enable_img'] : 'off';
			$settings['enable_img'] = isset( $settings['enable_img'] ) && $settings['enable_img'] == 'on' ? 'checked' : '';

			$counting_source = $source;

			$count_slices = (isset($counting_source) && !is_null($counting_source) && count($counting_source) > 0) ? count($counting_source) - 1 : 0;
			$count_series = (isset($counting_source[0]) && !is_null($counting_source[0]) && count($counting_source[0]) > 0) ? count($counting_source[0]) - 1 : 0;
			$count_rows = (isset($counting_source) && !is_null($counting_source) && count($counting_source) > 0) ? count(array_column($counting_source, 0)) - 1 : 0;

			// Slices settings
			$settings['slice_colors_default'] = $chart_default_colors;
			$settings['slice_color'] = isset( $settings['slice_color'] ) && $settings['slice_color'] != '' ? json_decode($settings['slice_color'], true) : array_slice($chart_default_colors, 0, $count_slices);;
			$settings['slice_offset'] = isset( $settings['slice_offset'] ) && $settings['slice_offset'] != '' ? json_decode($settings['slice_offset'], true) : array_fill(0, $count_slices, 0);
			$settings['slice_text_color'] = isset( $settings['slice_text_color'] ) && $settings['slice_text_color'] != '' ? json_decode($settings['slice_text_color'], true) : array_fill(0, $count_slices, '#ffffff');
			
			// Series settings
			$settings['series_colors_default'] = $chart_default_colors;
			$settings['series_color'] = isset( $settings['series_color'] ) && $settings['series_color'] != '' ? json_decode($settings['series_color'], true) : array_slice($chart_default_colors, 0, $count_series);;
			$settings['series_visible_in_legend'] = isset( $settings['series_visible_in_legend'] ) && $settings['series_visible_in_legend'] != '' ? json_decode($settings['series_visible_in_legend'], true) : array_fill(0, $count_series, 'on');
			$settings['series_line_width'] = isset( $settings['series_line_width'] ) && $settings['series_line_width'] != '' ? json_decode($settings['series_line_width'], true) : array_fill(0, $count_series, $settings['line_width']);
			$settings['series_point_size'] = isset( $settings['series_point_size'] ) && $settings['series_point_size'] != '' ? json_decode($settings['series_point_size'], true) : array_fill(0, $count_series, $settings['point_size']);
			$settings['series_point_shape'] = isset( $settings['series_point_shape'] ) && $settings['series_point_shape'] != '' ? json_decode($settings['series_point_shape'], true) : array_fill(0, $count_series, $settings['point_shape']);

			// Rows settings
			$settings['enable_row_settings'] = ( isset( $settings['enable_row_settings'] ) && $settings['enable_row_settings'] != '' ) ? $settings['enable_row_settings'] : 'on';
			$settings['enable_row_settings'] = isset( $settings['enable_row_settings'] ) && $settings['enable_row_settings'] == 'on' ? 'checked' : '';

			$settings['rows_color'] = isset( $settings['rows_color'] ) && $settings['rows_color'] != '' ? json_decode($settings['rows_color'], true) : array_fill(0, $count_rows, '');
			$settings['rows_opacity'] = isset( $settings['rows_opacity'] ) && $settings['rows_opacity'] != '' ? json_decode($settings['rows_opacity'], true) : array_fill(0, $count_rows, 1.0);

			return $settings;

		}

		/**
	     * Gets all settings of google charts for public area.
	     *
	     * @access public
	     * @return array
	    */
		public function get_chart_settings_google_public ($settings, $chartData) {
			$chart_default_colors = array('#3366cc','#dc3912','#ff9900','#109618', '#990099','#0099c6','#dd4477','#66aa00', '#b82e2e','#316395','#994499','#22aa99', '#aaaa11','#6633cc','#e67300','#8b0707', '#651067','#329262','#5574a6','#3b3eac', '#b77322','#16d620','#b91383','#f4359e', '#9c5935','#a9c413','#2a778d','#668d1c', '#bea413','#0c5922','#743411');

			$position_styles = array(
				"left" => 'margin-left:0',
				"right" => 'margin-right:0',
				"center" => 'margin:auto',
			);

			// Width
			$settings['width'] = isset( $settings['width'] ) && $settings['width'] != '' ? esc_attr( $settings['width'] ) : '100';
			$settings['width_format'] = isset( $settings['width_format'] ) && $settings['width_format'] != '' ? esc_attr( $settings['width_format'] ) : '%';
			$settings['responsive_width'] = ( isset( $settings['responsive_width'] ) && $settings['responsive_width'] != '' ) ? $settings['responsive_width'] : 'off';
			$settings['chart_width'] = isset($settings['responsive_width']) && $settings['responsive_width'] == 'on' ? '100' : $settings['width'].$settings['width_format'];
			
			// position
			$settings['position'] = isset( $settings['position'] ) && $settings['position'] != '' ? esc_attr( $settings['position'] ) : 'center';
			$settings['position'] = isset($position_styles[$settings['position']]) && $position_styles[$settings['position']] != '' ? $position_styles[$settings['position']] : 'margin:auto';
			
			// height
			$settings['height'] = isset( $settings['height'] ) && $settings['height'] != '' ? esc_attr( $settings['height'] ) : '400';
			$settings['height_format'] = isset( $settings['height_format'] ) && $settings['height_format'] != '' ? esc_attr( $settings['height_format'] ) : 'px';
			$settings['chart_height'] = $settings['height'].$settings['height_format'];

			// Font size
			$settings['font_size'] = isset( $settings['font_size'] ) && $settings['font_size'] != '' ? esc_attr( $settings['font_size'] ) : '15';

			// Background color
			$settings['background_color'] = isset( $settings['background_color'] ) && $settings['background_color'] != '' ? esc_attr( $settings['background_color'] ) : '#ffffff';

			// Transparent background
			$settings['transparent_background'] = isset( $settings['transparent_background'] ) && $settings['transparent_background'] != '' ? esc_attr( $settings['transparent_background'] ) : 'off';

			// Border width
			$settings['border_width'] = isset( $settings['border_width'] ) && $settings['border_width'] != '' ? esc_attr( $settings['border_width'] ) : '0';

			// Border radius
			$settings['border_radius'] = isset( $settings['border_radius'] ) && $settings['border_radius'] != '' ? esc_attr( $settings['border_radius'] ) : '0';

			// Border color
			$settings['border_color'] = isset( $settings['border_color'] ) && $settings['border_color'] != '' ? esc_attr( $settings['border_color'] ) : '#666666';

			// Chart Area background color
			$settings['chart_background_color'] = isset( $settings['chart_background_color'] ) && $settings['chart_background_color'] != '' ? esc_attr( $settings['chart_background_color'] ) : '#ffffff';

			// Chart Area border width
			$settings['chart_border_width'] = isset( $settings['chart_border_width'] ) && $settings['chart_border_width'] != '' ? esc_attr( $settings['chart_border_width'] ) : '0';

			// Chart Area border color
			$settings['chart_border_color'] = isset( $settings['chart_border_color'] ) && $settings['chart_border_color'] != '' ? esc_attr( $settings['chart_border_color'] ) : '#666666';

			// Chart Area left margin
			$settings['chart_left_margin_for_js'] = isset( $settings['chart_left_margin'] ) && $settings['chart_left_margin'] != '' ? esc_attr( $settings['chart_left_margin'] ) : 'auto';

			// Chart Area right margin
			$settings['chart_right_margin_for_js'] = isset( $settings['chart_right_margin'] ) && $settings['chart_right_margin'] != '' ? esc_attr( $settings['chart_right_margin'] ) : 'auto';

			// Chart Area top margin
			$settings['chart_top_margin_for_js'] = isset( $settings['chart_top_margin'] ) && $settings['chart_top_margin'] != '' ? esc_attr( $settings['chart_top_margin'] ) : 'auto';

			// Chart Area bottom margin
			$settings['chart_bottom_margin_for_js'] = isset( $settings['chart_bottom_margin'] ) && $settings['chart_bottom_margin'] != '' ? esc_attr( $settings['chart_bottom_margin'] ) : 'auto';
			
			// Title color
			$settings['title_color'] = isset( $settings['title_color'] ) && $settings['title_color'] != '' ? esc_attr( $settings['title_color'] ) : '#000000';

			// Title color
			$settings['title_shadow_color'] = isset( $settings['title_shadow_color'] ) && $settings['title_shadow_color'] != '' ? esc_attr( $settings['title_shadow_color'] ) : $settings['title_color'];
			
			// Title font size
			$settings['title_font_size'] = isset( $settings['title_font_size'] ) && $settings['title_font_size'] != '' ? esc_attr( $settings['title_font_size'] ) : '30';

			// title bold
			$settings['title_bold'] = ( isset( $settings['title_bold'] ) && $settings['title_bold'] != '' ) ? esc_attr($settings['title_bold']) : 'on';
			$settings['title_bold'] = isset( $settings['title_bold'] ) && $settings['title_bold'] != 'off'? 'bold' : 'normal';

			// Title text shadow
			$settings['title_text_shadow'] = ( isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] != '' ) ? esc_attr($settings['title_text_shadow']) : 'off';
			$settings['title_text_shadow'] = isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] == 'on' ? 'checked' : '';

			// title italic
			$settings['title_italic'] = ( isset( $settings['title_italic'] ) && $settings['title_italic'] != '' ) ? esc_attr($settings['title_italic']) : 'off';
			$settings['title_italic'] = isset( $settings['title_italic'] ) && $settings['title_italic'] != 'on'? 'normal' : 'italic';

			// title gap
			$settings['title_gap'] = (isset( $settings['title_gap'] ) && $settings['title_gap'] != '') ? esc_attr( $settings['title_gap'] ) : '5';

			// title gap
			$settings['title_gap_description'] = (isset( $settings['title_gap_description'] ) && $settings['title_gap_description'] != '') ? esc_attr( $settings['title_gap_description'] ) : '5';

			// title letter spacing
			$settings['title_letter_spacing'] = (isset( $settings['title_letter_spacing'] ) && $settings['title_letter_spacing'] != '') ? esc_attr( $settings['title_letter_spacing'] ) : 0;

			// title position
			$settings['title_position'] = isset( $settings['title_position'] ) && $settings['title_position'] != '' ? esc_attr( $settings['title_position'] ) : 'left';

			// title text transform
			$settings['title_text_transform'] = isset( $settings['title_text_transform'] ) && $settings['title_text_transform'] != '' ? esc_attr( $settings['title_text_transform'] ) : 'none';

			// title text decoration
			$settings['title_text_decoration'] = isset( $settings['title_text_decoration'] ) && $settings['title_text_decoration'] != '' ? esc_attr( $settings['title_text_decoration'] ) : 'none';

			// description color
			$settings['description_color'] = isset( $settings['description_color'] ) && $settings['description_color'] != '' ? esc_attr( $settings['description_color'] ) : '#4c4c4c';

			// description font size
			$settings['description_font_size'] = (isset( $settings['description_font_size'] ) && $settings['description_font_size'] != '') ? esc_attr( $settings['description_font_size'] ) : '16';
			
			// description Bold text
			$settings['description_bold'] = ( isset( $settings['description_bold'] ) && $settings['description_bold'] != '' ) ? esc_attr($settings['description_bold']) : 'off';
			$settings['description_bold'] = isset( $settings['description_bold'] ) && $settings['description_bold'] != 'on'? 'normal' : 'bold';

			// description text shadow
			$settings['description_text_shadow'] = ( isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] != '' ) ? esc_attr($settings['description_text_shadow']) : 'off';
			$settings['description_text_shadow'] = isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] == 'on' ? 'checked' : '';

			// description color
			$settings['description_shadow_color'] = isset( $settings['description_shadow_color'] ) && $settings['description_shadow_color'] != '' ? esc_attr( $settings['description_shadow_color'] ) : $settings['description_color'];

			// desctiption italic text
			$settings['description_italic'] = ( isset( $settings['description_italic'] ) && $settings['description_italic'] != '' ) ? esc_attr($settings['description_italic']) : 'off';
			$settings['description_italic'] = isset( $settings['description_italic'] ) && $settings['description_italic'] != 'on'? 'normal' : 'italic';

			// description position
			$settings['description_position'] = isset( $settings['description_position'] ) && $settings['description_position'] != '' ? esc_attr( $settings['description_position'] ) : 'left';

			// description text transform
			$settings['description_text_transform'] = isset( $settings['description_text_transform'] ) && $settings['description_text_transform'] != '' ? esc_attr( $settings['description_text_transform'] ) : 'none';

			// description letter spacing
			$settings['description_letter_spacing'] = isset( $settings['description_letter_spacing'] ) && $settings['description_letter_spacing'] != '' ? esc_attr( $settings['description_letter_spacing'] ) : 0;

			// description text decoration
			$settings['description_text_decoration'] = isset( $settings['description_text_decoration'] ) && $settings['description_text_decoration'] != '' ? esc_attr( $settings['description_text_decoration'] ) : 'none';

			// Rotation degree
			$settings['rotation_degree'] = isset( $settings['rotation_degree'] ) && $settings['rotation_degree'] != '' ? esc_attr( $settings['rotation_degree'] ) : '0';

			// Is stacked
			$settings['is_stacked'] = ( isset( $settings['is_stacked'] ) && $settings['is_stacked'] != '' ) ? $settings['is_stacked'] : 'off';

			// Line width
			$settings['line_width'] = isset( $settings['line_width'] ) && $settings['line_width'] != '' ? esc_attr( $settings['line_width'] ) : '2';

			// Slice border color
			$settings['slice_border_color'] = isset( $settings['slice_border_color'] ) && $settings['slice_border_color'] != '' ? esc_attr( $settings['slice_border_color'] ) : '#ffffff';

			// Reverse categories
			$settings['reverse_categories'] = ( isset( $settings['reverse_categories'] ) && $settings['reverse_categories'] != '' ) ? $settings['reverse_categories'] : 'off';

			// Slice text
			$settings['slice_text'] = isset( $settings['slice_text'] ) && $settings['slice_text'] != '' ? esc_attr( $settings['slice_text'] ) : 'percentage';

			// Tooltip trigger
			$settings['tooltip_trigger'] = isset( $settings['tooltip_trigger'] ) && $settings['tooltip_trigger'] != '' ? esc_attr( $settings['tooltip_trigger'] ) : 'hover';
			
			// Tooltip text
			$settings['tooltip_text'] = isset( $settings['tooltip_text'] ) && $settings['tooltip_text'] != '' ? esc_attr( $settings['tooltip_text'] ) : 'both';

			// Multiple data format
			$settings['multiple_data_format'] = isset( $settings['multiple_data_format'] ) && $settings['multiple_data_format'] != '' ? esc_attr( $settings['multiple_data_format'] ) : 'auto';

			// Data grouping settings
			$settings['data_grouping_limit'] = isset( $settings['data_grouping_limit'] ) && $settings['data_grouping_limit'] != '' ? esc_attr( $settings['data_grouping_limit'] ) : '0.5';
			$settings['data_grouping_label'] = isset( $settings['data_grouping_label'] ) && $settings['data_grouping_label'] != '' ? esc_attr( $settings['data_grouping_label'] ) : 'Other';
			$settings['data_grouping_color'] = isset( $settings['data_grouping_color'] ) && $settings['data_grouping_color'] != '' ? esc_attr( $settings['data_grouping_color'] ) : '#ccc';

			// Focus target
			$settings['focus_target'] = isset( $settings['focus_target'] ) && $settings['focus_target'] != '' ? esc_attr( $settings['focus_target'] ) : 'datum';

			// Show color code
			$settings['show_color_code'] = ( isset( $settings['show_color_code'] ) && $settings['show_color_code'] != '' ) ? $settings['show_color_code'] : 'off';

			// Italic text
			$settings['tooltip_italic'] = ( isset( $settings['tooltip_italic'] ) && $settings['tooltip_italic'] != '' ) ? $settings['tooltip_italic'] : 'off';

			// Bold text
			$settings['tooltip_bold'] = isset( $settings['tooltip_bold'] ) && $settings['tooltip_bold'] != '' ? esc_attr( $settings['tooltip_bold'] ) : 'default';

			// Tooltip text color
			$settings['tooltip_text_color'] = isset( $settings['tooltip_text_color'] ) && $settings['tooltip_text_color'] != '' ? esc_attr( $settings['tooltip_text_color'] ) : '#000000';

			// Tooltip font size
			$settings['tooltip_font_size'] = isset( $settings['tooltip_font_size'] ) && intval($settings['tooltip_font_size']) > 0 ? esc_attr( $settings['tooltip_font_size'] ) : $settings['font_size'];

			// Legend position
			$settings['legend_position'] = isset( $settings['legend_position'] ) && $settings['legend_position'] != '' ? esc_attr( $settings['legend_position'] ) : 'right';

			// Legend alignment
			$settings['legend_alignment'] = isset( $settings['legend_alignment'] ) && $settings['legend_alignment'] != '' ? esc_attr( $settings['legend_alignment'] ) : 'start';

			// Legend font color
			$settings['legend_color'] = isset( $settings['legend_color'] ) && $settings['legend_color'] != '' ? esc_attr( $settings['legend_color'] ) : '#000000';

			// Legend font size
			$settings['legend_font_size'] = isset( $settings['legend_font_size'] ) && intval($settings['legend_font_size']) > 0 ? esc_attr( $settings['legend_font_size'] ) : $settings['font_size'];

			// Legend Italic text
			$settings['legend_italic'] = ( isset( $settings['legend_italic'] ) && $settings['legend_italic'] != '' ) ? $settings['legend_italic'] : 'off';

			// Legend Bold text
			$settings['legend_bold'] = ( isset( $settings['legend_bold'] ) && $settings['legend_bold'] != '' ) ? $settings['legend_bold'] : 'off';

			// Opacity
			$settings['opacity'] = isset( $settings['opacity'] ) && $settings['opacity'] != '' ? esc_attr( $settings['opacity'] ) : '1.0';
			
			// Group width
			$settings['group_width'] = isset( $settings['group_width'] ) && $settings['group_width'] != '' ? esc_attr( $settings['group_width'] ) : '61.8';
			$settings['group_width_format'] = isset( $settings['group_width_format'] ) && $settings['group_width_format'] != '' ? esc_attr( $settings['group_width_format'] ) : '%';

			// Show chart description
			$settings['show_description'] = isset( $settings['show_description'] ) && $settings['show_description'] != '' ? esc_attr( $settings['show_description'] ) : 'on';
			
			// Show chart title
			$settings['show_title'] = isset( $settings['show_title'] ) && $settings['show_title'] != '' ? esc_attr( $settings['show_title'] ) : 'on';
			
			// Enable interactivity
			$settings['enable_interactivity'] = isset( $settings['enable_interactivity'] ) && $settings['enable_interactivity'] != '' ? esc_attr( $settings['enable_interactivity'] ) : 'on';

			// Maximized view
			$settings['maximized_view'] = ( isset( $settings['maximized_view'] ) && $settings['maximized_view'] != '' ) ? $settings['maximized_view'] : 'off';

			// Multiple data selection
			$settings['multiple_selection'] = ( isset( $settings['multiple_selection'] ) && $settings['multiple_selection'] != '' ) ? $settings['multiple_selection'] : 'off';

			// Point shape
			$settings['point_shape'] = isset( $settings['point_shape'] ) && $settings['point_shape'] != '' ? esc_attr( $settings['point_shape'] ) : 'circle';
			
			// Point size
			$settings['point_size'] = isset( $settings['point_size'] ) && $settings['point_size'] != '' ? absint(esc_attr( $settings['point_size'] )) : '0';
			
			// Crosshair trigger
			$settings['crosshair_trigger'] = isset( $settings['crosshair_trigger'] ) && $settings['crosshair_trigger'] != '' ? esc_attr( $settings['crosshair_trigger'] ) : 'none';
			
			// Crosshair orientation
			$settings['crosshair_orientation'] = isset( $settings['crosshair_orientation'] ) && $settings['crosshair_orientation'] != '' ? esc_attr( $settings['crosshair_orientation'] ) : 'both';
			
			// Crosshair opacity
			$settings['crosshair_opacity'] = isset( $settings['crosshair_opacity'] ) && $settings['crosshair_opacity'] != '' ? esc_attr( $settings['crosshair_opacity'] ) : '1.0';

			// Dash style
			$settings['dash_style'] = isset( $settings['dash_style'] ) && $settings['dash_style'] != '' ? esc_attr( str_replace(' ', '', $settings['dash_style']) ) : '';

			// Orientation
			$settings['orientation'] = ( isset( $settings['orientation'] ) && $settings['orientation'] != '' ) ? $settings['orientation'] : 'off';

			// Fill nulls
			$settings['fill_nulls'] = ( isset( $settings['fill_nulls'] ) && $settings['fill_nulls'] != '' ) ? $settings['fill_nulls'] : 'off';

			// Font size for org chart
			$settings['org_chart_font_size'] = isset( $settings['org_chart_font_size'] ) && $settings['org_chart_font_size'] != '' ? esc_attr( $settings['org_chart_font_size'] ) : 'medium';

			// Allow collapse
			$settings['allow_collapse'] = ( isset( $settings['allow_collapse'] ) && $settings['allow_collapse'] != '' ) ? $settings['allow_collapse'] : 'off';
			
			// Donut hole size
			$settings['donut_hole_size'] = isset( $settings['donut_hole_size'] ) && $settings['donut_hole_size'] != '' ? esc_attr( $settings['donut_hole_size'] ) : '0.4';

			// Org custom css class
			$settings['org_classname'] = isset( $settings['org_classname'] ) && $settings['org_classname'] != '' ? esc_attr( $settings['org_classname'] ) : '';
		
			$settings['org_node_background_color'] = isset( $settings['org_node_background_color'] ) && $settings['org_node_background_color'] != '' ? esc_attr( $settings['org_node_background_color'] ) : '#edf7ff';
			$settings['org_node_padding'] = isset( $settings['org_node_padding'] ) && $settings['org_node_padding'] != '' ? esc_attr( $settings['org_node_padding'] ) : '2';
			$settings['org_node_border_radius'] = isset( $settings['org_node_border_radius'] ) && $settings['org_node_border_radius'] != '' ? esc_attr( $settings['org_node_border_radius'] ) : '5';
			$settings['org_node_border_width'] = isset( $settings['org_node_border_width'] ) && $settings['org_node_border_width'] != '' ? esc_attr( $settings['org_node_border_width'] ) : '0';
			$settings['org_node_border_color'] = isset( $settings['org_node_border_color'] ) && $settings['org_node_border_color'] != '' ? esc_attr( $settings['org_node_border_color'] ) : '#b5d9ea';
			$settings['org_node_text_color'] = isset( $settings['org_node_text_color'] ) && $settings['org_node_text_color'] != '' ? esc_attr( $settings['org_node_text_color'] ) : '#000000';
			$settings['org_node_text_font_size'] = isset( $settings['org_node_text_font_size'] ) && $settings['org_node_text_font_size'] != '' ? esc_attr( $settings['org_node_text_font_size'] ) : '13';
			$settings['org_node_description_font_color'] = isset( $settings['org_node_description_font_color'] ) && $settings['org_node_description_font_color'] != '' ? esc_attr( $settings['org_node_description_font_color'] ) : '#ff0000';
			$settings['org_node_description_font_size'] = isset( $settings['org_node_description_font_size'] ) && $settings['org_node_description_font_size'] != '' ? esc_attr( $settings['org_node_description_font_size'] ) : '13';
			
			// Org custom selected css class
			$settings['org_selected_classname'] = isset( $settings['org_selected_classname'] ) && $settings['org_selected_classname'] != '' ? esc_attr( $settings['org_selected_classname'] ) : '';

			$settings['org_selected_node_background_color'] = isset( $settings['org_selected_node_background_color'] ) && $settings['org_selected_node_background_color'] != '' ? esc_attr( $settings['org_selected_node_background_color'] ) : '#fff7ae';
			$settings['org_selected_node_text_color'] = isset( $settings['org_selected_node_text_color'] ) && $settings['org_selected_node_text_color'] != '' ? esc_attr( $settings['org_selected_node_text_color'] ) : $settings['org_node_text_color'];

			
			// Horizontal axis settings
			$settings['haxis_title'] = isset( $settings['haxis_title'] ) && $settings['haxis_title'] != '' ? esc_attr( $settings['haxis_title'] ) : '';
			$settings['haxis_label_font_size'] = isset( $settings['haxis_label_font_size'] ) && $settings['haxis_label_font_size'] != '' ? esc_attr( $settings['haxis_label_font_size'] ) : $settings['font_size'];
			$settings['haxis_label_color'] = isset( $settings['haxis_label_color'] ) && $settings['haxis_label_color'] != '' ? esc_attr( $settings['haxis_label_color'] ) : '#000000';
			$settings['haxis_text_position'] = isset( $settings['haxis_text_position'] ) && $settings['haxis_text_position'] != '' ? esc_attr( $settings['haxis_text_position'] ) : 'out';
			$settings['haxis_direction'] = ( isset( $settings['haxis_direction'] ) && $settings['haxis_direction'] != '' ) ? $settings['haxis_direction'] : '1';
			$settings['haxis_text_color'] = isset( $settings['haxis_text_color'] ) && $settings['haxis_text_color'] != '' ? esc_attr( $settings['haxis_text_color'] ) : '#000000';
			$settings['haxis_baseline_color'] = isset( $settings['haxis_baseline_color'] ) && $settings['haxis_baseline_color'] != '' ? esc_attr( $settings['haxis_baseline_color'] ) : '#000000';
			$settings['haxis_text_font_size'] = isset( $settings['haxis_text_font_size'] ) && $settings['haxis_text_font_size'] != '' ? absint(esc_attr( $settings['haxis_text_font_size'] )) : $settings['font_size'];
			$settings['haxis_slanted'] = isset( $settings['haxis_slanted'] ) && $settings['haxis_slanted'] != '' ? esc_attr( $settings['haxis_slanted'] ) : 'automatic';
			$settings['haxis_slanted_text_angle'] = isset( $settings['haxis_slanted_text_angle'] ) && $settings['haxis_slanted_text_angle'] != '' && $settings['haxis_slanted_text_angle'] != '0' ? esc_attr( $settings['haxis_slanted_text_angle'] ) : '30';
			$settings['haxis_show_text_every'] = isset( $settings['haxis_show_text_every'] ) && $settings['haxis_show_text_every'] != '' ? esc_attr( $settings['haxis_show_text_every'] ) : '0';
			$settings['haxis_format'] = isset( $settings['haxis_format'] ) && $settings['haxis_format'] != '' ? esc_attr( $settings['haxis_format'] ) : '';
			$settings['haxis_max_value'] = isset( $settings['haxis_max_value'] ) && $settings['haxis_max_value'] != '' ? esc_attr( $settings['haxis_max_value'] ) : null;
			$settings['haxis_min_value'] = isset( $settings['haxis_min_value'] ) && $settings['haxis_min_value'] != '' ? esc_attr( $settings['haxis_min_value'] ) : null;
			$settings['haxis_gridlines_count'] = isset( $settings['haxis_gridlines_count'] ) && $settings['haxis_gridlines_count'] != '' ? esc_attr( $settings['haxis_gridlines_count'] ) : -1;
			$settings['haxis_italic'] = ( isset( $settings['haxis_italic'] ) && $settings['haxis_italic'] != '' ) ? $settings['haxis_italic'] : 'off';
			$settings['haxis_bold'] = ( isset( $settings['haxis_bold'] ) && $settings['haxis_bold'] != '' ) ? $settings['haxis_bold'] : 'off';
			$settings['haxis_title_italic'] = ( isset( $settings['haxis_title_italic'] ) && $settings['haxis_title_italic'] != '' ) ? $settings['haxis_title_italic'] : 'off';
			$settings['haxis_title_bold'] = ( isset( $settings['haxis_title_bold'] ) && $settings['haxis_title_bold'] != '' ) ? $settings['haxis_title_bold'] : 'off';
			$settings['haxis_gridlines_color'] = isset( $settings['haxis_gridlines_color'] ) && $settings['haxis_gridlines_color'] != '' ? esc_attr( $settings['haxis_gridlines_color'] ) : '#cccccc';
			$settings['haxis_minor_gridlines_color'] = isset( $settings['haxis_minor_gridlines_color'] ) && $settings['haxis_minor_gridlines_color'] != '' ? esc_attr( $settings['haxis_minor_gridlines_color'] ) : $settings['haxis_gridlines_color'];

			// Vertical axis settings
			$settings['vaxis_title'] = isset( $settings['vaxis_title'] ) && $settings['vaxis_title'] != '' ? esc_attr( $settings['vaxis_title'] ) : '';
			$settings['vaxis_label_font_size'] = isset( $settings['vaxis_label_font_size'] ) && $settings['vaxis_label_font_size'] != '' ? esc_attr( $settings['vaxis_label_font_size'] ) : $settings['font_size'];
			$settings['vaxis_label_color'] = isset( $settings['vaxis_label_color'] ) && $settings['vaxis_label_color'] != '' ? esc_attr( $settings['vaxis_label_color'] ) : '#000000';
			$settings['vaxis_text_position'] = isset( $settings['vaxis_text_position'] ) && $settings['vaxis_text_position'] != '' ? esc_attr( $settings['vaxis_text_position'] ) : 'out';
			$settings['vaxis_direction'] = ( isset( $settings['vaxis_direction'] ) && $settings['vaxis_direction'] != '' ) ? $settings['vaxis_direction'] : '1';
			$settings['vaxis_text_color'] = isset( $settings['vaxis_text_color'] ) && $settings['vaxis_text_color'] != '' ? esc_attr( $settings['vaxis_text_color'] ) : '#000000';
			$settings['vaxis_baseline_color'] = isset( $settings['vaxis_baseline_color'] ) && $settings['vaxis_baseline_color'] != '' ? esc_attr( $settings['vaxis_baseline_color'] ) : '#000000';
			$settings['vaxis_text_font_size'] = isset( $settings['vaxis_text_font_size'] ) && $settings['vaxis_text_font_size'] != '' ? absint(esc_attr( $settings['vaxis_text_font_size'] )) : $settings['font_size'];
			$settings['vaxis_format'] = isset( $settings['vaxis_format'] ) && $settings['vaxis_format'] != '' ? esc_attr( $settings['vaxis_format'] ) : '';
			$settings['vaxis_max_value'] = isset( $settings['vaxis_max_value'] ) && $settings['vaxis_max_value'] != '' ? esc_attr( $settings['vaxis_max_value'] ) : null;
			$settings['vaxis_min_value'] = isset( $settings['vaxis_min_value'] ) && $settings['vaxis_min_value'] != '' ? esc_attr( $settings['vaxis_min_value'] ) : null;
			$settings['vaxis_gridlines_count'] = isset( $settings['vaxis_gridlines_count'] ) && $settings['vaxis_gridlines_count'] != '' ? esc_attr( $settings['vaxis_gridlines_count'] ) : -1;
			$settings['vaxis_italic'] = ( isset( $settings['vaxis_italic'] ) && $settings['vaxis_italic'] != '' ) ? $settings['vaxis_italic'] : 'off';
			$settings['vaxis_bold'] = ( isset( $settings['vaxis_bold'] ) && $settings['vaxis_bold'] != '' ) ? $settings['vaxis_bold'] : 'off';
			$settings['vaxis_title_italic'] = ( isset( $settings['vaxis_title_italic'] ) && $settings['vaxis_title_italic'] != '' ) ? $settings['vaxis_title_italic'] : 'off';
			$settings['vaxis_title_bold'] = ( isset( $settings['vaxis_title_bold'] ) && $settings['vaxis_title_bold'] != '' ) ? $settings['vaxis_title_bold'] : 'off';
			$settings['vaxis_gridlines_color'] = isset( $settings['vaxis_gridlines_color'] ) && $settings['vaxis_gridlines_color'] != '' ? esc_attr( $settings['vaxis_gridlines_color'] ) : '#cccccc';
			$settings['vaxis_minor_gridlines_color'] = isset( $settings['vaxis_minor_gridlines_color'] ) && $settings['vaxis_minor_gridlines_color'] != '' ? esc_attr( $settings['vaxis_minor_gridlines_color'] ) : $settings['vaxis_gridlines_color'];

			// Animation settings
			$settings['enable_animation'] = ( isset( $settings['enable_animation'] ) && $settings['enable_animation'] != '' ) ? $settings['enable_animation'] : 'off';
			$settings['animation_duration'] = isset( $settings['animation_duration'] ) && $settings['animation_duration'] != '' ? absint(esc_attr( $settings['animation_duration'] )) : '1000';
			$settings['animation_startup'] = ( isset( $settings['animation_startup'] ) && $settings['animation_startup'] != '' ) ? $settings['animation_startup'] : 'on';
			$settings['animation_easing'] = isset( $settings['animation_easing'] ) && $settings['animation_easing'] != '' ? esc_attr( $settings['animation_easing'] ) : 'linear';

			$settings['enable_img'] = ( isset( $settings['enable_img'] ) && $settings['enable_img'] != '' ) ? $settings['enable_img'] : 'off';

			$count_slices = (isset($chartData['source']) && !is_null($chartData['source']) && count($chartData['source']) > 0) ? count($chartData['source']) - 1 : 0;
			$count_series = (isset($chartData['source'][0]) && !is_null($chartData['source'][0]) && count($chartData['source'][0]) > 0) ? count($chartData['source'][0]) - 1 : 0;
			$count_rows = (isset($chartData['source']) && !is_null($chartData['source']) && count($chartData['source']) > 0) ? count(array_column($chartData['source'], 0)) - 1 : 0;

			// Slices settings
			$settings['slice_colors_default'] = $chart_default_colors;
			$settings['slice_color'] = isset( $settings['slice_color'] ) && $settings['slice_color'] != '' ? json_decode($settings['slice_color'], true) : $chart_default_colors;
			$settings['slice_offset'] = isset( $settings['slice_offset'] ) && $settings['slice_offset'] != '' ? json_decode($settings['slice_offset'], true) : array_fill(0, $count_slices, 0);
			$settings['slice_text_color'] = isset( $settings['slice_text_color'] ) && $settings['slice_text_color'] != '' ? json_decode($settings['slice_text_color'], true) : array_fill(0, $count_slices, '#ffffff');
			
			// Series settings
			$settings['series_colors_default'] = $chart_default_colors;
			$settings['series_color'] = isset( $settings['series_color'] ) && $settings['series_color'] != '' ? json_decode($settings['series_color'], true) : $chart_default_colors;
			$settings['series_visible_in_legend'] = isset( $settings['series_visible_in_legend'] ) && $settings['series_visible_in_legend'] != '' ? json_decode($settings['series_visible_in_legend'], true) : array_fill(0, $count_series, 'on');
			$settings['series_line_width'] = isset( $settings['series_line_width'] ) && $settings['series_line_width'] != '' ? json_decode($settings['series_line_width'], true) : array_fill(0, $count_series, $settings['line_width']);
			$settings['series_point_size'] = isset( $settings['series_point_size'] ) && $settings['series_point_size'] != '' ? json_decode($settings['series_point_size'], true) : array_fill(0, $count_series, $settings['point_size']);
			$settings['series_point_shape'] = isset( $settings['series_point_shape'] ) && $settings['series_point_shape'] != '' ? json_decode($settings['series_point_shape'], true) : array_fill(0, $count_series, $settings['point_shape']);

			// Rows settings
			$settings['enable_row_settings'] = ( isset( $settings['enable_row_settings'] ) && $settings['enable_row_settings'] != '' ) ? $settings['enable_row_settings'] : 'on';
			
			$settings['rows_color'] = isset( $settings['rows_color'] ) && $settings['rows_color'] != '' ? json_decode($settings['rows_color'], true) : array_fill(0, $count_rows, '');
			$settings['rows_opacity'] = isset( $settings['rows_opacity'] ) && $settings['rows_opacity'] != '' ? json_decode($settings['rows_opacity'], true) : array_fill(0, $count_rows, 1.0);

			return $settings;

		}

		/**
	     * Gets all settings of chart.js charts for admin area.
	     *
	     * @access public
	     * @return array
	    */
		public function get_chart_settings_chartjs_admin ($settings) {
			$title_positions = array(
				"left" => __("Left", "chart-builder"),
				"right" => __("Right", "chart-builder"),
				"center" => __("Center", "chart-builder")
			);
		
			$text_transforms = array(
				"uppercase" => __("Uppercase", "chart-builder"),
				"lowercase" => __("Lowercase", "chart-builder"),
				"capitalize" => __("Capitalize", "chart-builder"),
				"none" => __("None", "chart-builder"),
			);
		
			$text_decorations = array(
				"overline" => __("Overline", "chart-builder"),
				"underline" => __("Underline", "chart-builder"),
				"line-through" => __("Line through", "chart-builder"),
				"none" => __("None", "chart-builder"),
			);

			// Title color
			$settings['title_color'] = isset( $settings['title_color'] ) && $settings['title_color'] != '' ? esc_attr( $settings['title_color'] ) : '#000000';

			// Title color
			$settings['title_shadow_color'] = isset( $settings['title_shadow_color'] ) && $settings['title_shadow_color'] != '' ? esc_attr( $settings['title_shadow_color'] ) : $settings['title_color'];

			// Title font size
			$settings['title_font_size'] = isset( $settings['title_font_size'] ) && $settings['title_font_size'] != '' ? esc_attr( $settings['title_font_size'] ) : '30';

			// Title Bold text
			$settings['title_bold'] = ( isset( $settings['title_bold'] ) && $settings['title_bold'] != '' ) ? esc_attr($settings['title_bold']) : 'on';
			$settings['title_bold'] = isset( $settings['title_bold'] ) && $settings['title_bold'] == 'on' ? 'checked' : '';

			// Title text shadow
			$settings['title_text_shadow'] = ( isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] != '' ) ? esc_attr($settings['title_text_shadow']) : 'off';
			$settings['title_text_shadow'] = isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] == 'on' ? 'checked' : '';

			// Title italic text
			$settings['title_italic'] = ( isset( $settings['title_italic'] ) && $settings['title_italic'] != '' ) ? esc_attr($settings['title_italic']) : 'off';
			$settings['title_italic'] = isset( $settings['title_italic'] ) && $settings['title_italic'] == 'on' ? 'checked' : '';

			// Title gap
			$settings['title_gap'] = isset( $settings['title_gap'] ) && $settings['title_gap'] != '' ? esc_attr( $settings['title_gap'] ) : '5';

			// Title gap
			$settings['title_gap_description'] = isset( $settings['title_gap_description'] ) && $settings['title_gap_description'] != '' ? esc_attr( $settings['title_gap_description'] ) : '5';

			// Title letter spacing
			$settings['title_letter_spacing'] = isset( $settings['title_letter_spacing'] ) && $settings['title_letter_spacing'] != '' ? esc_attr( $settings['title_letter_spacing'] ) : 0;

			// Title position
			$settings['title_position'] = isset( $settings['title_position'] ) && $settings['title_position'] != '' ? esc_attr( $settings['title_position'] ) : 'left';
			$settings['title_positions'] = $title_positions;

			// Title text transform
			$settings['title_text_transform'] = isset( $settings['title_text_transform'] ) && $settings['title_text_transform'] != '' ? esc_attr( $settings['title_text_transform'] ) : 'none';
			$settings['text_transforms'] = $text_transforms;

			// Title text decoration
			$settings['title_text_decoration'] = isset( $settings['title_text_decoration'] ) && $settings['title_text_decoration'] != '' ? esc_attr( $settings['title_text_decoration'] ) : 'none';
			$settings['text_decorations'] = $text_decorations;

			// description color
			$settings['description_color'] = isset( $settings['description_color'] ) && $settings['description_color'] != '' ? esc_attr( $settings['description_color'] ) : '#4c4c4c';
			
			// description font size
			$settings['description_font_size'] = isset( $settings['description_font_size'] ) && $settings['description_font_size'] != '' ? esc_attr( $settings['description_font_size'] ) : '16';

			// description Bold text
			$settings['description_bold'] = ( isset( $settings['description_bold'] ) && $settings['description_bold'] != '' ) ? esc_attr($settings['description_bold']) : 'off';
			$settings['description_bold'] = isset( $settings['description_bold'] ) && $settings['description_bold'] == 'on' ? 'checked' : '';

			// description text shadow
			$settings['description_text_shadow'] = ( isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] != '' ) ? esc_attr($settings['description_text_shadow']) : 'off';
			$settings['description_text_shadow'] = isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] == 'on' ? 'checked' : '';

			// description color
			$settings['description_shadow_color'] = isset( $settings['description_shadow_color'] ) && $settings['description_shadow_color'] != '' ? esc_attr( $settings['description_shadow_color'] ) : $settings['description_color'];

			// description italic text
			$settings['description_italic'] = ( isset( $settings['description_italic'] ) && $settings['description_italic'] != '' ) ? esc_attr($settings['description_italic']) : 'off';
			$settings['description_italic'] = isset( $settings['description_italic'] ) && $settings['description_italic'] == 'on' ? 'checked' : '';

			// description position
			$settings['description_position'] = isset( $settings['description_position'] ) && $settings['description_position'] != '' ? esc_attr( $settings['description_position'] ) : 'left';

			// description text transform
			$settings['description_text_transform'] = isset( $settings['description_text_transform'] ) && $settings['description_text_transform'] != '' ? esc_attr( $settings['description_text_transform'] ) : 'none';

			// description letter spacing
			$settings['description_letter_spacing'] = isset( $settings['description_letter_spacing'] ) && $settings['description_letter_spacing'] != '' ? esc_attr( $settings['description_letter_spacing'] ) : 0;

			// description text decoration
			$settings['description_text_decoration'] = isset( $settings['description_text_decoration'] ) && $settings['description_text_decoration'] != '' ? esc_attr( $settings['description_text_decoration'] ) : 'none';

			// outer_radius
			$settings['outer_radius'] = isset( $settings['outer_radius'] ) && $settings['outer_radius'] != '' ? esc_attr( absint($settings['outer_radius']) ) : 100;

			// slice_spacing
			$settings['slice_spacing'] = isset( $settings['slice_spacing'] ) && $settings['slice_spacing'] != '' ? esc_attr( absint($settings['slice_spacing']) ) : 1;

			// circumference
			$settings['circumference'] = isset( $settings['circumference'] ) && $settings['circumference'] != '' ? esc_attr( absint($settings['circumference']) ) : 360;

			// start_angle
			$settings['start_angle'] = isset( $settings['start_angle'] ) && $settings['start_angle'] != '' ? esc_attr( absint($settings['start_angle']) ) : 0;

			// Show chart description
			if (!isset($settings['show_description'])) {
				$settings['show_description'] = 'checked';
			} else {
				$settings['show_description'] = ( $settings['show_description'] != '' ) ? $settings['show_description'] : 'off';
				$settings['show_description'] = isset( $settings['show_description'] ) && $settings['show_description'] == 'on' ? 'checked' : '';
			}
			
			// Show chart title
			if (!isset($settings['show_title'])) {
				$settings['show_title'] = 'checked';
			} else {
				$settings['show_title'] = ( $settings['show_title'] != '' ) ? $settings['show_title'] : 'off';
				$settings['show_title'] = isset( $settings['show_title'] ) && $settings['show_title'] == 'on' ? 'checked' : '';
			}
			
			return $settings;

		}
		
		/**
	     * Gets all settings of chart.js charts for public area.
	     *
	     * @access public
	     * @return array
	    */
		public function get_chart_settings_chartjs_public ($settings) {
			// Show chart description
			$settings['show_description'] = isset( $settings['show_description'] ) && $settings['show_description'] != '' ? esc_attr( $settings['show_description'] ) : 'on';
			
			// Show chart title
			$settings['show_title'] = isset( $settings['show_title'] ) && $settings['show_title'] != '' ? esc_attr( $settings['show_title'] ) : 'on';
			
			// Title color
			$settings['title_color'] = isset( $settings['title_color'] ) && $settings['title_color'] != '' ? esc_attr( $settings['title_color'] ) : '#000000';

			// Title color
			$settings['title_shadow_color'] = isset( $settings['title_shadow_color'] ) && $settings['title_shadow_color'] != '' ? esc_attr( $settings['title_shadow_color'] ) : $settings['title_color'];
			
			// Title font size
			$settings['title_font_size'] = isset( $settings['title_font_size'] ) && $settings['title_font_size'] != '' ? esc_attr( $settings['title_font_size'] ) : '30';

			// title bold
			$settings['title_bold'] = ( isset( $settings['title_bold'] ) && $settings['title_bold'] != '' ) ? esc_attr($settings['title_bold']) : 'on';
			$settings['title_bold'] = isset( $settings['title_bold'] ) && $settings['title_bold'] != 'off'? 'bold' : 'normal';

			// Title text shadow
			$settings['title_text_shadow'] = ( isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] != '' ) ? esc_attr($settings['title_text_shadow']) : 'off';
			$settings['title_text_shadow'] = isset( $settings['title_text_shadow'] ) && $settings['title_text_shadow'] == 'on' ? 'checked' : '';

			// title italic
			$settings['title_italic'] = ( isset( $settings['title_italic'] ) && $settings['title_italic'] != '' ) ? esc_attr($settings['title_italic']) : 'off';
			$settings['title_italic'] = isset( $settings['title_italic'] ) && $settings['title_italic'] != 'on'? 'normal' : 'italic';

			// title gap
			$settings['title_gap'] = (isset( $settings['title_gap'] ) && $settings['title_gap'] != '') ? esc_attr( $settings['title_gap'] ) : '5';

			// title gap
			$settings['title_gap_description'] = (isset( $settings['title_gap_description'] ) && $settings['title_gap_description'] != '') ? esc_attr( $settings['title_gap_description'] ) : '5';

			// title letter spacing
			$settings['title_letter_spacing'] = (isset( $settings['title_letter_spacing'] ) && $settings['title_letter_spacing'] != '') ? esc_attr( $settings['title_letter_spacing'] ) : 0;

			// title position
			$settings['title_position'] = isset( $settings['title_position'] ) && $settings['title_position'] != '' ? esc_attr( $settings['title_position'] ) : 'left';

			// title text transform
			$settings['title_text_transform'] = isset( $settings['title_text_transform'] ) && $settings['title_text_transform'] != '' ? esc_attr( $settings['title_text_transform'] ) : 'none';

			// title text decoration
			$settings['title_text_decoration'] = isset( $settings['title_text_decoration'] ) && $settings['title_text_decoration'] != '' ? esc_attr( $settings['title_text_decoration'] ) : 'none';

			// description color
			$settings['description_color'] = isset( $settings['description_color'] ) && $settings['description_color'] != '' ? esc_attr( $settings['description_color'] ) : '#4c4c4c';

			// description font size
			$settings['description_font_size'] = (isset( $settings['description_font_size'] ) && $settings['description_font_size'] != '') ? esc_attr( $settings['description_font_size'] ) : '16';
			
			// description Bold text
			$settings['description_bold'] = ( isset( $settings['description_bold'] ) && $settings['description_bold'] != '' ) ? esc_attr($settings['description_bold']) : 'off';
			$settings['description_bold'] = isset( $settings['description_bold'] ) && $settings['description_bold'] != 'on'? 'normal' : 'bold';

			// description text shadow
			$settings['description_text_shadow'] = ( isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] != '' ) ? esc_attr($settings['description_text_shadow']) : 'off';
			$settings['description_text_shadow'] = isset( $settings['description_text_shadow'] ) && $settings['description_text_shadow'] == 'on' ? 'checked' : '';

			// description color
			$settings['description_shadow_color'] = isset( $settings['description_shadow_color'] ) && $settings['description_shadow_color'] != '' ? esc_attr( $settings['description_shadow_color'] ) : $settings['description_color'];

			// desctiption italic text
			$settings['description_italic'] = ( isset( $settings['description_italic'] ) && $settings['description_italic'] != '' ) ? esc_attr($settings['description_italic']) : 'off';
			$settings['description_italic'] = isset( $settings['description_italic'] ) && $settings['description_italic'] != 'on'? 'normal' : 'italic';

			// description position
			$settings['description_position'] = isset( $settings['description_position'] ) && $settings['description_position'] != '' ? esc_attr( $settings['description_position'] ) : 'left';

			// description text transform
			$settings['description_text_transform'] = isset( $settings['description_text_transform'] ) && $settings['description_text_transform'] != '' ? esc_attr( $settings['description_text_transform'] ) : 'none';

			// description letter spacing
			$settings['description_letter_spacing'] = isset( $settings['description_letter_spacing'] ) && $settings['description_letter_spacing'] != '' ? esc_attr( $settings['description_letter_spacing'] ) : 0;

			// description text decoration
			$settings['description_text_decoration'] = isset( $settings['description_text_decoration'] ) && $settings['description_text_decoration'] != '' ? esc_attr( $settings['description_text_decoration'] ) : 'none';

			// outer_radius
			$settings['outer_radius'] = isset( $settings['outer_radius'] ) && $settings['outer_radius'] != '' ? esc_attr( absint($settings['outer_radius']) ) : 100;
			
			// slice_spacing
			$settings['slice_spacing'] = isset( $settings['slice_spacing'] ) && $settings['slice_spacing'] != '' ? esc_attr( absint($settings['slice_spacing']) ) : 1;
			
			// circumference
			$settings['circumference'] = isset( $settings['circumference'] ) && $settings['circumference'] != '' ? esc_attr( absint($settings['circumference']) ) : 360;
			
			// start_angle
			$settings['start_angle'] = isset( $settings['start_angle'] ) && $settings['start_angle'] != '' ? esc_attr( absint($settings['start_angle']) ) : 0;
			
			return $settings;

		}
    }
}

if( ! function_exists( 'CBFunctions' ) ){
	/**
	 * Function for quick access to Chart_Builder_Functions class
	 *
	 * @since   1.0.0
	 * @return  Chart_Builder_Functions
	 */
	function CBFunctions(){

        static $instance = null;

        if( $instance === null ){
            $instance = Chart_Builder_Functions::get_instance( CHART_BUILDER_NAME );
        }

        if( $instance instanceof Chart_Builder_Functions ){
	        return $instance;
        }

		return Chart_Builder_Functions::get_instance( CHART_BUILDER_NAME );
	}
}