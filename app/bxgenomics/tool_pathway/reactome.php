<?php
include_once(__DIR__ . "/config.php");


// Upload File
if (isset($_GET['action']) && $_GET['action'] == 'upload_file') {

    header('Content-Type: application/json');
    $OUTPUT = array();
    $OUTPUT['type'] = 'Error';
    $OUTPUT['detail'] = '';


    $pathway_selected = '';
    $pathway_selected_name = '';
    $pathway_genes = array();

    if(isset($_POST['pathway']) && $_POST['pathway'] != ''){
        $pathway_selected = $_POST['pathway'];

        $pathway_gmt = __DIR__ . "/reactome/ReactomePathways.gmt";
        if (($handle = fopen($pathway_gmt, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 0, "\t")) !== FALSE) {
                if(! is_array($row) || count($row) <= 2) continue;

                $pathway_name = array_shift($row);
                $pathway = array_shift($row);

                if($pathway == $pathway_selected){
                    $pathway_selected_name = $pathway_name;
                    $pathway_genes = $row;
                    break;
                }
            }
            fclose($handle);
        }
    }

    $pathway_geneindex_genename = array();
    $pathway_name_geneindex = array();
    if (count($pathway_genes) > 0){

        $sql = "SELECT `GeneIndex`, `GeneName`  FROM `TBL_BXGENOMICS_GENES_INDEX` WHERE `Species` = 'Human' AND `Name` IN (?a)";
        $pathway_geneindex_genename = $BXAF_MODULE_CONN -> get_assoc('GeneIndex', $sql, $pathway_genes );

        $sql = "SELECT `Name`, `GeneIndex`  FROM `TBL_BXGENOMICS_GENES_INDEX` WHERE `Species` = 'Human' AND `GeneIndex` IN (?a)";
        $pathway_name_geneindex = $BXAF_MODULE_CONN -> get_assoc('Name', $sql, array_keys($pathway_geneindex_genename));

        foreach($pathway_geneindex_genename as $i=>$n){
    		if($i > 10000000) { $pathway_geneindex_genename[$i-10000000] = $n; unset($pathway_geneindex_genename[$i]); }
    	}
    	foreach($pathway_name_geneindex as $n=>$i){
    		if($i > 10000000) { $pathway_name_geneindex[$n] = $i-10000000; }
    	}

    }


    $all_geneindex_genenames = array();
    $sql = "SELECT `GeneIndex`, `GeneName`  FROM `TBL_BXGENOMICS_GENES_INDEX` WHERE `Species` = 'Human'";
    $all_geneindex_genenames = $BXAF_MODULE_CONN -> get_assoc('GeneIndex', $sql);

    foreach($all_geneindex_genenames as $i=>$n){
        if($i > 10000000) { $all_geneindex_genenames[$i-10000000] = $n; unset($all_geneindex_genenames[$i]); }
    }



    $time = time();

    $CACHE_DIR = $BXAF_CONFIG['CURRENT_SYSTEM_CACHE'] . $time;
    $CACHE_URL = $BXAF_CONFIG['CURRENT_SYSTEM_CACHE_URL'] . $time;

    if(! file_exists($CACHE_DIR)) mkdir($CACHE_DIR, 0755, true);
    if(file_exists("$CACHE_DIR/uploaded.csv")) unlink("$CACHE_DIR/uploaded.csv");

    $file_name = 'comparison.csv';
    if (isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $file_name = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], "$CACHE_DIR/uploaded.csv");
        }
    }
    else if(isset($_POST['Comparison_List']) && trim($_POST['Comparison_List']) != ''){

        $list = preg_split("/[\s,]+/", $_POST['Comparison_List'], NULL, PREG_SPLIT_NO_EMPTY);

		// public
		$sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM ?n WHERE `ComparisonID` IN (?a)";
		$comparison_indexnames_public = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, $BXAF_CONFIG['TBL_BXGENOMICS_COMPARISONS'], $list);
		if (! is_array($comparison_indexnames_public) || count($comparison_indexnames_public) <= 0) $comparison_indexnames_public = array();

		// Private
		$sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM ?n WHERE `ComparisonID` IN (?a)";
		$comparison_indexnames_private = $BXAF_MODULE_CONN -> get_assoc('ComparisonIndex', $sql, 'App_User_Data_Comparisons', $list);
		if (! is_array($comparison_indexnames_private) || count($comparison_indexnames_private) <= 0) $comparison_indexnames_private = array();

		$comparison_indexnames = $comparison_indexnames_public + $comparison_indexnames_private;

		if ( ! is_array($comparison_indexnames) || count($comparison_indexnames) <= 0 ){
			$OUTPUT['detail'] = "<div class='lead text-danger'>Error: No valid comparison is found. Please enter at least one valid comparison ID.</div>";
            echo json_encode($OUTPUT);
			exit();
		}
		else {
			foreach($list as $c){
				if(! in_array($c, $comparison_indexnames)){
					$OUTPUT['detail'] = "<div class='lead text-danger'>Error: Comparison '$c' is not found.</div>";
                    echo json_encode($OUTPUT);
					exit();
				}
			}
		}


        $gene_indexes = array_keys($pathway_geneindex_genename);

        $comparison_indexes = array_keys($comparison_indexnames);
        $comparison_names   = array_values($comparison_indexnames);

        if(isset($_POST['checkbox_no_filter']) && $_POST['checkbox_no_filter'] == 1){
            $tabix_results = tabix_search_bxgenomics( array(), $comparison_indexes, 'ComparisonData' );
        }
        else {
            $tabix_results = tabix_search_bxgenomics( $gene_indexes, $comparison_indexes, 'ComparisonData' );
        }

        $data_types = array('Log2FoldChange', 'PValue', 'AdjustedPValue');


        $handle_uploaded = fopen("$CACHE_DIR/uploaded.csv", "w");
        $header = array();
        $header[] = 'GeneName';
        foreach($comparison_names as $name) {
            foreach($data_types as $type) $header[] = $name . '_' . $type;
        }
        fputcsv($handle_uploaded, $header);

        $data_uploaded = array();
        foreach($tabix_results as $row){

            $g_index = $row['GeneIndex'];
            $g_name  = $all_geneindex_genenames[ $g_index ];
            if(! preg_match("/^[\w\-]+$/", $g_name) ) continue;

            $c_index = $row['ComparisonIndex'];
            $c_name  = $comparison_indexnames[$c_index];

            if(! isset($data_uploaded[$g_index])) $data_uploaded[$g_index] = array();

            $data_uploaded[$g_index]['GeneName'] = $g_name;
            foreach($data_types as $type) $data_uploaded[$g_index][$c_name . '_' . $type] = sprintf("%.4f", $row[ $type ] );

        }

        foreach($data_uploaded as $g_index => $row){

            if(count($row) != count($header)) continue;

            $row_values = array();
            foreach($header as $col){
                $row_values[] = $row[$col];
            }
            fputcsv($handle_uploaded, $row_values);
        }
    }






    $OUTPUT['pathway_name'] = $pathway_selected_name;
    $OUTPUT['pathway'] = $pathway_selected;
    $OUTPUT['time'] = $time;

    $OUTPUT['token'] = '';
    $OUTPUT['results'] = array();
    $OUTPUT['table'] = '';

    $csv_rows = 0;
    if(file_exists("$CACHE_DIR/uploaded.csv")){

        // Filter data
        $range_min = intval($_POST['range_min']);
        $range_max = intval($_POST['range_max']);

        $csv_contents = '';
        $csv_filtered = "$CACHE_DIR/filtered.csv";
        if(file_exists($csv_filtered)) unlink($csv_filtered);

        $n = 0;
        if (count($pathway_name_geneindex) > 0 && count($pathway_geneindex_genename) > 0 && ($handle = fopen("$CACHE_DIR/uploaded.csv", "r")) !== FALSE) {

            $handle_filtered = fopen($csv_filtered, "w");

            $header = fgetcsv($handle);
            $header[0] = 'GeneName';
            fputcsv($handle_filtered, $header);

            $csv_contents .= "<table id='table_filtered_results' class='table table-bordered table-hover'><thead><tr class='table-success'>";
            foreach($header as $k=>$v){
                if($k==0) {
                    $csv_contents .= "<th>Gene Name</th><th>Description</th>";
                }
                else $csv_contents .= "<th>$v</th>";
            }
            $csv_contents .= "</tr></thead><tbody>";

            while (($row = fgetcsv($handle)) !== FALSE) {
                if(! is_array($row) || count($row) <= 1) continue;

                $key = array_shift($row);
                if(! array_key_exists($key, $pathway_name_geneindex)){
                    continue;
                }

                $n++;

                $row_filtered = array();
                $row_filtered1 = array();
                if(array_key_exists($key, $pathway_name_geneindex)){
                    $index = $pathway_name_geneindex[$key];
                    $name = $all_geneindex_genenames[$index];
                    $row_filtered[] = $name;
                    $row_filtered1[] = $name;
                }
                else {
                    $row_filtered[] = $key;
                    $row_filtered1[] = $key;
                }

                foreach($row as $k=>$v){
                    if($_POST['data_option'] == 'range' && $v < $range_min) $row_filtered[] = $range_min;
                    else if($_POST['data_option'] == 'range' && $v > $range_max) $row_filtered[] = $range_max;
                    else $row_filtered[] = sprintf("%.4f", $v);

                    $row_filtered1[] = sprintf("%.4f", $v);
                }

                if(count($row_filtered) > 0){
                    fputcsv($handle_filtered, $row_filtered);

                    $csv_contents .= "<tr>";
                    foreach($row_filtered1 as $k=>$v){
                        if($k == 0){
                            $sql = "SELECT `Description`  FROM `GeneCombined` WHERE `GeneID` = ?s";
                        	$desc = $BXAF_MODULE_CONN -> get_one($sql, $v);

                            $csv_contents .= "<td>" . $v . "</td><td>" . $desc . "</td>";
                        }
                        else {
                            if($v == 'NA') $csv_contents .= "<td class='text-muted'>NA</td>";
                            else $csv_contents .= "<td style='color: " . get_stat_scale_color2($v, 'Log2FoldChange') . ";'>" . sprintf("%.4f", $v) . "</td>";
                        }
                    }
                    $csv_contents .= "</tr>";

                    $csv_rows++;
                }

            }
            fclose($handle);

            $csv_contents .= "</tbody></table>";

            fclose($handle_filtered);
        }

        $csv_contents .= "<div class='mx-3'><a href='$CACHE_URL/uploaded.csv'>Download Complete Data (CSV format)</a></div>";


        $csv_to_upload = "$CACHE_DIR/uploaded.csv";
        if(file_exists($csv_filtered) && (! isset($_POST['checkbox_no_filter']) || $_POST['checkbox_no_filter'] != 1) ){
            $csv_to_upload = $csv_filtered;
        }

        $target_url = 'https://reactome.org/AnalysisService/identifiers/form';

        $postFields = array(
            'pathway' => $pathway_selected,
            'file' => new cURLFile($csv_to_upload, "text/plain", $file_name)
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_POST,1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        $json=curl_exec ($ch);
        curl_close ($ch);

        $result_array = json_decode($json, true);

        $token = $result_array['summary']['token'];

        if($token != ''){
            $found_pathway = array();
            foreach($result_array['pathways'] as $k => $v){
                if($v['stId'] == $pathway_selected){
                    $found_pathway[$k] = $v;
                    break;
                }
            }
            $result_array['pathways'] = $found_pathway;

            $OUTPUT['token'] = $token;
            $OUTPUT['results'] = $result_array;
        }

        $OUTPUT['table'] = $csv_contents;

    }

    $OUTPUT['type'] = 'Success';

    echo json_encode($OUTPUT);

    exit();
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








$BXAF_CONFIG['PATHWAY_LIST'] = unserialize(file_get_contents("reactome/species/Homo_sapiens.txt"));

if (isset($_GET['pathway']) && $_GET['pathway'] != ''){
	$similar_pathways = find_similar_pathways($_GET['pathway']);
}


?><!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
    <link type="text/css" rel="stylesheet" href="css/style.css" />

    <script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js'></script>

    <link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.css' rel='stylesheet' type='text/css'>
    <script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables_all_extensions.min.js'></script>

    <script type="text/javascript" language="javascript" src="https://reactome.org/FireworksJs/fireworks/fireworks.nocache.js"></script>
    <script type="text/javascript" language="javascript" src="http://www.reactome.org/DiagramJs/diagram/diagram.nocache.js"></script>

    <style>
        body {
          padding-top: 5rem;
        }
        .hidden{
            display: none;
        }
    </style>


    <script>

        $(document).ready(function() {

            $('#table_select_pathway').DataTable();

            $(document).on('click', '#btn_select_pathway_show_modal', function() {
            	$('#modal_select_pathway').modal('show');
            });

            $(document).on('click', '.btn_select_search_pathway', function() {

    			<?php
    			if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){
    				echo "$('#modal_similar_pathways').modal('hide');";
    			}
    			?>

                var content = $(this).attr('content');
                var displayed_name = $(this).attr('displayed_name');

                $('#text_pathway_name').text(displayed_name);
                if( $('#text_pathway_name').hasClass('text-muted') )  $('#text_pathway_name').removeClass('text-muted')
                if(! $('#text_pathway_name').hasClass('text-danger') )  $('#text_pathway_name').addClass('text-danger')

                $('#input_pathway').val(content);

                $('#modal_select_pathway').modal('hide');

            });


    		<?php if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){ ?>
    		$(document).on('click', '.btn_select_similar_pathway', function() {

    			$('#modal_similar_pathways').modal('hide');

                var content = $(this).attr('content');
                var displayed_name = $(this).attr('displayed_name');

                $('#text_pathway_name').text(displayed_name);
                if( $('#text_pathway_name').hasClass('text-muted') )  $('#text_pathway_name').removeClass('text-muted')
                if(! $('#text_pathway_name').hasClass('text-danger') )  $('#text_pathway_name').addClass('text-danger')

                $('#input_pathway').val(content);

            });
    		<?php } ?>


            <?php
    			if ((sizeof($similar_pathways) == 1) && (is_array($similar_pathways))){

    				$key 	= array_keys($similar_pathways);
    				$key 	= $key[0];

    				$value 	= array_values($similar_pathways);
    				$value	= $value[0];

    				echo "var content = '$key';
                        var displayed_name = '$value';

                        $('#text_pathway_name').text(displayed_name);
                        if( $('#text_pathway_name').hasClass('text-muted') )  $('#text_pathway_name').removeClass('text-muted')
                        if(! $('#text_pathway_name').hasClass('text-danger') )  $('#text_pathway_name').addClass('text-danger')

                        $('#input_pathway').val(content); ";
    			}

    			if ((sizeof($similar_pathways) > 1) && (is_array($similar_pathways))){
    				echo "$('#modal_similar_pathways').modal('show');";
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



            // File Upload
            var options = {
                url: '<?php echo $_SERVER['PHP_SELF']; ?>?action=upload_file',
                type: 'post',
                beforeSubmit: function(formData, jqForm, options) {

                    $('#form_upload_file_busy').removeClass('hidden');

                    return true;
                },
                success: function(response){
                    $('#form_upload_file_busy').addClass('hidden');

                    var type = response.type;
                    if (type == 'Error') {
                        bootbox.alert(response.detail);
                    }
                    else {

                        $('#form_main').addClass('hidden');
                        $('#div_options_restart').removeClass('hidden');

                        if(response.table != ''){
                            $('#div_table').html(response.table);
                            $('#table_filtered_results').DataTable({ "pageLength": 100, "lengthMenu": [[10, 100, 500, 1000], [10, 100, 500, 1000]], dom: 'Blfrtip', buttons: [ 'colvis','csv','excel','pdf','print'] });
                        }

                        var w = window.innerWidth - 50;
                        var h = window.innerHeight - 20;


                        if(response.pathway != ''){

                            var diagram = Reactome.Diagram.create({
                                "placeHolder" : "diagramHolder",
                                "width" : window.innerWidth - 20,
                                "height" : 1200
                            });

                            //Initialising it to the "Hemostasis" pathway
                            diagram.loadDiagram(response.pathway);

                            //Adding different listeners
                            diagram.onDiagramLoaded(function (loaded) {
                                // console.info("Loaded ", loaded);
                                if(response.pathway_name != '') diagram.flagItems(response.pathway_name);
                                if(response.token != '') diagram.setAnalysisToken(response.token, "");
                            });

                            diagram.onObjectHovered(function (hovered){
                                // console.info("Hovered ", hovered);
                            });

                            diagram.onObjectSelected(function (selected){
                                // console.info("Selected ", selected);
                            });
                        }
                        else {
                            var fireworks = Reactome.Fireworks.create({
                                "placeHolder" : "fireworksHolder",
                                "width" : window.innerWidth - 20,
                                "height" : 1200
                            });

                            $('#div_options_overview').removeClass('hidden');

                            //Adding different listeners
                            fireworks.onFireworksLoaded(function (loaded) {

                            });

                            fireworks.onNodeHovered(function (hovered){
                                if(response.token != '') fireworks.setAnalysisToken(response.token, "");
                            });

                            fireworks.onNodeSelected(function (selected){
                                console.info("Selected ", selected);

                                if(selected !== 'null' && selected.hasOwnProperty('stId')){


                                    $('#div_fireworks').addClass('hidden');
                                    $('#div_options_restart').removeClass('hidden');


                                    var diagram = Reactome.Diagram.create({
                                        "placeHolder" : "diagramHolder",
                                        "width" : window.innerWidth - 20,
                                        "height" : 1200
                                    });

                                    //Initialising it to the "Hemostasis" pathway
                                    diagram.loadDiagram(selected.stId);

                                    //Adding different listeners
                                    diagram.onDiagramLoaded(function (loaded) {
                                        if(response.pathway_name != '') diagram.flagItems(response.pathway_name);
                                        if(response.token != '') diagram.setAnalysisToken(response.token, "");
                                    });

                                    diagram.onObjectHovered(function (hovered){
                                    });

                                    diagram.onObjectSelected(function (selected){
                                    });

                                }
                            });
                        }

                        return true;
                    }
                }
            };
            $('#form_main').ajaxForm(options);

        });

    </script>

