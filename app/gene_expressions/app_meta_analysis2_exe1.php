<?php

include_once('config_init.php');



$comparisonIDs 			= splitData($_POST['ComparisonIDs']);
$_POST['ComparisonIDs'] = $comparisonIDs;
$comparisonPlotColumns 	= array_clean($_POST['Comparison_Plot_Columns']);

$_POST['rank_product_analysis'] = intval($_POST['rank_product_analysis']);


cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);
if (array_size($comparisonIDs) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a comparison ID.";
	echo getAlerts($message, 'danger');
	exit();
}

$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($comparisonIDs, '', $_POST['data_source_private_project_indexes']);

if ($getComparisonIDsExistenceInfo == false){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The comparison IDs you entered do not exist in the database.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}

if ($getComparisonIDsExistenceInfo['hasMissing']){
			
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the comparison IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='comparisonMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo getAlerts($message, 'warning');

	echo "<div id='comparisonMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Comparison IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Comparison IDs Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDsExistenceInfoSection'>{$getComparisonIDsExistenceInfo['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Comparison IDs Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDsExistenceInfoSection'>{$getComparisonIDsExistenceInfo['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Comparison IDs Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDsExistenceInfoSection'>{$getComparisonIDsExistenceInfo['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'comparisonIDsExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Comparison IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$getComparisonIDsExistenceInfo['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getComparisonIDsExistenceInfo['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$getComparisonIDsExistenceInfo['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getComparisonIDsExistenceInfo['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$getComparisonIDsExistenceInfo['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getComparisonIDsExistenceInfo['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	echo "</div>";
	
	exit();
}









$sessionID = $_POST['sessionID'];
unset($_SESSION['MetaAnalysis_GeneExpression'][$sessionID]);
$_SESSION['MetaAnalysis_GeneExpression'][$sessionID]= $_POST;


if (true){
	unset($wizard);
	$wizard[1]['Icon'] 		= printFontAwesomeIcon('far fa-copy');
	$wizard[1]['Title']		= 'Select Comparisons';
	$wizard[1]['State']		= 2;
	$wizard[1]['Link']		= 'javascript:void(0);';
	$wizard[1]['Link-Class']= 'showForm1Trigger';
	
	
	$wizard[2]['Icon'] 		= printFontAwesomeIcon('fas fa-list-ol');
	$wizard[2]['Title']		= 'Edit Samples';
	$wizard[2]['State']		= 1;
	
	$wizard[3]['Icon'] 		= printFontAwesomeIcon('far fa-chart-bar');
	$wizard[3]['Title']		= 'Review Results';
	$wizard[3]['State']		= 0;
	
	echo "<div class='form-group row'>";
		echo printWizard($wizard);
	echo "</div>";
}

if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-12'>";
			echo "<h2 class='pt-3'>2. {$wizard[2]['Title']}</h2>";
			echo "<hr/>";
		echo "</div>";
	echo "</div>";
}



unset($currentIndex);
$allComparisonRecords = search_comparisons($comparisonIDs, "ComparisonID, ComparisonIndex, Case_SampleIDs, Control_SampleIDs", $_POST['data_source'], $_POST['data_source_private_project_indexes']);


foreach($getComparisonIDsExistenceInfo['ComparisonIndexes::ComparisonID'] as $currentComparisonIndex => $currentComparisonID){
	
	$currentIndex++;
	
	$currentComparisonInfo = $allComparisonRecords[$currentComparisonID];
	
	echo "<div class='row'>";
	echo "<div class='col-lg-10'>";
	echo "<div class='card'>";
		echo "<div class='card-body'>";
	
			if (true){
				echo "<div class='form-group row'>";
					echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
						echo "<div style='margin-top:10px;'>";
							echo "<h3 class='card-title'>Comparison #{$currentIndex}:</h3>";
						echo "</div>";
						
						echo "<div class='input-group' style='margin-top:10px;'>";
							echo "<input type='text' name='Comparison{$currentIndex}_Job' id='Comparison{$currentIndex}_Job' class='form-control ' value='{$currentComparisonID}' placeholder='Please enter a comparison name'/>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
			}
			
			
			if (true){
				echo "<div class='form-group row card-text'>";
					echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>";
						echo "<div id='comparison_group_error_section_{$currentIndex}' class='startHidden comparison_group_error_section'></div>";
					echo "</div>";
				echo "</div>";
			}
			
		
			if (true){	
				echo "<div class='form-group row card-text'>";	
					echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>";
						echo "<strong>Case Sample IDs:</strong>";
						
						$currentComparisonInfo['Case_SampleIDs'] = explode(';', $currentComparisonInfo['Case_SampleIDs']);
						$values = implode("\n", $currentComparisonInfo['Case_SampleIDs']);
						echo "<div style='margin-top:10px;'>";
							echo "<textarea class='form-control heatmapInputClass' rows='8' name='Comparison{$currentIndex}_Case' id='Comparison{$currentIndex}_Case' placeholder=''>{$values}</textarea>";
						echo "</div>";
					
					echo "</div>";
					
					echo "<div class='col-lg-6 col-md-6 col-sm-12 col-xs-12'>";
						echo "<strong>Control Sample IDs:</strong>";
						
						$currentComparisonInfo['Control_SampleIDs'] = explode(';', $currentComparisonInfo['Control_SampleIDs']);
						$values = implode("\n", $currentComparisonInfo['Control_SampleIDs']);
						echo "<div style='margin-top:10px;'>";
							echo "<textarea class='form-control heatmapInputClass' rows='8' name='Comparison{$currentIndex}_Control' id='Comparison{$currentIndex}_Control' placeholder=''>{$values}</textarea>";
						echo "</div>";
					
					echo "</div>";
				echo "</div>";			
			}
			
			echo "<input type='hidden' name='Comparison{$currentIndex}_ComparisonIndex' value='{$currentComparisonIndex}'/>";
		
		echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	
	echo "<br/>";
	echo "<br/>";
	
	//echo "<hr/>";
}

echo "<input type='hidden' name='Job' value='{$currentIndex}'/>";


	
if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-6'>";
			echo "<input type='hidden' value='{$sessionID}' name='sessionID'/>";
			echo "<button  class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('fas fa-arrow-circle-right') . " Continue</button>";
			echo "&nbsp;&nbsp;<a href='javascript:void(0);' class='showForm1Trigger'>" . printFontAwesomeIcon('fas fa-reply') . ' Back</a>';
		echo "</div>";
	echo "</div>";
}


?>

<script type="text/javascript">
$(document).ready(function(){

	$('#form_application1').hide();
	$('#form_application2').show();
	$('#form_application_content2').show();
	
});

</script>