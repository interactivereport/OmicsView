<?php
include_once('config_init.php');

if ($_GET['projectIndex'] != ''){
	$projectIndex = getSQLCache($_GET['projectIndex']);
}

$dataArray = prepareDashboardData_Project($projectIndex);

if (array_size($dataArray) <= 0) exit();


$filename = $_GET['filename'];

if ($filename == ''){
	$filename = 'data.csv';	
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');


$fp = fopen('php://output', 'w');

	
$headers = array_keys($dataArray[0]);
	
	
fputcsv($fp, $headers);


foreach($dataArray as $tempKey => $tempValue){
	fputcsv($fp, $tempValue);
}
	
fclose($fp);

exit();
	
	
?>