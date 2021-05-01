<?php
include_once('config_init.php');

if (!$_POST['API']){
	echo "<br/>";
	echo "<hr/>";
}

$_POST['width'] = abs(intval($_POST['width']));


cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

if (true){
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
			echo getAlerts($message, 'danger');
			exit();		
		}
		
		$_POST['GeneIndex'] = $geneIndex;
	}
}


if ($_POST['subplotBy'] == $_POST['colorBy']){
	$_POST['subplotBy'] = '';
}

if (true){
	$otherOptions = array();
	$otherOptions['geneNameStandard'] 						= $geneNameStandard;
	$otherOptions['colorBy'] 								= $_POST['colorBy'];
	$otherOptions['colorBy_settings'] 						= intval($_POST['colorBy_settings']);
	$otherOptions['data_source'] 							= $_POST['data_source'];
	$otherOptions['data_source_private_project_indexes'] 	= $_POST['data_source_private_project_indexes'];
	$otherOptions['marker'] 								= $_POST['marker'];
	$otherOptions['shapeBy'] 								= $_POST['shapeBy'];
	$otherOptions['y-axis']									= $_POST['y-axis'];
	$otherOptions['y-axis_settings'] 						= intval($_POST['y-axis_settings']);
	$otherOptions['keep_blank'] 							= $_POST['keep_blank'];
	
	if ($otherOptions['y-axis_settings'] == -1){
		$otherOptions['y-axis_customize'] = array_clean($_POST['y-axis_customize']);
		
		if (array_size($_POST['y-axis_customize']) <= 0){
			$otherOptions['y-axis_settings'] = 0;
			unset($otherOptions['y-axis_customize']);	
		}
	}
	
	
	if ($otherOptions['colorBy_settings'] == -1){
		$otherOptions['colorBy_customize'] = array_clean($_POST['colorBy_customize']);
		
		if (array_size($_POST['colorBy_customize']) <= 0){
			$otherOptions['colorBy_settings'] = 0;
			unset($otherOptions['colorBy_customize']);	
		}
	}
	

}


if ($_POST['graphLibrary'] == ''){
	$_POST['graphLibrary'] = 'Plotly';	
}





if ($_POST['graphLibrary'] == 'Plotly'){
	if ($_POST['subplotBy'] == ''){
		
		$results = prepareSingleBubblePlotDataByGeneName_Plotly_Single_v2($geneIndex, $otherOptions);
		$chartFile = 'app_bubble_plot_single_exe_tab_Plotly_single_v2.php';
		
	} else {
		$otherOptions['subplotBy'] = $_POST['subplotBy'];
		
		if (($_POST['subplotBy'] != '') && ($_POST['subplotBy_settings'] == -1) && (array_size($_POST['subplotBy_customize']) > 0)){
			$otherOptions['subplotBy_settings'] 	= -1;	
			$otherOptions['subplotBy_customize'] 	= $_POST['subplotBy_customize'];
		}
		
		$results = prepareSingleBubblePlotDataByGeneName_Plotly_Subplot_v2($geneIndex, $otherOptions);
		$chartFile = 'app_bubble_plot_single_exe_tab_Plotly_subplot_v2.php';
	}
}




if ($results == false){
	$message = "<div><strong>Warning!</strong> {$APP_MESSAGE['There are no comparison data available with the selected gene. Please try using a different gene.']}</div>";
	
	echo getAlerts($message, 'warning', 'col-lg-10 col-sm-12');
	exit();	
}

$currentCount 	= number_format($results['Summary']['Data_Count']['Current']);
$urlKey = putSQLCacheWithoutKey($_POST, '', 'URL', 1);


if ($_POST['API']){
	echo "<br/>";
	
	if ($_POST['ProjectIndex'] > 0){
		
		$project = get_one_record_by_id('project', $_POST['ProjectIndex']);
		
		echo "<p>Project: <a href='app_project_review.php?id={$_POST['ProjectIndex']}' target='_blank'>{$project['ProjectID']}</a></p>";
	}
	
	echo "<p>Click <a href='{$_POST['URL']}?key={$urlKey}' target='_blank'>here</a>	to customize the bubble plot in {$BXAF_CONFIG['BXAF_PAGE_APP_NAME']}</p>";
	echo "<br/>";
} else {
	echo "<div class='row'>";
		echo "<div class='col-12'>";
	
		if (true){
			
			$rawDataKey = putSQLCacheWithoutKey($results['Export']['Raw'], '', 'prepareSingleBubblePlotDataByGeneName_Export', 1);
			
			$message = 
				"<p><a href='app_common_table_download.php?key={$rawDataKey}&filename=bubble_plot_{$geneNameStandard}_raw.csv' target='_blank'>" . 
				printFontAwesomeIcon('fas fa-download') . "&nbsp;Download All Raw Data ({$currentCount})</a></p>";
			
			echo $message;
		}
		
		
	
		
		
		if ($_POST['graphLibrary'] == 'Plotly'){
			
			$saveSVGTrigger = "saveSVGTrigger" . getUniqueID();
			
			$message = 
				"<p><a href='javascript:void(0);' id='{$saveSVGTrigger}'>" . 
				printFontAwesomeIcon('far fa-file-image') . "&nbsp;Download Bubble Plot (SVG)</a></p>";
			
			echo $message;
			
		}

		echo "</div>";
	echo "</div>";
}





if ($_POST['API']){
	include($chartFile);
	
} else {

	echo "<div id='tabs'>";
		echo "<ul class='nav nav-tabs' role='tablist'>";
			echo "<li class='nav-item'>
					<a class='nav-link active' href='#Plot' role='tab' data-toggle='tab'>Plot</a>
				  </li>";
	
			echo "<li class='nav-item'>
					<a class='nav-link' href='#Table' role='tab' data-toggle='tab'>Data ({$currentCount})</a>
				  </li>";
		echo "</ul>";
	
				  
		echo "<div class='tab-content'>";
			if (true){
				echo "<div role='tabpanel' id='Plot' class='tab-pane fade in active show'>";
					echo "<br/>";
					include($chartFile);
				echo "</div>";
			}
			
			if (true){
				echo "<div role='tabpanel' id='Table' class='tab-pane fade in'>";
					echo "<br/>";
					include('app_bubble_plot_single_exe_tab_data.php');
				echo "</div>";
			}
		echo "</div>";
		
	echo "</div>";
}

?>