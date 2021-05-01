<?php


$dataArray = array();

$dataArray = $jobInfo['Gene_Expression_Plot_Details'];


if (!$useCustomColumn){
	$defaultGeneExpressionOptions = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options'];
	$defaultGeneExpressionOptionsCompleted = $APP_CONFIG['DB_Dictionary']['Samples']['Gene_Expression_Options_Completed'];	
}


if (true){
	$message = printFontAwesomeIcon('fas fa-info-circle') . " The following settings will be used in the gene expression plots tools. These settings are only used for this project and its corresponding samples only.";
	echo getAlerts($message, 'info');
}


$currentTable = 'Samples';

				
if (true){
	
	unset($checked);
	if ($dataArray['Gene_Expression_Plot_Default']){
		$checked = 'checked';
		$class = 'startHidden';
	} elseif (!isset($dataArray)){
		$checked = 'checked';	
		$class = 'startHidden';
	}

	$displayValue = 'Use system default settings. Uncheck this option to override with your own settings. ';


	echo "<div class='form-check-inline'>";
		echo "<label class='form-check-label' for='Gene_Expression_Plot_Default'>";
			echo "<input class='form-check-input' type='checkbox' name='Gene_Expression_Plot_Default' id='Gene_Expression_Plot_Default' value='1' {$checked}>";
				echo '&nbsp;' . $displayValue . '&nbsp;';
			echo "</label>";
	echo "</div>";
}

echo "<br/>";
echo "<br/>";

