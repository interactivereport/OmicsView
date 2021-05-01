<?php
include_once('config.php');
// print_r($_POST);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<?php
// if ($BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['bootstrap']){
// 	echo "<link   href='/{$BXAF_SYSTEM_SUBDIR}library/bootstrap/css/bootstrap.min.css.php' rel='stylesheet' type='text/css'>\n";
// 	echo "<script src='/{$BXAF_SYSTEM_SUBDIR}library/bootstrap/js/bootstrap.min.js.php'></script>\n";
// }
?>


<!-- <link href="../library/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"> -->
<!-- <link href="../library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../library/TableFilter/dist/tablefilter/style/tablefilter.css" rel="stylesheet">
<link href="../library/animate.css.php" rel="stylesheet">
<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../library/tether/dist/css/tether.min.css" rel="stylesheet"> -->
<link href="../css/main.css" rel="stylesheet">


<!-- <script type="text/javascript" src="../library/jquery.min.3.1.0.js"></script>
<script type="text/javascript" src="../library/tether/dist/js/tether.min.js"></script> -->
<!-- <script type="text/javascript" src="../library/bootstrap/dist/js/bootstrap.min.js.php"></script> -->
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/bootbox.min.js.php"></script>
<script type="text/javascript" src="../library/config.js"></script>


<script src="../library/plotly.min.js"></script>

</head>
<body>


<?php
$GENE_NAME = trim($_POST['gene_name']);
$Y_FIELD = $_POST['select_y_field'];
$COLORING_FIELD = $_POST['select_coloring_field'];
$AREA_FIELD = $_POST['area_setting'];
$AREA_FIELD_MODIFIED = ($AREA_FIELD == 'PValue') ? 'PVALUE' : 'ADJPVALUE';

// Get GeneIndex
$GENE_INDEX = search_gene_index($GENE_NAME);

if (!isset($GENE_INDEX) || trim($GENE_INDEX) == '' || intval($GENE_INDEX) < 0) {
  echo 'Error: No gene found.';
  exit();
}

$sql = "SELECT `ComparisonIndex`, `Log2FoldChange`, `PValue`, `AdjustedPValue`
    FROM `{$BXAF_CONFIG['TBL_COMPARISONDATA']}`
    WHERE `GeneIndex`=" . $GENE_INDEX;
$data_comparison = $DB -> get_all($sql);



$Y_FIELD_LIST = array();
$COLORING_FIELD_LIST = array();
$Y_FIELD_NUMBER = array(); // Appear times
$COLORING_FIELD_NUMBER = array();

foreach ($data_comparison as $comparison) {
  $sql = "SELECT `{$Y_FIELD}`, `{$COLORING_FIELD}`, `ComparisonID` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $comparison['ComparisonIndex'];
  $comparison_row = $DB -> get_row($sql);

  //echo '<pre>'; print_r($comparison_row); echo '</pre>';

  if (trim($comparison['Log2FoldChange']) == ''
    || trim($comparison['Log2FoldChange']) == '.'
    || trim($comparison['Log2FoldChange']) == 'NA'
    || trim($comparison['PValue']) == ''
    || trim($comparison['PValue']) == '.'
    || trim($comparison['PValue']) == 'NA'
    || trim($comparison_row[$Y_FIELD]) == ''
    || trim($comparison_row[$Y_FIELD]) == 'NA'
    || trim($comparison_row[$COLORING_FIELD]) == ''
    || trim($comparison_row[$COLORING_FIELD]) == 'NA') {
    continue;
  }

  // 统计出现次数
  if (!in_array($comparison_row[$Y_FIELD], array_keys($Y_FIELD_NUMBER))) {
    $Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] = 1;
  } else {
    $Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] += 1;
  }
  if (!in_array($comparison_row[$COLORING_FIELD], array_keys($COLORING_FIELD_NUMBER))) {
    $COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] = 1;
  } else {
    $COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] += 1;
  }

}

arsort($Y_FIELD_NUMBER);
arsort($COLORING_FIELD_NUMBER);


