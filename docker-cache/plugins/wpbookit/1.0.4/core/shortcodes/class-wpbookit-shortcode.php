<?php

class WPB_Init_ShortCode
{
    public $shortcode_classes = [];
    public function __construct()
    {
        $this->shortcode_classes = apply_filters('wpb_shortcode_classes',[
            'WPB_Shortcode_Profile',
            'WPB_Shortcode_Login',
            'WPB_Shortcode_Booking'
        ]);

        $this->include();
        $this->load_shotcodes();
    }
    
    public function include() {
        require_once IQWPB_PLUGIN_PATH . 'core/shortcodes/class-wpbookit-shortcode-abstract.php';
        $shortcode_classes_path = apply_filters('wpb_load_all_shortcode', [
            IQWPB_PLUGIN_PATH . 'core/shortcodes/classes/class-wpbookit-booking-shortcode.php',
            IQWPB_PLUGIN_PATH . 'core/shortcodes/bookings/class-wpbookit-profile-shortcode.php',
            IQWPB_PLUGIN_PATH . 'core/shortcodes/bookings/class-wpbookit-login-shortcode.php',
        ]);

        foreach ($shortcode_classes_path as $file) {
            require_once $file;
        }
    }

    public function load_shotcodes()
    {
        foreach ($this->shortcode_classes as  $class) {
            if(class_exists($class)){
                new $class;
            } 
        }
    }
}

new WPB_Init_ShortCode();
