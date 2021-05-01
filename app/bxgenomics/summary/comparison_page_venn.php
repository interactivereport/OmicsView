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
					Compare Comparisons Based on PAGE Results (Venn Diagrams)

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

									<div class="form-inline my-2">
										<label class="form-check-label font-weight-bold">Z-Score: </label>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Z_Score1" value="pos" <?php if(! isset($_GET['Z_Score1']) || $_GET['Z_Score1'] == 'pos') echo "checked"; ?>>
										  <label class="form-check-label">Z > 0</label>
										</div>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Z_Score1" value="neg" <?php if(isset($_GET['Z_Score1']) && $_GET['Z_Score1'] == 'neg') echo "checked"; ?>>
										  <label class="form-check-label">Z <= 0</label>
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
										<label class="form-check-label font-weight-bold">Z-Score: </label>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Z_Score2" value="pos" <?php if(! isset($_GET['Z_Score2']) || $_GET['Z_Score2'] == 'pos') echo "checked"; ?>>
										  <label class="form-check-label">Z > 0</label>
										</div>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Z_Score2" value="neg" <?php if(isset($_GET['Z_Score2']) && $_GET['Z_Score2'] == 'neg') echo "checked"; ?>>
										  <label class="form-check-label">Z <= 0</label>
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
										<label class="form-check-label font-weight-bold">Z-Score: </label>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Z_Score3" value="pos" <?php if(! isset($_GET['Z_Score3']) || $_GET['Z_Score3'] == 'pos') echo "checked"; ?>>
										  <label class="form-check-label">Z > 0</label>
										</div>
										<div class="form-check form-check-inline mx-2">
										  <input class="form-check-input" type="radio" name="Z_Score3" value="neg" <?php if(isset($_GET['Z_Score3']) && $_GET['Z_Score3'] == 'neg') echo "checked"; ?>>
										  <label class="form-check-label">Z <= 0</label>
										</div>
									</div>

								</div>

							</div>


		                    <div class="row my-5 pl-3">
		                        <input class="btn btn-primary" type="submit" value="Submit">
		                        <input class="btn btn-default mx-2" type="reset" value="Reset">


								<div class="form-inline pl-5">
									<label class="form-check-label font-weight-bold">FDR Cutoff of PAGE Results: </label>
								</div>

								<div class="form-inline">
									<div class="form-check form-check-inline mx-2">
									  <input class="form-check-input" type="radio" name="FDR" value="025" <?php if(! isset($_GET['FDR']) || $_GET['FDR'] == '025') echo "checked"; ?>>
									  <label class="form-check-label">0.25</label>
									</div>
									<div class="form-check form-check-inline mx-2">
									  <input class="form-check-input" type="radio" name="FDR" value="005" <?php if(isset($_GET['FDR']) && $_GET['FDR'] == '005') echo "checked"; ?>>
									  <label class="form-check-label">0.05</label>
									</div>
									<div class="form-check form-check-inline mx-2">
									  <input class="form-check-input" type="radio" name="FDR" value="001" <?php if(isset($_GET['FDR']) && $_GET['FDR'] == '001') echo "checked"; ?>>
									  <label class="form-check-label">0.01</label>
									</div>
								</div>

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
            url: 'comparison_exe.php?action=show_venn_diagram&type=page_results',
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