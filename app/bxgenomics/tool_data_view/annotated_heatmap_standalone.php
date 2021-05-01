<?php

include_once("config.php");




if (isset($_GET['action']) && $_GET['action'] == 'annotated_heatmap_standalone') {


    if(! isset($_GET['project']) || $_GET['project'] == ''){
    	$_GET['project'] = 0;
    }

    $time = microtime(true);

    $current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
    $current_project_url = $project_url . urlencode($_GET['project']) . '/';

    $current_cache_dir = "{$current_project_dir}cache/{$time}";
    $current_cache_url = "{$current_project_url}cache/{$time}";
    if(! file_exists($current_cache_dir) ) mkdir($current_cache_dir, 0775, true);

    file_put_contents("$current_cache_dir/parameters.txt", serialize($_POST) );


    $data_matrix_info = array();
    $sample_info = array();

    $file_type = "data_matrix";
    if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
            move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
        }
    }

    $file_type = "right_info";
    if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
            move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
        }
    }

    $file_type = "left_info";
    if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
            move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
        }
    }


    $file_type = "bottom_info";
    if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
            move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
        }
    }

    if(isset($_POST['use_demo_files']) != '' && $_POST['use_demo_files'] == 1){
        $data_matrix = __DIR__ . "/annotated_heatmap_data/data_matrix.csv";
        $left_info   = __DIR__ . "/annotated_heatmap_data/left_info.csv";
        $right_info  = __DIR__ . "/annotated_heatmap_data/right_info.csv";
        $bottom_info = __DIR__ . "/annotated_heatmap_data/bottom_info.csv";
    }
    else {
        $data_matrix = "{$current_cache_dir}/data_matrix.csv";

        if(file_exists("{$current_cache_dir}/left_info.csv")) $left_info   = "{$current_cache_dir}/left_info.csv";
        else $left_info = "none";

        if(file_exists("{$current_cache_dir}/right_info.csv")) $right_info   = "{$current_cache_dir}/right_info.csv";
        else $right_info = "none";

        if(file_exists("{$current_cache_dir}/bottom_info.csv")) $bottom_info   = "{$current_cache_dir}/bottom_info.csv";
        else $bottom_info = "none";

    }

    if(! file_exists($data_matrix)){
        echo '<h2 class="text-danger my-5">No data matrix file found. </h2> ';
        exit();
    }



    $Rscript = <<< RSCRIPT_CONTENT


        setwd('{$current_cache_dir}');

        library(ComplexHeatmap);
        library(circlize);
        options(stringsAsFactors=F);
        library(dplyr);
        library(stringr);
        library(data.table);
        library(svglite);
        library(genefilter);

        #user input
        value="{$_POST['value']}"
        log_transform={$_POST['log_transform']}
        small_value={$_POST['small_value']}
        Z_scale={$_POST['Z_scale']}
        show_gene_names={$_POST['show_gene_names']}
        show_sample_names={$_POST['show_sample_names']}
        cluster_gene={$_POST['cluster_gene']}
        cluster_sample={$_POST['cluster_sample']}
        title="{$_POST['title']}"
        sample_tree_height={$_POST['sample_tree_height']}
        gene_tree_height={$_POST['gene_tree_height']}
        pdf_width={$_POST['pdf_width']}
        pdf_height={$_POST['pdf_height']}
        top_white_space={$_POST['top_white_space']}

        #input files
        right_file  = "$right_info";  #use “none” if user didn’t upload file
        left_file   = "$left_info";
        bottom_file = "$bottom_info";
        data_file   = "$data_matrix";
        max_genes=5000;
        #####

        #start loading data
        color_legend=value
        subdata=read.csv(data_file, row.names=1, header=T)
        if (log_transform) {
        	subdata[subdata<0]=0; subdata=log2(subdata+small_value)
        	color_legend=str_c("log2(", value, ")")
        }

		dataSD=rowSds(subdata)
		SD_0=(dataSD==0)
		cat("Remove", sum(SD_0), "genes with SD=0\\n")
		subdata=subdata[!SD_0, ]
		#If too many genes, sub-sample

        if (Z_scale) {
        	sdata=t(scale(t(subdata)) )
        	sdata=pmin(sdata, 3)
        	sdata=pmax(sdata, (0-3))
        	subdata=sdata
        	color_legend=str_c("Scaled ", color_legend)
        }

		row_NA=is.na(rowSums(subdata))
		if (sum(row_NA)>0) {
		     cat("Remove", sum(row_NA), "genes due to NAs.\\n")
		     subdata=subdata[!row_NA, ]
		}

		if (nrow(subdata)>max_genes) {
		     cat("Too many genes, reduce from", nrow(subdata), "to", max_genes, "using random sampling.\\n")
		     sel=sample(1:nrow(subdata), max_genes)
		     subdata=subdata[sel, ]
		}


        if (right_file!="none") {
        	sample_info1=read.csv(right_file, row.names=1)
        	sel=match(rownames(sample_info1), colnames(subdata) )
        	if (sum(is.na(sel))>0 || nrow(sample_info1)!=ncol(subdata) ) {stop ("Right info not matching data!")}
        	sample_info1=sample_info1[sel, 1:ncol(sample_info1), drop=F]
        	ha_sample1 = HeatmapAnnotation(sample_info1, show_annotation_name=T, which="row",annotation_name_side="top")
        }


        if (left_file!="none") {
        	sample_info2=read.csv(left_file, row.names=1)
        	sel=match(rownames(sample_info2), colnames(subdata) )
        	if (sum(is.na(sel))>0 || nrow(sample_info2)!=ncol(subdata) ) {stop ("Left info not matching data!")}
        	sample_info2=sample_info2[sel,1:ncol(sample_info2), drop=F]
        	ha_sample2 = HeatmapAnnotation(sample_info2, show_annotation_name=T, which="row",annotation_name_side="top",show_legend = F)

        }

        if (bottom_file!="none") {
        	gene_info=read.csv(bottom_file, row.names=1)
        	sel=match(rownames(gene_info), rownames(subdata) )
        	if (sum(is.na(sel))>0 ||nrow(gene_info)!=nrow(subdata) ) {stop ("Bottom info not matching data!")}
        	gene_info=gene_info[sel, 1:ncol(gene_info), drop=F]
        ha_gene = HeatmapAnnotation(df = gene_info, show_annotation_name=T, which="column", annotation_name_side="right")
        h1_Signal=Heatmap(t(subdata), show_row_names=show_sample_names, show_column_names=show_gene_names, cluster_columns=cluster_gene, cluster_rows=cluster_sample,
        column_title=title, column_dend_height = unit(gene_tree_height, "mm"), row_dend_width = unit(sample_tree_height, "mm"),
        heatmap_legend_param = list(title = color_legend, color_bar = "continuous"),  bottom_annotation = ha_gene)

        } else {
        h1_Signal=Heatmap(t(subdata), show_row_names=show_sample_names, show_column_names=show_gene_names, cluster_columns=cluster_gene, cluster_rows=cluster_sample,
        column_title=title, column_dend_height = unit(gene_tree_height, "mm"), row_dend_width = unit(sample_tree_height, "mm"),
        	 heatmap_legend_param = list(title = color_legend, color_bar = "continuous"))
        }

        if (left_file!="none") {Plot=ha_sample2+h1_Signal} else {Plot=h1_Signal}
        if (right_file!="none") {Plot=Plot+ha_sample1}

        pdf("Complex_Heatmap_output.pdf", width=pdf_width, height=pdf_height)
        draw(Plot, padding = unit(c(10, 5, top_white_space, 5), "mm"))
        dev.off()

        png("Complex_Heatmap_output.png", width=pdf_width*72, height=pdf_height*72)
        draw(Plot, padding = unit(c(10, 5, top_white_space, 5), "mm"))
        dev.off()

        svglite("Complex_Heatmap_output.svg", width=pdf_width, height=pdf_height)
        draw(Plot, padding = unit(c(10, 5, top_white_space, 5), "mm"))
        dev.off()
        system("gzip -c Complex_Heatmap_output.svg >Complex_Heatmap_output.svgz")



