<?php

include_once(dirname(__DIR__) . '/config/config.php');

$BXAF_CONFIG['CURRENT_SYSTEM_SUBDIR']    = $BXAF_CONFIG['WORK_URL'] . "tool_pathway/{$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}/";
$BXAF_CONFIG['CURRENT_SYSTEM_CACHE']     = $BXAF_CONFIG['BXAF_ROOT_DIR'] . $BXAF_CONFIG['CURRENT_SYSTEM_SUBDIR'];
$BXAF_CONFIG['CURRENT_SYSTEM_CACHE_URL'] = $BXAF_CONFIG['BXAF_ROOT_URL'] . $BXAF_CONFIG['CURRENT_SYSTEM_SUBDIR'];
if (!is_dir($BXAF_CONFIG['CURRENT_SYSTEM_CACHE'])) mkdir($BXAF_CONFIG['CURRENT_SYSTEM_CACHE'], 0755, true);


$BXAF_CONFIG['SPECIES'] = ucfirst(strtolower($BXAF_CONFIG['APP_SPECIES']));
$_SESSION['SPECIES_DEFAULT'] = $BXAF_CONFIG['SPECIES'];


// All Pathways
$BXAF_CONFIG['PATHWAY_LIST'] = array();

$sql = "SELECT `File`, `Name` FROM `tbl_wikipathways_info` WHERE `Species` LIKE '" . $BXAF_CONFIG['SPECIES'] . "%' AND `Type` IN ('Protein', 'GeneProduct', 'Rna') AND `DB_Name` IN ('Entrez Gene', 'Uniprot-TrEMBL', 'Ensembl')";
$BXAF_CONFIG['PATHWAY_LIST'] = $BXAF_MODULE_CONN->get_assoc('File', $sql );

ksort($BXAF_CONFIG['PATHWAY_LIST']);



// All KEGG Pathways
$BXAF_CONFIG['KEGG_PATHWAY_LIST'] = array();

$list = explode("\n", file_get_contents(__DIR__ . "/kegg/kegg_list.txt"));
foreach($list as $row){
    list($identifier, $name) = explode("\t", $row);
    if($BXAF_CONFIG['SPECIES'] == 'Human' && strpos($identifier, "hsa") === 0) $BXAF_CONFIG['KEGG_PATHWAY_LIST'][$identifier] = htmlspecialchars($name, ENT_QUOTES);
    else if($BXAF_CONFIG['SPECIES'] == 'Mouse' && strpos($identifier, "mmu") === 0) $BXAF_CONFIG['KEGG_PATHWAY_LIST'][$identifier] = htmlspecialchars($name, ENT_QUOTES);
    else if($BXAF_CONFIG['SPECIES'] == 'Rat' && strpos($identifier, "rno") === 0) $BXAF_CONFIG['KEGG_PATHWAY_LIST'][$identifier] = htmlspecialchars($name, ENT_QUOTES);
}
asort($BXAF_CONFIG['KEGG_PATHWAY_LIST']);




/**
 * Make Gradient Color
 */
function gradient($HexFrom, $HexTo, $ColorSteps) {

	$FromRGB['r'] = hexdec(substr($HexFrom, 0, 2));
	$FromRGB['g'] = hexdec(substr($HexFrom, 2, 2));
	$FromRGB['b'] = hexdec(substr($HexFrom, 4, 2));

	$ToRGB['r'] = hexdec(substr($HexTo, 0, 2));
	$ToRGB['g'] = hexdec(substr($HexTo, 2, 2));
	$ToRGB['b'] = hexdec(substr($HexTo, 4, 2));

	$StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($ColorSteps);
	$StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($ColorSteps);
	$StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($ColorSteps);

	$GradientColors = array();

	for($i = 0; $i <= $ColorSteps; $i++)
	{
			$RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
			$RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
			$RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

			$HexRGB['r'] = sprintf('%02x', ($RGB['r']));
			$HexRGB['g'] = sprintf('%02x', ($RGB['g']));
			$HexRGB['b'] = sprintf('%02x', ($RGB['b']));

			$GradientColors[] = implode(NULL, $HexRGB);
	}

	return $GradientColors;
}




/**
 * Map Value to Gradient Color
 */

