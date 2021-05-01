<?php

$APP_CONFIG['Table']['Info'] 										= 'App_Info';
$APP_CONFIG['Tables']['App_Info']['Title'] 							= 'Application Info';

$APP_CONFIG['Table']['Cache']										= 'App_Cache';
$APP_CONFIG['Tables']['App_Cache']['Title'] 						= 'Application Cache';

$APP_CONFIG['Table']['List']										= 'UserSavedLists';
$APP_CONFIG['Tables']['UserSavedLists']['Title'] 					= 'Saved List';

$APP_CONFIG['Table']['UserPreference']								= 'UserPreference';
$APP_CONFIG['Tables']['UserPreference']['Title'] 					= 'User Preference';

$APP_CONFIG['Table']['Research_Project']							= 'App_Research_Project';
$APP_CONFIG['Tables']['App_Research_Project']['Title']				= 'Study';

$APP_CONFIG['Table']['Research_Project_User_Role']					= 'App_Research_Project_User_Roles';
$APP_CONFIG['Tables']['App_Research_Project_User_Roles']['Title']	= 'Study - User Roles';

$APP_CONFIG['Table']['App_Research_Project_Item']					= 'App_Research_Project_Item';
$APP_CONFIG['Tables']['App_Research_Project_Item']['Title']			= 'Study - Items';

$APP_CONFIG['Table']['App_Research_Project_Records']				= 'App_Research_Project_Records';
$APP_CONFIG['Tables']['App_Research_Project_Records']['Title']		= 'Study - Records';

$APP_CONFIG['Table']['KEGG_Pathway']								= 'App_KEGG_Pathway';
$APP_CONFIG['Tables']['App_KEGG_Pathway']['Title']					= 'KEGG Pathway';

$APP_CONFIG['Table']['App_User_Data_ComparisonData']				= 'App_User_Data_ComparisonData';
$APP_CONFIG['Tables']['App_User_Data_ComparisonData']['Title']		= 'Internal Comparison Data';

$APP_CONFIG['Table']['App_User_Data_Comparisons']					= 'App_User_Data_Comparisons';
$APP_CONFIG['Tables']['App_User_Data_Comparisons']['Title']			= 'Internal Comparisons';

$APP_CONFIG['Table']['App_User_Data_Comparisons_Combined']			= 'App_User_Data_Comparisons_Combined';
$APP_CONFIG['Tables']['App_User_Data_Comparisons_Combined']['Title']= 'All Samples';

$APP_CONFIG['Table']['App_User_Data_GeneLevelExpression']			= 'App_User_Data_GeneLevelExpression';
$APP_CONFIG['Tables']['App_User_Data_GeneLevelExpression']['Title']	= 'Internal Gene Expression Data';

$APP_CONFIG['Table']['App_User_Data_Projects']						= 'App_User_Data_Projects';
$APP_CONFIG['Tables']['App_User_Data_Projects']['Title']			= 'Internal Projects';

$APP_CONFIG['Table']['App_User_Data_Projects_Combined']				= 'App_User_Data_Projects_Combined';
$APP_CONFIG['Tables']['App_User_Data_Projects_Combined']['Title']	= 'All Projects';

$APP_CONFIG['Table']['App_User_Data_Samples']						= 'App_User_Data_Samples';
$APP_CONFIG['Tables']['App_User_Data_Samples']['Title']				= 'Internal Samples';

$APP_CONFIG['Table']['App_User_Data_Samples_Combined']				= 'App_User_Data_Samples_Combined';
$APP_CONFIG['Tables']['App_User_Data_Samples_Combined']['Title']	= 'All Samples';

$APP_CONFIG['Table']['App_User_Data_Datasets']						= 'App_User_Data_Datasets';
$APP_CONFIG['Tables']['App_User_Data_Datasets']['Title']			= 'Internal Dataset';

$APP_CONFIG['Table']['App_User_Data_Job']							= 'App_User_Data_Job';
$APP_CONFIG['Tables']['App_User_Data_Job']['Title']					= 'Internal Job';

