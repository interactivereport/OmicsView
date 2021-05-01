<?php
include_once('config.php');




// Add Comparison Form

if (isset($_GET['action']) && $_GET['action'] == 'add_comparison') {

	if (isset($_POST['default_value']) && trim($_POST['default_value']) != '') {
		$DEFAULT_VALUE = trim($_POST['default_value']);
	} else {
		$DEFAULT_VALUE = '';
	}
	$LENGTH = intval($_POST['length']);
	
	$has_in_house_comparison_data = has_in_house_comparison_data();

	echo '
	<div class="div_comparison_section">
		<div class="row">
			<div class="col-md-2 text-md-right gray p-t-sm">
				Comparison ID: <br />
        <a href="javascript:void(0);" onclick="$(this).parent().parent().parent().remove();">
          <i class="fa fa-times"></i> Remove
        </a>
			</div>
			<div class="col-md-4">

				<!--<input name="comparison_id[]" class="form-control" style="max-width:30em;" required>-->


				<div class="input-group" xstyle="max-width:30em;">
					<input type="text" name="comparison_id[]" class="form-control input_comparison_id" placeholder="Comparison ID" value="' . $DEFAULT_VALUE . '" required>';
					
					if ($has_in_house_comparison_data){
					echo '

					<span class="input-group-btn">
						<button
							class="btn_search_comparison btn btn-primary"
							inhouse="true"
							type="button"
							index="' . $LENGTH . '">
							<i class="fa fa-cog"></i> Inhouse
						</button>
					</span>';
					}
					
					
					echo '


					<span class="input-group-btn">
						<button
              class="btn_search_comparison btn btn-link"
							inhouse="false"
              type="button"
              index="' . $LENGTH . '">
              <i class="fa fa-search"></i> Search
            </button>
					</span>

				</div>
				<span class="gray">Please enter a comparison ID, e.g., GSE44720.GPL10558.test16</span>

			</div>
		</div>


		<div class="row mt-1">
			<div class="col-md-2 text-md-right gray p-t-sm">
				Data Column:
			</div>
			<div class="col-md-10">
				<select name="data_column_1[]" class="form-control select_column" style="max-width:30em;">
					<option value="logFC">Log2 Fold Change (Log2FC)</option>
					<option value="pvalue">p-value</option>
					<option value="FDR">FDR (Adjusted p-value)</option>
				</select>
			</div>
		</div>


		<div class="row mt-1">
			<div class="col-md-2 text-md-right gray p-t-sm">
				Visualization:
			</div>
			<div class="col-md-10">
				<select name="visualization_1[]" class="form-control select_visualization" style="max-width:30em;">
					<option value="0">Gradient Blue-White-Red (-1,0,1)</option>
					<option value="1">Gradient Blue-White-Red (-2,0,2)</option>
					<option value="2">Gradient Blue-White-Red (-3,0,3)</option>
					<option value="custom">&#9733; Customize Highlight Color</option>
				</select>

				<div class="alert alert-info custom_parameter_div" style="max-width:30em; margin-top:1em; display:none;">
					<div class="row">
						<div class="col-md-4 text-right p-t-sm">
							<strong>Cutoff Value: </strong>
						</div>
						<div class="col-md-8">
							<input name="custom_visualization_1_cutoff[]" class="form-control custom_parameter_cutoff" placeholder="Left, Middle, Right">
						</div>
					</div>
					<div class="row mt-1">
						<div class="col-md-4 text-right p-t-sm">
							<strong>Color Style: </strong>
						</div>
						<div class="col-md-8">
							<select name="custom_visualization_1_select_color_style[]" class="form-control custom_parameter_select_color_style">
								<option value="0">Blue - White - Red</option>
								<option value="1">Green - White - Red</option>
								<option value="2">Yellow - White - Blue</option>
								<option value="3">Yellow - Orange - Red</option>
							</select>
						</div>
					</div>
				</div>

			</div>
		</div>


		<div class="row mt-1" style="display:none;">
			<div class="col-md-2 text-md-right gray p-t-sm">
				Data Column 2:
			</div>
			<div class="col-md-10">
				<select name="data_column_2[]" class="form-control select_column" style="max-width:30em;">
					<option value="">Select a field</option>
					<option value="log2FC">Log2 Fold Change (Log2FC)</option>
					<option value="pvalue">p-value</option>
					<option value="FDR">FDR (Adjusted p-value)</option>
				</select>
			</div>
		</div>


		<div class="row mt-1" style="display:none;">
			<div class="col-md-2 text-md-right gray p-t-sm">
				Visualization 2:
			</div>
			<div class="col-md-10">
				<select name="visualization_2[]" class="form-control select_visualization" style="max-width:30em;">
					<option value="0">Gradient Blue-White-Red (-1,0,1)</option>
					<option value="1">Gradient Blue-White-Red (-2,0,2)</option>
					<option value="2">Gradient Blue-White-Red (-3,0,3)</option>
					<option value="custom">&#9733; Customize Highlight Color</option>
				</select>

				<div class="alert alert-info custom_parameter_div" style="max-width:30em; margin-top:1em; display:none;">
					<div class="row">
						<div class="col-md-4 text-right p-t-sm">
							<strong>Cutoff Value: </strong>
						</div>
						<div class="col-md-8">
							<input name="custom_visualization_2_cutoff[]" class="form-control custom_parameter_cutoff" placeholder="Left, Middle, Right">
						</div>
					</div>
					<div class="row mt-1">
						<div class="col-md-4 text-right p-t-sm">
							<strong>Color Style: </strong>
						</div>
						<div class="col-md-8">
							<select name="custom_visualization_2_select_color_style[]" class="form-control custom_parameter_select_color_style">
								<option value="0">Blue - White - Red</option>
								<option value="1">Green - White - Red</option>
								<option value="2">Yellow - White - Blue</option>
								<option value="3">Yellow - Orange - Red</option>
							</select>
						</div>
					</div>
				</div>

			</div>
		</div>



		<div class="row mt-1">
			<div class="col-md-2">&nbsp;</div>
			<div class="col-md-10">
				<a href="javascript:void(0);" class="btn_second_column font-sanspro-300">
					<i class="fa fa-angle-double-right"></i>
					Enable Second Visualization Column
				</a>
			</div>
		</div>
		<hr />
	</div>';
	exit();
}







