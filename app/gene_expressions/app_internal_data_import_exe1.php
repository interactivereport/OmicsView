<?php

include_once('config_init.php');



buildInternalDataKnownMap();



if ($_POST['Zip_Enable']){

	
	foreach($_FILES as $tempKey => $tempValue){
		if ($tempKey != 'Zip_File'){
			unset($_FILES[$tempKey]);	
		}
	}
	
	
	if (is_file($_FILES['Zip_File']['tmp_name'])){
		$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Uploaded/User_{$APP_CONFIG['User_Info']['ID']}/" . getUniqueID() . '/';
		if (!is_dir($destinationDirectory)){
			mkdir($destinationDirectory, 0777, true);
		}
		
		$_FILES['Zip_File']['Path'] = tempnam($destinationDirectory, '');

		rename($_FILES['Zip_File']['tmp_name'], $_FILES['Zip_File']['Path']);
		
		$cmd = "cd {$destinationDirectory}; {$BXAF_CONFIG['UNZIP_BIN']} -j {$_FILES['Zip_File']['Path']}";
		shell_exec($cmd);
		
		$scanDir = scandir($destinationDirectory);
	
		foreach($scanDir as $tempKey => $tempValue){
			
			$currentFile = "{$destinationDirectory}/{$tempValue}";
			$currentFile = str_replace('//', '/', $currentFile);
			
			if (is_dir($currentFile)) continue;
			
			if ($tempValue == $_FILES['Zip_File']['Path']) continue;
			
			$pathInfo = pathinfo($currentFile);
			
			$pathInfo['filename'] = strtolower($pathInfo['filename']);

			unset($currentCategory);
			if ($APP_CONFIG['Internal_Data_Filename_Hint'][$pathInfo['filename']] != ''){
					
				$currentCategory = $APP_CONFIG['Internal_Data_Filename_Hint'][$pathInfo['filename']];
					
				$_FILES[$currentCategory]['name'] = $tempValue;
				$_FILES[$currentCategory]['tmp_name'] = $currentFile;
				$_FILES[$currentCategory]['Path'] = $currentFile;
				$_FILES[$currentCategory]['type'] = mime_content_type($currentFile);
			}
		}
	}
	
	
} else {
	unset($_FILES['Zip_File']);
}



unset($inputArray);
foreach($_FILES as $tempKey => $tempValue){
	
	if ($tempKey == 'Zip_File') continue;
	
	$_POST['Files'][$tempKey]['Name'] = $_FILES[$tempKey]['name'];
	$_POST['Files'][$tempKey]['Type'] = $_FILES[$tempKey]['type'];
	
	if ($destinationDirectory == ''){
		$destinationDirectory = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Uploaded/User_{$APP_CONFIG['User_Info']['ID']}/" . getUniqueID() . '/';
	}
	if (!is_dir($destinationDirectory)){
		mkdir($destinationDirectory, 0777, true);
	}
	
	$_POST['Files'][$tempKey]['Path'] = tempnam($destinationDirectory, '');
	
	if ($_FILES[$tempKey]['tmp_name'] != $_POST['Files'][$tempKey]['Path']){
		rename($_FILES[$tempKey]['tmp_name'], $_POST['Files'][$tempKey]['Path']);
	}
	chmod($_POST['Files'][$tempKey]['Path'], 0777);
}



$results = validateInternalData($_POST);



if (array_size($results['Error_Message']) > 0){
	
	echo "<hr/>";
	
	unset($message);
	
	$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " We have found the following error:</div>";
	
	foreach($results['Error_Message'] as $tempKey => $tempValue){
		$message .= "<div>&nbsp;&nbsp;&bull;&nbsp;&nbsp;{$tempValue} </div>";
	}
	echo getAlerts($message, 'danger');
	exit();
}

$sessionID = $_POST['sessionID'];
unset($_SESSION['Internal_Data'][$sessionID]);
$_SESSION['Internal_Data'][$sessionID] = $_POST;


if (true){
	unset($wizard);
	$wizard[1]['Icon'] 		= printFontAwesomeIcon('far fa-copy');
	$wizard[1]['Title']		= 'Upload Files';
	$wizard[1]['State']		= 2;
	$wizard[1]['Link']		= 'javascript:void(0);';
	$wizard[1]['Link-Class']= 'showForm1Trigger';
	
	
	$wizard[2]['Icon'] 		= printFontAwesomeIcon('far fa-check-square');
	$wizard[2]['Title']		= 'Verify Headers';
	$wizard[2]['State']		= 1;
	
	
	$wizard[3]['Icon'] 		= printFontAwesomeIcon('far fa-save');
	$wizard[3]['Title']		= 'Save to Database';
	$wizard[3]['State']		= 0;
	
	echo "<div class='form-group row'>";
		echo printWizard($wizard);
	echo "</div>";
}

