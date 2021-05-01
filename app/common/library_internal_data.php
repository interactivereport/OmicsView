<?php

//$type
//0: ID, Title
//1: All

//$activeOnly
//1: return status = 1 only
//0: return all status


//$platformType
//Microarray or RNA-Seq
function internal_data_get_accessible_project($type = 0, $activeOnly = 1, $platformType = ''){
	
	if (!internal_data_enable()){
		return false;	
	}
	
	global $BAXF_CACHE, $BXAF_CONFIG, $APP_GLOBAL;
	
	if (isset($BAXF_CACHE[__FUNCTION__][$type][$activeOnly][$platformType])){
		return $BAXF_CACHE[__FUNCTION__][$type][$activeOnly][$platformType];
	} else {
	}

	$conn 			= bxaf_get_app_db_connection();

	$type 			= intval($type);
	$userID			= intval($_SESSION['User_Info']['ID']);
	$platformType	= addslashes(trim($platformType));
	
	
	if ($type == 0){
		$sql_fields = " `ProjectIndex`, `ProjectID`";	
	} else {
		$sql_fields = "*";	
	}
	
	if (general_is_admin_user() || ($BXAF_CONFIG['API'])  || ($APP_GLOBAL['API'])){
		
		
		$SQL_CONDITIONS = array();
		$SQL_CONDITIONS[]		= '(1)';
		
		if ($activeOnly){
			$SQL_CONDITIONS[] = "(`Status` = 1)";
		}
		
		if ($platformType != ''){
			$SQL_CONDITIONS[] = "(`Internal_Platform_Type` = '{$platformType}')";
		}
		
		$SQL_CONDITIONS = implode(' AND ', $SQL_CONDITIONS);
		
		$sql = "SELECT {$sql_fields} FROM `App_User_Data_Projects` WHERE {$SQL_CONDITIONS}  ORDER BY `ProjectID` ASC";
		$results = $conn->GetAssoc($sql);
	} else {

		$sql 		= "SELECT `ProjectIndex` FROM `App_User_Data_Projects` WHERE (`User_ID` = {$userID}) OR (`Permission` = 1)";

		$projectIndexes1 = $conn->GetCol($sql);
		$projectIndexes1 = array_filter($projectIndexes1, 'trim');
		$projectIndexes1 = array_unique($projectIndexes1);
		
		
		foreach($projectIndexes1 as $tempKey => $tempValue){
			$projectIndexes[] = $tempValue;
		}
		
		foreach($projectIndexes2 as $tempKey => $tempValue){
			$projectIndexes[] = $tempValue;
		}
		

		$projectIndexes = array_filter($projectIndexes, 'trim');
		$projectIndexes = array_unique($projectIndexes);
		$projectIndexes = implode(',', $projectIndexes);
		
		
		if ($projectIndexes != ''){
			
			$SQL_CONDITIONS = array();
			$SQL_CONDITIONS[]		= "(`ProjectIndex` IN ({$projectIndexes}))";
			
			if ($activeOnly){
				$SQL_CONDITIONS[] = "(`Status` = 1)";
			}
			
			if ($platformType != ''){
				$SQL_CONDITIONS[] = "(`Internal_Platform_Type` = '{$platformType}')";
			}
			
			$SQL_CONDITIONS = implode(' AND ', $SQL_CONDITIONS);
		
			$sql = "SELECT {$sql_fields} FROM `App_User_Data_Projects` WHERE {$SQL_CONDITIONS}  ORDER BY `ProjectID` ASC";
		
			$results = $conn->GetAssoc($sql);
		}
		
	}
	
	$BAXF_CACHE[__FUNCTION__][$type][$activeOnly][$platformType] = $results;
	
	return $results;
}


