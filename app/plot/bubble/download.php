<?php

include_once('config.php');
include_once('../profile/config.php');


$sessionKey = $_GET['sessionKey'];

if (!isset($_SESSION['App']['bubble'][$sessionKey]['Download'])){
	echo "Error! Please verify your URL and try again.";
	exit();	
}


$filename = 'data.csv';	

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');


$fp = fopen('php://output', 'w');
fwrite($fp, 'sep=,' . "\n");
	
fputcsv($fp, $_SESSION['App']['bubble'][$sessionKey]['Download']['Header']);


foreach($_SESSION['App']['bubble'][$sessionKey]['Download']['Body'] as $tempKey => $tempValue){
	fputcsv($fp, $tempValue);
}
	
fclose($fp);


?>