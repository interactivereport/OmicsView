<?php
include_once('config_init.php');

$resultFromCache 	= getSQLCache($_GET['key']);
$searchKeyword 		= trim($_POST['search']['value']);

if (is_numeric($searchKeyword)){
	$searchKeyword = '';	
}
	
if ($resultFromCache !== false){
	//Found	
} else {
	exit();	
}


unset($currentRowCount);

foreach($resultFromCache['Correlation_Coefficient_Transformed'] as $geneSourceIndex => $tempValue1){
	
	foreach($tempValue1 as $geneTargetIndex => $coff_Transformed){
		
		$currentRowCount++;
		
		if ($searchKeyword != ''){
			unset($found);
			if (stripos($resultFromCache['Gene_Source'][$geneSourceIndex], $searchKeyword) !== FALSE){
				$found = 1;
			} elseif (stripos($resultFromCache['Gene_Target'][$geneTargetIndex], $searchKeyword) !== FALSE){
				$found = 1;	
			}
			
			if (!$found){
				continue;		
			}
		}
		
		$sourceValues		= $resultFromCache['geneExpressionValueIndex'][$geneSourceIndex];
		$targetValues		= $resultFromCache['geneExpressionValueIndex'][$geneTargetIndex];
		cleanTwoNumericArrays($sourceValues, $targetValues);
		
		$rows[$currentRowCount]['Gene_Source'] 		= $resultFromCache['Gene_Source'][$geneSourceIndex];
		$rows[$currentRowCount]['Gene_Target'] 		= $resultFromCache['Gene_Target'][$geneTargetIndex];
		$rows[$currentRowCount]['Corr'] 			= round($resultFromCache['Correlation_Coefficient_Raw'][$geneSourceIndex][$geneTargetIndex], 5);
		$rows[$currentRowCount]['R2']				= round($rows[$currentRowCount]['Corr']*$rows[$currentRowCount]['Corr'], 5);
		$rows[$currentRowCount]['Count']			= array_size($sourceValues);
		
		
		$reviewURLSource	= "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$geneSourceIndex}";
		$reviewURLTarget	= "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$geneTargetIndex}";
		unset($actions);
		$actions[] = "<a title='Generate Scatterplot' modal-header='Correlation Using {$APP_MESSAGE['Gene']} Expression: {$resultFromCache['Gene_Source'][$geneSourceIndex]} vs {$resultFromCache['Gene_Target'][$geneTargetIndex]}' data-toggle='modal' href='app_correlation_genes_samples_plot.php?key={$_GET['key']}&source={$geneSourceIndex}&target={$geneTargetIndex}' data-target='#plotModal' title='Generate Scatterplot'>" . printFontAwesomeIcon('fas fa-chart-line') . "Plot</a>";
		$actions[] = "<a href='{$reviewURLSource}' title='Review {$rows[$currentRowCount]['Gene_Source']}' target='_blank'>" . printFontAwesomeIcon('fas fa-list') . "Review {$rows[$currentRowCount]['Gene_Source']}</a>";
		$actions[] = "<a href='{$reviewURLTarget}' title='Review {$rows[$currentRowCount]['Gene_Target']}' target='_blank'>" . printFontAwesomeIcon('fas fa-list') . "Review {$rows[$currentRowCount]['Gene_Target']}</a>";		


		$rows[$currentRowCount]['Actions'] = "<span class='text-nowrap'>" . implode("&nbsp; &nbsp;", $actions) . "</span>";
		
	}
}

unset($columns);
$columns[] = 'Gene_Source';
$columns[] = 'Gene_Target';
$columns[] = 'Corr';
$columns[] = 'R2';
$columns[] = 'Count';


$orderBy		= $_POST['order'][0]['column'];
$orderByColumn	= $columns[$orderBy];
$orderDirection = $_POST['order'][0]['dir'];
$orderDirection = strtoupper($orderDirection);

if ($orderDirection != 'ASC'){
	$orderDirection = 'DESC';
}

$ORDER_ARRAY 	= array($orderByColumn => $orderDirection);
naturalSort2DArray($rows);




$start			= abs(intval($_POST['start']));
$count			= abs(intval($_POST['length']));

$SQL_RESULTS 	= array_slice($rows, $start, $count);





$results['recordsTotal'] 	= array_size($rows);
$results['recordsFiltered'] = $results['recordsTotal'];




foreach($SQL_RESULTS as $rowID => $row){

	$results['data'][] = array_values($row);
}



if (!isset($results['data'])){
	$results['data'] = array();	
}
echo json_encode($results);




?>