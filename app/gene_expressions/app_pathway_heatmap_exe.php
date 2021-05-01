<?php

include_once('config_init.php');

$_POST['filter_by_top'] 	= abs($_POST['filter_by_top']);
$_POST['ComparisonIDs'] 	= splitData($_POST['ComparisonIDs']);
$uniqueID					= 'unique_' . md5(microtime(true));


if (($_POST['filter_by_value_enable']) && ($_POST['filter_by_value'] == '')){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter a numeric value in the data filter.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}

$_POST['filter_by_value'] 	= floatval($_POST['filter_by_value']);

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);
$getComparisonIDsExistenceInfo 	= getComparisonIDsExistenceInfo($_POST['ComparisonIDs'], '', $_POST['data_source_private_project_indexes']);

echo "<hr/>";

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

if (!$_POST['display_comparison_info']){
	$comparisons = search_comparisons($_POST['ComparisonIDs'], '', $_POST['data_source'], $_POST['data_source_private_project_indexes']);
} else {
	
	unset($fields);
	$fields['ComparisonIndex'] = 'ComparisonIndex';
	$fields['ComparisonID'] = 'ComparisonID';
	
	foreach($BXAF_CONFIG['COMPARISON_INFO']['Sequence'] as $tempKey => $tempValue){
		$fields[$tempValue] = $tempValue;
	}
	
	$fields = '`' . implode('`, `', $fields) . '`';
	
	$comparisons = search_comparisons($_POST['ComparisonIDs'], $fields, $_POST['data_source'], $_POST['data_source_private_project_indexes']);

	$comparisonInfoForDisplayCount = array();
	foreach($comparisons as $comparisonIndex => $comparisonInfo){
		
		
		
		$comparisonInfoForDisplay = getComparisonInfoForDisplay($comparisonInfo);
		
		$comparisonInfoForDisplayCount[$comparisonInfoForDisplay]++;
		
		if ($comparisonInfoForDisplayCount[$comparisonInfoForDisplay] > 1){
			
			$comparisonInfoForDisplay = "{$comparisonInfoForDisplay}_{$comparisonInfoForDisplayCount[$comparisonInfoForDisplay]}";
		}
		
		
		$comparisons[$comparisonIndex] = $comparisonInfoForDisplay;
		
	}

}




echo "<input type='hidden' id='svgCode1' value=''/>";
echo "<input type='hidden' id='pngCode1' value=''/>";



echo "<input type='hidden' id='svgCode2' value=''/>";
echo "<input type='hidden' id='pngCode2' value=''/>";

$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);

if ($_POST['category'] == 'PAGE_List') {
	

	echo "<input type='hidden' id='chartTitle1' value='PAGE Heatmap'/>";
	echo "<input type='hidden' id='chartTitle2' value=''/>";
	
	include_once('app_pathway_heatmap_exe_page.php');
	
	
} else {
	
	echo "<input type='hidden' id='chartTitle1' value='Up-Regulated'/>";
	echo "<input type='hidden' id='chartTitle2' value='Down-Regulated'/>";
	
	include_once('app_pathway_heatmap_exe_homer.php');
}





?>


<script type="text/javascript">

$(document).ready(function(){

					

	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_pathway_heatmap.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "Pairwise View", URL);
	}
	<?php } ?>
	
});


</script>