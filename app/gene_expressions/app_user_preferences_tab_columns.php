<?php


$currentUserPreferenceKey 	= $APP_CONFIG['APP']['List_Category'][$currentCategory]['Preference_Key'];
$currentTable 				= $APP_CONFIG['APP']['List_Category'][$currentCategory]['Table'];
$currentSectionID			= "{$currentCategory}_ColumnSection";
$currentDictionary			= $APP_CONFIG['DB_Dictionary'];





unset($currentColumns);
foreach($currentDictionary[$currentTable]['SQL'] as $currentSQL => $currentSQLInfo){
	

	if ($currentSQLInfo['HideFromSearch']) continue;
	if ($currentSQLInfo['HideFromOption']) continue;
	
	
	$currentTitle = $currentSQLInfo['Title'];
	
	if ($currentSQLInfo['Title_Long'] != ''){
		$currentTitle = $currentSQLInfo['Title_Long'];	
	}
	
	if (in_array($currentSQL, $getUserSettings[$currentUserPreferenceKey])){
		$currentColumns['Selected'][$currentSQL] = $currentTitle;
	} else {
		$currentColumns['Available'][$currentSQL] = $currentTitle;
	}
}


if (array_size($currentColumns['Available']) > 0){
	natcasesort($currentColumns['Available']);
	
	if ($currentCategory == 'Comparison'){
		
		unset($temp);
		foreach($currentColumns['Available'] as $tempKeyX => $tempValueX){

			if (strpos($tempValueX, '#') === 0){
				$temp['Other'][$tempKeyX] = $tempValueX;
			} elseif (strpos($tempKeyX, 'Case') === 0){
				$temp['Case'][$tempKeyX] = $tempValueX;	
			} elseif (strpos($tempKeyX, 'Control') === 0){
				$temp['Control'][$tempKeyX] = $tempValueX;	
			} else {
				$temp['Other'][$tempKeyX] = $tempValueX;
			}
		}
		
		unset($currentColumns['Available']);
		foreach($temp['Other'] as $tempKeyX => $tempValueX){
			$currentColumns['Available'][$tempKeyX] = $tempValueX;
		}
		
		foreach($temp['Case'] as $tempKeyX => $tempValueX){
			$currentColumns['Available'][$tempKeyX] = $tempValueX;
		}
		
		foreach($temp['Control'] as $tempKeyX => $tempValueX){
			$currentColumns['Available'][$tempKeyX] = $tempValueX;
		}
		
		
		
	}

}


if (array_size($currentColumns['Available']) > 60){

	$tempSize = ceil(array_size($currentColumns['Available'])/5);
	$currentColumns['Available'] = array_chunk($currentColumns['Available'], $tempSize, true);
	
	$rowClass = 'col-lg-2 col-md-2 col-sm-12';
	
} elseif (array_size($currentColumns['Available']) > 40){

	$tempSize = ceil(array_size($currentColumns['Available'])/3);
	$currentColumns['Available'] = array_chunk($currentColumns['Available'], $tempSize, true);
	
	$rowClass = 'col-lg-3 col-md-3 col-sm-12';
	
} elseif (array_size($currentColumns['Available']) > 20){
	
	$tempSize = ceil(array_size($currentColumns['Available'])/2);
	$currentColumns['Available'] = array_chunk($currentColumns['Available'], $tempSize, true);
	
	$rowClass = 'col-lg-4 col-md-4 col-sm-12';
} else {
	
	$temp = $currentColumns['Available'];
		
	unset($currentColumns['Available']);
	$currentColumns['Available'][0] = $temp;
	$rowClass = 'col-lg-4 col-md-4 col-sm-12';
}



echo "<br/>";
echo "<h2>{$APP_CONFIG['APP']['List_Category'][$currentCategory]['Name']} Display Options</h2>";
echo "<p class='form-text'>Please drag the columns between the panels.</p>";
echo "<br/>";

unset($modalBody);
$modalBody  .= "<div class='row table_setting_modal' id='{$currentSectionID}_Body'>";
	$modalBody  .= "<div class='{$rowClass} small'>";
		$modalBody  .= "<h5>Displayed</h5>";
		$modalBody  .= "<ul class='list-selected list-group {$currentSectionID}_list-group-sortable-connected list-group-sortable-exclude' id='{$currentSectionID}_Selected' xstyle='min-height:150px;'>";
			foreach($currentColumns['Selected'] as $currentColumn => $currentColumnTitle){
				$modalBody  .= "<li class='list-group-item'><input type='hidden' value='{$currentColumn}'/>{$currentColumnTitle}</li>";
			}
			
			$modalBody .= "<li class='list-group-item disabled'>Drag to here</li>";
		$modalBody  .= "</ul>";
	$modalBody  .= "</div>";

			
	$modalBody  .= "<div class='{$rowClass} small'>";
		$modalBody  .= "<h5>Available</h5>";
		$modalBody  .= "<ul class='list-available list-group {$currentSectionID}_list-group-sortable-connected list-group-sortable-exclude' id='{$currentSectionID}_Available' xstyle='min-height:150px;'>";
			foreach($currentColumns['Available'][0] as $currentColumn => $currentColumnTitle){
				$modalBody  .= "<li class='list-group-item'><input type='hidden' value='{$currentColumn}'>{$currentColumnTitle}</li>";
			}
		$modalBody  .= "</ul>";
	$modalBody  .= "</div>";
	
	for($i = 1; $i <= array_size($currentColumns['Available']); $i++){
		$modalBody  .= "<div class='{$rowClass} small'>";
			$modalBody  .= "<h5>&nbsp;</h5>";
			$modalBody  .= "<ul class='list-available list-group {$currentSectionID}_list-group-sortable-connected list-group-sortable-exclude' id='{$currentSectionID}_Available' xstyle='min-height:150px;'>";
				foreach($currentColumns['Available'][$i] as $currentColumn => $currentColumnTitle){
					$modalBody  .= "<li class='list-group-item'><input type='hidden' value='{$currentColumn}'>{$currentColumnTitle}</li>";
				}
			$modalBody  .= "</ul>";
		$modalBody  .= "</div>";
	}
	
$modalBody  .= "</div>";

echo $modalBody;


?>
<script type="text/javascript">
$(document).ready(function(){
	

	$('.<?php echo $currentSectionID; ?>_list-group-sortable-connected').sortable({
		placeholderClass: 'list-group-item',
		items: ':not(.disabled)',
		connectWith: '.<?php echo $currentSectionID; ?>_connected'
	});
	
});
</script>