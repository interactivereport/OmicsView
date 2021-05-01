<?php

$SQL_Bulk_Insert_Threadhold = 5000;


//Cache Table
unset($tableName, $headers, $dataType, $dataKey, $extra);
$tableName = $APP_CONFIG['Table']['Cache'];
$headers['Table_Name'] 			= 'Table_Name';
$headers['Key'] 				= 'Key';
$headers['Value'] 				= 'Value';
$headers['Category']			= 'Category';
$headers['Json_Decode_Assoc']	= 'Json_Decode_Assoc';

$dataType['Table_Name'] 		= 'varchar(64) NOT NULL';
$dataType['Key'] 				= 'varchar(256) NOT NULL';
$dataType['Value'] 				= 'longblob NOT NULL';
$dataType['Category'] 			= 'varchar(32) NOT NULL';
$dataType['Json_Decode_Assoc'] 	= 'tinyint(4) unsigned NOT NULL';

$dataKey['Table_Name'] 	= true;
$dataKey['Category']	= true;

$extra[]				= 'UNIQUE KEY `Key_Unique` (`Key`)';

createSQLTable($tableName, $headers, $dataType, $dataKey, 1, 1, $extra);




//Info Table
unset($tableName, $headers, $dataType, $dataKey, $extra);
$tableName = $APP_CONFIG['Table']['Info'];
$headers['Table_Name'] 	= 'Table_Name';
$headers['Key'] 		= 'Key';
$headers['Value'] 		= 'Value';

$dataType['Table_Name'] = 'varchar(64) NOT NULL';
$dataType['Key'] 		= 'varchar(256) NOT NULL';
$dataType['Value'] 		= 'varchar(256) NOT NULL';

$dataKey['Table_Name'] 	= true;
$dataKey['Key'] 		= true;

createSQLTable($tableName, $headers, $dataType, $dataKey, 1);


//List
unset($tableName, $headers, $dataType, $dataKey, $extra);
$tableName = $APP_CONFIG['Table']['List'];
$headers['User_ID'] 	= 'User_ID';
$headers['Name'] 		= 'Name';
$headers['Category'] 	= 'Category';
$headers['Table'] 		= 'Table';
$headers['Date'] 		= 'Date';
$headers['Items'] 		= 'Items';
$headers['Count'] 		= 'Count';
$headers['Notes'] 		= 'Notes';

$dataType['User_ID'] 	= 'int(11) unsigned NOT NULL';
$dataType['Name'] 		= 'varchar(64) NOT NULL';
$dataType['Category'] 	= 'varchar(32) NOT NULL';
$dataType['Table'] 		= 'varchar(32) NOT NULL';
$dataType['Date'] 		= 'date NOT NULL';
$dataType['Items'] 		= 'text NOT NULL';
$dataType['Count'] 		= 'int(11) unsigned NOT NULL';
$dataType['Notes'] 		= 'text NOT NULL';

$extra[]				= 'UNIQUE KEY `User_ID_Name_Category` (`User_ID`,`Name`,`Category`)';
$extra[]				= 'KEY `User_ID_Category` (`User_ID`,`Category`)';

createSQLTable($tableName, $headers, $dataType, $dataKey, 1, 1, $extra);




