<?php
include_once('../assets/config.php');

$dir = $BXAF_CONFIG['USER_FILES_DASHBOARD'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];

if (general_array_size($_SESSION['ComparisonIDs'][$_GET['Session']]) > 0){
	$COMPARISON_NAMES = $_SESSION['ComparisonIDs'][$_GET['Session']];
	$COMPARISON_NAMES = implode("\n", $COMPARISON_NAMES);
} elseif (isset($_GET['ComparisonIndex']) && is_numeric($_GET['ComparisonIndex'])){
	$comparison = get_one_record_by_index('comparison', $_GET['ComparisonIndex']);
	
	
	$COMPARISON_NAMES = $comparison['ComparisonID'];
	
} else {
	$content = file_get_contents($dir . '/comparisons.txt');
	$COMPARISON_NAMES = explode("|", $content);	
	$COMPARISON_NAMES = implode("\n", $COMPARISON_NAMES);
}


function getBiotype(){
	
	global $DB;
	
	$SQL = "SELECT `Biotype`, count(*) as `X` FROM `GeneCombined` group by `Biotype` ORDER BY  `X` DESC, `Biotype` ASC";
	$biotype = $DB->GetAssoc($SQL);
	
	
	$results['Highlighted']['BXAPP_ALL'] = '';
	$results['By Occurence']['BXAPP_ALL'] = '';
	$results['By Name']['BXAPP_ALL'] = '';
	
	$results['Highlighted']['BXAPP_ALL'] = $results['By Occurence']['BXAPP_ALL'] = $results['By Name']['BXAPP_ALL'] = 'Any Value (' . number_format(array_sum($biotype)) . ')';
	
	foreach($biotype as $tempKey => $count){
		if ($tempKey == ''){
			//s$results['Highlighted']['BXAPP_None'] = $results['By Occurence']['BXAPP_None'] = $results['By Name']['BXAPP_None'] = 'None/Blank (' . number_format($count) . ')'; 
		} else {
			
			if ($tempKey == 'protein_coding'){
				$results['Highlighted'][$tempKey] = $tempKey . ' (' . number_format($count) . ')';
			}
			
			$results['By Occurence'][$tempKey] = $tempKey . ' (' . number_format($count) . ')';
		}
	}
	
	ksort($biotype);
	
	foreach($biotype as $tempKey => $count){
		if ($tempKey != ''){
			$results['By Name'][$tempKey] = $tempKey . ' (' . number_format($count) . ')';
		}
	}
	
		
	return $results;
	
	
}
$biotype = getBiotype();




?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<?php

echo '
<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">


<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>
<script type="text/javascript" src="../library/config.js"></script>

<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>
';

?>

</head>

<style>
.form-text{
	color:#999;	
}

</style>

