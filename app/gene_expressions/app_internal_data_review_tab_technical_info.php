<?php

echo "<br/>";

$message = printFontAwesomeIcon('fas fa-info-circle') . " This tab is visible to admin users only.";
echo getAlerts($message, 'warning');



foreach($dataArray['Projects'] as $tempKeyX => $tempValueX){
	$dataArray['Projects_Sorted'][$tempValueX['ProjectIndex']] = $tempValueX;
}


foreach($dataArray['Comparisons'] as $tempKeyX => $tempValueX){
	$dataArray['Comparisons_Sorted'][$tempValueX['ComparisonIndex']] = $tempValueX;
}

echo "<br>";

echo "<div class='row'>";
	echo "<div class='col-12'>";
	
		if ($dataArray['DateTime_Start'] == ''){	
			echo "<p>There is no information available now.</p>";
		}
	
		if ($dataArray['DateTime_Start'] != ''){	
			echo "<p><strong>Background Process Started:</strong> {$dataArray['DateTime_Start']}</p>";
		}

		if ($dataArray['DateTime_End'] != ''){
			
			echo "<p><strong>Background Process Ended:</strong> {$dataArray['DateTime_End']}</p>";
			
			echo "<p><strong>Duration:</strong> {$dataArray['Duration']}</p>";
		}
		
		if (true){
			
			echo "<p><a href='app_internal_data_rerun.php?ID={$_GET['ID']}' target='_blank'>" . printFontAwesomeIcon('fas fa-redo-alt') . " Re-process this dataset</a></p>";
			
		}
		
		
	echo "</div>";
echo "</div>";



echo "<hr/>";


if (array_size($dataArray['Output']['Imported']) > 0){

	echo "<div class='row'>";
		echo "<div class='col-12'>";
	
			echo "<br/>";
			echo "<h4># of Records Imported</h4>";
			
			
			unset($tableContent);
			$tableContent['Header'][]		= 'Table';
			$tableContent['Header'][]		= 'Read';
			$tableContent['Header'][]		= 'Imported';
			
			
			if ($dataArray['Requirement']['Projects']){
				$tableContent['Body']['Projects']['Value'][] 	= "Projects";
				$tableContent['Body']['Projects']['Value'][] 	= number_format($dataArray['Output']['Line']['Projects']);
				$tableContent['Body']['Projects']['Value'][] 	= number_format($dataArray['Output']['Imported']['Projects']);
			}
			
			if ($dataArray['Requirement']['Samples']){
				$tableContent['Body']['Samples']['Value'][] 	= "Samples";
				$tableContent['Body']['Samples']['Value'][] 	= number_format($dataArray['Output']['Line']['Samples']);
				$tableContent['Body']['Samples']['Value'][] 	= number_format($dataArray['Output']['Imported']['Samples']);
			}
			
			if ($dataArray['Requirement']['Comparisons']){
				$tableContent['Body']['Comparisons']['Value'][] = "Comparisons";
				$tableContent['Body']['Comparisons']['Value'][] = number_format($dataArray['Output']['Line']['Comparisons']);
				$tableContent['Body']['Comparisons']['Value'][] = number_format($dataArray['Output']['Imported']['Comparisons']);
			}
			
			if ($dataArray['Requirement']['GeneLevelExpression']){
				$tableContent['Body']['GeneLevelExpression']['Value'][] 	= $APP_MESSAGE['Gene Level Expression Data'];
				$tableContent['Body']['GeneLevelExpression']['Value'][] 	= number_format($dataArray['Output']['Line']['GeneLevelExpression']);
				$tableContent['Body']['GeneLevelExpression']['Value'][] 	= number_format($dataArray['Output']['Imported']['GeneLevelExpression']);
			}
			
			if ($dataArray['Requirement']['ComparisonData']){
				$tableContent['Body']['ComparisonData']['Value'][] 	= "Comparison Data";
				$tableContent['Body']['ComparisonData']['Value'][] 	= number_format($dataArray['Output']['Line']['ComparisonData']);
				$tableContent['Body']['ComparisonData']['Value'][] 	= number_format($dataArray['Output']['Imported']['ComparisonData']);
			}
			
			
			echo printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12');
			unset($tableContent);
			
	
		echo "</div>";
	echo "</div>";
	
	echo "<hr/>";
}


