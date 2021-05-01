<?php
  if ($TIME_STAMP == 0) {
    $link_suffix = '';
  } else {
    $link_suffix = "?tid={$TIME_STAMP}";
  }

$PAGE['Category'] = 'Comparison Plotting Tools';
$PAGE['Barcode']  = 'Pathway Plot';




?>

<div class="btn-group mb-3" role="group" aria-label="Basic example">
  <button type="button" class="btn btn-outline-primary"
    onclick="window.location='index_r_barchart.php<?php echo $link_suffix; ?>'"
    id="btn_index_r_barchart">
    Eigenvalues
  </button>
  <button type="button" class="btn btn-outline-primary"
    onclick="window.location='index_r_variables_plot.php<?php echo $link_suffix; ?>'"
    id="btn_index_r_variables_plot">
    Variables Plot
  </button>
  <button type="button" class="btn btn-outline-primary"
    onclick="window.location='index_r_variables_table.php<?php echo $link_suffix; ?>'"
    id="btn_index_r_variables_table">
    Variables Data
  </button>
  <button type="button" class="btn btn-outline-primary"
    onclick="window.location='index_r_individuals_plot.php<?php echo $link_suffix; ?>'"
    id="btn_index_r_individuals_plot">
    Individuals Plot
  </button>
  <button type="button" class="btn btn-outline-primary"
    onclick="window.location='index_r_individuals_table.php<?php echo $link_suffix; ?>'"
    id="btn_index_r_individuals_table">
    Individuals Data
  </button>
  
  
  <?php if ($hasResearchProject){ ?>
  <button type="button"
    class="btn btn-outline-success"
    id="ResearchModalTrigger"
    data-toggle="modal"
    data-target="#ResearchProjectModal">
    Save to Study
  </button>
  <?php } ?>
  
 
</div>





<!----------------------------------------------------------------------------------------------->
<!-- Modal to Confirm Save Result -->
<!----------------------------------------------------------------------------------------------->
<div class="modal fade" id="modal_confirm_save_result">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">SAVE PCA RESULT</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <strong class="gray">Title:</strong>
        <input class="form-control mb-2" id="save_result_title">
        <strong class="gray">Description:</strong>
        <textarea class="form-control" id="save_result_description"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="btn_save_result">
          <i class="fa fa-floppy-o"></i> Save Result
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {

  //---------------------------------------------------------------------------
  // Save Result
  $(document).on('click', '#btn_save_result', function() {
    var vm          = $(this);
    var title       = $('#save_result_title').val();
    var description = $('#save_result_description').val();

    if (title == '') {
      bootbox.alert('Error: Please enter title.');
    }

    else {
      vm.attr('disabled', '')
        .children(':first')
        .removeClass('fa-floppy-o')
        .addClass('fa-spin fa-spinner');
      $.ajax({
        type: 'POST',
        url: 'exe.php?action=save_result',
        data: {
          title: title,
          description: description,
          type: 'R',
          time_stamp: '<?php echo $TIME_STAMP; ?>'
        },
        success: function(response) {
          vm.removeAttr('disabled')
            .children(':first')
            .addClass('fa-floppy-o')
            .removeClass('fa-spin fa-spinner');
          $('#modal_confirm_save_result').modal('hide');
          bootbox.alert('The result is saved. You can view all your saved PCA results <a href="my_pca_results.php">here</a>.');
        }
      });
    }
  });
  
  
 
});
</script>
