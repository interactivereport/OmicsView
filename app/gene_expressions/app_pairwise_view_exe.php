<?php

include_once('config_init.php');

$_POST['data_source'] = array('public', 'private');

$_POST['data_source_private_project_indexes'] = array_keys(internal_data_get_accessible_project());

if ($_POST['page_width'] <= 0){
	$_POST['page_width'] = 1000;	
}

if ($_POST['page_height'] <= 0){
	$_POST['page_height'] = 1000;	
}


if ($_POST['color_by'] == 'sample'){
	unset($_POST['color_by_gene']);
} elseif ($_POST['color_by'] == 'gene'){
	unset($_POST['color_by_sample']);
} else {
	unset($_POST['color_by_gene']);
	unset($_POST['color_by_sample']);
} 


//Check Sample IDs
if (true){
	
	if ($_POST['source'] == 'list'){
		$_POST['samples'] = splitData($_POST['samples']);	
	
		if (array_size($_POST['samples']) <= 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a sample ID.";
			echo getAlerts($message, 'danger');
			exit();
		}
		
		$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($_POST['samples'], '', $_POST['data_source_private_project_indexes']);
	}
	
	
	if ($_POST['source'] == 'filter'){
		
		$SQL_CONDITIONS = array();
		
		foreach($APP_CONFIG['DB_Dictionary']['Samples']['Filter'] as $tempKey => $tempValue){
			
			$currentColumn = $tempValue['SQL'];
			
			if (array_size($_POST["filter_{$currentColumn}"]) > 0){
				
				foreach($_POST["filter_{$currentColumn}"] as $tempKeyX => $tempValueX){
					
					if ($tempValueX != ''){
						$SQL_CONDITIONS[$currentColumn][] = "'" . addslashes(trim($tempValueX)) . "'";
					}
				}
				
				$SQL_CONDITIONS[$currentColumn] = "(`{$currentColumn}` IN (" .  implode(', ', $SQL_CONDITIONS[$currentColumn]) . "))";
			}
		}
		
		
		if (array_size($SQL_CONDITIONS) <= 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a value in the sample filter.";
			echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
			exit();	
		} else {
			$SQL_CONDITIONS = implode(' AND ', $SQL_CONDITIONS);
			
			$_POST['samples'] = search_all_records('Samples', 'SampleID', $SQL_CONDITIONS, 'GetCol');
			
			
			$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($_POST['samples'], '', $_POST['data_source_private_project_indexes']);
			unset($_POST['samples']);
				
		}
	}
	
		
	if ($getSampleIDsExistenceInfo == false){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There is no sample IDs matched your search condition.";
		echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
		exit();	
	}
	
	
}

//Check x axis gene
if ($_POST['x_axis'] == 'gene'){
	
	if ($_POST['x_axis_gene'] == ''){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " <strong>Horizontal Axis:</strong> The {$APP_MESSAGE['gene']} cannot be empty.";
		echo getAlerts($message, 'danger');
		exit();
	}
	
	$getGeneNameExistenceInfoX = getGeneNameExistenceInfo($_POST['x_axis_gene']);
	if ($getGeneNameExistenceInfoX == false){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . 
					" <strong>Horizontal Axis:</strong> The {$APP_MESSAGE['gene']} (<strong>{$_POST['x_axis_gene']}</strong>) does not exist in the database.";
		echo getAlerts($message);
		exit();
	}
	
	$_POST['x_axis_sample_attribute'] = '';
}

//Check y axis gene
if ($_POST['y_axis'] == 'gene'){
	if ($_POST['y_axis_gene'] == ''){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " <strong>Vertical Axis:</strong> The {$APP_MESSAGE['gene']} cannot be empty.";
		echo getAlerts($message, 'danger');
		exit();
	}
	
	$getGeneNameExistenceInfoY = getGeneNameExistenceInfo($_POST['y_axis_gene']);
	if ($getGeneNameExistenceInfoY == false){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . 
					" <strong>Vertical Axis:</strong> The {$APP_MESSAGE['gene']} (<strong>{$_POST['y_axis_gene']}</strong>) does not exist in the database.";
		echo getAlerts($message);
		exit();
	}
	
	$_POST['y_axis_sample_attribute'] = '';
}

