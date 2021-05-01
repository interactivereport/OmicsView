<?php


//*****************************************
// Ready to display search result
//*****************************************

if (!$_POST['API']){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			echo "<h3>Search Summary</h3>";
			
			
			if ($_POST['searchOption'] == 0){
				
				if ($_POST['data_source']['private'] == ''){
					if ($dataFilterSummary == ''){
						$message = "The search result contains {$results['DataCount_Display']} out of {$results['DataCount_WihoutConditions_Display']} data points.";
					} else {
						$message = "The search result contains {$results['DataCount_Display']} out of {$results['DataCount_WihoutConditions_Display']} data points, which matches all of the conditions below:";
					}
				} else {
					if ($dataFilterSummary == ''){
						$message = "The search result contains {$results['DataCount_Display']} data points.";
					} else {
						$message = "The search result contains {$results['DataCount_Display']} data points, which matches all of the conditions below:";
					}
				}
				
			} else {
				$message = "The search result contains {$results['DataCount_Display']} data points.";
			}
	
			echo "<p>" . printFontAwesomeIcon('fas fa-search') . " {$message}</p>";
	
			echo $dataFilterSummary;
		echo "</div>";
	echo "</div>";
}


if ($results['ComparisonID']['Missing_Count'] > 0){

	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the comparison IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='comparisonMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;
	
	echo "<div id='comparisonMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Comparison IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Comparison ID Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDExistenceInfoSection'>{$results['ComparisonID']['Input_Count_Display']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Comparison ID Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDExistenceInfoSection'>{$results['ComparisonID']['Output_Count_Display']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Comparison ID Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#comparisonIDExistenceInfoSection'>{$results['ComparisonID']['Missing_Count_Display']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'comparisonIDExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Comparison IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$results['ComparisonID']['Input_Count_Display']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['ComparisonID']['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$results['ComparisonID']['Output_Count_Display']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['ComparisonID']['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$results['ComparisonID']['Missing_Count_Display']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['ComparisonID']['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	
	echo "</div>";
	
}


if (!$_POST['API'] && $results['SampleID']['Missing_Count'] > 0){


	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Some of the sample IDs you entered do not exist in the database. Please click <a href='javascript:void(0);' id='sampleMissingInfoTrigger' class='forceLink'>here</a> for details.</p>";
	echo $message;
	
	echo "<div id='sampleMissingInfo' class='startHidden'>";
	
		unset($tableContent);
		$tableContent['Header'][1]		= 'Category';
		$tableContent['Header'][2] 		= '# of Sample IDs';
		
		
		$tableContent['Body'][1]['Value'][1]	= '# of Sample ID Entered';
		$tableContent['Body'][1]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDExistenceInfoSection'>{$results['SampleID']['Input_Count_Display']}</a>";
		
		$tableContent['Body'][2]['Value'][1]	= '# of Sample ID Available';
		$tableContent['Body'][2]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDExistenceInfoSection'>{$results['SampleID']['Output_Count_Display']}</a>";
		
		$tableContent['Body'][3]['Value'][1]	= '# of Sample ID Not Available';
		$tableContent['Body'][3]['Value'][2]	= "<a data-toggle='modal' href='#sampleIDExistenceInfoSection'>{$results['SampleID']['Missing_Count_Display']}</a>";
		
		echo printTableHTML($tableContent, 1, 1, 0);
		
		
		$modalID 	= 'sampleIDExistenceInfoSection';
		$modalTitle = "<h4 class='modal-title'>Summary of Sample IDs</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$results['SampleID']['Input_Count_Display']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['SampleID']['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$results['SampleID']['Output_Count_Display']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['SampleID']['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$results['SampleID']['Missing_Count_Display']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['SampleID']['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
	
	
	echo "</div>";
	
}


