<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Export Genes and Samples'];
$PAGE['Header']		= $APP_MESSAGE['Export Genes and Samples'];
$PAGE['Category']	= "Gene Expression Plots";

$PAGE['URL']		= 'app_export_genes_samples.php';
$PAGE['Body'] 		= 'app_export_genes_samples_content.php';
$PAGE['EXE'] 		= 'app_export_genes_samples_exe.php';
$PAGE['Barcode']	= 'app_export_genes_samples.php';

$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['selectPicker'] 	= 1;

include('page_generator.php');

?>