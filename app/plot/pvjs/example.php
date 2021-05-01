<?php
include_once('config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>

<link href="../library/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../library/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">

<style  type="text/css">
a#wplink {
text-decoration:none;
font-family:serif;
color:black;
font-size:12px;
}
#logolink {
	float:right;
	top:-20px;
	left: -10px;
	position:relative;
	z-index:2;
	opacity: 0.5;
}
html, body {
	width:100%;
	height:100%;
}
#pvjs-widget {
	top:0;
	left:0;
	font-size:12px;
	width:100%;
	height:inherit;
}
</style>


<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="js/jquery.layout.min-1.3.0.js"></script>
<script type="text/javascript" src="js/d3.min.js"></script>
<script type="text/javascript" src="js/mithril.min.js"></script>
<script type="text/javascript" src="js/polyfills.bundle.min.js"></script>
<script type="text/javascript" src="js/pvjs.core.min.js"></script>
<script type="text/javascript" src="js/pvjs.custom-element.min.js"></script>

<script>kaavioHighlights = [
{"selector":"ACSL4","backgroundColor":"url(#solids)","borderColor":"#B0B0B0"},
{"selector":"ACSL3","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ACACA","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ECHDC1","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"PECR","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"MECR","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ACSL6","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ECHDC2","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ACSL5","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"DECR1","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ACAA2","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ACLY","backgroundColor":"#B0B0B0","borderColor":"#B0B0B0"},
{"selector":"ACSL1","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"ECH1","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"ACACB","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"ECHS1","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"PC","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"ACAS2","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"FASN","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"ECHDC3","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"HADHSC","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},
{"selector":"SCD","backgroundColor":"#6A03B2","borderColor":"#6A03B2"},]
</script>

<title>WikiPathways Pathway Viewer</title>
</head>
<body>


<div id="wrapper">

	<?php include_once("../component_header.php"); ?>

	<div id="page-wrapper">

		<div class="container-fluid">
			<h3 class="m-t-2">
				Pathway Visualization &nbsp;
				<a href="index.php" class="font-sanspro-300 font_normal"><i class="fa fa-angle-double-right"></i> Create Pathway Chart</a>
			</h3>
			<hr />

			<wikipathways-pvjs
				id="pvjs-widget"
				src="./gpml_test/Hs_Fatty_Acid_Biosynthesis_WP357_85181.gpml"
				display-errors="true"
				display-warnings="true"
				fit-to-container="true"
				editor="disabled">
				<!--<img src="http://www.wikipathways.org/img_auth.php/7/7c/WP357_85181.png" alt="Diagram for pathway WP357" width="900" height="630" class="thumbimage">-->
			</wikipathways-pvjs>


		</div>

	</div>

</div>





<script>

var content = '<text id="info-box-text2" class="item" x="0" y="42"><tspan class="info-box-item-property-name">Organism: </tspan><tspan class="info-box-item-property-value">Test</tspan></text>';


checkReady();

function checkReady() {
    if ($('svg')[0] == null) {
        setTimeout("checkReady()", 300);
    } else {
		createGradient($('svg')[0],'solids',[
		  {offset:'0%', 'stop-color':'rgb(100,100,100)'},
		  {offset:'33%','stop-color':'#ff0000'},
		  {offset:'33%','stop-color':'#00ff00'},
		  {offset:'67%','stop-color':'#00ff00'},
		  {offset:'67%','stop-color':'#0000ff'}
		]);







		var svgNS = "http://www.w3.org/2000/svg";

		var newText = document.createElementNS(svgNS,"text");
		newText.setAttributeNS(null,"x",0);
		newText.setAttributeNS(null,"y",42);
		newText.setAttributeNS(null,"font-size","10px");
		var textNode = document.createTextNode('Legend: ');
		newText.appendChild(textNode);
		document.getElementById("info-box-0").appendChild(newText);



		var svgimg = document.createElementNS(svgNS,'image');
		svgimg.setAttributeNS(null,'height','200');
		svgimg.setAttributeNS(null,'width','200');
		svgimg.setAttributeNS('http://www.w3.org/1999/xlink','href', 'images/gradient_1.png');
		svgimg.setAttributeNS(null,'x','10');
		svgimg.setAttributeNS(null,'y','10');
		svgimg.setAttributeNS(null, 'visibility', 'visible');
		document.getElementById("info-box-0").append(svgimg);


		var myCircle = document.createElementNS(svgNS,"rect");
		myCircle.setAttributeNS(null,"id","mycircle");
		myCircle.setAttributeNS(null,"x",100);
		myCircle.setAttributeNS(null,"y",100);
		myCircle.setAttributeNS(null,"width",50);
		myCircle.setAttributeNS(null,"height",50);
		myCircle.setAttributeNS(null,"fill","green");
		myCircle.setAttributeNS(null,"stroke","none");
		document.getElementById("info-box-0").appendChild(myCircle);


    }
}

// $('svg path').attr('fill','url(#MyGradient)');

function createGradient(svg,id,stops){
	var svgNS = svg.namespaceURI;
	var grad  = document.createElementNS(svgNS,'linearGradient');
	grad.setAttribute('id',id);
	for (var i=0;i<stops.length;i++){
		var attrs = stops[i];
		var stop = document.createElementNS(svgNS,'stop');
		for (var attr in attrs){
			if (attrs.hasOwnProperty(attr)) stop.setAttribute(attr,attrs[attr]);
		}
		grad.appendChild(stop);
	}

	var defs = svg.querySelector('defs') || svg.insertBefore( document.createElementNS(svgNS,'defs'), svg.firstChild );
	return defs.appendChild(grad);
}


var domReady = function(callback) {
    document.readyState === "interactive" || document.readyState === "complete" ? callback() : document.addEventListener("DOMContentLoaded", callback);
};


domReady(function() {
	$('#sidebar_link_pvjs').addClass('active');
	$('#sidebar_link_pvjs').parent().parent().prev().addClass('active');
	$('#sidebar_link_pvjs').parent().parent().css('display', 'block');
	$('#sidebar_link_pvjs').children(':first').removeClass('fa-circle-o').addClass('fa-dot-circle-o');


	// Comparison info template
	var comparison_info = '<table class="table table-bordered" style="font-size:11px;">';
	comparison_info +=  '<tr>';
	comparison_info +=  		'<th>Comparison</th>';
	comparison_info +=  		'<th>log2FC</th>';
	comparison_info +=  		'<th>P-Value</th>';
	comparison_info +=  '</tr>';
	comparison_info +=  '<tr>';
	comparison_info +=  		'<td>GSE61754.GPL10558.test3</td>';
	comparison_info +=  		'<td>0.87</td>';
	comparison_info +=  		'<td>0.04</td>';
	comparison_info +=  '</tr>';
	comparison_info +=  '<tr>';
	comparison_info +=  		'<td>GSE44720.GPL10558.test16</td>';
	comparison_info +=  		'<td>-0.21</td>';
	comparison_info +=  		'<td>0.13</td>';
	comparison_info +=  '</tr>';

	// Load comparison info when clicking areas
	var annotation_text = 'Header';

	$(document).on('click', 'body', function() {
		var annotation_text_new = $('.annotation-header-text').html();
		if (annotation_text_new != annotation_text) {
			$('.annotation-items-container').find('.pvjs_added').remove();
			$('.annotation-items-container').prepend('<p class="pvjs_added">Loading comparison info...</p>');
			setTimeout(function() {
				$('.pvjs_added').html(comparison_info);
			}, 2000);
		}
		annotation_text = annotation_text_new;
	});

});
</script>


</body>
</html>
