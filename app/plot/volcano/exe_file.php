<?php
include_once('config.php');



// Add New Chart
if (isset($_GET['action']) && $_GET['action'] == 'add_new_chart') {

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


  header('Content-Type: application/json');
  $TIME = intval($_POST['time']);
  $OUTPUT = array('time' => $TIME);

  // Create Folders
  if (!is_dir($BXAF_CONFIG['USER_FILES_VOLCANO'] . '/user_csv')) {
      mkdir($BXAF_CONFIG['USER_FILES_VOLCANO'] . '/user_csv', 0755, true);
  }
  $dir = $BXAF_CONFIG['USER_FILES_VOLCANO'] . '/user_csv/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
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

        $_SESSION['volcano_files'][$TIME] = array();
        $file = fopen($dir . '/' . $TIME . '.csv', "r");
        $index = 0;
        while(!feof($file)){
          $row_content = fgetcsv($file);
          // Header row
          if ($index == 0) {
            $OUTPUT['header'] = $row_content;
            $_SESSION['volcano_files'][$TIME]['header'] = $row_content;
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


  $_SESSION['volcano_files'][$TIME]['ID_type'] = $OUTPUT['ID_type'];

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








// Generate Volcano Chart

if (isset($_GET['action']) && $_GET['action'] == 'volcano_generate_chart') {
	// echo '<pre>'; print_r($_POST); echo '</pre>'; // exit();

  $TIME               = $_POST['time_list'];
  $NAME               = $_POST['name_list'];
  $X_COL              = $_POST['x_col_list'];
  $Y_COL              = $_POST['y_col_list'];
  $X_CUTOFF           = $_POST['x_cutoff_list'];
  $Y_CUTOFF           = $_POST['y_cutoff_list'];
  $X_CUTOFF_CUSTOM    = $_POST['custom_x_cutoff_list'];
  $Y_CUTOFF_CUSTOM    = $_POST['custom_y_cutoff_list'];
  $CHART_NUMBER       = count($TIME);
  $FILE_GENE_PAIR     = array();
  $X_MIN              = 0;
  $X_MAX              = 0;
  $Y_MIN              = 0;
  $Y_MAX              = 0;
  $OUTPUT             = array();
  $OUTPUT['type']     = 'Success';
  $OUTPUT['detail']   = array();


  // Generate Multiple Charts
	for ($i = 0; $i < $CHART_NUMBER; $i++) {

    //-----------------------------------------------------------------
    // 1. Get Col Index for X,Y Coordinate
    //-----------------------------------------------------------------
    $x_coordinate_col_index = 0; // Which col for x
    $y_coordinate_col_index = 0; // Which col for y
    $header = $_SESSION['volcano_files'][$TIME[$i]]['header'];
    foreach ($header as $key => $value) {
      if ($X_COL[$i] == $value) {
        $x_coordinate_col_index = $key;
      }
      if ($Y_COL[$i] == $value) {
        $y_coordinate_col_index = $key;
      }
    }


    //-----------------------------------------------------------------
    // 2. Set X,Y Cutoff Values
    //-----------------------------------------------------------------
    if ($X_CUTOFF[$i] == 'enter_value') {
      $x_cutoff = floatval($X_CUTOFF_CUSTOM[$i]);
    } else {
      $x_cutoff = $X_CUTOFF[$i];
    }
    if ($Y_CUTOFF[$i] == 'enter_value') {
      $y_cutoff = floatval($Y_CUTOFF_CUSTOM[$i]);
    } else {
      $y_cutoff = $Y_CUTOFF[$i];
    }

    $chart_name = $NAME[$i];




    //-----------------------------------------------------------------
    // 3. Custom Genes
    //-----------------------------------------------------------------
    $custom_genes = array();
    if ($_POST['volcano_show_gene'] == 'true') {
      foreach(explode("\n", $_POST['gene_names']) as $gene) {
        if (trim($gene) != '') {
          $custom_genes[] = trim($gene);
        }
      }
    }


    //-----------------------------------------------------------------
    // 4. Generate File Gene ID => Symbol Lookup Pair List
    //-----------------------------------------------------------------
    $dir = $BXAF_CONFIG['USER_FILES_VOLCANO'] . '/user_csv/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
    foreach ($_POST['time_list'] as $file_index => $file_time) {
      $FILE_GENE_PAIR[$file_time] = array();
      $file = fopen($dir . '/' . $file_time . '.csv', "r");
      $index = 0;
      while(!feof($file)){
        $row_content = fgetcsv($file);
        if ($index > 0) {
          $file_id_type = $_SESSION['volcano_files'][$file_time]['ID_type'];
          $sql = "SELECT `GeneIndex` FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
                  WHERE `{$file_id_type}`='" . $row_content[0] . "'";
          $symbol_temp = $DB -> get_one($sql);

          // If gene found in database
          if (trim($symbol_temp) != '') {
            $FILE_GENE_PAIR[$file_time][$row_content[0]] = $symbol_temp;
          }
        }
        $index++;
      }
      fclose($file);
    }




    //-----------------------------------------------------------------
    // 5. Get All Data
    //-----------------------------------------------------------------
		$DATA_ALL = array(
			'selected' => array(),
			'up_regulated' => array(),
			'down_regulated' => array(),
			'unregulated' => array()
		);
    $file = fopen($dir . '/' . $TIME[$i] . '.csv', "r");
    $index = 0;

    // Read csv file to get gene info
    while(!feof($file)){
      $row_content = fgetcsv($file);
      if ($index > 0 && in_array($row_content[0], array_keys($FILE_GENE_PAIR[$TIME[$i]]))) {

        // General information
        $rowid = $FILE_GENE_PAIR[$TIME[$i]][$row_content[0]]; // ID in database
        $sql = "SELECT `GeneID`, `GeneName`
            FROM `" . $BXAF_CONFIG['TBL_GENECOMBINED'] . "`
            WHERE `GeneIndex`=" . intval($rowid);
        $gene_info = $DB -> get_row($sql);
        $name = trim($gene_info['GeneID']);
        $alt_name = trim($gene_info['GeneName']);

        // X,Y-coordinalte
        $x = $row_content[$x_coordinate_col_index];
        $y = $row_content[$y_coordinate_col_index];

        // Update Border
  			$X_MIN = min($X_MIN, $x);
  			$X_MAX = max($X_MAX, $x);
  			$Y_MIN = min($Y_MIN, $y);
  			$Y_MAX = max($Y_MAX, $y);


        // Group The Genes
  			// 1. If the gene is entered by the user
  			if (in_array($alt_name, $custom_genes)) {
  				$DATA_ROWS['selected'][] = $name . ',' . $alt_name . ',' . $y . ',' . $x;
  				$DATA_ALL['selected'][] = array(
  					'x' => $x,
  					'y' => $y,
  					'name' => $name,
  					'alt_name' => $alt_name,
  				);
  			}
  			// 2. Up-Regulated Genes
  			else if ($x > $x_cutoff && $y > $y_cutoff) {
  				$DATA_ROWS['up_regulated'][] = $name . ',' . $alt_name . ',' . $y . ',' . $x;
  				$DATA_ALL['up_regulated'][] = array(
  					'x' => $x,
  					'y' => $y,
  					'name' => $name,
  					'alt_name' => $alt_name
  				);
  			}
        // 3. Down-Regulated Genes
        else if ($x < (-1) * $x_cutoff && $y > $y_cutoff) {
  				$DATA_ROWS['down_regulated'][] = $name . ',' . $alt_name . ',' . $y . ',' . $x;
  				$DATA_ALL['down_regulated'][] = array(
  					'x' => $x,
  					'y' => $y,
  					'name' => $name,
  					'alt_name' => $alt_name
  				);
  			}
        // 4. Unregulated Genes
        else {
  				$DATA_ALL['unregulated'][] = array(
  					'x' => $x,
  					'y' => $y,
  					'name' => $name,
  					'alt_name' => $alt_name
  				);
  			}


      }
      $index++;
    }
    fclose($file);


    //-----------------------------------------------------------------
    // 6. Format Output
    //-----------------------------------------------------------------
    $OUTPUT['detail'][] = array(
      'time'       => $TIME[$i],
      'name'       => $NAME[$i],
      'x_cutoff'   => $x_cutoff,
      'y_cutoff'   => $y_cutoff,
      'border'     => array(
        'x_min'    => $X_MIN,
        'x_max'    => $X_MAX,
        'y_min'    => $Y_MIN,
        'y_max'    => $Y_MAX,
      ),
      'all_data'   => $DATA_ALL,
    );


  }

header('Content-Type: application/json');
echo json_encode($OUTPUT);
// echo '<pre>'; print_r($DATA_ALL); echo '</pre>';

exit();





	for ($i = 0; $i < $CHART_NUMBER; $i++) {





		// Output Chart
		echo '<div id="volcano_diagram_container_' . $i . '" class="volcano col-md-6"></div>';

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
								console.log(this);
								bootbox.alert("<h4>Gene " + current_gene.alt_name + "</h4><hr /><a href=\"../../app_gene_expression_rnaseq_single.php?GeneName="+current_gene.alt_name+"\" target=\"_blank\">View gene expression</a><br><a href=\"../bubble/index.php?id="+current_gene.alt_name+"\" target=\"_blank\">View bubble plot</a>");
							}
						}
					}
				}
			},

			tooltip: {
				useHTML: true,
				headerFormat: \'<span style="font-size:12px; color:green">{series.name}<br>\',
				pointFormat: "<b>name: </b>{point.alt_name}<br><b>id: </b><a href=\'http://useast.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={point.name}\' target=_blank>{point.name}</a><br><b>fold change: </b>{point.x}<br><b>significance: </b>{point.y}<br>Click to view detail"
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
					"name":"up-regulated",
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

					foreach($DATA_ALL['up_regulated'] as $value) {
						echo '{"x":' . $value['x'] . ',"y":' . $value['y'] . ', "name":"' . $value['name'] . '", "alt_name":"' . $value['alt_name'] . '"},';
					}

			echo '
					]

				},


				{
				"name":"down-regulated",
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
					foreach($DATA_ALL['unregulated'] as $value) {
						if ($index_unregulated < 5000) {
							echo '{"x":' . $value['x'] . ',"y":' . $value['y'] . ', "name":"' . $value['name'] . '", "alt_name":"' . $value['alt_name'] . '"},';
						}
						$index_unregulated += 1;
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
  // if (isset($DATA_ALL['selected']) && count($DATA_ALL['selected']) > 0) {
  //   echo '
  //   <table class="table table-bordered table-striped datatable">
  //     <thead>
  //       <tr>
  //         <th>Gene Symbol</th>
  //         <th>ID</th>
  //         <th>LogFC</th>
  //         <th>FDR</th>
  //         <th>P-Value</th>
  //       </tr>
  //     </thead>
  //     <tbody>';
  //       foreach ($DATA_ALL['selected'] as $key => $value) {
  //
  //         // Color for LogFC
	// 				if ($value['logfc'] >= 1) {
	// 					$color1 = '#FF0000';
	// 				} else if ($value['logfc'] > 0) {
	// 					$color1 = '#FF8989';
  //         } else if ($value['logfc'] == 0) {
  //         	$color1 = '#E5E5E5';
  //         } else if ($value['logfc'] > -1) {
  //         	$color1 = '#8ECC86';
  //         } else {
  //           $color1 = '#5AC72C';
  //         }
  //         // Color for LogFC
  //         if ($value['FDR'] > 0.05) {
	// 					$color2 = '#9CA4B3';
	// 				} else if ($value['FDR'] <= 0.01) {
  //         	$color2 = '#015402';
  //         } else {
  //           $color2 = '#5AC72C';
  //         }
  //         // Color for P-Value
  //         if ($value['pvalue'] >= 0.01) {
	// 					$color3 = '#9CA4B3';
	// 				} else {
  //           $color3 = '#5AC72C';
  //         }
  //
  //         echo '<tr>
  //           <td>' . $value['name'] . '</td>
  //           <td>' . $value['unique_id'] . '</td>
  //           <td style="color:' . $color1 . ';">' . $value['logfc'] . '</td>
  //           <td style="color:' . $color2 . ';">' . $value['FDR'] . '</td>
  //           <td style="color:' . $color3 . ';">' . $value['pvalue'] . '</td>
  //         </tr>';
  //       }
  //     echo '
  //     </tbody>
  //   </table>
  //   <script>
  //   $(document).ready(function() {
  //     $(".datatable").DataTable({
  //       "dom": \'Bfrtip\',
  //       "buttons": [
  //         \'copy\', \'csv\', \'excel\', \'pdf\', \'print\'
  //       ],
  //     });
  //   });
  //   </script>';
  // }

	exit();
}


?>
