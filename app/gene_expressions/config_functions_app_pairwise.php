<?php

function prepareCategoryPairwiseDataFromSample($sampleIndexes, $sampleAttribute1, $sampleAttribute2, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = '2018-04-14 21:48';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										json_encode($sampleAttribute1) . '::' . 
										json_encode($sampleAttribute2) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$samples 			= array();
	$sampleIndexString 	= implode(',', $sampleIndexes);
	if ($dataSource['public'] != ''){
		$sql_table	= 'Samples';

		
		
		$SQL_FIELDS		= array();
		$SQL_FIELDS[] 	= "`SampleIndex`";
		$SQL_FIELDS[] 	= "`SampleID`";
		
		if (isset($APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttribute1])){
			$SQL_FIELDS[] 	= "`{$sampleAttribute1}`";
		}
		
		if (isset($APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttribute2])){
			$SQL_FIELDS[] 	= "`{$sampleAttribute2}`";
		}
		
		$SQL_FIELDS = implode(', ', $SQL_FIELDS);
		
		$SQL 		= "SELECT {$SQL_FIELDS} FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString})";
		
		
		$samples	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		
	}
	
	if ($dataSource['private'] != ''){
		$sql_table	= 'App_User_Data_Samples';
		$SQL 		= "SELECT * FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";	

		$temp	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		foreach($temp as $tempKey => $tempValue){
			$samples[$tempKey] = $tempValue;
		}
	}
	
	$results = $samples;
	
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
	
	
}

function prepareColorPairwiseDataFromSample($sampleIndexes, $sampleAttribute, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = '2018-04-14 17:55';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										json_encode($sampleAttribute) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}

	$samples 			= array();
	$sampleIndexString 	= implode(',', $sampleIndexes);
	if ($dataSource['public'] != ''){
		$sql_table	= 'Samples';

		$SQL_FIELDS		= array();
		$SQL_FIELDS[] 	= "`SampleIndex`";
		
		if (isset($APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttribute])){
			$SQL_FIELDS[] 	= "`{$sampleAttribute}`";
		}
		
		$SQL_FIELDS = implode(', ', $SQL_FIELDS);
		
		$SQL 		= "SELECT {$SQL_FIELDS} FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString})";

		$samples	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		
	}
	
	if ($dataSource['private'] != ''){
		$sql_table	= 'App_User_Data_Samples';
		$SQL 		= "SELECT * FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";	

		$temp	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		foreach($temp as $tempKey => $tempValue){
			$samples[$tempKey] = $tempValue[$sampleAttribute];
		}
	}
	
	
	foreach($samples as $currentSampleIndex => $currentSampleValue){
		$results['Raw'][$currentSampleIndex] 	= ucwords2($currentSampleValue);
		$results['JS'][$currentSampleIndex] 	= '"' . sanitizeJavaScriptValue(ucwords2($currentSampleValue)) . '"';
	}
	
	$results['Type'] = 'Sample';
	$results['Name'] = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttribute]['Title'];
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
	
	
}

function prepareNumericPairwiseDataFromSample($sampleIndexes, $sampleAttribute, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	
	
	$version = '2018-04-14 17:31';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										$sampleAttribute 			. '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$samples 					= array();
	$sampleIndexString 			= implode(',', $sampleIndexes);

	if ($dataSource['public'] != ''){
		$sql_table				= 'Samples';
		$SQL 					= "SELECT `SampleIndex`, `SampleID`, `{$sampleAttribute}` FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString})";
		$samples				= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	
	
	if ($dataSource['private'] != ''){
		$sql_table	= 'App_User_Data_Samples';
		$SQL 		= "SELECT * FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";	

		$temp		= getSQL($SQL, 'GetAssoc', $sql_table);
		
		if (array_size($temp) > 0){
			foreach($temp as $tempKey => $tempValue){
				$samples[$tempKey] = $tempValue;
			}
		}
	}
	
	foreach($samples as $currentSampleIndex => $currentSampleInfo){

		$results['Data'][$currentSampleIndex]['SampleID'] 		= $samples[$currentSampleIndex]['SampleID'];
		$results['Data'][$currentSampleIndex]['Original'] 		= $samples[$currentSampleIndex][$sampleAttribute];
		
		$number													= string2number($samples[$currentSampleIndex][$sampleAttribute]);
		
		if (is_numeric($number)){
			$results['Data'][$currentSampleIndex]['Raw'] 			= $number;
			$results['Data'][$currentSampleIndex]['Transformed'] 	= $number;
		} else {
			$results['Data'][$currentSampleIndex]['Raw']			= '';
			$results['Data'][$currentSampleIndex]['Transformed'] 	= '';
		}
	}
	
	$results['Name'] 		= $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttribute]['Title'];;
	$results['Axis_Print'] 	= $results['Name'];
	$results['Axis_HTML'] 	= $results['Name'];
	$results['Type'] 		= 'Sample';
	

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
	
}

