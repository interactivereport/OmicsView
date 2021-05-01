<?php

$currentTable = 'Comparisons';

$APP_CONFIG['Internal_Data'][$currentTable]['Name'] 	= 'Comparison Info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example'] 	= 'download/Comparison_Info.csv';
$APP_CONFIG['Internal_Data'][$currentTable]['Table'] 	= 'App_User_Data_Comparisons';
$APP_CONFIG['Internal_Data'][$currentTable]['File_Not_Exist_Message'] 	= 'Comparison information is missing. Please provide a comparison info file.';


$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparisonname']	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparison_name']	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparison name']	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparison_id']	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['comparison id']	= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['title']			= 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Known_Map']['name']			= 'ComparisonID';

$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'comparison';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'comparisons';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'comparison_info';
$APP_CONFIG['Internal_Data'][$currentTable]['Example_File_Map'][] = 'comparisons_info';


$currentHeader = 'ComparisonID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Comparison ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;

$currentHeader = 'ProjectID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project ID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Optional_if_Single_Project'] = 1;

$currentHeader = 'ProjectIndex';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project Index';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['LinkToTable']	= 'App_User_Data_Projects';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['LinkToField']	= 'ProjectIndex';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['HideFromImport']= 1;


$currentHeader = 'Case_ACR70';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case ACR70';

$currentHeader = 'Case_AgeCategory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Age Category';

$currentHeader = 'Case_Allergen';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Allergen';

$currentHeader = 'Case_APOEStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case APOE Status';

$currentHeader = 'Case_BacteriaMorphology';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Bacteria Morphology';

$currentHeader = 'Case_BacteriaSource';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Bacteria Source';

$currentHeader = 'Case_BacteriaStrain';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Bacteria Strain';

$currentHeader = 'Case_BaselineSeverity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Baseline Severity';

$currentHeader = 'Case_BirthWeight';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Birth Weight';

$currentHeader = 'Case_BraakStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Braak Stage';

$currentHeader = 'Case_CellDescription';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cell Description';

$currentHeader = 'Case_CellLine';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cell Line';

$currentHeader = 'Case_CellMarkers';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cell Markers';

$currentHeader = 'Case_CellPassage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cell Passage';

$currentHeader = 'Case_CellSubgroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cell Subgroup';

$currentHeader = 'Case_CellType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cell Type';

$currentHeader = 'Case_ClinicalGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Clinical Group';

$currentHeader = 'Case_ClinicalPhase';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Clinical Phase';

$currentHeader = 'Case_ClinicalPhenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Clinical Phenotype';

$currentHeader = 'Case_Cohort';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Cohort';

$currentHeader = 'Case_COPD';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case COPD';

$currentHeader = 'Case_CultureCondition';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Culture Condition';

$currentHeader = 'Case_Description';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Description';

$currentHeader = 'Case_DiabetesReversalStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Diabetes ReversalStatus';

$currentHeader = 'Case_Diet';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Diet';

$currentHeader = 'Case_DifferentiationStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Differentiation Stage';

$currentHeader = 'Case_DiseaseHistory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease History';

$currentHeader = 'Case_DiseaseOnsetFstl1Level';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease Onset FSTL-1 Level';

$currentHeader = 'Case_DiseaseOnsetType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease Onset Type';

$currentHeader = 'Case_DiseaseRiskHaplotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease Risk Haplo Type';

$currentHeader = 'Case_DiseaseStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease Stage';

$currentHeader = 'Case_DiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease State';

$currentHeader = 'Case_DiseaseSubtype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Disease Subtype';

$currentHeader = 'Case_Donor';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Donor';

$currentHeader = 'Case_Dosage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Dosage';

$currentHeader = 'Case_Dosage_Gy';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Dosage (Gy)';

$currentHeader = 'Case_Ethnicity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Ethnicity';

$currentHeader = 'Case_FamilyHistory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Family History';

$currentHeader = 'Case_FamilyHistory_PD';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Family History (PD)';

$currentHeader = 'Case_FVCGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case FVC Group';

$currentHeader = 'Case_Gender';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Gender';

$currentHeader = 'Case_GeneticModification';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Genetic Modification';

$currentHeader = 'Case_Genotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Genotype';

$currentHeader = 'Case_GestationalAge_weeks';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Gestational Age (Weeks)';

