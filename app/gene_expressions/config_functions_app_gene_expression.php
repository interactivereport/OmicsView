<?php

function prepareGeneExpressionDataByGeneName($geneName = '', $columnToPlots = '', $valueTable = 'GeneFPKM', $valueColumn = 'FPKM', $searchOption = 0, $additionalMaterial = '', $additionalMaterial2 = '', $dataSource = array(), $internalProjectIndexes = array(), $otherOptions = array()){
	
	
	
	global $APP_CONFIG;
	
	$geneName = trim($geneName);
	
	if ($geneName == '') return false;
	
	$searchOption = intval($searchOption);
	if (($searchOption < 0) || ($searchOption > 2)){
		$searchOption = 0;	
	}
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	
	$cacheKey = __FUNCTION__ . '::' . md5($geneName . '::' . 
											json_encode($columnToPlots) . '::' . 
											$valueTable . '::' . 
											$valueColumn . '::' . 
											$searchOption . '::' . 
											json_encode($additionalMaterial) . '::' . 
											json_encode($additionalMaterial2) . '::' . 
											json_encode($dataSource) . '::' . 
											json_encode($internalProjectIndexes) . '::' . 
											json_encode($otherOptions) . '::' . 
											json_encode(gene_uses_TPM()) . '::' . 
											$version
											);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	if ($searchOption == 0){
		//Data Filter
		$dataFilter = &$additionalMaterial;
		

		
		foreach($dataFilter as $currentColumn => $values){
			if (strpos($currentColumn, 'Custom_') === 0){
				$dataFilterHasCustomerColumn = 1;
			}
		}
		
		
		
	} elseif ($searchOption == 1){
		//Comparison
		//public data source only, private will be disabled
		$comparisonIDList = array_clean($additionalMaterial);

		//Searching the sampleID (sampleName) using Comparison
		if (array_size($comparisonIDList) > 0){

			
			$columnToPlotComparisonArray	= array_clean($additionalMaterial2);
			
			
			foreach($columnToPlotComparisonArray as $tempKeyX => $currentColumn){
				if ($APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'] != ''){
					$columnToPlotComparisonArrayNative[$currentColumn] = $currentColumn;
				} else {
					$columnToPlotComparisonArrayNonNative[$currentColumn] = $currentColumn;
				}
			}
			
			
			
			$columnToPlotComparisonString	= implode(', ', $columnToPlotComparisonArrayNative);
			
	
			if ($columnToPlotComparisonString != ''){
				$columnToPlotComparisonString = ", {$columnToPlotComparisonString}";	
			}
			
			$results['ComparisonID']['Input'] = $comparisonIDList;
			
			
			
			unset($comparisonIDString);
			foreach($comparisonIDList as $tempKey => $tempValue){
				$comparisonIDString[] = "'" . addslashes($tempValue) . "'";
			}
			
			$comparisonIDString = implode(',', $comparisonIDString);
			
			$sql_table 			= 'Comparisons';
			$sql_column 		= 'ComparisonID';
			
			$comparisonResult	= search_comparisons($comparisonIDList, "ComparisonID, ComparisonIndex, Case_SampleIDs, Control_SampleIDs {$columnToPlotComparisonString}", $dataSource, $internalProjectIndexes, 'GetArray');
			

			
			if (array_size($comparisonResult) > 0){
				$results['ComparisonID']['Output'] = array_column($comparisonResult, 'ComparisonID');
				
				$comparisonIndexList	= array_column($comparisonResult, 'ComparisonIndex');
				$sampleIDList1			= array_column($comparisonResult, 'Case_SampleIDs');
				$sampleIDList2			= array_column($comparisonResult, 'Control_SampleIDs');
				$sampleIDList			= array_clean(explode(';', implode(';', $sampleIDList1) . ';' . implode(';', $sampleIDList2)));
				
				if (array_size($columnToPlotComparisonArray) > 0){
					
					$needToCombineSampleComparison = true;
					
					foreach($comparisonResult as $tempKey1 => $currentComparison){
						
						$currentSampleIDs = array_clean(explode(';', $currentComparison['Case_SampleIDs'] . ';' . $currentComparison['Control_SampleIDs']));
						
						$currentSampleID_Case 		= array_clean(explode(';', $currentComparison['Case_SampleIDs']));
						$currentSampleID_Control 	= array_clean(explode(';', $currentComparison['Control_SampleIDs']));

						foreach($currentSampleIDs as $tempKey2 => $currentSampleID){
							
							$sampleIDToComparisonIndex[$currentSampleID][$currentComparison['ComparisonIndex']] = $currentComparison['ComparisonID'];
							$sampleIDToComparisonIndex_Full[$currentSampleID][$currentComparison['ComparisonIndex']] = $currentComparison;
							$sampleID_ComparisonID = "{$currentSampleID}|{$currentComparison['ComparisonID']}";
							
							foreach($columnToPlotComparisonArray as $tempKey3 => $currentComparisonColumn){
								
								if (isset($currentComparison[$currentComparisonColumn])){
									$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = $currentComparison[$currentComparisonColumn];
								} else {
									
									if ($currentComparisonColumn == 'ComparisonID_Type'){
										
										if (in_array($currentSampleID, $currentSampleID_Case)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = $currentComparison['ComparisonID'] . '_Case';
											
											
										} elseif (in_array($currentSampleID, $currentSampleID_Control)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = $currentComparison['ComparisonID'] . '_Control';
											
										}
									}
									
									if ($currentComparisonColumn == 'Comparison_Type'){
										
										if (in_array($currentSampleID, $currentSampleID_Case)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = 'Case';

										} elseif (in_array($currentSampleID, $currentSampleID_Control)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = 'Control';	

										}
									}
									
								}
							}
							
						}
					}
				}
				

			} else {
				$dataFilter 	= '';
				$results['ComparisonID']['Output'] = array();
			}
			
		}

	} else {
		//Sample
		$sampleIDList = array_clean($additionalMaterial);
		
	}
	
	

	

	//*******************
	// Search Gene Index
	//*******************
	$species	= $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping_Choice'];
	$sql_table 	= $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping'][$species]['Table'];
	
	if ($sql_table != ''){
		$SQL 		= "SELECT GeneIndex FROM {$sql_table} WHERE ID = '{$geneName}'";
	} else {
		$sql_table 	= 'GeneCombined';
		$sql_column = 'GeneName';
		$SQL 		= "SELECT GeneIndex FROM {$sql_table} WHERE {$sql_column} = '{$geneName}'";		
	}
	$geneIndexes	= getSQL($SQL, 'GetCol', $sql_table);
	
	
	
	if (array_size($geneIndexes) <= 0){
		return false;
	}
	

	$species	= $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping_Choice'];
	$sql_table 	= $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping'][$species]['Table'];
	
	if ($sql_table != ''){
		$SQL 		= "SELECT ID FROM {$sql_table} WHERE ID = '{$geneName}'";
	} else {
		$sql_table 	= 'GeneCombined';
		$sql_column = 'GeneName';
		$SQL 		= "SELECT GeneName FROM {$sql_table} WHERE {$sql_column} = '{$geneName}'";
	}
	$geneName				= getSQL($SQL, 'GetOne', $sql_table);
	$results['GeneName'] 	= $geneName;
	
	//*******************
	// Search GeneFPKM
	//*******************
	$geneIndexString = implode(', ', $geneIndexes);
	
	
	if ($searchOption == 0){
		//default, using data filter
		$sql_table 				= $valueTable;
		$sql_column 			= 'GeneIndex';
		$SQL 					= "SELECT * FROM {$sql_table} WHERE {$sql_column} IN ({$geneIndexString})";

		unset($geneExpressionValue);
		if ($dataSource['public'] != ''){
			$geneExpressionValue	= tabix_search_records_with_index($geneIndexes, '', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, '', $sql_table);
				
				foreach($temp as $tempKeyX => $tempValueX){
					$geneExpressionValue[] = $tempValueX;
				}
				
				unset($temp);
			}
		}
		
	} else {
		
		
		
		//Search using sample IDs
		unset($sampleIDString);
		
		if (array_size($sampleIDList) > 0){
			foreach($sampleIDList as $tempKey => $tempValue){
				$sampleIDString[] = "'" . addslashes($tempValue) . "'";
			}
			$sampleIDString 		= implode(', ', $sampleIDString);
			
			$results['SampleID']['Input'] = array_combine($sampleIDList, $sampleIDList);
			
			$geneExpressionValue = array();
			if ($dataSource['public'] != ''){
				$sql_table 				= 'Samples';
				$sql_column 			= 'SampleID';
				$SQL 					= "SELECT SampleIndex FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIDString})";
				
				if ($otherOptions['Filter']){
					$SQL 				= "SELECT SampleIndex FROM {$sql_table} WHERE ({$sql_column} IN ({$sampleIDString})) AND (`{$otherOptions['Filter_Column']}` = '{$otherOptions['Filter_Value']}')";	
				}
				
				$sampleIndexes			= getSQL($SQL, 'GetCol', $sql_table);
				
				$searchUsingSampleIndex['Public'] = $sampleIndexes;
			
				if (array_size($sampleIndexes) > 0){
					$sql_table 				= $valueTable;
					$geneExpressionValue	= tabix_search_records_with_index($geneIndexes, $sampleIndexes, $sql_table);
				}
			}
			
			
			if ($dataSource['private'] != ''){
				$sql_table 				= 'App_User_Data_Samples';
				$sql_column 			= 'SampleID';
				$SQL 					= "SELECT SampleIndex FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIDString}) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";
				
				
				
				if ($otherOptions['Filter']){
					$SQL 				= "SELECT SampleIndex FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIDString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) AND (`{$otherOptions['Filter_Column']}` = '{$otherOptions['Filter_Value']}')";	
				}
				
				
				$sampleIndexesPrivate	= getSQL($SQL, 'GetCol', $sql_table);
				
				$searchUsingSampleIndex['Private'] = $sampleIndexesPrivate;

	
				$sql_table 				= $valueTable;
				foreach($internalProjectIndexes as $tempKey => $projectIndex){
					$temp					= tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $sampleIndexesPrivate, $sql_table);
					
					
						
					foreach($temp as $tempKeyX => $tempValueX){
						$geneExpressionValue[] = $tempValueX;
					}
					
					unset($temp);
				}
			}
			
			
			
		} else {
			$sql_table 				= $valueTable;
			
			unset($geneExpressionValue);
			if ($dataSource['public'] != ''){
				$geneExpressionValue	= tabix_search_records_with_index($geneIndexes, '', $sql_table);
			}
			
			if ($dataSource['private'] != ''){
				foreach($internalProjectIndexes as $tempKey => $projectIndex){
					$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, '', $sql_table);
					
					foreach($temp as $tempKeyX => $tempValueX){
						$geneExpressionValue[] = $tempValueX;
					}
					
					unset($temp);
				}
			}
			
		}

	}


	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	//*******************
	// Search Samples
	//*******************
	$sampleIndexes		= array_column($geneExpressionValue, 'SampleIndex');
	$sampleIndexes		= array_clean($sampleIndexes);
	
	$sampleIndexString 	= implode(', ', $sampleIndexes);
	
	$columnToPlotString	= implode(', ', $columnToPlots);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	

	$sql_table 		= 'Samples';
	$sql_column 	= 'SampleIndex';

	$searchConditions = array();
	if ($searchOption == 0){
		//Data Filter
		foreach($dataFilter as $currentColumn => $values){
			
			unset($valueString);
			foreach($values as $tempKey => $tempValue){
				$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
			}
			
			$valueString		= implode(", ", $valueString);
			$searchConditions[$currentColumn] = "({$currentColumn} IN ({$valueString}))";
		}
		
		
		if ($otherOptions['Modified_DiseaseState_Enable']){
			if (array_size($dataFilter['DiseaseState']) > 0){
				//Data Filter may contain the value from the mapping table
				unset($valueString);
				foreach($dataFilter['DiseaseState'] as $tempKey => $tempValue){
					$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
				}
					
				$valueString		= implode(", ", $valueString);
				
				if ($otherOptions['Modified_DiseaseState_Display_Abbreviation']){
					$specialSearchConditions = "(`Comparison_Disease_State_Abbreviation` IN ({$valueString}))";
				} else {
					$specialSearchConditions = "(`Comparison_Disease_State` IN ({$valueString}))";
				}
				
				if ($specialSearchConditions != ''){
					$SQL = "SELECT `SampleIndex` FROM `{$APP_CONFIG['Table']['App_Sample_Comparison_DiseaseState']}` WHERE ({$specialSearchConditions})";
					$specialSpecialIndexes = getSQL($SQL, 'GetCol', $sql_table);
					$specialSpecialIndexes = array_clean($specialSpecialIndexes);
				}
				
				if (array_size($specialSpecialIndexes) > 0){
					$specialSpecialIndexes = implode(',', $specialSpecialIndexes);
					$searchConditions['DiseaseState'] = "(({$searchConditions['DiseaseState']}) OR (`SampleIndex` IN ({$specialSpecialIndexes})))";
				}
				
				
			}
		}
		$searchConditions = implode(' AND ', $searchConditions);
	}
	
	unset($sampleSources);
	if ($dataSource['public'] != ''){
		
		if (isset($searchUsingSampleIndex['Public'])){
			
			$currentSampleIndexString = implode(',', $searchUsingSampleIndex['Public']);
			
			$SQL = "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE ({$sql_column} IN ({$currentSampleIndexString}))";
			
			
		} else {
			if ($searchConditions != ''){
				$SQL = "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE ({$sql_column} IN ({$sampleIndexString})) AND ({$searchConditions})";
			} else {
				$SQL = "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE ({$sql_column} IN ({$sampleIndexString}))";	
			}
		}
	
		$sampleSources	= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	
	
	unset($temp);
	if ($dataSource['private'] != ''){
		
		unset($searchConditionsPrivate);
		if ($searchOption == 0){
			//DataFilter
			
			if ($dataFilterHasCustomerColumn){
				
				foreach($dataFilter as $currentColumn => $values){
					if (strpos($currentColumn, 'Custom_') === 0){
						$column_info = internal_data_get_column($internalProjectIndexes, 'samples', $currentColumn);
						
						$column_real = $flexibleColumnSummary['By-Column'][$currentColumn]['SQL'];

						if ($column_real == '') continue;
						
						$results['Summary']['Column_Title'][$currentColumn] 	= $flexibleColumnSummary['By-Column'][$currentColumn]['Title'];
						$results['Summary']['Column_Mapping'][$currentColumn] 	= $column_real;
						
						unset($valueString);
						foreach($values as $tempKey => $tempValue){
							$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
						}
						
						$valueString		= implode(", ", $valueString);
						$searchConditionsPrivate[] = "((`{$column_real}` IN ({$valueString})) AND (`ProjectIndex` = '{$column_info['ProjectIndex']}'))";
						
						
					} else {
						$column_real = $currentColumn;
						
						if (!isset($APP_CONFIG['Internal_Data']['Samples']['Headers'][$currentColumn])) continue;

						
						unset($valueString);
						foreach($values as $tempKey => $tempValue){
							$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
						}
						
						$valueString		= implode(", ", $valueString);
						$searchConditionsPrivate[] = "({$currentColumn} IN ({$valueString}))";
						
					}

				}
				
				
				
			} else {
			
				foreach($dataFilter as $currentColumn => $values){
					
					if (!isset($APP_CONFIG['Internal_Data']['Samples']['Headers'][$currentColumn])) continue;
					
					unset($valueString);
					foreach($values as $tempKey => $tempValue){
						$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
					}
					
					$valueString		= implode(", ", $valueString);
					$searchConditionsPrivate[] = "({$currentColumn} IN ({$valueString}))";
				}
				
			}
			
			
			$searchConditionsPrivate = implode(' AND ', $searchConditionsPrivate);
			
			if ($searchConditionsPrivate == ''){
				$SQL 		= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString})";
			} else {
				$SQL 		= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString}) AND ({$searchConditionsPrivate})";
			}
			$temp = getSQL($SQL, 'GetAssoc', 'App_User_Data_Samples');
			
			
		} else {

			if (array_size($searchUsingSampleIndex['Private']) > 0){
				$searchConditionsPrivate = "`SampleIndex` IN (" . implode(',', $searchUsingSampleIndex['Private']) . ")";
				$SQL 		= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString}) AND ({$searchConditionsPrivate})";
			}
			$temp = getSQL($SQL, 'GetAssoc', 'App_User_Data_Samples');
		}

		
		foreach($temp as $currentSampleIndex => $currentSampleInfo){
			$sampleSources[$currentSampleIndex] = internal_data_transform_one_data('Samples', $currentSampleInfo);
			

			if (!isset($sampleSources[$currentSampleIndex]['ComparisonIndex'])){
				
				$currentSampleID = $currentSampleInfo['SampleID'];
				
				if ($currentSampleID != ''){
					foreach($sampleIDToComparisonIndex_Full[$currentSampleID] as $currentComparisonIndex => $currentComparison){
						$sampleSources[$currentSampleIndex]['ComparisonIndex'] = $currentComparisonIndex;
						$sampleSources[$currentSampleIndex]['ComparisonID'] = $currentComparison['ComparisonID'];
						$sampleSources[$currentSampleIndex]['ComparisonCategory'] = $currentComparison['ComparisonCategory'];
					}
				}
				
			}
		}
		
		
			
		unset($temp);
	}


	if (array_size($sampleSources) <= 0){
		return false;
	} else {
		
		$results['DataCount_WihoutConditions'] = 0;
		
		if ($dataSource['public'] != ''){
			$SQL 		= "SELECT count(*) FROM {$sql_table} WHERE ({$sql_column} IN ({$sampleIndexString}))";
			$results['DataCount_WihoutConditions'] += intval(getSQL($SQL, 'GetOne', $sql_table));
		}
		
		if ($dataSource['private'] != ''){
			$SQL 		= "SELECT count(*) FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString})";
			$results['DataCount_WihoutConditions'] += intval(getSQL($SQL, 'GetOne', 'App_User_Data_Samples'));
		}
		
		
		if ($otherOptions['Modified_DiseaseState_Enable']){
			
			if ($searchOption == 0){
				//Data Filter
				$sampleSources = modify_sample_disease_state_values($sampleSources, 
																	$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
																	array(),
																	$dataFilter['DiseaseState'],
																	$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
																	1,
																	$modify_sample_disease_state_values_executed
																	);
			} elseif ($searchOption == 1){
				//Comparison	
				$sampleSources = modify_sample_disease_state_values($sampleSources, 
																	$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
																	$comparisonIndexList,
																	array(),
																	$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
																	1,
																	$modify_sample_disease_state_values_executed
																	);
			} else {
				//Sample ID
				$sampleSources = modify_sample_disease_state_values($sampleSources, 
																	$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
																	array(),
																	array(),
																	$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
																	1,
																	$modify_sample_disease_state_values_executed
																	);
			}

			
			if ($modify_sample_disease_state_values_executed){
				$modify_sample_disease_state_values = true;
				$hasTransformed = true;
			} else {
				$modify_sample_disease_state_values = false;
				$hasTransformed = false;
			}
		}
	}
	
	
	if (array_size($columnToPlotComparisonArray) > 0){
		$hasTransformed = true;
		
		if (!$modify_sample_disease_state_values){
			
			foreach($sampleSources as $currentSampleIndex => $currentSampleInfo){
				unset($currentTemp);
				
				$currentSampleID = $currentSampleInfo['SampleID'];
				
				foreach($sampleIDToComparisonIndex[$currentSampleID] as $currentComparisonIndex => $currentComparisonID){
					
					$temp = $currentSampleInfo;
					$temp['SampleID_Org'] 		= $currentSampleID;
					$temp['SampleID'] 			= "{$currentSampleID}|{$currentComparisonID}";
					$temp['ComparisonIndex'] 	= $currentComparisonIndex;
					$temp['Mode'] = 1;

					$currentTemp[] = $temp;
				}
				
				
				$sampleSources[$currentSampleIndex] = $currentTemp;
				
			}
			
			
		} else {
			
			foreach($sampleSources as $currentSampleIndex => $currentSamples){
				
				foreach($currentSamples as $tempKey => $currentSampleInfo){
					
					$currentSampleID		= $currentSampleInfo['SampleID'];
					$currentComparisonIndex	= $currentSampleInfo['ComparisonIndex'];
					$currentComparisonID 	= $sampleIDToComparisonIndex[$currentSampleID][$currentComparisonIndex];
					
					$sampleSources[$currentSampleIndex][$tempKey]['SampleID'] = "{$currentSampleID}|{$currentComparisonID}";
					$sampleSources[$currentSampleIndex][$tempKey]['SampleID_Org'] = $currentSampleID;
					$sampleSources[$currentSampleIndex][$tempKey]['Mode'] = 2;
					
				}
				
			}
		}
	}

	foreach($geneExpressionValue as $geneExpressionKey => $geneExpressionArray){
		$currentSampleIndex = $geneExpressionArray['SampleIndex'];

		if (isset($sampleSources[$currentSampleIndex])){
			
			if (!$hasTransformed){
				$temp = $sampleSources[$currentSampleIndex];
				
				unset($sampleSources[$currentSampleIndex]);
				
				$sampleSources[$currentSampleIndex][0] = $temp;
			}
			
			foreach($sampleSources[$currentSampleIndex] as $currentSampleIndexX => $currentSampleRecord){
				
				$currentSampleIndex2 = $currentSampleIndex . '-' . $currentSampleIndexX;
	
				
				$results['DataCount']++;
				
				$results['canvasxpress']['y']['smps'][$currentSampleIndex2] 
					= '"' . sanitizeJavaScriptValue($currentSampleRecord['SampleID']) . '"';
				
				$otherOptions['transform_value'] = floatval($otherOptions['transform_value']);
					
				/*
				$results['canvasxpress']['y']['data'][$currentSampleIndex2] 
					= log2(floatval($geneExpressionArray[$valueColumn]) + $otherOptions['transform_value']);
				*/
					
				$results['canvasxpress']['y']['data'][$currentSampleIndex2] 
					= floatval($geneExpressionArray[$valueColumn]) + $otherOptions['transform_value'];
				
				if (!isset($results['Export']['Raw']['Headers']['SampleIndex'])){
					$results['Export']['Raw']['Headers']['SampleID'] 			= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
					$results['Export']['Transformed']['Headers']['SampleID'] 	= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
				}
	
				$results['Export']['Raw']['Body'][$currentSampleIndex2]['SampleID'] 				= $currentSampleRecord['SampleID'];
				$results['Export']['Transformed']['Body'][$currentSampleIndex2]['SampleID'] 		= $currentSampleRecord['SampleID'];
				$results['SampleID']['Output'][$currentSampleRecord['SampleID']]					= $currentSampleRecord['SampleID'];
				
				
				if (array_size($columnToPlotComparisonArrayNative) > 0){
					
					foreach($columnToPlotComparisonArrayNative as $tempKey3 => $currentComparisonColumn){
						
						if (!isset($results['Export']['Raw']['Headers'][$currentComparisonColumn])){
							$results['Export']['Raw']['Headers'][$currentComparisonColumn]
								= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentComparisonColumn]['Title'];
							$results['Export']['Transformed']['Headers'][$currentComparisonColumn] 	
								= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentComparisonColumn]['Title'];

						}
						
						$currentSampleID = $currentSampleRecord['SampleID'];
						
						$categoryToPlot	= array_clean($sampleComparisonAttributes[$currentSampleID][$currentComparisonColumn], 0, 1, 1, 0);
						$categoryToPlot	= ucwords2(trim(implode('; ', $categoryToPlot)));
						if ($categoryToPlot	== ''){
							$categoryToPlot	= $APP_CONFIG['Blank_Value'];
						}
						
						$results['Category_Count'][$currentComparisonColumn][$categoryToPlot]++;
						
						$results['canvasxpress']['x'][$currentComparisonColumn][$currentSampleIndex2] 
							= '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
						
						$results['Export']['Raw']['Body'][$currentSampleIndex2][$currentComparisonColumn] = $categoryToPlot;
						$results['Export']['Transformed']['Body'][$currentSampleIndex2][$currentComparisonColumn] = $categoryToPlot;
					}
				}
				
				
				
				
				if (array_size($columnToPlotComparisonArrayNonNative) > 0){
					
					foreach($columnToPlotComparisonArrayNonNative as $tempKey3 => $currentComparisonColumn){
						
						if (!isset($results['Export']['Raw']['Headers'][$currentComparisonColumn])){
							$results['Export']['Raw']['Headers'][$currentComparisonColumn]
								= $APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'][$currentComparisonColumn];
							$results['Export']['Transformed']['Headers'][$currentComparisonColumn] 	
								= $APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'][$currentComparisonColumn];
						}
						
						$currentSampleID = $currentSampleRecord['SampleID'];
						
						$categoryToPlot	= array_clean($sampleComparisonAttributes[$currentSampleID][$currentComparisonColumn], 0, 1, 1, 0);
						$categoryToPlot	= ucwords2(trim(implode('; ', $categoryToPlot)));
						if ($categoryToPlot	== ''){
							$categoryToPlot	= $APP_CONFIG['Blank_Value'];
						}
						
						$results['Category_Count'][$currentComparisonColumn][$categoryToPlot]++;
						
						$results['canvasxpress']['x'][$currentComparisonColumn][$currentSampleIndex2] 
							= '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
						
						$results['Export']['Raw']['Body'][$currentSampleIndex2][$currentComparisonColumn] = $categoryToPlot;
						$results['Export']['Transformed']['Body'][$currentSampleIndex2][$currentComparisonColumn] = $categoryToPlot;
					}
				}
				
				
				
				
				
				foreach($columnToPlots as $tempKey2 => $columnToPlot){

					
					
					if (!isset($results['Export']['Raw']['Headers'][$columnToPlot])){
						
						$currentColumnTitle = $flexibleColumnSummary['By-Column'][$columnToPlot]['Title'];
						
						if ($currentColumnTitle == ''){
							$currentColumnTitle = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$columnToPlot]['Title'];
						}
						
						
						$results['Export']['Raw']['Headers'][$columnToPlot] = $currentColumnTitle;
						$results['Export']['Transformed']['Headers'][$columnToPlot] = $currentColumnTitle;
					}
					
					
					
					
					$categoryToPlot		= ucwords2(trim($currentSampleRecord[$columnToPlot]));
					if ($categoryToPlot	== ''){
						$categoryToPlot	= $APP_CONFIG['Blank_Value'];
					}
					
					$results['Category_Count'][$columnToPlot][$categoryToPlot]++;
					
					
					$results['canvasxpress']['x'][$columnToPlot][$currentSampleIndex2] 
						= '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
					
						
					
					$results['Export']['Raw']['Body'][$currentSampleIndex2][$columnToPlot] = $categoryToPlot;
					$results['Export']['Transformed']['Body'][$currentSampleIndex2][$columnToPlot] = $categoryToPlot;
					
				}
				
				if (!isset($results['Export']['Raw']['Headers']["{$geneName} {$valueColumn}"])){
					$results['Export']['Raw']['Headers']["{$geneName} {$valueColumn}"] 			= $geneName;
					$results['Export']['Transformed']['Headers']["{$geneName} {$valueColumn}"] 	= $geneName;
				}
				
				$results['Export']['Raw']['Body'][$currentSampleIndex2][$valueColumn]			= $geneExpressionArray[$valueColumn];
				
				if ($otherOptions['transform']){
					$results['Export']['Transformed']['Body'][$currentSampleIndex2][$valueColumn]
						= log2(floatval($geneExpressionArray[$valueColumn]) + $otherOptions['transform_value']);
				} else {
					$results['Export']['Transformed']['Body'][$currentSampleIndex2][$valueColumn]
						= floatval($geneExpressionArray[$valueColumn]) + $otherOptions['transform_value'];
				}
			
			}
			
			
		}

	}
	
	
	
	
	if ($searchOption == 1){
		
		
		$results['ComparisonID']['Input_Count'] 			= array_size($results['ComparisonID']['Input']);
		$results['ComparisonID']['Input_Count_Display'] 	= number_format($results['ComparisonID']['Input_Count']);
		
		$results['ComparisonID']['Output_Count'] 			= array_size($results['ComparisonID']['Output']);
		$results['ComparisonID']['Output_Count_Display'] 	= number_format($results['ComparisonID']['Output_Count']);

		
		if ($results['ComparisonID']['Input_Count'] != $results['ComparisonID']['Output_Count']){
			$results['ComparisonID']['Missing'] 				= array_diff($results['ComparisonID']['Input'], $results['ComparisonID']['Output']);
			$results['ComparisonID']['Missing_Count'] 			= array_size($results['ComparisonID']['Missing']);
			$results['ComparisonID']['Missing_Count_Display'] 	= number_format($results['ComparisonID']['Missing_Count']);
		}
	}
	
	
	if ($searchOption == 2){
		
		$results['SampleID']['Input_Count'] 					= array_size($results['SampleID']['Input']);
		$results['SampleID']['Input_Count_Display'] 			= number_format($results['SampleID']['Input_Count']);
				
				
		$results['SampleID']['Output_Count'] 					= array_size($results['SampleID']['Output']);
		$results['SampleID']['Output_Count_Display'] 			= number_format($results['SampleID']['Output_Count']);
		
		if ($results['SampleID']['Input_Count'] != $results['SampleID']['Output_Count']){
			$results['SampleID']['Missing'] 				= array_diff($results['SampleID']['Input'], $results['SampleID']['Output']);
			$results['SampleID']['Missing_Count'] 			= array_size($results['SampleID']['Missing']);
			$results['SampleID']['Missing_Count_Display'] 	= number_format($results['SampleID']['Missing_Count']);
			
		}
	}
	

	
	foreach($results['Category_Count'] as $columnToPlot => $tempValue){
		arsort($results['Category_Count'][$columnToPlot]);
	}
	
	$results['DataCount_WihoutConditions_Display']	= number_format($results['DataCount_WihoutConditions']);
	$results['DataCount_Display'] 					= number_format($results['DataCount']);
	if ($results['DataCount'] == 0){
		return false;	
	}

	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
}


