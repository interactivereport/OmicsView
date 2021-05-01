<?php


function getInternalDataRequirement($dataType){
	
	if ($dataType == 'Sample'){
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 1;
		$requirement['GeneLevelExpression'] = 1;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 0;
		$requirement['ComparisonData'] 		= 0;	
	} elseif ($dataType == 'Comparison'){
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 0;
		$requirement['GeneLevelExpression'] = 0;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 1;
		$requirement['ComparisonData'] 		= 1;
	} elseif ($dataType == 'Project'){
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 0;
		$requirement['GeneLevelExpression'] = 0;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 0;
		$requirement['ComparisonData'] 		= 0;
	} else {
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 1;
		$requirement['GeneLevelExpression'] = 1;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 1;
		$requirement['ComparisonData'] 		= 1;
	}
	
	return $requirement;
	
}


function validateInternalData($inputArray){
	
	global $APP_CONFIG;
	
	$requirement = getInternalDataRequirement($inputArray['Data_Type']);
	
	
	foreach($APP_CONFIG['Internal_Data'] as $currentKey => $currentValue){
		if ($requirement[$currentKey] && !is_file($inputArray['Files'][$currentKey]['Path'])){
			$hasError = 1;
			$results['Error_Message'][] = $APP_CONFIG['Internal_Data'][$currentKey]['File_Not_Exist_Message'];
		}
	}
	
	if ($hasError){
		return $results;	
	}
	
	$delimiter = $inputArray['File_Fomat'];
	
	
	
	foreach($APP_CONFIG['Internal_Data'] as $currentKey => $currentValue){
		
		if (!$requirement[$currentKey]) continue;
		
		
		if ($currentKey == 'Samples'){
			$results[$currentKey]['readFirstFewLinesFromFile'] 
				= readFirstFewLinesFromFile($inputArray['Files'][$currentKey]['Path'], 0, 1, $delimiter);
			
			$sampleIDs = array_column($results[$currentKey]['readFirstFewLinesFromFile']['Body'], 'SampleID');
			
			
			if (array_size($sampleIDs) <= 0){
				
				foreach($APP_CONFIG['Internal_Data'][$currentKey]['Known_Map_SampleID'] as $tempKeyX => $tempValueX){
					$sampleIDs = array_column($results[$currentKey]['readFirstFewLinesFromFile']['Body'], $tempValueX);
					
					if (array_size($sampleIDs) > 0) break;
					
				}
			}
			
			$results[$currentKey]['SampleIDs'] = $sampleIDs;
			
			
			
			$results[$currentKey]['readFirstFewLinesFromFile']['Body'] = array_slice($results[$currentKey]['readFirstFewLinesFromFile']['Body'], 0, $APP_CONFIG['APP']['Interal_Data_Read_Count'], true);
			
			$temp = array();
			$temp[0] = '';
			
			foreach($results[$currentKey]['readFirstFewLinesFromFile']['Body'] as $tempKeyX => $tempValueX){
				$temp[] = $tempValueX;	
			}
			
			$results[$currentKey]['readFirstFewLinesFromFile']['Body'] = $temp;
			unset($temp, $results[$currentKey]['readFirstFewLinesFromFile']['Body'][0]);
			
			
		} else {
			$results[$currentKey]['readFirstFewLinesFromFile'] 
				= readFirstFewLinesFromFile($inputArray['Files'][$currentKey]['Path'], $APP_CONFIG['APP']['Interal_Data_Read_Count'], 1, $delimiter);
		}
		
		
		
		
		if (!isset($results[$currentKey]['readFirstFewLinesFromFile']['Header'])){
			$currentName = strtolower($APP_CONFIG['Internal_Data'][$currentKey]['Name']);
			$results['Error_Message'][] = "The {$currentName} file does not contain any header information. Please verify your file and try again.";
			$hasError = 1;
		} elseif (!isset($results[$currentKey]['readFirstFewLinesFromFile']['Body'])){
			$currentName = strtolower($APP_CONFIG['Internal_Data'][$currentKey]['Name']);
			$results['Error_Message'][] = "The {$currentName} file does not contain any data. Please verify your file and try again.";
			$hasError = 1;
		} else {
			
			$results[$currentKey]['Unsure-Count'] = 0;
			
			foreach($results[$currentKey]['readFirstFewLinesFromFile']['Header'] as $tempKey => $currentHeaderOrg){
	
				$currentHeader = sanitizeColumnHeader($currentHeaderOrg);
				
				
				if (isset($APP_CONFIG['Internal_Data'][$currentKey]['Headers'][$currentHeader])){
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['Index'] = $tempKey;
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['SQL'] 	= $currentHeader;
					
				} elseif ($inputArray['Expression_Fomat'] && support_table_format_data($currentKey) && isset($APP_CONFIG['Internal_Data'][$currentKey]['Known_Map_Table'][strtolower($currentHeader)])){
					
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['Index'] = $tempKey;
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['SQL'] 	= 'BXAPP_DATA_EXPRESSION';
					
					
				} elseif (isset($APP_CONFIG['Internal_Data'][$currentKey]['Known_Map'][strtolower($currentHeader)])){
					
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['Index'] = $tempKey;
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['SQL'] 	= $APP_CONFIG['Internal_Data'][$currentKey]['Known_Map'][strtolower($currentHeader)];
				} elseif (($currentKey == 'GeneLevelExpression') && (array_size($sampleIDs) > 0) && (in_array($currentHeaderOrg, $sampleIDs))){
					
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['Index'] = $tempKey;
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['SQL'] 	= 'SampleID';
					
				} elseif (0 && $inputArray['Expression_Fomat'] && support_table_format_data($currentKey)){
					
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['Index'] = $tempKey;
					$results[$currentKey]['Header-Map'][$currentHeaderOrg]['SQL'] 	= 'BXAPP_DATA_OTHER';
					
				} else {
					
					unset($levenshtein);
					
					foreach($APP_CONFIG['Internal_Data'][$currentKey]['Headers'] as $currentSQL => $tempValuex){
						$levenshtein[$currentSQL] = levenshtein(strtolower($currentHeader), strtolower($tempValuex['Name']));
					}
					asort($levenshtein);
					reset($levenshtein);
					
					$results[$currentKey]['Unsure'][$currentHeaderOrg] 				= $tempKey;
					$results[$currentKey]['Unsure-Count']++;
					
					
					if ($currentKey == 'GeneLevelExpression'){
						
						if ($tempKey == 0){
							$results[$currentKey]['Best-Guest'][$currentHeaderOrg]['Index'] = $tempKey;
							$results[$currentKey]['Best-Guest'][$currentHeaderOrg]['SQL'] 	= 'Gene';
						} else {
							$results[$currentKey]['Best-Guest'][$currentHeaderOrg]['Index'] = $tempKey;
							$results[$currentKey]['Best-Guest'][$currentHeaderOrg]['SQL'] 	= 'SampleID';
						}
					} else {
						$results[$currentKey]['Best-Guest'][$currentHeaderOrg]['Index'] = $tempKey;
						$results[$currentKey]['Best-Guest'][$currentHeaderOrg]['SQL'] 	= key($levenshtein);
					}
					
					$results['Summary']['Unsure-Count']++;

				}
			}
			
			
			
			
			if ($results[$currentKey]['Unsure-Count'] > 0){
				
				foreach($results[$currentKey]['Unsure'] as $currentHeaderOrg => $currentHeaderX){
					unset($found);
					
					$currentHeader = sanitizeColumnHeader($currentHeaderOrg);
					
					foreach($APP_CONFIG['Internal_Data'][$currentKey]['Headers'] as $targetHeader => $targetHeaderInfo){
						
						if (strtolower($currentHeader) == strtolower($targetHeaderInfo['Name'])){
							
							unset($results[$currentKey]['Unsure'][$currentHeaderOrg]);
							$results[$currentKey]['Unsure-Count']--;
							$results['Summary']['Unsure-Count']--;
							
							$results[$currentKey]['Header-Map'][$currentHeaderOrg]['Index'] = $currentHeaderX;
							$results[$currentKey]['Header-Map'][$currentHeaderOrg]['SQL'] 	= $targetHeader;
							
							if ($results[$currentKey]['Unsure-Count'] == 0){
								unset($results[$currentKey]['Unsure']);
							}
							
						}
						
					}
					
				}
				
			}
			
			
			
		}
		
		
	}
	
	
	if ($hasError){
		return $results;	
	}
	
	
	
	return $results;
	
	
}


