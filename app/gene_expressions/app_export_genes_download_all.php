<?php
include_once('config_init.php');

$dataArray = getSQLCache($_GET['key']);


if (array_size($dataArray) <= 0){
	echo printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please verify your URL and try again.";
	exit();
}



$filename = $_GET['filename'];

if ($filename == ''){
	$filename = 'data.txt';	
}
	


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');


$fp = fopen('php://output', 'w');
fwrite($fp, 'sep=,' . "\n");
	
	
if (true){
	$currentRow = array();
	foreach($dataArray['Export-All']['Headers'] as $currentCategory => $currentGroup){
		foreach($currentGroup as $tempKey => $tempValue){
			$currentRow[] = $tempValue;
		}
	}
	fputcsv($fp, $currentRow);
}




foreach($dataArray['Export-All']['Body'] as $geneIndexComparisonIndex => $currentGeneComparison){
	
	$currentRow = array();
	
	foreach($currentGeneComparison as $currentCategory => $currentGroup){
		foreach($currentGroup as $tempKey => $tempValue){
			$currentRow[] = $tempValue;
		}
	}
	fputcsv($fp, $currentRow);
	
	
	
}

fclose($fp);
	
exit();
	
	
?>