// Filter y field and coloring field
if ($_POST['y_setting'] == 'top_10') {
  $index = 0;
  foreach($Y_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control' || $index >= 10) {
      unset($Y_FIELD_NUMBER[$key]);
    }
    if (trim($key) != 'normal control') $index++;
  }
} else if ($_POST['y_setting'] == 'top_20') {
  $index = 0;
  foreach($Y_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control' || $index >= 20) {
      unset($Y_FIELD_NUMBER[$key]);
    }
    if (trim($key) != 'normal control') $index++;
  }
} else if ($_POST['y_setting'] == 'all') {
  foreach($Y_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control') {
      unset($Y_FIELD_NUMBER[$key]);
    }
  }
} else if ($_POST['y_setting'] == 'customize') {
  foreach($Y_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control' || !isset($_POST['y_' . str_replace(' ', '_', $key)])) {
      unset($Y_FIELD_NUMBER[$key]);
    }
  }
}

if ($_POST['coloring_setting'] == 'top_10') {
  $index = 0;
  foreach($COLORING_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control' || $index >= 10) {
      unset($COLORING_FIELD_NUMBER[$key]);
    }
    if (trim($key) != 'normal control') $index++;
  }
} else if ($_POST['coloring_setting'] == 'top_20') {
  $index = 0;
  foreach($COLORING_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control' || $index >= 20) {
      unset($COLORING_FIELD_NUMBER[$key]);
    }
    if (trim($key) != 'normal control') $index++;
  }
} else if ($_POST['coloring_setting'] == 'all') {
  foreach($COLORING_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control') {
      unset($COLORING_FIELD_NUMBER[$key]);
    }
  }
} else if ($_POST['coloring_setting'] == 'customize') {
  foreach($COLORING_FIELD_NUMBER as $key => $value) {
    if (trim($key) == 'normal control' || !isset($_POST['color_' . str_replace(' ', '_', $key)])) {
      unset($COLORING_FIELD_NUMBER[$key]);
    }
  }
}

// Get All Values
// Grouped by coloring settings
$ALL_MARKER = array();
$ALL_GENES = array();
$ALL_APPEARED_Y = array();

foreach ($data_comparison as $comparison) {
  $sql = "SELECT `{$Y_FIELD}`, `{$COLORING_FIELD}`, `ComparisonID`, `ComparisonIndex` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $comparison['ComparisonIndex'];
  $comparison_row = $DB -> get_row($sql);

  if (trim($comparison['Log2FoldChange']) == ''
    || trim($comparison['Log2FoldChange']) == '.'
    || trim($comparison['Log2FoldChange']) == 'NA'
    || trim($comparison['PValue']) == ''
    || trim($comparison['PValue']) == '.'
    || trim($comparison['PValue']) == 'NA'
    || trim($comparison_row[$Y_FIELD]) == ''
    || trim($comparison_row[$Y_FIELD]) == 'NA'
    || trim($comparison_row[$COLORING_FIELD]) == ''
    || trim($comparison_row[$COLORING_FIELD]) == 'NA') {
    continue;
  }

  // Skip unselected y&coloring option
  $y_temp = $comparison_row[$Y_FIELD];
  $color_temp = $comparison_row[$COLORING_FIELD];

  if (!in_array($y_temp, array_keys($Y_FIELD_NUMBER))
    || !in_array($color_temp, array_keys($COLORING_FIELD_NUMBER))) {
    continue;
  }



  // Save appeared y option and point info
  if (!in_array($y_temp, $ALL_APPEARED_Y)) {
    $ALL_APPEARED_Y[] = $y_temp;
  }

  if (!in_array($color_temp, array_keys($ALL_MARKER))) {
    $ALL_MARKER[$color_temp] = array(
      array(
        'Y_FIELD' =>$y_temp,
        'COLORING_FIELD' => $color_temp,
        //'LOGFC' => number_format(rtrim(rtrim(sprintf('%.8F', $comparison['Log2FoldChange']), '0'), "."), 8),
        'LOGFC' => $comparison['Log2FoldChange'],
        'PVALUE' => $comparison['PValue'],
        'ADJPVALUE' => $comparison['AdjustedPValue'],
        'COMPARISON_ID' => $comparison_row['ComparisonID'],
        'COMPARISON_INDEX' => $comparison_row['ComparisonIndex']
      )
    );
  } else {
    $ALL_MARKER[$color_temp][] = array(
      'Y_FIELD' =>$y_temp,
      'COLORING_FIELD' => $color_temp,
      //'LOGFC' => number_format(rtrim(rtrim(sprintf('%.8F', $comparison['Log2FoldChange']), '0'), "."), 8),
      'LOGFC' => $comparison['Log2FoldChange'],
      'PVALUE' => $comparison['PValue'],
      'ADJPVALUE' => $comparison['AdjustedPValue'],
      'COMPARISON_ID' => $comparison_row['ComparisonID'],
      'COMPARISON_INDEX' => $comparison_row['ComparisonIndex']
    );
  }


  // Save all genes to search
  $ALL_GENES[] = array(
    'x' => $comparison['Log2FoldChange'],
    'y' => $y_temp,
    'comparison_index' => $comparison['ComparisonIndex'],
    'gene_index' => $GENE_INDEX,
  );
}