$APP_CONFIG['Table']['App_User_Data_Research_Project']				= 'App_User_Data_Research_Project';
$APP_CONFIG['Tables']['App_User_Data_Research_Project']['Title']	= 'Internal Job Index';

$APP_CONFIG['Table']['App_Sample_Comparison_DiseaseState']			= 'App_Sample_Comparison_DiseaseState';
$APP_CONFIG['Tables']['App_Sample_Comparison_DiseaseState']['Title']= 'Special Disease State Table';

$APP_CONFIG['Table']['App_Meta_Analysis']							= 'App_Meta_Analysis';
$APP_CONFIG['Tables']['App_Meta_Analysis']['Title']					= 'Meta Analysis Table';

$APP_CONFIG['Table']['GeneAnnotation']								= 'GeneAnnotation';
$APP_CONFIG['Tables']['GeneAnnotation']['Title']					= 'Gene Annotation';

$APP_CONFIG['Table']['GeneCombined']								= 'GeneCombined';
$APP_CONFIG['Tables']['GeneCombined']['Title']						= 'Gene Annotation 2';

$APP_CONFIG['Table']['App_User_Data_Definition']					= 'App_User_Data_Definition';
$APP_CONFIG['Tables']['App_User_Data_Definition']['Title']			= 'Internal Data Definition';

$APP_CONFIG['Table']['App_User_Data_CellMap']						= 'App_User_Data_CellMap';
$APP_CONFIG['Tables']['App_User_Data_CellMap']['Title']				= 'CellMap';

$APP_CONFIG['Table']['App_User_Data_Audit_Trail']					= 'App_User_Data_Audit_Trail';
$APP_CONFIG['Tables']['App_User_Data_Audit_Trail']['Title']			= 'Audit Trail';

$APP_CONFIG['Table']['Samples']			= 'Samples';
$APP_CONFIG['Table']['Projects']		= 'Projects';
$APP_CONFIG['Table']['Comparisons']		= 'Comparisons';
$APP_CONFIG['Table']['Datasets']		= 'Datasets';

$APP_CONFIG['APP']['Search']['Operator'][0]	= 'Contains';
$APP_CONFIG['APP']['Search']['Operator'][1]	= 'Is';
$APP_CONFIG['APP']['Search']['Operator'][2]	= 'Is not';
$APP_CONFIG['APP']['Search']['Operator'][3]	= 'Starts With';
$APP_CONFIG['APP']['Search']['Operator'][4]	= 'Ends With';

//In Seconds, 0: Never expire
$APP_CONFIG['APP']['Cache_Expiration_Length'] = 86400;

$APP_CONFIG['APP']['Table']['PerPage']['JS'] = '10, 25, 50, 100, 250, 500, 1000';
$APP_CONFIG['APP']['Table']['PerPage']['Max'] = 1000;

$APP_CONFIG['APP']['Custom_Column_Count'] = 100;