function prepareGeneExpressionDataByGeneNames($geneNames = '', $columnToPlots = '', $valueTable = 'GeneFPKM', $valueColumn = 'FPKM', $searchOption = 0, $additionalMaterial = '', $additionalMaterial2 = '', $dataSource = array(), $internalProjectIndexes = array(), $otherOptions = array()){
	
	global $APP_CONFIG;
	
	$geneNames = array_clean($geneNames);
	
	if (array_size($geneNames) <= 0) return false;
	
	$searchOption = intval($searchOption);
	if (($searchOption < 0) || ($searchOption > 2)){
		$searchOption = 0;	
	}
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	
	$cacheKey = __FUNCTION__ . '::' . 
				md5(json_encode($geneNames) . '::' . 
					json_encode($columnToPlots) . '::' . 
					$valueTable . '::' . 
					$valueColumn . '::' . 
					$searchOption . '::' . 
					json_encode($additionalMaterial) . '::' . 
					json_encode($additionalMaterial2) . '::' . 
					json_encode($dataSource) . '::' . 
					json_encode($internalProjectIndexes) . '::' . 
					json_encode($otherOptions) . '::' . 
					json_encode(gene_uses_TPM()) . '::' . 
					$version
					);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	if ($searchOption == 0){
		//Data Filter
		$dataFilter = &$additionalMaterial;
		

		foreach($dataFilter as $currentColumn => $values){
			if (strpos($currentColumn, 'Custom_') === 0){
				$dataFilterHasCustomerColumn = 1;
				
				$column_real = $flexibleColumnSummary['By-Column'][$currentColumn]['SQL'];

				if ($column_real == '') continue;
				
				$results['Summary']['Column_Title'][$currentColumn] 	= $flexibleColumnSummary['By-Column'][$currentColumn]['Title'];
				$results['Summary']['Column_Mapping'][$currentColumn] 	= $column_real;
				
			}
		}
		
	} elseif ($searchOption == 1){
		//Comparison
		//public data source only, private will be disabled
		
		$comparisonIDList = array_clean($additionalMaterial);
		
		
		
		if (array_size($comparisonIDList) > 0){

			
			$columnToPlotComparisonArray	= array_clean($additionalMaterial2);
			
			foreach($columnToPlotComparisonArray as $tempKeyX => $currentColumn){
				if ($APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'] != ''){
					$columnToPlotComparisonArrayNative[$currentColumn] = $currentColumn;
				} else {
					$columnToPlotComparisonArrayNonNative[$currentColumn] = $currentColumn;
				}
			}
			
			
			
			$columnToPlotComparisonString	= implode(', ', $columnToPlotComparisonArrayNative);
	
			if ($columnToPlotComparisonString != ''){
				$columnToPlotComparisonString = ", {$columnToPlotComparisonString}";	
			}
			
			$results['ComparisonID']['Input'] = $comparisonIDList;
			
			unset($comparisonIDString);
			foreach($comparisonIDList as $tempKey => $tempValue){
				$comparisonIDString[] = "'" . addslashes($tempValue) . "'";
			}
			
			$comparisonIDString = implode(',', $comparisonIDString);
			
			$sql_table 			= 'Comparisons';
			$sql_column 		= 'ComparisonID';
			
			$comparisonResult	= search_comparisons($comparisonIDList, "ComparisonID, ComparisonIndex, Case_SampleIDs, Control_SampleIDs {$columnToPlotComparisonString}", $dataSource, $internalProjectIndexes, 'GetArray');
			
			
			if (array_size($comparisonResult) > 0){
				$results['ComparisonID']['Output'] = array_column($comparisonResult, 'ComparisonID');
				
				$comparisonIndexList	= array_column($comparisonResult, 'ComparisonIndex');
				$sampleIDList1			= array_column($comparisonResult, 'Case_SampleIDs');
				$sampleIDList2			= array_column($comparisonResult, 'Control_SampleIDs');
				$sampleIDList			= array_clean(explode(';', implode(';', $sampleIDList1) . ';' . implode(';', $sampleIDList2)));
				
				if (array_size($columnToPlotComparisonArray) > 0){
					
					$needToCombineSampleComparison = true;
					
					foreach($comparisonResult as $tempKey1 => $currentComparison){
						
						$currentSampleIDs = array_clean(explode(';', $currentComparison['Case_SampleIDs'] . ';' . $currentComparison['Control_SampleIDs']));
						
						$currentSampleID_Case 		= array_clean(explode(';', $currentComparison['Case_SampleIDs']));
						$currentSampleID_Control 	= array_clean(explode(';', $currentComparison['Control_SampleIDs']));

						foreach($currentSampleIDs as $tempKey2 => $currentSampleID){
							
							$sampleIDToComparisonIndex[$currentSampleID][$currentComparison['ComparisonIndex']] = $currentComparison['ComparisonID'];
							$sampleID_ComparisonID = "{$currentSampleID}|{$currentComparison['ComparisonID']}";
							
							foreach($columnToPlotComparisonArray as $tempKey3 => $currentComparisonColumn){
								
								if (isset($currentComparison[$currentComparisonColumn])){
									$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = $currentComparison[$currentComparisonColumn];
								} else {
									
									if ($currentComparisonColumn == 'ComparisonID_Type'){
										
										if (in_array($currentSampleID, $currentSampleID_Case)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = $currentComparison['ComparisonID'] . '_Case';
											
											
										} elseif (in_array($currentSampleID, $currentSampleID_Control)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = $currentComparison['ComparisonID'] . '_Control';
											
										}
									}
									
									if ($currentComparisonColumn == 'Comparison_Type'){
										
										if (in_array($currentSampleID, $currentSampleID_Case)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = 'Case';

										} elseif (in_array($currentSampleID, $currentSampleID_Control)){
											$sampleComparisonAttributes[$sampleID_ComparisonID][$currentComparisonColumn][] = 'Control';	

										}
									}
									
								}
							}
							
						}
					}
				}
				
				
			} else {
				$dataFilter 	= '';
				$results['ComparisonID']['Output'] = array();
			}
			
		}

	} else {
		$sampleIDList = array_clean($additionalMaterial);
	}

	
	
	//*******************
	// Search Gene Index
	//*******************
	$geneIndexes = searchGeneIndexes($geneNames);

	if (array_size($geneIndexes) <= 0){
		return false;	
	}
	$geneIndex2geneName = array_flip($geneIndexes);

	
	//*******************
	// Search GeneFPKM
	//*******************
	$geneIndexString = implode(', ', $geneIndexes);
	
	
	if ($searchOption == 0){
		//default, using data filter
		$sql_table 					= $valueTable;
		$sql_column 				= 'GeneIndex';
		$SQL 						= "SELECT * FROM {$sql_table} WHERE {$sql_column} IN ({$geneIndexString}) ORDER BY FIELD(GeneIndex, {$geneIndexString})";
		
		unset($geneExpressionValue);
		if ($dataSource['public'] != ''){
			$geneExpressionValue	= tabix_search_records_with_index($geneIndexes, '', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			foreach($internalProjectIndexes as $tempKey => $projectIndex){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, '', $sql_table);
				
				foreach($temp as $tempKeyX => $tempValueX){
					$geneExpressionValue[] = $tempValueX;
				}
				
				unset($temp);
			}
		}
	} else {
		//Search using sample IDs
		unset($sampleIDString);
		
		if (array_size($sampleIDList) > 0){
			foreach($sampleIDList as $tempKey => $tempValue){
				$sampleIDString[] = "'" . addslashes($tempValue) . "'";
			}
			$sampleIDString 		= implode(', ', $sampleIDString);
			
			
			
			$results['SampleID']['Input'] = array_combine($sampleIDList, $sampleIDList);
			
			$geneExpressionValue = array();
			unset($sampleIndexes);
			if ($dataSource['public'] != ''){
				$sql_table 				= 'Samples';
				$sql_column 			= 'SampleID';
				$SQL 					= "SELECT SampleIndex FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIDString})";
				$sampleIndexes			= getSQL($SQL, 'GetCol', $sql_table);
				
				if (array_size($sampleIndexes) > 0){
					$sql_table 				= $valueTable;
					$geneExpressionValue	= tabix_search_records_with_index($geneIndexes, $sampleIndexes, $sql_table);
				}
			}
			
			
			if ($dataSource['private'] != ''){
				$sql_table 				= 'App_User_Data_Samples';
				$sql_column 			= 'SampleID';
				$SQL 					= "SELECT SampleIndex FROM {$sql_table} WHERE ({$sql_column} IN ({$sampleIDString})) AND (`ProjectIndex` IN ({$internalProjectIndexString}))";
				$sampleIndexesPrivate	= getSQL($SQL, 'GetCol', $sql_table);
				
				foreach($sampleIndexesPrivate as $tempKeyX => $tempValueX){
					$sampleIndexes[] = $tempValueX;
				}
				
				
				$sql_table 				= $valueTable;
				foreach($internalProjectIndexes as $tempKey => $projectIndex){
					$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $sampleIndexesPrivate, $sql_table);
					
					foreach($temp as $tempKeyX => $tempValueX){
						$geneExpressionValue[] = $tempValueX;
					}
					
					unset($temp);
				}

			}
		} else {
			$sql_table 				= $valueTable;
			
			unset($geneExpressionValue);
			if ($dataSource['public'] != ''){
				$geneExpressionValue	= tabix_search_records_with_index($geneIndexes, '', $sql_table);
			}
			
			if ($dataSource['private'] != ''){
				foreach($internalProjectIndexes as $tempKey => $projectIndex){
					$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, '', $sql_table);
					
					foreach($temp as $tempKeyX => $tempValueX){
						$geneExpressionValue[] = $tempValueX;
					}
					
					unset($temp);
				}
			}
		}

	}

	
	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	
	//*******************
	// Search Samples
	//*******************
	$sampleIndexes		= array_column($geneExpressionValue, 'SampleIndex');
	$sampleIndexes		= array_clean($sampleIndexes);
	
	$sampleIndexString 	= implode(', ', $sampleIndexes);
	$columnToPlotString	= implode(', ', $columnToPlots);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}

	$sql_table 		= 'Samples';
	$sql_column 	= 'SampleIndex';
	
	unset($searchConditions);
	if ($searchOption == 0){
		foreach($dataFilter as $currentColumn => $values){
			
			unset($valueString);
			foreach($values as $tempKey => $tempValue){
				$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
			}
			
			$valueString		= implode(", ", $valueString);
			$searchConditions[$currentColumn] = "({$currentColumn} IN ({$valueString}))";
		}
		
		if ($otherOptions['Modified_DiseaseState_Enable']){
			if (array_size($dataFilter['DiseaseState']) > 0){
				//Data Filter may contain the value from the mapping table
				unset($valueString);
				foreach($dataFilter['DiseaseState'] as $tempKey => $tempValue){
					$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
				}
					
				$valueString		= implode(", ", $valueString);
				
				if ($otherOptions['Modified_DiseaseState_Display_Abbreviation']){
					$specialSearchConditions = "(`Comparison_Disease_State_Abbreviation` IN ({$valueString}))";
				} else {
					$specialSearchConditions = "(`Comparison_Disease_State` IN ({$valueString}))";
				}
				
				if ($specialSearchConditions != ''){
					$SQL = "SELECT `SampleIndex` FROM `{$APP_CONFIG['Table']['App_Sample_Comparison_DiseaseState']}` WHERE ({$specialSearchConditions})";
					$specialSpecialIndexes = getSQL($SQL, 'GetCol', $sql_table);
					$specialSpecialIndexes = array_clean($specialSpecialIndexes);
				}
				
				if (array_size($specialSpecialIndexes) > 0){
					$specialSpecialIndexes = implode(',', $specialSpecialIndexes);
					$searchConditions['DiseaseState'] = "(({$searchConditions['DiseaseState']}) OR (`SampleIndex` IN ({$specialSpecialIndexes})))";
				}
			}
		}
		
		$searchConditions = implode(' AND ', $searchConditions);
	}
	
	unset($sampleSources);
	if ($dataSource['public'] != ''){
		if ($searchConditions != ''){
			$SQL 		= "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE ({$sql_column} IN ({$sampleIndexString})) AND ({$searchConditions})";
		} else {
			$SQL 		= "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIndexString})";	
		}
		$sampleSources	= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	
	
	
	
	if ($dataSource['private'] != ''){

		unset($searchConditionsPrivate);		
		if ($searchOption == 0){
			//DataFilter
			
			if ($dataFilterHasCustomerColumn){
				
				foreach($dataFilter as $currentColumn => $values){
					

					if (strpos($currentColumn, 'Custom_') === 0){
						$column_info = internal_data_get_column($internalProjectIndexes, 'samples', $currentColumn);
						
						
						
						$column_real = $flexibleColumnSummary['By-Column'][$currentColumn]['SQL'];

						if ($column_real == '') continue;
						
						$results['Summary']['Column_Title'][$currentColumn] 	= $flexibleColumnSummary['By-Column'][$currentColumn]['Title'];
						$results['Summary']['Column_Mapping'][$currentColumn] 	= $column_real;
						
						unset($valueString);
						foreach($values as $tempKey => $tempValue){
							$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
						}
						
						$valueString		= implode(", ", $valueString);
						$searchConditionsPrivate[] = "((`{$column_real}` IN ({$valueString})) AND (`ProjectIndex` = '{$column_info['ProjectIndex']}'))";
						
						
					} else {
						$column_real = $currentColumn;
						
						if (!isset($APP_CONFIG['Internal_Data']['Samples']['Headers'][$currentColumn])) continue;

						
						unset($valueString);
						foreach($values as $tempKey => $tempValue){
							$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
						}
						
						$valueString		= implode(", ", $valueString);
						$searchConditionsPrivate[] = "({$currentColumn} IN ({$valueString}))";
						
					}

				}
			} else {
			
				foreach($dataFilter as $currentColumn => $values){
					
					if (!isset($APP_CONFIG['Internal_Data']['Samples']['Headers'][$currentColumn])) continue;
					
					unset($valueString);
					foreach($values as $tempKey => $tempValue){
						$valueString[] = "'" . addslashes(trim($tempValue)) . "'";
					}
					
					$valueString		= implode(", ", $valueString);
					$searchConditionsPrivate[] = "({$currentColumn} IN ({$valueString}))";
				}
				
			}

			$searchConditionsPrivate = implode(' AND ', $searchConditionsPrivate);
			
		} elseif ($searchOption == 2){
			
			if ($sampleIndexString != ''){
				$sql_column 			 = 'SampleIndex';
				$searchConditionsPrivate = 	"({$sql_column} IN ({$sampleIndexString}))";
			}
			
		}
		
		if ($searchConditionsPrivate == ''){
			$SQL 		= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString})";
		} else {
			$SQL 		= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString}) AND ({$searchConditionsPrivate})";
		}
		

		$temp = getSQL($SQL, 'GetAssoc', 'App_User_Data_Samples');
		
		
		foreach($temp as $tempKeyX => $tempValueX){
			$sampleSources[$tempKeyX] = internal_data_transform_one_data('Samples', $tempValueX);
		}
			
		unset($temp);
	}
	
	

	if (array_size($sampleSources) <= 0){
		return false;
	} else {
		$results['DataCount_WihoutConditions'] = array_size($geneExpressionValue);
		

		if ($otherOptions['Modified_DiseaseState_Enable']){
			
			if ($searchOption == 0){
				//Data Filter
				$sampleSources = modify_sample_disease_state_values($sampleSources, 
																	$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
																	array(),
																	$dataFilter['DiseaseState'],
																	$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
																	1,
																	$modify_sample_disease_state_values_executed
																	);				
			} elseif ($searchOption == 1){
				//Comparison	
				$sampleSources = modify_sample_disease_state_values($sampleSources, 
																	$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
																	$comparisonIndexList,
																	array(),
																	$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
																	1,
																	$modify_sample_disease_state_values_executed
																	);
			} else {
				//Sample ID
				$sampleSources = modify_sample_disease_state_values($sampleSources, 
																	$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
																	array(),
																	array(),
																	$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
																	1,
																	$modify_sample_disease_state_values_executed
																	);
			}
			
			if ($modify_sample_disease_state_values_executed){
				$modify_sample_disease_state_values = true;
				$hasTransformed = true;
			} else {
				$otherOptions['Modified_DiseaseState_Enable'] = false;	
				$modify_sample_disease_state_values = false;
				$hasTransformed = false;
			}
			
		} else {
				$otherOptions['Modified_DiseaseState_Enable'] = false;	
				$modify_sample_disease_state_values = false;
				$hasTransformed = false;
		}

	}
	
	
	
	
	if (array_size($columnToPlotComparisonArray) > 0){
		$hasTransformed = true;
		
		if (!$modify_sample_disease_state_values){
			
			foreach($sampleSources as $currentSampleIndex => $currentSampleInfo){
				unset($currentTemp);
				
				$currentSampleID = $currentSampleInfo['SampleID'];
				
				foreach($sampleIDToComparisonIndex[$currentSampleID] as $currentComparisonIndex => $currentComparisonID){
					
					$temp = $currentSampleInfo;
					$temp['SampleID_Org'] 		= $currentSampleID;
					$temp['SampleID'] 			= "{$currentSampleID}|{$currentComparisonID}";
					$temp['ComparisonIndex'] 	= $currentComparisonIndex;

					$currentTemp[] = $temp;
				}

				$sampleSources[$currentSampleIndex] = $currentTemp;
				
			}
			
			
		} else {
			
			foreach($sampleSources as $currentSampleIndex => $currentSamples){
				
				foreach($currentSamples as $tempKey => $currentSampleInfo){
					
					$currentSampleID		= $currentSampleInfo['SampleID'];
					$currentComparisonIndex	= $currentSampleInfo['ComparisonIndex'];
					$currentComparisonID 	= $sampleIDToComparisonIndex[$currentSampleID][$currentComparisonIndex];
					
					$sampleSources[$currentSampleIndex][$tempKey]['SampleID'] = "{$currentSampleID}|{$currentComparisonID}";
					$sampleSources[$currentSampleIndex][$tempKey]['SampleID_Org'] = $currentSampleID;
					
				}
				
			}
		}
	}

	
	
	

	unset($currentIndex, $currentIndexUnique);
	
	
	
	$transformedIndex = array();
	
	foreach($geneExpressionValue as $geneExpressionKey => $geneExpressionArray){
		
		$currentSampleIndex = $geneExpressionArray['SampleIndex'];
		
		if (isset($sampleSources[$currentSampleIndex])){
			
			
			
			if (!$hasTransformed){
				
				if (!isset($transformedIndex[$currentSampleIndex])){
					$temp = $sampleSources[$currentSampleIndex];
					
					unset($sampleSources[$currentSampleIndex]);
					
					$sampleSources[$currentSampleIndex][0] = $temp;
					
					$transformedIndex[$currentSampleIndex] = 1;
				}
			}
			

			
			foreach($sampleSources[$currentSampleIndex] as $currentSampleIndexX => $currentSampleRecord){
				
				$currentSampleIndex2 = $currentSampleIndex . '-' . $currentSampleIndexX;
				
				$currentIndexUnique++;
				
				$currentIndex 		= "{$currentSampleRecord['SampleID']}_{$currentIndexUnique}";
				
				$currentGeneName	= $geneIndex2geneName[$geneExpressionArray['GeneIndex']];
				
				$results['DataCount']++;
				
				$results['canvasxpress']['y']['smps'][$currentIndex] 
					= '"' . sanitizeJavaScriptValue($currentIndex) . '"';
				
				$otherOptions['transform_value'] = floatval($otherOptions['transform_value']);
				
				$results['canvasxpress']['y']['data'][$currentIndex] 
						= floatval($geneExpressionArray[$valueColumn]) + $otherOptions['transform_value'];
				

				if (!isset($results['Export']['Raw']['Headers']['SampleID'])){
					$results['Export']['Raw']['Headers']['SampleID'] 			= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
					$results['Export']['Transformed']['Headers']['SampleID'] 	= $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
				}
				
				$results['Export']['Raw']['Body'][$currentSampleIndex2]['SampleID'] 		= $currentSampleRecord['SampleID'];
				$results['Export']['Transformed']['Body'][$currentSampleIndex2]['SampleID'] = $currentSampleRecord['SampleID'];
				$results['SampleID']['Output'][$currentSampleRecord['SampleID']]			= $currentSampleRecord['SampleID'];

				
				$columnToPlot = 'Gene Symbol';
				$results['Category_Count'][$columnToPlot][$currentGeneName]++;
				$results['canvasxpress']['x'][$columnToPlot][$currentIndex] = '"' . sanitizeJavaScriptValue($currentGeneName) . '"';
				

				
				
				if (array_size($columnToPlotComparisonArrayNative) > 0){
					
					foreach($columnToPlotComparisonArrayNative as $tempKey3 => $currentComparisonColumn){
						
						if (!isset($results['Export']['Raw']['Headers'][$currentComparisonColumn])){
							$results['Export']['Raw']['Headers'][$currentComparisonColumn]
								= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentComparisonColumn]['Title'];
							$results['Export']['Transformed']['Headers'][$currentComparisonColumn] 	
								= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentComparisonColumn]['Title'];
						}
						
						$currentSampleID = $currentSampleRecord['SampleID'];
						
						$categoryToPlot	= array_clean($sampleComparisonAttributes[$currentSampleID][$currentComparisonColumn], 0, 1, 1, 0);
						$categoryToPlot	= ucwords2(trim(implode('; ', $categoryToPlot)));
						if ($categoryToPlot	== ''){
							$categoryToPlot	= $APP_CONFIG['Blank_Value'];
						}
						
						$results['Category_Count'][$currentComparisonColumn][$categoryToPlot]++;
						
						$results['canvasxpress']['x'][$currentComparisonColumn][$currentIndex] 
							= '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
						
						$results['Export']['Raw']['Body'][$currentSampleIndex2][$currentComparisonColumn] 			= $categoryToPlot;
						$results['Export']['Transformed']['Body'][$currentSampleIndex2][$currentComparisonColumn] 	= $categoryToPlot;
					}
				}

				if (array_size($columnToPlotComparisonArrayNonNative) > 0){
					
					foreach($columnToPlotComparisonArrayNonNative as $tempKey3 => $currentComparisonColumn){
						
						if (!isset($results['Export']['Raw']['Headers'][$currentComparisonColumn])){
							$results['Export']['Raw']['Headers'][$currentComparisonColumn]
								= $APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'][$currentComparisonColumn];
							$results['Export']['Transformed']['Headers'][$currentComparisonColumn] 	
								= $APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'][$currentComparisonColumn];
						}
						
						$currentSampleID = $currentSampleRecord['SampleID'];
						
						
						
						$categoryToPlot	= array_clean($sampleComparisonAttributes[$currentSampleID][$currentComparisonColumn], 0, 1, 1, 0);
						$categoryToPlot	= ucwords2(trim(implode('; ', $categoryToPlot)));
						if ($categoryToPlot	== ''){
							$categoryToPlot	= $APP_CONFIG['Blank_Value'];
						}
						
						$results['Category_Count'][$currentComparisonColumn][$categoryToPlot]++;
						
						$results['canvasxpress']['x'][$currentComparisonColumn][$currentIndex] 
							= '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
						
						$results['Export']['Raw']['Body'][$currentSampleIndex2][$currentComparisonColumn] = $categoryToPlot;
						$results['Export']['Transformed']['Body'][$currentSampleIndex2][$currentComparisonColumn] = $categoryToPlot;
					}
				}
				
				foreach($columnToPlots as $tempKey2 => $columnToPlot){
					
					if (!isset($results['Export']['Raw']['Headers'][$columnToPlot])){
						
						$currentColumnTitle = $flexibleColumnSummary['By-Column'][$columnToPlot]['Title'];
						
						if ($currentColumnTitle == ''){
							$currentColumnTitle = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$columnToPlot]['Title'];
						}
						
						
						$results['Export']['Raw']['Headers'][$columnToPlot] = $currentColumnTitle;
						$results['Export']['Transformed']['Headers'][$columnToPlot] = $currentColumnTitle;
					}
					
					$categoryToPlot		= ucwords2(trim($currentSampleRecord[$columnToPlot]));
					if ($categoryToPlot	== ''){
						$categoryToPlot	= $APP_CONFIG['Blank_Value'];	
					}
					
					$results['Category_Count'][$columnToPlot][$categoryToPlot]++;
	
					$results['canvasxpress']['x'][$columnToPlot][$currentIndex] 
						= '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
					
					$results['Export']['Raw']['Body'][$currentSampleIndex2][$columnToPlot] 			= $categoryToPlot;
					$results['Export']['Transformed']['Body'][$currentSampleIndex2][$columnToPlot] 	= $categoryToPlot;
					
	
				}
				
				if (!isset($results['Export']['Raw']['Headers'][$currentGeneName])){
					$results['Export']['Raw']['Headers'][$currentGeneName] 			= $currentGeneName;
					$results['Export']['Transformed']['Headers'][$currentGeneName] 	= $currentGeneName;
				}
				
				foreach($geneIndex2geneName as $tempGeneIndex => $tempGeneName){
					
					if (!isset($results['Export']['Raw']['Body'][$currentSampleIndex2][$tempGeneName])){
						$results['Export']['Raw']['Body'][$currentSampleIndex2][$tempGeneName] = '';
					}
					
					if (!isset($results['Export']['Transformed']['Body'][$currentSampleIndex2][$tempGeneName])){
						$results['Export']['Transformed']['Body'][$currentSampleIndex2][$tempGeneName] = '';
					}

				}
				
				
				$results['Export']['Raw']['Body'][$currentSampleIndex2][$currentGeneName]			= $geneExpressionArray[$valueColumn];
				
				if ($otherOptions['transform']){
					$results['Export']['Transformed']['Body'][$currentSampleIndex2][$currentGeneName] 	= log2($results['canvasxpress']['y']['data'][$currentIndex]);
				} else {
					$results['Export']['Transformed']['Body'][$currentSampleIndex2][$currentGeneName] 	= $results['canvasxpress']['y']['data'][$currentIndex];
				}

			}
			
		}
	}


	if ($searchOption == 1){
		
		$results['ComparisonID']['Input_Count'] 			= array_size($results['ComparisonID']['Input']);
		$results['ComparisonID']['Input_Count_Display'] 	= number_format($results['ComparisonID']['Input_Count']);
		
		$results['ComparisonID']['Output_Count'] 			= array_size($results['ComparisonID']['Output']);
		$results['ComparisonID']['Output_Count_Display'] 	= number_format($results['ComparisonID']['Output_Count']);
		
		if ($results['ComparisonID']['Input_Count'] != $results['ComparisonID']['Output_Count']){
			
			$results['ComparisonID']['Missing'] 				= array_diff($results['ComparisonID']['Input'], $results['ComparisonID']['Output']);
			$results['ComparisonID']['Missing_Count'] 			= array_size($results['ComparisonID']['Missing']);
			$results['ComparisonID']['Missing_Count_Display'] 	= number_format($results['ComparisonID']['Missing_Count']);
		}
	}
	
	
	if ($searchOption == 2){
		
		$results['SampleID']['Input_Count'] 					= array_size($results['SampleID']['Input']);
		$results['SampleID']['Input_Count_Display'] 			= number_format($results['SampleID']['Input_Count']);
		
		$results['SampleID']['Output_Count'] 					= array_size($results['SampleID']['Output']);
		$results['SampleID']['Output_Count_Display'] 			= number_format($results['SampleID']['Output_Count']);
		
		if ($results['SampleID']['Input_Count'] != $results['SampleID']['Output_Count']){
			
			$results['SampleID']['Missing'] 				= array_diff($results['SampleID']['Input'], $results['SampleID']['Output']);
			$results['SampleID']['Missing_Count'] 			= array_size($results['SampleID']['Missing']);
			$results['SampleID']['Missing_Count_Display'] 	= number_format($results['SampleID']['Missing_Count']);
			
		}
	}
	
	
	foreach($results['Category_Count'] as $columnToPlot => $tempValue){
		arsort($results['Category_Count'][$columnToPlot]);
	}
	
	$results['DataCount_WihoutConditions_Display']	= number_format($results['DataCount_WihoutConditions']);
	$results['DataCount_Display'] 					= number_format($results['DataCount']);
	if ($results['DataCount'] == 0){
		return false;	
	}

	putSQLCache($cacheKey, $results, '', __FUNCTION__);


	return $results;
	
}


