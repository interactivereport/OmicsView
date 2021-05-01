$('#geneListModal').on('show.bs.modal', function(){
    $.ajax({
        type: 'GET',
        url: 'app_list_ajax_selection.php?category=Gene&input_name=geneList&input_class=geneList&pre_selected_list_id=<?php echo $geneListID; ?>',
        success: function(responseText){
            $('#geneListModal').find('.modal-body').html(responseText);
        }
    });
});


$('#geneListModal').on('change', '.geneList', function(){
    var currentListID = $(this).val();
    
    var content = $('#gene_list_content_' + currentListID).val();
    
    $('#GeneNames').val(content);
});


$('#geneListModal').on('click', '.geneList_Name', function(){
    var currentListID 	= $(this).attr('listid');
    
    var radioID			= 'geneList_' + currentListID;
    
    $('#' + radioID).prop('checked', true);
    
    var content = $('#gene_list_content_' + currentListID).val();
    
    $('#GeneNames').val(content);
});