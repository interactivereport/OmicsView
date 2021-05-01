<?php

$version['primary'] 	= '4';
$version['secondary'] 	= '00';
$version['date']        = '2019-10-09';
$version['time']        = '00:00:00';

$version['version'] 	= $version['primary'] . $version['secondary'];
$version['datetime'] 	= $version['date'] . ' ' . $version['time'];

if ($_GET['output']) echo json_encode($version);


?>