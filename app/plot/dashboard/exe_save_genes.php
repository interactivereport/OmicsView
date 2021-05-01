<?php


  // print_r($_POST);

  $GENES = explode(",", $_POST['gene_list']);
  if (!is_array($GENES) || count($GENES) <= 0) {
    echo 'Error: No gene selected.';
    exit();
  }

  $name_list = array();
  foreach ($GENES as $index) {
    $sql = "SELECT `GeneID` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
            WHERE `GeneIndex`=" . $index;
    $name_list[] = $DB -> get_one($sql);
  }
  $uniqueID = md5(microtime(true));
  $_SESSION['List'][$uniqueID] = $name_list;
  echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Gene&Session={$uniqueID}";


  // if (!isset($_SESSION['SAVED_GENE'])
  //     || !is_array($_SESSION['SAVED_GENE'])) {
  //   session_start();
  //   $_SESSION['SAVED_GENE'] = $GENES;
  // }
  //
  // else {
  //   $_SESSION['SAVED_GENE'] = array_unique(array_merge($_SESSION['SAVED_GENE'], $GENES));
  // }

  exit();


?>