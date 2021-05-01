<?php

include_once(dirname(dirname(__DIR__)) . '/config.php');

$BXAF_CONFIG['BXAF_USER_CONTACT_ID'] = $_SESSION['User_Info']['ID'];

$BXAF_CONFIG['BXGENOMICS_REQUIRED_FILES'] = array(
	'comparison_page_all_msigdb.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/comparison_page_all_msigdb.txt',

	'list_001_neg.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/page_out/list_001_neg.txt',
	'list_001_pos.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/page_out/list_001_pos.txt',

	'list_005_neg.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/page_out/list_005_neg.txt',
	'list_005_pos.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/page_out/list_005_pos.txt',

	'list_025_neg.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/page_out/list_025_neg.txt',
	'list_025_pos.txt' => $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/page_out/list_025_pos.txt',
);

$BXAF_CONFIG['BXGENOMICS_DB_TABLES'] = array(
	'TBL_BXGENOMICS_GENES'               => 'GeneCombined',
	'TBL_BXGENOMICS_PROJECTS'            => 'Projects',
	'TBL_BXGENOMICS_SAMPLES'             => 'Samples',
	'TBL_BXGENOMICS_COMPARISONS'         => 'Comparisons',

	'TBL_BXGENOMICS_PROJECTS_USER'       => 'App_User_Data_Projects',
	'TBL_BXGENOMICS_SAMPLES_USER'        => 'App_User_Data_Samples',
	'TBL_BXGENOMICS_COMPARISONS_USER'    => 'App_User_Data_Comparisons',

	'TBL_BXGENOMICS_USERSAVEDLISTS'      => 'UserSavedLists',
	'TBL_BXGENOMICS_USERPREFERENCE'      => 'UserPreference',

	'TBL_BXGENOMICS_GENES_INDEX'         => 'TBL_BXGENOMICS_GENES_INDEX',
	'TBL_BXGENOMICS_GENE_LOOKUP'         => 'Gene_Lookup_' . $BXAF_CONFIG['SPECIES'],
	'TBL_BXGENOMICS_APP_CACHE'           => 'App_Cache',

	'TBL_WIKIPATHWAYS_INFO'              => 'tbl_wikipathways_info',

	'TBL_COMPARISON_GO_ENRICHMENT'       => 'tbl_comparison_go_enrichment',
	'TBL_COMPARISON_GO_ENRICHMENT_10_2'  => 'tbl_comparison_go_enrichment_10_2',
	'TBL_COMPARISON_GO_ENRICHMENT_10_6'  => 'tbl_comparison_go_enrichment_10_6',
	'TBL_COMPARISON_GO_ENRICHMENT_10_10' => 'tbl_comparison_go_enrichment_10_10',

	'TBL_GO_GENE_LIST'                   => 'tbl_go_gene_list',
	'TBL_PAGE_GENESETS'                  => 'tbl_page_genesets',

);

foreach ($BXAF_CONFIG['BXGENOMICS_DB_TABLES'] as $key => $value) {
	$BXAF_CONFIG[$key] = $value;
}


$BXAF_MODULE_CONN = bxaf_get_app_db_connection();


include_once(__DIR__ . '/config_functions.php');


$BXAF_CONFIG['BXGENOMICS_CACHE_DIR'] = "{$BXAF_CONFIG['WORK_DIR']}bxgenomics/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/";
$BXAF_CONFIG['BXGENOMICS_CACHE_URL'] = "{$BXAF_CONFIG['BXAF_ROOT_URL']}{$BXAF_CONFIG['WORK_URL']}bxgenomics/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/";

if (!is_dir($BXAF_CONFIG['BXGENOMICS_CACHE_DIR'])) mkdir($path, 0777, true);





