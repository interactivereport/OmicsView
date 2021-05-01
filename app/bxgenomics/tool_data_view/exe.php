<?php

include_once('config.php');


if (isset($_GET['action']) && $_GET['action'] == 'update_permission') {

    if(array_key_exists($_GET['project'], $projects_info)){
        $projects_info[ $_GET['project'] ]['Permission'] = $_GET['permission'] == 'Private' ? 'Private' : 'Public';
    }
    file_put_contents($projects_info_file, serialize($projects_info));

    exit();
}



if (isset($_GET['action']) && $_GET['action'] == 'delete_project') {

    if(array_key_exists($_GET['project'], $projects_info)){
        unset( $projects_info[ $_GET['project'] ] );
        file_put_contents($projects_info_file, serialize($projects_info));

        $current_dir = $project_dir . urlencode($_GET['project']);
        if(file_exists($current_dir)){
            $command = "rm -rf $current_dir";
            exec($command);
        }
    }

    exit();
}







else if (isset($_GET['action']) && $_GET['action'] == 'generate_plot') {

    $current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
    $current_project_url = $project_url . urlencode($_GET['project']) . '/';

    $meta_data_file = $current_project_dir . "meta_data.tsv";

    if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($meta_data_file) ){
        echo '<h4 class="text-danger">Error:</h4> Project meta file is not found.';
    	exit();
    }

	$parameters = unserialize(file_get_contents($current_project_dir . "parameters.txt"));

    $meta_data_info = array();
    $meta_data_head = array();
    if (($handle = fopen($meta_data_file, "r")) !== FALSE) {
        $meta_data_head = fgetcsv($handle, 0, "\t");
        while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
            foreach($meta_data_head as $i=>$c){
                $meta_data_info[$c][] = $data[$i];
            }
        }
        fclose($handle);
    }

    $temp1 = $meta_data_info[ $_POST['category'] ];
    asort($temp1);
    $temp2 = array();
    foreach($meta_data_info as $attribute => $vals) {
        foreach($temp1 as $k=>$v){
            $temp2[$attribute][$k] = $vals[$k];
        }
    }
    $meta_data_info = $temp2;


    if(! is_array($meta_data_info) || ! array_key_exists('Cluster', $meta_data_info) || ! array_key_exists('SampleID', $meta_data_info) || ! array_key_exists('SampleID', $meta_data_info)){
        echo '<h4 class="text-danger">Error:</h4> The meta data file does not have complete information: ' . "<a class='my-3' href='{$current_project_url}meta_data.tsv'><i class='fas fa-download'></i> Download Meta Data File</a>";
    	exit();
    }



    $x = array();

    if($_POST['included_all_categories_in_json'] == '1'){
        $attribute = $_POST['category'];
        $attribute = str_replace('Clinical_Triplets_', '', $attribute);
        $x[$attribute] = array();
        foreach($meta_data_info[$attribute] as $v) $x[$attribute][] = ($v == '' || $v == 'NA') ? "'Unknown'" :  ("'" . addslashes($v) . "'");

        $attribute = 'Cluster';
        $x[$attribute] = array();
        foreach($meta_data_info[$attribute] as $v) $x[$attribute][] = ($v == '' || $v == 'NA') ? "'Unknown'" :  ("'Cluster_" . addslashes($v) . "'");

        foreach($parameters['attributes_Sample'] as $attribute){
            $attribute = str_replace('Clinical_Triplets_', '', $attribute);
            if($attribute == $_POST['category'] || $attribute == 'Cluster') continue;
            $x[$attribute] = array();
            foreach($meta_data_info[$attribute] as $v) $x[$attribute][] = ($v == '' || $v == 'NA') ? "'Unknown'" :  ("'" . addslashes($v) . "'");
        }
    }
    else {
        $attribute = $_POST['category'];
        $attribute = str_replace('Clinical_Triplets_', '', $attribute);
        $x[$attribute] = array();
        foreach($meta_data_info[$attribute] as $v) $x[$attribute][] = ($v == '' || $v == 'NA') ? "'Unknown'" :  ("'" . addslashes($v) . "'");
    }

    $y["smps"] = array();
    foreach($meta_data_info['SampleID'] as $v) $y["smps"][] = ($v == '' || $v == 'NA') ? "'Unknown'" :  ("'" . addslashes($v) . "'");

    $y["data"] = $meta_data_info['nGene'];



    $output_contents = '';
    if(count($tabix_data) == $limit){
        $output_contents .= "<a class='my-3 lead' href='{$current_project_url}meta_data.tsv'><i class='fas fa-download'></i> Download Meta Data File</a>";
    }
    $output_contents .= '<div class="my-3 w-100"></div>' . "\n\n";

        $output_contents .= "<canvas class='plot_container my-3' id='plotSection' width='1000' height='900' xresponsive='false' aspectRatio='1:1'></canvas>" . "\n\n";
        $output_contents .= '<script type="text/javascript">' . "\n\n";

            $output_contents .= '$(document).ready(function() {' . "\n\n";

                $output_contents .= 'var plotObj = new CanvasXpress("plotSection", ' . "\n";

                    // data
                    $output_contents .= '{';

                        $output_contents .= '"x": {';

                            $x_contents = array();
                            foreach ($x as $k=>$vals) {
                                $x_contents[] = "'$k': [" . implode(",", $vals) . "]";
                            }
                            $output_contents .= implode(",\n", $x_contents);

                        $output_contents .= '},';
                        $output_contents .= "\n\n";

                        $output_contents .= '"y": {';

                            $output_contents .= "'vars': ['Number of Genes'],";
                            $output_contents .= '"smps":[' . implode(",", $y['smps']) . '],';
                            $output_contents .= '"data":[ [' . implode(",", $y['data']) . '] ]';

                        $output_contents .= '}';

                    $output_contents .= '},';
                    $output_contents .= "\n\n";


                    $output_contents .= '{';
                        $output_contents .= '
                            "graphOrientation"          : "vertical",
                            "graphType"                 : "Boxplot",
                            "jitter"                    : true,

                            "showViolinBoxplot"         : true,


                            "legendBox"                 : true,
                            "showLegend"                : true,

                            "plotByVariable"            : true,
                            "showBoxplotOriginalData"   : true,
                            "smpLabelRotate"            : 0,

                            "showShadow"                : false,

                            "title"                     : "Number of Genes Detected",

                            "printType"                 : "window",
                            "sizes"                     : [ "4.0", "4.5", "5.0", "5.5", "6.0", "6.5", "7.0", "7.5", "8.0", "8.5", "9.0", "9.5", "10.0", "10.5", "11.0", "11.5" ]
                        ';
                    $output_contents .= '}';
                    $output_contents .= "\n\n";


                    $output_contents .= "\n\n";

                $output_contents .= ');';
                $output_contents .= "\n\n";


                $output_contents .= 'plotObj.groupSamples(["' . $_POST['category'] . '"]);';

                $output_contents .= "\n\n";

            $output_contents .= '});';
        $output_contents .= '</script>';


    echo $output_contents;

    exit();
}





