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
      				PCA Scatter Plot Tool
      			</h1>
            <hr />

            <?php include_once('component_header.php'); ?>



            <!--------------------------------------------------------------------------------------------->
            <!-- Upload 3 Files -->
            <!--------------------------------------------------------------------------------------------->

            <h4>Upload Files: <span class="text-danger" style="font-size:16px;">Data file is required</span></h4>
            <!-- <div class="alert alert-warning">
              <strong>Notes:</strong>
              <ul class="mb-0">
                <li>You can select up to 3 files.</li>
                <li>All the files have to be in the same format (csv, txt or tsv).</li>
                <li>The file for individuals and coordinates is required, its name must starts with 'pca_data'.</li>
                <li>The file for individuals attributes is optional, its name must starts with 'pca_attributes'.</li>
                <li>The file for variances is optionval, its name must starts with 'pca_var'.</li>
              </ul>
            </div> -->


            <form id="form_upload_files">
              <div class="row mt-3" id="container_form_upload_file_step_1">
                <div class="col-md-2 text-right pt-1"><span class="text-danger font-weight-bold">Data File:</span></div>
                <div class="col-md-10">
                  <input type="file" name="file1"
                    id="input_upload_file_step_1"
                    onchange="$('#btn_submit_upload_files').show()">
                </div>
              </div>


              <div class="row mt-3" id="container_form_upload_file_step_2">
                <div class="col-md-2 text-right pt-1"><strong>Attributes File: </strong></div>
                <div class="col-md-10">
                  <input type="file" name="file2"
                    id="input_upload_file_step_2">
                </div>
              </div>


              <div class="row mt-3" id="container_form_upload_file_step_3">
                <div class="col-md-2 text-right pt-1"><strong>Variance File: </strong></div>
                <div class="col-md-10">
                  <input type="file" name="file3"
                    id="input_upload_file_step_3">
                </div>
              </div>


              <div class="row mt-3">
                <div class="col-md-2 text-right pt-1"><strong>Format: </strong></div>
                <div class="col-md-10">
                  <label>
                    <input type="radio" name="format_files" value="csv" checked>
                    csv
                  </label> &nbsp;
                  <label>
                    <input type="radio" name="format_files" value="txt">
                    txt / tsv
                  </label> &nbsp;
                </div>
              </div>

              <div class="row mt-1 mb-3">
                <div class="col-md-2 text-right pt-1"></div>
                <div class="col-md-10">
                  <button class="btn btn-info hidden"
                    id="btn_submit_upload_files">
                    <i class="fa fa-upload"></i> Upload Files
                  </button>
                </div>
              </div>

            </form>



            <div class="row mt-1 mb-3" style="display:none;" id="container_detail_step_1">
              <div class="col-md-2 text-right pt-2"><strong>X: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_headers" id="select_x">
                </select>
              </div>
              <div class="col-md-2 text-right pt-2"><strong>Y: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_headers" id="select_y">
                </select>
              </div>
            </div>
            <div class="row mt-1 mb-3" style="display:none;" id="container_detail_step_2">
              <div class="col-md-2 text-right pt-2"><strong>Color By: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_legends" id="select_legend_1">
                  <option value="">(None)</option>
                </select>
              </div>
              <div class="col-md-2 text-right pt-2"><strong>Shape By: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_legends" id="select_legend_2">
                  <option value="">(None)</option>
                </select>
              </div>
            </div>
            <div class="row mt-1 mb-3" style="display:none;" id="container_detail_step_3">
              <div class="col-md-2 text-right pt-2"><strong>Vars: </strong></div>
              <div class="col-md-10">
                <table class="table table-bordered table-sm" id="table_var">
                  <thead>
                    <tr class="table-inverse"><th>Dimension</th><th>Percentage</th></tr>
                  </thead>
                </table>
              </div>
            </div>


            <!--------------------------------------------------------------------------------------------->
            <!-- Step 1: Individuals and Coordinates -->
            <!--------------------------------------------------------------------------------------------->
