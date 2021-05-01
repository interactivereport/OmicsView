<?php

include_once('config_init.php');

$currentTable = 'Samples';

//Call from gene expressions tool
//Get data filter
if (true){
	
	echo '&nbsp;';
	
	
	$geneValueTable = trim($_POST['geneValueTable']); //GeneFPKM or GeneLevelExpression
	$mode			= trim($_POST['mode']);  //1: Single, 2: Multiple
	$category		= trim($_POST['category']); //Microarray or RNA-Seq
	$GTEx			= intval($_POST['GTEx']);  //1: Enable, 0: Disable
	
	cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

	
	if ($APP_CONFIG['APP']['Module']['Modified_DiseaseState']){
		if ($_POST['Modified_DiseaseState_Enable']){
			if (array_size($_POST['Modified_DiseaseState_ComparisonCategory']) <= 0){
				$_POST['Modified_DiseaseState_ComparisonCategory'] = $APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory'];
			}
		}
	} else {
		unset($_POST['Modified_DiseaseState_Enable'], $_POST['Modified_DiseaseState_ComparisonCategory']);
	}

	
	if ($mode == 1){
		$modalMessage 	= 'The number represents the occurrence.';
		$geneName		= trim($_POST['geneNames']);
	} elseif ($mode == 2){
		$modalMessage 	= $APP_MESSAGE['The two numbers next to the each category represent the corresponding occurrence and the number of gene, respectively.'];
		$geneName		= explode("\n", $_POST['geneNames']);
	} else {
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please reload the page and try again.";
		echo getAlerts($message);
		exit();
	}
	
	$getGeneNameExistenceInfo = getGeneNameExistenceInfo($geneName);
	
	if ($mode == 1){
		if ($getGeneNameExistenceInfo == false){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The {$APP_MESSAGE['gene']} (<strong>{$geneName}</strong>) does not exist in the database.";
			echo getAlerts($message);
			exit();
		}
	} elseif ($mode == 2){
		
		if ($getGeneNameExistenceInfo == false){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['The genes you entered do not exist in the database.']}";
			echo getAlerts($message, 'danger');
			exit();	
		}
	}
	

	if ($GTEx){
		$preSelectKey	= 'GTEx';
	} else {
		$preSelectKey	= 'Default';
	}
	
	$selectMode = $APP_CONFIG['DB_Dictionary']['Samples']['Preselect'][$category][$preSelectKey]['Mode'];
	
	if (true){
		//Regular
		if ($selectMode == 'Highest_Occurence'){
			$hasPreCheck 			= true;
			$preCheckMessage		= $APP_CONFIG['DB_Dictionary']['Samples']['Preselect'][$category][$preSelectKey]['Message_General'];
			$getColumnInfoByGene 	= getColumnInfoByGene($geneName, 
													$geneValueTable, 
													array_keys($APP_CONFIG['DB_Dictionary']['Samples']['Dropdown']),
													1, 
													$APP_CONFIG['DB_Dictionary']['Samples']['Preselect'][$category][$preSelectKey]['Column'],
													$_POST['data_source'],
													$_POST['data_source_private_project_indexes'],
													$_POST
													);
		} elseif ($selectMode == 'Multiple'){
			$hasPreCheck 			= true;
			$preCheckMessage		= $APP_CONFIG['DB_Dictionary']['Samples']['Preselect'][$category][$preSelectKey]['Message_General'];
			$getColumnInfoByGene 	= getColumnInfoByGene($geneName, 
													$geneValueTable, 
													array_keys($APP_CONFIG['DB_Dictionary']['Samples']['Dropdown']),
													2, 
													$APP_CONFIG['DB_Dictionary']['Samples']['Preselect'][$category][$preSelectKey]['Column'],
													$_POST['data_source'],
													$_POST['data_source_private_project_indexes'],
													$_POST
													);
		} else {
	
			
			$hasPreCheck 			= false;
			$preCheckMessage		= '';
			$getColumnInfoByGene 	= getColumnInfoByGene($geneName, 
													$geneValueTable, 
													array_keys($APP_CONFIG['DB_Dictionary']['Samples']['Dropdown']),
													0, 
													'',
													$_POST['data_source'],
													$_POST['data_source_private_project_indexes'],
													$_POST
													);
		}
	} else {
		$flexibleColumnSummary = getInternalDataJob_NonEmptyColumns($_POST['data_source_private_project_indexes'], 'sample');
		
		
		if ($selectMode == ''){
			
			$otherOptions = $_POST;
			$otherOptions['Column_Summary'] = $flexibleColumnSummary;

			$hasPreCheck 			= false;
			$preCheckMessage		= '';
			$getColumnInfoByGene 	= getColumnInfoByGene($geneName, 
													$geneValueTable, 
													$flexibleColumnSummary['Summary']['ByOccurence-Top'],
													0, 
													'',
													$_POST['data_source'],
													$_POST['data_source_private_project_indexes'],
													$otherOptions
													);
		}

	}

	
	if ($getColumnInfoByGene == FALSE){
		$message = "<strong>Warning!</strong> {$APP_MESSAGE['There are no data available. Please try to search with a different gene.']}";
		echo getAlerts($message, 'warning');
		exit();
	} elseif ($getGeneNameExistenceInfo['hasMissing']){
			
		$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['Some of the genes you entered do not exist in the database.']} Please click <a href='javascript:void(0);' id='geneMissingInfoAjaxTrigger' class='forceLink'>here</a> for details.</p>";
		echo $message;
		
		echo "<div id='geneMissingInfoAjax' class='startHidden'>";
		
			unset($tableContent);
			$tableContent['Header'][1]		= 'Category';
			$tableContent['Header'][2] 		= $APP_MESSAGE['# of Genes'];
			
			
			$tableContent['Body'][1]['Value'][1]	= $APP_MESSAGE['# of Genes Entered'];
			$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Input_Count']}</a>";
			
			$tableContent['Body'][2]['Value'][1]	= $APP_MESSAGE['# of Genes Available'];
			$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Output_Count']}</a>";
			
			$tableContent['Body'][3]['Value'][1]	= $APP_MESSAGE['# of Genes Not Available'];
			$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#geneNameExistenceInfoSection'>{$getGeneNameExistenceInfo['Missing_Count']}</a>";
			
			echo printTableHTML($tableContent, 1, 1, 0);
			
			
			$modalID 	= 'geneNameExistenceInfoSection';
			$modalTitle = "<h4 class='modal-title'>Summary</h4>";
			$modalBody  = "<div class='row'>";
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Entered ({$getGeneNameExistenceInfo['Input_Count']}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Input']) . "</textarea>";
				$modalBody  .= "</div>";
								
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Available ({$getGeneNameExistenceInfo['Output_Count']}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Output']) . "</textarea>";
				$modalBody  .= "</div>";
								
				$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
					$modalBody  .= "<div class='text-nowrap'><strong>{$APP_MESSAGE['Genes']} Not Available ({$getGeneNameExistenceInfo['Missing_Count']}):</strong></div>";
					$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getGeneNameExistenceInfo['Missing']) . "</textarea>";
				$modalBody  .= "</div>";
			$modalBody  .= "</div>";
			
			echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height modal-body-full-width');
		echo "</div>";
		
	}
	
	
	if ($preCheckMessage != ''){
		$message = $preCheckMessage;		
	} else {
		$message = 'By default, no filters will be used for searching the data. Please click the <strong>Set Filter</strong> to set the search filter.';	
	}
	
	
	$imgOn 	= printFontAwesomeIcon('fas fa-toggle-on  advancedOptionsTriggerIcon advancedOptionsTriggerOn');
	$imgOff = printFontAwesomeIcon('fas fa-toggle-off advancedOptionsTriggerIcon advancedOptionsTriggerOff startHidden');
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h5>
					<a href='javascript:void(0);' class='advancedOptionsTrigger'>{$imgOn} {$imgOff} Data Filter</a>
					&nbsp; &nbsp;
					<a href='javascript:void(0);' class='refreshDataFilterTrigger'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Refresh Filter</a>
					
					
				</h5>";
			echo "<p class='form-text advancedOptionsSection'>{$message}</p>";
			
			
			if ($getColumnInfoByGene['DataCount_Plot'] > 0){
				if ($getColumnInfoByGene['DataCount_Plot'] >= $APP_CONFIG['canvasxpress']['Data_Limit']){
					$formattedNumber = number_format($getColumnInfoByGene['DataCount_Plot']);
					$errorMessage =  "<p><strong>Warning!</strong> There are {$formattedNumber} data points in the search result. 
								Plotting too many data points may cause performance problem to your browser. 
								Please refine your search conditions to reduce the number of data points in the plot.</p>";
					echo getAlerts($errorMessage, 'warning advancedOptionsSection');
				}
			}
		echo "</div>";
	echo "</div>";
	
	
	echo "<div class='row'>";
	
	
	
	foreach ($getColumnInfoByGene['Value'] as $currentColumn => $tempValue1){

		$inputName 		= $currentColumn;
		
		if (true){
			$inputTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
		}
		
		$modalID		= "{$currentColumn}_Modal";
		$responseID		= "{$currentColumn}_response";
		
		$popUpMessage	= $APP_CONFIG['DB_Dictionary'][$currentTable]['Preselect'][$category][$preSelectKey]['Message'][$currentColumn];
		
		unset($preSelected);
		if ($selectMode == 'Highest_Occurence'){
			if ($APP_CONFIG['DB_Dictionary'][$currentTable]['Preselect'][$category][$preSelectKey]['Column'] == $currentColumn){
				$preSelected 		= 1;
				$preSelectedCount 	= 1;
				$preSelectedValues	= "<mark>" . implode('</mark>, <mark>', $getColumnInfoByGene['PreSelected'][$currentColumn]) . "</mark>";
				
				if ($mode == 1){
					$preSelectedMessage = "Selected({$preSelectedCount}): {$preSelectedValues}";
				} elseif ($mode == 2){
					$preSelectedMessage = "Selected({$preSelectedCount}): {$preSelectedValues}";
				}
			}
		} elseif ($selectMode == 'Multiple'){
			if (isset($APP_CONFIG['DB_Dictionary'][$currentTable]['Preselect'][$category][$preSelectKey]['Column'][$currentColumn])){
				$preSelected 		= 1;
				
				$preSelectedCount 	= array_size($APP_CONFIG['DB_Dictionary'][$currentTable]['Preselect'][$category][$preSelectKey]['Column'][$currentColumn]);
				$preSelectedValues	= "<mark>" . implode('</mark>, <mark>', $getColumnInfoByGene['PreSelected'][$currentColumn]) . "</mark>";
				
				if ($mode == 1){
					$preSelectedMessage = "Selected({$preSelectedCount}): {$preSelectedValues}";
				} elseif ($mode == 2){
					$preSelectedMessage = "Selected({$preSelectedCount}): {$preSelectedValues}";
				}
			}
		}
		
		
		
		
		if (true){
			
			echo "<div class='advancedOptionsSection col-lg-3'>";
			echo "<div class='form-group'>";
				echo "<label for='{$inputName}' class='col-form-label '><strong>{$inputTitle}:</strong></label>";
	
				echo "<div class='col-12'>";
					echo "<button type='button' class='col-3 form-control btn btn-secondary btn-sm' data-toggle='modal' data-target='#{$modalID}'>Set Filter</button>";
					
					unset($message);
					
					if (!$preSelected){
						$message = 'No filter has been set.';
					} else {
						$message = $preSelectedMessage;
					}
					
					
					

					if (isset($_POST['Extra'])){
						$preSelectedCount = array_size($_POST['Extra'][$currentColumn]);
						if ($preSelectedCount > 0){
							
							if ($preSelectedCount <= 5){
								
								$tempArrayX = $_POST['Extra'][$currentColumn];
	
								$tempArrayX = array_map('ucwords', $tempArrayX);
								
								foreach($tempArrayX as $tempKeyX => $tempValueX){
									if ($tempValueX == ''){
										 $tempArrayX[$tempKeyX] = $APP_CONFIG['Blank_Value'];
									}
								}
	
								$message = "Selected({$preSelectedCount}): <mark>" . implode("</mark>, <mark>", $tempArrayX) . "</mark>";
							} else {
								$message = "Selected: {$preSelectedCount}";
							}
						}
					}
					echo "<p class='form-text' id='{$responseID}'>{$message}</p>";
					
					
					if (true){
						$modalID = $modalID;
						
						$modalTitle = "<h4 class='modal-title'>{$inputTitle}</h4>";
						
						
						$modalBody = '';
						
						if ($popUpMessage != ''){
							$modalBody .= "<div><p class='form-text'>{$popUpMessage}</p></div>";
						}
						
						$modalBody .= "<div class='small' style='padding:20px;'>";
						
							
							
							$modalBody .= "<div class='table-responsive'>";
								$modalBody .= "<table class='tableToSort sortable-theme-slick table table-striped table-bordered table-condensed'>";
									$modalBody .= "<thead>";
										$modalBody .= "<tr>";
											//Select all checkbox
											$modalBody .= "<th class='text-center' data-sortable='false'>";
												$modalBody .= "<input type='checkbox' id='{$currentColumn}_checkboxSelectAll' class='advancedOptionsColumnSelectAll' children='{$currentColumn}_checkbox'/>";
											$modalBody .= "</th>";
											
											
											$modalBody .= "<th data-sort='string-ins' class='tableHeaderToSort'>";
												$modalBody .= "Category";
											$modalBody .= "</th>";
											
											
											$modalBody .= "<th data-sort='int' class='tableHeaderToSort'>";
												$modalBody .= "# of Sample";
											$modalBody .= "</th>";
											
											if ($mode == 2){
												$modalBody .= "<th data-sort='int' class='tableHeaderToSort'>";
													$modalBody .= $APP_MESSAGE['# of Gene'];
												$modalBody .= "</th>";
											}
										$modalBody .= "</tr>";
									$modalBody .= "</thead>";
									

									
									
									$modalBody .= "<tbody>";
										foreach($getColumnInfoByGene['Value'][$currentColumn] as $currentName => $tempValue2){
										
											$currentValue			= $tempValue2['Raw'];
											
											
											unset($preCheck);
											if ($mode == 2){
												$currentGeneCount	= array_size($tempValue2['By-Gene']);
												$currentCount		= intval($tempValue2['GeneValueCount']);
												
												
												if ($preSelected){
													if (isset($getColumnInfoByGene['PreSelected'][$currentColumn][$currentName])){
														$preCheck = 'checked';
													}
												}
												
											} else {
												$currentGeneCount	= 1;
												$currentCount		= intval($tempValue2['Total']);
												
												
												if ($preSelected){
													if (isset($getColumnInfoByGene['PreSelected'][$currentColumn][$currentName])){
														$preCheck = 'checked';
													}
												}
											}
											
											
											
											if (array_size($_POST['Extra'][$currentColumn]) > 0){
												if (in_array($currentValue, $_POST['Extra'][$currentColumn])){
													$preCheck = 'checked';
												}
												
												if ($tempValue2['Org'] != ''){
													if (in_array($tempValue2['Org'], $_POST['Extra'][$currentColumn])){
														$preCheck = 'checked';
													}
												}
											}											
											
											
											$currentHash 			= md5($currentColumn . '::'. $currentValue);
											
											
											if ($tempValue2['Org'] != ''){
												$currentValueSlashed 	= htmlspecialchars($tempValue2['Org'], ENT_QUOTES);
											} else {
												$currentValueSlashed 	= htmlspecialchars($currentValue, ENT_QUOTES);	
											}
											
											
											
											$currentNameSlashed 	= htmlspecialchars($currentName, ENT_QUOTES);
											
											$modalBody .= "<tr>";
												$modalBody .= "<td class='text-center'>";
													$modalBody .= "<div class='{$currentColumn}_checkboxSection'>";
														$modalBody .= "<input type='checkbox' id='{$currentHash}' name='{$currentColumn}[]' class='advancedOptionsColumn {$currentColumn}_checkbox' currentcolumn='{$currentColumn}' value='{$currentValueSlashed}' responseid='{$responseID}' display='{$currentNameSlashed}' {$preCheck}/>";
													$modalBody .= "</div>";
												$modalBody .= "</td>";
												
												
												$modalBody .= "<td>";
													$modalBody .= "<span title='{$currentValueSlashed}'>{$currentName}</span>";
												$modalBody .= "</td>";
												
												
												$modalBody .= "<td>";
													$modalBody .= "{$currentCount}";
												$modalBody .= "</td>";
												
												
												if ($mode == 2){
													$modalBody .= "<td>";
														$modalBody .= "{$currentGeneCount}";
													$modalBody .= "</td>";
												}
												
												
											$modalBody .= "</tr>";
										}
										
									
									$modalBody .= "</tbody>";
									
									
									
								
								$modalBody .= "</table>";
							$modalBody .= "</div>";
							
							
						$modalBody .= "</div>";
						
						
						echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
					}

				echo "</div>";
			echo "</div>";
			echo "</div>";
			
		}
	}
	echo "</div>";
	
	
	if (false){
	echo "<div class='form-group'>";
		echo "<button class='xcol-sm-offset-1 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot</button>";
		echo "&nbsp;<a href='{$PAGE['URL']}'>Reset</a>";
		echo "&nbsp;<span class='busySection startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
	echo "</div>";
	}
	
}

?>


<script type="text/javascript">

$(document).ready(function(){
	 var tableToSort = $('.tableToSort').stupidtable();
	 
	 
	tableToSort.on("aftertablesort", function (event, data) {
        var th = $(this).find("th");
        th.find(".arrow").remove();
        var dir = $.fn.stupidtable.dir;
		
		if (data.direction == 'asc'){
			var icon = "<?php echo printFontAwesomeIcon('fas fa-sort-up'); ?>";
		} else {
			var icon = "<?php echo printFontAwesomeIcon('fas fa-sort-down'); ?>";	
		}

        th.eq(data.column).append('<span class="arrow">' + icon +'</span>');
      });
	
	$('#submitButtonGroup').show();
	
	$('#data_filter_has_result').val(1);
	
});
</script>
<style>
.modal-dialog{
	max-width:800px;
}
</style>