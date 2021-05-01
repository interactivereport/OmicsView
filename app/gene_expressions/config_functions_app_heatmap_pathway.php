<?php

function readPAGE($comparisonIndexes){
	
	foreach($comparisonIndexes as $tempKey => $comparisonIndex){
		$file = internal_data_get_comparison_page_file($comparisonIndex);
		$temp = readFirstFewLinesFromFile($file, 0, 1, 'csv');
		$results[$comparisonIndex] = $temp['Body'];
	}
	
	return $results;
}


function readHOMER($comparisonIndexes, $direction, $category){
	
	foreach($comparisonIndexes as $tempKey => $comparisonIndex){
		$file = internal_data_get_comparison_homer_file($comparisonIndex, $direction, $category);

		$temp = readFirstFewLinesFromFile($file, 0, 1, 'tab');
		$results[$comparisonIndex] = $temp['Body'];
	}
	
	return $results;
}


function preparePAGEData($comparisonIndexes, $filter_by_value_enable, $filter_by_value, $filter_by_top){
	
	global $ORDER_ARRAY, $SORT_OPTIONS;
	
	$version = '2019-01-25 10:13';
	
	if ($filter_by_value_enable){
		$cacheKey = __FUNCTION__ . '::' . md5(json_encode($comparisonIndexes) . '::' . 
												$filter_by_value_enable . '::' . 
												$filter_by_value . '::' . 
												$filter_by_top . '::' . 
												$version
												);
	} else {
		$cacheKey = __FUNCTION__ . '::' . md5(json_encode($comparisonIndexes) . '::' . 
												$filter_by_top . '::' . 
												$version
												);
	}
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	
	//PAGE
	$rawData = readPAGE($comparisonIndexes);

	$candidate = array();
	if (!$filter_by_value_enable){
		
		foreach($rawData as $comparisonIndex => $currentDataGroup){
			foreach($currentDataGroup as $tempKey => $currentData){
				
				$identifier = trim($currentData['Name']);
				
				if (!isset($candidate[$identifier])){
					$candidate[$identifier] = abs($currentData['Z Score']);
				} else {
					//The higher the better
					if (abs($currentData['Z Score']) > abs($candidate[$identifier])){
						$candidate[$identifier] = abs($currentData['Z Score']);
					}
				}
			}
		}

	} else {

		foreach($rawData as $comparisonIndex => $currentDataGroup){
			foreach($currentDataGroup as $tempKey => $currentData){
				if (abs($currentData['Z Score']) >= $filter_by_value){
					
					$identifier = trim($currentData['Name']);

					if (!isset($candidate[$identifier])){
						$candidate[$identifier] = abs($currentData['Z Score']);
					} else {
						//The higher the better
						if (abs($currentData['Z Score']) > abs($candidate[$identifier])){
							$candidate[$identifier] = abs($currentData['Z Score']);
						}
					}
				}
			}
		}
	}


	arsort($candidate, SORT_NUMERIC);
	$candidate = array_slice($candidate, 0, $filter_by_top);
	
	putSQLCache($cacheKey, $candidate, '', __FUNCTION__);


	return $candidate;
	
	
}