<!--
            <h4>Individuals and Coordinates <span class="red">(Required)</span></h4>

            <div class="row mt-3" id="container_form_upload_file_step_1">
              <div class="col-md-2 text-right pt-1"><strong>Upload File: </strong></div>
              <div class="col-md-10">
                <form id="form_upload_file_step_1">
                  <input type="file" name="file"
                    id="input_upload_file_step_1"
                    onchange="$(this).parent().find('button').show()">

                  <label>
                    <input type="radio" name="format_step_1" value="csv" checked>
                    csv
                  </label> &nbsp;
                  <label>
                    <input type="radio" name="format_step_1" value="txt">
                    txt / tsv
                  </label> &nbsp;

                  <button class="btn btn-sm btn-outline-primary hidden"
                    id="btn_submit_upload_file_step_1">
                    <i class="fa fa-upload"></i> Upload
                  </button>
                </form>
              </div>
            </div>

            <div class="row mt-1 mb-3" style="display:none;" id="container_detail_step_1">
              <div class="col-md-2 text-right pt-2"><strong>X: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_headers" id="select_x">
                </select>
              </div>
              <div class="col-md-2 text-right pt-2"><strong>Y: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_headers" id="select_y">
                </select>
              </div>
            </div>
            <hr />
 -->


            <!--------------------------------------------------------------------------------------------->
            <!-- Step 2: Individual Attributes -->
            <!--------------------------------------------------------------------------------------------->
<!--
            <h4>Individual Attributes <span class="green">(Optional)</span></h4>

            <div class="row mt-3" id="container_form_upload_file_step_2">
              <div class="col-md-2 text-right pt-1"><strong>Upload File: </strong></div>
              <div class="col-md-10">
                <form id="form_upload_file_step_2">
                  <input type="file" name="file"
                    id="input_upload_file_step_2"
                    onchange="$(this).parent().find('button').show()">

                  <label>
                    <input type="radio" name="format_step_2" value="csv" checked>
                    csv
                  </label> &nbsp;
                  <label>
                    <input type="radio" name="format_step_2" value="txt">
                    txt / tsv
                  </label> &nbsp;

                  <button class="btn btn-sm btn-outline-primary hidden"
                    id="btn_submit_upload_file_step_2">
                    <i class="fa fa-upload"></i> Upload
                  </button>
                </form>
              </div>
            </div>

            <div class="row mt-1 mb-3" style="display:none;" id="container_detail_step_2">
              <div class="col-md-2 text-right pt-2"><strong>Label Color: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_legends" id="select_legend_1">
                  <option value="">(None)</option>
                </select>
              </div>
              <div class="col-md-2 text-right pt-2"><strong>Label Shape: </strong></div>
              <div class="col-md-4">
                <select class="form-control select_legends" id="select_legend_2">
                  <option value="">(None)</option>
                </select>
              </div>
            </div>
            <hr /> -->




            <!--------------------------------------------------------------------------------------------->
            <!-- Step 3: Variance Explained by Each Dimension  -->
            <!--------------------------------------------------------------------------------------------->
