<?php

$BXAF_CONFIG_CUSTOM['PAGE_LOGIN_REQUIRED']	= false;

include_once("config.php");

if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}

$current_project_dir = $project_dir . $_GET['project'] . '/';
$current_project_url = $project_url . $_GET['project'] . '/';


$current_project_files = array(
	"Cluster_Markers.csv",
	"cluster_summary.csv",
	"config.json",
	"meta_data.tsv",
	"parameters.txt",
	"pca_data.tsv",
	"PCA_heatmap.pdf",
	"PCA_plot.pdf",
	"PC_Elbow_plot.pdf",
	"Process_Log.tsv",
	"raw_exp_data.gz",
	"Rplots.pdf",
	"rscript.R",
	"rscript.Rout",
	"sample_info.csv",
	"scaled_data_var_gene.tsv",
	"sc_data.Rdata",
	"Stats.tsv",
	"tabix.bash",
	"tabix.log",
	"Tensor_URL.txt",
	"Top10Genes_heatmap.pdf",
	"Top4Genes_FeaturePlot.pdf",
	"tSNE_plot.pdf",
	"variable_genes.txt",
	"Violin_Plot_nGene.pdf"
);


$post_info = unserialize( file_get_contents($current_project_dir . 'parameters.txt') );


$file_Stats = $current_project_dir . 'Stats.tsv';
$file_Stats_info = array();
$contents = explode("\n", file_get_contents($file_Stats));
foreach($contents as $row){
	$list = explode("\t", $row);
	if($list[0] != '') $file_Stats_info[$list[0]] = $list[1];
}

$file_cluster_summary = $current_project_dir . 'cluster_summary.csv';
$cluster_summary_info = array();
if (($handle = fopen($file_cluster_summary, "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
		if($data[0] != '') $cluster_summary_info[ $data[0] ] = $data[1];
    }
    fclose($handle);
}

$Process_Log_info = array();
$file = $current_project_dir . 'Process_Log.tsv';
if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
		if($data[0] != '') $Process_Log_info[ $data[0] ] = $data[1];
    }
    fclose($handle);
}




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
            		SC RNA-Seq Visualization: <?php echo urldecode($_GET['project']); ?>
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

<?php if($projects_info[ $_GET['project'] ]['Owner'] == $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] ){ ?>
				<div class="my-3">
					<a class="mx-2 btn btn-sm btn-danger" href="edit_meta.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Update Meta Data </a>
					<a class="mx-2 btn btn-sm btn-danger btn_delete_project" href="Javascript: void(0);" project="<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Delete Project </a>
            	</div>
<?php } ?>

				<h3 class="my-3">Summary of Results:</h3>
				<div class="my-3">
					<p>
						From <strong><?php echo $file_Stats_info['All_Genes']; ?></strong> Genes, <strong><?php echo $file_Stats_info['Filtered_Genes']; ?></strong> passed gene filter. From <strong><?php echo $file_Stats_info['All_Cells']; ?></strong> cells, <strong><?php echo $file_Stats_info['Filtered_Cells']; ?></strong> passed filter.

						<a target="_blank" class="" href="<?php echo $current_project_url . 'Violin_Plot_nGene.pdf'; ?>"> View violin plot (PDF) of number of genes per cell</a>.
					</p>

					<p>
						Total of <strong><?php echo $file_Stats_info['Variable_Genes']; ?></strong> <a target="_blank" class="" href="<?php echo $current_project_url . 'variable_genes.txt'; ?>"> variable genes </a> were used for PCA and cluster analysis.
						<?php if($Process_Log_info['FindVariableGenes'] != 'AUTO') echo " The variable genes were identified with manual method."; ?>
						<?php if($Process_Log_info['PCA'] != 'PASS') echo " PCA analysis failed. No further results from Seurat are available. "; ?>
					</p>

