<?php
/**
 * @package Canto
 * @version 2.0.0
 */

// define( 'WP_ADMIN', false );
// define( 'WP_LOAD_IMPORTERS', false );
$error = null;


/*
 * Detect Mime type for createBlock type and insertBlocks
 */

function canto_check_mime_types($haystack, $needle)
{
    $mime_types = [];
    $found = false;
    foreach ($haystack as $key => $value) {
        if (strstr($key, "|")) {
            $keys = explode("|", $key);
            foreach ($keys as $k) {
                array_push($mime_types, strtolower($k));
            }
        } else {
            array_push($mime_types, strtolower($key));
        }
    }
    return in_array(strtolower($needle), $mime_types);
}

function canto_curl_action($url, $echo)
{
    $post_fbc_app_token = sanitize_text_field($_POST['fbc_app_token']);
    $header = array(
        'Authorization' => 'Bearer ' . $post_fbc_app_token,
        'user-agent' => 'Wordpress Plugin',
        'Referer' => 'Wordpress Plugin',
    );

    $response = wp_remote_get($url,
        array(
            'method' => 'GET',
            'headers' => $header,
            'timeout' => 120,
        )
    );

    $output = wp_remote_retrieve_body($response);

    return $output;
}

require_once(urldecode($_POST['abspath']) . 'wp-admin/admin.php');

if (!function_exists('wp_handle_upload')) {
    require_once($_POST['abspath'] . 'wp-admin/includes/file.php');
}

// Data Must be Sanitized, Escaped, and Validated
$post_description = sanitize_text_field($_POST['description']);
$post_alt = sanitize_text_field($_POST['alt']);
$post_copyright = sanitize_text_field($_POST['copyright']);
$post_terms = sanitize_text_field($_POST['terms']);
$post_fbc_id = sanitize_text_field($_POST['fbc_id']);
$post_fbc_scheme = sanitize_text_field($_POST['fbc_scheme']);

$send_id = sanitize_text_field($_POST['post_id']);
$post_fbc_flight_domain = sanitize_text_field($_POST['fbc_flight_domain']);
$post_fbc_app_api = sanitize_text_field($_POST['fbc_app_api']);
$post_caption = sanitize_text_field($_POST['caption']);
//$post_abspath = sanitize_text_field($_POST["abspath"]);
$post_title = sanitize_text_field($_POST['title']);
$post_size = sanitize_text_field($_POST['size']);
$post_align = sanitize_text_field($_POST['align']);
$post_link = sanitize_text_field($_POST['link']);
$post_chromeless = sanitize_text_field($_POST['chromeless']);

global $post;

$attachment = $post_fbc_id;
$id = $send_id;

//Go get the media item from Flight
$flight['api_url'] = 'https://' . $post_fbc_flight_domain . '.' . $post_fbc_app_api . '/api/v1/';
$flight['req'] = $flight['api_url'] . $post_fbc_scheme . '/' . $post_fbc_id;


//	$instance = Canto::instance();
$response = canto_curl_action($flight['req'], 0);
$response = (json_decode($response));


/* Check if mime_type is allowed */
$allowed_mime_types = get_allowed_mime_types();
$uploaded_file_type = pathinfo($response->name);

if (!canto_check_mime_types($allowed_mime_types, $uploaded_file_type['extension'])) {
    $error = "This file type is not allowed by your Wordpress theme. Please contact your theme developer to allow support of this file type: '" . $uploaded_file_type['extension'] . "'";
}

