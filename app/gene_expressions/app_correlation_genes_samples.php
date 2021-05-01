<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Correlation Tools Using Gene Expression'];
$PAGE['Header']		= $APP_MESSAGE['Correlation Tools Using Gene Expression'];
$PAGE['Category']	= "Gene Expression Plots";

$PAGE['URL']		= 'app_correlation_genes_samples.php';
$PAGE['Body'] 		= 'app_correlation_genes_samples_content.php';
$PAGE['EXE'] 		= 'app_correlation_genes_samples_exe.php';
$PAGE['Barcode']	= 'app_correlation_genes_samples.php';

$PAGE['Plugins']['selectPicker'] = 1;
$PAGE['Plugins']['canvasxpress'] = 1;


include('page_generator.php');

?>