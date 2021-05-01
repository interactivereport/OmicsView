<?php

//System Config

include_once(dirname(dirname(__FILE__)) . "/bxaf_lite/config.php");

$tempDir = dirname(__FILE__) . "/common/";
$tempFiles = glob("{$tempDir}/*.php");
foreach ($tempFiles as $tempKey => $tempValue){
	include_once($tempValue);
}

if ($BXAF_CONFIG_CUSTOM['APP_PROFILE'] != ''){

	$tempDir = dirname(__FILE__) . "/profile/{$BXAF_CONFIG_CUSTOM['APP_PROFILE']}";

	if (is_dir($tempDir)){
		$profileFiles = glob("{$tempDir}/*.php");

		sort($profileFiles);
		foreach ($profileFiles as $tempKey => $tempValue){
			include_once($tempValue);
		}
	}

	unset($profileFiles, $tempDir);

}

include_once(dirname(__FILE__) . "/common/message/message_gene.php");

if (gene_uses_TPM()){
	$APP_MESSAGE['RPKM/TPM'] = 'TPM';
} else {
	$APP_MESSAGE['RPKM/TPM'] = 'RPKM';
}



ini_set('memory_limit', -1);


if ($BXAF_CONFIG_CUSTOM['HOMER_PATH'] != ''){
	$tempPath = 'PATH='.getenv('PATH').':' . $BXAF_CONFIG_CUSTOM['HOMER_PATH'];
	putenv($tempPath);  
	unset($tempPath);
}


?>