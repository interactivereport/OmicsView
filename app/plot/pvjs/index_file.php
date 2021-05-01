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


if (isset($_GET['pathway']) && trim($_GET['pathway']) != '') {
  $DEFAULT_PATHWAY = trim($_GET['pathway']);
}
$DEFAULT_TYPE = $_GET['type'];
if (trim($DEFAULT_TYPE) == 'reactome') {
  $DEFAULT_PATHWAY .= ' (Reactome)';
}


unset($_SESSION['pvjs_files']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Pathway Plot</title>
<link rel="icon" href="../../img/logo_address_bar.png">

<link href="../library/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../library/TableFilter/dist/tablefilter/style/tablefilter.css" rel="stylesheet">
<link href="../library/DataTables/media/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="../library/tether/dist/css/tether.min.css" rel="stylesheet">
<link href="../library/animate.css.php" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">



<script type="text/javascript" src="../library/jquery.min.js.php"></script>
<script src="../library/jquery-migrate.js"></script>
<script type="text/javascript" src="../library/tether/dist/js/tether.min.js"></script>
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/bootstrap/dist/js/bootstrap.min.js.php"></script>
<script type="text/javascript" src="../library/DataTables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../library/lz-string/lz-string.min.js"></script>
<script type="text/javascript" src="../library/bootbox.min.js.php"></script>

<!-- DataTables -->
<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>

<script type="text/javascript" src="../library/config.js"></script>


<style>
#pvjs-widget {
	top:0;
	left:0;
	font-size:12px;
	width:100%;
	height:inherit;
	border:1px solid #CCC;
	border-radius:10px;
}
</style>

</head>
<body>

<div id="wrapper">

	<?php include_once("../component_header.php"); ?>

	<div id="page-wrapper">

		<div class="container-fluid">
			<h1 class="page-header">
				Pathway Visualization &nbsp;
				<a href="example.php" class="font-sanspro-300 font_normal hidden"><i class="fa fa-angle-double-right"></i> Load Example</a>
			</h1>
			<div class="row m-x-0 p-l-1">

  			<!-- <form id="form_pvjs_chart" method="post" enctype="multipart/form-data"> -->

  				<div class="row">
  					<div class="col-md-2 text-md-right gray p-t-sm">
  						Select Pathway
  					</div>
  					<div class="col-md-10">

              <div class="input-group" style="max-width:40em;">
                <input class="form-control" id="input_pathway_disabled" value="<?php
                  foreach($BXAF_CONFIG['PATHWAY_LIST'] as $key => $value) {
                    //if ($DEFAULT_PATHWAY == $value) echo $value;
  									if ( strtoupper(preg_replace("/[^a-zA-Z0-9]+/", "", $DEFAULT_PATHWAY)) ==  strtoupper(preg_replace("/[^a-zA-Z0-9]+/", "", $value))) {
                      echo $value;
                    }
                  }
                ?>" disabled>
                <span class="input-group-btn">
                  <button class="btn btn-default" id="btn_select_pathway_show_modal" type="button">Select</button>
                </span>
              </div>
              <input class="form-control hidden" name="pathway" value="<?php
                foreach($BXAF_CONFIG['PATHWAY_LIST'] as $key => $value) {
                  //if ($DEFAULT_PATHWAY == $value) echo $key;
                  if ( strtoupper(preg_replace("/[^a-zA-Z0-9]+/", "", $DEFAULT_PATHWAY)) ==  strtoupper(preg_replace("/[^a-zA-Z0-9]+/", "", $value))) {
                    echo $key;
                  }
                }
              ?>" id="input_pathway">

  						<p class="font-sanspro-300 m-b-1 m-t-1">
  							<a href="files/pathway/<?php echo $CONFIG_PROFILE['PVJS']['pathway_dir']; ?>/<?php echo $first_pathway; ?>" class="hidden" id="btn_download_pathway" download>
  								<i class="fa fa-download"></i> Download Pathway File: <span class="filename"><?php echo $first_pathway; ?></span>
  							</a>
  						</p>
  					</div>
  				</div>


  				<div class="row m-b-1 hidden">
  					<div class="col-md-2 text-md-right gray p-t-sm">
  						SVG Legend:
  					</div>
  					<div class="col-md-10">
  						<label>
  							<input type="checkbox" name="show_svg_legend">
  							Include Legend  in SVG
  						</label>
  						<input class="form-control" name="svg_legend_font_size" type="number" min="5" max="20" placeholder="Legend Font Size" style="width:160px; display:inline-block;margin-left:20px;">
  					</div>
  				</div>




  				<!-- All Comparisons -->
  				<div id="all_comparisons_container">
  				</div>



  				<!-- Submit Button Div -->
  				<div class="row">
  					<div class="col-md-2">&nbsp;</div>
  					<div class="col-md-10">
  						<p class="font-sanspro-300 m-b-1">
  							<a href="javascript:void(0);" id="btn_add_comparison">
  								<i class="fa fa-angle-double-right"></i> Add Comparison
  							</a>
  						</p>
  						<button id="btn_submit" class="btn btn-primary">
  							<i class="fa fa-upload"></i> Submit
  						</button>
  					</div>
  				</div>

  			<!-- </form> -->
			</div>

			<!--<button id="btn_save_svg">Save as SVG File</button>-->

			<div class="row m-x-0 m-t-2">
				<div id="debug"></div>
				<div id="debug2"></div>
			</div>


		</div>

	</div>

