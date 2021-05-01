<?php
include_once('config_init.php');

$category			= $_GET['Category'];

$PAGE['Title'] 		= "List of Meta Analyses Using Gene Expression Data";
$PAGE['Header']		= "List of Meta Analyses Using Gene Expression Data";
$PAGE['Category']	= "My Results";

$PAGE['URL']		= "app_meta_analysis2_browse.php";
$PAGE['Barcode']	= "app_meta_analysis2_browse.php";
$PAGE['Body'] 		= 'app_meta_analysis2_browse_component.php';
$PAGE['EXE'] 		= '';

if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');


?>