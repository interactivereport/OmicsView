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

if (isset($_GET['pathway']) && $_GET['pathway'] != ''){
	$similar_pathways = find_similar_pathways($_GET['pathway'], $_GET['type']);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

    <link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.css' rel='stylesheet' type='text/css'>
	<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.js'></script>

    <script type="text/javascript" src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js"> </script>

</head>
<body>
    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
    <div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
    	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
    	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
    		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">



          		<div class="container-fluid">

                    <div class="d-flex">
                        <h3>WikiPathway Visualization</h3>
                        <div class="ml-5 mt-2"><a href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fas fa-sync"></i> Start Over</a></div>
                        <div class="ml-5 mt-2 text-muted">Note: <span class="text-danger">*</span> denotes required fields.</div>
                    </div>
                    <hr class="w-100 my-1" />


                    <div class="mt-3 w-100">
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
                        </div>
                    </div>


                    <div class="form-group" id="div_select_comparisons">
                        <label class=""><span class="font-weight-bold">Enter comparison names</span> ( <span class="text-danger">one per row</span> )</label>

                        <a class="ml-3 btn_saved_lists" href="javascript:void(0);" category="Comparison" data_target="Comparison_List"> <i class="fas fa-angle-double-right"></i> Load from saved lists </a>

                        <a class="ml-3" href="Javascript: void(0);" id="btn_search_comparison"> <i class="fas fa-search"></i> Select Comparisons </a>

                        <a class="ml-3" href="Javascript: void(0);" onclick="$('#Comparison_List').val('');"> <i class="fas fa-times"></i> Clear </a>


                        <textarea class="form-control w-50" onBlur="$('#comparison_file').val('');" style="height:10rem;" name="Comparison_List" id="Comparison_List" category="Comparison"><?php echo implode("\n", $comparison_ids); ?></textarea>

                        <div class="w-100 my-3" id="div_process_comparison_list">
                            <a class="btn btn-outline-success btn-sm" href="Javascript: void(0);" id="btn_upload_comparison_list"> <i class="fas fa-search"></i> Process Above Comparison List </a>
                        </div>
                    </div>

                    <div class="w-100" id="div_form_upload_file">
                        <form class="form-inline" id="form_upload_file" enctype="multipart/form-data">
                            <label class="font-weight-bold">Upload your comparison files:</label>
                            <input id="input_upload_file" type="file" class="mx-2 form-control" name="file" style="" onchange="$('#form_upload_file').submit();">
                            <span id="form_upload_file_busy" class="text-danger mx-2 hidden"><i class="fas fa-spinner fa-spin mr-2"></i> Uploading file ... </span>
                            <small class="form-text text-muted">You can keep uploading multiple files.</small>
                        </form>

                    </div>


                    <form class="my-2" id="form_pathway_chart" method="post">

                        <input class="hidden" name="pathway" value="" id="input_pathway">

                        <div class="w-100 my-3" id="all_comparison_contents"></div>

                        <div class="w-100 form-check form-check-inline">
                            <label class="form-check-label mx-2 hidden" id="btn_busy"><i class="fas fa-pulse fa-spinner"></i></label>

                            <button id="btn_submit" type='submit' class="btn btn-primary"> Submit </button>

                            <input type="checkbox" class="form-check-input mx-2" id="btn_use_demo_file" />
                            <label class="form-check-label"> Use Demo File</label>

                            <label class="form-check-label mx-2"> ( <a href="files/demo.csv"> <i class="fas fa-download"></i> Demo File 1 </a> <a href="files/demo1.csv"> <i class="fas fa-download"></i> Demo File 2 </a>) </label>

                            <input type="checkbox" class="form-check-input mx-2" id="show_svg_legend" name="show_svg_legend" value="on" />
                            <label class="form-check-label"> Include Legend in SVG</label>
                        </div>
                    </form>


                    <div class="w-100 hidden" style='width:100%; height:1200px;'>
                        <iframe id="results_pathway_diagram" src='' frameborder='0' allowfullscreen style='width:100%; height:100%;'></iframe>
                	</div>

                    <div class="w-100 my-3">
                        <div id="div_results" class="w-100"></div>
                        <div id="div_debug" class="w-100 my-3"></div>
                    </div>

                </div>


            </div>

            <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
        </div>
    </div>





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
					<table class="table table-bordered table-striped table-hover w-100" id="table_select_pathway">
						<thead>
						<tr class="table-info">
                            <th>Species</th>
							<th>Pathway Name</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>';

                        foreach ($BXAF_CONFIG['PATHWAY_LIST'] as $key => $value) {
                            $species = preg_match("/^Hs_/", $key) ? 'Human' : (preg_match("/^mmu_/", $key) ? 'Mouse' : (preg_match("/^Rn_/", $key) ? 'Rat' : "" ) );
							echo '
							<tr>
                                <td>' . $species . ' &nbsp;</td>
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



<?php if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){ ?>
<!----------------------------------------------------------------------------------------------------->
<!-- Modal to Similar Pathway -->
<!----------------------------------------------------------------------------------------------------->
<div class="modal" id="modal_similar_pathways" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Please select a pathway</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
      	<p>The exact pathway is not found. Please try selecting a similar pathway below:</p>

      	<ul>
        <?php
			foreach($similar_pathways as $key => $value){
				echo "<li><a href='javascript:void(0);' class='btn_select_similar_pathway' content='{$key}' displayed_name='{$value}'>{$value}</a></li>";
			}
        ?>
        </ul>
        <p>If the pathway is not in the list, the likely cause is that some reactome pathways were not converted to wikipathway GPML format.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>






<?php include("modals.php"); ?>



<script>

    $(document).ready(function() {

        //---------------------------------------------------------------------------------
        // Select Pathway
        //---------------------------------------------------------------------------------
        $('#table_select_pathway').DataTable();

        $(document).on('click', '#btn_select_pathway_show_modal', function() {
        	$('#modal_select_pathway').modal('show');
        });

		<?php if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){ ?>
		$(document).on('click', '.btn_select_similar_pathway', function() {

			$('#modal_similar_pathways').modal('hide');

            var content = $(this).attr('content');
            var displayed_name = $(this).attr('displayed_name');

            var folder = '';
            if (content.substring(0, 3) == 'Hs_') {
                if (displayed_name.substring(displayed_name.length - 10) == '(Reactome)')  folder = 'homo_sapiens_reactome';
                else folder = 'homo_sapiens';
            }
            else if (content.substring(0, 3) == 'Mm_') {
                folder = 'mus_musculus';
            }

            $('#text_pathway_name').text(displayed_name);

            $('#input_pathway').val(content);

            $('#modal_select_pathway').modal('hide');

            $('#btn_download_pathway').show().attr('href', 'pathway/' + folder + '/' + content);

			$('#form_pathway_chart').submit();
        });
		<?php } ?>

        $(document).on('click', '.btn_select_search_pathway', function() {

			<?php
			if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){
				echo "$('#modal_similar_pathways').modal('hide');";
			}
			?>

            var content = $(this).attr('content');
            var displayed_name = $(this).attr('displayed_name');

            var folder = '';
            if (content.substring(0, 3) == 'Hs_') {
                if (displayed_name.substring(displayed_name.length - 10) == '(Reactome)')  folder = 'homo_sapiens_reactome';
                else folder = 'homo_sapiens';
            }
            else if (content.substring(0, 3) == 'Mm_') {
                folder = 'mus_musculus';
            }

            $('#text_pathway_name').text(displayed_name);

            $('#input_pathway').val(content);

            $('#modal_select_pathway').modal('hide');

            $('#btn_download_pathway').show().attr('href', 'pathway/' + folder + '/' + content);
        });

        <?php
			//$_GET['pathway'] is exact match
			if ((sizeof($similar_pathways) == 1) && (is_array($similar_pathways))){

				$key 	= array_keys($similar_pathways);
				$key 	= $key[0];

				$value 	= array_values($similar_pathways);
				$value	= $value[0];

				echo "var content = '$key';
                    var displayed_name = '$value';

                    var folder = '';
                    if (content.substring(0, 3) == 'Hs_') {
                        if (displayed_name.substring(displayed_name.length - 10) == '(Reactome)')  folder = 'homo_sapiens_reactome';
                        else folder = 'homo_sapiens';
                    }
                    else if (content.substring(0, 3) == 'Mm_') {
                        folder = 'mus_musculus';
                    }

                    $('#text_pathway_name').text(displayed_name);

                    $('#input_pathway').val(content);

                    $('#modal_select_pathway').modal('hide');

                    $('#btn_download_pathway').show().attr('href', 'pathway/' + folder + '/' + content);";
			}



			if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){
				echo "$('#modal_similar_pathways').modal('show');";
			}
        ?>



        // Select Comparison
        $('#table_select_comparison').DataTable();

    	$(document).on('click', '#btn_search_comparison', function() {
    		$('#modal_select_comparison').modal('show');
    	});
    	$(document).on('click', '.btn_select_search_comparison', function() {
    		var comparison_name = $(this).attr('content');
            $('#Comparison_List').val( comparison_name + "\n" + $('#Comparison_List').val() );
    		$('#modal_select_comparison').modal('hide');
    	});




        $(document).on('click', '#btn_upload_comparison_list', function() {

            if ($.trim( $('#Comparison_List').val() ) == '') {
                bootbox.alert("Please enter some comparison names in the text box above.");
                return false;
            }

            var all_comparison_names = [];
            $('.all_comparisons').each(function(){
                var id = $(this).attr('id');
                var name_id = id.replace("comparison_", "comparisonname_");
                all_comparison_names.push( $('#' + name_id).val() );
            });

            var lines = $('#Comparison_List').val().split("\n");

            var line = '';
            var comparison_names = [];
            var comparison_name = '';

            for (var i=0; i < lines.length; i++) {
                line = $.trim(lines[i]);
                if (line != '') {
                    var comparison_names = line.split(" ");
                    for (var j=0; j < comparison_names.length; j++) {
                        comparison_name = $.trim( comparison_names[j] );
                        if (comparison_name != '' && jQuery.inArray( comparison_name, all_comparison_names ) == '-1' ) {
                             $('#all_comparison_contents').append( add_comparison_section(comparison_name) );
                        }
                    }
                }
            }

            $('#Comparison_List').val('');

    	});




    <?php

        if(is_array($analysis_files) && count($analysis_files) > 0){
            foreach($analysis_files as $analysis_file){
                ?>

                $.ajax({
                    type: 'POST',
                    url: 'exe.php?action=upload_file',
                    data: {
                        'analysis_file': '<?php echo $analysis_file; ?>'
                    },
                    success: function(response) {
                        var type = response.type;
                        if (type == 'Error') {
                            bootbox.alert(response.detail);
                        }
                        else {
                            $('#all_comparison_contents').append(add_file_section(response));
                        }
                        return true;
                    }
                });

                <?php
            } // foreach($analysis_files as $analysis_file){
        } // if(is_array($analysis_files) && count($analysis_files) > 0){
    ?>

        // Use Demo File
        $(document).on('change', '#btn_use_demo_file', function() {

            if ($(this).is(':checked')) {
                // clear all comparisons
                $('#all_comparison_contents').html('');

                var pathway_file = 'Hs_Abacavir_transport_and_metabolism_WP2712_86664.gpml';
                var pathway_name = '(Human) Abacavir transport and metabolism (Reactome)';

                var pathway_folder = 'homo_sapiens_reactome';

                $('#text_pathway_name').text(pathway_name).removeClass('text-muted');
                $('#input_pathway').val(pathway_file);

                $('#btn_download_pathway').show().attr('href', 'pathway/' + pathway_folder + '/' + pathway_file);

                $('#form_upload_file').submit();

                $('#div_form_upload_file').addClass('hidden');
                $('#div_select_comparisons').addClass('hidden');

            }
            else {
                $('#all_comparison_contents').html('');

                $('#div_form_upload_file').removeClass('hidden');
                $('#div_select_comparisons').removeClass('hidden');

                $('#text_pathway_name').text('(No Pathway Selected)').addClass('text-muted');
                $('#input_pathway').val('');
                $('#btn_download_pathway').hide().attr('href', '');
            }

        });


        // Upload file
        var options = {
            url: 'exe.php?action=upload_file',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {
                $('#form_upload_file_busy').removeClass('hidden');
                return true;
            },
            success: function(response){
                $('#form_upload_file_busy').addClass('hidden');

                $('#input_upload_file').val('');

                var type = response.type;
                if (type == 'Error') {
                    bootbox.alert(response.detail);
                }
                else {
                    $('#all_comparison_contents').append(add_file_section(response));
                }
                return true;
            }
        };
        $('#form_upload_file').ajaxForm(options);



        // Show options after file uploading
        $(document).on('click', '.btn_add_file_comparison', function() {
            var time = $(this).attr('row_id');
            var header = $(this).attr('row_header').split(",");
            $('#file_' + time).append( add_file_comparison(time, header) );
        });
        $(document).on('click', '.btn_delete_file_comparison', function() {
            var time = $(this).attr('row_id');
            var last_comparison_number = $('.comparison_' + time).length;
            $('#comparison_' + time + '_' + last_comparison_number).parent().hide();
            $('#comparison_' + time + '_' + last_comparison_number).parent().html('');
        });

        // Generate Pathway Chart
        var options_generate_pathway_chart = {
            url: 'exe.php?action=generate_pathway_chart',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {
                $('#btn_submit').attr('disabled', '').children(':first').removeClass('fa-upload').addClass('fa-spin fa-spinner');

                $('#div_results').html('');
                $('#div_debug').html('');

                $('#results_pathway_diagram').parent().addClass('hidden');
                $('#results_pathway_diagram').attr('src', '');

                return true;
            },
            success: function(response){
                $('#btn_submit').removeAttr('disabled').children(':first').addClass('fa-upload').removeClass('fa-spin fa-spinner');

                if (response.type == 'Error') {
                    bootbox.alert(response.detail);
                } else {

                    $('#div_results').html(response.datatable);
                    $('#div_debug').html(response.raw);

                    $('#results_pathway_diagram').parent().removeClass('hidden');
                    $('#results_pathway_diagram').attr('src', '<?php //echo $BXAF_CONFIG['CURRENT_SYSTEM_URL']; ?>single_svg.php?time=' + response.time);

                }

                return true;
            }
        };
    	$('#form_pathway_chart').ajaxForm(options_generate_pathway_chart);



        // Auto-process comparisons and submit form
        if( $('#Comparison_List').val() != '' ){

            $('#btn_upload_comparison_list').click();

            if($('#input_pathway').val() != ''){
                $('#form_pathway_chart').submit();
            }
        }

    }); // End of $(document).ready(function() {



    //-----------------------------------------------------------------------------------------------
    // Content After Uploaded
    function add_comparison_section(comparison_name) {

        var url = "../tool_search/view.php?type=comparison&name=" + comparison_name;
        var time = Math.floor((Math.random() * 1000000) + 1);

        var comparison_number = 1 + $('.comparison_' + time).length;
        var unique_id = time + '_' + comparison_number;
        var all_comparison_number = 1 + $('.all_comparisons').length;


        var content = '<div class="my-3 table-light rounded border border-primary p-3" id="file_' + time + '" style="max-width: 1500px;">';

            content += '<div class="text-right">';
                content += '<a class="ml-auto mr-2" title="Delete this file" href="Javascript: void(0);" onClick="$(\'#file_' + time + '\').hide(); $(\'#file_' + time + '\').html(\'\');" ><i class="fas fa-times text-danger"></i></a>';
            content += '</div>';

            content += '<div class="w-100 mt-1"><div style="background-color: #F1F1F1;" class="rounded border border-success px-3 pt-1 all_comparisons comparison_' + time + '" id="comparison_' + unique_id + '">';

                content += '<div class="form-row"><input type="hidden" id="comparisonname_' + unique_id + '" name="comparisonname_' + unique_id + '" value="' + comparison_name + '">';

                    content += '<div class="form-group col-md-4">';
                        content += '<label class="text-muted"><span class="text-danger">*</span> Comparison Name: </label>';
                        content += '<h3><a target="_blank" href="' + url + '">' + comparison_name + '</a></h3>';
                    content += '</div>';

                    content += '<div class="form-group col-md-4">';
                        content += '<label class="text-muted"><span class="text-danger">*</span> Coloring of logFC: </label>';
                        content += '    <select name="color1_' + unique_id + '" class="custom-select">';
                        content += '      <option value="0">Gradient Blue-White-Red (-1,0,1)</option>';
                        content += '      <option value="1">Gradient Blue-White-Red (-2,0,2)</option>';
                        content += '      <option value="2">Gradient Blue-White-Red (-3,0,3)</option>';
                        content += '    </select>';
                    content += '</div>';

                    content += '<div class="form-group col-md-4">';
                        content += '<label class="text-muted">Coloring of </label>';

                        content += '<span class="form-check form-check-inline">';
                        content += '<input class="form-check-input mx-2" type="radio" name="color2field_' + unique_id + '" value="PValue" checked>';
                        content += '<label class="form-check-label text-muted">P.Value</label>';
                        content += '</span>';

                        content += '<span class="form-check form-check-inline">';
                        content += '<input class="form-check-input mx-2" type="radio" name="color2field_' + unique_id + '" value="AdjustedPValue">';
                        content += '<label class="form-check-label text-muted">adj.P.Val</label>';
                        content += '</span>';

                        content += '    <select name="color2_' + unique_id + '" class="custom-select">';
                        content += '      <option value="" selected></option>';
                        content += '      <option value="0">Dark Green (<0.01), Green (0.01-0.05), White (>0.05)</option>';
                        content += '      <option value="1">Green (<=0.01), White (>0.01)</option>';
                        content += '      <option value="2">Green (<=0.05), White (>0.05)</option>';
                        content += '    </select>';
                    content += '</div>';

                content += '  </div>';

            content += '</div></div>';

        content += '</div>';


        return content;
    }


    //-----------------------------------------------------------------------------------------------
    // Content After Uploaded
    function add_file_section(json) {

        var header = json.header;
        if(typeof(header) == "undefined"){
            return '';
        }

        var time = json.time;
        if(typeof(time) == "undefined"){
            return '';
        }

        var url = json.url;
        var file = json.file;

        var content = '<div class="my-3 table-light rounded border border-primary p-3" id="file_' + time + '" style="max-width: 1500px;">';

            content += '<div class="d-lg-flex">';
                content += '<h3 class="">Data file: <a href="' + url + '">' + file + '</a></h3>';
                content += '<a class="ml-5 mt-2 btn_add_file_comparison" row_id="' + time + '" row_header="' + header + '" title="Add Comparison" href="Javascript: void(0);"><i class="fas fa-plus"></i> Add Comparison</a>';
                content += '<a class="mx-5 mt-2 btn_delete_file_comparison" row_id="' + time + '" title="Delete Last Comparison" href="Javascript: void(0);"><i class="fas fa-trash"></i> Delete Last Comparison</a>';
                content += '<a class="ml-auto mr-2" title="Delete this file" href="Javascript: void(0);" onClick="$(\'#file_' + time + '\').hide(); $(\'#file_' + time + '\').html(\'\');" ><i class="fas fa-times text-danger"></i></a>';
            content += '</div>';

            content += add_file_comparison(time, header);

        content += '</div>';

        return content;
    }


    function add_file_comparison(time, header) {

        var content = '';
        var comparison_number = 1 + $('.comparison_' + time).length;
        var unique_id = time + '_' + comparison_number;
        var all_comparison_number = 1 + $('.all_comparisons').length;

        content += '<div class="w-100 mt-4"><div style="background-color: #F1F1F1;" class="rounded border border-success px-3 pt-1 all_comparisons comparison_' + time + '" id="comparison_' + unique_id + '">';

            content += '<div class="form-row">';

                content += '<div class="form-group col-md-4">';
                    content += '<label class="text-muted"><span class="text-danger">*</span> Comparison Name: </label>';
                    content += '<input name="comparisonname_' + unique_id + '" value="Comparison ' + all_comparison_number + '" class="form-control">';
                content += '</div>';

                content += '<div class="form-group col-md-4">';
                    content += '<label class="text-muted"><span class="text-danger">*</span> Coloring of logFC: </label>';
                    content += '    <select name="color1_' + unique_id + '" class="custom-select">';
                    content += '      <option value="0">Gradient Blue-White-Red (-1,0,1)</option>';
                    content += '      <option value="1">Gradient Blue-White-Red (-2,0,2)</option>';
                    content += '      <option value="2">Gradient Blue-White-Red (-3,0,3)</option>';
                    content += '    </select>';
                content += '</div>';

                content += '<div class="form-group col-md-4">';
                    content += '<label class="text-muted">Coloring of </label>';

                    content += '<span class="form-check form-check-inline">';
                    content += '<input class="form-check-input mx-2" type="radio" name="color2field_' + unique_id + '" value="PValue" checked>';
                    content += '<label class="form-check-label text-muted">P.Value</label>';
                    content += '</span>';

                    content += '<span class="form-check form-check-inline">';
                    content += '<input class="form-check-input mx-2" type="radio" name="color2field_' + unique_id + '" value="AdjustedPValue">';
                    content += '<label class="form-check-label text-muted">adj.P.Val</label>';
                    content += '</span>';

                    content += '    <select name="color2_' + unique_id + '" class="custom-select">';
                    content += '      <option value="" selected></option>';
                    content += '      <option value="0">Dark Green (<0.01), Green (0.01-0.05), White (>0.05)</option>';
                    content += '      <option value="1">Green (<=0.01), White (>0.01)</option>';
                    content += '      <option value="2">Green (<=0.05), White (>0.05)</option>';
                    content += '    </select>';
                content += '</div>';

            content += '  </div>';


            content += '<div class="form-row">';

                content += '<div class="form-group col-md-4">';
                    content += '<label class="text-muted"><span class="text-danger">*</span> Data column of <span class="text-success">logFC</span>: </label>';
                    content += '<select name="Log2FoldChange_' + unique_id + '" class="custom-select Log2FoldChange_' + time + '">';

                    var previous_selected = -1;
                    $(".Log2FoldChange_" + time).each(function(index){
                        if( this.selectedIndex > previous_selected) previous_selected = this.selectedIndex;
                    })

                    var found_option = 0;
                    for(var i in header){
                        var item = header[i];
                        var up = item.toUpperCase();
                        var selected = up.search("LOGFC") >= 0 ? 'selected' : (up.search("LOG2FC") >= 0 ? 'selected' : (up.search("LOGFOLDCHANGE") >= 0 ? 'selected' : ''));
                        if(found_option == 0 && selected == 'selected' && i > previous_selected + 1){
                            found_option = 1;
                            content += '<option value="' + item + '" selected>' + item + '</option>';
                        }
                        else if (item != '' && item.search("Name") < 0) {
                            content += '<option value="' + item + '">' + item + '</option>';
                        }
                    }
                    content += '</select>';
                content += '</div>';

                content += '<div class="form-group col-md-4">';
                    content += '<label class="text-muted">Data column of <span class="text-success">P-Value</span>: </label>';
                    content += '<select name="PValue_' + unique_id + '" class="custom-select PValue_' + time + '">';
                    content += '      <option value=""></option>';

                    var previous_selected = -1;
                    $(".PValue_" + time).each(function(index){
                        if( this.selectedIndex > previous_selected) previous_selected = this.selectedIndex;
                    })

                    var found_option = 0;
                    for(var i in header){
                        var item = header[i];
                        var up = item.toUpperCase();
                        var selected = up.search("P.VALUE") >= 0 ? 'selected' : (up.search("PVALUE") >= 0 ? 'selected' : (up.search("P-VALUE") >= 0 ? 'selected' : ''));
                        if(found_option == 0 && selected == 'selected' && i > previous_selected + 1){
                            found_option = 1;
                            content += '<option value="' + item + '" selected>' + item + '</option>';
                        }
                        else if (item != '' && item.search("Name") < 0) {
                            content += '<option value="' + item + '">' + item + '</option>';
                        }
                    }
                    content += '</select>';
                content += '</div>';

                content += '<div class="form-group col-md-4">';
                    content += '<label class="text-muted">Data column of <span class="text-success">Adjusted P-Value</span>: </label>';
                    content += '<select name="AdjustedPValue_' + unique_id + '" class="custom-select AdjustedPValue_' + time + '">';
                    content += '      <option value=""></option>';

                    var previous_selected = -1;
                    $(".AdjustedPValue_" + time).each(function(index){
                        if( this.selectedIndex > previous_selected) previous_selected = this.selectedIndex;
                    })

                    var found_option = 0;
                    for(var i in header){
                        var item = header[i];
                        var up = item.toUpperCase();
                        var selected = up.search("FDR") >= 0 ? 'selected' : (up.search("ADJ.P.VAL") >= 0 ? 'selected' : (up.search("ADJUSTED.P.VALUE") >= 0 ? 'selected' : (up.search("ADJUSTEDPVALUE") >= 0 ? 'selected' : '')));
                        if(found_option == 0 && selected == 'selected' && i > previous_selected + 1){
                            found_option = 1;
                            content += '<option value="' + item + '" selected>' + item + '</option>';
                        }
                        else if (item != '' && item.search("Name") < 0) {
                            content += '<option value="' + item + '">' + item + '</option>';
                        }
                    }
                    content += '</select>';
                content += '</div>';

            content += '</div>';

        content += '</div></div>';


        return content;
    }

</script>

</body>
</html>