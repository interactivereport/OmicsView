<?php
include_once('config.php');
include_once('../profile/config.php');

$sessionKey = md5(microtime(true) . '_' . rand(0, 1000));
$PAGE['Category'] = 'Comparison Plotting Tools';
$PAGE['Barcode']  = 'Bubble Plot (Multiple Genes)';

// Bubble Plot From Meta Analysis
$META = false;
/*if (isset($_GET['meta']) && trim($_GET['meta']) == 'true') {
  $META = true;
  $meta_genenames = implode("\n", $_SESSION['META_BUBBLE_PLOT_GENENAMES']);
  $meta_compnames = implode("\n", $_SESSION['META_BUBBLE_PLOT_COMPNAMES']);
}
*/

if (general_array_size($_SESSION['Multiple_Bubble_Plot'][$_GET['Session']]) > 0){
	$META = true;	
	$meta_genenames = implode("\n", $_SESSION['Multiple_Bubble_Plot'][$_GET['Session']]['Genes']);
	$meta_compnames = implode("\n", $_SESSION['Multiple_Bubble_Plot'][$_GET['Session']]['Comparisons']);
}





?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">


<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>
<script type="text/javascript" src="../library/config.js"></script>
<script src="../library/plotly.min.js"></script>

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
                <h1 class="pt-3"><?php echo $APP_MESSAGE['Genes']; ?> &amp; Comparisons Bubble Plot</h1>
				<hr />
				<div class="row mx-0 p-l-1" id="first_form_div">
                
                	<?php if ($BXAF_CONFIG['APP_PROFILE'] == 'human'){ ?>
					<a href="javascript:void(0);" id="btn_load_example" ><i class="fa fa-fw fa-info-circle"></i> Load Example Data</a>
                    <?php } ?>
					<form id="form_bubble_plot" method="post" enctype="multipart/form-data">
						<div class="row mt-1">
                        
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
												echo genesets_api_get_header_code('', '', 'textarea_genes');
												echo genesets_api_get_body_code();
											echo "</td>";
										}
										
										echo "</tr>";
									echo "</table>";
								  
								  ?>    
                                </p>
								<textarea name="genes" class="form-control height-150" id="textarea_genes"><?php if ($META) echo $meta_genenames; ?></textarea>
              				</div>
              
                          <div class="col-md-6">
                                <p>
                                    <strong>Comparison IDs:</strong>
                                    
                                    <a href="javascript:void(0);" id="btn_show_comparison_list" type="comparison">
                                        <i class="fa fa-fw fa-shopping-cart"></i> Load Saved Comparison IDs
                                    </a>
                                </p>
                                <textarea name="comparisons" class="form-control height-150" id="textarea_comparisons"><?php if ($META) echo $meta_compnames; ?></textarea>
                          </div>
              
              
						  <?php
                          
                            if (has_internal_data()){
                            
                                $has_internal_data = true;
                                
                                echo "<div class='row'>";
                                    echo "<div class='col-12'>";
                                        echo "<div class='form-check col-sm-12'>";
                                        
                                            echo "<div style='margin-top:8px;'>";
                                                echo "<strong>Source of the Comparison IDs:</strong>";
                                            echo "</div>";
                                            
                                            echo "<div style='margin-left:15px;'>";
                                                echo internal_data_print_form_html();
                                            echo "</div>";
                                            
                                            echo internal_data_print_modal();
            
                                        echo "</div>";							
                                    echo "</div>";
                                echo "</div>";
                            
                            }
                            
                            
                            if (true){
                                
                                echo "<div class='row'>";
                                    echo "<div class='col-12'>";
                                        echo "<div class='form-group col-sm-12'>";
                                        
                                            echo "<div style='margin-top:8px;'>";
                                                echo "<strong>Chart Height Scale Factor:</strong>";
                                            echo "</div>";
                                            
                                            
                                            echo "<div style='margin-left:15px;'>";
                                                echo "<input type='number' min='0.5' max='3' step='0.1' value='1' class='form-control' style='width:100px;' id='input_height_factor' name='height_factor'/>";
                                            echo "</div>";
                                            
                                        echo "</div>";							
                                    echo "</div>";
                                echo "</div>";
                                
                            }
                            
                            
                            if (true){
                                
                                echo "<div class='row'>";
                                    echo "<div class='col-12'>";
                                        echo "<div class='form-group col-sm-12'>";
                                        
                                            echo "<div style='margin-top:8px;'>";
                                                echo "<strong>Chart Left Margin Scale Factor:</strong>";
                                            echo "</div>";
                                            
                                            
                                            echo "<div style='margin-left:15px;'>";
                                                echo "<input type='number' min='0.5' max='3' step='0.1' value='1' class='form-control' style='width:100px;' id='input_left_factor' name='left_factor'/>";
                                            echo "</div>";
                                            
                                        echo "</div>";							
                                    echo "</div>";
                                echo "</div>";
                                
                            }
                            
            
                            if (true){
                                
                                echo "<div class='row'>";
                                    echo "<div class='col-12'>";
                                        echo "<div class='form-check col-sm-12'>";
                                        
                                            echo "<div style='margin-top:8px;'>";
                                                echo "<strong>Display Columns:</strong>";
                                            echo "</div>";
                                            
                                            
                                            echo "<div style='margin-left:15px;'>";
                                            
                                                echo "<div class='form-check-inline'>";
                                                    echo "<label class='form-check-label' for='table_option_logfc'>";
                                                        echo "<input class='form-check-input' type='checkbox' name='table_option_logfc' id='table_option_logfc' checked/>";
                                                        echo "&nbsp;Log<sub>2</sub> Fold Change";
                                                    echo "</label>";
                                                echo "</div>";
                                                
                                                echo "<div class='form-check-inline'>";
                                                    echo "<label class='form-check-label' for='table_option_pval'>";
                                                        echo "<input class='form-check-input' type='checkbox' name='table_option_pval' id='table_option_pval' checked/>";
                                                        echo "&nbsp;p-value";
                                                    echo "</label>";
                                                echo "</div>";
                                                
                                                echo "<div class='form-check-inline'>";
                                                    echo "<label class='form-check-label' for='table_option_fdr'>";
                                                        echo "<input class='form-check-input' type='checkbox' name='table_option_fdr' id='table_option_fdr' checked/>";
                                                        echo "&nbsp;FDR";
                                                    echo "</label>";
                                                echo "</div>";
                                                
                                                
                                            echo "</div>";
                                            
                                        echo "</div>";							
                                    echo "</div>";
                                echo "</div>";
                                
                            }
                          
                          ?>

                          <button id="btn_submit" class="btn btn-primary ml-3 mt-1">
                            <i class="fa fa-fw fa-bar-chart" aria-hidden="hidden"></i> Plot
                          </button>
                          &nbsp;
                          <div id="container_btn_save_svg">
                            <button type="button" class="btn btn-warning mt-1 hidden" id="btn_save_svg" index="1">
                              <i class="fa fa-download"></i> Save SVG
                            </button>
                          </div>
                          &nbsp;
                          <button type="button" class="btn btn-info mt-1 hidden" id="btn_export_gene_comp">
                            <i class="fa fa-download"></i> Export Genes/Comparisons
                          </button>
                          
                          <input type="hidden" id="svgCode" value=""/>
                          <input type="hidden" id="pngCode" value=""/>
                          
                          <input type='hidden' name='sessionKey' value='<?php echo $sessionKey; ?>'/>
						</div>
					</form>

    			</div>
            
            
				<div id="feedbackSection" style="display:none; margin-top:10px;">
              		<hr/>
        		</div>

        		<div class="row mx-0 mt-4" id="chart_div"></div>
        
				<div class="row mx-0 mt-4" id="table_div"></div>

			</div>
		</div>
	    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>


