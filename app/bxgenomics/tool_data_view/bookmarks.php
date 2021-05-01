<?php

include_once("config.php");

if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . urlencode($_GET['project']) ) ){
	header("Location: list.php");
}


$bookmarks_file = $project_dir . urlencode($_GET['project']) . '/bookmarks.txt';

if (isset($_GET['action']) && $_GET['action'] == 'delete_bookmark') {

	$bookmark = $_GET['bookmark'];

	$contents = json_decode(file_get_contents($bookmarks_file), true);
	if(array_key_exists($bookmark, $contents)) unset($contents[$bookmark]);
	$contents = array_values($contents);

	file_put_contents($bookmarks_file, json_encode($contents) );

    exit();
}


if (isset($_GET['action']) && $_GET['action'] == 'upload_file') {

	$target_file = $target_file_dir . 'bookmark_' . time() . '.txt';
	if(file_exists($target_file)) unlink($target_file);

    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {

		$contents = json_decode(file_get_contents($_FILES["file"]["tmp_name"]), true);
		$content = array_pop($contents);
		$content['label'] = $_POST['Name'];

		$contents = json_decode(file_get_contents($bookmarks_file), true);
		$contents[] = $content;
		file_put_contents($bookmarks_file, json_encode($contents) );

    }

	exit();
}





$bookmarks_info = array();
if(file_exists($bookmarks_file)){
	$bookmarks_url  = $project_url . urlencode($_GET['project']) . '/bookmarks.txt';
	$bookmarks_date = date ("Y-m-d H:i:s", filemtime($bookmarks_file));

	$contents = json_decode(file_get_contents($bookmarks_file), true);
	foreach($contents as $b=>$content){
		$bookmarks_info[$b]['Name'] = $content['label'];
		$bookmarks_info[$b]['Selected Cells'] = $content['selectedPoints'];
	}
}

$sample_names = array();
if (($handle = fopen($project_dir . urlencode($_GET['project']) . '/sample_info.csv', "r")) !== FALSE) {

	fgetcsv($handle);
	while (($data = fgetcsv($handle) ) !== FALSE) {
		$sample_names[] = $data[1];
    }
    fclose($handle);
}

foreach($bookmarks_info as $b=>$row){
	$samples = array();
	foreach($row['Selected Cells'] as $i=>$c) $samples[$i] = $sample_names[$c];
	$bookmarks_info[$b]['Selected Cells'] = $samples;
	$bookmarks_info[$b]['key'] = bxaf_save_to_cache($samples);
}

$config_json = json_decode(file_get_contents($project_dir . urlencode($_GET['project']) . '/config.json'), true);
if(file_exists($bookmarks_file) && (! isset($config_json['embeddings'][0]['bookmarksPath']) || $config_json['embeddings'][0]['bookmarksPath'] != $bookmarks_url) ) {
	$config_json['embeddings'][0]['bookmarksPath'] = $bookmarks_url;
	file_put_contents($project_dir . urlencode($_GET['project']) . '/config.json',  json_encode($config_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
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

            	<h3 class="">
					Bookmarks of SC RNA-Seq Visualization: <?php echo urldecode($_GET['project']); ?>
					<a class="ml-5" style="font-size: 1rem;" href="index.php"> <i class="fas fa-angle-double-right"></i> Start New Visualization</a>
					<a class="ml-5" style="font-size: 1rem;" href="list.php"> <i class="fas fa-angle-double-right"></i> All Visualizations</a>
            	</h3>
            	<hr />
				<div class="my-3">
					<a class="mx-2 btn btn-sm btn-success" href="view_iframe.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> 3D Visualization </a>
					<a class="mx-2 btn btn-sm btn-success" href="edit_meta.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Update Meta Data </a>
					<a class="mx-2 btn btn-sm btn-success" href="index.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-sync"></i> Re-Analysis </a>
					<a class="mx-2 btn btn-sm btn-success" href="summary.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-list"></i> Summary </a>
            	</div>

				<div class="my-3 p-3 table-warning border rounded">
	                <form id="form_upload" enctype="multipart/form-data" class="form-inline">
						<strong>Bookmark Name:</strong>
						<input class="mx-2 form-control" type="text" name="Name" value="" />
						<strong>Upload bookmark file:</strong>
						<input class="mx-2 form-control" type="file" name="file" required>
						<button type="submit" class="btn btn-primary" id="btn_submit">Upload</button>
						<a class="mx-2" href="view_iframe.php?project=<?php echo urlencode($_GET['project']); ?>"><i class="fa fa-download"></i> Go to 3D Visualization and Download New Bookmarks</a>
	                </form>
				</div>

<?php if(count($bookmarks_info) > 0) { ?>

				<h3 class="mt-5 mb-3">
					Current Bookmarks
					<a title='Download This Bookmark' class='ml-5' style='font-size: 1rem;' href='<?php echo $bookmarks_url; ?>' target='_blank'><i class='fas fa-download'></i> Download All Bookmarks</a>
				</h3>

				<div class="my-3">
					<table class="table table-bordered table-striped table-hover w-100" id="table_main">
			    		<thead>
			    			<tr class="table-info">
								<th>Name</th>
								<th>Date Created</th>
								<th># of Samples</th>
								<th>Selected Samples</th>
								<th>Actions</th>
			    			</tr>
			    		</thead>
						<tbody>
							<?php
								foreach($bookmarks_info as $file=>$row){
									echo "<tr>";
									echo "<td>" . $row['Name'] . "</td>";
									echo "<td>" . $bookmarks_date . "</td>";
									echo "<td>" . count($row['Selected Cells']) . "</td>";
									echo "<td>" . implode(", ", $row['Selected Cells']) . "</td>";
									echo "<td class='text-nowrap'>" . "<a title='Save The Selected Samples' class='mx-1' href='annotated_heatmap_sc.php?project=" . $_GET['project'] . "&key=" . $row['key'] . "' target='_blank'><i class='fas fa-chart-pie'></i> View Heatmap</a><BR>" . "<a title='Save The Selected Samples' class='mx-1' href='../../gene_expressions/app_list_new.php?Category=Sample&key=" . $row['key'] . "' target='_blank'><i class='fas fa-save'></i> Save Samples</a><BR>" . "<a title='Delete This Bookmark' class='mx-1 btn_delete_bookmark text-danger' href='Javascript: void(0);' bookmark='" . $file . "'><i class='fas fa-trash'></i> Delete Bookmark</a>" . "</td>";
									echo "</tr>";
								}
							?>
			    		</tbody>
			    	</table>
				</div>
<?php } //if(count($bookmarks_info) > 0) { ?>

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
        url: 'bookmarks.php?action=upload_file&project=<?php echo urldecode($_GET['project']); ?>',

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

			window.location = 'bookmarks.php?project=<?php echo urldecode($_GET['project']); ?>';

			return true;
		}

    };

    $('#form_upload').ajaxForm(options);

	$(document).on('click', '.btn_delete_bookmark', function() {
		var bookmark = $(this).attr('bookmark');

		bootbox.confirm({
		    message: "Are you sure you want to delete this bookmark?",
		    buttons: {
		        confirm: {
		            label: 'Yes',
		            className: 'btn-success'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn-danger'
		        }
		    },
		    callback: function (result) {
				if(result){
					$.ajax({
						type: 'GET',
						url: 'bookmarks.php?action=delete_bookmark&project=<?php echo urldecode($_GET['project']); ?>&bookmark=' + bookmark,
						success: function(responseText){

							location.reload(true);
						}
					});
				}
		    }
		});

	});

});

</script>

</body>
</html>