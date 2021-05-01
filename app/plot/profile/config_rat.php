<?php

	$CONFIG_PROFILE = array();
	$CONFIG_PROFILE['SPECIES'] = 'Rat';
	$CONFIG_PROFILE['WORK_DIR'] = 'plot_rat';
	
	//------------------------------------------------------------------------------
	// Bubble Plot
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['BUBBLE_PLOT']['geneset_col'] = array(
	  'StandardName' => 'Name',
	  'MemberNumber' => 'Gene Number'
	);
	$CONFIG_PROFILE['BUBBLE_PLOT']['demo_gene'] = 'Lats1';
	$CONFIG_PROFILE['BUBBLE_PLOT']['default_comparisons'] = 'E-MTAB-6081.GPL20797.DESeq2.test1\nE-MTAB-6081.GPL20797.DESeq2.test2\nE-MTAB-6081.GPL20797.DESeq2.test3\nE-MTAB-6081.GPL20797.DESeq2.test4\nE-MTAB-6081.GPL20797.DESeq2.test5';
	
	//------------------------------------------------------------------------------
	// Functional Enrichment
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['FUNCTIONAL_ENRICHMENT']['folder_name'] = 'Rat_GO_out';
	
	//------------------------------------------------------------------------------
	// PCA
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['PCA']['work_dir'] = 'plot_rat';
	
	//------------------------------------------------------------------------------
	// PVJS
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['PVJS']['pathway_dir'] = 'xxx';
	$CONFIG_PROFILE['PVJS']['demo_comparison'] = 'E-MTAB-6081.GPL20797.DESeq2.test1';
	
	//------------------------------------------------------------------------------
	// Volcano
	//------------------------------------------------------------------------------
	$CONFIG_PROFILE['VOLCANO']['demo_comparison'] = 'E-MTAB-6081.GPL20797.DESeq2.test1';
	
	$CONFIG_PROFILE['Naming-Override']['Comparison']['ComparisonID'] 		= 'Comparison ID';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_CellType'] 		= 'Cell Type';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_DiseaseState'] 	= 'Disease State';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_Treatment'] 		= 'Treatment';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['Case_SampleSource'] 	= 'Sample Source';
	$CONFIG_PROFILE['Naming-Override']['Comparison']['ComparisonContrast'] 	= 'Comparison Contrast';
	
?>