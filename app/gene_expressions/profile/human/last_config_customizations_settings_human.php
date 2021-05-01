<?php

if (true){
	//The available SQL column for data filter
	$currentTable = 'Samples';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['CellType']['SQL'] 			= 'CellType';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['DiseaseStage']['SQL'] 		= 'DiseaseStage';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['DiseaseState']['SQL'] 		= 'DiseaseState';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['Gender']['SQL'] 			= 'Gender';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['SampleSource']['SQL'] 		= 'SampleSource';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['Tissue']['SQL'] 			= 'Tissue';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']['PlatformName']['SQL'] 		= 'PlatformName';
	
	
	
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['DiseaseState']['SQL'] 		= 'DiseaseState';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['DiseaseState']['OpenStatus'] 	= true;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['Tissue']['SQL'] 				= 'Tissue';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['Gender']['SQL'] 				= 'Gender';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['SampleSource']['SQL'] 		= 'SampleSource';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['DiseaseStage']['SQL'] 		= 'DiseaseStage';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Filter']['PlatformName']['SQL'] 		= 'PlatformName';
	
	
	
	//The data filter preferences
	//Mode: 
	//Highest_Occurence: This will select the one with highest occurence
	//Multiple: This will select the one specified in the array
	//Column: Must be available in $APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Dropdown']
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Preselect']['Microarray']['Default']['Mode']						= 'Highest_Occurence';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Preselect']['Microarray']['Default']['Message_General']				= 'Due to systematic differences, we recommend plotting data points from one array platform at a time. The platform with the most data points is chosen by default.';
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Preselect']['Microarray']['Default']['Column']						= 'PlatformName';
		
	
	//Sample Attributes: Setting the value to 1 will precheck the box in the tool.
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['DiseaseState']['Default'] 	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Tissue']['Default'] 			= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Gender']['Default'] 			= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['CellType']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['ProjectName']['Default'] 	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Response']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['SamplingTime']['Default'] 	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['SubjectID']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Tissue']['Default'] 			= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Treatment']['Default'] 		= 1;
	/*
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['SampleSource']['Default'] 	= 0;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['DiseaseStage']['Default'] 	= 0;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['PlatformName']['Default'] 	= 0;
	*/
}

if (true){
	$currentTable = 'GeneCombined';
	//Gene Attributes: Setting the value to 1 will precheck the box in the tool.
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['GeneName']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['EntrezID']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Description']['Default'] 	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Alias']['Default'] 			= 1;
}

if (true){
	$currentTable = 'Comparisons';
	//Comparison Attributes: Setting the value to 1 will precheck the box in the tool.
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['ComparisonID']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Case_CellType']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['Case_DiseaseState']['Default'] 	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['ComparisonCategory']['Default']	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['ComparisonContrast']['Default']	= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Gene_Expression_Options']['PlatformName']['Default'] 		= 1;
}

if (true){
	$currentTable = 'GeneCombined';
	//Gene Attributes: Setting the value to 1 will precheck the box in the tool.
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Meta_Analysis_Options']['GeneName']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Meta_Analysis_Options']['EntrezID']['Default'] 		= 1;
	$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Meta_Analysis_Options']['Description']['Default'] 	= 1;
}

if (true){
	$APP_CONFIG['APP']['RNA_Seq']['Single_Example_Message'] 			= 'Please enter a gene name, e.g., CREB1';
	$APP_CONFIG['APP']['RNA_Seq']['Multiple_Example_Message'] 			= 'Please enter one or more gene names, seperated by line break.';
	
	$APP_CONFIG['APP']['Microarray']['Single_Example_Message'] 			= 'Please enter a gene name, e.g., CREB1';
	$APP_CONFIG['APP']['Microarray']['Multiple_Example_Message'] 		= 'Please enter one or more gene names, seperated by line break.';
}

