<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Pairwise View of Samples";
$PAGE['Header']		= "Pairwise View of Samples";
$PAGE['Category']	= "Review";

$PAGE['URL']		= 'app_pairwise_view.php';
$PAGE['Body'] 		= 'app_pairwise_view_content.php';
$PAGE['EXE'] 		= 'app_pairwise_view_exe.php';
$PAGE['Barcode']	= 'app_pairwise_view.php';

$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['canvasxpress']	= 1;
$PAGE['Disable_Left_Menu'] 			= 1;

$BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT'] = 'col-12 col-md-12 col-lg-12 col-xl-12 d-flex align-content-between flex-wrap';

include('page_generator.php');

?>