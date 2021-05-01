<?php


	header('Content-Type: application/json');

  // Specify Folder for Genes & Samples
  $time_stamp   = 0;
  if (isset($_POST['time_stamp']) && intval($_POST['time_stamp']) != 0) {
    $time_stamp = intval($_POST['time_stamp']);
  }
  $dir = get_PCA_dir($time_stamp);

  //---------------------------------------------------------------------------
  // Update variance
  if (file_exists($dir . '/PCA_barchart.csv')) {
    $_SESSION['PCA_R_VAR'] = array();
    $index = 0;
    $file = fopen($dir . '/PCA_barchart.csv', "r") or die('No file.');
    while(($row    = fgetcsv($file)) !== false){
      if ($index == 0) {
        foreach ($row as $k => $colname) {
          if ($colname == 'percentage of variance') $var_col_index = $k;
        }
      }
      if ($index > 0) {
        $_SESSION['PCA_R_VAR'][] = number_format($row[$var_col_index], 2) . '%';
      }
      $index++;
    }
    fclose($file);
  } else {
    unset($_SESSION['PCA_R_VAR']);
  }

  // $dir         = $BXAF_CONFIG['USER_FILES_PCA'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  $dir_chart   = 'files/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];
  if (!is_dir($dir_chart)) {
    mkdir($dir_chart, 0755, true);
  }
  $format      = (file_exists($dir . '/pca.txt')) ? 'txt' : 'csv';
  $OUTPUT      = array('type' => 'Success');
  $TIME        = time();

  // Detect Chart Type First
  $CHART_TYPE  = trim($_POST['chart_type']);


  $x           = trim($_POST['x']);
  $y           = trim($_POST['y']);
  $legend_1    = trim($_POST['legend_1']);
  $legend_2    = trim($_POST['legend_2']);
  $legend_3    = trim($_POST['legend_3']);
  $show_labels = trim($_POST['show_labels']);
  
  
  
  
  // $label_size  = trim($_POST['label_size']);
  // $marker_size = trim($_POST['marker_size']);

  $label_size   = (isset($_POST['label_size']))   ? $_POST['label_size']   : '10';
  $marker_size  = (isset($_POST['marker_size']))  ? $_POST['marker_size']  : '64';
  $graph_width  = (isset($_POST['graph_width']))  ? $_POST['graph_width']  : '640';
  $graph_height = (isset($_POST['graph_height'])) ? $_POST['graph_height'] : '480';


  $_SESSION['PCA_SETTING'] = array(
    'x'     => $x,
    'y'     => $y,
    'color' => $legend_1,
    'shape' => $legend_2,
    'size'  => $legend_3,
  );

  //-------------------------------------------------------------------------------------------
  // Check Illegal Input
  if ($x == $y) {
    $OUTPUT['type']   = 'Error';
    $OUTPUT['detail'] = 'X and Y should be different dimensions.';
    echo json_encode($OUTPUT);
    exit();
  }


  //-------------------------------------------------------------------------------------------
  // Find X & Y Data

  // Get all data
  if ($CHART_TYPE == 'individuals_plot') {
    $file            = fopen($dir . '/PCA_ind.coord.csv', "r") or die('No pca file.');
    $file_attributes = $dir . '/PCA_attributes.' . $format;
  } else if ($CHART_TYPE == 'variables_plot') {
    $file            = fopen($dir . '/PCA_var.coord.csv', "r") or die('No pca file.');
  } else {
    $file            = fopen($dir . '/pca.' . $format, "r") or die('No pca file.');
    $file_attributes = $dir . '/pca_attributes.' . $format;
  }

  $file_data     = array();
  $delimiter = ($format == 'txt') ? "\t" : ",";
  while(($row    = fgetcsv($file, 1000, $delimiter)) !== false){
    $file_data[] = $row;
  }
  fclose($file);
  

  

  // Find column index
  foreach ($file_data[0] as $key => $colname) {
    if ($colname == $x) $x_col_index = $key;
    if ($colname == $y) $y_col_index = $key;
    if ($colname == $legend_3)  {
      $legend_3_col_index = $key;
      $legend_3_src   = 'data';
    }
  }
  // Parse x & y data array
  $data_x        = array();
  $data_y        = array();
  for ($i = 1; $i < count($file_data); $i++) {
    $data_x[]    = $file_data[$i][$x_col_index];
    $data_y[]    = $file_data[$i][$y_col_index];
  }

  // $arrow_chart_data_indexes = arrow_chart_get_data_indexes($data_x, $data_y);
  // print_r($arrow_chart_data_indexes);
  //
  // exit();

  //-------------------------------------------------------------------------------------------
  // Find Legend Data

  $data_color = array();
  $data_shape = array();
  $data_label = array();
  $data_key   = array();

  // Read Legend File
  // if (file_exists($dir . '/pca_attributes.' . $format)) {
  if (file_exists($file_attributes)) {
    $file          = fopen($file_attributes, "r") or die('No file.');
    $legends_data  = array();
    $delimiter     = ($format == 'txt') ? "\t" : ",";
    while(($row    = fgetcsv($file, 1000, $delimiter)) !== false){
      $legends_data[] = $row;
    }
    fclose($file);
	

    // Get Column Index
    foreach ($legends_data[0] as $key => $colname) {
      if ($colname == $legend_1) $legend_1_col_index = $key;
      if ($colname == $legend_2) $legend_2_col_index = $key;
      if ($colname == $legend_3) {
        $legend_3_col_index = $key;
        $legend_3_src = 'attributes';
      }
    }

    // Check Color Legend
    if ($legend_1 == '') {
      for ($i = 1; $i <= count($data_x); $i++) {
        $data_color[]    = 'Active';
        $has_color_var   = 'false';
      }
    } else {
      $has_color_var     = 'true';
      for ($i = 1; $i <= count($data_x); $i++) {
        $data_color[]    = $legends_data[$i][$legend_1_col_index];
      }
    }

    // Check Shape Legend
    if ($legend_2 == '') {
      for ($i = 1; $i <= count($data_x); $i++) {
        $data_shape[]    = '1';
        $has_symbol_var  = 'false';
      }
    } else {
      $has_symbol_var    = 'true';
      for ($i = 1; $i <= count($data_x); $i++) {
        $data_shape[]    = $legends_data[$i][$legend_2_col_index];
      }
    }


    for ($i = 1; $i <= count($data_x); $i++) {
      $data_label[] = $file_data[$i][0];
      $data_key[]   = $i;
    }

  }

  else {
    for ($i = 1; $i <= count($data_x); $i++) {
      $data_color[] = 'Active';
      $data_shape[] = '1';
      $data_label[] = $file_data[$i][0];
      $data_key[]   = $i;
    }
    $has_color_var = 'false';
    $has_symbol_var = 'false';
  }


  //----------------------------------------------------------------------------------------
  // Check Size Legend
  if (!isset($legend_3) || $legend_3 == '') {
    $has_size_var   = 'false';
    for ($i = 1; $i <= count($data_x); $i++) {
      $data_size[]    = '1';
      $displayed_label_size     = 'NULL';
    }
  }
  else {
    $has_size_var          = 'true';
    if (strtoupper(substr($legend_3, 0, 3)) == 'COM' || strtoupper(substr($legend_3, 0, 3)) == 'DIM') {
      $displayed_label_size  = 'PC' . substr($legend_3, -1);
    } else {
      $displayed_label_size  = $legend_3;
    }
    // If from attributes file
    if ($legend_3_src == 'attributes') {
      for ($i = 1; $i <= count($data_x); $i++) {
        $data_size[]    = abs($legends_data[$i][$legend_3_col_index]);
      }
    }
    // If from data file
    else {
      for ($i = 1; $i <= count($data_x); $i++) {
        $data_size[]    = abs($file_data[$i][$legend_3_col_index]);
      }
    }
  }

  //-----------------------------------------------------------------------------------------
  // If Arrow Chart

  if ($CHART_TYPE == 'variables_plot') {

    // No 'size_by'
    $display_option = intval($_POST['display_option']);
    $arrow_chart_data_indexes = arrow_chart_get_data_indexes($x, $y, $display_option, $time_stamp);

    $data_x     = arrow_chart_filter_data($data_x, $arrow_chart_data_indexes);
    $data_y     = arrow_chart_filter_data($data_y, $arrow_chart_data_indexes);
    $data_color = arrow_chart_filter_data($data_color, $arrow_chart_data_indexes);
    $data_shape = arrow_chart_filter_data($data_shape, $arrow_chart_data_indexes);
    $data_label = arrow_chart_filter_data($data_label, $arrow_chart_data_indexes);
    $data_key   = arrow_chart_filter_data($data_key, $arrow_chart_data_indexes);


    $data_size[]    = '1';
    $has_size_var   = 'false';
    $displayed_label_size     = 'NULL';

    $data_arrow = array();
    for ($i = 0; $i < count($data_x); $i++) {
      $data_arrow[] = "arrow";
    }

    if (file_exists($dir . '/PCA_quali.sup.coord.csv')) {
      $has_color_var     = 'true';
      $file          = fopen($dir . '/PCA_quali.sup.coord.csv', "r") or die('No file.');
      $delimiter     = ",";
      $index = 0;
      while(($row    = fgetcsv($file, 1000, $delimiter)) !== false){
        // $file_data[] = $row;
        if ($index > 0) {
          $data_x[]     = $row[$x_col_index];
          $data_y[]     = $row[$y_col_index];
          $data_color[] = 'Active';
          $data_shape[] = '1';
          $data_label[] = $row[0];
          $data_key[]   = count($data_x);
          $data_arrow[] = "point";
        }
        $index++;
      }
      fclose($file);
    }



    if (file_exists($dir . '/PCA_quanti.sup.coord.csv')) {
      $file          = fopen($dir . '/PCA_quanti.sup.coord.csv', "r") or die('No file.');
      $delimiter     = ",";
      $index = 0;
      while(($row    = fgetcsv($file, 1000, $delimiter)) !== false){
        if ($index > 0) {
          $data_x[]     = $row[$x_col_index];
          $data_y[]     = $row[$y_col_index];
          $data_color[] = 'Supplementary';
          $data_shape[] = '1';
          $data_label[] = $row[0];
          $data_key[]   = count($data_x);
          $data_arrow[] = "arrow";
        }
        $index++;
      }
      fclose($file);
    }
  }




  //-------------------------------------------------------------------------------------------
  // Get Label for R Plots
  if (isset($_SESSION['PCA_R_VAR'])) {
    $file = fopen($dir . '/PCA_var.coord.csv', "r") or die('No file.');
    $index = 0;
    while(($row    = fgetcsv($file)) !== false){
      if ($index == 0) {
        foreach ($row as $k => $colname) {
          if ($colname == $x) {
            $x_lael = $_SESSION['PCA_R_VAR'][$k - 1];
            if (substr($x_lael, strlen($x_lael) - 1) == '%') {
              $x_lael = substr($x_lael, 0, strlen($x_lael) - 1);
            }
          }
          if ($colname == $y) {
            $y_lael = $_SESSION['PCA_R_VAR'][$k - 1];
            if (substr($y_lael, strlen($y_lael) - 1) == '%') {
              $y_lael = substr($y_lael, 0, strlen($y_lael) - 1);
            }
          }
        }
        break;
      }
      $index++;
    }
    fclose($file);
  }


  //-------------------------------------------------------------------------------------------
  // Update X Label and Y Label
  if (strtoupper(substr($x, 0, 3)) == 'DIM' || strtoupper(substr($x, 0, 4)) == 'COMP') {
    $x = 'PC' . substr($x, strlen($x) - 1);
  }
  if (strtoupper(substr($y, 0, 3)) == 'DIM' || strtoupper(substr($y, 0, 4)) == 'COMP') {
    $y = 'PC' . substr($y, strlen($y) - 1);
  }



	$xlab = $x;
	if (isset($_SESSION['PCA_DIMENSION_VAR'][$x])) {
		$xlab .= ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$x], 3) . '%)';
	} else if (isset($_SESSION['PCA_R_VAR'])) {
		if ($x_lael != '') {
			$xlab .= ' (' . $x_lael . '%)';
		}
	}
	
	
	$ylab = $y;
	  if (isset($_SESSION['PCA_DIMENSION_VAR'][$y])) {
		$ylab .= ' (' . number_format($_SESSION['PCA_DIMENSION_VAR'][$y], 3) . '%)';
	  } else if (isset($_SESSION['PCA_R_VAR'])) {
		if ($y_lael != '') {
		  $ylab .= ' (' . $y_lael . '%)';
		}
	  }
	  
	  
	
	  
   $data_label = array_values($data_label);
   
    if (true){
		
		
		unset($headers);
		foreach($legends_data as $tempKey => $tempValue){
				
			if ($tempKey == 0){
				$headers = $tempValue;	
			} else {
				$sampleInfo[$tempValue[0]] = array_combine($headers, $tempValue);
			}
		}
		
		$tooltip_text = array();
		foreach($data_label as $tempKey => $currentSampleID){
			
			$currentToolTip = '';
			
			$currentToolTip .= "<div><strong>{$currentSampleID}</strong></div>";
			$currentToolTip .= "<div><strong>{$xlab}</strong>: " . round($data_x[$tempKey], 3) . "</div>";
			$currentToolTip .= "<div><strong>{$ylab}</strong>: " . round($data_y[$tempKey], 3) . "</div>";
			
			
			if (isset($sampleInfo[$currentSampleID])){
				foreach($sampleInfo[$currentSampleID] as $currentColumn => $currentValue){
					if ($currentColumn == 'SampleName') continue;
					$currentToolTip .= "<div><strong>{$currentColumn}</strong>: {$currentValue}</div>";
				}
			} else {
				if ($legend_1 != ''){
					$currentToolTip .= "<div><strong>{$legend_1}</strong>: {$data_color[$tempKey]}</div>";
				}
				
				if (($legend_2 != '') && ($legend_2 != $legend_1)){
					$currentToolTip .= "<div><strong>{$legend_2}</strong>: {$data_shape[$tempKey]}</div>";
				}
			}
			
			$tooltip_text[] = $currentToolTip;
		}
	}
	
	
	
	
	
	  
	  

  //-------------------------------------------------------------------------------------------
  // Generate Chart HTML
  $HTML = '
  <!DOCTYPE html>

  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>  
	<script                 src="../../../../gene_expressions/js/jquery/1.11.3/jquery.min.js"></script>
  	<link rel="stylesheet" href="../../../../gene_expressions/js/shiny/shiny-1.2.0/shiny.css">
	<script                 src="../../../../gene_expressions/js/shiny/shiny-1.2.0/shiny.js"></script>
	
	<script type="text/javascript">
		window.Shiny.outputBindings = null;
	</script>
	
  	<script                 src="../../../../gene_expressions/js/htmlwidgets/htmlwidgets-1.3/htmlwidgets.js"></script>
	
	<link rel="stylesheet" href="../../../../gene_expressions/js/selectize/selectize-0.12.1/css/selectize.bootstrap3.css"/>
	<script                 src="../../../../gene_expressions/js/selectize/selectize-0.12.1/js/selectize.min.js"></script>
	
	<link rel="stylesheet" href="../../../../gene_expressions/js/ionrangeslider/ionrangeslider-2.0.3/css/ion.rangeSlider.css"/>
	<link rel="stylesheet" href="../../../../gene_expressions/js/ionrangeslider/ionrangeslider-2.0.3/css/ion.rangeSlider.skinShiny.css"/>
	<script                 src="../../../../gene_expressions/js/ionrangeslider/ionrangeslider-2.0.3/js/ion.rangeSlider.min.js"></script>
	
	<script                 src="../../../../gene_expressions/js/strftime/strftime-min.js"></script>
	
	<script                 src="../../../../gene_expressions/js/d3/d3-5.6.2.min.js"></script>
	<script                 src="../../../../gene_expressions/js/d3-legend/d3-legend.min.js"></script>
	<script                 src="../../../../gene_expressions/js/d3-labeler/d3-labeler.js"></script>
	
	<link rel="stylesheet" href="../../../../gene_expressions/js/d3.lasso/d3.lasso-1.0.0/lasso.css"/>
	<script                 src="../../../../gene_expressions/js/d3.lasso/d3.lasso-1.0.0/lasso.js"></script>
	
	<link rel="stylesheet" href="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3.css"/>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-utils.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-scales.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-dims_dc.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-menu.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-axes.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-dots.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-arrows.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-labels.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-lines.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-ellipses.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-zoom.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-legend.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-lasso.js"></script>
	<script                 src="../../../../gene_expressions/js/scatterD3/scatterD3-0.8.2/scatterD3-exports.js"></script>
	
	<script                 src="../../../../gene_expressions/js/scatterD3-binding/scatterD3-binding-0.9/scatterD3.js"></script>
	
	<link rel="stylesheet" href="../../../../gene_expressions/js/bootstrap/bootstrap-3.3.7/css/bootstrap.min.css"/>
	<script                 src="../../../../gene_expressions/js/bootstrap/bootstrap-3.3.7/js/bootstrap.min.js"></script>
	
	<style>
		#htmlwidget-1 {
			height: 90vh !important;
		}
	</style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">

      <div class="col-lg-12">
		<div id="htmlwidget-1" style="width:100%;height:auto" class="scatterD3"></div>
      </div>
    </div>
  </div>