$currentHeader = 'Case_Haplotype_IL7R';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Haplotype_IL7R';

$currentHeader = 'Case_IgM_MC';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case IgM_MC';

$currentHeader = 'Case_Infection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Infection';

$currentHeader = 'Case_InfectionGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case InfectionGroup';

$currentHeader = 'Case_InflammatoryComplications';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Inflammatory Complications';

$currentHeader = 'Case_InterferonSignature';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Interferon Signature';

$currentHeader = 'Case_LymphocyteCountGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Lymphocyte Count Group';

$currentHeader = 'Case_MaternalDisease';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Maternal Disease';

$currentHeader = 'Case_MaternalTreatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Maternal Treatment';

$currentHeader = 'Case_Medium';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Medium';

$currentHeader = 'Case_MetabolicStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Metabolic Status';

$currentHeader = 'Case_MIType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case MI Type';

$currentHeader = 'Case_Molecule';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Molecule';

$currentHeader = 'Case_mtDNAHaplogroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case mtDNA Haplo Group';

$currentHeader = 'Case_MutationStatus_15q11_2_q13';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Status (15q11.2-q13)';

$currentHeader = 'Case_MutationStatus_TREX1';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Status (TREX1)';

$currentHeader = 'Case_MutationType_16p11_2';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (16p11.2)';

$currentHeader = 'Case_MutationType_BRAF';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (BRAF)';

$currentHeader = 'Case_MutationType_CDKN2A';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (CDKN2A)';

$currentHeader = 'Case_MutationType_GBA';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (GBA)';

$currentHeader = 'Case_MutationType_HNRNPA2B1';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (HNRNPA2B1)';

$currentHeader = 'Case_MutationType_LRRK2';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (LRRK2)';

$currentHeader = 'Case_MutationType_MC1R';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (MC1R)';

$currentHeader = 'Case_MutationType_NR3C1';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (NR3C1)';

$currentHeader = 'Case_MutationType_PARK2';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (PARK2)';

$currentHeader = 'Case_MutationType_SNCA';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (SNCA)';

$currentHeader = 'Case_MutationType_VCP';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Mutation Type (VCP)';

$currentHeader = 'Case_NeuronalDDR';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Neuronal DDR';

$currentHeader = 'Case_OrganismPart';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Organism Part';

$currentHeader = 'Case_Outcome_DeathOrTransplant';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Outcome (Death or Transplant)';

$currentHeader = 'Case_ParisAgeGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Paris Age Group';

$currentHeader = 'Case_Phenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Phenotype';

$currentHeader = 'Case_PhosphateConcentration';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Phosphate Concentration';

$currentHeader = 'Case_PlasmaClinicalPhenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Plasma Clinical Phenotype';

$currentHeader = 'Case_PlasmaDiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Plasma Disease State';

$currentHeader = 'Case_PlasmaSamplingTime_months';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Plasma Sampling Time (Months)';

$currentHeader = 'Case_Population';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Population';

$currentHeader = 'Case_PreTreatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case PreTreatment';

$currentHeader = 'Case_ReadType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Read Type';

$currentHeader = 'Case_Response';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Response';

$currentHeader = 'Case_ResponseGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Response Group';

$currentHeader = 'Case_ResponseWeek8';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Response (Week 8)';

$currentHeader = 'Case_RF';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case RF';

$currentHeader = 'Case_Risk';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Risk';

$currentHeader = 'Case_rs10846744';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case rs10846744';

$currentHeader = 'Case_rs1333049';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case rs1333049';

$currentHeader = 'Case_SampleIDs';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sample IDs';

$currentHeader = 'Case_SampleMaterial';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sample Material';

$currentHeader = 'Case_SamplePathology';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sample Pathology';

$currentHeader = 'Case_SampleSource';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sample Source';

$currentHeader = 'Case_SamplingTime';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time';

$currentHeader = 'Case_SamplingTime_days';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time (days)';

$currentHeader = 'Case_SamplingTime_dpi';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time (dpi)';

$currentHeader = 'Case_SamplingTime_hours';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time (hours)';

$currentHeader = 'Case_SamplingTime_hpi';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time (hpi)';

$currentHeader = 'Case_SamplingTime_minutes';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time (minutes)';

$currentHeader = 'Case_SamplingTime_weeks';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Sampling Time (weeks)';

$currentHeader = 'Case_SerumClinicalPhenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Serum Clinical Phenotype';

