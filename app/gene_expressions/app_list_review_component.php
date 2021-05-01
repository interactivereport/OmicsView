<?php


if (array_size($dataArray) <= 0){
	echo "<div class='row'>";

		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The list does not exist. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'warning');

		echo "</div>";


	echo "</div>";


} elseif (!$dataArray['canAccess']){
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " You do not have permission to access this study.</p>";
			echo getAlerts($message, 'danger');
		echo "</div>";
	echo "</div>";

} else {


	

	unset($actions);
	echo "<div class='row'>";
		echo "<div class='col-12'>";

			if ($dataArray['canUpdate']){
				$URL = "app_list_update.php?ID={$_GET['ID']}";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-edit') . " Update</a>";
			}


			$URL = "app_list_new.php?Category={$dataArray['Category']}";
			$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " {$APP_CONFIG['APP']['List_Category'][$category]['Create_New_List']}</a>";

			if ($dataArray['canUpdate']){
				$actions[] = "<a href='javascript:void(0);' class='deleteListTrigger'>" . printFontAwesomeIcon('far fa-trash-alt') . " Delete This List</a>";
			}


			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";

		echo "</div>";
	echo "</div>";


	echo "<div class='row'>";

		echo "<div class='col-6'>";

			echo "<dl class='row'>";

				echo "<dt class='col-3 text-right'>Name:</dt>";
				echo "<dd class='col-9'>{$dataArray['Name']}</dt>";

				echo "<dt class='col-3 text-right'>{$APP_CONFIG['APP']['List_Category'][$category]['Number']}:</dt>";
				echo "<dd class='col-9'>{$dataArray['Count']}</dt>";

				echo "<dt class='col-3 text-right'>Date:</dt>";
				echo "<dd class='col-9'>{$dataArray['Date']}</dt>";

				if ($dataArray['Notes'] != ''){
					echo "<dt class='col-3 text-right'>Description:</dt>";
					echo "<dd class='col-9'>{$dataArray['Notes']}</dt>";
				}
				
			echo "</dl>";

		echo "</div>";

	echo "</div>";


	echo "<hr/>";

	$getTableColumnPreferences 	= getTableColumnPreferences($category);
	
	$listRecords = getListRecords($_GET['ID']);

	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<table id='resultTable' class='table table-sm table-striped w-100'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th class='text-left text-nowrap'>No.</th>";
						foreach($getTableColumnPreferences as $tempKey => $tempValue){
							echo "<th class='text-left text-nowrap'>{$tempValue['Title']}</th>";
						}
						echo "<th class='text-left text-nowrap'>Actions</th>";
					echo "</tr>";
				echo "</thead>";
				
				
				echo "<tbody>";
					unset($currentCount);
					foreach($listRecords as $currentIndex => $currentRecord){
						echo "<tr>";
							++$currentCount;
							echo "<td>{$currentCount}.</td>";
							foreach($getTableColumnPreferences as $tempKey => $tempValue){
								
								$categoryLower	= strtolower($category);
								$reviewURL 		= "{$BXAF_CONFIG['BXAF_APP_URL']}plot/search_comparison/single_comparison.php?type={$categoryLower}&id={$currentIndex}";
								$humanReadable	= $currentRecord[$APP_CONFIG['APP']['List_Category'][$category]['Column_Human']];
								
								
								$currentValue = $currentRecord[$tempValue['SQL']];
								
								if ($tempValue['SQL'] == $APP_CONFIG['APP']['List_Category'][$category]['Column_Human']){
									$currentValue	= "<a href='{$reviewURL}' target='_blank'>{$currentValue}</a>";
								} else {
									$currentValue	= displayLongText($currentValue);
								}
								
								
								echo "<td>";
									echo $currentValue;
								echo "</td>";
							}
							
							unset($actions);
							$actions[] = "<a href='{$reviewURL}' title='Review {$humanReadable}' target='_blank'>" . printFontAwesomeIcon('fas fa-list') . "Review</a>";
							if ($dataArray['canUpdate']){
								if ($dataArray['Count'] > 1){
									$actions[] = "<a href='javascript:void(0);' class='deleteItemTrigger' title='Remove {$humanReadable} from this list' internal_id='{$currentIndex}' human_readable='{$humanReadable}'>" . printFontAwesomeIcon('far fa-trash-alt') . "Remove From List</a>";
								}
							}
							
							echo "<td><span class='text-nowrap'>" . implode("&nbsp; &nbsp;", $actions) . "</span></td>";
						
						
						echo "</tr>";
					}
				echo "</tbody>";
				
			echo "</table>";



		echo "</div>";
	echo "</div>";
}



