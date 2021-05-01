<?php
include_once('config_init.php');



$jobID 		= intval($_GET['Job']);
$permission	= intval($_GET['Permission']);

if ($jobID <= 0){
	exit();	
}

updateInternalDataPermission($jobID, $permission);




?>