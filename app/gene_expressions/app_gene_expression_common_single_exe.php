<?php
include_once('config_init.php');

if (!$_POST['API']){
	echo "<br/>";
	echo "<hr/>";
}


$geneName 				= strtoupper(trim($_POST['GeneName']));


$plotColumns 			= array_clean($_POST['Plot_Columns']);


if (($_POST['groupSamples'] != '') && ($_POST['groupSamples'] != -1)){
	if (!in_array($_POST['groupSamples'], $plotColumns)){
		$plotColumns[] = $_POST['groupSamples'];
	}
}


if (($_POST['colorBy'] != '') && ($_POST['colorBy'] != -1)){
	if (!in_array($_POST['colorBy'], $plotColumns)){
		$plotColumns[] = $_POST['colorBy'];
	}
}


if (($_POST['shapeBy'] != '') && ($_POST['shapeBy'] != -1)){
	if (!in_array($_POST['shapeBy'], $plotColumns)){
		$plotColumns[] = $_POST['shapeBy'];
	}
}

if (($_POST['segregate'] != '') && ($_POST['segregate'] != -1)){
	if (!in_array($_POST['segregate'], $plotColumns)){
		$plotColumns[] = $_POST['segregate'];
	}
}

if (($_POST['sortBy'] != '') && ($_POST['sortBy'] != -1)){
	if (!in_array($_POST['sortBy'], $plotColumns)){
		$plotColumns[] = $_POST['sortBy'];
	}
}

$plotColumnsComparison	= array_clean($_POST['Plot_Columns_Comparison']);
$_POST['ComparisonIDs'] = splitData($_POST['ComparisonIDs']);
$_POST['SampleIDs'] 	= splitData($_POST['SampleIDs']);
$_POST['ProjectIDs'] 	= splitData($_POST['ProjectIDs']);
$_POST['searchOption'] 	= abs(intval($_POST['searchOption']));
if ($_POST['searchOption'] > 3){
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


cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);
if (($_POST['data_source']['private'] != '') && ($_POST['searchOption'] == 1)){
	//$_POST['searchOption'] = 0;	
}



if ($geneName == ''){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['Please enter at least a gene name and try again.']}";
	echo getAlerts($message, 'danger');
	exit();
}


if ($_POST['searchOption'] == 3){
	//Project
	if (array_size($plotColumns) <= 0){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a sample attribute first.";
		echo getAlerts($message, 'danger');
		exit();
	}
	
	if (array_size($_POST['ProjectIDs']) <= 0){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter one or more project ID.";
		echo getAlerts($message, 'danger');
		exit();
	}
	
	//Project
	foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'] as $currentColumn => $dropDownInfo){
		unset($_POST[$currentColumn]);
	}
	unset($_POST['ComparisonIDs'], $_POST['Plot_Columns_Comparison'], $_POST['SampleIDs']);
	
	$getProjectIDsExistenceInfo = getProjectIDsExistenceInfo($_POST['ProjectIDs'], array_keys(internal_data_get_accessible_project()));
	
	if ($getProjectIDsExistenceInfo['hasMissing']){
		
	
		$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the projects you entered do not exist in the database. Please click <a href='javascript:void(0);' id='projectMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
		
		echo $message;

		echo "<div id='projectMissingInfo' class='startHidden'>";
		
			unset($tableContent);
			$tableContent['Header'][1]		= 'Category';
			$tableContent['Header'][2] 		= '# of Projects';
			
			
			$tableContent['Body'][1]['Value'][1]	= '# of Projects Entered';
			$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#projectExistenceInfoSection'>{$getProjectIDsExistenceInfo['Input_Count']}</a>";
			
			$tableContent['Body'][2]['Value'][1]	= '# of Projects Available';
			$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#projectExistenceInfoSection'>{$getProjectIDsExistenceInfo['Output_Count']}</a>";
			
			$tableContent['Body'][3]['Value'][1]	= '# of Projects Not Available';
			$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#projectExistenceInfoSection'>{$getProjectIDsExistenceInfo['Missing_Count']}</a>";
			
			echo printTableHTML($tableContent, 1, 1, 0);
			
			
			$modalID 	= 'projectExistenceInfoSection';
			$modalTitle = "<h4 class='modal-title'>Summary</h4>";
			$modalBody  = "<div class='row'>";
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>Projects Entered ({$getProjectIDsExistenceInfo['Input_Count']}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getProjectIDsExistenceInfo['Input']) . "</textarea>";
				$modalBody  .= "</div>";
								
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>Projects Available ({$getProjectIDsExistenceInfo['Output_Count']}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getProjectIDsExistenceInfo['Output']) . "</textarea>";
				$modalBody  .= "</div>";
								
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>Projects Not Available ({$getProjectIDsExistenceInfo['Missing_Count']}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getProjectIDsExistenceInfo['Missing']) . "</textarea>";
				$modalBody  .= "</div>";
			$modalBody  .= "</div>";
			
			echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
		
		
		echo "</div>";
	
	}
	
	
	if ($getProjectIDsExistenceInfo['Output_Count'] > 0){
		$sampleIDs = getSampleIDFromProjectIndexes($getProjectIDsExistenceInfo['Index-ID']);
	}
	
	
	
	$_POST_ORG['searchOption'] 	= $_POST['searchOption'];
	$_POST_ORG['ProjectIDs'] 	= $_POST['ProjectIDs'];
	$_POST_ORG['SampleIDs'] 	= '';

	$_POST['searchOption'] 		= 2;	
	$_POST['SampleIDs'] 		= $sampleIDs;

}



