<?php
include_once(__DIR__ . "/config.php");


if (isset($_GET['action']) && $_GET['action'] == 'list'){

	$table = 'tbl_page_genesets';
	$filter = '1';
	if(isset($_GET['Name']) && $_GET['Name'] != '') $filter .= " AND `Name` LIKE '%" . addslashes($_GET['Name']). "%' ";
	if(isset($_GET['Gene_Counts']) && $_GET['Gene_Counts'] != '') $filter .= " AND `Gene_Counts` < " . intval($_GET['Gene_Counts']). " ";
	if(isset($_GET['Gene_Names']) && $_GET['Gene_Names'] != '') $filter .= " AND `Gene_Names` LIKE '%, " . addslashes($_GET['Gene_Names']). ",%' ";

    $sql2 = "SELECT * FROM `$table` WHERE $filter ";

    $sql = "";
    // Search Condition
    if(isset($_POST['search']['value']) && trim($_POST['search']['value']) != '') {
    	$search_array = array();
    	for ($i = 0; $i < count($_POST['columns']); $i++){
            if(! in_array($_POST['columns'][$i]['data'], array('Actions'))){
                $search_array[] = "`" . $_POST['columns'][$i]['data'] . "` LIKE '%" . $_POST['search']['value'] . "%'";
            }
    	}
    	$sql .= " AND (" . implode(" OR ", $search_array) . ")";
    }

    // Order Condition
    $sql .= " ORDER BY ";
    $condition_array = array();
    for ($i = 0; $i < count($_POST['order']); $i++) {
        $order = $_POST['columns'][$_POST['order'][$i]['column']]['data'];
        $asc = $_POST['order'][$i]['dir'];
        if(! in_array($order, array('Actions'))){
            $condition_array[] = "`$order` $asc";
        }
    }
    $sql .= implode(", ", $condition_array);



    $sql0 = "SELECT COUNT(*) FROM `$table` WHERE $filter ";

    $recordsTotal = $BXAF_MODULE_CONN->get_one($sql0);

    $recordsFiltered = $BXAF_MODULE_CONN->get_one($sql0 . $sql);

    $data = $BXAF_MODULE_CONN->get_all($sql2 . $sql . " LIMIT " . $_POST['start'] . "," . $_POST['length'] . "");

    $output_array = array(
		'sql' => $BXAF_MODULE_CONN->last_query(),
    	'draw' => intval($_POST['draw']),
    	'recordsTotal' => $recordsTotal,
    	'recordsFiltered' => $recordsFiltered,
    	'data' => array()
    );

    foreach($data as $value) {
        $row = array();
        foreach($value as $k=>$v){
			if($k == 'Name'){
				$row[$k] = "<a href='" . $value['URL'] . "' target='_blank'>" . $v . "</a>";
			}
			else if($k == 'Gene_Names'){
				$row[$k] = trim($v, ',');
			}
			else {
				$row[$k] = $v;
			}
        }
        $output_array['data'][] = $row;

    }
    echo json_encode($output_array);

    exit();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<link type="text/css" rel="stylesheet" href="css/style.css" />



    <style>
        .hidden{
            display: none;
        }
    </style>


</head>
<body>
	<?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

	<div id="bxaf_page_content" class="row no-gutters h-100">

        <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

		<div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

			<div id="bxaf_page_right_content" class="w-100 p-2">

				<h1>
					Search MSigDB

					<a href='Javascript: void(0);' onclick="if( $('#form_main').hasClass('hidden') ) $('#form_main').removeClass('hidden'); else $('#form_main').addClass('hidden');" style='font-size: 1rem;'><i class='fas fa-search'></i> Advanced Search</a>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="mx-1" style='font-size: 1rem;'><i class='fas fa-sync'></i> Reset Search Condition</a>
				</h1>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" class="" id="form_main" method="get">

				<div class="w-100 my-3 border table-info border-info rounded">

                    <div class="row m-3">

						<div class="col-md-12 col-lg-3 my-2">
							<div class="form-group my-0 font-weight-bold">
			                    	MSigDB Name:
							</div>
							<input type="text" class="form-control" id="Name" name="Name" value="<?php if($_GET['Name'] != '') echo $_GET['Name']; ?>" placeholder="enter a specific MSigDB name">
						</div>

						<div class="col-md-12 col-lg-3 my-2">
							<div class="form-group my-0 font-weight-bold">
			                    	Gene Name:
							</div>
							<input type="text" class="form-control" id="Gene_Names" name="Gene_Names" value="<?php if($_GET['Gene_Names'] != '') echo $_GET['Gene_Names']; ?>" placeholder="enter a specific gene name">
						</div>

						<div class="col-md-12 col-lg-2 my-2">
							<div class="form-group my-0 font-weight-bold">
			                    	Gene Counts Limit:
							</div>
							<input type="text" class="form-control" id="Gene_Counts" name="Gene_Counts" value="<?php if($_GET['Gene_Counts'] != '') echo $_GET['Gene_Counts']; ?>" placeholder="Gene counts limit">
						</div>

						<div class="col-md-12 col-lg-4 my-2">
							<div class="form-group my-0 font-weight-bold">
			                    	&nbsp;
							</div>
							<button class="btn btn-primary" type="submit"><i class='fas fa-search'></i> Search</button>
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default mx-1"><i class='fas fa-sync'></i> Reset</a>

							<input type="checkbox" class="form-control-checkbox" id="Show_Gene_Names" name="Show_Gene_Names" value="1"> Show Gene Names

						</div>

                    </div>
				</div>
                </form>


			    <div class="w-100 my-5">
			    	<table class="table" id="myTable">
			    		<thead>
			    			<tr class="table-success">
								<th>ID</th>
								<th>Name</th>
								<th>Genes</th>
<?php if($_GET['Show_Gene_Names'] == 1) echo "<th>Gene Names</th>"; ?>
			    			</tr>
			    		</thead>
			    	</table>
			    </div>


            </div>

		    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>

		</div>

	</div>



<link   href='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.css' rel='stylesheet' type='text/css'>
<script src='/<?php echo $BXAF_CONFIG['BXAF_SYSTEM_SUBDIR']; ?>library/datatables/datatables.min.js'></script>

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js "></script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/1.10.16/sorting/natural.js"></script>


<script type="text/javascript">

	$(document).ready(function(){

		$('#myTable').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": "<?php echo $_SERVER['PHP_SELF']; ?>?action=list<?php if($_SERVER['QUERY_STRING'] != '') echo '&' . $_SERVER['QUERY_STRING']; ?>",
	            "type": "POST"
	        },
	        dom: "Blfrtip",
	        buttons: [
	            'copy', 'csv', 'excel', 'pdf', 'print'
	        ],
			"paging": true,
			"pageLength": 25,
	        "lengthMenu": [[25, 100, 250, 1000, 5000], [25, 100, 250, 1000, 5000]],
	        // "order": [[0, 'desc']],
	        "columns": [
				{ "data": "ID" },
	            { "data": "Name" },
				{ "data": "Gene_Counts" }
<?php if($_GET['Show_Gene_Names'] == 1) echo ',{ "data": "Gene_Names" }'; ?>
	        ]
	    });

	});

</script>


</body>
</html>