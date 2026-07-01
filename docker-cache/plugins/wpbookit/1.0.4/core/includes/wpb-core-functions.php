<?php

if (!defined('ABSPATH')) {
    exit;
}

require 'wpb-booking-functions.php';
require 'wpb-calendar-functions.php';
require 'wpb-bookingtypes-functions.php';
require 'wpb-customer-functions.php';
require 'wpb-booking-emails.php';
require 'wpb-booking-emails-hooks.php';
require 'wpb-import-functions.php';
require 'wpb-guest-users-functions.php';



/**
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $default_path  Default path. (default: '').
 */

function wpb_get_template($template_name, $args = array(), $default_path = '')
{


    if (empty($default_path)) :
        $default_path = IQWPB_PLUGIN_PATH . 'templates/';
    endif;


    $template = locate_template(
        array(
            trailingslashit('wpbookit') . $template_name,
            $template_name,
        )
    );

    if (empty($template)) :
        $template = $default_path . $template_name;
    endif;


    if (!file_exists($template)) :

        var_dump($template);

        return new WP_Error(
            'error',
            sprintf(
                // translators: Template Name placeholder:0 
                __('%s does not exist.','wpbookit'),
                '<code>' . $template . '</code>'
            )
        );
    endif;


    do_action('wpb_before_template_part', $template, $args, $default_path);

    if (!empty($args) && is_array($args)) :
        extract($args);
    endif;
    include $template;

    do_action('wpb_after_template_part', $template, $args, $default_path);
}

function wpb_get_pagination($totalPages, $paged, $current_tab = 1)
{

    $pagination = paginate_links(
        apply_filters(
            'wpb_paginate_links_args',
            array(
                'base'      => admin_url('admin.php?page=wpbookit-dashboard&tab=' . $current_tab . '&paged=%#%'),
                'format'    => '?paged=%#%',
                'prev_text' => __('&laquo;','wpbookit'),
                'next_text' => __('&raquo;','wpbookit'),
                'total'     => $totalPages,
                'current'   => $paged,
                'mid_size'  => 3,
                'end_size'  => 2,
                'prev_next' => true,
                'type'      => 'array',
                'echo'      => false,
            ),
            $current_tab,
            $totalPages,
            $paged
        )
    );

    $output = '';
    if (is_array($pagination) && !empty($pagination)) :
        $output .= '<div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">';
        $output .= '<ul class="pagination justify-content-md-end justify-content-center">';

        foreach ($pagination as $page_link) :
            // Add 'page-link' class to the pagination links
            $page_link = str_replace('page-numbers', 'page-numbers page-link', $page_link);
            if (strpos($page_link, 'current') !== false) :
                $page_link = str_replace('current', 'current active', $page_link);
            endif;

            $output .= '<li class="paginate_button page-item">';
            $output .= wp_kses_post($page_link);
            $output .= '</li>';
        endforeach;

        $output .= '</ul>';
        $output .= '</div>';
    endif;
    return apply_filters('wpb_get_pagination', $output, $totalPages, $paged, $current_tab);
}
function wpb_get_payment_pagination($total_pages, $paged, $range = 2)
{

    ob_start(); ?>
    <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
        <ul class="pagination justify-content-md-end justify-content-center">

            <?php if (1 !== (int) $paged) : ?>
                <li class="paginate_button page-item previous" data-id="<?php echo esc_attr($paged - 1); ?>" id="datatable_previous">
                    <a aria-controls="datatable" aria-disabled="true" role="link" data-dt-idx="previous" tabindex="-1" class="page-link">
                        <span class="prev-icon">«</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php
            if ($total_pages > 1) :
                for ($i = 1; $i <= $total_pages; $i++) :
                    if ($i == 1 || $i == $total_pages || ($i >= $paged - $range && $i <= $paged + $range)) :
                        $active = $i == (int)$paged ? ' active' : '';  ?>
                        <li data-id="<?php echo esc_attr($i); ?>" class="<?php echo esc_attr('paginate_button page-item' . $active); ?>">
                            <a href="#" aria-controls="datatable" role="link" aria-current="page" data-dt-idx="<?php echo esc_attr($i - 1); ?>" tabindex="0" class="page-link"><?php echo esc_html($i); ?></a>
                        </li>

                    <?php elseif ($i == $paged - $range - 1 || $i == $paged + $range + 1) : ?>
                        <li class="paginate_button page-item disabled"><span class="page-link">&hellip;</span></li>

            <?php endif;
                endfor;
            endif;
            ?>
            <?php if (((int) $paged !== (int) $total_pages) && (0 != $total_pages) ) : ?>
                <li class="paginate_button page-item next" data-id="<?php echo esc_attr($paged + 1); ?>" id="datatable_next">
                    <a aria-controls="datatable" aria-disabled="true" role="link" data-dt-idx="next" tabindex="-1" class="page-link">
                        <span class="next-icon">»</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php
    return ob_get_clean();
}
function wpbookit_avatar($user_id, $class, $return_link = false)
{
    $avatar_id = get_user_meta($user_id, 'wp_user_avatar', true);

    $avatar_url = wp_get_attachment_image_url($avatar_id);
    
    $avatar_url = $avatar_url === false ? get_avatar_url(0, ['size' => 50]) : $avatar_url;

    if ($return_link == true) {
        return $avatar_url;
    }

    $avatar_image = wp_get_attachment_image($avatar_id, '', false, array('class' => $class)); //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
    
    if ($avatar_image) {
        return $avatar_image;
    }
    
    return get_avatar($user_id, 42, '', '', ['class' => $class]);
}