<body>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

  <div id="bxaf_page_content" class="row no-gutters h-100">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

      <div id="bxaf_page_right_content" class="w-100 p-2">

    		<div class="container-fluid">
    		<h1 class="pt-3">
    			<?php echo $APP_MESSAGE['Significantly Changed Genes']; ?>
    		</h1>
        <hr />
    		<div class="row mx-0 pl-3" id="first_form_div">

    			<form id="form_changed_genes" method="post" enctype="multipart/form-data">


            <div class="row">
              <div class="col-2">
                <p>
                  <strong>Comparison IDs:</strong><br />
                  <a href="javascript:void(0);" id="btn_show_comparison_list" type="comparison">
    								<i class="fa fa-fw fa-shopping-cart"></i> Load Saved Comparison IDs
    							</a>
                </p>
              </div>
              <div class="col-10">
                <textarea name="comparisons"
                          class="form-control height-150 width-max-400"
                          id="textarea_comparisons"><?php echo $COMPARISON_NAMES; ?>
                </textarea>
              </div>
            </div>

			<br/>
			<div class='row'>
            	<h5>Log<sub>2</sub>Fold Change and Statistical Value Filter</h5>
            </div>
            <hr/>
		
            <div class="row mt-3">
            	<div class="col-12">
                    <p class='form-text'>This filter is always enabled.</p>
                </div>
            
            
              <div class="col-2">
                <p><strong>Direction:</strong></p>
              </div>
              <div class="col-10">
                <label class="mr-3">
                  <input type="radio" name="direction" value="up" checked>
                  Upregulated
                </label>
                <label class="mr-3">
                  <input type="radio" name="direction" value="down">
                  Downregulated
                </label>
                <label class="mr-3">
                  <input type="radio" name="direction" value="both">
                  Both
                </label>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-2">
                <p><strong>Log<sub>2</sub>(Fold Change) Cutoff:</strong></p>
              </div>
              <div class="col-10">
                <input class="form-control width-max-100" name="logfc_cutoff" value="1">
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-2">
                <p><strong>Statistical Value and Cutoff:</strong></p>
              </div>
              <div class="col-10 form-inline">
                <label class="mr-3">
                  <input type="radio" class="cutoff_category" name="cutoff_category" id="cutoff_category_pvalue" value="PValue">
                  &nbsp;p-value
                </label>
                <label class="mr-3">
                  <input type="radio" class="cutoff_category" name="cutoff_category" id="cutoff_category_fdr" value="AdjustedPValue" checked>
                  &nbsp;FDR
                </label>
                
                <input class="form-control" name="cutoff_value" id="cutoff_value" value="0.05">
              </div>
            </div>
            
            
			<br/>
			<div class='row'>
            	<h5>Expression Level Filter</h5>
            </div>
            <hr/>
            
			<div class="row mt-3">
                <div class="col-12">
                    <p class='form-text'>This filter will be enabled if the cutoff is larger than zero.</p>
                </div>
            
              <div class="col-2">
                <p><strong>Expression Level Definition and Cutoff:</strong></p>
              </div>
              <div class="col-10 form-inline">
                <label class="mr-3">
                  <input type="radio" name="base_mean" value="case" checked>
                  &nbsp;Average <?php echo $APP_MESSAGE['RPKM/TPM']; ?> of Case Samples
                </label>
                <label class="mr-3">
                  <input type="radio" name="base_mean" value="control">
                  &nbsp;Average <?php echo $APP_MESSAGE['RPKM/TPM']; ?> of Control Samples
                </label>
                <label class="mr-3">
                  <input type="radio" name="base_mean" value="min">
                  &nbsp;Min of Two
                </label>
                <label class="mr-3">
                  <input type="radio" name="base_mean" value="max">
                  &nbsp;Max of Two
                </label>
                
                <input class="form-control" name="base_mean_cutoff_value" id="base_mean_cutoff_value" value="0">
              </div>
            </div>
            
            
            
            
            
            <br/>
			<div class='row'>
            	<h5>Gene Annotation Filter</h5>
                
            </div>
            <hr/>
            
            <div class="row mt-2">
            	<div class="col-12">
                    <p class='form-text'>This filter will be enabled if a biotype value is selected (anything other than "Any Value").</p>
                </div>
            
              <div class="col-2">
                <p><strong>Biotype:</strong></p>
              </div>
              <div class="col-2">
                <select class="form-control" name="biotype">
                
                	
                	<?php
						foreach($biotype as $currentGroup => $currentMembers){
							
							echo "<optgroup label='{$currentGroup}'>";
							foreach($currentMembers as $tempKey => $tempValue){
								echo "<option value='{$tempKey}'>{$tempValue}</option>";	
							}
							echo "</optgroup>";
							
						}
					?>
                    
                </select>
              </div>
            </div>
            
            
            <br/>
			<div class='row'>
            	<h5>Comparison Method</h5>
                
            </div>
            <hr/>
            
            <div class="row mt-3">
              <div class="col-2">
                <p><strong>Filter Method with Multiple Comparisons:</strong></p>
              </div>
              <div class="col-10 form-inline">
                <label class="mr-3">
                  <input type="radio" name="comparison_method" value="and" checked>
                  &nbsp;All Comparisons Pass All Filters
                </label>
                <label class="mr-3">
                  <input type="radio" name="comparison_method" value="or" >
                  &nbsp;At Least One Comparison Passes All Filters
                </label>
              </div>
            </div>
            

           

            <div class="row mt-2">
              <div class="col-2">&nbsp;</div>
              <div class="col-10" id="cutoff_value_container">
                <button id="btn_submit" class="btn btn-primary mt-3 width-100">
                  <i class="fa fa-chevron-circle-right"></i> Submit
                </button>
              </div>
            </div>




    			</form>

    		</div>




        <div class="row mx-0 mt-4" id="chart_div"></div>
    		<div class="row mx-0 mt-4" id="table_div"></div>

      </div>
      </div>
      <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
    </div>
  </div>




<div class="modal fade" id="modal_select_comparison_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        <h4 class="modal-title">Select Comparison List</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body" id="modal_select_comparison_list_body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary"
                data-dismiss="modal" id="btn_select_comparison_list">
          Select
        </button>
			</div>
		</div>
	</div>
</div>




<script type="text/javascript">

$(document).ready(function(){

	$(document).on('change', '.cutoff_category', function() {
		if ($('#cutoff_category_pvalue').is(":checked")) {
			$('#cutoff_value').val('0.01');
		} else {
			$('#cutoff_value').val('0.05');
		}
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
  
	$(document).on('click', '#btn_select_comparison_list', function () {
		var selected = $('.comparisonList').val();
		$('.comparisonList').each(function(index, element) {
			if ($(element).is(':checked')) {
				selected = $(element).val();
			}
		});
		$('#textarea_comparisons').val($('#comparison_list_content_' + selected).val());
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





	var options = {
		url: 'exe.php?action=show_changed_genes_table',
  		type: 'post',
    	beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit').prop('disabled', true).children(':first').removeClass('fa-chevron-circle-right').addClass('fa-spin fa-spinner');
			$('#table_div').empty();
			return true;
		},
    	success: function(response){
			$('#btn_submit').prop('disabled', false).children(':first').removeClass('fa-spin fa-spinner').addClass('fa-chevron-circle-right');
			
			if (response.substring(0, 5) == 'Error') {
				
				bootbox.alert(response);
				
			} else {
				
				$('#table_div').html(response);
				$('#table').DataTable({
					dom: 'Bfrtip',
					buttons: [
						'copy', 'csv', 'excel', 'pdf', 'print'
					]
				});
				
				if ($('#all_genes').val() != ''){
					$('#btn_save_genes').show();
				}
			}
			return true;
		}
    };
	$('#form_changed_genes').ajaxForm(options);


});


</script>




</body>
</html>
