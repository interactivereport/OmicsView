<?php

$dataArray = getInternalDataJob($_GET['ID']);


if ($_GET['Save']){
	$message = printFontAwesomeIcon('fas fa-check text-success') . " The settings have been updated.";
	echo getAlerts($message, 'success');
}

if (array_size($dataArray) <= 0){
	echo "<div class='row'>";

		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The data does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');

		echo "</div>";


	echo "</div>";

} else {

	unset($actions);
	echo "<div class='row'>";
		echo "<div class='col-12'>";

			if (true){
				$URL = "app_internal_data_import.php";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " Import New Data</a>";
			}
			
			if (true){
				$URL = "app_internal_data_browse.php";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-clone') . " Browse All</a>";
			}
			
			if ($dataArray['canUpdate']){
				$URL = "app_internal_data_update.php?ID={$dataArray['ID']}";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " Update this Dataset</a>";
			}
			
			if ($BXAF_CONFIG_CUSTOM['DATASET_ENABLE']){
				if ($dataArray['Project_Count'] == 1){
					$URL = "app_dataset_update.php?ProjectIndex={$dataArray['Projects'][0]['ProjectIndex']}";
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " Create Dataset</a>";
				}
			}
			
			if ($dataArray['canUpdate']){
				$URL = "app_internal_data_delete.php?ID={$dataArray['ID']}";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-trash-alt') . " Delete</a>";
			}
					

			echo "<p>" . implode(" &nbsp; &nbsp; &nbsp;", $actions) . "</p>";

		echo "</div>";
	echo "</div>";
	
	
	
	

	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Summary' role='tab' data-toggle='tab'>Summary</a>
				  </li>";

			if (array_size($dataArray['Output']['Error']) > 0){
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Error' role='tab' data-toggle='tab'>Error (" . array_size($dataArray['Output']['Error']) . ") " . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . "</a>
					  </li>";
			}
			

			if ($dataArray['Project_Count'] > 0){
				if ($dataArray['Project_Count'] == 1){
					$title = $APP_CONFIG['APP']['List_Category']['Project']['Name'];
					echo "<li class='nav-item'>
							<a class='nav-link' href='#Projects' role='tab' data-toggle='tab'>{$title}</a>
					  		</li>";
				} elseif ($dataArray['Project_Count'] > 1){
					$title = $APP_CONFIG['APP']['List_Category']['Project']['Names'];
					echo "<li class='nav-item'>
						<a class='nav-link' href='#Projects' role='tab' data-toggle='tab'>{$title} ({$dataArray['Project_Count']})</a>
					  </li>";
				}
			}
				  
			if ($dataArray['Comparison_Count'] > 0){	  
				if ($dataArray['Comparison_Count'] <= 1){
					$title = $APP_CONFIG['APP']['List_Category']['Comparison']['Name'];
				} else {
					$title = $APP_CONFIG['APP']['List_Category']['Comparison']['Names'];
				}
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Comparisons' role='tab' data-toggle='tab'>{$title} ({$dataArray['Comparison_Count']})</a>
					  </li>";
			}
				  
			
			if ($dataArray['Sample_Count'] > 0){  
				if ($dataArray['Sample_Count'] <= 1){
					$title = $APP_CONFIG['APP']['List_Category']['Sample']['Name'];
				} else {
					$title = $APP_CONFIG['APP']['List_Category']['Sample']['Names'];
				}
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Samples' role='tab' data-toggle='tab'>{$title} ({$dataArray['Sample_Count']})</a>
					  </li>";
			}
			
			if ($dataArray['Dataset_Count'] > 0){  
				if ($dataArray['Dataset_Count'] <= 1){
					$title = $APP_CONFIG['APP']['List_Category']['Dataset']['Name'];
				} else {
					$title = $APP_CONFIG['APP']['List_Category']['Dataset']['Names'];
				}
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Datasets' role='tab' data-toggle='tab'>{$title} ({$dataArray['Dataset_Count']})</a>
					  </li>";
			}


			
			if (isAdminUser()){
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Technical_Info' role='tab' data-toggle='tab'>Technical Info</a>
					  </li>";
				
			}
			

		echo "</ul>";
		
		
		
		
				  
		echo "<div class='tab-content'>";
			echo "<div role='tabpanel' id='Summary' class='tab-pane fade in active show'>";
				include('app_internal_data_review_tab_summary.php');
			echo "</div>";
			
			if (isAdminUser()){
				echo "<div role='tabpanel' id='Technical_Info' class='tab-pane fade in'>";
					include('app_internal_data_review_tab_technical_info.php');
				echo "</div>";
			}
			
			if (array_size($dataArray['Output']['Error']) > 0){
				echo "<div role='tabpanel' id='Error' class='tab-pane fade in'>";
					include('app_internal_data_review_tab_error.php');
				echo "</div>";
			}
			
			if ($dataArray['Project_Count'] > 0){
				echo "<div role='tabpanel' id='Projects' class='tab-pane fade in'>";
				if ($dataArray['Project_Count'] == 1){
					$records	= $dataArray['Projects'];
					
					$projectArray = $dataArray['Projects'][0];
					
					include('app_project_review_tab_summary.php');
				} else {
					$table 		= $APP_CONFIG['Table']['App_User_Data_Projects'];
					$preference	= 'Project';
					$title		= 'Projects';
					$table_public = 'Projects';
					$records	= $dataArray['Projects'];
					$dynamicPreference 	= true;
					$referenceProjectIndex = array_column($dataArray['Projects'], 'ProjectIndex');
					include('app_internal_data_review_tab_browse.php');
				}
				echo "</div>";
			}
			
			
			if ($dataArray['Comparison_Count'] > 0){
				echo "<div role='tabpanel' id='Comparisons' class='tab-pane fade in'>";
					$table 		= $APP_CONFIG['Table']['App_User_Data_Comparisons'];
					$preference	= 'Comparison';
					$title		= 'Comparisons';
					$table_public = 'Comparisons';
					$records	= $dataArray['Comparisons'];
					$dynamicPreference 	= true;
					$referenceProjectIndex = array_column($dataArray['Projects'], 'ProjectIndex');
					include('app_internal_data_review_tab_browse.php');
				echo "</div>";
			}
			
			
			if ($dataArray['Sample_Count'] > 0){  
				echo "<div role='tabpanel' id='Samples' class='tab-pane fade in'>";
					$table 		= $APP_CONFIG['Table']['App_User_Data_Samples'];
					$preference	= 'Sample';
					$title		= 'Samples';
					$table_public = 'Samples';
					$records	= $dataArray['Samples'];
					$dynamicPreference 	= true;
					$referenceProjectIndex = array_column($dataArray['Projects'], 'ProjectIndex');
					include('app_internal_data_review_tab_browse.php');
				echo "</div>";
			}
			
			
			
			if ($dataArray['Dataset_Count'] > 0){  
				echo "<div role='tabpanel' id='Datasets' class='tab-pane fade in'>";
					$table 		= $APP_CONFIG['Table']['App_User_Data_Datasets'];
					$preference	= 'Dataset';
					$title		= 'Datasets';
					$table_public = 'Datasets';
					$records	= $dataArray['Datasets'];
					$dynamicPreference 	= true;
					$referenceProjectIndex = array_column($dataArray['Projects'], 'ProjectIndex');
					include('app_internal_data_review_tab_browse.php');
				echo "</div>";
			}
			
			
			
			
			
		echo "</div>";
		
	echo "</div>";
}


?>
<script type="text/javascript">
$(document).ready(function(){

	$('a[data-toggle="tab"]').on( 'shown.bs.tab', function(){
        $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
    });
});

</script>