function wpb_mailer($to = false, $subject = '', $message = '')
{
    if (!$to)
        return false;

    add_filter('wp_mail_content_type', 'wpb_set_html_content_type');

    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    wp_mail($to, $subject, $message, $headers);
    remove_filter('wp_mail_content_type', 'wpb_set_html_content_type');
}

function wpb_set_html_content_type()
{
    return 'text/html';
}

function wpb_get_formated_date_time($date, $time = ''){

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');
    
    if ($time) {
        $date_format = $date_format . ' ' . $time_format;
    }

    $datetime_string = $date . ' ' . $time;

    $timezone = get_option('timezone_string');
    if (!empty($timezone)) {
        date_default_timezone_set($timezone);  //phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
    } 

    $timestamp = strtotime($datetime_string);
    // Reset the timezone to UTC
    date_default_timezone_set('UTC'); //phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set

    $date_time = wp_date($date_format, $timestamp);
    // $datetime_string = new DateTime($datetime_string);
    // $date_time = $datetime_string->format($date_format);
    return apply_filters('wpb_wordpress_formated_date_time', $date_time);
}
function wpb_get_formated_date($date){

    $date_format = get_option('date_format');
    $datetime_string = $date ;

    $timezone = get_option('timezone_string');
    if (!empty($timezone)) {
        date_default_timezone_set($timezone); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
    } 
    $timestamp = strtotime($datetime_string); 
    // Reset the timezone to UTC 
    date_default_timezone_set('UTC'); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set

    $date_time = wp_date($date_format, $timestamp);

    return apply_filters('wpb_wordpress_formated_date', $date_time);
}

function wpb_get_formated_time($time){

    $time_format = get_option('time_format');
    $date_format = $time_format;

    $timezone = get_option('timezone_string');
    if (!empty($timezone)) {
        date_default_timezone_set($timezone); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
    } 

    $timestamp = strtotime($time);
    // Reset the timezone to UTC
    date_default_timezone_set('UTC'); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set

    $date_time = wp_date($date_format, $timestamp);
    return apply_filters('wpb_wordpress_formated_date_time', $date_time);
}
function wpb_get_prefix_postfix_price($price,$only_prefix=true,$only_postfix=true) {
    if($price == esc_html__("Free", 'wpbookit')){
        return esc_html__("Free", 'wpbookit');
    }
    $wpb_prefix = wpb_get_general_settings()['prefix'] ?? '';
    $wpb_postfix = wpb_get_general_settings()['postfix'] ?? '';

    $formatted_price= $price;
    if($only_prefix){
        $formatted_price = $wpb_prefix . $formatted_price;
    }
    if($only_postfix){
        $formatted_price = $formatted_price . $wpb_postfix;
    }

    return apply_filters('wpb_general_setting_price_val', $formatted_price, $price);
}

