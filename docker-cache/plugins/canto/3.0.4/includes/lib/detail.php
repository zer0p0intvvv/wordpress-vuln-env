<?php

require_once($_REQUEST['wp_abspath'] . '/wp-admin/admin.php');

// Data Must be Sanitized, Escaped, and Validated
$subdomain = sanitize_text_field($_REQUEST['subdomain']);
$app_api = sanitize_text_field($_REQUEST['app_api']);
$scheme = sanitize_text_field($_REQUEST['scheme']);
$id = sanitize_text_field($_REQUEST['id']);
$token = sanitize_text_field($_REQUEST['token']);

if (isset($subdomain) && isset($app_api) && isset($scheme) && isset($id) && $subdomain && $app_api && $scheme && $id) {
    $url = 'https://' . $subdomain . '.' . $app_api . '/api/v1/' . $scheme . '/' . $id;

    $args_for_get = array(
        'Authorization' => 'Bearer ' . $token,
        'user-agent' => 'Wordpress Plugin',
        'Content-Type' => 'application/json;charset=utf-8'
    );

    $response = wp_remote_get($url,
        array(
            'method' => 'GET',
            'headers' => $args_for_get
        )
    );
    $body = wp_remote_retrieve_body($response);
    echo wp_json_encode($body);
}
?>

