<?php

function general_getInsertMultipleSQLQuery($SQL_TABLE = NULL, $dataArray = NULL, $header = ''){
	
	
	$SQL = "INSERT INTO {$SQL_TABLE} ";
	
	if (sizeof($header) > 0){
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

?>