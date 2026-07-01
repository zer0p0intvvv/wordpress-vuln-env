<?php
/**
 * Regular Booking
 *
 */

defined('ABSPATH') || exit;

/**
 * Booking Class.
 */
class WPB_Booking extends WPB_Data
{

	/**
	 * ID for this object.
	 */
	protected $id = 0;

	/**
	 * Booking Data array. This is the core booking data exposed in APIs.
	 */
	protected $data = array(
		'booking_type_id' 	=> 0,
		'customer_id' 		=> '',
		'booking_name' 		=> '',
		'booking_email'		=> '',
		'booking_type'		=> '',
		'transaction_id' 	=> 0,
		'date_created' 		=> '',
		'status' 			=> '',
		'booking_date' 		=> '0000-00-00',
		'timeslot' 			=> '00:00',
		'duration' 			=> '00'
	);

	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 */
	protected $meta_type = 'bookings';

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
	protected $cache_group = 'booking';

	/**
	 * Get the booking if ID is passed, otherwise the booking is new and empty.
	 **/
	public $is_exist= true;
	protected $object_type = 'bookings';

	public function __construct($booking = 0)
	{
		parent:: __construct($booking);
		if (is_numeric($booking) && $booking > 0):
			$this->set_id($booking);

		elseif ($booking instanceof self):
			$this->set_id($booking->get_id());

		elseif (!empty($booking->id)):
			$this->set_id($booking->id);

		else:
			$this->set_object_read(true);

		endif;
		if($this->load()){
			$this->is_exist = false;
		}

		if ($this->get_id() > 0):
			$this->read($this->data);
		endif;
	}

	/**
	 * Set ID.
	 */
	public function set_id($id)
	{
		$this->id = absint($id);
	}

	/**
	 * Set object read property.
	 */
	public function set_object_read($read = true)
	{
		$this->object_read = (bool) $read;
	}

	/**
	 * Sets booking booking_type_id.
	 */
	public function set_booking_type_id($booking_type_id)
	{
		$this->set_prop('booking_type_id', $booking_type_id);
	}

	/**
	 * Sets booking customer_id.
	 */
	public function set_customer_id($customer_id)
	{
		$this->set_prop('customer_id', $customer_id);
	}

	/**
	 * Sets booking timeslot.
	 */
	public function set_timeslot($timeslot)
	{
		$this->set_prop('timeslot', $timeslot);
	}

	/**
	 * Sets booking booking_date.
	 */
	public function set_booking_date($booking_date)
	{
		$this->set_prop('booking_date', $booking_date);
	}

	/**
	 * Sets booking transaction_id.
	 */
	public function set_transaction_id($transaction_id)
	{
		$this->set_prop('transaction_id', $transaction_id);
	}

	/**
	 * Sets date_created.
	 */
	public function set_date_created($date_created)
	{
		$this->set_prop('date_created', $date_created);
	}

	/**
	 * Sets booking status.
	 */
	public function set_status($status)
	{
		$this->set_prop('status', $status);
	}

	/**
	 * Sets a prop for a setter method of the current object.
	 */
	protected function set_prop($prop, $value)
	{
		if (array_key_exists($prop, $this->data)) {
			$this->changes[$prop] = $value;
		}
	}

	public function read($booking)
	{
		foreach ($booking??[] as $prop => $value):
			if (array_key_exists($prop, $this->data)) {
				$this->changes[$prop] = $value;
			}
		endforeach;
	}

	/**
	 * Returns the unique ID for booking object.
	 **/
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * Gets booking booking_type_id.
	 */
	public function get_booking_type_id($context = 'view')
	{
		return $this->get_prop('booking_type_id', $context);
	}

	/**
	 * Gets booking customer_id.
	 */
	public function get_customer_id($context = 'view')
	{
		return $this->get_prop('customer_id', $context);
	}
	/**
	 * Gets booking customer name.
	 */
	public function get_booking_name($context = 'view')
	{
		return $this->get_prop('booking_name', $context);
	}
	public function get_booking_email($context = 'view')
	{
		return $this->get_prop('booking_email', $context);
	}
	public function get_formated_booking_datetime(){
		return wpb_get_formated_date_time($this->get_booking_date('view', false), $this->get_timeslot('view', false));
	}

