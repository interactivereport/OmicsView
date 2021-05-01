<?php

//*****************************************************************************************
// Question: Derrick Cheng (derrick@bioinforx.com)
// Last Revised: 2019-09-08
//
// This file is part of the standard upgrade. Any changes made here will be overwritten.
// Please overwrite the value in override.php
//*****************************************************************************************

//Server Environment
$BXAF_CONFIG_CUSTOM['APP_DB_USER'] 						= 'db_user';
$BXAF_CONFIG_CUSTOM['APP_DB_PASSWORD'] 					= 'BioInfoRx@2018';

$BXAF_CONFIG_CUSTOM['BXAF_PAGE_AUTHOR']					= 'OmicsView Administrator';

//The full path to the SQLlite database file
$BXAF_CONFIG_CUSTOM['BXAF_DB_NAME']                     = '/var/www/html/diseaseatlas_share/users.db';

//Footer Text
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_FOOTER_CONTENT']			= "Powered by 
 <a href='https://canvasxpress.org' target='_blank'>CanvasXpress.js</a>,
 <a href='https://d3js.org/' target='_blank'>D3.js</a>, 
 <a href='https://plot.ly/javascript/' target='_blank'>Plotly.js</a>, 
 <a href='https://www.highcharts.com' target='_blank'>Highcharts.js</a>, 
 <a href='https://www.r-project.org/' target='_blank'>R</a>, 
 <a href='https://www.bioconductor.org' target='_blank'>Bioconductor</a>, 
 <a href='http://homer.ucsd.edu/homer/ngs/rnaseq/index.html' target='_blank'>HOMER</a>,
 <a href='https://wikipathways.org' target='_blank'>WikiPathways</a>,
 <a href='http://www.genome.jp/kegg/' target='_blank'>KEGG</a>, 
 <a href='http://reactome.org/' target='_blank'>Reactome</a> and
 <a href='http://bioinforx.com/contact' target='_blank'>BioInfoRx Data Platform</a>.
 
 </div>";

//$BXAF_CONFIG_CUSTOM['Research_Project_Department'] 		= array('Department A', 'Department B', 'Department C');

//The password to access the admin tool
$BXAF_CONFIG_CUSTOM['BXAF_ADMIN_PASSWORD']				= 'mypassword';

//Allow user to sign up
$BXAF_CONFIG_CUSTOM['USER_SIGNUP_ENABLE']				= true;

//Make sure that all pages require user to login
$BXAF_CONFIG_CUSTOM['BXAF_LOGIN_REQUIRED'] 				= true;

$BXAF_CONFIG_CUSTOM['GUEST_ACCOUNT']					= 'guest';

$BXAF_CONFIG_CUSTOM['GUEST_ACCOUNT_READONLY']			= false;

//The default password
//Comment out the following line will disable this feature
//$BXAF_CONFIG_CUSTOM['BXAF_USER_DEFAULT_PASSWORD']		= 'bigdat123';

$BXAF_CONFIG_CUSTOM['API_Key']							= 'lnpJMJ5ClbuHCylWqfBY8BoxxdrpU0';


$BXAF_CONFIG_CUSTOM['WIKIPATHWAY_GPML_PATH']			= '/share/DiseaseLand/pvjs/wikipathways/current/gpml/';

$BXAF_CONFIG_CUSTOM['USE_TPM_ALWAYS']					= true;

$BXAF_CONFIG_CUSTOM['SHARE_LIBRARY_DIR']				= '/var/www/html/omicsview_share/library-4.0/';
$BXAF_CONFIG_CUSTOM['KEGG']['Script']				 	= '/var/www/html/omicsview_share/library-4.0/KEGG/Scripts/';

$BXAF_CONFIG_CUSTOM['Left_Menu_Expanded']				= true;

$BXAF_CONFIG_CUSTOM['canvasxpress']['Log_Add_Value']	= 0.01;

$BXAF_CONFIG_CUSTOM['canvasxpress']['graphOrientation']	= 'vertical';

$BXAF_CONFIG_CUSTOM['Precheck_Modified_DiseaseState']	= false;

$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT']						= 1;

$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Gene'] 			= 100;
$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Comparison'] 		= 20;
$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Sample'] 			= 20;

$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Gene_Sample_Message']
	= "The data owner (QIAGEN Bioinformatics) has requested to limit to <strong>{$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Gene']}</strong> genes and <strong>{$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Sample']}</strong> samples.";
	
$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Gene_Comparison_Message']
	= "The data owner (QIAGEN Bioinformatics) has requested to limit to <strong>{$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Gene']}</strong> genes and <strong>{$BXAF_CONFIG_CUSTOM['EXPORT_LIMIT_OPTIONS']['Comparison']}</strong> comparisons.";

$BXAF_CONFIG_CUSTOM['BUBBLE_PLOT_SELECT_TOP']			= 'All';

?>