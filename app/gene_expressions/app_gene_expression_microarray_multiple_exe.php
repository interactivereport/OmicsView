<?php
include_once('config_init.php');

$currentTable 		= 'Samples';
$geneValueTable 	= 'GeneLevelExpression';
$toolPlatformType	= 'Microarray';
$geneValueColumn 	= 'Value';

$valueText = 'Value';

if ($_POST['transform']){
	$formula			= "log2({$valueText})";
} else {
	$formula			= $valueText;
}


$pageTitle			= 'Multiple Genes (Microarray)';

include('app_gene_expression_common_multiple_exe.php');

?>