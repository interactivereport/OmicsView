<?php

include_once("config.php");

$_GET['project'] = 0;
$current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
$current_project_url = $project_url . urlencode($_GET['project']) . '/';


if (isset($_GET['action']) && $_GET['action'] == 'annotated_heatmap_integrated') {


    if(! isset($_GET['project']) || $_GET['project'] == ''){
    	$_GET['project'] = 0;
    }

    $time = microtime(true);


    $current_cache_dir = "{$current_project_dir}cache/{$time}";
    $current_cache_url = "{$current_project_url}cache/{$time}";
    if(! file_exists($current_cache_dir) ) mkdir($current_cache_dir, 0775, true);

    file_put_contents("$current_cache_dir/parameters.txt", serialize($_POST) );


    if($_POST['Gene_List'] != ''){
        $gene_indexnames = category_text_to_idnames($_POST['Gene_List'], 'name', 'gene');

        if (! is_array($gene_indexnames) || count($gene_indexnames) <= 0) {
            echo '<h4 class="text-danger">Error:</h4> No genes found. Please enter some gene names.';
            exit();
        }
        $gene_indexes = array_keys($gene_indexnames);

    }

    if(intval($_POST['top_N']) > 0){

        $top_N = intval($_POST['top_N']);

        if(file_exists($current_project_dir . "Cluster_Markers.csv")){
            exec("ln -s {$current_project_dir}Cluster_Markers.csv {$current_cache_dir}/Cluster_Markers.csv");
        }
        else {
            echo '<h4 class="text-danger">Error:</h4> No Cluster Markers file found.';
            exit();
        }


        $Rscript = <<< RSCRIPT_CONTENT

    setwd('{$current_cache_dir}');

    options(stringsAsFactors=F)
    library(dplyr);
    library(stringr);
    top_N=$top_N
    markers=read.csv("Cluster_Markers.csv", row.names=1)
    top_genes <- markers %>% filter(!duplicated(gene))%>%group_by(cluster) %>% top_n(top_N, avg_logFC)
    gene_list=unique(top_genes\$gene)
    write(gene_list, file="Top_gene_list.txt", sep="\\n")

RSCRIPT_CONTENT;

        file_put_contents("$current_cache_dir/rscript_Top_gene_list.R", $Rscript);

        exec("/usr/bin/Rscript $current_cache_dir/rscript_Top_gene_list.R > $current_cache_dir/rscript_Top_gene_list.Rout 2>&1");

        if(file_exists($current_project_dir . "Top_gene_list.txt")){
            $gene_indexnames1 = category_text_to_idnames(file_get_contents($current_project_dir . "Top_gene_list.txt"), 'name', 'gene');
            if(is_array($gene_indexnames1) && count($gene_indexnames1) > 0){
                $gene_indexnames = array_merge($gene_indexnames, $gene_indexnames1);
            }
        }
    }


    if($_POST['Sample_List'] != ''){
        $sample_indexnames = category_text_to_idnames($_POST['Sample_List'], 'name', 'sample');

        if (! is_array($sample_indexnames) || count($sample_indexnames) <= 0) {
            echo '<h4 class="text-danger">Error:</h4> No samples found. Please enter some sample names.';
            exit();
        }
        $sample_indexes = array_keys($sample_indexnames);
    }

    $data_matrix_info = array();
    $sample_info = array();


    $all_geneindex_genenames = array();
    $sql = "SELECT `GeneIndex`, `GeneName`  FROM `TBL_BXGENOMICS_GENES_INDEX` WHERE `Species` = 'Human'";
    $all_geneindex_genenames = $BXAF_MODULE_CONN -> get_assoc('GeneIndex', $sql);

    $genes_template_filename = "genes_template.csv";
    $samples_template_filename = "samples_template.csv";
    $genes_template_content = array('');
    $samples_template_content = array();

    if(! is_array($gene_indexes)) $gene_indexes = array();
    if(count($sample_indexes) > 0){
        $tabix_results = tabix_search_bxgenomics( $gene_indexes, $sample_indexes, 'GeneFPKM' );

        $data_matrix_info[''] = array();
        $data_matrix_info[''][] = '';
        foreach($sample_indexnames as $s_index=>$s_name) $data_matrix_info[''][$s_index] = str_replace('-', '.', $s_name);
        ksort($data_matrix_info['']);
        $samples_template_content = $data_matrix_info[''];

        foreach($tabix_results as $row){

            $g_index = $row['GeneIndex'];
            $g_name  = $all_geneindex_genenames[ $g_index ];
            $s_index = $row['SampleIndex'];
            $s_name  = $sample_indexnames[$s_index];

            $value = $row['Value'];

            if($g_name == '') continue;

            if(! isset($data_matrix_info[$g_name])){
                $data_matrix_info[$g_name] = array();
                $data_matrix_info[$g_name][''] = $g_name;
            }

            $data_matrix_info[$g_name][$s_index] = $value;
            $genes_template_content[] = $g_name;
        }

        $file = "{$current_cache_dir}/data_matrix.csv";
        $handle = fopen($file, "w");
        foreach($data_matrix_info as $row) {
            ksort($row);
            fputcsv($handle, array_values($row) );
        }
        fclose($handle);

        $genes_template_content = array_unique($genes_template_content);
        $samples_template_content = array_unique($samples_template_content);
        file_put_contents("{$current_cache_dir}/{$genes_template_filename}", implode("\n", $genes_template_content));
        file_put_contents("{$current_cache_dir}/{$samples_template_filename}", implode("\n", $samples_template_content));

        if(isset($_POST['attributes_Sample']) && is_array($_POST['attributes_Sample']) && count($_POST['attributes_Sample']) > 0){
            $sql = "SELECT *  FROM ?n WHERE `SampleIndex` IN (?a)";
            $sample_info = $BXAF_MODULE_CONN -> get_all($sql, $BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES'], $sample_indexes);
        }

    }
    else {
        $file_type = "data_matrix";
        if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
            if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
                move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
            }
        }
    }


    $file_type = "right_info";
    $file = "{$current_cache_dir}/{$file_type}.csv";
    if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
            move_uploaded_file($_FILES[$file_type]['tmp_name'], $file);
        }
    }
    else if(is_array($sample_info) && count($sample_info) > 0){
        $handle = fopen($file, "w");
        fputcsv($handle, array_merge(array('SampleID'), $_POST['attributes_Sample']) );
        foreach($sample_info as $s_info) {
            $row = array();
            $row[] = str_replace('-', '.', $s_info['SampleID']);
            foreach($_POST['attributes_Sample'] as $fld) $row[] = $s_info[$fld];
            fputcsv($handle, $row);
        }
        fclose($handle);
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

        $output_content = '';

        $output_content .= "<div class='my-3'>";

            $output_content .= "<div class='my-3'><strong>Download Images:</strong> <a class='' href='$current_cache_url/Complex_Heatmap_output.pdf' target='_blank'> PDF File </a> | <a class='' href='$current_cache_url/Complex_Heatmap_output.svgz' target='_blank'> SVG FIle</a> <BR /> <strong>Download Templates:</strong> <a class='' href='$current_cache_url/{$genes_template_filename}' target='_blank'>Gene List (for creating Bottom Annotation File)</a> | <a class='' href='$current_cache_url/{$samples_template_filename}' target='_blank'>Sample List (for creating Left and Right Annotation Files)</a></div>";

            $output_content .= "<div class='my-3 text-center'><a class='' href='$current_cache_url/Complex_Heatmap_output.pdf' target='_blank'><img class='img-fluid' src='$current_cache_url/Complex_Heatmap_output.png' /></a></div>";

        $output_content .= "</div>";

        $_POST['OUTPUT'] = $output_content;

        $key = bxaf_save_to_cache($_POST);

        $url = $BXAF_CONFIG['BXAF_APP_URL'] . "bxgenomics/tool_data_view/annotated_heatmap_integrated.php?key=$key";
        $short_url = get_short_url($url);
        echo "<div class='my-1'><strong>Bookmark URL:</strong> <a href='$short_url' target='_blank'>$short_url</a></div>";
        echo $output_content;
    }
    else {
        echo '<h2 class="text-danger my-5">Failed to process data. </h2><div class="my-3">Folder: ' . $current_cache_dir . '</div>';

        if(file_exists("$current_cache_dir/rscript.Rout") && filesize("$current_cache_dir/rscript.Rout") > 0){
            $error = file_get_contents("$current_cache_dir/rscript.Rout");
            echo "<pre>$error</pre>";
        }
    }


    exit();
}







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



