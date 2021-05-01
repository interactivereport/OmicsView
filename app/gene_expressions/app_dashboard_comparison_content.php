<?php

if ($_GET['comparisonIndex'] != ''){
	$comparisonIndex = getSQLCache($_GET['comparisonIndex']);
}

$getUserSettings 				= getUserSettings();
$geneCount						= getTableCount($APP_CONFIG['APP']['List_Category']['Gene']['Table']);
$category						= 'Comparison';
$compairsonCount 				= getDashboardComparisonDataCount($comparisonIndex);
$compairsonCountFormatted		= number_format($compairsonCount);

if (array_size($comparisonIndex) <= 0){
	
	$text = 'Comparison Dashboard';
	echo "<h4>{$text}</h4>";
	
	if (has_public_comparison_data()){
		$internal_data_get_accessible_project = internal_data_get_accessible_project();
		if (array_size($internal_data_get_accessible_project) > 0){
			if ($getUserSettings['Data_Source'] == 'all'){
				$text = "Data Source: Omicsoft data and internal data (<a href='app_user_preferences.php' target='_blank'>Change Preferences</a>).";
			} elseif ($getUserSettings['Data_Source'] == 'public'){
				$text = "Data Source: Omicsoft data only (<a href='app_user_preferences.php' target='_blank'>Change Preferences</a>).";
			} else {
				$text = "Data Source: Internal data only (<a href='app_user_preferences.php' target='_blank'>Change Preferences</a>).";
			}
			 
			echo "<p class='form-text'>{$text}</p>";
		}
	} else {
		echo "<br/>";	
	}
	
	
	$sampleCount 	= getDashboardSampleDataCount();
	$projectCount	= getDashboardProjectDataCount();
	
} else {
	$text = "Summary of Selected Comparisons ({$compairsonCountFormatted})";	
	echo "<h3>{$text}</h3>";
	echo "<p>You are currently viewing a subset of comparisons. Please click <a href='app_dashboard_comparison.php'>here</a> to view all comparisons.</p>";

	$sampleCount 	= array_size(comparison_index_to_sample_id($comparisonIndex));
	$projectCount	= array_size(comparison_index_to_project_name($comparisonIndex));
}



$allStatistics = array();
unset($currentRowIndex, $currentColumnIndex);
			
$currentRowIndex = 1;
$currentColumnIndex = 1;
$allStatistics[$currentRowIndex][$currentColumnIndex]['Table'] 	= $APP_CONFIG['APP']['List_Category']['Project']['Table'];
if ($projectCount <= 1){
	$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Project']['Name'];
} else {
	$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Project']['Names'];
}
$allStatistics[$currentRowIndex][$currentColumnIndex]['Color'] 	= '#23C6C8';
$allStatistics[$currentRowIndex][$currentColumnIndex]['URL'] 	= $APP_CONFIG['APP']['List_Category']['Project']['Review_URL'];
$allStatistics[$currentRowIndex][$currentColumnIndex]['Count'] 	= $projectCount;



$currentColumnIndex++;
$allStatistics[$currentRowIndex][$currentColumnIndex]['Table'] 	= $APP_CONFIG['APP']['List_Category']['Comparison']['Table'];
if ($compairsonCount <= 1){
	$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Comparison']['Name'];
} else {
	$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Comparison']['Names'];
}
$allStatistics[$currentRowIndex][$currentColumnIndex]['Color'] 	= '#1C84C6';
$allStatistics[$currentRowIndex][$currentColumnIndex]['URL'] 	= $APP_CONFIG['APP']['List_Category']['Comparison']['Review_URL'];
$allStatistics[$currentRowIndex][$currentColumnIndex]['Count'] 	= $compairsonCount;




