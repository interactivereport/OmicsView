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

if ($_POST['comparison'] == 2){
	$_POST['direction']	= 1;
	$_POST['cutoff']	= 0;
	$_POST['limit'] 	= 0;
}

$_POST['min_matched'] = abs(intval($_POST['min_matched']));

if (array_size($geneNames) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a {$APP_MESSAGE['gene']} name and try again.";
	echo getAlerts($message, 'danger');
	exit();
}


if (array_size($comparisonIDs) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a comparison ID.";
	echo getAlerts($message, 'danger');
	exit();
}



$getGeneNameExistenceInfo = getGeneNameExistenceInfo($geneNames);


if ($getGeneNameExistenceInfo == false){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['The genes you entered do not exist in the database.']}";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}


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
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Entered ({$getGeneNameExistenceInfo['Input_Count']}):</strong></div>";
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


if ($_POST['comparison'] == 1){

	if ($getGeneNameExistenceInfo['Output_Count'] > 1){
		$message = "The <strong>Calculate the correlation against all available {$APP_MESSAGE['genes']}</strong> option is available for single {$APP_MESSAGE['gene']} only. Please revise your input and try again.";
		echo getAlerts($message, 'warning', 'col-lg-10 col-sm-12');
		exit();	
	}
	
} elseif ($_POST['comparison'] == 2){

	if ($getGeneNameExistenceInfo['Output_Count'] <= 1){
		$message = "The <strong>Calculate the correlations among the entered {$APP_MESSAGE['genes']} only </strong> option requires at least two {$APP_MESSAGE['genes']}. Please revise your input and try again.";
		echo getAlerts($message, 'warning', 'col-lg-10 col-sm-12');
		exit();	
	}
	
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


$results = prepareGeneComparisonCorrelation($geneIndexes, $comparisonIndexes, $_POST, $_POST['data_source'], $_POST['data_source_private_project_indexes']);



if (!$results['Summary']['HasResult']){
	$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your options and try again.</div>";
	
	if ($results['Summary']['Rejected']['Cutoff'] > 0){
		$needAdjustment = true;
		$message .= "<div>&bull; The cut-off ({$_POST['cutoff']}) may be too high. Please try to lower the cut-off value.</div>";
	}
	
	if ($results['Summary']['Rejected']['Min_Matched'] > 0){
		$needAdjustment = true;
		$message .= "<div>&bull; The minimum number of data point ({$_POST['min_matched']}%) may be too high. Please try to lower the value.</div>";
	}
	
	if ($needAdjustment){
		$message .= "<div>Please click <a data-toggle='modal' href='#advancedOptionSection'>Advanced Options</a> to adjust the values.</div>";
	}
	
	
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

		$count 		= array_size($results['Gene_Source']);
		$modalID 	= 'summaryGeneSource';
		$modalTitle = "<h4 class='modal-title'>Source {$APP_MESSAGE['Genes']}</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-6 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Gene Names']} ({$count}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $results['Gene_Source']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= "# of Source {$APP_MESSAGE['Genes']}";
		$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
	}
	
	if (true){

		$count 		= array_size($results['Gene_Target']);
		$modalID 	= 'summaryGeneTarget';
		$modalTitle = "<h4 class='modal-title'>Matched {$APP_MESSAGE['Genes']}</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-6 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Gene Names']} ({$count}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $results['Gene_Target']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= "# of Matched {$APP_MESSAGE['Genes']}";
		$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
	}
	
	
	if (true){
		
		natcasesort($results['Summary']['ComparisonIndex']);
		$count 		= array_size($results['Summary']['ComparisonIndex']);
		$modalID 	= 'summaryComparison';
		$modalTitle = "<h4 class='modal-title'>Comparison IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-6 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Comparison IDs ({$count}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $results['Summary']['ComparisonIndex']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= '# of Comparisons';
		$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
	}
	
	
	if ($results['Summary']['min_matched_count'] > 0){
		
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= 'Minimum # of Data Point to be Included in the Result';
		$tableContent['Body'][$currentRow]['Value'][2]	= "{$results['Summary']['min_matched_count']} ({$_POST['min_matched']}%)";
	}
	
	
	echo printTableHTML($tableContent, 1, 1, 0);

	$endTime = microtime(true);
	echo "<p class='form-text'>Time Spent: " . round($endTime - $APP_CONFIG['StartTime'], 2) . " seconds.</p>";
	
	echo "</div>";
}



