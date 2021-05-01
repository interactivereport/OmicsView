<?php
include_once('config_init.php');

$category			= $_GET['Category'];

if ($APP_CONFIG['APP']['List_Category'][$category]['Review_Titles'] == ''){
	header("Location: app_record_browse.php?Category=Gene");
	exit();
}

$PAGE['Title'] 		= $APP_CONFIG['APP']['List_Category'][$category]['Review_Titles'];
$PAGE['Header']		= $APP_CONFIG['APP']['List_Category'][$category]['Review_Titles'];
$PAGE['Category']	= "Review";

$PAGE['URL']		= "app_record_browse.php?Category={$category}";
$PAGE['Barcode']	= "Review_{$category}";
$PAGE['Body'] 		= 'app_record_browse_component.php';
$PAGE['EXE'] 		= '';

$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress']	= 0;

include('page_generator.php');

?>