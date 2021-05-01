<?php

include_once('config_init.php');

echo "<hr/>";


$geneNames 			= splitData($_POST['GeneNames']);
$_POST['GeneNames'] = $geneNames;

$comparisonIDs 			= splitData($_POST['ComparisonIDs']);
$_POST['ComparisonIDs'] = $comparisonIDs;

$genePlotColumns 	= array_clean($_POST['Gene_Plot_Columns']);
$comparisonPlotColumns 	= array_clean($_POST['Comparison_Plot_Columns']);

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

if (array_size($comparisonIDs) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a comparison ID.";
	echo getAlerts($message, 'danger');
	exit();
}


$getGeneNameExistenceInfo = getGeneNameExistenceInfo($geneNames);

if ($getGeneNameExistenceInfo['hasMissing']){
			
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['Some of the genes you entered do not exist in the database.']} Please click <a href='javascript:void(0);' id='geneMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;
	
	echo "<div id='geneMissingInfo' class='startHidden'>";
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= $APP_MESSAGE['# of Genes'];
		
		
		$tableContent['Body'][1]['Value'][1]	= $APP_MESSAGE['# of Genes Entered'];
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= $APP_MESSAGE['# of Genes Available'];
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= $APP_MESSAGE['# of Genes Not Available'];
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'geneNameExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of {$APP_MESSAGE['Genes']}</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Genes Entered ({$getGeneNameExistenceInfo['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$getGeneNameExistenceInfo['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$getGeneNameExistenceInfo['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	echo "</div>";
	
}


$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($comparisonIDs, '', $_POST['data_source_private_project_indexes']);

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




$geneIndexes 	= $getGeneNameExistenceInfo['GeneIndexes'];
$comparisonIndexes 	= $getComparisonIDsExistenceInfo['ComparisonIndexes'];

$results = exportGeneComparisonData($geneIndexes, $comparisonIndexes, $genePlotColumns, $comparisonPlotColumns, $_POST['data_source'], $_POST['data_source_private_project_indexes']);



if (!$results['Summary']['HasNumericValue']){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	exit();
	
}



if (true){
	
	$message = "<p>Please click <a href='javascript:void(0);' id='summaryTrigger' class='forceLink'>here</a> for the search summary.</p>";
	echo $message;
	
	echo "<div id='summarySection' class='startHidden'>";
	
	echo "<h3>Summary of Data</h3>";
	
	
	
	unset($tableContent, $currentRow);
	$tableContent['Header'][1]		= 'Category';
	$tableContent['Header'][2] 		= '# of Match';
	
	if (true){
		
		natcasesort($results['Summary']['Gene']);
		$count 		= array_size($results['Summary']['Gene']);
		$modalID 	= 'summaryGene';
		$modalTitle = "<h4 class='modal-title'>{$APP_MESSAGE['Gene Names']}</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-12 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Gene Names']} ({$count}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $results['Summary']['Gene']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= $APP_MESSAGE['# of Genes'];
		$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
	}
	
	
	if (true){
		
		natcasesort($results['Summary']['ComparisonIndex']);
		$count 		= array_size($results['Summary']['ComparisonIndex']);
		$modalID 	= 'summaryComparison';
		$modalTitle = "<h4 class='modal-title'>Comparison IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-12 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Comparison IDs ({$count}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $results['Summary']['ComparisonIndex']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= '# of Comparisons';
		$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
		echo printTableHTML($tableContent, 1, 1, 0);
	}

	$endTime = microtime(true);
	if (isDebugMode()){
		echo "<p class='form-text'>Time Spent: " . round($endTime - $APP_CONFIG['StartTime'], 2) . " seconds.</p>";
	}
	
	echo "</div>";
}



if (true){
	
	$key 		= putSQLCacheWithoutKey($results, '', 'exportGeneComparisonData', 1);
	
	$message	= '';
	if ($results['Summary']['Export_Limit']){
		$message .= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " {$BXAF_CONFIG['EXPORT_LIMIT_OPTIONS']['Gene_Comparison_Message']}</p>";
	}
	$message 	.= "<p>" . printFontAwesomeIcon('fas fa-download') . "<strong>Download:</strong></p>";

	unset($URLs);
	foreach($results['Summary']['Value Names'] as $tempKey => $tempValue){
		$URLs[] = "<a href='app_export_genes_download.php?key={$key}&value={$tempKey}&filename=Genes_Comparisons_{$tempKey}.csv'>{$tempValue}</a>";
	}
	$message 	.= "<p><strong>Matrix Format</strong>: " . implode('&nbsp; &bull; &nbsp;', $URLs) . "</p>";
	
	
	
	$message 	.= "<p><strong>Table Format</strong>: <a href='app_export_genes_download_all.php?key={$key}&filename=Genes_Comparisons_All.csv'>All Data Types</a></p>";
	
	if (!$results['Summary']['Export_Limit']){
		if ($_POST['data_source']['private'] == ''){
			$rawData = "<a href='app_export_genes_download.php?raw=1&key={$key}&filename=Genes_Comparisons_Raw.txt'>Raw Data (Tabix Output)</a>";
			$message 	.= "<p>- " . $rawData . "</p>";
		}
	}
	
	echo $message;
}
$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);


if (isDebugMode()){
	echo "<hr/>";
	echo processTimer($BXAPP_TIMER, 1);
}



?>



<script type="text/javascript">

$(document).ready(function(){
	

	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_export_genes_comparisons.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "<?php echo $APP_MESSAGE['Export Genes and Comparisons']; ?>", URL);
	}
	<?php } ?>

});

</script>
