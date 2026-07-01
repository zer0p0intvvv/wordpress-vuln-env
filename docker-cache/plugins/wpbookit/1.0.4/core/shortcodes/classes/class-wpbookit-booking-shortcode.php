<?php

final class WPB_Shortcode_Booking extends WPB_Shortcode
{
    public $shortcode = 'wpb-booking';
    public $attr = ['id' => ''];
    public $load_assets = 'core/shortcodes/assets/js/booking.js';


    public $booking_type_id;
    public WPB_Booking_Type $booking_type;
    public $booking_timezome;
    public $booking_options;
    public $require_guest_email_address;
    public $require_guest_phone_number;

    public WPB_Booking $booking_instance;

    public  $booked_date_timestamp;
    public $booking_location_source;
    public $booking_location;
    public $staff_name;
    public $booking_type_name;
    public $booked_date_timestamp_cal;
    public $booked_start_time_cal;
    public $booked_end_time_cal;
    public $booking_type_description;
    public $show_cancel_button;


    public $wpb_general_setting_data_booking_type;
    public $booking_totals_and_html;

    private $booking_tabs;
    public $is_group_booking;
    public $total_seat;
    public $payment;


    public function __construct()
    {
        parent::__construct();
    }
    public function wpb_shortcode_init($attr)
    { 
        $this->booking_type_id = '';

        if (isset($this->attr['id']) && !empty($this->attr['id'])) {
            $this->booking_type_id = $this->attr['id'];
        } elseif (isset($_GET['booking_type_id'])) {
            $this->booking_type_id = sanitize_text_field($_GET['booking_type_id']);
        }
        $this->booking_type = new WPB_Booking_Type($this->booking_type_id);

        // Load Tab
        $tabs =apply_filters('wpb_booking_shortcode_tabs',[
            [
                'tab'=>'wpb_booking_shortcode_detail_tab',
                'position' => 10  
            ],
            [
                'tab'=>'wpb_booking_shortcode_payment_tab',
                'position' => 20,
                'condition' => $this->booking_type->get_meta('price') > 0
            ],
        ],$this);

        $tabs = array_filter($tabs, function ($tab) {
            return !isset($tab['condition']) || $tab['condition'];
        });

        uasort($tabs, function ($a, $b) {
			return ($a['position']??0) - ($b['position']??0);
		});

        $this->booking_tabs = $tabs;
        $general_setting = get_option('wpb_general_setting_data');
        $require_guest_email = isset($general_setting['require_guest_email_address']) ? $general_setting['require_guest_email_address'] : false;
        $require_guest_phone = isset($general_setting['require_guest_phone_number']) ? $general_setting['require_guest_phone_number'] : false;

        $validation_rules = [
            'wpb_user_name'=>[
                "rules"=>[
                    'required'=> true,
                ],
                "messages" => [
                    'required'=> esc_html__("Please enter Full Name", 'wpbookit')
                ]
            ],
            'wpb_user_first_name'=>[
                "rules"=>[
                    'required'=> true,
                ],
                "messages" => [
                    'required'=> esc_html__("Please enter first name", 'wpbookit')
                ]
            ],
            'wpb_user_last_name'=>[
                "rules"=>[
                    'required'=> true,
                ],
                "messages" => [
                    'required'=> esc_html__("Please enter last name", 'wpbookit')
                ]
            ],
            'wpb_user_email'=>[
                "rules"=>[
                    'required'=> isset($general_setting['require_guest_email_address']) ? true : false ,
                    'customEmail'=> true,
                ],
                "messages" => [
                    'required'=> esc_html__("Please enter email", 'wpbookit'),
                    'customEmail'=> esc_html__("Please enter valid email", 'wpbookit'),
                ]
            ],
            'wpb_user_password'=>[
                "rules"=>[
                    'required'=> true,
                    "minlength"=> 6,
                ],
                "messages" => [
                    'required'=> esc_html__("Please enter Password", 'wpbookit'),
                    ]
            ],
            'wpb_login_user_email'=>[
                "rules"=>[
                    'required'=> true,
                    'customEmail'=> true,
                ],
                "messages" => [
                    'required'=> esc_html__("Please enter email", 'wpbookit'),
                    'customEmail'=> esc_html__("Please enter valid email", 'wpbookit'),
                ]
            ],
            'wpb_payments_gateways'=>[
                "rules"=>[
                    'required'=> true,
                ],
                "messages" => [
                    'required'=> esc_html__("Please Select Payment Gateway", 'wpbookit'),
                ]
            ],
            'wpb_user_phone_number'=>[
                "rules"=>[
                    'required'=> isset($general_setting['require_guest_phone_number']) ? true : false ,
                ],
                "messages" => [
                    'required'=> esc_html__("Please Enter Phone Number", 'wpbookit'),
                ]
            ],
        ];
        
        $this->extra_fields = apply_filters('wpb_booking_shortcode_extra_fields',['tabs'=> array_values($this->booking_tabs),'validation_rules'=>$validation_rules],$this);
    }


