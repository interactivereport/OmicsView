<?php

if (array_size($dataArray) <= 0){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The comparison does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
	echo "</div>";
	exit();
} else {

	
	$getPAGEInfoFromComparison = getPAGEInfoFromComparison($_GET['ID']);
	
	if ($getPAGEInfoFromComparison['Summary']['Row_Count'] <= 0){
		echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The PAGE file does not exist. Please contact us for details.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
		echo "</div>";
		exit();	
	}
	


	if (true){
		
		if ($direction == 'Up'){
			$category = "Upregulated {$APP_MESSAGE['Genes']}";
		} else {
			$category = "Downregulated {$APP_MESSAGE['Genes']}";
		}

		
		unset($actions);
		echo "<div class='row'>";
			echo "<div class='col-12'>";
	
				if (true){
					$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=comparison&id={$_GET['ID']}";
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " Review This Comparison</a>";
				}
				
				if (true){
					$URL = $APP_CONFIG['APP']['List_Category']['Comparison']['Review_URL'];
					$actions[] = "<a href='{$URL}' >" . printFontAwesomeIcon('fas fa-list') . " Browse All Comparisons</a>";
				}
				
				if (true){
					unset($fileArray);
					$fileArray['Path'] 	= $getPAGEInfoFromComparison['Path'];
					$fileArray['Attachment_Filename'] 	= "Comparison_PAGE_Report.csv";
					$fileKey		 	= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					$URL 			= "app_common_download.php?key={$fileKey}";
					$actions[] = "<a href='{$URL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " Download Comparison PAGE Report</a>";
				}
				
				if ($direction == 'Up'){
					$URL = "app_comparison_PAGE.php?id={$_GET['ID']}&direction=down";
					$actions[] = "<a href='{$URL}' >" . printFontAwesomeIcon('fas fa-list') . " Review Downregulated {$APP_MESSAGE['Genes']}</a>";
				} else {
					$URL = "app_comparison_PAGE.php?id={$_GET['ID']}&direction=up";
					$actions[] = "<a href='{$URL}' >" . printFontAwesomeIcon('fas fa-list') . " Review Upregulated {$APP_MESSAGE['Genes']}</a>";
				}
				
				
				
				echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
	
			echo "</div>";
		echo "</div>";
	}
	
	
	
	
	if (1){
		echo "<div class='row'>";
			$rowClass = 'col-3';
			
		
			if (true){			
				$sql_name 	= 'ComparisonID';
				$name 		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$sql_name]['Title'];
				$value		= $dataArray[$sql_name];
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
			if (true){			
				$sql_name 	= 'ComparisonCategory';
				$name 		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$sql_name]['Title'];
				$value		= $dataArray[$sql_name];
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
			if (true){			
				$sql_name 	= 'ComparisonContrast';
				$name 		= $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$sql_name]['Title'];
				$value		= str_replace(';', '<br/>', $dataArray[$sql_name]);
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
			
			if (true){			
				$name 		= 'Category';
				$value		= $category;
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
		echo "</div>";
	}
	
	

	echo "<div class='row'>";	
		echo "<div class='col-12'>";
		echo "<br/>";
		echo "<br/>";
		echo "<h6>Showing {$getPAGEInfoFromComparison['Summary']['Row_Count_Formatted']} Data Points from All {$APP_MESSAGE['Genes']}:</h6>";
		echo "</div>";
	echo "</div>";
	
	echo "<hr/>";
	
	if (true){
		
		if ($direction == 'Up'){
			$ORDER_ARRAY = array(2 => 'DESC');
		} else {
			$ORDER_ARRAY = array(2 => 'ASC');
		}

		naturalSort2DArray($getPAGEInfoFromComparison['Body']);		
		
		$headers = $getPAGEInfoFromComparison['Header'];
		
		
		
		unset($tableOption);
		$tableOption['id'] 		= 'resultTable';
		
		for ($i = 0; $i < array_size($headers); $i++){
			$tableOption['exportOptions'][] = $i;	
		}
		
		
		$tableOption['exportOptions'] = implode(',', $tableOption['exportOptions']);
		
		$tableOption['headers']	= $headers;
		$tableOption['dataKey']	= putSQLCacheWithoutKey($getPAGEInfoFromComparison, '', 'dataTableHTMLKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
	
		$tableOption['disableButton'] = false;
	
		if ($direction == 'Up'){
			$tableOption['order']		= '2, "desc"';
		} else {
			$tableOption['order']		= '2, "asc"';
		}
		
		//$tableOption['orderDisable']		= 1;
		
		
		
		$tableOption['pageLength']	= 100;
		
		for ($i = 0; $i < array_size($headers); $i++){
			$tableOption['columnScript'][] = 'null';
		}
		$tableOption['columnScript'] = implode(',', $tableOption['columnScript']);
		
		
		
		include('app_common_table_html.php');
	}

}





?>