function get_first_wpb_profile_page_url() {
    global $wpdb;

    // Query to search for the [wpb-profile] shortcode in pages
    $query = "
        SELECT ID
        FROM $wpdb->posts
        WHERE post_type = 'page'
        AND post_status = 'publish'
        AND post_content LIKE '%[wpb-profile]%'
        LIMIT 1
    ";

    $page_id = $wpdb->get_var($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 

    if ($page_id) {
        return get_permalink($page_id);
    } else {
        return admin_url();
    }
}

function get_email_reminder_value($emails_title) {
    global $wpdb;

    $sql = "SELECT reminder FROM $wpdb->wpb_booking_emails";
    $parameters = array();

    $sql .= " WHERE emails_title = %s";
    $parameters[] = $emails_title;

    if (!empty($parameters)) {
        $prepared_sql = $wpdb->prepare($sql, $parameters); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
    } else {
        $prepared_sql = $sql;
    }

    return $wpdb->get_var($prepared_sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
}

function wpb_get_general_settings(){
    $general_setting = get_option('wpb_general_setting_data');
    return apply_filters(
        'wpb_general_setting_data',
        $general_setting
    );
}

function wpb_get_theme_settings($key = 'all'){
    if($key === 'all'){
        $theme_setting = get_option('wpb_theme_setting_data');
    }else{
        $theme_setting = get_option('wpb_theme_setting_data')[$key] ?? '';
    }
    return apply_filters(
        'wpb_theme_setting_data',
        $theme_setting
    );
}

/**
 * Truncate a given text to a specified length without breaking words and append an ellipsis.
 *
 * @param string $text The text to be truncated.
 * @param int $length The desired length of the excerpt.
 * @return string The truncated text with an ellipsis if it exceeds the specified length.
 */
function wpb_get_excerpt($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    $excerpt = substr($text, 0, $length);
    $last_space = strrpos($excerpt, ' ');
    if ($last_space !== false) {
        $excerpt = substr($excerpt, 0, $last_space);
    }
    return $excerpt . '...';
}

function wpb_get_email_template_dynamic_keys($key)
{
        $data =  apply_filters('wpb_email_template_dynamic_keys',[
        'customer_booking_reminder' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
            '{{meeting_url}}'
        ],
        'customer_registration' => [
            '{{customer_name}}',
            '{{customer_email}}',
            '{{login_url}}',
            '{{password}}'
        ],
        'staff_registration' => [
            '{{customer_name}}',
            '{{customer_email}}',
            '{{staff_login}}',
            '{{password}}'
        ],
        'customer_booking_confirmation' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
            '{{meeting_url}}'
        ],
        'customer_booking_approval' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
            '{{meeting_url}}'
        ],
        'customer_booking_cancellation' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
        ],
        'staff_booking_reminder' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
            '{{meeting_url}}'
        ],
        'staff_booking_request' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
        ],
        'staff_booking_cancellation' => [
            '{{customer_name}}',
            '{{booking_type}}',
            '{{booking_status}}',
            '{{booking_date}}',
            '{{booking_time}}',
            '{{staff_name}}',
        ]
    ]);
    
   return  isset($data[$key]) ? $data[$key] : [];
}

