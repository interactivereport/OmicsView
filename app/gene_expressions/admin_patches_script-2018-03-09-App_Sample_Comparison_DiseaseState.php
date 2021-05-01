<?php

include_once('config_init.php');

$tableName = $APP_CONFIG['Table']['App_Sample_Comparison_DiseaseState'];


if (php_sapi_name() !== 'cli'){	
	exit();
}

if (1){

	$shouldBuildTable 		= false;
	$shouldTruncateTable 	= false;
	$shouldBuildPublicData	= false;
	$shouldBuildPrivateData	= false;
	$patchExecedLocal		= false;
	
	
	if (true){
		
		parse_str(implode('&', array_slice($argv, 1)), $_GET);
		
		
		if (array_size($_GET) <= 0){
			echo "This tool will build the Sample-Comparison-DiseaseState table." . "\n";
	
			echo "Usage:   php {$argv[0]} rebuild=1 public=1 private=1" . "\n";
			echo "   rebuild (0 or 1): Remove all values from the table first." . "\n";
			echo "   public (0 or 1):  Reindex public data" . "\n";
			echo "   private (0 or 1): Add missing private data" . "\n";
			echo "\n";
			exit();
			
			exit();	
		}
		
		if (!tableExists($tableName)){
			$shouldBuildTable 		= true;	
		}
		
		if ($_GET['rebuild']){
			$shouldTruncateTable 	= true;
		}
		
		if ($_GET['public']){
			$shouldBuildPublicData 	= true;
		}
		
		if ($_GET['private']){
			$shouldBuildPrivateData	= true;
		}
		
		

	}
	
	if ($shouldBuildTable){
		$SQL = "CREATE TABLE `{$tableName}` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `SampleIndex` int(11) unsigned NOT NULL,
  `SampleID` varchar(256) NOT NULL,
  `ComparisonIndex` int(11) unsigned NOT NULL,
  `ComparisonID` varchar(256) NOT NULL,
  `Type` varchar(32) NOT NULL,
  `ComparisonCategory` varchar(256) NOT NULL,
  `Control_DiseaseState` varchar(256) NOT NULL,
  `Case_DiseaseState` varchar(256) NOT NULL,
  `Comparison_Disease_State` varchar(256) NOT NULL,
  `Comparison_Disease_State_Abbreviation` varchar(256) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `SampleIndex` (`SampleIndex`),
  KEY `SampleID` (`SampleID`),
  KEY `ComparisonIndex` (`ComparisonIndex`),
  KEY `ComparisonID` (`ComparisonID`),
  KEY `Type` (`Type`),
  KEY `ComparisonCategory` (`ComparisonCategory`),
  KEY `Case_DiseaseState` (`Case_DiseaseState`),
  KEY `Control_DiseaseState` (`Control_DiseaseState`),
  KEY `Comparison_Disease_State` (`Comparison_Disease_State`),
  KEY `Comparison_Disease_State_Abbreviation` (`Comparison_Disease_State_Abbreviation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;";
		execSQL($SQL);
		$patchExecedLocal = true;
		
		
		echo "[Executed] Table {$tableName} has been created.\n";
		
	}
	
	if ($shouldTruncateTable){
		$SQL = "TRUNCATE TABLE {$tableName}";
		execSQL($SQL);
		$patchExecedLocal = true;
		
		
		echo "[Executed] Table {$tableName} has been emptied.\n";
		
		
	}
	
	$allData = array();
	$allIndexes = array();
	
	$internal_data_id_threshold = internal_data_id_threshold();
	if ($shouldBuildPublicData){
		
		$SQL = "DELETE FROM `{$APP_CONFIG['Table']['App_Sample_Comparison_DiseaseState']}` WHERE `SampleIndex` < {$internal_data_id_threshold}";
		execSQL($SQL);
		
		$SQL = "SELECT `ComparisonIndex`, `ComparisonID`, `Case_SampleIDs`, `Control_SampleIDs`, `Case_DiseaseState`, `Control_DiseaseState`, `ComparisonCategory` FROM `Comparisons`";
		
		$allData['Public'] = getSQL($SQL, 'GetAssoc', 'Comparisons', 0, 0);
		
		$SQL = "SELECT `SampleID`, `SampleIndex` FROM `Samples`";
		$allIndexes['Public'] = getSQL($SQL, 'GetAssoc', 'Samples', 0, 0);
		
		$patchExecedLocal = true;
		
		
		echo "[Executed] Indexing public data.\n";
	}
	
	
	
	if ($shouldBuildPrivateData){
		
		
		$SQL = "SELECT `ComparisonIndex` FROM `{$tableName}` WHERE (`ComparisonIndex` > {$internal_data_id_threshold})  GROUP BY `ComparisonIndex`";
		$comparisonIndexExists = getSQL($SQL, 'GetCol', $tableName, 0, 0);
		$comparisonIndexExists = implode(', ', $comparisonIndexExists);
		
		
		
		
		if ($comparisonIndexExists == ''){
			$SQL = "SELECT `ComparisonIndex`, `ComparisonID`, `Case_SampleIDs`, `Control_SampleIDs`, `Case_DiseaseState`, `Control_DiseaseState`, `ComparisonCategory` FROM `App_User_Data_Comparisons`";
		} else {
			$SQL = "SELECT `ComparisonIndex`, `ComparisonID`, `Case_SampleIDs`, `Control_SampleIDs`, `Case_DiseaseState`, `Control_DiseaseState`, `ComparisonCategory` FROM `App_User_Data_Comparisons` WHERE `ComparisonIndex` NOT IN ({$comparisonIndexExists})";			
		}
		
		$allData['Private'] = getSQL($SQL, 'GetAssoc', 'App_User_Data_Comparisons', 0, 0);
		
		
		$SQL = "SELECT `SampleID`, `SampleIndex` FROM `App_User_Data_Samples`";
		$allIndexes['Private'] = getSQL($SQL, 'GetAssoc', 'App_User_Data_Samples', 0, 0);
		
		$patchExecedLocal = true;
		
		echo "[Executed] Indexing private data.\n";
		
		
	}
	
	$currentModeCount['Public'] = $currentModeCount['Private'] = 0;
	foreach($allData as $currentMode => $currentDataGroup){

		$SQL_RESULTS = $allData[$currentMode];
		$sampleIndexLookup = $allIndexes[$currentMode];
		

		foreach($SQL_RESULTS as $comparisonIndex => $currentComparison){
		
		
			if (strpos($currentComparison['ComparisonCategory'], ' vs.') !== false){
				$category = explode(' vs. ', $currentComparison['ComparisonCategory']);
			} elseif (strpos($currentComparison['ComparisonCategory'], ' VS.') !== false){
				$category = explode(' VS. ', $currentComparison['ComparisonCategory']);
			} elseif (strpos($currentComparison['ComparisonCategory'], ' Vs.') !== false){
				$category = explode(' Vs. ', $currentComparison['ComparisonCategory']);
			}
			
			$caseCategory = $category[0];
			$controlCategory = $category[1];
			
			$abbreviation = '';
			if ((strpos($currentComparison['Case_DiseaseState'], '(') !== FALSE) && (strpos($currentComparison['Case_DiseaseState'], ')') !== FALSE)){
				preg_match('#\((.*?)\)#', $currentComparison['Case_DiseaseState'], $match);
				$abbreviation = trim($match[1]);
			} else {
				$abbreviation = $currentComparison['Case_DiseaseState'];	
			}
			
			
			if ($currentComparison['Case_SampleIDs'] != ''){
				$sampleIDs = splitComparisonSampleIDs($currentComparison['Case_SampleIDs']);	
				
				
				foreach($sampleIDs as $tempKey => $sampleID){
					
					unset($dataArray);
					
					$dataArray['SampleIndex'] 		= $sampleIndexLookup[$sampleID];
					$dataArray['SampleID'] 			= $sampleID;
					
					$dataArray['ComparisonIndex'] 	= $comparisonIndex;
					$dataArray['ComparisonID'] 		= $currentComparison['ComparisonID'];
					
	
					$dataArray['Type'] 				= 'Case';
					
					$dataArray['Case_DiseaseState'] 					= ucwords($currentComparison['Case_DiseaseState']);				
					$dataArray['Control_DiseaseState']					= ucwords($currentComparison['Control_DiseaseState']);
					$dataArray['ComparisonCategory']					= $currentComparison['ComparisonCategory'];
					
					if ($caseCategory != ''){
						$dataArray['Comparison_Disease_State'] 				= ucwords($currentComparison['Case_DiseaseState']) . '_' . $caseCategory;
						$dataArray['Comparison_Disease_State_Abbreviation'] = ucwords($abbreviation) . '_' . $caseCategory;
					} else {
						$dataArray['Comparison_Disease_State'] 				= ucwords($currentComparison['Case_DiseaseState']);
						$dataArray['Comparison_Disease_State_Abbreviation'] = ucwords($abbreviation);
					}
					
					
					$SQL = getInsertSQLQuery($tableName, $dataArray);
					
					$currentModeCount[$currentMode]++;
					execSQL($SQL);
				}
			}
			
			
			if ($currentComparison['Control_SampleIDs'] != ''){
				$sampleIDs = splitComparisonSampleIDs($currentComparison['Control_SampleIDs']);	
				
				
				foreach($sampleIDs as $tempKey => $sampleID){
					
					unset($dataArray);
					
					$dataArray['SampleIndex'] 		= $sampleIndexLookup[$sampleID];
					$dataArray['SampleID'] 			= $sampleID;
					
					$dataArray['ComparisonIndex'] 	= $comparisonIndex;
					$dataArray['ComparisonID'] 		= $currentComparison['ComparisonID'];
	
					$dataArray['Type'] 				= 'Control';
					
					$dataArray['Case_DiseaseState'] 					= ucwords($currentComparison['Case_DiseaseState']);				
					$dataArray['Control_DiseaseState']					= ucwords($currentComparison['Control_DiseaseState']);
					$dataArray['ComparisonCategory']					= $currentComparison['ComparisonCategory'];
					
					if ($caseCategory != ''){
						$dataArray['Comparison_Disease_State'] 				= ucwords($currentComparison['Case_DiseaseState']) . '_' . $controlCategory;
						$dataArray['Comparison_Disease_State_Abbreviation'] = ucwords($abbreviation) . '_' . $controlCategory;
					} else {
						$dataArray['Comparison_Disease_State'] 				= ucwords($currentComparison['Case_DiseaseState']);
						$dataArray['Comparison_Disease_State_Abbreviation'] = ucwords($abbreviation);
					}
					
					
					$SQL = getInsertSQLQuery($tableName, $dataArray);
					
					$currentModeCount[$currentMode]++;
					
					execSQL($SQL);
				}
				
				
			}
			
			
		}
	
	}

	
	if ($patchExecedLocal){
		
		clearCache(0);

		echo "[Executed] # of data added: (Public: {$currentModeCount['Public']}; Private: {$currentModeCount['Private']})\n";

	} else {
		echo "[Skipped] Table {$tableName} is already patched.\n";
		
	}

}



?>