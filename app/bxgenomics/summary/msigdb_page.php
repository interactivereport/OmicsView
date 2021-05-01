<?php
include_once(__DIR__ . "/config.php");

$tabix_index_file_dir = $BXAF_CONFIG['WORK_DIR'] . 'bxgenomics/comparison_page/';


if (!function_exists('tabix_search_records_private2')) {
	function tabix_search_records_private2($primaryIndex, $secondaryIndex, $tabix_index_file, $tabix_index_file2) {
		global $BXAF_CONFIG;


		if (is_array($primaryIndex)){
			$primaryIndex = array_filter($primaryIndex, 'is_numeric');
			$primaryIndex = array_unique($primaryIndex);
		}
		else $primaryIndex = array();

		if (is_array($secondaryIndex)){
			$secondaryIndex = array_filter($secondaryIndex, 'is_numeric');
			$secondaryIndex = array_unique($secondaryIndex);
		}
		else $secondaryIndex = array();


		if( (count($primaryIndex) <= 0 && count($secondaryIndex) <= 0)  || count($primaryIndex) > 1000 || count($secondaryIndex) > 1000){
			return array();
		}

		natsort($primaryIndex);
		natsort($secondaryIndex);


		$TABIX_CACHE_DIR = $BXAF_CONFIG['BXGENOMICS_CACHE_DIR'];


		if(! file_exists($TABIX_CACHE_DIR)) mkdir($TABIX_CACHE_DIR, 0777, true);


		$filePrefix	= time();
		$fileInput 			= $TABIX_CACHE_DIR . $filePrefix . '_input.txt';
		$fileOutputTabix	= $TABIX_CACHE_DIR . $filePrefix . '_output.tabix';
		$fileOutputTxt 		= $TABIX_CACHE_DIR . $filePrefix . '_output.txt';

		if(file_exists($fileInput)) unlink($fileInput);
		if(file_exists($fileOutputTabix)) unlink($fileOutputTabix);
		if(file_exists($fileOutputTxt)) unlink($fileOutputTxt);


		$command = "";
		if (count($primaryIndex) > 0 && count($secondaryIndex) > 0){

			$fp = fopen($fileInput, 'w');
			foreach($primaryIndex as $currentPrimaryIndex){
				foreach($secondaryIndex as $currentSecondaryIndex){
					$currentSecondaryIndex++;
					fwrite($fp, "{$currentPrimaryIndex}\t{$currentSecondaryIndex}\t{$currentSecondaryIndex}\n");
				}
			}
			fclose($fp);

			$command = "{$BXAF_CONFIG['TABIX_BIN']} {$tabix_index_file} -R {$fileInput} > {$fileOutputTabix}";

		}
		else if (count($primaryIndex) > 0 && count($secondaryIndex) <= 0){
			$command = "{$BXAF_CONFIG['TABIX_BIN']} {$tabix_index_file} " . implode(' ', $primaryIndex) . " > {$fileOutputTabix}";
		}
		else if (count($primaryIndex) <= 0 && count($secondaryIndex) > 0){
			$command = "{$BXAF_CONFIG['TABIX_BIN']} {$tabix_index_file2} " . implode(' ', $secondaryIndex) . " > {$fileOutputTabix}";
		}
		shell_exec($command);

		$RESULT = array();
	    if(file_exists($fileOutputTabix) && ($handle = fopen($fileOutputTabix, "r")) !== FALSE){
			$fp = fopen($fileOutputTxt, 'w');
            while (($row = fgetcsv($handle, 0, "\t")) !== FALSE) {

				$data = array(
					'MSigDB_ID'     => $row[0],
					'Comparison_Index' => $row[1],

					'MSigDB_Name'     => $row[2],
					'Comparison_Name' => $row[3],

					'Z_Score'         => $row[5],
					'P_Value'         => $row[6],
					'FDR'             => $row[7],

					'Genes_Total'     => $row[4],
					'Genes_Up'        => $row[8],
					'Genes_Down'      => $row[9]
				);
				fputcsv($fp, $data, "\t");

				$RESULT[] = $data;
			}
			fclose($fp);
		}

		return $RESULT;

	}
}


