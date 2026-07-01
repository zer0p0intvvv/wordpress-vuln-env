<?php

if(isset($_POST['csv'])):
	$string = stripslashes($_POST['csv']);
//	echo $string;
else:
	$string = 'an error occured.';
endif;
//echo 'howdy';
date_default_timezone_set('America/Los_Angeles');
$csvdate = date('Md-H-i-s-T');
$csvname = 'wordcounts-' . $csvdate . '.csv';
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=' . $csvname);
header('Pragma: no-cache');
echo $string;

?>