if (true){
	if ($_POST['searchOption'] == 0){
		//Default
		if (array_size($plotColumns) <= 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a sample attribute first.";
			echo getAlerts($message, 'danger');
			exit();
		}
	} else if ($_POST['searchOption'] == 1){
		//Comparison
		if ((array_size($plotColumnsComparison) <= 0) && (array_size($plotColumns) <= 0)){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a sample or comparison attribute first.";
			echo getAlerts($message, 'danger');
			exit();
		}
		
		if (array_size($_POST['ComparisonIDs']) <= 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter one or more comparison ID.";
			echo getAlerts($message, 'danger');
			exit();
		}	
	} else if ($_POST['searchOption'] == 2){
		//Sample
		if (array_size($plotColumns) <= 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least a sample attribute first.";
			echo getAlerts($message, 'danger');
			exit();
		}
		
		if (array_size($_POST['SampleIDs']) <= 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter one or more sample ID.";
			echo getAlerts($message, 'danger');
			exit();
		}	
	}
}


$columns = $APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'];



if (true){
	if ($_POST['searchOption'] == 0){
		//Data Filter
		unset($dataFilter);
		
		foreach($columns as $currentColumn => $dropDownInfo){
			if (array_size($_POST[$currentColumn]) > 0){
				$dataFilter[$currentColumn] = $_POST[$currentColumn];
			}
		}
		unset($_POST['ComparisonIDs'], $_POST['SampleIDs'], $_POST['Plot_Columns_Comparison']);
		
		$results = prepareGeneExpressionDataByGeneName($geneName, $plotColumns, $geneValueTable, $geneValueColumn, $_POST['searchOption'], $dataFilter, '', $_POST['data_source'], $_POST['data_source_private_project_indexes'], $_POST);
	} elseif ($_POST['searchOption'] == 1){
		
		//Comparison IDs
		foreach($columns as $currentColumn => $dropDownInfo){
			unset($_POST[$currentColumn]);
		}
		unset($_POST['SampleIDs']);
		
		$needPlatformCheck = 1;
		$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($_POST['ComparisonIDs'], $toolPlatformType, $_POST['data_source_private_project_indexes'], 0);
		
		if ($getComparisonIDsExistenceInfo == false){

			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The comparison IDs you entered do not exist in the database.";
			echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
			exit();
			
		} else {
			
		}
		
		
		
		$results = prepareGeneExpressionDataByGeneName($geneName, $plotColumns, $geneValueTable, $geneValueColumn, $_POST['searchOption'], $_POST['ComparisonIDs'], $plotColumnsComparison, $_POST['data_source'], $_POST['data_source_private_project_indexes'], $_POST);
	} elseif ($_POST['searchOption'] == 2){
		
		//Sample IDs
		
		foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'] as $currentColumn => $dropDownInfo){
			unset($_POST[$currentColumn]);
		}
		unset($_POST['ComparisonIDs'], $_POST['Plot_Columns_Comparison']);
	
		$needPlatformCheck = 1;	
		$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($_POST['SampleIDs'], $toolPlatformType, $_POST['data_source_private_project_indexes']);
		
		if ($getSampleIDsExistenceInfo == false){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The sample IDs you entered do not exist in the database.";
			echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
			exit();	
		}
		
		$results = prepareGeneExpressionDataByGeneName($geneName, $plotColumns, $geneValueTable, $geneValueColumn, $_POST['searchOption'], $_POST['SampleIDs'], '', $_POST['data_source'], $_POST['data_source_private_project_indexes'], $_POST);
	}
}