if(isset($_GET['action']) && $_GET['action'] == "get_gene_list") {

	$id = intval($_GET['id']);
	$sql = "SELECT * FROM `tbl_page_genesets` WHERE `ID` = ?i";
	$info = $BXAF_MODULE_CONN->get_row($sql, $id);

	$IDs = array();
	$Names = array();

	$IDs = explode(", ", $info['Gene_IDs']);
	$Names = explode(", ", trim(trim($info['Gene_Names'], ',')) );

	echo '
		<div>
			<div class="lead">Display Method:</div>
			<div>
				<input type="radio" class="content_detail_radio mx-2" name="content_detail" id="" value="0" onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_0\').removeClass(\'hidden\'); ">
				Gene IDs, one per row <BR />
				<input type="radio" class="content_detail_radio mx-2" name="content_detail" id="" value="2" onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_2\').removeClass(\'hidden\'); ">
				Gene IDs, comma seperated <BR />
				<input type="radio" class="content_detail_radio mx-2" name="content_detail" id="" value="1" checked  onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_1\').removeClass(\'hidden\'); ">
				Gene Names, one per row <BR />
				<input type="radio" class="content_detail_radio mx-2" name="content_detail" id="" value="3" onClick="$(\'.content_detail_all\').addClass(\'hidden\'); $(\'#textarea_content_3\').removeClass(\'hidden\'); ">
				Gene Names, comma seperated <BR />
			</div>
		</div>

		<hr>
		<div class="row m-0">
			<textarea class="hidden content_detail_all" id="textarea_content_0" style="height:300px; width:100%;">'. implode("\n", $IDs) . '</textarea>
			<textarea class="       content_detail_all" id="textarea_content_1" style="height:300px; width:100%;">'. implode("\n", $Names) . '</textarea>
			<textarea class="hidden content_detail_all" id="textarea_content_2" style="height:300px; width:100%;">'. implode(', ', $IDs) . '</textarea>
			<textarea class="hidden content_detail_all" id="textarea_content_3" style="height:300px; width:100%;">'. implode(', ', $Names) . '</textarea>
		</div>
		';

	exit();
}



