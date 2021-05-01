<?php

include_once("config.php");

if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}

$numeric_headers = array();
$non_numeric_headers = array();
$meta_data_info = array();
$sample_names_custom = array();


$current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
$current_project_url = $project_url . urlencode($_GET['project']) . '/';

$meta_data_file = $current_project_dir . "meta_data.tsv";
$meta_data_types = array();
if (($handle = fopen($meta_data_file, "r")) !== FALSE) {
	$meta_data_head = fgetcsv($handle, 0, "\t");
	$n = 0;
	while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
		foreach($meta_data_head as $i=>$c){
			$meta_data_info[$c][ $n ] = $data[$i];
			$meta_data_types[$c][ $data[$i] ] = is_numeric( $data[$i] ) ? 1 : 0;
		}
		$n++;
	}
	fclose($handle);

	$sample_names_custom = array_unique($meta_data_info['SampleID']);

	foreach($meta_data_types as $c=>$vals){
		$is_numeric = false;
		$vals = array_keys($vals);
		if(count($vals) > 50 || array_sum($vals) > 10) $is_numeric = true;
		if($is_numeric){
			$numeric_headers[] = $c;
		}
		else {
			$non_numeric_headers[] = $c;
		}
	}
}






if (isset($_GET['action']) && $_GET['action'] == 'annotated_heatmap_sc') {


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

        if(file_exists($current_cache_dir . "/Top_gene_list.txt")){
            $gene_indexnames1 = category_text_to_idnames(file_get_contents($current_cache_dir . "/Top_gene_list.txt"), 'name', 'gene');
            if(is_array($gene_indexnames1) && count($gene_indexnames1) > 0){
                foreach($gene_indexnames1 as $k=>$v) $gene_indexnames[$k] = $v;
            }
        }
    }



    $sample_indexes = array();
    if($_POST['Sample_List'] != ''){
        $sample_indexnames = category_text_to_idnames($_POST['Sample_List'], 'name', 'sample');

        if (! is_array($sample_indexnames) || count($sample_indexnames) <= 0) {
            echo '<h4 class="text-danger">Error:</h4> No samples found. Please enter some sample names.';
            exit();
        }

		foreach($sample_indexnames as $s_index=>$s_name){
			if(! in_array($s_name, $sample_names_custom)){
				unset($sample_indexnames[$s_index]);
			}
		}

        $sample_indexes = array_keys($sample_indexnames);
    }

    $data_matrix_info = array();


    $all_geneindex_genenames = array();
    $sql = "SELECT `GeneIndex`, `GeneName`  FROM `TBL_BXGENOMICS_GENES_INDEX` WHERE `Species` = '" . $BXAF_CONFIG['SPECIES'] . "'";
    $all_geneindex_genenames = $BXAF_MODULE_CONN -> get_assoc('GeneIndex', $sql);
	foreach($all_geneindex_genenames as $i=>$n){
		if($i > 10000000) { $all_geneindex_genenames[$i-10000000] = $n; unset($all_geneindex_genenames[$i]); }
	}

	$gene_indexes = array_keys($gene_indexnames);
    if(! is_array($gene_indexes)) $gene_indexes = array();


	$genes_template_filename = "genes_template.csv";
    $samples_template_filename = "samples_template.csv";
    $genes_template_content = array('');
    $samples_template_content = array();


	$file_type = "data_matrix";
	if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
		if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
			move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
		}
	}
    else if(count($sample_indexes) > 0){
        $tabix_results = tabix_search_bxgenomics( $gene_indexes, $sample_indexes, 'GeneFPKM' );

		$sample_indexnames_new = array();
		foreach($tabix_results as $i=>$row){
			$s_index = $row['SampleIndex'];
            $s_name  = $sample_indexnames[$s_index];

			if(! in_array($s_name, $sample_indexnames_new)) $sample_indexnames_new[ $s_index ] = $s_name;

		}
		$sample_indexnames = $sample_indexnames_new;
		$sample_indexes = array_keys($sample_indexnames);


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

    }


	$file_type = "left_info";
	if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
		if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
			move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
		}
	}
    else if(is_array($_POST['left_fields']) && count($_POST['left_fields']) > 0){

        $file = "{$current_cache_dir}/left_info.csv";
        $handle = fopen($file, "w");
        fputcsv($handle, array_merge(array('SampleID'), $_POST['left_fields']) );

		foreach($meta_data_info['SampleID'] as $i=>$SampleID) {
			if(! in_array($SampleID, $sample_indexnames)) continue;

			$row = array($SampleID);
			foreach($_POST['left_fields'] as $fld){
				if($fld == 'Cluster') $row[] = 'Cluster_' . $meta_data_info[$fld][$i];
				else  $row[] = $meta_data_info[$fld][$i];
			}
			fputcsv($handle, $row);
		}
        fclose($handle);
    }

	$file_type = "right_info";
	if (isset($_FILES[$file_type]["error"]) && $_FILES[$file_type]["error"] == UPLOAD_ERR_OK) {
		if (is_uploaded_file($_FILES[$file_type]['tmp_name'])) {
			move_uploaded_file($_FILES[$file_type]['tmp_name'], "$current_cache_dir/{$file_type}.csv");
		}
	}
	else if(is_array($_POST['right_fields']) && count($_POST['right_fields']) > 0){

        $file = "{$current_cache_dir}/right_info.csv";
        $handle = fopen($file, "w");
        fputcsv($handle, array_merge(array('SampleID'), $_POST['right_fields']) );

		foreach($meta_data_info['SampleID'] as $i=>$SampleID) {
			if(! in_array($SampleID, $sample_indexnames)) continue;

			$row = array($SampleID);
			foreach($_POST['right_fields'] as $fld){
				$row[] = $meta_data_info[$fld][$i];
			}
			fputcsv($handle, $row);
		}
        fclose($handle);
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
			ha_sample1 = HeatmapAnnotation(df=sample_info1, show_annotation_name=T, which="row",annotation_name_side="top")
        }


        if (left_file!="none") {
        	sample_info2=read.csv(left_file, row.names=1)
        	sel=match(rownames(sample_info2), colnames(subdata) )
        	if (sum(is.na(sel))>0 || nrow(sample_info2)!=ncol(subdata) ) {stop ("Left info not matching data!")}
        	sample_info2=sample_info2[sel,1:ncol(sample_info2), drop=F]
			ha_sample2 = HeatmapAnnotation(df=sample_info2, show_annotation_name=T, which="row",annotation_name_side="top",show_legend = F)
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

        $url = $BXAF_CONFIG['BXAF_APP_URL'] . "bxgenomics/tool_data_view/annotated_heatmap_sc.php?key=$key&project=" . urlencode($_GET['project']);
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
	'top_white_space'=>'25',
	'top_N' => '5'
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
		else {
			$sample_names_custom = $saved_info;
		}

        foreach($saved_info as $k=>$v){
            if(array_key_exists($k, $default_values)) $default_values[$k] = $v;
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


        			<div class="row w-100">
                    	<div class="col-md-6">
        					<?php include_once(__DIR__ . '/modal_gene.php'); ?>
        				</div>
                        <div class="col-md-6">
        					<?php include_once(__DIR__ . '/modal_sample.php'); ?>
        				</div>
        			</div>

					<div class="w-100 my-3">
    					<div class="form-inline">
    						<label class="form-check-label font-weight-bold mr-2" for="">Number of Top Genes for Each Cluster: </label>
							<input class="form-control" style="width: 5rem;" type="text" name="top_N" value="<?php echo $default_values['top_N']; ?>">
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
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Left Annotation Fields: </label>
    					</div>
						<div class="form-check form-check-inline">
							<?php
								$options = $numeric_headers;
								if(! in_array('Cluster', $options)) $options[] = 'Cluster';

								$checked = array();
								if(is_array($default_values['left_fields']) && count($default_values['left_fields']) > 0) $checked = $default_values['left_fields'];
								foreach($options as $opt){
									if($opt == 'nGene' || $opt == 'SampleID') continue;
									$cap = str_replace('_', ' ', str_replace('Clinical_Triplets_', '', $opt));
									echo '<div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" value="' . $opt . '" name="left_fields[]" ' . (in_array($opt, $checked) ? "checked " : "") . '>';
    									echo '<label class="form-check-label">' . $cap . '</label>';
    								echo '</div>';
								}
							?>
    					</div>
    				</div>

    				<div class="w-100 my-3">
    					<div class="form-check form-check-inline">
    						<label class="form-check-label font-weight-bold" for="">Categories in the Right Annotation File: </label>
    					</div>
						<div class="form-check form-check-inline">
							<?php
								$options = array_merge($non_numeric_headers, array('nGene'));
								$checked = array();
								if(is_array($default_values['right_fields']) && count($default_values['right_fields']) > 0) $checked = $default_values['right_fields'];
								foreach($options as $opt){
									if($opt == 'Cluster') continue;
									$cap = str_replace('_', ' ', str_replace('Clinical_Triplets_', '', $opt));
									echo '<div class="form-check form-check-inline"> <input class="form-check-input" type="checkbox" value="' . $opt . '" name="right_fields[]" ' . (in_array($opt, $checked) ? "checked " : "") . '>';
    									echo '<label class="form-check-label">' . $cap . '</label>';
    								echo '</div>';
								}
							?>
    					</div>
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
                    $url = $BXAF_CONFIG['BXAF_APP_URL'] . "bxgenomics/tool_data_view/annotated_heatmap_sc.php?key=" . $_GET['key'] . "&project=" . urlencode($_GET['project']);
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
		url: 'annotated_heatmap_sc.php?action=annotated_heatmap_sc&project=<?php echo urldecode($_GET['project']); ?>',
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