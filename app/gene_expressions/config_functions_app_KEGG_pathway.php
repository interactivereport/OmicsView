<?php

function getKEGGPathwayByID($ID, $process = 1){
	
	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['KEGG_Pathway'];
	$ID 		= intval($ID);
	
	if ($ID <= 0) return false;
	
	$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `ID` = {$ID}";
	$dataArray = getSQL($SQL, 'GetRow', $SQL_TABLE);
	
	if ($dataArray['ID'] > 0){
		if ($process){
			$dataArray = processKEGGPathway($dataArray, $ID);
		}
	}
	
	return $dataArray;
	
}


function getKEGGPathwayByIdentifier($Identifier, $process = 1){
	
	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['KEGG_Pathway'];
	$Identifier = trim($Identifier);
	
	if ($Identifier == '') return false;
	
	$SQL = "SELECT * FROM `{$SQL_TABLE}` WHERE `Identifier` = '{$Identifier}'";
	$dataArray = getSQL($SQL, 'GetRow', $SQL_TABLE);
	
	if ($dataArray['ID'] > 0){
		if ($process){
			$dataArray = processKEGGPathway($dataArray, $ID);
		}
	}
	
	return $dataArray;
	
}


function processKEGGPathway($dataArray, $ID){
	
	if (!$dataArray['processed']){
	
		if ($dataArray['ID'] <= 0){
			$dataArray['ID'] = $ID;	
		}
	
		$dataArray['XML'] = json_decode($dataArray['XML'], true);
		unset($dataArray['XML']);
		
		$startPosition 	= strpos($dataArray['HTML_Raw'], '<area');
		$endPosition	= strpos($dataArray['HTML_Raw'], '</map>');
		
		$length			= $endPosition - $startPosition;
		
		$dataArray['HTML_Map'] = substr($dataArray['HTML_Raw'], $startPosition, $length);
		unset($dataArray['HTML_Raw']);
		
		$dataArray['HTML_Map'] = str_replace('/dbget-bin/', 'http://www.kegg.jp/dbget-bin/', $dataArray['HTML_Map']);
		$dataArray['HTML_Map'] = str_replace('/kegg-bin/', 'http://www.kegg.jp/kegg-bin/', $dataArray['HTML_Map']);
		$dataArray['HTML_Map'] = str_replace('shape=circle', "shape='circle'", $dataArray['HTML_Map']);
		$dataArray['HTML_Map'] = str_replace('shape=rect', "shape='rect'", $dataArray['HTML_Map']);
		$dataArray['HTML_Map'] = str_replace('<area ', "<area target='_blank' ", $dataArray['HTML_Map']);
		$dataArray['HTML_Map'] = str_replace('onmouseover', 'onmouseoverX', $dataArray['HTML_Map']);
		$dataArray['HTML_Map'] = str_replace('onmouseout', 'onmouseoutX', $dataArray['HTML_Map']);
		
		$dataArray['processed'] = 1;
	}
	
	
	return $dataArray;
	
}


function validateKEGGIdentifier($identifier){
	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['KEGG_Pathway'];
		
	$SQL = "SELECT `Identifier` FROM `{$SQL_TABLE}` WHERE `Identifier` = '{$identifier}'";
	$SQL_RESULTS = getSQL($SQL, 'GetOne', $SQL_TABLE);
	
	if ($SQL_RESULTS == ''){
		return false;	
	} else {
		return true;	
	}
}

function getKEGGName($identifier){
	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['KEGG_Pathway'];
		
	$SQL = "SELECT `Name` FROM `{$SQL_TABLE}` WHERE `Identifier` = '{$identifier}'";
	$SQL_RESULTS = getSQL($SQL, 'GetOne', $SQL_TABLE);
	
	return $SQL_RESULTS;
}


function getKEGGMenu(){
	global $APP_CONFIG;
	
	$SQL_TABLE 	= $APP_CONFIG['Table']['KEGG_Pathway'];
		
	$SQL = "SELECT `Identifier`, `Name` FROM `{$SQL_TABLE}` ORDER BY `Name`";
	$SQL_RESULTS = getSQL($SQL, 'GetAssoc', $SQL_TABLE);
	
	return $SQL_RESULTS;
	
}

