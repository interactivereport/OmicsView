<?php
include_once('config_init.php');

$tableOption	= getSQLCache($_GET['key']);
if (array_size($tableOption) <= 0) exit();

$searchKeyword 	= trim($_POST['search']['value']);
$start			= abs(intval($_POST['start']));
$count			= intval($_POST['length']);
$orderBy		= $_POST['order'][0]['column'];
$orderDirection = $_POST['order'][0]['dir'];
$orderDirection = strtoupper($orderDirection);
if ($orderDirection != 'DESC'){
	$orderDirection = 'ASC';	
}

$tempHeaders 	= array_keys($tableOption['headers']);
$orderByColumn	= $tempHeaders[$orderBy];



if ($searchKeyword == ''){
	$currentSQL 	= "{$tableOption['Extra']['SQL_Data_All']} ORDER BY `{$orderByColumn}` {$orderDirection} LIMIT {$start}, {$count}";
} else {
	
	$getTableColumnPreferences = getTableColumnPreferences($tableOption['Extra']['category']);
	
	$queries = array();
	$searchKeyword = addslashes($searchKeyword);
	
	foreach($getTableColumnPreferences as $tempKey => $tempValue){
		$queries[] = "(`{$tempValue['SQL']}` LIKE '%{$searchKeyword}%')";
	}
	$queries = '(' . implode(' OR ', $queries) . ')';
	
	
	if (array_size($tableOption['Extra']['SQL_Search']) > 0){
		$queries = "AND {$queries}";
	} else {
		if ($tableOption['Extra']['SQL_Has_Condition']){
			$queries = "AND {$queries}";
		} else {
			$queries = "WHERE {$queries}";
		}
	}
	
	$currentSQL = "{$tableOption['Extra']['SQL_Data_All']} {$queries} ORDER BY `{$orderByColumn}` {$orderDirection} LIMIT {$start}, {$count}";
	$currentSQL_Without_Limit = "{$tableOption['Extra']['SQL_Data_All']} {$queries}";

}

$SQL_RESULTS 	= getSQL($currentSQL, 'GetArray', $tableOption['Extra']['SQL_Table'], !$tableOption['Extra']['Cache_Disabled'], !$tableOption['Extra']['Cache_Disabled']);
$SQL_RESULTS	= processData($tableOption['Extra']['category'], $SQL_RESULTS, '', 'HTML');



//******************
// Build Data
//******************

//$ORDER_ARRAY 	= array($orderBy => $orderDirection);
//naturalSort2DArray($rows);


if ($searchKeyword != ''){
	$currentSQL_Without_Limit = "{$tableOption['Extra']['SQL_Data_All']} {$queries}";
	$currentSQL_Without_Limit = str_replace('SELECT * FROM', 'SELECT count(*) FROM', $currentSQL_Without_Limit);
	
	$currentCount 	= getSQL($currentSQL_Without_Limit, 'GetOne', $tableOption['Extra']['SQL_Table'], !$tableOption['Extra']['Cache_Disabled'], !$tableOption['Extra']['Cache_Disabled']);
	
	
	$results['recordsTotal']	= $tableOption['Extra']['Count'];
	$results['recordsFiltered'] = intval($currentCount);
} else {
	
	$results['recordsTotal']	= $tableOption['Extra']['Count'];
	$results['recordsFiltered'] = $tableOption['Extra']['Count'];
}

foreach($SQL_RESULTS as $rowID => $row){
	$results['data'][] = $row;
}


if (!isset($results['data'])){
	$results['data'] = array();	
}


header('Content-Type: application/json; charset=utf-8');


echo json_encode($results,  JSON_PARTIAL_OUTPUT_ON_ERROR );


?>