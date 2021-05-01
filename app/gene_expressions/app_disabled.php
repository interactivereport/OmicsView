<?php


	if (true){
		echo "<div class='row'>";
		echo "<div class='col-8'>";
			
			$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . "This tool is not available. Please <a href='http://bioinforx.com/contact/' target='_blank'>contact us</a> for details.</div>";
			echo getAlerts($message, 'warning');
			exit();
			
		echo "</div>";
		echo "</div>";
	}
	
?>