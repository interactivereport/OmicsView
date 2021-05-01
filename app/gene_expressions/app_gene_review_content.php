<?php


if (array_size($dataArray) <= 0){
	echo "<div class='row'>";

		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The record does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');

		echo "</div>";


	echo "</div>";

} else {

	
	
	if (true){
		unset($actions);
		echo "<div class='row'>";
			echo "<div class='col-12'>";
			
				if (1){	
					unset($researchProjectAPI);
					$researchProjectAPI['Title'] 			= $identifier;
					$researchProjectAPI['Type'] 			= 'Gene';
					$researchProjectAPI['Source_Page'] 		= 'Gene Details';
					$researchProjectAPI['URL'] 				= "gene_expressions/app_gene_review?id={$_GET['ID']}";
					$researchProjectAPI['Record_Index']		= $_GET['ID'];
					include('app_research_project_api_modal.php');
						
					unset($researchProjectAPI);
				}
		
	
				if (true){
					$URL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Review_URL'];
					$title	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Search_Titles'];
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-search') . " {$title}</a>";
				}
	
	
				if (true){
					$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Default'];
					$identifier		= $dataArray[$identifierSQL];
	
					$URL = "app_list_new.php?Category={$recordCategory}&identifier={$identifier}";
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " {$APP_CONFIG['APP']['List_Category'][$recordCategory]['Create_New_List']}</a>";
				}
				
				if (true){
					$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Default'];
					$identifier		= $dataArray[$identifierSQL];
					
					$URL = "app_gene_expression_rnaseq_single.php?GeneName={$identifier}";
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-chart-line') . " {$APP_MESSAGE['Gene Expression Levels']}</a>";
				}
				
				if (true){
					$identifierSQL 	= $APP_CONFIG['APP']['List_Category'][$recordCategory]['Column_Default'];
					$identifier		= $dataArray[$identifierSQL];
					
					$URL = "../plot/bubble/index.php?id={$identifier}";
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-chart-pie') . " Bubble Plot</a>";
					
				}
				
				
				$get_internal_gene_viewer_url = get_internal_gene_viewer_url($identifier);
				if ($get_internal_gene_viewer_url != ''){
					$URL = $get_internal_gene_viewer_url;
					$text = get_internal_gene_viewer_text();
					$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-external-link-alt') . " {$text}</a>";	
					
				}
	
	
				echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
	
			echo "</div>";
		echo "</div>";
	}
	
	
	$iTargetURL = get_iTarget_URL($_GET['ID']);
	
	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Summary' role='tab' data-toggle='tab'>{$APP_CONFIG['APP']['List_Category']['Gene']['Name']} Summary</a>
				  </li>";

				  
			if ($iTargetURL != ''){
				$title = 'iTarget Baseline Expression';
				echo "<li class='nav-item'>
						<a class='nav-link' href='#iTarget' role='tab' data-toggle='tab'>{$title}</a>
					  </li>";
			}
		echo "</ul>";

				  
		echo "<div class='tab-content'>";
			echo "<div role='tabpanel' id='Summary' class='tab-pane fade in active show'>";
				include('app_gene_review_tab_summary.php');
			echo "</div>";

			
			if ($iTargetURL != ''){
				echo "<div role='tabpanel' id='iTarget' class='tab-pane fade in'>";
					
					echo "<br/>";
					echo "<p><a href='{$iTargetURL}' target='_blank'>" .  printFontAwesomeIcon('fas fa-external-link-alt') . " Open in iTarget</a></p>";
					echo "<br/>";
					
					echo "<div class='embed-responsive embed-responsive-1by1'>";
						echo "<iframe class='embed-responsive-item' src='{$iTargetURL}' allowfullscreen></iframe>";
					echo "</div>";
					
					
				echo "</div>";
			}
			
			
			
		echo "</div>";
		
	echo "</div>";
}


?>
