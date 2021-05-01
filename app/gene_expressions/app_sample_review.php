<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Review Sample Data";
$PAGE['Header']		= "Review Sample Data";
$PAGE['Category']	= "Review";

$PAGE['URL']		= 'app_sample_review.php';
$PAGE['Body'] 		= 'app_sample_review_content.php';
$PAGE['Barcode']	= 'app_record_browse.php?Category=Sample';

$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress'] 	= 0;

$recordCategory = 'Sample';

$dataArray = get_one_record_by_id($recordCategory, $_GET['ID']);

$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Human'];
$identifier		= $dataArray[$identifierSQL];

if ($identifier != ''){
	$PAGE['Header'] = "{$PAGE['Header']}: {$identifier}";	
}


include('page_generator.php');

?>