<?php

$dataArray = getSQLCache($_GET['key']);


if (!$useCustomColumn){
	$defaultGeneExpressionOptions = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options'];
	$defaultGeneExpressionOptionsCompleted = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options_Completed'];	
	$defaultGeneExpressionOptionsCompleted_Splitted_2 = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options_Splitted_2'];
}



$currentTable = 'Samples';
echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";


	if (true){
		echo "<div class='row'>";
		
			if (true){
				echo "<div class='col-lg-5'>";
					echo "<div style='margin-top:10px;'>";
						echo "<table>";
					echo "<tr>";
						if (true){
							echo "<td>";
								echo "<strong>{$APP_MESSAGE['Gene Names']}:</strong>";
							echo "</td>";
						}
						
						if (true){
							echo "<td>";
								echo "&nbsp;";
							echo "</td>";	
						}
						
						if (true){
							echo "<td>";
								echo "<a href='#geneListModal' data-toggle='modal'>" . printFontAwesomeIcon('fas fa-shopping-cart') . " {$APP_MESSAGE['Load Saved Genes']}</a>";
							echo "</td>";
						}
						
						if (true){
							echo "<td>";
								echo "&nbsp; &nbsp;";
							echo "</td>";	
						}
						
						
						if (true){
							echo "<td>";
								echo genesets_api_get_header_code('', '', 'GeneNames');
								echo genesets_api_get_body_code();
							echo "</td>";
						}
						
					echo "</tr>";
				echo "</table>";
					echo "</div>";
					
					$values = implode("\n", $dataArray['GeneNames']);
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control heatmapInputClass' rows='8' name='GeneNames' id='GeneNames' placeholder='Please enter two or more {$APP_MESSAGE['gene']} names, seperated by line break.'>{$values}</textarea>";
					echo "</div>";
					
					
					$modalID 	= 'geneListModal';
					$modalTitle = $APP_MESSAGE['Please select a gene list you like to load:'];
					$modalBody	= '';
					echo printModal($modalID, $modalTitle, $modalBody);
					
				echo "</div>";
			}
			
			
			if (!$APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample']){
				echo "<div class='col-lg-5'>";
					echo "<div style='margin-top:10px;'>";
						echo "<strong>Sample IDs:</strong> 
								<a href='#sampleListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Sample IDs</a>";
					echo "</div>";
					
					$values = implode("\n", $dataArray['SampleIDs']);
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control heatmapInputClass' rows='8' name='SampleIDs' id='SampleIDs' placeholder='Please enter two or more sample IDs, seperated by line break.'>{$values}</textarea>";
					echo "</div>";
					
					$modalID 	= 'sampleListModal';
					$modalTitle = 'Please select a sample list you like to load:';
					$modalBody	= '';
					echo printModal($modalID, $modalTitle, $modalBody);
					
				echo "</div>";
			} else {
				echo "<div class='col-lg-5'>";
					echo "<div style='margin-top:10px;'>";
					
						unset($tempArray);
						$tempArray[0] = 'Sample ID';
						$tempArray[1] = 'Comparison ID';


						echo "<select class='heatmapInputClass' id='searchOption' name='searchOption'>";
							foreach($tempArray as $tempKey => $tempValue){
								
								unset($selected);
								if (isset($dataArray['searchOption'])){
									if ($tempKey == $dataArray['searchOption']){
										$selected = 'selected';
										$searchOption = $tempKey;
									}
								} elseif ($tempKey == 0){
									$selected = 'selected';	
								}
								
								echo "<option id='searchOption_{$tempKey}_Section' value='{$tempKey}' {$selected}>{$tempValue}</option>";
							}
						echo "</select> &nbsp;";

						if (true){
							unset($class);
							if ($searchOption != 0){
								$class = 'startHidden';	
							}
							echo "<span class='searchOptionRelated searchOptionRelated_0 {$class}'><a href='#sampleListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Sample IDs</a></span>";
						}
						
						if (true){
							unset($class);
							if ($searchOption != 1){
								$class = 'startHidden';	
							}
							echo "<span class='searchOptionRelated searchOptionRelated_1 {$class}'><a href='#comparisonListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparison IDs</a></span>";
						}
					echo "</div>";
					

					echo "<div style='margin-top:10px;'>";
					
						if (true){
							unset($class);
							if ($searchOption != 0){
								$class = 'startHidden';	
							}
							$values = implode("\n", $dataArray['SampleIDs']);
							echo "<textarea class='searchOptionRelated searchOptionRelated_0 form-control heatmapInputClass {$class}' rows='8' name='SampleIDs' id='SampleIDs' placeholder='Please enter two or more sample IDs, seperated by line break.'>{$values}</textarea>";
							
							$modalID 	= 'sampleListModal';
							$modalTitle = 'Please select a sample list you like to load:';
							$modalBody	= '';
							echo printModal($modalID, $modalTitle, $modalBody);
						}
						
						
						if (true){
							unset($class);
							if ($searchOption != 1){
								$class = 'startHidden';	
							}
							$values = implode("\n", $dataArray['ComparisonIDs']);
							echo "<textarea class='searchOptionRelated searchOptionRelated_1  form-control heatmapInputClass {$class}' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='Please enter two or more comparison IDs, seperated by line break.'>{$values}</textarea>";
							
							$modalID 	= 'comparisonListModal';
							$modalTitle = 'Please select a comparison list you like to load:';
							$modalBody	= '';
							echo printModal($modalID, $modalTitle, $modalBody);
						}
						
						
					echo "</div>";
					

					
				echo "</div>";
				
				
				
				
				
				
				
				
			}
		echo "</div>";
		
		
		
		echo "<div class='row'><div class='col-lg-10'><div id='heatmapFeedback' class='startHidden'></div></div></div>";
	}
	
	
	
	
	if (array_size($defaultGeneExpressionOptions) > 0){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<div style='margin-top:20px;'>";
				echo "<strong>Sample Attributes: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>";
				echo "<input class='form-check-input Plot_Columns_Select_All_Trigger' type='checkbox'/>Select All";
				echo "&nbsp;";
				echo "<span id='Plot_Columns_Message' style='font-weight:normal;'></span>";
			echo "</div>";
			
			echo "<div style='margin-left:15px;'>";
			foreach($defaultGeneExpressionOptions as $currentColumn => $currentDetails){
				
				$randomID = md5($currentColumn . '::' .  rand());
				
				$class		= "Plot_Columns_" . md5($currentColumn);
				
				$checked 	= '';
				
				if (!isset($dataArray['Plot_Columns'])){
					if ($currentDetails['Default']){
						$checked = 'checked';	
					}
				} else {
					if (in_array($currentColumn, $dataArray['Plot_Columns'])){
						$checked = 'checked';	
					}
				}
				
				$currentTitle = $currentDetails['Title'];
				if ($currentTitle == ''){
					$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
				}
					
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<input type='checkbox' id='{$randomID}' class='form-check-input Plot_Columns {$class}' sibling='{$class}' name='Plot_Columns[]' value='{$currentColumn}' {$checked}/>
							{$currentTitle}
					  	</label>";
				echo "</div>";
				
			}
			
			
			if (array_size($defaultGeneExpressionOptionsCompleted) != array_size($defaultGeneExpressionOptions)){
				$randomID = md5($currentColumn . '::' .  rand() . rand());
				
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<a href='#Plot_Columns_Modal' data-toggle='modal' class='nowrap'>(More Attributes)</a>
							</label>";
				echo "</div>";
			}
			echo "</div>";
			
			
			
			if (array_size($defaultGeneExpressionOptionsCompleted) > 0){
				
				$modalID 	= 'Plot_Columns_Modal';
				$modalTitle = "<div class='modal-title'>";
					$modalTitle	.= "<h4>Additional Sample Attributes</h4>";

					$modalTitle	.= "<div style='margin-left:15px;'>";
						$modalTitle .= "<label class='form-check-label'>
										<input class='form-check-input Plot_Columns_Select_All_Trigger' type='checkbox'/>Select All
										</label>";
					$modalTitle	.= "</div>";
				$modalTitle	.= "</div>";
				
				
				unset($modalBody);
				
				$modalBody	.= "<div class='row'>";
				foreach($defaultGeneExpressionOptionsCompleted_Splitted_2 as $remainder => $tempValue){
					
					$modalBody	.= "<div class='col-lg-6'>";
					foreach($tempValue as $currentColumn => $currentDetails){
							
						$randomID 	= md5($currentColumn . '::' .  rand());
						
						$class		= "Plot_Columns_" . md5($currentColumn);
					
						$checked = '';
						
						if (array_size($dataArray) <= 0){
							if ($currentDetails['Default']){
								$checked = 'checked';	
							}
						} elseif (in_array($currentColumn, $dataArray['Plot_Columns'])){
							$checked = 'checked';
						}
						
						$currentTitle = $currentDetails['Title'];
						if ($currentTitle == ''){
							$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
						}
						
						
						$modalBody	.= "<div class='form-check'>";
							$modalBody .= "<label for='{$randomID}' class='form-check-label'>
											<input type='checkbox' id='{$randomID}' class='form-check-input Plot_Columns {$class}' sibling='{$class}' name='Plot_Columns[]' value='{$currentColumn}' {$checked}/>
											{$currentTitle}
											</label>";
						$modalBody 	.= "</div>";
						
						
					}
					$modalBody	.= "</div>";
					
				}
				$modalBody	.= "</div>";
				
				echo printModal($modalID, $modalTitle, $modalBody);
				
			}
			
			
		
			
		
		echo "</div>";

	
	
		echo "</div>";
	}
	
	
	
	
	if ($APP_CONFIG['APP']['Module']['Modified_DiseaseState']){
		
		echo "<div id='Modified_DiseaseState_Section' class='startHidden'>";
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			unset($checked);
			if (array_size($dataArray) > 0){
				if ($dataArray['Modified_DiseaseState_Enable']){
					$checked = 'checked';
					$class = '';
				} else {
					$checked = '';
					$class = 'startHidden';	
				}
			} else {
				if ($APP_CONFIG['APP']['Precheck_Modified_DiseaseState']){
					$checked = 'checked';
					$class = '';
				} else {
					$checked = '';
					$class = 'startHidden';	
				}
			}

			echo "<div class='form-check'>";
				echo "<label class='form-check-label' for='Modified_DiseaseState_Enable'>";
					echo "<input class='form-check-input Modified_DiseaseState' type='checkbox' name='Modified_DiseaseState_Enable' id='Modified_DiseaseState_Enable' value='1' {$checked}>";
						echo "<strong>Update Disease State with Comparison Category</strong><span class='startHidden'> (<a href='#Modified_DiseaseState_Modal' data-toggle='modal'>Help</a>)</span><strong>:</strong>";
					echo "</label>";
			echo "</div>";
			

		
			echo "<div id='Modified_DiseaseState_ComparisonCategory_Section' class='{$class}' style='margin-bottom:8px; margin-left:15px;'>";
			
			
				echo "<div xclass='form-check-inline'>Display sample data from the following comparison categories: </div>";
				foreach($APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory'] as $tempKey => $tempValue){
					
					unset($checked);
					
					if ($dataArray['Modified_DiseaseState_Enable']){
						if (in_array($tempKey, $dataArray['Modified_DiseaseState_ComparisonCategory'])){
							$checked = 'checked';
						}
					} else {
						$checked = 'checked';
					}
					
					$currentID = "Modified_DiseaseState_ComparisonCategory_" . md5($tempKey);
					
					echo "<div class='form-check-inline'>";
						echo "<label class='form-check-label' for='{$currentID}'>";
							echo "<input class='form-check-input Modified_DiseaseState_ComparisonCategory Modified_DiseaseState' type='checkbox' name='Modified_DiseaseState_ComparisonCategory[]' id='{$currentID}' value='{$tempKey}' {$checked}>";
								echo '&nbsp;' . $tempValue . '&nbsp;';
							echo "</label>";
					echo "</div>";
				}
				
				echo "<br/>";
				echo "<div xclass='form-check-inline' style='margin-top:15px;'>Other Option: </div>";
				
				
				if (true){
					
					unset($checked);
					if (array_size($dataArray) > 0){
						if ($dataArray['Modified_DiseaseState_Display_Abbreviation']){
							$checked = 'checked';
						}
					} else {
						$checked = 'checked';
					}
					
					$displayValue = 'Display Abbreviation if Available';


					echo "<div class='form-check-inline'>";
						echo "<label class='form-check-label' for='Modified_DiseaseState_Display_Abbreviation'>";
							echo "<input class='form-check-input Modified_DiseaseState' type='checkbox' name='Modified_DiseaseState_Display_Abbreviation' id='Modified_DiseaseState_Display_Abbreviation' value='1' {$checked}>";
								echo '&nbsp;' . $displayValue . '&nbsp;';
							echo "</label>";
					echo "</div>";
				}
				
			
			echo "</div>";

			
			
			
			if (true){
				unset($modalBody);					
				$modalID 	= 'Modified_DiseaseState_Modal';
				$modalTitle = "<h4 class='modal-title'>Modified Distate State</h4>";
				$modalBody	.= "<div>";
					$modalBody .= "<p>Help content goes here...</p>";
				
				$modalBody	.= "</div>";
				echo printModal($modalID, $modalTitle, $modalBody);
			}
			
			
			
			
		
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}
	
	if (has_internal_data()){
		
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
	
	
	if (array_size($APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown']) > 0){
		unset($class);
		if ($searchOption != 1){
			$class = 'startHidden';	
		}
		
		echo "<div class='row searchOptionRelated searchOptionRelated_1 {$class}'>";
		echo "<div class='col-lg-12'>";
			echo "<div style='margin-top:8px;'>";
				echo "<strong>Comparison Attributes:</strong>";
			echo "</div>";
			
			
			
			foreach($APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'] as $currentColumn => $currentTitle){
				
				if ($APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'] == '') continue;
				
				$randomID 		= md5($currentColumn . '::' .  rand());
				
				$checked 		= '';
				
				if (array_size($dataArray) > 0){
					if (in_array($currentColumn, $dataArray['Plot_Columns_Comparison'])){
						$checked = 'checked';	
					}
				} else {
					$checked = 'checked';
				}
				
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<input type='checkbox' id='{$randomID}' class='form-check-input' name='Plot_Columns_Comparison[]' value='{$currentColumn}' {$checked}/>
							{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title']}
							</label>";
				echo "</div>";
				
			}
		
		echo "</div>";
	
	
		echo "</div>";
	}
	
	
	if (true){
		echo "<div class='row' style='margin-top:20px;'>";
			echo "<input type='hidden' name='submit' value='1'/>";
			echo "<div class='form-group'>";
				echo "<input type='hidden' id='width' name='width' value='0'/>";
				echo "<button class='col-sm-offset-0 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot Heatmap</button>";
				echo "&nbsp;<a data-toggle='modal' href='#advancedOptionSection'>" . printFontAwesomeIcon('fas fa-cog') . " Advanced Options</a>";
				echo "&nbsp;&nbsp;<span id='cancelSection' class='startHidden'><a href='javascript:void(0);' id='cancelTrigger'>" . printFontAwesomeIcon('fas fa-pause') . "Cancel Submission</a></span>";
				echo "&nbsp;&nbsp;<span id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</span>";
			echo "</div>";
		echo "</div>";
	}
	
	
	//Modal
	if (true){
		$modalID 	= 'advancedOptionSection';
		$modalTitle = "<h4 class='modal-title'>Advanced Options</h4>";

		$modalBody = "<div>";
		
			$modalBody .= "<h5 class='modal-title'>Data Options</h5>";
			
			if (true){
				unset($checked, $class);
				
				if (isset($dataArray['transform'])){
					if ($dataArray['transform']){
						$checked = 'checked';
					} else {
						$class = 'startHidden';	
					}
				} else {
					$checked = 'checked';
				}
				
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='transform' class='form-check-label'>
										<input class='form-check-input' type='checkbox' id='transform' name='transform' value='1' {$checked}/>
										<strong>Enable Log<sub>2</sub> Transform</strong>
									</label>";
					$modalBody .= "&nbsp;";
				$modalBody .= "</div>";
			}
			
			
			if (true){
				$modalBody .= "<div class='transform_section {$class}' style='margin-left:30px;'>";
				
					if (isset($dataArray['transform_value']) && ($dataArray['transform_value'] >= 0)){
						$value = $dataArray['transform_value'];
					} else {
						$value = $APP_CONFIG['canvasxpress']['Log_Add_Value'];	
					}
					
					$value = abs(floatval($value));
					$modalBody .= "<div class='form-group'>";
						$modalBody .= "<label for='transform_value' class='col-form-label'>Value to Be Added for Log Transformation: &nbsp;</label>";
						$modalBody .= "<input class='col-2' type='text' id='transform_value' name='transform_value' value='{$value}'/>";
					$modalBody .= "</div>";
				
				$modalBody .= "</div>";
			}
			
			
			
			if (true){
				unset($checked);
				if (isset($dataArray['zscore'])){
					if ($dataArray['zscore']){
						$checked = 'checked';
					}
				} else {
					$checked = 'checked';	
				}
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='zscore' class='form-check-label'><input class='form-check-input' type='checkbox' id='zscore' name='zscore' value='1' {$checked}/><strong>Enable Z-Score Transformation</strong></label>";
				$modalBody .= "</div>";
			}
			
			
			
			
			if (true){
				
				$sql_name = 'upper_limit_enable';
				unset($checked, $class);
				
				if (isset($dataArray[$sql_name])){
					if ($dataArray[$sql_name]){
						$checked = 'checked';
					} else {
						$class = 'startHidden';	
					}
				} else {
					$checked = 'checked';
				}
				
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<input class='form-check-input' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked}/>
									<label for='{$sql_name}' class='form-check-label'>
										<strong>Enable Upper Limit</strong>
									  </label>";
					
					$sql_name = 'upper_limit_value';
				
					$modalBody .= "<span class='upper_limit_section {$class}'>: ";
					
					
						if (isset($dataArray[$sql_name])){
							$value = $dataArray[$sql_name];
							$value = floatval($value);
						} else {
							$value = $APP_CONFIG['canvasxpress']['Range_Upper_Limit'];	
						}
						
						$modalBody .= "<input class='col-2' type='text' class='form-control' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
					
					$modalBody .= "</span>";
					
					
				$modalBody .= "</div>";
			}
			
			
			
			if (true){
				
				$sql_name = 'lower_limit_enable';
				unset($checked, $class);
				
				if (isset($dataArray[$sql_name])){
					if ($dataArray[$sql_name]){
						$checked = 'checked';
					} else {
						$class = 'startHidden';	
					}
				} else {
					$checked = 'checked';
				}
				
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<input class='form-check-input' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked}/>
								<label for='{$sql_name}' class='form-check-label'><strong>Enable Lower Limit</strong></label>";
					
					$sql_name = 'lower_limit_value';
				
					$modalBody .= "<span class='lower_limit_section {$class}'>: ";
					
					
						if (isset($dataArray[$sql_name])){
							$value = $dataArray[$sql_name];
							$value = floatval($value);
						} else {
							$value = $APP_CONFIG['canvasxpress']['Range_Lower_Limit'];	
						}
						
						$modalBody .= "<input class='col-2' type='text' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
					
					$modalBody .= "</span>";
					
					
				$modalBody .= "</div>";
			}
			
			
			
			
			
			
			if (true){
				unset($checked);
				if (isset($dataArray['variablesClustered'])){
					if ($dataArray['variablesClustered']){
						$checked = 'checked';
					}
				} else {
					$checked = 'checked';	
				}
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='variablesClustered' class='form-check-label'><input class='form-check-input' type='checkbox' id='variablesClustered' name='variablesClustered' value='1' {$checked}/><strong>Cluster Genes</strong></label>";
				$modalBody .= "</div>";
			}
			
			if (true){
				unset($checked);
				if (isset($dataArray['samplesClustered'])){
					if ($dataArray['samplesClustered']){
						$checked = 'checked';
					}
				} else {
					$checked = 'checked';	
				}
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='samplesClustered' class='form-check-label'><input class='form-check-input' type='checkbox' id='samplesClustered' name='samplesClustered' value='1' {$checked}/><strong>Cluster Samples</strong></label>";
				$modalBody .= "<br/><br/><br/></div>";
			}
			
			
			
			$modalBody .= "<h5 class='modal-title'>Display Options</h5>";
			
			if (true){
				unset($checked);
				if (isset($dataArray['sampleOverLay'])){
					if ($dataArray['sampleOverLay']){
						$checked = 'checked';
					}
				} else {
					$checked = 'checked';	
				}
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='sampleOverLay' class='form-check-label'><input class='form-check-input' type='checkbox' id='sampleOverLay' name='sampleOverLay' value='1' {$checked}/><strong>Overlays Samples</strong></label>";
				$modalBody .= "</div>";
			}
			
			if (true){
				unset($checked);
				if (isset($dataArray['showVariableNames'])){
					if ($dataArray['showVariableNames']){
						$checked = 'checked';
					}
				} else {
					$checked = 'checked';	
				}
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='showVariableNames' class='form-check-label'><input class='form-check-input' type='checkbox' id='showVariableNames' name='showVariableNames' value='1' {$checked}/><strong>Display Genes Names</strong></label>";
				$modalBody .= "</div>";
			}
			
			
			if (true){
				unset($checked);
				if (isset($dataArray['showSampleNames'])){
					if ($dataArray['showSampleNames']){
						$checked = 'checked';
					}
				} else {
					$checked = 'checked';	
				}
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<label for='showSampleNames' class='form-check-label'><input class='form-check-input' type='checkbox' id='showSampleNames' name='showSampleNames' value='1' {$checked}/><strong>Display Sample IDs</strong></label>";
				$modalBody .= "</div>";
			}
		
		$modalBody .= "</div>";

		
		echo printModal($modalID, $modalTitle, $modalBody);
		
	}
	
	

echo "</form>";

echo "<div id='feedbackSection_placeholder'>&nbsp;</div>";
echo "<div id='feedbackSection' class='startHidden'></div>";
//echo "<input type='hidden' id='global_ajax_form_counter_server' value=0/>";

//echo "<a href='javascript:void(0);' id='xxx'>Abort Ajax</a>";
?>

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
	
	<?php if ($APP_CONFIG['APP']['Module']['Modified_DiseaseState']){ ?>
	$('#Modified_DiseaseState_Enable').change(function(){
		
		$('#dataFilterSection').empty();
		var currentValue = $(this).prop('checked');
		
		if (currentValue){
			$('#Modified_DiseaseState_ComparisonCategory_Section').show();
		} else {
			$('#Modified_DiseaseState_ComparisonCategory_Section').hide();
		}
		
	});	
	
	$('.Plot_Columns_<?php echo md5('DiseaseState'); ?>').change(function(){
		
		var currentValue = $(this).prop('checked');
		
		if (currentValue){
			$('#Modified_DiseaseState_Section').show();
		} else {
			$('#Modified_DiseaseState_Section').hide();
		}
	});	
	
	$('.Modified_DiseaseState').change(function(){
		$('#dataFilterSection').empty();
	});	
	
	if ($('.Plot_Columns_<?php echo md5('DiseaseState'); ?>').prop('checked')){
		$('#Modified_DiseaseState_Section').show();
	}
	<?php } ?>
	
	$('.Plot_Columns').change(function(){
		var currentValue = $(this).prop('checked');
		
		var currentClass = $(this).attr('sibling');
		
		$('.' + currentClass).prop('checked', currentValue);
		
		updateSelectedSampleAttributes();
	});

	$(document).on('click', '#geneMissingInfoTrigger', function(){
		$('#geneMissingInfo').toggle();
	});
	
	$(document).on('click', '#sampleMissingInfoTrigger', function(){
		$('#sampleMissingInfo').toggle();
	});
	
	$(document).on('click', '#summaryTrigger', function(){
		$('#summarySection').toggle();
	});
	
	
	$('#summaryTrigger').click(function(){
		$('#summarySection').toggle();
	});
	
	$('#transform').change(function(){
		var checked = $(this).prop('checked');
		
		if (checked){
			$('.transform_section').show();
		} else {
			$('.transform_section').hide();
		}
	});
	
	$('#upper_limit_enable').change(function(){
		var checked = $(this).prop('checked');
		
		if (checked){
			$('.upper_limit_section').show();
		} else {
			$('.upper_limit_section').hide();
		}
	});
	
	$('#lower_limit_enable').change(function(){
		var checked = $(this).prop('checked');
		
		if (checked){
			$('.lower_limit_section').show();
		} else {
			$('.lower_limit_section').hide();
		}
	});
	
	
	$('#form_application').on('change', 'input', function(){
		$('#feedbackSection').empty();
	});
	
	$('#form_application').on('change', 'textarea', function(){
		$('#feedbackSection').empty();
	});
	
	
	
	$('.heatmapInputClass').change(function(){

		var GeneNames = $('#GeneNames').val();
		var SampleIDs = $('#SampleIDs').val();

		var randomNumber = Math.random();
		
		$('#heatmapFeedback').empty();
		$('#heatmapFeedback').hide();
		
		$.ajax({
			type: 'POST',
			url: 'app_heatmap_ajax.php?action=1&random=' + randomNumber,
			data: 'GeneNames=' + GeneNames + '&SampleIDs=' + SampleIDs,
			success: function(responseText){
				responseText = $.trim(responseText);
				
				if (responseText != ''){
					$('#heatmapFeedback').show();
					$('#heatmapFeedback').html(responseText);
				}
			}
		});
	});
	
	$('#geneListModal').on('show.bs.modal', function(){
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Gene&input_name=geneList&input_class=geneList&pre_selected_list_id=<?php echo $geneListID; ?>',
			success: function(responseText){
				$('#geneListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#geneListModal').on('change', '.geneList', function(){
		var currentListID = $(this).val();
		
		var content = $('#gene_list_content_' + currentListID).val();
		
		$('#GeneNames').val(content);
	});
	
	
	$('#geneListModal').on('click', '.geneList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'geneList_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#gene_list_content_' + currentListID).val();
		
		$('#GeneNames').val(content);
	});
	
	
	$('#searchOption').change(function(){
		var currentValue = $(this).val();
		
		$('.searchOptionRelated').hide();
		
		$('.searchOptionRelated_' + currentValue).show();
	});
	
	$('#cancelTrigger').click(function(){
		global_ajax_form_counter_client = 0;
		$('#feedbackSection').empty();
		$('#busySection').hide();
		$('#cancelSection').hide();
		
	});
	
	
	<?php if ($APP_CONFIG['APP']['Module']['Comparison']){ ?>
	$('#comparisonListModal').on('show.bs.modal', function(){
		
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Comparison&input_name=list&input_class=list&pre_selected_list_id=<?php echo $comparisonListID; ?>',
			success: function(responseText){
				$('#comparisonListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#comparisonListModal').on('change', '.list', function(){
		var currentListID = $(this).val();
		
		var content = $('#comparison_list_content_' + currentListID).val();
		
		$('#ComparisonIDs').val(content);
	});
	
	
	$('#comparisonListModal').on('click', '.list_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'list_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#comparison_list_content_' + currentListID).val();
		
		$('#ComparisonIDs').val(content);
	});
	
	
	$(document).on('click', '#comparisonMissingInfoTrigger', function(){
		$('#comparisonMissingInfo').toggle();
	});
	<?php } ?>
	
	
	$('#sampleListModal').on('show.bs.modal', function(){
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Sample&input_name=sampleList&input_class=sampleList&pre_selected_list_id=<?php echo $sampleListID; ?>',
			success: function(responseText){
				$('#sampleListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#sampleListModal').on('change', '.sampleList', function(){
		var currentListID = $(this).val();
		
		var content = $('#sample_list_content_' + currentListID).val();
		
		$('#SampleIDs').val(content);
	});
	
	
	$('#sampleListModal').on('click', '.sampleList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'sampleList_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#sample_list_content_' + currentListID).val();
		
		$('#SampleIDs').val(content);
	});
	
	updateSelectedSampleAttributes();
	
	$('.Plot_Columns_Select_All_Trigger').change(function(){
		var currentValue = $(this).prop('checked');
		
		if (currentValue){
			$('.Plot_Columns').prop('checked', true);
			$('.Plot_Columns_Select_All_Trigger').prop('checked', true);
		} else {
			$('.Plot_Columns').prop('checked', false);
			$('.Plot_Columns_Select_All_Trigger').prop('checked', false);
		}
		
		updateSelectedSampleAttributes();
	});
	
	<?php if ($dataArray['submit']){ ?>
		$('#form_application').submit();
	<?php } ?>
	
	
	<?php if ($has_internal_data){ ?>
	$('#data_source_private').change(function(){
		updatePrivateSection();
	});
	
	$('.data_source_private_project_indexes').change(function(){
		updatePrivateProject();
	});
	
	updatePrivateProject();
	updatePrivateSection();
	<?php } ?>
	
	
});


<?php if ($has_internal_data){ ?>

function updatePrivateProject(){
	var selectedCount = 0;
	
	$('.data_source_private_project_indexes').each(function() {
		if ($(this).prop('checked')){
			selectedCount++;
		}
	});
	
	selectedCount = parseInt(selectedCount);
	
	$('#data_source_private_selected_count').html(selectedCount);
	
}

function updatePrivateSection(){
	var isChecked = $('#data_source_private').prop('checked');
		
	if (isChecked){
		$('#data_source_private_section').show();
		
		$('#searchOption').val(0);
		$('#searchOption').change();
		
		//$('#searchOption_1_Section').hide();
		
	} else {

		$('#searchOption_1_Section').show();
		$('#data_source_private_section').hide();
	}
}

<?php } ?>

var global_ajax_form_counter_client = 0;
var global_ajax_form_counter_server = 0;
function beforeSubmit(arr, $form, options) {
	$('.advancedOptionsSection').hide();
	$('#feedbackSection').empty();
	$('#feedbackSection').hide();
	$('#busySection').show();
	$('#cancelSection').show();
	
	global_ajax_form_counter_client++;
	arr.push({name:'global_counter', value:global_ajax_form_counter_client});
	
	
	return true;
}


function showResponse(responseText, statusText) {

	

	if (global_ajax_form_counter_server == global_ajax_form_counter_client){
		responseText = $.trim(responseText);
	
		$('#busySection').hide();
		$('#cancelSection').hide();
		$('#feedbackSection').html(responseText);
		$('#feedbackSection').show();
		
		
		$('html,body').animate({
			scrollTop: $('#feedbackSection').offset().top
		});
	}
	
	return true;

}


function updateSelectedSampleAttributes(){

	var checked = new Object();
	var currentValue = '';
	

	$('.Plot_Columns').each(function() {
		if ($(this).prop('checked')){
			currentValue = $(this).val();
			checked[currentValue] = 1;
		}
	});
	
	var count = Object.keys(checked).length;
	
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#Plot_Columns_Message').html(message);
	
	
	return true;
		
}

</script>