function guessKEGG($value){
	
	global $ORDER_ARRAY;
	
	$value = trim($value);
	
	$value = str_replace('  ', ' ', $value);
	
	$value = strtolower($value);
	
	if ($value != ''){
		$getKEGGMenu 	= getKEGGMenu();
		$getKEGGMenuOrg = $getKEGGMenu;
		
		$getKEGGMenu = array_map('strtolower', $getKEGGMenu);
		
		$getKEGGMenuReverse = array_flip($getKEGGMenu);
		
		if ($getKEGGMenuReverse[$value] != ''){
			$results['results'] = 1;
			$results['ID'] 		= $getKEGGMenuReverse[$value];
			$results['Name'] 	= $getKEGGMenuOrg[$results['ID']];
			$found = 1;
		} else {
			
			$valueCleaned = preg_replace('/[^\da-z]/i', '', $value);
			
			foreach($getKEGGMenuReverse as $candidateName => $candidateID){
				
				$candidateNameCleaned = preg_replace('/[^\da-z]/i', '', strtolower($candidateName));
				
				if ($candidateNameCleaned == $valueCleaned){
					$results['results'] = 1;
					$results['ID'] 		= $candidateID;
					$results['Name'] 	= $getKEGGMenuOrg[$candidateID];
					$found = 1;
					break;
				} else {
					
					$editDistances[$candidateID]['ID'] 			= $candidateID;
					$editDistances[$candidateID]['Name'] 		= $getKEGGMenuOrg[$editDistances[$candidateID]['ID']];
					$editDistances[$candidateID]['Distance'] 	= levenshtein($valueCleaned, $candidateNameCleaned);
					
				}
			}
			
		}
		
		if (!$found && isset($editDistances)){
			$ORDER_ARRAY['Distance'] = 'ASC';
			
			naturalSort2DArray($editDistances);
			
			$results['results'] = 2;
			$results['Array']	= array_slice($editDistances, 0, 10);
		}
		
		return $results;
	}
	
}


