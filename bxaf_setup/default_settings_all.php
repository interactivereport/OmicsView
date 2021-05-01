<?php

//*****************************************************************************************
// Question: Derrick Cheng (derrick@bioinforx.com)
// Last Revised: 2019-12-27
//
// This file is part of the standard upgrade. Any changes made here will be overwritten.
// Please overwrite the value in override.php
//*****************************************************************************************

//System-level Application
$BXAF_CONFIG_CUSTOM['GZIP_BIN']							= '/bin/pigz -p 3';
$BXAF_CONFIG_CUSTOM['GUNZIP_BIN']						= '/bin/unpigz -p 3';
$BXAF_CONFIG_CUSTOM['UNZIP_BIN']						= '/usr/bin/unzip';
$BXAF_CONFIG_CUSTOM['PHP_BIN']							= '/bin/php';
$BXAF_CONFIG_CUSTOM['SORT_BIN']							= '/bin/sort --parallel=4';
$BXAF_CONFIG_CUSTOM['CAT_BIN']							= '/bin/cat';
$BXAF_CONFIG_CUSTOM['TAIL_BIN']							= '/bin/tail';
$BXAF_CONFIG_CUSTOM['RM_BIN']							= '/bin/rm';
$BXAF_CONFIG_CUSTOM['FIND_BIN']							= '/bin/find';
$BXAF_CONFIG_CUSTOM['BGZIP_DIR'] 						= '/public/programs/tabix/latest/bgzip';
$BXAF_CONFIG_CUSTOM['TABIX_BIN'] 						= '/public/programs/tabix/latest/tabix';
$BXAF_CONFIG_CUSTOM['HOMER_PATH']						= '/public/programs/homer/bin';
$BXAF_CONFIG_CUSTOM['RSCRIPT_BIN'] 						= '/bin/Rscript';
$BXAF_CONFIG_CUSTOM['RSYNC_BIN'] 						= '/bin/rsync';
$BXAF_CONFIG_CUSTOM['NETSTAT_BIN'] 						= '/bin/netstat';
$BXAF_CONFIG_CUSTOM['PS_BIN'] 							= '/bin/ps';
$BXAF_CONFIG_CUSTOM['PKILL_BIN'] 						= '/bin/pkill';
$BXAF_CONFIG_CUSTOM['PGREP_BIN']						= '/bin/pgrep';
$BXAF_CONFIG_CUSTOM['GREP_BIN']							= '/usr/bin/grep';



$BXAF_CONFIG_CUSTOM['APP_DB_DRIVER']               	 	= 'mysql';
$BXAF_CONFIG_CUSTOM['APP_DB_SERVER'] 					= 'localhost';

$BXAF_CONFIG_CUSTOM['REDIS_ENABLE']						= true;
$BXAF_CONFIG_CUSTOM['REDIS_COMPRESSION']				= true;

//******************************************************************************************

//Dataset Level
$BXAF_CONFIG_CUSTOM['HAS_PUBLIC_DATA']					= true;
$BXAF_CONFIG_CUSTOM['HAS_PUBLIC_COMPARISON_DATA']		= true;
$BXAF_CONFIG_CUSTOM['HAS_PUBLIC_MICROARRAY_DATA']		= true;
$BXAF_CONFIG_CUSTOM['Gene_or_Protein'] 					= 'Gene';
$BXAF_CONFIG_CUSTOM['Internal_Data_Flexible_Columns']	= false;
$BXAF_CONFIG_CUSTOM['Left_Menu_Expanded']				= false;
$BXAF_CONFIG_CUSTOM['HAS_GTEx_DATA']					= false;
$BXAF_CONFIG_CUSTOM['Precheck_Modified_DiseaseState']	= true;

//******************************************************************************************

//Application Level
$BXAF_CONFIG_CUSTOM['Admin_User_Email']					= array('info@bioinforx.com');

//This account will be used when the user clicks "Login as Guest"
$BXAF_CONFIG_CUSTOM['GUEST_ACCOUNT']					= '';

//Application Level Settings
//# of upload handled by the background process at a time, recommend: 1
$BXAF_CONFIG_CUSTOM['IMPORT']['CONCURRENT']				= 1;

//# of records inserted into MySQL at a time
$BXAF_CONFIG_CUSTOM['IMPORT']['BULK_INSERT']			= 10000;


//Framework Level Settings
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_CSS_MENU'] 				= 'navbar-light bg-faded';
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_CSS_FOOTER']	    		= 'w-100 bg-faded text-center text-muted py-3';



//Settings to send emails
$BXAF_CONFIG_CUSTOM['EMAIL_METHOD']     				= 'url';
$BXAF_CONFIG_CUSTOM['SENDEMAIL_VIA_URL']     			= 'http://library.bxgenomics.com/api/email/app_send_message_encrypted.php?email=';
$BXAF_CONFIG_CUSTOM['SENDEMAIL_VIA_URL_KEY'] 			= 'FGDSrjekjkkjfgkd432fdsf12Edws';
$BXAF_CONFIG_CUSTOM['SENDEMAIL_VIA_URL_FROM'] 			= 'info@bioinforx.com';
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_EMAIL'] 					= 'info@bioinforx.com';


$BXAF_CONFIG_CUSTOM['NGS_PLATFORMS']					= array('GPL15433', 'GPL18460', 'GPL20301', 'GPL20795', 'GPL11154', 'GPL16791', 'GPL10999', 'GPL18573', 'GPL9052', 'GPL15456', 'GPL20301', 'GPL9115', 'GPL21290', 'GPL20795', 'GPL15433', 'GPL18460', 'GPL15520', 'GPL13112', 'GPL17021', 'GPL11002', 'GPL19057', 'GPL9185', 'GPL15103', 'GPL9250', 'GPL21103', 'GPL18480', 'GPL16173', 'GPL21493', 'GPL16417', 'GPL14844', 'GPL18694', 'GPL20797', 'GPL22396', 'GPL20084', 'GPL10287', 'GPL10669', 'GPL19052', 'RNA-SEQ');

$BXAF_CONFIG_CUSTOM['GENESETS_API']['genesets.php']		= '//bxngs.com/bxomics/api2_dc/genesets.php';
$BXAF_CONFIG_CUSTOM['GENESETS_API']['genesets.css']		= '//bxngs.com/bxomics/api2_dc/genesets.css';
$BXAF_CONFIG_CUSTOM['GENESETS_API']['genesets.js']		= '//bxngs.com/bxomics/api2_dc/genesets.js';


//******************************************************************************************

//Enable the modules
//1: Enable; 0: Disable
$BXAF_CONFIG_CUSTOM['APP_SWITCH']['Research_Project']	= 1;
$BXAF_CONFIG_CUSTOM['APP_SWITCH']['KEGG_Pathway']		= 1;
$BXAF_CONFIG_CUSTOM['APP_SWITCH']['Internal_Data']		= 1;

$BXAF_CONFIG_CUSTOM['Home_Page']						= '';

$BXAF_CONFIG_CUSTOM['BUBBLE_PLOT_SELECT_TOP']			= 20;

$BXAF_CONFIG_CUSTOM['TEXT_LENGTH']						= 50;

$BXAF_CONFIG_CUSTOM['CELL_MAP_THREAD']					= 8;

$BXAF_CONFIG_CUSTOM['BULK_UPDATE']						= true;

?>