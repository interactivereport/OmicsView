<?php
include_once('config_init.php');

$ID = intval($_POST['ID']);


if ($ID > 0){
	$results = updateList($_POST, $ID);
} else {
	$results = createList($_POST);
}


if ($results['Error']){
	
	$message = "<p>" . printFontAwesomeIcon('fas fa-exclamation-triangle text-danger') . " The system could not save the list.</p>";

	$message .= "<ul>";
	
	foreach($results['Message'] as $tempKey => $tempValue){
		$message .= "<li>{$tempValue}</li>";
	}
	
	$message .= "</ul>";
	
	echo getAlerts($message, 'danger');
	exit();
} else {
	
	$message = "<p>" . printFontAwesomeIcon('fas fa-check text-success') . "The list has been saved.</p>";
	
	echo getAlerts($message, 'success');
	$success = 1;
	
}



?>
<?php if ($success){ ?>

<script type="text/javascript">

$(document).ready(function(){
	window.location = "app_list_review.php?ID=<?php echo $results['ID']; ?>";	
	
});
</script>

<?php } ?>