else if (isset($_GET['action']) && $_GET['action'] == 'show_data_view') {

    $_POST['View_Name'] = preg_replace("/[^\w\.\-]/", '', $_POST['View_Name']);

    $current_dir = $project_dir . urlencode($_POST['View_Name']);
    $current_url = $project_url . urlencode($_POST['View_Name']);
    if( $_POST['View_Name'] == '' ){
        echo '<h4 class="text-danger">Error:</h4> You need to enter a unique view name.';
        exit();
    }
    else if(file_exists($current_dir)){
        echo '<h4 class="text-danger">Error:</h4> The view name is taken. Please try with a different one.';
        exit();
    }
    else {
        mkdir($current_dir, 0775, true);
    }

    file_put_contents("$current_dir/parameters.txt", serialize($_POST) );

    $sample_indexnames = category_text_to_idnames($_POST['Sample_List'], 'name', 'sample');
    if (! is_array($sample_indexnames) || count($sample_indexnames) <= 0) {
        echo '<h4 class="text-danger">Error:</h4> No samples found. Please enter some sample names.';
        exit();
    }
    else if(count($sample_indexnames) <= 5){
        echo '<h4 class="text-danger">Error:</h4>You need to add at least 5 samples.';
        exit();
    }
    else if(count($sample_indexnames) > 50000){
        echo '<h4 class="text-danger">Error:</h4> The maximum number of samples you can enter should be less than 50000.';
        exit();
    }
    $sample_indexes = array_keys($sample_indexnames);




    // Save Project Info
    $info = array(
        'Name' => $_POST['View_Name'],
        'DIR'  => $current_dir . '/',
        'URL'  => $current_url . '/',
        'Date' => date('Y-m-d H:i:s'),
        'Samples' => $sample_indexnames,
        'Owner' => $BXAF_CONFIG['BXAF_USER_CONTACT_ID'],
        'Permission' => 'Private',
    );
    $projects_info[ $_POST['View_Name'] ] = $info;
    file_put_contents($projects_info_file, serialize($projects_info));




    // Public
    $sql = "SELECT `SampleID`, `ProjectName` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES']}` WHERE `SampleID` IN (?a)";
	$samples_public = $BXAF_MODULE_CONN -> get_assoc('SampleID', $sql, array_values($sample_indexnames));
    $projects_public = array();
    if(is_array($samples_public) && count($samples_public) > 0){
        $sql = "SELECT `ProjectID`, `ProjectIndex` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS']}` WHERE `ProjectID` IN (?a)";
    	$projects_public = $BXAF_MODULE_CONN -> get_assoc('SampleID', $sql, array_values($samples_public));
    }

	// Private
    $sql = "SELECT `SampleIndex`, `ProjectIndex` FROM `App_User_Data_Samples` WHERE `SampleIndex` IN (" . implode(",", array_keys($sample_indexnames)). ")";
    $sampleindex_projectindex_private = $BXAF_MODULE_CONN -> get_assoc('SampleIndex', $sql);


    if(! file_exists("$current_dir/raw_exp_data.gz")){
        $batch_commands = "#!/usr/bin/bash\n";
        $batch_commands .= "cd $current_dir\n";

        $files = array();
        $tabix_index_file = $tabix_sample_file;
        if(is_array($samples_public) && count($samples_public) > 0){
            $batch_commands .= "{$BXAF_CONFIG['TABIX_BIN']} {$tabix_index_file} " . implode(" ", $sample_indexes) . " | /usr/bin/gzip > raw_exp_data_public.gz\n";
            $files[] = 'raw_exp_data_public.gz';
        }
        if(is_array($sampleindex_projectindex_private) && count($sampleindex_projectindex_private) > 0){
            $projectindex_sampleindex = array();
            foreach($sampleindex_projectindex_private as $sampleindex=>$projectindex){
                $projectindex_sampleindex[$projectindex][] = $sampleindex;
            }
            foreach($projectindex_sampleindex as $projectindex=>$sampleindexes){
                $tabix_index_file = "{$BXAF_CONFIG['WORK_DIR']}Internal_Data/Processed/ProjectIndex_{$projectindex}/bigzip/GeneLevelExpression.txt.Sample.gz";
                $batch_commands .= "{$BXAF_CONFIG['TABIX_BIN']} {$tabix_index_file} " . implode(" ", $sampleindexes) . " | /usr/bin/gzip > raw_exp_data_{$projectindex}.gz\n";
                $files[] = "raw_exp_data_{$projectindex}.gz";
            }
        }
        $batch_commands .= "cat " . implode(" ", $files) . " > raw_exp_data.gz\n";

        $batch_file = $current_dir . "/tabix.bash";
        file_put_contents($batch_file, $batch_commands);
        chmod($batch_file, 0755);
        chdir($current_dir);
        exec("$batch_file > tabix.log 2>&1");

    }


    if(! file_exists("$current_dir/sample_info.csv")){

        $attributes_Sample = array_merge( array('SampleIndex', 'SampleID'), $_POST['attributes_Sample'] );
        $sql = "SELECT `" . implode("`,`", $attributes_Sample). "` FROM `" . $BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES'] . "` WHERE `SampleIndex` IN (?a)";
        $sample_info = $BXAF_MODULE_CONN -> get_all($sql, array_keys($sample_indexnames) );

        // Private
        $sql = "SELECT `" . implode("`,`", $attributes_Sample). "` FROM `App_User_Data_Samples` WHERE `SampleIndex` IN (?a)";
        $sample_info_private = $BXAF_MODULE_CONN -> get_all($sql, array_keys($sample_indexnames) );
        $sample_info = array_merge($sample_info, $sample_info_private);

        foreach($attributes_Sample as $i=>$v) $attributes_Sample[$i] = str_replace('Clinical_Triplets_', '', $v);

        $sample_info_csv = $current_dir . "/sample_info.csv";
        $handle = fopen($sample_info_csv, "w");
        if($handle){
            fputcsv($handle, $attributes_Sample);
            foreach($sample_info as $row){
                fputcsv($handle, $row);
            }
            fclose($handle);
        }
    }


    $project = urlencode($_POST['View_Name']);
    $URL = $project_url . $project . '/';

    $Genes = '"Auto"';
    if($_POST['Gene_List'] != ''){
        $gene_indexnames = category_text_to_idnames($_POST['Gene_List'], 'name', 'gene');
        if (is_array($gene_indexnames) && count($gene_indexnames) > 0) {
            $Genes = 'c("' . implode('", "', $gene_indexnames). '")';
        }
    }


    $Rscript = <<< RSCRIPT_CONTENT

        options(stringsAsFactors=F)

        library(genefilter);
        library(dplyr);
        library(stringr);
        library(data.table);
        library(Seurat);

        #####gene annotation file
        gene_annot_file="$gene_annot_file"

        #####change based on system settings and user input
        project="$project" #the name entered by user
        URL=str_c("$URL")
        #URL of the output folder, configure file will be saved here

        tensor_URL="$tensor_URL" #this is the location of embedding-projector-standalone

        #user input
        min_cells={$_POST['min_cells']}; #Number of cells expressing the gene
        nGene_low={$_POST['nGene_low']};
        nGene_high={$_POST['nGene_high']};  #number of genes per cell
        x_low = {$_POST['x_low']};
        x_high = {$_POST['x_high']};
        y_cutoff = {$_POST['y_cutoff']}; #Variable genes, x, mean log2 expression level (default 0.1 to 8), y log(Variance/mean), default 0.5

        N_PC={$_POST['N_PC']}; #first 12 PC to find clutsers
        cluser_res={$_POST['cluser_res']}; #cluster resolution
        min_pct = {$_POST['min_pct']};
        logfc_threshold = {$_POST['logfc_threshold']}; #Find markers settings
        Genes=$Genes;  #highlight using gene expression values
        load_data="{$_POST['load_data']}";  #what type of data to load into tensorBoard
        #####

        #read expression data
        raw_data=fread("zcat raw_exp_data.gz");

        system.time(exp_data<-dcast(raw_data, V2~V1, value.var="V3", fun.aggregate = sum) );

        mdata=Matrix(as.matrix(exp_data[, 2:ncol(exp_data)]), sparse=T);
        rownames(mdata)=exp_data\$V2;
        sel=(rowSums(mdata)>0);
        cat(nrow(mdata), "remove no exp", sum(sel), "\\n");
        mdata=mdata[sel, ];

        #check to make sure there are at least 5 cells
        if ( ncol(mdata)<5) {
        	stop(str_c("Not enough cells in the data! Need at least 5, data set only has ", ncol(mdata), "\n"))
        }

        ##get annotation, replace geneIndex to Symbols (duplicate symbols will have sampleIndex suffix).

        gene_annot<-fread(cmd=str_c("zcat ", gene_annot_file), sep="\\t", header=T);

        gene_annot<-gene_annot%>%mutate(Symbol_Unique=ifelse(duplicated(GeneName), str_c(GeneName, "_", GeneIndex), GeneName) );
        sel=match(rownames(mdata), gene_annot\$GeneIndex);
        rownames(mdata)=gene_annot\$GeneID[sel];

        #load sample information, replace sampleIndex with SampleID
        sample_info=read.csv("sample_info.csv");
        sel=match(colnames(mdata), sample_info\$SampleIndex); sum(is.na(sel));
        colnames(mdata)=sample_info\$SampleID[sel];

        #create Suerat object, create violin plot of number of genes per cell
        sc_data <- CreateSeuratObject(raw.data = mdata, project = project, min.cells = min_cells);
        ggsave("Violin_Plot_nGene.pdf", VlnPlot(object = sc_data, features.plot = c("nGene")) );

        #filter, normalize, scale data
        sc_data <- FilterCells(sc_data, subset.names = "nGene", low.thresholds = nGene_low, high.thresholds = nGene_high);
        sc_data <- NormalizeData(object = sc_data, normalization.method = "LogNormalize", scale.factor = 10000);
        sc_data<-ScaleData(object=sc_data);

        #####New Code###
        #Detection of variable genes across the single cells
        raw_data=data.matrix(sc_data@raw.data);
        scaled_data=sc_data@scale.data;
        meta_data <- sample_info%>%select(-SampleIndex);
        meta_data<-meta_data%>%filter(SampleID %in% colnames(scaled_data))
        raw_data=log2(raw_data[, meta_data\$SampleID]+0.5);

        testVarGenes <- try(FindVariableGenes(object = sc_data, mean.function = ExpMean, dispersion.function = LogVMR, x.low.cutoff = x_low, x.high.cutoff = x_high, y.cutoff = y_cutoff, do.plot = F), silent = TRUE);

        ManualVar=F
        if (class(testVarGenes ) != "try-error") {
        	sc_data<-FindVariableGenes(object = sc_data, mean.function = ExpMean, dispersion.function = LogVMR, x.low.cutoff = x_low, x.high.cutoff = x_high, y.cutoff = y_cutoff, do.plot = F);
        	var_genes=sc_data@var.genes;
        	if (length(var_genes)<500) {
        		cat("Not enough varaible genes, use manual method\\n");
        		ManualVar=T;
        	} else {
        		cat(project, "variable genes:", length(var_genes), "\\n");
        		write(var_genes, "variable_genes.txt", sep="\\n");
        		cat ("FindVariableGenes\\tAUTO\\n", file="Process_Log.tsv")
        	}
        }

        if (class(testVarGenes) == "try-error" || ManualVar==T) {
        	cat("Seurat auto variable gene failed or not enough genes. Manually selected variable genes\\n");
        	dataSD=rowSds(raw_data)
        	dataM=rowMeans(raw_data)
        	dataS_M=dataSD/dataM
        	subdata=raw_data[dataS_M>0.3, ]
        	#trim down number of genes in subdata if needed
        	if (nrow(subdata)>5000) {
        		subdata=raw_data[dataS_M>0.3 & dataM>=1, ]
        		if (nrow(subdata)>5000) {
        			diff=rowSds(subdata)/rowMeans(subdata);
        			subdata=subdata[order(diff, decreasing=TRUE)[1:5000], ];
        		}
        	}
        	if (nrow(subdata)<500) {
        		subdata=raw_data[order(dataS_M,  decreasing=TRUE)[1:500], ];
        	}
        	var_genes=rownames(subdata);
        	cat(project, "manual variable genes:", length(var_genes), "\\n");
        	write(var_genes, "variable_genes.txt", sep="\\n");
        	sc_data@var.genes=var_genes;
        	cat ("FindVariableGenes\\tMANUAL\\n", file="Process_Log.tsv")
        }

        ###Produce file and meta data for tensorBoard. These files maybe updated if all Seurat analysis is done.


        #add a few gene expression values
        if (!all(Genes=="Auto") ){
        	In_data=(Genes %in% rownames(raw_data));
        	cat("Genes list", length(Genes), ", ", sum(In_data), "have matches in raw data matrix.\\n");
        	Genes=Genes[In_data];
        	for (gene in Genes) {
        		exp1=raw_data[gene, ];
        		meta_data=cbind(meta_data, exp1);
        		names(meta_data)[ncol(meta_data)]=gene;
        	}
        }

        fwrite(meta_data, "meta_data.tsv", sep="\\t", row.names=F,col.names=T);

        write.table(t(scaled_data[var_genes, ]), "scaled_data_var_gene.tsv", sep="\\t", row.names=F, col.names=F);

        if ( load_data=="all" ){  #only write all data file (huge) if user chooses this option
        	write.table(t(scaled_data), "scaled_data_all.tsv", sep="\\t", row.names=F, col.names=F);
        }

        #Write configure file  (json format)
        f1=ifelse(load_data=="all","scaled_data_all.tsv",  "scaled_data_var_gene.tsv") ; #ignore PCA at this time
        f2="meta_data.tsv";
        v_data=fread(f1, header=F);
        out_file="config.json";
        cat('{\\n',
        ' "embeddings": [\\n',
        '    {\\n',
        '      "tensorName": "', project, '",\\n',
        '      "tensorShape": [\\n',
        '        ', nrow(v_data), ',\\n',
        '        ', ncol(v_data), '\\n',
        '      ],\\n',
        '      "tensorPath": "', URL, f1, '",\\n',
        '      "metadataPath": "', URL, f2, '"\\n',
        '    }\\n',
        '  ]\\n',
        '}\\n', sep="", file=out_file);

        #create stats
        cat("All_Genes\\t", nrow(exp_data), "\\n", sep="", file="Stats.tsv");
        cat("Filtered_Genes\\t", nrow(scaled_data), "\\n", sep="", file="Stats.tsv", append=T);

        cat("All_Cells\\t", ncol(exp_data)-1, "\\n", sep="", file="Stats.tsv", append=T);

        cat("Filtered_Cells\\t", ncol(scaled_data), "\\n", sep="", file="Stats.tsv", append=T);
        cat("Variable_Genes\\t", length(var_genes), "\\n", sep="", file="Stats.tsv", append=T);
        cat("Clusters\\tNA\\n", file="Stats.tsv", append=T);  #not cluster at this time

        cat(tensor_URL, URL, out_file, "\\n", sep="", file="Tensor_URL.txt");
        #this is the URL to load tensorBoard with the data. Use in iFrame

        ######################


        #Perform linear dimensional reduction (PCA)
        testPCA<-try(RunPCA(object = sc_data, pc.genes = sc_data@var.genes, do.print = TRUE, pcs.print = 1:5, genes.print = 5), silent = TRUE)

        if (class(testPCA) == "try-error") {
        	cat ("PCA\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat ("FindClusters\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat ("tSNE\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat(testPCA)
        	stop("PCA failed... Aborting")
        }
        sc_data <- RunPCA(object = sc_data, pc.genes = sc_data@var.genes, do.print = TRUE, pcs.print = 1:5, genes.print = 5);
        cat ("PCA\\tPASS\\n", file="Process_Log.tsv", append=T)
        #may need in  the future
        sc_data <- ProjectPCA(object = sc_data, do.print = FALSE);
        N_cell= ncol(sc_data@scale.data)
        pdf("PCA_heatmap.pdf", width=8, height=10);
        PCHeatmap(object = sc_data, pc.use = 1:9, cells.use =  min(N_cell, 500), do.balanced = TRUE, label.columns = FALSE, use.full = FALSE);
        dev.off();
        ##
        ggsave("PC_Elbow_plot.pdf", PCElbowPlot(object = sc_data));
        #Cluster the cells

        testCluster<-try(FindClusters(object = sc_data, reduction.type = "pca", dims.use = 1:N_PC, resolution = cluser_res, print.output = 0, save.SNN = TRUE), silent = TRUE)
        if (class(testCluster) == "try-error") {
        	cat ("FindClusters\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat ("tSNE\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat(testCluster)
        	stop("Find Cluster failed... Aborting")
        }


        sc_data <- FindClusters(object = sc_data, reduction.type = "pca", dims.use = 1:N_PC, resolution = cluser_res, print.output = 0, save.SNN = TRUE);

        if (length(unique(sc_data@ident))>1) {
        	cat ("FindClusters\\tPASS\\n", file="Process_Log.tsv", append=T)
        	write.csv(table(sc_data@ident), "cluster_summary.csv", row.names=F);

        	# find markers for every cluster compared to all remaining cells, report
        	# only the positive ones
        	mm.markers <- FindAllMarkers(object = sc_data, only.pos = TRUE, min.pct = min_pct, logfc.threshold =  logfc_threshold );

        	top10 <- mm.markers %>% group_by(cluster) %>% top_n(10, avg_logFC);
        	pdf("Top10Genes_heatmap.pdf", width=10, height=max(10,  nrow(top10)/8));
        	print(DoHeatmap(object = sc_data, genes.use = top10\$gene, slim.col.label = TRUE, remove.key = TRUE));
        	dev.off();
        	write.csv(mm.markers, "Cluster_Markers.csv");
        	top4 <- mm.markers %>% group_by(cluster) %>% top_n(4, avg_logFC);
        	pdf("Top4Genes_FeaturePlot.pdf", width=10, height=max(10, nrow(top4)*0.6));
        	FeaturePlot(object = sc_data, features.plot = top4\$gene, cols.use = c("grey", "blue"), reduction.use = "pca");
        	dev.off();

        } else {
        	cat ("FindClusters\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat ("No clusters found!\\n")
        }
        ggsave("PCA_plot.pdf", PCAPlot(object = sc_data, dim.1 = 1, dim.2 = 2, do.return=TRUE));

        #tSNE
        testTSNE<-try(RunTSNE(object = sc_data, dims.use = 1:N_PC, do.fast = TRUE), silent = TRUE)
        if (class(testTSNE) == "try-error") {
        	cat ("tSNE\\tFAIL\\n", file="Process_Log.tsv", append=T)
        	cat(testTSNE)
        	save(sc_data, file="sc_data.Rdata");
        	stop("tSNE failed... Aborting")
        }

        sc_data <- RunTSNE(object = sc_data, dims.use = 1:N_PC, do.fast = TRUE);
        cat ("tSNE\\tPASS\\n", file="Process_Log.tsv", append=T)
        ggsave("tSNE_plot.pdf", TSNEPlot(object = sc_data, do.return=TRUE) );

        save(sc_data, file="sc_data.Rdata");

        if (length(unique(sc_data@ident))>1) {
        	top4 <- mm.markers %>% group_by(cluster) %>% top_n(4, avg_logFC);
        	pdf("Top4Genes_FeaturePlot.pdf", width=10, height=max(10, nrow(top4)*0.6));
        	FeaturePlot(object = sc_data, features.plot = top4\$gene, cols.use = c("grey", "blue"), reduction.use = "tsne");
        	dev.off();
        }


        ###Now output for TensorBoard Final Files
        if (all(Genes=="Auto")) {
        	top1 <- mm.markers %>% group_by(cluster) %>% top_n(1, avg_logFC);
        	Genes=top1\$gene;

        }

        pca_data=sc_data@dr\$pca@cell.embeddings;
        meta_data=sc_data@meta.data;
        names(meta_data)[4]="Cluster";
        meta_data\$SampleID=rownames(meta_data);
        meta_data <- meta_data[, c(1, 4, 5)];
        meta_data <- meta_data%>%left_join( sample_info%>%select(-SampleIndex) );


        #add a few gene expression values
        In_data=(Genes %in% rownames(raw_data));
        cat("Genes list", length(Genes), ", ", sum(In_data), "have matches in raw data matrix.\\n");
        Genes=Genes[In_data];
        for (gene in Genes) {
        	exp1=raw_data[gene, ];
        	meta_data=cbind(meta_data, exp1);
        	names(meta_data)[ncol(meta_data)]=gene;
        }
        fwrite(meta_data, "meta_data.tsv", sep="\\t", row.names=F,col.names=T);
        write.table(pca_data, "pca_data.tsv", sep="\\t", row.names=F,col.names=F);


        #Write configure file  (json format) (only if pca is selected)
        if (load_data=="pca") {
        	f1=ifelse(load_data=="all","scaled_data_all.tsv", ifelse(load_data=="pca", "pca_data.tsv", "scaled_data_var_gene.tsv")  );
        	f2="meta_data.tsv";
        	v_data=fread(f1, header=F);
        	out_file="config.json";
        	cat('{\\n',
        	' "embeddings": [\\n',
        	'    {\\n',
        	'      "tensorName": "', project, '",\\n',
        	'      "tensorShape": [\\n',
        	'        ', nrow(v_data), ',\\n',
        	'        ', ncol(v_data), '\\n',
        	'      ],\\n',
        	'      "tensorPath": "', URL, f1, '",\\n',
        	'      "metadataPath": "', URL, f2, '"\\n',
        	'    }\\n',
        	'  ]\\n',
        	'}\\n', sep="", file=out_file);
        }

        #re-create stats file
        cat("All_Genes\\t", nrow(exp_data), "\\n", sep="", file="Stats.tsv");
        cat("Filtered_Genes\\t", nrow(scaled_data), "\\n", sep="", file="Stats.tsv", append=T);
        cat("All_Cells\\t", ncol(exp_data), "\\n", sep="", file="Stats.tsv", append=T);
        cat("Filtered_Cells\\t", ncol(scaled_data), "\\n", sep="", file="Stats.tsv", append=T);
        cat("Variable_Genes\\t", length(sc_data@var.genes), "\\n", sep="", file="Stats.tsv", append=T);
        cat("Clusters\\t", length(unique(sc_data@ident)), "\\n", sep="", file="Stats.tsv", append=T);

        cat(tensor_URL, URL, out_file, "\\n", sep="", file="Tensor_URL.txt");
        #this is the URL to load tensorBoard with the data. Use in iFrame

RSCRIPT_CONTENT;

    file_put_contents("$current_dir/rscript.R", $Rscript);

    $command = "nohup nice -n 19 /usr/bin/Rscript $current_dir/rscript.R > $current_dir/rscript.Rout 2>&1 " . ' & echo $!; ';

    exec($command, $output);

    $process_id = (int)$output[0];

    echo '<h3><i class="fas fa-check-square text-success"></i> Message</h3><div class="text-muted">The data files are being processed. Pleae wait ... </div><div class="my-3 text-danger" id="processing_time5" project="' . $project . '" process_id="' . $process_id . '" value="0"></div>';

    exit();

}




else if (isset($_GET['action']) && $_GET['action'] == 'get_data_status') {

    $current_dir = $project_dir . $_GET['project'];

    if(! file_exists($current_dir)){
        echo '<h2 class="text-danger my-3">Error: the folder is not found.</h2> ';
        exit();
    }

    $command = 'ps -p ' . intval($_GET['process_id']);
    exec($command, $output);

    // Still running
    if (isset($output[1]) ){
        // Nothing to do: just wait
    }
    // Process is completed
    else {

        if( file_exists("$current_dir/Tensor_URL.txt") && filesize("$current_dir/Tensor_URL.txt") > 0 ){
            echo "view_iframe.php?project=" . $_GET['project'];
        }
        else {
            echo '<h2 class="text-danger my-3">Error: the R script failed.</h2> <pre>' . file_get_contents("$current_dir/rscript.Rout") . '</pre>';
            exit();
        }
    }

    exit();
}




else if (isset($_GET['action']) && $_GET['action'] == 'identify_abundant_genes') {

    $time = microtime(true);

    $current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
    $current_project_url = $project_url . urlencode($_GET['project']) . '/';

    $current_cache_dir = "{$current_project_dir}cache/{$time}";
    $current_cache_url = "{$current_project_url}cache/{$time}";
    if(! file_exists($current_cache_dir) ) mkdir($current_cache_dir, 0775, true);

    file_put_contents("$current_cache_dir/parameters.txt", serialize($_POST) );


    exec("ln -s {$current_project_dir}raw_exp_data.gz {$current_cache_dir}/raw_exp_data.gz");
    exec("ln -s {$current_project_dir}sample_info.csv {$current_cache_dir}/sample_info.csv");
    exec("ln -s {$current_project_dir}meta_data.tsv {$current_cache_dir}/meta_data.tsv");

    $Rscript = <<< RSCRIPT_CONTENT

        options(stringsAsFactors=F);

        library(dplyr);
        library(stringr);
        library(ggplot2);
        library(cowplot);
        theme_set(theme_cowplot());
        library(data.table);

        setwd('{$current_cache_dir}');

        gene_annot_file="$gene_annot_file";
        group_by="{$_POST['group_by']}";
        top_N={$_POST['top_N']};
        output_dir="$current_cache_dir/";


        file=str_c("zcat raw_exp_data.gz");
        raw_data=fread(cmd=file);
        head(raw_data);

        if (sum(is.na(raw_data\$V4))==nrow(raw_data) ) {
            cat("No count data, using expression values instead!\\n")
            cat("No count data, using expression values instead!\\n", file="NoCountData.txt")
            system.time(count_data<-dcast(raw_data, V2~V1, value.var="V3", fun.aggregate = sum) );
        } else {
            system.time(count_data<-dcast(raw_data, V2~V1, value.var="V4", fun.aggregate = sum) ); #V
        }


        mdata=as.matrix(count_data[, 2:ncol(count_data)]);
        rownames(mdata)=count_data\$V2;

        sel=(rowSums(mdata)>0);
        cat(nrow(mdata), "remove no exp", sum(sel), "\\n");
        mdata=mdata[sel, ];

        gene_annot<-fread(cmd=str_c("zcat ", gene_annot_file), sep="\\t", header=T);
        gene_annot<-gene_annot%>%mutate(Symbol_Unique=ifelse(duplicated(GeneName), str_c(GeneName, "_", GeneIndex), GeneName) );
        sel=match(rownames(mdata), gene_annot\$GeneIndex);
        rownames(mdata)=gene_annot\$GeneID[sel];

        #load sample information, replace sampleIndex with SampleID
        sample_info=read.csv("sample_info.csv");
        sel=match(colnames(mdata), sample_info\$SampleIndex); sum(is.na(sel));
        colnames(mdata)=sample_info\$SampleID[sel];

        #change to percentage of total counts
        total_counts=colSums(mdata);
        mdata=t(t(mdata)/total_counts);

        #now load group (meta data);
        meta_data=data.frame(fread("meta_data.tsv"));

        if (nrow(meta_data)!=ncol(mdata) ) {
            stop("Meta data row number does not match raw data sample number.");
        }
        new_order=match(meta_data\$SampleID, colnames(mdata));
        if (sum(is.na(new_order))>0) {
            stop("Meta data sample IDs do not match raw data sample IDs.");
        }

        meta_data=meta_data[new_order, ];
        sel=which(names(meta_data)==group_by);
        if (is.na(sel)) {stop("Column header not found in meta data file!")}
        types=meta_data[, group_by];
        types[is.na(types)]="Unknown";
        types[types==""]="Unknown";
        type_stat=data.frame(table(types));
        type_stat\$ID=rownames(type_stat);
        f1=str_c(output_dir, "Type_Stat.csv");
        write.csv(type_stat, f1, row.names=F);

        graph_height=max(8, top_N/50*8);
        #now loop through each type and output stat and graph
        for (i in 1:nrow(type_stat) ) {
        	sel_t=(types==type_stat\$types[i]);
        	subdata=mdata[, meta_data\$SampleID[sel_t]]*100; #percentage
        	Mean_Perc=rowMeans(subdata);
        	Mean_data=data.frame(Gene=rownames(subdata), Mean_Perc)%>%arrange(desc(Mean_Perc));
        	f1=str_c(output_dir, "Group_", i, "_Genes_Mean_Perc.csv");
        	write.csv(Mean_data, f1, row.names=F);

            top_sum=round(sum(Mean_data\$Mean_Perc[1:top_N])*100)/100;
        	top_data=subdata[Mean_data\$Gene[1:top_N], ];

        	f2=str_c(output_dir, "Group_", i, "_Top", top_N, "_Genes_Perc.csv");
        	write.csv(top_data, f2, row.names=T);

            plot_data=melt(top_data);
        	plot_data\$Var1=factor(plot_data\$Var1, levels=rev(rownames(top_data)) );

        	p=ggplot(plot_data, aes(x=value, y=Var1)) +geom_point(shape=3, size=1.5, alpha=0.2, color="chocolate4")+
        		labs(x ="% of total counts" , y = "Gene", title=str_c(type_stat\$types[i], "\\n", "Top ", top_N, " account for ", top_sum, "%") )+
        		theme(axis.text=element_text(size=7), axis.title=element_text(size=10))+
        		geom_point(data=Mean_data[1:top_N, ], mapping=aes(x=Mean_Perc, y=Gene), shape=1);

            f1=str_c(output_dir, "Group_", i, "_plot.pdf");
        	ggsave(file=f1, plot=p, height=graph_height, width=4);
        	f1=str_c(output_dir, "Group_", i, "_plot.png");
        	ggsave(file=f1, plot=p, height=graph_height, width=4);
        }

RSCRIPT_CONTENT;


    file_put_contents("$current_cache_dir/rscript.R", $Rscript);

    $command = "nohup nice -n 19 /usr/bin/Rscript $current_cache_dir/rscript.R > $current_cache_dir/rscript.Rout 2>&1 " . ' & echo $!; ';

    exec($command, $output);

    $process_id = (int)$output[0];

    echo '<h3><i class="fas fa-check-square text-success"></i> Message</h3><div class="text-muted">The data files are being processed. Pleae wait ... </div><div class="my-3 text-danger" id="processing_time5" project="' . $_GET['project'] . '" time="' . $time . '" process_id="' . $process_id . '" top_N="' . $_POST['top_N'] . '" value="0"></div>';

    exit();

}


else if (isset($_GET['action']) && $_GET['action'] == 'identify_abundant_genes_status') {

    $current_project_dir = $project_dir . $_GET['project'] . '/';
    $current_project_url = $project_url . $_GET['project'] . '/';

    $current_cache_dir = "{$current_project_dir}cache/" . $_GET['time'];
    $current_cache_url = "{$current_project_url}cache/" . $_GET['time'];

    if(! file_exists($current_cache_dir)){
        echo '<h2 class="text-danger my-3">Error: the folder is not found.</h2> ';
        exit();
    }

    $command = 'ps -p ' . intval($_GET['process_id']);
    exec($command, $output);

    // Still running
    if (isset($output[1]) ){
        // Nothing to do: just wait
    }
    // Process is completed
    else {

        if( file_exists("$current_cache_dir/Type_Stat.csv") && filesize("$current_cache_dir/Type_Stat.csv") > 0 ){

            $Type_Stat_info = array();
            $file = "$current_cache_dir/Type_Stat.csv";
            if (($handle = fopen($file, "r")) !== FALSE) {
                $head = fgetcsv($handle);
                while (($data = fgetcsv($handle)) !== FALSE) {
                    foreach($head as $i=>$h){
                        $Type_Stat_info[ $data[2] ][ 'types' ] = $data[0];
                        $Type_Stat_info[ $data[2] ][ 'Freq'  ] = $data[1];
                    }
                }
                fclose($handle);
            }

            echo '<h2 class="text-success my-3">Data analysis has finished successfully.</h2> ';

            if( file_exists("$current_cache_dir/NoCountData.txt") ){
                echo '<h2 class="text-danger my-3">Warning:  there is no count data in the project, expression values are used to compute top genes instead.</h2> ';
            }

            echo "<div class='row'>";

            foreach($Type_Stat_info as $i=>$vals){
                if(! file_exists("$current_cache_dir/Group_{$i}_plot.png") ) continue;

                echo "<div class='col-4'>";
                    echo "<h1 class='my-3 text-center'>" . $vals['types'] . " (" . $vals['Freq'] . " cells)</h1>";
                    echo "<div class='my-3 text-center'><a class='lead' href='$current_cache_url/Group_{$i}_Genes_Mean_Perc.csv' target='_blank'>Mean Percentage</a> | <a class='lead' href='$current_cache_url/Group_{$i}_Top{$_GET['top_N']}_Genes_Perc.csv' target='_blank'>Top {$_GET['top_N']} Genes</a></div>";
                    echo "<div class='my-3 text-center'><a class='' href='$current_cache_url/Group_{$i}_plot.pdf' target='_blank'><img class='img-fluid' src='$current_cache_url/Group_{$i}_plot.png' /></a></div>";
                echo "</div>";
            }
            echo "</div>";
        }
        else {
            echo '<h2 class="text-danger my-3">Error: the R script failed.</h2> <pre>' . file_get_contents("$current_cache_dir/rscript.Rout") . '</pre>';
            exit();
        }
    }

    exit();
}



?>