function mapToGradient($logFC, $cutoff=1) {

	// Not exists
	if ($logFC == '.' || trim($logFC) == '') {
		return 'CCCCCC';
	}

	//---------------------------------------------------------------------------
	// If Not Customized Cutoff
	//---------------------------------------------------------------------------
	if (!is_array($cutoff)) {
		// Red
		if ($logFC >= $cutoff) {
			return "FF0000";
		}

		// Blue
		if ($logFC <= (-1) * $cutoff) {
			return "0000FF";
		}

		// White
		if ($logFC == 0) {
			return "FFFFFF";
		}

		// Positive Value
		if ($logFC > 0) {
			$gradients = gradient('FFFFFF', 'FF0000', 10);
			return $gradients[$logFC * 10 / $cutoff % 10];
		}

		// Negative Value
		if ($logFC < 0) {
			$gradients = gradient('FFFFFF', '0000FF', 10);
			return $gradients[(-1) * $logFC * 10 / $cutoff % 10];
		}
	}

	//---------------------------------------------------------------------------
	// Customized Cutoff
	//---------------------------------------------------------------------------

	else {

		// Set values
		$cutoff_values = explode(",", $cutoff['cutoff']);
		$min = floatval(trim($cutoff_values[0]));
		$mid = floatval(trim($cutoff_values[1]));
		$max = floatval(trim($cutoff_values[2]));

		// Set colors
		$color_style = $cutoff['color_style'];
		if ($color_style == 0) { // Blue - White - Red
			$color_min = '0000FF';
			$color_mid = 'FFFFFF';
			$color_max = 'FF0000';
		}
		if ($color_style == 1) { // Green - White - Red
			$color_min = '008000';
			$color_mid = 'FFFFFF';
			$color_max = 'FF0000';
		}
		if ($color_style == 2) { // Yellow - White - Blue
			$color_min = 'FFD700';
			$color_mid = 'FFFFFF';
			$color_max = '0000FF';
		}
		if ($color_style == 3) { // Yellow - Orange - Red
			$color_min = 'FFD700';
			$color_mid = 'FFA500';
			$color_max = 'FF0000';
		}

		// On the right side
		if ($logFC >= $mid) {
			if ($logFC >= $max) {
				return $color_max;
			}
			$value_exceeded = floatval($logFC - $mid) / floatval($max - $mid);
			$gradients = gradient($color_mid, $color_max, 10);
			return $gradients[$value_exceeded * 10 % 10];
		}
		// On the left side
		else {
			if ($logFC <= $min) {
				return $color_min;
			}
			$value_exceeded = floatval($mid - $logFC) / floatval($mid - $min);
			$gradients = gradient($color_mid, $color_min, 10);
			return $gradients[$value_exceeded * 10 % 10];

		}

	}


}





/**
 * Generate Legend
 */
