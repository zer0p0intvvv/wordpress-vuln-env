<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/admin
 * @author     Chart Builder Team <info@ays-pro.com>
 */
class Chart_Builder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The capability of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $capability    The capability for users access to this plugin.
	 */
    private $capability;

	/**
	 * @var Chart_Builder_DB_Actions
	 */
	private $db_obj;

	/**
	 * @var Chart_Builder_Settings_DB_Actions
	 */
	private $settings_obj;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
		add_filter('set_screen_option_cb_charts_per_page', array(__CLASS__, 'set_screen'), 10, 3);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook_suffix) {
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        // Not enqueue styles if they are not on the current plugin page
        if (false === strpos($hook_suffix, $this->plugin_name)) return;
        //
		wp_enqueue_style( $this->plugin_name . '-normalize', plugin_dir_url( __FILE__ ) . 'css/normalize.css', array(), $this->version . time(), 'all' );
		wp_enqueue_style( $this->plugin_name . '-admin-general', plugin_dir_url( __FILE__ ) . 'css/admin-general.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-banner', plugin_dir_url( __FILE__ ) . 'css/banner.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-chart-builder-banner.css', plugin_dir_url(__FILE__) . 'css/chart-builder-banner.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-animate', plugin_dir_url(__FILE__) .  'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-font-awesome', plugin_dir_url(__FILE__) .  'css/ays-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-font-awesome-icons', plugin_dir_url(__FILE__) .  'css/ays-font-awesome-icons.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url(__FILE__) .  'css/ays-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-chosen', plugin_dir_url(__FILE__) .  'css/chosen.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-data-bootstrap', plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-jquery-ui.min', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');

		wp_enqueue_style( $this->plugin_name . '-layer', plugin_dir_url( __FILE__ ) . 'css/chart-builder-admin-layer.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chart-builder-admin.css', array(), $this->version . time(), 'all' );
        wp_enqueue_style( $this->plugin_name . "-pro-features", plugin_dir_url( __FILE__ ) . 'css/chart-builder-pro-features.css', array(), $this->version . time(), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix) {
		if (false !== strpos($hook_suffix, "plugins.php")){
            wp_enqueue_script( $this->plugin_name . '-sweetalert-js', plugin_dir_url( __FILE__ ) . 'js/chart-builder-sweetalert2.all.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, true);
			wp_localize_script($this->plugin_name . '-admin',  'chart_builder_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        // Not enqueue scripts if they are not on the current plugin page
        if (false === strpos($hook_suffix, $this->plugin_name)) return;
        //
        global $wp_version;
        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.5';
        $versionCompare = CBFunctions()->versionCompare( $version1, $operator, $version2 );

        if ( $versionCompare ) {
            wp_enqueue_script( $this->plugin_name.'-wp-load-scripts', plugin_dir_url(__FILE__) . 'js/load-scripts.js', array(), $this->version, true);
        }

        wp_enqueue_script( 'jquery' );

        /*
        ==========================================
           * Bootstrap
           * select2
           * jQuery DataTables
        ==========================================
        */
        wp_enqueue_script( $this->plugin_name . "-popper", plugin_dir_url(__FILE__) . 'js/popper.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . "-bootstrap", plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . '-select2js', plugin_dir_url( __FILE__ ) . 'js/ays-select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-chosen', plugin_dir_url( __FILE__ ) . 'js/chosen.jquery.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-datatable-min', plugin_dir_url( __FILE__ ) . 'js/chart-builder-datatable.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . "-db4.min.js", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . "-jquery-ui.min.js", plugin_dir_url( __FILE__ ) . 'js/jquery-ui.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . '-sweetalert-js', plugin_dir_url( __FILE__ ) . 'js/chart-builder-sweetalert2.all.min.js', array('jquery'), $this->version, true );

        wp_enqueue_script( $this->plugin_name . "-treeSortable", plugin_dir_url( __FILE__ ) . 'js/treeSortable.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . "-tippy-bundle", plugin_dir_url( __FILE__ ) . 'js/tippy-bundle.umd.js', array( 'jquery' ), $this->version, true );

		$table_col_mapping  = CBFunctions()->get_all_db_tables_column_mapping( 1 );
        $chart_banner_date = $this->ays_chart_update_banner_time();

		wp_enqueue_code_editor(
			array(
				'type' => 'sql',
				'codemirror' => array(
					'autofocus'         => true,
					'lineWrapping'      => true,
					'dragDrop'          => false,
					'matchBrackets'     => true,
					'autoCloseBrackets' => true,
					'extraKeys'         => array( 'Ctrl-Space' => 'autocomplete' ),
					'hintOptions'       => array( 'tables' => $table_col_mapping ),
				),
			)
		);

        wp_enqueue_script( $this->plugin_name . '-charts-google', plugin_dir_url(__FILE__) . 'js/google-chart.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-chart-js', plugin_dir_url(__FILE__) . 'js/chart-js.js', array('jquery'), $this->version, true);

		wp_enqueue_script( $this->plugin_name . '-functions', plugin_dir_url( __FILE__ ) . 'js/functions.js', array( 'jquery' ), $this->version, true );

        wp_register_script( $this->plugin_name . '-localized', '' );

        wp_localize_script( $this->plugin_name . '-localized', 'aysChartBuilderAdmin', array(
            'ajaxUrl'                            => admin_url( 'admin-ajax.php' ),
            'selectUser'                         => __( 'Select author', "chart-builder" ),
            'pleaseEnterMore'                    => __( "Please enter 1 or more characters", "chart-builder" ),
            'searching'                          => __( "Searching...", "chart-builder" ),
            'chartBannerDate'                    => $chart_banner_date,
            'selectUserRoles'                    => __( 'Select user roles', "chart-builder" ),
            'delete'                             => __( 'Delete', "chart-builder" ),
            'selectQuestionDefaultType'          => __( 'Select question default type', "chart-builder" ),
            'yes'                                => __( 'Yes', "chart-builder" ),
            'cancel'                             => __( 'Cancel', "chart-builder" ),
            'somethingWentWrong'                 => __( "Maybe something went wrong.", "chart-builder" ),
            'failed'                             => __( 'Failed', "chart-builder" ),
            'selectPage'                         => __( 'Select page', "chart-builder" ),
            'selectPostType'                     => __( 'Select post type', "chart-builder" ),
            'copied'                             => __( 'Copied!', "chart-builder"),
            'clickForCopy'                       => __( 'Click to copy', "chart-builder"),
            'selectForm'                         => __( 'Select form', "chart-builder"),
            'addImage'                           => __( 'Add Image', "chart-builder"),
            'editImage'                          => __( 'Edit Image', "chart-builder"),
            'confirmDelete'                      => __( 'Are you sure you want to delete the chart(s)?', "chart-builder"),
            'confirmRowDelete'                   => __( 'Are you sure you want to delete the row?', "chart-builder"),
            'confirmColDelete'                   => __( 'Are you sure you want to delete the column?', "chart-builder"),
            'minRowNotice'                       => __( 'Sorry, minimum count of rows should be 1', "chart-builder"),
            'minColNotice'                       => __( 'Sorry, minimum count of columns should be 1', "chart-builder"),
            'activated'                          => __( "Activated", "chart-builder" ),
            'errorMsg'                           => __( "Error", "chart-builder" ),
            'loadResource'                       => __( "Can't load resource.", "chart-builder" ),
            'somethingWentWrong'                 => __( "Maybe something went wrong.", "chart-builder" ),            
        ) );

        wp_localize_script( $this->plugin_name . '-localized', 'aysChartBuilderChartSettings', array(
            'types' => CBFunctions()->getAllowedTypes(),
            'max_selected_options' => 2,
            'l10n' => array(
                'invalid_source'      => esc_html__( 'You have entered invalid URL. Please, insert proper URL.', "chart-builder" ),
                'loading'             => esc_html__( 'Loading...', "chart-builder" ),
                'filter_config_error' => esc_html__( 'Please check the filters you have configured.', "chart-builder" ),
                'select_columns'      => esc_html__( 'Please select a few columns to include in the chart.', "chart-builder" ),
                'save_settings'       => __( 'You have modified the chart\'s settings. To modify the source/data again, you must save this chart and reopen it for editing. If you continue without saving the chart, you may lose your changes.', "chart-builder" ),
            ),
            'ajax' => array(
                'url' => admin_url( 'admin-ajax.php' ),
                'nonces' => array(
                    'filter_get_props' => wp_create_nonce( 'cbuilder-fetch-post-type-props' ),
                    'filter_get_data'  => wp_create_nonce( 'cbuilder-fetch-post-type-data' ),
                    'quiz_maker_get_data' => wp_create_nonce( 'cbuilder-fetch-quiz-maker-data' ),
                    'quiz_maker_save_data' => wp_create_nonce( 'cbuilder-save-quiz-maker-data' ),
                    'author_user_search' => wp_create_nonce( 'cbuilder-author-user-search' ),
                ),
                'actions' => array(
                    'filter_get_props' => 'fetch_post_type_props',
                    'filter_get_data' => 'fetch_post_type_data',
                    'quiz_maker_get_data'   => 'fetch_quiz_maker_data',
                    'quiz_maker_save_data'   => 'save_quiz_maker_data',
                    'author_user_search' => 'author_user_search'
                ),
            ),
            'db_query' => array(
                'tables' => $table_col_mapping,
            ),
        ) );
        
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin.js', array( 'jquery', $this->plugin_name . '-localized' ), $this->version . time(), true );
        wp_enqueue_script( $this->plugin_name . '-google', plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin-google.js', array( 'jquery', $this->plugin_name . '-localized' ), $this->version . time(), true );
        wp_enqueue_script( $this->plugin_name . '-chartjs', plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin-chartjs.js', array( 'jquery', $this->plugin_name . '-localized' ), $this->version . time(), true );
        wp_enqueue_script( $this->plugin_name . '-general-js', plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin-general.js', array( 'jquery', $this->plugin_name . '-localized' ), $this->version, true );

		if ( false !== strpos( $hook_suffix, 'settings' ) ) {
			wp_enqueue_script( $this->plugin_name . '-settings', plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin-settings.js', array( 'jquery' ), $this->version, true );

			wp_localize_script( $this->plugin_name . '-settings', 'aysChartBuilderAdminSettings', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'selectUserRoles'                   => __( 'Select user roles', $this->plugin_name ),
				'delete'                            => __( 'Delete', $this->plugin_name ),
				'selectQuestionDefaultType'         => __( 'Select question default type', $this->plugin_name ),
				'yes'                               => __( 'Yes', $this->plugin_name ),
				'cancel'                            => __( 'Cancel', $this->plugin_name ),
				'somethingWentWrong'                => __( "Maybe something went wrong.", $this->plugin_name ),
				'failed'                            => __( 'Failed', $this->plugin_name ),
				'selectPage'                        => __( 'Select page', $this->plugin_name ),
				'selectPostType'                    => __( 'Select post type', $this->plugin_name ),
				'copied'                            => __( 'Copied!', $this->plugin_name),
				'clickForCopy'                      => __( 'Click to copy', $this->plugin_name),
				'selectForm'                        => __( 'Select form', $this->plugin_name),
			) );
		}
	}

    /**
	 * De-register JavaScript files for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function disable_scripts($hook_suffix) {
        if (false !== strpos($hook_suffix, $this->plugin_name)) {
            if (is_plugin_active('ai-engine/ai-engine.php')) {
                wp_deregister_script('mwai');
                wp_deregister_script('mwai-vendor');
                wp_dequeue_script('mwai');
                wp_dequeue_script('mwai-vendor');
            }
        }
	}

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu(){

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        global $wpdb;
        $menu_item = __( 'Chart Builder', "chart-builder" );
        $this->capability = 'manage_options';

        add_menu_page(
            __( 'Chart Builder', "chart-builder" ),
            $menu_item,
            $this->capability,
            $this->plugin_name,
            array($this, 'display_plugin_charts_page'),
            CHART_BUILDER_ADMIN_URL . '/images/icons/ays_chart_logo_icon_bw.svg',
            '6.22'
        );

    }

    public function add_plugin_charts_submenu(){
        $hook_page_view = add_submenu_page(
            $this->plugin_name,
            __('All Charts', $this->plugin_name),
            __('All Charts', $this->plugin_name),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_charts_page')
        );

        add_action( "load-$hook_page_view", array( $this, 'screen_option_charts' ) );
    }

    public function add_plugin_add_new_submenu(){
        $hook_charts = add_submenu_page(
            $this->plugin_name,
            __('Add New', $this->plugin_name),
            __('Add New', $this->plugin_name),
            'manage_options',
            $this->plugin_name."&action=add",
            array($this, 'display_plugin_addnew_page')
        );
        add_action("load-$hook_charts", array( $this, 'add_tabs' ));
    }

    public function add_plugin_dashboard_submenu(){
        $hook_charts = add_submenu_page(
            $this->plugin_name,
            __('How to use', $this->plugin_name),
            __('How to use', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-dashboard',
            array($this, 'display_plugin_setup_page')
        );
        add_action("load-$hook_charts", array( $this, 'add_tabs' ));
    }

	public function add_plugin_general_settings_submenu(){
		$hook_settings = add_submenu_page( $this->plugin_name,
			__('General Settings', $this->plugin_name),
			__('General Settings', $this->plugin_name),
			'manage_options',
			$this->plugin_name . '-settings',
			array($this, 'display_plugin_settings_page')
		);
		add_action("load-$hook_settings", array($this, 'screen_option_settings'));
	}

    public function add_plugin_featured_plugins_submenu(){
        $hook_our_products = add_submenu_page( $this->plugin_name,
            __('Our products', $this->plugin_name),
            __('Our products', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-featured-plugins',
            array($this, 'display_plugin_featured_plugins_page') 
        );

        add_action("load-$hook_our_products", array( $this, 'add_tabs' ));
    }

    public function add_plugin_chart_features_submenu(){
        $hook_pro_features = add_submenu_page(
            $this->plugin_name,
            __('PRO Features', $this->plugin_name),
            __('PRO Features', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-chart-features',
            array($this, 'display_plugin_chart_features_page')
        );

        add_action("load-$hook_pro_features", array( $this, 'add_tabs' ));
    }

	public function screen_option_charts(){
		$option = 'per_page';
		$args = array(
			'label' => __('Charts', "chart-builder"),
			'default' => 5,
			'option' => 'cb_charts_per_page'
		);

		if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
			add_screen_option( $option, $args );
		}

		$this->db_obj = new Chart_Builder_DB_Actions( $this->plugin_name );
	}

	public function screen_option_settings(){
		$this->settings_obj = new Chart_Builder_Settings_DB_Actions( $this->plugin_name );
	}

    public function add_tabs() {
		$screen = get_current_screen();
	
		if ( ! $screen) {
			return;
		}
	}

	public function display_plugin_charts_page(){
        global $ays_chart_db_actions;

        $action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
		$id = (isset($_GET['id'])) ? absint( esc_attr($_GET['id']) ) : 0;

        if (isset($_POST['bulk_delete_confirm'])) {
            if (isset($_POST['bulk-delete']) && !empty($_POST['bulk-delete'])) {
                $ids = $_POST['bulk-delete'];
                foreach ($ids as $id) {
                    if ($id > 0) {
                        $this->db_obj->delete_item( $id );
                    }
                }
                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	            $url = esc_url_raw( add_query_arg( array(
		            "status" => 'all-deleted'
	            ), $url ) );
	            wp_redirect( $url );
                exit;
            }
        }

        switch ($action) {
            case 'trash':
                if( $id > 0 ){
                    $this->db_obj->trash_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'trashed'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'restore':
                if( $id > 0 ){
                    $this->db_obj->restore_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'restored'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'delete':
                if( $id > 0 ){
                    $this->db_obj->delete_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'deleted'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'publish':
                if( $id > 0 ){
                    $this->db_obj->publish_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'published'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'unpublish':
                if( $id > 0 ){
                    $this->db_obj->restore_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'unpublished'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'duplicate':
                if( $id > 0 ){
                    $this->db_obj->duplicate_item( $id );
                    $url = remove_query_arg( array('action', 'id', '_wpnonce') );
                    $url = esc_url_raw( add_query_arg( array(
                        "status" => 'duplicated'
                    ), $url ) );
                    wp_redirect( $url );
                    exit;
                }
                break;
            case 'add':
                include_once('partials/charts/actions/chart-builder-charts-actions.php');
                break;
            case 'edit':
                include_once('partials/charts/actions/chart-builder-charts-actions.php');
                break;
            default:
                include_once('partials/charts/chart-builder-charts-display.php');
        }
    }

    public function display_plugin_setup_page(){
        include_once('partials/chart-builder-admin-display.php');
    }

    public function display_plugin_addnew_page(){

        // $url = remove_query_arg( array('action', 'id', '_wpnonce') );
        // $url = esc_url_raw( add_query_arg( array(
        //     "action" => 'add'
        // ), $url ) );
        // wp_redirect( $url );
    }

	public function display_plugin_settings_page(){
		include_once('partials/settings/chart-builder-settings.php');
	}

    public function display_plugin_featured_plugins_page(){
        include_once('partials/features/chart-builder-plugin-featured-display.php');
    }

    public function display_plugin_chart_features_page(){
        include_once('partials/features/chart-builder-features-display.php');
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links( $links ){
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', "chart-builder") . '</a>',
            '<a href="https://ays-demo.com/chart-builder-demo/" target="_blank">' . __('Demo', "chart-builder") . '</a>',
            // '<a href="https://ays-pro.com/wordpress/chart-builder?utm_source=chart-free-dashboard&utm_medium=chart-plugins-page&utm_campaign=chart-buy-now" id="ays-chart-plugins-buy-now-button" target="_blank">' . __('Upgrade', "chart-builder") . '</a>',
            '<a href="https://ays-pro.com/wordpress/chart-builder?utm_source=chart-free-dashboard&utm_medium=chart-plugins-page&utm_campaign=chart-buy-now" id="ays-chart-plugins-buy-now-button" target="_blank">' . __('Upgrade 30% Sale', "chart-builder") . '</a>',
        );
        return array_merge( $settings_link, $links );

    }


    public static function set_screen($status, $option, $value){
        return $value;
    }

	public function ays_admin_ajax(){
		global $wpdb;

		$response = array(
			"status" => false
		);

		$function = isset($_REQUEST['function']) ? sanitize_text_field( $_REQUEST['function'] ) : null;

		if($function !== null){
			$response = array();
			if( is_callable( array( $this, $function ) ) ){
				$response = $this->$function();

	            ob_end_clean();
	            $ob_get_clean = ob_get_clean();
				echo json_encode( $response );
				wp_die();
			}

        }

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
		echo json_encode( $response );
		wp_die();
	}

    public function deactivate_plugin_option(){
        $request_value = esc_attr($_REQUEST['upgrade_plugin']);
        $upgrade_option = get_option( 'ays_chart_builder_upgrade_plugin', '' );
        if($upgrade_option === ''){
            add_option( 'ays_chart_builder_upgrade_plugin', $request_value );
        }else{
            update_option( 'ays_chart_builder_upgrade_plugin', $request_value );
        }
		ob_end_clean();
        $ob_get_clean = ob_get_clean();
        return json_encode( array( 'option' => get_option( 'ays_chart_builder_upgrade_plugin', '' ) ) );
		wp_die();
    }

    public function chart_builder_admin_footer($a){
        if(isset($_REQUEST['page'])){
            if(false !== strpos( sanitize_text_field( $_REQUEST['page'] ), $this->plugin_name)){
                ?>
                <div class="ays-chart-footer-support-box">
                    <span class="ays-chart-footer-link-row"><a href="https://wordpress.org/support/plugin/chart-builder" target="_blank"><?php echo __( "Support", "chart-builder"); ?></a></span>
                    <span class="ays-chart-footer-slash-row">/</span>
                    <span class="ays-chart-footer-link-row"><a href="https://ays-pro.com/wordpress-chart-builder-plugin-user-manual" target="_blank"><?php echo __( "Docs", "chart-builder"); ?></a></span>
                    <span class="ays-chart-footer-slash-row">/</span>
                    <span class="ays-chart-footer-link-row"><a href="https://ays-demo.com/chart-builder-plugin-suggestion-box" target="_blank"><?php echo __( "Suggest a Feature", "chart-builder"); ?></a></span>
                </div>
                <p style="font-size:13px;text-align:center;font-style:italic;">
                    <span style="margin-left:0px;margin-right:10px;" class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                    <span><?php echo esc_html(__( "If you love our plugin, please do big favor and rate us on", "chart-builder")); ?></span>
                    <a target="_blank" href='https://wordpress.org/support/plugin/chart-builder/reviews/'>WordPress.org</a>
                    <span class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                </p>
            <?php
            }
        }
    }

    // Chart Builder Elementor widget init
    public function chart_builder_el_widgets_registered() {
        // We check if the Elementor plugin has been installed / activated.
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
            // get our own widgets up and running:
            // copied from widgets-manager.php
            if ( class_exists( 'Elementor\Plugin' ) ) {
                if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
                    $elementor = Elementor\Plugin::instance();
                    if ( isset( $elementor->widgets_manager ) ) {
                        if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {
                            wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
                            $widget_file   = 'plugins/elementor/chart-builder-elementor.php';
                            $template_file = locate_template( $widget_file );
                            if ( !$template_file || !is_readable( $template_file ) ) {
                                $template_file = CHART_BUILDER_DIR . 'pb_templates/chart-builder-elementor.php';
                            }
                            if ( $template_file && is_readable( $template_file ) ) {
                                require_once $template_file;
                                Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor\Widget_Chart_Builder_Elementor() );
                            }
                        }
                    }
                }
            }
        }
    }

    public function fetch_post_type_props(){
	    $nonce = isset( $_POST['nonce'] ) ? wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cbuilder-fetch-post-type-props' ) : '';
	    if ( $nonce ) {
            $results = CBFunctions()->get_post_type_properties( sanitize_text_field( $_POST['post_type'] ) );

		    return array(
			    'success' => true,
			    'fields'  => $results,
		    );
	    }

	    return array(
		    'success' => false,
	    );
    }

	public static function get_max_id( $table ) {
        global $wpdb;
        $db_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . $table;

        $sql = "SELECT MAX(id) FROM {$db_table}";

        $result = intval( $wpdb->get_var( $sql ) );

        return $result;
    }

    public static function ays_restriction_string($type, $x, $length){
        $output = "";
        switch($type){
            case "char":                
                if(strlen($x)<=$length){
                    $output = $x;
                } else {
                    $output = substr($x,0,$length) . '...';
                }
                break;
            case "word":
                $res = explode(" ", $x);
                if(count($res)<=$length){
                    $output = implode(" ",$res);
                } else {
                    $res = array_slice($res,0,$length);
                    $output = implode(" ",$res) . '...';
                }
            break;
        }
        return $output;
    }

	public function ays_chart_sale_banner(){
        // if(isset($_POST['ays_chart_sale_btn']) && (isset( $_POST[CHART_BUILDER_NAME . '-sale-banner'] ) && wp_verify_nonce( $_POST[CHART_BUILDER_NAME . '-sale-banner'], CHART_BUILDER_NAME . '-sale-banner' )) && current_user_can( 'manage_options' )) {
        //     update_option('ays_chart_sale_btn', 1);
        //     update_option('ays_chart_sale_date', current_time( 'mysql' ));
        // }

        $ays_chart_sale_date = get_option('ays_chart_sale_date');

        $val = 60*60*24*5;

        $current_date = current_time( 'mysql' );
        $date_diff = strtotime($current_date) - intval(strtotime($ays_chart_sale_date)) ;
        
        $days_diff = $date_diff / $val;
    
        if(intval($days_diff) > 0 ){
            update_option('ays_chart_sale_btn', 0);
        }
    
        $ays_chart_builder_flag = intval(get_option('ays_chart_sale_btn'));
        if( $ays_chart_builder_flag == 0 ){
            if (isset($_GET['page']) && strpos($_GET['page'], CHART_BUILDER_NAME) !== false) {
                if( !(Chart_Builder_Admin::get_max_id('charts') <= 1) ){
                    // $this->ays_chart_sale_message_30_emma($ays_chart_builder_flag);
                    $this->ays_chart_sale_message20($ays_chart_builder_flag);
                    // $this->ays_chart_helloween_message($ays_chart_builder_flag);
                    // $this->ays_chart_black_friday_message($ays_chart_builder_flag);
                    // $this->ays_chart_christmas_message($ays_chart_builder_flag);
                    // $this->ays_chart_silver_bundle_message($ays_chart_builder_flag);
                }
            }
        }
    }

    public function ays_chart_dismiss_button(){

        $data = array(
            'status' => false,
        );

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ays_chart_dismiss_button') { 
            if( (isset( $_REQUEST['_ajax_nonce'] ) && wp_verify_nonce( $_REQUEST['_ajax_nonce'], $this->plugin_name . '-sale-banner' )) && current_user_can( 'manage_options' )){
                update_option('ays_chart_sale_btn', 1);
                update_option('ays_chart_sale_date', current_time( 'mysql' ));
                $data['status'] = true;
            }
        }

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode($data);
        wp_die();

    }

    // 30% Sale Emma
    public function ays_chart_sale_message_30_emma($ishmar){
        if($ishmar == 0 ){
            $content = array();
            $content[] = '<div id="ays-chart-new-mega-bundle-dicount-month-main-2024" class="notice notice-success is-dismissible ays_chart_dicount_info">';
                $content[] = '<div id="ays-chart-dicount-month" class="ays_chart_dicount_month">';

                    $content[] = '<div class="ays-chart-discount-box-sale-image"></div>';
                    $content[] = '<div class="ays-chart-dicount-wrap-box ays-chart-dicount-wrap-text-box">';

                        $content[] = '<div class="ays-chart-dicount-wrap-text-box-texts">';
                            $content[] = '<div>
                                            <a href="https://ays-pro.com/wordpress/chart-builder?utm_source=chart-free-dashboard&utm_medium=chart-sale-banner&utm_campaign=chart-sale-plugin-name" target="_blank" style="color:#30499B;">
                                            <span class="ays-chart-new-mega-bundle-limited-text">Limited</span> Offer for Chart Builder </a> <br> 
                                          </div>';
                        $content[] = '</div>';

                        $content[] = '<div style="font-size: 17px;">';
                            $content[] = '<img style="width: 24px;height: 24px;" src="' . esc_attr(CHART_BUILDER_ADMIN_URL) . '/images/icons/guarantee-new.png">';
                            $content[] = '<span style="padding-left: 4px; font-size: 14px; font-weight: 600;"> 30 Day Money Back Guarantee</span>';
                            
                        $content[] = '</div>';

                       

                        $content[] = '<div style="position: absolute;right: 10px;bottom: 1px;" class="ays-chart-dismiss-buttons-container-for-chart">';

                            $content[] = '<form action="" method="POST">';
                                $content[] = '<div id="ays-chart-dismiss-buttons-content">';
                                    if( current_user_can( 'manage_options' ) ){
                                        $content[] = '<button class="btn btn-link ays-button" name="ays_chart_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0; color: #30499B;
                                        ">Dismiss ad</button>';
                                        $content[] = wp_nonce_field( $this->plugin_name . '-sale-banner' ,  $this->plugin_name . '-sale-banner' );
                                    }
                                $content[] = '</div>';
                            $content[] = '</form>';
                            
                        $content[] = '</div>';

                    $content[] = '</div>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box ays-chart-dicount-wrap-countdown-box">';

                        $content[] = '<div id="ays-chart-countdown-main-container">';
                            $content[] = '<div class="ays-chart-countdown-container">';

                                $content[] = '<div id="ays-chart-countdown">';

                                    $content[] = '<div style="font-weight: 500;">';
                                        $content[] = __( "Offer ends in:", "chart-builder" );
                                    $content[] = '</div>';

                                    $content[] = '<ul>';
                                        $content[] = '<li><span id="ays-chart-countdown-days"></span>days</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-hours"></span>Hours</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-minutes"></span>Minutes</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-seconds"></span>Seconds</li>';
                                    $content[] = '</ul>';
                                $content[] = '</div>';

                                $content[] = '<div id="ays-chart-countdown-content" class="emoji">';
                                    $content[] = '<span>🚀</span>';
                                    $content[] = '<span>⌛</span>';
                                    $content[] = '<span>🔥</span>';
                                    $content[] = '<span>💣</span>';
                                $content[] = '</div>';

                            $content[] = '</div>';
                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box ays-chart-dicount-wrap-button-box">';
                        $content[] = '<a href="https://ays-pro.com/wordpress/chart-builder?utm_source=chart-free-dashboard&utm_medium=chart-sale-banner&utm_campaign=chart-sale-button" class="button button-primary ays-button" id="ays-button-top-buy-now" target="_blank">' . __( 'Buy Now !', "chart-builder" ) . '</a>';
                        $content[] = '<span >One-time payment</span>';
                    $content[] = '</div>';
                $content[] = '</div>';
            $content[] = '</div>';

            $content = implode( '', $content );
            echo html_entity_decode(esc_html( $content ));
        }        
    }

    // Self 20% sale
    public static function ays_chart_sale_message20($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-chart-dicount-month-main" class="notice notice-success is-dismissible ays_chart_dicount_info">';
                $content[] = '<div id="ays-chart-dicount-month" class="ays_chart_dicount_month">';
                    // $content[] = '<a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-chart-sale-banner-link"><img src="' . CHART_BUILDER_ADMIN_URL . '/images/ays_chart_logo.png"></a>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box ays-chart-dicount-wrap-text-box">';

                        $content[] = '<div class="ays-chart-dicount-sale-name-discount-box">';
							$content[] = '<span class="ays-chart-new-chart-pro-title">';
								$content[] = __( "<span><a href='https://ays-pro.com/wordpress/chart-builder?utm_source=chart-free-dashboard&utm_medium=chart-sale-banner&utm_campaign=chart-sale-plugin-name' target='_blank' style='color:#ffffff; text-decoration: underline;'>Chart Builder</a></span>", CHART_BUILDER_NAME );
							$content[] = '</span>';
							$content[] = '<div>';
								$content[] = '<img src="' . CHART_BUILDER_ADMIN_URL . '/images/ays-chart-banner-sale-30.svg" style="width: 70px;">';
							$content[] = '</div>';
						$content[] = '</div>';

                        $content[] = '<span class="ays-chart-new-chart-pro-desc">';
							$content[] = '<img class="ays-chart-new-chart-pro-guaranteeicon" src="' . CHART_BUILDER_ADMIN_URL . '/images/chart-builder-guaranteeicon.webp" style="width: 30px;">';
							$content[] = __( "30 Days Money Back Guarantee", CHART_BUILDER_NAME );
						$content[] = '</span>';
     
                        $content[] = '<div style="position: absolute;right: 10px;bottom: 1px;" class="ays-chart-dismiss-buttons-container-for-chart">';
                            $content[] = '<form method="POST">';
                                $content[] = '<div id="ays-chart-dismiss-buttons-content">';
                                    if (current_user_can( 'manage_options' )) {
                                        $content[] = '<button class="btn btn-link ays-button" name="ays_chart_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                                        $content[] = wp_nonce_field( CHART_BUILDER_NAME . '-sale-banner' ,  CHART_BUILDER_NAME . '-sale-banner' );
                                    }
                                $content[] = '</div>';
                            $content[] = '</form>';
                        $content[] = '</div>';

                    $content[] = '</div>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box ays-chart-dicount-wrap-countdown-box">';

                        $content[] = '<div id="ays-chart-countdown-main-container">';
                            $content[] = '<div class="ays-chart-countdown-container">';
                                $content[] = '<div id="ays-chart-countdown" style="display: block;">';
                                    $content[] = __( "Offer ends in:", CHART_BUILDER_NAME );
                                    
                                    $content[] = '<ul style="padding: 0">';
                                        $content[] = '<li><span id="ays-chart-countdown-days">0</span>' . __( 'Days', CHART_BUILDER_NAME ) . '</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-hours">0</span>' . __( 'Hours', CHART_BUILDER_NAME ) . '</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-minutes">0</span>' . __( 'Minutes', CHART_BUILDER_NAME ) . '</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-seconds">0</span>' . __( 'Seconds', CHART_BUILDER_NAME ) . '</li>';
                                    $content[] = '</ul>';
                                $content[] = '</div>';

                                $content[] = '<div id="ays-chart-countdown-content" class="emoji" style="display: none;">';
                                    $content[] = '<span>🚀</span>';
                                    $content[] = '<span>⌛</span>';
                                    $content[] = '<span>🔥</span>';
                                    $content[] = '<span>💣</span>';
                                $content[] = '</div>';
                            $content[] = '</div>';
                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box ays-chart-dicount-wrap-button-box">';
                        $content[] = '<a href="https://ays-pro.com/wordpress/chart-builder?utm_source=chart-free-dashboard&utm_medium=chart-sale-banner&utm_campaign=chart-sale-button" class="button button-primary ays-button" id="ays-button-top-buy-now" target="_blank" style="" >' . __( 'Buy Now', CHART_BUILDER_NAME ) . '</a>';
                        $content[] = '<span class="ays-chart-dicount-one-time-text">';
                            $content[] = __( "One-time payment", CHART_BUILDER_NAME );
                        $content[] = '</span>';
                    $content[] = '</div>';

                $content[] = '</div>';

            $content[] = '</div>';

            $content = implode( '', $content );
            echo $content;
        }
    }

    // Helloween banner
    public static function ays_chart_helloween_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-chart-dicount-month-main-helloween" class="notice notice-success is-dismissible ays_chart_dicount_info">';
                $content[] = '<div id="ays-chart-dicount-month-helloween" class="ays_chart_dicount_month_helloween">';
                    $content[] = '<div class="ays-chart-dicount-wrap-box-helloween-limited">';

                        $content[] = '<p>';
                            $content[] = __( "Limited Time 
                            <span class='ays-chart-dicount-wrap-color-helloween' style='color:#b2ff00;'>30%</span> 
                            <span>
                                SALE on
                            </span> 
                            <br>
                            <span style='' class='ays-chart-helloween-bundle'>
                                <a href='https://ays-pro.com/wordpress/chart-builder?utm_source=dashboard&utm_medium=chart-free&utm_campaign=helloween-sale-banner' target='_blank' class='ays-chart-dicount-wrap-color-helloween ays-chart-dicount-wrap-text-decoration-helloween' style='display:block; color:#b2ff00;margin-right:6px;'>
                                    Chart Builder
                                </a>
                            </span>", CHART_BUILDER_NAME );
                        $content[] = '</p>';
                        $content[] = '<p>';
                                $content[] = __( "Hurry up! 
                                                <a href='https://ays-pro.com/wordpress/chart-builder?utm_source=dashboard&utm_medium=chart-free&utm_campaign=helloween-sale-banner' target='_blank' style='color:#ffc700;'>
                                                    Check it out!
                                                </a>", CHART_BUILDER_NAME );
                        $content[] = '</p>';
                            
                    $content[] = '</div>';

                    
                    $content[] = '<div class="ays-chart-helloween-bundle-buy-now-timer">';
                        $content[] = '<div class="ays-chart-dicount-wrap-box-helloween-timer">';
                            $content[] = '<div id="ays-chart-countdown-main-container" class="ays-chart-countdown-main-container-helloween">';
                                $content[] = '<div class="ays-chart-countdown-container-helloween">';
                                    $content[] = '<div id="ays-chart-countdown">';
                                        $content[] = '<ul>';
                                            $content[] = '<li><p><span id="ays-chart-countdown-days"></span><span>days</span></p></li>';
                                            $content[] = '<li><p><span id="ays-chart-countdown-hours"></span><span>Hours</span></p></li>';
                                            $content[] = '<li><p><span id="ays-chart-countdown-minutes"></span><span>Mins</span></p></li>';
                                            $content[] = '<li><p><span id="ays-chart-countdown-seconds"></span><span>Secs</span></p></li>';
                                        $content[] = '</ul>';
                                    $content[] = '</div>';

                                    $content[] = '<div id="ays-chart-countdown-content" class="emoji">';
                                        $content[] = '<span>🚀</span>';
                                        $content[] = '<span>⌛</span>';
                                        $content[] = '<span>🔥</span>';
                                        $content[] = '<span>💣</span>';
                                    $content[] = '</div>';

                                $content[] = '</div>';

                            $content[] = '</div>';
                                
                        $content[] = '</div>';
                        $content[] = '<div class="ays-chart-dicount-wrap-box ays-buy-now-button-box-helloween">';
                            $content[] = '<a href="https://ays-pro.com/wordpress/chart-builder?utm_source=dashboard&utm_medium=chart-free&utm_campaign=helloween-sale-banner" class="button button-primary ays-buy-now-button-helloween" id="ays-button-top-buy-now-helloween" target="_blank" style="" >' . __( 'Buy Now !', CHART_BUILDER_NAME ) . '</a>';
                        $content[] = '</div>';
                    $content[] = '</div>';

                $content[] = '</div>';

                $content[] = '<div style="position: absolute;right: 0;bottom: 1px;"  class="ays-chart-dismiss-buttons-container-for-form-helloween">';
                    $content[] = '<form method="POST">';
                        $content[] = '<div id="ays-chart-dismiss-buttons-content-helloween">';
                        if( current_user_can( 'manage_options' ) ){
                            $content[] = '<button class="btn btn-link ays-button-helloween" name="ays_chart_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                            $content[] = wp_nonce_field( CHART_BUILDER_NAME . '-sale-banner' ,  CHART_BUILDER_NAME . '-sale-banner' );
                        }
                        $content[] = '</div>';
                    $content[] = '</form>';

                $content[] = '</div>';
                // $content[] = '<button type="button" class="notice-dismiss">';
                // $content[] = '</button>';
            $content[] = '</div>';

            $content = implode( '', $content );

            echo $content;
        }
    }

	// Black Friday banner
    public static function ays_chart_black_friday_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-chart-dicount-black-friday-month-main" class="notice notice-success is-dismissible ays_chart_dicount_info">';
                $content[] = '<div id="ays-chart-dicount-black-friday-month" class="ays_chart_dicount_month">';
                    $content[] = '<div class="ays-chart-dicount-black-friday-box">';
                        $content[] = '<div class="ays-chart-dicount-black-friday-wrap-box ays-chart-dicount-black-friday-wrap-box-80" style="width: 70%;">';
                            $content[] = '<div class="ays-chart-dicount-black-friday-title-row">' . __( 'Limited Time', "chart-builder" ) .' '. '<a href="https://ays-pro.com/wordpress/chart-builder?utm_medium=chart-free&utm_campaign=black-friday-sale-banner" class="ays-chart-dicount-black-friday-button-sale" target="_blank">' . __( 'Sale', "chart-builder" ) . '</a>' . '</div>';
                            $content[] = '<div class="ays-chart-dicount-black-friday-title-row">' . __( 'Chart Builder plugin', "chart-builder" ) . '</div>';
                        $content[] = '</div>';

                        $content[] = '<div class="ays-chart-dicount-black-friday-wrap-box ays-chart-dicount-black-friday-wrap-text-box">';
                            $content[] = '<div class="ays-chart-dicount-black-friday-text-row">' . __( '20% off', "chart-builder" ) . '</div>';
                        $content[] = '</div>';

                        $content[] = '<div class="ays-chart-dicount-black-friday-wrap-box" style="width: 25%;">';
                            $content[] = '<div id="ays-chart-countdown-main-container">';
                                $content[] = '<div class="ays-chart-countdown-container">';
                                    $content[] = '<div id="ays-chart-countdown" style="display: block;">';
                                        $content[] = '<ul>';
                                            $content[] = '<li><span id="ays-chart-countdown-days">0</span>' . __( 'Days', "chart-builder" ) . '</li>';
                                            $content[] = '<li><span id="ays-chart-countdown-hours">0</span>' . __( 'Hours', "chart-builder" ) . '</li>';
                                            $content[] = '<li><span id="ays-chart-countdown-minutes">0</span>' . __( 'Minutes', "chart-builder" ) . '</li>';
                                            $content[] = '<li><span id="ays-chart-countdown-seconds">0</span>' . __( 'Seconds', "chart-builder" ) . '</li>';
                                        $content[] = '</ul>';
                                    $content[] = '</div>';
                                    $content[] = '<div id="ays-chart-countdown-content" class="emoji" style="display: none;">';
                                        $content[] = '<span>🚀</span>';
                                        $content[] = '<span>⌛</span>';
                                        $content[] = '<span>🔥</span>';
                                        $content[] = '<span>💣</span>';
                                    $content[] = '</div>';
                                $content[] = '</div>';
                            $content[] = '</div>';
                        $content[] = '</div>';

                        $content[] = '<div class="ays-chart-dicount-black-friday-wrap-box" style="width: 25%;">';
                            $content[] = '<a href="https://ays-pro.com/wordpress/chart-builder?utm_medium=chart-free&utm_campaign=black-friday-sale-banner" class="ays-chart-dicount-black-friday-button-buy-now" target="_blank">' . __( 'Get Your Deal', "chart-builder" ) . '</a>';
                        $content[] = '</div>';
                    $content[] = '</div>';
                $content[] = '</div>';

                $content[] = '<div style="position: absolute;right: 0;bottom: 1px;"  class="ays-chart-dismiss-buttons-container-for-form-black-friday">';
                    $content[] = '<form method="POST">';
                        $content[] = '<div id="ays-chart-dismiss-buttons-content-black-friday">';
                            if( current_user_can( 'manage_options' ) ){
                                $content[] = '<button class="btn btn-link ays-button-black-friday" name="ays_chart_sale_btn" style="">' . __( 'Dismiss ad', "chart-builder" ) . '</button>';
                                $content[] = wp_nonce_field( CHART_BUILDER_NAME . '-sale-banner' ,  CHART_BUILDER_NAME . '-sale-banner' );
                            }
                        $content[] = '</div>';
                    $content[] = '</form>';
                $content[] = '</div>';
            $content[] = '</div>';

            $content = implode( '', $content );

            echo $content;
        }
    }

    // Christmas banner
    public static function ays_chart_christmas_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-chart-dicount-christmas-month-main" class="notice notice-success is-dismissible ays_chart_dicount_info">';
                $content[] = '<div id="ays-chart-dicount-christmas-month" class="ays_chart_dicount_month">';
                    $content[] = '<div class="ays-chart-dicount-christmas-box">';
                        $content[] = '<div class="ays-chart-dicount-christmas-wrap-box ays-chart-dicount-christmas-wrap-box-80">';
                            $content[] = '<div class="ays-chart-dicount-christmas-title-row">' . __( 'Limited Time', CHART_BUILDER_NAME ) .' '. '<a href="https://ays-pro.com/wordpress/chart-builder" class="ays-chart-dicount-christmas-button-sale" target="_blank">' . __( '40%', CHART_BUILDER_NAME ) . '</a>' . ' SALE</div>';
                            $content[] = '<div class="ays-chart-dicount-christmas-title-row">' . __( 'Chart Builder Plugin', CHART_BUILDER_NAME ) . '</div>';
                        $content[] = '</div>';

                        $content[] = '<div class="ays-chart-dicount-christmas-wrap-box" style="width: 25%;">';
                            $content[] = '<div id="ays-chart-countdown-main-container">';
                                $content[] = '<div class="ays-chart-countdown-container">';
                                    $content[] = '<div id="ays-chart-countdown" style="display: block;">';
                                        $content[] = '<ul>';
                                            $content[] = '<li><span id="ays-chart-countdown-days"></span>' . __( 'Days', CHART_BUILDER_NAME ) . '</li>';
                                            $content[] = '<li><span id="ays-chart-countdown-hours"></span>' . __( 'Hours', CHART_BUILDER_NAME ) . '</li>';
                                            $content[] = '<li><span id="ays-chart-countdown-minutes"></span>' . __( 'Minutes', CHART_BUILDER_NAME ) . '</li>';
                                            $content[] = '<li><span id="ays-chart-countdown-seconds"></span>' . __( 'Seconds', CHART_BUILDER_NAME ) . '</li>';
                                        $content[] = '</ul>';
                                    $content[] = '</div>';
                                    $content[] = '<div id="ays-chart-countdown-content" class="emoji" style="display: none;">';
                                        $content[] = '<span>🚀</span>';
                                        $content[] = '<span>⌛</span>';
                                        $content[] = '<span>🔥</span>';
                                        $content[] = '<span>💣</span>';
                                    $content[] = '</div>';
                                $content[] = '</div>';
                            $content[] = '</div>';
                        $content[] = '</div>';

                        $content[] = '<div class="ays-chart-dicount-christmas-wrap-box" style="width: 25%;">';
                            $content[] = '<a href="https://ays-pro.com/wordpress/chart-builder" class="ays-chart-dicount-christmas-button-buy-now" target="_blank">' . __( 'BUY NOW!', CHART_BUILDER_NAME ) . '</a>';
                        $content[] = '</div>';
                    $content[] = '</div>';
                $content[] = '</div>';

                $content[] = '<div style="position: absolute;right: 0;bottom: 1px;"  class="ays-chart-dismiss-buttons-container-for-form-christmas">';
                    $content[] = '<form method="POST">';
                        $content[] = '<div id="ays-chart-dismiss-buttons-content-christmas">';
                            $content[] = '<button class="btn btn-link ays-button-christmas" name="ays_chart_sale_btn" style="">' . __( 'Dismiss ad', CHART_BUILDER_NAME ) . '</button>';
                        $content[] = '</div>';
                    $content[] = '</form>';
                $content[] = '</div>';
            $content[] = '</div>';

            $content = implode( '', $content );

            echo $content;
        }
    }

    // Silver Bundle
    public function ays_chart_silver_bundle_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-chart-dicount-month-main" class="notice notice-success is-dismissible ays_chart_dicount_info">';
                $content[] = '<div id="ays-chart-dicount-month" class="ays_chart_dicount_month">';
                    $content[] = '<a href="https://ays-pro.com/silver-bundle" target="_blank" class="ays-chart-sale-banner-link"><img src="' . CHART_BUILDER_ADMIN_URL . '/images/silver_bundle_logo_box.png"></a>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box">';

                        $content[] = '<strong style="font-weight: bold;">';
                            $content[] = __( "Limited Time <span style='color:#E85011;'>50%</span> SALE on <br><span><a href='https://ays-pro.com/silver-bundle' target='_blank' style='color:#E85011; text-decoration: underline;'>Silver Bundle</a></span> (Quiz + Chart + Form)!", CHART_BUILDER_NAME );
                        $content[] = '</strong>';

                        $content[] = '<br>';

                        $content[] = '<strong>';
                                $content[] = __( "Hurry up! <a href='https://ays-pro.com/silver-bundle' target='_blank'>Check it out!</a>", CHART_BUILDER_NAME );
                        $content[] = '</strong>';

                        $content[] = '<div style="position: absolute;right: 10px;bottom: 1px;" class="ays-chart-dismiss-buttons-container-for-form">';

                            $content[] = '<form method="POST">';
                                $content[] = '<div id="ays-chart-dismiss-buttons-content">';
                                    $content[] = '<button class="btn btn-link ays-button" name="ays_chart_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                                $content[] = '</div>';
                            $content[] = '</form>';
                            
                        $content[] = '</div>';

                    $content[] = '</div>';

                    $content[] = '<div class="ays-chart-dicount-wrap-box">';

                        $content[] = '<div id="ays-chart-countdown-main-container">';
                            $content[] = '<div class="ays-chart-countdown-container">';

                                $content[] = '<div id="ays-chart-countdown">';
                                    $content[] = '<ul>';
                                        $content[] = '<li><span id="ays-chart-countdown-days"></span>days</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-hours"></span>Hours</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-minutes"></span>Minutes</li>';
                                        $content[] = '<li><span id="ays-chart-countdown-seconds"></span>Seconds</li>';
                                    $content[] = '</ul>';
                                $content[] = '</div>';

                                $content[] = '<div id="ays-chart-countdown-content" class="emoji">';
                                    $content[] = '<span>🚀</span>';
                                    $content[] = '<span>⌛</span>';
                                    $content[] = '<span>🔥</span>';
                                    $content[] = '<span>💣</span>';
                                $content[] = '</div>';

                            $content[] = '</div>';
                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    $content[] = '<a href="https://ays-pro.com/silver-bundle" class="button button-primary ays-button" id="ays-button-top-buy-now" target="_blank" style="height: 32px; display: flex; align-items: center; font-weight: 500; " >' . __( 'Buy Now !', CHART_BUILDER_NAME ) . '</a>';
                $content[] = '</div>';
            $content[] = '</div>';

            $content = implode( '', $content );
            echo $content;
        }
    }

    public function ays_chart_update_banner_time(){

        $date = time() + ( 3 * 24 * 60 * 60 ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS);
        // $date = time() + ( 60 ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS); // for testing | 1 min
        $next_3_days = date('M d, Y H:i:s', $date);

        $ays_chart_banner_time = get_option('ays_chart_20_bundle_banner_time');

        if ( !$ays_chart_banner_time || is_null( $ays_chart_banner_time ) ) {
            update_option('ays_chart_20_bundle_banner_time', $next_3_days ); 
        }

        $get_ays_chart_banner_time = get_option('ays_chart_20_bundle_banner_time');

        $val = 60*60*24*0.5; // half day
        // $val = 60; // for testing | 1 min

        $current_date = current_time( 'mysql' );
        $date_diff = strtotime($current_date) - intval(strtotime($get_ays_chart_banner_time));

        $days_diff = $date_diff / $val;
        if(intval($days_diff) > 0 ){
            update_option('ays_chart_20_bundle_banner_time', $next_3_days);
            $get_ays_chart_banner_time = get_option('ays_chart_20_bundle_banner_time');
        }

        return $get_ays_chart_banner_time;
    }

    public function author_user_search() {
        check_ajax_referer( 'cbuilder-author-user-search', 'security' );
        $params = $_REQUEST['params'];
        $search = isset($params['search']) && $params['search'] != '' ? sanitize_text_field( $params['search'] ) : null;
        $checked = isset($params['val']) && $params['val'] !='' ? sanitize_text_field( $params['val'] ) : null;
        $args = 'search=';
        if ($search !== null) {
            $args .= '*';
            $args .= $search;
            $args .= '*';
        }

        $users = get_users($args);

        $content_text = array(
            'results' => array()
        );

        foreach ($users as $key => $value) {
            if ($checked !== null) {
                if ( !is_array( $checked ) ) {
                    $checked2 = $checked;
                    $checked = array();
                    $checked[] = absint($checked2);
                }
                if (in_array($value->ID, $checked)) {
                    continue;
                } else {
                    $content_text['results'][] = array(
                        'id' => $value->ID,
                        'text' => $value->data->display_name,
                    );
                }
            } else {
                $content_text['results'][] = array(
                    'id' => $value->ID,
                    'text' => $value->data->display_name,
                );
            }
        }

        ob_end_clean();
        echo json_encode($content_text);
        wp_die();
    }

    /**
     * Determine if the plugin/addon installations are allowed.
     *
     * @since 6.4.0.4
     *
     * @param string $type Should be `plugin` or `addon`.
     *
     * @return bool
     */
    public static function ays_chart_can_install( $type ) {

        return self::ays_chart_can_do( 'install', $type );
    }

    /**
     * Determine if the plugin/addon activations are allowed.
     *
     * @since 6.4.0.4
     *
     * @param string $type Should be `plugin` or `addon`.
     *
     * @return bool
     */
    public static function ays_chart_can_activate( $type ) {

        return self::ays_chart_can_do( 'activate', $type );
    }

    /**
     * Determine if the plugin/addon installations/activations are allowed.
     *
     * @since 6.4.0.4
     *
     * @param string $what Should be 'activate' or 'install'.
     * @param string $type Should be `plugin` or `addon`.
     *
     * @return bool
     */
    public static function ays_chart_can_do( $what, $type ) {

        if ( ! in_array( $what, array( 'install', 'activate' ), true ) ) {
            return false;
        }

        if ( ! in_array( $type, array( 'plugin', 'addon' ), true ) ) {
            return false;
        }

        $capability = $what . '_plugins';

        if ( ! current_user_can( $capability ) ) {
            return false;
        }

        // Determine whether file modifications are allowed and it is activation permissions checking.
        if ( $what === 'install' && ! wp_is_file_mod_allowed( 'ays_chart_can_install' ) ) {
            return false;
        }

        // All plugin checks are done.
        if ( $type === 'plugin' ) {
            return true;
        }
        return false;
    }

    /**
     * Activate plugin.
     *
     * @since 1.0.0
     * @since 6.4.0.4 Updated the permissions checking.
     */
    public function ays_chart_activate_plugin() {

        // Run a security check.
        check_ajax_referer( $this->plugin_name . '-install-plugin-nonce', sanitize_key( $_REQUEST['_ajax_nonce'] ) );

        // Check for permissions.
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'chart-builder' ) );
        }

        $type = 'addon';

        if ( isset( $_POST['plugin'] ) ) {

            if ( ! empty( $_POST['type'] ) ) {
                $type = sanitize_key( $_POST['type'] );
            }

            $plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
            $activate = activate_plugins( $plugin );

            if ( ! is_wp_error( $activate ) ) {
                if ( $type === 'plugin' ) {
                    wp_send_json_success( esc_html__( 'Plugin activated.', 'chart-builder' ) );
                } else {
                        ( esc_html__( 'Addon activated.', 'chart-builder' ) );
                }
            }
        }

        if ( $type === 'plugin' ) {
            wp_send_json_error( esc_html__( 'Could not activate the plugin. Please activate it on the Plugins page.', 'chart-builder' ) );
        }

        wp_send_json_error( esc_html__( 'Could not activate the addon. Please activate it on the Plugins page.', 'chart-builder' ) );
    }

    /**
     * Install addon.
     *
     * @since 1.0.0
     * @since 6.4.0.4 Updated the permissions checking.
     */
    public function ays_chart_install_plugin() {

        // Run a security check.
        check_ajax_referer( $this->plugin_name . '-install-plugin-nonce', sanitize_key( $_REQUEST['_ajax_nonce'] ) );

        $generic_error = esc_html__( 'There was an error while performing your request.', 'chart-builder' );
        $type          = ! empty( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';

        // Check if new installations are allowed.
        if ( ! self::ays_chart_can_install( $type ) ) {
            wp_send_json_error( $generic_error );
        }

        $error = $type === 'plugin'
            ? esc_html__( 'Could not install the plugin. Please download and install it manually.', 'chart-builder' )
            : "";

        $plugin_url = ! empty( $_POST['plugin'] ) ? esc_url_raw( wp_unslash( $_POST['plugin'] ) ) : '';

        if ( empty( $plugin_url ) ) {
            wp_send_json_error( $error );
        }

        // Prepare variables.
        $url = esc_url_raw(
            add_query_arg(
                [
                    'page' => 'chart-builder-featured-plugins',
                ],
                admin_url( 'admin.php' )
            )
        );

        ob_start();
        $creds = request_filesystem_credentials( $url, '', false, false, null );

        // Hide the filesystem credentials form.
        ob_end_clean();

        // Check for file system permissions.
        if ( $creds === false ) {
            wp_send_json_error( $error );
        }
        
        if ( ! WP_Filesystem( $creds ) ) {
            wp_send_json_error( $error );
        }

        /*
         * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
         */
        require_once CHART_BUILDER_DIR . 'includes/admin/class-chart-builder-upgrader.php';
        require_once CHART_BUILDER_DIR . 'includes/admin/class-chart-builder-install-skin.php';
        require_once CHART_BUILDER_DIR . 'includes/admin/class-chart-builder-skin.php';


        // Do not allow WordPress to search/download translations, as this will break JS output.
        remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

        // Create the plugin upgrader with our custom skin.
        $installer = new ChartBuilder\Helpers\ChartBuilderPluginSilentUpgrader( new Chart_Builder_Install_Skin() );

        // Error check.
        if ( ! method_exists( $installer, 'install' ) ) {
            wp_send_json_error( $error );
        }

        $installer->install( $plugin_url );

        // Flush the cache and return the newly installed plugin basename.
        wp_cache_flush();

        $plugin_basename = $installer->plugin_info();

        if ( empty( $plugin_basename ) ) {
            wp_send_json_error( $error );
        }

        $result = array(
            'msg'          => $generic_error,
            'is_activated' => false,
            'basename'     => $plugin_basename,
        );

        // Check for permissions.
        if ( ! current_user_can( 'activate_plugins' ) ) {
            $result['msg'] = $type === 'plugin' ? esc_html__( 'Plugin installed.', 'chart-builder' ) : "";

            wp_send_json_success( $result );
        }

        // Activate the plugin silently.
        $activated = activate_plugin( $plugin_basename );
        remove_action( 'activated_plugin', array( 'ays_sccp_activation_redirect_method', 'gallery_p_gallery_activation_redirect_method', 'poll_maker_activation_redirect_method' ), 100 );

        if ( ! is_wp_error( $activated ) ) {

            $result['is_activated'] = true;
            $result['msg']          = $type === 'plugin' ? esc_html__( 'Plugin installed and activated.', 'chart-builder' ) : esc_html__( 'Addon installed and activated.', 'chart-builder' );

            wp_send_json_success( $result );
        }

        // Fallback error just in case.
        wp_send_json_error( $result );
    }

    /**
     * List of AM plugins that we propose to install.
     *
     * @since 6.4.0.4
     *
     * @return array
     */
    protected function get_am_plugins() {
        if ( !isset( $_SESSION ) ) {
            session_start();
        }

        $images_url = CHART_BUILDER_ADMIN_URL . '/images/icons/';

        $plugin_slug = array(
            'quiz-maker',
            'survey-maker',
            'poll-maker',
            'ays-popup-box',
            'secure-copy-content-protection',
            'gallery-photo-gallery',
            'ays-chatgpt-assistant',
            'easy-form',
        );

        $plugin_url_arr = array();
        foreach ($plugin_slug as $key => $slug) {
            if ( isset( $_SESSION['ays_chart_our_product_links'] ) && !empty( $_SESSION['ays_chart_our_product_links'] ) 
                && isset( $_SESSION['ays_chart_our_product_links'][$slug] ) && !empty( $_SESSION['ays_chart_our_product_links'][$slug] ) ) {
                $plugin_url = (isset( $_SESSION['ays_chart_our_product_links'][$slug] ) && $_SESSION['ays_chart_our_product_links'][$slug] != "") ? esc_url( $_SESSION['ays_chart_our_product_links'][$slug] ) : "";
            } else {
                $latest_version = $this->ays_chart_get_latest_plugin_version($slug);
                $plugin_url = 'https://downloads.wordpress.org/plugin/'. $slug .'.zip';
                if ( $latest_version != '' ) {
                    $plugin_url = 'https://downloads.wordpress.org/plugin/'. $slug .'.'. $latest_version .'.zip';
                    $_SESSION['ays_chart_our_product_links'][$slug] = $plugin_url;
                }
            }

            $plugin_url_arr[$slug] = $plugin_url;
        }

        $plugins_array = array(
            'quiz-maker/quiz-maker.php'        => array(
                'icon'        => $images_url . 'icon-quiz-128x128.png',
                'name'        => __( 'Quiz Maker', "easy-form" ),
                'desc'        => __( 'Create powerful and engaging quizzes, tests, and exams in minutes.', "easy-form" ),
                'desc_hidden' => __( 'Build an unlimited number of quizzes and questions.', "easy-form" ),
                'wporg'       => 'https://wordpress.org/plugins/quiz-maker/',
                'buy_now'     => 'https://ays-pro.com/wordpress/quiz-maker/',
                'url'         => $plugin_url_arr['quiz-maker'],
            ),
            'survey-maker/survey-maker.php'        => array(
                'icon'        => $images_url . 'icon-survey-128x128.png',
                'name'        => __( 'Survey Maker', 'chart-builder' ),
                'desc'        => __( 'Make amazing online surveys and get real-time feedback quickly and easily.', 'chart-builder' ),
                'desc_hidden' => __( 'Learn what your website visitors want, need, and expect with the help of Survey Maker. Build surveys without limiting your needs.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/survey-maker/',
                'buy_now'     => 'https://ays-pro.com/wordpress/survey-maker',
                'url'         => $plugin_url_arr['survey-maker'],
            ),
            'poll-maker/poll-maker-ays.php'        => array(
                'icon'        => $images_url . 'icon-poll-128x128.png',
                'name'        => __( 'Poll Maker', 'chart-builder' ),
                'desc'        => __( 'Create amazing online polls for your WordPress website super easily.', 'chart-builder' ),
                'desc_hidden' => __( 'Build up various types of polls in a minute and get instant feedback on any topic or product.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/poll-maker/',
                'buy_now'     => 'https://ays-pro.com/wordpress/poll-maker/',
                'url'         => $plugin_url_arr['poll-maker'],
            ),
            'ays-popup-box/ays-pb.php'        => array(
                'icon'        => $images_url . 'icon-popup-128x128.png',
                'name'        => __( 'Popup Box', 'chart-builder' ),
                'desc'        => __( 'Popup everything you want! Create informative and promotional popups all in one plugin.', 'chart-builder' ),
                'desc_hidden' => __( 'Attract your visitors and convert them into email subscribers and paying customers.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/ays-popup-box/',
                'buy_now'     => 'https://ays-pro.com/wordpress/popup-box/',
                'url'         => $plugin_url_arr['ays-popup-box'],
            ),
            'secure-copy-content-protection/secure-copy-content-protection.php'        => array(
                'icon'        => $images_url . 'icon-sccp-128x128.png',
                'name'        => __( 'Secure Copy Content Protection', 'chart-builder' ),
                'desc'        => __( 'Disable the right click, copy paste, content selection and copy shortcut keys on your website.', 'chart-builder' ),
                'desc_hidden' => __( 'Protect web content from being plagiarized. Prevent plagiarism from your website with this easy to use plugin.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/secure-copy-content-protection/',
                'buy_now'     => 'https://ays-pro.com/wordpress/secure-copy-content-protection/',
                'url'         => $plugin_url_arr['secure-copy-content-protection'],
            ),
            'gallery-photo-gallery/gallery-photo-gallery.php'        => array(
                'icon'        => $images_url . 'icon-gallery-128x128.jpg',
                'name'        => __( 'Gallery Photo Gallery', 'chart-builder' ),
                'desc'        => __( 'Create unlimited galleries and include unlimited images in those galleries.', 'chart-builder' ),
                'desc_hidden' => __( 'Represent images in an attractive way. Attract people with your own single and multiple free galleries from your photo library.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/gallery-photo-gallery/',
                'buy_now'     => 'https://ays-pro.com/wordpress/photo-gallery/',
                'url'         => $plugin_url_arr['gallery-photo-gallery'],
            ),
            'ays-chatgpt-assistant/ays-chatgpt-assistant.php'        => array(
                'icon'        => $images_url . 'icon-chatgpt-128x128.png',
                'name'        => __( 'AI Assistant with ChatGPT', 'chart-builder' ),
                'desc'        => __( 'ChatGPT AI Assistant plugin gives you the ability to automate various tasks related to content creation and programming.', 'chart-builder' ),
                'desc_hidden' => __( 'The ChatGPT AI Assistant plugin is here to empower your WordPress website. Have your personal chatbot, content generator right on your front end and backend.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/ays-chatgpt-assistant/',
                'buy_now'     => 'https://ays-pro.com/wordpress/chatgpt-assistant',
                'url'         => $plugin_url_arr['ays-chatgpt-assistant'],
            ),
            'easy-form/easy-form.php'        => array(
                'icon'        => $images_url . 'icon-form-128x128.png',
                'name'        => __( 'Easy Form', 'chart-builder' ),
                'desc'        => __( 'Choose the best WordPress form builder plugin. ', 'chart-builder' ),
                'desc_hidden' => __( 'Create contact forms, payment forms, surveys, and many more custom forms. Build forms easily with us.', 'chart-builder' ),
                'wporg'       => 'https://wordpress.org/plugins/easy-form/',
                'buy_now'     => 'https://ays-pro.com/wordpress/easy-form',
                'url'         => $plugin_url_arr['easy-form'],
            ),
        );

        return $plugins_array;
    }

    protected function ays_chart_get_latest_plugin_version( $slug ){

        if ( is_null( $slug ) || empty($slug) ) {
            return "";
        }

        $version_latest = "";

        if ( ! function_exists( 'plugins_api' ) ) {
              require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        }

        // set the arguments to get latest info from repository via API ##
        $args = array(
            'slug' => $slug,
            'fields' => array(
                'version' => true,
            )
        );

        /** Prepare our query */
        $call_api = plugins_api( 'plugin_information', $args );

        /** Check for Errors & Display the results */
        if ( is_wp_error( $call_api ) ) {
            $api_error = $call_api->get_error_message();
        } else {

            //echo $call_api; // everything ##
            if ( ! empty( $call_api->version ) ) {
                $version_latest = $call_api->version;
            }
        }

        return $version_latest;
    }

    /**
     * Get AM plugin data to display in the Addons section of About tab.
     *
     * @since 6.4.0.4
     *
     * @param string $plugin      Plugin slug.
     * @param array  $details     Plugin details.
     * @param array  $all_plugins List of all plugins.
     *
     * @return array
     */
    protected function get_plugin_data( $plugin, $details, $all_plugins ) {

        $have_pro = ( ! empty( $details['pro'] ) && ! empty( $details['pro']['plug'] ) );
        $show_pro = false;

        $plugin_data = array();

        if ( $have_pro ) {
            if ( array_key_exists( $plugin, $all_plugins ) ) {
                if ( is_plugin_active( $plugin ) ) {
                    $show_pro = true;
                }
            }
            if ( array_key_exists( $details['pro']['plug'], $all_plugins ) ) {
                $show_pro = true;
            }
            if ( $show_pro ) {
                $plugin  = $details['pro']['plug'];
                $details = $details['pro'];
            }
        }

        if ( array_key_exists( $plugin, $all_plugins ) ) {
            if ( is_plugin_active( $plugin ) ) {
                // Status text/status.
                $plugin_data['status_class'] = 'status-active';
                $plugin_data['status_text']  = esc_html__( 'Active', 'chart-builder' );
                // Button text/status.
                $plugin_data['action_class'] = $plugin_data['status_class'] . ' ays-chart-card__btn-info disabled';
                $plugin_data['action_text']  = esc_html__( 'Activated', 'chart-builder' );
                $plugin_data['plugin_src']   = esc_attr( $plugin );
            } else {
                // Status text/status.
                $plugin_data['status_class'] = 'status-installed';
                $plugin_data['status_text']  = esc_html__( 'Inactive', 'chart-builder' );
                // Button text/status.
                $plugin_data['action_class'] = $plugin_data['status_class'] . ' ays-chart-card__btn-info';
                $plugin_data['action_text']  = esc_html__( 'Activate', 'chart-builder' );
                $plugin_data['plugin_src']   = esc_attr( $plugin );
            }
        } else {
            // Doesn't exist, install.
            // Status text/status.
            $plugin_data['status_class'] = 'status-missing';

            if ( isset( $details['act'] ) && 'go-to-url' === $details['act'] ) {
                $plugin_data['status_class'] = 'status-go-to-url';
            }
            $plugin_data['status_text'] = esc_html__( 'Not Installed', 'chart-builder' );
            // Button text/status.
            $plugin_data['action_class'] = $plugin_data['status_class'] . ' ays-chart-card__btn-info';
            $plugin_data['action_text']  = esc_html__( 'Install Plugin', 'chart-builder' );
            $plugin_data['plugin_src']   = esc_url( $details['url'] );
        }

        $plugin_data['details'] = $details;

        return $plugin_data;
    }

    /**
     * Display the Addons section of About tab.
     *
     * @since 6.4.0.4
     */
    public function output_about_addons() {

        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins          = get_plugins();
        $am_plugins           = $this->get_am_plugins();
        $can_install_plugins  = self::ays_chart_can_install( 'plugin' );
        $can_activate_plugins = self::ays_chart_can_activate( 'plugin' );

        $content = '';
        $content.= '<div class="ays-chart-cards-block">';
        foreach ( $am_plugins as $plugin => $details ){

            $plugin_data              = $this->get_plugin_data( $plugin, $details, $all_plugins );
            $plugin_ready_to_activate = $can_activate_plugins
                && isset( $plugin_data['status_class'] )
                && $plugin_data['status_class'] === 'status-installed';
            $plugin_not_activated     = ! isset( $plugin_data['status_class'] )
                || $plugin_data['status_class'] !== 'status-active';

            $plugin_action_class = ( isset( $plugin_data['action_class'] ) && esc_attr( $plugin_data['action_class'] ) != "" ) ? esc_attr( $plugin_data['action_class'] ) : "";

            $plugin_action_class_disbaled = "";
            if ( strpos($plugin_action_class, 'status-active') !== false ) {
                $plugin_action_class_disbaled = "disbaled='true'";
            }

            $content .= '
                <div class="ays-chart-card">
                    <div class="ays-chart-card__content flexible">
                        <div class="ays-chart-card__content-img-box">
                            <img class="ays-chart-card__img" src="'. esc_url( $plugin_data['details']['icon'] ) .'" alt="'. esc_attr( $plugin_data['details']['name'] ) .'">
                        </div>
                        <div class="ays-chart-card__text-block">
                            <h5 class="ays-chart-card__title">'. esc_html( $plugin_data['details']['name'] ) .'</h5>
                            <p class="ays-chart-card__text">'. wp_kses_post( $plugin_data['details']['desc'] ) .'
                                <span class="ays-chart-card__text-hidden">
                                    '. wp_kses_post( $plugin_data['details']['desc_hidden'] ) .'
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="ays-chart-card__footer">';
                        if ( $can_install_plugins || $plugin_ready_to_activate || ! $details['wporg'] ) {
                            $content .= '<button class="'. esc_attr( $plugin_data['action_class'] ) .'" data-plugin="'. esc_attr( $plugin_data['plugin_src'] ) .'" data-type="plugin" '. $plugin_action_class_disbaled .'>
                                '. wp_kses_post( $plugin_data['action_text'] ) .'
                            </button>';
                        }
                        elseif ( $plugin_not_activated ) {
                            $content .= '<a href="'. esc_url( $details['wporg'] ) .'" target="_blank" rel="noopener noreferrer">
                                '. esc_html_e( 'WordPress.org', 'chart-builder' ) .'
                                <span aria-hidden="true" class="dashicons dashicons-external"></span>
                            </a>';
                        }
            $content .='
                        <a target="_blank" href="'. esc_url( $plugin_data['details']['buy_now'] ) .'" class="ays-chart-card__btn-primary">'. __('Buy Now', $this->plugin_name) .'</a>
                    </div>
                </div>';
        }
        $install_plugin_nonce = wp_create_nonce( $this->plugin_name . '-install-plugin-nonce' );
        $content.= '<input type="hidden" id="ays_chart_ajax_install_plugin_nonce" name="ays_chart_ajax_install_plugin_nonce" value="'. $install_plugin_nonce .'">';
        $content.= '</div>';

        echo $content;
    }

    /**
	 * Returns the data from Quiz maker.
	 *
	 * @access public
	 */
	public function fetch_quiz_maker_data(){
		check_ajax_referer( 'cbuilder-fetch-quiz-maker-data', 'security' );

		$params = $_POST['params'];
		if ( !isset($params) || empty($params) ) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => __( "Something went wrong.", "chart-builder" )
				)
			);
		}
		
		$query = isset($params['query']) && $params['query'] !== '' ? $params['query'] : '';
		if ( $query == '' ) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => __( "No data given.", "chart-builder" )
				)
			);
		}
		$quiz_id = isset($params['quizID']) && $params['quizID'] !== '' ? $params['quizID'] : null;
		$user_id = get_current_user_id();

		$return_results = CBFunctions()->get_quiz_query($query, $quiz_id, $user_id);

		if (empty($return_results)) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => __( "Something went wrong.", "chart-builder" )
				)
			);
		}

		$result_values = $return_results['result'];
		$query = $return_results['query'];

        if (empty($result_values)) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => $return_results['message']
				)
			);
		}

        if (count($return_results['result'][0]) != 1 ) {
			$results = array();
			$headers = array();
			if ( $result_values ) {
				$row_num = 0;
				foreach ( $result_values as $row ) {
					$result = array();
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

		if (empty($results)) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => $return_results['message']
				)
			);
		}

		foreach ($results as $key => $value) {
			if (!isset($value) || count($value) <= 1 && $key != 0) {
				unset($results[$key]);
			}
		}

		array_unshift($results, $headers);
		$results = array_values( $results );
		$data = $results;
		
		$headers = $results[0];
		unset( $results[0] );
		
		$html = CBFunctions()->get_table_html( $headers, $results );

		return array(
			'success' => true,
			'data' => array(
				'table' => $html,
				'data' => $data
			)
		);
	}

	/**
	 * Saves the Quiz maker data.
	 *
	 * @access public
	 */
	public function save_quiz_maker_data() {
		check_ajax_referer( 'cbuilder-save-quiz-maker-data', 'security' );

		$params = $_POST['params'];
		if ( !isset($params) || empty($params) ) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => __( "Something went wrong.", "chart-builder" )
				)
			);
		}

		$query_id = isset($params['query']) && $params['query'] !== '' ? $params['query'] : '';
		if ( $query_id == '' ) {
			return array(
				'success' => false,
				'data' => array(
					'msg' => __( "No data given.", "chart-builder" )
				)
			);
		}

		$chart_id = isset( $params['chart_id'] ) ? absint( $params['chart_id'] ) : 0;
		$quiz_id = isset($params['quizID']) && $params['quizID'] !== '' ? $params['quizID'] : 0;
		$user_id = get_current_user_id();

		if ( $chart_id >= 0 ) {

			$return_results = CBFunctions()->get_quiz_query($query_id, $quiz_id, $user_id);

			if (empty($return_results)) {
				return array(
					'success' => false,
					'data' => array(
						'msg' => __( "Something went wrong.", "chart-builder" )
					)
				);
			}

			$result_values = $return_results['result'];
			$query = $return_results['query'];

            if (empty($result_values)) {
				return array(
					'success' => false,
					'data' => array(
						'msg' => $return_results['message']
					)
				);
			}

			if ( !empty($result_values) && count($return_results['result'][0]) != 1 ) {
				$results = array();
				$headers = array();
				if ( $result_values ) {
					$row_num = 0;
					foreach ( $result_values as $row ) {
						$result = array();
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

            $source_type = 'quiz_maker';
            $option_name_for_data = $chart_id == 0 ? 'ays_chart_quiz_maker_results_temp' : 'ays_chart_quiz_maker_results_' . $chart_id;
            $option_name_for_quiz = $chart_id == 0 ? 'ays_chart_quiz_maker_quiz_data_temp' : 'ays_chart_quiz_maker_quiz_data_' . $chart_id;

            update_option( $option_name_for_data, array(
                'source_type' => $source_type,
                'source' => $query,
                'data' => $results,
            ) );
            update_option( $option_name_for_quiz, array(
                'quiz_query' => $query_id,
                'quiz_id' => $quiz_id
            ) );

		} else {
			return array(
				'success' => false,
				'data' => array(
					'msg' => __( 'Given incorrect Chart ID.', "chart-builder")
				)
			);
		}

		return array(
			'success' => true,
			'data' => array(
				'msg' => __( 'Data was successfully saved.', "chart-builder" )
			)
		);
	}

	/**
	 * Chart page action hooks
	 */

	/**
     * Chart page sources contents
	 * @param $args
	 */
	public function ays_chart_page_source_contents( $args ){
        $chart_source_type = $args['chart_source_type'];

        if ($chart_source_type === 'chart-js') {
            $sources_contents = apply_filters( 'ays_cb_chart_page_sources_contents_settings_chartjs', array(), $args );
        } else {
            $sources_contents = apply_filters( 'ays_cb_chart_page_sources_contents_settings', array(), $args );
        }

		$source_type = $args['source_type'];

		$sources = array();
		foreach ( $sources_contents as $key => $sources_content ) {
            $collapsed = $key == $source_type ? 'false' : 'true';

			$content = '<fieldset class="ays-accordion-options-container" data-collapsed="'. $collapsed .'">';
			if( isset( $sources_content['title'] ) ){
				$content .= '<legend class="ays-accordion-options-header">';
				$content .= '<svg class="ays-accordion-arrow '. ( $key == $source_type ? 'ays-accordion-arrow-down' : 'ays-accordion-arrow-right' ) .'" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                    <g>
                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: '. ( $key == $source_type ? '#008cff' : '#c4c4c4' ) .';" vector-effect="non-scaling-stroke" />
                    </g>
                </svg>';

				$content .= '<span>'. $sources_content['title'] .'</span></legend>';
			}

			$content .= '<div class="ays-accordion-options-content">';
			$content .= $sources_content['content'];
			$content .= '</div>';

			$content .= '</fieldset>';

			$sources[] = $content;
		}
		$content_for_escape = implode('' , $sources );
		echo html_entity_decode(esc_html( $content_for_escape ));
	}

    public function source_contents_import_from_csv_settings( $sources, $args ){
	    $html_class_prefix = $args['html_class_prefix'];
	    $html_name_prefix = $args['html_name_prefix'];

        ob_start();
	    ?>
        <div class="ays-accordion-data-main-wrap ays-pro-features-v2-main-box" style="padding:10px;">
            <div class="ays-pro-features-v2-big-buttons-box">
                <a href="https://www.youtube.com/watch?v=tZ8K3y0qOEY" target="_blank" class="ays-pro-features-v2-video-button">
                    <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                    <div class="ays-pro-features-v2-video-text">
                        <?php echo __("Watch Video" , "chart-builder"); ?>
                    </div>
                </a>
                <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                    <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                    <div class="ays-pro-features-v2-upgrade-text">
                        <?php echo __("Upgrade" , "chart-builder"); ?>
                    </div>
                </a>
            </div>
			<div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-main">
                <div class="<?php echo esc_attr($html_class_prefix) ?>file-import-form">
					<h6><?php echo __("Choose what kind of data would you like to upload.", "chart-builder"); ?></h6>
					<select class="form-select" style="max-width: none;" id="<?php echo esc_attr($html_class_prefix) ?>import-files-file-type">
						<option value="csv">CSV File</option>
					</select>
					<span style="display: block; font-style: italic; color: gray; font-size: 12px; margin: 5px 0;" class="<?php echo esc_attr($html_class_prefix) ?>small-hint-text"><?php echo __("Choose the format of the file you are going to import.", "chart-builder"); ?></span>
					<p class="<?php echo esc_attr($html_class_prefix) ?>csv-export-example" style="font-size: 15px; font-style: italic;">Example: 
						<a class="<?php echo esc_attr($html_class_prefix) ?>csv-export-example-link <?php echo esc_attr($html_class_prefix) ?>csv-export-example-link-other-types" style="cursor: pointer;">example.csv</a>
					</p>
					<div>
						<input style="padding: 0.5rem 1rem;" class="form-control form-control-md" id="<?php echo esc_attr($html_class_prefix) ?>import-files-input" name="<?php echo esc_attr($html_class_prefix) ?>import-files-input" type="file" accept=".csv" />
					</div>
					<div class='ays-chart-file-import-success'></div>
					<div class='ays-chart-file-import-error'></div>
					<div class="ays-chart-buttons-group">
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-file-import-fetch">
                            <?php echo __( 'Show Results', "chart-builder" ); ?>
                        </button>
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-file-import-show-on-chart">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                            <?php echo __( 'Preview', "chart-builder" ); ?>
                        </button>
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-file-import-save">
                            <?php echo __( 'Save data', "chart-builder" ); ?>
                        </button>
					</div>
				</div>
			</div>
        </div>
	    <?php
        $content = ob_get_clean();

	    $title = __( 'Import data from file', "chart-builder" ) . ' <a class="ays_help" data-bs-toggle="tooltip" title="' . __("With the help of this option, you can import a file in your chosen file format.","chart-builder") . '">
						<i class="ays_fa ays_fa_info_circle"></i>
					</a>';

	    $sources['file_import'] = array(
		    'content' => $content,
		    'title' => $title
	    );

        return $sources;
    }

	public function source_contents_import_from_db_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap ays-pro-features-v2-main-box" style="padding:10px;">
            <div class="ays-pro-features-v2-big-buttons-box">
                <a href="https://www.youtube.com/watch?v=tZ8K3y0qOEY" target="_blank" class="ays-pro-features-v2-video-button">
                    <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                    <div class="ays-pro-features-v2-video-text">
                        <?php echo __("Watch Video" , "chart-builder"); ?>
                    </div>
                </a>
                <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                    <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                    <div class="ays-pro-features-v2-upgrade-text">
                        <?php echo __("Upgrade" , "chart-builder"); ?>
                    </div>
                </a>
            </div>
            <div class="<?= $html_class_prefix ?>source-data-main-wrap">
                <div class="<?= $html_class_prefix ?>chart-source-data-main">
                    <div id="ays-chart-db-query">
                        <div class="<?= $html_class_prefix ?>-db-query-form">
                            <div id="db-query-form">
                                <input type="hidden" name="chart_id" value="1">
                                <textarea name="query" class="<?= $html_class_prefix ?>db-query" placeholder="<?php echo __( "Add your query here.", $this->plugin_name ); ?>"></textarea>
                                <div class='db-wizard-success'></div>
                                <div class='db-wizard-error'></div>
                            </div>
                            <div class="ays-chart-db-query-form-button ays-chart-buttons-group">
                                <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-query-fetch">
                                    <?php echo __( 'Show Results', "chart-builder" ); ?>
                                </button>
                                <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-query-show-on-chart">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                                    <?php echo __( 'Preview', "chart-builder" ); ?>
                                </button>
                                <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-query-save">
                                    <?php echo __( 'Save data', "chart-builder" ); ?>
                                </button>
                            </div>
                        </div>
                        <div class="db-wizard-hints">
                            <ul>
                                <!-- <li><//?php echo sprintf( __( 'For examples of queries and links to resources that you can use with this feature, please click %1$shere%2$s', $this->plugin_name ), '<a href="' . '#' . '" target="_blank">', '</a>' ); ?></li> -->
                                <li><?php echo sprintf( __( 'Use %1$sControl+Space%2$s for autocompleting keywords or table names.', $this->plugin_name ), '<span class="ays-chart-emboss">', '</span>' ); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Connect to Database', $this->plugin_name ) . ' <a class="ays_help" data-bs-toggle="tooltip" title="' . __("Insert the Database query and the appropriate information from your Database will be displayed in the chart.",$this->plugin_name) . '">
					<i class="ays_fa ays_fa_info_circle"></i>
				</a>';

		$sources['import_from_db'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function source_contents_import_from_external_db_settings( $sources, $args ){
        $html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap ays-pro-features-v2-main-box" style="padding:10px;">
            <div class="ays-pro-features-v2-big-buttons-box">
                <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                    <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                    <div class="ays-pro-features-v2-upgrade-text">
                        <?php echo __("Upgrade" , "chart-builder"); ?>
                    </div>
                </a>
            </div>
            <div class="<?= $html_class_prefix ?>source-data-main-wrap">
                <div class="<?= $html_class_prefix ?>chart-source-data-main">
                    <div class="form-group row mb-2">
                        <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label class="form-label">
                                <?php echo esc_html(__('Use custom database settings for this chart', "chart-builder")); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __(".","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                                <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" type="checkbox" />
                                <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                            </label>
                        </div>
                    </div>
                    <div id="ays-chart-external-db-query">
                        <div class="<?= $html_class_prefix ?>-db-query-form">
                            <div id="external-db-query-form">
                                <input type="hidden" name="chart_id" value="1">
                                <textarea name="query" class="<?= $html_class_prefix ?>external-db-query" placeholder="<?php echo __( "Add your query here.", $this->plugin_name ); ?>"></textarea>
                                <div class='db-wizard-success'></div>
                                <div class='db-wizard-error'></div>
                            </div>
                            <div class="ays-chart-db-query-form-button ays-chart-buttons-group">
                                <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-external-query-fetch">
                                    <?php echo __( 'Show Results', "chart-builder" ); ?>
                                </button>
                                <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-external-query-show-on-chart">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                                    <?php echo __( 'Preview', "chart-builder" ); ?>
                                </button>
                                <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-external-query-save">
                                    <?php echo __( 'Save data', "chart-builder" ); ?>
                                </button>
                            </div>
                        </div>
                        <div class="db-wizard-hints">
                            <ul>
                                <!-- <li><//?php echo sprintf( __( 'For examples of queries and links to resources that you can use with this feature, please click %1$shere%2$s', $this->plugin_name ), '<a href="' . '#' . '" target="_blank">', '</a>' ); ?></li> -->
                                <li><?php echo sprintf( __( 'Use %1$sControl+Space%2$s for autocompleting keywords or table names.', $this->plugin_name ), '<span class="ays-chart-emboss">', '</span>' ); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
		$content = ob_get_clean();

        $title = __( 'Connect to External Database', $this->plugin_name ) . ' <a class="ays_help" data-bs-toggle="tooltip" title="' . __("Insert the Database query and fetch data from an external database.", $this->plugin_name) . '">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>';

        $sources['import_from_external_db'] = array(
            'content' => $content,
            'title' => $title
        );

        return $sources;
    }

    public function source_contents_quiz_maker_integration_settings( $sources, $args ){
		global $wpdb;
		$chart_id = $args['chart_id'];
	    $html_class_prefix = $args['html_class_prefix'];
	    $html_name_prefix = $args['html_name_prefix'];
        $source = $args['source'];
        $settings = $args['settings'];
        $quiz_queries = $args['quiz_queries'];
        $quiz_query_tooltips = $args['quiz_query_tooltips'];
		$quiz_id = $args['quiz_id'];
		$quiz_query = $args['quiz_query'];

		if ( !is_plugin_active('quiz-maker/quiz-maker.php') ) {
			// $title = sprintf( __( 'Get Quiz Maker data', "chart-builder" ) . ' <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="' . __("By using this option, you can display the quiz statistics by charts.%s %sNote:%s The Quiz Maker plugin must be active.","chart-builder") . '">
			// 			<i class="ays_fa ays_fa_info_circle"></i>
			// 		</a>', '<br>', '<b>', '</b>');
			// $content = $this->blockquote_content_quiz;
	    	// $sources['quiz_maker'] = array(
			//     'content' => $content,
			//     'title' => $title
			// );

			return $sources;
		}

		$sql = "SELECT `title`, `id` FROM " . $wpdb->prefix . "aysquiz_quizes WHERE `published` = 1 AND `question_ids` <> ''";
		$quizes = $wpdb->get_results($sql, "ARRAY_A");

        ob_start();
	    ?>
        <div class="ays-accordion-data-main-wrap">
			<div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-main cb-changable-tab cb-pie_chart-tab cb-donut_chart-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
                <div id="<?php echo esc_attr($html_class_prefix) ?>quiz-maker-form">
					<input type="hidden" class="<?php echo esc_attr($html_class_prefix) ?>chart-id" value="<?= $chart_id ?>">
					<div class="<?= $html_class_prefix ?>select-quiz-maker-data-query-container" id="<?= $html_class_prefix ?>quiz-queries">
						<a class="ays_help <?= $html_class_prefix ?>quiz-query-tooltip" data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo isset($quiz_query) ? $quiz_query_tooltips[$quiz_query] : __( 'Select a query to display quiz data.', $this->plugin_name ) ?>">
							<i class="ays_fa ays_fa_info_circle"></i>
						</a>
						<select class="form-select" style="max-width: none;" id="<?php echo esc_attr($html_class_prefix) ?>select-quiz-maker-data-query" name="<?= $html_name_prefix ?>quiz_query">
							<option value=""><?= __( "Select query", "chart-builder" ) ?></option>
							<?php foreach ( $quiz_queries as $id => $q): 
                                $disabled = preg_replace('/[^0-9]/', '', $id) <= 6 ? "false" : "true" ; ?>
								<option value="<?= $id ?>" <?= $quiz_query == $id ? 'selected' : '' ?> <?php echo 'is-pro="'.$disabled.'"'; ?>><?= $q ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="<?php echo esc_attr($html_class_prefix) ?>select-quiz-maker-quiz-container">
						<select class="form-select" style="max-width: none;" id="<?php echo esc_attr($html_class_prefix) ?>select-quiz-maker-quiz" name="<?= $html_name_prefix ?>quiz_id">
							<option value="0"><?= __( "Select quiz", "chart-builder" ) ?></option>
							<?php foreach ( $quizes as $quiz): ?>
								<option value="<?= $quiz['id'] ?>" <?= $quiz_id == $quiz['id'] ? 'selected' : '' ?> ><?= $quiz['title'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div id="ays-chart-quiz-maker-success"></div>
					<div id="ays-chart-quiz-maker-error"></div>
					<div class="ays-chart-buttons-group">
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-quiz-maker-fetch">
                            <?php echo __( 'Show Results', "chart-builder" ); ?>
                        </button>
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-quiz-maker-show-on-chart">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                            <?php echo __( 'Preview', "chart-builder" ); ?>
                        </button>
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-quiz-maker-save">
                            <?php echo __( 'Save data', "chart-builder" ); ?>
                        </button>
					</div>
				</div>
                <div>
                    <a href="https://www.youtube.com/watch?v=vqx76dw6NC8" target="_blank" style="text-decoration:none;font-style:italic"><?php echo __('How to Connect Quizzes to Charts', 'chart-builder'); ?></a>
                </div>
			</div>
        </div>
	    <?php
        $content = ob_get_clean();

	    $title = sprintf( __( 'Get Quiz Maker data', $this->plugin_name ) . ' <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="' . __("By using this option, you can display the quiz statistics by charts.%s %sNote:%s The Quiz Maker plugin must be active.",$this->plugin_name) . '">
					<i class="ays_fa ays_fa_info_circle"></i>
				</a>', '<br>', '<b>', '</b>');
	    $sources['quiz_maker'] = array(
		    'content' => $content,
		    'title' => $title
	    );

        return $sources;
    }

    public function source_contents_woocommerce_integration_settings( $sources, $args ){
        if ( !is_plugin_active('woocommerce/woocommerce.php') ) {
            return $sources;
        }

        $chart_id = $args['chart_id'];
        $html_class_prefix = $args['html_class_prefix'];
        $html_name_prefix = $args['html_name_prefix'];
        ob_start();
        ?>
        <div class="ays-accordion-data-main-wrap  ays-accordion-data-main-wrap-woocommerce ays-pro-features-v2-main-box" style="padding:10px;">
            <div class="ays-pro-features-v2-big-buttons-box">
                <div class="ays-pro-features-v2-video-button"></div>
                <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                    <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                    <div class="ays-pro-features-v2-upgrade-text">
                        <?php echo __("Upgrade" , "chart-builder"); ?>
                    </div>
                </a>
            </div>
            <div class="<?= $html_class_prefix ?>chart-source-data-main">
                <div id="ays-chart-woocommerce-datas">
                    <div class="<?= $html_class_prefix ?>woocommerce-datas-form">
                        <div id="woocommerce-datas-form">
                            <input type="hidden" name="chart_id" value="<?= $chart_id ?>">
                            <div class="<?= $html_class_prefix ?>woocommerce-datas-query-container" id="<?= $html_class_prefix ?>woocommerce-datas-container-id" >
                                <a class="ays_help <?= $html_class_prefix ?>woocommerce-datas-tooltip" data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo __( 'Select a query to display data.', $this->plugin_name ) ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                                <select class="form-select" style="max-width: none;" id="<?= $html_class_prefix ?>woocommerce-datas-select" name="<?php echo esc_attr($html_name_prefix); ?>settings[woocommerce_data_id]">
                                    <option value=""><?= __( 'Select query', $this->plugin_name ) ?></option>
                                </select>
                            </div>
                            <div class='ays-chart-woocommerce-datas-success'></div>
                            <div class='ays-chart-woocommerce-datas-error'></div>
                        </div>
                        <div class="ays-chart-buttons-group">
                            <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-woocommerce-datas-fetch">
                                <?php echo __( 'Show Results', "chart-builder" ); ?>
                            </button>
                            <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-woocommerce-datas-show-on-chart">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                                <?php echo __( 'Preview', "chart-builder" ); ?>
                            </button>
                            <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns" id="ays-chart-woocommerce-datas-save">
                                <?php echo __( 'Save data', "chart-builder" ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();

        $title = sprintf( __( 'Get WooCommerce data', $this->plugin_name ) . ' <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="' . __("By using this option, you can display the WooCommerce statistics by charts.%s %sNote:%s The WooCommerce plugin must be active.",$this->plugin_name) . '">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>', '<br>', '<b>', '</b>');
        $sources['woocommerce'] = array(
            'content' => $content,
            'title' => $title
        );

        return $sources;
    }

    public function source_contents_manual_settings( $sources, $args ){
	    $html_class_prefix = $args['html_class_prefix'];
	    $html_name_prefix = $args['html_name_prefix'];
        $source = $args['source'];
		$source = isset($source["commonTypeCharts"]) ? $source["commonTypeCharts"] : $source;
        $settings = $args['settings'];
		$source_chart_type = $args['source_chart_type'];
		$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'add';
		
		if (isset($source) && !empty($source)) {
            if ( !isset( $source[0] ) ) {
                if (count($source[1]) > 2) {
                    $titles = array();
                    for ($i = 0; $i < count($source[1]); $i++) {
                        array_push($titles, __("Title", $this->plugin_name).$i);
                    }
                    $source[0] = $titles;
                } else {
                    $source[0] = array(
                        __("Country", $this->plugin_name),
                        __("Population", $this->plugin_name),
                    );
                }
    
                ksort($source);
            }
        }

		if ($action == 'add') {
			foreach ($source as $key => $row) {
				$source[$key] = array_slice($row, 0, 2);
			}
		} else if ($action == 'edit') {
			if ($source_chart_type == 'pie_chart' || $source_chart_type == 'donut_chart') {
				foreach ($source as $key => $row) {
					$source[$key] = array_slice($row, 0, 2);
				}
			}
		}
        ob_start();
	    ?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>source-data-main-wrap">
                <?php if($source_chart_type != 'org_chart'): ?>
                    <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-main <?= $html_class_prefix ?>chart-source-data-manual cb-changable-manual cb-pie_chart-manual cb-bar_chart-manual cb-column_chart-manual cb-line_chart-manual cb-donut_chart-manual display_none">
                        <!-- <div class="<//?= $html_class_prefix ?>icons-box">
                            <img class="<//?= $html_class_prefix ?>add-new-row" src="<//?php echo CHART_BUILDER_ADMIN_URL; ?>/images/icons/add-circle-outline.svg">
                        </div> -->
                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-content-container">
                            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-content">
                                <?php if(!empty($source)):
                                    foreach($source as $source_id => $source_value):
                                        if(!empty($source_value) ):
                                            if ($source_id == 0): ?>
                                                <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id = "<?php echo esc_attr($source_id); ?>">
                                                    <div class="ays-chart-empty-data-table-cell"></div>
                                                    <?php foreach($source_value as $each_source_id => $each_source_value): ?>
                                                        <div class="<?= $html_class_prefix ?>chart-source-data-input-box <?= $html_class_prefix ?>chart-source-title-box" data-cell-id = "<?php echo esc_attr($each_source_id); ?>">
                                                            <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-col" data-trigger="hover" data-bs-toggle="tooltip" title="Delete column" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10px">
                                                                <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                                            </svg>
                                                            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-titles-box-item">
                                                                <input type="text" class="ays-text-input form-control <?= $html_class_prefix ?>chart-source-title-input" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo stripslashes(esc_attr($each_source_value)); ?>" <?php echo $each_source_id == 0 ? "style='min-width:100px'" : "" ?>>
                                                                <?php if ($each_source_id !== 0): ?>
                                                                    <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-sort" data-sort-order="asc" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                                        <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z" style="fill: #b8b8b8;" />
                                                                    </svg>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id = "<?php echo esc_attr($source_id); ?>">
                                                    <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-row">
                                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                                            <path d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l9.4-9.4V224H109.3l9.4-9.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-9.4-9.4H224V402.7l-9.4-9.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-9.4 9.4V288H402.7l-9.4 9.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l9.4 9.4H288V109.3l9.4 9.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-64-64z" style="fill: #b8b8b8;" />
                                                        </svg>
                                                    </div>
                                                    <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>icons-remove-box">
                                                        <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-row" data-trigger="hover" data-bs-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                            <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                                        </svg>
                                                    </div>
                                                    <?php foreach($source_value as $each_source_id => $each_source_value): ?>
                                                        <?php if ($each_source_id == 0): ?>
                                                            <div class="<?= $html_class_prefix ?>chart-source-data-input-box <?= $html_class_prefix ?>chart-source-data-name-input-box" data-cell-id = "<?php echo esc_attr($each_source_id); ?>">
                                                                <input type="text" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo htmlspecialchars(esc_attr($each_source_value)); ?>">
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box <?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-number" data-cell-id = "<?php echo esc_attr($each_source_id); ?>">
                                                                <input type="number" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo stripslashes(esc_attr($each_source_value)); ?>" step="any">
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else:?>
                                    <div class="<?= $html_class_prefix ?>chart-source-data-edit-block">
                                        <div style="height: 63.11px; padding: 0 15px;"></div>
                                        <div class="<?= $html_class_prefix ?>chart-source-data-input-box <?= $html_class_prefix ?>chart-source-title-box">
                                            <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-col" data-trigger="hover" data-bs-toggle="tooltip" title="Delete column" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10px">
                                                <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                            </svg>
                                            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-titles-box-item">
                                                <input type="text" class="ays-text-input form-control <?= $html_class_prefix ?>chart-source-title-input" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[0][]" style='min-width:100px'>
                                                <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-sort" data-sort-order="asc" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z" style="fill: #b8b8b8;" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id="1">
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-row">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                                <path d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l9.4-9.4V224H109.3l9.4-9.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-9.4-9.4H224V402.7l-9.4-9.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-9.4 9.4V288H402.7l-9.4 9.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l9.4 9.4H288V109.3l9.4 9.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-64-64z" />
                                            </svg>
                                        </div>
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>icons-remove-box" >
                                            <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-row" data-trigger="hover" data-bs-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                            </svg>
                                        </div>
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box">
                                            <input type="text" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[1][]" >
                                        </div>
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box <?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-number">
                                            <input type="number" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[1][]" step="any">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>add-new-column-box cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt display_none">
                                <img class="<?php echo esc_attr($html_class_prefix) ?>add-new-column" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/add-circle-outline.svg">
                                <?php echo __( 'Add column', "chart-builder" ); ?>
                            </div>
                        </div>
                        <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>add-new-row-box">
                            <img class="<?php echo esc_attr($html_class_prefix) ?>add-new-row" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/add-circle-outline.svg">
                            <?php echo __( 'Add row', "chart-builder" ); ?>
                        </div>
                        <br>
                        <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns <?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttn">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                            <?php echo __( 'Preview', "chart-builder" ); ?>
                        </button>
                    </div>
                <?php endif; ?>
                <div class="<?= $html_class_prefix ?>chart-source-data-main-org-type display_none cb-changable-manual cb-org_chart-manual">
					<div class="<?= $html_class_prefix ?>chart-source-data-content-org-type">
						<ul id="<?= $html_class_prefix ?>chart-source-data-edit-tree-content">
						</ul>
					</div>
					<button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns <?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttn">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                        <?php echo __( 'Preview', "chart-builder" ); ?>
                    </button>
				</div>
            </div>
        </div>
	    <?php
        $content = ob_get_clean();

	    $title = __( 'Manual data', "chart-builder" ) . ' <a class="ays_help" data-bs-toggle="tooltip" title="' . __("Add the data manually. By clicking on the Add Row button you will be able to add as many rows as you need. While choosing the Line, Bar, Column Chart types you will be able to also add the columns.","chart-builder") . '">
						<i class="ays_fa ays_fa_info_circle"></i>
					</a>';

	    $sources['manual'] = array(
		    'content' => $content,
		    'title' => $title
	    );

        return $sources;
    }

    public function source_contents_manual_settings_chartjs( $sources, $args ){
	    $html_class_prefix = $args['html_class_prefix'];
	    $html_name_prefix = $args['html_name_prefix'];
        $source = $args['source'];
		$source = isset($source["commonTypeCharts"]) ? $source["commonTypeCharts"] : $source;
        $settings = $args['settings'];
		$source_chart_type = $args['source_chart_type'];
		$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'add';
		
        ob_start();
	    ?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>source-data-main-wrap">
                <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-main <?= $html_class_prefix ?>chart-source-data-manual">
                    <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-content-container">
                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-content">
                            <?php if(!empty($source)):
                                foreach($source as $source_id => $source_value):
                                    if(!empty($source_value) ):
                                        if ($source_id == 0): ?>
                                            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id = "<?php echo esc_attr($source_id); ?>">
                                                <div class="ays-chart-empty-data-table-cell"></div>
                                                <?php foreach($source_value as $each_source_id => $each_source_value): ?>
                                                    <div class="<?= $html_class_prefix ?>chart-source-data-input-box <?= $html_class_prefix ?>chart-source-title-box" data-cell-id = "<?php echo esc_attr($each_source_id); ?>">
                                                        <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-col" data-trigger="hover" data-bs-toggle="tooltip" title="Delete column" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10px">
                                                            <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                                        </svg>
                                                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-titles-box-item">
                                                            <input type="text" class="ays-text-input form-control <?= $html_class_prefix ?>chart-source-title-input" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo stripslashes(esc_attr($each_source_value)); ?>" <?php echo $each_source_id == 0 ? "style='min-width:100px'" : "" ?>>
                                                            <?php if ($each_source_id !== 0): ?>
                                                                <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-sort" data-sort-order="asc" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z" style="fill: #b8b8b8;" />
                                                                </svg>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id = "<?php echo esc_attr($source_id); ?>">
                                                <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-row">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                                        <path d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l9.4-9.4V224H109.3l9.4-9.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-9.4-9.4H224V402.7l-9.4-9.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-9.4 9.4V288H402.7l-9.4 9.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l9.4 9.4H288V109.3l9.4 9.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-64-64z" style="fill: #b8b8b8;" />
                                                    </svg>
                                                </div>
                                                <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>icons-remove-box">
                                                    <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-row" data-trigger="hover" data-bs-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                        <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                                    </svg>
                                                </div>
                                                <?php foreach($source_value as $each_source_id => $each_source_value): ?>
                                                    <?php if ($each_source_id == 0): ?>
                                                        <div class="<?= $html_class_prefix ?>chart-source-data-input-box <?= $html_class_prefix ?>chart-source-data-name-input-box" data-cell-id = "<?php echo esc_attr($each_source_id); ?>">
                                                            <input type="text" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo htmlspecialchars(esc_attr($each_source_value)); ?>">
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box <?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-number" data-cell-id = "<?php echo esc_attr($each_source_id); ?>">
                                                            <input type="number" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo stripslashes(esc_attr($each_source_value)); ?>" step="any">
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else:?>
                                <div class="<?= $html_class_prefix ?>chart-source-data-edit-block">
                                    <div style="height: 63.11px; padding: 0 15px;"></div>
                                    <div class="<?= $html_class_prefix ?>chart-source-data-input-box <?= $html_class_prefix ?>chart-source-title-box">
                                        <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-col" data-trigger="hover" data-bs-toggle="tooltip" title="Delete column" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10px">
                                            <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                        </svg>
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-titles-box-item">
                                            <input type="text" class="ays-text-input form-control <?= $html_class_prefix ?>chart-source-title-input" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[0][]" style='min-width:100px'>
                                            <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-sort" data-sort-order="asc" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z" style="fill: #b8b8b8;" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class = "<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id="1">
                                    <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-move-row">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                            <path d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l9.4-9.4V224H109.3l9.4-9.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-9.4-9.4H224V402.7l-9.4-9.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-9.4 9.4V288H402.7l-9.4 9.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l9.4 9.4H288V109.3l9.4 9.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-64-64z" />
                                        </svg>
                                    </div>
                                    <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>icons-remove-box" >
                                        <svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block <?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-row" data-trigger="hover" data-bs-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                            <path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />
                                        </svg>
                                    </div>
                                    <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box">
                                        <input type="text" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[1][]" >
                                    </div>
                                    <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box <?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-number">
                                        <input type="number" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[1][]" step="any">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>add-new-column-box cb-changable-opt cb-bar_chart-opt cb-line_chart-opt display_none">
                            <img class="<?php echo esc_attr($html_class_prefix) ?>add-new-column" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/add-circle-outline.svg">
                            <?php echo __( 'Add column', "chart-builder" ); ?>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>add-new-row-box">
                        <img class="<?php echo esc_attr($html_class_prefix) ?>add-new-row" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/add-circle-outline.svg">
                        <?php echo __( 'Add row', "chart-builder" ); ?>
                    </div>
                    <br>
                    <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttns <?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttn">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.722 6.59785C12.2407 3.47754 10.0017 1.90723 7.00009 1.90723C3.99697 1.90723 1.75947 3.47754 0.278215 6.59941C0.218802 6.72522 0.187988 6.86263 0.187988 7.00176C0.187988 7.14089 0.218802 7.27829 0.278215 7.4041C1.75947 10.5244 3.99853 12.0947 7.00009 12.0947C10.0032 12.0947 12.2407 10.5244 13.722 7.40254C13.8423 7.14941 13.8423 6.85566 13.722 6.59785ZM7.00009 10.9697C4.47978 10.9697 2.63447 9.6916 1.3329 7.00098C2.63447 4.31035 4.47978 3.03223 7.00009 3.03223C9.5204 3.03223 11.3657 4.31035 12.6673 7.00098C11.3673 9.6916 9.52197 10.9697 7.00009 10.9697ZM6.93759 4.25098C5.41884 4.25098 4.18759 5.48223 4.18759 7.00098C4.18759 8.51973 5.41884 9.75098 6.93759 9.75098C8.45634 9.75098 9.68759 8.51973 9.68759 7.00098C9.68759 5.48223 8.45634 4.25098 6.93759 4.25098ZM6.93759 8.75098C5.9704 8.75098 5.18759 7.96816 5.18759 7.00098C5.18759 6.03379 5.9704 5.25098 6.93759 5.25098C7.90478 5.25098 8.68759 6.03379 8.68759 7.00098C8.68759 7.96816 7.90478 8.75098 6.93759 8.75098Z" fill="#14524A" /></svg>
                        <?php echo __( 'Preview', "chart-builder" ); ?>
                    </button>
                </div>
            </div>
        </div>
	    <?php
        $content = ob_get_clean();

	    $title = __( 'Manual data', "chart-builder" ) . ' <a class="ays_help" data-bs-toggle="tooltip" title="' . __("Add the data manually. By clicking on the Add Row button you will be able to add as many rows as you need. While choosing the Line, Bar, Column Chart types you will be able to also add the columns.","chart-builder") . '">
						<i class="ays_fa ays_fa_info_circle"></i>
					</a>';

	    $sources['manual'] = array(
		    'content' => $content,
		    'title' => $title
	    );

        return $sources;
    }

	/**
	 * Chart page settings contents
	 * @param $args
	 */
	public function ays_chart_page_settings_contents( $args ){
        $chart_source_type = $args['chart_source_type'];

        if ($chart_source_type === 'chart-js') {
            $sources_contents = apply_filters( 'ays_cb_chart_page_settings_contents_settings_chartjs', array(), $args );
        } else {
            $sources_contents = apply_filters( 'ays_cb_chart_page_settings_contents_settings', array(), $args );
        }

		$sources = array();
		foreach ( $sources_contents as $key => $sources_content ) {
			$collapsed = $key == 'general_settings' ? 'false' : 'true';

			$content = '<fieldset class="ays-accordion-options-container" data-collapsed="' . $collapsed . '">';
			if(isset($sources_content['title'])){
				$content .= '<legend class="ays-accordion-options-header">';
				$content .= '<svg class="ays-accordion-arrow '. ( $key == 'general_settings' ? 'ays-accordion-arrow-down' : 'ays-accordion-arrow-right' ) .'" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                    <g>
                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: '. ( $key == 'general_settings' ? '#008cff' : '#c4c4c4' ) .';" vector-effect="non-scaling-stroke" />
                    </g>
                </svg>';

				$content .= '<span>'. esc_html($sources_content['title']) .'</span></legend>';
			}

			$content .= '<div class="ays-accordion-options-content">';
				$content .= $sources_content['content'];
			$content .= '</div>';

			$content .= '</fieldset>';

			$sources[] = $content;
		}
		$content_for_escape = implode('' , $sources );
		echo html_entity_decode(esc_html( $content_for_escape ));
	}

	public function settings_contents_general_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
        $chart_description = $args['chart_description'];
        $create_author_data = $args['create_author_data'];
		$status = $args['status'];
		$settings = $args['settings'];

        $show_title = $settings['show_title'];
        $show_description = $settings['show_description'];
        $enable_interactivity = $settings['enable_interactivity'];
        $maximized_view = $settings['maximized_view'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-status" class="form-label">
                            <?php echo esc_html(__('Status', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Decide whether the chart is active or not. If the chart is a draft, it won't be shown anywhere on your website (you don't need to remove shortcodes).","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-status" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>status" value="published" <?php echo $status == 'published' ? 'checked' : ''; ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Status -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-description" class="form-label">
                            <?php echo esc_html(__( "Description", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Set the chart description","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <textarea class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-textarea-input" id="ays-chart-description" type="text" name="<?php echo esc_attr($html_name_prefix); ?>description"><?php echo esc_attr($chart_description) ?></textarea>
                    </div>
                </div> <!-- Chart description -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-create-author" class="form-label">
                            <?php echo esc_html(__('Change the author of the current chart',"chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_html(__('You can change the author who created the current chart to your preferred one. You need to write the User ID here. Please note, that in case you write an ID, by which there are no users found, the changes will not be applied and the previous author will remain the same.',"chart-builder")); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-create-author" name="<?php echo esc_attr($html_name_prefix); ?>create_author">
                            <option value=""><?php echo esc_html(__('Select User',"chart-builder"))?></option>
                            <?php if (isset($create_author_data['ID'])) : ?>
                                <option value="<?php echo esc_html($create_author_data['ID'])?>" selected><?php echo esc_html($create_author_data['display_name'])?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div> <!-- Change chart author -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-show-title" class="form-label">
                            <?php echo esc_html(__('Show chart title', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If you disable the toggle, the Chart title will not be displayed on the Front-end. By default, the toggle is enabled.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-show-title" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[show_title]" value="on" <?php echo esc_attr($show_title); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Show title -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-show-description" class="form-label">
                            <?php echo esc_html(__('Show chart description', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If you disable the toggle, the Chart description will not be displayed on the Front-end. By default, the toggle is enabled.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-show-description" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[show_description]" value="on" <?php echo esc_attr($show_description); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Show description -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-pie_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-enable-interactivity" class="form-label"> 
                            <?php echo esc_html(__('Enable interactivity', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Decide if the chart shows events based on user actions or responds to user interaction. If not, the chart won't generate 'select' or similar interaction-based events (but will generate ready or error events), and won't show hovertext or change based on user input.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-enable-interactivity" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_interactivity]" value="on" <?php echo esc_attr($enable_interactivity); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Enable interactivity -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-maximized-view" class="form-label"> 
                            <?php echo esc_html(__('Maximized view', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If checked, maximizes the area of the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-maximized-view" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[maximized_view]" value="on" <?php echo esc_attr($maximized_view); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Maximized view -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'General Settings', "chart-builder" );

		$sources['general_settings'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_general_settings_chartjs( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
        $chart_description = $args['chart_description'];
        $create_author_data = $args['create_author_data'];
		$status = $args['status'];
		$settings = $args['settings'];

        $show_title = $settings['show_title'];
        $show_description = $settings['show_description'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-status" class="form-label">
                            <?php echo esc_html(__('Status', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Decide whether the chart is active or not. If the chart is a draft, it won't be shown anywhere on your website (you don't need to remove shortcodes).","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-status" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>status" value="published" <?php echo $status == 'published' ? 'checked' : ''; ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Status -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-description" class="form-label">
                            <?php echo esc_html(__( "Description", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Set the chart description","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <textarea class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-textarea-input" id="ays-chart-description" type="text" name="<?php echo esc_attr($html_name_prefix); ?>description"><?php echo esc_attr($chart_description) ?></textarea>
                    </div>
                </div> <!-- Chart description -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-create-author" class="form-label">
                            <?php echo esc_html(__('Change the author of the current chart',"chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_html(__('You can change the author who created the current chart to your preferred one. You need to write the User ID here. Please note, that in case you write an ID, by which there are no users found, the changes will not be applied and the previous author will remain the same.',"chart-builder")); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-create-author" name="<?php echo esc_attr($html_name_prefix); ?>create_author">
                            <option value=""><?php echo esc_html(__('Select User',"chart-builder"))?></option>
                            <?php if (isset($create_author_data['ID'])) : ?>
                                <option value="<?php echo esc_html($create_author_data['ID'])?>" selected><?php echo esc_html($create_author_data['display_name'])?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div> <!-- Change chart author -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-show-title" class="form-label">
                            <?php echo esc_html(__('Show chart title', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If you disable the toggle, the Chart title will not be displayed on the Front-end. By default, the toggle is enabled.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-show-title" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[show_title]" value="on" <?php echo esc_attr($show_title); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Show title -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-show-description" class="form-label">
                            <?php echo esc_html(__('Show chart description', "chart-builder")); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If you disable the toggle, the Chart description will not be displayed on the Front-end. By default, the toggle is enabled.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-show-description" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[show_description]" value="on" <?php echo esc_attr($show_description); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Show description -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'General Settings', "chart-builder" );

		$sources['general_settings'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

	public function settings_contents_tooltip_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $tooltip_trigger_options = $settings['tooltip_trigger_options'];
		$tooltip_trigger = $settings['tooltip_trigger'];
		$show_color_code = $settings['show_color_code'];
		$tooltip_italic = $settings['tooltip_italic'];
		$tooltip_bold = $settings['tooltip_bold'];
		$tooltip_bold_options = $settings['tooltip_bold_options'];
		$tooltip_text_color = $settings['tooltip_text_color'];
		$tooltip_font_size = $settings['tooltip_font_size'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap cb-changable-tab cb-pie_chart-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab cb-donut_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-trigger">
				            <?php echo esc_html(__( "Trigger", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose when to display the results on the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-tooltip-trigger" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_trigger]">
				            <?php
				            foreach ( $tooltip_trigger_options as $option_slug => $option ):
					            $selected = ( $tooltip_trigger == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Trigger -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-show-color-code">
				            <?php echo esc_html(__( "Show Color Code", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color will be displayed while clicking on a particular part of the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-show-color-code" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[show_color_code]" value="on" <?php echo esc_attr($show_color_code); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Show Color Code -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-text-color">
				            <?php echo esc_html(__( "Text color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the tooltip text.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-tooltip-text-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_text_color]" value="<?php echo esc_attr($tooltip_text_color) ?>">
                    </div>
                </div> <!-- Text color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size for all text within the chart tooltip, specified in pixels. Please note that if an invalid value is entered, it will revert to the default global font size.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-tooltip-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_font_size]" value="<?php echo esc_attr($tooltip_font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-italic">
				            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart tooltip text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-tooltip-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_italic]" value="on" <?php echo esc_attr($tooltip_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Italic text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-bold">
				            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose when to display the results on the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-tooltip-bold" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_bold]">
				            <?php
				            foreach ( $tooltip_bold_options as $option_slug => $option ):
					            $selected = ( $tooltip_bold == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Bold text -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Tooltip', "chart-builder" );

		$sources['tooltip'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_legend_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
        $legend_positions = $settings['legend_positions'];
        $legend_position = $settings['legend_position'];
        $legend_alignments = $settings['legend_alignments'];
        $legend_alignment = $settings['legend_alignment'];
        $legend_color = $settings['legend_color'];
        $legend_font_size = $settings['legend_font_size'];
        $legend_bold = $settings['legend_bold'];
        $legend_italic = $settings['legend_italic'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap cb-changable-tab cb-pie_chart-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab cb-donut_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-legend-position">
				            <?php echo esc_html(__( "Position", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose the appropriate position for the chart legend.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-legend-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[legend_position]">
                            <?php
				            foreach ( $legend_positions as $option_slug => $option ):
					            $selected = ( $legend_position == $option_slug ) ? 'selected' : '';
                                
                                $type_class = '';
                                if ($option_slug == 'left') {
                                    $type_class = ' cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt ';
                                } else if ($option_slug == 'in') {
                                    $type_class = ' cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt ';
                                } else if ($option_slug == 'labeled') {
                                    $type_class = ' cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt ';
                                }

					            ?>
                                
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?> class="<?php echo esc_attr($type_class); ?>"><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Legend position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-legend-alignment">
				            <?php echo esc_html(__( "Alignment", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose the appropriate alignment for the chart legend.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-legend-alignment" name="<?php echo esc_attr($html_name_prefix); ?>settings[legend_alignment]">
                            <?php
				            foreach ( $legend_alignments as $option_slug => $option ):
					            $selected = ( $legend_alignment == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Legend alignment -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-legend-font-color">
				            <?php echo esc_html(__( "Font Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose the font color for the chart legend.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-legend-font-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[legend_color]" value="<?php echo esc_attr($legend_color) ?>">
                    </div>
                </div> <!-- Legend font color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-legend-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size for all text within the chart legend, specified in pixels. Please note that if an invalid value is entered, it will revert to the default global font size.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-legend-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[legend_font_size]" value="<?php echo esc_attr($legend_font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-legend-italic">
				            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart legend text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-legend-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[legend_italic]" value="on" <?php echo esc_attr($legend_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Italic text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-legend-bold">
				            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart legend text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-legend-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[legend_bold]" value="on" <?php echo esc_attr($legend_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Bold text -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Legend', "chart-builder" );

		$sources['legend'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_horizontal_axis_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $haxis_title = $settings['haxis_title'];
        $axes_text_positions = $settings['axes_text_positions'];
        $haxis_text_position = $settings['haxis_text_position'];
        $haxis_direction = $settings['haxis_direction'];
        $haxis_text_color = $settings['haxis_text_color'];
        $haxis_baseline_color = $settings['haxis_baseline_color'];
        $haxis_slanted_options = $settings['haxis_slanted_options'];
        $haxis_slanted = $settings['haxis_slanted'];
        $haxis_slanted_text_angle = $settings['haxis_slanted_text_angle'];
        $haxis_show_text_every = $settings['haxis_show_text_every'];
        $haxis_format_options = $settings['axes_format_options'];
        $haxis_format = $settings['haxis_format'];
        $haxis_label_font_size = $settings['haxis_label_font_size'];
        $haxis_max_value = $settings['haxis_max_value'];
        $haxis_min_value = $settings['haxis_min_value'];
        $haxis_text_font_size = $settings['haxis_text_font_size'];
        $haxis_label_color = $settings['haxis_label_color'];
        $haxis_bold = $settings['haxis_bold'];
        $haxis_italic = $settings['haxis_italic'];
        $haxis_title_bold = $settings['haxis_title_bold'];
        $haxis_title_italic = $settings['haxis_title_italic'];
        $haxis_gridlines_count = $settings['haxis_gridlines_count'];
        $haxis_gridlines_color = $settings['haxis_gridlines_color'];
        $haxis_minor_gridlines_color = $settings['haxis_minor_gridlines_color'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap <?= $html_class_prefix ?>options-haxis-settings-tab cb-changable-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-title" class="form-label">
                            <?php echo esc_html(__( "Label", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The title of the horizontal axis","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-title" type="text" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_title]" value="<?php echo esc_attr($haxis_title) ?>">
                    </div>
                </div> <!-- Horizontal axis label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-label-font-size" class="form-label">
                            <?php echo esc_html(__( "Label font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The font size of the horizontal axis label.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-label-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_label_font_size]" value="<?php echo esc_attr($haxis_label_font_size) ?>">
                    </div>
                </div> <!-- Horizontal axis label font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-label-color">
				            <?php echo esc_html(__( "Label color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the horizontal axis label.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-haxis-label-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_label_color]" value="<?php echo esc_attr($haxis_label_color) ?>">
                    </div>
                </div> <!-- Horizontal axis label color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-title-italic">
				            <?php echo esc_html(__( "Italic label", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart horizontal axis label italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-haxis-title-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_title_italic]" value="on" <?php echo esc_attr($haxis_title_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Italic label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-title-bold">
				            <?php echo esc_html(__( "Bold label", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart horizontal axis label bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-haxis-title-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_title_bold]" value="on" <?php echo esc_attr($haxis_title_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Bold label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-text-position" class="form-label">
                            <?php echo esc_html(__( "Text position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Position of the horizontal axis text, relative to the chart area.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-haxis-text-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_text_position]">
                            <?php
				            foreach ( $axes_text_positions as $option_slug => $option ):
				    	        $selected = ( $haxis_text_position == $option_slug ) ? 'selected' : '';
				    	        ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Horizontal axis text position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-text-direction">
				            <?php echo esc_html(__( "Reverse Direction", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The direction in which the values grow along the horizontal axis. By default, low values are on the left of the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-haxis-text-direction" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_direction]" value="-1" <?php echo esc_attr($haxis_direction); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Horizontal axis text direction -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-text-color">
				            <?php echo esc_html(__( "Text color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the horizontal axis text.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-haxis-text-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_text_color]" value="<?php echo esc_attr($haxis_text_color) ?>">
                    </div>
                </div> <!-- Horizontal axis text color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-text-font-size" class="form-label">
                            <?php echo esc_html(__( "Text font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The font size of the horizontal axis text.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-text-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_text_font_size]" value="<?php echo esc_attr($haxis_text_font_size) ?>">
                    </div>
                </div> <!-- Horizontal axis text font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-italic">
				            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart horizontal axis text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-haxis-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_italic]" value="on" <?php echo esc_attr($haxis_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Italic text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-bold">
				            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart horizontal axis text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-haxis-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_bold]" value="on" <?php echo esc_attr($haxis_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Bold text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-slanted-text" class="form-label">
                            <?php echo esc_html(__( "Slanted text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("By choosing true the text will be slanted. In false it will be horizontal. In Automatic based on the size, it will be displayed either in horizontal or slanted. Note: This option only works, if 'Text position' option is set to 'Outside the chart'","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-haxis-slanted-text" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_slanted]">
                            <?php
				            foreach ( $haxis_slanted_options as $option_slug => $option ):
				    	        $selected = ( $haxis_slanted == $option_slug ) ? 'selected' : '';
				    	        ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Horizontal axis slanted text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section <?php echo ($haxis_slanted == 'false') ? 'display_none' : ''; ?>">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-slanted-text-angle" class="form-label">
                            <?php echo esc_html(__( "Slanted text angle", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The slanted text angle will define the angle. It will tilt in between -90 from 90 (except 0).","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-slanted-text-angle" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_slanted_text_angle]" value="<?php echo esc_attr($haxis_slanted_text_angle) ?>" step="15" min="-90" max="90">
                    </div>
                </div> <!-- Horizontal axis slanted text angle -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-format" class="form-label">
                            <?php echo esc_html(__( "Format", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="<?php 
                                echo htmlspecialchars( sprintf (
                                    "<p>" . __('A format string for numeric axis labels. You can choose any of the following:', "chart-builder") . "</p><ul class='ays_tooltop_ul'><li>" .
                                    __('%sNone:%s Displays numbers with no formatting (e.g., 8000000)', "chart-builder") . "</li><li>" .
                                    __('%sDecimal:%s Displays numbers with thousands separators (e.g., 8,000,000)', "chart-builder") . "</li><li>" .
                                    __('%sScientific:%s Displays numbers in scientific notation (e.g., 8e6)', "chart-builder") . "</li><li>" .
                                    __('%sCurrency:%s Displays numbers in the local currency (e.g., $8,000,000.00)', "chart-builder") . "</li><li>" .
                                    __('%sPercent:%s Displays numbers as percentages (e.g., 800,000,000%%)', "chart-builder") . "</li><li>" .
                                    __('%sShort:%s Displays abbreviated numbers (e.g., 8M)', "chart-builder") . "</li><li>" .
                                    __('%sLong:%s Displays numbers as full words (e.g., 8 million)', "chart-builder") . "</li></ul>",
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>'
                                ) );
                            ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-haxis-format" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_format]">
                            <?php
				            foreach ( $haxis_format_options as $option_slug => $option ):
				    	        $selected = ( $haxis_format == $option_slug ) ? 'selected' : '';
				    	        ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Horizontal axis format -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-max-value" class="form-label">
                            <?php echo esc_html(__( "Max value", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The maximum value of the axis.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-max-value" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_max_value]" value="<?php echo esc_attr($haxis_max_value) ?>">
                    </div>
                </div> <!-- Horizontal axis max value -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-min-value" class="form-label">
                            <?php echo esc_html(__( "Min value", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The minimum value of the axis.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-min-value" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_min_value]" value="<?php echo esc_attr($haxis_min_value) ?>">
                    </div>
                </div> <!-- Horizontal axis min value -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-gridlines-count" class="form-label">
                            <?php echo esc_html(__( "Gridlines count", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The approximate number of vertical gridlines inside the chart area. If you specify a positive number for gridlines count, it will be used to compute the min spacing between gridlines. You can specify a value of 1 to only draw one gridline, or 0 to draw no gridlines. Specify -1, which is the default, to automatically compute the number of gridlines based on other options. Note: This option will not work with 'Rotate vertical' option.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-gridlines-count" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_gridlines_count]" value="<?php echo esc_attr($haxis_gridlines_count) ?>">
                    </div>
                </div> <!-- Horizontal axis gridlines count -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-gridlines-color">
				            <?php echo esc_html(__( "Gridlines color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the horizontal axis gridlines.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-haxis-gridlines-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_gridlines_color]" value="<?php echo esc_attr($haxis_gridlines_color) ?>">
                    </div>
                </div> <!-- Horizontal axis gridlines color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-minor-gridlines-color">
				            <?php echo esc_html(__( "Minor gridlines color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the horizontal axis minor gridlines.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-haxis-minor-gridlines-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_minor_gridlines_color]" value="<?php echo esc_attr($haxis_minor_gridlines_color) ?>">
                    </div>
                </div> <!-- Horizontal axis minor gridlines color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-baseline-color">
				            <?php echo esc_html(__( "Baseline color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Specifies the color of the baseline for the horizontal axis.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-haxis-baseline-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_baseline_color]" value="<?php echo esc_attr($haxis_baseline_color) ?>">
                    </div>
                </div> <!-- Horizontal axis baseline color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-haxis-show-text-every" class="form-label">
                            <?php echo esc_html(__( "Label interval", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("How many horizontal axis labels to show, where 1 means show every label, 2 means show every other label, and so on. 0 is to try to show as many labels as possible without overlapping.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-haxis-show-text-every" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[haxis_show_text_every]" value="<?php echo esc_attr($haxis_show_text_every) ?>" step="1" min="0">
                    </div>
                </div> <!-- Horizontal axis slanted text angle -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Horizontal Axis Settings', "chart-builder" );

		$sources['horizontal_axis'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}
    
    public function settings_contents_vertical_axis_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $vaxis_title = $settings['vaxis_title'];
        $axes_text_positions = $settings['axes_text_positions'];
        $vaxis_text_position = $settings['vaxis_text_position'];
        $vaxis_direction = $settings['vaxis_direction'];
        $vaxis_text_color = $settings['vaxis_text_color'];
        $vaxis_baseline_color = $settings['vaxis_baseline_color'];
        $vaxis_format_options = $settings['axes_format_options'];
        $vaxis_format = $settings['vaxis_format'];
        $vaxis_label_font_size = $settings['vaxis_label_font_size'];
        $vaxis_max_value = $settings['vaxis_max_value'];
        $vaxis_min_value = $settings['vaxis_min_value'];
        $vaxis_text_font_size = $settings['vaxis_text_font_size'];
        $vaxis_label_color = $settings['vaxis_label_color'];
        $vaxis_bold = $settings['vaxis_bold'];
        $vaxis_italic = $settings['vaxis_italic'];
        $vaxis_title_bold = $settings['vaxis_title_bold'];
        $vaxis_title_italic = $settings['vaxis_title_italic'];
        $vaxis_gridlines_count = $settings['vaxis_gridlines_count'];
        $vaxis_gridlines_color = $settings['vaxis_gridlines_color'];
        $vaxis_minor_gridlines_color = $settings['vaxis_minor_gridlines_color'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap <?= $html_class_prefix ?>options-vaxis-settings-tab cb-changable-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-title" class="form-label">
                            <?php echo esc_html(__( "Label", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The title of the vertical axis","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-vaxis-title" type="text" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_title]" value="<?php echo esc_attr($vaxis_title) ?>">
                    </div>
                </div> <!-- Vertical axis label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-label-font-size" class="form-label">
                            <?php echo esc_html(__( "Label font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The font size of the vertical axis label.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-vaxis-label-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_label_font_size]" value="<?php echo esc_attr($vaxis_label_font_size) ?>">
                    </div>
                </div> <!-- Vertical axis label font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-label-color">
				            <?php echo esc_html(__( "Label color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the horizontal axis label.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-vaxis-label-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_label_color]" value="<?php echo esc_attr($vaxis_label_color) ?>">
                    </div>
                </div> <!-- Vertical axis label color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-title-italic">
				            <?php echo esc_html(__( "Italic label", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart vertical axis label italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-vaxis-title-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_title_italic]" value="on" <?php echo esc_attr($vaxis_title_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Italic label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-title-bold">
				            <?php echo esc_html(__( "Bold label", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart vertical axis label bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-vaxis-title-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_title_bold]" value="on" <?php echo esc_attr($vaxis_title_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Bold label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-text-position" class="form-label">
                            <?php echo esc_html(__( "Text position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Position of the vertical axis text, relative to the chart area.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-vaxis-text-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_text_position]">
                            <?php
				            foreach ( $axes_text_positions as $option_slug => $option ):
				    	        $selected = ( $vaxis_text_position == $option_slug ) ? 'selected' : '';
				    	        ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Vertical text position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-text-direction">
				            <?php echo esc_html(__( "Reverse Direction", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The direction in which the values along the vertical axis grow. By default, low values are on the bottom of the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-vaxis-text-direction" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_direction]" value="-1" <?php echo esc_attr($vaxis_direction); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Vertical axis text direction -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-text-color">
				            <?php echo esc_html(__( "Text color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the vertical axis text.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-vaxis-text-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_text_color]" value="<?php echo esc_attr($vaxis_text_color) ?>">
                    </div>
                </div> <!-- Vertical axis text color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-text-font-size" class="form-label">
                            <?php echo esc_html(__( "Text font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The font size of the horizontal axis text.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-vaxis-text-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_text_font_size]" value="<?php echo esc_attr($vaxis_text_font_size) ?>">
                    </div>
                </div> <!-- Vertical axis text font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-italic">
				            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart vertical axis text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-vaxis-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_italic]" value="on" <?php echo esc_attr($vaxis_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Italic text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-bold">
				            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to make chart vertical axis text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-vaxis-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_bold]" value="on" <?php echo esc_attr($vaxis_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Bold text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-format" class="form-label">
                            <?php echo esc_html(__( "Format", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="<?php 
                                echo htmlspecialchars( sprintf (
                                    "<p>" . __('A format string for numeric axis labels. You can choose any of the following:', "chart-builder") . "</p><ul class='ays_tooltop_ul'><li>" .
                                    __('%sNone:%s Displays numbers with no formatting (e.g., 8000000)', "chart-builder") . "</li><li>" .
                                    __('%sDecimal:%s Displays numbers with thousands separators (e.g., 8,000,000)', "chart-builder") . "</li><li>" .
                                    __('%sScientific:%s Displays numbers in scientific notation (e.g., 8e6)', "chart-builder") . "</li><li>" .
                                    __('%sCurrency:%s Displays numbers in the local currency (e.g., $8,000,000.00)', "chart-builder") . "</li><li>" .
                                    __('%sPercent:%s Displays numbers as percentages (e.g., 800,000,000%%)', "chart-builder") . "</li><li>" .
                                    __('%sShort:%s Displays abbreviated numbers (e.g., 8M)', "chart-builder") . "</li><li>" .
                                    __('%sLong:%s Displays numbers as full words (e.g., 8 million)', "chart-builder") . "</li></ul>",
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>',
                                    '<em>', '</em>'
                                ) );
                            ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-vaxis-format" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_format]">
                            <?php
				            foreach ( $vaxis_format_options as $option_slug => $option ):
				    	        $selected = ( $vaxis_format == $option_slug ) ? 'selected' : '';
				    	        ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Vertical axis format -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-max-value" class="form-label">
                            <?php echo esc_html(__( "Max value", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The maximum value of the axis.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-vaxis-max-value" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_max_value]" value="<?php echo esc_attr($vaxis_max_value) ?>">
                    </div>
                </div> <!-- Vertical axis max value -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-min-value" class="form-label">
                            <?php echo esc_html(__( "Min value", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The minimum value of the axis.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-vaxis-min-value" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_min_value]" value="<?php echo esc_attr($vaxis_min_value) ?>">
                    </div>
                </div> <!-- Vertical axis min value -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-gridlines-count" class="form-label">
                            <?php echo esc_html(__( "Gridlines count", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The approximate number of horizontal gridlines inside the chart area. If you specify a positive number for gridlines count, it will be used to compute the min spacing between gridlines. You can specify a value of 1 to only draw one gridline, or 0 to draw no gridlines. Specify -1, which is the default, to automatically compute the number of gridlines based on other options. Note: This option will not work with 'Rotate vertical' option.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-vaxis-gridlines-count" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_gridlines_count]" value="<?php echo esc_attr($vaxis_gridlines_count) ?>">
                    </div>
                </div> <!-- Vertical axis gridlines count -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-gridlines-color">
				            <?php echo esc_html(__( "Gridlines color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the vertical axis gridlines.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-vaxis-gridlines-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_gridlines_color]" value="<?php echo esc_attr($vaxis_gridlines_color) ?>">
                    </div>
                </div> <!-- Vertical axis gridlines color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-minor-gridlines-color">
				            <?php echo esc_html(__( "Minor gridlines color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the vertical axis minor gridlines.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-vaxis-minor-gridlines-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_minor_gridlines_color]" value="<?php echo esc_attr($vaxis_minor_gridlines_color) ?>">
                    </div>
                </div> <!-- Vertical axis minor gridlines color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-vaxis-baseline-color">
				            <?php echo esc_html(__( "Baseline color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Specifies the color of the baseline for the vertical axis.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-vaxis-baseline-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[vaxis_baseline_color]" value="<?php echo esc_attr($vaxis_baseline_color) ?>">
                    </div>
                </div> <!-- Vertical axis baseline color -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Vertical Axis Settings', "chart-builder" );

		$sources['vertical_axis'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_animation_settings( $sources, $args ) {
        $html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $enable_animation = $settings['enable_animation'];
        $animation_duration = $settings['animation_duration'];
        $animation_startup = $settings['animation_startup'];
        $animation_easing_options = $settings['animation_easing_options'];
        $animation_easing = $settings['animation_easing'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap <?= $html_class_prefix ?>options-animation-settings-tab cb-changable-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-enable-animation">
				            <?php echo esc_html(__( "Enable chart animation", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable chart animation.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch <?php echo esc_attr($html_class_prefix) ?>toggle-hidden-option" id="ays-chart-option-enable-animation" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_animation]" value="on" <?php echo esc_attr($enable_animation); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Enable animation -->
                <div class="form-group row <?php echo esc_attr($html_class_prefix) ?>animation-options-section <?php echo esc_attr($html_class_prefix) ?>hidden-options-section <?php echo ($enable_animation == 'checked') ? '' : 'display_none'; ?>">
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-animation-duration" class="form-label">
                                <?php echo esc_html(__( "Duration", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The duration of the chart animation, in milliseconds.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-animation-duration" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[animation_duration]" value="<?php echo esc_attr($animation_duration) ?>">
                        </div>
                    </div> <!-- Animation duration -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-animation-startup">
                                <?php echo esc_html(__( "Startup", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Determine if the chart will animate on the window load. If checked, the chart will start at the baseline and animate to its final state, else it will animate on size or data change.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                                <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-animation-startup" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[animation_startup]" value="on" <?php echo esc_attr($animation_startup); ?> >
                                <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                            </label>
                        </div>
                    </div> <!-- Animation startup -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-animation-easing" class="form-label">
                                <?php echo esc_html(__( "Easing", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="<?php 
                                    echo htmlspecialchars( sprintf(
                                        "<p>" . __('The easing function applied to the chart animation. The following options are available:', "chart-builder") . "</p><ul class='ays_tooltop_ul'><li>" .
                                        __('%sLinear:%s Constant speed.', "chart-builder") . "</li><li>" .
                                        __('%sEase in:%s Start slow and speed up.', "chart-builder") . "</li><li>" .
                                        __('%sEase out:%s Start fast and slow down.', "chart-builder") . "</li><li>" .
                                        __('%sEase in and out:%s Start slow, speed up, then slow down.', "chart-builder") . "</li></ul>",
                                        '<em>',
                                        '</em>',
                                        '<em>',
                                        '</em>',
                                        '<em>',
                                        '</em>',
                                        '<em>',
                                        '</em>'
                                    ) );
                                ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                            <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-animation-easing" name="<?php echo esc_attr($html_name_prefix); ?>settings[animation_easing]">
                                <?php
                                foreach ( $animation_easing_options as $option_slug => $option ):
                                    $selected = ( $animation_easing == $option_slug ) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div> <!-- Animation easing -->
                <div>
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Chart animation', "chart-builder" );

		$sources['animation'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
    }

    public function settings_contents_live_chart_settings( $sources, $args ) {
        $html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];

        ob_start();
		?>
        <div class="ays-accordion-data-main-wrap <?= $html_class_prefix ?>options-live-chart-settings-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <a href="https://www.youtube.com/watch?v=lhTqZmFUNz4" target="_blank" class="ays-pro-features-v2-video-button">
                            <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                            <div class="ays-pro-features-v2-video-text">
                                <?php echo __("Watch Video" , "chart-builder"); ?>
                            </div>
                        </a>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-enable-live-chart">
				            <?php echo esc_html(__( "Enable live chart", "chart-builder" )); ?>
							<a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If you enable this option, the chart data will be dynamically updated on the front end. Note: If the option is enabled, the chart data will be periodically updated (e.g., once in 3 seconds) without refreshing the page..","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch <?php echo esc_attr($html_class_prefix) ?>toggle-hidden-option" id="ays-chart-option-enable-live-chart" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_live_chart]" value="on">
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Enable live chart -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <a href="https://www.youtube.com/watch?v=lhTqZmFUNz4" target="_blank" class="ays-pro-features-v2-video-button">
                            <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                            <div class="ays-pro-features-v2-video-text">
                                <?php echo __("Watch Video" , "chart-builder"); ?>
                            </div>
                        </a>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-live-chart-interval" class="form-label">
                            <?php echo esc_html(__( "Interval", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Specify the time intervals to update the chart on the front end (e.g., once in 3 seconds). The option works via milliseconds. Note: The minimum interval must be 3000 milliseconds.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-live-chart-interval" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[live_chart_interval]" value="" min="3000">
                        <div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">ms</div>
                    </div>
                </div> <!-- Live chart interval -->
            </div>
        </div>
        <?php
		$content = ob_get_clean();

		$title = __( 'Live Chart', "chart-builder" );

        $sources['live_chart'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;

    }

    public function settings_contents_export_options( $sources, $args ){
        $html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
        
        $enable_img = $settings['enable_img'];
        
        ob_start();
        ?>
        <div class="ays-accordion-data-main-wrap <?= $html_class_prefix ?>options-export-tab cb-changable-tab cb-pie_chart-tab cb-donut_chart-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <a href="https://www.youtube.com/watch?v=9UqLXG5NU_I" target="_blank" class="ays-pro-features-v2-video-button">
                            <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                            <div class="ays-pro-features-v2-video-text">
                                <?php echo __("Watch Video" , "chart-builder"); ?>
                            </div>
                        </a>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-export-print">
                            <?php echo esc_html(__( "Print chart", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable the toggle and the users can print the chart data.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-export-print" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_print]" value="on">
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Print option end -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <a href="https://www.youtube.com/watch?v=9UqLXG5NU_I" target="_blank" class="ays-pro-features-v2-video-button">
                            <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                            <div class="ays-pro-features-v2-video-text">
                                <?php echo __("Watch Video" , "chart-builder"); ?>
                            </div>
                        </a>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-export-Excel">
                            <?php echo esc_html(__( "Excel download", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable the toggle and the users can export the chart data in Excel file format.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-export-excel" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_excel]" value="on">
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Excel option end -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <a href="https://www.youtube.com/watch?v=9UqLXG5NU_I" target="_blank" class="ays-pro-features-v2-video-button">
                            <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                            <div class="ays-pro-features-v2-video-text">
                                <?php echo __("Watch Video" , "chart-builder"); ?>
                            </div>
                        </a>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-export-CSV">
                            <?php echo esc_html(__( "CSV download", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable the toggle and the users can export the chart data in CSV file format.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-export-csv" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_csv]"  value="on">
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- CSV option end -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <a href="https://www.youtube.com/watch?v=9UqLXG5NU_I" target="_blank" class="ays-pro-features-v2-video-button">
                            <div class="ays-pro-features-v2-video-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Video_24x24_Hover.svg"></div>
                            <div class="ays-pro-features-v2-video-text">
                                <?php echo __("Watch Video" , "chart-builder"); ?>
                            </div>
                        </a>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-export-Copy">
                            <?php echo esc_html(__( "Copy chart data", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable the toggle and the users can copy the chart data (CTRL+C, CTRL+V).","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-export-copy" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_copy]"  value="on">
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Copy option end -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt cb-line_chart-opt cb-bar_chart-opt cb-column_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-export-img">
                            <?php echo esc_html(__( "Download chart image", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable the toggle and the users can download the chart as an image.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-export-img" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_img]"  value="on" <?php echo $enable_img?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- IMG option end -->
            </div>
        </div>
        <?php

        $content = ob_get_clean();

        $title = __( 'Frontend Actions', "chart-builder" );

        $sources['export_options'] = array(
            'content' => $content,
            'title' => $title
        );

        return $sources;
    }

    /**
	 * Chart page styles contents
	 * @param $args
	 */
	public function ays_chart_page_styles_contents( $args ){
        $chart_source_type = $args['chart_source_type'];

        if ($chart_source_type === 'chart-js') {
            $sources_contents = apply_filters( 'ays_cb_chart_page_styles_contents_settings_chartjs', array(), $args );
        } else {
            $sources_contents = apply_filters( 'ays_cb_chart_page_styles_contents_settings', array(), $args );
        }

		$sources = array();
		foreach ( $sources_contents as $key => $sources_content ) {
			$collapsed = $key == 'chart' ? 'false' : 'true';

			$content = '<fieldset class="ays-accordion-options-container" data-collapsed="' . $collapsed . '">';
			if(isset($sources_content['title'])){
				$content .= '<legend class="ays-accordion-options-header">';
				$content .= '<svg class="ays-accordion-arrow '. ( $key == 'chart' ? 'ays-accordion-arrow-down' : 'ays-accordion-arrow-right' ) .'" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                    <g>
                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: '. ( $key == 'chart' ? '#008cff' : '#c4c4c4' ) .';" vector-effect="non-scaling-stroke" />
                    </g>
                </svg>';

				$content .= '<span>'. esc_html($sources_content['title']) .'</span></legend>';
			}

			$content .= '<div class="ays-accordion-options-content">';
				$content .= $sources_content['content'];
			$content .= '</div>';

			$content .= '</fieldset>';

			$sources[] = $content;
		}
		$content_for_escape = implode('' , $sources );
		echo html_entity_decode(esc_html( $content_for_escape ));
	}

	public function settings_contents_chart_styles_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

		$width = $settings['width'];
		$width_format = $settings['width_format'];
        $title_positions = $settings['title_positions'];
		$position = $settings['position'];
		$width_format_options = $settings['width_format_options'];
		$responsive_width = $settings['responsive_width'];
		$height = $settings['height'];
        $height_format = $settings['height_format'];
		$font_size = $settings['font_size'];
        $org_chart_font_size_options = $settings['org_chart_font_size_options'];
		$org_chart_font_size = $settings['org_chart_font_size'];
		$background_color = $settings['background_color'];
        $transparent_background = $settings['transparent_background'];
        $border_width = $settings['border_width'];
        $border_radius = $settings['border_radius'];
        $border_color = $settings['border_color'];
		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-width" class="form-label">
                            <?php echo esc_html(__( "Width", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The width of the chart container, in percents.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[width]" value="<?php echo esc_attr($width) ?>">
						<select class="<?php echo esc_attr($html_class_prefix) ?>option-width-format-change <?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-width-format" name="<?php echo esc_attr($html_name_prefix); ?>settings[width_format]">
                            <?php
                            foreach ( $width_format_options as $option_slug => $option ):
                                $selected = ( $width_format == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-responsive-width" class="form-label">
                            <?php echo esc_html(__( "Responsive Width", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to keep the chart's width fixed at 100%, no matter what is set for the Width option. This makes the chart more responsive.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-responsive-width" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[responsive_width]" value="on" <?php echo esc_attr($responsive_width); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Responsive Width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-position">
				            <?php echo esc_html(__( "Position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The position of the chart. Note: The changes will be visible when the width option is not set to 100%","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[position]">
                            <?php
				            foreach ( $title_positions as $option_slug => $option ):
					            $selected = ( $position == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-height" class="form-label">
                            <?php echo esc_html(__( "Height", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The height of the chart container, in pixels.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-height" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[height]" value="<?php echo esc_attr($height) ?>">
						<select class="<?php echo esc_attr($html_class_prefix) ?>option-width-format-change <?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-height-format" name="<?php echo esc_attr($html_name_prefix); ?>settings[height_format]">
                            <?php
                            foreach ( $width_format_options as $option_slug => $option ):
                                $selected = ( $height_format == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Height -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size of the chart text.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[font_size]" value="<?php echo esc_attr($font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Font size -->
                <div class="form-group row mb-2 <?= $html_class_prefix ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?= $html_class_prefix ?>option-title">
                        <label for="ays-chart-option-org-chart-font-size" class="form-label">
                            <?php echo __( "Element size", $this->plugin_name ); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The size of the chart element.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
						<select class="<?= $html_class_prefix ?>option-select-input form-select" id="ays-chart-option-org-chart-font-size" name="<?php echo $html_name_prefix; ?>settings[org_chart_font_size]">
				            <?php
				            foreach ( $org_chart_font_size_options as $option_slug => $option ):
					            $selected = ( $org_chart_font_size == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo $option_slug; ?>" <?php echo $selected; ?>><?php echo $option; ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Font size for org type -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-background-color">
				            <?php echo esc_html(__( "Background Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The background color of the chart container.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-background-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[background_color]" value="<?php echo esc_attr($background_color) ?>">
                    </div>
                </div> <!-- Background color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-transparent-background" class="form-label">
                            <?php echo esc_html(__( "Transparent background", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart's background transparent. When enabled, both Background Color and Chart Area Background Color options will not work","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-transparent-background" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[transparent_background]" value="on" <?php echo esc_attr($transparent_background); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Transparent background -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-border-width">
				            <?php echo esc_html(__( "Border Width", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The width of the chart container border.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-border-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[border_width]" value="<?php echo esc_attr($border_width) ?>">
                    </div>
                </div> <!-- Border Width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-border-color">
				            <?php echo esc_html(__( "Border Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The border color of the chart container.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-border-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[border_color]" value="<?php echo esc_attr($border_color) ?>">
                    </div>
                </div> <!-- Border color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt cb-donut_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-border-radius">
				            <?php echo esc_html(__( "Border Radius", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The border radius of the chart container.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-border-radius" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[border_radius]" value="<?php echo esc_attr($border_radius) ?>">
                    </div>
                </div> <!-- Border radius -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Chart', "chart-builder" );

		$sources['chart'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

	public function settings_contents_chart_area_styles_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $chart_background_color = $settings['chart_background_color'];
		$chart_border_color = $settings['chart_border_color'];
		$chart_left_margin = $settings['chart_left_margin'];
		$chart_right_margin = $settings['chart_right_margin'];
		$chart_top_margin = $settings['chart_top_margin'];
		$chart_bottom_margin = $settings['chart_bottom_margin'];
		$chart_border_width = $settings['chart_border_width'];
		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap cb-changable-tab cb-pie_chart-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab cb-donut_chart-tab">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-background-color">
				            <?php echo esc_html(__( "Background Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The background color of the chart area.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-chart-background-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_background_color]" value="<?php echo esc_attr($chart_background_color) ?>">
                    </div>
                </div> <!-- Chart Area background color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-border-width">
				            <?php echo esc_html(__( "Border Width", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The border width of the chart area.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-chart-border-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_border_width]" value="<?php echo esc_attr($chart_border_width) ?>">
                    </div>
                </div> <!-- Chart Area Border Width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-border-color">
				            <?php echo esc_html(__( "Border Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The border color of the chart area.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-chart-border-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_border_color]" value="<?php echo esc_attr($chart_border_color) ?>">
                    </div>
                </div> <!-- Chart Area border color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-left-margin">
				            <?php echo esc_html(__( "Left Margin", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the chart's distance from the left border. Leave blank for auto-positioning.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-chart-left-margin" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_left_margin]" value="<?php echo esc_attr($chart_left_margin) ?>">
                    </div>
                </div> <!-- Chart Area Left Margin -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-right-margin">
				            <?php echo esc_html(__( "Right Margin", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the chart's distance from the right border. Leave blank for auto-positioning.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-chart-right-margin" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_right_margin]" value="<?php echo esc_attr($chart_right_margin) ?>">
                    </div>
                </div> <!-- Chart Area Right Margin -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-top-margin">
				            <?php echo esc_html(__( "Top Margin", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the chart's distance from the top border. Leave blank for auto-positioning.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-chart-top-margin" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_top_margin]" value="<?php echo esc_attr($chart_top_margin) ?>">
                    </div>
                </div> <!-- Chart Area Top Margin -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-chart-bottom-margin">
				            <?php echo esc_html(__( "Bottom Margin", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the chart's distance from the bottom border. Leave blank for auto-positioning.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-chart-bottom-margin" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[chart_bottom_margin]" value="<?php echo esc_attr($chart_bottom_margin) ?>">
                    </div>
                </div> <!-- Chart Area Bottom Margin -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Chart Area', "chart-builder" );

		$sources['chart_area'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

	public function settings_contents_title_styles_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

		$title_color = $settings['title_color'];
		$title_shadow_color = $settings['title_shadow_color'];
		$title_font_size = $settings['title_font_size'];
		$title_gap = $settings['title_gap'];
		$title_gap_description = $settings['title_gap_description'];
		$title_positions = $settings['title_positions'];
        $title_position = $settings['title_position'];
        $title_bold = $settings['title_bold'];
        $title_text_shadow = $settings['title_text_shadow'];
        $title_italic = $settings['title_italic'];
        $text_transforms = $settings['text_transforms'];
        $text_decorations = $settings['text_decorations'];
        $title_text_transform = $settings['title_text_transform'];
        $title_text_decoration = $settings['title_text_decoration'];
		$title_letter_spacing = $settings['title_letter_spacing'];
		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-color">
				            <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The color of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-title-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_color]" value="<?php echo esc_attr($title_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_font_size]" value="<?php echo esc_attr($title_font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-bold" class="form-label">
                            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart title text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-title-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_bold]" value="on" <?php echo esc_attr($title_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Title bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-italic" class="form-label">
                            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart title text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-title-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_italic]" value="on" <?php echo esc_attr($title_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Title italic -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-position">
				            <?php echo esc_html(__( "Position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The position of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-title-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_position]">
                            <?php
				            foreach ( $title_positions as $option_slug => $option ):
					            $selected = ( $title_position == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-gap" class="form-label">
                            <?php echo esc_html(__( "Gap", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart title and the chart container.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-gap" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_gap]" value="<?php echo esc_attr($title_gap) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title gap -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-gap-description" class="form-label">
                            <?php echo esc_html(__( "Distance from description", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart title and the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-gap-description" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_gap_description]" value="<?php echo esc_attr($title_gap_description) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title gap -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-text-transform">
				            <?php echo esc_html(__( "Text-transform", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-title-text-transform" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_text_transform]">
                            <?php
				            foreach ( $text_transforms as $option_slug => $option ):
					            $selected = ( $title_text_transform == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title text transform -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-letter-spacing" class="form-label">
                            <?php echo esc_html(__( "Letter spacing", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart title letters.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-letter-spacing" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_letter_spacing]" value="<?php echo esc_attr($title_letter_spacing) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title letter spacing -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-text-decoration">
				            <?php echo esc_html(__( "Text-decoration", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-title-text-decoration" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_text_decoration]">
                            <?php
				            foreach ( $text_decorations as $option_slug => $option ):
					            $selected = ( $title_text_decoration == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title text decoration -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-text-shadow" class="form-label">
                            <?php echo esc_html(__( "Text shadow", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to add a text shadow to chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-title-text-shadow" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_text_shadow]" value="on" <?php echo esc_attr($title_text_shadow); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Title bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-shadow-color">
				            <?php echo esc_html(__( "Text Shadow Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The text shadow color of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-title-shadow-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_shadow_color]" value="<?php echo esc_attr($title_shadow_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Title', "chart-builder" );

		$sources['title'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_title_styles_settings_chartjs( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

		$title_color = $settings['title_color'];
		$title_shadow_color = $settings['title_shadow_color'];
		$title_font_size = $settings['title_font_size'];
		$title_gap = $settings['title_gap'];
		$title_gap_description = $settings['title_gap_description'];
		$title_positions = $settings['title_positions'];
        $title_position = $settings['title_position'];
        $title_bold = $settings['title_bold'];
        $title_text_shadow = $settings['title_text_shadow'];
        $title_italic = $settings['title_italic'];
        $text_transforms = $settings['text_transforms'];
        $text_decorations = $settings['text_decorations'];
        $title_text_transform = $settings['title_text_transform'];
        $title_text_decoration = $settings['title_text_decoration'];
		$title_letter_spacing = $settings['title_letter_spacing'];
		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-color">
				            <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The color of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-title-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_color]" value="<?php echo esc_attr($title_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_font_size]" value="<?php echo esc_attr($title_font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-bold" class="form-label">
                            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart title text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-title-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_bold]" value="on" <?php echo esc_attr($title_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Title bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-italic" class="form-label">
                            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart title text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-title-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_italic]" value="on" <?php echo esc_attr($title_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Title italic -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-position">
				            <?php echo esc_html(__( "Position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The position of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-title-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_position]">
                            <?php
				            foreach ( $title_positions as $option_slug => $option ):
					            $selected = ( $title_position == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-gap" class="form-label">
                            <?php echo esc_html(__( "Gap", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart title and the chart container.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-gap" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_gap]" value="<?php echo esc_attr($title_gap) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title gap -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-gap-description" class="form-label">
                            <?php echo esc_html(__( "Distance from description", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart title and the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-gap-description" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_gap_description]" value="<?php echo esc_attr($title_gap_description) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title gap -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-text-transform">
				            <?php echo esc_html(__( "Text-transform", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-title-text-transform" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_text_transform]">
                            <?php
				            foreach ( $text_transforms as $option_slug => $option ):
					            $selected = ( $title_text_transform == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title text transform -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-letter-spacing" class="form-label">
                            <?php echo esc_html(__( "Letter spacing", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart title letters.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-title-letter-spacing" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_letter_spacing]" value="<?php echo esc_attr($title_letter_spacing) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart title letter spacing -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-text-decoration">
				            <?php echo esc_html(__( "Text-decoration", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-title-text-decoration" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_text_decoration]">
                            <?php
				            foreach ( $text_decorations as $option_slug => $option ):
					            $selected = ( $title_text_decoration == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Title text decoration -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-text-shadow" class="form-label">
                            <?php echo esc_html(__( "Text shadow", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to add a text shadow to chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-title-text-shadow" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_text_shadow]" value="on" <?php echo esc_attr($title_text_shadow); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Title bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-shadow-color">
				            <?php echo esc_html(__( "Text Shadow Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The text shadow color of the chart title.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-title-shadow-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_shadow_color]" value="<?php echo esc_attr($title_shadow_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Title', "chart-builder" );

		$sources['title'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_description_styles_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $description_color = $settings['description_color'];
		$description_font_size = $settings['description_font_size'];
        $title_positions = $settings['title_positions'];
        $description_position = $settings['description_position'];
        $description_bold = $settings['description_bold'];
        $description_text_shadow = $settings['description_text_shadow'];
        $description_shadow_color = $settings['description_shadow_color'];
        $description_italic = $settings['description_italic'];
        $text_transforms = $settings['text_transforms'];
        $text_decorations = $settings['text_decorations'];
        $description_text_transform = $settings['description_text_transform'];
        $description_text_decoration = $settings['description_text_decoration'];
        $description_letter_spacing = $settings['description_letter_spacing'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-color">
				            <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The color of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-description-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_color]" value="<?php echo esc_attr($description_color) ?>">
                    </div>
                </div> <!-- Chart description color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-description-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_font_size]" value="<?php echo esc_attr($description_font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart description font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-bold" class="form-label">
                            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart description text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-description-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_bold]" value="on" <?php echo esc_attr($description_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Description bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-italic" class="form-label">
                            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart description text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-description-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_italic]" value="on" <?php echo esc_attr($description_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Description italic -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-position">
				            <?php echo esc_html(__( "Position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The position of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-description-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_position]">
                            <?php
				            foreach ( $title_positions as $option_slug => $option ):
					            $selected = ( $description_position == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Description position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-text-transform">
				            <?php echo esc_html(__( "Text-transform", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-description-text-transform" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_text_transform]">
                            <?php
				            foreach ( $text_transforms as $option_slug => $option ):
					            $selected = ( $description_text_transform == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- description text transform -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-letter-spacing" class="form-label">
                            <?php echo esc_html(__( "Letter spacing", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart description letters.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-description-letter-spacing" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_letter_spacing]" value="<?php echo esc_attr($description_letter_spacing) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart description letter spacing -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-text-decoration">
				            <?php echo esc_html(__( "Text-decoration", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-description-text-decoration" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_text_decoration]">
                            <?php
				            foreach ( $text_decorations as $option_slug => $option ):
					            $selected = ( $description_text_decoration == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- description text decoration -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-text-shadow" class="form-label">
                            <?php echo esc_html(__( "Text shadow", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to add a text shadow to chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-description-text-shadow" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_text_shadow]" value="on" <?php echo esc_attr($description_text_shadow); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- description bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-shadow-color">
				            <?php echo esc_html(__( "Text Shadow Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The text shadow color of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-description-shadow-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_shadow_color]" value="<?php echo esc_attr($description_shadow_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Description', "chart-builder" );

		$sources['description'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_description_styles_settings_chartjs( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];

        $description_color = $settings['description_color'];
		$description_font_size = $settings['description_font_size'];
        $title_positions = $settings['title_positions'];
        $description_position = $settings['description_position'];
        $description_bold = $settings['description_bold'];
        $description_text_shadow = $settings['description_text_shadow'];
        $description_shadow_color = $settings['description_shadow_color'];
        $description_italic = $settings['description_italic'];
        $text_transforms = $settings['text_transforms'];
        $text_decorations = $settings['text_decorations'];
        $description_text_transform = $settings['description_text_transform'];
        $description_text_decoration = $settings['description_text_decoration'];
        $description_letter_spacing = $settings['description_letter_spacing'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-color">
				            <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The color of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-description-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_color]" value="<?php echo esc_attr($description_color) ?>">
                    </div>
                </div> <!-- Chart description color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The font size of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-description-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_font_size]" value="<?php echo esc_attr($description_font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart description font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-bold" class="form-label">
                            <?php echo esc_html(__( "Bold text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart description text bold.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-description-bold" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_bold]" value="on" <?php echo esc_attr($description_bold); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Description bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-italic" class="form-label">
                            <?php echo esc_html(__( "Italic text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to make the chart description text italic.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-description-italic" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_italic]" value="on" <?php echo esc_attr($description_italic); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Description italic -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-position">
				            <?php echo esc_html(__( "Position", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The position of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-description-position" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_position]">
                            <?php
				            foreach ( $title_positions as $option_slug => $option ):
					            $selected = ( $description_position == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- Description position -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-text-transform">
				            <?php echo esc_html(__( "Text-transform", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-description-text-transform" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_text_transform]">
                            <?php
				            foreach ( $text_transforms as $option_slug => $option ):
					            $selected = ( $description_text_transform == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- description text transform -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-letter-spacing" class="form-label">
                            <?php echo esc_html(__( "Letter spacing", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify the space between the chart description letters.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-description-letter-spacing" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_letter_spacing]" value="<?php echo esc_attr($description_letter_spacing) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Chart description letter spacing -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-text-decoration">
				            <?php echo esc_html(__( "Text-decoration", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Specify how to capitalize the text of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-description-text-decoration" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_text_decoration]">
                            <?php
				            foreach ( $text_decorations as $option_slug => $option ):
					            $selected = ( $description_text_decoration == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div> <!-- description text decoration -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-text-shadow" class="form-label">
                            <?php echo esc_html(__( "Text shadow", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Tick this option to add a text shadow to chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-description-text-shadow" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_text_shadow]" value="on" <?php echo esc_attr($description_text_shadow); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- description bold -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-description-shadow-color">
				            <?php echo esc_html(__( "Text Shadow Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The text shadow color of the chart description.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-description-shadow-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[description_shadow_color]" value="<?php echo esc_attr($description_shadow_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Description', "chart-builder" );

		$sources['description'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    /**
	 * Chart page settings contents
	 * @param $args
	 */
	public function ays_chart_page_advanced_settings_contents( $args ){
        $chart_source_type = $args['chart_source_type'];

        if ($chart_source_type === 'chart-js') {
            if ($args['source_chart_type'] !== 'pie_chart') {return;}
            $sources_contents = apply_filters( 'ays_cb_chart_page_advanced_settings_contents_settings_chartjs', array(), $args );
        } else {
            $sources_contents = apply_filters( 'ays_cb_chart_page_advanced_settings_contents_settings', array(), $args );
        }

		$sources = array();
		foreach ( $sources_contents as $key => $sources_content ) {
			$collapsed = $key == 'advanced_settings' ? 'false' : 'true';

			$content = '<fieldset class="ays-accordion-options-container" data-collapsed="' . $collapsed . '">';
			if(isset($sources_content['title'])){
				$content .= '<legend class="ays-accordion-options-header">';
				$content .= '<svg class="ays-accordion-arrow '. ( $key == 'advanced_settings' ? 'ays-accordion-arrow-down' : 'ays-accordion-arrow-right' ) .'" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                    <g>
                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: '. ( $key == 'advanced_settings' ? '#008cff' : '#c4c4c4' ) .';" vector-effect="non-scaling-stroke" />
                    </g>
                </svg>';

				$content .= '<span>'. esc_html($sources_content['title']) .'</span></legend>';
			}

			$content .= '<div class="ays-accordion-options-content">';
				$content .= $sources_content['content'];
			$content .= '</div>';

			$content .= '</fieldset>';

			$sources[] = $content;
		}
		$content_for_escape = implode('' , $sources );
		echo html_entity_decode(esc_html( $content_for_escape ));
	}

	public function settings_contents_advanced_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
        $tab_title = $args['tab_name'];

        $rotation_degree = $settings['rotation_degree'];
        $reverse_categories = $settings['reverse_categories'];
        $slice_border_color = $settings['slice_border_color'];
        $slice_texts = $settings['slice_texts'];
        $slice_text = $settings['slice_text'];
        $tooltip_text_options = $settings['tooltip_text_options'];
		$tooltip_text = $settings['tooltip_text'];
        $is_stacked = $settings['is_stacked'];
        $focus_target_options = $settings['focus_target_options'];
		$focus_target = $settings['focus_target'];
        $multiple_data_format_options = $settings['multiple_data_format_options'];
		$multiple_data_format = $settings['multiple_data_format'];
		$opacity = $settings['opacity'];
        $group_width = $settings['group_width'];
        $group_width_format = $settings['group_width_format'];
        $group_width_format_options = $settings['group_width_format_options'];
		$line_width = $settings['line_width'];
		$data_grouping_limit = $settings['data_grouping_limit'];
		$data_grouping_label = $settings['data_grouping_label'];
		$data_grouping_color = $settings['data_grouping_color'];
		$multiple_selection = $settings['multiple_selection'];
        $point_shape_options = $settings['point_shape_options'];
		$point_shape = $settings['point_shape'];
        $crosshair_trigger_options = $settings['crosshair_trigger_options'];
		$crosshair_trigger = $settings['crosshair_trigger'];
        $crosshair_orientation_options = $settings['crosshair_orientation_options'];
		$crosshair_orientation = $settings['crosshair_orientation'];
		$crosshair_opacity = $settings['crosshair_opacity'];
		$dash_style = $settings['dash_style'];
		$point_size = $settings['point_size'];
        $donut_hole_size = $settings['donut_hole_size'];
		$orientation = $settings['orientation'];
		$fill_nulls = $settings['fill_nulls'];
        $allow_collapse = $settings['allow_collapse'];
        $org_classname = $settings['org_classname'];
        $org_node_background_color = $settings['org_node_background_color'];
        $org_node_padding = $settings['org_node_padding'];
        $org_node_border_radius = $settings['org_node_border_radius'];
        $org_node_border_width = $settings['org_node_border_width'];
        $org_node_border_color = $settings['org_node_border_color'];
        $org_node_text_color = $settings['org_node_text_color'];
        $org_node_text_font_size = $settings['org_node_text_font_size'];
        $org_node_description_font_color = $settings['org_node_description_font_color'];
        $org_node_description_font_size = $settings['org_node_description_font_size'];
        $org_selected_classname = $settings['org_selected_classname'];
        $org_selected_node_background_color = $settings['org_selected_node_background_color'];
        $org_selected_node_text_color = $settings['org_selected_node_text_color'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>advanced-settings-data-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-rotation-degree" class="form-label">
                            <?php echo esc_html(__( "Degree of chart rotation", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The angle, in degrees, to rotate the chart by. The default of 0 will orient the leftmost edge of the first slice directly up.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-rotation-degree" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[rotation_degree]" value="<?php echo esc_attr($rotation_degree) ?>">
                    </div>
                </div> <!-- Rotation degree -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-reverse-categories">
                            <?php echo esc_html(__( "Reverse Categories", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If this option is enabled, the pie slices will be drawn counterclockwise.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-reverse-categories" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[reverse_categories]" value="on" <?php echo esc_attr($reverse_categories); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Reverse Categories -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-slice-border-color">
                            <?php echo esc_html(__( "Slice Border Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The color of the slice borders.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-slice-border-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[slice_border_color]" value="<?php echo esc_attr($slice_border_color) ?>">
                    </div>
                </div> <!-- Slice border color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-slice-text">
                            <?php echo esc_html(__( "Slice text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose the content of the text to be displayed on the pie slice.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-slice-text" name="<?php echo esc_attr($html_name_prefix); ?>settings[slice_text]">
                            <?php
                            foreach ( $slice_texts as $option_slug => $option ):
                                $selected = ( $slice_text == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Slice text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-text">
                            <?php echo esc_html(__( "Slice tooltip text", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose how to display the text in the chart tooltip.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-tooltip-text" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_text]">
                            <?php
                            foreach ( $tooltip_text_options as $option_slug => $option ):
                                $selected = ( $tooltip_text == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Slice tooltip Text -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-data-grouping-limit" class="form-label">
                            <?php echo esc_html(__( "Chart Data Grouping Limit", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The percentage value of the pie, below which a slice will not show individually. All slices that have not passed this value will be combined to a single 'Other' slice, whose size is the sum of all their sizes. Default is not to show individually any slice which is smaller than half a degree.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-data-grouping-limit" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[data_grouping_limit]" value="<?php echo esc_attr($data_grouping_limit) ?>" step=".1" min="0" max="360">
                    </div>
                </div> <!-- Data Grouping Limit -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-data-grouping-label" class="form-label">
                            <?php echo esc_html(__( "Chart Data Grouping Label", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("A label for the combination slice that holds all slices below chart data grouping limit.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-data-grouping-label" type="text" name="<?php echo esc_attr($html_name_prefix); ?>settings[data_grouping_label]" value="<?php echo esc_attr($data_grouping_label) ?>">
                    </div>
                </div> <!-- Data Grouping Label -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-data-grouping-color">
                            <?php echo esc_html(__( "Chart Data Grouping Color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Color for the combination slice that holds all slices below chart data grouping limit.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-data-grouping-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[data_grouping_color]" value="<?php echo esc_attr($data_grouping_color) ?>">
                    </div>
                </div> <!-- Data Grouping Color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-is-stacked">
                            <?php echo esc_html(__( "Is stacked", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Is stacked: If this option is enabled, the chart will be displayed in the proportional contribution of individual data points in comparison to a total.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-is-stacked" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[is_stacked]" value="on" <?php echo esc_attr($is_stacked); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Is stacked -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt cb-bar_chart-opt cb-column_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-focus-target">
                            <?php echo esc_html(__( "Focus target", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="<?php 
                                echo htmlspecialchars( sprintf(
                                    "<p>" . __('There are two ways to focus the data table elements.', "chart-builder") . "</p><ul class='ays_tooltop_ul'><li>" .
                                    __('%sSingle data:%s If you choose the Single Data option, the focus will be on the single data point. By this, the particular cell of the data table will be focused.', "chart-builder") . "</li><li>" .
                                    __('%sGroup data:%s If you choose the Group Data option, the focus will be on the grouped data points. By this, a row of the data table will be focused.', "chart-builder") . "</li></ul>",
                                    '<em>',
                                    '</em>',
                                    '<em>',
                                    '</em>'
                                ) );
                            ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-focus-target" name="<?php echo esc_attr($html_name_prefix); ?>settings[focus_target]">
                            <?php
                            foreach ( $focus_target_options as $option_slug => $option ):
                                $selected = ( $focus_target == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Focus target -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt cb-bar_chart-opt cb-column_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-opacity" class="form-label">
                            <?php echo esc_html(__( "Opacity", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The transparency of data points, with 1.0 being completely opaque and 0.0 fully transparent. In bar and column charts, this refers to the visible data: rectangles. In charts where selecting data creates a dot, such as the line chart, this refers to the circles that appear upon hover or selection.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-opacity" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[opacity]" value="<?php echo esc_attr($opacity) ?>" step=".1" min="0" max="1">
                    </div>
                </div> <!-- Data opacity -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-group-width">
                            <?php echo esc_html(__( "Bar width", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="<?php
								echo htmlspecialchars( sprintf(
                                    "<p>" . __('The width of the bars, specified in either of these formats:', "chart-builder") . "</p><ul class='ays_tooltop_ul'><li>" .
                                    __('%sPixels:%s Set the width of the bars in pixels.', "chart-builder") . "</li><li>" .
                                    __('%sPercentage:%s Set the width of the bars in percentage.', "chart-builder") . "</li></ul>",
                                    '<em>',
                                    '</em>',
                                    '<em>',
                                    '</em>'
                                ) );
                            ?>">
							    <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-group-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[group_width]" min="0" step=".1" value="<?php echo esc_attr($group_width) ?>">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-width-format-change <?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-group-width-format" name="<?php echo esc_attr($html_name_prefix); ?>settings[group_width_format]">
                            <?php
                            foreach ( $group_width_format_options as $option_slug => $option ):
                                $selected = ( $group_width_format == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Bars width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-line-width" class="form-label">
                            <?php echo esc_html(__( "Line width", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Data line width in pixels. Set 0 for hiding all the lines and show only the point. Note: If a line width is specified for series, it will override this setting.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-line-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[line_width]" value="<?php echo esc_attr($line_width) ?>">
                    </div>
                </div> <!-- Line width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-multiple-selection">
                            <?php echo esc_html(__( "Multiple data selection", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("When enabled, you may select multiple data points at once. To remove a data point from selection, click on it again. Note: This option works only when 'Tooltip trigger' option is set to 'When selected'.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-multiple-selection" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[multiple_selection]" value="on" <?php echo esc_attr($multiple_selection); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Multiple data selection -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-multiple-data-format">
                            <?php echo esc_html(__( "Multiple data format", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" data-bs-html="true" title="<?php 
                                echo htmlspecialchars( sprintf(
                                    "<p>" . __('How multiple data selections are rolled up into tooltips:', "chart-builder") . "</p><ul class='ays_tooltop_ul'><li>" .
                                    __('%sCategory:%s Group selected data by row titles.', "chart-builder") . "</li><li>" .
                                    __('%sSeries:%s Group selected data by column titles.', "chart-builder") . "</li><li>" .
                                    __('%sAuto:%s Group selected data by row titles, if they have the same titles or by column titles', "chart-builder") . "</li><li>" .
                                    __('%sNone:%s Show only one tooltip per selection.', "chart-builder") . "</li></ul>",
                                    '<em>',
                                    '</em>',
                                    '<em>',
                                    '</em>',
                                    '<em>',
                                    '</em>',
                                    '<em>',
                                    '</em>'
                                ) );
                            ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-multiple-data-format" name="<?php echo esc_attr($html_name_prefix); ?>settings[multiple_data_format]">
                            <?php
                            foreach ( $multiple_data_format_options as $option_slug => $option ):
                                $selected = ( $multiple_data_format == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Multiple data format -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-point-shape">
                            <?php echo esc_html(__( "Point shape", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The shape of individual data elements: 'Circle', 'Triangle', 'Square', 'Diamond', 'Star', or 'Polygon'.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-point-shape" name="<?php echo esc_attr($html_name_prefix); ?>settings[point_shape]">
                            <?php
                            foreach ( $point_shape_options as $option_slug => $option ):
                                $selected = ( $point_shape == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Point shape -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-point-size" class="form-label">
                            <?php echo esc_html(__( "Point size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Diameter of displayed points in pixels. Use zero to hide all points.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-point-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[point_size]" value="<?php echo esc_attr($point_size) ?>">
                    </div>
                </div> <!-- Point size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-orientation">
                            <?php echo esc_html(__( "Rotate Vertical", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable the option, if you want to change the direction of the chart from horizontal to vertical.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-orientation" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[orientation]" value="on" <?php echo esc_attr($orientation); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Orientation -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-fill-nulls">
                            <?php echo esc_html(__( "Autofill nulls", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Whether to guess and autofill the value of missing points when something goes wrong with the chart data. If checked, the chart will automatically fill the value of any missing data based on neighboring points. Else, it will leave a break in the line at the unknown point.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-fill-nulls" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[fill_nulls]" value="on" <?php echo esc_attr($fill_nulls); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Autofill nulls -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-crosshair-trigger">
                            <?php echo esc_html(__( "Crosshair trigger", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Choose when to display crosshairs. Crosshairs are thin vertical and horizontal lines centered on a data point in a chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-crosshair-trigger" name="<?php echo esc_attr($html_name_prefix); ?>settings[crosshair_trigger]">
                            <?php
                            foreach ( $crosshair_trigger_options as $option_slug => $option ):
                                $selected = ( $crosshair_trigger == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Crosshair trigger -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-crosshair-orientation">
                            <?php echo esc_html(__( "Crosshair orientation", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The crosshair orientation, which can be 'vertical' for vertical hairs only, 'horizontal' for horizontal hairs only, or 'both' for traditional crosshairs.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-crosshair-orientation" name="<?php echo esc_attr($html_name_prefix); ?>settings[crosshair_orientation]">
                            <?php
                            foreach ( $crosshair_orientation_options as $option_slug => $option ):
                                $selected = ( $crosshair_orientation == $option_slug ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div> <!-- Crosshair orientation -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-crosshair-opacity" class="form-label">
                            <?php echo esc_html(__( "Crosshair opacity", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The crosshair opacity, with 0.0 being fully transparent and 1.0 fully opaque.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-crosshair-opacity" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[crosshair_opacity]" value="<?php echo esc_attr($crosshair_opacity) ?>" step=".1" min="0" max="1">
                    </div>
                </div> <!-- Crosshair opacity -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-dash-style" class="form-label">
                            <?php echo esc_html(__( "Dash Pattern", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enter a sequence of numbers separated by commas to define the dash pattern. The first number specifies the length of the first dash, the second number specifies the length of the gap after it, and so on. For example, '4,4' will create a pattern of 4-length dashes followed by 4-length gaps, and '5,1,3' will create a pattern of a 5-length dash, a 1-length gap, a 3-length dash, a 5-length gap, and so on.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-dash-style" type="text" name="<?php echo esc_attr($html_name_prefix); ?>settings[dash_style]" value="<?php echo esc_attr($dash_style) ?>">
                    </div>
                </div> <!-- Line dash pattern -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?= $html_class_prefix ?>option-title">
                        <label for="ays-chart-option-donut-hole-size">
				            <?php echo __( "Hole size", $this->plugin_name ); ?>
                        </label>
                    </div>
                    <div class="col-sm-7 <?= $html_class_prefix ?>input-align-right <?= $html_class_prefix ?>option-input">
                        <input class="ays-text-input form-control <?= $html_class_prefix ?>option-text-input" id="ays-chart-option-donut-hole-size"  type="number" min="0" max="1" step=".1" name="<?php echo $html_name_prefix; ?>settings[donut_hole_size]" value="<?= $donut_hole_size ?>">
                    </div>
                </div> <!-- Donut hole size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none ays-pro-features-v2-main-box">
                    <div class="ays-pro-features-v2-small-buttons-box-middle ays-pro-features-v2-big-buttons-box">
                        <div class="ays-pro-features-v2-video-button"></div>
                        <a href="https://ays-pro.com/wordpress/chart-builder" target="_blank" class="ays-pro-features-v2-upgrade-button">
                            <div class="ays-pro-features-v2-upgrade-icon" style="background-image: url('<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg');" data-img-src="<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg"></div>
                            <div class="ays-pro-features-v2-upgrade-text">
                                <?php echo __("Upgrade" , "chart-builder"); ?>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-5 d-flex align-items-center <?= $html_class_prefix ?>option-title">
                        <label for="ays-chart-option-curve-type">
                            <?php echo __( "Line curve type", "chart-builder" ); ?>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <select class="<?= $html_class_prefix ?>option-select-input form-select" id="ays-chart-option-line-curve-type" name="<?php echo $html_name_prefix; ?>settings[line_curve_type]">
                            <option><?php echo __( "Straight", "chart-builder" ); ?></option>
                        </select>
                    </div>
                </div> <!-- Line curve type -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-allow-collapse">
                            <?php echo esc_html(__( "Allow collapse", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("If checked, double click on the node will collapse a node. Note: If the node has an Url option set in the source tab, this option will not work.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-allow-collapse" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[allow_collapse]" value="on" <?php echo esc_attr($allow_collapse); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Allow collapse -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-org-node-description-font-color">
                            <?php echo esc_html(__( "Description color", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Description text color of the node.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-org-node-description-font-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_description_font_color]" value="<?php echo esc_attr($org_node_description_font_color) ?>">
                    </div>
                </div> <!-- Org Node Description color -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-org-node-description-font-size" class="form-label">
                            <?php echo esc_html(__( "Description font size", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Description font size of the node.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-node-description-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_description_font_size]" value="<?php echo esc_attr($org_node_description_font_size) ?>">
                    </div>
                </div> <!-- Org Node Description font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-org-classname" class="form-label">
                            <?php echo esc_html(__( "Custom CSS class", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("A class name to assign to node elements to specify styles for the chart elements.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-classname" type="text" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_classname]" placeholder="chart-builder-org-chart-tree-node" value="<?php echo esc_attr($org_classname) ?>">
                    </div>
                </div> <!-- Org custom class -->
                <div class="form-group mb-2 cb-changable-opt cb-org_chart-opt display_none">
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none" style="background:transparent;box-shadow:unset;padding:10px 0">
                        <br>
                        <blockquote>
                            <?php echo sprintf(__( "%sNote:%s Custom CSS class must be set for the options below to take effect.", $this->plugin_name ), '<strong>', '</strong>'); ?>
                        </blockquote>
                        <br>
                    </div>
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-background-color">
                                <?php echo esc_html(__( "Background color", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Background color of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input id="ays-chart-option-org-node-background-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_background_color]" value="<?php echo esc_attr($org_node_background_color) ?>">
                        </div>
                    </div> <!-- Org Node Background color -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-padding" class="form-label">
                                <?php echo esc_html(__( "Padding", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Padding of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-node-padding" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_padding]" value="<?php echo esc_attr($org_node_padding) ?>">
                        </div>
                    </div> <!-- Org Node Padding -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-border-radius" class="form-label">
                                <?php echo esc_html(__( "Border radius", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Border radius of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-node-border-radius" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_border_radius]" value="<?php echo esc_attr($org_node_border_radius) ?>">
                        </div>
                    </div> <!-- Org Node Border radius -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-border-width" class="form-label">
                                <?php echo esc_html(__( "Border width", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Border width of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-node-border-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_border_width]" value="<?php echo esc_attr($org_node_border_width) ?>">
                        </div>
                    </div> <!-- Org Node Border width -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-border-color">
                                <?php echo esc_html(__( "Border color", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Border color of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input id="ays-chart-option-org-node-border-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_border_color]" value="<?php echo esc_attr($org_node_border_color) ?>">
                        </div>
                    </div> <!-- Org Node Background color -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-text-color">
                                <?php echo esc_html(__( "Text color", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Text color of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input id="ays-chart-option-org-node-text-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_text_color]" value="<?php echo esc_attr($org_node_text_color) ?>">
                        </div>
                    </div> <!-- Org Node Text color -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-node-text-font-size" class="form-label">
                                <?php echo esc_html(__( "Text font size", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Font size of the text of the node. Note: You need to set a Custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-node-text-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_node_text_font_size]" value="<?php echo esc_attr($org_node_text_font_size) ?>">
                        </div>
                    </div> <!-- Org Node Text font size -->
                </div> <!-- Org custom class options -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-org-selected-classname" class="form-label">
                            <?php echo esc_html(__( "Selected node custom CSS class", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("A class name to assign to node elements to specify styles for the selected chart elements.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-org-selected-classname" type="text" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_selected_classname]" placeholder="chart-builder-org-chart-tree-node-selected" value="<?php echo esc_attr($org_selected_classname) ?>">
                    </div>
                </div> <!-- Org custom selected class -->
                <div class="form-group mb-2 cb-changable-opt cb-org_chart-opt display_none">
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none" style="background:transparent;box-shadow:unset;padding:10px 0">
                        <br>
                        <blockquote>
                            <?php echo sprintf(__( "%sNote:%s Selected node custom CSS class must be set for the options below to take effect.", $this->plugin_name ), '<strong>', '</strong>'); ?>
                        </blockquote>
                        <br>
                    </div>
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-selected-node-background-color">
                                <?php echo esc_html(__( "Background color", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Background color of the selected node. Note: You need to set a Selected node custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input id="ays-chart-option-org-selected-node-background-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_selected_node_background_color]" value="<?php echo esc_attr($org_selected_node_background_color) ?>">
                        </div>
                    </div> <!-- Org selected Node Background color -->
                    <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-org_chart-opt display_none">
                        <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                            <label for="ays-chart-option-org-selected-node-text-color">
                                <?php echo esc_html(__( "Text color", "chart-builder" )); ?>
                                <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Text color of the selected node. Note: You need to set a Selected node custom CSS class for this option to work.","chart-builder") ); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                            <input id="ays-chart-option-org-selected-node-text-color" class="form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[org_selected_node_text_color]" value="<?php echo esc_attr($org_selected_node_text_color) ?>">
                        </div>
                    </div> <!-- Org selected Node text color -->
                </div> <!-- Org custom selected class options -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$sources['advanced_settings'] = array(
			'content' => $content,
			'title' => $tab_title
		);

		return $sources;
	}
    
    public function settings_contents_advanced_settings_chartjs( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
        $tab_title = $args['tab_name'];

        $outer_radius = $settings['outer_radius'];
        $slice_spacing = $settings['slice_spacing'];
        $circumference = $settings['circumference'];
        $start_angle = $settings['start_angle'];
        // $index_axis = $settings['index_axis'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>advanced-settings-data-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-outer-radius" class="form-label">
                            <?php echo esc_html(__( "Outer Radius", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The outer radius of the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-outer-radius" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[outer_radius]" value="<?php echo esc_attr($outer_radius) ?>">
                        <div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Outer Radius -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-slice-spacing" class="form-label">
                            <?php echo esc_html(__( "Slices Spacing", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Fixed arc offset (in pixels).","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-slice-spacing" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[slice_spacing]" value="<?php echo esc_attr($slice_spacing) ?>">
                        <div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Slice Spacing -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-circumference" class="form-label">
                            <?php echo esc_html(__( "Arc Coverage", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Adjust the sweep angle to define the arc's coverage area along the circumference.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-circumference" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[circumference]" value="<?php echo esc_attr($circumference) ?>">
                        <div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">°</div>
                    </div>
                </div> <!-- Arc Coverage -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-start-angle" class="form-label">
                            <?php echo esc_html(__( "Start Angle", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Starting angle to draw arcs from.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-start-angle" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[start_angle]" value="<?php echo esc_attr($start_angle) ?>">
                        <div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">°</div>
                    </div>
                </div> <!-- Start Angle -->
                <!-- <div class="form-group row mb-2 <?php // echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-pie_chart-opt cb-donut_chart-opt display_none">
                    <div class="col-sm-5 d-flex align-items-center <?php // echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-index-axis">
                            <?php // echo esc_html(__( "Horizontal ", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php // echo esc_attr( __("If this option is enabled, the pie slices will be drawn counterclockwise.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php // echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php // echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php // echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-index-axis" type="checkbox" name="<?php // echo esc_attr($html_name_prefix); ?>settings[index_axis]" value="on" <?php // echo esc_attr($index_axis); ?> >
                            <span class="<?php // echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php // echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> Reverse Categories -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$sources['advanced_settings'] = array(
			'content' => $content,
			'title' => $tab_title
		);

		return $sources;
	}
    
    public function settings_contents_slices_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
		$source = $args['source'];
        $source = isset($source["commonTypeCharts"]) ? $source["commonTypeCharts"] : $source;
		$source_chart_type = $args['source_chart_type'];
        $settings = $args['settings'];
        $title_row = array_shift($source);

        if (isset($source) && !empty($source)) {
            if ( !isset( $source[0] ) ) {
                if (count($source[1]) > 2) {
                    $titles = array();
                    for ($i = 0; $i < count($source[1]); $i++) {
                        array_push($titles, __("Title", $this->plugin_name).$i);
                    }
                    $source[0] = $titles;
                } else {
                    $source[0] = array(
                        __("Country", $this->plugin_name),
                        __("Population", $this->plugin_name),
                    );
                }
    
                ksort($source);
            }
        }

		if ($source_chart_type == 'pie_chart' || $source_chart_type == 'donut_chart') {
			foreach ($source as $key => $row) {
				$source[$key] = array_slice($row, 0, 2);
			}
		}

        ob_start();
        if ($source_chart_type == 'pie_chart' || $source_chart_type == 'donut_chart')  {
            $slice_color = $settings['slice_color'];
            $slice_colors_default = $settings['slice_colors_default'];
            $slice_offset = $settings['slice_offset'];
            $slice_text_color = $settings['slice_text_color'];

            ?>
            <div class="ays-accordion-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>options-slices-settings-tab cb-changable-tab cb-pie_chart-tab cb-donut_chart-tab">
                <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                    <div class="<?php echo esc_attr($html_class_prefix) ?>slices-settings">
                        <?php
                        foreach ( $source as $key => $val ):
                        ?>
                            <fieldset class="ays-slices-accordion-options-container <?php echo esc_attr($html_class_prefix) ?>options-slices-<?php echo esc_attr($key); ?>" data-collapsed="true">
                                <legend class="ays-slices-accordion-options-header">
                                    <svg class="ays-slices-accordion-arrow ays-slices-accordion-arrow-right" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                                        <g>
                                            <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: #c4c4c4;" vector-effect="non-scaling-stroke" />
                                        </g>
                                    </svg>
                                    <span><?php echo $val[0]; ?></span>
                                </legend>
                                <div class="ays-slices-accordion-options-content ays-slices-accordion-options-content">
                                    <div class="ays-slices-accordion-data-main-wrap">
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-slice-color">
                                                        <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                                                        <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The color to use for this slice.","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <input type="color" 
                                                        id="ays-chart-option-slice-color-<?php echo $key; ?>" 
                                                        class="ays-chart-option-slice-color form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[slice_color][<?php echo $key; ?>]" 
                                                        value="<?php echo isset($slice_color[$key]) ? esc_attr($slice_color[$key]) : $slice_colors_default[$key]; ?>" 
                                                        data-slice-id="<?php echo $key; ?>">
                                                </div>
                                            </div> <!-- Slice color -->
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-slice-offset" class="form-label">
                                                        <?php echo esc_html(__( "Offset", "chart-builder" )); ?>
                                                        <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("How far to separate the slice from the rest of the pie, from 0.0 (not at all) to 1.0 (the chart's radius).","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <input type="number" 
                                                        id="ays-chart-option-slice-offset-<?php echo $key; ?>" 
                                                        class="ays-chart-option-slice-offset ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[slice_offset][<?php echo $key; ?>]" 
                                                        value="<?php echo isset($slice_offset[$key]) ? esc_attr($slice_offset[$key]) : 0 ; ?>" 
                                                        data-slice-id="<?php echo $key; ?>" 
                                                        step=".01" min="0.0" max="1.0">
                                                </div>
                                            </div> <!-- Slice offset -->
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-slice-text-color">
                                                        <?php echo esc_html(__( "Text color", "chart-builder" )); ?>
                                                        <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars( __("The color to use for the text of this slice.","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <input type="color" 
                                                        id="ays-chart-option-slice-text-color-<?php echo $key; ?>" 
                                                        class="ays-chart-option-slice-text-color form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[slice_text_color][<?php echo $key; ?>]" 
                                                        value="<?php echo isset($slice_text_color[$key]) ? esc_attr($slice_text_color[$key]) : '#ffffff'; ?>" 
                                                        data-slice-id="<?php echo $key; ?>">
                                                </div>
                                            </div> <!-- Slice color -->
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            <br>
            <blockquote>
                <?php echo __( "Save the chart to update the data.", $this->plugin_name ); ?>
            </blockquote>
            </div>
            <?php

        } else {
            ?>
            <div class="ays-accordion-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>options-slices-settings-tab cb-changable-tab cb-pie_chart-tab cb-donut_chart-tab"></div>
            <?php
        }

		$content = ob_get_clean();

		$title = __( 'Slices settings', "chart-builder" );

		$sources['slices_settings'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_series_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
		$source = $args['source'];
        $source = isset($source["commonTypeCharts"]) ? $source["commonTypeCharts"] : $source;
		$source_chart_type = $args['source_chart_type'];
        
        $line_width = $settings['line_width'];
        $point_size = $settings['point_size'];
        $point_shape_options = $settings['point_shape_options'];
        $point_shape = $settings['point_shape'];

        if (isset($source) && !empty($source)) {
            if ( !isset( $source[0] ) ) {
                if (count($source[1]) > 2) {
                    $titles = array();
                    for ($i = 0; $i < count($source[1]); $i++) {
                        array_push($titles, __("Title", $this->plugin_name).$i);
                    }
                    $source[0] = $titles;
                } else {
                    $source[0] = array(
                        __("Country", $this->plugin_name),
                        __("Population", $this->plugin_name),
                    );
                }
    
                ksort($source);
            }
        }

		if ($source_chart_type == 'pie_chart' || $source_chart_type == 'donut_chart') {
			foreach ($source as $key => $row) {
				$source[$key] = array_slice($row, 0, 2);
			}
		}
		
        ob_start();
        if ($source_chart_type == 'pie_chart' || $source_chart_type == 'line_chart' || $source_chart_type == 'bar_chart' || $source_chart_type == 'column_chart') {
			$series_row = array_shift($source);
			$title = array_shift($series_row);
            $series_color = $settings['series_color'];
            $series_colors_default = $settings['series_colors_default'];
            $series_visible_in_legend = $settings['series_visible_in_legend'];
            $series_line_width = $settings['series_line_width'];
            $series_point_size = $settings['series_point_size'];
            $series_point_shape = $settings['series_point_shape'];

            ?>
            <div class="ays-accordion-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>options-series-settings-tab cb-changable-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
                <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                    <div class="<?php echo esc_attr($html_class_prefix) ?>series-settings">
                        <?php
                        foreach ( $series_row as $key => $val ):
                        ?>
                            <fieldset class="ays-series-accordion-options-container <?php echo esc_attr($html_class_prefix) ?>options-series-<?php echo esc_attr($key); ?>" data-collapsed="true">
                                <legend class="ays-series-accordion-options-header">
                                    <svg class="ays-series-accordion-arrow ays-series-accordion-arrow-right" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                                        <g>
                                            <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: #c4c4c4;" vector-effect="non-scaling-stroke" />
                                        </g>
                                    </svg>
                                    <span><?php echo $val; ?></span>
                                </legend>
                                <div class="ays-series-accordion-options-content ays-series-accordion-options-content">
                                    <div class="ays-series-accordion-data-main-wrap">
                                        <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-series-color">
                                                        <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <input type="color" 
                                                        id="ays-chart-option-series-color-<?php echo $key; ?>" 
                                                        class="ays-chart-option-series-color form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[series_color][<?php echo $key; ?>]" 
                                                        value="<?php echo isset($series_color[$key]) ? esc_attr($series_color[$key]) : $series_colors_default[$key]; ?>" 
                                                        data-series-id="<?php echo $key; ?>">
                                                </div>
                                            </div> <!-- series color -->
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-series-visible-in-legend">
                                                        <?php echo esc_html(__( "Visible in legend", "chart-builder" )); ?>
                                                    <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Tick this option to have the series shown in the legend, otherwise it won`t be shown. If the chart has only one column, disabling this option will not work. If you don't want any of the labels to be shown in the legend, change the 'Legend position' option to 'Hide' to entirely hide the legend .","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                                                        <input type="checkbox" 
                                                            id="ays-chart-option-series-visible-in-legend-<?php echo $key; ?>" 
                                                            class="ays-chart-option-series-visible-in-legend <?php echo esc_attr($html_class_prefix) ?>toggle-switch" 
                                                            name="<?php echo esc_attr($html_name_prefix); ?>settings[series_visible_in_legend][<?php echo $key; ?>]" 
                                                            value="on" 
                                                            data-series-id="<?php echo $key; ?>" 
                                                            <?php echo isset($series_visible_in_legend[$key]) && $series_visible_in_legend[$key] == 'on' ? 'checked' : (!isset($series_color[$key]) ? 'checked' : ''); ?> >
                                                        <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                                                    </label>
                                                </div>
                                            </div> <!-- Series visible in legend -->
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-series-line-width">
                                                        <?php echo esc_html(__( "Line width", "chart-builder" )); ?>
                                                        <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Line width in pixels. Set 0 for hiding all the lines and show only the point.","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <input type="number" 
                                                        id="ays-chart-option-series-line-width-<?php echo $key; ?>" 
                                                        class="ays-chart-option-series-line-width ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[series_line_width][<?php echo $key; ?>]" 
                                                        value="<?php echo isset($series_line_width[$key]) ? esc_attr($series_line_width[$key]) : $line_width; ?>" 
                                                        data-series-id="<?php echo $key; ?>">
                                                </div>
                                            </div> <!-- Line width -->
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-series-point-size">
                                                        <?php echo esc_html(__( "Point size", "chart-builder" )); ?>
                                                        <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Point size in pixels. Set 0 for hiding all points.","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <input type="number" 
                                                        id="ays-chart-option-series-point-size-<?php echo $key; ?>" 
                                                        class="ays-chart-option-series-point-size ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[series_point_size][<?php echo $key; ?>]" 
                                                        value="<?php echo isset($series_point_size[$key]) ? esc_attr($series_point_size[$key]) : $point_size; ?>" 
                                                        data-series-id="<?php echo $key; ?>">
                                                </div>
                                            </div> <!-- Point size -->
                                            <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-line_chart-opt display_none">
                                                <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                    <label for="ays-chart-option-series-point-shape">
                                                        <?php echo esc_html(__( "Point shape", "chart-builder" )); ?>
                                                        <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("The shape of individual data elements: 'Circle', 'Triangle', 'Square', 'Diamond', 'Star', or 'Polygon'.","chart-builder") ); ?>">
                                                            <i class="ays_fa ays_fa_info_circle"></i>
                                                        </a>
                                                    </label>
                                                </div>
                                                <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                    <select
                                                        id="ays-chart-option-series-point-shape-<?php echo $key; ?>" 
                                                        class="ays-chart-option-series-point-shape <?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" 
                                                        name="<?php echo esc_attr($html_name_prefix); ?>settings[series_point_shape][<?php echo $key; ?>]" 
                                                        data-series-id="<?php echo $key; ?>">
                                                    >
                                                        <?php
                                                        $value = isset($series_point_shape[$key]) ? esc_attr($series_point_shape[$key]) : $point_shape;
                                                        foreach ( $point_shape_options as $option_slug => $option ):
                                                            $selected = ( $value === $option_slug ) ? 'selected' : '';
                                                            ?>
                                                            <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
                                                        <?php
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                </div>
                                            </div> <!-- Point shape -->
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            <br>
            <blockquote>
                <?php echo __( "Save the chart to update the data.", $this->plugin_name ); ?>
                <br>
                <?php echo sprintf(__( "%sNote:%s If you are not able to set the options, disable the row settings feature.", $this->plugin_name ), '<strong>', '</strong>'); ?>
            </blockquote>
            </div>
            <?php

        } else {
            ?>
            <div class="ays-accordion-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>options-series-settings-tab cb-changable-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab"></div>
            <?php
        }

		$content = ob_get_clean();

		$title = __( 'Series settings', "chart-builder" );

		$sources['series_settings'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

    public function settings_contents_row_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
		$source = $args['source'];
        $source = isset($source["commonTypeCharts"]) ? $source["commonTypeCharts"] : $source;
		$source_chart_type = $args['source_chart_type'];

        if (isset($source) && !empty($source)) {
            if ( !isset( $source[0] ) ) {
                if (count($source[1]) > 2) {
                    $titles = array();
                    for ($i = 0; $i < count($source[1]); $i++) {
                        array_push($titles, __("Title", $this->plugin_name).$i);
                    }
                    $source[0] = $titles;
                } else {
                    $source[0] = array(
                        __("Country", $this->plugin_name),
                        __("Population", $this->plugin_name),
                    );
                }
    
                ksort($source);
            }
        }

        array_shift($source);
        $rows = array_column($source, 0);

        $enable_row_settings = $settings['enable_row_settings'];
        $series_color = $settings['series_color'][0] ?? '#3366cc';
        $rows_color = $settings['rows_color'];
        $rows_opacity = $settings['rows_opacity'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap <?php echo esc_attr($html_class_prefix) ?>options-row-settings-tab cb-changable-tab cb-bar_chart-tab cb-column_chart-tab cb-line_chart-tab">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section cb-changable-opt cb-bar_chart-opt cb-column_chart-opt cb-line_chart-opt">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-enable-row-settings">
                            <?php echo esc_html(__( "Enable row settings", "chart-builder" )); ?>
                            <a class="ays_help" data-bs-toggle="tooltip" title="<?php echo esc_attr( __("Enable this option to be able to choose the row settings. If this option is disabled, you will be able to set the general color form the series settings.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-1 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch <?php echo esc_attr($html_class_prefix) ?>toggle-hidden-option" id="ays-chart-option-enable-row-settings" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[enable_row_settings]" value="on" <?php echo esc_attr($enable_row_settings); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Enable row settings -->
                <div class="<?php echo esc_attr($html_class_prefix) ?>rows-settings <?php echo esc_attr($html_class_prefix) ?>hidden-options-section <?php echo $enable_row_settings === 'checked' ? '' : 'display_none'; ?>">
                    <br>
                    <?php
                    foreach ( $rows as $key => $val ):
                    ?>
                        <fieldset class="ays-rows-accordion-options-container <?php echo esc_attr($html_class_prefix) ?>options-rows-<?php echo esc_attr($key); ?>" data-collapsed="true">
                            <legend class="ays-rows-accordion-options-header">
                                <svg class="ays-rows-accordion-arrow ays-rows-accordion-arrow-right" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                                    <g>
                                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: #c4c4c4;" vector-effect="non-scaling-stroke" />
                                    </g>
                                </svg>
                                <span><?php echo $val; ?></span>
                            </legend>
                            <div class="ays-rows-accordion-options-content ays-rows-accordion-options-content">
                                <div class="ays-rows-accordion-data-main-wrap">
                                    <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                                        <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                            <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                <label for="ays-chart-option-rows-color">
                                                    <?php echo esc_html(__( "Color", "chart-builder" )); ?>
                                                </label>
                                            </div>
                                            <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                <input type="color" 
                                                    id="ays-chart-option-rows-color-<?php echo $key; ?>" 
                                                    class="ays-chart-option-rows-color form-control-color <?php echo esc_attr($html_class_prefix) ?>option-color-picker" 
                                                    name="<?php echo esc_attr($html_name_prefix); ?>settings[rows_color][<?php echo $key; ?>]" 
                                                    value="<?php echo isset($rows_color[$key]) && '' !== $rows_color[$key] ? esc_attr($rows_color[$key]) : $series_color; ?>" 
                                                    data-rows-id="<?php echo $key; ?>">
                                            </div>
                                        </div> <!-- color -->
                                        <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                                            <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                                                <label for="ays-chart-option-rows-color">
                                                    <?php echo esc_html(__( "Opacity", "chart-builder" )); ?>
                                                </label>
                                            </div>
                                            <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                                                <input type="number"
                                                    id="ays-chart-option-rows-opacity-<?php echo $key; ?>" 
                                                    class="ays-chart-option-rows-opacity ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" 
                                                    name="<?php echo esc_attr($html_name_prefix); ?>settings[rows_opacity][<?php echo $key; ?>]" 
                                                    value="<?php echo isset($rows_opacity[$key]) && '' !== $rows_opacity[$key] ? esc_attr($rows_opacity[$key]) : 1.0; ?>" 
                                                    data-rows-id="<?php echo $key; ?>" 
                                                    step=".1" min="0" max="1">
                                            </div>
                                        </div> <!-- opacity -->
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    <?php
                    endforeach;
                    ?>
                    <br>
                    <blockquote>
                        <?php echo __( "Save the chart to update the data.", $this->plugin_name ); ?>
                        <br>
                        <?php echo sprintf(__( "%sNote:%s The applied styles will work only if the chart has one column.", $this->plugin_name ), '<strong>', '</strong>'); ?>
                    </blockquote>
                </div>
                <div class="<?php echo esc_attr($html_class_prefix) ?>not-hidden-options-section <?php echo $enable_row_settings === 'checked' ? 'display_none' : ''; ?>">
                    <br>
                    <blockquote>
                        <?php echo sprintf(__( "%sNote:%s If this option is disabled, the general options will be set from the series settings.", $this->plugin_name ), '<strong>', '</strong>'); ?>
                    </blockquote>
                </div>
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Row settings', "chart-builder" );

		$sources['row_settings'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}
}