	public function get_booking_price(){
		global $wpdb;
		$price = $this->get_meta('price'); 
		$postfix = trim(wpb_get_general_settings()['postfix'] ?? '-');
		$prefix = trim(wpb_get_general_settings()['prefix'] ?? '-');
	
		$free_label = __("Free", 'wpbookit');
	
		return $wpdb->get_var($wpdb->prepare("
			SELECT 
				CASE 
					WHEN COALESCE(total_amount, 0) = 0 THEN %s
					ELSE CONCAT(%s, %s, FORMAT(COALESCE(total_amount, %s), 2), %s)
				END AS total_amount
			FROM {$wpdb->prefix}wpb_payments 
			WHERE bookings_id = %d", 
			$free_label, $prefix, $price, '0', $postfix, $this->id))??$free_label;
	}
	public function get_raw_booking_price(){
		global $wpdb;
	
		return $wpdb->get_var($wpdb->prepare("
			SELECT 
				FORMAT(total_amount, 2) AS total_amount
			FROM {$wpdb->prefix}wpb_payments 
			WHERE bookings_id = %d", 
			$this->id));
	}

	public function get_dis_booking_price(){
		global $wpdb;
	
		$paid_amount= $wpdb->get_var($wpdb->prepare("
		SELECT 
			 paid_amount
		FROM {$wpdb->prefix}wpb_payments 
		WHERE bookings_id = %d", 
		$this->id))??0;
		if(!is_numeric($paid_amount)){
			$paid_amount= 0;
		}
		return number_format($paid_amount,2,'.','');
	}

	public function get_raw_booking_sub_price(){
		global $wpdb;
	
		return number_format( $wpdb->get_var($wpdb->prepare("
			SELECT 
				subtotal_amount AS total_amount
			FROM {$wpdb->prefix}wpb_payments 
			WHERE bookings_id = %d", 
			$this->id)),2,'.','');
	}