function prepareHomerData($comparisonIndexes, $direction, $category, $filter_by_value_enable, $filter_by_value, $filter_by_top){
	
	global $ORDER_ARRAY, $SORT_OPTIONS;
	
	$version = '2019-01-25 10:13';
	
	if ($filter_by_value_enable){
		$cacheKey = __FUNCTION__ . '::' . md5(json_encode($comparisonIndexes) . '::' . 
												$direction . '::' . 
												$category . '::' . 
												$filter_by_value_enable . '::' .
												$filter_by_value . '::' . 
												$filter_by_top . '::' . 
												$version
												);
	} else {
		$cacheKey = __FUNCTION__ . '::' . md5(json_encode($comparisonIndexes) . '::' . 
												$direction . '::' . 
												$category . '::' . 
												$filter_by_top . '::' .
												$version
												);
	}
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$rawData = readHOMER($comparisonIndexes, $direction, $category);
		
	$candidate = array();
	if (!$filter_by_value_enable){
	
		foreach($rawData as $comparisonIndex => $currentDataGroup){
			foreach($currentDataGroup as $tempKey => $currentData){
				
				$identifier = trim($currentData['Term']);
				
				if (!isset($candidate[$identifier])){
					$candidate[$identifier] = $currentData['logP'];
				} else {
					if ($currentData['logP'] < $candidate[$identifier]){
						$candidate[$identifier] = $currentData['logP'];
					}
				}
			}
		}

	} else {
		
		foreach($rawData as $comparisonIndex => $currentDataGroup){
			foreach($currentDataGroup as $tempKey => $currentData){
				if ($currentData['logP'] <= $filter_by_value){
					
					$identifier = trim($currentData['Term']);
					
					if (!isset($candidate[$identifier])){
						$candidate[$identifier] = $currentData['logP'];
					} else {
						if ($currentData['logP'] < $candidate[$identifier]){
							$candidate[$identifier] = $currentData['logP'];
						}
					}
				}
			}
		}
	}
	
	asort($candidate, SORT_NUMERIC);
	$candidate = array_slice($candidate, 0, $filter_by_top);
	
	putSQLCache($cacheKey, $candidate, '', __FUNCTION__);


	return $candidate;
	
	
}


function preparePAGEDataByGeneSet($comparisons, $geneSets){
	
	
	global $ORDER_ARRAY, $SORT_OPTIONS;
	
	$version = '2019-01-25 10:13';
	
	$cacheKey = __FUNCTION__ . '::' . md5(json_encode($comparisons) . '::' . 
											json_encode($geneSets) . '::' . 
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}

	$geneSets = array_reverse($geneSets);
	$geneSets = array_combine($geneSets, $geneSets);

	
	//PAGE
	$rawData = readPAGE(array_keys($comparisons));

	$candidate = array();
	$results['Export']['Heatmap']['Headers']['Pathway'] = 'Gene Set';
	foreach($rawData as $comparisonIndex => $currentDataGroup){
		
		$results['JS']['x'][] = '"' . sanitizeJavaScriptValue($comparisons[$comparisonIndex]) . '"';
		$results['JS']['comparisonIndex'][] = $comparisonIndex;
		
		$results['Export']['Heatmap']['Headers'][$comparisonIndex] = $comparisons[$comparisonIndex];
		
		foreach($currentDataGroup as $tempKey => $currentData){
			
			$identifier = trim($currentData['Name']);
			
			if ($geneSets[$identifier] == '') continue;
			
			$candidate[$identifier][$comparisonIndex] = $currentData;

		}
	}
	
	
	foreach($geneSets as $tempKey => $identifier){
		if (!isset($results['JS']['y'][$identifier])){
			$results['JS']['y'][$identifier] = 	'"' . sanitizeJavaScriptValue($identifier) . '"';
			
			$identifier_length = strlen($identifier);
			
			if ($results['Summary']['max_identifier_length'] < $identifier_length){
				$results['Summary']['max_identifier_length'] = $identifier_length;
			}
			
		}
		
		$results['Export']['Heatmap']['Body'][$identifier]['Pathway'] = $identifier;
		
		foreach($candidate[$identifier] as $comparisonIndex => $currentData){
			$results['JS']['z'][$identifier][] = $currentData['Z Score'];
			
			$results['Export']['Heatmap']['Body'][$identifier][$comparisonIndex] = $currentData['Z Score'];
			
			$text 	= array();
			$text[] = sanitizeJavaScriptValue("Gene Set: {$identifier}");
			$text[] = sanitizeJavaScriptValue("Comparison ID: {$comparisons[$comparisonIndex]}");
			$text[] = sanitizeJavaScriptValue("Z-Score: " . round($currentData['Z Score'], 3));
			$text[] = sanitizeJavaScriptValue("# of Genes: " . number_format($currentData['Genes (tot)']));
			$text[] = sanitizeJavaScriptValue("p-value: " . round($currentData['p-value'], 7) );
			$text[] = sanitizeJavaScriptValue("FDR: " . round($currentData['FDR'], 3) );
			
			$results['JS']['text'][$identifier][] = '"' . implode('<br />', $text) . '"';
			
			
		}
		
		$results['JS']['z'][$identifier] = '[' . implode(',', $results['JS']['z'][$identifier]) . ']';
		$results['JS']['text'][$identifier] = '[' . implode(',', $results['JS']['text'][$identifier]) . ']';
	}
	
	$results['Export']['Heatmap']['Body'] = array_reverse($results['Export']['Heatmap']['Body']);
	
	$results['JS']['y'] = array_values($results['JS']['y']);
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
	
}


