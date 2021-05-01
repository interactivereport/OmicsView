<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Gene Details'];
$PAGE['Header']		= $APP_MESSAGE['Gene Details'];
$PAGE['Category']	= "Review";

$PAGE['URL']		= 'app_gene_review.php';
$PAGE['Body'] 		= 'app_gene_review_content.php';
$PAGE['Barcode']	= 'app_record_browse.php?Category=Gene';

$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress'] 	= 0;
$PAGE['Plugins']['selectPicker'] 	= 1;

$recordCategory = 'Gene';

$dataArray = get_one_record_by_id($recordCategory, $_GET['ID']);


$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Default'];
$identifier		= $dataArray[$identifierSQL];

if ($identifier != ''){
	$PAGE['Header'] = "{$PAGE['Header']}: {$identifier}";	
}


include('page_generator.php');

?>