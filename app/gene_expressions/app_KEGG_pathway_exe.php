<?php
$BXAPP_TIMER['config_init.php'][] = microtime(true);
include_once('config_init.php');
startTimer('config_init.php');


echo "<br/>";
echo "<hr/>";


$comparisonIDs 			= splitData($_POST['ComparisonIDs']);
$_POST['ComparisonIDs'] = $comparisonIDs;

if (array_size($comparisonIDs) <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please enter at least a comparison ID.";
	echo getAlerts($message, 'danger');
	exit();
}


if ($_POST['KEGG_Identifier'] == ''){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select a KEGG ID and try again.";
	echo getAlerts($message, 'danger');
	exit();
}


if (!validateKEGGIdentifier($_POST['KEGG_Identifier'])){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The KEGG identifier ({$_POST['KEGG_Identifier']}) is invalid.";
	echo getAlerts($message, 'danger');
	exit();
}


$_POST['Visualization'] = abs(intval($_POST['Visualization']));

if (($_POST['Visualization'] > 3) || ($_POST['Visualization'] < 1)){
	$_POST['Visualization'] = 1;
}

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);


startTimer('exe::getComparisonIDsExistenceInfo()');
$getComparisonIDsExistenceInfo = getComparisonIDsExistenceInfo($comparisonIDs, '', $_POST['data_source_private_project_indexes']);
startTimer('exe::getComparisonIDsExistenceInfo()');


if ($getComparisonIDsExistenceInfo == false){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The comparison IDs you entered do not exist in the database.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');
	exit();	
}

if ($getComparisonIDsExistenceInfo['hasMissing']){
			
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the comparison IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='comparisonMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;

	echo "<div id='comparisonMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Comparison IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Comparison IDs Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDsExistenceInfoSection'>{$getComparisonIDsExistenceInfo['Input_Count']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Comparison IDs Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDsExistenceInfoSection'>{$getComparisonIDsExistenceInfo['Output_Count']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Comparison IDs Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDsExistenceInfoSection'>{$getComparisonIDsExistenceInfo['Missing_Count']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'comparisonIDsExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Comparison IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$getComparisonIDsExistenceInfo['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getComparisonIDsExistenceInfo['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$getComparisonIDsExistenceInfo['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getComparisonIDsExistenceInfo['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$getComparisonIDsExistenceInfo['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $getComparisonIDsExistenceInfo['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	echo "</div>";
	
}



$comparisonIndexes 	= $getComparisonIDsExistenceInfo['ComparisonIndexes'];



startTimer('visualizeKEGG()');
$results = visualizeKEGG($comparisonIndexes, $getComparisonIDsExistenceInfo['ComparisonIndexes::ComparisonID'], $_POST['KEGG_Identifier'], $_POST['Visualization'], $_POST['data_source'], $_POST['data_source_private_project_indexes']);
startTimer('visualizeKEGG()');


if (!$results['Summary']['hasPNG']){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	exit();
}


if (!$results['Summary']['hasEntrezID']){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " No {$APP_MESSAGE['gene']} has been found. Please try to use a different KEGG ID or Comparison ID.";
	echo getAlerts($message, 'danger', 'col-lg-10 col-sm-12');	
}



if (true){
	$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);
	
	$getKEGGPathwayByIdentifier = getKEGGPathwayByIdentifier($_POST['KEGG_Identifier']);
	echo "<br/>";
	echo "<br/>";

}





if (true){
	//$message = "<p>Please click <a href='javascript:void(0);' id='summaryTrigger' class='forceLink'>here</a> for the search summary.</p>";
	//echo $message;
	
	echo "<div id='summarySection' class='startHiddenX'>";
	
	$URL 		= "http://www.kegg.jp/dbget-bin/www_bget?{$_POST['KEGG_Identifier']}";
	$KEGG_Name 	= $getKEGGPathwayByIdentifier['Name'];
	
	if (true){	
		unset($researchProjectAPI);
		$researchProjectAPI['Title'] 			= "KEGG Pathway Visualization of {$KEGG_Name}";
		$researchProjectAPI['Type'] 			= 'Comparison Plotting Tool';
		$researchProjectAPI['Source_Page'] 		= "KEGG Pathway Visualization";
		$researchProjectAPI['URL'] 				= "gene_expressions/app_KEGG_pathway.php?key={$urlKey}";
		$researchProjectAPI['Base64_Image_ID'] 	= '';
		$researchProjectAPI['Base64_Image_Path'] = $results['Output']['png'];
		$researchProjectAPI['Parameters'] 		= $urlKey;
	
		include('app_research_project_api_modal.php');
			
		unset($researchProjectAPI);
	}
	
	
	echo "<p><strong>KEGG ID</strong>: <a href='{$URL}' target='_blank'>{$KEGG_Name} ({$_POST['KEGG_Identifier']})</a></p>";
	
	echo "<p>Comparisons are mapped to the KEGG pathway in the order shown below:</p>";
		
	echo "<ul>";
		foreach($getComparisonIDsExistenceInfo['ComparisonIndexes::ComparisonID'] as $comparisonIndex => $comparisonName){
			
			$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=comparison&id={$comparisonIndex}";
			echo "<li><a href='{$URL}' target='_blank'>{$comparisonName}</a></li>";
		}
	echo "</ul>";
	
	echo "<hr/>";
	echo "</div>";
}


if (1){
	
	unset($dataArray);
	$dataArray['Path'] 			= $results['Output']['png'];
	$dataArray['ContentType'] 	= 'image/png';
	$pngKey 					= putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
	
	$imgURL = "app_common_download.php?key={$pngKey}";
	
	

	
	echo "<div>";
		echo "<img src='{$imgURL}' name='pathwayimage' usemap='#mapdata' border='0' />";
		echo "<map name='mapdata'>";
			echo $getKEGGPathwayByIdentifier['HTML_Map'];
		echo "</map>";
	echo "</div>";
	
}



if (isDebugMode()){
	echo "<hr/>";
	echo processTimer($BXAPP_TIMER, 1);
}



if ($results['Summary']['hasEntrezID']){
	echo "<br/>";
	echo "<hr/>";
	echo "<h2>Log<sub>2</sub> (Fold Change), P-Value and FDR</h2>";
	echo "<br/>";
	
	if (true){
		unset($URLs);
		
		$URLs[] = "<a href='app_common_table_download.php?key={$results['Output']['dataTablePrintKey']}'>Log<sub>2</sub>(Fold Change) and P-Value</a>";
		$URLs[] = "<a href='app_common_download.php?key=1&key={$results['Output']['RInputKey']}'>Input Data used in R</a>";
		
		echo "<p>" . printFontAwesomeIcon('fas fa-download') . "<strong>Download: </strong>" . implode('&nbsp; &bull; &nbsp;', $URLs) . "</p>";
		echo "<br/>";
		
	}


	unset($tableOption);
	$tableOption['id'] 		= 'resultTable';
	$tableOption['headers']	= $results['Output']['Headers'];
	$tableOption['dataKey']	= $results['Output']['dataTableHTMLKey'];
	include('app_common_table_html.php');
} 



?>


<script type="text/javascript">

$(document).ready(function(){
	

	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "app_KEGG_pathway.php?key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "KEGG Pathway Visualization", URL);
	}
	<?php } ?>

});

</script>