if ($tooManyDataPoints){
	
	$message = "<p>" .  printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are too many data points in the search result. 
					Plotting too many data points may cause performance problem to your browser. 
					Please refine your search conditions to reduce the number of data points in the plot.</p>
				<p>" . printFontAwesomeIcon('fa-spacer') . " For preview purposes, the corresponding plot is based on <strong>{$APP_CONFIG['canvasxpress']['Data_Limit']}</strong> (out of {$results['DataCount_WihoutConditions_Display']}) randomly selected data points.</p>
				
				";
	
	$message .= "<div style='margin-left:20px;'><ul>";
	
	
	
	foreach($plotColumns as $tempKey => $plotColumn){
		
		$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary'][$currentTable]['SQL'][$plotColumn]['Title'];
		
		$modalID = 'TooManyDataPoints_' . md5($plotColumn);
		$modalTitle = "<h4 class='modal-title'>{$plotColumnToDisplay} ({$results['DataCount_Display']})</h4>";
		
		if (true){
			unset($tableContent);
			$tableContent['Header'][1]		= 'Category';
			$tableContent['Header'][2] 		= "<span class='text-nowrap'># of Data Points</span>";
			
			foreach($results['Category_Count'][$plotColumn] as $plotColumnCategory => $geneValueCount){
				
				if ($geneValueCount >= $APP_CONFIG['canvasxpress']['Data_Limit']){
					$geneValueCount = $geneValueCount . '&nbsp;' . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger');
					$tableContent['Body'][$plotColumnCategory]['Class']	= 'danger';
				}
				
				$tableContent['Body'][$plotColumnCategory]['Value'][1]	= $plotColumnCategory;
				$tableContent['Body'][$plotColumnCategory]['Value'][2] 	= $geneValueCount;
				
			}
			
			$modalBody = printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12');	
			
		}
		
		echo printModal($modalID, $modalTitle, $modalBody, '', 'modal-body-full-height');
		
		$message .= "<li>";
			$message .= "<a href='#{$modalID}' data-toggle='modal'>";
				$message .= $plotColumnToDisplay;
			$message .= "</a>";
		$message .= "</li>";
		
	}
	$message .= "</ul></div>";
	
	echo $message;
}


if (array_size($_POST_ORG) > 0){
	foreach($_POST_ORG as $tempKey => $tempValue){
		$_POST[$tempKey] = $tempValue;
	}
}

$geneNameStandard				= guess_gene_name($geneName, '', 1);
$plotTitle = "{$APP_MESSAGE['Gene Expression Levels']} for {$geneNameStandard}";
$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);

if ($getProjectIDsExistenceInfo['Output_Count'] == 1){
	
	if ($getProjectIDsExistenceInfo['Output_Standard'][0] != ''){
		$plotTitle2 = "Project: <a href='app_project_review.php?id={$getProjectIDsExistenceInfo['ProjectIndexes'][0]}' target='_blank'>{$getProjectIDsExistenceInfo['Output_Standard'][0]}</a>";
	}
}





if (!$_POST['API']){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
		$endTime = microtime(true);
	
		if (true){
			
			$rawDataKey 		= putSQLCacheWithoutKey($results['Export']['Raw'], '', 'prepareGeneExpressionDataByGeneName_Export', 1);
			$plotDataKey		= putSQLCacheWithoutKey($results['Export']['Transformed'], '', 'prepareGeneExpressionDataByGeneName_Export', 1);
			
			$message = "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ' . 
						"<a href='app_gene_expression_common_single_download.php?key={$rawDataKey}&filename=gene_expression_data_{$geneName}_raw.csv' target='_blank'>Raw Data</a>" . 
						" - " . 
						"<a href='app_gene_expression_common_single_download.php?key={$plotDataKey}&filename=gene_expression_data_{$geneName}_plot.csv' target='_blank'>Plot Data</a>" . 
						"</p>";
			
			echo $message;
		}
	
		if (true){	
			unset($researchProjectAPI);
			$researchProjectAPI['Title'] 			= $plotTitle;
			$researchProjectAPI['Type'] 			= "{$APP_MESSAGE['Gene']} Expression Plot";
			$researchProjectAPI['Source_Page'] 		= $pageTitle;
			$researchProjectAPI['URL'] 				= "gene_expressions/{$_POST['URL']}?key={$urlKey}";
			$researchProjectAPI['Base64_Image_ID'] 	= 'plotSection';
			$researchProjectAPI['Parameters'] 		= $urlKey;
		
			include('app_research_project_api_modal.php');
				
			unset($researchProjectAPI);
		}
	
		if ($_SESSION['DEBUG_MODE']){	
			echo "<p class='form-text'>Time Spent: " . round($endTime - $APP_CONFIG['StartTime'], 2) . " seconds.</p>";
		}
		echo "</div>";
	echo "</div>";
}

if (!$_POST['API']){
	echo "<hr/>";
}



if ($tooManyDataPoints){
	$luckyCandidates = array_keys($results['canvasxpress']['y']['smps']);
	shuffle($luckyCandidates);
	$luckyCandidates = array_slice($luckyCandidates, 0, $APP_CONFIG['canvasxpress']['Data_Limit']);
	$luckyCandidates = array_combine($luckyCandidates, $luckyCandidates);
}





