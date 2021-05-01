<?php
include_once('config_init.php');

$PAGE['Title'] 		= "KEGG Visualization";
$PAGE['Header']		= "KEGG Visualization";
$PAGE['Category']	= "Comparison Plotting Tools";

$PAGE['URL']		= 'app_KEGG_pathway.php';
$PAGE['Body'] 		= 'app_KEGG_pathway_content.php';
$PAGE['EXE'] 		= 'app_KEGG_pathway_exe.php';
$PAGE['Barcode']	= 'app_KEGG_pathway.php';

$PAGE['Plugins']['dataTables'] 		= 1;
$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');

?>