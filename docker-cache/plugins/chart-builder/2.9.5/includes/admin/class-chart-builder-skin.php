<?php

use ChartBuilder\Helpers\Chart_Builder_Plugin_Silent_Upgrader_Skin;

/**
 * Skin for on-the-fly addon installations.
 *
 * @since 1.0.0
 * @since 6.4.0.4 Extend Chart_Builder_Plugin_Silent_Upgrader_Skin and clean up the class.
 */
class Chart_Builder_Install_Skin extends Chart_Builder_Plugin_Silent_Upgrader_Skin {

	/**
	 * Instead of outputting HTML for errors, json_encode the errors and send them
	 * back to the Ajax script for processing.
	 *
	 * @since 1.0.0
	 *
	 * @param array $errors Array of errors with the install process.
	 */
	public function error( $errors ) {

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}
	}
}
