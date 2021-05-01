<?php
include_once('config_init.php');



$jobInfo = getInternalDataJob($_POST['ID']);

if (!$jobInfo['canUpdate']){
	$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " You do not have permission to update this dataset.</p>";
	echo getAlerts($message, 'danger');
	exit();
}

if (!$_POST['Gene_Expression_Plot_Default']){
	
	if (array_size($_POST['Plot_Columns']) <= 0){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a sample attribute first.";
		echo getAlerts($message, 'danger');
		exit();
	}
	
	
	$results = validateCanvasXpressJSON($_POST['JSON']);
		
	if ($results !== FALSE){
		if (!$results['Result']){
			$message = "<p>" . printFontAwesomeIcon('fas fa-times text-danger') . "&nbsp; Error. Your JSON code is not valid: {$results['Error']}</p>";
			echo getAlerts($message, 'danger');
			exit();
		}
	}
	
	
}


updateInternalDataSettings($_POST);


?>
<script type="text/javascript">
$(document).ready(function(){

	window.location = 'app_internal_data_review.php?Save=1&ID=<?php echo $_POST['ID']; ?>';

});


</script>
