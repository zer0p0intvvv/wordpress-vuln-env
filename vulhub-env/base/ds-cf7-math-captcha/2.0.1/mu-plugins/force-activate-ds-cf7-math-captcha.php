<?php
/**
 * MU-Plugin: Force-activate ds-cf7-math-captcha.
 * Filters the active_plugins option so WordPress always treats the plugin as
 * active — no wp-cli, no shell script, no platform-specific line-ending issues.
 */
add_filter('option_active_plugins', function ($plugins) {
    $target = 'ds-cf7-math-captcha/ds-cf7-math-captcha.php';
    if (!in_array($target, (array) $plugins, true)) {
        $plugins[] = $target;
    }
    return $plugins;
});
