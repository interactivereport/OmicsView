<?php
include_once('config_init.php');

echo "<br/>";
echo "<hr/>";

$geneNames 				= splitData($_POST['GeneNames']);
$plotColumns 			= array_clean($_POST['Plot_Columns']);
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
	$_POST['searchOption'] = 0;	
}


if (array_size($geneNames) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a {$APP_MESSAGE['gene']} name and try again.";
	echo getAlerts($message, 'danger');
	exit();
}


$getGeneNameExistenceInfo = getGeneNameExistenceInfo($geneNames);

if ($getGeneNameExistenceInfo == false){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['The genes you entered do not exist in the database.']}";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
} else {
	$geneNames = $getGeneNameExistenceInfo['GeneIndexes::GeneName'];
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



$columns = $APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'];




if ($_POST['searchOption'] == 0){
	//Data Filter
	unset($dataFilter);
	foreach($columns as $currentColumn => $dropDownInfo){
		if (array_size($_POST[$currentColumn]) > 0){
			$dataFilter[$currentColumn] = $_POST[$currentColumn];
		}
	}
	unset($_POST['ComparisonIDs'], $_POST['SampleIDs'], $_POST['Plot_Columns_Comparison']);
	
	$results = prepareGeneExpressionDataByGeneNames($geneNames, $plotColumns, $geneValueTable, $geneValueColumn, $_POST['searchOption'], $dataFilter, '', $_POST['data_source'], $_POST['data_source_private_project_indexes'], $_POST);
} elseif ($_POST['searchOption'] == 1){
	//Comparison
	foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'] as $currentColumn => $dropDownInfo){
		unset($_POST[$currentColumn]);
	}
	unset($_POST['SampleIDs'], $_POST['ProjectIDs']);
	
	$needPlatformCheck = 1;
	$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($_POST['ComparisonIDs'], $toolPlatformType);
	
	if ($getComparisonIDsExistenceInfo == false){
		
		$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($_POST['ComparisonIDs'], $toolPlatformType, $_POST['data_source_private_project_indexes'], 1);
		if (0 && $getComparisonIDsExistenceInfo['Private_Count'] > 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " This tool does not support searching by internal comparison data.";
			echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
			exit();
		} else {
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The comparison IDs you entered do not exist in the database.";
			echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
			exit();
		}
	}
	
	$results = prepareGeneExpressionDataByGeneNames($geneNames, $plotColumns, $geneValueTable, $geneValueColumn, $_POST['searchOption'], $_POST['ComparisonIDs'], $plotColumnsComparison, $_POST['data_source'], $_POST['data_source_private_project_indexes'], $_POST);
	
} elseif ($_POST['searchOption'] == 2){
	//Sample
	foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'] as $currentColumn => $dropDownInfo){
		unset($_POST[$currentColumn]);
	}
	unset($_POST['ComparisonIDs'], $_POST['Plot_Columns_Comparison'], $_POST['ProjectIDs']);
	
	$needPlatformCheck = 1;	
	$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($_POST['SampleIDs'], $toolPlatformType, array_keys(internal_data_get_accessible_project()));
	
	
	
	if ($getSampleIDsExistenceInfo == false){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The sample IDs you entered do not exist in the database.";
		echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
		exit();	
	}
	
	$results = prepareGeneExpressionDataByGeneNames($geneNames, $plotColumns, $geneValueTable, $geneValueColumn, $_POST['searchOption'], $_POST['SampleIDs'], '', $_POST['data_source'], $_POST['data_source_private_project_indexes'], $_POST);
}



if ($getGeneNameExistenceInfo['hasMissing']){
	
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['Some of the genes you entered do not exist in the database.']} Please click <a href='javascript:void(0);' id='geneMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	
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
		$modalTitle = "<h4 class='modal-title'>Summary</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Entered ({$getGeneNameExistenceInfo['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Available ({$getGeneNameExistenceInfo['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Not Available ({$getGeneNameExistenceInfo['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	
	echo "</div>";
	
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


