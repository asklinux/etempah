$(document).ready(function(){    
    // Used in table list
    var deleteListBtnAction = $('#delete-list').attr('href');
    $('#delete-list').attr('href', deleteListBtnAction + 'Disabled');
        
    $('input[type=checkbox]').change(function(){
        var ids = '';
                
        if ($('input[type=checkbox ]:checked').size() > 0) {
            $('#delete-list').removeClass('disabled');
            $('#delete-list').attr('href', deleteListBtnAction);
                        
            $('input[type=checkbox ]:checked').each(function() {
                if (ids == '') {
                    ids = $(this).val();
                }
                else {
                    ids = ids + "," + $(this).val();
                }
            });
            $('input[name=ids]').val(ids);
        }
        else {
            $('#delete-list').addClass('disabled');
            $('#delete-list').attr('href', deleteListBtnAction + 'disabled');
        }
    });
        
    // Set default toggled button for group button
    $('.btn-not-approved').button('toggle');
 
});