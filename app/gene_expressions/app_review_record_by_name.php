<?php
include_once('config_init.php');



$targetCategory 	= $_GET['targetCategory'];
$value				= addslashes(trim($_GET['value']));
$_GET['recordIndex'] 	= intval($_GET['recordIndex']);
$currentSQL	= $_GET['currentSQL'];

if (internal_data_is_public($_GET['recordIndex'])){
	
	$SQL_TABLE = $APP_CONFIG['APP']['List_Category'][$targetCategory]['Table'];

} else {

	$SQL_TABLE = $APP_CONFIG['APP']['List_Category'][$targetCategory]['Table_User'];	
	
}

if (($SQL_TABLE == '') || ($value == '')){
	echo "Error. Please verify your link and try again.";
	exit();
}

$Column_Internal	= $APP_CONFIG['APP']['List_Category'][$targetCategory]['Column_Internal'];
$Column_Human		= $APP_CONFIG['APP']['List_Category'][$targetCategory]['Column_Human'];

$SQL = "SELECT `{$Column_Internal}` FROM `{$SQL_TABLE}` WHERE `{$Column_Human}` = '{$value}'";
$recordIndex = getSQL($SQL, 'GetOne', $SQL_TABLE);

$targetCategoryLower = strtolower($targetCategory);


$currentURL		= "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type={$targetCategoryLower}&id={$recordIndex}";


header("Location: {$currentURL}");
exit();

?>