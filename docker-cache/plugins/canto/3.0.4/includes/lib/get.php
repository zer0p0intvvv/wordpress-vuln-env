<?php
//error_reporting(E_ALL ^ E_DEPRECATED);
//ini_set("display_errors", 0);

require_once($_REQUEST['wp_abspath'] . '/wp-admin/admin.php');

// Data Must be Sanitized, Escaped, and Validated
$subdomain = sanitize_text_field($_REQUEST['subdomain']);
$app_api = sanitize_text_field($_REQUEST['app_api']);
$album = sanitize_text_field($_REQUEST['album']);
$limit = sanitize_text_field($_REQUEST['limit']);
$start = sanitize_text_field($_REQUEST['start']);
$token = sanitize_text_field($_REQUEST['token']);
$keyword = sanitize_text_field($_REQUEST['keyword']);

if (isset($subdomain) && !empty($subdomain) && isset($app_api) && !empty($app_api)) {

//	Images:  .gif,  .heic,  .jpeg .jpg,  .png,  .svg,  .webp,
//  Documents:  .doc .docx,  .key,  .odt,  .pdf,  .ppt .pptx .pps .ppsx,  .xls .xlsx,
//  Audio:  .mp3,  .m4a,  .ogg,  .wav,
//  Video:  .avi,  .mpg,  .mp4 .m4v,  .mov,  .ogv,  .vtt,  .wmv,  .3gp .3g2,
    $fileType4Images = "GIF|JPG|PNG|SVG|WEBP|";
    $fileType4Documents = "DOC|KEY|ODT|PDF|PPT|XLS|";
    $fileType4Audio = "MPEG|M4A|OGG|WAV|";
    $fileType4Video = "AVI|MP4|MOV|OGG|VTT|WMV|3GP";
    $fileType = $fileType4Images . $fileType4Documents . $fileType4Audio . $fileType4Video;

//    $fileType = "MPEG|M4A|OGG|WAV";

    if (isset($album) && $album != null && !empty($album)) {
        $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/album/' . $album . '?limit=' . $limit . '&start=' . $start
            . '&fileType=' . urlencode($fileType);
    } else {
//      $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/image?limit=' . $limit . '&start=' . $start;
//      $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/search?keyword=&scheme=image' . '&limit=' . $limit . '&start=' . $start;
        $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/search?keyword=&limit=' . $limit . '&start=' . $start
            . '&fileType=' . urlencode($fileType);
    }

    if (isset($keyword) && !empty($keyword)) {
        $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/search?keyword=' . urlencode($keyword) . '&fileType=' . urlencode($fileType) .
            '&operator=and&limit=' . $limit . '&start=' . $start;
    }

    $header = array('Authorization: Bearer ' . $token);

    $args_for_get = array(
        'Authorization' => 'Bearer ' . $token,
        'user-agent' => 'Wordpress Plugin',
        'Content-Type' => 'application/json;charset=utf-8'
    );

    $response = wp_remote_get($url,
        array(
            'method' => 'GET',
            'headers' => $args_for_get,
            'timeout' => 120,
        )
    );

    $body = wp_remote_retrieve_body($response);

    echo wp_json_encode($body);
}
?>
