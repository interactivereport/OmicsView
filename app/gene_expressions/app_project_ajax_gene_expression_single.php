<?php

include('config_init.php');


$_POST['ProjectIDs'] 	= splitData($_POST['ProjectIDs']);
$getProjectIDsExistenceInfo = getProjectIDsExistenceInfo($_POST['ProjectIDs'], array_keys(internal_data_get_accessible_project()));

if ($getProjectIDsExistenceInfo['Output_Count'] != 1){
	exit();
}

$projectIndex = $getProjectIDsExistenceInfo['ProjectIndexes'][0];
if (internal_data_is_public($projectIndex)){
	exit();	
}


$projectArray = get_one_record_by_id('Project', $projectIndex);	
if ($projectArray['Job_ID'] <= 0){
	exit();	
}

$jobInfo = getInternalDataJob($projectArray['Job_ID']);
if (array_size($jobInfo['Gene_Expression_Plot_Details']) <= 0){
	exit();	
}

$dataArray = array();
		
$dataArray['Plot_Columns'] = $jobInfo['Gene_Expression_Plot_Details']['Plot_Columns'];
$dataArray['groupSamples'] 	= $jobInfo['Gene_Expression_Plot_Details']['groupSamples'];
$dataArray['sortBy'] 		= $jobInfo['Gene_Expression_Plot_Details']['sortBy'];
		
$dataArray['colorBy'] 		= $jobInfo['Gene_Expression_Plot_Details']['colorBy'];
$dataArray['shapeBy'] 		= $jobInfo['Gene_Expression_Plot_Details']['shapeBy'];
$dataArray['segregate'] 	= $jobInfo['Gene_Expression_Plot_Details']['segregate'];

$dataArray['plot_height'] 	= $jobInfo['Gene_Expression_Plot_Details']['plot_height'];
$dataArray['plot_width'] 	= $jobInfo['Gene_Expression_Plot_Details']['plot_width'];

$dataArray['JSON'] 			= base64_encode(serialize($jobInfo['Gene_Expression_Plot_Details']['JSON']));
	
echo "<p class='form-text'>The default project settings have been loaded.</p>";
?>
<script type="text/javascript">

$(document).ready(function(){
	
	$('.Plot_Columns').prop('checked', false);
	<?php
		foreach($dataArray['Plot_Columns'] as $tempKey => $currentColumn){
			$checkBoxClass = "Plot_Columns_" . md5($currentColumn);
	?>
		$('.<?php echo $checkBoxClass; ?>').prop('checked', true);
	<?php } ?>
	updateSelectedSampleAttributes();
	
	
	$('#groupSamples').val('<?php echo $dataArray['groupSamples']; ?>');
	$('#sortBy').val('<?php echo $dataArray['sortBy']; ?>');
	$('#colorBy').val('<?php echo $dataArray['colorBy']; ?>');
	$('#shapeBy').val('<?php echo $dataArray['shapeBy']; ?>');
	$('#segregate').val('<?php echo $dataArray['segregate']; ?>');
	$('#plot_height').val('<?php echo $dataArray['plot_height']; ?>');
	$('#plot_width').val('<?php echo $dataArray['plot_width']; ?>');
	
	$('#JSON').val('<?php echo $dataArray['JSON']; ?>');
	$('JSON_Choice').prop('checked', true);
	
});
</script>