function prepareHeatMapData($geneIndexes, $inputIndexes, $columnToPlots, $platformType, $otherOptions, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG;

	$geneIndexes 	= array_clean($geneIndexes);	
	$columnToPlots 	= array_clean($columnToPlots);
	$platformType	= trim($platformType);
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($geneIndexes) . '::' . 
										json_encode($inputIndexes) . '::' . 
										json_encode($columnToPlots) . '::' . 
										$platformType . '::' . 
										json_encode($otherOptions) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode(gene_uses_TPM()) . '::' . 
										$version
										);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	$searchOption = intval($otherOptions['searchOption']);
	
	if ($searchOption != 1){
		$searchOption = 0;	
	}
	
	if ($dataSource['private'] != ''){
		$searchOption = 0;
	}
	
	if ($searchOption == 0){
		$sampleIndexes = &$inputIndexes;
	} else {
		//Comparison
		$comparisonIndexes = &$inputIndexes;
		
		if (array_size($comparisonIndexes) > 0){

			
			$columnToPlotComparisonArray	= array_clean($otherOptions['Plot_Columns_Comparison']);
			$columnToPlotComparisonString	= implode(', ', $columnToPlotComparisonArray);
	
			if ($columnToPlotComparisonString != ''){
				$columnToPlotComparisonString = ", {$columnToPlotComparisonString}";	
			}
			
			$results['ComparisonIndex']['Input'] = $comparisonIndexes;
		
			$comparisonIndexString = implode(',', $comparisonIndexes);
			
			$sql_table 			= 'Comparisons';
			$sql_column 		= 'ComparisonIndex';
			$SQL 				= "SELECT ComparisonID, ComparisonIndex, Case_SampleIDs, Control_SampleIDs {$columnToPlotComparisonString} FROM {$sql_table} WHERE {$sql_column} IN ({$comparisonIndexString})";

			$comparisonResult	= getSQL($SQL, 'GetArray', $sql_table);
			
			if (array_size($comparisonResult) > 0){
				$results['ComparisonIndex']['Output'] = array_column($comparisonResult, 'ComparisonIndex');
				
				$comparisonIndexList	= array_column($comparisonResult, 'ComparisonIndex');
				$sampleIDList1			= array_column($comparisonResult, 'Case_SampleIDs');
				$sampleIDList2			= array_column($comparisonResult, 'Control_SampleIDs');
				$sampleIDList			= array_clean(explode(';', implode(';', $sampleIDList1) . ';' . implode(';', $sampleIDList2)));
				
				if ($columnToPlotComparisonString != ''){
					
					foreach($comparisonResult as $tempKey1 => $currentComparison){
						
						$currentSampleIDs = array_clean(explode(';', $currentComparison['Case_SampleIDs'] . ';' . $currentComparison['Control_SampleIDs']));
					
						foreach($currentSampleIDs as $tempKey2 => $currentSampleID){
							
							foreach($columnToPlotComparisonArray as $tempKey3 => $currentComparisonColumn){
								
								$sampleComparisonAttributes[$currentSampleID][$currentComparisonColumn][] = $currentComparison[$currentComparisonColumn];
								
							}
							
						}
					}
				}
				
				
				
				if (array_size($sampleIDList) > 0){
					unset($sampleIDString);
					foreach($sampleIDList as $tempKey => $tempValue){
						$sampleIDString[] = "'" . addslashes($tempValue) . "'";
					}
					$sampleIDString 		= implode(', ', $sampleIDString);
					
					$sql_table 				= 'Samples';
					$sql_column 			= 'SampleID';
					$SQL 					= "SELECT SampleIndex FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIDString})";
					$sampleIndexes			= getSQL($SQL, 'GetCol', $sql_table);
				} else {
					return false;	
				}
				
				
				
			} else {
				$dataFilter 	= '';
				$results['ComparisonIndex']['Output'] = array();
			}
		
		}
		
	}
	
	
	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'GeneCombined';
	$sql_column 	= 'GeneName';
	
	$valueString 	= implode(', ', $geneIndexes);
	$SQL 			= "SELECT GeneIndex, GeneName FROM {$sql_table} WHERE GeneIndex IN ({$valueString}) ORDER BY FIELD(GeneIndex, {$valueString})";
	$geneInfo		= getSQL($SQL, 'GetAssoc', $sql_table);

	if (array_size($geneInfo) <= 0){
		return false;	
	}
	
	//*******************
	// Search GeneFPKM
	//*******************
	$geneIndexString 		= implode(', ', $geneIndexes);
	$sampleIndexString 		= implode(', ', $sampleIndexes);
	$sampleIndexStringOrg 	= $sampleIndexString;
	
	if ($platformType == 'RNA-Seq'){
		$sql_table	= 'GeneFPKM';
		$sql_column	= 'FPKM';
		$valueColumn= 'FPKM';
	} elseif ($platformType == 'Microarray'){
		$sql_table 	= 'GeneLevelExpression';
		$sql_column	= 'Value';
		$valueColumn= 'Value';
	}
	//$SQL 		= "SELECT GeneIndex, SampleIndex, {$sql_column} FROM {$sql_table} WHERE (GeneIndex IN ({$geneIndexString})) AND (SampleIndex IN ({$sampleIndexString}))";
	unset($geneExpressionValue);
	
	if ($dataSource['public'] != ''){
		if (array_size($sampleIndexes) > 0){
			$geneExpressionValue = tabix_search_records_with_index($geneIndexes, $sampleIndexes, $sql_table);
		}
	}
	
	if ($dataSource['private'] != ''){
		foreach($internalProjectIndexes as $tempKey => $projectIndex){
			$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $sampleIndexes, $sql_table);
			
			foreach($temp as $tempKeyX => $tempValueX){
				$geneExpressionValue[] = $tempValueX;
			}
			
			unset($temp);
		}
	}
	
	

	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	$columnToPlotString	= implode(', ', $columnToPlots);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$sampleIndexString = array_clean(array_column($geneExpressionValue, 'SampleIndex'));
	$sampleIndexString = implode(',', $sampleIndexString);
	
	unset($samples);
	if ($dataSource['public'] != ''){
		$sql_table	= 'Samples';
		$SQL 		= "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE SampleIndex IN ({$sampleIndexString}) ORDER BY FIELD(SampleIndex, {$sampleIndexStringOrg})";

		$samples	= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	
	if ($dataSource['private'] != ''){
		$sql_table	= 'App_User_Data_Samples';
		$SQL 		= "SELECT * FROM {$sql_table} WHERE SampleIndex IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(SampleIndex, {$sampleIndexStringOrg})";	

		$temp	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		foreach($temp as $tempKeyX => $tempValueX){
			$samples[$tempKeyX] = internal_data_transform_one_data('Samples', $tempValueX);
		}
	}
	
	
	if ($otherOptions['Modified_DiseaseState_Enable']){
		
		if ($searchOption == 1){
			//Comparison	
			$samples = modify_sample_disease_state_values($samples, 
															$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
															$comparisonIndexList,
															array(),
															$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
															1,
															$modify_sample_disease_state_values_executed
															);
		} else {
			$samples = modify_sample_disease_state_values($samples, 
															$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
															array(),
															array(),
															$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
															1,
															$modify_sample_disease_state_values_executed
															);
		}
		
		if (!$modify_sample_disease_state_values_executed){
			$otherOptions['Modified_DiseaseState_Enable'] = false;	
		}
		
	}
	

	foreach($samples as $currentSampleIndex1 => $currentSampleRecords){
		
		if (!$otherOptions['Modified_DiseaseState_Enable']){
			$temp = $currentSampleRecords;
			unset($currentSampleRecords);
			$currentSampleRecords[0] = $temp;
		}
		
		
		foreach($currentSampleRecords as $currentSampleIndexX => $currentSampleRecord){
			
			$currentSampleIndex = "{$currentSampleIndex1}-{$currentSampleIndexX}";
		
			$results['canvasxpress']['y']['smps'][] = '"' . sanitizeJavaScriptValue($currentSampleRecord['SampleID']) . '"';
			
			$results['Export']['Raw']['Headers']['Samples'][] 			= $currentSampleRecord['SampleID'];
			$results['Export']['Transformed']['Headers']['Samples'][] 	= $currentSampleRecord['SampleID'];
			
			foreach($columnToPlots as $tempKey2 => $columnToPlot){
				$categoryToPlot		= ucwords2(trim($currentSampleRecord[$columnToPlot]));
				if ($categoryToPlot	== ''){
					$categoryToPlot	= $APP_CONFIG['Blank_Value'];	
				}
				
				$results['canvasxpress']['x'][$columnToPlot][] = '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
				
				

				
				$columnToPlotPrintable = $flexibleColumnSummary['By-Column'][$columnToPlot]['Title'];
				if ($columnToPlotPrintable == ''){
					$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$columnToPlot]['Title'];
				}
				
				
				$results['Export']['Raw']['Headers'][$columnToPlotPrintable][] 			= $categoryToPlot;
				$results['Export']['Transformed']['Headers'][$columnToPlotPrintable][] 	= $categoryToPlot;
			}
			
			if ($columnToPlotComparisonString != ''){
					
				foreach($columnToPlotComparisonArray as $tempKey3 => $currentComparisonColumn){
					
					$currentSampleID = $currentSampleRecord['SampleID'];
					
					$categoryToPlot	= array_clean($sampleComparisonAttributes[$currentSampleID][$currentComparisonColumn], 0, 1, 1, 0);
					$categoryToPlot	= ucwords2(trim(implode('; ', $categoryToPlot)));
					if ($categoryToPlot	== ''){
						$categoryToPlot	= $APP_CONFIG['Blank_Value'];
					}
					
					$results['canvasxpress']['x'][$currentComparisonColumn][] = '"' . sanitizeJavaScriptValue($categoryToPlot) . '"';
					
					$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentComparisonColumn]['Title'];
					
					$results['Export']['Raw']['Headers'][$columnToPlotPrintable][] 			= $categoryToPlot;
					$results['Export']['Transformed']['Headers'][$columnToPlotPrintable][] 	= $categoryToPlot;				
	
				}
			}
		
		}
	}
	
	

	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		
		$currentGeneIndex 	= $tempValue['GeneIndex'];
		$currentSampleIndex = $tempValue['SampleIndex'];
		$currentValue		= $tempValue[$valueColumn];
		$geneExpressionValueIndex[$currentGeneIndex][$currentSampleIndex] = $currentValue;
	}
	
	$currentIndex = -1;
	
	
	$otherOptions['transform_value'] = abs(floatval($otherOptions['transform_value']));
	
	
	foreach($geneInfo as $currentGeneIndex => $currentGeneName){
		
		$currentIndex++;

		foreach($samples as $currentSampleIndex => $currentSampleRecords){
			
			$results['Summary']['Count']['DataPoint']++;
			
			if (!$otherOptions['Modified_DiseaseState_Enable']){
				$temp = $currentSampleRecords;
				unset($currentSampleRecords);
				$currentSampleRecords[0] = $temp;
			}
		
		
			foreach($currentSampleRecords as $currentSampleIndexX => $currentSampleRecord){
			
				if (!isset($results['Summary']['Samples'][$currentSampleIndex])){
					$results['Summary']['SampleIndex'][$currentSampleIndex] = $currentSampleRecord['SampleID'];
				}
				
				
				if (isset($geneExpressionValueIndex[$currentGeneIndex])){
					
					
					
					$results['Summary']['Gene'][$currentGeneIndex] = $currentGeneName;
					$results['canvasxpress']['y']['vars'][$currentGeneIndex] = '"' . sanitizeJavaScriptValue($currentGeneName) . '"';
					
					$currentValue = $geneExpressionValueIndex[$currentGeneIndex][$currentSampleIndex];
					
					if (($currentValue === '') || (is_null($currentValue))){
						$currentValue = '"NA"';
						$results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'][] = $currentValue;
						
					} else {
						$currentValue = floatval($currentValue);
						
						if (!isset($results['Summary']['HasNumericValue'])){
							$results['Summary']['HasNumericValue'] = 1;
						}
						if ($otherOptions['transform']){
							$transformedValue = log2(floatval($currentValue) + $otherOptions['transform_value']);
						} else {
							$transformedValue = $currentValue;
						}
						$results['canvasxpress']['y']['data_summary'][$currentIndex]['Numeric_Count']++;
						
						$results['canvasxpress']['y']['data_summary'][$currentIndex]['Numeric'][] = $transformedValue;
						
						$results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'][] = $transformedValue;
						
						
	
					}
					
					$results['canvasxpress']['y']['data_summary'][$currentIndex]['Raw'][]	= $currentValue;
	
					$results['Export']['Raw']['Genes'][$currentGeneName][] 					= $currentValue;
									
					if (!isset($results['Summary']['Gene_Name2Index'][$currentGeneName])){
						$results['Summary']['Gene_Name2Index'][$currentGeneName] = $currentIndex;
					}
					
					if (!isset($results['Summary']['Gene_Index2Name'][$currentIndex])){
						$results['Summary']['Gene_Index2Name'][$currentIndex] = $currentGeneName;
					}
	
				}
				
			}
		}

	}

	if ($otherOptions['zscore'] && $results['Summary']['HasNumericValue']){
		foreach($results['canvasxpress']['y']['data_summary'] as $currentIndex => $tempValue1){
			if ($results['canvasxpress']['y']['data_summary'][$currentIndex]['Numeric_Count'] > 0){
				$mean 	= calculateMean($results['canvasxpress']['y']['data_summary'][$currentIndex]['Numeric']);
				$stdev 	= calculateStdev($results['canvasxpress']['y']['data_summary'][$currentIndex]['Numeric']);
				
				foreach($results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'] as $tempKey => $tempValue){
					if ($tempValue != '"NA"'){
						$results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'][$tempKey] = calculateZScore($tempValue, $mean, $stdev);
					}
				}
			}
		}
	}
	
	if ($otherOptions['limit_enable'] && $results['Summary']['HasNumericValue']){
		foreach($results['canvasxpress']['y']['data_summary'] as $currentIndex => $tempValue1){
			if ($results['canvasxpress']['y']['data_summary'][$currentIndex]['Numeric_Count'] > 0){
				foreach($results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'] as $tempKey => $tempValue){
					if ($tempValue != '"NA"'){
						
						if ($otherOptions['upper_limit_enable']){
							if ($tempValue > $otherOptions['upper_limit_value']){
								$results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'][$tempKey] = $otherOptions['upper_limit_value'];
							}
						}
						
						
						if ($otherOptions['lower_limit_enable']){
							if ($tempValue < $otherOptions['lower_limit_value']){
								$results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'][$tempKey] = $otherOptions['lower_limit_value'];
							}
						}
						
					}
				}
			}
		}
	}
	
	foreach($results['canvasxpress']['y']['data_summary'] as $currentIndex => $tempValue){
		$results['canvasxpress']['y']['data'][$currentIndex] = $results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'];
		
		$currentGeneName = $results['Summary']['Gene_Index2Name'][$currentIndex];
		
		$results['Export']['Transformed']['Genes'][$currentGeneName] = $results['canvasxpress']['y']['data_summary'][$currentIndex]['Transformed'];
		$results['Summary']['Count']['Gene']++;
		
	}
	
	
	if ($platformType == 'RNA-Seq'){
		if (gene_uses_TPM()){
			$results['Summary']['Subtitle'] = 'TPM';
		} else {
			$results['Summary']['Subtitle'] = 'Value';
		}
	} else {
		$results['Summary']['Subtitle'] = 'Value';
	}
	
	if ($otherOptions['transform']){
		
		if ($otherOptions['transform_value'] > 0){
			$results['Summary']['Subtitle'] = "log2({$results['Summary']['Subtitle']} + {$otherOptions['transform_value']})";
		} else {
			$results['Summary']['Subtitle'] = "log2({$results['Summary']['Subtitle']})";
		}
	}
	
	if ($otherOptions['zscore']){
		$results['Summary']['Subtitle'] = "Z-Score from {$results['Summary']['Subtitle']}";
	}
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}


