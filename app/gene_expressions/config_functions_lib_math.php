<?php

function log2($number){
	return log($number, 2);
}

function calculateStdev($array){
	$sum 	= array_sum($array);
	$count 	= array_size($array);
	
	if ($count > 0){
		$mean	= $sum / $count;
		
		foreach($array as $tempKey => $tempValue) {
			$devs[] = pow($tempValue - $mean, 2);
		}
		
		return sqrt(array_sum($devs) / $count);
		
	} else {
		return 0;
	}
}

function calculateMean($array){
	$sum 	= array_sum($array);
	$count 	= array_size($array);
	
	if ($count > 0){
		$mean	= $sum / $count;
	} else {
		$mean	= 0;
	}

	return $mean;
}

function calculateZScore($number, $mean, $stdev){
	
	$number = floatval($number);
	$mean 	= floatval($mean);
	$stdev 	= floatval($stdev);
	
	if ($stdev == 0){
		return 0;	
	} else {
		return ($number - $mean)/$stdev;
	}
	
}




//Pearson Correlation
//$type
//0: Pearson
//1: Spearman
function getCorrelationCoefficient($argOrg1 = array(), $argOrg2=array(), $type = 0, &$length = 0){
	
	if ($type == 0){
		
		$argOrg1 = array_filter($argOrg1, 'is_numeric');
		$argOrg2 = array_filter($argOrg2, 'is_numeric');
		
		$commonKeys = array_intersect(array_keys($argOrg1), array_keys($argOrg2));
		
		foreach($commonKeys as $tempKey => $arrayKey){
			$arg1[$arrayKey] = $argOrg1[$arrayKey];
			$arg2[$arrayKey] = $argOrg2[$arrayKey];
		}

		
		$arg1 = array_values($arg1);
		$arg2 = array_values($arg2);
		
		
		$xnum = array_size($arg1);
		$ynum = array_size($arg2);
		
		if ($xnum <= 0){
			$length = $xnum;
			return false;	
		} elseif ($xnum != $ynum){
			$length = $xnum;
			return false;
		} elseif ($xnum < 3){
			$length = $xnum;
			return false;
		} else {
			$length = $xnum;	
		}
		
		$mean1 = array_sum($arg1) / $length;
		$mean2 = array_sum($arg2) / $length;
		
		$a	 = 0;
		$b	 = 0;
		$axb = 0;
		$a2	 = 0;
		$b2	 = 0;
		
		for ($i=0; $i<$length; $i++){
			$a = $arg1[$i]-$mean1;
			$b = $arg2[$i]-$mean2;
			$axb = $axb+($a*$b);
			$a2 = $a2+ pow($a,2);
			$b2 = $b2+ pow($b,2);
		}
		
		if ($a2*$b2 == 0){
			$corr = 0;	
		} else {
			$corr = $axb / sqrt($a2*$b2);
		}
		
		return $corr;
	
		
	} elseif ($type == 1){
		
		return getCorrelationCoefficient_Spearman($argOrg1, $argOrg2, $length);
		
	} else {
		
		return 0;
		
	}
}

//Spearman's Rank-Order Correlation
//https://statistics.laerd.com/statistical-guides/spearmans-rank-order-correlation-statistical-guide-2.php
function getCorrelationCoefficient_Spearman($argOrg1 = array(), $argOrg2=array(), &$length = 0){
	
	$argOrg1 = array_filter($argOrg1, 'is_numeric');
	$argOrg2 = array_filter($argOrg2, 'is_numeric');
	
	$commonKeys = array_intersect(array_keys($argOrg1), array_keys($argOrg2));
	
	foreach($commonKeys as $tempKey => $arrayKey){
		$arg1[$arrayKey] = $argOrg1[$arrayKey];
		$arg2[$arrayKey] = $argOrg2[$arrayKey];
	}

	$arg1 = array_values($arg1);
	$arg2 = array_values($arg2);
	
	
	$xnum = array_size($arg1);
	$ynum = array_size($arg2);
	
	if ($xnum <= 0){
		$length = $xnum;
		return false;	
	} elseif ($xnum != $ynum){
		$length = $xnum;
		return false;
	} elseif ($xnum < 3){
		$length = $xnum;
		return false;
	} else {
		$length = $xnum;	
	}
	

	arsort($arg1);
	$rankArray1	= array_keys($arg1);
	$rankArray1	= array_flip($rankArray1);

	arsort($arg2);
	$rankArray2	= array_keys($arg2);
	$rankArray2	= array_flip($rankArray2);
	
	$d_sqrt_sum = 0;
	for ($i=0; $i < $length; $i++) {
		
		$rank1 = $rankArray1[$i] + 1;  //array index starts from 0
		$rank2 = $rankArray2[$i] + 1;
		
		$d = abs($rank1 - $rank2);
		
		$d_sqrt_sum += $d*$d;
		
	}
	
	$temp = ($length*($length*$length - 1));
	
	if ($temp > 0){
		$p = 1 - (6*$d_sqrt_sum)/$temp;
	} else {
		$p = 1;	
	}
	
	return $p;
}

