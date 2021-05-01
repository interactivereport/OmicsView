<?php

include_once('config_init.php');


if ($APP_CONFIG['Dashboard']['Default'] == 'Comparison'){
	header("Location: app_dashboard_comparison.php");
} elseif ($APP_CONFIG['Dashboard']['Default'] == 'Sample'){
	header("Location: app_dashboard_sample.php");	
} elseif ($APP_CONFIG['Dashboard']['Default'] == 'Project'){
	header("Location: app_dashboard_project.php");
} else {
	header("Location: app_dashboard_project.php");
}

exit();
?>