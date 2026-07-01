<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! interface_exists( 'FormLift_Field_Interface' ) ):


	interface FormLift_Field_Interface {
		public function getId();

		public function getName();

		public function isRequired();

		public function getValue();

		public function getType();

		public function getLabel();

		public function hidden();

		public function name();

		public function text();

		public function email();

		public function phone();

		public function textarea();

		public function select();

		public function listbox();

		public function date();

		public function radio();

		public function checkbox();

		public function button();

		public function GDPR();

		public function custom();

		public function password();

		public function website();

		public function postal_code();

		public function template();

		public function zip_code();

		public function __toString();
	}

endif;