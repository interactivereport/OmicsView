<?php
include_once('config_init.php');

$PAGE['Title'] 		= "About";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "System Settings";

$PAGE['URL']		= 'admin_about.php';
$PAGE['Barcode']	= 'admin_about.php';
$PAGE['Body'] 		= 'admin_about_content.php';
$PAGE['EXE'] 		= '';


include('page_generator.php');

?>