function getColorLegend($field, $cutoff) {

	//echo $field;
	$result = '';

	if ($field == 'Log2FoldChange') {

		$result .= '<div class="font-sanspro-300 row mx-0" style="margin-top:5px;">Log2FC:</div>';

		if (!is_array($cutoff)) {
			$min = (-1) * ($cutoff + 1);
			$mid = 0;
			$max = $cutoff + 1;
			$color_left = '0000FF';
			$color_mid = 'FFFFFF';
			$color_right = 'FF0000';
		}
		else {
			$value_array = explode(',', $cutoff['value']);
			$min = floatval(trim($value_array[0]));
			$mid = floatval(trim($value_array[1]));
			$max = floatval(trim($value_array[2]));
			$color_style = $cutoff['color_style'];
			// Set Color
			if ($color_style == 0) {
				$color_left = '0000FF';
				$color_mid = 'FFFFFF';
				$color_right = 'FF0000';
			}
			if ($color_style == 1) {
				$color_left = '008000';
				$color_mid = 'FFFFFF';
				$color_right = 'FF0000';
			}
			if ($color_style == 2) {
				$color_left = 'FFD700';
				$color_mid = 'FFFFFF';
				$color_right = '0000FF';
			}
			if ($color_style == 3) {
				$color_left = 'FFD700';
				$color_mid = 'FFA500';
				$color_right = 'FF0000';
			}
		}


		$gradient_left = gradient($color_left, $color_mid, 50);
		$gradient_right = gradient($color_mid, $color_right, 50);
		foreach ($gradient_left as $color) {
			$result .= '<div style="float:left; display:inline-block; background-color:#' . $color . '; width:2px; height:10px;"></div>';
		}
		foreach ($gradient_right as $color) {
			$result .= '<div style="float:left; display:inline-block; background-color:#' . $color . '; width:2px; height:10px;"></div>';
		}
		$result .= '
		<div class="row mx-0" style="width: 200px;">
			<div style="float:left; display:inline-block;">' . $min . '</div>
			<div style="float:left; display:inline-block; margin-left:80px;">' . $mid . '</div>
			<div style="float:left; display:inline-block; margin-left:95px;">' . $max . '</div>
		</div>';

	}

	// Field is P-Value or FDR
	else {

		if ($field == 'PValue') {
			$result .= '<div class="font-sanspro-300 row mx-0" style="margin-top:5px;">P-Value:</div>';
		}
		else {
			$result .= '<div class="font-sanspro-300 row mx-0" style="margin-top:5px;">FDR:</div>';
		}

		// Three Colors
		if ($cutoff == 0) {
			$result .= '
			<div class="row mx-0">
				<div style="float:left; display:inline-block; height:15px; width:20px; background-color:#6B8E23;"></div>
				<div style="float:left; display:inline-block; padding-left:2px; padding-right:3px;">&lt; 0.01</div>
				<div style="float:left; display:inline-block; height:15px; width:20px; background-color:#00FF00;"></div>
				<div style="float:left; display:inline-block; padding-left:2px; padding-right:3px;">(0.01,0.05)</div>
				<div style="float:left; display:inline-block; height:15px; width:20px; background-color:#FFFFFF;"></div>
				<div style="float:left; display:inline-block; padding-left:2px; padding-right:3px;">&gt; 0.05</div>
			</div>';
		}
		// Two Colors
		else {
			if ($cutoff == 1) {
				$value = 0.01;
				$color = '00FF00';
			}
			else if ($cutoff == 2) {
				$value = 0.05;
				$color = '00FF00';
			}
			else if(is_array($cutoff)){
				$value = $cutoff['value'];
				$color_style = $cutoff['color_style'];
				if ($color_style == 0) {
					$color = '00FF00';
				} else if ($color_style == 1) {
					$color = '6B8E23';
				} else if ($color_style == 2) {
					$color = '0000FF';
				} else {
					$color = 'FF0000';
				}
			}

			$result .= '
			<div class="row mx-0">
				<div style="float:left; display:inline-block; height:15px; width:20px; background-color:#' . $color . ';"></div>
				<div style="float:left; display:inline-block; padding-left:5px; padding-right:10px;">&lt; ' . $value . '</div>
				<div style="float:left; display:inline-block; height:15px; width:20px; background-color:#FFFFFF;"></div>
				<div style="float:left; display:inline-block; padding-left:5px; padding-right:10px;">&gt; ' . $value . '</div>

			</div>';
		}

	}



	return $result;
}




/**
 * Generate Legend for SVG
 */
