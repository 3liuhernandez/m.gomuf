/*
 * Ajax action to api rest
*/
function action(form, btn, url, complete){
    var $ocrendForm = $('#'+form), __data = new FormData(document.getElementById(form));

    if(undefined == $ocrendForm.data('locked') || false == $ocrendForm.data('locked')) {
        
        var l = Ladda.create(document.querySelector(btn));
        l.start();

        $.ajax({
            type : 'POST',
            url : url,
            dataType: 'json',
            contentType:false,
            processData:false,
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data('locked', true) 
            },
            success : complete,
            error : function(xhr, status) {
                error_toastr('Ups!', 'Ha ocurrido un problema interno');
            },
            complete: function(){ 
                $ocrendForm.data('locked', false);
                l.stop();
            } 
        });
    }
}

/**
 * Formulario de contacto
 */

$('#contact_form').submit(e => {
    e.defaultPrevented;
    action('contact_form', '#contact_btn', 'api/contact', (json) => {
        if(json.success == 1) {
            success_toastr('Éxito', json.message);
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            error_toastr('Ups!', json.message);
        }
    });
    return false;
});

/**
 * Formulario de descarga
 */

$('#download_form').submit(e => {
    e.defaultPrevented;
    action('download_form', '#download_btn', 'api/download/book', (json) => {
        if(json.success == 1) {
            success_toastr('Éxito', json.message);
            document.getElementById('download_form').reset();
            document.getElementById('download_ebook').style.display='none';

            var anchor=document.createElement('a');
            anchor.setAttribute('href',json.file);
            anchor.setAttribute('download','');
            document.body.appendChild(anchor);
            anchor.click();
            anchor.parentNode.removeChild(anchor);
        } else {
            error_toastr('Ups!', json.message);
        }
    });
    return false;
});