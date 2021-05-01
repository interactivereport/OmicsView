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


if ($_GET['raw'] == 1){

	
	if (file_exists($dataArray['Summary']['Tabix']) && filesize($dataArray['Summary']['Tabix']) > 0){
		
		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		
		readfile($dataArray['Summary']['Tabix']);
		exit();
	} else {
		echo printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The file is not available. Please verify your link and try again.";
		exit();	
	}
	


} else {

	$valueColumn = $_GET['value'];
	if (!isset($dataArray['Summary']['Value Names'][$valueColumn])){
		$valueColumn = array_keys($dataArray['Summary']['Value Names']);
		$valueColumn = $valueColumn[0];
	}
	
	
	
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	
	
	$fp = fopen('php://output', 'w');
	fwrite($fp, 'sep=,' . "\n");
	
	
	
	
	
	foreach($dataArray['Export']['Headers'] as $rowKey => $currentRow){
		
		if (!isset($indexKey)){
			$indexKey = array_keys($currentRow);
		}
		
		array_unshift($currentRow, $rowKey);
		for ($i = 1; $i <= $dataArray['Summary']['Header Padding']; $i++){
			array_unshift($currentRow, '');
		}
		fputcsv($fp, $currentRow);
	}
	
	
	if (true){
		$currentRow 	= $dataArray['Summary']['Gene Columns'];
		$currentRow[] 	= $dataArray['Summary']['Overlap Name'];
		
		$indexCategory = $dataArray['Summary']['Index Column'];	
		

		
		foreach($indexKey as $tempKey => $tempValue){
			$currentRow[] 	= $tempValue;
		}
		
		fputcsv($fp, $currentRow);
	}
	
	
	
	foreach($dataArray['Export']['Gene_Values'][$valueColumn] as $geneIndex => $currentValues){
		
		unset($currentRow);
		
		foreach($dataArray['Summary']['Gene Columns'] as $tempKey => $tempValue){
			$currentRow[] = $dataArray['Export']['Gene_Annotation'][$geneIndex][$tempKey];
		}
		$currentRow[] = $geneIndex;
		
		foreach($currentValues as $tempKey => $tempValue){
			$currentRow[] = $tempValue;
		}
		
		
		fputcsv($fp, $currentRow);
		
		
		
		
	}
	
	
	fclose($fp);
	
	exit();


}


	
	
?>