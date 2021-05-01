<?php
include_once('config.php');

if (true){
	//Copy from index_r_barchart.php
	$SAVED_RESULT = false;
	if (isset($_GET['id']) && trim($_GET['id']) != '') {
		
	  $ROWID = bxaf_decrypt($_GET['id'], $BXAF_CONFIG['BXAF_KEY']);
	  if (intval($ROWID) != 0) {
		$SAVED_RESULT = true;
		$file_list = $BXAF_CONFIG['PCA_R_FILE_LIST'];
		$dir = $BXAF_CONFIG['SAVED_PCA'] . '/' . $_GET['id'];
		foreach ($file_list as $file) {
		  copy(
			$dir . '/' . $file,
			$BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $file
		  );
		}
	  }
	}
}


$PAGE['Category'] = 'List';
$PAGE['Barcode']  = 'Visualize PCA Results';

$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];

// Specify Folder for Genes & Samples
$TIME_STAMP = 0;
if (isset($_GET['tid']) && intval($_GET['tid']) != 0) {
  $TIME_STAMP = intval($_GET['tid']);
  //$dir = "{$BXAF_CONFIG['USER_FILES_PCA']}/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/{$_GET['tid']}";
  $dir = get_PCA_dir($TIME_STAMP);
  if (!is_dir($dir)) {
    echo 'No result exists.';
    exit();
  }

  // Reload Variances
//  $dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $TIME_STAMP;
  $dir = get_PCA_dir($TIME_STAMP);
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
}


// Delete all chart files.
/*
foreach (new DirectoryIterator(dirname(__FILE__) . '/files/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID']) as $fileInfo) {
  if(!$fileInfo->isDot()) {
    unlink($fileInfo->getPathname());
  }
}
*/

if (!file_exists($dir . '/PCA_var.coord.csv')) {
  echo 'No file exists.';
  exit();
}


$file = fopen($dir . '/PCA_var.coord.csv', "r") or die('No file.');
$file_data = array();
$delimiter = ",";

//------------------------------------------------------------
// Read File
$index = 0;
while(($row = fgetcsv($file, 1000, $delimiter)) !== false){
  // header
  if ($index == 0) {
    $headers = $row;
  }
  if (trim($row[0]) != '' && $index > 0) {
    $file_data[] = $row;
  }
  $index++;
}
fclose($file);



//------------------------------------------------------------
// Get Attributes
$attributes_file_exists = true;
if (file_exists($dir . '/PCA_attributes.csv')) {
  $file_attributes_dir = $dir . '/PCA_attributes.csv';
  $format = 'csv';
} else if (file_exists($dir . '/PCA_attributes.txt')) {
  $file_attributes_dir = $dir . '/PCA_attributes.txt';
  $format = 'txt';
} else {
  $attributes_file_exists = false;
}

if ($attributes_file_exists) {
  $index = 0;
  $delimiter = ($format == 'txt') ? "\t" : ",";
  $file_attributes = fopen($file_attributes_dir, "r") or die('No attributes file.');
  while(($row = fgetcsv($file_attributes, 1000, $delimiter)) !== false){
    // header
    if ($index == 0) {
      $attributes_headers = array_slice($row, 1);
    }
    if ($index == 1) {
      $attributes_data_example = array_slice($row, 1);
    }
    // if (trim($row[0]) != '' && $index > 0) {
    //   $file_data[] = $row;
    // }
    $index++;
  }
  fclose($file);

  $attributes_numeric_headers = $attributes_headers;
  if (is_array($attributes_data_example) && count($attributes_data_example) > 0) {
    foreach ($attributes_data_example as $key => $value) {
      if (floatval($value) == 0) {
        unset($attributes_numeric_headers[$key]);
      }
    }
  }
}

// print_r($attributes_headers);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-Equiv="Cache-Control" Content="no-cache" />
<meta http-Equiv="Pragma" Content="no-cache" />
<meta http-Equiv="Expires" Content="0" />
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<link href="../library/wenk.min.css" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/bootbox.min.js.php"></script>
<script type="text/javascript" src="../library/plotly.min.js"></script>
<script type="text/javascript" src="../library/config.js"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>


