<?php

if ($APP_CONFIG['APP']['List_Category'][$category]['Edit'] == ''){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The category is missing. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'danger');
		echo "</div>";
	echo "</div>";
	exit();
}


foreach($recordIndexes as $tempKey => $recordIndex){
	if (internal_data_is_public($recordIndex)){
		unset($recordIndexes[$tempKey]);
		$hasPublicData = true;
	}
}

if ($hasPublicData){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " You selections contain public data. They will be excluded from this edit. Since we update the public data several times a year, any changes made by the users will be overwritten in the future update.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
	echo "</div>";
}


if (array_size($recordIndexes) <= 0){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are no internal data available. Please select at least one internal data and try again.</p>";
			echo getAlerts($message, 'danger');
		echo "</div>";
	echo "</div>";
	exit();
}


if (true){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
		
			if (array_size($recordIndexes) > 1){
				echo "<h4># of Record to Be Updated: " . array_size($recordIndexes) . "</h4>";
			}
			
			echo "<p>Please check the box first before editing the value. If the box is unchecked, the new value will not be saved.</p>";
		echo "</div>";
	echo "</div>";
}



if (array_size($recordIndexes) == 1){
	$dataArray = get_one_record_by_id($category, $recordIndexes[0]);
} else {
	$dataArray = array();
}


if (true){
	echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";
	
	
	foreach($APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'] as $currentSQL => $currentSQLInfo){
	
		if ($currentSQLInfo['HideFromSearch']) continue;
		if ($currentSQLInfo['HideFromOption']) continue;
		if ($currentSQLInfo['HideFromUpdate']) continue;
		
		if (!isset($APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentSQL])) continue;		

		$currentTitle = $currentSQLInfo['Title'];
		
		if ($currentSQLInfo['Title_Long'] != ''){
			$currentTitle = $currentSQLInfo['Title_Long'];	
		}
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<label class='col-2 col-form-label'>
						<input type='checkbox' class='choice' children='{$currentSQL}' name='{$currentSQL}_Choice' value='1'/>
						{$currentTitle}:
					</label>";
				echo "<div class='col-3'>";
					echo "<input type='text' class='form-control' id='{$currentSQL}_Value' name='{$currentSQL}_Value' value='{$dataArray[$currentSQL]}' disabled>";
					echo "<input type='hidden' name='Choices[]' value='{$currentSQL}'/>";
				echo "</div>";
			echo "</div>";
		}
	}
	
	if (true){
		echo "<div class='form-group row'>";
			echo "<div class='offset-0 col-6'>";
				echo "<input type='hidden' name='Category' value='{$category}'/>";
				$recordIndexesString = implode(',', $recordIndexes);
				echo "<input type='hidden' name='IDs' value='{$recordIndexesString}'/>";
				echo "<button id='submitButton' class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " Save</button>";
				echo "&nbsp;<span id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
			echo "</div>";
		echo "</div>";
	}

	echo "</form>";
	
	echo "<div id='feedbackSection' class='startHidden'></div>";

	
}


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
	
	
	
	$('.choice').change(function(){
		
		var currentValue 	= $(this).prop('checked');
		var children 		= $(this).attr('children');
		
		if (currentValue){
			$('#' + children + '_Value').prop('disabled', false);
		} else {
			$('#' + children + '_Value').prop('disabled', true);
		}
		
	});

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