function getColorLegendSVG($field, $cutoff, $y, $font_size=10) {

	$result = "";

	// Field is logFC
	if ($field == 'Log2FoldChange') {

		// 'logFC' Label
		$result .= "var newText = document.createElementNS(svgNS,'text');";
		$result .= "newText.setAttributeNS(null,'x',0); ";
		$result .= "newText.setAttributeNS(null,'y'," . $y . "); ";
		$result .= "newText.setAttributeNS(null,'font-size','" . $font_size . "px');";
		$result .= "var textNode = document.createTextNode('Log2FC: ');";
		$result .= "newText.appendChild(textNode);";
		$result .= "document.getElementById('info-box-0').appendChild(newText);";

		if (!is_array($cutoff)) {
			$min = (-1) * ($cutoff + 1);
			$mid = 0;
			$max = $cutoff + 1;
			$color_gradient = '0';
		} else {
			$value_array = explode(',', $cutoff['value']);
			$min = floatval(trim($value_array[0]));
			$mid = floatval(trim($value_array[1]));
			$max = floatval(trim($value_array[2]));
			$color_style = $cutoff['color_style'];
			// Set Color
			if ($color_style == 0) {
				$color_gradient = '0';
			}
			if ($color_style == 1) {
				$color_gradient = '1';
			}
			if ($color_style == 2) {
				$color_gradient = '2';
			}
			if ($color_style == 3) {
				$color_gradient = '3';
			}
		}


		$result .= "var myCircle = document.createElementNS(svgNS,'rect');";
		$result .= "myCircle.setAttributeNS(null,'id','mycircle');";
		$result .= "myCircle.setAttributeNS(null,'x',40);";
		$result .= "myCircle.setAttributeNS(null,'y'," . intval($y - 10) . ");";
		$result .= "myCircle.setAttributeNS(null,'width',40);";
		$result .= "myCircle.setAttributeNS(null,'height',13);";
		$result .= "myCircle.setAttributeNS(null,'fill','url(#gradient_" . $color_gradient . ")');";
		$result .= "myCircle.setAttributeNS(null,'stroke','none');";
		$result .= "document.getElementById('info-box-0').appendChild(myCircle);";

		// Gradient Borders
		$result .= "var newText = document.createElementNS(svgNS,'text');";
		$result .= "newText.setAttributeNS(null,'x',90); ";
		$result .= "newText.setAttributeNS(null,'y'," . $y . "); ";
		$result .= "newText.setAttributeNS(null,'font-size','" . $font_size . "px');";
		$result .= "var textNode = document.createTextNode('[" . $min . ", " . $mid . ", " . $max . "]');";
		$result .= "newText.appendChild(textNode);";
		$result .= "document.getElementById('info-box-0').appendChild(newText);";

	}

	// Field is P-Value or FDR
	else {

		if ($field == 'PValue') {
			$field = 'P.Value';
		} else {
			$field = 'FDR';
		}

		// Field
		$result .= "var newText = document.createElementNS(svgNS,'text');";
		$result .= "newText.setAttributeNS(null,'x',0); ";
		$result .= "newText.setAttributeNS(null,'y'," . $y . "); ";
		$result .= "newText.setAttributeNS(null,'font-size','" . $font_size . "px');";
		$result .= "var textNode = document.createTextNode('" . $field . ": ');";
		$result .= "newText.appendChild(textNode);";
		$result .= "document.getElementById('info-box-0').appendChild(newText);";


		// Three Colors
		if ($cutoff == 0) {
			$value = 0.01;
			$color = '6B8E23';
		}
		// Two Colors
		else if ($cutoff == 1) {
			$value = 0.01;
			$color = '00FF00';
		} else if ($cutoff == 2) {
			$value = 0.05;
			$color = '00FF00';
		} else {
			$value = $cutoff['value'];
			$color_style = $cutoff['color_style'];
			if ($color_style == 0) {
				$color = '00FF00';
			} else if ($color_style == 1) {
				$color = '6B8E23';
			} else if ($color_style == 2) {
				$color = '0000FF';
			} else {
				$color = 'FF0000';
			}
		}

		// Color 1
		$result .= "var myCircle = document.createElementNS(svgNS,'rect');";
		$result .= "myCircle.setAttributeNS(null,'id','mycircle');";
		$result .= "myCircle.setAttributeNS(null,'x',40);";
		$result .= "myCircle.setAttributeNS(null,'y'," . intval($y - 10) . ");";
		$result .= "myCircle.setAttributeNS(null,'width',15);";
		$result .= "myCircle.setAttributeNS(null,'height',15);";
		$result .= "myCircle.setAttributeNS(null,'fill','#" . $color . "');";

		$result .= "myCircle.setAttributeNS(null,'stroke','none');";
		$result .= "document.getElementById('info-box-0').appendChild(myCircle);";
		// Label 1
		$result .= "var newText = document.createElementNS(svgNS,'text');";
		$result .= "newText.setAttributeNS(null,'x',58); ";
		$result .= "newText.setAttributeNS(null,'y'," . $y . "); ";
		$result .= "newText.setAttributeNS(null,'font-size','" . $font_size . "px');";
		$result .= "var textNode = document.createTextNode('<" . $value . "');";
		$result .= "newText.appendChild(textNode);";
		$result .= "document.getElementById('info-box-0').appendChild(newText);";

		if ($cutoff == 0) {
			$color2 = '00FF00';
			$label2 = '0.01-0.05';
		} else {
			$color2 = 'FFFFFF';
			$label2 = '>=' . $value;
		}

		// Color 2
		$result .= "var myCircle = document.createElementNS(svgNS,'rect');";
		$result .= "myCircle.setAttributeNS(null,'id','mycircle');";
		$result .= "myCircle.setAttributeNS(null,'x',85);";
		$result .= "myCircle.setAttributeNS(null,'y'," . intval($y - 10) . ");";
		$result .= "myCircle.setAttributeNS(null,'width',15);";
		$result .= "myCircle.setAttributeNS(null,'height',15);";
		$result .= "myCircle.setAttributeNS(null,'fill','#" . $color2 . "');";
		$result .= "myCircle.setAttributeNS(null,'stroke','black');";
		$result .= "document.getElementById('info-box-0').appendChild(myCircle);";
		// Label 2
		$result .= "var newText = document.createElementNS(svgNS,'text');";
		$result .= "newText.setAttributeNS(null,'x',103); ";
		$result .= "newText.setAttributeNS(null,'y'," . $y . "); ";
		$result .= "newText.setAttributeNS(null,'font-size','" . $font_size . "px');";
		$result .= "var textNode = document.createTextNode('" . $label2 . "');";
		$result .= "newText.appendChild(textNode);";
		$result .= "document.getElementById('info-box-0').appendChild(newText);";


		if ($cutoff == 0) {
			// Color 3
			$result .= "var myCircle = document.createElementNS(svgNS,'rect');";
			$result .= "myCircle.setAttributeNS(null,'id','mycircle');";
			$result .= "myCircle.setAttributeNS(null,'x',150);";
			$result .= "myCircle.setAttributeNS(null,'y'," . intval($y - 10) . ");";
			$result .= "myCircle.setAttributeNS(null,'width',15);";
			$result .= "myCircle.setAttributeNS(null,'height',15);";
			$result .= "myCircle.setAttributeNS(null,'fill','#FFFFFF');";
			$result .= "myCircle.setAttributeNS(null,'stroke','black');";
			$result .= "document.getElementById('info-box-0').appendChild(myCircle);";
			// Label 2
			$result .= "var newText = document.createElementNS(svgNS,'text');";
			$result .= "newText.setAttributeNS(null,'x',168); ";
			$result .= "newText.setAttributeNS(null,'y'," . $y . "); ";
			$result .= "newText.setAttributeNS(null,'font-size','" . $font_size . "px');";
			$result .= "var textNode = document.createTextNode('>0.05');";
			$result .= "newText.appendChild(textNode);";
			$result .= "document.getElementById('info-box-0').appendChild(newText);";
		}


	}



	return $result;
}





