<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Import Internal Data";
//$PAGE['Header']		= "Import Internal Data";
$PAGE['Category']	= "List";

$PAGE['URL']		= 'app_internal_data_import.php';
$PAGE['Body'] 		= 'app_internal_data_import_content.php';
$PAGE['EXE'] 		= 'app_internal_data_import_exe1.php';
$PAGE['Barcode']	= 'Import Internal Data';


$PAGE['Plugins']['canvasxpress'] = 0;

if (general_guest_account_readonly()){
	echo "Error. This feature has been disabled for guest account. Please sign up for a new account and try again.";
	exit();
}

include('page_generator.php');

?>