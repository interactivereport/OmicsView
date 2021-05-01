<?php


if (php_sapi_name() !== 'cli'){	
	exit();
}

include_once('config_init.php');

set_time_limit(0);
ini_set('memory_limit', -1);
$startMemoryUsage 	= memory_get_usage();
$startTime		 	= microtime(true);
$batchInsert 		= $BXAF_CONFIG_CUSTOM['IMPORT']['BULK_INSERT'];


/*
Status
0: Not Started
1: Completed. Ready to use
2: Processing
3: Error Found
*/
$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Job'];
$SQL 		= "SELECT count(*) FROM `{$SQL_TABLE}` WHERE `Status` = 2";
$count 		= $APP_CONFIG['SQL_CONN']->GetOne($SQL);
$count		= intval($count);

if ($count >= $BXAF_CONFIG_CUSTOM['IMPORT']['CONCURRENT']){
	echo "The system is processing {$count} job(s) now." . "\n";
	exit();	
}



echo "***************************************************\n\n";


//***********************************
// Job
//***********************************
if (true){
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Job'];
	$SQL 		= "SELECT * FROM `{$SQL_TABLE}` WHERE `Status` = 0 ORDER BY `ID` ASC LIMIT 1";
	$jobInfo	= $APP_CONFIG['SQL_CONN']->GetRow($SQL);
	
	if (array_size($jobInfo) <= 0){
		echo "There are no job available." . "\n";
		exit();	
	}
	
	$SQL 		= "SELECT count(*) FROM `{$SQL_TABLE}` WHERE (`Status` = 0) AND (`Input_Checksum` = '{$jobInfo['Input_Checksum']}')";
	$duplicatedCount = $APP_CONFIG['SQL_CONN']->GetOne($SQL);
	
	
	if ($duplicatedCount > 1){
		$SQL = "DELETE FROM  `{$SQL_TABLE}` WHERE (`Status` = 0) AND (`Input_Checksum` = '{$jobInfo['Input_Checksum']}') AND `ID` != '{$jobInfo['ID']}'";
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
	}
	
	unset($jobInfo, $duplicatedCount);
}



if (true){
	$SQL 		= "SELECT * FROM `{$SQL_TABLE}` WHERE `Status` = 0 ORDER BY `ID` ASC LIMIT 1";
	$jobInfo	= $APP_CONFIG['SQL_CONN']->GetRow($SQL);
	
	$jobInfo['Input'] 			= json_decode($jobInfo['Input'], true);
	$jobInfo['Input']['Expression_Fomat'] = intval($jobInfo['Input']['Expression_Fomat']);
	$jobInfo['Gene_Mapping']	= $jobInfo['Input']['Gene_Mapping'];
	$jobInfo['Output'] 			= NULL;
	
	$requirement = array();
	if ($jobInfo['Input']['Data_Type'] == 'Sample'){
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 1;
		$requirement['GeneLevelExpression'] = 1;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 0;
		$requirement['ComparisonData'] 		= 0;		
	} elseif ($jobInfo['Input']['Data_Type'] == 'Comparison'){
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 0;
		$requirement['GeneLevelExpression'] = 0;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 1;
		$requirement['ComparisonData'] 		= 1;
	} elseif ($jobInfo['Input']['Data_Type'] == 'Project'){
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 0;
		$requirement['GeneLevelExpression'] = 0;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 0;
		$requirement['ComparisonData'] 		= 0;
	} else {
		$requirement['Projects'] 			= 1;
		$requirement['Samples'] 			= 1;
		$requirement['GeneLevelExpression'] = 1;
		$requirement['GeneCount'] 			= 0;
		$requirement['Comparisons'] 		= 1;
		$requirement['ComparisonData'] 		= 1;
	}
	
	
	$delimiter = $jobInfo['Input']['File_Fomat'];
	if ($delimiter == 'tab'){
		$delimiter = "\t";	
	} elseif ($delimiter == 'csv'){
		$delimiter = ',';	
	} else {
		$delimiter = '';
	}
	

	echo "Updating job\n";
	
	unset($tempArray);
	$tempArray['Status'] 		= 2;
	$tempArray['Status_MySQL'] 	= 2;
	$tempArray['DateTime_Start'] 	= date('Y-m-d H:i:s');
	$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	
	$SQL = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Projects']}` WHERE `Job_ID` = {$jobInfo['ID']}";
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	
	$SQL = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Samples']}` WHERE `Job_ID` = {$jobInfo['ID']}";
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	
	$SQL = "DELETE FROM `{$APP_CONFIG['Table']['App_User_Data_Comparisons']}` WHERE `Job_ID` = {$jobInfo['ID']}";
	$APP_CONFIG['SQL_CONN']->Execute($SQL);

	
	unset($tempArray);
	
	echo "\n\n";
}



//***********************************
// Definition
//***********************************
if (true){
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Definition'];
	$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `Job_ID` = {$jobInfo['ID']}";
	$allDefinitions = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
	
	foreach($allDefinitions as $tempKey => $tempValue){
		$definition[$tempValue['Table_Standard']][$tempValue['Original']] = $tempValue;
	}
}






echo "***************************************************\n\n";

//***********************************
// Projects
//***********************************
if ($requirement['Projects']){
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Projects'];
	$SQL = "SELECT count(*) FROM `{$SQL_TABLE}`";
	$projectCountDB1 = intval($APP_CONFIG['SQL_CONN']->GetOne($SQL));
	
	$SQL = "SELECT max(`Increment`) FROM `{$SQL_TABLE}`";
	$projectCountDB2 = intval($APP_CONFIG['SQL_CONN']->GetOne($SQL));
	
	$projectCountDB = max($projectCountDB1, $projectCountDB2);
	$projectCountDB = intval($projectCountDB);
	
	echo "Gene/Protein Mapping: {$jobInfo['Gene_Mapping']}\n";
	echo "Reading Projects: {$jobInfo['Input']['Files']['Projects']['Path']}\n";
	echo "Inserting data to Projects table\n";
	$jobInfo['Output']['Imported']['Projects'] 	= 0;
	$jobInfo['Output']['Line']['Projects'] 		= 0;
	
	echo "Reading project file.\n";
	$sourceArray = readFirstFewLinesFromFile($jobInfo['Input']['Files']['Projects']['Path'], 0, 0, $delimiter);
	
	if ($delimiter == ''){
		$delimiter = guessFileDelimiter($jobInfo['Input']['Files']['Projects']['Path']);	
	}
		
	echo "Finish reading project file.\n";
	
	
	if (array_size($sourceArray['Body']) <= 0){
		echo "The project file is empty.\n";
		
		unset($errorArray);
		$errorArray['Type'] 		= 'File';
		$errorArray['Category'] 	= 'Projects';
		$errorArray['Path'] 		= $jobInfo['Input']['Files']['Projects']['Path'];
		$errorArray['Message'] 		= "The system is unable to read the project file.";
		$jobInfo['Output']['Error'][] = $errorArray;
		
		
		unset($tempArray);
		$tempArray['Status'] 		= 3;
		$tempArray['Status_MySQL'] 	= 2;
		$tempArray['DateTime_End'] 	= date('Y-m-d H:i:s');
		$tempArray['Output']		= json_encode($jobInfo['Output']);
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
		exit();	
	}
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Projects'];
	foreach($sourceArray['Body'] as $tempKey => $tempValue){
	
		$jobInfo['Output']['Line']['Projects']++;
	
		unset($dataArray);
		$dataArray['User_ID'] 			= $jobInfo['User_ID'];
		$dataArray['Date']	 			= date('Y-m-d');
		$dataArray['DateTime'] 			= date('Y-m-d H:i:s');
		$dataArray['Import_ID']			= $jobInfo['Import_ID'];
		$dataArray['Job_ID']			= $jobInfo['ID'];
		$dataArray['Permission']		= $jobInfo['Permission'];
		$dataArray['Status']			= 0;
	
		foreach($jobInfo['Input']['Choice']['Projects'] as $headerIndex => $currentSQL){
			if ($currentSQL == '') continue;
			
			$finalSQL = '';
			
			if ($currentSQL == 'BXAPP_CUSTOM_COLUMN'){
				
				$headerOrg = $jobInfo['Input']['Input_Headers']['Projects'][$headerIndex];
				$finalSQL = $definition['Projects'][$headerOrg]['Column'];
			} else {
				$finalSQL = $currentSQL;
			}
			
			if ($finalSQL != ''){
				$dataArray[$finalSQL] = trim($tempValue[$headerIndex]);
				$nonEmptyColumns['Projects'][$finalSQL] = $finalSQL;
			}
		}
		
		$dataArray['ProjectID_Original'] = $dataArray['ProjectID'];
		
		
		
		$projectCountDB++;
		$dataArray['ProjectID'] = "{$dataArray['ProjectID']}_P{$projectCountDB}";
		$dataArray['Increment'] = $projectCountDB;

		if ($projectCountDB <= 1){
			$dataArray['ProjectIndex'] = 20000001;
		}
		
		echo "projectCountDB: {$projectCountDB}\n";
		
		$SQL = getInsertSQLQuery($SQL_TABLE, $dataArray);
		
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		$jobInfo['Output']['Imported']['Projects']++;
	}
	
	if ($jobInfo['Output']['Imported']['Projects'] == 1){
		if ($dataArray['ProjectIndex'] > 0){
			$defaultProjectIndex = $dataArray['ProjectIndex'];
		} else {
			$defaultProjectIndex = $APP_CONFIG['SQL_CONN']->Insert_ID();	
		}
	}
	
	unset($sourceArray);
	echo "\n";
	
	
	echo "Indexing Project Records\n";
	
	$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE (`Job_ID` = '{$jobInfo['ID']}') AND (`User_ID` = '{$jobInfo['User_ID']}')";
	echo "SQL: {$SQL}\n";
	$temp = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
	
	$projectCount = array_size($temp);
	echo "Project Count: {$projectCount}\n";
	
	if ($projectCount == 1){
		$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE (`Job_ID` = '{$jobInfo['ID']}') AND (`User_ID` = '{$jobInfo['User_ID']}')";
		$projectReference = $APP_CONFIG['SQL_CONN']->GetRow($SQL);
	}
	
	

	foreach($temp as $projectIndex => $projectDetails){
		
		$projectDetails['ProjectIndex'] = $projectIndex;
		
		$projectIndexesLookup[strtolower($projectDetails['ProjectID_Original'])] = $projectDetails;
		
		$destinationDirectory = getInternalDataProcessedDir($jobInfo['User_ID'], $projectIndex);
		echo "Directory: {$destinationDirectory}\n";
		if (!is_dir($destinationDirectory)){
			mkdir($destinationDirectory, 0777, true);
		}
		
		$destinationDirectory = getInternalDataProcessedDir($jobInfo['User_ID'], $projectIndex) . 'bigzip/';
		echo "Directory: {$destinationDirectory}\n";
		if (!is_dir($destinationDirectory)){
			mkdir($destinationDirectory, 0777, true);
		}
		
		
		$projectName = $tempKey;
		$projectIndex = $tempValue;
		
	}

	echo "\n\n";
}

