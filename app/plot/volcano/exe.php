<?php
include_once('config.php');


// Generate Volcano Chart

if (isset($_GET['action']) && $_GET['action'] == 'volcano_generate_chart') {
	include('exe_volvano_generate_chart.php');
	exit();
}



// Generate Table
if (isset($_GET['action']) && $_GET['action'] == 'volcano_generate_table') {

	$LENGTH = intval($_POST['current_length']);

	// 1. Gene Type is Customize
	if ($_POST['gene_type'] == 'customize') {
		$GENE_LIST = explode("\n", $_POST['gene_names']);
	}


	// 2. Gene Type is Auto


		$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/up_0.csv","r") or die("Unable to open file!");
		$first_row = fgetcsv($file);
		fclose($file);


		echo '
		<ul class="nav nav-tabs" role="tablist">';

		for ($i = 0; $i < $LENGTH; $i++) {

			// Customized Genes
			if ($_POST['gene_type'] == 'customize') {
				echo '
				<li class="nav-item">
					<a class="nav-link';
				echo ($i == 0) ? ' active' : '';
				echo
					'" href="#table_' . $i . '_1" role="tab" data-toggle="tab">Selected Genes (Chart ' . intval($i + 1) . ')</a>
				</li>';
			}

			// Auto Genes
			else {
				echo '
				<li class="nav-item">
					<a class="nav-link';
				echo ($i == 0) ? ' active' : '';
				echo
					'" href="#table_' . $i . '_1" role="tab" data-toggle="tab">Upregulated Genes (Chart ' . intval($i + 1) . ')</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#table_' . $i . '_2" role="tab" data-toggle="tab">Downregulated Genes (Chart ' . intval($i + 1) . ')</a>
				</li>';
			}

		}

		echo '
		</ul>';


		echo '

		<!-- Tab panes -->
		<div class="tab-content">';



		for ($i = 0; $i < $LENGTH; $i++) {




			// Customized Genes
			if ($_POST['gene_type'] == 'customize') {
				echo '
				<div role="tabpanel" class="tab-pane fade p-t-2';

				echo ($i == 0) ? ' in active' : '';

				echo '" id="table_' . $i . '_1">
					<h5>Upregulated Genes Table</h5>
					<table class="table tableFilter table-hover table-bordered" id="tableFilter_' . $i . '_1"><thead><tr class="table-info">';

					$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/selected_" . $i . ".csv","r") or die("Unable to open file!");
					$row_head = fgetcsv($file);
					foreach($row_head as $head) {
						echo '<th>' . $head . '</th>';
					}
					fclose($file);
					//echo '<th>Gene ID</th><th>Gene Name</th><th>P-Value</th><th>logFC</th>';
					echo '</tr></thead><tbody>';

					$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/selected_" . $i . ".csv","r") or die("Unable to open file!");
					$index = 0;
					while(!feof($file)) {
						$data_temp = fgetcsv($file);
						//print_r($data_temp);
						if ($index > 0 && trim($data_temp[0]) != ''){
							echo '
							<tr>
								<td>' . $data_temp[0] . '</td>
								<td>' . $data_temp[1] . '</td>
								<td>' . pow(10, $data_temp[2] * (-1)) . '</td>
								<td>' . $data_temp[3] . '</td>
							</tr>';
						}
						$index++;
					}
					fclose($file);

					echo '</tbody></table>';


				echo '</div>';
			}


			// Auto Genes
			else {
				// First Table
				echo '
				<div role="tabpanel" class="tab-pane fade p-t-2';

				echo ($i == 0) ? ' in active' : '';

				echo '" id="table_' . $i . '_1">
					<h5>Upregulated Genes Table</h5>
					<table class="table tableFilter table-hover table-bordered" id="tableFilter_' . $i . '_1"><thead><tr class="table-info">';
					$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/up_" . $i . ".csv","r") or die("Unable to open file!");
					$row_head = fgetcsv($file);
					foreach($row_head as $head) {
						echo '<th>' . $head . '</th>';
					}
					fclose($file);
					//echo '<th>Gene ID</th><th>Gene Name</th><th>P-Value</th><th>logFC</th>';
					echo '</tr></thead><tbody>';

					$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/up_" . $i . ".csv","r") or die("Unable to open file!");
					$index = 0;
					while(!feof($file)) {
						$data_temp = fgetcsv($file);
						//print_r($data_temp);
						if ($index > 0 && trim($data_temp[0]) != ''){
							echo '
							<tr>
								<td>' . $data_temp[0] . '</td>
								<td>' . $data_temp[1] . '</td>
								<td>' . pow(10, $data_temp[2] * (-1)) . '</td>
								<td>' . $data_temp[3] . '</td>
							</tr>';
						}
						$index++;
					}
					fclose($file);

					echo '</tbody></table>';


				echo '
					</div>';
				echo '
					<div role="tabpanel" class="tab-pane fade p-t-2" id="table_' . $i . '_2">';

					// Second Table
					echo '
					<h5>Downregulated Genes Table</h5>
					<table class="table tableFilter table-hover table-bordered" id="tableFilter_' . $i . '_2"><thead><tr class="table-info">';
					$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/down_" . $i . ".csv","r") or die("Unable to open file!");
					$row_head = fgetcsv($file);
					foreach($row_head as $head) {
						echo '<th>' . $head . '</th>';
					}
					fclose($file);

					echo '</tr></thead><tbody>';

					$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/down_" . $i . ".csv","r") or die("Unable to open file!");
					$index = 0;
					while(!feof($file)) {
						$data_temp = fgetcsv($file);
						//print_r($data_temp);
						if ($index > 0 && trim($data_temp[0]) != ''){
							echo '
							<tr>
								<td>' . $data_temp[0] . '</td>
								<td>' . $data_temp[1] . '</td>
								<td>' . pow(10, $data_temp[2] * (-1)) . '</td>
								<td>' . $data_temp[3] . '</td>
							</tr>';
						}
						$index++;
					}
					fclose($file);

					echo '</tbody></table>';

				echo '</div>';

			}





			echo '
			<script>
				$(document).ready(function(){
					var tf_' . $i . '_1 = new TableFilter(document.querySelector(\'#tableFilter_' . $i . '_1\'), {
						base_path: \'../library/TableFilter/dist/tablefilter/\',
						col_widths: [\'50px\', \'50px\', \'50px\', \'50px\'],
						alternate_rows: true,
						paging: true,
						results_per_page: [\'Records: \', [10, 25, 50, 100]],
						rows_counter: true,
						rows_counter_text: \'Genes: \',
						btn_reset: true,
						btn_reset_text: "Clear",
						loader: true,
						no_results_message: true,
						col_types: [
							\'string\', \'string\',
							\'number\', \'number\'
						],
						extensions: [{ name: \'sort\' }]
					});
					var tf_' . $i . '_2 = new TableFilter(document.querySelector(\'#tableFilter_' . $i . '_2\'), {
						base_path: \'../library/TableFilter/dist/tablefilter/\',
						col_widths: [\'50px\', \'50px\', \'50px\', \'50px\'],
						alternate_rows: true,
						paging: true,
						results_per_page: [\'Records: \', [10, 25, 50, 100]],
						rows_counter: true,
						rows_counter_text: \'Genes: \',
						btn_reset: true,
						btn_reset_text: "Clear",
						loader: true,
						no_results_message: true,
						col_types: [
							\'string\', \'string\',
							\'number\', \'number\'
						],
						extensions: [{ name: \'sort\' }]
					});
					tf_' . $i . '_1.init();
					tf_' . $i . '_2.init();
					//$(".flt").addClass("form-control");
					//$(".fltrow").addClass("bg-primary");
					$("caption").css("caption-side", "top");
					$(\'.highcharts-credits\').attr("hidden", "");
				});
			</script>';


		}

		echo '</div>';






	exit();
}






// Add New Chart
if (isset($_GET['action']) && $_GET['action'] == 'add_new_chart') {

	$LENGTH = intval($_POST['current_length']);

	echo '
	<div class="chart_setting_single_container w-100">

		<div class="row">
			<div class="col-md-2 text-md-right gray">
				Comparison ID<br />
        <a href="javascript:void(0);" onclick="$(this).parent().parent().parent().remove();" class="red">
          <i class="fa fa-angle-double-right"></i> Remove Comp
        </a>
			</div>
			<div class="col-md-10">
				<!--<input name="comparison_id[]" class="form-control input_file" style="width:20em;" required>-->


				<div class="input-group" style="max-width:30em;">
					<input type="text" name="comparison_id[]" class="form-control input_file input_comparison_id" placeholder="Comparison ID" required>

          <span class="input-group-btn">
            <button
              class="btn_search_comparison btn btn-primary green"
              inhouse="true"
              type="button"
              index="' . $LENGTH . '">
              <i class="fa fa-cog"></i> Inhouse
            </button>
          </span>
          <span class="input-group-btn">
            <button
              class="btn_search_comparison btn btn-primary green"
              inhouse="false"
              type="button"
              index="' . $LENGTH . '">
              <i class="fa fa-search"></i> Comps
            </button>
          </span>



				</div>


				<span class="gray">Please enter the comparison id, e.g., GSE44720.GPL10558.test16</span>
			</div>
		</div>

		<div class="row mt-1">
			<div class="col-md-2 text-md-right gray">
				Y-axis Statistics:
			</div>
			<div class="col-md-10">
				<label>
					<input type="radio" name="volcano_y_statistics_' . intval($LENGTH). '" value="P-value">
					P-value
				</label>
				&nbsp;&nbsp;
				<label>
					<input type="radio" name="volcano_y_statistics_' . intval($LENGTH). '" value="FDR" checked>
					FDR
				</label>
			</div>
		</div>

		<div class="row m-t-sm">
			<div class="col-md-2 text-md-right p-t-sm gray">
				Chart Name
			</div>
			<div class="col-md-10">
				<input class="form-control" name="chart_name[]" value="Volcano Chart" style="width:20em;" required>
			</div>
		</div>

		<div class="row m-t-sm">
			<div class="col-md-2 text-md-right p-t-sm gray">
				Fold Change Cutoff:
			</div>
			<div class="col-md-10">
				<select class="form-control volcano_fc_cutoff custom-select float-left m-r-1" name="volcano_fc_cutoff[]" style="width:8.6em;">
					<option value="2">2</option>
					<option value="4">4</option>
					<option value="8">8</option>
					<option value="enter_value">Enter Value</option>
				</select>
				<input class="form-control float-left hidden" name="volcano_fc_custom_cutoff[]" placeholder="Custom Cutoff" style="width:10.3em;">
			</div>
		</div>

		<div class="row m-t-sm">
			<div class="col-md-2 text-md-right p-t-sm gray">
				Statistic Cutoff:
			</div>
			<div class="col-md-10">
				<select class="form-control volcano_statistic_cutoff custom-select float-left m-r-1" name="volcano_statistic_cutoff[]" style="width:8.6em;">
					<option value="0.05">0.05</option>
					<option value="0.01">0.01</option>
					<option value="0.001">0.001</option>
					<option value="enter_value">Enter Value</option>
				</select>
				<input class="form-control float-left hidden" name="volcano_statistic_custom_cutoff[]" placeholder="Custom Cutoff" style="width:10.3em;">
			</div>
		</div>


		<hr />

	</div>';
	exit();
}

?>
