<?php
include_once(dirname(__DIR__) . "/config.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
	<div id="bxaf_page_content" class="row no-gutters h-100">
        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
			<div id="bxaf_page_right_content" class="w-100 p-2">


				<h1 class="my-3 w-100">BxGenomics Tools</h1>

				<div class="my-3 w-100">
					<ul class="lead">

						<li class=""><a href="tool_pathway/reactome.php" target="_blank">Reactome Pathways</a> <i class="fas fa-check text-success"></i></li>
						<li class=""><a href="tool_pathway/kegg.php" target="_blank">KEGG Pathways</a> <i class="fas fa-check text-success"></i></li>
						<li class="mb-3"><a href="tool_pathway/index.php" target="_blank">WikiPathways</a> <i class="fas fa-check text-success"></i></li>

						<li><a href="tool_data_view/index.php" target="_blank">New RNA-Seq Visualization</a> <i class="fas fa-check text-success"></i> (Select samples from project GSE100070)</li>
						<li><a href="tool_data_view/list.php" target="_blank">List of RNA-Seq Visualizations</a> <i class="fas fa-check text-success"></i>
							<ul>
								<li><span>Identify Top Abundant Genes For Cell Groups</span> <i class="fas fa-check text-success"></i></li>
								<li><span>Annotated Heatmap Tool</span> <i class="fas fa-check text-success"></i></li>
							</ul>
						</li>

						<li class="mb-3"><a href="tool_data_view/annotated_heatmap_standalone.php" target="_blank">Annotated Heatmap Tool (Standalone)</a>  <i class="fas fa-check text-success"></i></li>

						<li><a href="summary/venn_overlap.php" target="_blank">List Overlap with Venn Diagram</a> <i class="fas fa-check text-success"></i></li>
						<li class="mb-3"><a href="summary/venn_draw.php" target="_blank">Draw Venn Diagrams with Numbers</a> <i class="fas fa-check text-success"></i></li>

						<li class="mb-3"><a href="summary/msigdb_list.php" target="_blank">Browse MSigDB Terms</a> <i class="fas fa-check text-success"></i></li>

						<li><a href="summary/comparison_search.php" target="_blank">Search Similar Comparisons (Count Overlapped Terms)</a> <i class="fas fa-check text-success"></i></li>
						<li><a href="summary/comparison_compare.php" target="_blank">Compare Comparisons Based on GO Analysis Results (Venn Diagram)</a> <i class="fas fa-check text-success"></i></li>
						<li class="mb-3"><a href="summary/comparison_go.php" target="_blank">List GO Enrichment Records (P-Value Cutoff: 10<sup>-6</sup>)</a> <i class="fas fa-check text-success"></i></li>

						<li><a href="summary/gene_list_compare.php" target="_blank">Compare Two or Three Gene Lists (Venn Diagrams)</a> <i class="fas fa-check text-success"></i></li>
						<li class="mb-3"><a href="summary/gene_list.php" target="_blank">Browse and Search Human Gene Lists</a> <i class="fas fa-check text-success"></i></li>

						<li><a href="summary/msigdb_page.php" target="_blank">Search PAGE Results</a></li>
						<li><a href="summary/comparison_page_search.php" target="_blank">Search Similar Comparisons Based on PAGE Results</a></li>
						<li class="mb-5"><a href="summary/comparison_page_venn.php" target="_blank">Compare Comparisons Based on PAGE Results (Venn Diagrams)</a></li>

						<!-- <li><a href="summary/build_tbl_comparison_go_enrichment_internal.php" target="_blank">Process new private comparisons to the databases</a></li> -->
					</ul>
				</div>

            </div>
		    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
		</div>
	</div>
</body>
</html>