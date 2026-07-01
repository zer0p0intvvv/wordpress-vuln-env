<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

function formlift_submitV2() {
	if ( ! isset( $_POST['action'] ) || $_POST['action'] != 'formlift_submit_form' ) {
		return;
	}

	global $FormLiftUser;

	$formId = $_POST["form_id"];

	do_action( 'formlift_before_submit', $formId );

	$fields = get_post_meta( $formId, FORMLIFT_FIELDS, true );

	$errors = array();

	//clear the cache from any previous submission.
	$FormLiftUser->remove_user_data( 'form_data' );

	$errors = apply_filters( 'formlift_before_field_validation', $errors );

	/**
	 * @var $fieldValidation FormLift_Validator
	 */

	foreach ( $fields as $field_options ) {
		$validator = new FormLift_Validator( $field_options, $formId );
		$isValid   = $validator->isValid();
		if ( is_wp_error( $isValid ) ) {
			$errors[ $validator->getId() ] = $isValid->get_error_message();
		} else {
			if ( isset( $field_options['name'] ) ) {
				if ( $validator->dataExists() ) {
					$FormLiftUser->set_user_data( $validator->getName(), $validator->getData() );
					# For compatibility mode!
					if ( formlift_get_form_setting( $formId, 'enable_compatibility_mode', false ) ) {
						$packet                          = $FormLiftUser->get_user_data( 'form_data', array() );
						$packet[ $validator->getName() ] = $validator->getData();
						$FormLiftUser->set_user_data( 'form_data', $packet );
					}
				} else {
					/* honor blank field submission */
					$FormLiftUser->remove_user_data( $validator->getName() );
				}
			}
		}
	}

	$FormLiftUser->update();

	//upload files ONLY IF the user passes other tests first.
	if ( empty( $errors ) ) {
		$errors = apply_filters( 'formlift_pre_submit', $errors );
	}

	//Final check to see if should send data or not.

	if ( empty( $errors ) ) {

		do_action( 'formlift_success_submit', $formId );

		//decode because it get's encoded by default
		$packet = array(
			'url' => html_entity_decode( formlift_get_form_setting( $formId, 'post_url', '' ) )
		);

		$xid = get_post_meta( $formId, 'inf_form_xid', true );

		//in case the form isn't an infusionsoft form.
		if ( ! empty( $xid ) ) {
			$packet['xid'] = $xid;
		}

		$name = get_post_meta( $formId, 'inf_form_name', true );

		if ( ! empty( $name ) ) {
			$packet['name'] = $name;
		}

		$version = get_post_meta( $formId, 'infusionsoft_version', true );

		if ( ! empty( $version ) ) {
			$packet['version'] = $version;
		}

		if ( formlift_get_form_setting( $formId, 'submit_via_ajax', false ) ) {

			$formData                 = $FormLiftUser->get_user_data( 'form_data' );
			$formData['inf_form_xid'] = $packet['xid'];
			$response                 = wp_remote_post( $packet['url'], array(
				'body' => $formData
			) );

			$response = wp_remote_retrieve_body( $response );

			wp_die( json_encode( array( 'msg' => 'success', 'body' => $response ) ) );
		}

		/**
		 * this works...
		 */
		if ( wp_doing_ajax() ) {
			wp_die( json_encode( $packet ) );
		} else {
			$FormLiftUser->set_user_data( 'form_parameters', $packet );
			add_action( 'template_redirect', 'submit_formlift_form_on_page_load' );
		}

	} elseif ( ! empty( $errors ) ) {

		do_action( 'formlift_failed_submit', $formId );

		if ( wp_doing_ajax() ) {
			wp_die( json_encode( $errors ) );
		} else {
			return;
		}
	} else {
		wp_die( 'Something should have happened...' );
	}
}

add_action( 'init', 'formlift_submitV2' );
add_action( 'wp_ajax_nopriv_formlift_submit_form', 'formlift_submitV2' );
add_action( 'wp_ajax_formlift_submit_form', 'formlift_submitV2' );

function submit_formlift_form_on_page_load() {
	global $FormLiftUser;

	$packet = $FormLiftUser->get_user_data( 'form_parameters' );
	$data   = $FormLiftUser->get_user_data( 'form_data' );

	?>
	<p>Please wait...</p>
	<form id="formlift" method="post" action="<?php echo $packet['url']; ?>">
		<input type="hidden" name="inf_form_xid" value="<?php echo $packet['xid']; ?>"/>
		<input type="hidden" name="inf_form_name" value="<?php echo $packet['name']; ?>"/>
		<input type="hidden" name="infusionsoft_version" value="<?php echo $packet['version']; ?>"/>
		<input type="hidden" name="timeZone" value="<?php echo sanitize_text_field( $_POST['timeZone'] ); ?>"/>
		<?php
		foreach ( $data as $name => $value ):
			?>
			<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
		<?php
		endforeach;
		?>
	</form>
	<script>document.getElementById('formlift').submit()</script>
	<?php
	die();
}