function prepareNumericPairwiseDataFromGeneExpression($sampleIndexes, $geneIndex, $geneName, $platformType, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	
	
	$version = '2018-04-14 17:31';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										$geneIndex 					. '::' . 
										$platformType				. '::' . 
										json_encode($dataSource)	. '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$getGeneExpressionTableInfo = getGeneExpressionTableInfo($platformType);
	$valueColumn				= $getGeneExpressionTableInfo['Column'];
	
	
	$samples 					= array();
	$sampleIndexString 			= implode(',', $sampleIndexes);
	
	
	if ($dataSource['public'] != ''){
		$sql_table				= 'Samples';
		$SQL 					= "SELECT `SampleIndex`, `SampleID` FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString})";
		$samples				= getSQL($SQL, 'GetAssoc', $sql_table);


		$sql_table 				= $getGeneExpressionTableInfo['Table'];
		$geneExpressionValue	= tabix_search_records_with_index(array($geneIndex), $sampleIndexes, $sql_table);
	}
	
	
	if ($dataSource['private'] != ''){
		
		
		$sql_table	= 'App_User_Data_Samples';
		$SQL 		= "SELECT `SampleIndex`, `SampleID` FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";	

		$temp		= getSQL($SQL, 'GetAssoc', $sql_table);
		
		if (array_size($temp) > 0){
			foreach($temp as $tempKey => $tempValue){
				$samples[$tempKey] = $tempValue;
			}
	
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, array($geneIndex), '', $sql_table);
				
				foreach($temp as $tempKey => $tempValue){
					$geneExpressionValue[] = $tempValue;
				}
				
				unset($temp);
			}
		}
		
	}
	
	
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentSampleIndex = $tempValue['SampleIndex'];
		$results['Data'][$currentSampleIndex]['SampleID'] 		= $samples[$currentSampleIndex];
		$results['Data'][$currentSampleIndex]['Original'] 		= trim($tempValue[$valueColumn]);
		$results['Data'][$currentSampleIndex]['Raw'] 			= trim($tempValue[$valueColumn]);
		$results['Data'][$currentSampleIndex]['Transformed'] 	= log2(floatval($tempValue[$valueColumn]) + $APP_CONFIG['canvasxpress']['Log_Add_Value']);
	}
	
	$results['Name'] 		= $geneName;
	$results['Axis_Print'] 	= "{$geneName}: log2({$valueColumn} + {$APP_CONFIG['canvasxpress']['Log_Add_Value']})";
	$results['Axis_HTML'] 	= "{$geneName}: log<sub>2</sub>({$valueColumn} + {$APP_CONFIG['canvasxpress']['Log_Add_Value']})";
	$results['Type'] 		= 'Gene';

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
	
}

