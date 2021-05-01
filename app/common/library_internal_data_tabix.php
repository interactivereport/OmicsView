<?php

//Version: 2019-04-12
//derrick@bioinforx.com

//**********************************************************
// Function: tabix_search_records_with_index()
// $projectIndex
// The project index of the internal data
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
function tabix_search_records_with_index_internal_data($projectIndex = NULL, $primaryIndex = NULL, $secondaryIndex = NULL, $table = NULL, $outputFormat = NULL, &$file_path = NULL){
	
	global $BXAF_CONFIG, $BXAF_TEMP, $BXAPP_TIMER, $BXAPP_TABIX_COUNTER;
	
	$projectIndex = intval($projectIndex);
	
	if ($projectIndex <= 0) return false;

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
			$indexFile 						= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/GeneLevelExpression.txt.gz";	
		} else {
			$indexFile 						= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/GeneLevelExpression.txt.Sample.gz";	
		}
		
		$BXAF_TEMP['columnOrder'] 			= array('SampleIndex', 'GeneIndex', 'Value');
		
		if (gene_uses_TPM()){
			if (get_gene_type() == 'Protein'){
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Protein Index', 'TPM');
			} else {
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Gene Index', 'TPM');
			}
		} else {
			if (get_gene_type() == 'Protein'){
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Protein Index', 'Value');
			} else {
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Gene Index', 'Value');
			}
		}
		
	} elseif ($table == 'GeneFPKM'){
		
		if ($which == 'Gene'){
			if (gene_uses_TPM()){
				$indexFile 					= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/GeneLevelExpression-TPM.txt.gz";
			} else {
				$indexFile 					= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/GeneLevelExpression.txt.gz";
			}
		} else {
			if (gene_uses_TPM()){
				$indexFile 					= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/GeneLevelExpression-TPM.txt.Sample.gz";				
			} else {
				$indexFile 					= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/GeneLevelExpression.txt.Sample.gz";

			}
		}		
		
		

		$BXAF_TEMP['columnOrder']			= array('SampleIndex', 'GeneIndex', 'FPKM', 'Count');
		
		if (gene_uses_TPM()){
			if (get_gene_type() == 'Protein'){
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Protein Index', 'TPM', 'Count');
			} else {
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Gene Index', 'TPM', 'Count');
			}
		} else {
			if (get_gene_type() == 'Protein'){
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Protein Index', 'Value', 'Count');
			} else {
				$BXAF_TEMP['columnOrderPrintable'] 	= array('Sample Index', 'Gene Index', 'FPKM', 'Count');
			}
		}

	} elseif ($table == 'ComparisonData'){
		
		if ($which == 'Gene'){
			$indexFile 						= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/ComparisonData.txt.gz";
		} else {
			$indexFile 						= "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Processed/ProjectIndex_{$projectIndex}/bigzip/ComparisonData.txt.Sample.gz";
		}	
		
		$BXAF_TEMP['columnOrder'] 			= array('ComparisonIndex', 'GeneIndex', 'Name', 'Log2FoldChange', 'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue');
		
		if (get_gene_type() == 'Protein'){
			$BXAF_TEMP['columnOrderPrintable'] 	= array('Comparison Index', 'Protein Index', 'Name', 'Log2 Fold Change', 'p-value', 'Adjusted p-value', 'Numerator Value', 'Denominator Value');
		} else {
			$BXAF_TEMP['columnOrderPrintable'] 	= array('Comparison Index', 'Gene Index', 'Name', 'Log2 Fold Change', 'p-value', 'Adjusted p-value', 'Numerator Value', 'Denominator Value');	
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
		$filePrefix	= $path . md5($projectIndex) . '_' . md5($indexFile) . '_' . md5(json_encode($primaryIndex)) . '_' . md5(json_encode($secondaryIndex));
	} elseif ($hasPrimaryIndex && !$hasSecondaryIndex){
		$filePrefix	= $path . md5($projectIndex) . '_' . md5($indexFile) . '_' . md5(json_encode($primaryIndex)) . '_Blank';
	} elseif (!$hasPrimaryIndex && $hasSecondaryIndex){
		$filePrefix	= $path . md5($projectIndex) . '_' . md5($indexFile) . '_' . 'Blank_' . md5(json_encode($secondaryIndex));
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

	
	$BXAPP_TIMER["tabix::file_exists::{$fileOutputTabix}"][] = microtime(true);
	if (!file_exists($fileOutputTabix) || filesize($fileOutputTabix) <= 0){
		if (($outputFormat == 'GetArrayAssoc') || ($outputFormat == 'GetArrayNumeric')){
			return array();	
		} else {
			return false;
		}
	}
	$BXAPP_TIMER["tabix::file_exists::{$fileOutputTabix}"][] = microtime(true);
	
	if ($table == 'GeneFPKM'){
		$overrideColumnOrder = internal_data_get_gene_level_expression_headers($projectIndex);
	}

	
	if (general_array_size($overrideColumnOrder) > 0){
		$BXAF_TEMP['columnOrder_Org'] = $BXAF_TEMP['columnOrder'];
		$BXAF_TEMP['columnOrderSize_Org'] = general_array_size($BXAF_TEMP['columnOrder_Org']);
		
		$BXAF_TEMP['columnOrder'] = $overrideColumnOrder;
		$BXAF_TEMP['columnOrderSize'] = general_array_size($overrideColumnOrder);
		
		$BXAF_TEMP['columnOrderPrintable_Org'] = $BXAF_TEMP['columnOrderPrintable'];
		
		$BXAF_TEMP['columnOrderPrintable'] = $overrideColumnOrder;
		
		foreach($overrideColumnOrder as $tempKey => $tempValue){
			
			$currentHeader = $tempValue;
			
			if ($tempValue == 'ComparisonIndex'){
				$currentHeader = 'Comparison Index';
			} elseif ($tempValue == 'GeneIndex'){
				if (get_gene_type() == 'Protein'){
					$currentHeader = 'Protein Index';
				} else {
					$currentHeader = 'Gene Index';
				}
			} elseif ($tempValue == 'SampleIndex'){
				$currentHeader = 'Sample Index';
			} elseif ($tempValue == 'FPKM'){
				if (get_gene_type() == 'Protein'){
					$currentHeader = 'Value';					
				} elseif (gene_uses_TPM()){
					$currentHeader = 'TPM';
				}
			}
			
			$BXAF_TEMP['columnOrderPrintable'][$tempKey] = $currentHeader;
		}
		
		
		
	}
	
	
	
	if (!file_exists($fileOutputTxt) || filesize($fileOutputTxt) <= 0){
		$BXAPP_TIMER["tabix::fileOutputTxt::{$fileOutputTxt}"][] = microtime(true);
		
		$headerString = implode("\t", $BXAF_TEMP['columnOrderPrintable']);
		$cmd = "echo '{$headerString}' | cat - {$fileOutputTabix} > {$fileOutputTxt}";
		shell_exec($cmd);

		$BXAPP_TIMER["tabix::fileOutputTxt::{$fileOutputTxt}"][] = microtime(true);
	}

	
	
	if ($outputFormat == ''){
		$outputFormat = 'GetArrayAssoc';
	}

	$BXAPP_TIMER["tabix::{$tabixUniqID}::results"][] = microtime(true);
	if ($outputFormat == 'GetArrayAssoc'){

		$verifiedColumnHeaders = 0;
		if (($handle = fopen($fileOutputTabix, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE) {
				
				$data		= explode("\t", trim($buffer));
				
				if (!$verifiedColumnHeaders){
					$verifiedColumnHeaders = 1;
					
					if (general_array_size($data) != $BXAF_TEMP['columnOrderSize']){
						$BXAF_TEMP['columnOrder'] = $BXAF_TEMP['columnOrder_Org'];
						$BXAF_TEMP['columnOrderSize'] = $BXAF_TEMP['columnOrderSize_Org'];
						$BXAF_TEMP['columnOrderPrintable'] = $BXAF_TEMP['columnOrderPrintable_Org'];
					}
				}
				
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




?>