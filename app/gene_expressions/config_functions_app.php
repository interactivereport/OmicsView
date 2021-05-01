<?php

function initialize(){
	global $APP_CONFIG, $APP_CONFIG_CUSTOM, $ORDER_ARRAY, $BXAF_CONFIG;
	
	
	$APP_CONFIG['APP_DIR'] 		= __DIR__ . '/';
	$APP_CONFIG['APP_URL'] 		= "//{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['REQUEST_URI']) . '/';
		
	$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
	
	
	if ($BXAF_CONFIG['REDIS_ENABLE']){
		
		try {
			$APP_CONFIG['REDIS_CONN'] 		= new Redis();
			$APP_CONFIG['REDIS_CONN']->pconnect('localhost', 6379);
			$APP_CONFIG['REDIS_CONN']->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
			$APP_CONFIG['REDIS_ENABLE'] 	= true;
		} catch(Exception $e){
			$APP_CONFIG['REDIS_CONN'] 		= FALSE;
			$APP_CONFIG['REDIS_ENABLE'] 	= FALSE;
			$BXAF_CONFIG['REDIS_ENABLE'] 	= FALSE;
		}
		
	}
	
	
	$SQL = "SELECT `Key`, `Value` FROM `{$APP_CONFIG['Table']['Info']}` WHERE `Table_Name` = '{$APP_CONFIG['Table']['Samples']}'";
	$clinicalTriplets = getSQL($SQL, 'GetAssoc', $APP_CONFIG['Table']['Samples']);
	
	
	
	foreach ($APP_CONFIG_CUSTOM['DB_Dictionary'] as $currentTable => $currentDetails){
		$currentColumns = getTableColumnNames($currentTable);
		
		if (isset($APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Preselect'])){
			$APP_CONFIG['DB_Dictionary'][$currentTable]['Preselect'] = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Preselect'];
		}
		
		
		foreach($currentColumns as $tempKey => $currentColumn){
			
			if (isset($APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL'][$currentColumn])){
				$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn] = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL'][$currentColumn];
			}
			
			
			if (($currentTable == $APP_CONFIG['Table']['Samples']) && (strpos($currentColumn, 'Clinical_Triplets_') === 0)){
				
				if ($APP_CONFIG['APP']['Module']['Clinical_Triplets']){
					$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['HideFromOption'] = 0;
					$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['HideFromSearch'] = 0;
					$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Clinical_Triplets'] = 1;
					
					if ($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'] == ''){
						if ($clinicalTriplets[$currentColumn] != ''){
							$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'] = $clinicalTriplets[$currentColumn];
							$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title_Long'] = "Clinical Triplets: {$clinicalTriplets[$currentColumn]}";
						}
					}
				} else {
					$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['HideFromOption'] = 1;
					$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['HideFromSearch'] = 1;
					$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Clinical_Triplets'] = 1;
				}
			}
		}
		
		foreach ($APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown'] as $currentColumn => $tempValue){
			if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn])){
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Dropdown'][$currentColumn] = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown'][$currentColumn];
			}
		}
		
		foreach ($APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter'] as $currentColumn => $tempValue){
			if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn])){
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Filter'][$currentColumn] = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter'][$currentColumn];
			}
		}
		
		
		foreach ($APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options'] as $currentColumn => $tempValue){
			if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn])){
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options'][$currentColumn] = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options'][$currentColumn];
			}
		}
		
		
		foreach ($APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Meta_Analysis_Options'] as $currentColumn => $tempValue){
			if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn])){
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options'][$currentColumn] = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Meta_Analysis_Options'][$currentColumn];
			}
		}
		
		
		
		foreach($currentColumns as $tempKey => $currentColumn){
			if ($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'] == ''){
				
				$columnTitle = str_replace('_', ' ', $currentColumn);
				
				$columnTitle = str_replace('Type', ' Type', $columnTitle);
				$columnTitle = str_replace('Status', ' Status', $columnTitle);
				
				$columnTitle = fromCamelCase($columnTitle);
				
				$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'] = trim($columnTitle);
			}
			
			
			if (($currentTable == 'Samples') || ($currentTable == 'GeneCombined') || ($currentTable == 'Comparisons')){
				if (!$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['HideFromOption']){
					$APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Completed'][$currentColumn]['Default'] = intval($APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options'][$currentColumn]['Default']);
					$APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Completed'][$currentColumn]['Title'] = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
					
					if (strpos($currentColumn, 'Clinical_Triplets_') === 0){
						unset($APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Completed'][$currentColumn]);
					}
					
					
					$APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Completed'][$currentColumn]['Default'] = intval($APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options'][$currentColumn]['Default']);
					$APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Completed'][$currentColumn]['Title'] = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
					
				}
			}
		}
		
		
		if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Completed'])){
			$ORDER_ARRAY = array('Title' => 'ASC');
			naturalSort2DArray($APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Completed'], $ORDER_ARRAY);
			
			unset($currentCount);
			foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Completed'] as $tempKey => $tempValue){
				$currentCount++;
				
				$remainder = $currentCount % 2;
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Splitted_2'][$remainder][$tempKey] = $tempValue;
				
				$remainder = $currentCount % 3;
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Gene_Expression_Options_Splitted_3'][$remainder][$tempKey] = $tempValue;
				
			}
		}
		
		
		if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Completed'])){
			$ORDER_ARRAY = array('Title' => 'ASC');
			naturalSort2DArray($APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Completed'], $ORDER_ARRAY);
			
			unset($currentCount);
			foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Completed'] as $tempKey => $tempValue){
				$currentCount++;
				
				$remainder = $currentCount % 2;
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Splitted_2'][$remainder][$tempKey] = $tempValue;
				
				$remainder = $currentCount % 3;
				$APP_CONFIG['DB_Dictionary'][$currentTable]['Meta_Analysis_Options_Splitted_3'][$remainder][$tempKey] = $tempValue;
				
			}
		}
		
	}
	
	ksort($APP_CONFIG['DB_Dictionary']);
	
	
	if ($BXAF_CONFIG['canvasxpress']['Log_Add_Value'] > 0){
		$APP_CONFIG['canvasxpress']['Log_Add_Value'] = abs(floatval($BXAF_CONFIG['canvasxpress']['Log_Add_Value']));
	}
	
	if ($BXAF_CONFIG['canvasxpress']['graphOrientation'] != ''){
		$APP_CONFIG['canvasxpress']['graphOrientation'] = trim($BXAF_CONFIG['canvasxpress']['graphOrientation']);
	}
	
	
	if ($BXAF_CONFIG['EXPORT_LIMIT']){
		$APP_CONFIG['EXPORT_LIMIT'] = true;
		$APP_CONFIG['EXPORT_LIMIT_OPTIONS'] = $BXAF_CONFIG['EXPORT_LIMIT_OPTIONS'];
	}
	
	
	return true;
}



