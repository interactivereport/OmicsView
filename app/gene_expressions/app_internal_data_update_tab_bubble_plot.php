<?php

$dataArray = array();

$dataArray = $jobInfo['Bubble_Plot_Details'];

foreach($APP_CONFIG['APP']['Bubble_Plot']['Dropdown'] as $tempKey => $currentColumn){
	if (!isset($APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn])){
		unset($APP_CONFIG['APP']['Bubble_Plot']['Dropdown'][$tempKey]);	
	}
}


if (true){
	$message = printFontAwesomeIcon('fas fa-info-circle') . " The following settings will be used in the bubble plot tools. These settings are only used for this project and its corresponding samples only.";
	echo getAlerts($message, 'info');
}


$currentTable = 'Samples';

				
if (true){
	
	unset($checked);
	if ($dataArray['Bubble_Plot_Default']){
		$checked = 'checked';
		$class = 'startHidden';
	} elseif (!isset($dataArray)){
		$checked = 'checked';	
		$class = 'startHidden';
	}

	$displayValue = 'Use system default settings. Uncheck this option to override with your own settings. ';


	echo "<div class='form-check-inline'>";
		echo "<label class='form-check-label' for='Bubble_Plot_Default'>";
			echo "<input class='form-check-input' type='checkbox' name='Bubble_Plot_Default' id='Bubble_Plot_Default' value='1' {$checked}>";
				echo '&nbsp;' . $displayValue . '&nbsp;';
			echo "</label>";
	echo "</div>";
}