function getGeneNameExistenceInfo($geneNames){
	
	global $APP_CONFIG;
	
	if (!is_array($geneNames)){
		$geneNames = array(0 => $geneNames);
	}
	
	$geneNames = array_clean($geneNames);
	
	if (array_size($geneNames) <= 0) return false;
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(json_encode($geneNames) . '::' . $version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;	
	}
	
	$geneIndexes = searchGeneIndexes($geneNames);
	
	if (array_size($geneIndexes) <= 0){
		return false;	
	} else {
		
		$results['Input_Count'] 	= array_size($geneNames);
		$results['Output_Count'] 	= array_size($geneIndexes);
		
		
		$results['Input'] 			= $geneNames;
		$results['Output'] 			= array_keys($geneIndexes);
	
		$results['GeneIndexes']		= array_keys(array_flip($geneIndexes));
		
		if (array_size($results['GeneIndexes']) == array_size($results['Output']) && (array_size($results['GeneIndexes']) > 0)){
			$results['GeneIndexes::GeneName'] = array_combine($results['GeneIndexes'], $results['Output']);
			
			foreach($results['GeneIndexes::GeneName'] as $geneIndex => $geneName){
				$results['GeneIndexes::GeneName'][$geneIndex] = guess_gene_name($geneName, '', 1);
			}
			
		}
		
		if ($results['Input_Count'] == $results['Output_Count']){
			$results['hasMissing']		= 0;
		} else {
			$results['hasMissing']		= 1;
			$results['Missing_Count'] 	= $results['Input_Count'] - $results['Output_Count'];
			$results['Missing']			= array_values(array_udiff($geneNames, $results['Output'], 'strcasecmp'));
		}
		
		
		putSQLCache($cacheKey, $results, '', __FUNCTION__);
		
		return $results;
		
	}
	
}


