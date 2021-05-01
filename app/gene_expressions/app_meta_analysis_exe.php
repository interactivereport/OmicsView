<?php

include_once('config_init.php');

$category = 'Gene';

echo "<hr/>";


$geneNames 			= splitData($_POST['GeneNames']);
$_POST['GeneNames'] = $geneNames;

$comparisonIDs 			= splitData($_POST['ComparisonIDs']);
$_POST['ComparisonIDs'] = $comparisonIDs;


$_POST['Missing_Total'] 	= abs(floatval($_POST['Missing_Total']));
$_POST['Statistic_Cutoff'] 	= abs(floatval($_POST['Statistic_Cutoff']));
$_POST['LogFC_Cutoff'] 		= floatval($_POST['LogFC_Cutoff']);

if ($_POST['Statistical_Type'] != 'FDR'){
	$_POST['Statistical_Type'] = 'p-value';
}


$_POST['n_data_points_value'] 			= floatval($_POST['n_data_points_value']);
$_POST['RP_Pval_value'] 				= floatval($_POST['RP_Pval_value']);
$_POST['RP_logFC_max_value'] 			= floatval($_POST['RP_logFC_max_value']);
$_POST['RP_logFC_min_value'] 			= floatval($_POST['RP_logFC_min_value']);
$_POST['Combined_Pval_maxP_value'] 		= floatval($_POST['Combined_Pval_maxP_value']);
$_POST['Combined_Pval_Fisher_value'] 	= floatval($_POST['Combined_Pval_Fisher_value']);
$_POST['up_per_value'] 					= floatval($_POST['up_per_value']);
$_POST['down_per_value'] 				= floatval($_POST['down_per_value']);



$genePlotColumns 	= array_clean($_POST['Gene_Plot_Columns']);

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
		$tableContent['Header'][2] 		= '# of Genes';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Genes Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Genes Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Genes Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'geneNameExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Genes</h4>";
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




$geneIndexes 		= $getGeneNameExistenceInfo['GeneIndexes'];
$comparisonIndexes 	= $getComparisonIDsExistenceInfo['ComparisonIndexes'];


$otherOptions = array();
$otherOptions['Missing_Total'] 		= $_POST['Missing_Total'];
$otherOptions['LogFC_Cutoff']		= $_POST['LogFC_Cutoff'];
$otherOptions['Statistical_Type']	= $_POST['Statistical_Type'];
$otherOptions['Statistic_Cutoff']	= $_POST['Statistic_Cutoff'];

$results = prepareMetaAnalysisData($geneIndexes, $comparisonIndexes, $genePlotColumns, $_POST['data_source'], $_POST['data_source_private_project_indexes'], $otherOptions);



if (!$results['Summary']['hasResult']){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	exit();
}



$newData = processMetaAnalysisData($results['Headers'], $results['Output_Raw']['Body'], $_POST);

$dataHeaders 	= $newData['Headers'];
$dataOutput		= $newData['Body'];

if (true){
	
	$message = "<p>Please click <a href='javascript:void(0);' id='summaryTrigger' class='forceLink'>here</a> for the search summary.</p>";
	echo $message;
	
	echo "<div id='summarySection' class='startHidden'>";
	
		echo "<h3>Summary of Data</h3>";
		unset($tableContent, $currentRow);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Match';
		
		if (array_size($results['Summary']['Gene']) > 0){
			
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
			$tableContent['Body'][$currentRow]['Value'][1]	= '# of Genes';
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
		}
		
		
		if (true){
			$currentRow++;
			$tableContent['Body'][$currentRow]['Value'][1]	= '# of Genes from Meta Analysis';
			$tableContent['Body'][$currentRow]['Value'][2]	= number_format(array_size($results['Output_Raw']['Body']));
		}
		
		if (true){
			$currentRow++;
			$tableContent['Body'][$currentRow]['Value'][1]	= '# of Genes after Applying Filters';
			$tableContent['Body'][$currentRow]['Value'][2]	= number_format(array_size($dataOutput));
		}
		
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
	
		$endTime = microtime(true);
		if (isDebugMode()){
			echo "<p class='form-text'>Time Spent: " . round($endTime - $APP_CONFIG['StartTime'], 2) . " seconds.</p>";
		}
	
	
	echo "</div>";
}


$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);


if (isDebugMode()){
	echo "<hr/>";
	echo processTimer($BXAPP_TIMER, 1);
}




if (array_size($dataOutput) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your <strong>data filters</strong> and try again.";
	echo getAlerts($message, 'danger');
	exit();
}




unset($dataHTML, $dataPrint);
unset($exportHeaderCount);
$dataHTML['Headers']['Checkbox'] = "<div class='text-center'><input type='checkbox' class='selectAllTrigger'/></div>";
$dataHTML['Headers']['Actions'] = 'Actions';

$sortID = 0;
$currentCount = 0;

foreach($dataHeaders as $currentColumn => $currentColumnDetails){
	
	$currentCount++;
	
	if (!$currentColumnDetails['Display']) continue;
	
	if ($currentColumn == 'RP_Pval') $sortID = $currentCount;
	
	
	
	$dataHTML['Headers'][$currentColumn] = $currentColumnDetails['Title_HTML'];
	
	$dataPrint['Headers'][$currentColumn] = $currentColumnDetails['Title'];
}



