<?php

include_once(dirname(dirname(dirname(__FILE__))) . "/config.php");
include_once('../profile/config.php');

//error_reporting(1);

$BXAF_CONFIG['LIBRARY_DIR'] = $BXAF_CONFIG['SHARE_LIBRARY_DIR'];


// Analysis Result Dir
$BXAF_CONFIG['PLOT']['COMP_GENE']                = $BXAF_CONFIG['LIBRARY_DIR'] . 'Human/Human_Comparisons';
//$BXAF_CONFIG['PLOT']['GO_OUTPUT']                = $BXAF_CONFIG['LIBRARY_DIR'] . $CONFIG_PROFILE['SPECIES'] . '/' . $CONFIG_PROFILE['SPECIES'] . '_GO_out';
$BXAF_CONFIG['PLOT']['GO_OUTPUT']                = $BXAF_CONFIG['GO_PATH'];

//$BXAF_CONFIG['PLOT']['PAGE_OUTPUT']              = $BXAF_CONFIG['LIBRARY_DIR'] . $CONFIG_PROFILE['SPECIES'] . '/PAGE_OUT';
$BXAF_CONFIG['PLOT']['PAGE_OUTPUT']              = $BXAF_CONFIG['PAGE_PATH'];

// User Generated Files
$BXAF_CONFIG['PLOT']['USER_FILES_DASHBOARD']     = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_dashboard';
$BXAF_CONFIG['PLOT']['USER_FILES_BUBBLE']        = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_bubble';
$BXAF_CONFIG['PLOT']['USER_FILES_VOLCANO']       = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_volcano';
$BXAF_CONFIG['PLOT']['USER_FILES_GO']            = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_go';
$BXAF_CONFIG['PLOT']['USER_FILES_PAGE']          = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_page';
$BXAF_CONFIG['PLOT']['USER_FILES_PCA']           = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_pca';
$BXAF_CONFIG['PLOT']['USER_FILES_PVJS']          = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_pvjs';
$BXAF_CONFIG['PLOT']['USER_FILES_META']          = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_meta';
$BXAF_CONFIG['PLOT']['USER_FILES_INHOUSE']       = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/user_files_inhouse';

// Saved PCA Results
$BXAF_CONFIG['PLOT']['SAVED_PCA']                = $BXAF_CONFIG['WORK_DIR'] . $CONFIG_PROFILE['WORK_DIR'] . '/saved_pca';


// Rewrite System Config
//Database name
$BXAF_CONFIG['APP_PLOT_DB_NAME'] 				= $BXAF_CONFIG['APP_DB_NAME'];
//Database user name
$BXAF_CONFIG['APP_PLOT_DB_USER'] 				= $BXAF_CONFIG['APP_DB_USER'];
//Database user password

$BXAF_CONFIG['WORK_PLOT_DIR']           = $BXAF_CONFIG['WORK_DIR'] . 'plot';
$BXAF_CONFIG['PLOT_URL']                = $BXAF_CONFIG['BXAF_APP_URL'] . 'plot';
$BXAF_CONFIG['COMP_GENE_HUMAN']         = $BXAF_CONFIG['PLOT']['COMP_GENE'];
$BXAF_CONFIG['GO_OUTPUT_HUMAN']         = $BXAF_CONFIG['PLOT']['GO_OUTPUT'];
$BXAF_CONFIG['PAGE_OUTPUT_HUMAN']       = $BXAF_CONFIG['PLOT']['PAGE_OUTPUT'];

$BXAF_CONFIG['USER_FILES_DASHBOARD']    = $BXAF_CONFIG['PLOT']['USER_FILES_DASHBOARD'];
$BXAF_CONFIG['USER_FILES_BUBBLE']       = $BXAF_CONFIG['PLOT']['USER_FILES_BUBBLE'];
$BXAF_CONFIG['USER_FILES_VOLCANO']      = $BXAF_CONFIG['PLOT']['USER_FILES_VOLCANO'];
$BXAF_CONFIG['USER_FILES_GO']           = $BXAF_CONFIG['PLOT']['USER_FILES_GO'];
$BXAF_CONFIG['USER_FILES_PAGE']         = $BXAF_CONFIG['PLOT']['USER_FILES_PAGE'];
$BXAF_CONFIG['USER_FILES_PCA']          = $BXAF_CONFIG['PLOT']['USER_FILES_PCA'];
$BXAF_CONFIG['USER_FILES_PVJS']         = $BXAF_CONFIG['PLOT']['USER_FILES_PVJS'];
$BXAF_CONFIG['USER_FILES_META']         = $BXAF_CONFIG['PLOT']['USER_FILES_META'];
$BXAF_CONFIG['SAVED_PCA']               = $BXAF_CONFIG['PLOT']['SAVED_PCA'];
$BXAF_CONFIG['INHOUSE_DATA_DIR']        = $BXAF_CONFIG['PLOT']['USER_FILES_INHOUSE'];


