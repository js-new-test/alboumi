$("#resetPassForm").validate({
    ignore: [], // ignore NOTHING
    rules: {                   
        password : {
            required : true,
        },
        confirm_password : {
            required : true,
            equalTo : "#password"
        },        
    },
    messages: {         
        password : {
            required : passwordReq
        },
        confirm_password : {
            required : confirmPassReq,
            equalTo : confirmPassMissMatch
        },              
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});