<?php

$jobInfo = getInternalDataJob($_GET['ID']);


if (array_size($jobInfo) <= 0){
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The data does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
	echo "</div>";
	
} elseif (!$jobInfo['canUpdate']){

	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " You do not have permission to update this dataset.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
	echo "</div>";
	
} else {

	unset($actions);
	echo "<div class='row'>";
		echo "<div class='col-12'>";

			if (true){
				$URL = "app_internal_data_import.php";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " Import Internal Data</a>";
			}
			
			if (true){
				$URL = "app_internal_data_browse.php";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-clone') . " Browse Internal Data</a>";
			}

			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";

		echo "</div>";
	echo "</div>";
	
	
	
	
	echo "<form id='form_application' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";
	
	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			if (true){
				echo "<li class='nav-item'>
						<a class='nav-link active' href='#GeneExpressionPlot' role='tab' data-toggle='tab'>Gene Expression Plot</a>
					  </li>";
			}
			
			if (true){
				echo "<li class='nav-item'>
						<a class='nav-link' href='#BubblePlot' role='tab' data-toggle='tab'>Bubble Plot</a>
					  </li>";
			}
		echo "</ul>";
		
		
		

		echo "<div class='tab-content'>";
			if (true){
				echo "<div role='tabpanel' id='GeneExpressionPlot' class='tab-pane fade in active show'>";
					include('app_internal_data_update_tab_gene_expression_plot.php');
				echo "</div>";
			}
			
			if (true){
				echo "<div role='tabpanel' id='BubblePlot' class='tab-pane fade in'>";
					include('app_internal_data_update_tab_bubble_plot.php');
				echo "</div>";
			}
		echo "</div>";
		
	echo "</div>";
	
	
		
	
	//Submit buttons
	if (true){
		echo "<br/>";
		unset($class);
		
		echo "<div id='submitButtonGroup' class='form-group'>";
			echo "<br/>";
			echo "<button class='xcol-sm-offset-1 btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " Save</button>";
			echo "<input type='hidden' name='URL' value='{$PAGE['URL']}'/>";
			echo "<input type='hidden' name='ID' value='{$_GET['ID']}'/>";
			echo "<input type='hidden' name='submit' value='1'/>";
			echo "&nbsp;<a href='{$PAGE['URL']}?ID={$_GET['ID']}'>Reset</a>";
			echo "&nbsp;<span class='busySection startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
		echo "</div>";
	}

	echo "</form>";
}



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
	
	
});


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
