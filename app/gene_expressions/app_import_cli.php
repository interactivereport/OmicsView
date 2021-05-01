<?php

if (php_sapi_name() !== 'cli'){	
	exit();
}

include_once('config_init.php');



//mysqlimport -u root -px xxxxxxx db_diseaseland_20161207 --local GeneLevelExpression.txt --ignore-lines 1

set_time_limit(0);
$overallStartTime = microtime(true);
$overallMemoryUsage = memory_get_usage();

//There are x rows in the table already.
//Set the following value to x+1
$startImportFromRow = 0;


$argvLength = array_size($argv);

for ($i = 1; $i < $argvLength; $i++){
	
	$pathinfo = pathinfo("./" . $argv[$i]);
	$filename = $pathinfo['filename'];
	
	if ((isset($APP_CONFIG['Table'][$filename])) || isset($APP_CONFIG['DB_Dictionary'][$filename])){
		
		if ($APP_CONFIG['DB_Dictionary'][$filename]['Empty_Table']) continue;
	
		if ((strpos($argv[$i], '/') === 0) || (strpos($argv[$i], './') === 0)){
			$candidates[$filename] = "{$argv[$i]}";	
		} else {
			$candidates[$filename] = "./{$argv[$i]}";	
		}
	}

}


$mode = 'cli';

include('app_import_exe_processor.php');

$overallTime = round(microtime(true) - $overallStartTime, 2) . ' seconds';


if ($count > 0){
	
	echo "\n*****************************************************************\n";
	echo implode(' ', $argv) . "\n";
	echo "Success! Your data has been imported.\n";	
	echo "Time Spent: {$overallTime}\n";
	echo "*****************************************************************\n\n";
	
} else {
	
	echo "Error! No data has been imported. Please verify your file and try again.\n";
}

?>