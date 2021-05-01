<?php

include_once("config.php");

if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}


$current_project_dir = $project_dir . $_GET['project'] . '/';
$current_project_url = $project_url . $_GET['project'] . '/';


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
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js"></script>

</head>
<body>

<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">


            <div class="container-fluid">

            	<h3 class="page-header">
            		Identify Top Abundant Genes For Cell Groups
					<a class="ml-5" style="font-size: 1rem;" href="index.php"> <i class="fas fa-angle-double-right"></i> Start New Visualization</a>
					<a class="mx-2" style="font-size: 1rem;" href="list.php"> <i class="fas fa-angle-double-right"></i> All Visualizations</a>
            	</h3>
            	<hr />

				<div class="my-3">
					<a class="mx-2 btn btn-sm btn-success" href="summary.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> Summary </a>
					<a class="mx-2 btn btn-sm btn-success" href="view_iframe.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> 3D Visualization </a>
					<a class="mx-2 btn btn-sm btn-success" href="violin.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Violin Plot </a>
					<a class="mx-2 btn btn-sm btn-success" href="violin_genes.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Violin Plot with Genes</a>
					<a class="mx-2 btn btn-sm btn-success" href="multi_tsne.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Multi-tSNE View</a>
					<a class="mx-2 btn btn-sm btn-success" href="index.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-sync"></i> Re-Analysis </a>
					<a class="mx-2 btn btn-sm btn-success" href="bookmarks.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-bookmark"></i> Bookmarks </a>
					<a class="mx-2 btn btn-sm btn-success" href="abundant.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> Top Abundant Genes </a>
            	</div>

				<form class="w-100" id="form_main">

    				<div class="w-100 my-3">
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Choose a category to group cells: </label>
    					</div>
						<div class="form-check form-check-inline">
    						<select class="custom-select" name="group_by">
								<?php


									$meta_data_file = $current_project_dir . "meta_data.tsv";
									$meta_data_info = array();
									if (($handle = fopen($meta_data_file, "r")) !== FALSE) {
										$meta_data_head = fgetcsv($handle, 0, "\t");
										while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
											foreach($meta_data_head as $i=>$c){
												$meta_data_info[$c][ $data[$i] ] = is_numeric( $data[$i] ) ? 1 : 0;
											}
										}
										fclose($handle);

										foreach($meta_data_info as $c=>$vals){
											$is_numeric = false;
											$vals = array_keys($vals);
											if(count($vals) > 50 || array_sum($vals) > 10) $is_numeric = true;
											if($is_numeric) unset($meta_data_info[$c]);
										}

									}

									$options = array_keys($meta_data_info);

									foreach($options as $opt){
										$cap = str_replace('_', ' ', str_replace('Clinical_Triplets_', '', $opt));
										echo "<option value='$opt'>$cap</option>";
									}
								?>
								<option value='Cluster'>Cluster</option>
							</select>

    					</div>
    				</div>

					<div class="w-100 my-3">
    					<div class="form-inline">
    						<label class="form-check-label font-weight-bold mr-2" for="">Display Top Genes: </label>
							<input class="form-control" style="width: 5rem;" type="text" name="top_N" value="50">
    					</div>
    				</div>

					<div class="w-100 my-3">

	        			<button type="submit" class="btn btn-primary" id="btn_submit">
	        				<i class="fas fa-chart-pie"></i> Submit
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


	//-----------------------------------------------------------------------------
	// Generate Chart
	//-----------------------------------------------------------------------------
	var options = {
		url: 'exe.php?action=identify_abundant_genes&project=<?php echo urldecode($_GET['project']); ?>',
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
					url: 'exe.php?action=identify_abundant_genes_status&project=' + $('#processing_time5').attr('project') + '&time=' + $('#processing_time5').attr('time') + '&process_id=' + $('#processing_time5').attr('process_id') + '&top_N=' + $('#processing_time5').attr('top_N'),
					success: function(responseText){
						if(responseText != ''){
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