function internal_data_get_accessible_jobIDs(){
	
	if (!internal_data_enable()){
		return false;	
	}
	
	global $BAXF_CACHE;
	
	if (isset($BAXF_CACHE[__FUNCTION__])){
		return $BAXF_CACHE[__FUNCTION__];
	}

	$conn 			= bxaf_get_app_db_connection();

	$type 			= intval($type);
	$userID			= intval($_SESSION['User_Info']['ID']);
	
	
	
	if (general_is_admin_user()){
		
		$sql = "SELECT `ID` FROM `App_User_Data_Job`";
		$results = $conn->GetCol($sql);

		
	} else {

		$sql 		= "SELECT `ID` FROM `App_User_Data_Job` WHERE (`User_ID` = '{$userID}') OR (`Permission` = 1)";

		$jobIDs1 = $conn->GetCol($sql);
		$jobIDs1 = array_filter($jobIDs1, 'trim');
		$jobIDs1 = array_unique($jobIDs1);


		foreach($jobIDs1 as $tempKey => $tempValue){
			$jobIDs[] = $tempValue;
		}
		
		foreach($jobIDs2 as $tempKey => $tempValue){
			$jobIDs[] = $tempValue;
		}
		

		$jobIDs = array_filter($jobIDs, 'trim');
		$jobIDs = array_unique($jobIDs);
		$jobIDs = implode(',', $jobIDs);
		
		
		if ($jobIDs != ''){
			
			$SQL_CONDITIONS = array();
			$SQL_CONDITIONS[]		= "(`ID` IN ({$jobIDs}))";
			
			$SQL_CONDITIONS = implode(' AND ', $SQL_CONDITIONS);
		
			$sql = "SELECT `ID` FROM `App_User_Data_Job` WHERE {$SQL_CONDITIONS}";
		
			$results = $conn->GetCol($sql);
		}
		
	}
	
	
	$BAXF_CACHE[__FUNCTION__] = $results;
	
	return $results;
}

function has_internal_data($platformType = NULL){
	
	if (!internal_data_enable()){
		return false;	
	}
	
	$internal_data_get_accessible_project = internal_data_get_accessible_project(0, 1, $platformType);
	
	if (is_array($internal_data_get_accessible_project) && sizeof($internal_data_get_accessible_project) > 0){
		return true;	
	} else {
		return false;	
	}
	
}

function internal_data_enable(){
	global $BXAF_CONFIG;
	
	return $BXAF_CONFIG['APP_SWITCH']['Internal_Data'];
}
	


function internal_data_print_form_html($dataArray = NULL){
	
	unset($results);
	
	if (has_public_comparison_data()){
		$tempID			= 'data_source_public';
		$tempValue 		= 'public';
		$tempName		= 'data_source[]';
		$tempDisplay 	= 'Omicsoft Data';
		
		if (!isset($dataArray['data_source'])){
			$tempChecked = 'checked';	
		} elseif (in_array($tempValue, $dataArray['data_source'])){
			$tempChecked = 'checked';
		} else {
			$tempChecked = '';
		}
		$results .=  "<div class='form-check-inline'>";
			$results .=  "<label class='form-check-label' for='{$tempID}'>";
				$results .=  "<input class='form-check-input data_source' type='checkbox' name='{$tempName}' id='{$tempID}' value='{$tempValue}' {$tempChecked}>";
				$results .=  '&nbsp;' . $tempDisplay . '&nbsp;';
			$results .=  "</label>";
		$results .=  "</div>";
	}
	
	
	if (true){
		$tempID			= 'data_source_private';
		$tempValue 		= 'private';
		$tempName		= 'data_source[]';
		$tempDisplay 	= 'Internal Data';
		
		if (!isset($dataArray['data_source'])){
			$tempChecked 	= 'checked';
			$tempStyle		= '';
		} elseif (in_array($tempValue, $dataArray['data_source'])){
			$tempChecked 	= 'checked';
			$tempStyle		= '';
		} else {
			$tempChecked = '';
			$tempStyle		= 'display:none;';
		}
		$results .=  "<div class='form-check-inline' id='data_source_private_wrapper'>";
			$results .=  "<label class='form-check-label' for='{$tempID}'>";
			
				$results .=  "<input class='form-check-input data_source' type='checkbox' name='{$tempName}' id='{$tempID}' value='{$tempValue}' {$tempChecked}>";
				$results .=  '&nbsp;' . $tempDisplay . '&nbsp;';
				$results .=  "</span>";
				
			$results .=  "</label>";
			
			$results .= "<span id='data_source_private_section' style='{$tempStyle}'>";
				$results .=  "&nbsp;<a href='#data_source_private_modal' data-toggle='modal'>Select Internal Project</a>: <mark><span id='data_source_private_selected_count'>0</span></mark>";
			$results .= "</span>";
			
		$results .=  "</div>";
	}
	
	
	return $results;

}


