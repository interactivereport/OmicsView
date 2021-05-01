<?php

$cacheArray = getSQLCache($_GET['key']);


echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";


	if (true){
		echo "<div class='row'>";
			echo "<div class='col-lg-6'>";		
			
			if (true){
				$name 				= 'Name';
				$displayName		= 'Name';
				$value 				= $dataArray[$name];
				$placeHolderText 	= 'A unique name of the list';
				
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					echo "<div class='col-8' id='Name_Group'>";
						echo "<input type='text' class='form-control' id='{$name}' name='{$name}' value='{$value}' required>";
						echo "<small class='form-text text-muted'>{$placeHolderText}</small>";
						echo "<div class='form-control-feedback'>
								<span id='Name_valid' class='startHidden Name_Check_Message'>The name is available.</span>
								<span id='Name_invalid' class='startHidden Name_Check_Message'>The name is not available.</span>
							</div>";
					echo "</div>";
				echo "</div>";
			}

			
			if (true){
				$name 				= 'Input';
				$displayName		= $APP_CONFIG['APP']['List_Category'][$category]['Title'];
				$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$dataArray['Category']]['Column_Human'];
				$value 				= implode("\n", $dataArray['Items'][$sql_column_human]);
				
				
				if (!isset($dataArray['Items'][$sql_column_human])){
					
					if (isset($cacheArray[$name])){
						$value = implode("\n", $cacheArray[$name]);
						$needValidate = true;
					} elseif (array_size($_SESSION['List'][$_GET['Session']]) > 0){
						
						$_SESSION['List'][$_GET['Session']] = array_clean($_SESSION['List'][$_GET['Session']]);
						
						$value = implode("\n", $_SESSION['List'][$_GET['Session']]);
						$needValidate = true;
					} elseif ($_GET['identifier'] != ''){
					
						$value = $_GET['identifier'];
						$needValidate = true;
					} elseif ($_GET['index'] != ''){
						$temp = get_one_record_by_id($_GET['Category'], $_GET['index']);
						$value = $temp[($APP_CONFIG['APP']['List_Category'][$_GET['Category']]['Column_Human'])];
						$needValidate = true;
					}
				}
				
				
				$placeHolderText 	= $APP_CONFIG['APP']['List_Category'][$category]['Example_Message'];
				
				
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					echo "<div class='col-8'>";
						echo "<textarea class='form-control' id='{$name}' name='{$name}' rows='10' placeholder='{$placeHolderText}' required>{$value}</textarea>";
						echo "<p class='form-text' id='inputFeedbackSection'></p>";
						echo "<div id='inputFeedbackSectionBusySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
					echo "</div>";
				echo "</div>";
			}
			
			
			if (true){
				$name 				= 'Notes';
				$displayName		= 'Description';
				$value 				= $dataArray[$name];
				$placeHolderText 	= '';
				
				echo "<div class='form-group row'>";
					echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
					echo "<div class='col-8'>";
						echo "<textarea class='form-control' id='{$name}' name='{$name}' rows='3'>{$value}</textarea>";
					echo "</div>";
				echo "</div>";
			}
			
			if (true){
				echo "<div class='form-group row'>";
					echo "<div class='offset-2 col-6'>";
						echo "<input type='hidden' name='Category' value='{$category}'/>";
						echo "<input type='hidden' name='ID' value='{$ID}'/>";
						echo "<button id='submitButton' class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " {$PAGE['Button']}</button>";
						echo "&nbsp;<span id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
					echo "</div>";
				echo "</div>";
			}
			
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
	
	
	$('#Input').change(function(){

		var currentValue = $(this).val();

		if (currentValue != ''){
			
			$('#inputFeedbackSectionBusySection').show();
			$('#inputFeedbackSection').empty();
			
			
			var data = new Object;
			data['Category'] 	= '<?php echo $category; ?>';
			data['Input'] 		= currentValue;
			
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=3',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					$('#inputFeedbackSection').html(responseText);
					
					$('#inputFeedbackSectionBusySection').hide();
				}
			});
		}
	});
	
	
	<?php if ($needValidate){ ?>
		$('#Input').change();
	<?php } ?>
	
	<?php /*
	$('#Name').change(function(){

		var Name = $(this).val();

		var randomNumber = Math.random();
		
		var data = new Object;
		data['Category'] 	= '<?php echo $category; ?>';
		data['Name'] 		= Name;
		data['ID'] 			= '<?php echo $ID; ?>';
		
		$('.Name_Check_Message').hide();
		
		$('#Name').removeClass('form-control-danger');
		$('#Name').removeClass('form-control-success');
		
		$('#Name_Group').removeClass('has-danger');
		$('#Name_Group').removeClass('has-success');
		
		
		if (Name != ''){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=1&random=' + randomNumber,
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText == '1'){
						$('#Name').addClass('form-control-danger');
						$('#Name_Group').addClass('has-danger');
						$('#Name_invalid').show();
					} else {
						$('#Name').addClass('form-control-success');
						$('#Name_Group').addClass('has-success');
						$('#Name_valid').show();
					}
				}
			});
		}
	});
	*/ ?>
	
});


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