function prepareColorPairwiseDataFromGeneExpression($sampleIndexes, $geneIndex, $geneName, $platformType, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	
	
	$version = '2018-04-24 22:52';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										$geneIndex 					. '::' . 
										$platformType				. '::' . 
										json_encode($dataSource)	. '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$getGeneExpressionTableInfo = getGeneExpressionTableInfo($platformType);
	$valueColumn				= $getGeneExpressionTableInfo['Column'];
	
	
	$samples 					= array();
	$sampleIndexString 			= implode(',', $sampleIndexes);
	
	
	if ($dataSource['public'] != ''){
		$sql_table				= 'Samples';
		$SQL 					= "SELECT `SampleIndex`, `SampleID` FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString})";
		$samples				= getSQL($SQL, 'GetAssoc', $sql_table);


		$sql_table 				= $getGeneExpressionTableInfo['Table'];
		$geneExpressionValue	= tabix_search_records_with_index(array($geneIndex), $sampleIndexes, $sql_table);
	}
	
	
	if ($dataSource['private'] != ''){
		
		
		$sql_table	= 'App_User_Data_Samples';
		$SQL 		= "SELECT `SampleIndex`, `SampleID` FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";	

		$temp		= getSQL($SQL, 'GetAssoc', $sql_table);
		
		if (array_size($temp) > 0){
			foreach($temp as $tempKey => $tempValue){
				$samples[$tempKey] = $tempValue;
			}
	
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, array($geneIndex), '', $sql_table);
				
				foreach($temp as $tempKey => $tempValue){
					$geneExpressionValue[] = $tempValue;
				}
				
				unset($temp);
			}
		}
		
	}
	
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentSampleIndex = $tempValue['SampleIndex'];
		$results['Data'][$currentSampleIndex]['SampleID'] 		= $samples[$currentSampleIndex];
		$results['Data'][$currentSampleIndex]['Original'] 		= trim($tempValue[$valueColumn]);
		$results['Data'][$currentSampleIndex]['Raw'] 			= trim($tempValue[$valueColumn]);
		$results['Data'][$currentSampleIndex]['Transformed'] 	= log2(floatval($tempValue[$valueColumn]) + $APP_CONFIG['canvasxpress']['Log_Add_Value']);
		
		$results['Raw'][] 	= $results['Data'][$currentSampleIndex]['Raw'];
		$results['JS'][]	= round($results['Data'][$currentSampleIndex]['Transformed'], 2);
		

	}

	$results['Name'] 		= $geneName;
	$results['Type'] 		= 'Gene';

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
	
}

