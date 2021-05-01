<?php

$dataArray = getSQLCache($_GET['key']);

$allColumns = getTableColumnNamesTitle($APP_CONFIG['APP']['List_Category'][$category]['Table'], 'HideFromSearchSelect');

$sessionID	= getUniqueID();



echo "<div class='row'>";
	echo "<div class='col-12'>";
		unset($actions);
		$actions[] = "<a href='javascript:void(0);' class='advancedSearchTrigger'>" . printFontAwesomeIcon('fas fa-search') . " Advanced Search</a>";
		
		$actions[] = "<a href='app_record_browse.php?Category={$category}&search=0'>" . printFontAwesomeIcon('fas fa-list') . " Browse All Records</a>";
		
		$actions[] = "<a href='{$APP_CONFIG['APP']['List_Category'][$category]['Preference_URL']}' target='_blank'>" . printFontAwesomeIcon('fas fa-cog') . " Display Preferences</a>";
		
		echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
	echo "</div>";
echo "</div>";
		
$rowCount = array_size($dataArray['Search']);

unset($class);

if ($_GET['search']){

	$rowCount = 1;
	$class = '';
	
	unset($dataArray);
	
	$currentIndex = 1;
	$dataArray['Search'][$currentIndex]['Field'] 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Default'];
	$dataArray['Search'][$currentIndex]['Operator'] = 1;
	$dataArray['Search'][$currentIndex]['Value'] 	= "";
	$dataArray['Search'][$currentIndex]['Logic'] 	= '';
	
	$dataArray['POST']['data_source'][] = 'private';
	$dataArray['POST']['data_source'][] = 'public';
	$submit = 0;
	
} elseif ($rowCount == 0){
	//Browse
	$rowCount = 1;
	$class = 'startHidden';
	
	unset($dataArray);
	
	$currentIndex = 1;
	$dataArray['Search'][$currentIndex]['Field'] 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Default'];
	$dataArray['Search'][$currentIndex]['Operator'] = 1;
	$dataArray['Search'][$currentIndex]['Value'] 	= "";
	$dataArray['Search'][$currentIndex]['Logic'] 	= '';
	
	$dataArray['POST']['data_source'][] = 'private';
	$dataArray['POST']['data_source'][] = 'public';
	$submit = 1;
} else {
	$submit = 1;
}


if ($_GET['hide'] == 1){
	$class = 'startHidden';	
}