function internal_data_print_modal($dataArray = NULL, $platformType = NULL){
	
	$internal_data_get_accessible_project = internal_data_get_accessible_project(0, 1, $platformType);
	

	$modalID 	= 'data_source_private_modal';
	if (($platformType == '') || (get_gene_type() == 'Protein')){
		$modalTitle = "<h4 class='modal-title'>Internal Projects</h4>";
	} else {
		$modalTitle = "<h4 class='modal-title'>Internal Projects ({$platformType})</h4>";
	}
	$modalBody	= '';
	
	
	if (general_array_size($internal_data_get_accessible_project) > 1){
		
		$labelID = "data_source_private_value_select_all_trigger";
		
		
		$checked = '';
		if (!isset($dataArray['data_source_private_project_indexes'])){
			$checked = 'checked';
		}
		
		
		$modalBody	.= "<div class='col-lg-6'>";
			$modalBody .= "<div class='form-check'>";
				$modalBody .= "<label for='{$labelID}' class='form-check-label text-nowrap'>
								<input {$checked} type='checkbox'  id='{$labelID}' class='form-check-input' onclick='if ($(this).prop(\"checked\")){ $(\".data_source_private_project_indexes\").prop(\"checked\", true);} else{ $(\".data_source_private_project_indexes\").prop(\"checked\", false);}'/>
								Select All
								</label> ";
			$modalBody .= "</div>";
		$modalBody	.= "</div>";
	}
	
	
	foreach($internal_data_get_accessible_project as $tempProjectIndex => $tempProjectName){
		
		$labelID = "data_source_private_value_" . md5($tempProjectIndex . $tempProjectName);
		
		$checked = '';
		
		if (isset($dataArray['data_source_private_project_indexes'])){
			if (in_array($tempProjectIndex, $dataArray['data_source_private_project_indexes'])){
				$checked = 'checked';
			}
		} else {
			$checked = 'checked';
		}
		
		
		$modalBody	.= "<div class='col-lg-6'>";
			$modalBody .= "<div class='form-check'>";
				$modalBody .= "<label for='{$labelID}' class='form-check-label text-nowrap'>
								<input type='checkbox' id='{$labelID}' class='form-check-input data_source_private_project_indexes' name='data_source_private_project_indexes[]' value='{$tempProjectIndex}' {$checked}/>
								{$tempProjectName}
								</label> ";
			$modalBody .= "</div>";
		$modalBody	.= "</div>";
	}
	
	

	$modalButtonText	= 'Close';
	$modalBodyClass		= '';
	$modalDialogClass	= '';
	$modalButtonID		= 'internal_data_close_trigger';
	$disableClose		= 0;
	

	
	$dialog .= "<div id='{$modalID}' class='modal fade' role='dialog'>";
		$dialog .= "<div class='modal-dialog {$modalDialogClass}' role='document'>";
			$dialog .= "<div class='modal-content'>";
				$dialog .= "<div class='modal-header'>";
					$dialog .= "{$modalTitle}";
					$dialog .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
				$dialog .= "</div>";
				
				$dialog .= "<div class='modal-body {$modalBodyClass}' style='height:450px; min-height:450px;'>{$modalBody}</div>";
				
				$dialog .= "<div class='modal-footer' style='border:none;'>";
				
					$disableClose = intval($disableClose);
					if ($disableClose == 0){
						$close = "data-dismiss='modal'";	
					} else {
						$close = "";
					}
				
					$dialog .= "<button type='button' class='btn btn-primary'  id='{$modalButtonID}' {$close}>{$modalButtonText}</button>";
				$dialog .= "</div>";
				
			$dialog .= "</div>";
		$dialog .= "</div>";
	$dialog .= "</div>";	
	
	return $dialog;	
}



