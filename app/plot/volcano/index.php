<?php
include_once('config.php');
include_once('../profile/config.php');

$PAGE['Category'] = 'Comparison Plotting Tools';
$PAGE['Barcode']  = 'Volcano Plot';

//------------------------------------------------------------------------------------
// Default Comparison
if (isset($_GET['id']) && trim($_GET['id']) != '') {
	$COMPARISON_ID_DEFAULT = true;
	$ROWID = intval($_GET['id']);
	$data = get_multiple_record('Comparison', $ROWID, 'GetRow');
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
if ($SRC != ''){
	if ($SRC == 'gsea') {
	  $file = $BXAF_CONFIG['USER_FILES_PAGE'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/selected_genes.txt';
	  $content = file_get_contents($file);
	  $CUSTOM_GENES = explode('|', $content);
	} elseif ($SRC == 'go') {
	  $file = $BXAF_CONFIG['USER_FILES_GO'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/selected_genes.txt';
	  $content = file_get_contents($file);
	  $CUSTOM_GENES = explode(',', $content);
	}
} else {
	
	if (($_GET['table'] == 'PAGE_List') && ($_GET['geneset'] != '')){
		$CUSTOM_GENES = get_gene_symbols_from_comparison_PAGE($_GET['geneset']);
		if (general_array_size($CUSTOM_GENES) > 0){
			$CUSTOM = true;	
		} else {
			unset($CUSTOM_GENES);
		}
		
	} elseif (($_GET['direction'] != '') && ($_GET['table'] != '') && ($_GET['geneset'] != '')){
		$CUSTOM_GENES = get_gene_symbols_from_comparison_GO($_GET['id'], $_GET['direction'], $_GET['table'], $_GET['geneset']);
		if (general_array_size($CUSTOM_GENES) > 0){
			$CUSTOM = true;	
		} else {
			unset($CUSTOM_GENES);
		}
	}
}



$GENE_NAMES = "";
foreach ($_SESSION['SAVED_GENE'] as $gene) {
	$sql = "SELECT `GeneName` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
					WHERE `GeneIndex`=$gene";
	$name = $DB -> get_one($sql);
	// $GENE_NAMES .= $name . "\n";
	$GENE_NAMES .= $name . '\n';
}

// If from HEATMAP
if ($SRC == '' && isset($_SESSION['CUSTOM_GENE']) && count($_SESSION['CUSTOM_GENE']) >= 1) {
  $CUSTOM_GENES = $_SESSION['CUSTOM_GENE'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">
<script type="text/javascript" src="js/highcharts-5.0.0.js"></script>
<script type="text/javascript" src="js/exporting-5.0.0.js"></script>
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>

<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>

<style>
.nowrap {
	white-space: nowrap;	
}
.volcano
{
    height: 640px;
    margin: 0px;
}
</style>
<script>
$(document).ready(function() {

	$('.sidebar-nav').on('click', '.category', function() {
		$(this).next().children('.children').toggle();
  	});

});
</script>

</head>
<body>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
	<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

      <div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">


        <div class="container-fluid px-0 pt-3">

    			<h1 class="page-header">
    				Volcano Plot 
    				<?php
    				if ($COMPARISON_ID_DEFAULT) {
    					echo '
    					<a href="../search_comparison/comparison_gene_table.php?id=' . $ROWID . '" class="font-sanspro-300 font_normal">
    						<i class="fa fa-angle-double-right"></i> View Genes
    					</a> &nbsp;';
    				}
    				?>
    				<!-- <a href="example.php" id="volcano_btn_load_demo" class="font-sanspro-300 font_normal">
    					<i class="fa fa-angle-double-right"></i> Load Example
    				</a> -->
    			</h1>
          <hr />
    			<div class="row mx-0 p-l-1">
    				<div class="col-md-12">
    				<form id="form_valcano_chart" method="post" enctype="multipart/form-data">
    					<input name="chart_number" value="1" id="chart_number" hidden>

    					<!-- Div For All Chart Settings-->

    					<div class="row mx-0" id="chart_setting_all_container">

    						<!-- Div For Single Chart Settings-->

    						<div class="chart_setting_single_container w-100">



    							<div class="row">
    								<div class="col-md-2 text-md-right gray">
    									Comparison ID:
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
    										</span>
    										<span class="input-group-btn">
    											<button
														class="btn_search_comparison btn btn-link "
														inhouse="false"
														type="button"
														index="0">
														<i class="fa fa-search"></i> Select Comparison
													</button>
    										</span>
    									</div>


    									<span class="gray">Please enter the comparison id, e.g., <?php echo $CONFIG_PROFILE['VOLCANO']['demo_comparison']; ?></span>
    								</div>
    							</div>

    							<div class="row mt-1">
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
    									<input class="form-control float-left" name="volcano_fc_custom_cutoff[]" placeholder="Custom Cutoff" style="width:10.3em;" hidden>
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
    									<input class="form-control float-left" name="volcano_statistic_custom_cutoff[]" placeholder="Custom Cutoff" style="width:10.3em;" hidden>
    								</div>
    							</div>

    							<hr />

    						</div>

    					</div>


    					<div class="row m-t-0">
    						<div class="col-md-2 text-md-right gray">
    							Show <?php echo $APP_MESSAGE['Gene']; ?> Name:
    						</div>
    						<div class="col-md-10">
    							<label>
    								<input type="radio" class="volcano_show_gene" name="volcano_show_gene" id="volcano_show_gene_auto" value="auto"<?php
                      echo (!$CUSTOM) ? ' checked' : '';
                    ?>>
    								Auto (Based on Cutoff)
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


    					<div class="row mt-1" id="volcano_show_gene_names_div"<?php
                echo (!$CUSTOM) ? ' style="display:none;"' : '';
              ?>>
    						<div class="col-md-2 text-md-right gray">
                            <?php /*
    							Enter <?php echo $APP_MESSAGE['Genes']; ?> Names：<br />
                  <a href="javascript:void(0);" id="btn_show_gene_list" style="float:right;">
    								<i class="fa-fw fas fa-shopping-cart"></i> Load Saved <?php echo $APP_MESSAGE['Genes']; ?>
    							</a>
								*/ ?>
                                
                                <?php
								echo "<strong>{$APP_MESSAGE['Gene Names']}:</strong>";
								?>
                                
    						</div>
    						<div class="col-lg-6 col-md-10 col-sm-12">
                            	<?php
								echo "<table>";
									echo "<tr>";
										if (true){
											echo "<td>";
												//echo "<strong>{$APP_MESSAGE['Gene Names']}:</strong>";
											echo "</td>";
										}
										
										if (true){
											echo "<td>";
												echo "&nbsp;";
											echo "</td>";	
										}
										
										if (true){
											echo "<td>";
												echo "<a href='javascript:void(0);' id='btn_show_gene_list' type='gene'><i class='fas fa-shopping-cart' aria-hidden='true'></i> {$APP_MESSAGE['Load Saved Genes']}</a>";
											echo "</td>";
										}
										
										if (true){
											echo "<td>";
												echo "&nbsp; &nbsp;";
											echo "</td>";	
										}
										
										
										if (true){
											echo "<td>";
												echo genesets_api_get_header_code('', '', 'volcano_show_gene_names');
												echo genesets_api_get_body_code();
											echo "</td>";
										}
										
										echo "</tr>";
									echo "</table>";
								
								
								?>
    							<textarea class="form-control" name="volcano_show_gene_names" id="volcano_show_gene_names" style="height:10rem;"><?php
                      if ($CUSTOM) {
                        echo trim(implode($CUSTOM_GENES, "\n"));
                      }
                    ?></textarea>
    						</div>
    					</div>



    					<div class="row mt-1">
    						<div class="col-md-2">&nbsp;</div>
    						<div class="col-md-10">
                            	<?php /*
    							<p class="font-sanspro-300 mb-3">
    								<a href="javascript:void(0);" id="btn_add_chart"><i class="fa fa-angle-double-right"></i> Add A New Chart</a>
    							</p>
								*/ ?>
    							<button id="btn_submit" class="btn btn-primary"><i class="far fa-chart-bar"></i> Plot</button>
    						</div>
    					</div>


    				</form>
    				</div>

    				<div class="col-md-4">
    				</div>
    			</div>

    			<div class="row mx-0 mt-4">
    				<div id="debug" class="w-100"></div>
    			</div>


    			<div class="row mx-0 mt-4" id="table_div">
    			</div>


    		</div>




      </div>
		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
		</div>

	</div>





<!----------------------------------------------------------------------------------------------------->
<!-- Modal to Select Comparison -->
<!----------------------------------------------------------------------------------------------------->
<div class="modal fade" id="modal_select_comparison" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Search Comparison</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<?php
					$sql = "SELECT `ComparisonID`,
								`Case_CellType`,
								`Case_DiseaseState`,
								`ComparisonContrast`
							FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`";
					//$data = $DB -> get_all($sql);
					$data = get_multiple_record('Comparison', 0, 'GetAll', "`ComparisonIndex`, `ComparisonID`, `Case_CellType`, `Case_DiseaseState`, `ComparisonContrast`", 1);
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
								<td class="nowrap">' . $comparison['ComparisonID'] . ' &nbsp;
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



<div class="modal fade" id="modal_select_gene_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        <h4 class="modal-title">Please select a gene list you like to load:</h4>
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








<script>



var domReady = function(callback) {
    document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};


domReady(function() {



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


	// // Load Demo
	// document
	// 	.getElementById('volcano_btn_load_demo')
	// 	.addEventListener('click', function() {
  //
	// 		document.getElementById("debug").innerHTML = 'Loading';
	// 		$.ajax({
	// 			type: 'POST',
	// 			url: 'exe.php?action=volcano_load_demo',
	// 			success: function(responseText) {
	// 				$('#debug').html(responseText);
	// 			}
	// 		});
	// });


	// Upload File
	$(document).on('change', '.input_file', function(e) {
		var current = $(this);
		var name = e.target.files[0].name;
		current.parent().parent().next().next().find('input').val(name.substring(0, name.length - 4));
	});
	/*$('input[type=file]').change(function(e){
		alert(e.target.files[0].name);
	});*/




	// Select Comparison
	var index_select = 0;
	$(document).on('click', '.btn_search_comparison', function() {
		index_select = $(this).attr('index');
		var inhouse = $(this).attr('inhouse');
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
	$(document).on('change', '.volcano_fc_cutoff, .volcano_statistic_cutoff', function() {
		var current = $(this);
		if (current.val() == 'enter_value') {
			current.next().removeAttr('hidden');
		} else {
			current.next().attr('hidden', '');
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
		var current_length = $('.input_file').length;
		$.ajax({
			type: 'POST',
			url: 'exe.php?action=add_new_chart',
			data: {current_length: current_length},
			success: function(responseText) {
				$('#chart_setting_all_container').append(responseText);
				$('#chart_number').val(parseInt(current_length) + 1);
			}
		});
	});

	// Load Saved Genes
	$(document).on('click', '#btn_load_saved_genes', function() {
		var saved_genes = '<?php echo $GENE_NAMES; ?>';
		$('#volcano_show_gene_names').val(saved_genes);
	});


  // Load Gene List
  $(document).on('click', '#btn_show_gene_list', function () {
    $.ajax({
  		type: 'POST',
  		url: '<?php echo $BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS'] . 'app_list_ajax_selection.php?category=Gene&input_name=geneList&input_class=geneList'; ?>',
  		success: function (response) {
        $('#modal_select_gene_list_body').html(response);
        $('#modal_select_gene_list').modal();
  		}
  	});
  });
  $(document).on('click', '#btn_select_gene_list', function () {
    var selected = $('.geneList').val();
		$('.geneList').each(function(index, element) {
			if ($(element).is(':checked')) {
				selected = $(element).val();
			}
		});
		$('#volcano_show_gene_names').val($('#gene_list_content_' + selected).val());
  });
  // Click to load, from Derrick
  $(document).on('click', '.geneList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		var radioID			= 'geneList_' + currentListID;
		$('#' + radioID).prop('checked', true);
		var content = $('#gene_list_content_' + currentListID).val();
		$('#volcano_show_gene_names').val(content);
	});
  $(document).on('change', '.geneList', function(){
		var currentListID = $(this).val();
		var content = $('#gene_list_content_' + currentListID).val();
		$('#volcano_show_gene_names').val(content);
	});


	// Generate Chart
	var options = {
		url: 'exe.php?action=volcano_generate_chart',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			if ($('#file').val() == '') {
				message_alert('error', 'Please select a csv file.', '');
				return false;
			}
			//$('#btn_submit').children(':first').removeClass('fa-upload').addClass('fa-spin fa-spinner');
			$('#btn_submit').attr('disabled', '');
			return true;
		},
    success: function(responseText, statusText){
			//$('#btn_submit').children(':first').removeClass('fa-spin fa-spinner').addClass('fa-upload');
			$('#btn_submit').removeAttr('disabled');
			if(responseText.substring(0, 5) == 'Error'){
				bootbox.alert(responseText);
			} else {
				$('#debug').html(responseText);
			}

			return true;
		}
    };
	$('#form_valcano_chart').ajaxForm(options);



});


</script>




</body>
</html>
