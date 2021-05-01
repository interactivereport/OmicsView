$('#sampleListModal').on('show.bs.modal', function(){
    $.ajax({
        type: 'GET',
        url: 'app_list_ajax_selection.php?category=Sample&input_name=sampleList&input_class=sampleList&pre_selected_list_id=<?php echo $sampleListID; ?>',
        success: function(responseText){
            $('#sampleListModal').find('.modal-body').html(responseText);
        }
    });
});


$('#sampleListModal').on('change', '.sampleList', function(){
    var currentListID = $(this).val();
    
    var content = $('#sample_list_content_' + currentListID).val();
    
    $('#SampleIDs').val(content);
});


$('#sampleListModal').on('click', '.sampleList_Name', function(){
    var currentListID 	= $(this).attr('listid');
    
    var radioID			= 'sampleList_' + currentListID;
    
    $('#' + radioID).prop('checked', true);
    
    var content = $('#sample_list_content_' + currentListID).val();
    
    $('#SampleIDs').val(content);
});
