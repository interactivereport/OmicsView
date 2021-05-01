<?php

echo "<br/>";
echo "<br/>";

echo "<div >";

	unset($tableContent);
	$tableContent['Header'][1]		= 'No.';
	$tableContent['Header'][2] 		= 'Date';
	$tableContent['Header'][3] 		= 'Modified By';
	$tableContent['Header'][4] 		= 'Attribute';
	$tableContent['Header'][5] 		= 'Before';
	$tableContent['Header'][6] 		= 'After';
	
	
	unset($currentIndex);
	foreach($componentOptions['AuditTrail'] as $tempKey => $currentAuditTrail){
		
		$currentIndex++;
		
		$tableContent['Body'][$tempKey]['Value'][1]	= "{$currentIndex}.";
		$tableContent['Body'][$tempKey]['Value'][2]	= $currentAuditTrail['DateTime'];
		
		
		$userInfo = getUserInfo($currentAuditTrail['User_ID']);
		$tableContent['Body'][$tempKey]['Value'][3]	= $userInfo['Display'];
		
		$tableContent['Body'][$tempKey]['Value'][4]	= $currentAuditTrail['Column'];
		$tableContent['Body'][$tempKey]['Value'][5]	= $currentAuditTrail['Value_Before'];
		$tableContent['Body'][$tempKey]['Value'][6]	= $currentAuditTrail['Value_After'];
		
		
	}
	
	
	
	echo printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-md-12 col-sm-12 col-xs-12');

echo "</div>";


unset($componentOptions);

?>
