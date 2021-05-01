<?php
include_once('config.php');
if (!isset($_GET['id']) || trim($_GET['id']) == '') {
	header("Location: index.php");
	exit();
}

if ($_GET['type'] == 'project'){
	header("Location: ../../gene_expressions/app_project_review.php?id={$_GET['id']}");
	exit();	
}

if ($_GET['type'] == 'sample'){
	header("Location: ../../gene_expressions/app_sample_review.php?id={$_GET['id']}");
	exit();	
}

if ($_GET['type'] == 'gene'){
	header("Location: ../../gene_expressions/app_gene_review.php?id={$_GET['id']}");
	exit();	
}

if ($_GET['type'] == 'dataset'){
	header("Location: ../../gene_expressions/app_dataset_review.php?id={$_GET['id']}");
	exit();	
}

// echo '<pre>'; print_r($BXAF_CONFIG); echo '</pre>';
// echo $BXAF_CONFIG['BXAF_URL'] . 'app/plot';exit();

$PAGE['Category'] = 'Search';
$PAGE['Barcode']  = 'Search Comparison';

// Page Type
if (isset($_GET['type']) && $_GET['type'] == 'sample') {
	$PAGE_TYPE = 'Sample';
	$PAGE['Barcode'] = 'Search Sample';
} else if ($_GET['type'] == 'gene') {
	$PAGE_TYPE = 'Gene';
	$PAGE['Barcode'] = 'Search Gene';
} else if ($_GET['type'] == 'project') {
	$PAGE_TYPE = 'Project';
	$PAGE['Barcode'] = 'Search Project';
} else {
	$PAGE_TYPE = 'Comparison';
}


$ROWID = intval($_GET['id']);
// Database Table
switch ($PAGE_TYPE) {
	case "Sample":
		$TABLE = $BXAF_CONFIG['TBL_SAMPLES'];
		$data = get_multiple_record($PAGE_TYPE, $ROWID, 'GetRow');
		break;
		
	case "Gene":
		$TABLE = $BXAF_CONFIG['TBL_GENECOMBINED'];
		$sql = "SELECT * FROM `{$TABLE}` WHERE `" . $PAGE_TYPE . "Index`=" . $ROWID;
		$data = $DB -> get_row($sql);
		break;

	case "Project":
		$TABLE = $BXAF_CONFIG['TBL_PROJECTS'];
		$data = get_multiple_record($PAGE_TYPE, $ROWID, 'GetRow');
		break;

	default:
		$TABLE = $BXAF_CONFIG['TBL_COMPARISONS'];
		$data = get_multiple_record($PAGE_TYPE, $ROWID, 'GetRow');
}

if (0 && isset($_GET['inhouse']) && $_GET['inhouse'] == 'true') {
  $INHOUSE = true;
  $TABLE   = $BXAF_CONFIG['TBL_INHOUSE_COMPARISON'];
  $sql = "SELECT * FROM `{$TABLE}` WHERE `" . $PAGE_TYPE . "Index`=" . $ROWID;
	$data = $DB -> get_row($sql);
} else {
  $INHOUSE = false;
}


if (general_array_size($data) <= 0){
	echo "Error. Please verify your link and try again.";
	exit();	
}


unset($data['User_ID']);
unset($data['Job_ID']);
unset($data['Import_ID']);
unset($data['Internal_Platform_Type']);
unset($data['Status']);
unset($data['Date']);
unset($data['DateTime']);
unset($data['ProjectIndex']);
unset($data['SampleIndex']);
unset($data['GeneIndex']);
unset($data['ComparisonIndex']);
unset($data['ID']);
unset($data['Increment']);
unset($data['Permission']);




if ($_GET['type'] == 'sample'){
	foreach($data as $tempKey => $tempValue){
		if (strpos($tempKey, 'Clinical_Triplets_') === 0){
			//unset($data[$tempKey]);	
		}
	}
}


