<?php
include_once('config_init.php');

?>
<script type="text/javascript">

global_ajax_form_counter_server = <?php echo $_POST['global_counter']; ?>;

</script>
<?php

echo "<hr/>";


//echo printMsg("Submission ID: {$_POST['global_counter']}");

$_POST['GeneNames'] 	= splitData($_POST['GeneNames']);
$_POST['SampleIDs'] 	= splitData($_POST['SampleIDs']);
$_POST['ComparisonIDs'] = splitData($_POST['ComparisonIDs']);
$plotColumns 			= array_clean($_POST['Plot_Columns']);

$_POST['searchOption'] 	= abs(intval($_POST['searchOption']));
if ($_POST['searchOption'] > 1){
	$_POST['searchOption'] = 0;	
}


if ($APP_CONFIG['APP']['Module']['Modified_DiseaseState']){
	if (!in_array('DiseaseState', $plotColumns)){
		unset($_POST['Modified_DiseaseState_Enable'], $_POST['Modified_DiseaseState_ComparisonCategory']);
	} else {
		
		if ($_POST['Modified_DiseaseState_Enable']){
			if (array_size($_POST['Modified_DiseaseState_ComparisonCategory']) <= 0){
				$_POST['Modified_DiseaseState_ComparisonCategory'] = $APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory'];
			}
		} else {
			unset($_POST['Modified_DiseaseState_Enable'], $_POST['Modified_DiseaseState_ComparisonCategory']);
		}
	}
} else {
	unset($_POST['Modified_DiseaseState_Enable'], $_POST['Modified_DiseaseState_ComparisonCategory']);
}

if (!isset($_POST['transform'])){
	$_POST['transform'] = 0;
}

if (!isset($_POST['zscore'])){
	$_POST['zscore'] = 0;
}

if (!isset($_POST['sampleOverLay'])){
	$_POST['sampleOverLay'] = 0;
}

if (!isset($_POST['upper_limit_enable'])){
	$_POST['upper_limit_enable'] = 0;
}

if (!isset($_POST['lower_limit_enable'])){
	$_POST['lower_limit_enable'] = 0;
}

if (!isset($_POST['variablesClustered'])){
	$_POST['variablesClustered'] = 0;
	$variablesClustered = 'false';
} else {
	$variablesClustered = 'true';	
}

if (!isset($_POST['samplesClustered'])){
	$_POST['samplesClustered'] = 0;
	$samplesClustered = 'false';
} else {
	$samplesClustered = 'true';	
}

if (!isset($_POST['showSampleNames'])){
	$_POST['showSampleNames'] = 0;
	$showSampleNames = 'false';
} else {
	$showSampleNames = 'true';	
}

if (!isset($_POST['showVariableNames'])){
	$_POST['showVariableNames'] = 0;
	$showVariableNames = 'false';
} else {
	$showVariableNames = 'true';	
}


$_POST['transform_value'] 	= abs(floatval($_POST['transform_value']));

if ($_POST['upper_limit_enable']){
	$_POST['upper_limit_value'] = floatval($_POST['upper_limit_value']);
	$_POST['limit_enable'] = 1;
} else {
	unset($_POST['upper_limit_value']);
}

if ($_POST['lower_limit_enable']){
	$_POST['lower_limit_value'] = floatval($_POST['lower_limit_value']);
	$_POST['limit_enable'] = 1;
} else {
	unset($_POST['lower_limit_value']);
}


if (array_size($_POST['GeneNames']) < 2){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least two {$APP_MESSAGE['gene']} names.";
	echo getAlerts($message, 'danger');
	
	exit();
}


if ($_POST['searchOption'] == 0){
	if (array_size($_POST['SampleIDs']) < 2){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least two sample IDs.";
		echo getAlerts($message, 'danger');
		
		exit();
	}
} elseif ($_POST['searchOption'] == 1){
	if (array_size($_POST['ComparisonIDs']) < 1){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least one comparison ID.";
		echo getAlerts($message, 'danger');
		
		exit();
	}
}


if ($_POST['lower_limit_enable'] && $_POST['upper_limit_enable']){
	if ($_POST['upper_limit_value'] < $_POST['lower_limit_value']){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The upper limit ({$_POST['upper_limit_value']}) cannot be smaller than the lower limit ({$_POST['lower_limit_value']}).";
		echo getAlerts($message, 'danger');
		
		exit();
	}
}

if (array_size($plotColumns) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a sample attribute first.";
	echo getAlerts($message, 'danger');
	
	exit();
}

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

