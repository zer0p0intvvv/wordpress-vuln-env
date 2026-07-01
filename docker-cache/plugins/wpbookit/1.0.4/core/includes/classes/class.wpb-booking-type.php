<?php
/**
 * Regular Booking Type
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Booking Type Class.
 */
class WPB_Booking_Type extends WPB_Data {
	
	/**
	 * ID for this object.
	 */
	protected $id = 0;
	
	/**
	 * Booking Type Data array. This is the core booking_type data exposed in APIs.
	 */
	protected $data = array(
		'name'       		=> '',
		'slug'       		=> '',
		'description'  		=> '',
		'type'    			=> '',
		'unavailable'		=> 0,
		'duration'			=> 0,
		'url'				=> 0,
		'status'			=> 0,
    );

	/**
	 * Stores additional meta data.
	 */
	protected $meta_data = null;

	/**
	 * Stores additional object_read
	 */
	protected $object_read = null;

    /**
	 * Stores meta in cache for future reads.
	 */
	protected $cache_group = 'booking_type';

	/**
	 * Get the booking_type if ID is passed, otherwise the booking_type is new and empty.
	 **/

	 	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 */
	protected $meta_type = 'booking_type';

	protected $object_type = 'booking_type';

	public $is_exist= true;

	public function __construct( $booking_Type = 0 ) {
			parent:: __construct($booking_Type);
		if ( is_numeric( $booking_Type ) && $booking_Type > 0 ) :
			$this->set_id( $booking_Type );
		
		elseif ( $booking_Type instanceof self ) :
			$this->set_id( $booking_Type->get_id() );

		elseif ( ! empty( $booking_Type->id ) ) :
			$this->set_id( $booking_Type->id );

		else :
			$this->set_object_read( true );

		endif;
		if($this->load()){
			$this->is_exist = false;
		}
		if ( $this->get_id() > 0 ) :
			$this->read( $booking_Type );
		endif;
	}

	/**
	 * Set object read property.
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Read Object.
	 */
	public function read( $booking_Type ){
		foreach( $this->data??[] as $prop => $value ) :
			if ( array_key_exists( $prop, $this->data ) ) {
				$this->changes[ $prop ] = $value;
			}
		endforeach;
	}

	/**
	 * Set ID.
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Sets booking type name.
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Sets booking type slug.
	 */
	public function set_slug( $slug ) {
		$this->set_prop( 'slug', $slug );
	}
	/**
	 * Sets booking type description.
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', $description );
	}

	/**
	 * Sets booking type type.
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', $type );
	}

	/**
	 * Sets booking type unavailable.
	 */
	public function set_unavailable( $unavailable ) {
		$this->set_prop( 'unavailable', $unavailable );
	}

	/**
	 * Sets duration.
	 */
	public function set_duration( $duration ) {
		$this->set_prop( 'duration', $duration );
	}

