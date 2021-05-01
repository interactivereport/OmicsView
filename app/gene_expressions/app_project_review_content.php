<?php


if (array_size($dataArray) <= 0){
	echo "<div class='row'>";

		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The record does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');

		echo "</div>";


	echo "</div>";

} else {

	$sampleIDs = getSampleIDFromProjectIndex($_GET['ID']);
	$sampleCount = array_size($sampleIDs);
	if ($sampleCount > 0){
		$samples = getSampleRecordsFromProjectIndex($_GET['ID']);
	}
	
	$comparisonIDs = getComparisonIDFromProjectIndex($_GET['ID']);
	$comparisonCount = array_size($comparisonIDs);
	if ($comparisonCount > 0){
		$comparisons = getComparisonRecordsFromProjectIndex($_GET['ID']);
	}
	
	//Actions
	if (true){
		
		echo "<div class='row'>";
			echo "<div class='col-12'>";
			
			
				if (true){
					unset($actions);
					if (true){
						$URL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Review_URL'];
						$title	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Search_Titles'];
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-search') . " {$title}</a>";
					}
		
					if (true){
						$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Human'];
						$identifier		= $dataArray[$identifierSQL];
						
						$URL = "app_list_new.php?Category={$recordCategory}&identifier={$identifier}";
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " {$APP_CONFIG['APP']['List_Category'][$recordCategory]['Create_New_List']}</a>";
					}
	

					if ($dataArray['Job_ID'] > 0){
						$URL 	= "app_internal_data_review.php?ID={$dataArray['Job_ID']}";
						$title	= "Review Internal Data";
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-window-restore') . " {$title}</a>";
					}
					
					echo "<p><strong>Projects:</strong> " . implode(" &nbsp; ", $actions) . "</p>";
				}
				
				
				if ($sampleCount > 0){
					unset($actions);
					
					if ($sampleCount > 0){
						$URL 	= "app_ajax.php?action=project_index_to_samples&projectIndex={$_GET['ID']}";
						$title	= "View All Samples ({$sampleCount})";
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-window-restore') . " {$title}</a>";
					}
		
		
					if ($sampleCount > 0){
						$URL 	= "app_ajax.php?action=project_index_to_sample_list&projectIndex={$_GET['ID']}";
						$title 	= "{$APP_CONFIG['APP']['List_Category']['Sample']['Create_New_List']} ({$sampleCount})";
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " {$title}</a>";
					}	
					
					echo "<p><strong>Samples:</strong> " . implode(" &nbsp; ", $actions) . "</p>";
				}
				
				
				if ($comparisonCount > 0){
					unset($actions);
					
					if ($comparisonCount > 0){
						$URL 	= "app_ajax.php?action=project_index_to_comparisons&projectIndex={$_GET['ID']}";
						$title	= "View All Comparisons ({$comparisonCount})";
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-window-restore') . " {$title}</a>";
					}
		
		
					if ($comparisonCount > 0){
						$URL 	= "app_ajax.php?action=project_index_to_comparison_list&projectIndex={$_GET['ID']}";
						$title 	= "{$APP_CONFIG['APP']['List_Category']['Comparison']['Create_New_List']} ({$comparisonCount})";
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " {$title}</a>";
					}
					
					echo "<p><strong>Comparisons:</strong> " . implode(" &nbsp; ", $actions) . "</p>";
				}
				
				if (true){
					unset($actions);
					
					if ($sampleCount > 0){
						
						
						
						if ($dataArray['Internal_Platform_Type'] != ''){
							if ($dataArray['Internal_Platform_Type'] == 'RNA-Seq'){
								$URL = "app_gene_expression_rnaseq_single.php?ProjectIndex={$_GET['ID']}";
							} else {
								$URL = "app_gene_expression_microarray_single.php?ProjectIndex={$_GET['ID']}";
							}

							
						} else {
							$platformType = getPlatformType($dataArray['LandPlatforms']);
							
							if ($platformType == 'RNA-Seq'){
								$URL = "app_gene_expression_rnaseq_single.php?ProjectIndex={$_GET['ID']}";
							} else {
								$URL = "app_gene_expression_microarray_single.php?ProjectIndex={$_GET['ID']}";
							}
							
							
						}
						
						$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-external-link-alt') . " {$APP_MESSAGE['Single Gene Expression Plot']}</a>";
					}
		
		
					if (array_size($actions) > 0){
						echo "<p><strong>Tools:</strong> " . implode(" &nbsp; ", $actions) . "</p>";
					}
				}
				
				
				
	
			echo "</div>";
		echo "</div>";
	}

	
	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Project' role='tab' data-toggle='tab'>{$APP_CONFIG['APP']['List_Category']['Project']['Name']} Project</a>
				  </li>";
				  
			
			if ($sampleCount > 0){
				$title = $APP_CONFIG['APP']['List_Category']['Sample']['Names'];
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Samples' role='tab' data-toggle='tab'>{$title} ({$sampleCount})</a>
					  </li>";
			}
			
			if ($comparisonCount > 0){
				$title = $APP_CONFIG['APP']['List_Category']['Comparison']['Names'];
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Comparisons' role='tab' data-toggle='tab'>{$title} ({$comparisonCount})</a>
					  </li>";
			}
			
			if ($datasetCount > 0){
				$title = $APP_CONFIG['APP']['List_Category']['Dataset']['Names'];
				echo "<li class='nav-item'>
						<a class='nav-link' href='#Datasets' role='tab' data-toggle='tab'>{$title} ({$datasetCount})</a>
					  </li>";
			}
			
			$auditTrail = getAuditTrail($APP_CONFIG['APP']['List_Category']['Project']['Table'], $_GET['id']);
			if (array_size($auditTrail) > 0){
				$title = 'Audit Trail';
				echo "<li class='nav-item'>
						<a class='nav-link' href='#AuditTrail' role='tab' data-toggle='tab'>{$title}</a>
					  </li>";
			}

		echo "</ul>";
		
				  
		echo "<div class='tab-content'>";
			echo "<div role='tabpanel' id='Project' class='tab-pane fade in active show'>";
				$projectArray = $dataArray;
				include('app_project_review_tab_summary.php');
			echo "</div>";

			
			if ($comparisonCount > 0){
				echo "<div role='tabpanel' id='Comparisons' class='tab-pane fade in'>";
					$table 		= $APP_CONFIG['Table']['App_User_Data_Comparisons'];
					
					$preference	= 'Comparison';
					$title		= 'Comparisons';
					$table_public = 'Comparisons';
					$records	= $comparisons;
					$dynamicPreference 	= !internal_data_is_public($_GET['ID']);
					$referenceProjectIndex = $_GET['ID'];
					include('app_internal_data_review_tab_browse.php');
				echo "</div>";
			}
			
			
			if ($sampleCount > 0){
				echo "<div role='tabpanel' id='Samples' class='tab-pane fade in'>";
					$table 		= $APP_CONFIG['Table']['App_User_Data_Samples'];
					$preference	= 'Sample';
					$title		= 'Samples';
					$table_public = 'Samples';
					$records	= $samples;
					$dynamicPreference 	= !internal_data_is_public($_GET['ID']);
					$referenceProjectIndex = $_GET['ID'];
					include('app_internal_data_review_tab_browse.php');
				echo "</div>";
			}
			
			if ($datasetCount > 0){
				echo "<div role='tabpanel' id='Datasets' class='tab-pane fade in'>";
					$table 		= $APP_CONFIG['Table']['App_User_Data_Datasets'];
					$preference	= 'Dataset';
					$title		= 'Datasets';
					$table_public = $APP_CONFIG['Table']['App_User_Data_Datasets'];
					$records	= $datasets;
					$dynamicPreference 	= !internal_data_is_public($_GET['ID']);
					$referenceProjectIndex = $_GET['ID'];
					include('app_internal_data_review_tab_browse.php');
				echo "</div>";
			}
			
			if (array_size($auditTrail) > 0){
				echo "<div role='tabpanel' id='AuditTrail' class='tab-pane fade in'>";
				
					unset($componentOptions);
					$componentOptions['Category'] 	= 'Project';
					$componentOptions['AuditTrail'] = $auditTrail;
				
					include('app_record_audit_trail.php');
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