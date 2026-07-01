<?php

/**
 * Admin View: Settings
 */

if (!defined('ABSPATH')) :
    exit;
endif; ?>
<footer class="footer">
    <div class="footer-body text-center">
        <?php echo esc_html( wpb_get_theme_settings('copyright_text') ?? "" ); ?>
    </div>
</footer>