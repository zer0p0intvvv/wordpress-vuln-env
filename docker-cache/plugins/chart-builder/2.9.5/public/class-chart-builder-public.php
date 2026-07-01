<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/public
 * @author     Chart Builder Team <info@ays-pro.com>
 */
class Chart_Builder_Public {

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
	 * @var string
	 */
	private $html_class_prefix = 'ays-chart-';

	/**
	 * @var string
	 */
	private $html_name_prefix = 'ays_chart_';

	/**
	 * @var string
	 */
	private $name_prefix = 'chart_';

	/**
	 * @var
	 */
	private $unique_id;

	/**
	 * @var Chart_Builder_DB_Actions
	 */
	private $db_object;

	/**
	 * @var array
	 */
	private $data;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->db_object =  new Chart_Builder_DB_Actions( $this->plugin_name );

		add_shortcode( 'ays_chart', array( $this, 'ays_generate_chart_method' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chart_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chart_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chart-builder-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($type) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chart_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chart_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$is_elementor_exists = Chart_Builder_DB_Actions::ays_chart_is_elementor();
        if( !$is_elementor_exists ) {
			if ($type === 'chart-js') {
				wp_enqueue_script( $this->plugin_name . '-chart-js', plugin_dir_url(__FILE__) . 'js/chart-js.js', array('jquery'), $this->version, true);
				wp_enqueue_script( $this->plugin_name . '-plugin-chartjs', plugin_dir_url( __FILE__ ) . 'js/chart-builder-public-chartjs.js', array( 'jquery' ), $this->version, false );
			} else {
				wp_enqueue_script( $this->plugin_name . '-charts-google', plugin_dir_url(__FILE__) . 'js/google-chart.js', array('jquery'), $this->version, true);
				wp_enqueue_script( $this->plugin_name . '-plugin-google', plugin_dir_url( __FILE__ ) . 'js/chart-builder-public-google.js', array( 'jquery' ), $this->version, false );
			}
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chart-builder-public.js', array( 'jquery' ), $this->version, false );
		}

		$this->get_encoded_options($type);
	}

	public function ays_generate_chart_method( $attr ) {
		$id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;

		if (is_null($id)) {
			return "<p class='wrong_shortcode_text' style='color:red;'>" . __( 'Wrong shortcode initialized', "chart-builder" ) . "</p>";
		}

		$is_elementor = Chart_Builder_DB_Actions::ays_chart_is_elementor();
		if ($is_elementor) {
			 return "<div style='width:100%;padding:6px 8px;font-size:13px;border:1px solid #757575;border-radius:2px;background-color:#f0f0f1;color:#2c3338;'>
			 			[ays_chart id=".$id."]
					</div>
					<p style='margin:4px 0 0 3px;font-size:12px;font-style:italic;'>
						" . __( 'Note: The chart will be visible on the front end of your website.', "chart-builder" ) . "
					</p>"; 
        }		

		$chartData = CBActions()->get_chart_data( $id );

		if ( is_null( $chartData ) ) {
			return "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', "chart-builder") . "</p>";
		}

		$this->enqueue_styles();
		$content = $this->show_chart( $id, $chartData, $attr );
		$this->enqueue_scripts($chartData['chart']['type']);

		return str_replace( array( "\r\n", "\n", "\r" ), '', $content );
	}

	public function show_chart( $id, $chartData, $attr ) {
		$chart = $chartData['chart'];
		$settings = $chartData['settings'];

		$unique_id = uniqid();
		$this->unique_id = $unique_id;

		$data = array();

		if ( is_null( $chart ) ) {
			return "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', "chart-builder") . "</p>";
		}

		$source_type = $chartData['source_type'];
		$chart_source_type = $chartData['chart']['type'];
		$user_id = get_current_user_id();

		if ($source_type == 'quiz_maker' && $user_id == 0 && $chart['quiz_query'] != 'q1') {
			return "<p class='ays_chart_not_logged_text'>" . __('You are not logged in. Please log in to view this chart.', $this->plugin_name) . "</p>";
		}

		$status = isset( $chart['status'] ) && $chart['status'] != '' ? $chart['status'] : '';

		if ( $status != 'published' ) {
			return "";
		}
		
		if ($chart_source_type === "chart-js") {
			$settings = CBFunctions()->get_chart_settings_chartjs_public($settings);
		} else {
			$settings = CBFunctions()->get_chart_settings_google_public($settings, $chartData['source']);
		}

		$data['chart_type'] = $chartData['source_chart_type'];
		$data['source'] = $chartData['source'];


		$data['options'] = $settings;

		if ($chart_source_type === "chart-js") {
			$content = $this->chartJsContent($chart, $settings, $chartData, $id);
		} else {
			$content = $this->googleChartsContent($chart, $settings, $chartData, $id);
		}

		$this->data = $data;

		return implode( '', $content );
	}

	public function get_encoded_options ($chart_source_type) {
		$data = $this->data;
		$encoded_data = base64_encode(json_encode($data));
		$handle = $chart_source_type === 'chart-js' ? $this->plugin_name . '-chart-js' : $this->plugin_name . '-charts-google';

		wp_localize_script($handle, 'aysChartOptions'.$this->unique_id, array('aysChartOptions' => $encoded_data));
	}

	public function googleChartsContent ($chart, $settings, $chartData, $id) {
		$chart_title = (isset($chart['title']) && $chart['title'] != '') ? stripslashes ( sanitize_text_field( $chart['title'] ) ) : '';
		$chart_description = (isset($chart['description']) && $chart['description'] != '') ? stripslashes ( sanitize_text_field( $chart['description'] ) ) : '';

		$content = array();

		$content[] = "<div class='" . $this->html_class_prefix . "container-google " . $this->html_class_prefix . "container-" . $id . "' id='" . $this->html_class_prefix . "container" . $this->unique_id . "' data-id='" . $this->unique_id . "'>";

			$content[] = "<div class='" . $this->html_class_prefix . "header-container'>";

			if ($settings['show_title'] == 'on') {
				$content[] = "<div class='" . $this->html_class_prefix . "charts-title " . $this->html_class_prefix . "charts-title" . $this->unique_id . "'>";
				$content[] = $chart_title;
				$content[] = "</div>";
			}
			
			if ($settings['show_description'] == 'on') {
				$content[] = "<div class='" . $this->html_class_prefix . "charts-description " . $this->html_class_prefix . "charts-description" . $this->unique_id . "'>";
				$content[] = $chart_description;
				$content[] = "</div>";
			}
			
		$content[] = "</div>";

		$content[] = "<div class='" . $this->html_class_prefix . "charts-main-container " . $this->html_class_prefix . "charts-main-container" . $this->unique_id . "' id=" . $this->html_class_prefix . $chartData['source_chart_type'] . $this->unique_id . " data-type='". $chartData['source_chart_type'] ."'></div>";

			$content[] = "<div class='" . $this->html_class_prefix . "actions-container'>";

				$content[] = "<div class='" . $this->html_class_prefix . "export-buttons' data-id='". $id . "'>";
					if (isset($settings['enable_img']) && $settings['enable_img'] == 'on'){
						$content[] = "<button class='" . $this->html_class_prefix . "export-button-" . $this->unique_id . "' title='Download as a PNG' data-type='image' value='image'>Image</button>";

						$content[] = "<div style='display:none'>";
								$content[] = "<iframe src='' style='width:100%;height:600px;'></iframe>";
						$content[] = "</div>";
					}
				$content[] = "</div>";

			$content[] = "</div>";
			
		$content[] = "</div>";

		$custom_css = "
			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "charts-main-container" . $this->unique_id . " {
				width: " . $settings['chart_width'] . ";
				height: " . $settings['chart_height'] . ";
				" . $settings['position'] . ";
				border-radius: " . $settings['border_radius'] . "px;
				overflow: hidden;
			}
			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "charts-main-container" . $this->unique_id . "[data-type='org_chart'] {
				overflow: auto;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "charts-main-container" . $this->unique_id . "[data-type='org_chart'] table.google-visualization-orgchart-table tbody td  {
				padding: initial;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "header-container {
				margin-bottom: " . $settings['title_gap'] . "px !important;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "header-container>." . $this->html_class_prefix . "charts-title" . $this->unique_id . " {
				color: " . $settings['title_color'] . ";
				font-size: " . $settings['title_font_size'] . "px;
				font-weight: " . $settings['title_bold'] . ";
				text-shadow: " . ($settings['title_text_shadow'] === 'checked' ? '2px 2px 5px '.$settings['title_shadow_color'] : '') . ";
				font-style: " . $settings['title_italic'] . ";
				text-align: " . $settings['title_position'] . ";
				text-transform: " . $settings['title_text_transform'] . ";
				text-decoration: " . $settings['title_text_decoration'] . ";
				letter-spacing: " . $settings['title_letter_spacing'] . "px;
				margin-bottom: " . $settings['title_gap_description'] . "px;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "header-container>." . $this->html_class_prefix . "charts-description" . $this->unique_id . " {
				color: " . $settings['description_color'] . ";
				font-size: " . $settings['description_font_size'] . "px; 
				font-weight: " . $settings['description_bold'] . ";
				text-shadow: " . ($settings['description_text_shadow'] === 'checked' ? '2px 2px 5px '.$settings['description_shadow_color'] : '') . ";
				font-style: " . $settings['description_italic'] . ";
				text-align: " . $settings['description_position'] . ";
				text-transform: " . $settings['description_text_transform'] . ";
				text-decoration: " . $settings['description_text_decoration'] . ";
				letter-spacing: " . $settings['description_letter_spacing'] . "px;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "actions-container>div." . $this->html_class_prefix . "export-buttons .ays-chart-export-button-" . $this->unique_id . " {
				color: " . $settings['description_color'] . "B3 !important;
				font-size: " . $settings['description_font_size'] . "px !important; 
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "actions-container>div." . $this->html_class_prefix . "export-buttons .ays-chart-export-button-" . $this->unique_id . ":hover {
				color: " . $settings['description_color'] . " !important;
			}
		";
        wp_add_inline_style($this->plugin_name, $custom_css);