	/**
	 * Sets booking type status.
	 */
	public function set_url( $status ) {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Sets booking type status.
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Sets a prop for a setter method of the current object.
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			$this->changes[ $prop ] = $value;
		}
	}

	/**
	 * Returns the unique ID for booking_type object.
	 **/
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets booking type name.
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Gets booking type slug.
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'slug', $context );
	}

	/**
	 * Gets booking type slug.
	 */
	public function get_bookingtype_permalink( $context = 'view' ) {
        $general_setting = wpb_get_general_settings();
        $base_url = $general_setting['permalink_strcture']??'booking';
		$permalink_structure = isset($base_url) ? $base_url : 'booking';
		
		// Ensure permalink structure starts with a slash
		if (substr($permalink_structure, 0, 1) !== '/') {
			$permalink_structure = '/' . $permalink_structure;
		}

		// Ensure permalink structure ends with a slash
		if (substr($permalink_structure, -1) !== '/') {
			$permalink_structure .= '/';
		}

		// Ensure the full URL ends correctly with the slug
		$permalink = site_url('/index.php') . $permalink_structure . $this->get_slug();

		return $permalink;
	}

	/**
	 * Gets booking type description.
	 */
	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Gets booking type type.
	 */
	public function get_type( $context = 'view' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Gets booking type unavailable.
	 */
	public function get_unavailable( $context = 'view' ) {
		return $this->get_prop( 'unavailable', $context );
	}

	/**
	 * Get duration.
	 */
	public function get_duration( $context = 'view' ) {
		return $this->get_prop( 'duration', $context );
	}
	
	/**
	 * Get URL.
	 */
	public function get_url( $context = 'view' ) {
		return $this->get_prop( 'url', $context );
	}

	/**
	 * Return the booking_type statuses without wpb- internal prefix.
	 **/
	public function get_status( $context = 'view' ) {
		$status = $this->get_prop( 'status', $context );

		if ( empty( $status ) && 'view' === $context ) :
			$status = apply_filters( 'wpb_default_booking_type_status', 'pending' );
		endif;
		return $status;
	}
	public function get_calulated_price( $context = 'view' ) {
		return (str_replace(',','',$this->get_meta( 'price', $context ))); //$price['tax_values']['total'];
	}

	/**
	 * Get basic booking type data in array format.
	 */
	public function get_base_data() {
		return array_merge(
			array( 'id' => $this->get_id() ),
			$this->data,
		);
	}

	/**
	 * Get all class data in array format.
	 * @return array
	 */
	public function get_data() {
		return array_merge(
			$this->get_base_data(),
			array(
				'meta_data' => $this->get_meta_data(),
			)
		);
	}

	/**
	 * Gets a prop for a getter method of the current object.
	 */
	protected function get_prop( $prop, $context = 'view' ) {
		$value = null;
		if ( !empty($this->data) && array_key_exists( $prop, $this->data ) ) :
			
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

			if ( 'view' === $context ) :
				$value = apply_filters( 'wpb_get_' . $prop, $value, $this );
			endif;
		endif;
		return $value;
	}

    /**
	 * Helper method to compute meta cache key. Different from WP Meta cache key in that meta data cached using this key also contains meta_id column.
	 */
	public function get_booking_type_cache_key() {
		if ( ! $this->get_id() ) return false;
		return self::generate_cache_key( $this->get_id(), $this->cache_group );
	}

    /**
	 * Generate cache key from id and group.
	 */
	public static function generate_cache_key( $id, $cache_group ) {
		return WPB_Cache_Handler::get_cache_prefix( $cache_group ) . WPB_Cache_Handler::get_cache_prefix( 'object_' . $id ) . 'object_booking_type_' . $id;
	}

	public function get_booking_type( $booking_Type_ID = false ){
		if( ! is_numeric( $booking_Type_ID ) ) return false;
		
		if ( ! empty( $this->cache_group ) ) :
			$cache_key = $this->get_booking_type_cache_key();

			if( !empty( $cache_key ) ) :
				$cached_booking_type = wp_cache_get( $cache_key, $this->cache_group );
			endif;
		endif;

		try {
			$booking_Type = new WPB_Booking_Type( $booking_Type_ID );
			if ( ! $cached_booking_type && ! empty( $this->cache_group ) ) :
				wp_cache_set( $cache_key, $booking_Type, $this->cache_group );
			endif;
			return $booking_Type;

		} catch ( Exception $e ) {
			$error = new WP_Error( 'booking_type_error', $e->getMessage() );
			return false;

		}
	}

	public function get_booking_types( $args = array(), $defaults = array('status' =>['1'])) {
		global $wpdb;
		$booking_Types = [];

		// Extract individual arguments
		$ids 			= $args['ids'] ?? null;
		$status 		= isset($args['status']) ? $args['status'] : $defaults['status'];
		$private_mode 	= isset($args['private_mode']) ? $args['private_mode'] : 'no';
		$paged 			= $args['paged'] ?? 1;
		$per_page 		= $args['per_page'] ?? 10;
		$staff          = isset($args['staff']) ? $args['staff'] : 0;       
		//print_r($staff);
		$charges 			= $args['charges'] ?? 'all';
	
		// Calculate the offset based on pagination
		$offset = ($paged - 1) * $per_page;
	
		// Prepare the SQL query with pagination and user ID filtering if user_id is set
		$query = "SELECT DISTINCT {$wpdb->wpb_booking_type}.* 
		FROM {$wpdb->wpb_booking_type} 
		LEFT JOIN {$wpdb->wpb_booking_typemeta} ON {$wpdb->wpb_booking_typemeta}.wpb_booking_type_id = {$wpdb->wpb_booking_type}.id  
		WHERE 1=1 ";

		$current_url = $_SERVER['REQUEST_URI'];
		if (strpos($current_url, 'wpbookit-dashboard') !== false && strpos($current_url, 'tab=bookings') !== false) {
			$query .= " AND {$wpdb->wpb_booking_type}.status = '1'";
		}

		if($ids){
			$booking_type_ids_placeholders = implode(', ', array_fill(0, count($ids), '%s'));
	
			$query .= $wpdb->prepare(
				" AND id IN ($booking_type_ids_placeholders)",
				...$ids
			);
		}

				
		if ( is_array( $status ) && !empty($status) ) :
			$status_placeholders_string = implode(', ', $status);
			$query .= $wpdb->prepare(
				" AND {$wpdb->wpb_booking_type}.status IN (%s)",
				$status_placeholders_string
			);
		endif;

		if( $staff) :
			$query .= $wpdb->prepare(
				" AND {$wpdb->wpb_booking_typemeta}.meta_key = 'staff' AND {$wpdb->wpb_booking_typemeta}.meta_value = %d ",
				$staff
			);
		endif;

		if($charges!='all' && in_array($charges,['free','paid']) ){
			if($charges=='free'){
				$query .= $wpdb->prepare(
					" AND %i.`meta_key` = 'price' AND %i.`meta_value` LIKE 0 ",
					$wpdb->wpb_booking_typemeta,$wpdb->wpb_booking_typemeta,
				);
			}else{
				$query .= $wpdb->prepare(
					" AND %i.`meta_key` = 'price' AND %i.`meta_value` NOT LIKE 0 ",
					$wpdb->wpb_booking_typemeta,$wpdb->wpb_booking_typemeta,
				);

			}
		}

		$query .= $wpdb->prepare(
			" LIMIT %d OFFSET %d",
			$per_page,
			$offset
		);
	
		// Apply filter to the SQL query
		$query = apply_filters('wpb_get_booking_types_query', $query, $args);

		// Execute the query
		$booking_TypeIDs = $wpdb->get_results($query);//phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 

		if ( ! empty( $booking_TypeIDs ) ) :
			foreach ( $booking_TypeIDs as $booking_Type_ID ) :
				if(!empty($private_mode) && $private_mode === 'yes'){
					$is_private_mode = get_metadata('wpb_booking_type', $booking_Type_ID->id, 'private_mode', true);
					if($is_private_mode ===  'false'){
						$booking_Types[] = new WPB_Booking_Type( $booking_Type_ID );
					}
				}else{
					$booking_Types[] = new WPB_Booking_Type( $booking_Type_ID );
				}
			endforeach;
		endif;
		
		return $booking_Types;
	}

	public function get_booking_type_staff() {
		$staff_id = $this->get_meta('staff');
		$wp_user_instance = get_user_by('ID', $staff_id);
		return $wp_user_instance;
	}

	public function get_booking_type_meta( $meta_key ) {
		return get_metadata('wpb_booking_type', $this->id, $meta_key, true);
	}
}
