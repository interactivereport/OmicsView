<?php
include_once('config_init.php');

$dataArray	= getSQLCache($_GET['key']);

if (array_size($dataArray['Body']) <= 0) exit();

$SQL_RESULTS = &$dataArray['Body'];

//******************
// Build Data
//******************
$searchKeyword 		= trim($_POST['search']['value']);
unset($currentRowCount);
foreach($SQL_RESULTS as $recordID => $tempRow){

	if ($searchKeyword != ''){
		$tempString = implode('   ', $tempRow);
		
		if (stripos($tempString, $searchKeyword) !== FALSE){
			//Found	
		} else {
			continue;	
		}
	}
	
	$rows[++$currentRowCount] = $tempRow;
	
}


$orderBy		= $_POST['order'][0]['column'];



if (is_numeric($orderBy)){
	
	if (isset($tempRow[$orderBy])){
		//Do nothing
	} else {
		if (array_size($dataArray['Headers']) > 0){
			$headers = array_keys($dataArray['Headers']);
			
			if (isset($headers[$orderBy])){
				$orderBy = $headers[$orderBy];
			}
		}
	}
}



$orderDirection = $_POST['order'][0]['dir'];
$orderDirection = strtoupper($orderDirection);

if ($orderDirection != 'DESC'){
	$orderDirection = 'ASC';	
}


$ORDER_ARRAY 	= array($orderBy => $orderDirection);
naturalSort2DArray($rows);



$start			= abs(intval($_POST['start']));
$count			= intval($_POST['length']);

if ($count > 0){
	$SQL_RESULTS 	= array_slice($rows, $start, $count);
} else {
	$SQL_RESULTS 	= &$rows;	
}



$results['recordsTotal'] 	= array_size($rows);
$results['recordsFiltered'] = $results['recordsTotal'];




foreach($SQL_RESULTS as $rowID => $row){
	$results['data'][] = array_values($row);
}



if (!isset($results['data'])){
	$results['data'] = array();	
}

header('Content-Type: application/json');
//echo json_encode($results);
echo json_encode($results,  JSON_PARTIAL_OUTPUT_ON_ERROR );


?>