if ($needPlatformCheck){
	if ($_POST['searchOption'] == 1){
		$platformHasError 		= $getComparisonIDsExistenceInfo['missPreferredPlatform'];
		
		if ($platformHasError){
			
			$platformErrorMessage 	= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please make sure that the platform type of the comparison IDs is <strong>{$toolPlatformType}</strong>.</p>";
			echo $platformErrorMessage;
	
		
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
						$modalBody  .= "<div class='col-lg-10 col-sm-12'>";
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
			
			exit();
			
		}
		
	} elseif ($_POST['searchOption'] == 2){
		$platformHasError 		= $getSampleIDsExistenceInfo['missPreferredPlatform'];
		
		
		if ($platformHasError){
			
			$platformErrorMessage 	= "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please make sure that the platform type of the sample IDs is <strong>{$toolPlatformType}</strong>.</p>";
			echo $platformErrorMessage;
	
		
			unset($tableContent);
			$tableContent['Header'][1]		= 'Platform Type';
			$tableContent['Header'][2]		= 'Platform Name';
			$tableContent['Header'][3] 		= '# of Sample IDs';
			
			unset($currentRow);
			foreach($getSampleIDsExistenceInfo['platformSummary'] as $platformType => $tempValue1){
				
				foreach($tempValue1 as $platformName => $tempValue2){
					$currentRow++;
				
					$count = array_size($tempValue2);
					
					$modalID 	= 'comparisonIDPlatform_' . md5($platformName);
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
			
			exit();
			
		}
		
	}
	
}


if (($results['DataCount'] > $APP_CONFIG['canvasxpress']['Data_Limit'])){
	$tooManyDataPoints = 1;	
}

if (array_size($dataFilter) > 0){
	$dataFilterSummary = "<ol>";
		foreach($dataFilter as $currentColumn => $columnCategories){
			
			$currentColumnTitle = $flexibleColumnSummary['By-Column'][$currentColumn]['Title'];
			
			if ($currentColumnTitle == ''){
				$currentColumnTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
			}
			
			foreach($columnCategories as $tempKey => $tempValue){
				
				if ($tempValue == ''){
					$columnCategories[$tempKey] = $APP_CONFIG['Blank_Value'];
				}
			}
			
			$dataFilterSummary .= "<li>{$currentColumnTitle} is: <mark>" . implode("</mark> or <mark>", $columnCategories) . "</li>";
		}
	$dataFilterSummary .= "</ol>";
}



if ($results['DataCount'] <= 0){
	$message = "<div><strong>Warning!</strong> There are no records available. Please modify your data filter and try again.</div>";
	
	if (array_size($dataFilter) > 0){
		$message .= "<p>The search is based on matching all of the following conditions:</p>";
		$message .= "<div>{$dataFilterSummary}</div>";
	}
	echo getAlerts($message, 'warning', 'col-lg-10 col-sm-12');
	exit();	
}

$geneIndex = search_gene_index($geneName);
$iTargetURL = get_iTarget_URL($geneIndex);


if ($_POST['API'] || $iTargetURL == ''){
	include('app_gene_expression_common_single_exe_tab_chart.php');
} else {

	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Summary' role='tab' data-toggle='tab'>Plot</a>
				  </li>";
	
				  
			if ($iTargetURL != ''){
				$title = 'iTarget Baseline Expression';
				echo "<li class='nav-item'>
						<a class='nav-link' href='#iTarget' role='tab' data-toggle='tab'>{$title}</a>
					  </li>";
			}
		echo "</ul>";
	
				  
		echo "<div class='tab-content'>";
			echo "<div role='tabpanel' id='Summary' class='tab-pane fade in active show'>";
				echo "<br/>";
				include('app_gene_expression_common_single_exe_tab_chart.php');
			echo "</div>";
	
			
			if ($iTargetURL != ''){
				echo "<div role='tabpanel' id='iTarget' class='tab-pane fade in'>";
					
					echo "<br/>";
					echo "<p><a href='{$iTargetURL}' target='_blank'>" .  printFontAwesomeIcon('fas fa-external-link-alt') . " Open in iTarget</a></p>";
					echo "<br/>";
					
					echo "<div class='embed-responsive embed-responsive-1by1'>";
						echo "<iframe class='embed-responsive-item' src='{$iTargetURL}' allowfullscreen></iframe>";
					echo "</div>";
					
					
				echo "</div>";
			}
			
			
			
		echo "</div>";
		
	echo "</div>";
}


?>