$currentRowIndex++;
$currentColumnIndex = 1;
$currentColumnIndex++;
$allStatistics[$currentRowIndex][$currentColumnIndex]['Table'] 	= $APP_CONFIG['APP']['List_Category']['Gene']['Table'];
$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Gene']['Names'];
$allStatistics[$currentRowIndex][$currentColumnIndex]['Color'] 	= '#1AB394';
$allStatistics[$currentRowIndex][$currentColumnIndex]['URL'] 	= $APP_CONFIG['APP']['List_Category']['Gene']['Review_URL'];
$allStatistics[$currentRowIndex][$currentColumnIndex]['Count'] 	= $geneCount;



$currentColumnIndex++;
$allStatistics[$currentRowIndex][$currentColumnIndex]['Table'] 	= $APP_CONFIG['APP']['List_Category']['Sample']['Table'];
if ($sampleCount <= 1){
	$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Sample']['Name'];
} else {
	$allStatistics[$currentRowIndex][$currentColumnIndex]['Title'] 	= $APP_CONFIG['APP']['List_Category']['Sample']['Names'];
}
$allStatistics[$currentRowIndex][$currentColumnIndex]['Color'] 	= '#FFA555';
$allStatistics[$currentRowIndex][$currentColumnIndex]['URL'] 	= $APP_CONFIG['APP']['List_Category']['Sample']['Review_URL'];
$allStatistics[$currentRowIndex][$currentColumnIndex]['Count'] 	= $sampleCount;



$allCharts = $APP_CONFIG['Dashboard']['Charts'];


echo "<div style='margin-left:10px;' id='statisticsSection'>";

echo "<div class='row'>";
	echo "<div class='col-lg-6 col-md-12 col-sm-12 col-xs-12'>";
		foreach($allStatistics as $currentRowIndex => $tempValue1){
			echo "<div class='row'>";
				foreach($tempValue1 as $currentColumnIndex => $tempValue2){
					
					$currentTitle	= $allStatistics[$currentRowIndex][$currentColumnIndex]['Title'];
					$currentCount 	= number_format($allStatistics[$currentRowIndex][$currentColumnIndex]['Count']);
					$currentColor	= $allStatistics[$currentRowIndex][$currentColumnIndex]['Color'];
					$currentURL		= $allStatistics[$currentRowIndex][$currentColumnIndex]['URL'];
					
					if ($currentCount == 0){
						$currentURL = '';	
					}
					
					echo "<div class='col-xs-6'>";
						if ($currentURL != ''){
							echo "<a href='{$currentURL}'>";
						}
						
						echo "<div style='background-color:{$currentColor};' class='statisticCell'>";
							echo "<p>{$currentCount}</p>";
							echo "<h4 class='nowrap'>{$currentTitle}</h4>";
						echo "</div>";
						
						if ($currentURL != ''){
							echo "</a>";
						}
					echo "</div>";
				}
			echo "</div>";
		}
		
		echo "<br/>";
		echo "<br/>";
		echo "<br/>";
		
		echo "<div class='row'>";
			echo "<table>";
				echo "<tr>";
					echo "<td>";
						echo "<div id='selectedCountSection'></div>";	
					echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>";
						echo "<div><a href='app_user_preferences.php' target='_blank'>" . printFontAwesomeIcon('fas fa-cog') . " Display Preferences</a></div>";				
					echo "</td>";
				echo "</tr>";
				
				echo "<tr>";
					echo "<td>";
						echo "Sort By: &nbsp;";
						echo "&nbsp; <input type='radio' class='sortMethod' name='sortMethod' id='sortByNumber' value='name' checked /> Counts";
						echo "&nbsp;";
						echo "&nbsp; <input type='radio' class='sortMethod' name='sortMethod' id='sortByName' value='name' /> Alphabets";
					echo "</td>";
				echo "</tr>";
				


				
				/*
				echo "<tr>";
					echo "<td>";
						echo "&nbsp; &nbsp;<input type='checkbox' class='data_source' value='public' checked /> Omicsoft Data";						
						echo "&nbsp; &nbsp;<input type='checkbox' class='data_source' value='private' /> Internal Data";
					echo "</td>";
				echo "</tr>";
				*/
			echo "</table>";


			
			

		
		echo "</div>";
		
	echo "</div>";
	
	
	if (true){
		$currentCandidate 	= $allCharts[0];
		echo "<div class='col-lg-6 col-md-12 col-sm-12 col-xs-12'>";
			echo "<div class='card'>";
				echo "<div class='card-header'>";
					echo "<span class='card-title'><strong>{$currentCandidate['Title']}</strong></span>";
					echo "<span class='selectedCountIndividualSection startHidden' style='padding-left:10px;'>(Selected: <span class='selectedCount'></span> out of {$compairsonCountFormatted})</span>";
					echo "<a id='resetTrigger_{$currentCandidate['Column']}' href='javascript:void(0);' class='pull-right'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
				echo "</div>";
				
				echo "<div class='card-block'>";
					echo "<div id='chartSection_{$currentCandidate['Column']}'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
				echo "</div>";
			
			echo "</div>";
			
		echo "</div>";
	}

