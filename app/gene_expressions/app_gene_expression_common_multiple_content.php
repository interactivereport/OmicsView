<?php

$dataArray = getSQLCache($_GET['key']);

if (array_size($dataArray) <= 0){
	if ($_GET['geneList'] > 0){
		$geneListArray = get_list_record_by_list_id_and_category($_GET['geneList'], 'Gene');
		if (array_size($geneListArray) > 0){
			$geneListID = $_GET['geneList'];
		}
	}
}


if (!$useCustomColumn){
	$defaultGeneExpressionOptions = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options'];
	$defaultGeneExpressionOptionsCompleted = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options_Completed'];	
	$defaultGeneExpressionOptionsCompleted_Splitted_2 = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options_Splitted_2'];
}


$currentTable = 'Samples';
echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";

	echo "<h4>Data Options</h4><hr/>";
	
	echo "<div id='inputSection'>";
	
		//Gene Names
		if (true){
		echo "<div class='row'>";
		echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
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
			
			$values = $dataArray['GeneNames'];
			
			if ($geneListID > 0){
				$sql_column_human 		= $APP_CONFIG['APP']['List_Category'][$geneListArray['Category']]['Column_Human'];
				$values 				= implode("\n", $geneListArray['Items'][$sql_column_human]);
			}
			
			echo "<div style='margin-top:10px;'>";
				echo "<textarea class='form-control' rows='8' name='GeneNames' id='GeneNames' placeholder='{$exampleMessage}'>{$values}</textarea>";
			echo "</div>";
			
			echo "<div id='loadFromSessionMessage' class='startHidden alert alert-warning' style='margin-top:10px;'>There are no saved {$APP_MESSAGE['genes']} available.</div>";
			
			
			$modalID 	= 'geneListModal';
			$modalTitle = $APP_MESSAGE['Please select a gene list you like to load:'];
			$modalBody	= '';
			echo printModal($modalID, $modalTitle, $modalBody);
			
			
		echo "</div>";
		echo "</div>";
	}
	
		//Data Source
		if (has_internal_data($category)){
		
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
	
		//Data Source: Jomics
		if ($APP_CONFIG['APP']['Module']['GTEx']){
		
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
		
			unset($checked);
			if ($dataArray['GTEx']){
				$checked = 'checked';	
			}
		
			echo "<div style='margin-top:10px;'>";
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='GTEx' class='form-check-label'>
							<input type='checkbox' id='GTEx' class='form-check-input' name='GTEx' value='1' {$checked}/>
							Search GTEx Samples Only
							</label>";
				echo "</div>";
			echo "</div>";
			
		echo "</div>";
		echo "</div>";
	}
	
		//Search Options: By Data Filter/Samples IDs/Comparison IDs/Project IDs
		if ($APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample']){
			echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
			
			
				echo "<div style='margin-top:12px;'>";
					echo "<strong>Search Options:</strong>";
				echo "</div>";
			
				echo "<div style='margin-left:15px;'>";
				unset($tempArray);
				$tempArray[0] = "Data Filter";
				
				$tempArray[3] = 'Search with Project IDs';
				
				$tempArray[2] = 'Search with Sample IDs';
	
				if ($APP_CONFIG['APP']['Module']['Comparison']){
					$tempArray[1] = 'Search with Comparison IDs';
				}
			
				foreach($tempArray as $tempKey => $tempValue){
					
					unset($checked);
					if (isset($dataArray['searchOption'])){
						if ($dataArray['searchOption'] == $tempKey){
							$checked = 'checked';
							$searchOption = $tempKey;
						}
					} else {
						if ($tempKey == 0){
							$checked = 'checked';	
							$searchOption = 0;
						}
					}
					
					echo "<div class='form-check-inline' id='searchOption_{$tempKey}_Section'>";
						echo "<label class='form-check-label' for='searchOption_{$tempKey}'>";
							echo "<input class='form-check-input searchOption' type='radio' name='searchOption' id='searchOption_{$tempKey}' value='{$tempKey}' {$checked}>";
								echo '&nbsp;' . $tempValue . '&nbsp;';
							echo "</label>";
					echo "</div>";
				}
				unset($tempArray);
				echo "</div>";
	
			echo "</div>";
			echo "</div>";
		}
	
		//Get Data Filter
		if (true){
			unset($class);
			if ($searchOption != 0){
				$class = 'startHidden';	
			}
			echo "<div class='searchOptionRelated searchOptionRelated_0 {$class}'>";
				echo "<div class='row'>";
				echo "<div class='col-lg-12'>";
					echo "<div style='margin-top:20px;'>";
					echo "<p id='missingGeneNameSection' class='startHidden form-text'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a {$APP_MESSAGE['gene']} name and try again.</p>
						</div>";
				echo "</div>";
				echo "</div>";
			echo "</div>";
		}
	
		//dataFilterSection
		if (true){
			unset($class);
			if ($searchOption != 0){
				$class = 'startHidden';	
			}
			echo "<div id='dataFilterSection' class='searchOptionRelated searchOptionRelated_0 dataFilterSection {$class}' style='margin-bottom:20px;'>";
			
				if (($searchOption == 0) && array_size($dataArray) > 0){
					
					$GET_ORG = $_GET;
					$POST_ORG = $_POST;
					
					unset($_GET, $_POST);
					$_GET['action']				= 1;
		
					$_POST['Extra']				= $dataArray;
					
					$_POST['category'] 			= $category;
					$_POST['geneValueTable'] 	= $geneValueTable;
					$_POST['mode'] 				= 2;
					$_POST['geneNames']			= $dataArray['GeneNames'];
					$_POST['data_source']		= $dataArray['data_source'];
					$_POST['GTEx']				= $dataArray['GTEx'];
					$_POST['Modified_DiseaseState_Enable']					= $dataArray['Modified_DiseaseState_Enable'];
					$_POST['Modified_DiseaseState_ComparisonCategory']		= $dataArray['Modified_DiseaseState_ComparisonCategory'];
					$_POST['Modified_DiseaseState_Display_Abbreviation']	= $dataArray['Modified_DiseaseState_Display_Abbreviation'];
					$_POST['data_source_private_project_indexes']			= $dataArray['data_source_private_project_indexes'];
					
					include('app_gene_expression_common_ajax.php');
					
					
					$_GET = $GET_ORG;
					$_POST = $POST_ORG;
				} else {
					
					echo "<div class='row'>";
						echo "<div class='col-4'>";
							echo "<div style='padding-top:80px; padding-left:20px; padding-bottom:80px; min-height:209px; border:dotted 1px #999;'>";
								echo "<button class='btn btn-success ShowDataFilterTrigger btn-sm' type='button'>
										<i class='fa fa-fw fas fa-search' aria-hidden='true'></i> Adjust Data Filter
									</button>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
					
				}
				
			echo "</div>";
			unset($class);
			echo "<div id='dataFilterSectionBusySection' class='startHidden col-lg-offset-1'>" . printFontAwesomeIcon('fas fa-spinner fa-spin fa-lg'). "</div>";
		}
		
		//Comparison List
		if ($APP_CONFIG['APP']['Module']['Comparison']){
			unset($class);
			if ($searchOption != 1){
				$class = 'startHidden';	
			}
			
			echo "<div class='row searchOptionRelated searchOptionRelated_1 {$class}'>";
			echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
				echo "<div style='margin-top:20px;'>";
					echo "<div>";
						echo "<strong>Comparison IDs:</strong> 
							  <a href='#comparisonListModal' data-toggle='modal'>" . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparison List</a>";
					echo "</div>";
					
					/*
					if (has_internal_data($category)){
						echo "<div>";
							echo "Due to the data format consistency, this tool does not support searching by internal comparison data.";
						echo "</div>";
					}
					*/
					
				echo "</div>";
				
				$values 		= implode("\n", $dataArray['ComparisonIDs']);
				$exampleMessage = 'Please enter one or more comparison IDs, seperated by line break';
				
				echo "<div style='margin-top:10px;'>";
					echo "<textarea class='form-control' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='{$exampleMessage}'>{$values}</textarea>";
				echo "</div>";
				
				$modalID 	= 'comparisonListModal';
				$modalTitle = 'Please select a comparison list you like to load:';
				$modalBody	= '';
				echo printModal($modalID, $modalTitle, $modalBody);
				
			echo "</div>";
			echo "</div>";
			
			
			
			
			if (array_size($APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown']) > 0){
				echo "<div class='row searchOptionRelated searchOptionRelated_1 {$class}'>";
				echo "<div class='col-lg-12'>";
					echo "<div style='margin-top:8px;'>";
						echo "<strong>Comparison Attributes:</strong>";
					echo "</div>";
					
					
					
					foreach($APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'] as $currentColumn => $currentTitle){
						
						//if ($APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title'] == '') continue;
						
						$randomID 		= md5($currentColumn . '::' .  rand());
						
						$checked 		= '';
						
						if (array_size($dataArray) > 0){
							if (in_array($currentColumn, $dataArray['Plot_Columns_Comparison'])){
								$checked = 'checked';
							}
						} else {
							$checked = 'checked';
						}
						
						$title = $currentTitle;
						
						echo "<div class='form-check form-check-inline'>";
							echo "<label for='{$randomID}' class='form-check-label'>
									<input type='checkbox' id='{$randomID}' class='form-check-input' name='Plot_Columns_Comparison[]' value='{$currentColumn}' {$checked}/>
									{$title}
									</label>";
						echo "</div>";
						
					}
				
				echo "</div>";
			
			
				echo "</div>";
			}
		
		}
		
		//Sample List
		if (true){
			unset($class);
			if ($searchOption != 2){
				$class = 'startHidden';	
			}
			
			echo "<div class='row searchOptionRelated searchOptionRelated_2 {$class}'>";
			echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
				echo "<div style='margin-top:20px;'>";
					echo "<strong>Sample IDs:</strong> 
							<a href='#sampleListModal' data-toggle='modal'>" . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Sample List</a>";
				echo "</div>";
				
				$values 		= implode("\n", $dataArray['SampleIDs']);
				$exampleMessage = 'Please enter one or more sample IDs, seperated by line break';
				
				echo "<div style='margin-top:10px;'>";
					echo "<textarea class='form-control' rows='8' name='SampleIDs' id='SampleIDs' placeholder='{$exampleMessage}'>{$values}</textarea>";
				echo "</div>";
				
				$modalID 	= 'sampleListModal';
				$modalTitle = 'Please select a sample list you like to load:';
				$modalBody	= '';
				echo printModal($modalID, $modalTitle, $modalBody);
				
			echo "</div>";
			echo "</div>";
		}
		
		//Project List
		if (true){
			unset($class);
			if ($searchOption != 3){
				$class = 'startHidden';	
			}
			
			echo "<div class='row searchOptionRelated searchOptionRelated_3 {$class}'>";
			echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
				echo "<div style='margin-top:20px;'>";
					echo "<strong>Project IDs:</strong> 
							<a href='#projectListModal' data-toggle='modal'>" . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Project List</a>";
				echo "</div>";
				
				$values 		= implode("\n", $dataArray['ProjectIDs']);
				$exampleMessage = 'Please enter one or more project IDs, seperated by line break';
				
				echo "<div style='margin-top:10px;'>";
					echo "<textarea class='form-control' rows='8' name='ProjectIDs' id='ProjectIDs' placeholder='{$exampleMessage}'>{$values}</textarea>";
				echo "</div>";
				
				$modalID 	= 'projectListModal';
				$modalTitle = 'Please select a project list you like to load:';
				$modalBody	= '';
				echo printModal($modalID, $modalTitle, $modalBody);
				
			echo "</div>";
			echo "</div>";
		}

	echo "</div>";	//inputSection
	
	
	
	echo "<br/>";
	
	
	echo "<h4>Plots Options</h4><hr/>";
	
	echo "<div id='outputSection'>";
	
		//Sample Attributes:
		if (array_size($defaultGeneExpressionOptions) > 0){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<div style='margin-top:12px;'>";
				echo "<strong>Sample Attributes: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>";
				echo "<input class='form-check-input Plot_Columns_Select_All_Trigger' type='checkbox'/>Select All";
				echo "&nbsp;";
				echo "<span id='Plot_Columns_Message' style='font-weight:normal;'></span>";
			echo "</div>";
			
			echo "<div style='margin-left:15px;'>";
			
			foreach($defaultGeneExpressionOptions as $currentColumn => $currentDetails){
				
				$randomID 		= md5($currentColumn . '::' .  rand());
				
				$classOuter		= '';
				$class			= "Plot_Columns_" . md5($currentColumn);
				$defaultClass	= '';
				$GTExClass		= '';
				$checked 		= '';
				
				if (!isset($dataArray['Plot_Columns'])){
					if ($currentDetails['Default']){
						$checked = 'checked';	
					}
				} else {
					if (in_array($currentColumn, $dataArray['Plot_Columns'])){
						$checked = 'checked';	
					}
				}
				
				
				if ($currentDetails['GTEx']){
					$classOuter = 'startHidden GTEx_Member';
					
					if (!isset($dataArray['Plot_Columns'])){
						if ($currentDetails['Default']){
							$classOuter = 'GTEx_Member';
							$checked 	= 'checked';	
						}
					} else {
						if (in_array($currentColumn, $dataArray['Plot_Columns'])){
							$classOuter = 'GTEx_Member';
							$checked 	= 'checked';	
						}
					}
				}
				
				if ($currentDetails['GTEx-Checked']){
					$GTExClass	= 'GTEx_Checkbox';	
				}
				
				
				if ($currentDetails['Default']){
					$defaultClass	= 'Default_Checkbox';		
				}
				
				$currentTitle = $currentDetails['Title'];
				if ($currentTitle == ''){
					$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
				}
				
				echo "<div class='form-check form-check-inline {$classOuter}'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<input type='checkbox' id='{$randomID}' class='form-check-input Plot_Columns {$class} {$defaultClass} {$GTExClass}' sibling='{$class}' name='Plot_Columns[]' value='{$currentColumn}' {$checked}/>
							{$currentTitle}
							</label>";
				echo "</div>";
				
			}
			
			
			if (array_size($defaultGeneExpressionOptionsCompleted) != array_size($defaultGeneExpressionOptions)){
				$randomID = md5($currentColumn . '::' .  rand() . rand());
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}'>
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
						
						$modalBody .= "<div class='form-check'>";
							$modalBody .= "<label for='{$randomID}' class='form-check-label'>
											<input type='checkbox' id='{$randomID}' class='form-check-input Plot_Columns {$class}' sibling='{$class}' name='Plot_Columns[]' value='{$currentColumn}' {$checked}/>
											{$currentTitle}
											</label>";
						$modalBody .= "</div>";
						
					}
					$modalBody .= "</div>";
				}
				$modalBody	.= "</div>";
				
				
				echo printModal($modalID, $modalTitle, $modalBody);
				
				
			}
		
		
		echo "</div>";
	
	
		echo "</div>";
	}
	
		//Update Disease State with Comparison Category:
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


	echo "</div>";
	
	
	//Modal
	if (true){
		$modalID 	= 'advancedOptionSection';
		$modalTitle = "<h4 class='modal-title'>Advanced Options</h4>";

		$modalBody = "<div>";

			$modalBody .= "<div class='row'>";
			
				$modalBody .= "<div class='col-12'>";
				
					//Log2 Transform
					if (true){
			
						$modalBody .= "<div style='margin-top:12px;'>";
							$modalBody .= "<strong>Data Transformation:</strong>";
						$modalBody .= "</div>";
			
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
								$class = '';	
							}
							
							$modalBody .= "<div class='form-check col-12' style='margin-left:15px;'>";
								$modalBody .= "<label for='transform' class='form-check-label'>
													<input class='form-check-input inputForm' type='checkbox' id='transform' name='transform' value='1' {$checked}/>
													Enable Log<sub>2</sub> Transform
												</label>";
								$modalBody .= "&nbsp;";
							$modalBody .= "</div>";
						}
			
						if (true){
							$modalBody .= "<div style='margin-left:15px;'>";
							
								if (isset($dataArray['transform_value']) && ($dataArray['transform_value'] >= 0)){
									$value = floatval($dataArray['transform_value']);
								} else {
									$value = $APP_CONFIG['canvasxpress']['Log_Add_Value'];	
								}
								
								$value = abs(floatval($value));
								$modalBody .= "<div class='form-group'>";
									$modalBody .= "<label for='transform_value' class='col-form-label'>Value to Be Added: &nbsp;</label>";
									$modalBody .= "<input class='col-3 inputForm' type='text' id='transform_value' name='transform_value' value='{$value}'/>";
								$modalBody .= "</div>";
							
							$modalBody .= "</div>";
						}
					}
	
					//Plot Orientation
					if (true){
					$modalBody .= "<div class='row'>";
					$modalBody .= "<div class='col-lg-12'>";
					
					
						$modalBody .= "<div style='margin-top:12px;'>";
							$modalBody .= "<strong>Plot Orientation:</strong>";
						$modalBody .= "</div>";
					
						unset($tempArray);
						$tempArray['horizontal'] 	= 'Horizontal';
						$tempArray['vertical'] 		= 'Vertical';
						
						$defaultArray['graphOrientation'] = $APP_CONFIG['canvasxpress']['graphOrientation'];
						
						
						$modalBody .= "<div style='margin-left:15px;'>";
						foreach($tempArray as $tempKey => $tempValue){
							
							unset($checked);
							if (isset($dataArray['graphOrientation'])){
								if ($dataArray['graphOrientation'] == $tempKey){
									$checked = 'checked';
								}
							} elseif ($defaultArray['graphOrientation'] == $tempKey){
								$checked = 'checked';
							} else {
								if ($tempKey == 'horizontal'){
									$checked = 'checked';	
								}
							}
							
							$modalBody .= "<div class='form-check-inline' id='graphOrientation_{$tempKey}_Section'>";
								$modalBody .= "<label class='form-check-label' for='graphOrientation_{$tempKey}'>";
									$modalBody .= "<input class='form-check-input' type='radio' name='graphOrientation' id='graphOrientation_{$tempKey}' value='{$tempKey}' {$checked}>";
										$modalBody .= '&nbsp;' . $tempValue . '&nbsp;';
									$modalBody .= "</label>";
							$modalBody .= "</div>";
						}
						unset($tempArray);
						$modalBody .= "</div>";
			
					$modalBody .= "</div>";
					$modalBody .= "</div>";
				}
				
					$modalBody .= "<br/>";
		
					$modalBody .= "<p class='form-text'>(Please enter 0 or leave blank if you want the auto settings.)</p>";
					
					//Chart Height
					if (true){
						$modalBody .= "<div style='margin-top:12px;'>";
						
							$modalBody .= "<div class='form-group row'>";
			
								$modalBody .= "<div class='col-3'>";
									$modalBody .= "<label for='plot_height' class='xcol-form-label'><strong>Chart Height: &nbsp;</strong></label>";
								$modalBody .= "</div>";
				
				
								$modalBody .= "<div class='col-3'>";
								
									$value = '';
									if (isset($dataArray['plot_height']) && ($dataArray['plot_height'] >= 0)){
										$value = intval($dataArray['plot_height']);
									}
									$value = abs(intval($value));
								
									$modalBody .= "<input class='inputForm form-control' type='text' id='plot_height' name='plot_height' value='{$value}'/>";
								$modalBody .= "</div>";
								
							$modalBody .= "</div>";
							
						
						$modalBody .= "</div>";
					}

					//Chart Width
					if (true){
						$modalBody .= "<div style='margin-top:12px;'>";
						
							$modalBody .= "<div class='form-group row'>";
				
								$modalBody .= "<div class='col-3'>";
									$modalBody .= "<label for='plot_width' class='xcol-form-label'><strong>Chart Width: &nbsp;</strong></label>";
								$modalBody .= "</div>";
				
				
								$modalBody .= "<div class='col-3'>";
								
									$value = '';
									if (isset($dataArray['plot_width']) && ($dataArray['plot_width'] >= 0)){
										$value = intval($dataArray['plot_width']);
									}
									$value = abs(intval($value));
								
									$modalBody .= "<input class='inputForm form-control' type='text' id='plot_width' name='plot_width' value='{$value}'/>";
								$modalBody .= "</div>";
								
							$modalBody .= "</div>";
							
						
						$modalBody .= "</div>";
					}
					
				$modalBody .= "</div>";
				
		
			$modalBody .= "</div>";
			
			
		$modalBody .= "</div>";

		
		echo printModal($modalID, $modalTitle, $modalBody);
		
	}
	
	
	//Submit Button
	if (true){
		unset($class);
		if ($searchOption == 0){
			//$class = 'startHidden';	
		}
		echo "<div id='submitButtonGroup' class='form-group {$class} XsearchOptionRelated XsearchOptionRelated_1 XsearchOptionRelated_2 XsearchOptionRelated_3'>";
			echo "<br/>";
			echo "<button class='xcol-sm-offset-1 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot</button>";
			echo "&nbsp; &nbsp;<a data-toggle='modal' href='#advancedOptionSection'>" . printFontAwesomeIcon('fas fa-cog') . " Advanced Options</a>";
			echo "&nbsp; &nbsp;<a href='{$PAGE['URL']}'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
			echo "&nbsp; &nbsp;<span class='busySection startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
			echo "<input type='hidden' id='data_filter_has_result' value='0'/>";
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
}

