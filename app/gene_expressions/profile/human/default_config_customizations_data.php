<?php

//*********************
// Data Info
//*********************


$currentTable = 'ComparisonData';
$APP_CONFIG['DB_Dictionary'][$currentTable]['id'] 			= 'ComparisonData';
$APP_CONFIG['DB_Dictionary'][$currentTable]['Title'] 		= 'Comparison Data';
$APP_CONFIG['DB_Dictionary'][$currentTable]['File_Example'] = 'ComparisonData.txt';
$APP_CONFIG['DB_Dictionary'][$currentTable]['Empty_Table']	= 1;
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Name']['Data_Type'] 				= 'VARCHAR(32)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Name']['Title'] 					= 'Name';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Log2FoldChange']['Data_Type'] 		= 'VARCHAR(16)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Log2FoldChange']['Title'] 			= 'Log2 Fold Change';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['PValue']['Data_Type'] 				= 'VARCHAR(16)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['PValue']['Title'] 					= 'p-value';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['AdjustedPValue']['Data_Type'] 		= 'VARCHAR(16)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['AdjustedPValue']['Title'] 			= 'Adjusted p-value';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['NumeratorValue']['Data_Type'] 		= 'VARCHAR(16)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['NumeratorValue']['Title'] 			= 'Numerator Value';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['DenominatorValue']['Data_Type'] 	= 'VARCHAR(16)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['DenominatorValue']['Title'] 		= 'Denominator Value';



$currentTable = 'Comparisons';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'Comparisons';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Comparisons';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example']	= 'Comparisons.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['HideFromOption']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['HideFromSearch']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['Title'] 			= 'Comparison Index';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonIndex']['NaturalSort']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonID']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonID']['Title'] 				= 'Comparison ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonID']['HideFromUpdate']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['Title'] 				= 'Project Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['HTML']				= 'reviewRecordByName';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['Target_Category']		= 'Project';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['HideFromUpdate']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformName']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformName']['Title'] 				= 'Platform Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformName']['Title_Short'] 		= 'Platform Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleDataMode']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleDataMode']['Title'] 			= 'Sample Data Mode';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonCategory']['Title'] 		= 'Comparison Category';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonCategory']['Title_Short'] 	= 'Comparison Category';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonContrast']['Title'] 		= 'Comparison Contrast';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonType']['Title'] 			= 'Comparison Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ID']['HideFromOption']				= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ID']['HideFromSearch']				= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_DiseaseState']['Title']			= 'Case Disease State';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_DiseaseState']['Title_Short'] 	= 'Disease State';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_CellType']['Title']				= 'Case Cell Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_CellType']['Title_Short'] 		= 'Cell Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_Treatment']['Title']			= 'Case Treatment';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_Treatment']['Title_Short'] 		= 'Treatment';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneModelID']['Title']				= 'Gene Model ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_SampleIDs']['Title']			= 'Case Sample IDs';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_SampleID_Count']['Title']		= '# of Samples (Case)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Control_SampleIDs']['Title']			= 'Control Sample IDs';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Control_SampleID_Count']['Title']	= '# of Samples (Control)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleID_Count']['Title']			= '# of Samples';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_IgM_MC']['Title']				= 'Case IgM MC';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Control_IgM_MC']['Title']			= 'Control IgM MC';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonType']['Title']			= 'Comparison Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Case_SampleID_Count']['HideFromUpdate']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Control_SampleID_Count']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleID_Count']['HideFromUpdate']			= 1;



/*
$currentTable = 'ExonJunctionAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'ExonJunctionAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Exon Junction Annotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'ExonJunctionAnnotation.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ExonJunctionIndex']['Data_Type'] 	= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ComparisonID']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Strand']['Data_Type'] 				= 'VARCHAR(4)';



$currentTable = 'ExonJunctionData';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'ExonJunctionData';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Exon Junction Data';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'ExonJunctionData.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ExonJunctionIndex']['Data_Type'] 	= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type']			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ReadCount']['Data_Type'] 			= 'VARCHAR(256)';



$currentTable = 'FusionAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'FusionAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Fusion Annotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'FusionAnnotation.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['FusionIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['FusionID']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Strand']['Data_Type'] 				= 'VARCHAR(4)';



$currentTable = 'FusionData';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'FusionData';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Fusion Data';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example']	= 'FusionData.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['FusionIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SeedCount']['Data_Type'] 			= 'VARCHAR(16)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['RescuedCount']['Data_Type'] 		= 'VARCHAR(16)';
*/


$currentTable = 'GeneAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'GeneAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Gene Annotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'GeneAnnotation.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Title'] 				= 'Gene Index';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['HideFromOption']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['HideFromSearch']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneID']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['EntrezID']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Title'] 					= 'Gene Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptNumber']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Strand']['Data_Type'] 				= 'VARCHAR(4)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Chromosome']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Start']['Data_Type'] 				= 'VARCHAR(16)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['End']['Data_Type'] 					= 'VARCHAR(16)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ExonLength']['Data_Type'] 			= 'VARCHAR(16)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Source']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL_Extra'][] = 'UNIQUE KEY `GeneIndex_Unique` (`GeneIndex`)';



