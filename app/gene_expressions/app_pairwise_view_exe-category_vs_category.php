<?php

include_once('config_init.php');

$results = preparePairwiseData_Category_vs_Category($getSampleIDsExistenceInfo['SampleIndexes'], 
													$x_category_data, 
													$_POST['x_axis_sample_attribute'],
													$_POST['y_axis_sample_attribute']);

if ($results['Sample_Count'] <= 0){
	$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The search result does not contain any data. Please refine your search conditions and try again.";
	echo getAlerts($message, 'danger');
	exit();
	
} else {
	
	echo "<div class='row'>";
		echo "<div class='col-lg-12'>";
			echo "<h3>{$results['Summary']['Title']}</h3>";
			
			echo "<p>The search result contains {$results['Sample_Count_Formatted']} samples.</p>";
			
		echo "</div>";
		
	echo "</div>";	
}


if (true){	
	unset($researchProjectAPI);
	$researchProjectAPI['Title'] 			= $results['Summary']['Title'];
	$researchProjectAPI['Type'] 			= 'Pairwise View of Samples';
	$researchProjectAPI['Source_Page'] 		= 'Pairwise View of Samples';
	$researchProjectAPI['URL'] 				= "gene_expressions/app_pairwise_view.php?key={$urlKey}";
	$researchProjectAPI['Base64_Image_ID'] 	= '';

	$researchProjectAPI['Parameters'] 		= $urlKey;
	include('app_research_project_api_modal.php');
	unset($researchProjectAPI);
}

echo "<br/>";

if (true){
	$tableDataKey 	= putSQLCacheWithoutKey($results['Export']['Table'], '', '', 1);
	$rawDataKey		= putSQLCacheWithoutKey($results['Export']['Sample'], '', '', 1);
	
	$message = "<p>" . printFontAwesomeIcon('fas fa-download') . ' Download: ' . 
				"<a href='app_common_table_download.php?key={$tableDataKey}&filename=Table.csv' target='_blank'>Table Data</a>" . 
				" - " . 
				"<a href='app_common_table_download.php?key={$rawDataKey}&filename=Sample.csv' target='_blank'>Sample Data</a>" . 
				"</p>";
	
	
	
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


if (array_size($results['valuesY']) > 25){
	$showTableInfo = 1;	
} else {
	$showTableInfo = 0;	
}


if (true){
	if ($results['Summary']['min'] != $results['Summary']['max']){
		echo "<div class='row'>";
			echo "<div class='col-12'>";
				echo "<p># of Sample IDs</p>";
			echo "</div>";
		echo "</div>";
	

		echo "<div class='row'>";
			echo "<div class='col-12'>";
				echo "<table id='legend-table'>";
					echo "<tr>";
						echo "<td><div class='legend-box'>{$results['Summary']['min']}</div></td>";

						
						foreach($APP_CONFIG['APP']['Heatmap_Colors'] as $tempKey => $currentColor){
							echo "<td><div class='legend-box' style='background-color:{$currentColor['Background']};'>&nbsp;</div></td>";
						}
						echo "<td><div class='legend-box' style='padding-left:5px;'>" . number_format($results['Summary']['max']) . "</div></td>";
					echo "</tr>";
				echo "</table>";
	
			echo "</div>";
		echo "</div>";

		echo "<br/>";
	}
	

		
	

	echo "<div id='resultTable'>";
		
		echo "<div class='table-responsive'>";
			$tableClass = getTableClass(1, 1);
			
			echo "<table class='{$tableClass} display'>";
			
				echo "<thead>";
					echo "<tr>";
						echo "<th>
								<p><strong>Vertical: </strong>{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['y_axis_sample_attribute']]['Title']}</p>
								<p><strong>Horizontal: </strong>{$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['x_axis_sample_attribute']]['Title']}</p>
								<br/>
							</th>";
						
						echo "<th><div class='tableHeader'>Total # of Samples</div></th>";
						
						foreach($results['valuesX'] as $tempKey => $tempValue){
							echo "<th><div class='tableHeader'>{$tempValue}</div></th>";
						}
						
						
					echo "</tr>";
				echo "</thead>";
				
				echo "<tbody>";
				
					foreach($results['valuesY'] as $tempKeyY => $tempValueY){
						
						echo "<tr>";
						
							
							echo "<td><div class='nowrap'><strong>{$tempValueY}</strong></div></td>";
							
							echo "<td><div class='text-center'>" . number_format($results['RowCount'][$tempKeyY]) . "</div></td>";
							
							foreach($results['valuesX'] as $tempKeyX => $tempValueX){
								
								$currentValue			= array_size($results['Histogram'][$tempValueY][$tempValueX]['SampleIndex']);
								$getHeatmapColors 		= getHeatmapColors($results['Summary']['min'], $results['Summary']['max'], $currentValue);
								$currentValueFormatted 	= number_format($currentValue);
								
								$foreground				= $getHeatmapColors['Foreground'];
								$background				= $getHeatmapColors['Background'];
								
								echo "<td>";
									echo"<div class='text-center' style='color: {$foreground}; background: {$background};'>{$currentValueFormatted}</div>";
								echo"</td>";
							}
							
						
						echo "</tr>";
						
					}
				echo "</tbody>";
				
				if ($showTableInfo){
				echo "<tfoot>";
					echo "<tr>";
					
						echo "<th class='nowrap'><strong>Vertical</strong>: {$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['y_axis_sample_attribute']]['Title']}</strong></th>";
						
						$colspan = array_size($results['valuesX']);
						echo "<th colspan='{$colspan}'><strong>Horizontal</strong>: {$APP_CONFIG['DB_Dictionary']['Samples']['SQL'][$_POST['x_axis_sample_attribute']]['Title']}</th>";
						
						
					echo "</tr>";
					
					
					
					echo "</tr>";
				
				
				echo "</tfoot>";
				}
				
				
				
			
			echo "</table>";
		
		
		
		echo "</div>";

		
	echo "</div>";
	
	
	
	$cellWidth = (1500-200)/$results['Summary']['x_count'];
	$cellWidth = intval($cellWidth);
	
	if ($cellWidth > 150){
		$cellWidth = 150;	
	}
	
	if ($cellWidth < 50){
		$cellWidth = 50;	
	}

}

