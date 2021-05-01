<?php

include_once('config.php');

$svgText = $_POST['svgText'];
header('Content-type: image/svg+xml');
header('Content-Disposition: attachment; filename="image.svg"'); 
print "$svgText";

?>