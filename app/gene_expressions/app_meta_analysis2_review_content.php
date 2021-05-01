<?php


unset($actions);
echo "<div class='row'>";
	echo "<div class='col-12'>";

		if (0){
			$URL = "app_research_project_update.php?ID={$ID}";
			$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " Update</a>";
		}
		
		if (true){
			$URL = "app_meta_analysis2.php";
			$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " Create New Meta Analysis</a>";
		}
		
		echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
		
		if ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Finished'){	
			unset($researchProjectAPI);
			$researchProjectAPI['Title'] 			= $dataArray['POST']['name'];
			$researchProjectAPI['Type'] 			= 'Meta Analysis';
			$researchProjectAPI['Source_Page'] 		= 'Meta Analysis Using Gene Expression Data';
			$researchProjectAPI['URL'] 				= "gene_expressions/app_meta_analysis2_review.php?ID={$_GET['ID']}&key={$_GET['key']}";
			$researchProjectAPI['HideMessage']		= 1;
			//$researchProjectAPI['Base64_Image_ID'] 	= 'plotSection';
			//$researchProjectAPI['Parameters'] 		= $urlKey;
		
			include('app_research_project_api_modal.php');
				
			unset($researchProjectAPI);
		}

	echo "</div>";
echo "</div>";



if (array_size($dataArray) <= 0){
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The meta analysis does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
	echo "</div>";
	
} else {
	
		echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Summary' role='tab' data-toggle='tab'>Summary</a>
				  </li>";
			
			if (isAdminUser()){
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Technical_Info' role='tab' data-toggle='tab'>Technical Info</a>
					  </li>";
				
			}
			

			
			if ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Finished'){
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Results' role='tab' data-toggle='tab'>Meta Analysis Results</a>
					  </li>";
			}
				  
			
			
			

		echo "</ul>";
		
		
		
		
				  
		echo "<div class='tab-content'>";
			echo "<div role='tabpanel' id='Summary' class='tab-pane fade in active show'>";
				include('app_meta_analysis2_review_tab_summary.php');
			echo "</div>";
			
			if (isAdminUser()){
				echo "<div role='tabpanel' id='Technical_Info' class='tab-pane fade in'>";
					include('app_meta_analysis2_review_tab_technical_info.php');
				echo "</div>";
			}
			
			
			if ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Finished'){
				echo "<div role='tabpanel' id='Results' class='tab-pane fade in'>";
					include('app_meta_analysis2_review_tab_results.php');
				echo "</div>";
			}
			
			
			echo "<div role='tabpanel' id='User_Role' class='tab-pane fade'>";
				echo "<h4 style='margin-top:50px;'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). " Loading...</h4>";
			echo "</div>";
		echo "</div>";
		
	echo "</div>";
	
	
	
	
	

	
	
	
}






?>

<script type="text/javascript">
$(document).ready(function(){

	$(document).on('change', '.selectAllTrigger', function(){
		var isChecked = $(this).prop('checked');
		
		if (isChecked){
			$('.recordCheckbox').prop('checked', true);	
		} else {
			$('.recordCheckbox').prop('checked', false);	
		}
	});
	
	
	$(document).on('click', '.forestPlot', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
		
		data['urlKey']	= $('#urlKey').val();
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});

		
		if (count > 0){
			/*
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=11',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#forestPlot_Missing_Record').show();	
					}
				}
			});
			*/
		} else {
			
			$('#forestPlot_Missing_Record').show();	
		}
	});
	

});

</script>