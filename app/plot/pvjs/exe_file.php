<?php
include_once('config.php');


// Add Comparison Form

if (isset($_GET['action']) && $_GET['action'] == 'add_comparison') {

	if (isset($_POST['default_value']) && trim($_POST['default_value']) != '') {
		$DEFAULT_VALUE = trim($_POST['default_value']);
	} else {
		$DEFAULT_VALUE = '';
	}
	$LENGTH = intval($_POST['length']);
  $TIME = time();

  // Output JSON Data
  header('Content-Type: application/json');
  $OUTPUT = array();
  $OUTPUT['time'] = $TIME;
  echo json_encode($OUTPUT);

	exit();
}





// Upload File

if (isset($_GET['action']) && $_GET['action'] == 'upload_file') {
  // print_r($_FILES); exit();
  header('Content-Type: application/json');
  $TIME = intval($_POST['time']);
  $OUTPUT = array('time' => $TIME);

  // Create Folders
  if (!is_dir(dirname(__FILE__) . '/files/user_csv')) {
      mkdir(dirname(__FILE__) . '/files/user_csv', 0755, true);
  }
  $dir = dirname(__FILE__) . '/files/user_csv/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
  }


  // Upload Files
  if(isset($_FILES["file"])) {
      //Filter the file types , if you want.
      if ($_FILES["file"]["error"] > 0) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
      }
      else {

        $tmp_name = $_FILES["file"]["tmp_name"];
        $name = $_FILES["file"]["name"];
        $size = $_FILES["file"]["size"];
        $type = $_FILES["file"]["type"];
        // check file type
        if (!in_array($type, array('application/vnd.ms-excel','text/plain','text/csv','text/tsv'))) {
          $OUTPUT['type'] = 'Error';
          $OUTPUT['detail'] = 'Please upload a csv file.';
          echo json_encode($OUTPUT);
          exit();
        };
        //move the uploaded file to uploads folder;
        move_uploaded_file($tmp_name, $dir . '/' . $TIME . '.csv');

        // if (isset($_SESSION['pvjs_files'])
        //     && !in_array($TIME, array_keys($_SESSION['pvjs_files']))) {
        //     $_SESSION['pvjs_files'][$TIME] = array();
        // }

        $_SESSION['pvjs_files'][$TIME] = array();
        $file = fopen($dir . '/' . $TIME . '.csv', "r");
        $index = 0;
        while(!feof($file)){
          $row_content = fgetcsv($file);
          // Header row
          if ($index == 0) {
            $OUTPUT['header'] = $row_content;
            $_SESSION['pvjs_files'][$TIME]['header'] = $row_content;
          }
          // Data rows
          else {
            $row_id = trim($row_content[0]);

            // Check data type
            $OUTPUT['ID_type'] = 'unmatched';
            // 1. Symbol
            $sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}` WHERE `GeneName`='{$row_id}'";
            $data = $DB -> get_row($sql);
            if (is_array($data) && count($data) > 1) {
              $OUTPUT['ID_type'] = 'GeneName'; break;
            }
            // 2. Entrez ID
            $sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}` WHERE `EntrezID`={$row_id}";
            $data = $DB -> get_row($sql);
            if (is_array($data) && count($data) > 1) {
              $OUTPUT['ID_type'] = 'EntrezID'; break;
            }
            // 3. Ensembl ID
            $sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}` WHERE `Ensembl`='{$row_id}'";
            $data = $DB -> get_row($sql);
            if (is_array($data) && count($data) > 1) {
              $OUTPUT['ID_type'] = 'Ensembl'; break;
            }
            // 4. Uniprot
            $sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}` WHERE `Uniprot`='{$row_id}'";
            $data = $DB -> get_row($sql);
            if (is_array($data) && count($data) > 1) {
              $OUTPUT['ID_type'] = 'Uniprot'; break;
            }

          }

          $index++;
        }
        fclose($file);
      }
  }


  $_SESSION['pvjs_files'][$TIME]['ID_type'] = $OUTPUT['ID_type'];
  if ($OUTPUT['ID_type'] == 'unmatched') {
    $OUTPUT['type'] = 'Error';
    $OUTPUT['detail'] = 'First column not match. Please retry';
    echo json_encode($OUTPUT);
    exit();
  }


  $OUTPUT['type'] = 'Success';
  echo json_encode($OUTPUT);
  exit();
}




