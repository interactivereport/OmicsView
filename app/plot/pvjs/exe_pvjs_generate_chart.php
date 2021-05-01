<?php



	$sessionKey = md5(microtime(true) . '_' . rand(0, 1000));
	
	$_SESSION['App']['pvjs']['exe_pvjs_generate_chart.php'][$sessionKey]['POST'] = $_POST;


	// Check Reactome or Not
	$pathway_label = trim($BXAF_CONFIG['PATHWAY_LIST'][$_POST['pathway']]);
	$_SESSION['App']['pvjs']['exe_pvjs_generate_chart.php'][$sessionKey]['Title'] = $pathway_label;
	if (substr($pathway_label, strlen($pathway_label) - 10) == '(Reactome)') {
		$folder_name = $CONFIG_PROFILE['PVJS']['pathway_dir'] . '_reactome';
	} else {
		$folder_name = $CONFIG_PROFILE['PVJS']['pathway_dir'];
	}

	// Get Gene in Pathway
	$file_dir = dirname(__FILE__) . '/files/pathway/' . $folder_name . '/' . $_POST['pathway'];
	$genes_in_pathway = extract_gene_id($file_dir);


	// Check Comparison Exist
	$comparison_index_list = array();
	// Save all cleaned data info for each inhouse comparison
	$INHOUSE_CLEANED_DATA = array();

	foreach ($_POST['comparison_id'] as $comparison) {

		unset($found);
		
		// If inhouse comparison, use a different table
		if (substr($comparison, 0, 9) == '(inhouse)') {
			$INHOUSE_NAME = substr($comparison, 9);
			$sql = "SELECT `ComparisonIndex`
						FROM `" . $BXAF_CONFIG['TBL_INHOUSE_COMPARISON'] . "`
						WHERE `Name`='" . $INHOUSE_NAME . "'
				  AND `Owner`=" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
			$comparison_id = '(inhouse)';
			$comparison_id .= $DB -> get_one($sql);
			
			// Get inhouse cleaned data file
			$dir = $BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/inhouse_comp_' . substr($comparison_id, 9);
			$inhouse_file = $dir . '/cleaned_data.csv';
			$inhouse_data = array();
			if (file_exists($inhouse_file)) {
				$file = fopen($inhouse_file,"r");
				$index = 0;
				while(! feof($file)) {
					$row = fgetcsv($file);
					if ($index > 0) {
						$inhouse_data[] = $row;
					}
					$index++;
				}
				fclose($file);
			}
			$INHOUSE_CLEANED_DATA[$comparison_id] = $inhouse_data;
	
		} else {
			
			$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}` WHERE `ComparisonID`='" . addslashes(trim($comparison)) . "'";
			$results = $DB -> get_row($sql);
			$comparison_id = $results['ComparisonIndex'];
			  
			if ($results['ComparisonID'] == ''){
				$sql = "SELECT * FROM `App_User_Data_Comparisons` WHERE `ComparisonID`='" . addslashes(trim($comparison)) . "'";
				
				$results = $DB -> get_row($sql); 
				$comparison_id = $results['ComparisonIndex'];
			}
			
		
		}
	
		$comparison_id = trim($comparison_id);
	
		if ($comparison_id == '') {
			unset($OUTPUT);
			$OUTPUT['info'] = 'Error: No comparison found: <strong class="red">' . $comparison . '</strong>. Please revise.';
			header('Content-Type: application/json');
			echo json_encode($OUTPUT);
			exit();
		}
		$comparison_index_list[] = $comparison_id;
	} //foreach
	
	
	

	// Generate Legends
	if (true){
	$LEGEND_INFO = '<div style="width:100%;"><h3>Legend</h3>';
		if (intval($_POST['svg_legend_font_size']) < 5 || intval($_POST['svg_legend_font_size']) > 20) {
			$legend_font_size = 10;
		} else {
			$legend_font_size = intval($_POST['svg_legend_font_size']);
		}
		
		$LEGEND_JS_SVG_CODE =  "var svgNS = 'http://www.w3.org/2000/svg';"; // Javascript Code to Update SVG
		$LEGEND_JS_SVG_CODE .= "var newText = document.createElementNS(svgNS,'text');";
		$LEGEND_JS_SVG_CODE .= "newText.setAttributeNS(null,'x',0);";
		$LEGEND_JS_SVG_CODE .= "newText.setAttributeNS(null,'y',42); ";
		$LEGEND_JS_SVG_CODE .= "newText.setAttributeNS(null,'font-size','" . $legend_font_size . "px');";
		$LEGEND_JS_SVG_CODE .= "var textNode = document.createTextNode('Legend: ');";
		$LEGEND_JS_SVG_CODE .= "newText.appendChild(textNode);";
		$LEGEND_JS_SVG_CODE .= "document.getElementById('info-box-0').appendChild(newText);";
		$y_svg = 42;
	
	
		$legend_number = 2;
		for ($i = 0; $i < count($_POST['comparison_id']); $i++) {
			$LEGEND_INFO .= '<div style="margin-top:5px;"><strong>' . $_POST['comparison_id'][$i] . '</strong></div>';
	
	
			$y_svg += 14;
			$LEGEND_JS_SVG_CODE .= "var newText = document.createElementNS(svgNS,'text');";
			$LEGEND_JS_SVG_CODE .= "newText.setAttributeNS(null,'x',0);";
			$LEGEND_JS_SVG_CODE .= "newText.setAttributeNS(null,'y'," . $y_svg . "); ";
			$LEGEND_JS_SVG_CODE .= "newText.setAttributeNS(null,'font-size','" . $legend_font_size . "px');";
			$LEGEND_JS_SVG_CODE .= "var textNode = document.createTextNode('" . $_POST['comparison_id'][$i] . "');";
			$LEGEND_JS_SVG_CODE .= "newText.appendChild(textNode);";
			$LEGEND_JS_SVG_CODE .= "document.getElementById('info-box-0').appendChild(newText);";
	
			// Settings 1
			if ($_POST['visualization_1'][$i] == 'custom') {
				$info_temp = array(
					'value'       	=> $_POST['custom_visualization_1_cutoff'][$i],
					'color_style'   => $_POST['custom_visualization_1_select_color_style'][$i]
				);
			} else {
				$info_temp = $_POST['visualization_1'][$i];
			}
			$LEGEND_INFO .= getColorLegend($_POST['data_column_1'][$i], $info_temp);
			$legend_number += 2;
	
			$y_svg += 14;
			$LEGEND_JS_SVG_CODE .= getColorLegendSVG($_POST['data_column_1'][$i], $info_temp, $y_svg, $legend_font_size);
	
	
	
	
			// Settings 2
			if (trim($_POST['data_column_2'][$i]) == '') {
				continue;
			}
			if ($_POST['visualization_2'][$i] == 'custom') {
				$info_temp = array(
					'value'        => $_POST['custom_visualization_2_cutoff'][$i],
					'color_style'   => $_POST['custom_visualization_2_select_color_style'][$i]
				);
				$LEGEND_INFO .= getColorLegend($_POST['data_column_2'][$i], $info_temp);
				$y_svg += 14;
				$LEGEND_JS_SVG_CODE .= getColorLegendSVG($_POST['data_column_2'][$i], $info_temp, $y_svg, $legend_font_size);
			} else {
				$info_temp = $_POST['visualization_2'][$i];
				$LEGEND_INFO .= getColorLegend($_POST['data_column_2'][$i], $info_temp);
				$y_svg += 14;
				$LEGEND_JS_SVG_CODE .= getColorLegendSVG($_POST['data_column_2'][$i], $info_temp, $y_svg, $legend_font_size);
			}
			$legend_number += 1;
	
	
		}
	$LEGEND_INFO .= '</div>';
	}



	//---------------------------------------------------------------------------------------
	// Loop All Pathway Genes
	$ALL_COLORING_GENE = array();
	$ALL_GENE_COMPARISON_INFO = array();



	foreach ($genes_in_pathway as $genename => $geneinfo) {

		$ALL_COLORING_GENE[$genename] = array(
			'Database'    => $geneinfo['Database'],
			'Database_ID' => $geneinfo['ID'],
			'Gene_Index'  => 0,
			'Color'       => array()
		);
		$ALL_GENE_COMPARISON_INFO[$genename] = array();

		// Find gene index
		// 1: If database is 'Entrez Gene'
		if ($geneinfo['Database'] == 'Entrez Gene') {
			$entrez_id = intval($geneinfo['ID']);
			$sql = "SELECT `GeneIndex`
				FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
				WHERE `EntrezID`=" . intval($geneinfo['ID']);
			$gene_index = $DB -> get_one($sql);
		} else if ($geneinfo['Database'] == 'Ensembl') {
			// 2. If database is 'Ensembl'
			$sql = "SELECT `GeneIndex`
			 	FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
			 	WHERE `Ensembl`='" . trim($geneinfo['ID']) . "'";
			$gene_index = $DB -> get_one($sql);
		} else if ($geneinfo['Database'] == 'Uniprot-TrEMBL') {
			// 3. If database is 'Uniprot-TrEMBL'
			$sql = "SELECT `GeneIndex`
			 	FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
			 	WHERE `Uniprot`='" . trim($geneinfo['ID']) . "'";
			$gene_index = $DB -> get_one($sql);
		}

		$ALL_COLORING_GENE[$genename]['Gene_Index'] = $gene_index;



		// Find current gene for each comparison
		foreach ($comparison_index_list as $key => $comparison_id) {


		if (substr($comparison_id, 0, 9) == '(inhouse)') {
			$INHOUSE = true;
        	$comparison_data = array();
			foreach ($INHOUSE_CLEANED_DATA[$comparison_id] as $row) {
			  if ($row[0] == $genename) {
				$comparison_data = array(
				  'Log2FoldChange' => $row[1],
				  'PValue'         => $row[2],
				  'AdjustedPValue' => $row[2],
				);
			  }
			}
        	// Save Comparison Info
  			$sql = "SELECT `Name`
      					FROM `{$BXAF_CONFIG['TBL_INHOUSE_COMPARISON']}`
      					WHERE `ComparisonIndex`=" . substr($comparison_id, 9);
  			$comparison_name = $DB -> get_one($sql);
      	} else {
        	$INHOUSE = false;
        	$geneIndexList = array($gene_index);
        	$comparisonIndexList = array($comparison_id);
			
			if (internal_data_is_public($comparison_id)){
	        	$result = tabix_search_records_with_index($geneIndexList, $comparisonIndexList, 'ComparisonData');
			} else {
				
				
				$currentComparison = get_multiple_record('Comparisons', $comparison_id, 'GetRow');
				
				$result = tabix_search_records_with_index_internal_data($currentComparison['ProjectIndex'], $geneIndexList, $comparisonIndexList, 'ComparisonData');
			}

			// print_r($result);
			// print_r($comparisonIndexList);
        	// $comparison_data = $result[0];

			// Use smallest p-val
			$index_used = 0;
			$curr_pval = $result[0]['PValue'];
			foreach ($result as $k => $data_row) {
				if ($data_row['PValue'] < $curr_pval) {
					$curr_pval = $data_row['PValue'];
					$index_used = $k;
				}
			}
		
			$comparison_data = $result[$index_used];
		
			if (!$isPrivateData){
				// Save Comparison Info
				$sql = "SELECT `ComparisonID`
									FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
									WHERE `ComparisonIndex`=" . $comparison_id;
			} else {
				$sql = "SELECT `ComparisonID`
									FROM `App_User_Data_Comparisons`
									WHERE `ComparisonIndex`=" . $comparison_id;
				
			}
			$comparison_name = $DB -> get_one($sql);
		}




		$ALL_GENE_COMPARISON_INFO[$genename][] = array(
			'name'           => $comparison_name,
			'logFC'          => $comparison_data['Log2FoldChange'],
			'p-value'        => $comparison_data['PValue'],
			'FDR'            => $comparison_data['AdjustedPValue'],
		);




			// Set Color
			if (!is_array($comparison_data) || count($comparison_data) <= 1) {
				$ALL_COLORING_GENE[$genename]['Color'][] = 'CCCCCC';
			} else {
				$color_temp = 'CCCCCC';

				
				// Get Color
				// 1: The first column
				// 1.1: LogFC
				if ($_POST['data_column_1'][$key] == 'logFC') {
					if ($_POST['visualization_1'][$key] != 'custom') {
						$color_temp = mapToGradient($comparison_data['Log2FoldChange'], intval(intval($_POST['visualization_1'][$key]) + 1));
					} else {
						// Check value format
						$cutoff_values = explode(",", $_POST['custom_visualization_1_cutoff'][$key]);
						if (count($cutoff_values) != 3) {
							unset($OUTPUT);
							$OUTPUT['info'] = 'Error: Cutoff value format error: <strong class="red">' . $_POST['custom_visualization_1_cutoff'][$key] . '</strong>.';
 							header('Content-Type: application/json');
							echo json_encode($OUTPUT);
							exit();
						}
						$info_temp = array(
							'cutoff'        => $_POST['custom_visualization_1_cutoff'][$key],
							'color_style'   => $_POST['custom_visualization_1_select_color_style'][$key]
						);
						$color_temp = mapToGradient($comparison_data['Log2FoldChange'], $info_temp);
					}
				}
				// 1.2: P-Value or FDR
				else {
					if ($_POST['data_column_1'][$key] == 'pvalue') {
						$field_value = $comparison_data['PValue'];
					} else {
						$field_value = $comparison_data['AdjustedPValue'];
					}
					if ($_POST['visualization_1'][$key] != 'custom') {
						$color_temp = mapToGradientPValue($field_value, $_POST['visualization_1'][$key]);
					} else {
						$info_temp = array(
							'value'        => $_POST['custom_visualization_1_cutoff'][$key],
							'color_style'   => $_POST['custom_visualization_1_select_color_style'][$key]
						);
						$color_temp = mapToGradientPValue($field_value, $info_temp);
					}
				}
				$ALL_COLORING_GENE[$genename]['Color'][] = $color_temp;


				// 2: The second column
				// 2.1: LogFC
				if ($_POST['data_column_2'][$key] == '') {
					continue;
				} elseif ($_POST['data_column_2'][$key] == 'logFC') {
					if ($_POST['visualization_2'][$key] != 'custom') {
						$color_temp = mapToGradient($comparison_data['Log2FoldChange'], intval(intval($_POST['visualization_2'][$key]) + 1));
					} else {
						// Check value format
						$cutoff_values = explode(",", $_POST['custom_visualization_2_cutoff'][$key]);
						if (count($cutoff_values) != 3) {
							unset($OUTPUT);
							$OUTPUT['info'] = 'Error: Cutoff value format error: <strong class="red">' . $_POST['custom_visualization_2_cutoff'][$key] . '</strong>.';
 							header('Content-Type: application/json');
							echo json_encode($OUTPUT);
							exit();
						}
						$info_temp = array(
							'cutoff'        => $_POST['custom_visualization_2_cutoff'][$key],
							'color_style'   => $_POST['custom_visualization_2_select_color_style'][$key]
						);
						$color_temp = mapToGradient($comparison_data['Log2FoldChange'], $info_temp);
					}
					$ALL_COLORING_GENE[$genename]['Color'][] = $color_temp;
				} else {
					
					// 2.2: P-Value or FDR
					if ($_POST['data_column_2'][$key] == 'pvalue'){
						$field_value = $comparison_data['PValue'];
					} else {
						$field_value = $comparison_data['AdjustedPValue'];
					}

					if ($_POST['visualization_2'][$key] != 'custom') {
						$color_temp = mapToGradientPValue($field_value, $_POST['visualization_2'][$key]);
					} else {
						$info_temp = array(
							'value'        => $_POST['custom_visualization_2_cutoff'][$key],
							'color_style'   => $_POST['custom_visualization_2_select_color_style'][$key]
						);
						$color_temp = mapToGradientPValue($field_value, $info_temp);
					}
					$ALL_COLORING_GENE[$genename]['Color'][] = $color_temp;
				}

			}
		}
	}


	$file_dir = $BXAF_CONFIG['USER_FILES_PVJS'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
	if (!is_dir($file_dir)) {
		mkdir($file_dir . '/', 0775, true);
	}
	file_put_contents($file_dir . '/comparison_info.txt', serialize($ALL_GENE_COMPARISON_INFO));



	$GENERAL_OUTPUT = '';
	// Output
	/*
	$GENERAL_OUTPUT = '
	<div class="btn-group hidden" role="group"  style="margin-bottom:1em;">
		<button id="btn_save_svg" class="btn btn-primary"><i class="fa fa-download"></i> Download SVG</button>
		<button id="btn_view_svg_new_window" class="btn btn-primary"><i class="fa fa-eye"></i> View SVG in new window</button>	
	</div>';
	*/

	$CHART_OUTPUT = '';



 /*<script type="text/javascript" src="js/jquery.layout.min-1.3.0.js"></script> */ 
	$CHART_OUTPUT .= '
	<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
	
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
		height="780"
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


			var legend_html = '<div class=\"kaavio-highlighter\" id=\"lengend_div\" style=\"top:40px; right:25px; width:250px; height:" . $legend_number * 35 . "px; background-color:rgba(0,0,0,0.1); padding:10px; border-radius:10px; border: 1px solid #CCCCCC;\">" . $LEGEND_INFO. "</div>';
			
			
			$('wikipathways-pvjs').append(legend_html);

			var toggle_legend_html = '<div class=\"kaavio-highlighter\" style=\"top:6px; right:320px; width:50px; height:50px;\"><button class=\"btn btn-sm btn-primary\" style=\"height:24px;padding-top:3px;\" onclick=\"$(\'#lengend_div\').slideToggle(300);\">Show/Hide Legend</button></div>';
			$('wikipathways-pvjs').append(toggle_legend_html);


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



	$CHART_OUTPUT .= "</script>";

	$GENERAL_OUTPUT .= $CHART_OUTPUT;

	file_put_contents($file_dir . '/svg_code.txt', str_replace("\n", "", $CHART_OUTPUT));


	$GENERAL_OUTPUT_WO_TABLE = $GENERAL_OUTPUT;
	unset($GENERAL_OUTPUT);

	// Info Table
	$GENERAL_OUTPUT .= '<br /><br /><h4><strong>Data Information Table</strong></h4><hr />';
	$comparison_number = count($comparison_index_list);
	$GENERAL_OUTPUT .= '<table class="table table-bordered table-striped" id="table_chart_info"><thead><tr><th>Gene Name</th><th>Database Type</th><th>Database ID</th>';
	
	for ($i = 0; $i < $comparison_number; $i++) {
		if (substr($comparison_index_list[$i], 0, 9) == '(inhouse)') {
				$GENERAL_OUTPUT .= '<th>' . $_POST['comparison_id'][$i] . ' Log2FC</th>';
				$GENERAL_OUTPUT .= '<th>' . $_POST['comparison_id'][$i] . ' Stats</th>';
		} else {
				$GENERAL_OUTPUT .= '<th>' . $_POST['comparison_id'][$i] . ' Log2FC</th>';
				$GENERAL_OUTPUT .= '<th>' . $_POST['comparison_id'][$i] . ' p-value</th>';
				$GENERAL_OUTPUT .= '<th>' . $_POST['comparison_id'][$i] . ' FDR</th>';
		}
	}
	$GENERAL_OUTPUT .= '</tr></thead><tbody>';

	foreach ($ALL_COLORING_GENE as $key => $value) {

		$comparison_info = $ALL_GENE_COMPARISON_INFO[$key];

		$GENERAL_OUTPUT .= '
		<tr>
			<td>' . $key . '</td>
			<td>' . $value['Database'] . '</td>
			<td>' . $value['Database_ID'] . '</td>';
			
			foreach ($comparison_info as $k => $info) {

				if (substr($comparison_index_list[$k], 0, 9) == '(inhouse)') {
					$GENERAL_OUTPUT .= '<td style="color:' . get_stat_scale_color($info['logFC'], 'logFC') . ';">';
					$GENERAL_OUTPUT .= (trim($info['logFC']) == '.') ? 'NA' : $info['logFC'];
					$GENERAL_OUTPUT .= '</td><td  style="color:' . get_stat_scale_color($info['FDR'], 'FDR') . ';">';
					$GENERAL_OUTPUT .= (trim($info['FDR']) == '.') ? 'NA' : $info['FDR'];
					$GENERAL_OUTPUT .= '</td>';
				} else {
					$GENERAL_OUTPUT .= '<td style="color:' . get_stat_scale_color($info['logFC'], 'logFC') . ';">';
					$GENERAL_OUTPUT .= (trim($info['logFC']) == '.') ? 'NA' : $info['logFC'];
					$GENERAL_OUTPUT .= '</td><td style="color:' . get_stat_scale_color($info['p-value'], 'PVal') . ';">';
					$GENERAL_OUTPUT .= (trim($info['p-value']) == '.') ? 'NA' : $info['p-value'];
					$GENERAL_OUTPUT .= '</td><td style="color:' . get_stat_scale_color($info['FDR'], 'FDR') . ';">';
					$GENERAL_OUTPUT .= (trim($info['FDR']) == '.') ? 'NA' : $info['FDR'];
					$GENERAL_OUTPUT .= '</td>';
				}

			}
		$GENERAL_OUTPUT .= '</tr>';
	}
	$GENERAL_OUTPUT .= '</tbody></table>';


	$GENERAL_OUTPUT .= "
	<script>
	$(document).ready(function() {
		$('#table_chart_info').DataTable({
			\"dom\": 'lBfrtip',
	    \"buttons\": [
	      'copy', 'csv', 'excel', 'pdf', 'print'
	    ],
      \"lengthMenu\": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, \"All\"]]
		});

	});
	</script>";
  

	$OUTPUT = array(
		'info' => $GENERAL_OUTPUT,
		'comparison_info' => $ALL_GENE_COMPARISON_INFO
	);
  
 	$_SESSION['App']['pvjs']['exe_pvjs_generate_chart.php'][$sessionKey]['info'] = $GENERAL_OUTPUT_WO_TABLE;
  
  
  	$URL = "exe_pvjs_generate_chart_iframe.php?sessionKey={$sessionKey}&iframe=1";


  	$OUTPUT['info'] = "
	
	<hr/>
	
	

	<div xstyle='width:100%;height:100%;margin:0 auto;background:#FFF;'>
    <div style='position:relative;'>
		<div style='width:100%; height:100%; min-height:850px; min-width:600px;'>&nbsp;</div>
			<iframe src='{$URL}' frameborder='0' allowfullscreen style='position:absolute;top:0;left:0;width:100%; height:100%;'></iframe>
		</div>
	</div>
	<hr/>
	" . $GENERAL_OUTPUT;
 
  
 
	header('Content-Type: application/json');
	echo json_encode($OUTPUT);





	exit();
	
?>