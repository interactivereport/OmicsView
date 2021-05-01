<?php
/*
//Private
ApoE3_Astr_SCI_Brdg_d20.vs.hiPSC_Astr_SCI_d30_P9
ApoE3_Astr_SCI_Brdg_d20.vs.hiPSC_Astr_TCS_d20_P9
ApoE3_Astr_SCI_Brdg_d20.vs.hiPSC_NPC_P9                

//Public
GSE99963.GPL16791.DESeq2.test2
GSE99963.GPL16791.DESeq2.test3
GSE99963.GPL16791.DESeq2.test4
*/



$_POST['logfc_cutoff'] = floatval($_POST['logfc_cutoff']);
$_POST['base_mean_cutoff_value'] = floatval($_POST['base_mean_cutoff_value']);


if ($_POST['logfc_cutoff'] <= 0) {
	echo 'Error: Log<sub></sub>(Fold Change) cutoff needs to be positive.';
	exit();
}

if ($_POST['base_mean_cutoff_value'] < 0) {
	echo 'Error: Expression level cutoff needs to be positive.';
	exit();
}

$COMPARISONS       	= general_array_clean(explode("\n", $_POST['comparisons']));
$DIRECTION         	= $_POST['direction'];
$LOGFC_CUTOFF      	= $_POST['logfc_cutoff'];
$CUTOFF_Category	= $_POST['cutoff_category'];
$CUTOFF_VALUE      	= $_POST['cutoff_value'];
$ALL_GENES 			= array();
$sampleString		= '';
$_POST['biotype']	= trim($_POST['biotype']);



//Get All Comparisons
if (true){
	//general_start_timer('Get Comparisons');
	
	$COMPARISON_LIST = array();
	unset($rawData);
	foreach ($COMPARISONS as $tempKey => $comparison_id) {
		$comparison_id = trim($comparison_id);
		
		if ($comparison_id != '') {
			$results = search_one_record_by_name('comparison', $comparison_id, 'GetRow', '*');
			
			if (general_array_size($results) > 0){
				$COMPARISON_LIST[$results['ComparisonIndex']] = $results['ComparisonID'];
				$rawData[$results['ComparisonIndex']] = $results;
				
				$comparison_to_samples[$results['ComparisonIndex']]['Control_SampleIDs'] = $results['Control_SampleIDs'];
				$comparison_to_samples[$results['ComparisonIndex']]['Case_SampleIDs'] = $results['Case_SampleIDs'];
				
				$sampleString .= ';' . $results['Control_SampleIDs'] . ';' . $results['Case_SampleIDs'];
			}
		}
	}
	//general_start_timer('Get Comparisons');
}