if (isset($_GET['action']) && $_GET['action'] == 'select_msigdb_id'){

	$table = 'tbl_page_genesets';
	$filter = '1';

    $sql2 = "SELECT `ID`, `Name`, `URL`, `Gene_Counts`, 'Actions' FROM `$table` WHERE $filter ";

    $sql = "";
    // Search Condition
    if(isset($_POST['search']['value']) && trim($_POST['search']['value']) != '') {
    	$search_array = array();
    	for ($i = 0; $i < count($_POST['columns']); $i++){
            if(! in_array($_POST['columns'][$i]['data'], array('Actions'))){
                $search_array[] = "`" . $_POST['columns'][$i]['data'] . "` LIKE '%" . $_POST['search']['value'] . "%'";
            }
    	}
    	$sql .= " AND (" . implode(" OR ", $search_array) . ")";
    }

    // Order Condition
    $sql .= " ORDER BY ";
    $condition_array = array();
    for ($i = 0; $i < count($_POST['order']); $i++) {
        $order = $_POST['columns'][$_POST['order'][$i]['column']]['data'];
        $asc = $_POST['order'][$i]['dir'];
        if(! in_array($order, array('Actions'))){
            $condition_array[] = "`$order` $asc";
        }
    }
    $sql .= implode(", ", $condition_array);



    $sql0 = "SELECT COUNT(*) FROM `$table` WHERE $filter ";
    $recordsTotal = $BXAF_MODULE_CONN->get_one($sql0);

    $recordsFiltered = $BXAF_MODULE_CONN->get_one($sql0 . $sql);

    $data = $BXAF_MODULE_CONN->get_all($sql2 . $sql . " LIMIT " . $_POST['start'] . "," . $_POST['length'] . "");

    $output_array = array(
		'sql' => $BXAF_MODULE_CONN->last_query(),
    	'draw' => intval($_POST['draw']),
    	'recordsTotal' => $recordsTotal,
    	'recordsFiltered' => $recordsFiltered,
    	'data' => array()
    );

    foreach($data as $value) {
        $row = array();
        foreach($value as $k=>$v){
			if($k == 'Name'){
				$row[$k] = "<a href='" . $value['URL'] . "' target='_blank'>" . $v . "</a>";
			}
			else if($k == 'Actions'){
				$row[$k] = '<a href="javascript:void(0);" class="btn_' . $_GET['action'] . '" content="' . $value['ID'] . '" displayed_name="' . ucwords(strtolower(str_replace('_', ' ', $value['Name']))) . '"><i class="fas fa-angle-double-right"></i> Select</a>';
			}
			else {
				$row[$k] = $v;
			}
        }
        $output_array['data'][] = $row;

    }
    echo json_encode($output_array);

    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'save_data_type'){
	$_SESSION['data_type'] = $_GET['data_type'];
	exit();
}
if (isset($_GET['action']) && $_GET['action'] == 'select_comparison_index'){

	if(isset($_SESSION['data_type']) && $_SESSION['data_type'] == 'Private') $table = 'App_User_Data_Comparisons';
	else $table = 'Comparisons';

	$filter = '1';

    $sql2 = "SELECT `ComparisonIndex`, `ComparisonID`, 'Actions' FROM `$table` WHERE $filter ";

    $sql = "";
    // Search Condition
    if(isset($_POST['search']['value']) && trim($_POST['search']['value']) != '') {
    	$search_array = array();
    	for ($i = 0; $i < count($_POST['columns']); $i++){
            if(! in_array($_POST['columns'][$i]['data'], array('Actions'))){
                $search_array[] = "`" . $_POST['columns'][$i]['data'] . "` LIKE '%" . addslashes($_POST['search']['value']) . "%'";
            }
    	}
    	$sql .= " AND (" . implode(" OR ", $search_array) . ")";
    }

    // Order Condition
    $sql .= " ORDER BY ";
    $condition_array = array();
    for ($i = 0; $i < count($_POST['order']); $i++) {
        $order = $_POST['columns'][$_POST['order'][$i]['column']]['data'];
        $asc = $_POST['order'][$i]['dir'];
        if(! in_array($order, array('Actions'))){
            $condition_array[] = "`$order` $asc";
        }
    }
    $sql .= implode(", ", $condition_array);



    $sql0 = "SELECT COUNT(*) FROM `$table` WHERE $filter ";
    $recordsTotal = $BXAF_MODULE_CONN->get_one($sql0);

    $recordsFiltered = $BXAF_MODULE_CONN->get_one($sql0 . $sql);

    $data = $BXAF_MODULE_CONN->get_all($sql2 . $sql . " LIMIT " . $_POST['start'] . "," . $_POST['length'] . "");

    $output_array = array(
		'sql' => $BXAF_MODULE_CONN->last_query(),
    	'draw' => intval($_POST['draw']),
    	'recordsTotal' => $recordsTotal,
    	'recordsFiltered' => $recordsFiltered,
    	'data' => array()
    );

    foreach($data as $value) {
        $row = array();
        foreach($value as $k=>$v){
			if($k == 'Actions'){
				$row[$k] = '<a href="javascript:void(0);" class="btn_' . $_GET['action'] . '" content="' . $value['ComparisonIndex'] . '" displayed_name="' . $value['ComparisonID'] . '"><i class="fas fa-angle-double-right"></i> Select</a>';
			}
			else {
				$row[$k] = $v;
			}
        }
        $output_array['data'][] = $row;

    }
    echo json_encode($output_array);

    exit();
}



