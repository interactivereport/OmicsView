<?php
include_once('../assets/config.php');

if (isset($_GET['action']) && $_GET['action'] == 'set_displayed_options') {
	include('exe_set_displayed_options.php');
	exit();	
}


if (isset($_GET['action']) && $_GET['action'] == 'get_data_table') {
	include('exe_get_data_table.php');
	exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'get_significantly_changed_genes') {
	include('exe_get_significantly_changed_genes');
	exit();	
}



if (isset($_GET['action']) && $_GET['action'] == 'show_changed_genes_table'){
	include('exe_show_changed_genes_table.php');
	exit();
}



if (isset($_GET['action']) && $_GET['action'] == 'save_selected_comparisons') {
	include('exe_save_selected_comparisons.php');
	exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'save_selected_samples') {
	include('exe_save_selected_samples.php');
	exit();	
	
}


if (isset($_GET['action']) && $_GET['action'] == 'save_genes') {
	include('exe_save_genes.php');
	exit();
}




if (isset($_GET['action']) && $_GET['action'] == 'get_json_data') {
	include('exe_get_json_data.php');
	exit();	
}


if (isset($_GET['action']) && $_GET['action'] == 'reorder_charts') {
	include('exe_reorder_charts.php');
	exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'get_file_from_work_dir') {
	
}


?>