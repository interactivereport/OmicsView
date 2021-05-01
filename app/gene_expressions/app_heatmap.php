<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Heatmap";
$PAGE['Header']		= "Heatmap";
$PAGE['Category']	= "Gene Expression Plots";

$PAGE['URL']		= 'app_heatmap.php';
$PAGE['Barcode']	= 'app_heatmap.php';
$PAGE['Body'] 		= 'app_heatmap_content.php';
$PAGE['EXE'] 		= 'app_heatmap_exe.php';

$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress']	= 1;

include('page_generator.php');

?>