RSCRIPT_CONTENT;

    file_put_contents("$current_cache_dir/rscript.R", $Rscript);

    exec("/usr/bin/Rscript $current_cache_dir/rscript.R > $current_cache_dir/rscript.Rout 2>&1");


    if(file_exists("$current_cache_dir/Complex_Heatmap_output.png")){

        echo "<div class='my-3'>";

            echo "<div class='my-3 text-center'><a class='lead' href='$current_cache_url/Complex_Heatmap_output.pdf' target='_blank'>Download PDF File</a> | <a class='lead' href='$current_cache_url/Complex_Heatmap_output.svgz' target='_blank'>Download SVG FIle</a></div>";

            echo "<div class='my-3 text-center'><a class='' href='$current_cache_url/Complex_Heatmap_output.pdf' target='_blank'><img class='img-fluid' src='$current_cache_url/Complex_Heatmap_output.png' /></a></div>";

        echo "</div>";

    }
    else {
        echo '<h2 class="text-danger my-5">Failed to process data. </h2> ';
        if(file_exists("$current_cache_dir/rscript.Rout") && filesize("$current_cache_dir/rscript.Rout") > 0){
            $error = file_get_contents("$current_cache_dir/rscript.Rout");
            echo "<pre>$error</pre>";
        }
    }


    exit();
}