$BXAF_CONFIG['TBL_BXGENOMICS_GENES_FIELDS'] = array(
	'GeneName', 'EntrezID', 'Source', 'Description', 'Alias', 'Ensembl', 'Unigene', 'Uniprot',
    'TranscriptNumber', 'Strand', 'Chromosome', 'Start', 'End', 'ExonLength', 'AccNum'
);
$BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS_FIELDS'] = array(
  'Disease', 'Accession', 'PubMed_ID', 'ExperimentType', 'ContactAddress', 'ContactOrganization',
  'ContactName', 'ContactEmail', 'ContactPhone', 'ContactWebLink', 'Keywords', 'Design', 'StudyType',
  'TherapeuticArea', 'Comment', 'Contributors', 'WebLink', 'PubMed', 'PubMed_Authors', 'Collection'
);
$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES_FIELDS'] = array(
	"ProjectName","PlatformGPL","PlatformName","BamFileName","CellType","Description","DiseaseStage","DiseaseState","Ethnicity","Gender","Organism","Response","SamplePathology","SampleSource","SampleType","SubjectID","Tissue","Title","Transfection","Treatment",
	"Clinical_Triplets_Weight_lb","Clinical_Triplets_HbA1c_%","Clinical_Triplets_TreatTime_days","Clinical_Triplets_SamplingTime_days","Clinical_Triplets_GestationalAge_weeks","Clinical_Triplets_ChipID","Clinical_Triplets_ReadLength_bp","Clinical_Triplets_CellID","Clinical_Triplets_TreatTime_hours","Clinical_Triplets_ExperimentBatch","Clinical_Triplets_Source_MitochondrialRate","Clinical_Triplets_RNASeq_Mapping_Rate__Percent","Clinical_Triplets_RNASeq_Mapped_Read_Count","Clinical_Triplets_Coverage_Mean","Clinical_Triplets_Coverage_GeneWithCoverageRat","Clinical_Triplets_Coverage_GeneWithCoverage",
	"Clinical_Triplets_Coverage_GeneWith1RPKMRate","Clinical_Triplets_Coverage_GeneWith1RPKM","Clinical_Triplets_Coverage_GeneWith10RPKMRate","Clinical_Triplets_Coverage_GeneWith10RPKM","Clinical_Triplets_CellNumber","Clinical_Triplets_BMI_kgm2","Clinical_Triplets_Alignment_UniquelyMappedRate","Clinical_Triplets_Alignment_NM_0Rate","Clinical_Triplets_Alignment_MappedRate","Clinical_Triplets_Alignment_Mapped","Clinical_Triplets_Alignment_All","Clinical_Triplets_Age_years"
);

$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS_FIELDS'] = array(
	'Case_SampleIDs', 'Control_SampleIDs', 'ComparisonCategory', 'ComparisonContrast',
    'Case_DiseaseState', 'Case_Tissue', 'Case_CellType', 'Case_Ethnicity', 'Case_Gender',
    'Case_SamplePathology', 'Case_SampleSource', 'Case_Treatment', 'Case_SubjectTreatment',
    'Case_AgeCategory', 'ComparisonType', 'Control_DiseaseState', 'Control_Tissue',
    'Control_CellType', 'Control_Ethnicity', 'Control_Gender', 'Control_SamplePathology',
    'Control_SampleSource', 'Control_Treatment', 'Control_SubjectTreatment', 'Control_AgeCategory'
);


$BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES_FIELD_VALUES'] = array(
	'CellType' => array(
      'NA', 'lymphoblast', 'peripheral blood mononuclear cell (PBMC)', 'epithelial cell'
    ),
    'DiseaseCategory' => array(
      'normal control', 'inflammatory bowel disease (IBD)', 'allergy;respiratory tract disease'
    ),
    'DiseaseState' => array(
      'normal control', 'asthma', 'ulcerative colitis (UC)', 'lung cancer', 'obesity'
    ),
    'SampleSource' => array(
      'skin', 'lung', 'colonic mucosa', 'lymphoblast', 'ileal mucosa'
    ),
    'Tissue' => array(
      'central nervous system - brain', 'skin', 'peripheral blood', 'lung', 'blood vessel'
    )
);


$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS_FIELD_VALUES'] = array(
	'ComparisonCategory' => array(
      'Treatment vs. Control', 'Disease vs. Normal', 'Responder vs. Non-Responder',
      'Disease1 vs. Disease2'
    ),
    'ComparisonContrast' => array(
      'obesity vs normal control', 'relapsing-remitting MS (RRMS) vs normal control',
      'response vs no response', 'rheumatoid arthritis (RA) vs normal control'
    ),
    'Case_CellType' => array(
      'synovial fibroblast cell', 'pulmonary fibroblast', 'adipocyte', 'monocyte',
      'airway epithelial cell', 'peripheral blood mononuclear cell (PBMC)'
    ),
    'Case_DiseaseState' => array(
      'normal control', 'ulcerative colitis (UC)', 'psoriasis', 'rheumatoid arthritis (RA)',
      'obesity', 'osteoarthritis (OA)'
    ),
    'Case_Tissue' => array(
      'colonic mucosa', 'skin', 'peripheral blood', 'subcutaneous adipose tissue',
      'synovial membrane', 'lung'
    )
);


?>