unset($canvasxpress);
foreach($results['canvasxpress']['x'] as $plotColumn => $plotColumnDetails){
	
	$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$plotColumn]['Title'];

	if ($plotColumnToDisplay == ''){
		$plotColumnToDisplay = $APP_CONFIG['DB_Dictionary']['Comparisons']['SQL'][$plotColumn]['Title'];
	}
	
	if ($plotColumnToDisplay == ''){
		$plotColumnToDisplay = $APP_CONFIG['APP']['Module']['Gene-Expression-Comparison-Sample-DropDown'][$plotColumn];
	}
	
	if ($plotColumnToDisplay == ''){
		$plotColumnToDisplay = $plotColumn;
	}
	
	$plotColumnToDisplayMap[$plotColumn] = $plotColumnToDisplay;
	
	if ($tooManyDataPoints){
		unset($tempArray);
		foreach($plotColumnDetails as $tempKey => $tempValue){
			if (isset($luckyCandidates[$tempKey])){
				$tempArray[$tempKey] = $tempValue;	
			}
		}
		
		$plotColumnDetails = $tempArray;
	}
	
	if (!isset($maxCount)){
		$currentCount = array_size(array_unique($plotColumnDetails));
		$maxCount['x'] = $currentCount;
	}

	$canvasxpress['x'][] = '"' . $plotColumnToDisplay . '": [' . implode(', ', $plotColumnDetails) . ']';
	
	$plotColumnToDisplayArray[] = $plotColumnToDisplay;
	
	if ($primaryColumn == ''){
		$primaryColumn = $plotColumnToDisplay;
	} elseif ($secdonaryColumn == ''){
		$secdonaryColumn = $plotColumnToDisplay;
	}
	
}



if ($tooManyDataPoints){
	unset($tempArray);
	foreach($results['canvasxpress']['y']['smps'] as $tempKey => $tempValue){
		if (isset($luckyCandidates[$tempKey])){
			$tempArray[$tempKey] = $tempValue;	
		}
	}
	
	$results['canvasxpress']['y']['smps'] = $tempArray;
	
	
}
$canvasxpress['y-smps'] = '[' . implode(",\n", $results['canvasxpress']['y']['smps']) . ']';

if ($tooManyDataPoints){
	unset($tempArray);
	foreach($results['canvasxpress']['y']['data'] as $tempKey => $tempValue){
		if (isset($luckyCandidates[$tempKey])){
			$tempArray[$tempKey] = $tempValue;	
		}
	}
	
	$results['canvasxpress']['y']['data'] = $tempArray;
}
$canvasxpress['y-data'] = '[[' . implode(",\n", $results['canvasxpress']['y']['data']) . ']]';