$currentIndex = 'Gene';
if (get_gene_type() == 'Gene'){
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Name'] 				= 'Gene';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['name'] 				= 'gene';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Names']				= 'Genes';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table'] 			= 'GeneCombined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_User'] 		= 'GeneCombined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human'] 		= 'geneName';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human_Alt'] 	= 'GeneName';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human2']		= 'GeneID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Default']	= 'GeneID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Internal'] 	= 'GeneIndex';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Title'] 			= 'Gene Names';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['title'] 			= 'gene names';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Title'] 		= 'Gene List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Titles'] 		= 'Gene Lists';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['List of Records'] 	= 'Genes';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Titles'] 		= 'Create a Gene List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Search_Titles'] 	= 'Search Genes';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_Titles'] 	= 'Review Genes';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_URL']	 	= "app_record_browse.php?Category={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_URL'] 	= "app_user_preferences.php?tab={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_Key'] 	= 'gene_search_page_table_column';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Section_Titles'] 	= 'List of Genes';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Create_New_List'] 	= 'Create Gene List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Update_List'] 		= 'Update Gene List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Number'] 			= '# of Genes';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['File_Update'] 		= 'app_list_gene_list.php';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Example_Message'] 	= 'Please enter the gene names you want to save, seperated by line break.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['No_Match_Message'] 	= 'The genes you entered do not exist in the database.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Selected_Records_To_Study'] 	= 'Save Selected Genes to Study';
} elseif (get_gene_type() == 'Protein'){
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Name'] 				= 'Protein';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['name'] 				= 'protein';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Names']				= 'Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table'] 			= 'GeneCombined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_User'] 		= 'GeneCombined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human'] 		= 'geneName';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human_Alt'] 	= 'GeneName';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human2']		= 'GeneID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Default']	= 'GeneID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Internal'] 	= 'GeneIndex';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Title'] 			= 'Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['title'] 			= 'proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Title'] 		= 'Protein List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Titles'] 		= 'Protein Lists';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['List of Records'] 	= 'Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Titles'] 		= 'Create a Protein List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Search_Titles'] 	= 'Search Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_Titles'] 	= 'Review Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_URL']	 	= "app_record_browse.php?Category={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_URL'] 	= "app_user_preferences.php?tab={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_Key'] 	= 'gene_search_page_table_column';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Section_Titles'] 	= 'List of Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Create_New_List'] 	= 'Create Protein List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Update_List'] 		= 'Update Protein List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Number'] 			= '# of Proteins';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['File_Update'] 		= 'app_list_gene_list.php';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Example_Message'] 	= 'Please enter the proteins you want to save, seperated by line break.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['No_Match_Message'] 	= 'The proteins you entered do not exist in the database.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Selected_Records_To_Study'] 	= 'Save Selected Proteins to Study';
}


if (true){
	$currentIndex = 'Sample';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Name'] 				= 'Sample';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['name'] 				= 'sample';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Names']				= 'Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table'] 			= 'Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_User'] 		= 'App_User_Data_Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_Combined'] 	= 'App_User_Data_Samples_Combined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human'] 		= 'SampleID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Default']	= 'SampleID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Internal'] 	= 'SampleIndex';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Title'] 			= 'Sample IDs';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['title'] 			= 'sample IDs';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Title'] 		= 'Sample List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Titles'] 		= 'Sample Lists';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['List of Records'] 	= 'Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Titles'] 		= 'Create a Sample List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Search_Titles'] 	= 'Search Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_Titles'] 	= 'Review Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_URL']	 	= "app_record_browse.php?Category={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_URL'] 	= "app_user_preferences.php?tab={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_Key'] 	= 'sample_search_page_table_column';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Section_Titles'] 	= 'List of Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Create_New_List'] 	= 'Create Sample List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Update_List'] 		= 'Update Sample List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Number'] 			= '# of Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['File_Update'] 		= 'app_list_sample_list.php';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Example_Message'] 	= 'Please enter the sample IDs you want to save, seperated by line break.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['No_Match_Message'] 	= 'The sample IDs you entered do not exist in the database.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Selected_Records_To_Study'] 	= 'Save Selected Samples to Study';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Edit'] 				= 'Update Selected Samples';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Edit_Title']		= 'Update Sample Records';
}

