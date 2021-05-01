<?php
	$sessionKey = $_POST['sessionKey'];
	
	if ($sessionKey != ''){
		$_POST['data_source'] = $_SESSION['App']['bubble'][$sessionKey]['POST']['data_source'];
		$_POST['data_source_private_project_indexes'] = $_SESSION['App']['bubble'][$sessionKey]['POST']['data_source_private_project_indexes'];
	}
	
	//$_POST['y_setting'] = 'top_5';
	//$_POST['coloring_setting'] = 'top_5';
	
	
	internal_data_sanitize_user_input($_POST['data_source'], $_POST['data_source_private_project_indexes']);
	


  	$TIME = time();
	$GENE_NAME = addslashes(trim($_POST['gene_name']));
	$Y_FIELD = $_POST['select_y_field'];
	$COLORING_FIELD = $_POST['select_coloring_field'];
	$AREA_FIELD = $_POST['area_setting'];
	$AREA_FIELD_MODIFIED = ($AREA_FIELD == 'PValue') ? 'PVALUE' : 'ADJPVALUE';

	// Get GeneIndex
	$GENE_INDEX = search_gene_index($GENE_NAME);

	if (!isset($GENE_INDEX) || trim($GENE_INDEX) == '' || intval($GENE_INDEX) < 0) {
		echo 'Error: No gene found.';
		exit();
	}

	// Tabix
	$geneIndex = array($GENE_INDEX);

	unset($data_comparison);
	if ($_POST['data_source']['public'] != ''){
		$data_comparison = tabix_search_records_with_index($geneIndex, '', 'ComparisonData');
	}
	
	if ($_POST['data_source']['private'] != ''){
		foreach($_POST['data_source_private_project_indexes'] as $tempKey => $projectIndex){
			$data_comparison_private = tabix_search_records_with_index_internal_data($projectIndex, $geneIndex, '', 'ComparisonData');
			
			foreach($data_comparison_private as $tempKeyX => $tempValueX){
				$data_comparison[] = $tempValueX;
			}
		}
	}


	$allComparisonIndexes 	= array_unique(array_column($data_comparison, 'ComparisonIndex'));
	
	if ($_POST['select_shape_field'] != ''){
		$COLORING_FIELD_SQL = "`{$_POST['select_shape_field']}`,";
		$hasShapeBy = true;	
	} else {
		$COLORING_FIELD_SQL = '';
		$hasShapeBy = false;	
	}
	

	if ($_POST['select_subplot_field'] != ''){
		$SUBPLOT_FIELD_SQL = "`{$_POST['select_subplot_field']}`,";
		$hasSubplot = true;
	} else {
		$SUBPLOT_FIELD_SQL = '';
		$hasSubplot = false;
	}
	
	if ($_POST['select_subplot_field_enable']){
		$hasSubplot = true;
	} else {
		$hasSubplot = false;
	}
	
	
	$ALL_COMPARISONS 		= search_comparisons_by_index($allComparisonIndexes, "`ComparisonIndex`, `{$Y_FIELD}`, `{$COLORING_FIELD}`, {$COLORING_FIELD_SQL} {$SUBPLOT_FIELD_SQL} `ComparisonID`, `ComparisonIndex`, `ComparisonCategory`, `ComparisonContrast`, `ProjectName`", $_POST['data_source'], $_POST['data_source_private_project_indexes']);	
	
	
	//$ALL_COMPARISONS = array_slice($ALL_COMPARISONS, 0, 20, true);
	//echo general_printr($ALL_COMPARISONS);
	//exit();
	

	//Do some filters here.
	//Significant Changed Data Point
	//Filter Method
	//0: No filter
	//1: Based on abs(Log2FC)
	//
	//take the highest 20/50/100/500/x abs(Log2FC)
	//1: Show all options
	



	$Y_FIELD_LIST = array();
	$COLORING_FIELD_LIST = array();
	$Y_FIELD_NUMBER = array(); // Appear times
	$COLORING_FIELD_NUMBER = array();

	foreach ($data_comparison as $comparison) {
		
		$comparison_row = $ALL_COMPARISONS[$comparison['ComparisonIndex']];

		if ($AREA_FIELD == 'PValue'){
			if (($comparison['PValue'] === '') || (is_null($comparison['PValue']) || ($comparison['PValue'] === '.') || ($comparison['PValue'] == 'NA'))){
				continue;	
			}
		} elseif ($AREA_FIELD == 'AdjustedPValue'){
			if (($comparison['AdjustedPValue'] === '') || (is_null($comparison['AdjustedPValue']) || ($comparison['AdjustedPValue'] === '.') || ($comparison['AdjustedPValue'] == 'NA'))){
				continue;	
			}
		}
		
		if ($_POST['y_setting'] != 'all'
			&& (trim($comparison_row[$Y_FIELD]) == '' || trim($comparison_row[$Y_FIELD]) == 'NA')) {
			continue;
		}
		if ($_POST['coloring_setting'] != 'all'
			&& (trim($comparison_row[$COLORING_FIELD]) == '' || trim($comparison_row[$COLORING_FIELD]) == 'NA')) {
			continue;
		}


		if (!in_array($comparison_row[$Y_FIELD], array_keys($Y_FIELD_NUMBER))) {
			$Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] = 1;
		} else {
			$Y_FIELD_NUMBER[$comparison_row[$Y_FIELD]] += 1;
		}
		if (!in_array($comparison_row[$COLORING_FIELD], array_keys($COLORING_FIELD_NUMBER))) {
			$COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] = 1;
		} else {
			$COLORING_FIELD_NUMBER[$comparison_row[$COLORING_FIELD]] += 1;
		}

	}

	arsort($Y_FIELD_NUMBER);
	arsort($COLORING_FIELD_NUMBER);
	
	if ($_POST['select_subplot_field_enable']){
		if ($_POST['coloring_setting'] == 'all'){
			$_POST['coloring_setting'] = 'top_20';	
		}
		
		if ($_POST['y_setting'] == 'all'){
			$_POST['y_setting'] = 'top_20';	
		}
	}


	// Filter y field and coloring field
	if ($_POST['y_setting'] == 'top_5') {
		$index = 0;
		foreach($Y_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || $index >= 5) {
				unset($Y_FIELD_NUMBER[$key]);
			}
			if (trim($key) != 'normal control') $index++;
		}
	} elseif ($_POST['y_setting'] == 'top_10') {
		$index = 0;
		foreach($Y_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || $index >= 10) {
				unset($Y_FIELD_NUMBER[$key]);
			}
			if (trim($key) != 'normal control') $index++;
		}
	} else if ($_POST['y_setting'] == 'top_20') {
		$index = 0;
		foreach($Y_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || $index >= 20) {
				unset($Y_FIELD_NUMBER[$key]);
			}
			if (trim($key) != 'normal control') $index++;
		}
	} else if ($_POST['y_setting'] == 'all') {
		// foreach($Y_FIELD_NUMBER as $key => $value) {
		// 	if (trim($key) == 'normal control') {
		// 		unset($Y_FIELD_NUMBER[$key]);
		// 	}
		// }
	} else if ($_POST['y_setting'] == 'customize') {
		foreach($Y_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || !isset($_POST['y_' . str_replace(' ', '_', $key)])) {
				unset($Y_FIELD_NUMBER[$key]);
			}
		}
	}


	if ($_POST['coloring_setting'] == 'top_5') {
		$index = 0;
		foreach($COLORING_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || $index >= 5) {
				unset($COLORING_FIELD_NUMBER[$key]);
			}
			if (trim($key) != 'normal control') $index++;
		}
	} elseif ($_POST['coloring_setting'] == 'top_10') {
		$index = 0;
		foreach($COLORING_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || $index >= 10) {
				unset($COLORING_FIELD_NUMBER[$key]);
			}
			if (trim($key) != 'normal control') $index++;
		}
	} else if ($_POST['coloring_setting'] == 'top_20') {
		$index = 0;
		foreach($COLORING_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || $index >= 20) {
				unset($COLORING_FIELD_NUMBER[$key]);
			}
			if (trim($key) != 'normal control') $index++;
		}
	} else if ($_POST['coloring_setting'] == 'all') {
		foreach($COLORING_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control') {
				unset($COLORING_FIELD_NUMBER[$key]);
			}
		}
	} else if ($_POST['coloring_setting'] == 'customize') {
		foreach($COLORING_FIELD_NUMBER as $key => $value) {
			if (trim($key) == 'normal control' || !isset($_POST['color_' . str_replace(' ', '_', $key)])) {
				unset($COLORING_FIELD_NUMBER[$key]);
			}
		}
	}



	// Get All Values
	// Grouped by coloring settings
	$ALL_MARKER = array();
	$ALL_GENES = array();
	$ALL_APPEARED_Y = array();
	$NUMBER_MISSING_DATA = 0;
  
	foreach ($data_comparison as $comparison) {
		
		$comparison_row = $ALL_COMPARISONS[$comparison['ComparisonIndex']];
		
		$comparison_row['ComparisonIndex'] = $comparison['ComparisonIndex'];
		
		
		if ($AREA_FIELD == 'PValue'){
			if (($comparison['PValue'] === '') || (is_null($comparison['PValue']) || ($comparison['PValue'] === '.') || ($comparison['PValue'] == 'NA'))){
				$NUMBER_MISSING_DATA++;
				continue;	
			}
		} elseif ($AREA_FIELD == 'AdjustedPValue'){
			if (($comparison['AdjustedPValue'] === '') || (is_null($comparison['AdjustedPValue']) || ($comparison['AdjustedPValue'] === '.') || ($comparison['AdjustedPValue'] == 'NA'))){
				$NUMBER_MISSING_DATA++;
				continue;	
			}
		}
		
		
		
		if ($_POST['y_setting'] != 'all'
			&& (trim($comparison_row[$Y_FIELD]) == '' || trim($comparison_row[$Y_FIELD]) == 'NA')) {
			$NUMBER_MISSING_DATA++;
			continue;
		}
		if ($_POST['coloring_setting'] != 'all'
			&& (trim($comparison_row[$COLORING_FIELD]) == '' || trim($comparison_row[$COLORING_FIELD]) == 'NA')) {
			$NUMBER_MISSING_DATA++;
			continue;
		}

		// Skip unselected y&coloring option
		$y_temp = $comparison_row[$Y_FIELD];
		$color_temp = $comparison_row[$COLORING_FIELD];

		if (!in_array($y_temp, array_keys($Y_FIELD_NUMBER))
			|| !in_array($color_temp, array_keys($COLORING_FIELD_NUMBER))) {

			if ($_POST['y_setting'] == 'all' && $_POST['coloring_setting'] == 'all') {
				$NUMBER_MISSING_DATA++;
			}

			continue;
		}



		// Save appeared y option and point info
		if (!in_array($y_temp, $ALL_APPEARED_Y)) {
			$ALL_APPEARED_Y[] = $y_temp;
		}
		
		
		
		
		
		$currentProject = search_one_record_by_name('project', $comparison_row['ProjectName'], 'GetRow');

		if (!in_array($color_temp, array_keys($ALL_MARKER))) {
			$ALL_MARKER[$color_temp] = array(
				array(
					'Y_FIELD' 			=> $y_temp,
					'COLORING_FIELD' 	=> $color_temp,
					'LOGFC' 			=> $comparison['Log2FoldChange'],
					'PVALUE' 			=> $comparison['PValue'],
					'ADJPVALUE' 		=> $comparison['AdjustedPValue'],
					'COMPARISON_ID' 	=> $comparison_row['ComparisonID'],
					'COMPARISON_INDEX' 	=> $comparison_row['ComparisonIndex'],
					'COMPARISON_CATEGORY' 		=> $comparison_row['ComparisonCategory'],
					'COMPARISON_CONTRAST' 		=> $comparison_row['ComparisonContrast'],
					'Project_Disease'			=> $currentProject['Disease'],
					'Project_StudyType'			=> $currentProject['StudyType'],
					'Project_TherapeuticArea'	=> $currentProject['TherapeuticArea'],
					'Project_Title'				=> $currentProject['Title'],
					'Project_PubMed'			=> $currentProject['PubMed'],
				)
			);
		} else {
			$ALL_MARKER[$color_temp][] = array(
				'Y_FIELD' =>$y_temp,
				'COLORING_FIELD' => $color_temp,
				'LOGFC' => $comparison['Log2FoldChange'],
				'PVALUE' => $comparison['PValue'],
				'ADJPVALUE' => $comparison['AdjustedPValue'],
				'COMPARISON_ID' => $comparison_row['ComparisonID'],
				'COMPARISON_INDEX' => $comparison_row['ComparisonIndex'],
       		 	'COMPARISON_CATEGORY' => $comparison_row['ComparisonCategory'],
       			'COMPARISON_CONTRAST' => $comparison_row['ComparisonContrast'],
				'COMPARISON_CATEGORY' 		=> $comparison_row['ComparisonCategory'],
				'COMPARISON_CONTRAST' 		=> $comparison_row['ComparisonContrast'],
				'Project_Disease'			=> $currentProject['Disease'],
				'Project_StudyType'			=> $currentProject['StudyType'],
				'Project_TherapeuticArea'	=> $currentProject['TherapeuticArea'],
				'Project_Title'				=> $currentProject['Title'],
				'Project_PubMed'			=> $currentProject['PubMed'],
			);
		}
		$allProjectIndexes[$comparison_row['ProjectIndex']] = $comparison_row['ProjectIndex'];


		// Save all genes to search
		$ALL_GENES[] = array(
			'x' => $comparison['Log2FoldChange'],
			'y' => $y_temp,
			'comparison_index' => $comparison['ComparisonIndex'],
			'gene_index' => $GENE_INDEX,
		);
	}


	asort($ALL_APPEARED_Y);
	$HEIGHT = max(800, count($ALL_APPEARED_Y) * 16 + 190);
	$ALL_APPEARED_Y_ORDERED = array();
	foreach ($ALL_APPEARED_Y as $option) {
		$ALL_APPEARED_Y_ORDERED[] = $option;
	}
	$dir = $BXAF_CONFIG['USER_FILES_BUBBLE'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
	if (!is_dir($dir)) {
		mkdir($dir, 0755, true);
	}
	file_put_contents($dir . '/y_field_options.txt', serialize($ALL_APPEARED_Y_ORDERED));
	file_put_contents($dir . '/all_genes.txt', serialize($ALL_GENES));


//Comparison
//ComparisonCategory
//ComparisonContrast
//Project
//Disease
//StudyType
//TherapeuticArea
//Title


  //-----------------------------------------------------------------------------
  // Save CSV File for Users to Download
  $csv_info = array();
  foreach ($ALL_MARKER as $markers) {
    foreach ($markers as $marker) {
      $csv_info[] = array(
        $GENE_NAME,
        $marker['COMPARISON_ID'],
        $marker['LOGFC'],
        $marker['PVALUE'],
        $marker['ADJPVALUE'],
		$marker['COMPARISON_CATEGORY'],
		$marker['COMPARISON_CONTRAST'],
		$marker['Project_Disease'],
		$marker['Project_StudyType'],
		$marker['Project_TherapeuticArea'],
		$marker['Project_Title'],
		$marker['Project_PubMed'],
      );
    }
  }

  	if ($sessionKey != ''){
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'] = array();
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'GeneName';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'ComparisonName';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Log2FC';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'PValue';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'FDR';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Category';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Contrast';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Disease';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Study Type';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Therapeutic Area';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'Project Title';
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Header'][] = 'PubMed';

		$_SESSION['App']['bubble'][$sessionKey]['Download']['Body'] = $csv_info;
		
		$hasResult = true;
	}

	if (general_array_size($ALL_GENES) <= 0){
		echo "<div class='alert alert-warning'>";
			echo "<p><i class='fa fa-exclamation-triangle text-danger' aria-hidden='true'></i> The search result does not contain any data. Please click <a href='index.php'>here</a> to refine your search conditions.</p>";
			
		echo "</div>";
		$hasResult = false;

		
	} else {
	
		
	
		// Output
		echo '
		<div class="row mt-1">
			<div class="col-md-2">
		  <button class="btn btn-sm btn-primary" id="btn_modify_settings"
			onclick="$(\'#first_form_div, #second_form_div\').slideToggle(300);">
			<i class="fa fa-cog"></i> Modify Settings
		  </button>
		  <a class="mt-1 btn btn-sm btn-info" href="download.php?sessionKey=' . $sessionKey . '" target="_blank">
			<i class="fa fa-download"></i> Download Data
		  </a>
				<a class="mt-1 btn btn-sm btn-warning btn_save_svg_' . $TIME . '" id="btn_save_svg" href="javascript:void(0);">
			<i class="fa fa-download"></i> Download SVG
		  </a>
	
		</div>
			<div class="col-md-4">
				<div class="alert alert-success" sstyle="width:35em">
					<p>The plot contains <strong>' . number_format(count($ALL_GENES)) . '</strong> out of <strong>' . number_format(intval(count($data_comparison) - $NUMBER_MISSING_DATA)) . '</strong> data points.</p>
				</div>
			</div>
		</div>
		<div class="w-100" style="min-height:' . $HEIGHT . 'px;" id="plot_div"></div>';
		
		
		
		
		
		
	
		echo "<script>";
	
	
		$index = 0;
		foreach ($ALL_MARKER as $key => $value) {
			$temp_logfc = array();
			$temp_y = array();
			$temp_area = array();
			$temp_text = array();
			$temp_comparison_index = array();
			$temp_comparison_id = array();
			foreach ($value as $k => $v) {
				
				if (is_numeric($v['LOGFC'])){
					$v['LOGFC'] = floatval($v['LOGFC']);	
				} else {
					$v['LOGFC'] = "''";	
				}
				
				$temp_logfc[] = $v['LOGFC'];
	
				// Set Y Axis Label
				$temp_y[] = '"' . $v['Y_FIELD'] . '"';
	
				
				if ($hasShapeBy){
					$currentComparisonIndex = $v['COMPARISON_INDEX'];
					
					$temp_text[] = '"' .
					"Comparison ID: " . $v['COMPARISON_ID']    . "<br />" . 
					"Category: " . $v['COMPARISON_CATEGORY']  . "<br />" . 
					"Contrast: " . $v['COMPARISON_CONTRAST']  . "<br />" . 
					substr($Y_FIELD, strpos($Y_FIELD, '_')+1) . ": " .  $v['Y_FIELD'] . "<br />"  . 
					substr($_POST['select_shape_field'], strpos($_POST['select_shape_field'], '_')+1) . ": " .  $ALL_COMPARISONS[$currentComparisonIndex][$_POST['select_shape_field']] . "<br />"  . 
					substr($COLORING_FIELD, strpos($COLORING_FIELD, '_')+1) . ": " . $v['COLORING_FIELD'] . "<br />" . 
					"P-value: " . $v['PVALUE'] . "<br />" . 
					"Adj P-value: " . $v['ADJPVALUE'] . "<br />" . 
					"log2FC: " . $v['LOGFC'] . "<br />" . 
					'"';
				} else {
					$temp_text[] = '"' .
					"Comparison ID: " . $v['COMPARISON_ID']    . "<br />" . 
					"Category: " . $v['COMPARISON_CATEGORY']  . "<br />" . 
					"Contrast: " . $v['COMPARISON_CONTRAST']  . "<br />" . 
					substr($Y_FIELD, strpos($Y_FIELD, '_')+1) . ": " .  $v['Y_FIELD'] . "<br />"  . 
					substr($COLORING_FIELD, strpos($COLORING_FIELD, '_')+1) . ": " . $v['COLORING_FIELD'] . "<br />" . 
					"P-value: " . $v['PVALUE'] . "<br />" . 
					"Adj P-value: " . $v['ADJPVALUE'] . "<br />" . 
					"log2FC: " . $v['LOGFC'] . "<br />" . 
					'"';
				}
				
				
	
				if ((-1000) * log10($v[$AREA_FIELD_MODIFIED]) < 5000 && (-1000) * log10($v[$AREA_FIELD_MODIFIED]) > 100) {
					$temp_area[] = floatval((-1000) * log10($v[$AREA_FIELD_MODIFIED]));
				} else if ((-1000) * log10($v[$AREA_FIELD_MODIFIED]) > 5000) {
					$temp_area[] = 5000;
				} else {
					$temp_area[] = 100;
				}
				
				
	
				$temp_comparison_index[] = $v['COMPARISON_INDEX'];
				$temp_comparison_id[] = $v['COMPARISON_ID'];
			}
	
			$key_modified = str_replace(';', '<br>', $key);
			$key_modified = $key_modified;
			
			/*
			foreach($temp_y as $tempKeyX => $tempValueX){
				//$temp_y[$tempKeyX] = 'x' . md5($tempValueX);	
			}
			
			
			
			foreach($temp_text as $tempKeyX => $tempValueX){
				//$temp_text[$tempKeyX] = 'x' . md5($tempValueX);	
			}
			*/
			
			if ($hasShapeBy){
				$temp_symbols = array();
				$shapeValues = array();
				foreach($temp_comparison_index as $tempKeyX => $currentComparisonIndex){
					$currentComparison 	= $ALL_COMPARISONS[$currentComparisonIndex];
					$shapeRawValue		= $currentComparison[$_POST['select_shape_field']];

					$shapeValues[$currentComparisonIndex] = $shapeRawValue;
					$temp_symbols[]	= plotly_get_marker_shapes_from_value($_POST['select_shape_field'], $shapeRawValue);
				}
			}
			

			//echo "<script>";
			if ($hasShapeBy){
				$symbol = "symbol: ['" . implode("', '", $temp_symbols) . "'],";
			} else {
				$symbol = '';		
			}
				
				
			$xaxis = '';
			$yaxis = '';
			
			
			if ($hasSubplot && $index > 0){
				$xaxis = "xaxis:'x{$index}',";
				$yaxis = "yaxis:'y{$index}',";
			}
			
				
			echo "
			var trace" . $index . " = {
				x: [" . implode(', ', $temp_logfc) . "],
				y: [" . implode(', ', $temp_y) . "],
				name: \"{$key_modified}\",
				hoverinfo: \"text\",
				text: [" . implode(', ', $temp_text) . "],
				mode: 'markers',
				" . $xaxis . "
				" . $yaxis . "
				marker: {
					size: [" . implode(', ', $temp_area) . "],
					sizeref: 7,
					sizemode: 'area',
					" . $symbol . "
					comparison_index: ['" . implode("', '", $temp_comparison_index) . "'],
					comparison_id: [\"" . implode('", "', $temp_comparison_id) . "\"],
				}
			};";				
				
						
			

			$index++;
		}
		
		if ($hasSubplot && $index > 0){
			$row	= ceil($index/3);
			$column = ceil($index/$row);
			$grid = "grid: {rows: {$row}, columns: {$column}, pattern: 'independent'},";
		}

	
		echo "
		var data = [trace0";
	
		for ($i = 1; $i < $index; $i++) {
			echo ", trace".$i;
		}
	
		echo "];
	
		var layout = {
			margin: {
				l: 300
			},
	
			title: \"Bubble Chart for " . $GENE_NAME . "<br>Colored by " . $COLORING_FIELD . "\",
			showlegend: true,
			height: " . $HEIGHT . ",
			" . $grid . "
			//width: 1200,
			xaxis: {
				title: 'Log 2 Fold Change',
			},
			yaxis: {
				// title: '" . addslashes($Y_FIELD) . "',
				categoryorder: 'category ascending',
		  range: [-0.5, " . count($ALL_APPEARED_Y) . ".5]
			},
			hovermode: 'closest',
		};
	
		main_plot = Plotly
		.plot('plot_div', data, layout, {displaylogo:false, modeBarButtonsToRemove:['sendDataToCloud'], scrollZoom:true, displayModeBar: true})
		.then(function(gd){
		  
		  Plotly.toImage(gd, {
								format: 'svg', 
								width: 1600, 
								height: " . $HEIGHT . "
								}).then(function(dataUrl) {
									$('#svgCode').val(dataUrl);
								});
								
		  Plotly.toImage(gd, {
								format: 'png', 
								width: 1600, 
								height: " . $HEIGHT . "
								}).then(function(dataUrl) {
									$('#pngCode').val(dataUrl);
								});
		  
	
		  $(document).on('click', '.btn_save_svg_" . $TIME . "', function() {
			
			Plotly
			  .downloadImage(gd, {
							filename: 'bubblePlot',
							format:'svg',
							height:" . $HEIGHT . ",
							width:1600
						})
			  .then(function(filename){
	
			  });
		  });
		  $('.loader').remove();

		});
	
		$(document).ready(function() {
			var graphDiv = document.getElementById('plot_div');
	
			graphDiv.on('plotly_click', function(data){
				var comparison = data.points[0].data.marker.comparison_id[data.points[0].pointNumber];
				var comparison_index = data.points[0].data.marker.comparison_index[data.points[0].pointNumber];
	
				bootbox.alert('<h4>Comparison ' + comparison + '</h4><br /><a href=\"../search_comparison/single_comparison.php?type=comparison&id='+comparison_index+'\">Comparison Detail</a><br /><a href=\"../volcano/index.php?id='+comparison_index+'\">Comparison Volcano Chart</a><br /><a href=\"../pvjs/index.php?id='+comparison_index+'\">Pathway View</a><br /><a href=\"../search_comparison/index.php?type=sample&comparison_id='+comparison_index+'\">Related Samples</a> &nbsp;');
			});
	
	
			graphDiv.on('plotly_selected', function(eventData) {
				var x = [];
				var y = [];
				eventData.points.forEach(function(pt) {
					x.push(pt.x);
					y.push(pt.y);
				});
				$.ajax({
					type: 'POST',
					url: 'exe.php?action=show_table&type=lasso_select',
					data: {x:x, y:y},
					success: function(responseText) {
						$('#table_div').html(responseText);
					}
				});
			});
		});
	
		</script>";
	}

?>

<div style='display:none;'>
	<canvas id='canvas' height='4000' width='4000'></canvas>
    <input type='hidden' id='svgCode'/>
    <input type='hidden' id='pngCode'/>
</div>

<hr/>

<?php if ($hasResult){ ?>
<div class='row'>
<div class='zcol-12'>
<div class='table table-striped table-sm table-responsive'>

    <table id='resultTable'>
        <thead>
            <?php
                echo "<tr>";
                foreach($_SESSION['App']['bubble'][$sessionKey]['Download']['Header'] as $tempKey => $tempValue){
                    echo "<th>{$tempValue}</th>";
                }
                echo "</tr>";
            ?>
        </thead>
        <tbody>
            <?php 
                foreach($csv_info as $tempKey => $currentRow){
                    echo "<tr>";
                        foreach($currentRow as $tempKeyX => $tempValueX){
                            echo "<td>{$tempValueX}</td>";
                        }
                    echo "</tr>";
                }			
            ?>
        </tbody>
    
    </table>
</div>
</div>
</div>
<?php } ?>


<script type="text/javascript">

$(document).ready(function(){
	
	<?php if ($hasResult){ ?>
	$('#resultTable').DataTable({
		"processing": 	true,
		"scrollX": 		true,
	});
	<?php } ?>
		
		
	
	$('.loader').remove();

});

</script>