$currentTable = 'GeneCombined';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'GeneCombined';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Gene';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'GeneCombined.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Skip_Creation'] 	= true;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Title'] 				= 'Gene Index';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['HideFromOption']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['NaturalSort']			= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['HideFromSearch']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneID']['Title'] 					= 'Gene ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['EntrezID']['Title'] 					= 'Entrez ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['EntrezID']['NaturalSort'] 			= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Title'] 					= 'Gene Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptNumber']['Title'] 			= 'Transcript Number';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptNumber']['NaturalSort'] 	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Strand']['Title'] 					= 'Strand';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Chromosome']['Title'] 				= 'Chromosome';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Start']['Title'] 					= 'Start';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Start']['NaturalSort'] 				= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['End']['Title'] 						= 'End';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['End']['NaturalSort'] 				= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ExonLength']['Title'] 				= 'Exon Length';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ExonLength']['NaturalSort'] 			= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Source']['Title'] 					= 'Source';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Description']['Title'] 				= 'Description';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Alias']['Title'] 					= 'Alias';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Ensembl']['Title'] 					= 'Ensembl';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Unigene']['Title'] 					= 'UniGene';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Uniprot']['Title'] 					= 'UniProt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['AccNum']['Title'] 					= 'AccNum';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Biotype']['Title'] 					= 'Biotype';



$currentTable = 'GeneFPKM';
$APP_CONFIG['DB_Dictionary'][$currentTable]['id'] 			= 'GeneFPKM';
$APP_CONFIG['DB_Dictionary'][$currentTable]['Title'] 		= 'Gene FPKM';
$APP_CONFIG['DB_Dictionary'][$currentTable]['File_Example'] = 'GeneFPKM.txt';
$APP_CONFIG['DB_Dictionary'][$currentTable]['Empty_Table']	= 1;
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['FPKM']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['FPKM']['Title']	 					= 'Gene FPKM';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Count']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Count']['Title']	 				= 'Gene Count';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL_Extra'][] = 'UNIQUE KEY `SampleIndex_GeneIndex` (`SampleIndex`,`GeneIndex`)';



$currentTable = 'GeneLevelExpression';
$APP_CONFIG['DB_Dictionary'][$currentTable]['id'] 			= 'GeneLevelExpression';
$APP_CONFIG['DB_Dictionary'][$currentTable]['Title'] 		= 'Gene Level Expression';
$APP_CONFIG['DB_Dictionary'][$currentTable]['File_Example'] = 'GeneLevelExpression.txt';
$APP_CONFIG['DB_Dictionary'][$currentTable]['Empty_Table']	= 1;
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Value']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL']['Value']['Title']	 				= 'Gene Level Expression';
$APP_CONFIG['DB_Dictionary'][$currentTable]['SQL_Extra'][] = 'UNIQUE KEY `SampleIndex_GeneIndex` (`SampleIndex`,`GeneIndex`)';


/*
$currentTable = 'Mutations';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'Mutations';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Mutations';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'Mutations.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['MutationIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneID']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptID']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptName']['Data_Type']		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptStrand']['Data_Type'] 	= 'VARCHAR(4)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Cosmic_ID']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Cosmic_Gene']['Data_Type']			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Cosmic_Strand']['Data_Type'] 		= 'VARCHAR(4)';



$currentTable = 'Mutations_Canonical';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'Mutations_Canonical';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Mutations Canonical';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'Mutations_Canonical.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['MutationIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneID']['Data_Type'] 				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Data_Type'] 			= 'VARCHAR(256)';



$currentTable = 'ProbeLevelExpression';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'ProbeLevelExpression';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Probe Level Expression';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'ProbeLevelExpression.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProbeName']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Expression']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['DetectionPValue']['Data_Type'] 	= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Percentile']['Data_Type'] 		= 'VARCHAR(256)';
*/


$currentTable = 'Projects';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'Projects';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Projects';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'Projects.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ID']['HideFromOption']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ID']['HideFromSearch']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectIndex']['NaturalSort']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectIndex']['Title']			= 'Project Index';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectIndex']['HideFromOption']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectIndex']['HideFromSearch']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectIndex']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectID']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectID']['Title'] 			= 'Project ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectID']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Platform'] ['Data_Type']			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Platform']['Title']				= 'Platform';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Title']['Data_Type']				= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformProvider']['Title']		= 'Platform Provider';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformOrganism']['Title']		= 'Platform Organism';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformDescription']['Title']	= 'Platform Description';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformTechnology']['Title']	= 'Platform Technology';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ContactOrganization']['Title']	= 'Contact Organization';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectCollection']['Title']		= 'Project Collection';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ExperimentType']['Title']		= 'Experiment Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['LandPlatforms']['Title']			= 'Land Platforms';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['WebLink']['Title']				= 'URL';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PubMed_Authors']['Title']		= 'PubMed Authors';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PubMed']['Title']				= 'PubMed';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ContactWebLink']['Title']		= 'Contact URL';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['BioProject']['Title']			= 'BioProject';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TherapeuticArea']['Title']		= 'Therapeutic Area';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['StudyType']['Title']				= 'Study Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Disease']['Title']				= 'Disease';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformType']['Title']			= 'Platform Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformProvider']['Title']		= 'Platform Provider';



