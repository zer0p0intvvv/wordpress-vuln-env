<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

add_action( 'plugins_loaded', 'formlift_typeform_conflict_solve' );

function formlift_typeform_conflict_solve() {
	if ( is_formlift_edit_page() ) {
		remove_action( 'admin_enqueue_scripts', 'tf_add_admin_scripts' );
	}
}

function is_formlift_edit_page() {
	return isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == 'infusion_form';
}