foreach($data as $tempKey => $tempValue){
	if (strpos($tempKey, 'Custom_') === 0){
		unset($data[$tempKey]);	
	}
}



// Check Permission for InHouse Comparison Data
if (isset($_GET['inhouse']) && $_GET['inhouse'] == 'true') {
  if ($BXAF_CONFIG['BXAF_USER_CONTACT_ID'] != $data['Owner']) {

    header("Location: index.php");
    exit();
  }
  // Format GO Report
  $report_dir = "{$BXAF_CONFIG['INHOUSE_DATA_DIR']}/inhouse_comp_{$_GET['id']}/cleaned_data.csv_GO_Analysis_";
  foreach (array('Up', 'Down') as $direction) {
    if (!file_exists("{$report_dir}{$direction}/report.html")) {
      modify_go_report("{$report_dir}{$direction}", $_GET['id']);
    }
  }
}



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

  <div class="loader loader-default is-active"
       data-text="Loading <?php echo $PAGE_TYPE; ?> Info"
       style="margin-left:0px; margin-top:0px;"></div>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

			<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">

    		<div class="container-fluid pt-3">
    		<h1 class="page-header">
    			<?php echo $PAGE_TYPE; ?> Details
    		</h1>
            
            <p>
            	<a href="index.php?type=<?php echo strtolower($PAGE_TYPE); ?>"> Search All <?php echo $PAGE_TYPE; ?>s</a>
                &bull;
                <a href="../dashboard/changed_genes.php?ComparisonIndex=<?php echo $_GET['id']; ?>"><?php echo $APP_MESSAGE['Significantly Changed Genes']; ?> </a>
            </p>
            
            
				<hr />


    		<!-- Action Buttons -->
    		<?php
    			if ($PAGE_TYPE == 'Comparison') {
    				echo "
    				<div class='row mb-3'>
    					<div class='col-md-4'>
							<div><strong>Comparison ID:</strong></div>
							<div>{$data['ComparisonID']}</div>
    					</div>";
						$researchProjectNotes[] = "Comparison ID: {$data['ComparisonID']}";
						
							if ($INHOUSE) {
								echo "<div class='col-md-4'>
										<div><strong>Name:</strong></div>
										<div>{$data['Name']}</div>
		    						</div>";
								$researchProjectNotes[] = "Name ID: {$data['Name']}";
							} else {
								echo "<div class='col-md-4'>
			    						<div><strong>Category:</strong></div>
										<div>{$data['ComparisonCategory']}</div>
									</div>";
								$researchProjectNotes[] = "Category: {$data['ComparisonCategory']}";
									
								echo "<div class='col-md-4'>
										<div><strong>Contrast:</strong></div>
										<div>{$data['ComparisonContrast']}</div>
									</div>";
								$researchProjectNotes[] = "Contrast: {$data['ComparisonContrast']}";
							}
						echo '
    				</div>

    				<a href="#info_table_div" class="btn btn-primary"><i class="fa fa-arrow-circle-down"></i> View Details</a> &nbsp;
    				<a href="../volcano/index.php?id=' . $ROWID;
						if ($INHOUSE) echo '&inhouse=true';
						echo '" class="btn btn-primary"><i class="fa fa-pie-chart"></i> Comparison Volcano Chart</a> &nbsp;
    				<a href="../pvjs/index.php?id=' . $ROWID;
						if ($INHOUSE) echo '&inhouse=true';
						echo '" class="btn btn-primary"><i class="fa fa-line-chart"></i> Pathway View</a> &nbsp;';
						

					

            if (!$INHOUSE) {
				
				if (($data['Case_SampleIDs'] != '') || ($data['Control_SampleIDs'] != '')){
              echo "
        				<a href='../../gene_expressions/app_comparison_to_samples.php?ID={$ROWID}' class='btn btn-primary' target='_blank'><i class='fa fa-database'></i> Related Samples</a> &nbsp;";
				}
						
						
			echo '

        				<a href="comparison_gene_table.php?id=' . $ROWID . '"
                   class="btn btn-primary btn_toggle_gene_details"
        					 rowid="' . $ROWID . '"
                   target="_blank">
        					<i class="fa fa-leaf"></i> Show ' . $APP_MESSAGE['Genes'] . 
        				'</a> &nbsp;';
            } else {
              if (file_exists($BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/inhouse_comp_' . $ROWID . '/uploaded_file.txt')) {
                $type = 'txt';
              } else {
                $type = 'csv';
              }
              
							echo '
              <a href="../functional_enrichment/Human_GO_out_inhouse/inhouse_comp_' . $ROWID . '/uploaded_file.' . $type . '"
                 class="btn btn-primary" download>
                <i class="fa fa-download"></i>
                Uploaded Data
              </a> &nbsp;
              <a href="../functional_enrichment/Human_GO_out_inhouse/inhouse_comp_' . $ROWID . '/cleaned_data.csv"
                 class="btn btn-primary" download>
                <i class="fa fa-download"></i>
                Cleaned Data
              </a>';
            }
            echo '
    				<br /><br />



            <div class="row mx-0" id="div_enrichment"></div>

    				<div class="row mx-0" id="div_gsea"></div>

            <div class="row">
				<div class="col-md-12 col-12" id="chart_up_div" style="min-width:600px; height: 600px;"></div>
			</div>

			<hr/>
			
			<div class="row">
              <div class="col-md-12 col-12" id="chart_down_div" style="min-width:600px; height: 600px;"></div>
            </div>
			
            <hr />
            <br />';
    			}
    			if ($PAGE_TYPE == 'Gene') {
    				echo '
    				<div class="row mb-3">
    					<div class="col-md-4">
    						<strong>Gene Symbol: </strong>' . $data['GeneName'] . '
    					</div>
    					<div class="col-md-8">
    						<strong>Description: </strong>' . $data['Description'] . '
    					</div>
    				</div>';
					

					
					echo '
    				<a href="../../gene_expressions/app_gene_expression_rnaseq_single.php?GeneName=' . $data['GeneID'] . '" class="btn btn-primary"><i class="fa fa-line-chart"></i> View Gene Expression</a> &nbsp;
    				<a href="../bubble/index.php?id=' . $data['GeneID'] . '" class="btn btn-primary"><i class="fa fa-pie-chart"></i> Gene Bubble Plot</a> &nbsp;
    				<br /><br />';
    			}
    			if ($PAGE_TYPE == 'Project') {
					
					
					
    				echo "
    				<a href='../../gene_expressions/app_ajax.php?action=project_index_to_samples&projectIndex={$_GET['id']}' class='btn btn-primary'>
						<i class='fa fa-database'></i> View All Samples</a> 
						&nbsp;
						
    				<a href='../../gene_expressions/app_ajax.php?action=project_index_to_sample_list&projectIndex={$_GET['id']}' class='btn btn-primary'>
    					<i class='fa fa-floppy-o'></i> Save Project Samples</a>
						&nbsp;
    				
					<br />
					<br />";
    			}
				
				
				if ($PAGE_TYPE == 'Sample') {
					
					
					echo "<br/><br/>";
					
					
    			}
				
    		?>



    		<!-- Table of Columns -->

    		<div id="info_table_div">
    			<table class="table table-striped table-bordered">
            <tr>
              <th colspan="4" class="text-center bg-info"><? echo $PAGE_TYPE; ?> Details</th>
            </tr>
    			<?php
    				// Table Content
    				$i = 0;
    				foreach ($data as $key => $value) {
    					if ($i % 2 == 0) echo '<tr>';
    					echo '<td class="text-right"><strong>' . str_replace('_', ' ', $key) . '</strong></td>';
						
						
						if ($key == 'ProjectName'){
							$currentURL = "../../gene_expressions/app_review_record_by_name.php?currentSQL=ProjectName&targetCategory=Project&recordIndex={$_GET['id']}&value={$value}";
							
							echo "<td><a href='{$currentURL}'>{$value}</a></td>";
								
						} else {
	    					echo '<td>' . str_replace('|', ' ', str_replace(';', ';<br />', $value)) . '</td>';
						}
    					if ($i % 2 == 1) echo '</tr>';
    					$i++;
    				}
    			?>
    			</table>
    		</div>

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

  <?php if ($PAGE_TYPE != 'Comparison') echo "$('.loader').remove();"; ?>

  <?php if ($PAGE_TYPE == 'Comparison') { ?>
    // $('.loader').remove();
    // Functional Enrichment
  	$.ajax({
  		type: 'POST',
  		url: '../functional_enrichment/exe.php?action=show_chart',
  		data: {comparison_index: '<?php echo $ROWID; ?>', inhouse: '<?php echo $INHOUSE; ?>'},
  		success: function(responseText) {
        	$('#div_enrichment').html(responseText);
			$('.loader').hide();
  		}
  	});

    // GSEA
    $.ajax({
  		type: 'POST',
  		url: '../gsea/exe.php?action=show_chart',
  		data: {comparison_index: '<?php echo $ROWID; ?>', inhouse: '<?php echo $INHOUSE; ?>'},
  		success: function(response) {
        $('#btn_submit').removeAttr('disabled')
                        .children(':first')
                        .removeClass('fa-spin fa-spinner')
                        .addClass('fa-upload');
        var data_up = response.up.data;
        var layout_up = response.up.layout;
        var setting_up = response.up.setting;

        var data_down = response.down.data;
        var layout_down = response.down.layout;
        var setting_down = response.down.setting;

        Plotly.newPlot('chart_up_div', data_up, layout_up, setting_up);
        Plotly.newPlot('chart_down_div', data_down, layout_down, setting_down);

        // Click
        var graphDivUp = document.getElementById('chart_up_div');
        var graphDivDown = document.getElementById('chart_down_div');

        var clickEvent = function(data){
          var name = data.points[0].y; // Geneset Name
		  
		  var popup = '<p>Selected: <strong>' + name + '</strong></p>';
			  popup += '<a href="../volcano/index.php?table=PAGE_List&id=<?php echo $ROWID; ?>&geneset=' + name + '" target="_blank">&bull; View Selected <?php echo $APP_MESSAGE['Gene']; ?> Set in Volcano Plot</a>';
              bootbox.alert(popup);
		  
		  /*
          $.ajax({
            type: 'POST',
            url: '../gsea/exe.php?action=go_to_volcano',
            data: {geneset_name: name},
            success: function(response) {
              //$('#debug').html(response);

              var popup = '<p>Selected: <strong>' + name + '</strong></p>';
			  popup += '<a href="../volcano/index.php?table=PAGE_List&id=<?php echo $ROWID; ?>&geneset=' + name + '" target="_blank">&bull; View Selected <?php echo $APP_MESSAGE['Gene']; ?> Set in Volcano Plot</a>';
              bootbox.alert(popup);

            }
          });
		  */
        }
        graphDivUp.on('plotly_click', clickEvent);
        graphDivDown.on('plotly_click', clickEvent);

		var page_report_up    = '<a href="page_report.php?id=<?php echo $ROWID; ?>&direction=up&inhouse=<?php echo $_GET['inhouse']; ?>"';
page_report_up       += ' class="btn btn-info">PAGE Report for Upregulated <?php echo $APP_MESSAGE['Genes']; ?></a>';
		var page_report_down  = '<a href="page_report.php?id=<?php echo $ROWID; ?>&direction=down&inhouse=<?php echo $_GET['inhouse']; ?>"';
page_report_down     += ' class="btn btn-info">PAGE Report for Downregulated <?php echo $APP_MESSAGE['Genes']; ?></a>';

		$('#chart_up_div').prepend(page_report_up);
		$('#chart_down_div').prepend(page_report_down);

  		  return true;
  		}
  	});


  <?php } ?>


	

});


</script>




</body>
</html>
