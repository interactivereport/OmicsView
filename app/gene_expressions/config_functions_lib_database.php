<?php

function execSQL($SQL = ''){
	global $APP_CONFIG;
	
	$SQL = trim($SQL);
	
	if ($SQL == '') return false;
	
	return $APP_CONFIG['SQL_CONN']->Execute($SQL);

}

function createSQLTable($tableName = NULL, $headers = NULL, $dataType = NULL, $dataKey = NULL, $PrimaryKeyColumnInsertFirst = 0, $primaryKeyBig = 0, $extra = '', $autoIndex = 1){
	global $APP_CONFIG;
	
	if (tableExists($tableName)) return false;
	
	
	unset($SQL);
		
	$keyType = "int(11)";
	
	if ($primaryKeyBig){
		$keyType = "bigint(20)";				
	}
	
	
	if ($PrimaryKeyColumnInsertFirst){
		$SQL[] = "`ID` {$keyType} unsigned NOT NULL AUTO_INCREMENT";
		$SQL[] = " PRIMARY KEY (`ID`)";
		$SQL[] = " UNIQUE KEY `ID` (`ID`)";	
	}

	foreach($headers as $tempKey => $currentColumn){
		$currentSQLDataType = $dataType[$tempKey];
		$SQL[] = "`{$currentColumn}` {$currentSQLDataType}";
	}

	if (!$PrimaryKeyColumnInsertFirst){
		$SQL[] = "`ID` {$keyType} unsigned NOT NULL AUTO_INCREMENT";
		$SQL[] = " PRIMARY KEY (`ID`)";
		$SQL[] = " UNIQUE KEY `ID` (`ID`)";	
	}		
	
	
	if ($autoIndex){
		foreach($headers as $tempKey => $currentColumn){
			if (endsWith(strtolower($currentColumn), 'index')){
				$SQL[] = " KEY `{$currentColumn}` (`{$currentColumn}`)";	
			}
			
			if (endsWith(strtolower($currentColumn), 'id')){
				$SQL[] = " KEY `{$currentColumn}` (`{$currentColumn}`)";
			}
			
			if ($dataKey[$currentColumn]){
				$SQL[] = " KEY `{$currentColumn}` (`{$currentColumn}`)";
			}
		}
	}
	
	foreach($extra as $tempKey => $tempValue){
		$SQL[] = $tempValue;	
	}
	
	
	$SQL = implode(', ', $SQL);
	$SQL = "CREATE TABLE IF NOT EXISTS `{$tableName}` ({$SQL}) ENGINE=MyISAM DEFAULT CHARSET=utf8";

	if ($SQL != ''){
		return execSQL($SQL);	
	} else {
		return false;	
	}
	
}

function truncateTable($tableName = NULL){
	global $APP_CONFIG;
	
	$tableName = trim($tableName);
		
	if ($tableName == '') return false;
		
	$SQL = "TRUNCATE TABLE {$tableName}";
	
	return execSQL($SQL);	
}


function changeTableIndexStatus($tableName = NULL, $enable = 1){
	global $APP_CONFIG;
	
	if ($enable){
		$action = 'ENABLE';	
	} else {
		$action = 'DISABLE';	
	}
	
	$SQL = "ALTER TABLE `{$tableName}` {$action} KEYS";
	
	if ($SQL != ''){
		return execSQL($SQL);	
	} else {
		return false;	
	}
	
}


function getTableCount($table = '', $cache = 1){

	if ($table == '') return 0;
	
	$SQL = "SELECT COUNT(*) FROM `{$table}`";
	
	return intval(getSQL($SQL, 'GetOne', $table, $cache, 1));	
}

