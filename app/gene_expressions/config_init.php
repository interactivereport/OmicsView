<?php

ini_set('zlib.output_compression','On');

include_once(dirname(dirname(__FILE__)) . "/config.php");

ini_set('memory_limit', -1);
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
error_reporting(0);

include_once('config_settings.php');
include_once('config_version.php');

include_once('config_functions_app.php');
include_once('config_functions_app_bubble_plot.php');
include_once('config_functions_app_correlation.php');
include_once('config_functions_app_dashboard.php');
include_once('config_functions_app_gene_expression.php');
include_once('config_functions_app_heatmap_pathway.php');
include_once('config_functions_app_internal_data.php');
include_once('config_functions_app_KEGG_pathway.php');
include_once('config_functions_app_list.php');
include_once('config_functions_app_meta_analysis.php');
include_once('config_functions_app_meta_analysis2.php');
include_once('config_functions_app_pairwise.php');
include_once('config_functions_app_record.php');
include_once('config_functions_app_relations.php');
include_once('config_functions_app_settings.php');

include_once('config_functions_lib_cache.php');
include_once('config_functions_lib_database.php');
include_once('config_functions_lib_general.php');
include_once('config_functions_lib_math.php');




//Basic Stuffs
$APP_CONFIG['APP_CURRENT_DIR'] 		= dirname(__FILE__);
$APP_CONFIG['APP_CURRENT_DIR'] 		= explode('/', $APP_CONFIG['APP_CURRENT_DIR']);
$APP_CONFIG['APP_CURRENT_DIR'] 		= array_pop($APP_CONFIG['APP_CURRENT_DIR']);
$APP_CONFIG['Profile']				= $BXAF_CONFIG['APP_PROFILE'];
$APP_CONFIG['User_Info']			= $_SESSION['User_Info'];
$APP_CONFIG['User_Info']['ID'] 		= intval($APP_CONFIG['User_Info']['ID']);


//$BXAF_CONFIG['BXAF_PAGE_TITLE'] 	= '';

//Profile
$profileFiles = glob("./profile/{$APP_CONFIG['Profile']}/*.php");

natsort($profileFiles);
foreach($profileFiles as $tempKey => $tempValue){
	include_once($tempValue);	
}

if ($BXAF_CONFIG['HAS_GTEx_DATA']){
	//$APP_CONFIG['APP']['Module']['GTEx'] = true;	
}

if ($BXAF_CONFIG['Precheck_Modified_DiseaseState']){
	$APP_CONFIG['APP']['Precheck_Modified_DiseaseState'] = true;
} else {
	$APP_CONFIG['APP']['Precheck_Modified_DiseaseState'] = false;
}




foreach($APP_CONFIG['Internal_Data_Settings']['Gene_Mapping'] as $tempKey => $tempValue){
	$APP_CONFIG['Table'][$tempValue['Table']]				= $tempValue['Table'];
	$APP_CONFIG['Tables'][$tempValue['Table']]['Title']		= "Gene Lookup Table ({$tempValue['Name']})";
}

$APP_CONFIG['StartTime'] = microtime(true);

$APP_CONFIG['BXAF_CONFIG_CUSTOM'] = $BXAF_CONFIG_CUSTOM;



if (!$APP_CONFIG['APP']['Module']['Comparison']){
	unset($APP_CONFIG['APP']['List_Category']['Comparison']);
}

initialize();

setRecordCount();



if (gene_uses_TPM()){
	$APP_CONFIG['DB_Dictionary']['GeneFPKM']['SQL']['FPKM']['Title'] = $APP_MESSAGE['Gene TPM'];
} else {
	$APP_CONFIG['DB_Dictionary']['GeneFPKM']['SQL']['FPKM']['Title'] = $APP_MESSAGE['Gene Value'];
}



if (isset($_GET)){
	foreach($_GET as $tempKey => $tempValue){
		if (!is_array($tempValue)){
			$_GET[$tempKey] = trim($tempValue);	
		}
	}
}

if (isset($_POST)){
	foreach($_POST as $tempKey => $tempValue){
		if (!is_array($tempValue)){
			$_POST[$tempKey] = trim($tempValue);	
		}
	}
}

$_GET['ID'] = intval($_GET['ID']);
$_GET['id'] = intval($_GET['id']);

if ($_GET['ID'] <= 0){
	$_GET['ID'] = $_GET['id'];	
}



unset($PAGE);
$PAGE['Title'] = $BXAF_CONFIG['BXAF_PAGE_TITLE'];



?>