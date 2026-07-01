<?php

use function Kucrut\Vite\enqueue_asset;

abstract class WPB_Shortcode
{
    public $shortcode='';
    public $attr=[];
    public $load_assets='';
    public $extra_fields=[];
    public function __construct()
    {
        add_shortcode($this->shortcode, [$this, 'wpb_shortcode_callback']);
    }
    public function wpb_shortcode_callback($atts){
        $this->attr = shortcode_atts($this->attr, $atts, $this->shortcode);
        $this->wpb_shortcode_init($atts);

        if(file_exists(IQWPB_PLUGIN_PATH.$this->load_assets)){
            $wpb_custom_code= get_option( 'wpb_custom_code_data', [  'css_code' => '',  'js_code' => '' ]);

            wp_register_style( 'wpb-custom-code-css', '' );
            wp_add_inline_style( 'wpb-custom-code-css', stripslashes($wpb_custom_code['css_code']));

            wp_register_script( 'wpb-custom-code-js', '', array("jquery"),IQWPB_VERSION );
            wp_register_script('wpb-jquery-validate', IQWPB_PLUGIN_URL . 'core/admin/assets/vendor/js/jquery.validate.min.js', ['jquery'], IQWPB_VERSION);
            wp_add_inline_script( 'wpb-custom-code-js',stripslashes($wpb_custom_code['js_code']) );
          

            wp_set_script_translations(  $this->shortcode.'-shortcode', 'wpbookit' );
            Kucrut\Vite\enqueue_asset(
                IQWPB_PLUGIN_PATH . 'core/dist',
                $this->load_assets,
                [
                    'handle' => $this->shortcode.'-shortcode',
                    'dependencies' => ['jquery', 'wp-i18n','wpb-custom-code-js','wpb-jquery-validate'],
                    'css-dependencies' => ['wpb-custom-code-css'],
                    'css-media' => 'all',
                    'css-only' => false,
                    'in-footer' => true,
                ]
            );
            wp_localize_script($this->shortcode.'-shortcode','wpbookit',[
                'confirm_delete_msg'=>esc_html__("Do you want to cancel this appointment?",'wpbookit'),
                'payment_failed'=>esc_html__("Payment failed. Please try again.",'wpbookit'),
                'valid_email'=>esc_html__("Please enter a valid email format.",'wpbookit'),
                'no_slots_available'=>esc_html__("* No slots available for the selected date.",'wpbookit'),

                'first_name_blank'   => esc_html__("First name cannot be blank", 'wpbookit'),
                'last_name_blank'    => esc_html__("Last name cannot be blank", 'wpbookit'),
                'email_blank'        => esc_html__("Email address cannot be blank", 'wpbookit'),
                'password_blank'     => esc_html__("Change Password cannot be blank", 'wpbookit'),
                'confirm_password_blank' => esc_html__("Confirm Password cannot be blank", 'wpbookit'),
                'confirm_change_password_not_match' => esc_html__("Confirm and Change Passwords don't match", 'wpbookit'),
                
                'cancel_booking_confirmation' => esc_html__("Do you want to cancel this appointment?", 'wpbookit'),
                'first_name_validation'       => esc_html__("First name can only contain alphabetic characters", 'wpbookit'),
                'email_validation'            => esc_html__("Please enter a valid email address", 'wpbookit'),
                'phone_number_validation'     => esc_html__("Phone number must be 10 digits", 'wpbookit'),
                'gender_validation'           => esc_html__("Please select a valid gender", 'wpbookit'),
                'password_strength'           => esc_html__("Required uppercase and lower letter, number, and special character.", 'wpbookit'),


                'flatpicker' => array(
                        'weekdays' => array(
                            'shorthand' => array(
                                esc_html__('Sun', 'wpbookit'),
                                esc_html__('Mon', 'wpbookit'),
                                esc_html__('Tue', 'wpbookit'),
                                esc_html__('Wed', 'wpbookit'),
                                esc_html__('Thu', 'wpbookit'),
                                esc_html__('Fri', 'wpbookit'),
                                esc_html__('Sat', 'wpbookit')
                            ),
                            'longhand' => array(
                                esc_html__('Sunday', 'wpbookit'),
                                esc_html__('Monday', 'wpbookit'),
                                esc_html__('Tuesday', 'wpbookit'),
                                esc_html__('Wednesday', 'wpbookit'),
                                esc_html__('Thursday', 'wpbookit'),
                                esc_html__('Friday', 'wpbookit'),
                                esc_html__('Saturday', 'wpbookit')
                            )
                        ),
                        'months' => array(
                            'shorthand' => array(
                                esc_html__('Jan', 'wpbookit'),
                                esc_html__('Feb', 'wpbookit'),
                                esc_html__('Mar', 'wpbookit'),
                                esc_html__('Apr', 'wpbookit'),
                                esc_html__('May', 'wpbookit'),
                                esc_html__('Jun', 'wpbookit'),
                                esc_html__('Jul', 'wpbookit'),
                                esc_html__('Aug', 'wpbookit'),
                                esc_html__('Sep', 'wpbookit'),
                                esc_html__('Oct', 'wpbookit'),
                                esc_html__('Nov', 'wpbookit'),
                                esc_html__('Dec', 'wpbookit')
                            ),
                            'longhand' => array(
                                esc_html__('January', 'wpbookit'),
                                esc_html__('February', 'wpbookit'),
                                esc_html__('March', 'wpbookit'),
                                esc_html__('April', 'wpbookit'),
                                esc_html__('May', 'wpbookit'),
                                esc_html__('June', 'wpbookit'),
                                esc_html__('July', 'wpbookit'),
                                esc_html__('August', 'wpbookit'),
                                esc_html__('September', 'wpbookit'),
                                esc_html__('October', 'wpbookit'),
                                esc_html__('November', 'wpbookit'),
                                esc_html__('December', 'wpbookit')
                            )
                        ),
                        'firstDayOfWeek' =>  get_option('start_of_week',1),
                        'weekAbbreviation' => esc_html__('Wk', 'wpbookit'),
                        'scrollTitle' => esc_html__('Scroll to increment', 'wpbookit'),
                        'toggleTitle' => esc_html__('Click to toggle', 'wpbookit'),
                        'amPM' => array(
                            esc_html__('AM', 'wpbookit'),
                            esc_html__('PM', 'wpbookit')
                        ),
                        'yearAriaLabel' => esc_html__('Year', 'wpbookit'),
                        'monthAriaLabel' => esc_html__('Month', 'wpbookit'),
                        'hourAriaLabel' => esc_html__('Hour', 'wpbookit'),
                        'minuteAriaLabel' => esc_html__('Minute', 'wpbookit'),
                    ),
                'extra_fields'=> $this->extra_fields,
            ]);

            wp_add_inline_script($this->shortcode.'-shortcode','var ajaxurl="'.esc_url(admin_url('admin-ajax.php')).'",wpb_nounce = "'. wp_create_nonce("wpb_ajax_nonce").'";' );
            // Enqueue Google Fonts
            add_action( 'admin_footer', function(){
                if(is_rtl()){
                    wp_enqueue_style('wpb-rtl', IQWPB_PLUGIN_URL . 'core/admin/assets/src/css/rtl.css', array(), IQWPB_VERSION);
                }
            } );
          
            wp_enqueue_style( 'wpbookit-font-family', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap', array(), IQWPB_VERSION );
        }

        return $this->wpb_shortcode_render();
    }
    public function wpb_shortcode_render()  {
        
    }
    public function wpb_shortcode_init($atts)  {
      
    }
}