if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-12'>";
			echo "<h2 class='pt-3'>2. Verify Headers</h2>";
			echo "<hr/>";
		echo "</div>";
	echo "</div>";
}


if ($results['Summary']['Unsure-Count'] == 0){
	
	
	$message = printFontAwesomeIcon('fas fa-check text-success') . " The system has found a match for every header. Please verify the table below to continue.";
	echo getAlerts($message, 'success', 'col-5');
		
	echo "<div class='form-group row'>";
		echo "<div class='col-6'>";
			echo "<button class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " Continue</button>";
			echo "&nbsp;&nbsp;<a href='javascript:void(0);' class='showForm1Trigger'>" . printFontAwesomeIcon('fas fa-reply') . ' Back</a>';
		echo "</div>";
	echo "</div>";
	
	
	echo "<hr/>";
}



unset($printed);

foreach($results as $currentKey => $currentInfo){
	
	if (!isset($APP_CONFIG['Internal_Data'][$currentKey])) continue;


	if ($printed){
		echo "<br/><hr/><br/>";	
	} else {
		echo "<br/>";	
	}
	
	echo "<h3>{$APP_CONFIG['Internal_Data'][$currentKey]['Name']}</h3>";
	
	$rowCount = array_size($currentInfo['readFirstFewLinesFromFile']['Body']);
	
	if ($rowCount == 1){
		echo "<p class='form-text'>The table below shows the first {$rowCount} row of data from the uploaded file.</p>";
	} else if ($rowCount > 1){
		echo "<p class='form-text'>The table below shows the first {$rowCount} rows of data from the uploaded file.</p>";
	}
	
	if ($currentInfo['Unsure-Count'] == 0){
		echo "<p class='form-text'>" . printFontAwesomeIcon('fas fa-check text-success') . " The system has found a match for each header. Please verify the following table:</p>";
	} else {
		
		if (is_internal_column_flexible($currentKey)){
			echo "<p class='form-text'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The system could not match the following headers. We have selected the closest match. Please try to select the closest one from our available list. If it is not available, please select <strong>Create Custom Name</strong> or <strong>Skip from Import</strong>.</p>";
			
			echo "<p>Set all of the following unknown headers to:&nbsp;";
				echo 
					"<a href='javascript:void(0);' id='mark_unmatched_columns_to_custom_{$currentKey}'>" . 
						printFontAwesomeIcon('far fa-file') . " Create Custom Name</a>" .
						" &nbsp; " . 
				    "<a href='javascript:void(0);' id='mark_unmatched_columns_to_skip_{$currentKey}'>" . 
						printFontAwesomeIcon('fas fa-times') . " Skip from Import</a>" . 
						" &nbsp; " . 
					"<a href='javascript:void(0);' id='mark_unmatched_columns_to_default_{$currentKey}'>" . 
						printFontAwesomeIcon('fas fa-redo') . " Restore to Closest Match</a>";
			echo "</p>";
			
		} else {
			echo "<p class='form-text'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The system could not match the following headers. We have selected the closest match. Please verify the headers below.</p>";
		}
		
		
		
		
		echo "<ol>";
			echo "<li>";
				echo implode("</li><li>", array_keys($currentInfo['Unsure']));
			echo "</li>";
		echo "</ol>";
		
	}
		
	
	
	unset($tableContent, $currentRow);
	$tableContent['Header'][1]		= 'Headers';
	
	foreach($currentInfo['readFirstFewLinesFromFile']['Header'] as $tempKey => $currentHeader){
		
		if ($results[$currentKey]['Best-Guest'][$currentHeader]){
			$icon = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger');
		} else {
			$icon = printFontAwesomeIcon('fas fa-check text-success');
		}
		
		$_SESSION['Internal_Data'][$sessionID]['Input_Headers'][$currentKey][$tempKey] = $currentHeader;
		$tableContent['Header'][] 	= "{$currentHeader} {$icon}";
	}

	$tableContent['Body'][1]['Value'][1]	= 'Map To:';
	
	
	unset($options);
	foreach($APP_CONFIG['Internal_Data'][$currentKey]['Headers'] as $currentSQL => $currentSQLInfo){
		$options[$currentSQLInfo['Name']] = "<option value='{$currentSQL}'>{$currentSQLInfo['Name']}</option>";
	}
	
	
	natksort($options);
	
	$options = implode(' ', $options);
	

	foreach($currentInfo['readFirstFewLinesFromFile']['Header'] as $tempKey => $currentHeader){
		
		if (is_internal_column_flexible($currentKey)){
			$tableContent['Body'][1]['Value'][] 	
				= "<select name='{$currentKey}_{$tempKey}' id='{$currentKey}_{$tempKey}'>
						<optgroup label='Action'>
							<option value=''>Skip from Import</option>
							<option value='BXAPP_CUSTOM_COLUMN'>Create Custom Name</option>
						</optgroup>
						
						<option value=''>----------------</option>						
						
						<optgroup label='Available'>
							{$options}
						</optgroup>
					</select>";
					
		} elseif ($_POST['Expression_Fomat'] && support_table_format_data($currentKey)){
			$tableContent['Body'][1]['Value'][] 	
				= "<select name='{$currentKey}_{$tempKey}' id='{$currentKey}_{$tempKey}'>
						<optgroup label='Action'>
							<option value=''>Skip from Import</option>
							<option value='BXAPP_DATA_EXPRESSION'>Expression Value</option>
							<option value='BXAPP_DATA_OTHER'>Other Value</option>
						</optgroup>
						
						<option value=''>----------------</option>						
						
						<optgroup label='Available'>
							{$options}
						</optgroup>
					</select>";
		} else {
			$tableContent['Body'][1]['Value'][] 	
				= "<select name='{$currentKey}_{$tempKey}' id='{$currentKey}_{$tempKey}'>
						<optgroup label='Action'>
							<option value=''>Skip from Import</option>
						</optgroup>
						
						<option value=''>----------------</option>						
						
						<optgroup label='Available'>
							{$options}
						</optgroup>
					</select>";
		}
	}
	
	$currentBodyKey = 1;
	


	
	foreach($currentInfo['readFirstFewLinesFromFile']['Body'] as $tempKey => $currentBody){
		$currentBodyKey++;
		
		$tableContent['Body'][$currentBodyKey]['Value'][1] = "{$tempKey}.";
		
		foreach($currentBody as $tempKey2 => $tempValue2){
			$tableContent['Body'][$currentBodyKey]['Value'][] = displayLongText($tempValue2, 30);
		}
		
	}
	
	$printed = 1;
	
	echo printTableHTML($tableContent, 1, 1, 0, 'col-12'); 
	
	echo "<br/>";
	
}


	
if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-6'>";
			echo "<input type='hidden' value='{$sessionID}' name='sessionID'/>";
			echo "<button  class='btn btn-primary' type='submit'>" . printFontAwesomeIcon('far fa-save') . " Continue</button>";
			echo "&nbsp;&nbsp;<a href='javascript:void(0);' class='showForm1Trigger'>" . printFontAwesomeIcon('fas fa-reply') . ' Back</a>';
		echo "</div>";
	echo "</div>";
}





