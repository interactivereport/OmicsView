<?php
include_once('config_init.php');


echo "<hr/>";

cleanInternalDataInput($_POST['data_source'], $_POST['data_source_private_project_indexes']);

$category 		= $_POST['Category'];
$categoryLower 	= strtolower($category);

if ($_POST['data_source']['private'] == ''){
	$_POST['fast'] = 1;
} else {
	$_POST['fast'] = 0;
}

$SQL_TABLE = $APP_CONFIG['APP']['List_Category'][$category]['Table'];

if ($SQL_TABLE == ''){
	$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please verify your URL and try again.</div>";
	echo getAlerts($message, 'danger');
	exit();
} else {
	
	$allColumns = getTableColumnNamesTitle($SQL_TABLE);
}


$rowCount = abs(intval($_POST['rowCount']));
if ($rowCount == 0){
	$searchAll = 1;	
} else {

	unset($SQL_CONDITIONS);
	for ($i = 1; $i <= $rowCount; $i++){
		
		unset($currentSQLCondition);
		
		$field = $_POST["Field_{$i}"];
		
		if (!$_POST['API']){
			if ($allColumns[$field] == '') continue;
		}
		
		
		$value = addslashes($_POST["Value_{$i}"]);
		if ($value == '') continue;
		
		$value = str_replace('**', '*', $value);
		
		if (strpos($value, '*') !== FALSE){
			
			$tempValue = str_replace('*', '%', $value);
			$currentSQLCondition = "(`{$field}` LIKE '{$tempValue}')";
			
			unset($searchRow);
			$searchRow['Field'] 	= $field;
			$searchRow['Operator'] = 0;
			$searchRow['Value'] 	= $value;
			$searchRow['Logic'] 	= $_POST["Logic_{$i}"];
			$searchRow['SQL']		= $currentSQLCondition;
			
			if (!isset($dataArray['Search'])){
				$dataArray['Search'][1] = $searchRow;
			} else {
				$dataArray['Search'][] = $searchRow;
			}

			
		} else {
			
			$tempValue = $value;
			
			$operator = intval($_POST["Operator_{$i}"]);
			
			if (!$_POST['API']){
				if ($APP_CONFIG['APP']['Search']['Operator'][$operator] == ''){
					continue;
				}
			}
			
			if ($operator == 0){
				$currentSQLCondition = "(`{$field}` LIKE '%{$tempValue}%')";
			} elseif ($operator == 1){
				$currentSQLCondition = "(`{$field}` = '{$tempValue}')";
			} elseif ($operator == 2){
				$currentSQLCondition = "(`{$field}` != '{$tempValue}')";
			} elseif ($operator == 3){
				$currentSQLCondition = "(`{$field}` LIKE '{$tempValue}%')";
			} elseif ($operator == 4){
				$currentSQLCondition = "(`{$field}` LIKE '%{$tempValue}')";
			} elseif ($operator == 5){
				$currentSQLCondition = "(`{$field}` IN ({$tempValue}))";
			}
			
			
			unset($searchRow);
			$searchRow['Field'] 	= $field;
			$searchRow['Operator'] 	= $operator;
			$searchRow['Value'] 	= $value;
			$searchRow['Logic'] 	= $_POST["Logic_{$i}"];
			$searchRow['SQL']		= $currentSQLCondition;
			
			if (!isset($dataArray['Search'])){
				$dataArray['Search'][1] = $searchRow;
			} else {
				$dataArray['Search'][] = $searchRow;
			}
		}
	}
}


if ($_POST['data_source']['public'] != ''){
	$SQL_TABLE = $APP_CONFIG['APP']['List_Category'][$category]['Table'];
	
	if (!isset($dataArray['Search'])){
		$SQL_Data 	= "SELECT * FROM `{$SQL_TABLE}`";		
		$SQL_Count 	= "SELECT count(*) FROM `{$SQL_TABLE}`";			
	} else {
		$SQL_Data = "SELECT * FROM `{$SQL_TABLE}` WHERE";
		$SQL_Count = "SELECT count(*) FROM `{$SQL_TABLE}` WHERE";
		foreach($dataArray['Search'] as $tempKey => $tempValue){
			$SQL_Data .= " {$tempValue['Logic']} {$tempValue['SQL']}";
			$SQL_Count .= " {$tempValue['Logic']} {$tempValue['SQL']}";
		}
	}
	
	$SQLs['Data'][$SQL_TABLE] = $SQL_Data;
	$SQLs['Count'][$SQL_TABLE] = $SQL_Count;
}


