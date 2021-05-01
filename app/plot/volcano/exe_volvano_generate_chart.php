<?php


//Up & Down: 3000
//Unreg: 4000


	echo "<hr/>";

	

	/**
	 * 1. Get Char Number
	 */
	$CHART_NUMBER = intval($_POST['chart_number']);


	/**
	 * 2. Check Custom Cutoff
	 */
	for ($i = 0; $i < $CHART_NUMBER; $i++) {

		$_POST['volcano_fc_custom_cutoff'][$i] = floatval($_POST['volcano_fc_custom_cutoff'][$i]);
		if (($_POST['volcano_fc_cutoff'][$i] == 'enter_value') && ($_POST['volcano_fc_custom_cutoff'][$i] <= 0)){
			echo 'Error. The custom fold change cutoff value needs to be greater than zero.';
			exit();
		}
		
		$_POST['volcano_statistic_custom_cutoff'][$i] = floatval($_POST['volcano_statistic_custom_cutoff'][$i]);
		if (($_POST['volcano_statistic_cutoff'][$i] == 'enter_value') && ($_POST['volcano_statistic_custom_cutoff'][$i] <= 0)){
			echo 'Error. The custom statistic cutoff value needs to be greater than zero.';
			exit();
		}
	}
	
	

	for ($i = 0; $i < $CHART_NUMBER; $i++) {

		// Set Default Value
		$TIME = time();
		
		
		if ($_POST['volcano_fc_cutoff'][$i] == 'enter_value'){
			$_POST['volcano_fc_cutoff'][$i] = floatval($_POST['volcano_fc_custom_cutoff'][$i]);
		}
		$fc_cutoff = $_POST['volcano_fc_cutoff'][$i];
		
		
		if ($_POST['volcano_statistic_cutoff'][$i] == 'enter_value'){
			$_POST['volcano_statistic_cutoff'][$i] = floatval($_POST['volcano_statistic_custom_cutoff'][$i]);
			
			
		}
		$statistic_cutoff = $_POST['volcano_statistic_cutoff'][$i];
		
		
		$CHART_NAME = trim($_POST['chart_name'][$i]);
		$significance_threshold = abs(log10($statistic_cutoff));
		$logfc_threshold = abs(log10($fc_cutoff) / log10(2));
		$X_MIN = 0;
		$X_MAX = 0;
		$Y_MIN = 0;
		$Y_MAX = 0;


		// Get Comparison Index

		// If inhouse comparison, use a different table
		if (0 && substr($_POST['comparison_id'][$i], 0, 9) == '(inhouse)') {
			  $INHOUSE = true;
			  $INHOUSE_NAME = substr($_POST['comparison_id'][$i], 9);
			  $sql = "SELECT `ComparisonIndex`, `ComparisonID`
							FROM `" . $BXAF_CONFIG['TBL_INHOUSE_COMPARISON'] . "`
							WHERE `Name`='" . substr($_POST['comparison_id'][$i], 9) . "'
					  AND `Owner`=" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
			  $comparison_info = $DB -> get_row($sql);
		} else {
			  $INHOUSE = false;
			  
			  $sql = "SELECT `ComparisonIndex`, `ComparisonID`
							FROM `" . $BXAF_CONFIG['TBL_COMPARISONS'] . "`
							WHERE `ComparisonID`='" . addslashes($_POST['comparison_id'][$i]) . "'";
							
 			  $comparison_info = $DB -> get_row($sql);
			  
			  
			  if (!is_array($comparison_info) || count($comparison_info) <= 1) {
				  $sql = "SELECT `ComparisonIndex`, `ComparisonID`, `ProjectIndex`
							FROM `App_User_Data_Comparisons`
							WHERE `ComparisonID`='" . addslashes($_POST['comparison_id'][$i]) . "'";
				  $comparison_info = $DB -> get_row($sql);
			  }
		}

		if (!is_array($comparison_info) || count($comparison_info) <= 1) {
			echo '
			<div id="volcano_diagram_container_' . $i . '" class="volcano col-md-6">
				No comparison found for comparison ID "' . $_POST['comparison_id'][$i] . '".
			</div>';
			continue;
		}
		$comparison_index = intval($comparison_info['ComparisonIndex']);






		// Check Customized Gene Symbol
		$CUSTOMIZE_GENE = false;
		$CUSTOMIZE_GENE_LIST = array();
		$OTHER_GENE_LABEL = 'true';
		if (isset($_POST['volcano_show_gene']) && $_POST['volcano_show_gene'] == 'customize') {
			$CUSTOMIZE_GENE = true;
			$OTHER_GENE_LABEL = 'false';
			foreach(explode("\n", $_POST['volcano_show_gene_names']) as $gene) {
				if (trim($gene) != '') {
					$CUSTOMIZE_GENE_LIST[] = trim($gene);
				}
			}
		}




		// Get All Data
		$DATA_ALL = array(
			'selected' => array(),
			'up_regulated' => array(),
			'down_regulated' => array(),
			'unregulated' => array()
		);
		$DATA_ROWS = array(
			'selected' => array("Gene ID,Gene Name," . $_POST['volcano_y_statistics_' . $i] . ",log2FC"),
			'up_regulated' => array("Gene ID,Gene Name," . $_POST['volcano_y_statistics_' . $i] . ",log2FC"),
			'down_regulated' => array("Gene ID,Gene Name," . $_POST['volcano_y_statistics_' . $i] . ",log2FC")
		);
		if ($_POST['volcano_y_statistics_' . $i] == 'P-value') {
			$Y_COL_NAME = 'PValue';
		} else {
			$Y_COL_NAME = 'AdjustedPValue';
		}

    // --------------------------------------------------------------------------------
    // Get Comparison Data from Tabix or Inhouse Files

    if (0 && $INHOUSE) {
      $dir = $BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/' . $comparison_info['ComparisonID'];
      $inhouse_file = $dir . '/cleaned_data.csv';
      if (file_exists($inhouse_file)) {
        $file = fopen($inhouse_file,"r");
        $index = 0;
        while(! feof($file)) {
          $row = fgetcsv($file);
          if ($index > 0) {
            $comparison_gene_data[] = $row;
          }
          $index++;
        }
        fclose($file);
      }
    } else {
      $comparisonIndexList = array($comparison_index);
	  if (internal_data_is_public($comparison_index)){
	      $result = tabix_search_records_with_index('', $comparisonIndexList, 'ComparisonData');
	  } else {
		  $result = tabix_search_records_with_index_internal_data($comparison_info['ProjectIndex'], '', $comparisonIndexList, 'ComparisonData');
	  }
      foreach ($result as $value) {
        $comparison_gene_data[] = $value;
      }

    }
	
	
	$SQL = "SELECT `GeneIndex`, `GeneName`, `Description` FROM `GeneCombined`";
	$all_gene_info = $DB -> GetAssoc($SQL);
	
		foreach ($comparison_gene_data as $key => $value) {
			
			$currentGeneIndex 		= $value['GeneIndex'];
			$currentComparisonIndex = $value['ComparisonIndex'];

			// Get basic info for INHOUSE data or not
			if (0 && $INHOUSE) {
				$x        = floatval($value[1]);
				$y        = -log10(floatval($value[2]));
				bcscale(5);
				$y        = bcpow($y, 1);
				$name     = trim($value[0]);
				$alt_name = $name;
			} else {
				$x = floatval($value['Log2FoldChange']);
				$y = -log10(floatval($value[$Y_COL_NAME]));
				bcscale(5);
				$y = bcpow($y, 1);
				$sql = "SELECT `GeneID`, `GeneName` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}` WHERE `GeneIndex`=" . intval($value['GeneIndex']);
						
				$gene_info = $DB -> get_row($sql);
				$name = trim($gene_info['GeneID']);
				$alt_name = trim($gene_info['GeneName']);
			}

			// Update Border
			$X_MIN = min($X_MIN, $x);
			$X_MAX = max($X_MAX, $x);
			$Y_MIN = min($Y_MIN, $y);
			$Y_MAX = max($Y_MAX, $y);

			// Group The Genes
			// 1. If the gene is entered by the user
			if (in_array($alt_name, $CUSTOMIZE_GENE_LIST)) {
				$DATA_ROWS['selected'][] = $name . ',' . $alt_name . ',' . $y . ',' . $x;
				$row_temp = array(
				  	'x' => $x,
					'y' => $y,
					'name' => $name,
					'alt_name' => $alt_name,
				);
				
				if (!$INHOUSE) {
				  $row_temp['logfc'] = $value['Log2FoldChange'];
				  $row_temp['FDR'] = $value['AdjustedPValue'];
				  $row_temp['unique_id'] = $value['Name'];
				  $row_temp['GeneIndex'] = $value['GeneIndex'];
				  
				  $row_temp['pvalue'] = $value['PValue'];
				}
				
				$DATA_ALL['selected'][] = $row_temp;
			} else if ($x > $logfc_threshold && $y > $significance_threshold) {
				// 2. Up-Regulated Genes
				$DATA_ROWS['up_regulated'][] = $name . ',' . $alt_name . ',' . $y . ',' . $x;
				$DATA_ALL['up_regulated'][] = array(
					'x' => $x,
					'y' => $y,
					'name' => $name,
					'alt_name' => $alt_name
				);
				$regulatedGenes['Up'][] = $currentGeneIndex;
			// 3. Down-Regulated Genes
			} else if ($x < (-1) * $logfc_threshold && $y > $significance_threshold) {
				$DATA_ROWS['down_regulated'][] = $name . ',' . $alt_name . ',' . $y . ',' . $x;
				$DATA_ALL['down_regulated'][] = array(
					'x' => $x,
					'y' => $y,
					'name' => $name,
					'alt_name' => $alt_name
				);
				$regulatedGenes['Down'][] = $currentGeneIndex;
			// 4. Unregulated Genes
			} else {
				$DATA_ALL['unregulated'][] = array(
					'x' => $x,
					'y' => $y,
					'name' => $name,
					'alt_name' => $alt_name
				);
				$regulatedGenes['Un'][] = $currentGeneIndex;
			}
		}

		// Save Row Number
		$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/up_" . $i . ".csv","w");
		foreach ($DATA_ROWS['up_regulated'] as $line){
			fputcsv($file,explode(',',$line));
		}
		fclose($file);

		$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/down_" . $i . ".csv","w");
		foreach ($DATA_ROWS['down_regulated'] as $line){
			fputcsv($file,explode(',',$line));
		}
		fclose($file);

		$file = fopen("files/" . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'] . "/selected_" . $i . ".csv","w");
		foreach ($DATA_ROWS['selected'] as $line){
			fputcsv($file,explode(',',$line));
		}
		fclose($file);

		if (function_exists('sort_by_y')) {
			function sort_by_y($a,$b) {
				if ($a['y'] == $b['y']) return 0;
				return ($a['y'] < $b['y'])?-1:1;
	    	}
		}
    usort($DATA_ALL['unregulated'], "sort_by_y");
	
		if (true){
			$general_get_unique_id = md5(json_encode($regulatedGenes['Up']) . '::' . $fc_cutoff . '::' . $statistic_cutoff);
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Value'] 					= $regulatedGenes['Up'];
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Title'] 					= "Upregulated Genes";
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Count'] 					= number_format(general_array_size($regulatedGenes['Up']));
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Y']	  					= $Y_COL_NAME;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['X']	  					= 'Log2FoldChange';
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Direction'] 				= 'Up';
			
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['fc_cutoff'] 				= $fc_cutoff;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['statistic_cutoff'] 		= $statistic_cutoff;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['significance_threshold'] 	= $significance_threshold;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['logfc_threshold'] 		= $logfc_threshold;
			
			
			$URL_Up = "../../gene_expressions/app_comparison_genes.php?ID={$currentComparisonIndex}&sessionID={$general_get_unique_id}";
		}
		
		if (true){
			$general_get_unique_id = md5(json_encode($regulatedGenes['Down']) . '::' . $fc_cutoff . '::' . $statistic_cutoff);
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Value'] 					= $regulatedGenes['Down'];
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Title'] 					= "Downregulated Genes";
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Count'] 					= number_format(general_array_size($regulatedGenes['Down']));
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Y']	  					= $Y_COL_NAME;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['X']	  					= 'Log2FoldChange';
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['Direction'] 				= 'Down';
			
						
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['fc_cutoff'] 				= $fc_cutoff;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['statistic_cutoff'] 		= $statistic_cutoff;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['significance_threshold'] 	= $significance_threshold;
			$_SESSION['Comparison_Gene'][$general_get_unique_id]['logfc_threshold'] 		= $logfc_threshold;
			
			$URL_Down = "../../gene_expressions/app_comparison_genes.php?ID={$currentComparisonIndex}&sessionID={$general_get_unique_id}";
		}
		
		if (true){
			$URL_All = "../../gene_expressions/app_comparison_genes.php?ID={$currentComparisonIndex}";
		}
		
		
		
			
		
		echo "<h4>Summary</h4>";
		echo "
		<div class='row'>
			<div class='col-lg-5 col-sm-12'>
				<div class='table-responsive'>
					<table class='table table-bordered table-sm table-striped'>
						<tbody>
							<tr>
								<td>Comparison:</td>
								<td><a href='../search_comparison/comparison_gene_table.php?id={$comparison_index}' target='_blank'>{$comparison_info['ComparisonID']}</a></td>
							</tr>
							<tr>
								<td>Fold Change Cutoff:</td>
								<td>{$_POST['volcano_fc_cutoff'][$i]}</td>
							</tr>
							<tr class=''>
								<td>Log<sub>2</sub>(Fold Change Cutoff):</td>
								<td>" . number_format(floatval(log10($_POST['volcano_fc_cutoff'][$i]) / log10(2)), 3) . "</td>
							</tr>
							<tr class=''>
								<td>Stat Cutoff:</td>
								<td>{$_POST['volcano_statistic_cutoff'][$i]}</td>
							</tr>
							<tr class=''>
								<td>-Log<sub>10</sub>(Stat Cutoff):</td>
								<td>" . number_format(floatval(log10($_POST['volcano_statistic_cutoff'][$i]) * (-1)), 3) . "</td>
							</tr>
							
							<tr class=''>
								<td># of Upregulated Genes:</td>
								<td><a href='{$URL_Up}' target='_blank'>" . number_format(general_array_size($regulatedGenes['Up'])) . "</a></td>
							</tr>
							<tr class=''>
								<td># of Downregulated Genes:</td>
								<td><a href='{$URL_Down}' target='_blank'>" . number_format(general_array_size($regulatedGenes['Down'])) . "</a></td>
							</tr>
							
							<tr class=''>
								<td>All Genes:</td>
								<td><a href='{$URL_All}' target='_blank'>" . number_format(general_array_size($regulatedGenes['Up']) + general_array_size($regulatedGenes['Down']) + general_array_size($regulatedGenes['Un'])) . "</a></td>
							</tr>
							
						</tbody>
					</table>
				</div>
			</div>
		</div>

	
	
	
	";


		// Output Chart
		echo '
    <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
      <div id="volcano_diagram_container_' . $i . '" class="volcano"></div>
    </div>';
	
		echo '
		<script>

		$(document).ready(function(){

		$(\'#volcano_diagram_container_' . $i . '\').highcharts({

			"chart":{"type":"scatter","zoomType":"xy"},

			"title": { "text": "' . $CHART_NAME . '" },

			"xAxis":{
				"title":{
					"enabled":true,
					"text":"log2(Fold Change)"
				},
				"startOnTick":true,
				"endOnTick":true,
				"showLastLabel":true,
				"gridLineWidth":1,
				"min":' . $X_MIN . ',
				"max":' . $X_MAX . '
			},

			"yAxis":{
				"title":{
					"enabled":true,
					"text":"-log10(' . $_POST['volcano_y_statistics_' . $i] . ')"
				},
				"startOnTick":true,
				"endOnTick":true,
				"showLastLabel":true,
				"gridLineWidth":1,
				"min":' . $Y_MIN . ',
				"max":' . $Y_MAX . '
			},

			"plotOptions":{
				"scatter":{
					"allowPointSelect":true,
					"marker": {"radius":2,"states":{"hover":{"enabled":true,"lineColor":"#333333"}}},
					"states": {"hover":{"marker":{"enabled":true}}},
					"turboThreshold":50000
				},

				series: {
					cursor: \'pointer\',
					point: {
						events: {
							click: function (e) {
								$(\'#geneList\').val($(\'#geneList\').val() + \' \' + this.alt_name);
								var current_gene = this;
								bootbox.alert("<h4>Gene " + current_gene.alt_name + "</h4><hr /><a href=\"' . $BXAF_CONFIG['BXAF_APP_URL_GENE_EXPRESSIONS'] . '/app_gene_expression_rnaseq_single.php?GeneName="+current_gene.alt_name+"\" target=\"_blank\">Gene Expression</a>';

                if (!$INHOUSE) {
                  	echo '<br><a href=\"../bubble/index.php?id="+current_gene.alt_name+"\" target=\"_blank\">Bubble Plot</a>';
				  	echo '<br><a href=\"../../gene_expressions/app_gene_review_internal.php?Gene="+current_gene.alt_name+"\" target=\"_blank\">Gene Details</a>';
					
					$get_internal_gene_viewer_text = get_internal_gene_viewer_text();
					$get_internal_gene_viewer_url = get_internal_gene_viewer_url();
					if ($get_internal_gene_viewer_text != ''){
						echo '<br><a href=\"' . $get_internal_gene_viewer_url . '"+current_gene.alt_name+"\" target=\"_blank\">' . $get_internal_gene_viewer_text . '</a>';
						
					}
                }
				
				
				
				
        echo '");
							}
						}
					}
				}
			},

			tooltip: {
				useHTML: true,
				headerFormat: \'<span style="font-size:12px; color:green">{series.name}<br>\',
				pointFormat: "<b>name: </b>{point.alt_name}<br><b>id: </b><a href=\'http://useast.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={point.name}\' target=_blank>{point.name}</a><br><b>fold change: </b>{point.x}<br><b>significance: </b>{point.y}<br>"
			},

			"series": [';

			// Genes Entered By Users
			if ($CUSTOMIZE_GENE) {
				echo '
				{
					"name":"selected",
					"color":"#f79e4d",
					marker: {
						radius: 4
					},
					dataLabels: {
						enabled: true,
						x: 35,
						y: 5,
						formatter:function() {
							return this.point.alt_name;
							/*if (this.point.y>2) {
								return this.point.alt_name;
							}*/
						},
						style:{color:"black"}
					},

					"data":[';

					foreach($DATA_ALL['selected'] as $value) {
						echo '{"x":' . $value['x'] . ',"y":' . $value['y'] . ', "name":"' . $value['name'] . '", "alt_name":"' . $value['alt_name'] . '"},';
					}

				echo '
					]
				},';
			}


			echo '

				{
					"name":"Upregulated",
					"color":"#FF0000",
					dataLabels: {
						enabled: ' . $OTHER_GENE_LABEL . ',
						x: 35,
						y: 5,
						formatter:function() {
							if (this.point.y>2) {
							return this.point.alt_name;
							}
						},
						style:{color:"black"}
					},

					"data":[';

					shuffle($DATA_ALL['up_regulated']);
					$DATA_ALL['up_regulated'] = array_slice($DATA_ALL['up_regulated'], 0, 3000);
					foreach($DATA_ALL['up_regulated'] as $value) {
						echo '{"x":' . $value['x'] . ',"y":' . $value['y'] . ', "name":"' . $value['name'] . '", "alt_name":"' . $value['alt_name'] . '"},';
					}

			echo '
					]

				},


				{
				"name":"Downregulated",
				"color":"#009966",
				dataLabels: {
					enabled: ' . $OTHER_GENE_LABEL . ',
					x:-35,
					y: 5,
					formatter:function() {
						if (this.point.y>2) {
						return this.point.alt_name;
						}
					},
					style:{color:"black"}
				},

				"data":[';
				
					shuffle($DATA_ALL['down_regulated']);
					$DATA_ALL['down_regulated'] = array_slice($DATA_ALL['down_regulated'], 0, 3000);
					foreach($DATA_ALL['down_regulated'] as $value) {
						echo '{"x":' . $value['x'] . ',"y":' . $value['y'] . ', "name":"' . $value['name'] . '", "alt_name":"' . $value['alt_name'] . '"},';
					}

			echo '
					]
				},

				// unregulated data
				{
					"name":"unregulated",
					"color":"#AEB6BF",
					"data":[';

					$index_unregulated = 0;
					shuffle($DATA_ALL['unregulated']);
					$DATA_ALL['unregulated'] = array_slice($DATA_ALL['unregulated'], 0, 4000);
					foreach($DATA_ALL['unregulated'] as $value) {
						echo '{"x":' . $value['x'] . ',"y":' . $value['y'] . ', "name":"' . $value['name'] . '", "alt_name":"' . $value['alt_name'] . '"},';
					}

			echo '
					]
				},


				{
					"name":"downfold threshold",
					"color":"#000000",
					"type":"line",
					"dashStyle":"Dash",
					"marker":{"enabled":false},
					"data":[[-' . $logfc_threshold . ',' . $Y_MIN . '],[-' . $logfc_threshold . ',' . 2 * $Y_MAX . ']]
				},


				{
					"name":"upfold threshold",
					"color":"#000000",
					"type":"line",
					"dashStyle":"Dash",
					"marker":{"enabled":false},
					"data":[[' . $logfc_threshold . ',' . $Y_MIN . '],[' . $logfc_threshold . ',' . 2 * $Y_MAX . ']]
				},


				{
					"name":"significance threshold",
					"color":"#000000",
					"type":"line",
					"dashStyle":"DashDot",
					"marker":{"enabled":false},
					"data":[[' . 2*$X_MIN . ',' . $significance_threshold . '],[' . 2*$X_MAX . ',' . $significance_threshold . ']]
				}
			]
		});
		});
		
		

		</script>';

	}


	// Data Table
  if (isset($DATA_ALL['selected']) && count($DATA_ALL['selected']) > 0) {
    echo '
    <table class="table table-bordered table-striped datatable" id="volcano_table">
      <thead>
        <tr>
          <th>Gene ID</th>
		  <th>Description</th>
          <th>Log2FC</th>
          <th>FDR</th>
          <th>P-Value</th>
        </tr>
      </thead>
      <tbody>';
        foreach ($DATA_ALL['selected'] as $key => $value) {
			
			$currentGeneIndex = $value['GeneIndex'];
			$value['GeneName'] = $all_gene_info[$currentGeneIndex]['GeneName'];
			$value['Description'] = $all_gene_info[$currentGeneIndex]['Description'];

					$value['logfc'] = $value['x'];

          // Color for LogFC
					if ($value['logfc'] >= 1) {
						$color1 = '#FF0000';
					} else if ($value['logfc'] > 0) {
						$color1 = '#FF8989';
          } else if ($value['logfc'] == 0) {
          	$color1 = '#E5E5E5';
          } else if ($value['logfc'] > -1) {
          	$color1 = '#7070FB';
          } else {
            $color1 = '#0000FF';
          }
          // Color for LogFC
          if ($value['FDR'] > 0.05) {
						$color2 = '#9CA4B3';
					} else if ($value['FDR'] <= 0.01) {
          	$color2 = '#015402';
          } else {
            $color2 = '#5AC72C';
          }
          // Color for P-Value
          if ($value['pvalue'] >= 0.01) {
						$color3 = '#9CA4B3';
					} else {
            $color3 = '#5AC72C';
          }

          echo '<tr>
            <td>' . $value['GeneName'] . '</td>
            <td>' . $value['Description'] . '</td>
            <td style="color:' . $color1 . ';">' . $value['logfc'] . '</td>
            <td style="color:' . $color2 . ';">' . $value['FDR'] . '</td>
            <td style="color:' . $color3 . ';">' . $value['pvalue'] . '</td>
          </tr>';
        }
      echo '
      </tbody>
    </table>
    <script>
    $(document).ready(function() {
      $("#volcano_table").DataTable({
        "dom": \'lBfrtip\',
        "buttons": [
          \'copy\', \'csv\', \'excel\', \'pdf\', \'print\'
        ],
      });
    });
    </script>';
  }

	
	
?>

<div style='display:none;'>
	<canvas id='canvas' height='1000' width='1000'></canvas>
</div>