<?php
/**
 * MU-Plugin: Force-activate Booking Calendar.
 * 放在 mu-plugins/ 目录，WordPress 每次请求自动加载。
 * 纯 PHP filter，无 wp-cli / shell 依赖，无 CRLF 时序问题。
 */
add_filter('option_active_plugins', function ($plugins) {
    $target = 'booking-calendar/booking_calendar.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
