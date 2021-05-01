<?php
include_once(__DIR__ . "/config.php");



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

</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_content" class="row no-gutters h-100">

        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

			<div id="bxaf_page_right_content" class="w-100 p-2">


				<h1>
					Search Similar Comparisons Based on GO Enrichment Results

				</h1>
				<hr class="w-100 my-1" />

				<div class="my-5 w-100">

	                <form class="" id="form_main" method="post">

						<div class="row m-3">

							<div class="col-md-3 px-3">
								<a href="Javascript: void(0);" class="btn_select_pathway_show_modal" target_field="comparison1">
			                      <i class="fas fa-angle-double-right"></i> Select Target Comparison
			                    </a>
								<input type="text" class="form-control" id="comparison1" name="comparison1" value="" id="comparison1">
							</div>

							<div class="col-md-4 px-3">
			                    GO Tree:
								<select class="custom-select" name="GO_Tree" id="GO_Tree">
									<?php
										$sql = "SELECT DISTINCT `GO_Tree` FROM `tbl_comparison_go_enrichment_10_2`";
										$options = $BXAF_MODULE_CONN->get_col($sql);
										$default = 'biological process';
										foreach($options as $opt) echo "<option value='$opt' " . ($default == $opt ? 'selected' : '') . ">$opt</option>";
									?>
  							  </select>
							</div>

							<div class="col-md-5 px-3">
								<div class="form-group my-0">
				                    Target Comparison Regulation:
								</div>

								<div class="form-group form-check form-check-inline my-1">
								  <input class="form-check-input mx-2" type="radio" name="Direction" value="Up" checked>
								  <label class="form-check-label">Up Regulated</label>

								  <input class="form-check-input mx-2" type="radio" name="Direction" value="Down">
								  <label class="form-check-label">Down Regulated</label>
								</div>
							</div>

						</div>


						<div class="row m-3">

							<div class="col-md-3 px-3">
								<div class="form-group my-0">
				                    P-Value Cutoff:
								</div>
								<div class="form-inline mx-2">
									<div class="form-check form-check-inline mr-2">
									  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-10">
									  <label class="form-check-label">10<sup>-10</sup></label>
									</div>
									<div class="form-check form-check-inline mx-2">
									  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-6" checked>
									  <label class="form-check-label">10<sup>-6</sup></label>
									</div>
									<div class="form-check form-check-inline mx-2">
									  <input class="form-check-input" type="radio" name="P_Value_Cutoff" value="-2">
									  <label class="form-check-label">0.01</label>
									</div>
								</div>
							</div>

							<div class="col-md-4 px-3">
								<div class="form-group my-0">
				                    Minimum Overlap Percentage (%):
								</div>
								<div class="form-inline mx-2">
									<input class="form-control" type="input" name="Minimum_Overlaps" value="20" style="width: 10rem;">
								</div>

							</div>

							<div class="col-md-5 px-3">
								<div class="form-group my-0">
				                    Found Comparison Regulation:
								</div>

								<div class="form-group form-check form-check-inline my-1">
								  <input class="form-check-input mx-2" type="radio" name="Found_Direction" value="1" checked>
								  <label class="form-check-label">Match</label>

								  <input class="form-check-input mx-2" type="radio" name="Found_Direction" value="-1">
								  <label class="form-check-label">Anit-Match</label>

  								  <input class="form-check-input mx-2" type="radio" name="Found_Direction" value="">
  								  <label class="form-check-label">Both</label>

								</div>
							</div>

						</div>

	                    <div class="row my-3 mx-5">
	                        <input class="btn btn-primary" type="submit" value="Search">
	                        <input class="btn btn-default mx-1" type="reset" value="Reset">

							<div class="form-check form-check-inline mx-2">
							  <input class="form-check-input" type="checkbox" name="Show_Terms" value="1">
							  <label class="form-check-label">Show Overlapped Terms</label>
							</div>

							<label id="form_upload_file_busy" class="mx-2 hidden text-danger"><i class="fas fa-spinner fa-spin"></i> Submitting ... </label>

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


						$sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `Comparisons` ";
						$comparison_id_names = $BXAF_MODULE_CONN->get_assoc('ComparisonIndex', $sql);

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


		$(document).on('click', '.btn_save_selected_records', function() {

			var selected_records = [];
			$('.checkbox_check_individual').each(function(i, e) {
				if ($(e).is(':checked')){
					selected_records.push( $(e).val() );
				}
			});

			if(selected_records.length <= 0){
				bootbox.alert("Please select some comparisons first.");
			}
			else {
				$.ajax({
					type: 'POST',
					url: 'comparison_exe.php?action=save_to_cache',
					data: {
						"cagetory": 'Comparison',
						"records": JSON.stringify(selected_records)
					},
					success: function(response) {
						if(response != ''){
							window.location = "../../gene_expressions/app_list_new.php?Category=Comparison&key=" + response;
						}
					}

				});
			}
        });



		$(document).on('click', '.btn_save_selected_records_plus_target', function() {

			var selected_records = [];
			selected_records.push( $('#comparison1').val() );
			$('.checkbox_check_individual').each(function(i, e) {
				if ($(e).is(':checked')){
					selected_records.push( $(e).val() );
				}
			});

			$.ajax({
				type: 'POST',
				url: 'comparison_exe.php?action=save_to_cache',
				data: {
					"cagetory": 'Comparison',
					"records": JSON.stringify(selected_records)
				},
				success: function(response) {
					if(response != ''){
						window.location = "../../plot/heatmap/index.php?key=" + response;
					}
				}

			});

        });



        var options = {
            url: 'comparison_exe.php?action=search',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {
				if( $('#comparison1').val() == ''){
					bootbox.alert("Please select a comparison first. ");
					return false;
				}

                $('#form_upload_file_busy').removeClass('hidden');

                return true;
            },
            success: function(response){
                $('#form_upload_file_busy').addClass('hidden');
                $('#div_debug').html(response);

				$('#table_search_results').DataTable({ order: [[ 3, 'desc' ]], "columnDefs": [ { "targets": 0, "orderable": false } ] });

                return true;
            }
        };
        $('#form_main').ajaxForm(options);

    });

</script>


</body>
</html>