/**
 * Map Value to Color (P-Value & FDR)
 */
function mapToGradientPValue($value, $cutoff) {
	// Not exists
	if ($value == '.' || trim($value) == '' || floatval($value) < 0) {
		return 'CCCCCC';
	}

	// If Not Customized Cutoff
	if (!is_array($cutoff)) {

		if ($cutoff == 0) {
			if (floatval($value) < 0.01) {
				return '6B8E23';
			} else if (floatval($value) <= 0.05) {
				return '00FF00';
			} else {
				return 'FFFFFF';
			}
		}

		if ($cutoff == 1) {
			if (floatval($value) <= 0.01) {
				return '00FF00';
			} else {
				return 'FFFFFF';
			}
		}

		if ($cutoff == 2) {
			if (floatval($value) <= 0.05) {
				return '00FF00';
			} else {
				return 'FFFFFF';
			}
		}
	}

	// If Customized Cutoff
	else {
		$cutoff_value = floatval($cutoff['value']);
		$color_style = $cutoff['color_style'];
		if ($color_style == 0) {
			$color = '00FF00';
		}
		if ($color_style == 1) {
			$color = '267347';
		}
		if ($color_style == 2) {
			$color = '0000FF';
		}
		if ($color_style == 3) {
			$color = 'FF0000';
		}

		if (floatval($value) <= $cutoff_value) {
			return $color;
		} else {
			return 'FFFFFF';
		}
	}
}



