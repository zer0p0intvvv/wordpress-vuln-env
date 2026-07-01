<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Setting_Field {
	var $id;
	var $label;
	var $type;
	var $values;
	var $form;
	var $description;

	function __construct( $type, $id = '', $label = '', $values = array(), $description = null ) {
		$this->id          = $id;
		$this->label       = $label;
		$this->type        = $type;
		$this->values      = $values;
		$this->description = $description;

		if ( isset( $_GET['post'] ) ) {
			$this->form = new FormLift_Form( $_GET['post'] );
		} else if ( get_current_screen()->id == FormLift_Settings_Page::$admin_page && get_current_screen()->post_type == "infusion_form" ) {
			$this->form = new FormLift_Form( "settings_page" );
		} else {
			$this->form = new FormLift_Form( null );
		}
	}

	private function separator() {
		return "<hr />";
	}

	private function title() {
		return "";
	}

	/**
	 * Creates a date-picker field
	 *
	 * @return string
	 */
	private function date() {
		$option_key = FORMLIFT_SETTINGS;

		$input = "<input class='formlift-input' id='$this->id' data-type=\"date\" name='{$option_key}[{$this->id}]' value='{$this->form->get_form_setting( $this->id )}'/>";
		$input .= "<script>jQuery(document).ready(function (){ jQuery('#$this->id').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, minDate: 0, maxDate: '+2Y'});});</script>";

		return $input;
	}


	/**
	 * Creates a plain text field
	 *
	 * @return string
	 */
	private function input() {
		$option_key = FORMLIFT_SETTINGS;
		$input      = "<input class='formlift-input' name='{$option_key}[{$this->id}]' value='{$this->form->get_form_setting( $this->id )}'/>";

		return $input;
	}

	/**
	 * Creates a password type field
	 *
	 * @return string
	 */
	private function secret() {
		$option_key = FORMLIFT_SETTINGS;
		$input      = "<input class='formlift-input' type='password' name='{$option_key}[{$this->id}]' value='{$this->form->get_form_setting( $this->id )}'/>";

		return $input;
	}

	/**
	 * creates a time picker field
	 *
	 * @return string
	 */
	private function time() {
		$option_key = FORMLIFT_SETTINGS;
		$input      = "<input class='formlift-input' id='$this->id' name='{$option_key}[{$this->id}]' value='{$this->form->get_form_setting( $this->id )}'/><script>jQuery( '#$this->id' ).timepicker({ 'timeFormat': 'H:i:s' });</script>";

		return $input;
	}

	/**
	 * creates a number type field
	 *
	 * @return string
	 */
	private function number() {
		$option_key = FORMLIFT_SETTINGS;
		$input      = "<input class='formlift-input' type='number' placeholder='{$this->form->get_default_form_setting( $this->id )}' name='{$option_key}[{$this->id}]' value='{$this->form->get_form_setting( $this->id )}'/>";

		return $input;
	}

	/**
	 * Creates a hidden field
	 *
	 * @return string
	 */
	private function hidden() {
		$option_key = FORMLIFT_SETTINGS;
		$input      = "<input class='formlift-input' type='hidden' name='{$option_key}[{$this->id}]' value='{$this->form->get_form_setting( $this->id )}'/>";

		return $input;
	}

	/**
	 * Creates a button
	 *
	 * @return string
	 */
	private function button() {
		$option_key = FORMLIFT_SETTINGS;
		$button     = "<input type='submit' name='{$option_key}[$this->id]' value='{$this->values}' class='button-primary'>";

		return $button;
	}

	/**
	 * Creates a checkbox field
	 *
	 * @return string
	 */
	private function checkbox() {
		$option_key = FORMLIFT_SETTINGS;
		$checked    = ( $this->form->get_form_setting( $this->id ) ) ? 'checked' : '';
		$input      = "<label class='switch'><input type='checkbox' name='{$option_key}[{$this->id}]' value='1' $checked/><span class='formlift-slider round'></span></label>";

		return $input;
	}

	/**
	 * Creates a dropdown select menu of options
	 *
	 * @return string
	 */
	private function select() {
		$option_key = FORMLIFT_SETTINGS;
		$value      = $this->form->get_form_setting( $this->id );
		$select     = "<select id='$this->id' name='{$option_key}[$this->id]' style='max-width:270px;'>";

		foreach ( $this->values as $possible_value => $label ) {
			if ( $possible_value == $value ) {
				$select .= "<option value='$possible_value' selected>$label</option>";
			} else {
				$select .= "<option value='$possible_value'>$label</option>";
			}
		}

		$select .= "</select>";

		return $select;
	}

	/**
	 * Creates a textarea
	 *
	 * @return string
	 */
	private function textarea() {
		$option_key = FORMLIFT_SETTINGS;
		$input      = "<textarea cols='30' rows='2' placeholder='{$this->form->get_default_form_setting( $this->id )}' name='{$option_key}[{$this->id}]'>{$this->form->get_form_setting( $this->id )}</textarea>";

		return $input;
	}

	/**
	 * Creates radio options
	 *
	 * @return string
	 */
	private function radio() {
		$option_key  = FORMLIFT_SETTINGS;
		$value       = ( $this->form->get_form_setting( $this->id ) ) ? $this->form->get_form_setting( $this->id ) : '';
		$the_content = "";
		foreach ( $this->values as $possible_value => $text ) {
			$text = utf8_decode( $text );
			if ( $possible_value == $value ) {
				$the_content .= "<label><input type='radio' name='{$option_key}[$this->id]' value='$possible_value' checked>$text</label><br/>";
			} else {
				$the_content .= "<label><input type='radio' name='{$option_key}[$this->id]' value='$possible_value'>$text</label><br/>";
			}
		}

		return $the_content;
	}

	private function editor() {
		$option_key = FORMLIFT_SETTINGS;
		ob_start();
		wp_editor( $this->form->get_form_setting( $this->id ), $this->id, array(
			"textarea_name" => $this->id
		) );
		$input = ob_get_clean();

		return $input;
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
		$label_html = "<label for=\"$this->id\">$this->label</label>";
		$method     = $this->type;
		$input_html = $this->$method();

		$content = self::wrap_label_cell( $label_html ) . self::wrap_input_cell( $input_html );
		if ( ! empty( $this->description ) ) {
			$content .= "<p>{$this->description}</p>";
		}

		return self::wrap_row( $content );
	}
}
