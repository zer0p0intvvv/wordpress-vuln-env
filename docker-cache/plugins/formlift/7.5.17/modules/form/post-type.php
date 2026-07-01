<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Anything to do with taxonomy, post_type and meta boxes goes here!
 */
class FormLift_Form_Post_Type {

	/**
	 * Add the FormLift post type as well as add the campaign taxonomy
	 */
	public static function create_form_lift_post_type() {
		//forms
		$labels = array(
			'name'               => __( 'FormLift' ),
			'singular_name'      => __( 'Infusionsoft Form' ),
			'add_new'            => __( 'Add Form' ),
			'add_new_item'       => __( 'Add Form' ),
			'all_items'          => __( 'All Forms' ),
			'edit_item'          => __( 'Edit Form' ),
			'new_item'           => __( 'New Form' ),
			'view'               => __( 'View' ),
			'view_item'          => __( 'View Form' ),
			'search_items'       => __( 'Search Forms' ),
			'not_found'          => __( 'No Forms Found' ),
			'not_found_in_trash' => __( 'No Forms Found In Trash' ),
			'archives'           => __( 'Form Archives' )
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false, // it's not public, it shouldn't have it's own permalink, and so on
			'publicly_queryable'  => true,  // you should be able to query it
			'show_ui'             => true,  // you should be able to edit it in wp-admin
			'exclude_from_search' => true,  // you should exclude it from search results
			'show_in_nav_menus'   => false, // you shouldn't be able to add it to menus
			'has_archive'         => false, // it shouldn't have archive page
			'rewrite'             => array(
				'slug' => 'forms'
			), // it shouldn't have rewrite rules
			'show_in_admin_bar'   => false,
			'menu_icon'           => plugins_url( 'assets/images/icon-20x20.png', __FILE__ ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'supports'            => array(
				'title',
				'author',
//                'editor'
			)
		);

		register_post_type( 'infusion_form', $args );

		//campaigns
		$labels = array(
			'name'              => _x( 'Campaign', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Campaign', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Search Campaigns', 'textdomain' ),
			'all_items'         => __( 'All Campaigns', 'textdomain' ),
			'parent_item'       => __( 'Parent Campaign', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Campaign:', 'textdomain' ),
			'edit_item'         => __( 'Edit Campaign', 'textdomain' ),
			'update_item'       => __( 'Update Campaign', 'textdomain' ),
			'add_new_item'      => __( 'Add New Campaign', 'textdomain' ),
			'new_item_name'     => __( 'New Campaign Name', 'textdomain' ),
			'menu_name'         => __( 'Campaigns', 'textdomain' )
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'campaign' ),
		);

		register_taxonomy(
			'campaigns',
			array( 'infusion_form' ),
			$args
		);
	}

	/**
	 * Custom colunm for the Form list
	 *
	 * @param $columns array
	 *
	 * @return array
	 */
	public static function set_custom_edit_infusion_form_columns( $columns ) {
		$new                    = array();
		$new['cb']              = $columns['cb'];
		$new['title']           = $columns['title'];
		$new['short_code']      = __( 'Short-Code', 'short_code_domain' );
		$new['num_impressions'] = __( 'Impressions', 'impress_dom' );
		$new['num_subs']        = __( 'Submissions', 'submiss_dom' );
		$new['avg_conversion']  = __( 'Avg. Conversion Rate', 'converge_dom' );
		$new['campaigns']       = __( 'Campaigns', 'campaign_domain' );

		//$new['tracking_date'] = __('Tracking Since', 'tracking_domain');
		return $new;
	}

	/**
	 * What to display for each custum column
	 *
	 * @param $column
	 * @param $post_id
	 */
	public static function formlift_custom_column( $column, $post_id ) {
		$site_url = get_site_url();
		switch ( $column ) {
			case 'short_code' :
				$terms = "<input id='form_shortcode_area_{$post_id}' type='text' style='width:100%' value='[formlift id=\"{$post_id}\"]' onclick='copy_shortcode(\"#form_shortcode_area_{$post_id}\")' readonly/>";
				if ( is_string( $terms ) ) {
					echo $terms;
				} else {
					_e( 'Unable to get short code :(', 'short_code_domain' );
				}
				break;
			case 'num_subs' :

				$num_subs = formlift_get_form_submissions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $post_id );

				$terms = sprintf( '<p><b>%s</b></p>', intval( $num_subs ) );
				if ( is_string( $terms ) ) {
					echo $terms;
				} else {
					_e( 'no submissions.', 'formlift' );
				}
				break;
			case 'num_impressions' :

				$num_imps = formlift_get_form_impressions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $post_id );

				$terms = sprintf( '<p><b>%s</b></p>', intval( $num_imps ) );
				if ( is_string( $terms ) ) {
					echo $terms;
				} else {
					_e( 'no impressions', 'formlift' );
				}
				break;
			case 'avg_conversion' :

				$subs = formlift_get_form_submissions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $post_id );
				$imps = formlift_get_form_impressions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $post_id );

				if ( ! empty ( $subs ) ) {
					$convs = floor( ( intval( $subs ) / intval( $imps ) ) * 100 );
				} else {
					$convs = 0;
				}

				$terms = sprintf( '<p><b>%s&#37;</b></p>', $convs );
				if ( is_string( $terms ) ) {
					echo $terms;
				} else {
					_e( 'no conversions', 'formlift' );
				}
				break;
			case 'campaigns' :
				$taxonomy  = "campaigns";
				$post_type = get_post_type( $post_id );
				$terms     = get_the_terms( $post_id, $taxonomy );

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$post_terms[] = "<a href='$site_url/wp-admin/edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " . esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, 'edit' ) ) . "</a>";
					}
					echo join( '<br />', $post_terms );
				} else {
					echo '<p><i>No Campaign Set.</i></p>';
				}
				break;

		}
	}

	/**
	 * Adds the Fill Out Form and Reset Stats actions
	 *
	 * @param $actions
	 * @param $post
	 *
	 * @return array
	 */
	public static function formlift_action_row( $actions, $post ) {
		//check for your post type
		if ( $post->post_type == "infusion_form" && get_post_status( $post ) == 'publish' ) {
			$new_actions                         = array();
			$new_actions['edit']                 = $actions['edit'];
			$new_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];
			$new_actions['trash']                = $actions['trash'];
			//$new_actions['reset_stats'] = "<a style='color:limegreen;cursor:pointer' onclick='resetStats({$post->ID})'>Reset Stats</a>";
			$permalink                = get_permalink( $post->ID );
			$new_actions['fill_form'] = "<a style='color:blue;' href='{$permalink}'>View</a>";

			return $new_actions;
		} else {
			return $actions;
		}
	}

	/**
	 * Allow Forms to be sorted by Conversion Rate, Number of Impressions, and Number of Submissions
	 *
	 * @param $query WP_Query
	 */
	public static function formlift_slice_orderby( $query ) {

		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'num_subs' == $orderby ) {

			$query->set( 'meta_key', 'submissions' );
			$query->set( 'orderby', 'meta_value_num' );

		} else if ( 'num_impressions' == $orderby ) {

			$query->set( 'meta_key', 'impressions' );
			$query->set( 'orderby', 'meta_value_num' );

		} else if ( 'avg_conversion' == $orderby ) {

			$query->set( 'meta_key', 'conversion_rate' );
			$query->set( 'orderby', 'meta_value_num' );

		}
	}

	/**
	 * give columns sortable trait
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public static function formlift_my_sortable_num_subs( $columns ) {
		$columns['num_subs']        = 'num_subs';
		$columns['num_impressions'] = 'num_impressions';
		$columns['avg_conversion']  = 'avg_conversion';

		return $columns;
	}

	/**
	 * The FormLift save function for individual forms.
	 *
	 * @param $post_id
	 */
	public static function save( $post_id ) {
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) && get_post_type( $post_id ) == "infusion_form" && isset( $_POST['formlift_editor_nonce'] ) && wp_verify_nonce( $_POST['formlift_editor_nonce'], 'formlift_saving_form_fields' ) ) {
			do_action( 'formlift_before_save_form', $post_id );
			do_action( 'formlift_after_save_form', $post_id );
		}
	}
}

add_action( 'init', array( 'FormLift_Form_Post_Type', 'create_form_lift_post_type' ) );
add_filter( 'manage_infusion_form_posts_columns', array(
	'FormLift_Form_Post_Type',
	'set_custom_edit_infusion_form_columns'
) );
add_action( 'manage_infusion_form_posts_custom_column', array(
	'FormLift_Form_Post_Type',
	'formlift_custom_column'
), 10, 2 );
add_filter( 'post_row_actions', array( 'FormLift_Form_Post_Type', 'formlift_action_row' ), 10, 2 );
add_filter( 'manage_edit-infusion_form_sortable_columns', array(
	'FormLift_Form_Post_Type',
	'formlift_my_sortable_num_subs'
) );
add_action( 'pre_get_posts', array( 'FormLift_Form_Post_Type', 'formlift_slice_orderby' ) );
add_action( 'save_post', array( 'FormLift_Form_Post_Type', 'save' ) );