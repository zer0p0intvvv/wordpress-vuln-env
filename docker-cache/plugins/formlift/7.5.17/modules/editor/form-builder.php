<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class FormLift_Form_Builder
 *
 * this takes an imported Form's HTML and parses it to produce the relevant array of Form Fields
 * also included are several functions pertaining to the daving of the form's data
 */
class FormLift_Form_Builder {
	/**
	 * Parses the HTML from either the HTML editor or the API code and creates the array that will load the form.
	 * and structure.
	 *
	 * @param $code
	 *
	 * @return mixed
	 */
	public static function parse_html( $code ) {

		/* if something is wrong with the code return ERROR code */
		if ( empty( $code ) || ! isset( $code ) ) {
			return new WP_Error( 'FORM_RETRIEVE_FAILED', 'No form code was supplied...' );
		} else {
			do_action( 'formlift_before_form_importing' );

			libxml_use_internal_errors( true );
			/* create a doc to import the form code from infusionsoft*/
			//$doc = new DOMDocument("1.0", "UFT-8");
			$doc = new DOMDocument();

			$doc->loadHTML( $code );

			libxml_use_internal_errors( false );

			/* find the top DOM element of the form */
			$startingNode = $doc->getElementsByTagName( 'form' )->item( 0 );

			if ( $startingNode === null ) {
				return new WP_Error( 'FORM_BUILD_FAILED', 'Something went wrong importing your form. A <code>&lt;form&gt;</code> tag was not found in the imported HTML. This means your form is incomplete in Infusionsoft.' );
			}
			/* remove extraneuous HTML */
			$doc->saveHTML( $startingNode );

			/* get the submisttion URL */
			$post_url = $startingNode->getAttribute( 'action' );
			set_transient( 'formlift_post_url', $post_url, 60 );

			/* traverse the HTML to find relevant fields */
			$domTree = new RecursiveIteratorIterator(
				new RecursiveDOMIterator( $doc ),
				RecursiveIteratorIterator::SELF_FIRST
			);

			$form_bits = array();

			/**
			 * Iterate recursively over the DOM tree
			 *
			 * @param $inputNode DOMElement
			 */
			foreach ( $domTree as $node ) {

				if ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'input' && ( $node->getAttribute( 'type' ) == 'text' || $node->getAttribute( 'type' ) == 'tel' || $node->getAttribute( 'type' ) == 'email' ) ) {
					/* get the label */
					$label = self::get_associated_label_element( $node->getAttribute( 'name' ), $doc );
					/* set the standard fields */
					$field = array(
						'id'        => sanitize_text_field( $node->getAttribute( 'id' ) ),
						'name'      => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'label'     => sanitize_text_field( $label ),
						'auto_fill' => true
					);
					/* check for special attributions */
					if ( $node->hasAttribute( 'onkeydown' ) ) {
						$field['type'] = 'date';
					} elseif ( preg_match( '/( url|website )/i', $node->getAttribute( 'name' ) ) ) {
						$field['type'] = 'website';
					} elseif ( preg_match( '/phone/i', $node->getAttribute( 'name' ) ) ) {
						$field['type'] = 'phone';
					} elseif ( preg_match( '/(FirstName|LastName)/i', $node->getAttribute( 'name' ) ) ) {
						$field['type'] = 'name';
					} elseif ( preg_match( '/Email/i', $node->getAttribute( 'name' ) ) ) {
						$field['type'] = 'email';
					} else {
						$field['type'] = 'text';
					}
					/* check to see if it's a required field In infusionsoft */
					if ( preg_match( '/\*/', $label ) ) {
						$field['required'] = 'required';
					}

					$form_bits[ $field['id'] ] = $field;

				} /* Check to see if it's a checkbox field */
				elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'input' && $node->getAttribute( 'type' ) == 'checkbox' ) {
					/* get the label */
					$label = self::get_associated_label_element( $node->getAttribute( 'id' ), $doc );
					/* set standard fields */
					$field = array(
						'id'              => sanitize_text_field( $node->getAttribute( 'id' ) ),
						'name'            => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'label'           => sanitize_text_field( $label ),
						'value'           => sanitize_text_field( $node->getAttribute( 'value' ) ),
						'type'            => 'checkbox',
						'validation_type' => 'none'
					);

					if ( preg_match( '/\*/', $label ) ) {
						$field['required'] = 'required';
					}

					$form_bits[ $field['id'] ] = $field;
				} /* Check to see if it's a password field */
				elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'input' && $node->getAttribute( 'type' ) == 'password' ) {
					/* get the label */
					$label = self::get_associated_label_element( $node->getAttribute( 'id' ), $doc );
					/* set standard fields */
					$field = array(
						'id'    => sanitize_text_field( $node->getAttribute( 'id' ) ),
						'name'  => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'label' => sanitize_text_field( $label ),
						'value' => sanitize_text_field( $node->getAttribute( 'value' ) ),
						'type'  => 'password'
					);

					if ( preg_match( '/\*/', $label ) ) {
						$field['required'] = 'required';
					}

					$form_bits[ $field['id'] ] = $field;
				} /* check to see if it's a hidden field */
				elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'input' && $node->getAttribute( 'type' ) == 'hidden' ) {

					$field = array(
						'id'        => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'type'      => sanitize_text_field( 'hidden' ),
						'name'      => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'value'     => sanitize_text_field( $node->getAttribute( 'value' ) ),
						'auto_fill' => true
					);

					if ( $field['name'] == 'inf_form_xid' || $field['name'] == 'inf_form_name' || $field['name'] == 'infusionsoft_version' ) {
						set_transient( $field['name'], $field['value'], 30 );
					} else {
						$form_bits[ $field['id'] ] = $field;
					}
					/* check to see if it's a BUTTON */
				} elseif ( $node->nodeType === XML_ELEMENT_NODE && ( ( $node->tagName == 'input' && $node->getAttribute( 'type' ) == 'submit' ) || $node->tagName == 'button' ) ) {

					$field                     = array(
						'id'    => 'submit_button',
						'type'  => 'button',
						'label' => sanitize_text_field( $node->textContent ),
					);
					$form_bits[ $field['id'] ] = $field;

				} elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'img' && $node->hasAttribute( 'onclick' ) ) {
					$field                     = array(
						'id'    => 'captcha_' . random_int( 0, 1000 ),
						'type'  => 'custom',
						'value' => $node->ownerDocument->saveHTML( $node )
					);
					$form_bits[ $field['id'] ] = $field;
				} elseif ( $node->nodeType === XML_ELEMENT_NODE && ( preg_match( '/^(img|h1|h2|h3|h4|h5|p)$/', $node->tagName ) || ( preg_match( '/^(script)$/', $node->tagName ) && preg_match( '/reloadJcaptcha\(\)/', $node->ownerDocument->saveHTML( $node ) ) ) ) ) {
					$field                     = array(
						'id'    => 'custom_html_' . random_int( 0, 1000 ),
						'type'  => 'custom',
						'value' => $node->ownerDocument->saveHTML( $node )
					);
					$form_bits[ $field['id'] ] = $field;
				} elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'input' && $node->getAttribute( 'type' ) == 'radio' ) {
					if ( isset( $form_bits[ $node->getAttribute( 'name' ) ] ) ) {
						$form_bits[ sanitize_text_field( $node->getAttribute( 'name' ) ) ]['options'][ sanitize_text_field( $node->getAttribute( 'id' ) ) ]['name']  = sanitize_text_field( $node->getAttribute( 'name' ) );
						$form_bits[ sanitize_text_field( $node->getAttribute( 'name' ) ) ]['options'][ sanitize_text_field( $node->getAttribute( 'id' ) ) ]['value'] = sanitize_text_field( $node->getAttribute( 'value' ) );
						$form_bits[ sanitize_text_field( $node->getAttribute( 'name' ) ) ]['options'][ sanitize_text_field( $node->getAttribute( 'id' ) ) ]['label'] = sanitize_text_field( self::get_associated_label_element( $node->getAttribute( 'id' ), $doc ) );
					} else {
						$main_label = self::get_associated_label_element( $node->getAttribute( 'name' ), $doc );
						$field      = array(
							'id'    => sanitize_text_field( $node->getAttribute( 'name' ) ),
							'name'  => sanitize_text_field( $node->getAttribute( 'name' ) ),
							'type'  => 'radio',
							'label' => sanitize_text_field( $main_label ),
						);
						if ( preg_match( '/\*/', $main_label ) ) {
							$field['required'] = 'required';
						}

						$field['options'][ sanitize_text_field( $node->getAttribute( 'id' ) ) ]['name']  = sanitize_text_field( $node->getAttribute( 'name' ) );
						$field['options'][ sanitize_text_field( $node->getAttribute( 'id' ) ) ]['value'] = sanitize_text_field( $node->getAttribute( 'value' ) );
						$field['options'][ sanitize_text_field( $node->getAttribute( 'id' ) ) ]['label'] = sanitize_text_field( self::get_associated_label_element( $node->getAttribute( 'id' ), $doc ) );

						$form_bits[ sanitize_text_field( $node->getAttribute( 'name' ) ) ] = $field;
					}
				} elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'textarea' ) {

					$label = self::get_associated_label_element( $node->getAttribute( 'name' ), $doc );
					$field = array(
						'id'        => sanitize_text_field( $node->getAttribute( 'id' ) ),
						'type'      => 'textarea',
						'name'      => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'label'     => sanitize_text_field( $label ),
						'auto_fill' => true
					);
					if ( preg_match( '/\*/', $label ) ) {
						$field['required'] = 'required';
					}
					$form_bits[ $field['id'] ] = $field;

				} elseif ( $node->nodeType === XML_ELEMENT_NODE && $node->tagName == 'select' ) {

					$label = self::get_associated_label_element( $node->getAttribute( 'id' ), $doc );
					$field = array(
						'id'        => sanitize_text_field( $node->getAttribute( 'id' ) ),
						'type'      => 'select',
						'name'      => sanitize_text_field( $node->getAttribute( 'name' ) ),
						'label'     => sanitize_text_field( $label ),
						'auto_fill' => true
					);

					$selectTree = new RecursiveIteratorIterator(
						new RecursiveDOMIterator( $node ),
						RecursiveIteratorIterator::SELF_FIRST
					);
					$i          = 0;

					foreach ( $selectTree as $selectNode ) {
						if ( $selectNode->nodeType === XML_ELEMENT_NODE && $selectNode->tagName == 'option' ) {
							$field['options']["option_$i"]['value'] = sanitize_text_field( $selectNode->getAttribute( 'value' ) );
							$field['options']["option_$i"]['label'] = sanitize_text_field( $selectNode->textContent );
							$i                                      += 1;
						}
					}

					if ( preg_match( '/\*/', $label ) ) {
						$field['required'] = 'required';
					}

					$form_bits[ $field['id'] ] = $field;
				}
			}
		}

		return $form_bits;
	}


	/**
	 * Gets the label element of a given input element
	 *
	 * @param $text string
	 * @param $doc  DOMDocument
	 *
	 * @return string
	 */
	public static function get_associated_label_element( $text, $doc ) {
		$domTree = new RecursiveIteratorIterator(
			new RecursiveDOMIterator( $doc ),
			RecursiveIteratorIterator::SELF_FIRST
		);
		foreach ( $domTree as $node ) {
			if ( $node->nodeType === XML_ELEMENT_NODE && $node->hasAttribute( 'for' ) && $node->getAttribute( 'for' ) == $text ) {
				return $node->textContent;
			}
		}

		return '';
	}

	/**
	 * Imports the from the API and runs the cleaning algorithm
	 *
	 * @param $post_id
	 */
	public static function save_form( $post_id ) {
		if ( isset( $_POST[ FORMLIFT_SETTINGS ]['form_refresh'] ) || isset( $_POST[ FORMLIFT_SETTINGS ]['form_sync'] ) ) {

			$form_id = absint( $_POST[ FORMLIFT_SETTINGS ]['infusionsoft_form_id'] );

			$temp_code = get_formlift_html( $form_id );
			$form_bits = self::parse_html( $temp_code );

			$form_name = formlift_get_webform_name( $form_id );

			global $wpdb;

			$wpdb->update( $wpdb->posts, [
				'post_title' => sanitize_text_field( $form_name )
			], [
				'ID' => $post_id
			] );

			if ( is_wp_error( $form_bits ) ) {
				FormLift_Notice_Manager::add_notice(
					$form_bits->get_error_code(),
					array(
						'type' => FORMLIFT_NOTICE_ERROR,
						'html' => $form_bits->get_error_message()
					)
				);
			}
			if ( isset( $_POST[ FORMLIFT_SETTINGS ]['form_refresh'] ) ) {
				update_post_meta( $post_id, FORMLIFT_FIELDS, self::sanitize_fields( $form_bits ) );
			} else if ( isset( $_POST[ FORMLIFT_SETTINGS ]['form_sync'] ) ) {
				$old_bits = $_POST[ FORMLIFT_FIELDS ];
				/* only add new fields, thus overwrite new bits with old bits and leave outstanding bots alone*/
				$new_bits = array_merge( $form_bits, $old_bits );
				update_post_meta( $post_id, FORMLIFT_FIELDS, self::sanitize_fields( $new_bits ) );
			}
		} else if ( isset( $_POST[ FORMLIFT_SETTINGS ]['parse_original_html'] ) ) {
			$code = $_POST[ FORMLIFT_SETTINGS ]['infusionsoft_form_original_html'];

			$code = stripslashes( $code );

			$code = preg_replace_callback( '/[\x{80}-\x{10FFFF}]/u', function ( $match ) {
				list( $utf8 ) = $match;
				$entity = mb_convert_encoding( $utf8, 'HTML-ENTITIES', 'UTF-8' );

				return $entity;
			}, $code );

			$form_bits = self::parse_html( $code );
			if ( is_wp_error( $form_bits ) ) {
				FormLift_Notice_Manager::add_notice(
					$form_bits->get_error_code(),
					array(
						'type' => FORMLIFT_NOTICE_ERROR,
						'html' => $form_bits->get_error_message()
					)
				);
			} else {
				update_post_meta( $post_id, FORMLIFT_FIELDS, self::sanitize_fields( $form_bits ) );
			}
		} else if ( isset( $_POST[ FORMLIFT_FIELDS ] ) ) {
			update_post_meta( $post_id, FORMLIFT_FIELDS, self::sanitize_fields( $_POST[ FORMLIFT_FIELDS ] ) );
		}
	}

	public static function sanitize_fields( $fields ) {

		if ( is_wp_error( $fields ) ) {
			return [];
		}

		foreach ( $fields as $fieldId => $fieldOptions ) {

			$fields[ $fieldId ]['type'] = sanitize_text_field( $fieldOptions['type'] );
			$fields[ $fieldId ]['id']   = sanitize_text_field( $fieldOptions['id'] );
			if ( isset( $fields[ $fieldId ]['name'] ) ):
				$fields[ $fieldId ]['name'] = sanitize_text_field( $fieldOptions['name'] );
			endif;
			if ( isset( $fields[ $fieldId ]['label'] ) ):
				$allowedTags                 = wp_kses_allowed_html();
				$allowedTags['a']['target']  = true;
				$fields[ $fieldId ]['label'] = wp_kses( $fieldOptions['label'], $allowedTags );
			endif;
			if ( isset( $fields[ $fieldId ]['value'] ) ):
				if ( $fields[ $fieldId ]['type'] == 'custom' ) {
					$fields[ $fieldId ]['value'] = wp_kses( $fieldOptions['value'], wp_kses_allowed_html( 'post' ) );
				} else {
					$fields[ $fieldId ]['value'] = sanitize_text_field( $fieldOptions['value'] );
				}
			endif;
			if ( isset( $fields[ $fieldId ]['auto_fill'] ) ):
				$fields[ $fieldId ]['auto_fill'] = sanitize_text_field( $fieldOptions['auto_fill'] );
			endif;
			if ( isset( $fields[ $fieldId ]['placeholder'] ) ):
				$fields[ $fieldId ]['placeholder'] = sanitize_text_field( $fieldOptions['placeholder'] );
			endif;
			if ( isset( $fields[ $fieldId ]['placeholder_text'] ) ):
				$fields[ $fieldId ]['placeholder_text'] = sanitize_text_field( $fieldOptions['placeholder_text'] );
			endif;
			if ( isset( $fields[ $fieldId ]['required'] ) ):
				$fields[ $fieldId ]['required'] = sanitize_text_field( $fieldOptions['required'] );
			endif;
			if ( isset( $fields[ $fieldId ]['size'] ) ):
				$fields[ $fieldId ]['size'] = sanitize_text_field( $fieldOptions['size'] );
			endif;
			if ( isset( $fields[ $fieldId ]['readonly'] ) ):
				$fields[ $fieldId ]['readonly'] = sanitize_text_field( $fieldOptions['readonly'] );
			endif;
			if ( isset( $fields[ $fieldId ]['classes'] ) ):
				$fields[ $fieldId ]['classes'] = sanitize_text_field( $fieldOptions['classes'] );
			endif;
		}

		return $fields;
	}


	/**
	 * Gets the form settings of another form and replaces  the settings of this form with them
	 *
	 * @param $post_id
	 */
	public static function import_form_settings( $post_id ) {
		if ( isset( $_POST[ FORMLIFT_FIELDS ]['do_form_import'] ) ) {
			$settings = get_post_meta( $_POST[ FORMLIFT_FIELDS ]['import_form_id'], FORMLIFT_STYLE, true );
			update_post_meta( $post_id, FORMLIFT_STYLE, $settings );
		}
	}

	/**
	 * Save the settings panel settings
	 *
	 * @param $post_id
	 */
	public static function save_settings( $post_id ) {
		if ( ! isset( $_POST['formlift_settings_nonce'] ) || ! wp_verify_nonce( $_POST['formlift_settings_nonce'], 'formlift_saving_settings' ) ) {
			wp_die( 'Invalid credentials...' );
		}

		if ( ! empty( $_POST[ FORMLIFT_SETTINGS ] ) ) {
			$form_settings = apply_filters( 'formlift_sanitize_form_settings', $_POST[ FORMLIFT_SETTINGS ] );
			update_post_meta( $post_id, FORMLIFT_SETTINGS, $form_settings );
		} else {
			delete_post_meta( $post_id, FORMLIFT_SETTINGS );
		}

		if ( get_transient( 'formlift_post_url' ) ) {
			$form_settings             = get_post_meta( $post_id, FORMLIFT_SETTINGS, true );
			$form_settings['post_url'] = esc_url( get_transient( 'formlift_post_url' ) );
			update_post_meta( $post_id, FORMLIFT_SETTINGS, $form_settings );
			delete_transient( 'formlift_post_url' );
		}

		$specialFields = array(
			'inf_form_xid',
			'inf_form_name',
			'infusionsoft_version'
		);

		foreach ( $specialFields as $key ) {
			if ( get_transient( $key ) ) {
				update_post_meta( $post_id, $key, esc_attr( get_transient( $key ) ) );
				delete_transient( $key );
			}
		}
	}
}

add_action( 'formlift_before_save_form', array( 'FormLift_Form_Builder', 'save_form' ) );
add_action( 'formlift_before_save_form', array( 'FormLift_Form_Builder', 'save_settings' ) );
add_action( 'formlift_before_save_form', array( 'FormLift_Form_Builder', 'import_form_settings' ) );