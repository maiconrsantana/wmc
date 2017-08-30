jQuery(function($){
    var flag = false;
    var body = $('body');
    body.on('click', '#save_ical', function(event){
        event.preventDefault();
        var parent = $(this).parent(),
            t = $(this),
            spinner = $('.spinner', parent),
            message = $('.form-message', parent);
        if(flag){
            return false;
        }
        flag = true;
        spinner.show();
        var data = {
            'action' : 'st_import_ical',
            'url' : $('input.ical_input', parent).val(),
            'post_id' : $('input[name="post_id"]', parent).val()
        };

        $.post(ajaxurl, data, function(respon){
            if(typeof respon === 'object'){
                message.html(respon.message);
            }
            flag = false;
            spinner.hide();
        },'json');
    });
});     

