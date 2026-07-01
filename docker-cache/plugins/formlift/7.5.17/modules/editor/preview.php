<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Preview {
	public static function add_meta_box() {
		add_meta_box(
			"infusion_preview",
			"Preview Form",
			array( 'FormLift_Preview', "meta_box_callback" ),
			"infusion_form",
			"side",
			"default"
		);
	}

	public static function meta_box_callback( $post ) {
		$form = new FormLift_Form( $post->ID );

		$fields = $form->get_fields();

		if ( empty( $fields ) ) {
			echo "Import a form before you can preview anything!";

			return;
		}

		$the_content = $form->get_style_sheet();
		$the_content .= $form->get_preview_form();
		echo $the_content;
	}
}

add_action( 'add_meta_boxes', array( 'FormLift_Preview', 'add_meta_box' ) );