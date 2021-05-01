<?php
include_once('config.php');


//----------------------------------------------------------------------------------------------------
// If 'id' is set
$SAVED_RESULT = false;
if (isset($_GET['id']) && trim($_GET['id']) != '') {
  $ROWID = bxaf_decrypt($_GET['id'], $BXAF_CONFIG['BXAF_KEY']);
  if (intval($ROWID) != 0) {
    $SAVED_RESULT = true;
    $file_list = array(
      'pca.txt',
      'pca.csv',
      'pca_attributes.txt',
      'pca_attributes.csv',
      'pca_var.txt',
      'pca_var.csv',
    );
    $dir = $BXAF_CONFIG['SAVED_PCA'] . '/' . $_GET['id'];
    foreach ($file_list as $file) {
      copy(
        $dir . '/' . $file,
        $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $file
      );
    }
  }
}




//----------------------------------------------------------------------------------------------------
// If 'id' is not set
//
// Get Header
$dir           = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
$format        = (file_exists($dir . '/pca.txt')) ? 'txt' : 'csv';
$file          = fopen($dir . '/pca.' . $format, "r") or die('No file.');
$file_data     = array();
$delimiter     = ($format == 'txt') ? "\t" : ",";
while(($row    = fgetcsv($file, 1000, $delimiter)) !== false){
  $file_data[] = $row;
}
fclose($file);
$pca_header    = array_slice($file_data[0], 1);


