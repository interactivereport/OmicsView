<?php
include_once('config.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Pathway Plot</title>

<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../css/main.css" rel="stylesheet">
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/DataTables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../library/lz-string/lz-string.min.js"></script>
<!-- DataTables -->
<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>

<script type="text/javascript" src="../library/config.js"></script>



<style>
#pvjs-widget {
	top:0;
	left:0;
	font-size:12px;
	width:100%;
	height:inherit;
	border:1px solid #CCC;
	border-radius:10px;
}
</style>

</head>
<body>

<?php
$file_dir = $BXAF_CONFIG['USER_FILES_PVJS'] . '/' . $BXAF_CONFIG['BXAF_USER_CONTACT_ID'];

$myfile = fopen($file_dir . '/svg_code.txt', "r") or die("Unable to open file!");

$content = fgets($myfile);



echo $content;



fclose($myfile);
?>






<script>
</script>
</body>
</html>
