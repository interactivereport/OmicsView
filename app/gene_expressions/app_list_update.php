<?php
include_once('config_init.php');

$ID = $_GET['ID'];

if ($ID > 0){
	$dataArray 			= get_list_record_by_list_id($_GET['ID']);
	$category			= $dataArray['Category'];
}



if ($APP_CONFIG['APP']['List_Category'][$category]['Page_Title'] == ''){
	$category = 'Gene';
}



$PAGE['Title'] 		= $APP_CONFIG['APP']['List_Category'][$category]['Update_List'];
$PAGE['Header']		= $APP_CONFIG['APP']['List_Category'][$category]['Update_List'];
$PAGE['Category']	= "List";
$PAGE['Button']		= 'Update';

$PAGE['URL']		= $APP_CONFIG['APP']['List_Category'][$category]['File_Update'];
$PAGE['Body'] 		= 'app_list_update_component.php';
$PAGE['EXE'] 		= 'app_list_update_exe.php';
$PAGE['Barcode']	= "List_{$category}";


//$PAGE['Plugins']['selectPicker'] 	= 1;




include('page_generator.php');

?>