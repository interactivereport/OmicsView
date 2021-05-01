<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Meta Analysis Using Gene Expression Data'];
$PAGE['Header']		= $APP_MESSAGE['Meta Analysis Using Gene Expression Data'];
$PAGE['Category']	= "My Results";

$PAGE['URL']		= 'app_meta_analysis2.php';
$PAGE['Body'] 		= 'app_meta_analysis2_content.php';
$PAGE['EXE'] 		= 'app_meta_analysis2_exe.php';
$PAGE['Barcode']	= 'app_meta_analysis2.php';

$PAGE['Plugins']['dataTables'] 		= 0;
$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['stupidTable'] 	= 0;
$PAGE['Plugins']['canvasxpress'] 	= 0;

if (!$APP_CONFIG['APP']['Module']['Comparison']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');

?>