if (true){

	echo "<div id='Gene_Expression_Plot_inputSection' class='{$class}'>";
	
	if (true){
		echo "<h4>Predefined Settings</h4>";
		echo "<p class='form-text'>If a project is selected, the following settings will be automatically used in the gene expression plot tools. End users can adjust the values in the advanced options.</p>";
		echo "<hr/>";
		
		echo "<div style='margin-left:30px;'>";
		
		//Sample Attributes:
		if (array_size($defaultGeneExpressionOptions) > 0){
			echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
				echo "<div style='margin-top:12px;'>";
					echo "<strong>Sample Attributes: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>";
					echo "<input class='form-check-input Plot_Columns_Select_All_Trigger' type='checkbox'/>Select All";
					echo "&nbsp;";
					echo "<span id='Plot_Columns_Message' style='font-weight:normal;'></span>";
					
				echo "</div>";
				
				echo "<div style='margin-left:15px;'>";
				
					
				
				foreach($defaultGeneExpressionOptions as $currentColumn => $currentDetails){
					
					if (!$APP_CONFIG['APP']['Module']['GTEx']){
						if ($currentDetails['GTEx']) continue;
					}
					
					$randomID 		= 'Plot_Columns_ID_' . md5($currentColumn . '::' .  rand());
					
					$classOuter		= '';
					$class			= "Plot_Columns_" . md5($currentColumn);
					$defaultClass	= '';
					$GTExClass		= '';
					$checked 		= '';
					
					if (!isset($dataArray['Plot_Columns'])){
						if ($currentDetails['Default']){
							$checked = 'checked';	
						}
					} else {
						if (in_array($currentColumn, $dataArray['Plot_Columns'])){
							$checked = 'checked';	
						}
					}
					
					
					if ($currentDetails['GTEx']){
						$classOuter = 'startHidden GTEx_Member';
						
						if (!isset($dataArray['Plot_Columns'])){
							if ($currentDetails['Default']){
								$classOuter = 'GTEx_Member';
								$checked 	= 'checked';	
							}
						} else {
							if (in_array($currentColumn, $dataArray['Plot_Columns'])){
								$classOuter = 'GTEx_Member';
								$checked 	= 'checked';	
							}
						}
					}
					
					if ($currentDetails['GTEx-Checked']){
						$GTExClass	= 'GTEx_Checkbox';	
					}
					
					
					if ($currentDetails['Default']){
						$defaultClass	= 'Default_Checkbox';		
					}
					
					
					$currentTitle = $currentDetails['Title'];
					if ($currentTitle == ''){
						$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
					}
					
					echo "<div class='form-check form-check-inline {$classOuter}'>";
						echo "<label for='{$randomID}' class='form-check-label'>
								<input type='checkbox' id='{$randomID}' class='form-check-input Plot_Columns {$class} {$defaultClass} {$GTExClass}' sibling='{$class}' name='Plot_Columns[]' value='{$currentColumn}' {$checked}/>
								{$currentTitle}
								</label>";
					echo "</div>";
					
				}
				
				
				if (array_size($defaultGeneExpressionOptionsCompleted) != array_size($defaultGeneExpressionOptions)){
					$randomID = md5($currentColumn . '::' .  rand() . rand());
					echo "<div class='form-check form-check-inline'>";
						echo "<label for='{$randomID}'>
								<a href='#Plot_Columns_Modal' data-toggle='modal' class='nowrap'>(More Attributes)</a>
							</label>";
					echo "</div>";
				}
				echo "</div>";
				
				
				if (array_size($defaultGeneExpressionOptionsCompleted) > 0){
					
					$modalID 	= 'Plot_Columns_Modal';
					$modalTitle = "<div class='modal-title'>";
						$modalTitle	.= "<h4>Additional Sample Attributes</h4>";
	
						$modalTitle	.= "<div style='margin-left:15px;'>";
							$modalTitle .= "<label class='form-check-label'>
											<input class='form-check-input Plot_Columns_Select_All_Trigger' type='checkbox'/>Select All
											</label>";
						$modalTitle	.= "</div>";
					$modalTitle	.= "</div>";
					
					
					unset($modalBody);
					
					$modalBody	.= "<div class='row'>";
					
					foreach($defaultGeneExpressionOptionsCompleted as $currentColumn => $currentDetails){
								
						$randomID 	= 'Plot_Columns_ID_' . md5($currentColumn . '::' .  rand());
						
						$class		= "Plot_Columns_" . md5($currentColumn);
					
						$checked = '';
						
						if (array_size($dataArray) <= 0){
							if ($currentDetails['Default']){
								$checked = 'checked';	
							}
						} elseif (in_array($currentColumn, $dataArray['Plot_Columns'])){
							$checked = 'checked';
						}
						
						$currentTitle = $currentDetails['Title'];
						if ($currentTitle == ''){
							$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
						}
						
						$modalBody	.= "<div class='col-lg-6'>";
							$modalBody .= "<div class='form-check'>";
								$modalBody .= "<label for='{$randomID}' class='form-check-label'>
												<input type='checkbox' id='{$randomID}' class='form-check-input Plot_Columns {$class}' sibling='{$class}' name='Plot_Columns[]' value='{$currentColumn}' {$checked}/>
												{$currentTitle}
												</label>";
							$modalBody .= "</div>";
						$modalBody	.= "</div>";
						
					}
					$modalBody	.= "</div>";
					
					
					echo printModal($modalID, $modalTitle, $modalBody);
					
					
				}
			
			
			echo "</div>";
		
		
			echo "</div>";
		}
		
		//Group Samples
		if (true){
			$name 				= 'groupSamples';
			$displayName		= 'Group Samples';
			$values 			= $defaultGeneExpressionOptionsCompleted;
			if (isset($dataArray[$name])){
				$value			= $dataArray[$name];
			} else {
				$value			= -1;
			}
			
			$placeHolderText 	= '';
			if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>Auto</option>";
						
						unset($selected);
						if ($value == -1){
							$selected = 'selected';	
						}
						echo "<option value='-1' {$selected}>None&#47;Disabled</option>";
					
						foreach($defaultGeneExpressionOptionsCompleted as $currentColumn => $currentDetails){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							$currentTitle = $currentDetails['Title'];
							if ($currentTitle == ''){
								$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
							}
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							
							
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
			}
		}
		
		//Sort By
		if (true){
			$name 				= 'sortBy';
			$displayName		= 'Sort By';
			$values 			= $defaultGeneExpressionOptionsCompleted;
			if (isset($dataArray[$name])){
				$value			= $dataArray[$name];
			} else {
				$value			= -1;
			}
			$placeHolderText 	= '';
			if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>Auto (Same as the Attribute in Group Samples)</option>";
										
						unset($selected);
						if ($value == -1){
							$selected = 'selected';	
						}
						echo "<option value='-1' {$selected}>None&#47;Disabled</option>";
					
						foreach($defaultGeneExpressionOptionsCompleted as $currentColumn => $currentDetails){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							$currentTitle = $currentDetails['Title'];
							if ($currentTitle == ''){
								$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
							}
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							
							
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
			}
		}
		
		
		//Color By
		if (true){
			$name 				= 'colorBy';
			$displayName		= 'Color By';
			$values 			= $defaultGeneExpressionOptionsCompleted;
			if (isset($dataArray[$name])){
				$value			= $dataArray[$name];
			} else {
				$value			= -1;
			}
			$placeHolderText 	= '';
			if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>Auto</option>";
						
						unset($selected);
						if ($value == -1){
							$selected = 'selected';	
						}
						echo "<option value='-1' {$selected}>None&#47;Disabled</option>";
					
						foreach($defaultGeneExpressionOptionsCompleted as $currentColumn => $currentDetails){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							$currentTitle = $currentDetails['Title'];
							if ($currentTitle == ''){
								$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
							}
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							
							
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
			}
		}
		
		//Shape By
		if (true){
			$name 				= 'shapeBy';
			$displayName		= 'Shape By';
			$values 			= $defaultGeneExpressionOptionsCompleted;
			if (isset($dataArray[$name])){
				$value			= $dataArray[$name];
			} else {
				$value			= -1;
			}
			$placeHolderText 	= '';
			if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>Auto</option>";
						
						unset($selected);
						if ($value == -1){
							$selected = 'selected';	
						}
						echo "<option value='-1' {$selected}>None&#47;Disabled</option>";
					
						foreach($defaultGeneExpressionOptionsCompleted as $currentColumn => $currentDetails){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							$currentTitle = $currentDetails['Title'];
							if ($currentTitle == ''){
								$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
							}
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							
							
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
			}
		}
	
		
		//Segregate Data
		if (true){
			$name 				= 'segregate';
			$displayName		= 'Segregate Data';
			$values 			= $defaultGeneExpressionOptionsCompleted;
			if (isset($dataArray[$name])){
				$value			= $dataArray[$name];
			} else {
				$value			= -1;
			}
			$placeHolderText 	= '';
			if (array_size($values) > 0){
			echo "<div style='margin-top:12px;'>";
				echo "<div class='form-group row'>";
				
				echo "<label for='{$name}' class='col-2 col-form-label'><strong>{$displayName}:</strong></label>";
				
				echo "<div class='col-2'>";
					echo "<select class='form-control' name='{$name}' id='{$name}' title='{$placeHolderText}'>";
					
						echo "<option value=''>None&#47;Disabled</option>";
					
						foreach($defaultGeneExpressionOptionsCompleted as $currentColumn => $currentDetails){
							
							unset($selected);
							
							if ($currentColumn === $value){
								$selected = 'selected';	
							}
							
							$currentTitle = $currentDetails['Title'];
							if ($currentTitle == ''){
								$currentTitle = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$currentColumn]['Title'];
							}
							
							echo "<option value='{$currentColumn}' {$selected}>{$currentTitle}</option>";
							
							
						}
					echo "</select>";
				echo "</div>";
				echo "</div>";
		
			echo "</div>";
			}
		}
		

		echo "<p class='form-text'>(Please enter 0 or leave blank if you want the auto settings.)</p>";
		//Chart Height
		if (true){
			echo "<div style='margin-top:12px;'>";
			
				echo "<div class='form-group row'>";
				
					echo "<div class='col-2'>";
						echo "<label for='plot_height' class='xcol-form-label'><strong>Chart Height: &nbsp;</strong></label>";
					echo "</div>";
		
		
					echo "<div class='col-2'>";
					
						$value = '';
						if (isset($dataArray['plot_height']) && ($dataArray['plot_height'] >= 0)){
							$value = intval($dataArray['plot_height']);
						}
						$value = abs(intval($value));
					
						echo "<input class='inputForm form-control' type='text' id='plot_height' name='plot_height' value='{$value}'/>";
					echo "</div>";
					
				echo "</div>";
			
			echo "</div>";
		}
		
		//Chart Width
		if (true){
			echo "<div style='margin-top:12px;'>";
			
				echo "<div class='form-group row'>";
	
					echo "<div class='col-2'>";
						echo "<label for='plot_width' class='xcol-form-label'><strong>Chart Width: &nbsp;</strong></label>";
					echo "</div>";
		
		
					echo "<div class='col-2'>";
					
						$value = '';
						if (isset($dataArray['plot_width']) && ($dataArray['plot_width'] >= 0)){
							$value = intval($dataArray['plot_width']);
						}
						$value = abs(intval($value));
					
						echo "<input class='inputForm form-control' type='text' id='plot_width' name='plot_width' value='{$value}'/>";
					echo "</div>";
				
				echo "</div>";
				
			echo "</div>";
		}
		
		echo "</div>";
	}
	
	
	if (true){
		echo "<br/>";
		echo "<h4>Programmable Settings</h4>";
		echo "<p class='form-text'>The programmable settings contain two options: CanvasXpress AfterRender JSON Code and JavaScript. Due to security reason, the JavaScript code is only available to admin users. These two settings cannot be changed by end users. Please click <a href='#CanvasXpressModal_Order' data-toggle='modal'>here</a> to learn more about the execution order. <a href='#CanvasXpressModal_Order' data-toggle='modal'>" . printFontAwesomeIcon('<i class="far fa-question-circle"></i>') . " Help</a>
		</p>";
		
		echo "<hr/>";
		
		echo "<div style='margin-left:30px;'>";
	
		//JSON
		if (true){
			
			echo "<div class='row'>";
			echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>";
				echo "<div style='margin-top:10px;'>";
					echo "<strong>CanvasXpress AfterRender JSON Code:</strong> 
							<a href='#CanvasXpressModal_JSON' data-toggle='modal'>" . printFontAwesomeIcon('<i class="far fa-question-circle"></i>') . " Help</a>";
					echo "<p class='form-text'>Please copy and paste the entire JSON file below.</p>";
				echo "</div>";
				
				$values = $dataArray['JSON'];
				
				
				
				echo "<div style='margin-top:10px;'>";
					echo "<textarea class='form-control' rows='20' name='JSON' id='JSON' placeholder='{$exampleMessage}'>{$values}</textarea>";
					
					echo "<div id='JSONFeedbackSection' class='startHidden'></div>";
				echo "</div>";

			echo "</div>";
			echo "</div>";
		}
		
		//JSCode
		if (true){
			
			
			
			if (general_is_admin_user()){
				$class = '';	
			} else {
				$class = 'startHidden';	
			}
			
			
			
			echo "<div class='row {$class}'>";
			echo "<div class='col-lg-8 col-md-8 col-sm-12 col-xs-12'>";
				echo "<div style='margin-top:10px;'>";
					echo "<strong>JavaScript Codes:</strong> 
							<a href='#CanvasXpressModal_JS' data-toggle='modal'>" . printFontAwesomeIcon('<i class="far fa-question-circle"></i>') . " Help</a>";
				echo "</div>";
				
				$values = $dataArray['JSCode'];
				
				echo "<div style='margin-top:10px;'>";
					echo "<textarea class='form-control' rows='8' name='JSCode' id='JSCode' placeholder='{$exampleMessage}'>{$values}</textarea>";
				echo "</div>";

				
			echo "</div>";
			echo "</div>";
		}

	
		echo "</div>";
		
	}
	
	echo "</div>";

}



