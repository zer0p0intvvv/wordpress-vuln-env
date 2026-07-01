<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2017-02-26
 * Time: 6:44 PM
 */

function formlift_track_impression( $formID ) {
	global $FormLiftUser;

//    if ( is_user_logged_in() && current_user_can( 'manage_options' ) )
//        return;

	if ( ! $FormLiftUser->hasImpression( $formID ) ) {
		$FormLiftUser->addImpression( $formID );
		formlift_add_impression( $formID );

		$imps = formlift_get_form_impressions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $formID );
		$subs = formlift_get_form_submissions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $formID );

		formlift_update_form_stats( $formID, $imps, $subs );
	}
}

add_action( 'formlift_after_get_form_code', 'formlift_track_impression' );

function formlift_track_submission( $formID ) {
	global $FormLiftUser;

	if ( ! $FormLiftUser->hasSubmission( $formID ) ) {
		$FormLiftUser->addSubmission( $formID );
		formlift_add_submission( $formID );

		$imps = formlift_get_form_impressions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $formID );
		$subs = formlift_get_form_submissions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ), $formID );

		formlift_update_form_stats( $formID, $imps, $subs );
	}
}

add_action( 'formlift_success_submit', 'formlift_track_submission' );

function formlift_load_tracking_script() {
	wp_enqueue_script( 'infusionsoft-tracking-script', 'https://' . FormLift_Infusionsoft_Manager::$app->getHostname() . '/app/webTracking/getTrackingCode', array(), false, true );
}

add_action( 'wp_enqueue_scripts', 'formlift_load_tracking_script' );

function formlift_update_form_stats( $formId, $impressions, $submissions ) {
	if ( ! empty ( $submissions ) ) {
		$convs = floor( ( intval( $submissions ) / intval( $impressions ) ) * 100 );
	} else {
		$convs = 0;
	}


	update_post_meta( $formId, 'conversion_rate', $convs );
	update_post_meta( $formId, 'impressions', $impressions );
	update_post_meta( $formId, 'submissions', $submissions );
}