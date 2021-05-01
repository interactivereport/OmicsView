<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Meta Analysis Using Comparison Data";
$PAGE['Header']		= "Meta Analysis Using Comparison Data";
$PAGE['Category']	= "My Results";

$PAGE['URL']		= 'app_meta_analysis.php';
$PAGE['Body'] 		= 'app_meta_analysis_content.php';
$PAGE['EXE'] 		= 'app_meta_analysis_exe.php';
$PAGE['Barcode']	= 'app_meta_analysis.php';

$PAGE['Plugins']['dataTables'] 		= 1;
$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');

?>