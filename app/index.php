<?php

include_once('config.php');


$URL = $BXAF_CONFIG['Home_Page'];

if ($URL == ''){
	$URL = 'gene_expressions/index.php';	
}



header("Location: {$URL}");
exit();

?>