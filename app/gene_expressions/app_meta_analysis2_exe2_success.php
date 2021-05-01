<?php

unset($_SESSION['MetaAnalysis_GeneExpression'][$sessionID]);
$_SESSION['MetaAnalysis_GeneExpression'][$sessionID]= $_POST;


$comparisonInput = array();
for ($i = 1; $i <= $_POST['Job']; $i++){
	$comparisonInput[$i]['Name'] 			= $_POST["Comparison{$i}_Job"];
	$comparisonInput[$i]['Case'] 			= $_POST["Comparison{$i}_Case"];
	$comparisonInput[$i]['Control'] 		= $_POST["Comparison{$i}_Control"];
	$comparisonInput[$i]['PlatformType'] 	= $platformType[$i];
}

$otherInfo = array('rank_product_analysis' => $_POST['rank_product_analysis']);

$prepareMetaAnalysisData2 = prepareMetaAnalysisData2($comparisonInput, $_POST['data_source'], $_POST['data_source_private_project_indexes'], $otherInfo);



if (true){
	unset($wizard);
	$wizard[1]['Icon'] 		= printFontAwesomeIcon('far fa-copy');
	$wizard[1]['Title']		= 'Select Comparisons';
	$wizard[1]['State']		= 2;
	$wizard[1]['Link']		= 'javascript:void(0);';
	$wizard[1]['Link-Class']= 'showForm1Trigger';
	
	
	$wizard[2]['Icon'] 		= printFontAwesomeIcon('fas fa-list-ol');
	$wizard[2]['Title']		= 'Edit Samples';
	$wizard[2]['State']		= 2;
	$wizard[2]['Link']		= 'javascript:void(0);';
	$wizard[2]['Link-Class']= 'showForm2Trigger';
	
	$wizard[3]['Icon'] 		= printFontAwesomeIcon('far fa-chart-bar');
	$wizard[3]['Title']		= 'Review Results';
	$wizard[3]['State']		= 1;
	
	echo "<div class='form-group row'>";
		echo printWizard($wizard);
	echo "</div>";
}

if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-12'>";
			echo "<h2 class='pt-3'>3. {$wizard[3]['Title']}</h2>";
			echo "<hr/>";
		echo "</div>";
	echo "</div>";
}



if (true){
	echo "<div class='form-group row'>";
		echo "<div class='col-lg-12'>";
			echo "The system is preparing the data. Please do not close this window.";
		echo "</div>";
	echo "</div>";
}


$dataArray = array();
$_POST['User_ID'] 		= $APP_CONFIG['User_Info']['ID'];
$_POST['Date']	 		= date('Y-m-d');
$_POST['DateTime'] 		= date('Y-m-d H:i:s');
$dataArray['POST'] 		= $_POST;
$dataArray['prepareMetaAnalysisData2'] = $prepareMetaAnalysisData2;
$urlKey = putSQLCacheWithoutKey($dataArray, 'app_meta_analysis2.php', 'URL', 1);



$inputArray = array();
$inputArray['Name'] 									= $_POST['name'];
$inputArray['Analysis_Type'] 							= 'Meta Analysis (Gene Expression)';
$inputArray['Parameters']['POST'] 						= $_POST;
$inputArray['Parameters']['prepareMetaAnalysisData2'] 	= $prepareMetaAnalysisData2;
$inputArray['Status'] 									= 'Running';

$results = addMetaAnalysis($inputArray);

$ID = $results['ID'];



?>
<script type="text/javascript">
$(document).ready(function(){

	$('#form_application2').hide();
	
	<?php if ($urlKey != ''){ ?>
	window.location = "app_meta_analysis2_review.php?ID=<?php echo $ID; ?>&key=<?php echo $urlKey; ?>";
	<?php } ?>
	
});

</script>

