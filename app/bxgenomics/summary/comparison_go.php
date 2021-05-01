<?php
include_once(__DIR__ . "/config.php");


if (isset($_GET['action']) && $_GET['action'] == 'list'){

	$table = 'tbl_comparison_go_enrichment';
	$filter = '1';
	if(isset($_GET['comparison1']) && $_GET['comparison1'] != '') $filter .= " AND `Comparison_Name` = '" . addslashes($_GET['comparison1']). "' ";
	if(isset($_GET['GO_Tree']) && $_GET['GO_Tree'] != '') $filter .= " AND `GO_Tree` = '" . addslashes($_GET['GO_Tree']). "' ";
	if(isset($_GET['Term']) && $_GET['Term'] != '') $filter .= " AND `Term` LIKE '%" . addslashes($_GET['Term']). "%' ";
	if(isset($_GET['Direction']) && $_GET['Direction'] != '') $filter .= " AND `Direction` = '" . addslashes($_GET['Direction']). "' ";
	if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] != '') $filter .= " AND `logP` < " . log(pow(10, intval($_GET['P_Value_Cutoff']))) . " ";

    $sql2 = "SELECT * FROM `$table` WHERE $filter ";

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

	if(isset($_POST['order']) && is_array($_POST['order']) && count($_POST['order']) > 0){
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
	}


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
            if($k == 'Comparison_Name'){
				$row[$k] = '<a href="' . $BXAF_CONFIG['BXAF_APP_URL'] . 'plot/search_comparison/single_comparison.php?type=comparison&id=' . $value['Comparison_Index'] . '">' . $v . '</a>';
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

			<div id="bxaf_page_right_content" class="w-100 p-2">

				<h1>
					GO Enrichment of Comparisons

					<a href='Javascript: void(0);' onclick="if( $('#form_main').hasClass('hidden') ) $('#form_main').removeClass('hidden'); else $('#form_main').addClass('hidden');" style='font-size: 1rem;'><i class='fas fa-search'></i> Advanced Search</a>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="mx-1" style='font-size: 1rem;'><i class='fas fa-sync'></i> Reset Search Condition</a>
				</h1>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" class="hidden" id="form_main" method="get">
				<div class="w-100 my-3 border border-primary rounded">
					<div class="row m-3">
						<div class="col-md-12 col-lg-4 my-2">
							<div class="form-group my-0 font-weight-bold">
								<a href="Javascript: void(0);" class="btn_select_pathway_show_modal font-weight-bold" target_field="comparison1">
			                      <i class="fas fa-angle-double-right"></i> Select Comparison
			                    </a>
							</div>

							<input type="text" class="form-control" id="comparison1" name="comparison1" value="<?php if($_GET['comparison1'] != '') echo $_GET['comparison1']; ?>">
						</div>

						<div class="col-md-12 col-lg-4 my-2">
							<div class="form-group my-0 font-weight-bold">
			                    	GO Tree:
							</div>

							<select class="custom-select" name="GO_Tree" id="GO_Tree">
							<?php
								$sql = "SELECT DISTINCT `GO_Tree` FROM `tbl_comparison_go_enrichment_10_2`";
								$options = $BXAF_MODULE_CONN->get_col($sql);
								array_unshift($options, '');
								$default = '';
								if($_GET['GO_Tree'] != '') $default = $_GET['GO_Tree'];
								foreach($options as $opt) echo "<option value='$opt' " . ($default == $opt ? 'selected' : '') . ">$opt</option>";
							?>
							  </select>
						</div>

						<div class="col-md-12 col-lg-4 my-2">
							<div class="form-group my-0 font-weight-bold">
			                    	GO Term:
							</div>

							<input type="text" class="form-control" id="Term" name="Term" value="<?php if($_GET['Term'] != '') echo $_GET['Term']; ?>">
						</div>

						<div class="col-md-12 col-lg-6 my-2">

							<div class="form-group form-check form-check-inline my-1">
								<label class="form-check-label font-weight-bold">Regulation: </label>

								<input class="form-check-input mx-2" type="radio" name="Direction" value="Up" <?php if(isset($_GET['Direction']) && $_GET['Direction'] == 'Up') echo 'checked'; ?>>
								<label class="form-check-label">Up Regulated</label>

								<input class="form-check-input mx-2" type="radio" name="Direction" value="Down" <?php if(isset($_GET['Direction']) && $_GET['Direction'] == 'Down') echo 'checked'; ?>>
								<label class="form-check-label">Down Regulated</label>

								<input class="form-check-input mx-2" type="radio" name="Direction" value="" <?php if(! isset($_GET['Direction']) || $_GET['Direction'] == '') echo 'checked'; ?>>
								<label class="form-check-label">Both</label>
							</div>
						</div>

						<div class="col-md-12 col-lg-6 my-2">

							<div class="form-group form-check form-check-inline my-1">

								<label class="form-check-label font-weight-bold">P-Value Cutoff: </label>

								<div class="form-check form-check-inline mx-2">
								  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-20" <?php if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] == '-20') echo 'checked'; ?>>
								  <label class="form-check-label">10<sup>-20</sup></label>
								</div>
								<div class="form-check form-check-inline mx-2">
								  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-15" <?php if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] == '-15') echo 'checked'; ?>>
								  <label class="form-check-label">10<sup>-15</sup></label>
								</div>
								<div class="form-check form-check-inline mx-2">
								  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-12" <?php if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] == '-12') echo 'checked'; ?>>
								  <label class="form-check-label">10<sup>-12</sup></label>
								</div>
								<div class="form-check form-check-inline mx-2">
								  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-9" <?php if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] == '-9') echo 'checked'; ?>>
								  <label class="form-check-label">10<sup>-9</sup></label>
								</div>
								<div class="form-check form-check-inline mx-2">
								  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-6" <?php if(! isset($_GET['P_Value_Cutoff']) || $_GET['P_Value_Cutoff'] == '-6' || $_GET['P_Value_Cutoff'] == '') echo 'checked'; ?>>
								  <label class="form-check-label">10<sup>-6</sup></label>
								</div>
							</div>
						</div>

					</div>

                    <div class="row my-3 mx-5">
                        <button class="btn btn-primary" type="submit"><i class='fas fa-search'></i> Search</button>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default mx-1"><i class='fas fa-sync'></i> Reset</a>
                    </div>
				</div>
                </form>


			    <div class="w-100 my-5">
			    	<table class="table" id="myTable">
			    		<thead>
			    			<tr>
			                    <th>Comparison</th>
			    				<th>Regulation</th>
								<th>GO Tree</th>
			    				<th>P-Value</th>
			                    <th>LogP</th>
			                    <th>Term</th>
			                    <th>GO Term ID</th>
			                    <th>Genes in Term</th>
			                    <th>Target Genes in Term</th>
			                    <th>Total Genes</th>
								<th>Total Target Genes</th>
			    			</tr>
			    		</thead>
			    	</table>
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
					<h4 class="modal-title">Select Comparison</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<?php
						echo '
						<table class="table table-bordered" id="table_select_pathway">
							<thead>
							<tr>
	                            <th>Comparison Index</th>
								<th>Comparison Name</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody>';

							$sql_comparison = "SELECT `ComparisonIndex`, `ComparisonID` FROM `Comparisons` ";
							$comparison_id_names = $BXAF_MODULE_CONN->get_assoc('ComparisonIndex', $sql_comparison);

	                        foreach ($comparison_id_names as $key => $value) {
								echo '
								<tr>
	                                <td>' . $key . '</td>
									<td>' . $value . '</td>
									<td><a href="javascript:void(0);" class="btn_select_search_pathway" content="' . $key . '" displayed_name="' . $value . '"><i class="fas fa-angle-double-right"></i> Select</a></td>
								</tr>';
							}

						echo '
							</tbody>
						</table>
						';
					?>
				</div>
				<div class="modal-footer">
					<input type="hidden" id="target_field" name="target_field" value="">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>





	<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js'></script>

	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js "></script>
	<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/1.10.16/sorting/natural.js"></script>


<script type="text/javascript">

	$(document).ready(function(){

        $('#table_select_pathway').DataTable();

        $(document).on('click', '.btn_select_pathway_show_modal', function() {
        	$('#modal_select_pathway').modal('show');

			$('#target_field').val( $(this).attr('target_field') );
        });

        $(document).on('click', '.btn_select_search_pathway', function() {
            var content = $(this).attr('content');
            var displayed_name = $(this).attr('displayed_name');

            $('#' + $('#target_field').val() ).val( displayed_name );

            $('#modal_select_pathway').modal('hide');

        });




		$('#myTable').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": "<?php echo $_SERVER['PHP_SELF']; ?>?action=list<?php if($_SERVER['QUERY_STRING'] != '') echo '&' . $_SERVER['QUERY_STRING']; ?>",
	            "type": "POST"
	        },
	        dom: "Blfrtip",
	        buttons: [
	            'copy', 'csv', 'excel', 'pdf', 'print'
	        ],
			"paging": true,
			"pageLength": 25,
	        "lengthMenu": [[25, 100, 250, 1000, 5000], [25, 100, 250, 1000, 5000]],
	        "order": [[3, 'desc']],
	        "columns": [
	            { "data": "Comparison_Name" },
	            { "data": "Direction" },
				{ "data": "GO_Tree" },
	            { "data": "Enrichment" },
	            { "data": "logP" },
	            { "data": "Term" },
	            { "data": "TermID" },
				{ "data": "Genes_in_Term" },
				{ "data": "Target_Genes_in_Term" },
	            { "data": "Total_Genes" },
	            { "data": "Total_Target_Genes" }
	        ]
	    });

	});

</script>


</body>
</html>