function getSQL($SQL = NULL, $type = NULL, $tableName = '', $cache = 1, $saveCache = 1){
	
	global $APP_CONFIG, $APP_CACHE;
	
	if (isset($APP_CACHE[__FUNCTION__][$SQL][$type])){
		return $APP_CACHE[__FUNCTION__][$SQL][$type];
	}
	
	if (!tableExists($tableName)) return false;
	
	$cacheKey = __FUNCTION__ . '::' . md5(($SQL) . '::' . $type);
	
	if ($cache){
		
		$resultsFromCache = getSQLCache($cacheKey);
		
		if (!(is_null($resultsFromCache) || $resultsFromCache == false)){
			return $resultsFromCache;	
		}
	}

	if ($type == 'GetOne'){
		$results = $APP_CONFIG['SQL_CONN']->GetOne($SQL);
		$json_decode_assoc = 0;
	} elseif ($type == 'GetAssoc'){
		$results = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
		$json_decode_assoc = 1;
	} elseif ($type == 'GetArray'){
		$results = $APP_CONFIG['SQL_CONN']->GetArray($SQL);
		$json_decode_assoc = 1;
	} elseif ($type == 'GetCol'){
		$results = $APP_CONFIG['SQL_CONN']->GetCol($SQL);
		$json_decode_assoc = 1;
	} elseif ($type == 'GetRow'){
		$results = $APP_CONFIG['SQL_CONN']->GetRow($SQL);
		$json_decode_assoc = 1;
	}
	
	$APP_CACHE[__FUNCTION__][$SQL][$type] = $results;
	
	if ($saveCache){
		putSQLCache($cacheKey, $results, $tableName, __FUNCTION__, $json_decode_assoc);
	}
	
	
	return $results;
}


function getSQLColumnValue($table = NULL, $column = NULL, $blank = '', $titleCase = 0){
	
	$SQL = "SELECT {$column} FROM {$table} GROUP BY {$column} ORDER BY NULL";
	
	$columns = getSQL($SQL, 'GetCol', $table);
	
	foreach($columns as $tempKey => $tempValue){
		
		$tempValue = trim($tempValue);
		
		if ($tempValue == ''){
			$results[$tempValue] = $blank;
		} else {
			$results[$tempValue] = $tempValue;	
		}
	}
	
	natcasesort($results);

	
	return $results;
	
}

function tableExists($table = NULL){
	
	global $APP_CONFIG;
	
	if ($_SESSION[__FUNCTION__][$table]){
		return $_SESSION[__FUNCTION__][$table];
	}
	
	$SQL = "SHOW TABLES LIKE '{$table}'";
	$tableResult = $APP_CONFIG['SQL_CONN']->GetOne($SQL);

	
	if ($tableResult != ''){
		$_SESSION[__FUNCTION__][$table] = 1;
		$results = 1;
	} else {
		$results = 0;	
	}


	return $results;
	
	
	
}

function getTableColumnNames($table = NULL, $useCache = 1){
	global $APP_CONFIG;
	
	if ($useCache){
		if (isset($_SESSION[__FUNCTION__][$table])){
			return $_SESSION[__FUNCTION__][$table];
		}
	}

	if (tableExists($table)){
		$results = $APP_CONFIG['SQL_CONN']->MetaColumnNames($table);
		$_SESSION[__FUNCTION__][$table] = $results;
	}
	
	return $results;
		
}

function getInsertMultipleSQLQuery($SQL_TABLE = NULL, $dataArray = NULL, $header = '', $delayed = 0){
	
	if (!$delayed){
		$SQL = "INSERT INTO `{$SQL_TABLE}` ";
	} else {
		$SQL = "INSERT DELAYED INTO `{$SQL_TABLE}` ";
	}
	
	if (array_size($header) > 0){
		$SQL_COLUMN_STRING = '(`' . implode('`, `', $header) . '`)';		
	} else {
		$SQL_COLUMN_STRING = '(`' . implode('`, `', array_keys($dataArray[0])) . '`)';
	}
	
	$SQL .= "{$SQL_COLUMN_STRING} VALUES ";
	
	foreach($dataArray as $tempKey => $tempValue){
		
		foreach($tempValue as $tempKey2 => $tempValue2){
			$tempValue[$tempKey2] = addslashes($tempValue2);
		}		
		
		$SQL_VALUE_STRING[] = "('" . implode("', '", array_values($tempValue)) . "')";
	}
	
	$SQL .= implode(',', $SQL_VALUE_STRING);
	
	return $SQL;
}


function getInsertSQLQuery($SQL_TABLE = NULL, $dataArray = NULL, $header = '', $delayed = 0, $trim = 1, $addslashes = 1){
	
	if (!$delayed){
		$SQL = "INSERT INTO `{$SQL_TABLE}` ";
	} else {
		$SQL = "INSERT DELAYED INTO `{$SQL_TABLE}` ";
	}
	
	foreach($dataArray as $key => $value){
		if ($trim){
			$value		 = trim($value);
		}
		
		if ($addslashes){
			$value		 = addslashes($value);
		}
		
		$dataArray[$key] = $value;
	}
	
	if (array_size($header) > 0){
		$SQL_COLUMN_STRING = '(`' . implode('`, `', $header) . '`)';		
	} else {
		$SQL_COLUMN_STRING = '(`' . implode('`, `', array_keys($dataArray)) . '`)';
	}

	$SQL .= "{$SQL_COLUMN_STRING} VALUES ";
	
	$SQL_VALUE_STRING = "('" . implode("', '", array_values($dataArray)) . "')";
	
	$SQL .= "{$SQL_VALUE_STRING}";
	
	return $SQL;
}

