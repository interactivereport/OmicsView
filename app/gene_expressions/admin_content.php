<?php

if (isAdminUser()){
	
	unset($currentIndex);
	
	if (true){
		$currentCategory = 'System Maintenance';
		
		$currentIndex++;
		$tools[$currentCategory][$currentIndex]['Title'] 	= 'Clear System Cache';
		$tools[$currentCategory][$currentIndex]['URL'] 		= 'admin_clear_caches.php';
		$tools[$currentCategory][$currentIndex]['target'] 	= '';
		
		$currentIndex++;
		$tools[$currentCategory][$currentIndex]['Title'] 	= 'About';
		$tools[$currentCategory][$currentIndex]['URL'] 		= 'admin_about.php';
		$tools[$currentCategory][$currentIndex]['target'] 	= '';
		
		
	}
	
	if (true){
		$currentCategory = 'QA Tools';
		
		$currentIndex++;
		$tools[$currentCategory][$currentIndex]['Title'] 	= 'Validate System Config File Settings';
		$tools[$currentCategory][$currentIndex]['URL'] 		= 'admin_settings_validate.php';
		$tools[$currentCategory][$currentIndex]['target'] 	= '_blank';
		
		$currentIndex++;
		$tools[$currentCategory][$currentIndex]['Title'] 	= 'Validate BxGenomics Settings';
		$tools[$currentCategory][$currentIndex]['URL'] 		= '../bxgenomics/check.php';
		$tools[$currentCategory][$currentIndex]['target'] 	= '_blank';
		
		$currentIndex++;
		$tools[$currentCategory][$currentIndex]['Title'] 	= 'Validate Record Counts';
		$tools[$currentCategory][$currentIndex]['URL'] 		= 'admin_tables_validate.php';
		$tools[$currentCategory][$currentIndex]['target'] 	= '_blank';
		
		$currentIndex++;
		$tools[$currentCategory][$currentIndex]['Title'] 	= 'Validate Internal Data Files';
		$tools[$currentCategory][$currentIndex]['URL'] 		= 'admin_internal_data_validate.php';
		$tools[$currentCategory][$currentIndex]['target'] 	= '_blank';
	}
	
	

	foreach($tools as $currentCategory => $tempValue1){
		
		
		echo "<h4>{$currentCategory}</h4>";
		echo "<ul>";
		foreach($tempValue1 as $currentIndex => $tempValue){
			
			echo "<li>";
				echo "<a href='{$tempValue['URL']}' target='{$tempValue['target']}'>{$tempValue['Title']}</a>";
			echo "</li>";
			
			
		}
		echo "</ul>";

		
	}

} else {
	echo "<p>You do not have permissions to access this tool.</p>";	
}


?>

