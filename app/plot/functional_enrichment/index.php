<?php
include_once('config.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Pathway Plot</title>
<link rel="icon" href="../../img/logo_address_bar.png">

<link href="../library/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">

<script type="text/javascript" src="../library/jquery.min.3.1.0.js"></script>
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../library/plotly.min.js"></script>




</head>
<body>

<div id="wrapper">

	<?php include_once("../component_header.php"); ?>

	<div id="page-wrapper">

		<div class="container-fluid">
			<h1 class="page-header">
				Functional Enrichment &nbsp;
				<a href="javascript:void(0);" class="font-sanspro-300 font_normal">
          <i class="fa fa-angle-double-right"></i>
          Load Example
        </a>
			</h1>




      <form id="form_functional_enrichment" method="post" enctype="multipart/form-data">

      <div class="row">
        <input name="comparison_id" class="form-control">
        <button type="submit">Submit</button>
      </div>

      </form>

      <div id="debug"></div>


	  </div>

  </div>

</div>




<script>
$(document).ready(function() {
  $('#sidebar_link_functional_enrichment').addClass('active');
	$('#sidebar_link_functional_enrichment').parent().parent().prev().addClass('active');
	$('#sidebar_link_functional_enrichment').parent().parent().css('display', 'block');
	$('#sidebar_link_functional_enrichment').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');


  // Generate Chart
	var options = {
		url: 'exe.php?action=show_chart',
 		type: 'post',
      beforeSubmit: function(formData, jqForm, options) {
  			$('#btn_submit').children(':first').removeClass('fa-chevron-circle-right').addClass('fa-spin fa-spinner');
  			$('#btn_submit').attr('disabled', '');
  			return true;
		},
      success: function(responseText, statusText){
  			$('#btn_submit').children(':first').removeClass('fa-spin fa-spinner').addClass('fa-chevron-circle-right');
  			$('#btn_submit').removeAttr('disabled');
        $('#debug').html(responseText);
			return true;
		}
    };
	$('#form_functional_enrichment').ajaxForm(options);


});

</script>








</body>
</html>
