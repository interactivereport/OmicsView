<?php

include_once("config.php");



if (isset($_GET['action']) && $_GET['action'] == 'generate_plot') {


    $current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
    $current_project_url = $project_url . urlencode($_GET['project']) . '/';

    $meta_data_file = $current_project_dir . "meta_data.tsv";

    if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($meta_data_file) ){
        echo '<h4 class="text-danger">Error:</h4> Project meta file is not found.';
    	exit();
    }

    $gene_indexnames = category_text_to_idnames($_POST['Gene_List'], 'name', 'gene');
    if (! is_array($gene_indexnames) || count($gene_indexnames) <= 0) {
        echo '<h4 class="text-danger">Error:</h4> No genes found. Please enter some gene names.';
        exit();
    }

    $sample_indexnames = category_text_to_idnames($_POST['Sample_List'], 'name', 'sample');
    if (! is_array($sample_indexnames) || count($sample_indexnames) <= 0) {
        echo '<h4 class="text-danger">Error:</h4> No samples found. Please enter some sample names.';
        exit();
    }


    $time = microtime(true);
    $current_cache_dir = "{$current_project_dir}cache/{$time}";
    $current_cache_url = "{$current_project_url}cache/{$time}";
    if(! file_exists($current_cache_dir) ) mkdir($current_cache_dir, 0775, true);


    $fileInput = $current_cache_dir . "/tabix.input";
	$fp = fopen($fileInput, 'w');
	foreach($gene_indexnames as $gene_index=>$gene_name){
		foreach($sample_indexnames as $sample_index=>$sample_name){
            $gene_index1 = $gene_index + 1;
            fwrite($fp, "{$sample_index}\t{$gene_index1}\t{$gene_index1}\n");
		}
	}
	fclose($fp);

    if(filesize($fileInput) <= 0){
        echo '<h4 class="text-danger">Error:</h4> No genes/samples found. Please verify gene names and sample names.';
        exit();
    }


    $fileOutput = $current_cache_dir . "/tabix.output";
    $batch_commands = "#!/usr/bin/bash\n";
    $batch_commands .= "cd $current_cache_dir\n";
    $batch_commands .= "{$BXAF_CONFIG['TABIX_BIN']} {$tabix_sample_file} -R {$fileInput} > {$fileOutput}\n";

    $batch_file = $current_cache_dir . "/tabix.bash";
    file_put_contents($batch_file, $batch_commands);
    chmod($batch_file, 0755);
    chdir($current_cache_dir);
    exec("$batch_file > tabix.log 2>&1");

    if(filesize($fileOutput) <= 0){
        echo '<h4 class="text-danger">Error:</h4> No data found for selected genes and samples.';
        exit();
    }


    $tabix_results = array();
    $found_gene_indexes = array();
    $found_sample_indexes = array();
    if (($handle = fopen($fileOutput, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {

            $found_sample_indexes[ $data[0] ] = 1;
            $found_gene_indexes[ $data[1] ] = 1;

            $tabix_results[ $data[0] ][ $data[1] ] = $data[2];
    	}
    	fclose($handle);
    }


    $found_sample_names = array();
    $data_matrix_csv = array();

    $k = 0;
    $data_matrix_csv[$k] = array();
    $data_matrix_csv[$k][] = '';
    foreach($found_sample_indexes as $sample_index => $v){
        $found_sample_names[ $sample_index ] = $sample_indexnames[$sample_index];
        $data_matrix_csv[$k][] = $sample_indexnames[$sample_index];
    }

    foreach($found_gene_indexes as $gene_index => $v){
        $k++;
        $data_matrix_csv[$k] = array();
        $data_matrix_csv[$k][] = $gene_indexnames[$gene_index];

        foreach($found_sample_indexes as $sample_index => $v){
            $data_matrix_csv[$k][] = isset($tabix_results[ $sample_index ][ $gene_index ]) ? $tabix_results[ $sample_index ][ $gene_index ] : 0;
        }
    }


    $file = "{$current_cache_dir}/data_matrix.csv";
    $handle = fopen($file, "w");
    foreach($data_matrix_csv as $row) {
        fputcsv($handle, $row );
    }
    fclose($handle);

    if(filesize($file) <= 0){
        echo '<h4 class="text-danger">Error:</h4> No data found for selected genes and samples.';
        exit();
    }



    $sample_group_csv = array();
    $sample_group_csv[] = array('SampleID', $_POST['category']);

    // $meta_data_info = array();
    $meta_data_head = array();
    if (($handle = fopen($meta_data_file, "r")) !== FALSE) {
        $meta_data_head = fgetcsv($handle, 0, "\t");
        $meta_data_head_flip = array_flip($meta_data_head);
        while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {

            $sample_name = $data[ $meta_data_head_flip['SampleID'] ];
            if(! in_array($sample_name, $found_sample_names)) continue;

            foreach($meta_data_head as $i=>$c){
                if($c == $_POST['category']) $sample_group_csv[] = array($sample_name, $data[$i]);
            }
        }
        fclose($handle);
    }


    $file = "{$current_cache_dir}/sample_group.csv";
    $handle = fopen($file, "w");
    foreach($sample_group_csv as $row) {
        fputcsv($handle, $row );
    }
    fclose($handle);

    if(filesize($file) <= 0){
        echo '<h4 class="text-danger">Error:</h4> No sample group information found for selected genes and samples.';
        exit();
    }





    $Rscript = <<< RSCRIPT_CONTENT

        setwd('{$current_cache_dir}');

        data_file="data_matrix.csv";
        sample_file="sample_group.csv";

        options(stringsAsFactors=F);
        library(ggplot2);
        library(stringr);
        library(svglite);
        library(cowplot);
        library(reshape2);

        subdata=read.csv(data_file,row.names=1, header=T);
        subdata[subdata<0]=0;
        subdata=log2(subdata+0.5);
        samples=read.csv(sample_file);

        if (!all(colnames(subdata)==samples[, 1])) { stop ("Sample names must match between FPKM table and meta data!")}
        plot.data=data.frame(t(subdata), Cluster=samples[, 2]);
        meta_type=colnames(samples)[2];
        Pdata=melt(data=plot.data, id.vars="Cluster");
        Pdata\$Cluster[Pdata\$Cluster==""]="Unknown";
        Nc=length(unique(Pdata\$Cluster));
        p<-ggplot(Pdata, aes(x=Cluster, y=value, color=Cluster, fill=Cluster)) +geom_violin(trim=T) + scale_x_discrete(limits = rev(levels(as.factor(Pdata\$Cluster))))+
          geom_jitter(shape=16, position=position_jitter(0.2), size=0.5, color="black", alpha=0.5)+facet_grid(cols=vars(variable))+
         theme(axis.text.x = element_text(angle = 90, hjust = 1)) +labs(x=meta_type, y="log2(FPKM+0.5)", title="Gene")+theme(legend.position="none");

        pdf_width=(nrow(subdata)*2.5+3);
        pdf_height=max(6, Nc*0.75);
        pdf("Gene_Violin_Plot.pdf", width=pdf_width, height=pdf_height);
        p+ coord_flip();
        dev.off();

        png("Gene_Violin_Plot.png", width=pdf_width*72, height=pdf_height*72);
        p+ coord_flip();
        dev.off();

        svglite("Gene_Violin_Plot.svg", width=pdf_width, height=pdf_height);
        p+ coord_flip();
        dev.off();
        system("gzip -c Gene_Violin_Plot.svg > Gene_Violin_Plot.svgz");

RSCRIPT_CONTENT;

    file_put_contents("$current_cache_dir/rscript.R", $Rscript);

    exec("/usr/bin/Rscript $current_cache_dir/rscript.R > $current_cache_dir/rscript.Rout 2>&1");

    sleep(3);

    if(file_exists("$current_cache_dir/Gene_Violin_Plot.svgz")){

        $output_content = '';

        $output_content .= "<div class='my-3'>";

            $output_content .= "<div class='my-3'><strong>Download Images:</strong> <a class='' href='$current_cache_url/Gene_Violin_Plot.pdf' target='_blank'> PDF File </a> | <a class='' href='$current_cache_url/Gene_Violin_Plot.svgz' target='_blank'> SVG FIle</a> </div>";

            $output_content .= "<div class='my-3 text-center'><a class='' href='$current_cache_url/Gene_Violin_Plot.pdf' target='_blank'><img class='img-fluid' src='$current_cache_url/Gene_Violin_Plot.png' /></a></div>";

        $output_content .= "</div>";

        $_POST['OUTPUT'] = $output_content;

        $key = bxaf_save_to_cache($_POST);

        $url = $BXAF_CONFIG['BXAF_APP_URL'] . "bxgenomics/tool_data_view/violin_genes2.php?key=$key&project=" . urlencode($_GET['project']);
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

?>