// echo '<pre>'; print_r($ALL_MARKER); echo '</pre>'; exit();




asort($ALL_APPEARED_Y);
$HEIGHT = max(700, count($ALL_APPEARED_Y) * 30);
$ALL_APPEARED_Y_ORDERED = array();
foreach ($ALL_APPEARED_Y as $option) {
  $ALL_APPEARED_Y_ORDERED[] = $option;
}
$dir = $BXAF_CONFIG['USER_FILES_BUBBLE'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
if (!is_dir($dir)) {
  mkdir($dir, 0755, true);
}
file_put_contents($dir . '/y_field_options.txt', serialize($ALL_APPEARED_Y_ORDERED));
file_put_contents($dir . '/all_genes.txt', serialize($ALL_GENES));

//-----------------------------------------------------------------------------
// Save CSV File for Users to Download
$csv_info = array();
foreach ($ALL_MARKER as $markers) {
  foreach ($markers as $marker) {
    $csv_info[] = array(
      $GENE_NAME,
      $marker['COMPARISON_ID'],
      $marker['LOGFC'],
      $marker['PVALUE'],
      $marker['ADJPVALUE']
    );
  }
}
$file = fopen($dir . '/download.csv',"w");
fputcsv($file, array('GeneName', 'ComparisonName', 'LogFC', 'PValue', 'FDR'));
foreach ($csv_info as $line){
  fputcsv($file, $line);
}
fclose($file);
chmod($dir . '/download.csv', 0755);




// Output
echo '
<div class="row mt-1">
  <div class="col-md-2">
    <button class="btn btn-sm btn-primary hidden" id="btn_modify_settings"
      onclick="$(\'#first_form_div, #second_form_div\').slideToggle(300);">
      <i class="fa fa-cog"></i> Modify Settings
    </button>
    <a class="mt-1 btn btn-sm btn-info" href="files/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/download.csv" download>
      <i class="fa fa-download"></i> Download Data
    </a>
  </div>
  <div class="col-md-10">
    <div class="alert alert-success" style="width:35em">
    <p>The plot contains <strong>' . count($ALL_GENES) . '</strong> out of <strong>' . count($data_comparison) . '</strong> data points.</p>
    <p>You can go back to modify settings</p>
    </div>
  </div>
</div>
<div id="plot_div"></div>';
echo "<script>";
$index = 0;
foreach ($ALL_MARKER as $key => $value) {
  $temp_logfc = array();
  $temp_y = array();
  $temp_area = array();
  $temp_text = array();
  $temp_comparison_index = array();
  $temp_comparison_id = array();
  foreach ($value as $k => $v) {
    $temp_logfc[] = $v['LOGFC'];

    // Set Y Axis Label
    $temp_y[] = "'" . addslashes($v['Y_FIELD']) . "'";
    //$temp_y[] = "'" . addslashes(substr($v['Y_FIELD'], 0, 20)) . '-<br>' . addslashes(substr($v['Y_FIELD'], 20)) . "'";


    $temp_text[] = "'Comparison ID: " . $v['COMPARISON_ID']  . "<br />" . substr($Y_FIELD, strpos($Y_FIELD, '_')+1) . ": " .  addslashes($v['Y_FIELD']) . "<br />"  . substr($COLORING_FIELD, strpos($COLORING_FIELD, '_')+1) . ": " . addslashes($v['COLORING_FIELD']) . "<br />P-value: " . $v['PVALUE'] . "<br />Adj P-value: " . $v['ADJPVALUE'] . "<br />logFC: " . $v['LOGFC'] . "<br />'";

    if ((-1000) * log10($v[$AREA_FIELD_MODIFIED]) < 5000 && (-1000) * log10($v[$AREA_FIELD_MODIFIED]) > 100) {
      $temp_area[] = (-1000) * log10($v[$AREA_FIELD_MODIFIED]);
    } else if ((-1000) * log10($v[$AREA_FIELD_MODIFIED]) > 5000) {
      $temp_area[] = 5000;
    } else {
      $temp_area[] = 100;
    }

    $temp_comparison_index[] = $v['COMPARISON_INDEX'];
    $temp_comparison_id[] = $v['COMPARISON_ID'];
  }



  echo "
  var trace" . $index . " = {
    x: [" . implode(', ', $temp_logfc) . "],
    y: [" . implode(', ', $temp_y) . "],
    name: '";
    $key_modified = str_replace(';', '<br>', $key);
    echo addslashes($key_modified);


  echo "',
    hoverinfo: 'text',
    text: [" . implode(', ', $temp_text) . "],
    mode: 'markers',
    marker: {
      size: [" . implode(', ', $temp_area) . "],
      sizeref: 7,
      sizemode: 'area',
      comparison_index: ['" . implode("', '", $temp_comparison_index) . "'],
      comparison_id: ['" . implode("', '", $temp_comparison_id) . "'],
    }
  };";
  $index++;
}


echo "
var data = [trace0";

for ($i = 1; $i < $index; $i++) {
  echo ", trace".$i;
}

echo "];

var layout = {
  margin: {
    l: 250
  },

  title: 'Bubble Chart for " . addslashes($GENE_NAME) . "<br>Colored by " . $COLORING_FIELD . "',
  showlegend: true,
  height: " . $HEIGHT . ",
  //width: 1200,
  xaxis: {
    title: 'Log 2 Fold Change',
  },
  yaxis: {
    // title: '" . addslashes($Y_FIELD) . "',
    categoryorder: 'category ascending'
  },
  hovermode: 'closest',
};

Plotly.newPlot('plot_div', data, layout, {displaylogo:false, modeBarButtonsToRemove:['sendDataToCloud'], scrollZoom:true, displayModeBar: true})
  .then(function() {
    window.requestAnimationFrame(function() {
      $('.loader').remove();
    });
  });





$(document).ready(function() {
  var graphDiv = document.getElementById('plot_div');

  graphDiv.on('plotly_click', function(data){
    var comparison = data.points[0].data.marker.comparison_id[data.points[0].pointNumber];
    var comparison_index = data.points[0].data.marker.comparison_index[data.points[0].pointNumber];
    console.log(data.points[0]);
    bootbox.alert('<h4>Comparison ' + comparison + '</h4><br /><a href=\"../search_comparison/single_comparison.php?type=comparison&id='+comparison_index+'\">Comparison Detail</a><br /><a href=\"../volcano/index.php?id='+comparison_index+'\">Comparison Volcano Chart</a><br /><a href=\"../pvjs/index.php?id='+comparison_index+'\">Pathway View</a><br /><a href=\"../search_comparison/index.php?type=sample&comparison_id='+comparison_index+'\">Related Samples</a> &nbsp;');
  });


  graphDiv.on('plotly_selected', function(eventData) {
    var x = [];
    var y = [];
    eventData.points.forEach(function(pt) {
      x.push(pt.x);
      y.push(pt.y);
    });
    $.ajax({
      type: 'POST',
      url: 'exe.php?action=show_table&type=lasso_select',
      data: {x:x, y:y},
      success: function(responseText) {
        $('#table_div').html(responseText);
      }
    });
  });
});

</script>";

?>

</body>
</html>
