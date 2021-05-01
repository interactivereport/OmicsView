<?php
include_once('config.php');

if (isset($_GET['id']) && trim($_GET['id']) != '') {
	$COMPARISON_ID_DEFAULT = true;
	$ROWID = intval($_GET['id']);
	$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $ROWID;
	$data = $DB -> get_row($sql);
	$COMPARISON_ID = $data['ComparisonID'];
} else {
	$COMPARISON_ID_DEFAULT = false;
}

// Show highlighted genes
if (isset($_GET['type']) && trim($_GET['type']) == 'custom') {
  $CUSTOM = true;
  $SRC = $_GET['src'];
} else {
  $CUSTOM = false;
  $SRC = '';
}

// If customized genes, get the gene list
if ($SRC == 'gsea') {
  $file = $BXAF_CONFIG['USER_FILES_PAGE'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/selected_genes.txt';
  $content = file_get_contents($file);
  $CUSTOM_GENES = explode('|', $content);
}
if ($SRC == 'go') {
  $file = $BXAF_CONFIG['USER_FILES_GO'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/selected_genes.txt';
  $content = file_get_contents($file);
  $CUSTOM_GENES = explode(',', $content);
}


$GENE_NAMES = "";
foreach ($_SESSION['SAVED_GENE'] as $gene) {
	$sql = "SELECT `GeneName` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
					WHERE `GeneIndex`=$gene";
	$name = $DB -> get_one($sql);
	// $GENE_NAMES .= $name . "\n";
	$GENE_NAMES .= $name . '\n';
}


unset($_SESSION['volcano_files']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Volcano Plot</title>
<link rel="icon" href="../../img/logo_address_bar.png">

<link href="../library/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../library/TableFilter/dist/tablefilter/style/tablefilter.css" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../library/tether/dist/css/tether.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">


<script type="text/javascript" src="../library/jquery.min.3.1.0.js"></script>
<script type="text/javascript" src="../library/tether/dist/js/tether.min.js"></script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript" src="../library/bootstrap/dist/js/bootstrap.min.js.php"></script>
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/bootbox.min.js.php"></script>
<script type="text/javascript" language="javascript" src="../library/TableFilter/dist/tablefilter/tablefilter.js"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>
<script type="text/javascript" src="../library/config.js"></script>


<!-- DataTables -->
<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>

<style>
.volcano
{
    height: 640px;
    margin: 0px;
}
</style>

</head>
<body>

<div id="wrapper">

	<?php include_once("../component_header.php"); ?>

	<div id="page-wrapper">

		<div class="container-fluid">
			<h1 class="page-header">
				Volcano Chart &nbsp;
				<a href="example.php" id="volcano_btn_load_demo" class="font-sanspro-300 font_normal">
					<i class="fa fa-angle-double-right"></i> Load Example
				</a>
			</h1>
			<div class="row m-x-0 p-l-1">
				<div class="col-md-12">

					<input name="chart_number" value="1" id="chart_number" hidden>

					<!-- Div For All Chart Settings-->

					<div class="row m-x-0" id="chart_setting_all_container">

						<!-- Div For Single Chart Settings-->

						<div class="chart_setting_single_container">



							<!-- <div class="row">
								<div class="col-md-2 text-md-right gray">
									Comparison ID
								</div>
								<div class="col-md-10">


									 <div class="input-group" style="max-width:30em;">
										<input name="comparison_id[]" class="form-control input_file input_comparison_id"
											<?php
											if ($COMPARISON_ID_DEFAULT) {
												echo ' value="' . $COMPARISON_ID . '"';
											}
											?>
										required>
										<span class="input-group-btn">
											<button class="btn_search_comparison btn btn-default green" type="button" index="0"><i class="fa fa-search"></i> Search</button>
										</span>
									</div>


									<span class="gray">Please enter the comparison id, e.g., GSE44720.GPL10558.test16</span>
								</div>
							</div>

							<div class="row m-t-1">
								<div class="col-md-2 text-md-right gray">
									Y-axis Statistics:
								</div>
								<div class="col-md-10">
									<label>
										<input type="radio" name="volcano_y_statistics_0" value="P-value">
										P-value
									</label>
									&nbsp;&nbsp;
									<label>
										<input type="radio" name="volcano_y_statistics_0" value="FDR" checked>
										FDR
									</label>
								</div>
							</div>

							<div class="row m-t-sm">
								<div class="col-md-2 text-md-right p-t-sm gray">
									Chart Name
								</div>
								<div class="col-md-10">
									<input class="form-control" name="chart_name[]" value="Volcano Chart" style="width:20em;" required>
								</div>
							</div>

							<div class="row m-t-sm">
								<div class="col-md-2 text-md-right p-t-sm gray">
									Fold Change Cutoff:
								</div>
								<div class="col-md-10">
									<select class="form-control volcano_fc_cutoff custom-select float-left m-r-1" name="volcano_fc_cutoff[]" style="width:8.6em;">
										<option value="2">2</option>
										<option value="4">4</option>
										<option value="8">8</option>
										<option value="enter_value">Enter Value</option>
									</select>
									<input class="form-control float-left hidden" name="volcano_fc_custom_cutoff[]" placeholder="Custom Cutoff" style="width:10.3em;">
								</div>
							</div>

							<div class="row m-t-sm">
								<div class="col-md-2 text-md-right p-t-sm gray">
									Statistic Cutoff:
								</div>
								<div class="col-md-10">
									<select class="form-control volcano_statistic_cutoff custom-select float-left m-r-1" name="volcano_statistic_cutoff[]" style="width:8.6em;">
										<option value="0.05">0.05</option>
										<option value="0.01">0.01</option>
										<option value="0.001">0.001</option>
										<option value="enter_value">Enter Value</option>
									</select>
									<input class="form-control float-left hidden" name="volcano_statistic_custom_cutoff[]" placeholder="Custom Cutoff" style="width:10.3em;">
								</div>
							</div>
 -->


						</div>

					</div>


					<div class="row m-t-0">
						<div class="col-md-2 text-md-right gray">
							Show Gene Symbol：
						</div>
						<div class="col-md-10">
							<label>
								<input type="radio" class="volcano_show_gene" name="volcano_show_gene" id="volcano_show_gene_auto" value="auto"<?php
                  echo (!$CUSTOM) ? ' checked' : '';
                ?>>
								Auto (based on cutoff)
							</label>
							&nbsp;&nbsp;
							<label>
								<input type="radio" class="volcano_show_gene" name="volcano_show_gene" id="volcano_show_gene_customize" value="customize"<?php
                  echo ($CUSTOM) ? ' checked' : '';
                ?>>
								Customize
							</label>
						</div>
					</div>


					<div class="row m-t-1" id="volcano_show_gene_names_div"<?php
            echo (!$CUSTOM) ? ' style="display:none;"' : '';
          ?>>
						<div class="col-md-2 text-md-right gray">
							Enter Gene Names：<br />
							<a href="javascript:void(0);" id="btn_load_saved_genes">
								<i class="fa fa-angle-double-right"></i> Load saved genes
							</a>
						</div>
						<div class="col-md-10">
							<textarea class="form-control" name="volcano_show_gene_names" id="volcano_show_gene_names" style="height:10rem;"><?php
                  if ($CUSTOM) {
                    echo trim(implode($CUSTOM_GENES, "\n"));
                  }
                ?>
							</textarea>
						</div>
					</div>



					<div class="row m-t-1">
						<div class="col-md-2">&nbsp;</div>
						<div class="col-md-10">
							<p class="font-sanspro-300 m-b-1">
								<a href="javascript:void(0);" id="btn_add_chart"><i class="fa fa-angle-double-right"></i> Add A New Chart</a>
							</p>
							<button id="btn_submit" class="btn btn-primary"><i class="fa fa-upload"></i> Submit</button>
						</div>
					</div>



				</div>

				<div class="col-md-4">
				</div>
			</div>

			<div class="row m-x-0 m-t-2">
				<div id="debug"></div>
			</div>


			<div class="row m-x-0 m-t-2" id="table_div">
			</div>


		</div>

	</div>

</div>






<!-- Modal -->
<div class="modal fade" id="modal_select_comparison" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Search Comparison</h4>
			</div>
			<div class="modal-body">
				<?php
					$sql = "SELECT `ComparisonID`,
								`Case_CellType`,
								`Case_DiseaseState`,
								`ComparisonContrast`
							FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`";
					$data = $DB -> get_all($sql);
					echo '
					<table class="table table-bordered datatable" id="table_select_comparison" style="width:100%; font-size:14px;">
						<thead>
						<tr>
							<th>Comparison ID</th>
							<th>DiseaseState</th>
							<th>ComparisonContrast</th>
							<th>CellType</th>
						</tr>
						</thead>
						<tbody>';

						foreach ($data as $comparison) {
							echo '
							<tr>
								<td>' . $comparison['ComparisonID'] . ' &nbsp;
									<a href="javascript:void(0);" class="btn_select_search_comparison" content="' . $comparison['ComparisonID'] . '"><i class="fa fa-angle-double-right"></i> Select</a>
								</td>
								<td>' . $comparison['Case_DiseaseState'] . '</td>
								<td>' . $comparison['ComparisonContrast'] . '</td>
								<td>' . $comparison['Case_CellType'] . '</td>
							</tr>';
						}
					echo '
						</tbody>
					</table>
					';
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>








<script>


// Generate File Upload Form for Each Comparison
function comparison_form(time) {
  var content = '';
  content += '<form id="form_upload_file_' + time + '">';
  content += '<div class="row m-b-1">';
  content += '  <div class="col-md-2 text-md-right gray">';
  content += '    Upload File:';
  content += '  </div>';
  content += '  <div class="col-md-10" id="csv_container_' + time + '">';
  content += '    <input name="time" value="' + time + '" hidden>';
  content += '    <input type="file" class="csv_file m-t-sm" name="file" rowid="' + time + '"';
  content += '           style="float:left; widht:100px"';
  content += '           onchange="$(this).next().show().addClass(\'animated fadeIn\');">';
  content += '    <button class="btn btn-sm btn-info" style="float:left; widht:100px; display:none;">';
  content += '      <i class="fa fa-upload"></i>';
  content += '    </button>';
  content += '  </div>';
  content += '</div><hr />';
  content += '</form>';
  content += '';
  return content;
}



// Content after uploaded
function content_uploaded(json) {
  var time = json.time;
  var header = json.header;
  var id_type = json.ID_type;
  var header_index = 0;
  var content = '';

  content += '<p>First Column Matched: <strong class="green">' + id_type + '</strong></p>';

  // Chart Name
  content += '<div class="row m-x-0">';
  content += '    <strong class="gray">Chart Name:</strong>';
  content += '    <input class="form-control chart_name" rowid="' + time + '">';
  content += '</div>';

  // X-axis Option
  content += '<div class="row m-t-sm">';
  content += '  <div class="col-md-6">';
  content += '    <strong class="gray">Select X Column:</strong>';
  content += '    <select class="form-control select_x_column" rowid="' + time + '">';
  header.forEach(function(item) {
    if (header_index > 0) {
      content += '  <option value="' + item + '">' + item + '</option>';
    }
    header_index++;
  });
  content += '    </select>';
  content += '  </div>';
  content += '  <div class="col-md-6">';
  content += '    <strong class="gray">X-axis Cutoff:</strong>';
  content += '    <select class="form-control select_x_cutoff" rowid="' + time + '">';
  content += '      <option value="2">2</option>';
  content += '      <option value="4">4</option>';
  content += '      <option value="8">8</option>';
  content += '      <option value="enter_value">Enter Value</option>';
  content += '    </select>';
  content += '    <input class="form-control custom_x_cutoff hidden m-t-sm">';
  content += '  </div>';
  content += '</div>';

  // Y-axis Option
  content += '<div class="row">';
  content += '  <div class="col-md-6">';
  content += '    <strong class="gray">Select Y Column:</strong>';
  content += '    <select class="form-control select_y_column" rowid="' + time + '">';

  header_index = 0;
  header.forEach(function(item) {
    if (header_index > 0) {
      content += '  <option value="' + item + '">' + item + '</option>';
    }
    header_index++;
  });
  content += '    </select>';
  content += '  </div>';
  content += '  <div class="col-md-6">';
  content += '    <strong class="gray">Y-axis Cutoff:</strong>';
  content += '    <select class="form-control select_y_cutoff" rowid="' + time + '">';
  content += '      <option value="0.05">0.05</option>';
  content += '      <option value="0.01">0.01</option>';
  content += '      <option value="0.001">0.001</option>';
  content += '      <option value="enter_value">Enter Value</option>';
  content += '    </select>';
  content += '    <input class="form-control custom_y_cutoff hidden m-t-sm">';
  content += '  </div>';
  content += '</div>';

  return content;
}



// Load New Comparison Form
function add_new_chart(current_length=0) {
  $.ajax({
		type: 'POST',
		url: 'exe_file.php?action=add_new_chart',
    data: {current_length: current_length},
		success: function(response) {

      var time = response.time;

      $('#chart_setting_all_container').append(comparison_form(time));
      $('#chart_number').val(parseInt(current_length) + 1);

      // File Upload
      var options = {
    		url: 'exe_file.php?action=upload_file',
     		type: 'post',
        beforeSubmit: function(formData, jqForm, options) {
          $('#csv_container_' + time).hide();
          $('#csv_container_' + time).parent().append('<div>&nbsp;&nbsp;<i class="fa fa-spin fa-spinner fa-2x"></i></div>');
    			return true;
    		},
        success: function(response2){
          console.log(response2);
          var time = response2.time;
          var type = response2.type;
          if (type == 'Error') {
            bootbox.alert(response2.detail);
            $('#csv_container_' + time).next().remove();
            $('#csv_container_' + time).show().children(':last').hide();
          }
          else {
            var time = response2.time;
            var header = response2.header;
            $('#csv_container_' + time).next(':last').remove();
            $('#csv_container_' + time).show().html(content_uploaded(response2));
          }
    			return true;
    		}
      };
    	$('#form_upload_file_' + time + '').ajaxForm(options);

			return true;
		}
	});
}



var domReady = function(callback) {
    document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};


domReady(function() {

  add_new_chart();

	//$('#table_select_comparison').DataTable();
  $('.datatable').DataTable();


	$('#sidebar_link_volcano').addClass('active');
	$('#sidebar_link_volcano').parent().parent().prev().addClass('active');
	$('#sidebar_link_volcano').parent().parent().css('display', 'block');
	$('#sidebar_link_volcano').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');

	<?php if ($COMPARISON_ID_DEFAULT) {
		echo "setTimeout(function(){ $('#btn_submit').trigger('click'); },1000);";
	}
	?>


	// Load Demo
	document
		.getElementById('volcano_btn_load_demo')
		.addEventListener('click', function() {

			document.getElementById("debug").innerHTML = 'Loading';
			$.ajax({
				type: 'POST',
				url: 'exe.php?action=volcano_load_demo',
				success: function(responseText) {
					$('#debug').html(responseText);
				}
			});
	});


	// Upload File
	$(document).on('change', '.input_file', function(e) {
		var current = $(this);
		var name = e.target.files[0].name;
		current.parent().parent().next().next().find('input').val(name.substring(0, name.length - 4));
	});





	// Select Comparison
	var index_select = 0;
	$(document).on('click', '.btn_search_comparison', function() {
		index_select = $(this).attr('index');
		$('#modal_select_comparison').modal('show');
	});
	$(document).on('click', '.btn_select_search_comparison', function() {
		var content = $(this).attr('content');
		$('#modal_select_comparison').modal('hide');
		$('.input_comparison_id').each(function(index, element) {
			if (index == index_select) {
				$(element).val(content);
			}
		});
	});



	// Change Select
	$(document).on('change', '.select_x_cutoff, .select_y_cutoff', function() {
		var current = $(this);
		if (current.val() == 'enter_value') {
			current.next().removeClass('hidden');
		} else {
			current.next().addClass('hidden');
		}
	});



	// Change Gene Symbol Option
	$(document).on('change', '.volcano_show_gene', function() {
		var current = $(this);
		if ($('#volcano_show_gene_auto').is(':checked')) {
			$('#volcano_show_gene_names_div').slideUp();
		} else if ($('#volcano_show_gene_customize').is(':checked')) {
			$('#volcano_show_gene_names_div').slideDown();
		}
	});


  // Change # Genes Displayed
  $(document).on('change', '#volcano_gene_number_all', function() {
		if ($(this).is(":checked")) {
      bootbox.alert('<h4 class="red">Warning:</h4> It may take over 1 minute to display all genes.');
    }
	});



	// Add New Chart
	$(document).on('click', '#btn_add_chart', function() {
		var current = $(this);
		var current_length = $('.csv_file').length;
    add_new_chart(current_length);
	});

	// Load Saved Genes
	$(document).on('click', '#btn_load_saved_genes', function() {
		var saved_genes = '<?php echo $GENE_NAMES; ?>';
		$('#volcano_show_gene_names').val(saved_genes);
	});





  // Generate Chart
  $(document).on('click', '#btn_submit', function() {
    var current = $(this);
    $(this).attr('disabled', '')
           .children(':first')
           .removeClass('fa-upload')
           .addClass('fa-spin fa-spinner');

    // Initialize Parameters
    var time_list             = [];
    var name_list             = [];
    var x_col_list            = [];
    var y_col_list            = [];
    var x_cutoff_list         = [];
    var y_cutoff_list         = [];
    var custom_x_cutoff_list  = [];
    var custom_y_cutoff_list  = [];
    var volcano_show_gene     = 'false';
    var gene_names            = $('#volcano_show_gene_names').val();


    $('.chart_name').each(function(index, element) {
      name_list.push($(element).val());
    });
    $('.select_x_column').each(function(index, element) {
      time_list.push($(element).attr('rowid'));
      x_col_list.push($(element).val());
    });
    $('.select_x_cutoff').each(function(index, element) {
      x_cutoff_list.push($(element).val());
    });
    $('.custom_x_cutoff').each(function(index, element) {
      custom_x_cutoff_list.push($(element).val());
    });
    $('.select_y_column').each(function(index, element) {
      y_col_list.push($(element).val());
    });
    $('.select_y_cutoff').each(function(index, element) {
      y_cutoff_list.push($(element).val());
    });
    $('.custom_y_cutoff').each(function(index, element) {
      custom_y_cutoff_list.push($(element).val());
    });

    if ($('#volcano_show_gene_customize').is(':checked')) {
      volcano_show_gene = 'true';
    }


    // AJAX
    $.ajax({
  		type: 'POST',
  		url: 'exe_file.php?action=volcano_generate_chart',
      data: {
        time_list:     time_list,
        name_list:     name_list,
        x_col_list:    x_col_list,
        y_col_list:    y_col_list,
        x_cutoff_list: x_cutoff_list,
        y_cutoff_list: y_cutoff_list,
        custom_x_cutoff_list: custom_x_cutoff_list,
        custom_y_cutoff_list: custom_y_cutoff_list,
        volcano_show_gene: volcano_show_gene,
        gene_names: gene_names
      },
  		success: function(response) {
        current.removeAttr('disabled')
               .children(':first')
               .addClass('fa-upload')
               .removeClass('fa-spin fa-spinner');


        // $('#debug').html(response);
  			console.log(response);

        if (response.type == 'Error') {
          bootbox.alert(response.detail);
        }
        else {



          var time = '';
          var name = '';
          var config;
          // var index_temp = 0;
          for (var i = 0; i < response.detail.length; i++) {

            name = response.detail[i].name;
            time = response.detail[i].time;
            border = response.detail[i].border;
            data = response.detail[i].all_data;

            // Generate Volcano Plot
            $('#debug').append('<div id="volcano_diagram_container_' + i + '" class="volcano col-md-12"></div>');

            config = {

              "chart": {"type":"scatter", "zoomType":"xy"},

              "title": { "text":  name},

              "xAxis": {
        				"title":{
        					"enabled":true,
        					"text": x_col_list[i]
        				},
        				"startOnTick": true,
        				"endOnTick": true,
        				"showLastLabel": true,
        				"gridLineWidth": 1,
        				"min": parseFloat(border.x_min),
        				"max": parseFloat(border.x_max)
        			},

              "yAxis": {
        				"title":{
        					"enabled":true,
        					"text": y_col_list[i]
        				},
        				"startOnTick":true,
        				"endOnTick":true,
        				"showLastLabel":true,
        				"gridLineWidth":1,
        				"min": parseFloat(border.y_min),
        				"max": parseFloat(border.y_max)
        			},

              "plotOptions": {
        				"scatter":{
        					"allowPointSelect": true,
        					"marker": {
                    "radius": 2,
                    "states": {
                      "hover": {"enabled": true,"lineColor": "#333333"}}
                    },
      					  "states": {
                    "hover": {"marker":{"enabled":true}}
                  },
      					  "turboThreshold":50000
        				},

                series: {
        					cursor: 'pointer',
        					point: {
        						events: {
        							click: function (e) {
        								$('#geneList').val($('#geneList').val() + ' ' + this.alt_name);
        								var current_gene = this;
        								console.log(this);
        								bootbox.alert("<h4>Gene " + current_gene.alt_name + "</h4><hr /><a href=\"../../app_gene_expression_rnaseq_single.php?GeneName="+current_gene.alt_name+"\" target=\"_blank\">View gene expression</a><br><a href=\"../bubble/index.php?id="+current_gene.alt_name+"\" target=\"_blank\">View bubble plot</a>");
        							}
        						}
        					}
        				}
              },

              'series': [

                // Selected
                {
        					"name":"selected",
        					"color":"#f79e4d",
        					marker: {
        						radius: 4
        					},
        					dataLabels: {
        						enabled: true,
        						x: 35,
        						y: 5,
        						formatter:function() {
        							return this.point.alt_name;
        						},
        						style:{color:"black"}
        					},
                  'data': []
                },

                // Up-Regulated
                {
        					"name":"up-regulated",
        					"color":"#FF0000",
        					marker: {
        						radius: 4
        					},
        					dataLabels: {
        						enabled: true,
        						x: 35,
        						y: 5,
        						formatter:function() {
        							return this.point.alt_name;
        						},
        						style:{color:"black"}
        					},
                  'data': []
                },

                // Down-Regulated
                {
        					"name":"down-regulated",
        					"color":"#009966",
        					marker: {
        						radius: 4
        					},
        					dataLabels: {
        						enabled: true,
        						x: 35,
        						y: 5,
        						formatter:function() {
        							return this.point.alt_name;
        						},
        						style:{color:"black"}
        					},
                  'data': []
                },

                // Unregulated
                {
        					"name":"unregulated",
        					"color":"#AEB6BF",
        					marker: {
        						radius: 4
        					},
        					dataLabels: {
        						enabled: true,
        						x: 35,
        						y: 5,
        						formatter:function() {
        							return this.point.alt_name;
        						},
        						style:{color:"black"}
        					},
                  'data': []
                },

                // Threshold: x left
                {
        					"name":"downfold threshold",
        					"color":"#000000",
        					"type":"line",
        					"dashStyle":"Dash",
        					"marker":{"enabled":false},
        					"data":[
                    [
                      parseFloat('-' + response.detail[i].x_cutoff),
                      parseFloat(border.y_min)
                    ],
                    [
                      parseFloat('-' + response.detail[i].x_cutoff),
                      parseFloat(2 * border.y_max)
                    ],
                  ]
        				},

                // Threshold: x right
                {
        					"name":"upfold threshold",
        					"color":"#000000",
        					"type":"line",
        					"dashStyle":"Dash",
        					"marker":{"enabled":false},
        					"data":[
                    [
                      parseFloat(response.detail[i].x_cutoff),
                      parseFloat(border.y_min)
                    ],
                    [
                      parseFloat(response.detail[i].x_cutoff),
                      parseFloat(2 * border.y_max)
                    ],
                  ]
        				},

                // Threshold: y
                {
                  "name":"upfold threshold",
                  "color":"#000000",
                  "type":"line",
                  "dashStyle":"Dash",
                  "marker":{"enabled":false},
                  "data":[
                    [
                      parseFloat(border.x_min),
                      parseFloat(response.detail[i].y_cutoff)
                    ],
                    [
                      parseFloat(border.x_max),
                      parseFloat(response.detail[i].y_cutoff)
                    ],
                  ]
                },


              ],


            };

            for (var index = 0; index < data.selected.length; index++) {
              config.series[0].data.push({
                x: parseFloat(data.selected[index].x),
                y: parseFloat(data.selected[index].y),
                name: data.selected[index].name,
                alt_name: data.selected[index].alt_name,
              });
            }
            for (var index = 0; index < data.up_regulated.length; index++) {
              config.series[1].data.push({
                x: parseFloat(data.up_regulated[index].x),
                y: parseFloat(data.up_regulated[index].y),
                name: data.up_regulated[index].name,
                alt_name: data.up_regulated[index].alt_name,
              });
            }
            for (var index = 0; index < data.down_regulated.length; index++) {
              config.series[2].data.push({
                x: parseFloat(data.down_regulated[index].x),
                y: parseFloat(data.down_regulated[index].y),
                name: data.down_regulated[index].name,
                alt_name: data.down_regulated[index].alt_name,
              });
            }
            for (var index = 0; index < data.unregulated.length; index++) {
              config.series[3].data.push({
                x: parseFloat(data.unregulated[index].x),
                y: parseFloat(data.unregulated[index].y),
                name: data.unregulated[index].name,
                alt_name: data.unregulated[index].alt_name,
              });
            }

            console.log(config.series[4]);

            $('#volcano_diagram_container_' + i).highcharts(config);
          }


        }

  			return true;
  		}
  	});
  });

	// // Generate Chart
	// var options = {
	// 	url: 'exe.php?action=volcano_generate_chart',
  // 		type: 'post',
  //       beforeSubmit: function(formData, jqForm, options) {
	// 		if ($('#file').val() == '') {
	// 			message_alert('error', 'Please select a csv file.', '');
	// 			return false;
	// 		}
	// 		$('#btn_submit').children(':first').removeClass('fa-upload').addClass('fa-spin fa-spinner');
	// 		$('#btn_submit').attr('disabled', '');
	// 		return true;
	// 	},
  //       success: function(responseText, statusText){
	// 		$('#btn_submit').children(':first').removeClass('fa-spin fa-spinner').addClass('fa-upload');
	// 		$('#btn_submit').removeAttr('disabled');
	// 		if(responseText.substring(0, 5) == 'Error'){
	// 			bootbox.alert(responseText);
	// 		} else {
	// 			$('#debug').html(responseText);
	// 		}
  //
	// 		return true;
	// 	}
  //   };
	// $('#form_valcano_chart').ajaxForm(options);



});


</script>




</body>
</html>