if (true){
	$modalID 	= 'CanvasXpressModal_Order';
	$modalTitle = "<h3>Execution Order</h3>";
	$modalBody	= '';
	
	$modalBody	.= "<p>The gene expression tool will override the graph settings using the following order:</p>";
	
	$modalBody	.= "<br/>";
	
	$modalBody	.= "<ol>";
		$modalBody	.= "<li>End user opens the gene expression tool.</li>";
		$modalBody	.= "<li><strong>The predefined settings</strong> are loaded (e.g., samples attributes, group samples, sort by, color by etc).</li>";
		$modalBody	.= "<li>End user clicks the Submit button to load the plot.</li>";
		$modalBody	.= "<li>The CanvasXpress object is constructued using the <strong>AfterRender JSON code</strong>.</li>";
		$modalBody	.= "<li>If the end user has made any changes in the Advanced Options pop-up box (e.g., group samples, sort by, color by etc), those settings will be applied to the plot.</li>";
		$modalBody	.= "<li>Finally, if the <strong>JavaScript Codes</strong> are available, the codes will be executed.</li>";
	$modalBody	.= "</ol>";
	
	
	$modalBody	.= "<p>In summary, the order are listed in the following:</p>";
	
	$modalBody	.= "<ul>";
		$modalBody	.= "<li>Highest: JavaScript Codes (cannot be changed by end users)</li>";
		$modalBody	.= "<li>Medium: Predefined Settings (can be changed by end users)</li>";
		$modalBody	.= "<li>Lowest: AfterRender JSON code (cannot be changed by end users)</li>";
	$modalBody	.= "</ul>";
	
	
	
	
	
	echo printModal($modalID, $modalTitle, $modalBody, '', '', 'modal-dialog-wide');	
	
}




