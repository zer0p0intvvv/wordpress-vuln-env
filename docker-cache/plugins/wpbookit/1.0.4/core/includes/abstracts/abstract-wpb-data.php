<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by
 * the different data store classes.
 *
 * @class       WPB_Data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract WPB Data Class
 *
 * Implemented by classes using the same CRUD(s) pattern.
 */
abstract class WPB_Data {

    /**
	 * ID for this object.
     */
    protected $id = 0;

    /**
	 * Core data for this object. Name value pairs (name + default value).
     */
    protected $data = array();

    /**
	 * This is the name of this object type.
	 */
	protected $object_type = 'data';

    /**
	 * Contains a reference to the data store for this class.
	 */
	protected $data_store;

    /**
	 * Extra data for this object.
	 */
    protected $extra_data = array();

    /**
	 * Stores meta in cache for future reads.
	 */
	protected $cache_group = '';

	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 */
	protected $meta_type = 'post';

    /**
	 * Stores additional meta data.
	 */
	protected $meta_data = null;

    /**
	 * Set to default_data on construct so we can track and reset data if needed.
	 */
	protected $default_data = array();

    /**
	 * Set to internal_meta_keys to exclude metadata from object.
	 */
	protected $internal_meta_keys = array();

	/**
	 * Core data changes for this object.
	 */
	protected $changes = array();

    /**
	 * Default constructor.
	 *
	 * @param int|object|array $read ID to load from the DB (optional) or already queried data.
	 */

	protected $db_table='aaa';

	
	public function __construct( $read = 0 ) {
		global $wpdb;

		$this->data         = array_merge( $this->data, $this->extra_data );
		$this->default_data = $this->data;
		
		$this->db_table= !empty( $wpdb->{'wpb_'.$this->object_type} ) ? $wpdb->{'wpb_'.$this->object_type} : $wpdb->prefix.'wpb_'.$this->object_type ;
	}

    /**
	 * Get the data store.
	 */
	public function get_data_store() {
		return $this->data_store;
	}

	/**
	 * Prefix for action and filter hooks on data.
	 */
	protected function get_hook_prefix() {
		return 'wpb_' . $this->object_type . '_get_';
	}

    /**
	 * Returns the unique ID for this object.
	 */
    public function get_id() {
		return $this->id;
	}

    // /**
	//  * Delete an object, set the ID to 0, and return result.
	//  */
	// public function delete( $force_delete = false ) {
	// 	/**
	// 	 * Filters whether an object deletion should take place. Equivalent to `pre_delete_post`.
	// 	 */
	// 	$check = apply_filters( "wpb_pre_delete_$this->object_type", null, $this, $force_delete );
	// 	if ( null !== $check ) :
	// 		return $check;
    //     endif;

	// 	if ( $this->data_store ) :
	// 		$this->data_store->delete( $this, array( 'force_delete' => $force_delete ) );
	// 		$this->set_id( 0 );
	// 		return true;
    //     endif;
	// 	return false;
	// }

	/**
	 * Returns all data for this object.
	 */
    public function get_data() {
		return array_merge( 
			array( 'id' => $this->get_id() ), 
			$this->data, 
			array( 
				'meta_data' => $this->get_meta_data() 
			) 
		);
	}

	/**
	 * Filter null meta values from array.
	 */
	protected function filter_null_meta( $meta ) {
		return ! is_null( $meta['value'] );
	}

    /**
	 * Get All Meta Data.
	 */
	public function get_meta_data() {
		$this->maybe_read_meta_data();
		return array_values( array_filter( $this->meta_data, array( $this, 'filter_null_meta' ) ) );
	}

    /**
	 * Read meta data if null.
	 */
	protected function maybe_read_meta_data() {
		if ( is_null( $this->meta_data ) ) :
			$this->read_meta_data();
        endif;
	}

	
	/**
	 * Helper function to initialize metadata entries from filtered raw meta data.
	 */
	public function init_meta_data( array $filtered_meta_data = array() ) {
		$this->meta_data = array();
		foreach ( $filtered_meta_data as $meta ) {
			$this->meta_data[] = array(
				'id'    => (int) $meta->meta_id,
				'key'   => $meta->meta_key,
				'value' => maybe_unserialize( $meta->meta_value ),
			);
		}
	}

