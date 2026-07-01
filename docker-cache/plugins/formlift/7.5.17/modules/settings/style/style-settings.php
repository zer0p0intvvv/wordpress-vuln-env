<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Style_Settings {
	public static function get_advanced_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'transition', 'Button Hover Fade Time', null, null,
				'Time it takes to transition from the buttons default state to the hover state in seconds. Example: 0.4s' ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button:hover', 'transition', 'Button Hover Fade Time 2', null, null,
				'The time it takes for the button to revert back to it\'s deafult state from the hover state. Example: 0.4s' ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input:focus', 'transition', 'Input Focus Color Fade Time', null, null,
				"The time it takes for an input field to transition to it's focused state when the user is interacting with the field. Example. 0.4s" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_check_style', 'transition', 'Radio Fade In/Out Time', null, null,
				"The time it takes for a radio button or checkbox field to transition to it's focused state when the user is interacting with the field. Example. 0.4s" ),

			/* changed from input to .formlift_input */
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_field .formlift_input::placeholder', 'color', 'Webkit Placeholder Color', null, null,
				'Placeholder color for Safari, Firefox, and Chrome browsers.' ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_field .formlift_input::-ms-input-placeholder', 'color', 'Microsoft Edge Placeholder Color', null, null,
				"Placeholder color for Microsoft Edge" ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_field .formlift_input:-ms-input-placeholder', 'color', 'Internet Explorer Placeholder Color', null, null,
				"Placeholder color for Microsoft Explorer" )

		);

		return apply_filters( 'formlift_get_advanced_css', $fields );
	}

	/**
	 * Returns a list of formlift_Fields for the Button Settings Section
	 *
	 * @return array
	 */
	public static function get_button_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_button', 'background-color', 'Color', null, null,
				"The default main background color of the button." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_button', 'border-color', 'Border Color', null, null,
				"The default border color of the button." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_button', 'color', 'Font Color', null, null,
				"The default font color of the button." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_button:hover', 'background-color', 'Hover Color', null, null,
				"The main background color of the button when it's being hovered over by the user's cursor." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_button:hover', 'border-color', 'Hover Border Color', null, null,
				"The border color of the button when it's when it's being hovered over by the user's cursor." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_button:hover', 'color', 'Hover Font Color', null, null,
				"The font color of the button when it's being hovered over by the user's cursor." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'width', 'Width', null, null,
				"The width of the button. Accepts percentage or pixel values. Example: 100% or 100px" ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift_button', 'border-style', 'Border Style', array(
				'none'   => 'No Border',
				'solid'  => 'Solid Line',
				'dotted' => 'Dotted Line',
				'dashed' => 'Dashed Line',
				'double' => 'Double Line',
				'groove' => 'Grouve Bevel',
				'ridge'  => 'Ridge Bevel',
				'outset' => 'Outset Bevel',
				'inset'  => 'Inset Bevel'
			), null, "The border appearance of the button. Select one from the dropdown." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'border-width', 'Border Width', null, null,
				"The width of the border, accepts pixel values only. Example: 3px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'border-radius', 'Border Radius', null, null,
				"The roundness of the button's corners. The higher the value, the more rounded they are. Accepts pixel values only. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'padding-top', 'Top Padding', null, null,
				"The space between the top of the button and the text within. Accepts Pixel Values. Example: 10px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'padding-bottom', 'Bottom Padding', null, null,
				"The space between the bottom of the button and the text within. Accepts pixel values. Example: 10px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'font-family', 'Font Family', null, null,
				"The font type you wish to use in the button. It must already be included in your theme if it's not a web safe font." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'font-size', 'Font Size', null, null,
				"The size of the button text. Accepts pixel values. Example: 16px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'font-weight', 'Font Weight', null, null,
				"The <b>boldness</b> of the button text. Generally accepts the following values: 300, 400, 500, 600, 700, normal, semi-bold, bold." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_button', 'box-shadow', 'Box Shadow', null, null,
				"The button's shadow. Accepts input of the format: (X Y BLUR COLOR) 1px 1px 5px #000" ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift_button_container', 'text-align', 'Alignment', array(
				'left'   => 'Left',
				'center' => 'Center',
				'right'  => 'Right'
			), null, "If your button is NOT full-width, this will dictate it's alignment in relation to the form." )
		);

		return apply_filters( 'formlift_get_button_css', $fields );
	}

	/**
	 * Returns a list of FormLift_Style_Fields for the input settings section
	 *
	 * @return array
	 */
	public static function get_input_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_input', 'background-color', 'Background Color', null, null,
				"The background color of all inputs when not being edited." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_input', 'border-color', 'Border Color', null, null,
				"The border color of all inputs when not being edited." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_input', 'color', 'Font Color', null, null,
				"The font color of all inputs when not being edited." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_input:focus', 'background-color', 'Focus Background-color', null, null,
				"The background color of the input when being edited." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_input:focus', 'border-color', 'Focus Border Color', null, null,
				"The border color of the input when being edited." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_input:focus', 'color', 'Focus Font Color', null, null,
				"The font color of the input when being edited." ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift_input', 'border-style', 'Border Style', array(
				'none'   => 'No Border',
				'solid'  => 'Solid Line',
				'dotted' => 'Dotted Line',
				'dashed' => 'Dashed Line',
				'double' => 'Double Line',
				'groove' => 'Grouve Bevel',
				'ridge'  => 'Ridge Bevel',
				'outset' => 'Outset Bevel',
				'inset'  => 'Inset Bevel'
			), null,
				"The border style of all input elements." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'border-width', 'Border Width', null, null,
				"The width of the border. Accepts pixel values. Example: 1px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'border-radius', 'Border Radius', null, null,
				"The roundness of the border. Accepts pixel values. The higher, the rounder. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'width', 'Input Width', null, null,
				"The width of the button in relation to the form. Accepts percentage or pixel values. Example: 100% for full width." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'height', 'Input Height', null, null,
				"The height of the inputs. Best left set to <b>auto</b>. Accepts pixel values." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'padding', 'Text Padding', null, null,
				"The space above and below the text inside the inputs. Accepts pixel values. Example: 10px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'font-family', 'Font Family', null, null,
				"The font type you wish to use in the inputs. It must already be included in your theme if it's not a web safe font." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'font-size', 'Font Size', null, null,
				"The size of the text inside the inputs. Accepts pixel values. Example 16px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'font-weight', 'Font Weight', null, null,
				"The <b>boldness</b> of the input text. Generally accepts the following values: 300, 400, 500, 600, 700, normal, semi-bold, bold." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_input', 'box-shadow', 'Box Shadow', null, null,
				"The inputs' shadow. Accepts input of the format: (X Y BLUR COLOR) 1px 1px 5px #000 or none." ),
		);

		return apply_filters( 'formlift_get_input_css', $fields );
	}

	public static function get_radio_checkbox_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_radio_option_container', 'padding', 'Option Spacing', null, null,
				"The spacing between radio button options. Accepts pixel values. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_radio_option_container .formlift_radio_label_container', 'font-size', 'Radio Option Font Size', null, null,
				"The font size of radio button text. Accepts pixel Values. Example: 15px" ),
