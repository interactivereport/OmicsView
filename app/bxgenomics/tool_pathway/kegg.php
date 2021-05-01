<?php
include_once("config.php");



$analysis_files = '';
if (isset($_GET['analysis'])) {
    $current_analysis = trim($_GET['analysis']);
    $analysis_id = intval(array_shift(explode("_", $current_analysis)));

    $comparisons = array();
    if($analysis_id > 0 && isset($_GET['comp']) && $_GET['comp'] != ''){
        $comparisons = explode(",", trim($_GET['comp']));
    }
    else if($analysis_id > 0){
        $sql = "SELECT `Comparisons` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_ANALYSIS']}` WHERE `ID`=" . $analysis_id;
        $comparisons = unserialize($BXAF_MODULE_CONN -> get_one($sql));
    }
    foreach($comparisons as $c){
        $file = $BXAF_CONFIG['ANALYSIS_DIR'] . "{$current_analysis}/alignment/DEG/{$c}/DEG_Analysis/{$c}_DEG.csv";
        if(file_exists($file)) $analysis_files[$c] = bxaf_encrypt($BXAF_CONFIG['ANALYSIS_DIR'] . "{$current_analysis}/alignment/DEG/{$c}/DEG_Analysis/{$c}_DEG.csv", $BXAF_CONFIG['BXAF_KEY']);
    }
}