if ($error === null) :

    //Get the download url
    $detail = $response->url->download;
    $detail = $detail . '/directuri';
    $detail = canto_curl_action($detail, 1);
    $location = trim($detail);

    $tmp = download_url($location);
    $file_array = array(
        'name' => $response->name,
        'tmp_name' => $tmp
    );

    // Check for download errors
    if (is_wp_error($tmp)) {
        error_log("error-----------" . json_encode($tmp->errors));
//        @unlink($file_array['tmp_name']);
//        $error = $tmp;
        $results = array("error" => $tmp->errors);
        exit(json_encode($results));
    }

    $post_data = array(
        'post_content' => $post_description,
        'post_excerpt' => $post_caption,
    );

    if (!empty($post_title)) {
        $post_data['post_title'] = $post_title;
    } else {
        $post_data['post_title'] = basename($file_array['name']);
    }


    /**
     * Check for Duplicates (existing images imported from Canto)
     */
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_query' => array(
            array(
                'key' => 'fbc_id',
                'value' => $post_fbc_id
            )
        )
    );
    $query = new WP_Query($args);
    $posts = $query->posts;
    if ($posts && get_option('fbc_duplicates') === "true") {
        $id = $posts[0]->ID;

        update_post_meta($id, '_wp_attachment_image_alt', $post_alt);
        update_post_meta($id, 'description', $post_description);
        update_post_meta($id, 'copyright', $post_copyright);
        update_post_meta($id, 'terms', $post_terms);
        update_post_meta($id, 'fbc_id', $post_fbc_id);
        update_post_meta($id, 'fbc_scheme', $post_fbc_scheme);


        $guid = explode("/", $posts[0]->guid);
        $file = array_pop($guid);
        $meta = wp_get_attachment_metadata($id);
        $uploads = wp_upload_dir();
        $dir_path = $uploads['path'];
//        $file_sub = $uploads['subdir'];
        $dir_path_file =  $dir_path . '/' . $file;

        if (file_exists($dir_path_file)) {
            unlink($dir_path_file);
        }

        if (is_array($meta)) {
            foreach ($meta["sizes"] as $size) {
                $fileName = $size["file"];
                // Create array with all old sizes for replacing in posts later
                // Look for files and delete them
                if (strlen($fileName)) {
                    $fp = $dir_path . '/' . $size["file"];
                    if (file_exists($fp)) {
                        unlink($fp);
                    }
                }
            }
        }

        // Copy file for Wordpress media access
        $copy = copy($tmp, $dir_path_file);
//        $file_perms = fileperms($dir_path_file) & 0777;
//        $chmod = @chmod($dir_path_file, $file_perms);
        $chmod = @chmod($dir_path_file, 0755);

        // Make thumb and/or update metadata
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $dir_path_file));
        update_attached_file($id, $dir_path_file);
        if (file_exists($file_array['tmp_name'])) {
            unlink($file_array['tmp_name']);
        }
    } else {
        $id = media_handle_sideload($file_array, $send_id, '', $post_data);
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            $results = array("error" => $id->errors['upload_error'][0]);
//            $results = array("error" => json_encode($id));
            exit(json_encode($results));
//            return $id;
        }
        add_post_meta($id, '_wp_attachment_image_alt', $post_alt);
        add_post_meta($id, 'description', $post_description);
        add_post_meta($id, 'copyright', $post_copyright);
        add_post_meta($id, 'terms', $post_terms);
        add_post_meta($id, 'fbc_id', $post_fbc_id);
        add_post_meta($id, 'fbc_scheme', $post_fbc_scheme);
    }

    $attachment_url = wp_get_attachment_url($id);
    $rel = $url = '';
    $html = $title = $post_title ?? '';

    //Create the link to section here.

    $align = $post_align ?? 'none';
    $size = $post_size ?? 'medium';
    $alt = $post_alt ?? '';
    $caption = $post_caption ?? '';
    $title = ''; // We no longer insert title tags into <img> tags, as they are redundant.
    $html = get_image_send_to_editor($id, $caption, $title, $align, $url, (bool)$rel, $size, $alt);


    $attachment = array();
    $attachment['post_title'] = $post_title;
    $attachment['post_excerpt'] = $post_caption;
    $attachment['image-size'] = $post_size;
    $attachment['image_alt'] = $post_alt;
    $attachment['align'] = $post_align;
    $attachment['description'] = $post_description;
    $attachment['copyright'] = $post_copyright;
    $attachment['terms'] = $post_terms;

    if ($post_link != "none")
        $attachment['url'] = $attachment_url;
    else
        $attachment['url'] = '';

    //This filter is documented in wp-admin/includes/media.php
    $html = apply_filters('media_send_to_editor', $html, $id, $attachment);

    // replace wp-image-<id>, wp-att-<id> and attachment_<id>
    $html = preg_replace(
        array(
            '#(caption id="attachment_)(\d+")#', // mind the quotes!
            '#(wp-image-|wp-att-)(\d+)#'
        ),
        array(
            sprintf('${1}nsm_%s_${2}', esc_attr($send_id)),
            sprintf('${1}nsm-%s-${2}', esc_attr($send_id)),
        ),
        $html
    );

    $attachment_id = $id;

    $mime_string = get_post_mime_type($attachment_id);
    $mime_type = explode('/', $mime_string);

    $results = array(
        "attachment" => $html,
        "attachment_id" => $attachment_id,
        "attachment_url" => $attachment_url,
        "attachment_mime" => $mime_type[0]
    );


endif; //End Error for $allowed_mime_types


if (isset($post_chromeless) && $post_chromeless) {
    if ($error === null) {
        exit(json_encode($results));
    } else {
        $results = array("error" => $error);
        exit(json_encode($results));
    }
} else {
    return media_send_to_editor(json_encode($results));
}
