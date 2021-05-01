<?php
include_once(__DIR__ . "/config.php");


if (!function_exists('array_iunique')) {
	function array_iunique($array) {
		return array_intersect_key(
			$array,
			array_unique(array_map("StrToLower",$array))
		);
	}
}



if (!function_exists('vennForTwo')) {
	function vennForTwo($array1, $array2, $label1, $label2, $size){

		if(strlen($label1)>3){$label01 = substr($label1, 0, 3).'.';} else {$label01 = $label1;}
		if(strlen($label2)>3){$label02 = substr($label2, 0, 3).'.';} else {$label02 = $label2;}

		echo '
		  <style>
			.venntooltip {
			  position: absolute;
			  text-align: center;
			  width: 128px;
			  height: 22px;
			  background: #333;
			  color: #ddd;
			  padding: 2px;
			  border: 0px;
			  border-radius: 8px;
			  opacity: 0;
			}
			</style>
		  <script>
		  	var sets = [';

		echo '
			 {"sets": [0], "label": "'.$label1.'", "size": '.count($array1).'},
			 {"sets": [1], "label": "'.$label2.'", "size": '.count($array2).'},
			 {"sets": [0, 1], "size": '.count(array_intersect($array1, $array2)).'}];';


		echo '
			var chart = venn.VennDiagram()
							 .width('.$size.')
							 .height('.$size.');

			var div = d3.select("#venn'.md5($label1).'_'.md5($label2).'")
			div.datum(sets).call(chart);

			var tooltip = d3.select("body").append("div")
				.attr("class", "venntooltip");

			div.selectAll("path")
				.style("stroke-opacity", 0)
				.style("stroke", "#fff")
				.style("stroke-width", 0)

			div.selectAll("g")
				.on("mouseover", function(d, i) {
					// sort all the areas relative to the current item
					venn.sortAreas(div, d);

					// Display a tooltip with the current size
					tooltip.transition().duration(400).style("opacity", .9);
					if(i == 0){
						tooltip.text("'.$label01.': " + d.size);
					} else if (i == 1){
						tooltip.text("'.$label02.': " + d.size);
					} else if (i == 2){
						tooltip.text("'.$label01.'&'.$label02.': " + d.size);
					}

					// highlight the current path
					var selection = d3.select(this).transition("tooltip").duration(400);
					selection.select("path")
						.style("stroke-width", 3)
						.style("fill-opacity", d.sets.length == 1 ? .4 : .1)
						.style("stroke-opacity", 1);
				})

				.on("mousemove", function() {
					tooltip.style("left", (d3.event.pageX) + "px")
						   .style("top", (d3.event.pageY - 28) + "px");
				})

				.on("mouseout", function(d, i) {
					tooltip.transition().duration(400).style("opacity", 0);
					var selection = d3.select(this).transition("tooltip").duration(400);
					selection.select("path")
						.style("stroke-width", 0)
						.style("fill-opacity", d.sets.length == 1 ? .25 : .0)
						.style("stroke-opacity", 0);
				});
			</script>

				<div class="col-md-9" id="venn'.md5($label1).'_'.md5($label2).'"></div>

		 ';

		 return TRUE;
	}
}



if (!function_exists('vennForThree')) {
	function vennForThree($array1, $array2, $array3, $label1, $label2, $label3, $size){

		if(strlen($label1)>3){$label01 = substr($label1, 0, 3).'.';} else {$label01 = $label1;}
		if(strlen($label2)>3){$label02 = substr($label2, 0, 3).'.';} else {$label02 = $label2;}
		if(strlen($label3)>3){$label03 = substr($label3, 0, 3).'.';} else {$label03 = $label3;}

		echo '
		  <style>
			.venntooltip {
			  position: absolute;
			  text-align: center;
			  width: 128px;
			  height: 22px;
			  background: #333;
			  color: #ddd;
			  padding: 2px;
			  border: 0px;
			  border-radius: 8px;
			  opacity: 0;
			}
			</style>
		  <script>
		  	var sets = [';

		echo '
			 {"sets": [0], "label": "'.$label1.'", "size": '.count($array1).'},
			 {"sets": [1], "label": "'.$label2.'", "size": '.count($array2).'},
			 {"sets": [2], "label": "'.$label3.'", "size": '.count($array3).'},
			 {"sets": [0, 1], "size": '.count(array_intersect($array1, $array2)).'},
			 {"sets": [0, 2], "size": '.count(array_intersect($array1, $array3)).'},
			 {"sets": [1, 2], "size": '.count(array_intersect($array2, $array3)).'},
			 {"sets": [0, 1, 2], "size": '.count(array_intersect($array1, array_intersect($array2, $array3))).'}];';


		echo '
			var chart = venn.VennDiagram()
							 .width('.$size.')
							 .height('.$size.');

			var div = d3.select("#venn'.md5($label1).'_'.md5($label2).'_'.md5($label3).'")
			div.datum(sets).call(chart);

			var tooltip = d3.select("body").append("div")
				.attr("class", "venntooltip");

			div.selectAll("path")
				.style("stroke-opacity", 0)
				.style("stroke", "#fff")
				.style("stroke-width", 0)

			div.selectAll("g")
				.on("mouseover", function(d, i) {
					// sort all the areas relative to the current item
					venn.sortAreas(div, d);

					// Display a tooltip with the current size
					tooltip.transition().duration(400).style("opacity", .9);
					if(i == 0){
						tooltip.text("'.$label01.': " + d.size);
					} else if (i == 1){
						tooltip.text("'.$label02.': " + d.size);
					} else if (i == 2){
						tooltip.text("'.$label03.': " + d.size);
					} else if (i == 3){
						tooltip.text("'.$label01.'&'.$label02.': " + d.size);
					} else if (i == 4){
						tooltip.text("'.$label01.'&'.$label03.': " + d.size);
					} else if (i == 5){
						tooltip.text("'.$label02.'&'.$label03.': " + d.size);
					} else if (i == 6){
						tooltip.text("'.$label01.'&'.$label02.'&'.$label03.': " + d.size);
					}

					// highlight the current path
					var selection = d3.select(this).transition("tooltip").duration(400);
					selection.select("path")
						.style("stroke-width", 3)
						.style("fill-opacity", d.sets.length == 1 ? .4 : .1)
						.style("stroke-opacity", 1);
				})

				.on("mousemove", function() {
					tooltip.style("left", (d3.event.pageX) + "px")
						   .style("top", (d3.event.pageY - 28) + "px");
				})

				.on("mouseout", function(d, i) {
					tooltip.transition().duration(400).style("opacity", 0);
					var selection = d3.select(this).transition("tooltip").duration(400);
					selection.select("path")
						.style("stroke-width", 0)
						.style("fill-opacity", d.sets.length == 1 ? .25 : .0)
						.style("stroke-opacity", 0);
				});
			</script>

				<div class="col-md-9" id="venn'.md5($label1).'_'.md5($label2).'_'.md5($label3).'"></div>

		 ';

		 return TRUE;
	}
}






