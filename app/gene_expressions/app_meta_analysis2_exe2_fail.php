<script type="text/javascript">
    
$(document).ready(function(){
	$('.comparison_group_error_section').empty();
	
	<?php
		foreach($error as $i => $tempValue){
			
			$currentErrorMessage = "<div class='alert alert-danger' role='alert'><ul><li>" . implode('</li><li>', $tempValue) . "</li></ul></div>";
			$currentErrorMessage .= implode(' ', $errorExtra[$i]);
			$currentErrorMessage = str_replace("\n", ' ', $currentErrorMessage);
	?>
	
		$('#comparison_group_error_section_<?php echo $i; ?>').html("<?php echo $currentErrorMessage; ?>");
		$('#comparison_group_error_section_<?php echo $i; ?>').show();
	
	
	<?php } ?>

});
</script>