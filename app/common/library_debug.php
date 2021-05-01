<?php


function general_start_timer($key = ''){
	
	global $BXAPP_TIMER;
	
	if ($key != ''){
		$BXAPP_TIMER[$key][] = microtime(true);
	}
	
	
	return true;
}



function general_process_timer($timer = NULL, $format = 0){
	
	global $APP_CONFIG, $BXAPP_TIMER;
	
	if ($timer == NULL){
		$timer = $BXAPP_TIMER;
	}
	
	foreach($timer as $tempKey => $tempValue){
		
		if (!isset($total)){
			$total = $tempValue[0];
		}
		
		$timer[$tempKey] = round(abs($tempValue[1] - $tempValue[0]), 8);
	}
	
	if (!isset($total)){
		$total = $APP_CONFIG['StartTime'];	
	}
	
	$total = round(microtime(true) - $total, 5);
	
	
	foreach($timer as $tempKey => $tempValue){
		
		$results[$tempKey]['Time'] = $tempValue;
		$results[$tempKey]['Percentage'] = round($tempValue/$total*100, 2);
		
		if ($format){
			$currentCount++;
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = $tempKey;
			$tableContent['Body'][$currentCount]['Value'][] = $results[$tempKey]['Time'];
			$tableContent['Body'][$currentCount]['Value'][] = $results[$tempKey]['Percentage'];
		}
		
	}
	
	
	
	if (!$format){
		$results['Total'] = $total;
		return $results;
	} else {
		
		$tableContent['Header'][] = 'No.';
		$tableContent['Header'][] = 'Item';
		$tableContent['Header'][] = "<span class='text-nowrap'>Time (s)</span>";
		$tableContent['Header'][] = "<span class='text-nowrap'>Percentage (%)</span>";
		
		if ($format){
			$currentCount++;
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = 'Total';
			$tableContent['Body'][$currentCount]['Value'][] = "<strong>{$total}</strong>";
			$tableContent['Body'][$currentCount]['Value'][] = 100;

			
			$currentCount++;
			$memory_get_usage = intval(memory_get_usage(true)/(1024*1024)) .'MB';
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = 'memory_get_usage()';
			$tableContent['Body'][$currentCount]['Value'][] = $memory_get_usage;
			$tableContent['Body'][$currentCount]['Value'][] = '';
			

			$currentCount++;
			$memory_get_peak_usage = intval(memory_get_peak_usage(true)/(1024*1024)) .'MB';
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = 'memory_get_peak_usage()';
			$tableContent['Body'][$currentCount]['Value'][] = $memory_get_peak_usage;
			$tableContent['Body'][$currentCount]['Value'][] = '';
			
		}
		
		return general_print_table_HTML($tableContent);
	}
	
}

?>