<?php


function getUserSettings(){
	
	global $APP_CONFIG;
	
	$user_id 		= intval($APP_CONFIG['User_Info']['ID']);
	$sql_table		= $APP_CONFIG['Table']['UserPreference'];
	$SQL 			= "SELECT * FROM {$sql_table} WHERE (`User_ID` = {$user_id})";

	$sql_results	= getSQL($SQL, 'GetAssoc', $sql_table, 0);

	foreach($sql_results as $tempKey => $tempValue){
		$results[$tempValue['Category']] = unserialize($tempValue['Detail']);
	}
	
	if ($results['Data_Source'] == ''){
		$results['Data_Source'] = 'all';	
	}
	
	if ($results['Gene_Data_Type'] == ''){
		$results['Gene_Data_Type'] = 'TPM';	
	}
	
	if ($results['Left_Menu_Expanded'] == ''){
		if (general_should_expand_left_menu_by_default()){
			$results['Left_Menu_Expanded'] = 1;
		} else {
			$results['Left_Menu_Expanded'] = 2;
		}
	}
	
	$internal_data_get_accessible_project = internal_data_get_accessible_project();
	if (array_size($internal_data_get_accessible_project) <= 0){
		$results['Data_Source'] = 'public';	
	}
	
	
	
	return $results;
}



function saveColumnPreferences($key = NULL, $value = NULL, $user_id = 0){
	
	global $APP_CONFIG;
	
	$key = trim($key);
	
	if ($key == '') return false;
	
	$user_id = intval($user_id);
	
	if ($user_id == 0){
		$user_id 	= intval($APP_CONFIG['User_Info']['ID']);
	}
	
	$sql_table		= $APP_CONFIG['Table']['UserPreference'];
	$SQL 			= "SELECT `ID` FROM {$sql_table} WHERE (`User_ID` = {$user_id}) AND `Category` = '{$key}'";

	$ID				= getSQL($SQL, 'GetOne', $sql_table, 0);
	
	unset($dataArray);
	$dataArray['Detail'] = serialize($value);
	if ($ID > 0){
		$SQL = getUpdateSQLQuery($sql_table, $dataArray, $ID);
	} else {
		
		$dataArray['User_ID'] 	= $user_id;
		$dataArray['Category'] 	= $key;
		
		$SQL = getInsertSQLQuery($sql_table, $dataArray);
	}
	
	if (0 && general_is_guest_user()){
		$_SESSION['User_Settings'][$key] = $value;
	} else {
		execSQL($SQL);
	}

	return true;
}



function getTableColumnPreferences($type = NULL){
	
	global $APP_CONFIG;
	
	$key			= strtolower($type) . "_search_page_table_column";
	
	$user_id 		= intval($APP_CONFIG['User_Info']['ID']);
	$sql_table		= $APP_CONFIG['Table']['UserPreference'];
	$SQL 			= "SELECT User_ID, Detail FROM {$sql_table} WHERE User_ID IN (0, 1, {$user_id}) AND Category = '{$key}'";
	
	
	if (0 && isset($_SESSION['User_Settings'][$key])){
		$sql_results = $_SESSION['User_Settings'][$key];
	} else {
		$sql_results = getSQL($SQL, 'GetAssoc', $sql_table, 0);
	}
	
	if (array_size($sql_results) > 0){
	
		$preferences	= $sql_results[$user_id];
		
		if ($preferences == ''){
			$preferences	= $sql_results[0];
		}
		
		if ($preferences == ''){
			$preferences	= $sql_results[1];
		}
		
		$preferences 		= unserialize($preferences);
	
		
		$sql_table		= $APP_CONFIG['APP']['List_Category'][$type]['Table'];
		
		
		$currentIndex = 0;
		if (true){
			if ($APP_CONFIG['APP']['List_Category'][$type]['Column_Human_Alt'] != ''){
				$sql_column = $APP_CONFIG['APP']['List_Category'][$type]['Column_Human_Alt'];
			} else {
				$sql_column			= $APP_CONFIG['APP']['List_Category'][$type]['Column_Human'];
			}
			$results[$currentIndex]['Title'] 	= $APP_CONFIG['DB_Dictionary'][$sql_table]['SQL'][$sql_column]['Title'];
			$results[$currentIndex]['SQL'] 		= $sql_column;
			
			$printed[$sql_column] = 1;
		}

		foreach($preferences as $tempKey => $sql_column){
			
			if (isset($APP_CONFIG['DB_Dictionary'][$sql_table]['SQL'][$sql_column])){
				
				if ($APP_CONFIG['DB_Dictionary'][$sql_table]['SQL'][$sql_column]['HideFromSearch']) continue;
				
				if ($APP_CONFIG['DB_Dictionary'][$sql_table]['SQL'][$sql_column]['Title'] != ''){
					if ($sql_column != 'ID'){
						if (!$printed[$sql_column]){
							$currentIndex++;
							$results[$currentIndex]['Title'] 	= $APP_CONFIG['DB_Dictionary'][$sql_table]['SQL'][$sql_column]['Title'];
							$results[$currentIndex]['SQL'] 		= $sql_column;
							$printed[$sql_column] = 1;
						}
					}
				}
				
			}
		}
		
	} elseif (array_size($APP_CONFIG['APP']['List_Category'][$type]['Default_Column_Order']) > 0){
		
		$table = $APP_CONFIG['APP']['List_Category'][$type]['Table'];
		
		$currentIndex = -1;
		foreach($APP_CONFIG['APP']['List_Category'][$type]['Default_Column_Order'] as $tempKey => $sql_column){
			$currentIndex++;
			$results[$currentIndex]['SQL'] 		= $sql_column;
			$results[$currentIndex]['Title'] 	= getColumnName($table, $sql_column);
			
		}
		
		
	}
	
	
	return $results;
}



function getAllSystemSettings(){
	
	global $APP_CONFIG;
	
	$results = $APP_CONFIG['Default_Settings'];
	
	$sql_table	= $APP_CONFIG['Table']['UserPreference'];
	$SQL 	 	= "SELECT * FROM `{$sql_table}` WHERE (`User_ID` = -1)";
	$sql_results = getSQL($SQL, 'GetAssoc', $sql_table, 0);

	foreach($sql_results as $tempKey => $tempValue){
		if ($tempValue != ''){
			$category 	= $tempValue['Category'];
			$detail 	= $tempValue['Detail'];
			
			$results[$category] = unserialize($detail);	
		}
	}
	
	
	if (!isset($results['Research_Project_Disease'])){
	
		$sql_table	= 'Samples';
		
		$currentColumns = getTableColumnNames($sql_table, 0);
		
		if (in_array('DiseaseState', $currentColumns)){

			$SQL		= "SELECT `DiseaseState` FROM `{$sql_table}` GROUP BY `DiseaseState`";
			$sql_results = getSQL($SQL, 'GetCol', $sql_table, 0);
			
			$sql_results = implode(';', $sql_results);
			$sql_results = explode(';', $sql_results);
			$sql_results = array_clean($sql_results);
			natcasesort($sql_results);
			
			foreach($sql_results as $tempKey => $tempValue){
				
				if (strtolower($tempValue) == 'na') continue;
				
				if (strtolower($tempValue) == 'normal control') continue;
				
				$results['Research_Project_Disease'][] = ucwords($tempValue);
			}
		}
	}
	
	
	
	
	return $results;
	
}


?>