if (($_POST['data_source']['private'] != '') && ($_POST['searchOption'] == 2)){
	$_POST['searchOption'] = 0;	
}


$getGeneNameExistenceInfo = getGeneNameExistenceInfo($_POST['GeneNames']);

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



if ($_POST['searchOption'] == 0){
	//Search By Sample IDs
	unset($_POST['ComparisonIDs'], $_POST['Plot_Columns_Comparison']);
		
	$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($_POST['SampleIDs'], '', $_POST['data_source_private_project_indexes']);
	
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
		$platformErrorMessage = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " You samples come from different microarray platforms. We recommend plotting expression data from one array platform at a time to minimize systematic differences.</p>";
		
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
					$modalBody  .= "<div class='col-lg-10 col-sm-12'>";
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
	
		if ($needExit){
			
			exit();
		}
		
	}
	
	
	
	$geneIndexes 	= $getGeneNameExistenceInfo['GeneIndexes'];
	$sampleIndexes 	= $getSampleIDsExistenceInfo['SampleIndexes'];

	$results = prepareHeatMapData($geneIndexes, $sampleIndexes, $plotColumns, $getSampleIDsExistenceInfo['platformType'], $_POST, $_POST['data_source'], $_POST['data_source_private_project_indexes']);

} elseif ($_POST['searchOption'] == 1){
	
	//Search By Comparison IDs
	unset($_POST['SampleIDs']);
	

	
	$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($_POST['ComparisonIDs']);
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
					$modalBody  .= "<div class='text-nowrap'><strong>Comparison IDs Entered ({$getComparisonIDsExistenceInfo['Input_Count']}):</strong></div>";
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
	
	
	unset($platformHasError, $platformErrorMessage);
	
	if (array_size($getComparisonIDsExistenceInfo['platformSummary']) > 1){
		
		$platformHasError 		= 1;
		$needExit				= 1;
		$platformErrorMessage 	= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " All comparisons should belong to the same platform type (RNA-Seq or Microarray).</p>";
		echo $platformErrorMessage;
		
	} elseif (array_size($getComparisonIDsExistenceInfo['platformSummary']['Microarray']) > 1){
		$platformHasError = 1;
		$platformErrorMessage = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " You comparisons come from different microarray platforms. We recommend plotting expression data from one array platform at a time to minimize systematic differences.</p>";
		
		echo $platformErrorMessage;
	}
	

	if ($platformHasError){
	
		
		unset($tableContent);
		$tableContent['Header'][1]		= 'Platform Type';
		$tableContent['Header'][2]		= 'Platform Name';
		$tableContent['Header'][3] 		= '# of Comparison IDs';
		
		unset($currentRow);
		foreach($getComparisonIDsExistenceInfo['platformSummary'] as $platformType => $tempValue1){
			
			foreach($tempValue1 as $platformName => $tempValue2){
				$currentRow++;
			
				$count = array_size($tempValue2);
				
				$modalID 	= 'comparisonIDPlatform_' . md5($platformName);
				$modalTitle = "<h4 class='modal-title'>{$platformName} ({$platformType})</h4>";
				$modalBody  = "<div class='row'>";
					$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
						$modalBody  .= "<div class='text-nowrap'><strong>Platform Type:</strong> {$platformType}</div>";
						$modalBody  .= "<div class='text-nowrap'><strong>Platform Name:</strong> {$platformName}</div>";
						$modalBody  .= "<div class='text-nowrap'><strong>Comparison IDs ({$count}):</strong></div>";
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
	
		if ($needExit){
			
			exit();
		}
		
	}
	
	
	
	$geneIndexes 		= $getGeneNameExistenceInfo['GeneIndexes'];
	$comparisonIndexes 	= $getComparisonIDsExistenceInfo['ComparisonIndexes'];

	
	$results = prepareHeatMapData($geneIndexes, $comparisonIndexes, $plotColumns, $getComparisonIDsExistenceInfo['platformType'], $_POST);
}


if (!$results['Summary']['HasNumericValue']){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	
	exit();
	
}



$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);


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
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
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
		natcasesort($results['Summary']['SampleIndex']);
		$count 		= array_size($results['Summary']['SampleIndex']);
		$modalID 	= 'summarySample';
		$modalTitle = "<h4 class='modal-title'>Sample IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
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
	
	
	$rawDataKey 		= putSQLCacheWithoutKey($results['Export']['Raw'], '', 'prepareHeatMapData_Export', 1);
	$heatmapDataKey		= putSQLCacheWithoutKey($results['Export']['Transformed'], '', 'prepareHeatMapData_Export', 1);
	
	$message = "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ' . 
				"<a href='app_heatmap_download.php?key={$rawDataKey}&filename=raw_data.csv' target='_blank'>Raw Data</a>" . 
				" - " . 
				"<a href='app_heatmap_download.php?key={$heatmapDataKey}&filename=heatmap_data.csv' target='_blank'>Heatmap Data</a>" . 
				"</p>";
	
	echo $message;
	
	
	if (true){	
		unset($researchProjectAPI);
		$researchProjectAPI['Title'] 			= 'Heatmap';
		$researchProjectAPI['Type'] 			= 'Gene Expression Plot';
		$researchProjectAPI['Source_Page'] 		= 'Heatmap';
		$researchProjectAPI['URL'] 				= "gene_expressions/app_heatmap.php?key={$urlKey}";
		$researchProjectAPI['Base64_Image_ID'] 	= 'plotSection';
		$researchProjectAPI['Parameters'] 		= $urlKey;
		include('app_research_project_api_modal.php');
			
		unset($researchProjectAPI);
	}
	
	
	
}