$current_project_dir = $project_dir;
$current_project_url = $project_url;


$default_values = array(
	'value'=>"FPKM",
	'log_transform'=>'T',
	'small_value'=>'0.5',
	'Z_scale'=>'T',
	'show_gene_names'=>'F',
	'show_sample_names'=>'F',
	'cluster_gene'=>'T',
	'cluster_sample'=>'T',
	'title'=>'Heatmap',
	'sample_tree_height'=>'20',
	'gene_tree_height'=>'20',
	'pdf_width'=>'15',
	'pdf_height'=>'10',
	'top_white_space'=>'25'
);



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
            		Annotated Heatmap Tool
            	</h3>
            	<hr />

				<form class="w-100" id="form_main">

                    <div class="w-100 my-3 form-group form-inline">
                        <span class="mx-2 font-weight-bold">Upload your data file (Genes in row, Samples in columns): </span>
                        <input id="input_upload_file" type="file" class="form-control mx-2" name="data_matrix" onclick="$('#Sample_List').val('');" >
						<a class="mx-2" href="annotated_heatmap_data/data_matrix.csv">
							<i class="fas fa-angle-double-right"></i> Example
						</a>
                    </div>

                    <div class="w-100 my-3 form-group form-inline">
                        <span class="mx-2">(Optional), Left annotation for Samples: &nbsp;&nbsp;</span>
                        <input id="input_upload_file" type="file" class="form-control mx-2" name="left_info" >
						<a class="mx-2" href="annotated_heatmap_data/left_info.csv">
							<i class="fas fa-angle-double-right"></i> Example
						</a>
                    </div>

                    <div class="w-100 my-3 form-group form-inline">
						<span class="mx-2">(Optional), Right annotation for Samples: </span>
                        <input id="input_upload_file" type="file" class="form-control mx-2" name="right_info" >
						<a class="mx-2" href="annotated_heatmap_data/right_info.csv">
							<i class="fas fa-angle-double-right"></i> Example
						</a>
                    </div>

					<div class="w-100 my-3 form-group form-inline">
						<span class="mx-2">(Optional), Bottom annotation for Genes: </span>
                        <input id="input_upload_file" type="file" class="form-control mx-2" name="bottom_info" >
						<a class="mx-2" href="annotated_heatmap_data/bottom_info.csv">
							<i class="fas fa-angle-double-right"></i> Example
						</a>
                    </div>

					<hr />
					<div class="my-3">
						<span class="font-weight-bold">Visualization Options</span>
						<a style="font-size: 1rem;" href="javascript:void(0);" onclick="if($('#div_options').hasClass('hidden')) $('#div_options').removeClass('hidden'); else $('#div_options').addClass('hidden'); "> <i class="fas fa-angle-double-right"></i> Show/Hide Options </a>
					</div>
					<hr />

					<div class="w-100 my-3 hidden" id="div_options">

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Expression value type: </label>
    						<input class="form-control" type="text" name="value" value="<?php echo $default_values['value']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
							<label class="font-weight-bold mx-2">Log transform expression data: </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="T" name="log_transform" <?php echo $default_values['log_transform'] == 'T' ? 'checked' : ''; ?>>
								<label class="form-check-label">True</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="F" name="log_transform" <?php echo $default_values['log_transform'] == 'F' ? 'checked' : ''; ?>>
								<label class="form-check-label">False</label>
							</div>
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Small value to add to zero in log transformation: </label>
    						<input class="form-control" type="text" name="small_value" value="<?php echo $default_values['small_value']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
							<label class="font-weight-bold mx-2">Z-Scale data: </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="T" name="Z_scale" <?php echo $default_values['Z_scale'] == 'T' ? 'checked' : ''; ?>>
								<label class="form-check-label">True</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="F" name="Z_scale" <?php echo $default_values['Z_scale'] == 'F' ? 'checked' : ''; ?>>
								<label class="form-check-label">False</label>
							</div>
        				</div>

						<div class="w-100 my-1 form-inline">
							<label class="font-weight-bold mx-2">Show gene names: </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="T" name="show_gene_names" <?php echo $default_values['show_gene_names'] == 'T' ? 'checked' : ''; ?>>
								<label class="form-check-label">True</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="F" name="show_gene_names" <?php echo $default_values['show_gene_names'] == 'F' ? 'checked' : ''; ?>>
								<label class="form-check-label">False</label>
							</div>
        				</div>

						<div class="w-100 my-1 form-inline">
							<label class="font-weight-bold mx-2">Show sample names: </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="T" name="show_sample_names" <?php echo $default_values['show_sample_names'] == 'T' ? 'checked' : ''; ?>>
								<label class="form-check-label">True</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="F" name="show_sample_names" <?php echo $default_values['show_sample_names'] == 'F' ? 'checked' : ''; ?>>
								<label class="form-check-label">False</label>
							</div>
        				</div>

						<div class="w-100 my-1 form-inline">
							<label class="font-weight-bold mx-2">Cluster genes: </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="T" name="cluster_gene" <?php echo $default_values['cluster_gene'] == 'T' ? 'checked' : ''; ?>>
								<label class="form-check-label">True</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="F" name="cluster_gene" <?php echo $default_values['cluster_gene'] == 'F' ? 'checked' : ''; ?>>
								<label class="form-check-label">False</label>
							</div>
        				</div>

						<div class="w-100 my-1 form-inline">
							<label class="font-weight-bold mx-2">Cluster samples: </label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="T" name="cluster_sample" <?php echo $default_values['cluster_sample'] == 'T' ? 'checked' : ''; ?>>
								<label class="form-check-label">True</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" value="F" name="cluster_sample" <?php echo $default_values['cluster_sample'] == 'F' ? 'checked' : ''; ?>>
								<label class="form-check-label">False</label>
							</div>
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Title for heatmap: </label>
    						<input class="form-control" type="text" name="title" value="<?php echo $default_values['title']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Height of sample tree: </label>
    						<input class="form-control" type="text" name="sample_tree_height" value="<?php echo $default_values['sample_tree_height']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Height of gene tree: </label>
    						<input class="form-control" type="text" name="gene_tree_height" value="<?php echo $default_values['gene_tree_height']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Graph width (inch): </label>
    						<input class="form-control" type="text" name="pdf_width" value="<?php echo $default_values['pdf_width']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">Graph height (inch): </label>
    						<input class="form-control" type="text" name="pdf_height" value="<?php echo $default_values['pdf_height']; ?>" placeholder="" >
        				</div>

						<div class="w-100 my-1 form-inline">
    						<label class="mx-2">White space on top of graph (mm): </label>
    						<input class="form-control" type="text" name="top_white_space" value="<?php echo $default_values['top_white_space']; ?>" placeholder="" >
        				</div>

					</div>


					<div class="w-100 my-3">

	        			<button type="submit" class="btn btn-primary" id="btn_submit">
	        				<i class="fas fa-chart-pie"></i> Submit
	        			</button>

						<span id="form_busy" class="mx-2 hidden text-danger"><i class="fas fa-spinner fa-spin"></i></span>

						<a class="ml-3" href="<?php echo $_SERVER['PHP_SELF']; ?>"> <i class="fas fa-sync"></i> Reset All </a>

						<div class="form-check form-check-inline">
							<input class="form-check-input ml-5" type="checkbox" value="1" name="use_demo_files">
							<label class="form-check-label">Try with Server Demo Files</label>
						</div>
					</div>

				</form>

	            <div class="my-3 p-3" id="div_results"></div>
	    		<div class="my-3" id="div_debug"></div>

            </div>

		</div>
		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>


<script>

$(document).ready(function() {


	//-----------------------------------------------------------------------------
	// Generate Chart
	//-----------------------------------------------------------------------------
	var options = {
		url: 'annotated_heatmap_standalone.php?action=annotated_heatmap_standalone&project=<?php echo urldecode($_GET['project']); ?>',
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