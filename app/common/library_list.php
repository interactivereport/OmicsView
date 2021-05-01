<?php

//Version: 2017-03-13
//derrick@bioinforx.com


//$category
//Gene, Sample, Project, Comparison
function get_list_records_by_category($category = NULL){
	
	$conn = bxaf_get_app_db_connection();
	
	$user_id = intval($_SESSION['BXAF_USER_LOGIN_ID']);
	
	$category = addslashes(trim($category));
	
	if ($category == ''){
		return false;	
	} else {
		$get_additional_accessible_lists_ids = get_additional_accessible_lists_ids();
		$additionalIDs = implode(',', $get_additional_accessible_lists_ids);
		
		if ($additionalIDs == ''){
			$sql = "SELECT * FROM `UserSavedLists` WHERE (`User_ID` = {$user_id}) AND (`Category` = '{$category}') ORDER BY `Name`";
		} else {
			$sql = "SELECT * FROM `UserSavedLists` WHERE (`Category` = '{$category}') AND ((`User_ID` = {$user_id}) OR (`ID` IN ({$additionalIDs}))) ORDER BY `Name`";
		}
		
		$results = $conn->GetAssoc($sql);

		foreach($results as $tempKey => $tempValue){

		
			$results[$tempKey]['canAccess'] = 0;
			$results[$tempKey]['canUpdate'] = 0;
		
			if ($results[$tempKey]['User_ID'] == $user_id){
				$results[$tempKey]['canAccess'] = 1;
				$results[$tempKey]['canUpdate'] = 1;
				$results[$tempKey]['isOwner'] 	= 1;
			} elseif (general_is_admin_user()){
				$results[$tempKey]['canAccess'] = 1;
			} elseif (in_array($tempKey, $get_additional_accessible_lists_ids)){
				$results[$tempKey]['canAccess'] = 1;
			}
			
			
			
			if ($results[$tempKey]['canAccess']){
				$results[$tempKey]['ID']	= $tempKey;
				$results[$tempKey]['Items'] = json_decode($tempValue['Items'], true);
				$results[$tempKey]['User'] 	= general_get_user_info($tempValue['User_ID']);
			} else {
				unset($results[$tempKey]);
			}
			
			
		}
		
		return $results;
	} 
	
}


function get_list_record_by_list_id_and_category($id = NULL, $category = NULL){
	
	$conn = bxaf_get_app_db_connection();
	
	$user_id = intval($_SESSION['BXAF_USER_LOGIN_ID']);
	$id 	= intval($id);
	
	$category = addslashes(trim($category));
	$get_additional_accessible_lists_ids = get_additional_accessible_lists_ids();
	
	if ($get_additional_accessible_lists_ids == ''){
		if ($category != ''){
			$sql = "SELECT * FROM `UserSavedLists` WHERE (`User_ID` = {$user_id}) AND (`ID` = {$id}) AND (`Category` = '{$category}')  ORDER BY `Name`";		
		} else {
			$sql = "SELECT * FROM `UserSavedLists` WHERE (`User_ID` = {$user_id}) AND (`ID` = {$id}) ORDER BY `Name`";	
		}
	} elseif (in_array($id, $get_additional_accessible_lists_ids)){
		if ($category != ''){
			$sql = "SELECT * FROM `UserSavedLists` WHERE (`ID` = {$id}) AND (`Category` = '{$category}')  ORDER BY `Name`";		
		} else {
			$sql = "SELECT * FROM `UserSavedLists` WHERE (`ID` = {$id}) ORDER BY `Name`";	
		}
	}
	

	$results = $conn->GetRow($sql);
	
	if (is_array($results) && sizeof($results) > 0){
		
		$results['canAccess'] = 0;
		$results['canUpdate'] = 0;
	
		if ($results['User_ID'] == $user_id){
			$results['canAccess'] 	= 1;
			$results['canUpdate'] 	= 1;
			$results['isOwner'] 	= 1;
			
		} elseif (general_is_admin_user()){
			$results['canAccess'] = 1;
		} elseif (in_array($id, $get_additional_accessible_lists_ids)){
			$results['canAccess'] = 1;
		}
		
		if (!$results['canAccess']){
			$get_additional_accessible_lists_ids = get_additional_accessible_lists_ids();
		
			if (in_array($id, $get_additional_accessible_lists_ids)){
				$results['canAccess'] = 1;
			}
		}
		
		
		if ($results['canAccess']){
			$results['Items'] 	= json_decode($results['Items'], true);
		} else {
			unset($results);	
		}
		
		
	}
	
	return $results;
	
}


