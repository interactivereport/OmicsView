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
$dir = get_PCA_dir($TIME_STAMP);

// Specify Folder for Genes & Samples
$TIME_STAMP = 0;
if (isset($_GET['tid']) && intval($_GET['tid']) != 0) {
  $TIME_STAMP = intval($_GET['tid']);
//  $dir = "{$BXAF_CONFIG['USER_FILES_PCA']}/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/{$_GET['tid']}";
  $dir = get_PCA_dir($TIME_STAMP);
  if (!is_dir($dir)) {
    echo 'No result exists.';
    exit();
  }
}

// Read Colnames
$file = fopen($dir . '/PCA_var.coord.csv', "r") or die('No file.');
$header = array_slice(fgetcsv($file), 1);
fclose($file);

// Dir: /opt/lampp/htdocs/diseaseexpress_share/work/plot/user_files_pca
// File Names:
// decathlon_eig.csv         -> PCA_barchart.csv
// decathlon_var.contrib.csv -> PCA_var.contrib.csv
// decathlon_var.coord.csv   -> PCA_var.coord.csv
// decathlon_var.cor.csv     -> PCA_var.cor.csv
// decathlon_var.cos2.csv    -> PCA_var.cos2.csv
// print_r($_SESSION['PCA_DIMENSION_VAR']);

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
.table-sm td, .table-sm th {
  padding: 0.25rem 0.4rem !important;
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

            <br />
            <strong>Select Dimensions</strong>
            <select class="form-control mb-3" style="max-width:200px;" id="select_dim">
              <?php
                $index = 1;
                foreach ($header as $colname) {
                  echo '<option value="' . $colname . '">';
                  echo 'PC' . $index;
                  echo '</option>';
                  $index++;
                }
              ?>
            </select>

            <!--------------------------------------------------------------------------------------------->
            <!-- Data Table -->
            <!--------------------------------------------------------------------------------------------->
            <div id="div_variables_datatable">
              <h4>Active Variables</h4>
              <div id="container_variables_datatable">
              <i class="fas fa-spin fa-spinner fa-pulse"></i> Loading the content...
              <?php /*
              <!-- <table class="table table-sm table-striped table-bordered" id="variables_datatable">
                <thead>
                  <tr>
                    <th>Variable</th>
                    <th>Contrib</th>
                    <th>Coord</th>
                    <th>Cor</th>
                    <th>Cos2</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table> -->
			  */ ?>
              </div>

              <?php if (file_exists($dir . '/' . 'PCA_quanti.sup.coord.csv')) { ?>
              <h4>Supplementary Variables</h4>
              <table class="table table-sm table-striped table-bordered" id="supplementary_variables_datatable">
                <thead>
                  <tr>
                    <th>Variable</th>
                    <th>Coord</th>
                    <th>Cor</th>
                    <th>Cos2</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <?php } if (file_exists($dir . '/' . 'PCA_quali.sup.coord.csv')) { ?>

              <h4>Qualitative Supplementary Variables</h4>
              <table class="table table-sm table-striped table-bordered" id="qualitative_variables_datatable">
                <thead>
                  <tr>
                    <th>Variable</th>
                    <th>Coord</th>
                    <th>Cos2</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <?php } ?>


            </div>



            <div id="debug"></div>

          </div>



        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>











<script>


$(document).ready(function() {

  $('#btn_index_r_variables_table').addClass('active');
  onChangeDimHandler();

  //----------------------------------------------------------------------------------
  // Get Variables Table Data
  //----------------------------------------------------------------------------------
  $(document).on('change', '#select_dim', () => onChangeDimHandler());



});


function onChangeDimHandler() {
  var select_dim = $('#select_dim').val();
  $.ajax({
    type: 'POST',
    url: 'exe_r.php?action=get_variables_data_table',
    data: {
      dim: select_dim,
      time_stamp: '<?php echo $TIME_STAMP; ?>'
    },
    success: function(response) {

      var table_content = '';
      table_content += '<table class="table table-sm table-striped table-bordered" id="variables_datatable">';
      table_content += '  <thead>';
      table_content += '    <tr>';
      table_content += '      <th>Variable</th>';
      table_content += '      <th>Contrib</th>';
      table_content += '      <th>Coord</th>';
      table_content += '      <th>Cor</th>';
      table_content += '      <th>Cos2</th>';
      table_content += '    </tr>';
      table_content += '  </thead>';
      table_content += '  <tbody>';
      table_content += response
        .data
        .map(data => {
          var row_content = '';
          row_content += '<tr>';
          row_content += '  <td>' + data[0] + '</td>';
          row_content += '  <td>' + data[1] + '</td>';
          row_content += '  <td>' + data[2] + '</td>';
          row_content += '  <td>' + data[3] + '</td>';
          row_content += '  <td>' + data[4] + '</td>';
          row_content += '</tr>';
          return row_content;
        })
        .join('');
      table_content += '  </tbody>';
      table_content += ' </table>';
      $('#container_variables_datatable').html(table_content);
      $('#variables_datatable').DataTable();


      if (response.data_supplementary != null) {
        var table_content_supplementary = response
          .data_supplementary
          .map(data => {
            var row_content = '';
            row_content += '<tr>';
            row_content += '  <td>' + data[0] + '</td>';
            row_content += '  <td>' + data[1] + '</td>';
            row_content += '  <td>' + data[2] + '</td>';
            row_content += '  <td>' + data[3] + '</td>';
            row_content += '</tr>';
            return row_content;
          })
          .join('');
        $('#supplementary_variables_datatable')
          .find('tbody')
            .html(table_content_supplementary)
          .parent()
            .DataTable();
      }
      if (response.data_quantitative != null) {
        var table_content_quantitative = response
          .data_quantitative
          .map(data => {
            var row_content = '';
            row_content += '<tr>';
            row_content += '  <td>' + data[0] + '</td>';
            row_content += '  <td>' + data[1] + '</td>';
            row_content += '  <td>' + data[2] + '</td>';
            row_content += '</tr>';
            return row_content;
          })
          .join('');
        $('#qualitative_variables_datatable')
          .find('tbody')
            .html(table_content_quantitative)
          .parent()
            .DataTable();
      }



    }
  });

}

</script>




</body>
</html>