if(! function_exists('get_stat_scale_color2')){
    function get_stat_scale_color2($value, $type='Log2FoldChange') {
      if ($type == 'Log2FoldChange') {
        if ($value >= 1) {
          return '#FF0000';
        } else if ($value > 0) {
          return '#FF8989';
        } else if ($value == 0) {
          return '#E5E5E5';
        } else if ($value > -1) {
          return '#7070FB';
        } else {
          return '#0000FF';
        }
      }
      else if ($type == 'AdjustedPValue') {
        if ($value > 0.05) {
          return '#9CA4B3';
        } else if ($value <= 0.01) {
          return '#015402';
        } else {

          return '#5AC72C';
        }
      }
      else if ($type == 'PValue') {
        if ($value >= 0.01) {
          return '#9CA4B3';
        } else {
          return '#5AC72C';
        }
      }
      return '';
    }
}


if(!function_exists('find_similar_pathways')){
	function find_similar_pathways($pathway, $type = ''){
		global $BXAF_CONFIG;

		$pathway = strtolower(trim($pathway));
		if ($pathway == '') return false;

		$results = $final_results = array();

        $pathways = $BXAF_CONFIG['PATHWAY_LIST'];
		foreach($pathways as $key => $value) {
            if ($type == 'reactome' && ! preg_match("/\(Reactome\)$/i", $value)){
    			continue;
    		}

			if (strtoupper(preg_replace("/[^a-zA-Z0-9]+/", "", $pathway)) ==  strtoupper(preg_replace("/[^a-zA-Z0-9]+/", "", $value))){
				return array($key => $value);
			}
            else if( strpos(strtoupper($value), strtoupper($pathway) ) !== false) {
				$results[$value] = 99;
			}
            else {
				$sim = similar_text(strtoupper($pathway), strtoupper($value), $percent);
				$results[$value] = $percent;
			}
		}


		if (sizeof($results) > 0){
            arsort($results);
			$results = array_slice($results, 0, 20);
			$flipped = array_flip($pathways);
			foreach($results as $value => $percent){
				$final_results[$flipped[$value]] = $value;
			}
		}

		return $final_results;
	}
}


// This function improves Derrick's function tabix_search_records_with_index_all() by filtering results
if(! function_exists('tabix_search_bxgenomics')){
    function tabix_search_bxgenomics($primaryIndex, $secondaryIndex, $table){
    	$outputFormat = 'GetArrayAssoc';

    	$results = array();
    	if($table == 'ComparisonData'){
    		$tabix_results  = tabix_search_records_with_index_all($primaryIndex, $secondaryIndex, $table, $outputFormat);

    		foreach($tabix_results as $i=>$row){
    		    if(is_numeric($row['ComparisonIndex']) && is_numeric($row['GeneIndex']) && (is_numeric($row['Log2FoldChange']) || is_numeric($row['PValue']) || is_numeric($row['AdjustedPValue'])) ){
    		        $results[] = array(
    		            'ComparisonIndex'=>$row['ComparisonIndex'],
    		            'GeneIndex'=>$row['GeneIndex'],
    		            'Log2FoldChange'=>is_numeric($row['Log2FoldChange']) ? $row['Log2FoldChange'] : "",
    		            'PValue'=>is_numeric($row['PValue']) ? $row['PValue'] : "",
    		            'AdjustedPValue'=>is_numeric($row['AdjustedPValue']) ? $row['AdjustedPValue'] : ""
    		        );
    		    }
    		}
    	}
    	else if($table == 'GeneLevelExpression' || $table == 'GeneFPKM'){
    		$tabix_results  = tabix_search_records_with_index_all($primaryIndex, $secondaryIndex, $table , $outputFormat);

    		foreach($tabix_results as $i=>$row){
    		    if(is_numeric($row['SampleIndex']) && is_numeric($row['GeneIndex']) && (is_numeric($row['Value']) || is_numeric($row['FPKM'])) ){
    		        $results[] = array(
    		            'SampleIndex'=>$row['SampleIndex'],
    		            'GeneIndex'=>$row['GeneIndex'],
    		            'Value'=>is_numeric($row['Value']) ? $row['Value'] : $row['FPKM']
    		        );
    		    }
    		}
    	}
    	return $results;
    }
}

?>