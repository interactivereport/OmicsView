<?php


function general_print_table_HTML($tableContent, $compact = 1, $striped = 0, $tableOnly = 1, $divClass = 'col-lg-5 col-sm-12', $tableClass = ''){
	
	
	if ((general_array_size($tableContent['Header']) <= 0) && (general_array_size($tableContent['Body']) <= 0)) return false;
	
	$result = '';
	$result .= "<div class='table-responsive'>";
	
		if ($tableClass == ''){
			$tableClass = general_get_table_class($compact, $striped);
		}
		
		$result .= "<table class='{$tableClass}'>";

		
		if (isset($tableContent['Header'])){
			$result .= "<thead>";
				$result .= "<tr>";
				foreach($tableContent['Header'] as $tempKey => $tempValue){
					$result .= "<th>{$tempValue}</th>";
				}
				$result .= "</tr>";
			$result .= "</thead>";
		}
		
		
		if (isset($tableContent['Body'])){
			$result .= "<tbody>";
				
				foreach($tableContent['Body'] as $row => $rowDetails){
					$result .= "<tr class='{$rowDetails['Class']}'>";
						if (isset($tableContent['Header'])){
							foreach($tableContent['Header'] as $tempKey => $tempValue){
								$result .= "<td>{$rowDetails['Value'][$tempKey]}</td>";
							}
						} else {
							foreach($rowDetails['Value'] as $tempKey => $tempValue){
								$result .= "<td>{$tempValue}</td>";
							}
						}
					$result .= "</tr>";
				}

			$result .= "</tbody>";
		}
		
		$result .= "</table>";
		
	$result .= "</div>";
	
	
	if (!$tableOnly){
		$result = "<div class='row'><div class='{$divClass}'>{$result}</div></div>";
	}
	
	
	return $result;
	
	
}

function general_get_table_class($compact = 1, $striped = 0){

	$classes[] = 'table';
	$classes[] = 'table-bordered';
	
	if ($compact){
		$classes[] = 'table-sm';	
	}
	
	if ($striped){
		$classes[] = 'table-striped';
	}
	
	return implode(' ', $classes);
}

?>