// Generate PVJS Chart

if (isset($_GET['action']) && $_GET['action'] == 'pvjs_generate_chart') {
  // echo '<pre>'; print_r($_POST); echo '</pre>';
  // print_r($_POST);
  header('Content-Type: application/json');
  if ($_POST['pathway'] == '') {
    $OUTPUT['type'] = 'Error';
    $OUTPUT['detail'] = 'Please select a pathway.';
    echo json_encode($OUTPUT);
    exit();
  }
  if (!isset($_SESSION['pvjs_files']) || count($_SESSION['pvjs_files']) == 0) {
    $OUTPUT['type'] = 'Error';
    $OUTPUT['detail'] = 'Please upload files for data visualization.';
    echo json_encode($OUTPUT);
    exit();
  }

  /*************************************
   *
   * 1. Check the Pathway is Reactome or Not
   * 2. Get Genes in the Pathway File
   * 3. Generate File Gene ID => Symbol Lookup Pair List
   * 4. Loop through All Pathway Genes
   *    4.1. Find gene index
   *         4.1.1. If database is 'Entrez Gene'
   *         4.1.2. If database is 'Ensembl'
   *         4.1.3. If database is 'Uniprot-TrEMBL'
   *
   *
   *
   *************************************/

  // 1. Check the Pathway is Reactome or Not
	$pathway_label = trim($BXAF_CONFIG['PATHWAY_LIST'][$_POST['pathway']]);
	if (substr($pathway_label, strlen($pathway_label) - 10) == '(Reactome)') {
		$folder_name = $CONFIG_PROFILE['PVJS']['pathway_dir'] . '_reactome';
	}
  else {
		$folder_name = $CONFIG_PROFILE['PVJS']['pathway_dir'];
	}

  // 2. Get Genes in the Pathway File
	$file_dir = dirname(__FILE__) . '/files/pathway/' . $folder_name . '/' . $_POST['pathway'];
	$genes_in_pathway = extract_gene_id($file_dir);



  // 3. Generate File Gene ID => Symbol Lookup Pair List
  $dir = dirname(__FILE__) . '/files/user_csv/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  $FILE_GENE_PAIR = array();

  foreach ($_POST['time_list'] as $file_index => $file_time) {
    $FILE_GENE_PAIR[$file_time] = array();
    $file = fopen($dir . '/' . $file_time . '.csv', "r");
    $index = 0;
    while(!feof($file)){
      $row_content = fgetcsv($file);
      if ($index > 0) {
        $file_id_type = $_SESSION['pvjs_files'][$file_time]['ID_type'];
        $sql = "SELECT `GeneIndex` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
                WHERE `{$file_id_type}`='" . $row_content[0] . "'";
        $symbol_temp = $DB -> get_one($sql);
        $FILE_GENE_PAIR[$file_time][$row_content[0]] = $symbol_temp;
      }
      $index++;
    }
    fclose($file);
  }


  // echo '<pre>'; print_r($genes_in_pathway); echo '</pre>'; exit();


  // 4. Loop through All Pathway Genes
	$ALL_COLORING_GENE = array();
	$ALL_GENE_COMPARISON_INFO = array();

  foreach ($genes_in_pathway as $genename => $geneinfo) {

		$ALL_COLORING_GENE[$genename] = array(
			'Database' => $geneinfo['Database'],
			'Database_ID' => $geneinfo['ID'],
			'Gene_Index' => 0,
			'Color' => array()
		);
    $ALL_GENE_COMPARISON_INFO[$genename] = array();

    // 4.1. Find gene index
		// 4.1.1. If database is 'Entrez Gene'
		if ($geneinfo['Database'] == 'Entrez Gene') {
			$entrez_id = intval($geneinfo['ID']);
			$sql = "SELECT `GeneIndex`
				FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
				WHERE `EntrezID`=" . intval($geneinfo['ID']);
			$gene_index = $DB -> get_one($sql);
		}
		// 4.1.2. If database is 'Ensembl'
		else if ($geneinfo['Database'] == 'Ensembl') {
			$sql = "SELECT `GeneID`
				FROM `{$BXAF_CONFIG['TBL_GENELIST']}`
				WHERE `Ensembl`='" . trim($geneinfo['ID']) . "'";
			$entrez_id = $DB -> get_one($sql);
			$sql = "SELECT `GeneIndex`
				FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
				WHERE `EntrezID`=" . intval($entrez_id);
			$gene_index = $DB -> get_one($sql);
		}
		// 4.1.3. If database is 'Uniprot-TrEMBL'
		else if ($geneinfo['Database'] == 'Uniprot-TrEMBL') {
			$sql = "SELECT `GeneID`
				      FROM `{$BXAF_CONFIG['TBL_GENELIST']}`
				      WHERE `Uniprot`='" . trim($geneinfo['ID']) . "'";
			$entrez_id = $DB -> get_one($sql);
			$sql = "SELECT `GeneIndex`
				      FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
				      WHERE `EntrezID`=" . intval($entrez_id);
			$gene_index = $DB -> get_one($sql);
		}
    $ALL_COLORING_GENE[$genename]['Gene_Index'] = $gene_index;


    // 4.2. Find current gene for each comparison
    foreach ($_POST['time_list'] as $file_index => $file_time) {

      // 4.2.1. Find Visualization Column Index
      $col_index = 0;
      foreach ($_SESSION['pvjs_files'][$file_time]['header'] as $key => $value) {
        if ($_POST['column_list']['file_index'] == $value) {
          $col_index = $key;
        }
      }

      // 4.2.2. Read Uploaded File and Find Genes Appeared in Pathway
      $file = fopen($dir . '/' . $file_time . '.csv', "r");
      $index = 0;
      $row = array();
      while(!feof($file)){
        $row_content = fgetcsv($file);
        if ($index > 0) {
          if ($FILE_GENE_PAIR[$file_time][$row_content[0]] == $ALL_COLORING_GENE[$genename]['Gene_Index']) {
            for ($i = 0; $i < count($row_content); $i++) {
              $row[$_SESSION['pvjs_files'][$file_time]['header'][$i]] = $row_content[$i];
            }
            $ALL_GENE_COMPARISON_INFO[$genename][] = $row;
          }
        }
        $index++;
      }
      fclose($file);


      // 4.2.3. Set Color
      if (count($row) <= 0) {
        $ALL_COLORING_GENE[$genename]['Color'][] = 'CCCCCC';
        $color_temp = 'CCCCCC';
      }
      else {
        // print_r($row);
        $mapped_value = $row[$_POST['column_list'][$file_index]];

        // Gradient color
        if ($_POST['visualization_list'][$file_index] < 2) {
          $color_temp = mapToGradient($mapped_value, intval($_POST['visualization_list'][$file_index]) + 1);
        }
        // Cutoff color
        else {
          $color_temp = mapToGradientPValue($mapped_value, intval($_POST['visualization_list'][$file_index]) - 2);
        }

        $ALL_COLORING_GENE[$genename]['Color'][] = $color_temp;
      }

    }

  }





  $file_dir = $BXAF_CONFIG_CUSTOM['USER_FILES_PVJS'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
	if (!is_dir($file_dir)) {
		mkdir($file_dir . '/', 0775, true);
	}
	file_put_contents($file_dir . '/comparison_info.txt', serialize($ALL_GENE_COMPARISON_INFO));

	//echo htmlentities($LEGEND_INFO); //exit();

	$LEGEND_INFO = addslashes(str_replace("\n", '', $LEGEND_INFO));

	// Output
	$GENERAL_OUTPUT = '
	<div class="btn-group hidden" role="group"  style="margin-bottom:1em;">
		<button id="btn_save_svg" class="btn btn-default"><i class="fa fa-download"></i> Download SVG</button>
		<button id="btn_view_svg_new_window" class="btn btn-default"><i class="fa fa-eye"></i> View SVG in new window</button>
	</div>';


	$CHART_OUTPUT = '';

	$CHART_OUTPUT .= '
	<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="js/jquery.layout.min-1.3.0.js"></script>
	<script type="text/javascript" src="js/d3.min.js"></script>
	<script type="text/javascript" src="js/mithril.min.js"></script>
	<script type="text/javascript" src="js/polyfills.bundle.min.js"></script>
	<script type="text/javascript" src="js/pvjs.core.min.js"></script>
	<script type="text/javascript" src="js/pvjs.custom-element.min.js"></script>
	<wikipathways-pvjs
		id="pvjs-widget"
		src="./files/pathway/' . $folder_name . '/' . $_POST['pathway'] . '"
		display-errors="true"
		display-warnings="true"
		fit-to-container="true"
		editor="disabled">
	</wikipathways-pvjs>
	<script>
	kaavioHighlights = [';

		// Define Area ID
		foreach ($ALL_COLORING_GENE as $key => $value) {
			$CHART_OUTPUT .= '{"selector":"' . $key . '","backgroundColor":"url(#solids_' . str_replace(' ', '_', $key) . ')","borderColor":"#B0B0B0"},';
		}

	$CHART_OUTPUT .= '
	]
	</script>';

	$CHART_OUTPUT .= "
	<script>
	checkReady();
	function checkReady() {
		if ($('svg')[0] == null) {
			setTimeout('checkReady()', 300);
		} else {

			$('#btn_save_svg').parent().removeClass('hidden');

			createGradient($('svg')[0],'gradient_0',[
				{offset:'5%', 'stop-color':'#0000FF'},
				{offset:'50%','stop-color':'#FFFFFF'},
				{offset:'95%','stop-color':'#FF0000'}
			]);
			createGradient($('svg')[0],'gradient_1',[
				{offset:'5%', 'stop-color':'#008000'},
				{offset:'50%','stop-color':'#FFFFFF'},
				{offset:'95%','stop-color':'#FF0000'}
			]);
			createGradient($('svg')[0],'gradient_2',[
				{offset:'5%', 'stop-color':'#FFD700'},
				{offset:'50%','stop-color':'#FFFFFF'},
				{offset:'95%','stop-color':'#0000FF'}
			]);
			createGradient($('svg')[0],'gradient_3',[
				{offset:'5%', 'stop-color':'#FFD700'},
				{offset:'50%','stop-color':'#FFA500'},
				{offset:'95%','stop-color':'#FF0000'}
			]);


			// var legend_html = '<div class=\"kaavio-highlighter\" id=\"lengend_div\" style=\"top:40px; right:25px; width:250px; height:" . $legend_number * 35 . "px; background-color:rgba(0,0,0,0.1); padding:10px; border-radius:10px; border: 1px solid #CCCCCC;\">" . $LEGEND_INFO. "</div>';
			// $('wikipathways-pvjs').append(legend_html);

			// var toggle_legend_html = '<div class=\"kaavio-highlighter\" style=\"top:6px; right:265px; width:50px; height:50px;\"><button class=\"btn btn-sm btn-default\" style=\"height:24px;padding-top:3px;\" onclick=\"$(\'#lengend_div\').slideToggle(300);\">Toggle Legend</button></div>';
			// $('wikipathways-pvjs').append(toggle_legend_html);


			";
		// Draw Area Color for Each Box Area
		foreach ($ALL_COLORING_GENE as $key => $value) {
			$CHART_OUTPUT .= "
			createGradient($('svg')[0],'solids_" . str_replace(' ', '_', $key) . "',[
				{offset:'0%', 'stop-color':'#" . $value['Color'][0] . "'},";

			for ($i = 1; $i < count($value['Color']); $i++) {
				$border_temp = $i * 100.0 / count($value['Color']);
				$CHART_OUTPUT .= "
				{offset:'" . intval($border_temp) . "%','stop-color':'#" . $value['Color'][$i - 1] . "'},
				{offset:'" . intval($border_temp) . "%','stop-color':'#" . $value['Color'][$i] . "'},";
			}
			$CHART_OUTPUT .= "]);";
		}


	// Include SVG Legend if Checked
	if (isset($_POST['show_svg_legend'])) {
		$CHART_OUTPUT .= $LEGEND_JS_SVG_CODE;
	}

	$CHART_OUTPUT .= "
		}
	}
	function createGradient(svg,id,stops){
		var svgNS = svg.namespaceURI;
		var grad  = document.createElementNS(svgNS,'linearGradient');
		grad.setAttribute('id',id);
		for (var i=0;i<stops.length;i++){
			var attrs = stops[i];
			var stop = document.createElementNS(svgNS,'stop');
			for (var attr in attrs){
				if (attrs.hasOwnProperty(attr)) stop.setAttribute(attr,attrs[attr]);
			}
			grad.appendChild(stop);
		}

		var defs = svg.querySelector('defs') || svg.insertBefore( document.createElementNS(svgNS,'defs'), svg.firstChild );
		return defs.appendChild(grad);
	}";


	// // Load comparison info when clicking areas
	// $CHART_OUTPUT .= "
	// $(document).ready(function() {
	// });
	// </script>";
  $CHART_OUTPUT .= "</script>";
	// echo $CHART_OUTPUT;
  $GENERAL_OUTPUT .= $CHART_OUTPUT;

	file_put_contents($file_dir . '/svg_code.txt', str_replace("\n", "", $CHART_OUTPUT));


	// Info Table
	$GENERAL_OUTPUT .= '<br /><br /><h4><strong>Data Information Table</strong></h4><hr />';
	$comparison_number = count($_POST['time_list']);
	$GENERAL_OUTPUT .= '<table class="table table-bordered table-striped" id="table_chart_info"><thead><tr><th>Gene Name</th><th>Database Type</th><th>Database ID</th>';

  // for ($i=0; $i<$comparison_number; $i++) {
  //   $GENERAL_OUTPUT .= '<th>' . $_POST['column_list'][$i] . '</th>';
  // }

	$GENERAL_OUTPUT .= '</tr></thead><tbody>';
	foreach ($ALL_COLORING_GENE as $key => $value) {

		$comparison_info = $ALL_GENE_COMPARISON_INFO[$key];

		$GENERAL_OUTPUT .= '
		<tr>
			<td>' . $key . '</td>
			<td>' . $value['Database'] . '</td>
			<td>' . $value['Database_ID'] . '</td>';

      // for ($i=0; $i<$comparison_number; $i++) {
      //   // $GENERAL_OUTPUT .= '<td>' . $comparison_info[$_POST['column_list'][$i]] . '</td>';
      //   print_r($comparison_info);
      // }

		$GENERAL_OUTPUT .= '
		</tr>';
	}
	$GENERAL_OUTPUT .= '</tbody></table>';

	$GENERAL_OUTPUT .= "
	<script>
	$(document).ready(function() {
		$('#table_chart_info').DataTable({
			\"dom\": 'Bfrtip',
	    \"buttons\": [
	      'copy', 'csv', 'excel', 'pdf', 'print'
	    ],
		});
		$('#table_chart_info th').append('<span style=\"float:right;margin-top:3px;\"><i class=\"fa fa-sort gray\"></i></span>').css('padding-right', '10px');
	});
	</script>";
  // echo $GENERAL_OUTPUT;




  $OUTPUT = array(
    'type' => 'Success',
    'info' => $GENERAL_OUTPUT,
    'comparison_info' => $ALL_GENE_COMPARISON_INFO
  );
  header('Content-Type: application/json');
  echo json_encode($OUTPUT);
  exit();



}

?>
