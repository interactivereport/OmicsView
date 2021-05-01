<?php

include_once('config_init.php');

$action = strtolower($_GET['action']);

switch($action){
	case('project_index_to_comparison_list'):
		if ($_GET['projectIndex'] >= 0){
			$comparison = getComparisonIDFromProjectIndex($_GET['projectIndex']);
			
			if (array_size($comparison) > 0){
				$sessionKey = getUniqueID();
				$_SESSION['List'][$sessionKey] = $comparison;
				header("Location: app_list_new.php?Category=Comparison&Session={$sessionKey}");
				exit();
			} else {
				$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are no comparison records available.";
				echo getAlerts($message, 'danger');
				exit();
			}
		}
		break;
		
	case('project_index_to_comparisons'):
		if ($_GET['projectIndex'] >= 0){
			$projectInfo = get_multiple_record('project', $_GET['projectIndex'], 'GetRow');
			$projectIndex = $projectInfo['ProjectIndex'];
			
			
			$dataArray = array();
			if (internal_data_is_public($projectIndex)){
				$projectName = $projectInfo['ProjectID'];
				$dataArray['POST']['data_source'][] = 'public';
			} else {
				$projectName = $projectInfo['ProjectID_Original'];
				$dataArray['POST']['data_source'][] = 'private';
			}
			
			
			$currentIndex = 1;
			$dataArray['Search'][$currentIndex]['Field'] 	= 'ProjectName';
			$dataArray['Search'][$currentIndex]['Operator'] = 1;
			$dataArray['Search'][$currentIndex]['Value'] 	= $projectName;
			$dataArray['Search'][$currentIndex]['Logic'] 	= '';

			$urlKey = putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
		
		
			$URL = "app_record_browse.php?Category=Comparison&key={$urlKey}&hide=1";
			header("Location: {$URL}");
			exit();
		}
		
		
	case('project_index_to_sample_list'):
		if ($_GET['projectIndex'] >= 0){
			$sample = getSampleIDFromProjectIndex($_GET['projectIndex']);
			
			if (array_size($sample) > 0){
				$sessionKey = getUniqueID();
				$_SESSION['List'][$sessionKey] = $sample;
				header("Location: app_list_new.php?Category=Sample&Session={$sessionKey}");
				exit();
			} else {
				$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " There are no sample records available.";
				echo getAlerts($message, 'danger');
				exit();
			}
		}
		break;
		
	case('project_index_to_samples'):
		if ($_GET['projectIndex'] >= 0){
			$projectInfo = get_multiple_record('project', $_GET['projectIndex'], 'GetRow');
			$projectIndex = $projectInfo['ProjectIndex'];
			
			
			$dataArray = array();
			if (internal_data_is_public($projectIndex)){
				$projectName = $projectInfo['ProjectID'];
				$dataArray['POST']['data_source'][] = 'public';
			} else {
				$projectName = $projectInfo['ProjectID_Original'];
				$dataArray['POST']['data_source'][] = 'private';
			}
			
			
			$currentIndex = 1;
			$dataArray['Search'][$currentIndex]['Field'] 	= 'ProjectName';
			$dataArray['Search'][$currentIndex]['Operator'] = 1;
			$dataArray['Search'][$currentIndex]['Value'] 	= $projectName;
			$dataArray['Search'][$currentIndex]['Logic'] 	= '';

			$urlKey = putSQLCacheWithoutKey($dataArray, '', 'URL', 1);
		
		
			$URL = "app_record_browse.php?Category=Sample&key={$urlKey}&hide=1";
			header("Location: {$URL}");
			exit();
		}
		break;
		
	case('parse_canvasxpress_json'):
		$results = validateCanvasXpressJSON($_POST['JSON']);
		
		if ($results !== FALSE){
			if ($results['Result']){
				echo "<p>" . printFontAwesomeIcon('fas fa-check text-success') . "&nbsp; The JSON code is valid. Here is the preview of how your JSON code will be executed:</p>";
				echo "<pre>" . implode("\n", $results['Preview']) . "</pre>";
			} else {
				echo "<p>" . printFontAwesomeIcon('fas fa-times text-danger') . "&nbsp; Error. Your JSON code is not valid: {$results['Error']}</p>";
			}
		}
	
		break;
	
		
		
		
	default:
		$message = printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The action does not match.";
		echo getAlerts($message, 'danger');
		break;
}




?>