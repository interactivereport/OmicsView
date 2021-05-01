<?php
include_once('../assets/config.php');

$TYPE = 'Comparison';
if (isset($_GET['type']) && $_GET['type'] == 'gene') {
  $TYPE = 'Gene';
} else if ($_GET['type'] == 'sample') {
  $TYPE = 'Sample';
}
$COL_SETTING = unserialize($BXAF_CONFIG['PREFERENCE_DETAIL'][strtolower($TYPE).'_search_page_table_column']);

// echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once($BXAF_CONFIG['BXAF_PAGE_HEADER']); ?>

<link href="../library/css-loader.min.css" rel="stylesheet">
<link href="../library/dataTable/dataTable.min.css" rel="stylesheet">
<link href="../css/main.css" rel="stylesheet">


<script type="text/javascript" src="../library/jquery.form.min.js.php"></script>
<script type="text/javascript" src="../library/dataTable/dataTable.min.js.php"></script>
<script type="text/javascript" src="../library/config.js"></script>

<!-- DataTables -->
<link href="../library/dataTableExtensions/buttons.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="../library/dataTableExtensions/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.flash.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/jszip.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/pdfmake.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/vfs_fonts.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.html5.min.js"></script>
<script type="text/javascript" src="../library/dataTableExtensions/buttons.print.min.js"></script>



</head>
<body>

  <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_MENU'])) include_once($BXAF_CONFIG['BXAF_PAGE_MENU']); ?>

  <div id="bxaf_page_content" class="row no-gutters h-100">

    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_LEFT'])) include_once($BXAF_CONFIG['BXAF_PAGE_LEFT']); ?>

    <div id="bxaf_page_right" class="<?php echo $BXAF_CONFIG['BXAF_PAGE_CSS_RIGHT']; ?>">

      <div id="bxaf_page_right_content" class="w-100 p-2">



      		<h1 class="pt-3">
      			Saved <?php echo $TYPE . 's'; ?>


            <?php
              if ($TYPE == 'Gene') {
                $other_types = array('comparison', 'sample');
              } else if ($TYPE == 'Comparison') {
                $other_types = array('gene', 'sample');
              } else if ($TYPE == 'Sample') {
                $other_types = array('comparison', 'gene');
              }
              foreach ($other_types as $type) {
                echo '&nbsp;
                  <a href="saved_info.php?type=' . $type . '"
                     id="volcano_btn_load_demo" class="font-sanspro-300 font_normal">
                    <i class="fa fa-angle-double-right"></i> Saved ' . strtoupper(substr($type, 0, 1)) . substr($type, 1) . 's
                  </a>';
              }
            ?>

            &nbsp;
            <a href="javascript:void(0);" class="font-sanspro-300 font_normal" id="btn_clear_session">
              <i class="fa fa-angle-double-right"></i> Clear All
            </a>
      		</h1>

          <hr />



          <div class="row mx-0 mb-3">

            <a href="../user_preference/index.php?type=search_table_<?php echo $TYPE; ?>"
               class="btn btn-success  mb-3"
               target="_blank"><i class="fa fa-cog"></i> Table Settings</a>
        		&nbsp;&nbsp;


            <button href="javascript:void(0);" class="btn btn-danger mb-3 btn_clear_session_single" type="<?php echo $TYPE; ?>">
               <i class="fa fa-trash"></i> Clear Saved <?php echo $TYPE . 's'; ?>
            </button>
            &nbsp;&nbsp;

          </div>
          <div class="row mx-0">

            <?php
              if (isset($_SESSION['SAVED_' . strtoupper($TYPE)])
                  && is_array($_SESSION['SAVED_' . strtoupper($TYPE)])
                  && count($_SESSION['SAVED_' . strtoupper($TYPE)]) > 0) {

                $ALL_INFO = $_SESSION['SAVED_' . strtoupper($TYPE)];



                //---------------------------------------------------------------------------------
                // Comparisons
                //---------------------------------------------------------------------------------

                if ($TYPE == 'Comparison') {

                  echo '<button id="btn_significant_genes" class="btn btn-primary mb-3">
                          <i class="fa fa-database"></i> Significantly Changed Genes
                        </button>
                        <input class="hidden" id="input_index" value="' . implode(',', $_SESSION['SAVED_COMPARISON']) . '">';
                  echo '<table class="table table-bordered table-striped datatable">
                    <thead>
                      <tr>
                        <th>ComparisonID</th>';

                      foreach ($COL_SETTING as $col) {
                        echo '<th>' . $col . '</th>';
                      }

                  echo '
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>';

                    foreach($ALL_INFO as $comparisonIndex) {
                      $sql = "SELECT *
                              FROM `{$BXAF_CONFIG['TBL_COMPARISONS']}`
                              WHERE `ComparisonIndex`={$comparisonIndex}";
                      $comparison = $DB -> get_row($sql);
                      echo '<tr>
                      <td>
                        <a href="../search_comparison/single_comparison.php?type=comparison&id=' . $comparisonIndex . '"
                           target="_blank">
                          ' . $comparison['ComparisonID'] . '
                        </a>
                      </td>';

                      foreach ($COL_SETTING as $col) {
                        echo '<td>' . $comparison[$col] . '</td>';
                      }

                      echo '
                      <td>
                        <button class="btn btn-sm btn-danger btn_delete" type="comparison" index="' . $comparisonIndex . '">
                          <i class="fa fa-trash"></i> Delete
                        </button>
                      </td>';
                    }
                  echo '</tbody></table>';
                }

                //---------------------------------------------------------------------------------
                // Genes
                //---------------------------------------------------------------------------------
                else if ($TYPE == 'Gene') {
                  echo '<table class="table table-bordered table-striped datatable">
                    <thead>
                      <tr>
                        <th>GeneID</th>';

                      foreach ($COL_SETTING as $col) {
                        echo '<th>' . $col . '</th>';
                      }

                  echo '
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>';

                    foreach($ALL_INFO as $geneIndex) {
                      $sql = "SELECT *
                              FROM `{$BXAF_CONFIG['TBL_GENECOMBINED']}`
                              WHERE `GeneIndex`={$geneIndex}";
                      $gene = $DB -> get_row($sql);
                      echo '<tr>
                      <td>
                        <a href="../search_comparison/single_comparison.php?type=gene&id=' . $geneIndex . '"
                           target="_blank">
                          ' . $gene['GeneName'] . '
                        </a>
                      </td>';

                      foreach ($COL_SETTING as $col) {
                        echo '<td>' . $gene[$col] . '</td>';
                      }

                      echo '
                      <td>
                        <button class="btn btn-sm btn-danger btn_delete" type="gene" index="' . $geneIndex . '">
                          <i class="fa fa-trash"></i> Delete
                        </button>
                      </td>';
                    }
                  echo '</tbody></table>';
                }




                //---------------------------------------------------------------------------------
                // Samples
                //---------------------------------------------------------------------------------
                else if ($TYPE == 'Sample') {
                  echo '<table class="table table-bordered table-striped datatable">
                    <thead>
                      <tr>
                        <th>SampleID</th>';

                      foreach ($COL_SETTING as $col) {
                        echo '<th>' . $col . '</th>';
                      }

                  echo '
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>';

                    foreach($ALL_INFO as $sampleIndex) {
                      $sql = "SELECT *
                              FROM `{$BXAF_CONFIG['TBL_SAMPLES']}`
                              WHERE `SampleIndex`={$sampleIndex}";
                      $sample = $DB -> get_row($sql);
                      echo '<tr>
                      <td>
                        <a href="../search_comparison/single_comparison.php?type=sample&id=' . $sampleIndex . '"
                           target="_blank">
                          ' . $sample['SampleID'] . '
                        </a>
                      </td>';

                      foreach ($COL_SETTING as $col) {
                        echo '<td>' . $sample[$col] . '</td>';
                      }

                      echo '
                      <td>
                        <button class="btn btn-sm btn-danger btn_delete" type="sample" index="' . $sampleIndex . '">
                          <i class="fa fa-trash"></i> Delete
                        </button>
                      </td>';
                    }
                  echo '</tbody></table>';
                }



              } else {
                echo '
                <div class="mt-3 alert alert-warning w-100">
                  You don\'t have any saved ' . strtolower($TYPE) . 's.
                </div>';
              }
            ?>
          </div>


      </div>
	    <?php if(file_exists($BXAF_CONFIG['BXAF_PAGE_FOOTER'])) include_once($BXAF_CONFIG['BXAF_PAGE_FOOTER']); ?>
		</div>
	</div>






