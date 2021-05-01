<?php
include_once('config_init.php');

$tableOption	= getSQLCache($_GET['tableOptionKey']);
if (array_size($tableOption) <= 0) exit();

$dataArray	= getSQLCache($_GET['key']);
if (array_size($dataArray) <= 0) exit();


$filename = $_GET['filename'];
if ($filename == ''){
	$filename = 'data.csv';	
}


$currentSQL 		= "{$tableOption['Extra']['SQL_Data_All']}";
$SQL_RESULTS 		= getSQL($currentSQL, 'GetArray', $tableOption['Extra']['SQL_Table']);
$dataArray['Body']	= processData($tableOption['Extra']['category'], $SQL_RESULTS, '', 'Print');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');


$fp = fopen('php://output', 'w');
fwrite($fp, 'sep=,' . "\n");
	
fputcsv($fp, $dataArray['Headers']);


foreach($dataArray['Body'] as $tempKey => $tempValue){
	fputcsv($fp, $tempValue);
}
	
fclose($fp);

exit();
	
	
?>