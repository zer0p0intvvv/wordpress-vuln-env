<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Widget_Chart_Builder_Elementor extends Widget_Base {
    public function get_name() {
        return 'chart-builder';
    }
    public function get_title() {
        return __( 'Chart Builder', 'chart-builder' );
    }
    public function get_icon() {
        // Icon name from the Elementor font file, as per http://dtbaker.net/web-development/creating-your-own-custom-elementor-widgets/
        return 'chart-elementor-widget-logo';
    }
	public function get_categories() {
		return array( 'general', 'wordpress' );
	}
    protected function register_controls() {
        $this->start_controls_section(
            'section_chart_builder',
            array(
                'label' => esc_html__( 'Chart Builder', 'chart-builder' ),
            )
        );
        $this->add_control(
            'important_note',
            array(
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<i class="chart-elementor-widget-logo"></i> '.esc_html__( 'Chart Builder', 'chart-builder' ),
                'content_classes' => 'chart-elementor-widget-logo-wrap',
            )
        );
        $this->add_control(
            'chart_selector',
            array(
                'label' => __( 'Select chart', 'chart-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => $this->get_default_chart(),
                'options' => $this->get_active_charts()
            )
        );

        $this->end_controls_section();
    }
    protected function render( $instance = array() ) {

        if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor' ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor_ajax' ) ) {
            echo '<style>
                .ays-chart-container .ays-chart-section:first-of-type {
                    display: block!important;
                }

                .ays-chart-container * {
                    pointer-events: none!important;
                    overflow: hidden!important;
                }

                div.elementor-widget-chart-builder>div.elementor-widget-container .chart-elementor-container {
                    width: 100%;
                    padding: 6px 8px;
                    font-size: 13px;
                    border: 1px solid #757575;
                    border-radius: 2px;
                    background-color: #f0f0f1;
                    color: #2c3338;
                }
            </style>';
        }

        $settings = $this->get_settings_for_display();
        echo ("<div class='chart-elementor-container'>
                    [ays_chart id={$settings['chart_selector']}]
                </div>");
        if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor' ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor_ajax' ) ) {
            echo ("<p style='margin:4px 0 0 3px;font-size:12px;font-style:italic;'>
                    " . __( 'Note: The chart will be visible on the front end of your website.', "chart-builder" ) . "
                </p>");
        }
    }

    public function get_active_charts(){
        global $wpdb;
        $current_user = get_current_user_id();
        $charts_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'charts';
        $sql = "SELECT id,title FROM {$charts_table} WHERE `status`='published'";
        if( ! current_user_can( 'manage_options' ) ){
            $sql .= " AND author_id = ". absint( $current_user ) ." ";
        }
        $results = $wpdb->get_results( $sql, ARRAY_A );
        $options = array();
        foreach ( $results as $result ){
            $options[$result['id']] = $result['title'];
        }
        return $options;
    }

    public function get_default_chart(){
        global $wpdb;
        $current_user = get_current_user_id();
        $charts_table = $wpdb->prefix . CHART_BUILDER_DB_PREFIX . 'charts';
        $sql = "SELECT id FROM {$charts_table} WHERE `status`='published'";
        if( ! current_user_can( 'manage_options' ) ){
            $sql .= " AND author_id = ". absint( $current_user ) ." ";
        }
        $sql .= " LIMIT 1;";
        $id = $wpdb->get_var( $sql );

        return intval($id);
    }

    protected function content_template() {}
    public function render_plain_content( $instance = array() ) {}
}
