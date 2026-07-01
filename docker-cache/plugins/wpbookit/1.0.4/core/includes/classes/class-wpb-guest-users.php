<?php
/**
 * Regular Guest_Users
 *
 */

defined('ABSPATH') || exit;

/**
 * Guest_Users Class.
 */
class WPB_Guest_User extends WPB_Data
{

	/**
	 * ID for this object.
	 */
	protected $id = 0;

	/**
	 * Guest_Users Data array. This is the core guest data exposed in APIs.
	 */
	protected $data = array(
		'guest_id'      => 0,
		'guest_name'    => '',
		'guest_email'   => '',
		'guest_phone_number'   => '',
		'booking_type'  => '',
		'date_created'  => '00/00/0000',
	);

	/**
	 * Stores additional object_read
	 */
	protected $object_read = null;

	/**
	 * Stores meta in cache for future reads.
	 */
	protected $cache_group = 'guest';

	/**
	 * Get the guest if ID is passed, otherwise the guest is new and empty.
	 **/
	public function __construct($guest = 0)
	{
		if (is_numeric($guest) && $guest > 0):
			$this->set_id($guest);

		elseif ($guest instanceof self):
			$this->set_id($guest->get_id());

		elseif (!empty($guest->id)):
			$this->set_id($guest->id);

		else:
			$this->set_object_read(true);

		endif;

		if ($this->get_id() > 0):
			$this->read($guest);
		endif;
	}

	/**
	 * Set object read property.
	 */
	public function set_object_read($read = true)
	{
		$this->object_read = (bool) $read;
	}


	/**
	 * Set ID.
	 */
	public function set_id($id)
	{
		$this->id = absint($id);
	}

	/**
	 * Sets Guest guest_name.
	 */
	public function set_guest_name($guest_name)
	{
		$this->set_prop('guest_name', $guest_name);
	}

	/**
	 * Sets Guest guest_email.
	 */
	public function set_guest_email($guest_email)
	{
		$this->set_prop('guest_email', $guest_email);
	}

	/**
	 * Sets date_created.
	 */
	public function set_date_created($date_created)
	{
		$this->set_prop('date_created', $date_created);
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

	public function read($guest)
	{
		foreach ($guest as $prop => $value):
			if (array_key_exists($prop, $this->data)) {
				$this->changes[$prop] = $value;
			}
		endforeach;
	}

	/**
	 * Returns the unique ID for guest object.
	 **/
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * Gets Guest guest_name.
	 */
	public function get_guest_name($context = 'view')
	{
		return $this->get_prop('guest_name', $context);
	}

	/**
	 * Gets Guest guest_email.
	 */
	public function get_guest_email($context = 'view')
	{
		return $this->get_prop('guest_email', $context);
	}
	/**
	 * Gets Guest guest_email.
	 */
	public function get_guest_phone_number($context = 'view')
	{
		return $this->get_prop('guest_phone_number', $context);
	}

	/**
	 * Get guest_status.
	 */
	public function get_date_created($context = 'view')
	{
		return $this->get_prop('date_created', $context);
	}


	/**
	 * Get basic Guest data in array format.
	 */
	public function get_base_data()
	{
		return array_merge(
			array('id' => $this->get_id()),
			$this->data,
		);
	}

	/**
	 * Gets a prop for a getter method of the current object.
	 */
	protected function get_prop($prop, $context = 'view')
	{
		$value = null;

		if (array_key_exists($prop, $this->data)):
			$value = array_key_exists($prop, $this->changes) ? $this->changes[$prop] : $this->data[$prop];

			if ('view' === $context):
				$value = apply_filters('wpb_get_' . $prop, $value, $this);
			endif;
		endif;
		return $value;
	}


	public function prepare_query( $args = array(), $count = false ){
		global $wpdb;
        $guest_name 	= sanitize_text_field( $args['guest_name']??'' );
        $guest_email 	= sanitize_text_field( $args['guest_email']??'' );
		$order 		    = sanitize_text_field( $args['order'] );
		$order_by 		= sanitize_text_field( $args['order_by'] );
        $per_page 		= sanitize_text_field( $args['per_page'] );
		$paged 	        = sanitize_text_field( $args['paged'] );
        $offset         = sanitize_text_field( $args['offset'] );

		if(empty( $offset ) ) :
			$offset = ($paged - 1) * $per_page;
		endif;

        $query = "SELECT {$wpdb->wpb_guest_users}.*
			FROM {$wpdb->wpb_guest_users}
            WHERE 1 = 1";

        if ( $guest_name ):
            $query .= $wpdb->prepare(
                " AND guest_name LIKE %s",
                '%' . $guest_name . '%'
            );
        endif;
        if ( $guest_email ):
            $query .= $wpdb->prepare(
                " AND guest_email LIKE %s",
                '%' . $guest_email . '%'
            );
        endif;

        $countQuery = $query;
		if( ! $count && $per_page != '-1') :
			$query .= $wpdb->prepare(
				" ORDER BY %i {$order} LIMIT %d OFFSET %d",
				$order_by,
				$per_page,
				$offset
			);
		endif;
        return apply_filters( 'wpb_get_guest_user_query', array( 'paginateQuery' => $query, 'countQuery' => $countQuery ), $args );

    }

	public function get_guest_user($guest = false,$get_by_col=false )
	{
		global $wpdb;
		if($get_by_col==false && !is_numeric($guest)){
			return false;
		}

		try {
            if ( is_numeric( $guest ) ) :
				$query 	 = $wpdb->prepare( "SELECT * FROM %i WHERE %i = %s", $wpdb->wpb_guest_users, 'id' , $guest );
				$guest   = $wpdb->get_row( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			elseif($get_by_col!=false):
				$query 	 = $wpdb->prepare( "SELECT * FROM %i WHERE %i = %s", $wpdb->wpb_guest_users,($get_by_col==false)? 'id': $get_by_col , $guest );
				$guest   = $wpdb->get_row( $query ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			endif;


			return new WPB_Guest_User($guest);
			
		} catch (Exception $e) {
			$error = new WP_Error('guest_error', $e->getMessage());
			return false;
		}
	}

	public function get_guest_users( $args = array() ){
		global $wpdb;

		$query 		 = $this->prepare_query( $args );
		$guest_users = $wpdb->get_results( $query['paginateQuery'] ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

        // Create a new stdClass object
        $guestusers  = new stdClass(); 
		$guestusers->results 		= [];
		$guestusers->total 		    = 0;
		$guestusers->maxnumpages 	= 0;

		if ( ! empty( $guest_users ) ) :
			foreach ( $guest_users as $guestuser ) :
				$guestusers->results[] = new WPB_Guest_User($guestuser);
			endforeach;

			$guestusers->total 	     = count( $wpdb->get_results( $query['countQuery'] ) ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$guestusers->maxnumpages = ceil( $guestusers->total/$args['per_page'] );
		endif;
		return $guestusers;
	}

}
