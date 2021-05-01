<?php


$lists = getAllMetaAnalyses($category);


$URL_New = "app_meta_analysis2.php";


if (array_size($lists) <= 0){
	
	header("Location: {$URL_New}");
	exit();

	
	
} else {
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";

			$actions[] = "<a href='{$URL_New}'>" . printFontAwesomeIcon('far fa-file') . " Create New Meta Analysis</a>";
		
			echo "<p>" . implode(" &nbsp; &nbsp; ", $actions) . "</p>";
		
		echo "</div>";
	echo "</div>";
	
	
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<table id='resultTable' class='table table-sm table-striped w-100'>";
				echo "<thead>";
					echo "<tr>";
						echo "<th class='text-left text-nowrap'>No.</th>";
						echo "<th class='text-left text-nowrap'>Name</th>";
						echo "<th class='text-left text-nowrap'>Owner</th>";
						echo "<th class='text-left text-nowrap'>Date</th>";
						echo "<th class='text-left text-nowrap'>Status</th>";
						echo "<th class='text-left text-nowrap'>Actions</th>";
					echo "</tr>";
				echo "</thead>";
		
			
				echo "<tbody>";
					unset($currentCount);
					foreach($lists as $listID => $listContent){
						
						echo "<tr>";
							++$currentCount;
							
							echo "<td><a href='app_meta_analysis2_review.php?ID={$listID}'>{$currentCount}.</a></td>";
							
							echo "<td><a href='app_meta_analysis2_review.php?ID={$listID}'>{$listContent['Name']}</a></td>";
							
							
							
							echo "<td>{$listContent['User']['Name']} ({$listContent['User']['Email']})</td>";
							echo "<td>{$listContent['Date']}</td>";
							
							echo "<td>{$listContent['Status']}</td>";
						
						
							unset($actions);
							$actions[] = "<a href='app_meta_analysis2_review.php?ID={$listID}' >" . printFontAwesomeIcon('fas fa-list') . "Review</a>";
			
							if ($listContent['canUpdate']){
								$actions[] = "<a href='javascript:void(0);' class='deleteListTrigger' listid='{$listID}' human_readable='{$listContent['Name']}'>" . printFontAwesomeIcon('far fa-trash-alt') . "Delete</a>";
							}
						
							echo "<td>" . implode(' &nbsp; ', $actions) . "</td>";
			
						echo "</tr>";
					}
				echo "</tbody>";
			echo "</table>";
		echo "</div>";
	echo "</div>";
				
	
	
}
	
	
if (true){
	$modalID 	= 'removeListModal';
	$modalTitle	= "<div id='removeListModalHumanReadableID'></div>";
	$modalBody	= "";
	$modalButtonTextAction 	= 'Delete';
	$modalButtonTextCancel	= 'Cancel';
	$modalButtonActionClass = 'removeListTrigger btn-danger';
	echo printConfirmation($modalID, $modalTitle, $modalBody, $modalButtonTextAction, $modalButtonTextCancel, $modalButtonActionClass);
}
	

?>

<script type="text/javascript">

$(document).ready(function(){
	
	
	$('#resultTable').DataTable({
        "processing": 	true,
		"scrollX": 		true,
		"order": [[ 0, "asc" ]],
		"columns": [
		    { "searchable": false },
				null,
				null,
				null,
				null,
			{ "searchable": false },
			  ],
		dom: '<"row col-12"l>Bfrtip',
		buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [1,2,3,4]
                }
            },
			{
                extend: 'csvHtml5',
				exportOptions: {
                    columns: [1,2,3,4]
                }
            },
            {
                extend: 'excelHtml5',
				exportOptions: {
                    columns: [1,2,3,4]
                }
            },
            {
                extend: 'pdfHtml5',
				exportOptions: {
                    columns: [1,2,3,4]
                }
            },

        ]
    });
	
	
	$('.deleteListTrigger').click(function(){
		var currentValue	 = parseInt($(this).attr('listid'));
		var humanReadable 	= $(this).attr('human_readable');
		
		if (currentValue > 0){
			
			$('#removeListModalHumanReadableID').html('You are going to delete <strong>' + humanReadable + '</strong>, do you want to continue?');

			$('#removeListModal').modal('show');
		
			$(document).on('click', '.removeListTrigger', function(){
				$('#removeListModal').modal('hide');
				window.location = 'app_meta_analysis2_delete_record.php?ID=' + currentValue;
			});
			

		}
	});
	
});
</script>