    /**
	 * Read Meta Data from the database.
	 */
    public function read_meta_data( $force_read = false ) {
		$this->meta_data = array();
		$cache_loaded    = false;

		if ( ! $this->get_id() ) return;

		if ( ! empty( $this->cache_group ) ) :
			// Prefix by group allows invalidation by group until https://core.trac.wordpress.org/ticket/4476 is implemented.
			$cache_key = $this->get_meta_cache_key();
        endif;

		if ( ! $force_read && ! empty( $this->cache_group ) ) :
			$cached_meta  = wp_cache_get( $cache_key, $this->cache_group );
			$cache_loaded = is_array( $cached_meta );
		endif;

		// We filter the raw meta data again when loading from cache, in case we cached in an earlier version where filter conditions were different.
		$raw_meta_data = $cache_loaded ? $this->filter_raw_meta_data( $this,$cached_meta ) : $this->read_meta( $this );

		if ( is_array( $raw_meta_data ) ) :
			$this->init_meta_data( $raw_meta_data );

			if ( ! $cache_loaded && ! empty( $this->cache_group ) ) :
				wp_cache_set( $cache_key, $raw_meta_data, $this->cache_group );
            endif;
		endif;
	}

    /**
	 * Helper method to compute meta cache key. Different from WP Meta cache key in that meta data cached using this key also contains meta_id column.
	 */
	public function get_meta_cache_key() {
		if ( ! $this->get_id() ) return false;
		return self::generate_meta_cache_key( $this->get_id(), $this->cache_group );
	}

    /**
	 * Generate cache key from id and group.
	 */
    public static function generate_meta_cache_key( $id, $cache_group ) {
		return WPB_Cache_Handler::get_cache_prefix( $cache_group ) . WPB_Cache_Handler::get_cache_prefix( 'object_' . $id ) . 'object_meta_' . $id;
	}

    /**
	 * Returns array of expected data keys for this object.
	 */
	public function get_data_keys() {
		return array_keys( $this->data??[] );
	}

    /**
	 * Returns all "extra" data keys for an object (for sub objects like product types).
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
	}

	/**
	 * Internal meta keys we don't want exposed as part of meta_data. This is in
	 * addition to all data props with _ prefix.
	 */
	protected function prefix_key( $key ) {
		return '_' === substr( $key, 0, 1 ) ? $key : '_' . $key;
	}

    /**
	 * Helper method to filter internal meta keys from all meta data rows for the object.
	 **/
    public function filter_raw_meta_data( &$object, $raw_meta_data ) {
		$this->internal_meta_keys = array_unique(
			array_merge(
				array_map(
					array( $this, 'prefix_key' ),
					$this->get_data_keys()
				),
				$this->internal_meta_keys
			)
		);

		$meta_data = array_filter( $raw_meta_data, array( $this, 'exclude_internal_meta_keys' ) );
		return apply_filters( "wpb_wp_{$this->meta_type}_read_meta", $meta_data, $this );
	}