if(isset($_GET['action']) && $_GET['action'] == "search") {

	$db_table = 'tbl_comparison_go_enrichment_10_6';
	if($_POST['P_Value_Cutoff'] == '-10') $db_table = 'tbl_comparison_go_enrichment_10_10';
	else if($_POST['P_Value_Cutoff'] == '-2') $db_table = 'tbl_comparison_go_enrichment_10_2';

	$sql = "SELECT * FROM `$db_table` WHERE `GO_Tree` = ?s AND `Direction` = ?s AND `Comparison_Name` = ?s";
	$target_info = $BXAF_MODULE_CONN->get_row($sql, $_POST['GO_Tree'], $_POST['Direction'], $_POST['comparison1']);

	if(! is_array($target_info) || count($target_info) <= 0) {
		echo "No record found.";
		exit();
	}
	$selected_terms = explode(",", $target_info['Terms']);

	$sql = "SELECT `Comparison_Name`, `Terms` FROM `$db_table` WHERE `GO_Tree` = ?s AND `Direction` = ?s";
	$found_terms = $BXAF_MODULE_CONN->get_assoc('Comparison_Name', $sql, $_POST['GO_Tree'], $_POST['Direction']);

	$found_counts = array();
	$overlap_counts = array();
	$overlap_details = array();
	foreach($found_terms as $comparison=>$terms){
		if($comparison == $target_info['Comparison_Name']) continue;

		$terms = explode(",", $terms);
		$found_counts[$comparison] = count($terms);

		$overlap = array_intersect($selected_terms, $terms);
		if(count($overlap) > 0){
			$overlap_details[$comparison] = implode(", ", $overlap);
			$overlap_counts[$comparison] = count($overlap);
		}
	}
	arsort($overlap_counts);


	$table_contents = "<div class='my-5 lead text-center'>Target Comparison: <span class='text-danger'>" . $target_info['Comparison_Name'] . "</span>, <span class='text-danger'>" . $_POST['Direction'] . "</span> Regulated, GO Tree: <span class='text-danger'>" . $_POST['GO_Tree'] . "</span>, Enriched Terms: <span class='text-danger'>" . count($selected_terms) . "</span></div>";

	$table_contents .= "<div class='w-100'>";

	$table_contents .= "<table id='table_search_results' class='table table-bordered table-hover'>";
	$table_contents .= "<thead><tr class='table-success'><th>Found Comparisons</th><th># Terms in Comparison</th><th># Overlaps</th><th>Overlapped Terms</th></tr></thead><tbody>";

	foreach($overlap_counts as $comparison=>$count){
		$table_contents .= "<tr>";

			$table_contents .= "<td>" . $comparison . " <a title='Show Venn Diagrams' href='comparison_compare.php?comparison1=" . $_POST['comparison1'] . "&comparison2=" . $comparison . "&GO_Tree=" . $_POST['GO_Tree'] . "&Direction=" . $_POST['Direction'] . "' target='_blank'><i class='fas fa-adjust'></i></a></td>";
			$table_contents .= "<td>" . $found_counts[$comparison] . "</td>";

			$table_contents .= "<td>" . $count . "</td>";

			if($_POST['Show_Terms'] == 1) $table_contents .= "<td>" . $overlap_details[$comparison] . "</td>";
			else $table_contents .= "<td></td>";
		$table_contents .= "</tr>";

	}
	$table_contents .= "</tbody></table>";
	$table_contents .= "</div>";

	echo $table_contents;

	exit();
}