function checkInternalDataChoice($inputArray){
	
	global $APP_CONFIG;
	
	$delimiter = $inputArray['File_Fomat'];
	
	$requirement = getInternalDataRequirement($inputArray['Data_Type']);
	
	if ($requirement['Projects']){
		$currentKey			= 'Projects';
		$keyColumn			= 'ProjectID';
		$rawFile	 		= readFirstFewLinesFromFile($inputArray['Files'][$currentKey]['Path'], 0, 1, $delimiter);
		
		if (in_array($keyColumn, $inputArray['Choice'][$currentKey])){
			$keyColumnIndex		= array_search($keyColumn, $inputArray['Choice'][$currentKey]);
			$keyColumnValue 	= array_column($rawFile['Body'], $rawFile['Header'][$keyColumnIndex]);
			$keyColumnValueUnique = array_unique($keyColumnValue);
			if (array_size($keyColumnValue) != array_size($keyColumnValueUnique)){
				$results['Error_Message'][] = "The project identifier column ({$rawFile['Header'][$keyColumnIndex]}) needs to be unique.";	
			}
			$projectCount 		= array_size($keyColumnValue);
		}
		
		
		if (is_internal_column_flexible($currentKey)){
			$array_count_values = array_count_values($inputArray['Choice'][$currentKey]);
			
			if ($array_count_values['BXAPP_CUSTOM_COLUMN'] > $APP_CONFIG['APP']['Custom_Column_Count']){
				$results['Error_Message'][] = "You have selected too many custom columns ({$array_count_values['BXAPP_CUSTOM_COLUMN']}) in project. The maximum number of custom column is {$APP_CONFIG['APP']['Custom_Column_Count']}.";
			}
		}
		
		
		
	}
	
	
	foreach($APP_CONFIG['Internal_Data'] as $currentKey => $currentValue){
		if ($requirement[$currentKey] && !is_file($inputArray['Files'][$currentKey]['Path'])){
			$hasError = 1;
			$results['Error_Message'][] = $APP_CONFIG['Internal_Data'][$currentKey]['File_Not_Exist_Message'];
		}
	}
	
	if ($hasError){
		return $results;	
	}
	
	foreach($APP_CONFIG['Internal_Data'] as $currentKey => $currentValue){
		
		if (!$requirement[$currentKey]) continue;
		
		foreach($APP_CONFIG['Internal_Data'][$currentKey]['Headers'] as $currentSQL => $currentSQLInfo){
			if ($currentSQLInfo['Required']){
				if (!in_array($currentSQL, $inputArray['Choice'][$currentKey])){
					
					if (($projectCount == 1) && ($currentSQLInfo['Optional_if_Single_Project'])){
						//Okay
					} else {
						$hasError = 1;
						$results['Error_Message'][] = "{$currentValue['Name']}: {$currentSQLInfo['Name']} is required.";	
					}
				}
			}
		}
	}
	
	if ($hasError){
		return $results;	
	}

	
	if ($requirement['Samples']){
		$currentKey			= 'Samples';
		$rawFile	 		= readFirstFewLinesFromFile($inputArray['Files'][$currentKey]['Path'], 0, 1, $delimiter);

		$keyColumn			= 'SampleID';
		$keyColumnIndex		= array_search($keyColumn, $inputArray['Choice'][$currentKey]);
		$keyColumnValue 	= array_column($rawFile['Body'], $rawFile['Header'][$keyColumnIndex]);
		$keyColumnValueUnique = array_unique($keyColumnValue);
		if (array_size($keyColumnValue) != array_size($keyColumnValueUnique)){
			$results['Error_Message'][] = "The sample identifier column ({$rawFile['Header'][$keyColumnIndex]}) needs to be unique.";	
		}
		
		
		$keyColumn			= 'PlatformName';
		$keyColumnIndex		= array_search($keyColumn, $inputArray['Choice'][$currentKey]);
		$keyColumnValue 	= array_column($rawFile['Body'], $rawFile['Header'][$keyColumnIndex]);
		
		foreach($keyColumnValue as $tempKey => $platformName){
			$platformTypes[] = getPlatformType($platformName);
		}
		
		$platformTypes		= array_clean($platformTypes);
		
		if (array_size($platformTypes) != 1){
			$results['Error_Message'][] = "In the platform name column of the sample info file, we have found both RNA-Seq and microarray platforms. Please make sure that the sample info contains one kind of platform only.";	
		}
		
		
		if (is_internal_column_flexible($currentKey)){
			$array_count_values = array_count_values($inputArray['Choice'][$currentKey]);
			
			if ($array_count_values['BXAPP_CUSTOM_COLUMN'] > $APP_CONFIG['APP']['Custom_Column_Count']){
				$results['Error_Message'][] = "You have selected too many custom columns ({$array_count_values['BXAPP_CUSTOM_COLUMN']}) in sample. The maximum number of custom column is {$APP_CONFIG['APP']['Custom_Column_Count']}.";
			}
		}

	}
	
	if ($requirement['Comparisons']){
		$currentKey			= 'Comparisons';
		$keyColumn			= 'ComparisonID';
		$rawFile	 		= readFirstFewLinesFromFile($inputArray['Files'][$currentKey]['Path'], 0, 1, $delimiter);
		$keyColumnIndex		= array_search($keyColumn, $inputArray['Choice'][$currentKey]);
		$keyColumnValue 	= array_column($rawFile['Body'], $rawFile['Header'][$keyColumnIndex]);
		$keyColumnValueUnique = array_unique($keyColumnValue);
		if (array_size($keyColumnValue) != array_size($keyColumnValueUnique)){
			$results['Error_Message'][] = "The comparison identifier column ({$rawFile['Header'][$keyColumnIndex]}) needs to be unique.";	
		}
		
		if (is_internal_column_flexible($currentKey)){
			$array_count_values = array_count_values($inputArray['Choice'][$currentKey]);
			
			if ($array_count_values['BXAPP_CUSTOM_COLUMN'] > $APP_CONFIG['APP']['Custom_Column_Count']){
				$results['Error_Message'][] = "You have selected too many custom columns ({$array_count_values['BXAPP_CUSTOM_COLUMN']}) in comparison. The maximum number of custom column is {$APP_CONFIG['APP']['Custom_Column_Count']}.";
			}
		}
	}
	
	if ($hasError){
		return $results;	
	}
	
	
	unset($dataArray);
	$dataArray['Input'] = $inputArray;
	
	$results['Job'] = createInternalDataJob($dataArray);
	
	
	$results['Summary'] = 1;
	
	
	return $results;
	
}