#outputSection{
	margin-left:20px;
}
</style>

<script type="text/javascript">

$(document).ready(function(){
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
	
	
	$('.searchOption').change(function(){
		var currentValue = parseInt($(this).val());
		
		$('.searchOptionRelated').hide();
		
		$('.searchOptionRelated_' + currentValue).show();
		
		var data_filter_has_result = parseInt($('#data_filter_has_result').val());
		
		if (data_filter_has_result == 1){
			$('#submitButtonGroup').show();	
		}
	});
	
	
	
	
	$(document).on('click', '#geneMissingInfoTrigger', function(){
		$('#geneMissingInfo').toggle();
	});
	
	
	$(document).on('click', '#geneMissingInfoAjaxTrigger', function(){
		$('#geneMissingInfoAjax').toggle();
	});
	
	
	
	
	$('#dataFilterSection').on('click', '.advancedOptionsTrigger', function(){
		$('.advancedOptionsSection').toggle();
		
		$('.advancedOptionsTriggerIcon').hide();
		
		if ($('.advancedOptionsSection').is(":visible")){
			$('.advancedOptionsTriggerOn').show();
		} else {
			$('.advancedOptionsTriggerOff').show();
		}
		
	});
	
	$('#dataFilterSection').on('change', '.advancedOptionsColumn', function(){
		
		var currentColumn 		= $(this).attr('currentcolumn');
		var checkBoxClass 		= currentColumn + '_checkbox';
		var message				= '';
		var selectedCount 		= 0;
		var selectedCheckbox 	= new Array();
		var responseID			= $(this).attr('responseid');
		
		$('.' + checkBoxClass).each(function() {
			if ($(this).prop('checked')){
				selectedCount++;
				
				selectedCheckbox.push($(this).attr('display'));
			}
		});
		
		selectedCount = parseInt(selectedCount);
		
		if (selectedCount == 0){
			message = 'No filter has been set.';
		} else {
			if (selectedCount <= 5){
				message = "Selected(" + selectedCount + "): " + '<mark>' + selectedCheckbox.join('</mark>, <mark>') + '</mark>';
			} else {
				message = "Selected: " + selectedCount;
			}
		}
		
		$('#' + responseID).html(message);
	});
	
	$('#dataFilterSection').on('change', '.advancedOptionsColumnSelectAll', function(){
		var childrenClass = $(this).attr('children');
		
		$('.' + childrenClass).prop('checked', $(this).prop('checked'));
		
		$('.advancedOptionsColumn').change();
	});
	
	
	
	$('.ShowDataFilterTrigger').click(function(){
		loadDataFilterSection();
	});
	
	
	$('#form_application').on('change', 'input', function(){
		$('#feedbackSection').empty();
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
	
	
	<?php if (true){ ?>
	$('#sampleListModal').on('show.bs.modal', function(){
		
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Sample&input_name=list&input_class=list&pre_selected_list_id=<?php echo $sampleListID; ?>',
			success: function(responseText){
				$('#sampleListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#sampleListModal').on('change', '.list', function(){
		var currentListID = $(this).val();
		
		var content = $('#sample_list_content_' + currentListID).val();
		
		$('#SampleIDs').val(content);
	});
	
	
	$('#sampleListModal').on('click', '.list_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'list_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#sample_list_content_' + currentListID).val();
		
		$('#SampleIDs').val(content);
	});
	
	
	$(document).on('click', '#sampleMissingInfoTrigger', function(){
		$('#sampleMissingInfo').toggle();
	});
	<?php } ?>
	
	<?php if (true){ ?>
	$('#projectListModal').on('show.bs.modal', function(){
		
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Project&input_name=list&input_class=list&pre_selected_list_id=<?php echo $projectListID; ?>',
			success: function(responseText){
				$('#projectListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#projectListModal').on('change', '.list', function(){
		var currentListID = $(this).val();
		
		var content = $('#project_list_content_' + currentListID).val();
		
		$('#ProjectIDs').val(content);
	});
	
	
	$('#projectListModal').on('click', '.list_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'list_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#project_list_content_' + currentListID).val();
		
		$('#ProjectIDs').val(content);
	});
	
	
	$(document).on('click', '#projectMissingInfoTrigger', function(){
		$('#projectMissingInfo').toggle();
	});
	<?php } ?>

	
	<?php if (true){ ?>
	$('#geneListModal').on('show.bs.modal', function(){
		
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Gene&input_name=list&input_class=list&pre_selected_list_id=<?php echo $geneListID; ?>',
			success: function(responseText){
				$('#geneListModal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#geneListModal').on('change', '.list', function(){
		var currentListID = $(this).val();
		
		var content = $('#gene_list_content_' + currentListID).val();
		
		$('#GeneNames').val(content);
	});
	
	
	$('#geneListModal').on('click', '.list_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'list_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#gene_list_content_' + currentListID).val();
		
		$('#GeneNames').val(content);
	});
	<?php } ?>
	

	<?php if ($APP_CONFIG['APP']['Module']['GTEx']){ ?>
	$('#GTEx').change(function(){
		var currentValue = $(this).prop('checked');

		$('.Plot_Columns').prop('checked', false);
		
		if (currentValue){
			$('.GTEx_Member').css('display', 'inline-block');
			$('.GTEx_Checkbox').prop('checked', true);
		} else {
			$('.GTEx_Member').hide();
			$('.Default_Checkbox').prop('checked', true);
		}
		updateSelectedSampleAttributes();
	});
	<?php } ?>
	
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
	
	
	<?php if (array_size($dataArray) > 0){ ?>
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
		
		if ($('#searchOption_1').prop('checked')){
			$('#searchOption_0').prop('checked', true);
			$('#searchOption_0').change();
		}
		
	} else {
		
		$('#data_source_private_section').hide();
	}
}

<?php } ?>


function beforeSubmit() {
	$('.advancedOptionsSection').hide();
	$('#feedbackSection').empty();
	$('#feedbackSection').hide();
	$('.busySection').show();
	
	$('.advancedOptionsTriggerIcon').hide();
	if ($('.advancedOptionsSection').is(":visible")){
		$('.advancedOptionsTriggerOn').show();
	} else {
		$('.advancedOptionsTriggerOff').show();
	}
	
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


function loadDataFilterSection(){
	
	var geneNames 	= $('#GeneNames').val();
	geneNames 		= $.trim(geneNames);
	
	if (geneNames == ''){
		$('#missingGeneNameSection').show();
		return;
	} else {
		$('#missingGeneNameSection').hide();	
	}
	
	<?php if ($APP_CONFIG['APP']['Module']['GTEx']){ ?>
	if ($('#GTEx').prop('checked')){
		var GTEx	= 1;
	} else {
		var GTEx	= 0;
	}
	<?php } else { ?>
		var GTEx	= 0;
	<?php } ?>

	
	$('#feedbackSection').empty();
	$('#dataFilterSection').empty();
	$('.dataFilterSection').hide();
	$('#submitButtonGroup').hide();
	$('#data_filter_has_result').val(0);
	
	
	var data = new Object();
	
	data['category'] 		= '<?php echo $category; ?>';
	data['geneValueTable'] 	= '<?php echo $geneValueTable; ?>';
	data['mode'] 			= 2;
	data['geneNames'] 		= geneNames;
	data['GTEx'] 			= GTEx;
	
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
	
	
	<?php if ($APP_CONFIG['APP']['Module']['Modified_DiseaseState']){ ?>
	
	if ($('#Modified_DiseaseState_Enable').prop('checked')){
		data['Modified_DiseaseState_Enable'] = 1;
		
		
		data['Modified_DiseaseState_ComparisonCategory[]']	= [];
		$('.Modified_DiseaseState_ComparisonCategory:checked').each(function() {
			data['Modified_DiseaseState_ComparisonCategory[]'].push($(this).val());
		});
		
		if ($('#Modified_DiseaseState_Display_Abbreviation').prop('checked')){
			data['Modified_DiseaseState_Display_Abbreviation'] = 1;	
		}
	}
	<?php } ?>
	
	if (geneNames != ''){
		
		$('#missingGeneNameSection').hide();
		$('#dataFilterSectionBusySection').show();
		
		$.ajax({
			type: 'POST',
			url: 'app_gene_expression_common_ajax.php',
			data: data,
			success: function(responseText){
				responseText = $.trim(responseText);
				
				if (responseText != ''){
					$('#dataFilterSectionBusySection').hide();
					
					$('#dataFilterSection').html(responseText);
					$('.dataFilterSection').show();
					$('#data_filter_has_result').val(1);
					
				}
			}
		});
	} else {
		$('#missingGeneNameSection').show();
	}
}

function updateSelectedSampleAttributes(){

	var checked = new Object();
	var currentValue = '';
	

	$('.Plot_Columns').each(function() {
		if ($(this).prop('checked')){
			currentValue = $(this).val();
			checked[currentValue] = 1;
			
			siblingClass = $(this).attr('sibling');
			$('.' + siblingClass).prop('checked', currentValue);
		}
	});
	
	var count = Object.keys(checked).length;
	
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#Plot_Columns_Message').html(message);
	
	
	
	
	return true;
		
}

</script>