if (true){
	$modalID 	= 'CanvasXpressModal_JSON';
	$modalTitle = "<h3>JSON Code for CanvasXpress</h3>";
	$modalBody	= '';
	
	$modalBody	.= "<h5>Method #1: Copy and paste the entire JSON file</h5>";
	$modalBody	.= "<p>After you have made the changes on the CanvasXpress graph, please right click and choose File -> Save as JSON. Use any editor to open the JSON file and make any changes. After you are done, please copy and paste the entire JSON file to the text box.</p>";
	
	$modalBody	.= "<br/>";
	
	$modalBody	.= "<h5>Method #2: Copy the 'afterRender' code only</h5>";
	$modalBody	.= "<p>If the JSON file is too large (e.g., contains too many data points), you may choose to copy the code after the afterRender section, like the following:</p>";
	
	$modalBody	.= "<div><img src='img/CanvasXpress_afterRender.png' class='img-fluid img-thumbnail' /></div>";

	$modalBody	.= "<br/>";

	$modalBody	.= 'Keep in mind that the CanvasXpress will load the afterRender section first, then apply the predefined settings afterward (because end users can adjust the predefined settings in the advanced options pop-up box). If you do not want your JSON code to be overridden, you may want to disable the predefined settings (e.g., sort by, color by, shape by etc) by setting them to none.';	
	
	$modalBody	.= "<br/>";
	
	
	echo printModal($modalID, $modalTitle, $modalBody, '', '', 'modal-dialog-wide');	
	
}


