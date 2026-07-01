<?php 

class Booking_Type_Handler {

    public function __construct() {
        add_filter( 'rewrite_rules_array', array( $this, 'add_booking_type_rewrite_rule'));
        add_filter( 'query_vars', array($this, 'register_query_vars'));
        add_action( 'template_redirect', array($this, 'template_redirect'), 10 );
        add_action( 'init', array( $this, 'add_permalink_tag' ) );

        add_action( 'wpb_enqueue_script',[$this,'wpb_enqueue_script']);
        
        register_activation_hook(__FILE__, 'flush_rewrite_rules');
        register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
    }

    public function add_booking_type_rewrite_rule($rules) {
        global $wp_rewrite;
    
        // Get general settings
        $general_setting = wpb_get_general_settings();
        $base_url = isset($general_setting['permalink_strcture']) ? $general_setting['permalink_strcture'] : 'booking';
    
        // Add new rewrite rules
        $new_rules = array(
            $base_url . '/([^/]+)/?$' => 'index.php?booking_type_slug=$matches[1]',
        );
        $wp_generated_rewrite_rules         = $wp_rewrite->generate_rewrite_rules( $base_url . '/([^/]+)/?$', EP_PAGES, true, true, false, false );

        // Merge new rules with existing rules
        $rules = array_merge($new_rules, $rules);
        return $rules;
    }

    public function add_permalink_tag() {
        add_rewrite_tag('%booking_type_slug%', '([^&]+)');
    }

    public function register_query_vars($vars) {
        // Register custom query variable
        $vars[] = 'booking_type_slug';
        return $vars;
    }

    public function template_redirect() {
        global $wp;
        // Check for the presence of the query variable and handle the request
        $general_setting = wpb_get_general_settings();
        $base_url        = 'booking';
        
        $booking_type_slug = get_query_var('booking_type_slug');
        $request_uri     = wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path_segments   = explode('/', trim($request_uri, '/'));
        $endpoint        = end($path_segments);

        if (strpos($wp->request, $base_url ) !== false) {
            
            if ( !empty( $booking_type_slug ) ) {
                $booking_type      = wpb_get_booking_type($booking_type_slug, ['id']);

                if( isset($booking_type['id'])) {
                    $booking_type_id = $booking_type['id'];
                    do_action('wpb_enqueue_script',['hide_header' =>$general_setting['hide-header']??false,'hide_footer' =>$general_setting['hide-footer']??false]);
                    get_header(); ?>
                        <div class="wpb-booking-dynamic-shortcode">
                            <?php echo do_shortcode("[wpb-booking id='" . $booking_type_id . "']"); ?>
                        </div>
                    <?php
                    get_footer();
                    exit;
                } else {
                    // Redirect to booking directory if the booking type is not found
                    wp_redirect( site_url() . '/' . $base_url . '/' );
                    exit;
                }
            } 
        }
    }
    public function wpb_enqueue_script($booking_args){
       
        if($booking_args['hide_footer']=="1"  || $booking_args['hide_header'] == "1"){
            wp_register_style( 'wpb-booking', false );
            wp_enqueue_style( 'wpb-booking' );

            $header_element = apply_filters("wpb_shortcode_page_header_element","#header,header");
            $footer_element = apply_filters("wpb_shortcode_page_footer_element","#footer,footer");

            ob_start();
            if($booking_args['hide_footer']=='1'){
                echo esc_html("{$footer_element}{display:none !important}");
            }
            if($booking_args['hide_header']=='1'){
                echo esc_html("{$header_element}{display:none !important}");
                echo esc_html(".storefront-breadcrumb{display:none !important}");
            }

            echo esc_html(".col-full {
                max-width: 100% !important;
            }
            ");
            $css= ob_get_clean();
    
            wp_add_inline_style( 'wpb-booking', $css );
        }
    }
}

// Instantiate the class to register hooks
new Booking_Type_Handler();
