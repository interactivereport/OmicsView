<?php
include_once('config.php');
include_once('../profile/config.php');

if (isset($_GET['action']) && $_GET['action'] == 'bubble_pre_generate_chart') {
	include('exe_bubble_pre_generate_chart.php');
	exit();
} else if (isset($_GET['action']) && $_GET['action'] == 'bubble_generate_chart') {
	include('exe_bubble_generate_chart.php');
	exit();	
} else if (isset($_GET['action']) && $_GET['action'] == 'show_table') {
	//print_r($_POST); exit();


	$dir = $BXAF_CONFIG['USER_FILES_BUBBLE'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];

	$fh = fopen($dir . '/y_field_options.txt', 'r') or die('Cannot open file!');
	$line = fgets($fh);
	$Y_INDEX = unserialize($line);
	fclose($fh);
	$Y_INDEX_FLIP = array_flip($Y_INDEX);




	$fh = fopen($dir . '/all_genes.txt', 'r') or die('Cannot open file!');
	$line = fgets($fh);
	$ALL_GENES = unserialize($line);
	fclose($fh);


	$ALL_DATA_POINTS = array();

	// For each selected points
	for ($i = 0; $i < count($_POST['x']); $i++) {
		// Find the right gene
		foreach ($ALL_GENES as $value) {
			if ($value['x'] == $_POST['x'][$i] && $Y_INDEX_FLIP[$value['y']] == $_POST['y'][$i]) {
				$comparison_index = $value['comparison_index'];
				$gene_index = $value['gene_index'];
				$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONDATA']}`
						WHERE `ComparisonIndex`=$comparison_index
						AND `GeneIndex`=$gene_index
						AND `Log2FoldChange`='" . $value['x'] . "'";
				$data = $DB -> get_row($sql);
				$ALL_DATA_POINTS[] = $data;
				break;
			}
		}
	}


	echo '
	<table class="table table-hover table-bordered table-striped">
		<thead>
			<th>ComparisonID</th>
			<th>GeneID</th>
			<th>Name</th>
			<th>LogFC</th>
			<th>P-Value</th>
			<th>Adj P-Value</th>
		</thead>
		<tbody>';

		foreach ($ALL_DATA_POINTS as $point) {
			$sql = "SELECT `ComparisonID` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
              WHERE `ComparisonIndex`=" . $point['ComparisonIndex'];
      $comparison_id = $DB -> get_one($sql);
      $sql = "SELECT `GeneID` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
              WHERE `GeneIndex`=" . $point['GeneIndex'];
      $gene_id = $DB -> get_one($sql);
			echo '
			<tr>
				<td>' . $comparison_id . '</td>
				<td>' . $gene_id . '</td>
				<td>' . $point['Name'] . '</td>
				<td>' . $point['Log2FoldChange'] . '</td>
				<td>' . $point['PValue'] . '</td>
				<td>' . $point['AdjustedPValue'] . '</td>
			</tr>';
		}

	echo '
		</tbody>
	</table>';


	exit();
}






else if (isset($_GET['action']) && $_GET['action'] == 'customize_sort') {
	//print_r($_POST); exit();

	$GENE_ID = intval(trim($_POST['gene_id']));
	$CATEGORY = trim($_POST['category']);
	$TYPE = trim($_POST['type']);
	$FIELD = trim($_POST['field']);



	// $sql = "SELECT `ComparisonIndex`, `Log2FoldChange`, `PValue`, `AdjustedPValue`
	// 		FROM `{$BXAF_CONFIG['TBL_COMPARISONDATA']}`
	// 		WHERE `GeneIndex`=" . $GENE_ID;
	// $data_comparison = $DB -> get_all($sql);

  // Tabix
	$geneIndex = array($GENE_ID);
	$data_comparison = tabix_search_records_with_index($geneIndex, '', 'ComparisonData');



	$Y_FIELD_LIST = array();
	$COLORING_FIELD_LIST = array();
	$FIELD_NUMBER = array(); // Appear times

	foreach ($data_comparison as $comparison) {
		$sql = "SELECT `{$FIELD}` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonIndex`=" . $comparison['ComparisonIndex'];
		$comparison_row = $DB -> get_row($sql);


		if (trim($comparison['Log2FoldChange']) == ''
			|| trim($comparison['Log2FoldChange']) == '.'
			|| trim($comparison['Log2FoldChange']) == 'NA'
			|| trim($comparison['PValue']) == ''
			|| trim($comparison['PValue']) == '.'
			|| trim($comparison['PValue']) == 'NA'
			|| trim($comparison_row[$FIELD]) == ''
			|| trim($comparison_row[$FIELD]) == 'NA') {
			continue;
		}



		if (!in_array($comparison_row[$FIELD], array_keys($FIELD_NUMBER))) {
			$FIELD_NUMBER[$comparison_row[$FIELD]] = 1;
		} else {
			$FIELD_NUMBER[$comparison_row[$FIELD]] += 1;
		}

	}

	if ($TYPE == 'category') {
		ksort($FIELD_NUMBER);
	} else {
		arsort($FIELD_NUMBER);
	}

	echo '<div style="height:70vh; overflow-y:scroll;">
				<table class="table table-bordered table-striped" style="font-size:14px;">
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Occurence</th>
					</tr>';
					foreach ($FIELD_NUMBER as $key => $value) {
						echo '
						<tr>
							<td><input type="checkbox" class="checkbox_customize_' . $CATEGORY . '" name="' . $CATEGORY . '_' . $key . '" value="' . $key . '"></td>
							<td>' . $key . '</td>
							<td>' . $value . '</td>
						</tr>';
					}
	echo '
				</table>
			</div>';


	exit();
}