//Get Comparison Tabix Data
if (true){
	//general_start_timer('Comparisons Tabix');
	
	foreach ($COMPARISON_LIST as $comparison_index => $comparison_id) {
		
		$genes = array();
		$comparisonIndex = array($comparison_index);
		
		if (internal_data_is_public($comparison_index)){
			$tabix_result = tabix_search_records_with_index('', $comparisonIndex, 'ComparisonData');
		} else {
			$projectIndex = $rawData[$comparison_index]['ProjectIndex'];
			$tabix_result = tabix_search_records_with_index_internal_data($projectIndex, '', $comparisonIndex, 'ComparisonData');
		}
		
		foreach ($tabix_result as $tempKeyX => $comparisonData) {
			
			$currentGeneIndex 		= $comparisonData['GeneIndex'];
			$currentComparisonIndex = $comparisonData['ComparisonIndex'];
			
			$passed = 0;
			
			if (!is_numeric($comparisonData[$CUTOFF_Category])){
				continue;	
			} elseif ($comparisonData[$CUTOFF_Category] >= $CUTOFF_VALUE) {
				continue;
			} elseif (!is_numeric($comparisonData['Log2FoldChange'])){
				continue;
			} else {
				
				$geneIDs[$currentGeneIndex] = $currentGeneIndex;
				
				if (!isset($indexAll[$currentGeneIndex][$currentComparisonIndex]['Filter']['Log2FC'])){
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['Filter']['Log2FC'] = 0;
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['Log2FoldChange'] = $comparisonData['Log2FoldChange'];
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['PValue'] 		= $comparisonData['PValue'];
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['AdjustedPValue'] = $comparisonData['AdjustedPValue'];
					
				}
				

				if ($DIRECTION == 'up') {
					if ($comparisonData['Log2FoldChange'] >= $LOGFC_CUTOFF) {
						$passed = 1;
					}
				} else if ($DIRECTION == 'down') {
					if ($comparisonData['Log2FoldChange'] <= (-1)*$LOGFC_CUTOFF && $comparisonData['Log2FoldChange'] <= 0) {
						$passed = 1;
					}
				} else {
					if (abs($comparisonData['Log2FoldChange']) >= $LOGFC_CUTOFF) {
						$passed = 1;
					}
				}
			
			}
			
			if ($passed){
				
				
				unset($qualified);
				if (!isset($score[$currentGeneIndex][$currentComparisonIndex])){
					$qualified = 1;
				} elseif (is_numeric($comparisonData['PValue']) && ($comparisonData['PValue'] < $score[$currentGeneIndex][$currentComparisonIndex])){
					$qualified = 1;
				} elseif (is_numeric($comparisonData['PValue']) && !is_numeric($comparisonData[$currentGeneIndex][$currentComparisonIndex])){
					$qualified = 1;
				}
				
				if ($qualified){
					$score[$currentGeneIndex][$currentComparisonIndex] 						= $comparisonData['PValue'];
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['Log2FoldChange'] = $comparisonData['Log2FoldChange'];
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['PValue'] 		= $comparisonData['PValue'];
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['AdjustedPValue'] = $comparisonData['AdjustedPValue'];
					$indexAll[$currentGeneIndex][$currentComparisonIndex]['Filter']['Log2FC'] = 1;
				}
			
			}
			
		}
		
		
		$comparison_to_samples[$comparison_index]['Control_SampleIDs'] = explode(';', $comparison_to_samples[$comparison_index]['Control_SampleIDs']);
		$comparison_to_samples[$comparison_index]['Case_SampleIDs'] = explode(';', $comparison_to_samples[$comparison_index]['Case_SampleIDs']);
		
	}
	//general_start_timer('Comparisons Tabix');
}



//Get Gene Info
if (general_array_size($geneIDs)){
	//general_start_timer('Gene');
	$allGenes = get_multiple_record('gene', $geneIDs, 'GetAssoc');
	//general_start_timer('Gene');
} else {
	
	echo "Error. The comparison data does not contain any information.";
	exit();	
}


if (($_POST['biotype'] != 'BXAPP_ALL') && ($_POST['biotype'] != '')){
	//general_start_timer('Filter Gene by Biotype');
	foreach($allGenes as $tempKey => $tempValue){
	
		if ($_POST['biotype'] == 'BXAPP_None'){
			$biotype = '';
		} else {
			$biotype = $_POST['biotype'];
		}
	
	
		if (trim($biotype) != trim($tempValue['Biotype'])){
			unset($allGenes[$tempKey]);
		}
	}
	//general_start_timer('Filter Gene by Biotype');
}


