<?php

$currentTable = 'GeneCount';

$APP_CONFIG['Internal_Data'][$currentTable]['Name'] 		= 'Gene Count';
$APP_CONFIG['Internal_Data'][$currentTable]['Example'] 		= 'download/Gene_Count.csv';
$APP_CONFIG['Internal_Data'][$currentTable]['Table'] 		= 'App_User_Data_GeneCount';
$APP_CONFIG['Internal_Data'][$currentTable]['File_Not_Exist_Message'] 	= 'Gene count is missing. Please provide a gene count file.';


$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['ensembl_geneid']	= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['id']				= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_id']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['geneid']			= 'Gene';

$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_count';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_counts';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'count';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'counts';


/*
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_name']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['genename']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_symbol']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['symbol']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['identifier']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_identifier']	= 'Gene';
*/

$currentHeader = 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Gene ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['ImportOnly']	= 1;

$currentHeader = 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['ImportOnly']	= 1;



?>