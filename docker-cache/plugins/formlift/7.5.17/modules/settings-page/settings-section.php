<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'FORMLIFT_INPUT', 'input' );
define( 'FORMLIFT_COLOR', 'color' );
define( 'FORMLIFT_SELECT', 'select' );
define( 'FORMLIFT_TEXT', 'textarea' );
define( 'FORMLIFT_RADIO', 'radio' );
define( 'FORMLIFT_MULTI', 'multi' );
define( 'FORMLIFT_CHECKBOX', 'checkbox' );
define( 'FORMLIFT_HIDDEN', 'hidden' );
define( 'FORMLIFT_BUTTON', 'button' );
define( 'FORMLIFT_ERROR', 'error' );
define( 'FORMLIFT_DATE', 'date' );
define( 'FORMLIFT_TIME', 'time' );
define( 'FORMLIFT_SEPARATOR', 'separator' );
define( 'FORMLIFT_TITLE', 'title' );
define( 'FORMLIFT_NUMBER', 'number' );
define( 'FORMLIFT_SECRET', 'secret' );
define( 'FORMLIFT_EDITOR', 'editor' );

class FormLift_Settings_Section {
	/*
	 * section_id string
	 * section_title string
	 * fields array('field_id', 'field_label' , 'field_type', $value(S))
	 */
	var $section_id;
	var $section_title;
	var $fields;
	var $name;
	var $form;

	/**
	 * formlift_Form_Settings_Section constructor.
	 *
	 * @param $form          FormLift_Form
	 * @param $section_id    String
	 * @param $section_title String
	 * @param $fields        array(formlift_Field)
	 * @param $premium       bool
	 */
	function __construct( $section_id, $name, $section_title, $fields ) {

		$this->name          = $name;
		$this->section_id    = $section_id;
		$this->section_title = $section_title;
		$this->fields        = $fields;
		if ( isset( $_GET['post'] ) ) {
			$this->form = new FormLift_Form( $_GET['post'] );
		} else {
			$this->form = new FormLift_Form( null );
		}

	}

	/**
	 * give's the formlift_Settings_Header class this sections header
	 *
	 * @param $headers
	 *
	 * @return mixed
	 */
	public function get_header() {
		$hide   = ( $this->section_id == formlift_get_active_tab( $this->name, $this->form ) ) ? " formlift-active" : "";
		$header = "<a href='javascript:void(0)' class='formlift-tab$hide' onclick='formliftOpenSection(event, \"$this->section_id\", \"$this->name\")'>$this->section_title</a>";

		return $header;
	}

	/**
	 * Wraps the section in the responsive layout. Also automatically displays if it was the last section open before saving.
	 *
	 * @param $content string
	 *
	 * @return string
	 */
	private function wrap_section( $content ) {
		$hide = ( $this->section_id == formlift_get_active_tab( $this->name, $this->form ) ) ? "" : "style='display:none'";

		return "<div id='_$this->section_id' class='formlift-animate-right formlift-section' {$hide}><h1>$this->section_title</h1>$content</div>";
	}


	public function __toString() {
		$content = '';

		foreach ( $this->fields as $field ) {
			$content .= $field;
		}

		return self::wrap_section( $content );
	}

}