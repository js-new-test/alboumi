$("#frontContactUsForm").validate({
    ignore: [], // ignore NOTHING
    rules: {        
        email: {
            required: true,
            email: true
        },   
        fullname : {
            required : true
        },        
        text_message : {
            required : true
        }     
    },
    messages: {
        email : {
            required: emailReq,
            email: emailInvalid
        }, 
        fullname : {
            required : fullNameReq
        },        
        text_message : {
            required : messageReq
        }      
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});