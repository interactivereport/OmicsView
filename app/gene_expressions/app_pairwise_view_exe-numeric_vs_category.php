<?php

include_once('config_init.php');


if ($y_numeric){
	$results = preparePairwiseData_Numeric_vs_Category(
													$getSampleIDsExistenceInfo['SampleIndexes'], 
													$y_numeric_data, 
													$x_category_data, 
													$_POST['x_axis_sample_attribute'],
													$color_data,
													$_POST['data_source'], 
													$_POST['data_source_private_project_indexes']);
													
	$graphOrientation = 'vertical';
	
	if ($y_numeric_data['Type'] == 'Sample'){
		$plotTitle = "{$y_numeric_data['Name']} vs {$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['x_axis_sample_attribute']]['Title']}";
	} else {
		$plotTitle = "Gene Expression Level of {$y_numeric_data['Name']} vs {$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['x_axis_sample_attribute']]['Title']}";
	}
														
} else {
	$results = preparePairwiseData_Numeric_vs_Category(
													$getSampleIDsExistenceInfo['SampleIndexes'], 
													$x_numeric_data, 
													$y_category_data, 
													$_POST['y_axis_sample_attribute'],
													$color_data,
													$_POST['data_source'], 
													$_POST['data_source_private_project_indexes']);
													
													
	if ($x_numeric_data['Type'] == 'Sample'){
		$plotTitle = "{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['y_axis_sample_attribute']]['Title']} vs {$x_numeric_data['Name']}";
	} else {
		$plotTitle = "{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['y_axis_sample_attribute']]['Title']} vs Gene Expression Level of {$x_numeric_data['Name']}";
	}
	
	$graphOrientation = 'horizontal';										
}



if ($results['Sample_Count'] <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	exit();
	
} else {
	echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<h3>{$plotTitle}</h4>";
			echo "<p>The search result contains {$results['Sample_Count_Formatted']} samples.</p>";
		echo "</div>";
	echo "</div>";	
}



if (true){	
	unset($researchProjectAPI);
	$researchProjectAPI['Title'] 			= $plotTitle;
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
            "x" : {
				"<?php echo $results['canvasxpress']['x']['Category_Name']; ?>": [<?php echo implode(',', $results['canvasxpress']['x']['Category']); ?>]
				
				<?php if ($results['canvasxpress']['x']['Color_Name'] != ''){ ?>
					,"<?php echo $results['canvasxpress']['x']['Color_Name']; ?>": [<?php echo implode(',', $results['canvasxpress']['x']['Color']); ?>]				
				<?php } ?>
				
								
            },
            "y" : {
              "vars" : ["<?php echo $results['canvasxpress']['y']['vars']; ?>"],
              "smps" : [ <?php echo implode(',', $results['canvasxpress']['y']['smps']); ?> ],
              "data" : [[<?php echo implode(',', $results['canvasxpress']['y']['data']); ?>]]              
            }
		},
		  
		  
		{
			 "graphOrientation": 			"<?php echo $graphOrientation; ?>",
			 "graphType": 					"Boxplot",
			 
			 "jitter": 						true,
			 
			 <?php if ($results['canvasxpress']['x']['Color_Name'] != ''){ ?>
				 "colorBy": 					"<?php echo $results['canvasxpress']['x']['Color_Name']; ?>",
	 			 "legendBox":  				true,
				 "showLegend": 				true,
			 <?php } else { ?> 
	 			 "legendBox":  				false,
				 "showLegend": 				false,
			 <?php } ?>
				

			 
			 
			 "plotByVariable": 				true,
			 "showBoxplotOriginalData": 	true,
			 "smpLabelRotate": 				0,
			 
			 
			 

			 "showShadow": 					false,
			 
			 "title": 						"",
			 'axisTitleScaleFontFactor': 	0.5,
			 'axisTickFontSize':			12,
			 'axisTickScaleFontFactor': 	0.5,
			 
			 "citation": 					"",
			 'citationScaleFontFactor': 	0.7,

			 
			 "xAxisTitle": 					"",	
			 "titleFontSize":				25,
			
			 
			 'smpLabelScaleFontFactor': 	0.7,
			 'varLabelScaleFontFactor':		0.7,
			 'titleScaleFontFactor': 		0.7,
			 'subtitleScaleFontFactor': 	0.7,

			 'legendScaleFontFactor': 		0.6,
			 
			 'nodeScaleFontFactor': 		0.7,
			 
			 'sampleSeparationFactor': 		0.7,
			 'variableSeparationFactor': 	0.7,
			 'widthFactor': 				0.7,

			 
			 'printType':					'window',
			 
			 
			 			 
		}
	);
	plotObj.sizes = plotObj.sizes.map(function(x){
			return Number(x * 0.5).toFixed(1);
		});
	CanvasXpress.stack["plotSection"]["config"]["sizes"] = plotObj.sizes.map(function(x){
		return Number(x * 0.5).toFixed(1);
	});
		
	plotObj.groupSamples(["<?php echo $results['canvasxpress']['x']['Category_Name']; ?>"]);
	
	<?php if ($urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "<?php echo "{$_POST['URL']}?key={$urlKey}"; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "", URL);
	}
	<?php } ?>
	
	

});

</script>

