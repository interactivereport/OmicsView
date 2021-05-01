<?php


function search_genes($names = NULL, $sql_fields = NULL){
	
	if (!is_array($names)){
		$names = general_split_data($names);
	}
	
	$names = general_array_clean($names);
	
	if (sizeof($names) <= 0) return false;

	$conn 			= bxaf_get_app_db_connection();
	
	//*******************
	// Search Gene Index
	//*******************
	unset($valueString);
	foreach($names as $tempKey => $geneName){
		$valueString[] = "'" . addslashes(trim($geneName)) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	
	if ($sql_fields == ''){
		$sql_fields = '`GeneIndex`, `GeneName`';	
	}
	
	$SQL 		= "SELECT {$sql_fields} FROM `GeneAnnotation` WHERE `GeneName` IN ({$valueString}) ORDER BY FIELD(GeneName, {$valueString})";

	$results 	= $conn->GetAssoc($SQL);
	
	return $results;
	
}


function get_data_type_by_name($category = NULL, $name = NULL){
	
	$search_one_record_by_name = search_one_record_by_name($category, $name, 'GetRow');
	
	if (strpos($category, 'comparison') === 0){
		$defaultColumn	= 'ComparisonIndex';
	} elseif (strpos($category, 'project') === 0){
		$defaultColumn	= 'ProjectIndex';
	} elseif (strpos($category, 'sample') === 0){
		$defaultColumn	= 'SampleIndex';
	} elseif (strpos($category, 'dataset') === 0){
		$defaultColumn	= 'DatasetIndex';
	} else {
		return false;	
	}
	
	if (!isset($search_one_record_by_name[$defaultColumn])){
		return false;	
	}
	
	$internal_data_is_public = internal_data_is_public($search_one_record_by_name[$defaultColumn]);
	
	if ($internal_data_is_public){
		return 'public';	
	} else {
		return 'private';	
	}

}


function search_one_record_by_name($category = NULL, $name = NULL, $method = 'GetAssoc', $sql_column = '*'){
	
	$name		= addslashes(trim($name));
	
	if ($name == '') return false;
	
	
	$category 	= trim(strtolower($category));
	
	if (strpos($category, 'comparison') === 0){
		$table_pubic 	= 'Comparisons';
		$table_private 	= 'App_User_Data_Comparisons';
		$column		 	= 'ComparisonID';
		$column2		= 'ComparisonID_Original';
		$defaultColumn	= 'ComparisonIndex';
	} elseif (strpos($category, 'project') === 0){
		$table_pubic 	= 'Projects';
		$table_private 	= 'App_User_Data_Projects';
		$column		 	= 'ProjectID';
		$column2		= 'ProjectID_Original';
		$defaultColumn	= 'ProjectIndex';
	} elseif (strpos($category, 'sample') === 0){
		$table_pubic 	= 'Samples';
		$table_private 	= 'App_User_Data_Samples';
		$column		 	= 'SampleID';
		$column2		= 'SampleID_Original';
		$defaultColumn	= 'SampleIndex';
	} elseif (strpos($category, 'dataset') === 0){
		$table_pubic 	= 'App_User_Data_Datasets';
		$table_private 	= 'App_User_Data_Datasets';
		$column		 	= 'DatasetID';
		$column2		= 'DatasetID_Original';
		$defaultColumn	= 'DatasetIndex';
	} elseif (strpos($category, 'gene') === 0){
		$table_pubic 	= 'GeneCombined';
		$table_private 	= '';
		$column		 	= 'GeneName';
		$column2		= 'GeneID';
		$defaultColumn	= 'GeneIndex';
	} else {
		return false;	
	}
	
	
	if (($method == 'GetOne') && ($sql_column == '*')){
		$sql_column = "`{$defaultColumn}`";
	}
	
	
	$conn 	= bxaf_get_app_db_connection();
	
	
	if ($table_pubic != ''){
		
		$SQL = "SELECT {$sql_column} FROM `{$table_pubic}` WHERE `{$column}` = '{$name}'";
		
		if ($method == 'GetAssoc'){
			$result = $conn->GetAssoc($SQL);
			$is_array = 1;
		} elseif ($method == 'GetRow'){
			$result = $conn->GetRow($SQL);
			$is_array = 1;
		} elseif ($method == 'GetAll'){
			$result = $conn->GetAll($SQL);
			$is_array = 1;
		} elseif ($method == 'GetArray'){
			$result = $conn->GetArray($SQL);
			$is_array = 1;
		} elseif ($method == 'GetCol'){
			$result = $conn->GetCol($SQL);
			$is_array = 1;
		} elseif ($method == 'GetOne'){
			$result = $conn->GetOne($SQL);
			$is_array = 0;
		}
		
		if ($is_array){
			if (general_array_size($result) > 0){
				return $result;	
			}
		} elseif ($result !== FALSE){
			return $result;
		}
	}

	

	if ($table_private != ''){		
		$privateProjectIDs = internal_data_get_accessible_project();
		$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
		
		if ($privateProjectIDs == ''){
			return false;
		}
		
		if ($column2 == ''){
			$SQL = "SELECT {$sql_column} FROM `{$table_private}` WHERE (`{$column}` = '{$name}') AND (`ProjectIndex` IN ({$privateProjectIDs}))";
		} else {
			$SQL = "SELECT {$sql_column} FROM `{$table_private}` WHERE ((`{$column}` = '{$name}') OR (`{$column2}` = '{$name}')) AND (`ProjectIndex` IN ({$privateProjectIDs}))";
		}

		if ($method == 'GetAssoc'){
			$result = $conn->GetAssoc($SQL);
		} elseif ($method == 'GetRow'){
			$result = $conn->GetRow($SQL);
		} elseif ($method == 'GetAll'){
			$result = $conn->GetAll($SQL);
		} elseif ($method == 'GetArray'){
			$result = $conn->GetArray($SQL);
		} elseif ($method == 'GetCol'){
			$result = $conn->GetCol($SQL);
		} elseif ($method == 'GetOne'){
			$result = $conn->GetOne($SQL);
		}
		
		return $result;

	}


	return false;
	
	
}



function search_comparisons($names = NULL, $sql_fields = NULL, $data_source = NULL, $internal_project_indexes = NULL, $method = 'GetAssoc'){
	
	if (!is_array($names)){
		$names = general_split_data($names);
	}
	
	$names = general_array_clean($names);
	
	if (sizeof($names) <= 0) return false;
		
	internal_data_sanitize_user_input($data_source, $internal_project_indexes);

	$conn 			= bxaf_get_app_db_connection();
	
	//*******************
	// Search Gene Index
	//*******************
	unset($valueString);
	foreach($names as $tempKey => $geneName){
		$valueString[] = "'" . addslashes(trim($geneName)) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	
	if ($sql_fields == ''){
		$sql_fields = '`ComparisonIndex`, `ComparisonID`';	
	}
	
	unset($results);
	if ($data_source['public'] != ''){
		$SQL		= "SELECT {$sql_fields} FROM `Comparisons` WHERE `ComparisonID` IN ({$valueString}) ORDER BY FIELD(ComparisonID, {$valueString})";
		if ($method == 'GetAssoc'){
			$results 	= $conn->GetAssoc($SQL);
		} else {
			$results 	= $conn->GetArray($SQL);
		}
	}
	
	
	if ($data_source['private'] != ''){
		$internal_project_indexes = implode(',', $internal_project_indexes);
		
		if ($internal_project_indexes != ''){
			$SQL		= "SELECT {$sql_fields} FROM `App_User_Data_Comparisons` WHERE `ComparisonID` IN ({$valueString}) AND (`ProjectIndex` IN ({$internal_project_indexes})) ORDER BY FIELD(ComparisonID, {$valueString})";
			
			if ($method == 'GetAssoc'){
				$temp 	= $conn->GetAssoc($SQL);
			} else {
				$temp 	= $conn->GetArray($SQL);
			}
			
			foreach($temp as $tempKey => $tempValue){
				if ($method == 'GetAssoc'){
					$results[$tempKey] = $tempValue;	
				} else {
					$results[] = $tempValue;	
				}
			}
		}
		
	}

	
	return $results;
	
	
}



function search_comparisons_by_index($indexes = NULL, $sql_fields = NULL, $data_source = NULL, $internal_project_indexes = NULL){
	
	if (!is_array($indexes)){
		$indexes = general_split_data($indexes);
	}
	
	$indexes = general_array_clean($indexes);
	
	if (sizeof($indexes) <= 0) return false;
	
	
	internal_data_sanitize_user_input($data_source, $internal_project_indexes);

	$conn 			= bxaf_get_app_db_connection();
	
	//*******************
	// Search Gene Index
	//*******************
	unset($valueString);
	foreach($indexes as $tempKey => $index){
		$valueString[] = "'" . addslashes(trim($index)) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	
	if ($sql_fields == ''){
		$sql_fields = '`ComparisonIndex`, `ComparisonID`';	
	}
	
	unset($results);
	if ($data_source['public'] != ''){
		$SQL		= "SELECT {$sql_fields} FROM `Comparisons` WHERE `ComparisonIndex` IN ({$valueString})";
		$results 	= $conn->GetAssoc($SQL);
	}
	
	
	if ($data_source['private'] != ''){
		$internal_project_indexes = implode(',', $internal_project_indexes);
		$SQL		= "SELECT {$sql_fields} FROM `App_User_Data_Comparisons` WHERE `ComparisonIndex` IN ({$valueString}) AND (`ProjectIndex` IN ({$internal_project_indexes}))";

		
		$temp 		= $conn->GetAssoc($SQL);
		
		foreach($temp as $tempKey => $tempValue){
			$results[$tempKey] = $tempValue;	
		}
		
	}
	
	return $results;
	
	
}

function get_sample_TPM($indexes = ''){
	$conn 	= bxaf_get_app_db_connection();
	
	$sources = internal_data_split_multiple_data_by_source($indexes);
	
	$indexString = implode(',', $sources['public']);
	if ($indexString != ''){
		$SQL = "SELECT `SampleIndex`, `TPMScalingFactor` FROM `Samples` WHERE (`SampleIndex` IN ({$indexString})) AND (`TPMScalingFactor` != '.')";
	} else {
		$SQL = "SELECT `SampleIndex`, `TPMScalingFactor` FROM `Samples` WHERE (`TPMScalingFactor` != '.')";
	}

	return $conn->GetAssoc($SQL);
}


//search by indexes, can be from public or private dataset
function get_multiple_record($category = NULL, $indexes = '', $method = 'GetAssoc', $sql_column = '*', $all_records = 0){
	
	global $BAXF_CACHE;
	
	$category = trim(strtolower($category));
	
	if (strpos($category, 'comparison') === 0){
		$table_pubic 	= 'Comparisons';
		$table_private 	= 'App_User_Data_Comparisons';
		$column		 	= 'ComparisonIndex';
	} elseif (strpos($category, 'project') === 0){
		$table_pubic 	= 'Projects';
		$table_private 	= 'App_User_Data_Projects';
		$column		 	= 'ProjectIndex';
	} elseif (strpos($category, 'sample') === 0){
		$table_pubic 	= 'Samples';
		$table_private 	= 'App_User_Data_Samples';
		$column		 	= 'SampleIndex';
	} elseif (strpos($category, 'dataset') === 0){
		$table_pubic 	= 'App_User_Data_Datasets';
		$table_private 	= 'App_User_Data_Datasets';
		$column		 	= 'DatasetIndex';
	} elseif (strpos($category, 'gene') === 0){
		$table_pubic 	= 'GeneCombined';
		$column		 	= 'GeneIndex';
	} else {
		return false;	
	}
	
	
	
	if ($indexes === ''){
		$all_records = 1;
	}
	
	unset($SQLs);
	if ($all_records == 1){
		$SQLs[]	= "SELECT {$sql_column} FROM `{$table_pubic}`";


		if ($table_private != ''){		
			$privateProjectIDs = internal_data_get_accessible_project();
			$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
			
			if ($privateProjectIDs != ''){
				$SQLs[]	= "SELECT {$sql_column} FROM `{$table_private}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
			}
		}
	} else {
		
		$sources = internal_data_split_multiple_data_by_source($indexes);	
		
		$indexString = implode(',', $sources['public']);
		if ($indexString != ''){
			$SQLs[]	= "SELECT {$sql_column} FROM `{$table_pubic}` WHERE `{$column}` IN ({$indexString})";
		}
		
		if ($table_private != ''){
			$indexString = implode(',', $sources['private']);
			if ($indexString != ''){
				$SQLs[]	= "SELECT {$sql_column} FROM `{$table_private}` WHERE `{$column}` IN ({$indexString})";
			}
		}
	}
	
	
	
	
	$cacheKey = md5(json_encode($SQLs) . '::' . $method);

	if (isset($BAXF_CACHE[__FUNCTION__][$cacheKey])){
		return $BAXF_CACHE[__FUNCTION__][$cacheKey];
	}

	

	$conn 	= bxaf_get_app_db_connection();
	unset($results);
	foreach($SQLs as $SQLKey => $SQL){
		
		if ($method == 'GetAssoc'){
			$records = $conn->GetAssoc($SQL);
		} elseif ($method == 'GetRow'){
			$records = $conn->GetRow($SQL);
		} elseif ($method == 'GetAll'){
			$records = $conn->GetAll($SQL);
		} elseif ($method == 'GetArray'){
			$records = $conn->GetArray($SQL);
		} elseif ($method == 'GetCol'){
			$records = $conn->GetCol($SQL);
		} elseif ($method == 'GetOne'){
			$records = $conn->GetOne($SQL);
			return $records;
		}
		
		if (!isset($results)){
			$results = $records;	
		} else {
			
			if ($method == 'GetAssoc'){
				foreach($records as $recordKey => $recordValue){
					$results[$recordKey] = $recordValue;	
				}	
			} else {
				foreach($records as $recordKey => $recordValue){
					$results[] = $recordValue;	
				}
			}
		}
	}
	
	$BAXF_CACHE[__FUNCTION__][$cacheKey] = $results;

	return $results;
	
}

function search_all_records($category = NULL, $sql_column = '*', $conditions = '(1)', $method = 'GetAssoc'){
	
	$category = trim(strtolower($category));
	
	if (strpos($category, 'comparison') === 0){
		$table_pubic 	= 'Comparisons';
		$table_private 	= 'App_User_Data_Comparisons';
		$column		 	= 'ComparisonIndex';
	} elseif (strpos($category, 'project') === 0){
		$table_pubic 	= 'Projects';
		$table_private 	= 'App_User_Data_Projects';
		$column		 	= 'ProjectIndex';
	} elseif (strpos($category, 'sample') === 0){
		$table_pubic 	= 'Samples';
		$table_private 	= 'App_User_Data_Samples';
		$column		 	= 'SampleIndex';
	} elseif (strpos($category, 'dataset') === 0){
		$table_pubic 	= 'App_User_Data_Datasets';
		$table_private 	= 'App_User_Data_Datasets';
		$column		 	= 'DatasetIndex';
	} elseif (strpos($category, 'gene') === 0){
		$table_pubic 	= 'GeneCombined';
		$column		 	= 'GeneIndex';
	} else {
		return false;	
	}
	
	$conn 	= bxaf_get_app_db_connection();
	

	unset($SQLs);
	$SQLs[]	= "SELECT {$sql_column} FROM `{$table_pubic}` WHERE {$conditions}";

	if ($table_private != ''){		
		$privateProjectIDs = internal_data_get_accessible_project();
		$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
		
		if ($privateProjectIDs != ''){
			$SQLs[]	= "SELECT {$sql_column} FROM `{$table_private}` WHERE (`ProjectIndex` IN ({$privateProjectIDs})) AND ({$conditions})";
		}
	}
	
	unset($results);
	foreach($SQLs as $SQLKey => $SQL){
		
		if ($method == 'GetAssoc'){
			$records = $conn->GetAssoc($SQL);
		} elseif ($method == 'GetRow'){
			$records = $conn->GetRow($SQL);
		} elseif ($method == 'GetAll'){
			$records = $conn->GetAll($SQL);
		} elseif ($method == 'GetArray'){
			$records = $conn->GetArray($SQL);
		} elseif ($method == 'GetCol'){
			$records = $conn->GetCol($SQL);
		} elseif ($method == 'GetOne'){
			$records = $conn->GetOne($SQL);
			return $records;
		}
		
		if (!isset($results)){
			$results = $records;	
		} else {
			
			if ($method == 'GetAssoc'){
				foreach($records as $recordKey => $recordValue){
					$results[$recordKey] = internal_data_transform_one_data($table_pubic, $recordValue);
				}	
			} else {
				foreach($records as $recordKey => $recordValue){
					$results[] = internal_data_transform_one_data($table_pubic, $recordValue);	
				}
			}
		}
	}

	return $results;
	
}

function get_one_record_by_id($category = NULL, $recordIndex = NULL){
	
	$category = trim(strtolower($category));
	
	$recordIndex = intval($recordIndex);
	
	if (strpos($category, 'comparison') === 0){
		$table 			= 'App_User_Data_Comparisons_Combined';
		$column		 	= 'ComparisonIndex';
	} elseif (strpos($category, 'project') === 0){
		$table 			= 'App_User_Data_Projects_Combined';
		$column		 	= 'ProjectIndex';
	} elseif (strpos($category, 'sample') === 0){
		$table 			= 'App_User_Data_Samples_Combined';
		$column		 	= 'SampleIndex';
	} elseif (strpos($category, 'dataset') === 0){
		$table 			= 'App_User_Data_Datasets';
		$column		 	= 'DatasetIndex';
	} elseif (strpos($category, 'gene') === 0){
		$table 			= 'GeneCombined';
		$column		 	= 'GeneIndex';
	} else {
		return false;	
	}
	
	$conn 	= bxaf_get_app_db_connection();

	$SQL = "SELECT * FROM `{$table}` WHERE (`{$column}` = '{$recordIndex}')";
	
	$results = $conn->GetRow($SQL);
	

	return $results;
	
}

function get_one_record_by_index($category = NULL, $recordIndex = NULL){
	return get_one_record_by_id($category, $recordIndex);
}

?>