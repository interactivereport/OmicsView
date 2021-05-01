<?php


if (array_size($dataArray) <= 0){
	echo "<div class='row'>";

		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The record does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');

		echo "</div>";


	echo "</div>";

} else {

	echo "<br/>";

	echo "<div class='row'>";

		echo "<div class='col-lg-8 col-md-12'>";
		
			if (true){
				unset($tableContent);
				$currentIndex = 0;
				foreach($dataArray as $currentSQL => $currentValue){
					
					
					if (!shouldDisplay($recordCategory, $currentSQL, $dataArray, 1)){
						continue;	
					}
					
					$currentDisplay = getRecordDisplayName($recordCategory, $currentSQL, $dataArray);
					if ($currentDisplay == '') continue;
					
					if (strpos($currentValue, '|') !== FALSE){
						$currentValue = str_replace('|', ', ', $currentValue);	
					}
										
					$currentIndex++;
					
					if ($currentIndex % 2 == 1){
						$currentRow++;
						$tableContent['Body'][$currentRow]['Value']["1_Header"] 	= "<strong>{$currentDisplay}</strong>:";
						$tableContent['Body'][$currentRow]['Value']["1_Value"] 		= $currentValue;
						$tableContent['Body'][$currentRow]['Value']["1_Seperator"] 	= '&nbsp;';
						$tableContent['Body'][$currentRow]['Value']["2_Header"] 	= '';
						$tableContent['Body'][$currentRow]['Value']["2_Value"] 		= '';
					} else {
						$tableContent['Body'][$currentRow]['Value']["2_Header"] 	= "<strong>{$currentDisplay}</strong>:";
						$tableContent['Body'][$currentRow]['Value']["2_Value"] 		= $currentValue;
					}
				}
	
				echo "<div class='tableSwitcherMember tableSwitcherComplete'>";
					echo printTableHTML($tableContent, 1, 1, 1);
				echo "</div>";
			}
			
			

			
			

		echo "</div>";

	echo "</div>";

	
}


?>