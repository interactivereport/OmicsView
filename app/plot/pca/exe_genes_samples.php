<?php

include_once('config.php');
include_once('../profile/config.php');


//********************************************************************************************
// Get Genes & Samples
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'get_gene_sample_data') {
	
	
  // print_r($_POST);
  header('Content-Type: application/json');
  $OUTPUT['type'] = 'Error';
  $TIME           = time();
  $GENES          = trim($_POST['genes']);
  $SAMPLES        = trim($_POST['samples']);
  if ($SAMPLES == '') {
    $OUTPUT['detail'] = 'Please enter valid gene names and sample names.';
    echo json_encode($OUTPUT);
    exit();
  }

  $GENES   = general_array_clean(explode("\n", $GENES));
  $SAMPLES = general_array_clean(explode("\n", $SAMPLES));

  $GENE_NAME_LOOKUP = array();
  $sql = "SELECT `GeneIndex`, `GeneID`
          FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`";
  $all_genes = $DB -> get_all($sql);
  foreach ($all_genes as $value) {
    $GENE_NAME_LOOKUP[$value['GeneIndex']] = $value['GeneID'];
  }

  $GENE_NAME_LOOKUP_FLIP = array_flip($GENE_NAME_LOOKUP);
  $GENE_INDEXES = array();
  foreach ($GENES as $geneid) {
    $GENE_INDEXES[] = $GENE_NAME_LOOKUP_FLIP[$geneid];
  }
  $SAMPLE_INDEXES = general_array_clean(array_map('search_sample_index', $SAMPLES));


	foreach($SAMPLE_INDEXES as $tempKey => $tempValue){
	
		if (!internal_data_is_public($tempValue)){
			$hasPrivateData = true;	
		} else {
			$hasPublicData = true;	
		}
	}



  $SAMPLE_PLATFORM = get_sample_platform($SAMPLE_INDEXES);
  if (!$SAMPLE_PLATFORM) {
    $OUTPUT['detail'] = 'Mixed sample type is not allowed.';
    echo json_encode($OUTPUT);
    exit();
  }
  if ($SAMPLE_PLATFORM == 'RNA-Seq') {
    $tabix_table = 'GeneFPKM';
    $tabix_colname = 'FPKM';
  } else {
    $tabix_table = 'GeneLevelExpression';
    $tabix_colname = 'Value';
  }

  if (count($SAMPLE_INDEXES) == '') {
    $OUTPUT['detail'] = 'No valid gene or sample. Please revise';
    echo json_encode($OUTPUT);
    exit();
  }


  //----------------------------------------------------------------------------------------
  // Generate R Input CSV
  //
  // $dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  $dir = "{$BXAF_CONFIG['USER_FILES_PCA']}/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/{$TIME}";
  if (!is_dir($dir)) mkdir($dir, 0755, true);

  $file = fopen("{$dir}/genes_samples.csv","w");
  // Header (Sample Names)
  $first_row = array_merge(array("GeneName"), $SAMPLES);
  fputcsv($file, $first_row);
  // Main Data

  //**************************************************************************

  if (!is_array($GENE_INDEXES) || count($GENE_INDEXES) == 0 || $GENE_INDEXES[0] == '') {
    $GENE_INDEXES = '';
    $DATA_MATRIX = array();
    foreach($GENE_NAME_LOOKUP as $geneindex => $geneid) {
		
		if ($geneindex == NULL) continue;
		
      $DATA_MATRIX[$geneindex] = array_merge(array($geneid), array_fill(0, count($SAMPLE_INDEXES), 'NA'));
    }
  } else {
    $DATA_MATRIX = array();
    foreach($GENE_INDEXES as $geneindex) {
		
		if ($geneindex == NULL) continue;

      $DATA_MATRIX[$geneindex] = array_merge(array($GENE_NAME_LOOKUP[$geneindex]), array_fill(0, count($SAMPLE_INDEXES), 'NA'));
    }
  }
  
  
	

  if ($hasPublicData){
	$results = tabix_search_records_with_index($GENE_INDEXES, $SAMPLE_INDEXES, $tabix_table);
  }
 
  
  if ($hasPrivateData){
	  
	$internal_data_split_multiple_data_by_source = internal_data_split_multiple_data_by_source($SAMPLE_INDEXES);
	  
	$sampleRecords = get_multiple_record('Sample', $internal_data_split_multiple_data_by_source['private'], 'GetAssoc');
	
	foreach($sampleRecords as $currentSampleIndex => $currentSampleRecord){
		
		$currentTabixResults = tabix_search_records_with_index_internal_data($currentSampleRecord['ProjectIndex'], $GENE_INDEXES, array($currentSampleIndex), $tabix_table);
		
		foreach($currentTabixResults as $tempKeyX => $tempValueX){
			$results[] = $tempValueX;	
		}
		
	}
  }
  

  
  $sample_index_flip = array_flip($SAMPLE_INDEXES);
  foreach ($results as $tabix_row) {
    $sample_index_key = $sample_index_flip[$tabix_row['SampleIndex']] + 1;
    $DATA_MATRIX[$tabix_row['GeneIndex']][$sample_index_key] = $tabix_row[$tabix_colname];
  }
  
  /*
  unset($DATA_MATRIX);
  foreach($results as $tempKey => $tabixRow){
	  
	  $geneIndex 	= $tabixRow['GeneIndex'];
	  $geneValue	= $tabixRow[$tabix_colname];
	  $sampleIndex	= $tabixRow['SampleIndex'];
	  
	  if (!isset($DATA_MATRIX[$geneIndex])){
		  
		  if ($GENE_NAME_LOOKUP[$geneIndex] == '') continue;
		 $DATA_MATRIX[$geneIndex]['Gene'] =  $GENE_NAME_LOOKUP[$geneIndex];
	  }
	  $DATA_MATRIX[$geneIndex]["Sample_{$sampleIndex}"] = $geneValue;
  }
  */
  
  
  
  
  
  foreach ($DATA_MATRIX as $row) {
    fputcsv($file, $row);
  }

  //print_r($DATA_MATRIX); exit();



  // print_r($results); exit();
  //
  // $index = 0;
  // $last_visited_gene = $results[0]['GeneIndex'];
  // $sample_index_flip = array_flip($SAMPLE_INDEXES);
  // $row_content = array_merge(array($GENE_NAME_LOOKUP[$last_visited_gene]), array_fill(0, count($SAMPLE_INDEXES), 'NA'));
  // foreach ($results as $row) {
  //   // If new gene
  //   if ($row['GeneIndex'] != $last_visited_gene) {
  //     fputcsv($file, $row_content);
  //     $last_visited_gene = $row['GeneIndex'];
  //     $row_content = array_merge(array($GENE_NAME_LOOKUP[$last_visited_gene]), array_fill(1, count($SAMPLE_INDEXES), 'NA'));
  //   }
  //
  //   $sample_index_key = $sample_index_flip[$row['SampleIndex']] + 1;
  //   $row_content[$sample_index_key] = $row[$tabix_colname];
  //
  // }


  //**************************************************************************





  fclose($file);
  chmod("{$dir}/genes_samples.csv", 0777);
  mkdir(dirname(__FILE__) . "/files/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}", 0755, true);
  copy("{$dir}/genes_samples.csv", dirname(__FILE__) . "/files/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/genes_samples.csv");


  //----------------------------------------------------------------------------------------
  // Generate Attributes CSV
  //
  // Remove files first
  foreach ($BXAF_CONFIG['PCA_R_FILE_LIST'] as $file) {
    unlink("{$dir}/{$file}");
  }
  $ATTR = $_POST['attr'];
  if (is_array($ATTR) && count($ATTR) > 0) {
    $file = fopen("{$dir}/PCA_attributes.csv","w");
    // Header
    $first_row = array_merge(array("SampleName"), $ATTR);
    fputcsv($file, $first_row);
    // Main Data
    foreach ($SAMPLE_INDEXES as $key => $sample_index) {
      $row = array();
	 $sample_info = get_one_record_by_index('sample',$sample_index);
	  
	  
	  
	  
	  
      $row[] = $sample_info['SampleID'];
	  
      foreach ($ATTR as $attr) {
        $row[] = $sample_info[$attr];
      }
      fputcsv($file, $row);
    }
    fclose($file);
  }


  //----------------------------------------------------------------------------------------
  // Generate R Script
  //
  $RCODE  = "";
  $RCODE .= "library(FactoMineR);\n";
  $RCODE .= "library(explor);\n";
  $RCODE .= "library(missMDA);\n";
  $RCODE .= "library(limma);\n";
  $RCODE .= "setwd('{$dir}');\n";
  $RCODE .= "data=read.csv('genes_samples.csv');\n";
  $RCODE .= "data1=data.matrix(data[, 2:ncol(data)]);\n";
  $RCODE .= "rownames(data1)=data[, 1]\n";
  $RCODE .= "sel=rowSums(is.na(data1)) < (ncol(data1)/2)\n";
  $RCODE .= "data1c=data1[sel, ]\n";
  $RCODE .= "sample_sel=( colSums(!is.na(data1c))>2)\n";
  $RCODE .= "cat('From', ncol(data1c), 'Samples, after removing those with fewer than 2 genes,', sum(sample_sel), 'samples left.\n')\n";
  $RCODE .= "data1c=data1c[, sample_sel]\n";
  $RCODE .= "Nfix=sum( rowSums(is.na(data1c))>0 )\n";
  $RCODE .= "cat(sum(sel), 'out of', nrow(data1), 'genes remain', Nfix, 'need to impute missing data.\n')\n";
  $RCODE .= 'if (Nfix>0) {' . "\n";
  $RCODE .= 'data1c <- imputePCA(data1c,ncp=2)$completeObs' . "\n";
  $RCODE .= 'min.value=min(data1, na.rm=T)' . "\n";
  $RCODE .= 'max.value=max(data1, na.rm=T)' . "\n";
  $RCODE .= 'data1c[data1c<min.value]=min.value' . "\n";
  $RCODE .= 'data1c[data1c>max.value]=max.value' . "\n";
  $RCODE .= '}' . "\n";
  $RCODE .= 'pca <- PCA(t(data1c), graph = FALSE);' . "\n";
  $RCODE .= "outdir='{$dir}';\n";
  $RCODE .= 'setwd(outdir);' . "\n";
  $RCODE .= 'write.csv(pca$eig, "PCA_barchart.csv");' . "\n";
  $RCODE .= 'write.csv(pca$var$coord, "PCA_var.coord.csv");' . "\n";
  $RCODE .= 'write.csv(pca$var$cor, "PCA_var.cor.csv");' . "\n";
  $RCODE .= 'write.csv(pca$var$cos2, "PCA_var.cos2.csv");' . "\n";
  $RCODE .= 'write.csv(pca$var$contrib, "PCA_var.contrib.csv");' . "\n";
  $RCODE .= 'write.csv(pca$ind$coord, "PCA_ind.coord.csv");' . "\n";
  $RCODE .= 'write.csv(pca$ind$cos2, "PCA_ind.cos2.csv");' . "\n";
  $RCODE .= 'write.csv(pca$ind$contrib, "PCA_ind.contrib.csv");' . "\n";
  $RCODE .= 'write.csv(pca$call$X, "PCA_input.data.csv");' . "\n";
  $RCODE .= 'write(deparse(pca$call$call), file="PCA_command.txt");' . "\n";




  file_put_contents("{$dir}/genes_samples.R", $RCODE);
  chmod("{$dir}/genes_samples.R", 0777);

  chdir($dir);
  bxaf_execute_in_background("R CMD BATCH genes_samples.R");

  foreach ($BXAF_CONFIG['PCA_R_FILE_LIST'] as $file) {
    chmod("{$dir}/{$file}", 0755);
  }

 


  //----------------------------------------------------------------------------------------
  // Output JSON Object
  $OUTPUT['type'] = 'Success';
  $OUTPUT['time'] = $TIME;
  echo json_encode($OUTPUT);
  exit();
}



function search_sample_index($sample_name) {
  global $BXAF_CONFIG, $DB;
  
  $sample_name = trim($sample_name);
  if ($sample_name == '') return '';
  
  $sql = "SELECT `SampleIndex`
          FROM `App_User_Data_Samples_Combined`
          WHERE `SampleID`='{$sample_name}'";

  $sample_index = $DB -> get_one($sql);
  
  if (!isset($sample_index) || trim($sample_index) == ''){
  
	 $sql = "SELECT `SampleIndex`
          FROM `App_User_Data_Samples`
          WHERE `SampleID`='{$sample_name}'";

  	$sample_index = $DB -> get_one($sql); 
	  
  }
  
  if (!isset($sample_index) || trim($sample_index) == '') $sample_index = '';
  
  
  return $sample_index;
}


function get_sample_platform($sample_indexes) {
	
	if (get_gene_type() == 'Protein'){
		return 'RNA-Seq';	
	}
	
  global $BXAF_CONFIG, $DB;

  $last_platform = '';

  foreach ($sample_indexes as $sample_index) {
	  $sample_index = intval($sample_index);
	  
    $sql = "SELECT `PlatformName`
            FROM `{$BXAF_CONFIG['TBL_SAMPLES']}`
            WHERE `SampleIndex`={$sample_index}";
    $platform_name = $DB -> get_one($sql);
	
	if ($platform_name == ''){
		$sql = "SELECT `PlatformName`
            FROM `App_User_Data_Samples`
            WHERE `SampleIndex`={$sample_index}";
    	$platform_name = $DB -> get_one($sql);
	}
	
	
    $platform_name = strtolower(trim($platform_name));
	


    if (strpos($platform_name, 'ngs') === 0){
    	$current_platform = 'RNA-Seq';
    } elseif (strpos($platform_name, 'gpl18460') === 0){
    	$current_platform = 'RNA-Seq';
    } else {
    	$current_platform = 'Microarray';
    }

    if ($current_platform == $last_platform || $last_platform == '') {
      $last_platform = $current_platform;
    } else {
      return false;
    }
    $last_platform = $current_platform;
  }

  return $last_platform;
}