if (true){
	$APP_CONFIG['APP']['Module']['GTEx'] 					= false;
	$APP_CONFIG['APP']['Module']['RNA-Seq'] 				= true;
	$APP_CONFIG['APP']['Module']['Microarray'] 				= true;
	$APP_CONFIG['APP']['Module']['Comparison'] 				= true;
	$APP_CONFIG['APP']['Module']['Modified_DiseaseState'] 	= true;
	$APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory']['Disease vs. Normal'] 			= 'Disease vs. Normal';
	$APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory']['Responder vs. Non-Responder'] = 'Responder vs. Non-Responder';
	$APP_CONFIG['APP']['Module']['Modified_DiseaseState_ComparisonCategory']['Treatment vs. Control'] 		= 'Treatment vs. Control';
	
	
	$APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample'] 				= true;
	//$APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown']['ComparisonID_Type'] 	= 'ComparisonID_Type';
	$APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown']['Comparison_Type'] 		= 'Case_Control';
	$APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown']['ComparisonID'] 			= 'Comparison ID';
	$APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown']['ComparisonCategory'] 	= 'Comparison Category';
}

if (true){
	$APP_CONFIG['canvasxpress']['Data_Limit'] 			= 5000;
	$APP_CONFIG['canvasxpress']['Data_Limit_Heatmap']	= 7000;
	$APP_CONFIG['canvasxpress']['Log_Add_Value'] 		= 0.5;
	$APP_CONFIG['canvasxpress']['Range_Lower_Limit'] 	= -3;
	$APP_CONFIG['canvasxpress']['Range_Upper_Limit'] 	= 3;
}

if (true){
	$APP_CONFIG['Meta_Analysis']['Missing_Total']		= 0.3;
	$APP_CONFIG['Meta_Analysis']['LogFC_Cutoff']		= 1;
	$APP_CONFIG['Meta_Analysis']['Statistical_Type']	= 'FDR';
	$APP_CONFIG['Meta_Analysis']['Statistic_Cutoff']	= '0.05';
	
	$APP_CONFIG['Meta_Analysis2']['Gene_Annotation'] = "{$BXAF_CONFIG['SHARE_LIBRARY_DIR']}Gene_Annotation/human.csv";
}

$APP_CONFIG['APP']['Interal_Data_Read_Count'] = 5;
$APP_CONFIG['Blank_Value'] = 'No Info';
$APP_CONFIG['Default_Settings']['Research_Project_Department'] 	= $BXAF_CONFIG_CUSTOM['Research_Project_Department'];

if (true){
	$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping_Choice'] = $BXAF_CONFIG_CUSTOM['SPECIES'];
	
	if (true){
		$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']['Human']['Name'] 	= 'Human';
		$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']['Human']['Table'] = 'Gene_Lookup_Human';
	}
	
	if (true){
		$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']['Mouse']['Name'] 	= 'Mouse';
		$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']['Mouse']['Table'] = 'Gene_Lookup_Mouse';
	}
	
	if (true){
		$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']['Rat']['Name'] 	= 'Rat';
		$APP_CONFIG['Internal_Data_Settings']['Gene_Mapping']['Rat']['Table'] 	= 'Gene_Lookup_Rat';
	}
}



						
$APP_CONFIG['Dashboard']['Default'] = 'Comparison';

if (true){
	$APP_CONFIG['Dashboard']['Charts'] = array();
	
	$currentIndex = 0;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Comparison']['Table'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Comparisons']['SQL']['ComparisonCategory']['Title_Short'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Column'] = 'ComparisonCategory';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Chart'] 	= 'PieChart';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Others']	= 'Other Categories';
	
	$currentIndex = 1;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Comparison']['Table'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Comparisons']['SQL']['Case_CellType']['Title_Short'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Column'] = 'Case_CellType';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Top'] 	= 15;   //Don't forget to update the user settings
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Others']	= 'Other Cell Types';
	
	$currentIndex = 2;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Comparison']['Table'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Comparisons']['SQL']['Case_DiseaseState']['Title_Short'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Column'] = 'Case_DiseaseState';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Others']	= 'Other Disease States';
	
	$currentIndex = 3;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Comparison']['Table'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Comparisons']['SQL']['Case_Treatment']['Title_Short'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Column'] = 'Case_Treatment';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Chart'] 	= 'BarChart-Vertical';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Top'] 	= 10;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Others']	= 'Other Treatments';
	
	$currentIndex = 4;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Comparison']['Table'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Comparisons']['SQL']['PlatformName']['Title_Short'];
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Column'] = 'PlatformName';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Chart'] 	= 'PieChart';
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts'][$currentIndex]['Others']	= 'Other Platforms';
}


