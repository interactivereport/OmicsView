<?php

include_once('../assets/config.php');

$BXAF_CONFIG['PCA_R_FILE_LIST'] = array(
  'PCA_barchart.csv',
  'PCA_attributes.csv',
  'PCA_ind.contrib.csv',
  'PCA_ind.coord.csv',
  'PCA_ind.cos2.csv',
  'PCA_quali.sup.coord.csv',
  'PCA_quali.sup.cos2.csv',
  'PCA_quanti.sup.coord.csv',
  'PCA_quanti.sup.cor.csv',
  'PCA_quanti.sup.cos2.csv',
  'PCA_var.contrib.csv',
  'PCA_var.coord.csv',
  'PCA_var.cor.csv',
  'PCA_var.cos2.csv',
);

$BXAF_CONFIG['PCA_R_FILE_LIST_REQUIRED'] = array(
  'PCA_barchart.csv',
  'PCA_ind.contrib.csv',
  'PCA_ind.coord.csv',
  'PCA_ind.cos2.csv',
  'PCA_var.contrib.csv',
  'PCA_var.coord.csv',
  'PCA_var.cor.csv',
  'PCA_var.cos2.csv',
);

$BXAF_CONFIG['PCA_R_FILE_LIST_OPTIONAL'] = array(
  'PCA_attributes.csv',
  'PCA_quali.sup.coord.csv',
  'PCA_quali.sup.cos2.csv',
  'PCA_quanti.sup.coord.csv',
  'PCA_quanti.sup.cor.csv',
  'PCA_quanti.sup.cos2.csv',
);

if (!is_dir($BXAF_CONFIG['USER_FILES_PCA'])) {
  mkdir($BXAF_CONFIG['USER_FILES_PCA'], 0755, true);
}

if (!is_dir($BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'])) {
  mkdir($BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'], 0755, true);
}


// Get var if file exists

$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
if (file_exists($dir . '/PCA_barchart.csv')) {
  $_SESSION['PCA_R_VAR'] = array();
  $index = 0;
  $file = fopen($dir . '/PCA_barchart.csv', "r") or die('No file.');
  while(($row    = fgetcsv($file)) !== false){
    if ($index == 0) {
      foreach ($row as $k => $colname) {
        if ($colname == 'percentage of variance') $var_col_index = $k;
      }
    }
    if ($index > 0) {
      $_SESSION['PCA_R_VAR'][] = number_format($row[$var_col_index], 2) . '%';
    }
    $index++;
  }
  fclose($file);
} else {
  unset($_SESSION['PCA_R_VAR']);
}


//----------------------------------------------------------------------------------------------------------------
// Get Displayed Data Indexes for Arrow Chart
//----------------------------------------------------------------------------------------------------------------
if (!function_exists('arrow_chart_get_data_indexes')) {
    function arrow_chart_get_data_indexes($x, $y, $display_option, $time_stamp=0) {

      global $DB, $BXAF_CONFIG;
	  $dir = get_PCA_dir($time_stamp);

      if (!file_exists($dir . '/PCA_var.contrib.csv')) return false;

      $file = fopen($dir . '/PCA_var.contrib.csv', "r") or die('No pca file.');
      $index = 0;
      $file_data = array();
      while(($row = fgetcsv($file)) !== false){
        if ($index == 0) {
          $file_header = $row;
        } else {
          $file_data[] = $row;
        }
        $index++;
      }
      fclose($file);

      $index_x = false;
      $index_y = false;
      foreach ($file_header as $key => $colname) {
        if ($colname == $x) $index_x = $key;
        if ($colname == $y) $index_y = $key;
      }
      if (!$index_x || !$index_y) return false;

      $data_x = array();
      $data_y = array();
      foreach ($file_data as $key => $value) {
        $data_x[] = $value[$index_x];
        $data_y[] = $value[$index_y];
      }

      // Select Top 10 for "$data_x" (Contribution)
      arsort($data_x);
      $index = 0;
      foreach ($data_x as $key => $value) {
        if ($index < $display_option) {
          $result[] = $key;
        }
        $index++;
      }
      arsort($data_y);
      $index = 0;
      foreach ($data_y as $key => $value) {
        if ($index < $display_option && !in_array($key, $result)) {
          $result[] = $key;
        }
        $index++;
      }
      return $result;


      //
      // $result = array();
      //
      // // Select Top 10 for "$data_x" (Contribution)
      // asort($data_x);
      // $index = 0;
      // foreach ($data_x as $key => $value) {
      //   if ($index < 5 || $index > count($data_x) - 6) {
      //     $result[] = $key;
      //   }
      //   $index++;
      // }
      //
      // // Select Top 5 and Bottom 5 for "$data_y"
      // asort($data_y);
      // $index = 0;
      // foreach ($data_y as $key => $value) {
      //   if ($index < 5 || $index > count($data_y) - 6) {
      //     if (!in_array($key, $result)) {
      //       $result[] = $key;
      //     }
      //   }
      //   $index++;
      // }
      //
      // asort($result);
      //
      // $output = array();
      // foreach ($result as $value) {
      //   $output[] = $value;
      // }
      //
      // return $output;
    }
}



//----------------------------------------------------------------------------------------------------------------
// Filter Data Displayed
//----------------------------------------------------------------------------------------------------------------
if (!function_exists('arrow_chart_filter_data')) {
    function arrow_chart_filter_data($data, $indexes) {
      $result = array();

      foreach ($data as $key => $value) {
        if (in_array($key, $indexes)) {
          $result[] = $value;
        }
      }

      return $result;
    }
}







//----------------------------------------------------------------------------------------------------------------
// Shell Functions
//----------------------------------------------------------------------------------------------------------------
if (!function_exists('bxaf_execute_in_background')) {
    function bxaf_execute_in_background($Command, $outputfile = '', $logfile = '', $Priority = 19)
    {

        $Priority = intval($Priority);
        if ($Priority <= 0 || $Priority > 19)
            $Priority = 19;
        if ($outputfile == '')
            $outputfile = '/dev/null';
        if ($logfile == '')
            $logfile = '/dev/null';
        return shell_exec("nohup nice -n $Priority $Command 1> $outputfile 2> $logfile & echo $!");
    }
}


function get_PCA_dir($TIME_STAMP){
	
	global $BXAF_CONFIG;
	
	$find = $BXAF_CONFIG['FIND_BIN'];
	
	if ($find == ''){
		$find = '/bin/find';	
	}
	
	if ($TIME_STAMP == '') {
		$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
	} else {
		$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $TIME_STAMP;
	}
	
	if (!file_exists($dir)){
		if ($TIME_STAMP != ''){
			$cmd = "{$find} {$BXAF_CONFIG['USER_FILES_PCA']} -name '{$TIME_STAMP}'";
			$dir = shell_exec($cmd);
		}
	}
	
	$dir = trim($dir);
	
	if ($dir == ''){
		if ($TIME_STAMP == '') {
			$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
		} else {
			$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $TIME_STAMP;
		}
	}
	
	return $dir;
	
}

?>