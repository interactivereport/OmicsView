<?php
include_once(__DIR__ . "/config.php");


$BXAF_CONFIG['BXAF_VENN_DATA_DIR'] = $BXAF_CONFIG['BXGENOMICS_CACHE_DIR'];
$BXAF_CONFIG['BXAF_VENN_DATA_URL'] = $BXAF_CONFIG['BXGENOMICS_CACHE_URL'];


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




if(isset($_GET['action']) && $_GET['action']=="upload"){

	if ($_FILES["file-0"]["error"] == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["file-0"]["tmp_name"];
		$name = $_FILES["file-0"]["name"];
		$file_type = $_FILES["file-0"]["type"];
		$file_size = $_FILES["file-0"]["size"];
		move_uploaded_file($tmp_name, $BXAF_CONFIG['BXAF_VENN_DATA_DIR'].'data.csv');
	}


	$file = fopen($BXAF_CONFIG['BXAF_VENN_DATA_DIR'].'data.csv',"r") or die("Unable to open file!");
	$csv_array = array();

	while(! feof($file)) {
		$csv_array[] = fgetcsv($file);
	}
	fclose($file);

	// Set the names of the datasets
	$_POST['name'] = array();
	$value_from_csv = array();
	for($i=1; $i<count($csv_array[0])+1; $i++){
		if($_GET['header'] == 2){
			$_POST['name'][] = 'Set'.$i;
		}
		else{
			foreach($csv_array[0] as $key=>$value){$_POST['name'][$key] = $value;}
		}
		$value_from_csv[$i-1] = array();
	}

	//$value_from_csv = array();
	if($_GET['header'] == 2){$start_index=0;} else {$start_index=1;}
	for($i=$start_index; $i<count($csv_array); $i++){
		foreach ($csv_array[$i] as $key=>$value){
			if(trim($value)!=''){
				$value_from_csv[$key][] = $value;
			}
		}
	}


	echo '<input class="hidden" id="nrows_uploaded" value="';
		for($i=0; $i<count($value_from_csv); $i++){
			echo ','.$i;
		}
	echo '">';

	for($i=0; $i<count($value_from_csv);$i++){
		echo '
			<div class="dataset_div medium_blue">
				<div class="form-inline">
					<div class="form-group">
						<input type="text" class="form-control block_left" placeholder="Name of Dataset" name="name[]" value="'.$_POST['name'][$i].'" required>
					</div>
					<div class="checkbox">
						<label><input class="check_box_dataset" type="checkbox" id="check_select'.$i.'" name="check_select'.$i.'" recordid="'.$i.'" type="checkbox" checked></label>
					</div>
					<div class="pull-right">
						<a href="javascript:void(0);" title="Delete" class="delete_dataset"> <i class="close">&times;</i></a>
					</div>
				</div>
				<textarea placeholder="List of Values" name="value[]" id="textarea">';
		foreach($value_from_csv[$i] as $id=>$info){
			echo	$info."\n";
		}
		echo    '</textarea>
			</div>
			 ';
	}



	exit();
}