echo "<div id='advancedSearchSection' class='{$class}'>";
echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";


	
	if (($category != 'Gene') && has_internal_data()){
		
		$has_internal_data = true;
		echo "<div id='data_source_section'>";
		echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<div class='form-group row'>";
			
				echo "<label class='col-1 '>Data Source:</label>";
				
				echo "<div class='col-5'>";
					echo internal_data_print_form_html($dataArray['POST']);
					echo internal_data_print_modal($dataArray['POST']);
				echo "</div>";
			
			echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}


	if (true){
		echo "<div class='row'>";
			echo "<div class='col-12'>";
				foreach($dataArray['Search'] as $currentSearchCount => $currentSearchInfo){
				echo "<div class='form-group row' id='searchRow_{$currentSearchCount}'>";
					
					if ($currentSearchCount == 1){
						echo "<label class='col-1 col-form-label'>Search:</label>";
					} else {
						
						$name 				= "Logic_{$currentSearchCount}";
						$value 				= $currentSearchInfo['Logic'];
						$placeHolderText 	= '';
						unset($checkAnd, $checkOr);
						
						if ($value == 'or'){
							$checkOr = 'checked';
						} else {
							$checkAnd = 'checked';
						}
						
						echo "<div class='col-1'>";
							echo "<div class='form-check form-check-inline'>";
								
								echo "<input type='radio' class='form-check-input' name='Logic_{$currentSearchCount}' value='and' {$checkAnd}/>";
								echo "<label class='form-check-label'> And</label>";

								echo "&nbsp; &nbsp;";
								
								echo "<input type='radio' class='form-check-input' name='Logic_{$currentSearchCount}' value='or' {$checkOr}/>";
								echo "<label class='form-check-label'> Or</label>";
							echo "</div>";
						echo "</div>";
					}
					
					
					if (true){
						$name 				= "Field_{$currentSearchCount}";
						$value 				= $currentSearchInfo['Field'];
						$placeHolderText 	= '';
						
						echo "<div class='col-2'>";
							echo "<div class='form-group'>";
								echo "<select class='form-control' name='{$name}'>";
									echo "<option value=''></option>";
								
									foreach($allColumns as $currentSQL => $currentTitle){
										
										unset($selected);
										
										if ($currentSQL == $value){
											$selected = 'selected';	
										}
									
										echo "<option value='{$currentSQL}' {$selected}>{$currentTitle}</option>";
										
									}
								echo "</select>";
							echo "</div>";
						echo "</div>";
					}
					
					
					if (true){
						$name 				= "Operator_{$currentSearchCount}";
						$value 				= $currentSearchInfo['Operator'];
						$placeHolderText 	= '';
						
						echo "<div class='col-1'>";
							echo "<div class='form-group'>";
								echo "<select class='form-control' name='{$name}'>";
									foreach($APP_CONFIG['APP']['Search']['Operator'] as $tempKey => $tempValue){
										unset($selected);
										
										if ($tempKey == $value){
											$selected = 'selected';	
										}
										echo "<option value='{$tempKey}' {$selected}>{$tempValue}</option>";
										
									}
								echo "</select>";
							echo "</div>";
						echo "</div>";
					}
					
					
					
					if (true){
						$name 				= "Value_{$currentSearchCount}";
						$value 				= htmlspecialchars($currentSearchInfo['Value'], ENT_QUOTES);
						$placeHolderText 	= '';
						
						echo "<div class='col-3'>";
							echo "<div class='form-group'>";
								echo "<input class='form-control' name='{$name}' {$placeHolderText} value='{$value}'/>";
							echo "</div>";
						echo "</div>";
					}
					
					
					if ($currentSearchCount == 1){
						echo "<label class='col-1 col-form-label'>";
							echo "<a href='javascript:void(0);' class='addSearchRowTrigger'>" . printFontAwesomeIcon('fas fa-plus') . " Add</a>";
						echo "</label>";
					} else {
						echo "<label class='col-1 col-form-label'>";
							echo "<a href='javascript:void(0);' class='deleteSearchRowTrigger' rowid='searchRow_{$currentSearchCount}'>" . printFontAwesomeIcon('far fa-trash-alt') . " Remove</a>";
						echo "</label>";	
					}
					
		
				echo "</div>";
		
			}
				echo "<div id='searchAppendArea'></div>";
			echo "</div>";
		echo "</div>";
	}



	if (true){
		echo "<div class='form-group row'>";
			echo "<div class='col-6'>";		
				echo "<a href='javascript:void(0);' class='addSearchRowTrigger'>" . printFontAwesomeIcon('fas fa-plus') . " Add Search Condition</a>";
			echo "</div>";
		echo "</div>";		
	}


	if (true){
		echo "<div class='form-group row'>";
			echo "<div class='col-6'>";
				echo "<input type='hidden' name='rowCount' id='rowCount' value='{$rowCount}'/>";
				echo "<input type='hidden' name='fast' id='fast' value='1'/>";
				echo "<input type='hidden' name='sessionID' value='{$sessionID}'/>";
				echo "<input type='hidden' name='Category' value='{$category}'/>";
				echo "<input type='hidden' name='URL' value='app_record_browse.php'/>";
				echo "<input type='hidden' name='bookmark' value='1'/>";
				echo "<input type='hidden' name='searchKeyword' id='searchKeyword' value='{$_GET['searchKeyword']}'/>";
				echo "<button id='submitButton' class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('fas fa-search') . " Search</button>";
				echo "&nbsp; &nbsp;";
				echo "<a href='app_record_browse.php?Category={$category}&search=1'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
			echo "</div>";
		echo "</div>";
	}
	

echo "</form>";				

echo "</div>";

echo "<div id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). " Loading...</div>";
echo "<div id='feedbackSection' class='startHidden'></div>";
	

?>

<style>

<?php if ($category == 'Dataset'){ ?>
	#data_source_section{
		display:none;	
	}
<?php } ?>
</style>

<script type="text/javascript">

