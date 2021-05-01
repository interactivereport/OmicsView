<?php

function setRecordCount(){
	
	global $APP_CONFIG, $BXAF_CONFIG;
	
	$privateProjectIDs		= internal_data_get_accessible_project();
	$privateProjectCount 	= array_size($privateProjectIDs);
	$privateProjectIDs 		= implode(',', array_keys($privateProjectIDs));
	
	
	if (get_gene_type() == 'Gene'){
		if ($_SESSION['Record_Number']['Gene'] <= 0){
			$_SESSION['Record_Number']['Gene'] = getTableCount($APP_CONFIG['APP']['List_Category']['Gene']['Table']);
		}
	} elseif (get_gene_type() == 'Protein'){
		if ($_SESSION['Record_Number']['Protein'] <= 0){
			$_SESSION['Record_Number']['Protein'] = getTableCount($APP_CONFIG['APP']['List_Category']['Gene']['Table']);
		}
	}
	
	if (true){
		if ($_SESSION['Cache']['Project_Public'] <= 0){
			$_SESSION['Cache']['Project_Public'] = getTableCount('Projects');
		}
		
		if ($privateProjectCount > 0){
			$_SESSION['Cache']['Project_Private'] = $privateProjectCount;
		} else {
			$_SESSION['Cache']['Project_Private'] = 0;	
		}
		$_SESSION['Record_Number']['Project'] = $_SESSION['Cache']['Project_Public'] + $_SESSION['Cache']['Project_Private'];
	}
	
	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		if ($_SESSION['Cache']['Comparison_Public'] <= 0){
			$_SESSION['Cache']['Comparison_Public'] = getTableCount('Comparisons');
		}
		
		if ($privateProjectCount > 0){
			$sql_table									= 'App_User_Data_Comparisons';
			$SQL 										= "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
			$_SESSION['Cache']['Comparison_Private'] 	= getSQL($SQL, 'GetOne', $sql_table, 0, 0);
		} else {
			$_SESSION['Cache']['Comparison_Private']	= 0;	
		}
		$_SESSION['Record_Number']['Comparison'] = $_SESSION['Cache']['Comparison_Public'] + $_SESSION['Cache']['Comparison_Private'];
	}
	
	
	if (true){
		if ($_SESSION['Cache']['Sample_Public'] <= 0){
			$_SESSION['Cache']['Sample_Public'] = getTableCount('Samples');
		}
		
		if ($privateProjectCount > 0){
			$sql_table								= 'App_User_Data_Samples';
			$SQL 									= "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
			$_SESSION['Cache']['Sample_Private'] 	= getSQL($SQL, 'GetOne', $sql_table, 0, 0);
		} else {
			$_SESSION['Cache']['Sample_Private']	= 0;	
		}
		$_SESSION['Record_Number']['Sample'] = $_SESSION['Cache']['Sample_Public'] + $_SESSION['Cache']['Sample_Private'];
	}

	return true;
}

function getDashboardProjectDataCount(){
	
	$getUserSettings = getUserSettings();
	
	if ($getUserSettings['Data_Source'] == 'all'){
		$needPublic 	= 1;
		$needPrivate 	= 1;
	} elseif ($getUserSettings['Data_Source'] == 'public'){
		$needPublic 	= 1;
		$needPrivate 	= 0;
	} elseif ($getUserSettings['Data_Source'] == 'private'){
		$needPublic 	= 0;
		$needPrivate 	= 1;
	}
	
	
	if ($needPublic){
		$sql_table		= 'Projects';
		$countPublic 	= getTableCount($sql_table);
	} 
	
	if ($needPrivate){
		$sql_table	= 'App_User_Data_Projects';
		$privateProjectIDs = internal_data_get_accessible_project();
		$countPrivate = array_size($privateProjectIDs);
	}
	
	return intval($countPublic) + intval($countPrivate);
}