// $BXAF_CONN = bxaf_get_app_db_connection();
// $DB = $BXAF_CONN;
$BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS'] = $BXAF_CONFIG['BXAF_WEB_URL'] . 'gene_expressions/';


$DB = bxaf_get_app_db_connection();

// Set user ID
$BXAF_CONFIG['BXAF_USER_CONTACT_ID']         = $_SESSION['BXAF_USER_LOGIN_ID'];
$BXAF_CONFIG['BXAF_SPECIES'] = $CONFIG_PROFILE['SPECIES'];
$BXAF_CONFIG['BXAF_KEY'] = 'bioinforx';

// Define related database table names
$BXAF_CONFIG['TBL_COMPARISONDATA']            = 'ComparisonData';
$BXAF_CONFIG['TBL_COMPARISONS']               = 'Comparisons';
$BXAF_CONFIG['TBL_GENEANNOTATION']            = 'GeneAnnotation';
$BXAF_CONFIG['TBL_GENELIST']                  = 'GeneList';
$BXAF_CONFIG['TBL_PROJECTS']                  = 'Projects';
$BXAF_CONFIG['TBL_SAMPLES']                   = 'Samples';
$BXAF_CONFIG['TBL_USERPREFERENCE']            = 'UserPreference';
$BXAF_CONFIG['TBL_GENECOMBINED']              = 'GeneCombined';

$BXAF_CONFIG['TBL_GENESET']                   = 'GeneSet';
$BXAF_CONFIG['TBL_INHOUSE_COMPARISON']        = 'InHouseComparisons';
$BXAF_CONFIG['TBL_PCA_RESULT']                = 'PCA_Result';

$BXAF_CONFIG['TBL_LIST'] = array(
	'TBL_COMPARISONDATA'           => 'ComparisonData',
	'TBL_COMPARISONS'              => 'Comparisons',
	'TBL_GENEANNOTATION'           => 'GeneAnnotation',
	'TBL_GENELIST'                 => $BXAF_CONFIG['BXAF_SPECIES'] . 'GeneList',
	'TBL_PROJECTS'                 => 'Projects',
	'TBL_SAMPLES'                  => 'Samples',
	'TBL_USERPREFERENCE'           => 'UserPreference',
	'TBL_GENECOMBINED'             => 'GeneCombined',
);

// Check Tables
$sql = "SHOW TABLES";
$data = $DB->get_all($sql);

$BXAF_CONFIG['TBL_LIST_EXISTING'] = array();
foreach ($data as $key => $value) {
	foreach ($value as $k => $v) {
		$BXAF_CONFIG['TBL_LIST_EXISTING'][] = $v;
	}
}


$BXAF_CONFIG['DATABASE_TABLE_MISSING'] = array();
foreach ($BXAF_CONFIG['TBL_LIST'] as $value) {
	if (!in_array($value, $BXAF_CONFIG['TBL_LIST_EXISTING'])) {
		$BXAF_CONFIG['DATABASE_TABLE_MISSING'][] = $value;
	}
}

