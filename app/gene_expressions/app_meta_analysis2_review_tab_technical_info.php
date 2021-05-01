<?php

echo "<br/>";

$message = printFontAwesomeIcon('fas fa-info-circle') . " This tab is visible to admin users only.";
echo getAlerts($message, 'warning');


$classLeft	= 'col-2';
$classRight = 'col-10';

echo "<div class='row'>";
	
	echo "<div class='col-11'>";
	
		echo "<br/>";

		echo "<dl class='row'>";
			if (true){
				echo "<dt class='{$classLeft} text-right'>Directory:</dt>";
				echo "<dd class='{$classRight}'>{$dataArray['prepareMetaAnalysisData2']['Summary']['Path']}</dt>";
			}
		
		
			$files = array();
			$files['Comparison List']['FileKey'] 		= 'Comparison_List.csv';
			$files['Comparison List']['Filename'] 		= 'Comparison_List.csv';
			$files['Comparison List']['ContentType'] 	= 'text/csv; charset=utf-8';
			
			$files['Comparison List']['FileKey'] 		= 'Sample_table.csv';
			$files['Sample Table']['Filename'] 			= 'Sample_table.csv';
			$files['Sample Table']['ContentType'] 		= 'text/csv; charset=utf-8';
			
			$files['Expression Matrix']['FileKey'] 		= 'Expression_matrix.csv';
			$files['Expression Matrix']['Filename'] 	= 'Expression_matrix.csv';
			$files['Expression Matrix']['ContentType'] 	= 'text/csv; charset=utf-8';
			
			$files['Gene Annotation']['FileKey'] 		= 'Gene_Annoation.csv';
			$files['Gene Annotation']['Filename'] 		= 'Gene_Annoation.csv';
			$files['Gene Annotation']['ContentType'] 	= 'text/csv; charset=utf-8';
			

			$files['R Command']['FileKey'] 				= 'command.R';
			$files['R Command']['Filename'] 			= 'run.R';
			$files['R Command']['ContentType'] 			= 'text/plain; charset=utf-8';
			
			$files['R Output']['FileKey'] 				= 'R-Command_Output';
			$files['R Output']['Filename'] 				= 'run.log';
			$files['R Output']['ContentType'] 			= 'text/plain; charset=utf-8';
			
			
			$files['Sample Gene Count']['FileKey'] 		= 'Sample_geneCount.csv';
			$files['Sample Gene Count']['Filename'] 		= 'Sample_geneCount.csv';
			$files['Sample Gene Count']['ContentType'] 	= 'text/csv; charset=utf-8';
			
			
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
					
					
					echo "<dt class='{$classLeft} text-right'>{$fileDisplay}:</dt>";
					echo "<dd class='{$classRight}'><a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " {$fileName}</a></dt>";
				}
			}
			
			

			
			
		echo "</dl>";

	echo "</div>";

echo "</div>";
	

?>