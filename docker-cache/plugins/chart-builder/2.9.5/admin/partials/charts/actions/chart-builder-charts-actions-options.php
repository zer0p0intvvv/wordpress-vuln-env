<?php
    if (isset($_GET['ays_chart_tab'])) {
        $ays_chart_tab = esc_attr($_GET['ays_chart_tab']);
    } else {
        $ays_chart_tab = 'tab1';
    }
    $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

    $id = (isset($_GET['id'])) ? absint( esc_attr($_GET['id']) ) : 0;

    $get_all_charts = CBActions()->get_charts('DESC');

    $does_id_exist = false;
    if (!empty($get_all_charts)) {
        $does_id_exist = in_array($id, array_column($get_all_charts, 'id'));
    }

    if (!$does_id_exist && $action == 'edit') {
        $url = remove_query_arg( array('action', 'id', 'status', 'ays_chart_tab') );
        wp_redirect( $url );
    }

    $html_name_prefix = 'ays_';
    $html_class_prefix = 'ays-chart-';

    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    $options = array();

    $google_charts = array(
        'line_chart' => [
            'name' => 'Line Chart',
            'icon' => 'line-chart-logo.png',
            'demo' => 'http://bit.ly/3P2ZDmy',
            'pro' => false,
        ],
        'bar_chart' => [
            'name' => 'Bar Chart',
            'icon' => 'bar-chart-logo.png',
            'demo' => 'http://bit.ly/3iuLxxV',
            'pro' => false,
        ],
        'pie_chart' => [
            'name' => 'Pie Chart',
            'icon' => 'pie-chart-logo.png',
            'demo' => 'http://bit.ly/3BgvACe',
            'pro' => false,
        ],
        'column_chart' => [
            'name' => 'Column Chart',
            'icon' => 'column-chart-logo.png',
            'demo' => 'http://bit.ly/3Pc1CFe',
            'pro' => false,
        ],
        'org_chart' => [
            'name' => 'Org Chart',
            'icon' => 'org-chart-logo.png',
            'demo' => 'https://bit.ly/3VQXop7',
            'pro' => false,
        ],
        'donut_chart' => [
            'name' => 'Donut Chart',
            'icon' => 'donut-chart-logo.png',
            'demo' => 'http://bit.ly/3HgvEWi',
            'pro' => false,
        ],
	    'histogram' => [
            'name' => 'Histogram',
            'icon' => 'histogram-logo.png',
            'demo' => 'http://bit.ly/3upA59L',
            'pro' => true,
        ],
        'geo_chart' => [
            'name' => 'Geo Chart',
            'icon' => 'geo-chart-logo.png',
            'demo' => 'http://bit.ly/3iIq4Sc',
            'pro' => true,
        ],
        'area_chart' => [
            'name' => 'Area Chart',
            'icon' => 'area-chart-logo.png',
            'demo' => 'https://ays-demo.com/area-chart-demo/',
            'pro' => true,
        ],
        'gauge_chart' => [
            'name' => 'Gauge Chart',
            'icon' => 'gauge-chart-logo.png',
            'demo' => 'https://ays-demo.com/gauge-chart-demo/',
            'pro' => true,
        ],
        'combo_chart' => [
            'name' => 'Combo Chart',
            'icon' => 'combo-chart-logo.png',
            'demo' => 'https://ays-demo.com/combo-chart-demo/',
            'pro' => true,
        ],
        'stepped_area_chart' => [
            'name' => 'Stepped Area Chart',
            'icon' => 'stepped-area-chart-logo.png',
            'demo' => 'https://ays-demo.com/stepped-area-chart-demo/',
            'pro' => true,
        ],
        'bubble_chart' => [
            'name' => 'Bubble Chart',
            'icon' => 'bubble-chart-logo.png',
            'demo' => 'https://ays-demo.com/bubble-chart-demo/',
            'pro' => true,
        ],
        'scatter_chart' => [
            'name' => 'Scatter Chart',
            'icon' => 'scatter-chart-logo.png',
            'demo' => 'https://ays-demo.com/scatter-chart-demo/',
            'pro' => true,
        ],
        'table_chart' => [
            'name' => 'Table Chart',
            'icon' => 'table-chart-logo.png',
            'demo' => 'https://ays-demo.com/table-chart-demo/',
            'pro' => true,
        ],
        'timeline_chart' => [
            'name' => 'Timeline Chart',
            'icon' => 'timeline-chart-logo.png',
            'demo' => 'https://ays-demo.com/timeline-chart-demo/',
            'pro' => true,
        ],
        'candlestick_chart' => [
            'name' => 'Candlestick Chart',
            'icon' => 'candlestick-chart-logo.png',
            'demo' => 'https://ays-demo.com/candlestick-chart-demo/',
            'pro' => true,
        ],
        'gantt_chart' => [
            'name' => 'Gantt Chart',
            'icon' => 'gantt-chart-logo.png',
            'demo' => 'https://ays-demo.com/gantt-chart-demo/',
            'pro' => true,
        ],
        'sankey_diagram' => [
            'name' => 'Sankey Diagram',
            'icon' => 'sankey-chart-logo.png',
            'demo' => 'https://ays-demo.com/sankey-diagram-chart-demo/',
            'pro' => true,
        ],
        'treemap' => [
            'name' => 'Treemap',
            'icon' => 'treemap-chart-logo.png',
            'demo' => 'https://ays-demo.com/threemap-chart-demo/',
            'pro' => true,
        ],
        'word_tree' => [
            'name' => 'Word Tree',
            'icon' => 'word-tree-logo.png',
            'demo' => 'https://ays-demo.com/word-tree-chart-demo/',
            'pro' => true,
        ],
        '3dpie_chart' => [
            'name' => '3D Pie Chart',
            'icon' => '3d-pie-chart-logo.png',
            'demo' => 'https://ays-demo.com/3d-pie-chart-demo/',
            'pro' => true,
        ],
    );

    $chartjs_charts = array(
        'line_chart' => [
            'name' => 'Line Chart',
            'icon' => 'line-chart-logo.png',
            'demo' => '',
            'pro' => false,
        ],
        'bar_chart' => [
            'name' => 'Bar Chart',
            'icon' => 'bar-chart-logo.png',
            'demo' => '',
            'pro' => false,
        ],
        'pie_chart' => [
            'name' => 'Pie Chart',
            'icon' => 'pie-chart-logo.png',
            'demo' => '',
            'pro' => false,
        ],
    );

    $chart_types = array(
        'line_chart'   => "Line Chart",
        'bar_chart'    => "Bar Chart",
        'pie_chart'    => "Pie Chart",
        'column_chart' => "Column Chart",
        'org_chart'    => 'Org Chart',
        'donut_chart'  => 'Donut Chart',
    );

    $chart_source_types = array(
        'google-charts' => "Google Charts",
        'chart-js'      => "Chart.js",
    );

    $chart_types_names = array(
        'line_chart'   => "Line",
        'bar_chart'    => "Bar",
        'pie_chart'    => "Pie",
        'column_chart' => "Column",
        'org_chart'    => 'Org',
        'donut_chart'  => 'Donut',
    );

    $object = array(
        'title' => '',
        'description' => '',
        'type' => 'google-charts',
        'source_chart_type' => 'pie_chart',
        'source_type' => 'manual',
        'source' => '',
        'status' => 'published',
        'date_created' => current_time( 'mysql' ),
        'date_modified' => current_time( 'mysql' ),
        'options' => json_encode( $options ),
    );

    $chart_data = array(
        'chart' => $object,
        'source_type' => 'manual',
        'source' => '',
        'settings' => array(),
        'options' => array(),
    );

    $similar_charts = array(
        'pie_chart' => array(
            'pie_chart' => 'pie-chart.png',
            'donut_chart' => 'donut-chart.png',
        ),
        'donut_chart' => array(
            'pie_chart' => 'pie-chart.png',
            'donut_chart' => 'donut-chart.png',
        ),
        'bar_chart' => array(
            'bar_chart' => 'bar-chart.png',
            'column_chart' => 'column-chart.png',
            'line_chart' => 'line-chart.png',
        ),
        'line_chart' => array(
            'bar_chart' => 'bar-chart.png',
            'column_chart' => 'column-chart.png',
            'line_chart' => 'line-chart.png',
        ),
        'column_chart' => array(
            'bar_chart' => 'bar-chart.png',
            'column_chart' => 'column-chart.png',
            'line_chart' => 'line-chart.png',
        ),
    );

    $quiz_queries = array(
        'q1' => __("The number of times all the users have passed the particular quiz", "chart-builder"),
        'q2' => __("The number of times the current user has passed all quizzes daily", "chart-builder"),
        'q3' => __("The number of times the current user has passed the current quiz", "chart-builder"),
        'q4' => __("The average score of current user of each quiz", "chart-builder"),
        'q5' => __("The number of times the current user has passed each quiz overall", "chart-builder"),
        'q6' => __("The current user's scores of the chosen quiz", "chart-builder"),
        'q7' => __("The average scores of current user of the quizzes for each quiz category (PRO)", "chart-builder"),
        'q8' => __("The number of times the user passed the chosen category quizzes (PRO)", "chart-builder"),
        'q9' => __("The number of people who got the particular score (PRO)", "chart-builder"),
        'q10' => __("The number of people based on the particular Interval score (PRO)", "chart-builder"),
        'q11' => __("The count of the logged-in users and guests for the last 7 days (PRO)", "chart-builder"),
        'q12' => __("The answers count for each question of the chosen quiz (PRO)", "chart-builder"),
        'q13' => __("The answers count of the chosen quiz/question category (PRO)", "chart-builder"),
        'q14' => __("The number of times all the users passed all the quizzes for the last 7 days (PRO)", "chart-builder")
    );

    $quiz_query_tooltips = array(
        '' => __( 'Select a query to display quiz data.', "chart-builder" ),
        'q1' => __("If you enable this option, you can display how many times all the users passed the particular quiz.", "chart-builder"),
        'q2' => __("If you enable this option, you can display how many times the current user has passed all quizzes on a daily basis.", "chart-builder"),
        'q3' => __("If you enable this option, you can display how many times the current user has passed the particular quiz.", "chart-builder"),
        'q4' => __("If you enable this option, you can show the average score of each quiz the current user has passed.", "chart-builder"),
        'q5' => __("If you enable this option, you can display how many times the current user has passed each quiz in general.", "chart-builder"),
        'q6' => __("If you enable this option, the scores the current user got for the particular quiz will be displayed.", "chart-builder"),
        'q7' => __("If you enable this option, the average scores of quizzes of each quiz category the current user has passed will be displayed for all users.", "chart-builder"),
        'q8' => __("If you enable this option, you can show how many times the current user passed the quizzes of each category.", "chart-builder"),
        'q9' => __("If you enable this option, you can group users by score. That is how many people received a particular grade for the chosen quiz.", "chart-builder"),
        'q10' => __("If you enable this option, you can display the number of people with their score by Intervals.", "chart-builder"),
        'q11' => __("If you enable this option, you can show the count statistics of the logged-in and non-logged-in users (guests) who passed the particular quiz within the last 7 days.", "chart-builder"),
        'q12' => __("If you enable this option, you can show all the answers count (Correct/Incorrect/Unanswered) of the chosen quiz for each question of each user.", "chart-builder"),
        'q13' => "<ul style='padding: 0; list-style-type: none;'>" .
                   '<li>' . sprintf( __("%sBy%s - Choose the method of filtering."), '<b>', '</b>' ) . '</li>' .
                   '<li>' . sprintf( __("%sBy quiz:%s Display all the questions of the chosen quiz"), '<b>', '</b>' ) . '</li>' .
                   '<li>' . sprintf( __("%sBy question category:%s Display all questions of the chosen category (of all quizzes)"), '<b>', '</b>' ) . '</li>' .
                   '<li>' . sprintf( __("%sBy quiz and question category:%s Display all the questions of the particular category of the chosen quiz", "chart-builder"), '<b>', '</b>' ) . '</li>' .
                '</ul>',
        'q14' => __("If you enable this option, you can display the number of times all the users (logged-in/guests) passed all the quizzes within the last 7 days", "chart-builder")
    );

    $heading = '';
    switch ($action) {
        case 'add':
            $heading = __( 'Add new chart', "chart-builder" );
            break;
        case 'edit':
            $heading = __( 'Edit chart', "chart-builder" );
            $object = $this->db_obj->get_item( $id );
            $chart_data = CBActions()->get_chart_data( $id );
            break;
    }

    if( isset( $_POST['ays_submit'] ) || isset( $_POST['ays_submit_top'] ) ) {
        $this->db_obj->add_or_edit_item( $id );
    }

    if( isset( $_POST['ays_apply'] ) || isset( $_POST['ays_apply_top'] ) ){
        $_POST['save_type'] = 'apply';
        $this->db_obj->add_or_edit_item( $id );
    }

    if( isset( $_POST['ays_save_new'] ) || isset( $_POST['ays_save_new_top'] ) ){
        $_POST['save_type'] = 'save_new';
        $this->db_obj->add_or_edit_item( $id );
    }

    $loader_iamge = '<span class="display_none ays_chart_loader_box"><img src="'. CHART_BUILDER_ADMIN_URL .'/images/loaders/loading.gif"></span>';

    /**
     * Data that need to get form @object variable
     *
     * @object is a data directly from database
     */

        // Date created
        $date_created = isset( $object['date_created'] ) && CBFunctions()->validateDate( $object['date_created'] ) ? esc_attr($object['date_created']) : current_time( 'mysql' );

        // Date modified
        $date_modified = current_time( 'mysql' );

        /**
         * Data that need to get form @chart variable
         */

            // Chart
            $chart = $chart_data['chart'];

            // Source type
            $source_type = stripslashes( $chart['source_type'] );

            if ($action === "add") {
                // Chart source type
                $allowed_sources = ['google-charts', 'chart-js'];
                $chart_source_type = isset($_GET['source']) && in_array($chart_source_type, $allowed_sources, true) ? sanitize_text_field($_GET['source']) : 'google-charts';

                // Chart type
                $source_chart_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'pie_chart';
            } else {
                $chart_source_type = stripslashes( $chart['type'] );
                $source_chart_type = stripslashes( $chart['source_chart_type'] );
            }

            if ($chart_source_type === 'chart-js') {
                $chart_source_default_data = CBActions()->get_charts_default_data_chartjs();
            } else {
                $chart_source_default_data = CBActions()->get_charts_default_data_google();
                if (!isset($chart_data['source']) || empty($chart_data['source'])) {
                    $chart_source_default_data = ($source_chart_type == 'org_chart') ? $chart_source_default_data['orgTypeChart'] : $chart_source_default_data['commonTypeCharts'];
                }
            }

            // Source
            $source = isset($chart_data['source']) && !empty($chart_data['source']) ? $chart_data['source'] : $chart_source_default_data;

            //Source Ordering for Org Chart Type
            $ordering = [];
            if (isset($chart_data['source']) && !empty($chart_data['source'])) {
                foreach($source as $key => $value) {
                    if ($key != 0) {
                        array_push($ordering, $key);
                    }
                }
            } else {
                $ordering = [1, 2, 3, 4, 5];
            }

            // Title
            $title = stripcslashes( $chart['title'] );

            // Description
            $description = stripcslashes( $chart['description'] );

            // Status
            $status = stripslashes( $chart['status'] );

            // Quiz query
            $quiz_query = isset($chart_data['quiz_query']) ? stripslashes($chart_data['quiz_query']) : '';

            // Quiz id
            $quiz_id = isset($chart_data['quiz_id']) ? intval($chart_data['quiz_id']) : 0;

            // Change the author of the current chart
            $change_create_author = (isset($chart['author_id']) && $chart['author_id'] != '') ? absint( sanitize_text_field( $chart['author_id'] ) ) : $user_id;

            if ( $change_create_author  && $change_create_author > 0 ) {
                global $wpdb;
                $users_table = esc_sql( $wpdb->prefix . 'users' );
                $sql_users = "SELECT ID, display_name FROM {$users_table} WHERE ID = {$change_create_author}";

                $create_author_data = $wpdb->get_row($sql_users, "ARRAY_A");

                if (!isset($create_author_data)) {
                    $create_author_data = array(
                        "ID" => 0,
                        "display_name" => __('Deleted user', 'chart-builder'),
                    );
                }
            } else {
                $change_create_author = $user_id;
                $create_author_data = array(
                    "ID" => $user_id,
                    "display_name" => $user->data->display_name,
                );
            }

        /**
         * Data that need to get form @settings variable
         */
            if ($chart_source_type === "chart-js") {
                $settings = CBFunctions()->get_chart_settings_chartjs_admin($chart_data['settings']);
            } else {
                $settings = CBFunctions()->get_chart_settings_google_admin($chart_data['settings'], $action, $source);
            }

            /**
         * Data that need to get form @options variable
         */
            $options = $object['options'];

    // Send data to JS
    $source_data_for_js = array(
        'source' => $source,
        'source_ordering' => $ordering,
        'quiz_query_tooltips' => $quiz_query_tooltips,
        'action' => $action,
        'settings' => $settings,
        'chartType' => $source_chart_type,
        'chartSourceType' => $chart_source_type,
        'chartTypesNames' => $chart_types_names,
        'chartTypesConnections' => $similar_charts,
        'imagesUrl' => CHART_BUILDER_ADMIN_URL.'/images',
        'addManualDataRow' => CHART_BUILDER_ADMIN_URL . '/images/icons/add-circle-outline.svg',
        // 'removeManualDataRow' => CHART_BUILDER_ADMIN_URL . '/images/icons/xmark.svg',
    );
    wp_localize_script( $this->plugin_name, "ChartBuilderSourceData" , $source_data_for_js );