<style>
strong {
  color: #666;
}
</style>

</head>
<body>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
  <div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <!-- <div id="bxaf_page_right_container"> -->
      <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

        <div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">


          <div class="container-fluid px-0 pt-3 w-100">

      			<h1 class="">
      				FactoMineR PCA Analysis
      			</h1>
            <hr />
            <?php include_once('component_header.php'); ?>

            <?php include_once('component_r_header_2.php'); ?>



            <div class="row">
              <div class="col-lg-4 col-md-4" style="max-width:300px;">
                <div class="card">
                  <div class="card-header bg-warning">
                    <h4 class="mb-0">Parameters</h4>
                  </div>
                  <div class="card-block">

                    <!------------------------------------------------------------------------------>
                    <!-- X Coordinate -->
                    <p class="mb-0 mt-2"><strong>X Coordinate:</strong></p>
                    <select class="form-control" id="select_x">
                      <?php
                        $index = 0;
                        foreach ($headers as $colname) {
                          if (trim($colname) != '') {
                            echo '<option value="' . $colname . '">';
                            echo 'PC'. intval($index + 1);
                            echo ' (' . $_SESSION['PCA_R_VAR'][$index] . ')';
                            echo '</option>';
                            $index++;
                          }
                        }
                      ?>
                    </select>

                    <!------------------------------------------------------------------------------>
                    <!-- Y Coordinate -->
                    <p class="mb-0 mt-2"><strong>Y Coordinate:</strong></p>
                    <select class="form-control" id="select_y">
                      <?php
                        $index = 0;
                        foreach ($headers as $colname) {
                          if (trim($colname) != '') {
                            echo '<option value="' . $colname . '"';
                            if ($index == 1) echo ' selected';
                            echo '>';
                            echo 'PC'. intval($index + 1);
                            echo ' (' . $_SESSION['PCA_R_VAR'][$index] . ')';
                            echo '</option>';
                            $index++;
                          }
                        }
                      ?>
                    </select>


                    <?php if ($attributes_file_exists) { ?>

                      <!------------------------------------------------------------------------------>
                      <!-- Label Color -->
                      <p class="mb-0 mt-2"><strong>Color By:</strong></p>
                      <select class="form-control" id="select_legend_1">
                        <option value="">(None)</option>
                        <?php
                          foreach ($attributes_headers as $col) {
                            echo '<option value="' . $col . '">' . $col . '</option>';
                          }
                        ?>
                      </select>

                      <!------------------------------------------------------------------------------>
                      <!-- Label Color -->
                      <p class="mb-0 mt-2"><strong>Shape By:</strong></p>
                      <select class="form-control" id="select_legend_2">
                        <option value="">(None)</option>
                        <?php
                          foreach ($attributes_headers as $col) {
                            echo '<option value="' . $col . '">' . $col . '</option>';
                          }
                        ?>
                      </select>

                    <?php } else { ?>

                      <p class="mb-0 mt-2">You can upload attributes file <a href="index_r_individuals_table.php">here</a> and create label.</p>

                    <?php } ?>


                    <!------------------------------------------------------------------------------>
                    <!-- Size By -->
                    <p class="mb-0 mt-2"><strong>Size By:</strong></p>
                    <select class="form-control" id="select_legend_3">
                      <option value="">(None)</option>
                      <?php
                        $index = 1;
                        foreach ($headers as $colname) {
                          if (trim($colname) != '') {
                            echo '<option value="' . $colname . '">PC' . $index . '</option>';
                            $index++;
                          }
                        }
                        foreach ($attributes_numeric_headers as $col) {
                          echo '<option value="' . $col . '">' . $col . '</option>';
                        }
                      ?>
                    </select>



                    <!------------------------------------------------------------------------------>
                    <!-- Label Size -->
                    <p class="mb-0 mt-2">
                      <strong>Label Size:</strong>
                      <span class="range_value">10</span> px &nbsp;
                      <label class="pull-right">
                        <input type="checkbox" id="checkbox_show_labels" checked> <strong>Show Labels</strong>
                      </label>
                    </p>
                    <div class="example__range pt-1">
                      <input type='range' class="input_range mt-2" min="5" max="20" step="1" value="10"
                        id="input_label_size">
                    </div>


                    <!------------------------------------------------------------------------------>
                    <!-- Marker Size -->
                    <p class="mb-0 mt-2"><strong>Marker Size:</strong>
                    <span class="range_value">64</span> px</p>
                    <div class="example__range pt-1">
                      <input type='range' class="input_range mt-2" min="10" max="100" step="1" value="64"
                        id="input_marker_size">
                    </div>



                    <!------------------------------------------------------------------------------>
                    <!-- Graph Width -->
                    <p class="mb-0 mt-2"><strong>Graph Size:</strong>
                    <span id="graph_width">640</span> *
                    <span id="graph_height">480</span>
                    px</p>
                    <div class="example__range pt-1">
                      <input type='range' class="input_range mt-2" min="200" max="1200" step="1" value="640"
                        id="input_graph_size">
                    </div>




                  </div>
                </div>
              </div>

              <div id="div_chart" xclass="col-md-8 col-lg-8">
                <i class="fas fa-spin fa-spinner fa-pulse"></i> Loading the content...
              </div>

            </div>


          </div>


          </div>
        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>











