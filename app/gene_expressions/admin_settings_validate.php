<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Validate Settings";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "System Settings";

$PAGE['URL']		= 'admin_settings_validate.php';
$PAGE['Barcode']	= 'admin_settings_validate.php';
$PAGE['Body'] 		= 'admin_settings_validate_content.php';
$PAGE['EXE'] 		= '';

$PAGE['Plugins']['dataTables'] 		= 0;
$PAGE['Plugins']['selectPicker']	= 0;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

include('page_generator.php');

?>