<script type="text/javascript">

	var instance;
	var inputData;

		
	HTMLWidgets.addPostRenderHandler(function() {
		var widgets = HTMLWidgets.widgets;
		var jed = widgets.filter(function(widget){
			return widget.name === "scatterD3"
		})[0];
	
	
		var el = document.getElementById("htmlwidget-1");

	    instance = window.HTMLWidgets.getInstance(el);

		inputData =
	{
  "data": {
              "x":          [' . implode(', ', $data_x) . '],

              "y":          [' . implode(', ', $data_y) . '],';

              if ($CHART_TYPE != 'variables_plot' && count($data_size) > 0) {
                $HTML .= '"size_var":   [' . implode(', ', $data_size) . '],';
              } else {
                $has_size_var = 'false';
              }
			  
			  
			  if (true){
				   $HTML .= '"tooltip_text":    ["' . implode('", "', $tooltip_text) . '"],';
			  }
			  
			  

              $HTML .= '

              "col_var":    ["' . implode('", "', $data_color) . '"],

              "lab":        ["' . implode('", "', $data_label) . '"],

              "symbol_var": ["' . implode('", "', $data_shape) . '"],

              "key_var":    [' . implode(', ', $data_key) . ']';

              if ($CHART_TYPE == 'variables_plot') {
                $HTML .= ', "type_var": ["' . implode('", "', $data_arrow) . '"]';
              }
  $HTML .= '
          },
          "settings": {
	"x_log": false,
    "y_log": false,
    "labels_size": ' . $label_size . ',
    "labels_positions": "auto",
    "point_size": ' . $marker_size . ',
    "point_opacity": 1,
    "opacities": null,
    "hover_size": 1,
    "hover_opacity": null,
	
	"xlab": "' . $xlab . '",
	  
	"ylab": "' . $ylab . '",

	"has_labels": ' . $show_labels . ',
    "col_lab": "' . $legend_1 . '",
    "col_continuous": false,
    "col_levels": null,
    "colors": null,
    "ellipses": false,
    "ellipses_data": [],
    "symbol_lab": "' . $legend_2 . '",
    "symbol_levels": null,
    "symbols": null,
    "size_range": [10, 300],
    "size_lab": "' . $displayed_label_size . '",
    "sizes": null,
    "opacity_lab": "NULL",
    "opacities.1": null,
    "unit_circle": false,
    "has_color_var": ' . $has_color_var . ',
    "has_symbol_var": ' . $has_symbol_var . ',
    "has_size_var": ' . $has_size_var . ',
    "has_opacity_var": false,
    "has_url_var": false,
    "has_legend": true,
    "has_tooltips": true,
    //"tooltip_text": null,
    "tooltip_position_x": "right",
    "tooltip_position_y": "bottom",
    "has_custom_tooltips": true,
    "click_callback": null,
    "zoom_callback": null,
    "zoom_on": null,
    "zoom_on_level": 3,
    "disable_wheel": false,
    "fixed": false,
    "legend_width": 150,
    "left_margin": 100,
    "html_id": "scatterD3-RALOWFSR",
    "xlim": null,
    "ylim": null,
    "x_categorical": false,
    "y_categorical": false,
    "menu": true,
    "lasso": true,
    "lasso_callback": "function(sel) {alert(sel.data().map(function(d) {return d.lab}).join(\'n\'));}",
    "dom_id_reset_zoom": "scatterD3-reset-zoom",
    "dom_id_svg_export": "scatterD3-svg-export",
    "dom_id_lasso_toggle": "scatterD3-lasso-toggle",
    "transitions": false,
    "axes_font_size": "120%",
    "legend_font_size": "100%",
    "caption": {
      "title": "",
      "subtitle": "",
      "text": ""
    },
    "lines": {
      "slope": [
        0,
        null
      ],
      "intercept": [
        0,
        0
      ],
      "stroke": [
        "#000",
        "#000"
      ],
      "stroke_width": [
        1,
        1
      ],
      "stroke_dasharray": [
        5,
        5
      ]
    }
  }
};
	


		instance.renderValue(inputData);
		
	});