//Search Summary
if (true){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h3>Search Summary</h3>";
			
			
			if ($_POST['searchOption'] == 0){
				
				$DataCount_WihoutConditions_Formatted = number_format($results['DataCount_WihoutConditions']);
				$DataCount_Formatted = number_format($results['DataCount']);
				
				if ($results['DataCount_WihoutConditions'] >= $results['DataCount']){
					if ($dataFilterSummary == ''){
						$message = "The search result contains {$DataCount_Formatted} out of {$DataCount_WihoutConditions_Formatted} data points.";
					} else {
						$message = "The search result contains {$DataCount_Formatted} out of {$DataCount_WihoutConditions_Formatted} data points, which matches all of the conditions below:";
					}
				} else {
					if ($dataFilterSummary == ''){
						$message = "The search result contains {$DataCount_WihoutConditions_Formatted} data points.";
					} else {
						$message = "The search result contains {$DataCount_WihoutConditions_Formatted} data points, which matches all of the conditions below:";
					}
				}
				
			} else {
				$message = "The search result contains {$results['DataCount']} data points.";
			}
	
			echo "<p>" . printFontAwesomeIcon('fas fa-search') . " {$message}</p>";
	
			echo $dataFilterSummary;
		echo "</div>";
	echo "</div>";
}


if ($results['ComparisonID']['Missing_Count'] > 0){

	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the comparison IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='comparisonMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;
	
	echo "<div id='comparisonMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Comparison IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Comparison ID Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDExistenceInfoSection'>{$results['ComparisonID']['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Comparison ID Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDExistenceInfoSection'>{$results['ComparisonID']['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Comparison ID Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDExistenceInfoSection'>{$results['ComparisonID']['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'comparisonIDExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Comparison IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$results['ComparisonID']['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['ComparisonID']['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$results['ComparisonID']['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['ComparisonID']['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$results['ComparisonID']['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['ComparisonID']['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	
	echo "</div>";
	
}


if ($results['SampleID']['Missing_Count'] > 0){

	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the sample IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='sampleMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;
	
	echo "<div id='sampleMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Sample IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Sample ID Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDExistenceInfoSection'>{$results['SampleID']['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Sample ID Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDExistenceInfoSection'>{$results['SampleID']['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Sample ID Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDExistenceInfoSection'>{$results['SampleID']['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'sampleIDExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Sample IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$results['SampleID']['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['SampleID']['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$results['SampleID']['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['SampleID']['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$results['SampleID']['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['SampleID']['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	
	echo "</div>";
	
}


if ($tooManyDataPoints){
	
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are too many data points in the search result. 
					Plotting too many data points may cause performance problem to your browser. 
					Please refine your search conditions to reduce the number of data points in the plot.</p>
				<p>" . printFontAwesomeIcon('fa-spacer') . " For preview purposes, the corresponding plot is based on <strong>{$APP_CONFIG['canvasxpress']['Data_Limit']}</strong> (out of {$results['DataCount_WihoutConditions']}) randomly selected data points.</p>
				
				";
	
	$message .= "<div style='margin-left:20px;'><ul>";
	foreach($plotColumns as $tempKey => $plotColumn){
		
		$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$plotColumn]['Title'];
		
		$modalID = 'TooManyDataPoints_' . md5($plotColumn);
		$modalTitle = "<h4 class='modal-title'>{$plotColumnToDisplay} ({$results['DataCount']})</h4>";
		
		if (true){
			unset($tableContent);
			$tableContent['Header'][1]		= 'Category';
			$tableContent['Header'][2] 		= "<span class='text-nowrap'># of Data Points</span>";
			
			foreach($results['Category_Count'][$plotColumn] as $plotColumnCategory => $geneValueCount){
				
				if ($geneValueCount >= $APP_CONFIG['canvasxpress']['Data_Limit']){
					$geneValueCount = $geneValueCount . '&nbsp;' . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger');
					$tableContent['Body'][$plotColumnCategory]['Class']	= 'danger';
				}
				
				$tableContent['Body'][$plotColumnCategory]['Value'][1]	= $plotColumnCategory;
				$tableContent['Body'][$plotColumnCategory]['Value'][2] 	= $geneValueCount;
				
			}
			
			$modalBody = printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12');	
			
		}
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
		
		$message .= "<li>";
			$message .= "<a href='#{$modalID}' data-toggle='modal'>";
				$message .= $plotColumnToDisplay;
			$message .= "</a>";
		$message .= "</li>";
		
	}
	$message .= "</ul></div>";
	
	echo $message;
}


if (array_size($_POST_ORG) > 0){
	foreach($_POST_ORG as $tempKey => $tempValue){
		$_POST[$tempKey] = $tempValue;
	}
}


