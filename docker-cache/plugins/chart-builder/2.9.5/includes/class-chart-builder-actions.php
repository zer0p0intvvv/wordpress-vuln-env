<?php

if( ! class_exists( 'Chart_Builder_Actions' ) ){

    /**
     * Class Chart_Builder_Actions
     * Class contains chart data filtering and hooks related to chart
     *
     * Main functionality belong to chart source and source type
     * Also chart settings and options
     *
     * Hooks used in the class
     *
     * @hooks           @actions        ays_chart_get_chart_data
     *
     *                  @filters        ays_chart_source_type
     *                                  ays_chart_source
     *                                  ays_chart_source_{source_type}
     *                                  ays_chart_get_settings
     *                                  ays_chart_get_options
     *                                  ays_chart_get_title
     *                                  ays_chart_get_type
     *                                  ays_chart_get_status
     *
     * @param           $plugin_name
     *
     * @since           1.0.0
     * @package         Chart_Builder
     * @subpackage      Chart_Builder/includes
     * @author          Chart Builder Team <info@ays-pro.com>
     */
    class Chart_Builder_Actions {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The object of the database.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $db_obj    The database object of the chart.
         */
        private $db_obj;

        /**
         * The constructor of the class
         *
         * @since   1.0.0
         *
         * @param   $plugin_name
         */
        public function __construct( $plugin_name ) {

	        /**
	         * Assigning $plugin_name to the @plugin_name property
	         */
            $this->plugin_name = $plugin_name;

	        /**
	         * Assigning database object to the @db_obj property
	         *
	         * Creating instance of Chart_Builder_DB_Actions class
	         */
            $this->db_obj = new Chart_Builder_DB_Actions( $this->plugin_name );
        }

        /**
         * Get instance of this class
         *
         * @since   1.0.0
         * @access  public
         *
         * @param   $plugin_name
         *
         * @return  Chart_Builder_Actions
         */
        public static function get_instance( $plugin_name ){
            return new self( $plugin_name );
        }

        /**
         * Get chart data by id
         *
         * @since   1.0.0
         * @access  public
         *
         * @param   $id
         *
         * @return  null|non-empty-array
         */
        public function get_chart_data( $id ){

            /**
             * Checking if given valid @id
             *
             * @return null if not valid
             */
            if( is_null( $id ) || intval( $id ) === 0 ){
                return null;
            }

            /**
             * Retrieving chart row from database
             *
             * Passing @id as a parameter
             */
            $chart = $this->db_obj->get_item( $id );
            if ( !$chart ) {
                return null;
            }


            /**
             * Validate chart general data by calling validation function
             *
             * Function must @return array
             */
            $chart = $this->validate_item_data( $chart );


            /**
             * Getting source chart type from @chart object
             */
            $source_chart_type = $chart['source_chart_type'];
            $source_chart_type = apply_filters( 'ays_chart_source_chart_type', $source_chart_type );


            /**
             * Getting source type from @chart object
             */
            $source_type = $chart['source_type'];


            /**
             * Applying filter to source type for development purposes
             *
             * @accept manual, custom, etc.
             */
            $source_type = apply_filters( 'ays_chart_source_type', $source_type );

            /**
             * Getting source from @chart object
             */
            $source = $chart['source'];

            /**
             * Getting chart author id
             */
            $author_id = $chart['author_id'];

            /**
             * Defining filter function name that will filter source data
             */
            $filter_function = 'chart_data_' . $source_type;


            /**
             * Calling filter function
             *
             * Before calling checking if the function exists and is callable
             *
             * Passing @source as a parameter
             * Expecting an array which must be returned after filter
             */
            $source_type_data = array();
            if( method_exists( $this , $filter_function ) && is_callable( array( $this, $filter_function ) ) ) {
                $source_type_data = $this->$filter_function( $source, $id );
            }

            /**
             * Checking that returning data was array
             */
            if( ! is_array( $source_type_data ) ){
                $source_type_data = array();
            }

            /**
             * Checking availability of data in the returned value from filter function
             */
            if( empty( $source_type_data ) ){
                $source_type_data = array();
            }

            /**
             * Final source after validations and filters
             * And final filter that applies to the source for giving access to change the data after all
             *
             * Applying filter to @source for development purposes
             *
             * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
             * Please don't use this filter often when you need to change the source
             * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
             */
            $final_source = apply_filters( 'ays_chart_source', $source_type_data, $source_type );


            /**
             * Getting @settings data from database by calling get_metadata function from db object
             *
             * get_metadata function must return an array that contains chart @settings
             */
            $chart_settings = $this->db_obj->get_metadata( $id );

            /**
             * Settings mustn't be empty or not array type data
             * So checking is array and not empty
             *
             * If validation not passed assigning empty array
             */
            if( ! is_array( $chart_settings ) || empty( $chart_settings ) ){
                $chart_settings = array();
            }

            /**
             * Converting @settings data to usable format
             *
             * convert_metadata function must return an array that contains formatted chart @settings
             */
            $chart_settings = $this->db_obj->convert_metadata( $chart_settings );

            /**
             * Applying defaults to @settings
             *
             * apply_default_metadata function must return an array
             * Expecting @settings with default values applied
             */
            $chart_settings = $this->db_obj->apply_default_metadata( $chart_settings );


            /**
             * Applying filter to @settings data for development purposes
             */
            $chart_settings = apply_filters( 'ays_chart_get_settings', $chart_settings );


            /**
             * Getting @options from @chart object
             */
            $chart_options = $chart['options'];

            /**
             * After getting @options from @chart object
             */
            unset( $chart['options'] );

            /**
             * Applying filter to @options for development purposes
             */
            $chart_options = apply_filters( 'ays_chart_get_options', $chart_options );

            /**
             * Getting quiz data
             */
            $quiz_query = $chart['quiz_query'];
            $quiz_id =  $chart['quiz_id'];

            /**
             * Collecting chart whole data into one variable
             *
             * @array $chart_data
             */
            $chart_data = array(
                'chart' => $chart,
                'author_id' => $author_id,
                'source_chart_type' => $source_chart_type,
                'source_type' => $source_type,
                'source' => $final_source,
                'settings' => $chart_settings,
                'options' => $chart_options,
                'quiz_query' => $quiz_query,
                'quiz_id' => $quiz_id,
            );

            /**
             * Action for get chart data function
             */
            do_action( 'ays_chart_get_chart_data', $chart_data );

            /**
             * Returning chart whole data
             *
             * @return $chart_data
             */
            return $chart_data;
        }

        /**
         * Chart data type manual
         * Validating the data and returning validated array
         *
         * Returning applied filter to array for development purposes
         *
         * @since   1.0.0
         * @access  protected
         *
         * @accept  JSON, empty string
         * @param   $source
         *
         * @return  array
         */
	    protected function chart_data_manual( $source, $chart_id ){

            /**
             * Defining an empty array that will return after validation
             */
            $filtered_data = array();

            /**
             * @source mustn't be empty or not string type data
             * So checking is string and not empty
             *
             * If validation not passed function will return an empty array
             */
            if( is_string( $source ) && trim( $source ) !== '' ){

                /**
                 * Data are expected to be of type JSON
                 * Trying to parse the data
                 */
                $decoded_data = json_decode( $source, true );

                /**
                 * @decoded_data mustn't be empty or not array type data
                 * So checking is array and not empty
                 */
                if( is_array( $decoded_data ) && ! empty( $decoded_data ) ){

                    /**
                     * Assigning parsed data to the @filtered_data variable
                     */
                    $filtered_data = $decoded_data;
                }

            }

            /**
             * Returning the data that has passed the validation
             * Returning applied filter to array for development purposes
             *
             * @return array
             */
            return apply_filters( 'ays_chart_source_manual', $filtered_data );
        }

        /**
         * Chart data type quiz_maker
         * Validating the data and returning validated array
         *
         * Returning applied filter to array for development purposes
         *
         * @since   1.0.0
         * @access  protected
         *
         * @accept  JSON, empty string
         * @param   $source
         *
         * @return  array
         */
	    protected function chart_data_quiz_maker( $source, $chart_id ){

            /**
             * Defining an empty array that will return after validation
             */
            $filtered_data = array();

            /**
             * @source mustn't be empty or not string type data
             * So checking is string and not empty
             *
             * If validation not passed function will return an empty array
             */
            if( is_string( $source ) && trim( $source ) !== '' ){

                /**
                 * Data are expected to be of type array
                 * Trying to parse the data
                 */
	            // $quiz_maker_data_option_name = 'ays_chart_quiz_maker_results_' . $chart_id;
	            // $quiz_maker_data = get_option( $quiz_maker_data_option_name, array() );
	            // $decoded_data = isset( $quiz_maker_data['source'] ) && $quiz_maker_data['source'] !== '' ? $quiz_maker_data['source'] : '';

                $quiz_maker_data_option_name = 'ays_chart_quiz_maker_quiz_data_' . $chart_id;
                $quiz_query_data = get_option( $quiz_maker_data_option_name );
                $quiz_query = isset( $quiz_query_data['quiz_query'] ) && $quiz_query_data['quiz_query'] !== '' ? $quiz_query_data['quiz_query'] : ''; 
                
                /**
                 * @quiz_query mustn't be empty or not array type data
                 * So checking is array and not empty
                 */
                if( $quiz_query !== '' ){

                    $quiz_id = isset( $quiz_query_data['quiz_id'] ) && $quiz_query_data['quiz_id'] !== '' ? $quiz_query_data['quiz_id'] : 0;
                    
                    $return_results = CBFunctions()->get_quiz_query( $quiz_query, $quiz_id, get_current_user_id() );
                
			        $result_values = $return_results['result'];
                
                    $results = array();
			        if ( !empty($result_values) ) {
			        	if (count($return_results['result'][0]) != 1 ) {
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
                        } else if (count($return_results['result'][0]) == 1) {
                            $results = array();
                            $headers = array();
                            if ( $result_values ) {
                                $row_num = 0;
                                foreach ( $result_values as $index => $row ) {
                                    $result = array();
                                    foreach ( $row as $k => $v ) {
                                        $result[] = strval($index) + 1;
                                        $result[] = $v;
                                        if ( $row_num === 0 ) {
                                            $headers[] = 'Quiz';
                                            $headers[] = $k;
                                        }
                                    }
                                    $results[] = $result;
                                    $row_num++;
                                }
                            }
                        }
                        array_unshift($results, $headers);
			        }

					$filtered_data = $results;
                }
            }

            /**
             * Returning the data that has passed the validation
             * Returning applied filter to array for development purposes
             *
             * @return array
             */
            return apply_filters( 'ays_chart_source_quiz_maker', $filtered_data );
        }

        /**
         * Chart data type custom
         * For developers
         * Returning data must be an array
         *
         * @since   1.0.0
         * @access  protected
         *
         * @accept  mixed
         * @param   $source
         *
         * @return  array
         */
	    protected function chart_data_custom( $source, $chart_id ){

            /**
             * Getting the data that will some developers pass via this filter
             */
            $filtered_data = apply_filters( 'ays_chart_source_custom', array(), $chart_id );

            /**
             * Checking that returning data was array
             */
            if( ! is_array( $filtered_data ) ){
                return array();
            }

            /**
             * Checking availability of data in array
             */
            if( empty( $filtered_data ) ){
                return array();
            }

            /**
             * Returning the data that has passed the validation
             *
             * @return array
             */
            return $filtered_data;
        }

        /**
         * Validation function for chart general data
         *
         * Validating title, description, type, status, etc.
         *
         * @since   1.0.0
         * @access  protected
         *
         * @param   $chart
         *
         * @return  array
         */
        protected function validate_item_data( $chart ){

            /**
             * @chart mustn't be empty or not array type data
             * So checking is array and not empty
             *
             * If validation not passed returning an empty array
             */
            if( ! is_array( $chart ) || empty( $chart ) ){
                return array();
            }

            /**
             * Defining an empty array that will return after validation
             *
             * During validation array will be filled with valid data
             */
            $chart_data = array();


            /**
             * Validating the @title
             *
             * Title mustn't be empty
             *
             * Applying esc_html filtering function to escape HTML from the title
             * Then applying filter to @title for development purposes
             *
             * @default "Chart example"
             */
            $chart_title = __( 'Chart example', "chart-builder" );
            if( isset( $chart['title'] ) && $chart['title'] !== '' ) {
                $chart_title = esc_html( $chart['title'] );
            }

            $chart_data['title'] = apply_filters( 'ays_chart_get_title', $chart_title );


            /**
             * Validating the @description
             *
             * Description is optional, so it can be empty
             * Applying stripslashes function to escape unnecessary slashes
             *
             * @default empty string
             */
            $chart_data['description'] = '';
            if( isset( $chart['description'] ) && $chart['description'] !== '' ){
                $chart_data['description'] = stripslashes( $chart['description'] );
            }

            /**
             * Validating the @author_id
             */
            $chart_data['author_id'] = ( isset( $chart['author_id'] ) && $chart['author_id'] !== '' ) ? esc_html( intval($chart['author_id']) ) : get_current_user_id();


            /**
             * Validating the @type
             *
             * Type is required, it can't be empty
             * Applying esc_html filtering function to escape HTML from the type
             *
             * Then applying filter to @type for development purposes
             *
             * @accept values are google-charts, etc.
             * @default google-charts
             */
            $chart_type = 'google-charts';
            if( isset( $chart['type'] ) && $chart['type'] !== '' ){
                $chart_type = esc_html( $chart['type'] );
            }

            $chart_data['type'] = apply_filters( 'ays_chart_get_type', $chart_type );

            /**
             * Validating the @source_chart_type
             *
             * Source chart type is required, it can't be empty
             * Applying esc_html filtering function to escape HTML from the @source_type
             *
             * @accept values are manual, file, WordPress, database, etc.
             * @default manual
             */
            $chart_data['source_chart_type'] = ( isset( $chart['source_chart_type'] ) && $chart['source_chart_type'] !== '' ) ? esc_html( $chart['source_chart_type'] ) : 'pie_chart';

            /**
             * Validating the @source_type
             *
             * Source type is required, it can't be empty
             * Applying esc_html filtering function to escape HTML from the @source_type
             *
             * @accept values are manual, file, WordPress, database, etc.
             * @default manual
             */
            $chart_data['source_type'] = 'manual';
            if( isset( $chart['source_type'] ) && $chart['source_type'] !== '' ){
                $chart_data['source_type'] = esc_html( $chart['source_type'] );
            }

            $chart_data['quiz_query'] = isset($chart['quiz_query']) && $chart['quiz_query'] != '' ? sanitize_text_field($chart['quiz_query']) : '';
            $chart_data['quiz_id'] = isset( $chart['quiz_id'] ) && $chart['quiz_id'] != '' ? intval( $chart['quiz_id'] ) : 0;

            /**
             * Validating the @source
             *
             * Source is required, but it can be empty
             * Applying esc_html filtering function to escape HTML from the @source
             *
             * @accept values are JSON, CSV, serialized data, etc.
             * @default empty string
             */
            $chart_data['source'] = '';
            if( isset( $chart['source'] ) && $chart['source'] !== '' ){
                $chart_data['source'] =  $chart['source'] ;
            }


            /**
             * Validating the @status
             *
             * Status is required, it can't be empty
             * Applying esc_html filtering function to escape HTML from the @status
             *
             * Then applying filter to type for development purposes
             *
             * @accept values are published and draft
             * @default draft
             */
            $chart_status = 'draft';
            if( isset( $chart['status'] ) && $chart['status'] !== '' ){
                $chart_status = esc_html( $chart['status'] );
            }

            $chart_data['status'] = apply_filters( 'ays_chart_get_status', $chart_status );


            /**
             * Validating the @options
             * Options is optional, it can be empty
             *
             * @accept JSON
             * @default empty string
             * @return array
             */
	        $chart_data['options'] = array();
            if( isset( $chart['options'] ) && $chart['options'] !== '' ){

                /**
                 * Data are expected to be of type JSON
                 * Trying to parse the data
                 */
                $chart_options = json_decode( $chart['options'], true );

                /**
                 * Options mustn't be empty or not array type data
                 * So checking is array and not empty
                 *
                 * If validation not passed assigning empty array
                 */
                if( ! is_array( $chart_options ) || empty( $chart_options ) ){
                    $chart_options = array();
                }

                /**
                 * Pushing the result to the returning variable
                 */
                $chart_data['options'] = $chart_options;
            }

            /**
             * Returning validated data
             * Returning an array
             *
             * @return array
             */
            return $chart_data;
        }

        /** 
         * Returns default array for google charts
         */ 
        public function get_charts_default_data_google(){
            return  array(
                "commonTypeCharts" => array(
                    '0' => array(
                        'Country',
                        'Population',
                    ),   
                    '1' => array(
                        'United States',
                        '189',
                    ),
                    '2' => array(
                        'China',
                        '43',
                    ),
                    '3' => array(
                        'Egypt',
                        '256',
                    ),
                    '4' => array(
                        'France',
                        '123',
                    ),
                    '5' => array(
                        'Australia',
                        '88',
                    )        
                ),
                "orgTypeChart" => array(
                    '0' => array(
                        'Name',
                        'Description',
                        'Image',
                        'Parent name',
                        'Tooltip',
                        'Url',
                        'Parent id',
                        'Level'
                    ),
                    '1' => array(
                        'Mike',
                        'President',
                        '',
                        '',
                        'The President',
                        '',
                        '',
                        '1'
                    ),
                    '2' => array(
                        'Jim',
                        'Vice President',
                        '',
                        'Mike',
                        'VP',
                        '',
                        '1',
                        '2'
                    ),
                    '3' => array(
                        'Bob',
                        '',
                        '',
                        'Jim',
                        'Bob Sponge',
                        '',
                        '2',
                        '3'
                    ),
                    '4' => array(
                        'Carol',
                        '',
                        '',
                        'Bob',
                        '',
                        '',
                        '3',
                        '4'
                    ),
                    '5' => array(
                        'Alice',
                        '',
                        '',
                        'Mike',
                        '',
                        '',
                        '1',
                        '2'
                    ),
                )
            );
        }

        /** 
         * Returns default array for chart.js charts
         */ 
        public function get_charts_default_data_chartjs(){
            return  array(
                '0' => array(
                    'Country',
                    'Population'
                ),   
                '1' => array(
                    'United States',
                    '189'
                ),
                '2' => array(
                    'China',
                    '43'
                ),
                '3' => array(
                    'Egypt',
                    '256'
                ),
                '4' => array(
                    'France',
                    '123'
                ),
                '5' => array(
                    'Australia',
                    '88'
                )        
            );
        }

        public function get_charts($ordering = ''){
            global $wpdb;
            $charts_table = esc_sql( $wpdb->prefix . CHART_BUILDER_DB_PREFIX ) . "charts";
    
            $sql = "SELECT id,title
                    FROM {$charts_table} WHERE `status` = 'published' OR `status` = 'draft'";
    
    
            if($ordering != ''){
                $sql .= ' ORDER BY id '.$ordering;
            }
    
            $charts = $wpdb->get_results( $sql , "ARRAY_A" );
    
            return $charts;
        }
    }
}

if( ! function_exists( 'CBActions' ) ){
	/**
	 * Function for quick access to Chart_Builder_Actions class
	 *
	 * @since   1.0.0
	 * @return  Chart_Builder_Actions
	 */
	function CBActions(){

		static $instance = null;

		if( $instance === null ){
			$instance = Chart_Builder_Actions::get_instance( CHART_BUILDER_NAME );
		}

		if( $instance instanceof Chart_Builder_Actions ){
			return $instance;
		}

		return Chart_Builder_Actions::get_instance( CHART_BUILDER_NAME );
	}
}