unset($currentCount);

foreach($dataOutput as $currentCount => $currentRecord){
	unset($currentActions);
	$tempURL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$currentRecord['GeneIndex']}";
	$currentActions[] 	= "<a title='Review Details' href='{$tempURL}'>" . printFontAwesomeIcon('fas fa-list') . "</a>";
	
	if ($APP_CONFIG['APP']['Module']['RNA-Seq']){
		$tempURL = "app_gene_expression_rnaseq_single.php?GeneName={$currentRecord['GeneName']}";
		$currentActions[] 	= "<a title='Gene Expressions from RNA-Seq'	href='{$tempURL}'>" . printFontAwesomeIcon('fas fa-chart-pie') . "</a>";
	}
	
	if ($APP_CONFIG['APP']['Module']['Microarray']){
		$tempURL = "app_gene_expression_microarray_single.php?GeneName={$currentRecord['GeneName']}";
		$currentActions[] 	= "<a title='Gene Expressions from Microarray'	href='{$tempURL}'>" . printFontAwesomeIcon('far fa-chart-bar') . "</a>";
	}

	$dataHTML['Body'][$currentCount][]= "<div class='text-center'><input currentcount='{$currentCount}' type='checkbox' class='recordCheckbox' value='{$currentRecord['GeneIndex']}'/></div>";
	
	$dataHTML['Body'][$currentCount][] = implode('&nbsp;', $currentActions);
	
	foreach($currentRecord as $currentHeader => $currentValue){
		
		if (!$dataHeaders[$currentHeader]['Display']) continue;
		
		$dataPrint['Body'][$currentCount][] = $currentValue;
		
		if ($dataHeaders[$currentHeader]['Numeric']){
			if (is_numeric($currentValue)){
				
				$decimal = $dataHeaders[$currentHeader]['Decimal'];
				if ($decimal <= 0) $decimal = 5;
				
				$currentValueForSort = number_format($currentValue, 10);
				$currentValue = round($currentValue, $decimal);
				$currentColor = getStatScaleColor($currentValue, $dataHeaders[$currentHeader]['Type']);
				$dataHTML['Body'][$currentCount][] = "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			} else {
				$dataHTML['Body'][$currentCount][] = $currentValue;
			}

		} else {
			$dataHTML['Body'][$currentCount][] = $currentValue;
		}
		
	}
	
}



if (true){
	$actions[] = "<a href='javascript:void(0);' class='createListTrigger btn btn-success'>" . printFontAwesomeIcon('far fa-file') . "&nbsp;{$APP_CONFIG['APP']['List_Category'][$category]['Save_Titles']}</a>";
	
	$feedback[] = "<div id='Record_Required_createListTrigger' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
}

if (true){
	
	unset($tableOption);
	$tableOption['id'] 		= 'resultTable';
	

	$tableOption['headers']			= $dataHTML['Headers'];
	$tableOption['dataKey']			= putSQLCacheWithoutKey($dataHTML, '', 'dataTableHTMLKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
	$tableOption['disableButton'] 	= false;
	

	if ($sortID > 0){
		$tableOption['order']			= "{$sortID}" . ', "asc"';
	} else {
		$tableOption['order']			= '2, "asc"';
	}
	
	
	$tableOption['pageLength']		= 100;
	
	$dataPrintKey = putSQLCacheWithoutKey($dataPrint, '', 'dataTablePrintKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
	

	$actions[] = "<a href='javascript:void(0);' class='btn btn-info bubblePlot'>" . printFontAwesomeIcon('fas fa-chart-pie') . "&nbsp;Bubble Plot</a>";
	$feedback[] = "<div id='bubblePlot_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
	
	
	$actions[] = "<a href='app_export_genes_comparisons.php?key={$urlKey}' class='btn btn-warning'  target='_blank'>" . printFontAwesomeIcon('fas fa-download') . "&nbsp;Export Genes and Comparisons</a>";
	
	
	
	$actions[] = "<a href='app_common_table_download.php?key={$dataPrintKey}&filename=search_result.csv' class='btn btn-secondary'>" . printFontAwesomeIcon('fas fa-download') . "&nbsp;Download</a>";
	
	
	
	
	//Checkbox
	$tableOption['columnScript'][] = '{"orderable": false}';
	$tableOption['columnScript'][] = '{"orderable": false}';
	
	for ($i = 2; $i < array_size($dataHTML['Headers']); $i++){
		$tableOption['columnScript'][] = 'null';
	}
	
	$tableOption['columnScript'] = implode(',', $tableOption['columnScript']);
//	unset($tableOption['columnScript']);



	echo "<div>" . implode('&nbsp; &nbsp;', $actions) . "</div>";
	echo "<br/>";
	echo "<div>" . implode("</div><div>", $feedback) . "</div>";
	echo "<br/>";
	

	include('app_common_table_html.php');
	
	echo "<input type='hidden' id='urlKey' value='{$urlKey}'/>";
	
	
}




?>



<script type="text/javascript">

$(document).ready(function(){
	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_meta_analysis.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "Meta Analysis", URL);
	}
	<?php } ?>

});

</script>
