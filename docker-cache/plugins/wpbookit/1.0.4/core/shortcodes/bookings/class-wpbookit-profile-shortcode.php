<?php
final class WPB_Shortcode_Profile extends WPB_Shortcode
{
    public $shortcode = 'wpb-profile';
    public $attr = ['booking_page_url'=> ''];
    public $load_assets = 'core/shortcodes/assets/js/profile.js';

    public function wpb_shortcode_render()
    {
        global $post, $error;
        $booking_page =  isset($this->attr['booking_page_url']) ? $this->attr['booking_page_url'] : '';
        $wpbookit_current_user = wp_get_current_user();
        $profile_username = $wpbookit_current_user->user_login;
        $user_data = get_user_by('id', $wpbookit_current_user->ID);
        if (empty($user_data)) {
            return do_shortcode('[wpb-login]');
        }
        $username = get_user_meta($user_data->ID, 'first_name', true) ? get_user_meta($user_data->ID, 'first_name', true) . (get_user_meta($user_data->ID, 'last_name', true) ? ' ' . get_user_meta($user_data->ID, 'last_name', true) : '') : false;
        if (!$username):
            $user_info = get_userdata($user_data->ID);
            if (!empty($user_info)):
                $username = $user_info->display_name;
            else:
                return false;
            endif;
        endif;
        if (!$username):
            $user_info = get_userdata($user_data->ID);
            if (!empty($user_info)):
                $username = $user_info->user_login;
            else:
                return false;
            endif;
        endif;
        $args = array('status' => array('wpb-pending'), 'user_id' => $user_data->ID);
        $numPending = 0;
        if ( isset(wpb_get_bookings($args)->results) && !empty(wpb_get_bookings($args)->results) && is_array(wpb_get_bookings($args)->results) ) {
            $numPending  = count(wpb_get_bookings($args)->results);
        }
        ob_start();
        wpb_get_template('shortcodes/html-shortcode-profile.php',array('username'=>$username,'post'=>$post,'user_id'=>$wpbookit_current_user->ID,'count_pending_booking'=>$numPending,'booking_page' => $booking_page));
        return ob_get_clean();
    }

}