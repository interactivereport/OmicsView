<?php
include('config.php');
error_reporting(0);

$sessionKey = $_GET['sessionKey'];


?>
<!DOCTYPE html>
<html>
<head>
<title>WikiPathways Pathway Viewer</title>

<script src='../../../bxaf_lite/library/jquery/jquery.min.js.php'></script>

<link   href='../../../bxaf_lite/library/tether/css/tether.min.css' rel='stylesheet'>
<script src='../../../bxaf_lite/library/tether/js/tether.min.js.php'></script>
<link   href='../../../bxaf_lite/library/bootstrap/css/bootstrap.min.css.php' rel='stylesheet' type='text/css'>
<script src='../../../bxaf_lite/library/bootstrap/js/bootstrap.min.js.php'></script>
<link   href='../../../bxaf_lite/library/font-awesome/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

<?php /* <script type='text/javascript' src="../../gene_expressions/js/bootstrap-select/bootstrap-select-1.12.2.min.js"></script> */ ?>

<style>
a#wplink {
	text-decoration:none;
	font-family:serif;
	color:black;
	font-size:12px;
}
#logolink {
	float:right;
	top:-20px;
	left: -10px;
	position:relative;
	z-index:2;
	opacity: 0.5;
}
html, body {
	width:100%;
	height:100%;
	font-size:14px;
}
#pvjs-widget {
	top:0;
	left:0;
	font-size:12px;
	width:100%;
	height:inherit;
	border:1px solid #CCC;
	border-radius:10px;
}
</style>


</head>

<body>


<script type="text/javascript">

$(document).ready(function(){
	
	var annotation_text = 'Header';
	// Click on gene box
	$(document).on('click', 'body', function() {
		var annotation_text_new = $('.annotation-header-text').text().trim();
		var description = $('.annotation-description').html();
		if (annotation_text != annotation_text_new) {
			$('.annotation-header').find('.pvjs_added').remove();
			if (description == 'GeneProduct' || description == 'Protein') {
				$('<p class="pvjs_added"><i class="fa fa-spin fa-spinner"></i> Loading comparison info...</p>').insertAfter('.annotation-header-text');
				var allGeneInfo = JSON.parse(localStorage.getItem('pathway'));
				var geneInfo = allGeneInfo[annotation_text_new][0];
	
				// Check Inhouse
				var inhouse = false;
				$('.input_comparison_id').each(function(index, element) {
				  if ($(element).val().substring(0, 9) == '(inhouse)') inhouse = true;
				});
	
				var content = '<table class="table table-bordered" style="font-size:12px;">';
				if (!inhouse) {
				  content += '<tr><td>FDR</td><td>' + geneInfo['FDR'] + '</td></tr>';
				  content += '<tr><td>P-value</td><td>' + geneInfo['p-value'] + '</td></tr>';
				} else {
				  content += '<tr><td>Stats</td><td>' + geneInfo['FDR'] + '</td></tr>';
				}
	
				content += '<tr><td>Name</td><td>' + geneInfo['name'] + '</td></tr>';
				content += '<tr><td>Log2FC</td><td>' + geneInfo['logFC'] + '</td></tr>';
				content += '</table>';
				// content += 'LogFC: ' + geneInfo['logFC'] + '</span>';
				$('.pvjs_added').html(content);
			}
		}
		annotation_text = annotation_text_new;
	});
});

</script>


<?php
	
	if ($_GET['iframe']){
		echo "<p><a href='exe_pvjs_generate_chart_iframe.php?sessionKey={$sessionKey}' target='_blank'><i class='fa fa-fw fa-expand' aria-hidden='true'></i> Full Screen</a></p>";
	}


	echo "<p>";
	
	
	echo "<a href='javascript:void(0);' id='saveSVGTrigger'>";
		echo "<i class='fa fa-fw fa-file-image-o' aria-hidden='true'></i> Download SVG";
	echo "</a>";

	echo "</p>";	
	
	
	
	

	//echo "<p><a href='javascript:void(0);' id='saveSNGTrigger'>Save SVG</a></p>";
   echo $_SESSION['App']['pvjs']['exe_pvjs_generate_chart.php'][$sessionKey]['info'];

?>

<div style='display:none;'>
	<canvas id='canvas' height='3000' width='3000'></canvas>
</div>



</body>
</html>