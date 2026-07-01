<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'FORMLIFT_NOTICE_ERROR', 'notice-error' );
define( 'FORMLIFT_NOTICE_SUCCESS', 'notice-success' );
define( 'FORMLIFT_NOTICE_WARNING', 'notice-warning' );
define( 'FORMLIFT_NOTICE_INFO', 'notice-info' );

class FormLift_Notice {
	var $id;
	var $dismissible;
	var $specific;
	var $type;
	var $html;
	var $premium;

	function __construct( $id, $type, $html, $dismissible, $specific, $premium ) {

		$this->id          = $id;
		$this->type        = $type;
		$this->html        = $html;
		$this->dismissible = $dismissible;
		$this->specific    = $specific;
		$this->premium     = $premium;

	}

	private function get_dismiss_form() {
		return "<button type='button' data-id=\"{$this->id}\" class='button' onclick='dismiss_formlift_notice(this)'>Dismiss</button>";
	}

	/**
	 * Echo function support
	 *
	 * @return string
	 */
	function __toString() {
		$screen = get_current_screen();
		if (
			( ( $this->specific && $screen->post_type == 'infusion_form' ) || ! $this->specific ) &&
			( ( $this->premium == 'premium_only' && FormLift_Module_Manager::has_modules() ) || ( $this->premium == 'free_only' && ! FormLift_Module_Manager::has_modules() ) || $this->premium == 'both' )
		) {

			$dismissible = $this->dismissible ? 'is-dismissible' : '';
			$notice = "<div id=\"{$this->id}\" class='notice {$this->type} {$dismissible}'><div class='formlift-notice-icon-container'>
<img src='" . plugins_url( "assets/images/icon-30x30.png", __FILE__ ) . "' class='formlift-notice-icon'/></div>
<div class='formlift-notice-text-container'>" . nl2br( $this->html ) . "</div>";
			$notice .= "</div>";

			return $notice;
		} else {
			return '';
		}
	}
}