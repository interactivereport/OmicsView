<?php

include_once('config.php');

//********************************************************************************************
// Upload 3 Files
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'upload_files') {

  // echo '<pre>'; print_r($_FILES); echo '</pre>'; exit();
  $format = ($_POST['format_files'] == 'txt') ? 'txt' : 'csv';
  $OUTPUT = array('type' => 'Error');
  header('Content-Type: application/json');

  //--------------------------------------------------------------------------------------------
  // Check Files
  $data_exists       = false;
  $attributes_exists = false;
  $var_exists        = false;
  $data_key          = -1;
  $attributes_key    = -1;
  $var_key           = -1;

  // foreach ($_FILES['file']['name'] as $key => $name) {
  //   if (substr($name, 0, 8) == 'pca_data') {
  //     $data_exists = true; $data_key = $key;
  //   }
  //   if (substr($name, 0, 14) == 'pca_attributes') {
  //     $attributes_exists = true;
  //     $attributes_key = $key;
  //   }
  //   if (substr($name, 0, 7) == 'pca_var') {
  //     $var_exists = true;
  //     $var_key = $key;
  //   }
  // }
  if (!isset($_FILES["file1"]) || !is_array($_FILES["file1"])) {
    $OUTPUT['detail'] = 'Data file is not uploaded.';
    echo json_encode($OUTPUT);
    exit();
  }


  //----------------------------------------------------------------------------------------
  // Upload Files
  //----------------------------------------------------------------------------------------
  //
  // File 1
  //

  if ($_FILES["file1"]["error"] === 0) {
    $tmp_name       = $_FILES["file1"]["tmp_name"];
    $name_1         = $_FILES["file1"]["name"];
    $size           = $_FILES["file1"]["size"];
    $type           = $_FILES["file1"]["type"];
    $dir            = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    if (!is_dir($dir . '/')) {
      mkdir($dir, 0755, true);
      chmod($dir, 0755);
    }
    copy($tmp_name, $dir . '/pca.' . $format);
  }

  if ($_FILES["file2"]["error"] === 0) {
    $tmp_name       = $_FILES["file2"]["tmp_name"];
    $name_2         = $_FILES["file2"]["name"];
    $size           = $_FILES["file2"]["size"];
    $type           = $_FILES["file2"]["type"];
    $dir            = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    if (!is_dir($dir . '/')) {
      mkdir($dir, 0755, true);
      chmod($dir, 0755);
    }
    copy($tmp_name, $dir . '/pca_attributes.' . $format);
  }

  if ($_FILES["file3"]["error"] === 0) {
    $tmp_name       = $_FILES["file3"]["tmp_name"];
    $name_3         = $_FILES["file3"]["name"];
    $size           = $_FILES["file3"]["size"];
    $type           = $_FILES["file3"]["type"];
    $dir            = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    if (!is_dir($dir . '/')) {
      mkdir($dir, 0755, true);
      chmod($dir, 0755);
    }
    copy($tmp_name, $dir . '/pca_var.' . $format);
  }




  //----------------------------------------------------------------------------------------
  // Read File & Get Header
  //----------------------------------------------------------------------------------------
  //
  // File 1
  //
  if (file_exists($dir . '/pca.' . $format)) {
    $file = fopen($dir . '/pca.' . $format, "r") or die('No file.');
    $file_1_data = array();
    $delimiter = ($format == 'txt') ? "\t" : ",";
    while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
      if (!is_array($row) || count($row) <= 1) {
        $OUTPUT['detail'] = 'Data file is not in correct format.';
        echo json_encode($OUTPUT);
        exit();
      }
      $file_1_data[] = $row;
    }
    fclose($file);
    unset($_SESSION['PCA_DIMENSION_VAR']);
  } else {
    $OUTPUT['detail'] = 'Data file is not in correct format.';
    $OUTPUT['detail2'] = $dir . '/pca.' . $format;
    echo json_encode($OUTPUT);
    exit();
  }
  //
  // File 2
  //
  $file_2_data = array();
  if (file_exists($dir . '/pca_attributes.' . $format)) {
    $file = fopen($dir . '/pca_attributes.' . $format, "r") or die('No file.');
    $delimiter = ($format == 'txt') ? "\t" : ",";
    while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
      if (!is_array($row) || count($row) <= 1) {
        $OUTPUT['detail'] = 'Attributes file is not in correct format.';
        echo json_encode($OUTPUT);
        exit();
      }
      $file_2_data[] = $row;
    }
    fclose($file);
  }
  //
  // File 3
  //
  $file_3_data = array();
  if (file_exists($dir . '/pca_var.' . $format)) {
    $file = fopen($dir . '/pca_var.' . $format, "r") or die('No file.');
    $delimiter = ($format == 'txt') ? "\t" : ",";
    $_SESSION['PCA_DIMENSION_VAR'] = array();
    $index = 0;
    while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
      // header
      if (!is_array($row) || count($row) <= 1) {
        $OUTPUT['detail'] = 'Variance file is not in correct format.';
        echo json_encode($OUTPUT);
        exit();
      }
      if ($index == 0) {
        $headers = $row;
      }
      if (trim($row[0]) != '') {
        $file_3_data[] = $row;
        $_SESSION['PCA_DIMENSION_VAR'][$row[0]] = $row[1];
      }
      $index++;
    }
    fclose($file);
    // Check colname
    $exist = false;
    foreach ($headers as $header) {
      if (trim($header) == 'percentage of variance') {
        $exist = true;
      }
    }
    if (!$exist) {
      $OUTPUT['type'] = 'Error';
      $OUTPUT['detail'] = 'Colname not matched';
      echo json_encode($OUTPUT);
      exit();
    }
  }



  //----------------------------------------------------------------------------------------
  // Output Info
  $OUTPUT['type']                = 'Success';
  $OUTPUT['name_1']              = $name_1;
  $OUTPUT['name_2']              = $name_2;
  $OUTPUT['name_3']              = $name_3;
  $OUTPUT['header_1']            = array_slice($file_1_data[0], 1);
  $OUTPUT['header_2']            = array_slice($file_2_data[0], 1);
  $OUTPUT['data_3']              = $file_3_data;
  echo json_encode($OUTPUT);
  // unset($_SESSION['PCA_DIMENSION_VAR']);





  exit();
}