if (true){
	$currentIndex = 'Project';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Name'] 				= 'Project';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['name'] 				= 'project';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Names']				= 'Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table'] 			= 'Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_User'] 		= 'App_User_Data_Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_Combined'] 	= 'App_User_Data_Projects_Combined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human'] 		= 'ProjectID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Default']	= 'ProjectID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Internal'] 	= 'ProjectIndex';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Title'] 			= 'Project IDs';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['title'] 			= 'project IDs';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Title'] 		= 'Project List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Titles'] 		= 'Project Lists';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['List of Records'] 	= 'Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Titles'] 		= 'Create a Project List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Search_Titles'] 	= 'Search Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_Titles'] 	= 'Review Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_URL']	 	= "app_record_browse.php?Category={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_URL'] 	= "app_user_preferences.php?tab={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_Key'] 	= 'project_search_page_table_column';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Section_Titles'] 	= 'List of Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Create_New_List'] 	= 'Create Project List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Update_List'] 		= 'Update Project List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Number'] 			= '# of Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['File_Update'] 		= 'app_list_project_list.php';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Example_Message'] 	= 'Please enter the project IDs you want to save, seperated by line break.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['No_Match_Message'] 	= 'The project IDs you entered do not exist in the database.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Selected_Records_To_Study'] 	= 'Save Selected Projects to Study';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Edit'] 				= 'Update Selected Projects';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Edit_Title']		= 'Update Project Records';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_Single_URL']	= "app_project_review.php?ID=";
	
	
	$APP_CONFIG['APP']['Permissions'][$currentIndex]['Update']['Table_Header']		= 'Can Update Project Permission?';
	$APP_CONFIG['APP']['Permissions'][$currentIndex]['Gene_List']['Table_Header']	= 'Can Add Gene List?';
	$APP_CONFIG['APP']['Permissions'][$currentIndex]['In_House']['Table_Header']	= 'Can Add Inhouse Data?';
	$APP_CONFIG['APP']['Permissions'][$currentIndex]['Heatmap']['Table_Header']		= 'Can Add Heatmap?';
}





if (true){
	$currentIndex = 'Comparison';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Name'] 				= 'Comparison';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['name'] 				= 'comparison';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Names'] 			= 'Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table'] 			= 'Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_User'] 		= 'App_User_Data_Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Table_Combined'] 	= 'App_User_Data_Comparisons_Combined';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Human'] 		= 'ComparisonID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Default']	= 'ComparisonID';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Column_Internal'] 	= 'ComparisonIndex';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Title'] 			= 'Comparison IDs';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['title'] 			= 'comparison IDs';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Title'] 		= 'Comparison List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Page_Titles'] 		= 'Comparison Lists';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['List of Records'] 	= 'Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Titles'] 		= 'Create a Comparison List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Search_Titles'] 	= 'Search Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_Titles'] 	= 'Review Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Review_URL']	 	= "app_record_browse.php?Category={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_URL'] 	= "app_user_preferences.php?tab={$currentIndex}";
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Preference_Key'] 	= 'comparison_search_page_table_column';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Section_Titles'] 	= 'List of Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Create_New_List'] 	= 'Create Comparison List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Update_List'] 		= 'Update Comparison List';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Number'] 			= '# of Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['File_Update'] 		= 'app_list_comparison_list.php';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Example_Message'] 	= 'Please enter the comparison IDs you want to save, seperated by line break.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['No_Match_Message'] 	= 'The comparison IDs you entered do not exist in the database.';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Save_Selected_Records_To_Study'] 	= 'Save Selected Comparisons to Study';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Edit'] 				= 'Update Selected Comparisons';
	$APP_CONFIG['APP']['List_Category'][$currentIndex]['Edit_Title']		= 'Update Comparison Records';
}



if (true){
	$APP_CONFIG['APP']['Research_Project_User_Role'][0]								= 'No Access';
	$APP_CONFIG['APP']['Research_Project_User_Role'][2]								= 'Read Only';
	$APP_CONFIG['APP']['Research_Project_User_Role'][1]								= 'Standard User';
	$APP_CONFIG['APP']['Research_Project_User_Role'][3]								= 'Full Access';
}


$APP_CONFIG['APP']['User_Preferences']['TBL_PREFERENCE_ALL_OPTIONS'] = array(
	'dashboard_chart_cell_type' => array('Hide Unknown', 'Hide Others', 'Show Top 15 (Uncheck to show all)'),
	'dashboard_chart_disease_state' => array('Hide Unknown', 'Hide Normal Control', 'Hide Others','Show Top 15 (Uncheck to show all)'),
	'dashboard_chart_treatment' => array('Hide Unknown', 'Hide Others'),
	'dashboard_chart_platform_name' => array('Hide Others'),
);
$APP_CONFIG['APP']['User_Preferences']['Data_Source'] 			= array('all' => 'All', 'private' => 'Internal Data');
$APP_CONFIG['APP']['User_Preferences']['Gene_Data_Type'] 		= array('FPKM' => 'Value', 'TPM' => 'TPM');
$APP_CONFIG['APP']['User_Preferences']['Left_Menu_Expanded'] 	= array('1' => 'Opened', '2' => 'Closed');


