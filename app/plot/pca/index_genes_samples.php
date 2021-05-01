<?php
include_once('config.php');

$sample_attributes = array(
  'DiseaseState' => 'Disease State',
  'Tissue'       => 'Tissue',
  'Gender'       => 'Gender',
  'SampleSource' => 'Sample Source',
  'DiseaseStage' => 'Disease Stage',
  'PlatformName' => 'Platform Name',
);
$sample_additional_attributes = array(
  'CellType' => 'Cell Type',
  'Collection' => 'Collection',
  'Description' => 'Description',
  'DiseaseCategory' => 'Disease Category',
  'Ethnicity' => 'Ethnicity',
  'Infection' => 'Infection',
  'Organism' => 'Organism',
  'PlatformGPL' => 'Platform (GPL)',
  'ProjectName' => 'Project Name',
  'Response' => 'Response',
  'SamplePathology' => 'Sample Pathology',
  'SampleType' => 'Sample Type',
  'SamplingTime' => 'Sampling Time',
  'Symptom' => 'Symptom',
  'TissueCategory' => 'Tissue Category',
  'Title' => 'Title',
  'Transfection' => 'Transfection',
  'Treatment' => 'Treatment',
);
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
      				PCA Tool for <?php echo $APP_MESSAGE['Genes']; ?> &amp; Samples
      			</h1>
            <hr />
            <?php include_once('component_header.php'); ?>

            <!--------------------------------------------------------------------------------------------->
            <!-- Form -->
            <!--------------------------------------------------------------------------------------------->

            <div class="row mt-3 mb-3">

              <!-- Gene Names -->
              <div class="col-md-6">
                <p>
                  <?php
				  
				  echo "<table>";
					echo "<tr>";
						if (true){
							echo "<td>";
								echo "<strong>{$APP_MESSAGE['Gene Names']}:</strong>";
							echo "</td>";
						}
						
						if (true){
							echo "<td>";
								echo "&nbsp;";
							echo "</td>";	
						}
						
						if (true){
							echo "<td>";
								echo "<a href='javascript:void(0);' id='btn_show_comparison_list' type='gene'><i class='fas fa-shopping-cart' aria-hidden='true'></i> {$APP_MESSAGE['Load Saved Genes']}</a>";
							echo "</td>";
						}
						
						if (true){
							echo "<td>";
								echo "&nbsp; &nbsp;";
							echo "</td>";	
						}
						
						
						if (true){
							echo "<td>";
								echo genesets_api_get_header_code('', '', 'textarea_gene_names');
								echo genesets_api_get_body_code();
							echo "</td>";
						}
						
						echo "</tr>";
					echo "</table>";
				  
				  ?>
                  
                  
                  
                  
                </p>
                <textarea class="form-control" style="height:200px;" id="textarea_gene_names"></textarea>
              </div>

              <!-- Sample Names -->
              <div class="col-md-6">
                <p>
                  <strong>Sample Names:</strong>
                  <a
                    href="javascript:void(0);"
                    id="btn_show_comparison_list"
                    type="sample">
                    <i class="fa fa-floppy-o"></i>
                    Load Saved Samples
                  </a>
                </p>
                <textarea
                  class="form-control"
                  style="height:200px;"
                  id="textarea_sample_names"></textarea>
              </div>

              <!-- Sample Attributes -->
              <div class="col-md-12 mt-3">
                <strong>Sample Attributes: </strong>
                <?php
                $index = 0;
                foreach ($sample_attributes as $key => $value) {
                  echo '
                  <label>
                    <input type="checkbox" class="checkbox_attributes" val="' . $key . '"';
                  echo ($index < 2) ? ' checked' : '';
                  echo '>
                      ' . $value . '
                    </input>
                  </label> &nbsp;';
                  $index++;
                }
                ?>
                <a
                  href="javascript:void(0);"
                  id="btn_more_attributes"
                  data-toggle="modal"
                  data-target="#modal_more_attributes">
                  (More Attributes)
                </a>
              </div>

              <div class="col-md-12 mt-3">
                <button class="btn btn-primary" id="btn_submit">
                  <i class="fa fa-upload"></i> Submit
                </button> &nbsp;
                <span id='busySection' style='display:none;'><i class='fas fa-spinner fa-spin' aria-hidden='true'></i></span>
                <a
                  href="index_r_barchart.php"
                  class="btn btn-success btn_after_analysis hidden">
                  <i class="fa fa-eye"></i> View Result
                </a> &nbsp;
                <a
                  href="files/<?php echo $BXAF_CONFIG['BXAF_USER_CONTACT_ID']; ?>/genes_samples.csv"
                  class="btn btn-warning btn_after_analysis hidden"
                  download>
                  <i class="fa fa-download"></i> Download Data
                </a>
              </div>
            </div>

            <div id="debug"></div>

          </div>



        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>



  <!--------------------------------------------------------------------------------------->
  <!-- Select Gene List -->
  <!--------------------------------------------------------------------------------------->
  <div class="modal fade" id="modal_select_gene_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-lg" role="document">
  		<div class="modal-content">
  			<div class="modal-header">
          <h4 class="modal-title">Select GeneList</h4>
  				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  			</div>
  			<div class="modal-body" id="modal_select_gene_list_body">
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-primary"
                  data-dismiss="modal" id="btn_select_gene_list">
            Select
          </button>
  			</div>
  		</div>
  	</div>
  </div>
  <div class="modal fade" id="modal_select_sample_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-lg" role="document">
  		<div class="modal-content">
  			<div class="modal-header">
          <h4 class="modal-title">Select SampleList</h4>
  				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  			</div>
  			<div class="modal-body" id="modal_select_sample_list_body">
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-primary"
                  data-dismiss="modal" id="btn_select_sample_list">
            Select
          </button>
  			</div>
  		</div>
  	</div>
  </div>


  <!--------------------------------------------------------------------------------------->
  <!-- More Attributes -->
  <!--------------------------------------------------------------------------------------->
  <div class="modal fade" id="modal_more_attributes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document">
  		<div class="modal-content">
  			<div class="modal-header">
          <h4 class="modal-title">Additional Sample Attributes</h4>
  				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  			</div>
  			<div class="modal-body" id="modal_more_attributes_body">


          <?php
          foreach ($sample_additional_attributes as $key => $value) {
            echo '
            <div class="col-md-6">
              <label>
                <input type="checkbox" class="checkbox_attributes" val="' . $key . '">
                  ' . $value . '
                </input>
              </label>
            </div>';
          }
          ?>

  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-primary"
                  data-dismiss="modal">
            OK
          </button>
  			</div>
  		</div>
  	</div>
  </div>