$currentHeader = 'Case_SerumDiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Serum Disease State';

$currentHeader = 'Case_SerumSamplingTime_months';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Serum Sampling Time (months)';

$currentHeader = 'Case_SmokingStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Smoking Status';

$currentHeader = 'Case_SocioeconomicStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Socioeconomic Status';

$currentHeader = 'Case_SourceName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Source Name';

$currentHeader = 'Case_SubjectGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Subject Group';

$currentHeader = 'Case_SubjectInfection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Subject Infection';

$currentHeader = 'Case_SubjectSubgroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Subject Subgroup';

$currentHeader = 'Case_SubjectTreatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Subject Treatment';

$currentHeader = 'Case_Symptom';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Symptom';

$currentHeader = 'Case_SymptomSeverity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Symptom Severity';

$currentHeader = 'Case_SystemicInflammation';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Systemic Inflammation';

$currentHeader = 'Case_TetramerStaining_myelin';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Tetramer Staining (myelin)';

$currentHeader = 'Case_TFC_Stage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case TFC Stage';

$currentHeader = 'Case_Tissue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Tissue';

$currentHeader = 'Case_TissueDescription';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Tissue Description';

$currentHeader = 'Case_TissueGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Tissue Group';

$currentHeader = 'Case_TissueRegion';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Tissue Region';

$currentHeader = 'Case_TNFInadequateResponder';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case TNF Inadequate Responder';

$currentHeader = 'Case_Transfection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Transfection';

$currentHeader = 'Case_Treatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment';

$currentHeader = 'Case_Treatment_Dose';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Dose';

$currentHeader = 'Case_TreatmentGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Group';

$currentHeader = 'Case_TreatmentProtocol';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Protocol';

$currentHeader = 'Case_TreatmentStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Status';

$currentHeader = 'Case_TreatTime';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Time';

$currentHeader = 'Case_TreatTime_days';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Time (days)';

$currentHeader = 'Case_TreatTime_hours';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Time (hours)';

$currentHeader = 'Case_TreatTime_minutes';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Time (minutes)';

$currentHeader = 'Case_TreatTime_months';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Treatment Time (months)';

$currentHeader = 'Case_Vaccine';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Vaccine';

$currentHeader = 'Case_VirusStrain';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Virus Strain';

$currentHeader = 'Case_VitalStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Case Vital Status';

$currentHeader = 'Collection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Collection';

$currentHeader = 'ComparisonCategory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Comparison Category';

$currentHeader = 'ComparisonContrast';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Comparison Contrast';

$currentHeader = 'ComparisonType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Comparison Type';

$currentHeader = 'Control_ACR70';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control ACR70';

$currentHeader = 'Control_AgeCategory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Age Category';

$currentHeader = 'Control_Allergen';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Allergen';

$currentHeader = 'Control_APOEStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control APOE Status';

$currentHeader = 'Control_BacteriaMorphology';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Bacteria Morphology';

$currentHeader = 'Control_BacteriaSource';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Bacteria Source';

$currentHeader = 'Control_BacteriaStrain';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Bacteria Strain';

$currentHeader = 'Control_BaselineSeverity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Baseline Severity';

$currentHeader = 'Control_BirthWeight';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Birth Weight';

$currentHeader = 'Control_BraakStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Braak Stage';

$currentHeader = 'Control_CellDescription';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cell Description';

$currentHeader = 'Control_CellLine';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cell Line';

$currentHeader = 'Control_CellMarkers';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cell Markers';

$currentHeader = 'Control_CellPassage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cell Passage';

$currentHeader = 'Control_CellSubgroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cell Subgroup';

$currentHeader = 'Control_CellType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cell Type';

$currentHeader = 'Control_ClinicalGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Clinical Group';

$currentHeader = 'Control_ClinicalPhase';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Clinical Phase';

$currentHeader = 'Control_ClinicalPhenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Clinical Phenotype';

$currentHeader = 'Control_Cohort';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Cohort';

$currentHeader = 'Control_COPD';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control COPD';

$currentHeader = 'Control_CultureCondition';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Culture Condition';

$currentHeader = 'Control_Description';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Description';

$currentHeader = 'Control_DiabetesReversalStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Diabetes Reversal Status';

$currentHeader = 'Control_Diet';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Diet';

