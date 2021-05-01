<?php
include_once('config_init.php');


$comparison = get_multiple_record('Comparison', $_GET['ID'], 'GetRow', '*');

if (($comparison['Case_SampleIDs'] != '') || ($comparison['Control_SampleIDs'] != '')){

	$sampleIDs = splitComparisonSampleIDs($comparison['Case_SampleIDs'], $comparison['Control_SampleIDs']);

	
	unset($dataArray, $currentCount);
	
	foreach($sampleIDs as $tempKey => $tempValue){
		
		
		$temp = array();
		$temp['Field'] 		= 'SampleID';
		$temp['Operator'] 	= 1;
		$temp['Value'] 		= trim($tempValue);
		$temp['Logic'] 		= 'or';
		$tempValue			= addslashes(trim($tempValue));
		$temp['SQL'] 		= "(`SampleID` = '{$tempValue}')";
		
		$dataArray['Search'][++$currentCount] = $temp;
	}
	
	
	if (internal_data_is_public($_GET['ID'])){
		$dataArray['POST']['data_source'][] = 'public';
	} else {
		$dataArray['POST']['data_source'][] = 'private';
		$dataArray['POST']['data_source_private_project_indexes'][] = $comparison['ProjectIndex'];
	}
	

	$urlKey = putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
		
		
	$URL = "app_record_browse.php?Category=Sample&key={$urlKey}&hide=1";
	
	header("Location: {$URL}");
	exit();
	
} else {
	
	echo "Error. This comparison record does not contain any sample information.";
	exit();	
}

?>