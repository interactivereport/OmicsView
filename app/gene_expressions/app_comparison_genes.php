<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Comparison Gene Info";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "Review";

$PAGE['URL']		= 'app_comparison_genes.php';
$PAGE['Barcode']	= 'app_comparison_genes.php';
$PAGE['Body'] 		= 'app_comparison_genes_content.php';
$PAGE['EXE'] 		= 'app_comparison_genes_exe.php';

$PAGE['Plugins']['dataTables'] 		= 1;
$PAGE['Plugins']['selectPicker']	= 0;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;


$dataArray = get_multiple_record('comparison', $_GET['ID'], 'GetRow', '*', 0);

if (array_size($dataArray) > 0){
	$PAGE['Title'] 	= "Comparison Gene Info of {$dataArray['ComparisonID']}";
	$PAGE['Header']	= $PAGE['Title'];
}

include('page_generator.php');

?>