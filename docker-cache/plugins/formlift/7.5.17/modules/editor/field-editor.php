<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! interface_exists( 'FormLift_Field_Interface' ) ) {
	include_once __DIR__ . '/../lib/field-interface.php';
}

class FormLift_Field_Editor implements FormLift_Field_Interface {
	var $option_key;
	var $id;
	var $name;
	var $type;
	var $value;
	var $label;
	var $options;
	var $placeholder;
	var $placeholder_text;
	var $required;
	var $date_options;
	var $auto_fill;
	var $size;
	var $pre_checked;
	var $remove_flag = false;
	var $readonly;
	var $classes;
	var $loose;
	var $advanced_options = array();

	//var $display_field;

	function __construct( $options ) {
		$this->option_key = FORMLIFT_FIELDS;

		//$this->display_field = new FormLift_Field( $options, get_the_ID() );

		if ( isset( $options['name'] ) ) {
			$this->name = $options['name'];
		}
		if ( isset( $options['type'] ) ) {
			$this->type = $options['type'];
		}
		if ( isset( $options['id'] ) ) {
			$this->id = $options['id'];
		}
		if ( isset( $options['value'] ) ) {
			$this->value = $options['value'];
		}
		if ( isset( $options['label'] ) ) {
			$this->label = $options['label'];
		}
		if ( isset( $options['placeholder'] ) ) {
			$this->placeholder = $options['placeholder'];
		}
		if ( isset( $options['placeholder_text'] ) ) {
			$this->placeholder_text = $options['placeholder_text'];
		}
		if ( isset( $options['required'] ) ) {
			$this->required = $options['required'];
		}
		if ( isset( $options['options'] ) ) {
			$this->options = $options['options'];
		}
		if ( isset( $options['is_loose'] ) ) {
			$this->loose = $options['is_loose'];
		}

		/* for compatibility */
		if ( isset( $options['radio_options'] ) ) {
			$this->options = $options['radio_options'];
		}
		if ( isset( $options['select_options'] ) ) {
			$this->options = $options['select_options'];
		}
		/* end compatibilty */

		if ( isset( $options['date_options'] ) ) {
			$this->date_options = $options['date_options'];
		}
		if ( isset( $options['auto_fill'] ) ) {
			$this->auto_fill = $options['auto_fill'];
		}
		if ( isset( $options['size'] ) ) {
			$this->size = $options['size'];
		}
		if ( isset( $options['pre_checked'] ) ) {
			$this->pre_checked = $options['pre_checked'];
		}
		if ( isset( $options['remove_flag'] ) ) {
			$this->remove_flag = true;
		}
		if ( isset( $options['readonly'] ) ) {
			$this->readonly = true;
		}
		if ( isset( $options['classes'] ) ) {
			$this->classes = $options['classes'];
		}
		/* for class extension with other fields */
		if ( isset( $options['advanced_options'] ) ) {
			$this->advanced_options = $options['advanced_options'];
		}

	}

	public function getFormId() {
		if ( wp_doing_ajax() ) {
			return $_POST['form_id'];
		} else {
			return get_the_ID();
		}
	}

	public function isRequired() {
		return $this->required;
	}

	public function getType() {
		return $this->type;
	}

	public function getName() {
		return ( isset( $this->name ) && ! empty( $this->name ) ) ? $this->name : $this->id;
	}

	public function getLabel() {
		if ( isset( $this->label ) ) {
			return $this->label;
		} else {
			return $this->getName();
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function getId() {
		return $this->id;
	}

	public function getOptions() {
		return $this->options;
	}

	public function isReadOnly() {
		return $this->readonly;
	}

	public function getAdditionalClasses() {
		return $this->classes;
	}

	public function getAdvancedOption( $option ) {
		return ( isset( $this->advanced_options[ $option ] ) ) ? $this->advanced_options[ $option ] : null;
	}

	public function hidden() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_value_field();
		$content .= $this->get_auto_fill_field();

		//$content.= $this->get_advanced_field_options();

		return $content;
	}


	public function text() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
		$content .= $this->get_placeholder_field();
		$content .= $this->get_placeholder_text_field();
		$content .= $this->get_value_field();
		$content .= $this->get_auto_fill_field();
		$content .= $this->get_required_field();
		$content .= $this->get_readonly_field();
		$content .= $this->get_custom_class_field();
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function name() {
		return $this->text();
	}

	public function email() {
		return $this->text();
	}

	public function phone() {
		return $this->text();
	}

	public function number() {
		return $this->text();
	}

	public function postal_code() {
		return $this->text();
	}

	public function zip_code() {
		return $this->text();
	}

	public function website() {
		return $this->text();
	}

	public function checkbox() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
//		$content .= $this->get_placeholder_field();
		$content .= $this->get_value_field();
		$content .= $this->get_pre_checked_field();
		$content .= $this->get_required_field();
		$content .= $this->get_loose_validation_field();
		$content .= $this->get_readonly_field();
		$content .= $this->get_custom_class_field();
		$content .= $this->get_advanced_field_options();

		return $content;

	}

