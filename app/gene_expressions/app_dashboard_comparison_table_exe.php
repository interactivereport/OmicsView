<?php

include_once('config_init.php');

if (array_size($_POST['ComparisonIndex']) > 0){
	
	$comparisonIndexes = $_POST['ComparisonIndex'];
	
	
	$internal_data_split_multiple_data_by_source = internal_data_split_multiple_data_by_source($comparisonIndexes);
	
	unset($_POST);
	
	
	
	
	$_POST['Category'] = 'Comparison';
	$_POST['data_source'][0] = 'public';
	$_POST['data_source'][1] = 'private';
	$_POST['data_source_private_project_indexes'] = array_keys(internal_data_get_accessible_project());
	
	
	$_POST['Field_1'] 		= 'ComparisonIndex';
	$_POST['Operator_1'] 	= 5;
	$_POST['Value_1'] 		= implode(',', $comparisonIndexes);
	$_POST['rowCount']		= 1;
	$_POST['API']			= 1;
	$_POST['URL']			= '';
	$_POST['bookmark']		= 0;
	
	
	if (array_size($internal_data_split_multiple_data_by_source['private']) <= 0){
		$_POST['fast'] = 1;	
		unset($_POST['data_source'][1]);
	}
	
	
	
	include('app_record_browse_component_exe.php');
}



?>