//Check color gene
if ($_POST['color_by'] == 'gene'){
	if ($_POST['color_by_gene'] == ''){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " <strong>Color Data Point By {$APP_MESSAGE['Gene']}:</strong> The {$APP_MESSAGE['gene']} cannot be empty.";
		echo getAlerts($message, 'danger');
		exit();
	}
	
	$getGeneNameExistenceInfoColor = getGeneNameExistenceInfo($_POST['color_by_gene']);
	if ($getGeneNameExistenceInfoColor == false){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . 
					" <strong>Color Data Point By {$APP_MESSAGE['Gene']}:</strong> The {$APP_MESSAGE['gene']} (<strong>{$_POST['color_by_gene']}</strong>) does not exist in the database.";
		echo getAlerts($message);
		exit();
	}
}

//Check Sample IDs
if (true){
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
	
	
	if (!(($_POST['x_axis'] == 'samples') && ($_POST['y_axis'] == 'samples'))){
		if (array_size($getSampleIDsExistenceInfo['platformSummary']) > 1){
			
			$platformHasError 		= 1;
			$needExit				= 1;
			$platformErrorMessage 	= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " All samples should belong to the same platform type (RNA-Seq or Microarray).</p>";
			echo $platformErrorMessage;
			
		} elseif (array_size($getSampleIDsExistenceInfo['platformSummary']['Microarray']) > 1){
			$platformHasError 		= 1;
			$platformErrorMessage 	= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " You samples come from different microarray platforms. We recommend plotting expression data from one array platform at a time to minimize systematic differences.</p>";
			
			echo $platformErrorMessage;
		}
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
	
		if ($needExit) exit();
		
	}
}


//**************************************************************


$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);

$x_numeric 			= $y_numeric 		= false;
$x_numeric_data 	= $y_numeric_data	= array();
$x_category_data 	= $y_category_data 	= array();

