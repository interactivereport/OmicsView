<?php

include('config_init.php');

$_GET['inputType'] 	= strtolower($_GET['inputType']);
$_GET['outputType'] = strtolower($_GET['outputType']);
$_GET['inputName']	= strtolower($_GET['inputName']);

if ($_GET['inputType'] == ''){
	$needAlert = 1;	
}

if ($_GET['inputName'] == ''){
	$needAlert = 1;
}


if (strpos($_GET['inputName'], '|') !== FALSE){
	$temp = explode('|', $_GET['inputName']);
	$_GET['inputName'] = $temp[0];
}


$record = search_one_record_by_name($_GET['inputType'], $_GET['inputName'], 'GetRow', '*');
if (array_size($record) <= 0){
	$needAlert = 1;	
	
	if (strpos($_GET['inputName'], '_') !== FALSE){
		
		$temp = explode('_', $_GET['inputName']);
		
		
		
		$tempCount = array_size($temp);
		
		if (is_numeric($temp[$tempCount - 1])){
			unset($temp[$tempCount - 1]);
			$_GET['inputName'] = implode('_', $temp);
			$record = search_one_record_by_name($_GET['inputType'], $_GET['inputName'], 'GetRow', '*');
			
			if (array_size($record) > 0){
				$needAlert = 0;	
			}
			
		}
		
		
		
		
	}
}





if ($needAlert){
	echo "Error. Please verify your URL and try again.";
	exit();
}
	
if ($_GET['inputType'] == 'sample'){
	$sampleIndex = $record['SampleIndex'];
	
	if ($_GET['outputType'] == 'sample'){
		
		$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=sample&id={$sampleIndex}";
		
	} elseif ($_GET['outputType'] == 'project'){
		$record = search_one_record_by_name('Project', $record['ProjectName'], 'GetRow', '*');
		if (array_size($record) > 0){
			$projectIndex = $record['ProjectIndex'];
			$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=project&id={$projectIndex}";
		}

	}
}


if ($URL == ''){
	echo "Error. Please verify your URL and try again.";
} else {
	header("Location: {$URL}");
}

exit();

?>