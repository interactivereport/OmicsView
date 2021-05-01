<?php
include_once('config_init.php');

$category 			= $_GET['category'];
$listName			= $_GET['input_name'];
$listClass			= $_GET['input_class'];
$preSelectedListID 	= $_GET['pre_selected_list_id'];
$target_id			= $_GET['target_id'];
$instance_id		= $_GET['instance_id'];

if ($listName == ''){
	$listName = 'list';	
}


if ($APP_CONFIG['APP']['List_Category'][$category]['Name'] == ''){
	$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The list category does not exist.</p>";
	$hasError = 1;
}


$categoryLowerCase		= strtolower($category);

if (!$hasError){
	$lists = get_list_records_by_category($category);
	
	
	if (!isset($lists[$preSelectedListID])){
		$preSelectedListID = 0;	
	}
	
	if (array_size($lists) <= 0){
		$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " There are no " . strtolower($APP_CONFIG['APP']['List_Category'][$category]['Page_Titles']) . " available.</p>";
		$hasError = 1;
	}
}


if ($hasError){
	echo $message;
} else {
	
	unset($tableContent);
	
	$tableContent['Header'][1]				= '&nbsp;';
	$tableContent['Header'][2]				= $APP_CONFIG['APP']['List_Category'][$category]['Title'];
	$tableContent['Header'][3] 				= 'Count';
	$tableContent['Header'][4] 				= 'Actions';
	
	
	foreach($lists as $listID => $listContent){
		
		unset($checked);
		
		if ($preSelectedListID == $listID){
			$checked = 'checked';
		}
		
		unset($actions);
		$actions[] = "<a href='{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_review.php?ID={$listID}' target='_blank'>" . printFontAwesomeIcon('fas fa-list') . "Review</a>";


		$tableContent['Body'][$listID]['Value'][1]	= "<input type='radio' id='{$instance_id}{$listName}_{$listID}' name='{$instance_id}{$listName}' class='{$listClass}' value='{$listID}' {$checked}/>";
		$tableContent['Body'][$listID]['Value'][2]	= "<a href='javascript:void(0);' class='{$listClass}_Name'  listid='{$listID}' >{$listContent['Name']}</a>";
		$tableContent['Body'][$listID]['Value'][3]	= $listContent['Count'];
		$tableContent['Body'][$listID]['Value'][4]	= implode(' &nbsp; ', $actions);
		
		$sql_column_human 	= $APP_CONFIG['APP']['List_Category'][$category]['Column_Human'];
		$valueToPrint 		= implode("\n", $listContent['Items'][$sql_column_human]);
		
		echo "<textarea style='display:none;' hidden id='{$instance_id}{$categoryLowerCase}_list_content_{$listID}'>{$valueToPrint}</textarea>";
	}
	
	echo printTableHTML($tableContent, 1, 1, 1);
	
	
}

?>