<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Export Genes and Comparisons'];
$PAGE['Header']		= $APP_MESSAGE['Export Genes and Comparisons'];
$PAGE['Category']	= "Comparison Plotting Tools";

$PAGE['URL']		= 'app_export_genes_comparisons.php';
$PAGE['Body'] 		= 'app_export_genes_comparisons_content.php';
$PAGE['EXE'] 		= 'app_export_genes_comparisons_exe.php';
$PAGE['Barcode']	= 'app_export_genes_comparisons.php';

$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['selectPicker'] 	= 1;

if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');

?>