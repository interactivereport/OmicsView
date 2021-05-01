<?php
include_once( __DIR__ . '/config.php');


// Get Saved List
if (isset($_GET['action']) && $_GET['action'] == 'get_saved_lists') {

	$type_list = array('comparison', 'gene', 'project', 'sample');
	$category = strtolower($_GET['category']);
	if(! in_array($category, $type_list)){
		echo "<h4 class='text-danger'>Error: no saved list found.</h4>";
		exit();
	}

	$sql = "SELECT * FROM ?n WHERE `Category` = ?s AND `User_ID` = ?i ORDER BY `Name`";
	$lists_data = $BXAF_MODULE_CONN -> get_all($sql, $BXAF_CONFIG['TBL_BXGENOMICS_USERSAVEDLISTS'], ucfirst($category), $_SESSION['User_Info']['ID']);

	echo "<table class='table table-bordered table-striped table-hover w-100 datatables'>";
	echo "<thead><tr class='table-info'><th>Name</th><th>Count</th><th>Action</th></tr></thead>";
	echo "<tbody>";

	foreach ($lists_data as $row) {
		$lists = json_decode($row['Items'], true);
		$content = implode("\n", $lists['Combined'] );
		echo "<tr><td>{$row['Name']}</td><td>{$row['Count']}</td><td><a href='Javascript: void(0);' class='btn_select_saved_gene_lists mr-3' content='{$content}'><i class='fas fa-check'></i> Select</a> <a href='../../gene_expressions/app_list_review.php?ID={$row['ID']}' target='_blank'><i class='fas fa-list'></i> Review</a></td></tr>";
	}
	echo "</tbody></table>";


	exit();
}



if(isset($_GET['action']) && $_GET['action'] == "get_geneset_genes") {

	$id = intval($_GET['id']);
	$sql = "SELECT * FROM `tbl_go_gene_list` WHERE `Species` = '{$BXAF_CONFIG['SPECIES']}' AND `ID` = ?i";
	$info = $BXAF_MODULE_CONN->get_row($sql, $id);

	$IDs = array();
	$Names = array();

	echo implode("\n", explode(", ", trim(trim($info['Gene_Names'], ',')) ) );

	exit();
}