<script>

$(document).ready(function() {

  $('.datatable').DataTable({
    "dom": 'Bfrtip',
    "buttons": [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ],
  });



  /* View Significantly Changed Genes */
  $(document).on('click', '#btn_significant_genes', function() {
    var data_list = $('#input_index').val().split(',');
    $.ajax({
      type: 'POST',
      url: 'exe.php?action=get_significantly_changed_genes',
      data: {comparisons: data_list},
      success: function(response) {
        window.location = 'changed_genes.php';
      }
    });
	});




  /* Clear Session */
  $(document).on('click', '#btn_clear_session', function() {
    bootbox.confirm({
      message: "Are you sure to remove all saved genes, comparisons and samples?",
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
            url: 'exe_saved_info.php?action=clear_session',
            success: function(response) {
              $('table').parent().remove();
              bootbox.alert('All saved genes and comparisons have been deleted.', function() {
                location.reload(true);
              });
            }
          });
        }
      }
    });
  });


  $(document).on('click', '.btn_clear_session_single', function() {
    var current = $(this);
    var type = current.attr('type');
    $.ajax({
      type: 'POST',
      url: 'exe_saved_info.php?action=clear_session_single',
      data: {type: type},
      success: function(response) {
        current.parent().remove();
        bootbox.alert('The ' + type + 's has been deleted.', function() {
          location.reload(true);
        });
      }
    });
  });



  /* Delete One Row */
  $(document).on('click', '.btn_delete', function() {
    var current = $(this);
    var type = current.attr('type');
    var index = current.attr('index');
    $.ajax({
      type: 'POST',
      url: 'exe_saved_info.php?action=delete_session',
      data: {type: type, index: index},
      success: function(response) {
        // alert(response);
        current.parent().parent().remove();
        bootbox.alert('The ' + type + ' has been deleted.', function() {
          location.reload(true);
        });
      }
    });
  });
});

</script>




</body>
</html>