function wpb_booking_reminder($booking_id)
{
    $customer_email_data = wpb_get_email_data('Customer Booking Reminder');
    $customer_email_status = $customer_email_data->status;
    if($customer_email_status ){

        $booking = wpb_get_booking($booking_id);
        $booking_date = $booking->get_booking_date();
        $time_slot = $booking->get_timeslot();
        $booking_datetime_string = $booking_date . ' ' . $time_slot;
    

        $site_timezone = new DateTimeZone(wpb_get_timezone());
    
        // Create the DateTime object in the site's timezone
        $booking_datetime = new DateTime($booking_datetime_string, $site_timezone);
    
        // Convert to UTC
        $booking_datetime->setTimezone(new DateTimeZone('UTC'));
        $booking_timestamp = $booking_datetime->getTimestamp();
        
        if($customer_email_status){
            
            $customer_reminder_value = $customer_email_data->reminder;
            $adjusted_timestamp = $booking_timestamp - $customer_reminder_value;

            // Schedule the event
            wp_schedule_single_event($adjusted_timestamp, 'wpb_customer_booking_reminder', [(int) $booking_id]);
        }
    }
}
function get_wpbookit_currencies() {
	static $currencies;

	if ( ! isset( $currencies ) ) {
		$currencies = array_unique(
			apply_filters(
				'wpbookit_currencies',
				array(
					'AED' => __( 'United Arab Emirates dirham', 'wpbookit' ),
					'AFN' => __( 'Afghan afghani', 'wpbookit' ),
					'ALL' => __( 'Albanian lek', 'wpbookit' ),
					'AMD' => __( 'Armenian dram', 'wpbookit' ),
					'ANG' => __( 'Netherlands Antillean guilder', 'wpbookit' ),
					'AOA' => __( 'Angolan kwanza', 'wpbookit' ),
					'ARS' => __( 'Argentine peso', 'wpbookit' ),
					'AUD' => __( 'Australian dollar', 'wpbookit' ),
					'AWG' => __( 'Aruban florin', 'wpbookit' ),
					'AZN' => __( 'Azerbaijani manat', 'wpbookit' ),
					'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'wpbookit' ),
					'BBD' => __( 'Barbadian dollar', 'wpbookit' ),
					'BDT' => __( 'Bangladeshi taka', 'wpbookit' ),
					'BGN' => __( 'Bulgarian lev', 'wpbookit' ),
					'BHD' => __( 'Bahraini dinar', 'wpbookit' ),
					'BIF' => __( 'Burundian franc', 'wpbookit' ),
					'BMD' => __( 'Bermudian dollar', 'wpbookit' ),
					'BND' => __( 'Brunei dollar', 'wpbookit' ),
					'BOB' => __( 'Bolivian boliviano', 'wpbookit' ),
					'BRL' => __( 'Brazilian real', 'wpbookit' ),
					'BSD' => __( 'Bahamian dollar', 'wpbookit' ),
					'BTC' => __( 'Bitcoin', 'wpbookit' ),
					'BTN' => __( 'Bhutanese ngultrum', 'wpbookit' ),
					'BWP' => __( 'Botswana pula', 'wpbookit' ),
					'BYR' => __( 'Belarusian ruble (old)', 'wpbookit' ),
					'BYN' => __( 'Belarusian ruble', 'wpbookit' ),
					'BZD' => __( 'Belize dollar', 'wpbookit' ),
					'CAD' => __( 'Canadian dollar', 'wpbookit' ),
					'CDF' => __( 'Congolese franc', 'wpbookit' ),
					'CHF' => __( 'Swiss franc', 'wpbookit' ),
					'CLP' => __( 'Chilean peso', 'wpbookit' ),
					'CNY' => __( 'Chinese yuan', 'wpbookit' ),
					'COP' => __( 'Colombian peso', 'wpbookit' ),
					'CRC' => __( 'Costa Rican col&oacute;n', 'wpbookit' ),
					'CUC' => __( 'Cuban convertible peso', 'wpbookit' ),
					'CUP' => __( 'Cuban peso', 'wpbookit' ),
					'CVE' => __( 'Cape Verdean escudo', 'wpbookit' ),
					'CZK' => __( 'Czech koruna', 'wpbookit' ),
					'DJF' => __( 'Djiboutian franc', 'wpbookit' ),
					'DKK' => __( 'Danish krone', 'wpbookit' ),
					'DOP' => __( 'Dominican peso', 'wpbookit' ),
					'DZD' => __( 'Algerian dinar', 'wpbookit' ),
					'EGP' => __( 'Egyptian pound', 'wpbookit' ),
					'ERN' => __( 'Eritrean nakfa', 'wpbookit' ),
					'ETB' => __( 'Ethiopian birr', 'wpbookit' ),
					'EUR' => __( 'Euro', 'wpbookit' ),
					'FJD' => __( 'Fijian dollar', 'wpbookit' ),
					'FKP' => __( 'Falkland Islands pound', 'wpbookit' ),
					'GBP' => __( 'Pound sterling', 'wpbookit' ),
					'GEL' => __( 'Georgian lari', 'wpbookit' ),
					'GGP' => __( 'Guernsey pound', 'wpbookit' ),
					'GHS' => __( 'Ghana cedi', 'wpbookit' ),
					'GIP' => __( 'Gibraltar pound', 'wpbookit' ),
					'GMD' => __( 'Gambian dalasi', 'wpbookit' ),
					'GNF' => __( 'Guinean franc', 'wpbookit' ),
					'GTQ' => __( 'Guatemalan quetzal', 'wpbookit' ),
					'GYD' => __( 'Guyanese dollar', 'wpbookit' ),
					'HKD' => __( 'Hong Kong dollar', 'wpbookit' ),
					'HNL' => __( 'Honduran lempira', 'wpbookit' ),
					'HRK' => __( 'Croatian kuna', 'wpbookit' ),
					'HTG' => __( 'Haitian gourde', 'wpbookit' ),
					'HUF' => __( 'Hungarian forint', 'wpbookit' ),
					'IDR' => __( 'Indonesian rupiah', 'wpbookit' ),
					'ILS' => __( 'Israeli new shekel', 'wpbookit' ),
					'IMP' => __( 'Manx pound', 'wpbookit' ),
					'INR' => __( 'Indian rupee', 'wpbookit' ),
					'IQD' => __( 'Iraqi dinar', 'wpbookit' ),
					'IRR' => __( 'Iranian rial', 'wpbookit' ),
					'IRT' => __( 'Iranian toman', 'wpbookit' ),
					'ISK' => __( 'Icelandic kr&oacute;na', 'wpbookit' ),
					'JEP' => __( 'Jersey pound', 'wpbookit' ),
					'JMD' => __( 'Jamaican dollar', 'wpbookit' ),
					'JOD' => __( 'Jordanian dinar', 'wpbookit' ),
					'JPY' => __( 'Japanese yen', 'wpbookit' ),
					'KES' => __( 'Kenyan shilling', 'wpbookit' ),
					'KGS' => __( 'Kyrgyzstani som', 'wpbookit' ),
					'KHR' => __( 'Cambodian riel', 'wpbookit' ),
					'KMF' => __( 'Comorian franc', 'wpbookit' ),
					'KPW' => __( 'North Korean won', 'wpbookit' ),
					'KRW' => __( 'South Korean won', 'wpbookit' ),
					'KWD' => __( 'Kuwaiti dinar', 'wpbookit' ),
					'KYD' => __( 'Cayman Islands dollar', 'wpbookit' ),
					'KZT' => __( 'Kazakhstani tenge', 'wpbookit' ),
					'LAK' => __( 'Lao kip', 'wpbookit' ),
					'LBP' => __( 'Lebanese pound', 'wpbookit' ),
					'LKR' => __( 'Sri Lankan rupee', 'wpbookit' ),
					'LRD' => __( 'Liberian dollar', 'wpbookit' ),
					'LSL' => __( 'Lesotho loti', 'wpbookit' ),
					'LYD' => __( 'Libyan dinar', 'wpbookit' ),
					'MAD' => __( 'Moroccan dirham', 'wpbookit' ),
					'MDL' => __( 'Moldovan leu', 'wpbookit' ),
					'MGA' => __( 'Malagasy ariary', 'wpbookit' ),
					'MKD' => __( 'Macedonian denar', 'wpbookit' ),
					'MMK' => __( 'Burmese kyat', 'wpbookit' ),
					'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'wpbookit' ),
					'MOP' => __( 'Macanese pataca', 'wpbookit' ),
					'MRU' => __( 'Mauritanian ouguiya', 'wpbookit' ),
					'MUR' => __( 'Mauritian rupee', 'wpbookit' ),
					'MVR' => __( 'Maldivian rufiyaa', 'wpbookit' ),
					'MWK' => __( 'Malawian kwacha', 'wpbookit' ),
					'MXN' => __( 'Mexican peso', 'wpbookit' ),
					'MYR' => __( 'Malaysian ringgit', 'wpbookit' ),
					'MZN' => __( 'Mozambican metical', 'wpbookit' ),
					'NAD' => __( 'Namibian dollar', 'wpbookit' ),
					'NGN' => __( 'Nigerian naira', 'wpbookit' ),
					'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'wpbookit' ),
					'NOK' => __( 'Norwegian krone', 'wpbookit' ),
					'NPR' => __( 'Nepalese rupee', 'wpbookit' ),
					'NZD' => __( 'New Zealand dollar', 'wpbookit' ),
					'OMR' => __( 'Omani rial', 'wpbookit' ),
					'PAB' => __( 'Panamanian balboa', 'wpbookit' ),
					'PEN' => __( 'Sol', 'wpbookit' ),
					'PGK' => __( 'Papua New Guinean kina', 'wpbookit' ),
					'PHP' => __( 'Philippine peso', 'wpbookit' ),
					'PKR' => __( 'Pakistani rupee', 'wpbookit' ),
					'PLN' => __( 'Polish z&#x142;oty', 'wpbookit' ),
					'PRB' => __( 'Transnistrian ruble', 'wpbookit' ),
					'PYG' => __( 'Paraguayan guaran&iacute;', 'wpbookit' ),
					'QAR' => __( 'Qatari riyal', 'wpbookit' ),
					'RON' => __( 'Romanian leu', 'wpbookit' ),
					'RSD' => __( 'Serbian dinar', 'wpbookit' ),
					'RUB' => __( 'Russian ruble', 'wpbookit' ),
					'RWF' => __( 'Rwandan franc', 'wpbookit' ),
					'SAR' => __( 'Saudi riyal', 'wpbookit' ),
					'SBD' => __( 'Solomon Islands dollar', 'wpbookit' ),
					'SCR' => __( 'Seychellois rupee', 'wpbookit' ),
					'SDG' => __( 'Sudanese pound', 'wpbookit' ),
					'SEK' => __( 'Swedish krona', 'wpbookit' ),
					'SGD' => __( 'Singapore dollar', 'wpbookit' ),
					'SHP' => __( 'Saint Helena pound', 'wpbookit' ),
					'SLL' => __( 'Sierra Leonean leone', 'wpbookit' ),
					'SOS' => __( 'Somali shilling', 'wpbookit' ),
					'SRD' => __( 'Surinamese dollar', 'wpbookit' ),
					'SSP' => __( 'South Sudanese pound', 'wpbookit' ),
					'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'wpbookit' ),
					'SYP' => __( 'Syrian pound', 'wpbookit' ),
					'SZL' => __( 'Swazi lilangeni', 'wpbookit' ),
					'THB' => __( 'Thai baht', 'wpbookit' ),
					'TJS' => __( 'Tajikistani somoni', 'wpbookit' ),
					'TMT' => __( 'Turkmenistan manat', 'wpbookit' ),
					'TND' => __( 'Tunisian dinar', 'wpbookit' ),
					'TOP' => __( 'Tongan pa&#x2bb;anga', 'wpbookit' ),
					'TRY' => __( 'Turkish lira', 'wpbookit' ),
					'TTD' => __( 'Trinidad and Tobago dollar', 'wpbookit' ),
					'TWD' => __( 'New Taiwan dollar', 'wpbookit' ),
					'TZS' => __( 'Tanzanian shilling', 'wpbookit' ),
					'UAH' => __( 'Ukrainian hryvnia', 'wpbookit' ),
					'UGX' => __( 'Ugandan shilling', 'wpbookit' ),
					'USD' => __( 'United States (US) dollar', 'wpbookit' ),
					'UYU' => __( 'Uruguayan peso', 'wpbookit' ),
					'UZS' => __( 'Uzbekistani som', 'wpbookit' ),
					'VEF' => __( 'Venezuelan bol&iacute;var (2008–2018)', 'wpbookit' ),
					'VES' => __( 'Venezuelan bol&iacute;var', 'wpbookit' ),
					'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'wpbookit' ),
					'VUV' => __( 'Vanuatu vatu', 'wpbookit' ),
					'WST' => __( 'Samoan t&#x101;l&#x101;', 'wpbookit' ),
					'XAF' => __( 'Central African CFA franc', 'wpbookit' ),
					'XCD' => __( 'East Caribbean dollar', 'wpbookit' ),
					'XOF' => __( 'West African CFA franc', 'wpbookit' ),
					'XPF' => __( 'CFP franc', 'wpbookit' ),
					'YER' => __( 'Yemeni rial', 'wpbookit' ),
					'ZAR' => __( 'South African rand', 'wpbookit' ),
					'ZMW' => __( 'Zambian kwacha', 'wpbookit' ),
				)
			)
		);
	}

	return $currencies;
}

