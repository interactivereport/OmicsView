<?php
include_once('config.php');
if (!isset($_GET['id']) || trim($_GET['id']) == '') {
	header("Location: index.php");
	exit();
} else {
	header("Location: ../../gene_expressions/app_comparison_genes.php?ID={$_GET['id']}");
	exit();
}




$ROWID 	= intval($_GET['id']);


if (internal_data_is_public($ROWID)){
	$TABLE 	= $BXAF_CONFIG['TBL_COMPARISONS'];
	$sql 	= "SELECT * FROM `{$TABLE}` WHERE `ComparisonIndex`='{$ROWID}'";
	$data 	= $DB -> get_row($sql);
} else {
	$data = get_multiple_record('Comparison', $ROWID, 'GetRow');
}







// Use Tabix
$comparisonIndex = array($ROWID);
if (internal_data_is_public($ROWID)){
	$tabix_result = tabix_search_records_with_index('', $comparisonIndex, 'ComparisonData');
} else {
	$tabix_result = tabix_search_records_with_index_internal_data($data['ProjectIndex'], '', $comparisonIndex, 'ComparisonData');
}

$ALL_GENES = array();
foreach ($tabix_result as $key => $value) {
	$temp_row = array();
	foreach ($value as $k => $v) {
		$temp_row[] = $v;
	}
	$gene_index = trim($value['GeneIndex']);
	$sql = "SELECT `GeneID`, `EntrezID` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}` WHERE `GeneIndex`=" . $gene_index;
	$temp = $DB -> get_row($sql);
	$temp_row[] = $temp['GeneID'];
	$temp_row[] = $temp['EntrezID'];
	$ALL_GENES[] = $temp_row;
}





// Find 'NumeratorValue' and 'DenominatorValue'
//$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $ROWID;
//$comparison = $DB -> get_row($sql);
$comparison = $data;

$category = $comparison['ComparisonCategory'];
$contrast = $comparison['ComparisonContrast'];


if (strpos($category, ' vs. ') !== false) {
  $pos1 = strpos($category, ' vs. ');
  $NumeratorValue = substr($category, 0, $pos1);
  $DenominatorValue = substr($category, $pos1+4);
} else {
  $pos2 = strpos($contrast, ' vs ');
  $NumeratorValue = trim(substr($contrast, 0, $pos2));
  $DenominatorValue = trim(substr($contrast, $pos2+3));
}


// Generate CSV File
$dir = dirname(__FILE__) . '/files/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
if (!is_dir($dir)) {
  mkdir($dir, 0755, true);
}

$file = fopen($dir . "/" . $data['ComparisonID'] . ".csv","w");

fputcsv($file, array('Name', 'Log2FoldChange', 'PValue', 'AdjustedPValue', $NumeratorValue, $DenominatorValue, 'GeneName', 'EntrezID'));

foreach ($ALL_GENES as $gene) {
  if (trim($gene['3']) != 'NA') {
    unset($gene[0]);
    unset($gene[1]);
    fputcsv($file, $gene);
  }
}
fclose($file);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../library/animate.css.php" rel="stylesheet">
<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/config.js"></script>

<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
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


</head>
<body>
  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

      <div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">

    		<div class="container-fluid">
      		<h1 class="pt-3">
      			Comparison Gene Info &nbsp;
            <a href="single_comparison.php?type=comparison&id=<?php echo $data['ComparisonIndex']; ?>" class="font-sanspro-300 font_normal"><i class="fa fa-angle-double-right"></i> Back to Comparison Details</a>&nbsp;
      		</h1>
          <hr />


      		<!-- Action Buttons -->
      		<?php

          echo '

          <div class="row mb-3">
            <div class="col-md-4">
              <strong>Comparison ID: </strong><br />' . $data['ComparisonID'] . '
            </div>
            <div class="col-md-4">
              <strong>Category: </strong><br />' . $data['ComparisonCategory'] . '
            </div>
            <div class="col-md-4">
              <strong>Contrast: </strong><br />' . $data['ComparisonContrast'] . '
            </div>
          </div>';

          echo '
          <a href="files/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . '/' . $data['ComparisonID'] . '.csv"
             class="btn btn-primary mb-3">
            Download File
          </a>';


          echo '

          <div class="alert alert-warning" id="div_loading">
            <i class="fa fa-spin fa-spinner"></i> Loading...
          </div>

          <table id="table_gene_info" class="table table-bordered"
                 style="display:none; width:100%;">
            <thead>
              <tr>
                <th>Name</th>
                <th>Log2FoldChange</th>
                <th>PValue</th>
                <th>AdjustedPValue</th>
                <th>' . $NumeratorValue . '</th>
                <th>' . $DenominatorValue . '</th>
                <th>GeneName</th>
                <th>EntrezID</th>
              </tr>
            </thead>
            <tbody>';

            foreach ($ALL_GENES as $gene) {
              if (trim($gene['3']) == 'NA') continue;
              echo '
              <tr>
                <td>' . $gene['2'] . '</td>
                <td>' . $gene['3'] . '</td>
                <td>' . $gene['4'] . '</td>
                <td>' . $gene['5'] . '</td>
                <td>' . $gene['6'] . '</td>
                <td>' . $gene['7'] . '</td>
                <td>' . $gene['8'] . '</td>
                <td>' . $gene['9'] . '</td>
              </tr>';
            }

          echo '</tbody></table>';
      		?>
					</div>
        </div>
  	    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
  	</div>



<script>


$(document).ready(function() {

	$('#sidebar_link_search_comparison').addClass('active');
	$('#sidebar_link_search_comparison').parent().parent().prev().addClass('active');
	$('#sidebar_link_search_comparison').parent().parent().css('display', 'block');
	$('#sidebar_link_search_comparison').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');

  setTimeout(function() {
    $('#div_loading')
      .removeClass('alert-warning')
      .addClass('alert-danger')
      .html('Gene data info exceeds limit of memory, please download the csv file.');
  }, 1000);



  $('#table_gene_info').DataTable({
    // dom: 'lBfrtip',
    // buttons: [
    //     // 'copy', 'csv', 'excel', 'pdf', 'print'
    //     'csv'
    // ],
    // "dom": 'lBfrtip',
    "initComplete": function( settings, json ) {
      $('#div_loading').hide();
      $('#table_gene_info').show();
    },
  });



});


</script>




</body>
</html>