<div class="modal fade" id="modal_geneset" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Please select a <?php echo $APP_MESSAGE['gene']; ?> set</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      
      <div class="modal-body"></div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->




<!--------------------------------------------------------------------------------------->
<!-- Select Gene List -->
<!--------------------------------------------------------------------------------------->
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


<!--------------------------------------------------------------------------------------->
<!-- Select Comparison List -->
<!--------------------------------------------------------------------------------------->
<div class="modal fade" id="modal_select_comparison_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        <h4 class="modal-title">Please select a comparison list you like to load:</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body" id="modal_select_comparison_list_body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="btn_select_comparison_list">Select</button>
			</div>
		</div>
	</div>
</div>



<script>



var domReady = function(callback) {
    document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};


domReady(function() {

	$('#sidebar_link_bubble').addClass('active');
	$('#sidebar_link_bubble').parent().parent().prev().addClass('active');
	$('#sidebar_link_bubble').parent().parent().css('display', 'block');
	$('#sidebar_link_bubble').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');



  // ***************************************************************************
  // Load Example
  // ***************************************************************************

  $(document).on('click', '#btn_load_example', function() {
    var default_genes         = 'DDX11L1\nWASH7P\nOR4F5\nFAM138F\nDQ597235\nDQ599768\nLOC100133331\n';
    default_genes            += 'OR4F29\nBC036251\nJA429830\nJA429831\nDQ575786\nAK310751\nLOC100288069\n';
    default_genes            += 'DQ582680\nCSPG4P1Y\nDQ571386\nDQ581594\nDQ597149\nGOLGA2P2Y\n';
    default_genes            += 'U6\nSPRY3\nVAMP7\nIL9R\nWASH1\nWASH6P\n';
    default_genes            += 'SMN1\nSMN2\nBC045789\nNAIP\nDQ570835\nSMA5\n';
    default_genes            += 'DQ587763\nGTF2H2B\nSMA3\nLOC441081\nGUSBP9\nGTF2H2\n';
    default_genes            += 'LOC647859\nZ70701\nDQ577092\nAK310013\nAK123868\nSNORD116-8\n';
    default_genes            += 'SNORD116-11\nIPW\nC15orf49\nPAR1\nSNORD116-29\nCUL2';

    var default_comparisons   = '<?php echo $CONFIG_PROFILE['BUBBLE_PLOT']['default_comparisons']; ?>';
    $('#textarea_genes').val(default_genes);
    $('#textarea_comparisons').val(default_comparisons);
	
	$('#form_bubble_plot').submit();
  });



  // ***************************************************************************
  // Use Gene Set
  // ***************************************************************************

  $(document).on('click', '#btn_select_gene_set', function() {
    var current = $(this);
    current.html('<i class="fa fa-spin fa-spinner"></i> Loading...');
    $.ajax({
      type    : 'POST',
      url     : 'exe.php?action=load_gene_set_modal',
      success : function(response) {
        $('.modal-body')     .html(response);
        $('.datatable')      .DataTable();
        $('#modal_geneset')  .modal();
        current.html('<i class="fa fa-angle-double-right"></i> Select Gene Set');
      }
    });
  });
  $(document).on('click', '.btn_select_one_gene_set', function() {
    var current   = $(this);
    var rowid     = current.attr('rowid');
    $('#modal_geneset').modal('hide');
    $.ajax({
      type    : 'POST',
      url     : 'exe.php?action=get_gene_set_detail',
      data    : {rowid: rowid},
      success : function(response) {
        $('#textarea_genes').val(response);
      }
    });
  });


  // ***************************************************************************
  // Load Gene List & Comparison List
	// ***************************************************************************
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
  $(document).on('click', '#btn_show_comparison_list', function () {
    $.ajax({
  		type: 'POST',
  		url: '<?php echo $BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS'] . 'app_list_ajax_selection.php?category=Comparison&input_name=comparisonList&input_class=comparisonList'; ?>',
  		success: function (response) {
        $('#modal_select_comparison_list_body').html(response);
        $('#modal_select_comparison_list').modal();
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
    $('#textarea_genes').val($('#gene_list_content_' + selected).val());
  });
  $(document).on('click', '#btn_select_comparison_list', function () {
    var selected = $('.geneList').val();
		$('.comparisonList').each(function(index, element) {
			if ($(element).is(':checked')) {
				selected = $(element).val();
			}
		});
    $('#textarea_comparisons').val($('#comparison_list_content_' + selected).val());
  });
  // Click to load, from Derrick
  $(document).on('click', '.geneList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		var radioID			= 'geneList_' + currentListID;
		$('#' + radioID).prop('checked', true);
		var content = $('#gene_list_content_' + currentListID).val();
		$('#textarea_genes').val(content);
	});
  $(document).on('change', '.geneList', function(){
		var currentListID = $(this).val();
		var content = $('#gene_list_content_' + currentListID).val();
		$('#textarea_genes').val(content);
	});
  $(document).on('click', '.comparisonList_Name', function(){
		var currentListID 	= $(this).attr('listid');
		var radioID			= 'comparisonList_' + currentListID;
		$('#' + radioID).prop('checked', true);
		var content = $('#comparison_list_content_' + currentListID).val();
		$('#textarea_comparisons').val(content);
	});
  $(document).on('change', '.comparisonList', function(){
		var currentListID = $(this).val();
		var content = $('#comparison_list_content_' + currentListID).val();
		$('#textarea_comparisons').val(content);
	});





  // ***************************************************************************
  // Generate Chart
  // ***************************************************************************
  var svg_index = $('#btn_save_svg').attr('index');
	var options = {
		url: 'exe.php?action=genes_comparisons_generate_chart',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
		$('#feedbackSection').hide();
		
			
		$('#chart_div').empty();
		$('#table_div').empty();
		$('#btn_save_svg').hide();
		$('#btn_export_gene_comp').hide();
      svg_index = parseInt(svg_index) + 1;
      $('#container_btn_save_svg')
        .html('<button type="button" class="btn btn-warning mt-1 hidden btn_save_svg_'+svg_index+'" id="btn_save_svg" index="'+svg_index+'"><i class="fa fa-download"></i> Save SVG</button>');
		
      $('#chart_div').empty();
	  
		$('#btn_submit')
      //  .prop('disabled', true)
        .children(':first')
        .removeClass('fa-chevron-circle-right')
        .addClass('fas fa-spin fa-spinner');
			return true;
		},
    success: function(response){
		$('#feedbackSection').show();
			$('#btn_submit')
        //.prop('disabled', false)
        .children(':first')
        .removeClass('fa-spin fa-spinner')
        .addClass('fa-chevron-circle-right');
			$('#btn_submit').removeAttr('disabled');
      //$('#chart_div').html(response);

	  var error				  = parseInt(response.error);
	  
	  if (error == 1){
		   $('#ResearchModalTrigger').hide();
		  $('#chart_div').html(response.message);
		  
	  } else {
		  
		  $('#ResearchModalTrigger').show();
		  $('#ResearchProjectSaved').hide();
		  
          var data                = response.data;
		  var layout              = response.layout;
		  var settings            = response.settings;
		  var gene_num            = response.Number.gene.length;
		  var comparison_num      = response.Number.comparison.length;
    

		  $('#btn_save_svg, #btn_export_gene_comp').show();
		  Plotly
			.newPlot('chart_div', data, layout, settings)
			.then(function(gd){
				Plotly.toImage(gd, {
								format: 'svg', 
								width: 1600, 
								height: layout.height
								}).then(function(dataUrl) {
									$('#svgCode').val(dataUrl);
								});
								
				Plotly.toImage(gd, {
								format: 'png', 
								width: 1600, 
								height: layout.height
								}).then(function(dataUrl) {
									$('#pngCode').val(dataUrl);
								});
								
				  $(document).on('click', '.btn_save_svg_' + svg_index, function() {
					Plotly
					  .downloadImage(gd, {
									filename: 'bubblePlot',
									format:'svg',
									height: layout.height,
									width: 1600
								})
					  .then(function(filename){
						  console.log(filename);
					  });
				  });
			  $('.loader').remove();
			});
	
	
	
	
		  var chartDiv            = document.getElementById('chart_div');
	
		  chartDiv.on('plotly_click', function(data){
				var gene              = data.points[0].data.marker.gene[data.points[0].pointNumber];
				var gene_name         = data.points[0].data.marker.gene_name[data.points[0].pointNumber];
				var comparison        = data.points[0].data.marker.comparison[data.points[0].pointNumber];
			var comparison_name   = data.points[0].data.marker.comparison_name[data.points[0].pointNumber];
			var content           = '<h4>Marker Information</h4><hr />';
			content              += 'Gene: ';
			content              += '<a href="../search_comparison/single_comparison.php?type=gene&id=' + gene + '" target="_blank">';
			content              += gene_name;
			content              += '</a><br />';
			content              += 'Comparison: ';
			content              += '<a href="../search_comparison/single_comparison.php?type=comparison&id=' + comparison + '" target="_blank">';
			content              += comparison_name;
			content              += '</a><br />';
				//console.log(data.points[0]);
				bootbox.alert(content);
			});
	
		  var num_content         = '<div class="alert alert-warning mt-1 mb-3 animated fadeIn">';
		  num_content            += '# of Genes Appeared: ' + gene_num + '<br/>';
		  num_content            += '# of Comparisons Appeared: ' + comparison_num + '<br/>';
				num_content            += '<a href="download.php?sessionKey=<?php echo $sessionKey; ?>" target="_blank">';
				num_content            += '<i class="fa fa-fw fa-download"></i> Download Data</a>';
		  num_content            += '</div>';
		  $('#chart_div').prepend(num_content);
	
		  $('#table_div').html(response.table);
		  $('#datatable_' + response.time).DataTable({
			"lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
			dom: 'lBfrtip',
			buttons: ['csv', 'excel', 'pdf']
		  });
	  }

			return true;
		}
    };
	$('#form_bubble_plot').ajaxForm(options);


  // ***************************************************************************
  // Go To Export Genes/Comparisons Page
  // ***************************************************************************
  $(document).on('click', '#btn_export_gene_comp', function() {
    var vm = $(this);

    var genes = $('#textarea_genes').val();
    var comps = $('#textarea_comparisons').val();
    $.ajax({
      type: 'POST',
      url: 'exe.php?action=go_to_export_tool',
      data: {genes: genes, comps: comps},
      success: function(response) {
        // alert(response);
        window.open('<?php echo $BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS']; ?>app_export_genes_comparisons.php?src=meta');
      }
    });


  });
  
    
	
	<?php if ($has_internal_data){ ?>
	$('#data_source_private').change(function(){
		updatePrivateSection();
	});
	
	$('.data_source_private_project_indexes').change(function(){
		updatePrivateProject();
	});
	
	updatePrivateProject();
	updatePrivateSection();
	<?php } ?>
});


<?php if ($has_internal_data){ ?>

function updatePrivateProject(){
	var selectedCount = 0;
	
	$('.data_source_private_project_indexes').each(function() {
		if ($(this).prop('checked')){
			selectedCount++;
		}
	});
	
	selectedCount = parseInt(selectedCount);
	
	$('#data_source_private_selected_count').html(selectedCount);
	
}

function updatePrivateSection(){
	var isChecked = $('#data_source_private').prop('checked');
		
	if (isChecked){
		$('#data_source_private_section').show();
		
	} else {
		$('#data_source_private_section').hide();
	}
}

<?php } ?>

</script>




</body>
</html>