/*
$currentTable = 'RnaSeqMutation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'RnaSeqMutation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'RNASeq Mutation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'RnaSeqMutation.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['MutationIndex']['Data_Type'] 	= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneIndex']['Data_Type'] 		= 'INTEGER(11)';
*/


$currentTable = 'Samples';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'Samples';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Samples';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'Samples.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ID']['HideFromOption']			= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ID']['HideFromSearch']			= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Title'] 			= 'Sample Index';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['HideFromOption']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['HideFromSearch']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['NaturalSort']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleID']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleID']['Title'] 				= 'Sample ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleID']['HideFromOption']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleID']['HideFromUpdate']		= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['Title'] 			= 'Project Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['HTML']			= 'reviewRecordByName';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['Target_Category']	= 'Project';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['ProjectName']['HideFromUpdate']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformGPL']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformGPL']['Title'] 			= 'Platform (GPL)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformName']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['PlatformName']['Title'] 			= 'Platform Name';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['BamFileName']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['BamFileName']['HideFromOption']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['BamFileName']['HideFromSearch']	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['CellType']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['CellType']['Title'] 				= 'Cell Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Collection']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Collection']['Title'] 			= 'Collection';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Description']['Data_Type'] 		= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['DiseaseStage']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['DiseaseStage']['Title'] 			= 'Disease Stage';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['DiseaseCategory']['Title'] 		= 'Disease Category';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['DiseaseState']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['DiseaseState']['Title'] 			= 'Disease State';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIntegrationID']['Data_Type'] 	= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIntegrationID']['Title'] 		= 'Sample Integration ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Ethnicity']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Ethnicity']['Title'] 			= 'Ethnicity';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Gender']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Gender']['Title']	 			= 'Gender';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Infection']['Data_Type'] 		= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Organism']['Data_Type'] 			= 'VARCHAR(256)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Response']['Data_Type'] 			= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SamplePathology']['Data_Type'] 	= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SamplePathology']['Title'] 		= 'Sample Pathology';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleSource']['Data_Type'] 		= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleSource']['Title'] 			= 'Sample Source';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleType']['Data_Type'] 		= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleType']['Title'] 			= 'Sample Type';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SamplingTime']['Data_Type'] 		= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SamplingTime']['Title'] 			= 'Sampling Time';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Symptom']['Data_Type'] 			= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TissueCategory']['Title'] 		= 'Tissue Category';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Tissue']['Data_Type'] 			= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Title']['Data_Type'] 			= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Transfection']['Data_Type'] 		= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Treatment'] ['Data_Type']		= 'TEXT';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SubjectID']['Title'] 			= 'Subject ID';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TPM Scaling Factor']['HideFromOption'] 	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TPM Scaling Factor']['HideFromSearch'] 	= 1;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TPM Scaling Factor']['HideFromUpdate'] 	= 1;

$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL_Extra'][] = 'UNIQUE KEY `SampleIndex_Unique` (`SampleIndex`)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL_Extra'][] = 'UNIQUE KEY `SampleID_Unique` (`SampleID`)';


/*
$currentTable = 'TranscriptAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'TranscriptAnnotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Transcript Annotation';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'TranscriptAnnotation.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptIndex']['Data_Type'] 	= 'INTEGER(11)';



$currentTable = 'TranscriptFPKM';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'TranscriptFPKM';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Transcript FPKM';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'TranscriptFPKM.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['SampleIndex']['Data_Type'] 		= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['TranscriptIndex']['Data_Type'] 	= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['FPKM']['Data_Type'] 			= 'VARCHAR(16)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Count']['Data_Type'] 			= 'VARCHAR(16)';

*/

$currentTable = 'HumanGeneList';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'HumanGeneList';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Human Gene List';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'HumanGeneList.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneID']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Symbol']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Alias']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Ensembl']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Unigene']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Uniprot']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['AccNum']['Data_Type'] 			= 'TEXT';


/*
$currentTable = 'MouseGeneList';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['id'] 			= 'MouseGeneList';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Title'] 		= 'Mouse Gene List';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['File_Example'] = 'MouseGeneList.txt';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['Empty_Table']	= 0;
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['geneID']['Data_Type'] 			= 'INTEGER(11)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Symbol']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['alias']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['GeneName']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Ensembl']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Unigene']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['Uniprot']['Data_Type'] 			= 'VARCHAR(255)';
$APP_CONFIG_CUSTOM['DB_Dictionary'][$currentTable]['SQL']['AccNum']['Data_Type'] 			= 'TEXT';
*/


unset($currentTable);

?>