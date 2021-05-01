<?php
include_once('config_init.php');


$dataArray 			= get_list_record_by_list_id($_GET['ID']);
$category			= $dataArray['Category'];
$internalID			= $_GET['internalID'];

removeItemFromList($_GET['ID'], $internalID);

header("Location: app_list_review.php?ID={$_GET['ID']}");
exit();

?>