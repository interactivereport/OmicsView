<?php

$file 	= 'canvasXpress.css';
$header	= 'css';


ini_set('zlib.output_compression','On'); 

$last_modified_time = filemtime($file); 
$etag = md5_file($file);

// Always send headers
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT"); 
header("Etag: {$etag}"); 
header("X-Content-Type-Options: nosniff"); 

// Exit if not modified
if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time || 
	@trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) { 
	header("HTTP/1.1 304 Not Modified"); 
	exit; 
}

header("Content-type: text/{$header}");
include($file);
?>