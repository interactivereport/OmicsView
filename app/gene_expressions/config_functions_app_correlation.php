<?php

function prepareGeneSampleCorrelation($geneIndexes, $secondaryIndexes, $platformType, $otherOptions, $dataSource, $internalProjectIndexes){
	global $APP_CONFIG;

	startTimer(__FUNCTION__ . '::checkCache');
	$geneIndexes 		= array_clean($geneIndexes);	
	$secondaryIndexes 	= array_clean($secondaryIndexes);
	$platformType		= trim($platformType);
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$version = '2020-03-17 12:10';
	
	$cacheKey 			= __FUNCTION__ . '::' . md5(json_encode($geneIndexes) . '::' . 
													json_encode($secondaryIndexes) . '::' . 
													$platformType . '::' . 
													json_encode($otherOptions) . '::' . 
													json_encode($dataSource) . '::' . 
													json_encode($internalProjectIndexes) . '::' . 
													json_encode(gene_uses_TPM()) . '::' . 
													$version
													);
													
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		startTimer(__FUNCTION__ . '::checkCache');
		return $resultsFromCache;
	}
	startTimer(__FUNCTION__ . '::checkCache');
	
	
	
	
	//*******************
	// Search Gene Index
	//*******************
	startTimer(__FUNCTION__ . '::geneInfoSource');
	$sql_table 			= 'GeneCombined';
	$geneIndexString	= implode(', ', $geneIndexes);
	$SQL 				= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
	$geneInfoSource		= getSQL($SQL, 'GetAssoc', $sql_table);
	startTimer(__FUNCTION__ . '::geneInfoSource');
	
	if (array_size($geneInfoSource) <= 0){
		return false;
	}

	
	startTimer(__FUNCTION__ . '::geneInfoTarget');
	if ($otherOptions['comparison'] == 2){
		//Local
		$geneInfoTarget = &$geneInfoSource;
	} else {
		//Global
		$SQL 				= "SELECT GeneName, GeneIndex FROM {$sql_table} ORDER BY GeneName ASC";
		$geneIndexes		= array_values(getSQL($SQL, 'GetAssoc', $sql_table));
		$geneIndexString	= implode(', ', $geneIndexes);
		$SQL 				= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
		$geneInfoTarget		= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	startTimer(__FUNCTION__ . '::geneInfoTarget');

	
	
	//*******************
	// Search Secondary
	//*******************
	startTimer(__FUNCTION__ . '::tabix');
	$geneIndexTarget			= array_keys($geneInfoTarget);
	$geneIndexString 			= implode(', ', $geneIndexTarget);
	$secondaryIndexString 		= implode(', ', $secondaryIndexes);
	$secondaryIndexStringOrg 	= $secondaryIndexString;
	
	if ($platformType == 'RNA-Seq'){
		$sql_table		= 'GeneFPKM';
		$value_table 	= 'GeneFPKM';
		
	} elseif ($platformType == 'Microarray'){
		$sql_table 		= 'GeneLevelExpression';
		$value_table 	= 'GeneLevelExpression';
	} else {
		return false;	
	}
	
	unset($geneExpressionRawFileCandidates);
	
	if ($otherOptions['comparison'] == 1){
		//Global
		if ($dataSource['public'] != ''){
			$geneExpressionRawFileCandidates[] = tabix_search_records_with_index('',               $secondaryIndexes, $sql_table, 'Path', $tabixFilePath);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$geneExpressionRawFileCandidates[] = tabix_search_records_with_index_internal_data($projectIndex, '', $secondaryIndexes, $sql_table, 'Path', $tabixFilePathPrivate);
			}
		}
	} else {
		//Local
		if ($dataSource['public'] != ''){
			$geneExpressionRawFileCandidates[] = tabix_search_records_with_index($geneIndexTarget, $secondaryIndexes, $sql_table, 'Path', $tabixFilePath);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				
				
				$geneExpressionRawFileCandidates[] = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexTarget, $secondaryIndexes, $sql_table, 'Path', $tabixFilePathPrivate);
			}
		}
	}
	startTimer(__FUNCTION__ . '::tabix');
	$geneExpressionRawFileCandidates = array_clean($geneExpressionRawFileCandidates);
	
	startTimer(__FUNCTION__ . '::geneExpressionValueIndex');
	$otherOptions['transform_value'] = abs(floatval($otherOptions['transform_value']));
	unset($secondaryIndexString);
	
	foreach ($geneExpressionRawFileCandidates as $currentFileCandidateKey => $currentFileCandidate){
		if ($currentFileCandidate == '') continue;
		
		if (($handle = fopen($currentFileCandidate, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE){
				
				$tempValue	= explode("\t", trim($buffer));

				
			
				$currentGeneIndex 		= intval($tempValue[1]);
				$currentSecondaryIndex 	= intval($tempValue[0]);
				$originalValue			= trim($tempValue[2]);
				$transformedValue		= trim($tempValue[2]);
				
				//0: 'Sample Index', 'Gene Index', 2: 'FPKM', 'Count'
				//0: 'Sample Index', 'Gene Index', 2: 'Value'
				
	
				if (($originalValue === '') || is_null($originalValue) || ($originalValue === '.') || !is_numeric($originalValue)){
					$originalValue 		= 'NA';
					$transformedValue	= 'NA';
				} else {
					if ($otherOptions['transform']){
						if ($originalValue < 0){
							$transformedValue = 0;	
						}
						
						if (($transformedValue + $otherOptions['transform_value']) > 0){
							$transformedValue = log2($transformedValue + $otherOptions['transform_value']);
						} else {
							$transformedValue = 'NA';
						}
					}
				}
				
				unset($keepGoing);
				if (!isset($score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				} elseif (is_numeric($tempValue[2]) && ($tempValue[2] > $score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				} elseif (is_numeric($tempValue[2]) && !is_numeric($score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				}
				
				if (!$keepGoing) continue;
				
				//$geneExpressionValueIndexOrg[$currentGeneIndex][$currentSecondaryIndex] 	= $originalValue;
				$geneExpressionValueIndex[$currentGeneIndex][$currentSecondaryIndex] 		= $transformedValue;
				$secondaryIndexString[$currentSecondaryIndex] 								= $currentSecondaryIndex;
				$score[$currentGeneIndex][$currentSecondaryIndex] 							= $tempValue[2];
	
			}
			fclose($handle);
		}
	}
	unset($score);
	startTimer(__FUNCTION__ . '::geneExpressionValueIndex');
	

	//*******************
	// Search Secondary Index
	//*******************
	startTimer(__FUNCTION__ . '::secondaries');
	$secondaryIndexString = implode(',', $secondaryIndexString);
	
	unset($secondaries);	
	if ($secondaryIndexString != ''){
		if ($dataSource['public'] != ''){
			$sql_table		= 'Samples';
			$SQL 			= "SELECT SampleIndex, SampleID FROM {$sql_table} WHERE SampleIndex IN ({$secondaryIndexString}) ORDER BY FIELD(SampleIndex, {$secondaryIndexStringOrg})";	
			$secondaries	= getSQL($SQL, 'GetAssoc', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			$sql_table 		= 'App_User_Data_Samples';
			$SQL			= "SELECT SampleIndex, SampleID FROM {$sql_table} WHERE SampleIndex IN ({$secondaryIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(SampleIndex, {$secondaryIndexStringOrg})";
			$secondariesPrivate	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($secondariesPrivate as $tempKeyX => $tempValueX){
				$secondaries[$tempKeyX] = $tempValueX;
			}
		}
	}
	
	$results['Gene_Source'] 				= &$geneInfoSource;	
	startTimer(__FUNCTION__ . '::secondaries');

	$otherOptions['cutoff'] = abs(floatval($otherOptions['cutoff']));
	$otherOptions['limit'] 	= abs(intval($otherOptions['limit']));
	
		
	
	
	startTimer(__FUNCTION__ . '::correlationCoefficientCache');
	foreach($geneInfoSource as $geneIndexSource => $geneNameSource){

		if (!isset($geneExpressionValueIndex[$geneIndexSource])) continue;

		foreach($geneInfoTarget as $geneIndexTarget => $geneTargetSource){
			
			if (!isset($geneExpressionValueIndex[$geneIndexTarget])) continue;
			
			if ($geneIndexSource == $geneIndexTarget) continue;
			
			if (isset($correlationCoefficientCache[$geneIndexTarget][$geneIndexSource])){
				$correlationCoefficient = $correlationCoefficientCache[$geneIndexTarget][$geneIndexSource];
			} else {
				$correlationCoefficient = getCorrelationCoefficient($geneExpressionValueIndex[$geneIndexSource], $geneExpressionValueIndex[$geneIndexTarget], $otherOptions['method']);
			}
			$correlationCoefficientCache[$geneIndexSource][$geneIndexTarget] = $correlationCoefficient;
			

			
			unset($qualified);
			if ($otherOptions['direction'] == 1){
				//ABS
				$corr = abs($correlationCoefficient);
				if ($corr >= $otherOptions['cutoff']){
					$qualified = 1;
				}
				
			} elseif ($otherOptions['direction'] == 2){
				//Positive
				$corr = $correlationCoefficient;
				if ($corr >= $otherOptions['cutoff']){
					$qualified = 1;
				}
			} else {
				//Negative
				$corr = $correlationCoefficient;
				if (($otherOptions['cutoff'] == 0) || ($corr <= $otherOptions['cutoff']*-1)){
					$qualified = 1;
				}
			}
			
			$results['Summary']['Count_Total']++;
			if ($qualified){
				$results['Correlation_Coefficient_Transformed'][$geneIndexSource][$geneIndexTarget] = $corr;
				$results['Summary']['Count']++;
			}
		}
		
		
		if (array_size($results['Correlation_Coefficient_Transformed'][$geneIndexSource]) > 0){
			if (($otherOptions['direction'] == 1) || ($otherOptions['direction'] == 2)){
				arsort($results['Correlation_Coefficient_Transformed'][$geneIndexSource]);
			} else {
				asort($results['Correlation_Coefficient_Transformed'][$geneIndexSource]);
			}
			
			$results['Summary']['HasResult'] = 1;
			
		}
		
		if ((array_size($results['Correlation_Coefficient_Transformed'][$geneIndexSource]) > $otherOptions['limit']) && ($otherOptions['limit'] > 0)){
			$results['Correlation_Coefficient_Transformed'][$geneIndexSource] = array_slice($results['Correlation_Coefficient_Transformed'][$geneIndexSource], 0, $otherOptions['limit'], true);
		}
		
		foreach($results['Correlation_Coefficient_Transformed'][$geneIndexSource] as $currentgeneIndexTarget => $tempValueX){
			$results['Gene_Target'][$currentgeneIndexTarget]					 				= $geneInfoTarget[$currentgeneIndexTarget];
			$results['Correlation_Coefficient_Raw'][$geneIndexSource][$currentgeneIndexTarget]	= $correlationCoefficientCache[$geneIndexSource][$currentgeneIndexTarget];
			
			$results['geneExpressionValueIndex'][$currentgeneIndexTarget] 						= $geneExpressionValueIndex[$currentgeneIndexTarget];
			//$results['geneExpressionValueIndexOrg'][$currentgeneIndexTarget] 					= $geneExpressionValueIndexOrg[$currentgeneIndexTarget];
		}
		
		$results['geneExpressionValueIndex'][$geneIndexSource]		= $geneExpressionValueIndex[$geneIndexSource];
		//$results['geneExpressionValueIndexOrg'][$geneIndexSource]	= $geneExpressionValueIndexOrg[$geneIndexSource];
		
	}
	
	
	startTimer(__FUNCTION__ . '::correlationCoefficientCache');


	startTimer(__FUNCTION__ . '::wrapUp');
	$results['Summary']['SampleIndex'] 		= $secondaries;
	$results['Summary']['cacheKey'] 		= $cacheKey;
	$results['Summary']['Function'] 		= __FUNCTION__;
	$results['Summary']['otherOptions'] 	= $otherOptions;
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	startTimer(__FUNCTION__ . '::wrapUp');
	
	return $results;
	
}



function prepareGeneComparisonCorrelation($geneIndexes, $secondaryIndexes, $otherOptions, $dataSource, $internalProjectIndexes){
	global $APP_CONFIG;

	startTimer(__FUNCTION__ . '::checkCache');
	$geneIndexes 		= array_clean($geneIndexes);	
	$secondaryIndexes 	= array_clean($secondaryIndexes);
	$platformType		= trim($platformType);
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = '2020-10-09 20:34';
	$cacheKey 			= __FUNCTION__ . '::' . md5(json_encode($geneIndexes) . '::' . 
													json_encode($secondaryIndexes) . '::' . 
													json_encode($otherOptions) . '::' . 
													json_encode($dataSource) . '::' . 
													json_encode($internalProjectIndexes) . '::' . 
													json_encode(gene_uses_TPM()) . '::' . 
													$version
													);
													
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		startTimer(__FUNCTION__ . '::checkCache');
		return $resultsFromCache;
	}
	startTimer(__FUNCTION__ . '::checkCache');
	
	
	
	//*******************
	// Search Gene Index
	//*******************
	startTimer(__FUNCTION__ . '::geneInfoSource');
	$sql_table 			= 'GeneCombined';
	$geneIndexString	= implode(', ', $geneIndexes);
	$SQL 				= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
	$geneInfoSource		= getSQL($SQL, 'GetAssoc', $sql_table);
	startTimer(__FUNCTION__ . '::geneInfoSource');
	
	if (array_size($geneInfoSource) <= 0){
		return false;
	}

	
	startTimer(__FUNCTION__ . '::geneInfoTarget');
	if ($otherOptions['comparison'] == 2){
		//Local
		$geneInfoTarget = &$geneInfoSource;
	} else {
		//Global
		$SQL 				= "SELECT GeneName, GeneIndex FROM {$sql_table} ORDER BY GeneName ASC";
		$geneIndexes		= array_values(getSQL($SQL, 'GetAssoc', $sql_table));
		$geneIndexString	= implode(', ', $geneIndexes);
		$SQL 				= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
		$geneInfoTarget		= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	startTimer(__FUNCTION__ . '::geneInfoTarget');

	
	
	//*******************
	// Search Secondary
	//*******************
	startTimer(__FUNCTION__ . '::tabix');
	$geneIndexTarget			= array_keys($geneInfoTarget);
	$geneIndexString 			= implode(', ', $geneIndexTarget);
	$secondaryIndexString 		= implode(', ', $secondaryIndexes);
	$secondaryIndexStringOrg 	= $secondaryIndexString;
	
	$sql_table 		= 'ComparisonData';
	$value_table 	= 'Comparisons';
		
	unset($geneExpressionRawFileCandidates);
	if ($otherOptions['comparison'] == 1){
		//Global
		if ($dataSource['public'] != ''){
			$geneExpressionRawFileCandidates[] = tabix_search_records_with_index('',               $secondaryIndexes, $sql_table, 'Path', $tabixFilePath);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$geneExpressionRawFileCandidates[] = tabix_search_records_with_index_internal_data($projectIndex, '', $secondaryIndexes, $sql_table, 'Path', $tabixFilePathPrivate);
			}
		}
	} else {
		//Local
		if ($dataSource['public'] != ''){
			$geneExpressionRawFileCandidates[] = tabix_search_records_with_index($geneIndexTarget, $secondaryIndexes, $sql_table, 'Path', $tabixFilePath);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$geneExpressionRawFileCandidates[] = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexTarget, $secondaryIndexes, $sql_table, 'Path', $tabixFilePathPrivate);
			}
		}
	}
	startTimer(__FUNCTION__ . '::tabix');

	
	startTimer(__FUNCTION__ . '::geneExpressionValueIndex');
	$otherOptions['transform_value'] = abs(floatval($otherOptions['transform_value']));
	unset($secondaryIndexString);
	
	foreach ($geneExpressionRawFileCandidates as $currentFileCandidateKey => $currentFileCandidate){
		if (($handle = fopen($currentFileCandidate, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE){
				
				$tempValue	= explode("\t", trim($buffer));
				
				//0: 'ComparisonIndex', 1:'GeneIndex', 2:'Name', 3:'Log2FoldChange', 4:'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue'
			
				$currentGeneIndex 		= intval($tempValue[1]);
				$currentSecondaryIndex 	= intval($tempValue[0]);
				$originalValue			= trim($tempValue[3]);
				$transformedValue		= trim($tempValue[3]);
				
				if (($originalValue === '') || is_null($originalValue) || ($originalValue === '.') || !is_numeric($originalValue)){
					$originalValue 		= 'NA';
					$transformedValue	= 'NA';
				}
				
				unset($keepGoing);
				if (!isset($score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				} elseif (is_numeric($tempValue[4]) && ($tempValue[4] < $score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				} elseif (is_numeric($tempValue[4]) && !is_numeric($score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				}
				
				if (!$keepGoing) continue;
				
				$geneExpressionValueIndex[$currentGeneIndex][$currentSecondaryIndex] 		= $transformedValue;
				$secondaryIndexString[$currentSecondaryIndex] 								= $currentSecondaryIndex;
				$score[$currentGeneIndex][$currentSecondaryIndex] 							= $tempValue[4];
	
			}
			fclose($handle);
		}
	}
	unset($score);
	startTimer(__FUNCTION__ . '::geneExpressionValueIndex');

	

	//*******************
	// Search Secondary Index
	//*******************
	startTimer(__FUNCTION__ . '::secondaries');
	$secondaryIndexString = implode(',', $secondaryIndexString);

	unset($secondaries);	
	if ($secondaryIndexString != ''){
		if ($dataSource['public'] != ''){
			$sql_table		= 'Comparisons';
			$SQL 			= "SELECT ComparisonIndex, ComparisonID FROM {$sql_table} WHERE ComparisonIndex IN ({$secondaryIndexString}) ORDER BY FIELD(ComparisonIndex, {$secondaryIndexStringOrg})";	
			$secondaries	= getSQL($SQL, 'GetAssoc', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			$sql_table 		= 'App_User_Data_Comparisons';
			$SQL			= "SELECT ComparisonIndex, ComparisonID FROM {$sql_table} WHERE ComparisonIndex IN ({$secondaryIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(ComparisonIndex, {$secondaryIndexStringOrg})";
			$secondariesPrivate	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($secondariesPrivate as $tempKeyX => $tempValueX){
				$secondaries[$tempKeyX] = $tempValueX;
			}
		}
	}
	
	$results['Gene_Source'] 				= &$geneInfoSource;	
	startTimer(__FUNCTION__ . '::secondaries');

	$otherOptions['cutoff'] = abs(floatval($otherOptions['cutoff']));
	$otherOptions['limit'] 	= abs(intval($otherOptions['limit']));
	
	$min_matched_count = intval(floor(array_size($secondaries) * $otherOptions['min_matched'] / 100));
	
	
	startTimer(__FUNCTION__ . '::correlationCoefficientCache');
	foreach($geneInfoSource as $geneIndexSource => $geneNameSource){

		if (!isset($geneExpressionValueIndex[$geneIndexSource])) continue;

		foreach($geneInfoTarget as $geneIndexTarget => $geneTargetSource){
			
			if (!isset($geneExpressionValueIndex[$geneIndexTarget])) continue;
			
			if ($geneIndexSource == $geneIndexTarget) continue;
			
			if (isset($correlationCoefficientCache[$geneIndexTarget][$geneIndexSource])){
				$correlationCoefficient = $correlationCoefficientCache[$geneIndexTarget][$geneIndexSource];
			} else {
				$correlationCoefficient = getCorrelationCoefficient($geneExpressionValueIndex[$geneIndexSource], $geneExpressionValueIndex[$geneIndexTarget], $otherOptions['method'], $coefficientSize);
			}
			$correlationCoefficientCache[$geneIndexSource][$geneIndexTarget] = $correlationCoefficient;


			unset($qualified2);
			if ($min_matched_count > 0){
				if ($coefficientSize >= $min_matched_count){
					$qualified2 = 1;
				} else {
					$qualified2 = 0;
					$results['Summary']['Rejected']['Min_Matched']++;
				}
			} else {
				$qualified2 = 1;	
			}

			unset($qualified);			
			if ($qualified2){
				if ($otherOptions['direction'] == 1){
					//ABS
					$corr = abs($correlationCoefficient);
					if ($corr >= $otherOptions['cutoff']){
						$qualified = 1;
					} else {
						$results['Summary']['Rejected']['Cutoff']++;	
					}
					
				} elseif ($otherOptions['direction'] == 2){
					//Positive
					$corr = $correlationCoefficient;
					if ($corr >= $otherOptions['cutoff']){
						$qualified = 1;
					} else {
						$results['Summary']['Rejected']['Cutoff']++;	
					}
				} else {
					//Negative
					$corr = $correlationCoefficient;
					if (($otherOptions['cutoff'] == 0) || ($corr <= $otherOptions['cutoff']*-1)){
						$qualified = 1;
					} else {
						$results['Summary']['Rejected']['Cutoff']++;	
					}
				}
			}
			
			$results['Summary']['Count_Total']++;
			if ($qualified){
				$results['Correlation_Coefficient_Transformed'][$geneIndexSource][$geneIndexTarget] = $corr;
				$results['Summary']['Count']++;
			}
		}
		
		
		if (array_size($results['Correlation_Coefficient_Transformed'][$geneIndexSource]) > 0){
			if (($otherOptions['direction'] == 1) || ($otherOptions['direction'] == 2)){
				arsort($results['Correlation_Coefficient_Transformed'][$geneIndexSource]);
			} else {
				asort($results['Correlation_Coefficient_Transformed'][$geneIndexSource]);
			}
			
			$results['Summary']['HasResult'] = 1;
			
		}
		
		if ((array_size($results['Correlation_Coefficient_Transformed'][$geneIndexSource]) > $otherOptions['limit']) && ($otherOptions['limit'] > 0)){
			$results['Correlation_Coefficient_Transformed'][$geneIndexSource] = array_slice($results['Correlation_Coefficient_Transformed'][$geneIndexSource], 0, $otherOptions['limit'], true);
		}
		
		foreach($results['Correlation_Coefficient_Transformed'][$geneIndexSource] as $currentgeneIndexTarget => $tempValueX){
			$results['Gene_Target'][$currentgeneIndexTarget]					 				= $geneInfoTarget[$currentgeneIndexTarget];
			$results['Correlation_Coefficient_Raw'][$geneIndexSource][$currentgeneIndexTarget]	= $correlationCoefficientCache[$geneIndexSource][$currentgeneIndexTarget];
			
			$results['geneExpressionValueIndex'][$currentgeneIndexTarget] 						= $geneExpressionValueIndex[$currentgeneIndexTarget];
		}
		
		$results['geneExpressionValueIndex'][$geneIndexSource]		= $geneExpressionValueIndex[$geneIndexSource];
		
	}
	
	
	startTimer(__FUNCTION__ . '::correlationCoefficientCache');


	startTimer(__FUNCTION__ . '::wrapUp');
	$results['Summary']['ComparisonIndex'] 	= $secondaries;
	$results['Summary']['cacheKey'] 		= $cacheKey;
	$results['Summary']['Function'] 		= __FUNCTION__;
	$results['Summary']['otherOptions'] 	= $otherOptions;
	$results['Summary']['min_matched_count'] 	= $min_matched_count;
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	startTimer(__FUNCTION__ . '::wrapUp');
	
	return $results;
	
}

function prepareGenesComparisonDataCorrelation($geneIndexes, $comparisonIndexX, $comparisonIndexY, $otherOptions, $dataSource, $internalProjectIndexes){
	global $APP_CONFIG;

	startTimer(__FUNCTION__ . '::checkCache');
	$geneIndexes 		= array_clean($geneIndexes);	
	$comparisonIndexX 	= intval($comparisonIndexX);
	$comparisonIndexY 	= intval($comparisonIndexY);
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey 			= __FUNCTION__ . '::' . md5(json_encode($geneIndexes) . '::' . 
													json_encode($comparisonIndexX) . '::' . 
													json_encode($comparisonIndexY) . '::' . 
													json_encode($otherOptions) . '::' . 
													json_encode($dataSource) . '::' . 
													json_encode($internalProjectIndexes) . '::' . 
													$version
													);
													
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		startTimer(__FUNCTION__ . '::checkCache');
		return $resultsFromCache;
	}
	startTimer(__FUNCTION__ . '::checkCache');
	
	
	
	//*******************
	// Search Gene Index
	//*******************
	startTimer(__FUNCTION__ . '::geneInfoSource');
	$sql_table 			= 'GeneCombined';
	$geneIndexString	= implode(', ', $geneIndexes);
	$SQL 				= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
	$geneInfoSource		= getSQL($SQL, 'GetAssoc', $sql_table);
	startTimer(__FUNCTION__ . '::geneInfoSource');
	
	if (array_size($geneInfoSource) <= 0){
		return false;
	}

	
	startTimer(__FUNCTION__ . '::geneInfoTarget');
	if ($otherOptions['comparison'] == 2){
		//Local
		$geneInfoTarget = &$geneInfoSource;
	} else {
		//Global
		$SQL 				= "SELECT GeneName, GeneIndex FROM {$sql_table} ORDER BY GeneName ASC";
		$geneIndexes		= array_values(getSQL($SQL, 'GetAssoc', $sql_table));
		$geneIndexString	= implode(', ', $geneIndexes);
		$SQL 				= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
		$geneInfoTarget		= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	startTimer(__FUNCTION__ . '::geneInfoTarget');

	
	
	//*******************
	// Search Secondary
	//*******************
	startTimer(__FUNCTION__ . '::tabix');
	$geneIndexTarget			= array_keys($geneInfoTarget);
	$geneIndexString 			= implode(', ', $geneIndexTarget);
	$secondaryIndexString 		= implode(', ', $secondaryIndexes);
	$secondaryIndexStringOrg 	= $secondaryIndexString;
	
	$sql_table 		= 'ComparisonData';
	$value_table 	= 'Comparisons';
		
	unset($geneExpressionRawFileCandidates);
	if ($otherOptions['comparison'] == 1){
		//Global
		if ($dataSource['public'] != ''){
			$geneExpressionRawFileCandidates[] = tabix_search_records_with_index('',               $secondaryIndexes, $sql_table, 'Path', $tabixFilePath);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$geneExpressionRawFileCandidates[] = tabix_search_records_with_index_internal_data($projectIndex, '', $secondaryIndexes, $sql_table, 'Path', $tabixFilePathPrivate);
			}
		}
	} else {
		//Local
		if ($dataSource['public'] != ''){
			$geneExpressionRawFileCandidates[] = tabix_search_records_with_index($geneIndexTarget, $secondaryIndexes, $sql_table, 'Path', $tabixFilePath);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$geneExpressionRawFileCandidates[] = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexTarget, $secondaryIndexes, $sql_table, 'Path', $tabixFilePathPrivate);
			}
		}
	}
	startTimer(__FUNCTION__ . '::tabix');

	
	startTimer(__FUNCTION__ . '::geneExpressionValueIndex');
	$otherOptions['transform_value'] = abs(floatval($otherOptions['transform_value']));
	unset($secondaryIndexString);
	
	foreach ($geneExpressionRawFileCandidates as $currentFileCandidateKey => $currentFileCandidate){
		if (($handle = fopen($currentFileCandidate, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE){
				
				$tempValue	= explode("\t", trim($buffer));
				
				//0: 'ComparisonIndex', 1:'GeneIndex', 2:'Name', 3:'Log2FoldChange', 4:'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue'
			
				$currentGeneIndex 		= intval($tempValue[1]);
				$currentSecondaryIndex 	= intval($tempValue[0]);
				$originalValue			= trim($tempValue[3]);
				$transformedValue		= trim($tempValue[3]);
				
				if (($originalValue === '') || is_null($originalValue) || ($originalValue === '.') || !is_numeric($originalValue)){
					$originalValue 		= 'NA';
					$transformedValue	= 'NA';
				}
				
				unset($keepGoing);
				if (!isset($score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				} elseif (is_numeric($tempValue[4]) && ($tempValue[4] < $score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				} elseif (is_numeric($tempValue[4]) && !is_numeric($score[$currentGeneIndex][$currentSecondaryIndex])){
					$keepGoing = 1;
				}
				
				if (!$keepGoing) continue;
				
				$geneExpressionValueIndex[$currentGeneIndex][$currentSecondaryIndex] 		= $transformedValue;
				$secondaryIndexString[$currentSecondaryIndex] 								= $currentSecondaryIndex;
				$score[$currentGeneIndex][$currentSecondaryIndex] 							= $tempValue[4];
	
			}
			fclose($handle);
		}
	}
	unset($score);
	startTimer(__FUNCTION__ . '::geneExpressionValueIndex');



	//*******************
	// Search Secondary Index
	//*******************
	startTimer(__FUNCTION__ . '::secondaries');
	$secondaryIndexString = implode(',', $secondaryIndexString);

	unset($secondaries);	
	if ($secondaryIndexString != ''){
		if ($dataSource['public'] != ''){
			$sql_table		= 'Comparisons';
			$SQL 			= "SELECT ComparisonIndex, ComparisonID FROM {$sql_table} WHERE ComparisonIndex IN ({$secondaryIndexString}) ORDER BY FIELD(ComparisonIndex, {$secondaryIndexStringOrg})";	
			$secondaries	= getSQL($SQL, 'GetAssoc', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			$sql_table 		= 'App_User_Data_Comparisons';
			$SQL			= "SELECT ComparisonIndex, ComparisonID FROM {$sql_table} WHERE ComparisonIndex IN ({$secondaryIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(ComparisonIndex, {$secondaryIndexStringOrg})";
			$secondariesPrivate	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($secondariesPrivate as $tempKeyX => $tempValueX){
				$secondaries[$tempKeyX] = $tempValueX;
			}
		}
	}
	
	$results['Gene_Source'] 				= &$geneInfoSource;	
	startTimer(__FUNCTION__ . '::secondaries');

	$otherOptions['cutoff'] = abs(floatval($otherOptions['cutoff']));
	$otherOptions['limit'] 	= abs(intval($otherOptions['limit']));
	
	startTimer(__FUNCTION__ . '::correlationCoefficientCache');
	foreach($geneInfoSource as $geneIndexSource => $geneNameSource){

		if (!isset($geneExpressionValueIndex[$geneIndexSource])) continue;

		foreach($geneInfoTarget as $geneIndexTarget => $geneTargetSource){
			
			if (!isset($geneExpressionValueIndex[$geneIndexTarget])) continue;
			
			if ($geneIndexSource == $geneIndexTarget) continue;
			
			if (isset($correlationCoefficientCache[$geneIndexTarget][$geneIndexSource])){
				$correlationCoefficient = $correlationCoefficientCache[$geneIndexTarget][$geneIndexSource];
			} else {
				$correlationCoefficient = getCorrelationCoefficient($geneExpressionValueIndex[$geneIndexSource], $geneExpressionValueIndex[$geneIndexTarget], $otherOptions['method']);
			}
			$correlationCoefficientCache[$geneIndexSource][$geneIndexTarget] = $correlationCoefficient;

			
			unset($qualified);
			if ($otherOptions['direction'] == 1){
				//ABS
				$corr = abs($correlationCoefficient);
				if ($corr >= $otherOptions['cutoff']){
					$qualified = 1;
				}
				
			} elseif ($otherOptions['direction'] == 2){
				//Positive
				$corr = $correlationCoefficient;
				if ($corr >= $otherOptions['cutoff']){
					$qualified = 1;
				}
			} else {
				//Negative
				$corr = $correlationCoefficient;
				if (($otherOptions['cutoff'] == 0) || ($corr <= $otherOptions['cutoff']*-1)){
					$qualified = 1;
				}
			}
			
			$results['Summary']['Count_Total']++;
			if ($qualified){
				$results['Correlation_Coefficient_Transformed'][$geneIndexSource][$geneIndexTarget] = $corr;
				$results['Summary']['Count']++;
			}
		}
		
		
		if (array_size($results['Correlation_Coefficient_Transformed'][$geneIndexSource]) > 0){
			if (($otherOptions['direction'] == 1) || ($otherOptions['direction'] == 2)){
				arsort($results['Correlation_Coefficient_Transformed'][$geneIndexSource]);
			} else {
				asort($results['Correlation_Coefficient_Transformed'][$geneIndexSource]);
			}
			
			$results['Summary']['HasResult'] = 1;
			
		}
		
		if ((array_size($results['Correlation_Coefficient_Transformed'][$geneIndexSource]) > $otherOptions['limit']) && ($otherOptions['limit'] > 0)){
			$results['Correlation_Coefficient_Transformed'][$geneIndexSource] = array_slice($results['Correlation_Coefficient_Transformed'][$geneIndexSource], 0, $otherOptions['limit'], true);
		}
		
		foreach($results['Correlation_Coefficient_Transformed'][$geneIndexSource] as $currentgeneIndexTarget => $tempValueX){
			$results['Gene_Target'][$currentgeneIndexTarget]					 				= $geneInfoTarget[$currentgeneIndexTarget];
			$results['Correlation_Coefficient_Raw'][$geneIndexSource][$currentgeneIndexTarget]	= $correlationCoefficientCache[$geneIndexSource][$currentgeneIndexTarget];
			
			$results['geneExpressionValueIndex'][$currentgeneIndexTarget] 						= $geneExpressionValueIndex[$currentgeneIndexTarget];
		}
		
		$results['geneExpressionValueIndex'][$geneIndexSource]		= $geneExpressionValueIndex[$geneIndexSource];
		
	}
	
	
	startTimer(__FUNCTION__ . '::correlationCoefficientCache');


	startTimer(__FUNCTION__ . '::wrapUp');
	$results['Summary']['ComparisonIndex'] 	= $secondaries;
	$results['Summary']['cacheKey'] 		= $cacheKey;
	$results['Summary']['Function'] 		= __FUNCTION__;
	$results['Summary']['otherOptions'] 	= $otherOptions;
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	startTimer(__FUNCTION__ . '::wrapUp');
	
	return $results;
	
}


?>