if ($_POST['base_mean_cutoff_value'] > 0){
	
	$hasBaseMean = 1;
	if ($sampleString != ''){
		
		$sampleArray = general_array_clean(explode(';', $sampleString));
		
		if (general_array_size($sampleArray) > 0){
			
			//general_start_timer('Get Sample SQL');
			$sampleStringForSQL = "'" . implode("','", $sampleArray) . "'";
			$SQL = "SELECT `SampleID`, `SampleIndex` FROM `App_User_Data_Samples_Combined` WHERE `SampleID` IN ({$sampleStringForSQL})";
			$sampleID_To_sampleIndex = $DB->GetAssoc($SQL);
			//general_start_timer('Get Sample SQL');
			
			
			
			
			//general_start_timer('Get Sample Tabix Data');
			$tabixFile = tabix_search_records_with_index_all(array_keys($allGenes), array_values($sampleID_To_sampleIndex), 'GeneFPKM', 'Path');
			//general_start_timer('Get Sample Tabix Data');
			
			
			
			
			if (!is_file($tabixFile)){
				echo 'Error: The tabix results cannot be read. Please contact the administrator for details.';
				exit();
			}
			

			//general_start_timer('Reading Sample Tabix Data');
			if (true){
				
				//SampleIndex	GeneIndex	FPKM	GeneCount
				//Data is always sorted by GeneIndex
				$readCount = 0;
				$fp = fopen($tabixFile, 'r');
				while (!feof($fp)){
					$currentLine = fgets($fp, 1000000);
						
					if (trim($currentLine) == '') continue;
					if (strpos('#', $currentLine) === 0) continue;
					
					$currentRow = explode("\t", $currentLine);
					
					$tabixIndex['Gene-Sample'][($currentRow[1])][($currentRow[0])] = $currentRow[2];
					
					$readCount++;
					
				}
				fclose($fp);
			}
			//general_start_timer('Reading Sample Tabix Data');
			
			
			
			
			if ($readCount <= 0){
				echo 'Error: The tabix result is empty. Please contact the administrator for details.';
				exit();
			}
			
			

			//general_start_timer('Calculating Base mean');
			foreach ($COMPARISON_LIST as $comparison_index => $comparison_id){
				
				$currentSampleIDs = array();
				$currentSampleIDs['Control'] = $comparison_to_samples[$comparison_index]['Control_SampleIDs'];
				$currentSampleIDs['Case'] = $comparison_to_samples[$comparison_index]['Case_SampleIDs'];
				
					
				$currentSampleIndexes = array();
				foreach($currentSampleIDs['Control'] as $tempKeyX => $currentSampleID){
					$currentSampleIndexes['Control'][] = $sampleID_To_sampleIndex[$currentSampleID];
				}
					
				foreach($currentSampleIDs['Case'] as $tempKeyX => $currentSampleID){
					$currentSampleIndexes['Case'][] = $sampleID_To_sampleIndex[$currentSampleID];
				}
				
				
				foreach ($allGenes as $currentGeneIndex => $geneInfo) {
					
					foreach($currentSampleIndexes['Control'] as $tempKeyX => $currentSampleIndex){
						$indexAll[$currentGeneIndex][$comparison_index]['Average_Control'][] = $tabixIndex['Gene-Sample'][$currentGeneIndex][$currentSampleIndex];
					}
					
					foreach($currentSampleIndexes['Case'] as $tempKeyX => $currentSampleIndex){
						$indexAll[$currentGeneIndex][$comparison_index]['Average_Case'][] = $tabixIndex['Gene-Sample'][$currentGeneIndex][$currentSampleIndex];
					}
					
					
					$indexAll[$currentGeneIndex][$comparison_index]['Average_Control'] = general_calculate_mean($indexAll[$currentGeneIndex][$comparison_index]['Average_Control']);
					$indexAll[$currentGeneIndex][$comparison_index]['Average_Control'] = round($indexAll[$currentGeneIndex][$comparison_index]['Average_Control'], 3);

					$indexAll[$currentGeneIndex][$comparison_index]['Average_Case'] = general_calculate_mean($indexAll[$currentGeneIndex][$comparison_index]['Average_Case']);
					$indexAll[$currentGeneIndex][$comparison_index]['Average_Case'] = round($indexAll[$currentGeneIndex][$comparison_index]['Average_Case'], 3);
					
					$indexAll[$currentGeneIndex][$comparison_index]['Max_Base_Mean'] = max($indexAll[$currentGeneIndex][$comparison_index]['Average_Control'], $indexAll[$currentGeneIndex][$comparison_index]['Average_Case']);
					
					$indexAll[$currentGeneIndex][$comparison_index]['Min_Base_Mean'] = min($indexAll[$currentGeneIndex][$comparison_index]['Average_Control'], $indexAll[$currentGeneIndex][$comparison_index]['Average_Case']);
					
					
					if ($_POST['base_mean_cutoff_value'] > 0){
					
						if ($_POST['base_mean'] == 'case'){
							$currentBaseMean = $indexAll[$currentGeneIndex][$comparison_index]['Average_Case'];
						} elseif ($_POST['base_mean'] == 'control'){
							$currentBaseMean = $indexAll[$currentGeneIndex][$comparison_index]['Average_Control'];
						} elseif ($_POST['base_mean'] == 'max'){
							$currentBaseMean = $indexAll[$currentGeneIndex][$comparison_index]['Max_Base_Mean'];
						} else {
							$currentBaseMean = $indexAll[$currentGeneIndex][$comparison_index]['Min_Base_Mean'];
						}
						
						if ($currentBaseMean < $_POST['base_mean_cutoff_value']){
							$testResult[$currentGeneIndex][$comparison_index] = 0;
							$indexAll[$currentGeneIndex][$comparison_index]['Filter']['Base_Mean'] = 0;
						
						} else {
							$testResult[$currentGeneIndex][$comparison_index] = 1;	
							$indexAll[$currentGeneIndex][$comparison_index]['Filter']['Base_Mean'] = 1;
						}
					
					}
				}
			}
			//general_start_timer('Calculating Base mean');
			
		}
		
	}
}