echo "</div>";


echo "<hr/>";




echo "<div id='dashboardSection'  style='padding-left:5px;'>";

	echo "<div class='row'>";
	
		if (true){
			$currentCandidate 	= $allCharts[1];
			echo "<div class='col-lg-6 col-md-12 col-sm-12 col-xs-12'>";
				echo "<div class='card'>";
					echo "<div class='card-header'>";
						echo "<span class='card-title'><strong>{$currentCandidate['Title']}</strong></span>";
						echo "<span class='selectedCountIndividualSection startHidden' style='padding-left:10px;'>(Selected: <span class='selectedCount'></span> out of {$compairsonCountFormatted})</span>";
						echo "<a id='resetTrigger_{$currentCandidate['Column']}' href='javascript:void(0);' class='pull-right'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
					echo "</div>";
					
					echo "<div class='card-block'>";
						echo "<div id='chartSection_{$currentCandidate['Column']}'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
					echo "</div>";
				
				echo "</div>";
				
			echo "</div>";
		}
		
		
		
		
		if (true){
			$currentCandidate 	= $allCharts[2];
			echo "<div class='col-lg-6 col-md-12 col-sm-12 col-xs-12'>";
				echo "<div class='card'>";
					echo "<div class='card-header'>";
						echo "<span class='card-title'><strong>{$currentCandidate['Title']}</strong></span>";
						echo "<span class='selectedCountIndividualSection startHidden' style='padding-left:10px;'>(Selected: <span class='selectedCount'></span> out of {$compairsonCountFormatted})</span>";
						echo "<a id='resetTrigger_{$currentCandidate['Column']}' href='javascript:void(0);' class='pull-right'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
					echo "</div>";
					
					echo "<div class='card-block'>";
						echo "<div id='chartSection_{$currentCandidate['Column']}'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
					echo "</div>";
				
				echo "</div>";
				
			echo "</div>";
		}
		
		
	echo "</div>";
	
	
	echo "<br/>";
	
	
	echo "<div class='row'>";
	
		if (true){
			$currentCandidate 	= $allCharts[3];
			echo "<div class='col-lg-6 col-md-12 col-sm-12 col-xs-12'>";
				echo "<div class='card'>";
					echo "<div class='card-header'>";
						echo "<span class='card-title'><strong>{$currentCandidate['Title']}</strong></span>";
						echo "<span class='selectedCountIndividualSection startHidden' style='padding-left:10px;'>(Selected: <span class='selectedCount'></span> out of {$compairsonCountFormatted})</span>";
						echo "<a id='resetTrigger_{$currentCandidate['Column']}' href='javascript:void(0);' class='pull-right'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
					echo "</div>";
					
					echo "<div class='card-block'>";
						echo "<div id='chartSection_{$currentCandidate['Column']}'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
					echo "</div>";
				
				echo "</div>";
				
			echo "</div>";
		}

		if (true){
			$currentCandidate 	= $allCharts[4];
			echo "<div class='col-lg-6 col-md-12 col-sm-12 col-xs-12'>";
				echo "<div class='card'>";
					echo "<div class='card-header'>";
						echo "<span class='card-title'><strong>{$currentCandidate['Title']}</strong></span>";
						echo "<span class='selectedCountIndividualSection startHidden' style='padding-left:10px;'>(Selected: <span class='selectedCount'></span> out of {$compairsonCountFormatted})</span>";
						echo "<a id='resetTrigger_{$currentCandidate['Column']}' href='javascript:void(0);' class='pull-right'>" . printFontAwesomeIcon('fas fa-sync-alt') . " Reset</a>";
					echo "</div>";
					
					echo "<div class='card-block'>";
						echo "<div id='chartSection_{$currentCandidate['Column']}'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
					echo "</div>";
				
				echo "</div>";
				
			echo "</div>";
		}
		
		
	echo "</div>";
	

	
	
	echo "<div id='tableSection'></div>";
	
	echo "<div id='tableSectionBusy'>" . printFontAwesomeIcon('fas fa-spinner fa-spin') . "</div>";
	
	echo "<input type='hidden' id='selected_comparison_indexes' value=''/>";
	
	