echo "***************************************************\n\n";



//***********************************
// Samples
//***********************************
if ($requirement['Samples']){
	echo "Reading Samples: {$jobInfo['Input']['Files']['Samples']['Path']}\n";
	echo "Inserting data to Samples table";
	$jobInfo['Output']['Imported']['Samples'] 	= 0;
	$jobInfo['Output']['Line']['Samples'] 		= 0;
	
	$platformType = '';
		
	$sourceArray = readFirstFewLinesFromFile($jobInfo['Input']['Files']['Samples']['Path'], 0, 0, $delimiter);

	
	if (array_size($sourceArray['Body']) <= 0){
		echo "The sample file is empty.\n";
		
		unset($errorArray);
		$errorArray['Type'] 		= 'File';
		$errorArray['Category'] 	= 'Samples';
		$errorArray['Path'] 		= $jobInfo['Input']['Files']['Samples']['Path'];
		$errorArray['Message'] 		= "The system is unable to read the sample file.";
		$jobInfo['Output']['Error'][] = $errorArray;
		
		
		unset($tempArray);
		$tempArray['Status'] 		= 3;
		$tempArray['Status_MySQL'] 	= 2;
		$tempArray['DateTime_End'] 	= date('Y-m-d H:i:s');
		$tempArray['Output']		= json_encode($jobInfo['Output']);
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
		exit();	
	}
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Samples'];
	foreach($sourceArray['Body'] as $tempKey => $tempValue){
	
		$jobInfo['Output']['Line']['Samples']++;
		
		unset($dataArray);
		$dataArray['User_ID'] 			= $jobInfo['User_ID'];
		$dataArray['Date']	 			= date('Y-m-d');
		$dataArray['DateTime'] 			= date('Y-m-d H:i:s');
		$dataArray['Import_ID']			= $jobInfo['Import_ID'];
		$dataArray['Job_ID']			= $jobInfo['ID'];
		$dataArray['Permission']		= $jobInfo['Permission'];
		
		foreach($jobInfo['Input']['Choice']['Samples'] as $headerIndex => $currentSQL){
			
			if ($currentSQL == '') continue;
	
			if ($currentSQL == 'ProjectID'){
				$dataArray[$currentSQL] 	= trim($tempValue[$headerIndex]);
				$dataArray['ProjectName'] 	= trim($tempValue[$headerIndex]);
				
				$currentReferenceProject	= $projectIndexesLookup[strtolower(trim($tempValue[$headerIndex]))];
				$dataArray['ProjectIndex'] 	= $currentReferenceProject['ProjectIndex'];

			} elseif ($currentSQL == 'PlatformName'){
				$platformType 			= getPlatformType($tempValue[$headerIndex]);
				$dataArray[$currentSQL] = trim($tempValue[$headerIndex]);
			} else {
			
				$finalSQL = '';
				
				if ($currentSQL == 'BXAPP_CUSTOM_COLUMN'){
					$headerOrg = $jobInfo['Input']['Input_Headers']['Samples'][$headerIndex];
					$finalSQL = $definition['Samples'][$headerOrg]['Column'];
				} else {
					$finalSQL = $currentSQL;
				}
				
				if ($finalSQL != ''){
					$dataArray[$finalSQL] = trim($tempValue[$headerIndex]);	
					
					$nonEmptyColumns['Samples'][$finalSQL] = $finalSQL;
					
				}

			}
		}
		
		
		$currentProjectIncrement = intval($currentReferenceProject['Increment']);
		
		if ($currentProjectIncrement <= 0){
			$currentProjectIncrement = $projectCountDB;
		}
		
		if ($currentProjectIncrement <= 0){
			$currentProjectIncrement = 1;
		}
		
		
		$dataArray['SampleID_Original'] = $dataArray['SampleID'];
		
		$dataArray['SampleID'] = "{$dataArray['SampleID']}_P{$currentProjectIncrement}";

		$dataArray['Increment'] = $currentProjectIncrement;
	
		if ($projectCount == 1){
			
			if ($dataArray['ProjectIndex'] <= 0){
				$dataArray['ProjectIndex'] = $projectReference['ProjectIndex'];
			}
			
			if ($dataArray['ProjectID'] == ''){
				$dataArray['ProjectID'] = $projectReference['ProjectID'];
			}
			
			if ($dataArray['PlatformName'] == ''){
				$dataArray['PlatformName'] = $projectReference['Platform'];
				
				if ($platformType == ''){
					$platformType = getPlatformType($projectReference['Platform']);
				}
			}
			
			if ($platformType == ''){
				if (get_gene_type() == 'Protein'){
					$platformType = 'RNA-Seq';
				}
			}
		}
		
		$SQL 			= "SELECT count(*) FROM `{$SQL_TABLE}`";
		$sampleCount1 	= intval($APP_CONFIG['SQL_CONN']->GetOne($SQL));
		
				
		$SQL 			= "SELECT max(`Increment`) FROM `{$SQL_TABLE}`";
		$sampleCount2	= intval($APP_CONFIG['SQL_CONN']->GetOne($SQL));
		
		$sampleCount	= max($sampleCount1, $sampleCount2);
		
		if ($sampleCount <= 0){
			$dataArray['SampleIndex'] = 20000001;
		}
		
		
		echo ".";
		$SQL = getInsertSQLQuery($SQL_TABLE, $dataArray);
		
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		$jobInfo['Output']['Imported']['Samples']++;
	}
	unset($sourceArray);
	
	
	$SQL = "SELECT `SampleIndex`, `SampleID`, `ProjectIndex`, `SampleID_Original` FROM `{$SQL_TABLE}` WHERE (`Job_ID` = '{$jobInfo['ID']}') AND (`User_ID` = '{$jobInfo['User_ID']}')";
	$temp = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
	
	foreach($temp as $sampleIndex => $sampleInfo){
		
		$sampleIDOriginal			= $sampleInfo['SampleID_Original'];
		
		$sampleInfo['SampleIndex'] = $sampleIndex;
		
		$sampleIndexesLookup[strtolower($sampleIDOriginal)] = $sampleInfo;
		
		
		$projectIndex 	= $sampleInfo['ProjectIndex'];
		
		
		
	}
	echo "\n\n";
}

if ($platformType == ''){
	if (get_gene_type() == 'Protein'){
		$platformType = 'RNA-Seq';
	}
}

echo "***************************************************\n\n";



