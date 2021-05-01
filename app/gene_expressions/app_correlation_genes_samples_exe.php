<?php
$BXAPP_TIMER['config_init.php'][] = microtime(true);
include_once('config_init.php');
startTimer('config_init.php');

echo "<hr/>";


$geneNames 			= splitData($_POST['GeneNames']);
$_POST['GeneNames'] = $geneNames;

$sampleIDs 			= splitData($_POST['SampleIDs']);
$_POST['SampleIDs'] = $sampleIDs;

$genePlotColumns 	= array_clean($_POST['Gene_Plot_Columns']);
$samplePlotColumns 	= array_clean($_POST['Sample_Plot_Columns']);

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

if ($_POST['comparison'] == 2){
	$_POST['direction']	= 1;
	$_POST['cutoff']	= 0;
	$_POST['limit'] 	= 0;
}

if (array_size($geneNames) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a {$APP_MESSAGE['gene']} name and try again.";
	echo getAlerts($message, 'danger');
	exit();
}



if (array_size($sampleIDs) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a sample ID.";
	echo getAlerts($message, 'danger');
	exit();
}

startTimer('exe::getGeneNameExistenceInfo()');
$getGeneNameExistenceInfo = getGeneNameExistenceInfo($geneNames);
startTimer('exe::getGeneNameExistenceInfo()');

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
		$message = "The <strong>Calculate the correlation against all available genes</strong> option is available for single {$APP_MESSAGE['gene']} only. Please revise your input and try again.";
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


startTimer('exe::getSampleIDsExistenceInfo()');
$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($sampleIDs, '', $_POST['data_source_private_project_indexes']);
startTimer('exe::getSampleIDsExistenceInfo()');

if ($getSampleIDsExistenceInfo == false){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The sample IDs you entered do not exist in the database.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}

if ($getSampleIDsExistenceInfo['hasMissing']){
			
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the sample IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='sampleMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;

	echo "<div id='sampleMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Sample IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Sample IDs Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDsExistenceInfoSection'>{$getSampleIDsExistenceInfo['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Sample IDs Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDsExistenceInfoSection'>{$getSampleIDsExistenceInfo['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Sample IDs Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDsExistenceInfoSection'>{$getSampleIDsExistenceInfo['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'sampleIDsExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Sample IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Sample IDs Entered ({$getSampleIDsExistenceInfo['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getSampleIDsExistenceInfo['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$getSampleIDsExistenceInfo['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getSampleIDsExistenceInfo['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$getSampleIDsExistenceInfo['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getSampleIDsExistenceInfo['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	echo "</div>";
	
}


unset($platformHasError, $platformErrorMessage);

if (array_size($getSampleIDsExistenceInfo['platformSummary']) > 1){
	
	$platformHasError 		= 1;
	$needExit				= 1;
	$platformErrorMessage 	= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " All samples should belong to the same platform type (RNA-Seq or Microarray).</p>";
	echo $platformErrorMessage;
	
} elseif (array_size($getSampleIDsExistenceInfo['platformSummary']['Microarray']) > 1){
	$platformHasError = 1;
	$platformErrorMessage = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " You samples come from different microarray platforms.</p>";
	
	echo $platformErrorMessage;
}


if ($platformHasError){

	
	unset($tableContent);
	$tableContent['Header'][1]		= 'Platform Type';
	$tableContent['Header'][2]		= 'Platform Name';
	$tableContent['Header'][3] 		= '# of Sample IDs';
	
	unset($currentRow);
	foreach($getSampleIDsExistenceInfo['platformSummary'] as $platformType => $tempValue1){
		
		foreach($tempValue1 as $platformName => $tempValue2){
			$currentRow++;
		
			$count = array_size($tempValue2);
			
			$modalID 	= 'sampleIDPlatform_' . md5($platformName);
			$modalTitle = "<h4 class='modal-title'>{$platformName} ({$platformType})</h4>";
			$modalBody  = "<div class='row'>";
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>Platform Type:</strong> {$platformType}</div>";
					$modalBody  .= "<div class='text-nowrap'><strong>Platform Name:</strong> {$platformName}</div>";
					$modalBody  .= "<div class='text-nowrap'><strong>Sample IDs ({$count}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $tempValue2) . "</textarea>";
				$modalBody  .= "</div>";
			$modalBody  .= "</div>";
			
			echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
			
		
			$tableContent['Body'][$currentRow]['Value'][1]	= $platformType;
			$tableContent['Body'][$currentRow]['Value'][2]	= $platformName;
			$tableContent['Body'][$currentRow]['Value'][3]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
			
			
		}
	}
	
	echo printTableHTML($tableContent, 1, 1, 0);

	if ($needExit) exit();
	
}



$geneIndexes 	= $getGeneNameExistenceInfo['GeneIndexes'];
$sampleIndexes 	= $getSampleIDsExistenceInfo['SampleIndexes'];


startTimer('exe::prepareGeneSampleCorrelation()');
$results = prepareGeneSampleCorrelation($geneIndexes, $sampleIndexes, $getSampleIDsExistenceInfo['platformType'], $_POST, $_POST['data_source'], $_POST['data_source_private_project_indexes']);
startTimer('exe::prepareGeneSampleCorrelation()');




startTimer('exe::postProcessing');
if (!$results['Summary']['HasResult']){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your options and try again.";
	
	if (($results['Summary']['Count_Total'] > 0) && ($results['Summary']['Total'] == 0)){
		$message .= "The cut-off may be too high. Please try to lower the cut-off value.";
	}
	
	
	echo getAlerts($message, 'danger');
	exit();
}



