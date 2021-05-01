<?php

if (php_sapi_name() !== 'cli'){	
	exit();
}

echo "DiseaseData Processing Tool (Version: 2019-04-16)" . "\n\n";

ini_set('auto_detect_line_endings', true);
set_time_limit(0);
ini_set('memory_limit', '-1');
error_reporting(0);
ignore_user_abort(true);

$startTime		 	= microtime(true);
$startMemoryUsage 	= memory_get_usage();
$argvLength 		= sizeof($argv);
$geneFile			= $argv[1];
$output				= $argv[2];

if ($argvLength != 3){
	echo "This tool converts the Gene Expression file from FPKM scale to TPM scale." . "\n";
	echo "Usage:   php {$argv[0]}   <GeneFPKM.txt>        <GeneFPKM-TPM.txt>" . "\n";
	echo "Example: php {$argv[0]}   /input/GeneFPKM.txt   /output/GeneFPKM-TPM.txt" . "\n";
	echo "\n";
	exit();
}


if (!is_file($geneFile)){
	echo "Error. The file: {$geneFile} cannot be read." . "\n";
	echo "\n";
	exit();
}


if (true){
	
	$start_time = microtime(true);
	echo "Reading: {$geneFile}\n";
	
	$fp = fopen($geneFile, 'r');
	
	unset($headers);
	while (!feof($fp)){
		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (!isset($headers)){
			$headers 	= explode("\t", trim($currentLine));
			$headerSize = sizeof($headers);
			$currentOutput = $headers;
			
			if (!in_array('FPKM', $headers)){
				echo "Error. FPKM not found." . "\n";
				echo "\n";
				exit();
			}
			
			
		} else {
			$currentOutput = explode("\t", trim($currentLine));
			$currentOutput = array_combine($headers, $currentOutput);
			
			if (sizeof($currentOutput) != $headerSize){
				continue;
			}
			
			if (!is_numeric($currentOutput['FPKM'])) continue;
			
			if (!isset($sampleFPKM[$currentOutput['SampleIndex']])){
				$sampleFPKM[$currentOutput['SampleIndex']] = 0;	
			}
			
			$sampleFPKM[$currentOutput['SampleIndex']] += $currentOutput['FPKM'];
			
		}
	}
	$duration = microtime(true) - $start_time;
	
	if ($duration < 60){
		echo "Reading: {$duration} seconds\n";
	} else {
		$duration = $duration / 60;	
		echo "Reading: {$duration} minutes\n";
	}
	

	
	fclose($fp);

}

echo "Indexing SampleFPKM\n";
$start_time = microtime(true);
foreach($sampleFPKM as $sampleIndex => $tempValues){
	if ($tempValues != 0){
		$sampleTPM[$sampleIndex] = 1e6/$tempValues;
	}
}
$duration = microtime(true) - $start_time;

if ($duration < 60){
	echo "Indexing SampleFPKM: {$duration} seconds\n";
} else {
	$duration = $duration / 60;	
	echo "Indexing SampleFPKM: {$duration} minutes\n";
}


echo "Writing {$output}\n";
$start_time = microtime(true);
$count = 0;
if (true){
	unset($headers);
	
	$fp 	= fopen($geneFile, 'r');
	$fpout 	= fopen($output, 'w');
	
	while (!feof($fp)){
		$currentLine = fgets($fp, 1000000);
			
		if (trim($currentLine) == '') continue;
		
		if (!isset($headers)){
			$headers 	= explode("\t", trim($currentLine));
			$headerSize = sizeof($headers);
			
			$currentOutput = $headers;
		} else {
			$currentOutput = explode("\t", trim($currentLine));
			$currentOutput = array_combine($headers, $currentOutput);
			
			if (sizeof($currentOutput) != $headerSize){
				continue;
			}
			
			
			$currentSampleIndex = $currentOutput['SampleIndex'];
			
			if (isset($sampleTPM[$currentSampleIndex]) && is_numeric($sampleTPM[$currentSampleIndex])){
				$currentOutput['FPKM'] *= $sampleTPM[$currentSampleIndex];
			}
			
		}
		
		$count++;
		fwrite($fpout, implode("\t", $currentOutput) . "\n");
	}
	
	fclose($fp);
	fclose($foutput);
}
$duration = microtime(true) - $start_time;

if ($duration < 60){
	echo "Writing: {$duration} seconds\n";
} else {
	$duration = $duration / 60;	
	echo "Writing: {$duration} minutes\n";
}


$endTime		 	= microtime(true);
$endMemoryUsage 	= memory_get_usage();


echo "Finished.\n\n";








?>