<?php
include_once(__DIR__ . "/config.php");


$sql_comparison = "SELECT `ComparisonIndex`, `ComparisonID` FROM `Comparisons` ";
$comparison_id_names = $BXAF_MODULE_CONN->get_assoc('ComparisonIndex', $sql_comparison);


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
					Compare Comparisons Based on GO Analysis Results (Venn Diagram)
				</h1>
				<hr class="w-100 my-1" />

				<div class="my-5 w-100">

	                <form class="w-100" id="form_main" method="post">

						<div class="container-fluid">

							<div class="row my-3">

								<div class="col-md-4">
									<input type="text" title="Enter custom comparison name" placeholder="custom comparison name" class="form-control w-50" id="comparison_name1" name="comparison_name1" value="A">
									<input type="text" title="Enter comparison name" placeholder="comparison name" class="form-control my-1" id="comparison1" name="comparison1" value="<?php echo ($_GET['comparison1'] != '') ? $_GET['comparison1'] : ""; ?>">
									<a href="Javascript: void(0);" class="btn_select_pathway_show_modal ml-5" target_field="comparison1">
				                      <i class="fas fa-angle-double-right"></i> Select Comparison 1
				                    </a>

									<div class="form-inline px-1">
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Direction1" value="Up" <?php if(! isset($_GET['Direction1']) || $_GET['Direction1'] == 'Up') echo "checked"; ?>>
										  <label class="form-check-label">Up Regulated</label>
										</div>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Direction1" value="Down" <?php if(isset($_GET['Direction1']) && $_GET['Direction1'] == 'Down') echo "checked"; ?>>
										  <label class="form-check-label">Down Regulated</label>
										</div>
									</div>
								</div>

								<div class="col-md-4">
									<input type="text" title="Enter custom comparison name" placeholder="custom comparison name" class="form-control w-50" id="comparison_name2" name="comparison_name2" value="B">
									<input type="text" title="Enter comparison name" placeholder="comparison name" class="form-control my-1" id="comparison2" name="comparison2" value="<?php echo ($_GET['comparison2'] != '') ? $_GET['comparison2'] : ""; ?>">
									<a href="Javascript: void(0);" class="btn_select_pathway_show_modal ml-5" target_field="comparison2">
				                      <i class="fas fa-angle-double-right"></i> Select Comparison 2
				                    </a>

									<div class="form-inline my-2">
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Direction2" value="Up" <?php if(! isset($_GET['Direction2']) || $_GET['Direction2'] == 'Up') echo "checked"; ?>>
										  <label class="form-check-label">Up Regulated</label>
										</div>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Direction2" value="Down" <?php if(isset($_GET['Direction2']) && $_GET['Direction2'] == 'Down') echo "checked"; ?>>
										  <label class="form-check-label">Down Regulated</label>
										</div>
									</div>
								</div>

								<div class="col-md-4">
									<input type="text" title="Enter custom comparison name" placeholder="custom comparison name" class="form-control w-50" id="comparison_name3" name="comparison_name3" value="C">
									<input type="text" title="Enter comparison name" placeholder="comparison name" class="form-control my-1" id="comparison3" name="comparison3" value="<?php echo ($_GET['comparison3'] != '') ? $_GET['comparison3'] : ""; ?>">
									<a href="Javascript: void(0);" class="btn_select_pathway_show_modal ml-5" target_field="comparison3">
				                      <i class="fas fa-angle-double-right"></i> Select Comparison 3
				                    </a>

									<div class="form-inline my-2">
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Direction3" value="Up" <?php if(! isset($_GET['Direction3']) || $_GET['Direction3'] == 'Up') echo "checked"; ?>>
										  <label class="form-check-label">Up Regulated</label>
										</div>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Direction3" value="Down" <?php if(isset($_GET['Direction3']) && $_GET['Direction3'] == 'Down') echo "checked"; ?>>
										  <label class="form-check-label">Down Regulated</label>
										</div>
									</div>
								</div>

							</div>

		                    <div class="row my-3 pl-3">

								<div class="form-inline">
									<label class="form-check-label font-weight-bold">GO Tree: </label>
								</div>

								<div class="form-inline my-2">
								  <select class="custom-select" name="GO_Tree" id="GO_Tree">
									<?php
										$sql = "SELECT DISTINCT `GO_Tree` FROM `tbl_comparison_go_enrichment_10_2`";
										$options = $BXAF_MODULE_CONN->get_col($sql);
										$default = 'biological process';
										if($_GET['GO_Tree'] != '') $default = $_GET['GO_Tree'];
										foreach($options as $opt) echo "<option value='$opt' " . ($default == $opt ? 'selected' : '') . ">$opt</option>";
									?>
								  </select>

								</div>

								<div class="form-inline pl-5">
									<label class="form-check-label">P-Value Cutoff: </label>
								</div>

								<div class="form-inline px-1">
									<div class="form-check form-check-inline px-2">
									  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-10" <?php if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] == '-10') echo "checked"; ?>>
									  <label class="form-check-label">10<sup>-10</sup></label>
									</div>
									<div class="form-check form-check-inline px-2">
									  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-6" <?php if(! isset($_GET['P_Value_Cutoff']) || $_GET['P_Value_Cutoff'] == '-6') echo "checked"; ?>>
									  <label class="form-check-label">10<sup>-6</sup></label>
									</div>
									<div class="form-check form-check-inline px-2">
									  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-2" <?php if(isset($_GET['P_Value_Cutoff']) && $_GET['P_Value_Cutoff'] == '-2') echo "checked"; ?>>
									  <label class="form-check-label">0.01</label>
									</div>
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


						$sql = "SELECT `ComparisonIndex`, `ComparisonID`, `ProjectIndex` FROM `App_User_Data_Comparisons` ORDER BY `ComparisonIndex`";
						$internal_comparison_id_names = $BXAF_MODULE_CONN->get_assoc('ComparisonIndex', $sql);

						$accessible_project_indexes = internal_data_get_accessible_project();

						// Internal Comparisons
						foreach ($internal_comparison_id_names as $key => $values) {

							$project_index = $values['ProjectIndex'];
							if (! is_array($accessible_project_indexes) || ! array_key_exists($project_index, $accessible_project_indexes) ){
								continue;
							}
							$value = $values['ComparisonID'];
							echo '
							<tr>
								<td>' . $key . '</td>
								<td>' . $value . '</td>
								<td><a href="javascript:void(0);" class="btn_select_search_pathway" content="' . $key . '" displayed_name="' . $value . '"><i class="fas fa-angle-double-right"></i> Select</a></td>
							</tr>';
						}

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


<script>

    $(document).ready(function() {

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


        // File Upload
        var options = {
            url: 'comparison_exe.php?action=show_venn_diagram&type=go_results',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {
				if( $('#comparison1').val() == '' || $('#comparison2').val() == ''){
					bootbox.alert("Please select at least two comparisons. ");
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