		return $content;
	}

	public function chartJsContent($chart, $settings, $chartData, $id) {
		$chart_title = (isset($chart['title']) && $chart['title'] != '') ? stripslashes ( sanitize_text_field( $chart['title'] ) ) : '';
		$chart_description = (isset($chart['description']) && $chart['description'] != '') ? stripslashes ( sanitize_text_field( $chart['description'] ) ) : '';

		$content = array();

		$content[] = "<div class='" . $this->html_class_prefix . "container-chartjs " . $this->html_class_prefix . "container-" . $id . "' id='" . $this->html_class_prefix . "container" . $this->unique_id . "' data-id='" . $this->unique_id . "'>";

			$content[] = "<div class='" . $this->html_class_prefix . "header-container'>";

				if ($settings['show_title'] == 'on') {
					$content[] = "<div class='" . $this->html_class_prefix . "charts-title " . $this->html_class_prefix . "charts-title" . $this->unique_id . "'>";
					$content[] = $chart_title;
					$content[] = "</div>";
				}
				
				if ($settings['show_description'] == 'on') {
					$content[] = "<div class='" . $this->html_class_prefix . "charts-description " . $this->html_class_prefix . "charts-description" . $this->unique_id . "'>";
					$content[] = $chart_description;
					$content[] = "</div>";
				}

			$content[] = "</div>";

			$content[] = "<div class='" . $this->html_class_prefix . "charts-main-container " . $this->html_class_prefix . "charts-main-container" . $this->unique_id . "' id=" . $this->html_class_prefix . $chartData['source_chart_type'] . "-" . $this->unique_id . " data-type='". $chartData['source_chart_type'] ."'>";
				$content[] = "<canvas id='" . $this->html_class_prefix . $chartData['source_chart_type'] . "-" . $this->unique_id . "-canvas'>";
			$content[] = "</div>";

		$content[] = "</div>";

		$custom_css = "
			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "header-container {
				margin-bottom: " . $settings['title_gap'] . "px !important;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "header-container>." . $this->html_class_prefix . "charts-title" . $this->unique_id . " {
				color: " . $settings['title_color'] . ";
				font-size: " . $settings['title_font_size'] . "px;
				font-weight: " . $settings['title_bold'] . ";
				text-shadow: " . ($settings['title_text_shadow'] === 'checked' ? '2px 2px 5px '.$settings['title_shadow_color'] : '') . ";
				font-style: " . $settings['title_italic'] . ";
				text-align: " . $settings['title_position'] . ";
				text-transform: " . $settings['title_text_transform'] . ";
				text-decoration: " . $settings['title_text_decoration'] . ";
				letter-spacing: " . $settings['title_letter_spacing'] . "px;
				margin-bottom: " . $settings['title_gap_description'] . "px;
			}

			#" . $this->html_class_prefix . "container" . $this->unique_id . " div." . $this->html_class_prefix . "header-container>." . $this->html_class_prefix . "charts-description" . $this->unique_id . " {
				color: " . $settings['description_color'] . ";
				font-size: " . $settings['description_font_size'] . "px; 
				font-weight: " . $settings['description_bold'] . ";
				text-shadow: " . ($settings['description_text_shadow'] === 'checked' ? '2px 2px 5px '.$settings['description_shadow_color'] : '') . ";
				font-style: " . $settings['description_italic'] . ";
				text-align: " . $settings['description_position'] . ";
				text-transform: " . $settings['description_text_transform'] . ";
				text-decoration: " . $settings['description_text_decoration'] . ";
				letter-spacing: " . $settings['description_letter_spacing'] . "px;
			}
		";
        wp_add_inline_style($this->plugin_name, $custom_css);

		return $content;
	}
}