if(isset($_GET['action']) && $_GET['action'] == "show_venn_diagram") {

	$db_table = 'tbl_go_gene_list';

	$comparisons = array();
	if($_POST['comparison1'] != '') $comparisons[] = $_POST['comparison1'];
	if($_POST['comparison2'] != '') $comparisons[] = $_POST['comparison2'];
	if($_POST['comparison3'] != '') $comparisons[] = $_POST['comparison3'];

	$go_tree = $_POST['GO_Tree'];
	$regulation = $_POST['Direction'];


	$BXAF_CONFIG['BXAF_VENN_DATA_DIR'] = $BXAF_CONFIG['BXGENOMICS_CACHE_DIR'];
	$BXAF_CONFIG['BXAF_VENN_DATA_URL'] = $BXAF_CONFIG['BXGENOMICS_CACHE_URL'];

	$size = 500;


	$name_result = array();
	$value_result = array();
	$comparison_names = array('A', 'B', 'C');
	foreach($comparison_names as $i=>$name){
		if(trim($_POST['comparison_name' . ($i+1)]) != '') $comparison_names[$i] = trim($_POST['comparison_name' . ($i+1)]);
	}

	$sql = "SELECT * FROM `$db_table` WHERE `Species` = '{$BXAF_CONFIG['SPECIES']}' AND `ID` IN (?a)";
	$gene_list_info = $BXAF_MODULE_CONN->get_all($sql, $comparisons);

	$comparisons_flip = array_flip($comparisons);
	foreach($gene_list_info as $i=>$row){
		$j = $comparisons_flip[ $row['ID'] ];
		$name_result[$i] = $comparison_names[$j];
		$value_result[ $i ] = explode(', ', $row['Gene_Names']);
	}

	$name_keys = array(
		'<strong>' . $comparison_names[0] . ':</strong> ' . $comparisons[0] . ' (<strong>' . count($value_result[0]) . '</strong>)',
		'<strong>' . $comparison_names[1] . ':</strong> ' . $comparisons[1] . " (<strong>" . count($value_result[1]) . '</strong>)',
		'<strong>' . $comparison_names[2] . ':</strong> ' . $comparisons[2] . " (<strong>" . count($value_result[2]) . '</strong>)'
	);

	$_SESSION['Venn_value_result'] = $value_result;
	$_SESSION['Venn_name_result'] = $name_result;


	// Top Result Above Tabs
	$label = array();
	for($i = 0; $i < count($name_result); $i++){
		if(strlen($name_result[$i])>3){$label[$i] = substr($name_result[$i], 0, 3).'.';} else {$label[$i] = $name_result[$i];}
	}

	echo '
		<div class="row m-3" id="top_summary">
			<h3 class="mt-3 text-success">Overlap Summary</h3>
			<div class="w-100 p-3">' . implode('<BR>', $name_keys) . '</div>

			<div class="w-100 my-1"><a href="'.$BXAF_CONFIG['BXAF_VENN_DATA_URL'].'result.csv" class="btn btn-warning btn-sm" style="margin-bottom:10px;" download>Download Overlap Results</a></div>

			<div class="col-md-3" style="max-height:'.$size.'px; overflow-y:auto; border:1px solid #ABD9AB; padding:10px;">
				<table class="table table-bordered table-hover">
					<thead>
						<tr class="success">
							<th>Set Name</th>
							<th>Count Number</th>
						</tr>
					</thead>
					<tbody>
					';



					// TOP 01: Individual Group
					foreach($value_result as $key=>$value){
					echo '
						<tr>
							<td>'.$name_result[$key].'</td>
							<td><a href="javascript:void(0);" class="content_detail" type="0" method="'.$key.'" other="" case="individual" title="'.$name_result[$key].'">'.count($value_result[$key]).'</td>
						</tr>';

					}



					// TOP 02: Double Groups
					for($i=0; $i<count($value_result)-1; $i++){
						for($j=$i+1; $j<count($value_result); $j++){
							if(is_array($value_result[$i]) && is_array($value_result[$j]) && count($value_result[$i])>0 && count($value_result[$j])>0){
								echo '
									<tr>
										<td>'.$name_result[$i].' &amp; '.$name_result[$j].'</td>
										<td><a href="javascript:void(0);" class="content_detail" type="0" method="'.$i.'_'.$j.'" other="" case="double" title="'.$name_result[$i].' & '.$name_result[$j].'">'.count(array_intersect($value_result[$i], $value_result[$j])).'</a></td>
									</tr>';
							}
						}
					}



					// TOP 03: Triple Groups
					for($i=0; $i<count($value_result)-1; $i++){
						for($j=$i+1; $j<count($value_result); $j++){
							for($k=$j+1; $k<count($value_result); $k++){
								if(is_array($value_result[$i]) && is_array($value_result[$j]) && is_array($value_result[$k]) && count($value_result[$i])>0 && count($value_result[$j])>0 && count($value_result[$k])>0){
									echo '
										<tr>
											<td>'.$name_result[$i].', '.$name_result[$j].' &amp; '.$name_result[$k].'</td>
											<td><a href="javascript:void(0);" class="content_detail" type="0" method="'.$i.'_'.$j.'_'.$k.'" other="" case="triple" title="'.$name_result[$i].' & '.$name_result[$j].' & '.$name_result[$k].'">'.count(array_intersect($value_result[$k], array_intersect($value_result[$i], $value_result[$j]))).'</td>
										</tr>';
								}
							}
						}
					}



					// TOP 03: Occur In Total Groups
					if(count($value_result)>3){
						$array_total_intersect = $value_result[0];
						$array_total_intersect_name = $name_result[0];
						for($i=1; $i<count($value_result); $i++){
							$array_total_intersect = array_intersect($array_total_intersect, $value_result[$i]);
							$array_total_intersect_name = $array_total_intersect_name.' & '.$name_result[$i];
						}
						echo '<tr><td>'.$array_total_intersect_name.'</td><td><a href="javascript:void(0);" class="content_detail" type="0" method="total" other="" case="total" title="Intersection of All Groups">'.count($array_total_intersect).'</a></td></tr>';

					}



					// TOP 04: Unique Group
					foreach($value_result as $key=>$value){
						$array_other_groups = array();
						for($i=0; $i<count($value_result); $i++){
							if($i != $key){$array_other_groups = array_merge($array_other_groups, $value_result[$i]); }
						}

						echo '
							<tr>
								<td>'.$name_result[$key].' only</td>
								<td><a href="javascript:void(0);" class="content_detail" type="0" method="'.$key.'_only" other="" case="unique" title="'.$name_result[$key].' Only">'.count(array_diff($value_result[$key], $array_other_groups)).'</td>
							</tr>';
					}



					// TOP 05: Union Groups

					$array_other_groups = array();
					for($i=0; $i<count($value_result); $i++){
						$array_other_groups = array_unique(array_merge($array_other_groups, $value_result[$i]));
					}

					echo '
						<tr>
							<td>Combined</td>
							<td><a href="javascript:void(0);" class="content_detail" type="0" method="union" other="" case="union" title="Union of All Groups">'.count($array_other_groups).'</td>
						</tr>';


					echo '
					</tbody>
				</table>
			</div>

			<div class="col-md-8">';

	echo '
		  <style>
			.venntooltip {
			  position: absolute;
			  text-align: center;
			  width: 128px;
			  height: 22px;
			  background: #333;
			  color: #ddd;
			  padding: 2px;
			  border: 0px;
			  border-radius: 8px;
			  opacity: 0;
			}
			.btn_content_detail{
				color: #74B2DB !important;
			}
			.btn_content_detail:hover{
				color: #007ACB !important;
			}
			.ui-widget{
				font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			}
			.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active .ui-state-hover{
				color:black;
				border:none;
			}
		  </style>


		  <script type="text/javascript">
	          $(document).ready(function(){

		     var sets = [ ';


		for($i=0; $i<count($value_result); $i++){
			echo '{"sets": ['.$i.'], "label": "'.$name_result[$i].'", "size": '.count($value_result[$i]).'},';
		}
		for($i=0; $i<count($value_result); $i++){
			for($j=$i+1; $j<count($value_result); $j++){
				if(is_array($value_result[$i]) && is_array($value_result[$j]) && count($value_result[$i])>0 && count($value_result[$j])>0){
					echo '{"sets": ['.$i.', '.$j.'], "size": '.count(array_intersect($value_result[$i], $value_result[$j])).'},';
				}
			}
		}
		for($i=0; $i<count($value_result)-2; $i++){
			for($j=$i+1; $j<count($value_result)-1; $j++){
				for($k=$j+1; $k<count($value_result); $k++){
					if(is_array($value_result[$i]) && is_array($value_result[$j]) && is_array($value_result[$k]) && count($value_result[$i])>0 && count($value_result[$j])>0 && count($value_result[$k])>0){
						echo '{"sets": ['.$i.', '.$j.', '.$k.'], "size": '.count(array_intersect($value_result[$i], $value_result[$j], $value_result[$k])).'},';
					}
				}
			}
		}

		echo '
         ];';



		echo '
			var chart = venn.VennDiagram()
							 .width('.$size.')
							 .height('.$size.');

			var div = d3.select("#venn")
			div.datum(sets).call(chart);

			var tooltip = d3.select("body").append("div")
				.attr("class", "venntooltip");

			div.selectAll("path")
				.style("stroke-opacity", 0)
				.style("stroke", "#fff")
				.style("stroke-width", 0)

			div.selectAll("g")
				.on("mouseover", function(d, i) {
					// sort all the areas relative to the current item
					venn.sortAreas(div, d);

					// Display a tooltip with the current size
					tooltip.transition().duration(400).style("opacity", .9);';

					if(count($value_result)==2){
						echo '
							if(i == 0){
								tooltip.text("'.$label[0].': " + d.size);
							} else if (i == 1){
								tooltip.text("'.$label[1].': " + d.size);
							} else if (i == 2){
								tooltip.text("'.$label[0].'&'.$label[1].': " + d.size);
							}';
					} else if (count($value_result)==3) {
						echo '
							if(i == 0){
								tooltip.text("'.$label[0].': " + d.size);
							} else if (i == 1){
								tooltip.text("'.$label[1].': " + d.size);
							} else if (i == 2){
								tooltip.text("'.$label[2].': " + d.size);
							} else if (i == 3){
								tooltip.text("'.$label[0].'&'.$label[1].': " + d.size);
							} else if (i == 4){
								tooltip.text("'.$label[0].'&'.$label[2].': " + d.size);
							} else if (i == 5){
								tooltip.text("'.$label[1].'&'.$label[2].': " + d.size);
							} else if (i == 6){
								tooltip.text("'.$label[0].'&'.$label[1].'&'.$label[2].': " + d.size);
							}';
					} else if (count($value_result)==4) {
						echo '
							if(i == 0){
								tooltip.text("'.$label[0].': " + d.size);
							} else if (i == 1){
								tooltip.text("'.$label[1].': " + d.size);
							} else if (i == 2){
								tooltip.text("'.$label[2].': " + d.size);
							} else if (i == 3){
								tooltip.text("'.$label[3].': " + d.size);
							} else if (i == 4){
								tooltip.text("'.$label[0].'&'.$label[1].': " + d.size);
							} else if (i == 5){
								tooltip.text("'.$label[0].'&'.$label[2].': " + d.size);
							} else if (i == 6){
								tooltip.text("'.$label[0].'&'.$label[3].': " + d.size);
							} else if (i == 7){
								tooltip.text("'.$label[1].'&'.$label[2].': " + d.size);
							} else if (i == 8){
								tooltip.text("'.$label[1].'&'.$label[3].': " + d.size);
							} else if (i == 9){
								tooltip.text("'.$label[2].'&'.$label[3].': " + d.size);
							} else if (i == 10){
								tooltip.text("'.$label[0].'&'.$label[1].'&'.$label[2].': " + d.size);
							} else if (i == 11){
								tooltip.text("'.$label[0].'&'.$label[1].'&'.$label[3].': " + d.size);
							} else if (i == 12){
								tooltip.text("'.$label[0].'&'.$label[2].'&'.$label[3].': " + d.size);
							} else if (i == 13){
								tooltip.text("'.$label[1].'&'.$label[2].'&'.$label[3].': " + d.size);
							}';
					} else { echo 'tooltip.text(d.size);';}

			echo '
					// highlight the current path
					var selection = d3.select(this).transition("tooltip").duration(400);
					selection.select("path")
						.style("stroke-width", 3)
						.style("fill-opacity", d.sets.length == 1 ? .4 : .1)
						.style("stroke-opacity", 1);
				})

				.on("mousemove", function() {
					tooltip.style("left", (d3.event.pageX) + "px")
						   .style("top", (d3.event.pageY - 28) + "px");
				})

				.on("mouseout", function(d, i) {
					tooltip.transition().duration(400).style("opacity", 0);
					var selection = d3.select(this).transition("tooltip").duration(400);
					selection.select("path")
						.style("stroke-width", 0)
						.style("fill-opacity", d.sets.length == 1 ? .25 : .0)
						.style("stroke-opacity", 0);
				});

	        });
	    </script>' . "\n\n";

        echo '	<div id="venn"></div>';



	echo   '</div>

		</div>';

	echo '
	<div class="card" style="border:0.125rem solid #0275D8;">
	<div class="card-header bg-primary">
		<h4 style="margin-bottom:0px;">
		Results
		<span class="pull-right" style="font-size:16px;"><a href="'. $BXAF_CONFIG['BXAF_VENN_DATA_URL'] . 'result.csv" style="color:#FFFF00;" download><i class="fas fa-hand-point-right fa-lg"></i> Download Results File</span>
		</h4>
	</div>
	<div class="card-block">
		<div role="tabpanel" class="bd-example bd-example-tabs">
			<ul role="tablist" class="nav nav-tabs" id="myTab">';

			for($i=0; $i<count($value_result)-1; $i++){
				for($j=$i+1; $j<count($value_result); $j++){
					if(is_array($value_result[$i]) && is_array($value_result[$j]) && count($value_result[$i])>0 && count($value_result[$j])>0){
			   			echo '<li class="nav-item"><a data-toggle="tab" role="tab" class="nav-link" href="#div_'.$i.'_'.$j.'">'.$name_result[$i].' &amp; '.$name_result[$j].'</a></li>';
					}
				}
			}
			for($i=0; $i<count($value_result)-2; $i++){
				for($j=$i+1; $j<count($value_result)-1; $j++){
					for($k=$j+1; $k<count($value_result); $k++){
						if(is_array($value_result[$i]) && is_array($value_result[$j]) && is_array($value_result[$k]) && count($value_result[$i])>0 && count($value_result[$j])>0 && count($value_result[$k])>0){
							echo '<li class="nav-item"><a data-toggle="tab" role="tab" class="nav-link" href="#div_'.$i.'_'.$j.'_'.$k.'">'.$name_result[$i].' &amp; '.$name_result[$j].' &amp; '.$name_result[$k].'</a></li>';
						}
					}
				}
			}
			echo '
			</ul>

			<div class="tab-content" id="myTabContent">
		 ';



	// Venn for two datasets

	for($i=0; $i<count($value_result)-1; $i++){
		for($j=$i+1; $j<count($value_result); $j++){
			if(is_array($value_result[$i]) && is_array($value_result[$j]) && count($value_result[$i])>0 && count($value_result[$j])>0){

				echo '<div class="tab-pane fade';
				if($i == 0 and $j == 1){echo ' active in';}
				echo '" role="tabpanel" id="div_'.$i.'_'.$j.'">
					<div class="card" style="margin-top:20px;margin-bottom:20px;">
					<div class="card-header">
						<h4><strong>'.$name_result[$i].'</strong> &amp; <strong>'.$name_result[$j].'</strong></h4>
					</div>
					<div class="card-block">
					 ';
				echo '
					<div class="row no_margin">
					<div class="col-md-3">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>DATA</th>
									<th>COUNT</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>'.$name_result[$i].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="2" method="'.$i.'" other="'.$i.'_'.$j.'" case="individual" title="'.$name_result[$i].'">'.count($value_result[$i]).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$j].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="2" method="'.$j.'" other="'.$i.'_'.$j.'" case="individual" title="'.$name_result[$j].'">'.count($value_result[$j]).'</a></td>
								</tr>
								<tr>
									<td>Combined</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="2" method="union" other="'.$i.'_'.$j.'" case="union" title="Union of All Groups">'.intval(count($value_result[$i])+count($value_result[$j])-count(array_intersect($value_result[$i], $value_result[$j]))).'</a></td>
								</tr>
								<tr>
									<td>Overlap</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="2" method="'.$i.'_'.$j.'" other="'.$i.'_'.$j.'" case="double" title="Intersection of All Groups">'.intval(count(array_intersect($value_result[$i], $value_result[$j]))).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$i].' Only</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="2" method="'.$i.'_only" other="'.$i.'_'.$j.'" case="unique" title="'.$name_result[$i].' Only">'.intval(count($value_result[$i]) - count(array_intersect($value_result[$i], $value_result[$j]))).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$j].' Only</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="2" method="'.$j.'_only" other="'.$i.'_'.$j.'" case="unique" title="'.$name_result[$j].' Only">'.intval(count($value_result[$j]) - count(array_intersect($value_result[$i], $value_result[$j]))).'</a></td>
								</tr>
							</tbody>
						</table>
					</div>
					';
				vennForTwo($value_result[$i], $value_result[$j], $name_result[$i], $name_result[$j], $size);
				echo '</div></div></div></div>';
			}
		}
	}






	// Venn for three datasets

	for($i=0; $i<count($value_result)-2; $i++){
		for($j=$i+1; $j<count($value_result)-1; $j++){
			for($k=$j+1; $k<count($value_result); $k++){
				if(is_array($value_result[$i]) && is_array($value_result[$j]) && is_array($value_result[$k]) && count($value_result[$i])>0 && count($value_result[$j])>0 && count($value_result[$k])>0){
					//echo '<div class="row">';
					echo '<div class="tab-pane fade" role="tabpanel" id="div_'.$i.'_'.$j.'_'.$k.'">
						<div class="card" style="margin-top:20px;margin-bottom:20px;">
						<div class="card-header">
							<h4><strong>'.$name_result[$i].', '.$name_result[$j].'</strong> &amp; <strong>'.$name_result[$k].'</strong>
							</h4>
						</div>
						<div class="card-block">
						 ';

					echo '
					<div class="row">
					<div class="col-md-3">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>DATA</th>
									<th>COUNT</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>'.$name_result[$i].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$i.'" other="'.$i.'_'.$j.'_'.$k.'" case="individual" title="'.$name_result[$i].'">'.count($value_result[$i]).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$j].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$j.'" other="'.$i.'_'.$j.'_'.$k.'" case="individual" title="'.$name_result[$j].'">'.count($value_result[$j]).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$k].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$k.'" other="'.$i.'_'.$j.'_'.$k.'" case="individual" title="'.$name_result[$k].'">'.count($value_result[$k]).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$i].' &amp; '.$name_result[$j].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$i.'_'.$j.'" other="'.$i.'_'.$j.'_'.$k.'" case="double" title="'.$name_result[$i].' & '.$name_result[$j].'">'.count(array_intersect($value_result[$i], $value_result[$j])).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$i].' &amp; '.$name_result[$k].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$i.'_'.$k.'" other="'.$i.'_'.$j.'_'.$k.'" case="double" title="'.$name_result[$i].' & '.$name_result[$k].'">'.count(array_intersect($value_result[$i], $value_result[$k])).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$j].' &amp; '.$name_result[$k].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$j.'_'.$k.'" other="'.$i.'_'.$j.'_'.$k.'" case="double" title="'.$name_result[$j].' & '.$name_result[$k].'">'.count(array_intersect($value_result[$j], $value_result[$k])).'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$i].' &amp; '.$name_result[$j].' &amp; '.$name_result[$k].'</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$i.'_'.$j.'_'.$k.'" other="'.$i.'_'.$j.'_'.$k.'" case="triple" title="'.$name_result[$i].' & '.$name_result[$j].' & '.$name_result[$k].'">' .
										count( array_intersect($value_result[$i], array_intersect($value_result[$j], $value_result[$k])) ) .
										'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$i].' Only</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$i.'_only" other="'.$i.'_'.$j.'_'.$k.'" case="unique" title="'.$name_result[$i].' Only">' .
										intval( count($value_result[$i])-count(array_intersect($value_result[$i], $value_result[$j]))-count(array_intersect($value_result[$i], $value_result[$k])) + count(array_intersect($value_result[$i], array_intersect($value_result[$j], $value_result[$k]))) ) .
									'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$j].' Only</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$j.'_only" other="'.$i.'_'.$j.'_'.$k.'" case="unique" title="'.$name_result[$j].' Only">'.
										intval( count($value_result[$j])-count(array_intersect($value_result[$i], $value_result[$j]))-count(array_intersect($value_result[$j], $value_result[$k])) + count(array_intersect($value_result[$i], array_intersect($value_result[$j], $value_result[$k]))) ).
										'</a></td>
								</tr>
								<tr>
									<td>'.$name_result[$k].' Only</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="'.$k.'_only" other="'.$i.'_'.$j.'_'.$k.'" case="unique" title="'.$name_result[$k].' Only">'.
									intval( count($value_result[$k])-count(array_intersect($value_result[$i], $value_result[$k]))-count(array_intersect($value_result[$j], $value_result[$k])) + count(array_intersect($value_result[$i], array_intersect($value_result[$j], $value_result[$k]))) ).
									'</a></td>
								</tr>
								<tr>
									<td>Combined</td>
									<td><a href="javascript:void(0);" class="content_detail btn_content_detail" type="3" method="union" other="'.$i.'_'.$j.'_'.$k.'" case="union" title="Union of All Groups">'.
										intval( count($value_result[$i]) + count($value_result[$j]) + count($value_result[$k])-count(array_intersect($value_result[$i], $value_result[$k])) - count(array_intersect($value_result[$j], $value_result[$k])) - count(array_intersect($value_result[$i], $value_result[$j])) +
										count(array_intersect($value_result[$i], array_intersect($value_result[$j], $value_result[$k]))) ) .
									'</a></td>
								</tr>
							</tbody>
						</table>
					</div>';

					vennForThree($value_result[$i], $value_result[$j], $value_result[$k], $name_result[$i], $name_result[$j], $name_result[$k], $size);

					echo '</div></div></div></div>';

				}
			}
		}
	}

	echo '</div></div></div></div>';






	// Output Result

	// Save data used later
	$intersect_number = array();
	foreach ($value_result as $key=>$value){$intersect_number[$key] = count($value_result[$key]);}
	for($i=0; $i<count($value_result)-1; $i++){
		for($j=$i+1; $j<count($value_result); $j++){
			$intersect_number[$i.'_'.$j] = count(array_intersect($value_result[$i], $value_result[$j]));
		}
	}
	for($i=0; $i<count($value_result)-2; $i++){
		for($j=$i+1; $j<count($value_result)-1; $j++){
			for($k=$j+1; $k<count($value_result); $k++){
				$intersect_number[$i.'_'.$j.'_'.$k] = count(array_intersect($value_result[$i], $value_result[$j], $value_result[$k]));
			}
		}
	}


	$intersect_values = array();
	foreach ($value_result as $key=>$value){$intersect_values[$key] = $value_result[$key];}
	for($i=0; $i<count($value_result)-1; $i++){
		for($j=$i+1; $j<count($value_result); $j++){
			$intersect_values[$i.'_'.$j] = array_intersect($value_result[$i], $value_result[$j]);
		}
	}
	for($i=0; $i<count($value_result)-2; $i++){
		for($j=$i+1; $j<count($value_result)-1; $j++){
			for($k=$j+1; $k<count($value_result); $k++){
				$intersect_values[$i.'_'.$j.'_'.$k] = array_intersect($value_result[$i], $value_result[$j], $value_result[$k]);
			}
		}
	}


	$file = fopen($BXAF_CONFIG['BXAF_VENN_DATA_DIR'] . "result.csv", "w");
		$output_name_array = array();
		$length_array = array();
		foreach ($value_result as $key=>$value){
			$output_name_array[] = $name_result[$key];
			$length_array[] = count($value);
		}
		for($i=0; $i<count($value_result)-1; $i++){
			for($j=$i+1; $j<count($value_result); $j++){
				if(is_array($value_result[$i]) && is_array($value_result[$j]) && count($value_result[$i])>0 && count($value_result[$j])>0){
					$output_name_array[] = $name_result[$i].'&'.$name_result[$j];
				}
			}
		}
		for($i=0; $i<count($value_result)-2; $i++){
			for($j=$i+1; $j<count($value_result)-1; $j++){
				for($k=$j+1; $k<count($value_result); $k++){
					if(is_array($value_result[$i]) && is_array($value_result[$j]) && is_array($value_result[$k]) && count($value_result[$i])>0 && count($value_result[$j])>0 && count($value_result[$k])>0){
						$output_name_array[] = $name_result[$i].'&'.$name_result[$j].'&'.$name_result[$k];
					}
				}
			}
		}
		if(count($name_result)>3){
			$output_name_array[] = implode('&', $name_result);
		}
		fputcsv($file,$output_name_array);

		$max_length = max($length_array);

		$len = 2;
		for($len=0; $len<$max_length; $len++){
			$temp = array();

			foreach ($value_result as $key=>$value){
				if($intersect_number[$key]>$len){
					$temp[] = trim($intersect_values[$key][$len]);
				} else {
					$temp[] = '';
				}
			}


			for($i=0; $i<count($value_result)-1; $i++){
				for($j=$i+1; $j<count($value_result); $j++){
					if(is_array($value_result[$i]) && is_array($value_result[$j]) && $intersect_number[$i]>0 && $intersect_number[$j]>0){
						if($intersect_number[$i.'_'.$j] > $len){
							$temp_array_compact = array();
							foreach($intersect_values[$i.'_'.$j] as $id=>$info){
								$temp_array_compact[] = $info;
							}
							$temp[] = trim($temp_array_compact[$len]);
						} else { $temp[] = ''; }
					}
				}
			}

			for($i=0; $i<count($value_result)-2; $i++){
				for($j=$i+1; $j<count($value_result)-1; $j++){
					for($k=$j+1; $k<count($value_result); $k++){
						if(is_array($value_result[$i]) && is_array($value_result[$j]) && is_array($value_result[$k]) && $intersect_number[$i]>0 && $intersect_number[$j]>0 && $intersect_number[$k]>0){
							if($intersect_number[$i.'_'.$j.'_'.$k] > $len){
								$temp_array_compact = array();
								foreach($intersect_values[$i.'_'.$j.'_'.$k] as $id=>$info){
									$temp_array_compact[] = $info;
								}
								$temp[] = trim($temp_array_compact[$len]);
							} else { $temp[] = ''; }
						}
					}
				}
			}

			if(count($name_result)>3){
				$array_total_intersect_values = array_values($array_total_intersect);
				if(count($array_total_intersect_values) > $len){
					$temp[] = trim($array_total_intersect_values[$len]);
				} else {
					$temp[] = '';
				}
			}

			fputcsv($file,$temp);
		}

	fclose($file);



	exit();
}