if ($_POST['data_source']['private'] != ''){
	
	$projectIndexString = implode(',', $_POST['data_source_private_project_indexes']);
	$SQL_TABLE = $APP_CONFIG['APP']['List_Category'][$category]['Table_User'];
	
	$SQL_Data 	= "SELECT * FROM `{$SQL_TABLE}` WHERE (`ProjectIndex` IN ({$projectIndexString}))";	
	$SQL_Count 	= "SELECT count(*) FROM `{$SQL_TABLE}` WHERE (`ProjectIndex` IN ({$projectIndexString}))";

	if (isset($dataArray['Search'])){
		
		$SQL_Data .= ' AND (';
		$SQL_Count .= ' AND (';
		
		foreach($dataArray['Search'] as $tempKey => $tempValue){
			$SQL_Data .= " {$tempValue['Logic']} {$tempValue['SQL']}";
			$SQL_Count .= " {$tempValue['Logic']} {$tempValue['SQL']}";
		}
		
		$SQL_Data .= ' )';
		$SQL_Count .= ' )';
	}
	
	$SQLs['Data'][$SQL_TABLE] = $SQL_Data;
	$SQLs['Count'][$SQL_TABLE] = $SQL_Count;
	
}



$totalCount  = 0;
foreach($SQLs['Count'] as $SQL_TABLE => $SQL){
	
	$SQLs['Result_Count'][$SQL_TABLE] = getSQL($SQL, 'GetOne', $SQL_TABLE);
	
	if ($SQLs['Result_Count'][$SQL_TABLE] > 0){
		
		if ($_POST['fast']){
			$currentSQL = "{$SQLs['Data'][$SQL_TABLE]} ORDER BY `ID` LIMIT {$APP_CONFIG['APP']['Table']['PerPage']['Max']}";
		} else {
			$currentSQL = $SQLs['Data'][$SQL_TABLE];
			$SQLs['Result'][$SQL_TABLE]	= getSQL($currentSQL, 'GetArray', $SQL_TABLE);
		}
	}
	
	
	$totalCount += $SQLs['Result_Count'][$SQL_TABLE];
}



if ($totalCount <= 0){
	$message = "<div>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning') . " The search result does not contain any data. Please refine your search conditions and try again.</div>";
	echo getAlerts($message, 'warning');
	exit();	
}


//*****************************************************

$dataArray['POST'] = $_POST;

$urlKey = putSQLCacheWithoutKey($dataArray, '', 'URL', 1);

$totalCount = number_format($totalCount);

echo "<h3>{$APP_CONFIG['APP']['List_Category'][$category]['List of Records'] } ({$totalCount})</h3>";
echo "<br/>";


unset($actions, $feedback);





if (true){
	$actions[] = "<a href='javascript:void(0);' class='createListTrigger btn btn-success button_text'>" . printFontAwesomeIcon('far fa-file') . "&nbsp;{$APP_CONFIG['APP']['List_Category'][$category]['Save_Titles']}</a>";
	
	$feedback[] = "<div id='Record_Required_createListTrigger' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
}


if ($category == 'Project'){
	if (true){
		$actions[] = "<a href='javascript:void(0);' class='projectToSampleTrigger btn btn-info button_text'>" . printFontAwesomeIcon('far fa-file') . "&nbsp;Create a Sample List</a>";
		
		$feedback[] = "<div id='projectToSample_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
				
		$feedback[] = "<div id='projectToSample_Missing_Result' class='startHidden text-warning errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning')  . " The selected projects do not contain any sample information.</div>";
	}
	
	
	if (!$BXAF_CONFIG['HIDE_Comparison_Tools']){
		$actions[] = "<a href='javascript:void(0);' class='projectToDashboardTrigger btn btn-danger button_text'>" . printFontAwesomeIcon('fas fa-chart-pie') . "&nbsp;Comparison Dashboard</a>";
				
		$feedback[] = "<div id='projectToDashboard_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
				
		$feedback[] = "<div id='projectToDashboard_Missing_Result' class='startHidden text-warning errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning')  . " The selected projects do not contain any sample information.</div>";
	}
	
	
}


