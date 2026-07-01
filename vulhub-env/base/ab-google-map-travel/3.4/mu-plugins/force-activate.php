<?php
/**
 * MU-Plugin: Force-activate AB Google Map Travel plugin.
 */
add_filter('option_active_plugins', function ($plugins) {
    $target = 'ab-google-map-travel/ab-google-map-travel.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