	/**
	 * Gets Payment Id.
	 */
	public function get_payment_id(){
		global $wpdb;
		$query = $wpdb->prepare("SELECT id FROM {$wpdb->wpb_payments} WHERE bookings_id = %d", $this->id);
		return $wpdb->get_var($query); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
	/**
	 * Gets booking customer.
	 */
	public function get_customer($context = 'view')
	{
		$customerID = $this->get_customer_id();
		if ($customerID):
			return get_userdata($customerID);
		endif;
		return $customerID;
	}
	/**
	 * Gets booking type name
	 */
	public function get_booking_type($context = 'view',$fields= [],$meta=false)
	{
		$bookingTypeID = $this->get_booking_type_id();
		if ($bookingTypeID):
			return wpb_get_booking_type((int)$bookingTypeID,$fields,$meta);
		endif;
		return $bookingTypeID;
	}

	/**
	 * Gets booking timeslot.
	 */
	public function get_timeslot($context = 'view',$with_wp_date_format=false)
	{
		$timeslot = $this->get_prop('timeslot', $context);
		if($with_wp_date_format){

			$time_format = get_option('time_format');
			$date_format = $time_format;

			$timezone = wpb_get_timezone();
			if (!empty($timezone)) {
				date_default_timezone_set($timezone); //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set 
			} 

			$timeslot = $this->get_prop('timeslot', $context);
			$timestamp = strtotime($timeslot);
			// Reset the timezone to UTC
			date_default_timezone_set('UTC'); //phpcs:ignore  WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set 

			return wp_date($date_format, $timestamp);
		}
		return $timeslot;
	}

	/**
	 * Gets booking duration.
	 */
	public function get_duration($context = 'view')
	{
		return $this->get_prop('duration', $context);
	}

	/**
	 * Gets booking date.
	 */
	public function get_booking_date($context = 'view',$with_wp_date_format=false)
	{
		$booking_date =  $this->get_prop('booking_date', $context);
		if($with_wp_date_format) return wp_date(get_option('date_format',strtotime($booking_date)));
		return $booking_date;
	}


	/**
	 * Gets booking transaction_id.
	 */
	public function get_transaction_id($context = 'view')
	{
		return $this->get_prop('transaction_id', $context);
	}

	/**
	 * Get date_created.
	 */
	public function get_date_created($context = 'view')
	{
		return $this->get_prop('date_created', $context);
	}

	/**
	 * Return the booking statuses without wpb- internal prefix.
	 **/
	public function get_status($context = 'view')
	{
		$status = $this->get_prop('status', $context);
		if (empty($status) && 'view' === $context ):
			$status = apply_filters('wpb_default_booking_status', 'pending');
		endif;
		return $status;
	}

	/**
	 * Get basic booking data in array format.
	 */
	public function get_base_data()
	{
		return array_merge(
			array('id' => $this->get_id()),
			$this->data,
		);
	}

	/**
	 * Get all class data in array format.
	 * @return array
	 */
	public function get_data()
	{
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
	protected function get_prop($prop, $context = 'view')
	{
		$value = null;

		if (array_key_exists($prop, $this->data??[])):
			$value = array_key_exists($prop, $this->changes) ? $this->changes[$prop] : $this->data[$prop];

			if ('view' === $context):
				$value = apply_filters('wpb_get_' . $prop, $value, $this);
			endif;
		endif;
		return $value;
	}

	/**
	 * Helper method to compute meta cache key. Different from WP Meta cache key in that meta data cached using this key also contains meta_id column.
	 */
	public function get_booking_cache_key() {
		if ( ! $this->get_id() ) return false;
		return self::generate_cache_key( $this->get_id(), $this->cache_group );
	}

	/**
	 * Generate cache key from id and group.
	 */
	public static function generate_cache_key($id, $cache_group) {
		return WPB_Cache_Handler::get_cache_prefix($cache_group) . WPB_Cache_Handler::get_cache_prefix('object_' . $id) . 'object_booking_' . $id;
	}

	public function prepare_query( $args = array(), $count = false ){
		global $wpdb;
		// Extract individual arguments
		$customer_id 	= sanitize_text_field( $args['user_id']??0 );
		$booking_type 	= $args['booking_type'];
		$status 		= $args['status']??null;
		$date 			= sanitize_text_field( $args['date']??'' );
		$date_from  	= sanitize_text_field( $args['date_from']??"" );
		$date_to 		= sanitize_text_field( $args['date_to']??'' );
		
		$time 			= sanitize_text_field( $args['time']??'' );
		$time_from  	= sanitize_text_field( $args['time_from']??'' );
		$time_to 		= sanitize_text_field( $args['time_to']??'' );

		$paged 			= sanitize_text_field( $args['paged']??1 );
		$per_page 		= sanitize_text_field( $args['per_page']??10 );
		$order 			= sanitize_text_field( isset($args['order']) && !empty($args['order'])?$args['order']:"asc" );
		$order_by 		= sanitize_text_field( isset($args['order_by']) && !empty($args['order_by'])?$args['order_by']:"id"   );
		$booking_name 	= sanitize_text_field( $args['booking_name']??"" );
		// $staff 			= sanitize_text_field( $args['staff']??0 );
		$staff 			= $args['staff']??false;
		
		$is_paid 	= sanitize_text_field( $args['is_paid']??false );
		$offset 	= sanitize_text_field( $args['offset']??'' );



		// Calculate the offset based on pagination
		if(empty($offset)){
			$offset = ($paged - 1) * $per_page;
		}

		$filter_sql_var_selector = apply_filters("wpb_get_booking_filter_sql_var_selector",'',$args) ;
		$filter_sql_left_join = apply_filters("wpb_get_booking_filter_sql_left_join",'',$args) ;
		// Prepare the SQL query with pagination and user ID filtering if user_id is set
		$query = "SELECT {$wpdb->wpb_bookings}.*, {$wpdb->wpb_payments}.total_amount AS price 
			{$filter_sql_var_selector}
			FROM {$wpdb->wpb_bookings}
			LEFT JOIN {$wpdb->wpb_bookingsmeta} 
			ON {$wpdb->wpb_bookings}.id = {$wpdb->wpb_bookingsmeta}.wpb_bookings_id 
			AND {$wpdb->wpb_bookingsmeta}.meta_key = 'staff_id'
			LEFT JOIN {$wpdb->wpb_payments} ON {$wpdb->wpb_bookings}.id = {$wpdb->wpb_payments}.bookings_id
			LEFT JOIN {$wpdb->wpb_booking_type} ON {$wpdb->wpb_bookings}.booking_type_id = {$wpdb->wpb_booking_type}.id
			LEFT JOIN {$wpdb->wpb_booking_typemeta} ON {$wpdb->wpb_bookings}.booking_type_id = {$wpdb->wpb_booking_typemeta}.wpb_booking_type_id AND {$wpdb->wpb_booking_typemeta}.meta_key = 'staff' 
			{$filter_sql_left_join}
			WHERE 1 = 1 ";
		$query .= apply_filters("wpb_get_booking_filter_sql_where_condition",'',$args) ;
		if ($is_paid ) {
			$query .= " AND {$wpdb->wpb_payments}.payment_status = '1'";
		}
			
		if ( $customer_id ):
			$query .= $wpdb->prepare(
				" AND customer_id = %d",
				$customer_id
			);
		endif;

		if ( $booking_name ):
			$query .= $wpdb->prepare(
				" AND booking_name LIKE %s",
				'%' . $booking_name . '%'
			);
		endif;

		if ( $booking_type ):
			$booking_type_placeholders = implode(', ', array_fill(0, count($booking_type), '%s'));

				$query .= $wpdb->prepare(
					" AND booking_type_id IN ($booking_type_placeholders)",
					...$booking_type
				);

		endif;

		if ( $staff ):
			$query .= $wpdb->prepare(
                " AND ({$wpdb->wpb_booking_typemeta}.meta_value = %s OR {$wpdb->wpb_bookingsmeta}.meta_value = %s)",
                $staff,
                $staff
            );
		endif;

		if (is_array($status)):
			$valid_statuses = array_intersect( $status, array_keys(wpb_get_booking_statuses()));

			if ( ! empty( $valid_statuses ) ):
				$placeholders = implode(', ', array_fill(0, count($valid_statuses), '%s'));

				$query .= $wpdb->prepare(
					" AND {$wpdb->wpb_bookings}.status IN ($placeholders)",
					...$valid_statuses
				);

			endif;
		endif;

		if(!empty($date)){
			$query .= $wpdb->prepare(
				" AND booking_date LIKE %s ",
				$date,
			);

		}else{
			
			if(! empty( $date_to ) && ! empty( $date_from ) && $date_to == $date_from):

				$query .= $wpdb->prepare(
					" AND booking_date BETWEEN %s AND %s",
					$date_to,
					$date_from
				);
			
			elseif ( ! empty( $date_to ) && ! empty( $date_from ) ) :
				$query .= $wpdb->prepare(
					" AND booking_date BETWEEN %s AND %s",
					$date_from,
					$date_to
				);
				
			elseif ( ! empty( $date_from ) ) :
				$query .= $wpdb->prepare(
					" AND booking_date >= %s",
					$date_from
				);
			elseif ( ! empty( $date_to ) ) :
				$query .= $wpdb->prepare(
					" AND booking_date <= %s",
					$date_to
				);
			endif;
		}


		if( ! empty( $time ) ){
			$query .= $wpdb->prepare(
				" AND timeslot LIKE %s ",
				$time,
			);

		}else{
			if ( ! empty( $time_to ) && ! empty( $time_from ) ) :
				$query .= $wpdb->prepare(
					" AND timeslot BETWEEN %s AND %s",
					$time_from,
					$time_to
				);
	
			elseif ( ! empty( $time_from ) ) :
				$query .= $wpdb->prepare(
					" AND timeslot >= %s",
					$time_from
				);
	
			elseif ( ! empty( $time_to ) ) :
				$query .= $wpdb->prepare(
					" AND timeslot <= %s",
					$time_to
				);
			endif;
		}

		$countQuery = $query;
		if( ! $count ) :
			$query .= $wpdb->prepare(
				" ORDER BY %i {$order} LIMIT %d OFFSET %d",
				$order_by,
				$per_page,
				$offset
			);
		endif;
		return apply_filters( 'wpb_get_bookings_query', array( 'paginateQuery' => $query, 'countQuery' => $countQuery ), $args );
	}

	
	public function get_booking($booking = false) {
		global $wpdb;
		if ( is_numeric( $booking ) ) :
			$this->set_id( $booking );
		endif;

		if ( ! empty( $this->cache_group ) ) :
			$cache_key = $this->get_booking_cache_key();

			if (!empty($cache_key)):
				$cached_booking = wp_cache_get($cache_key, $this->cache_group);
			endif;
		endif;

		try {
			if ( is_numeric( $booking ) ) :
				$query 	 = $wpdb->prepare( "SELECT * FROM %i WHERE id = %s",$wpdb->wpb_bookings, $booking );
				$booking = $wpdb->get_row( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
			endif;

			$booking = new WPB_Booking( $booking );

			if ( ! isset( $cached_booking ) && !empty( $this->cache_group ) ):
				wp_cache_set( $cache_key, $booking, $this->cache_group );
			endif;
			return $booking;
		} catch (Exception $e) {
			$error = new WP_Error('booking_error', $e->getMessage());
			return false;
		}
	}


	public function get_bookings( $args = array() ){
		global $wpdb;
		$query 		= $this->prepare_query( $args ); 
		$bookingIDs = $wpdb->get_results( $query['paginateQuery'] ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
		$bookings = new stdClass(); // Create a new stdClass object
		$bookings->results 		= [];
		$bookings->total 		= 0;
		$bookings->maxnumpages 	= 0;

		if ( ! empty( $bookingIDs ) ) :
			foreach ( $bookingIDs as $booking ) :
				$bookings->results[] = new WPB_Booking($booking);
			endforeach;

			$bookings->total 	   = count( $wpdb->get_results( $query['countQuery'] ) ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
			$bookings->maxnumpages = ceil( $bookings->total/$args['per_page'] );
		endif;
		return $bookings;
	}

	public function get_booking_subtotal_price($booking_type_id ) {
		return get_metadata( 'wpb_booking_type', $booking_type_id, 'price', true );
	}
	
	public function get_payment_mode() {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT payment_mode FROM {$wpdb->wpb_payments} WHERE bookings_id = %d", $this->id );
        return $wpdb->get_var( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
	}
	
	public function get_payment_status() {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT payment_status FROM {$wpdb->wpb_payments} WHERE bookings_id = %d", $this->id );
        return $wpdb->get_var( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
	}

	public function get_minimum_time_before_cancellation()
    {
        $settings = wpb_get_general_settings();
        $minimum_time_before_cancellation = isset($settings['minimum_time_before_cancellation']) ? $settings['minimum_time_before_cancellation'] : 0;

        $booking_date = $this->get_booking_date();
        $booking_timeslot = $this->get_timeslot();
        $booked_datetime_str = $booking_date . ' ' . $booking_timeslot;
    
        $timezone = new DateTimeZone(wpb_get_timezone());
        $booked_datetime = new DateTime($booked_datetime_str, $timezone);
        $current_datetime = new DateTime();
        $format = 'Y-m-d H:i:s';
        $current_date_time_formatted = $current_datetime->format($format);
        $booked_date_time_formatted = $booked_datetime->format($format);

        // Calculate the time difference in minutes
        $interval = $booked_datetime->diff($current_datetime);
        $time_difference_in_minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
    
    
        if ($time_difference_in_minutes <= $minimum_time_before_cancellation) {
            return false;
        }
    
        return true;
    }

	
}
