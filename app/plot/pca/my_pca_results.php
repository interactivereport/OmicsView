<?php
include_once('config.php');

$sql = "SELECT * FROM `{$BXAF_CONFIG['TBL_PCA_RESULT']}`
        WHERE `Owner_ID`={$BXAF_CONFIG['BXAF_USER_CONTACT_ID']}
        AND `Type`!='Meta'
        AND `bxafStatus`<5
        ORDER BY `ID` DESC";
$my_results = $DB -> get_all($sql);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>
<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../library/wenk.min.css" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">
<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/rangetouch.min.js"></script>
<script type="text/javascript" src="../library/config.js"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>


</head>
<body>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>
  <div id="bxaf_page_wrapper" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_WRAPPER']; ?>">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <!-- <div id="bxaf_page_right_container"> -->
      <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

        <div id="bxaf_page_right_content" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT_CONTENT']; ?>">

          <div class="container-fluid px-0 pt-3 w-100">

      			<h1 class="">
      				My PCA Saved Results
      			</h1>
            <hr />

            <?php include_once('component_header.php'); ?>

            <div class="w-100">
              <table class="table table-bordered datatable">
                <thead>
                  <tr class="table-info">
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach ($my_results as $result) {

                      if ($result['Type'] == 'R') {
                        $link = 'index_r_individuals_plot.php?id=';
                      } else {
                        $link = 'view_chart.php?id=';
                      }

                      echo '
                      <tr>
                        <td>
                          <a href="' . $link . bxaf_encrypt($result['ID'], $BXAF_CONFIG['BXAF_KEY']) . '">
                            ' . $result['Title'] . '
                          </a>
                        </td>
                        <td>' . $result['Description'] . '</td>
                        <td>
                          <button class="btn btn-sm btn-danger btn_remove_result"
                            rowid="' . bxaf_encrypt($result['ID'], $BXAF_CONFIG['BXAF_KEY']) . '">
                            <i class="fa fa-trash"></i> Delete
                          </button>
                        </td>
                      </tr>';
                    }
                  ?>
                </tbody>
              </table>
            </div>

            <div id="debug"></div>

          </div>
        </div>
  		  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
  		</div>
    <!-- </div> -->

	</div>





<script>


$(document).ready(function() {

  $('.datatable').DataTable();

  $(document).on('click', '.btn_remove_result', function() {
    var vm = $(this);
    var rowid = vm.attr('rowid');
    bootbox.confirm({
      message: "Are you sure to remove the PCA result?",
      buttons: {
        confirm: {
          label: 'Remove',
          className: 'btn-danger'
        },
        cancel: {
          label: 'Cancel',
          className: 'btn-primary'
        }
      },
      callback: function (result) {
        if (result) {
          $.ajax({
            type: 'POST',
            url: 'exe.php?action=delete_result',
            data: { rowid: rowid },
            success: function(response) {
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
