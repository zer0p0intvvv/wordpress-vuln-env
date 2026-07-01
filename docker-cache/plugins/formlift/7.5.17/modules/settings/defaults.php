<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Defaults {
	static $style_defaults = array(
		'formlift_button'                                                                    => array(
			'background-color' => '#dd3333',
			'border-color'     => '#dd3333',
			'color'            => '#FFFFFF',
			'width'            => '100%',
			'border-width'     => '1px',
			'border-radius'    => '2px',
			'border-style'     => 'solid',
			'padding-top'      => '15px',
			'padding-bottom'   => '15px',
			'font-family'      => 'Arial, Helvetica',
			'font-size'        => '18px',
			'font-weight'      => '600',
			'transition'       => '0.4s',
//            'box-shadow'        => '2px 2px 2px #777777'
		),
		'formlift_button:hover'                                                              => array(
			'background-color' => '#a82626',
			'border-color'     => '#a82626',
			'color'            => '#FFFFFF',
			'transition'       => '0.4s'
		),
		'formlift_button_container'                                                          => array(
			'text-align' => 'left'
		),
		'formlift_radio_option_container'                                                    => array(
			'display' => 'block',
			'padding' => '0'
		),
		'formlift_check_style'                                                               => array(
			'height'           => '17px',
			'width'            => '17px',
			'border-width'     => '1px',
			'background-color' => '#fcfcfc',
			'border-color'     => '#d1d1d1',
			'transition'       => '0.4s'
		),
		'formlift_check_style, .formlift_radio_label_container'                              => array(
			'--rb-size' => '20px'
		),
		'formlift_radio_option_container .formlift_radio_label_container'                    => array(
			'font-size' => '18px'
		),
		'formlift_radio_label_container:hover input ~ .formlift_check_style'                 => array(
			'background-color' => '#f4f4f4',
			'border-color'     => '#bcbcbc'
		),
		'formlift_radio_label_container input:checked ~ .formlift_check_style'               => array(
			'background-color' => '#f2f2f2',
			'border-color'     => '#8e8e8e'
		),
		'formlift_radio_label_container .formlift_radio ~ .formlift_check_style:after'       => array(
			'background-color' => '#000000'
		),
		'formlift_radio_label_container .formlift_is_checkbox ~ .formlift_check_style:after' => array(
			'border-color' => '#000000',
			'font-size'    => '18px',
			'color'        => '#000000'
		),
		'formlift_field .formlift_input::placeholder'                                        => array(
			'color' => '#777777'
		),
		'formlift_field .formlift_input::-ms-input-placeholder'                              => array(
			'color' => '#777777'
		),
		'formlift_field .formlift_input:-ms-input-placeholder'                               => array(
			'color' => '#777777'
		),
		'formlift_input'                                                                     => array(
			'background-color' => '#ffffff',
			'border-color'     => '#e0e0e0',
			'color'            => '#000000',
			'width'            => '100%',
			'border-style'     => 'solid',
			'border-width'     => '1px',
			'border-radius'    => '2px',
			'padding-top'      => '15px',
			'height'           => 'auto',
			'font-family'      => 'Arial, Helvetica',
			'font-size'        => '18px',
			'font-weight'      => '400',
			'padding'          => '10px',
//            'box-shadow'        => '2px 2px 2px #777777'
		),
		'formlift_input:focus'                                                               => array(
			'background-color' => '#f7f7f7',
			'border-color'     => '#e0e0e0',
			'color'            => '#000000',
			'transition'       => '0.4s'
		),
		'formlift-infusion-form'                                                             => array(
			'background-color' => 'rgba(255,255,255,0)',
			'border-color'     => 'rgba(255,255,255,0)',
			'padding-top'      => '10px',
			'padding-right'    => '10px',
			'padding-bottom'   => '10px',
			'padding-left'     => '10px',
			'width'            => '100%'
		),
		'formlift_field'                                                                     => array(
			'background-color' => 'rgba(255,255,255,0)',
			'border-color'     => 'rgba(255,255,255,0)',
			'padding-top'      => '5px',
			'padding-right'    => '5px',
			'padding-bottom'   => '5px',
			'padding-left'     => '5px'
		),
		'formlift_label'                                                                     => array(
			'font-family'   => 'Arial, Helvetica',
			'font-size'     => '18px',
			'font-weight'   => '400',
			'color'         => '#000000',
			'margin-bottom' => '10px'
		),
		'formlift-error-response'                                                            => array(
			'background-color' => 'rgba(255,255,255,0)',
			'border-color'     => 'rgba(255,255,255,0)',
			'border-width'     => '0',
			'border-radius'    => '0',
			'padding'          => '10px',
			'color'            => '#ff0000',
			'font-family'      => 'Arial, Helvetica',
			'font-size'        => '18px',
			'font-weight'      => '500'
		)
	);

	static $settings_defaults = array(
		'success_message'           => 'Success!',
		'please_wait_text'          => 'Please Wait...',
		'invalid_data_error'        => 'Something is wrong with your submission.',
		'required_error'            => 'This field is required.',
		'email_error'               => 'Please enter a valid email.',
		'phone_error'               => 'Please enter a valid phone number.',
		'date_error'                => 'Please enter a valid date.',
		//'captcha_error'		    => 'Please verify that you are not a robot.',
		'url_error'                 => 'Please enter a valid Url.',
		'password_error'            => 'The Passwords you entered do not match.',
		//'logged_in_error'       => 'You must be logged in to submit this form.',
		//'tracking_method' 		=> 'page_load',
		'time_to_live'              => 30,
		'form_settings_active_tab'  => 'formlift_infusionsoft_settings',
		'style_settings_active_tab' => 'formlift_button_css'
	);

	public static function add_style_reset_filter() {
		if ( is_user_logged_in() && is_admin() && current_user_can( 'manage_options' ) && isset( $_POST[ FORMLIFT_SETTINGS ]["reset_style_to_defaults"] ) ) {
			add_filter( "formlift_sanitize_style_settings", array( "FormLift_Defaults", "reset_style_settings" ) );
		}
	}

	public static function reset_style_settings( $settings ) {
		return FormLift_Defaults::$style_defaults;
	}

	public static function set_defaults_on_activation() {
		if ( is_user_logged_in() && is_admin() && current_user_can( 'activate_plugins' ) ) {
			if ( ! get_option( FORMLIFT_STYLE, false ) ) {
				update_option( FORMLIFT_STYLE, self::$style_defaults );
				update_option( FORMLIFT_SETTINGS, self::$settings_defaults );
			}
		}
	}
}

add_action( "plugins_loaded", array( "FormLift_Defaults", "add_style_reset_filter" ) );