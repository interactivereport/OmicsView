<?php

if ($_GET['key'] != ''){
	$dataArray = getSQLCache($_GET['key']);
}

foreach($APP_CONFIG['APP']['Bubble_Plot']['Dropdown'] as $tempKey => $currentColumn){
	if (!isset($APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn])){
		unset($APP_CONFIG['APP']['Bubble_Plot']['Dropdown'][$tempKey]);	
	}
}


echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";

	echo "<h4>Data Options</h4><hr/>";
	echo "<div id='inputSection'>";
	
		//Gene Name
		if (true){
			unset($geneName);
			if ($dataArray['GeneName'] != ''){
				$geneName = $dataArray['GeneName'];
			} elseif ($_GET['GeneName'] != ''){
				$geneName = $_GET['GeneName'];	
			}
			
			$exampleMessage = $APP_CONFIG['APP']['RNA_Seq']['Single_Example_Message'];
			
			
			echo "<div class='row'>";
			echo "<div class='col-lg-4 col-md-8 col-sm-12 col-xs-12'>";
				echo "<div style='margin-top:10px;'>";
					echo "<strong>{$APP_MESSAGE['Gene Name']}:</strong>";
				echo "</div>";
				
				echo "<div class='input-group' style='margin-top:10px;'>";
					echo "<input type='text' name='GeneName' id='GeneName' class='form-control ' value='{$geneName}' placeholder='{$exampleMessage}'/>";
				echo "</div>";
				
			echo "</div>";
			echo "</div>";
		}
		
		//Data Source
		if (internal_data_get_accessible_project(0, 1, '')){
			
			$has_internal_data = true;
			
			if (!isset($dataArray['data_source'])){
				$resetDataArray = true;
				$dataArray['data_source'][] = 'private';
				$dataArray['data_source'][] = 'public';
			}
			
			echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
			
				echo "<div style='margin-top:12px;'>";
					echo "<strong>Data Source:</strong>";
				echo "</div>";
				
				echo "<div style='margin-left:15px;'>";
				echo internal_data_print_form_html($dataArray);
				
				echo internal_data_print_modal($dataArray, $category);
				echo "</div>";
			
			echo "</div>";
			echo "</div>";
			
			if ($resetDataArray){
				unset($dataArray['data_source']);
			}
		}
	
	echo "</div>";	//inputSection
	
	
	
	
	echo "<h4>Plots Options</h4><hr/>";
	echo "<div id='outputSection'>";
	
		//Y-Axis
		if (true){
			$name 				= 'y-axis';
			$displayName		= 'Y-Axis';
			$values 			= $APP_CONFIG['APP']['Bubble_Plot']['Dropdown'];
			$value				= $dataArray[$name];
			
			if ($value == ''){
				$value = $APP_CONFIG['APP']['Bubble_Plot'][$name];
			}
			$placeHolderText 	= '';
			if (array_size($values) > 0){
				echo "<div style='margin-top:12px;'>";
					echo "<div class='form-group row'>";
					
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					
					echo "<div class='col-2'>";
						echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
							foreach($values as $tempKey => $currentColumn){
								
								unset($selected);
								
								if ($currentColumn === $value){
									$selected = 'selected';	
								}
								
								$currentTitle = $currentDetails['Title'];
								if ($currentTitle == ''){
									$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
								}
								
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
			$displayName		= 'Y-axis Settings';
			$variableName		= 'y_axis_settings';
			$defaultValue		= '20';			
			$values = array();
			//$values['5'] 		= 'Top 5';
			$values['10'] 		= 'Top 10';
			$values['20'] 		= 'Top 20';
			$values['50'] 		= 'Top 50';
			$values['0'] 		= 'Show All Values';
			$values['-1'] 		= 'Customize';
			
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong style='margin-left:20px;'>{$displayName}:</strong></label>";
					
					
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
							
							if ($tempKey != -1){
								echo "<div class='form-check-inline' id='{$name}_{$tempKey}_Section'>";
									echo "<label class='form-check-label' for='{$name}_{$tempKey}'>";
										echo "<input class='form-check-input {$variableName}' type='radio' name='{$name}' id='{$name}_{$tempKey}' value='{$tempKey}' {$checked}>";
											echo '&nbsp;' . $tempValue . '&nbsp;';
									echo "</label>";
								echo "</div>";								
							} else {
								echo "<div class='form-check-inline' id='{$name}_customize_section'>";
									echo "<label class='form-check-label' for='{$name}_customize'>";
										echo "<input class='form-check-input {$variableName}' type='radio' name='{$name}' id='{$name}_customize' value='{$tempKey}' {$checked}>";
											echo "<a href='#{$variableName}_customize_modal' id='{$variableName}_customize_modal_trigger' data-toggle='modal'>";
												echo '&nbsp;' . $tempValue . '&nbsp;';
											echo "</a>";
											echo "<span id='{$variableName}_count_message' style='font-weight:normal;'></span>";
									echo "</label>";
								echo "</div>";
								
								
								
								$modalID 	= "{$variableName}_customize_modal";
								$modalTitle = "<h4 class='modal-title'>{$displayName}</h4>";
								$modalBody  = "<div id='{$variableName}_customize_modal_body'>Please enter a gene name first.</div>";
								
								
								if (($dataArray[$name] == -1) && array_size($dataArray['y-axis_customize']) > 0){
									$_POST = array();
									$_POST['GeneName'] 								= $dataArray['GeneName'];
									$_POST['data_source'] 							= $dataArray['data_source'];
									$_POST['data_source_private_project_indexes'] 	= $dataArray['data_source_private_project_indexes'];
									$_POST['Column'] 								= $dataArray['y-axis'];
									$_POST['marker'] 								= $dataArray['marker'];
									$_POST['type'] 									= 'y-axis';
									$_POST['y-axis_customize'] 						= $dataArray['y-axis_customize'];
									ob_start();
									include('app_bubble_plot_single_ajax.php');
									$modalBody  = "<div id='{$variableName}_customize_modal_body'>" . ob_get_contents() . "</div>";
									$_POST = array();
									ob_end_clean();
									$modal['y-axis'] = 1;
								}
								
								echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
								
							}


						}
					echo "</div>";
					
				echo "</div>";	
			echo "</div>";
		}
		
		
		//Color By
		if (true){
			$name 				= 'colorBy';
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
					
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					
					echo "<div class='col-2'>";
						echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
							foreach($values as $tempKey => $currentColumn){
								
								unset($selected);
								
								if ($currentColumn === $value){
									$selected = 'selected';	
								}
								
								$currentTitle = $currentDetails['Title'];
								if ($currentTitle == ''){
									$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
								}
								
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
			$displayName		= 'Color By Settings';
			$variableName		= 'colorBy_settings';
			$defaultValue		= '20';			
			$values = array();
			//$values['5'] 		= 'Top 5';
			$values['10'] 		= 'Top 10';
			$values['20'] 		= 'Top 20';
			$values['50'] 		= 'Top 50';
			$values['0'] 		= 'Show All Values';
			$values['-1'] 		= 'Customize';
			
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong style='margin-left:20px;'>{$displayName}:</strong></label>";
					
					
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
							
							if ($tempKey != -1){
								echo "<div class='form-check-inline' id='{$name}_{$tempKey}_Section'>";
									echo "<label class='form-check-label' for='{$name}_{$tempKey}'>";
										echo "<input class='form-check-input {$variableName}' type='radio' name='{$name}' id='{$name}_{$tempKey}' value='{$tempKey}' {$checked}>";
											echo '&nbsp;' . $tempValue . '&nbsp;';
									echo "</label>";
								echo "</div>";								
							} else {
								echo "<div class='form-check-inline' id='{$name}_customize_section'>";
									echo "<label class='form-check-label' for='{$name}_customize'>";
										echo "<input class='form-check-input {$variableName}' type='radio' name='{$name}' id='{$name}_customize' value='{$tempKey}' {$checked}>";
											echo "<a href='#{$variableName}_customize_modal' id='{$variableName}_customize_modal_trigger' data-toggle='modal'>";
												echo '&nbsp;' . $tempValue . '&nbsp;';
											echo "</a>";
											echo "<span id='{$variableName}_count_message' style='font-weight:normal;'></span>";
									echo "</label>";
								echo "</div>";
								
								
								
								$modalID 	= "{$variableName}_customize_modal";
								$modalTitle = "<h4 class='modal-title'>{$displayName}</h4>";
								$modalBody  = "<div id='{$variableName}_customize_modal_body'>Please enter a gene name first.</div>";
								
								if (($dataArray[$name] == -1) && array_size($dataArray['colorBy_customize']) > 0){
									$_POST = array();
									$_POST['GeneName'] 								= $dataArray['GeneName'];
									$_POST['data_source'] 							= $dataArray['data_source'];
									$_POST['data_source_private_project_indexes'] 	= $dataArray['data_source_private_project_indexes'];
									$_POST['Column'] 								= $dataArray['colorBy'];
									$_POST['marker'] 								= $dataArray['marker'];
									$_POST['type'] 									= 'colorBy';
									$_POST['colorBy_customize'] 					= $dataArray['colorBy_customize'];
									ob_start();
									include('app_bubble_plot_single_ajax.php');
									$modalBody  = "<div id='{$variableName}_customize_modal_body'>" . ob_get_contents() . "</div>";
									$_POST = array();
									ob_end_clean();
									$modal['colorBy'] = 1;
								}
								
								echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
								
							}
						}
					echo "</div>";
					
				echo "</div>";	
			echo "</div>";
		}

		//Subplot By
		if (true){
			$name 				= 'subplotBy';
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
					
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					
					echo "<div class='col-2'>";
						echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
						
							echo "<option value=''>None</option>";
						
							foreach($values as $tempKey => $currentColumn){
								
								unset($selected);
								
								if ($currentColumn === $value){
									$selected = 'selected';	
								}
								
								$currentTitle = $currentDetails['Title'];
								if ($currentTitle == ''){
									$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
								}
								
								echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							}
						echo "</select>";
					echo "</div>";
					echo "</div>";
			
				echo "</div>";
			}
		}
		
		//Subplot By Settings
		if (true){
			$name 				= 'subplotBy_settings';
			$displayName		= 'Subplot By Settings';
			$variableName		= 'subplotBy_settings';
			$defaultValue		= '0';			
			$values = array();
			$values['0'] 		= 'Show All Values';
			$values['-1'] 		= 'Customize';
			
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong style='margin-left:20px;'>{$displayName}:</strong></label>";
					
					
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
							
							if ($tempKey != -1){
								echo "<div class='form-check-inline' id='{$name}_{$tempKey}_Section'>";
									echo "<label class='form-check-label' for='{$name}_{$tempKey}'>";
										echo "<input class='form-check-input {$variableName}' type='radio' name='{$name}' id='{$name}_{$tempKey}' value='{$tempKey}' {$checked}>";
											echo '&nbsp;' . $tempValue . '&nbsp;';
									echo "</label>";
								echo "</div>";								
							} else {
								echo "<div class='form-check-inline' id='{$name}_customize_section'>";
									echo "<label class='form-check-label' for='{$name}_customize'>";
										echo "<input class='form-check-input {$variableName}' type='radio' name='{$name}' id='{$name}_customize' value='{$tempKey}' {$checked}>";
											echo "<a href='#{$variableName}_customize_modal' id='{$variableName}_customize_modal_trigger' data-toggle='modal'>";
												echo '&nbsp;' . $tempValue . '&nbsp;';
											echo "</a>";
											echo "<span id='{$variableName}_count_message' style='font-weight:normal;'></span>";
									echo "</label>";
								echo "</div>";
								
								
								
								$modalID 	= "{$variableName}_customize_modal";
								$modalTitle = "<h4 class='modal-title'>{$displayName}</h4>";
								$modalBody  = "<div id='{$variableName}_customize_modal_body'>Please enter a gene name first.</div>";
								
								if (($dataArray[$name] == -1) && array_size($dataArray['subplotBy_customize']) > 0){
									$_POST = array();
									$_POST['GeneName'] 								= $dataArray['GeneName'];
									$_POST['data_source'] 							= $dataArray['data_source'];
									$_POST['data_source_private_project_indexes'] 	= $dataArray['data_source_private_project_indexes'];
									$_POST['Column'] 								= $dataArray['subplotBy'];
									$_POST['marker'] 								= $dataArray['marker'];
									$_POST['type'] 									= 'subplotBy';
									$_POST['subplotBy_customize'] 					= $dataArray['subplotBy_customize'];
									ob_start();
									include('app_bubble_plot_single_ajax.php');
									$modalBody  = "<div id='{$variableName}_customize_modal_body'>" . ob_get_contents() . "</div>";
									$_POST = array();
									ob_end_clean();
									$modal['subplotBy'] = 1;
								}
								
								echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
								
							}
						}
					echo "</div>";
					
				echo "</div>";	
			echo "</div>";
		}


		//Marker Area
		if (true){
			$name 				= 'marker';
			$displayName		= 'Marker Area';
			$defaultValue		= 'AdjustedPValue';			
			$values = array();
			$values['AdjustedPValue'] 	= 'Adjusted p-value';
			$values['PValue'] 			= 'p-value';
			
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					
					
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
							
							echo "<div class='form-check-inline' id='{$name}_{$tempKey}_Section'>";
								echo "<label class='form-check-label' for='{$name}_{$tempKey}'>";
									echo "<input class='form-check-input {$name}' type='radio' name='{$name}' id='{$name}_{$tempKey}' value='{$tempKey}' {$checked}>";
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
					
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					
					echo "<div class='col-2'>";
						echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
						
							echo "<option value=''>None</option>";
						
							foreach($values as $tempKey => $currentColumn){
								
								unset($selected);
								
								if ($currentColumn === $value){
									$selected = 'selected';	
								}
								
								$currentTitle = $currentDetails['Title'];
								if ($currentTitle == ''){
									$currentTitle = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'];
								}
								
								echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							}
						echo "</select>";
					echo "</div>";
					echo "</div>";
			
				echo "</div>";
			}
		}
		
		
		//Engine
		if (0){
			$name 				= 'graphLibrary';
			$displayName		= 'Bubble Plot Library';
			$defaultValue		= 'Plotly';			
			$values = array();
			$values['Plotly'] 		= 'Plotly';
			$values['CanvasXpress'] = 'CanvasXpress';

			
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					
					
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
							
							echo "<div class='form-check-inline' id='{$name}_{$tempKey}_Section'>";
								echo "<label class='form-check-label' for='{$name}_{$tempKey}'>";
									echo "<input class='form-check-input' type='radio' name='{$name}' id='{$name}_{$tempKey}' value='{$tempKey}' {$checked}>";
										echo '&nbsp;' . $tempValue . '&nbsp;';
								echo "</label>";
							echo "</div>";
						}
					echo "</div>";
					
				echo "</div>";	
			echo "</div>";
		}
		

	
	echo "</div>";	//outputSection
	
	
	//Modal
	if (true){
		$modalID 	= 'advancedOptionSection';
		$modalTitle = "<h4 class='modal-title'>Advanced Options</h4>";

		$modalBody = "<div>";
		
			if (true){
				
				
				$name 			= 'keep_blank';
				$displayName 	= 'Include Comparison with Empty Attributes';
				
				$checked = '';
				
				if ($dataArray[$name]){
					$checked = "checked='checked'";
				}
				
				$modalBody .= "<h5>Data Settings</h5>";	
				
				$modalBody .= "<div class='form-check'>";
						$modalBody .= "<input class='inputForm form-check-input' type='checkbox' id='{$name}' name='{$name}' value='1' {$checked}/>";
						$modalBody .= "<label for='{$name}' class='form-check-label'>{$displayName}</label>";
				$modalBody .= "</div>";
				
				$modalBody .= "<p class='form-text'>
							By default, comparison data will be excluded from the plot if its corresponding value is blank. 
							For example, if y-axis is set to cell type, only comparisons with non-empty cell type will be included in the plot. Check this option if you like to include more comparisons in the result.
							</p>";
							

			}
			
			$modalBody .= "<hr/>";
		
			if (true){
				$modalBody .= "<h5>Chart Dimensions</h5>";			
				$modalBody .= "<p class='form-text'>(Please enter 0 or leave blank if you want the auto settings.)</p>";
				
				
				$modalBody .= "<div class='row'>";
					if (true){
						
						$modalBody .= "<div class='col-5'>";
						
						$candidates = array();
						$candidates['plot_width']['name'] 		= 'Chart Width';
						
						$candidates['plot_height']['name'] 		= 'Chart Height';
						
						$candidates['margin_top']['name'] 		= 'Margin Top';
						$candidates['margin_top']['default'] 	= $APP_CONFIG['APP']['Bubble_Plot']['margin']['Top'];
						
						$candidates['margin_bottom']['name'] 	= 'Margin Bottom';
						$candidates['margin_bottom']['default'] = $APP_CONFIG['APP']['Bubble_Plot']['margin']['Bottom'];
						
						$candidates['margin_left']['name'] 		= 'Margin Left';
						$candidates['margin_left']['default'] 	= $APP_CONFIG['APP']['Bubble_Plot']['margin']['Left'];
						
						$candidates['margin_right']['name'] 	= 'Margin Right';
						$candidates['margin_right']['default'] 	= $APP_CONFIG['APP']['Bubble_Plot']['margin']['Right'];
			
						foreach($candidates as $name => $tempValue){
							
							$displayName = $tempValue['name'];
							
							if (isset($dataArray[$name])){
								$value		= $dataArray[$name];
							} else {
								$value		= $tempValue['default'];
							}
							
							$value = abs(intval($value));
							
							$modalBody .= "<div style='margin-top:12px;'>";
							
								$modalBody .= "<div class='form-group row'>";
					
									$modalBody .= "<div class='col-4'>";
										$modalBody .= "<label for='{$name}' class='xcol-form-label'><strong>{$displayName}: &nbsp;</strong></label>";
									$modalBody .= "</div>";
					
					
									$modalBody .= "<div class='col-6'>";
										$modalBody .= "<input class='inputForm form-control' type='text' id='{$name}' name='{$name}' value='{$value}'/>";
									$modalBody .= "</div>";
									
								$modalBody .= "</div>";
								
							
							$modalBody .= "</div>";
						}
						
						$modalBody .= "</div>";
					}
					
					if (true){
						
						$modalBody .= "<div class='col-7'>";
						
							$modalBody .= "<img src='img/chart_dimension.png' class='img-fluid'/>";
						
						$modalBody .= "</div>";
					}
				$modalBody .= "</div>";
			}
			
			
			
			
		$modalBody .= "</div>";

		
		echo printModal($modalID, $modalTitle, $modalBody);
		
	}
	

	
	
	
	
	//Submit buttons
	if (true){
		echo "<div id='submitButtonGroup' class='form-group'>";
			echo "<br/>";
			echo "<button class='xcol-sm-offset-1 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot</button>";
			echo "&nbsp; &nbsp;<a data-toggle='modal' href='#advancedOptionSection'>" . printFontAwesomeIcon('fas fa-cog') . " Advanced Options</a>";
			echo "&nbsp; &nbsp;<a href='{$PAGE['URL']}'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
			echo "&nbsp;<span class='busySection startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
			echo "<input type='hidden' id='width' name='width' value='0'/>";
			echo "<input type='hidden' name='URL' value='{$PAGE['URL']}'/>";
			echo "<input type='hidden' name='submit' value='1'/>";
		echo "</div>";
	}

