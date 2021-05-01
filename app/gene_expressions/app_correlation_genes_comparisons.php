<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Correlation Tools Using Comparisons";
$PAGE['Header']		= "Correlation Tools Using Comparisons";
$PAGE['Category']	= "Comparison Plotting Tools";

$PAGE['URL']		= 'app_correlation_genes_comparisons.php';
$PAGE['Body'] 		= 'app_correlation_genes_comparisons_content.php';
$PAGE['EXE'] 		= 'app_correlation_genes_comparisons_exe.php';
$PAGE['Barcode']	= 'app_correlation_genes_comparisons.php';

$PAGE['Plugins']['selectPicker'] = 1;
$PAGE['Plugins']['canvasxpress']	= 1;


if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');

?>