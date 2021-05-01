<?php
include_once('config_init.php');

$currentTable 		= 'Samples';
$geneValueTable 	= 'GeneLevelExpression';
$toolPlatformType	= 'Microarray';
$geneValueColumn 	= 'Value';


if ($_POST['transform_value'] != 0){
	$formula			= "Value + {$_POST['transform_value']}";
} else {
	$formula			= "Value";
}
$formula			= "Value";
$pageTitle			= 'Single Gene (Microarray)';

include('app_gene_expression_common_single_exe.php');

?>