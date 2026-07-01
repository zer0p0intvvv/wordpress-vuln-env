<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class WPB_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		WPBOOKIT
 * @subpackage	Classes/WPB_Settings
 * @author		Iqonic Design
 * @since		1.0.4
 */
class WPB_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.4
	 */
	private $plugin_name;
	private $plugin_path;
	private $plugin_url;

	/**
	 * Our WPB_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.4
	 */
	function __construct(){
		$this->plugin_name = IQWPB_NAME;
		$this->plugin_path = IQWPB_PLUGIN_PATH;
	}

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.4
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'wpbookit/settings/get_plugin_name', $this->plugin_name );
	}

	public function get_plugin_dir()  {
		return apply_filters( 'wpbookit/settings/get_plugin_dir', $this->plugin_path );
	}
}
