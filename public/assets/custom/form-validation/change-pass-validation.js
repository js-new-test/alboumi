$(document).ready(function() {

    $("#changePassword").validate( {
        rules: {                        
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
            password:{
                required: "Please enter password",
                minlength: "Password must be at least 6 digit"
            },
            confirm_password: {
                required: "Please enter confirm password",
                equalTo: "Confirm password is not same as password"
            }
        },
        errorPlacement: function ( error, element ) {
            // Add the `invalid-feedback` class to the error element
            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.next( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
    } );

});