if (isset($_GET['action']) && $_GET['action'] == 'select_msigdb_page_list'){

	$FDR = 0.25;
	if(isset($_POST['FDF']) && $_POST['FDF'] > 0) $FDR = $_POST['FDF'];

	$search_results = array();

	$primaryIndex = array(); if($_POST['msigdb_id'] != '') $primaryIndex = array($_POST['msigdb_id']);
	$secondaryIndex = array(); if($_POST['comparison_index'] != '') $secondaryIndex = array($_POST['comparison_index']);

	if( count($primaryIndex) <= 0 && count($secondaryIndex) <= 0 ){
		echo "<div class='text-danger lead my-3'>Please select a MSigDB term or a comparison.</div>";
		exit();
	}
	else if( count($primaryIndex) > 1000 || count($secondaryIndex) > 1000){
		echo "<div class='text-danger lead my-3'>You can not select too many MSigDB terms or comparisons.</div>";
		exit();
	}

	$search_results = tabix_search_records_private2($primaryIndex, $secondaryIndex, "{$tabix_index_file_dir}comparison_page_all_msigdb.txt.gz", "{$tabix_index_file_dir}comparison_page_all_msigdb.txt.comparison.gz");

	$search_results2 = tabix_search_records_private2($primaryIndex, $secondaryIndex, "{$tabix_index_file_dir}comparison_page_all_msigdb_private.txt.gz", "{$tabix_index_file_dir}comparison_page_all_msigdb_private.txt.comparison.gz");

	if(is_array($search_results) && is_array($search_results)) $search_results = array_merge($search_results, $search_results2);

	foreach($search_results as $i=>$result){
		if($result['FDR'] > $FDR) unset($search_results[$i]);
		if(isset($_POST['Genes_Total']) && $_POST['Genes_Total'] > 0 && $result['Genes_Total'] < $_POST['Genes_Total']) unset($search_results[$i]);
		if(isset($_POST['Genes_Up']) && $_POST['Genes_Up'] > 0 && $result['Genes_Up'] < $_POST['Genes_Up'])  unset($search_results[$i]);
		if(isset($_POST['Genes_Down']) && $_POST['Genes_Down'] > 0 && $result['Genes_Down'] < $_POST['Genes_Down']) unset($search_results[$i]);

		if(isset($_POST['P_Value']) && $_POST['P_Value'] > 0 && $result['P_Value'] > $_POST['P_Value']) unset($search_results[$i]);

		if(isset($_POST['Z_Score']) && $_POST['Z_Score'] > 0 && $result['Z_Score'] < $_POST['Z_Score']) unset($search_results[$i]);
		else if(isset($_POST['Z_Score']) && $_POST['Z_Score'] < 0 && $result['Z_Score'] > $_POST['Z_Score'])  unset($search_results[$i]);
	}

	if(count($search_results) <= 0){
		echo "<div class='text-danger lead my-3'>No records found.</div>";
		exit();
	}

	$table_contents .= "<div class='w-100'>";

	$table_contents .= "<table id='table_search_results' class='table table-bordered table-hover'>";
	$table_contents .= "<thead><tr class='table-success'><th>MSigDB Term</th><th>Comparison</th><th>Z Score</th><th>P Value</th><th>FDR</th><th>Genes Total</th><th>Genes Up</th><th>Genes Down</th></tr></thead><tbody>";

	foreach($search_results as $result){

		$table_contents .= "<tr>";

			$table_contents .= "<td><a href='http://software.broadinstitute.org/gsea/msigdb/cards/" . $result['MSigDB_Name'] . "' target='_blank'>" . $result['MSigDB_Name'] . "</a> <a title='Show Genes' href='Javascript: void(0);' list_id='" . $result['MSigDB_ID'] . "' list_name='" . $result['MSigDB_Name'] . "' class='content_detail mx-2'><i class='fas fa-list'></i></a></td>";
			$table_contents .= "<td>" . $result['Comparison_Name'] . "</td>";
			$table_contents .= "<td>" . $result['Z_Score'] . "</td>";
			$table_contents .= "<td>" . $result['P_Value'] . "</td>";
			$table_contents .= "<td>" . $result['FDR'] . "</td>";
			$table_contents .= "<td>" . $result['Genes_Total'] . "</td>";
			$table_contents .= "<td>" . $result['Genes_Up'] . "</td>";
			$table_contents .= "<td>" . $result['Genes_Down'] . "</td>";

		$table_contents .= "</tr>";

	}
	$table_contents .= "</tbody></table>";
	$table_contents .= "</div>";

	echo $table_contents;

    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

    <style>
        .hidden{
            display: none;
        }
    </style>

</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_content" class="row no-gutters h-100">

        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

			<div id="bxaf_page_right_content" class="w-100 p-2">

				<h1>
					Search PAGE Results

				</h1>


                <form class="w-100" id="form_main" method="post">
					<div class="container-fluid my-3 border table-info border-info rounded">

						<div class="row my-3">

							<div class="col-lg-3 col-md-6 col-sm-12">

								<input type="hidden" id="msigdb_id" name="msigdb_id" value="<?php echo ($_GET['msigdb_id'] != '') ? $_GET['msigdb_id'] : ""; ?>">
								<div class="w-100 my-2" id="msigdb_id_text">(No MSigDB term selected yet)</div>

								<a href="Javascript: void(0);" class="btn_select_msigdb_id_modal" target_field="msigdb_id">
			                      <i class="fas fa-angle-double-right"></i> Select a MSigDB Term
			                    </a>
							</div>

							<div class="col-lg-3 col-md-6 col-sm-12">
								<input type="hidden" id="comparison_index" name="comparison_index" value="<?php echo ($_GET['comparison_index'] != '') ? $_GET['comparison_index'] : ""; ?>">
								<div class="w-100 my-2" id="comparison_index_text">(No comparison record selected yet)</div>

								<a href="Javascript: void(0);" class="btn_select_comparison_index_modal" target_field="comparison_index">
			                    	<i class="fas fa-angle-double-right"></i> Select a Comparison
			                    </a>
							</div>

							<div class="col-lg-2 col-md-6 col-sm-12">
								<label class="font-weight-bold">Minimum Genes (Total): </label>
								<input title="Minimum number of total genes" class="form-control" type="text" id="Genes_Total" name="Genes_Total" value="<?php echo ($_GET['Genes_Total'] != '') ? $_GET['Genes_Total'] : ""; ?>">
							</div>

							<div class="col-lg-2 col-md-6 col-sm-12">
								<label class="font-weight-bold">Minimum Genes (Up): </label>
								<input title="Minimum number of up-regulated genes" class="form-control" type="text" id="Genes_Up" name="Genes_Up" value="<?php echo ($_GET['Genes_Up'] != '') ? $_GET['Genes_Up'] : ""; ?>">
							</div>

							<div class="col-lg-2 col-md-6 col-sm-12">
								<label class="font-weight-bold">Minimum Genes (Down): </label>
								<input title="Minimum number of down-regulated genes" class="form-control" type="text" id="Genes_Down" name="Genes_Down" value="<?php echo ($_GET['Genes_Down'] != '') ? $_GET['Genes_Down'] : ""; ?>">
							</div>

						</div>

						<div class="row my-3">

							<div class="col-lg-3 col-md-6 col-sm-12">
								<label class="font-weight-bold">Z-Score Limit: </label>
								<input title="Smaller than a negative number or larger than a positive number" placeholder="Positive (up-regulated) or Negative (down-regulated)" class="form-control" type="text" id="Z_Score" name="Z_Score" value="<?php echo ($_GET['Z_Score'] != '') ? $_GET['Z_Score'] : ""; ?>">
							</div>

							<div class="col-lg-3 col-md-6 col-sm-12">
								<label class="font-weight-bold">P-Value is smaller than: </label>
								<input class="form-control" type="text" id="P_Value" name="P_Value" value="<?php echo ($_GET['P_Value'] != '') ? $_GET['P_Value'] : ""; ?>">
							</div>

							<div class="col-lg-2 col-md-6 col-sm-12">
								<label class="font-weight-bold">FDR is smaller than: </label>
								<input class="form-control" type="text" id="FDF" name="FDF" value="<?php echo ($_GET['FDF'] != '') ? $_GET['FDF'] : "0.25"; ?>">
							</div>

							<div class="col-lg-4 col-md-6 col-sm-12">
								<div class="w-100 mb-2">&nbsp;</div>
								<input class="btn btn-primary" type="submit" value="Submit">
		                        <a class="btn btn-default mr-1" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fas fa-angle-double-right"></i> Reset</a>
								<label id="form_upload_file_busy" class="px-1 hidden text-danger"><i class="fas fa-spinner fa-spin"></i> Submitting ... </label>
							</div>

						</div>

					</div>

                </form>

                <div class="w-100 my-3">
                    <div id="div_results" class="w-100 my-5"></div>
                    <div id="div_debug" class="w-100 my-3"></div>
                </div>

            </div>

		    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>

		</div>

	</div>


	<!-------------------------------------------------------------------------------------------------------->
	<!-- Modal to Select MSigDB -->
	<!-------------------------------------------------------------------------------------------------------->
	<div class="modal" id="modal_select_msigdb_id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Select a MSigDB Term</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="w-100">
						<table class="table table-bordered w-100" id="table_select_msigdb_id">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Genes</th>
									<th>Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>


	<!-------------------------------------------------------------------------------------------------------->
	<!-- Modal to Select Comparison -->
	<!-------------------------------------------------------------------------------------------------------->
	<div class="modal" id="modal_select_comparison_index" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Select a Comparison</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="w-100 my-3">
						<input type="radio" class="select_data_type" name="select_data_type" value="Public" <?php if(! isset($_SESSION['data_type']) || $_SESSION['data_type'] != 'Private') echo "checked"; ?>> Public
						<input type="radio" class="select_data_type" name="select_data_type" value="Private" <?php if(isset($_SESSION['data_type']) && $_SESSION['data_type'] == 'Private') echo "checked"; ?>> Private
					</div>
					<div class="w-100">
						<table class="table table-bordered w-100" id="table_select_comparison_index">
							<thead>
								<tr>
									<th>Comparison Index</th>
									<th>Comparison Name</th>
									<th>Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>




<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css' rel='stylesheet' type='text/css'>
<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js'></script>

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js "></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/1.10.16/sorting/natural.js"></script>

<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js.php'></script>


<script type="text/javascript">

	$(document).ready(function(){

		$('#table_select_msigdb_id').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": "<?php echo $_SERVER['PHP_SELF']; ?>?action=select_msigdb_id",
	            "type": "POST"
	        },
			"paging": true,
			"pageLength": 10,
	        "lengthMenu": [[10, 25, 100, 250, 1000, 5000], [10, 25, 100, 250, 1000, 5000]],
	        "columns": [
				{ "data": "ID" },
				{ "data": "Name" },
				{ "data": "Gene_Counts" },
				{ "data": "Actions" }
	        ]
	    });

        $(document).on('click', '.btn_select_msigdb_id_modal', function() {
        	$('#modal_select_msigdb_id').modal('show');
        });
        $(document).on('click', '.btn_select_msigdb_id', function() {
			$('#msigdb_id').val( $(this).attr('content') );
            $('#msigdb_id_text').html( $(this).attr('displayed_name') );
            $('#modal_select_msigdb_id').modal('hide');
        });



		var myDataTable = $('#table_select_comparison_index').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": "<?php echo $_SERVER['PHP_SELF']; ?>?action=select_comparison_index",
	            "type": "POST"
	        },
			"paging": true,
			"pageLength": 10,
	        "lengthMenu": [[10, 25, 100, 250, 1000, 5000, 10000], [10, 25, 100, 250, 1000, 5000, 10000]],
	        "columns": [
				{ "data": "ComparisonIndex" },
				{ "data": "ComparisonID" },
				{ "data": "Actions" }
	        ]
	    });


		$(document).on('click', '.select_data_type', function() {
			$.ajax({
				url: '<?php echo $_SERVER['PHP_SELF']; ?>?action=save_data_type&data_type=' + $(this).val(),
				success: function(responseText, statusText){
					myDataTable.ajax.reload();
				}
			})
        });

        $(document).on('click', '.btn_select_comparison_index_modal', function() {
			$('#modal_select_comparison_index').modal('show');
        });
        $(document).on('click', '.btn_select_comparison_index', function() {
			$('#comparison_index').val( $(this).attr('content') );
            $('#comparison_index_text').html( $(this).attr('displayed_name') );
            $('#modal_select_comparison_index').modal('hide');
        });


		$(document).on('click', '.content_detail',function(){
			var list_id = $(this).attr('list_id');
			var list_name = $(this).attr('list_name');

			$.ajax({
				url: '<?php echo $_SERVER['PHP_SELF']; ?>?action=get_gene_list&id=' + list_id,
				success: function(responseText, statusText){
					bootbox.alert({
						title: list_name,
						message: responseText,
						callback: function(){}
					});
				}
			})
		})



        // File Upload
        var options = {
            url: '<?php echo $_SERVER['PHP_SELF']; ?>?action=select_msigdb_page_list',
            type: 'post',
            beforeSubmit: function(formData, jqForm, options) {

                $('#form_upload_file_busy').removeClass('hidden');

                return true;
            },
            success: function(response){
                $('#form_upload_file_busy').addClass('hidden');
                $('#div_results').html(response);

				$('#table_search_results').DataTable({
				    dom: "Blfrtip",
				    buttons: [
				        'copy', 'csv', 'excel', 'pdf', 'print'
				    ],
					"paging": true,
					"pageLength": 25,
				    "lengthMenu": [[25, 100, 250, 1000, 5000], [25, 100, 250, 1000, 5000]],
				    "order": [[4, 'asc']]
				});

                return true;
            }
        };
        $('#form_main').ajaxForm(options);


	});

</script>


</body>
</html>