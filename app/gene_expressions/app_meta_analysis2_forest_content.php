<?php

if (array_size($dataArray) <= 0){
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The meta analysis does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');
		echo "</div>";
	echo "</div>";
	
} else {
	
	unset($actions);
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			
			if (true){
				$URL = "app_meta_analysis2_review.php?key={$_GET['key']}&ID={$_GET['ID']}";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-reply') . " Return to Meta Analysis Results</a>";
			}
			
			if ($geneInfo['Gene_HTML'] != ''){
				$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$_GET['GeneIndex']}";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('fas fa-list') . " Review Gene Details</a>";
			}
			
			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
			
			
			
			if ($dataArray['prepareMetaAnalysisData2']['Status'] == 'Finished'){	
				unset($researchProjectAPI);
				$researchProjectAPI['Title'] 			= $PAGE['Header'];
				$researchProjectAPI['Type'] 			= 'Meta Analysis';
				$researchProjectAPI['Source_Page'] 		= 'Meta Analysis Using Gene Expression Data: Forest Plot';
				$researchProjectAPI['URL'] 				= "gene_expressions/app_meta_analysis2_forest.php?key={$_GET['key']}&GeneIndex={$_GET['GeneIndex']}&ID={$_GET['ID']}";
				$researchProjectAPI['HideMessage']		= 1;
				$researchProjectAPI['Base64_Image_Path'] 	= $geneInfo['Forest']['Summary']['forest_plot.png'];
				//$researchProjectAPI['Parameters'] 		= $urlKey;
			
				include('app_research_project_api_modal.php');
					
				unset($researchProjectAPI);
			}
			
	
		echo "</div>";
	echo "</div>";
		
	if ($dataArray['prepareMetaAnalysisData2']['Status'] != 'Finished'){
		echo "<div class='row'>";
			echo "<div class='col-12'>";
				$message = "<div>" . printFontAwesomeIcon('fas fa-info-circle') . " The meta analysis is being processed.</div>";
				echo getAlerts($message, 'warning');
			echo "</div>";
		echo "</div>";	
	} elseif (array_size($geneInfo) <= 0){
	
		echo "<div class='row'>";
			echo "<div class='col-12'>";
				$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The {$APP_MESSAGE['gene']} information is missing. Please verify your URL and try again.</p>";
				echo getAlerts($message, 'warning');
			echo "</div>";
		echo "</div>";
 
	} else {
		
		if (true){
			$classLeft	= 'col-2';
			$classRight = 'col-10';
			echo "<div class='row'>";
		
				echo "<div class='col-11'>";
				
					echo "<br/>";
			
					echo "<dl class='row'>";
					
						if ($dataArray['POST']['name'] != ''){
							
							$URL = "app_meta_analysis2_review.php?key={$_GET['key']}&ID={$_GET['ID']}";
							$content = "<a href='{$URL}' target='_blank'>{$dataArray['POST']['name']}</a>";
							
							
							echo "<dt class='{$classLeft} text-right'>Meta Analysis:</dt>";
							echo "<dd class='{$classRight}'>{$content}</dt>";
						}
					
						if (true){
							
							$URL = "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type=gene&id={$geneInfo['GeneInfo']['GeneIndex']}";
							$content = "<a href='{$URL}' target='_blank'>{$geneInfo['GeneInfo']['GeneName']}</a>";
							
							echo "<dt class='{$classLeft} text-right'>{$APP_MESSAGE['Gene Name']}:</dt>";
							echo "<dd class='{$classRight}'>{$content}</dt>";
						}
						
						
						if (true){
							echo "<dt class='{$classLeft} text-right'>EntrezID:</dt>";
							echo "<dd class='{$classRight}'>{$geneInfo['GeneInfo']['EntrezID']}</dt>";
						}
					
					
						if (true){
							echo "<dt class='{$classLeft} text-right'>Description:</dt>";
							echo "<dd class='{$classRight}'>{$geneInfo['GeneInfo']['Description']}</dt>";
						}
					
					echo "</dl>";
			
				echo "</div>";
			
			echo "</div>";
		}
		
		
		echo "<hr/>";
		
		echo "<br/>";
		
		if (true){
			
			echo "<div class='row'>";
				echo "<div class='col-12'>";	
				
					unset($fileArray);
					$fileArray['Path'] 					= $geneInfo['Forest']['Summary']['forest_plot.svg'];
					$fileArray['ContentType'] 			= 'image/svg+xml';
					$fileArray['Attachment_Filename'] 	= 'forest_plot.svg';
					$fileDownloadKey					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					$svgFile 							= "app_common_download.php?key={$fileDownloadKey}";
				
					unset($fileArray);
					$fileArray['Path'] 					= $geneInfo['Forest']['Summary']['forest_plot.png'];
					$fileArray['ContentType'] 			= 'image/png';
					$fileArray['Attachment_Filename'] 	= 'forest_plot.png';
					$fileDownloadKey					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					$pngFile 							= "app_common_download.php?key={$fileDownloadKey}";
					
					unset($fileArray);
					$fileArray['Path'] 					= $geneInfo['Forest']['Summary']['forest_plot.pdf'];
					$fileArray['ContentType'] 			= 'application/pdf';
					$fileArray['Attachment_Filename'] 	= 'forest_plot.pdf';
					$fileDownloadKey					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
					$pdfFile 							= "app_common_download.php?key={$fileDownloadKey}";
					
					
					echo "<div><strong>Download Plot</strong>: ";
						
						echo "<a href='{$svgFile}'>SVG</a>";
						
						echo " &bull; ";
						
						echo "<a href='{$pngFile}'>PNG</a>";
						
						echo " &bull; ";
						
						echo "<a href='{$pdfFile}'>PDF</a>";
					
					echo "</div>";
					
					echo "<br/>";
					
					echo "<div><a href='{$svgFile}'><img class='img-thumbnail' src='{$pngFile}'/></a></div>";
					
				
				echo "</div>";
			echo "</div>";
			
			
			
		}
		
		echo "<hr/>";
		echo "<br/>";
		
		if (true){
			$rawData = readFirstFewLinesFromFile($geneInfo['Forest']['Summary']['meta_analysis_summary.csv'], 0, 1, 'csv');
			
			
			unset($tableContent);
			
			$tableContent['Header'] = array('Comparison Number', 
											'Comparison Name', 
											'# of Case Samples',
											'# of Control Samples',
											"logFC","CI.L","CI.R","SE","P.Value","FDR", 
											);
											
										
			foreach($rawData['Body'] as $tempKey => $tempValue){
				
				$tableContent['Body'][$tempKey]['Value'] = array_values($tempValue);

			}
			
			echo printTableHTML($tableContent, 1, 1, 0, 'col-lg-8 col-sm-12');
		}

	
	
		if (true){
			echo "<div class='row'>";
				echo "<div class='col-12'>";	
				
					echo "<p>Notes:</p>";
					
					echo "<ol class='form-text'>";
						echo "<li>";
							echo "The 95% confidence interval are shown in the plot for visualization purpose only. We recommend using the P-values to decide if the change is significant or not.";
						echo "</li>";
						
						echo "<li>";
							echo "If Rank Product method was run, the meta analysis P-value is based on Rank Product method (RP). If Rank Product was not run, then the P-value is from MetaDE.ES method (ES). For {$APP_MESSAGE['gene expression']}, we recommend using Rank Product results as this method can capture down and up regulation more accurately, and the p-values tend to be more sensitive.";
						echo "</li>";
					echo "</ol>";
					
				
				echo "</div>";
			echo "</div>";
			
		}
	
	}
	
}






?>

<script type="text/javascript">
$(document).ready(function(){

	
	

});

</script>