function getReplaceSQLQuery($SQL_TABLE = NULL, $dataArray = NULL, $header = '', $trim = 1, $addslashes = 1){
	
	$SQL = "REPLACE INTO `{$SQL_TABLE}` ";
	
	foreach($dataArray as $key => $value){
		if ($trim){
			$value		 = trim($value);
		}
		
		if ($addslashes){
			$value		 = addslashes($value);
		}
		
		$dataArray[$key] = $value;
	}
	
	if (array_size($header) > 0){
		$SQL_COLUMN_STRING = '(`' . implode('`, `', $header) . '`)';		
	} else {
		$SQL_COLUMN_STRING = '(`' . implode('`, `', array_keys($dataArray)) . '`)';
	}

	$SQL .= "{$SQL_COLUMN_STRING} VALUES ";
	
	$SQL_VALUE_STRING = "('" . implode("', '", array_values($dataArray)) . "')";
	
	$SQL .= "{$SQL_VALUE_STRING}";
	
	return $SQL;
}



function getUpdateSQLQuery($SQL_TABLE = NULL, $dataArray = NULL, $ID = NULL, $SQL_FILTER = '', $trim = 1, $ID_Field = 'ID'){
	
	$SQL = "UPDATE `{$SQL_TABLE}` SET ";
	
	foreach($dataArray as $key => $value){
		if ($SQL_VALUE_STRING != '') $SQL_VALUE_STRING .= ', ';
		
		if ($trim){
			$value		 = trim($value);
		}
		
		$value = addslashes($value);
		
		$SQL_VALUE_STRING .= "`{$key}` = '{$value}'";
		
	}
	
	$SQL .= "{$SQL_VALUE_STRING} WHERE `{$ID_Field}` ";
	
	if (is_array($ID)){
		$ID = array_filter($ID);
		$ID = array_unique($ID);
		$ID = array_filter($ID, 'is_numeric');
		$ID = implode(',', $ID);
		
		$SQL .= " IN ({$ID})";
		
	} else {
		
		$ID = intval($ID);
		
		$SQL .= " = {$ID}";
	}
	
	if ($SQL_FILTER != ''){
		$SQL .= " AND {$SQL_FILTER}";	
	}
	
	
	return $SQL;
}


function getExecutedSQLs(){
	global $APP_CONFIG;
	
	return $APP_CONFIG['SQL_CONN']->get_stats();
	
}


function getLastInsertID(){
	global $APP_CONFIG;
	
	return $APP_CONFIG['SQL_CONN']->Insert_ID();
}


function getUserInfo($ID){
	
	return general_get_user_info($ID);
}

function getAllActiveUsers(){
	
	global $BXAF_CONFIG;
	
	if ($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'sqlite'){
		$BXAF_CONN = bxaf_get_user_db_connection();
	
		$SQL = "SELECT `ID`, `Login_Name`, `Name`, `First_Name`, `Last_Name`, `Email` FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN']} WHERE (bxafStatus != 9) OR (bxafStatus IS NULL) ORDER BY Name";
		$results = $BXAF_CONN->query($SQL);
	
		if ($results){
			while($row = $results->fetchArray(SQLITE3_ASSOC)){
				$data[$row['ID']] = $row;
			}
		}

		
		$BXAF_CONN->close();
	
	} else if($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'mysql'){
	
		$BXAF_CONN = bxaf_get_user_db_connection();
	
		$SQL = "SELECT `ID`, `Login_Name`, `Name`, `First_Name`, `Last_Name`, `Email` FROM `{$BXAF_CONFIG['TBL_BXAF_LOGIN']}` WHERE (bxafStatus != 9) OR (bxafStatus IS NULL) ORDER BY `Name`";
		$data = $BXAF_CONN->GetAssoc($sql);
	}
	
	
	return $data;
	
	
}


