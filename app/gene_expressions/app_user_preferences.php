<?php
include_once('config_init.php');

$PAGE['Title'] 		= 'Personal Preferences';
$PAGE['Header']		= 'Personal Preferences';
$PAGE['Category']	= "Settings";

$PAGE['URL']		= "app_user_preferences.php";
$PAGE['Barcode']	= "Personal Preferences";
$PAGE['Body'] 		= 'app_user_preferences_component.php';
$PAGE['EXE'] 		= 'app_user_preferences_exe.php';

$PAGE['Plugins']['Sortable'] 	= 1;

include('page_generator.php');

?>