<?php

$BXAF_CONFIG_CUSTOM['SORT_BIN']							= '/bin/sort --parallel=12';
$BXAF_CONFIG_CUSTOM['BXAF_DB_NAME']                     = '/var/www/html/diseaseatlas_share/users.db';



$SHARE_DIR_NAME														= 'diseaseatlas_share';
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

$BXAF_CONFIG_CUSTOM['GO_PATH']										= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Human/Human_GO_out_2018Q3/";
$BXAF_CONFIG_CUSTOM['GO_URL']										= "{$SHARE_DIR_NAME}/library-4.0/Human/Human_GO_out_2018Q3/";

$BXAF_CONFIG_CUSTOM['PAGE_PATH']									= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Human/PAGE_OUT_2018Q3/";
$BXAF_CONFIG_CUSTOM['PAGE_URL']										= "{$SHARE_DIR_NAME}/library-4.0/Human/PAGE_OUT_2018Q3/";

$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['GMT']			= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/R_Files/Human_msigdb.v6.2.symbols.gmt";
$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Script']		= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/Functional_Enrichment/Scripts/";
$BXAF_CONFIG_CUSTOM['FUNCTIONAL_ENRICHMENT_FILES']['Species']		= 'human';

$BXAF_CONFIG_CUSTOM['KEGG_Path']									= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/KEGG/Human/";

$BXAF_CONFIG_CUSTOM['SHARE_LIBRARY_DIR']							= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/";
$BXAF_CONFIG_CUSTOM['KEGG']['Script']				 				= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/KEGG/Scripts/";

$BXAF_CONFIG_CUSTOM['WIKIPATHWAY_GPML_PATH']						= "/var/www/html/{$SHARE_DIR_NAME}/library-4.0/wikipathways/current/gpml/";


$BXAF_CONFIG_CUSTOM['BGZIP_DIR'] = '/usr/local/bin/bgzip';
$BXAF_CONFIG_CUSTOM['TABIX_BIN'] = '/usr/local/bin/tabix';

$BXAF_CONFIG_CUSTOM['Admin_User_Email']					= array('info@bioinforx.com', 'baohong.zhang@biogen.com', 'derrick.cheng@biogen.com', 'xinmin.zhang@biogen.com');

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

?>
