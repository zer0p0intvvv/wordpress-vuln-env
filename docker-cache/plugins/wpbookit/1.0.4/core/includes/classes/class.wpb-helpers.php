<?php

// Exit if accessed directly.
if (!defined('ABSPATH'))
	exit;

/**
 * Class Wpbookit_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		WPBOOKIT
 * @subpackage	Classes/Wpbookit_Helpers
 * @author		Iqonic Design
 * @since		1.0.4
 */
class WPB_Helpers
{

	protected $wpb_user_roles;

	public function __construct()
	{
        add_action('init', [$this,'init']);
	}
    public function init() {
        $this->wpb_user_roles = apply_filters('wpb_users_roles', [
			'staff' => [
				'lable' => esc_html__('WPBookit Staff', 'wpbookit'),
				'role' => 'staff',
				'permission'=> [
					'read' 			  => true,
                	'manage_wpbookit' => true,
				]
			],
			'customer' => [
				'lable' => esc_html__('WPBookit Customer', 'wpbookit'),
				'role' => 'customer',
				'permission'=> [],
			],
		]);
    }

	public function get_role($role)
	{
		if (isset($this->wpb_user_roles[$role])) {
			return $this->wpb_user_roles[$role];
		}
		return false;
	}
	public function get_roles()
	{
		return $this->wpb_user_roles;
	}

