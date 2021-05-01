<?php

$BXAF_CONFIG_CUSTOM['PAGE_LOGIN_REQUIRED']	= false;

include_once("config.php");

if(! isset($_GET['project']) || $_GET['project'] == '' || ! file_exists($project_dir . $_GET['project'])){
	header("Location: list.php");
}


$current_project_url = $tensor_URL . $project_url . $_GET['project'] . '/config.json';

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
            		SC RNA-Seq Visualization: <?php echo urldecode($_GET['project']); ?>
					<a class="ml-5" style="font-size: 1rem;" href="index.php"> <i class="fas fa-angle-double-right"></i> Start New Visualization</a>
					<a class="mx-2" style="font-size: 1rem;" href="list.php"> <i class="fas fa-angle-double-right"></i> All Visualizations</a>
            	</h3>
            	<hr />
				<div class="my-3">
					<a class="mx-2 btn btn-sm btn-success" href="summary.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> Summary </a>
					<a class="mx-2 btn btn-sm btn-success" href="view_iframe.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> 3D Visualization </a>
					<a class="mx-2 btn btn-sm btn-success" href="violin.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Violin Plot </a>
					<a class="mx-2 btn btn-sm btn-success" href="violin_genes.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Violin Plot with Genes</a>
					<a class="mx-2 btn btn-sm btn-success" href="multi_tsne.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-edit"></i> Multi-tSNE View</a>
					<a class="mx-2 btn btn-sm btn-success" href="index.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-sync"></i> Re-Analysis </a>
					<a class="mx-2 btn btn-sm btn-success" href="bookmarks.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-bookmark"></i> Bookmarks </a>
					<a class="mx-2 btn btn-sm btn-success" href="abundant.php?project=<?php echo urlencode($_GET['project']); ?>"> <i class="fas fa-chart-pie"></i> Top Abundant Genes </a>
            	</div>

				<iframe style="min-height: 900px; border: 0px;" class="w-100 h-100" src='<?php echo $current_project_url; ?>'></iframe>

            </div>

            <div class="my-3 p-3" id="div_results"></div>
    		<div class="my-3" id="div_debug"></div>

		</div>
		<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
	</div>
</div>


</body>
</html>