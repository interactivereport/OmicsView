<?php
include_once('config_init.php');

$category 		= $_POST['Category'];


if ($APP_CONFIG['APP']['List_Category'][$category]['Table_Combined'] != ''){
	include('app_record_browse_component_exe_combined.php');
} else {
	include('app_record_browse_component_exe_regular.php');
}



?>