//echo general_printr($COMPARISON_LIST);
//echo general_printr($indexAll);


$filterCount = 1;

if ($_POST['base_mean_cutoff_value'] > 0){
	$filterCount++;	
}



//general_start_timer('Comparison_method');
foreach ($allGenes as $currentGeneIndex => $geneInfo) {
	
	$count_comparison_passes_filter = 0;
	
	$comparisonCount = 0;

	foreach($indexAll[$currentGeneIndex] as $comparison_index => $tempValue){
		$comparisonCount++;
		
		$no_filter_passed = array_sum($indexAll[$currentGeneIndex][$comparison_index]['Filter']);
		if ($no_filter_passed == $filterCount){
			$count_comparison_passes_filter++;	
		}
	}
	
	
	
	
	$gene_passed = false;
	if ($_POST['comparison_method'] == 'or'){
		if ($count_comparison_passes_filter > 0){
			$gene_passed = true;
		}
	} elseif ($_POST['comparison_method'] == 'and'){
		if ($count_comparison_passes_filter == $comparisonCount){
			$gene_passed = true;
		}
	}
	

	if (!$gene_passed){
		unset($allGenes[$currentGeneIndex]);
	}
  
}


//general_start_timer('Basemean Logic');

//echo general_process_timer(NULL, 1);
  
if (true){
	$count 	= general_array_size($allGenes);
	if ($count <= 0){
		echo "<div class='alert alert-warning mt-1 mb-3 w-100'>The system could not find any {$APP_MESSAGE['genes']}. Please revise your search option and try again.</div>";
		exit();
	}
}
  
if (true){
  $sessionKeyForGeneList = md5(microtime(true) . '_GeneList_' . rand(0, 1000));
  unset($_SESSION['List'][$sessionKeyForGeneList]);
  
  $sessionKeyForMeta = md5(microtime(true) . '_Meta_' . rand(0, 1000));
  unset($_SESSION['App']['meta_analysis'][$sessionKeyForGeneMeta]);
}
  