</div>






<!-- Modal to Select Comparison -->
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
								`Case_SampleSource`
							FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`";
					$data = $DB -> get_all($sql);
					echo '
					<table class="table table-bordered" id="table_select_comparison" style="width:100%; font-size:14px;">
						<thead>
						<tr>
							<th>Comparison ID</th>
							<th>DiseaseState</th>
							<th>SampleSource</th>
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
								<td>' . $comparison['Case_SampleSource'] . '</td>
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



<!-- Modal to Select Pathway -->
<div class="modal fade" id="modal_select_pathway" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Select Pathway</h4>
			</div>
			<div class="modal-body">
				<?php
					echo '
					<table class="table table-bordered" id="table_select_pathway" style="width:100%; font-size:14px;">
						<thead>
						<tr>
							<th>Pathway Name</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>';

            $index = 0;
            // foreach($BXAF_CONFIG['PATHWAY_LIST'] as $key => $value) {
            //   echo '<option value="' . $key . '"';
            //   if ($DEFAULT_PATHWAY == $value) {
            //     echo ' selected';
            //   }
            //   echo '>' . $value . '</option>';
            //   if ($index == 0) {
            //     $first_pathway = $key;
            //   }
            //   $index++;
            // }


						foreach ($BXAF_CONFIG['PATHWAY_LIST'] as $key => $value) {
              if ($index == 0) {
                $first_pathway = $key;
              }
							echo '
							<tr>
								<td>' . $value . ' &nbsp;
								</td>
								<td><a href="javascript:void(0);" class="btn_select_search_pathway" content="' . $key . '"';
              echo ' displayed_name="' . $value . '"><i class="fa fa-angle-double-right"></i> Select</a></td>
							</tr>';
              $index++;
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
  content += '</div>';
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

  content += '<div class="row">';
  content += '  <div class="col-md-6">';
  content += '    <strong class="gray">Select Column:</strong>';
  content += '    <select class="form-control select_header_column" rowid="' + time + '">';
  header.forEach(function(item) {
    if (header_index > 0) {
      content += '  <option value="' + item + '">' + item + '</option>';
    }
    header_index++;
  });
  content += '    </select>';
  content += '  </div>';
  content += '  <div class="col-md-6">';
  content += '    <strong class="gray">Visualization Option:</strong>';
  content += '    <select class="form-control select_visualization" rowid="' + time + '">';
  content += '      <option value="0">Gradient Blue-White-Red (-1,0,1)</option>';
  content += '      <option value="1">Gradient Blue-White-Red (-2,0,2)</option>';
  content += '      <option value="2">Dark Green (<0.01), Green (0.01-0.05), White (>0.05)</option>';
  content += '      <option value="3">Green (<=0.01), White (>0.01)</option>';
  content += '      <option value="4">Green (<=0.05), White (>0.05)</option>';
  content += '    </select>';
  content += '  </div>';
  content += '</div>';

  return content;
}


// Load New Comparison Form
function load_new_comparison() {
  $.ajax({
		type: 'POST',
		url: 'exe_file.php?action=add_comparison',
		success: function(response) {
			// $('#all_comparisons_container').append(responseText);

      var time = response.time;
      $('#all_comparisons_container').append(comparison_form(time));

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

          var type = response2.type;
          if (type == 'Error') {
            bootbox.alert(response2.detail);
            $('#csv_container_' + time).next(':last').remove();
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

/**
 * 1.  Auto Load Comparison Form
 * 2.  Add Comparison
 * 3.  Select Pathway
 * 4.  Select Comparison
 * 5.  Select Data Column
 * 6.  Enable Second Visualization Column
 * 7.  Change Select Visualization & Custom Visualization
 * 8.  Save SVG
 * 9.  View SVG in New Window
 * 10. Generate Chart
 */



var domReady = function(callback) {
    document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};


domReady(function() {


  // $('#fileInput').change(function () {
  //   alert(this.files[0]);
  //   sendFile(this.files[0]);
  // });
  //
  // function sendFile(file) {
  //   $.ajax({
  //     type: 'post',
  //     url: 'exe_file.php?action=upload_file',
  //     data: file,
  //     success: function (response) {
  //       alert(file.type);
  //       // do something
  //     },
  //     xhrFields: {
  //       // add listener to XMLHTTPRequest object directly for progress (jquery doesn't have this yet)
  //       onprogress: function (progress) {
  //         // calculate upload progress
  //         var percentage = Math.floor((progress.total / progress.totalSize) * 100);
  //         // log upload progress to console
  //         console.log('progress', percentage);
  //         if (percentage === 100) {
  //           console.log('DONE!');
  //         }
  //       }
  //     },
  //     processData: false,
  //     contentType: file.type
  //     // contentType: false
  //   });
  // }



	// LocalStorage Test Code
	// var string = ['test01', 'test02'];
	// alert(serialize(string).length);
	// var compressed = LZString.compress(serialize(string));
	// alert(compressed.length);

	$('#table_select_comparison').DataTable();
  $('#table_select_pathway').DataTable();
	$('#table_select_comparison th').append('<span style="float:right;margin-top:3px;"><i class="fa fa-sort gray"></i></span>').css('padding-right', '10px');

	$('#sidebar_link_pvjs').addClass('active');
	$('#sidebar_link_pvjs').parent().parent().prev().addClass('active');
	$('#sidebar_link_pvjs').parent().parent().css('display', 'block');
	$('#sidebar_link_pvjs').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');


	if ($('#input_pathway_disabled').val() == '') {
		<?php if (isset($_GET['pathway']) && $_GET['pathway'] != '') { ?>
		alert('The pathway is not found, try selecting a similar pathway. (The likely cause is that some reactome pathways were not converted to wikipathway GPMK format.)');
		<?php } ?>
	}

  // $(document).on('change', '.csv_file', function() {
  // });

	// Auto Load Comparison Form
	load_new_comparison();


  $(document).on('click', '#btn_add_comparison', function() {
    load_new_comparison();
  });





  // Show Select Pathway Modal
  $(document).on('click', '#btn_select_pathway_show_modal', function() {
		$('#modal_select_pathway').modal('show');
	});


  // Select Pathway
  $(document).on('click', '.btn_select_search_pathway', function() {
		var content = $(this).attr('content');
    var displayed_name = $(this).attr('displayed_name');
    $('#input_pathway_disabled').val(displayed_name);
    $('#input_pathway').val(content);
    $('#modal_select_pathway').modal('hide');
	});


	// Add Comparison
	// $(document).on('click', '#btn_add_comparison', function() {
	// 	var current = $(this);
	// 	var length = $('.input_comparison_id').length;
	// 	current.children(':first').removeClass('fa-angle-double-right').addClass('fa-spin fa-spinner');
	// 	$.ajax({
	// 		type: 'POST',
	// 		url: 'exe.php?action=add_comparison',
	// 		data: {length: length},
	// 		success: function(responseText) {
	// 			$('#all_comparisons_container').append(responseText);
	// 			current.children(':first').removeClass('fa-spin fa-spinner').addClass('fa-angle-double-right');
	// 			return true;
	// 		}
	// 	});
	// });



	// Select Pathway
	$(document).on('click', '.btn_select_search_pathway', function() {
		var displayname = $('#input_pathway_disabled').val();
		var filename = $('#input_pathway').val();
		var folder = '<?php echo $CONFIG_PROFILE['PVJS']['pathway_dir']; ?>';
		if (displayname.substring(displayname.length - 10) == '(Reactome)') {
			folder = '<?php echo $CONFIG_PROFILE['PVJS']['pathway_dir']; ?>_reactome';
		}
		$('#btn_download_pathway')
			.find('.filename')
			.remove();
		$('#btn_download_pathway')
			.removeClass('hidden')
			.attr('href', 'files/pathway/' + folder + '/' + filename)
			.append('<span class="filename">' + filename + '</span>');
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



	// Select Data Column
	$(document).on('change', '.select_column', function() {
		var current = $(this);
		var column = $(this).val();
		var select_visualization = $(this).parent().parent().next().find('select.select_visualization');
		if (column == 'logFC') {
			select_visualization.html('<option value="0">Gradient Blue-White-Red (-1,0,1)</option><option value="1">Gradient Blue-White-Red (-2,0,2)</option><option value="2">Gradient Blue-White-Red (-3,0,3)</option><option value="custom">&#9733; Customize Highlight Color</option>');
		}
		else {
			select_visualization.html('<option value="0">Dark Green (<0.01), Green (0.01-0.05), White (>0.05)</option><option value="1">Green (<=0.01), White (>0.01)</option><option value="2">Green (<=0.05), White (>0.05)</option><option value="custom">&#9733; Customize Highlight Color</option>');
		}
		current.parent().parent().next().find('.custom_parameter_div').slideUp(200);

	});




	// Enable Second Visualization Column
	$(document).on('click', '.btn_second_column', function() {
		var current = $(this);
		$(this).parent().parent().prev().slideDown(200);
		$(this).parent().parent().prev().prev().slideDown(200);
		current.parent().parent().remove();
	});



	// Change Select Visualization & Custom Visualization
	$(document).on('change', '.select_visualization', function() {
		var current = $(this);
		if (current.val() != 'custom') {
			current.next().slideUp(200);
		} else {
			var column = current.parent().parent().prev().find('select').val();
			if (column == 'logFC') {
				current.next().find('.custom_parameter_cutoff').attr('placeholder', 'Left, Middle, Right');
				current.next().find('.custom_parameter_select_color_style').html('<option value="0">Blue - White - Red</option><option value="1">Green - White - Red</option><option value="2">Yellow - White - Blue</option><option value="3">Yellow - Orange - Red</option>');
			} else {
				current.next().find('.custom_parameter_cutoff').attr('placeholder', 'Enter cutoff value');
				current.next().find('.custom_parameter_select_color_style').html('<option value="0">Green</option><option value="1">Dark Green</option><option value="2">Blue</option><option value="3">Red</option>');
			}
			current.next().slideDown(200);
		}
	});



	// Save SVG
	$(document).on('click', '#btn_save_svg', function() {
		var svgText = $('.diagram-container').html();

		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", "download_svg.php");
		form.setAttribute("accept-charset", "UTF-8");

		var hiddenSVGField = document.createElement("input");
		hiddenSVGField.setAttribute("type", "hidden");
		hiddenSVGField.setAttribute("name", "svgText");
		hiddenSVGField.setAttribute("value", svgText);

		form.appendChild(hiddenSVGField);
		document.body.appendChild(form);
		form.submit();
	});



	// View SVG in New Window
	$(document).on('click', '#btn_view_svg_new_window', function() {
		var svgText = $('.diagram-container').html();
		$.ajax({
			type: 'POST',
			url: 'exe.php?action=view_svg_new_window',
			data: {content: svgText},
			success: function(responseText) {
				window.location = 'single_svg.php';
				return true;
			}
		});

	});



	// Generate Chart

  $(document).on('click', '#btn_submit', function() {
    var current = $(this);
    $(this).attr('disabled', '')
           .children(':first')
           .removeClass('fa-upload')
           .addClass('fa-spin fa-spinner');

    // Initialize Parameters
    var pathway              = $('#input_pathway').val();
    var time_list            = [];
    var column_list          = [];
    var visualization_list   = [];

    $('.select_header_column').each(function(index, element) {
      time_list.push($(element).attr('rowid'));
      column_list.push($(element).val());
    });
    $('.select_visualization').each(function(index, element) {
      visualization_list.push($(element).val());
    });

    // AJAX
    $.ajax({
  		type: 'POST',
  		url: 'exe_file.php?action=pvjs_generate_chart',
      data: {pathway:pathway, time_list:time_list, column_list:column_list, visualization_list:visualization_list},
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
          $('#debug').html(response.info);
          localStorage.setItem('pathway', JSON.stringify(response.comparison_info));


          var annotation_text = 'Header';
          // Click on gene box
      		$(document).on('click', 'body', function() {
      			var annotation_text_new = $('.annotation-header-text').text().trim();
      			var description = $('.annotation-description').html();
      			if (annotation_text != annotation_text_new) {
      				$('.annotation-header').find('.pvjs_added').remove();
      				if (description == 'GeneProduct' || description == 'Protein') {
      					$('<p class="pvjs_added"><i class="fa fa-spin fa-spinner"></i> Loading comparison info...</p>').insertAfter('.annotation-header-text');
                var allGeneInfo = JSON.parse(localStorage.getItem('pathway'));
                var geneInfo = allGeneInfo[annotation_text_new][0];

                var content = '<table class="table table-bordered" style="font-size:12px;">';

                for (var property in geneInfo) {
                  if (geneInfo.hasOwnProperty(property)) {
                    content += '<tr><td>' + property + '</td><td>' + geneInfo[property] + '</td></tr>';
                  }
                }
                // content += '<tr><td>P-value</td><td>' + geneInfo['p-value'] + '</td></tr>';
                // content += '<tr><td>Name</td><td>' + geneInfo['name'] + '</td></tr>';
                // content += '<tr><td>LogFC</td><td>' + geneInfo['logFC'] + '</td></tr>';
                // content += '</table>';
                // alert(geneInfo);
                // var content = geneInfo["OD READING"];
                $('.pvjs_added').html(content);
      					// $.ajax({
      					// 	type: 'POST',
      					// 	url: 'exe.php?action=popup_load_comparison_info',
      					// 	data: {gene_name: annotation_text_new},
      					// 	success: function(response) {
      					// 		$('.pvjs_added').html(response);
      					// 		$('.annotation').css('width', '400px');
      					// 	}
      					// });
      				}
      			}
      			annotation_text = annotation_text_new;
      		});

        }
  			return true;
  		}
  	});
  });




	// var options = {
	// 	url: 'exe.php?action=pvjs_generate_chart',
 // 		type: 'post',
  //   beforeSubmit: function(formData, jqForm, options) {
	// 		$('#btn_submit').children(':first').removeClass('fa-upload').addClass('fa-spin fa-spinner');
	// 		$('#btn_submit').attr('disabled', '');
	// 		return true;
	// 	},
  //   success: function(responseText, statusText){
	// 		$('#btn_submit').children(':first').removeClass('fa-spin fa-spinner').addClass('fa-upload');
	// 		$('#btn_submit').removeAttr('disabled');
	// 		$('#debug').html(responseText.info);
  //
  //     // console.log(responseText.comparison_info);
  //     localStorage.setItem('pathway', JSON.stringify(responseText.comparison_info));
  //
  //     var annotation_text = 'Header';
  //     // Click on gene box
  // 		$(document).on('click', 'body', function() {
  // 			var annotation_text_new = $('.annotation-header-text').text().trim();
  // 			var description = $('.annotation-description').html();
  // 			if (annotation_text != annotation_text_new) {
  // 				$('.annotation-header').find('.pvjs_added').remove();
  // 				if (description == 'GeneProduct' || description == 'Protein') {
  // 					$('<p class="pvjs_added"><i class="fa fa-spin fa-spinner"></i> Loading comparison info...</p>').insertAfter('.annotation-header-text');
  //           var allGeneInfo = JSON.parse(localStorage.getItem('pathway'));
  //           var geneInfo = allGeneInfo[annotation_text_new][0];
  //
  //           var content = '<table class="table table-bordered" style="font-size:12px;"><tr><td>FDR</td><td>' + geneInfo['FDR'] + '</td></tr>';
  //           content += '<tr><td>P-value</td><td>' + geneInfo['p-value'] + '</td></tr>';
  //           content += '<tr><td>Name</td><td>' + geneInfo['name'] + '</td></tr>';
  //           content += '<tr><td>LogFC</td><td>' + geneInfo['logFC'] + '</td></tr>';
  //           content += '</table>';
  //           // content += 'LogFC: ' + geneInfo['logFC'] + '</span>';
  //           $('.pvjs_added').html(content);
  // 					// $.ajax({
  // 					// 	type: 'POST',
  // 					// 	url: 'exe.php?action=popup_load_comparison_info',
  // 					// 	data: {gene_name: annotation_text_new},
  // 					// 	success: function(response) {
  // 					// 		$('.pvjs_added').html(response);
  // 					// 		$('.annotation').css('width', '400px');
  // 					// 	}
  // 					// });
  // 				}
  // 			}
  // 			annotation_text = annotation_text_new;
  // 		});
  //
	// 		return true;
	// 	}
  // };
	// $('#form_pvjs_chart').ajaxForm(options);


});


</script>




</body>
</html>
