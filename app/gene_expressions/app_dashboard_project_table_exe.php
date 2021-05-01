<?php

include_once('config_init.php');


if (array_size($_POST['ProjectIndex']) > 0){
	
	$comparisonIndexes = $_POST['ProjectIndex'];
	
	unset($_POST);
	
	$_POST['Category'] = 'Project';
	$_POST['data_source'][0] = 'public';
	$_POST['data_source'][1] = 'private';
	$_POST['data_source_private_project_indexes'] = array_keys(internal_data_get_accessible_project());
	
	
	$_POST['Field_1'] 		= 'ProjectIndex';
	$_POST['Operator_1'] 	= 5;
	$_POST['Value_1'] 		= implode(',', $comparisonIndexes);
	$_POST['rowCount']		= 1;
	$_POST['API']			= 1;
	$_POST['URL']			= '';
	$_POST['bookmark']		= 0;
	
	
	include('app_record_browse_component_exe.php');
}



?>