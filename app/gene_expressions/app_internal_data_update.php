<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Update Internal Data";
$PAGE['Header']		= "Update Internal Data";
$PAGE['Category']	= "List";

$PAGE['URL']		= 'app_internal_data_update.php';
$PAGE['Body'] 		= 'app_internal_data_update_content.php';
$PAGE['Barcode']	= 'Import Internal Data';
$PAGE['EXE']		= 'app_internal_data_update_exe.php';

$PAGE['Plugins']['dataTables']		= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

include('page_generator.php');

?>