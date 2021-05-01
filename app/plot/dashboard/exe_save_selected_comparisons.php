<?php


  // print_r($_POST);

  $COMPARISONS = $_POST['comparisons'];
  if (!is_array($COMPARISONS) || count($COMPARISONS) <= 0) {
    echo 'Error: No comparison selected.';
    exit();
  }

  $name_list = array();
  foreach ($COMPARISONS as $index) {
    $sql = "SELECT `ComparisonID` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
            WHERE `ComparisonIndex`=" . $index;
    $name_list[] = $DB -> get_one($sql);
  }
  $uniqueID = md5(microtime(true));
  $_SESSION['List'][$uniqueID] = $name_list;
  echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Comparison&Session={$uniqueID}";

  exit();


?>