<?php if($Process_Log_info['PCA'] == 'PASS'){ ?>

<?php if($Process_Log_info['FindClusters'] == 'PASS'){ ?>
					<p>
						From cluster analysis, <strong><?php echo $file_Stats_info['Clusters']; ?></strong> cell clusters were identified. The number of cell in each cluster is shown below.
					</p>

					<?php
						unset($cluster_summary_info['Var1']);
						echo "<table class='table table-bordered table-striped w-50 my-3'>";
						echo "<thead><tr class='table-info'><th>Cluster</th><td>" . implode("</td><td>", array_keys($cluster_summary_info)). "</td></tr></thead>";
						echo "<tbody><tr><th>Number of Cells</th><td>" . implode("</td><td>", array_values($cluster_summary_info)). "</td></tr></tbody>";
						echo "</table>";
					?>
<?php } // if($Process_Log_info['FindClusters'] == 'PASS'){ ?>

					<ul>
						<li>
							See
							<?php if($Process_Log_info['tSNE'] == 'PASS') echo "<a target='_blank' class='' href='{$current_project_url}tSNE_plot.pdf'> 2-D tSNE plot </a> and "; ?>
							<a target="_blank" class="" href="<?php echo $current_project_url . 'PCA_plot.pdf'; ?>"> PCA plot </a> of the cells and clusters.
						</li>

<?php if($Process_Log_info['FindClusters'] == 'PASS'){ ?>
						<li>
							See <a target="_blank" class="" href="<?php echo $current_project_url . 'Cluster_Markers.csv'; ?>"> this file </a> for the markers for each cluster.
							Here markers are defined as genes which are up-regulated in the cluster vs. all other cells.
							Also see <a target="_blank" class="" href="<?php echo $current_project_url . 'Top10Genes_heatmap.pdf'; ?>"> heatmap of top 10 markers </a> in each cluster

							<?php if($Process_Log_info['tSNE'] == 'PASS') echo ", and <a target='_blank' class='' href='{$current_project_url}Top4Genes_FeaturePlot.pdf'> gene expression feature plot </a> of top 4 markers in each cluster"; ?>
							.
						</li>
<?php } // if($Process_Log_info['FindClusters'] == 'PASS'){ ?>

						<li>
							See <a target="_blank" class="" href="<?php echo $current_project_url . 'meta_data.tsv'; ?>"> this file </a> for the meta data for the samples, including cluster information, selected sample attributes, number of genes in each cell,.and log2 expression of selected genes.
						</li>
					</ul>
<?php } // if($Process_Log_info['PCA'] == 'PASS'){ ?>

					<div class="my-3">
						<a href="Javascript: void(0);" onclick="if( $('#div_parameters').hasClass('hidden') ) $('#div_parameters').removeClass('hidden'); else  $('#div_parameters').addClass('hidden');"> <i class="fas fa-angle-double-right"></i> Other analysis settings </a>
						<div class="hidden" id="div_parameters">

							<table class="table table-hover mt-4">
								<?php foreach($post_info as $k=>$v){
									if(($k== 'Sample_List' || $k== 'Gene_List') && $v != ''){
										$list = preg_split("/[\s,]+/", $v, NULL, PREG_SPLIT_NO_EMPTY);
										$v = implode(", ", $list);
									}
									else if($k== 'attributes_Sample' && $v != ''){
										$v = implode(", ", $v);
									}
									$k = str_replace('_', ' ', $k);
									echo "<tr><th class='text-nowrap'>$k</th><td>$v</td></tr>";
								} ?>
							</table>

						</div>
					</div>

				</div>

            </div>


            <div class="my-3 p-3" id="div_results"></div>
    		<div class="my-3" id="div_debug"></div>

		</div>
		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>


<script>

$(document).ready(function() {

	$(document).on('click', '.btn_delete_project', function() {
		var project = $(this).attr('project');

		bootbox.confirm({
		    message: "Are you sure you want to delete this visualization?",
		    buttons: {
		        confirm: {
		            label: 'Yes',
		            className: 'btn-success'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn-danger'
		        }
		    },
		    callback: function (result) {
				if(result){
					$.ajax({
						type: 'GET',
						url: 'exe.php?action=delete_project&project=' + project,
						success: function(responseText){
							location.reload(true);
						}
					});
				}
		    }
		});

	});

});

</script>

</body>
</html>