<?php
include_once('config_init.php');

$PAGE['Title'] 		= $APP_MESSAGE['Gene Expressions from RNA-Seq'];
$PAGE['Header']		= $APP_MESSAGE['Gene Expressions from RNA-Seq'];
$PAGE['Category']	= "Gene Expression Plots";

$PAGE['URL']		= 'app_gene_expression_rnaseq_multiple.php';
$PAGE['Body'] 		= 'app_gene_expression_common_multiple_content.php';
$PAGE['EXE'] 		= 'app_gene_expression_rnaseq_multiple_exe.php';
$PAGE['Barcode'] 	= 'app_gene_expression_rnaseq_multiple.php';

$PAGE['Plugins']['selectPicker'] 	= 1;
$PAGE['Plugins']['dataTables']		= 1;
$PAGE['Plugins']['canvasxpress']	= 1;

$geneValueTable 	= 'GeneFPKM';
$category			= 'RNA-Seq';
$exampleMessage		= $APP_CONFIG['APP']['RNA_Seq']['Multiple_Example_Message'];

if (!$APP_CONFIG['APP']['Module']['RNA-Seq']){
	$PAGE['Body'] = 'app_disabled.php';
}

include('page_generator.php');

?>