if (!in_array($BXAF_CONFIG['TBL_USERPREFERENCE'], $BXAF_CONFIG['DATABASE_TABLE_MISSING'])) {

	// Load User Preference
	$BXAF_CONFIG['TBL_PREFERENCE_CATEGORY'] = array(

	  // Search Tables
		'comparison_search_page_table_column' => serialize(array(
                                                              'Case_CellType',
                                                              'Case_DiseaseState',
                                                              'ComparisonCategory',
                                                              'ComparisonContrast',
															  'ComparisonType',
															  'ProjectName',
															  'SampleID_Count',
															  'Case_SampleID_Count',
															  'Control_SampleID_Count',
                                                              'PlatformName',
															  'ReferenceLibraryID',
                                                            )),
		'gene_search_page_table_column' => serialize(array(
															'EntrezID', 
															'GeneName', 
															'TranscriptNumber',
															'Description', 
															'Alias',
															)),
		'project_search_page_table_column' => serialize(array(
															'Platform', 
															'Title', 
															'Disease',
															'Description',
															'PubMed',
															'PubMed_Authors',
															'ReleaseDate',
															'StudyType',
															'ExperimentType',
															'ContactName',
															)),
															  
		'sample_search_page_table_column' => serialize(array(
															'ProjectName',
															'PlatformName', 
															'Description', 
															'DiseaseState', 
															'Tissue',
															'CellType',
															'Gender',
															'Treatment',
															'Response',
															)),

	  // Dashboard Options
		'dashboard_chart_cell_type' => serialize(array('Hide Unknown', 'Show Top 15 (Uncheck to show all)')),
		'dashboard_chart_disease_state' => serialize(array('Hide Unknown', 'Hide Normal Control', 'Show Top 15 (Uncheck to show all)')),
		'dashboard_chart_treatment' => serialize(array('Hide Unknown', 'Hide Others')),
		'dashboard_chart_platform_name' => serialize(array('Hide Others')),

	  // Dashboard Displayed Fields
		'dashboard_displayed_cell_type' => serialize(array(
		'B cell',
		'T cell',
		'adipocyte',
		'airway epithelial cell',
		'airway smooth muscle cell (ASMC)',
		'alveolar epithelial cell',
		'bronchial epithelial cell',
		'epidermal keratinocyte',
		'epithelial cell',
		'fibroblast',
		'helper T cell',
		'monocyte',
		'monocyte-derived dendritic cell (MoDC)',
		'peripheral blood mononuclear cell (PBMC)',
	  )),
		'dashboard_displayed_disease_state' => serialize(array(
		"Alzheimer's disease (AD)",
		"asthma",
		"chronic obstructive pulmonary disease (COPD)",
		"crohn's disease (CD)",
		"idiopathic pulmonary fibrosis",
		"multiple sclerosis (MS)",
		"obesity",
		"psoriasis",
		"psoriasis vulgaris",
		"relapsing-remitting MS (RRMS)",
		"rheumatoid arthritis (RA)",
		"systemic lupus erythematosus (SLE)",
		"ulcerative colitis (UC)",
	  )),
		'dashboard_displayed_treatment' => serialize(array(
		'Unknown',
		'Others',
		'none',
		'Transfection_NA',
		'hypoxia',
		'IL-1 beta;IFN gamma',
		'lipopolysaccharide (LPS)',
		'differentiation medium',
		'TNF',
		'anti-CD28 antibody;anti-CD3 antibody',
		'TNF alpha',
	  )),
		'dashboard_displayed_platform_name' => serialize(array(
		'Others',
		'Affymetrix.HG-U133A',
		'Affymetrix.HG-U133A_2',
		'Affymetrix.HG-U133B',
		'Affymetrix.HG-U133_Plus_2',
		'Affymetrix.HT_HG-U133_Plus_PM',
		'Affymetrix.HG-U133A',
		'Affymetrix.HuGene-1_0-st-v1',
		'GPL6480',
		'Illumina.HumanHT-12_V3_0_R2_11283641_A',
		'Illumina.HumanHT-12_V4_0_R1_15002873_B',
		'NGS.Illumina.HiSeq2000',
	  )),

	);

	$BXAF_CONFIG['PREFERENCE_DETAIL'] = array(); // Current User Preference


	foreach ($BXAF_CONFIG['TBL_PREFERENCE_CATEGORY'] as $category => $default) {
		$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_USERPREFERENCE']}`
				WHERE `User_ID`=" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "
				AND `Category`='" . $category . "'";
		$data = $DB -> get_all($sql);
		// Create record for new user
		if (!is_array($data) || count($data) <= 0){
			
			
			//Try to use admin setting first
			$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_USERPREFERENCE']}` WHERE (`User_ID`=-1) AND `Category`='" . $category . "'";
			$data = $DB -> GetRow($sql);
			
			if ($data['ID'] > 0){
				//Yes, the default setting is available
				$info = array(
					'User_ID' => $BXAF_CONFIG['BXAF_USER_CONTACT_ID'],
					'Category' => $category,
					'Detail' => $data['Detail'],
				);
			} else {
				//Then use the default settings
				$info = array(
					'User_ID' => $BXAF_CONFIG['BXAF_USER_CONTACT_ID'],
					'Category' => $category,
					'Detail' => $default
				);
			}

			$DB -> insert($BXAF_CONFIG['TBL_USERPREFERENCE'], $info);			
			
			
			$BXAF_CONFIG['PREFERENCE_DETAIL'][$category] = $default; // Save preference
		} else {
			$BXAF_CONFIG['PREFERENCE_DETAIL'][$category] = $data[0]['Detail']; // Save preference
		}
	}


	// All Options for Preferences
	$BXAF_CONFIG['TBL_PREFERENCE_ALL_OPTIONS'] = array(
		'dashboard_chart_cell_type' => array('Hide Unknown', 'Hide Others', 'Show Top 15 (Uncheck to show all)'),
		'dashboard_chart_disease_state' => array('Hide Unknown', 'Hide Normal Control', 'Hide Others','Show Top 15 (Uncheck to show all)'),
		'dashboard_chart_treatment' => array('Hide Unknown', 'Hide Others'),
		'dashboard_chart_platform_name' => array('Hide Others'),
	);

}