<script>


$(document).ready(function() {

  $('#btn_index_r_individuals_plot').addClass('active');

  refresh_chart();

  $('.example__range').on('mousemove', function() {
    var vm = $(this).find('.input_range');
    vm.parent()
      .prev()
      .find('.range_value')
      .html(vm.val());
  });


  $('#input_graph_size').on('mousemove', function() {
    var width = $(this).val();
    var height = parseInt(parseInt(width) / 1.33333333);
    $('#graph_width').html(width);
    $('#graph_height').html(height);
  });


  $(document).on('change', '#select_x, #select_y, #select_legend_1, #select_legend_2, #select_legend_3, #checkbox_show_labels, #input_label_size, #input_marker_size, #input_graph_size', function() {
    refresh_chart();
  });

});


function refresh_chart() {
  var x            = $('#select_x').val();
  var y            = $('#select_y').val();
  var legend_1     = $('#select_legend_1').val();
  var legend_2     = $('#select_legend_2').val();
  var legend_3     = $('#select_legend_3').val();
  var label_size   = $('#input_label_size').val();
  var marker_size  = $('#input_marker_size').val();
  var graph_width  = $('#input_graph_size').val();
  var graph_height = parseInt(parseInt(graph_width) / 1.33333333);
  var chart_type   = 'individuals_plot';
  var show_labels  = 'false';
  if ($('#checkbox_show_labels').is(':checked')){
	  show_labels = 'true';
  }

  $.ajax({
    type: 'POST',
    url: 'exe.php?action=generate_scatter_plot',
    data: {
      x: x,
      y: y,
      legend_1: legend_1,
      legend_2: legend_2,
      legend_3: legend_3,
      label_size: label_size,
      marker_size: marker_size,
      show_labels: show_labels,
      graph_width: graph_width,
      graph_height: graph_height,
      chart_type: chart_type,
      time_stamp: '<?php echo $TIME_STAMP; ?>'
    },
    success: function(response) {
      if (response.type && response.type == 'Error') {
        bootbox.alert(response.detail);
      } else {
        var time = response.chart_time;
		var content = '';
		content += '<p><a href="files/<?php echo $BXAF_CONFIG['BXAF_USER_CONTACT_ID']; ?>/chart_'+time+'.html" target="_blank">View in Full Screen</a></p>';
		content += '<iframe';
        content += ' src="files/<?php echo $BXAF_CONFIG['BXAF_USER_CONTACT_ID']; ?>/chart_'+time+'.html"';
        //content += ' height="100%"';
        //content += ' width="100%"';
        content += ' height="' + parseInt(parseInt(response.graph_height) * 1.3) + '"';
        content += ' width="' + parseInt(parseInt(response.graph_width) * 1.3) + '"';
        content += ' style="border: none !important;">';
        content += '</iframe>';
        $('#div_chart').html(content);
      }

    }
  });
}


</script>




</body>
</html>
