<?php

include_once('config_init.php');
$comparisonID 	= intval($_GET['id']);
$file			= $_GET['file'];
$direction 		= $_GET['direction'];
if ($direction != 'Up'){
	$direction = 'Down';
}


header("Location: ../bxgenomics/summary/report_enrichment.php?id={$comparisonID}&direction={$direction}");
exit();

?>