	public function textarea() {
		return $this->text();
	}

	public function password() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
		$content .= $this->get_placeholder_field();
		$content .= $this->get_required_field();
		$content .= $this->get_readonly_field();
		$content .= $this->get_custom_class_field();
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function date() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
		$content .= $this->get_value_field();

		$this->date_options = wp_parse_args( $this->date_options, [
			'format'   => 'yy-mm-dd',
			'min_date' => '',
			'max_date' => ''
		] );

		$dateFormat = ( ! empty( $this->date_options['format'] ) ) ? $this->date_options['format'] : 'yy-mm-dd';

		$content .= $this->wrap_row(
			$this->wrap_label_cell( "<label for=\"{$this->id}-format\">Date Format</label>" ) .
			$this->wrap_input_cell( "<input id=\"{$this->id}-format\" placeholder=\"yy-mm-dd\" type=\"text\" name=\"{$this->option_key}[{$this->id}][date_options][format]\" value=\"{$dateFormat}\"/>" )
			. "<p><a target='_blank' href='http://api.jqueryui.com/datepicker/#utility-formatDate'>See list of valid date formats.</a></p>"
		);

		$content .= $this->wrap_row(
			$this->wrap_label_cell( "<label for=\"{$this->id}-min-date\">Min Date</label>" ) .
			$this->wrap_input_cell( "<input id=\"{$this->id}-min-date\" placeholder=\"YYYY-MM-DD or # DAYS\" type=\"text\" name=\"{$this->option_key}[{$this->id}][date_options][min_date]\" value=\"{$this->date_options['min_date']}\"/>" )
		);

		$content .= $this->wrap_row(
			$this->wrap_label_cell( "<label for=\"{$this->id}-max-date\">Max Date</label>" ) .
			$this->wrap_input_cell( "<input id=\"{$this->id}-max-date\" placeholder=\"YYYY-MM-DD or # DAYS\" type=\"text\" name=\"{$this->option_key}[{$this->id}][date_options][max_date]\" value=\"{$this->date_options['max_date']}\"/>" )
		);

		$checked = ( isset( $this->date_options['show_year'] ) ) ? "checked" : "";
		$content .= $this->wrap_row(
			$this->wrap_label_cell( "<label for='{$this->id}-show-year'>Show Year Picker</label>" ) .
			$this->wrap_input_cell( "<label class=\"switch\"><input id=\"{$this->id}-show-year\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][date_options][show_year]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>" )
		);

		$checked = ( isset( $this->date_options['show_month'] ) ) ? "checked" : "";
		$content .= $this->wrap_row(
			$this->wrap_label_cell( "<label for=\"{$this->id}-show-month\">Show Month Picker</label>" ) .
			$this->wrap_input_cell( "<label class=\"switch\"><input id=\"{$this->id}-show-month\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][date_options][show_month]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>" )
		);

