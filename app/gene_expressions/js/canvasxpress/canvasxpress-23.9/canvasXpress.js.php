<?php

$file 	= 'canvasXpress.min.js';
$header	= 'javascript';


ini_set('zlib.output_compression','On'); 

$last_modified_time = filemtime($file); 
$etag = md5_file($file);

// Always send headers
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT"); 
header("Etag: {$etag}"); 

// Exit if not modified
if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time || 
	@trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) { 
	header("HTTP/1.1 304 Not Modified"); 
	exit; 
}

header("Content-type: text/{$header}");
include($file);
?>