    public function wpb_shortcode_render($atts = array())
    {
        // Set WordPress default timezone
        $timezone = wpb_get_timezone();
        $timezone = ! empty( $timezone ) ? $timezone : date_default_timezone_get();
         date_default_timezone_set( $timezone ); //phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set

        // Get current time in the desired format
        $current_time = new DateTime();
        $current_time_str =  $current_time->format('T') . ' - ' . $current_time->format('e');

        $this->booking_options = wpb_get_general_settings()['booking_options'] ?? "name-only";
        $this->wpb_general_setting_data_booking_type = wpb_get_general_settings()['booking_type'] ?? "registered";

        // Output the formatted text
        $this->booking_timezome = sprintf("(%s) %s", $current_time->format('P'), $current_time_str );

        $this->require_guest_email_address = wpb_get_general_settings()['require_guest_email_address'] ?? "false";
        $this->require_guest_phone_number = wpb_get_general_settings()['require_guest_phone_number'] ?? "false";
        ob_start();
        if (isset($_REQUEST['booking_confirmation']) ) {
           
            $this->booking_instance = new WPB_Booking($_REQUEST['booking_confirmation']);
            
            if($this->booking_type->get_meta('guest_invite') != 'true' &&  ((int)$this->booking_instance->get_customer_id()) !== get_current_user_id() || $this->booking_instance->is_exist == false ){
                return esc_html__("Booking not found", 'wpbookit');
            }

            
            $max_post_booking_days = $this->booking_type->get_meta('how_far')??-1;
            $this->booking_location_source = $this->booking_instance->get_meta('location_source');
            $this->booking_location = $this->booking_instance->get_meta('location');
            $this->staff_name =  $this->booking_type->get_meta('staff') ? get_userdata( $this->booking_type->get_meta('staff'))->display_name : '';
            $this->booking_type_name = wpb_get_booking_type((int)$this->booking_type_id,['name']);
            $this->booking_type_description = wpb_get_booking_type((int)$this->booking_type_id,['description']);
            $booked_timestamp= strtotime($this->booking_instance->get_booking_date().' '.$this->booking_instance->get_timeslot());
            $date_format = get_option('date_format');
            $full_date_format = $date_format . ' \a\t h:i a';
            $this->booked_date_timestamp = wp_date(apply_filters('wpb_booking_booked_date_format', $full_date_format), $booked_timestamp,new DateTimeZone( $timezone));
            $this->booked_date_timestamp_cal = date(apply_filters('wpb_booking_booked_date_format', $date_format), $booked_timestamp);  // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
            $this->booked_start_time_cal = date(apply_filters('wpb_booking_booked_date_format','H:i'), $booked_timestamp); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
            $end_timestamp = strtotime('+' . $this->booking_type->get_duration() . ' minutes', $booked_timestamp);
            $this->booked_end_time_cal = date(apply_filters('wpb_booking_booked_date_format', 'H:i'), $end_timestamp); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 

            $this->show_cancel_button = $this->booking_instance->get_minimum_time_before_cancellation();

            wpb_get_template(
                'shortcodes/booking/html-shortcode-booking-confirmation.php',
                ['shortcode_instance' => $this]
                );
            return ob_get_clean();
        }
        $this->is_group_booking = $this->booking_type->get_meta('enable_group_booking') ?? false;
        $this->total_seat = ($this->booking_type->get_meta('slots_per_booking_number') ?? 1);

        if ($this->booking_type->is_exist == false) {
            return esc_html__("Booking type not found", 'wpbookit');
        }
        $max_post_booking_days = $this->booking_type->get_meta('how_far');

        if(empty($max_post_booking_days) || $max_post_booking_days=='' || $max_post_booking_days==0){
            $max_post_booking_days= "365";
        }
        $this->payment = wpb_get_active_payment_gateways();
        $this->booking_totals_and_html = [];
      
        include IQWPB_PLUGIN_PATH . '/core/shortcodes/views/html-shortcode-booking.php';
        return  ob_get_clean();
    }
    public function get_tabs() {
        if($this->booking_type->get_meta( 'price' ) == 0 ){
            $this->booking_tabs= array_filter($this->booking_tabs,function($tab){
                return $tab['tab']!='wpb_booking_shortcode_payment_tab';
            });
        }
        return $this->booking_tabs ;
    }

}