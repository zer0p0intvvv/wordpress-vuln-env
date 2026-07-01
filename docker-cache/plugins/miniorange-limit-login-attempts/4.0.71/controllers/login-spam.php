<?php
global $moWpnsUtility,$mo_lla_dirName;
if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
} else {
		$active_tab = 'default';
}
include_once $mo_lla_dirName . 'views'.DIRECTORY_SEPARATOR.'login_spam.php';
?>