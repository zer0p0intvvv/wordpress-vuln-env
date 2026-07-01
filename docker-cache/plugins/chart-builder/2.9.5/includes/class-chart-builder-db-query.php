<?php
if( !class_exists( 'Chart_Builder_DB_Query' ) ){
	ob_start();

	/**
	 * Class Chart_Builder_DB_Query
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
	class Chart_Builder_DB_Query {

		/**
		 * The array of allowed types.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 * @var array
		 */
		protected static $allowed_types = array( 'string', 'number', 'boolean', 'date', 'datetime', 'timeofday' );

		/**
		 * The query.
		 *
		 * @access protected
		 * @var string
		 */
		protected $_query;

		/**
		 * The chart id.
		 *
		 * @access protected
		 * @var int
		 */
		protected $_chart_id;

		/**
		 * Any additional parameters (e.g. for connecting to a remote db).
		 *
		 * @access protected
		 * @var array
		 */
		protected $_params;

		/**
		 * The error message.
		 *
		 * @access protected
		 * @var string
		 */
		protected $_error;

		/**
		 * The array of data.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 * @var array
		 */
		protected $_data = array();

		/**
		 * The array of series.
		 *
		 * @since 1.0.0
		 *
		 * @access protected
		 * @var array
		 */
		protected $_series = array();

		/**
		 *
		 * @since 1.0.0
         *
		 * @access private
		 * @var
		 */
		private $_args;

		/**
		 * Constructor.
		 *
		 * @access public
		 * @param string $query The query.
		 * @param int    $chart_id The chart id.
		 * @param array  $params Any additional parameters (e.g. for connecting to a remote db).
		 */
		public function __construct( $query = null, $chart_id = null, $params = null ) {
			$this->_query = $query;
			$this->_chart_id = $chart_id;
			$this->_params = $params;
		}

		/**
		 * Return allowed types
		 *
		 * @since 1.0.1
		 *
		 * @static
		 * @access public
		 * @return array the allowed types
		 */
		public static function getAllowedTypes() {
			return self::$allowed_types;
		}

		/**
		 * Validates series tyeps.
		 *
		 * @since 1.0.1
		 *
		 * @static
		 * @access protected
		 *
		 * @param array $types The icoming series types.
		 *
		 * @return boolean TRUE if sereis types are valid, otherwise FALSE.
		 */
		protected static function _validateTypes( $types ) {
			foreach ( $types as $type ) {
				if ( ! in_array( $type, self::$allowed_types, true ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Fetches information from source, parses it and builds series and data arrays.
		 *
		 * @param bool $as_html Should the result be fetched as an HTML table or as an object.
		 * @param bool $results_as_numeric_array Should the result be fetched as ARRAY_N instead of ARRAY_A.
		 * @param bool $raw_results Should the result be returned without processing.
		 * @access public
		 * @return boolean TRUE on success, otherwise FALSE.
		 */
		public function fetch( $as_html = false, $results_as_numeric_array = false, $raw_results = false ) {
			if ( empty( $this->_query ) ) {
				return false;
			}

			// only select queries allowed.
			if ( preg_match( '/^\s*(insert|delete|update|replace|create|alter|drop|truncate)\s/i', $this->_query ) ) {
				$this->_error = __( 'Only SELECT queries are allowed', CHART_BUILDER_NAME );
				return false;
			}

            $validate_query = str_replace( "\n", ' ', $this->_query );
			// impose a limit if no limit clause is provided.
			if ( strpos( strtolower( $validate_query ), 'select' ) !== false ) {
				if ( strpos( strtolower( $validate_query ), ' limit ' ) === false ) {
					$this->_query .= ' LIMIT ' . apply_filters( 'ays_cb_sql_query_limit', 1000, $this->_chart_id );
				}
			}

			$this->_query = apply_filters( 'ays_cb_db_query', $this->_query, $this->_chart_id, $this->_params );

			$results = array();
			$headers = array();

			// short circuit results for remote dbs.
			if ( false !== ( $remote_results = apply_filters( 'ays_cb_db_query_execute', false, $this->_query, $as_html, $results_as_numeric_array, $raw_results, $this->_chart_id, $this->_params ) ) ) {
				$error = $remote_results['error'];
				if ( empty( $error ) ) {
					$results = $remote_results['results'];
					$headers = $remote_results['headers'];
				}

				$this->_error = $error;

				if ( $raw_results ) {
					return $results;
				}
			}

			if ( ! ( $results && $headers ) ) {
				global $wpdb;
				$wpdb->hide_errors();
				// @codingStandardsIgnoreStart
				$rows = $wpdb->get_results( $this->_query, $results_as_numeric_array ? ARRAY_N : ARRAY_A );
				// @codingStandardsIgnoreEnd
				$wpdb->show_errors();

				if ( $raw_results ) {
					return $rows;
				}

				if ( $rows ) {
					$results    = array();
					$headers    = array();
					if ( $rows ) {
						$row_num    = 0;
						foreach ( $rows as $row ) {
							$result     = array();
							$col_num    = 0;
							foreach ( $row as $k => $v ) {
								$result[]   = $v;
								if ( 0 === $row_num ) {
									$headers[]  = array( 'type' => $this->get_col_type( $col_num++ ), 'label' => $k );
								}
							}
							$results[] = $result;
							$row_num++;
						}
					}
				}

				$this->_error = $wpdb->last_error;
			}

			if ( $as_html ) {
				$results = $this->html( $headers, $results );
			} else {
				$results = $this->object( $headers, $results );
			}

			return apply_filters( 'ays_cb_db_query_results', $results, $headers, $as_html, $results_as_numeric_array, $raw_results, $this->_query, $this->_chart_id, $this->_params );
		}

		/**
		 * Get the data type of the column.
		 *
		 * @param int $col_num The column index in the fetched result set.
		 * @access private
		 * @return int
		 */
		private function get_col_type( $col_num ) {
			global $wpdb;
			switch ( $wpdb->get_col_info( 'type', $col_num ) ) {
				case 0:
				case 5:
				case 4:
				case 9:
				case 3:
				case 2:
				case 246:
				case 8:
					// numeric.
					return 'number';
				case 10:
				case 12:
				case 14:
					// date.
					return 'date';
			}
			return 'string';
		}

		/**
		 * Returns the HTML output.
		 *
		 * @param array $headers The headers of the result set.
		 * @param array $results The data of the result set.
		 * @access private
		 * @return string
		 */
		private function html( $headers, $results ) {
			ob_start();
			?>
			<table cellspacing="0" width="100%" id="results">
				<thead>
				<tr>
					<?php
					foreach ( $headers as $header ) {
						echo '<th>' . $header['label'] . '</th>';
					}
					?>
				</tr>
				</thead>
				<tfoot>
				</tfoot>
				<tbody>
				<?php
				foreach ( $results as $result ) {
					echo '<tr>';
					foreach ( $result as $r ) {
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
		 * Sets the series and data.
		 *
		 * @param array $headers The headers of the result set.
		 * @param array $results The data of the result set.
		 *
		 * @access private
		 * @return array
		 * @throws Exception
		 */
		private function object( $headers, $results ) {
			$series = array();
			foreach ( $headers as $header ) {
				$series[] = $header;
			}
			$this->_series = $series;

			$data = array();
			foreach ( $results as $row ) {
				$data[] = $this->_normalizeData( $row );
			}

			$this->_data = $data;

			return $this->_data;
		}

		/**
		 * Returns the final query.
		 *
		 * @access public
		 * @return string
		 */
		public function get_query() {
			return $this->_query;
		}

		/**
		 * Returns source name.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 * @return string The name of source.
		 */
		public function getSourceName() {
			return __CLASS__;
		}

		/**
		 * Returns series parsed from source.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 * @return array The array of series.
		 */
		public function getSeries() {
			return $this->_series;
		}

		/**
		 * Returns data parsed from source.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 * @return string The serialized array of data.
		 */
		public function getData( $fetch_from_editable_table = false ) {
			if ( $fetch_from_editable_table ) {
				$this->_fetchDataFromEditableTable();
			}
			return serialize( $this->_data );
		}

		/**
		 * Returns raw data array.
		 *
		 * @since 1.1.0
		 *
		 * @access public
		 * @return array
		 */
		public function getRawData( $fetch_from_editable_table = false ) {
			if ( $fetch_from_editable_table ) {
				$this->_fetchDataFromEditableTable();
			}
			return $this->_data;
		}

		/**
		 * Re populates series if the source is dynamic.
		 *
		 * @since 1.1.0
		 *
		 * @access public
		 *
		 * @param array $series The actual array of series.
		 * @param int   $chart_id The chart id.
		 *
		 * @return array The re populated array of series or old one.
		 */
		public function repopulateSeries( $series, $chart_id ) {
			return $series;
		}

		/**
		 * Re populates data if the source is dynamic.
		 *
		 * @since 1.1.0
		 *
		 * @access public
		 *
		 * @param array $data The actual array of data.
		 * @param int   $chart_id The chart id.
		 *
		 * @return array The re populated array of data or old one.
		 */
		public function repopulateData( $data, $chart_id ) {
			return $data;
		}

		/**
		 * Normalizes values according to series' type.
		 *
		 * @param array $data The row of data.
		 *
		 * @return array Normalized row of data.
		 * @throws Exception
		 * @since 1.0.0
		 *
		 * @access protected
		 *
		 */
		protected function _normalizeData( $data ) {
			// normalize values
			foreach ( $this->_series as $i => $series ) {
				// if no value exists for the seires, then add null
				if ( ! isset( $data[ $i ] ) ) {
					$data[ $i ] = null;
				}
				if ( is_null( $data[ $i ] ) ) {
					continue;
				}
				switch ( $series['type'] ) {
					case 'number':
						$data[ $i ] = ( is_numeric( $data[ $i ] ) ) ? floatval( $data[ $i ] ) : ( is_numeric( str_replace( ',', '', $data[ $i ] ) ) ? floatval( str_replace( ',', '', $data[ $i ] ) ) : null );
						break;
					case 'boolean':
						$datum = trim( strval( $data[ $i ] ) );
						$data[ $i ] = in_array( $datum, array( 'true', 'yes', '1' ), true ) ? 'true' : 'false';
						break;
					case 'timeofday':
						$date = new DateTime( '1984-03-16T' . $data[ $i ] );
						if ( $date ) {
							$data[ $i ] = array(
								intval( $date->format( 'H' ) ),
								intval( $date->format( 'i' ) ),
								intval( $date->format( 's' ) ),
								0,
							);
						}
						break;
					case 'datetime':
						// let's check if the date is a Unix epoch
						$value = DateTime::createFromFormat( 'U', $data[ $i ] );
						if ( $value !== false && ! is_wp_error( $value ) ) {
							$data[ $i ] = $value->format( 'Y-m-d H:i:s' );
						}
						break;
					case 'string':
						// if a ' is provided, strip the backslash
						$data[ $i ] = stripslashes( $this->toUTF8( $data[ $i ] ) );
						break;
				}
			}

			return apply_filters( 'ays_cb_format_data', $data, $this->_series );
		}

		/**
		 * Converts values to UTF8, if required.
		 *
		 * @access protected
		 *
		 * @param string $datum The data to convert.
		 *
		 * @return string The converted data.
		 */
		protected final function toUTF8( $datum ) {
			if ( ! function_exists( 'mb_detect_encoding' ) || mb_detect_encoding( $datum ) !== 'ASCII' ) {
				$datum = \ForceUTF8\Encoding::toUTF8( $datum );
			}
			return $datum;
		}

		/**
		 * Determines the formats of date/time columns.
		 *
		 * @access public
		 *
		 * @param array $series The actual array of series.
		 * @param array $data The actual array of data.
		 *
		 * @return array
		 */
		public static final function get_date_formats_if_exists( $series, $data ) {
			$date_formats = array();
			$types = array();
			$index = 0;
			foreach ( $series as $column ) {
				if ( in_array( $column['type'], array( 'date', 'datetime', 'timeofday' ), true ) ) {
					$types[] = array( 'index' => $index, 'type' => $column['type'] );
				}
				$index++;
			}

			if ( ! $types ) {
				return $date_formats;
			}

			$random = $data;
			// let's randomly pick 5 data points instead of cycling through the entire data set.
			if ( count( $data ) > 5 ) {
				$random = array();
				for ( $x = 0; $x < 5; $x++ ) {
					$random[] = $data[ rand( 0, count( $data ) - 1 ) ];
				}
			}

			foreach ( $types as $type ) {
				$formats = array();
				foreach ( $random as $datum ) {
					$f = self::determine_date_format( $datum[ $type['index'] ], $type['type'] );
					if ( $f ) {
						$formats[] = $f;
					}
				}
				// if there are multiple formats, use the most frequent format.
				$formats = array_filter( $formats );
				if ( $formats ) {
					$formats = array_count_values( $formats );
					arsort( $formats );
					$formats = array_keys( $formats );
					$final_format = reset( $formats );
					// we have determined the PHP format; now we have to change this into the JS format where m = MM, d = DD etc.
					$date_formats[] = array( 'index' => $type['index'], 'format' => str_replace( array( 'Y', 'm', 'd', 'H', 'i', 's' ), array( 'YYYY', 'MM', 'DD', 'HH', 'mm', 'ss' ), $final_format ) );
				}
			}
			return $date_formats;
		}

		/**
		 * Determines the date/time format of the given string.
		 *
		 * @access private
		 *
		 * @param string $value The string.
		 * @param string $type 'date', 'timeofday' or 'datetime'.
		 *
		 * @return string|null
		 */
		private static function determine_date_format( $value, $type ) {
			if ( version_compare( phpversion(), '5.3.0', '<' ) ) {
				return null;
			}

			$formats = array(
				'Y/m/d',
				'Y-m-d',
				'm/d/Y',
				'm-d-Y',
				'd-m-Y',
				'd/m/Y',
			);

			switch ( $type ) {
				case 'datetime':
					$formats = array_merge(
						$formats, array(
							'U',
							'Y/m/d H:i:s',
							'Y-m-d H:i:s',
							'm/d/Y H:i:s',
							'm-d-Y H:i:s',
						)
					);
					break;
				case 'timeofday':
					$formats = array_merge(
						$formats, array(
							'H:i:s',
							'H:i',
						)
					);
					break;
			}

			$formats = apply_filters( 'ays_cb_date_formats', $formats, $type );

			foreach ( $formats as $format ) {
				$return = DateTime::createFromFormat( $format, $value );
				if ( $return !== false && ! is_wp_error( $return ) ) {
					return $format;
				}
			}
			// invalid format
			return null;
		}

		/**
		 * Returns the error, if any.
		 *
		 * @access public
		 * @return string
		 */
		public function get_error() {
			return $this->_error;
		}

		/**
		 * Fetches information from the editable table and parses it to build series and data arrays.
		 *
		 * @since ?
		 *
		 * @access public
		 * @return boolean TRUE on success, otherwise FALSE.
		 */
		public function fetchFromEditableTable() {
			if ( empty( $this->_args ) ) {
				return false;
			}

			$this->_fetchSeriesFromEditableTable();
			$this->_fetchDataFromEditableTable();
			return true;
		}

		/**
		 * Fetches series information from the editable table. This is fetched only through the UI and not while refreshing the chart data.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 */
		private function _fetchSeriesFromEditableTable() {
			$params = $this->_args;
			$headers = array_filter( $params['header'] );
			$types = array_filter( $params['type'] );
			$header_row = $type_row = array();

			if ( $headers ) {
				foreach ( $headers as $header ) {
					if ( ! empty( $types[ $header ] ) ) {
						$this->_series[] = array(
							'label' => $header,
							'type'  => $types[ $header ],
						);
					}
				}
			}

			return true;
		}

		/**
		 * Fetches data information from the editable table.
		 *
		 * @throws Exception
		 * @since 1.0.0
		 *
		 * @access private
		 */
		private function _fetchDataFromEditableTable() {
			$headers    = wp_list_pluck( $this->_series, 'label' );
			$this->fetch();

			$data = $this->_data;
			$this->_data = array();

			foreach ( $data as $line ) {
				$data_row = array();
				// we have to make sure we are fetching the data in the right order
				// in case the columns have been reordered
				foreach ( $headers as $header ) {
					$value = $line[ $header ];
					// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					if ( in_array( $header, $headers ) ) {
						$data_row[] = $value;
					}
				}
				$this->_data[] = $this->_normalizeData( $data_row );
			}

			return true;
		}
	}
}