//Sample vs Sample
if (($_POST['y_axis'] == 'samples') && ($_POST['x_axis'] == 'samples')){
	
	if (!$_POST['x_axis_sample_numeric'] && !$_POST['y_axis_sample_numeric']){
		$x_numeric 			= false;
		$y_numeric 			= false;
		$x_category_data 	= prepareCategoryPairwiseDataFromSample($getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['x_axis_sample_attribute'],
														$_POST['y_axis_sample_attribute'],
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
		$y_category_data	= $x_category_data;
		
	} else {
		
		
		
		if ($_POST['x_axis_sample_numeric']){
			$x_numeric 			= true;
			$x_numeric_data 	= prepareNumericPairwiseDataFromSample(
														$getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['x_axis_sample_attribute'],
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
		} else {
			$x_numeric 			= false;
			$x_category_data 	= prepareCategoryPairwiseDataFromSample($getSampleIDsExistenceInfo['SampleIndexes'], 
															$_POST['x_axis_sample_attribute'],
															'',
															$_POST['data_source'], 
															$_POST['data_source_private_project_indexes']);
		}
	
		if ($_POST['y_axis_sample_numeric']){
			$y_numeric 			= true;
			$y_numeric_data		= prepareNumericPairwiseDataFromSample(
														$getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['y_axis_sample_attribute'],
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
		} else {
			$y_numeric 			= false;
			$y_category_data 	= prepareCategoryPairwiseDataFromSample($getSampleIDsExistenceInfo['SampleIndexes'], 
															$_POST['y_axis_sample_attribute'],
															'',
															$_POST['data_source'], 
															$_POST['data_source_private_project_indexes']);
		}
	}
	
} elseif (($_POST['y_axis'] == 'samples') && ($_POST['x_axis'] == 'gene')){
	
	if ($_POST['y_axis_sample_numeric']){
		$y_numeric 			= true;
		$y_numeric_data		= prepareNumericPairwiseDataFromSample(
														$getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['y_axis_sample_attribute'],
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
	} else {
		$y_numeric 			= false;
		$y_category_data 	= prepareCategoryPairwiseDataFromSample($getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['y_axis_sample_attribute'],
														'',
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
	}
			  
	if (true){
		$x_numeric 		= true;
		$x_numeric_data = prepareNumericPairwiseDataFromGeneExpression(
									$getSampleIDsExistenceInfo['SampleIndexes'], 
									$getGeneNameExistenceInfoX['GeneIndexes'][0],
									$getGeneNameExistenceInfoX['GeneIndexes::GeneName'][$getGeneNameExistenceInfoX['GeneIndexes'][0]], 
									$getSampleIDsExistenceInfo['platformType'], 
									$_POST['data_source'], 
									$_POST['data_source_private_project_indexes']);
	}
	
} elseif (($_POST['x_axis'] == 'samples') && ($_POST['y_axis'] == 'gene')){
	
	if ($_POST['x_axis_sample_numeric']){
		$x_numeric 			= true;
		$x_numeric_data		= prepareNumericPairwiseDataFromSample(
														$getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['x_axis_sample_attribute'],
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
		
	} else {
		$x_numeric 			= false;
		$x_category_data 	= prepareCategoryPairwiseDataFromSample($getSampleIDsExistenceInfo['SampleIndexes'], 
														$_POST['x_axis_sample_attribute'],
														'',
														$_POST['data_source'], 
														$_POST['data_source_private_project_indexes']);
	}
			  
	if (true){
		$y_numeric 		= true;
		$y_numeric_data = prepareNumericPairwiseDataFromGeneExpression(
									$getSampleIDsExistenceInfo['SampleIndexes'], 
									$getGeneNameExistenceInfoY['GeneIndexes'][0],
									$getGeneNameExistenceInfoY['GeneIndexes::GeneName'][$getGeneNameExistenceInfoY['GeneIndexes'][0]], 
									$getSampleIDsExistenceInfo['platformType'], 
									$_POST['data_source'], 
									$_POST['data_source_private_project_indexes']);
	}
	
} elseif (($_POST['y_axis'] == 'gene') && ($_POST['x_axis'] == 'gene')){
	
	if (true){
		$x_numeric 		= true;
		$x_numeric_data = prepareNumericPairwiseDataFromGeneExpression(
									$getSampleIDsExistenceInfo['SampleIndexes'], 
									$getGeneNameExistenceInfoX['GeneIndexes'][0],
									$getGeneNameExistenceInfoX['GeneIndexes::GeneName'][$getGeneNameExistenceInfoX['GeneIndexes'][0]], 
									$getSampleIDsExistenceInfo['platformType'], 
									$_POST['data_source'], 
									$_POST['data_source_private_project_indexes']);
	}
	
	if (true){
		$y_numeric 		= true;
		$y_numeric_data = prepareNumericPairwiseDataFromGeneExpression(
									$getSampleIDsExistenceInfo['SampleIndexes'], 
									$getGeneNameExistenceInfoY['GeneIndexes'][0],
									$getGeneNameExistenceInfoY['GeneIndexes::GeneName'][$getGeneNameExistenceInfoY['GeneIndexes'][0]], 
									$getSampleIDsExistenceInfo['platformType'], 
									$_POST['data_source'], 
									$_POST['data_source_private_project_indexes']);
	}
	
}


echo "<br/>";

if (!(!$x_numeric && !$y_numeric)){
	
	if ($_POST['color_by'] == 'sample'){
		
		$color_data = prepareColorPairwiseDataFromSample(
									$getSampleIDsExistenceInfo['SampleIndexes'], 
									$_POST['color_by_sample'],
									$_POST['data_source'], 
									$_POST['data_source_private_project_indexes']);
	} elseif ($_POST['color_by'] == 'gene'){
		$color_data = prepareColorPairwiseDataFromGeneExpression(
									$getSampleIDsExistenceInfo['SampleIndexes'], 
									$getGeneNameExistenceInfoColor['GeneIndexes'][0],
									$getGeneNameExistenceInfoColor['GeneIndexes::GeneName'][$getGeneNameExistenceInfoColor['GeneIndexes'][0]], 
									$getSampleIDsExistenceInfo['platformType'], 
									$_POST['data_source'], 
									$_POST['data_source_private_project_indexes']);

	} else {
		$color_data = array();	
	}
	
}




if ($x_numeric && $y_numeric){
	//Scatter Plot
	include('app_pairwise_view_exe-numeric_vs_numeric.php');
} elseif (!$x_numeric && !$y_numeric){
	//Table
	include('app_pairwise_view_exe-category_vs_category.php');
} else {
	
	if (($_POST['x_axis_sample_attribute'] == 'SampleID') || ($_POST['y_axis_sample_attribute'] == 'SampleID')){
		//Bar Chart
		include('app_pairwise_view_exe-numeric_vs_sampleID.php');
	} else {
		//Box Plot
		include('app_pairwise_view_exe-numeric_vs_category.php');
	}
	
}



?>


<script type="text/javascript">

$(document).ready(function(){
	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_pairwise_view.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "Pairwise View", URL);
	}
	<?php } ?>

});

</script>