    /**
	 * Callback to remove unwanted meta data.
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return ! in_array( $meta->meta_key, $this->internal_meta_keys, true ) && 0 !== stripos( $meta->meta_key, 'wp_' );
	}

    /**
	 * Returns an array of meta for an object.
	 */
    public function read_meta( &$object ) {
		global $wpdb;
        $db_info       = $this->get_db_info();
		$raw_meta_data = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT meta_id, meta_key, meta_value
				FROM {$db_info['table']}
				WHERE {$db_info['object_id_field']} = %d
				ORDER BY {$db_info['meta_id_field']}",
				$object->get_id()
			)
		);

		return $this->filter_raw_meta_data( $object, $raw_meta_data );
	}

    /**
	 * Table structure is slightly different between meta types, this function will return what we need to know.
	 **/
    protected function get_db_info() {
		global $wpdb;

		$meta_id_field      = 'meta_id'; // for some reason users calls this umeta_id so we need to track this as well.
		$table              = $wpdb->prefix . 'wpb_';

        $table              .= $this->meta_type . 'meta';
		$object_id_field    = 'wpb_'.$this->meta_type . '_id';

		return array(
			'table'           => $table,
			'object_id_field' => $object_id_field,
			'meta_id_field'   => $meta_id_field,
		);
	}

	/**
	 * Return list of internal meta keys.
	 */
	public function get_internal_meta_keys() {
		return $this->internal_meta_keys;
	}
	
    /**
	 * Check if the key is an internal one.
	 */
	protected function is_internal_meta_key( $key ) {

		$internal_meta_key = ! empty( $key ) && in_array( $key, $this->get_internal_meta_keys(), true );

		if ( ! $internal_meta_key ) return false;

		$has_setter_or_getter = is_callable( array( $this, 'set_' . ltrim( $key, '_' ) ) ) || is_callable( array( $this, 'get_' . ltrim( $key, '_' ) ) );

		if ( ! $has_setter_or_getter ) return false;

		if ( in_array( $key, $this->legacy_datastore_props, true ) ) {
			return true; // return without warning because we don't want to break legacy code which was calling add/get/update/delete meta.
		}

		/* translators: %s: $key Key to check */
		wc_doing_it_wrong( __FUNCTION__, sprintf( __( 'Generic add/update/get meta methods should not be used for internal meta data, including "%s". Use getters and setters.', 'wpbookit' ), $key ), '3.2.0' );

		return true;
	}

	/**
	 * Get Meta Data by Key.
	 */
	public function get_meta( $key = '', $single = true, $context = 'view' ) {
		if ( $this->is_internal_meta_key( $key ) || 1 == 1 ) :
			$function = 'get_' . ltrim( $key, '_' );

			if ( is_callable( array( $this, $function ) ) ) :
				return $this->{$function}();
            endif;
		endif;

		$meta_data  = $this->get_meta_data();

		$array_keys = array_keys( wp_list_pluck( $meta_data, 'key' ), $key, true );
		$value      = $single ? null : array();

		if ( ! empty( $array_keys ) ) :
			// We don't use the $this->meta_data property directly here because we don't want meta with a null value (i.e. meta which has been deleted via $this->delete_meta_data()).
			if ( $single ) :
				$value = $meta_data[ current( $array_keys ) ]['value'];
			else :
				$value = array_intersect_key( $meta_data, array_flip( $array_keys ) );
            endif;
		endif;

		if ( 'view' === $context ) :
			$value = apply_filters( $this->get_hook_prefix() . $key, $value, $this );
        endif;
		return $value;
	}


	public function load( $args = array(), $defaults = array('status' =>'enable')) {
		global $wpdb;
		if($this->id==0){
			return false;
		}

		// Prepare the SQL query with pagination and user ID filtering if user_id is set
		 $query= $wpdb->prepare("SELECT * FROM %i WHERE id LIKE %d",$this->db_table,$this->id);	
		
		// Apply filter to the SQL query
		$query = apply_filters("wpb_get_{$this->object_type}_query", $query, $args);
	
		// Execute the query
		$this->data = $wpdb->get_row($query,ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return is_null($this->data); 

	}
	public function save( array $data )  {
		global $wpdb;
		if( $this->id !== 0 ){
			return false;
		}
        $response = $wpdb->insert( $this->db_table, $data );
        return $response ? $wpdb->insert_id : false;
	}

    public function update( array $data, array $where )  {
        global $wpdb;
        return $wpdb->update( $this->db_table, $data, $where );
    }

    public function delete( array $where )  {
        global $wpdb;
        return $wpdb->delete( $this->db_table, $where );
    }

    public function maybe_create_table(){

        if( method_exists( $this, 'schema' ) ){
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();
            $schema = esc_sql(str_replace(
                array( "\n", "\r", "\t" ),
                '',
                $this->schema()
            ));

            $query = $wpdb->prepare(
                "CREATE TABLE %i ( {$schema} ) {$charset_collate}",
                $this->db_table
            );

            $result = maybe_create_table( $this->db_table, $query );

            if( ! $result ){
                wpb_error_log( "Failed to create table {$this->db_table}, query: {$schema}, error: {$wpdb->last_error}" );
            }else{
                do_action( "wpb_after_table_created", $this->db_table );
            }
        }
    }

    public function delete_table(){
        global $wpdb;
        $sql = $wpdb->prepare(
            "DROP TABLE IF EXISTS %i",
            $this->db_table
        );
        do_action( "wpb_before_table_deleted", $this->db_table );
        $wpdb->query($sql); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        do_action( "wpb_after_table_deleted", $this->db_table );
    }
}