function internal_data_sanitize_user_input(&$dataSource = NULL, &$internalProjectIndexes = NULL){
	
	if (general_array_size($dataSource) <= 0){
		$dataSource = array('public');
	}
	
	if ((general_array_size($dataSource) == 1) && ($dataSource[0] == 'public')) {
		$internalProjectIndexes = array();
	}
	
	$internalProjectIndexes = general_array_clean($internalProjectIndexes);
	if (general_array_size($internalProjectIndexes) <= 0){
		$needToRemovePrivate = 1;
	}
	
	if (!internal_data_enable()){
		$needToRemovePrivate = 1;
	}
	
	
	foreach($dataSource as $tempKey => $tempValue){
		if (($tempValue != 'public') && ($tempValue != 'private')){
			unset($dataSource[$tempKey]);
		}
		
		if (($tempValue == 'private') && $needToRemovePrivate){
			unset($dataSource[$tempKey]);
		}

	}
	
	if (general_array_size($dataSource) <= 0){
		$dataSource = array('public');
	}
	
	$dataSource = general_array_clean($dataSource);
	
	$dataSource = array_combine($dataSource, $dataSource);
		
	
	return true;

}


function internal_data_split_multiple_data_by_source($indexes = NULL){
	
	if (!is_array($indexes)){
		$indexes = general_split_data($indexes);
	}
	$indexes = general_array_clean($indexes);
	
	if (sizeof($indexes) <= 0) return false;
	
	foreach($indexes as $tempKey => $tempValue){
		
		if (internal_data_is_public($tempValue)){
			$results['public'][] = $tempValue;	
		} else {
			$results['private'][] = $tempValue;	
		}
	}
	
	return $results;
}

function internal_data_id_threshold(){
	return 20000000;	
}

function internal_data_is_public($index = NULL){
	
	$index = abs(intval($index));
	
	if ($index >= 20000000){
		return false;
	} else {
		return true;	
	}
}

function internal_data_get_comparison_directory($index = NULL){
	
	global $BXAF_CONFIG;

	if (internal_data_is_public($index)){
		return "{$BXAF_CONFIG['GO_PATH']}/comp_{$index}/";
	} else {
		return "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$index}/";
	}
	
}


function internal_data_get_comparison_URL($index = NULL){
	
	global $BXAF_CONFIG;

	if (internal_data_is_public($index)){
		return "{$BXAF_CONFIG['BXAF_ROOT_URL']}{$BXAF_CONFIG['GO_URL']}/comp_{$index}/";
	} else {
		return "{$BXAF_CONFIG['BXAF_ROOT_URL']}{$BXAF_CONFIG['WORK_URL']}/Internal_Data/Comparisons/comp_{$index}/";
	}
	
}

function internal_data_get_comparison_page_file($index = NULL){
	
	global $BXAF_CONFIG;

	if (internal_data_is_public($index)){
		$file = "{$BXAF_CONFIG['PAGE_PATH']}/comparison_{$index}_GSEA.PAGE.csv";
	} else {
		$file =  "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$index}/PAGE_comp_{$index}.csv";
	}
	
	$file = str_replace('//', '/', $file);
	
	return $file;
	
}


