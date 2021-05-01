<?php


  $content = file_get_contents('comparisons.json');

  header('Content-Type: application/json');
  $json = $json = json_decode($content, true);
  echo json_encode($json);
  exit();


?>