function getSampleIDsExistenceInfo($sampleIDs, $preferredPlatformType = '', $internalProjectIndexes){
	
	global $APP_CONFIG;
	
	if (!is_array($sampleIDs)){
		$sampleIDs = array(0 => $sampleIDs);
	}
	
	$sampleIDs = array_clean($sampleIDs);
	
	if (array_size($sampleIDs) <= 0) return false;
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(
							json_encode($sampleIDs) . '::' . 
							$preferredPlatformType . '::' . 
							json_encode($internalProjectIndexes) . '::' .
							$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;	
	}

	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'Samples';
	$sql_column 	= 'SampleID';
	
	unset($valueString);
	foreach($sampleIDs as $tempKey => $sampleID){
		$valueString[] = "'" . addslashes(trim($sampleID)) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	$SQL 			= "SELECT SampleIndex, SampleID, PlatformName FROM {$sql_table} WHERE {$sql_column} IN ({$valueString}) ORDER BY FIELD(SampleID, {$valueString})";
	$sampleIndexes 	= getSQL($SQL, 'GetAssoc', $sql_table);
	
	
	
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	if ($internalProjectIndexString != ''){
		$sql_table 	= 'App_User_Data_Samples';
		$SQL 		= "SELECT SampleIndex, SampleID, PlatformName FROM {$sql_table} WHERE ({$sql_column} IN ({$valueString})) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(SampleID, {$valueString})";
	
		$temp	 	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		
		
		foreach($temp as $tempKey => $tempValue){
			$sampleIndexes[$tempKey] = internal_data_transform_one_data('Samples', $tempValue);
		}
	}
	
	if (array_size($sampleIndexes) <= 0){
		return false;	
	} else {
		
		$results['Input_Count'] 	= array_size($sampleIDs);
		$results['Output_Count'] 	= array_size($sampleIndexes);
		
		
		$results['Input'] 			= $sampleIDs;
		$results['Output'] 			= array_column($sampleIndexes, 'SampleID');
		
		$results['SampleIndexes']	= array_keys($sampleIndexes);
		$results['PlatformNames']	= array_clean(array_column($sampleIndexes, 'PlatformName'));
		
		foreach($sampleIndexes as $tempKey => $tempValue){

			$platformType = getPlatformType($tempValue['PlatformName']);
			
			$results['platformSummary'][$platformType][$tempValue['PlatformName']][] = $tempValue['SampleID'];
			
			$results['platformType'] = $platformType;
			
			
			$results['byPlatformType'][$platformType][$tempKey] = $tempValue['SampleID'];
			
			$results['reversed-strtolower'][strtolower($tempValue['SampleID'])] = $tempKey;
			
			$results['Index-ID'][$tempKey] = $tempValue['SampleID'];
		}
		
		
		if ($results['Input_Count'] == $results['Output_Count']){
			$results['hasMissing']		= 0;
		} else {
			$results['hasMissing']		= 1;
			$results['Missing_Count'] 	= $results['Input_Count'] - $results['Output_Count'];
			$results['Missing']			= array_values(array_udiff($sampleIDs, $results['Output'], 'strcasecmp'));
		}


		if (array_size($results['platformSummary']) > 1){
			$results['hasMixedPlatform'] = 1;
		} else {
			$results['hasMixedPlatform'] = 0;	
		}
		
		if ($preferredPlatformType != ''){
			if (!isset($results['platformSummary'][$preferredPlatformType])){
				$results['missPreferredPlatform'] = 1;
			} else {
				$results['missPreferredPlatform'] = 0;	
			}
		}
		
		putSQLCache($cacheKey, $results, '', __FUNCTION__);
		
		return $results;
		
	}
	
}


function getComparisonIDFromIndex($comparisonIndex){
	
	$comparisonIndex = intval($comparisonIndex);
	
	return get_multiple_record('Comparison', $comparisonIndex, 'GetOne', 'ComparisonID', 0);
	

	
}


function getComparisonIDsExistenceInfo($comparisonIDs, $preferredPlatformType = '', $internalProjectIndexes, $ignoreInternalProjectIndexes = 0){
	
	global $APP_CONFIG;
	
	if (!is_array($comparisonIDs)){
		$comparisonIDs = array(0 => $comparisonIDs);
	}
	
	$comparisonIDs = array_clean($comparisonIDs);
	
	if (array_size($comparisonIDs) <= 0) return false;
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(
								json_encode($comparisonIDs) . '::' . 
								$preferredPlatformType . '::' . 
								json_encode($internalProjectIndexes) . '::' .
								$ignoreInternalProjectIndexes . '::' .
								$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;	
	}
	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'Comparisons';
	$sql_column 	= 'ComparisonID';
	
	unset($valueString);
	foreach($comparisonIDs as $tempKey => $comparisonID){
		$valueString[] = "'" . addslashes(trim($comparisonID)) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	$SQL 			= "SELECT ComparisonIndex, ComparisonID, PlatformName FROM {$sql_table} WHERE {$sql_column} IN ({$valueString}) ORDER BY FIELD(ComparisonID, {$valueString})";

	$comparisonIndexes 	= getSQL($SQL, 'GetAssoc', $sql_table);
	$results['Public_Count'] = array_size($comparisonIndexes);
	

	if ($ignoreInternalProjectIndexes){
		$internalProjectIndexString = implode(',', array_keys(internal_data_get_accessible_project()));
	} else {
		$internalProjectIndexString = implode(',', $internalProjectIndexes);
	}

	
	
	if ($internalProjectIndexString != ''){
		$sql_table 	= 'App_User_Data_Comparisons';
		$SQL 		= "SELECT ComparisonIndex, ComparisonID, PlatformName FROM {$sql_table} WHERE ({$sql_column} IN ({$valueString})) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(ComparisonID, {$valueString})";
		
		$temp	 	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		foreach($temp as $tempKey => $tempValue){
			$comparisonIndexes[$tempKey] = $tempValue;
		}
		
		$results['Private_Count'] = array_size($temp);
	}
	
	if (array_size($comparisonIndexes) <= 0){
		return false;	
	} else {
		
		$results['Input_Count'] 	= array_size($comparisonIDs);
		$results['Output_Count'] 	= array_size($comparisonIndexes);
		
		
		$results['Input'] 			= $comparisonIDs;
		$results['Output'] 			= array_column($comparisonIndexes, 'ComparisonID');
		
		$results['ComparisonIndexes']	= array_keys($comparisonIndexes);
		$results['PlatformNames']	= array_clean(array_column($comparisonIndexes, 'PlatformName'));
		
		foreach($comparisonIndexes as $tempKey => $tempValue){
			
			$platformType = getPlatformType($tempValue['PlatformName']);
			
			$results['platformSummary'][$platformType][$tempValue['PlatformName']][] = $tempValue['ComparisonID'];
			
			$results['platformType'] = $platformType;
		}
		
		
		if ($results['Input_Count'] == $results['Output_Count']){
			$results['hasMissing']		= 0;
		} else {
			$results['hasMissing']		= 1;
			$results['Missing_Count'] 	= $results['Input_Count'] - $results['Output_Count'];
			$results['Missing']			= array_values(array_udiff($comparisonIDs, $results['Output'], 'strcasecmp'));
		}
		
		if (array_size($results['ComparisonIndexes']) == array_size($results['Output']) && (array_size($results['ComparisonIndexes']) > 0)){
			$results['ComparisonIndexes::ComparisonID'] = array_combine($results['ComparisonIndexes'], $results['Output']);
			$results['ComparisonID::ComparisonIndexes'] = array_combine($results['Output'], $results['ComparisonIndexes']);
		}
		
		
		
		

		if (array_size($results['platformSummary']) > 1){
			$results['hasMixedPlatform'] = 1;
		} else {
			$results['hasMixedPlatform'] = 0;	
		}
		
		if ($preferredPlatformType != ''){
			if (!isset($results['platformSummary'][$preferredPlatformType])){
				$results['missPreferredPlatform'] = 1;
			} else {
				$results['missPreferredPlatform'] = 0;	
			}
		}
		
		
		
		putSQLCache($cacheKey, $results, '', __FUNCTION__);
		
		return $results;
		
	}
	
}

function getProjectIDsExistenceInfo($projectIDs, $internalProjectIndexes){
	
	global $APP_CONFIG;
	
	if (!is_array($projectIDs)){
		$projectIDs = array(0 => $projectIDs);
	}
	
	$projectIDs = array_clean($projectIDs);
	
	if (array_size($projectIDs) <= 0) return false;
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(
							json_encode($projectIDs) . '::' . 
							json_encode($internalProjectIndexes) . '::' .
							$version);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;	
	}

	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'Projects';
	$sql_column 	= 'ProjectID';
	
	unset($valueString);
	foreach($projectIDs as $tempKey => $projectID){
		$valueString[] = "'" . addslashes(trim($projectID)) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	$SQL 			= "SELECT ProjectIndex, ProjectID, PubMed FROM {$sql_table} WHERE {$sql_column} IN ({$valueString}) ORDER BY FIELD(ProjectID, {$valueString})";
	$projectIndexes 	= getSQL($SQL, 'GetAssoc', $sql_table);
	
	
	
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	if ($internalProjectIndexString != ''){
		$sql_table 	= 'App_User_Data_Projects';
		$SQL 		= "SELECT ProjectIndex, ProjectID, ProjectID_Original FROM {$sql_table} WHERE ({$sql_column} IN ({$valueString})) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(ProjectID, {$valueString})";
	
		$temp	 	= getSQL($SQL, 'GetAssoc', $sql_table);
		
		foreach($temp as $tempKey => $tempValue){
			$projectIndexes[$tempKey] = internal_data_transform_one_data('Projects', $tempValue);
		}
	}
	
	if (array_size($projectIndexes) <= 0){
		return false;	
	} else {
		
		$results['Input_Count'] 	= array_size($projectIDs);
		$results['Output_Count'] 	= array_size($projectIndexes);
		
		
		$results['Input'] 			= $projectIDs;
		$results['Output'] 			= array_column($projectIndexes, 'ProjectID');
		
		$results['ProjectIndexes']	= array_keys($projectIndexes);
		
		
		foreach($projectIndexes as $tempKey => $tempValue){

			$results['reversed-strtolower'][strtolower($tempValue['ProjectID'])] = $tempKey;
			
			$results['Index-ID'][$tempKey] = $tempValue['ProjectID'];
			
			if (internal_data_is_public($tempKey)){
				$results['Output_Standard'][] = $tempValue['ProjectID'];
			} else {
				$results['Output_Standard'][] = $projectIndexes[$tempKey]['ProjectID_Original'];
				
			}
			
			
		}
		
		
		if ($results['Input_Count'] == $results['Output_Count']){
			$results['hasMissing']		= 0;
		} else {
			$results['hasMissing']		= 1;
			$results['Missing_Count'] 	= $results['Input_Count'] - $results['Output_Count'];
			$results['Missing']			= array_values(array_udiff($projectIDs, $results['Output'], 'strcasecmp'));
		}
		
		putSQLCache($cacheKey, $results, '', __FUNCTION__);
		
		return $results;
		
	}
	
}


//$mode:
//0: Nothing
//1: Highest Occurence
//2: Prechecked

//$preferences
//$mode = 1: $preferences = name of the column, e.g., 'PlatformName'
//$mode = 2: $preferences[colum_name'] = array('value1', 'value2');


//$dataSource: array('public', 'private');
//$internalProjectIndexes: array of private project indexes
function getColumnInfoByGene($geneNames, $valueTable, $columns, $mode = 0, $preferences = '', $dataSource = array(), $internalProjectIndexes = array(), $otherOptions = array()){


	unset($otherOptions['geneNames']);
	
	global $APP_CONFIG;
	
	if (!is_array($geneNames)){
		$geneNames = array(0 => $geneNames);
	}
	
	$geneNames = array_clean($geneNames);
	
	if (array_size($geneNames) == 1){
		return getColumnInfo($valueTable, $columns, $mode, $preferences, $dataSource, $internalProjectIndexes, $otherOptions);
	}
	
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	
	if (array_size($geneNames) <= 0) return false;
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(
						json_encode($geneNames) . '::' . 
						$valueTable . '::' . 
						json_encode($columns) . '::' . 
						$mode . '::' . 
						json_encode($preferences) . '::' . 
						json_encode($dataSource) . '::' . 
						json_encode($internalProjectIndexes) . '::' . 
						json_encode($otherOptions) . '::' . 
						json_encode(gene_uses_TPM()) . '::' . 
						$version
						);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}

	$highestOccurenceColumn = '';
	
	if ($mode == 0){
		$preferences = '';	
	} elseif ($mode == 1){
		$highestOccurenceColumn = trim($preferences);
		
		if ($highestOccurenceColumn == ''){
			$mode = 0;	
		}
		
	} elseif ($mode == 2){
		if (array_size($preferences) <= 0){
			$preferences = '';
			$mode = 0;	
		}
	}
	
	
	$finalResults['Mode'] 					= $mode;
	$finalResults['highestOccurenceColumn'] = $highestOccurenceColumn;
	$finalResults['preferences'] 			= $preferences;
	
	$geneIndexes = searchGeneIndexes($geneNames);
	
	
	if (array_size($geneIndexes) <= 0){
		return false;	
	}
	$geneIndex2geneName = array_flip($geneIndexes);
	

	
	//*******************
	// Search GeneFPKM
	//*******************
	$geneIndexString = implode(', ', $geneIndexes);
	
	$sql_table 				= $valueTable;
	$sql_column 			= 'GeneIndex';

	unset($geneExpressionValue);
	if ($dataSource['public'] != ''){
		
		$geneExpressionValue= tabix_search_records_with_index($geneIndexes, '', $sql_table);
	}
	
	if ($dataSource['private'] != ''){
		foreach($internalProjectIndexes as $tempKey => $projectIndex){
			
			$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, '', $sql_table);
			
			foreach($temp as $tempKeyX => $tempValueX){
				$geneExpressionValue[] = $tempValueX;
			}
			
			unset($temp);
		}
	}
	
	
	
	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	
	
	//*******************
	// Search Samples
	//*******************
	$sampleIndexes		= array_column($geneExpressionValue, 'SampleIndex');
	$sampleIndexes		= array_clean($sampleIndexes);
	$sampleIndexString 	= implode(', ', $sampleIndexes);
	
	$columnString		= implode(', ', $columns);
	

	$sql_table 		= 'Samples';
	$sql_column 	= 'SampleIndex';
	
	unset($sampleSources);
	
	if ($dataSource['public'] != ''){
		$SQL 			= "SELECT SampleIndex, {$columnString} FROM {$sql_table} WHERE {$sql_column} IN ({$sampleIndexString})";
		$sampleSources	= getSQL($SQL, 'GetAssoc', $sql_table);
	}

	if ($dataSource['private'] != ''){
		$SQL 			= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString})";
		$temp 			= getSQL($SQL, 'GetAssoc', $sql_table);
		foreach($temp as $tempKeyX => $tempValueX){
			$sampleSources[$tempKeyX] = $tempValueX;	
		}
			
		unset($temp);
	}
	
	
	
	if ($otherOptions['Modified_DiseaseState_Enable']){
		$sampleSources = modify_sample_disease_state_values($sampleSources, 
															$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
															array(),
															array(),
															$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
															1,
															$modify_sample_disease_state_values_executed
															);
				
		
		
		if (!$modify_sample_disease_state_values_executed){
			$otherOptions['Modified_DiseaseState_Enable'] = false;	
		}
	}
	
	
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentSampleIndex = $tempValue['SampleIndex'];
		$geneIndex			= $tempValue['GeneIndex'];
		
		$sampleIndex2GeneIndex[$currentSampleIndex][] = $geneIndex;
		
		$sampleIndex2GeneName[$currentSampleIndex][] = $geneIndex2geneName[$geneIndex];
		
		$sampleIndexToGeneExpressionValueCount[$currentSampleIndex]++;
		
		if ($mode == 0){
			if (isset($sampleSources[$currentSampleIndex])){
				$finalResults['DataCount_Plot']++;
			}
		}
	}
	
	
	
	foreach($columns as $tempKey => $column){

		foreach($sampleSources as $sampleIndex => $sampleRows){
			
			
			if (!$otherOptions['Modified_DiseaseState_Enable']){
				$temp = $sampleRows;
				unset($sampleRows);
				$sampleRows[0] = $temp;
			}
			
			
			foreach($sampleRows as $currentSampleIndexX => $currentSampleRecord){
				
				
				
				$finalResults['DataCount']	 += $sampleIndexToGeneExpressionValueCount[$sampleIndex];
				
				$currentCategoryValue 			= trim($currentSampleRecord[$column]);
				$currentCategoryValueForDisplay = ucwords2($currentCategoryValue);
				
				
				if ($currentCategoryValueForDisplay == ''){
					$currentCategoryValueForDisplay = $APP_CONFIG['Blank_Value'];	
				}
				
				$results[$column][$currentCategoryValueForDisplay]['By-Gene'] 			= $sampleIndex2GeneName[$sampleIndex];
				$results[$column][$currentCategoryValueForDisplay]['Total']++;
				$results[$column][$currentCategoryValueForDisplay]['Raw'] 				= $currentCategoryValue;
				if ($currentSampleRecord["{$column}_Org"] != ''){
					$results[$column][$currentCategoryValueForDisplay]['Org'] 				= $currentSampleRecord["{$column}_Org"];
				}
				
				$results[$column][$currentCategoryValueForDisplay]['GeneValueCount'] 	+= $sampleIndexToGeneExpressionValueCount[$sampleIndex];
				
				if ($mode == 2){
					if (isset($preferences[$column])){
						if (in_array($currentCategoryValue, $preferences[$column])){
							$finalResults['DataCount_Plot']++;
							$finalResults['PreSelected'][$column][$currentCategoryValue] = $currentCategoryValueForDisplay;
						}
					}
				}
				
				
				if ($results[$column][$currentCategoryValueForDisplay]['Total'] > $max[$column]['Total']){
					$max[$column]['Total_Key'] 		= $currentCategoryValueForDisplay;
					$max[$column]['Total_Display'] 	= $currentCategoryValueForDisplay;
					$max[$column]['Total_Raw'] 		= $currentCategoryValue;
					$max[$column]['Total'] 			= $results[$column][$currentCategoryValueForDisplay]['Total'];
				}
				
				if ($results[$column][$currentCategoryValueForDisplay]['GeneValueCount'] > $max[$column]['GeneValueCount']){
					$max[$column]['GeneValueCount_Key'] 	= $currentCategoryValueForDisplay;
					$max[$column]['GeneValueCount_Display'] = $currentCategoryValueForDisplay;
					$max[$column]['GeneValueCount_Raw'] 	= $currentCategoryValue;
					$max[$column]['GeneValueCount'] 		= $results[$column][$currentCategoryValueForDisplay]['GeneValueCount'];
				}
			}
			
		}

		natksort($results[$column]);
		
		if (isset($results[$column][$APP_CONFIG['Blank_Value']])){
			$temp = $results[$column][$APP_CONFIG['Blank_Value']];
			unset($results[$column][$APP_CONFIG['Blank_Value']]);
			$results[$column][$APP_CONFIG['Blank_Value']] = $temp;
		}
	}
	
	
	if ($mode == 1){
		$finalResults['DataCount_Plot'] 						= $max[$highestOccurenceColumn]['Total'];
		$finalResults['PreSelected'][$highestOccurenceColumn][($max[$highestOccurenceColumn]['GeneValueCount_Raw'])] = $max[$highestOccurenceColumn]['GeneValueCount_Display'];
	}
	
	$finalResults['Value'] 	= $results;
	$finalResults['Max']	= $max;	
	
	putSQLCache($cacheKey, $finalResults, '', __FUNCTION__);

	
	return $finalResults;
	

}






function getPlatformType($platform){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	
	if (get_gene_type() == 'Gene'){
		
		$NGS_platforms = $BXAF_CONFIG['NGS_PLATFORMS'];
		
		if (array_size($NGS_platforms) <= 0){
			$NGS_platforms = array('GPL15433', 'GPL18460', 'GPL20301', 'GPL20795', 'GPL11154', 'GPL16791', 'GPL10999', 'GPL18573', 'GPL9052', 'GPL15456', 'GPL20301', 'GPL9115', 'GPL21290', 'GPL20795', 'GPL15433', 'GPL18460', 'GPL15520', 'GPL13112', 'GPL17021', 'GPL11002', 'GPL19057', 'GPL9185', 'GPL15103', 'GPL9250', 'GPL21103', 'GPL18480', 'GPL16173', 'GPL21493', 'GPL16417', 'GPL14844', 'GPL18694', 'GPL20797', 'GPL22396', 'GPL20084', 'GPL10287', 'GPL10669', 'GPL19052', 'RNA-SEQ');
		}
		
		$platform = strtoupper(trim($platform));
		
		if (strpos($platform, 'NGS') === 0){
			return 'RNA-Seq';
		} elseif (in_array($platform, $NGS_platforms)){
			return 'RNA-Seq';
		} else {
			if ($APP_CONFIG['APP']['DefaultPlatform'] != ''){
				return $APP_CONFIG['APP']['DefaultPlatform'];	
			} else {
				return 'Microarray';
			}
		}
	} elseif (get_gene_type() == 'Protein'){
		return 'RNA-Seq';	
	}
	
}



function getRecordsByCategory($category, $indexes){
	
	global $APP_CONFIG;
	
	$sql_table		= $APP_CONFIG['APP']['List_Category'][$category]['Table'];
	$sql_column		= $APP_CONFIG['APP']['List_Category'][$category]['Column_Internal'];
	$indexString 	= implode(', ', $indexes);
	
	if (($sql_table != '') && ($indexString != '')){
		
		$SQL			= "SELECT {$sql_column}, {$sql_table}.* FROM {$sql_table} WHERE {$sql_column} IN ({$indexString})";
		$sql_results	= getSQL($SQL, 'GetAssoc', $sql_table);

	}

	return $sql_results;
}



function exportGeneSampleData($geneIndexes, $sampleIndexes, $genePlotColumns, $samplePlotColumns, $platformType, $dataSource, $internalProjectIndexes){
	global $APP_CONFIG;

	$geneIndexes 		= array_clean($geneIndexes);	
	$sampleIndexes 		= array_clean($sampleIndexes);
	$genePlotColumns 	= array_clean($genePlotColumns);
	$samplePlotColumns 	= array_clean($samplePlotColumns);
	$platformType		= trim($platformType);
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	
	if ($APP_CONFIG['EXPORT_LIMIT']){
		$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($geneIndexes) . '::' . 
										json_encode($sampleIndexes) . '::' . 
										json_encode($genePlotColumns) . '::' . 
										json_encode($samplePlotColumns) . '::' .
										$platformType . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode(gene_uses_TPM()) . '::' . 
										json_encode($APP_CONFIG['EXPORT_LIMIT_OPTIONS']) . '::' . 
										$version
										);
	} else {
		$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($geneIndexes) . '::' . 
										json_encode($sampleIndexes) . '::' . 
										json_encode($genePlotColumns) . '::' . 
										json_encode($samplePlotColumns) . '::' .
										$platformType . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode(gene_uses_TPM()) . '::' . 
										$version
										);		
		
	}
	
	
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'GeneCombined';
	$sql_column 	= 'GeneName';
	
	$columnToPlotString	= implode(', ', $genePlotColumns);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$valueString 	= implode(', ', $geneIndexes);

	
	
	if ($valueString == ''){
		$geneIndexIsNotSupplied = 1;
		
		$SQL 			= "SELECT GeneIndex, GeneName, GeneName {$columnToPlotString} FROM {$sql_table}";
		
	} else {
		$SQL 			= "SELECT GeneIndex, GeneName, GeneName {$columnToPlotString} FROM {$sql_table} WHERE GeneIndex IN ({$valueString}) ORDER BY FIELD(GeneIndex, {$valueString})";
	}
	
	
	$geneInfo		= getSQL($SQL, 'GetAssoc', $sql_table);
	
	if (array_size($geneInfo) <= 0){
		return false;	
	}
	
	
	//*******************
	// Search GeneFPKM
	//*******************
	$geneIndexString 		= implode(', ', $geneIndexes);
	$sampleIndexString 		= implode(', ', $sampleIndexes);
	$sampleIndexStringOrg 	= $sampleIndexString;
	
	if ($platformType == 'RNA-Seq'){
		$sql_table		= 'GeneFPKM';
		$value_table 	= 'GeneFPKM';
		$sql_column		= 'FPKM';
		$valueColumn	= 'FPKM';
		
		$valueColumns[] = 'FPKM';
		$valueColumns[] = 'Count';
		
		if (array_size($APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['GeneFPKM-Override']['valueColumns']) > 0){
			$valueColumns = $APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['GeneFPKM-Override']['valueColumns'];
		}
		
	} elseif ($platformType == 'Microarray'){
		$sql_table 		= 'GeneLevelExpression';
		$value_table 	= 'GeneLevelExpression';
		$sql_column		= 'Value';
		$valueColumn	= 'Value';
		
		$valueColumns[] = 'Value';
		
		if (array_size($APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['GeneLevelExpression-Override']['valueColumns']) > 0){
			$valueColumns = $APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['GeneLevelExpression-Override']['valueColumns'];
		}
	}
	
	$valueColumnsString = implode(', ', $valueColumns);
	
	unset($geneExpressionValue);
	if ($dataSource['public'] != ''){
		if ($geneIndexIsNotSupplied){
			if (array_size($sampleIndexes) > 0){
				$geneExpressionValue = tabix_search_records_with_index('',           $sampleIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePath);
			}
		} else {
			if (array_size($sampleIndexes) > 0){
				$geneExpressionValue = tabix_search_records_with_index($geneIndexes, $sampleIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePath);
			}
		}
	}
	
	
	
	if ($dataSource['private'] != ''){
		
		foreach($internalProjectIndexes as $tempKey => $projectIndex){
			unset($temp);
			if ($geneIndexIsNotSupplied){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, '',           $sampleIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePathPrivate);
			} else {
				$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $sampleIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePathPrivate);
			}
			
			foreach($temp as $tempKeyX => $tempValueX){
				
				if (!$checkedColumnHeader){
					$checkedColumnHeader = true;
					
					foreach($tempValueX as $currentHeader => $currentValue){
						if ($currentHeader == 'SampleIndex') continue;
						if ($currentHeader == 'GeneIndex') continue;
						
						if (in_array($currentHeader, $valueColumns)) continue;
						
						$valueColumns[] = $currentHeader;
						
					}
				}
				
				$geneExpressionValue[] = $tempValueX;	
			}
			
			unset($temp);
		}
		
	}


	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentGeneIndex 	= $tempValue['GeneIndex'];
		$currentSampleIndex = $tempValue['SampleIndex'];
		
		unset($keepGoing);
		if (!isset($score[$currentGeneIndex][$currentSampleIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue[$valueColumn]) && ($tempValue[$valueColumn] > $score[$currentGeneIndex][$currentSampleIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue[$valueColumn]) && !is_numeric($score[$currentGeneIndex][$currentSampleIndex])){
			$keepGoing = 1;
		}
		
		if (!$keepGoing) continue;
		
		foreach($valueColumns as $tempKey2 => $currentValueColumn){
			$geneExpressionValueIndex[$currentValueColumn][$currentGeneIndex][$currentSampleIndex] = $tempValue[$currentValueColumn];
		}
		
		$score[$currentGeneIndex][$currentSampleIndex] = $tempValue[$valueColumn];
	}
	unset($score);

	//*******************
	// Search Sample Index
	//*******************
	$columnToPlotString	= implode(', ', $samplePlotColumns);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$sampleIndexString = array_clean(array_column($geneExpressionValue, 'SampleIndex'));
	$sampleIndexString = implode(',', $sampleIndexString);
	
	unset($samples);
	if ($sampleIndexString != ''){
		
		if ($dataSource['public'] != ''){
			$sql_table	= 'Samples';
			$SQL 		= "SELECT SampleIndex, SampleID, SampleID {$columnToPlotString} FROM {$sql_table} WHERE SampleIndex IN ({$sampleIndexString}) ORDER BY FIELD(SampleIndex, {$sampleIndexStringOrg})";
		
			$samples	= getSQL($SQL, 'GetAssoc', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			$sql_table	= 'App_User_Data_Samples';
			$SQL 		= "SELECT * FROM {$sql_table} WHERE SampleIndex IN ({$sampleIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(SampleIndex, {$sampleIndexStringOrg})";
		
			$temp	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($temp as $tempKeyX => $tempValueX){
				$samples[$tempKeyX] = internal_data_transform_one_data('Samples', $tempValueX);
			}
		}
	}
	
	
	$results['Export-All']['Headers']['Gene_Annotation'] = array();
	$results['Export-All']['Headers']['Sample'] = array();
	$results['Export-All']['Headers']['Value'] = array();
	
	
	$currentSampleCount = 0;
	foreach($samples as $currentSampleIndex => $tempValue1){
		
		if ($APP_CONFIG['EXPORT_LIMIT'] && (++$currentSampleCount > $APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Sample']) && ($APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Sample'] > 0)){
			$results['Summary']['Export_Limit'] = true;
			continue;	
		}
		
		$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
		
		
		$results['Export']['Headers'][$columnToPlotPrintable][$currentSampleIndex] = $tempValue1['SampleID'];
		$results['Export-All']['Headers']['Sample']['SampleID'] = $columnToPlotPrintable;
		
		$tempTitle = $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['Sample_ID']['Title'];
		if ($tempTitle == ''){
			$tempTitle = 'Sample ID';	
		}
		
		$samplesFormatted[$currentSampleIndex][$tempTitle] = $tempValue1['SampleID'];
		
		
		foreach($samplePlotColumns as $tempKey2 => $columnToPlot){
			$categoryToPlot		= ucwords2(trim($samples[$currentSampleIndex][$columnToPlot]));
			if ($categoryToPlot	== ''){
				$categoryToPlot	= $APP_CONFIG['Blank_Value'];	
			}
			
			$columnToPlotPrintable = $flexibleColumnSummary['By-Column'][$columnToPlot]['Title'];
			if ($columnToPlotPrintable == ''){
				$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$columnToPlot]['Title'];
			}
			
			$results['Export']['Headers'][$columnToPlotPrintable][$currentSampleIndex] 		= $categoryToPlot;
			
			$results['Export-All']['Headers']['Sample'][$columnToPlot] 						= $columnToPlotPrintable;
			
			$samplesFormatted[$currentSampleIndex][$columnToPlotPrintable] 					= $categoryToPlot;
			
		}
		
	}
	
	
	$currentIndex = -1;

	
	$currentGeneCount = 0;
	foreach($geneInfo as $currentGeneIndex => $currentGeneInfo){
		
		$currentGeneName = $currentGeneInfo['GeneName'];
		
		if ($APP_CONFIG['EXPORT_LIMIT'] && (++$currentGeneCount > $APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Gene']) && ($APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Gene'] > 0)){
			$results['Summary']['Export_Limit'] = true;
			continue;	
		}
		
		
		$currentIndex++;
		$currentSampleCount = 0;
		foreach($samples as $currentSampleIndex => $tempValue1){
			
			if ($APP_CONFIG['EXPORT_LIMIT'] && (++$currentSampleCount > $APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Sample']) && ($APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Sample'] > 0)){
				$results['Summary']['Export_Limit'] = true;
				continue;	
			}
			
			if (!isset($results['Summary']['Samples'][$currentSampleIndex])){
				$results['Summary']['SampleIndex'][$currentSampleIndex] = $samples[$currentSampleIndex]['SampleID'];
			}
			
			foreach($valueColumns as $currentValueColumnKey => $currentValueColumn){
				if (isset($geneExpressionValueIndex[$currentValueColumn][$currentGeneIndex])){
					
					$results['Summary']['Gene'][$currentGeneIndex] = $currentGeneName;
					
					
					$currentValue = $geneExpressionValueIndex[$currentValueColumn][$currentGeneIndex][$currentSampleIndex];
					
					if (($currentValue === '') || (is_null($currentValue) || ($currentValue === '.') || ($currentValue == 'NA'))){
						$currentValue = 'NA';
						
					} else {
						
						$currentValue = floatval($currentValue);
	
						if (!isset($results['Summary']['HasNumericValue'])){
							$results['Summary']['HasNumericValue'] = 1;
						}
					}
					
					if (!isset($results['Export']['Gene_Annotation'][$currentGeneIndex])){
						$results['Export']['Gene_Annotation'][$currentGeneIndex] = $currentGeneInfo;
					}
					$results['Export']['Gene_Values'][$currentValueColumn][$currentGeneIndex][$currentSampleIndex] = $currentValue;
					
					$results['Export-All']['Body']["{$currentGeneIndex}-{$currentSampleIndex}"]['Gene_Annotation'] = $currentGeneInfo;
					$results['Export-All']['Body']["{$currentGeneIndex}-{$currentSampleIndex}"]['Sample'] = $samplesFormatted[$currentSampleIndex];
					$results['Export-All']['Body']["{$currentGeneIndex}-{$currentSampleIndex}"]['Value'][$currentValueColumn] = $currentValue;
					
					
				}
			}
		}
		
	}
	
	$results['Summary']['Overlap Name'] = "{$APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['GeneIndex']['Title']}/{$APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleIndex']['Title']}";


	$results['Summary']['Gene Columns']['GeneName'] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['GeneName']['Title'];
	
	foreach($genePlotColumns as $tempKey => $tempValue){
		$results['Summary']['Gene Columns'][$tempValue] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$tempValue]['Title'];
		$results['Export-All']['Headers']['Gene_Annotation'][$tempValue] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$tempValue]['Title'];
	}
	
	$results['Summary']['Header Padding'] = array_size($results['Summary']['Gene Columns']);


	foreach($valueColumns as $tempKey => $tempValue){
		
		$currentTitle = $APP_CONFIG['DB_Dictionary'][$value_table]['SQL'][$tempValue]['Title'];
		
		if ($currentTitle == '') $currentTitle = $tempValue;
		
		$results['Summary']['Value Names'][$tempValue] 			= $currentTitle;
		
		$results['Export-All']['Headers']['Value'][$tempValue] 	= $currentTitle;
	}
	
	$results['Summary']['Tabix'] = $tabixFilePath;
	$results['Summary']['Index Column'] = 'SampleIndex';

	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}