function getDashboardSampleDataCount($sampleIndex = array()){
	
	$getUserSettings = getUserSettings();
	
	if ($getUserSettings['Data_Source'] == 'all'){
		$needPublic 	= 1;
		$needPrivate 	= 1;
	} elseif ($getUserSettings['Data_Source'] == 'public'){
		$needPublic 	= 1;
		$needPrivate 	= 0;
	} elseif ($getUserSettings['Data_Source'] == 'private'){
		$needPublic 	= 0;
		$needPrivate 	= 1;
	}
	
	$sampleIndexString = implode(',', $sampleIndex);
	
	if ($needPublic){
		$sql_table	= 'Samples';
		
		if ($sampleIndexString == ''){
			$SQL = "SELECT count(*) FROM `{$sql_table}`";
		} else {
			$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `SampleIndex` IN ({$sampleIndexString})";
		}
		$countPublic = getSQL($SQL, 'GetOne', $sql_table);
	} 
	
	if ($needPrivate){
		$sql_table	= 'App_User_Data_Samples';
		$privateProjectIDs = internal_data_get_accessible_project();
		$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
		if ($privateProjectIDs != ''){
			if ($sampleIndexString == ''){
				$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
			} else {
				$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs}) AND (`SampleIndex` IN ({$sampleIndexString}))";
			}
			$countPrivate = getSQL($SQL, 'GetOne', $sql_table, 0, 0);
		}
	}
	
	return intval($countPublic) + intval($countPrivate);
}

function prepareDashboardData_Sample($sampleIndexes = array()){
	
	global $APP_CONFIG;
	

	
	$allColumns = array_column($APP_CONFIG['Dashboard']['Charts_Sample'], 'Column');
	
	
	if (array_size($allColumns) > 0){
		
		$getUserSettings = getUserSettings();
	
		$SQL_COLUMNS = '`' . implode('`,`', $allColumns) . '`';
		
		$sql_table	= 'Samples';
		
		
		$sampleIndexes = array_filter($sampleIndexes, 'is_numeric');
		$sampleIndexes = implode(',', $sampleIndexes);	


		if ($sampleIndexes == ''){
			
			$getUserSettings = getUserSettings();
	
			if ($getUserSettings['Data_Source'] == 'all'){
				$allSampleData	= get_multiple_record('Sample', '', 'GetArray', "`SampleIndex`, {$SQL_COLUMNS}", 1);
			} elseif ($getUserSettings['Data_Source'] == 'public'){
				
				$sql_table			= 'Samples';
				$SQL 				= "SELECT `SampleIndex`, {$SQL_COLUMNS} FROM `{$sql_table}`";
				$allSampleData 	= getSQL($SQL, 'GetArray', $sql_table);
				
			} elseif ($getUserSettings['Data_Source'] == 'private'){
				
				$sql_table			= 'App_User_Data_Samples';
				$privateProjectIDs 	= internal_data_get_accessible_project();
				$privateProjectIDs 	= implode(',', array_keys($privateProjectIDs));
				if ($privateProjectIDs != ''){
					$SQL 				= "SELECT `SampleIndex`, {$SQL_COLUMNS} FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
					$allSampleData = getSQL($SQL, 'GetArray', $sql_table, 0, 0);
				}
				
			}
			
			
			
		} else {
			$allSampleData	= get_multiple_record('Sample', $sampleIndexes, 'GetArray', "`SampleIndex`, {$SQL_COLUMNS}", 0);
		}
		
		

		if (array_size($allSampleData) > 0){
			
			foreach($APP_CONFIG['Dashboard']['Charts_Sample'] as $tempKey => $currentChart){
				
				
				if (array_size($currentChart['Unknown_Keywords']) > 0){
					$currentChart['Unknown_Keywords'] = array_combine($currentChart['Unknown_Keywords'], $currentChart['Unknown_Keywords']);
				}
					
				foreach($allSampleData as $tempKeyX => $tempValueX){
					$currentValue = $allSampleData[$tempKeyX][$currentChart['Column']];
						
						
					if ($currentValue == ''){
						$allSampleData[$tempKeyX][$currentChart['Column']] = 'Unknown';
					} elseif (isset($currentChart['Unknown_Keywords'][$currentValue])){
						$allSampleData[$tempKeyX][$currentChart['Column']] = 'Unknown';
					}
				}
				
				
				
				if ($currentChart['Top'] > 0){
					
					$histogram = array_count_values(array_column($allSampleData, $currentChart['Column']));
					

					
					unset($histogram['']);
					unset($histogram['No Info']);
					unset($histogram[$currentChart['Others']]);
					unset($histogram['Unknown']);
							
					arsort($histogram);
					$slice = 1;
					
					
					if ($slice){					
						$histogram = array_slice($histogram, 0, $currentChart['Top']);
					}
					
					
					$candidates = array_combine(array_keys($histogram), array_keys($histogram));
					foreach($allSampleData as $tempKeyX => $tempValueX){
						if (!isset($candidates[$tempValueX[$currentChart['Column']]])){
							$allSampleData[$tempKeyX][$currentChart['Column']] = $currentChart['Others'];
						} else {
							$allSampleData[$tempKeyX][$currentChart['Column']] = ucwords(trim($allSampleData[$tempKeyX][$currentChart['Column']]));
						}
						
					}
					
				}
			}
		}
		
		
		return $allSampleData;
		
	} else {
		return false;	
	}
	
}