//***********************************
// Comparisons
//***********************************
if ($requirement['Comparisons']){
	echo "Reading Comparisons: {$jobInfo['Input']['Files']['Comparisons']['Path']}\n";
	echo "Inserting data to Comparisons table";
	$jobInfo['Output']['Imported']['Comparisons'] 	= 0;
	$jobInfo['Output']['Line']['Comparisons']		= 0;
	
	$sourceArray = readFirstFewLinesFromFile($jobInfo['Input']['Files']['Comparisons']['Path'], 0, 0, $delimiter);
	
	
	if (array_size($sourceArray['Body']) <= 0){
		echo "The comparison file is empty.\n";
		
		unset($errorArray);
		$errorArray['Type'] 		= 'File';
		$errorArray['Category'] 	= 'Comparisons';
		$errorArray['Path'] 		= $jobInfo['Input']['Files']['Comparisons']['Path'];
		$errorArray['Message'] 		= "The system is unable to read the comparison file.";
		$jobInfo['Output']['Error'][] = $errorArray;
		
		
		unset($tempArray);
		$tempArray['Status'] 		= 3;
		$tempArray['Status_MySQL'] 	= 2;
		$tempArray['DateTime_End'] 	= date('Y-m-d H:i:s');
		$tempArray['Output']		= json_encode($jobInfo['Output']);
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
		exit();	
	}
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_Comparisons'];
	
	foreach($sourceArray['Body'] as $tempKey => $tempValue){
		$jobInfo['Output']['Line']['Comparisons']++;
		
		unset($dataArray);
		$dataArray['User_ID'] 			= $jobInfo['User_ID'];
		$dataArray['Date']	 			= date('Y-m-d');
		$dataArray['DateTime'] 			= date('Y-m-d H:i:s');
		$dataArray['Import_ID']			= $jobInfo['Import_ID'];
		$dataArray['Job_ID']			= $jobInfo['ID'];
		$dataArray['Permission']		= $jobInfo['Permission'];
		
		
		
		foreach($jobInfo['Input']['Choice']['Comparisons'] as $headerIndex => $currentSQL){
			
			if ($currentSQL == '') continue;
	
			if ($currentSQL == 'ProjectID'){
				$dataArray[$currentSQL] 	= trim($tempValue[$headerIndex]);
				$dataArray['ProjectName'] 	= trim($tempValue[$headerIndex]);
				
				$currentReferenceProject	= $projectIndexesLookup[strtolower(trim($tempValue[$headerIndex]))];
				$dataArray['ProjectIndex'] 	= $currentReferenceProject['ProjectIndex'];
			} else {
				
				$finalSQL = '';
				
				if ($currentSQL == 'BXAPP_CUSTOM_COLUMN'){
					$headerOrg = $jobInfo['Input']['Input_Headers']['Comparisons'][$headerIndex];
					$finalSQL = $definition['Comparisons'][$headerOrg]['Column'];
				} else {
					$finalSQL = $currentSQL;
				}
				
				if ($finalSQL != ''){
					$dataArray[$finalSQL] = trim($tempValue[$headerIndex]);	
					$nonEmptyColumns['Comparisons'][$finalSQL] = $finalSQL;
				}
				
				
			}
		}
		
		
		$currentProjectIncrement = intval($currentReferenceProject['Increment']);
		
		if ($currentProjectIncrement <= 0){
			$currentProjectIncrement = $projectCountDB;
		}
		
		if ($currentProjectIncrement <= 0){
			$currentProjectIncrement = 1;
		}

		
		$dataArray['ComparisonID_Original'] = $dataArray['ComparisonID'];
		
		$dataArray['ComparisonID'] = "{$dataArray['ComparisonID']}_P{$currentProjectIncrement}";
		
		$dataArray['Increment'] = $currentProjectIncrement;
		
		if ($projectCount == 1){
			
			if ($dataArray['ProjectIndex'] <= 0){
				$dataArray['ProjectIndex'] = $projectReference['ProjectIndex'];
			}
			
			if ($dataArray['ProjectID'] == ''){
				$dataArray['ProjectID'] = $projectReference['ProjectID'];
			}
			
			if ($dataArray['PlatformName'] == ''){
				$dataArray['PlatformName'] = $projectReference['Platform'];
			}
				
		}
		
		if ($dataArray['ProjectName'] == ''){
			$dataArray['ProjectName'] = $dataArray['ProjectID'];
		}
		
		
		if (true){
			
			if (true){
				$dataArray['Case_SampleIDs']		= splitComparisonSampleIDs($dataArray['Case_SampleIDs']);
				$dataArray['Case_SampleID_Count'] 	= array_size($dataArray['Case_SampleIDs']);
				
				foreach($dataArray['Case_SampleIDs'] as $tempKeyXX => $tempValueXX){
					$dataArray['Case_SampleIDs'][$tempKeyXX] = "{$tempValueXX}_P{$dataArray['Increment']}";
				}
				
				$dataArray['Case_SampleIDs'] = implode(';', $dataArray['Case_SampleIDs']);
			}

			if (true){
				$dataArray['Control_SampleIDs']			= splitComparisonSampleIDs($dataArray['Control_SampleIDs']);
				$dataArray['Control_SampleID_Count']	= array_size($dataArray['Control_SampleIDs']);
				
				foreach($dataArray['Control_SampleIDs'] as $tempKeyXX => $tempValueXX){
					$dataArray['Control_SampleIDs'][$tempKeyXX] = "{$tempValueXX}_P{$dataArray['Increment']}";
				}
				
				$dataArray['Control_SampleIDs'] = implode(';', $dataArray['Control_SampleIDs']);
			}
			
			
			$dataArray['SampleID_Count'] = splitComparisonSampleIDs($dataArray['Case_SampleIDs'], $dataArray['Control_SampleIDs']);
			$dataArray['SampleID_Count'] = array_size($dataArray['SampleID_Count']);
			
		}
		
		$SQL 				= "SELECT count(*) FROM `{$SQL_TABLE}`";
		$comparisonCount1 	= intval($APP_CONFIG['SQL_CONN']->GetOne($SQL));
		
		$SQL 				= "SELECT max(`Increment`) FROM `{$SQL_TABLE}`";
		$comparisonCount2	= intval($APP_CONFIG['SQL_CONN']->GetOne($SQL));
		
		$comparisonCount	= max($comparisonCount1, $comparisonCount2);
		
		if ($comparisonCount <= 0){
			$dataArray['ComparisonIndex'] = 20000001;
		}

		
		
		echo ".";
		$SQL = getInsertSQLQuery($SQL_TABLE, $dataArray);
		
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		$jobInfo['Output']['Imported']['Comparisons']++;
	}
	unset($sourceArray);
	
	$SQL = "SELECT `ComparisonIndex`, `ComparisonID`, `ComparisonID_Original`, `ProjectIndex` FROM `{$SQL_TABLE}` WHERE (`Job_ID` = '{$jobInfo['ID']}') AND (`User_ID` = '{$jobInfo['User_ID']}')";
	$temp = $APP_CONFIG['SQL_CONN']->GetAssoc($SQL);
	
	foreach($temp as $comparisonIndex => $comparisonInfo){
		
		$comparisonInfo['ComparisonIndex'] = $comparisonIndex;
		
		
		$comparisonID_Standard = trim(strtolower($comparisonInfo['ComparisonID_Original']));
		
		$comparisonID_Standard = sanitizeColumnHeader2($comparisonID_Standard);
		
		
		$comparisonIndexesLookup[$comparisonID_Standard] = $comparisonInfo;
		
		
		
		
		
		$projectIndex 		= $tempValue['ProjectIndex'];
		
		
	}
	echo "\n\n";
}



echo "***************************************************\n\n";


if (true){
	echo "Updating MySQL records\n";
	unset($tempArray);
	$tempArray['Status_MySQL'] 	= 1;
	$tempArray['Output'] 		= json_encode($jobInfo['Output']);
	$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
	$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	unset($tempArray);
	
	echo "\n\n";
}


echo "***************************************************\n\n";


