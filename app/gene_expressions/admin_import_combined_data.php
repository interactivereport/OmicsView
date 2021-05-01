<?php

if (php_sapi_name() !== 'cli'){	
	exit();
}

include_once('config_init.php');

$tableName = $APP_CONFIG['Table']['Samples'];

echo "DiseaseData: Combine both Public and Private Data (Version: {$APP_CONFIG['Version']['Application Version']})" . "\n";

set_time_limit(0);
ini_set('memory_limit', '-1');

$startTime		 	= microtime(true);
$startMemoryUsage 	= memory_get_usage();
$argvLength 		= array_size($argv);
$option				= abs(intval($argv[1]));

if (($option > 5) || ($option <= 0)){
	$hasError = true;	
}

if ($argvLength != 2){
	$hasError = true;
}

if ($hasError){
	echo "Usage:   php {$argv[0]}  <option>" . "\n";
	echo "Option: \n";
	echo "1: Remove all data first, then import both public and private data\n";
	echo "2: Delete public data, then import public data only.\n";
	echo "3: Delete private data, then import private data only\n";
	echo "4: Import new public data only\n";
	echo "5: Import new private data only\n";
	echo "\n";
	exit();
}

$allTables = array(
		'Comparison' => $APP_CONFIG['Table']['App_User_Data_Comparisons_Combined'], 
		'Project' => $APP_CONFIG['Table']['App_User_Data_Projects_Combined'], 
		'Sample' => $APP_CONFIG['Table']['App_User_Data_Samples_Combined']
	);

$internal_data_id_threshold = internal_data_id_threshold();

if ($option == 1){
	echo "1: Remove all data first, then import both public and private data\n";
	$needToEmptyTable 			= true;
	$needToDeletePublicData 	= false;
	$needToDeletePrivateData 	= false;
	
	$needToImportPublicData		= true;
	$needToImportPrivateData	= true;
}

if ($option == 2){
	echo "2: Delete public data, then import public data only.\n";
	$needToEmptyTable 			= false;
	$needToDeletePublicData 	= true;
	$needToDeletePrivateData 	= false;
	
	$needToImportPublicData		= true;
	$needToImportPrivateData	= false;
}

if ($option == 3){
	echo "3: Delete private data, then import private data only\n";
	$needToEmptyTable 			= false;
	$needToDeletePublicData 	= false;
	$needToDeletePrivateData 	= true;
	
	$needToImportPublicData		= false;
	$needToImportPrivateData	= true;
}

if ($option == 4){
	echo "4: Import new public data only\n";
	$needToEmptyTable 			= false;
	$needToDeletePublicData 	= false;
	$needToDeletePrivateData 	= false;
	
	$needToImportPublicData		= true;
	$needToImportPrivateData	= false;
}

if ($option == 5){
	echo "5: Import new private data only\n";
	$needToEmptyTable 			= false;
	$needToDeletePublicData 	= false;
	$needToDeletePrivateData 	= false;
	
	$needToImportPublicData		= false;
	$needToImportPrivateData	= true;
}


if ($needToEmptyTable){
	echo "Truncating All Tables.\n";
	foreach($allTables as $currentCategory => $currentTable){
		echo "Emptying {$currentCategory} table: {$currentTable}\n";
		truncateTable($currentTable);
	}
	echo "------------------------------------------------------------\n";
	echo "\n";	
}

if ($needToDeletePublicData){
	echo "Deleting Public Data.\n";
	foreach($allTables as $currentCategory => $currentTable){
		echo "Delete public data from {$currentCategory} table: {$currentTable}\n";
		$columnInternal = $APP_CONFIG['APP']['List_Category'][$currentCategory]['Column_Internal'];
		$SQL = "DELETE 	* FORM `{$currentTable}` WHERE `{$columnInternal}` < {$internal_data_id_threshold}";
		execSQL($SQL);
	}
	echo "------------------------------------------------------------\n";
	echo "\n";
}

if ($needToDeletePrivateData){
	echo "Deleting Private Data.\n";
	foreach($allTables as $currentCategory => $currentTable){
		echo "Delete private data from {$currentCategory} table: {$currentTable}\n";
		$columnInternal = $APP_CONFIG['APP']['List_Category'][$currentCategory]['Column_Internal'];
		$SQL = "DELETE 	* FORM `{$currentTable}` WHERE `{$columnInternal}` >= {$internal_data_id_threshold}";
		execSQL($SQL);
	}
	echo "------------------------------------------------------------\n";
	echo "\n";
}