if (true){
	unset($currentIndex);
	
	$currentIndex++;
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Foreground'] = 'rgb(0, 0, 0)';
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Background'] = 'rgb(203, 220, 234)';
	
	$currentIndex++;
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Foreground'] = 'rgb(0, 0, 0)';
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Background'] = 'rgb(152, 186, 214)';
	
	$currentIndex++;
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Foreground'] = 'rgb(255, 255, 255)';
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Background'] = 'rgb(102, 151, 194)';
	
	
	$currentIndex++;
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Foreground'] = 'rgb(255, 255, 255)';
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Background'] = 'rgb(51, 117, 173)';
	
	
	$currentIndex++;
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Foreground'] = 'rgb(255, 255, 255)';
	$APP_CONFIG['APP']['Heatmap_Colors'][$currentIndex]['Background'] = 'rgb(0, 82, 153)';
}

if (true){
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('38', '5D', '8A');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('8C', '38', '36');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('71', '89', '3F');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('5C', '47', '76');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('35', '7D', '91');
	
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('B6', '6D', '31');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('42', '6D', 'A1');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('A4', '43', '40');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('89', '9F', '48');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('6C', '54', '8A');
	
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('3F', '92', 'A9');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('D3', '7F', '3A');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('4B', '7B', 'B4');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('B7', '4C', '49');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('94', 'B2', '55');
	
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('7A', '5F', '9A');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('47', 'A4', 'BD');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('EC', '8F', '42');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('73', '94', 'C5');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('C8', '73', '72');
	
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('A9', 'C3', '79');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('94', '80', 'AE');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('70', 'B7', 'CD');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('F8', 'A5', '6E');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('A1', 'B4', 'D4');
	
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('D6', 'A1', 'A0');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('C0', 'D2', 'A4');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('B3', 'A8', 'C4');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('A0', 'CA', 'D9');
	$APP_CONFIG['APP']['Color_Template'][0][]	= array('F9', 'BE', '9E');
}


if (true){
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['N.data.points']['Print']			= '# of Data Points';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['N.data.points']['HTML']			= '# of<br/>Data Points';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Up.Per']['Print'] 					= 'Upregulated (%)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Up.Per']['HTML'] 					= 'Upregulated (%)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Down.Per']['Print'] 				= 'Downregulated (%)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Down.Per']['HTML'] 				= 'Downregulated (%)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_Pval_Fisher']['Print'] 	= 'Combined p-value (Fisher)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_Pval_Fisher']['HTML'] 	= 'Combined p-value<br/>(Fisher)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_Pval_maxP']['Print'] 		= 'Combined p-value (Maximum p-value)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_Pval_maxP']['HTML'] 		= 'Combined p-value<br/>(Maximum p-value)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_FDR_Fisher']['Print'] 	= 'Combined FDR (Fisher)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_FDR_Fisher']['HTML'] 		= 'Combined FDR<br/>(Fisher)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_FDR_maxP']['Print'] 		= 'Combined FDR (Maximum p-value)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['Combined_FDR_maxP']['HTML'] 		= 'Combined FDR<br/>(Maximum p-value)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RankProd']['Print'] 				= 'Rank Products';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RankProd']['HTML'] 				= 'Rank Products';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC']['Print'] 				= 'Rank Products log2FC';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC']['HTML'] 				= 'Rank Products<br/>(log<sub>2</sub> Fold Change)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC_max']['Print'] 			= 'Max. Rank Products log2FC';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC_max']['HTML'] 			= 'Max. Rank Products<br/>(log<sub>2</sub> Fold Change)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC_min']['Print'] 			= 'Min. Rank Products log2FC';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_logFC_min']['HTML'] 			= 'Min. Rank Products<br/>(log<sub>2</sub> Fold Change)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_Pval']['Print'] 				= 'Rank Products (p-value)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_Pval']['HTML'] 					= 'Rank Products<br/>(p-value)';
	
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_FDR'] ['Print']					= 'Rank Products (FDR)';
	$APP_CONFIG['APP']['Meta_Analysis_Headers']['RP_FDR'] ['HTML']					= 'Rank Products<br/>(FDR)';
}

