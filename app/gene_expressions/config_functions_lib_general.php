<?php

function printrExpress($array, $sortKey = 0){
	if ($sortKey){
		natksort($array);
	}
	return "<pre>" . print_r($array, true) . "</pre>";
}

function printMsg($string){
	return "\n\n\n<p>{$string}</p>\n\n\n";
}

function natksort(&$array){
	if (array_size($array) > 0){
		uksort($array, 'strnatcasecmp');
	}
}

function array_size($array){
	if (is_array($array)){
		return intval(sizeof($array));
	} else {
		return 0;	
	}
}

function array_clean($array, $addslashes = 0, $unique = 1, $sort = 0, $preserveKey = 0){

	if (array_size($array) > 0){
		$array = array_map('trim', $array);
		if ($addslashes){
			$array = array_map('addslashes', $array);
		}
		$array = array_filter($array, 'strlen');
		if ($unique){
			$array = array_iunique($array);
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

function array_iunique($array) { 
    $lowered = array_map('strtolower', $array); 
    return array_intersect_key($array, array_unique($lowered)); 
} 

function naturalSort2DArray(&$array){
	
	if (!function_exists('naturalSort2DArrayCompare')){
		function naturalSort2DArrayCompare($a, $b){
			global $ORDER_ARRAY, $SORT_OPTIONS;
			$order = $ORDER_ARRAY;
		
			foreach($order as $key => $value){
				if (!isset($a[$key])) continue;
	
				unset($compareResult, $firm);
				
				if (is_numeric($a[$key]) && is_numeric($b[$key])){
					
					if (!$SORT_OPTIONS['abs']){
						if ($a[$key] >= $b[$key]){
							$compareResult = 1;
						} else {
							$compareResult = -1;	
						}
					} else {
						if (abs($a[$key]) >= abs($b[$key])){
							$compareResult = 1;
						} else {
							$compareResult = -1;	
						}
					}
				} elseif (strpos($a[$key], 'is_numeric') !== FALSE){
					
					$regex = '/<span(.*)?>(.*)?<\/span>/';
					
					preg_match($regex, $a[$key], $matches);
					$a_value = $matches[2];
					
					preg_match($regex, $b[$key], $matches);
					$b_value = $matches[2];
					
					if (is_numeric($a_value) && is_numeric($b_value)){
						if ($a_value >= $b_value){
							$compareResult = 1;
						} else {
							$compareResult = -1;	
						}
					} elseif (is_numeric($a_value) && !is_numeric($b_value)){
						$compareResult = -1;
						$firm = 1;
					} elseif (!is_numeric($a_value) && is_numeric($b_value)){
						$compareResult = 1;
						$firm = 1;
					} else {
						$compareResult = strnatcasecmp($a[$key], $b[$key]);
					}
					
				} else {
					$compareResult = strnatcasecmp($a[$key], $b[$key]);
				}
				
				if ($compareResult === 0) continue;
				
				$value = strtoupper(trim($value));
				
				if ($value === 'DESC'){
					if (!$firm){
						$compareResult = $compareResult*-1;
					}
				}
				
				return $compareResult;
			}
			
			return 0;
		}
	}

	uasort($array, 'naturalSort2DArrayCompare');
	
	return true;
}

if(!function_exists("array_column")){
    function array_column($array, $column_name){
        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
    }
}


function printTableHTML($tableContent, $compact = 1, $striped = 0, $tableOnly = 1, $divClass = 'col-lg-5 col-sm-12', $tableClass = ''){
	
	
	if ((array_size($tableContent['Header']) <= 0) && (array_size($tableContent['Body']) <= 0)) return false;
	
	$result = '';
	$result .= "<div class='table-responsive'>";
	
		if ($tableClass == ''){
			$tableClass = getTableClass($compact, $striped);
		}
		
		$result .= "<table class='{$tableClass}'>";

		
		if (isset($tableContent['Header'])){
			$result .= "<thead>";
				$result .= "<tr>";
				foreach($tableContent['Header'] as $tempKey => $tempValue){
					$result .= "<th>{$tempValue}</th>";
				}
				$result .= "</tr>";
			$result .= "</thead>";
		}
		
		
		if (isset($tableContent['Body'])){
			$result .= "<tbody>";
				
				foreach($tableContent['Body'] as $row => $rowDetails){
					$result .= "<tr class='{$rowDetails['Class']}'>";
						if (isset($tableContent['Header'])){
							foreach($tableContent['Header'] as $tempKey => $tempValue){
								$result .= "<td>{$rowDetails['Value'][$tempKey]}</td>";
							}
						} else {
							foreach($rowDetails['Value'] as $tempKey => $tempValue){
								$result .= "<td>{$tempValue}</td>";
							}
						}
					$result .= "</tr>";
				}

			$result .= "</tbody>";
		}
		
		$result .= "</table>";
		
	$result .= "</div>";
	
	
	if (!$tableOnly){
		$result = "<div class='row'><div class='{$divClass}'>{$result}</div></div>";
	}
	
	
	return $result;
	
	
}

function getTableClass($compact = 1, $striped = 0){

	$classes[] = 'table';
	$classes[] = 'table-bordered';
	
	if ($compact){
		$classes[] = 'table-sm';	
	}
	
	if ($striped){
		$classes[] = 'table-striped';
	}
	
	return implode(' ', $classes);
}

function printFontAwesomeIcon($icon, $quoteType = 1, $fixWidth = 1){
	
	global $APP_CONFIG;
	
	if ($fixWidth){
		$fixWidthClass = 'fa-fw';
	}

	if ($quoteType == 1){
		return "<i class='{$fixWidthClass} {$icon}' aria-hidden='true'></i>";
	} else {
		return "<i class=\"{$fixWidthClass} {$icon}\" aria-hidden=\"true\"></i>";
	}
}


function getAlerts($message, $type = 'danger', $class='col-lg-8 col-md-12 col-xs-12 col-sm-12'){

	$results .= "<div class='row'>";
		$results .=  "<div class='{$class}'>";
			$results .=  "<br/>";
			$results .=  "<div class='alert alert-{$type}'>";
				$results .=  $message;	
			$results .=  "</div>";
		$results .=  "</div>";
	$results .=  "</div>";	
	
	return $results;
}


function endsWith($haystack, $needle){
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}


function printModal($modalID, $modalTitle, $modalBody, $modalButtonText, $modalBodyClass = '', $modalDialogClass = '', $modalButtonID = '', $disableClose = 0){
	
	unset($results);
	
	if ($modalButtonText == '') $modalButtonText = 'Close';
	
	if ($modalID == ''){
		$modalID = 'Modal_' . getUniqueID();
	}
	
	
	$results .= "<div id='{$modalID}' class='modal fade' role='dialog'>";
		$results .= "<div class='modal-dialog {$modalDialogClass}' role='document'>";
			$results .= "<div class='modal-content'>";
				$results .= "<div class='modal-header'>";
					$results .= "{$modalTitle}";
					$results .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
				$results .= "</div>";
				
				$results .= "<div class='modal-body {$modalBodyClass}' style='overflow-x:hidden;'>{$modalBody}</div>";
				
				$results .= "<div class='modal-footer' style='border:none;'>";
				
					$disableClose = intval($disableClose);
					if ($disableClose == 0){
						$close = "data-dismiss='modal'";	
					} else {
						$close = '';	
					}
					
					if ($modalButtonID == ''){
						$modalButtonID = 'Modal_Button_' . getUniqueID();
					}
				
					$results .= "<button type='button' class='btn btn-primary'  id='{$modalButtonID}' {$close}>{$modalButtonText}</button>";
				$results .= "</div>";
				
			$results .= "</div>";
		$results .= "</div>";
	$results .= "</div>";	
	
	return $results;
	
}


function printConfirmation($modalID, $modalTitle, $modalBody, $modalButtonTextAction = 'Close', $modalButtonTextCancel = 'Cancel', $modalButtonActionClass = 'actionTrigger'){
	
	unset($results);
	
	if ($modalID == ''){
		$modalID = 'Modal_' . getUniqueID();
	}
	
	$results .= "<div id='{$modalID}' class='modal fade' role='dialog'>";
		$results .= "<div class='modal-dialog' role='document'>";
			$results .= "<div class='modal-content'>";
				$results .= "<div class='modal-header'>";
					$results .= "{$modalTitle}";
					$results .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
				$results .= "</div>";
				
				$results .= "<div class='modal-bodyX'>{$modalBody}</div>";
				
				$results .= "<div class='modal-footer' style='border:none;'>";
					$results .= "<a href='javascript:void(0);' class='btn {$modalButtonActionClass}'>{$modalButtonTextAction}</a>";
					$results .= "<button type='button' class='btn btn-default' data-dismiss='modal'>{$modalButtonTextCancel}</button>";
				$results .= "</div>";
				
			$results .= "</div>";
		$results .= "</div>";
	$results .= "</div>";	
	
	return $results;
	
}

function splitData($string){
	
	if (!is_array($string)){
		$string = trim($string);
		
		$array = explode("\n", $string);
	
		$array = array_clean($array);
		
		foreach($array as $tempKey => $tempValue){
			$tempArray = explode(',', $tempValue);
			
			foreach($tempArray as $tempKey2 => $tempValue2){
				$results[] = $tempValue2;	
			}
		}
	} else {
		$results = $string;	
	}
	
	$results = array_clean($results);
	
	return $results;

}


function startTimer($key){
	
	if (!isDebugMode()) return false;
	
	global $BXAPP_TIMER;
	
	$BXAPP_TIMER[$key][] = microtime(true);
	
	
	return true;
}



function processTimer($timer, $format = 0){
	
	global $APP_CONFIG;
	
	if (!isDebugMode()) return false;
	
	foreach($timer as $tempKey => $tempValue){
		
		if (!isset($total)){
			$total = $tempValue[0];
		}
		
		$timer[$tempKey] = round(abs($tempValue[1] - $tempValue[0]), 8);
	}
	
	if (!isset($total)){
		$total = $APP_CONFIG['StartTime'];	
	}
	
	$total = round(microtime(true) - $total, 5);
	
	
	foreach($timer as $tempKey => $tempValue){
		
		$results[$tempKey]['Time'] = $tempValue;
		$results[$tempKey]['Percentage'] = round($tempValue/$total*100, 2);
		
		if ($format){
			$currentCount++;
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = $tempKey;
			$tableContent['Body'][$currentCount]['Value'][] = $results[$tempKey]['Time'];
			$tableContent['Body'][$currentCount]['Value'][] = $results[$tempKey]['Percentage'];
		}
		
	}
	
	
	
	if (!$format){
		$results['Total'] = $total;
		return $results;
	} else {
		
		$tableContent['Header'][] = 'No.';
		$tableContent['Header'][] = 'Item';
		$tableContent['Header'][] = "<span class='text-nowrap'>Time (s)</span>";
		$tableContent['Header'][] = "<span class='text-nowrap'>Percentage (%)</span>";
		
		if ($format){
			$currentCount++;
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = 'Total';
			$tableContent['Body'][$currentCount]['Value'][] = "<strong>{$total}</strong>";
			$tableContent['Body'][$currentCount]['Value'][] = 100;

			
			$currentCount++;
			$memory_get_usage = intval(memory_get_usage(true)/(1024*1024)) .'MB';
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = 'memory_get_usage()';
			$tableContent['Body'][$currentCount]['Value'][] = $memory_get_usage;
			$tableContent['Body'][$currentCount]['Value'][] = '';
			

			$currentCount++;
			$memory_get_peak_usage = intval(memory_get_peak_usage(true)/(1024*1024)) .'MB';
			$tableContent['Body'][$currentCount]['Value'][] = "{$currentCount}.";
			$tableContent['Body'][$currentCount]['Value'][] = 'memory_get_peak_usage()';
			$tableContent['Body'][$currentCount]['Value'][] = $memory_get_peak_usage;
			$tableContent['Body'][$currentCount]['Value'][] = '';
			
		}
		
		return printTableHTML($tableContent, 1, 1);
	}
	
}

function isDebugMode(){
	return $_SESSION['DEBUG_MODE'];	
}

function displayLongText($string, $length = -1){
	
	global $BXAF_CONFIG;
	
	if ($length < 0){
		$length	= $BXAF_CONFIG['TEXT_LENGTH'];
	}
	
	if (strlen($string) >= $length){
		$string = substr($string, 0, $length) . '...';
	}
	
	return $string;
	
}

function isAdminUser($email = ''){
	
	global $BXAF_CONFIG;
	
	$email = trim($email);
	
	if ($email != ''){
		return in_array($email, $BXAF_CONFIG['Admin_User_Email']);
	} else {
		
		if (in_array($_SESSION['User_Info']['Email'], $BXAF_CONFIG['Admin_User_Email'])){
			return true;
		} elseif (in_array($_SESSION['User_Info']['Login_Name'], $BXAF_CONFIG['Admin_User_Email'])){
			return true;
		}
	}
	
	return false;
	
}

function getSVGCode($svg){
	
	if (strpos($svg, 'data:image/svg+xml,') === 0){

		$svg = str_replace('data:image/svg+xml,', '', $svg);
	
		return urldecode($svg);
	
	} else {
		
		return $svg;
	}
	
}

function guessFileDelimiter($file){
	
	if (!file_exists($file)){
		return false;
	}
	
	
	if (!is_file($file)){
		return false;
	}
	
	$fp = fopen($file, 'r');
	
	
	while (!feof($fp)){
		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (strpos('#', $currentLine) === 0) continue;
		
		
		$csv = str_getcsv(trim($currentLine), ',');
		$csv = array_map('trim', $csv);
		
		$tab = str_getcsv(trim($currentLine), "\t");
		$tab = array_map('trim', $tab);
		
		if (array_size($csv) > array_size($tab)){
			$delimiter = ',';
		} else {
			$delimiter = "\t";
		}
		
		break;

	}
	
	
	fclose($fp);
	
	return $delimiter;
	
}

function getFileHeader($file = '', $delimiter = ''){

	if (!file_exists($file)){
		return false;
	}
	
	if (!is_file($file)){
		return false;
	}
	
	$fp = fopen($file, 'r');
	

	while (!feof($fp)){
		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (strpos('#', $currentLine) === 0) continue;
		
		
		if ($delimiter == ''){
			$csv = str_getcsv(trim($currentLine), ',');
			$csv = array_map('trim', $csv);
			
			$tab = str_getcsv(trim($currentLine), "\t");
			$tab = array_map('trim', $tab);
			
			if (array_size($csv) > array_size($tab)){
				$delimiter = ',';
			} else {
				$delimiter = "\t";
			}
		}
		
		
		if (!isset($header)){
			$header = str_getcsv(trim($currentLine), $delimiter);
			
			$header = array_map('trim', $header);

			
			break;
		}
	}
	
	fclose($fp);
	
	return $header;
	
	
}


function readFirstFewLinesFromFile($file, $rowCount = 5, $combine = 1, $delimiter){

	
	if (!file_exists($file)){
		return false;
	}
	
	
	if (!is_file($file)){
		return false;
	}
	
	$fp = fopen($file, 'r');
	
	$delimiter = trim($delimiter);
	
	if ($delimiter == 'tab'){
		$delimiter = "\t";	
	} elseif ($delimiter == 'csv'){
		$delimiter = ',';	
	}
	
	/*
	if ($delimiter == ''){
		$delimiter = guessFileDelimiter($file);	
	}
	*/
	
	while (!feof($fp)){
		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (strpos('#', $currentLine) === 0) continue;
		
		
		if ($delimiter == ''){
			$csv = str_getcsv(trim($currentLine), ',');
			$csv = array_map('trim', $csv);
			
			$tab = str_getcsv(trim($currentLine), "\t");
			$tab = array_map('trim', $tab);
			
			if (array_size($csv) > array_size($tab)){
				$delimiter = ',';
			} else {
				$delimiter = "\t";
			}
		}
		
		
		if (!isset($header)){
			$header = str_getcsv(trim($currentLine), $delimiter);
			
			$header = array_map('trim', $header);
			
			$headerCount = array_size($header);
			
			$results['Header'] = $header;
			
			continue;
		} else {
			
			$currentRow = str_getcsv($currentLine, $delimiter);
			
			if ($headerCount == array_size($currentRow)){
				
				if ($rowCount > 0){
					if (++$currentRowCount <= $rowCount){
						if ($combine){
							$results['Body'][$currentRowCount] = array_combine($header, $currentRow);
						} else {
							$results['Body'][$currentRowCount] = $currentRow;
						}
					} else {
						break;	
					}
				} else {
					if ($combine){
						$results['Body'][] = array_combine($header, $currentRow);
					} else {
						$results['Body'][] = $currentRow;
					}
				}
				
				
			}
		}
	}
	
	
	fclose($fp);
	
	return $results;
	
}


function sanitizeColumnHeader($string){

	$string = str_replace('  ', ' ', $string);
	$string = str_replace(array('.', '[', ',', ' '), '_', $string);
	$string = str_replace('__', '_', $string);
	$string = str_replace(']', '', $string);
	$string = fixEncoding($string);
	$string = trim($string);
	
	return $string;
}

function sanitizeColumnHeader2($string){

	$string = sanitizeColumnHeader($string);
	$string = str_replace('_', '', $string);
	
	return $string;
}

function sanitizeJavaScriptValue($string){
	$string = trim($string);
	$string = str_replace('"', '', $string);
	return $string;	
}


function getUniqueID(){
	
	global $APP_CONFIG;
	
	return md5($APP_CONFIG['User_Info']['ID'] . '_' . microtime(true) . mt_rand());
}

function printWizard($wizard){

	$size = array_size($wizard);
	
	$results = '';
	
		$results .= "<div class='col-lg-12'>";
			
			$results .= "<br/>";
		
			$results .= "<table>";
			
				$results .= "<tr>";
					for ($i = 1; $i <= $size; $i++){
						
						if ($wizard[$i]['State'] == 0){
							$buttonClass 	= 'btn-outline-secondary';
							$disabled		= 'disabled';
						} elseif ($wizard[$i]['State'] == 1){
							$buttonClass 	= 'btn-primary';
							$disabled		= '';
						} elseif ($wizard[$i]['State'] == 2){
							$buttonClass 	= 'btn-outline-secondary';
							$disabled		= '';
						}
						
						$results .= "<td class='text-center'>";
						
							if ($wizard[$i]['Link'] == ''){
								$results .= "<button type='button' class='btn {$buttonClass} btn-circle btn-lg' {$disabled}>{$wizard[$i]['Icon']}</button>";
							} else {
								$results .= "<a href='{$wizard[$i]['Link']}' class='{$wizard[$i]['Link-Class']}' xstyle='cursor:pointer;'>";
									$results .= "<button role='button' class='btn {$buttonClass} btn-circle btn-lg' {$disabled}>{$wizard[$i]['Icon']}</button>";
								$results .= "</a>";
							}
						$results .= "</td>";
						
						
						if ($i != $size){
							$results .= "<td class='text-center'>";
								$results .= "<hr/>";
							$results .= "</td>";
						}
					}
				$results .= "</tr>";
				
				$results .= "<tr>";
					for ($i = 1; $i <= $size; $i++){
						
						unset($textIcon);
						
						if ($wizard[$i]['State'] == 2){
							$textIcon = printFontAwesomeIcon('fas fa-check text-success');
						}
						
						$results .= "<td class='text-center form-text'>";
							if ($wizard[$i]['Link'] == ''){
								$results .= "{$textIcon} {$wizard[$i]['Title']}";
							} else {
								$results .= "<a href='{$wizard[$i]['Link']}' class='{$wizard[$i]['Link-Class']}'>";
									$results .= "{$textIcon} {$wizard[$i]['Title']}";
								$results .= "</a>";
							}
						$results .= "</td>";
						
						
						if ($i != $size){
							$results .= "<td class='text-center'>";
								$results .= "<div style='width:100px;'>&nbsp;</div>";
							$results .= "</td>";
						}
					}

				$results .= "</tr>";
				
			$results .= "</table>";
			
			$results .= "<br/>";
			
		$results .= "</div>";


	return $results;	
	
}


function fromCamelCase($input) {
	
	$input = trim($input);
	
	if (preg_match('#[0-9]#',$input)){ 
		return $input;
	}
	
	$input = preg_split(
        '/(^[^A-Z]+|[A-Z][^A-Z]+)/',
        $input,
        -1, /* no limit for replacement count */
        PREG_SPLIT_NO_EMPTY /*don't return empty elements*/
            | PREG_SPLIT_DELIM_CAPTURE /*don't strip anything from output array*/
    );
	
	$input = trim(implode(' ', $input));
	$input = str_replace('  ', ' ', $input);
	
	return $input;
}

function fixEncoding($string){
	return recode_string("us..flat", $string);	
}

function ucwords2($title){
	
	$title = trim($title);
	
	$smallwordsarray = array( 'of','a','the','and','an','or','nor','but','is','if','then','else','when', 'at','from','by','on','off','for','in','out','over','to','into','with' );
	
	$words = explode(' ', $title);

	foreach ($words as $key => $word){
		if ($key == 0 || !in_array($word, $smallwordsarray)){
			$words[$key] = ucwords($word);
		}
	}
	
	// Join the words back into a string
	$newtitle = implode(' ', $words);
	
	$newtitle = str_replace(' Vs. ', ' vs. ', $newtitle);
	
	return $newtitle;
}


function string2number($string){
	$string = trim($string);
	
	if ($string == '') return '';
	
	if (is_numeric($string)){
		return $string;	
	} else {
		$strlen = strlen($string);
		
		$result = '';
		for ($i = 0; $i < $strlen; $i++){
			
			$currentChar = substr($string, $i, 1);
			
			if (is_numeric($currentChar)){
				$result .= $currentChar;
			} elseif ($currentChar == '.'){
				$result .= $currentChar;
			} elseif ($currentChar == ','){
				continue;
			} else {
				
				if ($result != ''){
					break;	
				}
				
			}
			
			
			
		}
		
		if (strlen($result) > 0){
			return floatval($result);	
		} else {
			return $result;
		}
	}
	
}

function getRandomRGBColor($identifier = '', &$colorLibrary = array(), $colorIndex = 0){
	
	global $APP_CONFIG;

	$colorProfile = $APP_CONFIG['APP']['Color_Template'][$colorIndex];
	
	if (!isset($colorLibrary[$identifier])){

		$currentUsed = array_size($colorLibrary);
		
		if ($currentUsed > array_size($colorProfile)){
			$currentUsed = $currentUsed % array_size($colorProfile);
		}
		
		
		if (($currentUsed == 0) || ($currentUsed == array_size($colorProfile))){
			$nextIndex = 0;
		} else {
			$nextIndex = $currentUsed;
		}
		
		
		$colorLibrary[$identifier] = $colorProfile[$nextIndex];
	}
	
	return $colorLibrary[$identifier];
	
}

function roundNumber($string, $decimal){
	
	$string = str_replace('E', 'e', $string);
	
	$temp = explode('e', $string);
	
	$numeric 	= $temp[0];
	$exponent 	= $temp[1];
	
	$results = round($numeric, $decimal);
	
	if ($exponent != ''){
		$results .= 'e' . $exponent;
	}
	
	return $results;
}

function formatFileSize($bytes){
	
	if ($bytes >= 1073741824){
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576){
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024){
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1){
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1){
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}
	
	return $bytes;
}

?>