	protected function wpb_get_metadata($meta_type, int $object_id, $meta_key = '') {
		global $wpdb;
		$bookings_meta_table = $wpdb->wpb_bookingsmeta;
		$booking_type_meta_table = $wpdb->wpb_booking_typemeta;

		if ($meta_type === 'booking') {
			$query = "SELECT * FROM {$bookings_meta_table} WHERE 1=1";
			if ($object_id) {
				$query .= $wpdb->prepare(
					" AND wpb_bookings_id = %d",
					$object_id
				);
			}

			if ($meta_key) {
				$query .= $wpdb->prepare(
					" AND meta_key = %s",
					$meta_key
				);
			}
		} elseif ($meta_type === 'booking_type') {
			$query = "SELECT * FROM {$booking_type_meta_table} WHERE 1=1";
			if ($object_id) {
				$query .= $wpdb->prepare(
					" AND booking_type_id = %d",
					$object_id
				);
			}

			if ($meta_key) {
				$query .= $wpdb->prepare(
					" AND meta_key = %s",
					$meta_key
				);
			}
		}

		if (empty($meta_key)) {
			$meta_value = $wpdb->get_results($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			return $meta_value;
		} else {
			$meta_data = $wpdb->get_row($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if (!is_null($meta_data)) {
				$meta_value = $meta_data->meta_value;
				return $meta_value;
			}
		}
	}

	public function get_staff_role() {
		return $this->get_role('staff')['role'];
	}

	public function get_customer_role(){
		return $this->get_role('customer')['role'];
	}

	public function wpb_get_booking_payment_details($bookings_id, $column = '') {
		global $wpdb;
		$payments = [];

		$query = "SELECT * FROM {$wpdb->wpb_payments} WHERE 1=1";

		if (empty($bookings_id)) {
			return;
		}

		$query .= $wpdb->prepare(
			" AND bookings_id = %d",
			$bookings_id
		);
		

		// Apply filter to the SQL query
		$query = apply_filters('wpb_get_booking_payment_details', $query, $column);

		// Execute the query
		$payments = $wpdb->get_row($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ($column):
			$payments = $payments->$column;
		endif;

		// Apply filter to the payment data
		$payments = apply_filters('wpb_get_booking_payment', $payments);

		return $payments;
	}

	public function print_pagination_dynamic_msg($total_posts, $item_per_page, $current_page)
	{
		$start_post_num = ($current_page - 1) * $item_per_page + 1;
		$end_post_num = min($current_page * $item_per_page, $total_posts);
        // translators: start post number placeholder:0, end post number placeholder:1 ,end post number placeholder:2
		echo esc_html( sprintf(__("Showing %1\$d - %2\$d of %3\$d entries", 'wpbookit'), $start_post_num, $end_post_num, $total_posts));
	}

	public function print_pagination($total_posts, $item_per_page = 10)
	{
		$pages = ceil($total_posts / $item_per_page);
		$query_params = [];
		wp_parse_str(wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $query_params);
		
		$page_attr = $query_params['page'] ?? '';
		$tab_attr = $query_params['tab'] ?? '';
		$current_page_value = $query_params['paged'] ?? '';

		if($total_posts<2){
			return false;
		}

		?>
		<ul class="pagination">
			<?php
			if (!empty($current_page_value) && $current_page_value != 1) {
				?>
				<li class="page-item">
					<a class="page-link"
						href="?page=<?php esc_html($page_attr); ?>&tab=<?php esc_html($tab_attr); ?>&paged=<?php esc_html(((int) $current_page_value - 1)); ?>"
						aria-label="Previous">
						<span aria-hidden="true">«</span>
					</a>
				</li>
				<?php
			}
			for ($i = 1; $i <= $pages; $i++): ?>
				<li class="page-item<?php esc_html($page_attr == $i ? ' active' : ''); ?>">
					<a href="?page=<?php esc_html($page_attr); ?>&tab=<?php esc_html($tab_attr); ?>&paged=<?php esc_html($i); ?>"
						class="page-link"><?php esc_html($i); ?></a>
				</li>
			<?php endfor;
			if ($current_page_value != $pages && $pages > 1) {
				?>
				<li class="page-item">
					<a class="page-link"
						href="?page=<?php esc_html($page_attr); ?>&tab=<?php esc_html($tab_attr); ?>&paged=<?php esc_html((int) $current_page_value + (empty($current_page_value) || $current_page_value == 0 ? 2 : 1)); ?>"
						aria-label="Next">
						<span aria-hidden="true">»</span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}
	public function wpb_get_all_weekdays() {
		$week_start_day = get_option('start_of_week'); 
		$weekdays = array();
		
		for ($i = 0; $i < 7; $i++) {
			$day_index = ($i + $week_start_day) % 7;
			$day_name = date('l', strtotime("Sunday +{$day_index} days")); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
			$translated_day = ucfirst(translate($day_name)); // phpcs:ignore  WordPress.WP.I18n.MissingArgDomain , WordPress.WP.I18n.NonSingularStringLiteralText , WordPress.WP.I18n.LowLevelTranslationFunction 
			$weekdays[strtolower($day_name)] = $translated_day;
		}
		
		return apply_filters('wpb_get_all_weekdays',$weekdays);
	}


    /**
     * Get the role of a user.
     *
     * This method retrieves the role of a user based on their user ID. If no user ID is provided,
     * it defaults to the current logged-in user. The function checks if the user has any roles,
     * intersects them with a predefined list of roles including 'administrator', and returns the first matched role.
     *
     * @param int $user_id The user ID. Defaults to 0, which means the current user.
     * @return string The user's role or an empty string if no role is found.
     */
    public function get_user_role( int $user_id = 0): string {
        // Use the provided user ID or get the current user ID if none is provided
        $user_id = $user_id > 0 ? $user_id : get_current_user_id();

        // Create a WP_User object for the user
        $user_obj = new WP_User($user_id);

        // Return an empty string if the user has no roles
        if (empty($user_obj->roles)) {
            return '';
        }

        // Get all defined roles and add 'administrator' to the list
        $all_roles = array_keys($this->get_roles());
        $all_roles[] = 'administrator';

        // Find the intersection of the user's roles and the predefined roles
        $kc_user_roles = array_intersect($all_roles, $user_obj->roles);

        // Get the first role from the intersection
        $role = array_shift($kc_user_roles);

        // Return an empty string if no role is found
        if (empty($role)) {
            return '';
        }

        // Apply a filter and return the user's role as a string
        return apply_filters('wpb_get_user_role', (string) $role);
    }


    /**
     * Send an unauthorized access response in JSON format.
     *
     * This method constructs a response array with an error status and a message
     * indicating that the user doesn't have permission to access the requested resource.
     * It then sends this response as a JSON object with a 403 HTTP status code.
     *
     * @return void
     */
    public static function send_unauthorize_access_response(): void{
        $response = array(
            'status'  => 'error',
            'message' => __('You don\'t have permission.', 'wpbookit'),
            'data'     => array(),
        );
        wp_send_json($response, 403 );
    }

    /**
     * Recursively sanitize an array of data.
     *
     * @param object|array $data The data to sanitize.
     * @return array|object The sanitized data.
     */
    public static function request_recursive_sanitize( object|array $data ): array|object {
        return is_array( $data ) ? self::request_recursive_sanitize_array( $data ) : self::request_recursive_sanitize_object( $data );
    }

    /**
     * Recursively sanitize an array of data.
     *
     * @param array $data The data to sanitize.
     * @return array The sanitized data.
     */
    public static function request_recursive_sanitize_array( array $data ): array {
        $sanitize_data = array();

        foreach ( $data as $key => $value ) {
            if ( is_null($value) || $value === '' ) {
                // If the value is an empty string, set it to ''.
                $sanitize_data[ $key ] = '';
            } elseif ( is_array( $value ) ) {
                // If the value is an array, recursively sanitize each element.
                $sanitize_data[ $key ] = self::request_recursive_sanitize_array( $value );
            } elseif ( is_object( $value ) ) {
                // If the value is an object, leave it as is.
                $sanitize_data[ $key ] = self::request_recursive_sanitize_object( $value );
            } elseif ( preg_match( '/<[^<]+>/', $value, $m ) !== 0 ) {
                // If the value contains HTML tags.
                $sanitize_data[ $key ] = wp_kses_post( $value );
            } elseif (str_contains(strtolower($key), 'email')) {
                // If the key contains 'email', sanitize the value as an email.
                $sanitize_data[ $key ] = sanitize_email( $value );
            } elseif (str_contains(strtolower($key), '_nonce')) {
                // If the key contains '_nonce', sanitize the value as a key.
                $sanitize_data[ $key ] = sanitize_key( $value );
            } else {
                // Otherwise, sanitize the value as a text field.
                $sanitize_data[ $key ] = sanitize_text_field( $value );
            }
        }

        return $sanitize_data;
    }


    /**
     * Recursively sanitize an object.
     *
     * @param object $data The object to sanitize.
     * @return object The sanitized object.
     */
    public static function request_recursive_sanitize_object( object $data ): object {
        // Initialize a new stdClass object to hold sanitized data.
        $sanitized_object = new stdClass();

        // Iterate through each property of the object.
        foreach ( $data as $key => $value ) {
            if ( is_null($value) || $value === '' ) {
                $sanitized_object->{$key} = '';
            } elseif ( is_object( $value ) ) {// Check if the value is another object.
                // Recursively sanitize the nested object.
                $sanitized_object->{$key} = self::request_recursive_sanitize_object( $value );
            } elseif ( is_array( $value ) ) {
                // Sanitize string data.
                $sanitized_object->{$key} = self::request_recursive_sanitize_array( $value );
            } elseif ( preg_match( '/<[^<]+>/', $value, $m ) !== 0 ) {
                // If the value contains HTML tags.
                $sanitized_object->{$key} = wp_kses_post( $value );
            } elseif (str_contains(strtolower($key), 'email')) {
                // If the key contains 'email', sanitize the value as an email.
                $sanitized_object->{$key} = sanitize_email( $value );
            } elseif (str_contains(strtolower($key), '_nonce')) {
                // If the key contains '_nonce', sanitize the value as a key.
                $sanitized_object->{$key} = sanitize_key( $value );
            } else {
                // For other types of data, keep them as is.
                $sanitized_object->{$key} = sanitize_text_field( $value );
            }
        }

        return $sanitized_object;
    }

    /**
     * Recursively replaces dynamic keys in the given content.
     *
     * @param array|string        $search The value being searched for.
     * @param array|string        $replace The replacement value.
     * @param object|array|string $content The content to search and replace within.
     * @return array|object|string The content with the replacements made.
     */
    public static function replace_dynamic_keys( array|string $search, array|string $replace, object|array|string $content ): object|array|string {
        if ( empty( $content ) ) {
            return $content;
        }

        if ( is_array( $content ) ) {
            // If content is an array, apply replacements recursively.
            foreach ( $content as $key => $value ) {
                $content[ $key ] = self::replace_dynamic_keys( $search, $replace, $value );
            }
        } elseif ( is_object( $content ) ) {
            // If content is an object, apply replacements to its properties recursively.
            foreach ( $content as $key => $value ) {
                $content->$key = self::replace_dynamic_keys( $search, $replace, $value );
            }
        } elseif ( is_string( $content ) ) {
            // If content is a string, perform the replacement.
            $content = str_replace( $search, $replace, $content );
        }

        return $content;
    }


    /**
     * Loads, optionally autoloads, and instantiates all classes in a specified folder within a given namespace.
     *
     * @param string $folder_name The folder containing the PHP class files.
     * @param string $full_namespace The full namespace of the classes within the folder.
     * @param bool $autoload Whether to autoload classes or manually include the files. Default is true.
     * @return void
     */
    public static function load_classes(string $folder_name, string $full_namespace, bool $autoload = true): void {
        // Get all PHP files in the directory
        $files = self::get_php_files_in_folder( $folder_name );

        foreach ($files as $file) {

            $class_name = basename($file, '.php');

            $full_class_name = apply_filters('wpb_update_dynamic_load_class_name', "{$full_namespace}{$class_name}", $file);

            if ( ! $autoload) {
                require_once $file;
            }

            if( class_exists($full_class_name) ){
                new $full_class_name();
            }else{
                wpb_error_log("Class {$full_class_name} not found");
            }
        }
    }

    /**
     * Retrieves all PHP files from a specified folder.
     *
     * @param string $folder_name The path to the folder where PHP files are located.
     * @return array An array of file paths if PHP files are found, or empty array if no files are found.
     */
    public static function get_php_files_in_folder( string $folder_name): array {
        // Use glob to get all PHP files in the specified directory without sorting the results
        $files = glob("{$folder_name}/*.php", GLOB_NOSORT);

        // Check if no PHP files were found
        if (empty($files)) {
            // Log an error message if no PHP files are found
            wpb_error_log("No PHP files found in the {$folder_name} folder");
            return array();
        }

        // Return the array of PHP file paths
        return $files;
    }

}
