<?php

function fix_ensembl_id($gene = NULL, $species = ''){
	
	global $BXAF_CONFIG;
	
	$gene = trim($gene);
	
	if ($gene == '') return false;
	
	if ($species == ''){
		$species = $BXAF_CONFIG['APP_SPECIES'];	
	}
	
	$species = strtolower($species);
	
	/*
	Human
	ENSG*.
	
	Mouse
	ENSMUSG
	
	Rat
	ENSRNOG
	*/
	
	
	if (strpos($gene, '.') !== FALSE){
		if ($species == 'human'){
			if (strpos(strtolower($gene), 'ensg') === 0){
				$needExplode = 1;
			}
		} elseif ($species == 'mouse'){
			if (strpos(strtolower($gene), 'ensmusg') === 0){
				$needExplode = 1;
			}
		} elseif ($species == 'rat'){
			if (strpos(strtolower($gene), 'ensrnog') === 0){
				$needExplode = 1;
			}
		}
	}

	if ($needExplode){
		$temp = explode('.', $gene);
		$gene = $temp[0];
	} elseif (!$needExplore && (get_gene_type() == 'Protein')){
		
		//Input: Q9BUR5-1
		//Output: Q9BUR5
		
		$temp = explode('-', $gene);
		
		if (ctype_digit(strval($temp[1]))){
			$gene = $temp[0];
		}
		
	}
	
	return $gene;
	
}

function guess_gene_name($gene = NULL, $species = '', $useStandardName = 0){
	
	global $BXAF_CONFIG;
	
	$gene = trim($gene);
	
	if ($gene == '') return false;
	
	if ($species == ''){
		$species = $BXAF_CONFIG['APP_SPECIES'];	
	}
	
	$species = strtolower($species);
	
	$gene = fix_ensembl_id($gene, $species);
	
	if ($useStandardName){
		$species = ucwords(strtolower($species));
		
		$conn	= bxaf_get_app_db_connection();
		$sql 	= "SELECT `GeneIndex` FROM `Gene_Lookup_{$species}` WHERE `ID` = '{$gene}'";
		$geneIndex = $conn->GetOne($sql);
		
		if ($geneIndex !== false){
			$sql	= "SELECT `GeneName` FROM `GeneCombined` WHERE `GeneIndex` = '{$geneIndex}'";
			$gene = $conn->GetOne($sql);
		} else {
			
			$sql	= "SELECT `GeneName` FROM `GeneCombined` WHERE (`GeneID` = '{$gene}') OR (`GeneName` = '{$gene}')";
			$gene = $conn->GetOne($sql);
		}
	}
	
	$gene = trim($gene);
	
	
	return $gene;	
	
}


function search_gene_index($gene_name = NULL){
	
	global $BXAF_CONFIG;
	
	$gene_name = trim($gene_name);
	
	if ($gene_name == '') return false;
	
	$species = $BXAF_CONFIG['APP_SPECIES'];	
	$species = ucwords(strtolower($species));
	
	$gene_name = guess_gene_name($gene_name);
	
	$gene_name = addslashes($gene_name);
	
	if ($gene_name == '') return -1;
	
	$conn		= bxaf_get_app_db_connection();
	
	$sql = "SELECT `GeneIndex` FROM `Gene_Lookup_{$species}` WHERE `ID` = '{$gene_name}'";
	$geneIndex = $conn->GetOne($sql);
	
	if ($geneIndex === false){
		$geneIndex = -1;
	}
	
	return $geneIndex;
	
}


function search_gene_indexes($gene_names = NULL){
	
	global $BXAF_CONFIG, $BAXF_CACHE;
	
	$cacheKey = json_encode($gene_names);
	
	if (isset($BAXF_CACHE[__FUNCTION__][$cacheKey])){
		return $BAXF_CACHE[__FUNCTION__][$cacheKey];
	}
	
	$gene_names = general_array_clean($gene_names);
	if (general_array_size($gene_names) <= 0) return false;
	
	$species = $BXAF_CONFIG['APP_SPECIES'];	
	$species = ucwords(strtolower($species));
	
	$valueString = array();
	foreach($gene_names as $tempKey => $gene_name){
		$valueString[] = "'" . addslashes(trim(fix_ensembl_id($gene_name, $species))) . "'";
	}
	$valueString 	= implode(', ', $valueString);
	
	$conn		= bxaf_get_app_db_connection();
	
	$SQL = "SELECT `ID`, `GeneIndex`  FROM `Gene_Lookup_{$species}` WHERE `ID` IN ({$valueString}) ORDER BY FIELD(ID, {$valueString})";	
	
	$results['Lookup'] = $conn->GetAssoc($SQL);
	
	if (general_array_size($results['Lookup']) != general_array_size($gene_names)){
		
		foreach($results['Lookup'] as $geneName => $geneIndex){
			$index['ID'][strtolower($geneName)]['GeneIndex'] = $geneIndex;
			$index['ID'][strtolower($geneName)]['ID'] 		= $geneName;
		}
		
		foreach($gene_names as $tempKey => $geneName){
			if (!isset($index['ID'][strtolower($geneName)])){
				$notFound[] = addslashes(strtolower($geneName));
			}
		}
		
		if (general_array_size($notFound) > 0){
			
			$searchConditons = "'" . implode("','", $notFound) . "'";
			
			$SQL = "SELECT `GeneIndex`, `GeneID`, `GeneName` FROM `GeneCombined` WHERE (`GeneID` IN ({$searchConditons})) OR (`GeneName`  IN ({$searchConditons}))";
			
			$sql_results = $conn->GetAssoc($SQL);
			
			foreach($sql_results as $geneIndex => $tempValue){
				$index['GeneID'][strtolower($tempValue['GeneID'])]['GeneID'] = $tempValue['GeneID'];
				$index['GeneID'][strtolower($tempValue['GeneID'])]['GeneIndex'] = $geneIndex;
				
				$index['GeneName'][strtolower($tempValue['GeneName'])]['GeneName'] = $tempValue['GeneName'];
				$index['GeneName'][strtolower($tempValue['GeneName'])]['GeneIndex'] = $geneIndex;

			}
			
			foreach($gene_names as $tempKey => $geneName){
				
				if (isset($index['ID'][strtolower($geneName)])){
					$standardGeneName 	= $index['ID'][strtolower($geneName)]['ID'];
					$geneIndex			= $index['ID'][strtolower($geneName)]['GeneIndex'];
					$finalResults[$standardGeneName] = $geneIndex;
				} elseif (isset($index['GeneID'][strtolower($geneName)])){
					$standardGeneName 	= $index['GeneID'][strtolower($geneName)]['GeneID'];
					$geneIndex			= $index['GeneID'][strtolower($geneName)]['GeneIndex'];
					$finalResults[$standardGeneName] = $geneIndex;
				} elseif (isset($index['GeneName'][strtolower($geneName)])){
					$standardGeneName 	= $index['GeneName'][strtolower($geneName)]['GeneName'];
					$geneIndex			= $index['GeneName'][strtolower($geneName)]['GeneIndex'];
					$finalResults[$standardGeneName] = $geneIndex;
				}
				
				
			}

		}
	} else {
		$finalResults = $results['Lookup'];	
	}
	
	$BAXF_CACHE[__FUNCTION__][$cacheKey] = $finalResults;
	
	return $finalResults;
	
}


