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
		echo "<tr><td>{$row['Name']}</td><td>{$row['Count']}</td><td><a href='Javascript: void(0);' class='btn_select_saved_lists mr-3' content='{$content}'><i class='fas fa-check'></i> Select</a> <a href='../../gene_expressions/app_list_review.php?ID={$row['ID']}' target='_blank'><i class='fas fa-list'></i> Review</a></td></tr>";
	}
	echo "</tbody></table>";


	exit();
}




$comparison_names = array();
if (isset($_GET['id']) && intval($_GET['id']) >= 0) {
    $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ID` = ?i";
    $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, intval($_GET['id']) );
}
else if (isset($_GET['ids']) && trim($_GET['ids']) != '') {
    $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ID` IN (?a)";
    $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, explode(',', $_GET['ids']) );
}
else if (isset($_GET['comparison_id']) && intval($_GET['comparison_id']) >= 0) {
    $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ID` = ?i";
    $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, intval($_GET['comparison_id']) );
}
else if (isset($_GET['comparison_ids']) && trim($_GET['comparison_ids']) != '') {
    $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ID` IN (?a)";
    $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, explode(',', $_GET['comparison_ids']) );
}
else if (isset($_GET['project_id']) && intval($_GET['project_id']) >= 0) {
    $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `_Projects_ID` = ?i";
    $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, intval($_GET['project_id']) );
}
else if (isset($_GET['comparison_list']) && intval($_GET['comparison_list']) > 0) {
    $sql = "SELECT `Items` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_USERSAVEDLISTS']}` WHERE `ID` = ?i ";
    $list_items = $BXAF_MODULE_CONN -> get_one($sql, intval($_GET['comparison_list']) );
    $ids = unserialize($list_items);

    if(is_array($ids) && count($ids) > 0) {
        $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ID` IN (?a)";
        $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, $ids);
    }
}
else if (isset($_GET['comparison_time']) && $_GET['comparison_time'] != '' && is_array($_SESSION['SAVED_LIST']) && array_key_exists($_GET['comparison_time'], $_SESSION['SAVED_LIST']) ) {
    $sql = "SELECT DISTINCT `Name` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ID` IN (?a)";
    $comparison_names = $BXAF_MODULE_CONN -> get_col($sql, $_SESSION['SAVED_LIST'][ $_GET['comparison_time'] ] );
}

if(isset($comparison_names_custom) && is_array($comparison_names_custom) && count($comparison_names_custom) > 0){
    $comparison_names = array_merge($comparison_names, array_values($comparison_names_custom));
}
sort($comparison_names);


?>

<div class="w-100 mb-2">
	<span class="font-weight-bold">Comparisons:</span>

	<a class="ml-3 btn_saved_comparison_lists" href="javascript:void(0);" category="Comparison" data_target="Comparison_List"> <i class="fas fa-angle-double-right"></i> Load from saved lists </a>

	<a class="ml-3" href="Javascript: void(0);" id="btn_search_comparison"> <i class="fas fa-search"></i> Search and Select </a>

	<a class="ml-3" href="Javascript: void(0);" onclick="$('#Comparison_List').val('');"> <i class="fas fa-times"></i> Clear </a>

</div>

<textarea class="form-control" onBlur="$('#comparison_file').val('');" style="height:10rem;" name="Comparison_List" id="Comparison_List" category="Comparison"><?php echo implode("\n", $comparison_names); ?></textarea>

<div class="modal" id="modal_select_comparison" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Search Comparison</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <?php
          echo '
          <table class="table table-bordered table-striped table-hover w-100 datatables">
            <thead>
            <tr class="table-info">
              <th>Name</th>
              <th>DiseaseState</th>
              <th>ComparisonContrast</th>
              <th>CellType</th>
            </tr>
            </thead>
            <tbody>';

			$sql = "SELECT `ComparisonIndex`, `ComparisonID`, `Case_CellType`, `Case_DiseaseState`, `ComparisonContrast` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE 1 ORDER BY `ComparisonID`";
			$comparisons = $BXAF_MODULE_CONN -> get_all($sql);

            foreach ($comparisons as $comparison) {
              echo '
              <tr>
                <td class="text-nowrap">' . $comparison['ComparisonID'] . '
                  <a href="javascript:void(0);" class="btn_select_search_comparison ml-2" content="' . $comparison['ComparisonID'] . '"><i class="fas fa-angle-double-right"></i> Select</a>
                </td>
                <td>' . $comparison['Case_DiseaseState'] . '</td>
                <td>' . $comparison['ComparisonContrast'] . '</td>
                <td>' . $comparison['Case_CellType'] . '</td>
              </tr>';
            }
          echo '
            </tbody>
          </table>
          ';
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade modal_saved_comparison_lists" id="modal_saved_comparison_lists" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">My Saved List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <input type="hidden" id="target_saved_comparison_lists" name="target_saved_comparison_lists" value="" />
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>

    $(document).ready(function() {

      	$(document).on('click', '#btn_search_comparison', function() {
			$('.datatables').DataTable();
      		$('#modal_select_comparison').modal('show');
      	});
      	$(document).on('click', '.btn_select_search_comparison', function() {
      		var comparison_name = $(this).attr('content');
              $('#Comparison_List').val( comparison_name + "\n" + $('#Comparison_List').val() );
      		$('#modal_select_comparison').modal('hide');
      	});


        // Select from Saved List
        $(document).on('click', '.btn_saved_comparison_lists', function() {
            var category = $(this).attr('category');
            $('#target_saved_comparison_lists').val( $(this).attr('data_target') );

            $.ajax({
    			type: 'GET',
    			url: '../tool_data_view/modal_comparison.php?action=get_saved_lists&category=' + category,
    			success: function(responseText){
    				$('#modal_saved_comparison_lists').find('.modal-body').html(responseText);
                    $('#modal_saved_comparison_lists').modal('show');

                    $('.datatables').DataTable();

    			}
    		});

    	});

        $(document).on('click', '.btn_select_saved_lists', function() {

            var target  = $('#target_saved_comparison_lists').val();

            var current_content  = $('#' + target).val();
            var new_content = $(this).attr('content');

            $('#' + target).val( new_content + "\n" + current_content );

    		$('#modal_saved_comparison_lists').modal('hide');
    	});

    });

</script>