if (true){
	$modalID 	= 'CanvasXpressModal_JS';
	$modalTitle = "<h3>JavaScript Code for CanvasXpress</h3>";
	$modalBody	= '';
	
	$modalBody	.= "<p class='alert alert-danger'>Warning! This section is for advanced users only. It will allow you to execute JavaScript codes in users' browsers. Please make sure that your codes comply with your organization security standard.</p>";
	
	$modalBody	.= "<p>You can enter the JavaScript codes to change the plot settings. Please refer to the <a href='https://canvasxpress.org/html/general.html' target='_blank'>CanvasXpress documentation</a> for details. Here are few examples. Keep in mind that the variable name of the plot object is <strong>plotObj</strong>.</p>";
	
	$modalBody	.= "<br/>";
	
	$modalBody	.= "<h5>Increase the Sample Label Font Size</h5>";
	$modalBody	.= "<pre>plotObj.setFontAttributeSize('smpLabelScaleFontFactor', 'increase');</pre>";
	
	$modalBody	.= "<h5>Change the Sample Labe Font Factor</h5>";
	$modalBody	.= "<pre>plotObj.changeAttribute('smpLabelScaleFontFactor', '1');</pre>";
	
	$modalBody	.= "<h5>Change the Axis Tick Scale Factor</h5>";
	$modalBody	.= "<pre>plotObj.changeAttribute('axisTickScaleFontFactor', '0.7');</pre>";
	
	
	echo printModal($modalID, $modalTitle, $modalBody, '', '', 'modal-dialog-wide');	
	
}




?>



<script type="text/javascript">

$(document).ready(function(){
	
	$('#Gene_Expression_Plot_Default').change(function(){
		var currentValue = $(this).prop('checked');
		
		if (currentValue){
			$('#Gene_Expression_Plot_inputSection').hide();
		} else {
			$('#Gene_Expression_Plot_inputSection').show();
		}
		
	});
	
	$('#JSON').change(function(){

		var currentValue = $(this).val();

		if (currentValue != ''){
			
			$('#JSONFeedbackSection').empty();
			
			
			var data = new Object;
			data['JSON'] 		= currentValue;
			
			$.ajax({
				type: 'POST',
				url: 'app_ajax.php?action=parse_canvasxpress_json',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					$('#JSONFeedbackSection').html(responseText);
					$('#JSONFeedbackSection').show();
				}
			});
		}
	});
	
	$('.Plot_Columns').change(function(){
		var currentValue = $(this).prop('checked');
		
		var currentClass = $(this).attr('sibling');
		
		$('.' + currentClass).prop('checked', currentValue);
		
		updateSelectedSampleAttributes();
	});

	$('.Plot_Columns_Select_All_Trigger').change(function(){
		var currentValue = $(this).prop('checked');
		
		if (currentValue){
			$('.Plot_Columns').prop('checked', true);
			$('.Plot_Columns_Select_All_Trigger').prop('checked', true);
		} else {
			$('.Plot_Columns').prop('checked', false);
			$('.Plot_Columns_Select_All_Trigger').prop('checked', false);
		}
		
		updateSelectedSampleAttributes();
	});
	
	updateSelectedSampleAttributes();
	
});



function updateSelectedSampleAttributes(){

	var checked = new Object();
	var currentValue = '';
	var siblingClass = '';
	

	$('.Plot_Columns').each(function() {
		if ($(this).prop('checked')){
			currentValue = $(this).val();
			checked[currentValue] = 1;
			
			siblingClass = $(this).attr('sibling');
			$('.' + siblingClass).prop('checked', currentValue);
		}
	});
	
	var count = Object.keys(checked).length;
	
	
	var message = "<mark>(Selected: " + count + ")</mark>";
	
	$('#Plot_Columns_Message').html(message);
	
	
	return true;
		
}
</script>