function getCorrelationCoefficient_Spearman_Details($argOrg1 = array(), $argOrg2=array()){
	
	$argOrg1 = array_filter($argOrg1, 'is_numeric');
	$argOrg2 = array_filter($argOrg2, 'is_numeric');
	
	$commonKeys = array_intersect(array_keys($argOrg1), array_keys($argOrg2));
	
	foreach($commonKeys as $tempKey => $arrayKey){
		$arg1[$arrayKey] = $argOrg1[$arrayKey];
		$arg2[$arrayKey] = $argOrg2[$arrayKey];
	}

	$arg1 = array_values($arg1);
	$arg2 = array_values($arg2);
	
	
	$xnum = array_size($arg1);
	$ynum = array_size($arg2);
	
	if ($xnum <= 0){
		return false;	
	} elseif ($xnum != $ynum){
		return false;
	} elseif ($xnum < 3){
		return false;
	} else {
		$length = $xnum;	
	}
	$results['Input'][1] = $arg1;
	$results['Input'][2] = $arg2;	

	arsort($arg1);
	$rankArray1	= array_keys($arg1);
	$rankArray1	= array_flip($rankArray1);

	arsort($arg2);
	$rankArray2	= array_keys($arg2);
	$rankArray2	= array_flip($rankArray2);
	
	$d_sqrt_sum = 0;
	for ($i=0; $i < $length; $i++) {
		
		$rank1 = $rankArray1[$i] + 1;  //array index starts from 0
		$rank2 = $rankArray2[$i] + 1;
		
		
		$results['Output'][1][$i] = $rank1;
		$results['Output'][2][$i] = $rank2;
		
		
		$d = abs($rank1 - $rank2);
		
		$d_sqrt_sum += $d*$d;
		
	}
	

	
	$temp = ($length*($length*$length - 1));
	
	if ($temp > 0){
		$p = 1 - (6*$d_sqrt_sum)/$temp;
	} else {
		$p = 1;	
	}
	
	$results['p'] = $p;
	
	return $results;
}


//http://www.clemson.edu/ces/phoenix/tutorials/regression/index.html
function getLinearRegression($arrayX, $arrayY){
	cleanTwoNumericArrays($arrayX, $arrayY);
	
	$arrayX = array_values($arrayX);
	$arrayY = array_values($arrayY);
	
	$nX		= sizeof($arrayX);
	$nY		= sizeof($arrayY);
	
	if (($nX <= 0) || ($nX != $nY)){
		return false;
	}
	
	for ($i = 0; $i < $nX; $i++){
		$x = $arrayX[$i];
		$y = $arrayY[$i];
		
		$xy[] = $x*$y;
		$xx[] = $x*$x;
	}
	
	
	$mUp	= (($nX*array_sum($xy)) - (array_sum($arrayX) * array_sum($arrayY)));
	$mLow	= $nX*array_sum($xx) - pow(array_sum($arrayX), 2);
	
	$result['Slope']	= $mUp / $mLow;
	
	$result['Constant']	= (array_sum($arrayY) - $result['Slope']*array_sum($arrayX)) / $nX;
	
	return $result;
	

}

function getRank($candidate, $allCandidates){
	
	arsort($allCandidates);
	$rankArray 	= array_keys($allCandidates);
	$rankArray	= array_flip($rankArray);
	
	if (isset($rankArray[$candidate])){
		$rank = $rankArray[$candidate] + 1;
	} else {
		$rank = 1;	
	}
	
	return intval($rank);
}



function getRankArray($allCandidates){
	
	$rankArray = $allCandidates;
	
	arsort($rankArray);
	$rankArray 	= array_keys($rankArray);
	$rankArray	= array_flip($rankArray);
	
	foreach($allCandidates as $tempKey => $tempValue){
		
		$result[$tempKey] = $rankArray[$tempKey] + 1;
	}
	
	return $result;
}


function cleanTwoNumericArrays(&$array1, &$array2){
	
	
	foreach($array1 as $tempKey => $tempValue){
		
		if (!is_numeric($array1[$tempKey])){
			unset($array1[$tempKey], $array2[$tempKey]);
		}
		
		
		if (!is_numeric($array2[$tempKey])){
			unset($array1[$tempKey], $array2[$tempKey]);
		}
		
	}
	
	return true;
	
}


?>