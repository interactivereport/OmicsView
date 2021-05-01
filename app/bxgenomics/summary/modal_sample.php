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
		echo "<tr><td>{$row['Name']}</td><td>{$row['Count']}</td><td><a href='Javascript: void(0);' class='btn_select_saved_sample_lists mr-3' content='{$content}'><i class='fas fa-check'></i> Select</a> <a href='../../gene_expressions/app_list_review.php?ID={$row['ID']}' target='_blank'><i class='fas fa-list'></i> Review</a></td></tr>";
	}
	echo "</tbody></table>";


	exit();
}

// Get project samples
if (isset($_GET['action']) && $_GET['action'] == 'get_project_samples') {

	$sql = "SELECT `SampleID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `ProjectName` = ?s";
	$samples = $BXAF_MODULE_CONN -> get_col($sql, $_GET['ProjectName']);

	if(is_array($samples) && count($samples) > 0) echo implode("\n", $samples);

	exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'get_sample_list'){

	$filter = '1';
	if(isset($_GET['SampleID']) && $_GET['SampleID'] != '') $filter .= " AND `SampleID` LIKE '%" . addslashes($_GET['SampleID']). "%' ";
	if(isset($_GET['DiseaseState']) && $_GET['DiseaseState'] != '') $filter .= " AND `DiseaseState` LIKE '%" . addslashes($_GET['DiseaseState']). "%' ";
	if(isset($_GET['PlatformGPL']) && $_GET['PlatformGPL'] != '') $filter .= " AND `PlatformGPL` LIKE '%" . addslashes($_GET['PlatformGPL']). "%' ";
	if(isset($_GET['PlatformName']) && $_GET['PlatformName'] != '') $filter .= " AND `PlatformName` LIKE '%" . addslashes($_GET['PlatformName']). "%' ";

	$table = $BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES'];
    $sql2 = "SELECT * FROM `$table` WHERE $filter ";

    $sql = "";
    // Search Condition
    if(isset($_POST['search']['value']) && trim($_POST['search']['value']) != '') {
    	$search_array = array();
    	for ($i = 0; $i < count($_POST['columns']); $i++){
            $search_array[] = "`" . $_POST['columns'][$i]['data'] . "` LIKE '%" . $_POST['search']['value'] . "%'";
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
			if($k == 'SampleID'){
				$row[$k] = "" . $v . "<a title='Select this gene sample' href='Javascript: void(0);' sample_id='" . $value['SampleIndex'] . "' content='" . $v . "' class='btn_select_current_sample mx-2'><i class='fas fa-check-circle'></i> Select</a>";
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






$sample_names = array();
if (isset($_GET['sample_id']) && intval($_GET['sample_id']) >= 0) {
    $sql = "SELECT DISTINCT `SampleID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `SampleIndex` = ?i";
    $sample_names = $BXAF_MODULE_CONN -> get_col($sql, intval($_GET['sample_id']) );
}
else if (isset($_GET['sample_ids']) && trim($_GET['sample_ids']) != '') {
    $sql = "SELECT DISTINCT `SampleID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `SampleIndex` IN (?a)";
    $sample_names = $BXAF_MODULE_CONN -> get_col($sql, explode(',', $_GET['sample_ids']) );
}
else if (isset($_GET['project_id']) && intval($_GET['project_id']) >= 0) {
	$sql = "SELECT `ProjectID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS']}` WHERE `ProjectIndex` = ?i";
    $project_name = $BXAF_MODULE_CONN -> get_one($sql, intval($_GET['project_id']) );

    $sql = "SELECT `SampleID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `ProjectName` = ?s";
    $sample_names = $BXAF_MODULE_CONN -> get_col($sql, $project_name );
}
else if (isset($_GET['Sample_List']) && intval($_GET['Sample_List']) > 0) {

    $sql = "SELECT `Items` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_USERSAVEDLISTS']}` WHERE `SampleIndex` = ?i ";
    $list_items = $BXAF_MODULE_CONN -> get_one($sql, intval($_GET['Sample_List']) );
    $ids = unserialize($list_items);

    if(is_array($ids) && count($ids) > 0) {
        $sql = "SELECT DISTINCT `SampleID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `SampleIndex` IN (?a)";
        $sample_names = $BXAF_MODULE_CONN -> get_col($sql, $ids);
    }
}
else if (isset($_GET['sample_time']) && $_GET['sample_time'] != '' && is_array($_SESSION['SAVED_LIST']) && array_key_exists($_GET['sample_time'], $_SESSION['SAVED_LIST']) ) {
    $sql = "SELECT DISTINCT `SampleID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `SampleIndex` IN (?a)";
    $sample_names = $BXAF_MODULE_CONN -> get_col($sql, $_SESSION['SAVED_LIST'][ $_GET['sample_time'] ] );
}

if(isset($sample_names_custom) && is_array($sample_names_custom) && count($sample_names_custom) > 0){
    $sample_names = array_merge($sample_names, array_values($sample_names_custom));
}
sort($sample_names);

?>



<div class="w-100 mb-2">
	<span class="font-weight-bold">Samples:</span>

	<a class="ml-3 btn_saved_sample_lists" href="javascript:void(0);" category="Sample" data_target="Sample_List"> <i class="fas fa-angle-double-right"></i> Load from saved lists </a>

	<a class="ml-3" href="Javascript: void(0);" id="btn_select_sample"> <i class="fas fa-search"></i> Search and Select </a>

	<a class="ml-3" href="Javascript: void(0);" id="btn_select_project"> <i class="fas fa-search"></i> Select a Project </a>

	<a class="ml-3" href="Javascript: void(0);" onclick="$('#Sample_List').val('');"> <i class="fas fa-times"></i> Clear </a>

</div>

<textarea class="form-control" style="height:10rem;" name="Sample_List" id="Sample_List" category="Sample"><?php echo implode("\n", $sample_names); ?></textarea>



<div class="modal" id="modal_select_sample" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Search Samples</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
		<table class="table table-bordered table-striped table-hover w-100 datatables" id="table_samples">
			<thead>
				<tr class="table-info">
					<th>SampleID</th>
					<th>DiseaseState</th>
					<th>PlatformGPL</th>
					<th>PlatformName</th>
				</tr>
			</thead>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		<input type="hidden" id="modal_select_sample_initiated" value="">
      </div>
    </div>
  </div>
</div>


<div class="modal" id="modal_select_project" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Search Samples</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
		<table class="table table-bordered table-striped table-hover w-100 datatables" id="table_projects">
			<thead>
				<tr class="table-info">
					<th>Project Name</th>
					<th>Samples</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>

<?php
	$sql = "SELECT `ProjectIndex`, `ProjectID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS']}` ORDER BY `ProjectID`";
	$projects = $BXAF_MODULE_CONN -> get_assoc('ProjectIndex', $sql );

	$sql = "SELECT `ProjectName`, COUNT(*) FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` GROUP BY `ProjectName`";
	$project_samples = $BXAF_MODULE_CONN -> get_assoc('ProjectName', $sql );

	foreach($projects as $projectindex => $ProjectName){
		$count = array_key_exists($ProjectName, $project_samples) ? $project_samples[$ProjectName] : 0;
		if($count <= 0) continue;
		echo "<tr>";
			echo "<td>$ProjectName</td>";
			echo "<td>" . $count . "</td>";
			echo "<td><a href='Javascript: void(0);' class='btn_select_current_project mr-3' ProjectName='{$ProjectName}'><i class='fas fa-check'></i> Select</a> <a href='../../plot/search_comparison/single_comparison.php?type=project&id={$projectindex}' target='_blank'><i class='fas fa-list'></i> Review</a></td>";
		echo "</tr>";
	}
?>
			</tbody>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade modal_saved_sample_lists" id="modal_saved_sample_lists" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">My Saved List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <input type="hidden" id="target_saved_sample_lists" name="target_saved_sample_lists" value="" />
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>

    $(document).ready(function() {

      	$(document).on('click', '#btn_select_sample', function() {

			if( $('#modal_select_sample_initiated').val() == '' ){

				$('#modal_select_sample_initiated').val(1);

				$('#table_samples').DataTable({
			        "processing": true,
			        "serverSide": true,
			        "ajax": {
			            "url": "../summary/modal_sample.php?action=get_sample_list<?php if($_SERVER['QUERY_STRING'] != '') echo '&' . $_SERVER['QUERY_STRING']; ?>",
			            "type": "POST"
			        },
			        "columns": [
						{ "data": "SampleID" },
						{ "data": "DiseaseState" },
						{ "data": "PlatformGPL" },
						{ "data": "PlatformName" }
			        ]
			    });

				$('#table_samples').on( 'draw.dt', function () {
					$('#modal_select_sample').modal('show');
				});
			}
			else {
				$('#modal_select_sample').modal('show');
			}

      	});

      	$(document).on('click', '.btn_select_current_sample', function() {
      		var name = $(this).attr('content');
              $('#Sample_List').val( name + "\n" + $('#Sample_List').val() );
      		$('#modal_select_sample').modal('hide');
      	});

		$('#table_projects').DataTable();
		$(document).on('click', '#btn_select_project', function() {
			$('#modal_select_project').modal('show');
		});
      	$(document).on('click', '.btn_select_current_project', function() {
			$.ajax({
    			type: 'GET',
    			url: '../summary/modal_sample.php?action=get_project_samples&ProjectName=' + $(this).attr('ProjectName'),
    			success: function(responseText){
					$('#Sample_List').val( responseText + "\n" + $('#Sample_List').val() );
					$('#modal_select_project').modal('hide');
    			}
    		});
      	});


        // Select from Saved List
        $(document).on('click', '.btn_saved_sample_lists', function() {
            var category = $(this).attr('category');
            $('#target_saved_sample_lists').val( $(this).attr('data_target') );

            $.ajax({
    			type: 'GET',
    			url: '../summary/modal_sample.php?action=get_saved_lists&category=' + category,
    			success: function(responseText){
    				$('#modal_saved_sample_lists').find('.modal-body').html(responseText);
                    $('#modal_saved_sample_lists').modal('show');

                    $('.datatables').DataTable();

    			}
    		});

    	});

        $(document).on('click', '.btn_select_saved_sample_lists', function() {

            var target  = $('#target_saved_sample_lists').val();

            var current_content  = $('#' + target).val();
            var new_content = $(this).attr('content');

            $('#' + target).val( new_content + "\n" + current_content );

    		$('#modal_saved_sample_lists').modal('hide');
    	});

    });

</script>