<script>


$(document).ready(function() {


  $(document).on('click', '#btn_submit', function() {
    var vm      = $(this);
    var genes   = $('#textarea_gene_names').val();
    var samples = $('#textarea_sample_names').val();
    var attr    = [];
    $('.checkbox_attributes')
      .each(function(index, element) {
        if ($(element).is(':checked')) {
          attr.push($(element).attr('val'));
        }
      });
	  
	  $('#busySection').show();
    $.ajax({
      type: 'POST',
      url: 'exe_genes_samples.php?action=get_gene_sample_data',
      data: { genes: genes, samples: samples, attr: attr },
      success: function(response) {
		  $('#busySection').hide();
        vm
          .removeAttr('disabled')
          .children(':first')
          .addClass('fa-upload')
          .removeClass('fa-spin fa-spinner');
        var type = response.type;
        if (type == 'Error') {
          bootbox.alert(response.detail);
        } else {
          setInterval(function() {
            window.location = 'index_r_barchart.php?tid=' + response.time;
          }, 1000);
        }
      }
    });
  });

  // ***************************************************************************
  // Load Gene List & Comparison List
	// ***************************************************************************
  $(document).on('click', '#btn_show_comparison_list', function () {
    var type = $(this).attr('type');
    if (type == 'gene') {
      $.ajax({
    		type: 'POST',
    		url: '<?php echo $BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS'] . 'app_list_ajax_selection.php?category=Gene&input_name=geneList&input_class=geneList'; ?>',
    		success: function (response) {
          $('#modal_select_gene_list_body').html(response);
          $('#modal_select_gene_list').modal();
    		}
    	});
    } else {
      $.ajax({
    		type: 'POST',
    		url: '<?php echo $BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS'] . 'app_list_ajax_selection.php?category=Sample&input_name=sampleList&input_class=sampleList'; ?>',
    		success: function (response) {
          $('#modal_select_sample_list_body').html(response);
          $('#modal_select_sample_list').modal();
    		}
    	});
    }

  });
  $(document).on('click', '#btn_select_gene_list', function () {
    var selected = $('.geneList').val();
		$('.geneList').each(function(index, element) {
			if ($(element).is(':checked')) {
				selected = $(element).val();
			}
		});
    $('#textarea_gene_names').val($('#gene_list_content_' + selected).val());
    $('#modal_select_gene_list').modal('hide');
  });
  $(document).on('click', '.geneList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		var radioID			= 'geneList_' + currentListID;
		$('#' + radioID).prop('checked', true);
		var content = $('#gene_list_content_' + currentListID).val();
		$('#textarea_gene_names').val(content);
    $('#modal_select_gene_list').modal('hide');
	});
  $(document).on('change', '.geneList', function(){
		var currentListID = $(this).val();
		var content = $('#gene_list_content_' + currentListID).val();
		$('#textarea_gene_names').val(content);
    $('#modal_select_gene_list').modal('hide');
	});
  $(document).on('click', '#btn_select_sample_list', function () {
    var selected = $('.sampleList').val();
		$('.sampleList').each(function(index, element) {
			if ($(element).is(':checked')) {
				selected = $(element).val();
			}
		});
    $('#textarea_sample_names').val($('#sample_list_content_' + selected).val());
    $('#modal_select_sample_list').modal('hide');
  });
  $(document).on('click', '.sampleList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		var radioID			= 'sampleList_' + currentListID;
		$('#' + radioID).prop('checked', true);
		var content = $('#sample_list_content_' + currentListID).val();
		$('#textarea_sample_names').val(content);
    $('#modal_select_sample_list').modal('hide');
	});
  $(document).on('change', '.sampleList', function(){
		var currentListID = $(this).val();
		var content = $('#sample_list_content_' + currentListID).val();
		$('#textarea_sample_names').val(content);
    $('#modal_select_sample_list').modal('hide');
	});


});

</script>




</body>
</html>
