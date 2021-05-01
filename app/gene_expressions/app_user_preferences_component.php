<?php

$getUserSettings = getUserSettings();


echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";

	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
		
			if (($_GET['tab'] == '') || ($_GET['tab'] == 'Main')){
				$class = 'active';
			} else {
				$class = ''	;
			}
		
			echo "<li class='nav-item'>
					<a class='nav-link {$class}' href='#Main' role='tab' data-toggle='tab'>General</a>
				  </li>";
			
			foreach($APP_CONFIG['APP']['List_Category'] as $currentCategory => $currentCategoryInfo){	  
				if (($currentCategory == 'Comparison') && ($BXAF_CONFIG['HIDE_Comparison_Tools'])){
					continue;	
				}
				if ($_GET['tab'] == $currentCategory){
					$class = 'active';
				} else {
					$class = ''	;
				}
				echo "<li class='nav-item'>
						<a class='nav-link {$class}' href='#{$currentCategory}' role='tab' data-toggle='tab'>{$currentCategoryInfo['Name']}</a>
					  </li>";
			}
		echo "</ul>";
	
	
	
	
		echo "<div class='tab-content'>";
			echo "<br/>";
			if (($_GET['tab'] == '') || ($_GET['tab'] == 'Main')){
				$class = 'in active show';
			} else {
				$class = ''	;
			}
			echo "<div role='tabpanel' id='Main' class='tab-pane fade {$class}'>";
				include('app_user_preferences_tab_main.php');
			echo "</div>";
			
			
			foreach($APP_CONFIG['APP']['List_Category'] as $currentCategory => $currentCategoryInfo){
				
				
				if (($currentCategory == 'Comparison') && ($BXAF_CONFIG['HIDE_Comparison_Tools'])){
					continue;	
				}
				
				if ($_GET['tab'] == $currentCategory){
					$class = 'in active show';
				} else {
					$class = ''	;
				}
				echo "<div role='tabpanel' id='{$currentCategory}' class='tab-pane fade {$class}'>";
					include('app_user_preferences_tab_columns.php');
				echo "</div>";
			}
		echo "</div>";
		
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<div class='offset-0 col-6'>";
					echo "<hr/>";
					
					if (isAdminUser()){
						echo "<p class='form-text'><input type='checkbox' name='default' value='1'/> Save as default settings for new users.</p>";
					}
					
					echo "<button id='submitButton' class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " Save</button>";
					echo "&nbsp;<span id='busySection' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
				echo "</div>";
			echo "</div>";
		}
		
	echo "</div>";
echo "</form>";

echo "<div id='feedbackSection' class='startHidden'></div>";


?>

<style>
.list-group-item{
	padding:5px;	
}

.list-selected li{
	color: #31708f;
	background-color: #d9edf7;
}

.list-available li{
	color: #3c763d;
	background-color: #dff0d8;
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
	
});


function beforeSubmit(arr, $form, options) {
	
	var i = 0;
	<?php 
		foreach($APP_CONFIG['APP']['List_Category'] as $currentCategory => $currentCategoryInfo){
			$currentSectionID = "{$currentCategory}_ColumnSection";
	?>
		i = 0;
		$('#<?php echo $currentSectionID; ?>_Selected input').each(function(index) {
			i++;
			
			arr.push({
				name: '<?php echo $currentCategory; ?>_' + i, 
				value:$(this).val()
			});
		});
		
		arr.push({
				name: '<?php echo $currentCategory; ?>_Count', 
				value:i
			});
	
	<?php } ?>
	
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