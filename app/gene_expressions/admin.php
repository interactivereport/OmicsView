<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Admin Tools";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "System Settings";

$PAGE['URL']		= 'admin.php';
$PAGE['Barcode']	= 'admin.php';
$PAGE['Body'] 		= 'admin_content.php';
$PAGE['EXE'] 		= '';


$PAGE['Plugins']['dataTables'] = 0;
$PAGE['Plugins']['selectPicker'] = 0;
$PAGE['Plugins']['stupidTable'] = 0;
$PAGE['Plugins']['canvasxpress'] = 0;

include('page_generator.php');

?>