<?php

$dataArray = getSQLCache($_GET['key']);

if (array_size($dataArray) <= 0){

	if ($_GET['comparisonList'] > 0){
		
		$comparisonListArray = get_list_record_by_list_id_and_category($_GET['comparisonList'], 'Comparison');
		
		if (array_size($comparisonListArray) > 0){
			$comparisonListID = $_GET['comparisonList'];
		}
		
	}
	
}



$currentTable = 'Samples';
echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";


	if (true){
		unset($value);
		if ($dataArray['KEGG_Identifier'] != ''){
			$value = $dataArray['KEGG_Identifier'];
		} elseif ($_GET['KEGG_Identifier'] != ''){
			$value = $_GET['KEGG_Identifier'];	
		} elseif ($_GET['KEGG'] != ''){
			
			$guessKEGG = guessKEGG($_GET['KEGG']);	
			
			if ($guessKEGG['results'] == 1){
				$value = $guessKEGG['ID'];
			}
		}
		
		$values = getKEGGMenu();
		
		echo "<div class='row'>";
		echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
			echo "<div style='margin-top:10px;'>";
				echo "<strong>KEGG ID:</strong>";
				echo "<p class='form-text'>e.g., Wnt Signaling Pathway, hsa04310</p>";
			echo "</div>";
			
			echo "<div class='input-group' style='margin-top:10px;'>";
				echo "<select class='form-control selectpicker' data-live-search='true' name='KEGG_Identifier' id='KEGG_Identifier' title='Please select a KEGG ID'>";
				
					echo "<option></option>";
				
					foreach($values as $tempKey => $tempValue){
						
						unset($selected);
						
						if ($tempKey === $value){
							$selected = 'selected';	
						}
						
						$tempValue = ucwords($tempValue);
						
						echo "<option value=\"{$tempKey}\" data-tokens=\"{$tempKey} {$tempValue}\" {$selected}>{$tempValue} ($tempKey)</option>";
						
						
					}
				echo "</select>";
			echo "</div>";
			
		echo "</div>";
		echo "</div>";
	}
	

	if (true){
		echo "<div class='row'>";
		echo "<div class='col-lg-5 col-md-8 col-sm-12 col-xs-12'>";
			echo "<div style='margin-top:10px;'>";
				echo "<strong>Comparison IDs:</strong> 
						<a href='#comparisonListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparison IDs</a>";
			echo "</div>";
	
			unset($values);
			if (isset($dataArray['ComparisonIDs'])){
				$values = implode("\n", $dataArray['ComparisonIDs']);
			} elseif ($_GET['ComparisonIndex'] != ''){
				
				$values = getComparisonIDFromIndex($_GET['ComparisonIndex']);
				
				
				
				if ($guessKEGG['results'] == 1){
					$submit = 1;	
				}
			} else if ($_GET['ComparisonIDs'] != ''){
				$values = $_GET['ComparisonIDs'];
				
				if ($guessKEGG['results'] == 1){
					$submit = 1;	
				}
				
			}
			echo "<div style='margin-top:10px;'>";
				echo "<textarea class='form-control inputForm' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='Please enter two or more comparison IDs, seperated by line break.'>{$values}</textarea>";
			echo "</div>";
			
			$modalID 	= 'comparisonListModal';
			$modalTitle = 'Please select a comparison list you like to load:';
			$modalBody	= '';
			echo printModal($modalID, $modalTitle, $modalBody, 'Select');
			
			
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
				echo "<strong>Source of the Comparison IDs:</strong>";
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
		unset($value);
		if ($dataArray['Visualization'] != ''){
			$value = $dataArray['Visualization'];
		} elseif ($_GET['Visualization'] != ''){
			$value = $_GET['Visualization'];	
		}


		unset($values);
		$values[1] = 'Gradient Blue-White-Red (-1,0,1)';
		$values[2] = 'Gradient Blue-White-Red (-2,0,2)';
		$values[3] = 'Gradient Blue-White-Red (-3,0,3)';


		$value = intval($value);		
		if (!isset($values[$value])){
			$value = 1;
		}
		

		
		echo "<div class='row'>";
		echo "<div class='col-lg-3 col-md-8 col-sm-12 col-xs-12'>";
			echo "<div style='margin-top:10px;'>";
				echo "<strong>Visualization:</strong>";
			echo "</div>";
			
			echo "<div class='input-group' style='margin-top:10px;'>";
				echo "<select class='form-control' name='Visualization' id='Visualization'>";
				
					foreach($values as $tempKey => $tempValue){
						
						unset($selected);
						
						if ($tempKey === $value){
							
							$selected = 'selected';	
						}
						
						echo "<option value='{$tempKey}' {$selected}>{$tempValue}</option>";
					}
				echo "</select>";
			echo "</div>";
			
		echo "</div>";
		echo "</div>";
	}
	
	
	
	echo "<div class='form-group'>";
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

<script type="text/javascript">

$(document).ready(function(){
	$('#form_application').ajaxForm({ 
        target: '#feedbackSection',
        url: '<?php echo $PAGE['EXE']; ?>',
        type: 'post',
		beforeSubmit: beforeSubmit,
        success: showResponse
    });
	
	
	$('#form_application').on('change', 'input', function(){
		$('#feedbackSection').empty();
	});
	
	$('#form_application').on('change', 'select', function(){
		$('#feedbackSection').empty();
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


	$(document).on('click', '#comparisonMissingInfoTrigger', function(){
		$('#comparisonMissingInfo').toggle();
	});
	
	$(document).on('click', '#summaryTrigger', function(){
		$('#summarySection').toggle();
	});
	

	
	<?php if ((array_size($dataArray) > 0) || ($submit == 1)){ ?>
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


</script>