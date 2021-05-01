<?php

include_once('config_init.php');

echo "<hr/>";


$geneNames 			= splitData($_POST['GeneNames']);
$_POST['GeneNames'] = $geneNames;

$sampleIDs 			= splitData($_POST['SampleIDs']);
$_POST['SampleIDs'] = $sampleIDs;

$genePlotColumns 	= array_clean($_POST['Gene_Plot_Columns']);
$samplePlotColumns 	= array_clean($_POST['Sample_Plot_Columns']);

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

if (array_size($sampleIDs) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a sample ID.";
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


$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($sampleIDs, '', $_POST['data_source_private_project_indexes']);

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
	$platformErrorMessage = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " Your samples come from different microarray platforms.</p>";
	
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

$results = exportGeneSampleData($geneIndexes, $sampleIndexes, $genePlotColumns, $samplePlotColumns,  $getSampleIDsExistenceInfo['platformType'], $_POST['data_source'], $_POST['data_source_private_project_indexes']);




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
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
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
		
		natcasesort($results['Summary']['SampleIndex']);
		$count 		= array_size($results['Summary']['SampleIndex']);
		$modalID 	= 'summarySample';
		$modalTitle = "<h4 class='modal-title'>Sample IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-12 col-sm-12'>";
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
	if (isDebugMode()){
		echo "<p class='form-text'>Time Spent: " . round($endTime - $APP_CONFIG['StartTime'], 2) . " seconds.</p>";
	}
	
	echo "</div>";
}

if (true){
	
	$key 		= putSQLCacheWithoutKey($results, '', 'exportGeneSampleData', 1);
	
	$message	= '';
	if ($results['Summary']['Export_Limit']){
		$message .= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " {$BXAF_CONFIG['EXPORT_LIMIT_OPTIONS']['Gene_Sample_Message']}</p>";
	}
	$message 	.= "<p>" . printFontAwesomeIcon('fas fa-download') . "<strong>Download:</strong></p>";

	unset($URLs);
	foreach($results['Summary']['Value Names'] as $tempKey => $tempValue){
		
		
		if (($tempKey == 'Value') || ($tempKey == 'FPKM')){
			
			if (gene_uses_TPM()){
				$filename = "{$APP_MESSAGE['Genes']}_Samples_TPM.csv";
			} else {
				$filename = "{$APP_MESSAGE['Genes']}_Samples_Value.csv";
			}
		} else {
			$filename = "{$APP_MESSAGE['Genes']}_Samples_{$tempKey}.csv";	
		}
		
		$URLs[] = "<a href='app_export_genes_download.php?key={$key}&value={$tempKey}&filename={$filename}'>{$tempValue}</a>";
	}	
	$message 	.= "<p><strong>Matrix Format</strong>: " . implode('&nbsp; &bull; &nbsp;', $URLs) . "</p>";
	
	
	if (array_size($results['Summary']['Value Names']) == 1){
		$message 	.= "<p><strong>Table Format</strong>: <a href='app_export_genes_download_all.php?key={$key}&filename={$APP_MESSAGE['Genes']}_Samples_All.csv'>{$tempValue}</a></p>";	
	} else {
		$message 	.= "<p><strong>Table Format</strong>: <a href='app_export_genes_download_all.php?key={$key}&filename={$APP_MESSAGE['Genes']}_Samples_All.csv'>All Data Types</a></p>";	
	}
	

	if (!$results['Summary']['Export_Limit']){
		if ($_POST['data_source']['private'] == ''){
			$rawData = "<a href='app_export_genes_download.php?raw=1&key={$key}&filename={$APP_MESSAGE['Genes']}_Samples_Raw.txt'>Raw Data (Tabix Output)</a>";
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
		var URL = "app_export_genes_samples.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "<?php echo $APP_MESSAGE['Export Genes and Samples']; ?>", URL);
	}
	<?php } ?>

});

</script>