// Generate PVJS Chart

if (isset($_GET['action']) && $_GET['action'] == 'pvjs_generate_chart') {
	include('exe_pvjs_generate_chart.php');
	exit();
}







// Load Comparison Info

if (isset($_GET['action']) && $_GET['action'] == 'popup_load_comparison_info') {

	$gene_name = $_POST['gene_name'];

	$file_dir = $BXAF_CONFIG['USER_FILES_PVJS'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/comparison_info.txt';
	if (!file_exists($file_dir)) {
		echo 'Error: No data file.';
		exit();
	}
	$content = unserialize(file_get_contents($file_dir));

  // Additional space for reactome pathway
  if (!isset($content[$gene_name])) {
    $result_row = $content[$gene_name . ' '];
  } else {
    $result_row = $content[$gene_name];
  }

	echo '
	<h5><i>Data Table</i></h5>
	<table class="table table-bordered" style="font-size:11px;">
		<tr>
			<th>Comparison</th>
			<th>Log2FC</th>
			<th>P-Value</th>
			<th>FDR</th>
		</tr>';

		foreach ($result_row as $value) {

			$pos_dot1 = strpos($value['name'], '.');
			$name = substr($value['name'], 0, $pos_dot1) . '_' . substr($value['name'], $pos_dot1 + 1);
			$name = str_replace('.', '<br />', $name);

			echo '
			<tr>
				<td>' . $name . '</td>
				<td>' . $value['logFC'] . '</td>
				<td>' . $value['p-value'] . '</td>
				<td>' . $value['FDR'] . '</td>
			</tr>';
		}
	echo '
	</table>';


	exit();
}






// View SVG in New Window

if (isset($_GET['action']) && $_GET['action'] == 'view_svg_new_window') {


	exit();
}









?>
