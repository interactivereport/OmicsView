<?php

$margin_top 	= intval(abs($_POST['margin_top']));
$margin_bottom 	= intval(abs($_POST['margin_bottom']));
$margin_left 	= intval(abs($_POST['margin_left']));
$margin_right 	= intval(abs($_POST['margin_right']));


$height = intval(abs($_POST['plot_height']));
if ($height <= 0){
	$height = $results['Summary']['Height'];
}


$width = intval(abs($_POST['plot_width']));
if ($width <= 0){
	$width = $results['Summary']['Width'];	
}
if ($width <= 0){
	$width = $_POST['width'];
}

if ($_POST['plot_width'] <= 0){
	$width = max($_POST['plot_width'], $results['Summary']['Width'], $_POST['width']);
}


echo "<h5>{$results['Summary']['Chart']['Title_Single_Line_No_Gene']}</h5>";

echo "<div id='plotSection' style='width: {$width}px; height: {$height}px;'></div>";

if (!$_POST['API']){
	echo "<input type='hidden' id='svgCode'/>";
	echo "<input type='hidden' id='pngCode'/>";
}

?>
<style>
.js-plotly-plot .plotly .modebar {
    left: 10%;
    transform: translateX(-10%);
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	
	<?php if (!$_POST['API'] && $urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "<?php echo "{$_POST['URL']}?key={$urlKey}"; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "", URL);
	}
	<?php } ?>
	
	<?php
	foreach($results['Chart'] as $traceID => $traceInfo){
	?>
		var <?php echo $traceInfo['ID']; ?> = {
			x: [<?php echo implode(',', $traceInfo['x']); ?>],
			y: [<?php echo implode(',', $traceInfo['y']); ?>],
			ComparisonIndex: [<?php echo implode(',', $traceInfo['ComparisonIndex']); ?>],
			hoverinfo: "text",
			text: [<?php echo implode(',', $traceInfo['text']); ?>],
			mode: "markers",
			showlegend: false,
			name:  "<?php echo $traceInfo['name']; ?>",
			xaxis: "<?php echo $traceInfo['xaxis']; ?>",
			yaxis: "<?php echo $traceInfo['yaxis']; ?>",
			marker: {
				size: [<?php echo implode(',', $traceInfo['size']); ?>],
				sizeref: 7,
				sizemode: 'area',
				color: [<?php echo implode(',', $traceInfo['color']); ?>],
				<?php if ($_POST['shapeBy'] != ''){ ?>
				symbol: [<?php echo implode(',', $traceInfo['symbol']); ?>],
				<?php } ?>
			},
		};
	
	<?php
	}
	?>
	
	<?php
	foreach($results['Chart-Legend']['Color'] as $legendID => $legendInfo){
	?>
		var <?php echo $legendInfo['ID']; ?> = {
		  x: [' '],
		  y: [' '],
		  name: '<?php echo $legendInfo['name']; ?>',
		  mode: 'markers',
		  type: 'scatter',
		  xaxis: '<?php echo $legendInfo['xaxis']; ?>',
		  yaxis: '<?php echo $legendInfo['yaxis']; ?>',
		  marker: {
			color: '<?php echo $legendInfo['color']; ?>',
			size: [50],
			sizeref: 7,
			sizemode: 'area',
		  }
		  
		};
	<?php
	}
	?>
	
	<?php
	foreach($results['Chart-Legend']['Shape'] as $legendID => $legendInfo){
	?>
		var <?php echo $legendInfo['ID']; ?> = {
		  x: [' '],
		  y: [' '],
		  name: '<?php echo $legendInfo['name']; ?>',
		  mode: 'markers',
		  type: 'scatter',
		  xaxis: '<?php echo $legendInfo['xaxis']; ?>',
		  yaxis: '<?php echo $legendInfo['yaxis']; ?>',
		  marker: {
			color: '<?php echo $legendInfo['color']; ?>',
			symbol: '<?php echo $legendInfo['symbol']; ?>',
			size: [50],
			sizeref: 7,
			sizemode: 'area',
		  }
		  
		};
	<?php
	}
	?>
	
	var data = [<?php echo implode(',', $results['Summary']['Trace_Variables']); ?>, <?php echo implode(',', $results['Summary']['Legend_Variables']); ?>];
	
	var layout = {
	  margin: {
		  	l: <?php echo $margin_left; ?>, 
			t: <?php echo $margin_top; ?>, 
			b: <?php echo $margin_bottom; ?>, 
			r: <?php echo $margin_right; ?>
			},
	  grid: {
		  	rows: 1, 
			columns: <?php echo array_size($results['Summary']['Subplots_Variables']); ?>, 
			subplots:[<?php echo implode(',', $results['Summary']['Subplots_Variables']); ?>],
			},
	  title: '<?php echo $results['Summary']['Chart']['Title_Short']; ?>',
	  hovermode: 'closest',
	  font: {size: 13}, 
	  showlegend: true,
	  height: <?php echo $height; ?>,
	  width: <?php echo $width; ?>,
	  xaxis: {
		  range: [<?php echo $results['Summary']['Chart']['range']['min']; ?>, <?php echo $results['Summary']['Chart']['range']['max']; ?>],
		  automargin: true,
		  },
	  
	  yaxis: {
		  categoryorder: 'category descending',
		  title: {
			  		text: "<?php echo $results['Summary']['y-axis_Title']; ?>",
			   		standoff: 50,
		  		},
			},

	  annotations: [
	  	<?php
		foreach($results['Chart'] as $traceID => $traceInfo){
		?>
			{
			  x: 0, 
			  y: 1, 
			  font: {size: 13}, 
			  text: "<?php echo $traceInfo['name']; ?>",
			  xref: "<?php echo $traceInfo['xaxis']; ?>", 
			  yref: 'paper', 
			  xanchor: 'center', 
			  yanchor: 'bottom', 
			  showarrow: false,
			},
		<?php
		}
		?>
		{
		  x: 0.5, 
		  y: -0.3, 
		  font: {size: 15}, 
		  text: "Log<sub>2</sub>(Fold Change)",
		  xref: "paper", 
		  yref: 'paper', 
		  xanchor: 'center', 
		  yanchor: 'bottom', 
		  showarrow: false,
		},
	  ]
	};
	
	var otherOptions = {
		displaylogo:false, 
		modeBarButtonsToRemove:['sendDataToCloud'], 
		scrollZoom:true, 
		displayModeBar: true,
		
	};
	
	
	var PlotlyObj = document.getElementById('plotSection');
	
	//Plotly.newPlot('plotSection', data, layout, otherOptions).then(function(gd){
	Plotly.newPlot(PlotlyObj, data, layout, otherOptions).then(function(gd){
		
		<?php if (!$_POST['API']){ ?>
		  Plotly.toImage(gd, {
								format: 'svg', 
								width: <?php echo $width; ?>, 
								height: <?php echo $height; ?>,
								}).then(function(dataUrl) {
									$('#svgCode').val(dataUrl);
								});
			
							
		  Plotly.toImage(gd, {
								format: 'png', 
								width: <?php echo $width; ?>, 
								height: <?php echo $height; ?>,
								}).then(function(dataUrl) {
									$('#pngCode').val(dataUrl);
								});
		<?php } ?>
		});
		
	PlotlyObj.on('plotly_click', function(e){
		if (typeof(e.points[0].pointIndex) != "undefined"){
			var pointIndex = e.points[0].pointIndex;
			
			if (typeof(e.points[0].data.ComparisonIndex[pointIndex]) != "undefined"){
				var comparisonIndex = e.points[0].data.ComparisonIndex[pointIndex];	
			}
			
			if (typeof(comparisonIndex) != "undefined"){
				var myWindow = window.open("app_comparison_review.php?ID=" + comparisonIndex, '_blank');
			}
			
		}
	});
	
		
		
	<?php if (!$_POST['API']){ ?>
	$("#<?php echo $saveSVGTrigger; ?>").click(function(){
		
		Plotly.downloadImage('plotSection', {
						filename: 'Bubble_Plot',
						format: 'svg',
						height: <?php echo $height; ?>,
						width: <?php echo $width; ?>, 
					})
		  .then(function(filename){

		  });
	});
	<?php } ?>
	
});
</script> 

