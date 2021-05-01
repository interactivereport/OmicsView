<?php
include_once('config_init.php');

$resultFromCache 	= getSQLCache($_GET['key']);

if ($resultFromCache !== false){
	//Found	
} else {
	echo "The record is not available. Please verify your link and try again.";
	exit();	
}

$geneSourceIndex	= intval($_GET['source']);
$geneTargetIndex	= intval($_GET['target']);

$sourceValues		= $resultFromCache['geneExpressionValueIndex'][$geneSourceIndex];
$targetValues		= $resultFromCache['geneExpressionValueIndex'][$geneTargetIndex];

if ($resultFromCache['Summary']['otherOptions']['method'] == 1){
	$useSpearman = 1;
	$correlationName = 'Spearman Correlation';
	$getCorrelationCoefficient_Spearman_Details = getCorrelationCoefficient_Spearman_Details($sourceValues, $targetValues);
} else {
	$correlationName = 'Pearson Correlation';
	$useSpearman = 0;
}


cleanTwoNumericArrays($sourceValues, $targetValues);
$dataCount			= array_size($sourceValues);
if (!$useSpearman){
	$linearRegression 	= getLinearRegression($sourceValues, $targetValues);
	$slope				= round($linearRegression['Slope'], 5);
	$contant			= round($linearRegression['Constant'], 5);
} else {
	$linearRegression 	= getLinearRegression($getCorrelationCoefficient_Spearman_Details['Output'][1], $getCorrelationCoefficient_Spearman_Details['Output'][2]);
	$slope				= round($linearRegression['Slope'], 5);
	$contant			= round($linearRegression['Constant'], 5);
}

if ($contant > 0){
	$equation	= "<i>y</i> = {$slope}<i>x</i> + {$contant}";
} elseif ($contant < 0){
	$contant	= abs($contant);
	$equation	= "<i>y</i> = {$slope}<i>x</i> - {$contant}";
} else {
	$equation	= "<i>y</i> = {$slope}<i>x</i>";
}

$sourceName			= $resultFromCache['Gene_Source'][$geneSourceIndex];
$targetName			= $resultFromCache['Gene_Target'][$geneTargetIndex];

$xAxisName			= $sourceName;
$yAxisName			= $targetName;

if (true){
	$xAxisNameHTML	= "Log<sub>2</sub>(FC of {$xAxisName})";
	$yAxisNameHTML	= "Log<sub>2</sub>(FC of {$yAxisName})";
	
	$xAxisName 		= "Log2(FC of {$xAxisName})";
	$yAxisName 		= "Log2(FC of {$yAxisName})";
	$correlation 	= round($resultFromCache['Correlation_Coefficient_Raw'][$geneSourceIndex][$geneTargetIndex], 5);
}
$rSquare = round($correlation * $correlation, 5);


if ($useSpearman){
	$tableContent['Header'][] = 'Comparison ID';
	$tableContent['Header'][] = "<span class='nowrap'>{$xAxisNameHTML}</span>";
	$tableContent['Header'][] = "<span class='nowrap'>{$yAxisNameHTML}</span>";
	$tableContent['Header'][] = "<span class='nowrap'>{$xAxisNameHTML}: Rank</span>";
	$tableContent['Header'][] = "<span class='nowrap'>{$yAxisNameHTML}: Rank</span>";
	
} else {
	$tableContent['Header'][] = 'Comparison ID';
	$tableContent['Header'][] = "{$xAxisNameHTML}";
	$tableContent['Header'][] = "{$yAxisNameHTML}";	
}


$i = -1;
foreach($sourceValues as $comparisonIndex => $sourceValue){
	$i++;
	$targetValue = $targetValues[$comparisonIndex];
	if ($useSpearman){
		$targetValue = $targetValues[$comparisonIndex];
		
		$varsString[] = '"' . $resultFromCache['Summary']['ComparisonIndex'][$comparisonIndex] . '"';
		$dataString[] = '[' . $getCorrelationCoefficient_Spearman_Details['Output'][1][$i] . ',' . $getCorrelationCoefficient_Spearman_Details['Output'][2][$i] . ']';
	
		$tableContent['Body'][$comparisonIndex]['Value'][] = "<a href='app_comparison_review.php?ID={$comparisonIndex}' target='_blank'>{$resultFromCache['Summary']['ComparisonIndex'][$comparisonIndex]}</a>";
		$tableContent['Body'][$comparisonIndex]['Value'][] = round($sourceValue, 5);
		$tableContent['Body'][$comparisonIndex]['Value'][] = round($targetValue, 5);

		$tableContent['Body'][$comparisonIndex]['Value'][] = $getCorrelationCoefficient_Spearman_Details['Output'][1][$i];
		$tableContent['Body'][$comparisonIndex]['Value'][] = $getCorrelationCoefficient_Spearman_Details['Output'][2][$i];
	} else {
		
		
		$varsString[] = '"' . $resultFromCache['Summary']['ComparisonIndex'][$comparisonIndex] . '"';
		$dataString[] = '[' . round($sourceValue, 5) . ',' . round($targetValue, 5) . ']';
	
		$tableContent['Body'][$comparisonIndex]['Value'][] = "<a href='app_comparison_review.php?ID={$comparisonIndex}' target='_blank'>{$resultFromCache['Summary']['ComparisonIndex'][$comparisonIndex]}</a>";
		$tableContent['Body'][$comparisonIndex]['Value'][] = round($sourceValue, 5);
		$tableContent['Body'][$comparisonIndex]['Value'][] = round($targetValue, 5);
	}
	
}