if(isset($_GET['action']) && $_GET['action']=="draw_pic"){

	if($_POST['sets']=='2' && $_POST['image_type']==2){
				echo 'static';
				$A = $_POST['A'];
				$B = $_POST['B'];
				$AB = $_POST['AB'];

				if($A>=$B){$n1 = sqrt($A/pi());$n2 = sqrt($B/pi());$n4 = $AB;$label1 = $_POST['labelA'];$label2 = $_POST['labelB'];}
				else if($A<$B){$n1 = sqrt($B/pi());$n2 = sqrt($A/pi());$n4 = $AB;$label1 = $_POST['labelB'];$label2 = $_POST['labelA'];}


				//////////////////////////////////////////////
				// Define Functions //////////////////////////
				//////////////////////////////////////////////
				if(!function_exists('area_check_center')) {
					function area_check_center($x, $y){ // $x, $y are radius of two circles here
						$part1 = pow($x, 2) * ( asin($y/$x) - (1/2) * sin(2 * asin( $y/$x )));// Area of the segment of the larger circle
						$part2 = (1/2) * pi() * pow($y, 2);// Area of the smaller semi-circle
						return $part1 + $part2;
					}
				}

				// For the case where the two centers are on the same side of the common chord
				// The last three parameters are radius of two circles and the overlap area
				if(!function_exists('equation_1')) {
					function equation_1($x, $y, $z, $yz){
						return pow($y, 2)*($x - (1/2)*sin(2*$x)) + pow($z, 2)*(pi() - asin(($y/$z)*sin($x)) + (1/2)*sin(2 * asin(($y/$z) * sin($x)))) - $yz;
						//return 5 - 3*$x;
					}
				}
				// Solve the equation (Using the method of bisection)
				if(!function_exists('solve_function_1')) {
					function solve_function_1($a, $b, $y, $z, $yz){
						$lower = equation_1($a, $y, $z, $yz);
						$upper = equation_1($b, $y, $z, $yz);
						$middle = equation_1(($a+$b)/2, $y, $z, $yz);

						if (abs($upper - $lower) < 0.000001){
							$_SESSION['result'] = $a;

						} else {
							if ($middle * $lower <= 0){$a=$a; $b=($a+$b)/2; solve_function_1($a, $b, $y, $z, $yz);}
							else {$b=$b; $a=($a+$b)/2; solve_function_1($a, $b, $y, $z, $yz);}
						}
					}
				}
				// For the case where the two centers are on the different sides of the common chord
				// The last three parameters are radius of two circles and the overlap area
				if(!function_exists('equation_2')) {
					function equation_2($x, $y, $z, $yz){
						return pow($y, 2)*($x - (1/2)*sin(2*$x)) + pow($z, 2)*( asin(($y/$z)*sin($x)) - (1/2)*sin(2 * asin(($y/$z) * sin($x)))) - $yz;
					}
				}
				// Solve the equation (Using the method of bisection)
				if(!function_exists('solve_function_2')) {
					function solve_function_2($a, $b, $y, $z, $yz){
						$lower = equation_2($a, $y, $z, $yz);
						$upper = equation_2($b, $y, $z, $yz);
						$middle = equation_2(($a+$b)/2, $y, $z, $yz);
						if (abs($upper - $lower) < 0.000001){
							$_SESSION['result'] = $a;

						} else {
							if ($middle * $lower <= 0){$a=$a; $b=($a+$b)/2; solve_function_2($a, $b, $y, $z, $yz);}
							else {$b=$b; $a=($a+$b)/2; solve_function_2($a, $b, $y, $z, $yz);}
						}
					}
				}


				solve_function_2(0,asin($n2/$n1)-0.01, $n1, $n2, $n4);



				/////////////////////////////////////////////////////////////////////////////////////////////////////
				// Figure out the lengths of lines of centres，inorder to determine positions of the three centers //
				/////////////////////////////////////////////////////////////////////////////////////////////////////

				// Take a look at circle A and circle B first
				// Determine whether the smaller circle is in the larger circle. Then determine whether the two centers are on the different sides of the common chord.
				if ($AB > $B){
					// echo 'Wrong value because AB > B.';
					exit();
				}
				if ($AB == $B){$ABC = $BC; exit();}

				if($AB == area_check_center($n1, $n2)){
					// The center of the smaller circle lies on the common chord
					$o1o2 = sqrt(pow($n1, 2) - pow($n2, 2));
				}

				else if ($AB > area_check_center($n1, $n2)){
					// Two centers are on the same side of the common chord
					solve_function_1(0, asin($n2/$n1)-0.001, $n1, $n2, $n4); //We
					$angle1 = $_SESSION['result'];
					$o1o2 = $n2 * ( sin( asin( ($n1/$n2)*sin($angle1) ) - $angle1 ) ) / sin($angle1);
				}

				else if ($AB < area_check_center($n1, $n2)){
					// Two centers are on the different sides of the common chord
					solve_function_2(0, asin($n2/$n1)-0.001, $n1, $n2, $n4); // The second parameter must be controlled because asin(x) will return "NAN" if x>1.
					$angle1 = $_SESSION['result'];
					$o1o2 = $n2 * ( sin( asin( ($n1/$n2)*sin($angle1) ) - $angle1 ) ) / sin($angle1);
				}



				echo '<br>-----------------------<br>';
				function get_centers_line($radius1, $radius2, $area) {
					if($area == area_check_center($radius1, $radius2)){ $centers_line = sqrt(pow($radius1, 2) - pow($radius2, 2));}
					if($area > area_check_center($radius1, $radius2)){
						// Two centers are on the same side of the common chord
						solve_function_1(0, asin($radius2/$radius1)-0.001, $radius1, $radius2, $area);
						$angle1 = $_SESSION['result'];
						$centers_line = $radius2 * ( sin( asin( ($radius1/$radius2)*sin($angle1) ) - $angle1 ) ) / sin($angle1);

					}
					if($area < area_check_center($radius1, $radius2)){
						// Two centers are on the different sides of the common chord
						solve_function_2(0, asin($radius2/$radius1)-0.001, $radius1, $radius2, $area);
						$angle1 = $_SESSION['result'];
						$centers_line = $radius2 * ( sin( asin( ($radius1/$radius2)*sin($angle1) ) + $angle1 ) ) / sin($angle1);
					}
					$_SESSION['centers_line'] = $centers_line;
					return $centers_line;
				}



				$o1o2 = get_centers_line($n1, $n2, $n4);

				// Positions for centers of three circles
				$o1x = 0; $o1y = 0; $o2x = $o1o2; $o2y = 0;


				// Can be changed.
				$width = $_POST['size'];
				$height = $_POST['size'] + 20*4;

				$coef = min($width/($n1 + $n2 + $o2x + 10), $height/($n1 + 10));

				$o1x_plot = $n1 * $coef + 10;
				$o1y_plot = $n1 * $coef + 10;
				$o2x_plot = ($n1 + $o2x) * $coef + 10;
				$o2y_plot = $n1 * $coef + 10;
				$n1_plot = $n1 * $coef;
				$n2_plot = $n2 * $coef;

				$icon1y_plot = $o1y_plot + $n1 * $coef + 40;
				$icon2y_plot = $icon1y_plot + 20;
				$icon3y_plot = $icon1y_plot + 40;



				// Create a canvas
				$im = imagecreatetruecolor($width, $height);
				$white = imagecolorallocate($im, 255, 255, 255);
				$black = imagecolorallocate($im, 0, 0, 0);
				imagefill($im, 0, 0, $white);
				$red = imagecolorallocatealpha($im, 255, 223, 195, 70);
				$red_dark = imagecolorallocatealpha($im, 235, 113, 12, 0);
				$blue = imagecolorallocatealpha($im, 194, 218, 235, 70);
				$blue_dark = imagecolorallocatealpha($im, 67, 142, 185, 70);
				$green = imagecolorallocatealpha($im, 175, 219, 175, 70);
				$green_dark = imagecolorallocatealpha($im, 0, 102, 0, 0);



				imagefilledellipse($im, $o1x_plot, $o1y_plot, 2*$n1_plot, 2*$n1_plot, $red);
				imagefilledellipse($im, $o2x_plot, $o2y_plot, 2*$n2_plot, 2*$n2_plot, $blue);

				$font = dirname(__FILE__).'/arial.ttf';

				//imagettftext($im, 14, 0, 40, 50, $red, $font, 'fdas');
				imagefilledrectangle($im, 10, $icon1y_plot-16, 26, $icon1y_plot, $red);
				imagettftext($im, 14, 0, 40, $icon1y_plot, $black, $font, $label1.' -> ('.pow($n1,2)*pi().')');


				imagefilledrectangle($im, 10, $icon2y_plot-16, 26, $icon2y_plot, $blue);
				imagettftext($im, 14, 0, 40, $icon2y_plot, $black, $font, $label2.' -> ('.pow($n2,2)*pi().')');

				imagefilledrectangle($im, 10, $icon3y_plot-16, 26, $icon3y_plot, $red);
				imagefilledrectangle($im, 10, $icon3y_plot-16, 26, $icon3y_plot, $blue);
				imagettftext($im, 14, 0, 40, $icon3y_plot, $black, $font, 'intersect('.$label1.', '.$label2.') -> ('.$n4.')');


				// Save the image
				$current_time = time();
				imagepng($im, $BXAF_CONFIG['BXAF_VENN_DATA_DIR'].$current_time.'venn.png');
				imagedestroy($im);
				echo $current_time;

				exit();
	}


	if($_POST['sets']=='3' && $_POST['image_type']==2){
				echo 'static';
				$A = $_POST['A'];
				$B = $_POST['B'];
				$C = $_POST['C'];
				$AB = $_POST['AB'];
				$AC = $_POST['AC'];
				$BC = $_POST['BC'];
				$ABC = $_POST['ABC'];

				if($A>=$B && $B>=$C){$n1 = sqrt($A/pi());$n2 = sqrt($B/pi());$n3 = sqrt($C/pi());$n4 = $AB;$n5 = $AC;$n6 = $BC;$label1 = $_POST['labelA'];$label2 = $_POST['labelB'];$label3 = $_POST['labelC'];}
				else if($A>=$C && $C>=$B){$n1 = sqrt($A/pi());$n2 = sqrt($C/pi());$n3 = sqrt($B/pi());$n4 = $AC;$n5 = $AB;$n6 = $BC;$label1 = $_POST['labelA'];$label2 = $_POST['labelC'];$label3 = $_POST['labelB'];}
				else if($B>=$A && $A>=$C){$n1 = sqrt($B/pi());$n2 = sqrt($A/pi());$n3 = sqrt($C/pi());$n4 = $AB;$n5 = $BC;$n6 = $AC;$label1 = $_POST['labelB'];$label2 = $_POST['labelA'];$label3 = $_POST['labelC'];}
				else if($B>=$C && $C>=$A){$n1 = sqrt($B/pi());$n2 = sqrt($C/pi());$n3 = sqrt($A/pi());$n4 = $BC;$n5 = $AB;$n6 = $AC;$label1 = $_POST['labelB'];$label2 = $_POST['labelC'];$label3 = $_POST['labelA'];}
				else if($C>=$A && $A>=$B){$n1 = sqrt($C/pi());$n2 = sqrt($A/pi());$n3 = sqrt($B/pi());$n4 = $AC;$n5 = $BC;$n6 = $AB;$label1 = $_POST['labelC'];$label2 = $_POST['labelA'];$label3 = $_POST['labelB'];}
				else if($C>=$B && $B>=$A){$n1 = sqrt($C/pi());$n2 = sqrt($B/pi());$n3 = sqrt($A/pi());$n4 = $BC;$n5 = $AC;$n6 = $AB;$label1 = $_POST['labelC'];$label2 = $_POST['labelB'];$label3 = $_POST['labelA'];}


				//////////////////////////////////////////////
				// Define Functions //////////////////////////
				//////////////////////////////////////////////
				if(!function_exists('area_check_center')) {
					function area_check_center($x, $y){ // $x, $y are radius of two circles here
						$part1 = pow($x, 2) * ( asin($y/$x) - (1/2) * sin(2 * asin( $y/$x )));// Area of the segment of the larger circle
						$part2 = (1/2) * pi() * pow($y, 2);// Area of the smaller semi-circle
						return $part1 + $part2;
					}
				}

				// For the case where the two centers are on the same side of the common chord
				// The last three parameters are radius of two circles and the overlap area
				if(!function_exists('equation_1')) {
					function equation_1($x, $y, $z, $yz){
						return pow($y, 2)*($x - (1/2)*sin(2*$x)) + pow($z, 2)*(pi() - asin(($y/$z)*sin($x)) + (1/2)*sin(2 * asin(($y/$z) * sin($x)))) - $yz;
						//return 5 - 3*$x;
					}
				}
				// Solve the equation (Using the method of bisection)
				if(!function_exists('solve_function_1')) {
					function solve_function_1($a, $b, $y, $z, $yz){
						$lower = equation_1($a, $y, $z, $yz);
						$upper = equation_1($b, $y, $z, $yz);
						$middle = equation_1(($a+$b)/2, $y, $z, $yz);

						if (abs($upper - $lower) < 0.000001){
							$_SESSION['result'] = $a;

						} else {
							if ($middle * $lower <= 0){$a=$a; $b=($a+$b)/2; solve_function_1($a, $b, $y, $z, $yz);}
							else {$b=$b; $a=($a+$b)/2; solve_function_1($a, $b, $y, $z, $yz);}
						}
					}
				}
				// For the case where the two centers are on the different sides of the common chord
				// The last three parameters are radius of two circles and the overlap area
				if(!function_exists('equation_2')) {
					function equation_2($x, $y, $z, $yz){
						return pow($y, 2)*($x - (1/2)*sin(2*$x)) + pow($z, 2)*( asin(($y/$z)*sin($x)) - (1/2)*sin(2 * asin(($y/$z) * sin($x)))) - $yz;
					}
				}
				// Solve the equation (Using the method of bisection)
				if(!function_exists('solve_function_2')) {
					function solve_function_2($a, $b, $y, $z, $yz){
						$lower = equation_2($a, $y, $z, $yz);
						$upper = equation_2($b, $y, $z, $yz);
						$middle = equation_2(($a+$b)/2, $y, $z, $yz);
						if (abs($upper - $lower) < 0.000001){
							$_SESSION['result'] = $a;

						} else {
							if ($middle * $lower <= 0){$a=$a; $b=($a+$b)/2; solve_function_2($a, $b, $y, $z, $yz);}
							else {$b=$b; $a=($a+$b)/2; solve_function_2($a, $b, $y, $z, $yz);}
						}
					}
				}



				solve_function_2(0,asin($n2/$n1)-0.01, $n1, $n2, $n4);


				/////////////////////////////////////////////////////////////////////////////////////////////////////
				// Figure out the lengths of lines of centres，inorder to determine positions of the three centers //
				/////////////////////////////////////////////////////////////////////////////////////////////////////

				// Take a look at circle A and circle B first
				// Determine whether the smaller circle is in the larger circle. Then determine whether the two centers are on the different sides of the common chord.
				if ($AB > $B){
					// echo 'Wrong value because AB > B.';
					exit();}
				if ($AB == $B){$ABC = $BC; exit();}

				if($AB == area_check_center($n1, $n2)){
					// The center of the smaller circle lies on the common chord
					$o1o2 = sqrt(pow($n1, 2) - pow($n2, 2));
				}

				else if ($AB > area_check_center($n1, $n2)){
					// Two centers are on the same side of the common chord
					solve_function_1(0, asin($n2/$n1)-0.001, $n1, $n2, $n4); //We
					$angle1 = $_SESSION['result'];
					$o1o2 = $n2 * ( sin( asin( ($n1/$n2)*sin($angle1) ) - $angle1 ) ) / sin($angle1);
				}

				else if ($AB < area_check_center($n1, $n2)){
					// Two centers are on the different sides of the common chord
					solve_function_2(0, asin($n2/$n1)-0.001, $n1, $n2, $n4); // The second parameter must be controlled because asin(x) will return "NAN" if x>1.
					$angle1 = $_SESSION['result'];
					$o1o2 = $n2 * ( sin( asin( ($n1/$n2)*sin($angle1) ) - $angle1 ) ) / sin($angle1);
				}



				function get_centers_line($radius1, $radius2, $area) {
					if($area == area_check_center($radius1, $radius2)){ $centers_line = sqrt(pow($radius1, 2) - pow($radius2, 2));}
					if($area > area_check_center($radius1, $radius2)){
						// Two centers are on the same side of the common chord
						solve_function_1(0, asin($radius2/$radius1)-0.001, $radius1, $radius2, $area);
						$angle1 = $_SESSION['result'];
						$centers_line = $radius2 * ( sin( asin( ($radius1/$radius2)*sin($angle1) ) - $angle1 ) ) / sin($angle1);

					}
					if($area < area_check_center($radius1, $radius2)){
						// Two centers are on the different sides of the common chord
						solve_function_2(0, asin($radius2/$radius1)-0.001, $radius1, $radius2, $area);
						$angle1 = $_SESSION['result'];
						$centers_line = $radius2 * ( sin( asin( ($radius1/$radius2)*sin($angle1) ) + $angle1 ) ) / sin($angle1);
					}
					$_SESSION['centers_line'] = $centers_line;
					return $centers_line;
				}



				$o1o2 = get_centers_line($n1, $n2, $n4);
				$o1o3 = get_centers_line($n1, $n3, $n5);
				$o2o3 = get_centers_line($n2, $n3, $n6);


				// Figure out angle-O2O1O13, then we can get the position for O3
				$angle_o2o1o3 = acos( (pow($o1o2, 2) + pow($o1o3, 2) - pow($o2o3, 2))/(2 * $o1o2 * $o1o3) );

				// Positions for centers of three circles
				$o1x = 0; $o1y = 0; $o2x = $o1o2; $o2y = 0;
				$o3x = $o1o3 * cos($angle_o2o1o3); $o3y = $o1o3 * sin($angle_o2o1o3);


				// For each point on the plane, check whether it is in the common area by checking the radius.
				$index = 0;
				for($i = 0; $i < intval($n1+2*$n2); $i++ ){
					for ($j = 0; $j < intval($n1+2*$n3); $j++){
						if(sqrt(pow($i-$o1x,2)+pow($j-$o1y,2))<$n1 && sqrt(pow($i-$o2x,2)+pow($j-$o2y,2))<$n2 && sqrt(pow($i-$o3x,2)+pow($j-$o3y,2))<$n3){
							$index = $index + 1;
						}
					}
				}


				// Can be changed.
				$width = $_POST['size'];
				$height = $_POST['size'] + 20*7;

				$coef = min($width/($n1 + $n2 + $o2x + 10), $height/($n1 + $n3 + $o3y + 10));

				$o1x_plot = $n1 * $coef + 10;
				$o1y_plot = ($n3 + $o3y) * $coef + 10;
				$o2x_plot = ($n1 + $o2x) * $coef + 10;
				$o2y_plot = ($n3 + $o3y) * $coef + 10;
				$o3x_plot = ($n1 + $o3x) * $coef + 10;
				$o3y_plot = $n3 * $coef + 10;
				$n1_plot = $n1 * $coef;
				$n2_plot = $n2 * $coef;
				$n3_plot = $n3 * $coef;

				$icon1y_plot = $o1y_plot + $n1 * $coef + 40;
				$icon2y_plot = $icon1y_plot + 20;
				$icon3y_plot = $icon1y_plot + 40;
				$icon4y_plot = $icon1y_plot + 60;
				$icon5y_plot = $icon1y_plot + 80;
				$icon6y_plot = $icon1y_plot + 100;
				$icon7y_plot = $icon1y_plot + 120;



				// Create a canvas
				$im = imagecreatetruecolor($width, $height);
				$white = imagecolorallocate($im, 255, 255, 255);
				$black = imagecolorallocate($im, 0, 0, 0);
				imagefill($im, 0, 0, $white);
				$red = imagecolorallocatealpha($im, 255, 223, 195, 70);
				$red_dark = imagecolorallocatealpha($im, 235, 113, 12, 0);
				$blue = imagecolorallocatealpha($im, 194, 218, 235, 70);
				$blue_dark = imagecolorallocatealpha($im, 67, 142, 185, 70);
				$green = imagecolorallocatealpha($im, 175, 219, 175, 70);
				$green_dark = imagecolorallocatealpha($im, 0, 102, 0, 0);



				imagefilledellipse($im, $o1x_plot, $o1y_plot, 2*$n1_plot, 2*$n1_plot, $red);
				imagefilledellipse($im, $o2x_plot, $o2y_plot, 2*$n2_plot, 2*$n2_plot, $blue);
				imagefilledellipse($im, $o3x_plot, $o3y_plot, 2*$n3_plot, 2*$n3_plot, $green);

				$font = dirname(__FILE__).'/arial.ttf';

				imagefilledrectangle($im, 10, $icon1y_plot-16, 26, $icon1y_plot, $red);
				imagettftext($im, 14, 0, 40, $icon1y_plot, $black, $font, $label1.' -> ('.pow($n1,2)*pi().')');


				imagefilledrectangle($im, 10, $icon2y_plot-16, 26, $icon2y_plot, $blue);
				imagettftext($im, 14, 0, 40, $icon2y_plot, $black, $font, $label2.' -> ('.pow($n2,2)*pi().')');

				imagefilledrectangle($im, 10, $icon3y_plot-16, 26, $icon3y_plot, $green);
				imagettftext($im, 14, 0, 40, $icon3y_plot, $black, $font, $label3.' -> ('.pow($n3,2)*pi().')');

				imagefilledrectangle($im, 10, $icon4y_plot-16, 26, $icon4y_plot, $red);
				imagefilledrectangle($im, 10, $icon4y_plot-16, 26, $icon4y_plot, $blue);
				imagettftext($im, 14, 0, 40, $icon4y_plot, $black, $font, 'intersect('.$label1.', '.$label2.') -> ('.$n4.')');

				imagefilledrectangle($im, 10, $icon5y_plot-16, 26, $icon5y_plot, $red);
				imagefilledrectangle($im, 10, $icon5y_plot-16, 26, $icon5y_plot, $green);
				imagettftext($im, 14, 0, 40, $icon5y_plot, $black, $font, 'intersect('.$label1.', '.$label3.') -> ('.$n5.')');

				imagefilledrectangle($im, 10, $icon6y_plot-16, 26, $icon6y_plot, $blue);
				imagefilledrectangle($im, 10, $icon6y_plot-16, 26, $icon6y_plot, $green);
				imagettftext($im, 14, 0, 40, $icon6y_plot, $black, $font, 'intersect('.$label2.', '.$label3.') -> ('.$n6.')');

				imagefilledrectangle($im, 10, $icon7y_plot-16, 26, $icon7y_plot, $blue);
				imagefilledrectangle($im, 10, $icon7y_plot-16, 26, $icon7y_plot, $green);
				imagefilledrectangle($im, 10, $icon7y_plot-16, 26, $icon7y_plot, $red);
				imagettftext($im, 14, 0, 40, $icon7y_plot, $black, $font, 'intersect('.$label1.', '.$label2.', '.$label3.') -> ('.$ABC.')');


				// Save the image
				$current_time = time();
				imagepng($im, $BXAF_CONFIG['BXAF_VENN_DATA_DIR'] . $current_time.'venn.png');
				imagedestroy($im);
				echo $current_time;

				exit();
	}


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

	<script type="text/javascript">
        $(document).ready(function(){
		  	var sets = [';

	if($_POST['sets']==3){
		echo '
			 {"sets": [0], "label": "'.$_POST['labelA'].'", "size": '.intval($_POST['A']).'},
			 {"sets": [1], "label": "'.$_POST['labelB'].'", "size": '.intval($_POST['B']).'},
			 {"sets": [2], "label": "'.$_POST['labelC'].'", "size": '.intval($_POST['C']).'},
			 {"sets": [0, 1], "size": '.intval($_POST['AB']).'},
			 {"sets": [0, 2], "size": '.intval($_POST['AC']).'},
			 {"sets": [1, 2], "size": '.intval($_POST['BC']).'},
			 {"sets": [0, 1, 2], "size": '.intval($_POST['ABC']).'}];';
	}
	else if($_POST['sets']==2) {
		echo '
			 {"sets": [0], "label": "'.$_POST['labelA'].'", "size": '.intval($_POST['A']).'},
			 {"sets": [1], "label": "'.$_POST['labelB'].'", "size": '.intval($_POST['B']).'},
			 {"sets": [0, 1], "size": '.intval($_POST['AB']).'}];';
	}

	echo    '
			var chart = venn.VennDiagram()
							 .width('.$_POST['size'].')
							 .height('.$_POST['size'].');

			var div = d3.select("#venn")
			div.datum(sets).call(chart);

			var tooltip = d3.select("#venn").append("div")
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
					tooltip.text(d.size);

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
	exit();
}




if(isset($_GET['action']) && $_GET['action']=="overlap"){

	if (count($_POST['value']) == 0 || trim($_POST['value'][0]) == ''){
		echo 'empty'; exit();
	}

	$_SESSION['Venn_value_result'] = array();
	$_SESSION['Venn_name_result'] = array();


	$count_name = count($_POST['name']);
	$count_name_unique = count(array_unique($_POST['name']));
	if($count_name_unique < $count_name){echo 'samename'; exit();}

	$dataset_selected = explode(',', $_POST['dataset_selected']);
	$name_result = array();




	// Get selected textareas

	for($i=0; $i<count($_POST['name']); $i++){
		$number_operated = 0;
		foreach($dataset_selected as $key=>$value){
			if(trim($value)!=''){
				if($value == $i){$number_operated += 1;}
				if(intval($value) != 0){
					if($value == -$i){$number_operated = 0;}
				} else {
					if($i==0 && strlen($value)==2){$number_operated = 0;}
				}
			}
		}
		if($number_operated%2 == 1 && trim($_POST['value'][$i])!=''){
			$name_result[] = $_POST['name'][$i];
			$pre_value_result[] = $_POST['value'][$i];
		}
	}





	// Get Result Array To Use

	$value_result = array();
	foreach ($pre_value_result as $key=>$value){
		$value_temp = array();
		if(isset($_POST['remove_duplicated'])){
			$array_before_remove_n = explode("\n", $value);
			$array_after_remove_n = array();
			foreach($array_before_remove_n as $k=>$v){
				$array_after_remove_n[] = trim($v);
			}

			foreach (array_unique($array_after_remove_n) as $id=>$info){
				if(trim($info) != ''){$value_temp[]=trim($info); }
			}
		} else {
			foreach (explode("\n", $value) as $id=>$info){
				if(trim($info) != ''){$value_temp[]=trim($info); }
			}
		}
		$value_result[] = $value_temp;
	}






	// If Not Case-sensitive

	if(!isset($_POST['case_sensitive'])){
		$value_sensitive = $value_result;
		$value_result_before_reorder = array();
		$value_result = array();
		foreach($value_sensitive as $k=>$v){
			$value_result_before_reorder[$k] = array_iunique($v);
			foreach($value_result_before_reorder[$k] as $id=>$info){

				$value_result[$k][] = strtolower($info);
			}
		}
	}

	$_SESSION['Venn_value_result'] = $value_result;
	$_SESSION['Venn_name_result'] = $name_result;






	// Top Result Above Tabs

	$label = array();
	for($i = 0; $i < count($name_result); $i++){
		if(strlen($name_result[$i])>3){$label[$i] = substr($name_result[$i], 0, 3).'.';} else {$label[$i] = $name_result[$i];}
	}

	echo '
		<div class="row m-3" id="top_summary">

			<h2 class="w-100 my-1">Result: </h2>

			<div class="w-100 my-1">Note: The graphic for 4 or more data sets only shows overlap with first two datasets. The table is accurate.</div>

			<div class="w-100 my-1"><a href="'.$BXAF_CONFIG['BXAF_VENN_DATA_URL'].'result.csv" class="btn btn-warning btn-sm" style="margin-bottom:10px;" download>Download Overlap Results</a></div>

			<div class="col-md-3" style="max-height:'.$_POST['size'].'px; overflow-y:auto; border:1px solid #ABD9AB; padding:10px;">
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
							 .width('.$_POST['size'].')
							 .height('.$_POST['size'].');

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
				//echo '<div class="row">';
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
				vennForTwo($value_result[$i], $value_result[$j], $name_result[$i], $name_result[$j], $_POST['size']);
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

					vennForThree($value_result[$i], $value_result[$j], $value_result[$k], $name_result[$i], $name_result[$j], $name_result[$k], $_POST['size']);

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
			//print_r($array_total_intersect); exit();
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



?>