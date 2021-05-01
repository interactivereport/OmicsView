<?php
include_once('config.php');
if (isset($_GET['type']) && $_GET['type'] == 'sample') {
	$PAGE_TYPE = 'Sample';
} else if ($_GET['type'] == 'gene') {
	$PAGE_TYPE = 'Gene';
} else if ($_GET['type'] == 'project') {
	$PAGE_TYPE = 'Project';
} else {
	$PAGE_TYPE = 'Comparison';
}
$ADDITIONAL_CONDITION = false;


header("Location: {$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_record_browse.php?Category={$PAGE_TYPE}");
exit();




//------------------------------------------------------
// Other Categories
//------------------------------------------------------

// Samples for a comparison
if ($PAGE_TYPE == 'Sample' && isset($_GET['comparison_id']) && intval(trim($_GET['comparison_id'])) != 0) {
	$COMPARISON_ID = intval(trim($_GET['comparison_id']));
	$sql = "SELECT `Case_SampleIDs`, `Control_SampleIDs` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $COMPARISON_ID;
	$data = $DB -> get_row($sql);
	$ALL_SAMPLES = array(); // Save all samples
	foreach(explode(";", $data['Case_SampleIDs']) as $sampleID) {
		$ALL_SAMPLES[] = trim($sampleID);
	}
	foreach(explode(";", $data['Control_SampleIDs']) as $sampleID) {
		$ALL_SAMPLES[] = trim($sampleID);
	}
	$ADDITIONAL_CONDITION = "`SampleID` IN ('" . implode("', '", $ALL_SAMPLES) . "')";
}