if(isset($_GET['key']) && $_GET['key'] != ''){

    $saved_info = bxaf_get_from_cache($_GET['key']);

    if(is_array($saved_info) && count($saved_info) > 0){
        if(isset($saved_info['Gene_List']) && $saved_info['Gene_List'] != ''){
            $gene_names_custom = explode("\n", $saved_info['Gene_List']);
        }

        if(isset($saved_info['Sample_List']) && $saved_info['Sample_List'] != ''){
            $sample_names_custom = explode("\n", $saved_info['Sample_List']);
        }

        foreach($saved_info as $k=>$v){
            if(array_key_exists($k, $default_values)) $default_values[$k] = $v;
        }

        if(array_key_exists('attributes_Sample', $saved_info)) $default_values['attributes_Sample'] =  $saved_info['attributes_Sample'];
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

            	<h3 class="page-header">
            		Annotated Heatmap Tool
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

                    <div class="w-100 my-3 form-group form-inline">
                        <span class="mx-2">Or, Upload your data file (Genes in row, Samples in columns): </span>
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


					<div class="w-100 my-3">
						<?php
        					$type = 'Sample';
							$list = $BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES_FIELDS'];
        					sort($list);
							$checked = array("CellType", "DiseaseState", "Tissue", "Treatment");
							if(is_array($default_values['attributes_Sample']) && count($default_values['attributes_Sample']) > 0) $checked = $default_values['attributes_Sample'];
						?>

						<div class="w-100 my-3">
                            <label class="font-weight-bold">Sample Attributes (Used to auto-generate right annotation file):</label>

							<span class="table-success mx-2 p-2">( <span id="span_number_attributes"><?php echo count($checked); ?></span> selected )</span>

							<a href="javascript:void(0);" onclick="if($('#div_attributes').hasClass('hidden')) $('#div_attributes').removeClass('hidden'); else $('#div_attributes').addClass('hidden'); "> <i class="fas fa-angle-double-right"></i> Show/Hide Attributes </a>

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
						Visualization Options
						<a style="font-size: 1rem;" href="javascript:void(0);" onclick="if($('#div_options').hasClass('hidden')) $('#div_options').removeClass('hidden'); else $('#div_options').addClass('hidden'); "> <i class="fas fa-angle-double-right"></i> Show/Hide Options </a>
					</h5>
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

	            <div class="my-3 p-3" id="div_results"><?php
                if(isset($saved_info['OUTPUT'])){
                    $url = $BXAF_CONFIG['BXAF_APP_URL'] . "bxgenomics/tool_data_view/annotated_heatmap_integrated.php?key=" . $_GET['key'];
                    $short_url = get_short_url($url);
                    echo "<div class='my-1'><strong>Bookmark URL:</strong> <a href='$short_url' target='_blank'>$short_url</a></div>";
                    echo $saved_info['OUTPUT'];
                } ?></div>
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
		url: 'annotated_heatmap_integrated.php?action=annotated_heatmap_integrated&project=<?php echo urldecode($_GET['project']); ?>',
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