if (true){

	echo "<div id='Bubble_Plot_inputSection' class='{$class}'>";
	
	//Y-Axis
	if (true){
		$name 				= 'y-axis';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Y-Axis';
		$sql_name			= "Bubble_Plot_{$name}";
		$values 			= $APP_CONFIG['APP']['Bubble_Plot']['Dropdown'];
		$value				= $dataArray[$name];
		
		if ($value == ''){
			$value = $APP_CONFIG['APP']['Bubble_Plot'][$name];
		}

		$placeHolderText 	= '';
		if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$sql_name}' id='{$sql_name}' title='{$placeHolderText}'>";
						foreach($values as $tempKey => $currentColumn){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							
							$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
							
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
		}
	}
	
	
	//Y-Axis Settings
	if (true){
		$name 				= 'y-axis_settings';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Y-axis Settings';
		$defaultValue		= '20';			
		$values = array();
		//$values['5'] 		= 'Top 5';
		$values['10'] 		= 'Top 10';
		$values['20'] 		= 'Top 20';
		$values['50'] 		= 'Top 50';
		$values['0'] 		= 'Show All Values';
		
		echo "<div style='margin-top:12px;'>";
			echo "<div class='form-group row'>";
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong style='margin-left:20px;'>{$displayName}:</strong></label>";
				
				
				echo "<div class='col-5'>";
					foreach($values as $tempKey => $tempValue){
						
						unset($checked);
						if (isset($dataArray[$name])){
							if ($dataArray[$name] == $tempKey){
								$checked = 'checked';
							}
						} else {
							if ($tempKey == $defaultValue){
								$checked = 'checked';	
							}
						}
						
						echo "<div class='form-check-inline' id='{$sql_name}_{$tempKey}_Section'>";
							echo "<label class='form-check-label' for='{$sql_name}_{$tempKey}'>";
								echo "<input class='form-check-input' type='radio' name='{$sql_name}' id='{$sql_name}_{$tempKey}' value='{$tempKey}' {$checked}>";
									echo '&nbsp;' . $tempValue . '&nbsp;';
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
				
			echo "</div>";	
		echo "</div>";
	}
	
	
	//Color By
	if (true){
		$name 				= 'colorBy';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Color By';
		$values 			= $APP_CONFIG['APP']['Bubble_Plot']['Dropdown'];
		$value				= $dataArray[$name];
		
		if ($value == ''){
			$value = $APP_CONFIG['APP']['Bubble_Plot'][$name];
		}
		$placeHolderText 	= '';
		if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$sql_name}' id='{$sql_name}' title='{$placeHolderText}'>";
						foreach($values as $tempKey => $currentColumn){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							
							$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
							
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
		}
	}
	
	
	//Color By Settings
	if (true){
		$name 				= 'colorBy_settings';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Color By Settings';
		$defaultValue		= '20';			
		$values = array();
		//$values['5'] 		= 'Top 5';
		$values['10'] 		= 'Top 10';
		$values['20'] 		= 'Top 20';
		$values['50'] 		= 'Top 50';
		$values['0'] 		= 'Show All Values';
		
		echo "<div style='margin-top:12px;'>";
			echo "<div class='form-group row'>";
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong style='margin-left:20px;'>{$displayName}:</strong></label>";
				
				
				echo "<div class='col-5'>";
					foreach($values as $tempKey => $tempValue){
						
						unset($checked);
						if (isset($dataArray[$name])){
							if ($dataArray[$name] == $tempKey){
								$checked = 'checked';
							}
						} else {
							if ($tempKey == $defaultValue){
								$checked = 'checked';	
							}
						}
						
						echo "<div class='form-check-inline' id='{$sql_name}_{$tempKey}_Section'>";
							echo "<label class='form-check-label' for='{$sql_name}_{$tempKey}'>";
								echo "<input class='form-check-input' type='radio' name='{$sql_name}' id='{$sql_name}_{$tempKey}' value='{$tempKey}' {$checked}>";
									echo '&nbsp;' . $tempValue . '&nbsp;';
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
				
			echo "</div>";	
		echo "</div>";
	}
	

	//Marker Area
	if (true){
		$name 				= 'marker';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Marker Area';
		$defaultValue		= 'AdjustedPValue';			
		$values = array();
		$values['AdjustedPValue'] 	= 'Adjusted p-value';
		$values['PValue'] 			= 'p-value';
		
		echo "<div style='margin-top:12px;'>";
			echo "<div class='form-group row'>";
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				
				echo "<div class='col-5'>";
					foreach($values as $tempKey => $tempValue){
						
						unset($checked);
						if (isset($dataArray[$name])){
							if ($dataArray[$name] == $tempKey){
								$checked = 'checked';
							}
						} else {
							if ($tempKey == $defaultValue){
								$checked = 'checked';	
							}
						}
						
						echo "<div class='form-check-inline' id='{$sql_name}_{$tempKey}_Section'>";
							echo "<label class='form-check-label' for='{$sql_name}_{$tempKey}'>";
								echo "<input class='form-check-input' type='radio' name='{$sql_name}' id='{$sql_name}_{$tempKey}' value='{$tempKey}' {$checked}>";
									echo '&nbsp;' . $tempValue . '&nbsp;';
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
				
			echo "</div>";	
		echo "</div>";
	}
	
	
	//Marker Shape By
	if (true){
		$name 				= 'shapeBy';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Marker Shape By';
		$values 			= $APP_CONFIG['APP']['Bubble_Plot']['Dropdown'];
		$value				= $dataArray[$name];
		
		if ($value == ''){
			$value = '';
		}
		$placeHolderText 	= '';
		if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$sql_name}' id='{$sql_name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>None</option>";
					
						foreach($values as $tempKey => $currentColumn){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
							
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
		}
	}
	
	
	//Subplot By
	if (true){
		$name 				= 'subplotBy';
		$sql_name			= "Bubble_Plot_{$name}";
		$displayName		= 'Subplot By';
		$values 			= $APP_CONFIG['APP']['Bubble_Plot']['Dropdown'];
		
		
		if (isset($dataArray[$name])){
			$value			= $dataArray[$name];	
		} else {
			$value			= $APP_CONFIG['APP']['Bubble_Plot']['subplotBy'];
		}

		$placeHolderText 	= '';
		if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$sql_name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$sql_name}' id='{$sql_name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>None</option>";
					
						foreach($values as $tempKey => $currentColumn){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							
							$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
							
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
		}
	}
	

	//Chart Height
	if (true){
		
		$name 				= 'plot_height';
		$sql_name			= "Bubble_Plot_{$name}";
		
		echo "<div style='margin-top:12px;'>";
		
			echo "<div class='form-group row'>";
			
				echo "<div class='col-2'>";
					echo "<label for='{$sql_name}' class='xcol-form-label'><strong>Chart Height: &nbsp;</strong></label>";
				echo "</div>";
	
	
				echo "<div class='col-2'>";
				
					$value = '';
					if (isset($dataArray[$name]) && ($dataArray[$name] >= 0)){
						$value = intval($dataArray[$name]);
					}
					$value = abs(intval($value));
				
					echo "<input class='inputForm form-control' type='text' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
				echo "</div>";
				
			echo "</div>";
			
		
		echo "</div>";
	}
	
	
	//Chart Width
	if (true){
		
		$name 				= 'plot_width';
		$sql_name			= "Bubble_Plot_{$name}";
		
		echo "<div style='margin-top:12px;'>";
		
			echo "<div class='form-group row'>";
			
				echo "<div class='col-2'>";
					echo "<label for='{$sql_name}' class='xcol-form-label'><strong>Chart Width: &nbsp;</strong></label>";
				echo "</div>";
	
	
				echo "<div class='col-2'>";
				
					$value = '';
					if (isset($dataArray[$name]) && ($dataArray[$name] >= 0)){
						$value = intval($dataArray[$name]);
					}
					$value = abs(intval($value));
				
					echo "<input class='inputForm form-control' type='text' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
				echo "</div>";
				
			echo "</div>";
			
		
		echo "</div>";
	}

	
	echo "</div>";

}





?>



<script type="text/javascript">

$(document).ready(function(){
	
	$('#Bubble_Plot_Default').change(function(){
		var currentValue = $(this).prop('checked');
		
		if (currentValue){
			$('#Bubble_Plot_inputSection').hide();
		} else {
			$('#Bubble_Plot_inputSection').show();
		}
		
	});
	
	
	
});



</script>