function wpb_get_timezone(){
    // Step 1: Retrieve the timezone setting from WordPress
    $timezone_string = get_option('timezone_string');

    // If the timezone string is empty, fall back to the offset
    if (empty($timezone_string)) {
        $gmt_offset = get_option('gmt_offset');
        $timezone_string = timezone_name_from_abbr('', (int)$gmt_offset * 3600, 0);
    }

    return $timezone_string;
}

function wpb_render_filtered_svg($icon = false){
    if(!$icon){
        return null;
    }

    $icon_set = [
        'physical_address' => 'core/admin/assets/images/Location.svg',
        'online_video' => 'core/admin/assets/images/video-on.svg',
        'phone_number' => 'core/admin/assets/images/Call.svg',
        'payment-fail' => 'core/admin/assets/images/payment-unsuccessful.svg',
        'globe-03' => 'core/admin/assets/images/globe-03.svg',
        'import' => 'core/admin/assets/images/downlaod.svg',
        'upload' => 'core/admin/assets/images/upload.svg',
        'double-check' => 'core/admin/assets/images/double-check.svg',
        'spinner' => 'core/admin/assets/images/spinner.svg',
        'phone-icon' => 'core/admin/assets/images/phone-icon.svg',
        'users-profiles-plus' => 'core/admin/assets/images/users-profiles-plus.svg',
        'list' => 'core/admin/assets/images/list.svg',
        'arrow-up-right' => 'core/admin/assets/images/arrow-up-right.svg',
        'wordpress-logo' => 'core/admin/assets/images/wordpress-logo.svg',
        'edit' => 'core/admin/assets/images/edit.svg',
        'delete' => 'core/admin/assets/images/delete-without-color.svg',
        'message' => 'core/admin/assets/images/message.svg',
        'profile' => 'core/admin/assets/images/profile.svg',
        'chevron-down' => 'core/admin/assets/images/chevron-down.svg'
    ];
    return file_get_contents(IQWPB_PLUGIN_PATH.$icon_set[$icon]); // phpcs:ignore  WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents  
}