echo "</form>";

echo "<div id='feedbackSection_placeholder'>&nbsp;</div>";
echo "<div id='feedbackSection' class='startHidden'></div>";


?>
<style>
#inputSection{
	margin-left:20px;
	margin-bottom:50px;
}

#outputSection{
	margin-left:20px;
}

.modal-dialog{
	max-width:800px;
}
</style>

<script type="text/javascript">

$(document).ready(function(){
	$('#width').val(document.getElementById("feedbackSection_placeholder").offsetWidth);
	
	$('#form_application').ajaxForm({ 
        target: '#feedbackSection',
        url: '<?php echo $PAGE['EXE']; ?>',
        type: 'post',
		beforeSubmit: beforeSubmit,
        success: showResponse
    });
	
	
	$('#GeneName').change(function(){
		refresh_yAxis();
		refresh_colorBy();
		refresh_subplotBy();
	});
	
	
	
	$('#y-axis').change(function(){
		refresh_yAxis();
	});
	
	
	$('.y_axis_settings').change(function(){
		var currentValue = $(this).val();
		
		if (currentValue == -1){
			$("#y_axis_settings_customize_modal").modal();
		}
		
	});
	
	$('#y_axis_settings_customize_modal_trigger').click(function(){
		var Gene 	= $('#GeneName').val();
		Gene 		= $.trim(Gene);
		
		if (Gene != ''){
			$('input:radio[name=y-axis_settings]').filter('[value=-1]').prop('checked', true);
		}
	});
	
	
	$('#y_axis_settings_customize_modal_body').on('change', '#y-axis_checkboxSelectAll', function(){
		$('.y-axis_Customize_Candidates').prop('checked', $(this).prop('checked'));
		update_yAxis_count();
	});
	
	$('#y_axis_settings_customize_modal_body').on('change', '.y-axis_Customize_Candidates', function(){
		update_yAxis_count();
	});


	
	$("#colorBy").change(function(){
		refresh_colorBy();
	});
	

	$('.colorBy_settings').change(function(){
		var currentValue = $(this).val();
		
		if (currentValue == -1){
			$("#colorBy_settings_customize_modal").modal();
		}
		
	});
	
	$('#colorBy_settings_customize_modal_trigger').click(function(){
		
		var Gene 	= $('#GeneName').val();
		Gene 		= $.trim(Gene);
		
		if (Gene != ''){
			$('input:radio[name=colorBy_settings]').filter('[value=-1]').prop('checked', true);
		}
	});
	
	
	$('#colorBy_settings_customize_modal_body').on('change', '#colorBy_checkboxSelectAll', function(){
		$('.colorBy_Customize_Candidates').prop('checked', $(this).prop('checked'));
		update_colorBy_count();
	});
	
	$('#colorBy_settings_customize_modal_body').on('change', '.colorBy_Customize_Candidates', function(){
		update_colorBy_count();
	});
	

	
	$("#subplotBy").change(function(){
		refresh_subplotBy();
	});
	
	$('.subplotBy_settings').change(function(){
		var currentValue = $(this).val();
		
		if (currentValue == -1){
			$("#subplotBy_settings_customize_modal").modal();
		}
		
	});
	
	$('#subplotBy_settings_customize_modal_trigger').click(function(){
		var Gene 	= $('#GeneName').val();
		Gene 		= $.trim(Gene);
		
		if (Gene != ''){
			$('input:radio[name=subplotBy_settings]').filter('[value=-1]').prop('checked', true);
		}
	});
	
	
	$('#subplotBy_settings_customize_modal_body').on('change', '#subplotBy_checkboxSelectAll', function(){
		$('.subplotBy_Customize_Candidates').prop('checked', $(this).prop('checked'));
		update_subplotBy_count();
	});
	
	$('#subplotBy_settings_customize_modal_body').on('change', '.subplotBy_Customize_Candidates', function(){
		update_subplotBy_count();
	});
	
	
	
	$('.marker').change(function(){
		refresh_yAxis();
		refresh_colorBy();
		refresh_subplotBy();
	});
	
	
	
	
	<?php if ($has_internal_data){ ?>
	$('#data_source_private').change(function(){
		updatePrivateSection();
	});
	
	$('.data_source_private_project_indexes').change(function(){
		updatePrivateProject(1);
	});
	
	
	$('#data_source_private_value_select_all_trigger').change(function(){
		refresh_yAxis();
		refresh_colorBy();
		refresh_subplotBy();
	});
	
	$('.data_source').change(function(){
		refresh_yAxis();
		refresh_colorBy();
		refresh_subplotBy();
	});
	
	updatePrivateProject(0);
	updatePrivateSection();
	<?php } ?>
	
	$('#form_application').on('change', 'input', function(){
		$('#feedbackSection').empty();
	});
	
	$('#form_application').on('change', 'select', function(){
		$('#feedbackSection').empty();
	});
	
	<?php if (!$submitHoldOff && array_size($dataArray) > 0){ ?>
		<?php if (!$modal['y-axis']){ ?>
			refresh_yAxis();
		<?php } ?>
		
		<?php if (!$modal['colorBy']){ ?>
			refresh_colorBy();
		<?php } ?>
		
		<?php if (!$modal['subplotBy']){ ?>
			refresh_subplotBy();
		<?php } ?>
	
	
		update_yAxis_count();
		update_colorBy_count();
		update_subplotBy_count();
		$('#form_application').submit();
	<?php } ?>
	
});