$currentHeader = 'Control_DifferentiationStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Differentiation Stage';

$currentHeader = 'Control_DiseaseHistory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease History';

$currentHeader = 'Control_DiseaseOnsetFstl1Level';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease Onset FSTL-1 Level';

$currentHeader = 'Control_DiseaseOnsetType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease OnsetType';

$currentHeader = 'Control_DiseaseRiskHaplotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease Risk Haplotype';

$currentHeader = 'Control_DiseaseStage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease Stage';

$currentHeader = 'Control_DiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease State';

$currentHeader = 'Control_DiseaseSubtype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Disease Subtype';

$currentHeader = 'Control_Donor';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Donor';

$currentHeader = 'Control_Dosage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Dosage';

$currentHeader = 'Control_Dosage_Gy';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Dosage (Gy)';

$currentHeader = 'Control_Ethnicity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Ethnicity';

$currentHeader = 'Control_FamilyHistory';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Family History';

$currentHeader = 'Control_FamilyHistory_PD';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Family History (PD)';

$currentHeader = 'Control_FVCGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control FVCGroup';

$currentHeader = 'Control_Gender';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Gender';

$currentHeader = 'Control_GeneticModification';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Genetic Modification';

$currentHeader = 'Control_Genotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Genotype';

$currentHeader = 'Control_GestationalAge_weeks';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Gestational Age (weeks)';

$currentHeader = 'Control_Haplotype_IL7R';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Haplotype IL7R';

$currentHeader = 'Control_IgM_MC';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control IgM MC';

$currentHeader = 'Control_Infection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Infection';

$currentHeader = 'Control_InfectionGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Infection Group';

$currentHeader = 'Control_InflammatoryComplications';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Inflammatory Complications';

$currentHeader = 'Control_InterferonSignature';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Interferon Signature';

$currentHeader = 'Control_LymphocyteCountGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Lymphocyte Count Group';

$currentHeader = 'Control_MaternalDisease';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Maternal Disease';

$currentHeader = 'Control_MaternalTreatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Maternal Treatment';

$currentHeader = 'Control_Medium';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Medium';

$currentHeader = 'Control_MetabolicStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Metabolic Status';

$currentHeader = 'Control_MIType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control MI Type';

$currentHeader = 'Control_Molecule';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Molecule';

$currentHeader = 'Control_mtDNAHaplogroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control mtDNA Haplogroup';

$currentHeader = 'Control_MutationStatus_15q11_2_q13';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Status (15q11.2-q13)';

$currentHeader = 'Control_MutationStatus_TREX1';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Status (TREX1)';

$currentHeader = 'Control_MutationType_16p11_2';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (16p11.2)';

$currentHeader = 'Control_MutationType_BRAF';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (BRAF)';

$currentHeader = 'Control_MutationType_CDKN2A';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (CDKN2A)';

$currentHeader = 'Control_MutationType_GBA';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (GBA)';

$currentHeader = 'Control_MutationType_HNRNPA2B1';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (HNRNPA2B1)';

$currentHeader = 'Control_MutationType_LRRK2';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (LRRK2)';

$currentHeader = 'Control_MutationType_MC1R';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (MC1R)';

$currentHeader = 'Control_MutationType_NR3C1';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (NR3C1)';

$currentHeader = 'Control_MutationType_PARK2';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (PARK2)';

$currentHeader = 'Control_MutationType_SNCA';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (SNCA)';

$currentHeader = 'Control_MutationType_VCP';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Mutation Type (VCP)';

$currentHeader = 'Control_NeuronalDDR';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Neuronal DDR';

$currentHeader = 'Control_OrganismPart';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Organism Part';

$currentHeader = 'Control_Outcome_DeathOrTransplant';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Outcome (Death or Transplant)';

$currentHeader = 'Control_ParisAgeGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Paris Age Group';

$currentHeader = 'Control_Phenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Phenotype';

$currentHeader = 'Control_PhosphateConcentration';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Phosphate Concentration';

$currentHeader = 'Control_PlasmaClinicalPhenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Plasma Clinical Phenotype';

$currentHeader = 'Control_PlasmaDiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Plasma Disease State';

$currentHeader = 'Control_PlasmaSamplingTime_months';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Plasma Sampling Time (months)';

$currentHeader = 'Control_Population';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Population';

$currentHeader = 'Control_PreTreatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Pre Treatment';

