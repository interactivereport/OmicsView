<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Review Internal Data";
$PAGE['Header']		= "Review Internal Data";
$PAGE['Category']	= "List";

$PAGE['URL']		= 'app_internal_data_review.php';
$PAGE['Body'] 		= 'app_internal_data_review_content.php';
$PAGE['Barcode']	= 'Import Internal Data';

$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress'] 	= 0;

$debug 				= $_GET['debug'];

include('page_generator.php');

?>