if (true){
	
	echo "<hr/>";
	
	$class = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
	$height = 800;
	$width	= 1000;
	
	
	$geneCount 		= array_size($results['Summary']['Gene']);
	$sampleCount 	= array_size($results['Summary']['SampleIndex']);
	
	if ($sampleCount <= 25){
		$height = 900;
		$smpLabelScaleFontFactor = 0.55;
		$smpLabelInterval = 1;
	} elseif ($sampleCount <= 50){
		$height = 1000;
		$smpLabelScaleFontFactor = 0.55;
		$smpLabelInterval = 2;
	} elseif ($sampleCount <= 100){
		$height = 1200;
		$smpLabelScaleFontFactor = 0.45;
		$smpLabelInterval = 2;
	} elseif ($sampleCount <= 200){
		$height = 1400;
		$smpLabelScaleFontFactor = 0.35;
		$smpLabelInterval = 4;
	} elseif ($sampleCount <= 300){
		$height = 1600;
		$smpLabelScaleFontFactor = 0.25;
		$smpLabelInterval = 6;
	} else {
		$height = 1800;
		$smpLabelScaleFontFactor = 0.15;
		$smpLabelInterval = 8;
	}
	
	
	
	if ($geneCount <= 50){
		$width = 1000;	
		$varLabelScaleFontFactor = 2;
	} elseif ($geneCount <= 100){
		$width = 1200;	
		$varLabelScaleFontFactor = 1.5;
	} elseif ($geneCount <= 200){
		$width = 1400;	
		$varLabelScaleFontFactor = 1;
	} elseif ($geneCount <= 300){
		$width = 1600;	
		$varLabelScaleFontFactor = 1.5;
	} else {
		$width = 1800;
		$varLabelScaleFontFactor = 1.5;
	}
	
	
	/*
	$_POST['width'] = intval($_POST['width']);
	if ($_POST['width'] > 0){
		$max_width = $_POST['width'] - 100;
		if ($width < $max_width){
			if (($width/$max_width <= 0.75) && ($width/$max_width >= 0.5)){
				$width = $max_width;	
			}
		} else {
			$width = $max_width;	
		}
	}
	*/
	

	echo "<div class='{$class}'>";

		echo "<div style='height:{$height}px; width:{$width}px;'>";
			echo "<canvas id='plotSection' width='{$width}' height='{$height}' xresponsive='false' aspectRatio='1:1'></canvas>";
		echo "</div>";
		
	echo "</div>";
	

}





unset($canvasxpress);
foreach($results['canvasxpress']['x'] as $plotColumn => $plotColumnDetails){
	
	$plotColumnToDisplay = $flexibleColumnSummary['By-Column'][$plotColumn]['Title'];
	
	if ($plotColumnToDisplay == ''){
		if (in_array($plotColumn, $plotColumns)){
			$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$plotColumn]['Title'];
		} elseif (in_array($plotColumn, $_POST['Plot_Columns_Comparison'])){
			$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$plotColumn]['Title'];
		}
	}
	
	if ($plotColumnToDisplay == '') $plotColumnToDisplay = $plotColumn;
	
	$canvasxpress['x'][] = '"' . $plotColumnToDisplay . '": [' . implode(', ', $plotColumnDetails) . ']';
	
	if ($primaryColumn == ''){
		$primaryColumn = $plotColumnToDisplay;
	}
}

$canvasxpress['y-smps'] = '[' . implode(",\n", $results['canvasxpress']['y']['smps']) . ']';
$canvasxpress['y-vars'] = '[' . implode(",\n", $results['canvasxpress']['y']['vars']) . ']';