function wpb_print_booking_type_location($location){
    if (!filter_var($location, FILTER_VALIDATE_URL)){
        return $location;
    }

    ob_start();
    ?>
    <a href="<?php echo esc_url($location) ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_url($location) ?></a>
    <?php

    return ob_get_clean(); 
    
}
function wpb_clear_pagination_link($link,$new_param){
    // Get the URL for the previous page with modified query parameters
    $prev_page_url_parts = wp_parse_url($link);

    // Remove existing query parameters
    $query_args = array();

    // Add your new query parameter
    $query_args = [$new_param=>""];

    // Reconstruct the URL
    return  esc_url(add_query_arg($query_args, $prev_page_url_parts['scheme'] . '://' . $prev_page_url_parts['host'] . $prev_page_url_parts['path']));

}

function wpb_return_zero(){
    return '0';
}
function wpb_get_active_payment_gateways(){
    $active_payment= array_intersect_key(get_option('wpb_payment_gateways',[]),apply_filters('wpb_booking_shortcode_active_payment_gateway',[]));

    return array_filter($active_payment,function($gateway){
       return isset($gateway['status']) && $gateway['status'] == 'true';
    });

}

function wpb_get_available_payment_gateways(){
    return apply_filters('wpb_booking_shortcode_active_payment_gateway',[]);
}
function wpb_get_payment_gateway_name($payment_gateway) {
    return apply_filters('wpb_booking_shortcode_active_payment_gateway',[])[$payment_gateway]??null;
}