function prepareHomerDataByGeneSet($comparisons, $geneSets, $direction, $category){
	
	
	global $ORDER_ARRAY, $SORT_OPTIONS;
	
	$version = '2019-01-25 10:13';
	
	$cacheKey = __FUNCTION__ . '::' . md5(json_encode($comparisons) . '::' . 
											json_encode($geneSets) . '::' . 
											$direction . '::' .
											$category . '::' .
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$geneSets = array_reverse($geneSets);
	$geneSets = array_combine($geneSets, $geneSets);
	
	
	
	//PAGE
	$rawData = readHOMER(array_keys($comparisons), $direction, $category);

	
	$candidate = array();
	$results['Export']['Heatmap']['Headers']['Pathway'] = 'Pathway';
	foreach($rawData as $comparisonIndex => $currentDataGroup){
		
		$results['JS']['x'][] = '"' . sanitizeJavaScriptValue($comparisons[$comparisonIndex]) . '"';
		
		$results['Export']['Heatmap']['Headers'][$comparisonIndex] = $comparisons[$comparisonIndex];
		
		
		$results['JS']['comparisonIndex'][] = $comparisonIndex;
		
		foreach($currentDataGroup as $tempKey => $currentData){
			
			$identifier = trim($currentData['Term']);
			
			if ($geneSets[$identifier] == '') continue;
			
			$candidate[$identifier][$comparisonIndex] = $currentData;

		}
	}
	
	
	foreach($geneSets as $tempKey => $identifier){
		if (!isset($results['JS']['y'][$identifier])){
			$results['JS']['y'][$identifier] = 	'"' . sanitizeJavaScriptValue($identifier) . '"';
			
			$identifier_length = strlen($identifier);
			
			if ($results['Summary']['max_identifier_length'] < $identifier_length){
				$results['Summary']['max_identifier_length'] = $identifier_length;
			}
			
		}
		
		$results['Export']['Heatmap']['Body'][$identifier]['Pathway'] = $identifier;
		
		foreach($candidate[$identifier] as $comparisonIndex => $currentData){
			$results['JS']['z'][$identifier][] = $currentData['logP'];
			
			$results['Export']['Heatmap']['Body'][$identifier][$comparisonIndex] = $currentData['logP'];
			
			$text 	= array();
			$text[] = sanitizeJavaScriptValue("Gene Set: {$identifier}");
			$text[] = sanitizeJavaScriptValue("Comparison ID: {$comparisons[$comparisonIndex]}");
			$text[] = sanitizeJavaScriptValue("log(p-value): " . round($currentData['logP'], 3));
			$text[] = sanitizeJavaScriptValue("# of Genes: " . number_format($currentData['Target Genes in Term']));
			
			$results['JS']['text'][$identifier][] = '"' . implode('<br />', $text) . '"';
			
			
		}
		
		$results['JS']['z'][$identifier] = '[' . implode(',', $results['JS']['z'][$identifier]) . ']';
		$results['JS']['text'][$identifier] = '[' . implode(',', $results['JS']['text'][$identifier]) . ']';
	}
	
	$results['Export']['Heatmap']['Body'] = array_reverse($results['Export']['Heatmap']['Body']);
	
	$results['JS']['y'] = array_values($results['JS']['y']);
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
	
}




?>