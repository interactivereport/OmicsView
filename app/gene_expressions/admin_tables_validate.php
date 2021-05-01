<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Validate Record Counts";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "Validate Record Counts";

$PAGE['URL']		= 'admin_tables_validate.php';
$PAGE['Barcode']	= 'admin_tables_validate.php';
$PAGE['Body'] 		= 'admin_tables_validate_content.php';
$PAGE['EXE'] 		= '';

$PAGE['Plugins']['dataTables'] 		= 0;
$PAGE['Plugins']['selectPicker']	= 0;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

include('page_generator.php');

?>