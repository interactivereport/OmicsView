<?php

include_once('config_init.php');

$action = intval($_GET['action']);

echo '&nbsp;';


//Call from heatmap tool
if ($action == 1){
	
	$geneNames 		= splitData($_POST['GeneNames']);
	$sampleIDs 		= splitData($_POST['SampleIDs']);
	
	$geneNamesSize	= array_size($geneNames);
	$sampleIDsSize	= array_size($sampleIDs);
	
	if (($geneNamesSize > 0) && ($sampleIDsSize > 0)){
		
		$totalSize = $geneNamesSize * $sampleIDsSize;
		
		if ($totalSize > $APP_CONFIG['canvasxpress']['Data_Limit_Heatmap']){
			
			$message = "<p class='form-text'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Plotting too many data points may cause performance problem to your browser. Please consider to reduce the number of {$APP_MESSAGE['genes']} / sample IDs. You can also proceed with current selection of {$APP_MESSAGE['genes']} and samples by clicking the Plot Heatmap button. If the system does not respond for a long time, you can click the Cancel Submission button, then reduce the number of {$APP_MESSAGE['genes']} or samples and try again.</p>";
			
			
			echo getAlerts($message, 'warning', 'col-lg-12');
			
		}
	}
	
}


?>