//***********************************
// Comparison Data
//***********************************
if ($requirement['ComparisonData']){
	echo "Reading ComparisonData: {$jobInfo['Input']['Files']['ComparisonData']['Path']}\n";
	echo "Preparing the csv file.\n";
	
	$jobInfo['Output']['Imported']['ComparisonData'] 	= 0;
	$jobInfo['Output']['Line']['ComparisonData']		= 0;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_ComparisonData'];
	$fp = fopen($jobInfo['Input']['Files']['ComparisonData']['Path'], 'r');
	
	unset($header, $headerCount, $outgoingSQL, $outgoingSQLCounter);
	unset($filePointer, $filePointerComp, $filesToCompress, $numericalCheck);
	while (!feof($fp)){
		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (strpos('#', $currentLine) === 0) continue;
	
		
		
		if (!isset($header)){
			
			$currentLineHeader = $currentLine;
			
			$header = str_getcsv(trim($currentLine), $delimiter);
			
			$header = array_map('trim', $header);
			
			$headerCount = array_size($header);
			
			
			continue;
		} else {
			
			$jobInfo['Output']['Line']['ComparisonData']++;
			
			$currentRow = str_getcsv($currentLine, $delimiter);
			
			$currentRow = array_map('trim', $currentRow);
			
			if ($headerCount == array_size($currentRow)){
				
				unset($dataArray);
				$dataArray['User_ID'] 			= $jobInfo['User_ID'];
				$dataArray['Date']	 			= date('Y-m-d');
				$dataArray['DateTime'] 			= date('Y-m-d H:i:s');
				$dataArray['Import_ID']			= $jobInfo['Import_ID'];
				$dataArray['Job_ID']			= $jobInfo['ID'];
				
				$dataArray['Log2FoldChange']	= 'NA';
				$dataArray['PValue']			= 'NA';
				$dataArray['AdjustedPValue']	= 'NA';
				$dataArray['NumeratorValue']	= 'NA';
				$dataArray['DenominatorValue']	= 'NA';

				foreach($jobInfo['Input']['Choice']['ComparisonData'] as $headerIndex => $currentSQL){
					
					if ($currentSQL == '') continue;
			
					if ($currentSQL == 'Gene'){

						$geneIndex					= guessGeneIndex($currentRow[$headerIndex], $jobInfo['Gene_Mapping']);
						
						$dataArray['GeneIndex']		= $geneIndex;
						$dataArray['GeneID']		= $currentRow[$headerIndex];
					} elseif ($currentSQL == 'ComparisonID'){
						
						$comparisonID_Standard 	= trim(strtolower($currentRow[$headerIndex]));
						$comparisonID_Standard 	= sanitizeColumnHeader2($comparisonID_Standard);
						$comparisonInfo 		= $comparisonIndexesLookup[$comparisonID_Standard];
						

						if (array_size($comparisonInfo) <= 0){
							$badProjectID = $currentRow[$headerIndex];
							continue;
						}
						
						$dataArray['ComparisonIndex']	= $comparisonInfo['ComparisonIndex'];
						$dataArray['ProjectIndex']		= $comparisonInfo['ProjectIndex'];
						
					} else {
						$dataArray[$currentSQL] = trim($currentRow[$headerIndex]);	
						
						if (is_numeric($dataArray[$currentSQL])){
							$numericalCheck[$currentSQL] = true;
						}
						
					}
				}
				
				if (($dataArray['ProjectIndex'] <= 0) && ($defaultProjectIndex > 0)){
					$dataArray['ProjectIndex'] = $defaultProjectIndex;
				}
				
				
				
				if ($dataArray['ProjectIndex'] <= 0){
						
					echo "Error! The project index is empty.\n";
						
					if (true){
						unset($errorArray);
						$errorArray['Type'] 		= 'ComparisonData';
						$errorArray['ProjectIndex'] = 0;
						$errorArray['Category'] 	= 'ComparisonData';
						$errorArray['Path'] 		= $jobInfo['Input']['Files']['ComparisonData']['Path'];
						$errorArray['Message'] 		= "The project ID in the comparison data ({$badProjectID}) is not available in the project file.";
						$jobInfo['Output']['Error'][] = $errorArray;
					}
					
					
					if (true){
						unset($tempArray);
						$tempArray['Status'] 					= 3;
						$tempArray['Status_Tabix_Comparison'] 	= 3;
						$tempArray['Output']					= json_encode($jobInfo['Output']);
						
						$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
						$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
						$APP_CONFIG['SQL_CONN']->Execute($SQL);
						unset($tempArray);
						exit();
					}
					
					
				}
				
				
				
				
				
				if ($dataArray['GeneIndex'] == -1){
					

					$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Error_Log/Job_{$jobInfo['ID']}/";					
					if (!is_dir($destinationDirectory)){
						mkdir($destinationDirectory, 0777, true);
					}
					
					$errorLog = "{$destinationDirectory}ComparisonData_Unknown_Gene.csv";
					
					if (!isset($jobInfo['Output']['Warning']['ComparisonData'])){
						unset($errorArray);
						$errorArray['Type'] 		= 'ComparisonData';
						$errorArray['Category'] 	= 'ComparisonData_Unknown_Gene';
						$errorArray['Path'] 		= $errorLog;
						if (get_gene_type() == 'Gene'){
							$errorArray['Message'] 		= "The comparison data file has unknown genes.";
						} elseif (get_gene_type() == 'Protein'){
							$errorArray['Message'] 		= "The comparison data file has unknown proteins.";
						}

						$jobInfo['Output']['Warning']['ComparisonData'] = $errorArray;
						
						file_put_contents($errorLog, "{$currentLineHeader}");
					}
					
					$jobInfo['Output']['Warning']['ComparisonData']['Count']++;
					
					
					file_put_contents($errorLog, "{$currentLine}", FILE_APPEND);
					
					
					unset($dataArray);
					continue;
				} else {
					$jobInfo['Output']['Summary']['ComparisonData']['GeneIndex'][$geneIndex]++;
				}
				
				
				$jobInfo['Output']['Imported']['ComparisonData']++;
				
				if (!isset($filePointer[$dataArray['ProjectIndex']])){
					//echo "File Pointer: {$dataArray['ProjectIndex']}\n";
					$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $dataArray['ProjectIndex']) . 'ComparisonData.txt';
					$filesToCompress[$dataArray['ProjectIndex']] = $tempFile;
					//echo "Location: {$tempFile}\n";
					$filePointer[$dataArray['ProjectIndex']] = fopen($tempFile, 'w');
					
					$tempArray = array('ComparisonIndex', 'GeneIndex', 'Name', 'Log2FoldChange', 'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue');
					fputcsv($filePointer[$dataArray['ProjectIndex']], $tempArray, "\t");
				}
				
				
				
				
				
				if (true){
									
					$tempArray = array(	$dataArray['ComparisonIndex'], 
										$dataArray['GeneIndex'], 
										$dataArray['GeneID'], 
										$dataArray['Log2FoldChange'], 
										$dataArray['PValue'], 
										$dataArray['AdjustedPValue'],
										$dataArray['NumeratorValue'],
										$dataArray['DenominatorValue']
										);
										
										
										
					fputcsv($filePointer[$dataArray['ProjectIndex']], $tempArray, "\t");
				}
				
				
				
				if (!isset($filePointerComp[$dataArray['ComparisonIndex']])){
					//echo "File Pointer: {$dataArray['ProjectIndex']}\n";
					$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$dataArray['ComparisonIndex']}/";
					
					if (!is_dir($destinationDirectory)){
						mkdir($destinationDirectory, 0777, true);
					}
					
					$tempFile = "{$destinationDirectory}comp_{$dataArray['ComparisonIndex']}.csv";
					
					$fileComparisons[$dataArray['ComparisonIndex']]['Dir'] 	= $destinationDirectory;
					$fileComparisons[$dataArray['ComparisonIndex']]['File'] = $tempFile;
					$fileComparisons[$dataArray['ComparisonIndex']]['FileName'] = "comp_{$dataArray['ComparisonIndex']}.csv";
					
					$jobInfo['Output']['GO']['csv'][$dataArray['ComparisonIndex']] = $tempFile;
					
										
					//echo "Location: {$tempFile}\n";
					$filePointerComp[$dataArray['ComparisonIndex']] = fopen($tempFile, 'w');
					
					$tempArray = array('GeneName', 'Log2FoldChange', 'PValue', 'AdjustedPValue');
					
					fputcsv($filePointerComp[$dataArray['ComparisonIndex']], $tempArray);
				}
				
				if (true){
					
					$tempArray = array(	getGeneNameFromGeneIndex($dataArray['GeneIndex']), 
										$dataArray['Log2FoldChange'], 
										$dataArray['PValue'], 
										$dataArray['AdjustedPValue'],
										);
					fputcsv($filePointerComp[$dataArray['ComparisonIndex']], $tempArray);
				}
								
				
				
				$outgoingSQLCounter++;
				$outgoingSQL[] = $dataArray;
				unset($dataArray);
			}
		}
	}
	fclose($fp);
	
	
	
	foreach($filePointer as $tempKey => $tempValue){
		fclose($tempValue);
	}
	
	foreach($filePointerComp as $tempKey => $tempValue){
		fclose($tempValue);
	}
	
	
	unset($check);
	$check['Log2FoldChange']	= "The system could not find any numerical log2 fold change.";
	$check['PValue']			= "The system could not find any numerical p-value.";
	$check['AdjustedPValue']	= "The system could not find any numerical adjusted p-value.";
	
	foreach($check as $currentSQL => $errorMessage){
		if (!$numericalCheck[$currentSQL]){
			unset($errorArray);
			$errorArray['Type'] 		= 'ComparisonData';
			$errorArray['Category'] 	= 'ComparisonData_Missing_Numeric_Data';
			$errorArray['Message'] 		= $errorMessage;
			$jobInfo['Output']['Warning']["ComparisonData_{$currentSQL}"] = $errorArray;
		}
	}

	
	if (true){
		unset($tempArray);
		$tempArray['Status_Tabix_Comparison'] 	= 2;
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$tempArray['Output'] 		= json_encode($jobInfo['Output']);
		$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
	}
	
	
	unset($tabixErrorCount);
	foreach($filesToCompress as $currentProjectIndex => $currentTxtFile){
		
		echo "Running Comparison Tabix for Project Index: {$currentProjectIndex}\n\n";
		
		if (true){
			$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'bigzip/ComparisonData.txt.gz';
			$jobInfo['Output']['Tabix'][$currentProjectIndex]['ComparisonData.txt.gz'] = $tempFile;
			
			$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$currentTxtFile} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k2,2n -k1,1n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
			echo "Running command[1]: {$cmd}\n\n";
			$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['ComparisonData.txt.gz'][] = $cmd;
			shell_exec($cmd);
			
			$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 2 -b 1 -e 1 -0 {$tempFile}";
			echo "Running command[2]: {$cmd}\n\n";
			$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['ComparisonData.txt.gz'][] = $cmd;
			shell_exec($cmd);

			
			
			if (!is_file($tempFile) || filesize($tempFile) <= 0){
					
				echo "Error! The tabix file is empty.\n";
					
				$tabixErrorCount++;	
					
				unset($errorArray);
				$errorArray['Type'] 		= 'Tabix';
				$errorArray['ProjectIndex'] = $currentProjectIndex;
				$errorArray['Category'] 	= 'ComparisonData.txt.gz';
				$errorArray['Path'] 		= $tempFile;
				$errorArray['Message'] 		= "The tabix index file is empty.";
				$jobInfo['Output']['Error'][] = $errorArray;
				
			}
			
			
		}
		
		
		if (true){
			$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'bigzip/ComparisonData.txt.Sample.gz';
			$jobInfo['Output']['Tabix'][$currentProjectIndex]['ComparisonData.txt.Sample.gz'] = $tempFile;
			
			
			$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$currentTxtFile} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k1,1n -k2,2n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
			$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['ComparisonData.txt.Sample.gz'][] = $cmd;
			echo "Running command[3]: {$cmd}\n\n";
			shell_exec($cmd);	
			
			
			$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 1 -b 2 -e 2 -0 {$tempFile}";
			echo "Running command[4]: {$cmd}\n\n";
			$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['ComparisonData.txt.Sample.gz'][] = $cmd;
			shell_exec($cmd);
			
			if (!is_file($tempFile) || filesize($tempFile) <= 0){
				echo "Error! The tabix file is empty.\n";
				
				$tabixErrorCount++;	
				
				unset($errorArray);
				$errorArray['Type'] 		= 'Tabix';
				$errorArray['ProjectIndex'] = $currentProjectIndex;
				$errorArray['Category'] 	= 'ComparisonData.txt.Sample.gz';
				$errorArray['Path'] 		= $tempFile;
				$errorArray['Message'] 		= "The tabix index file is empty.";
				$jobInfo['Output']['Error'][] = $errorArray;
			}
			
		}
		
	}
	
	
	
	if (true){
		unset($tempArray);
		if ($tabixErrorCount == 0){
			$tempArray['Status_Tabix_Comparison'] 	= 1;
		} else {
			$tempArray['Status_Tabix_Comparison'] 	= 3;
		}
		
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
	}
	
	
	echo "\n\n";
}


