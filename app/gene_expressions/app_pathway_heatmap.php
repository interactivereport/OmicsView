<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Pathway Heatmap Tool";
$PAGE['Header']		= "Pathway Heatmap Tool";
$PAGE['Category']	= "Comparison Plotting Tools";

$PAGE['URL']		= 'app_pathway_heatmap.php';
$PAGE['Barcode']	= 'app_pathway_heatmap.php';
$PAGE['Body'] 		= 'app_pathway_heatmap_content.php';
$PAGE['EXE'] 		= 'app_pathway_heatmap_exe.php';

$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['dataTables']		= 0;
$PAGE['Plugins']['plotly']			= 1;
$PAGE['Plugins']['canvasxpress']	= 0;
include('page_generator.php');

?>