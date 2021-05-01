<?php
include_once('config.php');
// print_r($_SESSION['PCA_DIMENSION_VAR']);

// Remove Historical Files
$dir = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
foreach (array('txt', 'csv') as $format) {
  unlink($dir . '/pca.' . $format);
  unlink($dir . '/pca_attributes.' . $format);
  unlink($dir . '/pca_var.' . $format);
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
<script type="text/javascript" src="../library/config.js"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>


<style>
table td {
  background-color: #E7E7E7;
}
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

            <!--------------------------------------------------------------------------------------------->
            <!-- Upload Files -->
            <!--------------------------------------------------------------------------------------------->

            <h4>
              Upload Zip File or
              <a href="javascript:void(0);" id="btn_load_example">
                View Demo
              </a>:
            </h4>

            <div class="alert alert-warning">
              <strong>Required Files</strong>
              <ul>
                <li>PCA_barchart.csv</li>
                <li>PCA_ind.contrib.csv</li>
                <li>PCA_ind.coord.csv</li>
                <li>PCA_ind.cos2.csv</li>
                <li>PCA_var.contrib.csv</li>
                <li>PCA_var.coord.csv</li>
                <li>PCA_var.cor.csv</li>
                <li>PCA_var.cos2.csv</li>
              </ul>
              <strong>Optional Files</strong>
              <ul>
                <li>PCA_attributes.csv</li>
                <li>PCA_quali.sup.coord.csv</li>
                <li>PCA_quali.sup.cos2.csv</li>
                <li>PCA_quanti.sup.coord.csv</li>
                <li>PCA_quanti.sup.cor.csv</li>
                <li>PCA_quanti.sup.cos2.csv</li>
              </ul>
            </div>
            <form id="form_upload_files">
              <div class="row mt-3">
                <div class="col-md-2 text-right pt-1"><span class="text-danger font-weight-bold">Data File:</span></div>
                <div class="col-md-10">
                  <input type="file" name="file"
                    id="input_upload_file"
                    onchange="$('#btn_submit').show()">
                  <br />

                  <button class="btn btn-primary mt-3 mr-3 hidden" id="btn_submit">
                    <i class="fa fa-upload"></i>
                    Upload Zip
                  </button>

                  <a href="r_example.zip" class="btn btn-warning mt-3" download>
                    <i class="fa fa-download"></i>
                    Download Example Zip
                  </a>

                </div>
              </div>
            </form>




            <div id="debug"></div>

          </div>



        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>











<script>


$(document).ready(function() {


  //----------------------------------------------------------------------------------
  // Load Example
  //----------------------------------------------------------------------------------
  $(document).on('click', '#btn_load_example', function() {
    $.ajax({
      type: 'POST',
      url: 'exe_r.php?action=load_example',
      success: function(response) {
        window.location = 'index_r_individuals_plot.php';
      }
    });
  });

  //----------------------------------------------------------------------------------
  // Upload Zip File
  //----------------------------------------------------------------------------------
	var options = {
		url: 'exe_r.php?action=upload_zip',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit')
        .attr('disabled', '')
        .children(':first')
        .removeClass('fa-upload')
        .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){
			$('#btn_submit')
        .removeAttr('disabled')
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-upload');
      // alert(response);
      if (response.substring(0, 5) == 'Error') {
        bootbox.alert(response);
      } else {
        window.location = 'index_r_individuals_plot.php';
      }

			return true;
		}
  };
	$('#form_upload_files').ajaxForm(options);






});

</script>




</body>
</html>