function exportGeneComparisonData($geneIndexes, $comparisonIndexes, $genePlotColumns, $comparisonPlotColumns, $dataSource, $internalProjectIndexes){
	global $APP_CONFIG;

	$geneIndexes 			= array_clean($geneIndexes);	
	$comparisonIndexes 		= array_clean($comparisonIndexes);
	$genePlotColumns 		= array_clean($genePlotColumns);
	$comparisonPlotColumns 	= array_clean($comparisonPlotColumns);
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	
	if ($APP_CONFIG['EXPORT_LIMIT']){
		$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($geneIndexes) . '::' . 
										json_encode($comparisonIndexes) . '::' . 
										json_encode($genePlotColumns) . '::' . 
										json_encode($comparisonPlotColumns) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode(gene_uses_TPM()) . '::' . 
										json_encode($APP_CONFIG['EXPORT_LIMIT_OPTIONS']) . '::' . 
										$version
										);
	} else {
		$cacheKey = __FUNCTION__ . '::' . md5(
										json_encode($geneIndexes) . '::' . 
										json_encode($comparisonIndexes) . '::' . 
										json_encode($genePlotColumns) . '::' . 
										json_encode($comparisonPlotColumns) . '::' . 
										json_encode($dataSource) . '::' . 
										json_encode($internalProjectIndexes) . '::' . 
										json_encode(gene_uses_TPM()) . '::' . 
										$version
										);
	}
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	
	//*******************
	// Search Gene Index
	//*******************
	$sql_table 		= 'GeneCombined';
	$sql_column 	= 'GeneName';
	
	$columnToPlotString	= implode(', ', $genePlotColumns);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$valueString 	= implode(', ', $geneIndexes);
	
	
	
	
	if ($valueString == ''){
		$geneIndexIsNotSupplied = 1;
		$SQL 			= "SELECT GeneName, GeneIndex FROM {$sql_table} ORDER BY GeneName ASC";
		$geneIndexes	= array_values(getSQL($SQL, 'GetAssoc', $sql_table));
		$valueString 	= implode(', ', $geneIndexes);
		
		$SQL 			= "SELECT GeneIndex, GeneName, GeneName {$columnToPlotString} FROM {$sql_table} ORDER BY FIELD(GeneIndex, {$valueString})";
		
	} else {
		$SQL 			= "SELECT GeneIndex, GeneName, GeneName {$columnToPlotString} FROM {$sql_table} WHERE GeneIndex IN ({$valueString}) ORDER BY FIELD(GeneIndex, {$valueString})";
	}
	$geneInfo		= getSQL($SQL, 'GetAssoc', $sql_table);

	
	if (array_size($geneInfo) <= 0){
		return false;	
	}
	
	
	//*******************
	// Search Comparisons
	//*******************
	$geneIndexString 	= implode(', ', $geneIndexes);
	$comparisonIndexString 	= implode(', ', $comparisonIndexes);
	$comparisonIndexStringOrg 	= $comparisonIndexString;
	
	
	if (true){
		$sql_table		= 'ComparisonData';
		$value_table 	= 'ComparisonData';
		
		$valueColumns = array('Log2FoldChange', 'PValue', 'AdjustedPValue', 'NumeratorValue', 'DenominatorValue');
		
		if (array_size($APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['ComparisonData-Override']['valueColumns']) > 0){
			$valueColumns = $APP_CONFIG['BXAF_CONFIG_CUSTOM']['TABIX_INDEX']['ComparisonData-Override']['valueColumns'];
		}
		
	}
		
	$valueColumnsString = implode(', ', $valueColumns);
	$SQL 		= "SELECT GeneIndex, ComparisonIndex, {$valueColumnsString} FROM {$sql_table} WHERE (GeneIndex IN ({$geneIndexString})) AND (ComparisonIndex IN ({$comparisonIndexString}))";
	
	unset($geneExpressionValue);
	if ($dataSource['public'] != ''){
		if ($geneIndexIsNotSupplied){
			if (array_size($comparisonIndexes) > 0){
				$geneExpressionValue = tabix_search_records_with_index('',           $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePath);
			}
		} else {
			if (array_size($comparisonIndexes) > 0){
				$geneExpressionValue = tabix_search_records_with_index($geneIndexes, $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePath);
			}
		}
	}
	
	
	if ($dataSource['private'] != ''){
		foreach($internalProjectIndexes as $tempKey => $projectIndex){
			unset($temp);
			if ($geneIndexIsNotSupplied){
				$temp = tabix_search_records_with_index_internal_data($projectIndex, '',           $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePathPrivate);
			} else {
				$temp = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $comparisonIndexes, $sql_table, 'GetArrayAssoc', $tabixFilePathPrivate);
			}
			

			
			foreach($temp as $tempKeyX => $tempValueX){
				$geneExpressionValue[] = $tempValueX;	
			}
			unset($temp);
		}
		
	}
	

	
	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentGeneIndex 		= $tempValue['GeneIndex'];
		$currentComparisonIndex = $tempValue['ComparisonIndex'];
		
		unset($keepGoing);
		if (!isset($score[$currentGeneIndex][$currentComparisonIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue['PValue']) && ($tempValue['PValue'] < $score[$currentGeneIndex][$currentComparisonIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue['PValue']) && !is_numeric($score[$currentGeneIndex][$currentComparisonIndex])){
			$keepGoing = 1;
		}
		
		if (!$keepGoing) continue;

		foreach($valueColumns as $tempKey2 => $currentValueColumn){
			$geneExpressionValueIndex[$currentValueColumn][$currentGeneIndex][$currentComparisonIndex] = $tempValue[$currentValueColumn];
		}
		
		$score[$currentGeneIndex][$currentComparisonIndex] = $tempValue['PValue'];
		
	}
	unset($score);



	//*******************
	// Search Comparison Index
	//*******************
	$columnToPlotString	= implode(', ', $comparisonPlotColumns);
	
	if ($columnToPlotString != ''){
		$columnToPlotString = ", {$columnToPlotString}";	
	}
	
	$comparisonIndexString = array_clean(array_column($geneExpressionValue, 'ComparisonIndex'));
	$comparisonIndexString = implode(',', $comparisonIndexString);
	
	
	unset($comparisons);
	if ($comparisonIndexString != ''){
		
		if ($dataSource['public'] != ''){
			$sql_table	= 'Comparisons';
			$SQL 		= "SELECT ComparisonIndex, ComparisonID, ComparisonID {$columnToPlotString} FROM {$sql_table} WHERE ComparisonIndex IN ({$comparisonIndexString}) ORDER BY FIELD(ComparisonIndex, {$comparisonIndexStringOrg})";	
		
			$comparisons	= getSQL($SQL, 'GetAssoc', $sql_table);
		}
		
		if ($dataSource['private'] != ''){
			$sql_table	= 'App_User_Data_Comparisons';
			$SQL 		= "SELECT * FROM {$sql_table} WHERE ComparisonIndex IN ({$comparisonIndexString}) AND (`ProjectIndex` IN ({$internalProjectIndexString})) ORDER BY FIELD(ComparisonIndex, {$comparisonIndexStringOrg})";
		
			$temp	= getSQL($SQL, 'GetAssoc', $sql_table);
			
			foreach($temp as $tempKeyX => $tempValueX){
				$comparisons[$tempKeyX] = $tempValueX;
			}
		}
	
	}
	
	$results['Export-All']['Headers']['Gene_Annotation'] 	= array();
	$results['Export-All']['Headers']['Comparison']			= array();
	$results['Export-All']['Headers']['Value']				= array();

	$currentComparisonCount = 0;
	foreach($comparisons as $currentComparisonIndex => $tempValue1){
		
		if ($APP_CONFIG['EXPORT_LIMIT'] && (++$currentComparisonCount > $APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Comparison']) && ($APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Comparison'] > 0)){
			$results['Summary']['Export_Limit'] = true;
			continue;	
		}
		
		$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonID']['Title'];
		$results['Export']['Headers'][$columnToPlotPrintable][$currentComparisonIndex] = $tempValue1['ComparisonID'];
		$results['Export-All']['Headers']['Comparison']['ComparisonID'] = $columnToPlotPrintable;
		
		foreach($comparisonPlotColumns as $tempKey2 => $columnToPlot){
			$categoryToPlot		= ucwords2(trim($comparisons[$currentComparisonIndex][$columnToPlot]));
			if ($categoryToPlot	== ''){
				$categoryToPlot	= $APP_CONFIG['Blank_Value'];	
			}
			
			$columnToPlotPrintable = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$columnToPlot]['Title'];
			$results['Export']['Headers'][$columnToPlotPrintable][$currentComparisonIndex] 			= $categoryToPlot;
			
			$results['Export-All']['Headers']['Comparison'][$columnToPlot] = $columnToPlotPrintable;
			
			$comparisonsFormatted[$currentComparisonIndex][$columnToPlotPrintable] 			= $categoryToPlot;
		}
		
	}

	$currentIndex = -1;

	
	$currentGeneCount = 0;
	foreach($geneInfo as $currentGeneIndex => $currentGeneInfo){
		
		if ($APP_CONFIG['EXPORT_LIMIT'] && (++$currentGeneCount > $APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Gene']) && ($APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Gene'] > 0)){
			$results['Summary']['Export_Limit'] = true;
			continue;	
		}
		
		$currentGeneName = $currentGeneInfo['GeneName'];

		$currentIndex++;
		$currentComparisonCount = 0;
		foreach($comparisons as $currentComparisonIndex => $tempValue1){
			
			if ($APP_CONFIG['EXPORT_LIMIT'] && (++$currentComparisonCount > $APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Comparison']) && ($APP_CONFIG['EXPORT_LIMIT_OPTIONS']['Comparison'] > 0)){
				$results['Summary']['Export_Limit'] = true;
				continue;
			}
			
			if (!isset($results['Summary']['Comparisons'][$currentComparisonIndex])){
				$results['Summary']['ComparisonIndex'][$currentComparisonIndex] = $comparisons[$currentComparisonIndex]['ComparisonID'];
			}
			
			foreach($valueColumns as $currentValueColumnKey => $currentValueColumn){
				if (isset($geneExpressionValueIndex[$currentValueColumn][$currentGeneIndex])){
					
					$results['Summary']['Gene'][$currentGeneIndex] = $currentGeneName;
					
					
					$currentValue = $geneExpressionValueIndex[$currentValueColumn][$currentGeneIndex][$currentComparisonIndex];
					
					if (($currentValue === '') || (is_null($currentValue) || ($currentValue === '.') || ($currentValue == 'NA'))){
						$currentValue = 'NA';
						
					} else {
						
						$currentValue = floatval($currentValue);
	
						if (!isset($results['Summary']['HasNumericValue'])){
							$results['Summary']['HasNumericValue'] = 1;
						}
					}
					
					if (!isset($results['Export']['Gene_Annotation'][$currentGeneIndex])){
						$results['Export']['Gene_Annotation'][$currentGeneIndex] = $currentGeneInfo;
					}
					
					$results['Export']['Gene_Values'][$currentValueColumn][$currentGeneIndex][$currentComparisonIndex] = $currentValue;
					
					$results['Export-All']['Body']["{$currentGeneIndex}-{$currentComparisonIndex}"]['Gene_Annotation'] = $currentGeneInfo;
					$results['Export-All']['Body']["{$currentGeneIndex}-{$currentComparisonIndex}"]['Comparison'] = $comparisonsFormatted[$currentComparisonIndex];
					$results['Export-All']['Body']["{$currentGeneIndex}-{$currentComparisonIndex}"]['Value'][$currentValueColumn] = $currentValue;
				}
			}
		}
		
	}
	
	
		
	
	$results['Summary']['Overlap Name'] = "{$APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['GeneIndex']['Title']}/{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL']['ComparisonIndex']['Title']}";


	$results['Summary']['Gene Columns']['GeneName'] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['GeneName']['Title'];
	
	foreach($genePlotColumns as $tempKey => $tempValue){
		$results['Summary']['Gene Columns'][$tempValue] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$tempValue]['Title'];
		$results['Export-All']['Headers']['Gene_Annotation'][$tempValue] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$tempValue]['Title'];
	}
	
	$results['Summary']['Header Padding'] = array_size($results['Summary']['Gene Columns']);

	foreach($valueColumns as $tempKey => $tempValue){
		$results['Summary']['Value Names'][$tempValue] = $APP_CONFIG['DB_Dictionary'][$value_table]['SQL'][$tempValue]['Title'];
		$results['Export-All']['Headers']['Value'][$tempValue] = $APP_CONFIG['DB_Dictionary'][$value_table]['SQL'][$tempValue]['Title'];
	}
	
	$results['Summary']['Tabix'] = $tabixFilePath;
	$results['Summary']['Index Column'] = 'ComparisonIndex';
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}



function guessGeneIndex($gene, $species = ''){
	
	global $APP_CONFIG, $BAXF_CACHE;
	
	$gene = addslashes(strtolower(trim($gene)));
	
	$gene = fix_ensembl_id($gene, $species);
	
	$species = trim($species);

	if ($species == '') return -1;
		
	$SQL_TABLE = "Gene_Lookup_{$species}";
	
	if (isset($BAXF_CACHE[__FUNCTION__][$gene][$species])){
		return $BAXF_CACHE[__FUNCTION__][$gene][$species];
	}
	
	
	$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `ID` = '{$gene}' LIMIT 1";

	$SQL_RESULTS = getSQL($SQL, 'GetRow', $SQL_TABLE);
	
	if ($SQL_RESULTS['ID'] == ''){
		
		$BAXF_CACHE[__FUNCTION__][$gene][$species] = -1;
		
		return -1;	
	} else {
		
		$BAXF_CACHE[__FUNCTION__][$gene][$species] = $SQL_RESULTS['GeneIndex'];
		
		return $SQL_RESULTS['GeneIndex'];
	}
	
}


function getGeneNameFromGeneIndex($geneIndex){
	
	global $APP_CONFIG, $BAXF_CACHE;
	
	$gene = intval($geneIndex);
	
	if (isset($BAXF_CACHE[__FUNCTION__][$geneIndex])){
		return $BAXF_CACHE[__FUNCTION__][$geneIndex];
	}
	
	$SQL_TABLE = 'GeneCombined';

	if (!is_numeric($geneIndex)) return false;
	
	$SQL = "SELECT `GeneName` FROM `{$SQL_TABLE}` WHERE `GeneIndex` = {$geneIndex}";

	$SQL_RESULTS = getSQL($SQL, 'GetOne', $SQL_TABLE);
		
	$BAXF_CACHE[__FUNCTION__][$geneIndex] = $SQL_RESULTS;
		
	return $SQL_RESULTS;
}


function cleanInternalDataInput(&$dataSource, &$internalProjectIndexes){
	
	if (array_size($dataSource) <= 0){
		$dataSource = array('public');
	}
	
	if ((array_size($dataSource) == 1) && ($dataSource[0] == 'public')) {
		$internalProjectIndexes = array();
	}
	

	$internalProjectIndexes = array_clean($internalProjectIndexes);
	
	
	
	if (array_size($internalProjectIndexes) <= 0){
		$needToRemovePrivate = 1;
	}
	
	if (!internal_data_enable()){
		$needToRemovePrivate = 1;
	}
	
	
	foreach($dataSource as $tempKey => $tempValue){
		if (($tempValue != 'public') && ($tempValue != 'private')){
			unset($dataSource[$tempKey]);
		}
		
		if (($tempValue == 'private') && $needToRemovePrivate){
			unset($dataSource[$tempKey]);
		}

	}
	
	if (array_size($dataSource) <= 0){
		$dataSource = array('public');
	}
	
	$dataSource = array_clean($dataSource);
	
	$dataSource = array_combine($dataSource, $dataSource);
		
	
	return true;

}


function reviewRecordByName($recordIndex, $currentSQL, $recordValue, $targetCategory){
	
	$recordValueEncoded = urlencode($recordValue);
	
	$URL = "<a href='app_review_record_by_name.php?currentSQL={$currentSQL}&targetCategory={$targetCategory}&recordIndex={$recordIndex}&value={$recordValueEncoded}' target='_blank'>{$recordValue}</a>";
	
	return $URL;
	
}


function splitComparisonSampleIDs($string1, $string2){
	$string1 = trim($string1);
	
	
	if ($string1 != ''){
		$array = explode(';', $string1);
	
		$array = array_clean($array);
		
		foreach($array as $tempKey => $tempValue){
			$tempArray = explode(',', $tempValue);
			
			foreach($tempArray as $tempKey2 => $tempValue2){
				$results[] = $tempValue2;	
			}
		}
	}
	
	$string2 = trim($string2);
	if ($string2 != ''){
		$array = explode(';', $string2);
	
		$array = array_clean($array);
		
		foreach($array as $tempKey => $tempValue){
			$tempArray = explode(',', $tempValue);
			
			foreach($tempArray as $tempKey2 => $tempValue2){
				$results[] = $tempValue2;	
			}
		}
	}
	
	
	$results = array_clean($results);
	
	return $results;

}


