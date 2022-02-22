<?php

function general_calculate_mean($array = array()){
	$sum 	= array_sum($array);
	$count 	= general_array_size($array);
	
	if ($count > 0){
		$mean	= $sum / $count;
	} else {
		$mean	= 0;
	}

	return $mean;
}


function general_scale_array($array = array(), $target_scale_min = 0, $target_scale_max = 1){
	
	$min = min($array);
	$max = max($array);
	$norm = array();
	
	foreach($array as $key => $x){
		$norm[$key] = ($x - $min) / ($max - $min);
		
		//scale up
		$norm[$key] = $norm[$key]*($target_scale_max - $target_scale_min) + $target_scale_min;

	}
	
	return $norm;
	
}


?>