function wpb_get_available_telemed() {
    return apply_filters('wpb_get_available_telemed',[])??false;
}
function wpb_update_fields_data_type($table_name, $new_fields)
{
    global $wpdb;
    foreach ($new_fields as $key => $nf) {
        $new_field = "ALTER TABLE `{$table_name}` ADD COLUMN `{$key}` {$nf};";
        maybe_add_column( $table_name, $key, $new_field );
    }
}
function wpb_append_class_base_on_rtl($ltr="",$rtl=""){
    return is_rtl()?$rtl:$ltr;
}
function wpb_render_pro_lable($ltr="",$rtl=""){
    ?>
    <a href="https://codecanyon.net/item/wpbookit-appointment-booking-calendar-for-wordpress/52836302" target="_blank" class="upgrade-pro-label"><?php esc_html_e('Upgrade Pro','wpbookit')?></a>
    <?php
}


function wpb_get_country_name_from_timezone($timezone) {
    // Create a new DateTimeZone object
    $tz = new DateTimeZone($timezone);
    
    // Get the location information from the timezone
    $location = $tz->getLocation();
    
    // Get the country code from the location information
    $countryCode = $location['country_code'];

    if( class_exists( 'Locale' ) ) :
        // Get the country name using the country code and Intl extension
        $countryName = (new Locale)->getDisplayRegion('-' . $countryCode, 'en');
    else :
        return $countryCode;
    endif;
    
    return $countryName;
}
function wpb_unicode_to_utf8($str) {
    // Replace 'u' with '\u' for json_decode compatibility
    $str = preg_replace('/u([0-9a-fA-F]{4})/', '\\u$1', $str);

    // Decode the Unicode string into proper UTF-8 characters
    return json_decode('"' . $str . '"');
}
function wpb_iq_get_booking_redirect_url(WPB_Booking $booking_instance,WPB_Booking_Type $booking_type_instance){
    return apply_filters('wpb_booking_redirect_link',add_query_arg(array(
        'booking_confirmation' => $booking_instance->get_id(),
    ), $booking_type_instance->get_bookingtype_permalink()));
}

/**
 * Logs an error message to the debug log file.
 *
 * Logs an error message to the WordPress debug log file. The log file location is determined
 * by the WP_DEBUG_LOG constant. If WP_DEBUG_LOG is not defined, the default WordPress debug
 * log file will be used.
 *
 * @param mixed  $error        The error message to log. If not scalar, it will be converted to a string.
 * @param string $message_type The message type (e.g., 'error', 'warning').
 */
function wpb_error_log( mixed $error, string $message_type = 'error' ): void {
    // Check if the error is a array or object, if not convert it to a string representation.
    if ( is_array($error) || is_object($error) ) {
        $error = print_r($error, true);
    }

    // Create the log message with the current time and error message.
    $message = current_time('Y-m-d H:i:s') . ' [' . $message_type . '] ' . $error . PHP_EOL;

    // Determine the debug log path based on WP_DEBUG_LOG setting.
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        $debug_log_path = is_string(WP_DEBUG_LOG) ? WP_DEBUG_LOG : ABSPATH . 'wp-content/debug.log';
        @error_log($message, 3, $debug_log_path);
    } else {
        // If WP_DEBUG_LOG is not set, log the message using the default error_log handler.
        @error_log($message);
    }
}