function modify_sample_disease_state_values($sampleArray = array(), 
											$comparisonCategories = array(), 
											$comparisonIndexes = array(), 
											$diseaseStateCategories = array(), 
											$useAbbreviation = 0,
											$duplicate = 0,
											&$executed
											){
	
	$executed = false;
	global $APP_CONFIG;
	
	if (!$APP_CONFIG['APP']['Module']['Modified_DiseaseState']) return $sampleArray;
	
	$sampleIndexes = array_clean(array_keys($sampleArray));
	
	if (array_size($sampleIndexes) <= 0) return $sampleArray;
	
	$sampleIndexes = implode(',', $sampleIndexes);
	
	$sql_table 	= $APP_CONFIG['Table']['App_Sample_Comparison_DiseaseState'];
	$SQL 		= "SELECT `SampleIndex`, `ComparisonIndex`, `Comparison_Disease_State`, `Comparison_Disease_State_Abbreviation` FROM `{$sql_table}` WHERE (`SampleIndex` IN ({$sampleIndexes}))";
	
	if (array_size($comparisonCategories) > 0){
		if (json_encode($comparisonCategories) != json_encode($APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory'])){
			foreach($comparisonCategories as $tempKey => $tempValue){
				$comparisonCategoriesForSearch[] = trim(addslashes($tempValue));
			}
			$comparisonCategoriesForSearch = "'" . implode("','", $comparisonCategoriesForSearch) . "'";
			$SQL .= " AND (`ComparisonCategory` IN ({$comparisonCategoriesForSearch}))";
		}
	}
	
	
	$comparisonIndexes = array_clean($comparisonIndexes);
	if (array_size($comparisonIndexes) > 0){
		$comparisonIndexesString = implode(',', $comparisonIndexes);
		$SQL .= " AND (`ComparisonIndex` IN ({$comparisonIndexesString}))";	
	}
	
	
	if (array_size($diseaseStateCategories) > 0){
		foreach($diseaseStateCategories as $tempKey => $tempValue){
			$diseaseStateCategoriesForSearch[] = trim(addslashes($tempValue));
		}
		
		$diseaseStateCategoriesForSearch = "'" . implode("','", $diseaseStateCategoriesForSearch) . "'";
		
		if ($useAbbreviation){
			$SQL .= " AND (`Comparison_Disease_State_Abbreviation` IN ({$diseaseStateCategoriesForSearch}))";
		} else {
			$SQL .= " AND (`Comparison_Disease_State` IN ({$diseaseStateCategoriesForSearch}))";
		}

	}
	
	$SQL .= " AND (`Control_DiseaseState` != '')";
	
	
	
	if (!$duplicate){
		$mapping	= getSQL($SQL, 'GetAssoc', $sql_table);
	} else {
		$mapping	= getSQL($SQL, 'GetArray', $sql_table);
	}
	
	if (array_size($mapping) <= 0){
		return $sampleArray;	
	}
	
	if ($useAbbreviation){
		$columnName = 'Comparison_Disease_State_Abbreviation';
	} else {
		$columnName = 'Comparison_Disease_State';	
	}
	
	
	
	
	if (!$duplicate){
		foreach($sampleArray as $sampleIndex => $currentSample){
			
			if (strpos($mapping[$sampleIndex][$columnName], '_') === 0){
				unset($sampleArray[$sampleIndex]);	
			} elseif ($mapping[$sampleIndex][$columnName] != ''){
				$sampleArray[$sampleIndex]['DiseaseState'] = $mapping[$sampleIndex][$columnName];
			} else {
				unset($sampleArray[$sampleIndex]);	
			}
		}
		
		$results = $sampleArray;
	} else {
		$processedSampleIndex = array();
		
		foreach($mapping as $tempKey => $tempValue){
			$sampleIndex = $tempValue['SampleIndex'];
			
			$base = $sampleArray[$sampleIndex];

			$base['DiseaseState_Org'] = $tempValue[$columnName];
			
			if ($useAbbreviation){
				$base['DiseaseState'] 		= makeDiseaseStateShort($tempValue[$columnName]);
			} else {
				$base['DiseaseState'] 		= $tempValue[$columnName];
			}
			$base['ComparisonIndex'] 	= $tempValue['ComparisonIndex'];

			$results[$sampleIndex][] = $base;
			
			$processedSampleIndex[$sampleIndex] = 1;
		}
		
		
		
		if (array_size($processedSampleIndex) != array_size($sampleArray)){
			foreach($sampleArray as $sampleIndex => $sampleInfo){
				
				if ($processedSampleIndex[$sampleIndex]) continue;
				$results[$sampleIndex][] = $sampleInfo;
				
			}
			
		}
		
	}
	
	
	
	
	$executed = true;
	return $results;
	
}

function makeDiseaseStateShort($string){

	global $APP_CONFIG;
	
	if (isset($APP_CONFIG['BXAF_CONFIG_CUSTOM']['COMPARISON_INFO']['ComparisonCategory_Short'])){
		
		foreach($APP_CONFIG['BXAF_CONFIG_CUSTOM']['COMPARISON_INFO']['ComparisonCategory_Short'] as $tempKey => $tempValue){
			if (endsWith($string, "_{$tempKey}")){
				$results = str_replace("_{$tempKey}", '', $string);
				
				$results = "{$results}_{$tempValue}";
				break;
			}
		}
	}
	
	if ($results == ''){
		$results = $string;	
	}
	
	return $results;
	
	
	
}

function comparison_index_to_project_name($comparisonIndex){
	
	global $APP_CONFIG;
	
	$source 	= internal_data_split_multiple_data_by_source($comparisonIndex);

	unset($projectNames);	
	if (array_size($source['public']) > 0){
		$comparisonIndexes = implode(',', $source['public']);
		$SQL_TABLE = $APP_CONFIG['Table']['Comparisons'];
		$SQL = "SELECT `ProjectName` FROM `{$SQL_TABLE}` WHERE `ComparisonIndex` IN ({$comparisonIndexes}) GROUP BY `ProjectName`";
		$comparisonPublic = getSQL($SQL, 'GetCol', $SQL_TABLE, 1, 1);

	}
	
	if (array_size($source['private']) > 0){
		$comparisonIndexes = implode(',', $source['private']);
		$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Comparisons'];
		$SQL = "SELECT `ProjectName` FROM `{$SQL_TABLE}` WHERE `ComparisonIndex` IN ({$comparisonIndexes})";
		$comparisonPrivate = getSQL($SQL, 'GetCol', $SQL_TABLE, 1, 1);
	}
	
	
	foreach($comparisonPublic as $tempKey => $tempValue){
		$projectNames[$tempValue] = $tempValue;
	}
	
	foreach($comparisonPrivate as $tempKey => $tempValue){
		$projectNames[$tempValue] = $tempValue;
	}
	
	
	return $projectNames;
}


function comparison_index_to_sample_id($comparisonIndex){
	
	global $APP_CONFIG;
	
	$source 	= internal_data_split_multiple_data_by_source($comparisonIndex);

	unset($sampleIDs);	
	if (array_size($source['public']) > 0){
		$comparisonIndexes = implode(',', $source['public']);
		$SQL_TABLE = $APP_CONFIG['Table']['Comparisons'];
		$SQL = "SELECT `ComparisonIndex`, `Case_SampleIDs`, `Control_SampleIDs` FROM `{$SQL_TABLE}` WHERE `ComparisonIndex` IN ({$comparisonIndexes})";
		$comparisonPublic = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 1, 1);

		
		foreach($comparisonPublic as $tempKey => $tempValue){
			$currentSampleIDString = explode(';', $tempValue['Case_SampleIDs']);
			$currentSampleIDString = array_clean($currentSampleIDString);
			foreach($currentSampleIDString as $tempKey2 => $tempValue2){
				$sampleIDs[] = $tempValue2;
			}
			
			$currentSampleIDString = explode(';', $tempValue['Control_SampleIDs']);
			$currentSampleIDString = array_clean($currentSampleIDString);
			foreach($currentSampleIDString as $tempKey2 => $tempValue2){
				$sampleIDs[] = $tempValue2;
			}
	
		}
	}
	
	if (array_size($source['private']) > 0){
		$comparisonIndexes = implode(',', $source['private']);
		$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Comparisons'];
		$SQL = "SELECT `ComparisonIndex`, `Case_SampleIDs`, `Control_SampleIDs` FROM `{$SQL_TABLE}` WHERE `ComparisonIndex` IN ({$comparisonIndexes})";
		$comparisonPrivate = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 1, 1);

		
		foreach($comparisonPrivate as $tempKey => $tempValue){
			$currentSampleIDString = explode(';', $tempValue['Case_SampleIDs']);
			$currentSampleIDString = array_clean($currentSampleIDString);
			foreach($currentSampleIDString as $tempKey2 => $tempValue2){
				$sampleIDs[] = $tempValue2;
			}
			
			$currentSampleIDString = explode(';', $tempValue['Control_SampleIDs']);
			$currentSampleIDString = array_clean($currentSampleIDString);
			foreach($currentSampleIDString as $tempKey2 => $tempValue2){
				$sampleIDs[] = $tempValue2;
			}
			
		}
	}
	
	return $sampleIDs;
	
}



function getSampleAttributes($includeClinicalTriplets = 0){
	
	global $APP_CONFIG;
	
	foreach($APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options_Completed'] as $tempKey => $tempValue){
		
		if (isset($APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options'][$tempKey])){
			$results['Default'][$tempKey] = $tempValue['Title'];
		}
		$results['All'][$tempKey] = $tempValue['Title'];
	}
	
	if ($APP_CONFIG['APP']['Module']['Clinical_Triplets'] && $includeClinicalTriplets){
		foreach($APP_CONFIG['DB_Dictionary']['Samples']['SQL'] as $tempKey => $tempValue){
			if ($APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$tempKey]['Clinical_Triplets']){
				$results['Clinical Triplets'][$tempKey] = $tempValue['Title'];
			}
		}
		
		natcasesort($results['Clinical Triplets']);
	}
	
	natcasesort($results['All']);
	
	return $results;
	
}




function getHeatmapColors($min, $max, $value){
	
	global $APP_CONFIG;
	
	$colorSize = array_size($APP_CONFIG['APP']['Heatmap_Colors']);
	
	$range = abs($max - $min);
	
	$interval = $range / $colorSize;
	
	for ($i = 1; $i <= $colorSize; $i++){
		
		if (($value >= $min + ($i - 1)*$interval) && ($value <= $min + $i * $interval)){
			return $APP_CONFIG['APP']['Heatmap_Colors'][$i];	
		}
	}
	
	return $APP_CONFIG['APP']['Heatmap_Colors'][$i];
}

function getGeneExpressionTableInfo($platformType){
	
	if ($platformType == 'RNA-Seq'){
		$info['Table'] 	= 'GeneFPKM';
		$info['Column']	= 'FPKM';
	} elseif ($platformType == 'Microarray'){
		$info['Table'] 	= 'GeneLevelExpression';
		$info['Column']	= 'Value';
	}
	
	return $info;
}


//mode:
//HTML
//Print
//HTML2
function processData($category, $SQL_RESULTS, $getTableColumnPreferences, $mode){
	
	global $APP_CONFIG, $BXAF_CONFIG, $APP_MESSAGE;
	
	$categoryLower = strtolower($category);
	
	if (array_size($getTableColumnPreferences) <= 0){
		$getTableColumnPreferences = getTableColumnPreferences($category);
	}
	
	if (($category == 'Comparison') || ($category == 'Gene') || ($category == 'Dataset')){
		$needActionColumn = true;
	} else {
		$needActionColumn = false;
	}
	
	$currentCount = 0;
	$tableTableRowIndex = -1;
	foreach($SQL_RESULTS as $recordKey => $currentRecord){
		
		
		$currentRecord = internal_data_transform_one_data($category, $currentRecord);
		
		$function = $APP_CONFIG['APP']['List_Category'][$category]['transform_function'];
		
		if ($function != '' && function_exists($function)){
			$currentRecord = $function($recordKey, $currentRecord, $mode);
		}
		

		
		$tableTableRowIndex++;
		$currentCount++;
		
		if (isset($currentRecord[($APP_CONFIG['APP']['List_Category'][$category]['Column_Internal'])])){
			$currentIndex	= $currentRecord[($APP_CONFIG['APP']['List_Category'][$category]['Column_Internal'])];
		} else {
			$currentIndex	= $recordKey;
		}
		$currentURL		= "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type={$categoryLower}&id={$currentIndex}";

		//Checkbox	
		if ($mode == 'HTML'){
			$dataHTML[$currentIndex][] 	= "<div class='text-center'><input currentcount='{$currentCount}' type='checkbox' class='recordCheckbox' value='{$currentIndex}' id='checkbox_datatable_row_index_{$tableTableRowIndex}'/></div>";
		}
		
		if ($mode == 'HTML'){
			//Actions
			unset($currentActions);
			if ($category == 'Comparison'){
				$tempURL = $currentURL;
				$currentActions[] 	= "<a title='Review Comparison' 		href='{$tempURL}'><button class='btn btn-primary btn-circle'>C</button></a>";
				
				$tempURL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/volcano/index.php?id={$currentIndex}";
				$currentActions[] 	= "<a title='View Volcano Chart' 		href='{$tempURL}'><button class='btn btn-info btn-circle'>V</button></a>";
				
				$tempURL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/pvjs/index.php?id={$currentIndex}";
				$currentActions[] 	= "<a title='View Pathway' 		href='{$tempURL}'><button class='btn btn-circle btn-warning'>P</button></a>";
				
			} elseif ($category == 'Gene'){
				$tempURL = $currentURL;
				
				if (get_gene_type() == 'Protein'){
					$currentActions[] 	= "<a title='{$APP_MESSAGE['Review Gene']}' href='{$tempURL}'><button class='btn btn-primary btn-circle'>P</button></a>";
				} else {
					$currentActions[] 	= "<a title='{$APP_MESSAGE['Review Gene']}' href='{$tempURL}'><button class='btn btn-primary btn-circle'>G</button></a>";
				}
				
				if ($APP_CONFIG['APP']['Module']['RNA-Seq']){
					$tempURL = "app_gene_expression_rnaseq_single.php?GeneName={$currentRecord['GeneName']}";
					$currentActions[] 	= "<a title='{$APP_MESSAGE['Gene Expressions from RNA-Seq']}' href='{$tempURL}'><button class='btn btn-info btn-circle'>R</button></a>";
				}
				
				if ($APP_CONFIG['APP']['Module']['Microarray']){
					$tempURL = "app_gene_expression_microarray_single.php?GeneName={$currentRecord['GeneName']}";
					$currentActions[] 	= "<a title='{$APP_MESSAGE['Gene Expressions from Microarray']}' 		href='{$tempURL}'><button class='btn btn-warning btn-circle'>M</button></a>";
				}
			
			
			
			} elseif ($category == 'Dataset'){
				
				
				
				if (true){
					$tempURL = "app_dataset_ajax.php?ID={$currentRecord['DatasetIndex']}&action=start";
					$currentActions[] 	= "<a href='{$tempURL}' target='_blank'><button class='btn btn-success'>Start/Launch</button></a>";
				}
				
				if (isAdminUser()){
					if (isCellxGeneRunning($currentRecord['DatasetIndex'])){
						$tempURL = "app_dataset_ajax.php?ID={$currentRecord['DatasetIndex']}&action=stop";
						$currentActions[] 	= "<a href='{$tempURL}'><button class='btn btn-danger'>Stop</button></a>";
					}
				}

				
			} else {
				$currentActions[] 	= "<a currentcount='{$currentCount}' target='_blank' href='{$currentURL}'>Review</a>";
			}
			
			if ($needActionColumn){
				$dataHTML[$currentIndex][] = "<div class='nowrap'>" . implode('&nbsp;', $currentActions) . "</div>";
			}
		}


		foreach($getTableColumnPreferences as $columnKey => $columnDetail){
			$currentSQL 						= $columnDetail['SQL'];
			
			if (($mode == 'Print')){
				$dataPrint[$currentIndex][] 	= $currentRecord[$currentSQL];
			} elseif ($mode == 'HTML'){
				$standardTable = $APP_CONFIG['APP']['List_Category'][$category]['Table'];
				
				if ($currentSQL == $APP_CONFIG['APP']['List_Category'][$category]['Column_Human']){
					$dataHTML[$currentIndex][] 	= "<a title='{$currentRecord[$currentSQL]}' class='text-nowrap' href='{$currentURL}'>{$currentRecord[$currentSQL]}</a>";
					
				} elseif (function_exists($APP_CONFIG['DB_Dictionary'][$standardTable]['SQL'][$currentSQL]['HTML'])){
					
					$function 		= $APP_CONFIG['DB_Dictionary'][$standardTable]['SQL'][$currentSQL]['HTML'];
					$targetCategory = $APP_CONFIG['DB_Dictionary'][$standardTable]['SQL'][$currentSQL]['Target_Category'];
				
					$dataHTML[$currentIndex][] 	= $function($currentIndex, $currentSQL, $currentRecord[$currentSQL], $targetCategory);
				
				} else {
					$dataHTML[$currentIndex][] 	= displayLongText($currentRecord[$currentSQL], 140);
				}
			} elseif ($mode == 'HTML2'){
				$standardTable = $APP_CONFIG['APP']['List_Category'][$category]['Table'];
				
				if ($currentSQL == $APP_CONFIG['APP']['List_Category'][$category]['Column_Human']){
					$dataHTML[$currentIndex][$currentSQL] 	= "<a title='{$currentRecord[$currentSQL]}' class='text-nowrap' href='{$currentURL}'>{$currentRecord[$currentSQL]}</a>";
					
				} elseif (function_exists($APP_CONFIG['DB_Dictionary'][$standardTable]['SQL'][$currentSQL]['HTML'])){
					
					$function 		= $APP_CONFIG['DB_Dictionary'][$standardTable]['SQL'][$currentSQL]['HTML'];
					$targetCategory = $APP_CONFIG['DB_Dictionary'][$standardTable]['SQL'][$currentSQL]['Target_Category'];
				
					$dataHTML[$currentIndex][$currentSQL] 	= $function($currentIndex, $currentSQL, $currentRecord[$currentSQL], $targetCategory);
				
				} else {
					$dataHTML[$currentIndex][$currentSQL] 	= displayLongText($currentRecord[$currentSQL], 140);
				}
			}
		}
	}
	
	
	
	if ($mode == 'Print'){
		return $dataPrint;
	} else {
		return $dataHTML;
	}
	
	
	
}


function getComparisonInfoForDisplay($comparison){
	
	global $BXAF_CONFIG;
	
	unset($results);
	foreach($BXAF_CONFIG['COMPARISON_INFO']['Sequence'] as $tempKey => $currentSQL){
		
		if (isset($BXAF_CONFIG['COMPARISON_INFO'][$currentSQL][$comparison[$currentSQL]])){
			
			$results[] = $BXAF_CONFIG['COMPARISON_INFO'][$currentSQL][$comparison[$currentSQL]];
		} else {
			$results[] = $comparison[$currentSQL];
		}
	}
	
	$results = array_filter($results, 'trim');
	
	if (array_size($results) > 0){
		return implode('.', $results);		
	} else {
		return $comparison['ComparisonID'];	
	}
	
}


