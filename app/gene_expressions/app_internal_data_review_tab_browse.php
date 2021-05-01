<?php

/*
$table 			= $APP_CONFIG['Table']['App_User_Data_Projects'];
$preference		= 'Project';
$title			= 'Projects';
$records		= $dataArray['Projects'];
$array_numeric = false;
$dynamicPreference = true
$referenceProjectIndex = $_GET['ID'];
$table_public	= 'Projects';
*/


$tableID = "Table_" . getUniqueID();


if ($dynamicPreference){
	
	if (!is_array($referenceProjectIndex)){
		$referenceProjectIndex = array(0 => $referenceProjectIndex);	
	}
	
	$flexibleColumnSummary = getInternalDataJob_NonEmptyColumns($referenceProjectIndex, $table_public);
	
	if (array_size($flexibleColumnSummary['By-Column']) > 0){
	
		$temp 	= getTableColumnPreferences($preference);
		
		$getTableColumnPreferences = array(0 => $temp[0]);
		
		foreach($flexibleColumnSummary['By-Column'] as $tempKey => $tempValue){
			
			$temp = array(0);
			$temp['Title'] 	= $tempValue['Title'];
			$temp['SQL'] 	= $tempKey;
			
			$getTableColumnPreferences[] = $temp;
				
		}
	} else {
		$dynamicPreference = false;
		$getTableColumnPreferences 	= getTableColumnPreferences($preference);
	}

} else {
	$dynamicPreference = false;
	$getTableColumnPreferences 	= getTableColumnPreferences($preference);
}



echo "<div class='row'>";
	echo "<div class='col-12'>";

		echo "<br/>";
		echo "<h3>{$title}</h3>";
		echo "<br/>";

		
		unset($tempArray, $actions);
		$tempArray['Input'] = array_column($records, $APP_CONFIG['APP']['List_Category'][$preference]['Column_Human']);
		
		if (($APP_CONFIG['APP']['List_Category'][$preference]['Column_Human_Alt'] != '') && (array_size($tempArray['Input']) <= 0)){
			$tempArray['Input'] = array_column($records, $APP_CONFIG['APP']['List_Category'][$preference]['Column_Human_Alt']);	
		}
		
		if (($APP_CONFIG['APP']['List_Category'][$preference]['Column_Human2'] != '') && (array_size($tempArray['Input']) <= 0)){
			$tempArray['Input'] = array_column($records, $APP_CONFIG['APP']['List_Category'][$preference]['Column_Human2']);	
		}
		
		if (($APP_CONFIG['APP']['List_Category'][$preference]['Column_Default'] != '') && (array_size($tempArray['Input']) <= 0)){
			$tempArray['Input'] = array_column($records, $APP_CONFIG['APP']['List_Category'][$preference]['Column_Default']);	
		}
		
		if (array_size($tempArray['Input']) > 0){
			$count				= array_size($tempArray['Input']);
			$newListURLKey		= putSQLCacheWithoutKey($tempArray, '', 'URL', 1);
			$actions[]			= "<a href='app_list_new.php?Category={$preference}&key={$newListURLKey}' target='_blank'>" . printFontAwesomeIcon('far fa-file') . "{$APP_CONFIG['APP']['List_Category'][$preference]['Create_New_List']} ({$count})</a>";
		}
		
		
		
		
		$tempArray = array_column($records, 'User_ID');
		$tempArray = array_clean($tempArray);
		if (can_update_record(array('User_ID' => $tempArray[0]))){
			$dataKey = putSQLCacheWithoutKey(array_column($records, $APP_CONFIG['APP']['List_Category'][$preference]['Column_Internal']), '', 'URL', 1);
			$URL 	= "app_record_update.php?Category={$preference}&recordIndex={$dataKey}";
			$title	= "Update {$APP_CONFIG['APP']['List_Category'][$preference]['Names']} ({$count})";
			$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " {$title}</a>";	
		}
		
		if (!$dynamicPreference){
			$actions[]			= "<a href='{$APP_CONFIG['APP']['List_Category'][$preference]['Preference_URL']}' target='_blank'>" . printFontAwesomeIcon('fas fa-cog') . " Display Preferences</a>";
		}
		
		
		echo "<p>" . implode('&nbsp; &nbsp; &nbsp;', $actions) . "</p>";
		
		echo "<br/>";

	echo "</div>";
