<?php

include_once(__DIR__ . "/config.php");

$BXAF_CONFIG['BXAF_VENN_DATA_DIR'] = $BXAF_CONFIG['BXGENOMICS_CACHE_DIR'];
$BXAF_CONFIG['BXAF_VENN_DATA_URL'] = $BXAF_CONFIG['BXGENOMICS_CACHE_URL'];


?><!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<link type="text/css" rel="stylesheet" href="css/style.css" />

	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js.php"></script>

	<link href="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery-ui/jquery-ui.min.css.php" rel="stylesheet">
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery-ui/jquery-ui.min.js.php"></script>

	<script src="js/d3.js.php"></script>
	<script src="js/venn.js.php"></script>

	<script type="text/javascript">


	$(document).ready(function(){
		$('.hidden_at_first').hide(0);

	    $( "#slider2" ).slider({
	      value:500,
	      min: 300,
	      max: 2000,
	      step: 100,
	      slide: function( event, ui ) {
	        $( "#amount2" ).val(ui.value );
	      }
	    });
	    $( "#amount2" ).val( $( "#slider2" ).slider( "value" ) );

		$(document).on("change", ".sets", function(){
			if($("input[name='sets']:checked").val() == 2){
				$('.not_for_two').fadeOut(500);
			} else if($("input[name='sets']:checked").val() == 3){
				$('.not_for_two').fadeIn(500);
			}
		})

		$('#example').click(function(){
			$("#A").val("1000");$("#B").val("700");$("#C").val("600");
			$("#AB").val("300");$("#AC").val("250");$("#BC").val("100");$("#ABC").val("30");
			if($('#sets2').is(':checked')){
				$('.not_for_two').fadeOut(0);
			}
		})

		$(document).on('click', '#btn_submit', function(){
			var options = {
				url: 'venn_exe.php?action=draw_pic&id=' + Date.now(),
				type: 'post',
				beforeSubmit: function(formData, jqForm, options){
					if ($('#A').val() == '' || $('#B').val() == ''){
						bootbox.alert("Please fill in required fields.");
						return false;
					}
					if($("input[name='sets']:checked").val() == 3){
						if( parseInt($('#A').val()) < parseInt($('#AB').val()) || parseInt($('#A').val()) < parseInt($('#AC').val()) ||
							parseInt($('#B').val()) < parseInt($('#AB').val()) || parseInt($('#B').val()) < parseInt($('#BC').val()) ||
							parseInt($('#C').val()) < parseInt($('#AC').val()) || parseInt($('#C').val()) < parseInt($('#BC').val()) ||
							parseInt($('#AB').val()) < parseInt($('#ABC').val()) || parseInt($('#AC').val()) < parseInt($('#ABC').val()) ||
							parseInt($('#BC').val()) < parseInt($('#ABC').val())){

								bootbox.alert('The number of sets you entered is wrong, please check.');

								return false;
							}
					} else if($("input[name='sets']:checked").val() == 2){
						if(parseInt($('#A').val()) < parseInt($('#AB').val()) || parseInt($('#B').val()) < parseInt($('#AB').val()) ){
								bootbox.alert('The number of sets you entered is wrong, please check.');
								return false;
							}
					}

					return true;
				},
				success: function(responseText, statusText){
					if(responseText.substring(0, 6)=='static'){
						var content = '<a href="<?php echo $BXAF_CONFIG['BXAF_VENN_DATA_URL']; ?>'+responseText.substring(responseText.length-10, responseText.length)+'venn.png" download><img src="<?php echo $BXAF_CONFIG['BXAF_VENN_DATA_URL']; ?>'+responseText.substring(responseText.length-10, responseText.length)+'venn.png"></a>';
						$('#div_results').html(content);
					} else if(responseText.substring(0, 6)=='square'){
						$('#div_results').html('<a href="<?php echo $BXAF_CONFIG['BXAF_VENN_DATA_URL']; ?>venn.png" download><img src="<?php echo $BXAF_CONFIG['BXAF_VENN_DATA_URL']; ?>venn.png"></a>');
					} else {
						$('#div_results').html(responseText);
					}
				}
			}
			$('#form_example').ajaxSubmit(options);
		})

	});

	</script>

</head>

<body>

	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
	<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
			<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">

	  			<div class="container-fluid">



					<div class="my-3">
						<h1>
							Area-Proportional Venn Diagram Plotter and Editor
							
						</h1>

						<div class="my-3">
							Tip: Use this tool to generate area-proportional Venn Diagrams.
							<a href="venn_overlap.php" class="mx-2 btn btn-sm btn-success"><i class="fas fa-link"></i> Calculate overlap between lists and draw Venn Diagram</a>
						</div>
					</div>


					<form id='form_example' role="form">

						<div class="form-inline">
							<div class="form-group"><strong>Number of Datasets: </strong></div>
							<div class="mx-2 checkbox">
								<input type="radio" name="sets" value="2" class="mx-2 sets" id="sets2"> Two
								<input type="radio" name="sets" value="3" class="mx-2 sets" id="sets3" checked> Three

								<a class="mx-2" href="javascript:void(0);" id="example"><i class="fas fa-arrow-circle-right"></i> Try With Example Data</a>
							</div>
						</div>



						<div class="row my-3 bg-light" id="data_div">
							<div class="col p-3">
								<div class="my-1"><strong>Datasets Labels:</strong></div>
								<div class="number_box my-1"><input type="text" class="form-control" placeholder="Label of Set A" name="labelA" id="labelA" value="A"></div>
								<div class="number_box my-1"><input type="text" class="form-control" placeholder="Label of Set B" name="labelB" id="labelB" value="B"></div>
								<div class="number_box not_for_two my-1"><input type="text" class="form-control" placeholder="Label of Set C" name="labelC" id="labelC" value="C"></div>
							</div>

							<div class="col p-3">
								<div class="my-1"><strong>Dataset Size (number only):</strong></div>
								<div class="number_box my-1"><input type="text" class="form-control" placeholder="Size of A" name="A" id="A"></div>
								<div class="number_box my-1"><input type="text" class="form-control" placeholder="Size of B" name="B" id="B"></div>
								<div class="number_box not_for_two my-1"><input type="text" class="form-control" placeholder="Size of C" name="C" id="C"></div>
							</div>

							<div class="col p-3">
								<div class="my-1"><strong>Intersection Size (number only):</strong></div>
								<div class="number_box my-1"><input type="text" class="form-control" placeholder="Size of A and B" name="AB" id="AB"></div>
								<div class="number_box not_for_two my-1"><input type="text" class="form-control" placeholder="Size of A and C" name="AC" id="AC"></div>
								<div class="number_box not_for_two my-1"><input type="text" class="form-control" placeholder="Size of B and C" name="BC" id="BC"></div>
								<div class="number_box not_for_two my-1"><input type="text" class="form-control" placeholder="Size of A and B and C" name="ABC" id="ABC"></div>
							</div>
						</div>

						<label for="amount2">Size of Venn Diagram (width and height in px):</label> <input type="text" id="amount2" name="size" readonly>

						<div id="slider2"></div>

						<div class="form-inline my-3">
							<strong>Diagram Type: </strong>
							<input class="mx-2" type="radio" name="image_type" value="1" checked> Responsive Diagram
							<input class="mx-2" type="radio" name="image_type" value="2"> Static Diagram
						</div>

						<button type="button" id="btn_submit" class="btn btn-primary">Display Venn Diagram</button>

					</form>

					<div class="w-100 my-3" id="div_debug"></div>
					<div class="w-100 my-3" id='div_results'></div>



				</div>

	        </div>
	        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	    </div>
	</div>

</body>
</html>