<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Edit_PopUp {

	public static function add_scripts() {
		$screen = get_current_screen();

		if ( $screen->post_type !== 'infusion_form' || $screen->base !== 'post' ) {
			return;
		}

		wp_enqueue_script( 'formlift-editor-popup', plugins_url( 'assets/js/modal.js', __FILE__ ), array(), FORMLIFT_JS_VERSION, true );
		wp_enqueue_style( 'formlift-popup-css', plugins_url( 'assets/css/modal.css', __FILE__ ), array(), FORMLIFT_CSS_VERSION );

	}

	function __toString() {
		$overlay        = "<div id=\"formliftPopUpOverlay\" style=\"display:none;\"></div>";
		$title          = "<div id=\"formliftPopUpTitle\"></div>";
		$close          = "<div id=\"formliftPopUpCloseContainer\"><button id=\"formliftCloseButton\" type=\"button\"><span class=\"dashicons dashicons-no\"></span></button></div>";
		$titleContainer = "<div id=\"formliftPopUpTitleContainer\">$title $close</div>";
		$content        = "<div id=\"formliftPopUpContent\"></div>";
		$footer         = "<div id=\"formliftPopUpFooter\"><button type=\"button\" id=\"formliftPopUpSaveButton\" class=\"button button-primary\">Save Changes</button></div>";
		$window         = "<div id=\"formliftPopUpWindow\" style=\"display:none;\">$titleContainer $content $footer</div>";

		return $overlay . $window;
	}
}

add_action( 'admin_enqueue_scripts', array( 'FormLift_Edit_PopUp', 'add_scripts' ) );