//            new FormLift_Style_Field(FORMLIFT_INPUT, 'formlift_check_style', 'height', 'Radio Button Height'),
//            new FormLift_Style_Field(FORMLIFT_INPUT, 'formlift_check_style', 'width', 'Radio Button Width'),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_check_style, .formlift_radio_label_container', '--rb-size', 'Radio Button Size', null, null,
				"The size of the radio button AND checkboxes. Accepts pixel values. Example: 20px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_radio_label_container .formlift_is_checkbox ~ .formlift_check_style:after', 'font-size', 'Check Mark Size', null, null,
				"The size of the check mark for the checkboxes. Accepts pixel values. Example: 18px" ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift_check_style', 'border-style', 'Border Style', array(
				'none'   => 'No Border',
				'solid'  => 'Solid Line',
				'dotted' => 'Dotted Line',
				'dashed' => 'Dashed Line',
				'double' => 'Double Line',
				'groove' => 'Grouve Bevel',
				'ridge'  => 'Ridge Bevel',
				'outset' => 'Outset Bevel',
				'inset'  => 'Inset Bevel'
			), null,
				"The border style for radio buttons and checkboxes." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_check_style', 'border-width', 'Radio Button Border Size', null, null,
				"The width of the border of radio buttons anc checkboxes. Accepts pixel values. Example: 1px" ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_check_style', 'background-color', 'Background Color', null, null,
				"The background color of the radio buttons and checkboxes when unselected." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_check_style', 'border-color', 'Border Color', null, null,
				"The border color of the radio buttons and checkboxes when unselected." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_radio_label_container:hover input ~ .formlift_check_style', 'background-color', 'Hover Background Color', null, null,
				"The background color of the radio buttons and checkboxes when unselected and being hovered over." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_radio_label_container:hover input ~ .formlift_check_style', 'border-color', 'Hover Border Color', null, null,
				"The border color of the radio buttons and checkboxes when unselected and being hovered over." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_radio_label_container input:checked ~ .formlift_check_style', 'background-color', 'Checked Background Color', null, null,
				"The background color of the radio buttons and checkboxes when checked." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_radio_label_container input:checked ~ .formlift_check_style', 'border-color', 'Checked Border Color', null, null,
				"The border color of the radio buttons and checkboxes when checked." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_radio_label_container .formlift_radio ~ .formlift_check_style:after', 'background-color', 'Checked Dot Color', null, null,
				"The color of the radio buttons' inner dot when checked." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_radio_label_container .formlift_is_checkbox ~ .formlift_check_style:after', 'color', 'Checkmark Color', null, null,
				"The color of the checkboxes' check mark when checked." ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift_radio_option_container', 'display', 'Radio List Display Type', array(
				'block'        => 'List',
				'inline-block' => 'Inline'
			), null,
				"Whether radio buttons appear inline of in a list format." )
		);

		return apply_filters( 'formlift_get_radio_checkbox_css', $fields );
	}

	/**
	 * Returns a list of FormLift_Style_Fields for the form settings section
	 *
	 * @return array
	 */
	public static function get_form_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift-infusion-form', 'background-color', 'Background Color', null, null,
				"The background color of the form." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift-infusion-form', 'border-color', 'Border Color', null, null,
				"The border color of the form." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-infusion-form', 'border-width', 'Border Width', null, null,
				"The width of the border. Accepts pixel values. Example: 3px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-infusion-form', 'border-radius', 'Border Radius', null, null,
				"The roundness of the corners. The higher the value, the rounder. Accepts pixel values. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift-infusion-form', 'border-style', 'Border Style', array(
				'none'   => 'No Border',
				'solid'  => 'Solid Line',
				'dotted' => 'Dotted Line',
				'dashed' => 'Dashed Line',
				'double' => 'Double Line',
				'groove' => 'Grouve Bevel',
				'ridge'  => 'Ridge Bevel',
				'outset' => 'Outset Bevel',
				'inset'  => 'Inset Bevel'
			), null,
				"The style of the form's border." ),
			new FormLift_Style_Field( FORMLIFT_MULTI, 'formlift-infusion-form', 'padding', 'Padding', array(), array(
				'&#8679;' => 'padding-top',
				'&#8680;' => 'padding-right',
				'&#8681;' => 'padding-bottom',
				'&#8678;' => 'padding-left',
			),
				"The spacing around the input fields to the edge to the border of the form. TOP | RIGHT | BOTTOM | LEFT . Accepts pixel values. Example: 5px | 5px | 5px | 5px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-infusion-form', 'width', 'Width', null, null,
				"The width of the form. Accepts pixel or percentage values. Example: 300px or 100%. Best left at 100% for responsiveness." )
		);

		return apply_filters( 'formlift_get_form_css', $fields );
	}

	/**
	 * Returns a list of FormLift_Style_Fields for the field settings section
	 *
	 * @return array
	 */
	public static function get_field_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_field', 'background-color', 'Background Color', null, null,
				"The background color of the fields. Fields are the containers of the inputs and not the inputs themselves." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_field', 'border-color', 'Border Color', null, null,
				"The border color of the fields." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_field', 'border-width', 'Border Width', null, null,
				"The width of the border. Accepts pixel values. Example: 3px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_field', 'border-radius', 'Border Radius', null, null,
				"The roundness of the corners. The higher the value, the rounder. Accepts pixel values. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_SELECT, 'formlift_field', 'border-style', 'Border Style', array(
				'none'   => 'No Border',
				'solid'  => 'Solid Line',
				'dotted' => 'Dotted Line',
				'dashed' => 'Dashed Line',
				'double' => 'Double Line',
				'groove' => 'Grouve Bevel',
				'ridge'  => 'Ridge Bevel',
				'outset' => 'Outset Bevel',
				'inset'  => 'Inset Bevel'
			), null,
				"The style of the fields' border." ),
			new FormLift_Style_Field( FORMLIFT_MULTI, 'formlift_field', 'padding', 'Padding', array(), array(
				'&#8679;' => 'padding-top',
				'&#8680;' => 'padding-right',
				'&#8681;' => 'padding-bottom',
				'&#8678;' => 'padding-left',
			), "The spacing around the input to the edge of the border of the field. TOP | RIGHT | BOTTOM | LEFT . Accepts pixel values. Example: 5px | 5px | 5px | 5px" )
		);

		return apply_filters( 'formlift_get_field_css', $fields );
	}

	/**
	 * Returns a list of FormLift_Style_Fields for the field settings section
	 *
	 * @return array
	 */
	public static function get_label_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift_label', 'color', 'Font Color', null, null,
				"Color of the input labels. This only applies when you are not using the label as placeholder text." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_label', 'font-family', 'Font Family', null, null,
				"The font type you wish to use for the labels. It must already be included in your theme if it's not a web safe font. This only applies when you are not using the label as placeholder text." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_label', 'font-size', 'Font Size', null, null,
				"The font size of the labels. This only applies when you are not using the label as placeholder text. Accepts pixel values. Example: 18px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_label', 'font-weight', 'Font Weight', null, null,
				"The <b>boldness</b> of the label text. Generally accepts the following values: 300, 400, 500, 600, 700, normal, semi-bold, bold. This only applies when you are not using the label as placeholder text." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift_label', 'margin-bottom', 'Bottom Spacing.', null, null,
				"The space between the input and the label. Accepts pixel values only. Example: 5px" )
		);

		return apply_filters( 'formlift_get_label_css', $fields );
	}

	/**
	 * Returns a list of FormLift_Style_Fields for the error settings section
	 *
	 * @return array
	 */
	public static function get_error_css() {
		$fields = array(
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift-error-response', 'background-color', 'Background Color', null, null,
				"Background color of error codes." ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift-error-response', 'border-color', 'Border Color', null, null,
				"Border color of error codes." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-error-response', 'border-radius', 'Border Radius', null, null,
				"Corner roundness of error codes. Accepts pixel values. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-error-response', 'border-width', 'Border Width', null, null,
				"Width of error codes' border. Accepts pixel values. Example: 1px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-error-response', 'padding', 'Text Padding', null, null,
				"Space around error text. Measured top of text to the border. Accepts pixel values. Example: 5px" ),
			new FormLift_Style_Field( FORMLIFT_COLOR, 'formlift-error-response', 'color', 'Font Color', null, null,
				"The error text color." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-error-response', 'font-family', 'Font Family', null, null,
				"The font type you wish to use in the errors. It must already be included in your theme if it's not a web safe font." ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-error-response', 'font-size', 'Font Size', null, null,
				"The text size of errors. Accepts pixel values. Example: 14px" ),
			new FormLift_Style_Field( FORMLIFT_INPUT, 'formlift-error-response', 'font-weight', 'Font Weight', null, null,
				"The <b>boldness</b> of the error text. Generally accepts the following values: 300, 400, 500, 600, 700, normal, semi-bold, bold. This only applies when you are not using the label as placeholder text." )
		);

		return apply_filters( 'formlift_get_error_css', $fields );
	}

	/**
	 * Function that saves the default settings
	 */
	public static function save_settings() {
		if ( isset( $_POST['formlift_options'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {
			$options = $_POST[ FORMLIFT_STYLE ];
			$options = apply_filters( 'formlift_sanitize_style_settings', $options );
			update_option( FORMLIFT_STYLE, $options );
			do_action( 'formlift_after_save_style_settings' );

		}
	}

	public static function clean_settings( $options ) {
		foreach ( $options as $class => $attributes ) {
			foreach ( $attributes as $meta_key => $value ) {
				$options[ $class ][ $meta_key ] = sanitize_text_field( stripslashes( $value ) );
			}
		}

		return $options;
	}

	public static function export_settings() {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['export_style'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {
			$filename = "formlift_style_settings_" . date( "Y-m-d_H-i", time() );

			header( "Content-type: text/plain" );
			//header("Content-disposition: csv" . date("Y-m-d") . ".csv");
			header( "Content-disposition: attachment; filename=" . $filename . ".txt" );
			// do not cache the file
			//header('Pragma: no-cache');
			//header('Expires: 0');

			$file = fopen( 'php://output', 'w' );

			fputs( $file, json_encode( get_option( FORMLIFT_STYLE ) ) );

			// output each row of the data

			fclose( $file );

			exit();
		}
	}

	public static function import_settings() {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['import_style'] ) && wp_verify_nonce( $_POST['formlift_options'], 'update' ) && current_user_can( 'manage_options' ) ) {

			$options = stripslashes( $_POST[ FORMLIFT_SETTINGS ]['import_style_settings'] );

			if ( empty( $options ) ) {
				FormLift_Notice_Manager::add_error( 'bad_import', "No settings to import..." );

				return;
			}

			$options = apply_filters( 'formlift_sanitize_style_settings', json_decode( $options, true ) );
			update_option( FORMLIFT_STYLE, $options );

		}
	}
}

add_filter( 'formlift_sanitize_style_settings', array( 'FormLift_Style_Settings', 'clean_settings' ) );
add_action( 'init', array( 'FormLift_Style_Settings', 'save_settings' ) );
add_action( 'init', array( 'FormLift_Style_Settings', 'export_settings' ) );
add_action( 'init', array( 'FormLift_Style_Settings', 'import_settings' ) );