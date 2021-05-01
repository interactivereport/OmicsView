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

	if (array_size($_SESSION['Comparison_Gene'][$_GET['sessionID']]) > 0){
		$preSelectedGeneIndexes = $_SESSION['Comparison_Gene'][$_GET['sessionID']]['Value'];
		$preSelectedTitle		= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['Title'];
		$preSelectedGeneCount	= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['Count'];
		
		$otherOptions = array();
		$otherOptions['process']				= true;
		
		$otherOptions['Y']						= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['Y'];
		$otherOptions['X']						= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['X'];
		$otherOptions['Direction']				= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['Direction'];
		
		$otherOptions['fc_cutoff']				= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['fc_cutoff'];
		$otherOptions['statistic_cutoff']		= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['statistic_cutoff'];
		$otherOptions['significance_threshold']	= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['significance_threshold'];
		$otherOptions['logfc_threshold']		= $_SESSION['Comparison_Gene'][$_GET['sessionID']]['logfc_threshold'];
		$otherOptions['file_suffix']			= $preSelectedTitle;
		
		$getGeneInfoFromComparison 	= getGeneInfoFromComparison($_GET['ID'], $preSelectedGeneIndexes, $otherOptions);
	} else {
		
		$otherOptions = array();
		$otherOptions['file_suffix']	= 'All_Genes';
		$getGeneInfoFromComparison 		= getGeneInfoFromComparison($_GET['ID'], array(), 'All_Genes');
	}

	
	$dataHTML					= &$getGeneInfoFromComparison['HTML'];

	if (true){
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
					$fileArray['Path'] 	= $getGeneInfoFromComparison['Summary']['Comparison_Gene_Info.csv'];
					$fileArray['Attachment_Filename'] 	= $getGeneInfoFromComparison['Summary']['File_name'];
					$fileKey		 	= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					$URL 			= "app_common_download.php?key={$fileKey}";
					$actions[] = "<a href='{$URL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " Download Comparison Gene Info</a>";
				}
				
				echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
	
			echo "</div>";
		echo "</div>";
	}
	
	
	
	
	if (true){
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
			
			
			if ($preSelectedTitle!= ''){			
				$name 		= 'Category';
				$value		= $preSelectedTitle;
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			} else {
				$name 		= 'Category';
				$value		= "All Genes";
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
		echo "</div>";
	}
	
	
	if ($otherOptions['process']){

		echo "<br/>";
		echo "<br/>";
		echo "<div class='row'>";
			$rowClass = 'col-3';
			
		
			if (true){			
				$name 		= 'Fold Change Cutoff';
				$value		= $otherOptions['fc_cutoff'];
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
			if (true){			
				$name 		= 'Log<sub>2</sub>(Fold Change Cutoff)';
				$value		= number_format($otherOptions['logfc_threshold'], 3);
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
			if (true){			
				$name 		= 'Stat Cutoff';
				$value		= number_format($otherOptions['statistic_cutoff'], 3);
			
				echo "<div class='{$rowClass}'>";
					echo "<div><strong>{$name}</strong>: <br/>{$value}</div>";
				echo "</div>";
			}
			
			
			if (true){			
				$name 		= '-Log<sub>10</sub>(Stat Cutoff)';
				$value		= number_format($otherOptions['significance_threshold'], 3);
			
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
		if ($preSelectedTitle != ''){
			echo "<h6>Showing {$getGeneInfoFromComparison['Summary']['Row_Count_Formatted']} Data Points from {$preSelectedGeneCount} {$preSelectedTitle}:</h6>";
		} else {
			echo "<h6>Showing {$getGeneInfoFromComparison['Summary']['Row_Count_Formatted']} Data Points from All Genes:</h6>";
		}
		echo "</div>";
	echo "</div>";
	
	echo "<hr/>";
	
	if (true){
		unset($tableOption);
		$tableOption['id'] 		= 'resultTable';
		
		for ($i = 0; $i < array_size($dataHTML['Headers']); $i++){
			$tableOption['exportOptions'][] = $i;	
		}
		
		
		$tableOption['exportOptions'] = implode(',', $tableOption['exportOptions']);
		
		$tableOption['headers']	= $dataHTML['Headers'];
		$tableOption['dataKey']	= putSQLCacheWithoutKey($dataHTML, '', 'dataTableHTMLKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
	
		$tableOption['disableButton'] = false;
	
		$tableOption['order']		= '1, "asc"';
		$tableOption['pageLength']	= 100;
		
		/*
		for ($i = 0; $i < array_size($dataHTML['Headers']); $i++){
			$tableOption['columnScript'][] = 'null';
		}
		$tableOption['columnScript'] = implode(',', $tableOption['columnScript']);
		*/
	
		
		
		include('app_common_table_html.php');
	}

}





?>