<!--
            <h4>Variance Explained by Each Dimension <span class="green">(Optional)</span></h4>

            <div class="row mt-3" id="container_form_upload_file_step_3">
              <div class="col-md-2 text-right pt-1"><strong>Upload File: </strong></div>
              <div class="col-md-10">
                <form id="form_upload_file_step_3">
                  <input type="file" name="file"
                    id="input_upload_file_step_3"
                    onchange="$(this).parent().find('button').show()">

                  <label>
                    <input type="radio" name="format_step_3" value="csv" checked>
                    csv
                  </label> &nbsp;
                  <label>
                    <input type="radio" name="format_step_3" value="txt">
                    txt / tsv
                  </label> &nbsp;

                  <button class="btn btn-sm btn-outline-primary hidden"
                    id="btn_submit_upload_file_step_3">
                    <i class="fa fa-upload"></i> Upload
                  </button>
                </form>
              </div>
            </div>

            <div class="row mt-1 mb-3" style="display:none;" id="container_detail_step_3">
              <div class="col-md-2 text-right pt-2"><strong>Vars: </strong></div>
              <div class="col-md-10">
                <table class="table table-bordered table-sm" id="table_var">
                  <thead>
                    <tr class="table-inverse"><th>Dimension</th><th>Percentage</th></tr>
                  </thead>
                </table>
              </div>
            </div>

 -->



            <div class="row mt-3 mb-3">
              <div class="col-md-2 text-right pt-2"></div>
              <div class="col-md-4">
                <button class="btn btn-primary hidden" id="btn_submit_draw_chart">
                  <i class="fa fa-bar-chart"></i>
                  Draw Chart
                </button>
              </div>
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


  //----------------------------------------------------------------------------------
  // Upload 3 Files
  //----------------------------------------------------------------------------------
	var options = {
		url: 'exe.php?action=upload_files',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit_upload_files')
        .attr('disabled', '')
        .children(':first')
        .removeClass('fa-upload')
        .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){
			$('#btn_submit_upload_files')
        .removeAttr('disabled')
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-upload');

      $('#container_form_upload_files').hide();

      if (response.type && response.type == 'Error') {
        bootbox.alert('<h4><span class="text-danger">Error:</span> ' + response.detail + '</h4>');
      } else {

        // Response for File 1
        var header_1 = response.header_1;
        $('.select_headers')
          .html(
            header_1
              .map((d) => '<option value="' + d + '">' + d + '</option>')
			  .join('')
            );
        $('#select_y option:nth-child(2)').attr('selected', '');
        $('#btn_submit_draw_chart').show();
        $('#container_detail_step_1').show();

        // Response for File 2
        if (response.header_2 != null && response.header_2 != undefined) {
          var header_2 = response.header_2;
          $('.select_legends')
            .append(
              header_2
                .map((d) => '<option value="' + d + '">' + d + '</option>')
                .join('')
              );
        }
        $('#container_detail_step_2').show();

        // Response for File 3
        if (response.data_3 != null && response.data_3 != undefined && response.data_3 != [] && response.data_3.length != 0) {
          $('#container_form_upload_file_step_3').hide();
          var data_3 = response.data_3
          $('#table_var')
            .append(
              data_3
                .map((d) => `<tr><td>${d[0]}</td><td>${d[1]}</td></tr>`)
                .join('')
              );
          $('#container_detail_step_3').show();
        }

      }

			return true;
		}
  };
	$('#form_upload_files').ajaxForm(options);




  //----------------------------------------------------------------------------------
  // Upload File Step 1
  //----------------------------------------------------------------------------------
	var options = {
		url: 'exe.php?action=upload_file_step_1',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit_upload_file_step_1')
        .attr('disabled', '')
        .children(':first')
        .removeClass('fa-upload')
        .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){
			$('#btn_submit_upload_file_step_1')
        .removeAttr('disabled')
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-upload');
      $('#container_form_upload_file_step_1').hide();
      var header = response.header;

      // Change Select Content
      $('.select_headers')
        .html(
          header
            .map((d) => '<option value="' + d + '">' + d + '</option>')
            .join('')
          );
      $('#select_y option:nth-child(2)').attr('selected', '');
      $('#btn_submit_draw_chart').show();
      $('#container_detail_step_1').show();

			return true;
		}
  };
	$('#form_upload_file_step_1').ajaxForm(options);




  //----------------------------------------------------------------------------------
  // Upload File Step 2
  //----------------------------------------------------------------------------------
	var options = {
		url: 'exe.php?action=upload_file_step_2',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit_upload_file_step_2')
        .attr('disabled', '')
        .children(':first')
        .removeClass('fa-upload')
        .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){
			$('#btn_submit_upload_file_step_2')
        .removeAttr('disabled')
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-upload');
      $('#container_form_upload_file_step_2').hide();
      var header = response.header;

      // Change Select Content
      $('.select_legends')
        .append(
          header
            .map((d) => '<option value="' + d + '">' + d + '</option>')
            .join('')
          );

      $('#container_detail_step_2').show();
			return true;
		}
  };
	$('#form_upload_file_step_2').ajaxForm(options);





  //----------------------------------------------------------------------------------
  // Upload File Step 3
  //----------------------------------------------------------------------------------
	var options = {
		url: 'exe.php?action=upload_file_step_3',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit_upload_file_step_3')
        .attr('disabled', '')
        .children(':first')
        .removeClass('fa-upload')
        .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){
			$('#btn_submit_upload_file_step_3')
        .removeAttr('disabled')
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-upload');

      if (response.type && response.type == 'Error') {
        bootbox.alert(response.detail);
      } else {
        $('#container_form_upload_file_step_3').hide();
        var data = response.data
        $('#table_var')
          .append(
            data
              .map((d) => `<tr><td>${d[0]}</td><td>${d[1]}</td></tr>`)
              .join('')
            );
        $('#container_detail_step_3').show();
      }

			return true;
		}
  };
	$('#form_upload_file_step_3').ajaxForm(options);




  //----------------------------------------------------------------------------------
  // Draw Chart
  //----------------------------------------------------------------------------------

  $(document).on('click', '#btn_submit_draw_chart', function() {
    var vm          = $(this);
    var x           = $('#select_x').val();
    var y           = $('#select_y').val();
    var legend_1    = $('#select_legend_1').val();
    var legend_2    = $('#select_legend_2').val();
    var label_size  = 10;
    var marker_size = 64;
    vm
      .attr('disabled', '')
      .children(':first')
      .removeClass('fa-bar-chart')
      .addClass('fa-spin fa-spinner');
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
        show_labels: 'true'
      },
      success: function(response) {
        vm
          .removeAttr('disabled')
          .children(':first')
          .addClass('fa-bar-chart')
          .removeClass('fa-spin fa-spinner');

        if (response.type && response.type == 'Error') {
          bootbox.alert(response.detail);
        } else {
          window.location = 'view_chart.php?time='+ response.chart_time;
        }

      }
    });
  });


});

</script>




</body>
</html>
