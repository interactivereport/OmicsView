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
						echo "<strong>Comparison IDs:</strong> 
								<a href='#comparisonListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparison IDs</a>";
					echo "</div>";
					
					$values = implode("\n", $dataArray['ComparisonIDs']);
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control inputForm' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='Please enter two or more comparison IDs, seperated by line break.'>{$values}</textarea>";
					echo "</div>";
					
					$modalID 	= 'comparisonListModal';
					$modalTitle = 'Please select a comparison list you like to load:';
					$modalBody	= '';
					echo printModal($modalID, $modalTitle, $modalBody, 'Select');
					
				echo "</div>";
			}
		echo "</div>";
	}
	
	
	if (has_internal_data($category)){
		
		if (!isset($dataArray['data_source'])){
			$dataArray['data_source'][] = 'private';
			$dataArray['data_source'][] = 'public';
		}
		
		$has_internal_data = true;
		
		$modalBody .= "<div class='form-check col-sm-12'>";
		
			$modalBody .= "<div style='margin-top:8px;'>";
				$modalBody .= "<strong>Source of the Comparison IDs:</strong>";
			$modalBody .= "</div>";
			
			$modalBody .= "<div style='margin-left:15px;'>";
				$modalBody .= internal_data_print_form_html($dataArray);
			$modalBody .= "</div>";
			
			$modalBody .= internal_data_print_modal($dataArray, $category);
		
		$modalBody .= "</div>";
		
		echo $modalBody;
		
	}
	
	
	
	if (true){
		

		$modalBody = "<div>";

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
					$modalBody .= "<p><strong>{$APP_MESSAGE['How do you like to compare the genes?']}</strong></p>";
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
			$modalBody .= "<br/>";
			
			
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
			
			$modalBody .= "<br/>";
			

			
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
				

				$modalBody .= "<div class='form-group row col-sm-12 localComparison {$localComparisonClass}' style='margin-left:1px;'>";
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
					$modalBody .= "<label for='method'><strong>{$APP_MESSAGE['Maximum Number of Top Matched Genes']}:</strong></label>
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

			}
			
			
			
			
			if (true){
				
				unset($checked1, $checked2, $checked3, $values);
				
				if (isset($dataArray['min_matched'])){
					$selected = $dataArray['min_matched'];
				} else {
					$selected = '0';
				}
							
				$values['0'] 		= 'None';
				$values['10'] 		= '10% of the # of comparisons';
				$values['25'] 		= '25%';
				$values['50'] 		= '50%';
				$values['75'] 		= '75%';
				$values['85'] 		= '85%';
				$values['95'] 		= '95%';
				$values['100'] 		= '100%';
				
				
				$modalBody .= "<div class='form-group row col-sm-12 ' style='margin-left:1px;'>";
					$modalBody .= "<label for='method'>
										<strong>Minimum # of Data Point to be Included in the Result:</strong>
										<p class='form-text'>
										The correlation coefficient of each {$APP_MESSAGE['gene']} pair is based on their intersected comparison data points. 
										If this number is set, it will eliminate the {$APP_MESSAGE['gene']} pair with high correlation coefficient and small intersection. 
										This number is relative to the number of comparison entered by user.
										</p>
									</label>
									<div xclass='col-1'>
										<select class='form-control inputForm' id='min_matched' name='min_matched' style='margin-left:15px;'/>
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
		
		echo printModal($modalID, $modalTitle, $modalBody);
		
	}
	
	


	if (true){
		echo "<div class='row' style='margin-top:20px;'>";
			echo "<input type='hidden' name='submit' value='1'/>";
			echo "<div class='form-group'>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<button class='xcol-sm-offset-1 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot</button>";
				echo "&nbsp; &nbsp;<a data-toggle='modal' href='#advancedOptionSection'>" . printFontAwesomeIcon('fas fa-cog') . " Advanced Options</a>";
				echo "&nbsp; &nbsp;<a href='{$PAGE['URL']}'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
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
	
	$(document).on('click', '#comparisonMissingInfoTrigger', function(){
		$('#comparisonMissingInfo').toggle();
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