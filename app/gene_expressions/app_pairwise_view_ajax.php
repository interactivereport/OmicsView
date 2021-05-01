<?php
include_once('config_init.php');

if (array_size($_POST) > 0){
	foreach($APP_CONFIG['DB_Dictionary']['Samples']['Filter'] as $tempKey => $tempValue){
				
		$currentColumn = $tempValue['SQL'];
		
		if (array_size($_POST["filter_{$currentColumn}"]) > 0){
			
			foreach($_POST["filter_{$currentColumn}"] as $tempKeyX => $tempValueX){
				
				if ($tempValueX != ''){
					$SQL_CONDITIONS[$currentColumn][] = "'" . addslashes(trim($tempValueX)) . "'";
				}
			}
			
			$SQL_CONDITIONS[$currentColumn] = "(`{$currentColumn}` IN (" .  implode(', ', $SQL_CONDITIONS[$currentColumn]) . "))";
		}
	}
}


$allSQLColumns 		= array_keys($APP_CONFIG['DB_Dictionary']['Samples']['Filter']);
$allSQLColumns 		= "`SampleIndex`, `" . implode('`,`', $allSQLColumns) . "`";


if (array_size($SQL_CONDITIONS) <= 0){
	
	$allRecordIndexes 	= get_multiple_record('Samples', '', 'GetAssoc', $allSQLColumns, 1);
	
} else {
	$SQL_CONDITIONS = implode(' AND ', $SQL_CONDITIONS);
	
	$allRecordIndexes = search_all_records('Samples', $allSQLColumns, $SQL_CONDITIONS, 'GetAssoc');
}


foreach($APP_CONFIG['DB_Dictionary']['Samples']['Filter'] as $tempKey1 => $tempValue1){
	$currentColumn	= $tempValue1['SQL'];
	$values 		= array_column($allRecordIndexes, $currentColumn);
	$values 		= array_count_values($values);
			
}

echo "<div id='updateButton' class='d-none d-xl-block'>";
	echo "<button class='btn btn-primary submitButton'>" . printFontAwesomeIcon('far fa-chart-bar') . " Update Plot</button>";
echo "</div>";

?>

<script type="text/javascript">

$(document).ready(function(){
	
	
	$('#selectedSampleCount').html("<mark>(Selected: <?php echo number_format(array_size($allRecordIndexes)); ?>)</mark>");
	
	<?php
		foreach($APP_CONFIG['DB_Dictionary']['Samples']['Filter'] as $tempKey1 => $tempValue1){
			$currentColumn	= $tempValue1['SQL'];
			$values 		= array_column($allRecordIndexes, $currentColumn);
			$values 		= array_count_values($values);
			
			$allCount		= array_sum($values);
			$allCount		= number_format($allCount);
	?>
		$('.filter_<?php echo $currentColumn; ?>_Count').html('0');
		
		$('#<?php echo $currentColumn; ?>_Total').html('<?php echo $allCount; ?>');
		
		
		<?php 
			foreach($values as $currentCategory => $currentCount){
				
				$currentCount 	= number_format($currentCount);
				$currentID		= "{$currentColumn}_" . md5(strtolower($currentCategory));
		?>
		
			$('#<?php echo $currentID; ?>').html('<?php echo $currentCount; ?>');
		
		
		
		<?php } ?>
	<?php } ?>

});

</script>


<style>
#updateButton {
    position:fixed;
    top: 50%;
    left: 50%;
    width:30em;
    height:18em;
    margin-top: -9em; /*set to a negative number 1/2 of your height*/
    margin-left: -15em; /*set to a negative number 1/2 of your width*/
    
   
}

</style>