function preparePairwiseData_Category_vs_Category($sampleIndexes, $sampleData, $sampleAttributeX, $sampleAttributeY){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	$version = '2018-04-14 21:57';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										json_encode($sampleData) . '::' . 
										json_encode($sampleAttributeX) . '::' . 
										json_encode($sampleAttributeY) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	unset($index);
	foreach($sampleIndexes as $tempKey => $currentSampleIndex){
		
		$results['Sample_Count']++;
		
		$results['Sample_Index'][] 	= $currentSampleIndex;
		
		$results['SampleID'][]		= $sampleData[$currentSampleIndex]['SampleID'];
		
		$currentSampleInfo = $sampleData[$currentSampleIndex];
		
		$valueX = ucwords2($currentSampleInfo[$sampleAttributeX]);
		$valueY = ucwords2($currentSampleInfo[$sampleAttributeY]);
		
		
		$results['Export']['Sample']['Body'][$currentSampleIndex]['SampleID'] = $sampleData[$currentSampleIndex]['SampleID'];
		
		if (($valueX == '') || ($valueX == 'NA')){
			$valueX = $APP_CONFIG['Blank_Value'];	
		}
		
		if (($valueY == '') || ($valueY == 'NA')){
			$valueY = $APP_CONFIG['Blank_Value'];	
		}
		
		$results['Export']['Sample']['Body'][$currentSampleIndex]['x'] = $valueX;
		$results['Export']['Sample']['Body'][$currentSampleIndex]['y'] = $valueY;
		
		
		$valuesX[] = $valueX;
		$valuesY[] = $valueY;
		$results['Histogram'][$valueY][$valueX]['SampleIndex'][$currentSampleIndex] = $currentSampleIndex;
		$results['Histogram'][$valueY][$valueX]['Count']++;
		
		$results['RowCount'][$valueY]++;
		
		if (!isset($min)){
			$min = $results['Histogram'][$valueY][$valueX]['Count'];
		}
		
		if (!isset($max)){
			$max = $results['Histogram'][$valueY][$valueX]['Count'];
		}
		
		if ($results['Histogram'][$valueY][$valueX]['Count'] < $min){
			$min = $results['Histogram'][$valueY][$valueX]['Count'];
		}
		
		if ($results['Histogram'][$valueY][$valueX]['Count'] > $max){
			$max = $results['Histogram'][$valueY][$valueX]['Count'];
		}
		
		$results['Candidate_Count']["{$valueY}_{$valueX}"] = 1;
	}

	$results['Sample_Count_Formatted'] = number_format($results['Sample_Count']);
	
	natcasesort($valuesX);
	$valuesX = array_clean($valuesX);
	$valuesX = array_combine($valuesX, $valuesX);
	
	if (isset($valuesX[$APP_CONFIG['Blank_Value']])){
		unset($valuesX[$APP_CONFIG['Blank_Value']]);
		$valuesX[] = $APP_CONFIG['Blank_Value'];
	}
	$results['valuesX'] = $valuesX;
	$results['Summary']['x_count'] = array_size($valuesX);
	
	
	natcasesort($valuesY);
	$valuesY = array_clean($valuesY);
	$valuesY = array_combine($valuesY, $valuesY);
	if (isset($valuesY[$APP_CONFIG['Blank_Value']])){
		unset($valuesY[$APP_CONFIG['Blank_Value']]);
		$valuesY[] = $APP_CONFIG['Blank_Value'];
	}
	$results['valuesY'] = $valuesY;
	$results['Summary']['y_count'] = array_size($valuesY);
	
	
	if (array_size($results['Candidate_Count']) < $results['Summary']['y_count'] * $results['Summary']['x_count']){
		$min = 0;	
	}
	
	$results['Summary']['min'] = $min;
	$results['Summary']['max'] = $max;
	
	$results['Summary']['Title'] = "{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttributeY]['Title']} vs {$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttributeX]['Title']}";
	
	$results['Export']['Table']['Headers']['Initial'] 
		= "{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttributeY]['Title']} / 			{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttributeX]['Title']}";
		
	$results['Export']['Table']['Headers']['Total'] = 'Total # of Samples';
	
	foreach($valuesX as $tempKey => $tempValue){
		$results['Export']['Table']['Headers'][$tempValue] = $tempValue;
	}
	
	
	foreach($valuesY as $tempKeyY => $tempValueY){
		
		$results['Export']['Table']['Body'][$tempValueY]['Initial'] = $tempValueY;
		
		$results['Export']['Table']['Body'][$tempValueY]['Total'] = $results['RowCount'][$tempValueY];
		
		foreach($valuesX as $tempKeyX => $tempValueX){
			
			$currentValue		= array_size($results['Histogram'][$tempValueY][$tempValueX]['SampleIndex']);
			
			$results['Export']['Table']['Body'][$tempValueY][$tempValueX] = $currentValue;
		}
		
	}

	$results['Export']['Sample']['Headers']['SampleID'] = $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];	
	$results['Export']['Sample']['Headers']['x'] 		= $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttributeX]['Title'];	
	$results['Export']['Sample']['Headers']['y'] 		= $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttributeY]['Title'];
	
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
}

