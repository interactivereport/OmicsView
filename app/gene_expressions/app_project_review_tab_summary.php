<?php


echo "<br/>";




echo "<div class='row'>";

	echo "<div class='col-lg-8'>";
	
		if (true){
			$actions = array();
			
			
			if ($projectArray['API']){
				$URL 	= "app_project_review.php?ID={$projectArray['ProjectIndex']}";
				$title	= "Review Project";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " {$title}</a>";
			}
			
			
			if (can_update_record($projectArray)){
				$dataKey = putSQLCacheWithoutKey(array($projectArray['ProjectIndex']), '', 'URL', 1);
				$URL 	= "app_record_update.php?Category=Project&recordIndex={$dataKey}";
				$title	= "Update Project";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " {$title}</a>";
			}
			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
			unset($actions);
		}
		
		echo "<br/>";


		
		if (true){
			echo "<dl class='row'>";

				foreach($projectArray as $currentSQL => $currentValue){
					
					if ($currentSQL == 'ID') continue;
					if ($currentSQL == 'ProjectIndex') continue;
					if ($currentSQL == 'SampleIndex') continue;
					if ($currentSQL == 'ComparisonIndex') continue;

					if ($currentValue == '') continue;
					
					$currentDisplay = getRecordDisplayName('Project', $currentSQL, $projectArray);
					
					if ($currentDisplay == '') continue;
					
					echo "<dt class='col-3 text-right'>{$currentDisplay}:</dt>";
					echo "<dd class='col-9'>{$currentValue}</dt>";
					
				}
				
			echo "</dl>";
		}

	echo "</div>";
	
	/*
	echo "<div class='col-lg-4'>";
		echo "ssssss";
	
	echo "</div>";
	*/
	

echo "</div>";

?>