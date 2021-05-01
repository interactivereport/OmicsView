<?php
include_once('config.php');

if (isset($_GET['action']) && $_GET['action'] == 'search_comparison') {
	//echo '<pre>'; print_r($_POST); echo '</pre>';

	$TYPE = trim($_POST['page_type']);

	switch ($TYPE) {
		case "Sample":
			$TABLE = $BXAF_CONFIG['TBL_SAMPLES'];
			break;
		case "Gene":
			$TABLE = $BXAF_CONFIG['TBL_GENECOMBINED'];
			break;
		case "Project":
			$TABLE = $BXAF_CONFIG['TBL_PROJECTS'];
			break;
		default:
			$TABLE = $BXAF_CONFIG['TBL_COMPARISONS'];
	}

	$SQL = "SELECT * FROM `{$TABLE}` WHERE";

	$SQL_APPEND = "";
	for ($i = 0; $i < count($_POST['search_field']); $i++) {
		$SQL_APPEND .= " ";

		// 1. Logic
		if ($i > 0) {
			$SQL_APPEND .= strtoupper($_POST['search_logic'][$i]);
			$SQL_APPEND .= " ";
		}

		// 2. Field Name
		$SQL_APPEND .= "`" . trim($_POST['search_field'][$i]) . "`";


		// 3. Operator & Value
		if ($_POST['search_operator'][$i] == 'is') {
			$SQL_APPEND .= "='";
			$SQL_APPEND .= addslashes(trim($_POST['search_value'][$i]));
			$SQL_APPEND .= "'";
		}
		else {
			$SQL_APPEND .= " LIKE ";
			if ($_POST['search_operator'][$i] == 'contains') {
				$SQL_APPEND .= "'%" . addslashes(trim($_POST['search_value'][$i])) . "%'";
			}
			if ($_POST['search_operator'][$i] == 'starts_with') {
				$SQL_APPEND .= "'" . addslashes(trim($_POST['search_value'][$i])) . "%'";
			}
			if ($_POST['search_operator'][$i] == 'ends_width') {
				$SQL_APPEND .= "'%" . addslashes(trim($_POST['search_value'][$i])) . "'";
			}
		}
	}

	$SQL .= $SQL_APPEND;

	//echo $SQL_APPEND; exit();

	$COMPARISONS = $DB -> get_all($SQL);


	echo '
	<input id="number_records" value="' . count($COMPARISONS) . '" hidden>

	<table class="table table-striped datatable table-bordered" style="font-size:14px;"><thead>';


	$col_setting = unserialize($BXAF_CONFIG['PREFERENCE_DETAIL'][strtolower($TYPE).'_search_page_table_column']);
	echo '<th>' . $PAGE_TYPE . 'ID</th>';
	foreach ($col_setting as $colname) {
		echo '<th>' . $colname . '</th>';
	}



	echo '</thead><tbody></tbody></table>';

	echo '
	<script>
	$(document).ready(function() {
		//$(\'.datatable\').DataTable();


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

		$(\'.datatable\').DataTable({
      "dom": \'lBfrtip\',
  		"lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
      // "buttons": [
      //   \'copy\', \'csv\', \'excel\', \'pdf\', \'print\'
      // ],
			buttons: [
	      $.extend( true, {}, buttonCommon, {
	          extend: \'copyHtml5\'
	      } ),
	      $.extend( true, {}, buttonCommon, {
	          extend: \'excelHtml5\'
	      } ),
	      $.extend( true, {}, buttonCommon, {
	          extend: \'csvHtml5\'
	      } ),
	      $.extend( true, {}, buttonCommon, {
	          extend: \'pdfHtml5\'
	      } )
	    ],
			"processing": true,
			"serverSide": true,
			"language": {
  	    "infoFiltered": ""
  	  },
			"ajax": {
				"url": "exe.php?action=data_table_dynamic_loading&type=' . $TYPE . '",
				"type": "POST",
				"data": {"sql": "' . addslashes($SQL_APPEND) . '"}
			},
			"iDisplayLength": 10,
			"columns": [';


			// Return table content filtered by type
			switch ($TYPE) {
				case "Sample":
					echo '
						{ "data": "SampleID", render: function(data) {
							var list = data.split("__");
							var name = list[0];
							var index = list[1];
							var content = name;
              content += \'&nbsp;&nbsp;<input type="checkbox" class="checkbox_save_session"\';
    					content += \' rowid="\' + index + \'">\';
							content += \'&nbsp;&nbsp;<a href="single_comparison.php?type=sample&id=\' + index
							content += \'" data-tootik="View Detail" target="_blank"><i class="fa fa-list-ul"></i></a>\';
							return content;
						} },';
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
					break;
				case "Gene":
					echo '
						{ "data": "GeneID", render: function(data) {
							var list = data.split("__");
							var name = list[0];
							var index = list[1];
							var content = name;
              content += \'&nbsp;&nbsp;<input type="checkbox" class="checkbox_save_session"\';
    					content += \' rowid="\' + index + \'">\';
              content += \'&nbsp;&nbsp;<a href="single_comparison.php?type=gene&id=\' + index;
							content += \'" data-tootik="View Detail" target="_blank"><i class="fa fa-list-ul"></i></a>\';
							return content;
						} },';
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
					break;
				case "Project":
					echo '
						{ "data": "ProjectID", render: function(data) {
							var list = data.split("__");
							var name = list[0];
							var index = list[1];
							var content = name + \'&nbsp;&nbsp;<a href="single_comparison.php?type=project&id=\' + index
							content += \'" data-tootik="View Detail" target="_blank"><i class="fa fa-list-ul"></i></a>\';
							return content;
						} },';
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
					break;
				default:
					echo '
					{ "data": "ComparisonID", render: function(data) {
						var list = data.split("__");
						var comparison_name = list[0];
						var comparison_index = list[1];
						var content = comparison_name;
            content += \'<br /><input type="checkbox" class="checkbox_save_session"\';
  					content += \' rowid="\' + comparison_index + \'">\';
						content += \'&nbsp;&nbsp;<a href="single_comparison.php?id=\';
						content += comparison_index;
						content += \'" data-tootik="View Detail" target="_blank" class="btn_view_detail">\';
						content += \'<i class="fa fa-list-ul"></i></a>\';
						content += \'&nbsp;&nbsp;<a href="../volcano/index.php?id=\';
						content += comparison_index;
						content += \'" data-tootik="View Volcano Plot" class="btn_view_volcano_plot">\';
						content += \'<i class="fa fa-pie-chart"></i></a>\';
						content += \'&nbsp;&nbsp;<a href="../pvjs/index.php?id=\';
						content += comparison_index;
						content += \'" data-tootik="View Pathway" class="btn_view_pvjs">\';
						content += \'<i class="fa fa-bar-chart"></i></a>\';
						return content;
					} },';
					foreach ($col_setting as $colname) {
						echo '{ "data": "' . $colname . '" },';
					}
			}

	echo '
			]
		});


	});
	</script>';


	exit();
}





