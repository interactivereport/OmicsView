<?php



echo "<br/>";

unset($errorCount);


foreach($dataArray['Output']['Error_Processed'] as $currentTable => $dataInfo){

	echo "<h3>{$APP_CONFIG['Internal_Data'][$currentTable]['Name']}</h3>";
	
	
	foreach($dataInfo as $errorKey => $errorInfo){
		
		$errorCount++;
		
		echo "<p class='form-text'>{$errorCount}. {$errorInfo['Message']}</p>";
		
		if ($errorInfo['Path'] != ''){
			
			unset($fileArray);
			$fileArray['Path'] 	= $errorInfo['Path'];
			$fileArray['Attachment_Filename'] 	= 'File';
			$fileKey		 	= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
			$fileURL 			= "app_common_download.php?key={$fileKey}";
			
			echo "<p class='form-text'>&nbsp; &nbsp; <strong>File Location</strong>: <a href='{$fileURL}' target='_blank'>Download</a></p>";
		}
	}
	
	
	echo "<hr/>";
	
	
}

?>