//********************************************************************************************
// Upload File Step 1
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'upload_file_step_1') {
  // echo '<pre>'; print_r($_FILES); echo '</pre>';

  $format = ($_POST['format_step_1'] == 'txt') ? 'txt' : 'csv';
  $OUTPUT = array('type' => 'Success');
  header('Content-Type: application/json');

  //----------------------------------------------------------------------------------------
  // Upload File
  if ($_FILES["file"]["error"] === 0) {
    $tmp_name       = $_FILES["file"]["tmp_name"];
    $name           = $_FILES["file"]["name"];
    $size           = $_FILES["file"]["size"];
    $type           = $_FILES["file"]["type"];
    $dir            = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    if (!is_dir($dir . '/')) {
      mkdir($dir, 0755, true);
      chmod($dir, 0755);
    }
    copy($tmp_name, $dir . '/pca.' . $format);
  }



  //----------------------------------------------------------------------------------------
  // Read File & Get Header
  $file = fopen($dir . '/pca.' . $format, "r") or die('No file.');
  $file_data = array();
  $delimiter = ($format == 'txt') ? "\t" : ",";
  while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
    $file_data[] = $row;
  }
  fclose($file);



  //----------------------------------------------------------------------------------------
  // Output Info
  $OUTPUT['name']                = $name;
  $OUTPUT['header']              = array_slice($file_data[0], 1);
  echo json_encode($OUTPUT);
  unset($_SESSION['PCA_DIMENSION_VAR']);

  exit();
}





//********************************************************************************************
// Upload File Step 2
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'upload_file_step_2') {
  // echo '<pre>'; print_r($_FILES); echo '</pre>';

  $format = ($_POST['format_step_2'] == 'txt') ? 'txt' : 'csv';
  $OUTPUT = array('type' => 'Success');
  header('Content-Type: application/json');

  //----------------------------------------------------------------------------------------
  // Upload File
  if ($_FILES["file"]["error"] === 0) {
    $tmp_name       = $_FILES["file"]["tmp_name"];
    $name           = $_FILES["file"]["name"];
    $size           = $_FILES["file"]["size"];
    $type           = $_FILES["file"]["type"];
    $dir            = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    if (!is_dir($dir . '/')) {
      mkdir($dir, 0755, true);
      chmod($dir, 0755);
    }
    copy($tmp_name, $dir . '/pca_attributes.' . $format);
  }



  //----------------------------------------------------------------------------------------
  // Read File & Get Header
  $file = fopen($dir . '/pca_attributes.' . $format, "r") or die('No file.');
  $file_data = array();
  $delimiter = ($format == 'txt') ? "\t" : ",";
  while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
    $file_data[] = $row;
  }
  fclose($file);



  //----------------------------------------------------------------------------------------
  // Output Info
  $OUTPUT['name']                = $name;
  $OUTPUT['header']              = array_slice($file_data[0], 1);
  echo json_encode($OUTPUT);

  exit();
}





