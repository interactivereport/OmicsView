<?php
include_once('config_init.php');

$PAGE['Title'] 		= "Clear Application Caches";
$PAGE['Header']		= $PAGE['Title'];
$PAGE['Category']	= "Settings";

$PAGE['URL']		= 'admin_clear_caches.php';
$PAGE['Barcode']	= 'admin_clear_caches.php';
$PAGE['Body'] 		= 'admin_clear_caches_content.php';
$PAGE['EXE'] 		= '';


include('page_generator.php');

?>