<?php

	$CONFIG_PROFILE = array();
	$CONFIG_PROFILE['SPECIES'] = 'Mouse';
	$CONFIG_PROFILE['WORK_DIR'] = 'plot_mouse';
	
	//------------------------------------------------------------------------------
	// Bubble Plot
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['BUBBLE_PLOT']['geneset_col'] = array(
	  'StandardName' => 'Name',
	  'MemberNumber' => 'Gene Number'
	);
	$CONFIG_PROFILE['BUBBLE_PLOT']['demo_gene'] = 'Xkr4';
	$CONFIG_PROFILE['BUBBLE_PLOT']['default_comparisons'] = 'GSE32137.GPL7202.test1\nGSE16846.GPL339.test1\nGSE64750.GPL1261.test3\nGSE63176.GPL19146.test4\nGSE59644.GPL6885.test1\nGSE27066.GPL1261.test2';
	
	//------------------------------------------------------------------------------
	// Functional Enrichment
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['FUNCTIONAL_ENRICHMENT']['folder_name'] = 'Mouse_GO_out';
	
	//------------------------------------------------------------------------------
	// PCA
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['PCA']['work_dir'] = 'plot_mouse';
	
	//------------------------------------------------------------------------------
	// PVJS
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['PVJS']['pathway_dir'] = 'mus_musculus';
	$CONFIG_PROFILE['PVJS']['demo_comparison'] = 'GSE64750.GPL1261.test3';
	
	//------------------------------------------------------------------------------
	// Volcano
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['VOLCANO']['demo_comparison'] = 'GSE64750.GPL1261.test3';
	
	$CONFIG_PROFILE['Naming-Override']['Comparison']['ComparisonID'] 		= 'Comparison ID';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_CellType'] 		= 'Cell Type';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_DiseaseState'] 	= 'Disease State';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_Treatment'] 		= 'Treatment';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_SampleSource'] 	= 'Sample Source';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['ComparisonContrast'] 	= 'Comparison Contrast';
	
?>