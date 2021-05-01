<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Review Project Data";
$PAGE['Header']		= "Review Project Data";
$PAGE['Category']	= "Review";

$PAGE['URL']		= 'app_project_review.php';
$PAGE['Body'] 		= 'app_project_review_content.php';
$PAGE['Barcode']	= 'app_record_browse.php?Category=Project';

$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress'] 	= 0;

$recordCategory = 'Project';

$dataArray = get_one_record_by_id($recordCategory, $_GET['ID']);


$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Human'];
$identifier		= $dataArray[$identifierSQL];

if ($identifier != ''){
	$PAGE['Header'] = "{$PAGE['Header']}: {$identifier}";	
}


include('page_generator.php');

?>