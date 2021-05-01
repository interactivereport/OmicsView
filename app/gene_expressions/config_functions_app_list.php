<?php

function createList($inputArray){
	
	global $APP_CONFIG;

	if ($inputArray['Name'] == ''){
		$hasError = 1;
		$errorMessages[] = "The list name is required.";
	} else {
	
		if (check_list_name_exist($inputArray['Name'], $inputArray['Category'], $inputArray['ID'])){
			$hasError = 1;
			$errorMessages[] = "The name of the list has been taken. Please try using a different name.";
		} else {
			$intputOrg				= $inputArray['Input'];
			$inputArray['Input'] 	= splitData($inputArray['Input']);	
			if (array_size($inputArray['Input']) <= 0){
				$hasError = 1;
				$errorMessages[] = "The content of the list cannot be empty.";
			} else {
			
				$sql_table 			= $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['Table'];
				$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['Column_Human'];
				$sql_column_unique 	= $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['Column_Internal'];
				
				$getListInputSummary = getListInputSummary($intputOrg, $inputArray['Category']);
				
				$sql_results = $getListInputSummary['Raw'];
				
				if (array_size($sql_results) <= 0){
					$hasError = 1;
					$errorMessages[] = $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['No_Match_Message'];
				} else {
					$sql_results 	= array_unique($sql_results);
					
					unset($dataArray);
					$dataArray['User_ID'] 	= intval($APP_CONFIG['User_Info']['ID']);
					$dataArray['Name'] 		= $inputArray['Name'];
					$dataArray['Category'] 	= $inputArray['Category'];
					$dataArray['Table'] 	= $sql_table;
					$dataArray['Date'] 		= date("Y-m-d");
					
					$dataArray['Items'][$sql_column_human] 	= array_values($sql_results);
					$dataArray['Items'][$sql_column_unique] = array_keys($sql_results);
					$dataArray['Items']['Combined'] 		= $sql_results;
					$dataArray['Items']		= json_encode($dataArray['Items'], true);
					
					$dataArray['Count'] 	= array_size($sql_results);
					$dataArray['Notes'] 	= $inputArray['Notes'];
					
					
					$sql = getInsertSQLQuery($APP_CONFIG['Table']['List'], $dataArray);
		
					$sql_exe_results = execSQL($sql);
					
					if (!$sql_exe_results){
						$hasError = 1;
						$errorMessages[] = "Database error. Please contact us for details.";
					} else {
						
						$results['ID'] = getLastInsertID();	
					}
				}
			}
		}
	}
	
	if ($hasError){
		$results['Error'] = 1;
		$results['Message'] = $errorMessages;
	} else {
		$results['Error'] = 0;
		
	}
	
	return $results;
}



function updateList($inputArray, $ID){
	
	global $APP_CONFIG;
	
	$ID = abs(intval($ID));
	
	if ($ID <= 0){
		return createList($inputArray);
	}
	

	if ($inputArray['Name'] == ''){
		$hasError = 1;
		$errorMessages[] = "The list name is required.";
	} else {
		
		
		if (check_list_name_exist($inputArray['Name'], $inputArray['Category'], $ID)){
			$hasError = 1;
			$errorMessages[] = "The name of the list has been taken. Please try using a different name.";
		} else {
			$intputOrg				= $inputArray['Input'];
			$inputArray['Input'] 	= splitData($inputArray['Input']);	
			if (array_size($inputArray['Input']) <= 0){
				$hasError = 1;
				$errorMessages[] = "The content of the list cannot be empty.";
			} else {
	
				$sql_table 			= $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['Table'];
				$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['Column_Human'];
				$sql_column_unique 	= $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['Column_Internal'];
				
				$getListInputSummary = getListInputSummary($intputOrg, $inputArray['Category']);
				
				$sql_results = $getListInputSummary['Raw'];
				
				if (array_size($sql_results) <= 0){
					$hasError = 1;
					$errorMessages[] = $APP_CONFIG['APP']['List_Category'][$inputArray['Category']]['No_Match_Message'];
				} else {
					
					$sql_results 	= array_unique($sql_results);
					
					unset($dataArray);
					$dataArray['Name'] 		= $inputArray['Name'];
					$dataArray['Table'] 	= $sql_table;
					
					$dataArray['Items'][$sql_column_human] 	= array_values($sql_results);
					$dataArray['Items'][$sql_column_unique] = array_keys($sql_results);
					$dataArray['Items']['Combined'] 		= $sql_results;
					$dataArray['Items']		= json_encode($dataArray['Items'], true);
					
					$dataArray['Count'] 	= array_size($sql_results);
					$dataArray['Notes'] 	= $inputArray['Notes'];
					
					$sql = getUpdateSQLQuery($APP_CONFIG['Table']['List'], $dataArray, $ID, "(`User_ID` = {$APP_CONFIG['User_Info']['ID']})");
					
		
					$sql_exe_results = execSQL($sql);
					
					if (!$sql_exe_results){
						$hasError = 1;
						$errorMessages[] = "Database error. Please contact us for details.";
					} else {
						
						$results['ID'] = $ID;
					}
				
				}
			}
		}
	}
	
	if ($hasError){
		$results['Error'] = 1;
		$results['Message'] = $errorMessages;
	} else {
		$results['Error'] = 0;
	}
	
	return $results;
}