foreach($candidates as $currentTable => $fileName){

	
	if (is_file($fileName)){
		
		
		if ($mode == 'cli'){
			echo "\n*****************************************************************\n";
			echo "Reading file: {$fileName}\n";
		}
		
		$fp = fopen($fileName, 'r');
		
		unset($SQL_Buffer, $header, $count, $recordCount, $customIndex, $needCustomIndex);
		
		$startTime = microtime(true);
		$startMemory = memory_get_usage();
		while (!feof($fp)){
			$currentLine = fgets($fp, 1000000);
			
			if (trim($currentLine) == '') continue;
			
			$count++;
			if ($count == 1){
				$currentLine = trim($currentLine);
			}
			$row 			= str_getcsv($currentLine, "\t");

			
			if (array_size($row) > 0){
				
				if ($count == 1){
					$header = $row;
					
					
					foreach($header as $tempKey => $currentColumn){
						$header[$tempKey] = str_replace('[', '_', $header[$tempKey]);
						$header[$tempKey] = str_replace(']', '', $header[$tempKey]);
						$header[$tempKey] = str_replace('.', '_', $header[$tempKey]);
						$header[$tempKey] = str_replace(' ', '_', $header[$tempKey]);
						
						
						$firstChar = substr($header[$tempKey], 0, 1);
						if (is_numeric($firstChar)){
							$header[$tempKey] = 'Data_' . $header[$tempKey];
						}
					}
					
					
					unset($SQL, $dataType);
					foreach($header as $tempKey => $currentColumn){
						$currentSQLDataType = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Data_Type'];
						if ($currentSQLDataType == ''){
							$currentSQLDataType = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Data_Type'];
						}
						
						
						if ($currentSQLDataType == ''){
							
							
							if (endsWith(strtolower($currentColumn), 'index')){
								$currentSQLDataType = 'INTEGER(11)';
							} elseif (endsWith(strtolower($currentColumn), 'value')){
								$currentSQLDataType = 'VARCHAR(32)';
							} elseif (endsWith(strtolower($currentColumn), 'id')){
								$currentSQLDataType = 'VARCHAR(32)';
							} elseif (endsWith(strtolower($currentColumn), 'count')){
								$currentSQLDataType = 'VARCHAR(32)';
							} elseif (endsWith(strtolower($currentColumn), 'number')){
								$currentSQLDataType = 'VARCHAR(32)';
							} elseif (endsWith(strtolower($currentColumn), 'title')){
								$currentSQLDataType = 'VARCHAR(32)';
							} elseif (strtolower($currentColumn) == 'strand'){
								$currentSQLDataType = 'VARCHAR(4)';
							} else {
								$currentSQLDataType = 'TEXT';
							}
						}
						
						$dataType[$tempKey] = $currentSQLDataType;
					}
					
					$SQL_Extra = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL_Extra'];
					if ($SQL_Extra == ''){
						$SQL_Extra = $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL_Extra'];
					}

					createSQLTable($currentTable, $header, $dataType, $dataKey, 0, 1, $SQL_Extra);
					
					if ($mode == 'cli'){
						echo "The table {$currentTable} has been created.\n";
					}
					
					break;
					
				}
			
			}
			
		}
		
		
		if ($APP_CONFIG['DB_Dictionary'][$currentTable]['Empty_Table']){
			if ($mode == 'cli'){
				echo "This is an empty table, no need to import any data.\n";
			}
			
		} else {
		
			if ($mode == 'cli'){
				echo "Importing data to {$currentTable}. This step may take a while.\n";
			}
			
			$cmd = "mysqlimport -u {$BXAF_CONFIG['APP_DB_USER']} -p{$BXAF_CONFIG['APP_DB_PASSWORD']} {$BXAF_CONFIG['APP_DB_NAME']} -h{$BXAF_CONFIG['APP_DB_SERVER']} --local {$fileName} --ignore-lines 1";
			shell_exec($cmd);
			
			if ($mode == 'cli'){
				echo "Running the following command:\n";
				echo "{$cmd}\n";
			}
			
			
			if ($mode == 'cli'){
				echo "The system finished importing data to {$currentTable}.\n";
				echo "*****************************************************************\n\n\n";
			}
		} 
		
		
		
		$endTime 	= microtime(true);
		$endMemory 	= memory_get_usage();
		$timeSpent	= ($endTime - $startTime) / 60;
		
		$memoryUsed = ($endMemory - $startMemory)/(1024*1024);

		$timer[$currentTable]['Database'] 		= $APP_CONFIG['DB_Dictionary'][$currentTable]['Title'];
		$timer[$currentTable]['Time Spent'] 	= round($timeSpent, 2) . ' minutes';
		$timer[$currentTable]['Memory'] 		= round($memoryUsed, 2) . ' MB';
		
		unset($dataArray, $SQL_Buffer);
		$dataArray['Table_Name']	= $currentTable;
		$dataArray['Key']			= 'Record_Count';
		$dataArray['Value']			= $recordCount;
		$SQL_Buffer[]				= $dataArray;
		
		unset($dataArray);
		$dataArray['Table_Name']	= $currentTable;
		$dataArray['Key']			= 'Time_Spent';
		$dataArray['Value']			= round($timeSpent, 2);
		$SQL_Buffer[] 				= $dataArray;
		
		unset($dataArray);
		$dataArray['Table_Name']	= $currentTable;
		$dataArray['Key']			= 'Memory';
		$dataArray['Value']			= abs(round($memoryUsed, 2));
		$SQL_Buffer[] 				= $dataArray;
		
		unset($dataArray);
		$dataArray['Table_Name']	= $currentTable;
		$dataArray['Key']			= 'Date_Imported';
		$dataArray['Value']			= date("Y-m-d H:i:s");
		$SQL_Buffer[] 				= $dataArray;
		
		
		$SQL = getInsertMultipleSQLQuery($APP_CONFIG['Table']['Info'], $SQL_Buffer);
		execSQL($SQL);
		unset($SQL_Buffer);
		
		
		                         
		
		fclose($fp);
		unset($fp);
	
	}
	
}

?>