<?php

if (!isAdminUser()){
	echo "<p>You do not have permissions to access this tool.</p>";	
} else {
	
	
	if (true){
		unset($results, $currentIndex, $categories);
		
		foreach($categories as $tempKey => $currentCategory){
			$tablePublic		= $APP_CONFIG['APP']['List_Category'][$currentCategory]['Table'];
			$tablePrivate		= $APP_CONFIG['APP']['List_Category'][$currentCategory]['Table_User'];
			$tableCombined		= $APP_CONFIG['APP']['List_Category'][$currentCategory]['Table_Combined'];
			
			$countPublic		= getTableCount($tablePublic, 0);
			$countPrivate		= getTableCount($tablePrivate, 0);
			$countCombined		= getTableCount($tableCombined, 0);
			
			if (true){
				$currentIndex++;
				$results[$currentIndex]['Table'] 	= $tablePublic;
				$results[$currentIndex]['Test'] 	= "Testing Record Count: {$tablePublic}";
				$results[$currentIndex]['Value'] 	= number_format($countPublic);
				
				if ($countPublic > 0){
					$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
					$results[$currentIndex]['Notes'] 	= '# of Public Data is > 0';
				} else {
					$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-times text-danger');
					$results[$currentIndex]['Notes'] 	= '# of Public Data is 0';
				}
			}
			
			
			if ($countPrivate > 0){
				
				if (true){
					$currentIndex++;
					$results[$currentIndex]['Table'] 	= $tablePrivate;
					$results[$currentIndex]['Test'] 	= "Testing Record Count: {$tablePrivate}";
					$results[$currentIndex]['Value'] 	= number_format($countPrivate);
					$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
					$results[$currentIndex]['Notes'] 	= 'N/A';
				}
				
				if (true){	
					$currentIndex++;
					$results[$currentIndex]['Table'] 	= $tableCombined;
					$results[$currentIndex]['Test'] 	= "Testing Record Count: {$tableCombined}";
					$results[$currentIndex]['Value'] 	= number_format($countCombined);
					
					if ($countCombined == ($countPublic + $countPrivate)){
						$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
						$results[$currentIndex]['Notes'] 	= '# of Combined Data = # of Public + Private';
					} else {
						$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-times text-danger');
						$results[$currentIndex]['Notes'] 	= '# of Combined Data != # of Public + Private';
					}
				}
				
			}
			
		}
	}
	

	if (true){
		$tables = array();
		$tables[] = $APP_CONFIG['Table']['GeneAnnotation'];
		$tables[] = $APP_CONFIG['Table']['GeneCombined'];
		
		
		
		$tables[] = 'TBL_BXGENOMICS_GENES_INDEX';
		
		
		
		$tables[] = 'tbl_wikipathways_info';
		
		$tables[] = 'GeneList';
		$tables[] = 'GeneSet';
		
		
		foreach($tables as $tempKey => $currentTable){
			$currentCount		= getTableCount($currentTable, 0);
			
			if (true){
				$currentIndex++;
				$results[$currentIndex]['Table'] 	= $currentTable;
				$results[$currentIndex]['Test'] 	= "Testing Record Count: {$currentTable}";
				$results[$currentIndex]['Value'] 	= number_format($currentCount);
				
				
				if ($currentCount > 0){
					$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
					$results[$currentIndex]['Notes'] 	= '# of Data > 0';
				} else {
					$results[$currentIndex]['Result'] 	= printFontAwesomeIcon('fas fa-times text-danger');
					$results[$currentIndex]['Notes'] 	= '# of Data == 0';
				}
			}
		}
	}
	
	
	
	unset($tableContent);
	$tableContent['Header']['No.'] 				= 'No.';
	$tableContent['Header']['Test'] 			= 'Test';
	$tableContent['Header']['Table'] 			= 'Table';
	$tableContent['Header']['Value'] 			= '# of Record';
	$tableContent['Header']['Result'] 			= 'Test Result';
	$tableContent['Header']['Notes'] 			= 'Reason';
	
	if (true){
		echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
				unset($currentIndex);
				foreach($results as $currentIndex => $currentResult){
					
					$tableContent['Body'][$currentIndex]['Value']['No.'] 	= "{$currentIndex}.";
					$tableContent['Body'][$currentIndex]['Value']['Table'] 	= $currentResult['Table'];
					$tableContent['Body'][$currentIndex]['Value']['Test'] 	= $currentResult['Test'];
					$tableContent['Body'][$currentIndex]['Value']['Value'] 	= $currentResult['Value'];
					$tableContent['Body'][$currentIndex]['Value']['Result']	= $currentResult['Result'];
					$tableContent['Body'][$currentIndex]['Value']['Notes']	= $currentResult['Notes'];
				}
			
				echo printTableHTML($tableContent, 1, 1, 1);
				
			echo "</div>";
		echo "</div>";
	
	}
}
?>

