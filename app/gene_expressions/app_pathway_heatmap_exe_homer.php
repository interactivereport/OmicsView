<?php

//Up
$colorScale1 = array(
        array('1.0', 'rgb(165,0,38)'),
        array('0.8', 'rgb(215,48,39)'),
        array('0.6', 'rgb(244,109,67)'),
        array('0.4', 'rgb(253,174,97)'),
        array('0.2', 'rgb(254,224,144)'),
        array('0.0', 'rgb(224,243,248)'),
		);
		
$colorScale1 = array(
        array('0.0', 'rgb(179,13,30)'),
        array('1.0', 'rgb(220,220,220)'),
		);
		
		
//Down		
$colorScale2 = array(
        array('0.0', 'rgb(224,243,248)'),
        array('1.0', 'rgb(49,54,149)'),
		);
		
$colorScale2 = array(
        array('1.0', 'rgb(224,243,248)'),
        array('0.0', 'rgb(49,54,149)'),
		);
		
$_POST['GeneSet_Up'] 	= array_clean(explode("\n", $_POST['GeneSet_Up']));
$_POST['GeneSet_Down'] 	= array_clean(explode("\n", $_POST['GeneSet_Down']));



$results1 				= prepareHomerDataByGeneSet($comparisons, $_POST['GeneSet_Up'], 'Up', $_POST['category']);
$results2 				= prepareHomerDataByGeneSet($comparisons, $_POST['GeneSet_Down'], 'Down', $_POST['category']);




echo "<hr/>";


if (true){
	$heatmapDataKey		= putSQLCacheWithoutKey($results1['Export']['Heatmap'], '', 'prepareHomerDataByGeneSet', 1);
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h4>Upregulated Pathways vs. Comparison ID</h4>";
			echo "<h5>{$APP_CONFIG['APP']['Homer'][$_POST['category']]}: log<sub>10</sub>(p-value)</h5>";
			echo "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ';
				echo "<a href='javascript:void(0);' id='downloadPlot1_{$uniqueID}'>SVG</a>";
				echo " - ";
				echo "<a href='app_common_table_download.php?key={$heatmapDataKey}&filename=pathway_heatmap_{$_POST['category']}_Up-Regulated.csv' target='_blank'>Heatmap Data</a>";
			echo "<div id='plotSection1' class='plotSection'></div>";
		echo "</div>";
	echo "</div>";
	
}

echo "<hr/>";


if (true){
	$heatmapDataKey		= putSQLCacheWithoutKey($results2['Export']['Heatmap'], '', 'prepareHomerDataByGeneSet', 1);
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h4>Downregulated Pathways vs. Comparison ID</h4>";
			echo "<h5>{$APP_CONFIG['APP']['Homer'][$_POST['category']]}: log<sub>10</sub>(p-value)</h5>";
			echo "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ';
				echo "<a href='javascript:void(0);' id='downloadPlot2_{$uniqueID}'>SVG</a>";
				echo " - ";
				echo "<a href='app_common_table_download.php?key={$heatmapDataKey}&filename=pathway_heatmap_{$_POST['category']}_Down-Regulated.csv' target='_blank'>Heatmap Data</a>";
			echo "<div id='plotSection2' class='plotSection'></div>";
		echo "</div>";
	echo "</div>";
	
}
	




?>



<script type="text/javascript">

