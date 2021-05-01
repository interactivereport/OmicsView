<?php

include_once('config_init.php');

$sessionID = $_POST['sessionID'];

foreach($_SESSION['MetaAnalysis_GeneExpression'][$sessionID] as $tempKey => $tempValue){
	if (!isset($_POST[$tempKey])){
		$_POST[$tempKey] = $tempValue;	
	}
}


unset($error);
for ($i = 1; $i <= $_POST['Job']; $i++){
	$_POST["Comparison{$i}_Case"] = array_clean(splitData($_POST["Comparison{$i}_Case"]));
	$_POST["Comparison{$i}_Control"] = array_clean(splitData($_POST["Comparison{$i}_Control"]));
	
	unset($localSampleIDs);
	
	foreach(array('Case', 'Control') as $tempKey => $currentType){
		
		$currenttype = strtolower($currentType);
		
		if (array_size($_POST["Comparison{$i}_{$currentType}"]) <= 0){
			$error[$i][] = "The {$currenttype} sample IDs of Comparison #{$i} is empty.";
			$hasError = true;
		}
		
		foreach($_POST["Comparison{$i}_{$currentType}"] as $tempKeyX => $currentSampleID){
			
			$localSampleIDs[] = $currentSampleID;
			
			if (!isset($allSampleID[$currentSampleID])){
				$allSampleID[$currentSampleID] = $i;
			} else {
				$error[$i][] = "The {$currenttype} sample ID (<strong>{$currentSampleID}</strong>) has been used in comparison #{$allSampleID[$currentSampleID]}";
			}
		}
		
		$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($_POST["Comparison{$i}_{$currentType}"], '', $_POST['data_source_private_project_indexes']);
		
		

		if ($getSampleIDsExistenceInfo['hasMissing']){
			
			if ($getSampleIDsExistenceInfo['Missing_Count'] > 1){
				$error[$i][] = "The following {$currenttype} sample IDs are not available in the database: <strong>" . implode('</strong>, <strong>', $getSampleIDsExistenceInfo['Missing']) . "</strong>";
			} else {
				$error[$i][] = "The following {$currenttype} sample ID is not available in the database: <strong>" . implode(', ', $getSampleIDsExistenceInfo['Missing']) . "</strong>";
			}
		}
	}
	
	
	$getSampleIDsExistenceInfo = getSampleIDsExistenceInfo($localSampleIDs, '', $_POST['data_source_private_project_indexes']);
	
	$platformType[$i] = $getSampleIDsExistenceInfo['platformType'];

	if ($getSampleIDsExistenceInfo['hasMixedPlatform']){
		
		$error[$i][] = "This comparison contains sample IDs from microarray and RNA-Seq platforms";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Platform Type';
		$tableContent['Header'][2]		= 'Platform Name';
		$tableContent['Header'][3] 		= '# of Sample IDs';
		
		unset($currentRow);
		foreach($getSampleIDsExistenceInfo['platformSummary'] as $platformType => $tempValue1){
			
			foreach($tempValue1 as $platformName => $tempValue2){
				$currentRow++;
			
				$count = array_size($tempValue2);
				
				$modalID 	= "comparisonIDPlatform_{$i}_" . md5($platformName);
				$modalTitle = "<h4 class='modal-title'>{$platformName} ({$platformType})</h4>";
				$modalBody  = "<div class='row'>";
					$modalBody  .= "<div class='col-lg-10 col-sm-12'>";
						$modalBody  .= "<div class='text-nowrap'><strong>Platform Type:</strong> {$platformType}</div>";
						$modalBody  .= "<div class='text-nowrap'><strong>Platform Name:</strong> {$platformName}</div>";
						$modalBody  .= "<div class='text-nowrap'><strong>Sample IDs ({$count}):</strong></div>";
						$modalBody  .= "<ul class='small'><li>" . implode('</li><li>', $tempValue2) . "</li></ul>";
					$modalBody  .= "</div>";
				$modalBody  .= "</div>";
				
				echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');

				$tableContent['Body'][$currentRow]['Value'][1]	= $platformType;
				$tableContent['Body'][$currentRow]['Value'][2]	= $platformName;
				$tableContent['Body'][$currentRow]['Value'][3]	= "<a data-toggle='modal' href='#{$modalID}'>{$count}</a>";
			}
		}
		
		$errorExtra[$i][] = "<div style='margin-left:20px;'>" . printTableHTML($tableContent, 1, 1, 0, 'col-12') . "</div>";
	}
	
}

if (isset($error)){
	include('app_meta_analysis2_exe2_fail.php');
} else {
	include('app_meta_analysis2_exe2_success.php');
}

?>