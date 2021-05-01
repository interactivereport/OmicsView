<?php

include_once('config_init.php');



$results = preparePairwiseData_Numeric_vs_Numeric(
								$getSampleIDsExistenceInfo['SampleIndexes'],
								$x_numeric_data,
								$y_numeric_data,
								$color_data,
								$_POST['data_source'], 
								$_POST['data_source_private_project_indexes']
								);

if ($results['Sample_Count'] <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	exit();
	
} else {
	echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<h3>{$results['canvasxpress']['title']}</h4>";
			
			echo "<p>The search result contains {$results['Sample_Count_Formatted']} samples.</p>";
		echo "</div>";
	echo "</div>";	
}



if (true){	
	unset($researchProjectAPI);
	$researchProjectAPI['Title'] 			= $results['canvasxpress']['title'];
	$researchProjectAPI['Type'] 			= 'Pairwise View of Samples';
	$researchProjectAPI['Source_Page'] 		= 'Pairwise View of Samples';
	$researchProjectAPI['URL'] 				= "gene_expressions/app_pairwise_view.php?key={$urlKey}";
	$researchProjectAPI['Base64_Image_ID'] 	= 'plotSection';

	$researchProjectAPI['Parameters'] 		= $urlKey;
	include('app_research_project_api_modal.php');
	unset($researchProjectAPI);
}

echo "<br/>";

if (true){
	$dataKey = putSQLCacheWithoutKey($results['Export'], '', '', 1);
	$message = 
		"<p>
			<a href='app_common_table_download.php?key={$dataKey}&filename=raw_data.csv' target='_blank'>". 
					printFontAwesomeIcon('fas fa-download') . 
					" Download Data
			</a>
		</p>";
		
		
	$sessionID = getUniqueID();
	unset($_SESSION['List'][$sessionID]);
	$_SESSION['List'][$sessionID] = $results['SampleID'];
		
	$message .= 
		"<p>
			<a href='app_list_new.php?Category=Sample&Session={$sessionID}' target='_blank'>". 
					printFontAwesomeIcon('far fa-file') . 
					" {$APP_CONFIG['APP']['List_Category']['Sample']['Create_New_List']} ({$results['Sample_Count_Formatted']})
			</a>
		</p>";
		
		
	echo $message;	
}

echo "<hr/>";




if (true){
	$class = 'col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12';
	$height = $_POST['page_height'];
	$width	= $_POST['page_width'];

	echo "<div class='row'>";
		echo "<div class='{$class}'>";
			
			

			echo "<div style='height:{$height}px; width:{$width}px;'>";
				echo "<canvas id='plotSection' width='{$width}' height='{$height}' xresponsive='true' aspectRatio='1:1'></canvas>";
			echo "</div>";
			
		echo "</div>";
	echo "</div>";
}



?>


<script type="text/javascript">

$(document).ready(function(){
	var plotObj = new CanvasXpress('plotSection',
		{
			
  "z": {
    "<?php echo $results['canvasxpress']['Category']; ?>": [<?php echo implode(',', $results['canvasxpress']['z']['Category']); ?>],
    
  },

  "y": {
    "smps": [<?php echo implode(',', $results['canvasxpress']['y']['smps']); ?>],
    "vars": [<?php echo implode(',', $results['canvasxpress']['y']['vars']); ?>],
    "data": [<?php echo implode(',', $results['canvasxpress']['y']['data']); ?>]
  }
}, {
  "axisAlgorithm": "rPretty",
  "axisTickFontSize": 11,
  "axisTitleFontSize": 11,
  "backgroundType": "window",
  "backgroundWindow": "rgb(238,238,238)",
  "colorBy": "<?php echo $results['canvasxpress']['Category']; ?>",
  
  "graphType": "Scatter2D",
  "legend": [],
  "legendBackgroundColor": "rgb(238,238,238)",
  "legendBoxColor": "rgb(0,0,0)",
  "legendFontSize": 11,
  "legendInside": true,
  "legendPosition": "bottomRight",
  "maxRows": 5,
  "motionCurrentFontSize": 50,
  "motionWidth": 250,
  "randomNetworkLayout": false,
  "reproduceTime": 50,
  "showTransition": true,
  "sizeBy": "<?php echo $results['canvasxpress']['Category']; ?>",
  "sizeByShowLegend": false,
  "sizes": [<?php echo implode(',', $results['canvasxpress']['sizes']); ?>],
  "smpDendrogramNewick": false,
  "subtitleFontSize": 23,
  "summaryType": "raw",
  "title": "",
  "titleFontSize": 25,
  "varDendrogramNewick": false,
  "xAxis": [
    "<?php echo $results['canvasxpress']['xAxisTitle']; ?>"
  ],
  "xAxisFloorValue": null,
  "xAxisTickColor": "rgb(255,255,255)",
  "xAxisTitle": "<?php echo $results['canvasxpress']['xAxisTitle']; ?>",
  "yAxis": [
    "<?php echo $results['canvasxpress']['yAxisTitle']; ?>"
  ],
  "yAxisTickColor": "rgb(255,255,255)",
  "yAxisTitle": "<?php echo $results['canvasxpress']['yAxisTitle']; ?>"
});
	
	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "<?php echo "{$_POST['URL']}?key={$urlKey}"; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "", URL);
	}
	<?php } ?>
	
	

});

</script>
