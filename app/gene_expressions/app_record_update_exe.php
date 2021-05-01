<?php
include_once('config_init.php');

$recordIndexes = explode(',', trim($_POST['IDs']));
$recordIndexes = array_clean($recordIndexes);

if (array_size($recordIndexes) <= 0){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " No internal data has been selected. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'danger');
		echo "</div>";
	echo "</div>";
	exit();
}

$category = $_POST['Category'];
if ($APP_CONFIG['APP']['List_Category'][$category]['Edit'] == ''){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The category is missing. Please verify your URL and try again.</p>";
			echo getAlerts($message, 'danger');
		echo "</div>";
	echo "</div>";
	exit();
}

$values = array();
foreach($_POST['Choices'] as $tempKey => $currentSQL){
	if ($_POST["{$currentSQL}_Choice"] == 1){
		$values[$currentSQL] = trim($_POST["{$currentSQL}_Value"]);
	}
}


if (array_size($values) <= 0){
	echo "<div class='row'>";
		echo "<div class='col-12'>";
			$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " Please select at least an item to update.</p>";
			echo getAlerts($message, 'danger');
		echo "</div>";
	echo "</div>";
	exit();
}


$results = updateRecords($category, $recordIndexes, $values);

if (true){
	$message = printFontAwesomeIcon('fas fa-check text-success') . " The records have been updated.";
	echo getAlerts($message, 'success');
	exit();	
}



?>

<script type="text/javascript">

$(document).ready(function(){
	window.location = "app_research_project_review.php?ID=<?php echo $results['ID']; ?>";
});

</script>