if (array_size($dataArray['Output']['Tabix']) > 0){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
	
			echo "<br/>";
			echo "<h4>Tabix Files</h4>";
			echo "<p class='form-text'>The tabix files are grouped based on project.</p>";
	
			
			foreach($dataArray['Output']['Tabix'] as $projectIndex => $tabixInfo){
				echo "<h5><strong>{$dataArray['Projects_Sorted'][$projectIndex]['ProjectID']}</strong></h5>";

				if ($dataArray['Requirement']['ComparisonData']){
				echo "<div style='margin:10px;'>";
					$currentFile = 'ComparisonData.txt.gz';		
					echo "<h6 style='margin-left:20px;'>Comparison Data (Indexed by Comparison)</h6>";
						echo "<p style='margin-left:30px;'><strong>Path</strong>: {$tabixInfo[$currentFile]}</p>";
						if (isset($dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile])){
							echo "<p style='margin-left:30px;'><strong>Command #1</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][0]}</textarea></p>";
								  
							echo "<p style='margin-left:30px;'><strong>Command #2</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][1]}</textarea></p>";
						}
					echo "</div>";
				echo "<br/>";
				}
					
				if ($dataArray['Requirement']['ComparisonData']){
				echo "<div style='margin:10px;'>";
					$currentFile = 'ComparisonData.txt.Sample.gz';		
					echo "<h6 style='margin-left:20px;'>Comparison Data (Indexed by Sample)</h6>";
						echo "<p style='margin-left:30px;'><strong>Path</strong>: {$tabixInfo[$currentFile]}</p>";
						if (isset($dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile])){
							echo "<p style='margin-left:30px;'><strong>Command #1</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][0]}</textarea></p>";
								  
							echo "<p style='margin-left:30px;'><strong>Command #2</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][1]}</textarea></p>";
						}
					echo "</div>";
				echo "<br/>";
				}
					
				if ($dataArray['Requirement']['GeneLevelExpression']){
				echo "<div style='margin:10px;'>";
					$currentFile = 'GeneLevelExpression.txt.gz';
				echo "<h6 style='margin-left:20px;'>{$APP_MESSAGE['Gene Level Expression Data']} (Indexed by {$APP_MESSAGE['Gene']})</h6>";
						echo "<p style='margin-left:30px;'><strong>Path</strong>: {$tabixInfo[$currentFile]}</p>";
						if (isset($dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile])){
							echo "<p style='margin-left:30px;'><strong>Command #1</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][0]}</textarea></p>";
								  
							echo "<p style='margin-left:30px;'><strong>Command #2</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][1]}</textarea></p>";
						}
					echo "</div>";
				echo "<br/>";
				


				echo "<div style='margin:10px;'>";
					$currentFile = 'GeneLevelExpression.txt.Sample.gz';
					echo "<h6 style='margin-left:20px;'>{$APP_MESSAGE['Gene Level Expression Data']} (Indexed by Sample)</h6>";
						echo "<p style='margin-left:30px;'><strong>Path</strong>: {$tabixInfo[$currentFile]}</p>";
						
						if (isset($dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile])){
							echo "<p style='margin-left:30px;'><strong>Command #1</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][0]}</textarea></p>";
								  
							echo "<p style='margin-left:30px;'><strong>Command #2</strong>:</p>
								  <p style='margin-left:30px;'><textarea>{$dataArray['Output']['Tabix_Command'][$projectIndex][$currentFile][1]}</textarea></p>";
						}
					echo "</div>";
				echo "<br/>";
				}
				
				
			}
		echo "</div>";
	echo "</div>";
	echo "<hr/>";
}


if (array_size($dataArray['Output']['GO']['csv']) > 0){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
	
			echo "<br/>";
			echo "<h4>PAGE & GO Files</h4>";
			echo "<p class='form-text'>The PAGE and GO files are generated for each comparison record. The script below was used to generate the GO files.</p>";
			echo "<div style='margin-left:20px;'><strong>Script</strong>: </div>";
			echo "<div style='margin-left:30px;'><textarea>{$dataArray['Output']['GO']['script']}</textarea></div>";
			echo "<br>";
			
			
			unset($tableContent);
			$tableContent['Header'][]		= 'Comparison Index';
			$tableContent['Header'][]		= 'Comparison Name';
			$tableContent['Header'][]		= 'Path';
		
			
			foreach($dataArray['Output']['GO']['csv'] as $comparisonIndex => $comparisonInfo){
				
				$pathinfo = pathinfo($comparisonInfo);
				
				$tableContent['Body'][$comparisonIndex]['Value'][] 	= number_format($comparisonIndex);
				$tableContent['Body'][$comparisonIndex]['Value'][] 	= $dataArray['Comparisons_Sorted'][$comparisonIndex]['ComparisonID'];
				$tableContent['Body'][$comparisonIndex]['Value'][] 	= $pathinfo['dirname'];
				
			}
			
			echo printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12');
			unset($tableContent);
			
		echo "</div>";
	echo "</div>";
}




?>

<style>
textarea{
	width:90%;
	color:#888;
	margin-left:20px;
	margin-right:50px;
}

</style>