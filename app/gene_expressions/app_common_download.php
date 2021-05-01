<?php
include_once('config_init.php');

$file 					= getSQLCache($_GET['key']);

$filePath 				= $file['Path'];
$contentType 			= $file['ContentType'];
$attachmentFilename 	= $file['Attachment_Filename'];
$pathinfo  				= pathinfo($filePath);


if ($contentType == ''){

	if ($pathinfo['extension'] == 'csv'){
		$contentType = 'text/csv';
	}
	
}



if ($contentType == ''){
	$contentType = mime_content_type($filePath);
}



if ($attachmentFilename == ''){
	$pathinfo  = pathinfo($filePath);	
	$attachmentFilename = $pathinfo['basename'];
}


if (file_exists($filePath) == false){
	echo printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please verify your URL and try again.";
	exit();	
}


$contentDisposition = 'attachment';

if ($file['contentDisposition'] != ''){
	$contentDisposition = $file['contentDisposition'];
}


header('Pragma: public');
header('Cache-Control: max-age=86400');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
header("Content-Type: {$contentType}");

if ($attachmentFilename != ''){
	header('Content-Disposition: ' . $contentDisposition . '; filename="' . $attachmentFilename . '"');	
}


readfile($filePath);


?>