if (true){
	$key 	= $results['Summary']['cacheKey'];
	$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);
	
	if (true){	
		unset($researchProjectAPI);
		$researchProjectAPI['Title'] 			= 'Correlation Tools Using Comparisons';
		$researchProjectAPI['Type'] 			= 'Comparison Plotting Tool';
		$researchProjectAPI['Source_Page'] 		= 'Correlation Tools Using Comparisons';
		$researchProjectAPI['URL'] 				= "gene_expressions/app_correlation_genes_comparisons.php?key={$urlKey}";
		$researchProjectAPI['Base64_Image_ID'] 	= '';
		$researchProjectAPI['Parameters'] 		= $urlKey;
		include('app_research_project_api_modal.php');
			
		unset($researchProjectAPI);
	}

	
	$geneCandidates = array_clean(array_merge(array_values($results['Gene_Source']), array_values($results['Gene_Target'])));
	
	
	if ($_POST['data_source']['private'] == ''){
		unset($dataArray);
		$dataArray['GeneNames'] = $geneCandidates;
		$dataArray['ComparisonIDs'] = array_values($results['Summary']['ComparisonIndex']);
		$dataArray['searchOption'] = 1;
		$dataArray['data_source'] = $_POST['data_source'];
		$exportURLKey 			= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
		$URLs[]					= "<a href='app_heatmap.php?key={$exportURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-chart-bar') . "Heatmap</a>";
	}
	
	
	
	unset($dataArray);
	$dataArray['GeneNames'] = $geneCandidates;
	$dataArray['ComparisonIDs'] = array_values($results['Summary']['ComparisonIndex']);
	$dataArray['data_source'] = $_POST['data_source'];
	$dataArray['data_source_private_project_indexes'] = $_POST['data_source_private_project_indexes'];
	$exportURLKey 			= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	$URLs[]					= "<a href='app_export_genes_comparisons.php?key={$exportURLKey}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . "Export Genes and Comparisons</a>";
	
	
	unset($dataArray);
	$dataArray['Input'] = $geneCandidates;
	$count				= array_size($geneCandidates);
	$newListURLKey		= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	$URLs[]				= "<a href='app_list_new.php?Category=Gene&key={$newListURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-file') . "Create New Gene List ({$count})</a>";
	
	
	unset($dataArray);
	$dataArray['Input'] = array_values($results['Summary']['ComparisonIndex']);
	$count				= array_size($results['Summary']['ComparisonIndex']);
	$newListURLKey		= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	$URLs[]				= "<a href='app_list_new.php?Category=Comparison&key={$newListURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-file') . "Create New Comparison List ({$count})</a>";
	
	
	echo "<p>" . implode(" &nbsp;&bull;&nbsp; ", $URLs) . "</p>";
	echo "<br/>";
}


if (true){
	
	echo "<div class='row'>";

		echo "<div class='col-12'>";

			echo "<table id='resultTable' class='table table-sm table-striped w-100'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th class='text-left text-nowrap'>Source Gene</th>";
						echo "<th class='text-left text-nowrap'>Matched Gene</th>";
						echo "<th class='text-left text-nowrap'>Correlation Coefficient</th>";
						echo "<th class='text-left text-nowrap'>R<sup>2</sup></th>";
						echo "<th class='text-left text-nowrap'># of Data Point</th>";
						echo "<th class='text-left text-nowrap'>Actions</th>";
					echo "</tr>";
				echo "</thead>";
			echo "</table>";

		echo "</div>";

	echo "</div>";
	
	
	
	$modalID 	= 'plotModal';
	$modalTitle = 'Title';
	$modalBody 	= "The system is preparing for the plot..." . printFontAwesomeIcon('fas fa-spinner fa-spin');
	
	echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height modal-body-full-width');
	

}





?>


<script type="text/javascript">

$(document).ready(function(){
	
	$("#plotModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		
		$(this).find(".modal-body").html("<?php echo $modalBody; ?>");
		$(this).find(".modal-body").load(link.attr("href"));
		
		
		$(this).find(".modal-header").html('<h4>' + link.attr('modal-header') + '</h4>');
		
	});
	
	$("#plotModal").on("hidden.bs.modal", function(e) {
		$(this).find(".modal-body").html("<?php echo $modalBody; ?>");
		
		$(this).find(".modal-header").empty();
	});
	
	

	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_correlation_genes_comparisons.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "Correlation Tools Using Comparisons", URL);
	}
	<?php } ?>
	
	
	$('#resultTable').DataTable({
        "processing": 	true,
        "serverSide": 	true,
		"scrollX": 		true,
		"ajax": {
				"url": "app_correlation_genes_comparisons_table_ajax.php?key=<?php echo $key; ?>",
				"type": "POST"
			},
		"order": [[ 0, "asc" ]],
		dom: '<"row col-12"l>Bfrtip',
		buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
			{
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },

        ],
		"aoColumnDefs": [{
				'bSortable': false,
				'aTargets': [ 5 ]}
    			]

    });


});

</script>