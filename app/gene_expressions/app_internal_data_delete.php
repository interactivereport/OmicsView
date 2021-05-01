<?php
include_once('config_init.php');

deleteInternalDataByJobID($_GET['ID']);
header("Location: app_internal_data_browse.php");
exit();

?>