$(document).ready(function(){
	
	$('.advancedSearchTrigger').click(function(){
		$('#advancedSearchSection').toggle();
	});
	
	
	
	$('.addSearchRowTrigger').click(function(){
		var text = '';
		var rowCount = $('#rowCount').val();
		rowCount++;
		$('#rowCount').val(rowCount);
		
		
		text += "<div class='form-group row' id='searchRow_" + rowCount + "'>";
		
		if (true){
			text += "<div class='col-1'>";
				text += "<div class='form-check form-check-inline'>";
					text += "<input type='radio' class='form-check-input' name='Logic_" + rowCount + "' value='and' checked/>";
					text += "<label class='form-check-label'> And</label>";
					
					text += "&nbsp; &nbsp;";
					
					text += "<input type='radio' class='form-check-input' name='Logic_" + rowCount + "' value='or'/>";
					text += "<label class='form-check-label'> Or</label>";
				text += "</div>";
			text += "</div>";
		}
		
		if (true){
			
			text += "<div class='col-2'>";
				text += "<div class='form-group'>";
					text += "<select class='form-control' name='Field_" + rowCount + "'>";
						text += "<option value=''></option>";
					
						<?php
							foreach($allColumns as $currentSQL => $currentTitle){
 	 							unset($selected);
								
								if ($currentSQL == $APP_CONFIG['APP']['List_Category'][$category]['Column_Default']){
									$selected = 'selected';
								}
								
						?>
							
							text += "<?php echo "<option value='{$currentSQL}' {$selected}>{$currentTitle}</option>"; ?>";
							
						<?php } ?>
					text += "</select>";
				text += "</div>";
			text += "</div>";	
		}
		
		
		
		
		if (true){
					
			text += "<div class='col-1'>";
				text += "<div class='form-group'>";
					text += "<select class='form-control' name='Operator_" + rowCount + "'>";
					<?php
						foreach($APP_CONFIG['APP']['Search']['Operator'] as $tempKey => $tempValue){
							unset($selected);
							
							if ($tempKey == 1){
								$selected = 'selected';	
							}
					?>
							text += "<?php echo "<option value='{$tempKey}' {$selected}>{$tempValue}</option>"; ?>";
							
					<?php } ?>
					text += "</select>";
				text += "</div>";
			text += "</div>";
		}
		
		
		if (true){
		
			text += "<div class='col-3'>";
				text += "<div class='form-group'>";
					text += "<input class='form-control' name='Value_" + rowCount + "'/>";
				text += "</div>";
			text += "</div>";	
			
		}
		
		
		if (true){
		
			text += "<label class='col-1 col-form-label'>";
				text += "<a href='javascript:void(0);' class='deleteSearchRowTrigger' rowid='searchRow_" + rowCount + "'><?php echo printFontAwesomeIcon('far fa-trash-alt'); ?> Remove</a>";
			text += "</label>";
			
		}
		
		text += "</div>";
		
		
		
		$('#searchAppendArea').append(text);
		
		
		
		
		
	});
	
	
	$(document).on('click', '.deleteSearchRowTrigger', function(){
		var rowID = $(this).attr('rowid');
		$('#' + rowID).empty();
		$('#' + rowID).hide();
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
	
		data['Category'] = '<?php echo $category; ?>';
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
	
	
	<?php if ($category == 'Project'){ ?>
	$(document).on('click', '.projectToSampleTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=5',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#projectToSample_Missing_Result').show();	
					}
				}
			});
		} else {
			
			$('#projectToSample_Missing_Record').show();	
		}
	});
	
	
	$(document).on('click', '.projectToDashboardTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=9',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						window.location = responseText;
					} else {
						$('#projectToDashboard_Missing_Result').show();	
					}
				}
			});
		} else {
			
			$('#projectToDashboard_Missing_Record').show();	
		}
	});
	<?php } ?>
	
	
	<?php if ($category == 'Comparison'){ ?>
	$(document).on('click', '.comparisonToSampleTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=6',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#comparisonToSample_Missing_Result').show();	
					}
				}
			});
		} else {
			
			$('#comparisonToSample_Missing_Record').show();	
		}
	});
	
	
	$(document).on('click', '.comparisonToSignificantTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=7',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#comparisonToSignificant_Missing_Result').show();	
					}
				}
			});
		} else {
			$('#comparisonToSignificant_Missing_Record').show();	
		}
	});
	
	
	
	$(document).on('click', '.comparisonToDashboardTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=8',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#comparisonToDashboard_Missing_Result').show();	
					}
				}
			});
		} else {
			
			$('#comparisonToDashboard_Missing_Record').show();	
		}
	});
	
	<?php } ?>
	
	
	<?php if ($category == 'Sample'){ ?>
	$(document).on('click', '.sampleToDashboardTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=10',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#sampleToDashboard_Missing_Result').show();	
					}
				}
			});
		} else {
			
			$('#sampleToDashboard_Missing_Record').show();	
		}
	});
	
	<?php } ?>
	
	<?php if ($APP_CONFIG['APP']['List_Category'][$category]['Edit'] != ''){ ?>
	$(document).on('click', '.updateRecordTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=13',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#updateRecord_Missing_Result').show();	
					}
				}
			});
		} else {
			$('#updateRecord_Missing_Record').show();	
		}
	});
	<?php } ?>
	
	
	$('#form_application').ajaxForm({ 
        target: '#feedbackSection',
        url: 'app_record_browse_component_exe.php',
        type: 'post',
		beforeSubmit: beforeSubmit,
        success: showResponse
    });
	

	<?php if ($submit){ ?>
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

		$('#searchOption_1_Section').hide();
		
		
	} else {
		
		$('#searchOption_1_Section').show();
		$('#data_source_private_section').hide();
	}
}

<?php } ?>
function beforeSubmit() {
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