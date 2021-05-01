<?php

echo "<br/>";


echo "<h3>Meta Analysis Results for {$dataArray['POST']['name']}</h3>";

echo "<br/>";

echo "<h5>Comparisons used in the analysis:</h5>";


if (true){
	$rawData = readFirstFewLinesFromFile($dataArray['prepareMetaAnalysisData2']['Summary']['Comparison_geneCount.csv'], 0, 1, 'csv');
	
	
	unset($tableContent);
	
	$tableContent['Header'] = array('Comparison Name', 
									'Comparison Number', 
									'Average Number of <br/>Genes for Meta Analysis', 
									'Average Number of <br/>Not Available (NA) Genes', 
									'# of Case Samples',
									'# of Control Samples');
									
								
	foreach($rawData['Body'] as $tempKey => $tempValue){
		
		$tableContent['Body'][$tempKey]['Value'] = array_values($tempValue);
		
		foreach($tableContent['Body'][$tempKey]['Value'] as $tempKeyX => $tempValueX){
			if (is_numeric($tempValueX)){
				$tableContent['Body'][$tempKey]['Value'][$tempKeyX] = number_format($tempValueX);
			}
		}
	}
	
	
	
	echo printTableHTML($tableContent, 1, 1, 0, 'col-lg-8 col-sm-12');
}




if (true){
	echo "<div class='row'>";
		echo "<div class='col-lg-8 col-sm-12'>";
		
		echo "<p>";
		
			if (true){
				unset($fileArray);
				$fileArray['Path'] 					= $dataArray['prepareMetaAnalysisData2']['Summary']['Comparison_List.csv'];
				$fileArray['ContentType'] 			= $fileInfo['ContentType'];
				$fileArray['Attachment_Filename'] 	= 'Comparison_List.csv';
				$fileDownloadKey					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					
				$fileURL = "app_common_download.php?key={$fileDownloadKey}";
				
					
				echo "View samples in each comparison (";
				echo "<a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . "Comparison_List.csv</a>)";
			}
			
			echo " | ";
			
			if (true){
				unset($fileArray);
				$fileArray['Path'] 					= $dataArray['prepareMetaAnalysisData2']['Summary']['Sample_geneCount.csv'];
				$fileArray['ContentType'] 			= $fileInfo['ContentType'];
				$fileArray['Attachment_Filename'] 	= 'Sample_geneCount.csv';
				$fileDownloadKey					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					
				$fileURL = "app_common_download.php?key={$fileDownloadKey}";
				
					
				echo "View number of {$APP_MESSAGE['genes']} in each comparison (";
				echo "<a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . "Sample_geneCount.csv</a>)";
			}
		
		echo "</p>";
		
		echo "</div>";
	echo "</div>";
}

if (true){
	
	echo "<div class='row'>";
		echo "<div class='col-lg-8 col-sm-12'>";
		
		echo "<p>";
			$number1 = number_format($dataArray['prepareMetaAnalysisData2']['Summary']['NGene_info']['All_genes']);
			$number2 = number_format($dataArray['prepareMetaAnalysisData2']['Summary']['NGene_info']['Genes_no_NAs']);
			echo "From <strong>{$number1}</strong> {$APP_MESSAGE['genes']} listed in the comparisons, <strong>{$number2}</strong> {$APP_MESSAGE['genes']} are present in all comparisons and thus produce statistical results from meta-analysis.";
		echo "</p>";
		

		echo "<p class='form-text'>
				Note: The statistical values from meta analysis are only available for {$APP_MESSAGE['genes']} that are present in all comparisons. If the number of {$APP_MESSAGE['genes']} in one comparison is much lower than others, consider running another meta-analysis without this comparison to get statistical values for more genes.
			</p>";
		echo "</div>";
	echo "</div>";
	
}


echo "<hr/>";