if (true){
	$APP_CONFIG['APP']['Meta_Analysis']['n_data_points'] 							= 1;
	$APP_CONFIG['APP']['Meta_Analysis']['up_per'] 									= 0;
	$APP_CONFIG['APP']['Meta_Analysis']['down_per'] 								= 0;
	$APP_CONFIG['APP']['Meta_Analysis']['Combined_Pval_Fisher'] 					= 1;
	$APP_CONFIG['APP']['Meta_Analysis']['Combined_Pval_maxP'] 						= 0.01;
	$APP_CONFIG['APP']['Meta_Analysis']['RP_Pval'] 									= 0.01;
	$APP_CONFIG['APP']['Meta_Analysis']['RP_logFC_max']								= '';
	$APP_CONFIG['APP']['Meta_Analysis']['RP_logFC_min']								= '';
	
	$APP_CONFIG['APP']['Meta_Analysis']['Display']['Log2FoldChange']				= "Log<sub>2</sub> Fold Change";
	$APP_CONFIG['APP']['Meta_Analysis']['Display']['PValue']						= "p-value";
	$APP_CONFIG['APP']['Meta_Analysis']['Display']['AdjustedPValue']				= "FDR";
}


$APP_CONFIG['APP']['Gene_Search_Data_Filter_Default_Column_No']					= 12;

$APP_CONFIG['APP']['PAGE']['PAGE_List'] 				= 'PAGE List';

if (!isset($BXAF_CONFIG['COMPARISON_INFO']['Charts'])){
	$APP_CONFIG['APP']['Homer']['biological_process'] 		= 'Biological Process';
	$APP_CONFIG['APP']['Homer']['cellular_component'] 		= 'Cellular Component';
	$APP_CONFIG['APP']['Homer']['molecular_function'] 		= 'Molecular Function';
	$APP_CONFIG['APP']['Homer']['kegg'] 					= 'KEGG';
	$APP_CONFIG['APP']['Homer']['msigdb'] 					= 'Molecular Signature';
	$APP_CONFIG['APP']['Homer']['interpro'] 				= 'Interpro Protein Domain';
	$APP_CONFIG['APP']['Homer']['wikipathways'] 			= 'WikiPathway';
	$APP_CONFIG['APP']['Homer']['reactome'] 				= 'Reactome';
} else {
	foreach ($BXAF_CONFIG['COMPARISON_INFO']['Charts'] as $tempKey => $tempValue){
		$APP_CONFIG['APP']['Homer'][$tempValue] = $tempKey;
	}

}

if (true){
	$APP_CONFIG['APP']['Bubble_Plot']['Dropdown'] = array(
		'Case_AgeCategory',
		'Case_CellType',
		'Case_DiseaseState',
		'Case_Ethnicity',
		'Case_Gender',
		'Case_SamplePathology',
		'Case_SampleSource',
		'Case_SubjectTreatment',
		'ComparisonCategory'
	);

	$APP_CONFIG['APP']['Bubble_Plot']['y-axis'] = 'Case_DiseaseState';
	
	$APP_CONFIG['APP']['Bubble_Plot']['colorBy'] = 'Case_CellType';
	
	$APP_CONFIG['APP']['Bubble_Plot']['subplotBy'] = 'ComparisonCategory';
	
	$APP_CONFIG['APP']['Bubble_Plot']['margin']['Left'] 	= 500;
	$APP_CONFIG['APP']['Bubble_Plot']['margin']['Top'] 		= 100;
	$APP_CONFIG['APP']['Bubble_Plot']['margin']['Bottom'] 	= 200;
	$APP_CONFIG['APP']['Bubble_Plot']['margin']['Right'] 	= 200;
}

$APP_CONFIG['canvasxpress']['Data_Limit_Bubble_Plot'] = 2000;


?>