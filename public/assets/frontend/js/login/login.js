$("#frontLoginForm").validate({
    ignore: [], // ignore NOTHING
    rules: {        
        email: {
            required: true,
            email: true
        },
        password : {
            required : true
        },        
    },
    messages: {
        email : {
            required: emailReq,
            email: emailInvalid
        },
        password : {
            required: passwordReq,
        }
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});