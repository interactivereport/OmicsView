<?php
include_once('config_init.php');


if ($_GET['ID'] <= 0){
	$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The meta analysis does not exist. Please verify your URL and try again.</p>";
	echo getAlerts($message, 'warning');
	exit();
} else {

	$dataArray 			= getMetaAnalysis2($_GET['ID']);
	
	if ($dataArray['canUpdate']){
		deleteMetaAnalysis($_GET['ID']);
	}
	
	header("Location: app_meta_analysis2_browse.php");

	exit();
}



?>