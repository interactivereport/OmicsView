<?php

echo "<div class='row'>";
	echo "<div class='col-12'>";
	echo "<form id='form_application1' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";

		if (true){
			unset($wizard);
			$wizard[1]['Icon'] 		= printFontAwesomeIcon('far fa-copy');
			$wizard[1]['Title']		= 'Upload Files';
			$wizard[1]['State']		= 1;
			
			
			$wizard[2]['Icon'] 		= printFontAwesomeIcon('far fa-check-square');
			$wizard[2]['Title']		= 'Verify Headers';
			$wizard[2]['State']		= 0;
			
			
			$wizard[3]['Icon'] 		= printFontAwesomeIcon('far fa-save');
			$wizard[3]['Title']		= 'Save to Database';
			$wizard[3]['State']		= 0;
			
			echo "<div class='form-group row'>";
				echo printWizard($wizard);
			echo "</div>";
		}

		if (0){
			echo "<div class='form-group row'>";
				echo "<div class='col-12'>";
					echo "<h2 class='pt-3'>1. Upload Files</h2>";
					echo "<hr/>";
				echo "</div>";
			echo "</div>";
		}
		
		
		if (1){
			echo "<div class='form-group row'>";
				echo "<div class='col-12'>";
					echo "<h3 class='pt-3'>About Your Data</h3>";
					echo "<hr/>";
				echo "</div>";
			echo "</div>";
		}
		
		
		if (true){
		
			$name 				= 'Data_Type';
			$displayName		= 'Data Type';
			$values				= array('' 				=> 'All', 
										'Sample' 		=> "Project, {$APP_MESSAGE['Sample and Gene Data Only']}", 
										'Comparison' 	=> 'Project, Comparison and Comparison Data',
										'Project'		=> 'Project Only',
										);
			$value				= '';
			$placeHolderText 	= '';
			
			
			echo "<div class='form-group row'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-lg-10 col-sm-12'>";
					foreach($values as $tempKey => $tempValue){
						
						if ($tempKey == $value){
							$checked = 'checked';	
						} else {
							unset($checked);	
						}
						
						echo "<div class='form-check'>";
							echo "<label class='form-check-label'>";
								echo "<input type='radio' class='form-check-input Data_Type' value='{$tempKey}' name='{$name}' {$checked}/>&nbsp;";
									echo "{$tempValue}";
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
			
			echo "</div>";
		}
		
		if (true){
		
			$name 				= 'Zip_Enable';
			$displayName		= 'How would you like to upload?';
			$values				= array('0' => 'Multiple Files', '1' => 'One Zip File');
			$value				= '';
			$placeHolderText 	= '';
			
			
			echo "<div class='form-group row'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}</strong></label>";
				
				echo "<div class='col-lg-10 col-sm-12'>";
					foreach($values as $tempKey => $tempValue){
						
						if ($tempKey == $value){
							$checked = 'checked';	
						} else {
							unset($checked);	
						}
						
						echo "<div class='form-check'>";
							echo "<label class='form-check-label'>";
								echo "<input type='radio' class='form-check-input Zip_Enable' value='{$tempKey}' name='{$name}' {$checked}/>&nbsp;";
									echo "{$tempValue}";
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
			
			echo "</div>";
		}
		
		echo "<br/>";
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<div class='col-12'>";
					echo "<h3 class='pt-3'>Upload Your Data</h3>";
					echo "<hr/>";
				echo "</div>";
			echo "</div>";
		}
		
		if (true){
			echo "<p><span class='require_class'>*</span>: Required</p>";
		}
		
		
		if (true){
			$currentTable		= 'Zip_File';
			$name				= 'Zip_File';
			$displayName		= 'Zip_File';;
			$placeHolderText 	= "<a data-toggle='modal' href='#Zip_File_Modal'>Requirement</a>";
		
			echo "<div class='Zip_Enable_Member Zip_Enable_Member_1 startHidden'>";	
			echo "<div class='form-group row'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}<span class='require_class'>*</span>:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file' id='{$name}' name='{$name}' value='{$value}'>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
			echo "</div>";
		}
			
		if (true){	
			$modalID 	= 'Zip_File_Modal';
			$modalTitle = "<h4 class='modal-title'>Upload Data Using Zip File</h4>";
			$modalBody  = "<div class='row'>";
				$modalBody  .= "<div class='col-lg-12 col-sm-12'>";
					$modalBody  .= "<p>The application will determine the data type based on the file name. Please make sure that your zip file contains the following files:</p>";
					$modalBody  .= "<p><span class='require_class'>*</span>: Required</p>";
					$modalBody  .= "<ul>";
					

					
						if (true){
							$currentTable		= 'Projects';
							$name				= $currentTable;
							$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
							$pathInfo			= pathinfo($APP_CONFIG['Internal_Data'][$currentTable]['Example']);
							$modalBody  		.= "<li class='Projects_Member'><strong>{$displayName}</strong><span class='require_class'>*</span>: <a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>{$pathInfo['basename']}</a></li>";
						}
						
						if (true){
							$currentTable		= 'Samples';
							$name				= $currentTable;
							$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
							$pathInfo			= pathinfo($APP_CONFIG['Internal_Data'][$currentTable]['Example']);
							$modalBody  		.= "<li class='Samples_Member'><strong>{$displayName}</strong><span class='require_class'>*</span>: <a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>{$pathInfo['basename']}</a></li>";
						}
						
						if (true){
							$currentTable		= 'GeneLevelExpression';
							$name				= $currentTable;
							$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
							$pathInfo			= pathinfo($APP_CONFIG['Internal_Data'][$currentTable]['Example']);
							$modalBody  		.= "<li class='GeneLevelExpression_Member'><strong>{$displayName}</strong><span class='require_class'>*</span>: <a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>{$pathInfo['basename']}</a></li>";
						}
						
						if (true){
							$currentTable		= 'GeneCount';
							$name				= $currentTable;
							$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
							$pathInfo			= pathinfo($APP_CONFIG['Internal_Data'][$currentTable]['Example']);
							$modalBody  		.= "<li class='GeneCount_Member'>{$displayName}: <a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>{$pathInfo['basename']}</a></li>";
						}
						
						if (true){
							$currentTable		= 'Comparisons';
							$name				= $currentTable;
							$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
							$pathInfo			= pathinfo($APP_CONFIG['Internal_Data'][$currentTable]['Example']);
							$modalBody  		.= "<li class='Comparisons_Member'><strong>{$displayName}</strong><span class='require_class'>*</span>: <a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>{$pathInfo['basename']}</a></li>";
						}
						
						if (true){
							$currentTable		= 'ComparisonData';
							$name				= $currentTable;
							$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
							$pathInfo			= pathinfo($APP_CONFIG['Internal_Data'][$currentTable]['Example']);
							$modalBody  		.= "<li class='ComparisonData_Member'><strong>{$displayName}</strong><span class='require_class'>*</span>: <a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>{$pathInfo['basename']}</a></li>";
						}
						
						
					$modalBody  .= "</ul>";
				$modalBody  .= "</div>";
			$modalBody  .= "</div>";
			
			echo printModal($modalID, $modalTitle, $modalBody);
		}
		
		
	
		if (true){
			$currentTable		= 'Projects';
			$name				= $currentTable;
			$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
			$placeHolderText 	= "<a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>Example file</a>";
			
			echo "<div class='form-group row Zip_Enable_Member Zip_Enable_Member_0 Data_Type_Member_Project'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}<span class='require_class'>*</span>:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file' id='{$name}' name='{$name}' value='{$value}' required>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
		}
		
		if (true){
			$currentTable		= 'Samples';
			$name				= $currentTable;
			$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
			$placeHolderText 	= "<a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>Example file</a>";
			
			echo "<div class='form-group row Zip_Enable_Member Data_Type_Member Data_Type_Member_Sample Zip_Enable_Member_0'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}<span class='require_class'>*</span>:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file' id='{$name}' name='{$name}' value='{$value}' required>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
		}
		
		if (true){
			$currentTable		= 'GeneLevelExpression';
			$name				= $currentTable;
			$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
			$placeHolderText 	= "<a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>Example file</a>";
			
			echo "<div class='form-group row Zip_Enable_Member Data_Type_Member Data_Type_Member_Sample Zip_Enable_Member_0'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}<span class='require_class'>*</span>:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file' id='{$name}' name='{$name}' value='{$value}' required>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
		}
		
		if (true){
			$currentTable		= 'GeneCount';
			$name				= $currentTable;
			$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
			$placeHolderText 	= "<a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>Example file</a>";
			
			echo "<div class='form-group row Zip_Enable_Member Data_Type_Member Data_Type_Member_Sample Zip_Enable_Member_0'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file' id='{$name}' name='{$name}' value='{$value}'>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
		}
		
		if (true){
			$currentTable		= 'Comparisons';
			$name				= $currentTable;
			$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
			$placeHolderText 	= "<a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>Example file</a>";
			
			echo "<div class='form-group row Zip_Enable_Member Data_Type_Member Data_Type_Member_Comparison Zip_Enable_Member_0'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}<span class='require_class'>*</span>:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file' id='{$name}' name='{$name}' value='{$value}' required>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
		}
		
		if (true){
			$currentTable		= 'ComparisonData';
			$name				= $currentTable;
			$displayName		= $APP_CONFIG['Internal_Data'][$name]['Name'];
			$placeHolderText 	= "<a href='{$APP_CONFIG['Internal_Data'][$currentTable]['Example']}' target='_blank'>Example file</a>";
			
			echo "<div class='form-group row Zip_Enable_Member Data_Type_Member Data_Type_Member_Comparison Zip_Enable_Member_0'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}<span class='require_class'>*</span>:</strong></label>";
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<input type='file' class='form-control-file-file' id='{$name}' name='{$name}' value='{$value}' required>";
					echo "<p class='form-text '>{$placeHolderText}</p>";
				echo "</div>";
			echo "</div>";
		}
		
		echo "<br/>";
		
		if (1){
			echo "<div class='form-group row'>";
				echo "<div class='col-12'>";
					echo "<h3 class='pt-3'>Other Information</h3>";
					echo "<hr/>";
				echo "</div>";
			echo "</div>";
		}
		
		
		if (true){
		
			$name 				= 'File_Fomat';
			$displayName		= 'Data Format';
			$values				= array('' => 'Auto Detect', 'csv' => 'CSV (Comma Separated)', 'tab' => 'Tab Delimited');
			$value				= '';
			$placeHolderText 	= '';
			
			
			echo "<div class='form-group row'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-lg-10 col-sm-12'>";
					foreach($values as $tempKey => $tempValue){
						
						if ($tempKey == $value){
							$checked = 'checked';	
						} else {
							unset($checked);	
						}
						
						echo "<div class='form-check'>";
							echo "<label class='form-check-label'>";
								echo "<input type='radio' class='form-check-input' value='{$tempKey}' name='{$name}' {$checked}/>&nbsp;";
									echo "{$tempValue}";
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
			
			echo "</div>";
		}
		
		
		if (true){
		
			$name 				= 'Expression_Fomat';
			$displayName		= 'Expression Data Format';
			$values				= array('0' => 'Matrix Format', '1' => 'Table Format');
			$value				= '';
			$placeHolderText 	= "<a data-toggle='modal' href='#Expression_Fomat_Modal'>Help</a>";;
			
			if (true){
				echo "<div class='form-group row Data_Type_Member Data_Type_Member_Sample Data_Type_Member_Comparison'>";
					echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}:</strong></label>";
					
					echo "<div class='col-lg-10 col-sm-12'>";
						foreach($values as $tempKey => $tempValue){
							
							if ($tempKey == $value){
								$checked = 'checked';	
							} else {
								unset($checked);	
							}
							
							echo "<div class='form-check'>";
								echo "<label class='form-check-label'>";
									echo "<input type='radio' class='form-check-input' value='{$tempKey}' name='{$name}' {$checked}/>&nbsp;";
										echo "{$tempValue}";
								echo "</label>";
							echo "</div>";
						}
						echo "<p class='form-text '>{$placeHolderText}</p>";
					echo "</div>";
				
				echo "</div>";
			}
			
			if (true){
				$modalID 	= 'Expression_Fomat_Modal';
				$modalTitle = "<h4 class='modal-title'>Expression File Format</h4>";
				$modalBody  = "<div class='row'>";
					$modalBody  .= "<div class='col-lg-12 col-sm-12'>";
						$modalBody  .= "<p>The application supports two different formats:</p>";
						
						
						$modalBody  .= "<h4>Matrix Format</h4>";
						$modalBody  .= "<p>This format supports only one kind of data at a time.</p>";
						
						unset($tableContent);
						$tableContent['Header'][1]		= $APP_MESSAGE['Gene'];
						$tableContent['Header'][2] 		= "Sample_1";
						$tableContent['Header'][3] 		= "Sample_2";
						$tableContent['Header'][4] 		= "Sample_3";
						
						
						$tableContent['Body'][1]['Value'][1]	= "CREB1";
						$tableContent['Body'][1]['Value'][2]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][1]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][1]['Value'][4]	= round(mt_rand(200, 1000) / 100, 2);
						
						
						$tableContent['Body'][2]['Value'][1]	= "TP53";
						$tableContent['Body'][2]['Value'][2]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][2]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][2]['Value'][4]	= round(mt_rand(200, 1000) / 100, 2);
						
						$tableContent['Body'][3]['Value'][1]	= "WASH7P";
						$tableContent['Body'][3]['Value'][2]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][3]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][3]['Value'][4]	= round(mt_rand(200, 1000) / 100, 2);
						
						$modalBody  .= printTableHTML($tableContent, 1, 1, 0, 'col-lg-12');
						
						
						$modalBody  .= "<hr/>";
						$modalBody  .= "<h4>Table Format:</h4>";
						$modalBody  .= "<p>This format supports only multiple kinds of data.</p>";
						
						unset($tableContent);
						$tableContent['Header'][1]		= $APP_MESSAGE['Gene'];
						$tableContent['Header'][2] 		= "Sample_ID";
						$tableContent['Header'][3] 		= "Expression";
						$tableContent['Header'][4] 		= "Count";
						
						
						$tableContent['Body'][1]['Value'][1]	= "CREB1";
						$tableContent['Body'][1]['Value'][2]	= "Sample_1";
						$tableContent['Body'][1]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][1]['Value'][4]	= mt_rand(10, 100);
						
						
						$tableContent['Body'][2]['Value'][1]	= "CREB1";
						$tableContent['Body'][2]['Value'][2]	= "Sample_2";
						$tableContent['Body'][2]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][2]['Value'][4]	= mt_rand(10, 100);
						
						$tableContent['Body'][3]['Value'][1]	= "CREB1";
						$tableContent['Body'][3]['Value'][2]	= "Sample_3";
						$tableContent['Body'][3]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][3]['Value'][4]	= mt_rand(10, 100);
						
						$tableContent['Body'][4]['Value'][1]	= "TP53";
						$tableContent['Body'][4]['Value'][2]	= "Sample_1";
						$tableContent['Body'][4]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][4]['Value'][4]	= mt_rand(10, 100);
						
						
						$tableContent['Body'][5]['Value'][1]	= "TP53";
						$tableContent['Body'][5]['Value'][2]	= "Sample_2";
						$tableContent['Body'][5]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][5]['Value'][4]	= mt_rand(10, 100);
						
						$tableContent['Body'][6]['Value'][1]	= "TP53";
						$tableContent['Body'][6]['Value'][2]	= "Sample_3";
						$tableContent['Body'][6]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][6]['Value'][4]	= mt_rand(10, 100);
						
						$tableContent['Body'][7]['Value'][1]	= "WASH7P";
						$tableContent['Body'][7]['Value'][2]	= "Sample_1";
						$tableContent['Body'][7]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][7]['Value'][4]	= mt_rand(10, 100);
						
						
						$tableContent['Body'][8]['Value'][1]	= "WASH7P";
						$tableContent['Body'][8]['Value'][2]	= "Sample_2";
						$tableContent['Body'][8]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][8]['Value'][4]	= mt_rand(10, 100);
						
						$tableContent['Body'][9]['Value'][1]	= "WASH7P";
						$tableContent['Body'][9]['Value'][2]	= "Sample_3";
						$tableContent['Body'][9]['Value'][3]	= round(mt_rand(200, 1000) / 100, 2);
						$tableContent['Body'][9]['Value'][4]	= mt_rand(10, 100);
						
						$modalBody  .= printTableHTML($tableContent, 1, 1, 0, 'col-lg-12');
						
					$modalBody  .= "</div>";
				$modalBody  .= "</div>";
				
				echo printModal($modalID, $modalTitle, $modalBody);
			}
			
		}
		
		if (array_size($APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']) > 0){
		
			$name 				= 'Gene_Mapping';
			$displayName		= $APP_MESSAGE['Gene Mapping'];
			$value 				= $APP_CONFIG['Internal_Data_Settings']['Gene_Mapping_Choice'];
			$placeHolderText 	= 'Please select the species of the gene';
			
			
			echo "<div class='form-group row Data_Type_Member Data_Type_Member_Sample Data_Type_Member_Comparison'>";
				echo "<label for='{$name}' class='col-lg-2 col-sm-12 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-lg-10 col-sm-12'>";
					foreach($APP_CONFIG['Internal_Data_Settings']['Gene_Mapping'] as $tempKey => $tempValue){
						
						if ($tempKey == $value){
							$checked = 'checked';	
						} else {
							unset($checked);	
						}
						
						echo "<div class='form-check'>";
							echo "<label class='form-check-label'>";
								echo "<input type='radio' class='form-check-input' value='{$tempKey}' name='{$name}' {$checked}/>&nbsp;";
									echo "{$tempValue['Name']}";
							echo "</label>";
						echo "</div>";
					}
				echo "</div>";
			
			echo "</div>";
		}
		
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<label for='Permission' class='col-lg-2 col-sm-12 col-form-label'><strong>Access:</strong></label>";
				
				echo "<div class='col-lg-10 col-sm-12'>";
					echo "<div class='form-check'>";
						echo "<label class='form-check-label'>";
							echo "<input type='checkbox' class='form-check-input' value='1' name='Permission'/>&nbsp; All users will have access to this dataset";
						echo "</label>";
					echo "</div>";
				echo "</div>";
			
			echo "</div>";
		}
		
		echo "<br/>";
		
		if (true){
			echo "<div class='form-group row'>";
				echo "<div class='offset-2 col-6'>";
					echo "<input type='hidden' name='sessionID' value='" . getUniqueID() . "'/>";
					echo "<button id='submitButton' class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " Continue</button>";
					echo "&nbsp;&nbsp;<a href='app_internal_data_import.php'>" . printFontAwesomeIcon('fas fa-sync-alt') . ' Reset</a>';
					echo "&nbsp;<span id='busySection1' class='startHidden'>" . printFontAwesomeIcon('fas fa-spinner fa-spin'). "</span>";
				echo "</div>";
			echo "</div>";
		}
		
	echo "</form>";
	echo "</div>";