echo "***************************************************\n\n";


//***********************************
// GeneCount
//***********************************
if ($requirement['GeneLevelExpression'] && file_exists($jobInfo['Input']['Files']['GeneCount']['Path'])){
	
	unset($geneHeaderKey, $sampleHeaderKeyCount);
	foreach($jobInfo['Input']['Choice']['GeneLevelExpression'] as $tempKey => $tempValue){
		
		if ($tempValue == '') continue;
		
		if ($tempValue == 'Gene'){
			if (!isset($geneHeaderKey)){
				$geneHeaderKey = $tempKey;
			}
		}
		
		if ($tempValue == 'SampleID'){
			$sampleHeaderKeyCount++;
		}
		
	}
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_GeneCount'];
	$fp = fopen($jobInfo['Input']['Files']['GeneCount']['Path'], 'r');
	unset($header, $headerCount, $outgoingSQL, $outgoingSQLCounter);
	
	unset($geneCountIndex);
	while (!feof($fp)){

		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (strpos('#', $currentLine) === 0) continue;
		
		if (!isset($header)){
			
			$currentLineHeader = $currentLine;
			
			$header = str_getcsv(trim($currentLine), $delimiter);
			
			$header = array_map('trim', $header);
			
			$headerCount = array_size($header);

			continue;
		} else {
			
			$jobInfo['Output']['Line']['GeneCount'] += $sampleHeaderKeyCount;
			
			$currentRow = str_getcsv($currentLine, $delimiter);
			
			$currentRow = array_map('trim', $currentRow);
			
			if ($headerCount == array_size($currentRow)){
				
				unset($dataArray);
				
				$geneID 	= $currentRow[$geneHeaderKey];
				$geneIndex 	= guessGeneIndex($geneID, $jobInfo['Gene_Mapping']);
				
				
				
				
				unset($currentRow[$geneHeaderKey]);
			
				$tempCount = -1;
				foreach($currentRow as $tempKey => $tempValue){
					
					if ($jobInfo['Input']['Choice']['GeneLevelExpression'][$tempKey] == 'SampleID'){
						
						if (isset($sampleIndexesLookup[strtolower(trim($header[$tempKey]))])){
							$sampleInfo = $sampleIndexesLookup[strtolower(trim($header[$tempKey]))];
						} else {
							continue;	
						}
						
						
						$currentSampleIndex = $sampleInfo['SampleIndex'];
						
						$geneCountIndex[$geneIndex][$currentSampleIndex] = $tempValue;

	
					}
				}
				
				
			}
		}
		
	}
	
}



