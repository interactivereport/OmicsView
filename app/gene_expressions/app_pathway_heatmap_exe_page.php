<?php

$colorScale = array(
        array('1.0', 'rgb(165,0,38)'),
        array('0.888888888889', 'rgb(215,48,39)'),
        array('0.777777777778', 'rgb(244,109,67)'),
        array('0.666666666667', 'rgb(253,174,97)'),
        array('0.555555555556', 'rgb(254,224,144)'),
        array('0.444444444444', 'rgb(224,243,248)'),
        array('0.333333333333', 'rgb(171,217,233)'),
        array('0.222222222222', 'rgb(116,173,209)'),
        array('0.111111111111', 'rgb(69,117,180)'),
        array('0', 'rgb(49,54,149)'),
		);

$_POST['GeneSet'] 	= array_clean(explode("\n", $_POST['GeneSet']));
$results 			= preparePAGEDataByGeneSet($comparisons, $_POST['GeneSet']);






if (true){
	

	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h4>{$APP_MESSAGE['Gene']} Set vs. Comparison ID</h4>";
		echo "</div>";
	echo "</div>";
	
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h5>Z-Score</h5>";
		echo "</div>";
	echo "</div>";
}


if (true){

	$heatmapDataKey		= putSQLCacheWithoutKey($results['Export']['Heatmap'], '', 'prepareHomerDataByGeneSet', 1);
	
	$message = "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ' . 
				"<a href='javascript:void(0);' id='downloadPlot_{$uniqueID}'>SVG</a>" . 
				" - " . 
				"<a href='app_common_table_download.php?key={$heatmapDataKey}&filename=pathway_heatmap_PAGE.csv' target='_blank'>Heatmap Data</a>" . 
				"</p>";
	
	echo $message;

	
}





echo "<div class='row'>";
	echo "<div class='col-12'>";
		echo "<div id='plotSection'></div>";
	echo "</div>";
echo "</div>";



?>



<script type="text/javascript">

$(document).ready(function(){

	
	var data = [{
		z: [<?php echo implode(',', $results['JS']['z']); ?>],
		x: [<?php echo implode(',', $results['JS']['x']); ?>],
		y: [<?php echo implode(',', $results['JS']['y']); ?>],
		comparisonIndex: [<?php echo implode(',', $results['JS']['comparisonIndex']); ?>],
		zmax: 10,
		zmin: -10,
		type: 'heatmap',
		colorscale: <?php echo json_encode($colorScale); ?>,
		text: [<?php echo implode(',', $results['JS']['text']); ?>],
	  }];
	  
	var layout = {
		width: <?php echo 9 * $results['Summary']['max_identifier_length'] + 100 + 40 * array_size($results['JS']['x']); ?>,
		height:<?php echo max(1200, array_size($results['JS']['y']) * 20 + 200); ?>,
		margin: {
			l: <?php echo $results['Summary']['max_identifier_length']*9; ?>,
			t: 400,
		},
		xaxis: {
			side: 'top',
			tickangle: -90,
		  },
		hoverinfo: 'text',
		
	};	
	
	

	Plotly.newPlot('plotSection', data, layout).then(function(gd){
					Plotly.toImage(gd, {
			  				format: 'svg', 
							width: layout.width,
							height: layout.height
							}).then(function(dataUrl) {
					    		$('#svgCode1').val(dataUrl);
							});
							
	 				Plotly.toImage(gd, {
			  				format: 'png', 
							width: layout.width,
							height: layout.height
							}).then(function(dataUrl) {
					    		$('#pngCode1').val(dataUrl);
							});
							
							
					$(document).on('click', '#downloadPlot_<?php echo $uniqueID; ?>', function(){
						Plotly
							.downloadImage(gd, {
								filename: 'PAGE_Heatmap',
								format: 'svg',
								height: layout.height,
								width: layout.width
							})
							.then(function(filename){
								
							});
							
						});
						
						
					});
					
					

	
	
	document.getElementById('plotSection').on('plotly_click', function(data){
		
		var currentIndex_X = data.points[0].pointIndex[1];
		var currentIndex_Y = data.points[0].pointIndex[0];
		var comparisonIndex = data.points[0].data.comparisonIndex[currentIndex_X];
		var comparisonID	= data.points[0].x;
		var geneSet			= data.points[0].y;
		
		
		var content = "<h4>Summary</h4><div style='margin-left:15px;'>" + data.points[0].text + "</div><br/>";
		
		content += "<ul>";
		
			content += "<li><a href='../plot/search_comparison/single_comparison.php?type=comparison&id=" + comparisonIndex + "' target='_blank'>";
			content += "Review Comparison (" + comparisonID + ")";
			content += "</a></li>";
			

			content += "<li><a href='../plot/volcano/index.php?table=<?php echo $_POST['category']; ?>&id=" + comparisonIndex + "&geneset=" + encodeURIComponent(geneSet) + "' target='_blank'>";
			content += "View Data in Volcano Plot";
			content += "</a></li>";
		content += "</ul>";

		bootbox.confirm({
            message: content,
            buttons: {
              confirm: {
                label: 'Close',
                className: 'btn-primary'
              },
              cancel: {
                label: 'No',
                className: 'btn-danger hidden'
              }
            },
            callback: function (result) {
             
            }
          });
		
		
	});
	
});


</script>