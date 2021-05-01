<?php

//echo "<br/>";
echo "<br/>";

echo "<div class='row'>";

	echo "<div class='col-8'>";
	
		if (true){
			$actions = array();
			if (can_update_record($sampleArray)){
				$dataKey = putSQLCacheWithoutKey(array($sampleArray['SampleIndex']), '', 'URL', 1);
				$URL 	= "app_record_update.php?Category=Sample&recordIndex={$dataKey}";
				$title	= "Update Sample";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " {$title}</a>";
			}
			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
			unset($actions);
			
		}
		
		echo "<br/>";

	
	
		if (true){
			unset($tableContent);
			$currentIndex = 0;
			foreach($sampleArray as $currentSQL => $currentValue){
				
				
				if (!shouldDisplay($recordCategory, $currentSQL, $sampleArray, 0)){
					continue;	
				}
				
				$currentDisplay = getRecordDisplayName($recordCategory, $currentSQL, $sampleArray);
				if ($currentDisplay == '') continue;
									
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

			echo "<div class='tableSwitcherMember tableSwitcherCompact'>";
				echo printTableHTML($tableContent, 1, 1, 1);
			echo "</div>";
		}
	
		if (true){
			unset($tableContent);
			$currentIndex = 0;
			foreach($sampleArray as $currentSQL => $currentValue){
				
				
				if (!shouldDisplay($recordCategory, $currentSQL, $sampleArray, 1)){
					continue;	
				}
				
				$currentDisplay = getRecordDisplayName($recordCategory, $currentSQL, $sampleArray);
				if ($currentDisplay == '') continue;
									
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

			echo "<div class='startHidden tableSwitcherMember tableSwitcherComplete'>";
				echo printTableHTML($tableContent, 1, 1, 1);
			echo "</div>";
		}

	echo "</div>";

echo "</div>";

?>
<script type="text/javascript">

$(document).ready(function(){
	
	$('.tableSwitcherToComplete').click(function(){
		$('.tableSwitcherMember').hide();
		$('.tableSwitcherComplete').show();
	});
	
	$('.tableSwitcherToCompact').click(function(){
		$('.tableSwitcherMember').hide();
		$('.tableSwitcherCompact').show();
	});
	
});
</script>