$(document).ready(function(){
	
	var data1 = [{
		z: [<?php echo implode(',', $results1['JS']['z']); ?>],
		x: [<?php echo implode(',', $results1['JS']['x']); ?>],
		y: [<?php echo implode(',', $results1['JS']['y']); ?>],
		comparisonIndex: [<?php echo implode(',', $results1['JS']['comparisonIndex']); ?>],
		zmax: 0,
		zmin: -10,
		type: 'heatmap',
		colorscale: <?php echo json_encode($colorScale1); ?>,
		text: [<?php echo implode(',', $results1['JS']['text']); ?>],
	  }];
	  
	var layout1 = {
		width: <?php echo 9 * $results1['Summary']['max_identifier_length'] + 100 + 40 * array_size($results1['JS']['x']); ?>,
		height:<?php echo max(1200, array_size($results1['JS']['y']) * 20 + 200); ?>,
		margin: {
			l: <?php echo $results1['Summary']['max_identifier_length']*9; ?>,
			t: 400,
		},
		xaxis: {
			side: 'top',
			tickangle: -90,
		  },
		hoverinfo: 'text',
		
	};	


					
					
	var data2 = [{
		z: [<?php echo implode(',', $results2['JS']['z']); ?>],
		x: [<?php echo implode(',', $results2['JS']['x']); ?>],
		y: [<?php echo implode(',', $results2['JS']['y']); ?>],
		comparisonIndex: [<?php echo implode(',', $results2['JS']['comparisonIndex']); ?>],
		zmax: 0,
		zmin: -10,
		type: 'heatmap',
		colorscale: <?php echo json_encode($colorScale2); ?>,
		text: [<?php echo implode(',', $results2['JS']['text']); ?>],
	  }];
	  
	var layout2 = {
		width: <?php echo 9 * $results2['Summary']['max_identifier_length'] + 100 + 40 * array_size($results1['JS']['x']); ?>,
		height:<?php echo max(1200, array_size($results2['JS']['y']) * 20 + 200); ?>,
		margin: {
			l: <?php echo $results2['Summary']['max_identifier_length']*9; ?>,
			t: 400,
		},
		xaxis: {
			side: 'top',
			tickangle: -90,
		  },
		hoverinfo: 'text',
		
	};	
	
	
	Plotly.newPlot('plotSection1', data1, layout1).then(function(gd){
					Plotly.toImage(gd, {
			  				format: 'svg', 
							width: layout1.width,
							height: layout1.height
							}).then(function(dataUrl) {
					    		$('#svgCode1').val(dataUrl);
							});
							
	 				Plotly.toImage(gd, {
			  				format: 'png', 
							width: layout1.width,
							height: layout1.height
							}).then(function(dataUrl) {
					    		$('#pngCode1').val(dataUrl);
							});
					
					
					$(document).on('click', '#downloadPlot1_<?php echo $uniqueID; ?>', function(){
						Plotly
							.downloadImage(gd, {
								filename: 'Up-Regulated',
								format: 'svg',
								height: layout1.height,
								width: layout1.width
							})
							.then(function(filename){
								
							});
							
						});
						
						
					});
					
					
	Plotly.newPlot('plotSection2', data2, layout2).then(function(gd){
					Plotly.toImage(gd, {
			  				format: 'svg', 
							width: layout2.width,
							height: layout2.height
							}).then(function(dataUrl) {
					    		$('#svgCode2').val(dataUrl);
							});
							
	 				Plotly.toImage(gd, {
			  				format: 'png', 
							width: layout2.width,
							height: layout2.height
							}).then(function(dataUrl) {
					    		$('#pngCode2').val(dataUrl);
							});
					$(document).on('click', '#downloadPlot2_<?php echo $uniqueID; ?>', function(){
						Plotly
							.downloadImage(gd, {
								filename: 'Down-Regulated',
								format: 'svg',
								height: layout2.height,
								width: layout2.width
							})
							.then(function(filename){
								
							});
							
						});
						
						
					});
					
	document.getElementById('plotSection1').on('plotly_click', function(data){
		
		var currentIndex_X = data.points[0].pointIndex[1];
		var currentIndex_Y = data.points[0].pointIndex[0];
		var comparisonIndex = data.points[0].data.comparisonIndex[currentIndex_X];
		var comparisonID	= data.points[0].x;
		var geneSet			= data.points[0].y;
		
		var comparisonIndexes = '';
		
		for (var i = 0; i <  data.points[0].data.comparisonIndex.length; i++){
			comparisonIndexes += data.points[0].data.comparisonIndex[i] + ',';
		}
		
		
		var content = "<h4>Summary</h4><div style='margin-left:15px;'>" + data.points[0].text + "</div><br/>";
		
		content += "<ul>";
		
			content += "<li><a href='../plot/search_comparison/single_comparison.php?type=comparison&id=" + comparisonIndex + "' target='_blank'>";
			content += "Review Comparison (" + comparisonID + ")";
			content += "</a></li>";
			
			
			content += "<li><a href='../plot/volcano/index.php?direction=Up&table=<?php echo $_POST['category']; ?>&id=" + comparisonIndex + "&geneset=" + encodeURIComponent(geneSet) + "' target='_blank'>";
			content += "View Data in Volcano Plot";
			content += "</a></li>";
			
			<?php if ($_POST['category'] == 'kegg'){ ?>
				content += "<li><a href='../bxgenomics/tool_pathway/kegg.php?id=" + encodeURIComponent(comparisonIndex) + "&KEGG=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "KEGG Pathway Visualization (" + comparisonID + " only)</a>";
				content += "</a></li>";
				
				content += "<li><a href='../bxgenomics/tool_pathway/kegg.php?ids=" + encodeURIComponent(comparisonIndexes) + "&KEGG=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "KEGG Pathway Visualization (All Comparisons)</a>";
				content += "</a></li>";
			<?php } ?>

			
			<?php if ($_POST['category'] == 'wikipathways'){ ?>
				content += "<li><a href='../bxgenomics/tool_pathway/index.php?id=" + encodeURIComponent(comparisonIndex) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "WikiPathways Visualization (" + comparisonID + " only)";
				content += "</a></li>";
				
				content += "<li><a href='../bxgenomics/tool_pathway/index.php?ids=" + encodeURIComponent(comparisonIndexes) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "WikiPathways Visualization (All Comparisons)";
				content += "</a></li>";
			<?php } ?>
			
			
			<?php if ($_POST['category'] == 'reactome'){ ?>
				content += "<li><a href='../bxgenomics/tool_pathway/reactome.php?id=" + encodeURIComponent(comparisonIndex) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "Reactome Visualization (" + comparisonID + " only)";
				content += "</a></li>";
				
				content += "<li><a href='../bxgenomics/tool_pathway/reactome.php?ids=" + encodeURIComponent(comparisonIndexes) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "Reactome Visualization (All Comparisons)";
				content += "</a></li>";
			<?php } ?>
			
			
			
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

	document.getElementById('plotSection2').on('plotly_click', function(data){
		
		var currentIndex_X = data.points[0].pointIndex[1];
		var currentIndex_Y = data.points[0].pointIndex[0];
		var comparisonIndex = data.points[0].data.comparisonIndex[currentIndex_X];
		var comparisonID	= data.points[0].x;
		var geneSet			= data.points[0].y;
		
		var comparisonIndexes = '';
		
		for (var i = 0; i <  data.points[0].data.comparisonIndex.length; i++){
			comparisonIndexes += data.points[0].data.comparisonIndex[i] + ',';
		}
		
		
		var content = "<h4>Summary</h4><div style='margin-left:15px;'>" + data.points[0].text + "</div><br/>";
		
		content += "<ul>";
		
			content += "<li><a href='../plot/search_comparison/single_comparison.php?type=comparison&id=" + comparisonIndex + "' target='_blank'>";
			content += "Review Comparison (" + comparisonID + ")";
			content += "</a></li>";
			
			
			content += "<li><a href='../plot/volcano/index.php?direction=Down&table=<?php echo $_POST['category']; ?>&id=" + comparisonIndex + "&geneset=" + encodeURIComponent(geneSet) + "' target='_blank'>";
			content += "View Data in Volcano Plot";
			content += "</a></li>";
			
			
			<?php if ($_POST['category'] == 'kegg'){ ?>
				content += "<li><a href='../bxgenomics/tool_pathway/kegg.php?id=" + encodeURIComponent(comparisonIndex) + "&KEGG=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "KEGG Pathway Visualization (" + comparisonID + " only)</a>";
				content += "</a></li>";
				
				content += "<li><a href='../bxgenomics/tool_pathway/kegg.php?ids=" + encodeURIComponent(comparisonIndexes) + "&KEGG=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "KEGG Pathway Visualization (All Comparisons)</a>";
				content += "</a></li>";
			<?php } ?>
			
			
			<?php if ($_POST['category'] == 'wikipathways'){ ?>
				content += "<li><a href='../bxgenomics/tool_pathway/index.php?id=" + encodeURIComponent(comparisonIndex) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "WikiPathways Visualization (" + comparisonID + " only)";
				content += "</a></li>";
				
				content += "<li><a href='../bxgenomics/tool_pathway/index.php?ids=" + encodeURIComponent(comparisonIndexes) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "WikiPathways Visualization (All Comparisons)";
				content += "</a></li>";
			<?php } ?>
			
			
			<?php if ($_POST['category'] == 'reactome'){ ?>
				content += "<li><a href='../bxgenomics/tool_pathway/reactome.php?id=" + encodeURIComponent(comparisonIndex) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "Reactome Visualization (" + comparisonID + " only)";
				content += "</a></li>";
				
				content += "<li><a href='../bxgenomics/tool_pathway/reactome.php?ids=" + encodeURIComponent(comparisonIndexes) + "&pathway=" + encodeURIComponent(geneSet) + "' target='_blank'>";
				content += "Reactome Visualization (All Comparisons)";
				content += "</a></li>";
			<?php } ?>
			
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