		$content .= $this->get_auto_fill_field();
		$content .= $this->get_placeholder_field();
		$content .= $this->get_placeholder_text_field();
		$content .= $this->get_required_field();
		$content .= $this->get_readonly_field();
		$content .= $this->get_custom_class_field();
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function radio() {
		$option_container = "<div class=\"formlift-option-container formlift-sortable-fields\">";
		$uniqueId         = uniqid( 'option_' );

		if ( empty( $this->options ) ) {
			$row              = "<div class=\"formlift-option-editor\" id=\"$uniqueId-$this->id\" data-field-id=\"$this->id\">";
			$row              .= "<input placeholder=\"label\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][label]\" value=\"\">";
			$row              .= "<input placeholder=\"value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][value]\" value=\"\">";
			$row              .= "<input type=\"radio\" name=\"{$this->option_key}[{$this->id}][pre_checked]\" value=\"$uniqueId\">Selected";
			$row              .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][disabled]\" value=\"1\">Disabled";
			$row              .= "<span class=\"dashicons dashicons-plus formlift-option-add formlift-option-icon\"></span><span class=\"dashicons dashicons-trash formlift-option-icon formlift-option-delete\"></span><span class=\"dashicons dashicons-move formlift-move-icon formlift-option-icon\"></span>";
			$row              .= "</div>";
			$option_container .= $row;
		} else {
			foreach ( $this->options as $radio_option_id => $radio_option_list ) {
				$row = "<div class=\"formlift-option-editor\" id=\"$radio_option_id\" data-field-id=\"$this->id\">";
				$row .= "<input placeholder=\"label\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$radio_option_id][label]\" value=\"{$radio_option_list['label']}\">";
				$row .= "<input placeholder=\"value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$radio_option_id][value]\" value=\"{$radio_option_list['value']}\">";

				$checked = ( isset( $this->pre_checked ) && $this->pre_checked == $radio_option_id ) ? 'checked' : '';
				$row     .= "<input type=\"radio\" name=\"{$this->option_key}[{$this->id}][pre_checked]\" value=\"$radio_option_id\" $checked>Selected";

				$checked = ( isset( $radio_option_list['disabled'] ) ) ? 'checked' : '';
				$row     .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][$radio_option_id][disabled]\" value=\"1\" $checked>Disabled";

				$row              .= "<span class=\"dashicons dashicons-plus formlift-option-add formlift-option-icon\"></span><span class=\"dashicons dashicons-trash formlift-option-icon formlift-option-delete\"></span><span class=\"dashicons dashicons-move formlift-move-icon formlift-option-icon\"></span>";
				$row              .= "</div>";
				$option_container .= $row;
			}
		}

		$option_container .= "</div>";

		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
		$content .= $this->get_auto_fill_field();
		$content .= $this->get_required_field();
		$content .= $this->get_loose_validation_field();
		$content .= $this->get_custom_class_field();
		$content .= self::wrap_row( $option_container );
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function select() {
		$option_container = "<div class=\"formlift-option-container formlift-sortable-fields\">";
		$i                = 0;
		$uniqueId         = uniqid( 'option_' );

		if ( empty( $this->options ) ) {
			$row              = "<div class=\"formlift-option-editor\" id=\"$uniqueId-$this->id\" data-field-id=\"$this->id\">";
			$row              .= "<input placeholder=\"label\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][label]\" value=\"\">";
			$row              .= "<input placeholder=\"value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][value]\" value=\"\">";
			$row              .= "<input type=\"radio\" name=\"{$this->option_key}[{$this->id}][pre_checked]\" value=\"$uniqueId\">Selected";
			$row              .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][disabled]\" value=\"1\">Disabled";
			$row              .= "<span class=\"dashicons dashicons-plus formlift-option-add formlift-option-icon\"></span><span class=\"dashicons dashicons-trash formlift-option-icon formlift-option-delete\"></span><span class=\"dashicons dashicons-move formlift-move-icon formlift-option-icon\"></span>";
			$row              .= "</div>";
			$option_container .= $row;
		} else {
			foreach ( $this->options as $option_num => $select_option_list ) {
				$row = "<div class=\"formlift-option-editor\" id=\"option_{$i}-$this->id\" data-field-id=\"$this->id\">";
				$row .= "<input placeholder=\"label\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][label]\" value=\"{$select_option_list['label']}\">";
				$row .= "<input placeholder=\"value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][value]\" value=\"{$select_option_list['value']}\">";

				$checked = ( isset( $this->pre_checked ) && $this->pre_checked == "option_{$i}" ) ? 'checked' : '';
				$row     .= "<input type=\"radio\" name=\"{$this->option_key}[{$this->id}][pre_checked]\" value=\"option_{$i}\" $checked>Selected";

				$checked = ( isset( $select_option_list['disabled'] ) ) ? 'checked' : '';
				$row     .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][disabled]\" value=\"1\" $checked>Disabled";

				$row              .= "<span class=\"dashicons dashicons-plus formlift-option-add formlift-option-icon\"></span><span class=\"dashicons dashicons-trash formlift-option-icon formlift-option-delete\"></span><span class=\"dashicons dashicons-move formlift-move-icon formlift-option-icon\"></span>";
				$row              .= "</div>";
				$option_container .= $row;
				$i                += 1;
			}
		}
		$option_container .= "</div>";

		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
		$content .= $this->get_auto_fill_field();
		$content .= $this->get_placeholder_field();
		$content .= $this->get_required_field();
		$content .= $this->get_loose_validation_field();
		$content .= $this->get_readonly_field();
		$content .= $this->get_custom_class_field();
		$content .= self::wrap_row( $option_container );
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function listbox() {
		$option_container = "<div class=\"formlift-option-container formlift-sortable-fields\">";
		$i                = 0;
		$uniqueId         = uniqid( 'option_' );

		if ( empty( $this->options ) ) {
			$row              = "<div class=\"formlift-option-editor\" id=\"$uniqueId-$this->id\" data-field-id=\"$this->id\">";
			$row              .= "<input placeholder=\"label\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][label]\" value=\"\">";
			$row              .= "<input placeholder=\"value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][value]\" value=\"\">";
			$row              .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][pre_checked]\" value=\"1\">Selected";
			$row              .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][$uniqueId][disabled]\" value=\"1\">Disabled";
			$row              .= "<span class=\"dashicons dashicons-plus formlift-option-add formlift-option-icon\"></span><span class=\"dashicons dashicons-trash formlift-option-icon formlift-option-delete\"></span><span class=\"dashicons dashicons-move formlift-move-icon formlift-option-icon\"></span>";
			$row              .= "</div>";
			$option_container .= $row;
		} else {
			foreach ( $this->options as $option_num => $select_option_list ) {
				$row = "<div class=\"formlift-option-editor\" id=\"option_{$i}-$this->id\" data-field-id=\"$this->id\">";
				$row .= "<input placeholder=\"label\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][label]\" value=\"{$select_option_list['label']}\">";
				$row .= "<input placeholder=\"value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][value]\" value=\"{$select_option_list['value']}\">";

				$checked = ( isset( $select_option_list['pre_checked'] ) ) ? 'checked' : '';
				$row     .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][pre_checked]\" value=\"1\" $checked>Selected";

				$checked = ( isset( $select_option_list['disabled'] ) ) ? 'checked' : '';
				$row     .= "<input type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][options][option_{$i}][disabled]\" value=\"1\" $checked>Disabled";

				$row              .= "<span class=\"dashicons dashicons-plus formlift-option-add formlift-option-icon\"></span><span class=\"dashicons dashicons-trash formlift-option-icon formlift-option-delete\"></span><span class=\"dashicons dashicons-move formlift-move-icon formlift-option-icon\"></span>";
				$row              .= "</div>";
				$option_container .= $row;
				$i                += 1;
			}
		}
		$option_container .= "</div>";

		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();
		$content .= $this->get_label_field();
		$content .= $this->get_auto_fill_field();
		$content .= $this->get_placeholder_field();
		$content .= $this->get_required_field();
		$content .= $this->get_loose_validation_field();
		$content .= $this->get_readonly_field();
		$content .= $this->get_custom_class_field();;
		$content .= self::wrap_row( $option_container );
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function button() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$label   = "<label for=\"formlift-button-text\">Button Text</label>";
		$input   = "<textarea id=\"formlift-button-text\" name=\"{$this->option_key}[{$this->id}][label]\" cols='40'>$this->label</textarea>";
		$content .= self::wrap_row( self::wrap_label_cell( $label ) . self::wrap_input_cell( $input ) );
		$content .= $this->get_custom_class_field();
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function GDPR() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= $this->get_name_field();

		$sitename = get_bloginfo( 'name' );

		if ( empty( $this->label ) ) {
			$this->label = "I consent to receive promotions, marketing, emails, and the occasional flyer from {$sitename}. I acknowledge that I can unsubscribe at anytime. For more information on how you we use your information, please visit our <a target=\"_blank\" href=\"/privacy-policy/\">Privacy Policy</a>.";
		}

		$label   = "<label for=\"{$this->id}-label\">GDPR Compliancy Text:</label>";
		$input   = "<textarea id=\"{$this->id}-label\" name=\"{$this->option_key}[{$this->id}][label]\" cols='40'>$this->label</textarea>";
		$content .= self::wrap_row( self::wrap_label_cell( $label ) . self::wrap_input_cell( $input ) );

		$label = "<label for=\"{$this->id}-value\">Compliance Value:</label>";

		$value = ( $this->getValue() ) ? $this->value : 'I Consent';
		$input = "<input id=\"{$this->id}-value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][value]\" value=\"{$value}\"/>";

		$content .= self::wrap_row( self::wrap_label_cell( $label ) . self::wrap_input_cell( $input ) );

		$label   = "<label for=\"{$this->id}-eu_only\">Show this field only if the contact is in the EU.</label>";
		$checked = ( isset( $this->advanced_options['eu_only'] ) ) ? "checked" : "";
		$input   = "<label class=\"switch\"><input id=\"{$this->id}-eu_only\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][advanced_options][eu_only]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";
		$content .= $this::wrap_row( $this::wrap_label_cell( $label ) . $this::wrap_input_cell( $input ) );

		$content .= $this->get_required_field();
		$content .= $this->get_custom_class_field();
		$content .= $this->get_advanced_field_options();

		return $content;
	}

	public function custom() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();
		$content .= self::wrap_row( "<textarea id=\"{$this->id}-editor-formlift\" data-editorconvert='true' class='wp-editor' name=\"{$this->option_key}[{$this->id}][value]\" style=\"width: 100%\" rows=\"15\" placeholder=\"Supports HTML and Shortcodes\">$this->value</textarea>" );

		return $content;
	}

	public function template() {
		$content = $this->get_type_field();
		$content .= $this->get_id_field();

		$content = apply_filters( 'formlift_field_editor_template_' . $this->type, $content, $this );

		return $content;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$label = strip_tags( $this->getLabel() );

		if ( strlen( $label ) > 50 ) {
			$label = substr( $label, 0, 50 ) . "...";
		}

		$title = get_formlift_field_type_name( $this->getType() ) . ": " . $label;
		//container 0
		$field = "<div class=\"{$this->get_size()}\" id=\"field-box-$this->id\"><div class=\"formlift-field-box\">";
		//header container 1
		$field .= "<div class=\"formlift-field-header\">";
		//label container 2
		$field .= "<div class=\"formlift-field-box-heading\" title='{$title}'>";
		//label
		$field .= "<span class=\"formlift-heading-text\">$title</span>";
		//end label container 2
		$field .= "</div>";
		//options container 3d
		$field .= "<div class=\"formlift-field-header-options\">";
		//field size button
		$field   .= "<div class='formlift-header-option'>";
		$field   .= "<select class=\"formlift-header-select formlift-switch-width\" data-change-id=\"field-box-$this->id\" id=\"{$this->id}-size-option\" name=\"{$this->option_key}[$this->id][size]\">";
		$options = array(
			'1/1',
			'1/2',
			'1/3',
			'2/3',
			'1/4',
			'3/4'
		);
		foreach ( $options as $value ) {
			if ( $this->size == $value ) {
				$field .= "<option value=\"$value\" selected>$value</option>";
			} else {
				$field .= "<option value=\"$value\">$value</option>";
			}
		}
		$field .= "</select>";
		$field .= "</div>";
		//move button
		$field .= "<div class='formlift-header-option'>";
		$field .= "<a title=\"Edit: {$label}\" href=\"#source_id={$this->id}-content\" class=\"formlift_trigger_popup formlift-header-button\" ><span class=\"dashicons dashicons-edit\"></span></a>";
		$field .= "</div>";

		//add field
		$field .= "<div class='formlift-header-option'>";
		$field .= "<a title=\"Add Custom Field\" href=\"#source_id=custom-field-options\" class=\"add_custom_field formlift_trigger_popup formlift-header-button\" ><span class=\"dashicons dashicons-plus\"></span></a>";
		$field .= "</div>";

		//delete button
		$field .= "<div class='formlift-header-option'>";
		$field .= "<a href=\"javascript:void(0)\" class=\"formlift-header-button\" id=\"{$this->id}-delete\"><span class=\"dashicons dashicons-trash formlift-delete-field\" data-delete-id=\"field-box-$this->id\"></span></a>";
		$field .= "</div>";

		//end options container 3
		$field .= "</div>";
		//end header container 1
		$field .= "</div>";
		//content container
		$field .= "<div id=\"{$this->id}-content\" style=\"display: none\">";

		if ( method_exists( $this, $this->type ) ) {
			$field .= call_user_func( array( $this, $this->type ) );
		} else {
			$field .= $this->template();
		}

		$field .= "</div>";
		//end container 0
		$field .= "</div></div>";

		return $field;
	}

	public function get_size() {
		if ( $this->size == '1/2' ) {
			return 'formlift-col formlift-span_1_of_2';
		} elseif ( $this->size == '1/3' ) {
			return 'formlift-col formlift-span_1_of_3';
		} elseif ( $this->size == '2/3' ) {
			return 'formlift-col formlift-span_2_of_3';
		} elseif ( $this->size == '1/4' ) {
			return 'formlift-col formlift-span_1_of_4';
		} elseif ( $this->size == '3/4' ) {
			return 'formlift-col formlift-span_3_of_4';
		} else {
			return 'formlift-col formlift-span_4_of_4';
		}
	}

	public function get_name_field() {
		$label = "<label for=\"{$this->id}-name\">Field Name</label>";
		//$readonly = (isset($this->name))? 'readonly':'';
//        $input = "<input placeholder=\"required\" id=\"{$this->id}-name\" type=\"text\" name=\"{$this->option_key}[{$this->id}][name]\" value=\"$this->name\" {$readonly} required/>";
		$input   = "<input placeholder=\"required\" id=\"{$this->id}-name\" type=\"text\" name=\"{$this->option_key}[{$this->id}][name]\" value=\"$this->name\" required/>";
		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>The <b>Name</b> field is used to distinguish if the field should be autopopulated with certain data if auto population is enabled. It is also what matches this field to the field in Infusionsoft and should not be changed.</p>";

		return self::wrap_row( $content );
	}

	public function get_id_field() {
		$label    = "<label for=\"{$this->id}-id\">Field ID</label>";
		$readonly = ( isset( $this->id ) ) ? 'readonly' : '';
		$input    = "<input id=\"{$this->id}-id\" type=\"text\" name=\"{$this->option_key}[{$this->id}][id]\" value=\"$this->id\" {$readonly} required/>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>The <b>Id</b> field is used to match fields during form syncing. Should not be changed.</p>";

		return self::wrap_row( $content );
	}

	public function get_type_field() {
		$label = "<label for=\"{$this->id}-type\">Field Type</label>";

		$options = get_formlift_field_types();

		$select = "<select class=\"switch-field-type\" data-change-id=\"$this->id\" id=\"{$this->id}-type\" name=\"{$this->option_key}[{$this->id}][type]\">";
		foreach ( $options as $optionGroupName => $types ) {
			$select .= "<optgroup label=\"$optionGroupName\">";
			foreach ( $types as $type => $type_label ) {
				if ( $type == $this->getType() ) {
					$select .= "<option value=\"$type\" selected>$type_label</option>";
				} else {
					$select .= "<option value=\"$type\">$type_label</option>";
				}
			}
			$select .= "</optgroup>";
		}
		$select  .= "</select>";
		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $select );
		$content .= "<p>The <b>Type</b> is used to switch the field to another field type while maintaining the fields properties such as Name, Id, Options, and rules.</p>";

		return self::wrap_row( $content );
	}

	public function get_placeholder_field() {
		$label   = "<label for=\"{$this->id}-placeholder\">Use label as placeholder text instead</label>";
		$checked = ( isset( $this->placeholder ) ) ? "checked" : "";

		$input = "<label class=\"switch\"><input id=\"{$this->id}-placeholder\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][placeholder]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>This will remove the label from above the field, and use the label text as placeholder text within the field for a clean look.</p>";

		return self::wrap_row( $content );
	}

	public function get_placeholder_text_field() {
		$label = "<label for=\"{$this->id}-placeholder-text\">Show some Placeholder Text</label>";
		$input = "<input id=\"{$this->id}-placeholder-text\" type=\"text\" name=\"{$this->option_key}[{$this->id}][placeholder_text]\" value=\"$this->placeholder_text\"/>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>If labels are enabled, then you can specify example inputs using this setting.</p>";

		return self::wrap_row( $content );
	}

	public function get_value_field() {
		$label = "<label for=\"{$this->id}-value\">Set A Default Value</label>";
		$input = "<input id=\"{$this->id}-value\" type=\"text\" name=\"{$this->option_key}[{$this->id}][value]\" value=\"$this->value\"/>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>You may enter a default value here. If auto population is enabled for this field, and data exists to perform the auto-population, then the default value will be overwritten.<p>";

		return self::wrap_row( $content );
	}

	public function get_label_field() {
		$label = "<label for=\"{$this->id}-label\">Field Label</label>";
		$input = "<textarea id=\"{$this->id}-label\" name=\"{$this->option_key}[{$this->id}][label]\" cols='40'>$this->label</textarea>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>The label appears above the field with the given text, or if you have <b>label as placeholder</b> enabled it will appear as placeholder text within the field.<p>";

		return self::wrap_row( $content );
	}

	public function get_custom_class_field() {
		$label   = "<label for=\"{$this->id}-classes\">Add Custom CSS Classes</label>";
		$input   = "<input id=\"{$this->id}-classes\" type=\"text\" name=\"{$this->option_key}[{$this->id}][classes]\" value=\"$this->classes\"/>";
		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>The classes get added to the <b>field container</b> rather than the input area itself. That way you can specify special classes for the fields and labels within easier.</p>";

		return self::wrap_row( $content );
	}

	public function get_readonly_field() {
		$label   = "<label for=\"{$this->id}-readonly\">Make this field readonly</label>";
		$checked = ( isset( $this->readonly ) ) ? "checked" : "";
		$input   = "<label class=\"switch\"><input id=\"{$this->id}-readonly\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][readonly]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>If you do not want to allow users to edit the contents of this field then you may set it to readonly.<p>";

		return self::wrap_row( $content );
	}

	public function get_loose_validation_field() {
		$label   = "<label for=\"{$this->id}-loose\">Disable strict validation.</label>";
		$checked = ( isset( $this->loose ) ) ? "checked" : "";
		$input   = "<label class=\"switch\"><input id=\"{$this->id}-loose\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][is_loose]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>This will allow you to have multiple radio options of the same with the same <b>Field Name</b> and still pass validation if at least one of them is selected. This will not be relevant to you in most cases and is for advanced customization.<p>";

		return self::wrap_row( $content );
	}

	public function get_required_field() {
		$label   = "<label for=\"{$this->id}-required\">Make This Field Required</label>";
		$checked = ( isset( $this->required ) ) ? "checked" : "";

		$input = "<label class=\"switch\"><input id=\"{$this->id}-required\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][required]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>This will make the field required.<p>";

		return self::wrap_row( $content );
	}

	public function get_pre_checked_field() {
		$label   = "<label for=\"{$this->id}-pre_checked\">Pre-Check this field</label>";
		$checked = ( isset( $this->pre_checked ) ) ? "checked" : "";
		$input   = "<label class=\"switch\"><input id=\"{$this->id}-pre_checked\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][pre_checked]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>This will pre-check the field. Users will still be able to uncheck it.<p>";

		return self::wrap_row( $content );
	}

	public function get_auto_fill_field() {
		$label   = "<label for=\"{$this->id}-auto\">Auto populate this field</label>";
		$checked = ( isset( $this->auto_fill ) ) ? "checked" : "";
		$input   = "<label class=\"switch\"><input id=\"{$this->id}-auto\" type=\"checkbox\" name=\"{$this->option_key}[{$this->id}][auto_fill]\" value=\"true\" $checked/><span class=\"formlift-slider - round\"></span></label>";

		$content = self::wrap_label_cell( $label ) . self::wrap_input_cell( $input );
		$content .= "<p>This will allow the field to auto-populate with user data if it previously exists or is passed to the page via email. Any default value set will be overwritten if auto-populated data exists.<p>";

		return self::wrap_row( $content );
	}

	public function get_advanced_field_options() {
		$content = "";
		$content .= apply_filters( 'formlift_advanced_field_options', $content, $this );

		//$content.= apply_filters( 'formlift_advanced_field_options_' . $this->type, $content, $this );
		return $content;
	}

	/**
	 * Wraps label and input in editable row.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public static function wrap_row( $content ) {
		return "<div class=\"formlift-row\">$content</div>";
	}

	/**
	 * Wraps the label element returned by the above functions in a cell
	 *
	 * @param $content string
	 *
	 * @return string
	 */
	public static function wrap_label_cell( $content ) {
		return "<div class=\"formlift-cell formlift-cell-label formlift-builder-cell\">$content</div>";
	}

	/**
	 * Wraps the input element returned by the above functions in a cell
	 *
	 * @param $content string
	 *
	 * @return string
	 */
	public static function wrap_input_cell( $content ) {
		return "<div class=\"formlift-cell formlift-cell-input\">$content</div>";
	}
}