$dir  = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
$format_attributes  = (file_exists($dir . '/pca.txt')) ? 'txt' : 'csv';
if (file_exists($dir . '/pca_attributes.' . $format_attributes)) {
  $file          = fopen($dir . '/pca_attributes.' . $format_attributes, "r") or die('No file.');
  $file_data     = array();
  $delimiter     = ($format_attributes == 'txt') ? "\t" : ",";
  while(($row    = fgetcsv($file, 1000, $delimiter)) !== false){
    $file_data[] = $row;
  }
  fclose($file);
  $pca_label_header = array_slice($file_data[0], 1);

  $pca_label_numeric_header = $pca_label_header;

  foreach (array_slice($file_data[1], 1) as $key => $value) {
    if (floatval($value) == 0) {
      unset($pca_label_numeric_header[$key]);
    }
  }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../library/wenk.min.css" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/rangetouch.min.js"></script>
<script type="text/javascript" src="../library/config.js"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>

<style>
table td {
  background-color: #E7E7E7;
}
strong {
  color: #666;
}

/*.example__range {
    padding: 10px 15px;
    background-color: #CCC;
    border-radius: 100px;
    box-shadow: inset 0 1px 1px rgba(52,63,74,.1), 0 1px 0 rgba(255,255,255,.15);
}
input[type=range] {
    display: block;
    height: 7px;
    width: 100%;
    margin: 0;
    padding: 0;
    vertical-align: middle;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
    border-radius: 100px;
    background-color: #666;
    color: rgb(144, 144, 144);
    text-rendering: auto;
    color: initial;
    letter-spacing: normal;
    word-spacing: normal;
    text-transform: none;
    text-indent: 0px;
    text-shadow: none;
    display: inline-block;
    text-align: start;
    margin: 0em 0em 0em 0em;
    font: 11px system-ui;
}*/
.input_range {
  width: 100%;
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
      				PCA Scatter Plot Tool
      			</h1>
            <hr />
            <?php include_once('component_header.php'); ?>

            <div class="row">
              <div class="col-md-4" style="max-width:300px;">
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
                        foreach ($pca_header as $colname) {
                          echo '<option value="' . $colname . '"';
                          if ($colname == $_SESSION['PCA_SETTING']['x']) {
                            echo ' selected';
                          }
                          echo '>' . $colname;
                          if (isset($_SESSION['PCA_DIMENSION_VAR'][$colname])) {
                            echo ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$colname], 3) . '%)';
                          }
                          echo '</option>';
                        }
                      ?>
                    </select>

                    <!------------------------------------------------------------------------------>
                    <!-- Y Coordinate -->
                    <p class="mb-0 mt-2"><strong>Y Coordinate:</strong></p>
                    <select class="form-control" id="select_y">
                      <?php
                        $index = 0;
                        foreach ($pca_header as $colname) {
                          echo '<option value="' . $colname . '"';
                          if (!$_SESSION['PCA_SETTING']['y'] || $SAVED_RESULT) {
                            if ($index == 1) echo ' selected';
                          } else {
                            if ($colname == $_SESSION['PCA_SETTING']['y']) {
                              echo ' selected';
                            }
                          }

                          echo '>' . $colname;
                          if (isset($_SESSION['PCA_DIMENSION_VAR'][$colname])) {
                            echo ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$colname], 3) . '%)';
                          }
                          echo '</option>';
                          $index++;
                        }
                      ?>
                    </select>

                    <!------------------------------------------------------------------------------>
                    <!-- Label Color -->
                    <p class="mb-0 mt-2"><strong>Color By:</strong></p>
                    <select class="form-control" id="select_legend_1">
                      <option value="">(None)</option>
                      <?php
                        foreach ($pca_label_header as $colname) {
                          echo '<option value="' . $colname . '"';
                          if (file_exists($dir . '/pca_attributes.' . $format)) {
                            if ($colname == $_SESSION['PCA_SETTING']['color']) {
                              echo ' selected';
                            }
                            echo '>' . $colname;
                            if (isset($_SESSION['PCA_DIMENSION_VAR'][$colname])) {
                              echo ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$colname], 3) . '%)';
                            }
                            echo '</option>';
                          }
                        }
                      ?>
                    </select>


                    <!------------------------------------------------------------------------------>
                    <!-- Label Color -->
                    <p class="mb-0 mt-2"><strong>Shape By:</strong></p>
                    <select class="form-control" id="select_legend_2">
                      <option value="">(None)</option>
                      <?php
                        foreach ($pca_label_header as $colname) {
                          echo '<option value="' . $colname . '"';
                          if (file_exists($dir . '/pca_attributes.' . $format)) {
                            if ($colname == $_SESSION['PCA_SETTING']['shape']) {
                              echo ' selected';
                            }
                            echo '>' . $colname;
                            if (isset($_SESSION['PCA_DIMENSION_VAR'][$colname])) {
                              echo ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$colname], 3) . '%)';
                            }
                            echo '</option>';
                          }
                        }
                      ?>
                    </select>




                    <!------------------------------------------------------------------------------>
                    <!-- Size By -->
                    <p class="mb-0 mt-2"><strong>Size By:</strong></p>
                    <select class="form-control" id="select_legend_3">
                      <option value="">(None)</option>
                      <?php
                        $index = 1;
                        foreach ($pca_header as $colname) {
                          echo '<option value="' . $colname . '">PC' . $index . '</option>';
                          // if (file_exists($dir . '/pca_attributes.' . $format)) {
                          //   if ($colname == $_SESSION['PCA_SETTING']['size']) {
                          //     echo ' selected';
                          //   }
                          //   echo '>' . $colname;
                          //   if (isset($_SESSION['PCA_DIMENSION_VAR'][$colname])) {
                          //     echo ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$colname], 3) . '%)';
                          //   }
                          //   echo '</option>';
                          // }
                          $index++;
                        }
                        foreach ($pca_label_numeric_header as $col) {
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


                    <!------------------------------------------------------------------------------>
                    <!-- Save Result -->
                    <?php if (!$SAVED_RESULT) { ?>
                    <button class="btn btn-primary mt-3"
                      id="btn_pre_save_result"
                      data-toggle="modal"
                      data-target="#modal_confirm_save_result">
                      <i class="fa fa-floppy-o"></i>
                      Save Result
                    </button>
                    <?php } ?>




                  </div>
                </div>
              </div>

              <div id="div_chart">
                <!-- <div class="loader loader-default is-active"
                     data-text="Loading Info"
                     style="margin-left:0px; margin-top:0px;"></div> -->
                <iframe
                  src="files/<?php echo $BXAF_CONFIG['BXAF_USER_CONTACT_ID']; ?>/chart_<?php echo $_GET['time']; ?>.html"
                  height="500"
                  width="700"
                  style="border: none !important;">
                </iframe>
              </div>
            </div>



            <div id="debug"></div>

          </div>
        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>


<!----------------------------------------------------------------------------------------------->
<!-- Modal to Confirm Save Result -->
<!----------------------------------------------------------------------------------------------->
<div class="modal fade" id="modal_confirm_save_result">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">SAVE PCA RESULT</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <strong class="gray">Title:</strong>
        <input class="form-control mb-2" id="save_result_title">
        <strong class="gray">Description:</strong>
        <textarea class="form-control" id="save_result_description"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="btn_save_result">
          <i class="fa fa-floppy-o"></i> Save Result
        </button>
      </div>
    </div>
  </div>
</div>







<script>


$(document).ready(function() {

  $('.example__range').on('mousemove', function() {
    var vm = $(this).find('.input_range');
    vm.parent()
      .prev()
      .find('.range_value')
      .html(vm.val());
  });

  refresh_chart();


  $('#input_graph_size').on('mousemove', function() {
    var width = $(this).val();
    var height = parseInt(parseInt(width) / 1.33333333);
    $('#graph_width').html(width);
    $('#graph_height').html(height);
  });


  //---------------------------------------------------------------------------
  // Save Result
  $(document).on('click', '#btn_save_result', function() {
    var vm          = $(this);
    var title       = $('#save_result_title').val();
    var description = $('#save_result_description').val();

    if (title == '') {
      bootbox.alert('Error: Please enter title.');
    }

    else {
      vm.attr('disabled', '')
        .children(':first')
        .removeClass('fa-floppy-o')
        .addClass('fa-spin fa-spinner');
      $.ajax({
        type: 'POST',
        url: 'exe.php?action=save_result',
        data: { title: title, description: description },
        success: function(response) {
          vm.removeAttr('disabled')
            .children(':first')
            .addClass('fa-floppy-o')
            .removeClass('fa-spin fa-spinner');
          $('#modal_confirm_save_result').modal('hide');
          bootbox.alert('The result is saved. You can view all your saved PCA results <a href="my_pca_results.php">here</a>.');
        }
      });
    }


  });


  //---------------------------------------------------------------------------
  // Re-draw Chart
  $(document).on(
    'change',
    '#select_x, #select_y, #select_legend_1, #select_legend_2, #select_legend_3, #input_label_size, #input_marker_size, #checkbox_show_labels, #input_graph_width, #input_graph_height, #input_graph_size',
    function() {

    var vm           = $(this);
    var x            = $('#select_x').val();
    var y            = $('#select_y').val();
    var legend_1     = $('#select_legend_1').val();
    var legend_2     = $('#select_legend_2').val();
    var legend_3     = $('#select_legend_3').val();
    var label_size   = $('#input_label_size').val();
    var marker_size  = $('#input_marker_size').val();
    var graph_width  = $('#input_graph_size').val();
    var graph_height = parseInt(parseInt(graph_width) / 1.33333333);

    var show_labels = 'false';
    if ($('#checkbox_show_labels').is(':checked')) show_labels = 'true';

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
        graph_height: graph_height
      },
      success: function(response) {
        if (response.type && response.type == 'Error') {
          bootbox.alert(response.detail);
        }

        else {
          var content = '<iframe';
          var time = response.chart_time;
          content += ' src="files/<?php echo $BXAF_CONFIG['BXAF_USER_CONTACT_ID']; ?>/chart_'+time+'.html"';
          content += ' height="' + parseInt(parseInt(response.graph_height) * 1.3) + '"';
          content += ' width="' + parseInt(parseInt(response.graph_width) * 1.3) + '"';
          content += ' style="border: none !important;">';
          content += '</iframe>';
          $('#div_chart').html(content);
        }
      }
    });


  });

});



function refresh_chart() {
  var x            = $('#select_x').val();
  var y            = $('#select_y').val();
  var legend_1     = $('#select_legend_1').val();
  var legend_2     = $('#select_legend_2').val();
  var label_size   = $('#input_label_size').val();
  var marker_size  = $('#input_marker_size').val();
  var graph_width  = $('#input_graph_size').val();
  var graph_height = parseInt(parseInt(graph_width) / 1.33333333);

  var show_labels = 'false';
  if ($('#checkbox_show_labels').is(':checked')) show_labels = 'true';

  $.ajax({
    type: 'POST',
    url: 'exe.php?action=generate_scatter_plot',
    data: {
      x: x,
      y: y,
      legend_1: legend_1,
      legend_2: legend_2,
      label_size: label_size,
      marker_size: marker_size,
      show_labels: show_labels,
      graph_width: graph_width,
      graph_height: graph_height
    },
    success: function(response) {
      if (response.type && response.type == 'Error') {
        bootbox.alert(response.detail);
      }

      else {
        var content = '<iframe';
        var time = response.chart_time;
        content += ' src="files/<?php echo $BXAF_CONFIG['BXAF_USER_CONTACT_ID']; ?>/chart_'+time+'.html"';
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