function getGeneInfoFromComparison($comparisonIndex, $preferredGeneIndexes = array(), $otherOptions = array()){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	
	$version  = $APP_CONFIG['Version']['Cache Version'];

	$cacheKey = __FUNCTION__ . '::' . md5(
										$comparisonIndex . 
										json_encode(gene_uses_TPM()) . '::' . 
										json_encode($preferredGeneIndexes) . '::' . 
										json_encode($otherOptions) . '::' . 
										$version
										);
										
	$resultsFromCache = getSQLCache($cacheKey);
	
	$file_suffix = $otherOptions['file_suffix'];
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	if (array_size($preferredGeneIndexes) > 0){
		$preferredGeneIndexes = array_combine($preferredGeneIndexes, $preferredGeneIndexes);
		$hasPreferredGene = true;	
	}
	
	$comparisonArray = get_multiple_record('comparison', $comparisonIndex, 'GetRow', '*', 0);

	

	if (true){	
		if (internal_data_is_public($comparisonIndex)){
			$tabix = tabix_search_records_with_index('', array($comparisonIndex), 'ComparisonData');
		} else {
			$tabix = tabix_search_records_with_index_internal_data($comparisonArray['ProjectIndex'], '', array($comparisonIndex), 'ComparisonData');
		}

		
		
		if (array_size($tabix) <= 0){
			return false;	
		}
	}

	
	if (true){
		$geneIndexes = array_clean(array_column($tabix, 'GeneIndex'));
		$geneIndexes = implode(',', $geneIndexes);
		if ($geneIndexes == '') return false;

		$sql_table 	= 'GeneCombined';
		$SQL 		= "SELECT `GeneIndex`, `GeneName`, `EntrezID` FROM {$sql_table} WHERE `GeneIndex` IN ({$geneIndexes})";
		$allGenes	= getSQL($SQL, 'GetAssoc', $sql_table);
	}
	
		
	
	if (true){
		$comparisonCategory = $comparisonArray['ComparisonCategory'];
		$comparisonContrast = $comparisonArray['ComparisonContrast'];

		if (strpos($comparisonCategory, ' vs. ') !== false) {
			$temp = explode(' vs. ', $comparisonCategory);
			$numeratorHeader 	= $temp[0];
			$denominatorHeader	= $temp[1];
			$hasNumeratorHeader	= true;
		} elseif (strpos($comparisonCategory, ' vs ') !== false) {
			
			if ($comparisonContrast != ''){
				$temp = explode(' vs ', $comparisonContrast);
			} else {
				$temp = explode(' vs ', $comparisonCategory);	
			}
			$numeratorHeader 	= $temp[0];
			$denominatorHeader	= $temp[1];
			$hasNumeratorHeader	= true;
		}
	}
	
	if (true){
		$path			 = "{$BXAF_CONFIG['WORK_DIR']}Comparison_Gene_Info/{$BXAF_CONFIG['APP_PROFILE']}/";
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}
		$results['Summary']['Path'] = $path;
		$file_suffix = str_replace(' ', '_', trim($file_suffix));
		
		$inputFilePath = "{$path}Comparison_Gene_Info_{$comparisonIndex}_{$file_suffix}.csv";
		
		$results['Summary']['Comparison_Gene_Info.csv'] = $inputFilePath;
		$results['Summary']['File_name'] = "Comparison_Gene_Info_{$comparisonIndex}_{$file_suffix}.csv";
		
		$fp = fopen($inputFilePath, 'w');
	}
	

	if (true){	
		$dataArray   = array();
		$dataArray['Headers']['Name'] 			= $APP_CONFIG['DB_Dictionary']['ComparisonData']['SQL']['Name']['Title'];
		$dataArray['Headers']['Log2FoldChange'] 	= $APP_CONFIG['DB_Dictionary']['ComparisonData']['SQL']['Log2FoldChange']['Title'];
		$dataArray['Headers']['PValue'] 			= $APP_CONFIG['DB_Dictionary']['ComparisonData']['SQL']['PValue']['Title'];
		$dataArray['Headers']['AdjustedPValue'] 	= $APP_CONFIG['DB_Dictionary']['ComparisonData']['SQL']['AdjustedPValue']['Title'];
		
		if ($hasNumeratorHeader){
			$dataArray['Headers']['NumeratorValue'] 	= $numeratorHeader;
			$dataArray['Headers']['DenominatorValue'] 	= $denominatorHeader;
		}
		
		$dataArray['Headers']['GeneName'] 		= $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['GeneName']['Title'];
		$dataArray['Headers']['EntrezID'] 		= $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['EntrezID']['Title'];
		
		fputcsv($fp, $dataArray['Headers']);
	}
	
	$decimal = 5;
	

	
	
	foreach($tabix as $tabixID => $currentRecord){
		
		if ($hasPreferredGene){
			if (!isset($preferredGeneIndexes[$currentRecord['GeneIndex']])){
				continue;
			}
		}
		
		if ($otherOptions['process']){
			
			$fc_cutoff 				= $otherOptions['fc_cutoff'];
			$statistic_cutoff 		= $otherOptions['statistic_cutoff'];
			$significance_threshold = $otherOptions['significance_threshold'];
			$logfc_threshold 		= $otherOptions['logfc_threshold'];
			
			$Y_COL_NAME				= $otherOptions['logfc_threshold'];
			
			$x = floatval($currentRecord['Log2FoldChange']);
			$y = -log10(floatval($currentRecord['Y']));
			
			
			if ($otherOptions['Direction'] == 'Up'){
				if ($x > $logfc_threshold && $y > $significance_threshold){
					//Pass
				} else {
					continue;	
				}
			} elseif ($otherOptions['Direction'] == 'Down'){
				if ($x < (-1) * $logfc_threshold && $y > $significance_threshold){
					//Pass
				} else {
					continue;	
				}
			}
		}
		
		
		
		$results['Summary']['Row_Count']++;
		
		$currentCSV = array();
		
		if (true){
			$dataArray['Body'][$tabixID][] 	= $currentRecord['Name'];
			$currentCSV[]		 			= $currentRecord['Name'];
		}
		
		if (true){
			$sql_name						= 'Log2FoldChange';
			$currentColor 					= getStatScaleColor($currentRecord[$sql_name], $sql_name);
			
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$dataArray['Body'][$tabixID][] 	= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			$currentCSV[] 					= $currentRecord[$sql_name];
		}
		
		if (true){
			$sql_name						= 'PValue';
			$currentColor 					= getStatScaleColor($currentRecord[$sql_name], $sql_name);
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$dataArray['Body'][$tabixID][] 	= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			$currentCSV[] 					= $currentRecord[$sql_name];
		}
		
		if (true){
			$sql_name						= 'AdjustedPValue';
			$currentColor 					= getStatScaleColor($currentRecord[$sql_name], $sql_name);
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$dataArray['Body'][$tabixID][] 	= "<span is_numeric='true' style='color:{$currentColor};'>{$currentValue}</span>";
			$currentCSV[] 					= $currentRecord[$sql_name];
		}
		
		if ($hasNumeratorHeader){
			$sql_name						= 'NumeratorValue';
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$dataArray['Body'][$tabixID][] 	= $currentValue;
			$currentCSV[] 					= $currentRecord[$sql_name];
		}
			
			
		if ($hasNumeratorHeader){
			$sql_name						= 'DenominatorValue';
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$dataArray['Body'][$tabixID][] 	= $currentValue;
			$currentCSV[] 					= $currentRecord[$sql_name];
		}
		
		
		$currentGeneIndex = $currentRecord['GeneIndex'];
		
		
		$dataArray['Body'][$tabixID][] 	= "<a title='{$allGenes[$currentGeneIndex]['GeneName']}' href='app_gene_review.php?id={$currentGeneIndex}' target='_blank'>{$allGenes[$currentGeneIndex]['GeneName']}</a>";
		$currentCSV[] 					= $allGenes[$currentGeneIndex]['GeneName'];
		
		$dataArray['Body'][$tabixID][] 	= $allGenes[$currentGeneIndex]['EntrezID'];
		$currentCSV[] 					= $allGenes[$currentGeneIndex]['EntrezID'];
		
		fputcsv($fp, $currentCSV);
		unset($currentCSV);
	}
	
	$results['Summary']['Row_Count_Formatted'] = number_format($results['Summary']['Row_Count']);

	fclose($fp);	
	
	$results['HTML'] = $dataArray;

	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}

function getPAGEInfoFromComparison($comparisonIndex){
	
	global $APP_CONFIG, $BXAF_CONFIG, $APP_MESSAGE;

	
	
	$version  = $APP_CONFIG['Version']['Cache Version'];

	$cacheKey = __FUNCTION__ . '::' . md5(
										$comparisonIndex . 
										$version
										);
										
	$resultsFromCache = getSQLCache($cacheKey);
	
	
	
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	

	if (true){	
		if (internal_data_is_public($comparisonIndex)){
			$file = "{$BXAF_CONFIG['PAGE_PATH']}/comparison_{$comparisonIndex}_GSEA.PAGE.csv";
		} else {
			$file = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$comparisonIndex}/PAGE_comp_{$comparisonIndex}.csv";
		}
		
		if (!file_exists($file)){
			return false;	
		}
	}
	
	
	if (true){
		$rawData = readFirstFewLinesFromFile($file, 0, 1, 'csv');
		$results['Path'] = $file;
		$results['Summary'] = array();

		$results['Header'][0] = "{$APP_MESSAGE['Gene']} List";
		$results['Header'][1] = "# of {$APP_MESSAGE['Gene']} (Total)";
		$results['Header'][2] = 'Z-Score';
		$results['Header'][3] = 'p-value';
		$results['Header'][4] = 'FDR';
		$results['Header'][5] = "# of {$APP_MESSAGE['Gene']} (Up)";
		$results['Header'][6] = "# of {$APP_MESSAGE['Gene']} (Down)";

	}
	
	
	$decimal = 5;
	

	foreach($rawData['Body'] as $rowID => $currentRecord){
		
		$results['Summary']['Row_Count']++;
		
		
		
		if (true){
			$sql_name						= 'Z Score';
			$typeName						= 'ZScore';
			$currentColor 					= getStatScaleColor($currentRecord[$sql_name], $typeName);
			
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$currentRecord[$sql_name] 	= "<span is_numeric='true' number='{$currentValue}' style='color:{$currentColor};'>{$currentValue}</span>";
		}



		if (true){
			$sql_name						= 'p-value';
			$typeName						= 'PValue';
			$currentColor 					= getStatScaleColor($currentRecord[$sql_name], $typeName);
			
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$currentRecord[$sql_name] 	= "<span is_numeric='true' number='{$currentValue}' style='color:{$currentColor};'>{$currentValue}</span>";
		}
		
		if (true){
			$sql_name						= 'FDR';
			$typeName						= 'AdjustedPValue';
			$currentColor 					= getStatScaleColor($currentRecord[$sql_name], $typeName);
			
			if (is_numeric($currentRecord[$sql_name])){
				$currentValue				= round($currentRecord[$sql_name], $decimal);
			} else {
				$currentValue				= '';	
			}
			$currentRecord[$sql_name] 	= "<span is_numeric='true' number='{$currentValue}' style='color:{$currentColor};'>{$currentValue}</span>";
		}
		
		$results['Body'][$rowID] = array_values($currentRecord);
		
	}
		
	$results['Summary']['Row_Count_Formatted'] = number_format($results['Summary']['Row_Count']);

	
	
	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	
	return $results;
	
}


function searchGeneIndexes($geneNames){
	
	global $APP_CONFIG;
	
	if (!is_array($geneNames)){
		$geneNames = array(0 => $geneNames);
	}
	
	return search_gene_indexes($geneNames);
}


function getGeneDataByProjectIndex($projectIndex, $projectID, $valueTable = 'GeneFPKM', $valueColumn = 'FPKM'){
	
	global $APP_CONFIG;
	
	$version  = $APP_CONFIG['Version']['Cache Version'];

	$cacheKey = __FUNCTION__ . '::' . md5(
										$projectIndex . 
										$projectID . 
										$valueTable . 
										$valueColumn . 
										json_encode(gene_uses_TPM()) . '::' . 
										$version
										);
										
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	if (internal_data_is_public($projectIndex)){
		$projectID 		= addslashes($projectID);
		$sql_table 		= $APP_CONFIG['Table']['Samples'];
		$SQL 			= "SELECT `SampleIndex` FROM {$sql_table} WHERE `ProjectName` = '{$projectID}'";
	} else {
		$sql_table 		= $APP_CONFIG['Table']['App_User_Data_Samples'];
		$SQL 			= "SELECT `SampleIndex` FROM {$sql_table} WHERE `ProjectIndex` = '{$projectIndex}'";
	}
	$sampleIndexes 	= getSQL($SQL, 'GetCol', $sql_table);
	
	
	if (array_size($sampleIndexes) <= 0){
		return false;	
	}
	
	$sampleArray = get_multiple_record('Sample', $sampleIndexes, 'GetAssoc', '`SampleIndex`, `SampleID`');
	
	if (internal_data_is_public($projectIndex)){
		if (array_size($sampleIndexes) > 0){
			$geneExpressionValue = tabix_search_records_with_index('', $sampleIndexes, $valueTable, 'GetArrayAssoc', $tabixFilePath);
		}
	} else {
		if (array_size($sampleIndexes) > 0){
			$geneExpressionValue = tabix_search_records_with_index_internal_data($projectIndex, '', $sampleIndexes, $valueTable, 'GetArrayAssoc', $tabixFilePath);
		}
	}

	if (array_size($geneExpressionValue) <= 0){
		return false;	
	}
	
	$geneIndexes = array_column($geneExpressionValue, 'GeneIndex');
	$geneIndexes = array_clean($geneIndexes);
	
	$geneArray = get_multiple_record('Gene', '', 'GetAssoc', "`GeneIndex`, `GeneName`, `EntrezID`", 1);
	
	
	
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentGeneIndex 	= $tempValue['GeneIndex'];
		$currentSampleIndex = $tempValue['SampleIndex'];
		
		$keepGoing = false;
		if (!isset($score[$currentGeneIndex][$currentSampleIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue[$valueColumn]) && ($tempValue[$valueColumn] > $score[$currentGeneIndex][$currentSampleIndex])){
			$keepGoing = 1;
		} elseif (is_numeric($tempValue[$valueColumn]) && !is_numeric($score[$currentGeneIndex][$currentSampleIndex])){
			$keepGoing = 1;
		}
		
		if (!$keepGoing) continue;
		
		$geneExpressionValueIndex[$currentGeneIndex][$currentSampleIndex] = $tempValue[$valueColumn];
		
		$score[$currentGeneIndex][$currentSampleIndex] = $tempValue[$valueColumn];
	}
	unset($score);
	
	$results = array();
	
	$results['Header'][] = '';
	$results['Header'][] = '';
	$results['Header'][] = $APP_CONFIG['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
	
	foreach($sampleIndexes as $tempKey => $currentSampleIndex){
		$results['Header'][] = $sampleArray[$currentSampleIndex];
	}
	
	$results['Body']['Headers'][] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['GeneName']['Title'];
	$results['Body']['Headers'][] = $APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL']['EntrezID']['Title'];
	$results['Body']['Headers'][] = "Gene Index / Sample Index";
	foreach($sampleIndexes as $tempKey => $currentSampleIndex){
		$results['Body']['Headers'][] = $currentSampleIndex;
	}
	
	foreach($geneExpressionValueIndex as $currentGeneIndex => $tempValue1){
		
		$results['Body'][$currentGeneIndex][-1] = $geneArray[$currentGeneIndex]['GeneName'];
		$results['Body'][$currentGeneIndex][-2] = $geneArray[$currentGeneIndex]['EntrezID'];
		$results['Body'][$currentGeneIndex][-3] = $currentGeneIndex;
		
		foreach($tempValue1 as $currentSampleIndex => $value){
			$results['Body'][$currentGeneIndex][$currentSampleIndex] = $value;
		}
	}
	

	putSQLCache($cacheKey, $results, '', __FUNCTION__);
	
	return $results;
	
}



function get_iTarget_URL($geneIndex){
	
	global $BXAF_CONFIG;
	
	if ($BXAF_CONFIG['iTarget_Ensembl_URL'] == '') return false;
	
	$emsembl = geneIndexToEnsembl($geneIndex);
	
	if ($emsembl == '') return false;
	
	$URL = "{$BXAF_CONFIG['iTarget_Ensembl_URL']}{$emsembl}";
	
	return $URL;
		
}


function geneIndexToEnsembl($geneIndex){
	
	$emsembl = get_multiple_record('gene', $geneIndex, 'GetOne', '`Ensembl`', 0);
	
	$emsembl = explode('|', $emsembl);
	$emsembl = array_clean($emsembl);
	$emsembl = $emsembl[0];
	
	if (strtolower($emsembl) == 'na'){
		$emsembl = '';	
	}
	
	return $emsembl;
	
}

function getColumnInfo($valueTable, $columns, $mode = 0, $preferences = '', $dataSource = array(), $internalProjectIndexes = array(), $otherOptions = array()){
	
	
	unset($otherOptions['geneNames']);
	
	
	global $APP_CONFIG;
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$version = $APP_CONFIG['Version']['Cache Version'];
	$cacheKey = __FUNCTION__ . '::' . md5(
						$valueTable . '::' . 
						json_encode($columns) . '::' . 
						$mode . '::' . 
						json_encode($preferences) . '::' . 
						json_encode($dataSource) . '::' . 
						json_encode($internalProjectIndexes) . '::' . 
						json_encode($otherOptions) . '::' . 
						$version
						);

	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}

	$highestOccurenceColumn = '';
	
	if ($mode == 0){
		$preferences = '';	
	} elseif ($mode == 1){
		$highestOccurenceColumn = trim($preferences);
		
		if ($highestOccurenceColumn == ''){
			$mode = 0;	
		}
		
	} elseif ($mode == 2){
		if (array_size($preferences) <= 0){
			$preferences = '';
			$mode = 0;	
		}
	}
	
	
	$finalResults['Mode'] 					= $mode;
	$finalResults['highestOccurenceColumn'] = $highestOccurenceColumn;
	$finalResults['preferences'] 			= $preferences;
	
	
	//*******************
	// Search Samples
	//*******************
	$sampleIndexes		= array_column($geneExpressionValue, 'SampleIndex');
	$sampleIndexes		= array_clean($sampleIndexes);
	$sampleIndexString 	= implode(', ', $sampleIndexes);
	
	$columnString		= implode(', ', $columns);
	

	$sql_table 		= 'Samples';
	$sql_column 	= 'SampleIndex';
	
	unset($sampleSources);
	
	if ($dataSource['public'] != ''){
		$SQL 			= "SELECT SampleIndex, {$columnString} FROM {$sql_table}";
		$sampleSources	= getSQL($SQL, 'GetAssoc', $sql_table);
	}

	if ($dataSource['private'] != ''){
		$SQL 			= "SELECT * FROM `App_User_Data_Samples` WHERE `ProjectIndex` IN ({$internalProjectIndexString})";
		$temp 			= getSQL($SQL, 'GetAssoc', $sql_table);
		foreach($temp as $tempKeyX => $tempValueX){
			$sampleSources[$tempKeyX] = $tempValueX;	
		}
			
		unset($temp);
	}
	
	
	
	if ($otherOptions['Modified_DiseaseState_Enable']){
		$sampleSources = modify_sample_disease_state_values($sampleSources, 
															$otherOptions['Modified_DiseaseState_ComparisonCategory'], 
															array(),
															array(),
															$otherOptions['Modified_DiseaseState_Display_Abbreviation'],
															1,
															$modify_sample_disease_state_values_executed
															);
				
		
		
		if (!$modify_sample_disease_state_values_executed){
			$otherOptions['Modified_DiseaseState_Enable'] = false;	
		}
	}
	
	
	/*
	foreach($geneExpressionValue as $tempKey => $tempValue){
		$currentSampleIndex = $tempValue['SampleIndex'];
		$geneIndex			= $tempValue['GeneIndex'];
		
		$sampleIndex2GeneIndex[$currentSampleIndex][] = $geneIndex;
		
		$sampleIndex2GeneName[$currentSampleIndex][] = $geneIndex2geneName[$geneIndex];
		
		$sampleIndexToGeneExpressionValueCount[$currentSampleIndex]++;
		
		if ($mode == 0){
			if (isset($sampleSources[$currentSampleIndex])){
				$finalResults['DataCount_Plot']++;
			}
		}
	}
	*/
	
	
	
	foreach($columns as $tempKey => $column){

		foreach($sampleSources as $sampleIndex => $sampleRows){
			
			
			if (!$otherOptions['Modified_DiseaseState_Enable']){
				$temp = $sampleRows;
				unset($sampleRows);
				$sampleRows[0] = $temp;
			}
			
			
			foreach($sampleRows as $currentSampleIndexX => $currentSampleRecord){
				
				
				
				$finalResults['DataCount']	 += $sampleIndexToGeneExpressionValueCount[$sampleIndex];
				
				$currentCategoryValue 			= trim($currentSampleRecord[$column]);
				$currentCategoryValueForDisplay = ucwords2($currentCategoryValue);
				
				
				if ($currentCategoryValueForDisplay == ''){
					$currentCategoryValueForDisplay = $APP_CONFIG['Blank_Value'];	
				}
				
				$results[$column][$currentCategoryValueForDisplay]['By-Gene'] 			= $sampleIndex2GeneName[$sampleIndex];
				$results[$column][$currentCategoryValueForDisplay]['Total']++;
				$results[$column][$currentCategoryValueForDisplay]['Raw'] 				= $currentCategoryValue;
				if ($currentSampleRecord["{$column}_Org"] != ''){
					$results[$column][$currentCategoryValueForDisplay]['Org'] 				= $currentSampleRecord["{$column}_Org"];
				}
				
				$results[$column][$currentCategoryValueForDisplay]['GeneValueCount'] 	+= $sampleIndexToGeneExpressionValueCount[$sampleIndex];
				
				if ($mode == 2){
					if (isset($preferences[$column])){
						if (in_array($currentCategoryValue, $preferences[$column])){
							$finalResults['DataCount_Plot']++;
							$finalResults['PreSelected'][$column][$currentCategoryValue] = $currentCategoryValueForDisplay;
						}
					}
				}
				
				
				if ($results[$column][$currentCategoryValueForDisplay]['Total'] > $max[$column]['Total']){
					$max[$column]['Total_Key'] 		= $currentCategoryValueForDisplay;
					$max[$column]['Total_Display'] 	= $currentCategoryValueForDisplay;
					$max[$column]['Total_Raw'] 		= $currentCategoryValue;
					$max[$column]['Total'] 			= $results[$column][$currentCategoryValueForDisplay]['Total'];
				}
				
				if ($results[$column][$currentCategoryValueForDisplay]['GeneValueCount'] > $max[$column]['GeneValueCount']){
					$max[$column]['GeneValueCount_Key'] 	= $currentCategoryValueForDisplay;
					$max[$column]['GeneValueCount_Display'] = $currentCategoryValueForDisplay;
					$max[$column]['GeneValueCount_Raw'] 	= $currentCategoryValue;
					$max[$column]['GeneValueCount'] 		= $results[$column][$currentCategoryValueForDisplay]['GeneValueCount'];
				}
			}
			
		}

		natksort($results[$column]);
		
		if (isset($results[$column][$APP_CONFIG['Blank_Value']])){
			$temp = $results[$column][$APP_CONFIG['Blank_Value']];
			unset($results[$column][$APP_CONFIG['Blank_Value']]);
			$results[$column][$APP_CONFIG['Blank_Value']] = $temp;
		}
	}
	
	
	if ($mode == 1){
		$finalResults['DataCount_Plot'] 						= $max[$highestOccurenceColumn]['Total'];
		$finalResults['PreSelected'][$highestOccurenceColumn][($max[$highestOccurenceColumn]['GeneValueCount_Raw'])] = $max[$highestOccurenceColumn]['GeneValueCount_Display'];
	}
	
	$finalResults['Value'] 	= $results;
	$finalResults['Max']	= $max;	
	
	putSQLCache($cacheKey, $finalResults, '', __FUNCTION__);

	
	return $finalResults;
	

}

?>