function createInternalDataJob($inputArray){
	
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	unset($dataArray);
	
	$dataArray['Status'] 			= 0;
	$dataArray['User_ID'] 			= $APP_CONFIG['User_Info']['ID'];
	$dataArray['Date']	 			= date('Y-m-d');
	$dataArray['DateTime'] 			= date('Y-m-d H:i:s');
	$dataArray['Import_ID']			= md5("{$dataArray['User_ID']}_{$dataArray['DateTime']}");
	$dataArray['Permission']		= intval($inputArray['Input']['Permission']);
	
	$input = $inputArray['Input'];
	$dataArray['Input']				= json_encode($inputArray['Input']);
	$dataArray['Input_Checksum']	= md5($dataArray['Input']);
	
	
	
	if (isset($_SESSION['Internal_Data_Saved'][$dataArray['Input_Checksum']])){
		$results['Error'] 	= 0;
		$results['ID']		= $_SESSION['Internal_Data_Saved'][$dataArray['Input_Checksum']];
	} else {
		
		$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Job'];
		$SQL = getInsertSQLQuery($SQL_TABLE, $dataArray);
	
		$sql_exe_results = execSQL($SQL);
		
		if (!$sql_exe_results){
			$results['Error'] 	= 1;
			$results['SQL'] 	= $SQL;
			$results['Message'] = "Database error. Please contact us for details.";
		} else {
			$results['Error'] = 0;
			$results['ID']		= getLastInsertID();
			
			$_SESSION['Internal_Data_Saved'][$dataArray['Input_Checksum']] = $results['ID'];
			
		}
		
	}

	return $results;
	
}