// ---------------------------------------------------------------------------------------------------------
// Generate Chart for Genes .vs. Comparisons
else if (isset($_GET['action']) && $_GET['action'] == 'genes_comparisons_generate_chart') {
	include('exe_genes_comparisons_generate_chart.php');
	exit();
}






// ---------------------------------------------------------------------------------------------------------
// Show Gene Set Modal
else if (isset($_GET['action']) && $_GET['action'] == 'load_gene_set_modal') {

	$sql = "SELECT `ID`," . implode(",", array_keys($CONFIG_PROFILE['BUBBLE_PLOT']['geneset_col'])) . " FROM `{$BXAF_CONFIG['TBL_GENESET']}`";
  $data = $DB -> get_all($sql);

  $table = '<table class="table datatable table-bordered table-striped" style="width:100% !important;">
  <thead>
    <tr>';

		foreach ($CONFIG_PROFILE['BUBBLE_PLOT']['geneset_col'] as $colname) {
			$table .= '<th>' . $colname . '</th>';
		}

	$table .= '
    </tr>
  </thead>
  <tbody>';
  foreach ($data as $row) {
    $table .= '
    <tr>
      <td>
        <a href="javascript:void(0);" class="btn_select_one_gene_set" rowid="' . $row['ID'] . '">
        ' . $row['StandardName'] . '
      </td>';

			foreach ($CONFIG_PROFILE['BUBBLE_PLOT']['geneset_col'] as $key => $colname) {
				if ($key != 'StandardName') {
					$table .= '<td>' . $row[$key] . '</td>';
				}
			}

		$table .= '
    </tr>';
  }
  $table             .= '</tbody></table>';

  $table = str_replace("\n", "", $table);
  echo $table;
  // echo str_replace(" ", "", $table);

  // $OUTPUT             = array();
  // $OUTPUT['table']    = $table;
  //
  // header('Content-Type: application/json');
  // echo json_encode($OUTPUT);
  exit();

}





// ---------------------------------------------------------------------------------------------------------
// Show Gene Set Modal
else if (isset($_GET['action']) && $_GET['action'] == 'get_gene_set_detail') {
  $ROWID = $_POST['rowid'];
  $sql                = "SELECT `Members`
                         FROM `{$BXAF_CONFIG['TBL_GENESET']}`
                         WHERE `ID`={$ROWID}";
  $members            = $DB -> get_one($sql);
  echo $members;
}




// ---------------------------------------------------------------------------------------------------------
// Load Session Info
else if (isset($_GET['action']) && $_GET['action'] == 'load_session_info') {
  //print_r($_POST);
  $TYPE = strtoupper($_POST['type']);
  $index_list = $_SESSION['SAVED_' . $TYPE];

  if ($TYPE == 'GENE') {
	$table = $BXAF_CONFIG['TBL_GENECOMBINED'];
    $name_col = 'GeneName';
    $index_col = 'GeneIndex';
  } else {
    $table = $BXAF_CONFIG['TBL_COMPARISONS'];
    $name_col = 'ComparisonID';
    $index_col = 'ComparisonIndex';
  }

  $ALL_NAMES = array();
  foreach ($index_list as $index) {
    $sql = "SELECT `{$name_col}` FROM `{$table}`
            WHERE `{$index_col}`=$index";

    $name = $DB -> get_one($sql);
    $ALL_NAMES[] = $name;
  }

  echo implode(",", $ALL_NAMES);

	exit();
}




//********************************************************************************************
// Go To Derrick's Export Tool
//********************************************************************************************

if (isset($_GET['action']) && trim($_GET['action']) == 'go_to_export_tool') {
  // print_r($_POST);
  $_SESSION['META_SELECTED_GENENAMES'] = explode("\n", $_POST['genes']);
  $_SESSION['META_SELECTED_COMPNAMES'] = explode("\n", $_POST['comps']);

  foreach ($_SESSION['META_SELECTED_GENENAMES'] as $key => $value) {
    if (trim($value) == '') {
      unset($_SESSION['META_SELECTED_GENENAMES'][$key]);
    }
  }
  foreach ($_SESSION['META_SELECTED_COMPNAMES'] as $key => $value) {
    if (trim($value) == '') {
      unset($_SESSION['META_SELECTED_COMPNAMES'][$key]);
    }
  }


  // print_r($_SESSION['META_SELECTED_GENENAMES']);
  // print_r($_SESSION['META_SELECTED_COMPNAMES']);
  exit();
}








?>
