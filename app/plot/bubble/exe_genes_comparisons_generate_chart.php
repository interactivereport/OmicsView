<?php

	internal_data_sanitize_user_input($_POST['data_source'], $_POST['data_source_private_project_indexes']);
	
	// Get All Gene Indexs and Names
	$ALL_GENES = search_genes($_POST['genes']);
	
	if (general_array_size($ALL_GENES) <= 0){
		
		$OUTPUT = array();
		$OUTPUT['error'] = 1;
		$OUTPUT['message'] = 'The gene names are not available. Please verify your genes and try again.';
		header('Content-Type: application/json');
	  	echo json_encode($OUTPUT);	
		exit();
	}
	
	// Get All Comparison Indexs and Names
	$ALL_COMPARISONS_ORG = search_comparisons($_POST['comparisons'], "`ComparisonIndex`, `ComparisonID`, `ComparisonCategory`, `ComparisonContrast`, `ProjectName`", $_POST['data_source'], $_POST['data_source_private_project_indexes']);
	
	
	if (general_array_size($ALL_COMPARISONS_ORG) <= 0){
		
		$OUTPUT = array();
		$OUTPUT['error'] = 1;
		$OUTPUT['message'] = 'The comparison data is not available. Please verify your comparison IDs and try again.';
		header('Content-Type: application/json');
	  	echo json_encode($OUTPUT);	
		exit();
	}
	
	
	
	foreach($ALL_COMPARISONS_ORG as $tempKey => $data){
		
		$ALL_COMPARISONS[$tempKey] = array(
				'ID'       => $data['ComparisonID'],
				'Category' => $data['ComparisonCategory'],
				'Contrast' => $data['ComparisonContrast']
			);
	}
	
	
	
	

	
	// Generate Ouput Data
	$ALL_DATA = array();
	$EXISTING_NUMBER = array( // Save all appeared x-coordinage & y-coordinage
		'gene'                         => array(),
		'comparison'                   => array()
	);
	$downloaded_csv_info = array();



	$geneIndexes       = array_keys($ALL_GENES);
	$comparisonIndexes = array_keys($ALL_COMPARISONS);
	

	
	unset($data_comparisons);
	if ($_POST['data_source']['public'] != ''){
		$data_comparisons  = tabix_search_records_with_index($geneIndexes, $comparisonIndexes, 'ComparisonData');
	}
	
	if ($_POST['data_source']['private'] != ''){
		foreach($_POST['data_source_private_project_indexes'] as $tempKey => $projectIndex){
			$data_comparisons_private = tabix_search_records_with_index_internal_data($projectIndex, $geneIndexes, $comparisonIndexes, 'ComparisonData');
			foreach($data_comparisons_private as $tempKeyX => $tempValueX){
				$data_comparisons[] = $tempValueX;
			}
		}
	}
	
	

  // Group Data by Comp & Genes
  $ALL_DATA_SRC = array();
  $src_data_row = array();
  foreach ($geneIndexes as $geneIndex) {
    $src_data_row[$geneIndex] = array();
  }
  foreach ($comparisonIndexes as $comparisonIndex) {
    $ALL_DATA_SRC[$comparisonIndex] = $src_data_row;
  }
  foreach ($data_comparisons as $tabix_data_row) {
		// Use smallest p-val for duplicate geneIndex & compIndex
		if (count($ALL_DATA_SRC[$tabix_data_row['ComparisonIndex']][$tabix_data_row['GeneIndex']]) > 0
      && $ALL_DATA_SRC[$tabix_data_row['ComparisonIndex']][$tabix_data_row['GeneIndex']]['PValue'] < $tabix_data_row['PValue']) {
      continue;
    }
    $ALL_DATA_SRC[$tabix_data_row['ComparisonIndex']][$tabix_data_row['GeneIndex']] = $tabix_data_row;
  }
  
	


  // foreach ($ALL_DATA_SRC as $comparisonIndex => $value) {
	foreach ($comparisonIndexes as $comparisonIndex) {
    $value = $ALL_DATA_SRC[$comparisonIndex];
    $temp_x                        = array();
    $temp_y                        = array();
    $temp_text                     = array();
    $temp_area                     = array();
    $temp_gene_index               = array();
    $temp_gene_name                = array();
    $temp_comparison_index         = array();
    $temp_comparison_name          = array();

    foreach ($value as $geneIndex => $v) {
      $downloaded_csv_info_row     = array();
	  $currentProject = search_one_record_by_name('project', $ALL_COMPARISONS_ORG[$comparisonIndex]['ProjectName'], 'GetRow');
	  
	  
      $temp_x[]                    = $v['Log2FoldChange'];
      $temp_y[]                    = $ALL_GENES[$geneIndex];
      $text                        = 'Log2FC: ' . $v['Log2FoldChange'] . '<br />';
      $text                       .= 'Category: ' . $ALL_COMPARISONS[$comparisonIndex]['Category'] . '<br />';
      $text                       .= 'Contrast: ' . $ALL_COMPARISONS[$comparisonIndex]['Contrast'] . '<br />';
      $text                       .= 'Gene: ' . $ALL_GENES[$geneIndex] . '<br />';
      $text                       .= 'Comparison: ' . $ALL_COMPARISONS[$comparisonIndex]['ID'] . '<br />';
      $text                       .= 'FDR: ' . $v['AdjustedPValue'] . '<br />';
      $temp_text[]                 = $text;

      $marker                    = (-17) * log10($v['AdjustedPValue']);
      if ($marker > 50)  $marker = 50;
      if ($marker < 8)   $marker = 8;
      $temp_area[]               = $marker;

      $temp_gene_index[]         = $geneIndex;
      $temp_comparison_index[]   = $comparisonIndex;
      $temp_gene_name[]          = $ALL_GENES[$geneIndex];
      $temp_comparison_name[]    = $ALL_COMPARISONS[$comparisonIndex]['ID'];

      $downloaded_csv_info_row[] = $ALL_GENES[$geneIndex];
      $downloaded_csv_info_row[] = $ALL_COMPARISONS[$comparisonIndex]['ID'];
      $downloaded_csv_info_row[] = $v['Log2FoldChange'];
      $downloaded_csv_info_row[] = $v['PValue'];
      $downloaded_csv_info_row[] = $v['AdjustedPValue'];
      $downloaded_csv_info_row[] = $ALL_COMPARISONS[$comparisonIndex]['Category'];
      $downloaded_csv_info_row[] = $ALL_COMPARISONS[$comparisonIndex]['Contrast'];
	  $downloaded_csv_info_row[] = $currentProject['Disease'];
	  $downloaded_csv_info_row[] = $currentProject['StudyType'];
	  $downloaded_csv_info_row[] = $currentProject['TherapeuticArea'];
	  $downloaded_csv_info_row[] = $currentProject['Title'];
	  $downloaded_csv_info_row[] = $currentProject['PubMed'];
	  
	  
	  
      $downloaded_csv_info[]     = $downloaded_csv_info_row;
    }


    // Ignore empty data trace
    if (count($temp_x) > 0) {
      $ALL_DATA[] = array(
        'x'					=> $temp_x,
        'y'					=> $temp_y,
        'name'				=> $ALL_COMPARISONS[$comparisonIndex]['ID'],
        'mode'				=> 'markers',
        'hoverinfo'			=> 'text',
		'text'				=> $temp_text,
        'marker'			=> array(
			'size'				=> $temp_area,
			'gene'				=> $temp_gene_index,
			'gene_name'			=> $temp_gene_name,
			'comparison'		=> $temp_comparison_index,
			'comparison_name'	=> $temp_comparison_name,
        )
      );

      // Save appeared x & y
      $EXISTING_NUMBER['comparison'][] = $ALL_COMPARISONS[$comparisonIndex];
      $temp_genes = array_unique(array_merge($EXISTING_NUMBER['gene'], $temp_y));
      $EXISTING_NUMBER['gene'] = array();
      foreach ($temp_genes as $gene) {
        $EXISTING_NUMBER['gene'][] = $gene;
      }
    }

  }

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
  
  
  	$sessionKey = $_POST['sessionKey'];
  
	if ($sessionKey != ''){
		unset($_SESSION['App']['bubble'][$sessionKey]);
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
		
		$_SESSION['App']['bubble'][$sessionKey]['Download']['Body'] = $downloaded_csv_info;
	}


	//-----------------------------------------------------------------------------
  // DataTable HTML Code
  $TABLE_DATA = array();
  // $TABLE_HEADER = array('GeneName', 'GeneID');
  $TABLE_HEADER = array();
  $data_default_row = array();   // Default row for table data
  foreach ($ALL_COMPARISONS as $comp) {
    $TABLE_HEADER[] = $comp['ID'] . '_logFC';
    $TABLE_HEADER[] = $comp['ID'] . '_PVal';
    $TABLE_HEADER[] = $comp['ID'] . '_FDR';
  }
  foreach ($TABLE_HEADER as $header) {
    $data_default_row[$header] = '.';
  }
  foreach ($geneIndexes as $geneIndex) {
    $TABLE_DATA[$geneIndex] = $data_default_row;
  }

  foreach ($data_comparisons as $row) {
    $temp_comp_id = $ALL_COMPARISONS[$row['ComparisonIndex']]['ID'];

		if (isset($TABLE_DATA[$row['GeneIndex']][$temp_comp_id . '_PVal'])
      && $TABLE_DATA[$row['GeneIndex']][$temp_comp_id . '_PVal'] != '.'
      && $TABLE_DATA[$row['GeneIndex']][$temp_comp_id . '_PVal'] < $row['PValue']) {
      continue;
    }

    $TABLE_DATA[$row['GeneIndex']][$temp_comp_id . '_logFC'] = $row['Log2FoldChange'];
    $TABLE_DATA[$row['GeneIndex']][$temp_comp_id . '_PVal'] = $row['PValue'];
    $TABLE_DATA[$row['GeneIndex']][$temp_comp_id . '_FDR'] = $row['AdjustedPValue'];
  }

  $TIME = time();
  $TABLE = '
  	<hr/>
    <table class="table table-bordered table-sm table-striped table-responsive" id="datatable_' . $TIME . '">
      <thead>
        <tr>
          <th>Gene Name</th>
          <th>Gene Index</th>';

          foreach ($TABLE_HEADER as $header) {
						if (!isset($_POST['table_option_logfc']) && substr($header, -5)=='logFC'
							|| !isset($_POST['table_option_pval']) && substr($header, -4)=='PVal'
							|| !isset($_POST['table_option_fdr']) && substr($header, -3)=='FDR') {
							continue;
						}
						$TABLE .= "<th style='width:250px !important;'>";
						if (strpos($header, '.') !== false) {
							$TABLE .= substr($header, 0, 17) . '<br />' . substr($header, 17);
						} else {
							$TABLE .= $header;
						}
						$TABLE .= "</th>";
          }

  $TABLE .= '
        </tr>
      </thead>
      <tbody>';

      foreach ($geneIndexes as $geneIndex) {
        $TABLE .= '<tr>';
        $TABLE .= '
          <td>' . $ALL_GENES[$geneIndex] . '</td>
          <td>' . $geneIndex . '</td>';
        $index = 0;
        foreach ($TABLE_DATA[$geneIndex] as $key => $value) {

					if (!isset($_POST['table_option_logfc']) && substr($key, -5)=='logFC'
						|| !isset($_POST['table_option_pval']) && substr($key, -4)=='PVal'
						|| !isset($_POST['table_option_fdr']) && substr($key, -3)=='FDR') {
						$index++;
						continue;
					}

					$header = $TABLE_HEADER[$index];

          if (substr($header, -5)=='logFC') {
            $TABLE .= "<td style='width:250px !important;color:" . get_stat_scale_color($value, 'logFC') . ";'>{$value}</td>";
          } else if (substr($header, -4)=='PVal') {
            $TABLE .= "<td style='width:250px !important;color:" . get_stat_scale_color($value, 'PVal') . ";'>{$value}</td>";
          } else if (substr($header, -3)=='FDR') {
            $TABLE .= "<td style='width:250px !important;color:" . get_stat_scale_color($value, 'FDR') . ";'>{$value}</td>";
          }
					// if ($index % 3 == 0) {
          //   $TABLE .= "<td style='width:250px !important;color:" . get_stat_scale_color($value, 'logFC') . ";'>{$value}</td>";
          // } else if ($index % 3 == 1) {
          //   $TABLE .= "<td style='width:250px !important;color:" . get_stat_scale_color($value, 'PVal') . ";'>{$value}</td>";
          // } else {
          //   $TABLE .= "<td style='width:250px !important;color:" . get_stat_scale_color($value, 'FDR') . ";'>{$value}</td>";
          // }
          $index++;
        }
        $TABLE .= '</tr>';
      }

  $TABLE .= '
      </tbody>
    </table>';

  // echo $TABLE; exit();





  // Generate Output
  $OUTPUT = array(
    'data'                         => $ALL_DATA,
    'layout'                       => array(),
    'settings'                     => array(),
	 'error'                     => 0
  );

  $OUTPUT['layout'] = array(
    'title'                        => 'Bubble Plot',
    'xaxis'                        => array('title' => 'Log 2 Fold Change'),
    'yaxis'                        => array('range' => array(-2, count($EXISTING_NUMBER['gene']) + 1)),
  	'margin'                       => array('l' => intval(100 * floatval($_POST['left_factor']))),
    'hovermode'                    => 'closest',
    'height'                       => intval(floatval($_POST['height_factor']) * max(500, count($EXISTING_NUMBER['gene']) * 16 + 200)),
    // 'width'                     => 400
  );

  $OUTPUT['settings'] = array(
    'displaylogo'                  => false,
    'modeBarButtonsToRemove'       => array('sendDataToCloud'),
    'scrollZoom'                   => true,
    'displayModeBar'               => true,
  );

  $OUTPUT['Number'] = $EXISTING_NUMBER;
  $OUTPUT['userid'] = $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  $OUTPUT['time'] 	= $TIME;
  $OUTPUT['table'] 	= "<div class='row'><div class='col-12'>{$TABLE}</div></div>";

  header('Content-Type: application/json');
  echo json_encode($OUTPUT);



?>