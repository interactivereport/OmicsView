<?php

if (!isAdminUser()){
	echo "<p>You do not have permissions to access this tool.</p>";	
} else {
	
	if (true){
		$errorCount = 0;
		$currentTable = $APP_CONFIG['Table']['App_User_Data_Projects'];
		$SQL = "SELECT `ProjectIndex`, `User_ID`  FROM `{$currentTable}`";
		$projectIndexes						= getSQL($SQL, 'GetAssoc', $currentTable, 1, 1);
		
		
		unset($tableContent);
		$tableContent['Header']['ProjectIndex'] 	= 'Project Index';
		$tableContent['Header']['Test'] 			= 'Test';
		$tableContent['Header']['Path'] 			= 'Path';
		$tableContent['Header']['Result'] 			= 'Test Result';
		$tableContent['Header']['Notes'] 			= 'Reason';
		
		unset($currentIndex);
		
		foreach($projectIndexes as $currentProjectIndex => $currentUserID){

			$baseDir = getInternalDataProcessedDir($currentUserID, $currentProjectIndex);
			
			if (true){
				$currentIndex++;
				$testFile = $baseDir . 'GeneLevelExpression.txt';	
				
				$tableContent['Body'][$currentIndex]['Value']['ProjectIndex'] 	= $currentProjectIndex;
				$tableContent['Body'][$currentIndex]['Value']['Test']			= "GeneLevelExpression.txt";
				$tableContent['Body'][$currentIndex]['Value']['Path'] 			= "<pre>{$testFile}</pre>";
				
				if (file_exists($testFile)){
					$tableContent['Body'][$currentIndex]['Value']['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
					$tableContent['Body'][$currentIndex]['Value']['Notes'] 		= "File exists.";
				} else {
					$tableContent['Body'][$currentIndex]['Value']['Result'] 	= printFontAwesomeIcon('fas fa-times text-danger');
					$tableContent['Body'][$currentIndex]['Value']['Notes'] 		= "File does not exist.";
				}
			}
			
			
			if ($BXAF_CONFIG['HAS_TPM_DATA']){
				$currentIndex++;
				$testFile = $baseDir . 'GeneLevelExpression-TPM.txt';	
				
				$tableContent['Body'][$currentIndex]['Value']['ProjectIndex'] 	= $currentProjectIndex;
				$tableContent['Body'][$currentIndex]['Value']['Test']			= "GeneLevelExpression-TPM.txt";
				$tableContent['Body'][$currentIndex]['Value']['Path'] 			= "<pre>{$testFile}</pre>";
				
				if (file_exists($testFile)){
					$tableContent['Body'][$currentIndex]['Value']['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
					$tableContent['Body'][$currentIndex]['Value']['Notes'] 		= "File exists.";
				} elseif (file_exists($baseDir . 'GeneLevelExpression.txt')){
					
					
					$header = getFileHeader($baseDir . 'GeneLevelExpression.txt', "\t");
					
					if (in_array('FPKM', $header)){
						$errorCount++;
						$tableContent['Body'][$currentIndex]['Value']['Result'] 	= printFontAwesomeIcon('fas fa-times text-danger');
						$tableContent['Body'][$currentIndex]['Value']['Notes'] 		= "File does not exist.";	
					} else {
						$tableContent['Body'][$currentIndex]['Value']['Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
						$tableContent['Body'][$currentIndex]['Value']['Notes'] 		= "Microarray Format. Does not need TPM.";
					}
				}
			}

		}
		
	}
	
	
	if (true){
		echo "<h4># of Error: {$errorCount}</h4>";
		
		if ($errorCount > 0){
			echo "<p>To fix the problem, please run the following:</p>";
			
			echo "<pre>";
			echo "cd {$BXAF_CONFIG['BXAF_APP_DIR']}/gene_expressions\n";
			echo "sudo php ./admin_fix_GeneFPKM_Internal_Scan.php 1\n";
			echo "</pre>";


		}
		
	}
	
	if (isset($tableContent['Body'])){
		echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
				echo printTableHTML($tableContent, 1, 1, 1);
			echo "</div>";
		echo "</div>";
	
	} else {
		echo "<p>There are no internal projects available.</p>";
	}
}
?>