if(isset($_GET['action']) && $_GET['action']=="get_content_detail"){
	$result = array();


	/*----------------------------------------------------------------------------------------*/
	// Type 01: Individual
	/*----------------------------------------------------------------------------------------*/
	if ($_GET['case'] == 'individual'){
		$i = $_GET['method'];
		$result['name'] = $_SESSION['Venn_name_result'][$i];
		$result['value'] = $_SESSION['Venn_value_result'][$i];
	}


	/*----------------------------------------------------------------------------------------*/
	// Type 02: Double
	/*----------------------------------------------------------------------------------------*/
	if ($_GET['case'] == 'double'){
		$i = $_GET['method'][0];
		$j = $_GET['method'][2];
		$result['name'] = $_SESSION['Venn_name_result'][$i].' & '.$_SESSION['Venn_name_result'][$j];
		$result['value'] = array_unique( array_intersect($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$j]));
	}



	/*----------------------------------------------------------------------------------------*/
	// Type 03: Triple
	/*----------------------------------------------------------------------------------------*/

	if ($_GET['case'] == 'triple'){
		$i = $_GET['method'][0];
		$j = $_GET['method'][2];
		$k = $_GET['method'][4];
		$result['name'] = $_SESSION['Venn_name_result'][$i].' & '.$_SESSION['Venn_name_result'][$j].' & '.$_SESSION['Venn_name_result'][$k];
		$result['value'] = array_unique( array_intersect($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$j], $_SESSION['Venn_value_result'][$k]));
	}



	/*----------------------------------------------------------------------------------------*/
	// Type 04: Union
	/*----------------------------------------------------------------------------------------*/
	if ($_GET['case'] == 'union'){
		if ($_GET['type']==0){
			$result['name'] = 'Contents for union of all groups';
			$result_value_temp = array();
			for($index = 0; $index < count($_SESSION['Venn_value_result']); $index++){
				$result_value_temp = array_merge($result_value_temp, $_SESSION['Venn_value_result'][$index]);
			}
			$result['value'] = array_unique($result_value_temp);
		}

		if ($_GET['type']==2){
			$i = $_GET['other'][0];
			$j = $_GET['other'][2];
			$result['name'] = 'Contents for union of groups';
			$result['value'] = array_unique( array_merge($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$j]));
		}

		if ($_GET['type']==3){
			$i = $_GET['other'][0];
			$j = $_GET['other'][2];
			$k = $_GET['other'][4];
			$result['name'] = 'Contents for union of groups';
			$result['value'] = array_unique( array_merge($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$j], $_SESSION['Venn_value_result'][$k]));
		}
	}



	/*----------------------------------------------------------------------------------------*/
	// Type 05: Total
	/*----------------------------------------------------------------------------------------*/
	if ($_GET['case'] == 'total'){
		$result['name'] = 'Intersection for union of all groups';
		$result_value_temp = array_intersect($_SESSION['Venn_value_result'][0], $_SESSION['Venn_value_result'][1]);
		for($index = 2; $index < count($_SESSION['Venn_value_result']); $index++){
			$result_value_temp = array_intersect($result_value_temp, $_SESSION['Venn_value_result'][$index]);
		}
		$result['value'] = $result_value_temp;
	}



	/*----------------------------------------------------------------------------------------*/
	// Type 06: Unique
	/*----------------------------------------------------------------------------------------*/
	if ($_GET['case'] == 'unique'){

		if($_GET['type'] == 0){
			$i = $_GET['method'][0];
			$all_other = array();
			for ($index = 0; $index < count($_SESSION['Venn_value_result']); $index++){
				if($index != $i){$all_other = array_merge($all_other, $_SESSION['Venn_value_result'][$index]); }
			}
			$result['name'] = 'Contents in '.$_SESSION['Venn_name_result'][$i].' only';
			$result['value'] = array_unique(array_diff($_SESSION['Venn_value_result'][$i], $all_other));
		}

		if($_GET['type'] == 2){
			$i = $_GET['method'][0];
			$m = $_GET['other'][0];
			$n = $_GET['other'][2];
			$result['name'] = 'Contents in '.$_SESSION['Venn_name_result'][$i].' only';
			if($i == $m){
				$result['value'] = array_unique(array_diff($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$n]));
			} else if($i == $n){
				$result['value'] = array_unique(array_diff($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$m]));
			}
		}

		if($_GET['type'] == 3){
			$i = $_GET['method'][0];
			$m = $_GET['other'][0];
			$n = $_GET['other'][2];
			$p = $_GET['other'][4];
			$result['name'] = 'Contents in '.$_SESSION['Venn_name_result'][$i].' only';
			if($i == $m){
				$result['value'] = array_unique(array_diff($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$n], $_SESSION['Venn_value_result'][$p]));
			} else if($i == $n){
				$result['value'] = array_unique(array_diff($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$m], $_SESSION['Venn_value_result'][$p]));
			} else if($i == $p){
				$result['value'] = array_unique(array_diff($_SESSION['Venn_value_result'][$i], $_SESSION['Venn_value_result'][$m], $_SESSION['Venn_value_result'][$n]));
			}
		}
	}


	unset($_SESSION['Venn_detail_result']);
	$_SESSION['Venn_detail_result'] = $result['value'];

	echo '
		<div>
			<span class="lead">Display Method:</span>
			<span>
				<input type="radio" class="content_detail_radio mx-2" name="content_detail" id="content_detail0" value="0" checked>
				One ID per line
				<input type="radio" class="content_detail_radio mx-2" name="content_detail" id="content_detail1" value="1">
				Separated by comma
			</span>
		</div>

		<hr>
		<div class="row m-0">
			<textarea id="content_detail0_div" class="p-1" style="height:300px; width:100%;">';

			foreach ($result['value'] as $value){echo $value. "\n";}
	echo	'
			</textarea>
			<textarea class="hidden" id="content_detail1_div" style="height:300px; width:100%;">'. implode(', ', $result['value']) . '</textarea>
		</div>
		';

	exit();
}



if(isset($_GET['action']) && $_GET['action']=="save_gene_list"){

	exit();
}

?>