function getAllInternalDataJobs(){

	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Job'];

	$jobIDs = internal_data_get_accessible_jobIDs();
	$jobIDs = implode(',', $jobIDs);
	
	
	if ($jobIDs != ''){
		
		$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE (`ID` IN ({$jobIDs})) ORDER BY `ID` DESC";
		$dataArray = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 0, 0);

		foreach($dataArray as $ID => $tempValue){
			$dataArray[$ID] = processInternalDataJob($tempValue, $ID);
		}
	}
	
	return $dataArray;
	
}


function getInternalDataJob($ID = 0){

	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Job'];
	$ID 		= intval($ID);
	
	if ($ID <= 0) return false;
	
	
	$jobIDs = internal_data_get_accessible_jobIDs();
	
	
	
	if (!in_array($ID, $jobIDs)){
		return false;	
	}
	
	$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `ID` = {$ID}";
	$dataArray = getSQL($SQL, 'GetRow', $SQL_TABLE, 0, 0);
	
	
	if ($dataArray['ID'] > 0){
		$dataArray = processInternalDataJob($dataArray, $ID, 1);
	}
	
	return $dataArray;
	
}


function processInternalDataJob($dataArray, $ID, $detail = 0){
	
	global $APP_CONFIG, $APP_MESSAGE, $BXAF_CONFIG;
	
	if ($dataArray['Processed']){
		return $dataArray;	
	} elseif (array_size($dataArray) > 0){
		
		$dataArray['Processed'] = 1;
		
		if ($dataArray['ID'] <= 0){
			$dataArray['ID'] = $ID;	
		}
	
		$dataArray['Input'] 	= json_decode($dataArray['Input'], true);
		$dataArray['Output'] 	= json_decode($dataArray['Output'], true);
		$dataArray['Project_Details'] 				= json_decode($dataArray['Project_Details'], true);
		$dataArray['Sample_Details'] 				= json_decode($dataArray['Sample_Details'], true);
		$dataArray['Comparison_Details'] 			= json_decode($dataArray['Comparison_Details'], true);
		$dataArray['GeneLevelExpression_Details'] 	= json_decode($dataArray['GeneLevelExpression_Details'], true);
		$dataArray['Gene_Expression_Plot_Details'] 	= json_decode($dataArray['Gene_Expression_Plot_Details'], true);
		$dataArray['Bubble_Plot_Details'] 			= json_decode($dataArray['Bubble_Plot_Details'], true);
		
		
		$dataArray['User']		= getUserInfo($dataArray['User_ID']);
		
		$requirement = getInternalDataRequirement($dataArray['Input']['Data_Type']);

		$dataArray['Requirement'] = $requirement;
		
		$dataArray['Gene_Mapping'] = $dataArray['Input']['Gene_Mapping'];
		
		if ($dataArray['Gene_Mapping'] == ''){
			$dataArray['Gene_Mapping'] = $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping_Choice'];	
		}
		
		$dataArray['Gene_Mapping_Display'] = $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping'][$dataArray['Gene_Mapping']]['Name'];

		if (isAdminUser()){
			$dataArray['canUpdate'] = 1;
		} elseif ($dataArray['User_ID'] == $APP_CONFIG['User_Info']['ID']){
			$dataArray['canUpdate'] = 1;
		} else {
			$dataArray['canUpdate'] = 0;
		}
		
		if ($dataArray['Permission']){
			$dataArray['Permission_HTML'] = 'Public';
		} else {
			$dataArray['Permission_HTML'] = 'Private';
		}
		
		
		$dataArray['Status_Printable']			= getInternalDataJobStatusPrint($dataArray['Status']);
		$dataArray['Status_HTML'] 				= getInternalDataJobStatusHTML($dataArray['Status']);
	
		$dataArray['Status_MySQL_Printable']	= getInternalDataJobStatusPrint($dataArray['Status_MySQL']);
		$dataArray['Status_MySQL_HTML'] 		= getInternalDataJobStatusHTML($dataArray['Status_MySQL']);
		
		if ($requirement['GeneLevelExpression']){
			$dataArray['Status_Tabix_GeneLevelExpression_Printable']	= getInternalDataJobStatusPrint($dataArray['Status_Tabix_GeneLevelExpression']);
			$dataArray['Status_Tabix_GeneLevelExpression_HTML']			= getInternalDataJobStatusHTML($dataArray['Status_Tabix_GeneLevelExpression']);
		}
		
		if ($requirement['Comparisons']){
			$dataArray['Status_Tabix_Comparison_Printable']		= getInternalDataJobStatusPrint($dataArray['Status_Tabix_Comparison']);
			$dataArray['Status_Tabix_Comparison_HTML']			= getInternalDataJobStatusHTML($dataArray['Status_Tabix_Comparison']);
			
			$dataArray['Status_GO_Printable']		= getInternalDataJobStatusPrint($dataArray['Status_GO']);
			$dataArray['Status_GO_HTML']			= getInternalDataJobStatusHTML($dataArray['Status_GO']);
			
			$dataArray['Status_PAGE_Printable']		= getInternalDataJobStatusPrint($dataArray['Status_PAGE']);
			$dataArray['Status_PAGE_HTML']			= getInternalDataJobStatusHTML($dataArray['Status_PAGE']);
		}
		
		if ($dataArray['DateTime_Start'] == '0000-00-00 00:00:00'){
			$dataArray['DateTime_Start'] = '';
		}
		
		
		if ($dataArray['DateTime_End'] == '0000-00-00 00:00:00'){
			$dataArray['DateTime_End'] = '';
		} else {
			$dataArray['Duration'] = strtotime($dataArray['DateTime_End']) - strtotime($dataArray['DateTime_Start']);
			$dataArray['Duration'] = round($dataArray['Duration']/60, 2) . ' minutes';
		}
		
		
		if ($dataArray['Internal_Platform_Type'] == ''){
			$dataArray['Internal_Platform_Type'] = 'RNA-Seq';	
		}
		
		
		if (true){		
			$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Projects'];
			$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `Job_ID` = {$ID}";
			$dataArray['Projects'] = getSQL($SQL, 'GetArray', $SQL_TABLE);
			$dataArray['Project_Count'] = array_size($dataArray['Projects']);
			
			$dataArray['Project_HTML'] = array_column($dataArray['Projects'], 'ProjectID');
			$dataArray['Project_HTML'] = implode(',', $dataArray['Project_HTML']);
			
			if ($dataArray['Project_HTML'] == ''){
				$dataArray['Project_HTML'] = 'To Be Determined';	
			}
			
			
		}
			
			
		if ($requirement['Comparisons']){
			$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Comparisons'];
			if ($detail){
				$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `Job_ID` = {$ID}";
				$dataArray['Comparisons'] 		= getSQL($SQL, 'GetArray', $SQL_TABLE);
				$dataArray['Comparison_Count'] 	= array_size($dataArray['Comparisons']);
			} else {
				$SQL = "SELECT count(*) FROM `{$SQL_TABLE}` WHERE `Job_ID` = {$ID}";
				$dataArray['Comparison_Count'] =  getSQL($SQL, 'GetOne', $SQL_TABLE);
			}
		}
		
		
		if ($requirement['Samples']){
			$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Samples'];
			if ($detail){	
				$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `Job_ID` = {$ID}";
				$dataArray['Samples'] 		= getSQL($SQL, 'GetArray', $SQL_TABLE);
				$dataArray['Sample_Count'] 	= array_size($dataArray['Samples']);
			} else {
				$SQL = "SELECT count(*) FROM `{$SQL_TABLE}` WHERE `Job_ID` = {$ID}";
				$dataArray['Sample_Count'] = getSQL($SQL, 'GetOne', $SQL_TABLE);
			}
		}

		
		if ($detail){	
		
		
			if (isset($dataArray['Output']['Summary']['GeneLevelExpression']['GeneIndex'])){
				$count = number_format(array_size($dataArray['Output']['Summary']['GeneLevelExpression']['GeneIndex']));
				$errorMessage	= "<div class='text-nowrap'>{$APP_MESSAGE['# of Unique Gene Imported']}: {$count}</div>";
				$dataArray['Input']['Files']['GeneLevelExpression']['Warning'][] = $errorMessage;
			}
			
			
			if (isset($dataArray['Output']['Summary']['ComparisonData']['GeneIndex'])){
				$count = number_format(array_size($dataArray['Output']['Summary']['ComparisonData']['GeneIndex']));
				$errorMessage	= "<div class='text-nowrap'>{$APP_MESSAGE['# of Unique Gene Imported']}: {$count}</div>";
				$dataArray['Input']['Files']['ComparisonData']['Warning'][] = $errorMessage;
			}

			foreach($dataArray['Output']['Warning'] as $errorKey => $errorInfo){
				
				if (isset($dataArray['Input']['Files'][$errorKey])){
					
					unset($fileArray);
					$fileArray['Path'] 			= $errorInfo['Path'];
					$fileKey		 			= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
				
					$fileURL 					= "app_common_download.php?key={$fileKey}";
					$tempCount					= number_format($errorInfo['Count']);
					$tempPercentage				= round($errorInfo['Count']/$dataArray['Output']['Line'][$errorKey] * 100, 2);
					
					
					$errorMessage	= 
						"<div class='text-nowrap'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " {$errorInfo['Message']}</div>" . 
						"<div># of Affected Data:  {$tempCount} ({$tempPercentage}%)</div>" . 
						"<div><a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " Download Complete List</a></div>";
					
					
					$dataArray['Input']['Files'][$errorKey]['Warning'][] = $errorMessage;
				} elseif (strpos($errorKey, 'GeneLevelExpression_SampleID') === 0){
					
					unset($fileArray);
					$fileArray['Path'] 			= $errorInfo['Path'];
					$fileKey		 			= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
				
					$fileURL 					= "app_common_download.php?key={$fileKey}";
					$tempCount					= number_format($errorInfo['Count']);
					$tempPercentage				= round($errorInfo['Count']/$dataArray['Output']['Line']['GeneLevelExpression'] * 100, 2);
					
					
					$errorMessage	= 
						"<div class='text-nowrap'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " {$errorInfo['Message']}</div>" . 
						"<div># of Affected Data:  {$tempCount} ({$tempPercentage}%)</div>" . 
						"<div><a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " Download Complete List</a></div>";
					
					
					$dataArray['Input']['Files']['GeneLevelExpression']['Warning'][] = $errorMessage;
					
				} elseif (strpos($errorKey, 'ComparisonData_') === 0){
					
					$errorMessage	= 
						"<div class='text-nowrap'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " {$errorInfo['Message']}</div>";
					$dataArray['Input']['Files']['ComparisonData']['Warning'][] = $errorMessage;
				} elseif (strpos($errorKey, 'GeneLevelExpression_') === 0){
					
					$errorMessage	= 
						"<div class='text-nowrap'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " {$errorInfo['Message']}</div>";
					$dataArray['Input']['Files']['GeneLevelExpression']['Warning'][] = $errorMessage;
				}
				
			}

			
			foreach($dataArray['Output']['Error'] as $errorKey => $errorInfo){
				$type = $errorInfo['Type'];
				$dataArray['Output']['Error_Processed'][$type][] = $errorInfo;
			}
			
			
			if (isset($dataArray['Input']['Files']['GeneCount']) && isset($dataArray['Input']['Files']['GeneLevelExpression'])){
				$dataArray['Output']['Imported']['GeneCount'] = $dataArray['Output']['Imported']['GeneLevelExpression'];
				
				$dataArray['Input']['Files']['GeneCount']['Warning'] = $dataArray['Input']['Files']['GeneLevelExpression']['Warning'];
			}
			
			
			
		}

	}
	

	return $dataArray;
	
}