$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);
$geneCount = array_size($results['Category_Count']['Gene Symbol']);
$plotTitle = "{$APP_MESSAGE['Gene']} Expression Levels of Multiple {$APP_MESSAGE['Genes']}";


if (true){
	
	$rawDataKey 		= putSQLCacheWithoutKey($results['Export']['Raw'], '', 'prepareGeneExpressionDataByGeneName_Export', 1);
	$plotDataKey		= putSQLCacheWithoutKey($results['Export']['Transformed'], '', 'prepareGeneExpressionDataByGeneName_Export', 1);
	
	$message = "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ' . 
				"<a href='app_gene_expression_common_single_download.php?key={$rawDataKey}&filename=gene_expression_data_raw.csv' target='_blank'>Raw Data</a>" . 
				" - " . 
				"<a href='app_gene_expression_common_single_download.php?key={$plotDataKey}&filename=gene_expression_data_plot.csv' target='_blank'>Plot Data</a>" . 
				"</p>";
	
	echo $message;
	
	
	if (true){	
		unset($researchProjectAPI);
		$researchProjectAPI['Title'] 			= $plotTitle;
		$researchProjectAPI['Type'] 			= "{$APP_MESSAGE['Gene']} Expression Plot";
		$researchProjectAPI['Source_Page'] 		= $pageTitle;
		$researchProjectAPI['URL'] 				= "gene_expressions/{$_POST['URL']}?key={$urlKey}";
		$researchProjectAPI['Base64_Image_ID'] 	= 'plotSection';
		$researchProjectAPI['Parameters'] 		= $urlKey;
		include('app_research_project_api_modal.php');
			
		unset($researchProjectAPI);
	}
	
	
}


$endTime = microtime(true);
if ($_SESSION['DEBUG_MODE']){	
	echo "<p class='form-text'>Time Spent: " . round($endTime - $APP_CONFIG['StartTime'], 2) . " seconds.</p>";
}

if ($tooManyDataPoints){
	$luckyCandidates = array_keys($results['canvasxpress']['y']['smps']);
	shuffle($luckyCandidates);
	$luckyCandidates = array_slice($luckyCandidates, 0, $APP_CONFIG['canvasxpress']['Data_Limit']);
	$luckyCandidates = array_combine($luckyCandidates, $luckyCandidates);
}


unset($canvasxpress);
foreach($results['canvasxpress']['x'] as $plotColumn => $plotColumnDetails){
	
	$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$plotColumn]['Title'];
	
	if ($plotColumnToDisplay == ''){
		$plotColumnToDisplay = $APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'][$plotColumn];
	}
	
	if ($plotColumnToDisplay == ''){
		$plotColumnToDisplay = $plotColumn;
	}
	
	if ($tooManyDataPoints){
		unset($tempArray);
		foreach($plotColumnDetails as $tempKey => $tempValue){
			if (isset($luckyCandidates[$tempKey])){
				$tempArray[$tempKey] = $tempValue;	
			}
		}
		$plotColumnDetails = $tempArray;
	}
	
	$canvasxpress['x'][] = '"' . $plotColumnToDisplay . '": [' . implode(', ', $plotColumnDetails) . ']';
	
	if ($primaryColumn == ''){
		$primaryColumn = $plotColumnToDisplay;
	} elseif ($secdonaryColumn == ''){
		$secdonaryColumn = $plotColumnToDisplay;
	}
}



if ($tooManyDataPoints){
	unset($tempArray);
	foreach($results['canvasxpress']['y']['smps'] as $tempKey => $tempValue){
		if (isset($luckyCandidates[$tempKey])){
			$tempArray[$tempKey] = $tempValue;	
		}
	}
	
	$results['canvasxpress']['y']['smps'] = $tempArray;
	
}
$canvasxpress['y-smps'] = '[' . implode(",\n", $results['canvasxpress']['y']['smps']) . ']';



if ($tooManyDataPoints){
	unset($tempArray);
	foreach($results['canvasxpress']['y']['data'] as $tempKey => $tempValue){
		if (isset($luckyCandidates[$tempKey])){
			$tempArray[$tempKey] = $tempValue;	
		}
	}
	
	$results['canvasxpress']['y']['data'] = $tempArray;
	
}
$canvasxpress['y-data'] = '[[' . implode(",\n", $results['canvasxpress']['y']['data']) . ']]';