function removeItemFromList($ID, $items){
	
	global $APP_CONFIG;
	
	$ID = abs(intval($ID));
	
	$list = get_list_record_by_list_id($ID);
	
	if (array_size($list) <= 0){
		return false;
	}
	
	if (!is_array($items)){
		$items = array($items);	
	}


	$sql_table 			= $APP_CONFIG['APP']['List_Category'][$list['Category']]['Table'];
	$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$list['Category']]['Column_Human'];
	$sql_column_unique 	= $APP_CONFIG['APP']['List_Category'][$list['Category']]['Column_Internal'];	
	

	
	unset($dataArray);
	
	$dataArray['Items'] = $list['Items'];
	
	foreach($items as $tempKey => $tempValue){
		unset($dataArray['Items']['Combined'][$tempValue]);
	}

	
	$dataArray['Items'][$sql_column_human] 	= array_values($dataArray['Items']['Combined']);
	$dataArray['Items'][$sql_column_unique] = array_keys($dataArray['Items']['Combined']);
	$dataArray['Count'] 					= array_size($dataArray['Items']['Combined']);

	
	if ($dataArray['Count'] >= 1){
		$dataArray['Items']		= json_encode($dataArray['Items'], true);

		
		$sql = getUpdateSQLQuery($APP_CONFIG['Table']['List'], $dataArray, $ID, "(`User_ID` = {$APP_CONFIG['User_Info']['ID']})");
		$sql_exe_results = execSQL($sql);
	}


	
	return false;
}


