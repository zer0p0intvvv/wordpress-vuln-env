<?php
/**
 * Admin View: Settings
 *
 * This file represents the WPBookit settings page view in the admin panel.
 */

// Prevent direct access to this file.
if (!defined('ABSPATH')) {
    exit;
}

// Action hook before the main content of the settings page.
do_action('wpb_settings_before_main_content', $tabs, $current_tab);
?>

<main class="main-content">
    <?php
    // Action hook before the content of the settings page.
    do_action('wpb_settings_before_content', $tabs, $current_tab);

    // Action hook for the specific settings tab content.
    do_action('wpb_settings_' . $current_tab);

    // Action hook for the settings tabs.
    do_action('wpb_settings_tabs_' . $current_tab);

    // Action hook after the content of the settings page.
    do_action('wpb_settings_after_content', $tabs, $current_tab);
    ?>
</main>

<?php
// Action hook after the main content of the settings page.
do_action('wpb_settings_after_main_content', $tabs, $current_tab);
?>