$(document).ready(function() {
	
	function show_popup(){
		//100: margin-bottom from the scatterD3-dims.js
		var height = $("#htmlwidget-1").height() - 100;
		
		//250: margin-left from above
		var width = $("#htmlwidget-1").width() - 250;
		
		$(".y-axis-label").attr("dy", "-50px");
		$(".y-axis-label").attr("dx", "-" + height/2 + "px");
		$(".y-axis-label").css("text-anchor", "middle");
		$(".y-axis-label").css("font-size", "150%");
		
		$(".x-axis-label").attr("dy", "50px");
		$(".x-axis-label").attr("dx", "-" + width/2 + "px");
		$(".x-axis-label").css("text-anchor", "middle");
		$(".x-axis-label").css("font-size", "150%");
   	};
   window.setTimeout( show_popup, 100 );
   //1000 = 1 second

});
</script>


</body>
</html>
';

  file_put_contents($dir_chart . '/chart_' . $TIME . '.html', $HTML);

  $OUTPUT['type']         = 'Success';
  $OUTPUT['graph_width']  = $graph_width;
  $OUTPUT['graph_height'] = $graph_height;
  $OUTPUT['data_x']       = $data_x;
  $OUTPUT['data_y']       = $data_y;
  $OUTPUT['data_color']   = $data_color;
  $OUTPUT['data_shape']   = $data_shape;
  $OUTPUT['data_label']   = $data_label;
  $OUTPUT['data_key']     = $data_key;
  $OUTPUT['chart_time']   = $TIME;
  $OUTPUT['path']  		  = $dir_chart . '/chart_' . $TIME . '.html';
  $OUTPUT['dir']  		  = $dir;
  
  // $OUTPUT['content'] = $HTML;
  echo json_encode($OUTPUT);
  
?>