function cleanPlotCache(){
	
	global $BXAF_CONFIG;
	
	$dir = $BXAF_CONFIG['BXAF_APP_DIR'] . 'plot/dashboard/';
	unlink($dir . 'comparisons.json');
	
	$dir = $BXAF_CONFIG['BXAF_APP_DIR'] . 'plot/bubble/files/';
	$cmd = "{$BXAF_CONFIG['RM_BIN']} -Rf {$dir}";
	shell_exec($cmd);
	
	return true;
}


function getTableColumnNamesTitle($table, $filter = ''){
	
	global $APP_CONFIG, $BAXF_CACHE;
	
	if (isset($BAXF_CACHE[__FUNCTION__][$table])){
		return $BAXF_CACHE[__FUNCTION__][$table];
	}

	if (tableExists($table)){
		$columnNames = getTableColumnNames($table, 1);
		
		
		foreach($columnNames as $tempKey => $currentSQL){
			
			if ($currentSQL == 'ID') continue;
			
			if ($filter != ''){
				if ($APP_CONFIG['DB_Dictionary'][$table]['SQL'][$currentSQL][$filter]) continue;
				if ($APP_CONFIG['Internal_Data'][$table]['Headers'][$currentSQL][$filter]) continue;
			}
			
			
			$currentTitle = '';
			
			if (($currentTitle == '') && isset($APP_CONFIG['DB_Dictionary'][$table]['SQL'][$currentSQL]['Title_Long'])){
				$currentTitle = $APP_CONFIG['DB_Dictionary'][$table]['SQL'][$currentSQL]['Title_Long'];
			}
			
			if (($currentTitle == '') && isset($APP_CONFIG['DB_Dictionary'][$table]['SQL'][$currentSQL]['Title'])){
				$currentTitle = $APP_CONFIG['DB_Dictionary'][$table]['SQL'][$currentSQL]['Title'];	
			}
			
			if (($currentTitle == '') && isset($APP_CONFIG['Internal_Data'][$table]['Headers'][$currentSQL]['Name'])){
				$currentTitle = $APP_CONFIG['Internal_Data'][$table]['Headers'][$currentSQL]['Name'];
			}
			
			if ($currentTitle == ''){
				$currentTitle = str_replace('_', ' ', $currentSQL);
			}
			
			$results[$currentSQL] = $currentTitle;
			
		}
		
		natsort($results);
		
		$BAXF_CACHE[__FUNCTION__][$table] = $results;
	}
	
	return $results;
		
}

function getColumnName($table, $column){
	
	$getTableColumnNamesTitle = getTableColumnNamesTitle($table);

	return $getTableColumnNamesTitle[$column];
}

function getColumnPlaceholderText($table, $column){
	
	global $APP_CONFIG;
	
	return $APP_CONFIG['DB_Dictionary'][$table]['SQL'][$column]['Placeholder'];
	
	
}

?>