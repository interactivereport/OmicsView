<?php

$allInternalDataJobs = getAllInternalDataJobs();


if (array_size($allInternalDataJobs) <= 0){
	header("Location: app_internal_data_import.php");
	exit();	
}

echo "<div class='row'>";
		echo "<div class='col-12'>";

			if (true){
				$URL = "app_internal_data_import.php";
				$actions[] = "<a href='{$URL}'>" . printFontAwesomeIcon('far fa-file') . " Import Internal Data</a>";
			}
			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";

		echo "</div>";
	echo "</div>";
	
	
$geneMapping = array_clean(array_column($allInternalDataJobs, 'Gene_Mapping_Display'));

$statusHTML = array_clean(array_column($allInternalDataJobs, 'Status_HTML'));



echo "<div class='row'>";
echo "<div class='col-12 w-100'>";
echo "<table id='resultTable' class='table table-sm table-striped w-100'>";
	echo "<thead>";
		echo "<tr>";
			echo "<th class='text-left text-nowrap'>No.</th>";
			echo "<th class='text-left text-nowrap'>Project</th>";
			
			echo "<th class='text-left text-nowrap'>Platform Type</th>";

			if (array_size($geneMapping) > 1){
				echo "<th class='text-left text-nowrap'>{$APP_MESSAGE['Gene Mapping']}</th>";
			}
			
			echo "<th class='text-left text-nowrap'># of Comparisons</th>";
			echo "<th class='text-left text-nowrap'># of Samples</th>";
			
			if (array_size($statusHTML) > 1){
				echo "<th class='text-left text-nowrap'>Import Status</th>";
			}
			
			echo "<th class='text-left text-nowrap'>Permission</th>";
			
			echo "<th class='text-left text-nowrap'>Date</th>";

			echo "<th class='text-left text-nowrap'>Owner</th>";

			echo "<th class='text-left text-nowrap'>Actions</th>";
		echo "</tr>";
	echo "</thead>";
	
	
	echo "<tbody>";
		unset($currentCount);
		foreach($allInternalDataJobs as $jobID => $jobInfo){
			++$currentCount;
			
			echo "<tr>";
			
					
				echo "<td>";
					echo "<a href='app_internal_data_review.php?ID={$jobID}'>{$currentCount}.</a>";
				echo "</td>";
				
				
				echo "<td>";
					echo "<a href='app_internal_data_review.php?ID={$jobID}'>" . displayLongText($jobInfo['Project_HTML']) . "</a>";
				echo "</td>";
				
				echo "<td>";
					echo $jobInfo['Internal_Platform_Type'];
				echo "</td>";
				
				if (array_size($geneMapping) > 1){
					echo "<td>";
						echo $jobInfo['Gene_Mapping_Display'];
					echo "</td>";
				}
				
				echo "<td>";
					echo "{$jobInfo['Comparison_Count']}";
				echo "</td>";

				echo "<td>";
					echo "{$jobInfo['Sample_Count']}";
				echo "</td>";	

				
				if (array_size($statusHTML) > 1){
					echo "<td>";
						echo $jobInfo['Status_HTML'];
					echo "</td>";
				}
				
				echo "<td>";
					
					if (!$jobInfo['canUpdate']){
						echo $jobInfo['Permission_HTML'];
					} else {
						
						unset($checked0, $checked1);
						
						if ($jobInfo['Permission'] == 1){
							$checked1 = "selected";
						} else {
							$checked0 = "selected";
						}
						
						echo "<select id='Permission_{$jobID}' class='permissionTrigger' job='{$jobID}'>";
							echo "<option value='0' {$checked0}>Private</option>";
							echo "<option value='1' {$checked1}>Public</option>";
						echo "</select>";
						
						echo "<a href='javascript:void(0);' class='startHidden Permission_Icon' id='Permission_{$jobID}_good'>" . printFontAwesomeIcon('fas fa-check text-success') . "</a>";
						echo "<a href='javascript:void(0);' class='startHidden Permission_Icon' id='Permission_{$jobID}_busy'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</a>";
						
					}
				echo "</td>";
				
				echo "<td class='nowrap'>";
					echo $jobInfo['Date'];
				echo "</td>";
				
				echo "<td>";
					echo "<div class='nowrap text-nowrap'><a title='{$jobInfo['User']['Name']}' href='mailto:{$jobInfo['User']['Email']}'>{$jobInfo['User']['Name']}</a></div>";
				echo "</td>";
				
				
				echo "<td>";
					unset($actions);
					
					$actions[] = "<a href='app_internal_data_review.php?ID={$jobID}'>" . printFontAwesomeIcon('fas fa-list') . " Review</a>";
					
					if ($jobInfo['canUpdate']){
						$actions[] = "<a href='app_internal_data_update.php?ID={$jobID}'>" . printFontAwesomeIcon('far fa-edit') . " Update</a>";
						$actions[] = "<a href='app_internal_data_delete.php?ID={$jobID}'>" . printFontAwesomeIcon('far fa-trash-alt') . " Delete</a>";
					}
					
					
					echo "<div class='nowrap text-nowrap'>" . implode(' &nbsp; ', $actions) . "</div>";
				echo "</td>";
			
			
			echo "</tr>";
		}
	echo "</tbody>";
	
echo "</table>";
echo "</div>";
echo "</div>";


?>
<style>
#resultTable td{
	vertical-align:top;
}
</style>

<script type="text/javascript">
$(document).ready(function(){

	$('#resultTable').DataTable({
        "processing": 	false,
		"scrollX": 		true,
		"order": [[ 0, "asc" ]],
		"columns": [
				<?php //No. ?>
		    	{ "searchable": false },
				
				
				<?php //Project ?>
				null,
				
				<?php //Platform Type ?>
				null,
				
				
				<?php if (array_size($geneMapping) > 1){ ?>
				null,
				<?php } ?>
				
				<?php //# of Comparison ?>
				null,
				
				<?php //# of Sample ?>
				null,
				
				
				<?php if (array_size($statusHTML) > 1){ ?>
				null,
				<?php } ?>
				
				
				<?php //Permissions ?>
				null,
				
				<?php //Date ?>
				null,
				
				<?php //Owner ?>
				null,
				
				<?php //Actions ?>
				{ "searchable": false },
			  ],
    });
	
	$($.fn.dataTable.tables( true ) ).css('width', '100%');
    $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();	
	 
	
	
	$('.permissionTrigger').change(function(){
		var jobID 		= $(this).attr('job');
		var permission 	= $(this).val();
		
		$('.Permission_Icon').hide();
		$('#Permission_' + jobID + '_busy').show();
		
		$.ajax({
			type: 'GET',
			url: 'app_internal_data_permission_modify.php?Job=' + jobID + '&Permission=' + permission,
			success: function(responseText){
				$('#Permission_' + jobID + '_busy').hide();
				$('#Permission_' + jobID + '_good').show();
			}
		});
		
	});

	
});


</script>

