<?php

include_once('config_init.php');

if (has_public_comparison_data()){
	if ($_POST['Data_Source'] != ''){
		saveColumnPreferences('Data_Source', $_POST['Data_Source']);
		
		if (isAdminUser()){
			if ($_POST['default']){
				saveColumnPreferences($key, $_POST['Data_Source'], -1);
			}
		}
		
	}
}


if ($_POST['Gene_Data_Type'] != ''){
	saveColumnPreferences('Gene_Data_Type', $_POST['Gene_Data_Type']);
	
	if (isAdminUser()){
		if ($_POST['default']){
			saveColumnPreferences($key, $_POST['Gene_Data_Type'], -1);
		}
	}
	
}


if ($_POST['Left_Menu_Expanded'] != ''){
	saveColumnPreferences('Left_Menu_Expanded', $_POST['Left_Menu_Expanded']);
	
	if (isAdminUser()){
		if ($_POST['default']){
			saveColumnPreferences($key, $_POST['Left_Menu_Expanded'], -1);
		}
	}
	
}


foreach($APP_CONFIG['APP']['User_Preferences']['TBL_PREFERENCE_ALL_OPTIONS'] as $key => $tempValue){
	$value = array_clean($_POST[$key]);
	saveColumnPreferences($key, $value);
	
	if (isAdminUser()){
		if ($_POST['default']){
			saveColumnPreferences($key, $value, -1);
		}
	}
	
}


foreach($APP_CONFIG['APP']['List_Category'] as $currentCategory => $currentCategoryInfo){
	
	if (($currentCategory == 'Comparison') && ($BXAF_CONFIG['HIDE_Comparison_Tools'])){
		continue;	
	}
	
	$key = $currentCategoryInfo['Preference_Key'];
	
	unset($value);
	
	for ($i = 1; $i <= $_POST["{$currentCategory}_Count"]; $i++){
		$value[] = $_POST["{$currentCategory}_{$i}"];
	}
	
	$value = array_clean($value);
	
	
	
	
	if (array_size($value) > 0){
		saveColumnPreferences($key, $value);
		
		if (isAdminUser()){
			if ($_POST['default']){
				saveColumnPreferences($key, $value, -1);
			}
		}
		
	}
	
}

$message = printFontAwesomeIcon('fas fa-check text-success') . " The personal preferences have been saved.";
echo getAlerts($message, 'success');



?>