if ($category == 'Comparison'){
	if (true){
		$actions[] = "<a href='javascript:void(0);' class='comparisonToSampleTrigger btn btn-info button_text'>" . printFontAwesomeIcon('far fa-file') . "&nbsp;Create a Sample List</a>";
		
		$feedback[] = "<div id='comparisonToSample_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
				
		$feedback[] = "<div id='comparisonToSample_Missing_Result' class='startHidden text-warning errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning')  . " The selected comparisons do not contain any sample information.</div>";
	}
	

	if (true){
		$actions[] = "<a href='javascript:void(0);' class='comparisonToSignificantTrigger btn btn-danger button_text'>" . printFontAwesomeIcon('far fa-file') . "&nbsp;Significantly Changed Genes</a>";
				
		$feedback[] = "<div id='comparisonToSignificant_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
				
		$feedback[] = "<div id='comparisonToSignificant_Missing_Result' class='startHidden text-warning errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning')  . " The selected comparisons do not contain any comparison information.</div>";
	}
	
	
	if (!$_POST['API']){
		$actions[] = "<a href='javascript:void(0);' class='comparisonToDashboardTrigger btn btn-warning button_text'>" . printFontAwesomeIcon('fas fa-chart-pie') . "&nbsp;Comparison Dashboard</a>";
				
		$feedback[] = "<div id='comparisonToDashboard_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
				
		$feedback[] = "<div id='comparisonToDashboard_Missing_Result' class='startHidden text-warning errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning')  . " The selected comparisons do not contain any comparison information.</div>";
	}

}


if ($category == 'Sample'){
	if (!$_POST['API']){
		$actions[] = "<a href='javascript:void(0);' class='sampleToDashboardTrigger btn btn-info button_text'>" . printFontAwesomeIcon('fas fa-chart-pie') . "&nbsp;Sample Dashboard</a>";
				
		$feedback[] = "<div id='sampleToDashboard_Missing_Record' class='startHidden text-danger errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger')  . " Please select at least a {$APP_CONFIG['APP']['List_Category'][$category]['name']} record first.</div>";
				
		$feedback[] = "<div id='sampleToDashboard_Missing_Result' class='startHidden text-warning errorMessage'>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-warning')  . " The selected samples do not contain any sample information.</div>";
	}
}


$getTableColumnPreferences 	= getTableColumnPreferences($category);


if (($category == 'Comparison') || ($category == 'Gene')){
	$needActionColumn = true;
} else {
	$needActionColumn = false;
}

unset($dataHTML, $dataPrint);
unset($exportHeaderCount);

$exportHeaderCount++;
$dataHTML['Headers']['Checkbox'] = "<div class='text-center'><input type='checkbox' class='selectAllTrigger'/></div>";
if ($needActionColumn){
	$exportHeaderCount++;
	$dataHTML['Headers']['Actions'] = 'Actions';
}


foreach($getTableColumnPreferences as $columnKey => $columnDetail){
	$currentSQL 	= $columnDetail['SQL'];
	$currentTitle	= $columnDetail['Title'];
	
	$dataHTML['Headers'][$currentSQL] = $currentTitle;
	$dataPrint['Headers'][$currentSQL] = $currentTitle;
	$exportHeaderCount++;
}

$dataHTML['Body'] = array();
$dataPrint['Body'] = array();

if (!$_POST['fast']){
	foreach($SQLs['Result'] as $SQL_TABLE => $currentResults){
		$dataHTML['Body'] 	+= processData($category, $currentResults, $getTableColumnPreferences, 'HTML');
		$dataPrint['Body'] 	+= processData($category, $currentResults, $getTableColumnPreferences, 'Print');
		
	}
}


unset($tableOption);
$tableOption['id'] 		= 'resultTable';



