<?php

$dataArray = getSQLCache($_GET['key']);

if ($_GET['src'] == 'meta'){
	
	if ((array_size($_SESSION['META_SELECTED_GENENAMES']) > 0) && (array_size($_SESSION['META_SELECTED_COMPNAMES']) > 0)){
		$loadFromSession = 1;	
	}
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
					
					if (!$loadFromSession){
						$values = implode("\n", $dataArray['GeneNames']);
					} else {
						$values = implode("\n", $_SESSION['META_SELECTED_GENENAMES']);
					}
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control' rows='8' name='GeneNames' id='GeneNames' placeholder='Please enter one or more {$APP_MESSAGE['gene']} names, seperated by line break. Leave empty if you want to export all available genes.'>{$values}</textarea>";
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
	



	
	
	if (array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Gene_Expression_Options']) > 0){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<div style='margin-top:20px;'>";
				echo "<strong>{$APP_MESSAGE['Gene Attributes']} <span id='Gene_Plot_Columns_Message' style='font-weight:normal;'></span>:</strong>";
			echo "</div>";
			
			
			foreach($APP_CONFIG['DB_Dictionary']['GeneCombined']['Gene_Expression_Options'] as $currentColumn => $currentDetails){
				
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
			
			
			if (array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Gene_Expression_Options_Completed']) != array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Gene_Expression_Options'])){
				$randomID = md5($currentColumn . '::' .  rand() . rand());
				
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<a href='#Gene_Plot_Columns_Modal' data-toggle='modal' class='nowrap'>(More Attributes)</a>
							</label>";
				echo "</div>";
			}
			
			
			if (array_size($APP_CONFIG['DB_Dictionary']['GeneCombined']['Gene_Expression_Options_Completed']) > 0){
				
				$modalID 	= 'Gene_Plot_Columns_Modal';
				$modalTitle = "<h4 class='modal-title'>Additional {$APP_MESSAGE['Gene Attributes']}</h4>";
				
				unset($modalBody);
				
				$modalBody	.= "<div class='row'>";
				foreach($APP_CONFIG['DB_Dictionary']['GeneCombined']['Gene_Expression_Options_Splitted_2'] as $remainder => $tempValue){
					
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

	

	
	
	
	if (array_size($APP_CONFIG['DB_Dictionary']['Comparisons']['Gene_Expression_Options']) > 0){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<div style='margin-top:20px;'>";
				echo "<strong>Comparison Attributes <span id='Comparison_Plot_Columns_Message' style='font-weight:normal;'></span>:</strong>";
			echo "</div>";
			
			
			foreach($APP_CONFIG['DB_Dictionary']['Comparisons']['Gene_Expression_Options'] as $currentColumn => $currentDetails){
				
				$randomID = md5($currentColumn . '::' .  rand());
				
				$class		= "Comparison_Plot_Columns_" . md5($currentColumn);
				
				$checked 	= '';
				
				if (!isset($dataArray['Comparison_Plot_Columns'])){
					if ($currentDetails['Default']){
						$checked = 'checked';	
					}
				} else {
					if (in_array($currentColumn, $dataArray['Comparison_Plot_Columns'])){
						$checked = 'checked';	
					}
				}
					
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<input type='checkbox' id='{$randomID}' class='form-check-input Comparison_Plot_Columns {$class}' sibling='{$class}' name='Comparison_Plot_Columns[]' value='{$currentColumn}' {$checked}/>
							{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title']}
					  	</label>";
				echo "</div>";
				
			}
			
			
			if (array_size($APP_CONFIG['DB_Dictionary']['Comparisons']['Gene_Expression_Options_Completed']) != array_size($APP_CONFIG['DB_Dictionary']['Comparisons']['Gene_Expression_Options'])){
				$randomID = md5($currentColumn . '::' .  rand() . rand());
				
				echo "<div class='form-check form-check-inline'>";
					echo "<label for='{$randomID}' class='form-check-label'>
							<a href='#Comparison_Plot_Columns_Modal' data-toggle='modal' class='nowrap'>(More Attributes)</a>
							</label>";
				echo "</div>";
			}
			
			
			if (array_size($APP_CONFIG['DB_Dictionary']['Comparisons']['Gene_Expression_Options_Completed']) > 0){
				
				$modalID 	= 'Comparison_Plot_Columns_Modal';
				$modalTitle = "<h4 class='modal-title'>Additional Comparison Attributes</h4>";
				
				unset($modalBody);
				
				$modalBody	.= "<div class='row'>";
				foreach($APP_CONFIG['DB_Dictionary']['Comparisons']['Gene_Expression_Options_Splitted_3'] as $remainder => $tempValue){
					
					$modalBody	.= "<div class='col-lg-4'>";
					foreach($tempValue as $currentColumn => $currentDetails){
							
						$randomID 	= md5($currentColumn . '::' .  rand());
						
						$class		= "Comparison_Plot_Columns_" . md5($currentColumn);
					
						$checked = '';
						
						if (array_size($dataArray) <= 0){
							if ($currentDetails['Default']){
								$checked = 'checked';	
							}
						} elseif (in_array($currentColumn, $dataArray['Comparison_Plot_Columns'])){
							$checked = 'checked';
						}
						
						
						$modalBody	.= "<div class='form-check'>";
							$modalBody .= "<label for='{$randomID}' class='form-check-label'>
											<input type='checkbox' id='{$randomID}' class='form-check-input Comparison_Plot_Columns {$class}' sibling='{$class}' name='Comparison_Plot_Columns[]' value='{$currentColumn}' {$checked}/>
											{$APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$currentColumn]['Title']}
											</label>";
						$modalBody 	.= "</div>";
						
						
					}
					$modalBody	.= "</div>";
					
				}
				$modalBody	.= "</div>";
				
				echo printModal($modalID, $modalTitle, $modalBody, '', '', 'modal-dialog-wide');
				
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
	
	if (true){
		echo "<div class='row' style='margin-top:20px;'>";
			echo "<input type='hidden' name='submit' value='1'/>";
			echo "<div class='form-group'>";
				echo "<button class='col-sm-offset-0 btn btn-primary' type='submit'>Submit</button>";
				
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
	
	$('.Comparison_Plot_Columns').change(function(){
		var currentValue = $(this).prop('checked');
		
		var currentClass = $(this).attr('sibling');
		
		$('.' + currentClass).prop('checked', currentValue);
		
		updateSelectedComparisonAttributes();
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
	updateSelectedComparisonAttributes();
	
	<?php if ($dataArray['submit'] || $loadFromSession){ ?>
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


function updateSelectedComparisonAttributes(){

	var checked = new Object();
	var currentValue = '';
	

	$('.Comparison_Plot_Columns').each(function() {
		if ($(this).prop('checked')){
			currentValue = $(this).val();
			checked[currentValue] = 1;
		}
	});
	
	var count = Object.keys(checked).length;
	
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#Comparison_Plot_Columns_Message').html(message);
	
	
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

</script>