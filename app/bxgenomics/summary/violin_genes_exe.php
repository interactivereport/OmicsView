<?php

include_once("config.php");



if (isset($_GET['action']) && $_GET['action'] == 'generate_plot') {

    $gene_indexnames = category_text_to_idnames($_POST['Gene_List'], 'name', 'gene');

    if (! is_array($gene_indexnames) || count($gene_indexnames) <= 0) {
        echo '<h4 class="text-danger">Error:</h4> No genes found. Please enter some gene names.';
        exit();
    }
    $gene_indexes = array_keys($gene_indexnames);


    $sample_indexnames = category_text_to_idnames($_POST['Sample_List'], 'name', 'sample');

    if (! is_array($sample_indexnames) || count($sample_indexnames) <= 0) {
        echo '<h4 class="text-danger">Error:</h4> No samples found. Please enter some sample names.';
        exit();
    }
    $sample_indexes = array_keys($sample_indexnames);


	$tabix_results = tabix_search_bxgenomics( $gene_indexes, $sample_indexes, 'GeneFPKM' ); // GeneFPKM, GeneLevelExpression, ComparisonData

	if (! is_array($tabix_results) || count($tabix_results) <= 0) {
        echo '<h4 class="text-danger">Error:</h4> No NGS data retrieved.';
        exit();
    }

	foreach($tabix_results as $i=>$row){
		$tabix_results[$i]['Value'] = log($row['Value'] + 0.5, 2);
	}

	$sql = "SELECT * FROM ?n WHERE `SampleIndex` IN (?a)";
	$sample_info1 = $BXAF_MODULE_CONN -> get_assoc('SampleIndex', $sql, $BXAF_CONFIG['TBL_BXGENOMICS_SAMPLES'], $sample_indexes);

	$sql = "SELECT * FROM ?n WHERE `SampleIndex` IN (?a)";
	$sample_info2 = $BXAF_MODULE_CONN -> get_assoc('SampleIndex', $sql, 'App_User_Data_Samples', $sample_indexes);

	$sample_info = $sample_info1 + $sample_info2;

	$x = array();

	$category = $_POST['category'];

	foreach($tabix_results as $i=>$row){
		$gene_index = $row['GeneIndex'];
		$sample_index = $row['SampleIndex'];
		$value = $row['Value'];

		if(! array_key_exists($sample_index, $sample_info)) continue;

		$v = $gene_indexnames[$gene_index] == '' ? 'Unknown' : $gene_indexnames[$gene_index];
		$x['Gene Symbol'][] = "'" . addslashes($v) . "'";

		$v = $sample_info[$sample_index][$category] == '' ? 'Unknown' : $sample_info[$sample_index][$category];
		$x[$category][] = "'" . addslashes($v) . "'";

		foreach($_POST['attributes_Sample'] as $attribute){
			$v = $sample_info[$sample_index][$attribute] == '' ? 'Unknown' : $sample_info[$sample_index][$attribute];
			$x[$attribute][] = "'" . addslashes($v) . "'";
		}

		$y["smps"][] = "'" . addslashes($sample_info[$sample_index]['SampleID']) . "'";
		$y["data"][] = $value;
	}


    $output_contents = '';

    $output_contents .= '<div class="my-3 w-100"></div>' . "\n\n";

        $output_contents .= "<canvas class='plot_container my-3' id='plotSection' width='1000' height='900' xresponsive='false' aspectRatio='1:1'></canvas>" . "\n\n";
        $output_contents .= '<script type="text/javascript">' . "\n\n";

            $output_contents .= '$(document).ready(function() {' . "\n\n";

                $output_contents .= 'var plotObj = new CanvasXpress("plotSection", ' . "\n";

                    // data
                    $output_contents .= '{';

                        $output_contents .= '"x": {';

                            $x_contents = array();
                            foreach ($x as $k=>$vals) {
                                $x_contents[] = "'$k': [" . implode(",", $vals) . "]";
                            }
                            $output_contents .= implode(",\n", $x_contents);

                        $output_contents .= '},';
                        $output_contents .= "\n\n";

                        $output_contents .= '"y": {';

                            $output_contents .= "'vars': ['log2(FPKM+0.5)'],";
                            $output_contents .= '"smps":[' . implode(",", $y['smps']) . '],';
                            $output_contents .= '"data":[ [' . implode(",", $y['data']) . '] ]';

                        $output_contents .= '}';

                    $output_contents .= '},';
                    $output_contents .= "\n\n";


					$segregateSamplesBy = $_POST['segregateSamplesBy'] != '' ? ('"segregateSamplesBy"        : ["' . $_POST['segregateSamplesBy'] . '"],') : '';

                    $output_contents .= '{';


                        $output_contents .= $segregateSamplesBy . '

                            "graphOrientation"          : "vertical",
                            "graphType"                 : "Boxplot",
                            "jitter"                    : "Boxplot",

                            "showViolinBoxplot"         : true,


                            "legendBox"                 : true,
                            "showLegend"                : true,

                            "plotByVariable"            : true,
                            "showBoxplotOriginalData"   : true,
                            "smpLabelRotate"            : 0,

                            "showShadow"                : false,

                            "title"                     : "log2(FPKM+0.5)",

                            "axisTitleScaleFontFactor"  : 0.5,
                            "axisTickFontSize"          : 12,
                            "axisTickScaleFontFactor"   : 0.5,

                            "citation"                  : "",
                            "citationScaleFontFactor"   : 0.7,

                            "xAxisTitle"                : "",
                            "titleFontSize"             : 25,

                            "smpLabelScaleFontFactor"   : 0.7,
                            "varLabelScaleFontFactor"   : 0.7,
                            "titleScaleFontFactor"      : 0.7,
                            "subtitleScaleFontFactor"   : 0.7,

                            "legendScaleFontFactor"     : 0.6,
                            "nodeScaleFontFactor"       : 0.7,
                            "sampleSeparationFactor"    : 0.7,
                            "variableSeparationFactor"  : 0.7,
                            "widthFactor"               : 0.7,
                            "printType"                 : "window",
                            "sizes"                     : [ "4.0", "4.5", "5.0", "5.5", "6.0", "6.5", "7.0", "7.5", "8.0", "8.5", "9.0", "9.5", "10.0", "10.5", "11.0", "11.5" ]
                        ';
                    $output_contents .= '}';
                    $output_contents .= "\n\n";


                    $output_contents .= "\n\n";

                $output_contents .= ');';
                $output_contents .= "\n\n";


                $output_contents .= 'plotObj.groupSamples(["' . $_POST['category'] . '"]);';
				$output_contents .= "\n\n";

            $output_contents .= '});';
        $output_contents .= '</script>';


    echo $output_contents;

    exit();
}

?>