//-----------------------------------------------------------------------------------
// Database
//-----------------------------------------------------------------------------------


// $sql = "CREATE TABLE `PCA_Result` (
//           `ID` int(11) NOT NULL,
//           `Owner_ID` int(11) NOT NULL,
//           `Title` varchar(255) NOT NULL,
//           `Type` varchar(255) NOT NULL DEFAULT '',
//           `Description` text NOT NULL,
//           `bxafStatus` tinyint(1) NOT NULL DEFAULT '0'
//         ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
// $DB -> Execute($sql);
// $sql = "ALTER TABLE `PCA_Result`
//         ADD PRIMARY KEY (`ID`),
//         ADD KEY `Owner_ID` (`Owner_ID`),
//         ADD KEY `bxafStatus` (`bxafStatus`),
//         ADD KEY `Title` (`Title`);";
// $DB -> Execute($sql);
// $sql = "ALTER TABLE `PCA_Result`
//         MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;";
// $DB -> Execute($sql);


// $sql = "CREATE TABLE `Meta_Result` (
//           `ID` int(11) NOT NULL,
//           `Owner_ID` int(11) NOT NULL,
//           `Title` varchar(255) NOT NULL,
//           `Type` varchar(255) NOT NULL DEFAULT '',
//           `Description` text NOT NULL,
//           `bxafStatus` tinyint(1) NOT NULL DEFAULT '0'
//         ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
// $DB->Execute($sql);
// $sql = "ALTER TABLE `Meta_Result`
//         ADD PRIMARY KEY (`ID`),
//         ADD KEY `Owner_ID` (`Owner_ID`),
//         ADD KEY `bxafStatus` (`bxafStatus`),
//         ADD KEY `Title` (`Title`);";
// $DB->Execute($sql);
// $sql = "ALTER TABLE `Meta_Result`
//         MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;";
// $DB->Execute($sql);

function get_stat_scale_color($value, $type='logFC') {
  if ($type == 'logFC') {
    if ($value >= 1) {
      return '#FF0000';
    } else if ($value > 0) {
      return '#FF8989';
    } else if ($value == 0) {
      return '#E5E5E5';
    } else if ($value > -1) {
      return '#7070FB';
    } else {
      return '#0000FF';
    }
  }
  if ($type == 'FDR') {
    if ($value > 0.05) {
      return '#9CA4B3';
    } else if ($value <= 0.01) {
      return '#015402';
    } else {
      return '#5AC72C';
    }
  }
  if ($type == 'PVal') {
    if ($value >= 0.01) {
      return '#9CA4B3';
    } else {
      return '#5AC72C';
    }
  }
  return;
}



?>
