<?php
/**
 * MU-Plugin: Force-activate Simple Membership plugin.
 * WordPress 每次请求自动加载，通过 option_active_plugins filter 注入激活状态。
 * 无 wp-cli / shell 依赖，无 CRLF 时序问题。
 */
add_filter('option_active_plugins', function ($plugins) {
    $target = 'simple-membership/simple-wp-membership.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