$currentHeader = 'Control_ReadType';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Read Type';

$currentHeader = 'Control_Response';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Response';

$currentHeader = 'Control_ResponseGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Response Group';

$currentHeader = 'Control_ResponseWeek8';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Response (Week 8)';

$currentHeader = 'Control_RF';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control RF';

$currentHeader = 'Control_Risk';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Risk';

$currentHeader = 'Control_rs10846744';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control rs10846744';

$currentHeader = 'Control_rs1333049';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control rs1333049';

$currentHeader = 'Control_SampleIDs';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sample IDs';

$currentHeader = 'Control_SampleMaterial';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sample Material';

$currentHeader = 'Control_SamplePathology';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sample Pathology';

$currentHeader = 'Control_SampleSource';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sample Source';

$currentHeader = 'Control_SamplingTime';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time';

$currentHeader = 'Control_SamplingTime_days';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time (days)';

$currentHeader = 'Control_SamplingTime_dpi';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time (dpi)';

$currentHeader = 'Control_SamplingTime_hours';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time (hours)';

$currentHeader = 'Control_SamplingTime_hpi';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time (hpi)';

$currentHeader = 'Control_SamplingTime_minutes';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time (minutes)';

$currentHeader = 'Control_SamplingTime_weeks';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Sampling Time (weeks)';

$currentHeader = 'Control_SerumClinicalPhenotype';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Serum Clinical Phenotype';

$currentHeader = 'Control_SerumDiseaseState';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Serum Disease State';

$currentHeader = 'Control_SerumSamplingTime_months';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Serum Sampling Time (months)';

$currentHeader = 'Control_SmokingStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Smoking Status';

$currentHeader = 'Control_SocioeconomicStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Socioeconomic Status';

$currentHeader = 'Control_SourceName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Source Name';

$currentHeader = 'Control_SubjectGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Subject Group';

$currentHeader = 'Control_SubjectInfection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Subject Infection';

$currentHeader = 'Control_SubjectSubgroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Subject Subgroup';

$currentHeader = 'Control_SubjectTreatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Subject Treatment';

$currentHeader = 'Control_Symptom';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Symptom';

$currentHeader = 'Control_SymptomSeverity';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Symptom Severity';

$currentHeader = 'Control_SystemicInflammation';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Systemic Inflammation';

$currentHeader = 'Control_TetramerStaining_myelin';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Tetramer Staining (myelin)';

$currentHeader = 'Control_TFC_Stage';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control TFC Stage';

$currentHeader = 'Control_Tissue';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Tissue';

$currentHeader = 'Control_TissueDescription';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Tissue Description';

$currentHeader = 'Control_TissueGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Tissue Group';

$currentHeader = 'Control_TissueRegion';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Tissue Region';

$currentHeader = 'Control_TNFInadequateResponder';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control TNF Inadequate Responder';

$currentHeader = 'Control_Transfection';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Transfection';

$currentHeader = 'Control_Treatment';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment';

$currentHeader = 'Control_Treatment_Dose';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment (Dose)';

$currentHeader = 'Control_TreatmentGroup';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Group';

$currentHeader = 'Control_TreatmentProtocol';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Protocol';

$currentHeader = 'Control_TreatmentStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Status';

$currentHeader = 'Control_TreatTime';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Time';

$currentHeader = 'Control_TreatTime_days';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Time (days)';

$currentHeader = 'Control_TreatTime_hours';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Time (hours)';

$currentHeader = 'Control_TreatTime_minutes';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Time (minutes)';

$currentHeader = 'Control_TreatTime_months';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Treatment Time (months)';

$currentHeader = 'Control_Vaccine';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Vaccine';

$currentHeader = 'Control_VirusStrain';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Virus Strain';

$currentHeader = 'Control_VitalStatus';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Control Vital Status';

$currentHeader = 'GeneModelID';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Gene Model ID';

$currentHeader = 'Length';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Length';

$currentHeader = 'Organism';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Organism';

$currentHeader = 'PlatformName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Platform Name';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Required']		= 1;
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Optional_if_Single_Project'] = 1;

$currentHeader = 'ProjectName';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Project Name';


$currentHeader = 'SampleDataMode';
$APP_CONFIG['Internal_Data'][$currentTable]['Headers'][$currentHeader]['Name']			= 'Sample Data Mode';

?>