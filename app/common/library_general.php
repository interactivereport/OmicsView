<?php

function general_find_file($basepath = NULL, $file_name = NULL){
	global $BXAF_CONFIG;
	
	$find = $BXAF_CONFIG['FIND_BIN'];
	
	if ($find == ''){
		$find = '/bin/find';	
	}
	
	$cmd = "{$find} {$basepath} -name '{$file_name}'";
	
	return shell_exec($cmd);
}


function general_is_admin_user($email = ''){
	
	global $BXAF_CONFIG, $APP_GLOBAL;
	
	if ($email == ''){
		$email = $_SESSION['User_Info']['Email'];
	}
	
	if ($BXAF_CONFIG['API'] || $APP_GLOBAL['API']){
		return true;
	}
	
	if (in_array($email, $BXAF_CONFIG['Admin_User_Email'])){
		return true;	
	} else {
		return false;
	}
	
}

function general_array_clean($array = NULL, $addslashes = 0, $unique = 1, $sort = 0, $preserveKey = 0){

	if (sizeof($array) > 0){
		$array = array_map('trim', $array);
		if ($addslashes){
			$array = array_map('addslashes', $array);
		}
		$array = array_filter($array, 'strlen');
		if ($unique){
			$array = array_unique($array);
		}
		
		if ($sort){
			natcasesort($array);	
		}
		
		if (!$preserveKey){
			$array = array_values($array);
		}
	}
	
	return $array;
}


function general_printr($array = NULL){
	return "<pre>" . print_r($array, true) . "</pre>";
}


function general_get_user_info($ID = NULL){
	
	global $BXAF_CONFIG;
	
	if (isset($_SESSION['App_Cache'][__FUNCTION__][$ID])){
		return $_SESSION['App_Cache'][__FUNCTION__][$ID];
	}
	
	if ($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'sqlite'){
		$BXAF_CONN = bxaf_get_user_db_connection();
	
		$SQL = "SELECT `ID`, `Login_Name`, `Name`, `First_Name`, `Last_Name`, `Email` FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN']} WHERE (ID = {$ID})";
		$results = $BXAF_CONN->query($SQL);
	
		if ($results){
			while($row = $results->fetchArray(SQLITE3_ASSOC)){
				$data = $row;
			}
		}

		
		$BXAF_CONN->close();
	
	} else if($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'mysql'){
	
		$BXAF_CONN = bxaf_get_user_db_connection();
	
		$SQL = "SELECT `ID`, `Login_Name`, `Name`, `First_Name`, `Last_Name`, `Email` FROM `{$BXAF_CONFIG['TBL_BXAF_LOGIN']}` WHERE (ID = {$ID})";
		$data = $BXAF_CONN->get_all($sql);
	}
	
	
	
	if ($data['Email'] != ''){
		$data['Display'] = "{$data['Name']} ({$data['Email']})";
	} else {
		$data['Display'] = "{$data['Name']}";
	}
	
	
	$_SESSION['App_Cache'][__FUNCTION__][$ID] = $data;
	
	return $data;
}



function general_split_data($string = NULL){
	
	if (!is_array($string)){
		$string = trim($string);
		
		$array = explode("\n", $string);
	
		$array = general_array_clean($array);
		
		foreach($array as $tempKey => $tempValue){
			$tempArray = explode(',', $tempValue);
			
			foreach($tempArray as $tempKey2 => $tempValue2){
				$results[] = $tempValue2;	
			}
		}
	} else {
		$results = $string;	
	}
	
	$results = general_array_clean($results);
	
	return $results;

}

function general_array_size($array = NULL){
	if (is_array($array)){
		return intval(sizeof($array));
	} else {
		return 0;	
	}
}

function general_ends_with($haystack = NULL, $needle = NULL){
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

//1: Opened; 2: Closed
function general_should_expand_left_menu_by_default(){

	global $BXAF_CONFIG, $BAXF_CACHE;
	
	if (isset($BAXF_CACHE[__FUNCTION__])){
		return $BAXF_CACHE[__FUNCTION__];
	}
	
	if (isset($_SESSION['User_Settings']['Left_Menu_Expanded'])){
		if ($_SESSION['User_Settings']['Left_Menu_Expanded'] == '1'){
			$results = true;
		} else {
			$results = false;
		}
		
		return $results;
	}
	
	$conn 			= bxaf_get_app_db_connection();
	$userID			= intval($_SESSION['User_Info']['ID']);
	
	$SQL 		= "SELECT `Detail` FROM `UserPreference` WHERE (`User_ID` = {$userID}) AND `Category` = 'Left_Menu_Expanded'";
	$results 	= $conn->GetOne($SQL);
	
	if ($results == ''){
		$SQL 		= "SELECT `Detail` FROM `UserPreference` WHERE (`User_ID` = -1) AND `Category` = 'Left_Menu_Expanded'";
		$results 	= $conn->GetOne($SQL);
	}
	
	
	
	if ($results == ''){
		
		$results = boolval($BXAF_CONFIG['Left_Menu_Expanded']);
		
	} else {
	
		$results = unserialize($results);
		
		if ($results == '1'){
			$results = true;
		} else {
			$results = false;
		}
		
	}
	
	$BAXF_CACHE[__FUNCTION__] = $results;
		
	return $results;
	
}

function general_get_unique_id(){

	return md5(mt_rand() . '_' . microtime(true));
}

function general_is_guest_user(){
	global $BXAF_CONFIG;
	
	if (($BXAF_CONFIG['GUEST_ACCOUNT'] != '') && ($BXAF_CONFIG['GUEST_ACCOUNT'] == $_SESSION['User_Info']['Login_Name'])){
		return true;	
	} else {
		return false;	
	}
}

function general_guest_account_readonly(){
	global $BXAF_CONFIG;
	
	if (general_is_guest_user() && $BXAF_CONFIG['GUEST_ACCOUNT_READONLY']){
		return true;		
	} else {
		return false;	
	}
}

function can_update_record($dataArray){

	global $BXAF_CONFIG;
	
	if (isset($dataArray['Is_Private']) && (!$dataArray['Is_Private'])) return false;
	
	if (!$BXAF_CONFIG['BULK_UPDATE']) return false;
	
	if (general_is_admin_user()) return true;
	
	$userID = intval($_SESSION['User_Info']['ID']);
	
	if (($userID > 0) && ($userID == $dataArray['User_ID'])){
		return true;
	}
	
	return false;
	
}

?>