function getInternalDataJobStatusPrint($status){
	if ($status == 0){
		$status = 'Not Started. Waiting to Be Processed';
	} elseif ($status == 1){
		$status = 'Completed';
	} elseif ($status == 2){
		$status = 'Processing';
	} elseif ($status == 3){
		$status = 'Error Found';
	}
	
	return $status;
	
}


function getInternalDataJobStatusHTML($status){
	if ($status == 0){
		$status = 'Not Started. Waiting to Be Processed';
	} elseif ($status == 1){
		$status = printFontAwesomeIcon('fas fa-check text-success') . '&nbsp;' . 'Completed';
	} elseif ($status == 2){
		$status = printFontAwesomeIcon('fas fa-spinner fa-spin') . '&nbsp;' . 'Processing';
	} elseif ($status == 3){
		$status = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . '&nbsp;' . 'Error Found';
	}
	
	return $status;
	
}


function getInternalDataProcessedDir($userID = 0, $projectIndex = 0){
	
	global $BXAF_CONFIG;
	
	return "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/";	
}


function deleteInternalDataByJobID($jobID){
	
	global $APP_CONFIG;
	
	$jobID = intval($jobID);
	
	$jobInfo = getInternalDataJob($jobID);

	if ($jobInfo['canUpdate']){
		
		unset($SQLs);
		
		$tables = array();
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Job'];
		
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Research_Project'];
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Definition'];
		
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Comparisons'];
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Projects'];
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Samples'];
		
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Comparisons_Combined'];
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Projects_Combined'];
		$tables[] = $APP_CONFIG['Table']['App_User_Data_Samples_Combined'];
		
		foreach($tables as $tempKey => $currentTable){
			if (tableExists($currentTable)){
				if ($currentTable == $APP_CONFIG['Table']['App_User_Data_Job']){
					$SQLs[] = "DELETE FROM `{$currentTable}` WHERE (`ID` = '{$jobID}')";					
				} else {
					$SQLs[] = "DELETE FROM `{$currentTable}` WHERE (`Job_ID` = '{$jobID}')";
				}
			}	
		}

		
		foreach($SQLs as $tempKey => $SQL){
			execSQL($SQL);
		}
		
		clearCache(0);
		clear_tabix_cache();
		cleanPlotCache();
		
		return true;
	}
}

