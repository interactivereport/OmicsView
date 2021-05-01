<?php


function putSQLCache($key, $value, $table = '', $category = '', $json_decode_assoc = 1, $expire = 0){

	global $APP_CONFIG;
	
	$dataArray['Key'] 			= trim($key);
	
	if ($dataArray['Key'] == '') return false;
	
	if (empty($value)){
		return false;
	}
	
	$cacheResult = getSQLCache($key);
	if ($cacheResult !== false){
		return false;
	}
	
	
	$dataArray['Table_Name']		= trim($table);
	$dataArray['Category']			= trim($category);

	
	if ($dataArray['Category'] == 'URL'){
		
		if ($APP_CONFIG['REDIS_ENABLE']){
			$enableRedis 	= true;
		}
		
		$enableSQL		= true;
	} else {
		
		if ($APP_CONFIG['REDIS_ENABLE']){
			$enableRedis 	= true;
			$enableSQL		= false;
		} else {
			$enableRedis 	= false;
			$enableSQL		= true;
		}
		
	}
	

	if ($enableRedis){
		$dataArray['Json_Decode_Assoc'] = 3;
		$dataArray['Value'] 			= $value;
		putRedisCache(array($key => $dataArray), $expire);
		
	}
	
	if ($enableSQL){

		$dataArray['Json_Decode_Assoc']	= $json_decode_assoc;		
		if ($json_decode_assoc == 2){
			$dataArray['Value'] 		= bzcompress(serialize($value));
		} else {
			$dataArray['Value'] 		= bzcompress(json_encode($value));
		}
		
		$SQL = getInsertSQLQuery($APP_CONFIG['Table']['Cache'], $dataArray, '', 0, 1, 1);
		
		$conn 			= bxaf_get_app_db_connection();
		$results = $conn->Execute($SQL);
	}
	
	return true;
	
	
}


function putSQLCacheWithoutKey($value, $table = '', $category = '', $json_decode_assoc = 1, $expire = 0){

	$key = sha1(serialize($value));
	
	putSQLCache($key, $value, $table, $category, $json_decode_assoc, $expire);
	
	return $key;
}


function getSQLCache($key, $process = 1){

	global $APP_CONFIG;
	
	$key = trim($key);
	
	$results = getRedisCache($key);
	
	if ($results === FALSE){
		$conn 			= bxaf_get_app_db_connection();
		$redisFail = true;
		$SQL = "SELECT * FROM {$APP_CONFIG['Table']['Cache']} WHERE `Key` = '{$key}'";
		$results = $conn->GetRow($SQL);
	}
	
	$value = $results['Value'];
	
	
	if (!is_null($value)){

		if ($process && ($results['Json_Decode_Assoc'] != 3)){
			
			$value = bzdecompress($value);
			
			if ($results['Json_Decode_Assoc'] == 1){
				$value = json_decode($value, true);
			} elseif ($results['Json_Decode_Assoc'] == 0){
				$value = json_decode($value);
			} elseif ($results['Json_Decode_Assoc'] == 2){
				$value = unserialize($value);
			} elseif ($results['Json_Decode_Assoc'] == 3){
				
			}
		}
		
		if (!is_null($value)){
			
			if ($redisFail){
				
				$results['Json_Decode_Assoc'] 	= 3;
				$results['Value'] 				= $value;
				
				putRedisCache(array($key => $results));
			}
			
			
			
			return $value;
		} else {
			return false;
		}
	} else {
		
		return false;
	}
}

function getRedisCache($key){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	if (!$APP_CONFIG['REDIS_ENABLE']) return false;
	
	$key = trim($key);
	
	if ($key == '') return false;
	
	$redisKey = getRedisKey($key);
	
	$results = $APP_CONFIG['REDIS_CONN']->get($redisKey);
	
	if ($BXAF_CONFIG['REDIS_COMPRESSION']){
		$results = bzdecompress($results);
		$results = unserialize($results);
	}

	return $results;
	
}



function getRedisKey($key){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	if ($BXAF_CONFIG['REDIS_COMPRESSION']){
		$key = "{$BXAF_CONFIG['APP_DB_NAME']}::{$key}::Compressed";
	} else {
		$key = "{$BXAF_CONFIG['APP_DB_NAME']}::{$key}::RAW";
	}

	return $key;
	
}

function putRedisCache($dataArray, $expire = 0){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	if (!$APP_CONFIG['REDIS_ENABLE']) return false;
	
	$expire = intval(abs($expire));
	
	if (array_size($dataArray) <= 0) return false;
	
	foreach($dataArray as $key => $value){
		$key = trim($key);
		if ($key == '') continue;
		if ($value == FALSE) continue;
	
		$redisKey = getRedisKey($key);
		$candidates[$redisKey] = $value;
		
		if ($BXAF_CONFIG['REDIS_COMPRESSION']){
			$candidates[$redisKey] = bzcompress(serialize($value));
		}
	}
	
	if (array_size($candidates) > 0){
		
		if ($expire == 0){
			return $APP_CONFIG['REDIS_CONN']->mset($candidates);
		} else {
			foreach($candidates as $key => $value){
				
				$APP_CONFIG['REDIS_CONN']->set($key, $value);
				$APP_CONFIG['REDIS_CONN']->setTimeout($key, $expire);
			}
			
			return true;
		}
		
	} else {
		return false;	
	}
}


//$scope
//0: Exclude URL
//1: Include URL
function clearCache($scope = 0){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	if ($scope == 0){
		$SQL = "DELETE FROM `{$APP_CONFIG['Table']['Cache']}` WHERE `Category` != 'URL'";
	} else {
		$SQL = "DELETE FROM `{$APP_CONFIG['Table']['Cache']}`";
	}
	
	$conn 			= bxaf_get_app_db_connection();
	$results = $conn->Execute($SQL);

	$cmd = "redis-cli KEYS '{$BXAF_CONFIG['APP_DB_NAME']}::*' | xargs redis-cli DEL";
	
	shell_exec($cmd);
	
	return true;
}




?>