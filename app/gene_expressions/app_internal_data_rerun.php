<?php
include_once('config_init.php');

if (!isAdminUser()){
	echo "<p>You do not have permissions to access this tool.</p>";	
	exit();
}

$dataArray = getInternalDataJob($_GET['ID']);


if (array_size($dataArray) <= 0){
	echo "<div class='row'>";

		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The data does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');

		echo "</div>";


	echo "</div>";
	
}

reRunInternalDataByJobID($_GET['ID']);

echo "<p>The application is re-processing this data now.</p>";


?>