echo "</div>";


?>


<style>

.statisticCell{
	font-family: Arial,Helvetica,sans-serif;
	color:#FFF;
	padding:20px;
	text-align:center;
	font-size:20px;
	border-collapse:collapse;
	border:1px solid #FFF;
	width: 160px !important;
	height: 150px  !important;
	padding-top:40px;
	vertical-align:middle;
}

.statisticCell a{
	color:#FFF;
}

.card-block{
	height:450px;
	max-height:450px;
	overflow-y:auto;
	
}


#chartSection_Case_Treatment .x.axis text {
    text-anchor: end !important;
    transform: rotate(-35deg);
	font-size:11px;
}

.dc-chart text.pie-slice {
	fill: black;

}

.dc-chart g.row text {
	fill: #000 !important;
	font-size:11px !important;
	
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	
	<?php
		foreach($allCharts as $tempKey => $currentChart){
			$resetTrigger 	= "resetTrigger_{$currentChart['Column']}";
			$chartObj		= "chartObj_{$currentChart['Column']}";
			$chartSection	= "chartSection_{$currentChart['Column']}";
			
			if ($currentChart['Chart'] == 'PieChart'){
				echo "var {$chartObj} = dc.pieChart('#{$chartSection}');" . "\n";
			} elseif ($currentChart['Chart'] == 'BarChart-Vertical'){
				echo "var {$chartObj} = dc.barChart('#{$chartSection}');" . "\n";
				echo "{$chartObj}.yAxis().tickFormat(d3.format('d'));" . "\n";
			} elseif ($currentChart['Chart'] == 'BarChart-Horizontal'){
				echo "var {$chartObj} = dc.rowChart('#{$chartSection}');" . "\n";
			}
	?>
			$(document).on('click', '#<?php echo $resetTrigger; ?>', function(){
				<?php echo $chartObj; ?>.filterAll();
				dc.redrawAll()
			});
			
	<?php } ?>
	
	
	d3.csv('app_dashboard_comparison_data.php?comparisonIndex=<?php echo $_GET['comparisonIndex']; ?>', function (data) {
        var totalWidth 	= 990;

        var processedData 	= crossfilter(data);
        var all 			= processedData.groupAll();
		var rowBarHeight	= 18;
		var chartHeight		= 390;
		
		function removeEmptyValues_ComparisonCategory(sourceGroup){
			return {
				all:function(){
					return sourceGroup.all().filter(function(d){
						<?php
						
							unset($checks);
							$checks[] = "(d.key != '')";
							
							
							
							echo  'return (' . implode(' && ', $checks) . ')'; 
						?>
					});
				}
			};
		}
		
		
		function removeEmptyValues_Case_CellType(sourceGroup){
			return {
				all:function(){
					return sourceGroup.all().filter(function(d){
						<?php
						
							unset($checks);
							$checks[] = "(d.key != '')";
							
							if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_cell_type'])){
								$checks[] = "(d.key != 'Unknown')";	
							}
							
							if (in_array('Hide Others', $getUserSettings['dashboard_chart_cell_type'])){
								$checks[] = "(!(d.key).startsWith('Other'))";
							}
							
							echo  'return (' . implode(' && ', $checks) . ')'; 
						?>
					});
				}
			};
		}
		
		
		function removeEmptyValues_Case_Tissue(sourceGroup){
			return {
				all:function(){
					return sourceGroup.all().filter(function(d){
						<?php
						
							unset($checks);
							$checks[] = "(d.key != '')";
							
							if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_cell_type'])){
								$checks[] = "(d.key != 'Unknown')";	
							}
							
							if (in_array('Hide Others', $getUserSettings['dashboard_chart_cell_type'])){
								$checks[] = "(!(d.key).startsWith('Other'))";
							}
							
							echo  'return (' . implode(' && ', $checks) . ')'; 
						?>
					});
				}
			};
		}
		
		
		function removeEmptyValues_Case_DiseaseState(sourceGroup){
			return {
				all:function(){
					return sourceGroup.all().filter(function(d){
						<?php
						
							unset($checks);
							$checks[] = "(d.key != '')";
							
							if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_disease_state'])){
								$checks[] = "(d.key != 'Unknown')";	
							}
							
							if (in_array('Hide Others', $getUserSettings['dashboard_chart_disease_state'])){
								$checks[] = "(!(d.key).startsWith('Other'))";
							}
							
							if (in_array('Hide Normal Control', $getUserSettings['dashboard_chart_disease_state'])){
								$checks[] = "(d.key != 'Normal Control')";	
							}
							
							echo  'return (' . implode(' && ', $checks) . ')'; 
						?>
					});
				}
			};
		}
		
		
		function removeEmptyValues_Case_Treatment(sourceGroup){
			return {
				all:function(){
					return sourceGroup.all().filter(function(d){
						<?php
						
							unset($checks);
							$checks[] = "(d.key != '')";
							
							if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_treatment'])){
								$checks[] = "(d.key != 'Unknown')";	
							}
							
							if (in_array('Hide Others', $getUserSettings['dashboard_chart_treatment'])){
								$checks[] = "(!(d.key).startsWith('Other'))";
							}
							
							echo  'return (' . implode(' && ', $checks) . ')'; 
						?>
					});
				}
			};
		}
		
		
		function removeEmptyValues_PlatformName(sourceGroup){
			return {
				all:function(){
					return sourceGroup.all().filter(function(d){
						<?php
						
							unset($checks);
							$checks[] = "(d.key != '')";
							
							if (in_array('Hide Others', $getUserSettings['dashboard_chart_platform_name'])){
								$checks[] = "(!(d.key).startsWith('Other'))";
							}
							
							echo  'return (' . implode(' && ', $checks) . ')'; 
						?>
					});
				}
			};
		}


		<?php
		foreach($allCharts as $tempKey => $currentChart){
			$chartObj		= "chartObj_{$currentChart['Column']}";
			$chartSection	= "chartSection_{$currentChart['Column']}";
			$dimensionVar	= "Dimension_{$currentChart['Column']}";
			$groupVar		= "Group_{$currentChart['Column']}";
		?>
			var <?php echo $dimensionVar; ?> = processedData.dimension(function (d) {
				return d['<?php echo $currentChart['Column']; ?>'];
			});
			
			var <?php echo $groupVar; ?>	= removeEmptyValues_<?php echo $currentChart['Column']; ?>(<?php echo $dimensionVar; ?>.group().reduceCount());
			
			
			<?php if ($currentChart['Chart'] == 'PieChart'){ ?>
				<?php echo $chartObj; ?>
					.width(totalWidth / 1.1)
					.height(parseInt(chartHeight * 0.8))
					.slicesCap(40)
					.innerRadius(40)
					.dimension(<?php echo $dimensionVar; ?>)
					.group(<?php echo $groupVar; ?>)
					.renderLabel(true)
					.ordinalColors(d3.scale.category10().range())
					.transitionDuration(500)
					.drawPaths(true)
					.title(function (d) {
						return d.key + ': ' + d.value;
					})
					.label(function (d) {
						return d.value;
					})
					.on('filtered', function (){
						reloadTable(<?php echo $dimensionVar; ?>.top(Infinity));
					})
					.legend(dc.legend());
			<?php } ?>
			
			
			<?php if ($currentChart['Chart'] == 'BarChart-Horizontal'){ ?>
			var <?php echo $currentChart['Column']; ?>_Height = parseInt((<?php echo $dimensionVar; ?>.group().size() + 2)*(rowBarHeight + 5));
			if ((<?php echo $currentChart['Column']; ?>_Height < chartHeight) || (isNaN(<?php echo $currentChart['Column']; ?>_Height))){
				<?php echo $currentChart['Column']; ?>_Height = chartHeight;	
			}
			<?php echo $chartObj; ?>
				.width(totalWidth / 2.1)
				.height(<?php echo $currentChart['Column']; ?>_Height)
				.fixedBarHeight(rowBarHeight)
				.margins({top: 20, left: 10, right: 10, bottom: 20})
				.dimension(<?php echo $dimensionVar; ?>)
				.ordinalColors(d3.scale.category10().range())
				.renderLabel(true)
				.ordering(function (d) {
					
					if ($('#sortByName').prop('checked')){
						return d.key;
					} else {
						return -d.value;
					}
				})
				.group(<?php echo $groupVar; ?>)
				.elasticX(true)
				.on('filtered', function (){
					reloadTable(<?php echo $dimensionVar; ?>.top(Infinity));
				})
				.label(function (d) {
					return d.key + "\n" + ' (' + d.value + ')';
				})
				.xAxis().tickFormat(d3.format("d"));
			<?php } ?>
			
			
			
			<?php if ($currentChart['Chart'] == 'BarChart-Vertical'){ ?>
			<?php echo $chartObj; ?>
				.height(parseInt(chartHeight))
				.width(totalWidth / 1.5)
				.margins({top: 40, left: 50, right: 10, bottom: 150})
				.x(d3.scale.ordinal())
				.xUnits(dc.units.ordinal)
				.yAxisLabel('')
				.brushOn(true)
				.elasticY(true)
				.dimension(<?php echo $dimensionVar; ?>)
				.barPadding(0.1)
				.outerPadding(0.05)
				.ordering(function (d) {
					if ($('#sortByName').prop('checked')){
						return d.key;
					} else {
						return -d.value;
					}
				})
				.title(function (d) {
					return d.key + ': ' + d.value;
				})
				.group(<?php echo $groupVar; ?>)
				.on('renderlet', function (chart){
					
					//Check if labels exist
					var gLabels = chart.select(".labels");
					if (gLabels.empty()){
						gLabels = chart.select(".chart-body").append('g').classed('labels', true);
					}

					var gLabelsData = gLabels.selectAll("text").data(chart.selectAll(".bar")[0]);
					
					gLabelsData.exit().remove(); //Remove unused elements
					
					gLabelsData.enter().append("text") //Add new elements
					
					gLabelsData
					.attr('text-anchor', 'middle')
					.attr('fill',  function(d){ 
						if (+d.getAttribute('y') > 0){
							return 'black';
						} else {
							return 'white';
						}
					})
					.text(function(d){
						return d3.select(d).data()[0].data.value;
					})
					.attr('x', function(d){ 
						return +d.getAttribute('x') + (d.getAttribute('width')/2); 
					})
					.attr('y', function(d){ 
						if (+d.getAttribute('y') > 0){
							return +d.getAttribute('y') - 5;
						} else {
							return +d.getAttribute('y') + 15;
						}
					})
					.attr('style', function(d){
						//if (+d.getAttribute('height') < 18) return "display:none";
					});
					
				})
				.on('filtered', function (){
					reloadTable(<?php echo $dimensionVar; ?>.top(Infinity));
				});
			<?php } ?>
			
		<?php } ?>
			

        var recordCount = dc.dataCount('#selectedCountSection')
            .dimension(processedData)
            .group(all)
            .html({
                some: "<strong>%filter-count</strong> out of <strong><?php echo $compairsonCountFormatted; ?></strong> comparisons have been selected. <br/> <a href='javascript:dc.filterAll(); dc.renderAll();'><?php echo printFontAwesomeIcon('fas fa-sync-alt'); ?> Reset all filters</a> &nbsp; <a href='javascript:void(0);' class='dashboardTrigger'><?php echo printFontAwesomeIcon('fas fa-chart-pie'); ?> Create a new dashboard based on selected comparisons</a>",
                all: 'Showing all comparisons (<?php echo $compairsonCountFormatted; ?>). Please click on the graphs to apply filters.<br/><br/>'
            });


        dc.renderAll();
		reloadTable(<?php echo $dimensionVar; ?>.top(Infinity));
		
		$('.card-block .fa-spinner').hide();

    });
	
	
	
	
	$(document).on('change', '.sortMethod', function(){
		dc.renderAll();
	});
	
	
	
	$(document).on('click', '.dashboardTrigger', function(){
		

		var selected_comparison_indexes = $('#selected_comparison_indexes').val();

		if (selected_comparison_indexes != ''){
			
			var data = new Object();	
			data['index[]']	= selected_comparison_indexes.split(',');
			
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=8',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						window.location = responseText;
					}
				}
			});
		}
	});
	
	
	
	
	
	
	$('#tableSection').on('change', '.selectAllTrigger', function(){
		var isChecked = $(this).prop('checked');
		
		if (isChecked){
			$('.recordCheckbox').prop('checked', true);	
		} else {
			$('.recordCheckbox').prop('checked', false);	
		}
	});


	$('#tableSection').on('click', '.createListTrigger', function(){
		
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=4',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					}
				}
			});
		} else {
			
			$('#Record_Required_createListTrigger').show();	
		}
	});
	
	$('#tableSection').on('click', '.comparisonToSampleTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=6',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#comparisonToSample_Missing_Result').show();	
					}
				}
			});
		} else {
			
			$('#comparisonToSample_Missing_Record').show();	
		}
	});
	
	
	$('#tableSection').on('click', '.comparisonToSignificantTrigger', function(){
		$('.errorMessage').hide();
		
		var count = 0;
		var data = new Object();
	
		data['Category'] = '<?php echo $category; ?>';
		data['index[]']	= [];
		
		$('.recordCheckbox:checked').each(function() {
			count++;
			data['index[]'].push($(this).val());
		});
		
		if (count > 0){
			$.ajax({
				type: 'POST',
				url: 'app_list_ajax.php?action=7',
				data: data,
				success: function(responseText){
					responseText = $.trim(responseText);
					
					if (responseText != ''){
						var redirectWindow = window.open(responseText, '_blank');
    					redirectWindow.location;
					} else {
						$('#comparisonToSignificant_Missing_Result').show();	
					}
				}
			});
		} else {
			$('#comparisonToSignificant_Missing_Record').show();	
		}
	});
	
	
});



function reloadTable(obj){
	var count = parseInt(obj.length);
	var countNum = count;
	
	var data = new Object();
	var selected_comparison_indexes = '';
	count = count.toLocaleString();
	
	$('.selectedCount').html(count);
	$('.selectedCountIndividualSection').show();
	$('#tableSection').empty();
	$('#tableSectionBusy').show();

	
	data['ComparisonIndex'] = [];
		
	for (var tempKey in obj) {
		// skip loop if the property is from prototype
		if (!obj.hasOwnProperty(tempKey)) continue;
		
		data['ComparisonIndex'].push(obj[tempKey].ComparisonIndex);
		selected_comparison_indexes += ',' + obj[tempKey].ComparisonIndex;
	}
	
	$('#selected_comparison_indexes').val(selected_comparison_indexes);
	

	$.ajax({
		type: 'POST',
		url: 'app_dashboard_comparison_table_exe.php',
		data: data,
		success: function(responseText){
			$('#tableSectionBusy').hide();
			$('#tableSection').html(responseText);
		}
	});
}
</script>
