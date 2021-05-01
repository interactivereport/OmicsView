<?php

//Variables
/*
$PAGE['Title']
$PAGE['Body']
*/

if ($PAGE['Title'] != ''){
	$BXAF_CONFIG['BXAF_PAGE_TITLE'] = $PAGE['Title'];
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
if (true){
	include('page_generator_headers.php');	
}

if (true){
	echo "<body>";
	
		if ((!$PAGE['Full_Screen']) && file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])){
			include_once($BXAF_CONFIG['BXAF_PAGE_MENU']);
		}
	
	
		echo "<div id='bxaf_page_content' class='row no-gutters h-100'>";
		
			if ((!$PAGE['Full_Screen']) && (!$PAGE['Disable_Left_Menu']) && file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])){
				include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']);
			}
	
			echo "<div id='bxaf_page_right' class='{$BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']}'>";
				echo "<div id='bxaf_page_right_content' class='w-100 p-2'>";
					echo "<div class='container-fluid'>";
						if ((!$PAGE['Full_Screen']) && ($PAGE['Header'] != '')){
							echo "<div class='row'>";
								echo "<div class='col-12'>";
									echo "<h1 class='Xpage-header pt-3'>{$PAGE['Header']}</h1>";
									echo "<hr/>";
								echo "</div>";
							echo "</div>";
						}
	
						if (is_file($PAGE['Body'])){
							if ($PAGE['Body'] == $PAGE['URL']){
								$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') .  ' $PAGE[Body] == $PAGE[URL]';
								echo getAlerts($message, 'danger');
							} else {
								include($PAGE['Body']);
							}
						} else {
							$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . ' The $PAGE[Body] variable is either empty or the file does not exist. Please verify your code and try again.';
							echo getAlerts($message, 'warning');
						}
					echo "</div>";
				echo "</div>";
	
				if(!$PAGE['Full_Screen'] && file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])){
					include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']);
				}
	
			echo "</div>";
	
		echo "</div>";
	
	echo "</body>";
}

echo "</html>";

?>