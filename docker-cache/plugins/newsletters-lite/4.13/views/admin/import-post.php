<?php // phpcs:ignoreFile ?>
<div class="wrap newsletters <?php echo esc_html($this->pre); ?>">
    <h2><?php esc_html_e('Importing Subscribers', 'wp-mailinglist'); ?></h2>
	<?php 
	if (!empty($subscribers)) {
	    $cleanedSubscribers = [];
	    $firstLineSkipped = false; // Flag to skip the first line
	    foreach ($subscribers as $subscriber) {
	        // Skip the first line
	        if (!$firstLineSkipped) {
	            $firstLineSkipped = true;
	            continue;
	        }

	        // Validate subscriber data
	        if (
                is_array($subscriber) &&
                !empty($subscriber['email']) &&
                filter_var($subscriber['email'], FILTER_VALIDATE_EMAIL)
            ) {
	            $cleanedSubscribers[] = $subscriber;
	        }
	    }
	    $subscribers = $cleanedSubscribers; // Assign cleaned data back to $subscribers
	}

	if (!empty($subscribers)) : ?>	

        <p class="newsletters_importajaxcount">
            <span id="importajaxcount">
                <strong><span id="importajaxcountinside" class="newsletters_success">0</span></strong>
            </span>
            <span id="importajaxfailedcount">
                (<strong><span id="importajaxfailedcountinside" class="newsletters_error">0</span></strong> failed)
            </span>
            <?php esc_html_e('out of', 'wp-mailinglist'); ?>
            <strong><?php echo count($subscribers); ?></strong>
            <?php esc_html_e('subscribers have been imported.', 'wp-mailinglist'); ?>
        </p>

        <div id="importprogressbar"></div>

        <p class="submit">
            <a href="javascript:history.go(-1);" class="button button-primary">
                <i class="fa fa-arrow-left"></i> <?php esc_html_e('Back', 'wp-mailinglist'); ?>
            </a>
            <a href="#" onclick="toggleImporting();" id="toggleimporting" class="button-primary">
                <i class="fa fa-refresh fa-spin"></i> <?php esc_html_e('Importing Subscribers', 'wp-mailinglist'); ?>
            </a>
            <a href="#" onclick="pauseImporting();" id="pauseimporting" class="button-secondary" style="display: none;">
                <i class="fa fa-pause"></i> <?php esc_html_e('Pause', 'wp-mailinglist'); ?>
            </a>
            <span id="import_loading" style="display:none;"><i class="fa fa-refresh fa-spin fa-fw"></i></span>
            <span id="importmore" style="display:none;">
                <a href="?page=<?php echo $this->sections->importexport; ?>" class="button-secondary">
                    <?php esc_html_e('Import More', 'wp-mailinglist'); ?>
                </a>
            </span>
        </p>

        <div id="importajaxsuccessrecords" class="scroll-list" style="display:none;"></div>
        <div id="importajaxfailedrecords" class="scroll-list" style="display:none;"></div>
        <div id="importajaxresponse"><!-- response here --></div>

        <script type="text/javascript">
            var allsubscribers = <?php echo json_encode($subscribers); ?>;
            var subscribercount = allsubscribers.length;
            var import_preventbu = "<?php echo !empty($_POST['import_preventbu']) ? 'Y' : 'N'; ?>";
            var import_overwrite = "<?php echo !empty($_POST['import_overwrite']) ? 'Y' : 'N'; ?>";
            var importingnumber = 50;
            var currentImportIndex = 0;
            var completed = 0;
            var imported = 0;
            var failed = 0;
            var requests = [];
            var paused = false;
            var confirmation_subject = jQuery('#confirmation_subject').html();
            var confirmation_email = jQuery('#confirmation_email').html();
            var warnMessage = "<?php esc_html_e('You have unsaved changes on this page! All unsaved changes will be lost and it cannot be undone.', 'wp-mailinglist'); ?>";

            jQuery(document).ready(function () {
                toggleImporting();
                jQuery("#importprogressbar").progressbar({value: 0});
            });

            function toggleImporting() {
                if (paused) {
                    paused = false;
                    jQuery('#toggleimporting').html('<i class="fa fa-refresh fa-spin"></i> <?php esc_html_e('Reading data, please wait', 'wp-mailinglist'); ?>');
                    jQuery('#pauseimporting').show();
                    processImport();
                } else {
                    paused = true;
                    jQuery('#toggleimporting').html('<?php esc_html_e('Start Importing', 'wp-mailinglist'); ?>');
                    jQuery('#pauseimporting').hide();
                }
            }

            function pauseImporting() {
                paused = true;
                jQuery('#toggleimporting').html('<?php esc_html_e('Continue Importing', 'wp-mailinglist'); ?>');
                jQuery('#pauseimporting').hide();
            }

            function processImport() {
                if (paused || currentImportIndex >= subscribercount) {
                    return;
                }
                var importsubscribers = allsubscribers.slice(currentImportIndex, currentImportIndex + importingnumber);
                currentImportIndex += importingnumber;
                importmultiple(importsubscribers);
            }

            function importmultiple(importsubscribers) {
                var importData = {
                    subscribers: importsubscribers,
                    import_preventbu: import_preventbu,
                    import_overwrite: import_overwrite,
                    confirmation_subject: confirmation_subject,
                    confirmation_email: confirmation_email,
                    security: '<?php echo esc_html(wp_create_nonce('importmultiple')); ?>'
                };

                var request = jQuery.post(newsletters_ajaxurl + 'action=newsletters_importmultiple', importData, function (response) {
                    var importdata = response.split("<||>");
                    importdata.forEach(function (data) {
                        if (data) {
                            var parts = data.split('<|>');
                            var success = parts[0].slice(0, 1);
                            var email = parts[0].substring(1);
                            var message = parts[1];
                            if (success === "Y") {
                                imported++;
                                jQuery('#importajaxcountinside').text(imported);
                                jQuery('#importajaxsuccessrecords').prepend('<div class="ui-state-highlight ui-corner-all" style="margin-bottom:3px;"><p><i class="fa fa-check"></i> ' + email + '</p></div>').fadeIn().prev().fadeIn();
                            } else {
                                failed++;
                                jQuery('#importajaxfailedcountinside').text(failed);
                                jQuery('#importajaxfailedrecords').prepend('<div class="ui-state-error ui-corner-all" style="margin-bottom:3px;"><p><i class="fa fa-exclamation-triangle"></i> ' + email + ' - ' + message + '</p></div>').fadeIn().prev().fadeIn();
                            }
                            completed++;
                            var value = (completed * 100) / subscribercount;
                            jQuery("#importprogressbar").progressbar("value", value);
                        }
                    });
                    if (completed < subscribercount) {
                        processImport();
                    } else {
                        jQuery('#toggleimporting').hide();
                        warnMessage = null;
                        jQuery('#importmore').show();
                        jQuery('#import_loading').hide();
                    }
                }).fail(function () {
                    failed += importsubscribers.length;
                    completed += importsubscribers.length;
                    processImport();
                });

                requests.push(request);
            }

            jQuery(document).ready(function () {
                window.onbeforeunload = function () {
                    if (warnMessage != null) return warnMessage;
                };
            });
        </script>
    <?php else : ?>
        <p class="newsletters_error"><?php esc_html_e('No subscribers are available for import, please try again.', 'wp-mailinglist'); ?></p>
        <p>
            <a href="javascript:history.go(-1);" class="button button-primary"><?php esc_html_e('&laquo; Back', 'wp-mailinglist'); ?></a>
        </p>
    <?php endif; ?>
</div>