//***********************************
// GeneLevelExpression
//***********************************
if ($requirement['GeneLevelExpression']){
	echo "Reading GeneLevelExpression: {$jobInfo['Input']['Files']['GeneLevelExpression']['Path']}\n";
	echo "Preparing the csv file.\n";
	$jobInfo['Output']['Imported']['GeneLevelExpression'] = 0;
	$jobInfo['Output']['Line']['GeneLevelExpression'] = 0;
	
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['App_User_Data_GeneLevelExpression'];
	$fp = fopen($jobInfo['Input']['Files']['GeneLevelExpression']['Path'], 'r');
	unset($header, $headerCount, $outgoingSQL, $outgoingSQLCounter);
	unset($filePointer, $filesToCompress, $filesToCompress_TPM, $numericalCheck, $TPMFile_Input, $TPMFile_Output);
	unset($numericalCheck);
	
	if (!$jobInfo['Input']['Expression_Fomat']){
		unset($geneHeaderKey, $sampleHeaderKeyCount);
		foreach($jobInfo['Input']['Choice']['GeneLevelExpression'] as $tempKey => $tempValue){
			
			if ($tempValue == '') continue;
			
			if ($tempValue == 'Gene'){
				if (!isset($geneHeaderKey)){
					$geneHeaderKey = $tempKey;
				}
			}
			
			if ($tempValue == 'SampleID'){
				$sampleHeaderKeyCount++;
			}
			
		}
	
		while (!feof($fp)){
	
			$currentLine = fgets($fp, 1000000);
				
			if (trim($currentLine) == '') continue;
			
			if (strpos('#', $currentLine) === 0) continue;
			
			if (!isset($header)){
				
				$currentLineHeader = $currentLine;
				
				$header = str_getcsv(trim($currentLine), $delimiter);
				
				$header = array_map('trim', $header);
				
				$headerCount = array_size($header);
				
				
				continue;
			} else {
				
				$jobInfo['Output']['Line']['GeneLevelExpression'] += $sampleHeaderKeyCount;
				
				$currentRow = str_getcsv($currentLine, $delimiter);
				
				$currentRow = array_map('trim', $currentRow);
				
				if ($headerCount == array_size($currentRow)){
					
					unset($dataArray);
					
					
					$geneID 	= $currentRow[$geneHeaderKey];
					$geneIndex 	= guessGeneIndex($geneID, $jobInfo['Gene_Mapping']);
					
					unset($currentRow[$geneHeaderKey]);
					if ($geneIndex == -1){
	
						$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Error_Log/Job_{$jobInfo['ID']}/";					
						if (!is_dir($destinationDirectory)){
							mkdir($destinationDirectory, 0777, true);
						}
						
						$errorLog = "{$destinationDirectory}GeneLevelExpression_Unknown_Gene.csv";
						
						if (!isset($jobInfo['Output']['Warning']['GeneLevelExpression'])){
							unset($errorArray);
							$errorArray['Type'] 		= 'GeneLevelExpression';
							$errorArray['Category'] 	= 'GeneLevelExpression_Unknown_Gene';
							$errorArray['Path'] 		= $errorLog;
							if (get_gene_type() == 'Gene'){
								$errorArray['Message'] 	= "The gene level expression file has unknown genes.";	
							} elseif (get_gene_type() == 'Protein'){
								$errorArray['Message'] 	= "The protein level expression file has unknown proteins.";
							}
							$jobInfo['Output']['Warning']['GeneLevelExpression'] = $errorArray;
							
							file_put_contents($errorLog, "{$currentLineHeader}");
						}
						
						$jobInfo['Output']['Warning']['GeneLevelExpression']['LineCount']++;
	
						$jobInfo['Output']['Warning']['GeneLevelExpression']['Count'] += $sampleHeaderKeyCount;
						
						
						
						file_put_contents($errorLog, "{$currentLine}", FILE_APPEND);
						
						continue;
					} else {
						$jobInfo['Output']['Summary']['GeneLevelExpression']['GeneIndex'][$geneIndex]++;
					}
				
					unset($dataArray);
					$tempCount = -1;
					foreach($currentRow as $tempKey => $tempValue){
						
						if ($jobInfo['Input']['Choice']['GeneLevelExpression'][$tempKey] == 'SampleID'){
							$tempCount++;
							$dataArray[$tempCount]['User_ID'] 			= $jobInfo['User_ID'];
							$dataArray[$tempCount]['Date']	 			= date('Y-m-d');
							$dataArray[$tempCount]['DateTime'] 			= date('Y-m-d H:i:s');
							$dataArray[$tempCount]['Import_ID']			= $jobInfo['Import_ID'];
							$dataArray[$tempCount]['Job_ID']			= $jobInfo['ID'];
							$dataArray[$tempCount]['GeneIndex']			= $geneIndex;
							$dataArray[$tempCount]['Value']				= $tempValue;
							
							if ($dataArray[$tempCount]['Value'] === ''){
								$dataArray[$tempCount]['Value'] = 'NA';	
							}
							
							if (isset($sampleIndexesLookup[strtolower(trim($header[$tempKey]))])){
								$sampleInfo = $sampleIndexesLookup[strtolower(trim($header[$tempKey]))];
							} else {
								
								//Missing Sample ID
								$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Error_Log/Job_{$jobInfo['ID']}/";					
								if (!is_dir($destinationDirectory)){
									mkdir($destinationDirectory, 0777, true);
								}
								
								$errorLog = "{$destinationDirectory}GeneLevelExpression_Unknown_SampleID.csv";
								
								if (!isset($jobInfo['Output']['Warning']['GeneLevelExpression_SampleID'])){
									unset($errorArray);
									$errorArray['Type'] 		= 'GeneLevelExpression';
									$errorArray['Category'] 	= 'GeneLevelExpression_Unknown_SampleID';
									$errorArray['Path'] 		= $errorLog;
									if (get_gene_type() == 'Gene'){
										$errorArray['Message'] 	= "The gene level expression file has unknown sample IDs.";	
									} elseif (get_gene_type() == 'Protein'){
										$errorArray['Message'] 	= "The protein level expression file has unknown sample IDs.";	
									}
									
									$jobInfo['Output']['Warning']['GeneLevelExpression_SampleID'] = $errorArray;
									
									file_put_contents($errorLog, "{$currentLineHeader}");
								}
								
								$jobInfo['Output']['Warning']['GeneLevelExpression_SampleID']['LineCount']++;
			
								$jobInfo['Output']['Warning']['GeneLevelExpression_SampleID']['Count']++;
								
								file_put_contents($errorLog, "{$currentLine}", FILE_APPEND);
								
								continue;	
							}
							
							$dataArray[$tempCount]['SampleIndex']		= $sampleInfo['SampleIndex'];
							$dataArray[$tempCount]['ProjectIndex']		= $sampleInfo['ProjectIndex'];
							
							if (($dataArray[$tempCount]['ProjectIndex'] <= 0) && ($defaultProjectIndex > 0)){
								$dataArray[$tempCount]['ProjectIndex'] = $defaultProjectIndex;
							}
							
							
							$dataArray[$tempCount]['Count']				= 'NA';
							
							if (isset($geneCountIndex[$geneIndex][$sampleInfo['SampleIndex']])){
								$dataArray[$tempCount]['Count']			= $geneCountIndex[$geneIndex][$sampleInfo['SampleIndex']];	
							}
							
							
							
							if (true){
								
								$currentTempValue = $dataArray[$tempCount];
								$dataArray = NULL;
								unset($dataArray);
								
								
								$jobInfo['Output']['Imported']['GeneLevelExpression']++;
							
								$currentProjectIndex = $currentTempValue['ProjectIndex'];
								
								if (($currentProjectIndex < 0) && ($defaultProjectIndex > 0)){
									$currentProjectIndex = $defaultProjectIndex;
								}
						
								$currentProjectIndex = intval($currentProjectIndex);
								
								if (!isset($filePointer[$currentProjectIndex])){
									echo "File Pointer (currentProjectIndex): {$currentProjectIndex}\n";
									$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'GeneLevelExpression.txt';
									$filesToCompress[$currentProjectIndex] = $tempFile;
									echo "Location (tempFile): {$tempFile}\n";
									$filePointer[$currentProjectIndex] = fopen($tempFile, 'w');
									
									if ($platformType == 'Microarray'){
										$tempArray = array('SampleIndex', 'GeneIndex', 'Value');
									} else {
										//RNA-Seq
										$tempArray = array('SampleIndex', 'GeneIndex', 'FPKM', 'Count');
										
										if ($BXAF_CONFIG['HAS_TPM_DATA']){
											$TPMFile_Input 	= getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'GeneLevelExpression.txt';
											$TPMFile_Output = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'GeneLevelExpression-TPM.txt';
											$filesToCompress_TPM[$currentProjectIndex] = $TPMFile_Output;
										}
									}
									
									$jobInfo['GeneLevelExpression_Details'] = array('Headers' => $tempArray);
									
									fputcsv($filePointer[$currentProjectIndex], $tempArray, "\t");
									
								}
	
								
								
								if ($platformType == 'Microarray'){
									$tempArray = array(	$currentTempValue['SampleIndex'], 
													$currentTempValue['GeneIndex'], 
													$currentTempValue['Value']
													);
								} else {
									//RNA-Seq
									$tempArray = array(	$currentTempValue['SampleIndex'], 
													$currentTempValue['GeneIndex'], 
													$currentTempValue['Value'],
													$currentTempValue['Count'],
													);
								}
								
								if (is_numeric($currentTempValue['Value'])){
									$numericalCheck['Value'] = true;
								}
								
								
								fputcsv($filePointer[$currentProjectIndex], $tempArray, "\t");
								
							}
							
							unset($dataArray[$tempCount]);
		
						}
					}
					unset($dataArray);
					

					unset($dataArray);
	
					
				}
			}
		}
		fclose($fp);
	} else {
		//Table Format
		unset($geneHeaderKey, $sampleHeaderKey, $expressionValueHeaderKey, $otherHeaderKeys, $sampleHeaderKeyCount);
		foreach($jobInfo['Input']['Choice']['GeneLevelExpression'] as $tempKey => $tempValue){
			
			if ($tempValue == '') continue;
			
			if ($tempValue == 'Gene'){
				if (!isset($geneHeaderKey)){
					$geneHeaderKey = $tempKey;
				}
			}
			
			if ($tempValue == 'SampleID'){
				if (!isset($sampleHeaderKey)){
					$sampleHeaderKey = $tempKey;
				}
			}
			
			if ($tempValue == 'BXAPP_DATA_EXPRESSION'){
				if (!isset($expressionValueHeaderKey)){
					$expressionValueHeaderKey = $tempKey;
				}
			}
			
			if ($tempValue == 'BXAPP_DATA_OTHER'){
				$title = $jobInfo['Input']['Input_Headers']['GeneLevelExpression'][$tempKey];
				$otherHeaderKeys[$title] = $tempKey;
			}
			
		}

		while (!feof($fp)){
			$currentLine = fgets($fp, 1000000);
				
			if (trim($currentLine) == '') continue;
			
			if (strpos('#', $currentLine) === 0) continue;
		
			
			
			if (!isset($header)){
				
				$currentLineHeader = $currentLine;
				
				$header = str_getcsv(trim($currentLine), $delimiter);
				
				$header = array_map('trim', $header);
				
				$headerCount = array_size($header);
				
				
				continue;
			} else {
				
				$jobInfo['Output']['Line']['GeneLevelExpression']++;
				
				$currentRow = str_getcsv($currentLine, $delimiter);
				
				$currentRow = array_map('trim', $currentRow);
				
				if ($headerCount == array_size($currentRow)){
					
					unset($dataArray);
					
					if (true){
						$sampleID 		= $currentRow[$sampleHeaderKey];
						
						if (isset($sampleIndexesLookup[strtolower(trim($sampleID))])){
							$sampleInfo = $sampleIndexesLookup[strtolower(trim($sampleID))];
						} else {
							
							//Missing Sample ID
							$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Error_Log/Job_{$jobInfo['ID']}/";					
							if (!is_dir($destinationDirectory)){
								mkdir($destinationDirectory, 0777, true);
							}
							
							$errorLog = "{$destinationDirectory}GeneLevelExpression_Unknown_SampleID.csv";
							
							if (!isset($jobInfo['Output']['Warning']['GeneLevelExpression_SampleID'])){
								unset($errorArray);
								$errorArray['Type'] 		= 'GeneLevelExpression';
								$errorArray['Category'] 	= 'GeneLevelExpression_Unknown_SampleID';
								$errorArray['Path'] 		= $errorLog;
								if (get_gene_type() == 'Gene'){
									$errorArray['Message'] 	= "The gene level expression file has unknown sample IDs.";	
								} elseif (get_gene_type() == 'Protein'){
									$errorArray['Message'] 	= "The protein level expression file has unknown sample IDs.";	
								}
								$jobInfo['Output']['Warning']['GeneLevelExpression_SampleID'] = $errorArray;
								
								file_put_contents($errorLog, "{$currentLineHeader}");
							}
							
							$jobInfo['Output']['Warning']['GeneLevelExpression_SampleID']['LineCount']++;
		
							$jobInfo['Output']['Warning']['GeneLevelExpression_SampleID']['Count']++;
							
							file_put_contents($errorLog, "{$currentLine}", FILE_APPEND);
							
							continue;	
							
							
						}
						
						
						$sampleIndex	= $sampleInfo['SampleIndex'];
						$currentProjectIndex = $sampleInfo['ProjectIndex'];
						
						if (($currentProjectIndex < 0) && ($defaultProjectIndex > 0)){
							$currentProjectIndex = $defaultProjectIndex;
						}
						
						$currentProjectIndex = intval($currentProjectIndex);
						
						$dataArray['SampleIndex'] = $sampleIndex;
					}
					
					if (true){
						$geneID 	= $currentRow[$geneHeaderKey];
						$geneIndex 	= guessGeneIndex($geneID, $jobInfo['Gene_Mapping']);
	
						if ($geneIndex == -1){
		
							$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Error_Log/Job_{$jobInfo['ID']}/";					
							if (!is_dir($destinationDirectory)){
								mkdir($destinationDirectory, 0777, true);
							}
							
							$errorLog = "{$destinationDirectory}GeneLevelExpression_Unknown_Gene.csv";
							
							if (!isset($jobInfo['Output']['Warning']['GeneLevelExpression'])){
								unset($errorArray);
								$errorArray['Type'] 		= 'GeneLevelExpression';
								$errorArray['Category'] 	= 'GeneLevelExpression_Unknown_Gene';
								$errorArray['Path'] 		= $errorLog;
								
								if (get_gene_type() == 'Gene'){
									$errorArray['Message'] 	= "The gene level expression file has unknown genes.";	
								} elseif (get_gene_type() == 'Protein'){
									$errorArray['Message'] 	= "The protein level expression file has unknown proteins.";
								}
								
								
								$jobInfo['Output']['Warning']['GeneLevelExpression'] = $errorArray;
								
								file_put_contents($errorLog, "{$currentLineHeader}");
							}
							
							$jobInfo['Output']['Warning']['GeneLevelExpression']['LineCount']++;
		
							$jobInfo['Output']['Warning']['GeneLevelExpression']['Count']++;
							
							
							
							file_put_contents($errorLog, "{$currentLine}", FILE_APPEND);
							
							continue;
						} else {
							$jobInfo['Output']['Summary']['GeneLevelExpression']['GeneIndex'][$geneIndex]++;
						}
						
						$dataArray['GeneIndex'] = $geneIndex;
					}
					
					
					
					if (true){
						$expressionValue = $currentRow[$expressionValueHeaderKey];
						
						if (is_numeric($expressionValue)){
							$numericalCheck['Value'] = true;
						}
						
						if ($platformType == 'Microarray'){
							$dataArray['Value'] = $expressionValue;
						} else {
							$dataArray['FPKM'] = $expressionValue;
							$dataArray['Count'] = 'NA';
						}
					}
					
					
					foreach($otherHeaderKeys as $tempKeyX => $tempValueX){
						$dataArray[$tempKeyX] = $currentRow[$tempValueX];
					}
					
					if (($currentProjectIndex < 0) && ($defaultProjectIndex > 0)){
						$currentProjectIndex = $defaultProjectIndex;
					}
					
					$currentProjectIndex = intval($currentProjectIndex);
					
					
					if (!isset($filePointer[$currentProjectIndex])){
						echo "File Pointer (currentProjectIndex): {$currentProjectIndex}\n";
						$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'GeneLevelExpression.txt';
						$filesToCompress[$currentProjectIndex] = $tempFile;
						echo "Location (tempFile): {$tempFile}\n";
						$filePointer[$currentProjectIndex] = fopen($tempFile, 'w');
						
						if ($platformType == 'Microarray'){
							$tempArray = array_keys($dataArray);
						} else {
							//RNA-Seq
							$tempArray = array_keys($dataArray);
							
							if ($BXAF_CONFIG['HAS_TPM_DATA']){
								$TPMFile_Input 	= getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'GeneLevelExpression.txt';
								$TPMFile_Output = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'GeneLevelExpression-TPM.txt';
								$filesToCompress_TPM[$currentProjectIndex] = $TPMFile_Output;
							}
						}
						
						$jobInfo['GeneLevelExpression_Details'] = array('Headers' => $tempArray);
						
						fputcsv($filePointer[$currentProjectIndex], $tempArray, "\t");
					}

					$jobInfo['Output']['Imported']['GeneLevelExpression']++;										
					fputcsv($filePointer[$currentProjectIndex], $dataArray, "\t");
				}
			}
		}
		fclose($fp);
	}
	
	
	if (($TPMFile_Output != '')  && $BXAF_CONFIG['HAS_TPM_DATA']){
		$rootDir = dirname($argv[0]);
		$cmd = "{$BXAF_CONFIG_CUSTOM['PHP_BIN']} {$rootDir}/admin_fix_GeneFPKM_Internal.php {$TPMFile_Input} {$TPMFile_Output}";
		echo "Running command[4-TPM]: {$cmd}\n\n";
		shell_exec($cmd);
		$jobInfo['Output']['TPM'][$currentProjectIndex]['GeneLevelExpression.txt'] = $TPMFile_Input;
		$jobInfo['Output']['TPM'][$currentProjectIndex]['GeneLevelExpression-TPM.txt'] = $TPMFile_Output;
	}
	

	
	foreach($filePointer as $tempKey => $tempValue){
		fclose($tempValue);
	}
	
	unset($check);
	if (get_gene_type() == 'Protein'){
		$check['Value']	= "The system could not find any numerical protein level expression.";
	} else {
		$check['Value']	= "The system could not find any numerical gene level expression.";
	}
	

	
	foreach($check as $currentSQL => $errorMessage){
		if (!$numericalCheck[$currentSQL]){
			unset($errorArray);
			$errorArray['Type'] 		= 'GeneLevelExpression';
			$errorArray['Category'] 	= 'GeneLevelExpression_Missing_Numeric_Data';
			$errorArray['Message'] 		= $errorMessage;
			$jobInfo['Output']['Warning']["GeneLevelExpression_{$currentSQL}"] = $errorArray;
		}
	}
	
	
	if (true){
		unset($tempArray);
		$tempArray['Status_Tabix_GeneLevelExpression'] 	= 2;
		$tempArray['Output'] 		= json_encode($jobInfo['Output']);
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
	}
	echo "Finish preparing GeneLevelExpression csv file.\n";
	
	
	if (true){
	
		echo "FileToCompress: " . print_r($filesToCompress, true) . "\n";
		unset($tabixErrorCount);
		foreach($filesToCompress as $currentProjectIndex => $currentTxtFile){
			echo "Running GeneLevelExpression Tabix for Project Index: {$currentProjectIndex}\n\n";
			
			if (true){
				$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'bigzip/GeneLevelExpression.txt.gz';
				$jobInfo['Output']['Tabix'][$currentProjectIndex]['GeneLevelExpression.txt.gz'] = $tempFile;
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$currentTxtFile} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k2,2n -k1,1n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
				echo "Running command[5]: {$cmd}\n\n";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression.txt.gz'][] = $cmd;
				shell_exec($cmd);	
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 2 -b 1 -e 1 -0 {$tempFile}";
				echo "Running command[6]: {$cmd}\n\n";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression.txt.gz'][] = $cmd;
				shell_exec($cmd);
				
				if (!is_file($tempFile) || filesize($tempFile) <= 0){
					echo "Error! The tabix file is empty.\n";
					$tabixErrorCount++;	
					
					unset($errorArray);
					$errorArray['Type'] 		= 'Tabix';
					$errorArray['ProjectIndex'] = $currentProjectIndex;
					$errorArray['Category'] 	= 'GeneLevelExpression.txt.gz';
					$errorArray['Path'] 		= $tempFile;
					$errorArray['Message'] 		= "The tabix index file is empty.";
					$jobInfo['Output']['Error'][] = $errorArray;
				}
				
			}
			
			
	
			if (true){
				$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'bigzip/GeneLevelExpression.txt.Sample.gz';
				$jobInfo['Output']['Tabix'][$currentProjectIndex]['GeneLevelExpression.txt.Sample.gz'] = $tempFile;
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$currentTxtFile} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k1,1n -k2,2n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression.txt.Sample.gz'][] = $cmd;
				echo "Running command[7]: {$cmd}\n\n";
				shell_exec($cmd);	
				
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 1 -b 2 -e 2 -0 {$tempFile}";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression.txt.Sample.gz'][] = $cmd;
				echo "Running command[8]: {$cmd}\n\n";
				shell_exec($cmd);
				
				
				if (!is_file($tempFile) || filesize($tempFile) <= 0){
					echo "Error! The tabix file is empty.\n";
					$tabixErrorCount++;	
					
					unset($errorArray);
					$errorArray['Type'] 		= 'Tabix';
					$errorArray['ProjectIndex'] = $currentProjectIndex;
					$errorArray['Category'] 	= 'GeneLevelExpression.txt.Sample.gz';
					$errorArray['Path'] 		= $tempFile;
					$errorArray['Message'] 		= "The tabix index file is empty.";
					$jobInfo['Output']['Error'][] = $errorArray;
				}
			}
			
		}
	
	
		if (true){
			unset($tempArray);
			
			if ($tabixErrorCount == 0){
				$tempArray['Status_Tabix_GeneLevelExpression'] 	= 1;
			} else {
				$tempArray['Status_Tabix_GeneLevelExpression'] 	= 3;
			}
			$tempArray['Output'] 		= json_encode($jobInfo['Output']);
			$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
			$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
			$APP_CONFIG['SQL_CONN']->Execute($SQL);
			unset($tempArray);
		}
		
	}
	
	if (isset($filesToCompress_TPM) && $BXAF_CONFIG['HAS_TPM_DATA']){
	
		echo "filesToCompress_TPM: " . print_r($filesToCompress_TPM, true) . "\n";
		unset($tabixErrorCount);
		foreach($filesToCompress_TPM as $currentProjectIndex => $currentTxtFile){
			echo "Running GeneLevelExpression-TPM Tabix for Project Index: {$currentProjectIndex}\n\n";
			
			if (true){
				$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'bigzip/GeneLevelExpression-TPM.txt.gz';
				$jobInfo['Output']['Tabix'][$currentProjectIndex]['GeneLevelExpression-TPM.txt.gz'] = $tempFile;
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$currentTxtFile} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k2,2n -k1,1n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
				echo "Running command[5-TPM]: {$cmd}\n\n";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression-TPM.txt.gz'][] = $cmd;
				shell_exec($cmd);	
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 2 -b 1 -e 1 -0 {$tempFile}";
				echo "Running command[6-TPM]: {$cmd}\n\n";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression-TPM.txt.gz'][] = $cmd;
				shell_exec($cmd);
				
				if (!is_file($tempFile) || filesize($tempFile) <= 0){
					echo "Error! The tabix file is empty.\n";
					$tabixErrorCount++;	
					
					unset($errorArray);
					$errorArray['Type'] 		= 'Tabix';
					$errorArray['ProjectIndex'] = $currentProjectIndex;
					$errorArray['Category'] 	= 'GeneLevelExpression-TPM.txt.gz';
					$errorArray['Path'] 		= $tempFile;
					$errorArray['Message'] 		= "The tabix index file is empty.";
					$jobInfo['Output']['Error'][] = $errorArray;
				}
				
			}
			
			
	
			if (true){
				$tempFile = getInternalDataProcessedDir($jobInfo['User_ID'], $currentProjectIndex) . 'bigzip/GeneLevelExpression-TPM.txt.Sample.gz';
				$jobInfo['Output']['Tabix'][$currentProjectIndex]['GeneLevelExpression.txt.Sample.gz'] = $tempFile;
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$currentTxtFile} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k1,1n -k2,2n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression.txt-TPM.Sample.gz'][] = $cmd;
				echo "Running command[7-TPM]: {$cmd}\n\n";
				shell_exec($cmd);	
				
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 1 -b 2 -e 2 -0 {$tempFile}";
				$jobInfo['Output']['Tabix_Command'][$currentProjectIndex]['GeneLevelExpression.txt-TPM.Sample.gz'][] = $cmd;
				echo "Running command[8-TPM]: {$cmd}\n\n";
				shell_exec($cmd);
				
				
				if (!is_file($tempFile) || filesize($tempFile) <= 0){
					echo "Error! The tabix file is empty.\n";
					$tabixErrorCount++;	
					
					unset($errorArray);
					$errorArray['Type'] 		= 'Tabix';
					$errorArray['ProjectIndex'] = $currentProjectIndex;
					$errorArray['Category'] 	= 'GeneLevelExpression-TPM.txt.Sample.gz';
					$errorArray['Path'] 		= $tempFile;
					$errorArray['Message'] 		= "The tabix index file is empty.";
					$jobInfo['Output']['Error'][] = $errorArray;
				}
			}
			
		}
	
	
		if (true){
			unset($tempArray);
			
			if ($tabixErrorCount == 0){
				$tempArray['Status_Tabix_GeneLevelExpression-TPM'] 	= 1;
			} else {
				$tempArray['Status_Tabix_GeneLevelExpression-TPM'] 	= 3;
			}
			$tempArray['Output'] 		= json_encode($jobInfo['Output']);
			$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
			$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
			$APP_CONFIG['SQL_CONN']->Execute($SQL);
			unset($tempArray);
		}
		
	}
	

	
	echo "\n\n";
}