if (true){
	$APP_CONFIG['Dashboard']['Charts_Sample'] = array();
	
	$currentIndex = 0;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Sample']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['CellType']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Column']	= 'CellType';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Chart'] 	= 'PieChart';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Others']	= 'Other Categories';
	
	$currentIndex = 1;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Sample']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['DiseaseStage']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Column'] 	= 'DiseaseStage';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Top'] 	= 15;   //Don't forget to update the user settings
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Others']	= 'Other Disease Stages';
	
	$currentIndex = 2;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Sample']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['DiseaseState']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Column'] 	= 'DiseaseState';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Others']	= 'Other Disease States';
	
	if (true){
		$currentIndex = 3;
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Sample']['Table'];
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['Ethnicity']['Title'];
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Column'] 	= 'Ethnicity';
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Chart'] 	= 'BarChart-Vertical';
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Top'] 	= 10;
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.', 'No Info');
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Others']	= 'Other Ethnicities';
	}
	
	if (false){
		$currentIndex = 3;
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Sample']['Table'];
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['Gender']['Title'];
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Column'] 	= 'Gender';
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Chart'] 	= 'PieChart';
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Top'] 	= 10;
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.', 'No Info');
		$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Others']	= 'Other Genders';
	}
	
	$currentIndex = 4;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Sample']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Samples']['SQL']['PlatformName']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Column'] 	= 'PlatformName';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Chart'] 	= 'PieChart';
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Sample'][$currentIndex]['Others']	= 'Other Platforms';
}


if (true){
	$APP_CONFIG['Dashboard']['Charts_Project'] = array();
	
	$currentIndex = 0;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Project']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Projects']['SQL']['TherapeuticArea']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Column']	= 'TherapeuticArea';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Chart'] 	= 'PieChart';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Others']	= 'Other Categories';
	
	$currentIndex = 1;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Project']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Projects']['SQL']['StudyType']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Column'] = 'StudyType';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Chart'] 	= 'BarChart-Vertical';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Top'] 	= 10;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.', 'No Info');
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Others']	= 'Other Ethnicities';
	
	$currentIndex = 2;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Project']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Projects']['SQL']['Disease']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Column'] = 'Disease';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Others']	= 'Other Disease States';
	
	$currentIndex = 3;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Project']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Projects']['SQL']['Platform']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Column'] = 'Platform';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Top'] 	= 15;   //Don't forget to update the user settings
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Others']	= 'Other Disease Stages';
	
	$currentIndex = 4;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Table'] 	= $APP_CONFIG_CUSTOM['APP']['List_Category']['Project']['Table'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Title'] 	= $APP_CONFIG_CUSTOM['DB_Dictionary']['Projects']['SQL']['PlatformProvider']['Title'];
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Column'] = 'PlatformProvider';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Chart'] 	= 'BarChart-Horizontal';
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Top'] 	= 15;
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Unknown_Keywords']	= array('NA', 'na', 'none', '.');
	$APP_CONFIG['Dashboard']['Charts_Project'][$currentIndex]['Others']	= 'Other Platforms';
}


if (true){
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Astrocytes']['Title'] 			= 'Astrocytes';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Astrocytes']['Color'] 			= '#E41A1C';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Cardiomyocytes']['Title'] 		= 'Cardiomyocytes';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Cardiomyocytes']['Color'] 		= '#99445E';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Endothelial']['Title'] 		= 'Endothelial';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Endothelial']['Color'] 		= '#377EB8';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Hepatocytes']['Title'] 		= 'Hepatocytes';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Hepatocytes']['Color'] 		= '#49A859';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Macrophage']['Title'] 			= 'Macrophage';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Macrophage']['Color']			= '#984EA3';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Neuron']['Title'] 				= 'Neuron';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Neuron']['Color'] 				= '#D26A45';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Oligodendrocytes']['Title'] 	= 'Oligodendrocytes';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Oligodendrocytes']['Color'] 	= '#FFB515';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Pancreatic']['Title'] 			= 'Pancreatic';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Pancreatic']['Color'] 			= '#A65628';
	
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Skeletal']['Title'] 			= 'Skeletal';
	$APP_CONFIG['APP']['CellMap']['Cell_Types']['Skeletal']['Color'] 			= '#F781BF';
	
	
}

?>