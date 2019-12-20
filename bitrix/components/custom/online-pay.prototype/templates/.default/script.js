$(document).ready(function() {
    var form = $('#online-pay-form form');

    $.validateExtend({
        sum : {
            required : true
        },
        client_phone : {
            required : true
        },
    });

    form.validate({
        onKeyup : true,
        eachValidField : function() {
            $(this).removeClass('error').addClass('success');
        },
        eachInvalidField : function() {
            $(this).removeClass('success').addClass('error');
        },
    })
    form.find('input[data-validate="sum"]').formatter({
        pattern: '{{9999999999999}}'
    });
    form.find('input[data-validate="client_phone"]').formatter({
        pattern: '+{{9999999999999}}'
    });
});