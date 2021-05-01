<?php

function getSampleIDFromProjectIndex($projectIndex){
	
	if (internal_data_is_public($projectIndex)){
	
		$projectInfo = get_multiple_record('project', $projectIndex, 'GetRow');
	
		$projectID = $projectInfo['ProjectID'];
	
		if ($projectID == '') return false;
	
	
		$samples = search_all_records('sample', '*', "`ProjectName` = '{$projectID}'", 'GetAssoc');
		
	} else {
		
		$samples = search_all_records('sample', '*', "`ProjectIndex` = '{$projectIndex}'", 'GetAssoc');
	}
	
	$sampleIDs = array_column($samples, 'SampleID');
	
	return $sampleIDs;
	
}

function getSampleIDFromProjectIndexes($input){
	
	$projectIndexes = array_keys($input);
	
	
	$sources = internal_data_split_multiple_data_by_source($projectIndexes);	
	
	
	if (array_size($sources['public']) > 0){
		
		$projectIDs = array();
		foreach($sources['public'] as $tempKey => $currentProjectIndex){
			$projectIDs[] = $input[$currentProjectIndex];
		}

		$projectIDString = '"' . implode('","', $projectIDs) . '"';
		
		$samples = search_all_records('sample', '*', "`ProjectName` IN ({$projectIDString})", 'GetAssoc');
	}
	
	if (array_size($sources['private']) > 0){
		$projectIndexStr = implode(',', $sources['private']);
		$temp = search_all_records('sample', '*', "`ProjectIndex` IN ({$projectIndexStr})", 'GetAssoc');
		
		foreach($temp as $tempKey => $tempValue){
			$samples[$tempKey] = $tempValue;
		}
	}

	
	$sampleIDs = array_column($samples, 'SampleID');
	
	return $sampleIDs;
	
}

function getSampleRecordsFromProjectIndex($projectIndex){
	
	if (internal_data_is_public($projectIndex)){
	
		$projectInfo = get_multiple_record('project', $projectIndex, 'GetRow');
	
		$projectID = $projectInfo['ProjectID'];
	
		if ($projectID == '') return false;
	
	
		$samples = search_all_records('sample', '*', "`ProjectName` = '{$projectID}'", 'GetAssoc');
		
	} else {
		
		$samples = search_all_records('sample', '*', "`ProjectIndex` = '{$projectIndex}'", 'GetAssoc');
	}
	
	return $samples;
	
}

function getComparisonIDFromProjectIndex($projectIndex){
	
	if (internal_data_is_public($projectIndex)){
	
		$projectInfo = get_multiple_record('project', $projectIndex, 'GetRow');
	
		$projectID = $projectInfo['ProjectID'];
	
		if ($projectID == '') return false;
	
	
		$comparisons = search_all_records('comparison', '*', "`ProjectName` = '{$projectID}'", 'GetAssoc');
		
	} else {
		
		$comparisons = search_all_records('comparison', '*', "`ProjectIndex` = '{$projectIndex}'", 'GetAssoc');

	}
	
	$comparisonIDs = array_column($comparisons, 'ComparisonID');
	
	return $comparisonIDs;
	
	
}

function getComparisonRecordsFromProjectIndex($projectIndex){
	
	if (internal_data_is_public($projectIndex)){
	
		$projectInfo = get_multiple_record('project', $projectIndex, 'GetRow');
	
		$projectID = $projectInfo['ProjectID'];
	
		if ($projectID == '') return false;
	
	
		$comparisons = search_all_records('comparison', '*', "`ProjectName` = '{$projectID}'", 'GetAssoc');
		
	} else {
		
		$comparisons = search_all_records('comparison', '*', "`ProjectIndex` = '{$projectIndex}'", 'GetAssoc');

	}
	
	return $comparisons;

}

function getDatasetIDFromProjectIndex($projectIndex){
	
	if (true){
		
		$datasets = search_all_records('dataset', '*', "`ProjectIndex` = '{$projectIndex}'", 'GetAssoc');
	}
	
	$datasetIDs = array_column($datasets, 'DatasetID');
	
	return $datasetIDs;
	
}

function getDatasetIDFromProjectIndexes($input){
	
	$projectIndexes = array_keys($input);
	
	
	$sources = internal_data_split_multiple_data_by_source($projectIndexes);	
	
	if (array_size($sources['private']) > 0){
		$projectIndexStr = implode(',', $sources['private']);
		$temp = search_all_records('dataset', '*', "`ProjectIndex` IN ({$projectIndexStr})", 'GetAssoc');
		
		foreach($temp as $tempKey => $tempValue){
			$datasets[$tempKey] = $tempValue;
		}
	}

	
	$datasetIDs = array_column($datasets, 'DatasetID');
	
	return $datasetIDs;
	
}

function getDatasetRecordsFromProjectIndex($projectIndex){
	
	if (true){
		$datasets = search_all_records('dataset', '*', "`ProjectIndex` = '{$projectIndex}'", 'GetAssoc');
	}
	
	return $datasets;
	
}

?>