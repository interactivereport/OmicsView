<?php

$BXAF_CONFIG['LEFT_MENU_ITEMS'] = array();

$currentMenuIndex = 0;

if (true){
	$currentCategory = 'Gene Expression Plots';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Title'] 						= 'Gene Expression Plots';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Icon'] 						= 'fas fa-sliders-h';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['expanded']					= general_should_expand_left_menu_by_default();
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Single Gene (RNA-Seq)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_gene_expression_rnaseq_single.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_gene_expression_rnaseq_single.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Multiple Genes (RNA-Seq)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_gene_expression_rnaseq_multiple.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_gene_expression_rnaseq_multiple.php';
	
	if (!$BXAF_CONFIG['HIDE_GeneLevelExpression_Tools']){
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Single Gene (Microarray)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_gene_expression_microarray_single.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_gene_expression_microarray_single.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Multiple Genes (Microarray)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_gene_expression_microarray_multiple.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_gene_expression_microarray_multiple.php';
	}
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Heatmap';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_heatmap.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_heatmap.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Correlation Tools Using Gene Expression';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_correlation_genes_samples.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_correlation_genes_samples.php';
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Export Genes and Samples';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_export_genes_samples.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_export_genes_samples.php';
	}
	
}


if (!$BXAF_CONFIG['HIDE_Comparison_Menu']){

	$currentMenuIndex++;
	$currentCategory = 'Comparison Plotting Tools';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Title'] 						= 'Comparison Plotting Tools';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Icon'] 						= 'fas fa-chart-pie';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['expanded']					= general_should_expand_left_menu_by_default();
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Bubble Plot (Single Gene)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Bubble Plot (Single Gene)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_bubble_plot_single.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Bubble Plot (Multiple Genes)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Bubble Plot (Multiple Genes)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'plot/bubble/multiple.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Volcano Plot';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Volcano Plot';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'plot/volcano/index.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'WikiPathways Visualization';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'WikiPathways Visualization';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/tool_pathway/index.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'KEGG Visualization';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_KEGG_pathway.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/tool_pathway/kegg.php';
	
	if (!$BXAF_CONFIG['Module_Disable']['Reactome Visualization']){
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Reactome Visualization';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'reactome.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/tool_pathway/reactome.php';
	}

	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Pathway Heatmap Tool';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Pathway Heatmap Tool';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_pathway_heatmap.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Correlation Tools Using Comparisons';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_correlation_genes_comparisons.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_correlation_genes_comparisons.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Export Genes and Comparisons';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_export_genes_comparisons.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_export_genes_comparisons.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Similar Comparisons (GO)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']	= 'comparison_search.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']		= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/comparison_search.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Similar Comparisons (PAGE)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']	= 'comparison_page_search.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']		= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/comparison_page_search.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Comparisons Venn Diagram (GO)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']	= 'comparison_compare.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']		= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/comparison_compare.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Comparisons Venn Diagram (PAGE)';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']	= 'comparison_page_venn.php';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']		= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/comparison_page_venn.php';
}

if (true){

	$currentCategory = 'Review';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Title'] 						= 'Review';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Icon'] 						= 'fas fa-search';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['expanded']					= general_should_expand_left_menu_by_default();
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Review Genes';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Review Genes';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_record_browse.php?Category=Gene';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Review Projects';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Review Projects';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_record_browse.php?Category=Project';
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Review Samples';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Review Samples';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_record_browse.php?Category=Sample';
	}
	
	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Review Comparisons';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Review Comparisons';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_record_browse.php?Category=Comparison';
		
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Review GO Enrichments';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'comparison_go.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/comparison_go.php';
	
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Review PAGE Results';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'msigdb_page.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/msigdb_page.php';
	}

	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Pairwise View of Samples';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Pairwise View of Samples';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_pairwise_view.php';
	}
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Functional Gene Lists';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']	= 'gene_list';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']		= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/gene_list.php';
	}

	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Project Dashboard';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Project Dashboard';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_dashboard_project.php';
	}
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Sample Dashboard';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Sample Dashboard';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_dashboard_sample.php';
	}
	
	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Comparison Dashboard';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Comparison Dashboard';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_dashboard_comparison.php';
	}

}


if (!general_guest_account_readonly()){
	$currentCategory = 'List';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Title'] 						= 'My Results';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Icon'] 						= 'fa-list-ul';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['expanded']					= general_should_expand_left_menu_by_default();
	
	
	if ($BXAF_CONFIG['APP_SWITCH']['Internal_Data']){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Internal Data';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Internal Data';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_internal_data_browse.php';
	}
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Gene Lists';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'List_Gene';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_list_browse.php?Category=Gene';
	}
	
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Project Lists';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'List_Project';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_list_browse.php?Category=Project';
	}
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Sample Lists';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'List_Sample';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_list_browse.php?Category=Sample';
	}

	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Comparison Lists';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'List_Comparison';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_list_browse.php?Category=Comparison';
	}


}


if (true){
	$currentCategory = 'Other Tools';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Title'] 						= 'Other Tools';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Icon'] 						= 'fa-list-ul';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['expanded']					= general_should_expand_left_menu_by_default();
	

	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Visualize PCA Results';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'index_genes_samples.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'plot/pca/index_genes_samples.php';
	}
	
	
	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Meta Analysis (Comparisons)';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_meta_analysis.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_meta_analysis.php';
	}

	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Meta Analysis (Gene Expression)';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'app_meta_analysis2_browse.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_meta_analysis2_browse.php';
	}
	
	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Significantly Changed Genes';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'changed_genes.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'plot/dashboard/changed_genes.php';
	}
	
	
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Venn Diagram';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'venn_overlap.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/venn_overlap.php';
	}
	
	if (true){
		$currentMenuIndex++;
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Functional Gene List Venn Diagram';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'gene_list_compare.php';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= '';
		$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'bxgenomics/summary/gene_list_compare.php';
	}

}




if (true){
	$currentCategory = 'Settings';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Title'] 						= 'Settings';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Icon'] 						= 'fas fa-cog';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['expanded']					= general_should_expand_left_menu_by_default();
	
	
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Personal Preferences';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Barcode']		= 'Personal Preferences';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= 'fas fa-sliders-h';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_APP_URL'] . 'gene_expressions/app_user_preferences.php';
	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'User Profile';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= 'fas fa-user';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_USER_PROFILE'];

	
	$currentMenuIndex++;
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Title']		= 'Sign Out';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['Icon']		= 'fas fa-sign-out-alt';
	$BXAF_CONFIG['LEFT_MENU_ITEMS'][$currentCategory]['Children'][$currentMenuIndex]['URL']			= $BXAF_CONFIG['BXAF_LOGOUT_PAGE'];
}


$BXAF_CONFIG['PAGE_MENU_ITEMS'] = array();


foreach($_SESSION['Record_Number'] as $tempKey => $tempValue){
	$tempValue = number_format($tempValue);
	$BXAF_CONFIG['PAGE_MENU_ITEMS'][] = array('Name' => "{$tempKey}s ($tempValue)", 
											  'URL' => "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_record_browse.php?Category={$tempKey}"
											  );
}


if (in_array($_SESSION['User_Info']['Email'], $BXAF_CONFIG['Admin_User_Email']) || in_array($_SESSION['User_Info']['Login_Name'], $BXAF_CONFIG['Admin_User_Email'])){
	$BXAF_CONFIG['PAGE_MENU_ITEMS'][] = array('Name' => 'Admin Tools', 
	                                          'URL' => "/{$BXAF_CONFIG['BXAF_APP_SUBDIR']}gene_expressions/admin.php");
}


?>