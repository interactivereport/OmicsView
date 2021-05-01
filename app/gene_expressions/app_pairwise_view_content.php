<?php

$dataArray = getSQLCache($_GET['key']);
if (array_size($dataArray) > 0){
	$submit 	= 1;
}


$allSQLColumns 		= array_keys($APP_CONFIG['DB_Dictionary']['Samples']['Filter']);
$allSQLColumns 		= "`SampleIndex`, `" . implode('`,`', $allSQLColumns) . "`";


$allRecordIndexes 	= get_multiple_record('Samples', '', 'GetAssoc', $allSQLColumns, 1);
$recordCount		= array_size($allRecordIndexes);
$recordCountDisplay	= number_format($recordCount);



echo "<div class='row'>";
	echo "<div class='col-xl-3 col-lg-4'>";
		echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' enctype='multipart/form-data' autocomplete='off'>";
			
			if (true){
				
				echo "<fieldset>";
				
				echo "<legend>&nbsp;Sample Source&nbsp;&nbsp;</legend>";
			
				if (true){
					echo "<div class='row'>";
					echo "<div class='col-lg-12'>";

						$sql_name	= 'source';
						if (isset($dataArray[$sql_name])){
							$value	= $dataArray[$sql_name];
						} else {
							$value	= 'filter';
						}
			
					
						$values = array();
						$values['filter'] 	= 'Sample Filter';
						$values['list'] 	= 'Enter Sample IDs Manually / Select from List';
			
			
						echo "<div>";
							foreach($values as $currentKey => $currentDisplayValue){
								
								$checked = '';
								if ($value == $currentKey){
									$checked = 'checked';
								}
								
								echo "<div class='form-check-inline'>";
									echo "<label class='form-check-label' for='{$sql_name}_{$currentKey}'>";
										echo "<input class='form-check-input {$sql_name}' type='radio' id='{$sql_name}_id_{$currentKey}' name='{$sql_name}' value='{$currentKey}' {$checked}>";
											echo '&nbsp;' . $currentDisplayValue . '&nbsp;';
										echo "</label>";
								echo "</div>";
							}
							unset($values);
						echo "</div>";
			
			
					echo "</div>";
					echo "</div>";
				}
				
				echo "<br/>";
			
			
				if (true){
					$filterHTML = '';
					
					$name		= "Sample Filter";
					$filterHTML .= "<div style='margin-bottom:10px;'>";
						$filterHTML .= "<div><strong>{$name}</strong> <span id='selectedSampleCount'></span></div>";
						$filterHTML .= "<div>";
						$filterHTML .= "<a href='javascript:void(0);' id='resetFilterTrigger'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset Filter</a></div>";
					$filterHTML .= "</div>";
					
					foreach($APP_CONFIG['DB_Dictionary']['Samples']['Filter'] as $tempKey1 => $tempValue1){
		
						$currentColumn	= $tempValue1['SQL'];
						$label 			= $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$currentColumn]['Title'];
						$sql_name		= $currentColumn;
						$sql_id			= $sql_name;
						$sql_value 		= array();
						
						$values 		= array_column($allRecordIndexes, $currentColumn);
						$values 		= array_count_values($values);
						arsort($values);
						
						
						$blacklist		= array('normal control', 'No Info', 'NA');
						
						foreach($blacklist as $tempKeyX => $tempValueX){
							if (isset($values[$tempValueX])){
								$temp = $values[$tempValueX];
								unset($values[$tempValueX]);
								$values[$tempValueX] = $temp;
								unset($temp);
							}
						}
						
						
						$openStatus		= $tempValue1['OpenStatus'];
						
						if (isset($dataArray["filter_{$sql_name}"])){
							$sql_value = $dataArray["filter_{$sql_name}"];
						} elseif ($_GET[$sql_name] != ''){
							$sql_value	= array($_GET[$sql_name]);
							$submit		= 1;
						}
						
						if (array_size($sql_value) > 0){
							$openStatus = 1;	
						}
						
						
						$filterHTML .="<div class='card'>";
							$filterHTML .="<div class='card-block'>";
								$filterHTML .="<div class='card-title'>";
									if ($openStatus){
										$classOpened = '';	
										$classClosed = 'startHidden';
										$opened		 = 1;
									} else {
										$classOpened = 'startHidden';	
										$classClosed = '';
										$opened		 = 0;
									}
								
									$filterHTML .="<a href='javascript:void(0);' opened='{$opened}' class='h6 filterTreeTrigger' childrenname='{$sql_name}'>";
									$filterHTML .="<span id='{$sql_name}_menu_opened' class='{$classOpened}'>" . printFontAwesomeIcon('fas fa-caret-down') . "</span>";
									$filterHTML .="<span id='{$sql_name}_menu_closed' class='{$classClosed}'>" . printFontAwesomeIcon('fas fa-caret-right') . "</span>";
									$filterHTML .="{$label}</a>";
								$filterHTML .="</div>";
								
								if ($openStatus){
									$class = '';	
								} else {
									$class = 'startHidden';	
								}
								
								$filterHTML .= "<div class='card-text card-text-overflow {$class}' id='{$sql_name}_section'>";
								
									$filterHTML .= "<table class='table table-sm table-striped'>";
										$filterHTML .= "<thead>";
											$filterHTML .="<tr>";
												$filterHTML .="<th style='width:20px;'>
																<input id='{$currentColumn}_SelectAll' class='selectAllTrigger' type='checkbox' value='1' children_class='{$currentColumn}'/>
																</th>";
												$filterHTML .="<th style='width:90%;'>
																	<span class='small'>
																		<strong>Select All</strong>
																	</span>
																</th>";
												

												
												$countID = "{$sql_name}_Total";
												
												$filterHTML .="<th>
																	<span class='badge badge-secondary'>
																			<span id='{$countID}' class='filter_{$sql_name}_Count'>{$recordCountDisplay}</span>
																			| {$recordCountDisplay}
																	</span>
															</th>";
		
											$filterHTML .="</tr>";
										$filterHTML .="</thead>";
										
										
										$filterHTML .="<tbody>";
										foreach($values as $currentCategory => $currentCount){
											
											if ($currentCategory == '') continue;
											
											$currentCategory = ucwords2($currentCategory);
											
											unset($checked);
											if (in_array($currentCategory, $sql_value)){
												$checked = 'checked';	
											}
											
											$currentCountDisplay = number_format($currentCount);
											
											$countID = "{$currentColumn}_" . md5(strtolower($currentCategory));
		
											
											$filterHTML .="<tr>";
												$filterHTML .="<td style='width:20px;'><input parentcheckbox='{$currentColumn}_SelectAll' class='user_checkbox {$sql_name}' type='checkbox' value=\"{$currentCategory}\" name='filter_{$sql_name}[]' {$checked}/></td>";
												$filterHTML .="<td style='width:90%;'><span title='{$currentCategory}' class='small'>{$currentCategory}</span></td>";
												$filterHTML .="<td><span class='badge badge-secondary'>
																	<span id='{$countID}' class='filter_{$sql_name}_Count'>{$currentCountDisplay}</span>
																	| {$currentCountDisplay}
																	</span>
																</td>";
											$filterHTML .="</tr>";
										}
										$filterHTML .="</tbody>";
										
									$filterHTML .="</table>";
								$filterHTML .="</div>";
							$filterHTML .="</div>";
						$filterHTML .="</div>";
					}
					
					echo "<div id='sample_source_filter' class='sample_source_member startHidden'>";
						echo $filterHTML;
					echo "</div>";
				}
				
				if (true){
					$sql_name	= "samples";
					$name		= "Sample IDs";
					if (isset($dataArray[$sql_name])){
						$values = implode("\n", $dataArray[$sql_name]);
					} else {
						$values = '';
					}
					$exampleMessage = 'Please enter one or more sample IDs, seperated by line break.';
					
					echo "<div id='sample_source_list' class='sample_source_member startHidden'>";
							echo "<div>";
								echo "<strong>{$name}:</strong> 
										<a href='#samples_list_modal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Sample IDs</a>";
							echo "</div>";
							
							
							echo "<div style='margin-top:10px;'>";
								echo "<textarea class='form-control' rows='6' name='{$sql_name}' id='{$sql_name}' placeholder='{$exampleMessage}'>{$values}</textarea>";
							echo "</div>";
							
							$modalID 	= "samples_list_modal";
							$modalTitle = 'Please select a sample list you like to load:';
							$modalBody	= '';
							echo printModal($modalID, $modalTitle, $modalBody, 'Select');
					echo "</div>";
				}
			
				echo "</fieldset>";

			}
			
			
			if (true){
				$axisArray = array();
				$axisArray['x_axis'] = 'Horizontal Axis';
				$axisArray['y_axis'] = 'Vertical Axis';				
			
				foreach($axisArray as $axis => $Axis){
					echo "<fieldset>";
					
					echo "<legend>&nbsp;{$Axis}&nbsp;&nbsp;</legend>";
				
					if (true){
						echo "<div class='row'>";
						echo "<div class='col-lg-12'>";
						
							$sql_name	= $axis;
							$name		= $Axis;
							if (isset($dataArray[$sql_name])){
								$value	= $dataArray[$sql_name];
							} else {
								$value	= 'samples';
							}
				
						
							$values = array();
							$values['samples'] 	= 'Samples';
							$values['gene'] 	= 'Gene';
				
				
							echo "<div>";
								
							
								foreach($values as $currentKey => $currentDisplayValue){
									
									$checked = '';
									if ($value == $currentKey){
										$checked = 'checked';
									}
									
									echo "<div class='form-check-inline'>";
										echo "<label class='form-check-label' for='{$sql_name}_{$currentKey}'>";
											echo "<input class='form-check-input {$sql_name}' type='radio' id='{$sql_name}_id_{$currentKey}' name='{$sql_name}' value='{$currentKey}' {$checked}>";
												echo '&nbsp;' . $currentDisplayValue . '&nbsp;';
											echo "</label>";
									echo "</div>";
								}
								unset($values);
							echo "</div>";
				
				
						echo "</div>";
						echo "</div>";
					}
	
					if (true){
						$sql_name	= "{$axis}_gene";
						$name		= $APP_MESSAGE['Gene Name'];
						if (isset($dataArray[$sql_name])){
							$value	= $dataArray[$sql_name];
						} else {
							$value 	= '';	
						}
						$exampleMessage = "Please enter a {$APP_MESSAGE['gene']} name, e.g., {$APP_MESSAGE['Gene_Example']}";
						
						
						echo "<div class='row {$axis}_section {$sql_name}_section startHidden'>";
						echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>";
							echo "<div style='margin-top:10px;'>";
								echo "<strong>{$name}:</strong>";
							echo "</div>";
							
							echo "<div class='input-group' style='margin-top:10px;'>";
								echo "<input type='text' name='{$sql_name}' id='{$sql_name}' class='form-control ' value='{$value}' placeholder='{$exampleMessage}'/>";
							echo "</div>";
							
						echo "</div>";
						echo "</div>";
					}

					if (true){
						$sql_name	= "{$axis}_sample_attribute";
						$name		= "Sample Attribute";
						if (isset($dataArray[$sql_name])){
							$value	= $dataArray[$sql_name];
						} else {
							$value 	= '';	
						}
						
						
						$values = getSampleAttributes(1);
						$values['Default']['SampleID'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
						$values['All']['SampleID'] 		= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['SampleID']['Title'];
						natksort($values['All']);
						
						echo "<div class='row {$axis}_section {$axis}_samples_section startHidden'>";
						echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>";
							echo "<div style='margin-top:10px;'>";
								echo "<strong>{$name}:</strong>";
							echo "</div>";
							
							echo "<div class='input-group' style='margin-top:10px;'>";
							
								echo "<select class='form-control' name='{$sql_name}' id='{$sql_name}'>";
								
									echo "<optgroup label='Highlighted'>";
										foreach($values['Default'] as $currentKey => $currentDisplayValue){
											unset($selected);
											if ($value == $currentKey){
												$selected = "selected='selected'";	
											}
											
											echo "<option value='{$currentKey}' {$selected}>{$currentDisplayValue}</option>";
										}
									echo "</optgroup>";
									
									
									if (array_size($values['Clinical Triplets']) > 0){
										echo "<optgroup label='Clinical Triplets'>";
										foreach($values['Clinical Triplets'] as $currentKey => $currentDisplayValue){
											unset($selected);
											if ($value == $currentKey){
												$selected = "selected='selected'";	
											}
											
											echo "<option value='{$currentKey}' class='clinical_triplets' {$selected}>{$currentDisplayValue}</option>";
										}
									echo "</optgroup>";
										
									}
									
									
									echo "<optgroup label='All'>";
										foreach($values['All'] as $currentKey => $currentDisplayValue){
											unset($selected);
											if ($value == $currentKey){
												$selected = "selected='selected'";	
											}
											
											echo "<option value='{$currentKey}' {$selected}>{$currentDisplayValue}</option>";
										}
									echo "</optgroup>";
								
								
								echo "</select>";
							
	
							echo "</div>";
							
						echo "</div>";
						echo "</div>";
					}
					
					if (true){
						$sql_name	= "{$axis}_sample_numeric";
						$name		= "Convert sample data to numeric";
						if ($dataArray[$sql_name]){
							$checked	= 'checked';
						} else {
							$checked 	= '';	
						}
						
						
						
						
						echo "<div class='row {$axis}_section {$axis}_samples_section startHidden'>";
						echo "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>";
							echo "<div class='input-group' style='margin-top:10px;'>";
							
								echo "<div class='form-check'>";
									echo "<input class='form-check-input' type='checkbox' value='1' name='{$sql_name}' id='{$sql_name}' {$checked}/>";
									echo "<label class='form-check-label' for='{$sql_name}'>{$name}</label>";
								echo "</div>";
								
							echo "</div>";
							
						echo "</div>";
						echo "</div>";
					}
	
					
					echo "</fieldset>";
					
					
					
					if (!$printedSwapLink){
						
						$printedSwapLink = true;
						
						echo "<div style='margin-top:10px;'>";
							echo "<a href='javascript:void(0);' id='swapAxisTrigger'>" .  printFontAwesomeIcon('fas fa-arrows-alt-v') . " Swap Axis Settings</a>";
						echo "</div>";
					}
					
				}
			
			}
			
			
			
			
			if (true){
				
				echo "<fieldset id='other_section'>";
				
				echo "<legend>&nbsp;Other Options&nbsp;&nbsp;</legend>";
				
				if (true){
					$sql_name	= "color_by";
					$name		= "Color Data Point By";
					
					echo "<div>";
						echo "<strong>{$name}:</strong>";
					echo "</div>";
					
					if (true){
						$sql_name				= "color_by";
						$currentKey 			= '';
						$currentDisplayValue 	= 'None';
						$checked = '';
						if ($dataArray[$sql_name] == $currentKey){
							$checked = 'checked';
						}
							
						echo "<div class='form-check' style='margin-top:10px;'>";
							echo "<label class='form-check-label' for='{$sql_name}_{$currentKey}'>";
								echo "<input class='form-check-input {$sql_name}' type='radio' id='{$sql_name}_id_{$currentKey}' name='{$sql_name}' value='{$currentKey}' {$checked}>";
									echo '&nbsp;' . $currentDisplayValue . '&nbsp;';
								echo "</label>";
						echo "</div>";
					}
					
					

					if (true){
						$currentKey 			= 'sample';
						$currentDisplayValue 	= 'Sample';
						$checked = '';
						if ($dataArray[$sql_name] == $currentKey){
							$checked = 'checked';
						}
							
						echo "<div class='form-check' style='margin-top:10px;'>";
							echo "<label class='form-check-label' for='{$sql_name}_{$currentKey}'>";
								echo "<input class='form-check-input {$sql_name}' type='radio' id='{$sql_name}_id_{$currentKey}' name='{$sql_name}' value='{$currentKey}' {$checked}>";
									echo '&nbsp;' . $currentDisplayValue . '&nbsp;';
								echo "</label>";
						echo "</div>";
					}
					
					if (true){
						$sql_name	= "color_by_sample";
						$values 	= getSampleAttributes(0);
						
						if (isset($dataArray[$sql_name])){
							$value	= $dataArray[$sql_name];
						} else {
							$value 	= '';	
						}
						
						
						echo "<div class='color_by_member' id='{$sql_name}_section'>";
						echo "<div class='input-group' style='margin-top:10px;'>";
							echo "<select class='form-control' name='{$sql_name}' id='{$sql_name}'>";
							
								echo "<optgroup label='Highlighted'>";
									foreach($values['Default'] as $currentKey => $currentDisplayValue){
										unset($selected);
										if ($value == $currentKey){
											$selected = "selected='selected'";	
										}
										
										echo "<option value='{$currentKey}' {$selected}>{$currentDisplayValue}</option>";
									}
								echo "</optgroup>";
								
								echo "<optgroup label='All'>";
									foreach($values['All'] as $currentKey => $currentDisplayValue){
										unset($selected);
										if ($value == $currentKey){
											$selected = "selected='selected'";	
										}
										
										echo "<option value='{$currentKey}' {$selected}>{$currentDisplayValue}</option>";
									}
								echo "</optgroup>";
								
							echo "</select>";
						echo "</div>";
						echo "</div>";
					}
					
					
					
					
					
					if (true){
						$sql_name				= "color_by";
						$currentKey 			= 'gene';
						$currentDisplayValue 	= 'Gene';
						$checked = '';
						if ($dataArray[$sql_name] == $currentKey){
							$checked = 'checked';
						}
							
						echo "<div class='form-check' style='margin-top:10px;'>";
							echo "<label class='form-check-label' for='{$sql_name}_{$currentKey}'>";
								echo "<input class='form-check-input {$sql_name}' type='radio' id='{$sql_name}_id_{$currentKey}' name='{$sql_name}' value='{$currentKey}' {$checked}>";
									echo '&nbsp;' . $currentDisplayValue . '&nbsp;';
								echo "</label>";
						echo "</div>";
					}
					
					
					
					
					if (true){
						$sql_name			= "color_by_gene";
						$placeHolderText	= "Please enter a {$APP_MESSAGE['gene']} name";
						echo "<div class='color_by_member startHidden' id='{$sql_name}_section'>";
						echo "<div class='input-group' style='margin-top:10px;'>";
							echo "<input type='text' class='form-control' name='{$sql_name}' id='{$sql_name}' value='{$dataArray[$sql_name]}' placeholder='{$placeHolderText}'>";
						echo "</div>";
						echo "</div>";
					}
					
					
					
					
				}
				

				echo "<br/>";
			
				
				
			
				echo "</fieldset>";

			}
			
			echo "<div class='form-group'>";
				echo "<input type='hidden' id='page_height' name='page_width' value='0'/>";
				echo "<input type='hidden' id='page_width' name='page_width' value='0'/>";
				echo "<br/>";
				echo "<button class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot</button>";
				echo "&nbsp;<a href='{$PAGE['URL']}'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
			echo "</div>";

		
		echo "</form>";
	echo "</div>";
	
	
	echo "<div class='col-xl-9 col-lg-8'>";
		echo "<div id='feedbackSection'></div>";
		echo "<div id='feedbackSection2' class='startHidden'></div>";
		echo "<h4 class='busySection startHidden' style='margin-top:50px;'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). " Loading...</h4>";
	echo "</div>";
	
	
echo "</div>";


?>


<style>

fieldset{
	border:1px solid #999;	
	padding:10px;
	margin-top:20px;
}

legend{
	width:auto;
	max-width:auto;	
	font-size: 1.2em;
	font-weight:bold;
}


.card{
	margin-bottom:10px;	
}

.card-block{
	padding:5px;	
}

.card-text{
	max-height:190px;	
}

.card-text-overflow{
	overflow-y:auto;
}

.table{
	margin-bottom:2px;	
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

	$('.source').change(function(){
		refresh_sample_source_section();
	});
	
	
	

	<?php if (true){ ?>
	
	$('#sample_source_filter').on('click', '#resetFilterTrigger', function(){
		$('.user_checkbox').prop('checked', false);
		$('.selectAllTrigger').prop('checked', false);
		
		$('#selectedSampleCount').empty();
		
		$('#feedbackSection2').empty();

	});
	
	
	$('#sample_source_filter').on('change', '.selectAllTrigger', function(){
		var childrenClass 	= $(this).attr('children_class');
		var checkStatus		= $(this).prop('checked');
		
		$('.' + childrenClass).prop('checked', checkStatus);
		
		submitFilterData();
	});
	
	
	$('#sample_source_filter').on('click', '.filterTreeTrigger', function(){
		
		var childrenName		= $(this).attr('childrenname');
		var currentOpenedStatus = parseInt($(this).attr('opened'));

		if (currentOpenedStatus == 1){
			
			$('#' + childrenName + '_section').hide();
			$('#' + childrenName + '_menu_opened').hide();
			$('#' + childrenName + '_menu_closed').show();
			
			$(this).attr('opened', '0');
		} else {
			
			$('#' + childrenName + '_section').show();
			$('#' + childrenName + '_menu_opened').show();
			$('#' + childrenName + '_menu_closed').hide();
			
			$(this).attr('opened', '1');
		}

	});
	
	
	
	$('#sample_source_filter').on('change', '.user_checkbox', function(){
		
		var parentCheckbox = $(this).attr('parentcheckbox');
		
		$('#' + parentCheckbox).prop('checked', false);
		
		submitFilterData();
	});
	
	$('#sample_source_filter').on('change', '.inputBox', function(){
		submitFilterData();
	});
	
	
	$('#feedbackSection').on('click', '#searchResultTrigger', function(){
		$('#searchSummary').toggle();
	});
	<?php } ?>
	
	
	
	$('#y_axis_sample_attribute').change(function(){
		var currentValue = $(this).val();
		
		if (currentValue.startsWith("Clinical_Triplets_")){
			$('#y_axis_sample_numeric').prop('checked', true);
		}
	});
	
	$('#x_axis_sample_attribute').change(function(){
		var currentValue = $(this).val();
		
		if (currentValue.startsWith("Clinical_Triplets_")){
			$('#x_axis_sample_numeric').prop('checked', true);
		}
	});
	
	
	
	$('.x_axis').change(function(){
		refresh_axis_section();
	});	
	
	$('.y_axis').change(function(){
		refresh_axis_section();
	});	
	
	
	$('.color_by').change(function(){
		refresh_color_section();
	});	
	
	
	$('#swapAxisTrigger').click(function(){
		
		var x, y;

		x = $('.x_axis:checked').val();
		y = $('.y_axis:checked').val(); 
		$('#y_axis_id_' + x).prop('checked', true);
		$('#x_axis_id_' + y).prop('checked', true);
		
		
		x = $('#x_axis_gene').val();
		y = $('#y_axis_gene').val();
		$('#x_axis_gene').val(y);
		$('#y_axis_gene').val(x);
		
		
		
		x = $('#x_axis_sample_attribute').val();
		y = $('#y_axis_sample_attribute').val();
		$('#x_axis_sample_attribute').val(y);
		$('#y_axis_sample_attribute').val(x);
		
		x = $('#x_axis_sample_numeric').prop('checked');
		y = $('#y_axis_sample_numeric').prop('checked');
		$('#x_axis_sample_numeric').prop('checked', y);
		$('#y_axis_sample_numeric').prop('checked', x);
		

		refresh_axis_section();
		
		$('#form_application').submit();
	
	});	
	
	
	$(document).on('click', '.submitButton', function(){
		$('#form_application').submit();
	});
	
	$(window).resize(function() {
		$('#page_width').val(document.getElementById("feedbackSection").clientWidth);
		$('#page_height').val(document.getElementById("feedbackSection").clientWHeight);
	});
	
	
	<?php if (true){ ?>
	
	$('#samples_list_modal').on('show.bs.modal', function(){
		
		$.ajax({
			type: 'GET',
			url: 'app_list_ajax_selection.php?category=Sample&input_name=list&input_class=list&pre_selected_list_id=<?php echo $sampleListID; ?>',
			success: function(responseText){
				$('#samples_list_modal').find('.modal-body').html(responseText);
			}
		});
	});
	
	
	$('#samples_list_modal').on('change', '.list', function(){
		var currentListID = $(this).val();
		
		
		var content = $('#sample_list_content_' + currentListID).val();
		
		$('#samples').val(content);
	});
	
	
	$('#samples_list_modal').on('click', '.list_Name', function(){
		var currentListID 	= $(this).attr('listid');
		
		var radioID			= 'list_' + currentListID;
		
		$('#' + radioID).prop('checked', true);
		
		var content = $('#sample_list_content_' + currentListID).val();
		
		$('#samples').val(content);
	});
	
	
	
	$(document).on('click', '#sampleMissingInfoTrigger', function(){
		$('#sampleMissingInfo').toggle();
	});
	

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
	
	<?php if ($dataArray['source'] == 'filter'){ ?>
		submitFilterData();
	<?php } ?>
	
	refresh_sample_source_section();
	refresh_axis_section();
	refresh_color_section();
	
	$('#page_width').val(document.getElementById("feedbackSection").clientWidth);
	$('#page_height').val(document.getElementById("feedbackSection").clientWHeight);
	
	<?php if (array_size($dataArray) > 0){ ?>
		$('#form_application').submit();
	<?php } ?>
	
	
});

function refresh_sample_source_section(){
	var source = $('.source:checked').val();
	
	$('.sample_source_member').hide();	
	
	$('#sample_source_' + source).show();

	return true;
}

function refresh_color_section(){
	
	var color_by = $('.color_by:checked').val();
	
	$('.color_by_member').hide();	
	
	$('#color_by_' + color_by + '_section').show();

	return true;
}



function refresh_axis_section(){

	var value_x = $('.x_axis:checked').val();
	var value_y = $('.y_axis:checked').val();
	
	$('.x_axis_section').hide();
	$('.x_axis_' + value_x + '_section').show();
	
	
	$('.y_axis_section').hide();
	$('.y_axis_' + value_y + '_section').show();

	return true;
}

function submitFilterData(){
	
	var data = new Object();
	var hasSelected = false;
	
	
	<?php 
	
	foreach($APP_CONFIG['DB_Dictionary']['Samples']['Filter'] as $tempKey1 => $tempValue1){
		$currentColumn	= $tempValue1['SQL'];
	?>
		data['filter_<?php echo $currentColumn; ?>'] = [];
		$(".<?php echo $currentColumn; ?>:checked").each(function() {
			data['filter_<?php echo $currentColumn; ?>'].push($(this).val());
			hasSelected = true;
		});
	<?php } ?>
	
	
	$('#feedbackSection2').empty();			
	$('.busySection').show();
	$('#feedbackSection2').hide();

	$.ajax({
		type: 'POST',
		url: 'app_pairwise_view_ajax.php',
		data: data,
		success: function(responseText){
			$('.busySection').hide();
			$('#feedbackSection2').empty();
			$('#feedbackSection2').html(responseText);
			$('#feedbackSection2').show();
			
			$('#feedbackSection').empty();
		}
	});	

	return true;
}



function beforeSubmit() {
	$('.advancedOptionsSection').hide();
	$('#feedbackSection').empty();
	$('#feedbackSection').hide();
	
	$('#feedbackSection2').empty();	
	$('#feedbackSection2').hide();	
	
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
	
	$('#feedbackSection2').empty();	
	$('#feedbackSection2').hide();
	
	$('#feedbackSection').html(responseText);
	$('#feedbackSection').show();
	
	$('html,body').animate({
		scrollTop: $('#bxaf_page_right_content').offset().top
	});
	
	return true;

}



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
		
		$('#searchOption_1_Section').hide();
		
	} else {

		$('#searchOption_1_Section').show();
		$('#data_source_private_section').hide();
	}
}

<?php } ?>

</script>