<?php
include_once('config.php');

if (!$_GET['noforward']){
	header("Location: {$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_comparison_PAGE.php?id={$_GET['id']}&direction={$_GET['direction']}");
	exit();
}



if (!isset($_GET['id']) || trim($_GET['id']) == '') {
	header("Location: index.php");
	exit();
}

$ROWID = $_GET['id'];
$DIRECTION = $_GET['direction'];


if (isset($_GET['number']) && $_GET['number'] == 'All') {
  $NUMBER = 'All';
} else if (intval($_GET['number']) > 0) {
  $NUMBER = intval($_GET['number']);
} else {
  $NUMBER = 1000;
}

// Check Inhouse Comparison
if (isset($_GET['inhouse']) && $_GET['inhouse'] == 'true') {
  $INHOUSE = true;
	$report_file = '../functional_enrichment/' . $BXAF_CONFIG['BXAF_SPECIES'] . '_GO_out_inhouse/inhouse_comp_' . $ROWID . '/PAGE_cleaned_data.csv.csv';
} else {
	//Bug
  $INHOUSE = false;
  $report_file = $BXAF_CONFIG['PAGE_OUTPUT_HUMAN'] . '/comparison_' . $ROWID . '_GSEA.PAGE.csv';
}

//var_dump($report_file);

// $report_file = '../gsea/PAGE_OUT/comparison_' . $ROWID . '_GSEA.PAGE.csv';

$file = fopen($report_file,"r") or die('Unable to open');
$FILE_CONTENT = array();
while(! feof($file)) {
	$row = fgetcsv($file);

	if (is_array($row) && count($row) > 1 && trim($row[0]) != 'Name') {
  	$FILE_CONTENT[] = $row;
	}
}
fclose($file);


function sort_by_large($a, $b) {
  return ($a['2'] - $b['2'] > 0) ? -1 : 1;
}
function sort_by_small($a, $b) {
  return ($a['2'] - $b['2'] < 0) ? -1 : 1;
}





// Get Top 10 Z-Score Records

usort($FILE_CONTENT, 'sort_by_large');



if ($DIRECTION == 'up') {
  $RESULT_DATA = array();
	if ($NUMBER == 'All') {
		foreach ($FILE_CONTENT as $row) {
			$RESULT_DATA[] = $row;
		}
	} else {
	  for ($i = 0; $i < $NUMBER; $i++) {
	    $RESULT_DATA[] = $FILE_CONTENT[$i];
	  }
	}
} else {
  // Get Bottom 10 Z-Score Records
  usort($FILE_CONTENT, 'sort_by_small');
  $RESULT_DATA = array();
	if ($NUMBER == 'All') {
		foreach ($FILE_CONTENT as $row) {
			$RESULT_DATA[] = $row;
		}
	} else {
	  for ($i = 0; $i < $NUMBER; $i++) {
	    $RESULT_DATA[] = $FILE_CONTENT[$i];
	  }
	}
}


//echo general_printr($RESULT_DATA);
//exit();

// echo '<pre>'; print_r($FILE_CONTENT); echo '</pre>'; exit();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">


<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/plotly.min.js"></script>
<script type="text/javascript" src="../library/config.js"></script>

<link href="../library/DataTables/media/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/DataTables/media/js/jquery.dataTables.min.js"></script>

<!-- DataTables -->
<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>


</head>
<body>


  <div class="loader loader-default is-active"
       data-text="Loading <?php echo $PAGE_TYPE; ?> Info"
       style="margin-left:0px; margin-top:0px;"></div>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_content" class="row no-gutters h-100">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

      <div id="bxaf_page_right_content" class="w-100 p-2">

    		<div class="container-fluid">
	    		<h1 class="pt-3">
	    			<?php echo strtoupper($DIRECTION); ?>regulated PAGE Report &nbsp;
	    			<a href="single_comparison.php?type=comparison&id=<?php echo $ROWID; ?>&inhouse=<?php echo $_GET['inhouse']; ?>" class="font-sanspro-300 font_normal">
	            <i class="fa fa-angle-double-right"></i> Back to comparison page
	          </a>&nbsp;
	    		</h1>
	        <hr />



	    		<div class="row mx-0">

	          <select class="form-control width-200" id="select_number_displayed">
	            <option value="0">Select # displayed
	            <?php
	              $number_options = array('100', '1000', '2000', '5000', '10000', 'All');
	              foreach ($number_options as $number) {
	                echo '<option value="' . $number . '">' . $number . '</option>';
	              }
	            ?>
	          </select>
	          <br />
	    			<table class="table table-bordered table-striped datatable">
	    				<thead>
	    					<tr class="bg-primary">
	    						<th style="max-width:400px;">Gene Set Name</th>
	    						<th style="white-space:nowrap"># Genes</th>
	    						<th style="white-space:nowrap">Z Score</th>
	    						<th style="white-space:nowrap">P Value</th>
	    						<th style="white-space:nowrap">FDR</th>
	    					</tr>
	    				</thead>
	    				<tbody>
	    					<?php
	    						foreach ($RESULT_DATA as $key => $value) {

	                  if (floatval($value[2]) > 1) {
	                    $z_color = '#FF0000';
	                  } else if (floatval($value[2]) > 0) {
	                    $z_color = '#FF9C9C';
	                  } else if (floatval($value[2]) == 0) {
	                    $z_color = '#979797';
	                  } else if (floatval($value[2]) > -1) {
	                    $z_color = '#81C86E';
	                  } else {
	                    $z_color = '#02CA2D';
	                  }


	                  if (floatval($value[3]) < 0.01) {
	                    $p_color = '#02CA2D';
	                  } else {
	                    $p_color = '#979797';
	                  }

	                  if (floatval($value[4]) < 0.05) {
	                    $fdr_color = '#02CA2D';
	                  } else {
	                    $fdr_color = '#979797';
	                  }

	    							echo '
	    								<tr>
	    									<td>' . $value[0] . '</td>
	    									<td>' . $value[1] . '</td>
	    									<td style="color:' . $z_color . '">' . $value[2] . '</td>
	    									<td style="color:' . $p_color . '">' . $value[3] . '</td>
	    									<td style="color:' . $fdr_color . '">' . $value[4] . '</td>
	    								</tr>';
	    						}
	    					?>
	    				</tbody>
	    			</table>
	    		</div>

				</div>

      </div>
      <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
    </div>
  </div>








<script>


$(document).ready(function() {

	$('.datatable').DataTable({
    "initComplete": function(settings, json) {
      $('.loader').remove();
    },
    "dom": 'Bfrtip',
    "buttons": [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    "order": [[ 0, "desc" ]]
  });

  $(document).on('change', '#select_number_displayed', function() {
    var number = $(this).val();
    window.location = 'page_report.php?id=<?php echo $ROWID; ?>&direction=<?php echo $DIRECTION; ?>&inhouse=<?php echo $_GET['inhouse']; ?>&number=' + number;
  });

});


</script>




</body>
</html>