function internal_data_get_comparison_homer_file($index = NULL, $direction = '', $category = ''){
	
	global $BXAF_CONFIG;
	
	$index = abs(intval($index));
	
	$direction = trim(strtolower($direction));
	
	if ($direction == 'up'){
		$direction = 'Up';
	} else {
		$direction = 'Down';	
	}
	

	if (internal_data_is_public($index)){
		$file = "{$BXAF_CONFIG['GO_PATH']}/comp_{$index}/comp_{$index}_GO_Analysis_{$direction}/{$category}.txt";
	} else {
		$file =  "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$index}/comp_{$index}_GO_Analysis_{$direction}/{$category}.txt";
	}
	
	$file = str_replace('//', '/', $file);
	
	return $file;	
	
}




//$category = 'Project', 'comparison', 'sample'
function internal_data_can_access($category = '', $index = ''){
	
	$index 		= abs(intval($index));
	$category 	= trim(strtolower($category));
		
	if (strpos($category, 'comparison') === 0){
		$table_private 	= 'App_User_Data_Comparisons';
		$defaultColumn	= 'ComparisonIndex';
		$category 		= 'comparison';
	} elseif (strpos($category, 'project') === 0){
		$table_private 	= 'App_User_Data_Projects';
		$defaultColumn	= 'ProjectIndex';
		$category 		= 'project';
	} elseif (strpos($category, 'sample') === 0){
		$table_private 	= 'App_User_Data_Samples';
		$defaultColumn	= 'SampleIndex';
		$category 		= 'sample';
	} else {
		$category 		= '';
	}
	
	if ($category == '') return false; 
	
	if (internal_data_is_public($index)) return true;
	
	
	$accessible_project_indexes = internal_data_get_accessible_project();
	
	if (general_array_size($accessible_project_indexes) <= 0) return false;
	
	
	if ($category == 'project'){
		$project_index = $index;
	} else {
		
		$SQL 			= "SELECT `ProjectIndex` FROM `{$table_private}` WHERE `{$defaultColumn}` = '{$index}'";
		$conn 			= bxaf_get_app_db_connection();
		$project_index 	= $conn->GetOne($SQL);
	}
	
	
	if (isset($accessible_project_indexes[$project_index])){
		return true;	
	} else {
		return false;	
	}
	
}



function internal_data_get_all_definition($table_public = NULL, $jobID = NULL, $groupBy = 'Column'){
	
	global $BAXF_CACHE;
	
	if (!is_internal_column_flexible($table_public)){
		return false;	
	}
	
	if (isset($BAXF_CACHE[__FUNCTION__][$table_public][$jobID][$groupBy])){
		return $BAXF_CACHE[__FUNCTION__][$table_public][$jobID][$groupBy];
	}

	$SQL  = "SELECT * FROM `App_User_Data_Definition` WHERE (`Table_Standard` = '{$table_public}') AND (`Job_ID` = '{$jobID}')";
	$conn = bxaf_get_app_db_connection();
	$results = $conn->GetAssoc($SQL);
	
	foreach($results as $tempKey => $tempValue){
		$finalResults[$tempValue[$groupBy]] = $tempValue;
	}

	$BAXF_CACHE[__FUNCTION__][$table_public][$jobID][$groupBy] = $finalResults;
	
	return $finalResults;
	
	
}


function internal_data_get_definition($table_public = NULL, $jobID = NULL, $column = NULL){
	
	global $BAXF_CACHE;
	
	if (!is_internal_column_flexible($table_public)){
		return false;	
	}
	
	if (isset($BAXF_CACHE[__FUNCTION__][$table_public][$jobID][$column])){
		return $BAXF_CACHE[__FUNCTION__][$table_public][$jobID][$column];
	}

	$allDefinitions = internal_data_get_all_definition($table_public, $jobID);
	
	
	$BAXF_CACHE[__FUNCTION__][$table_public][$jobID][$column] = $allDefinitions[$column];
	
	return $allDefinitions[$column];
	
	
}


