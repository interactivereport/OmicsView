<?php

$currentTable = 'ComparisonData';

$APP_CONFIG['Internal_Data'][$currentTable]['Name'] 		= 'Comparison Data';
$APP_CONFIG['Internal_Data'][$currentTable]['Example'] 	= 'download/Comparisons_Data.csv';
$APP_CONFIG['Internal_Data'][$currentTable]['Table'] 	= 'App_User_Data_ComparisonData';
$APP_CONFIG['Internal_Data'][$currentTable]['File_Not_Exist_Message'] 	= 'Comparison data is missing. Please provide a comparison data file.';

$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['p_value']			= 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['p.value']			= 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['pvalue']			= 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['p_val']			= 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['p.val']			= 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['pval']			= 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['adj_p_val']	= 'AdjustedPValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['adj.p.value']	= 'AdjustedPValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['adj_pval']	= 'AdjustedPValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['adj_pvalue']	= 'AdjustedPValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['logfc']		= 'Log2FoldChange';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['log2fc']		= 'Log2FoldChange';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['feature']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['genename']	 	= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparisonname']	 	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparison']	 	= 'ComparisonID';

$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'comparison_data';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'comparisons_data';

 
/*
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['ensembl_geneid']	= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['id']				= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_name']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['genename']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_symbol']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['symbol']			= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['identifier']		= 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['gene_identifier']	= 'Gene';
*/
 

$currentHeader = 'Gene';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Gene ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Comparison ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['LinkToTable']	= 'App_User_Data_Comparisons';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['LinkToField']	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'Log2FoldChange';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Log2 Fold Change';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'PValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'p-value';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'AdjustedPValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Adjusted p-value';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'NumeratorValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Numerator Value';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 0;

$currentHeader = 'DenominatorValue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Denominator Value';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 0;

?>