function getListInputSummary($input, $category){
	
	global $APP_CONFIG;
	
	
	
	$sql_table 			= $APP_CONFIG['APP']['List_Category'][$category]['Table'];
	$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Human'];
	$sql_column_human2 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Human2'];
	$sql_column_unique 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Internal'];
	
	$input				= splitData($input);	

	if ($category == 'Gene'){
		$sql_results = search_gene_indexes($input);
		
		$sql_results = array_flip($sql_results);
			
	} else {

		unset($valueString);
		foreach($input as $tempKey => $tempValue){
			$valueString[] = "'" . addslashes($tempValue) . "'";
		}
		
		$valueString 	= implode(', ', $valueString);
		if ($sql_column_human2 == ''){
			$SQL		= "SELECT {$sql_column_unique}, {$sql_column_human} FROM {$sql_table} WHERE {$sql_column_human} IN ({$valueString}) ORDER BY FIELD({$sql_column_human}, {$valueString})";
		} else {
			$SQL		= "SELECT {$sql_column_unique}, {$sql_column_human} FROM {$sql_table} WHERE {$sql_column_human} IN ({$valueString}) ORDER BY {$sql_column_human2}, FIELD({$sql_column_human}, {$valueString})";		
		}
		$sql_results 	= getSQL($SQL, 'GetAssoc', $sql_table);
		$sql_results 	= array_unique($sql_results);
		
		
		if (internal_data_enable()){
			$sql_table 			= $APP_CONFIG['APP']['List_Category'][$category]['Table_User'];
			
			if ($APP_CONFIG['APP']['List_Category'][$category]['Table'] != $APP_CONFIG['APP']['List_Category'][$category]['Table_User']){
				$projectIndexes = internal_data_get_accessible_project(0, 0);
				$projectIndexes = implode(',', array_keys($projectIndexes));
				
				if ($projectIndexes != ''){
					if ($sql_column_human2 == ''){
						$SQL		= "SELECT {$sql_column_unique}, {$sql_column_human} FROM {$sql_table} WHERE ({$sql_column_human} IN ({$valueString})) AND (`ProjectIndex` IN ({$projectIndexes})) ORDER BY FIELD({$sql_column_human}, {$valueString})";
					} else {
						$SQL		= "SELECT {$sql_column_unique}, {$sql_column_human} FROM {$sql_table} WHERE {$sql_column_human} IN ({$valueString}) AND (`ProjectIndex` IN ({$projectIndexes})) ORDER BY {$sql_column_human2}, FIELD({$sql_column_human}, {$valueString})";		
					}
									
					$sql_results2 	= getSQL($SQL, 'GetAssoc', $sql_table);
					$sql_results2 	= array_unique($sql_results2);
					
					foreach($sql_results2 as $tempKey => $tempValue){
						$sql_results[$tempKey] = $tempValue;
					}
					
				}
				
				
			}
		}
	}
	
	$results['Raw'] 			= $sql_results;

	$results['Input'] 			= $input;
	$results['Input_Count'] 	= array_size($input);
	$results['Output'] 			= array_values($sql_results);
	$results['Output_Count'] 	= array_size($results['Output']);

	$results['Missing']			= array_udiff($results['Input'], $results['Output'], 'strcasecmp');
	$results['Missing_Count']	= array_size($results['Missing']);
	



	return $results;
	
}


function getListRecords($ID){
	
	global $APP_CONFIG;
	
	$dataArray 	= get_list_record_by_list_id($ID);
	
	
	
	if (array_size($dataArray) <= 0) return false;
	
	$category	= $dataArray['Category'];
	
	
	
	$summary 	= getListInputSummary($dataArray['Items']['Combined'], $dataArray['Category']);
	$indexes 	= implode(',', array_keys($summary['Raw']));
	
	$sql_table 			= $APP_CONFIG['APP']['List_Category'][$category]['Table'];
	$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Human'];
	$sql_column_human2 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Human2'];
	$sql_column_unique 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Internal'];
	
	$getTableColumnPreferences 	= getTableColumnPreferences($category);
	$columns = '`' . implode('`, `', array_column($getTableColumnPreferences, 'SQL')) . '`';
	$SQL		= "SELECT `{$sql_column_unique}`, `{$sql_column_human}`, {$columns} FROM `{$sql_table}` WHERE `{$sql_column_unique}` IN ({$indexes}) ORDER BY FIELD(`{$sql_column_unique}`, {$indexes})";		
	
	
	$sql_results 	= getSQL($SQL, 'GetAssoc', $sql_table);
	
	
	
	foreach($sql_results as $currentIndex => $currentRecord){
		$sql_results[$currentIndex][$sql_column_unique] = $currentIndex;
	}
	
	
	
	if (internal_data_enable()){
		
		if (array_size($sql_results) < $dataArray['Count']){
			$sql_table 	= $APP_CONFIG['APP']['List_Category'][$category]['Table_User'];
			$SQL		= "SELECT * FROM {$sql_table} WHERE {$sql_column_unique} IN ({$indexes}) ORDER BY FIELD(`{$sql_column_unique}`, {$indexes})";	
			$sql_results2 	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($sql_results2 as $currentIndex => $currentRecord){
				
				$sql_results[$currentIndex][$sql_column_human] = $currentRecord[$sql_column_human];
				
				foreach($getTableColumnPreferences  as $tempKey => $tempValue){
					
					$currentSQL = $tempValue['SQL'];
					
					if ($currentSQL == $sql_column_unique){
						$sql_results[$currentIndex][$currentSQL] = $currentIndex;
					} else {
						$sql_results[$currentIndex][$currentSQL] = $currentRecord[$currentSQL];
					}
				}
				
				
			}
			
		}
	}
	
	
	
	return $sql_results;
}


?>