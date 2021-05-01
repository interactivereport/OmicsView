<?php
include_once("config.php");


// Get Saved List
if (isset($_GET['action']) && $_GET['action'] == 'get_saved_lists') {

	$type_list = array('comparison', 'gene', 'project', 'sample');
	$category = strtolower($_GET['category']);
	if(! in_array($category, $type_list)){
		echo "<h4 class='text-danger'>Error: no saved list found.</h4>";
		exit();
	}

	$sql = "SELECT * FROM ?n WHERE `User_ID` = ?i AND `Category` = ?s ORDER BY `Name`";
	$lists_data = $BXAF_MODULE_CONN -> get_all($sql, $BXAF_CONFIG['TBL_BXGENOMICS_USERSAVEDLISTS'], $_SESSION['User_Info']['ID'], $category);

	echo "<table class='table table-bordered table-hover'>";
	echo "<thead><tr class='table-info'><th>Name</th><th>Count</th><th>Action</th></tr></thead>";
	echo "<tbody>";

	$k = ucfirst($category) . "ID";
	if($category == 'gene') $k = 'geneName';

	foreach ($lists_data as $row) {
		$content = json_decode($row['Items'], true);
		$content = $content[$k];
		$content = implode("\n", $content);
		echo "<tr><td><a href='../../gene_expressions/app_list_review.php?ID={$row['ID']}' target='_blank'>{$row['Name']}</a></td><td>{$row['Count']}</td><td><a href='Javascript: void(0);' class='btn_select_saved_lists' content='{$content}'><i class='fas fa-check'></i> Select</a></td></tr>";
	}
	echo "</tbody></table>";


	exit();
}

?>


<div class="modal fade modal_saved_lists" id="modal_saved_lists" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">My Saved List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <input type="hidden" id="target_saved_lists" name="target_saved_lists" value="" />
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>

    $(document).ready(function() {

        // Select from Saved List
        $(document).on('click', '.btn_saved_lists', function() {
            var category = $(this).attr('category');
            $('#target_saved_lists').val( $(this).attr('data_target') );

            $.ajax({
    			type: 'GET',
    			url: 'modals.php?action=get_saved_lists&category=' + category,
    			success: function(responseText){
    				$('#modal_saved_lists').find('.modal-body').html(responseText);
                    $('#modal_saved_lists').modal('show');

    			}
    		});

    	});
        $(document).on('click', '.btn_select_saved_lists', function() {

            var target  = $('#target_saved_lists').val();

            var current_content  = $('#' + target).val();
            var new_content = $(this).attr('content');

            $('#' + target).val( new_content + "\n" + current_content );

    		$('#modal_select_comparison').modal('hide');
    	});

    });

</script>