for ($i = 0; $i < $exportHeaderCount; $i++){
	
	if ($i == 0) continue;
	
	if (($needActionColumn) && ($i == 1)){
		continue;
	}
	
	$tableOption['exportOptions'][] = $i;	
}


$tableOption['exportOptions'] = implode(',', $tableOption['exportOptions']);



$tableOption['headers']	= $dataHTML['Headers'];
if (!$_POST['fast']){
	$tableOption['dataKey']	= putSQLCacheWithoutKey($dataHTML, '', 'dataTableHTMLKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
}

if ($_POST['API']){
	$tableOption['disableButton'] = true;	
} else {
	$tableOption['disableButton'] = false;
}
$tableOption['order']	= '2, "asc"';
$tableOption['pageLength']	= 100;



$dataPrintKey = putSQLCacheWithoutKey($dataPrint, '', 'dataTablePrintKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);




//Checkbox
$tableOption['columnScript'][] = '{"orderable": false}';
//Actions
if ($needActionColumn){
	$tableOption['columnScript'][] = '{"orderable": false}';
}

foreach($getTableColumnPreferences as $columnKey => $columnDetail){
	$tableOption['columnScript'][] = 'null';
}


$tableOption['columnScript'] = implode(',', $tableOption['columnScript']);
$tableOption['Extra']['SQL_Data_All'] 	= $SQLs['Data'][$APP_CONFIG['APP']['List_Category'][$category]['Table']];
$tableOption['Extra']['SQL_Count']		= $SQLs['Count'][$APP_CONFIG['APP']['List_Category'][$category]['Table']];
$tableOption['Extra']['Count']			= $SQLs['Result_Count'][$APP_CONFIG['APP']['List_Category'][$category]['Table']];
$tableOption['Extra']['Fast_Mode']		= $_POST['fast'];
$tableOption['Extra']['category']		= $category;
$tableOption['Extra']['SQL_Table']		= $APP_CONFIG['APP']['List_Category'][$category]['Table'];
$tableOption['Extra']['SQL_Search']		= $dataArray['Search'];

$tableOption['tableOptionKey'] 			= putSQLCacheWithoutKey($tableOption, '', 'tableOptionKey', 1, $APP_CONFIG['APP']['Cache_Expiration_Length']);
$tableOption['searchKeyword']			= $_POST['searchKeyword'];




if (!$APP_CONFIG['EXPORT_LIMIT']){
	if ($_POST['fast']){
		$actions[] = "<a href='app_common_table_download_fast.php?key={$dataPrintKey}&tableOptionKey={$tableOption['tableOptionKey']}&filename=search_result.csv' class='btn btn-secondary button_text'>" . 
						printFontAwesomeIcon('fas fa-download') . "&nbsp;Download</a>";
	} else {
		$actions[] = "<a href='app_common_table_download.php?key={$dataPrintKey}&filename=search_result.csv' class='btn btn-secondary button_text'>" . 
						printFontAwesomeIcon('fas fa-download') . "&nbsp;Download</a>";
		
	}
}

if (!$_POST['Simple']){
	echo "<div>" . implode('&nbsp; &nbsp;', $actions) . "</div>";
	echo "<br/>";
	echo "<div>" . implode("</div><div>", $feedback) . "</div>";
	echo "<br/>";
}

include('app_common_table_html.php');



?>

<script type="text/javascript">

$(document).ready(function(){
	
	<?php if (!$_POST['Simple'] && $_POST['bookmark'] && ($urlKey != '')){ ?>
	if ('function' == typeof window.history.pushState) {
		var URL = "<?php echo $_POST['URL']; ?>?Category=<?php echo $category; ?>&key=<?php echo $urlKey; ?>";
		var urlObject = { foo: "bar" };
		window.history.pushState(urlObject, "<?php echo $APP_CONFIG['APP']['List_Category'][$category]['Review_Titles']; ?>", URL);
	}
	<?php } ?>
	
	<?php if ($_POST['searchKeyword'] != ''){ ?>
	$('#searchKeyword').val('');
	<?php } ?>

});

</script>

<style>
.button_text{
	font-size:smaller;
}

</style>