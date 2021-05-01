<?php

if ($_GET['key'] != ''){
	$dataArray = getSQLCache($_GET['key']);
} elseif ($_GET['bxaf_save_to_cache_key'] != ''){
	$dataArray = getSQLCache($_GET['bxaf_save_to_cache_key']);
	$dataArray['ComparisonIDs'] = $dataArray['Input'];
}






echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";

	echo "<div id='main_form'>";
	if (true){
		echo "<div class='row'>";
		echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
			echo "<div style='margin-top:10px;'>";
				echo "<strong>Comparison IDs:</strong> 
						<a href='#comparisonListModal' data-toggle='modal'>" . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparisons</a>";
			echo "</div>";
			
			$values = $dataArray['ComparisonIDs'];
			
			if (array_size($values) > 0){
				$values = implode("\n", $values);	
			}
			
			
			$exampleMessage = 'Please enter one or more comparison ID, seperated by line break.';
			
			echo "<div style='margin-top:10px;'>";
				echo "<textarea class='form-control' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='{$exampleMessage}'>{$values}</textarea>";
			echo "</div>";
			
			$modalID 	= 'comparisonListModal';
			$modalTitle = 'Please select a comparison list you like to load:';
			$modalBody	= '';
			echo printModal($modalID, $modalTitle, $modalBody);
			
			
		echo "</div>";
		echo "</div>";
	}
	

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
	
	
	if (true){
				
		
		
		if (isset($dataArray['category'])){
			$selected = $dataArray['category'];
		} else {
			$selected = 'PAGE_List';
		}
		
		unset($values);
		$values['PAGE']		= $APP_CONFIG['APP']['PAGE'];
		$values['Homer'] 	= $APP_CONFIG['APP']['Homer'];
		
		natksort($values['Homer']);
		
		echo "<div style='margin-top:12px;'>";
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<div class='form-group row col-lg-12'>";
				echo "<label for='category'><strong>Set:</strong></label>";
				echo "<div>";
				echo "<select class='form-control' id='category' name='category' style='margin-left:15px;'/>";
				
					foreach($values as $category => $tempValues){
						
						echo "<optgroup label='{$category}'>";
				
							foreach($tempValues as $tempKey => $tempValue){
								
								unset($checked);
								if ($selected == $tempKey){
									$checked = "selected='selected'";
								}
								echo "<option value='{$tempKey}' {$checked}>{$tempValue}</option>";
							}
							
							
						echo "</optgroup>";
							
						}
						
				echo "</select>";
				echo "</div>";
			echo "</div>";	
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}
	
	
	if (true){
		
		
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
		
			echo "<div style='margin-top:12px;'>";
				echo "<strong>Data Filter:</strong>";
			echo "</div>";
			
			echo "<div style='margin-left:15px;'>";
			
				echo "<div style='margin-top:5px;'>";
				echo "<label>";
				
					unset($checked);
					if ($dataArray['filter_by_value_enable']){
						$checked = 'checked';	
					}
				
					echo "<input type='checkbox' name='filter_by_value_enable' class='filter_by_value_enable' id='filter_by_value_enable' value='1' {$checked}/> Enable ";
                  
					echo "<span class='filter_by_PAGE'>z-score lower limit</span>";
					echo "<span class='filter_by_Homer startHidden'>p-value upper limit</span>";
					echo "<br/>";
					
					
					echo "<span id='filter_by_value_section' class='startHidden'>";
					echo "<span class='filter_by_PAGE'>|z-score| &ge; </span>";
					echo "<span class='filter_by_Homer startHidden'>log<sub>10</sub>(p-value) &le; </span>";
					echo "<input type='text' name='filter_by_value' id='filter_by_value' value='{$dataArray['filter_by_value']}'/>";
					echo "</span>";
                  
				echo "</label>";
				echo "</div>";
			
			
			echo "</div>";
		
		echo "</div>";
		echo "</div>";
	}

	

	if (true){
		echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
		
			echo "<div style='margin-top:12px;'>";
				echo "<strong>Display Option:</strong>";
			echo "</div>";
			
			
			if (array_size($BXAF_CONFIG['COMPARISON_INFO']['Sequence']) > 0){
				echo "<div style='margin-left:15px;'>";
				
					echo "<div style='margin-top:5px;'>";
					echo "<label>";
					
						unset($checked);
						if (array_size($dataArray) > 0){
							if ($dataArray['display_comparison_info']){
								$checked = 'checked';
							}
						} else {
							$checked = '';
						}
					
						echo "<input type='checkbox' name='display_comparison_info' class='display_comparison_info' id='display_comparison_info' value='1' {$checked}/> Use comparison info instead of comparison ID ";
					 
					echo "</label>";
					echo "</div>";
				echo "</div>";
			}
			
			
			
			echo "<div style='margin-left:15px;'>";
			
				echo "<div style='margin-top:5px;'>";
                echo "<label>";
                	echo "Display top ";
					
					echo "<select name='filter_by_top' id='filter_by_top'>";
					
					
						if (isset($dataArray['filter_by_top'])){
							$selected = $dataArray['filter_by_top'];
						} else {
							$selected = '20';
						}
						
						$values = array(10, 20, 50, 100, 200);
					
						foreach($values as $tempKey => $tempValue){
							
							unset($checked);
							if ($selected == $tempValue){
								$checked = "selected='selected'";
							}
							echo "<option value='{$tempValue}' {$checked}>{$tempValue}</option>";
						}
					
					
					
					echo "</select>";

					
					echo "<span class='filter_by_PAGE'> {$APP_MESSAGE['gene']} sets with highest</span>";
					echo "<span class='filter_by_Homer startHidden'> pathways with smallest</span>";

					echo "&nbsp;";
                  
                  	echo "<span class='filter_by_PAGE'>|z-score|</span>";
					echo "<span class='filter_by_Homer' style='display:none;'>log<sub>10</sub>(p-value)</span>";
                echo "</label>";
                echo "</div>";
		
			
			echo "</div>";
		
		echo "</div>";
		echo "</div>";
		
		
	}

	
	if (true){
		echo "<div>";
			echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
				echo "<div style='margin-top:20px;'>";
					echo "<button class='btn btn-success getPathwayGeneSetTrigger' type='button'>" . printFontAwesomeIcon('fas fa-search fa-fw') . "Get ";
						echo "<span class='filter_by_PAGE'>Gene Sets</span>";
						echo "<span class='filter_by_Homer startHidden'>Pathways</span>";
					echo "</button>";
					
					$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a comparison ID and try again.";
					echo "<div id='missingComparisonIDSection' class='startHidden form-text'>" . getAlerts($message, 'danger', 'col-lg-10 col-sm-12') . "</div>";
					
				echo "</div>";
			echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	echo "</div>";
	
	echo "<div id='PathwayGeneSetSection' class='PathwayGeneSetSection startHidden' style='margin-bottom:20px;'></div>";
	echo "<div id='PathwayGeneSetSectionBusySection' class='startHidden col-lg-offset-1'>" . printFontAwesomeIcon('fas fa-spinner fa-spin fa-lg'). "</div>";
	

	echo "<div class='form-group startHidden' id='submitButtonSection'>";
		echo "<br/>";
		echo "<button class='xcol-sm-offset-1 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-chart-bar') . " Plot</button>";
		echo "&nbsp;<a href='{$PAGE['URL']}'>Reset</a>";
		echo "&nbsp;<span class='busySection startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
	echo "</div>";
	
	
	echo "<input type='hidden' name='URL' value='{$PAGE['URL']}'/>";
	echo "<input type='hidden' name='submit' value='1'/>";

echo "</form>";

echo "<div id='feedbackSection' class='startHidden'></div>";

?>


<style>
.modebar-btn--logo{
	display:none;	
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
	
	
	$('#main_form').on('change', 'textarea', function(){
		
		$('#PathwayGeneSetSection').hide();
		$('#PathwayGeneSetSection').empty();
		
		$('#submitButtonSection').hide();
		
		$('#feedbackSection').hide();
		$('#feedbackSection').empty();
		
	});
	
	$('#main_form').on('change', 'input', function(){
		
		$('#PathwayGeneSetSection').hide();
		$('#PathwayGeneSetSection').empty();
		
		$('#submitButtonSection').hide();
		
		$('#feedbackSection').hide();
		$('#feedbackSection').empty();
		
	});
	
	$('#main_form').on('change', 'select', function(){
		
		$('#PathwayGeneSetSection').hide();
		$('#PathwayGeneSetSection').empty();
		
		$('#submitButtonSection').hide();
		
		$('#feedbackSection').hide();
		$('#feedbackSection').empty();
		
	});
	
	
	$('#PathwayGeneSetSection').on('change', 'textarea', function(){
		
		$('#feedbackSection').hide();
		$('#feedbackSection').empty();
		
	});
	
	
	$('.filter_by_value_enable').change(function(){
		if ($('#filter_by_value_enable').prop('checked')){
			$('#filter_by_value_section').show();
		} else {
			$('#filter_by_value_section').hide();
		}
	});
	
	
	
	
	$('.getPathwayGeneSetTrigger').click(function(){
		loadDataFilterSection();
	});
	
	
	$('#form_application').on('change', 'input', function(){
		$('#feedbackSection').empty();
	});
	
	$(document).on('change', '#category', function(){
		
		var currentValue = $(this).val();
		
		if (currentValue == 'PAGE_List'){
			$('.filter_by_PAGE').show();
			$('.filter_by_Homer').hide();
		} else {
			$('.filter_by_PAGE').hide();
			$('.filter_by_Homer').show();
		}
		
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
	
	
	<?php if (array_size($dataArray) > 0){ ?>
		$('#category').change();
		$('#filter_by_value_enable').change();
		loadDataFilterSection();
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


function loadDataFilterSection(){
	
	var ComparisonIDs 	= $('#ComparisonIDs').val();
	ComparisonIDs 		= $.trim(ComparisonIDs);
	$('#missingComparisonIDSection').hide();
	
	$('#feedbackSection').empty();
	$('#PathwayGeneSetSection').empty();
	$('.PathwayGeneSetSection').hide();
	
	
	var data = new Object();
	
	data['ComparisonIDs'] 	= ComparisonIDs;
	data['category'] 		= $('#category').val();
	
	if ($('#filter_by_value_enable').prop('checked')){
		data['filter_by_value_enable'] 	= 1;
		data['filter_by_value'] = $('#filter_by_value').val();
	} else {
		data['filter_by_value_enable'] = 0;	
	}

	data['filter_by_top'] 	= $('#filter_by_top').val();
	
	
	
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
	
	
	if (ComparisonIDs != ''){
		
		$('#missingComparisonIDSection').hide();
		$('#PathwayGeneSetSectionBusySection').show();
		
		$.ajax({
			type: 'POST',
			url: 'app_pathway_heatmap_ajax.php',
			data: data,
			success: function(responseText){
				responseText = $.trim(responseText);
				
				if (responseText != ''){
					$('#PathwayGeneSetSectionBusySection').hide();
					
					$('#PathwayGeneSetSection').html(responseText);
					$('.PathwayGeneSetSection').show();
					
				}
			}
		});
	} else {
		$('#missingComparisonIDSection').show();
	}
}


</script>