function getDashboardComparisonDataCount($comparisonIndexes = array()){
	
	$getUserSettings = getUserSettings();
	
	if ($getUserSettings['Data_Source'] == 'all'){
		$needPublic 	= 1;
		$needPrivate 	= 1;
	} elseif ($getUserSettings['Data_Source'] == 'public'){
		$needPublic 	= 1;
		$needPrivate 	= 0;
	} elseif ($getUserSettings['Data_Source'] == 'private'){
		$needPublic 	= 0;
		$needPrivate 	= 1;
	}

	$comparisonIndexes = array_filter($comparisonIndexes, 'is_numeric');
	$comparisonIndexes = implode(',', $comparisonIndexes);	
	
	
	if ($comparisonIndexes == ''){
		
		if ($needPublic){
			$sql_table	= 'Comparisons';
			$SQL = "SELECT count(*) FROM `{$sql_table}`";
			$countPublic = getSQL($SQL, 'GetOne', $sql_table);
		} 
		
		if ($needPrivate){
			$sql_table	= 'App_User_Data_Comparisons';
			$privateProjectIDs = internal_data_get_accessible_project();
			$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
			if ($privateProjectIDs != ''){
				$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
				$countPrivate = getSQL($SQL, 'GetOne', $sql_table, 0, 0);
			}
		}
		
	} else {
		if (true){
			$sql_table	= 'Comparisons';
			$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE (`ComparisonIndex` IN ({$comparisonIndexes}))";
			$countPublic = getSQL($SQL, 'GetOne', $sql_table);
		}
		
		if (true){
			$sql_table	= 'App_User_Data_Comparisons';
			$privateProjectIDs = internal_data_get_accessible_project();
			$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
			if ($privateProjectIDs != ''){
				$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE (`ProjectIndex` IN ({$privateProjectIDs})) AND (`ComparisonIndex` IN ({$comparisonIndexes}))";
				$countPrivate = getSQL($SQL, 'GetOne', $sql_table, 0, 0);
			}
		}
		
	}
	
	
	return intval($countPublic) + intval($countPrivate);
	
}

