<?php

include_once('config_init.php');
buildInternalDataKnownMap();

$sessionID = $_POST['sessionID'];


$inputArray = $_SESSION['Internal_Data'][$sessionID];
if (array_size($inputArray) <= 0){
	$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The files are not available. Please try to import the data again.</div>";
	echo getAlerts($message, 'danger');
	exit();	
}


foreach($_POST as $tempKey => $tempValue){
	
	$temp = explode('_', $tempKey);
	
	if (array_size($temp) == 2){
		$choice[$temp[0]][$temp[1]] = $tempValue;
	}
}
$inputArray['Choice'] = $choice;


$results = checkInternalDataChoice($inputArray);



if (array_size($results['Error_Message']) > 0){
	
	echo "<hr/>";
	
	unset($message);
	
	$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " We have found the following error:</div>";
	
	foreach($results['Error_Message'] as $tempKey => $tempValue){
		$message .= "<div>&nbsp;&nbsp;&bull;&nbsp;&nbsp;{$tempValue} </div>";
	}
	echo getAlerts($message, 'danger');
	exit();
}



if (true){
	unset($wizard);
	$wizard[1]['Icon'] 		= printFontAwesomeIcon('far fa-copy');
	$wizard[1]['Title']		= 'Upload Files';
	$wizard[1]['State']		= 2;
	$wizard[1]['Link']		= 'javascript:void(0);';
	$wizard[1]['Link-Class']= 'showForm1Trigger';
	
	
	$wizard[2]['Icon'] 		= printFontAwesomeIcon('far fa-check-square');
	$wizard[2]['Title']		= 'Verify Headers';
	$wizard[2]['State']		= 2;
	$wizard[2]['Link']		= 'javascript:void(0);';
	$wizard[2]['Link-Class']= 'showForm2Trigger';
	
	
	
	$wizard[3]['Icon'] 		= printFontAwesomeIcon('far fa-save');
	$wizard[3]['Title']		= 'Save to Database';
	$wizard[3]['State']		= 1;
	
	echo "<div class='form-group row'>";
		echo printWizard($wizard);
	echo "</div>";
}

if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-12'>";
			echo "<h2 class='pt-3'>3. Save to Database</h2>";
			echo "<hr/>";
		echo "</div>";
	echo "</div>";
}


if ($results['Job']['ID'] > 0){
	$message = printFontAwesomeIcon('fas fa-check text-success') . " The system is importing your data. Please click <a href='app_internal_data_review.php?ID={$results['Job']['ID']}'>here</a> to review the progress.";
	echo getAlerts($message, 'success');
} else {
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The system could not save your data. Please contact the system administrator for details.";
	echo getAlerts($message, 'danger');
}

?>

<script type="text/javascript">

$(document).ready(function(){

	$('#form_application1').hide();	
	$('#form_application2').hide();
	
	<?php if ($results['Job']['ID'] > 0){ ?>
		window.location = "<?php echo "app_internal_data_review.php?ID={$results['Job']['ID']}"; ?>";
	<?php } ?>
	
});

</script>