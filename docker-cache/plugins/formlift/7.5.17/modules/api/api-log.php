<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Log
 * A really simple logging class that writes flat data to a file.
 *
 * @version   1.0
 * @license   MIT
 * @author    Dennis Thompson
 * @copyright AtomicPages LLC 2014
 */
class FormLift_Api_Log {

	private $handle, $dateFormat;

	public function __construct( $file, $mode = "a" ) {
//		$this->handle     = fopen( $file, $mode );
//		$this->dateFormat = "d/M/Y H:i:s";
	}

	public function dateFormat( $format ) {
//		$this->dateFormat = $format;
	}

	public function getDateFormat() {
//		return $this->dateFormat;
	}

	/**
	 * Writes info to the log
	 *
	 * @param mixed, string or an array to write to log
	 *
	 * @access public
	 */
	public function log( $entries ) {
//		if ( is_string( $entries ) ) {
//			fwrite( $this->handle, "[" . date( $this->dateFormat ) . "] " . $entries . "\n" );
//		} else {
//			foreach ( $entries as $value ) {
//				fwrite( $this->handle, "[" . date( $this->dateFormat ) . "] " . $value . "\n" );
//			}
//		}
	}
}