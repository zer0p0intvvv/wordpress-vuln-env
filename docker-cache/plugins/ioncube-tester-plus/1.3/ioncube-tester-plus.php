<?php
/*
Plugin Name: ionCube Tester Plus
Version: 1.3
Plugin URI: http://www.mapsmarker.com
Description: This plugin helps you to determine if the ionCube loaders are installed correctly on your web server. If not, a installation wizard with giving install instructions.
Author: Robert Harm
Author URI: http://www.harm.co.at
License: GPLv2
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'ioncube-tester-plus.php') { die ("Please do not access this file directly. Thanks!"); }
if ( ! defined( 'ICT_PLUGIN_URL' ) )
define ("ICT_PLUGIN_URL", plugin_dir_url(__FILE__));

class ioncubetesterplus
{
function __construct() {
	add_action('admin_notices', array(&$this, 'ICT_admin_notices'),6);
  }
  function check_ioncube_loaders() {
	if (extension_loaded('ionCube Loader')) {
		return true;
	}
	if ( function_exists('ioncube_file_is_encoded') ) {
		return true;
	}
	if ( function_exists('phpinfo') ) {
		ob_start();
		phpinfo(8);
		$phpinfo = ob_get_clean();
		if ( false !== strpos($phpinfo, 'ionCube') ) {
			return true;
		}
	}
	return false;
  }
  function ICT_admin_notices() {
	if ($this->check_ioncube_loaders() == true) {
		if ( function_exists('ioncube_loader_iversion') ) {
			$ioncube_loader_iversion = ioncube_loader_iversion();
			$ioncube_loader_version_major = (int)substr($ioncube_loader_iversion,0,1);
			$ioncube_loader_version_minor = (int)substr($ioncube_loader_iversion,1,2);
			$ioncube_loader_version_revision = (int)substr($ioncube_loader_iversion,3,2);
			$ioncube_loader_version = "$ioncube_loader_version_major.$ioncube_loader_version_minor.$ioncube_loader_version_revision";
		} else {
			$ioncube_loader_version = ioncube_loader_version();
			$ioncube_loader_version_major = (int)substr($ioncube_loader_version,0,1);
			$ioncube_loader_version_minor = (int)substr($ioncube_loader_version,2,1);
		}
		echo '<div class="updated" style="padding:0 10px 10px 10px;"><h3>IonCube loaders v' . $ioncube_loader_version . ' are <span style="color:green;font-weight:bold;">AVAILABLE</span> on this web server.</h3>You can start the installation of plugins like <a href="http://www.mapsmarker.com/" target="_blank">Leaflet Maps Marker Pro</a></div>';
	} else {
		$ioncube_phpini_exists = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'php.ini';
		if (!file_exists($ioncube_phpini_exists)) {
			echo '<div class="error" style="padding:0 10px 10px 10px;"><h3>IonCube loaders are <span style="color:red;font-weight:bold;">NOT AVAILABLE</span> on this web server.</h3>' . sprintf(__('<a href="%1s" target="_blank">Please click here to start the installation wizard</a> which offers an interactive tutorial on how to install the required "ionCube Loader" on your web server.','lmm'), ICT_PLUGIN_URL . 'loader-wizard.php' ) . '</div>';
		} else {
			echo '<div class="error" style="padding:10px;">' . sprintf(__('You already ran the <a href="%1s" target="_blank">ioncube installation wizard</a> and copied the created php.ini file to <strong>%2s</strong><br/>To finish the ioncube installation, please also copy the file <strong>php.ini</strong> to the directory <strong>%3s</strong><br/>Afterwards the ioncube installation is finished and this admin message should turn green.','lmm'), plugin_dir_url(__FILE__) . '/loader-wizard.php', plugin_dir_path(__FILE__) . '/', ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR ) . '</div>';
		}
	}
  }
}  //info: end class
$run_ioncubetesterplus = new ioncubetesterplus();
unset($run_ioncubetesterplus);
?>