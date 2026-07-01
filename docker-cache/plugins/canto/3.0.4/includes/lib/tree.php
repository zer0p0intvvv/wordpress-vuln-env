<?php
//error_reporting(E_ALL ^ E_DEPRECATED);
//ini_set("display_errors", 0);

require_once($_REQUEST['wp_abspath'] . '/wp-admin/admin.php');

// Data Must be Sanitized, Escaped, and Validated
$subdomain = sanitize_text_field($_REQUEST['subdomain']);
$app_api = sanitize_text_field($_REQUEST['app_api']);
$ablumid = sanitize_text_field($_REQUEST['ablumid']);
$token = sanitize_text_field($_REQUEST['token']);

if (isset($subdomain) && !empty($subdomain) && isset($app_api) && !empty($app_api)) {
    if (isset($ablumid) && !empty($ablumid)) {
        $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/tree/' . $ablumid . '?sortBy=name&sortDirection=ascending';
    } else {
        $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/tree?sortBy=name&sortDirection=ascending&layer=1';
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