function reRunInternalDataByJobID($jobID){
	
	global $APP_CONFIG;
	
	$jobID = intval($jobID);
	
	$jobInfo = getInternalDataJob($jobID);

	if ($jobInfo['canUpdate']){
		
		unset($SQLs);
		
		$SQLs[] = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Comparisons']}` WHERE (`Job_ID` = '{$jobID}')";
		$SQLs[] = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Projects']}` WHERE (`Job_ID` = '{$jobID}')";
		$SQLs[] = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Samples']}` WHERE (`Job_ID` = '{$jobID}')";
		$SQLs[] = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Research_Project']}` WHERE (`Job_ID` = '{$jobID}')";
		$SQLs[] = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Definition']}` WHERE (`Job_ID` = '{$jobID}')";
		
		$SQLs[] = "UPDATE `{$APP_CONFIG['Table']['App_User_Data_Job']}` SET 
					`Status` = 0, `Status_MySQL` = 0, 
					`Status_Tabix_Comparison` = 0, `Status_Tabix_GeneLevelExpression` = 0, 
					`Status_GO` = 0, `Status_PAGE` = 0
					 WHERE (`ID` = '{$jobID}')";
		
		foreach($SQLs as $tempKey => $SQL){
			execSQL($SQL);
		}
		
		clearCache(0);
		clear_tabix_cache();
		cleanPlotCache();
		
		return true;
	}
}