// Samples for a project
if ($PAGE_TYPE == 'Sample' && isset($_GET['project_id']) && trim($_GET['project_id']) != '') {
	$PROJECT_ID = trim($_GET['project_id']);
	$sql = "SELECT `SampleIndex` FROM `{$BXAF_CONFIG['TBL_SAMPLES']}` WHERE `ProjectName`='" . $PROJECT_ID . "'";

	$data = $DB -> get_all($sql);
	$ALL_SAMPLE_ID = array(); // Save all samples
	foreach($data as $sample) {
		$ALL_SAMPLE_ID[] = trim($sample['SampleIndex']);
	}
	$ADDITIONAL_CONDITION = "`SampleIndex` IN (" . implode(", ", $ALL_SAMPLE_ID) . ")";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../library/animate.css.php" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link href="../library/tootik.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">


<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>

<!-- Data Tables -->
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>


<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>

<!-- <script type="text/javascript" language="javascript" src="../library/DataTables/extensions/Buttons/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="../library/DataTables/dynamic_loading/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="../library/DataTables/dynamic_loading/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="../library/DataTables/dynamic_loading/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="../library/DataTables/extensions/Buttons/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="../library/DataTables/extensions/Buttons/js/buttons.print.min.js "></script> -->




<script type="text/javascript" src="../library/config.js"></script>




</head>
<body>
  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

  <div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

      <div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">

    		<div class="container-fluid pt-3">

					<h1 class="page-header">
      			Search <?php echo $PAGE_TYPE; ?> &nbsp;
      		</h1>
					<hr />
      		<p>
      			<a href="index.php?type=comparison" class="m-r-1"><i class="fa fa-angle-double-right"></i> Search Comparison</a>
      			<a href="index.php?type=gene" class="m-r-1"><i class="fa fa-angle-double-right"></i> Search Gene</a>
      			<a href="index.php?type=project" class="m-r-1"><i class="fa fa-angle-double-right"></i> Search Project</a>
      			<a href="index.php?type=sample" class="m-r-1"><i class="fa fa-angle-double-right"></i> Search Sample</a>
      		</p>
      		<p class="gray">Note: You can do quick search using the search box on the top-right of the table, or apply advanced search below.</p>

      		<a href="javascript:void(0);" class="btn btn-primary mb-1" id="btn_toggle_search_div">
            <i class="fa fa-search"></i> Advance Search
          </a>
      		&nbsp;&nbsp;
      		<a href="../user_preference/index.php?type=search_table_<?php echo $PAGE_TYPE; ?>"
             class="btn btn-success mb-1"
             target="_blank">
             <i class="fa fa-cog"></i> Table Settings
          </a>
      		&nbsp;&nbsp;



          <?php
          // Save to Session
          if ($PAGE_TYPE == 'Comparison' || $PAGE_TYPE == 'Gene' || $PAGE_TYPE == 'Sample') { ?>
            <a href="javascript:void(0);"
               class="btn btn-warning mb-1"
               id="btn_save_session"
               type="<?php echo strtolower($PAGE_TYPE); ?>">
               <i class="fa fa-cog"></i> Save Selected <?php echo $PAGE_TYPE.'s'; ?>
            </a>
        		&nbsp;&nbsp;
          <?php }

      		if ($PAGE_TYPE == 'Project') {
      			echo '
      			<a href="javascript:void(0);"
               class="btn btn-warning mb-1"
               id="btn_save_session"
               type="' . strtolower($PAGE_TYPE) . '">
               <i class="fa fa-cog"></i> Save Samples from Selected Projects';
      			echo '</a>&nbsp;&nbsp;';
      		}
      		?>


          <?php
          // Significant Genes for Several Comparisons
          if ($PAGE_TYPE == 'Comparison') { ?>
						<a href="javascript:void(0);"
							 class="btn btn-primary mb-1"
							 id="btn_save_comparison_samples">
							 <i class="fa fa-database"></i> Save Samples
						</a>
						&nbsp;&nbsp;
            <a href="javascript:void(0);"
               class="btn btn-info mb-1"
               id="btn_significant_genes">
               <i class="fa fa-database"></i> Significantly Changed Genes
            </a>
        		&nbsp;&nbsp;
          <?php } ?>

      		<a href="javascript:void(0);" onclick="location.reload(true);"><i class="fa fa-angle-double-right"></i> Reset search conditions</a>

      		&nbsp;
      		<label>
      			<input type="checkbox" class="check_all">
      			Check/Uncheck All
      		</label>



      		<div class="row m-x-0 alert alert-warning" style="display:none;">
      			<form class="m-b-0" id="form_search_comparison" method="post" enctype="multipart/form-data">
      			<input name="page_type" class="hidden" value="<?php echo $PAGE_TYPE; ?>">

      			<div class="row">
      				<div class="col-sm-2"><strong>Logic</strong></div>
      				<div class="col-sm-4"><strong>Field to Search</strong></div>
      				<div class="col-sm-2"><strong>Operator</strong></div>
      				<div class="col-sm-4"><strong>Value</strong></div>
      			</div>


      			<div id="search_condition_div">
      				<div class="row">
      					<div class="col-sm-2 mb-1">
      						<input class="hidden" name="search_logic[]">
      					</div>
      					<div class="col-sm-4 mb-1">
      						<select class="form-control" name="search_field[]">
      						<?php
      							foreach($BXAF_CONFIG["COLNAME_LIST_" . strtoupper($PAGE_TYPE)] as $colname) {
      								echo '<option value="' . $colname . '">' . $colname . '</option>';
      							}
      						?>
      						</select>
      					</div>
      					<div class="col-sm-2 mb-1">
      						<select class="form-control"  name="search_operator[]">
      							<option value="is">is</option>
      							<option value="contains">contains</option>
      							<option value="starts_with">starts with</option>
      							<option value="ends_width">ends with</option>
      						</select>
      					</div>
      					<div class="col-sm-4 mb-1">
      						<input class="form-control" name="search_value[]" required>
      					</div>
      				</div>
      			</div>

      			<button class="btn btn-primary btn-outline" id="btn_submit"><i class="fa fa-check-circle"></i> Search</button>
      			&nbsp;&nbsp;
      			<a href="javascript:void(0);" id="btn_add_search_condition"><i class="fa fa-angle-double-right"></i> Add Search Condition</a>

      			<div id="div_number_records" class="row m-x-0 m-t-1"></div>

      			</form>
      		</div>

      		<div class="mt-3" id="div_main_table">

      		<?php

            // if ($PAGE_TYPE == 'Comparison' || $PAGE_TYPE == 'Gene' || $PAGE_TYPE == 'Sample') {
            //   echo '
            //     <label>
          	// 			<input type="checkbox" class="check_all">
          	// 			Check/Uncheck All
          	// 		</label>';
            // }

        		echo '<table class="table table-bordered table-striped" id="table_select_comparison" style="width:100%; font-size:14px;"><thead><tr>';


        		$col_setting = unserialize($BXAF_CONFIG['PREFERENCE_DETAIL'][strtolower($PAGE_TYPE).'_search_page_table_column']);
        		//foreach ($BXAF_CONFIG['DEFAULT_TABLE_COLUMNS'][$PAGE_TYPE] as $colname) {

        		echo '<th>' . $PAGE_TYPE . 'ID';

      			// echo '
      			// 	<label>
      			// 		<input type="checkbox" class="check_all">
      			// 		Check/Uncheck All
      			// 	</label>';

      			echo '</th>';
        		foreach ($col_setting as $colname) {
        			echo '<th>' . $colname . '</th>';
        		}

        		echo '</tr></thead><tbody></tbody></table>';

      		?>
      		</div>
        </div>

      </div>
      <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
    </div>
  </div>



<script>


$(document).ready(function() {



	$('#sidebar_link_search_<?php echo strtolower($PAGE_TYPE); ?>').addClass('active');
	$('#sidebar_link_search_<?php echo strtolower($PAGE_TYPE); ?>').parent().parent().prev().addClass('active');
	$('#sidebar_link_search_<?php echo strtolower($PAGE_TYPE); ?>').parent().parent().css('display', 'block');
	$('#sidebar_link_search_<?php echo strtolower($PAGE_TYPE); ?>').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');

	//$('#table_select_comparison').DataTable();

	var buttonCommon = {
		exportOptions: {
			format: {
				body: function ( data, row, column, node ) {
					// Strip $ from salary column to make it numeric
					if (column === 0) {
						return data.substring(0, data.indexOf("&nbsp;"));
					} else {
						return data;
					}
				}
			}
		}
	};

	$('#table_select_comparison').DataTable({
    <?php if ($PAGE_TYPE == 'Gene') { ?>
      "order": [[ 1, "desc" ]], // Sort Entrez ID
    <?php } ?>
    "dom": 'lBfrtip',
		"lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
    // "buttons": [
    //   'copy', 'csv', 'excel', 'pdf', 'print'
    // ],
		buttons: [
      $.extend( true, {}, buttonCommon, {
          extend: 'copyHtml5'
      } ),
      $.extend( true, {}, buttonCommon, {
          extend: 'excelHtml5'
      } ),
      $.extend( true, {}, buttonCommon, {
          extend: 'csvHtml5'
      } ),
      $.extend( true, {}, buttonCommon, {
          extend: 'pdfHtml5'
      } )
    ],
		"processing": true,
		"serverSide": true,
		"language": {
	    "infoFiltered": "" // Remove text 'filtered from NAN entries'
	  },
		"ajax": {
			"url": "exe.php?action=data_table_dynamic_loading&type=<?php echo $PAGE_TYPE; ?>",
			"type": "POST",
			<?php
				if ($ADDITIONAL_CONDITION) {
					echo '"data": {"sql": " ' . addslashes($ADDITIONAL_CONDITION) . '"}';
				}
			?>
			//"data": {"test": "test"}
		},
		"iDisplayLength": 10,
		"columns": [
			<?php if ($PAGE_TYPE == 'Sample') { ?>
				{ "data": "SampleID", render: function(data) {
					var list = data.split("__");
					var name = list[0];
					var index = list[1];
					var content = name;
          content += '&nbsp;&nbsp;<input type="checkbox" class="checkbox_save_session"';
					content += ' rowid="' + index + '">';
          content += '&nbsp;&nbsp;<a href="single_comparison.php?type=sample&id=' + index
					content += '" data-tootik="View Detail" target="_blank"><i class="fa fa-list-ul"></i></a>';
					return content;
				} },
				<?php
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
				?>
			<?php } else if ($PAGE_TYPE == 'Gene') { ?>
				{ "data": "GeneID", render: function(data) {
					var list = data.split("__");
					var name = list[0];
					var index = list[1];
					var content = name;

          content += '&nbsp;&nbsp;<input type="checkbox" class="checkbox_save_session"';
					content += ' rowid="' + index + '">';
          content += '&nbsp;&nbsp;<a href="single_comparison.php?type=gene&id=' + index;
					content += '" data-tootik="View Detail" target="_blank"><i class="fa fa-list-ul"></i></a>';
					return content;
				} },
				<?php
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
				?>
			<?php } else if ($PAGE_TYPE == 'Project') { ?>
				{ "data": "ProjectID", render: function(data) {
					var list = data.split("__");
					var name = list[0];
					var index = list[1];
					var content = name;
					content += '&nbsp;&nbsp;<input type="checkbox" class="checkbox_save_session"';
					content += ' rowid="' + index + '">';
					content += '&nbsp;&nbsp;<a href="single_comparison.php?type=project&id=' + index;

					content += '" data-tootik="View Detail" target="_blank"><i class="fa fa-list-ul"></i></a>';
					return content;
				} },
				<?php
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
				?>
			<?php } else { ?>
				{ "data": "ComparisonID", render: function(data) {
					var list = data.split("__");
					var comparison_name = list[0];
					var comparison_index = list[1];
					var content = '';

					content += comparison_name
					content += '<br /><input type="checkbox" class="checkbox_save_session"';
					content += ' rowid="' + comparison_index + '">';
					content += '&nbsp;&nbsp;<a href="single_comparison.php?type=comparison&id=';
					content += comparison_index;
					content += '" data-tootik="View Detail" target="_blank" class="btn_view_detail">';
					content += '<i class="fa fa-list-ul"></i></a>';
					content += '&nbsp;&nbsp;<a href="../volcano/index.php?id=';
					content += comparison_index;
					content += '" data-tootik="View Volcano Plot" class="btn_view_volcano_plot">';
					content += '<i class="fa fa-pie-chart"></i></a>';
					content += '&nbsp;&nbsp;<a href="../pvjs/index.php?id=';
					content += comparison_index;
					content += '" data-tootik="View Pathway" class="btn_view_pvjs">';
					content += '<i class="fa fa-bar-chart"></i></a>';

					return content;
				} },
				<?php
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
				?>
			<?php } ?>
		]
	});



	var add_search_condition_content = '';
	add_search_condition_content += 	'<div class="row">';
	add_search_condition_content += 		'<div class="col-sm-2 mb-1">';
	add_search_condition_content += 			'<select class="form-control" name="search_logic[]">';
	add_search_condition_content += 				'<option value="and">AND</option>';
	add_search_condition_content += 				'<option value="or">OR</option>';
	add_search_condition_content += 			'</select>';
	add_search_condition_content += 		'</div>';
	add_search_condition_content += 		'<div class="col-sm-4 mb-1">';
	add_search_condition_content += 			'<select class="form-control" name="search_field[]">';
	<?php
		foreach($BXAF_CONFIG["COLNAME_LIST_" . strtoupper($PAGE_TYPE)] as $colname) {
			echo 'add_search_condition_content += \'<option value="' . $colname . '">' . $colname . '</option>\';';
		}
	?>
	add_search_condition_content += 			'</select>';
	add_search_condition_content += 		'</div>';
	add_search_condition_content += 		'<div class="col-sm-2 mb-1">';
	add_search_condition_content += 			'<select class="form-control" name="search_operator[]">';
	add_search_condition_content += 				'<option value="is">is</option>';
	add_search_condition_content += 				'<option value="contains">contains</option>';
	add_search_condition_content += 				'<option value="starts_with">starts with</option>';
	add_search_condition_content += 				'<option value="ends_width">ends with</option>';
	add_search_condition_content += 			'</select>';
	add_search_condition_content += 		'</div>';
	add_search_condition_content += 		'<div class="col-sm-4 mb-1">';
	add_search_condition_content += 			'<input class="form-control" name="search_value[]">';
	add_search_condition_content += 		'</div>';
	add_search_condition_content += 	'</div>';



  /* Toggle Search Condition */
  $(document).on('click', '#btn_toggle_search_div', function() {
    $('#search_condition_div').parent().parent().slideToggle(300);
  });

  /* Add Search Condition */
	$(document).on('click', '#btn_add_search_condition', function() {
		$('#search_condition_div').append(add_search_condition_content);
	});

  /* View Significantly Changed Genes */
  $(document).on('click', '#btn_significant_genes', function() {
    var data_list = [];
		$('.checkbox_save_session').each(function(index, element) {
      if ($(element).is(':checked')) {
        data_list.push($(element).attr('rowid'));
      }
    });
    if (data_list.length == 0) {
      bootbox.alert('No comparison is selected.');
    }
    $.ajax({
      type: 'POST',
      url: '../dashboard/exe.php?action=get_significantly_changed_genes',
      data: {comparisons: data_list},
      success: function(response) {
        window.location = '../dashboard/changed_genes.php';
      }
    });
	});






	/* Check All */
	$(document).on('click', '.check_all', function() {
		var check_all_index = false;
		$('.checkbox_save_session').each(function(index, element) {
  		if (index == 0) {
  			if ($(element).is(':checked')) {
  				check_all_index = true;
  			} else {
  				check_all_index = false;
  			}
  		}
  		if (check_all_index) {
  			$(element).prop('checked', false);
  		} else {
  			$(element).prop('checked', true);
  		}
		});
	});



  //---------------------------------------------------------------------------------
  // Save Session Genes & Comparisons
  $(document).on('click', '#btn_save_session', function() {
    var type = $(this).attr('type');
    var data_list = [];
		$('.checkbox_save_session').each(function(index, element) {
      if ($(element).is(':checked')) {
        data_list.push($(element).attr('rowid'));
      }
    });
    $.ajax({
      type: 'POST',
      url: 'exe.php?action=save_to_session',
      data: {data_list: data_list, type: type},
      success: function(response) {
        if (type == 'project') type='sample';
        // alert(response);
        if (response.substring(0, 5) == 'Error') {
          $('#top-bar-alert')
            .addClass('alert-danger')
            .html(response)
            .slideDown();
          setTimeout(function() {
            $('#top-bar-alert').slideUp();
          }, 5000);
        } else {
          $('#top-bar-alert')
            .addClass('alert-success')
            .html('The ' + type + 's have been added to saved list. \
                  Click <a href="../dashboard/saved_info.php?type=' + type + '">here</a> to view saved ' + type + 's.')
            .slideDown();
					window.location = response;
          setTimeout(function() {
            $('#top-bar-alert').slideUp();
          }, 5000);

        }
      }
    });
	});


	//---------------------------------------------------------------------------------
  // Save Comparison Samples
	$(document).on('click', '#btn_save_comparison_samples', function() {
		var type = $(this).attr('type');
		var data_list = [];
		$('.checkbox_save_session').each(function(index, element) {
			if ($(element).is(':checked')) {
				data_list.push($(element).attr('rowid'));
			}
		});
		$.ajax({
			type: 'POST',
			url: '../dashboard/exe.php?action=save_selected_samples',
      data: {comparisons: data_list},
      success: function(response) {
        if (response.substring(0, 5) == 'Error') {
          bootbox.alert(response);
        } else {
					window.location = response;
        }
			}
		});
	});

	var options = {
		url: 'exe.php?action=search_comparison',
 		type: 'post',
        beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit').children(':first').removeClass('fa-check-circle').addClass('fa-spin fa-spinner');
			$('#btn_submit').attr('disabled', '');
			return true;
		},
        success: function(responseText, statusText){
			$('#btn_submit').children(':first').removeClass('fa-spin fa-spinner').addClass('fa-check-circle');
			$('#btn_submit').removeAttr('disabled');

			if(responseText.substring(0, 5) == 'Error'){
				bootbox.alert(responseText);
			} else {
				$('#div_main_table').html(responseText);
				$('#div_number_records').html($('#number_records').val() + ' out of <?php echo $_SESSION['RECORD_NUMBER'][strtolower($PAGE_TYPE)]; ?> records found. ');
			}

			return true;
		}
    };
	$('#form_search_comparison').ajaxForm(options);


});


</script>




</body>
</html>