<?php if ($has_internal_data){ ?>
function updatePrivateProject(refreshModal){
	var selectedCount = 0;
	
	$('.data_source_private_project_indexes').each(function() {
		if ($(this).prop('checked')){
			selectedCount++;
		}
	});
	
	selectedCount = parseInt(selectedCount);
	
	$('#data_source_private_selected_count').html(selectedCount);
	
	if (refreshModal == 1){
		refresh_yAxis();
		refresh_colorBy();
		refresh_subplotBy();
	}
}

function updatePrivateSection(){
	var isChecked = $('#data_source_private').prop('checked');
		
	if (isChecked){
		$('#data_source_private_section').show();
	} else {
		$('#data_source_private_section').hide();
	}
}
<?php } ?>

function beforeSubmit() {

	$('#feedbackSection').empty();
	$('#feedbackSection').hide();
	$('.busySection').show();
	
	return true;
}

function showResponse(responseText, statusText) {
	responseText = $.trim(responseText);

	$('.busySection').hide();
	$('#feedbackSection').html(responseText);
	$('#feedbackSection').show();
	
	$('html,body').animate({
		scrollTop: $('#feedbackSection').offset().top
	});
	
	return true;

}

function refresh_yAxis(){
	
	var Gene 	= $('#GeneName').val();
	Gene 		= $.trim(Gene);
	
	if (Gene != ''){
		
		var data = new Object();
	
		data['GeneName'] 		= Gene;
		data['Column'] 			= $('#y-axis').val();
		data['marker'] 			= $("input[name='marker']:checked"). val();
		data['type'] 			= 'y-axis';
		
		<?php if ($has_internal_data){ ?>
		data['data_source[]']	= [];
		data['data_source_private_project_indexes[]']	= [];
		
		$('.data_source:checked').each(function() {
			data['data_source[]'].push($(this).val());
		});
		
		
		$('.data_source_private_project_indexes:checked').each(function() {
			data['data_source_private_project_indexes[]'].push($(this).val());
		});
		<?php } ?>
	
		$.ajax({
			type: 'POST',
			url: 'app_bubble_plot_single_ajax.php',
			data: data,
			success: function(responseText){
				responseText = $.trim(responseText);
				
				if (responseText != ''){
					$('#y_axis_settings_customize_modal_body').html(responseText);
					update_yAxis_count();
				}
			}
		});	
	}
}

