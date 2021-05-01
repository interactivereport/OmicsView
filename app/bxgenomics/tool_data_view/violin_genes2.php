<?php

include_once("config.php");


if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}


$current_project_dir = $project_dir . $_GET['project'] . '/';
$current_project_url = $project_url . $_GET['project'] . '/';


$meta_data_file = $current_project_dir . "meta_data.tsv";
$meta_data_info = array();
$sample_names_custom = array();
if (($handle = fopen($meta_data_file, "r")) !== FALSE) {
	$meta_data_head = fgetcsv($handle, 0, "\t");
	while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
		foreach($meta_data_head as $i=>$c){
			if($c == 'SampleID') $sample_names_custom[] = $data[$i];
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




if(isset($_GET['key']) && $_GET['key'] != ''){
    $saved_info = bxaf_get_from_cache($_GET['key']);
    if(is_array($saved_info) && count($saved_info) > 0){
        if(isset($saved_info['Gene_List']) && $saved_info['Gene_List'] != ''){
            $gene_names_custom = explode("\n", $saved_info['Gene_List']);
        }
        if(isset($saved_info['Sample_List']) && $saved_info['Sample_List'] != ''){
            $sample_names_custom = explode("\n", $saved_info['Sample_List']);
        }
		else {
			$sample_names_custom = $saved_info;
		}
    }
}

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

    <link  href="../../gene_expressions/js/canvasxpress/canvasxpress-20.1/canvasXpress.css.php" rel="stylesheet">
	<script src="../../gene_expressions/js/canvasxpress/canvasxpress-20.1/canvasXpress.js.php"></script>

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



    				<div class="w-100 my-3">
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Choose a category to group cells: </label>
    					</div>
						<div class="form-check form-check-inline">
    						<select class="custom-select" name="category">
								<?php

									$options = array_keys($meta_data_info);
									array_push($options, 'GeneSymbol');
									array_push($options, 'Cluster');
									$options = array_unique($options);
									sort($options);

									foreach($options as $opt){
										$cap = str_replace('_', ' ', str_replace('Clinical_Triplets_', '', $opt));
										echo "<option value='$opt' " . ($opt == $saved_info['category'] ? "selected" : "") . ">$cap</option>";
									}

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

				</form>


	            <div class="my-3 p-3" id="div_results"><?php
                if(isset($saved_info['OUTPUT'])){
                    $url = $BXAF_CONFIG['BXAF_APP_URL'] . "bxgenomics/tool_data_view/violin_genes2.php?key=" . $_GET['key'] . "&project=" . urlencode($_GET['project']);
                    $short_url = get_short_url($url);
                    echo "<div class='my-1'><strong>Bookmark URL:</strong> <a href='$short_url' target='_blank'>$short_url</a></div>";
                    echo $saved_info['OUTPUT'];
                } ?></div>
	    		<div class="my-3" id="div_debug"></div>

            </div>

		</div>
		<?php // if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>


<script>

$(document).ready(function() {

	var options = {
		url: 'violin_genes2_exe.php?action=generate_plot&project=<?php echo urldecode($_GET['project']); ?>',
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