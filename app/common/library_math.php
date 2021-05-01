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


?>