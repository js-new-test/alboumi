$("#frontChangePassForm").validate({
    ignore: [], // ignore NOTHING
    rules: {    
        currentpassword : {
            required : true
        },               
        password : {
            required : true,
        },
        confirm_password : {
            required : true,
            equalTo : "#password"
        },               
    },
    messages: { 
        currentpassword : {
            required : currentPassReq
        },       
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