<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Bubble Plot (Single Gene)'];
$PAGE['Header']		= 'Bubble Plot';
$PAGE['Category']	= 'Comparison Plotting Tools';

$PAGE['URL']		= 'app_bubble_plot_single.php';
$PAGE['Body'] 		= 'app_bubble_plot_single_content.php';
$PAGE['EXE'] 		= 'app_bubble_plot_single_exe.php';
$PAGE['Barcode'] 	= 'app_bubble_plot_single.php';

$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['plotly']			= 1;
$PAGE['Plugins']['canvasxpress']	= 0;
$PAGE['Plugins']['stupidTable']		= 1;


include('page_generator.php');

?>