if (isset($_GET['action']) && $_GET['action'] == 'view_comparison_detail') {
	// print_r($_POST);
	$ROWID = intval($_POST['rowid']);
	$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $ROWID;
	$data = $DB -> get_row($sql);
	echo '<p><strong>' . $data['ComparisonID'] . '</strong></p>';

	echo '
	<div style="overflow-y: scroll; height: 70vh;">
		<table class="table table-striped table-bordered">';

		foreach ($data as $key => $value) {
			if ($key != 'ComparisonID') {
				echo '
				<tr>
					<td>' . $key . '</td>
					<td>' . $value . '</td>
				</tr>';
			}
		}

	echo '
		</table>
	</div>';

	exit();
}







if (isset($_GET['action']) && $_GET['action'] == 'data_table_dynamic_loading') {

	$TYPE = trim($_GET['type']);

	switch ($TYPE) {
		case "Sample":
			$TABLE = $BXAF_CONFIG['TBL_SAMPLES'];
			break;
		case "Gene":
			$TABLE = $BXAF_CONFIG['TBL_GENECOMBINED'];
			break;
		case "Project":
			$TABLE = $BXAF_CONFIG['TBL_PROJECTS'];
			break;
		default:
			$TABLE = $BXAF_CONFIG['TBL_COMPARISONS'];
	}

	$sql = "SELECT * FROM `{$TABLE}`";


	// Search Condition
	if(isset($_POST['search']['value']) && trim($_POST['search']['value']) != '') {
		$search_array = array();
		for ($i = 0; $i < count($_POST['columns']); $i++){
			$search_array[] = "`" . $_POST['columns'][$i]['data'] . "` LIKE '%" . $_POST['search']['value'] . "%'";
		}
		$sql .= " WHERE (" . implode(" OR ", $search_array) . ")";
		// Condition from Advanced Search
		if (isset($_POST['sql']) && trim($_POST['sql']) != '') {
			$sql .= " AND" . $_POST['sql'];
		}
	} else {
		if (isset($_POST['sql']) && trim($_POST['sql']) != '') {
			$sql .= " WHERE" . $_POST['sql'];
		}
	}




	// Order Condition
	$sql .= " ORDER BY ";
	$condition_array = array();
	for ($i = 0; $i < count($_POST['order']); $i++) {
		$condition_array[] = "`" . $_POST['columns'][$_POST['order'][$i]['column']]['data'] . "` " . $_POST['order'][$i]['dir'] . "";
	}
	$sql .= implode(", ", $condition_array);

	$data = $DB -> get_all($sql);



	$count = count($data);
	$output_array = array(
		'draw' => intval($_POST['draw']),
		'recordsTotal' => $recordsTotal,
		'recordsFiltered' => $count,
		'data' => array(),
		'sql' => $sql
	);



	foreach($data as $key => $value) {
		if($key >= intval($_POST['start']) && $key < intval($_POST['start'] + $_POST['length'])){

			// Combine ID and Index
			$value[$TYPE . 'ID'] = $value[$TYPE . 'ID'] . '__' . $value[$TYPE . 'Index'];


			$output_array['data'][] = $value;
		}
	}
	echo json_encode($output_array);




	exit();
}


