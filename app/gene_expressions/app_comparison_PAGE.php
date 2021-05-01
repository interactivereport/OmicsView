<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Comparison PAGE Report";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "Review";

$PAGE['URL']		= 'app_comparison_PAGE.php';
$PAGE['Barcode']	= 'app_comparison_PAGE.php';
$PAGE['Body'] 		= 'app_comparison_PAGE_content.php';
$PAGE['EXE'] 		= 'app_comparison_PAGE_exe.php';

$PAGE['Plugins']['dataTables'] 		= 1;
$PAGE['Plugins']['selectPicker']	= 0;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

$_GET['direction'] = strtolower($_GET['direction']);


$dataArray = get_multiple_record('comparison', $_GET['ID'], 'GetRow', '*', 0);

if (array_size($dataArray) > 0){
	if ($_GET['direction'] == 'up'){
		$PAGE['Header'] = $PAGE['Title'] 	= "PAGE Report of {$dataArray['ComparisonID']}: Upregulated {$APP_MESSAGE['Genes']}";
		$direction = 'Up';
	} else {
		$PAGE['Header'] = $PAGE['Title'] 	= "PAGE Report of {$dataArray['ComparisonID']}: Downregulated {$APP_MESSAGE['Genes']}";
		$direction = 'Down';
	}
	
}

include('page_generator.php');

?>