echo "</div>";


echo "<div>";
	echo "<form id='form_application2' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";
		echo "<div id='form_application_content2' class='startHidden feedbackSection'></div>";
	echo "</form>";
echo "</div>";



echo "<div>";
	echo "<form id='form_application3' action='javascript:void(0);' method='post' role='form' class='form-horizontal' enctype='multipart/form-data' autocomplete='off'>";
		echo "<div id='form_application_content3' class='startHidden feedbackSection'></div>";
	echo "</form>";
echo "</div>";
	
	



?>

<style>
.require_class{
	color:red;	
	font-size:1.2em;
}
</style>


<script type="text/javascript">

$(document).ready(function(){
	$('#form_application1').ajaxForm({ 
        target: '#form_application_content2',
        url: 'app_internal_data_import_exe1.php',
        type: 'post',
		beforeSubmit: beforeSubmit1,
        success: showResponse1
    });
	
	
	$('#form_application2').ajaxForm({ 
        target: '#form_application_content3',
        url: 'app_internal_data_import_exe2.php',
        type: 'post',
		beforeSubmit: beforeSubmit2,
        success: showResponse2
    });
	
	
	
	$(document).on('change', '.Zip_Enable', function(){
		updateFileChoice();
	});
	
	
	
	
	$(document).on('change', '.Data_Type', function(){
		updateFileChoice();
	});
	
	
	$(document).on('click', '.showForm1Trigger', function(){
		$('#form_application_content2').empty();
		$('#form_application_content2').hide();
		
		$('#form_application_content3').empty();
		$('#form_application_content3').hide();
		
		$('#form_application1').show();
	});
	
	$(document).on('click', '.showForm2Trigger', function(){
		$('#form_application_content3').empty();
		$('#form_application_content3').hide();
		
		$('#form_application2').show();
	});
	
	
});