function visualizeKEGG($comparisonIndexes, $comparisonNames, $KEGG_Identifier, $visualizeOption, $dataSource, $internalProjectIndexes){
	
	global $APP_CONFIG, $BXAF_CONFIG;

	$comparisonIDs 		= array_clean($comparisonIDs);	
	$visualizeOption	= intval($visualizeOption);
	
	if (($visualizeOption > 3) || ($visualizeOption < 1)){
		$visualizeOption = 1;
	}
	
	cleanInternalDataInput($dataSource, $internalProjectIndexes);
	$internalProjectIndexString = implode(',', $internalProjectIndexes);
	
	$cacheKey 	= __FUNCTION__ . '::' . md5(json_encode($comparisonIndexes) . '::' . 
											$KEGG_Identifier . '::' . 
											$visualizeOption . '::' . 
											'2019-01-19 02:03');
											
	$dirKey		= str_replace('::', '_', $cacheKey);
	
	$resultsFromCache = getSQLCache($cacheKey);
	
	if ($resultsFromCache !== false){
		return $resultsFromCache;
	}
	
	unset($comparisonDataFiles);
	if ($dataSource['public'] != ''){
		$sql_table			= 'ComparisonData';
		$comparisonDataFiles[] = tabix_search_records_with_index('',           $comparisonIndexes, $sql_table, 'Path', $tabixFilePath);
	}
	
	if ($dataSource['private'] != ''){
		foreach($internalProjectIndexes as $tempKey => $projectIndex){
			$sql_table				= 'ComparisonData';
			$comparisonDataFiles[] 	= tabix_search_records_with_index_internal_data($projectIndex, '',           $comparisonIndexes, $sql_table, 'Path', $tabixFilePath);
		}
	}
	
	
	
	
	/*
        (
            [ComparisonIndex] => 4215
            [GeneIndex] => 0
            [Name] => DDX11L1
            [Log2FoldChange] => 0.0016
            [PValue] => 9.979e-001
            [AdjustedPValue] => 1.000e+000
            [NumeratorValue] => -5.6875
            [DenominatorValue] => -5.6875
        )
		
		Array
		(
			[0] => 4215
			[1] => 0
			[2] => DDX11L1
			[3] => 0.0016
			[4] => 9.979e-001
			[5] => 1.000e+000
			[6] => -5.6875
			[7] => -5.6875
		)
	*/
	
	startTimer(__FUNCTION__ . '::fopen');
	unset($index, $geneIndexes);
	foreach($comparisonDataFiles as $comparisonDataFileKey => $comparisonDataFile){
		if (($handle = fopen($comparisonDataFile, 'r')) !== FALSE) {
			while (($buffer = fgets($handle, 1000)) !== FALSE){
				
				$tempValue	= explode("\t", trim($buffer));
				
				$currentComparisonIndex = intval($tempValue[0]);
				$currentGeneIndex	 	= intval($tempValue[1]);
				$currentGeneName		= trim($tempValue[2]);
				$currentLogFC			= trim($tempValue[3]);
				$currentPValue			= trim($tempValue[4]);
				$currentAdjustedPValue	= trim($tempValue[5]);
				
				$geneIndexes[$currentGeneIndex] = $currentGeneIndex;
				
				if (($currentLogFC === '') || is_null($currentLogFC) || ($currentLogFC === '.') || !is_numeric($currentLogFC)){
					$currentLogFC = 'NA';
				}
				
				if (($currentPValue === '') || is_null($currentPValue) || ($currentPValue === '.') || !is_numeric($currentPValue)){
					$currentPValue = 'NA';
				}
				
				
				unset($temp);
				$temp['ComparisonIndex'] 	= $currentComparisonIndex;
				$temp['GeneIndex'] 			= $currentGeneIndex;
				$temp['Name'] 				= $currentGeneName;
				$temp['Log2FoldChange'] 	= $currentLogFC;
				$temp['PValue'] 			= $currentPValue;
				$temp['AdjustedPValue'] 	= $currentAdjustedPValue;
				
	
				
				$currentIndex = intval($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['currentIndex']) + 1;
				
				$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['currentIndex'] = $currentIndex;
				
				$comparisonInfo[$currentComparisonIndex] = 1;
				
				
				if ($currentPValue != 'NA'){
					$index[$currentGeneIndex]['Summary']['hasNumeric'] = 1;	
				}
				
				
				if (!isset($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min-PValue'])){
					$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min-PValue']	= $currentPValue;
					$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min_index'] 	= $currentIndex;
					$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']			= $temp;
				} else {
					
					if ($currentPValue != 'NA'){
						if ($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min-PValue'] == 'NA'){
							$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min-PValue']	= $currentPValue;
							$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min_index'] 	= $currentIndex;
							$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']			= $temp;	
						} elseif ($currentPValue < $index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min-PValue']){
							$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min-PValue']	= $currentPValue;
							$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min_index'] 	= $currentIndex;
							$index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']			= $temp;	
						}
					}
				}
				
			}
			fclose($handle);
		}
	}
	startTimer(__FUNCTION__ . '::fopen');
	
	
	
	foreach($comparisonNames as $comparisionIndex => $comparisonName){
		if (!$comparisonInfo[$currentComparisonIndex]){
			unset($comparisonNames[$comparisionIndex]);
		}
	}
	
	
	startTimer(__FUNCTION__ . '::GeneAnnotation');
	if (true){
		$sql_table 			= 'GeneCombined';
		$geneIndexesString 	= implode(',', $geneIndexes);
		
		if ($geneIndexesString == ''){
			return false;	
		}
		
		$SQL 				= "SELECT `GeneIndex`, `EntrezID`, `GeneName`, `Description` FROM {$sql_table} WHERE `GeneIndex` IN ({$geneIndexesString}) AND (`EntrezID` != '')";
		$geneInfo	 		= getSQL($SQL, 'GetAssoc', $sql_table);
		
		foreach($geneInfo as $currentGeneIndex => $tempValue){
			
			if ($tempValue['EntrezID'] == '') continue;
			
			if (!isset($entrezIDLookup[$tempValue['EntrezID']])){
				$entrezIDLookup[$tempValue['EntrezID']] = $tempValue;
				$entrezIDLookup[$tempValue['EntrezID']]['GeneIndex'] = $currentGeneIndex;
			}
		}
	}
	startTimer(__FUNCTION__ . '::GeneAnnotation');
	
	
	startTimer(__FUNCTION__ . '::prepareInputFile');
	if (true){
		
		$path			 = "{$BXAF_CONFIG['WORK_DIR']}KEGG_Pathway/{$BXAF_CONFIG['APP_PROFILE']}/{$dirKey}/";
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}
		$results['Summary']['Path'] = $path;
		
		unset($inputFileName);
		if (array_size($comparisonName) == 1){
			$inputFileName = "comp1_logFC.csv";
		} else {
	
			unset($currentIndex);
			foreach($comparisonNames as $comparisionIndex => $comparisonName){
				$currentIndex++;
				$inputFileName[] = "{$currentIndex}_{$comparisionIndex}";
			}	
			
			$inputFileName = "comp" . implode('_', $inputFileName) . '_logFC.csv';
			
		}
		
		$inputFilePath = "{$path}{$inputFileName}";
		
		$results['Input']['file'] = $inputFileName;
		$results['Input']['path'] = $inputFilePath;
	
		
		$fp = fopen($inputFilePath, 'w');
	
		unset($headers);	
		$headers[] = 'geneID';
		foreach($comparisonNames as $comparisionIndex => $comparisonName){
			$headers[] = $comparisonName;
		}
		fputcsv($fp, $headers);
		unset($headers);	
		
		
		foreach($index as $currentGeneIndex => $tempValue1){
			unset($values);
			
			if (trim($geneInfo[$currentGeneIndex]['EntrezID']) == '') continue;
			
			if (!$index[$currentGeneIndex]['Summary']['hasNumeric']) continue;
	
			$results['Summary']['hasInput'] = 1;
			
			$values[] = $geneInfo[$currentGeneIndex]['EntrezID'];
			
			foreach($comparisonNames as $currentComparisonIndex => $comparisonName){
				
				if (isset($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['Log2FoldChange'])){
					$values[] = $index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['Log2FoldChange'];
				} else {
					$values[] = 'NA';
				}
			}
			
			fputcsv($fp, $values);
		}
		fclose($fp);
	}
	startTimer(__FUNCTION__ . '::prepareInputFile');
	
	
	
	
	if (!$results['Summary']['hasInput']){
		return false;	
	}
	
	
	startTimer(__FUNCTION__ . '::R');
	if (true){
		$results['R-Command'] = "{$BXAF_CONFIG['RSCRIPT_BIN']} {$BXAF_CONFIG['KEGG']['Script']}/draw_kegg.R {$inputFileName} {$KEGG_Identifier} {$visualizeOption} {$BXAF_CONFIG['KEGG_Path']}";
		
		$results['Bash'] = "{$path}run.sh";
		
		$bash = "cd {$path};\n
		{$results['R-Command']};\n";
		
		file_put_contents($results['Bash'], $bash);
		chmod($results['Bash'], 0755);
		
		shell_exec($results['Bash']);
	}
	startTimer(__FUNCTION__ . '::R');
	
	
	startTimer(__FUNCTION__ . '::postProcessing');
	if (true){
		$results['Output']['Files'] = scandir($path);
		$results['Output']['png'] = glob("{$path}*.png");
		$results['Output']['png'] = trim($results['Output']['png'][0]);
		
		if (file_exists($results['Output']['png'])){
			$results['Summary']['hasPNG'] = true;
		}
		
		$results['Output']['EntrezID_File'] = glob("{$path}*.list");
		$results['Output']['EntrezID_File'] = trim($results['Output']['EntrezID_File'][0]);
		
		
		if (file_exists($results['Output']['EntrezID_File'])){
			$entrezIDs = file_get_contents($results['Output']['EntrezID_File']);
			$results['Output']['EntrezID'] = splitData($entrezIDs);
			
			if (array_size($results['Output']['EntrezID'])){
				$results['Summary']['hasEntrezID'] = 1;
			} else {
				$results['Summary']['hasEntrezID'] = 0;
			}
			
			
			if ($results['Summary']['hasEntrezID']){
				$dataTableHTML['Headers'][] 	= 'Gene ID<br/>&nbsp;';
				$dataTablePrint['Headers'][] 	= 'GeneName';
				
				$dataTableHTML['Headers'][] 	= 'Description<br/>&nbsp;';
				$dataTablePrint['Headers'][] 	= 'Description';
				
				foreach($comparisonNames as $comparisionID => $comparisonName){
					$dataTableHTML['Headers'][] = "{$comparisonName} <br/> Log<sub>2</sub>(Fold Change)";
					$dataTableHTML['Headers'][] = "{$comparisonName} <br/> P-Value";
					$dataTableHTML['Headers'][] = "{$comparisonName} <br/> FDR";
					
					
					$dataTablePrint['Headers'][] = "{$comparisonName} (Log2 Fold Change)";
					$dataTablePrint['Headers'][] = "{$comparisonName} (P-Value)";
					$dataTablePrint['Headers'][] = "{$comparisonName} (FDR)";
				}
				
				$results['Output']['Headers'] = $dataTableHTML['Headers'];
				
				
				unset($currentIndex);
				foreach($results['Output']['EntrezID'] as $tempKey => $entrezID){
					$currentGeneIndex 	= $entrezIDLookup[$entrezID]['GeneIndex'];
					$geneIDDisplay		= $entrezIDLookup[$entrezID]['GeneName'];
					
					$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$currentGeneIndex}";
					$dataTableHTML['Body'][$entrezID][] 	= "<a title='{$geneIDDisplay}' href='{$URL}' target='_blank'>{$geneIDDisplay}</a>";
					$dataTablePrint['Body'][$entrezID][] 	= $geneIDDisplay;
					
					
					$dataTableHTML['Body'][$entrezID][] 	= displayLongText($entrezIDLookup[$entrezID]['Description']);
					$dataTablePrint['Body'][$entrezID][] 	= $entrezIDLookup[$entrezID]['Description'];
					
					foreach($comparisonNames as $currentComparisonIndex => $comparisonName){
						
						if (isset($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['Log2FoldChange'])){
							
							$value = $index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['Log2FoldChange'];
							$color = getStatScaleColor($value, 'Log2FoldChange');
							
							$dataTableHTML['Body'][$entrezID][] 	= "<span is_numeric='true' style='color:{$color};'>{$value}</span>";
							$dataTablePrint['Body'][$entrezID][] 	= $value;
						} else {
							$dataTableHTML['Body'][$entrezID][] 	= 'N/A';
							$dataTablePrint['Body'][$entrezID][] 	= 'N/A';
						}
						
						if (isset($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['PValue'])){
							
							$value = $index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['PValue'];
							$color = getStatScaleColor($value, 'PValue');
							
							$dataTableHTML['Body'][$entrezID][] 	= "<span is_numeric='true' style='color:{$color};'>{$value}</span>";
							$dataTablePrint['Body'][$entrezID][] 	= $value;
						} else {
							$dataTableHTML['Body'][$entrezID][] 	= 'N/A';
							$dataTablePrint['Body'][$entrezID][] 	= 'N/A';
						}
						
						
						if (isset($index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['AdjustedPValue'])){
							
							$value = $index[$currentGeneIndex]['Comparison'][$currentComparisonIndex]['min']['AdjustedPValue'];
							$color = getStatScaleColor($value, 'AdjustedPValue');
							
							$dataTableHTML['Body'][$entrezID][] 	= "<span is_numeric='true' style='color:{$color};'>{$value}</span>";
							$dataTablePrint['Body'][$entrezID][] 	= $value;
						} else {
							$dataTableHTML['Body'][$entrezID][] 	= 'N/A';
							$dataTablePrint['Body'][$entrezID][] 	= 'N/A';
						}
						
						
						 
					}
					
				}
			
			}
		}
		
		if (isset($dataTableHTML)){
			$results['Output']['dataTableHTMLKey'] 	= putSQLCacheWithoutKey($dataTableHTML, '', 'dataTableHTMLKey', 1);
			$results['Output']['dataTablePrintKey'] = putSQLCacheWithoutKey($dataTablePrint, '', 'dataTablePrintKey', 1);
		}
		
		if ($inputFilePath != ''){
			unset($file);
			$file['Path'] 					= $inputFilePath;
			$file['ContentType'] 			= 'text/csv; charset=utf-8';
			$file['Attachment_Filename'] 	= $inputFileName;
			$results['Output']['RInputKey'] = putSQLCacheWithoutKey($file, '', 'RInputKey', 1);
		}
	}
	startTimer(__FUNCTION__ . '::postProcessing');
	
	
	
	startTimer(__FUNCTION__ . '::wrapUp');
	if (true){
		putSQLCache($cacheKey, $results, '', __FUNCTION__);
	}
	startTimer(__FUNCTION__ . '::wrapUp');
	

	return $results;
	
}

function getStatScaleColor($value, $type) {
	
	if (is_numeric($value)){
		
		$value = floatval($value);
	
		if ($type == 'Log2FoldChange') {
			if ($value >= 1) {
			  return '#FF0000';
			} elseif ($value > 0) {
			  return '#FF8989';
			} elseif ($value == 0) {
			  return '#E5E5E5';
			} elseif ($value > -1) {
			  return '#7070FB';
			} else {
			  return '#0000FF';
			}
		} elseif ($type == 'AdjustedPValue') {
			if ($value > 0.05) {
			  return '#9CA4B3';
			} elseif ($value <= 0.01) {
			  return '#015402';
			} else {
			  return '#5AC72C';
			}
		} elseif ($type == 'PValue') {
			if ($value >= 0.01) {
			  return '#9CA4B3';
			} else {
			  return '#5AC72C';
			}
		} elseif ($type == 'ZScore') {
			if ($value > 1) {
			  return '#FF0000';
			} elseif ($value > 0) {
			  return '#FF9C9C';
			} elseif ($value == 0) {
			  return '#979797';
			} elseif ($value > -1) {
			  return '#81C86E';
			} else {
			  return '#02CA2D';
			}
		}
	}
	
	return '#000';
}


?>