//-----------------------------------------------------------------------------
// Save to session
if (isset($_GET['action']) && $_GET['action'] == 'save_to_session') {
  // print_r($_POST);
  $TYPE = $_POST['type'];

  if (!isset($_POST['data_list'])
      || !is_array($_POST['data_list'])
      || count($_POST['data_list']) <= 0) {
    echo 'Error: No ' . $TYPE . ' selected.';
    exit();
  }

  $DATA = $_POST['data_list'];


  // For Projects, search samples
  if ($TYPE == 'project') {
    $sample_index_list = array();

    foreach ($DATA as $project_id) {
      // Get project ID
      $sql = "SELECT `ProjectID` FROM `{$BXAF_CONFIG['TBL_PROJECTS']}`
              WHERE `ProjectIndex`=" . $project_id;
      $project_name = $DB -> get_one($sql);

      // Get sample index
      $sql = "SELECT `SampleIndex` FROM `{$BXAF_CONFIG['TBL_SAMPLES']}`
              WHERE `ProjectName`='" . $project_name . "'";
      $samples = $DB -> get_all($sql);

      foreach ($samples as $sample) {
        $sample_index_list[] = $sample['SampleIndex'];
      }
    }
    // Save to session
    // if (!isset($_SESSION['SAVED_SAMPLE'])
    //     || !is_array($_SESSION['SAVED_SAMPLE'])) {
    //   $_SESSION['SAVED_SAMPLE'] = $sample_index_list;
    // } else {
    //   $_SESSION['SAVED_SAMPLE'] = array_unique(array_merge($_SESSION['SAVED_SAMPLE'], $sample_index_list));
    // }
    $name_list = array();
    foreach ($sample_index_list as $index) {
      $sql = "SELECT `SampleID` FROM `{$BXAF_CONFIG['TBL_SAMPLES']}`
              WHERE `SampleIndex`=" . $index;
      $name_list[] = $DB -> get_one($sql);
    }
    $uniqueID = md5(microtime(true));
    $_SESSION['List'][$uniqueID] = $name_list;
    echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Sample&Session={$uniqueID}";
    exit();
  }


// unset($_SESSION['SAVED_GENE']);
  // For others



  if ($TYPE == 'gene') {
    $name_list = array();
    foreach ($DATA as $index) {
      $sql = "SELECT `GeneID` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
              WHERE `GeneIndex`=" . $index;
      $name_list[] = $DB -> get_one($sql);
    }
    $uniqueID = md5(microtime(true));
    $_SESSION['List'][$uniqueID] = $name_list;
    echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Gene&Session={$uniqueID}";
  }

  if ($TYPE == 'comparison') {
    $name_list = array();
    foreach ($DATA as $index) {
      $sql = "SELECT `ComparisonID` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
              WHERE `ComparisonIndex`=" . $index;
      $name_list[] = $DB -> get_one($sql);
    }
    $uniqueID = md5(microtime(true));
    $_SESSION['List'][$uniqueID] = $name_list;
    echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Comparison&Session={$uniqueID}";
  }

  if ($TYPE == 'sample') {
    $name_list = array();
    foreach ($DATA as $index) {
      $sql = "SELECT `SampleID` FROM `{$BXAF_CONFIG['TBL_SAMPLES']}`
              WHERE `SampleIndex`=" . $index;
      $name_list[] = $DB -> get_one($sql);
    }
    $uniqueID = md5(microtime(true));
    $_SESSION['List'][$uniqueID] = $name_list;
    echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Sample&Session={$uniqueID}";
  }
  

  exit();
}






//-----------------------------------------------------------------------------
// Save samples of a project
if (isset($_GET['action']) && $_GET['action'] == 'project_save_samples') {
  // print_r($_POST);
  $project_name = $_POST['project_name'];
  $sql = "SELECT `SampleID` FROM `{$BXAF_CONFIG['TBL_SAMPLES']}`
          WHERE `ProjectName`='{$project_name}'";
  $data = $DB -> get_all($sql);

  $name_list = array();
  foreach ($data as $row) {
    $name_list[] = $row['SampleID'];
  }
  $uniqueID = md5(microtime(true));
  $_SESSION['List'][$uniqueID] = $name_list;
  echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_list_new.php?Category=Sample&Session={$uniqueID}";

 
  exit();
}




?>