function updateFileChoice(){
	var Zip_Enable = parseInt($("input[name='Zip_Enable']:checked").val());

	$('.Zip_Enable_Member').hide();
	$('.Zip_Enable_Member_' + Zip_Enable).show();
	
	var Data_Type = $("input[name='Data_Type']:checked").val();
	
	
	if (Zip_Enable == 0){
		$('#Zip_File').prop('required', false);
		$('#Projects').prop('required', true);
		$('#Samples').prop('required', true);
		$('#GeneLevelExpression').prop('required', true);
		$('#Comparisons').prop('required', true);
		$('#ComparisonData').prop('required', true);
		
		$('.Data_Type_Member').hide();
		
		if (Data_Type == ''){
			$('.Data_Type_Member').show();
			
			$('#Projects').prop('required', true);
			$('#Samples').prop('required', true);
			$('#GeneLevelExpression').prop('required', true);
			$('#Comparisons').prop('required', true);
			$('#ComparisonData').prop('required', true);
			
			
			$('.Projects_Member').show();
			$('.Samples_Member').show();
			$('.GeneLevelExpression_Member').show();
			$('.GeneCount_Member').show();
			$('.Comparisons_Member').show();
			$('.ComparisonData_Member').show();
			
		} else {
			$('.Data_Type_Member_' + Data_Type).show();
			
			if (Data_Type == 'Sample'){
				$('#Projects').prop('required', true);
				$('#Samples').prop('required', true);
				$('#GeneLevelExpression').prop('required', true);
				$('#Comparisons').prop('required', false);
				$('#ComparisonData').prop('required', false);
				
				$('.Projects_Member').show();
				$('.Samples_Member').show();
				$('.GeneLevelExpression_Member').show();
				$('.GeneCount_Member').show();
				$('.Comparisons_Member').hide();
				$('.ComparisonData_Member').hide();
				
			} else if (Data_Type == 'Comparison'){
				$('#Projects').prop('required', true);
				$('#Samples').prop('required', false);
				$('#GeneLevelExpression').prop('required', false);
				$('#Comparisons').prop('required', true);
				$('#ComparisonData').prop('required', true);
				
				
				$('.Projects_Member').show();
				$('.Samples_Member').hide();
				$('.GeneLevelExpression_Member').hide();
				$('.GeneCount_Member').hide();
				$('.Comparisons_Member').show();
				$('.ComparisonData_Member').show();
				
			} else if (Data_Type == 'Project'){
				$('#Projects').prop('required', true);
				$('#Samples').prop('required', false);
				$('#GeneLevelExpression').prop('required', false);
				$('#Comparisons').prop('required', false);
				$('#ComparisonData').prop('required', false);
				
				
				$('.Projects_Member').show();
				$('.Samples_Member').hide();
				$('.GeneLevelExpression_Member').hide();
				$('.GeneCount_Member').hide();
				$('.Comparisons_Member').hide();
				$('.ComparisonData_Member').hide();
			}
			
		}

	} else {
		$('#Zip_File').prop('required', true);
		
		$('#Projects').prop('required', false);
		$('#Samples').prop('required', false);
		$('#GeneLevelExpression').prop('required', false);
		$('#Comparisons').prop('required', false);
		$('#ComparisonData').prop('required', false);
		
		
		
		if (Data_Type == ''){
			$('.Projects_Member').show();
			$('.Samples_Member').show();
			$('.GeneLevelExpression_Member').show();
			$('.GeneCount_Member').show();
			$('.Comparisons_Member').show();
			$('.ComparisonData_Member').show();
			
		} else {
			if (Data_Type == 'Sample'){

				$('.Projects_Member').show();
				$('.Samples_Member').show();
				$('.GeneLevelExpression_Member').show();
				$('.GeneCount_Member').show();
				$('.Comparisons_Member').hide();
				$('.ComparisonData_Member').hide();
				
			} else if (Data_Type == 'Comparison'){
				
				$('.Projects_Member').show();
				$('.Samples_Member').hide();
				$('.GeneLevelExpression_Member').hide();
				$('.GeneCount_Member').hide();
				$('.Comparisons_Member').show();
				$('.ComparisonData_Member').show();
				
			} else if (Data_Type == 'Project'){
				
				$('.Projects_Member').show();
				$('.Samples_Member').hide();
				$('.GeneLevelExpression_Member').hide();
				$('.GeneCount_Member').hide();
				$('.Comparisons_Member').hide();
				$('.ComparisonData_Member').hide();
			}
		}
		
		
		
	}
}

function beforeSubmit1() {
	$('#form_application_content2').empty();
	$('#form_application_content2').hide();
	$('#busySection1').show();
	return true;
}


function showResponse1(responseText, statusText) {
	responseText = $.trim(responseText);

	$('#busySection1').hide();
	$('#form_application_content2').html(responseText);
	$('#form_application_content2').show();
	return true;

}



function beforeSubmit2() {
	$('#form_application_content3').empty();
	$('#form_application_content3').hide();
	$('#busySection2').show();
	return true;
}


function showResponse2(responseText, statusText) {
	responseText = $.trim(responseText);

	$('#busySection2').hide();
	$('#form_application_content3').html(responseText);
	$('#form_application_content3').show();
	
	$('html,body').animate({
		scrollTop: $('#form_application_content3').offset().top
	});
	
	
	return true;

}
</script>