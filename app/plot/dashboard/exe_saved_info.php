<?php
include_once('../assets/config.php');

if (isset($_GET['action']) && $_GET['action'] == 'delete_session') {
  // print_r($_POST);
  $TYPE = $_POST['type'];
  $INDEX = $_POST['index'];

  foreach ($_SESSION['SAVED_' . strtoupper($TYPE)] as $key => $value) {
    if ($value == $INDEX) {
      unset($_SESSION['SAVED_' . strtoupper($TYPE)][$key]);
    }
  }
  exit();
}



if (isset($_GET['action']) && $_GET['action'] == 'clear_session') {
  unset($_SESSION['SAVED_GENE']);
  unset($_SESSION['SAVED_COMPARISON']);
  unset($_SESSION['SAVED_SAMPLE']);
  exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'clear_session_single') {
  unset($_SESSION['SAVED_' . strtoupper($_POST['type'])]);
  exit();
}


?>
