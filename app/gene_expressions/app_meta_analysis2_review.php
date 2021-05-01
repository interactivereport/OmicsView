<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Meta Analysis Using Gene Expression Data";
$PAGE['Header']		= "Meta Analysis Using Gene Expression Data";
$PAGE['Category']	= "My Results";

$PAGE['URL']		= 'app_meta_analysis2_review.php';
$PAGE['Body'] 		= 'app_meta_analysis2_review_content.php';
$PAGE['EXE'] 		= '';
$PAGE['Barcode']	= 'app_meta_analysis2.php';

$PAGE['Plugins']['dataTables'] 		= 1;
$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
} else {
	
	if ($_GET['ID'] > 0){
		$dataArrayCompleted = getMetaAnalysis2($_GET['ID']);
		$dataArray 			= $dataArrayCompleted['Parameters'];
	}
	
	
	if (array_size($dataArray) <= 0){
		unset($dataArrayCompleted);
		$dataArray = getSQLCache($_GET['key']);
	}
	
	if (array_size($dataArray) > 0){
		$dataArray = processMetaAnalysis2($_GET['key'], $dataArray, $_GET['ID']);
		
		
		
		if ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Running'){
			$PAGE['Meta']['Refresh'] = 30;
		} elseif ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Finished'){
			if ((array_size($dataArrayCompleted) > 0) && ($dataArrayCompleted['Status'] != 'Finished') && ($_GET['ID'] > 0)){
				completeMetaAnalysisStatus2($_GET['ID']);
			}
		}
	}
	
}

include('page_generator.php');

?>