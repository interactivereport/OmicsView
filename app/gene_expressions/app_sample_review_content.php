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
			
				if (true){
					$URL 	= 'javascript:void(0);';
					$title	= 'Display All Sample Attributes';
					$actions[] = 
					"<a href='{$URL}' class='tableSwitcherToComplete tableSwitcherCompact tableSwitcherMember'>" . printFontAwesomeIcon('far fa-square') . " {$title}</a>
					 <a href='{$URL}' class='tableSwitcherToCompact  tableSwitcherComplete tableSwitcherMember startHidden'>" . printFontAwesomeIcon('far fa-check-square') . " {$title}</a>";
				}
	
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
	
	
				echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
	
			echo "</div>";
		echo "</div>";
	}
	
	
	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Summary' role='tab' data-toggle='tab'>{$APP_CONFIG['APP']['List_Category']['Sample']['Name']} Summary</a>
				  </li>";
				  
			if ($dataArray['ProjectIndex'] > 0){
				echo "<li class='nav-item'>
					<a class='nav-link' href='#Project' role='tab' data-toggle='tab'>{$APP_CONFIG['APP']['List_Category']['Project']['Name']} Summary</a>
				  </li>";	
			}

			$auditTrail = getAuditTrail($APP_CONFIG['APP']['List_Category']['Sample']['Table'], $_GET['id']);

			if (array_size($auditTrail) > 0){
				$title = 'Audit Trail';
				echo "<li class='nav-item'>
						<a class='nav-link' href='#AuditTrail' role='tab' data-toggle='tab'>{$title}</a>
					  </li>";
			}

		echo "</ul>";
		
		
		
		
				  
		echo "<div class='tab-content'>";
			echo "<div role='tabpanel' id='Summary' class='tab-pane fade in active show'>";
				$sampleArray = $dataArray;
				include('app_sample_review_tab_summary.php');
			echo "</div>";

			
			if ($dataArray['ProjectIndex'] > 0){
				echo "<div role='tabpanel' id='Project' class='tab-pane fade in'>";
					$projectArray = get_one_record_by_id('Project', $dataArray['ProjectIndex']);
					$projectArray['API'] = true;
					include('app_project_review_tab_summary.php');
				echo "</div>";
			}
			
			
			if (array_size($auditTrail) > 0){
				echo "<div role='tabpanel' id='AuditTrail' class='tab-pane fade in'>";
				
					unset($componentOptions);
					$componentOptions['Category'] 	= 'Sample';
					$componentOptions['AuditTrail'] = $auditTrail;
				
					include('app_record_audit_trail.php');
				echo "</div>";
			}
			
			
			
		echo "</div>";
		
	echo "</div>";

}


?>

