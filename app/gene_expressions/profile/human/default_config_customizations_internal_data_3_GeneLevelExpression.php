<?php

$currentTable = 'GeneLevelExpression';

$APP_CONFIG['Internal_Data'][$currentTable]['Name'] 		= 'Gene Level Expression';
$APP_CONFIG['Internal_Data'][$currentTable]['Example'] 	= 'download/Gene_Expression_Data.csv';
$APP_CONFIG['Internal_Data'][$currentTable]['Table'] 	= 'App_User_Data_GeneLevelExpression';
$APP_CONFIG['Internal_Data'][$currentTable]['File_Not_Exist_Message'] 	= 'Gene level expression is missing. Please provide a gene level expression file.';


$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['ensembl_geneid']	= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['id']				= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_id']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['geneid']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_name']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['genename']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_symbol']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['symbol']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['identifier']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_identifier']	= 'Gene';


$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'genes';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'genes_info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_data';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'genes_data';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_expression';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_expressions';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_expression_data';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_expressions_data';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'genelevelexpression';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_level_expression';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'gene_expression_level';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'protein_expression_level';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'proteins';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'protein_data';






$currentHeader = 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Gene ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['ImportOnly']	= 1;

$currentHeader = 'SampleID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['ImportOnly']	= 1;



?>