function buildInternalDataKnownMap(){
	
	global $APP_CONFIG;
	
	
	foreach($APP_CONFIG['Internal_Data'] as $category => $currentInfo){
		
		foreach($APP_CONFIG['Internal_Data'][$category]['Headers'] as $currentColumn => $currentColumnInfo){
			$currentName = strtolower($currentColumnInfo['Name']);
			
			if ($currentName == '') continue;
			
			if (!isset($APP_CONFIG['Internal_Data'][$category]['Known_Map'][$currentName])){
				$APP_CONFIG['Internal_Data'][$category]['Known_Map'][$currentName] = $currentColumn;
			}
			
			$currentColumnLower = strtolower($currentColumn);
			if (!isset($APP_CONFIG['Internal_Data'][$category]['Known_Map'][$currentColumn])){
				$APP_CONFIG['Internal_Data'][$category]['Known_Map'][$currentColumnLower] = $currentColumn;
			}
			
		}
		
		foreach($APP_CONFIG['Internal_Data'][$category]['Example_File_Map'] as $tempKey => $tempValue){
			$APP_CONFIG['Internal_Data_Filename_Hint'][$tempValue] = $category;
		}
	}
	
	return true;
	
}


function updateInternalDataPermission($jobID, $permission){
	
	global $APP_CONFIG;
	
	$jobID = intval($jobID);
	
	$jobInfo 	= getInternalDataJob($jobID);
	$permission	= intval($permission);
	
	if ($jobInfo['canUpdate']){
		$dataArray = array();
		
		if (($permission != 0) && ($permission != 1)){
			$permission = 0;	
		}
		unset($SQLs);
		
		$SQLs[] = "UPDATE `{$APP_CONFIG['Table']['App_User_Data_Job']}` SET `Permission` = '{$permission}' WHERE (`ID` = '{$jobID}')";
		$SQLs[] = "UPDATE `{$APP_CONFIG['Table']['App_User_Data_Comparisons']}` SET `Permission` = '{$permission}' WHERE (`Job_ID` = '{$jobID}')";
		$SQLs[] = "UPDATE `{$APP_CONFIG['Table']['App_User_Data_Projects']}` SET `Permission` = '{$permission}' WHERE (`Job_ID` = '{$jobID}')";
		$SQLs[] = "UPDATE `{$APP_CONFIG['Table']['App_User_Data_Samples']}` SET `Permission` = '{$permission}' WHERE (`Job_ID` = '{$jobID}')";
		
		foreach($SQLs as $tempKey => $SQL){
			execSQL($SQL);
		}
		
		
		
		clearCache(0);
		clear_tabix_cache();
		cleanPlotCache();
		
		return true;
	}
	
	
}

function getInternalDataJob_NonEmptyColumns($projectIndexes = '', $category = ''){
	
	return false;

}


function getColumnDisplayName($category = '', $column = '', $jobID = 0){
	
	global $APP_CONFIG, $BAXF_CACHE;
	
	if (isset($BAXF_CACHE[__FUNCTION__][$category][$column][$jobID])){
		return $BAXF_CACHE[__FUNCTION__][$category][$column][$jobID];
	}

	
	$category = trim(strtolower($category));
	
	if (strpos($category, 'comparison') === 0){
		$currentTable	 	= 'Comparisons';
	} elseif (strpos($category, 'project') === 0){
		$currentTable 	= 'Projects';
	} elseif (strpos($category, 'sample') === 0){
		$currentTable 	= 'Samples';
	} elseif (strpos($category, 'gene') === 0){
		$currentTable 	= 'GeneCombined';
	} else {
		return $column;	
	}
	
	

	
	if (strpos($column, 'Custom_') === 0){
		$definition = internal_data_get_definition($currentTable, $jobID, $column);	
		$display = $definition['Display'];
	} else {
		
		$display = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$column]['Title'];
	
		if ($display == ''){
			$display = $APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$column]['Name'];
		}
	
		if (strpos($column, '_') !== FALSE){
			$display = str_replace('_', ' ', $display);
		}	
	}
	
	$BAXF_CACHE[__FUNCTION__][$category][$column][$jobID] = $display;

	return $display;
	
}



