<?php

//*****************************************************************************************
// Question: Derrick Cheng (derrick@bioinforx.com)
// Last Revised: 2019-09-08
//
// This file is part of the standard upgrade. Any changes made here will be overwritten.
// Please overwrite the value in override.php
//*****************************************************************************************
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_APP_NAME'] 							= 'Visual Analytics of Omics Data';
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_TITLE'] 								= 'Visual Analytics of Omics Data';

$BXAF_CONFIG_CUSTOM['APP_DB_NAME'] 				   					= 'db_omicsview_human_v4';

$BXAF_CONFIG_CUSTOM['APP_PROFILE']					   				= 'human';
$BXAF_CONFIG_CUSTOM['APP_SPECIES']					    			= 'human';
$BXAF_CONFIG_CUSTOM['SPECIES'] 										= 'Human';

$BXAF_CONFIG_CUSTOM['HAS_TPM_DATA']									= true;
$BXAF_CONFIG_CUSTOM['HAS_GTEx_DATA']								= true;

$SHARE_DIR_NAME														= 'omicsview_share';
$BXAF_CONFIG_CUSTOM['WORK_DIR'] 				                	= "/var/www/html/{$SHARE_DIR_NAME}/work-omicsview_human_v4.0/";
$BXAF_CONFIG_CUSTOM['WORK_URL']										= "{$SHARE_DIR_NAME}/work-omicsview_human_v4.0/";

$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['ComparisonData'] 				= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/ComparisonData.txt.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['ComparisonData-Sample'] 		= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/ComparisonData.txt.Sample.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneFPKM'] 						= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/GeneFPKM.txt.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneFPKM-Sample'] 				= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/GeneFPKM.txt.Sample.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneFPKM-TPM'] 					= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/GeneFPKM-TPM.txt.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneFPKM-TPM-Sample'] 			= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/GeneFPKM-TPM.txt.Sample.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneLevelExpression'] 			= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/GeneLevelExpression.txt.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneLevelExpression-Sample'] 	= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/tabix/Human_Omics_2018Q3/GeneLevelExpression.txt.Sample.gz";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneAnnotation']			 	= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Gene_Annotation/HumanGeneAnnotation.txt";
$BXAF_CONFIG_CUSTOM['TABIX_INDEX']['GeneAnnotation.gz']			 	= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Gene_Annotation/HumanGeneAnnotation.txt.gz";

$BXAF_CONFIG_CUSTOM['GO_PATH']										= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Human_Omics/Human_Omics_GO_out_2018Q3/";
$BXAF_CONFIG_CUSTOM['GO_URL']										= "{$SHARE_DIR_NAME}/library-4.0/Human_Omics/Human_Omics_GO_out_2018Q3/";

$BXAF_CONFIG_CUSTOM['PAGE_PATH']									= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Human_Omics/PAGE_OUT_2018Q3/";
$BXAF_CONFIG_CUSTOM['PAGE_URL']										= "{$SHARE_DIR_NAME}/library-4.0/Human_Omics/PAGE_OUT_2018Q3/";

$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['GMT']			= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/R_Files/Human_msigdb.v6.2.symbols.gmt";
$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Script']		= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Functional_Enrichment/Scripts/";
$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Species']		= 'human';

$BXAF_CONFIG_CUSTOM['KEGG_Path']									= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/KEGG/Human/";

//$BXAF_CONFIG_CUSTOM['iTarget_Ensembl_URL']							= 'http://itarget.bxaf.net/target/';
//$BXAF_CONFIG_CUSTOM['iTarget_Ensembl_Annotation']					= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Gene_Annotation/Human_Target.tsv";

$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['CellType1 vs. CellType2'] 	= 'C1vsC2';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Disease vs. Normal'] 			= 'DvsN';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Disease Vs. Normal'] 			= 'DvsN';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Disease1 vs. Disease2'] 		= 'D1vsD2';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Other Comparisons'] 			= 'Others';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Resistant vs. Sensitive'] 	= 'RvsS';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Responder vs. Non-Responder'] = 'RvsNS';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Tissue1 vs. Tissue2'] 		= 'Ts1vsTs2';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Treatment vs. Control'] 		= 'TvsC';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory']['Treatment1 vs. Treatment2'] 	= 'Tr1vsTr2';

$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['CellType1']				 	= 'C1';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['CellType2']				 	= 'C2';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Disease']				 	= 'D';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Disease1']				 	= 'D1';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Disease2']				 	= 'D2';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Other Comparisons']			= 'Others';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Resistant']				 	= 'R';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Sensitive']				 	= 'S';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Responder']				 	= 'R';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Non-Responder']				= 'NR';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Normal']				 	= 'N';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Tissue1']					= 'T1';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Tissue2']					= 'T2';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Treatment']					= 'T';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Control']					= 'C';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Treatment1']				= 'T1';
$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['ComparisonCategory_Short']['Treatment2']				= 'T2';

$BXAF_CONFIG_CUSTOM['COMPARISON_INFO']['Sequence'] = array('ComparisonCategory', 'Case_Tissue', 'Case_DiseaseState', 'ProjectName');


//*****************************************************************************************
// Internal variables, no change is needed.
//*****************************************************************************************
$BXAF_APP_SUBDIR                                        = "omicsview_human_v4.0/app/";
$BXAF_CUSTOMER_SUBDIR                                   = "omicsview_human_v4.0/bxaf_setup/OmicsView/";
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_HEADER_CUSTOM_CSS']		= "/{$BXAF_APP_SUBDIR}css/page.css";
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_HEADER_CUSTOM_JS']		= "/{$BXAF_APP_SUBDIR}js/page.js";
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_APP_LOGO_URL'] 			= "/{$BXAF_CUSTOMER_SUBDIR}Logo-105x25.png";
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_APP_URL_ICON']			= "/{$BXAF_CUSTOMER_SUBDIR}Logo-105x25.png";
$BXAF_CONFIG_CUSTOM['BXAF_LOGIN_SUCCESS'] 				= "/{$BXAF_APP_SUBDIR}gene_expressions/index.php";
//$BXAF_CONFIG_CUSTOM['POWER_USER_FILE'] 					= "/{$BXAF_CUSTOMER_SUBDIR}power_users.php";


?>