//********************************************************************************************
// Upload File Step 3
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'upload_file_step_3') {
  // echo '<pre>'; print_r($_FILES); echo '</pre>';

  $format = ($_POST['format_step_3'] == 'txt') ? 'txt' : 'csv';
  $OUTPUT = array('type' => 'Success');
  header('Content-Type: application/json');

  //----------------------------------------------------------------------------------------
  // Upload File
  if ($_FILES["file"]["error"] === 0) {
    $tmp_name       = $_FILES["file"]["tmp_name"];
    $name           = $_FILES["file"]["name"];
    $size           = $_FILES["file"]["size"];
    $type           = $_FILES["file"]["type"];
    $dir            = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    if (!is_dir($dir . '/')) {
      mkdir($dir, 0755, true);
      chmod($dir, 0755);
    }
    copy($tmp_name, $dir . '/pca_var.' . $format);
  }



  //----------------------------------------------------------------------------------------
  // Read File & Get Header
  $file = fopen($dir . '/pca_var.' . $format, "r") or die('No file.');
  $file_data = array();
  $delimiter = ($format == 'txt') ? "\t" : ",";
  $_SESSION['PCA_DIMENSION_VAR'] = array();
  $index = 0;
  while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
    // header
    if ($index == 0) {
      $headers = $row;
    }
    if (trim($row[0]) != '') {
      $file_data[] = $row;
      $_SESSION['PCA_DIMENSION_VAR'][$row[0]] = $row[1];
    }
    $index++;
  }
  fclose($file);

  // Check colname
  $exist = false;
  foreach ($headers as $header) {
    if (trim($header) == 'percentage of variance') {
      $exist = true;
    }
  }
  if (!$exist) {
    $OUTPUT['type'] = 'Error';
    $OUTPUT['detail'] = 'Colname not matched';
    echo json_encode($OUTPUT);
    exit();
  }



  //----------------------------------------------------------------------------------------
  // Output Info
  $OUTPUT['name']                = $name;
  $OUTPUT['data']                = $file_data;
  echo json_encode($OUTPUT);

  exit();
}






//********************************************************************************************
// Generate Scatter Plot
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'generate_scatter_plot') {
  include('exe_generate_scatter_plot.php');

  exit();
}





//********************************************************************************************
// Save PCA Result
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'save_result') {
  // print_r($_POST);
  $TITLE       = trim($_POST['title']);
  $DESCRIPTION = trim($_POST['description']);
  $TYPE        = (isset($_POST['type']) && $_POST['type'] == 'R') ? 'R' : '';
  $TIME_STAMP  = intval($_POST['time_stamp']);
  /*
  if ($TIME_STAMP == 0) {
    $dir_from = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  } else {
    $dir_from = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $TIME_STAMP;
  }
  */
  $dir = get_PCA_dir($TIME_STAMP);

  $info = array(
    'Owner_ID'    => $BXAF_CONFIG['BXAF_USER_CONTACT_ID'],
    'Title'       => $TITLE,
    'Type'        => $TYPE,
    'Description' => $DESCRIPTION,
  );
  $rowid = $DB -> insert($BXAF_CONFIG['TBL_PCA_RESULT'], $info);


  $dir = $BXAF_CONFIG['SAVED_PCA'] . '/' . bxaf_encrypt($rowid, $BXAF_CONFIG['BXAF_KEY']);
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
  }

  //-----------------------------------------------------------------------------
  // For First Tool
  if ($TYPE == '') {
    $file_list = array(
      'pca.txt',
      'pca.csv',
      'pca_attributes.txt',
      'pca_attributes.csv',
      'pca_var.txt',
      'pca_var.csv',
    );
  }
  // For R
  else {
    $file_list = $BXAF_CONFIG['PCA_R_FILE_LIST'];
  }



  foreach ($file_list as $file) {
    copy(
      $dir_from . '/' . $file,
      $dir . '/' . $file
    );
  }

  echo bxaf_encrypt($rowid, $BXAF_CONFIG['BXAF_KEY']);
  // $BXAF_CONFIG['TBL_PCA_RESULT'];

  exit();
}




//********************************************************************************************
// Delete PCA Result
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'delete_result') {
  $ROWID = bxaf_decrypt($_POST['rowid'], $BXAF_CONFIG['BXAF_KEY']);
  $info  = array('bxafStatus' => 9);
  $DB -> update($BXAF_CONFIG['TBL_PCA_RESULT'], $info, "`ID`=" . $ROWID);
  exit();
}
