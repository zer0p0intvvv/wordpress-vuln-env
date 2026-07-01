<?php
date_default_timezone_set('America/Los_Angeles');
$csvdate = date('Md-H-i-s-T');
$csvname = 'wordcounts-' . $csvdate . '.csv';
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=' . $csvname);
header('Pragma: no-cache');
readfile($_GET['path'] . 'cpwc.csv');
?>