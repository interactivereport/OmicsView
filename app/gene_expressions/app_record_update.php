<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Update Internal Data";
$PAGE['Header']		= "Update Internal Data";
$PAGE['Category']	= "List";

$PAGE['URL']		= 'app_record_update.php';
$PAGE['Body'] 		= 'app_record_update_content.php';
$PAGE['EXE']		= 'app_record_update_exe.php';
$PAGE['Barcode']	= 'Import Internal Data';


$PAGE['Plugins']['canvasxpress'] = 0;


if ($APP_CONFIG['APP']['List_Category'][$_GET['Category']]['Edit_Title'] != ''){
	$PAGE['Title'] = $PAGE['Header'] = $APP_CONFIG['APP']['List_Category'][$_GET['Category']]['Edit_Title'];
}

if (general_guest_account_readonly()){
	echo "Error. This feature has been disabled for guest account. Please sign up for a new account and try again.";
	exit();
}


$category 		= $_GET['Category'];
$currentTable 	= $APP_CONFIG['APP']['List_Category'][$category]['Table'];

if ($_GET['recordIndex'] != ''){
	$recordIndexes = getSQLCache($_GET['recordIndex']);

	if (array_size($recordIndexes) == 1){
		if ($APP_CONFIG['APP']['List_Category'][$category]['Update_URL'] != ''){
			header("Location: {$APP_CONFIG['APP']['List_Category'][$category]['Update_URL']}{$recordIndexes[0]}");
			exit();
		}
	}

}


include('page_generator.php');

?>