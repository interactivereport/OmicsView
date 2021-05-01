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

if ($_POST['transform']){
	$formula			= "log2({$valueText})";
} else {
	$formula			= $valueText;
}


$pageTitle			= 'Multiple Genes (RNA-Seq)';

include('app_gene_expression_common_multiple_exe.php');

?>