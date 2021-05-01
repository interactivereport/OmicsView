<?php

include_once("config.php");

if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}



if (isset($_GET['action']) && $_GET['action'] == 'upload_csv') {

	$target_tsv = $project_dir . $_GET['project'] . '/meta_data.tsv';
    $target_csv = $project_dir . $_GET['project'] . '/meta_data_uploaded.csv';

	if(file_exists($target_csv)) unlink($target_csv);

    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {

        if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_csv)){

			if(file_exists($target_tsv)) unlink($target_tsv);

			$handle_tsv = fopen($target_tsv, "w");
			if (($handle = fopen($target_csv, "r") ) !== FALSE) {
			    while (($data = fgetcsv($handle) ) !== FALSE) {
					if(count($data) > 1){
						fputcsv($handle_tsv, $data, "\t");
					}
			    }
			    fclose($handle);
			}

		}
    }

	exit();
}



$meta_data_file = $project_dir . $_GET['project'] . '/meta_data.tsv';
$meta_data_csv = $project_dir . $_GET['project'] . '/meta_data.csv';
$meta_data_csv_url = $project_url . $_GET['project'] . '/meta_data.csv';

if(! file_exists($meta_data_file)){
	header("Location: list.php");
}


$handle_csv = fopen($meta_data_csv, "w");
$meta_data_info = array();
if (($handle = fopen($meta_data_file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
		if(count($data) > 1){
			$meta_data_info[] = $data;
			fputcsv($handle_csv, $data);
		}
    }
    fclose($handle);
}



$BXAF_CONFIG['BXAF_PAGE_LEFT']   = '';
$BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']			= '';
$BXAF_CONFIG['BXAF_PAGE_CSS_LEFT']				= '';
$BXAF_CONFIG['BXAF_PAGE_CSS_LEFT_FIXED_WIDTH']	= '';
$BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']				= 'w-100';
$BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']		= 'w-100 p-3';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/jquery/jquery.form.min.js"></script>

	<link  href="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css" rel="stylesheet">
	<script src="/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js"></script>

</head>
<body>

<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
<div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>
	<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">
		<div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">



            <div class="container-fluid">

            	<h3 class="page-header">
            		Edit Meta Data of SC RNA-Seq Visualizations
					<a class="ml-5" style="font-size: 1rem;" href="index.php"> <i class="fas fa-angle-double-right"></i> Start New Visualization</a>
					<a class="ml-5" style="font-size: 1rem;" href="list.php"> <i class="fas fa-angle-double-right"></i> All Visualizations</a>
            	</h3>
            	<hr />
				<div class="my-3">
					<a class="mx-2 btn btn-sm btn-success" href="summary.php?project=<?php echo urldecode($_GET['project']); ?>"> <i class="fas fa-list"></i> Analysis Summary</a>
					<a class="mx-2 btn btn-sm btn-success" href="index.php?project=<?php echo urldecode($_GET['project']); ?>"> <i class="fas fa-sync"></i> Re-Analysis</a>
					<a class="mx-2 btn btn-sm btn-success" href="bookmarks.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-bookmark"></i> Bookmarks </a>
					<a class="mx-2 btn btn-sm btn-success" href="view_iframe.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> 3D Visualization </a>
            	</div>

				<div class="my-3 p-3 table-warning border rounded">
	                <form id="form_upload" class="form-inline">
	                  <strong>Upload csv file:</strong>
	                  <input class="mx-2 form-control" type="file" name="file" required>
	                  <button class="btn btn-primary" id="btn_submit">Upload</button>
	                  <a class="mx-2" href="<?php echo $meta_data_csv_url; ?>"><i class="fa fa-download"></i> Download CSV Template</a>
	                </form>
				</div>


				<div class="my-3">
					<table class="table table-bordered table-striped table-hover w-100" id="table_main">
			    		<thead>
			    			<tr class="table-info">
								<?php foreach($meta_data_info[0] as $c) echo "<th>$c</th>"; ?>
			    			</tr>
			    		</thead>
						<tbody>

							<?php
								foreach($meta_data_info as $i=>$row){
									if($i == 0) continue;
									echo "<tr>";
									foreach($row as $c) echo "<td>$c</td>";
									echo "</tr>";
								}
							?>
			    		</tbody>
			    	</table>

				</div>

            </div>

            <div class="my-3 p-3" id="div_results"></div>
    		<div class="my-3" id="div_debug"></div>

		</div>
		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>



<script>

$(document).ready(function() {

	$('#table_main').DataTable();

    var options = {
        type: 'post',
        url: 'edit_meta.php?action=upload_csv&project=<?php echo urldecode($_GET['project']); ?>',

		beforeSubmit: function(formData, jqForm, options) {

			$('#btn_submit')
				.attr('disabled', '')
				.children(':first')
				.removeClass('fa-chart-pie')
				.addClass('fa-spin fa-spinner');

			return true;
		},
    	success: function(response){

			$('#btn_submit')
				.removeAttr('disabled')
				.children(':first')
				.addClass('fa-chart-pie')
				.removeClass('fa-spin fa-spinner');

			location.reload();

			return true;
		}

    };

    $('#form_upload').ajaxForm(options);

});

</script>

</body>
</html>