if (true){
	
	$message = "<p>Please click <a href='javascript:void(0);' id='summaryTrigger' class='forceLink'>here</a> for the search summary.</p>";
	echo $message;
	
	echo "<div id='summarySection' class='startHidden'>";
	
	echo "<h3>Summary of Data</h3>";
	
	unset($tableContent);
	$tableContent['Header'][1]		= 'Platform Name';
	$tableContent['Header'][2] 		= '# of Sample IDs';
	
	unset($currentRow);
	foreach($getSampleIDsExistenceInfo['platformSummary'] as $platformType => $tempValue1){
		
		foreach($tempValue1 as $platformName => $tempValue2){
			$currentRow++;
		
			$count = array_size($tempValue2);
			
			$modalID 	= 'sampleIDPlatform_' . md5($platformName);
			$modalTitle = "<h4 class='modal-title'>{$platformName} ({$platformType})</h4>";
			$modalBody  = "<div class='row'>";
				$modalBody  .= "<div class='col-lg-6 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>Platform Type:</strong> {$platformType}</div>";
					$modalBody  .= "<div class='text-nowrap'><strong>Platform Name:</strong> {$platformName}</div>";
					$modalBody  .= "<div class='text-nowrap'><strong>Sample IDs ({$count}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $tempValue2) . "</textarea>";
				$modalBody  .= "</div>";
			$modalBody  .= "</div>";
			
			echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
		
			$tableContent['Body'][$currentRow]['Value'][1]	= $platformName;
			$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
			
		}
	}
	
	echo printTableHTML($tableContent, 1, 1, 0);
	
	
	
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
		
		natcasesort($results['Summary']['SampleIndex']);
		$count 		= array_size($results['Summary']['SampleIndex']);
		$modalID 	= 'summarySample';
		$modalTitle = "<h4 class='modal-title'>Sample IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-6 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Sample IDs ({$count}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%; height:100%;' rows={$count}>" . implode("\n", $results['Summary']['SampleIndex']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
		$currentRow++;
		$tableContent['Body'][$currentRow]['Value'][1]	= '# of Samples';
		$tableContent['Body'][$currentRow]['Value'][2]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
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
		$researchProjectAPI['Title'] 			= "Correlation Tools Using {$APP_MESSAGE['Gene']} Expression";
		$researchProjectAPI['Type'] 			= "{$APP_MESSAGE['Gene']} Expression Plot";
		$researchProjectAPI['Source_Page'] 		= "Correlation Tools Using {$APP_MESSAGE['Gene']} Expression";
		$researchProjectAPI['URL'] 				= "gene_expressions/app_correlation_genes_samples.php?key={$urlKey}";
		$researchProjectAPI['Base64_Image_ID'] 	= '';
		$researchProjectAPI['Parameters'] 		= $urlKey;
		include('app_research_project_api_modal.php');
			
		unset($researchProjectAPI);
	}
	
	$geneCandidates = array_clean(array_merge(array_values($results['Gene_Source']), array_values($results['Gene_Target'])));

	unset($dataArray);
	$dataArray['GeneNames'] = $geneCandidates;
	$dataArray['SampleIDs'] = array_values($results['Summary']['SampleIndex']);
	$dataArray['data_source'] = $_POST['data_source'];
	$dataArray['data_source_private_project_indexes'] = $_POST['data_source_private_project_indexes'];
	$exportURLKey 			= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	$URLs[]					= "<a href='app_heatmap.php?key={$exportURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-chart-bar') . "Heatmap</a>";
	$URLs[]					= "<a href='app_export_genes_samples.php?key={$exportURLKey}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . "{$APP_MESSAGE['Export Genes and Samples']}</a>";
	
	
	unset($dataArray);
	$dataArray['Input'] = $geneCandidates;
	$count				= array_size($geneCandidates);
	$newListURLKey		= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	$URLs[]				= "<a href='app_list_new.php?Category=Gene&key={$newListURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-file') . "Create New Gene List ({$count})</a>";
	
	
	unset($dataArray);
	$dataArray['Input'] = array_values($results['Summary']['SampleIndex']);
	$count				= array_size($results['Summary']['SampleIndex']);
	$newListURLKey		= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	$URLs[]				= "<a href='app_list_new.php?Category=Sample&key={$newListURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-file') . "Create New Sample List ({$count})</a>";
	
	
	echo "<p>" . implode(" &nbsp;&bull;&nbsp; ", $URLs) . "</p>";
	echo "<br/>";
}


if (true){
	
	echo "<div class='row'>";

		echo "<div class='col-12'>";

			echo "<table id='resultTable' class='table table-sm table-striped w-100'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th class='text-left text-nowrap'>Source {$APP_MESSAGE['Gene']}</th>";
						echo "<th class='text-left text-nowrap'>Matched {$APP_MESSAGE['Gene']}</th>";
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
startTimer('exe::postProcessing');

if (isDebugMode()){
	echo "<hr/>";
	echo processTimer($BXAPP_TIMER, 1);
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
		var URL = "app_correlation_genes_samples.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "Correlation Tools Using <?php echo $APP_MESSAGE['Gene']; ?> Expression", URL);
	}
	<?php } ?>
	
	
	$('#resultTable').DataTable({
        "processing": 	true,
        "serverSide": 	true,
		"scrollX": 		true,
		"ajax": {
				"url": "app_correlation_genes_samples_table_ajax.php?key=<?php echo $key; ?>",
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