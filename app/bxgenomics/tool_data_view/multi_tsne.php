<?php

include_once("config.php");


if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}


$current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
$current_project_url = $project_url . urlencode($_GET['project']) . '/';



$Process_Log_info = array();
$file = $current_project_dir . 'Process_Log.tsv';
if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
		if($data[0] != '') $Process_Log_info[ $data[0] ] = $data[1];
    }
    fclose($handle);
}

$parameters = unserialize( file_get_contents("$current_project_dir/parameters.txt") );


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

	<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js'></script>

	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js.php'></script>


	<script src="js/svg-pan-zoom.js"></script>

	<style>
		.svg-pan-zoom-control-background {
			fill: gray !important;
		}
		.svg-pan-zoom-control-element {
			fill: white !important;
		}
		.hidden_soft {
			display: none;
		}
		.mt-1 {
			margin-top: 7px;
		}
	</style>

<?php if($Process_Log_info['tSNE'] != 'PASS'){ ?>
	<script>
		$(document).ready(function() {
			bootbox.alert("<h4 class='text-danger my-5'>Multi-tSNE view not available since tSNE failed in Seurat analysis. Please try re-run the analysis with difference settings.</h4>", function(){
				window.location = "list.php";
			})
		});
	</script>
<?php } //if($Process_Log_info['tSNE'] != 'PASS'){ ?>


</head>
<body>

<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">


            <div class="container-fluid">

            	<h3 class="page-header">
            		Multi-tSNE Map View of Gene Expression Profile
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

        				</div>
        			</div>

					<div class="w-100 my-3">
						<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Show tSNE Cluster: </label>
    					</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="t_cluster" id="t_cluster_T" value="T" checked>
							<label class="form-check-label" for="t_cluster_T">Yes</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="t_cluster" id="t_cluster_N" value="F">
							<label class="form-check-label" for="t_cluster_N">No</label>
						</div>
					</div>

					<div class="w-100 my-3">
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Display Attributes: </label>
    					</div>
						<div class="form-check form-check-inline">
    						<select class="custom-select" name="sel_attribute">
								<option value="None" selected>None</option>
								<?php
									foreach($parameters['attributes_Sample'] as $s) echo "<option value='$s'>$s</option>";
								?>
							</select>

    					</div>
    				</div>

					<div class="w-100 my-3">
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Top Genes for Each Cluster: </label>
    					</div>
						<div class="form-check form-check-inline">
    						<select class="custom-select" name="N_top_genes">
								<?php
									for($i=0; $i <= 10; $i++) echo "<option value='$i' " . ($i == 2 ? 'selected' : '') . ">$i</option>";
								?>
							</select>

    					</div>
    				</div>

					<div class="w-100 my-3">

	        			<button type="submit" class="btn btn-primary" id="btn_submit">
	        				<i class="fas fa-chart-pie"></i> Plot
	        			</button>

						<a class="ml-3" href="<?php echo $_SERVER['PHP_SELF']; ?>"> <i class="fas fa-sync"></i> Reset All </a>
					</div>

					<div class="w-100 my-3">
						<label class="text-warning" for="">This program may run over one minute. Please be patient!</label>
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
		url: 'multi_tsne_exe.php?action=generate_plot&project=<?php echo urldecode($_GET['project']); ?>',
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

			panZoomInstance = svgPanZoom("#svg_main", {
				zoomEnabled: true,
				controlIconsEnabled: true,
				fit: true,
				center: true,
				minZoom: 0.1
			});
			panZoomInstance.zoom(1.1);

			return true;
		}
	};

	$('#form_main').ajaxForm(options);

});


</script>

</body>
</html>