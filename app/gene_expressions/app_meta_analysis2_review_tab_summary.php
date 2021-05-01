<?php

	if ($dataArray['prepareMetaAnalysisData2']['Status'] != 'Finished'){
		
		echo "<div class='row'>";
			echo "<div class='col-12'>";
				$message = "<div>" . printFontAwesomeIcon('fas fa-info-circle') . " The analysis is being processed. This page will be refreshed every 30 seconds.</div>";
				echo getAlerts($message, 'warning');
			echo "</div>";
		echo "</div>";	
	}
	
	
	
	
	$classLeft	= 'col-3';
	$classRight = 'col-9';
	
	echo "<div class='row'>";
	
		echo "<div class='col-11'>";
		
			echo "<br/>";

			echo "<dl class='row'>";

				if ($dataArray['POST']['name'] != ''){
					echo "<dt class='{$classLeft} text-right'>Analysis Name:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['POST']['name']}</dt>";
				}
			
				if ($dataArray['POST']['Date'] != ''){
					echo "<dt class='{$classLeft} text-right'>Date Created:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['POST']['Date']}</dt>";
				}
			
				if ($dataArray['POST']['User_ID'] > 0){
					echo "<dt class='{$classLeft} text-right'>Owner:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['POST']['User']['Name']} ({$dataArray['POST']['User']['Email']})</dt>";
				}
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Status:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['POST']['Status_HTML']}</dt>";
				}

				
			echo "</dl>";

		echo "</div>";

	echo "</div>";
	

	
	
?>