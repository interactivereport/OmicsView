<?php

include_once('config_init.php');

$PAGE['Title'] 		= '';
$PAGE['Header']		= '';
$PAGE['Category']	= '';

$PAGE['URL']		= "app_dashboard_project.php";
$PAGE['Barcode']	= "";
$PAGE['Body'] 		= 'app_dashboard_project_content.php';
$PAGE['EXE'] 		= '';

$PAGE['Plugins']['dc'] = 1;
$PAGE['Plugins']['dataTables'] 		= 1;
$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['canvasxpress'] = 0;

include('page_generator.php');

?>