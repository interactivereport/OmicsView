<?php
include_once('config.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>GSEA</title>
<link rel="icon" href="../../img/logo_address_bar.png">

<link href="../library/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">

<script type="text/javascript" src="../library/jquery.min.3.1.0.js"></script>
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../library/plotly.min.js"></script>
<script type="text/javascript" src="../library/bootbox.js"></script>




</head>
<body>

<div id="wrapper">

	<?php include_once("../component_header.php"); ?>

	<div id="page-wrapper">

		<div class="container-fluid">
			<h1 class="page-header">
				GSEA
			</h1>




      <form id="form_gsea" method="post" enctype="multipart/form-data">
        <div class="row">
          <input name="comparison_index" class="form-control">
          <button class="btn btn-primary" type="submit" id="btn_submit"><i class="fa fa-upload"></i> Submit</button>
        </div>
      </form>

      <div id="debug"></div>
      <div id="chart_up_div" style="width: 100%; height: 600px;"></div>
      <div id="chart_down_div" style="width: 100%; height: 600px;"></div>


	  </div>

  </div>

</div>




<script>
$(document).ready(function() {
  // $('#sidebar_link_functional_enrichment').addClass('active');
	// $('#sidebar_link_functional_enrichment').parent().parent().prev().addClass('active');
	// $('#sidebar_link_functional_enrichment').parent().parent().css('display', 'block');
	// $('#sidebar_link_functional_enrichment').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');


  // Generate Chart
	var options = {
		url: 'exe.php?action=show_chart',
 		type: 'post',
    beforeSubmit: function(formData, jqForm, options) {
			$('#btn_submit').attr('disabled', '')
                      .children(':first')
                      .removeClass('fa-upload')
                      .addClass('fa-spin fa-spinner');
			return true;
		},
    success: function(response){
			$('#btn_submit').removeAttr('disabled')
                      .children(':first')
                      .removeClass('fa-spin fa-spinner')
                      .addClass('fa-upload');
      var data_up = response.up.data;
      var layout_up = response.up.layout;
      var setting_up = response.up.setting;

      var data_down = response.down.data;
      var layout_down = response.down.layout;
      var setting_down = response.down.setting;

      Plotly.newPlot('chart_up_div', data_up, layout_up, setting_up);
      Plotly.newPlot('chart_down_div', data_down, layout_down, setting_down);

      // Click
      var graphDivUp = document.getElementById('chart_up_div');
      var graphDivDown = document.getElementById('chart_down_div');

      var clickEvent = function(data){
        var name = data.points[0].y; // Geneset Name
        $.ajax({
          type: 'POST',
          url: 'exe.php?action=go_to_volcano',
          data: {geneset_name: name},
          success: function(response) {
            //$('#debug').html(response);
            bootbox.confirm(
              "Click 'OK' to go to volcano plot page.",
              function(result){
                if (result) {
                  window.location = '../volcano/index.php?type=custom&src=gsea';
                }
              }
            );
          }
        });
      }
      graphDivUp.on('plotly_click', clickEvent);
      graphDivDown.on('plotly_click', clickEvent);

		  return true;
		}
  };

	$('#form_gsea').ajaxForm(options);


  // graphDiv.on('plotly_click', function(data){
  //   var comparison = data.points[0].data.marker.comparison_id[data.points[0].pointNumber];
  //   var comparison_index = data.points[0].data.marker.comparison_index[data.points[0].pointNumber];
  //   console.log(data.points[0]);
  //   bootbox.alert('<h4>Comparison ' + comparison + '</h4><br /><a href=\"../search_comparison/single_comparison.php?type=comparison&id='+comparison_index+'\">Comparison Detail</a><br /><a href=\"../volcano/index.php?id='+comparison_index+'\">Comparison Volcano Chart</a><br /><a href=\"../pvjs/index.php?id='+comparison_index+'\">Pathway View</a><br /><a href=\"../search_comparison/index.php?type=sample&comparison_id='+comparison_index+'\">Related Samples</a> &nbsp;');
  // });


});

</script>








</body>
</html>
