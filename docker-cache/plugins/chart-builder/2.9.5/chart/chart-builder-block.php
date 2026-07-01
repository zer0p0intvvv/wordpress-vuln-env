<?php
    /**
     * Enqueue front end and editor JavaScript
     */

    function ays_chart_gutenberg_scripts() {
        global $current_screen;
        global $wp_version;
        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.3.12';
        $versionCompare = aysChartBuilderVersionCompare($version1, $operator, $version2);
        if( ! $current_screen ){
            return null;
        }

        if( ! $current_screen->is_block_editor ){
            return null;
        }

        // wp_enqueue_script( CHART_BUILDER_NAME . "-plugin", CHART_BUILDER_PUBLIC_URL . '/js/chart-builder-public-plugin.js', array('jquery'), CHART_BUILDER_VERSION, true);

        // Enqueue the bundled block JS file
        if($versionCompare){
            wp_enqueue_script(
                'chart-builder-block-js',
                CHART_BUILDER_BASE_URL ."/chart/chart-builder-block-new.js",
                array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
                CHART_BUILDER_VERSION, true
            );
        }
        else{
            wp_enqueue_script(
                'chart-builder-block-js',
                CHART_BUILDER_BASE_URL ."/chart/chart-builder-block.js",
                array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
                CHART_BUILDER_VERSION, true
            );
        }
        
        wp_enqueue_style( CHART_BUILDER_NAME, CHART_BUILDER_PUBLIC_URL . '/css/chart-builder-public.css', array(), CHART_BUILDER_VERSION, 'all');
        
        // Enqueue the bundled block CSS file
        if($versionCompare){            
            wp_enqueue_style(
                'chart-builder-block-css',
                CHART_BUILDER_BASE_URL ."/chart/chart-builder-block-new.css",
                array(),
                CHART_BUILDER_VERSION, 'all'
            );
        }
        else{            
            wp_enqueue_style(
                'chart-builder-block-css',
                CHART_BUILDER_BASE_URL ."/chart/chart-builder-block.css",
                array(),
                CHART_BUILDER_VERSION, 'all'
            );
        }
    }

    function ays_chart_gutenberg_block_register() {
        
        global $wpdb;
        $block_name = 'chart';
        $block_namespace = 'chart-builder/' . $block_name;
        
        $current_user = get_current_user_id();
        $sql = "SELECT * FROM ". $wpdb->prefix . CHART_BUILDER_DB_PREFIX . "charts WHERE status = 'published'";
        if( ! current_user_can( 'manage_options' ) ){
            $sql .= " AND author_id = ". absint( $current_user ) ." ";
        }
        $results = $wpdb->get_results($sql, "ARRAY_A");
        
        register_block_type(
            $block_namespace, 
            array(
                'render_callback'   => 'chart_builder_render_callback',                
                'editor_script'     => 'chart-builder-block-js',
                'style'             => 'chart-builder-block-css',
                'category'          => 'media',
                'keywords'          => array(
                    'Chart',
                    'Visual Data',
                    'Graph',
                    'Chart Maker',
                    'Data Charts',
                    'Dynamic Charts'
                ),
                'attributes'	    => array(
                    'idner' => $results,
                    'metaFieldValue' => array(
                        'type'  => 'integer', 
                    ),
                    'shortcode' => array(
                        'type'  => 'string',				
                    ),
                    'className' => array(
                        'type'  => 'string',
                    )
                ),
            )
        );
    }    
    
    function chart_builder_render_callback( $attributes ) {
        global $current_screen;
        $is_front = true;

        if( ! empty( $current_screen ) ){
            if( isset( $current_screen->is_block_editor ) && $current_screen->is_block_editor === true ){
                $is_front = false;
            }
        }elseif ( wp_is_json_request() ) {
            $is_front = false;
        }

        $ays_html = "<div></div>";

        if( ! empty( $attributes["shortcode"] ) ) {
            $ays_html = $attributes["shortcode"];
        }else{
            if( $is_front === true ){
                $ays_html = '';
            }
        }

        return $ays_html;
    }

    if(function_exists("register_block_type")){
            // Hook scripts function into block editor hook
        add_action( 'enqueue_block_editor_assets', 'ays_chart_gutenberg_scripts' );
        add_action( 'init', 'ays_chart_gutenberg_block_register' );
    }

    function aysChartBuilderVersionCompare($version1, $operator, $version2) {
    
        $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
        $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );
    
        if (strlen ( $_fv ) > strlen ( $_sv )) {
            $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
        }
    
        if (strlen ( $_fv ) < strlen ( $_sv )) {
            $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
        }
    
        return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
    }
