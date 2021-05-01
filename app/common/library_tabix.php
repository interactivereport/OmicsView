<?php

//Version: 2019-04-12
//derrick@bioinforx.com

//**********************************************************
// Function: tabix_search_records_with_index()
//
// Select records using Gene Index and Secondary Index
//
// $primaryIndex
//   Optional
//   array of gene Index, e.g.,
//   array(1,2,3,4,5)
//
// $secondaryIndex
//   Optional
//   array of sample/comparison Index, e.g.,
//   array(10,20,30,40)
//
// $table
//   Required: One of the following
//	 - ComparisonData
//	 - GeneLevelExpression
//   - GeneFPKM
//
// $outputFormat
//   Optional (Default: GetArrayAssoc)
//
//	 1.) GetArrayAssoc
//		Similar to the format of $BXAF_CONN->GetArray($SQL), e.g.,
//
/*
			Array
			(
				[0] => Array
					(
						[SampleIndex] => 23535
						[GeneIndex] => 17327
						[FPKM] => 5.0030
						[Count] => 540.1146
					)
			
				[1] => Array
					(
						[SampleIndex] => 23536
						[GeneIndex] => 17327
						[FPKM] => 7.9114
						[Count] => 570.7538
					)
			
				[2] => Array
					(
						[SampleIndex] => 23537
						[GeneIndex] => 17327
						[FPKM] => 4.7168
						[Count] => 487.0143
					)
*/
//	 2.) GetArrayNumeric
//		 Similar to GetArrayAssoc, except that the array is numeric.
//
/*
			Array
			(
				[0] => Array
					(
						[0] => 23535
						[1] => 17327
						[2] => 5.0030
						[3] => 540.1146
					)
			
				[1] => Array
					(
						[0] => 23536
						[1] => 17327
						[2] => 7.9114
						[3] => 570.7538
					)
			
*/
//	 3.) Raw
//		 This will return a string that contains the raw data of the tabix output. Further parsing is required.
//
//       "23535 17327 5.0030 540.1146
//        23536 17327 7.9114 570.7538"
//
//	 4.) Path
//		 This will return a string that contains the file path of the tabix output. Caller will need to read the content using the path.
//
//       "/tmp/tabixout.txt"
//
// $file_path
// A pass-by-reference variable
// If it is set, it will return the file path of the tabix file. Notice that the file will contain the header.


// To prepare Tabix File:
/*
bgzip -c GeneFPKM.txt > GeneFPKM.txt.gz
tabix -S 1 -s 2 -b 1 -e 1 -0 GeneFPKM.txt.gz
tail -n +2 GeneFPKM.txt | sort -k1,1n -k2,2n | bgzip > GeneFPKM.txt.Sample.gz
tabix -s 1 -b 2 -e 2 -0 GeneFPKM.txt.Sample.gz

bgzip -c GeneLevelExpression.txt > GeneLevelExpression.txt.gz
tabix -S 1 -s 2 -b 1 -e 1 -0 GeneLevelExpression.txt.gz
tail -n +2 GeneLevelExpression.txt | sort -k1,1n -k2,2n | bgzip > GeneLevelExpression.txt.Sample.gz
tabix -s 1 -b 2 -e 2 -0 GeneLevelExpression.txt.Sample.gz

bgzip -c ComparisonData.txt > ComparisonData.txt.gz
tabix -S 1 -s 2 -b 1 -e 1 -0 ComparisonData.txt.gz
tail -n +2 ComparisonData.txt | sort -k1,1n -k2,2n | bgzip > ComparisonData.txt.Sample.gz
tabix -s 1 -b 2 -e 2 -0 ComparisonData.txt.Sample.gz
*/



