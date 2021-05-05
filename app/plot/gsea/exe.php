<?php

include_once('config.php');

if (isset($_GET['action']) && $_GET['action'] == 'show_chart') {

  // print_r($_POST);
  $COMPARISON_INDEX = $_POST['comparison_index'];

  // If it's in-house comparison
  if (isset($_POST['inhouse']) && $_POST['inhouse']) {
    $csv_dir = $BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/inhouse_comp_' . $COMPARISON_INDEX . '/PAGE_cleaned_data.csv.csv';
  } else {
    $csv_dir = $BXAF_CONFIG['PAGE_OUTPUT_HUMAN'] . '/comparison_' . $COMPARISON_INDEX . '_GSEA.PAGE.csv';
  }
  
  
  if (!internal_data_is_public($COMPARISON_INDEX)){
	$csv_dir = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$COMPARISON_INDEX}/PAGE_comp_{$COMPARISON_INDEX}.csv";
  }
  
  
  $CONTENT = array();

  if (file_exists($csv_dir)) {
    $file = fopen($csv_dir, "r");
    while (! feof($file)) {
      $CONTENT[] = fgetcsv($file);
    }
    fclose($file);
  }

  function sort_by_large($a, $b) {
    return ($a['2'] - $b['2'] > 0) ? -1 : 1;
  }
  function sort_by_small($a, $b) {
    return ($a['2'] - $b['2'] < 0) ? -1 : 1;
  }

  // Get Top 10 Z-Score Records
  usort($CONTENT, 'sort_by_large');
  $RESULT_LARGE = array();
  for ($i = 0; $i < 10; $i++) {
    $RESULT_LARGE[] = $CONTENT[$i];
  }

  // Get Bottom 10 Z-Score Records
  usort($CONTENT, 'sort_by_small');
  $RESULT_SMALL = array();
  for ($i = 0; $i < 10; $i++) {
    $RESULT_SMALL[] = $CONTENT[$i];
  }


  // echo '<pre>'; print_r($RESULT_LARGE); echo '</pre>';
  // echo '<pre>'; print_r($RESULT_SMALL); echo '</pre>';


  $x_up = array();
  $y_up = array();
  $marker_size_up = array();
  $marker_color_up = array();
  $text_up = array();
  foreach ($RESULT_LARGE as $key => $value) {
    $x_up[] = $value['2'];
    $y_up[] = $value['0'];

    $marker = log10($value['1']) * 10;
    // if (floatval($value['1']/5) > 70) {
    //   $marker = 70;
    // } else if (floatval($value['1']/5) < 5) {
    //   $marker = 5;
    // } else {
    //   $marker = floatval($value['1']/5);
    // }
    $marker_size_up[] = $marker;

    if (floatval($value[4]) < 0.2) {
      $marker_color_up[] = '#FF0000';
    } else if (floatval($value[4]) < 0.5) {
      $marker_color_up[] = '#FF8989';
    } else if (floatval($value[4]) < 0.8) {
      $marker_color_up[] = '#8E8EFF';
    } else {
      $marker_color_up[] = '#0000FF';
    }

    $text =  'Name: ' . $value[0] . '<br />';
    $text .= 'Total Genes: ' . $value[1] . '<br />';
    $text .= 'Z Score: ' . $value[2] . '<br />';
    $text .= 'P-Value: ' . $value[3] . '<br />';
    $text .= 'FDR: ' . $value[4];
    $text_up[] = $text;
  }

  $x_down = array();
  $y_down = array();
  $marker_size_down = array();
  $marker_color_down = array();
  $text_down = array();
  foreach ($RESULT_SMALL as $key => $value) {
    $x_down[] = $value['2'];
    $y_down[] = $value['0'];


    $marker = log10($value['1']) * 10;
    // if (floatval($value['1']/5) > 70) {
    //   $marker = log10($value['1']); //70;
    // } else if (floatval($value['1']/5) < 5) {
    //   $marker = log10($value['1']);//5;
    // } else {
    //   $marker = log10($value['1']);//floatval($value['1']/5);
    // }
    $marker_size_down[] = $marker;

    if (floatval($value[4]) < 0.2) {
      $marker_color_down[] = '#FF0000';
    } else if (floatval($value[4]) < 0.5) {
      $marker_color_down[] = '#FF8989';
    } else if (floatval($value[4]) < 0.8) {
      $marker_color_down[] = '#8E8EFF';
    } else {
      $marker_color_down[] = '#0000FF';
    }

    $text =  'Name: ' . $value[0] . '<br />';
    $text .= 'Total Genes: ' . $value[1] . '<br />';
    $text .= 'Z Score: ' . $value[2] . '<br />';
    $text .= 'P-Value: ' . $value[3] . '<br />';
    $text .= 'FDR: ' . $value[4];
    $text_down[] = $text;
  }

  // Output
  $JSON_ARRAY = array(
    'up' => array(
      'data' => array(),
      'layout' => array(),
      'setting' => array(),
    ),
    'down' => array(
      'data' => array(),
      'layout' => array(),
      'setting' => array(),
    ),
  );

  $JSON_ARRAY['up']['data'][] = array(
    'x' => $x_up,
    'y' => $y_up,
    'mode' => 'markers',
    'marker' => array(
      'size' => $marker_size_up,
      'color' => $marker_color_up,
    ),
    'hoverinfo' => 'text',
    'text' => $text_up,
  );

  $JSON_ARRAY['down']['data'][] = array(
    'x' => $x_down,
    'y' => $y_down,
    'mode' => 'markers',
    'marker' => array(
      'size' => $marker_size_down,
      'color' => $marker_color_down,
    ),
    'hoverinfo' => 'text',
    'text' => $text_down,
  );


  $layout = array(
    'title' => 'GSEA Plot',
    'showlegend' => false,
    // 'height' => 600,
    // 'width' => 600,
    'margin' => array('l' => 500),
    'xaxis' => array('title' => 'Z Score', 'tickfont' => array('size' => 11),),
	'yaxis' => array('tickfont' => array('size' => 9),),
    // 'yaxis' => array('title' => 'Geneset Name'),
    'hovermode' => 'cloest',
  );
  $setting = array(
    'displaylogo' => false,
    'modeBarButtonsToRemove' => array('sendDataToCloud'),
    'scrollZoom' => true,
    'displayModeBar' => false,
  );

  $JSON_ARRAY['up']['layout'] = $layout;
  $JSON_ARRAY['up']['setting'] = $setting;
  $JSON_ARRAY['down']['layout'] = $layout;
  $JSON_ARRAY['down']['setting'] = $setting;

  $JSON_ARRAY['up']['layout']['title'] 		= "PAGE Plot - Up-regulated {$APP_MESSAGE['Genes']}";
  $JSON_ARRAY['down']['layout']['title'] 	= "PAGE Plot - Down-regulated {$APP_MESSAGE['Genes']}";

  header('Content-Type: application/json');
  echo json_encode($JSON_ARRAY);

  exit();
}




if (isset($_GET['action']) && $_GET['action'] == 'go_to_volcano') {
  //print_r($_POST);
  $GENESET = trim($_POST['geneset_name']);
  $sql = "SELECT * FROM `GeneSet` WHERE `StandardName`='{$GENESET}'";
  $data = $DB -> get_row($sql);
  $MEMBERS = explode(",", $data['Members']);

  $genes_saved = implode("|", $MEMBERS);


  $dir = $BXAF_CONFIG['USER_FILES_PAGE'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
  }

  file_put_contents($dir . '/selected_genes.txt', $genes_saved);

  exit();
}


?>>
