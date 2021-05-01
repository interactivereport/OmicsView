<?php


if (isset($results['Export']['HTML'])){
	unset($tableOption);
	$tableOption['id'] 		= 'resultTable';
	
	for ($i = 0; $i < array_size($results['Export']['HTML']['Headers']); $i++){
		$tableOption['exportOptions'][] = $i;	
	}
	
	
	$tableOption['exportOptions'] = implode(',', $tableOption['exportOptions']);
	
	$tableOption['headers']	= $results['Export']['HTML']['Headers'];
	$tableOption['dataKey']	= putSQLCacheWithoutKey($results['Export']['HTML'], '', 'dataTableHTMLKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);

	$tableOption['disableButton'] = false;

	$tableOption['order']		= '1, "asc"';
	$tableOption['pageLength']	= 100;
	
	for ($i = 0; $i < array_size($results['Export']['HTML']['Headers']); $i++){
		$tableOption['columnScript'][] = 'null';
	}
	$tableOption['columnScript'] = implode(',', $tableOption['columnScript']);
	

	
	
	include('app_common_table_html.php');
}

?>