<?php
if (isAdminUser()){
	
	unset($tableContent);
	$tableContent['Header'][1]		= 'Category';
	$tableContent['Header'][2] 		= 'Value';
	
	$tableContent['Body']['Application Profile']['Value'][1] 	= 'Application Profile';
	$tableContent['Body']['Application Profile']['Value'][2] 	= $APP_CONFIG['Profile'];
	
	if ($BXAF_CONFIG['PUBLIC_DATA_VERSION'] != ''){
		$tableContent['Body']['Public Data Version']['Value'][1] 	= 'Public Data Version';
		$tableContent['Body']['Public Data Version']['Value'][2] 	= $BXAF_CONFIG['PUBLIC_DATA_VERSION'];
	}
	
	$tableContent['Body']['Database']['Value'][1] 	= 'MySQL Database';
	$tableContent['Body']['Database']['Value'][2] 	= $BXAF_CONFIG['APP_DB_NAME'];
	
	$tableContent['Body']['User Database']['Value'][1] 	= 'User Database';
	$tableContent['Body']['User Database']['Value'][2] 	= $BXAF_CONFIG['BXAF_DB_NAME'];

			
	foreach($APP_CONFIG['Version'] as $tempKey => $tempValue){
		
		$tableContent['Body'][$tempKey]['Value'][1] 	= $tempKey;
		$tableContent['Body'][$tempKey]['Value'][2] 	= $tempValue;
		
	}
	
	$tableContent['Body']['Admin Users']['Value'][1]	= 'Admin Users';
	$tableContent['Body']['Admin Users']['Value'][2]	= "<ul><li>" . implode('</li><li>', $BXAF_CONFIG_CUSTOM['Admin_User_Email']) . "</li></ul>";
	
	
	
	
	echo printTableHTML($tableContent, 1, 1, 0);
	
	echo "<hr/>";


} else {
	echo "<p>You do not have permissions to access this tool.</p>";	
}

?>