$comparison_ids = array();
if (isset($_GET['id']) && intval($_GET['id']) >= 0) {
    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ComparisonIndex` = ?i";
    $comparison_ids = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, intval($_GET['id']) );

    if(! is_array($comparison_ids) || count($comparison_ids) <= 0) $comparison_ids = array();

    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `App_User_Data_Comparisons` WHERE `ComparisonIndex` = ?i";
    $comparison_ids2 = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, intval($_GET['id']) );
    if(is_array($comparison_ids2) && count($comparison_ids2) > 0){
        $comparison_ids = array_merge($comparison_ids, $comparison_ids2);
    }
}
else if (isset($_GET['ComparisonIndex']) && intval($_GET['ComparisonIndex']) >= 0) {
    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ComparisonIndex` = ?i";
    $comparison_ids = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, intval($_GET['ComparisonIndex']) );

    if(! is_array($comparison_ids) || count($comparison_ids) <= 0) $comparison_ids = array();

    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `App_User_Data_Comparisons` WHERE `ComparisonIndex` = ?i";
    $comparison_ids2 = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, intval($_GET['ComparisonIndex']) );
    if(is_array($comparison_ids2) && count($comparison_ids2) > 0){
        $comparison_ids = array_merge($comparison_ids, $comparison_ids2);
    }
}
else if (isset($_GET['ids']) && trim($_GET['ids']) != '') {
    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ComparisonIndex` IN (?a)";
    $comparison_ids = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, explode(',', $_GET['ids']) );

    if(! is_array($comparison_ids) || count($comparison_ids) <= 0) $comparison_ids = array();

    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `App_User_Data_Comparisons` WHERE `ComparisonIndex` IN (?a)";
    $comparison_ids2 = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, explode(',', $_GET['ids']) );
    if(is_array($comparison_ids2) && count($comparison_ids2) > 0){
        $comparison_ids = array_merge($comparison_ids, $comparison_ids2);
    }

}
else if (isset($_GET['project_id']) && intval($_GET['project_id']) >= 0) {
    $sql = "SELECT `ProjectID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_PROJECTS']}` WHERE `ProjectIndex` = ?i";
    $project_name = $BXAF_MODULE_CONN -> get_one($sql, intval($_GET['project_id']) );

    if($project_name == ''){
        $sql = "SELECT `ProjectID_Original` FROM `App_User_Data_Projects` WHERE `ProjectIndex` = ?i";
        $project_name = $BXAF_MODULE_CONN -> get_one($sql, intval($_GET['project_id']) );
    }

    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}` WHERE `ProjectName` = ?s";
    $comparison_ids = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, $project_name );
    if(! is_array($comparison_ids) || count($comparison_ids) <= 0) $comparison_ids = array();

    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `App_User_Data_Comparisons` WHERE `ProjectName` = ?s";
    $comparison_ids2 = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, $project_name );

    if(is_array($comparison_ids2) && count($comparison_ids2) > 0){
        $comparison_ids = array_merge($comparison_ids, $comparison_ids2);
    }
}




// Public Comparisons
$sql = "SELECT `ComparisonIndex`, `ComparisonID` AS 'Name', `Case_CellType`, `Case_DiseaseState`, `ComparisonContrast` FROM `{$BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS']}`";
$comparison_info = $BXAF_MODULE_CONN -> get_all($sql);

// Internal Comparisons
$sql = "SELECT `ComparisonIndex`, `ComparisonID` AS 'Name', `Case_CellType`, `Case_DiseaseState`, `ComparisonContrast`, `ProjectIndex` FROM `App_User_Data_Comparisons` ORDER BY `ComparisonIndex`";
$internal_comparison_info = $BXAF_MODULE_CONN->get_all($sql);
$accessible_project_indexes = internal_data_get_accessible_project();
foreach ($internal_comparison_info as $values) {
    $project_index = $values['ProjectIndex'];
    if (is_array($accessible_project_indexes) && array_key_exists($project_index, $accessible_project_indexes) ){
        $comparison_info[] = $values;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

    <script type="text/javascript" src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js"> </script>

	<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.js'></script>

    <script type="text/javascript" language="javascript" src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/js/natural.js"></script>

</head>
<body>
<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">




      		<div class="container-fluid">

                <div class="d-flex">
                    <h3>KEGG Pathway Visualization</h3>
                    <div class="ml-5 mt-2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fas fa-sync"></i> Start Over</a></div>
                    <div class="ml-5 mt-2 text-muted">Note: <span class="text-danger">*</span> denotes required fields.</div>
                </div>

                <hr class="w-100 my-1" />

                <form class="my-3" id="form_show_pathway" enctype="multipart/form-data" method="post" style="max-width: 50rem;">

                    <div class="form-group">
                        <span class="text-muted" id="text_pathway_name">(No Pathway Selected)</span>
                        <a href="Javascript: void(0);" class="hidden ml-2" id="btn_download_pathway" target="_blank">
                          <i class="fas fa-download"></i> Download Pathway File
                        </a>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-outline-success btn-sm" type="button" id="btn_select_pathway_show_modal">
                          <i class="fas fa-angle-double-right"></i> Select Pathway
                        </button>
                        <input class="hidden" name="KEGG_Identifier" value="" id="input_pathway">
                    </div>


                    <div class="form-group">
                        <label class="font-weight-bold">Enter comparison names (<span class="text-danger">one per row</span>):</label>

                        <a class="ml-3 btn_saved_lists" href="javascript:void(0);" category="Comparison" data_target="Comparison_List"> <i class="fas fa-angle-double-right"></i> Load from saved lists </a>

                        <a class="ml-3" href="Javascript: void(0);" id="btn_select_comparison"> <i class="fas fa-search"></i> Select Comparisons </a>

                        <a class="ml-3" href="Javascript: void(0);" onclick="$('#Comparison_List').val('');"> <i class="fas fa-times"></i> Clear </a>


                        <textarea class="form-control" onBlur="$('#comparison_file').val('');" style="height:10rem;" name="Comparison_List" id="Comparison_List" category="Comparison"><?php echo implode("\n", $comparison_ids); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Or, upload your comparison files:</label>
                        <input type="file" class="" id="comparison_file" name="comparison_file" onchange="$('#Comparison_List').val('');">
                        <a href="kegg/demo_logfc.csv"> <i class="fas fa-angle-double-right"></i> Demo Data </a>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Visualization:</label>
                        <select class='custom-select' name='Visualization' id='Visualization'>
        					<option value='1'>Gradient Blue-White-Red (-1,0,1)</option>
        					<option value='2'>Gradient Blue-White-Red (-2,0,2)</option>
        					<option value='3'>Gradient Blue-White-Red (-3,0,3)</option>
        				</select>
                    </div>

                    <div class="w-100 form-check form-check-inline">
                        <button id="btn_submit" type='submit' class="btn btn-primary"> Submit </button>

                        <label class="form-check-label mx-2 hidden" id="btn_busy"><i class="fas fa-pulse fa-spinner"></i></label>

                    </div>
                </form>

                <div class="w-100 my-3">
                    <div id="div_results" class="w-100"></div>
                    <div id="div_debug" class="w-100 my-3"></div>
                </div>

            </div>


        </div>

        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
    </div>
</div>


<?php include_once('modals.php'); ?>



<!-------------------------------------------------------------------------------------------------------->
<!-- Modal to Select Pathway -->
<!-------------------------------------------------------------------------------------------------------->
<div class="modal" id="modal_select_pathway" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Select Pathway</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<?php
					echo '
					<table class="table table-bordered" id="table_select_pathway">
						<thead class="table-success">
						<tr>
                            <th>Code</th>
							<th>Pathway Name</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>';

                        foreach ($BXAF_CONFIG['KEGG_PATHWAY_LIST'] as $key => $value) {
							echo '
							<tr>
                                <td>' . $key . ' &nbsp;</td>
								<td>' . $value . ' &nbsp;</td>
								<td><a href="javascript:void(0);" class="btn_select_search_pathway" content="' . $key . '" displayed_name="' . $value . '"><i class="fas fa-angle-double-right"></i> Select</a></td>
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


<!----------------------------------------------------------------------------------------------------->
<!-- Modal to Select Comparison -->
<!----------------------------------------------------------------------------------------------------->
<div class="modal" id="modal_select_comparison" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Search Comparison</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <?php

          echo '
          <table class="table table-bordered table-striped table-hover w-100" id="table_select_comparison">
            <thead>
            <tr class="table-info">
              <th>Name</th>
              <th>DiseaseState</th>
              <th>ComparisonContrast</th>
              <th>CellType</th>
            </tr>
            </thead>
            <tbody>';

            foreach ($comparison_info as $comparison) {
              echo '
              <tr>
                <td class="text-nowrap">' . $comparison['Name'] . '
                  <a href="javascript:void(0);" class="btn_select_search_comparison ml-2" content="' . $comparison['Name'] . '"><i class="fas fa-angle-double-right"></i> Select</a>
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

    $(document).ready(function() {

        $('#table_select_pathway').DataTable();

        $(document).on('click', '#btn_select_pathway_show_modal', function() {
        	$('#modal_select_pathway').modal('show');
        });

        $(document).on('click', '.btn_select_search_pathway', function() {
            var content = $(this).attr('content');
            var displayed_name = $(this).attr('displayed_name');

            $('#text_pathway_name').text( displayed_name );
            $('#input_pathway').val(content);

            $('#modal_select_pathway').modal('hide');

            $('#btn_download_pathway').show().attr('href', 'kegg/xml_png/' + content + '.png');
        });

<?php
if (isset($_GET['KEGG']) && $_GET['KEGG'] != '') {
    foreach ($BXAF_CONFIG['KEGG_PATHWAY_LIST'] as $key => $value) {
        if($_GET['KEGG'] == $value){

            echo "$('#text_pathway_name').text( '$value' );\n";
            echo "$('#input_pathway').val( '$key' );\n";
            echo "$('#btn_download_pathway').show().attr('href', 'kegg/xml_png/{$key}.png');\n";

            break;
        }
    }
}

?>

        // Select Comparison
        $('#table_select_comparison').DataTable();

    	$(document).on('click', '#btn_select_comparison', function() {
    		$('#modal_select_comparison').modal('show');
    	});
    	$(document).on('click', '.btn_select_search_comparison', function() {
            var comparison_name = $(this).attr('content');
            $('#Comparison_List').val( comparison_name + "\n" + $('#Comparison_List').val() );
    		$('#modal_select_comparison').modal('hide');
    	});


        var options = {
            url: 'exe.php?action=show_kegg_diagram',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {
                $('#btn_busy').removeClass('hidden');
                return true;
            },
            success: function(response){
                $('#btn_busy').addClass('hidden');

                $('#div_results').html(response);

                $('#resultTable').DataTable({
                    "pageLength": 100,
                    "dom": 'Blfrtip',
                    "buttons": [ 'colvis','copy','csv','excel','pdf','print'],
                    "order": [ [0, 'desc'] ],
                    "columnDefs": [ { type: 'natural', targets: 0 } ]
                });

                return true;
            }
        };
        $('#form_show_pathway').ajaxForm(options);

    }); // End of $(document).ready(function() {

</script>

</body>
</html>