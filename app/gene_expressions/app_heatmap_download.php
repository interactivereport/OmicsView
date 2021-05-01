<?php
include_once('config_init.php');

$dataArray = getSQLCache($_GET['key']);


if (array_size($dataArray) <= 0){
	echo printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please verify your URL and try again.";
	exit();
}


$filename = $_GET['filename'];

if ($filename == ''){
	$filename = 'data.csv';	
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');


$fp = fopen('php://output', 'w');
fwrite($fp, 'sep=,' . "\n");
	
foreach($dataArray['Headers'] as $tempKey => $tempValue){
	array_unshift($tempValue, $tempKey);
	fputcsv($fp, $tempValue);
}

foreach($dataArray['Genes'] as $tempKey => $tempValue){
	array_unshift($tempValue, $tempKey);
	fputcsv($fp, $tempValue);
}
	
fclose($fp);

exit();
	
	
?>