</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
	<div id="bxaf_page_content" class="row no-gutters h-100">
        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
			<div id="bxaf_page_right_content" class="w-100 p-2">

                <div class="d-flex">
                    <h1>Reactome Pathway Visualization</h1>
                    <div class="ml-5 mt-2 text-muted"></div>
                </div>
                <hr class="w-100 my-1" />

                    <form class="my-2" id="form_main" method="post" enctype="multipart/form-data">

                        <div class="mt-3 w-100">
                            <div class="form-group">
                                <input class="hidden" name="pathway" id="input_pathway" value="">
                                <span class="text-muted" id="text_pathway_name">(No Pathway Selected yet, will show Pathway Overview)</span>
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

                            <a class="ml-3" href="Javascript: void(0);" id="btn_select_comparison"> <i class="fas fa-search"></i> Select Comparisons </a>

                            <a class="ml-3" href="Javascript: void(0);" onclick="$('#Comparison_List').val('');"> <i class="fas fa-times"></i> Clear </a>


                            <textarea class="form-control w-50" onBlur="$('#comparison_file').val('');" style="height:10rem;" name="Comparison_List" id="Comparison_List" category="Comparison"><?php echo implode("\n", $comparison_ids); ?></textarea>

                        </div>


                        <div class="form-group form-inline">

                            <span class="mx-2">Or, Upload your comparison file: </span>

                            <input id="input_upload_file" type="file" class="form-control mx-2" name="file" onclick="$('#Comparison_List').val('');" >

                            <span id="form_upload_file_busy" class="mx-2 hidden text-danger"><i class="fas fa-spinner fa-spin"></i> Uploading ... </span>

                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Data Range Options: </label>

                            <div class="form-row align-items-center">
                                <div class="col-auto my-1">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="data_option" value="" checked>
                                      <label class="form-check-label">No Change</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="data_option" value="range">
                                      <label class="form-check-label">Limit values within range: </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-control" style="width: 4rem;" type="number" name="range_min" id="range_min" value="-1">
                                      <label class="mx-2"> - </label>
                                      <input class="form-control" style="width: 4rem;" type="number" name="range_max" id="range_max" value="1">
                                    </div>
                                </div>

                                <div class="col-auto my-1">
                                    <select class="custom-select" onchange="if($(this).val() != '') { $('#range_min').val( (-1) * $(this).val() ); $('#range_max').val( $(this).val() ); $(this).val(''); } ">
                                        <option value="" selected>Select a Preset Range</option>
                                        <option value="1">-1 to 1</option>
                                        <option value="2">-2 to 2</option>
                                        <option value="3">-3 to 3</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <input class="btn btn-primary" type="submit" value="View Pathway">

                            <input class="mx-1" type="checkbox" name="checkbox_no_filter" id="checkbox_no_filter" value="1">Upload all gene data to Reactome

                            <a class="mx-2" href="files/demo_logfc.csv">
                                <i class="fas fa-angle-double-right"></i> Demo Data1
                            </a>
                            <a class="mx-2" href="files/demo_pvalue.csv">
                                <i class="fas fa-angle-double-right"></i> Demo Data2
                            </a>
                            <a class="mx-2" href="files/demo_adjpval.csv">
                                <i class="fas fa-angle-double-right"></i> Demo Data3
                            </a>

                        </div>

                    </form>

                    <div class="w-100 my-3">
                        <div id="div_options" class="w-100 my-3">
                            <a id="div_options_overview" class="hidden mr-5" href="Javascript: void(0);" onclick="if( $('#div_fireworks').hasClass('hidden') ) $('#div_fireworks').removeClass('hidden'); else $('#div_fireworks').addClass('hidden'); "><i class='fas fa-caret-right'></i> Show/Hide Overview</a>
                            <a id="div_options_restart" class="hidden" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class='fas fa-caret-right'></i> Restart</a>
                        </div>
                        <div id="div_fireworks" class="w-100 my-3"><div id="fireworksHolder"></div></div>
                        <div id="div_diagram" class="w-100 my-3"><div id="diagramHolder"></div></div>
                        <div id="div_table" class="w-100 my-3"></div>
                        <div id="div_debug" class="w-100 my-3"></div>
                    </div>


    <?php include("modals.php"); ?>


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
    						<thead>
    						<tr>
                                <th>Pathway Code</th>
    							<th>Pathway Name</th>
    							<th>Action</th>
    						</tr>
    						</thead>
    						<tbody>';

                            foreach ($BXAF_CONFIG['PATHWAY_LIST'] as $key => $value) {
    							echo '
    							<tr>
                                    <td>' . $key . '</td>
    								<td>' . $value . '</td>
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


            </div>

		    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>

		</div>

	</div>

</body>
</html>