$varsString = implode(',', $varsString);
$dataString = implode(',', $dataString);

if (array_size($sourceValues) <= 0){
	echo "The record is not available. Please verify your link and try again.";
	exit();	
}

if (array_size($targetValues) <= 0){
	echo "The record is not available. Please verify your link and try again.";
	exit();	
}

echo "<div class='row' id='rawData_{$geneSourceIndex}_{$geneTargetIndex}_section'>";
	echo "<div class='col-12' style='width:690px; height:690px; padding:10px;'>";
		echo "<canvas id='plotSection' width='680' height='680' xresponsive='true' aspectRatio='1:1'></canvas>";
	echo "</div>";
	

	echo "<div class='col-12'>";
		echo "<p class='help-block'>
			<strong>{$correlationName}:</strong> {$correlation}
			&nbsp;&nbsp;&nbsp;
			<strong>R<sup>2</sup>:</strong> {$rSquare}
			&nbsp;&nbsp;&nbsp;
			<strong>Linear Regression:</strong> {$equation}
			&nbsp;&nbsp;&nbsp;
			<strong># of Data Point:</strong> {$dataCount}
			
		</p>";
		
		echo "<p class='help-block'>
				<a href='javascript:void(0);' id='rawData_{$geneSourceIndex}_{$geneTargetIndex}_trigger' status='0'>" . 
					"<span id='rawData_{$geneSourceIndex}_{$geneTargetIndex}_show'>" . printFontAwesomeIcon('fas fa-expand-alt') . "Show</span>
					 <span id='rawData_{$geneSourceIndex}_{$geneTargetIndex}_hide' class='startHidden'>" . printFontAwesomeIcon('fas fa-compress-alt') . "Hide</span>
					 Data
					</a>
			</p>
		</div>";
		
		
	echo "<div class='col-12 startHidden' id='rawData_{$geneSourceIndex}_{$geneTargetIndex}_table'>";
		echo "<hr/>";
		echo printTableHTML($tableContent, 1, 1, 0, 'col-lg-12 col-sm-12');
	echo "</div>";
		
		
echo "</div>";

?>
<style>
.modal-dialog{
	max-width:805px;
}

.startHidden{
	display:none;	
}
</style>

<script type="text/javascript">

$(document).ready(function(){
	
	$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_section"; ?>').on('click', '#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_trigger"; ?>', function(){
		var currentStatus = parseInt($(this).attr('status'));
		

		if (currentStatus == 1){
			$(this).attr('status', '0');
			
			$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_table"; ?>').hide();
			$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_show"; ?>').show();
			$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_hide"; ?>').hide();
			
			
		} else {
			$(this).attr('status', '1');
			
			
			$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_table"; ?>').show();
			$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_show"; ?>').hide();
			$('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_hide"; ?>').show();

			$('#plotModal .modal-body').animate({
				scrollTop: $('#<?php echo "rawData_{$geneSourceIndex}_{$geneTargetIndex}_table"; ?>').offset().top
						}, 10);
		}
	});
	

	var plotObj = new CanvasXpress('plotSection',
		  {"y" : {
              "smps" : [ "<?php echo $xAxisName; ?>", "<?php echo $yAxisName; ?>" ],
              "vars" : [ <?php echo $varsString; ?> ],
              "data" : [ <?php echo $dataString; ?> ]
            },
          },
          {"backgroundType": "window",
           "backgroundWindow": "rgb(238,238,238)",

           "colors": ["rgba(64,64,64,0.5)"],
		   
           "decorationsBackgroundColor": "rgb(238,238,238)",
           "decorationsBoxColor": "rgb(0,0,0)",
		   
		   <?php if ($slope > 0){ ?>
           "decorationsPosition": "bottomRight",
		   <?php } else { ?>
           "decorationsPosition": "bottomLeft",
		   <?php } ?>
		   
           "graphType": "Scatter2D",
           "legendInside": true,
           "plotBox": false,
           "showDecorations": false,
           "xAxis": ["<?php echo $xAxisName; ?>"],
           "xAxisTickColor": "rgb(255,255,255)",
           "yAxis": ["<?php echo $yAxisName; ?>"],
           "yAxisTickColor": "rgb(255,255,255)",
		   
          }
	);
	plotObj.sizes = plotObj.sizes.map(function(x){
			return Number(x * 0.5).toFixed(1);
		});
	CanvasXpress.stack["plotSection"]["config"]["sizes"] = plotObj.sizes.map(function(x) { return Number(x * 0.5).toFixed(1); });
	
	
	//plotObj.addRegressionLine('red', false);
	plotObj.addRegressionLine(false, 'red');
	plotObj.toggleAttribute('showConfidenceIntervals');

});

</script>
