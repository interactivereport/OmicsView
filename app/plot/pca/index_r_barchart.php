<?php
include_once('config.php');

$PAGE['Category'] = 'List';
$PAGE['Barcode']  = 'Visualize PCA Results';


// Specify Folder for Genes & Samples
$TIME_STAMP = 0;
if (isset($_GET['tid']) && intval($_GET['tid'] != 0)) {
  $TIME_STAMP = intval($_GET['tid']);
  $dir = "{$BXAF_CONFIG['USER_FILES_PCA']}/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/{$TIME}";
  if (!is_dir($dir)) {
    echo 'No result exists.';
    exit();
  }
}

// Dir: /opt/lampp/htdocs/diseaseexpress_share/work/plot/user_files_pca

// File Names:

// decathlon_eig.csv         -> PCA_barchart.csv
// decathlon_var.contrib.csv -> PCA_var.contrib.csv
// decathlon_var.coord.csv   -> PCA_var.coord.csv
// decathlon_var.cor.csv     -> PCA_var.cor.csv
// decathlon_var.cos2.csv    -> PCA_var.cos2.csv

// decathlon_ind.coord.csv   -> PCA_ind.coord.csv
// decathlon_ind.contrib.csv -> PCA_ind.contrib.csv
// decathlon_ind.cos2.csv    -> PCA_ind.cos2.csv


// decathlon_quanti.sup.coord.csv -> PCA_quanti.sup.coord.csv
// decathlon_quanti.sup.cor.csv   -> PCA_quanti.sup.cor.csv
// decathlon_quanti.sup.cos2.csv  -> PCA_quanti.sup.cos2.csv

// decathlon_quali.sup.coord.csv -> PCA_quali.sup.coord.csv
// decathlon_quali.sup.cos2.csv  -> PCA_quali.sup.cos2.csv


//----------------------------------------------------------------------------------------------------
// If 'id' is set
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
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

            <!--------------------------------------------------------------------------------------------->
            <!-- BarChart -->
            <!--------------------------------------------------------------------------------------------->
            <div id="div_barchart">
              <span id="msg_loading"><i class="fas fa-spinner fa-pulse"></i> Loading the content...</span>
            </div>







            <div id="debug"></div>

          </div>


        </div>
        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>











<script>


$(document).ready(function() {

  $('#btn_index_r_barchart').addClass('active');

  //----------------------------------------------------------------------------------
  // Get BarChart Info
  //----------------------------------------------------------------------------------
  get_bar_chart();


});


function get_bar_chart() {
  $.ajax({
    type: 'POST',
    url: 'exe_r.php?action=get_barchart',
    data: { time_stamp: '<?php echo $TIME_STAMP; ?>' },
    success: function(response) {

      var type = response.type;
      if (type == 'Error') {
        bootbox.alert(response.detail);
      }
      else if (type == 'Success') {
        $('#msg_loading').hide();

        var data = [
          {
            x: response.data.x,
            y: response.data.y,
            type: 'bar'
          }
        ];
        var layout = {
          title: 'Bar Chart',
          width: 800,
          height: 600,
          xaxis: { title: 'Dimensions' },
          yaxis: { title: 'Percentage of Variance' },
        };
        Plotly.newPlot('div_barchart', data, layout);
      } else if (type == 'Pending') {
        setTimeout(function() { get_bar_chart(); }, 2000);
      }
    }
  });
}
</script>




</body>
</html>