function refresh_colorBy(){
	
	var Gene 	= $('#GeneName').val();
	Gene 		= $.trim(Gene);
	
	if (Gene != ''){
		
		var data = new Object();
	
		data['GeneName'] 		= Gene;
		data['Column'] 			= $('#colorBy').val();
		data['marker'] 			= $("input[name='marker']:checked"). val();
		data['type'] 			= 'colorBy';
		
		<?php if ($has_internal_data){ ?>
		data['data_source[]']	= [];
		data['data_source_private_project_indexes[]']	= [];
		
		$('.data_source:checked').each(function() {
			data['data_source[]'].push($(this).val());
		});
		
		
		$('.data_source_private_project_indexes:checked').each(function() {
			data['data_source_private_project_indexes[]'].push($(this).val());
		});
		<?php } ?>
	
		$.ajax({
			type: 'POST',
			url: 'app_bubble_plot_single_ajax.php',
			data: data,
			success: function(responseText){
				responseText = $.trim(responseText);
				
				if (responseText != ''){
					$('#colorBy_settings_customize_modal_body').html(responseText);
					update_colorBy_count();
				}
			}
		});	
	}
}

function refresh_subplotBy(){
	
	var Gene 	= $('#GeneName').val();
	Gene 		= $.trim(Gene);
	
	if (Gene != ''){
		
		var data = new Object();
	
		data['GeneName'] 		= Gene;
		data['Column'] 			= $('#subplotBy').val();
		data['marker'] 			= $("input[name='marker']:checked"). val();
		data['type'] 			= 'subplotBy';
		
		<?php if ($has_internal_data){ ?>
		data['data_source[]']	= [];
		data['data_source_private_project_indexes[]']	= [];
		
		$('.data_source:checked').each(function() {
			data['data_source[]'].push($(this).val());
		});
		
		
		$('.data_source_private_project_indexes:checked').each(function() {
			data['data_source_private_project_indexes[]'].push($(this).val());
		});
		<?php } ?>
	
		$.ajax({
			type: 'POST',
			url: 'app_bubble_plot_single_ajax.php',
			data: data,
			success: function(responseText){
				responseText = $.trim(responseText);
				
				if (responseText != ''){
					$('#subplotBy_settings_customize_modal_body').html(responseText);
					update_subplotBy_count();
				}
			}
		});	
	}
}

function update_yAxis_count(){
	var count = 0

	$('.y-axis_Customize_Candidates').each(function() {
		if ($(this).prop('checked')) count++;
	});
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#y_axis_settings_count_message').html(message);
	return true;
}

function update_colorBy_count(){
	var count = 0

	$('.colorBy_Customize_Candidates').each(function() {
		if ($(this).prop('checked')) count++;
	});
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#colorBy_settings_count_message').html(message);
	return true;
}


function update_subplotBy_count(){
	var count = 0

	$('.subplotBy_Customize_Candidates').each(function() {
		if ($(this).prop('checked')) count++;
	});
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#subplotBy_settings_count_message').html(message);
	return true;
}



</script>