function preparePairwiseData_Numeric_vs_Numeric($sampleIndexes, $numericDataX, $numericDataY, $colorData, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	
	$version = '2018-04-14 21:40';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) . '::' . 
										json_encode($numericDataX) 	. '::' . 
										json_encode($numericDataY) 	. '::' . 
										json_encode($colorData) 	. '::' . 
										json_encode($dataSource) 	. '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$x_name = $numericDataX['Name'];
	$y_name = $numericDataY['Name'];
	
	foreach($sampleIndexes as $tempKey => $currentSampleIndex){
		
		unset($isNumericX, $isNumericY);
		
		if (isset($numericDataX['Data'][$currentSampleIndex]['Raw'])){
			if (is_numeric($numericDataX['Data'][$currentSampleIndex]['Raw'])){
				$isNumericX = true;	
			}
		}
		
		if (isset($numericDataY['Data'][$currentSampleIndex]['Raw'])){
			if (is_numeric($numericDataY['Data'][$currentSampleIndex]['Raw'])){
				$isNumericY = true;	
			}
		}
		
		
		if ($isNumericX && $isNumericY){
			
			$results['Sample_Count']++;
		
			$results['Sample_Index'][] 	= $currentSampleIndex;
		
			$sampleID					= $numericDataX['Data'][$currentSampleIndex]['SampleID'];
			
			if ($sampleID == ''){
				$sampleID				= $numericDataY['Data'][$currentSampleIndex]['SampleID'];
			}
			
			$results['SampleID'][]		= $sampleID;
			
			if ($colorData['Type'] == 'Sample'){
				$results['canvasxpress']['z']['Category'][] = $colorData['JS'][$currentSampleIndex];
			}
			
			//Dot Size
			$results['canvasxpress']['sizes'][] = 10;
			
			$results['canvasxpress']['y']['vars'][] 	= '"' . sanitizeJavaScriptValue($sampleID) . '"';	
			
			
			$dataX 		= $numericDataX['Data'][$currentSampleIndex]['Raw'];
			$dataY 		= $numericDataY['Data'][$currentSampleIndex]['Raw'];
			

			$dataX_log2 = $numericDataX['Data'][$currentSampleIndex]['Transformed'];
			$dataY_log2 = $numericDataY['Data'][$currentSampleIndex]['Transformed'];
			
			$results['canvasxpress']['y']['data'][]		= '[' . $dataX_log2 . ',' . $dataY_log2 . ']';
			
			
			$results['Export']['Body'][$currentSampleIndex]['SampleID'] 			= $sampleID;
			
			$results['Export']['Body'][$currentSampleIndex]["{$x_name}_Original"] 	= $numericDataX['Data'][$currentSampleIndex]['Original'];
			$results['Export']['Body'][$currentSampleIndex]["{$x_name}_Plot"] 		= $numericDataX['Data'][$currentSampleIndex]['Transformed'];
			
			$results['Export']['Body'][$currentSampleIndex]["{$y_name}_Original"] 	= $numericDataY['Data'][$currentSampleIndex]['Original'];
			$results['Export']['Body'][$currentSampleIndex]["{$y_name}_Plot"] 		= $numericDataY['Data'][$currentSampleIndex]['Transformed'];
			
			if ($colorData['Type'] == 'Sample'){
				$results['Export']['Body'][$currentSampleIndex]["Color_By"] 		= $colorData['Raw'][$currentSampleIndex];
			} elseif ($colorData['Type'] == 'Gene'){
				$results['Export']['Body'][$currentSampleIndex]["Color_By"] 		= $colorData['Data'][$currentSampleIndex]['Transformed'];
			}
		}
	}
	
	if ($results['Sample_Count'] > 0){
		
		$results['Sample_Count_Formatted'] = number_format($results['Sample_Count']);
		
		if ($colorData['Type'] == 'Gene'){
			$results['canvasxpress']['z']['Category'] = $colorData['JS'];
		}
		
		
		$results['canvasxpress']['Category'] = $colorData['Name'];
		
		$results['canvasxpress']['y']['smps']['x']	= "'{$numericDataX['Axis_Print']}'";
		$results['canvasxpress']['y']['smps']['y'] 	= "'{$numericDataY['Axis_Print']}'";
		
		$results['canvasxpress']['xAxisTitle']		= $numericDataX['Axis_Print'];
		$results['canvasxpress']['yAxisTitle']		= $numericDataY['Axis_Print'];

		$results['Export']['Headers']['SampleID'] 				= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
		$results['Export']['Headers']["{$x_name}_Original"] 	= "{$x_name} (Original)";
		$results['Export']['Headers']["{$x_name}_Plot"] 		= "{$x_name} (Plot)";
		
		$results['Export']['Headers']["{$y_name}_Original"] 	= "{$y_name} (Original)";
		$results['Export']['Headers']["{$y_name}_Plot"] 		= "{$y_name} (Plot)";
		
		if ($colorData['Type'] != ''){
			$results['Export']['Headers']["Color_By"] 			= "Color By: {$colorData['Name']}";
		}
		
		
		$results['canvasxpress']['title'] = '';
		
		if ($numericDataX['Type'] == $numericDataY['Type']){
			if ($numericDataX['Type'] == 'Gene'){
				$results['canvasxpress']['title'] = "Gene Expression Levels of {$numericDataY['Name']} vs {$numericDataX['Name']}";
			} else {
				$results['canvasxpress']['title'] = "{$numericDataY['Name']} vs {$numericDataX['Name']}";
			}
		} else {
			
			if ($numericDataY['Type'] == 'Gene'){
				$results['canvasxpress']['title'] = "Gene Expression Levels of {$numericDataY['Name']} vs {$numericDataX['Name']}";
			} else {
				$results['canvasxpress']['title'] = "{$numericDataX['Name']} vs Gene Expression Levels of {$numericDataY['Name']}";
			}
		}
		
	}
	


	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
}

