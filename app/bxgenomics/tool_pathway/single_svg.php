<?php
include_once('config.php');

$svg_code_file = $BXAF_CONFIG['CURRENT_SYSTEM_CACHE'] . $_GET['time'] . '/svg_code.txt';
if(! file_exists($svg_code_file)) die("SVG file is not found.");
else $svg_code = file_get_contents($svg_code_file);

$svg_data_file = $BXAF_CONFIG['CURRENT_SYSTEM_CACHE'] . $_GET['time'] . '/svg_data.txt';
if(file_exists($svg_data_file)) $svg_data = file_get_contents($svg_data_file);
else $svg_data = json_encode('');

?>
<html>
<head>
<?php
	$BXAF_CONFIG['BXAF_PAGE_HEADER_LIBRARIES']['font-awesome'] = 0;
	include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']);
?>

<style>
#pvjs-widget {
	top:0;
	left:0;
	font-size:12px;
	width:100% !important;
	height:100% !important;
	border:1px solid #CCC;
	border-radius:10px;
}
</style>


<script>

$(document).ready(function() {

	$(document).on('click', '#btn_save_svg', function() {
		var svgText = $('.diagram-container').html();

		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", "download_svg.php");
		form.setAttribute("accept-charset", "UTF-8");

		var hiddenSVGField = document.createElement("input");
		hiddenSVGField.setAttribute("type", "hidden");
		hiddenSVGField.setAttribute("name", "svgText");
		hiddenSVGField.setAttribute("value", svgText);

		form.appendChild(hiddenSVGField);
		document.body.appendChild(form);
		form.submit();
	});


	var annotation_text = 'Header';
	
	// Click on gene box
	$(document).on('click', '#pvjs-widget', function() {

		var annotation_text_new = $('.annotation-header-text').text().trim();
		var description = $('.annotation-description').html();

		$('.annotation-items-container-list').removeClass('ariutta-loading');

		$('.annotation-header').find('.annotation-header-close').remove();
		$('.annotation-header').find('.annotation-header-move').remove();

		$('.annotation-header').find('.pvjs_added').remove();


		if (annotation_text != annotation_text_new) {

			if (description == 'GeneProduct' || description == 'Protein') {

				$('<p class="pvjs_added"><i class="fas fa-spinner fa-pulse"></i> Loading comparison info...</p>').insertAfter('.annotation-description');

				var allGeneInfo = <?php echo $svg_data; ?>;

				var content = '';

				if (allGeneInfo.hasOwnProperty(annotation_text_new)) {
					var geneInfo = allGeneInfo[annotation_text_new];
					for(var key in geneInfo) {
						var info = geneInfo[key];

						if(content == ''){
							content += '<div style="font-size:11px; text-align: left;">';
							if(info['GeneName'] != '') content += '<strong>GeneName: </strong><a target="_blank" href="https://www.genecards.org/cgi-bin/carddisp.pl?gene=' + info['GeneName'] + '">' + info['GeneName'] + '</a><BR />';
							if(info['Alias'] != '') content += '<strong>Alias: </strong>' + info['Alias'] + '<BR />';
							if(info['Ensembl'] != '') content += '<strong>Ensembl: </strong><a target="_blank" href="http://useast.ensembl.org/Multi/Search/Results?q=' + info['Ensembl'] + '">' + info['Ensembl'] + '</a><BR />';
							if(info['EntrezID'] != '') content += '<strong>EntrezID: </strong><a target="_blank" href="https://www.ncbi.nlm.nih.gov/gene?term=' + info['EntrezID'] + '">' + info['EntrezID'] + '</a><BR />';
							if(info['Description'] != '') content += '<strong>Description: </strong>' + info['Description'] + '<BR />';
							if(info['GeneIndex'] != '') content += '<strong>Details: </strong><a target="_blank" href="../../../app/plot/search_comparison/single_comparison.php?type=gene&id=' + info['GeneIndex'] + '">View</a><BR />';
							content += '</div>';
						}

						content += '<table class="table table-bordered mt-3" style="font-size:11px;">';
						content += '<tr><td>Comparison</td><td>' + info['comparisonname'] + '</td></tr>';
						if(info['Log2FoldChange']) content += '<tr><td>logFC</td><td>' + info['Log2FoldChange'] + '</td></tr>';
						if(info['PValue']) content += '<tr><td>P.Value</td><td>' + info['PValue'] + '</td></tr>';
						if(info['AdjustedPValue']) content += '<tr><td>adj.P.Val</td><td>' + info['AdjustedPValue'] + '</td></tr>';
						content += '</table>';
					}
				}

				if(content == '') content = '<p class="text-danger p-3">No data found.</p>';

				$('.pvjs_added').html(content);
			}

		}
		annotation_text = annotation_text_new;
	});


});

</script>

</head>

<body class="p-3">
	<a href='Javascript: void(0);' id='btn_save_svg' class='mx-2'><i class='fas fa-download'></i> Download SVG</a>
	<a href='single_svg.php?time=<?php echo $_GET['time']; ?>' target="_blank" class='mx-2'><i class='fas fa-download'></i> Open in New Window</a>

	<hr />

	<?php
	  echo $svg_code;
	?>

</body>
</html>