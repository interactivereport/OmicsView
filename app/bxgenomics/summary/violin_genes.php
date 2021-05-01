<?php

include_once("config.php");


$BXAF_CONFIG['BXAF_PAGE_LEFT']   = '';
$BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']			= '';
$BXAF_CONFIG['BXAF_PAGE_CSS_LEFT']				= '';
$BXAF_CONFIG['BXAF_PAGE_CSS_LEFT_FIXED_WIDTH']	= '';
$BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']				= 'w-100';
$BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']		= 'w-100 p-3';



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

    <link href="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery-ui/jquery-ui.min.css.php" rel="stylesheet">

	<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js'></script>

	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js.php'></script>

    <link  href="../../gene_expressions/js/canvasxpress/canvasxpress-18.1/canvasXpress.css.php" rel="stylesheet">
	<script src="../../gene_expressions/js/canvasxpress/canvasxpress-18.1/canvasXpress.js.php"></script>

</head>
<body>

<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">


            <div class="container-fluid">

            	<h3 class="page-header">
            		Violin Plot on User-selected Genes
					<a class="ml-5" style="font-size: 1rem;" href="index.php"> <i class="fas fa-angle-double-right"></i> Start New Visualization</a>
					<a class="mx-2" style="font-size: 1rem;" href="list.php"> <i class="fas fa-angle-double-right"></i> All Visualizations</a>
            	</h3>
            	<hr />

				<form class="w-100" id="form_main">

        			<div class="row w-100">
                    	<div class="col-md-6">
        					<?php include_once(__DIR__ . '/modal_gene.php'); ?>
        				</div>
                        <div class="col-md-6">
        					<?php include_once(__DIR__ . '/modal_sample.php'); ?>
        				</div>
        			</div>

<?php

		$name_captions = array();
		foreach ($BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES_FIELDS'] as $colname) {
			$caption = str_replace("_", " ", str_replace('Clinical_Triplets_', '', $colname));
			$name_captions[$colname] = $caption;
		}
		asort($name_captions);

?>

    				<div class="w-100 my-3">
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Group Samples By: </label>
    					</div>
						<div class="form-check form-check-inline">
    						<select class="custom-select" name="category">
								<?php
									foreach ($name_captions as $colname=>$caption) {
										echo "<option value='$colname'>$caption</option>";
									}
								?>
							</select>

    					</div>
    				</div>

					<div class="w-100 my-3">
    					<div class="my-3">
    						<label class="font-weight-bold" for="">Include other categories in json file: </label>
    					</div>
						<div class="my-3" id="div_attributes">

							<?php
								$type = 'Sample';
								$checked = array("CellType", "DiseaseState", "Tissue", "Treatment");
								foreach ($name_captions as $colname=>$caption) {
									echo '<div class="form-check form-check-inline">
										<input class="form-check-input attributes_checkbox_all reset_chart" type="checkbox" category="' . $type . '" value="' . $colname . '" name="attributes_' . $type . '[]" ' . (in_array($colname, $checked) ? "checked " : "") . '>';
										echo '<label class="form-check-label">' . $caption . '</label>';
									echo '</div>';
								}
							?>

    					</div>
    				</div>


					<div class="w-100 my-3">
						<div class="form-check form-check-inline">
							<div class="form-check form-check-inline">
        						<label class="form-check-label font-weight-bold" for="">Segregate Data By Gene Symbol: </label>
        					</div>
							<div class="form-check form-check-inline">
        						<input class="form-check-input" type="radio" name="segregateSamplesBy" value="Gene Symbol" checked>
        						<label class="form-check-label" for="">Yes</label>
        					</div>
							<div class="form-check form-check-inline">
        						<input class="form-check-input" type="radio" name="segregateSamplesBy" value="">
        						<label class="form-check-label" for="">No</label>
        					</div>

    					</div>
    				</div>


					<div class="w-100 my-3">

	        			<button type="submit" class="btn btn-primary" id="btn_submit">
	        				<i class="fas fa-chart-pie"></i> Plot
	        			</button>

						<a class="ml-3" href="<?php echo $_SERVER['PHP_SELF']; ?>"> <i class="fas fa-sync"></i> Reset All </a>
					</div>

				</form>


	            <div class="my-3 p-3" id="div_results"></div>
	    		<div class="my-3" id="div_debug"></div>

            </div>

		</div>

	</div>
</div>


<script>

$(document).ready(function() {

	var options = {
		url: 'violin_genes_exe.php?action=generate_plot',
 		type: 'post',
    	beforeSubmit: function(formData, jqForm, options) {

    		$('#div_results').html('');

			$('#btn_submit')
				.attr('disabled', '')
				.children(':first')
				.removeClass('fa-chart-pie')
				.addClass('fa-spin fa-spinner');

			return true;
		},
    	success: function(response){

			$('#btn_submit')
				.removeAttr('disabled')
				.children(':first')
				.addClass('fa-chart-pie')
				.removeClass('fa-spin fa-spinner');

				$('#div_results').html(response);

			return true;
		}
	};

	$('#form_main').ajaxForm(options);


});

</script>

</body>
</html>