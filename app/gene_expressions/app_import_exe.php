<?php

set_time_limit(0);
$overallStartTime = microtime(true);


unset($candidates);
foreach($APP_CONFIG['DB_Dictionary'] as $currentTable => $tempValue){
	$sqlName = $tempValue['id'];
	
	if (is_file($_FILES[$sqlName]['tmp_name'])){
		$candidates[$currentTable] = $_FILES[$sqlName]['tmp_name'];
	}
}



include('app_import_exe_processor.php');


$overallTime = round(microtime(true) - $overallStartTime, 2) . ' seconds';


if ($count > 0){
	
	$message = "<strong>Success!</strong> Your data has been imported.";	
	echo getAlerts($message, 'success');
	
	echo "<p class='form-text'><strong>Time Spent:</strong> {$overallTime}</p>";
	
	
	echo printTableHTML($tableContent, 1, 1, 0);

	
} else {
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " No data has been imported. Please verify your file and try again.";		
	echo getAlerts($message, 'danger');
	
}

?>