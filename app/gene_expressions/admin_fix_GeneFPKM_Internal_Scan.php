<?php

if (php_sapi_name() !== 'cli'){	
	exit();
}

include_once('config_init.php');

set_time_limit(0);
ini_set('memory_limit', -1);

echo "\n";
echo "DiseaseData Processing Tool (Version: 2020-03-27)" . "\n\n";

ini_set('auto_detect_line_endings', true);
set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(0);
ignore_user_abort(true);

$startTime		 	= microtime(true);
$startMemoryUsage 	= memory_get_usage();
$argvLength 		= sizeof($argv);
$action				= abs(intval($argv[1]));

if ($argvLength != 2){
	echo "This tool go through all internal projects, and rebuild the missing files (e.g., TPM)." . "\n";
	echo "Usage:   php {$argv[0]} <action: 0: Preview; 1: Run>" . "\n";
	echo "Preview: php {$argv[0]} 0" . "\n";
	echo "Run:     php {$argv[0]} 1" . "\n";
	echo "\n";
	exit();
}


if ($action != 1) $action = 0;

if (true){
	$APP_CONFIG['SQL_CONN'] = bxaf_get_app_db_connection();
	$currentTable 			= $APP_CONFIG['Table']['App_User_Data_Projects'];
	$SQL 					= "SELECT `ProjectIndex`, `User_ID`  FROM `{$currentTable}`";
	$projectIndexes			= getSQL($SQL, 'GetAssoc', $currentTable, 1, 1);
	
	foreach($projectIndexes as $currentProjectIndex => $currentUserID){

		$baseDir = getInternalDataProcessedDir($currentUserID, $currentProjectIndex);
		
		if ($BXAF_CONFIG['HAS_TPM_DATA']){
			
			
			$fileFPKM = $TPMFile_Input = $baseDir . 'GeneLevelExpression.txt';	
			$fileTPM = $TPMFile_Output = $baseDir . 'GeneLevelExpression-TPM.txt';
			
			if (file_exists($fileFPKM) && !file_exists($fileTPM)){
				
				$header = getFileHeader($fileFPKM, "\t");
					
				if (!in_array('FPKM', $header)){
					continue;
				}
				
				echo "ProjectIndex: {$currentProjectIndex}\n";
				echo "TPM file does not exist: {$fileTPM}\n";
				echo "\n";
				
				
					
				$cmd = "{$BXAF_CONFIG_CUSTOM['PHP_BIN']} admin_fix_GeneFPKM_Internal.php {$TPMFile_Input} {$TPMFile_Output}";
				echo "Convert FPKM to TPM: \n";
				echo "{$cmd}\n\n";
				if ($action == 1) shell_exec($cmd);
				
				$tempFile = getInternalDataProcessedDir($currentUserID, $currentProjectIndex) . 'bigzip/GeneLevelExpression-TPM.txt.gz';
				$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$fileTPM} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k2,2n -k1,1n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
				echo "Building Gene Expression Level Data (Indexed by Gene) #1: \n";
				echo "{$cmd}\n\n";
				if ($action == 1) shell_exec($cmd);

				
				$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 2 -b 1 -e 1 -0 {$tempFile}";
				echo "Building Gene Expression Level Data (Indexed by Gene) #2: \n";
				echo "{$cmd}\n\n";
				if ($action == 1) shell_exec($cmd);
				
				
				
				$tempFile = getInternalDataProcessedDir($currentUserID, $currentProjectIndex) . 'bigzip/GeneLevelExpression-TPM.txt.Sample.gz';
				$cmd = "{$BXAF_CONFIG_CUSTOM['CAT_BIN']} {$fileTPM} | {$BXAF_CONFIG_CUSTOM['TAIL_BIN']} -n +2 | {$BXAF_CONFIG_CUSTOM['SORT_BIN']} -k1,1n -k2,2n | {$BXAF_CONFIG_CUSTOM['BGZIP_DIR']} > {$tempFile}";
				echo "Building Gene Expression Level Data (Indexed by Sample) #1: \n";
				echo "{$cmd}\n\n";
				if ($action == 1) shell_exec($cmd);
				
				
				$cmd = "{$BXAF_CONFIG_CUSTOM['TABIX_BIN']} -s 1 -b 2 -e 2 -0 {$tempFile}";
				echo "Building Gene Expression Level Data (Indexed by Sample) #2: \n";
				echo "{$cmd}\n\n";
				if ($action == 1) shell_exec($cmd);
					
					
				
				
				
				echo "------------------------------------------------\n";
			}
			
			
		}
		
		
	}
		
	
		
}







$endTime		 	= microtime(true);
$endMemoryUsage 	= memory_get_usage();


echo "Finished.\n\n";








?>