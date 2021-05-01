<?php

$dataArray = getSQLCache($_GET['key']);

$getOldSavedMetaAnalysesCount = getOldSavedMetaAnalysesCount();

if ($getOldSavedMetaAnalysesCount > 0){
	echo "<p><a href='../plot/meta_analysis/my_results.php' target='_blank'>" . printFontAwesomeIcon('fas fa-external-link-alt') . " Saved Meta Analysis Result</a></p>";
}


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
					
					if (!$loadFromSession){
						$values = implode("\n", $dataArray['GeneNames']);
					} else {
						$values = implode("\n", $_SESSION['META_SELECTED_GENENAMES']);
					}
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control' rows='8' name='GeneNames' id='GeneNames' placeholder='Please use more than two {$APP_MESSAGE['genes']} or leave empty to explore all available {$APP_MESSAGE['genes']}.'>{$values}</textarea>";
					echo "</div>";
					
					
					$modalID 	= 'geneListModal';
					$modalTitle = $APP_MESSAGE['Please select a gene list you like to load:'];
					$modalBody	= '';
					echo printModal($modalID, $modalTitle, $modalBody, 'Select');
					
				echo "</div>";
			}
			
			
			if (true){
				echo "<div class='col-lg-5'>";
					echo "<div style='margin-top:10px;'>";
						echo "<strong>Comparison IDs:</strong> 
								<a href='#comparisonListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparison IDs</a>";
					echo "</div>";
					
					if (!$loadFromSession){
						$values = implode("\n", $dataArray['ComparisonIDs']);
					} else {
						$values = implode("\n", $_SESSION['META_SELECTED_COMPNAMES']);
					}
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='Please enter two or more comparison IDs, seperated by line break.'>{$values}</textarea>";
					echo "</div>";
					
					$modalID 	= 'comparisonListModal';
					$modalTitle = 'Please select a comparison list you like to load:';
					$modalBody	= '';
					echo printModal($modalID, $modalTitle, $modalBody, 'Select');
					
				echo "</div>";
			}
		echo "</div>";
	}
	



	
	
	if (array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Meta_Analysis_Options']) > 0){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<div style='margin-top:20px;'>";
				echo "<strong>{$APP_MESSAGE['Gene Attributes']} <span id='Gene_Plot_Columns_Message' style='font-weight:normal;'></span>:</strong>";
			echo "</div>";
			
			
			foreach($APP_CONFIG['DB_Dictionary']['GeneCombined']['Meta_Analysis_Options'] as $currentColumn => $currentDetails){
				
				$randomID = md5($currentColumn . '::' .  rand());
				
				$class		= "Gene_Plot_Columns_" . md5($currentColumn);
				
				$checked 	= '';
				
				if (!isset($dataArray['Gene_Plot_Columns'])){
					if ($currentDetails['Default']){
						$checked = 'checked';	
					}
				} else {
					if (in_array($currentColumn, $dataArray['Gene_Plot_Columns'])){
						$checked = 'checked';	
					}
				}
					
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<input type='checkbox' id='{$randomID}' class='form-check-input Gene_Plot_Columns {$class}' sibling='{$class}' name='Gene_Plot_Columns[]' value='{$currentColumn}' {$checked}/>
							{$APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$currentColumn]['Title']}
					  	</label>";
				echo "</div>";
				
			}
			
			
			if (array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Meta_Analysis_Options_Completed']) != array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Meta_Analysis_Options'])){
				$randomID = md5($currentColumn . '::' .  rand() . rand());
				
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<a href='#Gene_Plot_Columns_Modal' data-toggle='modal' class='nowrap'>(More Attributes)</a>
							</label>";
				echo "</div>";
			}
			
			
			if (array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Meta_Analysis_Options_Completed']) > 0){
				
				$modalID 	= 'Gene_Plot_Columns_Modal';
				$modalTitle = "<h4 class='modal-title'>Additional {$APP_MESSAGE['Gene Attributes']}</h4>";
				
				unset($modalBody);
				
				$modalBody	.= "<div class='row'>";
				foreach($APP_CONFIG['DB_Dictionary']['GeneCombined']['Meta_Analysis_Options_Splitted_2'] as $remainder => $tempValue){
					
					$modalBody	.= "<div class='col-lg-6'>";
					foreach($tempValue as $currentColumn => $currentDetails){
							
						$randomID 	= md5($currentColumn . '::' .  rand());
						
						$class		= "Gene_Plot_Columns_" . md5($currentColumn);
					
						$checked = '';
						
						if (array_size($dataArray) <= 0){
							if ($currentDetails['Default']){
								$checked = 'checked';	
							}
						} elseif (in_array($currentColumn, $dataArray['Gene_Plot_Columns'])){
							$checked = 'checked';
						}
						
						
						$modalBody	.= "<div class='form-check'>";
							$modalBody .= "<label for='{$randomID}' class='form-check-label'>
											<input type='checkbox' id='{$randomID}' class='form-check-input Gene_Plot_Columns {$class}' sibling='{$class}' name='Gene_Plot_Columns[]' value='{$currentColumn}' {$checked}/>
											{$APP_CONFIG['DB_Dictionary']['GeneCombined']['SQL'][$currentColumn]['Title']}
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


	
	if (has_internal_data()){
		
		$has_internal_data = true;
		
		if (!isset($dataArray['data_source'])){
			$resetDataArray = true;
			$dataArray['data_source'][] = 'private';
			$dataArray['data_source'][] = 'public';
		}
		
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
		
			echo "<div style='margin-top:8px;'>";
				echo "<strong>Source of the Sample IDs:</strong>";
			echo "</div>";
			
			echo internal_data_print_form_html($dataArray);
			
			echo internal_data_print_modal($dataArray, $category);
		
		echo "</div>";
		echo "</div>";
		
		if ($resetDataArray){
			unset($dataArray['data_source']);
		}
	}
	
	
	echo "<br/>";
	
	
	
	if (true){
		$modalID 	= 'Meta_Analysis_Modal';
		$modalTitle = "<h4 class='modal-title'>Meta Analysis Options</h4>";
				
		unset($modalBody);
		
		$modalBody .= "<p class='form-text'>The following options determine how the meta anlaysis will be run:</p>";
		
			if (true){
				
				$sql_name 	= 'Missing_Total';
				$name		= 'Missing Data Allowed for p-value';
				
				if (isset($dataArray[$sql_name])){
					$value = $dataArray[$sql_name];
				} else {
					$value = $APP_CONFIG['Meta_Analysis'][$sql_name];
				}
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-6 col-form-label' for='{$sql_name}'><strong>{$name}:</strong></label>";
		
						
					$modalBody .= "<div class='col-3'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			if (true){
				
				$sql_name 	= 'LogFC_Cutoff';
				$name		= "<strong>Log<sub>2</sub> Fold Change Cutoff</strong> <br/><span class='gray'>(1 is 2 fold)</span>";
				
				if (isset($dataArray[$sql_name])){
					$value = $dataArray[$sql_name];
				} else {
					$value = $APP_CONFIG['Meta_Analysis'][$sql_name];
				}
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-6 col-form-label' for='{$sql_name}'>{$name}:</label>";
		
						
					$modalBody .= "<div class='col-3'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
	
			if (true){
				
				$sql_name 	= 'Statistical_Type';
				$name		= "Statistical Type for Changed Gene";
				
				if (isset($dataArray[$sql_name])){
					$value = $dataArray[$sql_name];
				} else {
					$value = $APP_CONFIG['Meta_Analysis'][$sql_name];
				}
				
				unset($values);
				$values['FDR'] 		= 'FDR';
				$values['p-value'] 	= 'p-value';
				
				
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label for='{$name}' class='col-6 col-form-label'><strong>{$name}:</strong></label>";
					
					$modalBody .= "<div class='col-3'>";
						$modalBody .= "<select class='form-control' data-live-search='true' name='{$sql_name}' id='{$sql_name}' title='{$placeHolderText}'>";
						
							foreach($values as $tempKey => $tempValue){
								
								unset($selected);
								
								if ($tempValue === $value){
									$selected = 'selected';	
								}
								
								$modalBody .= "<option value=\"{$tempValue}\" data-tokens=\"{$tempValue}\" {$selected}>{$tempValue}</option>";
								
								
							}
						$modalBody .= "</select>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";
			}
			
			if (true){
				
				$sql_name 	= 'Statistic_Cutoff';
				$name		= "Statistical Cutoff";
				
				if (isset($dataArray[$sql_name])){
					$value = $dataArray[$sql_name];
				} else {
					$value = $APP_CONFIG['Meta_Analysis'][$sql_name];
				}
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-6 col-form-label' for='{$sql_name}'><strong>{$name}:</strong></label>";
		
						
					$modalBody .= "<div class='col-3'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name}' name='{$sql_name}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
		
		echo printModal($modalID, $modalTitle, $modalBody);
			
		echo "<h6><a href='#Meta_Analysis_Modal' data-toggle='modal' class='nowrap'>" . printFontAwesomeIcon('fas fa-cog') . " Meta Analysis Options</a></h6>";
	
	}
	
	echo "<br/>";
	
	if (true){
		
		$modalID 	= 'Data_Filters_Modal';
		$modalTitle = "<h4 class='modal-title'>Data Display Options</h4>";
				
		unset($modalBody);
		
		$modalBody = "";
		
			if (true){
				
				$sql_name 		= 'n_data_points_enable';
				$sectionClass 	= 'n_data_points_section';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['N.data.points']['Print']}<span class='{$sectionClass} startHidden'> &ge;</span>";
	
				unset($checked);
				if (isset($dataArray[$sql_name])){
					if ($dataArray[$sql_name]){
						$checked = 'checked';
					}
				}
				
				$sql_name2 		= 'n_data_points_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['n_data_points'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				

				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			
			if (true){
				
				$sql_name 		= 'RP_Pval_enable';
				$sectionClass 	= 'RP_Pval_section';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_Pval']['Print']}<span class='{$sectionClass} startHidden'> &le;</span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'RP_Pval_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['RP_Pval'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			
			if (true){
				
				$sql_name 		= 'RP_logFC_max_enable';
				$sectionClass 	= 'RP_logFC_max_section';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC_max']['HTML']}<span class='{$sectionClass} startHidden'>: </span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'RP_logFC_max_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['RP_logFC_max'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			
			if (true){
				
				$sql_name 		= 'RP_logFC_min_enable';
				$sectionClass 	= 'RP_logFC_min_section';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC_min']['HTML']}<span class='{$sectionClass} startHidden'>: </span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'RP_logFC_min_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['RP_logFC_min'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			
			if (true){
				
				$sql_name 		= 'Combined_Pval_maxP_enable';
				$sectionClass 	= 'Combined_Pval_maxP';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_Pval_maxP']['Print']}<span class='{$sectionClass} startHidden'> &le;</span>";
	
				unset($checked);
				if (isset($dataArray[$sql_name])){
					if ($dataArray[$sql_name]){
						$checked = 'checked';
					}
				} else {
					//$checked = 'checked';
				}
				
				$sql_name2 		= 'Combined_Pval_maxP_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['Combined_Pval_maxP'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			if (true){
				
				$sql_name 		= 'Combined_Pval_Fisher_enable';
				$sectionClass 	= 'Combined_Pval_Fisher';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_Pval_Fisher']['Print']}<span class='{$sectionClass} startHidden'> &le;</span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'Combined_Pval_Fisher_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['Combined_Pval_Fisher'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
			if (true){
				
				$sql_name 		= 'up_per_enable';
				$sectionClass 	= 'up_per_section';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['Up.Per']['Print']}<span class='{$sectionClass} startHidden'> &ge;</span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'up_per_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['up_per'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
	
			if (true){
				
				$sql_name 		= 'down_per_enable';
				$sectionClass 	= 'down_per_section';
				$name			= "{$APP_CONFIG['APP']['Meta_Analysis_Headers']['Down.Per']['Print']}<span class='{$sectionClass} startHidden'> &ge;</span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'down_per_value';
				$value			= $APP_CONFIG['APP']['Meta_Analysis']['down_per'];
				
				if (isset($dataArray[$sql_name2])){
					$value		= $dataArray[$sql_name2];
				}
				
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-3 startHidden {$sectionClass}'>";			
						$modalBody .= "<input type='text' class='form-control' id='{$sql_name2}' name='{$sql_name2}' value='{$value}'/>";
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
	
			if (true){
				
				$sql_name 		= 'display_enable';
				$sectionClass 	= 'display_section';
				$name			= "Display<span class='{$sectionClass}'>:</span>";
	
				unset($checked);
				if ($dataArray[$sql_name]){
					$checked = 'checked';
				} else {
					$checked = '';
				}
				
				$sql_name2 		= 'display';
				
		
				$modalBody .= "<div class='form-group row' style='margin-top:8px; margin-left:5px;'>";
					$modalBody .= "<label class='col-9 col-form-label' for='{$sql_name}'>
							<input class='form-check-input filterCheckbox' type='checkbox' id='{$sql_name}' name='{$sql_name}' value='1' {$checked} section='{$sectionClass}'/>
							<strong>{$name}</strong>
						</label>";
		
					$modalBody .= "<div class='col-5 startHidden {$sectionClass}' style='margin-top:10px;'>";			
					
					
						foreach($APP_CONFIG['APP']['Meta_Analysis']['Display'] as $currentKey => $currentDisplay){
							
							unset($checked);
							if (isset($dataArray[$sql_name2])){
								if (in_array($currentKey, $dataArray[$sql_name2])){
									$checked = 'checked';
								}
							} else {
								$checked = 'checked';
							}
							
					
							$modalBody .= "<div class='form-check'>
									<label class='form-check-label' for='display'>
										<input class='form-check-input data_source' name='display[]' value='{$currentKey}' {$checked} type='checkbox'>
										{$currentDisplay}&nbsp;&nbsp;
									</label>
								</div>";
	
						}
	
						
					$modalBody .= "</div>";
				$modalBody .= "</div>";	
			}
			
		echo printModal($modalID, $modalTitle, $modalBody);
			
		echo "<h6><a href='#Data_Filters_Modal' data-toggle='modal' class='nowrap'>" . printFontAwesomeIcon('fas fa-list') . " Data Display Options</a></h6>";
	}
		

	
	
	
	if (true){
		echo "<div class='row' style='margin-top:20px;'>";
			echo "<input type='hidden' name='submit' value='1'/>";
			echo "<div class='form-group'>";
				echo "<button class='col-sm-offset-0 btn btn-primary' type='submit'>Submit</button>";
				echo "&nbsp;&nbsp;<a href='{$PAGE['URL']}'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
				echo "&nbsp;<span id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
			echo "</div>";
		echo "</div>";
	}

	

echo "</form>";

echo "<div id='feedbackSection' class='startHidden'></div>";

?>

<script type="text/javascript">

$(document).ready(function(){
	$('#form_application').ajaxForm({ 
        target: '#feedbackSection',
        url: '<?php echo $PAGE['EXE']; ?>',
        type: 'post',
		beforeSubmit: beforeSubmit,
        success: showResponse
    });
	
	$('.Gene_Plot_Columns').change(function(){
		var currentValue = $(this).prop('checked');
		
		var currentClass = $(this).attr('sibling');
		
		$('.' + currentClass).prop('checked', currentValue);
		
		updateSelectedGeneAttributes();
	});


	$(document).on('click', '#geneMissingInfoTrigger', function(){
		$('#geneMissingInfo').toggle();
	});
	
	$(document).on('click', '#comparisonMissingInfoTrigger', function(){
		$('#comparisonMissingInfo').toggle();
	});
	
	$(document).on('click', '#summaryTrigger', function(){
		$('#summarySection').toggle();
	});
	
	
	$('#summaryTrigger').click(function(){
		$('#summarySection').toggle();
	});

	
	$('#form_application').on('change', 'input', function(){
		$('#feedbackSection').empty();
	});
	
	$('#form_application').on('change', 'textarea', function(){
		$('#feedbackSection').empty();
	});
	
	$('#form_application').on('change', '.filterCheckbox', function(){
		filterCheckbox($(this).attr('id'));
	});
	
	
	$('#form_application').on('click', '#advancedOptionsTrigger', function(){
		//$('#advancedOptionsTrigger').hide();
		$('#advancedOptionsSection').toggle();
	});
	
	
	$(document).on('change', '.selectAllTrigger', function(){
		var isChecked = $(this).prop('checked');
		
		if (isChecked){
			$('.recordCheckbox').prop('checked', true);	
		} else {
			$('.recordCheckbox').prop('checked', false);	
		}
	});
	
	$(document).on('click', '.createListTrigger', function(){
		
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = 'Gene';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=4',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					}
				}
			});
		} else {
			
			$('#Record_Required_createListTrigger').show();	
		}
		
		
	});
	
	
	$(document).on('click', '.bubblePlot', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
		
		data['urlKey']	= $('#urlKey').val();
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});

		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=11',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#bubblePlot_Missing_Record').show();	
					}
				}
			});
		} else {
			
			$('#bubblePlot_Missing_Record').show();	
		}
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
	
	
	
	$('#comparisonListModal').on('show.bs.modal', function(){
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Comparison&input_name=comparisonList&input_class=comparisonList&pre_selected_list_id=<?php echo $comparisonListID; ?>',
			success: function(responseText){
				$('#comparisonListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#comparisonListModal').on('change', '.comparisonList', function(){
		var currentListID = $(this).val();
		
		var content = $('#comparison_list_content_' + currentListID).val();
		
		$('#ComparisonIDs').val(content);
	});
	
	
	$('#comparisonListModal').on('click', '.comparisonList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'comparisonList_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#comparison_list_content_' + currentListID).val();
		
		$('#ComparisonIDs').val(content);
	});
	
	
	
	
	
	
	
	
	
	
	updateSelectedGeneAttributes();
	
	
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
	


	filterCheckbox('n_data_points_enable');
	filterCheckbox('up_per_enable');
	filterCheckbox('down_per_enable');
	filterCheckbox('Combined_Pval_Fisher_enable');
	filterCheckbox('Combined_Pval_maxP_enable');
	filterCheckbox('display_enable');




	<?php if ($dataArray['submit'] || $loadFromSession){ ?>
		$('#form_application').submit();
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
	} else {
		$('#data_source_private_section').hide();
	}
}

<?php } ?>


function beforeSubmit() {
	$('.advancedOptionsSection').hide();
	$('#feedbackSection').empty();
	$('#feedbackSection').hide();
	$('#busySection').show();
	return true;
}


function showResponse(responseText, statusText) {
	responseText = $.trim(responseText);

	$('#busySection').hide();
	$('#feedbackSection').html(responseText);
	$('#feedbackSection').show();
	
	$('html,body').animate({
		scrollTop: $('#feedbackSection').offset().top
	});
	
	return true;

}


function updateSelectedGeneAttributes(){

	var checked = new Object();
	var currentValue = '';
	

	$('.Gene_Plot_Columns').each(function() {
		if ($(this).prop('checked')){
			currentValue = $(this).val();
			checked[currentValue] = 1;
		}
	});
	
	var count = Object.keys(checked).length;
	
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#Gene_Plot_Columns_Message').html(message);
	
	
	return true;
		
}

function filterCheckbox(id){
	
	var currentValue 	= $('#' + id).prop('checked');
	var currentSection	= $('#' + id).attr('section');

	
	if (currentValue){
		$('.' + currentSection).show();
	} else {
		$('.' + currentSection).hide();
	}

	
	return true;
}

</script>