function internal_data_transform_one_data($category = NULL, $dataArray = NULL){
	
	if ($dataArray['BXAPP_Transformed']) return $dataArray;

	$category = strtolower(trim($category));
	
	if (strpos($category, 'comparison') === 0){
		$table_public = 'Comparisons';
	} elseif (strpos($category, 'project') === 0){
		$table_public = 'Projects';
	} elseif (strpos($category, 'sample') === 0){
		$table_public = 'Samples';
	} else {
		return $dataArray;
	}

	if (!is_internal_column_flexible($table_public)){
		return $dataArray;
	} else {
		
		$jobID = $dataArray['Job_ID'];
		
		if ($jobID > 0){
			
			$definition = internal_data_get_all_definition($table_public, $jobID, 'Column');
			
			foreach($dataArray as $column => $value){
				if (strpos($column, 'Custom_') === 0){
					
					unset($dataArray[$column]);
					
					if (isset($definition[$column])){
						$dataArray[($definition[$column]['Display_Internal'])] = $value;
					}
					
				}
			}
			
			$dataArray['BXAPP_Transformed'] = 1;
		}
		
		return $dataArray;
	}
	
}


function internal_data_get_column($project_index_array = NULL, $category = NULL, $column_internal = NULL){
	
	global $BAXF_CACHE;
	

	$column_internal = trim($column_internal);
	
	if ($column_internal == '') return false;
	
	
	
	$category = trim(strtolower($category));
	
	if (strpos($category, 'comparison') === 0){
		$table_pubic 	= 'Comparisons';
	} elseif (strpos($category, 'project') === 0){
		$table_pubic 	= 'Projects';
	} elseif (strpos($category, 'sample') === 0){
		$table_pubic 	= 'Samples';
	} elseif (strpos($category, 'gene') === 0){
		$table_pubic 	= 'GeneCombined';
	} else {
		return false;	
	}
	
	
	$project_index_to_jobID = get_multiple_record('project', $project_index_array, 'GetAssoc', '`ProjectIndex`, `Job_ID`', 0);
	
	$JobIDs = implode(',', $project_index_to_jobID);
	
	if ($JobIDs == '') return false;
	
	$SQL = "SELECT * FROM `App_User_Data_Definition` WHERE (`Display_Internal` = '{$column_internal}') AND (`Table_Standard` = '{$table_pubic}') AND (`Job_ID` IN ({$JobIDs})) LIMIT 1";
	
	
	$conn 	= bxaf_get_app_db_connection();
	$sql_results = $conn->GetRow($SQL);
	
	foreach($project_index_to_jobID as $projectIndex => $jobID){
		if ($jobID == $sql_results['Job_ID']){
			$sql_results['ProjectIndex'] = $projectIndex;
			return $sql_results;
		}
	}

	return $sql_results;
	
}

function internal_data_get_gene_level_expression_headers($project_index = NULL){
	
	global $BAXF_CACHE;
	
	$project_index = intval($project_index);
	
	if (isset($BAXF_CACHE[__FUNCTION__][$project_index])){
		return $BAXF_CACHE[__FUNCTION__][$project_index];
	}
	
	if ($project_index <= 0) return false;
	
	$conn 	= bxaf_get_app_db_connection();
	
	$SQL 	= "SELECT `Job_ID` FROM `App_User_Data_Projects` WHERE `ProjectIndex` = '{$project_index}'";
	$jobID	= intval($conn->GetOne($SQL));
	
	if ($jobID <= 0) return false;
	
	$SQL 			= "SELECT `GeneLevelExpression_Details` FROM `App_User_Data_Job` WHERE `ID` = '{$jobID}'";
	$sql_results	= $conn->GetOne($SQL);
	

	if ($sql_results == '') return false;

	$results = json_decode($sql_results, true);
	
	$results = $results['Headers'];

	$BAXF_CACHE[__FUNCTION__][$project_index] = $results;
	
	return $results;
	
	
}


?>