function getAllLoginLogs($startDate = '', $endDate = ''){
	
	global $BXAF_CONFIG;
	
	if (($startDate == '') && ($endDate == '')){
		$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN_LOG']} WHERE (`Login_ID` > 0) ORDER BY ID";
	} else {
		$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN_LOG']} WHERE (`Login_ID` > 0) AND (`Status_Time` >= '{$startDate}')  AND (`Status_Time` <= '{$endDate}') ORDER BY ID";
	}

	
	if ($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'sqlite'){
		$BXAF_CONN = bxaf_get_user_db_connection();
	
		
		$results = $BXAF_CONN->query($SQL);

		if ($results){
			while($row = $results->fetchArray(SQLITE3_ASSOC)){
				$data[$row['ID']] = $row;
			}
		}

		
		$BXAF_CONN->close();
	
	} else if($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'mysql'){
	
		$BXAF_CONN = bxaf_get_user_db_connection();
	
		$data = $BXAF_CONN->GetAssoc($sql);
	}
	
	return $data;
	
	
}


function getAllLoginLogsCustom($startDate = '', $endDate = '', $file = ''){
	
	global $BXAF_CONFIG;
	
	$file = trim($file);
	
	if (!file_exists($file)) return false;
	
	if (($startDate == '') && ($endDate == '')){
		$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN_LOG']} WHERE (`Login_ID` > 0) ORDER BY ID";
	} else {
		$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN_LOG']} WHERE (`Login_ID` > 0) AND (`Status_Time` >= '{$startDate}')  AND (`Status_Time` <= '{$endDate}') ORDER BY ID";
	}

	
	$BXAF_CONN = new SQLite3($file);
	
	$results = $BXAF_CONN->query($SQL);

	if ($results){
		while($row = $results->fetchArray(SQLITE3_ASSOC)){
			$data[$row['ID']] = $row;
		}
	}

	
	$BXAF_CONN->close();

	
	return $data;
	
}

function getAllLoginLogsCustomByKeyword($startDate = '', $endDate = '', $file = '', $keyword = ''){
	
	global $BXAF_CONFIG;
	
	$file = trim($file);
	
	if (!file_exists($file)) return false;
	
	if (($startDate == '') && ($endDate == '')){
		$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN_LOG']} WHERE (`Login_ID` > 0) ORDER BY ID";
	} else {
		$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN_LOG']} WHERE (`Login_ID` > 0) AND (`Status_Time` >= '{$startDate}')  AND (`Status_Time` <= '{$endDate}') ORDER BY ID";
	}

	
	$BXAF_CONN = new SQLite3($file);
	
	$results = $BXAF_CONN->query($SQL);

	if ($results){
		while($row = $results->fetchArray(SQLITE3_ASSOC)){
			
			$row['SERVER'] = unserialize(stripslashes($row['SERVER']));
			
			if (strpos($row['SERVER']['PHP_SELF'], $keyword) === FALSE) continue;
			
			$count++;
			
			$data[$row['ID']] = $row;
		}
	}

	
	$BXAF_CONN->close();

	
	return $data;
	
}




function getAllLoginsCustom($file = ''){
	
	global $BXAF_CONFIG;
	
	$file = trim($file);
	
	if (!file_exists($file)) return false;
	
	$SQL = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN']} ORDER BY ID";
	
	$BXAF_CONN = new SQLite3($file);
	
	$results = $BXAF_CONN->query($SQL);

	if ($results){
		while($row = $results->fetchArray(SQLITE3_ASSOC)){
			$data[$row['ID']] = $row;
		}
	}

	
	$BXAF_CONN->close();

	
	return $data;
	
}

function getTableStructure($SQL_TABLE = NULL, $checkSum = 1){
	
	global $APP_CONFIG;
	
	
	if (tableExists($SQL_TABLE)){
		$SQL = "SHOW CREATE TABLE `{$SQL_TABLE}`";
		
		$SQL_RESULT = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
		
		$structure	= preg_replace("/ AUTO_INCREMENT=[0-9]*/i", "", $SQL_RESULT[$SQL_TABLE]);
		
		if ($checkSum){
			$structure = md5($structure);	
		}
		
	}
	
	
	return $structure;
}

function getColumnStructure($SQL_TABLE = NULL, $column = NULL){
	
	global $APP_CONFIG;
	
	if (tableExists($SQL_TABLE)){
		$SQL = "SHOW FIELDS FROM `{$SQL_TABLE}`";
		$SQL_RESULT = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
		
		
		if ($column == ''){
			return $SQL_RESULT;	
		} else {
			return $SQL_RESULT[$column];
		}
	} else {
		return false;
	}
	
}

?>