if (true){
	$class = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
	$height = 800;
	$width	= 1000;
	$smpLabelScaleFontFactor = 0.7;
	$hideSampleLabel = false;
	
	if ($maxCount['x'] <= 30){
		$height = 900;
		$smpLabelScaleFontFactor = 0.7;
		$legendFontDecrease = 1;
	} elseif ($maxCount['x'] <= 60){
		$height = 1200;
		$smpLabelScaleFontFactor = 0.5;
		$legendFontDecrease = 2;
	} elseif ($maxCount['x'] <= 90){
		$height = 1500;
		$smpLabelScaleFontFactor = 0.3;
		$legendFontDecrease = 5;
		$smpLabelInterval = 2;
	} elseif ($maxCount['x'] <= 120){
		$height = 1800;
		$smpLabelScaleFontFactor = 0.5;
		$legendFontDecrease = 8;
		
		
		
		if ($_POST['graphOrientation'] == 'vertical'){
			$smpLabelInterval = 5;
			$useMaxWidth = true;	
		} else {
			$smpLabelInterval = 3;
			$smpLabelScaleFontFactor = 0.7;
		}
	} else {
		$height = 1800;
		$smpLabelScaleFontFactor = 0.5;
		$legendFontDecrease = 8;

		if ($_POST['graphOrientation'] == 'vertical'){
			$smpLabelInterval = 8;
			$useMaxWidth = true;	
		} else {
			$smpLabelInterval = 2;
		}
	}
	
	
	if ($_POST['graphOrientation'] == 'vertical'){
		$temp = $height;
		$height = $width;
		$width = $height;
		
		if ($width <= 1000){
			$width = 1000;	
		}
	}
	

	if ($useMaxWidth){
		$_POST['width'] = intval($_POST['width']);
		if ($_POST['width'] > 0){
			$max_width = $_POST['width'] - 100;
			if ($width < $max_width){
				if (($width/$max_width <= 0.75) && ($width/$max_width >= 0.5)){
					$width = $max_width;	
				}
			} else {
				$width = $max_width;	
			}
		}
	}
	
	
	$_POST['plot_width'] = intval(abs($_POST['plot_width']));
	if ($_POST['plot_width'] >= 100){
		$width = $_POST['plot_width'];	
	}
	
	
	
	
	$_POST['plot_height'] = intval(abs($_POST['plot_height']));
	if ($_POST['plot_height'] >= 100){
		$height = $_POST['plot_height'];	
	}
	

	echo "<div class='row'>";
		echo "<div class='{$class}'>";
			echo "<h3>{$plotTitle}</h3>";
			
			if ($plotTitle2 != ''){
				echo "<h5>{$plotTitle2}</h5>";	
			}
			
			if ($_POST['API']){
				echo "<p>Click <a href='{$_POST['URL']}?key={$urlKey}' target='_blank'>here</a>	to view details.</p>";
			}
			
			if ($tooManyDataPoints){
				echo "<p class='form-text'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . "&nbsp;The following plot contains <strong>{$APP_CONFIG['canvasxpress']['Data_Limit']}</strong> randomly selected data points (out of {$results['DataCount_WihoutConditions_Display']}) from the search result.</p>";	
			}
	
	
			echo "<div style='height:{$height}px; width:{$width}px;'>";
				echo "<canvas id='plotSection' width='{$width}' height='{$height}' xresponsive='true' aspectRatio='1:1'></canvas>";
			echo "</div>";
			
		echo "</div>";
	echo "</div>";
	

}


if (($_POST['searchOption'] == 1) && ($getComparisonIDsExistenceInfo['Output_Count'] > 0)){

	$_POST_org = $_POST;
	$urlKey_org = $urlKey;
	
	unset($_POST);
	$_POST['ComparisonIndex'] = $getComparisonIDsExistenceInfo['ComparisonIndexes'];

	
	if (true){
		$comparisonIndexes = $_POST['ComparisonIndex'];
	
		unset($_POST);
		
		$_POST['Category'] = 'Comparison';
		$_POST['data_source'] = $_POST_org['data_source'];
		$_POST['data_source_private_project_indexes'] = $_POST_org['data_source_private_project_indexes'];
		
		
		$_POST['Field_1'] 		= 'ComparisonIndex';
		$_POST['Operator_1'] 	= 5;
		$_POST['Value_1'] 		= implode(',', $comparisonIndexes);
		$_POST['rowCount']		= 1;
		$_POST['API']			= 1;
		$_POST['URL']			= '';
		$_POST['bookmark']		= 0;
		$_POST['Simple']		= 1;
		
		
		
		include('app_record_browse_component_exe.php');	
		
	}
	$_POST = $_POST_org;
	$urlKey = $urlKey_org;

} elseif (($_POST['searchOption'] == 2) && ($getSampleIDsExistenceInfo['Output_Count'] > 0)){

	$_POST_org = $_POST;
	$urlKey_org = $urlKey;

	unset($_POST);
	$_POST['SampleIndex'] = $getSampleIDsExistenceInfo['SampleIndexes'];

	
	if (true){
		$sampleIndexes = $_POST['SampleIndex'];
	
		unset($_POST);
		
		$_POST['Category'] = 'Sample';
		$_POST['data_source'] = $_POST_org['data_source'];
		$_POST['data_source_private_project_indexes'] = $_POST_org['data_source_private_project_indexes'];
		
		
		$_POST['Field_1'] 		= 'SampleIndex';
		$_POST['Operator_1'] 	= 5;
		$_POST['Value_1'] 		= implode(',', $sampleIndexes);
		$_POST['rowCount']		= 1;
		$_POST['API']			= 1;
		$_POST['URL']			= '';
		$_POST['bookmark']		= 0;
		$_POST['Simple']		= 1;
		
		
		
		include('app_record_browse_component_exe.php');	
		
	}
	$_POST = $_POST_org;
	$urlKey = $urlKey_org;
}


$_POST['JSON'] = unserialize(base64_decode($_POST['JSON']));

?>


