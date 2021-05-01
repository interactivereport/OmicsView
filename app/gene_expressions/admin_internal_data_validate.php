<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Validate Internal Data Files";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "Validate Internal Data Files";

$PAGE['URL']		= 'admin_internal_data_validate.php';
$PAGE['Barcode']	= 'admin_internal_data_validate.php';
$PAGE['Body'] 		= 'admin_internal_data_validate_content.php';
$PAGE['EXE'] 		= '';

$PAGE['Plugins']['dataTables'] 		= 0;
$PAGE['Plugins']['selectPicker']	= 0;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

include('page_generator.php');

?>