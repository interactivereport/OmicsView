<?php

$currentTable = 'Samples';

$APP_CONFIG['Internal_Data'][$currentTable]['Name'] 		= 'Sample Info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example'] 	= 'download/Sample_Info.csv';
$APP_CONFIG['Internal_Data'][$currentTable]['Table'] 	= 'App_User_Data_Samples';
$APP_CONFIG['Internal_Data'][$currentTable]['File_Not_Exist_Message'] 	= 'Sample information is missing. Please provide a sample info file.';

$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map_SampleID'] = array('Sample_ID', 'sample_id', 'sampleid', 'sampleID', 'name', 'Name', 'SampleName', 'sample_name');

$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['samplename']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sample_name']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sample name']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sample_id']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sample id']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sampleid']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sample.id']		= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['title']			= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['name']			= 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['sex']				= 'Gender';

$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'sample';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'samples';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'sample_info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'samples_info';


$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['project_name']	= 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['projectname']	 	= 'ProjectID';

$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['platform_name']		= 'PlatformName';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['platform']		 	= 'PlatformName';



$currentHeader = 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;


$currentHeader = 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Optional_if_Single_Project'] = 1;

$currentHeader = 'ProjectIndex';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project Index';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['LinkToTable']	= 'App_User_Data_Projects';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['LinkToField']	= 'ProjectIndex';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['HideFromImport']= 1;

$currentHeader = 'PlatformGPL';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform GPL';

$currentHeader = 'PlatformName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Name';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Optional_if_Single_Project'] = 1;


$currentHeader = 'BamFileName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Bam File Name';

$currentHeader = 'CellType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Cell Type';

$currentHeader = 'Collection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Collection';

$currentHeader = 'Description';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Description';

$currentHeader = 'DiseaseStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Disease Stage';

$currentHeader = 'DiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Disease State';

$currentHeader = 'Ethnicity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Ethnicity';

$currentHeader = 'Gender';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Gender';

$currentHeader = 'Infection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Infection';

$currentHeader = 'Organism';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Organism';

$currentHeader = 'Response';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Response';

$currentHeader = 'SampleIntegrationID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample Integration ID';

$currentHeader = 'SamplePathology';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample Pathology';

$currentHeader = 'SampleSource';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample Source';

$currentHeader = 'SampleType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample Type';

$currentHeader = 'SamplingTime';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sampling Time';

$currentHeader = 'Symptom';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Symptom';

$currentHeader = 'Tissue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Tissue';

$currentHeader = 'Title';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Title';

$currentHeader = 'Transfection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Transfection';

$currentHeader = 'Treatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Treatment';

$currentHeader = 'SubjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Subject ID';

$currentHeader = 'Age';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Age';

$currentHeader = 'DiseaseSubgroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Disease Subgroup';

$currentHeader = 'DiseaseGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Disease Group';

?>