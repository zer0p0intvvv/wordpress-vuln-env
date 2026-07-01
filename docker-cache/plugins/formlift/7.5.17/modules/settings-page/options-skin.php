<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Options_Skin {
	var $ID;
	var $name;
	var $form;
	var $sections = array();
	var $header;

	public function __construct( $post_id, $name ) {
		$this->name = $name;
		$this->ID   = $post_id;
		$this->form = new FormLift_Form( $post_id );
	}

	/**
	 * Echo support
	 *
	 * @return string
	 */
	function __toString() {

		$content = $this->get_active_tab();
		$content .= "<div id='formlift-settings-{$this->name}' class='formlift-settings-page'>";
		$content .= $this->build_navigation();
		//$content.= "<div class='formlift-navigation'>{$this->build_navigation()}</div>";
		$content .= $this->build_settings_window();
		//$content.= "<div class='formlift-settings'>{$this->build_settings_window()}</div>";
		$content .= "</div>";

		return $content;
	}

	/**
	 * Add the header to the navigation
	 *
	 * @return string
	 */
	private function build_navigation() {
		$content = "<nav id='formlift-header' class='formlift-nav'>";
		foreach ( $this->sections as $section ) {
			$content .= $section->get_header();
		}

		return $content . "<button type='submit' style='margin: 45px' class='button button-large'>SAVE CHANGES</button></nav>";
	}

	/**
	 * Add the settings window pane
	 *
	 * @return string
	 */
	private function build_settings_window() {
		$content = "<div class='formlift-sections-container'>";
		foreach ( $this->sections as $section ) {
			$content .= $section;
		}
		$content .= "</div>";

		return $content;
	}

	/**
	 * adds a settings section to the panel
	 *
	 * @param $section
	 */
	public function add_section( $id, $label, $fields ) {
		$section = new FormLift_Settings_Section( $id, $this->name, $label, $fields );
		array_push( $this->sections, $section );
	}

	private function get_active_tab() {
		$value = formlift_get_active_tab( $this->name, $this->form );
		$key   = FORMLIFT_SETTINGS;

		return "<input type='hidden' name='{$key}[{$this->name}_active_tab]' id='{$this->name}_active_tab' value='$value'/>";
	}
}

/**
 * @param $tab_name string
 * @param $form     FormLift_Form
 *
 * @return string
 */
function formlift_get_active_tab( $tab_name, $form ) {
	$value = $form->get_form_setting( $tab_name . '_active_tab' );

	$screen = get_current_screen();

	if ( $screen->action == "add" ) {
		if ( $tab_name == "style_settings" ) {
			return "formlift_button_css";
		} else {
			return "formlift_import_settings";
		}
	} else if ( $screen->id == FormLift_Settings_Page::$admin_page && empty( $value ) ) {
		$value = get_formlift_setting( $tab_name . '_active_tab' );

		if ( ! empty( $value ) ) {
			return $value;
		}

		if ( $tab_name == "style_settings" ) {
			return "formlift_button_css";
		} else {
			return "formlift_infusionsoft_settings";
		}
	} else {
		return $value;
	}
}