unset($dataHTML);
if (isset($dataArray['prepareMetaAnalysisData2']['Processed']['MetaDE.ES_Output.csv']['HTML'])){
	$dataHTML = &$dataArray['prepareMetaAnalysisData2']['Processed']['MetaDE.ES_Output.csv']['HTML'];
}

if (isset($dataArray['prepareMetaAnalysisData2']['Processed']['RP_ES_data.csv']['HTML'])){
	$dataHTML = &$dataArray['prepareMetaAnalysisData2']['Processed']['RP_ES_data.csv']['HTML'];
}


if (isset($dataHTML)){
	echo "<br/>";
	echo "<h5>Meta Analysis Results</h5>";
	
	
	if (true){
		$files = array();
		if (!$dataArray['POST']['rank_product_analysis']){
			$files['Meta Analysis Results']['FileKey'] 		= 'MetaDE.ES_Output.csv';
			$files['Meta Analysis Results']['Filename'] 		= 'MetaDE.ES_Output.csv';
			$files['Meta Analysis Results']['ContentType'] 	= 'text/csv; charset=utf-8';
		} else {
			$files['Meta Analysis Results']['FileKey'] 		= 'RP_ES_data.csv';
			$files['Meta Analysis Results']['Filename'] 		= 'RP_ES_data.csv';
			$files['Meta Analysis Results']['ContentType'] 	= 'text/csv; charset=utf-8';
		}
		
		foreach($files as $fileDisplay => $fileInfo){
			
			$fileName 	= $fileInfo['Filename'];
			$fileKey 	= $fileInfo['FileKey'];
			
			if (is_file($dataArray['prepareMetaAnalysisData2']['Summary'][$fileKey])){
				
				unset($fileArray);
				$fileArray['Path'] 					= $dataArray['prepareMetaAnalysisData2']['Summary'][$fileKey];
				$fileArray['ContentType'] 			= $fileInfo['ContentType'];
				$fileArray['Attachment_Filename'] 	= $fileName;
				$fileDownloadKey					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
				
				$fileURL = "app_common_download.php?key={$fileDownloadKey}";

				echo "<p><a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " Download</a></p>";
			}
		}
		
	}
	
	
	
	unset($tableOption);
	$tableOption['id'] 				= 'resultTable';
	

	$tableOption['headers']			= $dataHTML['Headers'];
	$tableOption['dataKey']			= putSQLCacheWithoutKey($dataHTML, '', 'dataTableHTMLKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
	$tableOption['disableButton'] 	= false;
	

	if ($sortID > 0){
		$tableOption['order']			= "{$sortID}" . ', "asc"';
	} else {
		$tableOption['order']			= '2, "asc"';
	}
	
	
	$tableOption['pageLength']		= 100;
	
	$dataPrintKey = putSQLCacheWithoutKey($dataPrint, '', 'dataTablePrintKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
	
	
	unset($actions);
	$actions[] = "<a href='javascript:void(0);' class='btn btn-info forestPlot'>" . printFontAwesomeIcon('fas fa-chart-pie') . "&nbsp;Forest Plot</a>";
	$feedback[] = "<div id='forestPlot_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_MESSAGE['gene']} first.</div>";
	unset($actions);
	
	
	//Checkbox
	$tableOption['columnScript'][] = '{"orderable": false}';
	$tableOption['columnScript'][] = '{"orderable": false}';
	
	for ($i = 2; $i < array_size($dataHTML['Headers']); $i++){
		$tableOption['columnScript'][] = 'null';
	}
	
	$tableOption['columnScript'] = implode(',', $tableOption['columnScript']);

	echo "<div>" . implode('&nbsp; &nbsp;', $actions) . "</div>";
	echo "<br/>";
	echo "<div>" . implode("</div><div>", $feedback) . "</div>";
	echo "<br/>";
	

	include('app_common_table_html.php');
	
	echo "<input type='hidden' id='urlKey' value='{$urlKey}'/>";
	
	
}






?>