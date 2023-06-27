$(document).ready(function() {

    $("#loginForm").validate( {
        ignore: ".ignore",
        rules: {
            email: {
                required: true,
                email: true,
            }, 
            password: {
                required: true,
                minlength : 6
            },
            hiddenRecaptcha: {
                required: function () {
                    if (grecaptcha.getResponse() == '') {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        },
        messages: {
            email:{
                required: 'Please enter email address',
                email: 'Please enter a valid email address',
            },
            password:{
                required: "Please enter password",
                minlength: "Password must be at least 6 digit"
            },   
            hiddenRecaptcha:{
                required:"Please complete the captcha"
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

function recaptchaCallback() {
    $('#hiddenRecaptcha').valid();
  };