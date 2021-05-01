<?php

include_once("config.php");



if (isset($_GET['action']) && $_GET['action'] == 'generate_plot') {

    $gene_indexnames = category_text_to_idnames($_POST['Gene_List'], 'name', 'gene');

    if (! is_array($gene_indexnames) || count($gene_indexnames) <= 0) {
        if( $_POST['N_top_genes'] == 0 ){
            echo '<h4 class="text-danger">Error:</h4> Please choose at least one gene.';
            exit();
        }
        else $user_genes = '0';
    }
    $user_genes = '"' . implode('", "', $gene_indexnames) . '"';



    $current_project_dir = $project_dir . urlencode($_GET['project']) . '/';
    $current_project_url = $project_url . urlencode($_GET['project']) . '/';

    $time = microtime(true);
    $current_cache_dir = "{$current_project_dir}cache/{$time}";
    $current_cache_url = "{$current_project_url}cache/{$time}";
    if(! file_exists($current_cache_dir) ) mkdir($current_cache_dir, 0775, true);

    exec("ln -s {$current_project_dir}Cluster_Markers.csv {$current_cache_dir}/Cluster_Markers.csv");
    exec("ln -s {$current_project_dir}sample_info.csv {$current_cache_dir}/sample_info.csv");
    exec("ln -s {$current_project_dir}sc_data.Rdata {$current_cache_dir}/sc_data.Rdata");



    $Rscript = <<< RSCRIPT_CONTENT

options(stringsAsFactors=F);

library(dplyr);
library(stringr);
library(data.table);
library(Seurat);
library(svglite);

setwd('{$current_cache_dir}');

### user input (two new lines added Nov 2018)
t_cluster={$_POST['t_cluster']};  #T for Y, F for N based on Show tSNE Cluster option
sel_attribute="{$_POST['sel_attribute']}" #based on user selection from Display Sample Attributes
N_top_genes={$_POST['N_top_genes']}; #Number of top genes from each cluster
user_genes=c($user_genes);
file_name="Multi_tSNE_$time"; #this is used to name output files

markers=read.csv("Cluster_Markers.csv", row.names=1);
if (N_top_genes>0) {
    topN <- markers %>% group_by(cluster) %>% top_n(N_top_genes, avg_logFC);
    Genes=unique(c(topN\$gene, user_genes));
} else {
    Genes=unique(user_genes);
}

load("sc_data.Rdata");
scale_data=data.matrix(sc_data@scale.data);
In_data=(Genes %in% rownames(scale_data));
cat("Genes list", length(Genes), ", ", sum(In_data), "have matches in raw data matrix.\\n");
Genes=Genes[In_data];

if (sum(In_data)==0) {
    stop("No genes match data...Aborting");
}



library(gridExtra)
g_legend<-function(a.gplot){
    tmp <- ggplot_gtable(ggplot_build(a.gplot))
    leg <- which(sapply(tmp\$grobs, function(x) x\$name) == "guide-box")
    legend <- tmp\$grobs[[leg]]
    legend
}

p=FeaturePlot(object = sc_data, features.plot = Genes, cols.use = c("grey", "blue"), reduction.use = "tsne", do.return=T);
nCol <- 2
if (length(Genes) == 1) {nCol <- 1}
if (length(Genes) > 6) {nCol <- 3}
if (length(Genes) > 9) { nCol <- 4}

N_extra=t_cluster + (sel_attribute!="None" & sel_attribute!="none")
if (N_extra>0 & nCol<3) {nCol=3}

p1=NULL
if (t_cluster) {
	data.plot <- as.data.frame(GetDimReduction(object = sc_data, reduction.type = "tsne", slot = "cell.embeddings") )
	data.plot\$ident<-  as.factor(x = sc_data@ident)
	p0 <- ggplot(data = data.plot, mapping = aes(x = tSNE_1 , y = tSNE_2, color=ident )) + geom_point(size = 1)
	p0=p0+labs(title="Cluster", x="tSNE_1", y="tSNE_2", color="tSNE Cluster")
	legend <- g_legend(p0)
	p1=list(p0=p0+ theme(legend.position = "none"), legend)
	if (nCol==3) {p1=list(p0=p0+ theme(legend.position = "none"), legend, NULL)}
	if (nCol==4) {p1=list(p0=p0+ theme(legend.position = "none"), legend, NULL, NULL)}
}


p2=NULL
if (sel_attribute!="None" & sel_attribute!="none") {
	data.plot <- as.data.frame(GetDimReduction(object = sc_data, reduction.type = "tsne", slot = "cell.embeddings") )
	sample_info=read.csv("sample_info.csv")
	sel_cols=c("SampleID", sel_attribute)
	data.plot\$SampleID=rownames(data.plot)
	data.plot<-data.plot%>%left_join(sample_info%>%select(one_of(sel_cols)))
	p0 <- ggplot(data = data.plot, mapping = aes_string(x = "tSNE_1" , y = "tSNE_2", color=sel_attribute )) +
	    geom_point(size = 1)+labs(title=sel_attribute)
	legend <- g_legend(p0)
	p2=list(p0=p0+ theme(legend.position = "none"), legend)
	if (nCol==3) {p2=list(p0=p0+ theme(legend.position = "none"), legend, NULL)}
	if (nCol==4) {p2=list(p0=p0+ theme(legend.position = "none"), legend, NULL, NULL)}

}

nRow=ceiling(length(Genes)/nCol)+N_extra
pdf(str_c(file_name, ".pdf"), width=max(6, nCol*3), height=max(8, nRow*3));
print(plot_grid(plotlist = c(p1, p2, p), ncol = nCol) )
dev.off();

png(str_c(file_name, ".png"), width=max(6, nCol*3)*72, height=max(8, nRow*3)*72);
print(plot_grid(plotlist = c(p1, p2, p), ncol = nCol) )
dev.off();

svglite(str_c(file_name, ".svg"),width=max(6, nCol*3), height=max(8, nRow*3));
print(plot_grid(plotlist = c(p1, p2, p), ncol = nCol) )
dev.off()


RSCRIPT_CONTENT;

    file_put_contents("$current_cache_dir/rscript.R", $Rscript);

    exec("/usr/bin/Rscript $current_cache_dir/rscript.R > $current_cache_dir/rscript.Rout 2>&1");

    if(file_exists("{$current_cache_dir}/Multi_tSNE_{$time}.pdf") && filesize("{$current_cache_dir}/Multi_tSNE_{$time}.pdf") > 0){

        echo "<div class='my-3'>Download Images: <a class='btn btn-info mx-2' href='{$current_cache_url}/Multi_tSNE_{$time}.pdf' target='_blank'>PDF</a>  <a class='btn btn-info mx-2' href='{$current_cache_url}/Multi_tSNE_{$time}.png' target='_blank'>PNG</a>  <a class='btn btn-info mx-2' href='{$current_cache_url}/Multi_tSNE_{$time}.svg' target='_blank'>SVG</a></div>";

        $svg_code = '';
        if(file_exists("{$current_cache_dir}/Multi_tSNE_{$time}.svg")){
            $svg_code = file_get_contents("{$current_cache_dir}/Multi_tSNE_{$time}.svg");

            $svg_code = substr($svg_code, strpos($svg_code,"<svg") + 5);

            $svg_code = "<svg id='svg_main' style='display: inline; width: inherit; min-width: inherit; max-width: inherit; height: inherit; min-height: inherit; max-height: inherit; border: black solid 1px;' " . $svg_code;

            echo '<hr /><div class="w-100 h-100" style="display: block; min-height: 1200px;">' . $svg_code . '</div>';

        }

    }
    else {
        echo '<h4 class="text-danger my-5">Failed to process data.</h4> ';
    }


    exit();
}

?>