function gene_uses_TPM(){

	global $BXAF_CONFIG, $BAXF_CACHE;
	
	if (!$BXAF_CONFIG['HAS_TPM_DATA']) return false;
	
	if (isset($BAXF_CACHE[__FUNCTION__])){
		return $BAXF_CACHE[__FUNCTION__];
	}
	
	if (isset($_SESSION['User_Settings']['Gene_Data_Type'])){
		if ($_SESSION['User_Settings']['Gene_Data_Type'] == 'TPM'){
			$results = true;
		} else {
			$results = false;
		}
		
		return $results;
	}
	
	$conn 			= bxaf_get_app_db_connection();
	$userID			= intval($_SESSION['User_Info']['ID']);
	
	$SQL 		= "SELECT `Detail` FROM `UserPreference` WHERE (`User_ID` = {$userID}) AND `Category` = 'Gene_Data_Type'";
	$results 	= $conn->GetOne($SQL);
	
	if ($results == ''){
		$SQL 		= "SELECT `Detail` FROM `UserPreference` WHERE (`User_ID` = -1) AND `Category` = 'Gene_Data_Type'";
		$results 	= $conn->GetOne($SQL);
	}
	
	if ($results == ''){
		
		$results = $BXAF_CONFIG['USE_TPM_ALWAYS'];

	} else {
	
		$results = unserialize($results);
		
		if ($results == 'TPM'){
			$results = true;
		} else {
			$results = false;
		}
		
	}
	
	$BAXF_CACHE[__FUNCTION__] = $results;
		
	return $results;
	
}

function get_internal_gene_viewer_url($input = NULL){
	
	global $BXAF_CONFIG;
	
	$BXAF_CONFIG['Gene_Viewer_Internal'] = trim($BXAF_CONFIG['Gene_Viewer_Internal']);
	
	if ($BXAF_CONFIG['Gene_Viewer_Internal'] != ''){
		return "{$BXAF_CONFIG['Gene_Viewer_Internal']}{$input}";
	} else {
		return false;	
	}
	
}

function get_internal_gene_viewer_text(){
	
	global $BXAF_CONFIG;
	
	return trim($BXAF_CONFIG['Gene_Viewer_Internal_Text']);
}


function get_gene_symbols_from_comparison_GO($comparisonIndex = NULL, $direction = NULL, $category = NULL, $term = NULL){
	
	$term = trim(strtolower($term));
	if ($term == '') return false;

    $file = internal_data_get_comparison_homer_file($comparisonIndex, $direction, $category);

	if (is_file($file)){
		
		$fp = fopen($file, 'r');
		
		while (!feof($fp)){
			$currentLine = fgets($fp, 1000000);
			
			if (trim($currentLine) == '') continue;
			if (strpos('#', $currentLine) === 0) continue;

			$currentRow = explode("\t", $currentLine);
			
			if (trim(strtolower($currentRow[1])) == $term) {
				fclose($fp);
				
				$currentRow[10] = trim($currentRow[10]);
				
				if (($currentRow[10] != '') && ($currentRow[10] != '""')){
					$genes = explode(',', $currentRow[10]);
					return general_array_clean($genes);
				} else {
					return false;	
				}
			}
		}
		
		fclose($fp);

	}
	
	return false;
	
}

function get_gene_symbols_from_comparison_PAGE($geneset = NULL){
	
	$geneset = addslashes(trim($geneset));
	if ($geneset == '') return false;
	
	$SQL 	= "SELECT `Members` FROM `GeneSet` WHERE `StandardName` = '{$geneset}' LIMIT 1";
	
	$conn 	= bxaf_get_app_db_connection();
	$genes 	= $conn->GetOne($SQL);
	
	if ($genes != ''){
		$genes = explode(',', $genes);
		$genes = general_array_clean($genes);
		return $genes;
	} else {
		return false;	
	}
	

	


}

?>