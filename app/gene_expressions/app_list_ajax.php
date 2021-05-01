<?php
include_once('config_init.php');


$action = intval($_GET['action']);
$category = $_POST['Category'];

if ($action == 1){
	
	//1: A name exists
	//0: Name is okay
	
	$testResult = check_list_name_exist($_POST['Name'], $_POST['Category'], $_POST['ID']);
	
	if ($testResult){
		echo 1;	
	} else {
		echo 0;	
	}

} elseif ($action == 2){
	
	$listID = $_GET['ID'];
	
	delete_list($listID);
	
	echo 1;
	
} elseif ($action == 3){
	
	$results = getListInputSummary($_POST['Input'], $category);
	
	if ($results['Missing_Count'] > 0){
		$modalID 	= 'userInputSummaryModal';
		$modalTitle = "<h4 class='modal-title'>{$APP_CONFIG['APP']['List_Category'][$category]['Section_Titles']}</h4>";
		$modalBody  = "<div class='row'>";
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Entered ({$results['Input_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['Input']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Available ({$results['Output_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['Output']) . "</textarea>";
			$modalBody  .= "</div>";
							
			$modalBody  .= "<div class='col-lg-4 col-sm-12'>";
				$modalBody  .= "<div class='text-nowrap'><strong>Not Available ({$results['Missing_Count']}):</strong></div>";
				$modalBody  .= "<textarea style='width:100%;' rows=10>" . implode("\n", $results['Missing']) . "</textarea>";
			$modalBody  .= "</div>";
		$modalBody  .= "</div>";
		
		echo printModal($modalID, $modalTitle, $modalBody);
		
		echo printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Some {$APP_CONFIG['APP']['List_Category'][$category]['title']} you entered are not available. Please click <a data-toggle='modal' href='#userInputSummaryModal'>here</a> for details.";
		
	} else {
		echo printFontAwesomeIcon('fas fa-check text-success')  . " You have entered {$results['Output_Count']} {$APP_CONFIG['APP']['List_Category'][$category]['title']}, all of them are available in the database.";
	}
	
} elseif ($action == 4){
	//Given indexes, create a new list
	$category = $_POST['Category'];

	$results = get_multiple_record($category, $_POST['index'], 'GetCol', "`{$APP_CONFIG['APP']['List_Category'][$category]['Column_Human']}`");

	$results = array_clean($results);

	
	if (array_size($results) > 0){
		
		$sessionKey = getUniqueID();
		$_SESSION['List'][$sessionKey] = $results;
		echo "app_list_new.php?Category={$category}&Session={$sessionKey}";
	}
	
} elseif ($action == 5){
	//Given project indexes, return sample IDs
	$source 	= internal_data_split_multiple_data_by_source($_POST['index']);

	unset($projects);	
	if (array_size($source['public']) > 0){
		$projectIndexes = implode(',', $source['public']);
		$SQL_TABLE = $APP_CONFIG['Table']['Projects'];
		$SQL = "SELECT `ProjectIndex`, `ProjectID` FROM `{$SQL_TABLE}` WHERE `ProjectIndex` IN ({$projectIndexes})";
		$projectPublic = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 1, 1);

		
		foreach($projectPublic as $tempKey => $tempValue){
			$projectPublic[$tempKey] = "'" . addslashes($tempValue) . "'";
		}
		$projectPublicString = implode(',', $projectPublic);
		
		
		$SQL_TABLE = $APP_CONFIG['Table']['Samples'];
		$SQL = "SELECT `SampleID` FROM `{$SQL_TABLE}` WHERE `ProjectName` IN ({$projectPublicString})";
		$samplePublic = getSQL($SQL, 'GetCol', $SQL_TABLE, 1, 1);
		
		$sample = $samplePublic;
	}
	
	
	if (array_size($source['private']) > 0){
		$projectIndexes = implode(',', $source['private']);
		
		$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Samples'];
		$SQL = "SELECT `SampleID` FROM `{$SQL_TABLE}` WHERE `ProjectIndex` IN ({$projectIndexes})";
		$samplePrivate = getSQL($SQL, 'GetCol', $SQL_TABLE, 1, 1);
		
		foreach($samplePrivate as $tempKey => $tempValue){
			$sample[] = $tempValue;	
		}
		
	}
	
	if (array_size($sample) > 0){
		
		$sessionKey = getUniqueID();
		$_SESSION['List'][$sessionKey] = $sample;
		echo "app_list_new.php?Category=Sample&Session={$sessionKey}";
	}
	
	
} elseif ($action == 6){
	//Given comparison indexes, return sample IDs
	$sampleIDs = comparison_index_to_sample_id($_POST['index']);
	
	if (array_size($sampleIDs) > 0){
		
		natsort($sampleIDs);
		
		$sessionKey = getUniqueID();
		$_SESSION['List'][$sessionKey] = $sampleIDs;
		echo "app_list_new.php?Category=Sample&Session={$sessionKey}";
	}
	
	
} elseif ($action == 7){
	//Given comparison indexes, return comparison IDs
	$source 	= internal_data_split_multiple_data_by_source($_POST['index']);

	unset($comparisons);	
	if (array_size($source['public']) > 0){
		$comparisonIndexes = implode(',', $source['public']);
		$SQL_TABLE = $APP_CONFIG['Table']['Comparisons'];
		$SQL = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$SQL_TABLE}` WHERE `ComparisonIndex` IN ({$comparisonIndexes})";
		$comparisonIDs = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 1, 1);
	}
	
	if (array_size($source['private']) > 0){
		$comparisonIndexes = implode(',', $source['private']);
		$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Comparisons'];
		$SQL = "SELECT `ComparisonIndex`, `ComparisonID` FROM `{$SQL_TABLE}` WHERE `ComparisonIndex` IN ({$comparisonIndexes})";
		$comparisonPrivate = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 1, 1);

		
		foreach($comparisonPrivate as $tempKey => $tempValue){
			$comparisonIDs[] = $tempValue;
			
		}
	}
	
	if (array_size($comparisonIDs) > 0){
		
		natsort($comparisonIDs);
		
		$sessionKey = getUniqueID();
		$_SESSION['ComparisonIDs'][$sessionKey] = $comparisonIDs;
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}plot/dashboard/changed_genes.php?Session={$sessionKey}";
	}
	
	
} elseif ($action == 8){
	//Given comparison indexes, forward to dashboard
	$_POST['index'] 	= array_clean($_POST['index']);

	
	if (array_size($_POST['index']) > 0){
		natsort($_POST['index']);
		$dataKey = putSQLCacheWithoutKey($_POST['index'], '', 'URL', 1);
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_dashboard_comparison.php?comparisonIndex={$dataKey}";
	}
	
} elseif ($action == 9){
	//Given project indexes, return comparison Indexs
	$source 	= internal_data_split_multiple_data_by_source($_POST['index']);

	unset($projects);	
	if (array_size($source['public']) > 0){
		$projectIndexes = implode(',', $source['public']);
		$SQL_TABLE = $APP_CONFIG['Table']['Projects'];
		$SQL = "SELECT `ProjectIndex`, `ProjectID` FROM `{$SQL_TABLE}` WHERE `ProjectIndex` IN ({$projectIndexes})";
		$projectPublic = getSQL($SQL, 'GetAssoc', $SQL_TABLE, 1, 1);

		
		foreach($projectPublic as $tempKey => $tempValue){
			$projectPublic[$tempKey] = "'" . addslashes($tempValue) . "'";
		}
		$projectPublicString = implode(',', $projectPublic);
		
		
		$SQL_TABLE = $APP_CONFIG['Table']['Comparisons'];
		$SQL = "SELECT `ComparisonIndex` FROM `{$SQL_TABLE}` WHERE `ProjectName` IN ({$projectPublicString})";
		$comparisonPublic = getSQL($SQL, 'GetCol', $SQL_TABLE, 1, 1);
		
		$comparison = $comparisonPublic;
	}
	
	
	if (array_size($source['private']) > 0){
		$projectIndexes = implode(',', $source['private']);
		
		$SQL_TABLE = $APP_CONFIG['Table']['App_User_Data_Comparisons'];
		$SQL = "SELECT `ComparisonIndex` FROM `{$SQL_TABLE}` WHERE `ProjectIndex` IN ({$projectIndexes})";
		$comparisonPrivate = getSQL($SQL, 'GetCol', $SQL_TABLE, 1, 1);
		
		foreach($comparisonPrivate as $tempKey => $tempValue){
			$comparison[] = $tempValue;	
		}
		
	}
	
	if (array_size($comparison) > 0){
		
		natsort($comparison);
		$dataKey = putSQLCacheWithoutKey($comparison, '', 'URL', 1);
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_dashboard_comparison.php?comparisonIndex={$dataKey}";
	}

} elseif ($action == 10){
	//Given samlpe indexes, forward to dashboard
	$_POST['index'] 	= array_clean($_POST['index']);

	
	if (array_size($_POST['index']) > 0){
		natsort($_POST['index']);
		$dataKey = putSQLCacheWithoutKey($_POST['index'], '', 'URL', 1);
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_dashboard_sample.php?sampleIndex={$dataKey}";
	}
} elseif ($action == 11){
	//Given gene indexes, forward to dashboard
	$_POST['index'] 	= array_clean($_POST['index']);

	$results = getSQLCache($_POST['urlKey']);
	

	
	if (array_size($_POST['index']) > 0){
		
		$sessionKey = getUniqueID();
		
		$_SESSION['Multiple_Bubble_Plot'][$sessionKey]['Comparisons'] = $results['ComparisonIDs'];
		$_SESSION['Multiple_Bubble_Plot'][$sessionKey]['Genes']
			= get_multiple_record('Gene', $_POST['index'], 'GetCol', 'GeneName', 0);
			
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}plot/bubble/multiple.php?meta=true&Session={$sessionKey}";
	}
} elseif ($action == 12){
	//Given project indexes, forward to dashboard
	$_POST['index'] 	= array_clean($_POST['index']);

	
	if (array_size($_POST['index']) > 0){
		natsort($_POST['index']);
		$dataKey = putSQLCacheWithoutKey($_POST['index'], '', 'URL', 1);
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_dashboard_project.php?projectIndex={$dataKey}";
	}
} elseif ($action == 13){
	//Given indexes, forward to update tool
	$_POST['index'] 	= array_clean($_POST['index']);

	if (array_size($_POST['index']) > 0){
		natsort($_POST['index']);
		$dataKey = putSQLCacheWithoutKey($_POST['index'], '', 'URL', 1);
		echo "{$BXAF_CONFIG['BXAF_APP_URL']}gene_expressions/app_record_update.php?Category={$_POST['Category']}&recordIndex={$dataKey}";
	}
}


?>