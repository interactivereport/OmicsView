<?php
include_once('config_init.php');

$category			= $_GET['Category'];

if ($APP_CONFIG['APP']['List_Category'][$category]['Page_Title'] == ''){
	$category = 'Gene';	
}

$PAGE['Title'] 		= $APP_CONFIG['APP']['List_Category'][$category]['Create_New_List'];
$PAGE['Header']		= $APP_CONFIG['APP']['List_Category'][$category]['Create_New_List'];
$PAGE['Category']	= "List";
$PAGE['Button']		= $APP_CONFIG['APP']['List_Category'][$category]['Create_New_List'];

$PAGE['URL']		= $APP_CONFIG['APP']['List_Category'][$category]['File_Update'];
$PAGE['Barcode']	= "List_{$category}";
$PAGE['Body'] 		= 'app_list_update_component.php';
$PAGE['EXE'] 		= 'app_list_update_exe.php';


if (general_guest_account_readonly()){
	echo "Error. This feature has been disabled for guest account. Please sign up for a new account and try again.";
	exit();
}

include('page_generator.php');

?>