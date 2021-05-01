<?php

include_once('config_init.php');

$_POST['filter_by_top'] 	= abs($_POST['filter_by_top']);
$_POST['ComparisonIDs'] 	= splitData($_POST['ComparisonIDs']);


if (($_POST['filter_by_value_enable']) && ($_POST['filter_by_value'] == '')){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter a numeric value in the data filter.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}

$_POST['filter_by_value'] 	= floatval($_POST['filter_by_value']);

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);
$getComparisonIDsExistenceInfo 	= getComparisonIDsExistenceInfo($_POST['ComparisonIDs'], '', $_POST['data_source_private_project_indexes']);



if ($getComparisonIDsExistenceInfo == false){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The comparison IDs you entered do not exist in the database.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}

if ($getComparisonIDsExistenceInfo['hasMissing']){
			
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the comparison IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='comparisonMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;

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
	
}

$comparisons					= search_comparisons($_POST['ComparisonIDs'], '', $_POST['data_source'], $_POST['data_source_private_project_indexes']);


echo "<hr/>";

if ($_POST['category'] == 'PAGE_List') {
	
	
	$candidate = preparePAGEData(array_keys($comparisons), $_POST['filter_by_value_enable'], $_POST['filter_by_value'], $_POST['filter_by_top']);
	
	

	$geneSetNames 					= array_keys($candidate);
	$geneSetNames 					= array_clean($geneSetNames);
	$geneSetNamesCount 				= array_size($geneSetNames);
	$geneSetNamesCount_Formatted 	= number_format($geneSetNamesCount);
	
	
	if ($geneSetNamesCount <= 0){
		
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are no {$APP_MESSAGE['gene']} sets match your search conditions. Please try again with a different value.";
		echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
		exit();	
	} else {
		
		echo "<p>There are {$geneSetNamesCount_Formatted} {$APP_MESSAGE['gene']} sets in the search result.</p>";
		
		if (true){
			echo "<div class='row'>";
			echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
				$values = implode("\n", $geneSetNames);
				$exampleMessage = "Please enter one or more {$APP_MESSAGE['gene']} set, seperated by line break.";
				
				echo "<div style='margin-top:10px;'>";
					echo "<textarea class='form-control' rows='8' name='GeneSet' id='GeneSet' placeholder='{$exampleMessage}'>{$values}</textarea>";
				echo "</div>";
				
			echo "</div>";
			echo "</div>";
		}
	
	}
	
	
	
} else {
	
	foreach(array('Up', 'Down') as $tempKeyX => $direction){
		
		$candidate = prepareHomerData(array_keys($comparisons), $direction, $_POST['category'], $_POST['filter_by_value_enable'], $_POST['filter_by_value'], $_POST['filter_by_top']);

		$geneSetNames[$direction]					= array_keys($candidate);
		$geneSetNames[$direction]					= array_clean($geneSetNames[$direction]);
		$geneSetNamesCount[$direction] 				= array_size($geneSetNames[$direction]);
		$geneSetNamesCount_Formatted[$direction] 	= number_format($geneSetNamesCount[$direction]);

	}
	
	
	if (($geneSetNamesCount['Up'] <= 0) && ($geneSetNamesCount['Down'] <= 0)){
	
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are no pathways match your search conditions. Please try again with a different value.";
		echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
		exit();	
	}
	
	if (true){
		echo "<div class='row'>";
			echo "<div class='col-lg-5 col-md-6 col-sm-12 col-xs-12'>";
			
				if ($geneSetNamesCount['Up'] > 0){
					$values = implode("\n", $geneSetNames['Up']);
					$exampleMessage = 'Please enter one or more pathway, seperated by line break.';
					
					echo "<div style='margin-top:10px;'>";
						echo "<p><strong>Upregulated Pathways ({$geneSetNamesCount_Formatted['Up']}):</strong></p>";
						echo "<textarea class='form-control' rows='8' name='GeneSet_Up' id='GeneSet_Up' placeholder='{$exampleMessage}'>{$values}</textarea>";
					echo "</div>";
				} else {
					echo "<p>There are no {$APP_MESSAGE['gene']} sets available.</p>";	
				}
				
			echo "</div>";
			
			
			echo "<div class='col-lg-5 col-md-6 col-sm-12 col-xs-12'>";
				
				if ($geneSetNamesCount['Down'] > 0){
					$values = implode("\n", $geneSetNames['Down']);
					$exampleMessage = 'Please enter one or more pathways, seperated by line break.';
					
					echo "<div style='margin-top:10px;'>";
						echo "<p><strong>Downregulated Pathways ({$geneSetNamesCount_Formatted['Down']}):</strong></p>";
						echo "<textarea class='form-control' rows='8' name='GeneSet_Down' id='GeneSet_Down' placeholder='{$exampleMessage}'>{$values}</textarea>";
					echo "</div>";
				} else {
					echo "<p>There are no pathways available.</p>";	
				}
				
			echo "</div>";
			
		echo "</div>";
	}
	
}






?>

<script type="text/javascript">

$(document).ready(function(){
	
	$('#submitButtonSection').show();
	
	$('#form_application').submit();
	
});


</script>