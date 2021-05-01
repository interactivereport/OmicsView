<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Review Internal Data";
$PAGE['Header']		= "Review Internal Data";
$PAGE['Category']	= "List";

$PAGE['URL']		= 'app_internal_data_browse.php';
$PAGE['Body'] 		= 'app_internal_data_browse_content.php';
$PAGE['Barcode']	= 'Import Internal Data';


$PAGE['Plugins']['canvasxpress'] = 0;

include('page_generator.php');

?>