<?php

$dataArray = getSQLCache($_GET['key']);


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
										echo "<strong>{$APP_MESSAGE['Source Gene Names']}:</strong>";
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
						echo "<textarea class='form-control inputForm' rows='8' name='GeneNames' id='GeneNames' placeholder='Please enter one or more {$APP_MESSAGE['gene']} names, seperated by line break.'>{$values}</textarea>";
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
						echo "<strong>Sample IDs:</strong> 
								<a href='#sampleListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Sample IDs</a>";
					echo "</div>";
					
					$values = implode("\n", $dataArray['SampleIDs']);
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control inputForm' rows='8' name='SampleIDs' id='SampleIDs' placeholder='Please enter two or more sample IDs, seperated by line break.'>{$values}</textarea>";
					echo "</div>";
					
					$modalID 	= 'sampleListModal';
					$modalTitle = 'Please select a sample list you like to load:';
					$modalBody	= '';
					echo printModal($modalID, $modalTitle, $modalBody, 'Select');
					
				echo "</div>";
			}
		echo "</div>";
	}
	
	
	if (true){
		

		$modalBody = "<div>";
		
			if (has_internal_data($category)){
				
				$has_internal_data = true;
				
				if (!isset($dataArray['data_source'])){
					$dataArray['data_source'][] = 'private';
					$dataArray['data_source'][] = 'public';
				}
				
				
				$modalBody .= "<div class='form-check col-sm-12'>";
				
					$modalBody .= "<div style='margin-top:8px;'>";
						$modalBody .= "<strong>Source of the Sample IDs:</strong>";
					$modalBody .= "</div>";
					
					$modalBody .= "<div style='margin-left:15px;'>";
						$modalBody .= internal_data_print_form_html($dataArray);
					$modalBody .= "</div>";
					
					$modalBody .= internal_data_print_modal($dataArray, '');
				
				$modalBody .= "</div>";
				
			}
		
		
		
			if (true){
				unset($checked1, $checked2, $class);
				
				if (isset($dataArray['comparison'])){
					if ($dataArray['comparison'] == 1){
						$checked1 = 'checked';
					} else {
						$checked2 = 'checked';
						$localComparisonClass = 'startHidden';
					}
				} else {
					$checked1 = 'checked';
				}
				
				
				
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<p><strong>How do you like to compare the {$APP_MESSAGE['genes']}?</strong></p>";
				$modalBody .= "</div>";	
				
				$modalBody .= "<div class='form-check col-sm-12' style='margin-left:35px;'>";
					$modalBody .= "<label class='form-check-label'>
										<input class='form-check-input inputForm comparison' type='radio' id='comparison_1' name='comparison' value='1' {$checked1}/>
										Calculate the correlations against all available {$APP_MESSAGE['genes']} in database
									</label>";

				$modalBody .= "</div>";
				
				
				$modalBody .= "<div class='form-check col-sm-12' style='margin-left:35px;'>";
					$modalBody .= "<label class='form-check-label'>
										<input class='form-check-input inputForm comparison' type='radio' id='comparison_2' name='comparison' value='2' {$checked2}/>
										Calculate the correlations among the entered {$APP_MESSAGE['genes']} only
									</label>";
				$modalBody .= "</div>";

			}
			
			
			
			if (true){
				unset($checked0, $checked1, $class);
				
				if (isset($dataArray['method'])){
					if ($dataArray['method'] == 1){
						$checked1 = 'checked';
					} else {
						$checked0 = 'checked';
					}
				} else {
					$checked0 = 'checked';
				}
				
				
				
				$modalBody .= "<div class='form-check col-sm-12'>";
					$modalBody .= "<p><strong>Correlation Method:</strong></p>";
				$modalBody .= "</div>";	
				
				$modalBody .= "<div class='form-check col-sm-12' style='margin-left:35px;'>";
					$modalBody .= "<label class='form-check-label'>
										<input class='form-check-input inputForm' type='radio' id='method_0' name='method' value='0' {$checked0}/>
										Pearson Correlation
									</label>";

				$modalBody .= "</div>";
				
				
				$modalBody .= "<div class='form-check col-sm-12' style='margin-left:35px;'>";
					$modalBody .= "<label class='form-check-label'>
										<input class='form-check-input inputForm' type='radio' id='method_1' name='method' value='1' {$checked1}/>
										Spearman Correlation (Pearson Correlation Coefficient Between Ranked Variables)
									</label>";
				$modalBody .= "</div>";

			}
			
			
			

			
			if (true){
				unset($checked, $class);
				
				if (isset($dataArray['transform'])){
					if ($dataArray['transform']){
						$checked = 'checked';
					} else {
						$class = 'startHidden';	
					}
				} else {
					$checked = '';
					$class = 'startHidden';	
				}
				
				$modalBody .= "<div class='form-check col-sm-12' style='margin-top:15px;'>";
					$modalBody .= "<label for='transform' class='form-check-label'>
										<input class='form-check-input inputForm' type='checkbox' id='transform' name='transform' value='1' {$checked}/>
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
						$modalBody .= "<input class='col-1 inputForm' type='text' id='transform_value' name='transform_value' value='{$value}'/>";
					$modalBody .= "</div>";
				
				$modalBody .= "</div>";

			}
			
			
			
			
			
			if (true){
				
				unset($checked1, $checked2, $checked3, $values);
				
				if (isset($dataArray['direction'])){
					$selected = $dataArray['direction'];
				} else {
					$selected = 1;
				}
				
				
				$values[1] = 'Both';
				$values[2] = 'Positive';
				$values[3] = 'Negative';
				

				$modalBody .= "<div class='form-group row col-sm-12 localComparison {$localComparisonClass}' style='margin-left:1px; margin-top:10px;'>";
					$modalBody .= "<label for='method'><strong>Direction of Correlation:</strong></label>
									<div xclass='col-1'>
									<select class='form-control inputForm' id='direction' name='direction' style='margin-left:15px;'/>";
									
							foreach($values as $tempKey => $tempValue){
								unset($checked);
								
								if ($selected == $tempKey){
									$checked = "selected='selected'";
								}
								
								
								$modalBody .= "<option value='{$tempKey}' {$checked}>{$tempValue}</option>";
								
								
							}		
										
					$modalBody .= "</select></div>";
				$modalBody .= "</div>";	

			}
			
			
			
			
			if (true){
				
				unset($checked1, $checked2, $checked3, $values);
				
				if (isset($dataArray['cutoff'])){
					$selected = $dataArray['cutoff'];
				} else {
					$selected = '0.80';
				}
				
				
				$values['0.995'] 	= '0.995';
				$values['0.95'] 	= '0.95';
				$values['0.90'] 	= '0.90';
				$values['0.85'] 	= '0.85';
				$values['0.80'] 	= '0.80';
				$values['0.76'] 	= '0.76';
				$values['0.54'] 	= '0.54';
				$values['0'] 		= 'No Cut-off';
				
				$modalBody .= "<div class='form-group row col-sm-12 localComparison {$localComparisonClass}' style='margin-left:1px;'>";
					$modalBody .= "<label for='method'><strong>Cut-off of Correlation Coefficient:</strong></label>
									<div xclass='col-1'>
									<select class='form-control inputForm' id='cutoff' name='cutoff' style='margin-left:15px;'/>";
									
							foreach($values as $tempKey => $tempValue){
								unset($checked);
								
								if ($selected == $tempKey){
									$checked = "selected='selected'";
								}
								
								
								$modalBody .= "<option value='{$tempKey}' {$checked}>{$tempValue}</option>";
								
								
							}		
										
					$modalBody .= "</select></div>";
				$modalBody .= "</div>";	
				
				//$modalBody .= "<br/>";
			}
			
			
		
			if (true){
				
				unset($checked1, $checked2, $checked3, $values);
				
				if (isset($dataArray['limit'])){
					$selected = $dataArray['limit'];
				} else {
					$selected = '100';
				}
							
				$values['10'] 		= '10';
				$values['20'] 		= '20';
				$values['50'] 		= '50';
				$values['100'] 		= '100';
				$values['200'] 		= '200';
				$values['500'] 		= '500';
				$values['1000'] 	= '1000';
				$values['0'] 		= 'All';	
				
				$modalBody .= "<div class='form-group row col-sm-12 localComparison {$localComparisonClass}' style='margin-left:1px;'>";
					$modalBody .= "<label for='method'><strong>Maximum Number of Top Matched {$APP_MESSAGE['Genes']}:</strong></label>
									<div xclass='col-1'>
										<select class='form-control inputForm' id='limit' name='limit' style='margin-left:15px;'/>
									";
									
							foreach($values as $tempKey => $tempValue){
								unset($checked);
								
								if ($selected == $tempKey){
									$checked = "selected='selected'";
								}
								
								
								$modalBody .= "<option value='{$tempKey}' {$checked}>{$tempValue}</option>";
								
								
							}		
										
					$modalBody .= "</select></div>";
				$modalBody .= "</div>";	
				
				$modalBody .= "<br/>";
			}


		$modalBody .= "</div>";

		$modalID 	= 'advancedOptionSection';
		
		$modalTitle = "<h4 class='modal-title'>Advanced Options</h4>";
		
		echo "<br/>";
		echo "<div class='row'>";
			echo "<div class='col-12'>";

				echo $modalTitle;
				echo "<hr/>";

				echo $modalBody;
			
			echo "</div>";
		echo "</div>";
		
		
		
		
	}
	
	


	if (true){
		echo "<div class='row' style='margin-top:20px;'>";
			echo "<input type='hidden' name='submit' value='1'/>";
			echo "<div class='form-group'>";
				echo "<button class='col-sm-offset-0 btn btn-primary' type='submit'>Submit</button>";
				echo "&nbsp;<span id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</span>";
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

	

	
	$(document).on('change', '.inputForm', function(){
		$('#feedbackSection').empty();
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
	
	$('#transform').change(function(){
		var checked = $(this).prop('checked');
		
		if (checked){
			$('.transform_section').show();
		} else {
			$('.transform_section').hide();
		}
	});
	
	
	$('.comparison').change(function(){
		var currentValue = $(this).val();
		
		if (currentValue == 2){
			$('.localComparison').hide();
		} else {
			$('.localComparison').show();
		}
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



</script>