function get_list_record_by_list_id($id = NULL){
	
	$conn = bxaf_get_app_db_connection();
	
	$user_id = intval($_SESSION['BXAF_USER_LOGIN_ID']);
	$id 	= intval($id);
	
	$sql = "SELECT * FROM `UserSavedLists` WHERE (`ID` = {$id})";

	$results = $conn->GetRow($sql);
	
	if (is_array($results) && sizeof($results) > 0){
		
		$results['canAccess'] = 0;
		$results['canUpdate'] = 0;
		
		if ($results['User_ID'] == $user_id){
			$results['canAccess'] 	= 1;
			$results['canUpdate'] 	= 1;
			$results['isOwner'] 	= 1;
			
		} elseif (general_is_admin_user()){
			$results['canAccess'] = 1;
		}
		
		if (!$results['canAccess']){
			$get_additional_accessible_lists_ids = get_additional_accessible_lists_ids();
		
			if (in_array($id, $get_additional_accessible_lists_ids)){
				$results['canAccess'] = 1;
			}
		}
			
	
	
		if ($results['canAccess']){
			
			$results['Items'] = json_decode($results['Items'], true);
		}

		if (!$results['canAccess']){
			unset($results);	
		}

		
	}
	
	return $results;
	
}


function get_list_record_by_list_ids($ids = NULL){
	
	$conn = bxaf_get_app_db_connection();
	
	if (is_array($ids) && sizeof($ids) > 0){
		$ids = array_unique($ids);
		$ids = array_filter($ids, 'trim');
	}
	
	$ids = implode(',', $ids);
	
	$sql = "SELECT * FROM `UserSavedLists` WHERE (`ID` IN ({$ids})) ORDER BY `Name`";	

	$results = $conn->GetAssoc($sql);
	
	foreach($results as $tempKey => $tempValue){

	
		$results['canAccess'] = 0;
		$results['canUpdate'] = 0;
	
		if ($results['User_ID'] == $user_id){
			$results['canAccess'] 	= 1;
			$results['canUpdate'] 	= 1;
			$results['isOwner'] 	= 1;
		} elseif (general_is_admin_user()){
			$results['canAccess'] = 1;
		}
		
		
		if ($results['canAccess']){
			$results[$tempKey]['ID']	= $tempKey;
			$results[$tempKey]['Items'] = json_decode($tempValue['Items'], true);

			$results[$tempKey]['User'] 	= general_get_user_info($tempValue['User_ID']);
		} else {
			unset($results[$tempKey]);
		}
		
		
	}
	
	return $results;
	
}


//$category
//Gene, Sample, Project, Comparison
function check_list_name_exist($name = NULL, $category = NULL, $id = NULL){
	
	$conn = bxaf_get_app_db_connection();
	
	$name 		= addslashes(trim($name));
	$category 	= addslashes(trim($category));
	$user_id 	= intval($_SESSION['BXAF_USER_LOGIN_ID']);
	$id			= intval($id);
	
	
	
	$get_additional_accessible_lists_ids = get_additional_accessible_lists_ids();
	$get_additional_accessible_lists_ids = implode(',', $get_additional_accessible_lists_ids);
	
	if ($get_additional_accessible_lists_ids == ''){
		$sql = "SELECT `ID` FROM `UserSavedLists` WHERE (`User_ID` = {$user_id}) AND (`Name` = '{$name}') AND (`Category` = '{$category}')";
	} else {
		$sql = "SELECT `ID` FROM `UserSavedLists` WHERE ((`User_ID` = {$user_id}) OR (`ID` IN ({$get_additional_accessible_lists_ids}))) AND (`Name` = '{$name}') AND (`Category` = '{$category}')";
	}
	
	
	$results = $conn->GetOne($sql);
	
	if ($results <= 0){
		return false;	
	} else {
		if ($results == $id) {
			return false;	
		} else {
			return true;	
		}
	}
	
}


function delete_list($id = NULL){
	
	$conn = bxaf_get_app_db_connection();
	
	$user_id = intval($_SESSION['BXAF_USER_LOGIN_ID']);
	$id 	= intval($id);
	
	if (($user_id > 0) && ($id > 0)){
		$sql = "DELETE FROM `UserSavedLists` WHERE (`User_ID` = {$user_id}) AND (`ID` = {$id})";
		$conn->Execute($sql);
		
		
	}
	
	return true;
	
}


function get_additional_accessible_lists_ids(){
	
	$conn = bxaf_get_app_db_connection();
	
	$user_id = intval($_SESSION['BXAF_USER_LOGIN_ID']);
	
	$results = array();
	
	
	return $results;
	
}

?>