function preparePairwiseData_Numeric_vs_SampleID($sampleIndexes, $numericData, $sampleData, $colorData, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG, $ORDER_ARRAY;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = '2018-04-15 03:31';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) 	. '::' . 
										json_encode($numericData) 		. '::' . 
										json_encode($sampleData) 		. '::' . 
										json_encode($colorData) 		. '::' . 
										json_encode($dataSource)		. '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	foreach($sampleIndexes as $tempKey => $currentSampleIndex){
		
		$results['Sample_Count']++;
		
		if ($colorData['Type'] == 'Sample'){
			
			$colorCategory = $colorData['Raw'][$currentSampleIndex];
			
			if ($colorCategory	== ''){
				$colorCategory = $APP_CONFIG['Blank_Value'];
			}
			
			$results['SampleID'][] = $sampleData[$currentSampleIndex]['SampleID'];
			
			
			$results['data'][$colorCategory][$currentSampleIndex]['Value'] 		= $numericData['Data'][$currentSampleIndex]['Transformed'];
			$results['data'][$colorCategory][$currentSampleIndex]['SampleID']	= $sampleData[$currentSampleIndex]['SampleID'];
			
		}
	}

	if ($results['Sample_Count'] > 0){
		$results['Sample_Count_Formatted'] = number_format($results['Sample_Count']);
		
		natksort($results['data']);
	
		if (isset($results['data'][($APP_CONFIG['Blank_Value'])])){
			$temp = $results['data'][($APP_CONFIG['Blank_Value'])];
			
			unset($results['data'][($APP_CONFIG['Blank_Value'])]);
			
			$results['data'][($APP_CONFIG['Blank_Value'])] = $temp;
		}
	
	
	
		$results['canvasxpress']['smpTitle'] 		= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
	
	
		if ($colorData['Type'] == 'Sample'){
			$results['canvasxpress']['colorBy']		= $colorData['Name'];
		}
		
		$results['Export']['Headers']['SampleID'] 	= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
		$results['Export']['Headers']["x_name"] 	= $numericData['Name'];
		$results['Export']['Headers']["Color"] 		= $colorData['Name'];
		
		
		$ORDER_ARRAY = array('Value' => 'DESC', 'SampleID' => 'ASC');
		foreach($results['data'] as $colorCategory => $tempValue){
			naturalSort2DArray($results['data'][$colorCategory]);
			
		
			foreach($results['data'][$colorCategory] as $tempKeyX => $tempValueX){
				$results['canvasxpress']['x']['Category'][] = '"' . sanitizeJavaScriptValue($colorCategory) . '"';	
				
				$results['canvasxpress']['y']['smps'][]		= '"' . sanitizeJavaScriptValue($tempValueX['SampleID']) . '"';
				
				$results['canvasxpress']['y']['data'][]		= $tempValueX['Value'];
				
				
				$results['Export']['Body'][$tempValueX['SampleID']]["SampleID"] = $tempValueX['SampleID'];
				$results['Export']['Body'][$tempValueX['SampleID']]["x_name"] 	= $tempValueX['Value'];
				$results['Export']['Body'][$tempValueX['SampleID']]["Color"] 	= $colorCategory;
				
				
				
			}
		}
		
		
		$results['canvasxpress']['xAxisTitle'] = $numericData['Axis_Print'];
	}
	
	
	

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
}

