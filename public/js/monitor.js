jQuery(document).ready(function($) {
    $.ajax({
        url: 'http://10.1.10.149/slave_sync',
        type: 'GET',
        dataType: 'json',
        
    })
    .done(function(data) {
        console.log("success");
        console.log(data);
    })
    .fail(function() {

        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
});