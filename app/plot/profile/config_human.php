<?php

$CONFIG_PROFILE = array();
	$CONFIG_PROFILE['SPECIES'] = 'Human';
	$CONFIG_PROFILE['WORK_DIR'] = 'plot_human';
	
	//------------------------------------------------------------------------------
	// Bubble Plot
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['BUBBLE_PLOT']['geneset_col'] = array(
	  'StandardName' => 'Name',
	  'CategoryCode' => 'Category',
	  'MemberNumber' => 'Gene Number'
	);
	$CONFIG_PROFILE['BUBBLE_PLOT']['demo_gene'] = 'WASH7P';
	$CONFIG_PROFILE['BUBBLE_PLOT']['default_comparisons'] = 'GSE16879.GPL570.test1\nGSE16879.GPL570.test12\nGSE16879.GPL570.test14\nGSE16879.GPL570.test15\nGSE52746.GPL17996.test1\nGSE52746.GPL17996.test2\nGSE52746.GPL17996.test3';
	
	//------------------------------------------------------------------------------
	// Functional Enrichment
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['FUNCTIONAL_ENRICHMENT']['folder_name'] = 'Human_GO_out';
	
	//------------------------------------------------------------------------------
	// PCA
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['PCA']['work_dir'] = 'plot';
	
	//------------------------------------------------------------------------------
	// PVJS
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['PVJS']['pathway_dir'] 		= 'homo_sapiens';
	$CONFIG_PROFILE['PVJS']['demo_comparison'] 	= 'GSE44720.GPL10558.test16';
	
	//------------------------------------------------------------------------------
	// Volcano
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['VOLCANO']['demo_comparison'] = 'GSE44720.GPL10558.test16';
	
	
	$CONFIG_PROFILE['Naming-Override']['Comparison']['ComparisonID'] 		= 'Comparison ID';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_CellType'] 		= 'Cell Type';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_DiseaseState'] 	= 'Disease State';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_Treatment'] 		= 'Treatment';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_SampleSource'] 	= 'Sample Source';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['ComparisonContrast'] 	= 'Comparison Contrast';
	

	
?>
