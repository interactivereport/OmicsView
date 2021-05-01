<?php
include_once('config_init.php');


if ($_GET['ID'] <= 0){
	$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The list does not exist. Please verify your URL and try again.</p>";
	echo getAlerts($message, 'warning');
	exit();
} else {

	$dataArray 			= get_list_record_by_list_id($_GET['ID']);
	$category			= $dataArray['Category'];
	
	if ($dataArray['canUpdate']){
		delete_list($_GET['ID']);
	}
	
	header("Location: app_list_browse.php?Category={$category}");

	exit();
}



?>