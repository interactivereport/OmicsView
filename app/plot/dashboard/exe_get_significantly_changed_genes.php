<?php


  // print_r($_POST); exit();


  $COMPARISON_ID_LIST = array();
  foreach ($_POST['comparisons'] as $comparison_index) {
    if (trim($comparison_index) != '') {
      $sql = "SELECT `ComparisonID` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
              WHERE `ComparisonIndex`=" . $comparison_index;
      $comparison_id = $DB -> get_one($sql);
      $COMPARISON_ID_LIST[] = $comparison_id;
    }
  }
  if (count($COMPARISON_ID_LIST) == 0) {
    echo 'Error: No comparison is selected.';
    exit();
  }

  $dir = $BXAF_CONFIG['USER_FILES_DASHBOARD'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
  }
  file_put_contents($dir . '/comparisons.txt', implode("|", $COMPARISON_ID_LIST));
  exit();


?>