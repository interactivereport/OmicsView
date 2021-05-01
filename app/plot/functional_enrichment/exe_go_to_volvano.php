<?php



  $COMPARISON_INDEX = $_POST['index'];
  $file_name = $_POST['file_name'] . '.txt';
  $term_name = $_POST['name'];
  $direction = $_POST['direction'];
  $inhouse = $_POST['inhouse'];
  

  // // If it's in-house comparison
  if (isset($_POST['inhouse']) && $_POST['inhouse'] == 'true') {
    $dir = $BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/inhouse_comp_' . $COMPARISON_INDEX . '/cleaned_data.csv_GO_Analysis_' . $direction;
  } else {
    $dir = internal_data_get_comparison_directory($COMPARISON_INDEX) . "comp_{$COMPARISON_INDEX}_GO_Analysis_{$direction}";
  }

  if (!file_exists($dir . '/' . $file_name)) {
    echo 'Error: File "' . $dir . '/' . $file_name . '" does not exist. Please contact the webmaster.';
    exit();
  }


  $myfile = fopen($dir . '/' . $file_name, "r") or die("Unable to open file!");
  $GENE_NAMES = '';
  while(!feof($myfile)) {
      $row_content = explode("\t", fgets($myfile));
      if ($row_content[1] == $term_name) {
        $GENE_NAMES = $row_content[10];
        break;
      }
  }
  fclose($myfile);


  if ($GENE_NAMES == '') {
    echo 'Error: No term "' . $term_name . '" found in file "' . $dir . '/' . $file_name . '".';
    exit();
  }


  $dir_genes = $BXAF_CONFIG['USER_FILES_GO'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  if (!is_dir($dir_genes)) {
    mkdir($dir_genes, 0755, true);
  }
  file_put_contents($dir_genes . '/selected_genes.txt', $GENE_NAMES);

  exit();
  
?>