<?php

  // print_r($_POST);
  $comparisons  = explode(',', $_POST['comparisons']);
  $uniqueID     = md5(microtime(true));
  unset($_SESSION['Active_Comparisons']);
  $_SESSION['Active_Comparisons'][$uniqueID] = $comparisons;
  echo $uniqueID;
  exit();
  
 ?>