<?php

	$sessionKey = $_POST['sessionKey'];
  	if ($sessionKey != ''){
		unset($_SESSION['App']['bubble'][$sessionKey]);
		$_SESSION['App']['bubble'][$sessionKey]['POST'] = $_POST;
	}
	

	//echo '<pre>'; print_r($_POST); echo '</pre>'; exit();

	$GENE_NAME = addslashes(trim($_POST['gene_name']));
	$Y_FIELD = $_POST['select_y_field'];
	$COLORING_FIELD = $_POST['select_coloring_field'];

	// Check
	if ($Y_FIELD == $COLORING_FIELD) {
		echo 'Error. The Y-axis field has to be different from the coloring field.';
		exit();
	}

	// Get GeneIndex
	$GENE_INDEX = search_gene_index($GENE_NAME);
	

	if (!isset($GENE_INDEX) || trim($GENE_INDEX) == '' || intval($GENE_INDEX) < 0) {
		echo 'Error. The system could not find the gene you entered. Please try again.';
		exit();
	}


	// Tabix
	$geneIndex = array($GENE_INDEX);
	
	internal_data_sanitize_user_input($_POST['data_source'], $_POST['data_source_private_project_indexes']);
	
	
	unset($data_comparison);
	if ($_POST['data_source']['public'] != ''){
		$data_comparison = tabix_search_records_with_index($geneIndex, '', 'ComparisonData');
	}
	
	if ($_POST['data_source']['private'] != ''){
		foreach($_POST['data_source_private_project_indexes'] as $tempKey => $projectIndex){
			$data_comparison_private = tabix_search_records_with_index_internal_data($projectIndex, $geneIndex, '', 'ComparisonData');
			
			foreach($data_comparison_private as $tempKeyX => $tempValueX){
				$data_comparison[] = $tempValueX;
			}
		}
	}
	
	$allComparisonIndexes 	= array_unique(array_column($data_comparison, 'ComparisonIndex'));
	$ALL_COMPARISONS 		= search_comparisons_by_index($allComparisonIndexes, "`ComparisonIndex`, `{$Y_FIELD}`, `{$COLORING_FIELD}`", $_POST['data_source'], $_POST['data_source_private_project_indexes']);


	$Y_FIELD_LIST = array();
	$COLORING_FIELD_LIST = array();
	$Y_FIELD_NUMBER = array(); // Appear times
	$COLORING_FIELD_NUMBER = array();

	foreach ($data_comparison as $tempKey => $comparison) {
//		$sql = "SELECT `{$Y_FIELD}`, `{$COLORING_FIELD}` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $comparison['ComparisonIndex'];
//		$comparison_row = $DB -> get_row($sql);

		$comparison_row = $ALL_COMPARISONS[$comparison['ComparisonIndex']];

		//echo '<pre>'; print_r($comparison_row); echo '</pre>';

		if (trim($comparison['Log2FoldChange']) == ''
			|| trim($comparison['Log2FoldChange']) == '.'
			|| trim($comparison['Log2FoldChange']) == 'NA'
			|| trim($comparison['PValue']) == ''
			|| trim($comparison['PValue']) == '.'
			|| trim($comparison['PValue']) == 'NA'
			|| trim($comparison_row[$Y_FIELD]) == ''
			|| trim($comparison_row[$Y_FIELD]) == 'NA'
			|| trim($comparison_row[$COLORING_FIELD]) == ''
			|| trim($comparison_row[$COLORING_FIELD]) == 'NA') {
			continue;
		}

		if (!in_array($comparison_row[$Y_FIELD], array_keys($Y_FIELD_NUMBER))) {
			$Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] = 1;
		} else {
			$Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] += 1;
		}
		if (!in_array($comparison_row[$COLORING_FIELD], array_keys($COLORING_FIELD_NUMBER))) {
			$COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] = 1;
		} else {
			$COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] += 1;
		}

	}

	arsort($Y_FIELD_NUMBER);
	arsort($COLORING_FIELD_NUMBER);
	
	if ($BXAF_CONFIG['BUBBLE_PLOT_SELECT_TOP'] == 10){
		$selected_top_10 = 'checked';
	} elseif ($BXAF_CONFIG['BUBBLE_PLOT_SELECT_TOP'] == 20){
		$selected_top_20 = 'checked';
	} elseif ($BXAF_CONFIG['BUBBLE_PLOT_SELECT_TOP'] == 'All'){
		$selected_top_all = 'checked';
	} else {
		$selected_top_20 = 'checked';
	}
	
	


	echo '
	<input name="select_y_field" value="' . $Y_FIELD . '" type="hidden">
	<input name="select_coloring_field" value="' . $COLORING_FIELD . '" type="hidden">
	<input name="select_shape_field" value="' . $_POST['select_shape_field'] . '" type="hidden">
	<input name="select_subplot_field_enable" value="' . $_POST['select_subplot_field_enable'] . '" type="hidden">
	<input name="select_subplot_field" value="' . $_POST['select_subplot_field'] . '" type="hidden">
	<input name="gene_name" value="' . $GENE_NAME . '" type="hidden">
	<input name="sessionKey" value="' . $sessionKey . '" type="hidden">


	<div class="row mt-1">
		<div class="col-md-2 text-md-right gray">
			Marker Area
		</div>
		<div class="col-md-10">
			<label class="m-r-1">
				<input type="radio" name="area_setting" value="PValue">
				P-Value
			</label>
			<label class="m-r-1">
				<input type="radio" name="area_setting" value="AdjustedPValue" checked>
				Adjusted P-Value
			</label>
		</div>
	</div>


	<div class="row mt-1">
		<div class="col-md-2 text-md-right gray">
			Y-axis Setting
		</div>
		<div class="col-md-10">
			<strong>' . $Y_FIELD . '</strong><br />
			<label class="m-r-1">
				<input class="radio_y_setting" type="radio" name="y_setting" value="top_10" ' . $selected_top_10 . '>
				Show Top 10
			</label>
			<label class="m-r-1">
				<input class="radio_y_setting" type="radio" name="y_setting" value="top_20" ' . $selected_top_20 . '>
				Show Top 20
			</label>
			<label class="m-r-1">
				<input class="radio_y_setting" type="radio" name="y_setting" value="all" ' . $selected_top_all . '>
				Show All
			</label>
			<label class="m-r-1">
				<input class="radio_y_setting" type="radio" name="y_setting" value="customize">
				Customize
			</label>
			<br />
			<div class="alert alert-warning m-b-0" id="y_customize_div" style="display:none;">
			</div>

			<div class="modal fade bd-example-modal-lg" id="modal_y" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title" id="gridModalLabel">' . substr($Y_FIELD, 5) . '</h4>

							<a href="javascript:void(0);" class="btn_customize_sort" gene_id="' . $GENE_INDEX . '" category="y" type="category" field="' . $Y_FIELD . '">
								<i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
								Sort by Category
							</a> &nbsp;
							<a href="javascript:void(0);" class="btn_customize_sort" gene_id="' . $GENE_INDEX . '" category="y" type="occurence" field="' . $Y_FIELD . '">
								<i class="fa fa-sort-numeric-asc" aria-hidden="true"></i>
								Sort by Occurence
							</a>
						</div>

						<div class="modal-body" id="modal_y_content">
							<div style="height:70vh; overflow-y:scroll;">

								<label>
									<input type="checkbox" id="check_all_y">
									Check / Uncheck All
								</label>

								<table class="table table-bordered table-striped" style="font-size:14px;">
									<tr>
										<th>&nbsp;</th>
										<th>Name</th>
										<th>Occurence</th>
									</tr>';
									foreach ($Y_FIELD_NUMBER as $key => $value) {
										echo '
										<tr>
											<td><input type="checkbox" class="checkbox_customize_y" name="y_' . $key . '" value="' . $key . '"></td>
											<td>' . $key . '</td>
											<td>' . $value . '</td>
										</tr>';
									}
							echo '
								</table>';
						
						echo '
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="btn_save_customize_y">Save changes</button>
						</div>
					</div>
				</div>
			</div>



		</div>
	</div>



	<div class="row mt-1">
		<div class="col-md-2 text-md-right gray">
			Coloring Setting
		</div>
		<div class="col-md-10">
			<strong>' . $COLORING_FIELD . '</strong><br />
			<label class="m-r-1">
				<input class="radio_coloring_setting" type="radio" name="coloring_setting" value="top_10" ' . $selected_top_10 . '>
				Show Top 10
			</label>
			<label class="m-r-1">
				<input class="radio_coloring_setting" type="radio" name="coloring_setting" value="top_20" ' . $selected_top_20 . '>
				Show Top 20
			</label>
			<label class="m-r-1">
				<input class="radio_coloring_setting" type="radio" name="coloring_setting" value="all" ' . $selected_top_all . '>
				Show All
			</label>
			<label class="m-r-1">
				<input class="radio_coloring_setting" type="radio" name="coloring_setting" value="customize">
				Customize
			</label>
			<br />
			<div class="alert alert-warning m-b-0" id="color_customize_div" style="display:none;">
			</div>

			<div class="modal fade bd-example-modal-lg" id="modal_color" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title" id="gridModalLabel">' . substr($COLORING_FIELD, 5) . '</h4>

							<a href="javascript:void(0);" class="btn_customize_sort" gene_id="' . $GENE_INDEX . '" category="color" type="category" field="' . $COLORING_FIELD . '">
								<i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
								Sort by Category
							</a> &nbsp;
							<a href="javascript:void(0);" class="btn_customize_sort" gene_id="' . $GENE_INDEX . '" category="color" type="occurence" field="' . $COLORING_FIELD . '">
								<i class="fa fa-sort-numeric-asc" aria-hidden="true"></i>
								Sort by Occurence
							</a>
						</div>
						<div class="modal-body" id="modal_color_content">
							<div style="height:70vh; overflow-y:scroll;">

								<label>
									<input type="checkbox" id="check_all_color">
									Check / Uncheck All
								</label>

								<table class="table table-bordered table-striped" style="font-size:14px;">
									<tr>
										<th>&nbsp;</th>
										<th>Name</th>
										<th>Occurence</th>
									</tr>';
									foreach ($COLORING_FIELD_NUMBER as $key => $value) {
										echo '
										<tr>
											<td><input type="checkbox" class="checkbox_customize_color" name="color_' . $key . '" value="' . $key . '"></td>
											<td>' . $key . '</td>
											<td>' . $value . '</td>
										</tr>';
									}
							echo '
								</table>';


						echo '
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="btn_save_customize_color">Save changes</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="row mt-1">
		<div class="col-md-2 text-md-right gray"></div>
		<div class="col-md-10">
			<button type="submit" class="btn btn-primary" id="btn_submit_generate"><i class="fa fa-pie-chart"></i> Plot</button>
		</div>
	</div>
	
	<hr/>
	';

	echo "
	<script>
	$(document).ready(function() {

		$(document).on('change', '#check_all_y', function() {
			if ($('#check_all_y').is(':checked')) {
				$('.checkbox_customize_y').each(function(index, element) {
					$(element).prop('checked', true);
				});
			} else {
				$('.checkbox_customize_y').each(function(index, element) {
					$(element).prop('checked', false);
				});
			}
		});

		$(document).on('change', '#check_all_color', function() {
			if ($('#check_all_color').is(':checked')) {
				$('.checkbox_customize_color').each(function(index, element) {
					$(element).prop('checked', true);
				});
			} else {
				$('.checkbox_customize_color').each(function(index, element) {
					$(element).prop('checked', false);
				});
			}
		});



		$(document).on('change', '.radio_y_setting', function() {
			if ($(this).val() == 'customize') {
				$('#modal_y').modal('show');
			} else {
				$('#y_customize_div').hide();
			}
		});

		$(document).on('change', '.radio_coloring_setting', function() {
			if ($(this).val() == 'customize') {
				$('#modal_color').modal('show');
			} else {
				$('#color_customize_div').hide();
			}
		});

		$(document).on('click', '#btn_save_customize_y', function() {
			$('#modal_y').modal('hide');
			var y_selected = [];
			$('.checkbox_customize_y').each(function(index, element) {
				if ($(element).is(':checked')) {
					y_selected.push($(element).val());
				}
			});
			$('#y_customize_div').html('<strong>' + y_selected.length + ' options selected: </strong><br />' + y_selected);
			$('#y_customize_div').show();
		});

		$(document).on('click', '#btn_save_customize_color', function() {
			$('#modal_color').modal('hide');
			var color_selected = [];
			$('.checkbox_customize_color').each(function(index, element) {
				if ($(element).is(':checked')) {
					color_selected.push($(element).val());
				}
			});
			$('#color_customize_div').html('<strong>' + color_selected.length + ' options selected: </strong><br />' + color_selected);
			$('#color_customize_div').show();
		});



		$(document).on('click', '.btn_customize_sort', function() {
			var gene_id = $(this).attr('gene_id');
			var category = $(this).attr('category');
			var type = $(this).attr('type');
			var field = $(this).attr('field');
			$.ajax({
				type: 'POST',
				url: 'exe.php?action=customize_sort',
				data: {gene_id: gene_id, category: category, type: type, field: field},
				success: function(responseText){
					$('#modal_' + category + '_content').html(responseText);
				}
			});
		});



		var options_generate = {
			url: 'exe.php?action=bubble_generate_chart',
			type: 'post',
			beforeSubmit: function(formData, jqForm, options) {
		        // Loader
       			$('body').prepend('<div class=\"loader loader-default is-active\" data-text=\"Drawing...\" style=\"margin-left:0px; margin-top:0px;\"></div>');
				$('#btn_submit_generate').html('<i class=\"fa fa-spin fa-spinner\"></i> Plotting...');
				$('#btn_submit_generate').attr('disabled', '');
				return true;
			},
			success: function(responseText, statusText){
				$('#btn_submit_generate').html('<i class=\"fa fa-pie-chart\"></i> Plot');
				$('#btn_submit_generate').removeAttr('disabled');
				$('#chart_div').html(responseText);
        		//$('#first_form_div, #second_form_div').slideUp(200);
				return true;
			}
		};
		$('#form_bubble_plot_filter').ajaxForm(options_generate);

	});

	</script>";





	exit();


?>