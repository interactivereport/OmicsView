<?php

function plotly_get_all_marker_shapes(){
	
	global $BAXF_CACHE;
	
	if (isset($BAXF_CACHE[__FUNCTION__])){
		return $BAXF_CACHE[__FUNCTION__];
	}

	$base = array();
	$base[] = 'circle';
	$base[] = 'square';
	$base[] = 'diamond';
	$base[] = 'cross';
	$base[] = 'x';
	$base[] = 'triangle-up';
	$base[] = 'triangle-down';
	$base[] = 'triangle-left';
	$base[] = 'triangle-right';
	$base[] = 'triangle-ne';
	$base[] = 'triangle-se';
	$base[] = 'triangle-sw';
	$base[] = 'triangle-nw';
	$base[] = 'pentagon';
	$base[] = 'hexagon';
	$base[] = 'hexagon2';
	$base[] = 'octagon';
	$base[] = 'star';
	$base[] = 'hexagram';
	$base[] = 'star-triangle-up';
	$base[] = 'star-triangle-down';
	$base[] = 'star-square';
	$base[] = 'star-diamond';
	$base[] = 'diamond-tall';
	$base[] = 'diamond-wide';
	$base[] = 'hourglass';
	$base[] = 'bowtie';
	$base[] = 'circle-cross';
	$base[] = 'circle-x';
	$base[] = 'square-cross';
	$base[] = 'square-x';
	$base[] = 'diamond-cross';
	$base[] = 'diamond-x';
	$base[] = 'cross-thin';
	$base[] = 'x-thin';
	$base[] = 'asterisk';
	$base[] = 'hash';
	$base[] = 'y-up';
	$base[] = 'y-down';
	$base[] = 'y-left';
	$base[] = 'y-right';
	$base[] = 'line-ew';
	$base[] = 'line-ns';
	$base[] = 'line-ne';
	$base[] = 'line-nw';
	
	$decoration = array();
	$decoration[] = '';
	$decoration[] = '-open';
	$decoration[] = '-dot';
	$decoration[] = '-open-dot';
	
	

	foreach($decoration as $tempKey1 => $currentDecoration){
		foreach($base as $tempKey2 => $currentBase){
			if (!isset($results)){
				$results = array(1 => "{$currentBase}{$currentDecoration}");
			} else {
				$results[] = "{$currentBase}{$currentDecoration}";
			}
		}
	}
	
	$BAXF_CACHE[__FUNCTION__] = $results;
	
	return $results;

}


function plotly_get_marker_shapes_from_value($key = NULL, $value = NULL){
	
	global $BAXF_PLOTLY;
	
	$allMarkers = plotly_get_all_marker_shapes();
	$allMarkersCount = general_array_size($allMarkers);
	
	$key 	= trim(strtolower($key));
	$value 	= trim(strtolower($value));
	
	if ($value == ''){
		$value = 'blank';	
	}
	
	if (!isset($BAXF_PLOTLY[$key]['ShapeID'])){
		$BAXF_PLOTLY[$key]['ShapeID'][$value] = 1;
		$BAXF_PLOTLY[$key]['Current'] = 1;
		$shapeID = 1;
	} elseif (isset($BAXF_PLOTLY[$key]['ShapeID'][$value])){
		$shapeID = $BAXF_PLOTLY[$key]['ShapeID'][$value];
	} elseif (!isset($BAXF_PLOTLY[$key]['ShapeID'][$value])){
		
		if ($BAXF_PLOTLY[$key]['Current'] >= $allMarkersCount){
			$BAXF_PLOTLY[$key]['Current'] = 0;
		}
		
	
		$BAXF_PLOTLY[$key]['Current']++;
		$shapeID = $BAXF_PLOTLY[$key]['Current'];
		$BAXF_PLOTLY[$key]['ShapeID'][$value] = $shapeID;
		
	}
	
	
	return $allMarkers[$shapeID];
	
	
	
	
	
}


?>