function preparePairwiseData_Numeric_vs_Category($sampleIndexes, $numericData, $sampleData, $sampleAttribute, $colorData, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG, $ORDER_ARRAY;

	$sampleIndexes 	= array_clean($sampleIndexes);	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = '2018-04-18 16:40';
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($sampleIndexes) 	. '::' . 
										json_encode($numericData) 		. '::' . 
										json_encode($sampleData) 		. '::' .
										$sampleAttribute				. '::' .
										json_encode($colorData) 		. '::' . 
										json_encode($dataSource)		. '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	foreach($numericData['Data'] as $currentSampleIndex => $numericDataInfo){
		
		if (!is_numeric($numericDataInfo['Transformed'])) continue;
		
		$results['Sample_Count']++;
		
		$results['canvasxpress']['x']['Category'][] 	= '"' . sanitizeJavaScriptValue(ucwords2($sampleData[$currentSampleIndex][$sampleAttribute])) . '"';	
		
		
		if ($colorData['Type'] == 'Sample'){
			$results['canvasxpress']['x']['Color'][] 	= $colorData['JS'][$currentSampleIndex];
		}
		
		$results['canvasxpress']['y']['smps'][] 	= '"' . sanitizeJavaScriptValue(ucwords2($sampleData[$currentSampleIndex]['SampleID'])) . '"';	
		$results['canvasxpress']['y']['data'][] 	= $numericDataInfo['Transformed'];
		
		$results['SampleID'][]						= $sampleData[$currentSampleIndex]['SampleID'];
		$results['Export']['Body'][$currentSampleIndex]['SampleID'] 						= $sampleData[$currentSampleIndex]['SampleID'];
		$results['Export']['Body'][$currentSampleIndex]["{$numericData['Name']}_Original"] 	= $numericDataInfo['Original'];
		$results['Export']['Body'][$currentSampleIndex]["{$numericData['Name']}_Plot"] 		= $numericDataInfo['Transformed'];
		
		
		if ($colorData['Type'] == 'Sample'){
			$results['Export']['Body'][$currentSampleIndex]["Color_By"] 		= $colorData['Raw'][$currentSampleIndex];
		} elseif ($colorData['Type'] == 'Gene'){
			$results['Export']['Body'][$currentSampleIndex]["Color_By"] 		= $colorData['Data'][$currentSampleIndex]['Transformed'];
		}
		
		

	}

	if ($results['Sample_Count'] > 0){
		$results['Sample_Count_Formatted'] = number_format($results['Sample_Count']);
		$results['canvasxpress']['y']['vars'] = $numericData['Name'];
		$results['canvasxpress']['x']['Category_Name'] = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$sampleAttribute]['Title'];
		
		$results['Export']['Headers']['SampleID'] 							= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
		$results['Export']['Headers']["{$numericData['Name']}_Original"] 	= "{$numericData['Name']} (Original)";
		$results['Export']['Headers']["{$numericData['Name']}_Plot"] 		= "{$numericData['Name']} (Plot)";
		
		
		if ($colorData['Type'] == 'Sample'){
			$results['canvasxpress']['x']['Color_Name'] 	= $colorData['Name'];
			$results['Export']['Headers']["Color_By"] 		= "Color By: {$colorData['Name']}";
		} elseif ($colorData['Type'] == 'Gene'){
			$results['canvasxpress']['x']['Color']			= $colorData['JS'];
			$results['canvasxpress']['x']['Color_Name'] 	= $colorData['Name'];	
			$results['Export']['Headers']["Color_By"] 		= "Color By: {$colorData['Name']}";
		}
		
	}
	
	
	

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
}


?>