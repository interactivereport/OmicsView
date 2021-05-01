<?php

if (true){
	echo "<br/>";
	echo "<h3>General</h3>";

	if (true){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			$currentKey = 'Left_Menu_Expanded';
			
			echo "<div style='margin-top:12px;'>";
				echo "<strong>Expand Left Menu:</strong>";
			echo "</div>";
			
			foreach($APP_CONFIG['APP']['User_Preferences'][$currentKey] as $tempKeyX => $displayText){
				
				unset($checked);
				if ($getUserSettings[$currentKey] == $tempKeyX){
					$checked = "checked='checked'";
				}
				
				$currentID = "{$currentKey}_" . md5($tempKeyX);
				
				echo "<div class='form-check-inline' id='{$currentID}'>";
					echo "<label class='form-check-label' for='{$currentID}'>";
						echo "<input class='form-check-input' type='radio' name='{$currentKey}' id='{$currentID}' value='{$tempKeyX}' {$checked}>";
							echo '&nbsp;' . $displayText . '&nbsp;';
						echo "</label>";
				echo "</div>";
			}
			unset($tempArray);
		echo "</div>";
		echo "</div>";
	}
	
	if (true){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			if (array_size(internal_data_get_accessible_project()) > 0){
			$currentKey = 'Data_Source';
			
			echo "<div style='margin-top:12px;'>";
				echo "<strong>Data Source:</strong>";
			echo "</div>";
			
			foreach($APP_CONFIG['APP']['User_Preferences'][$currentKey] as $tempKeyX => $displayText){
				
				unset($checked);
				if ($getUserSettings[$currentKey] == $tempKeyX){
					$checked = 'checked';
				}
				
				$currentID = "{$currentKey}_" . md5($tempKeyX);
				
				echo "<div class='form-check-inline' id='{$currentID}'>";
					echo "<label class='form-check-label' for='{$currentID}'>";
						echo "<input class='form-check-input' type='radio' name='{$currentKey}' id='{$currentID}' value='{$tempKeyX}' {$checked}>";
							echo '&nbsp;' . $displayText . '&nbsp;';
						echo "</label>";
				echo "</div>";
			}
			unset($tempArray);
			
			
		}
		echo "</div>";
		echo "</div>";
	}
}

	

if ($BXAF_CONFIG['HAS_TPM_DATA']){

	echo "<div class='row'>";
	echo "<div class='col-lg-12'>";
	
	if (true){
		$currentKey = 'Gene_Data_Type';
		
		echo "<div style='margin-top:12px;'>";
			echo "<strong>Gene Data Type:</strong>";
		echo "</div>";
		
		foreach($APP_CONFIG['APP']['User_Preferences'][$currentKey] as $tempKeyX => $displayText){
			
			unset($checked);
			if ($getUserSettings[$currentKey] == $tempKeyX){
				$checked = 'checked';
			}
			
			$currentID = "{$currentKey}_" . md5($tempKeyX);
			
			echo "<div class='form-check-inline' id='{$currentID}'>";
				echo "<label class='form-check-label' for='{$currentID}'>";
					echo "<input class='form-check-input' type='radio' name='{$currentKey}' id='{$currentID}' value='{$tempKeyX}' {$checked}>";
						echo '&nbsp;' . $displayText . '&nbsp;';
					echo "</label>";
			echo "</div>";
		}
		unset($tempArray);
		
		
	}
		
		
		
	echo "</div>";
	echo "</div>";
}





if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
	echo "<br/>";
	echo "<br/>";
	echo "<h3>Comparison Dashboard</h3>";
	echo "<div class='row'>";
	echo "<div class='col-lg-12'>";
	
	if (0 && has_public_comparison_data()){
		if (array_size(internal_data_get_accessible_project()) > 0){
			$currentKey = 'Data_Source';
			
			echo "<div style='margin-top:12px;'>";
				echo "<strong>Data Source:</strong>";
			echo "</div>";
			
			foreach($APP_CONFIG['APP']['User_Preferences'][$currentKey] as $tempKeyX => $displayText){
				
				unset($checked);
				if ($getUserSettings[$currentKey] == $tempKeyX){
					$checked = 'checked';
				}
				
				$currentID = "{$currentKey}_" . md5($tempKeyX);
				
				echo "<div class='form-check-inline' id='{$currentID}'>";
					echo "<label class='form-check-label' for='{$currentID}'>";
						echo "<input class='form-check-input' type='radio' name='{$currentKey}' id='{$currentID}' value='{$tempKeyX}' {$checked}>";
							echo '&nbsp;' . $displayText . '&nbsp;';
						echo "</label>";
				echo "</div>";
			}
			unset($tempArray);
			
			
		}
	}
		
		
		
		
	
	
		unset($candidates);
		$candidates['dashboard_chart_cell_type'] 		= $APP_CONFIG['Dashboard']['Charts'][1]['Title'];
		$candidates['dashboard_chart_disease_state'] 	= $APP_CONFIG['Dashboard']['Charts'][2]['Title'];
		$candidates['dashboard_chart_treatment'] 		= $APP_CONFIG['Dashboard']['Charts'][3]['Title'];
		$candidates['dashboard_chart_platform_name'] 	= $APP_CONFIG['Dashboard']['Charts'][4]['Title'];
	
	
		foreach($candidates as $currentKey => $currentTitle){
			echo "<div style='margin-top:12px;'>";
				echo "<strong>{$currentTitle}:</strong>";
			echo "</div>";
		
			foreach($APP_CONFIG['APP']['User_Preferences']['TBL_PREFERENCE_ALL_OPTIONS'][$currentKey] as $tempKeyX => $displayText){
				
				unset($checked);
				if (in_array($displayText, $getUserSettings[$currentKey])){
					$checked = 'checked';
				}
				
				$currentID = "{$currentKey}_" . md5($displayText);
				
				echo "<div class='form-check-inline' id='{$currentID}'>";
					echo "<label class='form-check-label' for='{$currentID}'>";
						echo "<input class='form-check-input' type='checkbox' name='{$currentKey}[]' id='{$currentID}' value='{$displayText}' {$checked}>";
							echo '&nbsp;' . $displayText . '&nbsp;';
						echo "</label>";
				echo "</div>";
			}
			unset($tempArray);
		}

	echo "</div>";
	echo "</div>";
}

?>