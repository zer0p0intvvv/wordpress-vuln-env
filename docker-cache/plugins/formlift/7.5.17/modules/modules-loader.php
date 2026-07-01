<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

include __DIR__ . '/api/api-log.php';
include __DIR__ . '/api/AppV2.php';
include __DIR__ . '/api/infusionsoft-manager.php';

//include __DIR__ . '/awards/awards.php';

include __DIR__ . '/editor/editor.php';
include __DIR__ . '/editor/field-editor.php';
include __DIR__ . '/editor/form-builder.php';
include __DIR__ . '/editor/form-settings.php';
include __DIR__ . '/editor/modal.php';
include __DIR__ . '/editor/preview.php';

include __DIR__ . '/form/field-validator.php';
include __DIR__ . '/form/form.php';
include __DIR__ . '/form/form-field.php';
include __DIR__ . '/form/post-type.php';
include __DIR__ . '/form/submit.php';

include __DIR__ . '/lib/conflicts.php';
include __DIR__ . '/lib/field-interface.php';
include __DIR__ . '/lib/functions.php';
include __DIR__ . '/lib/locations.php';
include __DIR__ . '/lib/RecursiveDomIterator.php';
include __DIR__ . '/lib/update-manager.php';

include __DIR__ . '/notices/notice.php';
include __DIR__ . '/notices/notice-manager.php';

include __DIR__ . '/personalization/sessions.php';
include __DIR__ . '/personalization/user.php';

include __DIR__ . '/premium-modules/module-manager.php';

include __DIR__ . '/settings/defaults.php';
include __DIR__ . '/settings/form/settings.php';
include __DIR__ . '/settings/form/settings-field.php';
include __DIR__ . '/settings/style/style-field.php';
include __DIR__ . '/settings/style/style-settings.php';

include __DIR__ . '/settings-page/options-skin.php';
include __DIR__ . '/settings-page/settings-page.php';
include __DIR__ . '/settings-page/settings-section.php';

include __DIR__ . '/tracking/stats.php';
include __DIR__ . '/tracking/tables.php';
include __DIR__ . '/tracking/tracking.php';

include __DIR__ . '/groundhogg.php';

if ( is_admin() ) {
	\FormLift\Groundhogg::instance();
}