if (true){
	$class = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
	$height = 800;
	$width	= 1000;
	
	$smpLabelScaleFontFactor = 0.7;
	
	if ($geneCount <= 30){
		$height = 900;
		$smpLabelScaleFontFactor = 0.7;
	} elseif ($geneCount <= 60){
		$height = 1200;
		$smpLabelScaleFontFactor = 0.5;
	} else {
		$height = 1600;
		$smpLabelScaleFontFactor = 0.3;
	}
	
	$varLabelScaleFontFactor = 0.7;
	
	$_POST['plot_width'] = intval(abs($_POST['plot_width']));
	if ($_POST['plot_width'] >= 100){
		$width = $_POST['plot_width'];	
	}

	$_POST['plot_height'] = intval(abs($_POST['plot_height']));
	if ($_POST['plot_height'] >= 100){
		$height = $_POST['plot_height'];	
	}

		
	echo "<hr/>";

	echo "<div class='row'>";
		echo "<div class='{$class}'>";
			echo "<h3>{$APP_MESSAGE['Gene']} Expression Levels</h3>";
			
			if ($tooManyDataPoints){
				echo "<p class=form-text>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . "&nbsp;The following plot contains <strong>{$APP_CONFIG['canvasxpress']['Data_Limit']}</strong> data points randomly selected from the search result.</p>";	
			}
	
	
			echo "<div style='height:{$height}px; width:{$width}px;'>";
				echo "<canvas id='plotSection' width='{$width}' height='{$height}' xresponsive='false' aspectRatio='1:1'></canvas>";
			echo "</div>";
			
		echo "</div>";
	echo "</div>";
	

}


if (($_POST['searchOption'] == 1) && ($getComparisonIDsExistenceInfo['Output_Count'] > 0)){

	$_POST_org = $_POST;
	$urlKey_org = $urlKey;
	
	unset($_POST);
	$_POST['ComparisonIndex'] = $getComparisonIDsExistenceInfo['ComparisonIndexes'];
	
	if (true){
		$comparisonIndexes = $_POST['ComparisonIndex'];
	
		unset($_POST);
		
		$_POST['Category'] = 'Comparison';
		$_POST['data_source'][0] = 'public';
		$_POST['data_source'][1] = 'private';
		$_POST['data_source_private_project_indexes'] = array_keys(internal_data_get_accessible_project());
		
		
		$_POST['Field_1'] 		= 'ComparisonIndex';
		$_POST['Operator_1'] 	= 5;
		$_POST['Value_1'] 		= implode(',', $comparisonIndexes);
		$_POST['rowCount']		= 1;
		$_POST['API']			= 1;
		$_POST['URL']			= '';
		$_POST['bookmark']		= 0;
		$_POST['Simple']		= 1;
		
		
		
		include('app_record_browse_component_exe.php');	
		
	}
	$_POST = $_POST_org;
	$urlKey = $urlKey_org;
} elseif (($_POST['searchOption'] == 2) && ($getSampleIDsExistenceInfo['Output_Count'] > 0)){

	$_POST_org = $_POST;
	$urlKey_org = $urlKey;

	unset($_POST);
	$_POST['SampleIndex'] = $getSampleIDsExistenceInfo['SampleIndexes'];

	
	if (true){
		$sampleIndexes = $_POST['SampleIndex'];
	
		unset($_POST);
		
		$_POST['Category'] = 'Sample';
		$_POST['data_source'][0] = 'public';
		$_POST['data_source'][1] = 'private';
		$_POST['data_source_private_project_indexes'] = array_keys(internal_data_get_accessible_project());
		
		
		$_POST['Field_1'] 		= 'SampleIndex';
		$_POST['Operator_1'] 	= 5;
		$_POST['Value_1'] 		= implode(',', $sampleIndexes);
		$_POST['rowCount']		= 1;
		$_POST['API']			= 1;
		$_POST['URL']			= '';
		$_POST['bookmark']		= 0;
		$_POST['Simple']		= 1;
		
		
		
		include('app_record_browse_component_exe.php');	
		
	}
	$_POST = $_POST_org;
	$urlKey = $urlKey_org;
}




?>
<script type="text/javascript">

