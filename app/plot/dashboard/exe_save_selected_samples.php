<?php


  // print_r($_POST); exit();

  $COMPARISONS = $_POST['comparisons'];
  if (!is_array($COMPARISONS) || count($COMPARISONS) <= 0) {
    echo 'Error: No comparison selected.';
    exit();
  }

  $name_list = array();
  $sql = "SELECT `Case_SampleIDs`, `Control_SampleIDs` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
          WHERE `ComparisonIndex` IN ('" . implode("','", $COMPARISONS) . "')";
  $data = $DB -> get_all($sql);
  foreach ($data as $row) {
    $row_sample_list = explode(';', $row['Case_SampleIDs']);
    foreach ($row_sample_list as $sampe_id) {
      if (!in_array($sampe_id, $name_list)) {
        $name_list[] = $sampe_id;
      }
    }
    $row_sample_list = explode(';', $row['Control_SampleIDs']);
    foreach ($row_sample_list as $sampe_id) {
      if (!in_array($sampe_id, $name_list)) {
        $name_list[] = $sampe_id;
      }
    }
  }

  $uniqueID = md5(microtime(true));
  $_SESSION['List'][$uniqueID] = $name_list;
  echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Sample&Session={$uniqueID}";

  exit();


?>