if (true){
	unset($tempArray);
	$tempArray['GeneLevelExpression_Details'] = json_encode($jobInfo['GeneLevelExpression_Details']);
	$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
	$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	unset($tempArray);
}
	

if (array_size($jobInfo['Output']['Error']) > 0){
	unset($tempArray);
	$tempArray['Status'] = 3;
	$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
	$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	unset($tempArray);
	exit();	
}


echo "***************************************************\n\n";

//***********************************
// PAGE + GO
//***********************************

if ($requirement['ComparisonData'] && array_size($fileComparisons) > 0){
	
	echo "PAGE + GO\n";
	
	
	$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons_Scripts/";
	
	if (!is_dir($destinationDirectory)){
		mkdir($destinationDirectory, 0777, true);
	}
	
	$scriptDir = "{$destinationDirectory}buildGO_Job_{$jobInfo['ID']}.sh";
	
	$jobInfo['Output']['GO']['script'] = $scriptDir;
	
	
	$fp = fopen($scriptDir, 'w');
	


	foreach($fileComparisons as $currentComparisonIndex => $currentCSV){
		//GO Analysis
		$cmd = "cd {$currentCSV['Dir']}\n";
		fwrite($fp, $cmd);
		
		$cmd = "{$BXAF_CONFIG_CUSTOM['RSCRIPT_BIN']} {$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Script']}/Process.Internal.Comparison.R {$currentCSV['FileName']} {$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Species']} {$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['GMT']} auto\n\n";
		fwrite($fp, $cmd);
		
		//Bonferroni_batch
		$cmd = "cd {$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/" . "\n";
		fwrite($fp, $cmd);
		
		$cmd = "{$BXAF_CONFIG_CUSTOM['RSCRIPT_BIN']} {$BXAF_CONFIG_CUSTOM['SHARE_LIBRARY_DIR']}R_Files/Bonferroni_batch.R {$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/ {$currentComparisonIndex} {$currentComparisonIndex}" . "\n";
		fwrite($fp, $cmd);
	}
	
	fclose($fp);
	chmod($scriptDir, 0755);

	
	
	if (true){
		unset($tempArray);
		$tempArray['Status_GO'] 	= 2;
		$tempArray['Status_PAGE'] 	= 2;
		$tempArray['Output'] 		= json_encode($jobInfo['Output']);
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
	}
	
	echo "Running command: {$scriptDir}\n";
	shell_exec($scriptDir);
	
	
	if (true){
		unset($tempArray);
		$tempArray['Status_GO'] 	= 1;
		$tempArray['Status_PAGE'] 	= 1;
		$tempArray['Output'] 		= json_encode($jobInfo['Output']);
		$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
		$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
		$APP_CONFIG['SQL_CONN']->Execute($SQL);
		unset($tempArray);
	}
	

	echo "\n\n";
	
}

