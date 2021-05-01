<?php

if (!isAdminUser()){
	echo "<p>You do not have permissions to access this tool.</p>";	
} else {
	unset($settings);
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[WORK_DIR]']['Value'] 									= $BXAF_CONFIG_CUSTOM['WORK_DIR'];
		$settings['BXAF_CONFIG_CUSTOM[WORK_DIR]']['Type'] 									= 'Directory';
		
		$settings['BXAF_CONFIG_CUSTOM[WORK_URL]']['Value'] 									= $BXAF_CONFIG_CUSTOM['WORK_URL'];
		$settings['BXAF_CONFIG_CUSTOM[WORK_URL]']['Type'] 									= 'String';
	}
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[BGZIP_DIR]']['Value'] 								= $BXAF_CONFIG_CUSTOM['BGZIP_DIR'];
		$settings['BXAF_CONFIG_CUSTOM[BGZIP_DIR]']['Type'] 									= 'File';
		
		$settings['BXAF_CONFIG_CUSTOM[TABIX_BIN]']['Value'] 								= $BXAF_CONFIG_CUSTOM['TABIX_BIN'];
		$settings['BXAF_CONFIG_CUSTOM[TABIX_BIN]']['Type'] 									= 'File';
	}
	
	
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[TABIX_INDEX][GeneAnnotation]']['Value'] 				= $BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneAnnotation'];
		$settings['BXAF_CONFIG_CUSTOM[TABIX_INDEX][GeneAnnotation]']['Type'] 				= 'File';
		
		$settings['BXAF_CONFIG_CUSTOM[TABIX_INDEX][GeneAnnotation.gz]']['Value'] 			= $BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneAnnotation.gz'];
		$settings['BXAF_CONFIG_CUSTOM[TABIX_INDEX][GeneAnnotation.gz]']['Type'] 			= 'File';
	}
	
	
	
	if ($BXAF_CONFIG_CUSTOM['HAS_PUBLIC_COMPARISON_DATA']){
		$settings['BXAF_CONFIG_CUSTOM[GO_PATH]']['Value'] 									= $BXAF_CONFIG_CUSTOM['GO_PATH'];
		$settings['BXAF_CONFIG_CUSTOM[GO_PATH]']['Type'] 									= 'Directory';
		
		$settings['BXAF_CONFIG_CUSTOM[GO_URL]']['Value'] 									= $BXAF_CONFIG_CUSTOM['GO_URL'];
		$settings['BXAF_CONFIG_CUSTOM[GO_URL]']['Type'] 									= 'String';
		
		$settings['BXAF_CONFIG_CUSTOM[PAGE_PATH]']['Value'] 								= $BXAF_CONFIG_CUSTOM['PAGE_PATH'];
		$settings['BXAF_CONFIG_CUSTOM[PAGE_PATH]']['Type'] 									= 'Directory';
		
		$settings['BXAF_CONFIG_CUSTOM[PAGE_URL]']['Value'] 									= $BXAF_CONFIG_CUSTOM['PAGE_URL'];
		$settings['BXAF_CONFIG_CUSTOM[PAGE_URL]']['Type'] 									= 'String';
	}
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[FUNCTIONAL_ENRICHMENT_FILES][GMT]']['Value'] 			= $BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['GMT'];
		$settings['BXAF_CONFIG_CUSTOM[FUNCTIONAL_ENRICHMENT_FILES][GMT]']['Type'] 			= 'File';
		
		$settings['BXAF_CONFIG_CUSTOM[FUNCTIONAL_ENRICHMENT_FILES][Script]']['Value'] 		= $BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Script'];
		$settings['BXAF_CONFIG_CUSTOM[FUNCTIONAL_ENRICHMENT_FILES][Script]']['Type'] 		= 'Directory';
		
		$settings['BXAF_CONFIG_CUSTOM[FUNCTIONAL_ENRICHMENT_FILES][Species]']['Value'] 		= $BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Species'];
		$settings['BXAF_CONFIG_CUSTOM[FUNCTIONAL_ENRICHMENT_FILES][Species]']['Type'] 		= 'String';
	}
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[APP_PROFILE]']['Value'] 								= $BXAF_CONFIG_CUSTOM['APP_PROFILE'];
		$settings['BXAF_CONFIG_CUSTOM[APP_PROFILE]']['Type'] 								= 'String';
		
		$settings['BXAF_CONFIG_CUSTOM[APP_SPECIES]']['Value'] 								= $BXAF_CONFIG_CUSTOM['APP_SPECIES'];
		$settings['BXAF_CONFIG_CUSTOM[APP_SPECIES]']['Type'] 								= 'String';
		
		$settings['BXAF_CONFIG_CUSTOM[SPECIES]']['Value'] 									= $BXAF_CONFIG_CUSTOM['SPECIES'];
		$settings['BXAF_CONFIG_CUSTOM[SPECIES]']['Type'] 									= 'String';
	}
	
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[WIKIPATHWAY_GPML_PATH]']['Value'] 					= $BXAF_CONFIG_CUSTOM['WIKIPATHWAY_GPML_PATH'];
		$settings['BXAF_CONFIG_CUSTOM[WIKIPATHWAY_GPML_PATH]']['Type'] 						= 'Directory';
	}
	
	if (true){
		$settings['BXAF_CONFIG_CUSTOM[HOMER_PATH]']['Value'] 								= $BXAF_CONFIG_CUSTOM['HOMER_PATH'];
		$settings['BXAF_CONFIG_CUSTOM[HOMER_PATH]']['Type'] 								= 'Directory';
	}
	
	if ($BXAF_CONFIG_CUSTOM['CELL_MAP_ENABLE']){
		$settings['BXAF_CONFIG_CUSTOM[CELL_MAP_FOLDER]']['Value'] 							= $BXAF_CONFIG_CUSTOM['CELL_MAP_FOLDER'];
		$settings['BXAF_CONFIG_CUSTOM[CELL_MAP_FOLDER]']['Type'] 							= 'Directory';
	}
	
	
	
	unset($tableContent);
	$tableContent['Header']['Variable Name'] 	= 'Variable Name';
	$tableContent['Header']['Category'] 		= 'Category';
	$tableContent['Header']['Current Value'] 	= 'Current Value';
	$tableContent['Header']['Test Result'] 		= 'Test Result';
	
	if (true){
		echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
				unset($currentIndex);
				foreach($settings as $variableName => $variableProfile){
					
					$category = $variableProfile['Type'];
					$variableContent = $variableProfile['Value'];
					
					$currentIndex++;
					unset($testResult, $errorMessage);
					
					
					if ($category == 'Directory'){
						if ($variableContent == ''){
							$errorMessage 	= 'Cannot be empty';
						} elseif (!file_exists($variableContent)){
							$errorMessage 	= 'Directory does not exist.';
						} else {
							$testResult = true;	
						}
					} elseif ($category == 'File'){
						if ($variableContent == ''){
							$errorMessage 	= 'Cannot be empty';
						} elseif (!is_file($variableContent)){
							$errorMessage 	= 'File does not exist.';
						} else {
							$testResult = true;	
						}
					} elseif ($category == 'String'){
						if ($variableContent == ''){
							$errorMessage 	= 'Cannot be empty';
						} else {
							$testResult = true;	
						}
					}
					
					$tableContent['Body'][$currentIndex]['Value']['Variable Name'] 	= $variableName;
					$tableContent['Body'][$currentIndex]['Value']['Category'] 		= $category;
					$tableContent['Body'][$currentIndex]['Value']['Current Value'] 	= $variableContent;
					
					if ($testResult){
						$tableContent['Body'][$currentIndex]['Value']['Test Result'] 	= printFontAwesomeIcon('fas fa-check text-success');
					} else {
						$tableContent['Body'][$currentIndex]['Value']['Test Result'] 	= printFontAwesomeIcon('fas fa-times text-danger') . '&nbsp;' . $errorMessage;
					}
	
					
	
				}
			
				echo printTableHTML($tableContent, 1, 1, 1);
				
			echo "</div>";
		echo "</div>";
	
	}
}
?>

