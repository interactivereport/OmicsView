<?php
include_once('config_init.php');


if ($_POST['Column'] == ''){

	if ($_POST['type'] == 'subplotBy'){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select a subplot by attribute first.";	
	} else {
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select a comparison attribute first.";
	}
	echo getAlerts($message, 'danger', 'col-lg-12 col-sm-12');
	exit();
}


if (true){
	cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);
	
	$geneName 				= strtoupper(trim($_POST['GeneName']));
	$geneNameStandard		= guess_gene_name($geneName, '', 1);
	
	if ($geneName == ''){
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['Please enter at least a gene name and try again.']}";
		echo getAlerts($message, 'danger');
		exit();
	} else {
		$geneIndex = search_gene_index($geneName);
		
		if ($geneIndex < 0){
			$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " {$APP_MESSAGE['The gene does not exist:']} <strong>{$geneName}</strong>";
			echo getAlerts($message, 'danger', 'col-lg-12 col-sm-12');
			exit();		
		}
		
		$_POST['GeneIndex'] = $geneIndex;
	}
}


$results = prepareSingleBubblePlotHistogram($geneIndex, $_POST);

if ($results == false){
	$message = "<div><strong>Warning!</strong> {$APP_MESSAGE['There are no comparison data available with the selected gene. Please try using a different gene.']}</div>";
	
	echo getAlerts($message, 'warning', 'col-lg-12 col-sm-12');
	exit();	
}

$currentColumn = $_POST['type'];


echo "<h5>{$geneNameStandard}, {$results['Summary']['Title']}</h5>";


echo "<div class='small' style='padding:20px;'>";
	echo "<div class='table-responsive'>";
		echo "<table id='{$currentColumn}_tableToSort' class='sortable-theme-slick table table-striped table-bordered table-condensed'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th class='text-center' data-sortable='false'>";
						echo "<input type='checkbox' id='{$currentColumn}_checkboxSelectAll' class='advancedOptionsColumnSelectAll' children='{$currentColumn}_checkbox'/>";
					echo "</th>";
					
					
					echo "<th data-sort='string-ins' class='tableHeaderToSort'>";
						echo "Category";
					echo "</th>";
					
					
					echo "<th data-sort='int' class='tableHeaderToSort'>";
						echo "# of Comparison Data";
					echo "</th>";

				echo "</tr>";
			echo "</thead>";
			
			
			echo "<tbody>";
			foreach($results['histogram'] as $currentValue => $currentCount){
				
				$currentValueSlashed 	= htmlspecialchars($currentValue, ENT_QUOTES);
				$currentValueDisplay 	= ucwords2($currentValue);
				$currentCount			= $currentCount;
				
				
				if (in_array($currentValueSlashed, $_POST["{$currentColumn}_customize"])){
					$preCheck = "checked='checked'";
				} else {
					$preCheck = '';	
				}
				
				
				echo "<tr>";
					
					echo "<td class='text-center'>";
						echo "<input type='checkbox' name='{$currentColumn}_customize[]' class='{$currentColumn}_Customize_Candidates' value='{$currentValueSlashed}' {$preCheck}/>";
					echo "</td>";
					
					
					echo "<td>";
						echo "<span title='{$currentValueSlashed}'>{$currentValueDisplay}</span>";
					echo "</td>";
					
					
					echo "<td>";
						echo $currentCount;
					echo "</td>";

				echo "</tr>";
			}
			echo "</tbody>";
		
		echo "</table>";	
			
	echo "</div>";
echo "</div>";


$tableToSortID = "X_" . getUniqueID() . "_tableToSort";

?>
<script type="text/javascript">

$(document).ready(function(){
	 var <?php echo $tableToSortID; ?> = $('#<?php echo $currentColumn; ?>_tableToSort').stupidtable();
	 
	 <?php echo $tableToSortID; ?>.on("aftertablesort", function (event, data) {
        var th = $(this).find("th");
        th.find(".arrow").remove();
        var dir = $.fn.stupidtable.dir;
		
		if (data.direction == 'asc'){
			var icon = "<?php echo printFontAwesomeIcon('fas fa-sort-up'); ?>";
		} else {
			var icon = "<?php echo printFontAwesomeIcon('fas fa-sort-down'); ?>";	
		}

        th.eq(data.column).append('<span class="arrow">' + icon +'</span>');
      });
	
});
</script>