?>
<script type="text/javascript">
$(document).ready(function(){

	$('#resultTable table').DataTable({
		"pageLength": 100
    });
	
	

});


</script>

<style>
#legend-table td{
	padding:0px;
}

.legend-box{
	text-align:center;
	height:30px;
	width:30px;	
}

#resultTable td{
	padding:1px !important;	
}

#resultTable {
	font-size:13px;	
}


#resultTable thead th{
	font-weight: 400;
	font-size: 12px;
	line-height: 11px;
	height: 200px;
	background-position: center bottom 5px;
	padding: 5px;
	border: none;
	width: 7%;
	min-width: 30px;
	
}

#resultTable .tableHeader{
	font-weight:bold;
	display: block;
	width: <?php echo $cellWidth; ?>px;
	margin-left: 50%;
	padding-left:20px;
	-webkit-transform-origin: 0 0;
	-moz-transform-origin: 0 0;
	-ms-transform-origin: 0 0;
	-o-transform-origin: 0 0;
	transform-origin: 0 0;
	-webkit-transform: rotate(-60deg) translateY(-50%);
	-moz-transform: rotate(-60deg) translateY(-50%);
	-ms-transform: rotate(-60deg) translateY(-50%);
	-o-transform: rotate(-60deg) translateY(-50%);
	transform: rotate(-60deg) translateY(-50%);	
}

<?php if (!$showTableInfo){ ?>
.dataTables_length{
	display:none;
}

.dataTables_info{
	display:none;
}

.dataTables_paginate {
	display:none;
}

<?php } ?>


</style>