?>

<script type="text/javascript">
$(document).ready(function(){

	$('#form_application1').hide();
	<?php foreach($results as $currentKey => $currentInfo){ ?>
		<?php foreach($currentInfo['Header-Map'] as $tempKey => $tempValue){ ?>
			<?php $currentID = "{$currentKey}_{$tempValue['Index']}"; ?>
			$('#<?php echo $currentID; ?>').val('<?php echo $tempValue['SQL']; ?>');
		<?php } ?>
			
		<?php foreach($currentInfo['Best-Guest'] as $tempKey => $tempValue){ ?>
			<?php $currentID = "{$currentKey}_{$tempValue['Index']}"; ?>
			$('#<?php echo $currentID; ?>').val('<?php echo $tempValue['SQL']; ?>');
		<?php } ?>
		
		<?php if (is_internal_column_flexible($currentKey)){ ?>
			$('#mark_unmatched_columns_to_custom_<?php echo $currentKey; ?>').click(function(){
				<?php foreach($currentInfo['Best-Guest'] as $tempKey => $tempValue){ ?>
					<?php $currentID = "{$currentKey}_{$tempValue['Index']}"; ?>
					$('#<?php echo $currentID; ?>').val('BXAPP_CUSTOM_COLUMN');
				<?php } ?>
			});
			
			$('#mark_unmatched_columns_to_skip_<?php echo $currentKey; ?>').click(function(){
				<?php foreach($currentInfo['Best-Guest'] as $tempKey => $tempValue){ ?>
					<?php $currentID = "{$currentKey}_{$tempValue['Index']}"; ?>
					$('#<?php echo $currentID; ?>').val('');
				<?php } ?>
			});
			
			$('#mark_unmatched_columns_to_default_<?php echo $currentKey; ?>').click(function(){
				<?php foreach($currentInfo['Best-Guest'] as $tempKey => $tempValue){ ?>
					<?php $currentID = "{$currentKey}_{$tempValue['Index']}"; ?>
					$('#<?php echo $currentID; ?>').val('<?php echo $tempValue['SQL']; ?>');
				<?php } ?>
			});
		<?php } ?>
		
	<?php } ?>

	
	<?php if ($results['Summary']['Unsure-Count'] == 0){ ?>
		$('#form_application2').submit();
	<?php } ?>
});

</script>