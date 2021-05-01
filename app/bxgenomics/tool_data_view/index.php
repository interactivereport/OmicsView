<?php
include_once("config.php");

$default_values = array(
	'View_Name'=>"SCV_" . time(),
	'Sample_List'=>'',
	'table_samples_length'=>'10',
	'table_projects_length'=>'10',
	'DataTables_Table_0_length'=>'10',
	'attributes_Sample'=>array(),
	'min_cells'=>'5',
	'nGene_low'=>'(-Inf)',
	'nGene_high'=>'Inf',
	'x_low'=>'0.1',
	'x_high'=>'8',
	'y_cutoff'=>'0.5',
	'N_PC'=>'12',
	'logfc_threshold'=>'0.25',
	'min_pct'=>'0.1',
	'cluser_res'=>'0.6',
	'Gene_List'=>'',
	'load_data'=>'var'
);

if(isset($_GET['project']) && $_GET['project'] != '' && file_exists($project_dir . $_GET['project'] . '/parameters.txt')){
	$default_values = unserialize( file_get_contents($project_dir . $_GET['project'] . '/parameters.txt') );
	$default_values['View_Name'] = "Re-Analysis of " . $default_values['View_Name'];

	$sample_names_custom = preg_split("/[\s,]+/", $default_values['Sample_List'], NULL, PREG_SPLIT_NO_EMPTY);
	$gene_names_custom = preg_split("/[\s,]+/", $default_values['Gene_List'], NULL, PREG_SPLIT_NO_EMPTY);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js"></script>

	<link  href="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css" rel="stylesheet">
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js"></script>

</head>
<body>

<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">



            <div class="container-fluid">

            	<h1 class="page-header">
            		Start New RNA-Seq Visualization
					<a class="ml-5" style="font-size: 1rem;" href="list.php"> <i class="fas fa-angle-double-right"></i> All Visualizations</a>
            	</h1>
            	<hr />


                <form class="w-100" id="form_main">

        			<div class="w-100">

        				<div class="w-100 my-3 form-inline">
    						<label class="mr-2 font-weight-bold" for="View_Name">Name:</label>
    						<input class="form-control w-50 text-danger" type="text" name="View_Name" value="<?php echo $default_values['View_Name']; ?>" id="View_Name" placeholder="Enter an alphanumeric name for your visualization, must be unique" >
        				</div>

						<?php include_once(__DIR__ . '/modal_sample.php'); ?>

						<div class="w-100 my-3">
							<?php
	        					$type = 'Sample';
								$list = $BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES_FIELDS'];
	        					sort($list);
								$checked = array("CellType", "DiseaseState", "Tissue", "Treatment");
								if(is_array($default_values['attributes_Sample']) && count($default_values['attributes_Sample']) > 0) $checked = $default_values['attributes_Sample'];
							?>

							<div class="w-100 my-3">
								<label class="font-weight-bold">Sample Attributes:</label>

								<span class="table-success mx-2 p-2">( <span id="span_number_attributes"><?php echo count($checked); ?></span> selected )</span>

								<a href="javascript:void(0);" onclick="if($('#div_attributes').hasClass('hidden')) $('#div_attributes').removeClass('hidden'); else $('#div_attributes').addClass('hidden'); "> <i class="fas fa-angle-double-right"></i> Show Attributes </a>

							</div>

	        				<?php
        						echo '<div id="div_attributes" class="w-100 hidden my-3">';

									$name_captions = array();
									foreach ($list as $colname) {
										$caption = str_replace("_", " ", str_replace('Clinical_Triplets_', '', $colname));
										$name_captions[$colname] = $caption;
									}
									asort($name_captions);
        							foreach ($name_captions as $colname=>$caption) {
        								echo '<div class="form-check form-check-inline">
        									<input class="form-check-input attributes_checkbox_all reset_chart" type="checkbox" category="' . $type . '" value="' . $colname . '" name="attributes_' . $type . '[]" ' . (in_array($colname, $checked) ? "checked " : "") . '>';
        									echo '<label class="form-check-label">' . $caption . '</label>';
        								echo '</div>';
        							}

        						echo '</div>';

	        				?>

        				</div>

						<hr />
						<h5 class="my-3">
							RNA-Seq Visualization Options
							<a style="font-size: 1rem;" href="javascript:void(0);" onclick="if($('#div_options').hasClass('hidden')) $('#div_options').removeClass('hidden'); else $('#div_options').addClass('hidden'); "> <i class="fas fa-angle-double-right"></i> Show Options </a>
						</h5>
						<hr />

						<div class="w-100 my-3 hidden" id="div_options">

							<label class="font-weight-bold my-3">Filter Genes and Cells:</label>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Select genes expressed in at least </label>
	    						<input class="form-control" type="text" name="min_cells" value="<?php echo $default_values['min_cells']; ?>" id="min_cells" placeholder="" >
								<label class="mx-2">cells.</label>
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Select cells expressing at least </label>
	    						<input class="form-control" type="text" name="nGene_low" value="<?php echo $default_values['nGene_low']; ?>" id="nGene_low" placeholder="" >
								<label class="mx-2">genes.</label>
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Select cells expressing at most </label>
	    						<input class="form-control" type="text" name="nGene_high" value="<?php echo $default_values['nGene_high']; ?>" id="nGene_high" placeholder="" >
								<label class="mx-2">genes.</label>
	        				</div>


							<label class="font-weight-bold my-3">Variable Genes:</label>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Mean log2 expression &gt; </label>
	    						<input class="form-control" type="text" name="x_low" value="<?php echo $default_values['x_low']; ?>" id="x_low" placeholder="" >
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Mean log2 expression &lt; </label>
	    						<input class="form-control" type="text" name="x_high" value="<?php echo $default_values['x_high']; ?>" id="x_high" placeholder="" >
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Log2(Variance/mean) &gt; </label>
	    						<input class="form-control" type="text" name="y_cutoff" value="<?php echo $default_values['y_cutoff']; ?>" id="y_cutoff" placeholder="" >
	        				</div>


							<label class="font-weight-bold my-3">Find Clusters and Makers:</label>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Number of principle components to use </label>
	    						<input class="form-control" type="text" name="N_PC" value="<?php echo $default_values['N_PC']; ?>" id="N_PC" placeholder="" >
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Marker is at least </label>
	    						<input class="form-control" type="text" name="logfc_threshold" value="<?php echo $default_values['logfc_threshold']; ?>" id="logfc_threshold" placeholder="" >
								<label class="mx-2"> fold (log-scale) higher in the cluster</label>
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Marker is expressed in at least </label>
	    						<input class="form-control" type="text" name="min_pct" value="<?php echo $default_values['min_pct']; ?>" id="min_pct" placeholder="" >
								<label class="mx-2"> faction of cells in either of the population</label>
	        				</div>
							<div class="w-100 my-1 px-5 form-inline">
	    						<label class="mx-2">Cluster resolution </label>
	    						<input class="form-control" type="text" name="cluser_res" value="<?php echo $default_values['cluser_res']; ?>" id="cluser_res" placeholder="" >
	        				</div>


							<label class="my-3"><span class="font-weight-bold">Show Gene Data</span> (Past a gene list, or leave blank for auto):</label>
							<div class="w-100 my-1 px-5">
								<?php include_once(__DIR__ . '/modal_gene.php'); ?>
							</div>

							<label class="font-weight-bold my-3">Load Data:</label>
							<div class="w-100 my-1 px-5 form-inline">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" value="pca" name="load_data" <?php echo $default_values['load_data'] == 'pca' ? 'checked' : ''; ?>>
									<label class="form-check-label">PCA</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" value="var" name="load_data" <?php echo $default_values['load_data'] == 'var' ? 'checked' : ''; ?>>
									<label class="form-check-label">Variable Genes</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" value="all" name="load_data" <?php echo $default_values['load_data'] == 'all' ? 'checked' : ''; ?>>
									<label class="form-check-label">All Genes</label>
								</div>
	        				</div>

						</div>


        			</div>

        			<button type="submit" class="btn btn-primary mt-3" id="btn_submit">
        				<i class="fas fa-chart-pie"></i> Plot
        			</button>

					<div class="text-muted">This program will create visualization records, process the data, prepare the config file, and load the TensorBoard in an iframe.</div>

        		</form>

            </div>

            <div class="my-3 p-3" id="div_results"></div>
    		<div class="my-3" id="div_debug"></div>

		</div>
		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>



<div class="modal" id="modal_data_filter">
	<div class="modal-dialog modal-lg" role="document">


			<div class="modal-content w-100">

			  <div class="modal-header">
				<h3 class="modal-title" id="modal_data_filter_title">Data Filters</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>

			  <div class="modal-body w-100" id="modal_data_filter_body"></div>

			  <div class="modal-footer">
				<input type="hidden" value="" id="modal_data_filter_field">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="modal_data_filter_save">Save</button>
			  </div>

			</div>


	</div>
</div>



<script>

$(document).ready(function() {

	$(document).on('change', '.reset_chart', function() {
        $('#div_data_filter').html('');
		$('#div_results').html('');
		$('#div_debug').html('');
	});


	$(document).on('change', '.attributes_checkbox_all', function() {
		// Update number selected
		var number = 0;
		$('.attributes_checkbox_all').each(function(i, e) {
			if ($(e).is(':checked')) number++;
		});
		$('#span_number_attributes').html(number);

		$('#div_data_filter').html('');
		$('#div_results').html('');
		$('#div_debug').html('');

	});


	var options = {
		url: 'exe.php?action=show_data_view',
 		type: 'post',
    	beforeSubmit: function(formData, jqForm, options) {

    		$('#div_results').html('');
			$('#btn_submit').children(':first').removeClass('fa-chart-pie').addClass('fa-spin fa-spinner');

			return true;
		},
    	success: function(response){

			$('#btn_submit').children(':first').addClass('fa-chart-pie').removeClass('fa-spin fa-spinner');
			$('#div_results').html(response);

			var interval5 = setInterval(function(){
				var processingTime = parseInt($('#processing_time5').attr('value')) + 1;
				$('#processing_time5').attr('value', processingTime);
				$('#processing_time5').html('Processing in progress ... ' + processingTime + ' sec');
			}, 1000);

			var interval6 = setInterval(function(){
				$.ajax({
					type: 'GET',
					url: 'exe.php?action=get_data_status&project=' + $('#processing_time5').attr('project') + '&process_id=' + $('#processing_time5').attr('process_id'),
					success: function(responseText){
						if(responseText.indexOf("view_iframe.php") == 0){
							clearInterval(interval5);
							clearInterval(interval6);
							window.location = responseText;
						}
						else if(responseText != ''){
							clearInterval(interval5);
							clearInterval(interval6);
							$('#div_results').html(responseText);
						}
					}
				});
			}, 5000);

			return true;
		}
	};

	$('#form_main').ajaxForm(options);

});

</script>

</body>
</html>