function updateInternalDataSettings($inputArray){
	
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	unset($dataArray);
	
	if ($inputArray['ID'] <= 0){
		return false;	
	}
	
	if (true){
		$dataArray['Gene_Expression_Plot_Details'] 			= array();
		
		if ($inputArray['Gene_Expression_Plot_Default']){
			$dataArray['Gene_Expression_Plot_Details']['Gene_Expression_Plot_Default'] = 1;
		} else {
			$dataArray['Gene_Expression_Plot_Details']['Gene_Expression_Plot_Default'] = 0;
			
			$dataArray['Gene_Expression_Plot_Details']['Plot_Columns'] 	= array_clean($inputArray['Plot_Columns']);
			$dataArray['Gene_Expression_Plot_Details']['groupSamples'] 	= $inputArray['groupSamples'];
			$dataArray['Gene_Expression_Plot_Details']['colorBy'] 		= $inputArray['colorBy'];
			$dataArray['Gene_Expression_Plot_Details']['shapeBy'] 		= $inputArray['shapeBy'];
			$dataArray['Gene_Expression_Plot_Details']['segregate'] 	= $inputArray['segregate'];
			$dataArray['Gene_Expression_Plot_Details']['sortBy'] 		= $inputArray['sortBy'];
			
			$dataArray['Gene_Expression_Plot_Details']['JSON'] 			= $inputArray['JSON'];	
			$dataArray['Gene_Expression_Plot_Details']['JSCode'] 		= $inputArray['JSCode'];
			
			
			$inputArray['plot_height'] = intval(abs($inputArray['plot_height']));
			if ($inputArray['plot_height'] >= 100){
				$dataArray['Gene_Expression_Plot_Details']['plot_height'] 	= intval(abs($inputArray['plot_height']));
			} else {
				$dataArray['Gene_Expression_Plot_Details']['plot_height'] 	= 0;	
			}
			
			$inputArray['plot_width'] = intval(abs($inputArray['plot_width']));
			if ($inputArray['plot_width'] >= 100){
				$dataArray['Gene_Expression_Plot_Details']['plot_width'] 	= intval(abs($inputArray['plot_width']));
			} else {
				$dataArray['Gene_Expression_Plot_Details']['plot_width'] 	= 0;
			}
		}
	
		$dataArray['Gene_Expression_Plot_Details'] = json_encode($dataArray['Gene_Expression_Plot_Details']);
	}
	
	
	if (true){
		$dataArray['Bubble_Plot_Details'] 			= array();
		
		if ($inputArray['Bubble_Plot_Details_Default']){
			$dataArray['Bubble_Plot_Details']['Bubble_Plot_Default'] = 1;
		} else {
			$dataArray['Bubble_Plot_Details']['Bubble_Plot_Details_Default'] = 0;

			$dataArray['Bubble_Plot_Details']['y-axis'] 				= $inputArray['Bubble_Plot_y-axis'];
			$dataArray['Bubble_Plot_Details']['y-axis_settings'] 		= $inputArray['Bubble_Plot_y-axis_settings'];			
			$dataArray['Bubble_Plot_Details']['colorBy'] 				= $inputArray['Bubble_Plot_colorBy'];
			$dataArray['Bubble_Plot_Details']['colorBy_settings'] 		= $inputArray['Bubble_Plot_colorBy_settings'];
			$dataArray['Bubble_Plot_Details']['marker'] 				= $inputArray['Bubble_Plot_marker'];
			$dataArray['Bubble_Plot_Details']['shapeBy'] 				= $inputArray['Bubble_Plot_shapeBy'];
			$dataArray['Bubble_Plot_Details']['subplotBy'] 				= $inputArray['Bubble_Plot_subplotBy'];

			$inputArray['Bubble_Plot_plot_height'] = intval(abs($inputArray['Bubble_Plot_plot_height']));
			if ($inputArray['Bubble_Plot_plot_height'] >= 100){
				$dataArray['Bubble_Plot_Details']['plot_height'] 	= intval(abs($inputArray['Bubble_Plot_plot_height']));
			} else {
				$dataArray['Bubble_Plot_Details']['plot_height'] 	= 0;	
			}
			
			$inputArray['Bubble_Plot_plot_width'] = intval(abs($inputArray['Bubble_Plot_plot_width']));
			if ($inputArray['Bubble_Plot_plot_width'] >= 100){
				$dataArray['Bubble_Plot_Details']['plot_width'] 	= intval(abs($inputArray['Bubble_Plot_plot_width']));
			} else {
				$dataArray['Bubble_Plot_Details']['plot_width'] 	= 0;
			}
		}
	
		$dataArray['Bubble_Plot_Details'] = json_encode($dataArray['Bubble_Plot_Details']);
	}
	
	
	
	

	$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Job'];
	$SQL = getUpdateSQLQuery($SQL_TABLE, $dataArray, $inputArray['ID']);
	

	$sql_exe_results = execSQL($SQL);

	

	return $results;
	
}


function validateCanvasXpressJSON($string){
	
	$string = trim(trim($string), ',');
	$string = trim($string);
	
	if ($string == ''){
		return false;
	}
	
	$string = str_replace(array("\n", "\t", "\r"), '', trim($string));
	
	for ($i = 1; $i <= 10; $i++){
		$string = str_replace("  ", ' ', trim($string));
	}

	$dataArray = json_decode(trim($string), true);
	

	if (array_size($dataArray) > 0){
		
		if (array_size($dataArray['afterRender']) > 0){
			$results['Result'] = 1;
			$results['Array'] = $dataArray['afterRender'];
			
			foreach ($dataArray['afterRender'] as $tempKey => $tempValue){
				$results['Preview'][] = "afterRenderObject.push( " . json_encode($tempValue) . " );";
			}
			
		} elseif (is_string($dataArray[0][0]) && is_array($dataArray[0][1])){
			$results['Result'] = 1;
			$results['Array'] = $dataArray;
			
			foreach ($dataArray as $tempKey => $tempValue){
				$results['Preview'][] = "afterRenderObject.push( " . json_encode($tempValue) . " );";
			}
		} else {
			$string 	= trim(trim($string), ',');
			$dataArray  = json_decode('[' . $string . ']', true);
			
			if (is_string($dataArray[0][0]) && is_array($dataArray[0][1])){
				$results['Result'] = 1;
				$results['Array'] = $dataArray;
				
				foreach ($dataArray as $tempKey => $tempValue){
					$results['Preview'][] = "afterRenderObject.push( " . json_encode($tempValue) . " );";
				}
			}
			
		}

			
		if (!$results['Result']){
			$results['Result'] = 0;	
			
			$results['Error'] = "The expected format should be a two-dimensional array, but your code is only a one-dimensional array. Please make sure that you copy the JSON code after the attribute: 'afterRender'."; 
			
		}
		
	} else {
		$results['Result'] = 0;	
		
		$count_open_bracket = substr_count($string, '[');
		$count_close_bracket = substr_count($string, ']');
		
		if ($count_open_bracket != $count_close_bracket){
			$results['Error'] = "The number of opening bracket( [ ) does not equal to closing bracket( ] )"; 
		} else {
			$results['Error'] = "The JSON code cannot be parsed."; 
		}
	}
	
	return $results;
}

?>