echo "</div>";



echo "<div class='row'>";
	echo "<div class='col-12'>";
		echo "<table id='{$tableID}' class='resultTable table table-sm table-striped w-100'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th class='text-left text-nowrap'>No.</th>";
					$tempArray_DataTable = array();
					$tempCount_DataTable = 0;
					foreach($getTableColumnPreferences as $tempKey => $tempValue){
						$tempArray_DataTable[] = ++$tempCount_DataTable;
						echo "<th class='text-left text-nowrap'>{$tempValue['Title']}</th>";
					}
					echo "<th class='text-left text-nowrap'>Actions</th>";
				echo "</tr>";
			echo "</thead>";
			
			$currentCount = 0;

			echo "<tbody>";
			
			foreach($records as $currentIndex => $currentRecord){
				
				$function = $APP_CONFIG['APP']['List_Category'][$preference]['transform_function'];
				
				if ($function != '' && function_exists($function)){
					$currentRecord = $function($currentIndex, $currentRecord, 'HTML');
				}
				
				
				
				++$currentCount;
				echo "<tr>";
					echo "<td>{$currentCount}.</td>";
				
					foreach($getTableColumnPreferences as $tempKey => $tempValue){
						
						if (isset($currentRecord[$APP_CONFIG['APP']['List_Category'][$preference]['Column_Internal']])){
							$internalIndex = $currentRecord[$APP_CONFIG['APP']['List_Category'][$preference]['Column_Internal']];
						} else {
							$internalIndex = $currentIndex;
						}
						
						$categoryLower	= strtolower($preference);
						$reviewURL 		= "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type={$categoryLower}&id={$internalIndex}";
						$humanReadable	= $currentRecord[$APP_CONFIG['APP']['List_Category'][$preference]['Column_Human']];
						
						$currentValue = $currentRecord[$tempValue['SQL']];
						
						if ($tempValue['SQL'] == $APP_CONFIG['APP']['List_Category'][$preference]['Column_Human']){
							$currentValue	= "<a href='{$reviewURL}' target='_blank'>{$currentValue}</a>";
						} else {
							$currentValue	= displayLongText($currentValue);
						}
						
						
						echo "<td>";
							echo $currentValue;
						echo "</td>";
					}
					
					
					unset($actions);
					
					if (!isset($currentRecord['Actions'])){
						$actions[] = "<a href='{$reviewURL}' title='Review {$humanReadable}' target='_blank'>" . printFontAwesomeIcon('fas fa-list') . "Review</a>";
					} else {
						$actions = $currentRecord['Actions'];
					}
					echo "<td><span class='text-nowrap'>" . implode("&nbsp; &nbsp;", $actions) . "</span></td>";
				
				echo "</tr>";
			}
			
			echo "</tbody>";
			
		echo "</table>";
		
	echo "</div>";
echo "</div>";

?>

<script type="text/javascript">

$(document).ready(function(){
	

var <?php echo $tableID; ?> = $('#<?php echo $tableID; ?>').DataTable({
        "processing": 	false,
        "serverSide": 	false,
		"scrollX": 		true,
		"columns": [
		    { "searchable": false },
				<?php for ($i = 1; $i <= $tempCount_DataTable; $i++){ ?>
					null,
				<?php } ?>
			{ "searchable": false },
			  ],
		dom: '<"row col-12"l>Bfrtip',
		buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [<?php echo implode(',', $tempArray_DataTable); ?>]
                }
            },
			{
                extend: 'csvHtml5',
				exportOptions: {
                    columns: [<?php echo implode(',', $tempArray_DataTable); ?>]
                }
            },
            {
                extend: 'excelHtml5',
				exportOptions: {
                    columns: [<?php echo implode(',', $tempArray_DataTable); ?>]
                }
            },
            {
                extend: 'pdfHtml5',
				exportOptions: {
                    columns: [<?php echo implode(',', $tempArray_DataTable); ?>]
                }
            },

        ]

    });
	
	
    
});

</script>