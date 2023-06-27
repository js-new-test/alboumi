$(document).ready(function() {

    $("#customerSignup").validate( {
        rules: {
            firstname: "required",
            lastname: "required",            
            mobile: {
                required: true,
                number: true
            },            
            email: {
                required: true,
                email: true,
            },                                    
            password: {
                required: true,
                minlength : 6
            },
            confirm_password : {   
                required: true,             
                equalTo : "#password"
            }                        
        },
        messages: {
            firstname: "Please enter first name",
            lastname: "Please enter last name",            
            mobile: {
                required: "Please enter phone number",
                number: "Phone number must be in digit"
            }, 
            email:{
                required: 'Please enter email address',
                email: 'Please enter a valid email address',
            },                                   
            password:{
                required: "Please enter password",
                minlength: "Password must be at least 6 digit"
            },
            confirm_password: {
                required: "Please enter confirm password",
                equalTo: "Confirm password is not same as password"
            }
        },
        errorElement: "em",
        errorPlacement: function ( error, element ) {
            // Add the `invalid-feedback` class to the error element
            error.addClass( "invalid-feedback" );
            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.next( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
        highlight: function ( element, errorClass, validClass ) {
            $(element).addClass( "is-invalid" ).removeClass( "is-valid" );
        },
        unhighlight: function (element, errorClass, validClass) {
            $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
        }
    });

});