function prepareDashboardData_Comparison($comparisonIndexes = array()){
	
	global $APP_CONFIG;
	

	
	$allColumns = array_column($APP_CONFIG['Dashboard']['Charts'], 'Column');
	
	if (array_size($allColumns) > 0){
		
		$getUserSettings = getUserSettings();
	
		$SQL_COLUMNS = '`' . implode('`,`', $allColumns) . '`';
		
		$sql_table	= 'Comparisons';
		
		
		$comparisonIndexes = array_filter($comparisonIndexes, 'is_numeric');
		$comparisonIndexes = implode(',', $comparisonIndexes);	


		if ($comparisonIndexes == ''){
			
			$getUserSettings = getUserSettings();
	
			if ($getUserSettings['Data_Source'] == 'all'){

				$allComparisonData	= get_multiple_record('Comparison', '', 'GetArray', "`ComparisonIndex`, {$SQL_COLUMNS}", 1);
			} elseif ($getUserSettings['Data_Source'] == 'public'){
				
				$sql_table			= 'Comparisons';
				$SQL 				= "SELECT `ComparisonIndex`, {$SQL_COLUMNS} FROM `{$sql_table}`";
				
				$allComparisonData 	= getSQL($SQL, 'GetArray', $sql_table);
				
			} elseif ($getUserSettings['Data_Source'] == 'private'){
				
				$sql_table			= 'App_User_Data_Comparisons';
				$privateProjectIDs 	= internal_data_get_accessible_project();
				$privateProjectIDs 	= implode(',', array_keys($privateProjectIDs));
				if ($privateProjectIDs != ''){
					$SQL 				= "SELECT `ComparisonIndex`, {$SQL_COLUMNS} FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
					$allComparisonData = getSQL($SQL, 'GetArray', $sql_table, 0, 0);
				}
				
			}
			
			
			
		} else {
			$allComparisonData	= get_multiple_record('Comparison', $comparisonIndexes, 'GetArray', "`ComparisonIndex`, {$SQL_COLUMNS}", 0);
		}
		
		

		if (array_size($allComparisonData) > 0){
			
			foreach($APP_CONFIG['Dashboard']['Charts'] as $tempKey => $currentChart){
				
				
				if (array_size($currentChart['Unknown_Keywords']) > 0){
					$currentChart['Unknown_Keywords'] = array_combine($currentChart['Unknown_Keywords'], $currentChart['Unknown_Keywords']);
				}
					
				foreach($allComparisonData as $tempKeyX => $tempValueX){
					$currentValue = $allComparisonData[$tempKeyX][$currentChart['Column']];
						
						
					if ($currentValue == ''){
						$allComparisonData[$tempKeyX][$currentChart['Column']] = 'Unknown';
					} elseif (isset($currentChart['Unknown_Keywords'][$currentValue])){
						$allComparisonData[$tempKeyX][$currentChart['Column']] = 'Unknown';
					}
				}
				
				
				
				if ($currentChart['Top'] > 0){
					
					$histogram = array_count_values(array_column($allComparisonData, $currentChart['Column']));

					unset($histogram['']);
					
					
					if ($currentChart['Column'] == 'Case_CellType'){
						if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_cell_type'])){
							unset($histogram['Unknown']);
						}
					} elseif ($currentChart['Column'] == 'Case_DiseaseState'){
						if (in_array('Hide Normal Control', $getUserSettings['dashboard_chart_disease_state'])){
							unset($histogram['normal control'], $histogram['Normal Control']);
						}
						
						if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_disease_state'])){
							unset($histogram['Unknown']);
						}
					} elseif ($currentChart['Column'] == 'Case_Treatment'){
						if (in_array('Hide Unknown', $getUserSettings['dashboard_chart_treatment'])){
							unset($histogram['Unknown']);
						}
					}
							
					arsort($histogram);
					unset($slice);			
					if ($currentChart['Column'] == 'Case_CellType'){
						if (in_array('Show Top 15 (Uncheck to show all)', $getUserSettings['dashboard_chart_cell_type'])){
							$slice = 1;
						} else {
							$slice = 0;	
						}
						
						
						if ($slice){
							if (!in_array('Hide Others', $getUserSettings['dashboard_chart_cell_type'])){
								$currentChart['Top']--;
							}
						}
						
					} elseif ($currentChart['Column'] == 'Case_DiseaseState'){
						if (in_array('Show Top 15 (Uncheck to show all)', $getUserSettings['dashboard_chart_disease_state'])){
							$slice = 1;	
						} else {
							$slice = 0;	
						}
						
						if ($slice){
							if (!in_array('Hide Others', $getUserSettings['dashboard_chart_disease_state'])){
								$currentChart['Top']--;
							}
						}
						
						
					} else {
						$slice = 1;	
					}
					
					
					if ($slice){					
						$histogram = array_slice($histogram, 0, $currentChart['Top']);
					}
					
					
					$candidates = array_combine(array_keys($histogram), array_keys($histogram));
					foreach($allComparisonData as $tempKeyX => $tempValueX){
						if (!isset($candidates[$tempValueX[$currentChart['Column']]])){
							$allComparisonData[$tempKeyX][$currentChart['Column']] = $currentChart['Others'];
						} else {
							$allComparisonData[$tempKeyX][$currentChart['Column']] = ucwords(trim($allComparisonData[$tempKeyX][$currentChart['Column']]));
						}
						
					}
					
				}
			}
		}
		
		
		return $allComparisonData;
		
	} else {
		return false;	
	}
	
}

function getDashboardProjectDataCount2($projectIndex = array()){
	
	$getUserSettings = getUserSettings();
	
	if ($getUserSettings['Data_Source'] == 'all'){
		$needPublic 	= 1;
		$needPrivate 	= 1;
	} elseif ($getUserSettings['Data_Source'] == 'public'){
		$needPublic 	= 1;
		$needPrivate 	= 0;
	} elseif ($getUserSettings['Data_Source'] == 'private'){
		$needPublic 	= 0;
		$needPrivate 	= 1;
	}
	
	$projectIndexString = implode(',', $projectIndex);
	
	if ($needPublic){
		$sql_table	= 'Projects';
		
		if ($projectIndexString == ''){
			$SQL = "SELECT count(*) FROM `{$sql_table}`";
		} else {
			$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$projectIndexString})";
		}
		$countPublic = getSQL($SQL, 'GetOne', $sql_table);
	} 
	
	if ($needPrivate){
		$sql_table	= 'App_User_Data_Projects';
		$privateProjectIDs = internal_data_get_accessible_project();
		$privateProjectIDs = implode(',', array_keys($privateProjectIDs));
		if ($privateProjectIDs != ''){
			if ($projectIndexString == ''){
				$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
			} else {
				$SQL = "SELECT count(*) FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs}) AND (`ProjectIndex` IN ({$projectIndexString}))";
			}
			$countPrivate = getSQL($SQL, 'GetOne', $sql_table, 0, 0);
		}
	}
	
	return intval($countPublic) + intval($countPrivate);
}

