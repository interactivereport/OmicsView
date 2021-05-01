<?php
	
	if ($debug){
		$classLeft	= 'col-2';
		$classRight = 'col-10';	
	} elseif (array_size($dataArray['Input']['Research_Projects']) > 0){
		$classLeft	= 'col-2';
		$classRight = 'col-10';	
	} else {
		$classLeft	= 'col-2';
		$classRight = 'col-10';	
	}

	echo "<div class='row'>";
	
		echo "<div class='col-11'>";
		
			echo "<br/>";

			echo "<dl class='row'>";
				if (true){
					echo "<dt class='{$classLeft} text-right'>Date Created:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['Date']}</dt>";
				}
			
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Owner:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['User']['Name']} ({$dataArray['User']['Email']})</dt>";
				}
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Platform Type:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['Internal_Platform_Type']}</dt>";	
				}
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>{$APP_MESSAGE['Gene Mapping']}:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['Gene_Mapping_Display']}</dt>";	
				}
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Import Status:</dt>";
					echo "<dd class='{$classRight}'>{$dataArray['Status_HTML']}</dt>";
				}
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Progress:</dt>";
						unset($tableContent);
						$tableContent['Header'][]		= 'Step';
						$tableContent['Header'][]		= 'Status';
						
						
						$tableContent['Body']['Status_MySQL']['Value'][] 	= "Inserting Data to the database (MySQL)";
						$tableContent['Body']['Status_MySQL']['Value'][] 	= "<div class='nowrap'>{$dataArray['Status_MySQL_HTML']}</div>";
						
						if ($dataArray['Requirement']['Comparisons']){
							$tableContent['Body']['Status_Tabix_Comparison']['Value'][] 	= "Indexing Comparison Data (Tabix)";
							$tableContent['Body']['Status_Tabix_Comparison']['Value'][] 	= "<div class='nowrap'>{$dataArray['Status_Tabix_Comparison_HTML']}</div>";
						}
						
						if ($dataArray['Requirement']['GeneLevelExpression']){
							$tableContent['Body']['Status_Tabix_GeneLevelExpression']['Value'][] 	= $APP_MESSAGE['Indexing Gene Level Expression Data (Tabix)'];
							$tableContent['Body']['Status_Tabix_GeneLevelExpression']['Value'][] 	= "<div class='nowrap'>{$dataArray['Status_Tabix_GeneLevelExpression_HTML']}</div>";
						}
							
						if ($dataArray['Requirement']['Comparisons']){
							$tableContent['Body']['Status_PAGE']['Value'][] 	= "Indexing Comparison Results (PAGE)";
							$tableContent['Body']['Status_PAGE']['Value'][] 	= "<div class='nowrap'>{$dataArray['Status_PAGE_HTML']}</div>";
							
							$tableContent['Body']['Status_GO']['Value'][] 		= "Indexing Comparison Results (GO)";
							$tableContent['Body']['Status_GO']['Value'][] 		= "<div class='nowrap'>{$dataArray['Status_GO_HTML']}</div>";
						}
					echo "<dd class='{$classRight}'>" . printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12') . "</dt>";
				}
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Data Type:</dt>";
						unset($tableContent);
						$tableContent['Header'][]		= 'Data Type';
						$tableContent['Header'][]		= 'File';
						
						if ($dataArray['Status'] > 0){
							$tableContent['Header'][]		= "<div class='nowrap'># of Data Imported</div>";
							$tableContent['Header'][]		= "<div class='nowrap'># of Data in the File</div>";
						}
						
						if (isset($dataArray['Output']['Warning'])){
							$tableContent['Header'][]		= 'Notes';
						}
						
						
						foreach($dataArray['Input']['Files'] as $tempKey => $tempValue){
							
							unset($fileArray);
							$fileArray['Path'] 					= $dataArray['Input']['Files'][$tempKey]['Path'];
							$fileArray['ContentType'] 			= $dataArray['Input']['Files'][$tempKey]['Type'];
							$fileArray['Attachment_Filename'] 	= $dataArray['Input']['Files'][$tempKey]['Name'];
							$fileKey		 					= putSQLCacheWithoutKey($fileArray, '', 'URL', 1);
							
							$fileURL = "app_common_download.php?key={$fileKey}";
							
							
							$tableContent['Body'][$tempKey]['Value'][] 	= "<div class='nowrap'>{$APP_CONFIG['Internal_Data'][$tempKey]['Name']}</div>";
							$tableContent['Body'][$tempKey]['Value'][] 	= "<div class='nowrap'><a href='{$fileURL}' target='_blank'>" . printFontAwesomeIcon('fas fa-download') . " {$fileArray['Attachment_Filename']}</a></div>";
							
							if ($dataArray['Status'] > 0){
								$tableContent['Body'][$tempKey]['Value'][] 	= number_format($dataArray['Output']['Imported'][$tempKey]);
								$tableContent['Body'][$tempKey]['Value'][] 	= number_format($dataArray['Output']['Line'][$tempKey]);
							}
							
							
							
							
							if (isset($dataArray['Input']['Files'][$tempKey]['Warning'])){
								
								$tableContent['Body'][$tempKey]['Value'][]	= "<div>" . implode("</div><hr/><div>", $dataArray['Input']['Files'][$tempKey]['Warning']) . "</div>";
									
							} else {
								$tableContent['Body'][$tempKey]['Value'][]		= '';	
							}
							
							
							
							
						}
					echo "<dd class='{$classRight}'>" . printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12') . "</dt>";
				}
	
				
			
				
				
				if (true){
					echo "<dt class='{$classLeft} text-right'>Links:</dt>";
					
					
					$URLs = array();
					
					if (true){
						$URL = "app_project_review.php?id={$dataArray['Projects'][0]['ProjectIndex']}";
						$URLs[] = "<a href='{$URL}' target='_blank'> Review Project</a>";
					}
					
					if (array_size($dataArray['Samples']) > 0){
						if ($dataArray['Internal_Platform_Type'] == 'RNA-Seq'){
							$URL = "app_gene_expression_rnaseq_single.php?ProjectIndex={$dataArray['Projects'][0]['ProjectIndex']}";
							$URLs[] = "<a href='{$URL}' target='_blank'> {$APP_MESSAGE['Single Gene Expression Plot']}</a>";
						} else {
							$URL = "app_gene_expression_microarray_single.php?ProjectIndex={$dataArray['Projects'][0]['ProjectIndex']}";
							$URLs[] = "<a href='{$URL}' target='_blank'> {$APP_MESSAGE['Single Gene Expression Plot']}</a>";
						}
					}
					
					
					echo "<dd class='{$classRight}'>";
						echo "<ul>";
						echo "<li>" . implode("</li><li>", $URLs) . "</li>";
						echo "</ul>";
					echo "</dt>";	
					
					
				}
				
				
			echo "</dl>";

		echo "</div>";

	echo "</div>";
	
?>