if (true){
	echo "<div class='mt-1 mb-3 w-100'>";
		echo "<hr/><br/>";
		
		echo "<div>";
			$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Gene&Session={$sessionKeyForGeneList}";
			echo "<a href='{$URL}' target='_blank' class='btn btn-success'><i class='fa fa-fw fa-file-o' aria-hidden='true'></i> Create a {$APP_MESSAGE['Gene']} List</a>";
			
			echo '&nbsp; &nbsp;';
			
			$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/meta_analysis/index.php?Session={$sessionKeyForMeta}";
			echo "<a href='{$URL}' target='_blank' class='btn btn-info'><i class='fa fa-fw fa-file-o' aria-hidden='true'></i> Create a Meta Analysis</a>";
		echo "</div>";
	
	echo "</div>";

	$count = number_format($count);
	echo "<div class='mt-1 mb-3 w-100'><h4>List of {$APP_MESSAGE['Significantly Changed Genes']} ({$count})</h4></div>";
}


if (true){
	unset($table);
	$table .= "<table class='table table-bordered table-striped' id='table'>";
		$table .= "<thead>";
			$table .= "<tr>";
			$table .= "<th class='text-nowrap'>{$APP_MESSAGE['Gene ID']}</th>";
			$table .= "<th>Description</th>";
			$table .= "<th>Biotype</th>";
	
			foreach ($COMPARISON_LIST as $comparison_index => $comparison_id){
				$table .= "<th class='text-nowrap'>{$comparison_id}<br/>Log<sub>2</sub> Fold Change</th>";
				$table .= "<th class='text-nowrap'>{$comparison_id}<br/>p-value</th>";
				$table .= "<th class='text-nowrap'>{$comparison_id}<br/>Adjusted p-value</th>";
	
				if ($hasBaseMean){
					$table .= "<th class='text-nowrap'>{$comparison_id}<br/>Average Case {$APP_MESSAGE['RPKM/TPM']}</th>";
					$table .= "<th class='text-nowrap'>{$comparison_id}<br/>Average Control {$APP_MESSAGE['RPKM/TPM']}</th>";
				}
			}
			$table .= "</tr>";
		$table .= "</thead>";
		
		
		$table .= "<tbody>";
	
			$GENE_INDEX_LIST = array();
			foreach ($allGenes as $geneIndex => $geneInfo) {
				$table .= "<tr>";
					$table .= "<td class='text-nowrap'><a href='../search_comparison/single_comparison.php?type=gene&id={$geneIndex}' target='_blank'>{$geneInfo['GeneID']}</a></td>";
					$table .= "<td class='text-nowrap'>{$geneInfo['Description']}</td>";
					$table .= "<td class='text-nowrap'>{$geneInfo['Biotype']}</td>";
		
					$_SESSION['List'][$sessionKeyForGeneList][] = $geneInfo['GeneName'];
					$_SESSION['App']['meta_analysis'][$sessionKeyForMeta]['Gene'][] = $geneInfo['GeneName'];
	
					foreach ($COMPARISON_LIST as $comparison_index => $comparison_id){
	
						$_SESSION['App']['meta_analysis'][$sessionKeyForMeta]['Comparison'][$comparison_index] = $comparison_id;
	
						$value = $indexAll[$geneIndex][$comparison_index]['Log2FoldChange'];
						$color = get_stat_scale_color($value, 'logFC');
						$table .= "<td style='color:{$color};'>{$value}</td>";
						
						
						$value = $indexAll[$geneIndex][$comparison_index]['PValue'];
						$color = get_stat_scale_color($value, 'PVal');
						$table .= "<td style='color:{$color};'>{$value}</td>";
						
						$value = $indexAll[$geneIndex][$comparison_index]['AdjustedPValue'];
						$color = get_stat_scale_color($value, 'FDR');
						$table .= "<td style='color:{$color};'>{$value}</td>";
						
						if ($hasBaseMean){
							$value = $indexAll[$geneIndex][$comparison_index]['Average_Case'];
							$table .= "<td>{$value}</td>";
						
							$value = $indexAll[$geneIndex][$comparison_index]['Average_Control'];
							$table .= "<td>{$value}</td>";
						}
	
					}
	
	
				$table .= "</tr>";
			}
		
		$table .= '</tbody>';
		
	$table .= '</table>';
	
	echo $table;
}
  

  exit();


?>