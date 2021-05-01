<?php
include_once('config_init.php');

function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}

$dataArray = getSQLCache($_GET['key']);


if (array_size($dataArray) <= 0){
	echo printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please verify your URL and try again.";
	exit();
}

$results[] = $dataArray['Headers'];
foreach($dataArray['Body'] as $tempKey => $tempValue){
	$results[] = $tempValue;
}

$results = transpose($results);


$filename = $_GET['filename'];
if ($filename == ''){
	$filename = 'data.txt';	
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');


$fp = fopen('php://output', 'w');
fwrite($fp, 'sep=,' . "\n");

foreach($results as $tempKey => $tempValue){
	fputcsv($fp, $tempValue);
}
	
fclose($fp);

exit();
	
	
?>