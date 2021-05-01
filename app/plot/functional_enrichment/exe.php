<?php

include_once('config.php');


if (isset($_GET['action']) && $_GET['action'] == 'show_chart') {
	include('exe_show_chart.php');
	exit();
}


// Go To Volcano Plot
if (isset($_GET['action']) && $_GET['action'] == 'go_to_volcano') {
	include('exe_go_to_volvano.php');
	exit();
}


// Save Genes
if (isset($_GET['action']) && $_GET['action'] == 'save_genes') {
	include('exe_save_genes.php');
	exit();
}



?>
