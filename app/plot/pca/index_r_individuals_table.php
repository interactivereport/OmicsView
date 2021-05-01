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

// unlink($dir . '/PCA_attributes.csv');
// unlink($dir . '/PCA_attributes.txt');

// Dir: /opt/lampp/htdocs/diseaseexpress_share/work/plot/user_files_pca
// File Names:
// decathlon_eig.csv         -> PCA_barchart.csv
// decathlon_var.contrib.csv -> PCA_var.contrib.csv
// decathlon_var.coord.csv   -> PCA_var.coord.csv
// decathlon_var.cor.csv     -> PCA_var.cor.csv
// decathlon_var.cos2.csv    -> PCA_var.cos2.csv


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
            <i class="fas fa-spin fa-spinner fa-pulse"></i> Loading the content...
            <?php /*
              <table class="table table-sm table-striped table-bordered" id="variables_datatable">
                <thead>
                  <tr>
                    <th>Variable</th>
                    <th>Contrib</th>
                    <th>Coord</th>
                    <th>Cos2</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
			  */ ?>
              

            </div>


            <hr />
            <h4>Upload Attributes File:</h4>
            <form id="form_upload_attributes_file">
              <input type="file" name="file"
                id="input_upload_attributes_file"
                onchange="$(this).parent().find('button').show()">
              <label>
                <input type="radio" name="format" value="csv" checked>
                csv
              </label> &nbsp;
              <label>
                <input type="radio" name="format" value="txt">
                txt / tsv
              </label> &nbsp;
              <button class="btn btn-sm btn-outline-primary hidden"
                id="btn_submit_upload_attributes_file">
                <i class="fa fa-upload"></i> Upload
              </button>
            </form>
            <div id="container_attributes_file" class="w-100">
            <i class="fas fa-spin fa-spinner fa-pulse"></i> Loading the content...
              <table class="table table-bordered table-striped table-sm" id="table_attributes">
                <thead><tr></tr></thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="w-100" style="height:100px;"></div>



            <div id="debug"></div>

          </div>



        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>











<script>


$(document).ready(function() {

  $('#btn_index_r_individuals_table').addClass('active');
  onChangeDimHandler();

  //----------------------------------------------------------------------------------
  // Get Variables Table Data
  //----------------------------------------------------------------------------------
  $(document).on('change', '#select_dim', () => onChangeDimHandler());


  //----------------------------------------------------------------------------------
  // Upload File
  //----------------------------------------------------------------------------------
  var options = {
		url: 'exe_r.php?action=upload_file',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit_upload_attributes_file')
        .attr('disabled', '')
        .children(':first')
        .removeClass('fa-upload')
        .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){

			$('#btn_submit_upload_attributes_file')
        .removeAttr('disabled')
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-upload');

      window.location = 'index_r_individuals_plot.php';

      if (response.type == 'Error') {
        alert(response.Detail);
      } else {
        var header = response.header;
        var header_content = response
          .header
          .map(data => {
            return '<td>' + data + '</td>';
          })
          .join('');
        var attributes_content = response
          .data
          .map(data => {
            var row = data
              .map(row_info => {
                return '<td>' + row_info + '</td>';
              })
              .join('');
            return '<tr>' + row + '</tr>';
          })
          .join('');

        $('#table_attributes thead tr').html(header_content);
        $('#table_attributes tbody').html(attributes_content);
        $('#table_attributes').DataTable();

        $('#input_upload_attributes_file').val('');
        $('#btn_submit_upload_attributes_file').hide();
      }

			return true;
		}
  };
	$('#form_upload_attributes_file').ajaxForm(options);



});



function onChangeDimHandler() {
  var select_dim = $('#select_dim').val();
  $.ajax({
    type: 'POST',
    url: 'exe_r.php?action=get_individuals_data_table',
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
          row_content += '</tr>';
          return row_content;
        })
        .join('');

      table_content += '  </tbody>';
      table_content += '</table>';

      $('#div_variables_datatable').html(table_content);
      $('#variables_datatable').DataTable();


    }
  });
};

</script>




</body>
</html>
