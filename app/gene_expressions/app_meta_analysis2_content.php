<?php

$dataArray = getSQLCache($_GET['key']);

if (true){
echo "<div xclass='row'>";
	echo "<form id='form_application1' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";

		if (true){
			unset($wizard);
			$wizard[1]['Icon'] 		= printFontAwesomeIcon('far fa-copy');
			$wizard[1]['Title']		= 'Select Comparisons';
			$wizard[1]['State']		= 1;

			$wizard[2]['Icon'] 		= printFontAwesomeIcon('fas fa-list-ol');
			$wizard[2]['Title']		= 'Edit Samples';
			$wizard[2]['State']		= 0;
			
			$wizard[3]['Icon'] 		= printFontAwesomeIcon('far fa-chart-bar');
			$wizard[3]['Title']		= 'Review Results';
			$wizard[3]['State']		= 0;
			
			echo "<div class='form-group row'>";
				echo printWizard($wizard);
			echo "</div>";
		}

		if (true){
			echo "<div class='form-group row'>";
				echo "<div class='col-12'>";
					echo "<h2 class='pt-3'>1. {$wizard[1]['Title']}</h2>";
					echo "<hr/>";
				echo "</div>";
			echo "</div>";
		}
		
				
				
				
		if (true){
			echo "<div class='row'>";
				echo "<div class='col-lg-3 col-md-5 col-sm-12 col-xs-12'>";
					echo "<div style='margin-top:10px;'>";
						echo "<strong>Meta Analysis Name:</strong>";
					echo "</div>";
					
					echo "<div class='input-group' style='margin-top:10px; margin-bottom:20px;'>";
						echo "<input type='text' name='name' id='name' class='form-control ' value='' placeholder=''/>";
					echo "</div>";
					
				echo "</div>";
			echo "</div>";
		}
		
		
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<div class='col-lg-12'>";
					echo "<div style='margin-top:10px;'>";
						echo "<strong>Comparison IDs:</strong> 
								<a href='#comparisonListModal' data-toggle='modal'>"  . printFontAwesomeIcon('fas fa-shopping-cart') . " Load Saved Comparison IDs</a>";
					echo "</div>";
					
					if (isset($dataArray['ComparisonIDs'])){
						$values = implode("\n", $dataArray['ComparisonIDs']);
					}
					echo "<div style='margin-top:10px;'>";
						echo "<textarea class='form-control' rows='8' name='ComparisonIDs' id='ComparisonIDs' placeholder='Please enter one or more comparison IDs, seperated by line break.'>{$values}</textarea>";
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
			
			echo "<div class='form-group row'>";
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
			
			
			echo "<div class='form-group row'>";
			echo "<div class='col-lg-12'>";
			
				echo "<div style='margin-top:12px;'>";
					echo "<strong>Options:</strong>";
				echo "</div>";
				
				echo "<div style='margin-left:0px;'>";
				
					echo "<div style='margin-top:5px;'>";
					echo "<label>";
					
						unset($checked);
						if ($dataArray['rank_product_analysis']){
							$checked = 'checked';
						} else {
							$checked = 'checked';
						}
					
						echo "<input type='checkbox' name='rank_product_analysis' id='rank_product_analysis' value='1' {$checked}/> Perform Rank Product Analysis";
						echo "<br/>";
					  
					echo "</label>";
					echo "</div>";
				
				
				echo "</div>";
			
			echo "</div>";
			echo "</div>";
		}
	
		
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<div class='col-6'>";
					echo "<input type='hidden' name='sessionID' value='" . getUniqueID() . "'/>";
					echo "<button id='submitButton' class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('fas fa-arrow-circle-right') . " Continue</button>";
					echo "&nbsp;&nbsp;<a href='{$PAGE['URL']}'>" . printFontAwesomeIcon('fas fa-sync-alt') . ' Reset</a>';
					echo "&nbsp;<span id='busySection1' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
				echo "</div>";
			echo "</div>";
		}
		
	echo "</form>";

echo "</div>";
}

if (true){
	echo "<div xclass='row'>";
		echo "<form id='form_application2' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";
			echo "<div id='form_application_content2' class='startHidden feedbackSection'></div>";
		echo "</form>";
	echo "</div>";
}


if (true){
	echo "<div xclass='row'>";
		echo "<form id='form_application3' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";
			echo "<div id='form_application_content3' class='startHidden feedbackSection'></div>";
		echo "</form>";
	echo "</div>";
}


?>

<style>
.comparisonGroup{
	margin-top:10px;
	margin-bottom:10px;
	border:1px solid #999;
	padding:5px;
}
</style>

<script type="text/javascript">

$(document).ready(function(){
	$('#form_application1').ajaxForm({ 
        target: '#form_application_content2',
        url: 'app_meta_analysis2_exe1.php',
        type: 'post',
		beforeSubmit: beforeSubmit1,
        success: showResponse1
    });
	
	$('#form_application2').ajaxForm({ 
        target: '#form_application_content3',
        url: 'app_meta_analysis2_exe2.php',
        type: 'post',
		beforeSubmit: beforeSubmit2,
        success: showResponse2
    });
	
	
	
	$(document).on('click', '.showForm1Trigger', function(){
		$('#form_application_content2').empty();
		$('#form_application_content2').hide();
		
		$('#form_application_content3').empty();
		$('#form_application_content3').hide();
		
		$('#form_application1').show();
	});
	
	$(document).on('click', '.showForm2Trigger', function(){
		
		$('#form_application1').hide();
		$('#form_application_content1').hide();
		
		$('#form_application_content3').empty();
		$('#form_application_content3').hide();
		
		$('#form_application2').show();
		$('#form_application_content2').show();
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
	


	<?php if ($dataArray['submit']){ ?>
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


function beforeSubmit1() {
	$('#form_application_content2').empty();
	$('#form_application_content2').hide();
	$('#busySection1').show();
	return true;
}


function showResponse1(responseText, statusText) {
	responseText = $.trim(responseText);

	$('#busySection1').hide();
	$('#form_application_content2').html(responseText);
	$('#form_application_content2').show();
	return true;

}


function beforeSubmit2() {
	$('#form_application_content3').empty();
	$('#form_application_content3').hide();
	$('#busySection2').show();
	return true;
}


function showResponse2(responseText, statusText) {
	responseText = $.trim(responseText);

	$('#busySection2').hide();
	$('#form_application_content3').html(responseText);
	$('#form_application_content3').show();
	
	$('html,body').animate({
		scrollTop: $('#form_application_content3').offset().top
	});
	
	
	return true;

}




</script>