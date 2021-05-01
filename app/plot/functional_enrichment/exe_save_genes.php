<?php

  $term_name           = $_POST['pathway'];
  $COMPARISON_INDEX    = $_POST['comparison'];

  $file_name = $_POST['chart_name'] . '.txt';
  $direction = $_POST['direction'];

  if (isset($_POST['inhouse']) && $_POST['inhouse'] == 'true') {
    $dir = $BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/inhouse_comp_' . $COMPARISON_INDEX . '/cleaned_data.csv_GO_Analysis_' . $direction;
  } else {
    $dir = internal_data_get_comparison_directory($COMPARISON_INDEX) . '/comp_' . $COMPARISON_INDEX . '_GO_Analysis_' . $direction;
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

  $genes_list = explode(",", $GENE_NAMES);
  $uniqueID = md5(microtime(true));
  $_SESSION['List'][$uniqueID] = $genes_list;
  echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Gene&Session={$uniqueID}";
  
?>