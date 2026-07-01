<?php
/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2018-04-10
 * Time: 7:41 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! interface_exists( 'FormLift_Field_Interface' ) ) {
	include_once __DIR__ . '/../lib/field-interface.php';
}

class FormLift_Validator implements FormLift_Field_Interface {

	var $id;
	var $name;
	var $type;
	var $value;
	var $options;
	var $required = false;
	var $formId;
	var $message;
	var $loose = false;

	function __construct( $options, $formId ) {
		$this->type = $options['type'];

		if ( isset( $options['name'] ) ) {
			$this->name = $options['name'];
		}

		if ( isset( $options['id'] ) ) {
			$this->id = $options['id'];
		}

		if ( isset( $options['options'] ) ) {
			$this->options = $options['options'];
		}

		if ( isset( $options['value'] ) ) {
			$this->value = $options['value'];
		}

		if ( isset( $options['required'] ) ) {
			$this->required = true;
		}

		if ( isset( $options['is_loose'] ) ) {
			$this->loose = $options['is_loose'];
		}

		$this->formId = $formId;
	}

	public function getErrorMessage() {
		return $this->message;
	}

	public function setErrorMessage( $key_or_message ) {
		$messages = get_post_meta( $this->formId, FORMLIFT_SETTINGS, true );
		if ( isset( $messages[ $key_or_message ] ) ) {
			$message = $messages[ $key_or_message ];
		}

		if ( empty( $message ) ) {
			$message = get_formlift_setting( $key_or_message, false );
		}

		if ( empty( $message ) ) {
			$message = $key_or_message;
		}

		$this->message = $message;

		if ( ! wp_doing_ajax() ) {
			add_filter( 'formlift_field_preload_has_error_' . $this->getId(), array( $this, 'getErrorMessage' ) );
		}

		return $this->message;
	}

	public function isRequired() {
		return $this->required;
	}

	public function isStrict() {
		return ! $this->loose;
	}

	public function getType() {
		return $this->type;
	}

	public function getName() {
		return ( isset( $this->name ) ) ? $this->name : $this->id;
	}

	public function getLabel() {
		return $this->id;
	}

	public function getValue() {
		return $this->value;
	}

	public function getId() {
		return $this->id;
	}

	public function hidden() {
		return true;
	}

	public function text() {
		if ( $this->dataExists() ) {
			return true;
		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function email() {
		if ( $this->dataExists() ) {

			if ( ! filter_var( $this->getData(), FILTER_VALIDATE_EMAIL ) ) {
				return $this->setErrorMessage( 'email_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function name() {
		if ( $this->dataExists() ) {

			if ( ! preg_match( "/^[a-zA-Z'\- ]*$/", $this->getData() ) ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function number() {
		if ( $this->dataExists() ) {

			if ( ! preg_match( "/^[1-9]*$/", $this->getData() ) ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function phone() {
		if ( $this->dataExists() ) {

			if ( ! preg_match( "/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im", $this->getData() ) ) {
				return $this->setErrorMessage( 'phone_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function textarea() {
		return $this->text();
	}

	public function select() {
		if ( $this->dataExists() && $this->isStrict() ) {

			$found = false;

			foreach ( $this->options as $optionId => $options ) {
				if ( $this->getData() == $options['value'] ) {
					$found = true;
					break;
				}
			}

			if ( ! $found ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function listbox() {
		return $this->select();
	}

	public function radio() {
		if ( ( $this->dataExists() || $this->getData() === 0 ) && $this->isStrict() ) {

			$found = false;

			foreach ( $this->options as $optionId => $options ) {
				if ( $this->getData() == $options['value'] ) {
					$found = true;
					break;
				}
			}

			if ( ! $found ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function date() {
		if ( $this->dataExists() ) {

			$d = DateTime::createFromFormat( 'Y-m-d', $this->getData() );

			if ( $d && $d->format( 'Y-m-d' ) !== $this->getData() ) {
				return $this->setErrorMessage( 'date_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function checkbox() {
		if ( $this->dataExists() && $this->isStrict() ) {

			if ( $this->getData() !== $this->getValue() ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function button() {
		return true;
	}

	public function GDPR() {
		if ( $this->dataExists() ) {

			if ( $this->getData() !== "I Consent" ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function custom() {
		return true;
	}

	public function password() {
		if ( $this->dataExists() ) {
			if ( $this->getName() === 'inf_other_Password' ) {
				if ( ! isset( $_POST['inf_other_Password'] ) || ! isset( $_POST['inf_other_RetypePassword'] ) || $_POST['inf_other_Password'] != $_POST['inf_other_RetypePassword'] ) {
					return $this->setErrorMessage( 'password_error' );
				} else {
					return true;
				}
			} else {
				return true;
			}
		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function website() {
		if ( $this->dataExists() ) {

			if ( ! filter_var( $this->getData(), FILTER_VALIDATE_URL ) ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function postal_code() {
		if ( $this->dataExists() ) {

			if ( ! preg_match( "/[ABCEGHJKLMNPRSTVXY][0-9][ABCEGHJKLMNPRSTVWXYZ] ?[0-9][ABCEGHJKLMNPRSTVWXYZ][0-9]/i", $this->getData() ) ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}

		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function zip_code() {
		if ( $this->dataExists() ) {

			if ( ! preg_match( "/^\d{5}(?:[-\s]\d{4})?$/", $this->getData() ) ) {
				return $this->setErrorMessage( 'invalid_data_error' );
			} else {
				return true;
			}
		} else if ( $this->isRequired() ) {
			return $this->setErrorMessage( 'required_error' );
		} else {
			return true;
		}
	}

	public function template() {
		return apply_filters( 'formlift_custom_field_validation_' . $this->getType(), $this );
	}

	public function dataExists( $name = false ) {
		if ( ! $name ) {
			$name = $this->getName();
		}

		$not_empty = ! empty( $_POST[ $name ] );
		$isset     = isset( $_POST[ $name ] );

		return $isset && $not_empty;
	}

	public function getData( $name = false ) {
		if ( ! $name ) {
			$name = $this->getName();
		}
		if ( isset ( $_POST[ $name ] ) ) {
			if ( is_string( $_POST[ $name ] ) ) {
				return sanitize_textarea_field( stripslashes( $_POST[ $name ] ) );
			} else {
				return array_map( 'sanitize_text_field', $_POST[ $name ] );
			}
		} else {
			return null;
		}
	}

	public function isValid() {
		if ( method_exists( $this, $this->getType() ) ) {
			$valid = call_user_func( array( $this, $this->getType() ) );
		} else {
			$valid = $this->template();
		}

		/* bit of a hack but hey whatever */
		if ( has_filter( 'formlift_extra_custom_validation' ) ) {
			$additionalCheck = apply_filters( 'formlift_extra_custom_validation', $this );
		} else {
			$additionalCheck = true;
		}

		if ( $valid !== true ) {
			return new WP_Error( '1', $valid );
		} else if ( $additionalCheck !== true ) {
			return new WP_Error( '1', $additionalCheck );
		} else {
			return $valid;
		}
	}

	public function __toString() {
		return $this->getName();
	}
}