function prepareDashboardData_Project($projectIndexes = array()){
	
	global $APP_CONFIG;
	

	
	$allColumns = array_column($APP_CONFIG['Dashboard']['Charts_Project'], 'Column');
	
	
	if (array_size($allColumns) > 0){
		
		$getUserSettings = getUserSettings();
	
		$SQL_COLUMNS = '`' . implode('`,`', $allColumns) . '`';
		
		$sql_table	= 'Projects';
		
		
		$projectIndexes = array_filter($projectIndexes, 'is_numeric');
		$projectIndexes = implode(',', $projectIndexes);	


		if ($projectIndexes == ''){
			
			$getUserSettings = getUserSettings();
	
			if ($getUserSettings['Data_Source'] == 'all'){
				$allProjectData	= get_multiple_record('Project', '', 'GetArray', "`ProjectIndex`, {$SQL_COLUMNS}", 1);
			} elseif ($getUserSettings['Data_Source'] == 'public'){
				
				$sql_table			= 'Projects';
				$SQL 				= "SELECT `ProjectIndex`, {$SQL_COLUMNS} FROM `{$sql_table}`";
				$allProjectData 	= getSQL($SQL, 'GetArray', $sql_table);
				
			} elseif ($getUserSettings['Data_Source'] == 'private'){
				
				$sql_table			= 'App_User_Data_Projects';
				$privateProjectIDs 	= internal_data_get_accessible_project();
				$privateProjectIDs 	= implode(',', array_keys($privateProjectIDs));
				if ($privateProjectIDs != ''){
					$SQL 				= "SELECT `ProjectIndex`, {$SQL_COLUMNS} FROM `{$sql_table}` WHERE `ProjectIndex` IN ({$privateProjectIDs})";
					$allProjectData = getSQL($SQL, 'GetArray', $sql_table, 0, 0);
				}
				
			}
			
			
			
		} else {
			$allProjectData	= get_multiple_record('Project', $projectIndexes, 'GetArray', "`ProjectIndex`, {$SQL_COLUMNS}", 0);
		}
		
		

		if (array_size($allProjectData) > 0){
			
			foreach($APP_CONFIG['Dashboard']['Charts_Project'] as $tempKey => $currentChart){
				
				
				if (array_size($currentChart['Unknown_Keywords']) > 0){
					$currentChart['Unknown_Keywords'] = array_combine($currentChart['Unknown_Keywords'], $currentChart['Unknown_Keywords']);
				}
					
				foreach($allProjectData as $tempKeyX => $tempValueX){
					$currentValue = $allProjectData[$tempKeyX][$currentChart['Column']];
						
						
					if ($currentValue == ''){
						$allProjectData[$tempKeyX][$currentChart['Column']] = 'Unknown';
					} elseif (isset($currentChart['Unknown_Keywords'][$currentValue])){
						$allProjectData[$tempKeyX][$currentChart['Column']] = 'Unknown';
					}
				}
				
				
				
				if ($currentChart['Top'] > 0){
					
					$histogram = array_count_values(array_column($allProjectData, $currentChart['Column']));
					

					
					unset($histogram['']);
					unset($histogram['No Info']);
					unset($histogram[$currentChart['Others']]);
					unset($histogram['Unknown']);
							
					arsort($histogram);
					$slice = 1;
					
					
					if ($slice){					
						$histogram = array_slice($histogram, 0, $currentChart['Top']);
					}
					
					
					$candidates = array_combine(array_keys($histogram), array_keys($histogram));
					foreach($allProjectData as $tempKeyX => $tempValueX){
						if (!isset($candidates[$tempValueX[$currentChart['Column']]])){
							$allProjectData[$tempKeyX][$currentChart['Column']] = $currentChart['Others'];
						} else {
							$allProjectData[$tempKeyX][$currentChart['Column']] = ucwords(trim($allProjectData[$tempKeyX][$currentChart['Column']]));
						}
						
					}
					
				}
			}
		}
		
		
		return $allProjectData;
		
	} else {
		return false;	
	}
	
}




?>