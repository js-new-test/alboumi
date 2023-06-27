$("#frontMyAccountForm").validate({
    ignore: [], // ignore NOTHING
    rules: {        
        email: {
            required: true,
            email: true
        },           
        firstName : {
            required : true
        },
        lastName : {
            required : true
        },     
        mobile : {
            required : true,
            minlength: 8,
            number: true
        },
        // dateOfBirth: {
        //     required : true
        // }
    },
    messages: {
        email : {
            required: emailReq,
            email: emailInvalid
        },         
        firstName : {
            required : firstNameReq
        },
        lastName : {
            required : lastNameReq
        },
        mobile : {
            required : mobileReq,
            minlength: mobileMustBe,
            number: mobileNum
        },
        // dateOfBirth: {
        //     required : dateOfBirthReq
        // }     
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});