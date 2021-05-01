<?php
include_once('config_init.php');

$category			= $_GET['Category'];

$PAGE['Title'] 		= $APP_CONFIG['APP']['List_Category'][$category]['Page_Titles'];
$PAGE['Header']		= $APP_CONFIG['APP']['List_Category'][$category]['Page_Titles'];
$PAGE['Category']	= "List";

$PAGE['URL']		= "app_list_browse.php?Category={$category}";
$PAGE['Barcode']	= "List_{$category}";
$PAGE['Body'] 		= 'app_list_browse_component.php';
$PAGE['EXE'] 		= '';

$PAGE['Plugins']['dataTables'] = 1;

include('page_generator.php');

?>