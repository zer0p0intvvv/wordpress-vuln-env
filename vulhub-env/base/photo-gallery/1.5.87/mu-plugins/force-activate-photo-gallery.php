<?php
/**
 * MU-Plugin: Force-activate Photo Gallery by 10Web.
 * WordPress 每次请求自动加载，通过 option_active_plugins filter 注入激活状态。
 * 完全绕过 wp-cli / shell 脚本，无 CRLF 时序问题。
 */
add_filter('option_active_plugins', function ($plugins) {
    $target = 'photo-gallery/photo-gallery.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
