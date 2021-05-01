<?php
include_once('config_init.php');


$dataArray 			= get_list_record_by_list_id($_GET['ID']);

$category			= $dataArray['Category'];



$PAGE['Title'] 		= $APP_CONFIG['APP']['List_Category'][$category]['Page_Title'];
$PAGE['Header']		= $APP_CONFIG['APP']['List_Category'][$category]['Page_Title'];
$PAGE['Category']	= "List";

$PAGE['URL']		= $APP_CONFIG['APP']['List_Category'][$category]['File_Update'];
$PAGE['Body'] 		= 'app_list_review_component.php';
$PAGE['EXE'] 		= '';
$PAGE['Barcode']	= "List_{$category}";


if ($category == ''){
	$PAGE['Title'] 	= 'List';
	$PAGE['Header'] = 'List';
}


if (array_size($dataArray) > 0){
	$PAGE['Header']		= "{$APP_CONFIG['APP']['List_Category'][$category]['Page_Title']}: {$dataArray['Name']}";
	
}




include('page_generator.php');

?>