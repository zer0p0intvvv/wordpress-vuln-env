<?php // phpcs:ignoreFile ?>
<!-- API -->

<?php

$debugging = get_option('tridebugging');
$this->debugging = (empty($debugging)) ? $this->debugging : true;

if (!file_exists(NEWSLETTERS_LOG_FILE)) {
    esc_html_e('The log file does not exist yet.', 'wp-mailinglist');
    die();
}

if (empty(filesize(NEWSLETTERS_LOG_FILE))) {
    $info = [
        'enabled' => $this->debugging,
    ];

    if (!file_exists(NEWSLETTERS_LOG_FILE)) {
        echo esc_html_e('The log file does not exist.', 'wp-mailinglist');
        die();
    }

    // File path.
    $info['filePath'] = NEWSLETTERS_LOG_FILE;

    $file_size = @filesize($info['filePath']);

    if (empty($file_size)) {
        $file_size = '0B';
    }

    echo esc_html_e('The log file is empty.', 'wp-mailinglist');
    die();
}

$lines = 500;
// Open file.
$f = @fopen(NEWSLETTERS_LOG_FILE, 'rb'); // phpcs:ignore

if (false === $f) {
    echo esc_html_e('Could not open the log file', 'wp-mailinglist');
    die();
}

// Sets buffer size, according to the number of lines to retrieve.
// This gives a performance boost when reading a few lines from the file.
$buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

// Jump to last character.
fseek($f, -1, SEEK_END);

// Read it and adjust line number if necessary.
// (Otherwise the result would be wrong if file doesn't end with a blank line).
if (fread($f, 1) != "\n") $lines -= 1; // phpcs:ignore

// Start reading.
$output = '';
$chunk = '';

// While we would like more.
while (ftell($f) > 0 && $lines >= 0) {
    // Figure out how far back we should jump.
    $seek = min(ftell($f), $buffer);

    // Do the jump (backwards, relative to where we are).
    fseek($f, -$seek, SEEK_CUR);

    // Read a chunk and prepend it to our output.
    $output = ($chunk = fread($f, $seek)) . $output; // phpcs:ignore

    // Jump back to where we started reading.
    fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

    // Decrease our line counter.
    $lines -= substr_count($chunk, "\n");
}

// While we have too many lines.
// (Because of buffer size we might have read too many).
while ($lines++ < 0) {
    // Find first newline and remove all text before that.
    $output = substr($output, strpos($output, "\n") + 1);
}

// Close file and return.
fclose($f); // phpcs:ignore

?>

<div class="wrap newsletters">
    <h1><?php esc_html_e('View Logs', 'wp-mailinglist'); ?></h1>

    <?php $this->render('settings-navigation', false, true, 'admin'); ?>

    <p><?php esc_html_e('The debug log displays the last 500 lines and only shows certain logs such as when a cron job fires or when you face a specific issue. The logs are not the same as PHP error logs.', 'wp-mailinglist'); ?><br/>
        <?php _e('<a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/3926/newsletters-debugging/" target="_blank" >Debugging documentation</a>. ', 'wp-mailinglist'); ?>
    </p>    
    <?php if (!empty($log_protection_message)) : ?>
        <div class="notice <?php echo esc_attr($log_protection_message_class); ?>"><p><?php echo esc_html($log_protection_message); ?></p></div>
    <?php endif; ?>
    <textarea style="width: 100%; min-height: 600px;"><?php echo esc_textarea($output); ?></textarea>
    <?php if (empty($log_protected)) : ?>
        <form method="post" style="margin-top: 10px;">
            <?php wp_nonce_field('newsletters_protect_log_file'); ?>
            <p>
                <button type="submit" name="protect_log_file" class="button button-secondary"><?php esc_html_e('Protect Log File via .htaccess', 'wp-mailinglist'); ?></button>
                <span class="description"><?php esc_html_e('Add a .htaccess rule to block direct access to the log file.', 'wp-mailinglist'); ?></span>
            </p>
        </form>
        <?php //if (empty($log_htaccess_writable)) : ?>
            <div class="log-htaccess-manual" style="margin-top: 10px;">
                <a href="#" class="toggle-log-htaccess-rule" aria-expanded="false"><?php esc_html_e('Or add this to your htaccess to protect your log', 'wp-mailinglist'); ?></a>
                <div class="log-htaccess-rule" style="display: none; margin-top: 5px;">
                    <pre><?php echo esc_html($log_htaccess_rule); ?></pre>
                </div>
            </div>
            <script type="text/javascript">
                (function($) {
                    $(document).ready(function() {
                        $('.toggle-log-htaccess-rule').on('click', function(event) {
                            event.preventDefault();

                            var $toggle = $(this);
                            var $container = $toggle.closest('.log-htaccess-manual').find('.log-htaccess-rule');
                            var expanded = $toggle.attr('aria-expanded') === 'true';

                            $container.toggle(!expanded);
                            $toggle.attr('aria-expanded', expanded ? 'false' : 'true');
                        });
                    });
                })(jQuery);
            </script>
        <?php //endif; ?>
    <?php endif; ?>
</div>