foreach($results['canvasxpress']['y']['data'] as $tempKey => $tempValue){
	$canvasxpress['y-data'][] = '[' . implode(", ", $tempValue) . ']';
}

$canvasxpress['y-data'] = '[' . implode(",\n", $canvasxpress['y-data']) . ']';



?>



<script type="text/javascript">

$(document).ready(function(){
	
	//global_ajax_form_counter_server = <?php echo $_POST['global_counter']; ?>;
	
	var plotObj = new CanvasXpress('plotSection',
		{
            "x" : {
				<?php echo implode(",\n", $canvasxpress['x']); ?>
            },
            "y" : {
              "vars" : <?php echo $canvasxpress['y-vars']; ?>,
              "smps" : <?php echo $canvasxpress['y-smps']; ?>,
              "data" : <?php echo $canvasxpress['y-data']; ?>
              
            }
		},
		  
		  
		{
			"colorSpectrum": ["blue", "white", "red"],
			
			"colorSpectrumZeroValue": 		0,
			"graphType": 					"Heatmap",
			"heatmapIndicatorHeight": 		50,
			"heatmapIndicatorHistogram": 	false,
			"heatmapIndicatorPosition": 	"topRight",
			"heatmapIndicatorWidth": 		60,
			"heatmapSmpSeparateBy": 		"Treatment",
			
			"samplesClustered": 			<?php echo $samplesClustered; ?>,
			"variablesClustered": 			<?php echo $variablesClustered; ?>,
						
			"title":						"<?php echo $APP_MESSAGE['Gene']; ?> Expression Levels",
			"subtitle":						"<?php echo $results['Summary']['Subtitle']; ?>",

			
			"legendBox":  					false,
			"showLegend": 					false,
			"showShadow": 					false,
			
			/*
			'axisTitleScaleFontFactor': 	0.5,
			'axisTickFontSize':				10,
			'axisTickScaleFontFactor': 		0.5,
			 
			'citation': 					'',
			'citationScaleFontFactor': 		0.7,

			 
			'xAxisTitle': 					'',	
			'titleFontSize':				25,
			
			
			
			
			'titleScaleFontFactor': 		0.7,
			'subtitleScaleFontFactor': 		0.7,

			'legendScaleFontFactor': 		0.6,
			 
			'nodeScaleFontFactor': 			0.7,
			 
			'sampleSeparationFactor': 		0.7,
			'variableSeparationFactor': 	0.7,
			'widthFactor': 					0.7,
			 
			'fontAttributeSize':			0.2,

			'varLabelScaleFontFactor':		<?php echo $varLabelScaleFontFactor; ?>,
			*/
			/*
			'marginTop':					25,		
			'marginLeft':					25,			
			'marginRight':					25,
			'marginBottom':					100,
			*/
			'marginTop':					25,		
			 'marginBottom':					150,
			'smpLabelScaleFontFactor': 		<?php echo $smpLabelScaleFontFactor; ?>,
			
			'varLabelScaleFontFactor':		<?php echo $varLabelScaleFontFactor; ?>,
			
			'showSampleNames':				<?php echo $showSampleNames; ?>,
			'showVariableNames':			<?php echo $showVariableNames; ?>,
			'printType':					'window',
			
			 <?php if ($smpLabelInterval > 0){ ?>
			 'smpLabelInterval': 	<?php echo $smpLabelInterval; ?>,
			 <?php } ?>
		}
	);
	
	
	<?php 
		if ($_POST['sampleOverLay']){
			
			foreach($plotColumns as $tempKey => $tempValue){
				
				$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$tempValue]['Title'];
				//echo "plotObj.showSampleOverlays('{$columnToPlotPrintable}');";
				echo "plotObj.modifySampleOverlays('{$columnToPlotPrintable}');";
			}
			
			
			foreach($_POST['Plot_Columns_Comparison'] as $tempKey => $tempValue){
				
				$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$tempValue]['Title'];
				echo "plotObj.modifySampleOverlays('{$columnToPlotPrintable}');";
			}
		}
		
	?>
	
	plotObj.sizes = plotObj.sizes.map(function(x){
			return Number(x * 0.5).toFixed(1);
		});
	CanvasXpress.stack["plotSection"]["config"]["sizes"] = plotObj.sizes.map(function(x) { return Number(x * 0.5).toFixed(1); });
	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_heatmap.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "Heatmap", URL);
	}
	<?php } ?>
	
	


});

</script>