<script type="text/javascript">

$(document).ready(function(){
	$('.CanvasXpressContainer').bind('contextmenu', function(e) {
		return false;
	}); 
	
	
	<?php 
	
		if (($_POST['JSON'] != '') && ($_POST['JSON_Choice'])){
			
			$JSON_Results = validateCanvasXpressJSON($_POST['JSON']);
			
			if ($JSON_Results['Result']){
				echo "var afterRenderObject = [];\n";
				echo implode("\n", $JSON_Results['Preview']);
			}
		}

		
	?>
	
	var data = {
            "x" : {
				<?php echo implode(",\n", $canvasxpress['x']); ?>
            },
            "y" : {
              "vars" : ['expression'],
              "smps" : <?php echo $canvasxpress['y-smps']; ?>,
              "data" : <?php echo $canvasxpress['y-data']; ?>
            }
		};
		
	var config = {

			 "graphType": 					"Boxplot",
			 "jitter": 						true,
			 
			 <?php if ($secdonaryColumn != ''){ ?>
			 	<?php if ($_POST['colorBy'] == ''){ ?>
			 		"colorBy": 					"<?php echo $secdonaryColumn; ?>",
				<?php } ?>
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
			 
			 "title": 						"<?php echo $geneNameStandard; ?>",
			 'xAxisTitle':					'<?php echo $formula; ?>',
			 <?php
			 /*
			 'axisTitleScaleFontFactor': 	0.5,
			 'axisTickFontSize':			12,
			 'axisTickScaleFontFactor': 	0.5,
			 
			 "citation": 					"",
			 'citationScaleFontFactor': 	0.7,

			 
			 "xAxisTitle": 					"",	
			 "titleFontSize":				25,
			
			 
			 'smpLabelScaleFontFactor': 	<?php echo $smpLabelScaleFontFactor; ?>,
			 'varLabelScaleFontFactor':		<?php echo $varLabelScaleFontFactor; ?>,
			 'titleScaleFontFactor': 		0.7,
			 'subtitleScaleFontFactor': 	0.7,

			 'legendScaleFontFactor': 		0.6,
			 
			 'nodeScaleFontFactor': 		0.7,
			 
			 'sampleSeparationFactor': 		0.7,
			 'variableSeparationFactor': 	0.7,
			 'widthFactor': 				0.7,
*/
			 ?>
			 'printType':					'window',
			 
			 
			 <?php if ($_POST['graphOrientation'] == 'vertical'){ ?>
			 'graphOrientation':			'vertical',
			 <?php } ?>
			 
			 
			 <?php if ($smpLabelScaleFontFactor > 0){ ?>
			 'smpLabelScaleFontFactor': 	<?php echo $smpLabelScaleFontFactor; ?>,
			 <?php } ?>
			 
			 <?php if ($varLabelScaleFontFactor > 0){ ?>
			 'varLabelScaleFontFactor': 	<?php echo $varLabelScaleFontFactor; ?>,
			 <?php } ?>
			 
			 <?php if ($smpLabelInterval > 0){ ?>
			 'smpLabelInterval': 	<?php echo $smpLabelInterval; ?>,
			 <?php } ?>
			 
			 
			 
		};
		
	var targetID = 'plotSection';


	var plotObj = new CanvasXpress({
		renderTo: targetID,
        data: data,
        config: config,
		
		<?php if ($JSON_Results['Result']){ ?>
        afterRender: afterRenderObject,
		<?php } ?>
	});	
		
		<?php /*	
		{
			click: function(o, e, t) { if ((typeof o != 'undefined') && (typeof o.y != 'undefined') && (typeof o.y['smps'] != 'undefined')){ var sampleName = o.y['smps']; var temp = sampleName.split('|'); sampleName = temp[0]; var geneValue = parseFloat(o.y['data']).toFixed(4); var title = o.y['vars']; var content = '<div><strong>Sample</strong>: ' + sampleName + '</div>' + '<div><strong>Expression</strong>: ' + geneValue + '</div>' + '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=sample&inputName=' + sampleName + '\' target=\'_blank\'>View Sample</a></div>' + '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=project&inputName=' + sampleName + '\' target=\'_blank\'>View Project</a></div>'; t.showInfoSpan(e, content); }},
					
									
			mousemove: function(o, e, t) { if ((typeof o != 'undefined') && (typeof o.y != 'undefined') && (typeof o.y['smps'] != 'undefined')){ var sampleName = o.y['smps']; var temp = sampleName.split('|'); sampleName = temp[0]; var geneValue = parseFloat(o.y['data']).toFixed(4); var title = o.y['vars']; var content = '<div><strong>Sample</strong>: ' + sampleName + '</div>' + '<div><strong>Expression</strong>: ' + geneValue + '</div>' + '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=sample&inputName=' + sampleName + '\' target=\'_blank\'>View Sample</a></div>' + '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=project&inputName=' + sampleName + '\' target=\'_blank\'>View Project</a></div>'; t.showInfoSpan(e, content); }},

			
			mouseout: function(o, e, t) {},		
			
					
			dblclick: function(o, e, t) { if ((typeof o != 'undefined') && (typeof o.y != 'undefined') && (typeof o.y['smps'] != 'undefined')){ var sampleName = o.y['smps']; var temp = sampleName.split('|'); sampleName = temp[0]; var geneValue = parseFloat(o.y['data']).toFixed(4); var title = o.y['vars']; var content = '<div><strong>Sample</strong>: ' + sampleName + '</div>' + '<div><strong>Expression</strong>: ' + geneValue + '</div>' + '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=sample&inputName=' + sampleName + '\' target=\'_blank\'>View Sample</a></div>' + '<div><a href=\'app_record_redirector.php?inputType=sample&outputType=project&inputName=' + sampleName + '\' target=\'_blank\'>View Project</a></div>'; t.showInfoSpan(e, content); }},
		}
		*/ ?>
	
	plotObj.sizes = plotObj.sizes.map(function(x){
			return Number(x * 0.5).toFixed(1);
		});
	CanvasXpress.stack["plotSection"]["config"]["sizes"] = plotObj.sizes.map(function(x){
		return Number(x * 0.5).toFixed(1);
	});
		
	
	<?php 
		if ($_POST['groupSamples'] == -1){
			//Do nothing
		} elseif ($_POST['groupSamples'] == ''){
			$groupSamples = $primaryColumn;
	?>
		plotObj.groupSamples(["<?php echo $primaryColumn; ?>"]);
	<?php 
		} else {
			$groupSamples = $plotColumnToDisplayMap[$_POST['groupSamples']];
	?>
		plotObj.groupSamples(["<?php echo $plotColumnToDisplayMap[$_POST['groupSamples']]; ?>"]);
	<?php } ?>



	
	
	<?php 
	if ($secdonaryColumn != ''){
		for ($i = 1; $i <= $legendFontDecrease; $i++){
	?>
		plotObj.setFontAttributeSize('legendScaleFontFactor', 'decrease');
	<?php 
		}
	}
	?>
	
	<?php if (($_POST['colorBy'] != '') && ($_POST['colorBy'] != '-1')){ ?>
		plotObj.changeAttribute('colorBy', '<?php echo $plotColumnToDisplayMap[$_POST['colorBy']]; ?>');
	<?php } ?>
	
	
	<?php if (($_POST['shapeBy'] != '') && ($_POST['shapeBy'] != '-1')){ ?>
		plotObj.changeAttribute('shapeBy', '<?php echo $plotColumnToDisplayMap[$_POST['shapeBy']]; ?>');
	<?php } ?>
	

	<?php if ($hideSampleLabel){ ?>
		plotObj.toggleAttribute('showSampleNames');
	<?php } ?>
	
	
	<?php if ($_POST['transform']){ ?>
		plotObj.transform('log2');
    <?php } ?>
	
	<?php if ($_POST['segregate'] != ''){ ?>
		plotObj.segregate('Samples', ['<?php echo $plotColumnToDisplayMap[$_POST['segregate']]; ?>'], false, true);
	<?php } ?>
	
	<?php 
		if ($_POST['sortBy'] == ''){
	?>
		plotObj.sortSamplesByCategory(['<?php echo $groupSamples; ?>'], null);
	<?php } elseif ($_POST['sortBy'] != '-1'){ ?>
		plotObj.sortSamplesByCategory(['<?php echo $plotColumnToDisplayMap[$_POST['sortBy']]; ?>'], null);
	<?php } ?>
	
	
	<?php if (!$_POST['API'] && $urlKey != ''){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "<?php echo "{$_POST['URL']}?key={$urlKey}"; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "", URL);
	}
	<?php } ?>
	
	
	<?php
		if ($_POST['JSCode'] != ''){
			echo $_POST['JSCode'];
		}
	?>

	
	
	

});

</script>