//If you have updated the tabix dataset, please clear the cache:
//Visit: /app/gene_expressions/admin_clear_caches.php
//or run clear_tabix_cache();
function tabix_search_records_with_index($primaryIndex = NULL, $secondaryIndex = NULL, $table = NULL, $outputFormat = NULL, &$file_path = NULL){
	
	global $BXAF_CONFIG, $BXAF_TEMP, $BXAPP_TIMER, $BXAPP_TABIX_COUNTER;
	
	$tabixUniqID = ++$BXAPP_TABIX_COUNTER;


	$BXAPP_TIMER["tabix::{$tabixUniqID}::init()"][] = microtime(true);

	if (is_array($primaryIndex)){
		$primaryIndex = array_filter($primaryIndex, 'is_numeric');
		$primaryIndex = array_unique($primaryIndex);
		
		if (is_array($primaryIndex) && sizeof($primaryIndex) > 0){
			$hasPrimaryIndex = true;
		}
	}

	
	if (is_array($secondaryIndex)){
		$secondaryIndex = array_filter($secondaryIndex, 'is_numeric');
		$secondaryIndex = array_unique($secondaryIndex);
		
		if (is_array($secondaryIndex) && sizeof($secondaryIndex) > 0){
			$hasSecondaryIndex = true;
		}
	}
	
	
	if ($hasPrimaryIndex && $hasSecondaryIndex){
		$which = 'Gene';
	} elseif ($hasPrimaryIndex && !$hasSecondaryIndex){
		$which = 'Gene';
	} elseif (!$hasPrimaryIndex && $hasSecondaryIndex){
		$which = 'Sample';
	} else {
		return false;	
	}



	if ($table == 'GeneLevelExpression'){
		
		if ($which == 'Gene'){
			$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['GeneLevelExpression'];	
		} else {
			$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['GeneLevelExpression-Sample'];
		}
		
		$BXAF_TEMP['columnOrder'] 			= array('SampleIndex', 'GeneIndex', 'Value');
		$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Gene Index', 'Value');
		
		if (sizeof($BXAF_CONFIG['TABIX_INDEX']['GeneLevelExpression-Override']['columnOrder']) > 0){
			$BXAF_TEMP['columnOrder'] = $BXAF_CONFIG['TABIX_INDEX']['GeneLevelExpression-Override']['columnOrder'];
		}
		
		if (sizeof($BXAF_CONFIG['TABIX_INDEX']['GeneLevelExpression-Override']['columnOrderPrintable']) > 0){
			$BXAF_TEMP['columnOrderPrintable'] = $BXAF_CONFIG['TABIX_INDEX']['GeneLevelExpression-Override']['columnOrderPrintable'];
		}
		
	} elseif ($table == 'GeneFPKM'){
		
		if ($which == 'Gene'){
			if (gene_uses_TPM()){
				$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-TPM'];
			} else {
				$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM'];	
			}
		} else {
			if (gene_uses_TPM()){
				$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-TPM-Sample'];
			} else {
				$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-Sample'];
			}
		}

		$BXAF_TEMP['columnOrder']			= array('SampleIndex', 'GeneIndex', 'FPKM', 'Count');
		$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Gene Index', 'FPKM', 'Count');
		
		if (sizeof($BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-Override']['columnOrder']) > 0){
			$BXAF_TEMP['columnOrder'] = $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-Override']['columnOrder'];
		}
		
		if (sizeof($BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-Override']['columnOrderPrintable']) > 0){
			$BXAF_TEMP['columnOrderPrintable'] = $BXAF_CONFIG['TABIX_INDEX']['GeneFPKM-Override']['columnOrderPrintable'];
		}
		
		
		
		
	} elseif ($table == 'ComparisonData'){
		
		if ($which == 'Gene'){
			$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['ComparisonData'];	
		} else {
			$indexFile 						= $BXAF_CONFIG['TABIX_INDEX']['ComparisonData-Sample'];
		}	
		
		$BXAF_TEMP['columnOrder'] 			= array('ComparisonIndex', 'GeneIndex', 'Name', 'Log2FoldChange', 'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue');
		$BXAF_TEMP['columnOrderPrintable'] 	= array('Comparison Index', 'Gene Index', 'Name', 'Log2 Fold Change', 'p-value', 'Adjusted p-value', 'Numerator Value', 'Denominator Value');
		
		if (sizeof($BXAF_CONFIG['TABIX_INDEX']['ComparisonData-Override']['columnOrder']) > 0){
			$BXAF_TEMP['columnOrder'] = $BXAF_CONFIG['TABIX_INDEX']['ComparisonData-Override']['columnOrder'];
		}
		
		if (sizeof($BXAF_CONFIG['TABIX_INDEX']['ComparisonData-Override']['columnOrderPrintable']) > 0){
			$BXAF_TEMP['columnOrderPrintable'] = $BXAF_CONFIG['TABIX_INDEX']['ComparisonData-Override']['columnOrderPrintable'];
		}
		
		
	}
	$BXAF_TEMP['columnOrderSize'] = sizeof($BXAF_TEMP['columnOrder']);


	if (!is_file($indexFile)){
		if (($outputFormat == 'GetArrayAssoc') || ($outputFormat == 'GetArrayNumeric')){
			return array();	
		} else {
			return false;
		}
	}
	
	$path			 = "{$BXAF_CONFIG['WORK_DIR']}tabix/{$BXAF_CONFIG['APP_PROFILE']}/" . __FUNCTION__ . '/' . $table . '/';
	
	
	if (!is_dir($path)){
		mkdir($path, 0777, true);
	}
	
	if ($hasPrimaryIndex && $hasSecondaryIndex){
		$filePrefix	= $path . md5($indexFile) . '_' . md5(json_encode($primaryIndex)) . '_' . md5(json_encode($secondaryIndex));
	} elseif ($hasPrimaryIndex && !$hasSecondaryIndex){
		$filePrefix	= $path . md5($indexFile) . '_' . md5(json_encode($primaryIndex)) . '_Blank';
	} elseif (!$hasPrimaryIndex && $hasSecondaryIndex){
		$filePrefix	= $path . md5($indexFile) . '_' . 'Blank_' . md5(json_encode($secondaryIndex));
	}
	
	
	$fileInput 			= $filePrefix . '_input.txt';
	$fileOutputTabix	= $filePrefix . '_output.tabix';
	$fileOutputTxt 		= $filePrefix . '_output.txt';
	$file_path 			= $fileOutputTxt;
	
	$BXAPP_TIMER["tabix::{$tabixUniqID}::init()"][] = microtime(true);
	
	if (!file_exists($fileOutputTabix) || filesize($fileOutputTabix) <= 0){
		
		$BXAPP_TIMER["tabix::{$tabixUniqID}::prepareCMD"][] = microtime(true);
		if ($hasPrimaryIndex && $hasSecondaryIndex){

			$fp = fopen($fileInput, 'w');

			natsort($primaryIndex);
			natsort($secondaryIndex);
		
			foreach($primaryIndex as $tempKey1 => $currentPrimaryIndex){
				foreach($secondaryIndex as $tempKey2 => $currentSecondaryIndex){
					$currentSecondaryIndex++;
					fwrite($fp, "{$currentPrimaryIndex}\t{$currentSecondaryIndex}\t{$currentSecondaryIndex}\n");
				}
			}
			fclose($fp);
			
			$cmd = "{$BXAF_CONFIG['TABIX_BIN']} {$indexFile} -R {$fileInput} > {$fileOutputTabix}";
			
		} elseif ($hasPrimaryIndex && !$hasSecondaryIndex){

			$cmd = "{$BXAF_CONFIG['TABIX_BIN']} {$indexFile} " . implode(' ', $primaryIndex) . " > {$fileOutputTabix}";
			
		} elseif (!$hasPrimaryIndex && $hasSecondaryIndex){
			
			$cmd = "{$BXAF_CONFIG['TABIX_BIN']} {$indexFile} " . implode(' ', $secondaryIndex) . " > {$fileOutputTabix}";
		}
		$BXAPP_TIMER["tabix::{$tabixUniqID}::prepareCMD"][] = microtime(true);
		
		
		$BXAPP_TIMER["tabix::{$tabixUniqID}::shell_exec()"][] = microtime(true);

		shell_exec($cmd);
		

		$BXAPP_TIMER["tabix::{$tabixUniqID}::shell_exec()"][] = microtime(true);
	}

	
	$BXAPP_TIMER["tabix::{$tabixUniqID}::file_exists::{$fileOutputTabix}"][] = microtime(true);
	if (!file_exists($fileOutputTabix) || filesize($fileOutputTabix) <= 0){
		if (($outputFormat == 'GetArrayAssoc') || ($outputFormat == 'GetArrayNumeric')){
			return array();	
		} else {
			return false;
		}
	}
	$BXAPP_TIMER["tabix::{$tabixUniqID}::file_exists::{$fileOutputTabix}"][] = microtime(true);
	
	
	if (!file_exists($fileOutputTxt) || filesize($fileOutputTxt) <= 0){
		$BXAPP_TIMER["tabix::{$tabixUniqID}::fileOutputTxt::{$fileOutputTxt}"][] = microtime(true);
		
		$headerString = implode("\t", $BXAF_TEMP['columnOrderPrintable']);
		$cmd = "echo '{$headerString}' | cat - {$fileOutputTabix} > {$fileOutputTxt}";
		shell_exec($cmd);
		$BXAPP_TIMER["tabix::{$tabixUniqID}::fileOutputTxt::{$fileOutputTxt}"][] = microtime(true);
	}
	
	
	if ($outputFormat == ''){
		$outputFormat = 'GetArrayAssoc';
	}
	
	$BXAPP_TIMER["tabix::{$tabixUniqID}::results"][] = microtime(true);
	if ($outputFormat == 'GetArrayAssoc'){
		if (($handle = fopen($fileOutputTabix, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE) {
				$data		= explode("\t", trim($buffer));
				$results[] 	= array_combine($BXAF_TEMP['columnOrder'], $data);
			}
			fclose($handle);
		}
	} elseif ($outputFormat == 'GetArrayNumeric'){
		if (($handle = fopen($fileOutputTabix, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE) {
				$results[] = explode("\t", trim($buffer));
			}
			fclose($handle);
		}
	} elseif ($outputFormat == 'Raw'){
		$results = file_get_contents($fileOutputTabix);
	} elseif ($outputFormat == 'Path'){
		$results = $fileOutputTabix;
	}
	
	$BXAPP_TIMER["tabix::{$tabixUniqID}::results"][] = microtime(true);
	
	return $results;
	
}


function clear_tabix_cache(){
	global $BXAF_CONFIG;
	
	$path = "{$BXAF_CONFIG['WORK_DIR']}tabix/";
	
	$cmd = "rm -Rf {$path}";
	
	shell_exec($cmd);
	
	return true;
	
}


//tabix_search_records_with_index_all()
//All inputs are the same as tabix_search_records_with_index()
//$primaryIndex = an array of GeneIndex
//	e.g., array(17327)
//
//$secondaryIndex = an array of SampleIndex or ComparisonIndex
//	e.g., array(251, 252, 253, 20000174,20000121, 20000138, 20000139);
//
//$table
//   Required: One of the following
//	 - ComparisonData
//	 - GeneLevelExpression
//   - GeneFPKM
//
//$outputFormat
//	- GetArrayAssoc
//		e.g.,
/*
[0] => Array
        (
            [SampleIndex] => 251
            [GeneIndex] => 17327
            [FPKM] => 9.6746
            [Count] => 299.6068
        )

    [1] => Array
        (
            [SampleIndex] => 252
            [GeneIndex] => 17327
            [FPKM] => 24.9112
            [Count] => 1122.6809
        )
*/
//	- GetArrayNumeric
/*
[0] => Array
        (
            [0] => 251
            [1] => 17327
            [2] => 9.6746
            [3] => 299.6068
        )

    [1] => Array
        (
            [0] => 252
            [1] => 17327
            [2] => 24.9112
            [3] => 1122.6809
        )
*/
//	- Raw
/*
251	17327	9.6746	299.6068
252	17327	24.9112	1122.6809
253	17327	23.7142	1040.6980
20000121	17327	4.74	NA
20000138	17327	4.895	NA
20000139	17327	4.74	NA
20000174	17327	4.979	NA
*/

// - File
//Return the file path of the result.
function tabix_search_records_with_index_all($primaryIndex = NULL, $secondaryIndex = NULL, $table = NULL, $outputFormat = NULL){
	
	global $BXAF_CONFIG;
	
	if ($outputFormat == ''){
		$outputFormat = 'GetArrayAssoc';
	}
	
	if ($outputFormat == 'File'){
		$needFilePath = true;
		$outputFormat = 'Raw';	
	}
			
	$results = false;
	if (($outputFormat == 'GetArrayAssoc') || ($outputFormat == 'GetArrayNumeric')){
		$results = array();	
	}
	
	
	
	if (general_array_size($secondaryIndex) > 0){
		$allPrivateProjects 	= false;
		$secondaryIndexSplit 	= internal_data_split_multiple_data_by_source($secondaryIndex);
		
		if (general_array_size($secondaryIndexSplit['public']) > 0){
			$needPublic 			= true;
			$secondaryIndexPublic 	= $secondaryIndexSplit['public'];
		}
		
		if (general_array_size($secondaryIndexSplit['private']) > 0){
			$needPrivate 			= true;
			$secondaryIndexPrivate	= $secondaryIndexSplit['private'];
		}
	} else {
		$allPrivateProjects = true;
		$needPublic = $needPrivate = true;
		$secondaryIndexPublic = $secondaryIndexPrivate = array();
	}
	
	
	$filePathArray = array();
	if ($needPublic){
		$temp = '';
		$results = tabix_search_records_with_index($primaryIndex, $secondaryIndexPublic, $table, $outputFormat, $temp);
	}
	
	if ($needPublic && !$needPrivate){
		return $results;	
	}
	
	
	
	
	if ($needPrivate){
		$privateProjectIDs 		= internal_data_get_accessible_project();
		$privateProjectIDAry 	= array_keys($privateProjectIDs);
		$privateProjectIDs		= implode(',', array_keys($privateProjectIDs));
		
		
		

		if ($privateProjectIDs != ''){
			
			if (!$allPrivateProjects){
				$conn 			= bxaf_get_app_db_connection();
				$privateIndexes = implode(',', $secondaryIndexPrivate);
				
				if (($table == 'GeneLevelExpression') || ($table == 'GeneFPKM')){
					$SQL = "SELECT `SampleIndex`, `ProjectIndex` FROM `App_User_Data_Samples` 
								WHERE (`ProjectIndex` IN ({$privateProjectIDs})) AND (`SampleIndex` IN ({$privateIndexes}))";
					$privateIndex_ProjectIndex 	= $conn->GetAssoc($SQL);
				} elseif ($table == 'ComparisonData'){
					$SQL	= "SELECT `ComparisonIndex`, `ProjectIndex` FROM `App_User_Data_Comparisons` 
								WHERE (`ProjectIndex` IN ({$privateProjectIDs})) AND (`ComparisonIndex` IN ({$privateIndexes}))";
					$privateIndex_ProjectIndex 	= $conn->GetAssoc($SQL);
				}
				
				
				
				
				if (general_array_size($privateIndex_ProjectIndex) > 0){
					$projectIndex_privateIndex = array();
					foreach($privateIndex_ProjectIndex as $privateIndex => $projectIndex){
						$projectIndex_privateIndex[$projectIndex][] = $privateIndex;
					}
				}
				
				
			} else {
				foreach($privateProjectIDAry as $tempKey => $projectIndex){
					$projectIndex_privateIndex[$projectIndex] = array();
				}
			}
			
			
			
			
			foreach($projectIndex_privateIndex as $projectIndex => $privateIndexes){
				
				$temp = '';
				$currentResults = tabix_search_records_with_index_internal_data($projectIndex, $primaryIndex, $privateIndexes, $table, $outputFormat, $temp);

				
				if (($outputFormat == 'GetArrayAssoc') || ($outputFormat == 'GetArrayNumeric')){
					foreach($currentResults as $tempKey => $tempValue){
						$results[] = $tempValue;
					}
				} elseif ($outputFormat == 'Path'){
					if (isset($results)){
						if (!is_array($results)){
							$results = array($results);
						}
					}
					
					if ($currentResults != ''){
						$results[] = $currentResults;
					}
					
				} elseif ($outputFormat == 'Raw'){
					$results .= $currentResults;
				}
			}
			
		}
	}
	
	
	
	if ($outputFormat == 'Path'){
		
		foreach($results as $tempKey => $currentFile){
			if (!file_exists($currentFile)){
				unset($results[$tempKey]);
			}
		}
				
		$path			 = "{$BXAF_CONFIG['WORK_DIR']}tabix/{$BXAF_CONFIG['APP_PROFILE']}/" . __FUNCTION__ . '/' . $table . '/';
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}
		
		$filePrefix	= md5(json_encode($primaryIndex)) . '_' . md5(json_encode($secondaryIndex));
		$fileOutput = $path . $filePrefix . "_Combined.txt";
		
		$cmd = "{$BXAF_CONFIG['CAT_BIN']} " . implode(' ', $results) . " > {$fileOutput}";
		
		shell_exec($cmd);
		
		$results = $fileOutput;
	}
	
	
	return $results;

		
	
}


?>