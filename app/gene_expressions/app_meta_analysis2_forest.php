<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Meta Analysis Using Gene Expression Data";
$PAGE['Header']		= "Meta Analysis Using Gene Expression Data";
$PAGE['Category']	= "My Results";

$PAGE['URL']		= 'app_meta_analysis2_forest.php';
$PAGE['Body'] 		= 'app_meta_analysis2_forest_content.php';
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
		
		if (isset($_GET['GeneIndex']) && is_numeric($_GET['GeneIndex'])){
			$geneInfo = processMetaAnalysis2_Gene($_GET['key'], $dataArray, $_GET['GeneIndex']);
			
			
			if ($geneInfo['Gene_HTML'] != ''){
					
				$PAGE['Header'] = "Forest Plot for Gene {$geneInfo['Gene_HTML']}";
			}
			
		}
	}
	
}

include('page_generator.php');

?>