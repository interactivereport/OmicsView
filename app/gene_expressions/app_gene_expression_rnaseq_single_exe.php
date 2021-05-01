<?php
include_once('config_init.php');

$currentTable 		= 'Samples';
$geneValueTable 	= 'GeneFPKM';
$toolPlatformType	= 'RNA-Seq';
$geneValueColumn 	= 'FPKM';
if (gene_uses_TPM()){
	$valueText	= 'TPM';
} else {
	$valueText	= 'Value';
}

if ($_POST['transform_value'] != 0){
	$formula			= "{$valueText} + {$_POST['transform_value']}";
} else {
	$formula			= $valueText;
}
$formula			= $valueText;


$pageTitle			= 'Single Gene (RNA-Seq)';

include('app_gene_expression_common_single_exe.php');

?>