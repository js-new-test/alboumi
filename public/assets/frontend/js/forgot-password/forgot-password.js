$("#frontForgotPassForm").validate({
    ignore: [], // ignore NOTHING
    rules: {        
        email: {
            required: true,
            email: true
        },        
    },
    messages: {
        email : {
            required: emailReq,
            email: emailInvalid
        },        
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});