if(isset($_GET['action']) && $_GET['action'] == "get_geneset_detail") {

	$id = intval($_GET['id']);
	$sql = "SELECT * FROM `tbl_go_gene_list` WHERE `Species` = '{$BXAF_CONFIG['SPECIES']}'  AND `ID` = ?i";
	$info = $BXAF_MODULE_CONN->get_row($sql, $id);

	$IDs = array();
	$Names = array();

	$IDs = explode(",", $info['Gene_IDs']);
	$Names = explode(", ", trim(trim($info['Gene_Names'], ',')) );

	echo '
		<div class="w-100 p-1">
			<div class="lead">Display Method:</div>
			<div>
				<input type="radio" class=" mx-2" name="content_detail" id="" value="0" onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_0\').removeClass(\'hidden\'); ">
				Gene IDs, one per row <BR />
				<input type="radio" class=" mx-2" name="content_detail" id="" value="2" onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_2\').removeClass(\'hidden\'); ">
				Gene IDs, comma seperated <BR />
				<input type="radio" class=" mx-2" name="content_detail" id="" value="1" checked  onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_1\').removeClass(\'hidden\'); ">
				Gene Names, one per row <BR />
				<input type="radio" class=" mx-2" name="content_detail" id="" value="3" onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_3\').removeClass(\'hidden\'); ">
				Gene Names, comma seperated <BR />
			</div>
		</div>

		<hr>

		<div class="w-100 p-1">
			<textarea class="hidden content_detail_all" id="textarea_content_0" style="height:300px; width:100%;">'. implode("\n", $IDs) . '</textarea>
			<textarea class="       content_detail_all" id="textarea_content_1" style="height:300px; width:100%;">'. implode("\n", $Names) . '</textarea>
			<textarea class="hidden content_detail_all" id="textarea_content_2" style="height:300px; width:100%;">'. implode(', ', $IDs) . '</textarea>
			<textarea class="hidden content_detail_all" id="textarea_content_3" style="height:300px; width:100%;">'. implode(', ', $Names) . '</textarea>
		</div>
		';

		echo "<div class='w-100 my-2 text-right'><a title='Select this geneset' href='Javascript: void(0);' geneset_id='" . $id . "' geneset_name='" . $info['Name'] . "' class='btn_select_current_geneset mx-2'><i class='fas fa-check-circle'></i> Select This Gene Set</a></div>";

	exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'get_geneset_list'){

	$table = 'tbl_go_gene_list';
	$filter = " `Species` = '{$BXAF_CONFIG['SPECIES']}' ";
	if(isset($_GET['Category']) && $_GET['Category'] != '') $filter .= " AND `Category` = '" . addslashes($_GET['Category']). "' ";
	if(isset($_GET['Code']) && $_GET['Code'] != '') $filter .= " AND `Code` LIKE '%" . addslashes($_GET['Code']). "%' ";
	if(isset($_GET['Name']) && $_GET['Name'] != '') $filter .= " AND `Name` LIKE '%" . addslashes($_GET['Name']). "%' ";
	if(isset($_GET['Gene_Counts']) && $_GET['Gene_Counts'] != '') $filter .= " AND `Gene_Counts` < " . intval($_GET['Gene_Counts']). " ";
	if(isset($_GET['Gene_Names']) && $_GET['Gene_Names'] != '') $filter .= " AND `Gene_Names` LIKE '%" . addslashes($_GET['Gene_Names']). "%' ";

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
			if($k == 'Gene_Names'){
				$row[$k] = "<a title='Select this gene set' href='Javascript: void(0);' geneset_id='" . $value['ID'] . "' geneset_name='" . $value['Name'] . "' class='btn_select_current_geneset mx-2'><i class='fas fa-check-circle'></i></a> <a title='Show genes in this gene set' href='Javascript: void(0);' geneset_id='" . $value['ID'] . "' geneset_name='" . $value['Name'] . "' class='btn_show_content_detail mx-2'><i class='fas fa-list'></i></a>";
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



$gene_names = array();
if (isset($_GET['gene_id']) && intval($_GET['gene_id']) >= 0) {
    $sql = "SELECT DISTINCT `GeneName` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_GENES']}` WHERE `ID` = ?i";
    $gene_names = $BXAF_MODULE_CONN -> get_col($sql, intval($_GET['gene_id']) );
}
else if (isset($_GET['gene_ids']) && trim($_GET['gene_ids']) != '') {
	$gene_names = category_text_to_idnames($_GET['gene_ids'], 'id', 'gene');
}
else if (isset($_GET['gene_list']) && intval($_GET['gene_list']) > 0) {
    $sql = "SELECT `Items` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_USERSAVEDLISTS']}` WHERE `ID` = ?i ";
    $list_items = $BXAF_MODULE_CONN -> get_one($sql, intval($_GET['gene_list']) );
    $ids = unserialize($list_items);

    if(is_array($ids) && count($ids) > 0) {
        $sql = "SELECT DISTINCT `GeneName` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_GENES']}` WHERE `ID` IN (?a)";
        $gene_names = $BXAF_MODULE_CONN -> get_col($sql, $ids);
    }
}
else if (isset($_GET['gene_time']) && $_GET['gene_time'] != '' && is_array($_SESSION['SAVED_LIST']) && array_key_exists($_GET['gene_time'], $_SESSION['SAVED_LIST']) ) {
    $sql = "SELECT DISTINCT `GeneName` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_GENES']}` WHERE `ID` IN (?a)";
    $gene_names = $BXAF_MODULE_CONN -> get_col($sql, $_SESSION['SAVED_LIST'][ $_GET['gene_time'] ] );
}
else if (isset($_GET['gene_name']) && trim($_GET['gene_name']) != '') {
	$gene_names = category_text_to_idnames($_GET['gene_name'], 'name', 'gene');
}
else if (isset($_GET['gene_names']) && trim($_GET['gene_names']) != '') {
	$gene_names = category_text_to_idnames($_GET['gene_names'], 'name', 'gene');
}
else if (isset($_GET['geneset_id']) && trim($_GET['geneset_id']) != '') {
    $sql = "SELECT `Gene_Names` FROM `tbl_go_gene_list` WHERE `Species` = '{$BXAF_CONFIG['SPECIES']}' AND `ID` = ?i";
    $genes = $BXAF_MODULE_CONN->get_one($sql, intval($_GET['geneset_id']));
    if($genes != '') $gene_names = explode(", ", $genes);
}
else if (isset($_GET['geneset']) && trim($_GET['geneset']) != '') {
    $sql = "SELECT `Gene_Names` FROM `tbl_go_gene_list` WHERE `Species` = '{$BXAF_CONFIG['SPECIES']}' AND `Name` = ?s";
    $genes = $BXAF_MODULE_CONN->get_one($sql, trim($_GET['geneset']));
    if($genes != '') $gene_names = explode(", ", $genes);
}


if(isset($gene_names_custom) && is_array($gene_names_custom) && count($gene_names_custom) > 0){
    $gene_names = array_merge($gene_names, array_values($gene_names_custom));
}
sort($gene_names);


?>


<div class="w-100 mb-2">
	<span class="font-weight-bold">Genes:</span>

	<a class="ml-3 btn_saved_gene_lists" href="javascript:void(0);" category="Gene" data_target="Gene_List"> <i class="fas fa-angle-double-right"></i> Load from saved lists </a>

	<a class="ml-3 btn_select_geneset" href="Javascript: void(0);" id="btn_select_geneset"> <i class="fas fa-search"></i> Load functional gene sets </a>

	<a class="ml-3" href="Javascript: void(0);" onclick="$('#Gene_List').val('');"> <i class="fas fa-times"></i> Clear </a>

</div>

<textarea class="form-control" style="height:10rem;" name="Gene_List" id="Gene_List" category="Gene"><?php echo implode("\n", $gene_names); ?></textarea>



<!-------------------------------------------------------------------------------------------------------->
<!-- Modal to Select MSigDB -->
<!-------------------------------------------------------------------------------------------------------->
<div class="modal fade" id="modal_select_geneset" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Select Gene Set</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
                <table class="table table-bordered table-striped table-hover w-100" id="table_geneset">
		    		<thead>
		    			<tr class="table-info">
							<th>ID</th>
		                    <th>Category</th>
		    				<th>Code</th>
							<th>Name</th>
							<th>Genes</th>
							<th>Actions</th>
		    			</tr>
		    		</thead>
		    	</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<input type="hidden" id="modal_select_geneset_initiated" value="">
			</div>
		</div>
	</div>
</div>



<div class="modal fade modal_saved_gene_lists" id="modal_saved_gene_lists" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">My Saved List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <input type="hidden" id="target_saved_gene_lists" name="target_saved_gene_lists" value="" />
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>

    $(document).ready(function() {

		$(document).on('click', '.btn_show_content_detail',function(){
			var geneset_id = $(this).attr('geneset_id');
			var geneset_name = $(this).attr('geneset_name');

			$.ajax({
				url: '../tool_data_view/modal_gene.php?action=get_geneset_detail&id=' + geneset_id,
				success: function(responseText, statusText){
					bootbox.alert({
						title: geneset_name,
						message: responseText,
						callback: function(){}
					});
				}
			})
		})

        $(document).on('click', '.btn_select_current_geneset',function(){
			var geneset_id = $(this).attr('geneset_id');
			var geneset_name = $(this).attr('geneset_name');

			$.ajax({
				url: '../tool_data_view/modal_gene.php?action=get_geneset_genes&id=' + geneset_id,
				success: function(responseText, statusText){

                    $('#Gene_List').val( responseText + "\n" + $('#Gene_List').val() );
					$('#modal_select_geneset').modal('hide');
				}
			})
		})

      	$(document).on('click', '.btn_select_geneset', function() {

			if( $('#modal_select_geneset_initiated').val() == '' ){

				$('#modal_select_geneset_initiated').val(1);

				$('#table_geneset').DataTable({
			        "processing": true,
			        "serverSide": true,
			        "ajax": {
			            "url": "../tool_data_view/modal_gene.php?action=get_geneset_list<?php if($_SERVER['QUERY_STRING'] != '') echo '&' . $_SERVER['QUERY_STRING']; ?>",
			            "type": "POST"
			        },
			        "columns": [
						{ "data": "ID" },
			            { "data": "Category" },
			            { "data": "Code" },
						{ "data": "Name" },
						{ "data": "Gene_Counts" },
						{ "data": "Gene_Names" }
			        ]
			    });

			}

      		$('#modal_select_geneset').modal('show');
      	});
      	$(document).on('click', '.btn_select_search_gene', function() {
      		var content = $(this).attr('content');
              $('#Gene_List').val( content + "\n" + $('#Gene_List').val() );
      		$('#modal_select_geneset').modal('hide');
      	});


        // Select from Saved List
        $(document).on('click', '.btn_saved_gene_lists', function() {
            var category = $(this).attr('category');
            $('#target_saved_gene_lists').val( $(this).attr('data_target') );

            $.ajax({
    			type: 'GET',
    			url: '../tool_data_view/modal_gene.php?action=get_saved_lists&category=' + category,
    			success: function(responseText){
    				$('#modal_saved_gene_lists').find('.modal-body').html(responseText);
                    $('#modal_saved_gene_lists').modal('show');

                    $('.datatables').DataTable();

    			}
    		});

    	});

        $(document).on('click', '.btn_select_saved_gene_lists', function() {

            var target  = $('#target_saved_gene_lists').val();

            var current_content  = $('#' + target).val();
            var new_content = $(this).attr('content');

            $('#' + target).val( new_content + "\n" + current_content );

    		$('#modal_saved_gene_lists').modal('hide');
    	});

    });

</script>