$(document).ready(function(){

	var plotObj = new CanvasXpress('plotSection',
		{
            "x" : {
				<?php echo implode(",\n", $canvasxpress['x']); ?>
            },
            "y" : {
              "vars" : ['expression'],
              "smps" : <?php echo $canvasxpress['y-smps']; ?>,
              "data" : <?php echo $canvasxpress['y-data']; ?>
              
            }
		},
		  
		  
		{
			 "graphOrientation": 		"horizontal",
			 "graphType": 				"Boxplot",
			 "jitter": 					true,
			 
			 <?php 
			 	if ($secdonaryColumn != ''){
					echo "'colorBy': '{$secdonaryColumn}',";
				} 
			?>

			 
			 "plotByVariable": 			true,
			 "showBoxplotOriginalData": true,
			 "smpLabelRotate": 			0,
			 
			 
			 
			 "legendBox":  				true,
			 "showLegend": 				true,
			 "showShadow": 				false,
			 
			 "title": 					"<?php //echo $formula; ?>",
			 <?php
			 /*
			 'axisTitleScaleFontFactor': 	0.5,
			 'axisTickFontSize':			12,
			 'axisTickScaleFontFactor': 	0.5,
			 
			 "citation": 				"",
			 'citationScaleFontFactor': 0.7,

			 
			 "xAxisTitle": 				"",	
			 "titleFontSize":			25,
			
			 
			 'smpLabelScaleFontFactor': 	<?php echo $smpLabelScaleFontFactor; ?>,
			 'varLabelScaleFontFactor':		<?php echo $varLabelScaleFontFactor; ?>,
			 'titleScaleFontFactor': 		0.7,
			 'subtitleScaleFontFactor': 	0.7,

			 'legendScaleFontFactor': 		0.6,
			 
			 'nodeScaleFontFactor': 		0.7,
			 
			 'sampleSeparationFactor': 		0.7,
			 'variableSeparationFactor': 	0.7,
			 'widthFactor': 				0.7,
*/
			 ?>
			 "xAxisTitle": 					"<?php echo $valueText; ?>",	
			 'printType':					'window',
			 
			 <?php if ($_POST['graphOrientation'] == 'vertical'){ ?>
			 'graphOrientation':			'vertical',
			 <?php } ?>
		},
		
		<?php /*
		{
			click: function(o, e, t) { if ((typeof o != 'undefined') && (typeof o.y != 'undefined') && (typeof o.y['smps'] != 'undefined')){ var sampleName = o.y['smps']; var geneValue = parseFloat(o.y['data']).toFixed(4); var title = o.y['vars']; var content = '<div><strong>Sample</strong>: ' + sampleName + '</div>'; content += '<div><strong>Expression</strong>: ' + geneValue + '</div>'; content += '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=sample&inputName=' + sampleName + '\' target=\'_blank\'>View Sample</a></div>'; content += '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=project&inputName=' + sampleName + ' target=\'_blank\'>View Project</a></div>'; t.showInfoSpan(e, content); }},
			mousemove: function(o, e, t) { if ((typeof o != 'undefined') && (typeof o.y != 'undefined') && (typeof o.y['smps'] != 'undefined')){ var sampleName = o.y['smps']; var geneValue = parseFloat(o.y['data']).toFixed(4); var title = o.y['vars']; var content = '<div><strong>Sample</strong>: ' + sampleName + '</div>'; content += '<div><strong>Expression</strong>: ' + geneValue + '</div>'; content += '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=sample&inputName=' + sampleName + '\' target=\'_blank\'>View Sample</a></div>'; content += '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=project&inputName=' + sampleName + ' target=\'_blank\'>View Project</a></div>'; t.showInfoSpan(e, content); }},
			mouseout: function(o, e, t) {},
			dblclick: function(o, e, t) { return false; }
		}
		*/ ?>
	);
	plotObj.sizes = plotObj.sizes.map(function(x){
			return Number(x * 0.5).toFixed(1);
		});
	CanvasXpress.stack["plotSection"]["config"]["sizes"] = plotObj.sizes.map(function(x) { return Number(x * 0.5).toFixed(1); });
	plotObj.groupSamples(["<?php echo $primaryColumn; ?>"]);
	
	
	<?php if ($secdonaryColumn == ''){ ?>
		plotObj.changeAttribute("colorBy", "<?php echo $primaryColumn; ?>");
	<?php } ?>

	
	<?php if ($_POST['transform']){ ?>
	plotObj.transform('log2');
    <?php } ?>
	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "<?php echo "{$_POST['URL']}?key={$urlKey}"; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "", URL);
	}
	<?php } ?>

});

</script>
