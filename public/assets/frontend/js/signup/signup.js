$("#customerSignup").validate({
    ignore: [], // ignore NOTHING
    rules: {        
        email: {
            required: true,
            email: true
        },   
        password : {
            required : true,
        },
        confirm_password : {
            required : true,
            equalTo : "#password"
        },
        firstName : {
            required : true
        },     
        mobile : {
            required : true,
            minlength: 8,
            number: true
        }
    },
    messages: {
        email : {
            required: emailReq,
            email: emailInvalid
        }, 
        password : {
            required : passwordReq
        },
        confirm_password : {
            required : confirmPassReq,
            equalTo : confirmPassMissMatch
        },
        firstName : {
            required : firstNameReq
        },
        mobile : {
            required : mobileReq,
            minlength: mobileMustBe,
            number: mobileNum
        }      
    },
    errorPlacement: function (error, element)
    {
        error.insertAfter( element );
    }    
});

$('#loyalty_number').on('keyup', function(){
    if($(this).val() != '')
    {
        $('#loyalty_flag').prop('checked', false);
        $('#loyalty_flag').attr('disabled', 'disabled');        
    }
    else if($(this).val() == '')
    {
        $('#loyalty_flag').removeAttr('disabled');
    }    
})