if ($needToImportPublicData){
	echo "Reading Public Data.\n";
	foreach($allTables as $currentCategory => $currentTable){
		echo "Reading public data from {$currentCategory}.\n";
		$columnInternal = $APP_CONFIG['APP']['List_Category'][$currentCategory]['Column_Internal'];
		
		$SQL = "SELECT count(*) FROM `{$currentTable}` WHERE `Is_Private` = 0";
		echo "SQL: {$SQL}\n";
		$count = getSQL($SQL, 'GetOne', $currentTable, 0, 1);


		$sourceTable = $APP_CONFIG['APP']['List_Category'][$currentCategory]['Table'];		
		if ($count == 0){
			$SQL = "SELECT * FROM `{$sourceTable}`";
		} else {
			$SQL = "SELECT * FROM `{$sourceTable}` WHERE `{$columnInternal}` >= {$count}";
	
		}
		
		echo "SQL: {$SQL}\n";		
		$SQL_RESULTS = getSQL($SQL, 'GetArray', $sourceTable, 0, 1);
		
		$dataArray = array();
		$currentCount = 0;
		foreach($SQL_RESULTS as $tempKey => $tempValue){
			
			unset($tempValue['ID']);
			$tempValue['Is_Private'] = 0;
			
			$tempValue['Date'] 		= date("Y-m-d");
			$tempValue['DateTime'] 	= date("Y-m-d H:i:s");
			
			$dataArray[] = $tempValue;
			$currentCount++;
			
			if ($currentCount == $BXAF_CONFIG['IMPORT']['BULK_INSERT']){
				echo "Inserting {$currentCount} records to {$currentTable}\n";
				$SQL = getInsertMultipleSQLQuery($currentTable, $dataArray);
				execSQL($SQL);
				$dataArray = array();
				$currentCount = 0;
			}
		}
		
		
		if ($currentCount > 0){
			echo "Inserting {$currentCount} records to {$currentTable}\n";
			$SQL = getInsertMultipleSQLQuery($currentTable, $dataArray);
			execSQL($SQL);
			$dataArray = array();
			$currentCount = 0;
		}
		
		echo "\n";
	}
	echo "------------------------------------------------------------\n";
	echo "\n";
}



if ($needToImportPrivateData){
	echo "Reading Private Data.\n";
	foreach($allTables as $currentCategory => $currentTable){
		echo "Reading private data from {$currentCategory}.\n";
		$columnInternal = $APP_CONFIG['APP']['List_Category'][$currentCategory]['Column_Internal'];
		
		$SQL = "SELECT max(`{$columnInternal}`) FROM `{$currentTable}` WHERE `Is_Private` = 1";
		echo "SQL: {$SQL}\n";
		$maxIndex = getSQL($SQL, 'GetOne', $currentTable, 0, 1);
		$maxIndex = intval($maxIndex);

		$sourceTable = $APP_CONFIG['APP']['List_Category'][$currentCategory]['Table_User'];		
		if ($maxIndex == 0){
			$SQL = "SELECT * FROM `{$sourceTable}`";
		} else {
			$SQL = "SELECT * FROM `{$sourceTable}` WHERE `{$columnInternal}` > {$maxIndex}";
	
		}
		
		echo "SQL: {$SQL}\n";		
		$SQL_RESULTS = getSQL($SQL, 'GetArray', $sourceTable, 0, 1);
		
		$dataArray = array();
		$currentCount = 0;
		foreach($SQL_RESULTS as $tempKey => $tempValue){
			
			unset($tempValue['ID']);
			$tempValue['Is_Private'] = 1;
			
			$dataArray[] = $tempValue;
			$currentCount++;
			
			if ($currentCount == $BXAF_CONFIG['IMPORT']['BULK_INSERT']){
				echo "Inserting {$currentCount} records to {$currentTable}\n";
				$SQL = getInsertMultipleSQLQuery($currentTable, $dataArray);
				execSQL($SQL);
				$dataArray = array();
				$currentCount = 0;
			}
		}
		
		
		if ($currentCount > 0){
			echo "Inserting {$currentCount} records to {$currentTable}\n";
			$SQL = getInsertMultipleSQLQuery($currentTable, $dataArray);
			execSQL($SQL);
			$dataArray = array();
			$currentCount = 0;
		}
		
		echo "\n";
	}
	echo "------------------------------------------------------------\n";
	echo "\n";
}

clearCache(0);
clear_tabix_cache();


$endTime		 	= microtime(true);
$endMemoryUsage 	= memory_get_usage();



echo "Duration: " . round(($endTime - $startTime)/60, 2) . " minutes \n";
echo "Memory: " . round($endMemoryUsage/(1024*1024), 3) . " MB \n";


echo "Finished.\n";

echo "------------------------------------------------------------\n";

exit();


?>