<?php

function getRecordDisplayName($category = NULL, $column = NULL, $dataArray = array()){
	
	global $APP_CONFIG;
	
	$category = trim(strtolower($category));
	
	if (strpos($category, 'comparison') === 0){
		$currentTable	 	= 'Comparisons';
	} elseif (strpos($category, 'project') === 0){
		$currentTable 	= 'Projects';
	} elseif (strpos($category, 'sample') === 0){
		$currentTable 	= 'Samples';
	} elseif (strpos($category, 'gene') === 0){
		$currentTable 	= 'GeneCombined';
	} elseif (strpos($category, 'dataset') === 0){
		$currentTable 	= 'Datasets';
	} else {
		return $column;	
	}
	
	$display = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$column]['Title'];
	
	if ($display == ''){
		$display = $APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$column]['Name'];
	}

	if (strpos($column, '_') !== FALSE){
		$display = str_replace('_', ' ', $display);
	}
	
	if (($display == '') && (strpos($column, 'Custom_') === 0)){
		$definition = internal_data_get_definition($currentTable, $dataArray['Job_ID'], $column);	
		$display = $definition['Display'];
	}

	return $display;
	
}



//$mode
//0: If value is empty, return false
//1: return true regardless of the status
function shouldDisplay($category = NULL, $column = NULL, $dataArray = NULL, $mode = 0){
	
	global $APP_CONFIG;
	
	if ($column == 'ID') return false;
	if ($column == 'ProjectIndex') return false;
	if ($column == 'SampleIndex') return false;
	if ($column == 'ComparisonIndex') return false;
	if ($column == 'GeneIndex') return false;
	if ($column == 'DatasetIndex') return false;
	
	if ($column == 'Date') return false;
	if ($column == 'DateTime') return false;
	if ($column == 'User_ID') return false;
	if ($column == 'Permission') return false;
	if ($column == 'Import_ID') return false;
	if ($column == 'Job_ID') return false;
	if ($column == 'Increment') return false;
	if ($column == 'DatasetID_Original') return false;
	if ($column == 'User_ID') return false;
	
	
	$currentTable = $APP_CONFIG['APP']['List_Category'][$category]['Table'];
	if ($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$column]['HideFromSearch']){
		return false;
	}
	
	
	if (strpos($column, 'Custom_') === 0){
		$definition = internal_data_get_definition($currentTable, $dataArray['Job_ID'], $column);	
		$display = $definition['Display'];
		
		if ($display != '') return true;
	}
	
	if ($dataArray[$column] == ''){
		if ($mode == 0) return false;
	}
	
	return true;
	
}

function updateRecords($category = NULL, $recordIndexes = NULL, $values = NULL){
	
	global $APP_CONFIG;
	
	$currentTableUser 		= $APP_CONFIG['APP']['List_Category'][$category]['Table_User'];
	$currentTableCombined 	= $APP_CONFIG['APP']['List_Category'][$category]['Table_Combined'];
	$columnInternal			= $APP_CONFIG['APP']['List_Category'][$category]['Column_Internal'];
	
	if ($currentTableUser == '') return false;
	if (array_size($values) <= 0) return false;
	
	foreach($recordIndexes as $tempKey => $recordIndex){
		if (internal_data_is_public($recordIndex)){
			unset($recordIndexes[$tempKey]);
			$hasPublicData = true;
		}
	}
	
	if (array_size($recordIndexes) <= 0) return false;
	
	if (true){
		$recordIndexesString = implode(',', $recordIndexes);
		
		$SQL = "SELECT `{$columnInternal}`, `{$columnInternal}`, `" . implode('`,`', array_keys($values)) . "` FROM `{$currentTableUser}` WHERE `{$columnInternal}` IN ({$recordIndexesString})";
		
		$dataBefore = getSQL($SQL, 'GetAssoc', $currentTableUser);
	}

	if (true){
		
		$dataUpdate = array();
		
		$SQL = getUpdateSQLQuery($currentTableUser, $values, $recordIndexes, '', 1, $columnInternal);
		execSQL($SQL);
		
		$SQL = getUpdateSQLQuery($currentTableCombined, $values, $recordIndexes, '', 1, $columnInternal);
		execSQL($SQL);

	}
	
	if (true){
		$auditTrail = array();
		
		$common = array();
		$common['Date']	 	= date('Y-m-d');
		$common['DateTime'] = date('Y-m-d H:i:s');
		$common['User_ID'] 	= $APP_CONFIG['User_Info']['ID'];
		$common['Table']	= $APP_CONFIG['APP']['List_Category'][$category]['Table'];
			
					
		foreach($recordIndexes as $tempKey => $recordIndex){
			$tempArray = $common;
			$tempArray['Record_Index']	= $recordIndex;
			
			foreach($values as $currentSQL => $currentValue){
				$tempArray['Column'] 		= $currentSQL;
				$tempArray['Value_Before']	= $dataBefore[$recordIndex][$currentSQL];
				$tempArray['Value_After'] 	= $currentValue;
				$auditTrail[] = $tempArray;
			}
		}
		
		$SQL = getInsertMultipleSQLQuery($APP_CONFIG['Table']['App_User_Data_Audit_Trail'], $auditTrail, '', 1);
		
		execSQL($SQL);
		
		
	}
	
	clearCache(0);
	
	return true;

}

function getAuditTrail($table, $recordIndex){
	
	global $APP_CONFIG;
	
	$recordIndex = intval($recordIndex);
	
	$SQL = "SELECT * FROM `{$APP_CONFIG['Table']['App_User_Data_Audit_Trail']}` WHERE (`Table` = '{$table}') AND (`Record_Index` = {$recordIndex}) ORDER BY `ID` DESC";

	return getSQL($SQL, 'GetAssoc', $APP_CONFIG['APP']['List_Category'][$category]['Table']);
	
}

?>