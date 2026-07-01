<?php
/**
 * @package WP Live Chat
 */
/*
Plugin Name: wp-shoutbox-live-chat
Plugin URI: https://steveas.by
Description: Shoutbox live chat for WordPress by Steveas.
Version: 1.4.2
Author: Steveas
Author URI: https://steveas.by
License: GPLv2 or later
Text Domain: wp-shoutbox-live-chat
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Shoutbox {
    const version = '1.4.2';
    const default_db_version = '26';
    const default_badwords_list = '4r5e, 5h1t, 5hit, a55, anal, anus, ar5e, arrse, arse, ass, ass-fucker, asses, assfucker, assfukka, asshole, assholes, asswhole, a_s_s, b!tch, b00bs, b17ch, b1tch, ballbag, balls, ballsack, bastard, beastial, beastiality, bellend, bestial, bestiality, bi+ch, biatch, bitch, bitcher, bitchers, bitches, bitchin, bitching, bloody, blow job, blowjob, blowjobs, boiolas, bollock, bollok, boner, boob, boobs, booobs, boooobs, booooobs, booooooobs, breasts, buceta, bugger, bum, bunny fucker, butt, butthole, buttmuch, buttplug, c0ck, c0cksucker, carpet muncher, cawk, chink, cipa, cl1t, clit, clitoris, clits, cnut, cock, cock-sucker, cockface, cockhead, cockmunch, cockmuncher, cocks, cocksuck , cocksucked , cocksucker, cocksucking, cocksucks , cocksuka, cocksukka, cok, cokmuncher, coksucka, coon, cox, crap, cum, cummer, cumming, cums, cumshot, cunilingus, cunillingus, cunnilingus, cunt, cuntlick , cuntlicker , cuntlicking , cunts, cyalis, cyberfuc, cyberfuck , cyberfucked , cyberfucker, cyberfuckers, cyberfucking , d1ck, damn, dick, dickhead, dildo, dildos, dink, dinks, dirsa, dlck, dog-fucker, doggin, dogging, donkeyribber, doosh, duche, dyke, ejaculate, ejaculated, ejaculates , ejaculating , ejaculatings, ejaculation, ejakulate, f u c k, f u c k e r, f4nny, fag, fagging, faggitt, faggot, faggs, fagot, fagots, fags, fanny, fannyflaps, fannyfucker, fanyy, fatass, fcuk, fcuker, fcuking, feck, fecker, felching, fellate, fellatio, fingerfuck , fingerfucked , fingerfucker , fingerfuckers, fingerfucking , fingerfucks , fistfuck, fistfucked , fistfucker , fistfuckers , fistfucking , fistfuckings , fistfucks , flange, fook, fooker, fuck, fucka, fucked, fucker, fuckers, fuckhead, fuckheads, fuckin, fucking, fuckings, fuckingshitmotherfucker, fuckme , fucks, fuckwhit, fuckwit, fudge packer, fudgepacker, fuk, fuker, fukker, fukkin, fuks, fukwhit, fukwit, fux, fux0r, f_u_c_k, gangbang, gangbanged , gangbangs , gaylord, gaysex, goatse, God, god-dam, god-damned, goddamn, goddamned, hardcoresex , hell, heshe, hoar, hoare, hoer, homo, hore, horniest, horny, hotsex, jack-off , jackoff, jap, jerk-off , jism, jiz , jizm , jizz, kawk, knob, knobead, knobed, knobend, knobhead, knobjocky, knobjokey, kock, kondum, kondums, kum, kummer, kumming, kums, kunilingus, l3i+ch, l3itch, labia, lmfao, lust, lusting, m0f0, m0fo, m45terbate, ma5terb8, ma5terbate, masochist, master-bate, masterb8, masterbat*, masterbat3, masterbate, masterbation, masterbations, masturbate, mo-fo, mof0, mofo, mothafuck, mothafucka, mothafuckas, mothafuckaz, mothafucked , mothafucker, mothafuckers, mothafuckin, mothafucking , mothafuckings, mothafucks, mother fucker, motherfuck, motherfucked, motherfucker, motherfuckers, motherfuckin, motherfucking, motherfuckings, motherfuckka, motherfucks, muff, mutha, muthafecker, muthafuckker, muther, mutherfucker, n1gga, n1gger, nazi, nigg3r, nigg4h, nigga, niggah, niggas, niggaz, nigger, niggers , nob, nob jokey, nobhead, nobjocky, nobjokey, numbnuts, nutsack, orgasim , orgasims , orgasm, orgasms , p0rn, pawn, pecker, penis, penisfucker, phonesex, phuck, phuk, phuked, phuking, phukked, phukking, phuks, phuq, pigfucker, pimpis, piss, pissed, pisser, pissers, pisses , pissflaps, pissin , pissing, pissoff , poop, porn, porno, pornography, pornos, prick, pricks , pron, pube, pusse, pussi, pussies, pussy, pussys , rectum, retard, rimjaw, rimming, s hit, s.o.b., sadist, schlong, screwing, scroat, scrote, scrotum, semen, sex, sh!+, sh!t, sh1t, shag, shagger, shaggin, shagging, shemale, shi+, shit, shitdick, shite, shited, shitey, shitfuck, shitfull, shithead, shiting, shitings, shits, shitted, shitter, shitters , shitting, shittings, shitty , skank, slut, sluts, smegma, smut, snatch, son-of-a-bitch, spac, spunk, s_h_i_t, t1tt1e5, t1tties, teets, teez, testical, testicle, tit, titfuck, tits, titt, tittie5, tittiefucker, titties, tittyfuck, tittywank, titwank, tosser, turd, tw4t, twat, twathead, twatty, twunt, twunter, v14gra, v1gra, vagina, viagra, vulva, w00se, wang, wank, wanker, wanky, whoar, whore, willies, willy, xrated, xxx';
    const default_disallow_usernames_list = 'admin, moderator';
    const default_guest_num_digits = '3';
    const default_timeout_refresh_users = '30';
    const default_timeout_refresh_messages = '2';
    const default_manual_gmt_offset = '0';
    const default_message_maximum_number_chars = '400';
    const default_avatar_size = '32';
    const default_ip_blocklist = '';
    const default_adsense_content = '';
    const default_clean_target = '300';
    const default_inactivity_timeout = '1800';

    const quick_flag_version_minimum = '2.00';
    const quick_flag_link = '';
    const link = '';
    const donate_link = '';
    const support_link = '';
    const faq_link = '';
    const changelog_link = '';

    public $default_name;
    public $date_format;
    public $time_format;
    public $gmt_offset;
    public $user_ip;
    public $user_id;
    public $user_status;
    public $user_name;
    public $no_participation;
    public $ip_blocked;
    public $must_login;
    public $options;

    protected $basename;
    protected $log_file;
    protected $db_version;
    protected $url;
    protected $path;

    public $embedded_rooms = array();
    public $smilies = array(
                    ':)' => 'smile.gif',
                    ':-P' => 'tongue.gif',
                    ':pirate2:' => 'pirate2.gif',
                    ':w00t:' =>'w00t.gif',
                    ':-D' =>'grin.gif',
                    ':lol:' => 'laugh.gif',
                    ':-(' => 'sad.gif',
                    ':-O' => 'ohmy.gif',
                    '8-)' => 'cool1.gif',
                    ':wacko:' => 'wacko.gif',
                    ':spider:' => 'spider.gif',
                    ':hmmm:' => 'hmmm.gif',
                    ':super:' => 'super.gif',
                    ':love:' => 'love.gif',
                    ':greedy:' => 'greedy.gif',
                    ':kiss:' => 'kiss.gif',
                    ':weirdo:' => 'weirdo.gif',
                    ':blush:' =>'blush.gif',
                    ':boxing:' => 'boxing.gif',
                    ':king:' => 'king.gif'
                );

    public function __construct(){
        $this->url = plugin_dir_url(__FILE__);
        $this->path =  plugin_dir_path(__FILE__);
        $this->basename = plugin_basename(__FILE__);
        $this->log_file = $this->path . 'shoutbox.log';
        $this->options = get_option('Shoutbox_options');
        $this->db_version = get_option('Shoutbox_db_version');

        $this->default_name = __('Guest_', 'shoutbox');
        $this->user_ip = (isset($_SERVER['HTTP_X_FORWARD_FOR'])) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
        $this->date_format = get_option('date_format');
        $this->time_format = get_option('time_format');
        $this->gmt_offset = ($this->options['manual_gmt_offset'] + get_option('gmt_offset'))*3600;

        add_action('init', array($this, 'init'));

        add_action('plugins_loaded', array($this, 'update_db_check'));

        add_action('wp_print_styles', array($this, 'style'));
        add_action('admin_print_styles', array($this, 'style'));

        add_action('wp_enqueue_scripts', array($this, 'js'));
        add_action('admin_enqueue_scripts', array($this, 'js'));

        add_action('admin_enqueue_scripts', array($this, 'admin_js'));

        add_action('admin_init', array($this, 'settings_init'));

        add_action('admin_menu', array($this, 'add_options_page'));

        add_action('admin_notices', array($this, 'quick_flag_version_notice'));

        add_action('admin_init', array($this, 'quick_flag_version_notice_dismiss'));

        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));

        add_shortcode('shoutbox', array($this, 'shortcode'));

        add_filter('plugin_row_meta', array($this, 'plugin_meta'), 10, 2);

        add_action('widgets_init', array($this, 'load_widgets'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-init', array($this, 'init_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-init', array($this, 'init_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-update-users',  array($this, 'update_users_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-update-users',  array($this, 'update_users_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-update-messages', array($this, 'update_messages_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-update-messages', array($this, 'update_messages_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-new-message', array($this, 'new_message_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-new-message', array($this, 'new_message_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-transcript', array($this, 'transcript_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-transcript', array($this, 'transcript_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-ban', array($this, 'ban_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-ban', array($this, 'ban_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-clean-private', array($this, 'clean_private_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-clean-private', array($this, 'clean_private_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-clean', array($this, 'clean_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-clean', array($this, 'clean_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-delete', array($this, 'delete_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-delete', array($this, 'delete_ajax_handler'));

        add_action( 'wp_ajax_nopriv_shoutbox-ajax-username-check', array($this, 'username_check_ajax_handler'));
        add_action( 'wp_ajax_shoutbox-ajax-username-check', array($this, 'username_check_ajax_handler'));

        register_activation_hook(__FILE__, array($this, 'clear_cache'));
        register_deactivation_hook(__FILE__, array($this, 'clear_cache'));

        if(!(defined('DOING_AJAX') && DOING_AJAX)){
            if(isset($this->options['clean_target_auto'])){
                add_action('Shoutbox_target_clean_update', array($this, 'clean_rooms_to_target'));
                register_deactivation_hook(__FILE__, array($this, 'deschedule_target_clean_update'));

                $this->schedule_target_clean_update();
            }else{
                $this->deschedule_target_clean_update();
            }

            if(isset($this->options['clean_private_auto'])){
                add_action('Shoutbox_private_clean_update', array($this, 'clean_private_ajax_handler'));

                register_deactivation_hook(__FILE__, array($this, 'deschedule_private_clean_update'));

                $this->schedule_private_clean_update();
            }else{
                $this->deschedule_private_clean_update();
            }
        }
    }

    public function schedule_target_clean_update(){
        if(!wp_next_scheduled('Shoutbox_target_clean_update')){
            wp_schedule_event(time(), 'daily', 'Shoutbox_target_clean_update');

            $this->log('Target clean auto scheduled');
        }
    }

    public function deschedule_target_clean_update(){
        if(wp_next_scheduled('Shoutbox_target_clean_update')){
            wp_clear_scheduled_hook('Shoutbox_target_clean_update');

            $this->log('Target clean auto descheduled');
        }
    }

    public function schedule_private_clean_update(){
        if(!wp_next_scheduled('Shoutbox_private_clean_update')){
            wp_schedule_event(time(), 'daily', 'Shoutbox_private_clean_update');

            $this->log('Private clean auto scheduled');
        }
    }

    public function deschedule_private_clean_update(){
        if(wp_next_scheduled('Shoutbox_private_clean_update')){
            wp_clear_scheduled_hook('Shoutbox_private_clean_update');

            $this->log('Private clean auto descheduled');
        }
    }

    public function init(){
	load_plugin_textdomain('shoutbox', false, dirname($this->basename) . '/languages/');

        $this->gmt_offset += $this->options['manual_gmt_offset'];

        if(is_user_logged_in()){
            if(current_user_can('manage_options') || current_user_can('moderate_Shoutbox')){
                $this->user_status = 0;
            }else{
                $this->user_status = 1;
            }

            global $current_user;
            get_currentuserinfo();

            if(isset($_COOKIE['Shoutbox_alias_'.$current_user->ID])){
                $this->user_name =  stripslashes($_COOKIE['Shoutbox_alias_'.$current_user->ID]);
            } else{
                setcookie('Shoutbox_alias_'.$current_user->ID, $current_user->user_login, 0, COOKIEPATH, COOKIE_DOMAIN);
                $this->user_name =  $current_user->user_login;
            }

            $this->user_id = $current_user->ID;
        } else{
            $this->user_status = 2;

            if(isset($_COOKIE['Shoutbox_alias'])){
                $this->user_name = stripslashes($_COOKIE['Shoutbox_alias']);
            } else{
                $maxNumWidthNumDigits = '';
                $numDigits = $this->options['guest_num_digits'];
                for($i=0; $i<$numDigits; $i++){
                    $maxNumWidthNumDigits .= '9';
                }

                $this->user_name = $this->options['default_name'].mt_rand(0, $maxNumWidthNumDigits);
                setcookie('Shoutbox_alias', $this->user_name, 0, COOKIEPATH, COOKIE_DOMAIN);
            }
            $this->user_id = 0;
        }

        $this->no_participation = 0;


        $this->ip_blocked = 0;
        if( isset($this->options['ip_blocklist'])
            &&
            $this->user_status != 0
            &&
            strpos($this->options['ip_blocklist'], $this->user_ip) !== false){
                $this->ip_blocked = 1;
                $this->no_participation = 1;
        }

        if($this->ip_blocked == 0){
            $this->must_login = 0;
            if( isset($this->options['only_logged_in_users'])
                &&
                $this->user_status == 2){
                $this->must_login = 1;
                $this->no_participation = 1;
            }
        }
    }

    public function style() {
        global $wp_styles;

        $my_style_url = $this->url . 'css/shoutbox.css';
        $my_style_file = $this->path . 'css/shoutbox.css';

        $stupid_ie_style_url = $this->url . 'css/shoutbox-ie.css';
        $stupid_ie_style_file = $this->path . 'css/shoutbox-ie.css';

        $theme_style_url = get_stylesheet_directory_uri() . '/shoutbox.css';
        $theme_style_file = get_stylesheet_directory() . '/shoutbox.css';

        if (file_exists($my_style_file)) {
            wp_enqueue_style('Shoutbox_style_sheet', $my_style_url);
        }

        if (file_exists($stupid_ie_style_file)) {
            wp_enqueue_style('Shoutbox_ie_style_sheet', $stupid_ie_style_url, array('Shoutbox_style_sheet'));
            $wp_styles->add_data('Shoutbox_ie_style_sheet', 'conditional', 'lt IE 8');
        }

        if (file_exists($theme_style_file)) {
            wp_enqueue_style('Shoutbox_theme_style_sheet', $theme_style_url, array('Shoutbox_style_sheet', 'Shoutbox_ie_style_sheet'));
        }
    }

    public function js() {
        wp_enqueue_script('jquery');

        if(isset($this->options['debug_mode']) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)){
            $script_suffix = '.dev';
            $debug_mode = 1;
        }else{
            $script_suffix = '';
            $debug_mode = 0;
        }

        wp_enqueue_script('shoutbox-c00kie', ($this->url.'js/jquery.c00kie'.$script_suffix.'.js'), array('jquery'), self::version, true);
        wp_enqueue_script('shoutbox-load', ($this->url.'js/shoutbox-load'.$script_suffix.'.js'), array('jquery', 'shoutbox-c00kie'), self::version, true);
        wp_localize_script('shoutbox-load', 'Shoutbox',
            array(
                'url' => $this->url,
                'ajaxurl' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
                'user_id' => $this->user_id,
                'version' => self::version,
                'debug_mode' => $debug_mode
            )
        );
    }

    public function admin_js($hook) {
        if ($hook == 'settings_page_shoutbox/shoutbox') {

            $script_suffix = (isset($this->options['debug_mode']) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)) ? '.dev' : '';

            wp_enqueue_script('shoutbox-admin', ($this->url.'js/shoutbox-admin'.$script_suffix.'.js'), array('jquery'), self::version, true);
            wp_localize_script('shoutbox-admin', 'Shoutbox_admin',
                array (
                    'ajaxurl' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
                    'i18n' =>
                    array(
                        'clean_private_confirm' => __('You\'re about to permanently delete all messages from all private chat rooms as well as old private chat invitations. Are you sure?','shoutbox'),
                        'clean_private_done' => __('Done','shoutbox')
                    ),
                )
            );
        }
    }

    public function username_check_ajax_handler(){
        global $wpdb;

        if($this->no_participation == 0){
            $Shoutbox_users_table_name = $wpdb->prefix . 'Shoutbox_users';

            $username_invalid = 0;
            $username_bad_words = 0;
            $username_exists = 0;
            $username_blocked = 0;

            if($_POST['username_check'] != $this->user_name){
                global $current_user;
                get_currentuserinfo();
                $_POST['username_check'] = trim(stripslashes($_POST['username_check']));

                if  (
                        ($_POST['username_check'] == '')
                        ||
                        ($_POST['username_check'] == 'Shoutbox')
                        ||
                        (isset($this->options['disallow_special_usernames']) && !validate_username($_POST['username_check']))
                    )
                    $username_invalid = 1;

                if  (   $username_invalid == 0
                        &&
                        ($this->filter($_POST['username_check'], true) != $_POST['username_check'])
                    )
                    $username_bad_words = 1;

                global $wp_version;
                if (version_compare($wp_version, '3.1', '<')){
                    require_once(ABSPATH . WPINC . '/registration.php');
                }

                if($username_bad_words == 0 && (!is_user_logged_in() || (is_user_logged_in() && strcasecmp($_POST['username_check'], $current_user->user_login) != 0))){

                    if($username_exists == 0){
                        $sql = 'SELECT COUNT(*) FROM '.$Shoutbox_users_table_name.' WHERE alias like "%' . like_escape($_POST['username_check']) . '";';

                        $users = $wpdb->get_var($sql);

                        if($users != 0){
                            $username_exists = 1;
                        }
                    }

                    if($username_exists == 0  && $this->user_status != 0 && isset($this->options['disallow_logged_in_usernames'])){
                        if(username_exists($_POST['username_check']) != null){
                            $username_exists = 1;
                        }
                    }

                    if($username_exists == 0 && $this->user_status != 0 && isset($this->options['disallow_usernames_list']) && ($this->options['disallow_usernames_list'] != '')){
                        $blocked_usernames = explode(',', $this->options['disallow_usernames_list']);
                        foreach ($blocked_usernames as $blocked_username) {
                            if(strcasecmp($_POST['username_check'], trim($blocked_username)) == 0){
                                $username_blocked = 1;
                                break;
                            }
                        }
                    }
                }

                if($username_exists == 0 && $username_blocked == 0 && $username_invalid == 0 && $username_bad_words == 0){
                    if ($this->user_status == 2){
                        setcookie('Shoutbox_alias', $_POST['username_check'], 0, COOKIEPATH, COOKIE_DOMAIN);
                    }else{
                        setcookie('Shoutbox_alias_'.$current_user->ID, $_POST['username_check'], 0, COOKIEPATH, COOKIE_DOMAIN);
                    }
                }
            }

            $response = json_encode(array('no_participation' => 0, 'username' => $_POST['username_check'], 'username_exists'=> $username_exists , 'username_blocked'=> $username_blocked, 'username_invalid'=> $username_invalid, 'username_bad_words'=> $username_bad_words));
        }else{
            $response = json_encode(array('no_participation' => 1));
        }

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function delete_ajax_handler(){
        global $wpdb;
        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        $ids = implode(', ', esc_sql((array) $_POST['to_delete_ids']));
        $rows_affected = $wpdb->query('DELETE FROM '.$Shoutbox_messages_table_name.' WHERE id IN ('.$ids.');');

        $response = json_encode(array('rows_affected' => $rows_affected));

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function clean_ajax_handler(){
        $rows_affected = $this->clean_room_to_target($_POST['to_clean_room_name'], $this->options['clean_target']);

        $response = json_encode(array('rows_affected' => $rows_affected));

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function clean_private_ajax_handler(){
        $this->log('Private clean initiated');

        global $wpdb;
        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        $rows_affected = $wpdb->query('DELETE FROM '.$Shoutbox_messages_table_name.' WHERE room LIKE "quick\_chat\_%" OR alias = "Shoutbox"');

        $response = json_encode(array('rows_affected' => $rows_affected));

        $this->log('Private clean finished');

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function ban_ajax_handler(){
        if($this->options['ip_blocklist'] != ''){
            $ip_blocklist = array_map('trim',explode(",", $this->options['ip_blocklist']));
        } else{
            $ip_blocklist = array();
        }

        foreach ($_POST['to_ban_ips'] as $ban_ip) {
            $ip_blocklist[] = $ban_ip;
        }

        $this->options['ip_blocklist'] = implode(", ", array_unique($ip_blocklist));

        update_option('Shoutbox_options', $this->options);

        $response = json_encode(array());

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function transcript_ajax_handler(){
        global $wpdb;

        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        $transcript_datetime = date_i18n('d-m-Y_H-i-s', time()+$this->gmt_offset);

        $transcript_file = $this->path.'transcripts/'.$_POST['room_name'].'_'.$transcript_datetime.'.txt';

        $transcript_url = $this->url.'transcripts/'.$_POST['room_name'].'_'.$transcript_datetime.'.txt';

        $transcript_handle = fopen($transcript_file, 'w');

        $sql = $wpdb->prepare('SELECT alias, UNIX_TIMESTAMP(timestamp) as unix_timestamp, message FROM '.$Shoutbox_messages_table_name.' WHERE room = %s AND alias != "Shoutbox" ORDER BY unix_timestamp ASC', $_POST['room_name']);
        $messages = $wpdb->get_results($sql);

        $transcript = '';

        foreach($messages as $v){
            $v->timestring = date_i18n($this->date_format.' - '.$this->time_format, $v->unix_timestamp+$this->gmt_offset);
            $transcript .= $v->alias.' ['.$v->timestring.']: '.$v->message."\n";
        }



        fwrite($transcript_handle, $transcript);

        fclose($transcript_handle);

        $response = json_encode(array('transcript_url' => $transcript_url));

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function new_message_ajax_handler(){
        global $wpdb;

        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        if($this->user_status != 0)
            $_POST['message'] = wp_kses(trim(stripslashes(substr($_POST['message'], 0, $this->options['message_maximum_number_chars']))),'');
        else
            $_POST['message'] = wp_kses(trim(stripslashes($_POST['message'])),'');

        if($this->no_participation == 0 && $_POST['message'] != ''){

            if($this->user_status != 0){
                $_POST['message'] = $this->filter($_POST['message'], (isset($this->options['replace_inside_bad_words'])? true:false));
            }

            if(isset($this->options['hyperlinks'])){
                $_POST['message'] = links_add_target(make_clickable($_POST['message']));
            }

            $wpdb->query('INSERT INTO '.$Shoutbox_messages_table_name.' (wpid, room, timestamp, alias, status, ip, message) VALUES ( "'.$this->user_id.'", "'.esc_sql($_POST['room']).'", NOW(), "'.(($_POST['sys_mes'] == 'true') ? 'Shoutbox': esc_sql($this->user_name)).'", '.$this->user_status.', "'.$this->user_ip.'", "'.esc_sql($_POST['message']).'");');
        }
        $response = json_encode(array('no_participation' => $this->no_participation));

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function update_messages_ajax_handler(){
        global $wpdb;
        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        ob_start();
        header( "Content-Type: application/json" );
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

        $rooms = implode('", "', esc_sql((array)$_POST['rooms']));

        $startTime = time();
        while((time()-$startTime)<=20){
            $sql = 'SELECT id, wpid, room, timestamp, UNIX_TIMESTAMP(timestamp) AS unix_timestamp, alias, status, message FROM '
                                                    .$Shoutbox_messages_table_name.' WHERE room IN ("'.$rooms.'") '
                                                    .' AND timestamp > FROM_UNIXTIME('.esc_sql($_POST['last_timestamp']).') '
                                                    .' ORDER BY unix_timestamp ASC';

            $messages = $wpdb->get_results($sql);
            if($messages){
                foreach($messages as $v){
                    $v->timestring = date_i18n($this->date_format.' - '.$this->time_format, $v->unix_timestamp+$this->gmt_offset);

                    if (function_exists('get_simple_local_avatar')) {
                        $v->avatar = get_simple_local_avatar($v->wpid, $this->options['avatar_size'], '', $v->alias);
                    } else {
                        $v->avatar = get_avatar($v->wpid, $this->options['avatar_size'], '', $v->alias);
                    }
                }
                $response = json_encode(array('no_participation' => $this->no_participation, 'success'=> 1,'messages'=>$messages));

                echo $response;
                ob_flush(); flush();
                exit;
            }else{
                sleep($this->options['timeout_refresh_messages']);
            }
        }

        $response = json_encode(array('no_participation' => $this->no_participation, 'success'=> 0));

        echo $response;
        ob_flush(); flush();
        exit;
    }

    public function update_users_ajax_handler(){
        global $wpdb;

        $Shoutbox_users_table_name = $wpdb->prefix . 'Shoutbox_users';

        $wpdb->query('DELETE FROM '.$Shoutbox_users_table_name.' WHERE timestamp_polled < TIMESTAMPADD(SECOND,-'.($this->options['timeout_refresh_users']*2).',NOW());');

        $rooms = esc_sql((array)$_POST['rooms']);
        $last_room = end($rooms);

        $Shoutbox_users_table_name = $wpdb->prefix . 'Shoutbox_users';

        $sql_update = 'INSERT INTO '.$Shoutbox_users_table_name.' (status, room, timestamp_polled, timestamp_joined, alias, ip, ccode, cname) VALUES';

        $sql_update_country_code = 'NULL';
        $sql_update_country_name = 'NULL';
        $sql_fetch_country = 'NULL as c, NULL as m';
        if($this->quick_flag_capable()){
            $sql_fetch_country = 'ccode as c, cname as m';
            if(($country_info = $this->country_info($this->user_ip)) != false){
                $sql_update_country_code = '"'.esc_sql($country_info->code).'"';
                $sql_update_country_name = '"'.esc_sql($country_info->name).'"';
            }
        }

        if($this->user_status == 0)
            $sql_fetch = 'SELECT status, alias, room, id, ip, '.$sql_fetch_country.'  FROM '.$Shoutbox_users_table_name.'  WHERE (';
        else
            $sql_fetch = 'SELECT status, alias, room, '.$sql_fetch_country.'  FROM '.$Shoutbox_users_table_name.'  WHERE (';

        foreach ($rooms as $room){
            $sql_fetch .= 'room = "'.$room.'"';
            $sql_update .= '('.$this->user_status.', "'.$room.'", NOW(), NOW(), "'.$this->user_name.'", "'.$this->user_ip.'", '.$sql_update_country_code.', '.$sql_update_country_name.')';
            if($room != $last_room){
                $sql_fetch .= ' OR ';
                $sql_update .= ', ';
            }
        }

        $sql_update .= ' ON DUPLICATE KEY UPDATE timestamp_polled = NOW(), ccode='.$sql_update_country_code.', cname='.$sql_update_country_name.';';

        $sql_fetch .= ') ORDER BY timestamp_joined ASC';

        if($this->no_participation == 0)
            $wpdb->query($sql_update);

        $users = $wpdb->get_results($sql_fetch);

        $response = json_encode(array('no_participation' => $this->no_participation, 'users' => $users));

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function init_ajax_handler(){
        $js_vars = array (
                'cookiepath' => COOKIEPATH,
                'cookie_domain' => COOKIE_DOMAIN,
                'smilies' => $this->smilies,
                'user_status' => $this->user_status,
                'user_ip' => $this->user_ip,
                'user_name' => $this->user_name,
                'ip_blocked' => $this->ip_blocked,
                'must_login' => $this->must_login,
                'no_participation' => $this->no_participation,
                'timeout_refresh_users' => $this->options['timeout_refresh_users'],
                'clean_target' => $this->options['clean_target'],
                'message_maximum_number_chars' => $this->options['message_maximum_number_chars'],
                'clean_target' => $this->options['clean_target'],
                'adsense_content' => $this->options['adsense_content'],
                'inactivity_timeout' => $this->options['inactivity_timeout'],
                'audio_enable' => (isset($this->options['message_sound_default_on'])) ? 1 : 0,
                'loggedin_initiate_private' => (isset($this->options['loggedin_initiate_private'])) ? 1 : 0,
                'guests_initiate_private' => (isset($this->options['guests_initiate_private'])) ? 1 : 0,
                'allow_change_username' => (isset($this->options['allow_change_username'])) ? 1 : 0,
                'i18n' => array(
                    'not_allowed_to_initiate_s' => __('Unfortunately you do not have the authority to initiate private chat','shoutbox'),
                    'delete_what_s' => __('You must select at least one message','shoutbox'),
                    'delete_confirm_s' => __('Are you sure you want to permanently delete selected messages?','shoutbox'),
                    'clean_confirm_s' => __('Are you sure you want to permanently delete all except the latest %s messages?','shoutbox'),
                    'ban_who_s' => __('You must select at least one user','shoutbox'),
                    'ban_confirm_s' => __('Are you sure you want to add selected users to your Shoutbox admin options IP blocklist?','shoutbox'),
                    'reply_to_s' => __('Reply to %s','shoutbox'),
                    'username_exists_s' => __('Already taken!','shoutbox'),
                    'username_blocked_s' => __('Not allowed!','shoutbox'),
                    'username_invalid_s' => __('Illegal characters!','shoutbox'),
                    'username_bad_words_s' => __('Profanity!','shoutbox'),
                    'invitation_received_s' => __('%s invites you to private chat. To start private chat you can click his user name on the user list.','shoutbox'),
                    'invitation_sent_s' => __('Your private chat invitation has been sent to %s.','shoutbox'),
                    'private_title_s' => __('Private chat','shoutbox'),
                    'notice_s' => __('Notice','shoutbox'),
                    'private_with_s' => __('Start private chat with %s','shoutbox'),
                    'private_close_s' => __('Close this private chat','shoutbox'),
                    'private_minimize_s' => __('Minimize this private chat','shoutbox'),
                    'private_restore_s' => __('Restore this private chat','shoutbox'),
                    'private_invite_confirm_s' => __('You\'re about to send private chat invitation to %s. Are you sure?' ,'shoutbox'),
                    'private_accept_confirm_s' => __('You\'re about to accept private chat invitation from %s. Are you sure?' ,'shoutbox'),
                    'dropped_inactivity_s' => __('You\'ve been dropped out of chat due to long period of inactivity. To continue with chat please refresh this page.','shoutbox'),
                    'username_check_wait_s' => __('Checking...','shoutbox'),
                    'add_blocklist_s' => __('Add this user\'s IP address to your IP block list','shoutbox'),
                    'ban_s' => __('Ban','shoutbox'),
                    'fetch_transcript_s' => __('Fetch this chat room transcript','shoutbox'),
                    'transcript_s' => __('Transcript','shoutbox'),
                    'all_toggle_s' => __('Select/deselect all messages toggle','shoutbox'),
                    'toggle_s' => __('Toggle','shoutbox'),
                    'delete_selected_s' => __('Delete selected messages','shoutbox'),
                    'delete_s' => __('Delete','shoutbox'),
                    'clean_all_except_s' => __('Clean all except the latest %s messages','shoutbox'),
                    'clean_s' => __('Clean','shoutbox'),
                    'scroll_toggle_s' => __('Enable/disable auto scroll when new message arrives','shoutbox'),
                    'scroll_s' => __('Scroll','shoutbox'),
                    'sound_toggle_s' => __('Enable/disable sound notification when new message arrives','shoutbox'),
                    'sound_s' => __('Sound','shoutbox'),
                    'ip_banned_s' => __('You\'re banned from chat.','shoutbox'),
                    'must_login_s' => __('You must login if you want to participate in chat.','shoutbox'),
                    'send_s' => __('Send','shoutbox')
                )
            );

        $response = json_encode(array('js_vars' => $js_vars));

        header( "Content-Type: application/json" );
        echo $response;
        exit;
    }

    public function clean_rooms_to_target(){
        $this->log('Target clean initiated');

        global $wpdb;
        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        $rooms = $wpdb->get_col('SELECT room FROM '.$Shoutbox_messages_table_name.' GROUP BY room;');
        if($rooms){
            foreach($rooms as $room){
                $this->clean_room_to_target($room, $this->options['clean_target']);
            }
        }

        $this->log('Target clean finished');
    }

    public function clear_cache(){
        if ( function_exists('wp_cache_clear_cache') ) {
            $GLOBALS["super_cache_enabled"]=1;
            wp_cache_clear_cache();
        }else if(function_exists('simple_cache_clear')){
            simple_cache_clear();
        }else{
            if ( function_exists('w3tc_pgcache_flush') )
                w3tc_pgcache_flush();

            if ( function_exists('w3tc_dbcache_flush') )
                w3tc_dbcache_flush();

            if ( function_exists('w3tc_minify_flush') )
                w3tc_minify_flush();

            if ( function_exists('w3tc_objectcache_flush') )
                w3tc_objectcache_flush();

            if ( function_exists('wp_cache_clear_cache') )
                wp_cache_clear_cache();
        }
    }

    public function country_info($ip){
        if(isset($_COOKIE['quick_flag_info'])){
            $info = unserialize(stripslashes($_COOKIE['quick_flag_info']));
            if($info == FALSE || $info->ip == $ip)
                return $info;
        }

        global $quick_flag;
        $info = $quick_flag->get_info($ip);
        setcookie('quick_flag_info', serialize($info), 0, COOKIEPATH, COOKIE_DOMAIN, false, true);

        return $info;
    }

    public function plugin_meta($links, $file) {
        if ($file == $this->basename) {
            return array_merge(
                $links
            );
        }
        return $links;
    }

    public function action_links($links, $file){
        if ($file == $this->basename) {
            $settings_link = '<a href="' . get_admin_url(null, 'admin.php?page='.$this->basename) . '">'.__('Settings', 'quick-count').'</a>';
            $links[] = $settings_link;
        }

        return $links;
    }

    public function add_options_page(){
        add_options_page('Shoutbox '.__('options page','shoutbox'), 'Shoutbox', 'manage_options', __FILE__, array($this, 'options_page'));
        add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
    }

    public function options_page(){
    ?>
        <div class="wrap">
            <div class="icon32" id="icon-options-general"><br></div>
            <h2>Shoutbox</h2>
            <form action="options.php" method="post">
            <?php settings_fields('Shoutbox_options'); ?>
            <?php do_settings_sections(__FILE__); ?>
            <p class="submit">
                <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
            </p>
            </form>
        </div>
    <?php
    }

    public function options_validate($input) {
        global $wp_version;
        $validation_errors = array();

        $roles = $this->get_editable_roles();
        foreach($roles as $key => $value){
            $role = get_role($key);
            if(isset($input['moderator_roles_'.$key]) && !isset($this->options['moderator_roles_'.$key])){
                $role->add_cap('moderate_Shoutbox');
            } else if(!isset($input['moderator_roles_'.$key]) && isset($this->options['moderator_roles_'.$key])){
                $role->remove_cap('moderate_Shoutbox');
            }
        }

        if(!is_numeric($input['timeout_refresh_users']) || $input['timeout_refresh_users'] < 1){
            $input['timeout_refresh_users'] =  self::default_timeout_refresh_users;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_timeout_refresh_users',
                        'code' => 'Shoutbox_timeout_refresh_users_error',
                        'title' => __('Interval for refreshing list of online users (seconds):','shoutbox'),
                        'message' => __('Must be positive integer.','shoutbox'));
        } else{
            $input['timeout_refresh_users'] = floor($input['timeout_refresh_users']);
        }

        if(!is_numeric($input['timeout_refresh_messages']) || $input['timeout_refresh_messages'] < 1){
            $input['timeout_refresh_messages'] =  self::default_timeout_refresh_messages;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_timeout_refresh_messages',
                        'code' => 'Shoutbox_timeout_refresh_messages_error',
                        'title' => __('Interval for refreshing list of messages (seconds):','shoutbox'),
                        'message' => __('Must be positive integer.','shoutbox'));
        } else{
            $input['timeout_refresh_messages'] = floor($input['timeout_refresh_messages']);
        }

        $input['badwords_list'] =  wp_filter_nohtml_kses(trim($input['badwords_list']));

        $input['ip_blocklist'] =  wp_filter_nohtml_kses(trim($input['ip_blocklist']));

        $input['disallow_usernames_list'] =  wp_filter_nohtml_kses(trim($input['disallow_usernames_list']));

        if(!is_numeric($input['guest_num_digits']) || $input['guest_num_digits'] < 1 || $input['guest_num_digits'] > 10){
            $input['guest_num_digits'] =  self::default_guest_num_digits;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_guest_num_digits',
                        'code' => 'Shoutbox_guest_num_digits_error',
                        'title' => __('Maximum number of digits for random guests chat user name suffix:','shoutbox'),
                        'message' => sprintf(__('Must be positive integer less than %d.','shoutbox'), 10));
        }

        if(!is_numeric($input['manual_gmt_offset']) || $input['manual_gmt_offset'] < -12 || $input['manual_gmt_offset'] > 12){
            $input['manual_gmt_offset'] =  self::default_manual_gmt_offset;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_manual_gmt_offset',
                        'code' => 'Shoutbox_manual_gmt_offset_error',
                        'title' => __('Manual timestamp offset when displaying messages (+/- hours):','shoutbox'),
                        'message' => sprintf(__('Must be integer with value between %d and %d.','shoutbox'), -12, 12));
        } else{
            $input['manual_gmt_offset'] = floor($input['manual_gmt_offset']);
        }

        if(!is_numeric($input['message_maximum_number_chars']) || $input['message_maximum_number_chars'] < 1 || $input['message_maximum_number_chars'] < 50){
            $input['message_maximum_number_chars'] =  self::default_message_maximum_number_chars;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_message_maximum_number_chars',
                        'code' => 'Shoutbox_message_maximum_number_chars_error',
                        'title' => __('Maximum number of characters for each message:','shoutbox'),
                        'message' => sprintf(__('Must be positive integer with value %d or greater.','shoutbox'), 50));
        } else{
            $input['message_maximum_number_chars'] = floor($input['message_maximum_number_chars']);
        }

        if(!is_numeric($input['avatar_size']) || $input['avatar_size'] < 16 || $input['avatar_size'] > 512){
            $input['avatar_size'] =  self::default_avatar_size;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_avatar_size',
                        'code' => 'Shoutbox_avatar_size_error',
                        'title' => __('User avatar size (pixels):','shoutbox'),
                        'message' => sprintf(__('Must be integer with value between %d and %d.','shoutbox'), 16, 512));
        } else{
            $input['avatar_size'] = floor($input['avatar_size']);
        }

        if(!is_numeric($input['clean_target']) || $input['clean_target'] < 1){
            $input['clean_target'] =  self::default_clean_target;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_clean_target',
                        'code' => 'Shoutbox_clean_target_error',
                        'title' => __('Clean button will delete messages older than following number of messages per chat room:','shoutbox'),
                        'message' => __('Must be positive integer.','shoutbox'));
        } else{
            $input['clean_target'] = floor($input['clean_target']);
        }

        if(!is_numeric($input['inactivity_timeout']) || $input['inactivity_timeout']  < 1 || $input['inactivity_timeout'] < 2 * $input['timeout_refresh_users']){
            $input['inactivity_timeout'] =  self::default_inactivity_timeout;
            $validation_errors[] =
                array(  'setting' => 'Shoutbox_inactivity_timeout',
                        'code' => 'Shoutbox_inactivity_timeout_error',
                        'title' => __('Timeout for disabling updates to inactive user (seconds):','shoutbox'),
                        'message' => sprintf(__('Must be positive integer with value %d or greater.','shoutbox'), 2 * $input['timeout_refresh_users']));
        } else {
            $input['inactivity_timeout'] = floor($input['inactivity_timeout']);
        }

        if(!empty($validation_errors) && version_compare($wp_version, '3.0', '>=')){
            foreach ($validation_errors as $error) {
                add_settings_error($error['setting'], $error['code'], $error['title'].' '.$error['message']);
            }
        }

        $this->clear_cache();

        return $input;
    }

    public function settings_init(){
        register_setting('Shoutbox_options', 'Shoutbox_options', array($this, 'options_validate'));

        add_settings_section('donate_section', __('Info','shoutbox'), array($this, 'settings_section_donate'), __FILE__);
        add_settings_section('general_section', __('General options','shoutbox'), array($this, 'settings_section_general'), __FILE__);
        add_settings_section('private_section', __('Private chat options','shoutbox'), array($this,'settings_section_private'), __FILE__);
        add_settings_section('filter_section', __('Filter options','shoutbox'), array($this, 'settings_section_filter'), __FILE__);
        add_settings_section('security_section', __('Security options','shoutbox'), array($this, 'settings_section_security'), __FILE__);
        add_settings_section('appearance_section', __('Appearance options','shoutbox'), array($this, 'settings_section_appearance'), __FILE__);

        add_settings_field('Shoutbox_debug_mode', __('Debug mode (enable only when debugging):','shoutbox'), array($this, 'settings_field_debug_mode'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_message_sound_default_on', __('Incoming message sound notification on by default:','shoutbox'), array($this, 'settings_field_message_sound_default_on'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_clean_target_auto', __('Do automatic daily cleanup to delete messages older than target number of messages:','shoutbox'), array($this, 'settings_field_clean_target_auto'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_def_name', __('Chat name prefix for guest users:','shoutbox'), array($this, 'settings_field_defname'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_guest_num_digits', __('Maximum number of digits for random guests chat user name suffix:','shoutbox'), array($this, 'settings_field_guest_num_digits'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_timeout_refresh_users', __('Interval for refreshing list of online users (seconds):','shoutbox'), array($this, 'settings_field_timeout_refresh_users'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_timeout_refresh_messages', __('Interval for refreshing list of messages (seconds):','shoutbox'), array($this, 'settings_field_timeout_refresh_messages'), __FILE__, 'general_section');
        add_settings_field('Shoutbox_clean_target', __('Target number of messages for automatic daily cleanup and chat interface "Clean" button:','shoutbox'), array($this, 'settings_field_clean_target'), __FILE__, 'general_section');

        add_settings_field('Shoutbox_hyperlinks', __('Convert URLs to hyperlinks:','shoutbox'), array($this, 'settings_field_hyperlinks'), __FILE__, 'filter_section');
        add_settings_field('Shoutbox_disallow_special_usernames', __('Disallow using special characters inside chat user names (including special locale characters):','shoutbox'), array($this, 'settings_field_disallow_special_usernames'), __FILE__, 'filter_section');
        add_settings_field('Shoutbox_replace_inside_bad_words', __('Filter bad words contained inside other words:','shoutbox'), array($this, 'settings_field_replace_inside_bad_words'), __FILE__, 'filter_section');
        add_settings_field('Shoutbox_bad_words', __('Bad words list (comma separated):','shoutbox'), array($this, 'settings_field_badwords'), __FILE__, 'filter_section');

        add_settings_field('Shoutbox_loggedin_initiate_private', __('Logged in users can initiate private chat:','shoutbox'), array($this, 'settings_field_loggedin_initiate_private'), __FILE__, 'private_section');
        add_settings_field('Shoutbox_guests_initiate_private', __('Guest users can initiate private chat:','shoutbox'), array($this, 'settings_field_guests_initiate_private'), __FILE__, 'private_section');
        add_settings_field('Shoutbox_clean_private_auto', __('Automatically delete all messages from all private chat rooms as well as old private chat invitations daily:','shoutbox'), array($this, 'settings_field_clean_private_auto'), __FILE__, 'private_section');
        add_settings_field('Shoutbox_clean_private', __('Delete all messages from all private chat rooms as well as old private chat invitations now:','shoutbox'), array($this, 'settings_field_clean_private'), __FILE__, 'private_section');

        add_settings_field('Shoutbox_moderator_roles', __('WordPress user roles allowed to access moderation tools:','shoutbox'), array($this, 'settings_field_moderator_roles'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_only_logged_in_users', __('Only logged in users can participate in chat:','shoutbox'), array($this, 'settings_field_only_logged_in_users'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_disallow_logged_in_usernames', __('Protect registered users user names from being used by other users:','shoutbox'), array($this, 'settings_field_disallow_logged_in_usernames'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_allow_change_username', __('Allow users to change their chat user name:','shoutbox'), array($this, 'settings_field_allow_change_username'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_inactivity_timeout', __('Timeout for disabling updates to inactive user (seconds):','shoutbox'), array($this, 'settings_field_inactivity_timeout'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_message_maximum_number_chars', __('Maximum number of characters for each message:','shoutbox'), array($this, 'settings_field_message_maximum_number_chars'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_disallow_usernames_list', __('Restricted chat user names list (comma separated):','shoutbox'), array($this, 'settings_field_disallow_usernames_list'), __FILE__, 'security_section');
        add_settings_field('Shoutbox_ip_blocklist', __('Deny chat access to the following IP addresses (comma separated):','shoutbox'), array($this, 'settings_field_ip_blocklist'), __FILE__, 'security_section');

        add_settings_field('Shoutbox_hide_widget_if_embedded', __('Hide Shoutbox sidebar widget on pages where same chat room is embedded using shortcode:','shoutbox'), array($this, 'settings_field_hide_widget_if_embedded'), __FILE__, 'appearance_section');
        add_settings_field('Shoutbox_manual_gmt_offset', __('Manual timestamp offset when displaying messages (+/- hours):','shoutbox'), array($this, 'settings_field_manual_gmt_offset'), __FILE__, 'appearance_section');
        add_settings_field('Shoutbox_avatar size', __('User avatar size (pixels):','shoutbox'), array($this, 'settings_field_avatar_size'), __FILE__, 'appearance_section');
        add_settings_field('Shoutbox_adsense_code', __('Advertisement code for your AdSense or other ads placed between chat user name input box and message text input box:','shoutbox'), array($this, 'settings_field_adsense_content'), __FILE__, 'appearance_section');

        add_settings_field('Shoutbox_version', __('Shoutbox version:','shoutbox'), array($this, 'settings_field_version'), __FILE__, 'donate_section');
    }

    public function settings_section_donate() {
        echo '<p>';
        echo __('Thanks for using Steveas\'s ShoutBox!<br/>Here is shortcode for inserting chat to pages/posts:<hr/>[shoutbox height="400" room="default" userlist="1" userlist_position="left" smilies="1" send_button="0" loggedin_visible="1" guests_visible="1" avatars="1" counter="1"]','shoutbox');
        echo '</p>';
    }

    public function settings_section_general() {
        echo '<p>';
        echo __('Here you can control all general options:','shoutbox');
        echo '</p>';
    }

    public function settings_section_private() {
        echo '<p>';
        echo __('Here you can control all private chat options:','shoutbox');
        echo '</p>';
    }

    public function settings_section_filter() {
        echo '<p>';
        echo __('Here you can control Shoutbox message and chat user names filter:','shoutbox');
        echo '</p>';
    }

    public function settings_section_security() {
        echo '<p>';
        echo __('In this section you can control security options:','shoutbox');
        echo '</p>';
    }

    public function settings_section_appearance() {
        echo '<p>';
        echo __('Here are the Shoutbox appearance options:','shoutbox');
        echo '</p>';
    }

    public function settings_field_timeout_refresh_users() {
        echo '<input id="Shoutbox_timeout_refresh_users" name="Shoutbox_options[timeout_refresh_users]" size="10" type="text" value="'.$this->options['timeout_refresh_users'].'" />';
    }

    public function settings_field_timeout_refresh_messages() {
        echo '<input id="Shoutbox_timeout_refresh_messages" name="Shoutbox_options[timeout_refresh_messages]" size="10" type="text" value="'.$this->options['timeout_refresh_messages'].'" />';
    }

    public function settings_field_faq() {
        echo '<a href="'.self::faq_link.'" target="_blank">'.__('FAQ','shoutbox').'</a>';
    }

    public function settings_field_version() {
        echo self::version;
    }

    public function settings_field_changelog() {
        echo '<a href="'.self::changelog_link.'" target="_blank">'.__('Changelog','shoutbox').'</a>';
    }

    public function settings_field_support_page() {
        echo '<a href="'.self::support_link.'" target="_blank">'.__('Shoutbox at TechyTalk.info','shoutbox').'</a>';
    }

    public function settings_field_paypal() {}

    public function settings_field_defname() {
        echo '<input id="Shoutbox_def_name" name="Shoutbox_options[default_name]" size="10" type="text" value="'.__($this->options['default_name'],'shoutbox').'" />';
    }

    public function settings_field_guest_num_digits() {
        echo '<input id="Shoutbox_guest_num_digits" name="Shoutbox_options[guest_num_digits]" size="10" type="text" value="'.$this->options['guest_num_digits'].'" />';
    }

    public function settings_field_badwords() {
        echo '<a class="Shoutbox_show_hide" href="">'.__('Show', 'shoutbox').'</a>';
        echo '<br>';
        echo '<textarea class="shoutbox-show-hide"  id="Shoutbox_bad_words" name="Shoutbox_options[badwords_list]" rows="5" cols="50" >'.$this->options['badwords_list'].'</textarea>';
    }

    public function settings_field_disallow_special_usernames() {
        echo '<input id="Shoutbox_disallow_special_usernames" name="Shoutbox_options[disallow_special_usernames]" type="checkbox" value="1" ';
        if(isset($this->options['disallow_special_usernames'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_replace_inside_bad_words() {
        echo '<input id="Shoutbox_replace_inside_bad_words" name="Shoutbox_options[replace_inside_bad_words]" type="checkbox" value="1" ';
        if(isset($this->options['replace_inside_bad_words'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_hyperlinks() {
        echo '<input id="Shoutbox_hyperlinks" name="Shoutbox_options[hyperlinks]" type="checkbox" value="1" ';
        if(isset($this->options['hyperlinks'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_disable_quick_flag() {
        echo '<input id="Shoutbox_disable_quick_flag" name="Shoutbox_options[disable_quick_flag]" type="checkbox" value="1" ';
        if(isset($this->options['disable_quick_flag'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_hide_widget_if_embedded() {
        echo '<input id="Shoutbox_hide_widget_if_embedded" name="Shoutbox_options[hide_widget_if_embedded]" type="checkbox" value="1" ';
        if(isset($this->options['hide_widget_if_embedded'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_hide_linkhome() {
        echo '<input id="Shoutbox_hide_linkhome" name="Shoutbox_options[hide_linkhome]" type="checkbox" value="1" ';
        if(isset($this->options['hide_linkhome'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_debug_mode() {
        echo '<input id="Shoutbox_debug_mode" name="Shoutbox_options[debug_mode]" type="checkbox" value="1" ';
        if(isset($this->options['debug_mode'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_only_logged_in_users() {
        echo '<input id="Shoutbox_only_logged_in_users" name="Shoutbox_options[only_logged_in_users]" type="checkbox" value="1" ';
        if(isset($this->options['only_logged_in_users'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_message_sound_default_on() {
        echo '<input id="Shoutbox_message_sound_default_on" name="Shoutbox_options[message_sound_default_on]" type="checkbox" value="1" ';
        if(isset($this->options['message_sound_default_on'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_disallow_logged_in_usernames() {
        echo '<input id="Shoutbox_disallow_logged_in_usernames" name="Shoutbox_options[disallow_logged_in_usernames]" type="checkbox" value="1" ';
        if(isset($this->options['disallow_logged_in_usernames'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_disallow_usernames_list() {
        echo '<a class="Shoutbox_show_hide" href="">'.__('Show', 'shoutbox').'</a>';
        echo '<br>';
        echo '<textarea class="shoutbox-show-hide" id="Shoutbox_disallow_usernames_list" name="Shoutbox_options[disallow_usernames_list]" rows="5" cols="50">'.$this->options['disallow_usernames_list'].'</textarea>';
    }

    public function settings_field_ip_blocklist() {
        echo '<a class="Shoutbox_show_hide" href="">'.__('Show', 'shoutbox').'</a>';
        echo '<br>';
        echo '<textarea class="shoutbox-show-hide" id="Shoutbox_ip_blocklist" name="Shoutbox_options[ip_blocklist]" rows="5" cols="50">'.$this->options['ip_blocklist'].'</textarea>';
    }

    public function settings_field_adsense_content(){
        echo '<a class="Shoutbox_show_hide" href="">'.__('Show', 'shoutbox').'</a>';
        echo '<br>';
        echo '<textarea class="shoutbox-show-hide" id="Shoutbox_adsense_content" name="Shoutbox_options[adsense_content]" rows="5" cols="50">'.$this->options['adsense_content'].'</textarea>';
    }

    public function settings_field_manual_gmt_offset() {
        echo '<input id="Shoutbox_manual_gmt_offset" name="Shoutbox_options[manual_gmt_offset]" size="10" type="text" value="'.$this->options['manual_gmt_offset'].'" />';
    }

    public function settings_field_loggedin_initiate_private() {
        echo '<input id="Shoutbox_loggedin_initiate_private" name="Shoutbox_options[loggedin_initiate_private]" type="checkbox" value="1" ';
        if(isset($this->options['loggedin_initiate_private'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_guests_initiate_private() {
        echo '<input id="Shoutbox_guests_initiate_private" name="Shoutbox_options[guests_initiate_private]" type="checkbox" value="1" ';
        if(isset($this->options['guests_initiate_private'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_message_maximum_number_chars() {
        echo '<input id="Shoutbox_message_maximum_number_chars" name="Shoutbox_options[message_maximum_number_chars]" size="10" type="text" value="'.$this->options['message_maximum_number_chars'].'" />';
    }

    public function settings_field_avatar_size() {
        echo '<input id="Shoutbox_avatar_size" name="Shoutbox_options[avatar_size]" size="10" type="text" value="'.$this->options['avatar_size'].'" />';
    }

    public function settings_field_clean_target() {
        echo '<input id="Shoutbox_clean_target" name="Shoutbox_options[clean_target]" size="10" type="text" value="'.$this->options['clean_target'].'" />';
    }

    public function settings_field_clean_target_auto(){
        echo '<input id="Shoutbox_clean_target_auto" name="Shoutbox_options[clean_target_auto]" type="checkbox" value="1" ';
        if(isset($this->options['clean_target_auto'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_clean_private(){
        echo '<a id="Shoutbox_clean_private" href="">'.__('Delete').'</a>';
    }

    public function settings_field_clean_private_auto(){
        echo '<input id="Shoutbox_clean_private_auto" name="Shoutbox_options[clean_private_auto]" type="checkbox" value="1" ';
        if(isset($this->options['clean_private_auto'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_allow_change_username(){
        echo '<input id="Shoutbox_allow_change_username" name="Shoutbox_options[allow_change_username]" type="checkbox" value="1" ';
        if(isset($this->options['allow_change_username'])) echo 'checked="checked"';
        echo '/>';
    }

    public function settings_field_inactivity_timeout() {
        echo '<input id="Shoutbox_inactivity_timeout" name="Shoutbox_options[inactivity_timeout]" size="10" type="text" value="'.$this->options['inactivity_timeout'].'" />';
    }

    public function settings_field_moderator_roles(){
        $roles = $this->get_editable_roles();

        foreach($roles as $key => $value){
            echo '<input id="Shoutbox_moderator_roles_'.$key.'" name="Shoutbox_options[moderator_roles_'.$key.']" type="checkbox" value="1" ';
            if(isset($this->options['moderator_roles_'.$key])) echo 'checked="checked"';
            echo '/> <label for="Shoutbox_options[moderator_roles_'.$key.']">'.translate_user_role($value['name']).'</label>';
            echo '<br/>';
        }
    }

    public function get_editable_roles() {
        global $wp_roles;

        if (!isset($wp_roles))
            $wp_roles = new WP_Roles();

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);

        array_shift($editable_roles);

        return $editable_roles;

    }

    public function quick_flag_version_notice() {
        global $current_screen;

        if ($this->quick_flag_capable() == false &&
            current_user_can('manage_options') &&
            ($current_screen->base == 'settings_page_shoutbox/shoutbox')) {
            global $current_user ;
            $user_id = $current_user->ID;
        }
    }

    public function quick_flag_version_notice_dismiss() {
        if(current_user_can('manage_options')){
            global $current_user;
            $user_id = $current_user->ID;

            if (isset($_GET['Shoutbox_quick_flag_notice_dismiss']) && '0' == $_GET['Shoutbox_quick_flag_notice_dismiss'] ) {
                add_user_meta($user_id, 'Shoutbox_quick_flag_notice_dismiss', 'true', true);
            }
        }
    }

    public function quick_flag_capable(){
        global $quick_flag;

        if(!isset($quick_flag) || !is_object($quick_flag) || (Quick_Flag::version < self::quick_flag_version_minimum) || isset($this->options['disable_quick_flag']))
            return false;

        return true;
    }

    public function dashboard_widget(){
        echo $this->Shoutbox(400, 'admin_room_'.substr(md5(AUTH_SALT),0,5), 1, 'left', 0, 0, 1, 1, 1);
    }

    public function add_dashboard_widgets() {
        if($this->user_status == 0)
            wp_add_dashboard_widget('Shoutbox_dashboard_widget', __('Shoutbox Admin\'s Lounge','shoutbox'), array($this, 'dashboard_widget'));
    }

    public function Shoutbox($height = 400, $room = 'default', $userlist = 1, $userlist_position = 'left', $smilies = 1, $send_button = 0, $loggedin_visible = 1, $guests_visible = 1, $avatars = 1, $counter = 1) {
        $content = '';
        ob_start();

        echo '<div class="shoutbox-container" data-shoutbox-id="'.wp_generate_password(12, false, false).'" data-shoutbox-height="'.$height.'" data-shoutbox-room-name="'.$room.'" data-shoutbox-userlist="'.$userlist.'" data-shoutbox-userlist-position="'.$userlist_position.'" data-shoutbox-smilies="'.$smilies.'" data-shoutbox-send-button="'.$send_button.'" data-shoutbox-loggedin-visible="'.$loggedin_visible.'" data-shoutbox-guests-visible="'.$guests_visible.'" data-shoutbox-avatars="'.$avatars.'" data-shoutbox-counter="'.$counter.'">';
            echo '<div class="shoutbox-loading">'.__('LOADING...', 'shoutbox').'</div>';
        echo '</div>';

        $content =  ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function shortcode( $atts, $content=null, $code="" ) {
        extract(shortcode_atts( array(	'height' => 400,
                                        'room' => 'default',
                                        'userlist' => 1,
                                        'userlist_position' => 'left',
                                        'avatars' => 1,
                                        'smilies' => 1,
                                        'send_button' => 0,
                                        'loggedin_visible' => 1,
                                        'guests_visible' => 1,
                                        'counter' => 1),
                                        $atts ));

        $this->embedded_rooms[$room] = 1;

        return $this->Shoutbox($height, $room, $userlist, $userlist_position, $smilies, $send_button, $loggedin_visible, $guests_visible, $avatars, $counter);
    }

    public function load_widgets() {
        register_widget('Shoutbox_Widget');
    }

    public function install() {
        global $wpdb;

        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';
        $Shoutbox_users_table_name = $wpdb->prefix . 'Shoutbox_users';

        if($this->db_version < 12){
            // Shoutbox cannot be upgraded from 1.x to 2.x
            $Shoutbox_messages_uninstall_table_name = $wpdb->prefix . 'Shoutbox';
            $this->options = get_option('Shoutbox_options');
            $query = $wpdb->query('DROP TABLE IF EXISTS '.$Shoutbox_messages_uninstall_table_name.';');
        }

        if ($this->db_version < 15){
            // This is upgrade from v14 database to v15 database, added id auto increment to users table, impossible to do alter, nuke it (QC v2.30)
            $query = $wpdb->query('DROP TABLE IF EXISTS '.$Shoutbox_users_table_name.';');
        }

        $messages_table_exists = ($wpdb->get_var('SHOW TABLES LIKE \''.$Shoutbox_messages_table_name.'\';') == $Shoutbox_messages_table_name) ? 1: 0;
        $users_table_exists = ($wpdb->get_var('SHOW TABLES LIKE \''.$Shoutbox_users_table_name.'\';') == $Shoutbox_users_table_name) ? 1: 0;

        if($messages_table_exists == 0) {
        $sql_messages = 'CREATE TABLE '.$Shoutbox_messages_table_name.' (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            wpid BIGINT(20) UNSIGNED NOT NULL DEFAULT "0",
            room VARCHAR(50) NOT NULL DEFAULT "default",
            timestamp TIMESTAMP NOT NULL,
            alias VARCHAR(100) NOT NULL DEFAULT "",
            status TINYINT(1) NOT NULL DEFAULT 2,
            ip VARCHAR(39) NOT NULL,
            message TEXT NOT NULL,
            INDEX (timestamp ASC),
            INDEX (room ASC)) ENGINE=MyISAM DEFAULT CHARACTER SET utf8, COLLATE utf8_general_ci;';

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql_messages);

        } else{
                if($this->db_version < 14){
                    // This is upgrade from v13 database to v14 database, we need to alter table (QC v2.20)
                    $query = $wpdb->query('ALTER TABLE '.$Shoutbox_messages_table_name.' ADD COLUMN md5email CHAR(32) NOT NULL DEFAULT "" AFTER alias;');
                }

                if($this->db_version < 18){
                    // This is upgrade from v16 database to v18 database, we need to alter table (QC v2.40)
                    $query = $wpdb->query('ALTER TABLE '.$Shoutbox_messages_table_name.' CHANGE COLUMN alias alias VARCHAR(255) NOT NULL DEFAULT "";');
                }

                if($this->db_version < 19){
                    // This is upgrade from v18 database to v19 database, we need to alter table (QC v2.40)
                    $query = $wpdb->query('ALTER TABLE '.$Shoutbox_messages_table_name.' ADD COLUMN wpid BIGINT(20) UNSIGNED NOT NULL DEFAULT "0" AFTER id;');
                    $query = $wpdb->query('ALTER TABLE '.$Shoutbox_messages_table_name.' DROP md5email;');
                }
        }

        if($users_table_exists == 0) {
            $sql_users = 'CREATE TABLE '.$Shoutbox_users_table_name.' (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            status TINYINT(1) NOT NULL DEFAULT 2,
            room VARCHAR(50) NOT NULL DEFAULT "default",
            timestamp_polled TIMESTAMP NOT NULL,
            timestamp_joined TIMESTAMP NOT NULL,
            alias VARCHAR(255) NOT NULL default "",
            ip VARCHAR(39) NOT NULL DEFAULT "" ,
            ccode CHAR(2) NULL,
            cname VARCHAR(150) NULL,
            INDEX (timestamp_polled ASC, timestamp_joined ASC),
            UNIQUE KEY roomalias (room, alias)) ENGINE=MyISAM DEFAULT CHARACTER SET utf8, COLLATE utf8_general_ci;';

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql_users);

        } else{
            if($this->db_version < 23){
                $wpdb->query('ALTER TABLE '.$Shoutbox_users_table_name.' ADD COLUMN ccode CHAR(2) NULL AFTER ip;');
                $wpdb->query('ALTER TABLE '.$Shoutbox_users_table_name.' ADD COLUMN cname VARCHAR(150) NULL AFTER ccode;');
            }
        }

        if($this->db_version < 12){
            // Shoutbox cannot be upgraded from 1.x to 2.x
            if(get_option('Shoutbox_options')) delete_option('Shoutbox_options');
            if(get_option('Shoutbox_db_version')) delete_option('Shoutbox_db_version');
            if(get_option('widget_shoutbox-widget')) delete_option('widget_shoutbox-widget');
        }

        if($this->db_version < 13){
            if(!isset($this->options['adsense_content'])) {
                $this->options['adsense_content'] = self::default_adsense_content;
            }
        }

        if($this->db_version < 14){
            $widget_options = get_option('widget_shoutbox-widget');
            if(isset($widget_options) && is_array($widget_options)){
                foreach($widget_options as &$option){
                    if (is_array($option) && !empty($option)){
                            $option['gravatars'] = 1;
                            $option['gravatars_size'] = 32;
                    }
                }
                update_option('widget_shoutbox-widget', $widget_options);
            }
        }

        if($this->db_version < 15){
            if(!isset($this->options['timeout_refresh_messages'])) {
                $this->options['timeout_refresh_messages'] = self::default_timeout_refresh_messages;
            }

            if(isset($this->options['timeout_consider_offline'])) {
                unset($this->options['timeout_consider_offline']);
            }
        }

        if($this->db_version < 16){
            $widget_options = get_option('widget_shoutbox-widget');
            if(isset($widget_options) && is_array($widget_options)){
                foreach($widget_options as &$option){
                    if (is_array($option) && !empty($option)){
                            $option['loggedin_visible'] = 1;
                            $option['guests_visible'] = 1;
                    }
                }
                update_option('widget_shoutbox-widget', $widget_options);
            }
        }

        if($this->db_version < 18){
            if(!isset($this->options['manual_gmt_offset'])) {
                $this->options['manual_gmt_offset'] = self::default_manual_gmt_offset;
            }

            // Remove few options to simplify code (server performance)
            if(isset($this->options['keep_first_last'])) {
                unset($this->options['keep_first_last']);
            }

            if(isset($this->options['allow_guests_choice'])) {
                unset($this->options['allow_guests_choice']);
            }

            if(isset($this->options['allow_logged_in_choice'])) {
                unset($this->options['allow_logged_in_choice']);
            }

            // Increase users and messages refresh times (server performance)
            if(isset($this->options['timeout_refresh_users'])) {
                $this->options['timeout_refresh_users'] = self::default_timeout_refresh_users;
            }

            if(isset($this->options['timeout_refresh_messages'])) {
                $this->options['timeout_refresh_messages'] = self::default_timeout_refresh_messages;
            }
        }

        if($this->db_version < 19){
            if(isset($this->options['keep_around_count'])) {
                unset($this->options['keep_around_count']);
            }

            if(!isset($this->options['message_maximum_number_chars'])) {
                $this->options['message_maximum_number_chars'] = self::default_message_maximum_number_chars;
            }

            if(!isset($this->options['avatar_size'])) {
                $this->options['avatar_size'] = self::default_avatar_size;
            }

            $widget_options = get_option('widget_shoutbox-widget');
            if(isset($widget_options) && is_array($widget_options)){
                foreach($widget_options as &$option){
                    if (is_array($option) && !empty($option)){
                            $option['height'] = $option['widgetheight'];
                    }
                }
                update_option('widget_shoutbox-widget', $widget_options);
            }
        }

        if($this->db_version < 20){
            $widget_options = get_option('widget_shoutbox-widget');
            if(isset($widget_options) && is_array($widget_options)){
                foreach($widget_options as &$option){
                    if (is_array($option) && !empty($option)){
                            $option['avatars'] = 1;
                            $option['smilies'] = 1;
                    }
                }
                update_option('widget_shoutbox-widget', $widget_options);
            }
        }

        if($this->db_version < 21){
            if(!isset($this->options['clean_target'])) {
                $this->options['clean_target'] = self::default_clean_target;
            }

            if(!isset($this->options['allow_change_username'])) {
                $this->options['allow_change_username'] = 1;
            }
        }

        if($this->db_version < 24){
            if(!isset($this->options['inactivity_timeout'])) {
                $this->options['inactivity_timeout'] = self::default_inactivity_timeout;
            }
        }

        if($this->db_version < 25){
            $widget_options = get_option('widget_shoutbox-widget');
            if(isset($widget_options) && is_array($widget_options)){
                foreach($widget_options as &$option){
                    if (is_array($option) && !empty($option)){
                            $option['counter'] = 1;
                    }
                }
                update_option('widget_shoutbox-widget', $widget_options);
            }
        }

        if($this->db_version < 26){
            if(!isset($this->options['clean_target_auto'])) {
                $this->options['clean_target_auto'] = '1';
            }

            if(!isset($this->options['clean_private_auto'])) {
                $this->options['clean_private_auto'] = '1';
            }
        }

        if(!isset($this->options['hyperlinks'])) {
            $this->options['hyperlinks'] = '1';
        }

        if(!isset($this->options['disallow_logged_in_usernames'])) {
            $this->options['disallow_logged_in_usernames'] = '1';
        }

        if(!isset($this->options['timeout_refresh_users'])) {
            $this->options['timeout_refresh_users'] = self::default_timeout_refresh_users;
        }

        if(!isset($this->options['default_name'])) {
            $this->options['default_name'] = $this->default_name;
        }

        if(!isset($this->options['badwords_list'])) {
            $this->options['badwords_list'] = self::default_badwords_list;
        }

        if(!isset($this->options['guest_num_digits'])) {
            $this->options['guest_num_digits'] = self::default_guest_num_digits;
        }

        if(!isset($this->options['ip_blocklist'])) {
            $this->options['ip_blocklist'] = self::default_ip_blocklist;
        }

        if(!isset($this->options['disallow_usernames_list'])) {
            $this->options['disallow_usernames_list'] = self::default_disallow_usernames_list;
        }

        update_option('Shoutbox_db_version', self::default_db_version);
        update_option('Shoutbox_options', $this->options);
        $this->clear_cache();
    }

    public function update_db_check() {
        if ($this->db_version != self::default_db_version) {
            $this->install();
        }
    }

    protected function current_admin_url(){
        $url = get_admin_url() . basename($_SERVER['SCRIPT_FILENAME']);

        if(!empty($_SERVER['QUERY_STRING'])){
            $url .= '?'.$_SERVER['QUERY_STRING'];
        }

        return $url;
    }

    protected function clean_room_to_target($room, $target){
        global $wpdb;
        $Shoutbox_messages_table_name = $wpdb->prefix . 'Shoutbox_messages';

        $sql = $wpdb->prepare(
            'DELETE FROM '.$Shoutbox_messages_table_name.'
                WHERE id <= (
                SELECT id
                FROM (
                    SELECT id
                    FROM '.$Shoutbox_messages_table_name.'
                        WHERE room = %s AND alias != "Shoutbox"
                    ORDER BY id DESC
                    LIMIT 1 OFFSET %d
                ) foo
            )', $room, $target);

        $rows_affected = $wpdb->query($sql);

        return $rows_affected;
    }

    protected function filter($text, $replace_inside_words){
        if(isset($this->options['badwords_list']) && ($this->options['badwords_list'] != '')){
            $strings = explode(',', $this->options['badwords_list']);
            foreach($strings as $word){
                $word = trim($word);

                $replacement = str_repeat('*', strlen($word));

                if($replace_inside_words){
                    $text = str_ireplace($word, $replacement, $text);
                }
                else{
                    $text = preg_replace('/\b'.$word.'\b/i', $replacement, $text);
                }
            }
        }
        return $text;
    }

    protected function log($title, $code = null, $message = null){
        if(isset($this->options['debug_mode']) || (defined('WP_DEBUG') && WP_DEBUG)){
            $log_file_append = '['.gmdate('D, d M Y H:i:s \G\M\T').'] ' . $title;

            if($code !== null){
               $log_file_append .= ', code: ' . $code;
            }

            if($message !== null){
               $log_file_append .= ', message: ' . $message;
            }
            file_put_contents($this->log_file, $log_file_append . "\n", FILE_APPEND);
        }
    }
}
global $Shoutbox;
$Shoutbox = new Shoutbox();

require_once(dirname(__FILE__) . '/widgets.php');
?>