if ($dataArray['canUpdate']){
	$modalID 	= 'removeFromListModal';
	$modalTitle	= "<div id='removeFromListModalHumanReadableID'></div>";
	$modalBody	= "";
	$modalButtonTextAction 	= 'Delete';
	$modalButtonTextCancel	= 'Cancel';
	$modalButtonActionClass = 'removeFromListTrigger btn-danger';
	echo printConfirmation($modalID, $modalTitle, $modalBody, $modalButtonTextAction, $modalButtonTextCancel, $modalButtonActionClass);



	$modalID 	= 'removeListModal';
	$modalTitle	= "You are going to delete this list. Do you want to continue?";
	$modalBody	= "";
	$modalButtonTextAction 	= 'Delete';
	$modalButtonTextCancel	= 'Cancel';
	$modalButtonActionClass = 'removeListTrigger btn-danger';
	echo printConfirmation($modalID, $modalTitle, $modalBody, $modalButtonTextAction, $modalButtonTextCancel, $modalButtonActionClass);
}


$columnIDs = array_keys($getTableColumnPreferences);
$columnIDs = implode(',', $columnIDs);

echo "<hr/>";


unset($tableOption['exportOptions']);
?>
<script type="text/javascript">
$(document).ready(function(){

	$('#resultTable').DataTable({
        "processing": 	true,
		"scrollX": 		true,
		"order": [[ 0, "asc" ]],
		"columns": [
			{ "searchable": false },
		    <?php 
				unset($currentCount);
				foreach($getTableColumnPreferences as $tempKey => $tempValue){ 
					$tableOption['exportOptions'][] = ++$currentCount;
			?>
				null,
			<?php 
				} 
				
				$tableOption['exportOptions'] = implode(',', $tableOption['exportOptions']);
			?>
			{ "searchable": false },
			  ],
			  
			  
		dom: '<"row col-12"l>Bfrtip',
		buttons: [
            {
                extend: 'copyHtml5',
				<?php if ($tableOption['exportOptions'] != ''){ ?>
                exportOptions: {
                    columns: [<?php echo $tableOption['exportOptions']; ?>]
                }
				<?php } ?>
            },
			{
                extend: 'csvHtml5',
				<?php if ($tableOption['exportOptions'] != ''){ ?>
                exportOptions: {
                    columns: [<?php echo $tableOption['exportOptions']; ?>]
                }
				<?php } ?>
            },
            {
                extend: 'excelHtml5',
				<?php if ($tableOption['exportOptions'] != ''){ ?>
                exportOptions: {
                    columns: [<?php echo $tableOption['exportOptions']; ?>]
                }
				<?php } ?>
            },
            {
                extend: 'pdfHtml5',
				<?php if ($tableOption['exportOptions'] != ''){ ?>
                exportOptions: {
                    columns: [<?php echo $tableOption['exportOptions']; ?>]
                }
				<?php } ?>
            },

        ]
    });

	<?php if ($dataArray['canUpdate']){ ?>

	$('#resultTable').on('click', '.deleteItemTrigger', function(){
		var internalID 		= $(this).attr('internal_id');
		var humanReadable	= $(this).attr('human_readable');

		$('#removeFromListModalHumanReadableID').html('You are going to remove <strong>' + humanReadable + '</strong> from the list. Do you want to continue?');

		$('#removeFromListModal').modal('show');

		$(document).on('click', '.removeFromListTrigger', function(){
			$('#removeFromListModal').modal('hide');
			window.location = "app_list_delete.php?ID=<?php echo $_GET['ID']; ?>&Category=<?php echo $dataArray['Category']; ?>&internalID=" + internalID;
		});
	});
	
	$('.deleteListTrigger').click(function(){

		$('#removeListModal').modal('show');

		$(document).on('click', '.removeListTrigger', function(){
			$('#removeListModal').modal('hide');
			
			window.location = 'app_list_delete_record.php?ID=<?php echo $_GET['ID']; ?>';
			
		});

	});
	
	<?php } ?>



});


</script>

<style>
#resultTable td{
	vertical-align:top;
}
</style>
