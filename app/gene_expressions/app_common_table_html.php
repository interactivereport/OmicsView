<?php

/*
$tableOption['id'] 				= 'id';
$tableOption['exportOptions'] 	= '0,1,2,3,4';
$tableOption['headers']		 	= array(1,2,3,4,5);
$tableOption['dataKey']		 	= 'key';
$tableOption['disableButton']	= false;
$tableOption['order']			= '';
$tableOption['orderDisable']	= 0;

$tableOption['columnScript']	= '';
$tableOption['pageLength']		= 100;

$tableOption['Extra']['SQL_Data_All'] 	= $SQLs['Data'][$APP_CONFIG['APP']['List_Category'][$category]['Table']];
$tableOption['Extra']['SQL_Count']		= $SQLs['Count'][$APP_CONFIG['APP']['List_Category'][$category]['Table']];
$tableOption['Extra']['SQL_Has_Condition'] = false;
$tableOption['Extra']['Count']			= $totalCount;
$tableOption['Extra']['Fast_Mode']		= $_POST['fast'];
$tableOption['tableOptionKey']			= '123';
$tableOption['searchKeyword']			= 'mysearchkeyword';
*/


$tableOption['pageLength'] = intval($tableOption['pageLength']);

if ($tableOption['pageLength'] <= 0){
	$tableOption['pageLength'] = 50;	
}

if ($tableOption['order'] == ''){
	$tableOption['order'] = '0, "asc"';	
}

if ($tableOption['id'] == ''){
	$tableOption['id'] = 'table_' . md5(date('Y-m-d H:i:s'));	
}



echo "<div class='row'>";
echo "<div class='col-12'>";

	echo "<table id='{$tableOption['id']}' class='table table-sm table-striped w-100'>";
		echo "<thead>";
			echo "<tr>";
				foreach($tableOption['headers'] as $tempKey => $tempValue){
					echo "<th class='Xtext-center text-nowrap'>{$tempValue}</th>";
				}
			echo "</tr>";
		echo "</thead>";
	echo "</table>";

echo "</div>";
echo "</div>";

echo "<input type='hidden' id='{$tableOption['id']}_Search' value=''/>";



if ($tableOption['Extra']['Fast_Mode']){
	$ajaxURL = "app_common_table_json_fast.php?key={$tableOption['tableOptionKey']}";
} else {
	$ajaxURL = "app_common_table_json.php?key={$tableOption['dataKey']}";
}



?>


<script type="text/javascript">
$(document).ready(function(){
	
	
	$('#<?php echo $tableOption['id']; ?>').on( 'draw.dt', function () {
		var currentValue = $('#<?php echo $tableOption['id']; ?>_filter input').val();
		$('#<?php echo "{$tableOption['id']}_Search"; ?>').val(currentValue);
	});

	var table_<?php echo $tableOption['id']; ?> = $('#<?php echo $tableOption['id']; ?>').DataTable({
        "processing": 	true,
        "serverSide": 	true,
		"searching":	true,
		"select": 		true,
		
		<?php if ($tableOption['searchKeyword'] != ''){ ?>
		"search": {
			"search": "<?php echo $tableOption['searchKeyword']; ?>"
		  },
		<?php } ?>
		
		
		//"scrollX": 		true,
		"pageLength": 	<?php echo $tableOption['pageLength']; ?>,
		"lengthMenu":	[ <?php echo $APP_CONFIG['APP']['Table']['PerPage']['JS'];  ?> ],
		"ajax": {
				"url": "<?php echo $ajaxURL; ?>",
				"type": "POST"
			},
			
		<?php if (!$tableOption['orderDisable']){ ?>
		"order": [[ <?php echo $tableOption['order']; ?> ]],
		<?php } ?>
		
		<?php if ($tableOption['columnScript'] != ''){ ?>
		"columns": [
			<?php echo $tableOption['columnScript']; ?>
		  ],
		<?php } ?>
		
		<?php if (!$tableOption['disableButton']){ ?>
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
		<?php } ?>
    });
	
	/*
	table_<?php echo $tableOption['id']; ?>.on( 'select', function ( e, dt, type, indexes ) {
    	if ( type === 'row' ) {
			var selectIndexes = table_<?php echo $tableOption['id']; ?>.rows(indexes).toArray();
			for (var i = 0; i < selectIndexes[0].length; i++){
				//$('#checkbox_datatable_row_index_' + selectIndexes[0][i]).prop('checked', true);
			}
		}
	});
	*/


});


</script>