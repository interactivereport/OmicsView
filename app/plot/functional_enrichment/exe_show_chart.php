<?php

  // print_r($_POST); exit();

  // 1. Find Comparison Index
  if (isset($_POST['comparison_index']) && trim($_POST['comparison_index']) != '') {
    $COMPARISON_INDEX = intval(trim($_POST['comparison_index']));
  } else {
    $COMPARISON_ID = trim($_POST['comparison_id']);
    $sql = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
            WHERE `ComparisonID`='" . $COMPARISON_ID . "'";
    $data = $DB -> get_row($sql);
    if (!is_array($data) || count($data) <= 1) {
      echo 'Error: No comparison found.';
      exit();
    }
    $COMPARISON_INDEX = $data['ComparisonIndex'];
  }

  //-------------------------------------------------------------------------------------------
  // 2. Check Result Directory
  //
  // If it's in-house comparison
  if (isset($_POST['inhouse']) && $_POST['inhouse']) {
    $INHOUSE = true;
    $dir = $BXAF_CONFIG['INHOUSE_DATA_DIR'] . '/inhouse_comp_' . $COMPARISON_INDEX;
    $dir_pre_direction = $dir . '/cleaned_data.csv_GO_Analysis_';

    $report_link_pre_direction = '../functional_enrichment/Human_GO_out_inhouse/inhouse_comp_' . $COMPARISON_INDEX . '/cleaned_data.csv_GO_Analysis_';
    $report_file = 'report.html';
  } else {
    $INHOUSE = false;
	
	$dir = internal_data_get_comparison_directory($COMPARISON_INDEX);
	//$dir = "{$BXAF_CONFIG['WORK_DIR']}/Internal_Data/Comparisons/comp_{$COMPARISON_INDEX}";
	$dir_pre_direction = $dir . "/comp_{$COMPARISON_INDEX}_GO_Analysis_";
	$report_link_pre_direction = '../functional_enrichment/Human_GO_out/comp_' . $COMPARISON_INDEX . '/comp_' . $COMPARISON_INDEX . '_GO_Analysis_';
	
	
	
    $report_file = 'report.html';
  }


  if (!is_dir($dir)) {
    echo "<p>Error. The result folder for comparison is missing ({$dir}), please verify your files.</p>";
    exit();
  }


  $CHART_ARRAY = array(
    'Biological Process'      => 'biological_process',
    'Cellular Component'      => 'cellular_component',
    'Molecular Function'      => 'molecular_function',
    'KEGG'                    => 'kegg',
    'Molecular Signature'     => 'msigdb',
    'Interpro Protein Domain' => 'interpro',
    'Wiki Pathway'            => 'wikipathways',
    'Reactome'                => 'reactome'
  );
  
  if (isset($BXAF_CONFIG['COMPARISON_INFO']['Charts'])){
	$CHART_ARRAY = $BXAF_CONFIG['COMPARISON_INFO']['Charts'];
  }


  foreach (array('Up', 'Down') as $direction) {

    echo '<div class="row"><div class="col-md-3 p-t-2"><ul class="nav nav-tab nav-stacked list-group">';
    echo '
      <li class="enrichment_tab_left list-group-item list-group-item-info text-center">
        <h4>' . $direction . 'regulated Genes</h4>
      </li>';

    foreach ($CHART_ARRAY as $chart_name => $chart_file_name) {
		 $currentFile = $dir_pre_direction . "{$direction}/" . $chart_file_name . ".txt";
		 if (file_exists($currentFile) == false) continue;
		 
		unset($class);
		if ($chart_name == 'Biological Process'){
			$class = 'active';
		}
		
      	echo "<li class='enrichment_tab_left list-group-item {$class} {$direction}_Class'>&nbsp;";
			echo "<a data-toggle='tab' href='#{$chart_file_name}_div_{$direction}' role='tab' class='chartTrigger' parent_class='{$direction}_Class'>{$chart_name}</a>";
		echo "</li>";
    }

/*
	$functional_enrichment_url = "{$report_link_pre_direction}{$direction}/{$report_file}";
	if (!internal_data_is_public($COMPARISON_INDEX)){
		$functional_enrichment_url = "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_functional_enrichment_report.php?id={$COMPARISON_INDEX}&direction={$direction}";
	}
	*/
	$functional_enrichment_url = "../../bxgenomics/summary/report_enrichment.php?id={$COMPARISON_INDEX}&direction={$direction}";
    echo "<li class='enrichment_tab_left list-group-item'>
            <a href='{$functional_enrichment_url}' style='color:blue !important;' target='_blank'>
              <i class='fa fa-angle-double-right' aria-hidden='true'></i>
              Enrichment Report
            </a>
          </li>";

    echo '</ul></div><div class="tab-content col-md-9 p-x-0" style="min-width:750px;">';


    // Each chart div
    foreach ($CHART_ARRAY as $chart_name => $chart_file_name) {

      echo '<div id="' . $chart_file_name . '_div_' . $direction . '" class="tab-pane fade';
      if ($chart_name == 'Biological Process') echo ' active show';
      echo '" aria-expanded="';

      if ($chart_name == 'Biological Process') {
        echo 'true';
      } else {
        echo 'false';
      }

      echo '">';

	  $currentFile = $dir_pre_direction . "{$direction}/" . $chart_file_name . ".txt";
	  
	  if (file_exists($currentFile) == false) continue;
	  
      $myfile = fopen($currentFile, "r") or die("Unable to open file!");


      $CONTENT_ARRAY = array();
	  $candidate_count = 0;
      while(!feof($myfile)) {
		  $rawData = fgets($myfile);
		  
		  if (trim($rawData) == '') continue;
		  
          $row_content = explode("\t", $rawData);
          $CONTENT_ARRAY[] = $row_content;
		  $candidate_count++;
      }
      fclose($myfile);
	  
	  if ($candidate_count <= 0) continue;
	  


      // Leave only 10 records
      foreach ($CONTENT_ARRAY as $key => $value) {
        $upper_bound = 11;
        if ($key == 0 || $key >= $upper_bound) {
          unset($CONTENT_ARRAY[$key]);
        }
      }

      // Prepare for drawing chart
      $CONTENT_LOGP_ARRAY = array();
      $CONTENT_NAME_ARRAY = array();
      $CONTENT_GENE_NUMBER_ARRAY = array();
      $CONTENT_HOVER_TEXT_ARRAY = array();
      $CONTENT_ANNOTATION_TEXT_ARRAY = array();
      $max_name_length = 0;
      foreach ($CONTENT_ARRAY as $key => $value) {
        $CONTENT_LOGP_ARRAY[] = $value[3];
        $CONTENT_NAME_ARRAY[] = $value[1];
        $max_name_length = max($max_name_length, strlen($value[1]));
        $CONTENT_GENE_NUMBER_ARRAY[] = count(explode(",", $value[10]));
        $CONTENT_HOVER_TEXT_ARRAY[] = '<b>' . $value[1] . '</b><br />Number of Genes: ' . count(explode(",", $value[10]));
        $CONTENT_ANNOTATION_TEXT_ARRAY[] = '&nbsp;log(p):' . number_format($value[3], 2);
      }
      $CONTENT_LOGP_ARRAY = array_reverse($CONTENT_LOGP_ARRAY);
      $CONTENT_NAME_ARRAY = array_reverse($CONTENT_NAME_ARRAY);
      $CONTENT_GENE_NUMBER_ARRAY = array_reverse($CONTENT_GENE_NUMBER_ARRAY);
      $CONTENT_HOVER_TEXT_ARRAY = array_reverse($CONTENT_HOVER_TEXT_ARRAY);
      $CONTENT_ANNOTATION_TEXT_ARRAY = array_reverse($CONTENT_ANNOTATION_TEXT_ARRAY);


      // Output
	  
	  echo "<br/>";
	  echo "<div><a href='javascript:void(0);' id='download_SVG_{$chart_file_name}_{$direction}'>Download SVG File</a></div>";
	  
      echo '<div id="myDiv_' . $chart_file_name . '_' . $direction . '" style=" height: 420px;"></div></div>';

      echo "
      <script>
        $(document).ready(function() {

          var xData = [" . implode(", ", $CONTENT_GENE_NUMBER_ARRAY) . "];
          var yData = [\"" . implode('", "', $CONTENT_NAME_ARRAY) . "\"];
          var annotationText = [\"" . implode('", "', $CONTENT_ANNOTATION_TEXT_ARRAY) . "\"];

          var data = [{
            type: 'bar',
            x: [" . implode(", ", $CONTENT_GENE_NUMBER_ARRAY) . "],
            y: [\"" . implode('", "', $CONTENT_NAME_ARRAY) . "\"],
            orientation: 'h',
            hoverinfo: 'text',
      			text: [\"" . implode('", "', $CONTENT_HOVER_TEXT_ARRAY) . "\"],
            textposition: 'top',
          }];


          var layout = {
        		margin: {
        			l: " . min(intval($max_name_length) * 7, 500) . "
        		},
        		title: '{$chart_name}',
        		showlegend: false,
        		xaxis: {
        			title: 'Number of Genes',
              showticklabels: true,
        		},
        		hovermode: 'closest',
            annotations: []
        	};

          for (var i = 0; i < ";
		  
		  if ($candidate_count < 10){
			  echo $candidate_count-1;
		  } else {
			  echo 10;  
		  }

		  

      echo "; i++) {
            var result = {
              xref: 'x1',
              yref: 'y1',
              x: xData[i] + Math.max.apply(null, xData) / 6,
              y: yData[i],
              text: annotationText[i],
              font: {
                family: 'Arial',
                size: 12,
                color: 'rgb(50, 171, 96)'
              },
              showarrow: false,
            };
            layout.annotations.push(result);
          }


          Plotly.newPlot('myDiv_" . $chart_file_name . "_" . $direction . "', data, layout).then(function(gd) {
            window.requestAnimationFrame(function() {
              window.requestAnimationFrame(function() {
                $('.loader').remove();
              });
            });";
			
			echo '
			$(document).on("click", "#download_SVG_' . $chart_file_name . '_' . $direction . '", function(){
						Plotly
							.downloadImage(gd, {
								filename: "' . $chart_file_name . '_' . $direction . '-Regulated",
								format: "svg",
								height: layout.height,
								width: layout.width
							})
							.then(function(filename){
								
							});
					});';
			
			
			echo "
          });


          var graphDiv" . $chart_file_name . $direction . " = document.getElementById('myDiv_" . $chart_file_name . "_" . $direction . "');
          clickEvent = function(data){
            var name = data.points[0].y; // Geneset Name
            $.ajax({
              type: 'POST',
              url: '../functional_enrichment/exe.php?action=go_to_volcano',
              data: {index: '$COMPARISON_INDEX', name: name, file_name: '$chart_file_name', direction: '$direction', inhouse:'";
                if ($INHOUSE) {
                  echo 'true';
                } else {
                  echo 'false';
                }
          echo "'},
              success: function(response) {";

                if ($chart_file_name == 'wikipathways') {
                  echo "bootbox.alert('<h4>' + name + ':</h4><a href=\"../volcano/index.php?type=custom&src=go&id=$COMPARISON_INDEX";
                    if ($INHOUSE) echo '&inhouse=true';
                  echo "\">";
                  echo "&#9830; View Selected Gene Set in Volcano Plot</a> <br />";
                  echo "<a href=\"../pvjs/index.php?type=" . $chart_file_name . "&pathway=' + encodeURIComponent(name) + '&id=$COMPARISON_INDEX";
                    if ($INHOUSE) echo '&inhouse=true';
                  echo "\">";
                  echo "&#9830; View in Pathway</a><br />";
                  echo "<a href=\"javascript:void(0);\" direction=\"$direction\" chart_name=\"$chart_file_name\" class=\"btn_save_genes\" pathway=\"'+name+'\" comparison=\"$COMPARISON_INDEX\" inhouse=\"";
                    if ($INHOUSE) echo 'true';
                    else echo 'false';
                  echo "\">";
                  echo "&#9830; Save Genes</a>');";
				  
				} elseif ($chart_file_name == 'reactome') {
                  echo "bootbox.alert('<h4>' + name + ':</h4><a href=\"../volcano/index.php?type=custom&src=go&id=$COMPARISON_INDEX";
                    if ($INHOUSE) echo '&inhouse=true';
                  echo "\">";
                  echo "&#9830; View Selected Gene Set in Volcano Plot</a> <br />";
                  echo "<a href=\"../../bxgenomics/tool_pathway/reactome.php?type=" . $chart_file_name . "&pathway=' + encodeURIComponent(name) + '&id=$COMPARISON_INDEX";
                    if ($INHOUSE) echo '&inhouse=true';
                  echo "\">";
                  echo "&#9830; View in Pathway</a><br />";
                  echo "<a href=\"javascript:void(0);\" direction=\"$direction\" chart_name=\"$chart_file_name\" class=\"btn_save_genes\" pathway=\"'+name+'\" comparison=\"$COMPARISON_INDEX\" inhouse=\"";
                    if ($INHOUSE) echo 'true';
                    else echo 'false';
                  echo "\">";
                  echo "&#9830; Save Genes</a>');";


                } else {
					unset($inhouseAttribute);
					if ($INHOUSE){
						$inhouseAttribute = 'true';	
					} else {
						$inhouseAttribute = 'false';	
					}
					
                  echo "bootbox.alert('";
				  echo "<h5>' + name + ':</h5>";
				  
				  echo "<ul>";
				  
					  if (true){
						  echo "<li><a href=\"../volcano/index.php?type=custom&src=go&id={$COMPARISON_INDEX}&inhouse={$inhouseAttribute}\">View selected gene set in volcano plot</a></li>";
						  //echo "<li><a href=\"../volcano/index.php?table=PAGE_List&id={$COMPARISON_INDEX}&geneset=\"'  + encodeURIComponent(name) + '>View Data in Volcano Plot</a></li>";
					  }
					  
					  if ($chart_file_name == 'kegg'){
						  echo "<li><a href=\"../../bxgenomics/tool_pathway/kegg.php?ComparisonIndex={$_POST['comparison_index']}&KEGG=' + name + '\">View in KEGG Pathway</a></li>";
					  }
					  
					  if (true){
						echo "<li><a href=\"javascript:void(0);\" direction=\"{$direction}\" chart_name=\"{$chart_file_name}\" class=\"btn_save_genes\" pathway=\"'+name+'\" comparison=\"{$COMPARISON_INDEX}\" inhouse=\"{$inhouseAttribute}\">Save Genes</a></li>";  
					  }
				  
				  echo "</ul>";
				  echo "');";
                }

                echo "
              }
            });
          }
          graphDiv" . $chart_file_name . $direction . ".on('plotly_click', clickEvent);

        });
      </script>";

    }
    echo '</div></div><hr />';
  }

  
?>

<script>
$(document).ready(function() {
	$(document).on('click', '.btn_save_genes', function() {
	  var direction  = $(this).attr('direction');
	  var inhouse    = $(this).attr('inhouse');
	  var chart_name = $(this).attr('chart_name');
	  var pathway    = $(this).attr('pathway');
	  var comparison = $(this).attr('comparison');
	  $.ajax({
		type: 'POST',
		url: '../functional_enrichment/exe.php?action=save_genes',
		data: {
			direction:direction, 
			chart_name:chart_name, 
			pathway:pathway, 
			comparison:comparison, 
			inhouse: inhouse
			},
		success: function(response){
		  if (response.substring(0, 5) == 'Error') {
			bootbox.alert(response);
		  } else {
			window.location = response;
		  }
		}
	  });
	});
	$(document).on('click', '.enrichment_tab_left a', function() {
	  $('.enrichment_tab_left a').removeClass('active');
	});
	
	$(document).on('click', '.chartTrigger', function() {
		var parent_class = $(this).attr('parent_class');
		$('.' + parent_class).removeClass('active');
		$(this).parent().addClass('active');
	});
	
	
});
</script>