echo "***************************************************\n\n";



if (true){
	$APP_CONFIG['SQL_CONN'] 	= bxaf_get_app_db_connection();
		
	unset($tempArray);
	$tempArray['DateTime_End'] 				= date('Y-m-d H:i:s');
	$tempArray['Status'] 					= 1;
	$tempArray['Internal_Platform_Type'] 	= $platformType;
	
	
	$SQL = getUpdateSQLQuery($APP_CONFIG['Table']['App_User_Data_Job'], $tempArray, $jobInfo['ID']);
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	unset($tempArray);
	
	
	
	$projectIndexes = array_column($projectIndexesLookup, 'ProjectIndex');
	$projectIndexes = implode(',', $projectIndexes);
	
	if ($platformType == ''){
		if (get_gene_type() == 'Protein'){
			$platformType = 'RNA-Seq';
		}
	}
	
	
	$SQL = "UPDATE `{$APP_CONFIG['Table']['App_User_Data_Projects']}` SET `Status` = 1, `Internal_Platform_Type` = '{$platformType}' WHERE `ProjectIndex` IN ({$projectIndexes})";
	$APP_CONFIG['SQL_CONN']->Execute($SQL);
	
	echo "Finish importing the data to MySQL database.\n\n";
}

if (true){
	$rootDir = dirname($argv[0]);
	$cmd = "cd {$rootDir}; {$BXAF_CONFIG_CUSTOM['PHP_BIN']} {$rootDir}/admin_patches_script-2018-03-09-App_Sample_Comparison_DiseaseState.php rebuild=0 public=0 private=1";
	echo "Running command: {$cmd}\n\n";
	shell_exec($cmd);
	
	$cmd = "cd {$rootDir}; {$BXAF_CONFIG_CUSTOM['PHP_BIN']} {$rootDir}/admin_import_combined_data.php 5";
	echo "Running command: {$cmd}\n\n";
	shell_exec($cmd);
}


clearCache(0);
clear_tabix_cache();
//cleanPlotCache();

$duration = microtime(true) - $APP_CONFIG['StartTime'];
$duration = round($duration/60, 2);

echo "Duration: {$duration} minutes\n\n";

echo "End of program\n\n";

exit();
?>