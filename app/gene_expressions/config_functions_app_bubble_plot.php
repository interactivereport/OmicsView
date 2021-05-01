<?php



function prepareSingleBubblePlotHistogram($geneIndex = -1, $otherOptions = array()){
	
	global $APP_CONFIG;
	
	$geneIndex = intval($geneIndex);
	
	if ($geneIndex < 0) return false;
	
	$version = '2021-01-27 23:43';
	
	$cacheKey = __FUNCTION__ . '::' . md5($geneIndex . '::' . 
											json_encode($otherOptions) . '::' . 
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$results['Summary']['Options'] = $otherOptions;

	
	//Getting $allComparisonData
	if (true){
		
		cleanInternalDataInput($otherOptions['data_source'], $otherOptions['data_source_private_project_indexes']);
		$internalProjectIndexString = implode(',', $otherOptions['data_source_private_project_indexes']);
	
		unset($allComparisonData);
		if ($otherOptions['data_source']['public'] != ''){
			$allComparisonData = tabix_search_records_with_index(array($geneIndex), '', 'ComparisonData', 'GetArrayAssoc');
			
		}
	
		if ($otherOptions['data_source']['private'] != ''){
			foreach($otherOptions['data_source_private_project_indexes'] as $tempKey => $projectIndex){
				$data_comparison_private = tabix_search_records_with_index_internal_data($projectIndex, array($geneIndex), '', 'ComparisonData', 'GetArrayAssoc');
				
				
				foreach($data_comparison_private as $tempKeyX => $tempValueX){
					$allComparisonData[] = $tempValueX;
				}
			}
		}
		
		if (array_size($allComparisonData) <= 0){
			return false;	
		}

		$allComparisonIndexes 	= array_unique(array_column($allComparisonData, 'ComparisonIndex'));
		if (array_size($allComparisonIndexes) <= 0){
			return false;	
		}
		
		$comparisonColumns = "`ComparisonIndex`, `{$otherOptions['Column']}`";
		
		
		$allComparisons = search_comparisons_by_index($allComparisonIndexes, $comparisonColumns, $otherOptions['data_source'], $otherOptions['data_source_private_project_indexes']);
		
		if (array_size($allComparisons) <= 0){
			return false;	
		}
		
		$results['Summary']['Data_Count']['Original'] = array_size($allComparisonData);
		
	}
	
	$valueColumn = $otherOptions['marker'];
			
	//Prepare histogram, remove unnecessary comparison data
	if (true){
		foreach($allComparisonData as $tempKey => $comparisonData){
			
			$comparisonIndex 	= $comparisonData['ComparisonIndex'];
			$comparisonMeta		= trim($allComparisons[$comparisonIndex]);
			$comparisonValue	= $comparisonData[$valueColumn];

			if (!is_numeric($comparisonValue)){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if (true){
				if (($comparisonMeta == '') || ($comparisonMeta == '.') || (strtolower($comparisonMeta) == 'na')){
					unset($allComparisonData[$tempKey]);
					continue;	
				}
			}
			
			if (!is_numeric($comparisonData['Log2FoldChange'])){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if ($comparisonData['Log2FoldChange'] == '.'){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if ($comparisonData['Log2FoldChange'] == ''){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			$results['histogram'][$comparisonMeta]++;
		}
		
		if (array_size($allComparisonData) <= 0){
			return false;
			exit();	
		}
		
		
		$results['Summary']['Data_Count']['All'] = array_size($allComparisonData);
		
		arsort($results['histogram']);

	}
	
	$results['Summary']['Title'] 	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][($otherOptions['Column'])]['Title'];
	
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
}

function prepareSingleBubblePlotDataByGeneName($geneIndex = -1, $otherOptions = array()){
	
	global $APP_CONFIG;
	
	$geneIndex = intval($geneIndex);
	
	if ($geneIndex < 0) return false;
	
	$version = '2021-01-27 23:43';
	
	$cacheKey = __FUNCTION__ . '::' . md5($geneIndex . '::' . 
											json_encode($otherOptions) . '::' . 
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$results['Summary']['Options'] = $otherOptions;

	
	//Getting $allComparisonData
	if (true){
		
		cleanInternalDataInput($otherOptions['data_source'], $otherOptions['data_source_private_project_indexes']);
		$internalProjectIndexString = implode(',', $otherOptions['data_source_private_project_indexes']);
	
		unset($allComparisonData);
		if ($otherOptions['data_source']['public'] != ''){
			$allComparisonData = tabix_search_records_with_index(array($geneIndex), '', 'ComparisonData', 'GetArrayAssoc');
			
		}
	
		if ($otherOptions['data_source']['private'] != ''){
			foreach($otherOptions['data_source_private_project_indexes'] as $tempKey => $projectIndex){
				$data_comparison_private = tabix_search_records_with_index_internal_data($projectIndex, array($geneIndex), '', 'ComparisonData', 'GetArrayAssoc');
				
				
				foreach($data_comparison_private as $tempKeyX => $tempValueX){
					$allComparisonData[] = $tempValueX;
				}
			}
		}
		
		if (array_size($allComparisonData) <= 0){
			return false;	
		}

		$allComparisonIndexes 	= array_unique(array_column($allComparisonData, 'ComparisonIndex'));
		if (array_size($allComparisonIndexes) <= 0){
			return false;	
		}
		
		$comparisonColumns = array();
		$comparisonColumns[] = 'ComparisonIndex';
		$comparisonColumns[] = 'ComparisonID';
		$comparisonColumns[] = 'ComparisonCategory';
		$comparisonColumns[] = 'ComparisonContrast';
		$comparisonColumns[] = 'ProjectName';
		$comparisonColumns[] = $otherOptions['y-axis'];
		$comparisonColumns[] = $otherOptions['colorBy'];
		$comparisonColumns[] = $otherOptions['subplotBy'];
		$comparisonColumns[] = $otherOptions['shapeBy'];
		
		$comparisonColumns = array_clean($comparisonColumns);
		$comparisonColumns = "`" . implode('`,`', $comparisonColumns) . "`";
		
		
		$allComparisons = search_comparisons_by_index($allComparisonIndexes, $comparisonColumns, $otherOptions['data_source'], $otherOptions['data_source_private_project_indexes']);
		
		if (array_size($allComparisons) <= 0){
			return false;	
		}
		
		$results['Summary']['Data_Count']['Original'] = array_size($allComparisonData);
		
	}
	
	
	$yAxisColumn 		= $otherOptions['y-axis'];
	$colorByColumn 		= $otherOptions['colorBy'];
	$subplotByColumn 	= $otherOptions['subplotBy'];
	
	
			
	//Prepare histogram, remove unnecessary comparison data
	if (true){
		foreach($allComparisonData as $tempKey => $comparisonData){
			
			$comparisonIndex = $comparisonData['ComparisonIndex'];
			
			$comparison = $allComparisons[$comparisonIndex];
			
			$valueColumn = $otherOptions['marker'];
			
			
			if (!is_numeric($comparisonData[$valueColumn])){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if (true){
				$comparison[$yAxisColumn] = trim($comparison[$yAxisColumn]);
				if (($comparison[$yAxisColumn] == '') || ($comparison[$yAxisColumn] == '.') || (strtolower($comparison[$yAxisColumn]) == 'na')){
					
					if ($otherOptions['keep_blank']){
						$allComparisons[$comparisonIndex][$yAxisColumn] = $APP_CONFIG['Blank_Value'];
					} else {
						unset($allComparisonData[$tempKey]);
						continue;
					}
				}
			}
			
			if (true){
				$comparison[$colorByColumn] = trim($comparison[$colorByColumn]);
				if (($comparison[$colorByColumn] == '') || ($comparison[$colorByColumn] == '.') || (strtolower($comparison[$colorByColumn]) == 'na')){
					
					if ($otherOptions['keep_blank']){
						$allComparisons[$comparisonIndex][$colorByColumn] = $APP_CONFIG['Blank_Value'];
					} else {
						unset($allComparisonData[$tempKey]);
						continue;
					}
				}
			}
			
			if (!is_numeric($comparisonData['Log2FoldChange'])){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if ($comparisonData['Log2FoldChange'] == '.'){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if ($comparisonData['Log2FoldChange'] == ''){
				unset($allComparisonData[$tempKey]);
				continue;
			}
			
			if ($otherOptions['y-axis_settings'] > 0){		
				if (strtolower($comparison[$yAxisColumn]) == 'normal control'){
					unset($allComparisonData[$tempKey]);
					continue;
				}
			} elseif (($otherOptions['y-axis_settings'] == -1) && (array_size($otherOptions['y-axis_customize']) > 0)){
				if (!in_array($comparison[$yAxisColumn], $otherOptions['y-axis_customize'])){
					unset($allComparisonData[$tempKey]);
					continue;
				}
			}
	
			if ($otherOptions['colorBy_settings'] > 0){		
				if (strtolower($comparison[$colorByColumn]) == 'normal control'){
					unset($allComparisonData[$tempKey]);
					continue;
				}
			} elseif (($otherOptions['colorBy_settings'] == -1) && (array_size($otherOptions['colorBy_customize']) > 0)){
				if (!in_array($comparison[$colorByColumn], $otherOptions['colorBy_customize'])){
					unset($allComparisonData[$tempKey]);
					continue;
				}
			}
			
			
			if (($subplotByColumn != '') && ($otherOptions['subplotBy_settings'] == -1) && (array_size($otherOptions['subplotBy_customize']) > 0)){
				if (!in_array($comparison[$subplotByColumn], $otherOptions['subplotBy_customize'])){
					unset($allComparisonData[$tempKey]);
					continue;
				}
			}
			
			
			
			$allComparisonData[$tempKey][$yAxisColumn] = $comparison[$yAxisColumn];
			
			$results['histogram']['y-axis'][($comparison[$yAxisColumn])]++;
			$results['histogram']['colorBy'][($comparison[$colorByColumn])]++;
		}
		
		if (array_size($allComparisonData) <= 0){
			return false;
			exit();	
		}
		
		if (true){
			global $ORDER_ARRAY;
			$ORDER_ARRAY = array($otherOptions['y-axis'] => 'ASC');
			naturalSort2DArray($allComparisonData);
		}
		
		$results['Summary']['Data_Count']['All'] = array_size($allComparisonData);
		
		arsort($results['histogram']['y-axis']);
		arsort($results['histogram']['colorBy']);
		
		if ($otherOptions['y-axis_settings'] > 0){
			$results['histogram']['y-axis'] = array_slice($results['histogram']['y-axis'], 0, $otherOptions['y-axis_settings']);	
		}
		
		if ($otherOptions['colorBy_settings'] > 0){
			$results['histogram']['colorBy'] = array_slice($results['histogram']['colorBy'], 0, $otherOptions['colorBy_settings']);	
		}
	}
	
	
	if (true){
		$allProjectNames = array_clean(array_column($allComparisons, 'ProjectName'));
		$allProjectNames = "'" . implode("','", $allProjectNames) . "'";
		
		$SQL = "SELECT `ProjectID`, `ProjectIndex`, `Disease`, `StudyType`, `TherapeuticArea`, `Title`, `PubMed` FROM `{$APP_CONFIG['Table']['App_User_Data_Projects_Combined']}` WHERE `ProjectID` IN ({$allProjectNames})";
		$allProjectRecords = getSQL($SQL, 'GetAssoc', $APP_CONFIG['Table']['App_User_Data_Projects_Combined']);
	}
	
	

	if ($otherOptions['y-axis'] != ''){
		$results['Summary']['y-axis'] 			= $otherOptions['y-axis'];
		$results['Summary']['y-axis_Title'] 	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][($otherOptions['y-axis'])]['Title'];
	}
	
	if ($otherOptions['colorBy'] != ''){
		$results['Summary']['colorBy'] 			= $otherOptions['colorBy'];
		$results['Summary']['colorBy_Title'] 	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][($otherOptions['colorBy'])]['Title'];
	}
	
	if ($otherOptions['shapeBy'] != ''){
		$results['Summary']['shapeBy'] 			= $otherOptions['shapeBy'];
		$results['Summary']['shapeBy_Title'] 	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][($otherOptions['shapeBy'])]['Title'];
	}
	
	if ($otherOptions['subplotBy'] != ''){
		$results['Summary']['subplotBy'] 		= $otherOptions['subplotBy'];
		$results['Summary']['subplotBy_Title'] 	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][($otherOptions['subplotBy'])]['Title'];
	}
	
	$results['Summary']['Chart']['range']['min'] = -0.5;
	$results['Summary']['Chart']['range']['max'] = 0.5 + array_size($results['histogram']['y-axis']);	
	
	
	$results['ComparisonData'] 				= &$allComparisonData;
	$results['ProjectRecords'] 				= &$allProjectRecords;
	$results['ComparisonRecords'] 			= &$allComparisons;

	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
}


function prepareSingleBubblePlotDataByGeneName_Plotly_Single_v2($geneIndex = -1, $otherOptions = array()){
	
	
	global $APP_CONFIG;
	
	$geneIndex = intval($geneIndex);
	
	if ($geneIndex < 0) return false;
	
	$version = '2021-01-27 23:43';
	
	$cacheKey = __FUNCTION__ . '::' . md5($geneIndex . '::' . 
											json_encode($otherOptions) . '::' . 
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$results = prepareSingleBubblePlotDataByGeneName($geneIndex, $otherOptions);
	

	
	if ($results == false){
		return false;
	}
	
	$allComparisonData	= $results['ComparisonData'];
	$allProjectRecords 	= $results['ProjectRecords'];
	$allComparisons		= $results['ComparisonRecords'];
	
	unset($results['ComparisonData']);
	unset($results['ProjectRecords']);
	unset($results['ComparisonRecords']);
	
	
	foreach($allComparisonData as $tempKey => $comparisonData){

		$comparisonIndex 	= $comparisonData['ComparisonIndex'];
		$comparison 		= $allComparisons[$comparisonIndex];
		$valueColumn 		= $otherOptions['marker'];
		$projectID			= $comparison['ProjectName'];
		$project			= $allProjectRecords[$projectID];

		if (true){		
			$yAxisColumn	= $otherOptions['y-axis'];
			$currentYAxis	= $comparison[$yAxisColumn];
			
			if ($currentYAxis == ''){
				$currentYAxis = $APP_CONFIG['Blank_Value'];	
			}
		}
		
		if (true){
			$colorByColumn 	= $otherOptions['colorBy'];
			$currentColorBy	= $comparison[$colorByColumn];
			
			if ($currentColorBy == ''){
				$currentColorBy = $APP_CONFIG['Blank_Value'];	
			}
		}
		
		if (true){
			$markerColumn 	= $otherOptions['marker'];
			$currentMarker	= $comparisonData[$markerColumn];
		}
		
		if ($otherOptions['shapeBy'] != ''){
			$shapeByColumn	= $otherOptions['shapeBy'];
			$currentShapeBy	= $comparison[$shapeByColumn];
			
			if ($currentShapeBy == ''){
				$currentShapeBy = $APP_CONFIG['Blank_Value'];	
			}
		}
		
		if (!isset($results['histogram']['y-axis'][$currentYAxis])){
			unset($allComparisonData[$tempKey]);
			continue;
		}
		
		if (!isset($results['histogram']['colorBy'][$currentColorBy])){
			unset($allComparisonData[$tempKey]);
			continue;
		}
		

		if ($otherOptions['y-axis_settings'] > 0){		
			if (strtolower($comparison[$yAxisColumn]) == 'normal control'){
				unset($allComparisonData[$tempKey]);
				continue;
			}
		}

		if ($otherOptions['colorBy_settings'] > 0){		
			if (strtolower($comparison[$colorByColumn]) == 'normal control'){
				unset($allComparisonData[$tempKey]);
				continue;
			}
		}
		
		if (0){
			$random = rand(1, 120);
			if ($random > 2){
				unset($allComparisonData[$tempKey]);
				continue;
			}
		}
		
		if (!isset($results['Summary']['Log2FoldChange']['min'])){
			$results['Summary']['Log2FoldChange']['min'] = $comparisonData['Log2FoldChange'];
		}
		
		if (!isset($results['Summary']['Log2FoldChange']['max'])){
			$results['Summary']['Log2FoldChange']['max'] = $comparisonData['Log2FoldChange'];
		}
		
		if ($comparisonData['Log2FoldChange'] > $results['Summary']['Log2FoldChange']['max']){
			$results['Summary']['Log2FoldChange']['max'] = $comparisonData['Log2FoldChange'];
		}
		
		if ($comparisonData['Log2FoldChange'] < $results['Summary']['Log2FoldChange']['min']){
			$results['Summary']['Log2FoldChange']['min'] = $comparisonData['Log2FoldChange'];
		}
		

		$traceID = "Trace";
		
		
		if (!isset($results['Chart'][$traceID]['name'])){
			$results['Chart'][$traceID]['name'] = "My Trace";
		}
		
		$results['Chart'][$traceID]['x'][] = $comparisonData['Log2FoldChange'];
		$results['Chart'][$traceID]['y'][] = '"' . sanitizeJavaScriptValue(ucwords2($currentYAxis)) . '"';
		
		if (!isset($results['Chart'][$traceID]['yaxis-index'])){
			$results['Chart'][$traceID]['yaxis-index'][$currentYAxis] = 1;
		}
		
		$temp = array();
		$temp[] = "{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title']}: " . ucwords2($comparison['ComparisonID']);
		$temp[] = "{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title']}: " . ucwords2($comparison['ComparisonCategory']);
		$temp[] = "{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonContrast']['Title']}: " . ucwords2($comparison['ComparisonContrast']);
		$temp[] = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$yAxisColumn]['Title'] . ": " . ucwords2($currentYAxis);
		$temp[] = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$colorByColumn]['Title'] . ": " . ucwords2($currentColorBy);
		$temp[] = "p-value: {$comparisonData['PValue']}";
		$temp[] = "Adjusted P-value: {$comparisonData['AdjustedPValue']}";
		$temp[] = "log2(Fold Change): {$comparisonData['Log2FoldChange']}";
		$temp[] = "";
		$temp[] = "Double click to review the comparison details";
		$results['Chart'][$traceID]['text'][] = '"' . sanitizeJavaScriptValue(implode('<br />', $temp)) . '"';
		$results['Chart'][$traceID]['ComparisonIndex'][] = $comparisonIndex;
		
		if ($colorByColumn != ''){
			$currentColor = getRandomRGBColor($currentColorBy, $globalColorIndex, 0);
			
			if (!isset($results['Chart-Legend']['Color'][$currentColorBy])){
				$legendIndex++;
				$results['Chart-Legend']['Color'][$currentColorBy]['name'] 	= sanitizeJavaScriptValue(ucwords2($currentColorBy));
				$results['Chart-Legend']['Color'][$currentColorBy]['color'] = '#' . implode('', $currentColor);
				$results['Chart-Legend']['Color'][$currentColorBy]['xaxis']	= "xLC{$legendIndex}";
				$results['Chart-Legend']['Color'][$currentColorBy]['yaxis'] = "yLC{$legendIndex}";
				$results['Chart-Legend']['Color'][$currentColorBy]['ID']	= "legend_c_{$legendIndex}";
				$results['Chart-Legend']['Color'][$currentColorBy]['Type']	= 'Color';
			}

		} else {
			$currentColor = array(0, 0, 0);
		}
		$results['Chart'][$traceID]['color'][] = '"#' . implode('', $currentColor) . '"';
		
		
		
		if ($shapeByColumn != ''){
			
			$currentShape = plotly_get_marker_shapes_from_value($shapeByColumn, $currentShapeBy);
			
			if (!isset($results['Chart-Legend']['Shape'][$currentShapeBy])){
				$legendIndex++;
				$results['Chart-Legend']['Shape'][$currentShapeBy]['name'] 		= sanitizeJavaScriptValue(ucwords2($currentShapeBy));
				$results['Chart-Legend']['Shape'][$currentShapeBy]['color'] 	= '#000';
				$results['Chart-Legend']['Shape'][$currentShapeBy]['symbol'] 	= $currentShape;
				$results['Chart-Legend']['Shape'][$currentShapeBy]['xaxis']		= "xLS{$legendIndex}";
				$results['Chart-Legend']['Shape'][$currentShapeBy]['yaxis'] 	= "yLS{$legendIndex}";
				$results['Chart-Legend']['Shape'][$currentShapeBy]['ID']		= "legend_s_{$legendIndex}";
				$results['Chart-Legend']['Shape'][$currentShapeBy]['Type']		= 'Shape';
			}

		} else {
			$currentShape = 'circle';
		}
		$results['Chart'][$traceID]['symbol'][] = '"' . $currentShape . '"';
		
		
		
		
		
		if ($markerColumn != ''){
			if ((-1000) * log10($currentMarker) < 5000 && (-1000) * log10($currentMarker) > 100) {
				$marker_size = floatval((-1000) * log10($currentMarker));
			} else if ((-1000) * log10($currentMarker) > 5000) {
				$marker_size = 5000;
			} else {
				$marker_size = 100;
			}
			
			
			if (!isset($results['Summary']['Marker'])){
				if ($otherOptions['marker'] == 'AdjustedPValue'){
					$results['Summary']['Marker'] = '-log10(FDR)';
				} elseif ($otherOptions['marker'] == 'PValue'){
					$results['Summary']['Marker'] = '-log10(p-value)';
				}
			}
			
			$results['Chart'][$traceID]['size'][] = intval($marker_size);
		}

		if (!isset($results['Export']['Raw']['Headers'])){
			$results['Export']['Raw']['Headers']['ComparisonID']		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title'];
			
			$results['Export']['Raw']['Headers']['Log2FoldChange']		= "Log2(Fold Change)";
			$results['Export']['Raw']['Headers']['PValue']				= "P-Value";
			$results['Export']['Raw']['Headers']['AdjustedPValue']		= "Adjusted P-Value";
			
			$results['Export']['Raw']['Headers']['ComparisonCategory']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title'];
			$results['Export']['Raw']['Headers']['ComparisonContrast']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonContrast']['Title'];
			
			$results['Export']['Raw']['Headers']['Disease']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Disease']['Title'];
			$results['Export']['Raw']['Headers']['StudyType']			= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['StudyType']['Title'];
			$results['Export']['Raw']['Headers']['TherapeuticArea']		= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['TherapeuticArea']['Title'];
			$results['Export']['Raw']['Headers']['Title']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Title']['Title'];
			$results['Export']['Raw']['Headers']['PubMed']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['PubMed']['Title'];
		}
		
		if (!isset($results['Export']['HTML']['Headers'])){
			$results['Export']['HTML']['Headers']['ComparisonID']		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title'];
			
			$results['Export']['HTML']['Headers']['Log2FoldChange']		= "Log<sub>2</sub>(Fold Change)";
			$results['Export']['HTML']['Headers']['PValue']				= "P-Value";
			$results['Export']['HTML']['Headers']['AdjustedPValue']		= "Adjusted P-Value";
			
			$results['Export']['HTML']['Headers']['ComparisonCategory']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title'];
			$results['Export']['HTML']['Headers']['ComparisonContrast']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonContrast']['Title'];
			
			$results['Export']['HTML']['Headers']['Disease']			= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Disease']['Title'];
			$results['Export']['HTML']['Headers']['StudyType']			= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['StudyType']['Title'];
			$results['Export']['HTML']['Headers']['TherapeuticArea']	= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['TherapeuticArea']['Title'];
			$results['Export']['HTML']['Headers']['Title']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Title']['Title'];
			$results['Export']['HTML']['Headers']['PubMed']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['PubMed']['Title'];
		}
		
		if (true){
			$tempArray = array();
			
			$tempArray['ComparisonID'] 			= $comparison['ComparisonID'];
			
			$tempArray['Log2FoldChange'] 		= $comparisonData['Log2FoldChange'];
			$tempArray['PValue'] 				= $comparisonData['PValue'];
			$tempArray['AdjustedPValue'] 		= $comparisonData['AdjustedPValue'];
			

			$tempArray['ComparisonCategory'] 	= $comparison['ComparisonCategory'];
			$tempArray['ComparisonContrast'] 	= $comparison['ComparisonContrast'];
			
			$tempArray['Disease'] 				= $project['Disease'];
			$tempArray['StudyType'] 			= $project['StudyType'];
			$tempArray['TherapeuticArea'] 		= $project['TherapeuticArea'];
			$tempArray['Title'] 				= $project['Title'];
			$tempArray['PubMed'] 				= $project['PubMed'];	
			
			$results['Export']['Raw']['Body'][] = $tempArray;
			
		}
		
		if (true){
			$tempArray = array();
			
			$tempArray['ComparisonID'] 			= $comparison['ComparisonID'];
			
			$decimal = 5;
			
			if (true){
				$sql_name						= 'Log2FoldChange';
				$currentColor 					= getStatScaleColor($comparisonData[$sql_name], $sql_name);
				
				if (is_numeric($comparisonData[$sql_name])){
					$currentValue				= round($comparisonData[$sql_name], $decimal);
				} else {
					$currentValue				= '';	
				}
				$tempArray[$sql_name] 			= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			}
			
			
			if (true){
				$sql_name						= 'PValue';
				$currentColor 					= getStatScaleColor($comparisonData[$sql_name], $sql_name);
				
				if (is_numeric($comparisonData[$sql_name])){
					$currentValue				= round($comparisonData[$sql_name], $decimal);
				} else {
					$currentValue				= '';	
				}
				$tempArray[$sql_name] 			= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			}
			
			
			if (true){
				$sql_name						= 'AdjustedPValue';
				$currentColor 					= getStatScaleColor($comparisonData[$sql_name], $sql_name);
				
				if (is_numeric($comparisonData[$sql_name])){
					$currentValue				= round($comparisonData[$sql_name], $decimal);
				} else {
					$currentValue				= '';	
				}
				$tempArray[$sql_name] 			= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			}


			$tempArray['ComparisonCategory'] 	= $comparison['ComparisonCategory'];
			$tempArray['ComparisonContrast'] 	= $comparison['ComparisonContrast'];
			
			$tempArray['Disease'] 				= $project['Disease'];
			$tempArray['StudyType'] 			= $project['StudyType'];
			$tempArray['TherapeuticArea'] 		= $project['TherapeuticArea'];
			$tempArray['Title'] 				= $project['Title'];
			$tempArray['PubMed'] 				= $project['PubMed'];	
			
			$results['Export']['HTML']['Body'][] = $tempArray;
			
		}

	}
	
	
	$results['Summary']['Chart']['range']['min'] = floor($results['Summary']['Log2FoldChange']['min']) - 0.5;
	$results['Summary']['Chart']['range']['max'] = ceil($results['Summary']['Log2FoldChange']['max']) + 0.5;
	
	
	
	
	if ($otherOptions['colorBy'] != ''){
		
		natksort($results['Chart-Legend']['Color']);
		if (isset($results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])])){
			$temp = $results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])];
			unset($results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])]);
			$results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])] = $temp;
		}
		
		if (true){
			$temp = array();
			
			$temp['name'] 	= sanitizeJavaScriptValue(ucwords2($results['Summary']['colorBy_Title']));
			$temp['color']	= '#FFF';
			$temp['xaxis']	= "xL0";
			$temp['yaxis']	= "yL0";
			$temp['ID']		= "legend_c_0";
			$temp['Type']	= 'Color';
			
			array_unshift($results['Chart-Legend']['Color'], $temp);
		}
	
		foreach($results['Chart-Legend']['Color'] as $tempKey => $tempValue){
			$results['Summary']['Legend_Variables'][] = $tempValue['ID'];	
		}

	}


	if ($otherOptions['shapeBy'] != ''){
		natksort($results['Chart-Legend']['Shape']);
		if (isset($results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])])){
			$temp = $results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])];
			unset($results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])]);
			$results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])] = $temp;
		}
		
		if (true){
			$temp = array();
			
			$temp['name'] 	= sanitizeJavaScriptValue(ucwords2($results['Summary']['shapeBy_Title']));
			$temp['color']	= '#FFF';
			$temp['xaxis']	= "xL0";
			$temp['yaxis']	= "yL0";
			$temp['ID']		= "legend_s_0";
			$temp['Type']	= 'Shape';
			
			array_unshift($results['Chart-Legend']['Shape'], $temp);
		}
		
		
		if (true){
			$temp = array();
			
			$temp['name'] 	= ' ';
			$temp['color']	= '#FFF';
			$temp['xaxis']	= "xL0";
			$temp['yaxis']	= "yL0";
			$temp['ID']		= "legend_spacer_0";
			$temp['Type']	= 'Shape';
			
			array_unshift($results['Chart-Legend']['Shape'], $temp);
		}
	
		foreach($results['Chart-Legend']['Shape'] as $tempKey => $tempValue){
			$results['Summary']['Legend_Variables'][] = $tempValue['ID'];	
		}
	}


	
	$traceIndex = 0;
	foreach($results['Chart'] as $traceID => $traceInfo){
		$traceIndex++;
		$results['Chart'][$traceID]['xaxis']	= "x{$traceIndex}";
		$results['Chart'][$traceID]['yaxis'] 	= "y1";
		$results['Chart'][$traceID]['ID']		= "trace{$traceIndex}";
		
		$results['Summary']['Trace_Variables'][] = $results['Chart'][$traceID]['ID'];
		$results['Summary']['Subplots_Variables'][] = '"' . $results['Chart'][$traceID]['xaxis'] . $results['Chart'][$traceID]['yaxis'] . '"';
	}
	

	
	$autoHeight = max(array_size($results['histogram']['y-axis']), array_size($results['Summary']['Legend_Variables'])) * 25;
	$autoHeight += $APP_CONFIG['APP']['Bubble_Plot']['margin']['Top'] + $APP_CONFIG['APP']['Bubble_Plot']['margin']['Bottom'];
	
	$results['Summary']['Height'] 	= max(800, $autoHeight);
	
	if (array_size($results['Chart']) > 1){
		$results['Summary']['Width']	= array_size($results['Chart'])*300;
	} else {
		$results['Summary']['Width']	= 0;
	}
	
	
	$results['Summary']['Data_Count']['Current'] = array_size($allComparisonData);
	
	$results['Summary']['Chart']['Title_Short']	= "Bubble Plot for {$otherOptions['geneNameStandard']}";

	$results['Summary']['Chart']['Title'] 		= array();
	$results['Summary']['Chart']['Title'][] 	= $results['Summary']['Chart']['Title_Short'];
	
	if ($results['Summary']['y-axis_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Group by {$results['Summary']['y-axis_Title']}";
	}
	
	if ($results['Summary']['colorBy_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Color by {$results['Summary']['colorBy_Title']}";
	}
	
	if ($results['Summary']['shapeBy_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Shape by {$results['Summary']['shapeBy_Title']}";
	}
	
	$temp = $results['Summary']['Chart']['Title'];
	array_shift($temp);
	$results['Summary']['Chart']['Title_Single_Line_No_Gene'] 	= implode('; ', $temp);
	$results['Summary']['Chart']['Title_Single_Line'] 			= implode('; ', $results['Summary']['Chart']['Title']);
	$results['Summary']['Chart']['Title'] 						= implode('<br />', $results['Summary']['Chart']['Title']);
	
	
	if (array_size($allComparisonData) <= 0){
		return false;
		exit();	
	}
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);

	return $results;
	
}

function prepareSingleBubblePlotDataByGeneName_Plotly_Subplot_v2($geneIndex = -1, $otherOptions = array()){
	
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	$geneIndex = intval($geneIndex);
	
	if ($geneIndex < 0) return false;
	
	$version = '2021-01-27 23:43';
	
	$cacheKey = __FUNCTION__ . '::' . md5($geneIndex . '::' . 
											json_encode($otherOptions) . '::' . 
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$results = prepareSingleBubblePlotDataByGeneName($geneIndex, $otherOptions);
	if ($results == false){
		return false;
	}
	
	$allComparisonData	= $results['ComparisonData'];
	$allProjectRecords 	= $results['ProjectRecords'];
	$allComparisons		= $results['ComparisonRecords'];
	
	unset($results['ComparisonData']);
	unset($results['ProjectRecords']);
	unset($results['ComparisonRecords']);
	
	
	$legendIndex = 0;
	foreach($allComparisonData as $tempKey => $comparisonData){

		
		$comparisonIndex 	= $comparisonData['ComparisonIndex'];
		$comparison 		= $allComparisons[$comparisonIndex];
		$valueColumn 		= $otherOptions['marker'];
		$projectID			= $comparison['ProjectName'];
		$project			= $allProjectRecords[$projectID];

		if (true){		
			$yAxisColumn	= $otherOptions['y-axis'];
			$currentYAxis	= $comparison[$yAxisColumn];
		}
		
		if (true){
			$colorByColumn 	= $otherOptions['colorBy'];
			$currentColorBy	= $comparison[$colorByColumn];
			
			if ($currentColorBy == ''){
				$currentColorBy = $APP_CONFIG['Blank_Value'];	
			}
		}
		
		if ($otherOptions['subplotBy'] != ''){
			$subplotColumn	= $otherOptions['subplotBy'];
			$currentSubplot	= $comparison[$subplotColumn];
			
			if ($currentSubplot == ''){
				$currentSubplot = $APP_CONFIG['Blank_Value'];	
			}			
		}
		
		if (true){
			$markerColumn 	= $otherOptions['marker'];
			$currentMarker	= $comparisonData[$markerColumn];
		}
		
		if ($otherOptions['shapeBy'] != ''){
			$shapeByColumn	= $otherOptions['shapeBy'];
			$currentShapeBy	= $comparison[$shapeByColumn];
			
			if ($currentShapeBy == ''){
				$currentShapeBy = $APP_CONFIG['Blank_Value'];	
			}
		}
		
		if (!isset($results['histogram']['y-axis'][$currentYAxis])){
			unset($allComparisonData[$tempKey]);
			continue;
		}
		
		if (!isset($results['histogram']['colorBy'][$currentColorBy])){
			unset($allComparisonData[$tempKey]);
			continue;
		}
		

		if ($otherOptions['y-axis_settings'] > 0){		
			if (strtolower($comparison[$yAxisColumn]) == 'normal control'){
				unset($allComparisonData[$tempKey]);
				continue;
			}
		}

		if ($otherOptions['colorBy_settings'] > 0){		
			if (strtolower($comparison[$colorByColumn]) == 'normal control'){
				unset($allComparisonData[$tempKey]);
				continue;
			}
		}
		
		
		if (!isset($results['Summary']['Log2FoldChange']['min'])){
			$results['Summary']['Log2FoldChange']['min'] = $comparisonData['Log2FoldChange'];
		}
		
		if (!isset($results['Summary']['Log2FoldChange']['max'])){
			$results['Summary']['Log2FoldChange']['max'] = $comparisonData['Log2FoldChange'];
		}
		
		if ($comparisonData['Log2FoldChange'] > $results['Summary']['Log2FoldChange']['max']){
			$results['Summary']['Log2FoldChange']['max'] = $comparisonData['Log2FoldChange'];
		}
		
		if ($comparisonData['Log2FoldChange'] < $results['Summary']['Log2FoldChange']['min']){
			$results['Summary']['Log2FoldChange']['min'] = $comparisonData['Log2FoldChange'];
		}
		

		$traceID = $currentSubplot;
		
		
		if (!isset($results['Chart'][$traceID]['name'])){
			$results['Chart'][$traceID]['name'] = sanitizeJavaScriptValue(ucwords2($currentSubplot));
		}
		
		$results['Chart'][$traceID]['x'][] = $comparisonData['Log2FoldChange'];
		$results['Chart'][$traceID]['y'][] = '"' . sanitizeJavaScriptValue(ucwords2($currentYAxis)) . '"';
		
		if (!isset($results['Chart'][$traceID]['yaxis-index'])){
			$results['Chart'][$traceID]['yaxis-index'][$currentYAxis] = 1;
		}
		
		$temp = array();
		$temp[] = "{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title']}: " . ucwords2($comparison['ComparisonID']);
		$temp[] = "{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title']}: " . ucwords2($comparison['ComparisonCategory']);
		$temp[] = "{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonContrast']['Title']}: " . ucwords2($comparison['ComparisonContrast']);
		$temp[] = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$yAxisColumn]['Title'] . ": " . ucwords2($currentYAxis);
		$temp[] = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$colorByColumn]['Title'] . ": " . ucwords2($currentColorBy);
		$temp[] = "p-value: {$comparisonData['PValue']}";
		$temp[] = "Adjusted P-value: {$comparisonData['AdjustedPValue']}";
		$temp[] = "log2(Fold Change): {$comparisonData['Log2FoldChange']}";
		$temp[] = "";
		$temp[] = "Double click to review the comparison details";
		$results['Chart'][$traceID]['text'][] = '"' . sanitizeJavaScriptValue(implode('<br />', $temp)) . '"';
		$results['Chart'][$traceID]['ComparisonIndex'][] = $comparisonIndex;
		
		
		if ($colorByColumn != ''){
			$currentColor = getRandomRGBColor($currentColorBy, $globalColorIndex, 0);
			
			if (!isset($results['Chart-Legend']['Color'][$currentColorBy])){
				$legendIndex++;
				$results['Chart-Legend']['Color'][$currentColorBy]['name'] 	= sanitizeJavaScriptValue(ucwords2($currentColorBy));
				$results['Chart-Legend']['Color'][$currentColorBy]['color'] 	= '#' . implode('', $currentColor);
				$results['Chart-Legend']['Color'][$currentColorBy]['xaxis']	= "xL{$legendIndex}";
				$results['Chart-Legend']['Color'][$currentColorBy]['yaxis'] 	= "yL{$legendIndex}";
				$results['Chart-Legend']['Color'][$currentColorBy]['ID']		= "legend{$legendIndex}";
			}
		} else {
			$currentColor = array(0, 0, 0);
		}
		$results['Chart'][$traceID]['color'][] = '"#' . implode('', $currentColor) . '"';
		
		
		if ($shapeByColumn != ''){
			
			$currentShape = plotly_get_marker_shapes_from_value($shapeByColumn, $currentShapeBy);
			
			if (!isset($results['Chart-Legend']['Shape'][$currentShapeBy])){
				$legendIndex++;
				$results['Chart-Legend']['Shape'][$currentShapeBy]['name'] 		= sanitizeJavaScriptValue(ucwords2($currentShapeBy));
				$results['Chart-Legend']['Shape'][$currentShapeBy]['color'] 	= '#000';
				$results['Chart-Legend']['Shape'][$currentShapeBy]['symbol'] 	= $currentShape;
				$results['Chart-Legend']['Shape'][$currentShapeBy]['xaxis']		= "xLS{$legendIndex}";
				$results['Chart-Legend']['Shape'][$currentShapeBy]['yaxis'] 	= "yLS{$legendIndex}";
				$results['Chart-Legend']['Shape'][$currentShapeBy]['ID']		= "legend_s_{$legendIndex}";
				$results['Chart-Legend']['Shape'][$currentShapeBy]['Type']		= 'Shape';
			}

		} else {
			$currentShape = 'circle';
		}
		$results['Chart'][$traceID]['symbol'][] = '"' . $currentShape . '"';
		
		
		if ($markerColumn != ''){
			if ((-1000) * log10($currentMarker) < 5000 && (-1000) * log10($currentMarker) > 100) {
				$marker_size = floatval((-1000) * log10($currentMarker));
			} else if ((-1000) * log10($currentMarker) > 5000) {
				$marker_size = 5000;
			} else {
				$marker_size = 100;
			}
			
			
			if (!isset($results['Summary']['Marker'])){
				if ($otherOptions['marker'] == 'AdjustedPValue'){
					$results['Summary']['Marker'] = '-log10(FDR)';
				} elseif ($otherOptions['marker'] == 'PValue'){
					$results['Summary']['Marker'] = '-log10(p-value)';
				}
			}
			
			$results['Chart'][$traceID]['size'][] = intval($marker_size);
		}

		if (!isset($results['Export']['Raw']['Headers'])){
			$results['Export']['Raw']['Headers']['ComparisonID']		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title'];
			
			$results['Export']['Raw']['Headers']['Log2FoldChange']		= "Log2(Fold Change)";
			$results['Export']['Raw']['Headers']['PValue']				= "P-Value";
			$results['Export']['Raw']['Headers']['AdjustedPValue']		= "Adjusted P-Value";
			
			$results['Export']['Raw']['Headers']['ComparisonCategory']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title'];
			$results['Export']['Raw']['Headers']['ComparisonContrast']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonContrast']['Title'];
			
			$results['Export']['Raw']['Headers']['Disease']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Disease']['Title'];
			$results['Export']['Raw']['Headers']['StudyType']			= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['StudyType']['Title'];
			$results['Export']['Raw']['Headers']['TherapeuticArea']		= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['TherapeuticArea']['Title'];
			$results['Export']['Raw']['Headers']['Title']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Title']['Title'];
			$results['Export']['Raw']['Headers']['PubMed']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['PubMed']['Title'];
		}
		
		if (!isset($results['Export']['HTML']['Headers'])){
			$results['Export']['HTML']['Headers']['ComparisonID']		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title'];
			
			$results['Export']['HTML']['Headers']['Log2FoldChange']		= "Log<sub>2</sub>(Fold Change)";
			$results['Export']['HTML']['Headers']['PValue']				= "P-Value";
			$results['Export']['HTML']['Headers']['AdjustedPValue']		= "Adjusted P-Value";
			
			$results['Export']['HTML']['Headers']['ComparisonCategory']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title'];
			$results['Export']['HTML']['Headers']['ComparisonContrast']	= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonContrast']['Title'];
			
			$results['Export']['HTML']['Headers']['Disease']			= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Disease']['Title'];
			$results['Export']['HTML']['Headers']['StudyType']			= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['StudyType']['Title'];
			$results['Export']['HTML']['Headers']['TherapeuticArea']	= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['TherapeuticArea']['Title'];
			$results['Export']['HTML']['Headers']['Title']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['Title']['Title'];
			$results['Export']['HTML']['Headers']['PubMed']				= $APP_CONFIG['DB_Dictionary']['Projects']['SQL']['PubMed']['Title'];
		}
		
		if (true){
			$tempArray = array();
			
			$tempArray['ComparisonID'] 			= $comparison['ComparisonID'];
			
			$tempArray['Log2FoldChange'] 		= $comparisonData['Log2FoldChange'];
			$tempArray['PValue'] 				= $comparisonData['PValue'];
			$tempArray['AdjustedPValue'] 		= $comparisonData['AdjustedPValue'];
			

			$tempArray['ComparisonCategory'] 	= $comparison['ComparisonCategory'];
			$tempArray['ComparisonContrast'] 	= $comparison['ComparisonContrast'];
			
			$tempArray['Disease'] 				= $project['Disease'];
			$tempArray['StudyType'] 			= $project['StudyType'];
			$tempArray['TherapeuticArea'] 		= $project['TherapeuticArea'];
			$tempArray['Title'] 				= $project['Title'];
			$tempArray['PubMed'] 				= $project['PubMed'];	
			
			$results['Export']['Raw']['Body'][] = $tempArray;
			
		}
		
		if (true){
			$tempArray = array();
			
			$tempArray['ComparisonID'] 			= $comparison['ComparisonID'];
			
			$decimal = 5;
			
			if (true){
				$sql_name						= 'Log2FoldChange';
				$currentColor 					= getStatScaleColor($comparisonData[$sql_name], $sql_name);
				
				if (is_numeric($comparisonData[$sql_name])){
					$currentValue				= round($comparisonData[$sql_name], $decimal);
				} else {
					$currentValue				= '';	
				}
				$tempArray[$sql_name] 			= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			}
			
			
			if (true){
				$sql_name						= 'PValue';
				$currentColor 					= getStatScaleColor($comparisonData[$sql_name], $sql_name);
				
				if (is_numeric($comparisonData[$sql_name])){
					$currentValue				= round($comparisonData[$sql_name], $decimal);
				} else {
					$currentValue				= '';	
				}
				$tempArray[$sql_name] 			= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			}

			
			
			if (true){
				$sql_name						= 'AdjustedPValue';
				$currentColor 					= getStatScaleColor($comparisonData[$sql_name], $sql_name);
				
				if (is_numeric($comparisonData[$sql_name])){
					$currentValue				= round($comparisonData[$sql_name], $decimal);
				} else {
					$currentValue				= '';	
				}
				$tempArray[$sql_name] 			= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			}


			$tempArray['ComparisonCategory'] 	= $comparison['ComparisonCategory'];
			$tempArray['ComparisonContrast'] 	= $comparison['ComparisonContrast'];
			
			$tempArray['Disease'] 				= $project['Disease'];
			$tempArray['StudyType'] 			= $project['StudyType'];
			$tempArray['TherapeuticArea'] 		= $project['TherapeuticArea'];
			$tempArray['Title'] 				= $project['Title'];
			$tempArray['PubMed'] 				= $project['PubMed'];	
			
			$results['Export']['HTML']['Body'][] = $tempArray;
			
		}

	}
	
	
	$results['Summary']['Chart']['range']['min'] = floor($results['Summary']['Log2FoldChange']['min']) - 0.5;
	$results['Summary']['Chart']['range']['max'] = ceil($results['Summary']['Log2FoldChange']['max']) + 0.5;
	
	natksort($results['Chart']);
	//Reorder the group based on subplot values
	if (isset($results['Chart'][($APP_CONFIG['Blank_Value'])])){
		$temp = $results['Chart'][($APP_CONFIG['Blank_Value'])];
		unset($results['Chart'][($APP_CONFIG['Blank_Value'])]);
		$results['Chart'][($APP_CONFIG['Blank_Value'])] = $temp;
	}
	
	
	if (isset($BXAF_CONFIG['COMPARISON_INFO'][($otherOptions['subplotBy'])])){
		
		$oldChart = $results['Chart'];
		
		$results['Chart'] = array();
		
		foreach($BXAF_CONFIG['COMPARISON_INFO'][($otherOptions['subplotBy'])] as $currentSubplot => $tempValue){
			if (isset($oldChart[$currentSubplot])){
				$results['Chart'][$currentSubplot] = $oldChart[$currentSubplot];
				unset($oldChart[$currentSubplot]);
			}
		}
		
		foreach($oldChart as $currentSubplot => $tempValue){
			$results['Chart'][$currentSubplot] = $oldChart[$currentSubplot];
			unset($oldChart[$currentSubplot]);
		}
		
		
	}
	
	
	
	if ($otherOptions['colorBy'] != ''){
		natksort($results['Chart-Legend']['Color']);
		if (isset($results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])])){
			$temp = $results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])];
			unset($results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])]);
			$results['Chart-Legend']['Color'][($APP_CONFIG['Blank_Value'])] = $temp;
		}
		
		if (true){
			$temp = array();
			
			$temp['name'] 	= sanitizeJavaScriptValue(ucwords2($results['Summary']['colorBy_Title']));
			$temp['color']	= '#FFF';
			$temp['xaxis']	= "xL0";
			$temp['yaxis']	= "yL0";
			$temp['ID']		= "legend_c_0";
			
			array_unshift($results['Chart-Legend']['Color'], $temp);
		}
	
		
		
		foreach($results['Chart-Legend']['Color'] as $tempKey => $tempValue){
			$results['Summary']['Legend_Variables'][] = $tempValue['ID'];	
		}
	}
	
	
	if ($otherOptions['shapeBy'] != ''){
		natksort($results['Chart-Legend']['Shape']);
		if (isset($results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])])){
			$temp = $results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])];
			unset($results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])]);
			$results['Chart-Legend']['Shape'][($APP_CONFIG['Blank_Value'])] = $temp;
		}
		
		if (true){
			$temp = array();
			
			$temp['name'] 	= sanitizeJavaScriptValue(ucwords2($results['Summary']['shapeBy_Title']));
			$temp['color']	= '#FFF';
			$temp['xaxis']	= "xL0";
			$temp['yaxis']	= "yL0";
			$temp['ID']		= "legend_s_0";
			$temp['Type']	= 'Shape';
			
			array_unshift($results['Chart-Legend']['Shape'], $temp);
		}
		
		
		if (true){
			$temp = array();
			
			$temp['name'] 	= ' ';
			$temp['color']	= '#FFF';
			$temp['xaxis']	= "xL0";
			$temp['yaxis']	= "yL0";
			$temp['ID']		= "legend_spacer_0";
			$temp['Type']	= 'Shape';
			
			array_unshift($results['Chart-Legend']['Shape'], $temp);
		}
	
		foreach($results['Chart-Legend']['Shape'] as $tempKey => $tempValue){
			$results['Summary']['Legend_Variables'][] = $tempValue['ID'];	
		}
	}
	
	
	$traceIndex = 0;
	foreach($results['Chart'] as $traceID => $traceInfo){
		$traceIndex++;
		$results['Chart'][$traceID]['xaxis']	= "x{$traceIndex}";
		$results['Chart'][$traceID]['yaxis'] 	= "y1";
		$results['Chart'][$traceID]['ID']		= "trace{$traceIndex}";
		
		$results['Summary']['Trace_Variables'][] = $results['Chart'][$traceID]['ID'];
		$results['Summary']['Subplots_Variables'][] = '"' . $results['Chart'][$traceID]['xaxis'] . $results['Chart'][$traceID]['yaxis'] . '"';
		
		
		foreach($results['histogram']['y-axis'] as $currentYAxis => $yAxisCount){
			if (!isset($results['Chart'][$traceID]['yaxis-index'][$currentYAxis])){
				$results['Chart'][$traceID]['x'][] 		= 0;
				$results['Chart'][$traceID]['y'][] 		= '"' . sanitizeJavaScriptValue(ucwords2($currentYAxis)) . '"';
				$results['Chart'][$traceID]['color'][] 	= '"#FFF"';
				$results['Chart'][$traceID]['size'][] 	= 0;
			}
			
			$results['Chart'][$traceID]['x'][] 		= $results['Summary']['Chart']['range']['min'];
			$results['Chart'][$traceID]['y'][] 		= '"' . sanitizeJavaScriptValue(ucwords2($currentYAxis)) . '"';
			$results['Chart'][$traceID]['color'][] 	= '"#FFF"';
			$results['Chart'][$traceID]['size'][] 	= 0;
			
			$results['Chart'][$traceID]['x'][] 		= $results['Summary']['Chart']['range']['max'];
			$results['Chart'][$traceID]['y'][] 		= '"' . sanitizeJavaScriptValue(ucwords2($currentYAxis)) . '"';
			$results['Chart'][$traceID]['color'][] 	= '"#FFF"';
			$results['Chart'][$traceID]['size'][] 	= 0;
		}
		
	}
	

	$autoHeight = max(array_size($results['histogram']['y-axis']), array_size($results['Summary']['Legend_Variables'])) * 25;
	$autoHeight += $APP_CONFIG['APP']['Bubble_Plot']['margin']['Top'] + $APP_CONFIG['APP']['Bubble_Plot']['margin']['Bottom'];
	
	$results['Summary']['Height'] 	= max(800, $autoHeight);
	
	
	if (array_size($results['Chart']) > 1){
		$results['Summary']['Width']	= array_size($results['Chart'])*300;
	} else {
		$results['Summary']['Width']	= 0;
	}
	
	
	
	$results['Summary']['Data_Count']['Current'] = array_size($allComparisonData);


	$results['Summary']['Chart']['Title_Short']	= "Bubble Plot for {$otherOptions['geneNameStandard']}";

	$results['Summary']['Chart']['Title'] 		= array();
	$results['Summary']['Chart']['Title'][] 	= $results['Summary']['Chart']['Title_Short'];
	
	if ($results['Summary']['y-axis_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Group by {$results['Summary']['y-axis_Title']}";
	}
	
	if ($results['Summary']['colorBy_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Color by {$results['Summary']['colorBy_Title']}";
	}
	
	if ($results['Summary']['shapeBy_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Shape by {$results['Summary']['shapeBy_Title']}";
	}
	
	if ($results['Summary']['subplotBy_Title'] != ''){
		$results['Summary']['Chart']['Title'][] = "Subplot by {$results['Summary']['subplotBy_Title']}";
	}


	$temp = $results['Summary']['Chart']['Title'];
	array_shift($temp);
	$results['Summary']['Chart']['Title_Single_Line_No_Gene'] 	= implode('; ', $temp);
	$results['Summary']['Chart']['Title_Single_Line'] 			= implode('; ', $results['Summary']['Chart']['Title']);
	$results['Summary']['Chart']['Title'] 						= implode('<br />', $results['Summary']['Chart']['Title']);
	
	
	
	if (array_size($allComparisonData) <= 0){
		return false;
		exit();	
	}
	
	
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
}

?>