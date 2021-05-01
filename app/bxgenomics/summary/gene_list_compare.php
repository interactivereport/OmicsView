<?php
include_once(__DIR__ . "/config.php");

if (isset($_GET['action']) && $_GET['action'] == 'list'){

	$table = 'tbl_go_gene_list';
	$filter = " `Species` = '{$BXAF_CONFIG['SPECIES']}' ";

    $sql2 = "SELECT `ID`, `Category`, `Name`, `Gene_Counts`, 'Actions' FROM `$table` WHERE $filter ";

    $sql = "";
    // Search Condition
    if(isset($_POST['search']['value']) && trim($_POST['search']['value']) != '') {
    	$search_array = array();
    	for ($i = 0; $i < count($_POST['columns']); $i++){
            if(! in_array($_POST['columns'][$i]['data'], array('Actions'))){
                $search_array[] = "`" . $_POST['columns'][$i]['data'] . "` LIKE '%" . $_POST['search']['value'] . "%'";
            }
    	}
    	$sql .= " AND (" . implode(" OR ", $search_array) . ")";
    }

    // Order Condition
    $sql .= " ORDER BY ";
    $condition_array = array();
    for ($i = 0; $i < count($_POST['order']); $i++) {
        $order = $_POST['columns'][$_POST['order'][$i]['column']]['data'];
        $asc = $_POST['order'][$i]['dir'];
        if(! in_array($order, array('Actions'))){
            $condition_array[] = "`$order` $asc";
        }
    }
    $sql .= implode(", ", $condition_array);



    $sql0 = "SELECT COUNT(*) FROM `$table` WHERE $filter ";
    $recordsTotal = $BXAF_MODULE_CONN->get_one($sql0);

    $recordsFiltered = $BXAF_MODULE_CONN->get_one($sql0 . $sql);

    $data = $BXAF_MODULE_CONN->get_all($sql2 . $sql . " LIMIT " . $_POST['start'] . "," . $_POST['length'] . "");

    $output_array = array(
		'sql' => $BXAF_MODULE_CONN->last_query(),
    	'draw' => intval($_POST['draw']),
    	'recordsTotal' => $recordsTotal,
    	'recordsFiltered' => $recordsFiltered,
    	'data' => array()
    );

    foreach($data as $value) {
        $row = array();
        foreach($value as $k=>$v){
			if($k == 'Actions'){
				$row[$k] = '<a href="javascript:void(0);" class="btn_select_search_pathway" content="' . $value['ID'] . '" displayed_name="' . $value['Category'] . ': ' . $value['Name'] . '"><i class="fas fa-angle-double-right"></i> Select</a>';
			}
			else if($k == 'Code' || $k == 'Name'){
				$row[$k] = "" . str_replace('_', ' ', $v) . "";
			}
			else {
				$row[$k] = $v;
			}
        }
        $output_array['data'][] = $row;

    }
    echo json_encode($output_array);

    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<link type="text/css" rel="stylesheet" href="css/style.css" />


	<link href="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery-ui/jquery-ui.min.css.php" rel="stylesheet">
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery-ui/jquery-ui.min.js.php"></script>

	<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js'></script>

	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js.php'></script>

	<script src="js/d3.js.php"></script>
	<script src="js/venn.js.php"></script>

    <style>
        .hidden{
            display: none;
        }
    </style>

</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_content" class="row no-gutters h-100">

        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

			<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">


				<h1>
					Compare Gene Lists with Venn Diagrams

				</h1>
				<hr class="w-100 my-1" />

				<div class="my-5 w-100">

	                <form class="w-100" id="form_main" method="post">

						<div class="container-fluid">

							<div class="row my-3">

								<div class="col-md-4">
									<input type="text" title="Enter a custom gene list label" placeholder="custom gene list label" class="form-control w-50" id="comparison_name1" name="comparison_name1" value="A">

									<input type="hidden" id="comparison1" name="comparison1" value="<?php echo ($_GET['comparison1'] != '') ? $_GET['comparison1'] : ""; ?>">
									<div class="w-100 my-2" id="comparison1_text">(Not selected yet)</div>

									<a href="Javascript: void(0);" class="btn_select_pathway_show_modal" target_field="comparison1">
				                      <i class="fas fa-angle-double-right"></i> Select First Gene List
				                    </a>
								</div>

								<div class="col-md-4">
									<input type="text" title="Enter a custom gene list label" placeholder="custom gene list label" class="form-control w-50" id="comparison_name2" name="comparison_name2" value="B">

									<input type="hidden" id="comparison2" name="comparison2" value="<?php echo ($_GET['comparison2'] != '') ? $_GET['comparison2'] : ""; ?>">
									<div class="w-100 my-2" id="comparison2_text">(Not selected yet)</div>

									<a href="Javascript: void(0);" class="btn_select_pathway_show_modal" target_field="comparison2">
				                      <i class="fas fa-angle-double-right"></i> Select Second Gene List
				                    </a>
								</div>

								<div class="col-md-4">
									<input type="text" title="Enter a custom gene list label" placeholder="custom gene list label" class="form-control w-50" id="comparison_name3" name="comparison_name3" value="C">

									<input type="hidden" id="comparison3" name="comparison3" value="<?php echo ($_GET['comparison3'] != '') ? $_GET['comparison3'] : ""; ?>">
									<div class="w-100 my-2" id="comparison3_text">(Not selected yet)</div>

									<a href="Javascript: void(0);" class="btn_select_pathway_show_modal" target_field="comparison3">
				                      <i class="fas fa-angle-double-right"></i> Select Third Gene List
				                    </a>
								</div>

							</div>

		                    <div class="row my-5 pl-3">
		                        <input class="btn btn-primary" type="submit" value="Submit">
		                        <input class="btn btn-default mx-2" type="reset" value="Reset">

								<label id="form_upload_file_busy" class="px-2 hidden text-danger"><i class="fas fa-spinner fa-spin"></i> Submitting ... </label>

							</div>

						</div>

	                </form>

	                <div class="w-100 my-3">
	                    <div id="div_results" class="w-100 my-3"></div>
	                    <div id="div_debug" class="w-100 my-3"></div>
	                </div>




				</div>


            </div>

		    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>

		</div>

	</div>


<!-------------------------------------------------------------------------------------------------------->
<!-- Modal to Select Comparison -->
<!-------------------------------------------------------------------------------------------------------->
<div class="modal" id="modal_select_pathway" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select a Gene List</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<?php
					echo '<div class="w-100">
					<table class="table table-bordered w-100" id="table_select_pathway">
						<thead>
						<tr>
                            <th>ID</th>
							<th>Category</th>
							<th>Name</th>
							<th>Genes</th>
							<th>Action</th>
						</tr>
						</thead>';

					echo '</table></div>';
				?>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="target_field" name="target_field" value="">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


<script>

    $(document).ready(function() {

		$('#table_select_pathway').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": "<?php echo $_SERVER['PHP_SELF']; ?>?action=list",
	            "type": "POST"
	        },
			"paging": true,
			"pageLength": 10,
	        "lengthMenu": [[10, 25, 100, 250, 1000, 5000], [10, 25, 100, 250, 1000, 5000]],
	        "columns": [
				{ "data": "ID" },
	            { "data": "Category" },
				{ "data": "Name" },
				{ "data": "Gene_Counts" },
				{ "data": "Actions" }
	        ]
	    });



        $(document).on('click', '.btn_select_pathway_show_modal', function() {
        	$('#modal_select_pathway').modal('show');

			$('#target_field').val( $(this).attr('target_field') );
        });

        $(document).on('click', '.btn_select_search_pathway', function() {

            var content = $(this).attr('content');
            var displayed_name = $(this).attr('displayed_name');

            $('#' + $('#target_field').val() ).val( content );
			$('#' + $('#target_field').val() + '_text' ).html( displayed_name );

            $('#modal_select_pathway').modal('hide');

        });


        // File Upload
        var options = {
            url: 'gene_list_exe.php?action=show_venn_diagram',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {
				if( $('#comparison1').val() == '' || $('#comparison2').val() == ''){
					bootbox.alert("Please select at least two gene lists. ");
					return false;
				}

                $('#form_upload_file_busy').removeClass('hidden');

                return true;
            },
            success: function(response){
                $('#form_upload_file_busy').addClass('hidden');
                $('#div_debug').html(response);

                return true;
            }
        };
        $('#form_main').ajaxForm(options);


		$(document).on('click', '.content_detail',function(){
			var type = $(this).attr('type');
			var method = $(this).attr('method');
			var other = $(this).attr('other');
			var case0 = $(this).attr('case');
			var title = $(this).attr('title');
			$.ajax({
				method: 'POST',
				url: 'comparison_exe.php?action=get_content_detail&type=' + type + '&method=' + method + '&other=' + other + '&case=' + case0,
				success: function(responseText, statusText){
					bootbox.alert({
						title: title,
						message: responseText,
						callback: function(){}
					});
				}
			})
		})

		$(document).on('change', '.content_detail_radio', function(){
			if($('#content_detail0').is(":checked")){
				$('#content_detail1_div').addClass('hidden');
				$('#content_detail0_div').hide().removeClass('hidden').fadeIn(0);
			} else {
				$('#content_detail0_div').addClass('hidden');
				$('#content_detail1_div').hide().removeClass('hidden').fadeIn(0);
			}
		})

    });

</script>


</body>
</html>