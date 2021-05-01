<?php 


if ($BXAF_CONFIG['APP_PROFILE'] == 'human'){
	
	include_once('config_human.php');
	
} elseif ($BXAF_CONFIG['APP_PROFILE'] == 'human_jomics'){
	
	include_once('config_human.php');
	
} elseif (($BXAF_CONFIG['APP_PROFILE'] == 'mouse') || ($BXAF_CONFIG['APP_PROFILE'] == 'mouse_single_cell')){
	
	include_once('config_mouse.php');
	
} elseif ($BXAF_CONFIG['APP_PROFILE'] == 'cho'){
	

	include_once('config_cho.php');
	
} elseif ($BXAF_CONFIG['APP_PROFILE'] == 'human_single_cell'){
	
	include_once('config_human_single_cell.php');
	
} elseif (($BXAF_CONFIG['APP_PROFILE'] == 'rat') || ($BXAF_CONFIG['APP_PROFILE'] == 'rat_single_cell')){
	
	include_once('config_rat.php');
	
}


?>