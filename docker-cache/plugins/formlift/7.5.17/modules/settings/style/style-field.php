<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Style_Field {
	var $class;
	var $attribute;
	var $label;
	var $type;
	var $values;
	var $sub_fields;
	var $form;
	var $description;

	function __construct( $type, $class, $attribute, $label = '', $values = array(), $sub_fields = array(), $description = null ) {
		$this->class       = $class;
		$this->attribute   = $attribute;
		$this->label       = $label;
		$this->type        = $type;
		$this->values      = $values;
		$this->sub_fields  = $sub_fields;
		$this->description = $description;

		if ( isset( $_GET['post'] ) ) {
			$this->form = new FormLift_Form( $_GET['post'] );
		} else if ( get_current_screen()->id == FormLift_Settings_Page::$admin_page && get_current_screen()->post_type == "infusion_form" ) {
			$this->form = new FormLift_Form( "settings_page" );
		} else {
			$this->form = new FormLift_Form( null );
		}
	}

	/**
	 * Creates a wp-color-picker field
	 *
	 * @return string
	 */
	private function color() {
		$option_key = FORMLIFT_STYLE;
		$input      = "<input type='text' class='formlift-color-picker input' data-alpha=\"true\" placeholder='{$this->form->get_default_style_setting( $this->class, $this->attribute )}' name='{$option_key}[{$this->class}][{$this->attribute}]' value='{$this->form->get_style_setting( $this->class, $this->attribute )}'/>";

		return $input;
	}

	/**
	 * Creates a plain text field
	 *
	 * @return string
	 */
	private function input() {
		$option_key = FORMLIFT_STYLE;
		$input      = "<input type='text' class='input' placeholder='{$this->form->get_default_style_setting( $this->class, $this->attribute )}' name='{$option_key}[{$this->class}][{$this->attribute}]' value='{$this->form->get_style_setting( $this->class, $this->attribute )}'/>";

		return $input;
	}

	/**
	 * Creates a dropdown select menu of options
	 *
	 * @return string
	 */
	private function select() {
		$option_key = FORMLIFT_STYLE;
		$select     = "<select name='{$option_key}[{$this->class}][{$this->attribute}]' style='max-width:270px;'>";
		if ( key_exists( $this->form->get_style_setting( $this->class, $this->attribute ), $this->values ) ) {
			foreach ( $this->values as $possible_value => $label ) {
				if ( $possible_value == $this->form->get_style_setting( $this->class, $this->attribute ) ) {
					$select .= "<option value='$possible_value' selected>$label</option>";
				} else {
					$select .= "<option value='$possible_value'>$label</option>";
				}
			}
		} else {
			$value = $this->form->get_default_style_setting( $this->class, $this->attribute );
			foreach ( $this->values as $possible_value => $label ) {
				if ( $possible_value == $value ) {
					$select .= "<option value='$possible_value' selected>$label</option>";
				} else {
					$select .= "<option value='$possible_value'>$label</option>";
				}
			}
		}
		$select .= "</select>";

		return $select;
	}

	/**
	 * Creates side by side small text inputs
	 *
	 * @param $field formlift_Style_Field
	 *
	 * @return string
	 */
	private function multi() {
		$option_key = FORMLIFT_STYLE;
		$table      = '';
		foreach ( $this->sub_fields as $sub => $attribute ) {
			$table .= "<input class='formlift-input' style='width: 50px' placeholder='{$this->form->get_default_style_setting( $this->class, $attribute )}' name='{$option_key}[{$this->class}][{$attribute}]' value='{$this->form->get_style_setting( $this->class, $attribute )}'/>";
		}

		return $table;

	}

	/**
	 * Wraps content in the row div
	 *
	 * @param $content string
	 *
	 * @return string
	 */
	public static function wrap_row( $content ) {
		return "<div class='formlift-row'>$content</div>";
	}

	/**
	 * Wraps the label element returned by the above functions in a cell
	 *
	 * @param $content string
	 *
	 * @return string
	 */
	public static function wrap_label_cell( $content ) {
		return "<div class='formlift-cell formlift-cell-label'>$content</div>";
	}

	/**
	 * Wraps the input element returned by the above functions in a cell
	 *
	 * @param $content string
	 *
	 * @return string
	 */
	public static function wrap_input_cell( $content ) {
		return "<div class='formlift-cell formlift-cell-input'>$content</div>";
	}

	public function __toString() {
		$label_html = "<label>$this->label</label>";
		$method     = $this->type;
		$input_html = $this->$method();
		$content    = self::wrap_label_cell( $label_html ) . self::wrap_input_cell( $input_html );
		if ( ! empty( $this->description ) ) {
			$content .= "<p>{$this->description}</p>";
		}

		return self::wrap_row( $content );
	}
}
