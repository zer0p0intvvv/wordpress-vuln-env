<?php
final class WPB_Shortcode_Login extends WPB_Shortcode
{
    public $shortcode = 'wpb-login';
    public $attr = ['test' => 'sample'];
    public $load_assets = 'core/shortcodes/assets/js/login.js';

    public function wpb_shortcode_render()
    {
        $registration_enabled = get_option('users_can_register');
        $current_user = wp_get_current_user();
        $already_logged_in = $current_user ? true : false;

        ob_start();
        require_once IQWPB_PLUGIN_PATH . '/core/shortcodes/views/html-shortcode-login.php';
        return ob_get_clean();
    }

}